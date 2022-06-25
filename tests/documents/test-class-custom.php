<?php
/**
 * Test class Custom
 *
 * @package Opensearch_Connect
 */

use OSC\Document\Custom;

/**
 * Custom_Test class
 */
class Custom_Test extends WP_UnitTestCase {

	/**
	 * Set up
	 *
	 * @return void
	 */
	public function setUp() : void {
		parent::setUp();

		$this->custom = array(
			'document_location' => array(
				'id'     => 1,
				'object' => 'custom',
				'type'   => 'custom-type',
			),
			'menu_order'        => 0,
			'media_id'          => 0,
			'title'             => 'Custom Item',
			'slug'              => 'custom-item',
			'content'           => 'Lorem Ipsum',
			'excerpt'           => '',
			'keywords'          => 'custom, item',
			'url'               => 'https://www.example.com',
			'tax'               => array(),
			'meta'              => array(),
			'hide_from_search'  => false,
		);

		$this->document = new Custom( $this->custom );
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown() : void {
		parent::tearDown();

		unset( $this->document );
	}

	/**
	 * Test set field data from object.
	 *
	 * @return void
	 */
	public function test_set_field_data_from_object() {
		$document = $this->getMockBuilder( 'OSC\Document\Custom' )
			->disableOriginalConstructor()
			->getMock();

		$reflection          = new ReflectionClass( $document );
		$reflection_property = $reflection->getMethod( 'set_field_data_from_object' );
		$reflection_property->setAccessible( true );

		$output = $reflection_property->invokeArgs( $document, array( $this->custom ) );

		$this->assertTrue( $output );
	}
}
