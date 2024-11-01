<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'trev_csmap_create_posttype' ) ) {
	/**
	 * Function to create custom post type
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_create_posttype() {
		register_post_type(
			'post_share_schedules',
			array(
				'labels'       => array(
					'name'          => esc_html__( 'Share Schedule', 'the-real-estate-voice' ),
					'singular_name' => esc_html__( 'Share Schedule', 'the-real-estate-voice' ),
				),
				'public'       => false,
				'has_archive'  => false,
				'rewrite'      => array( 'slug' => 'post_share_schedules' ),
				'show_in_rest' => false,

			)
		);

		$show_albums = get_posts(
			array(
				'posts_per_page' => 8,
				'post_type'      => 'post_share_schedules',
				'genre'          => 'jazz',
				'post_status'    => 'any',
			)
		);

	}
}

add_action( 'init', 'trev_csmap_create_posttype' );

if ( ! function_exists( 'trev_csmap_share_schedule_post' ) ) {

	/**
	 * Function share the schedule post
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_share_schedule_post( $new_status, $old_status, $post ) {

		if ( ( 'publish' != $old_status ) && ( 'publish' == $new_status ) ) {

			if( 'post_share_schedules'==$post->post_type && trev_csmap_is_pro_license_plugin() ){

				$revoice_platform = get_post_meta( $post->ID, 'revoice_platform', true );
				$general_tab_option_name = get_option( 'general_tab_option_name' );

				if ( 'instagram' == $revoice_platform ) {

					$graph_api = TREV_CSMAP_GRAPH_API . 'v3.2/';
					$img_url   = get_post_meta( $post->ID, 'revoice_post_image', true );
					$user_ids  = get_post_meta( $post->ID, 'revoice_scheduled_insta_id', true );
					$caption   = $post->post_content;

					if ( empty( $img_url ) ) {
						$data['status'] = 'error';
						$data['msg']    = esc_html__( 'Image url is not defined', 'the-real-estate-voice' );
						add_post_meta( $post->ID, 'sc_error', $data );
					}

					$current_user    = get_user_by( 'id', get_current_user_id() );
					$uploadsurl      = wp_upload_dir();
					$bytes           = time();
					$folder_file     = '/instagram/' . $current_user->user_login . $bytes . '.jpg';
					$insta_watermark = $uploadsurl['basedir'] . $folder_file;

					copy( $img_url, $insta_watermark );

					trev_csmap_apply_watermark( $insta_watermark, '', $insta_watermark );

					$insta_image_url         = $uploadsurl['baseurl'] . $folder_file;
					$get_long_lived_fb_token = get_option( 'accessTokenLong' );

					if ( empty( $user_ids ) || empty( $get_long_lived_fb_token ) ) {
						$data['status'] = 'error';
						$data['msg']    = esc_html__( 'Instagram user id or Access token is not defined', 'the-real-estate-voice' );
						add_post_meta( $post->ID, 'sc_error', $data );
					}

					if ( ! empty( $graph_api ) ) {

						foreach ( $user_ids as  $user_id ) {

							$api = $graph_api . $user_id . '/media?image_url=' . $insta_image_url . '&caption=' . urlencode( $caption ) . '&access_token=' . $get_long_lived_fb_token;

							$container_response = trev_csmap_get_container_id_from_fb( $api );

							if ( ! empty( $container_response ) ) {
								$container_id = $container_response->id;

								$api_post = $graph_api . $user_id . '/media_publish?creation_id=' . $container_id . '&access_token=' . $get_long_lived_fb_token;

								$image_response=trev_csmap_post_publish_to_instagram($api_post);

								if ( $image_response ) {
									update_post_meta( $post->ID, 'revoice_shared', current_datetime() );
								}
							} else {
								$data['status'] = 'error';
								$data['msg']    = esc_html__( 'Container id is missing', 'the-real-estate-voice' );
								add_post_meta( $post->ID, 'sc_error', $data );
							}
						}
					} else {
						$data['status'] = 'error';
						$data['msg']    = esc_html__( 'Something went wrong! Instagram share API is not connected yet', 'the-real-estate-voice' );
						add_post_meta( $post->ID, 'sc_error', $data );
					}

					if ( file_exists( $insta_watermark ) ) {
						unlink( $insta_watermark );

					}
				} else {

					$api_token              = get_option( 'rev_api_token' );
					$id                     = $post->ID;
					$title                  = $post->post_title;
					$desc                   = $post->post_content;
					$page_id                = get_post_meta( $id, 'revoice_scheduled_page_id', true );
					$post_id                = get_post_meta( $id, 'revoice_scheduled_post_id', true );
					
					$sharable_agent         = get_post_meta( $id, 'revoice_scheduled_agenet_details', true );

					$revoice_post_type      = get_post_meta( $id, 'revoice_post_type', true );
					
					$revoice_listing_detail = '';

					if ( 'listings' == $revoice_post_type || 'listing' == $revoice_post_type || 'property' == $revoice_post_type ) {

						$revoice_listing_detail = get_post_meta( $id, 'revoice_listing_detail', true );
						$desc                   = $revoice_listing_detail . " \r\n\n " . $desc;
						if ( ! empty( $sharable_agent ) ) {
							$desc .= " \r\n\n " . $sharable_agent;
						}
					}

					$status   = esc_html__( 'Auto Shared', 'the-real-estate-voice' );
					$page_ids = explode( ',', $page_id );

					foreach ( $page_ids as $page_id ) {

						$bytes         = random_bytes( 20 );
						$rand          = bin2hex( $bytes );
						$get_pages_api = TREV_CSMAP_FACEBOOK_CONNECT_URL . 'facebook/api.php?type=share_in_page&api_token=' . $api_token . '&rand=' . $rand . '&page_id=' . $page_id . '&title=' . $title . '&desc=' . $desc . '&link=' . get_the_permalink( $post_id );

						$response = wp_remote_get(
							$get_pages_api,
							array(
								'timeout'     => 120,
								'httpversion' => '1.1',
							)
						);

						$responseBody = wp_remote_retrieve_body( $response );
						$result       = json_decode( $responseBody );
					}

					$pages = array();

					if ( is_object( $result ) && ! is_wp_error( $result ) ) {
						if ( 0 == $result->error ) {

							if( isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform'] ) {

								trev_csmap_update_preperty_meta( $post_id, 'revoice_shared', current_datetime() );
								trev_csmap_update_preperty_meta( $post_id, 'revoice_sharing_status', $status );

							}else{
								update_post_meta( $post_id, 'revoice_shared', current_datetime() );
								update_post_meta( $post_id, 'revoice_sharing_status', $status );	
							}
							

						} else {
							add_post_meta( $post->ID, 'sc_error', $result->msg );
							wp_update_post(
								array(
									'ID'          => $post->ID,
									'post_status' => 'draft',
								)
							);
						}
					} else {
						$data['status'] = 'error';
						$data['msg']    = esc_html__( 'Something went wrong!', 'the-real-estate-voice' );
						add_post_meta( $post->ID, 'sc_error', esc_html__( 'Something went wrong!', 'the-real-estate-voice' ) );
						wp_update_post(
							array(
								'ID'          => $post->ID,
								'post_status' => 'draft',
							)
						);
					}
				}
			}
		}
	}
}

add_action( 'transition_post_status', 'trev_csmap_share_schedule_post', 10, 3 );

if ( ! function_exists( 'trev_csmap_revoice_limit' ) ) {
	/**
	 * Function to limit the string
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_revoice_limit( $var, $limit ) {
		if ( strlen( $var ) > $limit ) {
			return substr( $var, 0, $limit ) . '...';
		} else {
			return $var;
		}
	}
}

if ( ! function_exists( 'trev_csmap_og_meta' ) ) {

	/**
	 * Function to add OGTAGS
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_og_meta() {

		global $post, $rev_post_types, $wpdb;
		
		$revcp_settings_options  = get_option( 'auto_social_option_name' );
		$general_tab_option_name = get_option( 'general_tab_option_name' );
		
		if( isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform'] ) {
			
			remove_action( 'aioseo_meta_views', 'aioseo_filter_meta_views');

		}
	

		if ( is_array( $revcp_settings_options ) ) {
			$featured_img_url='';
			if ( isset( $revcp_settings_options['add_open_graph_tags_1'] ) && 1 == trim( $revcp_settings_options['add_open_graph_tags_1'] ) ) {
				$post_id = $post->ID;

				$title = get_the_title($post_id);

				$permalink = get_permalink($post_id);
				$postsubtitrare  = get_post_meta( $post_id, 'id-subtitrare', true );
				$post_subtitrare = get_post( $postsubtitrare );
				$content         = trev_csmap_revoice_limit( strip_tags( $post_subtitrare->post_content ), 297 );

				$fb_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );
				

				$featured_img_url = $fb_image[0];

				if ( isset( $general_tab_option_name['website_platform'] ) && 'SiteLoft' == $general_tab_option_name['website_platform'] ) {

					$post_id = $post->ID;

					$hero_image_thumbs = get_post_meta( $post->ID, 'thumbnails', true );
					if(!empty($hero_image_thumbs) && ! empty( $hero_image_thumbs[0]['full'] )){
						$featured_img_url = $hero_image_thumbs[0]['full'];
					}else{
						$hero_image_full = get_post_meta( $post_id, 'images', true );
						if(!empty($hero_image_full)){
							$featured_img_url = $hero_image_full[0]['full'];	
						}
					}
					if( strpos($featured_img_url, '//') !== false ){

						$featured_img_url = str_replace('//', 'https://',$featured_img_url);	

					}
					
		
				}elseif (isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform']) {


					$actual_link = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

					$parts = parse_url($actual_link);

					$id = explode('/', $parts['path']);

					$post_id = end($id);
					
					if(!empty($post_id)){

						$qry_img ="SELECT * FROM attachments WHERE parent_id = $post_id";	
						$image_res = $wpdb->get_results( $qry_img );	
					}
	
					

					if(!empty($image_res)){
						$featured_img_url= $image_res[0]->url;
					}

					if(!empty($post_id)){
						$qry_post="SELECT * FROM properties WHERE id=$post_id";	
						$qry_res = $wpdb->get_results( $qry_post );	
					}
					
					if(!empty($qry_res)){
						$title     = $qry_res[0]->headline;
						$content   = $qry_res[0]->description;
						$permalink = get_site_url().'/'.$post_id;	
					}
					

					$featured_img_url = $featured_img_url.'.png';
					
						
				}
				else{
					 $fb_image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );
					$post_id = $post->ID;

					$featured_img_url = $fb_image[0];

					$title     = get_the_title($post_id);

					$permalink = get_permalink($post_id);

					$postsubtitrare  = get_post_meta( $post_id, 'id-subtitrare', true );
					$post_subtitrare = get_post( $postsubtitrare );
					$content         = trev_csmap_revoice_limit( strip_tags( $post_subtitrare->post_content ), 297 );
				}

				$postType = get_post_type();

				if ( in_array($postType, $rev_post_types) || is_single() ) {
								
					?>

		<meta property="og:title" content="<?php echo esc_html($title); ?>"/>
		<meta property="og:description" content="<?php echo esc_attr( $content ); ?>" />
		<meta property="og:url" content="<?php echo esc_url($permalink); ?>"/>
		<meta property="og:image" content="<?php echo esc_url( $featured_img_url ); ?>" />
		<meta property="og:image:url" content="<?php echo esc_url( $featured_img_url ); ?>" />
		<meta property="og:image:secure_url" content="<?php echo esc_url( $featured_img_url );?>" />

		<meta property="og:image:type" content="image/png" />
		<meta property="og:image:width" content="1120">
		<meta property="og:image:height" content="500">
		
		<meta property="og:type" content="<?php	 echo esc_attr('article');?>" />
		<meta property="og:site_name" content="<?php bloginfo( 'name' ); ?>"/>
					<?php
				}
			}
		}
	}
}
add_action( 'wp_head', 'trev_csmap_og_meta',1 );

