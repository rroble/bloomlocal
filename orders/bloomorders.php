<?php
/**
 * Plugin Name: Bloomlocal Orders
 * Plugin URI: https://orders.bloomlocal.net/
 * Description: One click export for bloomlocal sites.
 * Version: 0.1.0
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

$export_options = null;

add_filter('option_woocommerce-order-export-now', function ($options) {
    global $export_options;
    $export_options = $options;
    return $options;
}, 20, 1);

// add_filter('woe_order_export_started', function ($order_id) {
//     if (strpos($order_id, 'york_') !== false) {
//         $data = json_decode(file_get_contents(__DIR__.'/orders/yorkflorists.co.uk_2020-07-08.json'), true);
//         $id = str_replace('york_', '', $order_id);
//         foreach ($data as $row) {
//             if ($row['id'] == $id) {
//                 $order = new WC_Order($row);
//                 $order->set_id($order_id);
//                 return $order;
//             }
//         }
//     }
//     return $order_id;
// }, 20, 1);

add_filter('woe_get_order_ids', function ($order_ids) {
    global $export_options;
    // $data = json_decode(file_get_contents(__DIR__.'/orders/yorkflorists.co.uk_2020-07-08.json'), true);
    // $order_ids = array(17177, 'york_line');
    // foreach ($data as $row) {
    //     $order_ids[] = 'york_'.$row['id'];
    // }
    var_dump($order_ids, $export_options);

    return $order_ids;
}, 20, 1);


add_filter('woe_fetch_order_row', function ($row, $order_id) {
    if ($order_id == 'york_line') {
        $row['order_number'] = 'yorkflorists.co.uk';
        return $row;
    }
    if (strpos($order_id, 'york_') !== false) {
        $id = str_replace('york_', '', $order_id);
        $data = json_decode(file_get_contents(__DIR__.'/orders/yorkflorists.co.uk_2020-07-08.json'), true);
        foreach ($data as $_row) {
            if ($_row['id'] == $id) {
                // $row['_row'] = $_row;
                // return array_merge($row, $_row);
                foreach ($_row as $key => $value) {
                    if ($key == 'line_items') {
                        foreach ($value as $lid => $line_item) {
                            $row['products'][] = array(
                                'sku' => $line_item['sku'],
                                'line_id' => 1+$lid,
                                'name' => $line_item['name'],
                                'qty' => $line_item['quantity'],
                                'item_price' => $line_item['price'],
                            );
                        }
                        // continue;
                    }
                    // if (is_array($value)) {
                    //     foreach ($value as $valkey => $subvalue) {
                    //         $row[$key.'_'.$valkey] = $subvalue;
                    //     }
                    // } else {
                    //     $row['order_'.$key] = $value;
                    //     $row[$key] = $value;
                    // }
                }
                // $row['order_date'] = date('Y-m-d H:i:s', strtotime($_row['date_paid'])); // TODO: london time
                // $row['billing_address'] = $_row['billing']['address_1'].', '.$_row['billing']['address_2'];
                // $row['shipping_address'] = $_row['shipping']['address_1'].', '.$_row['shipping']['address_2'];
                // $row['cart_discount'] = $_row['discount_total'];
                // if (!empty($_row['shipping_lines'][0]['method_title'])) {
                //     $row['shipping_method_title'] = $_row['shipping_lines'][0]['method_title'];
                //     $row['order_shipping'] = $_row['shipping_lines'][0]['total'];
                // }
                // return $row;

                foreach ($row as $field => $value) {
                    if (is_array($value)) {
                    } elseif ( $field == 'order_id' ) {
                        $row[$field] =  $row['id'];
                    }
                    // } elseif ( $field == 'order_date' ) {
                    //     $row[$field] =  ! method_exists( $this->order,
                    //         "get_date_created" ) ? $this->order->order_date : ( $this->order->get_date_created() ? gmdate( 'Y-m-d H:i:s',
                    //         $this->order->get_date_created()->getOffsetTimestamp() ) : '' );
                    // } elseif ( $field == 'modified_date' ) {
                    //     $row[$field] = ! method_exists( $this->order,
                    //         "get_date_modified" ) ? $this->order->modified_date : ( $this->order->get_date_modified() ? gmdate( 'Y-m-d H:i:s',
                    //         $this->order->get_date_modified()->getOffsetTimestamp() ) : '' );
                    // } elseif ( $field == 'completed_date' ) {
                    //     $row[$field] = ! method_exists( $this->order,
                    //         "get_date_completed" ) ? $this->order->completed_date : ( $this->order->get_date_completed() ? gmdate( 'Y-m-d H:i:s',
                    //         $this->order->get_date_completed()->getOffsetTimestamp() ) : '' );
                    // } elseif ( $field == 'paid_date' ) {
                    //     $row[$field] = ! method_exists( $this->order,
                    //         "get_date_paid" ) ? $this->order->paid_date : ( $this->order->get_date_paid() ? gmdate( 'Y-m-d H:i:s',
                    //         $this->order->get_date_paid()->getOffsetTimestamp() ) : '' );
                    // } elseif ( $field == 'order_number' ) {
                    //     $row[$field] = $this->parent_order ? $this->parent_order->get_order_number() : $this->order->get_order_number(); // use parent order number
                    // } elseif ( $field == 'order_subtotal' ) {
                    //     $row[$field] = wc_format_decimal( $this->order->get_subtotal(), 2 );
                    // } elseif ( $field == 'order_subtotal_minus_discount' ) {
                    //     $row[$field] = $this->order->get_subtotal() - $this->order->get_total_discount();
                    // } elseif ( $field == 'order_subtotal_refunded' ) {
                    //     $row[$field] = wc_format_decimal( WC_Order_Export_Data_Extractor::get_order_subtotal_refunded( $this->order ), 2 );
                    // } elseif ( $field == 'order_subtotal_minus_refund' ) {
                    //     $row[$field] = wc_format_decimal( $this->order->get_subtotal() - WC_Order_Export_Data_Extractor::get_order_subtotal_refunded( $this->order ),
                    //         2 );
                    //     //order total
                    // } elseif ( $field == 'order_total' ) {
                    //     $row[$field] = $this->order->get_total();
                    // } elseif ( $field == 'order_total_no_tax' ) {
                    //     $row[$field] = $this->order->get_total() - $this->order->get_total_tax();
                    // } elseif ( $field == 'order_refund' ) {
                    //     $row[$field] = $this->order->get_total_refunded();
                    // } elseif ( $field == 'order_total_inc_refund' ) {
                    //     $row[$field] = $this->order->get_total() - $this->order->get_total_refunded();
                    //     //shipping
                    // } elseif ( $field == 'order_shipping' ) {
                    //     $row[$field] = method_exists($this->order,"get_shipping_total") ? $this->order->get_shipping_total() : $this->order->get_total_shipping();
                    // } elseif ( $field == 'order_shipping_plus_tax' ) {
                    //     $row[$field] = ( method_exists($this->order,"get_shipping_total") ? $this->order->get_shipping_total() : $this->order->get_total_shipping() ) + $this->order->get_shipping_tax();
                    // } elseif ( $field == 'order_shipping_refunded' ) {
                    //     $row[$field] = $this->order->get_total_shipping_refunded();
                    // } elseif ( $field == 'order_shipping_minus_refund' ) {
                    //     $row[$field] = ( method_exists($this->order,"get_shipping_total") ? $this->order->get_shipping_total() : $this->order->get_total_shipping() ) - $this->order->get_total_shipping_refunded();
                    //     //shipping tax
                    // } elseif ( $field == 'order_shipping_tax_refunded' ) {
                    //     $row[$field] = WC_Order_Export_Data_Extractor::get_order_shipping_tax_refunded( $this->order_id );
                    // } elseif ( $field == 'order_shipping_tax_minus_refund' ) {
                    //     $row[$field] = $this->order->get_shipping_tax() - WC_Order_Export_Data_Extractor::get_order_shipping_tax_refunded( $this->order_id );
                    //     //order tax
                    // } elseif ( $field == 'order_tax' ) {
                    //     $row[$field] = wc_round_tax_total( $this->order->get_cart_tax() );
                    // } elseif ( $field == 'order_total_fee' ) {
                    //     $row[ $field ] = array_sum( array_map( function ( $item ) {
                    //         return $item->get_total();
                    //     }, $this->order->get_fees() ) );
                    // } elseif ( $field == 'order_total_tax' ) {
                    //     $row[$field] = wc_round_tax_total( $this->order->get_total_tax() );
                    // } elseif ( $field == 'order_total_tax_refunded' ) {
                    //     $row[$field] = wc_round_tax_total( $this->order->get_total_tax_refunded() );
                    // } elseif ( $field == 'order_total_tax_minus_refund' ) {
                    //     $row[$field] = wc_round_tax_total( $this->order->get_total_tax() - $this->order->get_total_tax_refunded() );
                    // } elseif ( $field == 'order_status' ) {
                    //     $status        = empty( $this->order_status ) ? $this->order->get_status() : $this->order_status;
                    //     $status        = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
                    //     $row[$field] = isset( WC_Order_Export_Data_Extractor::$statuses[ 'wc-' . $status ] ) ? WC_Order_Export_Data_Extractor::$statuses[ 'wc-' . $status ] : $status;
                    // } elseif ( $field == 'user_login' OR $field == 'user_email' OR $field == 'user_url' ) {
                    //     $row[$field] = $this->user ? $this->user->$field : "";
                    // } elseif ( $field == 'user_role' ) {
                    //     $roles         = $wp_roles->roles;
                    //     $row[$field] = ( isset( $this->user->roles[0] ) && isset( $roles[ $this->user->roles[0] ] ) ) ? $roles[ $this->user->roles[0] ]['name'] : ""; // take first role Name
                    // } elseif ( $field == 'customer_total_orders' ) {
                    //     $row[$field] = ( isset( $this->user->ID ) ) ? wc_get_customer_order_count( $this->user->ID ) : 0;
                    // } elseif ( $field == 'customer_first_order_date' ) {
                    //     $first_order = WC_Order_Export_Data_Extractor::get_customer_order( $this->user, $this->order_meta, 'first' );
                    //     $row[$field] = $first_order ? ( $first_order->get_date_created() ? gmdate( 'Y-m-d H:i:s',
                    //         $first_order->get_date_created()->getOffsetTimestamp() ) : '' ) : '';
                    // } elseif ( $field == 'customer_last_order_date' ) {
                    //     $last_order = WC_Order_Export_Data_Extractor::get_customer_order( $this->user, $this->order_meta, 'last' );
                    //     $row[$field] = $last_order? ( $last_order->get_date_created() ? gmdate( 'Y-m-d H:i:s',
                    //         $last_order->get_date_created()->getOffsetTimestamp() ) : '' ) : '';
                    // } elseif ( $field == 'billing_address' ) {
                    //     $row[$field] = join( ", ",
                    //         array_filter( array( $this->order_meta["_billing_address_1"], $this->order_meta["_billing_address_2"] ) ) );
                    // } elseif ( $field == 'shipping_address' ) {
                    //     $row[$field] = join( ", ",
                    //         array_filter( array( $this->order_meta["_shipping_address_1"], $this->order_meta["_shipping_address_2"] ) ) );
                    // } elseif ( $field == 'billing_full_name' ) {
                    //     $row[$field] = trim( $this->order_meta["_billing_first_name"] . ' ' . $this->order_meta["_billing_last_name"] );
                    // } elseif ( $field == 'shipping_full_name' ) {
                    //     $row[$field] = trim( $this->order_meta["_shipping_first_name"] . ' ' . $this->order_meta["_shipping_last_name"] );
                    // } elseif ( $field == 'billing_country_full' ) {
                    //     $row[$field] = isset( WC_Order_Export_Data_Extractor::$countries[ $this->billing_country ] ) ? WC_Order_Export_Data_Extractor::$countries[ $this->billing_country ] : $this->billing_country;
                    // } elseif ( $field == 'shipping_country_full' ) {
                    //     $row[$field] = isset( WC_Order_Export_Data_Extractor::$countries[ $this->shipping_country ] ) ? WC_Order_Export_Data_Extractor::$countries[ $this->shipping_country ] : $this->shipping_country;
                    // } elseif ( $field == 'billing_state_full' ) {
                    //     $country_states = WC()->countries->get_states( $this->billing_country );
                    //     $row[$field] = isset( $country_states[ $this->billing_state ] ) ? html_entity_decode( $country_states[ $this->billing_state ] ) : $this->billing_state;
                    // } elseif ( $field == 'shipping_state_full' ) {
                    //     $country_states = WC()->countries->get_states( $this->shipping_country );
                    //     $row[$field] = isset( $country_states[ $this->shipping_state ] ) ? html_entity_decode( $country_states[ $this->shipping_state ] ) : $this->shipping_state;
                    // } elseif ( $field == 'billing_citystatezip' ) {
                    //     $row[$field] = WC_Order_Export_Data_Extractor::get_city_state_postcode_field_value( $this->order, 'billing' );
                    // } elseif ( $field == 'billing_citystatezip_us' ) {
                    //     $row[$field] = WC_Order_Export_Data_Extractor::get_city_state_postcode_field_value( $this->order, 'billing', true );
                    // } elseif ( $field == 'shipping_citystatezip' ) {
                    //     $row[$field] = WC_Order_Export_Data_Extractor::get_city_state_postcode_field_value( $this->order, 'shipping' );
                    // } elseif ( $field == 'shipping_citystatezip_us' ) {
                    //     $row[$field] = WC_Order_Export_Data_Extractor::get_city_state_postcode_field_value( $this->order, 'shipping', true );
                    // } elseif ( $field == 'products' OR $field == 'coupons' ) {
                    //     if ( isset( $this->data[ $field ] ) ) {
                    //         $row[$field] = $this->data[ $field ];
                    //     }
                    // } elseif ( $field == 'shipping_method_title' ) {
                    //     $row[$field] = $this->order->get_shipping_method();
                    // } elseif ( $field == 'shipping_method' OR $field == 'shipping_method_only') {
                    //     $shipping_methods = $this->order->get_items( 'shipping' );
                    //     $shipping_method  = reset( $shipping_methods ); // take first entry
                    //     if ( ! empty( $shipping_method ) ) {
                    //         $row[$field] = $field == 'shipping_method_only' ? $shipping_method['method_id'] : $shipping_method['method_id'] . ':' . $shipping_method['instance_id'];
                    //     }
                    // } elseif ( $field == 'coupons_used' ) {
                    //     $row[$field] = count( $this->data['coupons'] );
                    // } elseif ( $field == 'total_weight_items' ) {
                    //     $total_weight = 0;
                    //     foreach ( $this->data['products'] as $product ) {
                    //         $total_weight += (float) $product['qty'] * (float) $product['weight'];
                    //     }
                    //     $row[$field] = $total_weight;
                    // } elseif ( $field == 'count_total_items' ) {
                    //     $row[$field] = $this->order->get_item_count();
                    // } elseif ( $field == 'count_exported_items' ) {
                    //     $count = 0; // count only exported!
                    //     if ( $this->export['products'] ) {
                    //         foreach ( $this->data['products'] as $product ) {
                    //             $count += $product['qty'];
                    //         }
                    //         $row[$field] = $count;
                    //     }
                    // } elseif ( $field == 'count_unique_products' ) { // speed! replace with own counter ?
                    //     $row[$field] = count( $this->data['products'] );
                    // } elseif ( $field == 'customer_note' ) {
                    //     $notes = array( $this->post->post_excerpt );
                    //     if ( $this->options['export_refund_notes'] ) {
                    //         $refunds = $this->order->get_refunds();
                    //         foreach ( $refunds as $refund ) {
                    //             // added get_reason for WC 3.0
                    //             $notes[] = method_exists( $refund,
                    //                 'get_reason' ) ? $refund->get_reason() : $refund->get_refund_reason();
                    //         }
                    //     }
                    //     $row[$field] = implode( "\n", array_filter( $notes ) );
                    // } elseif ( $field == 'first_refund_date' ) {
                    //     $value = '';
                    //     foreach ( $this->order->get_refunds() as $refund ) {
                    //         $value = ! method_exists( $refund,
                    //             "get_date_created" ) ? $refund->date : ( $refund->get_date_created() ? gmdate( 'Y-m-d H:i:s',
                    //             $refund->get_date_created()->getOffsetTimestamp() ) : '' );
                    //         break;// take only first
                    //     }
                    //     $row[$field] = $value;
                    // } elseif ( isset( $this->static_vals['order'][ $field ] ) ) {
                    //     $row[$field] = $this->static_vals['order'][ $field ];
                    // } elseif ( $field == 'order_notes' ) {
                    //     remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10 );
                    //     $args  = array(
                    //         'post_id' => $this->order_id,
                    //         'approve' => 'approve',
                    //         'type'    => 'order_note',
                    //     );
                    //     $notes = get_comments( $args );
                    //     add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );
                    //     $comments = array();
                    //     if ( $notes ) {
                    //         foreach ( $notes as $note ) {
                    //             if ( ! empty( $this->options['export_all_comments'] ) || $note->comment_author !== __( 'WooCommerce',
                    //                     'woocommerce' ) ) { // skip system notes by default
                    //                 $comments[] = apply_filters( 'woe_get_order_notes', $note->comment_content, $note, $this->order );
                    //             }
                    //         }
                    //     }
                    //     $row[$field] = implode( "\n", $comments );
                    // } elseif ( $field == 'embedded_edit_order_link' ) {
                    //     $row[$field] = sprintf(
                    //         '<a href="%s" target="_blank">%s</a>',
                    //         get_edit_post_link($this->order_id),
                    //         __( 'Edit order', 'woo-order-export-lite' )
                    //     );
                    // } elseif ( isset( $this->order_meta[ $field ] ) ) {
                    //     $field_data = array();
                    //     do_action( 'woocommerce_order_export_add_field_data', $field_data, $this->order_meta[ $field ], $field );
                    //     if ( empty( $field_data ) ) {
                    //         $field_data[ $field ] = $this->order_meta[ $field ];
                    //     }
                    //     $row = array_merge( $row, $field_data );
                    // } elseif ( isset( $this->order_meta[ "_" . $field ] ) ) { // or hidden field
                    //     $row[$field] = $this->order_meta[ "_" . $field ];
                    // } else { // order_date...
                    //         $row[$field] = method_exists( $this->order,
                    //             'get_' . $field ) ? $this->order->{'get_' . $field}() : get_post_meta( $this->order_id, '_' . $field, true );
                    //         //print_r($field."=".$label); echo "debug static!\n\n";
                    // }
                }

                break;
            }
        }
    }
    file_put_contents('/home/randolph/src/bloomlocal/wordpress/tmp/debug.log', "order_{$order_id}: "
    .PHP_EOL.'---------------------------'.PHP_EOL.print_r($row,1).PHP_EOL, FILE_APPEND);
    return $row;
}, 20, 2);
