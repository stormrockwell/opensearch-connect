<?php
/**
 * Test class Term
 *
 * @package Opensearch_Connect
 */

use OSC\Document\Term;

/**
 * Term_Test class
 */
class Term_Test extends WP_UnitTestCase {

	/**
	 * Set up
	 *
	 * @return void
	 */
	public function setUp() : void {
		parent::setUp();

		$this->term = $this->factory->term->create_and_get(
			array(
				'name'     => 'Category Name',
				'taxonomy' => 'category',
			)
		);

		$this->document = new Term( $this->term );
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown() : void {
		parent::tearDown();

		unset( $this->post );
		unset( $this->term );
		unset( $this->document );
	}

	/**
	 * Test set field data from object.
	 *
	 * @return void
	 */
	public function test_set_field_data_from_object() {
		$document = $this->getMockBuilder( 'OSC\Document\Term' )
			->disableOriginalConstructor()
			->getMock();

		$reflection          = new ReflectionClass( $document );
		$reflection_property = $reflection->getMethod( 'set_field_data_from_object' );
		$reflection_property->setAccessible( true );

		$output = $reflection_property->invokeArgs( $document, array( $this->term ) );

		$this->assertTrue( $output );
	}
}
