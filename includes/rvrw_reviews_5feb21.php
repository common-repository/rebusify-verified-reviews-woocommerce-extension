<?php
//error_reporting(0);
/*error_reporting(0);
error_reporting(E_ALL);
ini_set('display_errors', 'On');
*/
global $current_user, $wpdb, $post;
$POST_TABLE = $wpdb->prefix . 'posts';
$table_name = $wpdb->prefix . 'rvrw_transaction_reviews';
//$P$BoEBV.rBChXkfxrhdZBtTnx2gizvt8/




if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



if(isset($_POST['btnsubmitReview']))
{
    include_once RVRW_ABSPATH.'/includes/rvrw_reviews_process.php';
}
if(isset($msg))
{
    echo '<div>'.$msg.'</div>';
}
function rvrw_check_https() {
	
	if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
		
		return true; 
	}
	return false;
}
 $enable_video=0;
 $product_type="F";
if (rvrw_exist_option('rbs_video')) {
$enable_video=get_option('rbs_video');
}
if (rvrw_exist_option('rbs_product_type')) {
 $product_type=get_option('rbs_product_type');
}

?>
<script src="<?php echo plugins_url('rebusify-verified-reviews-woocommerce-extension/js/RecordRTC.js'); ?>"></script><script src="<?php echo plugins_url('rebusify-verified-reviews-woocommerce-extension/js/DetectRTC.js'); ?>"> </script><script src="<?php echo plugins_url('rebusify-verified-reviews-woocommerce-extension/js/adapter-latest.js'); ?>"></script><script src="<?php echo plugins_url('rebusify-verified-reviews-woocommerce-extension/js/video_recording.js');?>"></script>
<h4>Review Transactions</h4>
<?php


$getTxnQuery="SELECT tr.* FROM $table_name AS tr inner join "
        . " $POST_TABLE AS p on tr.order_id=p.ID where tr.user_id = $current_user->ID "
        . " and tr.status = '0' and p.post_status='wc-completed' && DATEDIFF(NOW(), tr.transaction_date) <= 60";
$transactions = $wpdb->get_results($getTxnQuery);



$countQuery="SELECT count(*) FROM $table_name AS tr inner join "
        . " $POST_TABLE AS p on tr.order_id=p.ID where tr.user_id = $current_user->ID "
        . " and tr.status = '0' and p.post_status='wc-completed' && DATEDIFF(NOW(), tr.transaction_date) <= 60";

$rowExist=$wpdb->get_var($countQuery);



if ($rowExist==0) {
    
     echo "No transactions to review.";
} else {
 


    foreach ($transactions as $transaction) {
        ?>
        <a  id="trans_id_<?php echo $transaction->id ?>" class="trans_id" 
            onClick="rvrw_getid(<?php echo $transaction->id ?>)" href="javascript:void(0);"><p><?php echo '#' . $transaction->order_id; ?></p></a>

        <?php
    }
}


?>
 <div id="review_form" class="review_form">
    <form id="ratingform" method="POST" action=""><input type="hidden" name="btnsubmitReview" value="1" />
       
        
        <fieldset id='demo1' class="rating_review"><input type="hidden" name="video_id" id="video_id"  value="0" /><input type="hidden" name="domain_name" id="domain_name"  value="<?php echo get_site_url();?>" /><input type="hidden" name="rating_id" id="rating_id"  value="" /><input class="stars" type="radio" id="star5" type="radio" name="rating" value="5"/><label class = "full" for="star5" title="Awesome - 5 stars"></label><input class="stars" type="radio" id="star4" type="radio" name="rating" value="4" /><label class = "full" for="star4" title="Pretty good - 4 stars"></label><input class="stars" type="radio" id="star3" type="radio" name="rating" value="3"/><label class = "full" for="star3" title="Meh - 3 stars"></label><input class="stars" type="radio" id="star2" type="radio" name="rating" value="2"/><label class = "full" for="star2" title="Kinda bad - 2 stars"></label><input class="stars" type="radio" id="star1" type="radio" name="rating" value="1"/><label class = "full" for="star1" title="1 star" ></label></fieldset>
        <div id='feedback'></div>
        <div><input required="" maxlength="25" id="txt_name" type="text" value="" placeholder="First Name" name="txt_name"/>
        </div>

        <div>
            <input maxlength="60" required="" type='text' id='title' name='title' value="" placeholder="Review Title"/>
        </div>
        <div>
            <textarea maxlength="3000" placeholder="Review Description" 
                      required="" name="description" class="rvrwdesc" id="description" rows="4"  cols="40"></textarea><?php 
                      if($enable_video) {
                      if(rvrw_check_https()) { ?>
                      <a   onclick="displayRecording()"><img title="Record a 30 second Video Review" src="<?php echo get_site_url().'/wp-content/plugins/rebusify-verified-reviews-woocommerce-extension/images/record-video-review.png'?>"/></a>
                      <?php } 
           
        else
        { ?>
            <a   onclick="alert('Video recording will work only on secure site')"><img title="Record a 30 second Video Review" src="<?php echo get_site_url().'/wp-content/plugins/rebusify-verified-reviews-woocommerce-extension/images/record-video-review.png'?>"/></a>
        <?php } 
        } ?></div>
        
        
        <div id="rvrw_videomsg">
            
        </div>
        <?php  if($enable_video) { ?>
        <div class="recordrtc" id="rv_videosection" style="display: none;">
           
        
            <div id="rvrw_video">
                     <video id='rv_video' muted=false volume=1 id="your-video-id" controls="" autoplay=""></video>
            </div>
            <div class="rvrw_loadingIcon"  id="rvrw_loadingIcon">
            <img src="<?php echo get_site_url().'/wp-content/plugins/rebusify-verified-reviews-woocommerce-extension/images/loading.gif'; ?>"/>
            Video Saving to Server...
            </div>
             <div>
                <button type="button" class="btn rvrwbtn" id='rv-recording-btn' >Record</button>
            </div>
            <div  style="display: none;" id="rvrw_upload_div"><button type="button" id="rv_play_recording"   class="btn rvrwbtn" >Play</button><button type="button" id="rv_reset_recording"   class="btn rvrwbtn" >Reset</button><button type="button" class="btn rvrwbtn" id="upload-to-server">Save</button></div>
        </div>
        <?php } ?>
       
        <div id="rate_error" class="error" ><p  >please rate it first.</p> 
        </div>
        <div id="description_error" class="error" > 
            <p>Description field is required.</p></div>
        <div id="title_error" class="error" >
            <p>Title field is required.</p></div>
        <div id="name_error" class="error" > 
            <p>First Name field is required.</p></div>

        <div style="text-align: left;">
            <button class="btn rev_button" data-popup-open="popup-1" href="#" 
                    id="before_review">Submit Review
            </button>
        </div>

        <div class="popup" data-popup="popup-1">

            <div class="popup-inner">
                <div class="modal-header">
                    <h5 class="modal-title">Do you want to submit this review?</h5>
                </div>

                <div class="modal-body">        
                    <input type="submit" class="btn rbs-success" id="review_it" data-popup-close="popup-1" value="YES" >

                    <button type="button" class="btn rbs-danger" data-popup-close="popup-1" href="#">
                        NO</button>
                </div>
                <a class="popup-close" data-popup-close="popup-1" href="#">x</a>
            </div>
        </div>
    </form>

</div>
