<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 error_reporting(0);
  error_reporting(E_ALL);
  ini_set('display_errors', 'On');
 
//for xss validation

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

include_once('rvrw_validation_functions.php');
 include_once('rvrw_term.php');
 
if (isset($_REQUEST['btnsubmit'])) {
    $apiKey = isset($_REQUEST['txt_apikey']) ?  rvrw_sanitize_data($_REQUEST['txt_apikey'], 'xss_clean') : "";
    $enable = isset($_REQUEST['chk_enable']) ? rvrw_sanitize_data($_REQUEST['chk_enable'],'xss_clean') : 0;
    $email = isset($_REQUEST['txt_email']) ? sanitize_email($_REQUEST['txt_email']) : "";
    $chk_icons= isset($_REQUEST['chk_icons']) ? rvrw_sanitize_data($_REQUEST['chk_icons'],'int') : 0;
    $chk_video= isset($_REQUEST['chk_video']) ? rvrw_sanitize_data($_REQUEST['chk_video'],'int') : 0;
   
    $email = rvrw_sanitize_data($email, 'xss_clean');
    $enable=rvrw_sanitize_data($enable,'int');
    $chk_icons=rvrw_sanitize_data($chk_icons,'int');
    $chk_video=rvrw_sanitize_data($chk_video,'int');
    
    $email= sanitize_text_field($email);
    $apiKey= sanitize_text_field($apiKey);
      
    

    $siteurl = get_site_url();
    $sendRequest=FALSE; 
    $success = "";
    $message="Data saved successfully.";

    if ($apiKey!="" && $email!="") {
        
        // check if he has change his api key or his email only then 
        // we will send request to rebusify
        
        if (rvrw_exist_option('rbs_serial_key')) {
                      $oldApi=  get_option('rbs_serial_key', $apiKey);
                      if($oldApi!=$apiKey)
                      {
                          $sendRequest=TRUE; 
                      }
                    } else {
                        $sendRequest=TRUE;
                    }

                    if (rvrw_exist_option('rbs_email')) {
                       $oldEmail= get_option('rbs_email', $email);
                       if($oldEmail!=$email)
                       {
                             $sendRequest=TRUE; 
                       }
                    } else {
                        $sendRequest=TRUE;
                    }

        if($sendRequest) { 
        // send api key activation report to trustalyze.com
   

    
     $body = array( "domain"=>$siteurl,
             "apikey"=>$apiKey,
             "email"=>$email
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
         
        $response = wp_remote_post( 'https://reviews.trustalyze.com/api/rbs/api_key_activation.php', $args );
        $response_message = wp_remote_retrieve_response_message( $response );
        $http_code = wp_remote_retrieve_response_code( $response );
       
        
        if ( is_wp_error( $response ) ) {
              //$error_message = $response->get_error_message();
              $message = "Sorry Rebusify site is not accessible.";
        }
        else {
        if ( 200 != $http_code && ! empty( $response_message ) ) {
		//return new WP_Error( $http_code, $response_message );
                 $message = "Sorry Rebusify site is not accessible.";
	} elseif ( 200 != $http_code ) {
		//return new WP_Error( $http_code, 'Unknown error occurred' );
             $message = "Sorry Rebusify site is not accessible.";
	} else {  
		 $responsedata = wp_remote_retrieve_body( $response );
                 
                    $data = json_decode($responsedata, true);
                    // print_r($data);
                    foreach ($data as $key => $value) {
                    $message = $value['msg'];
                    $success = $value['success'];
                    $product_type = $value['producttype'];
                }
        }
        }
       
         
    }
        
         if ($success == 'yes' && $sendRequest) {
             // update api key and email only if we have send a request and responce is success
                   

                    if (rvrw_exist_option('rbs_serial_key')) {
                        update_option('rbs_serial_key', $apiKey);
                    } else {
                        add_option('rbs_serial_key', $apiKey);
                    }

                    if (rvrw_exist_option('rbs_email')) {
                        update_option('rbs_email', $email);
                    } else {
                        add_option('rbs_email', $email);
                    }
                    if (rvrw_exist_option('rbs_product_type')) {

                        update_option('rbs_product_type', $product_type);
                    } else {

                        add_option('rbs_product_type', $product_type);
                    }
                    
                    
                    
                } 
                
                 if (rvrw_exist_option('rbs_enabel_review')) {

                        update_option('rbs_enabel_review', $enable);
                    } else {

                        add_option('rbs_enabel_review', $enable);
                    }
                    
                    if (rvrw_exist_option('rbs_icons')) {

                        update_option('rbs_icons', $chk_icons);
                    } else {

                        add_option('rbs_icons', $chk_icons);
                    }
                    
                    if (rvrw_exist_option('rbs_video')) {

                        update_option('rbs_video', $chk_video);
                    } else {

                        add_option('rbs_video', $chk_video);
                    }
        
    } else {
        $message = 'Please enter all details.';
    }
}
if(isset($_REQUEST['btnsubmitregis']))
{
    include 'rvrw_submit_order.php';
}


$rbs_email = "";
$enableReview = 0;
$rbs_email = "";
$rbs_serial_key = "";
$enable_video=0;
$product_type="F";
$enable_icons=0;

if (rvrw_exist_option('rbs_enabel_review')) {
    $enableReview = get_option('rbs_enabel_review');
}

if (rvrw_exist_option('rbs_serial_key')) {
    $rbs_serial_key = get_option('rbs_serial_key');
}

if (rvrw_exist_option('rbs_email')) {
    $rbs_email = get_option('rbs_email');
}

if (rvrw_exist_option('rbs_icons')) {
    $enable_icons = get_option('rbs_icons');
}
if (rvrw_exist_option('rbs_video')) {
$enable_video=get_option('rbs_video');
}

if (rvrw_exist_option('rbs_product_type')) {
 $product_type=get_option('rbs_product_type');

}





?>
<style>
    

.advanced-modal-popup {
	display: none;
	position: fixed;
	
	padding-top: 80px;
	left: 0;
	top: 32px;
	width: 100%;
	height: 80%;
	overflow: auto;
	background-color: rgb(0,0,0);
	background-color: rgba(0,0,0,0.4);
	z-index: 9999;
}
.popupheader {
	padding: 15px 16px;
	background-color: #D75842;
	color: white;
        margin-bottom: 0px !important;
        border-radius: 4px 4px 0px 0px;
        text-align: center;
        font-size: 22px;
}

.popupheader .close-sign {
	color: white;
	float: right;
	font-size: 28px;
	font-weight: bold;
	line-height: 0.1;
          cursor: pointer;
}
.popupheader h3 {
	margin: 5px 0px;
	text-transform: uppercase;
	font-size: 16px;
	font-weight: bold;color:white;
}
.advanced-modal-body-popup.align-left {
	text-align: left;
	padding: 10px 15px;
        background-color: white;
        min-height:300px;
        border-radius: 0px 0px 4px 4px;
}
.advanced-modal-content-popup.bigpopup {
    max-width: 460px;
    margin:auto;
}
#browseAreaInner{
    max-height:800px;
    overflow-y: scroll;
}
.rvrwrgsdv{
    width:100%;
    float:left;
    margin-top:10px;
}
.rvrwrgsdv50
{
    margin-top:10px;
    width:50%;
    float:left;
}
.rvrwrgsdv60
{
    margin-top:10px;
    width:60%;
    float:left;
}
.rvrwrgsdv40
{
    margin-top:10px;
    width:40%;
    float:left;
}
.rvrwrgsdv50 input{
    width:71%;
}
.rvrwrgsdv input{
    width:85%;
}

