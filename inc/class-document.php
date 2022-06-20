<?php
/**
 * Document Class
 *
 * @package Opensearch_Connect
 */

declare( strict_types = 1 );

namespace OSC;

/**
 * Document abstract class.
 */
abstract class Document {

	/**
	 * OpenSearch Base Fields
	 *
	 * @var array
	 */
	private $base_fields = array(
		'document_location' => array(
			'id'      => 1,
			'site_id' => 0,
			'object'  => '', // post, user, term, etc.
			'type'    => '', // post, page, media, etc.
		),
		'menu_order'        => 0,
		'media_id'          => 0,
		'title'             => '',
		'content'           => '',
		'excerpt'           => '',
		'keywords'          => '',
		'url'               => '',
		'tax'               => array(), // Taxonomies and terms.
		'meta'              => array(),
		'hide_from_search'  => false,
	);

	/**
	 * Fields and values for document.
	 *
	 * @var array
	 */
	private $fields = array();

	/**
	 * Constructor
	 *
	 * @param object|array $object  WP Object or array used to build field data.
	 */
	public function __construct( $object ) {
		$this->set_field_data_from_object( $object );
	}

	/**
	 * Get Data
	 *
	 * @param  array|object $object  Object or array used to parse out field data.
	 * @return void
	 */
	abstract protected function set_field_data_from_object( $object );

	/**
	 * Get field data.
	 *
	 * @return  array|bool  Return false if field data was never set.
	 */
	public function get_field_data() {
		$field_data = apply_filters( 'osc/document/get_field_data', $this->fields );

		return ! empty( $field_data ) ? $field_data : false;
	}
}