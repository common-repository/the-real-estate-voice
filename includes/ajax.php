<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_trev_csmap_revoice_add', 'trev_csmap_revoice_add' );

/**
 * Ajax Callback function to add post
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_revoice_add() {
	check_ajax_referer( 'revoice_nonce_fb', 'security' );
	if ( ! function_exists( 'is_plugin_active' ) ) {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$general_tab_options = get_option( 'general_tab_option_name' );

	$bcats = sanitize_text_field( wp_unslash( $_POST['categories'] ) );

	$categories = json_decode( $bcats );

	$title = sanitize_text_field( wp_unslash( $_POST['title'] ) );

	$content = wp_kses_post( $_POST['content'] );

	$image = esc_url( wp_unslash( $_POST['image'] ) );

	$post_excerpt = sanitize_text_field( wp_unslash( $_POST['post_excerpt'] ) );

	$revcp_settings_options = get_option( 'revcp_settings_option_name' );

	$attribute_posts_to_this_user_1 = $revcp_settings_options['attribute_posts_to_this_user_1'];

	$post_status_2 = $revcp_settings_options['post_status_2'];

	if( !isset($post_status_2) || empty($post_status_2) ){
		$post_status_2='publish';
	}
	
	$cats = array();

	if ( is_array( $categories ) ) {

		foreach ( $categories as $cat ) {

			$parent_term = term_exists( $cat, 'category' );

			if ( ! is_array( $parent_term ) ) {
				$term = wp_insert_term( $cat, 'category' );

				if ( is_array( $term ) ) {
					$cats[] = $term['term_id'];
				}
			} else {
				$cats[] = $parent_term['term_id'];
			}
		}
	}

	$my_post = array(
		'post_title'    => wp_strip_all_tags( $title ),
		'post_content'  => $content,
		'post_excerpt'  => $post_excerpt,
		'post_status'   => $post_status_2,
		'post_author'   => $attribute_posts_to_this_user_1,
		'post_category' => $cats,

	);

	$thumbnailWithSize = array();
	$post_id           = wp_insert_post( $my_post );
	if ( ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, 'revoice_sharing_status', 'Never Shared' );
	}

	$response = wp_remote_get(
		$image,
		array(
			'timeout'   => 20,
			'sslverify' => false,
		)
	);

	if ( ! is_wp_error( $response ) ) {

		$bits         = wp_remote_retrieve_body( $response );
		$filename     = strtotime( 'now' ) . '_' . uniqid() . '.jpg';
		$upload       = wp_upload_bits( $filename, null, $bits );
		$data['guid'] = $upload['url'];

		if ( ! empty( $upload['url'] ) ) {

			$data['post_mime_type'] = 'image/jpeg';
			$attach_id              = wp_insert_attachment( $data, $upload['file'], 0 );
			set_post_thumbnail( $post_id, $attach_id );

			if ( isset( $general_tab_options['website_platform'] ) && 'SiteLoft' == $general_tab_options['website_platform'] && is_plugin_active( 'wordpress-sync-plugin/functions.php' ) ) {

				update_post_meta( $post_id, 'hero_image', $attach_id );
				$all_sizes   = get_intermediate_image_sizes();
				$all_sizes[] = 'full';

				if ( ! empty( $all_sizes ) ) {
					foreach ( $all_sizes as $key => $reg_size ) {
						$thumbnailWithSize[ $reg_size ] = get_the_post_thumbnail_url( $post_id, $reg_size );
					}
				}
				update_post_meta( $post_id, 'hero_image_thumbs', $thumbnailWithSize );

			}
		}
	}

	$data = array();

	$data['status'] = $post_status_2;

	$data['post_id'] = $post_id;

	$data['editpage'] = get_edit_post_link( $post_id );

	wp_send_json( $data );
	die();

}

add_action( 'wp_ajax_trev_csmap_add_schedule', 'trev_csmap_add_schedule' );

/**
 * Ajax Callback function to add post in schedule for facebook
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_add_schedule() {
	check_ajax_referer( 'revoice_nonce_fb', 'security' );

	$general_tab_option_name = get_option( 'general_tab_option_name' );

	

	$title        = sanitize_text_field( wp_unslash( $_POST['title'] ) );
	$id           = sanitize_text_field( wp_unslash( $_POST['id'] ) );
	$share_desc   = wp_kses_post( $_POST['share_desc'] );
	$desc         = wp_kses_post( $_POST['desc'] );
	$post_type    = sanitize_text_field( wp_unslash( $_POST['post_type'] ) );
	$plafrom      = sanitize_text_field( wp_unslash( $_POST['platform'] ) );
	$date_time    = sanitize_text_field( wp_unslash( $_POST['datetime'] ) );
	$page_id      = sanitize_text_field( wp_unslash( $_POST['page_id'] ) );
	$lising_page  = sanitize_text_field( wp_unslash( $_POST['listing_page'] ) );
	$status       = esc_html__( 'Currently scheduled', 'the-real-estate-voice' );
	$postdate     = date( 'Y-m-d H:i:s', strtotime( $date_time ) );
	$postdate_gmt = get_gmt_from_date( $date_time, 'Y-m-d H:i:s' );

	$post_data = array(
		'post_title'    => $title,
		'post_content'  => $desc,
		'post_status'   => 'future',
		'post_type'     => 'post_share_schedules',
		'post_date_gmt' => $postdate_gmt,
		'post_date'     => $postdate,
		'edit_date'     => 'true',
	);

	$post_id = wp_insert_post( $post_data );

	if ( $post_id > 0 ) {

		update_post_meta( $post_id, 'revoice_scheduled', $date_time );
		update_post_meta( $post_id, 'revoice_scheduled_status', 'scheduled' );
		update_post_meta( $post_id, 'revoice_scheduled_post_id', $id );
		update_post_meta( $post_id, 'revoice_post_type', $post_type );
		update_post_meta( $post_id, 'revoice_listing_detail', $share_desc );
		

		if( isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform'] ) {
			trev_csmap_update_preperty_meta( $id, 'revoice_scheduled_id', $post_id );
			trev_csmap_update_preperty_meta( $id, 'revoice_sharing_status', $status );

		}else{
			update_post_meta( $id, 'revoice_scheduled_id', $post_id );
			update_post_meta( $id, 'revoice_sharing_status', $status );

		}
		
		update_post_meta( $post_id, 'revoice_scheduled_page_id', $page_id );
		update_post_meta( $post_id, 'revoice_platform', $plafrom );
		update_post_meta( $post_id, 'revoice_listingpage', $lising_page );

	}

	die();
}


add_action( 'wp_ajax_trev_csmap_insta_add_schedule', 'trev_csmap_insta_add_schedule' );


/**
 * Ajax Callback function to add post in schedule for instagram
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_insta_add_schedule() {
	check_ajax_referer( 'revoice_nonce_insta', 'insta_security' );

	$title        = sanitize_text_field( wp_unslash( $_POST['title'] ) );
	$id           = sanitize_text_field( wp_unslash( $_POST['id'] ) );
	$share_desc   = isset( $_POST['share_desc'] ) ? wp_kses_post( $_POST['share_desc'] ) : '';
	$user_ids     = isset( $_POST['insta_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['insta_ids'] ) ) : '';
	$insta_ids    = explode( ',', $user_ids );
	$image        = esc_url( wp_unslash( $_POST['image'] ) );
	$desc         = wp_kses_post( $_POST['desc'] );
	$post_type    = sanitize_text_field( wp_unslash( $_POST['post_type'] ) );
	$lising_page  = sanitize_text_field( wp_unslash( $_POST['listing_page'] ) );
	$date_time    = sanitize_text_field( wp_unslash( $_POST['datetime'] ) );
	$plafrom      = sanitize_text_field( wp_unslash( $_POST['platform'] ) );
	$status       = esc_html__( 'Currently scheduled', 'the-real-estate-voice' );
	$postdate     = date( 'Y-m-d H:i:s', strtotime( $date_time ) );
	$postdate_gmt = get_gmt_from_date( $date_time, 'Y-m-d H:i:s' );

	$post_data = array(

		'post_title'    => $title,
		'post_content'  => $share_desc,
		'post_status'   => 'future',
		'post_type'     => 'post_share_schedules',
		'post_date_gmt' => $postdate_gmt,
		'post_date'     => $postdate,
		'edit_date'     => 'true',

	);

	$post_id = wp_insert_post( $post_data );

	if ( $post_id > 0 ) {

		update_post_meta( $post_id, 'revoice_scheduled', $date_time );
		update_post_meta( $post_id, 'revoice_scheduled_status', 'scheduled' );
		update_post_meta( $post_id, 'revoice_scheduled_post_id', $id );
		update_post_meta( $post_id, 'revoice_post_type', $post_type );
		update_post_meta( $post_id, 'revoice_post_image', $image );
		update_post_meta( $id, 'revoice_scheduled_id', $post_id );
		update_post_meta( $id, 'revoice_sharing_status', $status );
		update_post_meta( $post_id, 'revoice_scheduled_insta_id', $insta_ids );
		update_post_meta( $post_id, 'revoice_platform', $plafrom );
		update_post_meta( $post_id, 'revoice_listingpage', $lising_page );

	}

	die();
}

add_action( 'wp_ajax_trev_csmap_update_insta_add_schedule', 'trev_csmap_update_insta_add_schedule' );

/**
 * Ajax Callback function to add post in schedule for instagram
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_update_insta_add_schedule() {
	check_ajax_referer( 'revoice_nonce_insta', 'insta_security' );
	$edit_desc    = wp_kses_post( $_POST['edit_desc'] );
	$sc_datetime  = sanitize_text_field( wp_unslash( $_POST['sc_datetime'] ) );
	$post_id      = sanitize_text_field( wp_unslash( $_POST['post_id'] ) );
	$user_ids     = isset( $_POST['insta_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['insta_ids'] ) ) : '';
	$insta_ids    = explode( ',', $user_ids );
	$postdate     = date( 'Y-m-d H:i:s', strtotime( $sc_datetime ) );
	$postdate_gmt = get_gmt_from_date( $sc_datetime, 'Y-m-d H:i:s' );

	$post_data = array(
		'ID'            => $post_id,
		'post_content'  => $edit_desc,
		'post_date'     => $postdate,
		'post_date_gmt' => $postdate_gmt,

	);

	$post_id = wp_update_post( $post_data );

	if ( $post_id > 0 ) {
		update_post_meta( $post_id, 'revoice_scheduled', $sc_datetime );
		update_post_meta( $post_id, 'revoice_scheduled_insta_id', $insta_ids );
	}
	die();
}


add_action( 'wp_ajax_trev_csmap_schedule_update', 'trev_csmap_schedule_update' );

/**
 * Ajax Callback function to update schedule post
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_schedule_update() {
	check_ajax_referer( 'revoice_nonce_fb', 'security' );
	$wp_response    = array();
	$id             = sanitize_text_field( wp_unslash( $_POST['id'] ) );
	$desc           = wp_kses_post( $_POST['desc'] );
	$share_desc     = wp_kses_post( $_POST['share_desc'] );
	$sharable_agent = sanitize_text_field( wp_unslash( $_POST['sharable_agent'] ) );
	$date_time      = sanitize_text_field( wp_unslash( $_POST['datetime'] ) );
	$page_id        = sanitize_text_field( wp_unslash( $_POST['page_id'] ) );
	$postdate       = date( 'Y-m-d H:i:s', strtotime( $date_time ) );
	$postdate_gmt   = get_gmt_from_date( $date_time, 'Y-m-d H:i:s' );

	$post_data = array(
		'ID'            => $id,
		'post_content'  => $desc,
		'post_status'   => 'future',
		'post_type'     => 'post_share_schedules',
		'post_date_gmt' => $postdate_gmt,
		'post_date'     => $postdate,
		'edit_date'     => 'true',
	);

	$result = wp_update_post( $post_data );

	if ( is_wp_error( $result ) ) {

		$error_code              = array_key_first( $result->errors );
		$error_message           = $result->errors[ $error_code ][0];
		$wp_response['response'] = $error_message;

	} else {

		update_post_meta( $id, 'revoice_scheduled', $date_time );
		update_post_meta( $id, 'revoice_scheduled_page_id', $page_id );

		if ( ! empty( $sharable_agent ) ) {
			update_post_meta( $id, 'revoice_scheduled_agenet_details', $sharable_agent );
		}

		$wp_response['response'] = 'success';
	}

	wp_send_json( $wp_response );

	die();
}


add_action( 'wp_ajax_trev_csmap_remove_schedule', 'trev_csmap_remove_schedule' );

/**
 * Ajax Callback function to remove schedule
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_remove_schedule() {
	check_ajax_referer( 'revoice_nonce_fb', 'security' );

	$id      = sanitize_text_field( wp_unslash( $_POST['id'] ) );

	$post_id = get_post_meta( $id, 'revoice_scheduled_post_id', true );

	$general_tab_option_name = get_option( 'general_tab_option_name' );

	if( isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform'] ) {

		if( trev_csmap_exist_preperty_meta($post_id, 'revoice_shared') ){
			trev_csmap_update_preperty_meta( $post_id, 'revoice_sharing_status', 'Shared' );
		}else{
			trev_csmap_update_preperty_meta($post_id, 'revoice_sharing_status', 'Never Shared' );
		}

		trev_delete_preperty_meta( $post_id, 'revoice_scheduled_id' );

	}else{
		if ( metadata_exists( 'post', $post_id, 'revoice_shared' ) ) {
			update_post_meta( $post_id, 'revoice_sharing_status', 'Shared' );
		} else {
			update_post_meta( $post_id, 'revoice_sharing_status', 'Never Shared' );
		}

		delete_post_meta( $post_id, 'revoice_scheduled_id', $id );
		
	}
	wp_delete_post( $id );

	
	die();

}


add_action( 'wp_ajax_trev_csmap_insta_remove_schedule', 'trev_csmap_insta_remove_schedule' );

/**
 * Ajax Callback function to remove schedule
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_insta_remove_schedule() {
	check_ajax_referer( 'revoice_nonce_insta', 'insta_security' );
	$id = sanitize_text_field( wp_unslash( $_POST['id'] ) );
	wp_delete_post( $id );
	die();

}


add_action( 'wp_ajax_trev_csmap_share_now', 'trev_csmap_share_now' );

/**
 * Ajax Callback function to share post
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_share_now() {
	check_ajax_referer( 'revoice_nonce_fb', 'security' );
	$wp_response    = array();
	$title          = sanitize_text_field( wp_unslash( $_POST['title'] ) );
	$status         = sanitize_text_field( wp_unslash( $_POST['status'] ) );
	$id             = sanitize_text_field( wp_unslash( $_POST['id'] ) );
	$general_tab_option_name = get_option( 'general_tab_option_name' );

	if ( isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform'] ) {
		
		$current_status = trev_csmap_get_preperty_meta( $id, 'revoice_sharing_status' );

	}else{

		$current_status = get_post_meta( $id, 'revoice_sharing_status', true );	

	}
	

	if ( 'Currently scheduled' == $current_status ) {
			$status = $current_status;
	}

	$desc     = wp_kses_post( $_POST['desc'] );
	$data     = array();
	$page_id  = sanitize_text_field( wp_unslash( $_POST['page_id'] ) );
	$page_ids = explode( ',', $page_id );

	if ( count( $page_ids ) < 1 ) {

		$data['status'] = 'error';
		$data['msg']    = esc_html__( 'No Page Selected!', 'the-real-estate-voice' );

	} else {
		$api_token = get_option( 'rev_api_token' );
		foreach ( $page_ids as $page_id ) {

			$bytes = random_bytes( 20 );
			$rand  = bin2hex( $bytes );

			$get_pages_api = TREV_CSMAP_FACEBOOK_CONNECT_URL . 'facebook/api.php?type=share_in_page&api_token=' . $api_token . '&rand=' . $rand . '&page_id=' . $page_id . '&title=' . $title . '&desc=' . $desc . '&link=' . get_the_permalink( $id );

			$response = wp_remote_get(
				$get_pages_api,
				array(
					'timeout'     => 120,
					'httpversion' => '1.1',
					'sslverify'   => false,
				)
			);

			$responseBody = wp_remote_retrieve_body( $response );
			$result       = json_decode( $responseBody );

		}

		$pages = array();
		if ( is_object( $result ) && ! is_wp_error( $result ) ) {
			if ( $result->error == 0 ) {
				$data['status'] = 'success';
				$data['msg']    = esc_html__( 'Successfully Shared', 'the-real-estate-voice' );
			
				if( isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform'] ) {

					$current_datetime = date( 'd/m/Y' );

				trev_csmap_update_preperty_meta($id,'revoice_shared',$current_datetime);	
					trev_csmap_update_preperty_meta( $id, 'revoice_sharing_status', $status );	

				}else{

					update_post_meta( $id, 'revoice_shared', current_datetime() );
					update_post_meta( $id, 'revoice_sharing_status', $status );	

				}
				
			} else {
				$data['status'] = 'error';
				$data['msg']    = $result->msg;
			}
		} else {
			$data['status'] = 'error';
			$data['msg']    = esc_html__( 'Something went wrong!', 'the-real-estate-voice' );
		}
	}
	
	wp_send_json( $data );
	die();
}

add_action( 'wp_ajax_trev_csmap_insta_share_now', 'trev_csmap_insta_share_now' );

/**
 * Ajax Callback function to share post
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_insta_share_now() {
	check_ajax_referer( 'revoice_nonce_insta', 'insta_security' );
	$graph_api     = TREV_CSMAP_GRAPH_API . 'v3.2/';
	$img_url       = isset( $_POST['image_url'] ) ? esc_url( wp_unslash( $_POST['image_url'] ) ) : '';
	$caption       = isset( $_POST['desc'] ) ? wp_kses_post( $_POST['desc'] ) : '';
	$user_ids      = isset( $_POST['insta_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['insta_ids'] ) ) : '';
	$user_ids      = explode( ',', $user_ids );
	$response_send = array();

	if ( empty( $img_url ) ) {
		$response_send['status'] = 'error';
		$response_send['msg']    = esc_html__( 'Image url is not defined', 'the-real-estate-voice' );
		wp_send_json( $response_send );
	}

	$current_user    = get_user_by( 'id', get_current_user_id() );
	$uploadsurl      = wp_upload_dir();
	$bytes           = time();
	$folder_file     = '/instagram/' . $current_user->user_login . $bytes . '.jpg';
	$insta_watermark = $uploadsurl['basedir'] . $folder_file;

	copy( $img_url, $insta_watermark );

	trev_csmap_apply_watermark( $insta_watermark, '', $insta_watermark );

	$insta_image_url = $uploadsurl['baseurl'] . $folder_file;

	$get_long_lived_fb_token = get_option( 'accessTokenLong' );

	if ( empty( $user_ids ) || empty( $get_long_lived_fb_token ) ) {
		$response_send['status'] = 'error';
		$response_send['msg']    = esc_html__( 'Instagram user id or Access token is not defined', 'the-real-estate-voice' );
		wp_send_json( $response_send );
	}

	if ( ! empty( $graph_api ) ) {

		foreach ( $user_ids as  $user_id ) {

			$api = $graph_api . $user_id . '/media?image_url=' . $insta_image_url . '&caption=' . urlencode( $caption ) . '&access_token=' . $get_long_lived_fb_token;

			$container_response = trev_csmap_get_container_id_from_fb( $api );

			if ( ! empty( $container_response->error ) ) {

				$response_send['status'] = 'error';
				$response_send['msg']    = $container_response->error->error_user_title . ' ' . $container_response->error->error_user_msg;

			} else {

				if ( ! empty( $container_response ) ) {

					$container_id = $container_response->id;

					$api_post = $graph_api . $user_id . '/media_publish?creation_id=' . $container_id . '&access_token=' . $get_long_lived_fb_token;

					$image_response = trev_csmap_post_publish_to_instagram( $api_post );

					if ( $image_response ) {

						$response_send[] = $image_response;
					}
				} else {
					$response_send['status'] = 'error';
					$response_send['msg']    = esc_html__( 'Container id is missing', 'the-real-estate-voice' );

				}
			}
		}

		wp_send_json( $response_send );

	} else {

		$response_send['status'] = 'error';
		$response_send['msg']    = esc_html__( 'Something went wrong! Instagram share API is not connected yet', 'the-real-estate-voice' );
		wp_send_json( $response_send );
	}

	if ( file_exists( $insta_watermark ) ) {
		unlink( $insta_watermark );
	}
	die();
}


add_action( 'wp_ajax_trev_csmap_remove_page', 'trev_csmap_remove_page' );

if ( ! function_exists( 'trev_csmap_remove_page' ) ) {

	/**
	 * Ajax Callback function to remove post
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_remove_page() {
		check_ajax_referer( 'revoice_nonce_fb', 'security' );
		$id        = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$api_token = get_option( 'rev_api_token' );
		$bytes     = random_bytes( 20 );
		$rand      = bin2hex( $bytes );

		$get_pages_api = 'https://therealestatevoice.com.au/facebook/api.php?type=remove_page&api_token=' . $api_token . '&rand=' . $rand . '&id=' . $id;
		$response      = wp_remote_get(
			$get_pages_api,
			array(
				'timeout'     => 120,
				'httpversion' => '1.1',
				'sslverify'   => false,
			)
		);
		die();
	}
}

add_action( 'wp_ajax_trev_csmap_remove_pages', 'trev_csmap_remove_pages' );

if ( ! function_exists( 'trev_csmap_remove_pages' ) ) {

	/**
	 * Ajax Callback function to remove facebook pages
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_remove_pages() {
		check_ajax_referer( 'revoice_nonce_fb', 'security' );
		$wp_response = array();
		$page_ids    = sanitize_text_field( wp_unslash( $_POST['ids'] ) );
		$ids         = explode( ',', $page_ids );
		$api_token   = get_option( 'rev_api_token' );
		$bytes       = random_bytes( 20 );
		$rand        = bin2hex( $bytes );

		foreach ( $ids as $id ) {

			$get_pages_api = 'https://therealestatevoice.com.au/facebook/api.php?type=remove_page&api_token=' . $api_token . '&rand=' . $rand . '&id=' . $id;

			$response = wp_remote_get(
				$get_pages_api,
				array(
					'timeout'     => 120,
					'httpversion' => '1.1',
					'sslverify'   => false,
				)
			);

			$wp_response['code']    = $response['response']['code'];
			$wp_response['message'] = $response['response']['message'];

		}
		wp_send_json( $wp_response );

		die();
	}
}


add_action( 'wp_ajax_trev_csmap_remove_insta_pages', 'trev_csmap_remove_insta_pages' );

if ( ! function_exists( 'trev_csmap_remove_insta_pages' ) ) {

	/**
	 * Ajax Callback function to remove insta pages
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_remove_insta_pages() {
		check_ajax_referer( 'revoice_nonce_insta', 'insta_security' );
		$wp_response = array();

		update_option( 'revoice_get_insta_data', '' );

		wp_send_json( true );

		die();
	}
}


add_action( 'wp_ajax_trev_csmap_instagram_page_remove', 'trev_csmap_instagram_page_remove' );

if ( ! function_exists( 'trev_csmap_instagram_page_remove' ) ) {
	/**
	 * Ajax Callback function to remove instagram single page
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_instagram_page_remove() {
		check_ajax_referer( 'revoice_nonce_insta', 'insta_security' );
		$insta_id       = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$get_exist_data = get_option( 'revoice_get_insta_data' );

		foreach ( $get_exist_data['ig_user_ids'] as $key => $id ) {
			if ( $id == $insta_id ) {
				unset( $get_exist_data['ig_user_ids'][ $key ] );
				unset( $get_exist_data['ig_names'][ $key ] );
				unset( $get_exist_data['ig_user_names'][ $key ] );
			}
		}

		foreach ( $get_exist_data['ig_user_data'] as $key => $ig_user_data ) {
			if ( $ig_user_data['id'] == $insta_id ) {
				unset( $get_exist_data['ig_user_data'][ $key ] );
			}
		}

		update_option( 'revoice_get_insta_data', $get_exist_data );

		wp_send_json( true );

		die();
	}
}


add_action( 'wp_ajax_trev_csmap_connect_insta_pages', 'trev_csmap_connect_insta_pages' );

/**
 * Ajax Callback function to connect insta pages
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_connect_insta_pages() {
	check_ajax_referer( 'revoice_nonce_insta', 'insta_security' );
	$wp_response = array();

	trev_csmap_get_insta_pages();

	$revoice_insta_pages = get_option( 'revoice_get_insta_data' );

	$ig_user_ids = $revoice_insta_pages['ig_user_ids'];

	if ( ! empty( $ig_user_ids ) ) {
		$wp_response['status'] = true;
	} else {
		$wp_response['status'] = false;
	}

	wp_send_json( $wp_response );

	die();
}


add_action( 'wp_ajax_trev_csmap_select_post_cat', 'trev_csmap_select_post_cat' );

/**
 * Ajax Callback to get category
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_select_post_cat() {
	check_ajax_referer( 'revoice_nonce_fb', 'security' );
	$wp_response = array();
	if ( isset( $_POST['post_select'] ) && ! empty( $_POST['post_select'] ) ) {
		$post_select = sanitize_text_field( wp_unslash( $_POST['post_select'] ) );

		if ( 'articles' == $post_select ) {
			$post_select = 'post';
		}

		$taxonomies_objects = get_object_taxonomies( $post_select, 'objects' );
		$taxonomies         = get_taxonomies( array( 'object_type' => array( $post_select ) ) );

		$unset_cat = array(
			'post_tag',
			'nav_menu',
			'link_category',
			'post_format',
			'wp_theme',
			'wp_template_part_area',
		);
		foreach ( $unset_cat as  $taxonomy ) {
			unset( $taxonomies[ $taxonomy ] );

		}

		$cat_all = array();
		foreach ( $taxonomies as  $taxonomy ) {
			$hierarchical = $taxonomies_objects[ $taxonomy ]->hierarchical;
			if ( $hierarchical ) {
				$args      = array(
					'taxonomy'   => $taxonomy,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'hide_empty' => false,
				);
				$cat_all[] = get_categories( $args );
			}
		}

		$categories = array();
		foreach ( $cat_all as $cat ) {

			$i = 0;
			foreach ( $cat as $key => $cat_name ) {
				$categories[ $i ]['id']   = $cat_name->term_id;
				$categories[ $i ]['name'] = $cat_name->name;

				$i++;
			}
		}

		$wp_response['categories'] = $categories;

		wp_send_json( $wp_response );

		die();

	}
}

add_action( 'wp_ajax_trev_csmap_remove_brand_logo', 'trev_csmap_remove_brand_logo' );

/**
 * Ajax Callback to remove instagram brand logo
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_remove_brand_logo() {
	check_ajax_referer( 'revoice_nonce_insta', 'insta_security' );
	$wp_response = array();

	if ( isset( $_POST['brand_logo'] ) && ! empty( $_POST['brand_logo'] ) ) {

		$instagram_settings = get_option( 'instagram_tab_option_name' );
		if ( isset( $instagram_settings['logo'] ) && ! empty( $instagram_settings['logo'] ) ) {
			unset( $instagram_settings['logo'] );
		}
		update_option( 'instagram_tab_option_name', $instagram_settings );
		$brand_logo            = sanitize_text_field( wp_unslash( $_POST['brand_logo'] ) );
		$wp_response['result'] = 'success';
		wp_send_json( $wp_response );
		die();

	}
}