#rvrw_country{
    width:85%;
}
#rvrw_state{
    width:85%;
}
.rvrwrgsdv span{
    width:100%;display:block;
}
#rvrwtxtara{
    background-color: #d8d2d2;
     width:85%;
    height:150px;
}

.rvrwrgsdv input[type=checkbox]{
    width:10px;
    border-color:#D75842;
}

.rvrwrgsdv input[type=submit]{
    width:50%;
}
#browseAreaInner label{
    width:100%;float:left; 
}
.rvrwlogo{
 text-align: left;
 float: left;
 background-color: white;
}
.rvrwsubmit{
    background-color: #D75842 !important;
    color:white !important;
    border-color:  #D75842 !important;
}
.rvrwtopdiv{
    margin-bottom: 10px;
}
</style>
<div class="wrap woocommerce">
    <h2>Trustalyze</h2>

    <div id="store_address-description">
<?php if (isset($message)) echo '<p style="color:red">' . $message . '</p>'; ?> 
        <p>
            Use the Trustalyze Confidence System (TCS) with Woocommerce for verified reviews.
            You will need a Serial Key. Please visit <a href="https://reviews.trustalyze.com/plans/" target="_blank">Trustalyze.com</a> to get your key.</p>
    </div>
    <?php
    if (!rvrw_exist_option('rbs_serial_key')) {  ?>
        <button name="btnregister" onclick="rvrwOpenRegistration()" class="button-primary woocommerce-save-button"
                type="buttun" value="Register Free Account" >Register Free Account</button>
    <?php }
    ?>
    <input type="hidden" id="rvrwSiteUrl" value="<?php echo get_site_url()?>" />
    <form action="" method="POST">
        <table class="form-table">

            <tr valign="top">

                <th scope="row" class="titledesc">
                    <label for="woocommerce_store_address">Enable/Disable 
                    </label>
                </th>

                <td><input <?php if ($enableReview == 1) echo 'checked'; ?> value="1" type="checkbox" name="chk_enable"/>Enable Trustalyze Reviews </td>
            </tr>
            <tr valign="top">

                <th scope="row" class="titledesc">
                    <label for="rebusify_email_address">Trustalyze Email Address 
                    </label>
                </th>


                <td class="forminp forminp-text">
                    <span ><i title="This is your trustalyze email address" class="fa fa-question-circle" aria-hidden="true"></i>
                    </span>&nbsp;
                    <input required="" type="email" value="<?php echo $rbs_email; ?>" name="txt_email"/>
                </td>

            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="rebusify_serial_key">Trustalyze Serial Key
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <span ><i title="This is your Rebusify Serial Key 
                              which you will get from trustalyze.com"
                              class="fa fa-question-circle" aria-hidden="true">

                        </i>
                    </span>&nbsp;
                    <input required="" type="text" value="<?php echo $rbs_serial_key; ?>" name="txt_apikey"/>
                </td>

            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="rebusify_serial_key">My Account Page Icons
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <input type="checkbox" value="1" <?php if($enable_icons==1) echo "checked"; ?> name="chk_icons"/>
                    Enable icons on My Account Page. Some themes have icons on my account page.
                </td>

            </tr>
             <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="rebusify_serial_key">Enable Video Reviews
                    </label>
                </th>
                <td class="forminp forminp-text">
                   <!-- <input <?php if($product_type=="F") echo 'disabled'; ?> type="checkbox" value="1" <?php if($enable_video==1) echo "checked"; ?> name="chk_video"/>-->
                    <input type="checkbox" value="1" <?php if($enable_video==1) echo "checked"; ?> name="chk_video"/>
                    Enable your customer to leave a video review along with their text review.<br>
                    Requires HTTPS
                </td>

            </tr>

        </table>
        <p class="submit">

            <button name="btnsubmit" class="button-primary woocommerce-save-button"
                    type="submit" value="Save changes">Save changes</button>
        </p>
    </form>
