"use strict";
var insta_detail = '';
var insta_page_select = [];
var desc = '';
var title = '';
var image = '';
var date='';

jQuery(document).ready(function ($) {

	jQuery('.remove-brand').click(function () {
		var brand_logo = jQuery(this).data('logo-id');

		jQuery.ajax({
			url: revoiceInstaAjax.ajaxurl,
			method: 'POST',
			dataType: "json",
			data: {
				'action': 'trev_csmap_remove_brand_logo',
				'brand_logo': brand_logo,
				'insta_security':revoiceInstaAjax.ajax_nonce
			},
			success: function (response) {

				if (response.result == 'success') {
					Swal.fire(
						'Great!',
						'Brand logo is removed successfully!',
						'success'
					).then((result) => {
						location.reload();
					});
				} else {
					Swal.fire(
						'',
						'Could not removed the logo',
						'error'
					).then((result) => {
						location.reload();
					});
				}

			}


		});
	});


	jQuery(document).on('click', '.post_select_page input', function () {

		if (jQuery(this).is(":checked")) {
			insta_page_select.push(jQuery(this).val());
		} else {
			insta_page_select.splice(jQuery.inArray(jQuery(this).val(), insta_page_select), 1);
		}

		if (insta_page_select.length > 0) {
			jQuery('.insta_share_now').attr('disabled', false);
			jQuery('.schedule_insta').attr('disabled', false);
			jQuery('.edit_schedule_insta').attr('disabled', false);
		} else {
			jQuery('.insta_share_now').attr('disabled', true);
			jQuery('.schedule_insta').attr('disabled', true);
			jQuery('.edit_schedule_insta').attr('disabled', true);
		}

	});


	// on upload button click
	$('body').on('click', '#instagram-logo', function (e) {
		e.preventDefault();
		var button = $(this),
			custom_uploader = wp.media({
				title: 'Insert image',
				library: {
					type: 'image'
				},
				button: {
					text: 'Use this image'
				},
				multiple: false
			}).on('select', function () {
				var attachment = custom_uploader.state().get('selection').first().toJSON();
				$('.placeholder-image').html('<img src="' + attachment.url + '">');
				$('#instagram_brand_logo').val(attachment.id);
			}).open();
	});


	jQuery(document).on('click', '.insta_share_btn', function (e) {
		insta_page_select = [];

		if(revoiceInstaAjax.get_insta_data.length > 0){

			Array.from(revoiceInstaAjax.get_insta_data).forEach(function (item) {
				    insta_page_select.push(item.id);
		    });

		}
		    
		var listing_detail = '';
		var wind_height    = jQuery(window).height() - 80;
		jQuery('body').addClass('open-overlay'); //open popup

		desc = jQuery(this).attr('data-desc');		// Post description
		title = jQuery(this).attr('data-title');	// Post Title
		image = jQuery(this).attr('data-image');	// Post Image
		date = jQuery(this).attr('data-date');	    // Post Date

		var description = desc + '\r\n\n' + revoiceInstaAjax.instagram_hashtags;
		schedule_share_post_popup(description, title, image, date);
	});


	jQuery(document).on('click', '.schedule_insta', function (e) {

		// ======================= //

		e.preventDefault();

		var listing_detail = [];
		var time = '';
		var display_date = '';
		var $ = jQuery;
		var post_id 	 = jQuery(this).attr('data-id');
		var listing_page = revoiceInstaAjax.is_holidays_tab;
		var post_type	 = jQuery(this).attr('data-post-type');
		insta_detail 	 = jQuery(this).attr('data-insta-detail');
		var date 	     = jQuery(this).attr('data-date');
		var share_desc   = jQuery('#share_desc').val();
		var currentdate  = new Date();
		var platform     = 'instagram';
		var day = (currentdate.getDate() < 10 ? '0' : '') + currentdate.getDate();
		var month = (currentdate.getMonth() < 10 ? '0' : '') + (currentdate.getMonth() + 1);

		if (date == '') {
			display_date = currentdate.getFullYear() + '-' + (month) + '-' + day;
			time = currentdate.getHours() + ":" + (currentdate.getMinutes() + 3);
		} else {
			display_date = date;
			time = '06:00';
		}

		if (revoiceInstaAjax.plugin_version == false) {
			trev_csmap_subscription_popup();
			return false;
		}
		var currentDateTime = display_date + ' ' + time;

		var listing_detail_back = jQuery(this).data('listing-detail');

		if (listing_page == '') {
			listing_page = '0';
		}
		else {
			listing_page = listing_page;
		}
		Swal.fire({
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
				return {
					sc_datetime: sc_datetime
				}
			}
		}).then((result) => {

			if (result.isConfirmed) {

				var fd = new FormData();

				fd.append("action", 'trev_csmap_insta_add_schedule');
				fd.append("title", title);
				fd.append("desc", desc);
				fd.append("share_desc", share_desc);
				fd.append("id", post_id);
				fd.append("post_type", post_type);
				fd.append("datetime", result.value.sc_datetime);
				fd.append("platform", platform);
				fd.append("listing_page", listing_page);
				fd.append("image", image);
				fd.append("insta_ids", insta_page_select);
				fd.append("insta_security", revoiceInstaAjax.ajax_nonce);
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

			var	revoice_scheduled_id = jQuery(this).attr('data-schedule-id');


				schedule_share_post_popup(share_desc, title, image, post_id, revoice_scheduled_id);

			}
		});
		var todaysDate = new Date().toISOString().substring(0, 16);

		// ====================== //
	});


	jQuery('.insta_edit_schedule').click(function () {
		insta_page_select = [];
		var id 		  = jQuery(this).attr('data-id');
		var desc 	  = jQuery(this).attr('data-desc');
		var title 	  = jQuery(this).attr('data-title');
		var image 	  = jQuery(this).attr('data-image');
		var date 	  = jQuery(this).attr('data-event_time');
		var insta_ids = jQuery(this).attr('data-instaid');

		edit_schedule_share_post_popup(id, title, desc, image, date, insta_ids)
	});


	jQuery(document).on('click', '.delete_insta_schedule', function () {
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
				fd.append("action", 'trev_csmap_insta_remove_schedule');
				fd.append("id", id);
				fd.append("insta_security", revoiceInstaAjax.ajax_nonce);
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

	jQuery(document).on('click', '.edit_schedule_insta', function (e) {

		// ======================= //

		var $ = jQuery;
		var edit_desc = jQuery('#share_desc').val();
		var sc_datetime = jQuery('#sc_datetime').val();
		var post_id = jQuery(this).attr('data-id');

		e.preventDefault();

		var fd = new FormData();

		fd.append("action", 'trev_csmap_update_insta_add_schedule');
		fd.append("edit_desc", edit_desc);
		fd.append("post_id", post_id);
		fd.append("sc_datetime", sc_datetime);
		fd.append("insta_ids", insta_page_select);
		fd.append("insta_security", revoiceInstaAjax.ajax_nonce);
		Swal.fire({
			text: 'Updating Schedule....',
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
					'Schedule Successfully Updated!',
					'success'
				).then((result) => {
					location.reload();
				});
			}
		});


		// ====================== //
	});

	jQuery('.insta_connect_removes').on('click', function () {
		var $ = jQuery;
		var id = $(this).attr('data-id');
		var fd = new FormData();

		fd.append("action", 'trev_csmap_remove_insta_pages');
		fd.append("id", id);
		fd.append("insta_security", revoiceInstaAjax.ajax_nonce);

		Swal.fire({
			title: 'Are you sure?',
			text: "",
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
							Swal.fire(
								'Great!',
								'Instagram Successfully Disconnected!',
								'success'
							);
							location.reload();
						}
					});
				}
			}
		})
	});

	jQuery('.instagram_page_remove').on('click', function () {
		var $ = jQuery;
		var id = $(this).attr('data-id');
		var fd = new FormData();

		fd.append("action", 'trev_csmap_instagram_page_remove');
		fd.append("id", id);
		fd.append("insta_security", revoiceInstaAjax.ajax_nonce);
		Swal.fire({
			title: 'Are you sure?',
			text: "",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, remove it!'
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

	jQuery('.insta_connect_btn').on('click', function () {
		var $ = jQuery;

		var fd = new FormData();
		fd.append("action", 'trev_csmap_connect_insta_pages');
		fd.append("insta_security", revoiceInstaAjax.ajax_nonce);


		Swal.fire({
			text: 'Connecting wait....',
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

				if (response.status) {
					Swal.fire(
						'Great!',
						'Instagram Successfully Connected!',
						'success'
					).then((result) => {
						location.reload();
					});
				} else {
					Swal.fire(
						'Sorry!',
						revoiceInstaAjax.instagram_error_msg,
						'error'
					).then((result) => {
						location.reload();
					});
				}

				//location.reload();
			}
		});



	});

});

function edit_schedule_share_post_popup(id, title, desc, image, date, insta_ids) {

	if (!revoiceInstaAjax.plugin_version) {
		trev_csmap_subscription_popup();
		return false;
	}

	// Add Hashtags in the post description

	var brand_logo = revoiceInstaAjax.brand_logo;
	var instaDate = revoiceInstaAjax.get_insta_data;

	var pages_html = '';
	var page_title = '';
	var ins_link = '';
	for (var i = 0; i < instaDate.length; i++) {
		var displayname = instaDate[i].displayname;
		var insta_id = instaDate[i].id;
		var username = instaDate[i].username;
		var checked = '';
		if (insta_ids.includes(insta_id)) {
			var checked = "checked='checked'";
			insta_page_select.push(insta_id);
		}
		var pages_html = pages_html + '<div class="checkbox-wrap"><input type="checkbox" id="' + 'page_' + insta_id + '" name="insta_pages[]" value="' + insta_id + '" ' + checked + '><label for="' + 'page_' + insta_id + '">' + displayname + ' (' + username + ')</label></div>';
	}

	if (insta_page_select.length > 0) {
		jQuery('.edit_schedule_insta').attr('disabled', false);
	}
	else {
		jQuery('.edit_schedule_insta').attr('disabled', true);
	}


	if (instaDate.length > 0) {
		page_title = 'Select Your Page';
	}
	else {
		page_title = '<p class="no_insta_conn">No Instagram page is selected</p>';
		ins_link = '<a class="insta_conn_link" href="' + revoiceInstaAjax.instagram_connect_link + '">Click here to connect</a>';
	}

	// Popup Html
	var layout_version = `
		<div class=" instagram_share_window">
			<div class="instagram_share_window_wrap">
				<div class="instagram_window_right">
					<div class="form-group">
						<div class="Share_Caption">
							<h5 class="insta-caption-title">Edit share caption</h5>
							<textarea class="form-control" id="share_desc" rows="12">`+ desc + `</textarea>
							<input type="hidden" id="image_url" name="image_url" value="`+ image + `">
						</div>
						<div class="post_select_page insta_page_select">
						 <h5 class="page_title_popup">`+ page_title + `</h5>
						 `+ ins_link + `
						  `+ pages_html + `
						</div>
						<div class="Share_Date" id="date_time">
					        <span class="dt-label">Select Date &amp; Time:</span>
					        <input type="input" id="sc_datetime" value="`+ date + `">
				        </div>
					</div>
				</div>
				<div class="instagram_window_left">
					<div class="post_image">
						<img src="`+ image + `">
						<div class="post_image brand-logo">
							<img src="`+ brand_logo + `">
						</div>
					</div>
					
				</div>
			</div>
			<div class="action_button_area right_side_btn">
				<div class="action_left">
					<button type="button" data-id='`+ id + `' class="swal2-confirm button-primary swal2-styled delete_insta_schedule" style="display: inline-block;">Delete</button>
				</div>
				<div class="action_right">
					<button type="button" data-id='`+ id + `' data-date='` + date + `' data-insta-detail='` + desc + `' class="swal2-confirm button-primary swal2-styled edit_schedule_insta" >Update Schedule</button>
				</div>
			</div>
		</div>`;
	Swal.fire({
		title: 'Share to Instagram',
		html: layout_version + `
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
			container: 'instagram_popop_container',
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

function schedule_share_post_popup(desc, title, image) {

	if (!revoiceInstaAjax.is_holidays_tab && !revoiceInstaAjax.plugin_version) {

		trev_csmap_subscription_popup();
		return false;
	}

	// Add Hashtags in the post description

	var brand_logo = revoiceInstaAjax.brand_logo;
	var instaDate = revoiceInstaAjax.get_insta_data;

	var pages_html = '';
	var page_title = '';
	var ins_link = '';
	for (var i = 0; i < instaDate.length; i++) {
		var displayname = instaDate[i].displayname;
		var insta_id = instaDate[i].id;
		var username = instaDate[i].username;
		var checked = '';
		if (insta_page_select.includes(insta_id)) {
			var checked = "checked='checked'";
		}
		var pages_html = pages_html + '<div class="checkbox-wrap"><input type="checkbox" id="' + 'page_' + insta_id + '" name="insta_pages[]" value="' + insta_id + '" ' + checked + '><label for="' + 'page_' + insta_id + '">' + displayname + ' (' + username + ')</label></div>';
	}

	if (insta_page_select.length > 0) {
		var disabled = '';
	}
	else {
		var disabled = 'disabled';
	}

	if (jQuery('.checkbox-wrap').length > 0) {

	}

	if (instaDate.length > 0) {
		page_title = 'Select Your Page';
	}
	else {
		page_title = '<p class="no_insta_conn">No Instagram page is selected</p>';
		ins_link = '<a class="insta_conn_link" href="' + revoiceInstaAjax.instagram_connect_link + '">Click here to connect</a>';
	}



	// Popup Html
	var layout_version = `
		<div class=" instagram_share_window">
			<div class="instagram_share_window_wrap">
				<div class="instagram_window_right">
						<div class="form-group">
							<h5 class="insta-caption-title">Edit share caption</h5>
							<textarea class="form-control" id="share_desc" rows="12">`+ desc + `</textarea>
							<input type="hidden" id="image_url" name="image_url" value="`+ image + `">
						</div>
						<div class="post_select_page insta_page_select">
						 <h5 class="page_title_popup">`+ page_title + `</h5>
						 `+ ins_link + `
						  `+ pages_html + `
						</div>
						
				</div>
				<div class="instagram_window_left">
					<div class="post_image">
						<img src="`+ image + `">
						<div class="post_image brand-logo">
							<img src="`+ brand_logo + `">
						</div>
					</div>
					
				</div>
			</div>
			<div class="action_button_area right_side_btn">
				<div class="action_left">
					<button type="button"  class="swal2-confirm button-primary swal2-styled insta_share_now" style="display: inline-block;" `+ disabled + `>Share Now</button>
				</div>
				<div class="action_right">
					<button type="button" data-id="" data-date='`+ date + `' data-insta-detail='` + desc + `' class="swal2-confirm button-primary swal2-styled schedule_insta" style="display: inline-block;" aria-label="" ` + disabled + `>Schedule</button>  
				</div>
			</div>
		</div>`;
	Swal.fire({
		title: 'Share to Instagram',
		html: layout_version + `
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
			container: 'instagram_popop_container',
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

function trev_csmap_subscription_popup() {
	Swal.fire({
		title: revoiceInstaAjax.premium_message['premium_title'],
		html: revoiceInstaAjax.premium_message['premium_v_description'] + '<div class="revoice-footer-buttons"><a href="' + revoiceInstaAjax.premium_message['premium_v_link'] + '" class="revoice-button" target="_blank">' + revoiceInstaAjax.premium_message['premium_v_button'] + '</a></div>',
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

jQuery('body').on('click', '.insta_share_now', function () {

	var $ = jQuery;
	var desc = jQuery('#share_desc').val();
	var image_url = jQuery('#image_url').val();

	instant_share_insta_post(desc, image_url, insta_page_select);
});


function instant_share_insta_post(desc, image_url, insta_page_select) {

	var $ = jQuery;

	var fd = new FormData();

	fd.append("action", 'trev_csmap_insta_share_now');

	fd.append("desc", desc);
	fd.append("image_url", image_url);
	fd.append("insta_ids", insta_page_select);
	fd.append("insta_security", revoiceInstaAjax.ajax_nonce);

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
			if (response) {
				var obj = JSON.stringify(response.error);
				if (obj) {
					var message = JSON.parse(obj);
					Swal.fire(
						'Oho!',
						message.message + ' ' + message.error_user_msg,
						'error'
					)
				}

			}
			if (response.length && response[0].id) {
				Swal.fire(
					'Great!',
					'Successfully Shared!',
					'success'
				)
			}
		}
	});
}

