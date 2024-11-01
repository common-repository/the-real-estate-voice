<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function to retun feed url
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_get_feed_url() {
	$feed_url = TREV_CSMAP_FACEBOOK_CONNECT_URL;
	return $feed_url;
}

/**
 * Function to get license key
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_get_license_key() {
	return trev_csmap_get_feed_url() . '/';
}

/**
 * Function to get list of facebook pages
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_get_fb_pages() {

	$page_id_name = array();
	$page_ids     = array();
	$page_names   = array();
	$fb_pages     = array();
	$bytes        = random_bytes( 20 );
	$rand         = bin2hex( $bytes );
	$fb_ids       = array();
	$perm         = get_option( 'rev_api_token' );
	$get_feed_url = trev_csmap_get_feed_url();

	$get_pages_api = $get_feed_url . '/facebook/api.php?type=get_pages&api_token=' . $perm . '&rand=' . $rand;

	$response = wp_remote_get(
		$get_pages_api,
		array(
			'timeout'     => 120,
			'httpversion' => '1.1',
			'sslverify'   => false,
		)
	);

	$response_body = wp_remote_retrieve_body( $response );
	$result        = json_decode( $response_body );
	$pages         = array();

	if ( is_object( $result ) && ! is_wp_error( $result ) ) {
		if ( $result->error == 0 ) {
			$pages = $result->pages;
		}
	}

	foreach ( $pages as $page ) {
		$page_ids[]                = $page->id;
		$page_names[]              = $page->name;
		$page_id_name[ $page->id ] = $page->name;
		$fb_ids[]                  = $page->fb_id;
	}

	update_option( 'fb_pages_ids', $fb_ids );
	$get_fb_user_id = trev_csmap_get_fb_user_id();
	update_option( 'fb_user_id', $get_fb_user_id );

	$fb_pages['fb_ids']      = $page_ids;
	$fb_pages['fb_pages']    = $page_names;
	$fb_pages['fb_page_id']  = $page_id_name;
	$fb_pages['pages']       = $pages;
	$fb_pages['fb_real_ids'] = $fb_ids;

	return $fb_pages;
}

/**
 * Function to get FB user id
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_get_fb_user_id() {

	$token   = get_option( 'accessTokenLong' );
	$api_url = TREV_CSMAP_GRAPH_API . 'v13.0/me?fields=id&access_token=' . $token;

	$response = wp_remote_get(
		$api_url,
		array(
			'timeout'     => 120,
			'httpversion' => '1.1',
			'sslverify'   => false,
		)
	);

	$response_body = wp_remote_retrieve_body( $response );
	$result_body  = json_decode( $response_body );
	if ( isset( $result_body->id ) && ! empty( $result_body->id ) ) {
		return $result_body->id;
	}

}

/**
 * Function to get list of instagram pages
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_get_insta_pages() {

	$insta_pages     = array();
	$accessTokenLong = get_option( 'accessTokenLong' );
	$graph_api       = TREV_CSMAP_GRAPH_API . 'v13.0/';
	$get_fb_ids      = get_option( 'fb_pages_ids' );
	$ig_user_ids     = array();
	$ig_names        = array();
	$ig_user_names   = array();
	$ig_user_data    = array();

	if ( ! empty( $get_fb_ids ) ) {
		foreach ( $get_fb_ids as $key => $fb_id ) {
			$ig_user_ids[] = trev_csmap_instagram_ig_user_id( $fb_id );
		}
	}

	$ig_user_ids = array_filter( $ig_user_ids );

	if ( ! empty( $ig_user_ids ) ) {
		foreach ( $ig_user_ids as $ig_user_id ) {
			$insta_pages_api = TREV_CSMAP_GRAPH_API . 'v13.0/' . $ig_user_id . '/?fields=name,username&access_token=' . $accessTokenLong;

			$response = wp_remote_get(
				$insta_pages_api,
				array(
					'timeout'     => 120,
					'httpversion' => '1.1',
					'sslverify'   => false,
				)
			);

			$response_body    = wp_remote_retrieve_body( $response );
			$result          = json_decode( $response_body );
			$ig_names[]      = $result->name;
			$ig_user_names[] = $result->username;

			$ig_user_data[ $ig_user_id ]['displayname'] = $result->name;
			$ig_user_data[ $ig_user_id ]['username']    = $result->username;
			$ig_user_data[ $ig_user_id ]['id']          = $ig_user_id;
		}
	}

	$ig_user_data                 = array_values( $ig_user_data );
	$insta_pages['ig_names']      = $ig_names;
	$insta_pages['ig_user_names'] = $ig_user_names;
	$insta_pages['ig_user_ids']   = $ig_user_ids;
	$insta_pages['ig_user_data']  = $ig_user_data;

	update_option( 'revoice_get_insta_data', $insta_pages );
}

if ( ! function_exists( 'trev_csmap_get_post_data' ) ) {

	/**
	 * Function to get post data
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_get_post_data( $schedule_id ) {

		$general_tab_options = get_option( 'general_tab_option_name' );
		$get_post_date       = array();
		$post_id             = get_post_meta( $schedule_id, 'revoice_scheduled_post_id', true );
		$image_url   		 ='';

		if ( isset( $general_tab_options['website_platform'] ) && 'SiteLoft' == $general_tab_options['website_platform'] ) {

			$postType = get_post_type($post_id);

			if('listings' == $postType){
				
				$hero_image_thumbs = get_post_meta( $post_id, 'thumbnails', true );

				if(!empty($hero_image_thumbs) && ! empty( $hero_image_thumbs[0]['full'] )){

					$image_url = $hero_image_thumbs[0]['full'];

				}else{

					$hero_image_full = get_post_meta( $post_id, 'images', true );

					if(!empty($hero_image_full)){
						$image_url = $hero_image_full[0]['full'];	
					}
				}
			}else{
				$hero_image_thumbs = get_post_meta( $post_id, 'hero_image_thumbs', true );
				if ( ! empty( $hero_image_thumbs['full'] ) ) {
					$image_url = $hero_image_thumbs['full'];
				}
			}
			

		}else{
			$image_url                 = get_the_post_thumbnail_url( $post_id, 'full' );	
		}
		
		$desc                       = get_the_excerpt( $schedule_id );
		$get_post_date['post_id']   = $post_id;
		$get_post_date['image_url'] = $image_url;
		$get_post_date['desc']      = $desc;

		return $get_post_date;
	}
}

function trev_csmap_get_agentpoint_data( $schedule_id ) {

		$get_post_date              = array();
		$post_id                    = get_post_meta( $schedule_id, 'revoice_scheduled_post_id', true );

		global $wpdb;

		$properties = 'properties';

		$qry_post = "SELECT * FROM $properties WHERE id=".$post_id;

		$posts = $wpdb->get_results( $qry_post );

		$attachment = 'attachments';

		$qry_img = "SELECT * FROM $attachment WHERE parent_id=".$post_id;

		$images_res = $wpdb->get_results( $qry_img );
		if(!empty($images_res) || !empty($posts)){
			$image_url                  = $images_res[0]->url;
		 $desc                       = $posts[0]->description;
		 $get_post_date['post_id']   = $post_id;
		 $get_post_date['image_url'] = $image_url;
		 $get_post_date['desc']      = $desc;
		}
		 

		 return $get_post_date;
	}

if ( ! function_exists( 'trev_csmap_listing_auto_share_post' ) ) {

	/**
	 * Callback Function to run when add listing
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_listing_auto_share_post( $post_id, $post ) {

		if ( ! trev_csmap_is_pro_license_plugin() ) {
			return $post_id;
		}

		// Facebook Auto share
		$api_token                   = get_option( 'rev_api_token' );
		$revoice_get_fb_pages        = trev_csmap_get_fb_pages();
		$page_ids                    = $revoice_get_fb_pages['fb_ids'];
		$status                      = esc_html__( 'Auto Shared', 'the-real-estate-voice' );
		$title                       = $post->post_title;
		$desc                        = $post->post_content;
		
		$auto_instant_share          = false;
		$auto_schedule_share         = false;
		$enabled_sharing_for_listing = false;
		$get_status_auto_share       = get_option( 'auto_post_option_name' );


		if ( isset( $get_status_auto_share['share_times'] ) && 0 == $get_status_auto_share['share_times'] ) {

			$auto_instant_share = true;

		} elseif ( isset( $get_status_auto_share['share_times'] ) && 1 == $get_status_auto_share['share_times'] ) {

			$auto_schedule_share = true;
		}
	

		if ( isset( $get_status_auto_share['enabled_sharing_for']['listing'] ) && 1 == $get_status_auto_share['enabled_sharing_for']['listing'] ) {
			$enabled_sharing_for_listing = true;

		}

		$dripfeed_schedule = '';
		if ( isset( $get_status_auto_share['drip_feed_time'] ) && ! empty( $get_status_auto_share['drip_feed_time'] ) ) {

			$dripfeed_schedule = $get_status_auto_share['drip_feed_time'];

		}

		// if enable enable listing from facebook settings
		if ($enabled_sharing_for_listing ) {

			// If enebled instantly share
			if ( $auto_instant_share ) {
				$currency        = $bedroom_emoji = $bathroom_emoji = $garage_emoji = $listing_price = $nullSpace = $bedroom_asset = $bathroom_asset = $garage_asset = $map_address = '';
				$currency_symbol = wpsight_get_currency();

				$currency     = '$';
				$price        = get_post_meta( $post_id, '_price', true );
				$bedroom      = get_post_meta( $post_id, '_details_3', true );
				$bathroom     = get_post_meta( $post_id, '_details_4', true );
				$garage       = get_post_meta( $post_id, '_details_5', true );
				$map_address  = get_post_meta( $post_id, '_map_address', true );
				$agent_name   = get_post_meta( $post_id, '_agent_name', true );
				$agent_phone  = get_post_meta( $post_id, '_agent_phone', true );
				$listing_link = get_permalink( $post_id );
				$listing_desc = get_the_excerpt( $post_id );

				if ( ! empty( $bedroom ) ) {
					$bedroom_emoji = '🛌  ';
					$bedroom_asset = $bedroom . ' ' . $bedroom_emoji;
				}

				if ( ! empty( $bathroom ) ) {
					$bathroom_emoji = '🛀  ';
					$bathroom_asset = $bathroom . ' ' . $bathroom_emoji;
				}

				if ( ! empty( $garage ) ) {
					$garage_emoji = '🚗 ';
					$garage_asset = $garage . ' ' . $garage_emoji;
				}

				if ( isset( $price ) && ! empty( $price ) ) {
					$listing_price = $price . "\n\n";
				}

				$list_data = $bedroom_asset . '' . $bathroom_asset . '' . $garage_asset;

				if ( ! empty( $list_data ) ) {
					$list_data = $list_data . "\n\n\n";
				} else {
					$list_data = '';
				}
				if ( ! empty( $map_address ) ) {
					$map_address = $map_address;
				}

				$sharable_desc = $title . "\n\n\n" . $list_data . $listing_price . $map_address . "\n\n\n" . $listing_link . "\n\n\n" . $listing_desc;
				if ( ! empty( $agent_phone ) ) {
					$sharable_desc .= "\n\n\n" . esc_html__( 'For more information call ', 'the-real-estate-voice' ) . $agent_name . ' on ' . $agent_phone;
				}

				foreach ( $page_ids as $page_id ) {

					$bytes = random_bytes( 20 );
					$rand  = bin2hex( $bytes );

					$get_pages_api = trev_csmap_get_feed_url() . '/facebook/api.php?type=share_in_page&api_token=' . $api_token . '&rand=' . $rand . '&page_id=' . $page_id . '&title=' . $title . '&desc=' . $sharable_desc . '&link=' . get_the_permalink( $post_id );

					$response = wp_remote_get(
						$get_pages_api,
						array(
							'timeout'     => 120,
							'httpversion' => '1.1',
							'sslverify'   => false,
						)
					);

					$response_body = wp_remote_retrieve_body( $response );
					$result       = json_decode( $response_body );

					if ( is_object( $result ) && ! is_wp_error( $result ) ) {
						if ( $result->error == 0 ) {
							update_post_meta( $post_id, 'revoice_shared', current_datetime() );
							update_post_meta( $post_id, 'revoice_sharing_status', $status );

						} else {
							add_post_meta( $post_id, 'sc_error', $result->msg );

						}
					} else {

						add_post_meta( $post_id, 'sc_error', 'Something went wrong!' );

					}
				}
			}

			// If Drip feed enabled
			if ( $auto_schedule_share ) {

				$fb_page_ids       = implode( ',', $page_ids );
				$current_time_zone = get_option( 'timezone_string' );
				date_default_timezone_set( $current_time_zone );

				$date_now  = date( 'H:i' );
				$post_type = 'listing';

				if ( $dripfeed_schedule > $date_now ) {
					
					$date_time = date( 'Y-m-d' ) . ' ' . $dripfeed_schedule;

				} else {

					date( 'Y-m-d', strtotime( date( 'Y-m-d' ) . ' +1 days' ) );
					$tomorrow  = date( 'Y-m-d', strtotime( '+1 days' ) );
					$date_time = $tomorrow . ' ' . $dripfeed_schedule;

				}

				$status       = 'Currently scheduled';
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

				$schedule_post_id = wp_insert_post( $post_data );

				if ( $schedule_post_id > 0 ) {

				update_post_meta( $schedule_post_id, 'revoice_scheduled', $date_time );
				update_post_meta( $schedule_post_id, 'revoice_scheduled_status', 'scheduled' );
				update_post_meta( $schedule_post_id, 'revoice_scheduled_post_id', $post_id );
				update_post_meta( $schedule_post_id, 'revoice_post_type', $post_type );
				update_post_meta( $post_id, 'revoice_scheduled_id', $schedule_post_id );
				update_post_meta( $post_id, 'revoice_sharing_status', $status );
				update_post_meta( $schedule_post_id, 'revoice_scheduled_page_id', $fb_page_ids);

				}
			}
		}
	}
}

add_action( 'publish_listing', 'trev_csmap_listing_auto_share_post', 10, 2 );


if ( ! function_exists( 'trev_csmap_listing_images' ) ) {
	/**
	 * Function to listing gallery
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	add_filter( 'wpsight_meta_boxes', 'trev_csmap_listing_images', 99, 1 );
	function trev_csmap_listing_images( $meta_boxes ) {

		$meta_boxes['listing_images'] = wpsight_meta_box_listing_images();

		return $meta_boxes;

	}
}

if ( ! function_exists( 'trev_csmap_listing_details_key' ) ) {

	/**
	 * Function get the listing details
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_listing_details_key() {
		$wpcasa      = get_option( 'wpcasa' );
		$listing_key = array();

		foreach ( $wpcasa as $details => $label ) {
			if ( isset( $label['label'] ) ) {
				if ( 'Bedroom' == $label['label'] || 'bedroom' == $label['label'] || 'Beds' == $label['label'] || 'beds' == $label['label'] ) {
					$listing_key['bedroom'] = $details;
				}
				if ( 'Bathroom' == $label['label'] || 'bathroom' == $label['label'] || 'Baths' == $label['label'] || 'baths' == $label['label'] ) {
					$listing_key['bathroom'] = $details;
				}
				if ( 'Garage' == $label['label'] || 'garage' == $label['label'] || 'Car Park' == $label['label'] || 'Parks' == $label['label'] ) {
					$listing_key['garage'] = $details;
				}
				if ( 'Floor Area' == $label['label'] || 'Floor area' == $label['label'] || 'floor area' == $label['label'] ) {
					$listing_key['floor_area'] = $details;
				}
				if ( 'Land Area' == $label['label'] || 'Land area' == $label['label'] || 'land area' == $label['label'] ) {

					$listing_key['land_area'] = $details;
				}
			}
		}

		return $listing_key;

	}
}

if ( ! function_exists( 'trev_csmap_listing_agent_details' ) ) {

	/**
	 * Function to listing agent details
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_listing_agent_details( $listing_id ) {

		$agent_details = array();

		$agent_details['name']        = get_post_meta( $listing_id, '_agent_name', true );
		$agent_details['company']     = get_post_meta( $listing_id, '_agent_company', true );
		$agent_details['description'] = get_post_meta( $listing_id, '_agent_description', true );
		$agent_details['phone']       = get_post_meta( $listing_id, '_agent_phone', true );
		$agent_details['website']     = get_post_meta( $listing_id, '_agent_website', true );
		$agent_details['twitter']     = get_post_meta( $listing_id, '_agent_twitter', true );
		$agent_details['facebook']    = get_post_meta( $listing_id, '_agent_facebook', true );
		$agent_details['logo']        = get_post_meta( $listing_id, '_agent_logo', true );

		return $agent_details;

	}
}

if ( ! function_exists( 'trev_csmap_article_library_categories' ) ) {

	/**
	 * Function to article library category
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_article_library_categories( $country = '' ) {

		if(isset($country) && !empty($country)){

			if('NZ' == $country){

				$library_cats = array(
					'3'  => esc_html__( 'Selling', 'the-real-estate-voice' ),
					'4'  => esc_html__( 'Buying', 'the-real-estate-voice' ),
					'5'  => esc_html__( 'Investment', 'the-real-estate-voice' ),
					'6'  => esc_html__( 'Improvements', 'the-real-estate-voice' ),
					'7'  => esc_html__( 'Lifestyle', 'the-real-estate-voice' ),
					'8'  => esc_html__( 'Management', 'the-real-estate-voice' ),
					'9'  => esc_html__( 'Renting', 'the-real-estate-voice' ),
					'10' => esc_html__( 'Learn', 'the-real-estate-voice' ),
					'11' => esc_html__( 'Maintenance', 'the-real-estate-voice' ),
				);

			}elseif ('UK' == $country) {
				
				$library_cats = array(
					'3'  => esc_html__( 'Selling', 'the-real-estate-voice' ),
					'4'  => esc_html__( 'Buying', 'the-real-estate-voice' ),
					'5'  => esc_html__( 'Investment', 'the-real-estate-voice' ),
					'6'  => esc_html__( 'Improvements', 'the-real-estate-voice' ),
					'7'  => esc_html__( 'Lifestyle', 'the-real-estate-voice' ),
					'8'  => esc_html__( 'Management', 'the-real-estate-voice' ),
					'9'  => esc_html__( 'Renting', 'the-real-estate-voice' ),
					'10' => esc_html__( 'Learn', 'the-real-estate-voice' ),
					'11' => esc_html__( 'Maintenance', 'the-real-estate-voice' ),
				);

			}elseif ('USA' == $country) {
				
				$library_cats = array(
					'3'  => esc_html__( 'Selling', 'the-real-estate-voice' ),
					'4'  => esc_html__( 'Buying', 'the-real-estate-voice' ),
					'5'  => esc_html__( 'Investment', 'the-real-estate-voice' ),
					'6'  => esc_html__( 'Improvements', 'the-real-estate-voice' ),
					'7'  => esc_html__( 'Lifestyle', 'the-real-estate-voice' ),
					'8'  => esc_html__( 'Management', 'the-real-estate-voice' ),
					'9'  => esc_html__( 'Renting', 'the-real-estate-voice' ),
					'10' => esc_html__( 'Learn', 'the-real-estate-voice' ),
					'11' => esc_html__( 'Maintenance', 'the-real-estate-voice' ),
				);

			}else{
				$library_cats = trev_csmap_article_library_categories();
			}

		}else{

			$library_cats = array(
				'3'  => esc_html__( 'Selling', 'the-real-estate-voice' ),
				'4'  => esc_html__( 'Buying', 'the-real-estate-voice' ),
				'5'  => esc_html__( 'Investment', 'the-real-estate-voice' ),
				'6'  => esc_html__( 'Improvements', 'the-real-estate-voice' ),
				'7'  => esc_html__( 'Lifestyle', 'the-real-estate-voice' ),
				'8'  => esc_html__( 'Management', 'the-real-estate-voice' ),
				'9'  => esc_html__( 'Renting', 'the-real-estate-voice' ),
				'10' => esc_html__( 'Learn', 'the-real-estate-voice' ),
				'11' => esc_html__( 'Maintenance', 'the-real-estate-voice' ),
			);

			
		}

		return $library_cats;
	}
}

if ( ! function_exists( 'trev_csmap_instagram_library_categories' ) ) {

	/**
	 * Function to instagram library category
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_instagram_library_categories() {

		$insta_cats = array(

			'19' => esc_html__( 'Holidays', 'the-real-estate-voice' ),
			'20' => esc_html__( 'Quotes', 'the-real-estate-voice' ),
			'21' => esc_html__( 'NZ Holidays', 'the-real-estate-voice' ),
			'14' => esc_html__( 'Renovation', 'the-real-estate-voice' ),
			'16' => esc_html__( 'Downsizing', 'the-real-estate-voice' ),
			'17' => esc_html__( 'Renting', 'the-real-estate-voice' ),
			'18' => esc_html__( 'Invest', 'the-real-estate-voice' ),
			'15' => esc_html__( 'Selling', 'the-real-estate-voice' ),

		);

		return $insta_cats;
	}
}

/**
 * Function to get pro version url
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_licence_version_url() {

	$current_domain = home_url();
	$url            = preg_replace( '(^https?://)', '', $current_domain );
	$response       = wp_remote_get(
		TREV_CSMAP_PRO_VERSION_API,
		array(
			'sslverify' => false,
			'blocking' => true,
			'headers'   => array(
				'x-wp-host' => $url,
			),
		)
	);

	$response_body = wp_remote_retrieve_body( $response );
	$result       = json_decode( $response_body );
	$res_url      = (array) $result;

	return $res_url;

}

/**
 * Function to apply watermark on given image path
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_apply_watermark( $filepath, $type, $save_as_file ) {

	global $edd_options;

	if ( is_file( $filepath ) ) {

		$original_image_details = getimagesize( $filepath );
		$func_type              = preg_replace( '#image/#i', '', $original_image_details['mime'] );
		$acceptable_formats     = array( 'jpeg', 'gif', 'png' );

		if ( ! in_array( $func_type, $acceptable_formats ) ) {
			return false;
		}

		$funcName = 'imagecreatefrom' . $func_type;

		ob_start();

		$original_image = $funcName( $filepath );
		$error          = ob_get_clean();

		if ( ! $original_image ) {
			return false;
		}
	} else {
		return false;
	}

	$watermark_position = 'br';

	if ( $watermark_position ) {

		$watermark_image   = '';
		$instagram_setting = get_option( 'instagram_tab_option_name' );

		if ( ! empty( $instagram_setting['logo'] ) ) {
			$uploads      = wp_upload_dir();
			$abs_path_arr = image_get_intermediate_size( $instagram_setting['logo'], 'watermark-size' );

			if ( ! empty( $abs_path_arr ) ) {
				
				$watermark_image = str_replace( $uploads['baseurl'], $uploads['basedir'], $abs_path_arr['url'] );

			}else{

				$abs_path = wp_get_attachment_image_src( $instagram_setting['logo'] );
				$watermark_image = str_replace( $uploads['baseurl'], $uploads['basedir'], $abs_path[0] );
			}
		}
		if ( ! empty( $watermark_image ) ) {

			$upload_dir           = wp_upload_dir();
			$watermark_image_path = $watermark_image;

			if ( is_file( $watermark_image_path ) ) {
				$owatermark_image_details = getimagesize( $watermark_image_path );
				$func_type_wtermark       = preg_replace( '#image/#i', '', $owatermark_image_details['mime'] );

				if ( 'png' == $func_type_wtermark ) {
					$overlay = imagecreatefrompng( $watermark_image_path );
				} else {
					$overlay = imagecreatefromjpeg( $watermark_image_path );
				}

				if ( $original_image && $overlay ) {
					imagealphablending( $overlay, false );
					imagesavealpha( $overlay, true );

					$original_image_width   = imagesx( $original_image );
					$original_image_height  = imagesy( $original_image );
					$watermark_image_width  = imagesx( $overlay );
					$watermark_image_height = imagesy( $overlay );

					if ( $watermark_position && 'none' != $watermark_position ) {

						switch ( $watermark_position ) {

							// top left.
							case 'tl':
								$watermark_start_x = 0;
								$watermark_start_y = 0;
								break;

							// top center.
							case 'tc':
								$watermark_start_x = ( $original_image_width / 2 ) - ( $watermark_image_width / 2 );
								$watermark_start_y = 0;
								break;

							// top right.
							case 'tr':
								$watermark_start_x = $original_image_width - $watermark_image_width;
								$watermark_start_y = 0;
								break;

							// middle left.
							case 'ml':
								$watermark_start_x = 0;
								$watermark_start_y = ( $original_image_height / 2 ) - ( $watermark_image_height / 2 );
								break;

							// middle center.
							case 'mc':
								$watermark_start_x = ( $original_image_width / 2 ) - ( $watermark_image_width / 2 );
								$watermark_start_y = ( $original_image_height / 2 ) - ( $watermark_image_height / 2 );
								break;

							// middle right.
							case 'mr':
								$watermark_start_x = $original_image_width - $watermark_image_width;
								$watermark_start_y = ( $original_image_height / 2 ) - ( $watermark_image_height / 2 );
								break;

							// bottom left.
							case 'bl':
								$watermark_start_x = 0;
								$watermark_start_y = $original_image_height - $watermark_image_height;
								break;

							// bottom center.
							case 'bc':
								$watermark_start_x = ( $original_image_width / 2 ) - ( $watermark_image_width / 2 );
								$watermark_start_y = $original_image_height - $watermark_image_height;
								break;

							// bottom right.
							case 'br':
							default:
								$half_and_half      = ( $original_image_width / 2 ) + 60;
								$watermarkhalfWidth = $watermark_image_width / 2;

								$watermark_start_x = ( $original_image_width / 2 ) + $half_and_half / 2 - $watermarkhalfWidth;

								$watermark_start_y = ( $original_image_height - $watermark_image_height ) - 20;
								break;
						}

						// Copy another image from main image and overlay it.
						imagecopy( $original_image, $overlay, $watermark_start_x, $watermark_start_y, 0, 0, $watermark_image_width, $watermark_image_height );
					}

					$funcname_generate = 'image' . $func_type;

					if ( 'jpeg' == $func_type ) {

						$jpeg_quality = apply_filters( 'edd_img_wtm_jpeg_quality', 100 );
						$jpeg_quality = ( isset( $jpeg_quality ) && '' != trim( $jpeg_quality ) ) ? intval( $jpeg_quality ) : 75;

						$funcname_generate( $original_image, $save_as_file, $jpeg_quality );

					} elseif ( 'png' == $func_type ) {

						// Creating the transparent background for png image.
						imagesavealpha( $original_image, true );
						$transparent = imagecolorallocatealpha( $original_image, 0, 0, 0, 127 );
						imagefill( $original_image, 0, 0, $transparent );

						$png_quality = apply_filters( 'edd_img_wtm_png_quality', 6 );
						$png_quality = ( isset( $png_quality ) && '' != trim( $png_quality ) ) ? intval( $png_quality ) : 6;

						$funcname_generate( $original_image, $save_as_file, $png_quality );

					} else {
						$funcname_generate( $original_image, $save_as_file );
					}

					return true;
				}
			}
		}
	}

	return false;
}

/**
 * Function to get container id from fb graph api
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_get_container_id_from_fb( $api_url ) {

	$response = wp_remote_post(
		$api_url,
		array(
			'timeout'     => 120,
			'httpversion' => '1.1',
			'sslverify'   => false,
		)
	);

	$response_body = wp_remote_retrieve_body( $response );
	$container    = json_decode( $response_body );
	return $container;

}

/**
 * Function to post image and text to instagram api
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_post_publish_to_instagram( $api_url ) {

	$response = wp_remote_post(
		$api_url,
		array(
			'timeout'     => 120,
			'httpversion' => '1.1',
			'sslverify'   => false,
		)
	);

	$response_body = wp_remote_retrieve_body( $response );
	$result       = json_decode( $response_body );

	return $result;

}

/**
 * Function to get IG user id from fb page id
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_instagram_ig_user_id( $page_id ) {

	$token      = get_option( 'accessTokenLong' );
	$api_url    = TREV_CSMAP_GRAPH_API . 'v13.0/' . $page_id . '?fields=instagram_business_account&access_token=' . $token;
	$ig_user_id = '';

	$response = wp_remote_get(
		$api_url,
		array(
			'timeout'     => 120,
			'httpversion' => '1.1',
			'sslverify'   => false,
		)
	);

	$response_body = wp_remote_retrieve_body( $response );
	$result_body  = json_decode( $response_body );

	$result = (array) $result_body;

	if ( array_key_exists( 'instagram_business_account', $result ) ) {
		$ig_user_id = $result['instagram_business_account']->id;

	}

	return $ig_user_id;
}



/**
 * Function to get fb page access token
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_fb_page_access_token( $page_id = '' ) {

	$page_access_tokens = array();
	$token              = get_option( 'accessTokenLong' );
	$get_fb_user_id     = get_option( 'fb_user_id' );
	$api_url            = TREV_CSMAP_GRAPH_API . $get_fb_user_id . '/accounts?fields=name,access_token&access_token=' . $token;

	$response = wp_remote_get(
		$api_url,
		array(
			'timeout'     => 120,
			'httpversion' => '1.1',
			'sslverify'   => false,
		)
	);

	$response_body = wp_remote_retrieve_body( $response );
	$result_body  = json_decode( $response_body );

	if ( ! empty( $result_body->data ) ) {
		foreach ( $result_body->data as $key => $data ) {

			$page_access_tokens[ $data->id ] = $data->access_token;
			if ( $data->id == $page_id ) {
				$page_access_token = $data->access_token;
			}
		}
	}

	if ( isset( $page_id ) && ! empty( $page_id ) ) {
		return $page_access_token;
	} else {
		return $page_access_tokens;
	}

}


/**
 * Function to get fb page reactions data
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_get_reactions_data( $page_id, $metric = '' ) {

	$page_insight_data  = array();
	$page_access_tokens = trev_csmap_fb_page_access_token( $page_id );
	$until              = time();
	$since              = strtotime( date( 'Y-m-01' ) );
	if ( empty( $metric ) ) {
		$metric = 'page_fans';
	}
	$api_url = TREV_CSMAP_GRAPH_API . $page_id . '/insights/?access_token=' . $page_access_tokens . '&metric=' . $metric . '&date_preset=last_14d';

	$response = wp_remote_get(
		$api_url,
		array(
			'timeout'     => 120,
			'httpversion' => '1.1',
			'sslverify'   => false,
		)
	);

	$response_body = wp_remote_retrieve_body( $response );
	$result_body  = json_decode( $response_body );

	if ( ! empty( $result_body ) ) {

		if ( ! empty( $result_body->data ) ) {
			$datas = $result_body->data[0]->values;

			foreach ( $datas as $key => $data ) {
				$page_insight_data[ $metric ][ $data->end_time ] = $data->value;
			}
		}
	}

	return $page_insight_data;

}


/**
 * Function to get fb page insights data
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_fb_insights_data( $page_id, $metric = '' ) {

	$page_insight_data  = array();
	$page_access_tokens = trev_csmap_fb_page_access_token( $page_id );

	if ( ! empty( $metric ) ) {
		$param = $metric;
	} else {
		$param = 'page_impressions_unique,page_impressions,page_engaged_users';
	}

	$date_presets = array( 'last_14d', 'last_30d', 'last_90d' );

	foreach ( $date_presets as $key => $date_preset ) {

		$api_url = TREV_CSMAP_GRAPH_API . $page_id . '/insights/?access_token=' . $page_access_tokens . '&metric=' . $param . '&period=day&date_preset=' . $date_preset;

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'     => 120,
				'httpversion' => '1.1',
				'sslverify'   => false,
			)
		);

		$response_body = wp_remote_retrieve_body( $response );
		$result_body  = json_decode( $response_body );
		$get_data     = array();

		if ( ! empty( $result_body->data ) ) {
			foreach ( $result_body->data as $key => $data ) {

				foreach ( $data->values as $key => $metic_data ) {
					$page_insight_data[ $date_preset ][ $data->name ][ $metic_data->end_time ] = $metic_data->value;
				}
			}
		}
	}

	return $page_insight_data;

}

/**
 * Function to get instagram profile view data
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_get_insta_insight( $ig_id, $metric = '' ) {

	$page_post_id  = array();
	$insta_insight = $insta_reach = $insta_impressions = array();
	$token         = get_option( 'accessTokenLong' );
	$until         = time();
	$date_presets  = array( 'last_14d', 'last_29d');
	$insta_insight_data  = array();
	

	if ( ! empty( $metric ) ) {
		$param = $metric;
	} else {
		$param = 'profile_views,impressions,reach';
	}

	foreach ( $date_presets as $key => $date_preset ) {
		if('last_14d' == $date_preset){
			$last_days = '- 14 days';
		}elseif ('last_29d' == $date_preset) {
			$last_days = '- 29 days';
		}
		$since         = strtotime( date( 'Y-m-d' ) . $last_days );

		$api_url 	   = TREV_CSMAP_GRAPH_API . 'v13.0/' . $ig_id . '/insights?access_token=' . $token . '&metric=' . $param . '&period=day&until=' . $until . '&since=' . $since;

		$response = wp_remote_get(
			$api_url,
			array(
				'timeout'     => 120,
				'httpversion' => '1.1',
				'sslverify'   => false,
			)
		);

		$response_body = wp_remote_retrieve_body( $response );
		$result_body   = json_decode( $response_body );
		$get_data      = array();
	
		if ( ! empty( $result_body->data ) ) {
			foreach ( $result_body->data as $key => $data ) {

				foreach ( $data->values as $key => $metic_data ) {
					$insta_insight_data[ $date_preset ][ $data->name ][ $metic_data->end_time ] = $metic_data->value;
				}
			}
		}
	}

	
	return $insta_insight_data;

}


function  trev_csmap_get_web_plateform_data($revoice_post_type, $post_id){
	
	$general_tab_options 	= get_option( 'general_tab_option_name' );
	$periodArray = array(
		'rental_period_1' => esc_html__( 'per Month', 'revoice' ),
		'rental_period_2' => esc_html__( 'per Week', 'revoice' ),
		'rental_period_3' => esc_html__( 'per Year', 'revoice' ),
		'rental_period_4' => esc_html__( 'per Day', 'revoice' )
	);

	if ( 'listing' == $revoice_post_type ||  'listings'  == $revoice_post_type || 'property'  == $revoice_post_type) {
		
		//$currency_symbol = wpsight_get_currency();
		$price           = get_post_meta( $post_id, '_price', true );
		$price_period 	 = get_post_meta( $post_id, '_price_period', true );
		$listing_details = array();

		$listing_details['map_address'] = get_post_meta( $post_id, '_map_address', true );

		if( isset($general_tab_options['website_platform']) && 'Website Blue' == $general_tab_options['website_platform'] ) {

			$listing_details['bedroom']  = get_post_meta( $post_id, '_details_1', true );
			$listing_details['bathroom'] = get_post_meta( $post_id, '_details_2', true );
			$listing_details['garage']   = get_post_meta( $post_id, '_details_3', true );

			if ( isset( $price ) && ! empty( $price ) ) {
				if ( ! empty( $price_period ) ) {
					
					$listing_details['price'] = '$' . $price . ' ' . $periodArray[ $price_period ];
				}else{

					$listing_details['price'] = '$' . $price;
				}
			}

		}elseif( isset($general_tab_options['website_platform']) && 'SiteLoft' == $general_tab_options['website_platform'] ) {

			$listing_details['bedroom']  = get_post_meta( $post_id, 'detailsBeds', true );
			$listing_details['bathroom'] = get_post_meta( $post_id, 'detailsBaths', true );
			$listing_details['garage']   = get_post_meta( $post_id, 'detailsCarAccom', true );

			$listing_details['price']    = get_post_meta( $post_id, 'price', true );

			if ( empty( $listing_details['price'] ) ) {

				$listing_details['price'] = get_post_meta( $post_id, 'priceMatch', true );
			}


			$addressStreetNumber = get_post_meta( $post_id, 'addressStreetNumber', true );

			$addressStreet   = get_post_meta( $post_id, 'addressStreet', true );
			$addressSuburb   = get_post_meta( $post_id, 'addressSuburb', true );
			$addressPostcode = get_post_meta( $post_id, 'addressPostcode', true );
			$addressState    = get_post_meta( $post_id, 'addressState', true );

			if ( ! empty( $addressStreetNumber ) ) {
				$addressStreetNumber .= ',';
			}

			if ( ! empty( $addressStreet ) ) {
				$addressStreet .= ',';
			}

			if ( ! empty( $addressSuburb ) ) {
				$addressSuburb .= ',';
			}

			if ( ! empty( $addressPostcode ) && ! empty( $addressState ) ) {
				$addressPostcode .= ',';
			}

			$listing_details['map_address'] = $addressStreetNumber . $addressStreet . $addressSuburb . $addressPostcode . $addressState;


		}elseif( isset($general_tab_options['website_platform']) && 'Agentpoint' == $general_tab_options['website_platform'] ) {

			global $wpdb;

			if('property' == $revoice_post_type){

				$properties = 'properties';
				
				$qry_post = "SELECT * FROM $properties WHERE id=$post_id";

				$posts = $wpdb->get_results( $qry_post );
				if(!empty($posts)){
					$listing_details['bedroom']  = $posts[0]->bedrooms;
					$listing_details['bathroom'] = $posts[0]->bathrooms;
					$listing_details['garage']   = $posts[0]->garage;

					$listing_details['price']    = $posts[0]->price;

					if ( empty( $listing_details['price'] ) ) {

						$listing_details['price'] = $posts[0]->display_price_text;
					}

					$addressStreetNumber = $posts[0]->street_number;

					$addressStreet   = $posts[0]->street;
					$addressSuburb   = $posts[0]->suburb;
					$addressPostcode = $posts[0]->postcode;
					$addressState    = $posts[0]->state;

					if ( ! empty( $addressStreetNumber ) ) {
						$addressStreetNumber .= ',';
					}

					if ( ! empty( $addressStreet ) ) {
						$addressStreet .= ',';
					}

					if ( ! empty( $addressSuburb ) ) {
						$addressSuburb .= ',';
					}

					if ( ! empty( $addressPostcode ) && ! empty( $addressState ) ) {
						$addressPostcode .= ',';
					}

					$listing_details['map_address'] = $addressStreetNumber . $addressStreet . $addressSuburb . $addressPostcode . $addressState;

				}
				

			}
		


		}else{

			$listing_details['bedroom']     = get_post_meta( $post_id, '_details_3', true );
			$listing_details['bathroom']    = get_post_meta( $post_id, '_details_4', true );
			$listing_details['garage']      = get_post_meta( $post_id, '_details_5', true );

			if ( isset( $price ) && ! empty( $price ) ) {
				$listing_details['price'] 	= $price;
			}

			$agent_details_all        		= trev_csmap_listing_agent_details( $post_id );
			$listing_details['name']  		= $agent_details_all['name'];
			$listing_details['phone'] 		= $agent_details_all['phone'];
		}

		$listing_details['listing_link'] 	= get_permalink( $post_id );
		$listing_details['listing_desc'] 	= get_the_excerpt( $post_id );

		$bedroom_emoji = '';
		if ( ! empty( $listing_details['bedroom'] ) ) {
			$bedroom_emoji                    = '&#128716;';
			$listing_details['bedroom_emoji'] = '&#128716';
		}

		$bathroom_emoji = '';
		if ( ! empty( $listing_details['bathroom'] ) ) {
			$bathroom_emoji                    = '&#128704;';
			$listing_details['bathroom_emoji'] = '&#128704';
		}

		$garage_emoji = '';
		if ( ! empty( $listing_details['garage'] ) ) {
			$garage_emoji 					   = '&#128663;';
			$listing_details['garage_emoji']   = '&#128663';
		}

		
		return $listing_details;
	
	}
}

add_action( 'admin_footer', 'trev_csmap_admin_footer', PHP_INT_MAX );

if ( ! function_exists( 'trev_csmap_admin_footer' ) ) {

	/**
	 * Function to set style & js globaly for admin
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 **/
	function trev_csmap_admin_footer() {
		?>
		<style type="text/css">
			.revoice_activation_notice.error .notice-wrap {
				display: flex;
				justify-content: space-between;
				align-items: center;
			}
		</style>
		<script type="text/javascript">
			  jQuery( "ul#adminmenu a[href='<?php echo TREV_CSMAP_HELP_LINK ?>']" ).attr( 'target', '_blank' );

		</script>
		<?php
	}
}


