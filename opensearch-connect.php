<?php
/**
 * Plugin Name: OpenSearch Connect
 * Plugin URI: https://www.stormrockwell.com
 * Description: Flexible plugin for developers that adds the ability to sync and search for data on your site using OpenSearch.
 * Version: 0.1
 * Author: Storm Rockwell
 * Author URI: https://www.stormrockwell.com
 * License: GPL3
 *
 * @package Opensearch_Connect
 */

define( 'OSC_CONNECT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

require_once OSC_CONNECT_PLUGIN_PATH . 'vendor/autoload.php';

// Load classes.
spl_autoload_register(
	function( $class ) {
		$file_parts = explode( '\\', $class );

		// Convert class names to file paths.
		if ( count( $file_parts ) > 1 && 'OSC' === $file_parts[0] ) {

			// Remove OSC namespace.
			unset( $file_parts[0] );

			// Add class prefix to last file.
			$last_key                = array_key_last( $file_parts );
			$file_parts[ $last_key ] = 'class-' . $file_parts[ $last_key ] . '.php';

			// Set case and word delimiters.
			$file = implode( DIRECTORY_SEPARATOR, $file_parts );
			$file = strtolower( $file );
			$file = str_replace( '_', '-', $file );

			require_once OSC_CONNECT_PLUGIN_PATH . 'includes/' . $file;
		}
	}
);

OSC\Client_Bridge::get_instance();
