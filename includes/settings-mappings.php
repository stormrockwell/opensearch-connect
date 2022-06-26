<?php
/**
 * Index Settings & Mappings
 *
 * @package Opensearch_Connect
 */

$osc_settings_mappings = array(
	'settings' => array(
		'analysis' => array(
			'analyzer'  => array(
				'default'        => array(
					'tokenizer' => 'standard',
					'filter'    => array( 'lowercase', 'synonym', 'snowball', 'stop', 'word_delimiter_graph' ),
				),
				'ngram_analyzer' => array(
					'tokenizer' => 'ngram_tokenizer',
				),
			),
			'tokenizer' => array(
				'ngram_tokenizer' => array(
					'type'        => 'ngram',
					'min_gram'    => 3,
					'max_gram'    => 3,
					'token_chars' => array(
						'letter',
						'digit',
					),
				),
			),
			'filter'    => array(
				'synonym' => array(
					'type'     => 'synonym',
					'synonyms' => array(), // TODO: Create field for synonyms.
				),
			),
		),
	),
	'mappings' => array(
		'properties' => array(
			'document_location' => array(
				'type'       => 'nested',
				'properties' => array(
					'id'     => array(
						'type' => 'long',
					),
					'object' => array(
						'type' => 'keyword',
					),
					'type'   => array(
						'type' => 'keyword',
					),
				),
			),
			'menu_order'        => array(
				'type' => 'long',
			),
			'image_id'          => array(
				'type' => 'long',
			),
			'title'             => array(
				'type'     => 'text',
				'analyzer' => 'simple',
				'fields'   => array(
					'raw'   => array(
						'type' => 'keyword',
					),
					'ngram' => array(
						'type'     => 'text',
						'analyzer' => 'ngram_analyzer',
					),
				),
			),
			'slug'              => array(
				'type' => 'keyword',
			),
			'content'           => array(
				'type' => 'text',
			),
			'excerpt'           => array(
				'type' => 'text',
			),
			'keywords'          => array(
				'type' => 'text',
			),
			'url'               => array(
				'type' => 'keyword',
			),
			'tax'               => array(
				'type' => 'nested',
			),
			'meta'              => array(
				'type' => 'nested',
			),
		),
	),
);

/**
 * Filter for settings & mappings.
 *
 * @hook osc/settings_mappings
 * @param  array $osc_settings_mappings Settings & mappings for OpenSearch index.
 * @return array
 */
return apply_filters( 'osc/settings_mappings', $osc_settings_mappings );
