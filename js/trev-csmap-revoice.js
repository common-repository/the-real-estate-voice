"use strict";
jQuery(document).ready(function () {


    var statusVersion = revoiceDataAjax.plugin_version;
    var searchParams = new URLSearchParams(window.location.search);
    var redirect = false;

    if (searchParams.has('page')) {
        var param = searchParams.get('page');
        if (param == 'trev-csmap-media-hub') {
            redirect = true;

        }
    }

    if (searchParams.has('include_exists') || searchParams.has('tab')) {
        var param = searchParams.get('include_exists');
        var paramTab = searchParams.get('tab');

        if (param == 1 || paramTab == 'order' || paramTab == 'my-content') {
            redirect = false;

        }
    }

    if (!statusVersion && redirect) {
        var url = jQuery('#include_exists').attr('data-url');
        location.replace(url + "&include_exists=1");
    }


});


var winHeight = jQuery(window).height() - 100;
var fb_page_load = revoiceDataAjax.pages;
var library_content = '';
var library_category = '';
var fb_page_select = [];
 
jQuery(document).on('click', '.post_select_page input', function () {

    if(jQuery(this).is(":checked")) {
        
        fb_page_select.push(jQuery(this).val());

    }else{
        
        fb_page_select.splice(jQuery.inArray(jQuery(this).val(), fb_page_select), 1);
    }

    if (fb_page_select.length > 0) {
        jQuery('.share_now_btn').attr('disabled', false);
        jQuery('.schedule_now').attr('disabled', false);
        jQuery('.update_schedule').attr('disabled', false);
    } else {
        jQuery('.share_now_btn').attr('disabled', true);
        jQuery('.schedule_now').attr('disabled', true);
        jQuery('.update_schedule').attr('disabled', true);
    }
 
});


jQuery("#include_exists").on("change", function (e) {

    if (jQuery(this).is(':checked')) {
        var url = jQuery(this).attr('data-url');
        location.replace(url + "&include_exists=1");
    }
    else {
        var url = jQuery(this).attr('data-url');
        location.replace(url);
    }
});


jQuery(document).on("click", ".add_blog_button", function () {
    
    desc  = jQuery(this).attr('data-desc');
    title = jQuery(this).attr('data-title');
    image = jQuery(this).attr('data-image');

    var auto_share               = revoiceDataAjax.auto_share;
    var auto_instant_share       = revoiceDataAjax.auto_instant_share;
    var auto_schedule_share      = revoiceDataAjax.auto_schedule_share;
    var enabled_sharing_for_post = revoiceDataAjax.enabled_sharing_for_post;
    var fb_auto_select           = revoiceDataAjax.fb_ids;

    var elm = this;

    var $ = jQuery;

    Swal.fire({
        text: 'Are sure you want to add this article?',
        showCancelButton: true,
        cancelButtonText: revoiceDataAjax.plugin_version ? 'Add Schedule':'Cancel',
        confirmButtonText: 'Yes',
        animation: true,
        showCloseButton:true,
         customClass: {

            cancelButton: revoiceDataAjax.plugin_version?'add-schedule-article':''
        },
        showClass: {
            popup: ''
        },
        hideClass: {
            popup: ''
        },
    }).then((result) => {
        
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed || result.dismiss=='cancel') {

            var fd = new FormData();
            var add_flag = jQuery(this).data('add-from');

            if (add_flag == 'preivew') {
                 
                var categories   = library_category;
                var content      = library_content;
                var post_excerpt = desc;
            }
            else {
                var categories = jQuery(elm).parent().parents('.blog_item').find('.map_categories').text();
                var content = jQuery(elm).parent().parents('.blog_item').find('.map_content').html();
                var post_excerpt = jQuery(elm).parent().parents('.blog_item').find('.library-excerpt').html();
            }


            var title = jQuery(this).data('title');
            var image = jQuery(this).data('image');
            if(result.dismiss=='cancel' && revoiceDataAjax.plugin_version){

               trev_add_schedule_article(desc,title,image,categories,content,post_excerpt);

           }else{
            
               fd.append("action", 'trev_csmap_revoice_add');
               fd.append("categories", categories);
               fd.append("title", title);
               fd.append("content", content);
               fd.append("post_excerpt", post_excerpt);
               fd.append("image", image);
               fd.append("security", revoiceDataAjax.ajax_nonce);

               if(result.isConfirmed ){
                    Swal.fire({
                        text: 'Adding Article....',
                        showConfirmButton: false,
                        timerProgressBar: true,
                    })
                }

                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    processData: false,
                    contentType: false,
                    data: fd,

                    success: function (response) {

                        if(result.isConfirmed){
                            Swal.fire({
                                icon: 'success',
                                text: 'Article Successfully Added!',
                            }).then((result) => {
                          
                                var post_id = response.post_id;
                                
                                if (response.status == 'publish' && revoiceDataAjax.share_after_adding) {
                                    schedule_share_post_popup(desc, title, image, post_id, '');
                                }
                                if (response.status == 'draft') {
                                    location.href = response.editpage.replace('&amp;', '&');
                                }
                            });
                         }

                      
                    }
                });

           }
           
        }
    })
});


// On change of Category drodown

