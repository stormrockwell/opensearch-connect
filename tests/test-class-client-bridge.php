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

		/* Create index if doesn't exist */
		if ( ! $this->client_bridge->index_exists() ) {
			$this->client_bridge->create_index();
		}
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
	 * Test Index Document
	 *
	 * @dataProvider provide_document
	 *
	 * @param string $id       OpenSearch document index.
	 * @param array  $document OpenSearch document.
	 * @return void
	 */
	public function test_index_document( $id, $document ) {
		$output = $this->client_bridge->index_document( $id, $document );
		$this->assertEquals( 'created', $output['result'], 'Expected a result of "created"' );
	}


	/**
	 * Test Delete Document
	 *
	 * @dataProvider provide_document
	 *
	 * @param string $id       OpenSearch document index.
	 * @param array  $document OpenSearch document.
	 * @return void
	 */
	public function test_delete_document( $id, $document ) {
		/* Create document so we can test deleting it. */
		$this->client_bridge->index_document( $id, $document );

		$output = $this->client_bridge->delete_document( $id );
		$terew  = $output;
		$this->assertEquals( 'deleted', $output['result'], 'Expected a result of "deleted"' );
	}

	/**
	 * Provide document dataProvider.
	 *
	 * @return array
	 */
	public function provide_document() {
		return array(
			array(
				'1',
				array(
					'id'       => 1,
					'site_id'  => 0,
					'doc_type' => 'post',
					'title'    => 'Hello World',
				),
			),
		);
	}
}
