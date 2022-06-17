<?php
/**
 * Class client bridge
 *
 * @package Elastic Connect
 */

declare( strict_types = 1 );

namespace Opensearch_Connect;

use Elastic\Elasticsearch\ClientBuilder;

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
	 * @param array $hosts
	 * @return object
	 */
	protected function get_es_client( $hosts ) {
		return ClientBuilder::create()
			->setHosts( $hosts )
			->build();
	}

	public function index_document( $document ) {
	}

}
