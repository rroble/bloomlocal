<?php

/**
 * Category/shop products list
 * 
 * Hide add to cart button.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$show_add_to_cart_button = false;

add_action('woocommerce_cart_collaterals', function () {
	global $show_add_to_cart_button;
	$show_add_to_cart_button = true;
});

add_filter('woocommerce_loop_add_to_cart_link', function ($link, $product, $args) {
	global $show_add_to_cart_button;
	return $show_add_to_cart_button ? $link : null;
}, 20, 3);
