<?php
/**
 * Test Client Bridge Client
 *
 * @package Elastic Connect
 */

use Opensearch_Connect\Client_Bridge;

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

		$this->Client_Bridge = Client_Bridge::get_instance();
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown() : void {
		parent::tearDown();

		unset( $this->Client_Bridge );
	}

	public function test_get_es_client() {
		$input  = array( '127.0.0.1' );

		$Client = $this->getMockBuilder( 'Client_Bridge' )
			->disableOriginalConstructor()
			->getMock();

        $reflection = new ReflectionClass( $Client );
        $reflection_property = $reflection->getProperty( 'get_es_client' );
        $reflection_property = $reflection_property->setAccessible( true );

		$output = $this->Client_Bridge->get_es_client( $input );

		$this->assertIsObject( $output );
	}

	/**
	 * @dataProvider provide_document
	 */
	public function test_index_document( $document ) {
		$output = $this->Client->index_document( $document );
		$this->assertTrue( $output );
	}

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