jQuery(".blog_filter.content-library #category").on('change', function () {

    var url = jQuery(this).attr('data-url');
    var post_cat_url = "&cpost_cat=" + jQuery('#post_type').val();

    if (jQuery(this).val() == "all") {
        location.replace(url);
    }
    else {
        var url = url + "&csrp_cat=" + jQuery(this).val();
        location.replace(url);
    }
});

jQuery("#post_category").on('change', function () {
    var url = jQuery(this).attr('data-url');
    var category = jQuery('#category').val();
    if (category != "all") {
        category_url = "&csrp_cat=" + category;
    }
    var url = url + category_url + "&cpost_cat=" + jQuery(this).val();
    location.replace(url);
});


var desc = "";
var title = "";
var image = "";
var id = "";
var selected_page = "";
var revoice_scheduled_id = "";

jQuery('body').on("keyup", "#share_desc", function () {
    var post_content = jQuery(this).val();
    desc = post_content;
});


jQuery('body').on("click", ".schedule_now", function (e) {

    e.preventDefault();

    var listing_detail = [];
    var $ = jQuery;
    var pages = revoiceDataAjax.pages;
    var listing_page = revoiceDataAjax.is_listing_tab;
    var post_id = jQuery(this).attr('data-id');
    var post_type = jQuery(this).attr('data-post-type');
    var share_desc = jQuery('#sharable_desc').val();
    var platform = 'facebook';
    var minutesToAdd = 0.3;
    var currentdate = new Date();
    var time = currentdate.getHours() + ":" + (currentdate.getMinutes() + 3);

    var day = (currentdate.getDate() < 10 ? '0' : '') + currentdate.getDate();
    var month = (currentdate.getMonth() < 10 ? '0' : '') + (currentdate.getMonth() + 1);
    var currentDateTime = currentdate.getFullYear() + '-' + (month) + '-' + day + ' ' + time;
    var listing_detail_back = jQuery(this).data('listing-detail');
    var page_title = '';

    if (revoiceDataAjax.plugin_version == false) {
        trev_csmap_subscription_popup();
        return false;
    }


    if (listing_page == '') {
        listing_page = '0';
    }
    else {
        listing_page = listing_page;
    }

    if (fb_page_select.length < 1) {

        if (pages.length < 1) {
            Swal.fire(
                'Oho!',
                'No Facebook page is configured. Please connect a Facebook page in the settings.',
                'error'
            )
        }
        else {
            Swal.fire(
                'Oho!',
                'Please select a page!',
                'error'
            )
        }
        return false;
    }
    Swal.fire({
        //  title: 'Schedule: '+title,
        html: `<div class="map_share_window"> <div class="form-group">
        <label for="meeting-time">Choose a time for your schedule</label>
        <input type="input" id="sc_datetime" value="`+ currentDateTime + `">
        </div>
        </div>
        <div class="action_button_area">
        </div>
        <style>
        </style>`,

        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Confirm Schedule',
        showCloseButton: true,
        cancelButtonText: 'Back',
        focusConfirm: false,
        showClass: {
            popup: ''
        },
        hideClass: {
            popup: ''
        },
        customClass: {
            container: 'confirm-schedule-popup',
        },

        preConfirm: () => {
            const sc_datetime = Swal.getPopup().querySelector('#sc_datetime').value

            if (!sc_datetime) {
                Swal.showValidationMessage(`Please Select date & time`)
            }
            return { sc_datetime: sc_datetime }
        }
    }).then((result) => {

        if (result.isConfirmed) {

            var fd = new FormData();


            fd.append("action", 'trev_csmap_add_schedule');
            fd.append("title", title);

            fd.append("desc", desc);
            fd.append("share_desc", share_desc);

            fd.append("id", post_id);
            fd.append("post_type", post_type);

            fd.append("page_id", fb_page_select);
            fd.append("datetime", result.value.sc_datetime);

            fd.append("platform", platform);

            fd.append("listing_page", listing_page);
            fd.append("security", revoiceDataAjax.ajax_nonce);

            Swal.fire({
                text: 'Adding Schedule....',
                showConfirmButton: false,
                timerProgressBar: true,
            })
            $.ajax({
                type: "POST",
                url: ajaxurl,
                processData: false,
                contentType: false,
                data: fd,

                success: function (response) {
                    Swal.fire(
                        'Great!',
                        'Schedule Successfully Added!',
                        'success'
                    ).then((result) => {
                        location.reload();
                    });
                }
            });
        }
        else {

            revoice_scheduled_id = jQuery(this).attr('data-schedule-id');

            if (post_type == 'listing' || post_type == 'listings' || post_type == 'property') {
                listing_detail['bedroom'] = listing_detail_back.bedroom;
                listing_detail['bathroom'] = listing_detail_back.bathroom;
                listing_detail['bedroom_emoji'] = listing_detail_back.bedroom_emoji;
                listing_detail['bathroom_emoji'] = listing_detail_back.bathroom_emoji;
                listing_detail['garage'] = listing_detail_back.garage;
                listing_detail['garage_emoji'] = listing_detail_back.garage_emoji;
                listing_detail['listing_link'] = listing_detail_back.listing_link;
                listing_detail['map_address'] = listing_detail_back.map_address;
                listing_detail['price'] = listing_detail_back.price;

                schedule_share_post_popup(desc, title, image, post_id, revoice_scheduled_id, listing_detail);
            }
            else {
                schedule_share_post_popup(desc, title, image, post_id, revoice_scheduled_id);
            }
        }
    });
    var todaysDate = new Date().toISOString().substring(0, 16);
});


