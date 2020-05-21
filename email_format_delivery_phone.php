<?php

$shipping_phone = null;

add_filter('woocommerce_email_order_meta_fields', function ($fields, $sent_to_admin, $order, $filter = true) use (&$shipping_phone) {
    if ($filter && isset($fields['shipping_phone'])) {
        $shipping_phone = $fields['shipping_phone']['value'];
        unset($fields['shipping_phone']);
    }
    return $fields;
}, 20, 4);

add_filter('woocommerce_order_get_formatted_shipping_address', function ($address, $raw_address, $order) use (&$shipping_phone) {
    if (isset($shipping_phone)) {
        return $address . '<br>' . wc_make_phone_clickable($shipping_phone);
    }
    return $address;
}, 20, 3);
