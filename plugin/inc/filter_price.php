<?php

add_filter('posts_clauses', function ($args, $wp_query) {
    global $wpdb;

    if ( ! $wp_query->is_main_query() || ( ! isset( $_GET['max_price'] ) && ! isset( $_GET['min_price'] ) ) ) {
        return $args;
    }

    // undo wc filter query
    if ( strstr( $args['join'], 'wc_product_meta_lookup' ) ) {
        $find = 'wc_product_meta_lookup.max_price <= ';
        $left = strpos($args['where'], $find);
        $right = strpos($args['where'], ' ', $left + strlen($find));
        if ($left === false || $right === false) {
            return $args;
        }

        $right_bound = substr($args['where'], $left, $right-$left);
        $args['where'] = str_replace($right_bound, "{$right_bound} */", $args['where']);
        // left bound
        $args['where'] = str_replace('AND wc_product_meta_lookup.min_price', '/* AND wc_product_meta_lookup.min_price', $args['where']);

        $sql = "LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id";
        $args['join'] = str_replace($sql, "/* {$sql} */", $args['join']);
    }

    if ( ! strstr( $args['join'], $wpdb->postmeta ) ) {
        $args['join'] .= " LEFT JOIN {$wpdb->postmeta} ON ($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = '_price') ";
    }

    $get_value = function ($marker) use ($args) {
        $left = strpos($args['where'], $marker);
        $right = strpos($args['where'], ' ', $left + strlen($marker));
        if ($left === false || $right === false) {
            return '0';
        }

        $value = substr($args['where'], $left + strlen($marker), $right - $left - strlen($marker));

        return (float) $value;
    };
    $GLOBALS['filter_price_min'] = $get_value('wc_product_meta_lookup.min_price >= ');
    $GLOBALS['filter_price_max'] = $get_value('wc_product_meta_lookup.max_price <= ');

    $args['where'] .= $wpdb->prepare(
        " AND $wpdb->postmeta.meta_value BETWEEN %f AND %f",
        $GLOBALS['filter_price_min'],
        $GLOBALS['filter_price_max']
    );

    return $args;

}, 20, 2);

add_filter('woocommerce_variation_prices', function ($prices, $product, $for_display) {
    if (!$GLOBALS['filter_price_min'] || !$GLOBALS['filter_price_max']) {
        return $prices;
    }

    foreach ( $prices as $price_key => $variation_prices ) {
        foreach ($variation_prices as $var_key => $price) {
            if ($price < $GLOBALS['filter_price_min'] || $price > $GLOBALS['filter_price_max']) {
                unset($prices[$price_key][$var_key]);
            }
        }
    }

    return $prices;
}, 20, 3);
