<?php
/**
 * Class indexer
 *
 * @package Opensearch_Connect
 */

declare( strict_types = 1 );

use OSC\Client_Bridge;
use OSC\Document;

namespace OSC;

/**
 * Class Indexer
 *
 * Brige requests to the OpenSearch package.
 */
class Indexer {

	/**
	 * Instance of self
	 *
	 * @var self
	 */
	protected static $instance;

	/**
	 * Indexable post types
	 *
	 * TODO: field for indexables
	 *
	 * @var array
	 */
	protected $indexable_post_types = array( 'post', 'page' );

	/**
	 * Indexable terms
	 *
	 * @var array
	 */
	protected $indexable_terms = array( 'post_tag', 'category' );

	/**
	 * Constructor
	 */
	protected function __construct() {
		$this->client_bridge = Client_Bridge::get_instance();
	}

	/**
	 * Get Instance.
	 *
	 * @param mixed ...$args Args assigned to instance in constructor.
	 */
	public static function get_instance( ...$args ) {
		if ( is_null( self::$instance ) ) {
			static::$instance = new static( ...$args );
		}

		return static::$instance;
	}

	/**
	 * Is Post Indexable
	 *
	 * @param  \WP_Post $post  WP_Post object.
	 * @return boolean
	 */
	private function is_post_indexable( \WP_Post $post ) : bool {
		$is_indexable = true;

		// Check indexable post types.
		if ( ! in_array( $post->post_type, $this->indexable_post_types, true ) ) {
			$is_indexable = false;
		}

		// Check post status.
		if ( 'publish' !== $post->post_status ) {
			$is_indexable = false;
		}

		/**
		 * Filter for is post indexable
		 *
		 * @param  bool    $is_indexable  Whether or not a post can be indexed.
		 * @param  WP_Post $post          WP_Post object.
		 * @return bool
		 */
		return apply_filters( 'osc/is_post_indexable', $is_indexable, $post );
	}

	/**
	 * Index Post
	 *
	 * @param  integer $post_id  Post ID to index.
	 * @return boolean
	 */
	public function index_post( int $post_id ) : bool {
		$post = get_post( $post_id );

		// Check indexable post types.
		if ( ! $this->is_post_indexable( $post ) ) {
			return false;
		}

		$post_document = new Document\Post( $post );

		return $this->client_bridge->index_document(
			$post_document->get_document_id(),
			$post_document->get_field_data()
		);
	}

	/**
	 * Delete Post
	 *
	 * @param integer $post_id  Post ID to delete.
	 * @return boolean
	 */
	public function delete_post( int $post_id ) : bool {
		$post          = get_post( $post_id );
		$post_document = new Document\Post( $post );

		return $this->client_bridge->delete_document(
			$post_document->get_document_id()
		);
	}

}
