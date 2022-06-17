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
	protected static $_instance;

	/**
	 * Constructor
	 */
	protected function __construct() {
		// TODO: create fields for hosts.
		$hosts = array( '127.0.0.1' );

		$this->es_client = $this->get_es_client( $hosts );
	}

	/**
	 * Get Instance.
	 *
	 * @param mixed ...$args Args assigned to instance in constructor.
	 */
	public static function get_instance( ...$args ) {
		if ( is_null( self::$_instance ) ) {
			static::$_instance = new static( ...$args );
		}

		return static::$_instance;
	}

	/**
	 * Get ElasticSearch CLient
	 *
	 * @param array $hosts Elasticsearch instances.
	 * @return object
	 */
	protected function get_es_client( $hosts ) {
		print_r( 'hi' );
		
		return ( new \OpenSearch\ClientBuilder() )
			->setHosts( $hosts )
			->setBasicAuthentication( 'admin', 'admin' ) // TODO: add credentials to backend and define option.
			->setSSLVerification( false ) // TODO: add field for SSL verify.
			->build();
	}

	public function index_document( $document ) {
	}

}