/* Add Schedule Article*/
function trev_add_schedule_article(desc,title,image,categories,content,post_excerpt) {
    var $ = jQuery;
    var minutesToAdd = 0.3;
    var currentdate = new Date();
    var time = currentdate.getHours() + ":" + (currentdate.getMinutes() + 3);
    var day = (currentdate.getDate() < 10 ? '0' : '') + currentdate.getDate();
    var month = (currentdate.getMonth() < 10 ? '0' : '') + (currentdate.getMonth() + 1);
    var currentDateTime = currentdate.getFullYear() + '-' + (month) + '-' + day + ' ' + time;

    if (revoiceDataAjax.plugin_version == false) {
        trev_csmap_subscription_popup();
        return false;
    }


    Swal.fire({
        //  title: 'Schedule: '+title,
        html: `<div class="map_share_window"> <div class="form-group">
        <label for="meeting-time">Choose a time for your schedule</label>
        <input type="input" id="sc_datetime" value="`+ currentDateTime + `">
        </div>
        </div>
        <div class="action_button_area">
        </div>
        <style>
        </style>`,

        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: 'Confirm Schedule',
        showCloseButton: true,
        cancelButtonText: 'Back',
        focusConfirm: false,
        showClass: {
            popup: ''
        },
        hideClass: {
            popup: ''
        },
        customClass: {
            container: 'confirm-schedule-popup',
        },

        preConfirm: () => {
            const sc_datetime = Swal.getPopup().querySelector('#sc_datetime').value

            if (!sc_datetime) {
                Swal.showValidationMessage(`Please Select date & time`)
            }
            return { sc_datetime: sc_datetime }
        }
    }).then((result) => {

       if (result.isConfirmed) {

            var fd = new FormData();


            fd.append("action", 'trev_csmap_schedule_article');
            fd.append("categories", categories);
            fd.append("title", title);
            fd.append("content", content);
            fd.append("post_excerpt", post_excerpt);
            fd.append("image", image);
            fd.append("datetime", result.value.sc_datetime);
            fd.append("security", revoiceDataAjax.ajax_nonce);

            Swal.fire({
                text: 'Adding Schedule....',
                showConfirmButton: false,
                timerProgressBar: true,
            })
            $.ajax({
                type: "POST",
                url: ajaxurl,
                processData: false,
                contentType: false,
                data: fd,

                success: function (response) {
                    Swal.fire(
                        'Great!',
                        'Schedule Successfully Added!',
                        'success'
                    ).then((result) => {
                        location.reload();
                    });
                }
            });
      
    }else{
        jQuery('.add_blog_button').trigger('click');
    } 
   });
   
}

jQuery('body').on("click",".add-schedule-article",function(e){
    var add_flag = jQuery(this).data('add-from');

    if (add_flag == 'preivew') {
        var categories   = library_category;
        var content      = library_content;
        var post_excerpt = desc;
    }

    var desc_exerpt = jQuery(this).data('desc');
    var title = jQuery(this).data('title');
    var image = jQuery(this).data('image');    

    trev_add_schedule_article(desc_exerpt,title,image,categories,content,desc_exerpt);
});

var page_title  = 'Select Your Page';
var delet_btn   = '';
var revoice_scheduled_id = '';

jQuery('.map_share_btn').on('click', function () {

    fb_page_select = [];

    var listing_detail = {};
    var wind_height = jQuery(window).height() - 80;

    jQuery('body').addClass('open-overlay');

    desc = jQuery(this).attr('data-desc');
    title = jQuery(this).attr('data-title');
    image = jQuery(this).attr('data-image');
    id = jQuery(this).attr('data-id');
    var post_type = jQuery(this).data('post-type');

    revoice_scheduled_id = jQuery(this).attr('data-schedule-id');

    if (post_type == 'listing' || post_type == 'listings' || post_type == 'property') {
        listing_detail['bedroom'] = jQuery(this).data('bedroom');
        listing_detail['bathroom'] = jQuery(this).data('bathroom');
        listing_detail['garage'] = jQuery(this).data('garage');
        listing_detail['link'] = jQuery(this).data('listing-link');
        listing_detail['address'] = jQuery(this).data('address');
        listing_detail['bedroom_emoji'] = jQuery(this).data('bedroom-emoji');
        listing_detail['bathroom_emoji'] = jQuery(this).data('bathroom-emoji');
        listing_detail['garage_emoji'] = jQuery(this).data('garage-emoji');
        listing_detail = jQuery(this).data('listing-details');
       
        schedule_share_post_popup(desc, title, image, id, revoice_scheduled_id, listing_detail);
    }
    else {
        schedule_share_post_popup(desc, title, image, id, revoice_scheduled_id);
    }
});


