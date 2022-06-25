<?php
/**
 * Document Custom class.
 *
 * @package Opensearch_Connect
 */

declare( strict_types = 1 );

namespace OSC\Document;

/**
 * Custom class.
 */
class Custom extends \OSC\Document {

	/**
	 * Get Data
	 *
	 * @param  array|object $object  Object or array used to parse out field data.
	 * @return bool
	 */
	protected function set_field_data_from_object( $object ) : bool {
		if ( ! is_object( $object ) && ! is_array( $object ) ) {
			return false;
		}

		$array = is_object( $object ) ? (array) $object : $object;

		// Set field data.
		$this->fields = wp_parse_args(
			$array,
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

		return 'custom-' . $this->fields['document_location']['id'];
	}
}
