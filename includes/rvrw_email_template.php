<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//for xss validation
include_once RVRW_ABSPATH.'/includes/rvrw_validation_functions.php';

global $wpdb;
$RVRW_EMAIL_TEMPLATE= $wpdb->prefix."rvrw_email_template";
if(isset($_REQUEST['btn_save'])){
    $body= isset($_REQUEST['txt_body']) ? strip_tags(trim($_REQUEST['txt_body'])) : "";
    $subject= isset($_REQUEST['txt_subject']) ? strip_tags(trim($_REQUEST['txt_subject'])) : "";
    if($body!="" && $subject!=""){
    $body=  rvrw_sanitize_data($body,'xss_clean');
    $subject=  rvrw_sanitize_data($subject,'xss_clean');
    
     $data= array('body'=>$body, 'subject' => $subject);
   $where =array('id'=>1);
   $data_formate=array('%s','%s');
   $where_formate=array('%d');
   
   //$wpdb->update($RVRW_EMAIL_TEMPLATE,$data,$where,$data_formate,$where_formate);
   $msg="Template updated successfully.";
   
   $query="update $RVRW_EMAIL_TEMPLATE set body='$body',subject='$subject' where id=1";
   //echo $query;
   $wpdb->query($query);
    }
    else
    {
         $msg="Please fill all the fields.";
    }
}


       

$email= $wpdb->get_row("select * from $RVRW_EMAIL_TEMPLATE limit 1");
?>
<div class="rvrow fulldiv">

    <div class="rvrow-inner">
        <?php if(isset($msg)) echo "<div style='color:red;'>$msg</div>";?>
        <script src="<?php echo get_site_url()?>/wp-content/plugins/rebusify-verified-reviews-woocommerce-extension/ckeditor/ckeditor.js" type="text/javascript"></script>
        <form action="" method="POST">
            <table>
                <tr><td>
                        <label>Email Subject:</label><br>
                        <input type="text" value="<?php echo $email->subject;?>" name="txt_subject" required="" />
                    </td></tr>
                <tr><td>
                        <label>Email Body:</label>      <br> 
                        <textarea required="" style="width: 469px; height: 300px;" class="ckeditor" name="txt_body">
                            <?php echo $email->body;?>
                        </textarea>

                    </td></tr>

                <tr><td>
                    <input type="submit" class="button button-primary" name="btn_save" value="Save Changes"/></td></tr>

            </table>
        </form>
    </div>
</div>