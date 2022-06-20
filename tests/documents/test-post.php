<?php
/**
 * Test Document class
 *
 * @package Opensearch_Connect
 */

use OSC\Document\Post;

/**
 * Document_Test class
 */
class Post_Test extends WP_UnitTestCase {

	/**
	 * Set up
	 *
	 * @return void
	 */
	public function setUp() : void {
		parent::setUp();

		$this->post = $this->factory->post->create_and_get();

		$this->document = new Post();
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown() : void {
		parent::tearDown();

		unset( $this->post );
	}

	/**
	 * Test get data
	 *
	 * @return void
	 */
	public function test_get_data() {
		$output = $this->document->get_data();

		$this->assertIsArray( $output );
	}
}
