"use strict";
jQuery(document).ready(function () {
    setTimeout(function(){
        var spinner = jQuery('#loader');
        spinner.hide();
    },600);

    var searchParams = new URLSearchParams(window.location.search);
    
    if(searchParams.has('get_page') || searchParams.has('date_preset')) {
        jQuery('html, body').animate({scrollTop: jQuery('#trev-nav-tabs').offset().top - 50}, 1000);
    }

    jQuery('#auto_post_article').click(function(){
        jQuery(this).attr('disabled', true);
        var status = jQuery(this).val();
        jQuery.ajax({
            url: revoiceDataAjax.ajaxurl,
            method: 'POST',
            dataType: "json",
            data: {
                'action': 'trev_csmap_toggle_auto_post',
                'status': status,
                'security': revoiceDataAjax.ajax_nonce

            },
            success: function (response) {
             
                if(response.success) {
                    setTimeout(function(){
                        jQuery('#auto_post_article').prop("disabled", false); 
                        jQuery('#auto_post_article').removeAttr("disabled");
                    },500);

                }
            },
            error: function (jqXHR, exception) {

            },
        });
    });

    jQuery('#toggle_share_article').click(function(){
        jQuery(this).attr('disabled', true);
        var id = jQuery(this).attr('id');
        jQuery.ajax({
            url: revoiceDataAjax.ajaxurl,
            method: 'POST',
            dataType: "json",
            data: {
                'action': 'trev_csmap_toggle_share',
                'id': id,
                'security': revoiceDataAjax.ajax_nonce

            },
            success: function (response) {

                if(response.success) {
               
                 setTimeout(function(){
                    jQuery('#toggle_share_article').prop("disabled", false); 
                    jQuery('#toggle_share_article').removeAttr("disabled");
                },500);

             }else{
                location.href=response.redirect;
             }


         },
         error: function (jqXHR, exception) {

         },
     });
    });

    jQuery('#toggle_share_listing').click(function(){
        jQuery(this).attr('disabled', true);
        var id = jQuery(this).attr('id');
        
        jQuery.ajax({
            url: revoiceDataAjax.ajaxurl,
            method: 'POST',
            dataType: "json",
            data: {
                'action': 'trev_csmap_toggle_share',
                'id':  id,
                'security': revoiceDataAjax.ajax_nonce

            },
            success: function (response) {

                if(response.success) {
                   
                   setTimeout(function(){

                    jQuery('#toggle_share_listing').prop("disabled", false); 
                    jQuery('#toggle_share_listing').removeAttr("disabled");
                },500);

               }else{
                 location.href=response.redirect;
               }

           },
           error: function (jqXHR, exception) {

           },
       });
    });


    jQuery('#check_licence').click(function(e){
        jQuery(this).text('Checking...');
        e.preventDefault();
      
        jQuery.ajax({
            url: revoiceDataAjax.ajaxurl,
            method: 'POST',
            dataType: "json",
            data: {
                'action': 'trev_csmap_check_licence_ajax',
                'security': revoiceDataAjax.ajax_nonce

            },
            success: function (response) {
                console.log(response);
                if(response.success) {
                   
                  location.href = response.redirect;
                  jQuery('#check_licence').text("Check Licence"); 
               }

           },
           error: function (jqXHR, exception) {

           },
       });
    });
    var statusVersion = revoiceDataAjax.plugin_version;
    jQuery('.free-version li input').click(function(){
        
        if(!statusVersion){
            jQuery(this).prop('checked', false); 
            trev_csmap_subscription_popup();    
        }
        
    });
    
    function trev_csmap_subscription_popup() {
        Swal.fire({
            title: revoiceDataAjax.premium_message['premium_title'],
            html: revoiceDataAjax.premium_message['premium_v_description'] + '<div class="revoice-footer-buttons"><a href="' + revoiceDataAjax.premium_message['premium_v_link'] + '" class="revoice-button" target="_blank">' + revoiceDataAjax.premium_message['premium_v_button'] + '</a></div>',
            showCloseButton: true,
            showConfirmButton: false,
            showClass: {
                popup: ''
            },
            hideClass: {
                popup: ''
            },
            customClass: {
                popup: 'revoice-subscription-popup',
            },
        });
    }

    jQuery('#dashboard_fb_pages').change(function () {
         var spinner = jQuery('#loader');
        spinner.show();
        var fb_page = jQuery(this).val();
        var date_preset = jQuery('#fb_date_preset').val();
        location.href = location + '&get_page=' + fb_page + '&date_preset=' + date_preset;

    });

    jQuery('#fb_date_preset').change(function () {
        var spinner = jQuery('#loader');
        spinner.show();
        var date_preset = jQuery(this).val();
        var fb_page = jQuery('#dashboard_fb_pages').val();
        location.href = location + '&get_page=' + fb_page + '&date_preset=' + date_preset;

    });

    var selectedOption = jQuery('#share_times').val();
    if (selectedOption == 1) {
        jQuery('#drip_feed_time').parent().parent().show();
    } else {
        jQuery('#drip_feed_time').parent().parent().hide();
    }

    jQuery('#share_times').change(function () {
        var selectedOption = jQuery(this).val();
        if (selectedOption == 1) {
            jQuery('#drip_feed_time').parent().parent().show();
        } else {
            jQuery('#drip_feed_time').parent().parent().hide();
        }
    });

    if (jQuery('#drip_feed_time').length > 0) {
        jQuery('#drip_feed_time').timepicker({
            showTodayButton: true,
            todayHighlight: true,
            minDate: 0
        });
    }

    jQuery("#ui-datepicker-div").addClass("rev-calendar");

    jQuery("#post_type").on("change", function (e) {

        var post_cat = jQuery(this).val();

        jQuery.ajax({
            url: revoiceDataAjax.ajaxurl,
            method: 'POST',
            dataType: "json",
            data: {
                'action': 'trev_csmap_select_post_cat',
                'post_select': post_cat,
                'revoice_nonce': revoiceDataAjax.ajax_nonce

            },
            success: function (response) {
                var categories = response.categories;

                if (categories) {
                    var cat_list = '<option value="all">All Categories</option>';
                    for (var i = 0; i < categories.length; i++) {
                        cat_list += '<option value="' + categories[i].id + '">' + categories[i].name + '</option>';
                    }

                    jQuery('.category_filter #category').html(cat_list);
                }


            },
            error: function (jqXHR, exception) {

            },
        });
    });
});


jQuery('body').on('focus', '#sc_datetime', function () {
    var minutesToAdd   = 3;
    var currentDate    = new Date();
    var futureDate     = new Date(currentDate.getTime() + minutesToAdd * 60000);
    var currentHour    = currentDate.getHours();
    var currentMinutes = (currentDate.getMinutes() + 3);

    jQuery(this).datetimepicker({
        dateFormat: "yy-mm-dd",
        minDate: futureDate,
        onSelect: function (dateText, inst) {
        }
    });
});


if(jQuery('body').hasClass('swal2-shown ')){
    jQuery.datepicker._gotoToday = function (id) {
        setTimeout(function () {
            var minutesToAdd = 3;
            var currentDate = new Date();
            var futureDate = new Date(currentDate.getTime() + minutesToAdd * 60000);

            jQuery(id).datetimepicker('setDate', new Date(currentDate.getTime() + minutesToAdd * 60000));
        }, 300);
    }    
}
