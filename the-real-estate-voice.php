<?php
/**
 * Plugin Name:       The Real Estate Voice: Content and Social Media Auto Posting
 * Plugin URI:        https://wordpress.org/plugins/the-real-estate-voice/
 * Description:       The only all-in-one automated social media marketing WordPress plugin to help you harness your real estate voice and boost your profile.
 * Version:           1.3.3
 * Requires at least: 5.2
 * Requires PHP:      7.0
 * Author:            The Real Estate Voice
 * Author URI:        https://www.therealestatevoice.com.au/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       the-real-estate-voice
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Function to check is paid version or free
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_is_pro_license_plugin() {

	$plugin_setting = get_option( 'revcp_settings_option_name' );

	if ( isset( $plugin_setting['pro_version'] ) && 1 == $plugin_setting['pro_version'] ) {
		return true;
	}
	return false;
}

if ( ! defined( 'TREV_CSMAP_PLUGIN_VERSION' ) ) {
	define( 'TREV_CSMAP_PLUGIN_VERSION', '1.1' );
}
if ( ! defined( 'TREV_CSMAP_PRO_VERSION_API' ) ) {
	define( 'TREV_CSMAP_PRO_VERSION_API', 'https://trev.partica.com/api/plugin' );
}
if ( ! defined( 'TREV_CSMAP_GRAPH_API' ) ) {
	define( 'TREV_CSMAP_GRAPH_API', 'https://graph.facebook.com/' );
}
if ( ! defined( 'TREV_CSMAP_REALESTATEVOICE_URL' ) ) {
	define( 'TREV_CSMAP_REALESTATEVOICE_URL', 'https://www.therealestatevoice.com.au/' );
}
if ( ! defined( 'TREV_CSMAP_FACEBOOK_CONNECT_URL' ) ) {
	define( 'TREV_CSMAP_FACEBOOK_CONNECT_URL', TREV_CSMAP_REALESTATEVOICE_URL );
}
if ( ! defined( 'TREV_CSMAP_CALENDAR_BANNER' ) ) {
	define( 'TREV_CSMAP_CALENDAR_BANNER', TREV_CSMAP_REALESTATEVOICE_URL . 'images/social_calendar_banner.svg' );
}
if ( ! defined( 'TREV_CSMAP_LIB_BANNER_URL' ) ) {
	if ( trev_csmap_is_pro_license_plugin() ) {
		define( 'TREV_CSMAP_LIB_BANNER_URL', TREV_CSMAP_REALESTATEVOICE_URL . 'images/content_center_banner.svg' );
	} else {
		define( 'TREV_CSMAP_LIB_BANNER_URL', TREV_CSMAP_REALESTATEVOICE_URL . 'images/content_center_banner_free.svg' );
	}
}

if ( ! defined( 'TREV_CSMAP_SOCIAL_BANNER_URL' ) ) {
	if ( trev_csmap_is_pro_license_plugin() ) {
		define( 'TREV_CSMAP_SOCIAL_BANNER_URL', TREV_CSMAP_REALESTATEVOICE_URL . 'images/facebook_sharing_banner.svg' );
	} else {
		define( 'TREV_CSMAP_SOCIAL_BANNER_URL', TREV_CSMAP_REALESTATEVOICE_URL . 'images/content_center_banner_free.svg' );
	}
}

if ( ! defined( 'TREV_CSMAP_LIB_BANNER_LINK' ) ) {
	if ( trev_csmap_is_pro_license_plugin() ) {
		define( 'TREV_CSMAP_LIB_BANNER_LINK', '' );
	} else {
		define( 'TREV_CSMAP_LIB_BANNER_LINK', TREV_CSMAP_REALESTATEVOICE_URL . 'license-plugin/' );

	}
}

if ( ! defined( 'TREV_CSMAP_INSTAGRAM_BANNER_URL' ) ) {
	define( 'TREV_CSMAP_INSTAGRAM_BANNER_URL', TREV_CSMAP_REALESTATEVOICE_URL . 'images/instagram_sharing_banner.svg' );
}
if ( ! defined( 'TREV_CSMAP_SUBSCRIBE_LINK' ) ) {
	define( 'TREV_CSMAP_SUBSCRIBE_LINK', TREV_CSMAP_REALESTATEVOICE_URL . 'license-plugin/' );
}
if ( ! defined( 'TREV_CSMAP_DIR_URL' ) ) {
	define( 'TREV_CSMAP_DIR_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'TREV_CSMAP_DIR_PATH' ) ) {
	define( 'TREV_CSMAP_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'TREV_CSMAP_DIR' ) ) {
	define( 'TREV_CSMAP_DIR', dirname( __FILE__ ) ); // plugin dir.
}
if ( ! defined( 'TREV_CSMAP_BASENAME' ) ) {
	define( 'TREV_CSMAP_BASENAME', basename( TREV_CSMAP_DIR ) ); // base name.
}
if ( ! defined( 'TREV_CSMAP_DASHBOARD_BANNER' ) ) {
	define( 'TREV_CSMAP_DASHBOARD_BANNER', TREV_CSMAP_REALESTATEVOICE_URL . 'images/dashboard_banner.svg' );
}
if ( ! defined( 'TREV_CSMAP_SETTINGS_BANNER' ) ) {
	define( 'TREV_CSMAP_SETTINGS_BANNER', TREV_CSMAP_REALESTATEVOICE_URL . 'images/settings_banner.svg' );
}
if ( ! defined( 'TREV_CSMAP_SOCAIL_BANNER' ) ) {
	define( 'TREV_CSMAP_SOCAIL_BANNER', TREV_CSMAP_REALESTATEVOICE_URL . 'images/social_graph_banner.jpg' );
}

if ( ! defined( 'TREV_CSMAP_ORDER_CONENT_URL' ) ) {
	define( 'TREV_CSMAP_ORDER_CONENT_URL', 'https://www.therealestatevoice.com.au/order-content/?v=843949991' );
}

if ( ! defined( 'TREV_CSMAP_AUTOMATION_BANNER' ) ) {
	define( 'TREV_CSMAP_AUTOMATION_BANNER', 'https://www.therealestatevoice.com.au/images/automation_banner.svg' );
}

if ( ! defined( 'TREV_CSMAP_HELP_LINK' ) ) {
	define( 'TREV_CSMAP_HELP_LINK', 'http://support.therealestatevoice.com.au/' );
}

require TREV_CSMAP_DIR_PATH . 'classes/class-trev-csmap-revoice-calendar.php';
require TREV_CSMAP_DIR_PATH . 'includes/hooks.php';
require TREV_CSMAP_DIR_PATH . 'classes/class-trev-csmap-schedules-table.php';
require TREV_CSMAP_DIR_PATH . 'includes/settings-tab.php';
require TREV_CSMAP_DIR_PATH . 'classes/class-trev-csmap-settings.php';
require TREV_CSMAP_DIR_PATH . 'classes/class-trev-csmap-contentlibrary.php';
require TREV_CSMAP_DIR_PATH . 'includes/ajax.php';
require TREV_CSMAP_DIR_PATH . 'includes/advance-ajax.php';
require TREV_CSMAP_DIR_PATH . 'includes/cron.php';
require TREV_CSMAP_DIR_PATH . 'includes/custom-functions.php';

$get_pro_url = get_option( 'get_pro_url' );
	
$selected_country = trev_selected_country();

if ( !defined( 'TREV_CSMAP_FREE_VER_URL' ) ) {

	switch ($selected_country) {
		case 'UK':
			define( 'TREV_CSMAP_FREE_VER_URL', 'https://therevoice.com/freeuklibrary' );
			break;

		case 'NZ':
			define( 'TREV_CSMAP_FREE_VER_URL', 'https://therevoice.com/freenzlibrary' );
			break;

		case 'USA':
			define( 'TREV_CSMAP_FREE_VER_URL', 'https://therevoice.com/freeuslibrary' );
			break;

		case 'AUS':
			define( 'TREV_CSMAP_FREE_VER_URL', 'https://therevoice.com/freeauslibrary' );
			break;
		
		default:
			define( 'TREV_CSMAP_FREE_VER_URL', 'https://therevoice.com/freeauslibrary' );	
			break;
	}

}

if ( !defined( 'TREV_CSMAP_PRO_VER_URL' ) ) {

	switch ($selected_country) {
		case 'UK':
			define( 'TREV_CSMAP_PRO_VER_URL', 'https://therevoice.com/uklibrary' );
			break;

		case 'NZ':
			define( 'TREV_CSMAP_PRO_VER_URL', 'https://therevoice.com/nzlibrary' );
			break;

		case 'USA':
			define( 'TREV_CSMAP_PRO_VER_URL', 'https://therevoice.com/uslibrary' );
			break;

		case 'AUS':
			define( 'TREV_CSMAP_PRO_VER_URL', 'https://therevoice.com/auslibrary' );
			break;
		
		default:
			define( 'TREV_CSMAP_PRO_VER_URL', 'https://therevoice.com/auslibrary' );	
			break;
	}

}

if ( ! defined( 'TREV_CSMAP_FEED_URL' ) ) {

	if ( trev_csmap_is_pro_license_plugin() ) {
		if ( isset( $get_pro_url['libraryRSS'] ) && !empty( $get_pro_url['libraryRSS'] ) ) {
			define( 'TREV_CSMAP_FEED_URL', $get_pro_url['libraryRSS'] );
		} else {
			define( 'TREV_CSMAP_FEED_URL', TREV_CSMAP_PRO_VER_URL . '?call_custom_simple_rss=1&csrp_thumbnail_size=larg' );
		}
	} else {
		define( 'TREV_CSMAP_FEED_URL', TREV_CSMAP_FREE_VER_URL . '?call_custom_simple_rss=1&csrp_thumbnail_size=large&' );
	}

}

	
	
if ( ! defined( 'TREV_CSMAP_AUTO_POST_FEED_URL' ) ) {

	if ( trev_csmap_is_pro_license_plugin() ) {
		if ( isset( $get_pro_url['autopostRSS'] ) && !empty( $get_pro_url['autopostRSS'] ) ) {
			define( 'TREV_CSMAP_AUTO_POST_FEED_URL', $get_pro_url['autopostRSS'] );
		} else {
			define( 'TREV_CSMAP_AUTO_POST_FEED_URL', TREV_CSMAP_PRO_VER_URL . '?call_custom_simple_rss=1&csrp_thumbnail_size=large' );
		}
	} else {
		define( 'TREV_CSMAP_AUTO_POST_FEED_URL', TREV_CSMAP_FREE_VER_URL . '?call_custom_simple_rss=1&csrp_thumbnail_size=large' );
	}

}

	
if ( ! defined( 'TREV_CSMAP_INSTAGRAM_FEED_URL' ) ) {
	$free_version_url = 'https://therevoice.com/instagram/?call_custom_simple_rss=1&csrp_thumbnail_size=full';
	if ( trev_csmap_is_pro_license_plugin() ) {
		if ( isset( $get_pro_url['instagramRSS'] ) && ! empty( $get_pro_url['instagramRSS'] ) ) {
			define( 'TREV_CSMAP_INSTAGRAM_FEED_URL', $get_pro_url['instagramRSS'] );
		} else {
			define( 'TREV_CSMAP_INSTAGRAM_FEED_URL', $free_version_url );
		}
	} else {
		define( 'TREV_CSMAP_INSTAGRAM_FEED_URL', $free_version_url );
	}
}

add_action( 'admin_enqueue_scripts', 'trev_csmap_plugin_function' );


	/**
	 * Callback function to include the scripts on WordPress backend
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
function trev_csmap_plugin_function() {

	$share_after_adding       = false;
	$auto_instant_share       = false;
	$auto_schedule_share      = false;
	$enabled_sharing_for_post = false;
	$get_status_auto_share    = get_option( 'auto_post_option_name' );
	$revcp_settings           = get_option( 'revcp_settings_option_name' );

	$instagram_error_msg = esc_html__( 'Please connect to Facebook before selecting your Instagram Page.', 'the-real-estate-voice' );

		if( isset( $get_status_auto_share['share_after_adding'] ) && 1 == $get_status_auto_share['share_after_adding'] ){
			$share_after_adding = true;
		}
	
		if ( isset( $get_status_auto_share['share_times'] ) && 0 == $get_status_auto_share['share_times'] ) {

			$auto_instant_share = true;

		} elseif ( isset( $get_status_auto_share['share_times'] ) && 1 == $get_status_auto_share['share_times'] ) {
			$auto_schedule_share = true;
		}
	

	if ( isset( $get_status_auto_share['enabled_sharing_for']['post'] ) && 1 == $get_status_auto_share['enabled_sharing_for']['post'] ) {
		$enabled_sharing_for_post = true;
	}

	$get_fb_pages = trev_csmap_get_fb_pages();
	$ig_user_data = '';

	if ( ! empty( get_option( 'revoice_get_insta_data' ) ) ) {
		$get_insta_pages = get_option( 'revoice_get_insta_data' );
		$ig_user_data    = $get_insta_pages['ig_user_data'];
	}

	$version_status  = false;
	$is_listing_tab  = false;
	$is_holidays_tab = false;
	$current_page    = '';
	$current_tab     = '';
	$cat_tab         = '';

	if ( trev_csmap_is_pro_license_plugin() ) {
		$version_status = true;
	}

	if ( isset( $_GET['page'] ) && ! empty( $_GET['page'] ) ) {
		$current_page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
	}

	if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
		$current_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
	}

	if ( isset( $_GET['cat_tab'] ) && ! empty( $_GET['cat_tab'] ) ) {
		$cat_tab = sanitize_text_field( wp_unslash( $_GET['cat_tab'] ) );
	}

	if ( 'trev-csmap-social-media' == $current_page && 'share_listing' == $current_tab ) {
		$is_listing_tab = true;

	}

	if ( $current_page ) {

		$premium_message = array(
			'premium_title'         => esc_html__( 'This is a premium feature available for subscribers only!', 'the-real-estate-voice' ),
			'premium_v_description' => esc_html__( 'Subscribe to unlock all features and content', 'the-real-estate-voice' ),

			'premium_v_button'      => esc_html__( 'Unlock all features', 'the-real-estate-voice' ),
			'premium_v_link'        => TREV_CSMAP_SUBSCRIBE_LINK,
		);

		wp_register_style( 'trev-csmap-css', TREV_CSMAP_DIR_URL . 'css/trev-csmap-css.css', array(), TREV_CSMAP_PLUGIN_VERSION, 'all' );

		wp_register_script( 'trev-csmap-custom', TREV_CSMAP_DIR_URL . 'js/trev-csmap-custom.js', array( 'jquery' ), TREV_CSMAP_PLUGIN_VERSION, true );

		wp_register_script( 'trev-csmap-revoice', TREV_CSMAP_DIR_URL . 'js/trev-csmap-revoice.js', array( 'jquery' ), TREV_CSMAP_PLUGIN_VERSION, true );

		wp_register_script( 'trev-csmap-instagram', TREV_CSMAP_DIR_URL . 'js/trev-csmap-instagram.js', array( 'jquery' ), TREV_CSMAP_PLUGIN_VERSION, true );

		wp_register_script( 'trev-csmap-graph-chart', TREV_CSMAP_DIR_URL . 'js/chart.min.js', array( 'jquery' ), TREV_CSMAP_PLUGIN_VERSION, false );

		wp_register_script( 'trev-csmap-timepicker', TREV_CSMAP_DIR_URL . 'js/jquery-ui-timepicker-addon.js', array( 'jquery' ), TREV_CSMAP_PLUGIN_VERSION, true );

		wp_register_style( 'trev-csmap-jquery-ui', TREV_CSMAP_DIR_URL . 'css/trev-csmap-jquery-ui.css', array(), TREV_CSMAP_PLUGIN_VERSION, 'all' );

		wp_register_style( 'font-OpenSans', 'https://fonts.googleapis.com/css2?family=Bitter&family=Open+Sans&display=swap', array(), TREV_CSMAP_PLUGIN_VERSION );

		wp_register_style( 'trev-csmap-insta-style', TREV_CSMAP_DIR_URL . 'css/trev-csmap-insta-style.css', array(), TREV_CSMAP_PLUGIN_VERSION, 'all' );

		wp_enqueue_script( 'trev-csmap-custom' );

		wp_enqueue_script( 'sweetalert2@11', TREV_CSMAP_DIR_URL . 'js/sweetalert2.js', array( 'jquery' ), TREV_CSMAP_PLUGIN_VERSION, true );

		if ( 'trev-csmap-social-media' == $current_page || 'trev-csmap-calendar' == $current_page || 'trev-csmap-media-hub' == $current_page || ( 'trev-csmap-settings' == $current_page && isset( $current_tab ) && 'auto-social' == $current_tab ) ) {

			wp_enqueue_script( 'trev-csmap-revoice' );

			wp_localize_script(
				'trev-csmap-revoice',
				'revoiceDataAjax',
				array(
					'ajax_nonce'               => wp_create_nonce( 'revoice_nonce_fb' ),
					'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
					'share_after_adding'	   => $share_after_adding,
					'auto_instant_share'       => $auto_instant_share,
					'auto_schedule_share'      => $auto_schedule_share,
					'enabled_sharing_for_post' => $enabled_sharing_for_post,
					'fb_ids'                   => $get_fb_pages['fb_ids'],
					'fb_pages'                 => $get_fb_pages['fb_pages'],
					'fb_page_id'               => $get_fb_pages['fb_page_id'],
					'pages'                    => $get_fb_pages['pages'],
					'plugin_title'             => esc_html__( 'The Real Estate Voice', 'the-real-estate-voice' ),
					'plugin_version'           => $version_status,
					'subscribe_link'           => TREV_CSMAP_SUBSCRIBE_LINK,
					'is_listing_tab'           => $is_listing_tab,
					'premium_message'          => $premium_message,
					'current_page'             => $current_page,
					'current_tab'              => $current_tab,
					'fb_connect_link'          => admin_url( 'admin.php?page=trev-csmap-settings&tab=auto-social' ),
				)
			);
		}

		if ( 'trev-csmap-instagram' == $current_page ) {
			if ( isset( $_GET['cat_tab'] ) && 19 == sanitize_text_field( wp_unslash( $_GET['cat_tab'] ) ) || ! isset( $_GET['cat_tab'] ) ) {
				$is_holidays_tab = true;
			} else {
				$is_holidays_tab = false;
			}
		}

		if ( ( 'trev-csmap-settings' == $current_page && isset( $current_tab ) && 'auto-social-insta' == $current_tab ) || 'trev-csmap-instagram' == $current_page || 'trev-csmap-calendar' == $current_page || ( isset( $current_tab ) && 'trev-csmap-calendar' == $current_tab ) ) {
			$logo_url         = '';
			$hashtags         = '';
			$instagram_option = get_option( 'instagram_tab_option_name' );
			if ( ! empty( $instagram_option ) ) {
				$logo_id  = ! empty( $instagram_option['logo'] ) ? $instagram_option['logo'] : '';
				if(isset($instagram_option['hashtags'])){
					$hashtags = $instagram_option['hashtags'];	
				}
				
			}

			if ( ! empty( $logo_id ) ) {
				$logo_url = wp_get_attachment_url( $logo_id );
			}

			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}

			wp_enqueue_script( 'trev-csmap-instagram' );

			wp_localize_script(
				'trev-csmap-instagram',
				'revoiceInstaAjax',
				array(
					'ajax_nonce'               => wp_create_nonce( 'revoice_nonce_insta' ),
					'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
					
					'auto_instant_share'       => $auto_instant_share,
					'auto_schedule_share'      => $auto_schedule_share,
					'enabled_sharing_for_post' => $enabled_sharing_for_post,
					'plugin_title'             => esc_html__( 'The Real Estate Voice', 'the-real-estate-voice' ),
					'plugin_version'           => $version_status,
					'subscribe_link'           => TREV_CSMAP_SUBSCRIBE_LINK,
					'instagram_hashtags'       => $hashtags,
					'is_holidays_tab'          => $is_holidays_tab,
					'premium_message'          => $premium_message,
					'brand_logo'               => $logo_url,
					'get_insta_data'           => $ig_user_data,
					'instagram_error_msg'      => $instagram_error_msg,
					'instagram_connect_link'   => admin_url( 'admin.php?page=trev-csmap-settings&tab=auto-social-insta' ),

				)
			);
			wp_enqueue_style( 'trev-csmap-insta-style' );
		}

		wp_localize_script(
			'trev-csmap-custom',
			'revoiceDataAjax',
			array(
				'ajax_nonce' => wp_create_nonce( 'revoice_nonce_custom' ),
				'ajaxurl'    => admin_url( 'admin-ajax.php' ),
				'premium_message'          => $premium_message
			)
		);

		wp_enqueue_style( 'font-OpenSans' );
		if ( 'trev-csmap-dashboard' == $current_page || 'trev-csmap-media-hub' == $current_page || 'trev-csmap-social-media' == $current_page || 'trev-csmap-instagram' == $current_page || 'trev-csmap-calendar' == $current_page ) {
			wp_enqueue_style( 'wpb-google-fonts', 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700', false, TREV_CSMAP_PLUGIN_VERSION );
		}

		if ( 'trev-csmap-social-media' == $current_page || 'trev-csmap-calendar' == $current_page || 'trev-csmap-instagram' == $current_page || ( 'trev-csmap-settings' == $current_page && isset( $current_tab ) && 'auto-post' == $current_tab || 'trev-csmap-media-hub' == $current_page ) ) {

			wp_enqueue_style( 'trev-csmap-jquery-ui' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'trev-csmap-timepicker' );

		}
		if ( 'trev-csmap-dashboard' == $current_page ) {
			wp_enqueue_script( 'trev-csmap-graph-chart' );
		}

		if ( 'trev-csmap-media-hub' == $current_page && 'order' == $current_tab ) {
			wp_enqueue_style( 'popins-google-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;1,100;1,200&display=swap', false, TREV_CSMAP_PLUGIN_VERSION );
		}
	}

	wp_enqueue_style( 'trev-csmap-css' );
}


	/**
	 * Load Function on plugin activation
	 *
	 * This gets the plugin ready for translation.
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
function trev_csmap_plugin_loaded() {

	$revoice_lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
	$revoice_lang_dir = apply_filters( 'revoice_languages_directory', $revoice_lang_dir );

	// Traditional WordPress plugin locale filter.
	$locale = apply_filters( 'plugin_locale', get_locale(), 'the-real-estate-voice' );
	$mofile = sprintf( '%1$s-%2$s.mo', 'the-real-estate-voice', $locale );

	// Setup paths to current locale file.
	$mofile_local  = $revoice_lang_dir . $mofile;
	$mofile_global = WP_LANG_DIR . '/' . TREV_CSMAP_BASENAME . '/' . $mofile;

	if ( file_exists( $mofile_global ) ) { // Look in global.
		load_textdomain( 'the-real-estate-voice', $mofile_global );
	} elseif ( file_exists( $mofile_local ) ) { // Look in local.
		load_textdomain( 'the-real-estate-voice', $mofile_local );
	} else { // Load the default language files.
		load_plugin_textdomain( 'the-real-estate-voice', false, $revoice_lang_dir );
	}

	$user_id = get_current_user_id();

	if ( isset( $_GET['revoice-dismissed'] ) ) {
		update_user_meta( $user_id, 'revoice_notice_dismissed', 1 );
	}

	if ( version_compare( PHP_VERSION, '7.1.0', '<' ) && ! is_ssl() ) {
		$get_status = get_user_meta( $user_id, 'revoice_notice_dismissed', true );

		if ( ! $get_status ) {
			add_action( 'admin_notices', 'trev_csmap_activation_notice' );
		}
	}

}


add_action( 'plugins_loaded', 'trev_csmap_plugin_loaded' );

/**
 * Admin Notice on load plugin activation.
 *
 * @since 0.1.0
 */
