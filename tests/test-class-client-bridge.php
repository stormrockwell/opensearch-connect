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

		$this->client_bridge->delete_index();
		unset( $this->client_bridge );
	}

	/**
	 * Test Get ES Client to ensure it connected.
	 *
	 * @return void
	 */
	public function test_get_os_client() {
		$input = array( 'https://opensearch-node1:9200', 'https://opensearch-node2:9200' );

		$client_bridge = $this->getMockBuilder( 'OSC\Client_Bridge' )
			->disableOriginalConstructor()
			->getMock();

		$reflection          = new ReflectionClass( $client_bridge );
		$reflection_property = $reflection->getMethod( 'get_os_client' );
		$reflection_property->setAccessible( true );

		$output = $reflection_property->invokeArgs( $client_bridge, array( $input ) );

		$this->assertTrue( $output->ping() );
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
	 * Test index exists.
	 *
	 * @return void
	 */
	public function test_index_exists() {
		$output = $this->client_bridge->index_exists();
		$this->assertIsBool( $output );
	}

	/**
	 * Test Create index
	 *
	 * @return void
	 */
	public function test_create_index() {
		$output = $this->client_bridge->create_index();
		$this->assertTrue( $output['acknowledged'] );
	}

	/**
	 * Test Create index
	 *
	 * @return void
	 */
	public function test_delete_index() {
		$output = $this->client_bridge->delete_index();
		$this->assertTrue( $output['acknowledged'] );
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
