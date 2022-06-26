<?php
/**
 * Class client bridge
 *
 * @package Opensearch_Connect
 */

declare( strict_types = 1 );

namespace OSC;

/**
 * Class Client Bridge
 *
 * Brige requests to the OpenSearch package.
 */
class Client_Bridge {

	/**
	 * Instance of self
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Index Name
	 *
	 * @var string
	 */
	public $index_name;

	/**
	 * Constructor
	 */
	protected function __construct() {
		// TODO: create fields for hosts, index name, and credentials.
		// TODO: handle multisite.
		/**
		 * Hook for OpenSearch index name
		 *
		 * @hook osc/index_name
		 * @param $index_name OpenSearch index name.
		 * @return string
		 */
		$this->index_name = apply_filters( 'osc/index_name', 'osc' );

		$hosts = array( 'https://opensearch-node1:9200', 'https://opensearch-node2:9200' );

		$this->os_client = $this->get_os_client( $hosts );

		// Create index if doesn't exist.
		if ( ! $this->index_exists() ) {
			$this->create_index();
		}
	}

	/**
	 * Get Instance.
	 *
	 * @param mixed ...$args Args assigned to instance in constructor.
	 */
	public static function get_instance( ...$args ) {
		if ( is_null( self::$instance ) ) {
			static::$instance = new static( ...$args );
		}

		return static::$instance;
	}

	/**
	 * Get OpenSearch Client
	 *
	 * @param array $hosts OpenSearch instances.
	 * @return object
	 */
	protected function get_os_client( $hosts ) {
		return ( new \OpenSearch\ClientBuilder() )
			->setHosts( $hosts )
			->setBasicAuthentication( 'admin', 'admin' ) // TODO: add credentials to backend and define option.
			->setSSLVerification( false ) // TODO: add field for SSL verify.
			->build();
	}

	/**
	 * Refresh indices
	 *
	 * @return array
	 */
	public function refresh() : array {
		return $this->os_client->indices()->refresh();
	}

	/**
	 * Check if index exists
	 *
	 * @return boolean
	 */
	public function index_exists() : bool {
		return $this->os_client->indices()->exists(
			array(
				'index' => $this->index_name,
			)
		);
	}

	/**
	 * Create OpenSearch index.
	 *
	 * @param  string $index_name  Name of index.
	 * @return array
	 */
	public function create_index( string $index_name = '' ) : array {
		$index_name = empty( $index_name ) ? $this->index_name : $index_name;

		// Get settings and mappings.
		$body = require OSC_CONNECT_PLUGIN_PATH . 'includes' . DIRECTORY_SEPARATOR . 'settings-mappings.php';

		// Delete index if it exists.
		$this->delete_index( $index_name );

		return $this->os_client->indices()->create(
			array(
				'index' => $index_name,
				'body'  => $body,
			)
		);
	}

	/**
	 * Delete index if exists
	 *
	 * @param string $index_name  Name of index.
	 * @return array
	 */
	public function delete_index( $index_name = '' ) : array {
		$index_name = empty( $index_name ) ? $this->index_name : $index_name;

		return $this->os_client->indices()->delete(
			array(
				'index'              => $index_name,
				'ignore_unavailable' => true,
			)
		);
	}

	/**
	 * Clone Index
	 *
	 * @param string $index_name  Current index name.
	 * @param string $target      Target index name.
	 * @return array
	 */
	public function clone_index( string $index_name, string $target ) {
		return $this->os_client->indices()->clone(
			array(
				'index'  => $index_name,
				'target' => $target,
			)
		);
	}

	public function set_index_readonly( string $index_name ) {
		return $this->os_client->indices()->putSettings(
			array(
				'index' => $index_name,
				'body'  => array(
					'settings' => array(
						'index.blocks.write' => true,
					),
				),
			)
		);
	}

	/**
	 * Index OpenSearch document
	 *
	 * @param  string $id       Unique identifier for document.
	 * @param  array  $document OpenSearch document body.
	 * @return boolean
	 */
	public function index_document( string $id, array $document ) : bool {
		$document_exists = $this->document_exists( $id );

		if ( $document_exists ) {
			$response = $this->os_client->update(
				array(
					'index' => $this->index_name,
					'id'    => $id,
					'body'  => array(
						'doc' => $document,
					),
				)
			);

			return 'updated' === $response['result'] || 'noop' === $response['result'];
		} else {
			$response = $this->os_client->create(
				array(
					'index' => $this->index_name,
					'id'    => $id,
					'body'  => $document,
				)
			);

			return 'created' === $response['result'];
		}
	}

	/**
	 * Index OpenSearch documents
	 *
	 * Note: this does not handle updating documents that exist.
	 *
	 * @param array  $documents   OpenSearch document.
	 * @param string $index_name  Name of index.
	 * @return boolean
	 */
	public function bulk_index_documents( array $documents, string $index_name = '' ) : bool {
		$index_name = empty( $index_name ) ? $this->index_name : $index_name;

		// Create body for bulk indexing.
		$body = array();
		foreach ( $documents as $document ) {
			if ( is_subclass_of( $document, 'OSC\Document' ) ) {
				$body[] = array(
					'index' => array(
						'_index' => $index_name,
						'_id'    => $document->get_document_id(),
					),
				);

				$body[] = $document->get_field_data();
			}
		}

		$response = $this->os_client->bulk(
			array(
				'body' => $body,
			)
		);

		return false === $response['errors'];
	}

	/**
	 * Delete OpenSearch document
	 *
	 * @param  string $id  Unique identifier for document.
	 * @return boolean
	 */
	public function delete_document( string $id ) : bool {
		$document_exists = $this->document_exists( $id );

		if ( $document_exists ) {
			$response = $this->os_client->delete(
				array(
					'index' => $this->index_name,
					'id'    => $id,
				)
			);

			return 'deleted' === $response['result'];
		}

		return true;
	}

	/**
	 * Check if OpenSearch document exists.
	 *
	 * @param  string $id  Unique identifier for document.
	 * @return boolean
	 */
	public function document_exists( string $id ) : bool {
		$this->refresh();
		return $this->os_client->exists(
			array(
				'index' => $this->index_name,
				'id'    => $id,
			)
		);
	}

	/**
	 * Search
	 *
	 * @param array $args Search arguments.
	 * @return array
	 */
	public function search( $args ) : array {
		// TODO: Search Body class to handle args.
		$search_body = array(
			'size'  => 5,
			'query' => array(
				'match' => array(
					'title' => $args['s'],
				),
			),
		);

		return $this->os_client->search(
			array(
				'index' => $this->index_name,
				'body'  => $search_body,
			)
		);
	}

}
