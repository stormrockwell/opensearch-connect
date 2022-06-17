<?php
/**
 * Test Client Bridge Client
 *
 * @package Opensearch_Connect
 */

use OSC\Client_Bridge;

/**
 * Client_Bridge_Test class
 */
class Client_Bridge_Test extends WP_UnitTestCase {

	/**
	 * Set up
	 *
	 * @return void
	 */
	public function setUp() : void {
		parent::setUp();

		$this->client_bridge = Client_Bridge::get_instance();
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown() : void {
		parent::tearDown();

		unset( $this->client_bridge );
	}

	/**
	 * Test Get ES Client
	 *
	 * @return void
	 */
	public function test_get_es_client() {
		$input = array( '127.0.0.1' );

		$client_bridge = $this->getMockBuilder( 'OSC\Client_Bridge' )
			->disableOriginalConstructor()
			->getMock();

		$reflection          = new ReflectionClass( $client_bridge );
		$reflection_property = $reflection->getMethod( 'get_es_client' );
		$reflection_property->setAccessible( true );

		$output = $reflection_property->invokeArgs( $client_bridge, array( $input ) );

		$this->assertIsObject( $output );
	}

	/**
	 * Test Index Document
	 *
	 * @dataProvider provide_document
	 *
	 * @param array $document OpenSearch document.
	 * @return void
	 */
	public function test_index_document( $document ) {
		$output = $this->client_bridge->index_document( $document );
		$this->assertTrue( $output );
	}

	/**
	 * Provide document dataProvider.
	 *
	 * @return array
	 */
	public function provide_document() {
		return array(
			array(
				'id'       => 1,
				'doc_type' => 'post',
				'title'    => 'Hello World',
			),
		);
	}
}
