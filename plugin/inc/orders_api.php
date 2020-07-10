<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('woocommerce_rest_shop_order_object_query', function ($args, $request) {
	global $wpdb;

	if (!empty($request['delivery_date'])) {
		$order_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT order_id
				FROM {$wpdb->prefix}woocommerce_order_items
				WHERE order_item_id IN ( SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = 'Delivery Date' AND meta_value = '%s' )
				AND order_item_type = 'line_item'",
				$request['delivery_date']
			)
		);

		// Force WP_Query return empty if don't found any order.
		$order_ids = ! empty( $order_ids ) ? $order_ids : array( 0 );

		$args['post__in'] = $order_ids;
	}

	return $args;
}, 20, 2);