function instant_share_post(desc, title, id, fb_auto_select, status) {

    var $ = jQuery;
    var fd = new FormData();
    fd.append("action", 'trev_csmap_share_now');
    fd.append("title", title);
    fd.append("desc", desc);
    fd.append("id", id);
    fd.append("status", status);
    fd.append("page_id", fb_auto_select);
    fd.append("security", revoiceDataAjax.ajax_nonce);

    if (fb_auto_select.length < 1) {
        Swal.fire(
            'Oho!',
            'No Facebook page is configured. Please connect a Facebook page in the settings.',
            'error'
        )
        return false;
    }

    Swal.fire({
        text: 'Please wait sharing...',
        showConfirmButton: false,
        timerProgressBar: true,
    })

    $.ajax({
        type: "POST",
        url: ajaxurl,
        processData: false,
        contentType: false,
        data: fd,
        success: function (response) {
            if (response.status == 'error') {
                Swal.fire(
                    'Oho!',
                    response.msg,
                    'error'
                )
            }
            else {
                Swal.fire(
                    'Great!',
                    'Successfully Shared!',
                    'success'
                )
            }
        }
    });
}

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

function schedule_share_post_popup(desc, title, image, id, revoice_scheduled_id = '', listing_detail = '') {
 if(fb_page_select.length == 0){
        fb_page_load.forEach(function (item) {

         fb_page_select.push(item.id);
    });
 
 }
       
                
    var listing_detail_obj = {};
    var pluginName = revoiceDataAjax.plugin_title;
    var statusVersion = revoiceDataAjax.plugin_version;
    var hostname = window.location.hostname;
    var pages = revoiceDataAjax.pages;
    var sharable_desc = '';
    var list_data = '';
    var list_link = '';
    var address = '';
    var listing_detail_show = '';
    var post_type = jQuery('.map_share_btn').attr('data-post-type');
    var price = '';
    var sharable_price = '';
    var nullSpace = '';
    var nullSpace1 = '';
    var bedroom_emoji = '';
    var bedroom = '';
    var bathroom_emoji = '';
    var bathroom = '';
    var garage_emoji = '';
    var garage = '';
    var agentName = '';
    var agentPhone = '';
    var agentDetails = '';
    var sharable_agent_detail = '';



    if (typeof post_type == 'undefined' || post_type == '') {
        post_type = 'post';
    }


    if (revoiceDataAjax.is_listing_tab && revoiceDataAjax.plugin_version == false && ( post_type == 'listing' || post_type == 'listings' || post_type == 'property' ) ) {
        trev_csmap_subscription_popup();
        return false;
    }

    if (listing_detail.price == "" || typeof listing_detail.price != 'undefined') {
        price = `<div class='listing-price'>` + listing_detail.price + `</div>`;
        sharable_price = listing_detail.price;
    }
    if (typeof listing_detail.bedroom_emoji != 'undefined') {
        bedroom_emoji = listing_detail.bedroom_emoji;
        bedroom = listing_detail.bedroom;
        nullSpace1 = '&nbsp; &nbsp;';
    }

    if (typeof listing_detail.bathroom_emoji != 'undefined') {
        bathroom_emoji = listing_detail.bathroom_emoji;
        bathroom = listing_detail.bathroom;
        nullSpace = '&nbsp;';
    }

    if (typeof listing_detail.garage_emoji != 'undefined') {
        garage = listing_detail.garage;
        garage_emoji = listing_detail.garage_emoji;
    }

    if ((bedroom && bathroom && garage) || (bathroom && garage)) {
        nullSpace = '&nbsp; &nbsp;';
    }
    if (listing_detail.phone) {
        agentDetails = `<div class='agent_details'>For more information call ` + listing_detail.name + ' on ' + listing_detail.phone + ` </div>`;
        sharable_agent_detail = 'For more information call ' + listing_detail.name + ' on ' + listing_detail.phone;
    }

    listing_detail_obj['bedroom'] = listing_detail.bedroom;
    listing_detail_obj['bedroom_emoji'] = listing_detail.bedroom_emoji;
    listing_detail_obj['bathroom'] = listing_detail.bathroom;
    listing_detail_obj['bathroom_emoji'] = listing_detail.bathroom_emoji;
    listing_detail_obj['garage'] = listing_detail.garage;
    listing_detail_obj['garage_emoji'] = listing_detail.garage_emoji;
    listing_detail_obj['map_address'] = listing_detail.map_address;
    listing_detail_obj['listing_link'] = listing_detail.listing_link;
    listing_detail_obj['price'] = listing_detail.price;


    var listing_detail_back = JSON.stringify(listing_detail_obj);

    if (listing_detail) {

        list_data = bedroom + ' ' + bedroom_emoji + nullSpace1 + bathroom + ' ' + bathroom_emoji + nullSpace + garage + ' ' + garage_emoji;
        list_link = listing_detail.listing_link;

        address = listing_detail.map_address;
        sharable_desc = title + '\r\n\n' + list_data + '\r\n\n' + sharable_price + '\r\n\n' + address + '\r\n\n' + list_link;
        listing_detail_show = `<div class="listing-details-wrap">
            <div class='listing-title'>`+ title + `</div>
            <div class='listing-details'>`+ list_data + `</div>
            `+ price + `
            <div class='listing-address'>`+ address + `</div>
            <a target="_blank" href=`+ list_link + ` class='listing-link'>` + list_link + `</a>
        </div>`;
    }

    var pages_html = '';
    var page_title = '';
    var fb_link    = '';

    fb_page_load.forEach(function (item) {
        var checked = '';
        if (fb_page_select.includes(item.id)) {
            var checked = "checked='checked'";
        }
        pages_html = pages_html + '<div class="checkbox-wrap"><input type="checkbox" id="' + 'page_' + item.id + '" name="fb_pages[]" value="' + item.id + '" ' + checked + '><label for="' + 'page_' + item.id + '">' + item.name + '</label></div>';
    });

    if (fb_page_select.length > 0) {
        var disabled = '';
    }
    else {
        var disabled = 'disabled';
    }

    if (revoice_scheduled_id) {
        delet_btn = `<button type="button" data-id="` + revoice_scheduled_id + `" class="swal2-confirm button-primary delete_schedule swal2-styled delete_db_btn" aria-label="" style="display: inline-block;">Delete</button>`;
    }
    else {
        delet_btn = '';
    }


    if (fb_page_load.length > 0) {
        page_title = 'Select Your Page';
    }
    else {

        page_title = '<p class="no_fb_conn">No Facebook page is selected</p>';
        fb_link = '<a class="fb_conn_link" href="' + revoiceDataAjax.fb_connect_link + '">Click here to connect</a>';
    }

    if (statusVersion || post_type == 'post' || (revoiceDataAjax.current_page == 'trev-csmap-media-hub' && revoiceDataAjax.current_tab == 'main')) {
        
        var layout_version = `<div class="bottom-part-wrap"> <div class="post_select_page">
        <h5 class="page_title_popup">`+ page_title + `</h5>
        `+ fb_link + `

        `+ pages_html + `
        </div>
        <div class="action_button_area">
        <div class="left_side_btn">`+ delet_btn + `</div>
        <div class="right_side_btn">
        <button type="button" data-id="`+ id + `" class="swal2-confirm button-primary swal2-styled share_now_btn" style="display: inline-block;" ` + disabled + `>Share now</button>
        <button type="button" data-id="`+ id + `" data-post-type="` + post_type + `" data-listing-detail='` + listing_detail_back + `' class="swal2-confirm button-primary swal2-styled schedule_now" aria-label="" style="display: inline-block;" ` + disabled + `>Schedule</button>
        </div>
        </div></div>`;
    }
    else {
        var layout_version = ` <div class="post_select_page">
        <h5 class="page_title_popup">Subscribe to share listings</h5>
        </div>
        <div class="action_button_area">
        <div class="left_side_btn">
        <button type="button"  class="swal2-confirm button-primary swal2-styled subscribe_now" style="display: inline-block;" >Subscribe</button>
        </div>
        <div class="right_side_btn">

        <button type="button" class="swal2-confirm button-primary swal2-styled close-swal" style="display: inline-block;" aria-label="">Cancle</button>  
        </div>
        </div>`;
    }

    Swal.fire({
        title: 'Share to Facebook',
        html: `<div class="map_share_window">
        <div>`+ listing_detail_show + `</div>
        <div class="form-group">
        <input type="hidden" id="sharable_desc" value="`+ sharable_desc + `">
        <input type="hidden" id="sharable_agent" value="`+ sharable_agent_detail + `">

        <textarea class="form-control" id="share_desc" rows="4">`+ desc + `</textarea>
        </div>
        `+ agentDetails + `
        <p class="published-by"><span>Published by </span>`+ pluginName + `</p>
        <div class="post_image">
        <img src="`+ image + `">
        </div>
        <div class="preview-content">
        <div class="hostname">`+ hostname + `</div>
        <div class="post_title_area"><h2>`+ title + `</h2></div>
        <div class="post_desc_area">`+ desc + `</div>
        </div>
        </div>

        `+ layout_version + `
        <style>
        </style>`,
        showConfirmButton: false,
        showCloseButton: true,
        focusConfirm: false,
        animation: true,
        showClass: {
            popup: ''
        },
        hideClass: {
            popup: ''
        },
        customClass: {
            container: post_type,
        },
        preConfirm: () => {
            const login = Swal.getPopup().querySelector('#login').value
            const password = Swal.getPopup().querySelector('#password').value
            if (!login || !password) {
                Swal.showValidationMessage(`Please enter login and password`)
            }
            return { login: login, password: password }
        }
    }).then((result) => {

    })
}

