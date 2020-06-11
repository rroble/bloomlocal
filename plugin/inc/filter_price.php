<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Bloomlocal_Price_Filter {
    private static $min_price = null;
    private static $max_price = null;
    private static $search_range = true; // false min_value only

    public static function apply($args, $wp_query) {
        global $wpdb;

        // must have filter/s
        if (
            $wp_query->is_main_query() && !isset($_GET['max_price']) && !isset($_GET['min_price']) || 
            !$wp_query->is_main_query() && !static::$min_price && !static::$max_price
            ) {
			return $args;
		}

        if (strstr($args['join'], 'wc_product_meta_lookup')) {
            $find = 'wc_product_meta_lookup.max_price <= ';
            $left = strpos($args['where'], $find);
            $right = strpos($args['where'], ' ', $left + strlen($find));
            if ($left !== false && $right !== false) {
                // undo wc filter query
                $right_bound = substr($args['where'], $left, $right-$left);
                $args['where'] = str_replace($right_bound, "{$right_bound} */", $args['where']);
                $args['where'] = str_replace('AND wc_product_meta_lookup.min_price', '/* AND wc_product_meta_lookup.min_price', $args['where']);

                // use values
                static::$min_price = static::get_value('wc_product_meta_lookup.min_price >= ', $args['where']);
                static::$max_price = static::get_value('wc_product_meta_lookup.max_price <= ', $args['where']);
            }
        } else {
            // make join
            $args['join'] .= " LEFT JOIN {$wpdb->wc_product_meta_lookup} wc_product_meta_lookup ON $wpdb->posts.ID = wc_product_meta_lookup.product_id ";
        }

        if (static::$search_range) {
            if ( ! strstr( $args['join'], $wpdb->postmeta ) ) {
                $args['join'] .= " LEFT JOIN {$wpdb->postmeta} ON ($wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->postmeta.meta_key = '_price') ";
            }
            $args['where'] .= $wpdb->prepare(
                " AND CAST($wpdb->postmeta.meta_value as SIGNED) BETWEEN %f AND %f",
                static::$min_price,
                static::$max_price
            );
            $args['orderby'] = str_replace('wc_product_meta_lookup.max_price DESC', "$wpdb->postmeta.meta_value*1 DESC", $args['orderby']);
            $args['orderby'] = str_replace('wc_product_meta_lookup.min_price ASC', "$wpdb->postmeta.meta_value*1 ASC", $args['orderby']);
        } else {
            $args['where'] .= $wpdb->prepare(
                " AND wc_product_meta_lookup.min_price BETWEEN %f AND %f",
                static::$min_price,
                static::$max_price
            );
            $args['orderby'] = str_replace('wc_product_meta_lookup.max_price DESC', 'wc_product_meta_lookup.min_price DESC', $args['orderby']);
        }

        return $args;
    }

    private static function get_value($marker, $where) {
        $left = strpos($where, $marker);
        $right = strpos($where, ' ', $left + strlen($marker));
        if ($left === false || $right === false) {
            return '0';
        }

        $value = substr($where, $left + strlen($marker), $right - $left - strlen($marker));

        return (float) $value;
    }

    public static function paginate() {
        if (empty($_SERVER['HTTP_REFERER'])) {
            return;
        }

        $vars = array();
        parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $vars);

        if (empty($vars['min_price']) && empty($vars['max_price'])) {
            return;
        }

        // from woocommerce/includes/class-wc-query.php::529
        static::$min_price = isset( $vars['min_price'] ) ? floatval( wp_unslash( $vars['min_price'] ) ) : 0; // WPCS: input var ok, CSRF ok.
        static::$max_price = isset( $vars['max_price'] ) ? floatval( wp_unslash( $vars['max_price'] ) ) : PHP_INT_MAX; // WPCS: input var ok, CSRF ok.

        /**
         * Adjust if the store taxes are not displayed how they are stored.
         * Kicks in when prices excluding tax are displayed including tax.
         */
        if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
            $tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
            $tax_rates = WC_Tax::get_rates( $tax_class );

            if ( $tax_rates ) {
                static::$min_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( static::$min_price, $tax_rates ) );
                static::$max_price -= WC_Tax::get_tax_total( WC_Tax::calc_inclusive_tax( static::$max_price, $tax_rates ) );
            }
        }
    }

    public static function price($prices, $product, $for_display) {
        if (!static::$min_price || !static::$max_price) {
            return $prices;
        }

        foreach ( $prices as $price_key => $variation_prices ) {
            foreach ($variation_prices as $var_key => $price) {
                if ($price < static::$min_price || $price > static::$max_price) {
                    unset($prices[$price_key][$var_key]);
                }
            }
        }

        return $prices;
    }
}

add_filter('posts_clauses', array('Bloomlocal_Price_Filter', 'apply'), 20, 2);
add_filter('woocommerce_variation_prices', array('Bloomlocal_Price_Filter', 'price'), 20, 30);
add_action('astra_shop_pagination_infinite', array('Bloomlocal_Price_Filter', 'paginate'), 20);