function trev_csmap_activation_notice() {

	$curl = admin_url( sprintf( basename( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) ) );
	if ( strpos( $curl, 'admin.php' ) !== false ) {
		$revoice_dismissed = $curl . '&revoice-dismissed=1';
	} else {
		$revoice_dismissed = '?revoice-dismissed=1';
	}

	echo "<div class='trev_csmap_activation_notice error '>
    <p class='notice-wrap'><span>" . get_plugin_data( __FILE__ )['Name'] . esc_html__( '  requires SSL to be able to publish to instagram as your PHP version is 7.0 or less. Upgrade PHP or install SSL.', 'the-real-estate-voice' ) . "</span><span><a href='" . esc_html( $revoice_dismissed ) . "' class=''>Dismiss</a></span></p>
    </div>";

}

add_filter( 'custom_menu_order', 'trev_csmap_change_submenu_order' );

	/**
	 * Callback Function to re order the menu
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
function trev_csmap_change_submenu_order( $menu_order ) {
	global $submenu;

	if ( isset( $submenu['trev'] ) && is_array( $submenu['trev'][0] ) ) {
		unset( $submenu['trev'][0] );
		
		foreach ( $submenu['trev'] as $index => $item ) {
			if( !empty( $item[0] ) )
			{
				if ( ('Home') === $item[0] ) {
					$custom = $item;
					unset( $submenu['trev'][ $index ] );
					break;
				}
		    }
		}
		
			
		if ( ! empty( $custom ) ) {
			array_unshift( $submenu['trev'], $custom );
		}
	}

	return $menu_order;
}

add_action( 'init', 'trev_csmap_set_api_token' );

if ( ! function_exists( 'trev_csmap_set_api_token' ) ) {

	/**
	 * Callback Function to set the API token
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_set_api_token() {
		global $rev_post_types, $auto_frequency, $sharing_status;

		$general_tab_option_name = get_option( 'general_tab_option_name' );

		if ( isset( $general_tab_option_name['website_platform'] ) && 'SiteLoft' == $general_tab_option_name['website_platform'] ) {
			
			$listing = 'listings';
		}
		elseif ( isset( $general_tab_option_name['website_platform'] ) && 'Easy Property Listings' == $general_tab_option_name['website_platform'] ) { 

			$listing = 'property';

		}else {

			$listing = 'listing';

		}

		$rev_post_types = array(
			'post'    => 'post',
			'listing' => $listing,

		);

		$auto_frequency = 30;

		$get = get_option( 'rev_api_token' );

		if ( ! $get ) {
			$bytes = random_bytes( 20 );
			$token = bin2hex( $bytes );
			update_option( 'rev_api_token', $token );
		}

		$sharing_status = array(
			'Never Shared'        => esc_html__( 'Never Shared', 'the-real-estate-voice' ),
			'Auto Shared'         => esc_html__( 'Auto Shared', 'the-real-estate-voice' ),
			'Shared'              => esc_html__( 'Shared', 'the-real-estate-voice' ),
			'Currently scheduled' => esc_html__( 'Currently scheduled', 'the-real-estate-voice' )
		);
	}
}

register_activation_hook( __FILE__, 'trev_csmap_activation_callback' );

/**
 * Function to call initial base while plugin activating
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_activation_callback() {

	if ( version_compare( PHP_VERSION, '7.0.0', '<' ) ) {

		wp_die( 'Plugin could not activate as your PHP version is less than 7.0 Please upgrade the PHP version ' . trev_csmap_plugin_activation_notice() );

	}

	if ( ! wp_next_scheduled( 'trev_csmap_cron_add_article_auto' ) ) {
		wp_schedule_event( time(), 'every_frequency_minutes', 'trev_csmap_cron_add_article_auto' );
	}

	if ( ! wp_next_scheduled( 'trev_csmap_cron_check_license' ) ) {

		wp_schedule_event( time(), 'hourly', 'trev_csmap_cron_check_license' );
	}

	$uploadsurl = wp_upload_dir();
	$instagram  = $uploadsurl['basedir'] . '/instagram';

	if ( ! is_dir( $instagram ) ) {
		wp_mkdir_p( $instagram );

	}

	$get_pro_url = trev_csmap_licence_version_url();

	update_option( 'get_pro_url', $get_pro_url );

	$revcp_settings     = get_option( 'revcp_settings_option_name' );
	$auto_post_settings = get_option( 'auto_post_option_name' );
	$auto_social_option = get_option( 'auto_social_option_name');

	$revcp_settings['post_status_2'] = 'publish';
	$revcp_settings['attribute_posts_to_this_user_1'] = 1;

	$auto_post_settings['auto_attribute_posts_to_this_user_2'] = 1;
	$auto_post_settings['auto_post_status_3']                  = 'publish';

	$auto_social_option['add_open_graph_tags_1'] = 1;

	$revcp_settings['pro_version'] = 0;

	
	if ( is_array($get_pro_url) && ! empty( $get_pro_url ) ) {
		$revcp_settings['pro_version'] = 1;
		$library_cats                  = trev_csmap_article_library_categories();
		$auto_post_settings['auto_maximum_posts_per_check_4']      = 2;
		$temp = array();
		
		foreach ( $library_cats as $cat_id => $cat_name ) {
			$temp[ $cat_name ] = $cat_id;
		}

		$revcp_settings['library_cats'] = $temp;
	}

	update_option( 'revcp_settings_option_name', $revcp_settings );
	update_option( 'auto_post_option_name', $auto_post_settings );
	update_option( 'auto_social_option_name', $auto_social_option );
	
}

/**
 * Function to show notice on plugin activation.
 *
 * @since 0.1.0
 */
function trev_csmap_plugin_activation_notice() {
	return '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">' . esc_html__( 'Go back', 'the-real-estate-voice' ) . '</a>';
}

register_deactivation_hook( __FILE__, 'trev_csmap_deactivation_callback' );

/**
 * Function to callback on plugin deactivation
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_deactivation_callback() {
	wp_clear_scheduled_hook( 'trev_csmap_cron_add_article_auto' );
	wp_clear_scheduled_hook( 'trev_csmap_cron_check_license' );
	$user_id = get_current_user_id();
	delete_user_meta( $user_id, 'revoice_notice_dismissed' );
}

if ( ! function_exists( 'trev_csmap_watermark_custom_size' ) ) {

	/**
	 * Function to add custom size tom watermark image
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_watermark_custom_size() {
		add_image_size( 'watermark-size', 400, 200, false );
	}

	add_action( 'after_setup_theme', 'trev_csmap_watermark_custom_size' );
}
