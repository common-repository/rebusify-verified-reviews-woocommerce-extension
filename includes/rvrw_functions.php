<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */




if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


add_action('admin_menu', 'rvrw_add_pages');

// add menu for other user not for admin
// Function to Create Menu and submenu in Admin:
// action function for above hook

function rvrw_add_pages() {

    $optionpage_top_level = "Trustalyze TCS";
    $iconurl = RVRW_PATH . 'images/rbs-woocommerce-extension-logo.png';

    // userlevel=8 restrict users to "Administrators" only 
    // Add a new submenu under Options:
    // add_options_page('Test Options', 'Test Options', 'administrator', 'testoptions', 'dsp_options_page');
    // Add a new top-level menu (ill-advised): add_menu_page(page_title, menu_title, capability, handle, [function], [icon_url])

    if (current_user_can('manage_options')) {
        add_menu_page($optionpage_top_level, $optionpage_top_level, 'administrator', 'rvrw-admin-sub-page1', 'rvrw_review_page', $iconurl);

        // Add a submenu to the custom top-level menu: add_submenu_page(parent, page_title, menu_title, capability required, file/handle, [function])

        add_submenu_page('rvrw-admin-sub-page1', 'Trustalyze TCS', 'Trustalyze TCS', 'administrator', 'rvrw-admin-sub-page1', 'rvrw_review_page');
        add_submenu_page('rvrw-admin-sub-page1', 'Invitations', 'Invitations', 'administrator', 'rvrw-admin-sub-page2', 'rvrw_invite_page');
        add_submenu_page('rvrw-admin-sub-page1', 'Scamalyze', 'Scamalyze', 'administrator', 'rvrw-admin-sub-page3', 'rvrw_scamalyze_page');
    }
}

function rvrw_review_page() {
    include( RVRW_ABSPATH . '/includes/rvrw_review_configuration.php');
}

function rvrw_invite_page() {
    include( RVRW_ABSPATH . '/includes/rvrw_header.php');
}

function rvrw_scamalyze_page()
{
    include( RVRW_ABSPATH . '/includes/rvrw_scamalyze.php');
}

function rvrw_is_site_admin() {
    return in_array('administrator', wp_get_current_user()->roles);
}

function rvrw_load_jquery() {
    if (!wp_script_is('jquery', 'enqueued')) {

        //Enqueue
        wp_enqueue_script('jquery');
    }
}

add_action('wp_enqueue_scripts', 'rvrw_load_jquery');

/* this function will make sure load index.js  after jquery loaded */

function rvrw_enqueue_script() {

    wp_deregister_script('rvrw_js');
    wp_register_script(
            'rvrw_js', plugins_url('rebusify-verified-reviews-woocommerce-extension/js/index.js'), array('jquery')
    );



    // to include colorbox script in plugin.
    wp_enqueue_script('rvrw_js');

    if (rvrw_check_exist_option('rbs_icons')) {
        $enable_icons = get_option('rbs_icons');
        if ($enable_icons == 1) {
            wp_enqueue_style('rvrw_icon_style', plugins_url('rebusify-verified-reviews-woocommerce-extension/assets/css/rqx_icon_style.css'));
        }
    }
}

add_action('wp_enqueue_scripts', 'rvrw_enqueue_script');

function rvrw_check_exist_option($arg) {

    global $wpdb;
    $prefix = $wpdb->prefix;
    $db_options = $prefix . 'options';
    $sql_query = 'SELECT * FROM ' . $db_options . ' WHERE option_name LIKE "' . $arg . '"';

    $results = $wpdb->get_results($sql_query, OBJECT);

    if (count($results) === 0) {
        return false;
    } else {
        return true;
    }
}

function rvrx_send_mail($email, $user_name = "User") {
    global $wpdb;
    $RVRW_EMAIL_TEMPLATE = $wpdb->prefix . "rvrw_email_template";

    $emailTemplate = $wpdb->get_row("SELECT * FROM $RVRW_EMAIL_TEMPLATE WHERE ID=1");
    if (!is_null($emailTemplate)) {
        $subject = $emailTemplate->subject;
        $content = $emailTemplate->body;

        $content = str_replace("NAME", $user_name, $content);

        $admin_email = get_option('admin_email');
        $headers = "From :" . $admin_email . "\r\n";
        $headers .= "Reply-To: " . $admin_email . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

       // echo $content;
        wp_mail($email, $subject, $content, $headers);
       
    }
}

function sendReminder($orderId) {
    global $wpdb;
    $RVRW_USER_REVIEW_REMINDER = $wpdb->prefix . "rvrw_user_review_reminder";

    $order_detail = wc_get_order($orderId);
    if ($order_detail) {

        $billing_email = $order_detail->get_billing_email();
        $payment_status = $order_detail->get_status();
        $billing_username = $order_detail->get_billing_first_name();
        if ($payment_status == "completed") {

            // check if this order exist in table
            $orderExist = $wpdb->get_var("select count(*) from $RVRW_USER_REVIEW_REMINDER "
                    . "where order_id=$orderId");
            if ($orderExist == 0) {
                $data = array('order_id' => $orderId, 'mail_sent' => 'YES');

                $data_formate = array('%d', '%s');

                $wpdb->insert($RVRW_USER_REVIEW_REMINDER, $data, $data_formate);
            } else {

                $data = array('mail_sent' => 'YES');
                $where = array('order_id' => $orderId);
                $data_formate = array('%s');
                $where_formate = array('%d');

                $wpdb->update($RVRW_USER_REVIEW_REMINDER, $data, $where, $data_formate, $where_formate);
            }
            if ($billing_email) {
                rvrx_send_mail($billing_email, $billing_username);
                return "Invitation sent!";
            }
        } else {
            return "This order's payment status is not completed!";
        }
    } else {
        return "This order does not exist!";
    }
}

function resetReminder($orderId)
{
    global $wpdb;
    $RVRW_USER_REVIEW_REMINDER = $wpdb->prefix . "rvrw_user_review_reminder";
  
    $data = array('mail_sent' => 'NO');
    $where = array('order_id' => $orderId);
    $data_formate = array('%s');
    $where_formate = array('%d');

    $wpdb->update($RVRW_USER_REVIEW_REMINDER, $data, $where, $data_formate, $where_formate);
}

function rvrw_plugin_activate() {

   $adminemail=get_option('admin_email');
   $site_url=get_site_url();
   $mailbody='Trustalyze Verified Reviews Woocommerce Extension plugin has activated on '.$site_url;
   wp_mail('activation@trustalyze.com','Trustalyze Verified Reviews Woocommerce Extension Plugin Activated',$mailbody,'From:'.$adminemail);
   
                           

  /* activation code here */
}

function rvrw_check_https() {
	
	if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
		
		return true; 
	}
	return false;
}