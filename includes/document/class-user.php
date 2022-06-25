<?php
/**
 * Document User class.
 *
 * @package Opensearch_Connect
 */

declare( strict_types = 1 );

namespace OSC\Document;

/**
 * Term class.
 */
class User extends \OSC\Document {

	/**
	 * Get Data
	 *
	 * @param  array|object $object  Object or array used to parse out field data.
	 * @return bool
	 */
	protected function set_field_data_from_object( $object ) : bool {
		if ( ! is_a( $object, 'WP_User' ) ) {
			return false;
		}

		// Get meta and turn the results into single values instead of arrays.
		$first_name  = get_user_meta( $object->ID, 'first_name', true );
		$last_name   = get_user_meta( $object->ID, 'last_name', true );
		$description = get_user_meta( $object->ID, 'description', true );

		// Set field data.
		$this->fields = wp_parse_args(
			array(
				'document_location' => array(
					'id'     => $object->ID,
					'object' => 'user',
					'type'   => '',
				),
				'title'             => $object->data->display_name,
				'slug'              => $object->data->user_nicename,
				'content'           => $this->format_content( $description ),
				'menu_order'        => 0, // TODO: Look into potential ordering options.
				'url'               => get_author_posts_url( $object->data->ID ),
				'meta'              => array(
					'first_name' => $first_name,
					'last_name'  => $last_name,
				),
			),
			$this->base_fields
		);

		return true;
	}

	/**
	 * Get Document ID.
	 *
	 * @return false|integer
	 */
	public function get_document_id() {
		if ( empty( $this->fields ) || ! $this->fields['document_location']['id'] ) {
			return false;
		}

		return 'user-' . $this->fields['document_location']['id'];
	}

	/**
	 * Format content
	 *
	 * @param  string $content  Content to format.
	 * @return string
	 */
	protected function format_content( string $content ) : string {
		$content = do_shortcode( apply_filters( 'the_content', $content ) ); // phpcs:ignore
		$content = wp_strip_all_tags( $content );

		return $content;
	}
}
