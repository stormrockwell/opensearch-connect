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

		$this->post = $this->get_post();
		$this->term = $this->get_term();
		$this->user = $this->get_user();

		$this->indexer = Indexer::get_instance();

		$this->client_bridge = Client_Bridge::get_instance();
		$this->client_bridge->create_index();
	}

	/**
	 * Get and create post if needed.
	 *
	 * @return \WP_Post
	 */
	public function get_post() {
		if ( isset( $this->post ) ) {
			return $this->post;
		}

		$this->post = $this->factory->post->create_and_get();

		return $this->post;
	}

	/**
	 * Get and create user if needed.
	 *
	 * @return \WP_User
	 */
	public function get_user() {
		if ( isset( $this->user ) ) {
			return $this->user;
		}

		$this->user = $this->factory->user->create_and_get();

		return $this->user;
	}

	/**
	 * Get and create term if needed.
	 *
	 * @return \WP_Term
	 */
	public function get_term() {
		if ( isset( $this->term ) ) {
			return $this->term;
		}

		$this->term = $this->factory->term->create_and_get();

		return $this->term;
	}

	/**
	 * Tear down
	 *
	 * @return void
	 */
	public function tearDown() : void {
		parent::tearDown();

		unset( $this->indexer );

		$this->client_bridge->delete_index();
		unset( $this->client_bridge );
	}

	/**
	 * Test index document
	 *
	 * @dataProvider provide_documents
	 *
	 * @param  object $input  Object to be indexed.
	 * @return void
	 */
	public function test_index_document( $input ) {
		$output = $this->indexer->index_document( $input );

		$this->assertTrue( $output );
	}

	/**
	 * Test update document
	 *
	 * @dataProvider provide_documents
	 *
	 * @param  object $input  Object to be updated.
	 * @return void
	 */
	public function test_update_document( $input ) {
		$this->indexer->index_document( $input );
		$output = $this->indexer->index_document( $input );

		$this->assertTrue( $output );
	}

	/**
	 * Test delete document
	 *
	 * @dataProvider provide_documents
	 *
	 * @param  object $input  Object to be updated.
	 * @return void
	 */
	public function test_delete_document( $input ) {
		$this->indexer->index_document( $input );
		$output = $this->indexer->index_document( $input );

		$this->assertTrue( $output );
	}

	/**
	 * Test delete nonexisting document
	 *
	 * @dataProvider provide_documents
	 *
	 * @param  object $input  Object to be updated.
	 * @return void
	 */
	public function test_delete_nonexisting_document( $input ) {
		$this->indexer->index_document( $input );
		$output = $this->indexer->index_document( $input );

		$this->assertTrue( $output );
	}

	// Index update delete.

	/**
	 * Provide Documents data provider
	 *
	 * @return array
	 */
	public function provide_documents() {
		return array(
			'Post Object' => array( 'input' => $this->get_post() ),
			'Term Object' => array( 'input' => $this->get_term() ),
			'User Object' => array( 'input' => $this->get_user() ),
		);
	}

}
