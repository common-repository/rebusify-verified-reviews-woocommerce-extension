<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

  // here we will increase max_execution_time out limit for php
  $timeout = 45;
if ( ! ini_get( 'safe_mode' ) ){
    set_time_limit( $timeout + 10 );
}
//echo ini_get( 'max_execution_time' );;


$fname = isset($_REQUEST['txt_fname']) ? rvrw_sanitize_data($_REQUEST['txt_fname'], 'xss_clean') : "";
$lname = isset($_REQUEST['txt_lname']) ? rvrw_sanitize_data($_REQUEST['txt_lname'], 'xss_clean') : "";
$company_name = isset($_REQUEST['txt_company_name']) ? rvrw_sanitize_data($_REQUEST['txt_company_name'], 'xss_clean') : "";

$country = isset($_REQUEST['rvrw_country']) ? rvrw_sanitize_data($_REQUEST['rvrw_country'], 'xss_clean') : "";
$city = isset($_REQUEST['txt_city']) ? rvrw_sanitize_data($_REQUEST['txt_city'], 'xss_clean') : "";
$state = isset($_REQUEST['rvrw_state']) ? rvrw_sanitize_data($_REQUEST['rvrw_state'], 'xss_clean') : "";
$zip = isset($_REQUEST['txt_zip']) ? rvrw_sanitize_data($_REQUEST['txt_zip'], 'xss_clean') : "";
$phone = isset($_REQUEST['txt_phone']) ? rvrw_sanitize_data($_REQUEST['txt_phone'], 'xss_clean') : "";
$address1 = isset($_REQUEST['txt_address1']) ? rvrw_sanitize_data($_REQUEST['txt_address1'], 'xss_clean') : "";
$address2 = isset($_REQUEST['txt_address2']) ? rvrw_sanitize_data($_REQUEST['txt_address2'], 'xss_clean') : "";
$user_name = isset($_REQUEST['txt_account_username']) ? rvrw_sanitize_data($_REQUEST['txt_account_username'], 'xss_clean') : "";
$about_company = isset($_REQUEST['txt_about_company']) ? rvrw_sanitize_data($_REQUEST['txt_about_company'], 'xss_clean') : "";
$keyword = isset($_REQUEST['txt_keyword']) ? rvrw_sanitize_data($_REQUEST['txt_keyword'], 'xss_clean') : "";
$email = isset($_REQUEST['txt_email']) ? rvrw_sanitize_data($_REQUEST['txt_email'], 'xss_clean') : "";
$password = isset($_REQUEST['txt_password']) ? rvrw_sanitize_data($_REQUEST['txt_password'], 'xss_clean') : "";
$web_url=$siteurl;
//print_r($_REQUEST);

if ($fname != "" && $lname != "" && $company_name != "" && $web_url != "" && 
        $country != "" && $city != "" && $zip != "" && $phone != "" 
        && $address1 != "" && $user_name != "" && $about_company != "" && $keyword != "" && $email != "" && $password != "") {
    
      
    // send create an order request to trustalyze.com
    
    $about_company= substr($about_company, 0,249);
    
    $body = array( "fname"=>$fname,"lname"=>$lname,"company_name"=>$company_name,
         "web_url"=>$web_url,"country"=>$country,
        "city"=>$city,"zip"=>$zip,
        "phone"=>$phone,"address1"=>$address1,"user_name"=>$user_name,
        "about_company"=>$about_company,"keyword"=>$keyword,"email"=>$email,
        "password"=>$password,"state"=>$state,"address2"=>$address2 );
         
         $args = array(
            'method' => 'POST',
            'timeout' => $timeout,
            'redirection' => '5',
            'headers'  => array(
                'Content-type: application/x-www-form-urlencoded'
            ),
           'body' => $body
        );
         
        $response = wp_remote_post( 'https://reviews.trustalyze.com/api/rbs/create_order.php', $args );
        $response_message = wp_remote_retrieve_response_message( $response );
        $http_code = wp_remote_retrieve_response_code( $response );
       
        
        if ( is_wp_error( $response ) ) {
              $error_message = $response->get_error_message();
              $message = "Sorry Trustalyze site is not accessible.";
              //echo $error_message;
        }
        else {
        if ( 200 != $http_code && ! empty( $response_message ) ) {
	$error_message=	WP_Error( $http_code, $response_message );
            //return new WP_Error( $http_code, $response_message );
                 $message = "Sorry Trustalyze site is not accessible..";
	} elseif ( 200 != $http_code ) {
           // return new WP_Error( $http_code, 'Unknown error occurred' );
            $error_message =WP_Error( $http_code, 'Unknown error occurred' );
             $message = "Sorry Trustalyze site is not accessible...";
	} else {  
		 $responsedata = wp_remote_retrieve_body( $response );
                 
                    $data = json_decode($responsedata, true);
                   
                    //print_r($data);
                    foreach ($data as $key => $value) {
                    $message = $value['msg'];
                    $success = $value['success'];
                    $apiKey = $value['apikey'];
                    
                }
                if($success=="yes") {
                    
                    $message=$message.'<br> Here is your Trustalyze Serial Key:&nbsp; '.$apiKey;
                    $message=$message."<br>Please click the \"Save\" button at the bottom to save your settings. ";
                    
                    
                    $rbs_serial_key=$apiKey;
                    $rbs_email=$email;
                    $enableReview=1;
                    
                    if (rvrw_exist_option('rbs_new_serial_key')) {
                        update_option('rbs_new_serial_key', $apiKey);
                    } else {
                        add_option('rbs_new_serial_key', $apiKey);
                    }
                    $check_enable_review=1;
                    
                     
                    
                    }
                   
                
                
        }
        }
       
         
    
} else { 
    $message ="All Fields are mandtory while register a free account!";
}