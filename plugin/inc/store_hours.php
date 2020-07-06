<?php
/**
* Plugin Name: Date Picker Plugin
* Description: This is Date picker plugin It create based on London Time zone.
* Version: 1.3
* Author: Helal Uddin Ujjal
**/

// Modified for bloomlocal store hours
// randolph

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter('woocommerce_get_sections_products' , function ($settings_tab) {
    $settings_tab['bl_store_hours'] = __('Store Hours');
    return $settings_tab;
}, 20, 1);


add_filter( 'woocommerce_get_settings_products' , function ($settings, $current_section) {
    if($current_section != 'bl_store_hours') {
        return $settings;
    }

    $custom_settings =  array(
        array(
            'name' => __('Store Hours Settings'),
            'type' => 'title',
            'desc' => __( 'Manage store open/close times and same-day delivery cutoff. UK Time' ),
            'id'   => 'date_picker' 
        ),
        array(
            'name' => __('Use Settings'),
            'type' => 'checkbox',
            'desc' => __('Activate these settings in product/checkout page'),
            'id'   => 'st_enable' 
        ),
        array(
            'name' => __( 'Holiday', 'woocommerce'),
            'type' => 'textarea',
            'desc_tip' => true,
            'default' => __( '[12, 25, 2019], [12, 26, 2019], [01, 01, 2020]'),
            'desc' => __( 'you must be folow this proccess to input holliday like                       [mm,dd,yyyy],[mm,dd,yyyy],[mm,dd,yyyy]'),
            'id'    => 'dp_holliday',
            'class' =>'form-control',
        ),
    );

    $options = array(
        '-1' => __('Close', 'woocommerce'),
        '' => __('Open', 'woocommerce'),
    );
    $start = 10;
    for ($i = 0; $i < 24; $i++) {
        $h = $start + $i;
        if ($h > 24) {
            $h = $h - 24;
        }
        $options[$h] = sprintf('Open - Same day delivery Cut-off %s', bloomlocal_to_human_hours($h));
    }
    $days = array(
        'st_sunday' => '-1',
        'st_monday' => 13,
        'st_tuesday' => 13,
        'st_wednesday' => 13,
        'st_thursday' => 13,
        'st_friday' => 13,
        'st_saturday' => 13,
    );
    foreach ($days as $id => $default) {
        $day = ucfirst(str_replace('st_', '', $id));
        $custom_settings[] = array(
            'name' => $day,
            'type' => 'select',
            'id'    => $id,
            'class' =>'form-control',
            'options' => $options,
            'value' => get_option($id, $default),
        );
    }
    $custom_settings[] = array(
        'type' => 'sectionend',
        'id' => 'date_picker'
    );
    return $custom_settings;
}, 20, 2);
  
add_action('woocommerce_before_add_to_cart_button', function () {
    if (!get_option('st_enable', false)) {
        return;
    }

    $settings = new stdClass();
    foreach (array('st_sunday', 'st_monday', 'st_tuesday', 'st_wednesday', 'st_thursday', 'st_friday', 'st_saturday') as $i => $name) {
        $settings->$i = get_option($name);
    }
    $settings = json_encode($settings);
    $holiday = get_option('dp_holliday');

// YOUR SCRIPT HERE BELOW 
echo "
<script type='text/javascript'>
var store_hours = $settings;

jQuery(document).ready(function ($) {
    var origdp = $('#datepicker'),
        dp = $(origdp[0].outerHTML).removeClass('hasDatepicker');
    
    $(dp).attr('readonly', true);
	$(dp).attr('onfocus', \"this.value='';\");
    
    var holiday = [$holiday]; 
    
    function checkOutDisableDays(date) {
        var day = date.getDay(), Sunday = 0, Monday = 1, Tuesday = 2, Wednesday = 3, Thursday = 4, Friday = 5, Saturday = 6;
        
        if (store_hours[day] == -1) {
            return [false];
        }
        
        for (i = 0; i < holiday.length; i++) {
            if (date.getMonth() == holiday[i][0] - 1 &&
            date.getDate() == holiday[i][1] &&
            date.getFullYear() == holiday[i][2]) {
                return [false];
            }
        }
        
        return [true];
    }

	var uk = new Date().toLocaleString('en-US', { timeZone: 'Europe/London' });
	var d = new Date(uk);
	var ukDay = d.getDay();
    var cutoff = store_hours[ukDay];
    var ukHours = d.getHours().toLocaleString();

    console.log(ukHours, cutoff);

    $(dp).datepicker({
        beforeShowDay: checkOutDisableDays,
        dateFormat: 'dd/mm/yy',
        minDate: (function () {
            if (cutoff == '' || cutoff == -1 || ukHours < cutoff) {
                return 0;
            }

            return 1;
        }())
    });

    origdp.after(dp);
    origdp.remove();
});
</script>";
});

// add_action('wp_enqueue_scripts', function () {
//     // enqueue all our scripts
//     wp_register_script( 'jquery-ui-datepicker', 'https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.js', array( 'jquery' ), null, true );
//     wp_enqueue_script( 'jquery-ui-datepicker' );
//     wp_enqueue_style( 'ui-jquery', plugins_url( '/assets/ui-jquery.css', __FILE__ ) );
//     wp_enqueue_style( 'style', plugins_url( '/assets/style.css', __FILE__ ) );
// });

// $plugin = plugin_basename(__FILE__);
// add_filter("plugin_action_links_$plugin", function ($links) {
//     $settings_link = '<a href="admin.php?page=wc-settings&tab=products&section=wp_datepicker_notices">Settings</a>';
//     array_push( $links, $settings_link );
//     return $links;
// });

function bloomlocal_to_human_hours($n) {
    $h = $n > 12 ? $n - 12 : $n;
    $p = $n == 24 || $n < 12 ? 'AM' : 'PM';
    return str_pad($h, 2, '0', STR_PAD_LEFT).':00 '.$p;
}
