<?php
/**
 * Document Post class.
 *
 * @package Opensearch_Connect
 */

declare( strict_types = 1 );

namespace OSC\Document;

/**
 * Post class.
 */
class Post extends \OSC\Document {

	/**
	 * Get Data
	 *
	 * @param  array|object $object  Object or array used to parse out field data.
	 * @return bool
	 */
	protected function set_field_data_from_object( $object ) : bool {
		if ( ! is_a( $object, 'WP_Post' ) ) {
			return false;
		}

		// Get taxonomy data.
		$object_taxonomies = get_object_taxonomies( $object->post_type );
		$tax_data          = array_map(
			function( $tax_slug ) {
				return wp_get_post_terms( $tax_slug );
			},
			$object_taxonomies
		);

		// Get meta data.
		$meta_data = get_post_meta( $object->ID );

		$this->fields = wp_parse_args(
			$this->base_fields,
			array(
				'document_location' => array(
					'id'     => $object->ID,
					'object' => 'post',
					'type'   => $object->post_type,
				),
				'title'             => $object->post_title,
				'content'           => $object->content,
				'excerpt'           => $object->excerpt,
				'media_id'          => get_post_thumbnail_id( $object->ID ),
				'menu_order'        => $object->menu_order,
				'url'               => '',
				'tax'               => $tax_data, // Taxonomies and terms.
				'meta'              => $meta_data,
			)
		);

		return true;
	}
}
