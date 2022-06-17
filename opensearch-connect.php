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
require_once OSC_CONNECT_PLUGIN_PATH . 'inc/class-client-bridge.php';

OSC\Client_Bridge::get_instance();
