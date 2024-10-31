<?php
/**
 * Plugin Name: Trustalyze Verified Reviews Woocommerce Extension
 * Plugin URI: https://www.trustalyze.com/for-merchants/
 * Description:  WooCommerce Extension for Trustalyze and the Trustalyze Confidence System.
 * Author: Trustalyze
 * Author URI: https://www.trustalyze.com
 * Version: 1.4.3
 * Text Domain: trustalyze
 * Domain Path: /i18n/languages
 * Copyright (c) 2017 - 2021, Trustalyze, LLC - All rights reserved.
 * @package Trustalyze
 */
 
defined('ABSPATH') or exit;

define('RVRW_PATH', plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)) . '/');

if (!defined('RVRW_ABSPATH'))
    define('RVRW_ABSPATH', plugin_dir_path(__FILE__));

// Make sure WooCommerce is active

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

   
    $error = 'ERROR! You must have Woocommerce installed to use the Rebusify
            Confidence System Extension for Woocommerce';
    wp_die(sprintf($error));
}



/**

 * Adds plugin page links

 * 

 * @since 1.0.0

 * @param array $links all plugin links

 * @return array $links all plugin links + our custom links (i.e., "Settings")

 */
function rvrw_gateway_plugin_links($links) {

    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=rvrw-admin-sub-page1') . '">' . __('Configure', 'wc-rqx-offline') . '</a>'
    );

    return array_merge($plugin_links, $links);
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'rvrw_gateway_plugin_links');


require('rvrw_actions.php');



require_once 'includes/rvrw_functions.php';
require_once 'includes/rvrw_general.php';
rvrw_create_tables();


function rvrw_fetch_merchant_reply() {
  if ( !isset($_REQUEST['rvrw_mch_rply']) ) return;

  //echo 'hi cod eis code';
  include_once RVRW_ABSPATH.'/includes/rvrw_fetch_merchant_reply.php';
  exit;
}
add_action( 'init', 'rvrw_fetch_merchant_reply' );

function rvrw_exist_option($arg) {

    global $wpdb;
    $prefix = $wpdb->prefix;
    $db_options = $prefix . 'options';
    $sql_query = 'SELECT * FROM ' . $db_options . ' WHERE option_name = "' . $arg . '"';

    $results = $wpdb->get_results($sql_query, OBJECT);

    if (count($results) === 0) {
        return false;
    } else {
        return true;
    }
}


register_activation_hook( __FILE__, 'rvrw_plugin_activate' );