jQuery(document).on('click', '.delete_schedule', function () {
    var $ = jQuery;
    var id = jQuery(this).attr('data-id');

    Swal.fire({
        title: 'Are you sure?',
        text: "Are you sure you want to remove this share?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes!'
    }).then((result) => {

        if (result.isConfirmed) {
            var fd = new FormData();
            fd.append("action", 'trev_csmap_remove_schedule');
            fd.append("id", id);
            fd.append("security", revoiceDataAjax.ajax_nonce);
            Swal.fire({
                text: 'Removing Schedule....',
                showConfirmButton: false,
                timerProgressBar: true,
            })

            $.ajax({
                type: "POST",
                url: ajaxurl,
                processData: false,
                contentType: false,
                data: fd,

                success: function (response) {

                    Swal.fire(
                        'Great!',
                        'Schedule Successfully Removed!',
                        'success'
                    ).then(() => {
                        location.reload();
                    });
                }
            });
        }
    })
});


jQuery('.edit_schedule').click(function () {
    fb_page_select = [];
    var schedule_id = jQuery(this).data('id');
    var schedule_title = jQuery(this).data('event');
    var schedule_dateTime = jQuery(this).data('event_time');
    var schedule_image = jQuery(this).data('image');
    var schedule_idesc = jQuery(this).data('desc');
    var schedule_pageids = jQuery(this).data('pageid');
    var current_page = jQuery(this).data('content-page');
    var parent_content = jQuery(this).data('parent-content');
    var page_listing = jQuery(this).data('paglisting');
    var plugin_status = jQuery(this).data('plugin-status');
    var dateTimeLable = '<span class="dt-label">Select Date & Time:</span>';
    var pages_string = revoiceDataAjax.pages;
    var pluginName = revoiceDataAjax.plugin_title;
    var statusVersion = revoiceDataAjax.plugin_version;
    var hostname = window.location.hostname;

    var listing_detail = [];
    var wind_height = jQuery(window).height() - 80;

    jQuery('body').addClass('open-overlay');

    desc  = jQuery(this).attr('data-desc');
    title = jQuery(this).attr('data-title');
    image = jQuery(this).attr('data-image');
    id = jQuery(this).attr('data-id');
    var post_type = jQuery(this).data('post-type');
    var price = '';
    var sharable_price = '';
    var nullSpace = '';
    var bedroom_emoji = '';
    var bedroom = '';
    var bathroom_emoji = '';
    var bathroom = '';
    var garage_emoji = '';
    var garage = '';
    var agentDetails = '';
    var sharable_agent_detail = '';

    if (typeof post_type == 'undefined' || post_type == '') {
        post_type = 'post';
    }

    if (revoiceDataAjax.is_listing_tab && revoiceDataAjax.plugin_version == false && ( post_type == 'listing' || post_type == 'property') ) {
        trev_csmap_subscription_popup();
        return false;
    }

    if (revoiceDataAjax.plugin_version == false && post_type == 'undefine' && page_listing == 0) {
        trev_csmap_subscription_popup();
        return false;
    }

    if (plugin_status == false) {
        trev_csmap_subscription_popup();
        return false;
    }

    if (post_type == 'listing' || post_type == 'property' || post_type == 'listings' ) {
        listing_detail = jQuery(this).data('listing-details');
    }

    if (typeof listing_detail.price != 'undefined') {
        price = `<div class='listing-price'>` + listing_detail.price + `</div>`;
        sharable_price = listing_detail.price;
    }

    if (typeof listing_detail.bedroom_emoji != 'undefined') {
        bedroom_emoji = listing_detail.bedroom_emoji;
        bedroom = listing_detail.bedroom;
        nullSpace = '&nbsp; &nbsp;';
    }

    if (typeof listing_detail.bathroom_emoji != 'undefined') {
        bathroom_emoji = listing_detail.bathroom_emoji;
        bathroom = listing_detail.bathroom;
    }

    if (typeof listing_detail.garage_emoji != 'undefined') {
        garage = listing_detail.garage;
        garage_emoji = listing_detail.garage_emoji;
    }

    if (listing_detail.phone) {
        agentDetails = `<div class='agent_details'>For more information call ` + listing_detail.name + ' on ' + listing_detail.phone + ` </div>`;
        sharable_agent_detail = 'For more information call ' + listing_detail.name + ' on ' + listing_detail.phone;
    }

    var str = String(schedule_pageids);
    var pages_html = '';
    var checked = '';
    var sharable_desc = '';
    var list_data = '';
    var list_link = '';
    var address = '';
    var listing_detail_show = '';

    if (post_type == 'listing' || post_type == 'property' || post_type == 'listings') {
        if (listing_detail) {
            list_data = bedroom + ' ' + bedroom_emoji + nullSpace + bathroom + ' ' + bathroom_emoji + nullSpace + garage + ' ' + garage_emoji;
            list_link = listing_detail.listing_link;
            address = listing_detail.map_address;
            sharable_desc = schedule_title + '\r\n\n' + list_data + '\r\n\n' + sharable_price + '\r\n\n' + address + '\r\n\n' + list_link;
            listing_detail_show = `<div class="listing-details-wrap">
            <div class='listing-title'>`+ schedule_title + `</div>
            <div class='listing-details'>`+ list_data + `</div>
            `+ price + `
            <div class='listing-address'>`+ address + `</div>
            <a target="_blank" href=`+ list_link + ` class='listing-link'>` + list_link + `</a>

            </div>`;
        }
    }


    pages_string.forEach(function (item) {

        if (str.includes(item.id)) {
            var checked = "checked='checked'";
            fb_page_select.push(item.id);
        }
        pages_html = pages_html + '<div class="checkbox-wrap"><input type="checkbox" id="' + 'page_' + item.id + '" name="fb_pages[]" value="' + item.id + '" ' + checked + '><label for="' + 'page_' + item.id + '">' + item.name + '</label></div>';
    });

    if (fb_page_select.length > 0) {
        var disabled = 'disabled';
    }
    else {
        var disabled = '';
    }


    if (current_page == 'content') {
        var post_id = jQuery(this).data('post-id');
        var share_now_btn = `<button type="button" data-id="` + post_id + `" class="swal2-confirm button-primary swal2-styled share_now_btn" aria-label="" style="display: inline-block;" "` + disabled + `">Share now</button>`;
    }

    if (schedule_id) {
        delet_btn = `<button type="button" data-id="` + schedule_id + `" class="swal2-confirm button-primary delete_schedule swal2-styled delete_db_btn" aria-label="" style="display: inline-block;">Delete</button>`;
    }
    else {
        delet_btn = '';
    }


    if (statusVersion || post_type == 'post') {
        var layout_version = ` <div class="bottom-part-wrap"><div class="post_select_page">
        <h5 class="page_title_popup">`+ page_title + `</h5>

        `+ pages_html + `
        </div>
        <div class="date-time" id="date_time">
        `+ dateTimeLable + `<input type="input" id="sc_datetime" value="` + schedule_dateTime + `" readonly>
        </div>
        <div class="action_button_area">
        <div class="left_side_btn">`+ delet_btn + `</div>
        <div class="right_side_btn">
        <button type="button" data-id="`+ schedule_id + `" class="swal2-confirm button-primary swal2-styled update_schedule" aria-label="" style="display: inline-block;" "` + disabled + `">Update Schedule</button>

        </div>
        </div></div>`;
    }

    else if (statusVersion || post_type == 'undefined' && page_listing == 1) {

        var layout_version = `<div class="post_select_page">
        <h5 class="page_title_popup">`+ page_title + `</h5></div>
        <div class="date-time" id="date_time">
        `+ dateTimeLable + `<input type="input" id="sc_datetime" value="` + schedule_dateTime + `" readonly>
        </div>
        <div class="action_button_area">
        <div class="left_side_btn">`+ delet_btn + `</div>
        <div class="right_side_btn">
        <button type="button" data-id="`+ schedule_id + `" class="swal2-confirm button-primary swal2-styled update_schedule" aria-label="" style="display: inline-block;" "` + disabled + `">Update Schedule</button>

        </div>
        </div>`;
    }
    else {
        var layout_version = ` <div class="post_select_page">
        <h5 class="page_title_popup">Subscribe to share listings</h5>
        </div>
        <div class="action_button_area">
        <div class="left_side_btn">
        <button type="button"  class="swal2-confirm button-primary swal2-styled subscribe_now" style="display: inline-block;" >Subscribe</button>
        </div>
        <div class="right_side_btn">

        <button type="button" class="swal2-confirm button-primary  swal2-styled close-swal" style="display: inline-block;" aria-label="">Cancle</button>  
        </div>
        </div>`;
    }

    Swal.fire({
        title: 'Edit Schedule',
        html: `<div class="map_share_window edit-schedule">
        <div>`+ listing_detail_show + `</div>
        <div class="form-group">
        <input type="hidden" id="sharable_desc" value="`+ sharable_desc + `">
        <input type="hidden" id="sharable_agent" value="`+ sharable_agent_detail + `">
        <textarea class="form-control" id="schedule_title" rows="4">`+ schedule_idesc + `</textarea>
        </div>
        <p class="published-by"><span>Published by </span>`+ pluginName + `</p>
        `+ agentDetails + `
        <div class="post_image">
        <img src="`+ schedule_image + `">
        </div>
        <div class="preview-content">
        <div class="hostname">`+ hostname + `</div>
        <div class="post_title_area"><h2>`+ schedule_title + `</h2></div>
        <div class="post_desc_area">`+ parent_content + `</div>
        </div>
        </div>
        `+ layout_version + `

        <style> </style>`,
        showConfirmButton: false,
        showCloseButton: true,
        showClass: {
            popup: ''
        },
        hideClass: {
            popup: ''
        },
        customClass: {
            container: post_type,
        },
        focusConfirm: false
    });
});

