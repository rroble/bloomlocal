<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('woocommerce_checkout_process', function () {

    $check_same_day_cutoff = function ($date) {
        $tz = new DateTimeZone('Europe/London');
        $delivery = Datetime::createFromFormat('d/m/Y', $date, $tz);

        $now = new Datetime('now', $tz);

        // check only same day
        if ($delivery->format('Y-m-d') != $now->format('Y-m-d')) {
            return;
        }

        // Cut off 11am Same-Day Delivery
        $day_key = 'st_'.strtolower(date('l'));
        $cutoffValue = get_option($day_key, 13);
        $cutoff = clone $now;
        $cutoff->setTime($cutoffValue, 0);

        if ($now->getTimestamp() > $cutoff->getTimestamp()) {
            $showCutoff = bloomlocal_to_human_hours($cutoffValue);
            throw new Exception("<strong>Cut off $showCutoff Same-Day Delivery</strong> Please change delivery date.");
        }
    };

    foreach (WC()->cart->get_cart_contents() as $item) {
        if (isset($item[WCPA_CART_ITEM_KEY]))
        foreach ($item[WCPA_CART_ITEM_KEY] as $field) {
            if (isset($field['label']) && $field['label'] == 'Delivery Date' && !empty($field['value'])) {
                $check_same_day_cutoff($field['value']);
            }
        }
    }
}, 20, 2);
