<?php
/**
 * Plugin Name: Bloomlocal
 * Plugin URI: https://bloomlocal.net/
 * Description: Various WooCommerce enhancements for florists websites.
 * Version: 0.1.9
 * Author: Arcanys
 * Author URI: https://arcanys.com/
 * Text Domain: bloomlocal
 *
 * @package Bloomlocal
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define('BLOOMLOCAL_PLUGIN_VERSION', '0.1.9');

require_once __DIR__ . '/inc/admin_filter_orders_by_delivery_date.php';
require_once __DIR__ . '/inc/email_format_delivery_phone.php';
require_once __DIR__ . '/inc/cart.php';
// require_once __DIR__ . '/inc/export_orders.php';

require_once __DIR__ . '/inc/updater.php';

add_action('init', function() {
    Bloomlocal_Updater::init(BLOOMLOCAL_PLUGIN_VERSION, plugin_basename(__FILE__));
});