//Updated schedule event
jQuery(document).on('click', '.update_schedule', function () {

    var $ = jQuery;
    var desc = jQuery('#schedule_title').val();
    var sharable_agent = jQuery('#sharable_agent').val();
    var datetime = jQuery(' #sc_datetime').val();
    var share_desc = jQuery('#sharable_desc').val();
    var id = jQuery(this).data('id');
    var fd = new FormData();

    fd.append("action", 'trev_csmap_schedule_update');
    fd.append("desc", desc);
    fd.append("share_desc", share_desc);
    fd.append("sharable_agent", sharable_agent);
    fd.append("id", id);
    fd.append("datetime", datetime);
    fd.append("page_id", fb_page_select);
    fd.append("security", revoiceDataAjax.ajax_nonce);
    if (datetime == '' || datetime == null) {
        Swal.fire(
            'Oho!',
            'Please select date & time!',
            'error'
        )
        return false;
    }
    if (desc == '' || desc == null) {
        Swal.fire(
            'Oho!',
            'Please add description',
            'error'
        )
        return false;
    }
    if (fb_page_select.length < 1) {
        Swal.fire(
            'Oho!',
            'Please select minimum one facebook page',
            'error'
        )
        return false;
    }

    Swal.fire({
        text: 'Please wait, Updating the post...',
        showConfirmButton: false,
        timerProgressBar: true,
    })

    $.ajax({
        type: "POST",
        url: ajaxurl,
        processData: false,
        contentType: false,
        data: fd,
        success: function (response) {
            if (response.response == 'success') {
                Swal.fire(
                    'Great!',
                    'Schedule Successfully Updated!',
                    'success'
                ).then((result) => {
                    location.reload();
                });
            }
            else {
                Swal.fire(
                    'Fail',
                    'Something went wrong! ' + response.response,
                    'error'
                ).then((result) => {
                    location.reload();
                });
            }
        }
    });
});

