/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var jq = jQuery.noConflict();

function rvrwSendEmail(order_id){
 jq("#orderId").val(order_id);
     jq("#frmsend").submit();   
}

function rvrwCloseCommentBox(box)
{
    jQuery("#textnotes").val('');
    jQuery('#' + box).css({'display': 'none'});
}


function rvrwOpenRegistration()
{
    jQuery('#browseArea' ).css({'display': 'block'});
}

jq(document).ready(function ($) {
    
     jq('#rvrw_country').on('change', function (e) {
         url = jQuery("#rvrwSiteUrl").val();
          cId = jQuery("#rvrw_country").val();
        
        var loadUrl = url + "/wp-content/plugins/rebusify-verified-reviews-woocommerce-extension/includes/rvrw_fetch_states.php?cId=" + cId;
        //alert(loadUrl);
        var jqxhr = jQuery.get(loadUrl);

        jqxhr.success(function (data)
        {
           jQuery("#my_custom_state_field").html(data);
            
        });

        jqxhr.error(function (data)
        {
            
            alert('We are unable to load page,Please try again');

        });
         
     });
 });