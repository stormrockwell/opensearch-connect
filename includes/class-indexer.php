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
	 * Instance of Client_Bridge
	 *
	 * @var Client_Bridge
	 */
	public $client_bridge;

	/**
	 * Constructor
	 */
	protected function __construct() {
		$this->client_bridge = Client_Bridge::get_instance();

		$this->add_hooks();
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
	 * Add WP Hooks.
	 *
	 * @return void
	 */
	private function add_hooks() {
		if ( ! defined( 'OSC_IS_TESTING' ) ) {
			add_action( 'save_post', array( $this, 'index_post' ), PHP_INT_MAX );
			add_action( 'deleted_post', array( $this, 'delete_post' ), PHP_INT_MAX );
			add_action( 'transition_post_status', array( $this, 'handle_post_status_change' ), PHP_INT_MAX, 3 );
		}
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
		 * @hook osc/is_post_indexable
		 * @param  bool    $is_indexable  Whether or not a post can be indexed.
		 * @param  WP_Post $post          WP_Post object.
		 * @return bool
		 */
		return apply_filters( 'osc/is_post_indexable', $is_indexable, $post );
	}

	/**
	 * Is Term Indexable
	 *
	 * @param  \WP_Term $term  WP_Term object.
	 * @return boolean
	 */
	private function is_term_indexable( \WP_Term $term ) : bool {
		$is_indexable = true;

		// Check indexable term types.
		if ( ! in_array( $term->taxonomy, $this->indexable_terms, true ) ) {
			$is_indexable = false;
		}

		/**
		 * Filter for is term indexable
		 *
		 * @hook osc/is_term_indexable
		 * @param  bool    $is_indexable  Whether or not a term can be indexed.
		 * @param  WP_Term $term          WP_Term object.
		 * @return bool
		 */
		return apply_filters( 'osc/is_term_indexable', $is_indexable, $term );
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
	 * Index Term
	 *
	 * @param  \WP_Term $term WP_Term object to index.
	 * @return boolean
	 */
	public function index_term( \WP_Term $term ) : bool {
		if ( ! $this->is_term_indexable( $term ) ) {
			return false;
		}

		$term_document = new Document\Term( $term );

		return $this->client_bridge->index_document(
			$term_document->get_document_id(),
			$term_document->get_field_data()
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

	/**
	 * Delete Term
	 *
	 * @param  \WP_Term $term WP_Term to delete.
	 * @return boolean
	 */
	public function delete_term( \WP_Term $term ) : bool {
		$term_document = new Document\Term( $term );

		return $this->client_bridge->delete_document(
			$term_document->get_document_id()
		);
	}

	/**
	 * Handle post status change
	 *
	 * @param string   $new_status  New post status.
	 * @param string   $old_status  Old post status.
	 * @param \WP_Post $post        Post object that was changed.
	 * @return void
	 */
	public function handle_post_status_change( string $new_status, string $old_status, \WP_Post $post ) {
		if ( 'publish' === $new_status ) {
			$this->index_post( $post->ID );
		} else {
			$this->delete_post( $post->ID );
		}
	}

}
