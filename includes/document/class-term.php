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

		$hide_from_search = 'false';
		if ( 'category' === $object->taxonomy && 'Uncategorized' === $object->name ) {
			/**
			 * Filter for hiding uncategorized from search
			 *
			 * @hook osc/document/term/hide_uncategorized_from_search
			 * @param  bool     $value   Whether or not to hide the term from search.
			 * @param  \WP_Term $object  WP_Term object.
			 * @return bool
			 */
			$hide_from_search = apply_filters( 'osc/document/term/hide_uncategorized_from_search', true, $object );
		}

		// Set field data.
		$this->fields = wp_parse_args(
			array(
				'document_location' => array(
					'id'     => $object->term_id,
					'object' => 'term',
					'type'   => $object->taxonomy,
				),
				'parent_id'         => $object->parent,
				'title'             => $object->name,
				'slug'              => $object->slug,
				'content'           => $this->format_content( $object->description ),
				'menu_order'        => 0, // TODO: Add support for taxonomy terms order plugin (term_order).
				'url'               => get_term_link( $object ),
				'meta'              => $object->meta,
				'hide_from_search'  => $hide_from_search,
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
}
