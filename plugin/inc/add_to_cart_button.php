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

add_filter('woocommerce_loop_add_to_cart_link', function ($link, $product, $args) {
	return null;
}, 20, 3);
