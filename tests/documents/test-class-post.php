<?php
/**
 * Test class Post
 *
 * @package Opensearch_Connect
 */

use OSC\Document\Post;

/**
 * Post_Test class
 */
class Post_Test extends WP_UnitTestCase {

	/**
	 * Set up
	 *
	 * @return void
	 */
	public function setUp() : void {
		parent::setUp();

		// Setting up term to assign to post.
		$this->term = $this->factory->term->create_and_get(
			array(
				'name'     => 'Category Name',
				'taxonomy' => 'category',
			)
		);

		$this->post     = $this->factory->post->create_and_get( array( 'category' => 'category-name' ) );
		$this->document = new Post( $this->post );
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown() : void {
		parent::tearDown();

		unset( $this->post );
		unset( $this->document );
	}

	/**
	 * Test set field data from object.
	 *
	 * @return void
	 */
	public function test_set_field_data_from_object() {
		$document = $this->getMockBuilder( 'OSC\Document\Post' )
			->disableOriginalConstructor()
			->getMock();

		$reflection          = new ReflectionClass( $document );
		$reflection_property = $reflection->getMethod( 'set_field_data_from_object' );
		$reflection_property->setAccessible( true );

		$output = $reflection_property->invokeArgs( $document, array( $this->post ) );

		$this->assertTrue( $output );
	}
}
