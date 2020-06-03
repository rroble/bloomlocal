<?php
/**
 * Plugin Name: Bloomlocal
 * Plugin URI: https://bloomlocal.net/
 * Description: Bloomlocal
 * Version: 0.1.8
 * Author: Randolph Roble
 * Author URI: https://github.com/rroble
 * Text Domain: bloomlocal
 *
 * @package Bloomlocal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('BLOOMLOCAL_PLUGIN_VERSION', '0.1.8');

require_once __DIR__ . '/admin_filter_orders_by_delivery_date.php';
require_once __DIR__ . '/email_format_delivery_phone.php';
require_once __DIR__ . '/cart.php';
require_once __DIR__ . '/export_orders.php';

require_once __DIR__ . '/updater.php';

add_action('init', function() {
    Bloomlocal_Updater::init(BLOOMLOCAL_PLUGIN_VERSION, plugin_basename(__FILE__));
});