/**
 * Function to get selected country
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_selected_country(){

	$general_setting = get_option('general_tab_option_name');
	$revoice_country = $general_setting['revoice_country'];

	return $revoice_country;
}


/**
 * Function to get country Name
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_country_name($country_code){
	$country_code = strtoupper($country_code);
	$countryName = '';

	switch ($country_code) {
		case 'AUS':
			 $countryName = 'Australia';
			break;
		case 'USA':
			 $countryName = 'USA';
			break;
		case 'UK':
			 $countryName = 'UK';
			break;
		case 'NZ':
			 $countryName = 'New Zealand';
			break;
		
		default:
			$countryName = 'Australia';
			break;
	}

 
	return $countryName;
}


add_action('admin_menu', 'trev_csmap_admin_menu');

/**
 * Function to Add link of help menu
 *
 * @package The Real Estate Voice
 * @since 1.1.3
 */
function trev_csmap_admin_menu() {
    global $submenu;
   
    $submenu['trev'][7][2] =  TREV_CSMAP_HELP_LINK;
}


add_action('trash_post','trev_csmap_delete_function');

/**
 * Function to delete schedule share after post delete
 *
 * @package The Real Estate Voice
 * @since 1.1.3
 */
function trev_csmap_delete_function( $post_id ){	
	
	$sch_id = get_post_meta( $post_id, 'revoice_scheduled_id', true );
	delete_post_meta( $post_id, 'revoice_scheduled_id', $sch_id );
	delete_post_meta( $post_id, 'revoice_sharing_status' );
	wp_delete_post( $sch_id );

}

