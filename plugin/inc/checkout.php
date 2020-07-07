<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checkout
 * 
 * Required fields validation / html5.
 */
wp_register_script('bloomlocal-checkout', plugins_url('checkout.js', BLOOMLOCAL_PLUGIN), array('jquery'));
wp_enqueue_script('bloomlocal-checkout');