<?php if (rvrw_exist_option('rbs_product_type')) {
    if (get_option('rbs_product_type') == 'F') {
        ?>


            <table width="600" border="0">
                <tr>
                    <td width="600">As a Trustalyze user, you are currently limited to 5000 transactions per month including posting to the blockchain. If you have more than 5000 review transactions per month please contact us.
                    </td>
                    <td width="25">&nbsp;</td>
                    <td width="120"><form action="https://reviews.trustalyze.com/contact/" method="post" target="_blank" >
                            <input type="submit" value="Contact Us" class="button button-primary"/>
                        </form>

                    </td>
                </tr>
            </table>
    <?php }
}
?>
</div>

<div style="display:none;" id="browseArea"  class="advanced-modal-popup">
                    <div class="advanced-modal-content-popup bigpopup">
                        <div class="popupheader">
                            <span class="rvrwlogo">
                                <img src="<?php echo get_site_url();?>/wp-content/plugins/rebusify-verified-reviews-woocommerce-extension/images/registration-logo.png"/>
                            </span>
                            <span onclick="rvrwCloseCommentBox('browseArea')" class="close-sign">×</span> 
                            
                            CREATE FREE ACCOUNT</div>
                        <div class="advanced-modal-body-popup align-left">
                            <div class="rvrwtopdiv">This form will create your free account at Trustalyze.com automatically.</div>
                            <form action="" method="POST">
                            <div id="browseAreaInner">
                                <div class="rvrwrgsdv50">
                                    <label>First Name<abbr class="required" title="required">*</abbr></label><br>
                                    <input type="text" name="txt_fname" value="<?php if(isset($fname)) echo $fname;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv50">
                                     <label>Last name<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_lname" value="<?php if(isset($lname)) echo $lname;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>Company name<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_company_name" value="<?php if(isset($company_name)) echo $company_name;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>Website Url<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_weburl" value="<?php if(isset($web_url)) echo $web_url;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>About Company<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_about_company" value="<?php if(isset($about_company)) echo $about_company;?>" required/>
                                </div>
                                <div class="rvrwrgsdv">
                                     <label>Site Keyword<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_keyword" value="<?php if(isset($keyword)) echo $keyword;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                    
                                     <?php 
                                global $woocommerce;
