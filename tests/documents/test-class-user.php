<?php
/**
 * Test class User
 *
 * @package Opensearch_Connect
 */

use OSC\Document\User;

/**
 * User_Test class
 */
class User_Test extends WP_UnitTestCase {

	/**
	 * Set up
	 *
	 * @return void
	 */
	public function setUp() : void {
		parent::setUp();

		$this->user     = $this->factory->user->create_and_get();
		$this->document = new User( $this->user );
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown() : void {
		parent::tearDown();

		unset( $this->user );
		unset( $this->document );
	}

	/**
	 * Test set field data from object.
	 *
	 * @return void
	 */
	public function test_set_field_data_from_object() {
		$document = $this->getMockBuilder( 'OSC\Document\User' )
			->disableOriginalConstructor()
			->getMock();

		$reflection          = new ReflectionClass( $document );
		$reflection_property = $reflection->getMethod( 'set_field_data_from_object' );
		$reflection_property->setAccessible( true );

		$output = $reflection_property->invokeArgs( $document, array( $this->user ) );

		$this->assertTrue( $output );
	}
}