jQuery('body').on('change', '#selected_page', function () {
    selected_page = jQuery(this).val();
});

jQuery('body').on('click', '.share_now_btn', function () {
    var $ = jQuery;
    var desc = jQuery('#share_desc').val();
    var share_desc = jQuery('#sharable_desc').val();
    var sharable_agent = jQuery('#sharable_agent').val();
    desc = share_desc + '\r\n\n' + desc + '\r\n\n' + sharable_agent;
    var id = jQuery(this).data('id');
    var status = 'Shared';
    instant_share_post(desc, title, id, fb_page_select, status);
});

jQuery('.facebook_connect_remove').on('click', function () {
    var $ = jQuery;
    var id = $(this).attr('data-id');
    var fd = new FormData();

    fd.append("action", 'trev_csmap_remove_page');
    fd.append("id", id);
    fd.append("security", revoiceDataAjax.ajax_nonce);

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result) {
            if (result.isConfirmed) {
                Swal.fire({
                    text: 'Please wait....',
                    showConfirmButton: false,
                    timerProgressBar: true,
                })

                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    processData: false,
                    contentType: false,
                    data: fd,
                    success: function (response) {
                        Swal.fire(
                            'Great!',
                            'Page Successfully Removed!',
                            'success'
                        );
                        location.reload();
                    }
                });
            }
        }
    })
});


