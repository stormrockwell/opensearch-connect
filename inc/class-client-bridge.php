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
 * Brige requests to the ElasticSearch package.
 */
class Client_Bridge {

	/**
	 * Instance of self
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Constructor
	 */
	protected function __construct() {
		// TODO: create fields for hosts.
		$hosts = array( 'https://opensearch-node1:9200', 'https://opensearch-node2:9200' );

		$this->os_client = $this->get_os_client( $hosts );
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
	 * Index OpenSearch document
	 *
	 * @param array $document OpenSearch document.
	 * @return boolean
	 */
	public function index_document( $document ) {
		return true; // TODO: actually work. $document should also be a class of Document.
	}

}
