<?php
/**
 * Plugin Name: Elastic Connect
 * Plugin URI: https://www.stormrockwell.com
 * Description: Flexible plugin for developers that adds the ability to sync and search for data on your site using ElasticSearch.
 * Version: 0.1
 * Author: Storm Rockwell
 * Author URI: https://www.stormrockwell.com
 * License: GPL2
 */

define( 'ELASTIC_CONNECT_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

require_once ELASTIC_CONNECT_PLUGIN_PATH . 'vendor/autoload.php';
require_once ELASTIC_CONNECT_PLUGIN_PATH . 'inc/class-client-bridge.php';

Opensearch_Connect\Client_Bridge::get_instance();
