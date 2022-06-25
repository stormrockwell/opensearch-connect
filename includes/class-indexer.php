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
	 * Users indexable.
	 *
	 * @var boolean
	 */
	protected $users_indexable = true;

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
			// Post hooks.
			add_action( 'save_post', array( $this, 'handle_save_post' ), PHP_INT_MAX );
			add_action( 'deleted_post', array( $this, 'handle_delete_post' ), PHP_INT_MAX );
			add_action( 'transition_post_status', array( $this, 'handle_post_status_change' ), PHP_INT_MAX, 3 );

			// Term hooks.
			add_action( 'created_term', array( $this, 'handle_save_term' ), PHP_INT_MAX, 3 );
			add_action( 'edited_term', array( $this, 'handle_save_term' ), PHP_INT_MAX, 3 );
			add_action( 'pre_delete_term', array( $this, 'handle_delete_term' ), PHP_INT_MAX, 2 );

			// User hooks.
			add_action( 'user_register', array( $this, 'handle_save_user' ), PHP_INT_MAX );
			add_action( 'profile_update', array( $this, 'handle_save_user' ), PHP_INT_MAX );
			add_action( 'delete_user', array( $this, 'handle_delete_user' ), PHP_INT_MAX, 3 );
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
	 * Are users Indexable
	 *
	 * @return boolean
	 */
	private function are_users_indexable() : bool {
		$is_indexable = $this->users_indexable;

		/**
		 * Filter for are users indexable
		 *
		 * @hook osc/are_users_indexable
		 * @param  bool    $is_indexable  Whether or not a user can be indexed.
		 * @return bool
		 */
		return apply_filters( 'osc/are_users_indexable', $is_indexable );
	}

	/**
	 * Get document by object
	 *
	 * @param  object $object  Object to be evaluated depending on class name.
	 * @return object|null
	 */
	public function get_document_by_object( object $object ) {
		$document = null;

		switch ( get_class( $object ) ) {
			case 'WP_Post':
				if ( $this->is_post_indexable( $object ) ) {
					$document = new Document\Post( $object );
				}
				break;
			case 'WP_Term':
				if ( $this->is_term_indexable( $object ) ) {
					$document = new Document\Term( $object );
				}
				break;
			case 'WP_User':
				if ( $this->are_users_indexable() ) {
					$document = new Document\User( $object );
				}
				break;
			default:
				// TODO: Add custom document by default.
		}

		/**
		 * Filter get document by object.
		 *
		 * @hook osc/indexer/get_document_by_object
		 * @param object $document Document class.
		 * @param object $object   Object passed to retrieve the document class.
		 */
		return apply_filters( 'osc/indexer/get_document_by_object', $document, $object );
	}

	/**
	 * Index Document
	 *
	 * @param  object $object  Mixed object types.
	 * @return boolean
	 */
	public function index_document( object $object ) : bool {
		$document = $this->get_document_by_object( $object );

		if ( null === $document ) {
			return true;
		}

		return $this->client_bridge->index_document(
			$document->get_document_id(),
			$document->get_field_data()
		);
	}

	/**
	 * Delete Document
	 *
	 * @param  object $object  Mixed object types.
	 * @return boolean
	 */
	public function delete_document( object $object ) : bool {
		$document = $this->get_document_by_object( $object );

		if ( null === $document ) {
			return true;
		}

		return $this->client_bridge->delete_document(
			$document->get_document_id()
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
			$this->index_document( $post );
		} else {
			$this->delete_document( $post );
		}
	}


	/**
	 * Handle Save Post
	 *
	 * @param integer $post_id Post ID.
	 * @return void
	 */
	public function handle_save_post( int $post_id ) {
		$post = get_post( $post_id );
		$this->index_document( $post );
	}

	/**
	 * Handle Delete Post
	 *
	 * @param integer $post_id Post ID.
	 * @return void
	 */
	public function handle_delete_post( int $post_id ) {
		$post = get_post( $post_id );
		$this->delete_document( $post );
	}

	/**
	 * Handle Save Term
	 *
	 * @param integer $term_id   Term ID.
	 * @param integer $tax_id    Taxonomy ID.
	 * @param string  $tax_slug  Taxonomy slug.
	 * @return void
	 */
	public function handle_save_term( int $term_id, int $tax_id, string $tax_slug ) {
		$term = get_term( $term_id, $tax_slug );
		$this->index_document( $term );
	}

	/**
	 * Handle Delete Term
	 *
	 * @param integer $term_id   Term ID.
	 * @param string  $tax_slug  Taxonomy slug.
	 * @return void
	 */
	public function handle_delete_term( int $term_id, string $tax_slug ) {
		$term = get_term( $term_id, $tax_slug );
		$this->delete_document( $term );
	}

	/**
	 * Handle save user
	 *
	 * @param integer $user_id User ID.
	 * @return void
	 */
	public function handle_save_user( int $user_id ) {
		$user = get_user_by( 'id', $user_id );
		$this->index_document( $user );
	}

	/**
	 * Handle delete user.
	 *
	 * @param integer  $user_id   User ID.
	 * @param int|null $reassign  User data should be reassigned to.
	 * @param \WP_User $user      WP_User object.
	 * @return void
	 */
	public function handle_delete_user( int $user_id, $reassign, \WP_User $user ) {
		$this->delete_document( $user );
	}
}
