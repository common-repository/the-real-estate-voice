<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_init', 'trev_csmap_check_for_autoshare_enabled' );

if ( ! function_exists( 'trev_csmap_check_for_autoshare_enabled' ) ) {

	/**
	 * Callback function to run auto post on setting save
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_check_for_autoshare_enabled() {

		if ( isset( $_POST['auto_post_option_name']['auto_enabled_1'] ) && ( 'yes' == $_POST['auto_post_option_name']['auto_enabled_1'] || '1' == $_POST['auto_post_option_name']['auto_enabled_1'] ) ) {

			$auto_post_option_name = get_option( 'auto_post_option_name' );
			$updated               = false;

			if ( ! isset( $auto_post_option_name['auto_enabled_1'] ) || 'yes' != $auto_post_option_name['auto_enabled_1'] ) {
				$updated                                 = true;
				$auto_post_option_name['auto_enabled_1'] = 'yes';

			} elseif ( isset( $_POST['auto_post_option_name']['auto_maximum_posts_per_check_4'] ) ) {
				$updated               = true;
				$auto_post_option_name = get_option( 'auto_post_option_name' );

				if ( $auto_post_option_name['auto_maximum_posts_per_check_4'] != $_POST['auto_post_option_name']['auto_maximum_posts_per_check_4'] ) {

					$auto_post_option_name['auto_maximum_posts_per_check_4'] = sanitize_text_field( wp_unslash( $_POST['auto_post_option_name']['auto_maximum_posts_per_check_4'] ) );
				}
			}

			if ( $updated ) {

				update_option( 'auto_post_option_name', $auto_post_option_name );
				trev_csmap_cron_add_post();

			}
		}

	}
}

if ( ! function_exists( 'trev_csmap_cron_add_post' ) ) {

	/**
	 * Cron callback function to add post from the feed
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_cron_add_post() {

		global $auto_frequency;

		if ( ! function_exists( 'post_exists' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		$auto_post_option_name = get_option( 'auto_post_option_name' );
		$general_tab_options   = get_option( 'general_tab_option_name' );
		$last_run              = get_option( 'auto_last_run' );
		$min_passed            = 0;

		if ( ! empty( $last_run ) ) {
			$currenttime = current_datetime()->getTimeStamp();
			$last_run    = $last_run->getTimeStamp();
			$diff_time   = ( $currenttime - $last_run ) / 60;
			$min_passed  = $diff_time;
		} else {

			if ( isset( $auto_frequency ) ) {
				$min_passed = $auto_frequency + 1;
			}
		}

		$success_pull = false;
		$config       = false;
		$enabled      = false;
		$posts_limit  = 10;
		$post_status  = 'publish';
		$user         = 1;
		$feed_0       = TREV_CSMAP_AUTO_POST_FEED_URL;

		$config = true;

		if ( isset( $auto_post_option_name['auto_enabled_1'] ) ) {

			$auto_enabled = $auto_post_option_name['auto_enabled_1'];

			if ( 'yes' == $auto_enabled ) {
				$enabled = true;
			} else {
				$enabled = false;
			}
		}

		if ( isset( $auto_post_option_name['auto_maximum_posts_per_check_4'] ) ) {
			$posts_limit = $auto_post_option_name['auto_maximum_posts_per_check_4'];
		}
		if ( isset( $auto_post_option_name['auto_attribute_posts_to_this_user_2'] ) ) {
			$user = $auto_post_option_name['auto_attribute_posts_to_this_user_2'];
		}

		if ( isset( $auto_post_option_name['auto_post_status_3'] ) ) {
			$post_status = $auto_post_option_name['auto_post_status_3'];
		}

		$postid = '';
		if ( $config && $enabled && trev_csmap_is_pro_license_plugin() ) {

			$rss                   = simplexml_load_file( $feed_0, 'SimpleXMLElement', LIBXML_NOCDATA );
			$rss_test              = ob_get_clean();
			$checked               = '';
			$eparm                 = '';
			$current_rss_count     = count( $rss->channel->item );
			$fb_post               = array();
			$alreay_exist          = 0;
			$new_added             = 0;
			$added_item            = 0;
			$main_post_add_counter = 0;

			foreach ( $rss->channel->item as $item ) {
				$thumbnailWithSize = array();
				$exist_id          = post_exists( $item->title, '', '', 'post' );

				if ( ! empty( $exist_id ) ) {
					$alreay_exist++;
					$main_post_add_counter++;
					continue; }

				if ( $alreay_exist > $posts_limit ) {
					break;
				}

				if ( $added_item < $posts_limit && $main_post_add_counter < $posts_limit ) {

					$categories = json_decode( wp_json_encode( $item->category ), true );
					$title      = $item->title;

					ob_start();
					echo $content = $item->children( 'content', true )->encoded;
					$contents     = ob_get_contents();
					ob_end_clean();
					$image = $item->children( 'media', true )->content->attributes()['url'];
					$cats  = array();
					if ( is_array( $categories ) ) {
						foreach ( $categories as $cat ) {
							$parent_term = term_exists( $cat, 'category' );
							if ( ! is_array( $parent_term ) ) {
								$term = wp_insert_term(
									$cat,
									'category'
								);
								if ( is_array( $term ) ) {
									$cats[] = $term['term_id'];
								}
							} else {
								$cats[] = $parent_term['term_id'];
							}
						}
					}

					ob_start();
					echo $description = $item->description;
					$descriptions     = ob_get_contents();
					ob_end_clean();
					$my_post = array(
						'post_title'    => strip_tags( $title ),
						'post_content'  => $contents,
						'post_status'   => $post_status,
						'post_excerpt'  => $descriptions,
						'post_author'   => $user,
						'post_category' => $cats,
					);

					$post_id = wp_insert_post( $my_post );
					$postid  = $post_id;
					update_post_meta( $post_id, 'revoice_sharing_status', 'Never Shared' );
					// Gethering detail for facebook post.
					$fb_post[ $post_id ]['title'] = strip_tags( $title );
					$fb_post[ $post_id ]['id']    = $post_id;
					$fb_post[ $post_id ]['desc']  = $descriptions;

					$response = wp_remote_get(
						$image,
						array(
							'timeout'   => 20,
							'sslverify' => false,
						)
					);

					if ( ! function_exists( 'is_plugin_active' ) ) {
						include_once ABSPATH . 'wp-admin/includes/plugin.php';
					}

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

					$new_added++;
					$added_item++;
					$main_post_add_counter++;
				}
			}

			update_option( 'auto_last_run', current_datetime() );
			update_option( 'auto_last_items', $new_added );
			update_option( 'auto_last_skipped', $alreay_exist );

			// Facebook Auto share
			
			$auto_instant_share       = false;
			$auto_schedule_share      = false;
			$enabled_sharing_for_post = false;
			$get_status_auto_share    = get_option( 'auto_post_option_name' );

			if ( isset( $get_status_auto_share['share_times'] ) && 0 == $get_status_auto_share['share_times'] ) {

				$auto_instant_share = true;
			} elseif ( isset( $get_status_auto_share['share_times'] ) && 1 == $get_status_auto_share['share_times'] ) {
				$auto_schedule_share = true;
			}
			

			if ( isset( $get_status_auto_share['enabled_sharing_for']['post'] ) && 1 == $get_status_auto_share['enabled_sharing_for']['post'] ) {
				$enabled_sharing_for_post = true;
			}
			$dripfeed_schedule = '';
			if ( isset( $get_status_auto_share['drip_feed_time'] ) && ! empty( $get_status_auto_share['drip_feed_time'] ) ) {

				$dripfeed_schedule = $get_status_auto_share['drip_feed_time'];
			}

			$api_token = get_option( 'rev_api_token' );

			$revoice_get_fb_pages = trev_csmap_get_fb_pages();

			$page_ids = $revoice_get_fb_pages['fb_ids'];

			$status = esc_html__( 'Never Shared', 'the-real-estate-voice' );

			// if enable enable article from facebook settings.
			if ( $enabled_sharing_for_post ) {

				foreach ( $fb_post as $key => $post_detail ) {

					$title   = $post_detail['title'];
					$post_id = $post_detail['id'];
					$desc    = $post_detail['desc'];

					// fb page loop.
					foreach ( $page_ids as $page_id ) {

						// If enebled instantly share.
						if ( $auto_instant_share ) {
							$status = esc_html__( 'Auto Shared', 'the-real-estate-voice' );
							$bytes  = random_bytes( 20 );
							$rand   = bin2hex( $bytes );

							$get_pages_api = trev_csmap_get_feed_url() . '/facebook/api.php?type=share_in_page&api_token=' . $api_token . '&rand=' . $rand . '&page_id=' . $page_id . '&title=' . $title . '&desc=' . $desc . '&link=' . get_the_permalink( $post_id );

							$response     = wp_remote_get(
								$get_pages_api,
								array(
									'timeout'     => 120,
									'httpversion' => '1.1',
								)
							);
							$responseBody = wp_remote_retrieve_body( $response );
							$result       = json_decode( $responseBody );

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
						}// If enebled instantly share - end

					}

					// If Drip feed enabled
					if ( $auto_schedule_share ) {
						
						$fb_page_ids       = implode( ',', $page_ids );
						$current_time_zone = get_option( 'timezone_string' );
						date_default_timezone_set( $current_time_zone );

						$date_now  = date( 'H:i' );
						$post_type = 'post';

						if ( $dripfeed_schedule > $date_now ) {
							$date_time = date( 'Y-m-d' ) . ' ' . $dripfeed_schedule;

						} else {
							date( 'Y-m-d', strtotime( date( 'Y-m-d' ) . ' +1 days' ) );
							$tomorrow  = date( 'Y-m-d', strtotime( '+1 days' ) );
							$date_time = $tomorrow . ' ' . $dripfeed_schedule;

						}

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

						$schedule_post_id = wp_insert_post( $post_data );

						if ( $schedule_post_id > 0 ) {
							update_post_meta( $schedule_post_id, 'revoice_scheduled', $date_time );
							update_post_meta( $schedule_post_id, 'revoice_scheduled_status', 'scheduled' );
							update_post_meta( $schedule_post_id, 'revoice_scheduled_post_id', $post_id );
							update_post_meta( $schedule_post_id, 'revoice_post_type', $post_type );

							update_post_meta( $post_id, 'revoice_scheduled_id', $schedule_post_id );
							update_post_meta( $post_id, 'revoice_sharing_status', $status );
							update_post_meta( $schedule_post_id, 'revoice_scheduled_page_id', $fb_page_ids );
						}
					}
				}
			}

			update_post_meta( $postid, 'revoice_sharing_status', $status );

		}
	}
}

add_action( 'admin_init', 'trev_csmap_pull_the_articles' );

if ( ! function_exists( 'trev_csmap_pull_the_articles' ) ) {
	/**
	 * Construct function
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_pull_the_articles() {

		if ( isset( $_GET['page'] ) && 'auto-post' == $_GET['page'] && isset( $_GET['pull'] ) && 1 == $_GET['pull'] ) {

			trev_csmap_cron_add_post();
			$refresh_url = admin_url( 'admin.php?page=auto-post' );
			wp_redirect( $refresh_url );
			exit;
		}
	}
}



add_filter( 'cron_schedules', 'trev_csmap_cron_add_article' );

if ( ! function_exists( 'trev_csmap_cron_add_article' ) ) {

	/**
	 * Add custom cron interval time
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_cron_add_article( $schedules ) {

		global $auto_frequency;

		$schedules['every_frequency_minutes'] = array(
			'interval' => 60 * $auto_frequency,
			'display'  => esc_html__( 'Every 30 Minutes', 'the-real-estate-voice' ),
		);

		return $schedules;
	}
}


add_action( 'trev_csmap_cron_add_article_auto', 'trev_csmap_action_get_article_feed' );

if ( ! function_exists( 'trev_csmap_action_get_article_feed' ) ) {
	/**
	 * Cron callback function for adding post
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_action_get_article_feed() {
		trev_csmap_cron_add_post();
	}
}


add_action( 'trev_csmap_cron_check_license', 'trev_csmap_action_check_license_pro' );

if ( ! function_exists( 'trev_csmap_action_check_license_pro' ) ) {

	/**
	 * Cron callback function to verify license post
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_action_check_license_pro() {
		$get_pro_url    	= trev_csmap_licence_version_url();
		$revcp_settings 	= get_option( 'revcp_settings_option_name' );
		$auto_post_settings = get_option( 'auto_post_option_name' );

		update_option( 'get_pro_url', $get_pro_url );
		$revcp_settings['pro_version'] = 0;

		if ( is_array($get_pro_url) && !empty( $get_pro_url ) ) {
			$revcp_settings['pro_version'] = 1;
			$library_cats                  = trev_csmap_article_library_categories();

			$temp = array();
			foreach ( $library_cats as $cat_id => $cat_name ) {
				$temp[ $cat_name ] = $cat_id;
			}

			$revcp_settings['library_cats'] = $temp;
			$auto_post_settings['auto_maximum_posts_per_check_4'] = 2;
		}
		
		update_option( 'revcp_settings_option_name', $revcp_settings );
		update_option( 'auto_post_option_name', $auto_post_settings );
	}
}
