<?php
/**
 * Document Term class.
 *
 * @package Opensearch_Connect
 */

declare( strict_types = 1 );

namespace OSC\Document;

/**
 * Term class.
 */
class Term extends \OSC\Document {

	/**
	 * Get Data
	 *
	 * @param  array|object $object  Object or array used to parse out field data.
	 * @return bool
	 */
	protected function set_field_data_from_object( $object ) : bool {
		if ( ! is_a( $object, 'WP_Term' ) ) {
			return false;
		}

		// Get meta data.
		$meta_data = $object->meta;

		// Set field data.
		$this->fields = wp_parse_args(
			array(
				'document_location' => array(
					'id'     => $object->term_id,
					'object' => 'term',
					'type'   => $object->taxonomy,
				),
				'parent_id'         => $object->post_parent,
				'title'             => $object->name,
				'slug'              => $object->slug,
				'content'           => $this->format_content( $object->description ),
				'menu_order'        => 0, // TODO: Add support for taxonomy terms order plugin (term_order).
				'url'               => get_term_link( $object ),
				'meta'              => $meta_data,
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

		return 'term-' . $this->fields['document_location']['id'];
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
