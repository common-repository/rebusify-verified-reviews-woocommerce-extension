<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 error_reporting(0);
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');*/
  
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global  $wpdb;

$RVRW_REVIEW_REPLIES= $wpdb->prefix ."rvrw_review_replies";

//for xss validation
include_once RVRW_ABSPATH.'/includes/rvrw_validation_functions.php';

$reply = rvrw_sanitize_data(trim($_REQUEST['reply']), 'nohtml');
$reply=rvrw_sanitize_data($reply,'xss_clean'); 
$date=date('Y-m-d');
$review_transaction_id=rvrw_sanitize_data($_REQUEST['review_transaction_id'],'int'); 
$type= rvrw_sanitize_data($_REQUEST['type'],'xss_clean'); 
$review_id= rvrw_sanitize_data($_REQUEST['review_id'],'int'); 
$review_id=  rvrw_sanitize_data($review_id,'xss_clean');
$review_transaction_id=  rvrw_sanitize_data($review_transaction_id,'xss_clean');

$reply=sanitize_text_field($reply);


$thread_id=0;
  // fetch thread id 
$getthread=$wpdb->get_row("select id from $RVRW_REVIEW_REPLIES where "
        . " review_transaction_id=$review_transaction_id order by id DESC limit 1");
            if(!is_null($getthread))
            {
                $thread_id=$getthread->id;
            }


  $data = array('reply' => $reply, 'date' => $date,'review_transaction_id'=>$review_transaction_id,'thread_id'=>$thread_id,
           'type'=>$type,'review_id'=>$review_id );
        $format = array('%s','%s','%d','%d','%s','%d');
        $wpdb->insert($RVRW_REVIEW_REPLIES,$data,$format);
//echo $insertQuery;
