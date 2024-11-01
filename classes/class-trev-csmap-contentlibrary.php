<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Generic Contect Library page functionality.
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */

if ( ! class_exists( 'TREV_CSMAP_Revoice_ContentLibrary' ) ) {

	class TREV_CSMAP_Revoice_ContentLibrary {

		private $content_libraray_options;

		/**
		 * Init contruct
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		//  added by indiasan start 
		public function __construct() {
			// add_action( 'admin_menu', array( $this, 'trev_csmap_library_add_plugin_page' ) );
        //  added by indiasan end 
		}

		/**
		 * Add submenu to the setting menu
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_library_add_plugin_page() {

			add_submenu_page(
				'trev',
				esc_html__( 'Media Hub', 'the-real-estate-voice' ),
				esc_html__( 'Library', 'the-real-estate-voice' ),
				'manage_options',
				'trev-csmap-media-hub',
				array( $this, 'trev_csmap_library_create_admin_page' ),
				1
			);

		}

		/**
		 * Handle function to display setting page for library
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_library_create_admin_page() {

			$this->content_libraray_options = get_option( 'content_libraray_option_name' );
			$revcp_settings_options         = get_option( 'revcp_settings_option_name' );?>

			<div class="wrap">
				<?php

				$feed        = TREV_CSMAP_FEED_URL;
				$link_banner = TREV_CSMAP_LIB_BANNER_LINK;

				?>

				<?php if ( ! empty( $link_banner ) ) { ?>
					<a href="<?php echo esc_url( $link_banner ); ?>" target="_blank">
				<?php } ?>

				<img class="banner_image" src="<?php echo esc_url( TREV_CSMAP_LIB_BANNER_URL ); ?>">
				<?php if ( ! empty( $link_banner ) ) { ?>
					</a>
				<?php } ?>

				<?php

				settings_errors();
				$active_tab    = 'main';
				$page          = 1;
				$post_per_page = 50;
				$config        = false;

				if ( isset( $_GET['tab'] ) ) {

					$active_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
				}

				if ( isset( $_GET['paged'] ) ) {

					$page = sanitize_text_field( wp_unslash( $_GET['paged'] ) );

				}

				if ( ! empty( TREV_CSMAP_FEED_URL ) ) {

					$config = true;

					$feed_0 = TREV_CSMAP_FEED_URL;

				}
				?>

				<h2 class="nav-tab-wrapper">

					<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-media-hub&tab=main' ) ); ?>" class="nav-tab <?php echo ( $active_tab == 'main' ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Articles', 'the-real-estate-voice' ); ?></a>

					<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-media-hub&tab=order' ) ); ?>" class="nav-tab <?php echo ( $active_tab == 'order' ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Order Content', 'the-real-estate-voice' ); ?></a>

					<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-media-hub&tab=my-content' ) ); ?>" class="nav-tab <?php echo ( $active_tab == 'my-content' ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'My Content', 'the-real-estate-voice' ); ?></a>

				</h2>

				<div class="tabs-content">
					<?php
					if ( 'main' == $active_tab ) {

						if ( $config ) {
							include TREV_CSMAP_DIR_PATH . 'includes/order-tab.php';
						} else {
							?>
							<center><h3><?php echo esc_html__( 'Please Add Feed URL in settings page!', 'the-real-estate-voice' ); ?>
							 </h3></center>
							<?php
						}
					} elseif ( 'my-content' == $active_tab ) {
						include TREV_CSMAP_DIR_PATH . 'includes/my-content-tab.php';
					} else {
						include TREV_CSMAP_DIR_PATH . 'includes/main-tab.php';
					}
					?>
				</div>

			

		</div>
			<?php
		}

	}
}

if ( is_admin() ) {
	$content_libraray = new TREV_CSMAP_Revoice_ContentLibrary();
}


