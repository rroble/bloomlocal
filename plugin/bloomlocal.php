<?php
/**
 * Plugin Name: Bloomlocal
 * Plugin URI: https://bloomlocal.net/
 * Description: Various WooCommerce enhancements for florists websites.
 * Version: 0.1.21
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

define('BLOOMLOCAL_PLUGIN_VERSION', '0.1.21');
define('BLOOMLOCAL_PLUGIN_BASE', plugin_basename(__FILE__));
define('BLOOMLOCAL_PLUGIN', __FILE__);

require_once __DIR__ . '/inc/admin_filter_orders_by_delivery_date.php';
require_once __DIR__ . '/inc/email_format_delivery_phone.php';
require_once __DIR__ . '/inc/cart.php';
require_once __DIR__ . '/inc/checkout.php';
require_once __DIR__ . '/inc/filter_price.php';
require_once __DIR__ . '/inc/store_hours.php';
require_once __DIR__ . '/inc/orders_api.php';
require_once __DIR__ . '/inc/add_to_cart_button.php';
require_once __DIR__ . '/inc/updater.php';


function admin_script() {
    // Only add to the edit.php admin page.
    // See WP docs.
    wp_enqueue_style('jquery-ui.multidatespicker-css', plugin_dir_url(__FILE__) . 'Multiple-Dates-Picker/jquery-ui.multidatespicker.css');
    wp_enqueue_script('jquery-ui.multidatespicker-js', plugin_dir_url(__FILE__) . 'Multiple-Dates-Picker/jquery-ui.multidatespicker.js', array('jquery-ui-datepicker'), null, true);
    wp_enqueue_script('admin_datepicker', plugin_dir_url(__FILE__) . 'admin_datepicker.js', array('jquery-ui.multidatespicker-js'), null, true);
}

add_action('admin_enqueue_scripts', 'admin_script');