// open up an Article preview popup 
jQuery('.content-library-list .map_image, .content-library-list .map_title').click(function () {

    var libraryCat = '';

    library_category = jQuery(this).parent().parents('.blog_item').find('.map_categories').text();
    var title = jQuery(this).parent().parents('.blog_item').find('.map_title').text();
    library_content = jQuery(this).parent().parents('.blog_item').find('.map_content').html();
    var post_excerpt = jQuery(this).parent().parents('.blog_item').find('.library-excerpt').html();
    
    var image = jQuery(this).parent().parents('.blog_item').find('.map_image').attr('src');
    var json = jQuery.parseJSON(library_category);

    for (var i = 0; i < json.length; i++) {
        libraryCat = libraryCat + '<span>' + json[i] + '</span>';
    }
    var schedule_btn = '<button data-image="' + image + '" data-desc="' + post_excerpt + '" type="button"  class="add-schedule-article button button-secondary"   data-title="' + title + '"  data-add-from="preivew"   " aria-label="" style="display: inline-block;" >Add Schedule</button>';
    var cancel_btn   = '<button type="button"  class="button button-secondary close-swal " aria-label="Close this dialog" style="display: inline-block;" >Cancel</button>';

    var second_btn = revoiceDataAjax.plugin_version ? schedule_btn:cancel_btn;

    Swal.fire({
        html: '<div class="map_share_window article-library-preview">' +
            '<div class="preview-content-wrap">' +
            '<div class="library-post-title">' + title + '</div>' +
            '<div class="library-categories">' + libraryCat + '</div>' +
            '<div class="post_image">' +
            '<img src="' + image + '">' +
            '</div>' +
            '<div class="library-preview-content">' +
            '<div class="post_desc_area" id="library_content">' + library_content + '</div>' +
            '<div class="library-categories" style="display:none">' + library_category + '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="library-preview-buttons">' +
            '<button type="button"  class="button button-primary add_blog_button" data-image="' + image + '" data-title="' + title + '" data-desc="' + post_excerpt + '" data-add-from="preivew"   aria-label="" style="display: inline-block;" > Add now</button>' 
            + second_btn +
            '</div>' +

            '<style>' +
            '</style>',
        showConfirmButton: false,
        showCloseButton: true,
        focusConfirm: false,
        animation: true,
        showClass: {
            popup: ''
        },
        hideClass: {
            popup: ''
        },
        customClass: {
            
            container: 'library-content-preview-popup',
        },
    });
});

jQuery(document).on('click', '.close-swal', function () {
    swal.close();
});

jQuery('.facebook_connect_removes').on('click', function () {
    var $ = jQuery;
    var ids = $(this).attr('data-ids');
    var fd = new FormData();

    fd.append("action", 'trev_csmap_remove_pages');
    fd.append("ids", ids);
    fd.append("security", revoiceDataAjax.ajax_nonce);

    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Disconnect it!'
    }).then((result) => {
        if (result) {
            if (result.isConfirmed) {
                Swal.fire({
                    text: 'Please wait....',
                    showConfirmButton: false,
                    timerProgressBar: true,
                })
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    processData: false,
                    contentType: false,
                    data: fd,
                    success: function (response) {
                        if (response.code == 200 && response.message == 'OK') {
                            Swal.fire(
                                'Great!',
                                'Facebook Successfully Disconnected!',
                                'success'
                            );
                        }
                        location.reload();
                    }
                });
            }
        }
    })
});

jQuery(document).on('click', '.swal2-container', function () {
    jQuery('body').removeClass('open-overlay');
});

jQuery(document).on('click', '.subscribe_now', function () {
    var redirect_link = revoiceDataAjax.subscribe_link;
    window.open(redirect_link, '_blank');
});