$countries_obj   = new WC_Countries();
$countries   = $countries_obj->__get('countries');
 $default_country = $countries_obj->get_base_country();
echo '<div id="my_custom_countries_field">';

woocommerce_form_field('rvrw_country', array(
'type'       => 'select',
'class'      => array( 'chzn-drop' ),
'label'      => __('Country'),
'placeholder'    => __('Enter something'),
'options'    => $countries,
'name'   =>"dd_country",
'required'   =>true,
'default' => $default_country
)
);
echo '</div>';
                                     ?>
                                <!--<input type="text" name="txt_country" value=""/>-->
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>Street Address<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_street" value="<?php if(isset($address1)) echo $address1;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>Town / City Keyword<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_city" value="<?php if(isset($city)) echo $city;?>" required/>
                                </div>
                                <div class="rvrwrgsdv">
                                     
                                <?php  global $woocommerce;
    $countries_obj   = new WC_Countries();
    $countries   = $countries_obj->__get('countries');
    $default_county_states = $countries_obj->get_states( $default_country );

    echo '<div id="my_custom_state_field">';

    woocommerce_form_field('rvrw_state', array(
    'type'       => 'select',
    'class'      => array( 'chzn-drop' ),
    'label'      => __('State'),
    'placeholder'    => __('Enter something'),
    'options'    => $default_county_states,
    'name'       =>"dd_state",
    'required'   =>true
    )
    );
    echo '</div>'; ?>
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>Postcode / Zip<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_zip" value="<?php if(isset($zip)) echo $zip;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>Phone<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_phone" value="<?php if(isset($phone)) echo $phone;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>Email Address<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_email" value="<?php if(isset($email)) echo $email;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>Account Username<abbr class="required" title="required">*</abbr></label><br>
                                <input type="text" name="txt_account_username" value="<?php if(isset($user_name)) echo $user_name;?>" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                     <label>Create account password<abbr class="required" title="required">*</abbr></label><br>
                                     <input type="password" name="txt_password" value="" required/>
                                </div> 
                                <div class="rvrwrgsdv">
                                    <textarea disabled="" id="rvrwtxtara"><?php echo $terms;?></textarea>
                                
                                </div> 
                                <div class="rvrwrgsdv">
                                     <input type="checkbox" name="ckk_term" value="" required/>
                                I have read and agree to the terms and conditions.
                                </div>
                                <div class="rvrwrgsdv">
                                <input type="submit" class="button rvrwsubmit" value="Complete Registration" name="btnsubmitregis" />
                                </div> 
                                
                            </div>
                        </form>
                            
                            


                        </div>
                    </div>
                </div>