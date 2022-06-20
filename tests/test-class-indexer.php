<?php
/**
 * Test Indexer Client
 *
 * @package Opensearch_Connect
 */

use OSC\Indexer;
use OSC\Client_Bridge;

/**
 * Indexer_Test class
 */
class Indexer_Test extends WP_UnitTestCase {

	/**
	 * Set up
	 *
	 * @return void
	 */
	public function setUp() : void {
		parent::setUp();

		$this->post_id       = $this->factory->post->create();
		$this->indexer       = Indexer::get_instance();
		$this->client_bridge = Client_Bridge::get_instance();
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown() : void {
		parent::tearDown();

		unset( $this->indexer );
	}

	/**
	 * Test index post
	 *
	 * @return void
	 */
	public function test_index_post() {
		$input = $this->post_id;

		$output = $this->indexer->index_post( $input );

		$this->assertTrue( $output );
	}

	/**
	 * Test delete post.
	 *
	 * @return void
	 */
	public function test_delete_post() {
		$input = $this->post_id;

		// Index post to delete.
		$this->indexer->index_post( $input );
		$this->client_bridge->refresh();

		// Delete post.
		$output = $this->indexer->delete_post( $input );

		$this->assertTrue( $output );
	}

}
