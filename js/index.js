/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var jq = jQuery.noConflict();




function rvrw_getid(id) {

    jq("#review_form").hide(300);

    jq("#review_form").show(100);

    jq("#rating_id").val(id);

    
    jq("#ratingform").trigger("reset");
    

    jq("#feedback").html('');

}

jq(document).ready(function ($) {
    
     jq('[data-popup-open]').on('click', function (e) {
        var description = jq('#description').val();
        var title = jq('#title').val();

        var rateisChecked = jQuery("input[name=rating]:checked").val();
        var firstName = jq('#txt_name').val();
        if (!rateisChecked) {

            jq("div#rate_error").show();

            return false;

        }

        if (description == "") {

            jq("div#rate_error").hide();
            jq("div#description_error").show();

            return false;

        }
        if (title == "") {

            jq("div#rate_error").hide();
            jq("div#description_error").hide();

            jq("div#title_error").show();

            return false;

        }
        if (firstName == "") {

            jq("div#rate_error").hide();
            jq("div#description_error").hide();
            jq("div#title_error").hide();
            jq("div#name_error").show();

            return false;

        }
        if (jq('#description').val() != "" && jq('#title').val() != "" && rateisChecked)
        {
            var targeted_popup_class = jQuery(this).attr('data-popup-open');

            jq('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);

            e.preventDefault();
        }

    });

    //----- CLOSE

    jq('[data-popup-close]').on('click', function (e) {

        var targeted_popup_class = jQuery(this).attr('data-popup-close');

        jq('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

        e.preventDefault();

    });

    jq(".trans_id").click(function (e) {

        jq(".trans_id").removeClass("review_active");

        jq(this).addClass("review_active");

        e.preventDefault();

    });



    jq("#demo1 .full").hover(function () {

        jq("#feedback").text(jq(this).attr('title'));

    },
            function () {

                if (jq('.selected')) {

                    jq('#feedback').text(jq('.selected').attr('data-desc'));

                } else {

                    jq('#feedback').text('Rate this product');

                }

            });

    jQuery(function (jq) {

        jq('.error').hide();



    });
    
    jq('#review_it').click(function (event) {
       
        jq("#ratingform").submit();
    });

      
   

});

 function rvrwShowRply(id) {

        jq(".rplDiv").hide(300);

        jq("#rpldv" + id).show(100);
    }
