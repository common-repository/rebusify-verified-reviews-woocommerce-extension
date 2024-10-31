<?php


 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//for xss validation
include_once RVRW_ABSPATH.'/includes/rvrw_validation_functions.php';

$rating_id = isset($_REQUEST['rating_id']) ?  strip_tags(trim($_REQUEST['rating_id'])) : "";
$description = isset($_REQUEST['description']) ?  strip_tags(trim($_REQUEST['description'])) : "";
$rate = isset($_REQUEST['rating']) ?  strip_tags(trim($_REQUEST['rating'])) : "";
$rate_date = date("Y-m-d H:i:s");
$siteurl = get_site_url();
$title= isset($_REQUEST['title']) ? strip_tags(trim($_REQUEST['title'])) : "";
$first_name= isset($_REQUEST['txt_name']) ? strip_tags(trim($_REQUEST['txt_name'])) : "";
$video_id=isset($_REQUEST['video_id']) ? $_REQUEST['video_id'] : 0; 

if($rating_id!="" && $description!="" && $rate!="" && $title!="" && $first_name!=""){


$description=  rvrw_sanitize_data($description,'xss_clean');
$title=  rvrw_sanitize_data($title,'xss_clean');
$first_name=  rvrw_sanitize_data($first_name,'xss_clean');
$rating_id=  rvrw_sanitize_data($rating_id,'int');
$rating_id=  rvrw_sanitize_data($rating_id,'xss_clean');
$rate=  rvrw_sanitize_data($rate,'int');
$rate=  rvrw_sanitize_data($rate,'xss_clean');

$first_name=sanitize_text_field($first_name);
$title=sanitize_text_field($title);
$description=sanitize_text_field($description);

$description= stripslashes_deep($description);
$title=stripslashes_deep($title);
$first_name=stripslashes_deep($first_name);




$trans_id = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM $table_name "
        . " WHERE id = %d && status = %s" , array($rating_id , '0' )  ));

$gettransid = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $rating_id ", ARRAY_A);

 
$shortreview= substr($description, 0,2999); // 3000 char limit for review

 
$title=substr($title, 0,59); // 60 limit for title 
$first_name=substr($first_name, 0,24); // 25 limit for name 
$msg1="";
$result="";

if ( $trans_id > 0 ){
    // check if data is not already saved
    
    $updateExist= $wpdb->get_var("select count(*) from $table_name WHERE id=$rating_id and status='0'");
    if($updateExist)
    {
 
   
   $data= array('rate'=>$rate, 'description' => $shortreview, 'status' =>1, 
		'rate_date' => $rate_date ,'title'=>$title,'first_name'=>$first_name);
   $where =array('id'=>$rating_id);
   $data_formate=array('%d','%s','%s','%s','%s','%s');
   $where_formate=array('%d');
   
   $wpdb->update($table_name,$data,$where,$data_formate,$where_formate);
   
    $transaction_id=  $gettransid['transaction_id'];
    
//fetch only 350 characters
 
 if (get_option('rbs_enabel_review'))
 {
     $review=get_option('rbs_enabel_review');
     if($review==1) // send revie to trustalyze.com
     {
         
         $body = array( "domain"=>$siteurl,
             "rate"=>$rate,
             "reviews"=>$shortreview,
             "transaction"=>$transaction_id,
             "title"=>$title,
             "firstname"=>$first_name,
             "review_transaction_id"=>$rating_id,
             "video_id"=>$video_id);
         
         $args = array(
            'method' => 'POST',
            'timeout' => '480',
            'redirection' => '5',
            'headers'  => array(
                'Content-type: application/x-www-form-urlencoded'
            ),
           'body' => $body
        );
         
         $response = wp_remote_post( 'https://reviews.trustalyze.com/api/rbs/index.php', $args );
        
       
       
         
         if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            //echo "Something went wrong: $error_message";
        } else {
             $http_code = wp_remote_retrieve_response_code( $response );
             if($http_code===200)
            {
                 $responsedata = wp_remote_retrieve_body( $response );
                 
                    $data = json_decode($responsedata, true);
                    //print_r($data);
                    if(sizeof($data)>0) {
                    foreach ($data as $key => $value) {
                    $message = $value['msg'];
                    $result= $value['result'];
                    
                    }
                }
                if($result=='success'){
                $msg1= '- Your review also updated at Trustalyze.com';
                }
            }
        }
         
      
     }

 }
//echo $http_code;
   $msg= "Thanks For Rating ".$msg1;

    }
}
else
{
    $msg= "This transaction does not found.";
}
}
 else {
    $msg= "Please fill all the fields.";
}


?>
