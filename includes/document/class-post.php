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
		$tax_data          = array();
		foreach ( $object_taxonomies as $tax_slug ) {
			$tax_data[ $tax_slug ] = wp_get_post_terms( $tax_slug );
		}

		// Get meta data.
		$meta_data                  = get_post_meta( $object->ID );
		$meta_data['author']        = $object->post_author;
		$meta_data['status']        = $object->post_status;
		$meta_data['date']          = $object->post_date;
		$meta_data['date_modified'] = $object->post_modified;

		// Set field data.
		$this->fields = wp_parse_args(
			array(
				'document_location' => array(
					'id'     => $object->ID,
					'object' => 'post',
					'type'   => $object->post_type,
				),
				'parent_id'         => $object->post_parent,
				'title'             => $object->post_title,
				'content'           => $object->post_content,
				'excerpt'           => $object->post_excerpt,
				'media_id'          => get_post_thumbnail_id( $object->ID ),
				'menu_order'        => $object->menu_order,
				'url'               => get_permalink( $object ),
				'tax'               => $tax_data, // Taxonomies and terms.
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

		return 'post-' . $this->fields['document_location']['id'];
	}
}
