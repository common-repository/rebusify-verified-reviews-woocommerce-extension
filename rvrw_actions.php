<?php

error_reporting(0);
error_reporting(E_ALL);
ini_set('display_errors', 'On');


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//woocommerce_order_status_changed
add_action('woocommerce_order_status_changed', 'rvrw_set_txn');
add_action('woocommerce_payment_complete', 'rvrw_custom_tracking');
add_action('wp_enqueue_scripts', 'rvrw_woo_ext_styles_fontawsome');
add_action('woocommerce_before_checkout_form', 'rvrw_check_ip',10,1);
//woocommerce_before_checkout_form


function rvrw_reviews_account_menu_items($items) {

    $logout = $items['customer-logout'];
    unset($items['customer-logout']);
    $items['reviews'] = __('Review Transactions', 'rqxreviews');
    $items['reviewreply'] = __('Review Replies', 'rqxreviewreply');
    $items['customer-logout'] = $logout;
    return $items;
}

add_filter('woocommerce_account_menu_items', 'rvrw_reviews_account_menu_items', 13, 1);

function rvrw_reviews_add_my_account_endpoint() {

    add_rewrite_endpoint('reviews', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('reviewreply', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
}

add_action('init', 'rvrw_reviews_add_my_account_endpoint');

function rvrw_custom_flush_rewrite_rules() {

    add_rewrite_endpoint('reviews', EP_ROOT | EP_PAGES);
    add_rewrite_endpoint('reviewreply', EP_ROOT | EP_PAGES);
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'rvrw_custom_flush_rewrite_rules');
register_deactivation_hook(__FILE__, 'rvrw_custom_flush_rewrite_rules');

function rvrw_reviews_endpoint_content() {
    require( 'includes/rvrw_reviews.php');
}

add_action('woocommerce_account_reviews_endpoint', 'rvrw_reviews_endpoint_content');

function rvrw_reviews_reviewreply_endpoint_content() {
    require( 'includes/rvrw_review-reply.php');
}

add_action('woocommerce_account_reviewreply_endpoint', 'rvrw_reviews_reviewreply_endpoint_content');

function rvrw_custom_tracking($order_id) {

    global $wpdb, $woocommerce, $product;
    // Lets grab the order
    $order = wc_get_order($order_id);
    $orderid = $order->get_id();
    $order_status  = $order->get_status(); 

    $order->get_items();
    $user_id   = $order->get_user_id(); 

    $trasanction_id = get_post_meta($orderid, '_transaction_id', true);
    
    
    
  
    $table_name = $wpdb->prefix . 'rvrw_transaction_reviews';

    $trans_id = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $table_name WHERE order_id = %s ", $orderid));

    if ($trans_id > 0) {

        $data= array('user_id'=>$user_id, 'transaction_id' => $trasanction_id);
        $where =array('order_id'=>$orderid);
        $data_formate=array('%d','%s');
        $where_formate=array('%d');

        $wpdb->update($table_name,$data,$where,$data_formate,$where_formate);
   
   
    } else {

         $data = array('user_id' => $user_id, 'order_id' => $orderid,
             'transaction_id'=>$trasanction_id );
        $format = array('%d','%d','%s');
        $wpdb->insert($table_name,$data,$format);
    }
}

/* this function will call on order staus change this will help if somehow order
 *  is on hold and doens't complete by payment 
 * gateway and admin change order status by admin section { in that case woocommerce_payment_complete 
 * will not call }*/
function rvrw_set_txn($order_id)
{
     global $wpdb, $woocommerce, $product;
    // Lets grab the order
    $order = wc_get_order($order_id);
    $orderid = $order->get_id();
    $order_status  = $order->get_status(); 

    $order->get_items();
    $user_id   = $order->get_user_id(); 

    $trasanction_id = get_post_meta($orderid, '_transaction_id', true);
    
       
  
    $table_name = $wpdb->prefix . 'rvrw_transaction_reviews';

    $trans_id = $wpdb->get_var($wpdb->prepare("SELECT count(*) FROM $table_name WHERE order_id = %s ", $orderid));

    if ($trans_id > 0) {

        $data= array('user_id'=>$user_id, 'transaction_id' => $trasanction_id);
        $where =array('order_id'=>$orderid);
        $data_formate=array('%d','%s');
        $where_formate=array('%d');

        $wpdb->update($table_name,$data,$where,$data_formate,$where_formate);
   
   
    } else {

         $data = array('user_id' => $user_id, 'order_id' => $orderid,
             'transaction_id'=>$trasanction_id );
        $format = array('%d','%d','%s');
        $wpdb->insert($table_name,$data,$format);
    }
}
    

function rvrw_woo_ext_styles_fontawsome() {

    wp_register_style('fontawsome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), null, false);
    wp_enqueue_style('fontawsome');


    wp_register_style('rvrw_woo_commerce_extension', plugins_url('/assets/css/rqx_woo_commerce_extension.css', __FILE__), array(), '1', 'all');
    wp_enqueue_style('rvrw_woo_commerce_extension');

   
}

function rvrw_plugin_admin_head_js()
{
      wp_enqueue_script( 'my_admin_script', plugins_url('/js/admin.js', __FILE__) );
}

function rvrw_plugin_admin_head_css() {

    wp_enqueue_style('rvrw_admin_style', plugins_url('/css/adminindex.css', __FILE__));
    
  }


  function rvrw_check_ip()
  {
       global $wpdb;
       $RVRW_BLOCKED_USER=$wpdb->prefix."rvrw_blocked_user";
       $rbs_enable_scamaylyze=0;
       $rbs_block_checkout=0;
       $ip_address_exist=0;
       $adminemail=get_option('admin_email');
       $site_url=get_site_url();
       
       
       if (rvrw_exist_option('rbs_enable_scamaylyze')) { 
           $rbs_enable_scamaylyze=get_option('rbs_enable_scamaylyze');
       }
       if (rvrw_exist_option('rbs_block_checkout')) { 
           $rbs_block_checkout=get_option('rbs_block_checkout');
       }
       // if admin want to check ip by scamalyze
       if($rbs_enable_scamaylyze) {
       
       $ip=rvrw_get_user_ip();
       
       /*if(isset($_REQUEST['ip'])){
       $ip=trim($_REQUEST['ip']);
       }*/
       
       // check if this IP exist in our table
       $ipDetails=$wpdb->get_row("SELECT block FROM "
               . "$RVRW_BLOCKED_USER WHERE ip_address=INET_ATON('$ip')");
    
       $blockIP=false; 
       //echo '<h2>Hello there! Happy shopping!'.$ip.'</h2>';
       
        if(!is_null($ipDetails))
       {  
           $ip_address_exist=1;
           if($ipDetails->block=="YES")
           {
                $blockIP=true; //echo 'block194*'.$rbs_block_checkout;
           }
       }
       else {  
         //echo ' null';
         /// check ip from scamalyze
            
            $response_result=rvrw_scamalyze_check($ip);
           // print_r($response_result);
          
            
            if($response_result['risk']=="very high"){
               $blockIP=true; 
            }
            else if( $response_result['score']>=80)
            {
                 $blockIP=true; 
            }
       }
       
       if($blockIP )
       {
           // INSERT IP ADDRESS IF NOT ALREADY EXIST IN OUR DB
        if(!$ip_address_exist) {
         
            $date=date('Y-m-d');
            $user_name="";
            $email="";
            $insertQuery="INSERT INTO $RVRW_BLOCKED_USER(id,user_name,ip_address,email,date) "
               . "VALUES(NULL,'$user_name',INET_ATON('$ip'),'$email','$date')";
            $wpdb->query($insertQuery);
            //echo $insertQuery;
         }
        
        // if admin wants to block checkout
        if($rbs_block_checkout) {
           // echo 'yes blopck me ';
        ?>
        <script type="text/javascript">
           
           alert("Your account has been limited, please contact us at <?php echo $adminemail;?> for more information,\n Thanks,\n Admin");
           window.location.href="<?php echo $site_url; ?>";
        </script>
        <?php
        }
       }
       }   
  }
  
  function rvrw_scamalyze_check($ip)
  {
        $timeout = 45;
        if ( ! ini_get( 'safe_mode' ) ){
            set_time_limit( $timeout + 10 );
        }

       

        $key=  "2d028d80cbb5aaee111d285ac0c37119";
        $url="https://api01.scamalytics.com/trustalyze/";
        $testmode=1;
        $message="";
        $status="";
        $score=0;
        $risk="low";
        $response_array=array();
        $body = array( "ip"=>$ip,
            "key"=>$key,
            "test"=>$testmode);
         
        $args = array(
            'method' => 'POST',
            'timeout' => $timeout,
            'redirection' => '5',
            'headers'  => array(
                'Content-type: application/x-www-form-urlencoded'
            ),
           'body' => $body
        );
         
        $response = wp_remote_post( $url, $args );
        $response_message = wp_remote_retrieve_response_message( $response );
        $http_code = wp_remote_retrieve_response_code( $response );
       
        
        if ( is_wp_error( $response ) ) {
              $error_message = $response->get_error_message();
              $message = $error_message." Sorry scamalytics site is not accessible.";
              $response_array['risk']=$risk;
              $response_array['score']=$score;
              $response_array['message']=$message;
        }
        else {
        if ( 200 != $http_code && ! empty( $response_message ) ) {
		//return new WP_Error( $http_code, $response_message );
                 $message = $response_message."Sorry scamalytics site is not accessible11.";
                 $response_array['risk']=$risk;
              $response_array['score']=$score;
              $response_array['message']=$message;
	} elseif ( 200 != $http_code ) {
		//return new WP_Error( $http_code, 'Unknown error occurred' );
             $message = "Sorry scamalytics site is not accessible2.";
             $response_array['risk']=$risk;
              $response_array['score']=$score;
              $response_array['message']=$message;
	} else {  
		 $responsedata = wp_remote_retrieve_body( $response );
                 
                    $data = json_decode($responsedata, true);
                    // print_r($data);
                    foreach ($data as $key => $value) {
                       if($key=="status") {
                            $status=$value;
                        }
                        if( $status=="ok") {
                            $message='success';
                        if($key=="score")
                        {
                            $score = $value;
                            
                        }
                        if($key=="risk")
                        {
                             $risk = $value;
                        }
                        }
                        else{
                            $message="status fail";
                        }
                                        
                }
                $response_array['risk']=$risk;
                $response_array['score']=$score;
                $response_array['message']=$message;
        }
        }
        return $response_array;
  }
  
  function rvrw_get_user_ip() {
if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
//check ip from share internet
$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
//to check ip is pass from proxy
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
$ip = $_SERVER['REMOTE_ADDR'];
}
return apply_filters( 'wpb_get_ip', $ip );
}
 
  
add_action('admin_head', 'rvrw_plugin_admin_head_js');
add_action('admin_head', 'rvrw_plugin_admin_head_css');