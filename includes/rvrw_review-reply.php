<?php
/* error_reporting(0);
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
 */

global $current_user, $wpdb, $post;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>
<h4>Review Reply</h4>

<?php
$TRANSACTION_REVIEW_TABLE = $wpdb->prefix . 'rvrw_transaction_reviews';
$RVRW_REVIEW_REPLIES = $wpdb->prefix . "rvrw_review_replies";

//for xss validation
include_once('rvrw_validation_functions.php');

if (isset($_POST['btnsubmit'])) {
   
    $txtreply = isset($_REQUEST['txtrply']) ? rvrw_sanitize_data(strip_tags($_REQUEST['txtrply']), 'xss_clean') : "";

    $txtreply= substr($txtreply, 0,2999); // 3000 char limit for review reply

    $date = date("Y-m-d");
    $review_transaction_id = isset($_REQUEST['review_transaction_id']) ? rvrw_sanitize_data( $_REQUEST['review_transaction_id'], 'xss_clean') : "";
    
    if($txtreply!="" && $review_transaction_id!="") {
    // fetch thread id 
    $thread_id = 0;
    $review_id=0;
    $getthread = $wpdb->get_row("select id,review_id from $RVRW_REVIEW_REPLIES where "
            . " review_transaction_id=$review_transaction_id order by id DESC limit 1");
    if (!is_null($getthread)) {
        $thread_id = $getthread->id;
        $review_id=$getthread->review_id;
    
  
       $data = array('reply' => $txtreply, 'date' => $date,'review_transaction_id'=>$review_transaction_id,
             'thread_id'=>$thread_id,'type'=>'C','review_id'=>$review_id );
        $format = array('%s','%s','%d','%d','%s','%d');
        $wpdb->insert($RVRW_REVIEW_REPLIES,$data,$format);

    // update rebusify 
     
    
     $body = array( "reply"=>$txtreply,
            "review_transaction_id"=>$review_transaction_id,
            "type"=>"C",
            "review_id"=>$review_id
         );
         
         $args = array(
            'method' => 'POST',
            'timeout' => '5',
            'redirection' => '5',
            'headers'  => array(
                'Content-type: application/x-www-form-urlencoded'
            ),
           'body' => $body
        );
         
        $response = wp_remote_post( 'https://reviews.trustalyze.com/api/rbs/fetch_customer_reply.php', $args );
        $response_message = wp_remote_retrieve_response_message( $response );
        $http_code = wp_remote_retrieve_response_code( $response );
       
        
        if ( is_wp_error( $response ) ) {
              //$error_message = $response->get_error_message();
              $message = "Sorry Rebusify site is not accessible.";
        }
        else {
        if ( 200 != $http_code && ! empty( $response_message ) ) {
		//return new WP_Error( $http_code, $response_message );
                 $message = $error_message."Sorry Rebusify site is not accessible11.";
	} elseif ( 200 != $http_code ) {
		//return new WP_Error( $http_code, 'Unknown error occurred' );
             $message = $error_message."Sorry Rebusify site is not accessible2.";
	} else {
		$message="Reply Saved.";
        }
        }
    
    }
}
else
{
    $message="Please fill all the fields.";
}
}
 
$transactions = $wpdb->get_results("SELECT * FROM $TRANSACTION_REVIEW_TABLE where"
        . " user_id = $current_user->ID && status = '1' ");

$rplyexist = FALSE;

if (!is_null($transactions)) {

    foreach ($transactions as $transaction) {

        $reviewReply = $wpdb->get_row("SELECT order_id,rp.reply,rp.type FROM $TRANSACTION_REVIEW_TABLE as rt "
                . " inner join $RVRW_REVIEW_REPLIES as rp on rt.id=rp.review_transaction_id"
                . " where rt.id=" . $transaction->id .
                " order by thread_id DESC limit 1");



        if (!is_null($reviewReply)) {
            if ($reviewReply->type == 'M') { /// if last reply is by merchant
                $rplyexist = TRUE;
                ?>


                <form action="" method="POST">
                    <input type="hidden" value="<?php echo $transaction->id; ?>" name="review_transaction_id"/>
                    <div class="rv_rpl_dv">
                        <?php echo '#' . $transaction->order_id; ?>

                    </div>
                    <div class="rv_rpl_dv">
                        <?php echo $reviewReply->reply; ?>
                    </div>
                    <div class="rv_rpl_dv">
                        <a  id="trans_id_<?php echo $transaction->id ?>" class="trans_review" onClick="rvrwShowRply(<?php echo $transaction->id ?>)" href="javascript:void(0);"> 
                            <img src="<?php echo RVRW_PATH . '/images/reply-icon.png' ?>"/>&nbsp; Reply
                        </a>
                    </div>
                    <div class="rplDiv" id="rpldv<?php echo $transaction->id; ?>">
                        <div class="rv_rpl_dv">
                            <textarea maxlength="3000" required="" rows="4" cols="40" placeholder="Submit Your Reply" name="txtrply"></textarea>

                        </div>
                        <div class="rv_rpl_dv">
                            <input type="submit" class="btn rev_button" value="Submit Reply" name="btnsubmit" />
                        </div>
                    </div>
                </form>

                <?php
            }
        }
    }
    if (!$rplyexist) {
        echo "No replies from the merchant.";
    }
} else {

    echo "No replies from the merchant.";
}
?>