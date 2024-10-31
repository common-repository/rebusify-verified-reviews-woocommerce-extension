<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function rvrw_create_tables() {
//new in version 1.1. sets the database value to the wordpress database prefix
    global $wpdb;


    //1. Create Table 
    $RVRW_REVIEW_REPLIES = $wpdb->prefix . "rvrw_review_replies";
    $RVRW_TRANSACTION_REVIEW = $wpdb->prefix . 'rvrw_transaction_reviews';
    $RVRW_EMAIL_TEMPLATE= $wpdb->prefix."rvrw_email_template";
    $RVRW_USER_REVIEW_REMINDER=$wpdb->prefix."rvrw_user_review_reminder";
    $RVRW_BLOCKED_USER=$wpdb->prefix."rvrw_blocked_user";

    // check condition if table exists

    if ($wpdb->get_var("show tables like '$RVRW_REVIEW_REPLIES'") != $RVRW_REVIEW_REPLIES) {
        $wpdb->query("CREATE TABLE IF NOT EXISTS $RVRW_REVIEW_REPLIES (id bigint(20)  NOT NULL AUTO_INCREMENT,
  	reply text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,date date NOT NULL,review_transaction_id int(11) NOT NULL
        ,thread_id int(11) NOT NULL,type ENUM('M','C'),review_id bigint(20) NOT NULL,PRIMARY KEY (id) ) AUTO_INCREMENT=1 ");
    }
    
     if ($wpdb->get_var("show tables like '$RVRW_TRANSACTION_REVIEW'") != $RVRW_TRANSACTION_REVIEW) {
    
     $sql = "CREATE TABLE $RVRW_TRANSACTION_REVIEW (

			      id int(7) NOT NULL AUTO_INCREMENT,

			      user_id int(10) NOT NULL,

			      order_id int(10) NOT NULL,

			      rate int(2) NOT NULL,

			      rate_date datetime  NOT NULL,

			      description text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,

			      transaction_date timestamp NOT NULL,

			      transaction_id varchar(100) NOT NULL,

			      status ENUM('0', '1')  DEFAULT '0' ,
                              title varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
                              first_name varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,
			      PRIMARY KEY  (id)

			    );";
      $wpdb->query($sql);
     }
     
      if ($wpdb->get_var("show tables like '$RVRW_EMAIL_TEMPLATE'") != $RVRW_EMAIL_TEMPLATE) {
        $wpdb->query("CREATE TABLE IF NOT EXISTS $RVRW_EMAIL_TEMPLATE (id tinyint(20)  NOT NULL AUTO_INCREMENT,
  	body text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,subject text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL
        ,PRIMARY KEY (id) ) AUTO_INCREMENT=1 ");
        $site_url=get_site_url();
        $body="<div> Hi NAME,</div><div>Thank you for your purchase. Would you mind posting a review of your experience?</div>"
            ."<div> Simply click the link below to take you to your my account page and there you will find the transaction id for leaving a review. </div>"
            ."<div>".$site_url."/my-account/reviews/</div><div> Thank you</div>";
         $wpdb->query("INSERT INTO $RVRW_EMAIL_TEMPLATE(body,subject) VALUES('$body','Please review your purchase')");
    }
  
    if ($wpdb->get_var("show tables like '$RVRW_USER_REVIEW_REMINDER'") != $RVRW_USER_REVIEW_REMINDER) {
        $wpdb->query("CREATE TABLE IF NOT EXISTS $RVRW_USER_REVIEW_REMINDER (id tinyint(20)  NOT NULL AUTO_INCREMENT,
  	order_id bigint(20),mail_sent ENUM('NO','YES') ,PRIMARY KEY (id) ) AUTO_INCREMENT=1 ");
        }
        
         if ($wpdb->get_var("show tables like '$RVRW_BLOCKED_USER'") != $RVRW_BLOCKED_USER) {
        $wpdb->query("CREATE TABLE IF NOT EXISTS $RVRW_BLOCKED_USER (id bigint(20)  NOT NULL AUTO_INCREMENT,
  	user_name varchar(50),ip_address INT UNSIGNED NOT NULL,email varchar(100)
        ,date date NOT NULL,block ENUM('YES','NO') DEFAULT 'YES' NOT NULL,PRIMARY KEY (id) ) AUTO_INCREMENT=1 ");
        }
}


