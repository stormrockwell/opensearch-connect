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
		$this->index_name = 'opensearch-connect';

		$hosts = array( 'https://opensearch-node1:9200', 'https://opensearch-node2:9200' );

		$this->os_client = $this->get_os_client( $hosts );

		/* Create index if doesn't exist */
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
	 * @param array $body        Body usually containing additional settings/mappings.
	 * @return array
	 */
	public function create_index( array $body = array() ) : array {

		/* Delete index if it exists */
		$this->delete_index( $this->index_name );

		return $this->os_client->indices()->create(
			array(
				'index' => $this->index_name,
				'body'  => $body,
			)
		);
	}

	/**
	 * Delete index if exists
	 *
	 * @return array
	 */
	public function delete_index() : array {
		return $this->os_client->indices()->delete(
			array(
				'index'              => $this->index_name,
				'ignore_unavailable' => true,
			)
		);
	}

	/**
	 * Index OpenSearch document
	 *
	 * @param array $document OpenSearch document.
	 * @return boolean
	 */
	public function index_document( $document ) {
		return true; // TODO: actually work. $document should also be a class of Document.
	}

}
