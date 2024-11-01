<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles Setting page functionality.
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
if ( ! class_exists( 'TREV_CSMAP_Revoice_settings' ) ) {

	class TREV_CSMAP_Revoice_settings {

		private $revcp_settings_options;
		private $sc_list_table;
		private $general_tab_options;

		/**
		 * Init Contruct
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'trev_csmap_settings_page' ) );
			add_action( 'admin_init', array( $this, 'trev_csmap_settings_page_init' ) );
			add_action( 'admin_init', array( $this, 'trev_csmap_auto_post_page_init' ) );
			add_action( 'admin_init', array( $this, 'trev_csmap_auto_social_page_init' ) );
			add_action( 'admin_init', array( $this, 'trev_csmap_general_tab_admin_init' ) );
			add_action( 'admin_init', array( $this, 'trev_csmap_instagram_tab_admin_init' ) );
		}

		/**
		 * Add Settings menu
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_settings_page() {
			// add_menu_page(
			// 	esc_html__( 'The Real Estate Voice', 'the-real-estate-voice' ),
			// 	esc_html__( 'The Real Estate Voice', 'the-real-estate-voice' ),
			// 	'manage_options',
			// 	'trev-csmap-settings',
			// 	array( $this, 'trev_csmap_settings_create_initial_page' ),
			// 	'dashicons-category',
			// 	1
			// );

			add_submenu_page(
				'trev',
				esc_html__( 'Home', 'the-real-estate-voice' ),
				esc_html__( 'Home', 'the-real-estate-voice' ),
				'manage_options',
				'trev-csmap-dashboard',
				array( $this, 'trev_csmap_create_admin_page_media_hub' ),
				5
			);

			// add_submenu_page(
			// 	'trev',
			// 	esc_html__( 'Social Media', 'the-real-estate-voice' ),
			// 	esc_html__( 'Facebook', 'the-real-estate-voice' ),
			// 	'manage_options',
			// 	'trev-csmap-social-media',
			// 	array( $this, 'trev_csmap_social_media_admin_page' ),
			// 	6
			// );

			// add_submenu_page(
			// 	'trev',
			// 	esc_html__( 'Instagram', 'the-real-estate-voice' ),
			// 	esc_html__( 'Instagram', 'the-real-estate-voice' ),
			// 	'manage_options',
			// 	'trev-csmap-instagram',
			// 	array( $this, 'trev_csmap_instagram_media_page' ),
			// 	7
			// );

			// add_submenu_page(
			// 	'trev',
			// 	esc_html__( 'Scheduled & Calendar', 'the-real-estate-voice' ),
			// 	esc_html__( 'Calendar', 'the-real-estate-voice' ),
			// 	'manage_options',
			// 	'trev-csmap-calendar',
			// 	array( $this, 'trev_csmap_calendar_and_schedule_page' ),
			// 	7
			// );

			add_submenu_page(
				'options-general.php',
				esc_html__( 'The Real Estate Voice', 'the-real-estate-voice' ),
				esc_html__( 'The Real Estate Voice', 'the-real-estate-voice' ),
				'manage_options',
				'trev-csmap-settings',
				array( $this, 'trev_csmap_settings_create_admin_page' ),
				8
			);

			// add_submenu_page(
			// 	'trev',
			// 	esc_html__( 'Help', 'the-real-estate-voice' ),
			// 	esc_html__( 'Help', 'the-real-estate-voice' ),
			// 	'manage_options',
			// 	'trev-csmap-help',
			// 	array( $this, 'trev_csmap_help_menu' ),
			// 	8
			// );

		}



		/**
		 * Callback for Help menu
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_help_menu() {
			
		}

		public function trev_csmap_settings_create_initial_page() {

		}

		/**
		 * Callback for Dasboard menu
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_create_admin_page_media_hub() {
			?>
			<div class="wrap">
				<?php

				$logourl             = '';
				$revoice_insta_pages = get_option( 'revoice_get_insta_data' );

				$fb_ids = get_option( 'fb_pages_ids' );

				if ( isset( $revoice_insta_pages['ig_user_ids'] ) ) {
					$ig_user_ids = $revoice_insta_pages['ig_user_ids'];
				}

				$instagram_setting = get_option( 'instagram_tab_option_name' );

				if ( ! empty( $instagram_setting['logo'] ) ) {
					$logourl = wp_get_attachment_url( $instagram_setting['logo'] );
				}

				if ( trev_csmap_is_pro_license_plugin() ) {
					$free_link = '';
					echo '<img class="banner_image" src="' . esc_url( TREV_CSMAP_DASHBOARD_BANNER ) . '" alt="' . esc_attr( 'Dashboard' ) . '" />';
				} else {
					echo '<a href="' . esc_url( TREV_CSMAP_LIB_BANNER_LINK ) . '" target="_blank">';
					echo '<img class="banner_image" src="' . esc_url( TREV_CSMAP_SOCIAL_BANNER_URL ) . '">';
					echo '</a>';
				}

				?>


			<div class="welcome-section">	

					<div class="welcome-header">
						<h1><?php esc_html_e( 'Welcome to your real estate voice', 'the-real-estate-voice' ); ?></h1>
						<?php if ( empty( $fb_ids ) || empty( $ig_user_ids ) || $logourl == '' ) { ?>
						<p><?php esc_html_e( 'To get the most out of your plugin, please complete the following steps in the settings:', 'the-real-estate-voice' ); ?></p>	
						<?php } ?> 
					</div>

					<div class="steps-list">
						<ul>
							<?php if ( empty( $fb_ids ) ) { ?>

								<li>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-settings&tab=auto-social' ) ); ?>"><img src="<?php echo esc_url( TREV_CSMAP_DIR_URL . '/images/step1.png' ); ?>" alt="<?php echo esc_attr( 'Connect to facebook', 'the-real-estate-voice' ); ?>" ></a>
								</li>

							<?php } ?>


							<?php if ( empty( $ig_user_ids ) ) { ?>

								<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-settings&tab=auto-social-insta' ) ); ?>">
									<img src="<?php echo esc_url( TREV_CSMAP_DIR_URL . '/images/step2.png' ); ?>" alt="<?php esc_attr_e( 'Select your instagram page', 'the-real-estate-voice' ); ?>" >	</a>
								</li>
							<?php } ?>

							<?php if ( empty( $logourl ) ) { ?>

								<li><a href="<?php echo esc_url( admin_url( '/admin.php?page=trev-csmap-settings&tab=auto-social-insta' ) ); ?>">
									<img src="<?php echo esc_url( TREV_CSMAP_DIR_URL . '/images/step3.png' ); ?>"  alt="<?php echo esc_attr( 'Choose a branding logo', 'the-real-estate-voice' ); ?>" ></a>
								</li>

							<?php } ?>

						</ul>



					</div>	
					<div class="automation-settings">
						<?php 
						include TREV_CSMAP_DIR_PATH . 'includes/trev-csmap-automation-settings.php';
						 ?>
					</div>
			</div>
		</div>
			<?php
			include TREV_CSMAP_DIR_PATH . 'includes/revoice-social-graph.php';

		}

		/**
		 * Callback for Calendar & Schedule menu
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_calendar_and_schedule_page() {
			$calendar_banner = TREV_CSMAP_CALENDAR_BANNER;
			?>

			<div class="wrap">
			<?php

				$banner    = TREV_CSMAP_SOCIAL_BANNER_URL;
				$free_link = TREV_CSMAP_LIB_BANNER_LINK;

				if ( trev_csmap_is_pro_license_plugin() ) {
					$free_link = '';
					echo '<img class="banner_image" src="' . esc_url( $calendar_banner ) . '" alt="' . esc_attr( 'Your Social Media Planner' ) . '" />';
				} else {
					echo '<a href="' . esc_url( $free_link ) . '" target="_blank">';
					echo '<img class="banner_image" src="' . esc_url( $banner ) . '">';
					echo '</a>';
				}

				settings_errors();

				$active_tab = 'calendar';

				$page = 1;

				$post_per_page = 10;

				$config = true;

				if ( isset( $_GET['tab'] ) ) {

					$active_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );

				}
				if ( isset( $_GET['paged'] ) ) {

					$page = sanitize_text_field( wp_unslash( $_GET['paged'] ) );

				}

				$args                = array(
					'public' => true,
				);
				$post_types          = get_post_types( $args, 'names' );
				$current_post_type   = '';
				$current_post_status = '';
				$post_statuses       = get_post_statuses();

				?>

				<h2 class="nav-tab-wrapper">

				<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-calendar&tab=calendar' ) ); ?>" class="nav-tab <?php echo ( $active_tab == 'calendar' ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Calendar', 'the-real-estate-voice' ); ?></a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-calendar&tab=scheduled' ) ); ?>" class="nav-tab <?php echo ( $active_tab == 'scheduled' ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'List', 'the-real-estate-voice' ); ?></a>

				</h2>

				<div class="tabs-content">

					<?php
					if ( 'calendar' == $active_tab ) {
						include TREV_CSMAP_DIR_PATH . 'includes/calendar-tab.php';
					} else {

						$this->last_logins_options = get_option( 'last_logins_option_name' );

						$this->sc_list_table->prepare_items();
						include TREV_CSMAP_DIR_PATH . 'includes/scheduled-tab.php';

					}

					?>

				</div>


		</div>
			<?php

		}

		/**
		 * Callback Render the settings fields
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_settings_create_admin_page() {

			if ( isset( $_GET['tab'] ) && 'general' == $_GET['tab'] ) {
				?>

					<div class="wrap" id="general_tab_settings">	
						<?php
						settings_errors();

						$this->revcp_settings_options = get_option( 'revcp_settings_option_name' );
						?>	

						<form method="post" action="options.php">
							<?php
							settings_fields( 'general_tab_admin_group' );
							do_settings_sections( 'general-tab-admin' );
							submit_button();
							?>
						</form>
					</div>

				<?php

			} elseif ( isset( $_GET['tab'] ) && 'auto-social-insta' == $_GET['tab'] ) {
				$this->auto_post_options = get_option( 'instagram_tab_admin_group' );
				?>

				<div class="wrap">
	
					<?php settings_errors(); ?>

					<form method="post" action="options.php">
						<?php
						settings_fields( 'instagram_tab_admin_group' );
						do_settings_sections( 'instagram-tab-admin' );
						submit_button();
						?>
					</form>
				</div>
				<?php

			} elseif ( isset( $_GET['page'] ) ) {

				if(trev_csmap_is_pro_license_plugin()){

					$this->auto_post_options  = get_option( 'auto_post_option_name' );

					?>

					<div class="wrap">

						<?php settings_errors(); ?>

						<form method="post" action="options.php">
							<?php
							settings_fields( 'auto_post_option_group' );?>
						
							<?php do_settings_sections( 'auto-post-admin' ); ?>
						
							<?php do_settings_sections( 'auto-post-admin-fb' ); ?>

							<!-- added by indiansan start -->
							<?php 
							settings_fields( 'general_tab_admin_group' );
							?>

							<?php
							 do_settings_sections( 'general-tab-admin' ); 
							?>
							<!-- added by indiansan end -->

							<?php submit_button();	?>
						</form>
					</div>

				<?php } else{ 
					echo trev_csmap_get_settings_tab_buttons();
					?>

						<a class="trev-automation-banner" href="<?php echo esc_url( TREV_CSMAP_LIB_BANNER_LINK ) ;?>" target="_blank"><img class="banner_image" src="<?php echo esc_url( TREV_CSMAP_AUTOMATION_BANNER ) ;?>" alt="<?php echo esc_attr('Automation is a premium feature','the-real-estate-voice')?>">
						</a>

				<?php } ?>


		<?php } elseif ( isset( $_GET['tab'] ) && 'auto-social' == $_GET['tab'] ) { ?>

			<div class="wrap" id="facebook_settings">
				<?php
				settings_errors();

				$this->auto_social_options = get_option( 'auto_social_option_name' );
				?>

				<form method="post" action="options.php">
					<?php
					settings_fields( 'auto_social_option_group' );
					do_settings_sections( 'auto-social-admin' );
					submit_button();
					?>
				</form>
			</div>

				<?php
		} else {

			$this->revcp_settings_options = get_option( 'revcp_settings_option_name' );
			?>

			<div class="wrap">
	
				<?php settings_errors(); ?>

				<form method="post" action="options.php">
					<?php

					settings_fields( 'revcp_settings_option_group' );
					do_settings_sections( 'revcp-settings-admin' );
					submit_button();

					?>
				</form>
			</div>

			<?php
		}
		}

		/**
		 * Register the plugin setting
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_settings_page_init() {

			$this->sc_list_table = new TREV_CSMAP_Schedules_Table();

			register_setting(
				'revcp_settings_option_group',
				'revcp_settings_option_name',
				array( $this, 'trev_csmap_settings_sanitize' )
			);

			add_settings_section(
				'revcp_settings_setting_section',
				'',
				array( $this, 'trev_csmap_settings_section_info' ),
				'revcp-settings-admin'
			);

			add_settings_field(
				'attribute_posts_to_this_user_1',
				esc_html__( 'Attribute posts to this user', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_attribute_posts_to_user_callback' ),
				'revcp-settings-admin',
				'revcp_settings_setting_section'
			);

			add_settings_field(
				'post_status_2',
				esc_html__( 'Post status', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_post_status_callback' ),
				'revcp-settings-admin',
				'revcp_settings_setting_section'
			);

			if ( trev_csmap_is_pro_license_plugin() ) {
				add_settings_field(
					'library_cats',
					esc_html__( 'Categories', 'the-real-estate-voice' ),
					array( $this, 'trev_csmap_library_categories_callback' ),
					'revcp-settings-admin',
					'revcp_settings_setting_section'
				);
			}

			add_settings_field(
				'pro_version',
				'',
				array( $this, 'trev_csmap_pro_version_callback' ),
				'revcp-settings-admin',
				'revcp_settings_setting_section'
			);

		}

		/**
		 * Sanitize each of setting fields value
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_settings_sanitize( $input ) {

			$sanitary_values = array();

			if ( isset( $input['attribute_posts_to_this_user_1'] ) ) {
				$sanitary_values['attribute_posts_to_this_user_1'] = $input['attribute_posts_to_this_user_1'];
			}

			if ( isset( $input['post_status_2'] ) ) {
				$sanitary_values['post_status_2'] = $input['post_status_2'];
			}

			if ( isset( $input['library_cats'] ) ) {
				$sanitary_values['library_cats'] = $input['library_cats'];
			}

			if ( isset( $input['pro_version'] ) ) {
				$sanitary_values['pro_version'] = $input['pro_version'];
			} else {
				$auto_social_options = get_option( 'auto_post_option_name' );
				if ( isset( $auto_social_options['enabled_sharing_for'] ) ) {
					unset( $auto_social_options['enabled_sharing_for'] );
					update_option( 'auto_post_option_name', $auto_social_options );
				}
			}

			return $sanitary_values;
		}

		/**
		 * Function to display setting tab
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_settings_section_info() {
			echo trev_csmap_get_settings_tab_buttons();
		}

		/**
		 * Facebook account select custom callback function for setting
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_attribute_posts_to_user_callback() {

			$args  = array(
				'orderby' => 'ID',
				'order'   => 'ASC',
			);
			$users = get_users( $args );

			?>
			<select name="revcp_settings_option_name[attribute_posts_to_this_user_1]" id="attribute_posts_to_this_user_1">
			<?php $selected = ( isset( $this->revcp_settings_options['attribute_posts_to_this_user_1'] ) && 'user ' === $this->revcp_settings_options['attribute_posts_to_this_user_1'] ) ? 'selected' : ''; ?>

			<?php
			foreach ( $users as $user ) {
				$selected = '';
				if ( isset( $this->revcp_settings_options['attribute_posts_to_this_user_1'] ) ) {
					if ( $user->ID == $this->revcp_settings_options['attribute_posts_to_this_user_1'] ) {
						$selected = 'selected';
					}
				}
				?>

				<option value="<?php echo esc_attr( $user->ID ); ?>" <?php echo esc_attr( $selected ); ?>> <?php echo esc_html( $user->display_name ); ?></option>
			<?php } ?> 

			</select>

		<p class="description">
			<i><?php esc_html_e( 'Articles from the library will be posted by this user', 'the-real-estate-voice' ); ?></i>
		</p>
			<?php
		}

		public function trev_csmap_post_status_callback() {

			?>
			<select name="revcp_settings_option_name[post_status_2]" id="post_status_2">
			<?php $selected = ( isset( $this->revcp_settings_options['post_status_2'] ) && $this->revcp_settings_options['post_status_2'] === 'publish' ) ? 'selected' : ''; ?>
			<option value="publish" <?php echo esc_attr( $selected ); ?>> <?php esc_html_e( 'Publish', 'the-real-estate-voice' ); ?></option>
			<?php $selected = ( isset( $this->revcp_settings_options['post_status_2'] ) && $this->revcp_settings_options['post_status_2'] === 'draft' ) ? 'selected' : ''; ?>
			<option value="draft" <?php echo esc_attr( $selected ); ?>> <?php esc_html_e( 'Draft', 'the-real-estate-voice' ); ?></option>
			<?php $selected = ( isset( $this->revcp_settings_options['post_status_2'] ) && $this->revcp_settings_options['post_status_2'] === 'pending' ) ? 'selected' : ''; ?>
			<option value="pending" <?php echo esc_attr( $selected ); ?>> <?php esc_html_e( 'Pending', 'the-real-estate-voice' ); ?></option>
			</select> 
			<p class="description">
				<i><?php esc_html_e( 'Publish articles immediately or put into draft mode', 'the-real-estate-voice' ); ?></i>
			</p>
			<?php
		}


		/**
		 * Callback function to save setting for pro field
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_pro_version_callback() {

			$rev_checkbox_field = isset( $this->revcp_settings_options['pro_version'] ) ? $this->revcp_settings_options['pro_version'] : '';

			if ( ! isset( $rev_checkbox_field ) || empty( $rev_checkbox_field ) ) {
				$this->revcp_settings_options['library_cats'] = array();
			}

			?>
			<div class="library_cats_wrap">
				<input type='hidden' id="plugin-license" name='revcp_settings_option_name[pro_version]' <?php checked( $rev_checkbox_field, 1 ); ?> value='<?php echo esc_attr( $this->revcp_settings_options['pro_version'] ); ?>'> 
			</div>
			<?php
		}

		/**
		 * Callback function for category custom field
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_library_categories_callback() {

			// Checkbox of library content - feed
			
			$revoice_country = trev_selected_country();
			$library_cats    = trev_csmap_article_library_categories( $revoice_country );

			$i = 0;
			foreach ( $library_cats as $cat_id => $library_cat ) {
				$selected = '';

				$rev_checkbox_field = isset( $this->revcp_settings_options['library_cats'] ) ?
				(array) $this->revcp_settings_options['library_cats'] : array();
				?>

				<div class="library_cats_wrap">
					<input type='checkbox' id="<?php echo esc_attr( $library_cat ); ?>" name='revcp_settings_option_name[library_cats][<?php echo esc_attr( $library_cat ); ?>]' <?php checked( in_array( $cat_id, $rev_checkbox_field ), 1 ); ?> value='<?php echo esc_attr( $cat_id ); ?>'> <label for="<?php echo esc_attr( $library_cat ); ?>"><?php echo esc_html( $library_cat ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
				</div>
				<?php
				$i++;

			}
			?>
				<p class="description">
					<i><?php esc_html_e( 'Select the article categories you want displayed in the library', 'the-real-estate-voice' ); ?></i>
				</p>
			<?php

		}

		/**
		 * Instagram Media tab setting field
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_instagram_media_page() {
			$instagram_banner = TREV_CSMAP_INSTAGRAM_BANNER_URL;

			?>
			<div class="wrap">
				<?php
				$banner    = TREV_CSMAP_SOCIAL_BANNER_URL;
				$free_link = TREV_CSMAP_LIB_BANNER_LINK;

				if ( trev_csmap_is_pro_license_plugin() ) {
					$free_link = '';
					echo '<img class="banner_image" src="' . esc_url( $instagram_banner ) . '" alt="' . esc_attr( 'Instagram Library' ) . '" />';
				} else {
					echo '<a href="' . esc_url( $free_link ) . '" target="_blank">';
					echo '<img class="banner_image" src="' . esc_url( $banner ) . '">';
					echo '</a>';
				}
				?>
			</div>
			<?php

			settings_errors();

				$active_tab    = '19';
				$page          = 1;
				$post_per_page = 20;
				$config        = true;

			if ( isset( $_GET['cat_tab'] ) ) {

				$active_tab = sanitize_text_field( wp_unslash( $_GET['cat_tab'] ) );

			}
			if ( isset( $_GET['paged'] ) ) {

				$page = sanitize_text_field( wp_unslash( $_GET['paged'] ) );

			}

				$args = array(
					'public' => true,
				);

				$post_types             = get_post_types( $args, 'names' );
				$current_post_type      = '';
				$current_post_status    = '';
				$post_statuses          = get_post_statuses();
				$revcp_settings_options = get_option( 'revcp_settings_option_name' );

				$categories = array();
				if ( empty( $categories ) || ! trev_csmap_is_pro_license_plugin() ) {
					$temp_cat = trev_csmap_instagram_library_categories();
					foreach ( $temp_cat as $key => $cat_val ) {
						$categories[ $cat_val ] = $key;
					}
				}
				?>
				<h2 class="nav-tab-wrapper">
					<?php
					foreach ( $categories as $value => $cat_id ) {

						?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-instagram&cat_tab="' . $cat_id ) ); ?>" class="nav-tab <?php echo ( $active_tab == $cat_id ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php echo esc_html( $value ); ?></a>
					<?php } ?>
				</h2>

				<div class="tabs-content">

					<?php
					if ( $active_tab ) {
						include TREV_CSMAP_DIR_PATH . 'includes/instagram/days-tab.php';
					}
					?>

				</div>
				<?php
		}
		/**
		 * Social Media tab setting field
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_social_media_admin_page() {
			?>
	
			<div class="wrap">
				<?php
				$banner    = TREV_CSMAP_SOCIAL_BANNER_URL;
				$free_link = TREV_CSMAP_LIB_BANNER_LINK;
				if ( trev_csmap_is_pro_license_plugin() ) {
					$free_link = '';
				}
				?>
				<?php
				if ( ! empty( $free_link ) ) {
					echo '<a href="' . esc_url( $free_link ) . '" target="_blank">';}
				?>
				<img class="banner_image" src="<?php echo esc_url( $banner ); ?>">
				<?php
				if ( ! empty( $free_link ) ) {
					echo '</a>';}
				?>
	
				<?php
				settings_errors();

				$active_tab = 'share';

				$page = 1;

				$post_per_page = 10;

				$config = true;

				if ( isset( $_GET['tab'] ) ) {

					$active_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );

				}
				if ( isset( $_GET['paged'] ) ) {

					$page = sanitize_text_field( wp_unslash( $_GET['paged'] ) );

				}

				$args                = array(
					'public' => true,
				);
				$post_types          = get_post_types( $args, 'names' );
				$current_post_type   = '';
				$current_post_status = '';
				$post_statuses       = get_post_statuses();

				?>

				<h2 class="nav-tab-wrapper">

					<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-social-media&tab=share' ) ); ?>" class="nav-tab <?php echo ( $active_tab == 'share' ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Content', 'the-real-estate-voice' ); ?></a>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-social-media&tab=share_listing' ) ); ?>" class="nav-tab <?php echo ( $active_tab == 'share_listing' ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Listings', 'the-real-estate-voice' ); ?></a>
	
				</h2>

				<div class="tabs-content">

					<?php
					if ( 'share' == $active_tab ) {

						if ( $config ) {
							include TREV_CSMAP_DIR_PATH . 'includes/share-tab.php';
						} else {
							echo '<center><h3>' . esc_html__( 'Please Add Feed URL in settings page!', 'the-real-estate-voice' ) . '</h3></center>';

						}
					} elseif ( 'share_listing' == $active_tab ) {

						if ( $config ) {
							include TREV_CSMAP_DIR_PATH . 'includes/share-listing-tab.php';
						} else {
							echo '<center><h3>' . esc_html__( 'Please Add Feed URL in settings page!', 'the-real-estate-voice' ) . '</h3></center>';
						}
					} elseif ( 'calendar' == $active_tab ) {
						include TREV_CSMAP_DIR_PATH . 'includes/calendar_tab.php';
					} else {

						$this->last_logins_options = get_option( 'last_logins_option_name' );

						$this->sc_list_table->prepare_items();
						include TREV_CSMAP_DIR_PATH . 'includes/scheduled_tab.php';

					}

					?>

				</div>


		</div>
				
			<?php
		}



		/**
		 * Function to register all setting and fields
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_auto_social_page_init() {

			register_setting(
				'auto_social_option_group',
				'auto_social_option_name',
				array( $this, 'trev_csmap_auto_social_sanitize' )
			);
			add_settings_field(
				'add_open_graph_tags_2',
				'',
				array( $this, 'trev_csmap_connect_facebook' ),
				'auto-social-admin',
				'auto_social_setting_section'
			);
			add_settings_section(
				'auto_social_setting_section',
				esc_html__( '', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_auto_social_section_info' ),
				'auto-social-admin'
			);
			


			add_settings_field(
				'add_open_graph_tags_1',
				esc_html__( 'Add Open Graph Tags', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_open_graph_tags_callback' ),
				'auto-social-admin',
				'auto_social_setting_section'
			);

		}

		/**
		 * Function to register all setting and fields for general tab
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_general_tab_admin_init() {
			$this->general_tab_options = get_option( 'general_tab_option_name' );

			register_setting(
				'general_tab_admin_group',
				'general_tab_option_name',
				array( $this, 'trev_csmap_general_tab_sanitize' )
			);

			add_settings_section(
				'general_tab_setting_section',
				esc_html__( '', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_general_section_info' ),
				'general-tab-admin'
			);

			add_settings_field(
				'website_platform',
				esc_html__( 'Select your website platform', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_general_website_platform' ),
				'general-tab-admin',
				'general_tab_setting_section'
			);

			add_settings_field(
				'revoice_country',
				esc_html__( 'Select Country', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_country_selection_callback' ),
				'general-tab-admin',
				'general_tab_setting_section'
			);

			add_settings_field(
				'trev_check_licence',
				esc_html__( 'Check Licence', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_check_licence_callback' ),
				'general-tab-admin',
				'general_tab_setting_section'
			);

		}


		/**
		 * Function to register all setting and fields for instagram tab
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_instagram_tab_admin_init() {

			$this->instagram_tab_options = get_option( 'instagram_tab_option_name' );

			register_setting(
				'instagram_tab_admin_group',
				'instagram_tab_option_name',
				array( $this, 'trev_csmap_instagram_tab_sanitize' )
			);

			add_settings_section(
				'instagram_tab_setting_section',
				esc_html__( '', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_instagram_section_info' ),
				'instagram-tab-admin'
			);

			add_settings_field(
				'instagram_connect',
				esc_html__( 'Connect to instagram', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_connect_instagram' ),
				'instagram-tab-admin',
				'instagram_tab_setting_section'
			);

			add_settings_field(
				'instagram_logo',
				esc_html__( 'Brand Logo', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_instagram_logo_callback' ),
				'instagram-tab-admin',
				'instagram_tab_setting_section'
			);

			add_settings_field(
				'instagram_hashtags',
				esc_html__( 'Default Hashtags', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_instagram_hashtags_callback' ),
				'instagram-tab-admin',
				'instagram_tab_setting_section'
			);

		}


		/**
		 * Handle to Section info on general tab
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		// added by indiasan start
		public function trev_csmap_general_section_info() {
			// echo trev_csmap_get_settings_tab_buttons();
		}
		// added by indiasan end

		/**
		 * Handle to Section info on instagram tab
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_instagram_section_info() {
			echo trev_csmap_get_settings_tab_buttons();
		}

		/**
		 * Handle to sanitize the fields values
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_auto_social_sanitize( $input ) {
			$sanitary_values = array();
		

			if ( isset( $input['add_open_graph_tags_1'] ) ) {
				$sanitary_values['add_open_graph_tags_1'] = $input['add_open_graph_tags_1'];
			}

			if ( isset( $input['preferred_share_time_2'] ) ) {
				$sanitary_values['preferred_share_time_2'] = $input['preferred_share_time_2'];
			}

			if ( isset( $input['select_types_to_share_3'] ) ) {
				$sanitary_values['select_types_to_share_3'] = $input['select_types_to_share_3'];
			}

			return $sanitary_values;
		}

		/**
		 * Handle to sanitize the fields values
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_general_tab_sanitize( $input ) {

			$sanitary_values = array();

			if ( isset( $input['website_platform'] ) ) {
				$sanitary_values['website_platform'] = $input['website_platform'];
			}

			if ( isset( $input['revoice_country'] ) ) {
				$sanitary_values['revoice_country'] = $input['revoice_country'];
			}

			return $sanitary_values;
		}

		/**
		 * Handle to sanitize instagram fields values
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_instagram_tab_sanitize( $input ) {

			$sanitary_values = array();

			if ( isset( $input['logo'] ) ) {
				$sanitary_values['logo'] = $input['logo'];
			}

			if ( isset( $input['hashtags'] ) ) {
				$sanitary_values['hashtags'] = $input['hashtags'];
			}

			return $sanitary_values;
		}


		/**
		 * Display setting tab buttons
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_auto_social_section_info() {
			echo trev_csmap_get_settings_tab_buttons();
		}


		/**
		 * Callback function for general tab setting
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_general_website_platform() {

			$website_platform = array(

				'Generic'              => esc_html__( 'Generic', 'the-real-estate-voice' ),
				'WPCasa'               => esc_html__( 'WPCasa', 'the-real-estate-voice' ),
				'Website Blue'         => esc_html__( 'Website Blue', 'the-real-estate-voice' ),
				//'Agentpoint' 		   => esc_html__('Agentpoint','the-real-estate-voice'),
				'Easy Property Listings' => esc_html__( 'Easy Property Listings', 'the-real-estate-voice' ),
				'SiteLoft'               => esc_html__( 'SiteLoft', 'the-real-estate-voice' )

			);

			?>

			<select name="general_tab_option_name[website_platform]" id="website_platform">
				<?php

			foreach ( $website_platform as $key => $platform ) {
				$selected = ( isset( $this->general_tab_options['website_platform'] ) && $this->general_tab_options['website_platform'] == $key ) ? 'selected' : '';	?>

				<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $selected ); ?> > <?php echo esc_html( $platform ); ?></option>

			<?php } ?>

				</select> 
		
				<p class="description">
					<i><?php esc_html_e( "Select your website provider.", 'the-real-estate-voice' ); ?></i>
				</p>

			<?php
		}

		/**
		 * Callback function for general tab setting
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_country_selection_callback() {

			$country_selection = array(
				'AUS' => esc_html__( 'Australia', 'the-real-estate-voice' ),
				'NZ'  => esc_html__( 'New Zealand', 'the-real-estate-voice' ),
				'UK'  => esc_html__( 'UK', 'the-real-estate-voice' ),
				'USA' => esc_html__( 'USA', 'the-real-estate-voice' )

			);

							
			?>

			<select name="general_tab_option_name[revoice_country]" id="revoice_country">
				<?php
				foreach ( $country_selection as $key => $country ) {
					$selected = ( isset( $this->general_tab_options['revoice_country'] ) && $this->general_tab_options['revoice_country'] == $key ) ? 'selected' : '';
					?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $selected ); ?> > <?php echo esc_html( $country ); ?></option>
				<?php } ?>
				</select> 

			
				<p class="description">
					<i><?php esc_html_e( ' ', 'the-real-estate-voice' ); ?></i>
				</p>

			<?php
		}


		/**
		 * Callback function for general tab setting
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_check_licence_callback() {

			?>
			<button id="check_licence" class="primary">
				<?php echo esc_html__('Check Licence','the-real-estate-voice');?>
			</button>

		<?php 
			
		}

		/**
		 * Callback function for instagram tab setting
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_instagram_logo_callback() {
			$instagram_settings = $this->instagram_tab_options;
			$logo_id            = ! empty( $instagram_settings['logo'] ) ? $instagram_settings['logo'] : '';
			$logo_url           = '';

			if ( ! empty( $logo_id ) ) {
				$logo_url = wp_get_attachment_url( $logo_id );
				$getImage = wp_get_attachment_image_src( $logo_id, 'watermark-size' );

				if ( empty( $getImage[3] ) ) {
					require_once ABSPATH . 'wp-admin/includes/image.php';
					$imagePath = wp_get_original_image_path( $logo_id );
					$metadata  = wp_generate_attachment_metadata( $logo_id, $imagePath );

					if ( ! is_wp_error( $metadata ) && ! empty( $metadata ) ) {
						wp_update_attachment_metadata( $logo_id, $metadata );
					}
				}
			}

			?>
			<button type="button" class="button button-primary" id="instagram-logo"><?php esc_html_e( 'Select Image', 'the-real-estate-voice' ); ?></button>
			<p class="description insta-brand-desc">
				<i><?php esc_html_e( 'Select a PNG logo for your branding', 'the-real-estate-voice' ); ?></i>
			</p>
			<div class="placeholder-image">
				<?php if ( ! empty( $logo_url ) ) { ?>
					<img src="<?php echo esc_url( $logo_url ); ?>">
					<div class="remove-brand" data-logo-id="<?php echo esc_attr( $logo_id ); ?>">&times;</div>
				<?php } ?>		
			
			</div>
			<input type="hidden" name="instagram_tab_option_name[logo]" id="instagram_brand_logo" value="<?php echo esc_attr( $logo_id ); ?>">
			

			<?php
		}

		/**
		 * Callback function for instagram tab setting
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_instagram_hashtags_callback() {

			$instagram_settings = $this->instagram_tab_options;
			$hashtags           = ! empty( $instagram_settings['hashtags'] ) ? $instagram_settings['hashtags'] : '';
			?>

			<textarea name="instagram_tab_option_name[hashtags]" id="default_hashtags" placeholder="#realestate #voicerealestate #iloverealestate
		
Voice Real Estate
📞 9999 99999
💻 www.voicerealestate.com
LINK IN BIO" rows="8" style="width:44%"><?php echo esc_html( $hashtags ); ?></textarea>
			<p class="description">
				<i><?php esc_html_e( 'Enter any content you always want to add when sharing', 'the-real-estate-voice' ); ?></i>
			</p>

			<?php
		}


		/**
		 * Callback function for display custom field for post type selection
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_auto_sharing_for_callback() {

			global $wp_post_types, $rev_post_types;

			$post_types = $rev_post_types;

			if ( ! trev_csmap_is_pro_license_plugin() && isset( $post_types['listing'] ) ) {
				unset( $post_types['listing'] );

			}

			
			foreach ( $post_types as $key => $post_option ) {
			
				$selected = ( isset( $this->auto_post_options['enabled_sharing_for'][ $key ] ) && 1 == $this->auto_post_options['enabled_sharing_for'][ $key ] ) ? 'checked' : '';
				
				if( 'post' == $post_option ){

					$post_name = esc_html__( 'Articles', 'the-real-estate-voice' );

				} else {

					$object = get_post_type_object( $post_option );
					if ( $object ) {

						$post_name = $object->label;
					} else {
						$post_name = $post_option;
					}
				}

				?>

					<input type="checkbox" id="<?php echo esc_attr( $post_option ); ?>" <?php echo esc_attr( $selected ); ?>  name="auto_post_option_name[enabled_sharing_for][<?php echo esc_attr( $key ); ?>]" value="1">
					<label for="<?php echo esc_attr( $post_option ); ?>"><?php echo esc_html( ucfirst( $post_name ) ); ?></label>&nbsp;&nbsp;

				<?php
			} ?>
			<p class="description">
				<i><?php esc_html_e( 'Automatically share new content to Facebook', 'the-real-estate-voice' ); ?></i>
			</p>
			<?php 
		}

		/**
		 * Callback function to show dialog of sharing article after adding from library
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_share_after_adding_callback() {
		
			$checked = ( isset( $this->auto_post_options['share_after_adding'] ) && 1 == $this->auto_post_options['share_after_adding'] ) ? 'checked' : '';
			?>

			<input type="checkbox" id="share_after_adding" <?php echo esc_attr( $checked ); ?>  name="auto_post_option_name[share_after_adding]" value="1">
			<label for="share_after_adding"><?php echo esc_html('Show share dialog','the-real-estate-voice' ); ?></label>&nbsp;&nbsp;
			<p class="description">
				<i><?php esc_html_e( 'Show share to Facebook after adding article from library', 'the-real-estate-voice' ); ?></i>
			</p>	
			<?php
			
		}

		/**
		 * Callback function for display custom field for time to post
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_auto_share_times_callback() {
			$selected_instant = ( isset( $this->auto_post_options['share_times'] ) && 0 == $this->auto_post_options['share_times'] ) ? 'selected' : '';

			$selected_drip = ( isset( $this->auto_post_options['share_times'] ) && 1 == $this->auto_post_options['share_times'] ) ? 'selected' : '';

			?>
			 
				<select name="auto_post_option_name[share_times]" id="share_times">

					<option value="0" <?php echo esc_attr( $selected_instant ); ?>> <?php esc_html_e( 'Instantly', 'the-real-estate-voice' ); ?></option>
					<option value="1" <?php echo esc_attr( $selected_drip ); ?>> <?php esc_html_e( 'Preferred Time', 'the-real-estate-voice' ); ?></option>
				</select> 

			
				<p class="description">
					<i><?php esc_html_e( 'Share straight away or preferred time', 'the-real-estate-voice' ); ?></i>
				</p>	
			<?php
		}

		/**
		 * Callback function for display custom field for time selection setting
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_share_schedule_times_callback() {

			if ( isset( $this->auto_post_options['share_times'] ) && 1 == $this->auto_post_options['share_times'] ) {

				$dripfeed_schedule = '';
				if ( isset( $this->auto_post_options['drip_feed_time'] ) && ! empty( $this->auto_post_options['drip_feed_time'] ) ) {
					$dripfeed_schedule = $this->auto_post_options['drip_feed_time'];
				}
			} else {
				$dripfeed_schedule = '';
			}

			?>

			<input type="text" placeholder="HH:mm" name="auto_post_option_name[drip_feed_time]" id="drip_feed_time" value="<?php echo esc_attr( $dripfeed_schedule ); ?>">

			<?php

		}


		/**
		 * Callback function for display custom field for instagram connect
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_connect_instagram() {
			?>
			<div class="social_page_connect">
				<h4 class="fb-box-title"><?php esc_html_e( 'Connect your instagram page', 'the-real-estate-voice' ); ?></h4>
				<?php
				if ( ! empty( get_option( 'revoice_get_insta_data' ) ) ) {

					$revoice_insta_pages = get_option( 'revoice_get_insta_data' );

					$ig_user_ids = $revoice_insta_pages['ig_user_ids'];
				}
				?>
				<div class="social_site_item">
					<div class="sc_item"><img src="<?php echo esc_url( TREV_CSMAP_DIR_URL ); ?>/images/instagram-icon.png"> <span><?php esc_html_e( 'Instagram', 'the-real-estate-voice' ); ?></span> </div>
					<?php
					if ( ! empty( $ig_user_ids ) ) {
						?>
					<div class="re-connect">
						<img class="insta_connect_btn" src="<?php echo esc_url( TREV_CSMAP_DIR_URL ); ?>/images/re-connect-small.png" alt="<?php esc_attr_e( 're-connect', 'the-real-estate-voice' ); ?>">

					</div>
				<?php } ?>
					<?php
					if ( isset( $_GET['accessTokenLong'] ) && ! empty( $_GET['accessTokenLong'] ) ) {
						$accessTokenLong = sanitize_text_field( $_GET['accessTokenLong'] );
						update_option( 'accessTokenLong', $accessTokenLong );
					}
					$callback = urlencode( get_admin_url() . 'admin.php?page=trev-csmap-settings&tab=auto-social-insta' );

					$bytes    = random_bytes( 20 );
					$rand     = bin2hex( $bytes );
					$perm     = get_option( 'rev_api_token' );
					$fb_pages = trev_csmap_get_fb_pages();
					$pageIds  = implode( ',', $fb_pages['fb_ids'] );

					if ( empty( $ig_user_ids ) ) {
						?>

				<div class="sc_item_button"><a href="javascript:void(0)" class="insta_connect_btn"><?php esc_html_e( 'Connect', 'the-real-estate-voice' ); ?></a></div>

					<?php } else { ?>

				<div class="sc_item_button">
					<a href="javascript:void(0)" data-ids="<?php echo esc_attr( wp_json_encode( $ig_user_ids ) ); ?>" class="facebook_connect_btn insta_connect_removes">
						<?php esc_html_e( 'Disconnect', 'the-real-estate-voice' ); ?>
					</a>
				</div>

			<?php } ?>	



			</div>
			<div class="connected_pages">

				<?php
				if ( ! empty( $revoice_insta_pages ) ) {

					foreach ( $revoice_insta_pages['ig_user_data'] as $ig_user_data ) {

						?>

					<div class="fb_page_name">
						<div class="fb_pg_name">
							<?php esc_html_e( 'Page:' ); ?>
							<strong><?php esc_html_e( $ig_user_data['displayname'], 'the-real-estate-voice' ); ?>	
							</strong><span><?php esc_html_e( '(' . $ig_user_data['username'] . ')', 'the-real-estate-voice' ); ?></span>
						</div>

						<div class="fb_pg_del">
							<button type="button" data-id="<?php echo esc_attr( $ig_user_data['id'] ); ?>" class="delete_fb_connect instagram_page_remove">
								<img src="<?php echo esc_url( TREV_CSMAP_DIR_URL ); ?>/images/trash_icon_transparent.png"> 
							</button>
						</div>
					</div>
						<?php
					}
				}
				?>
			</div>
		</div>		
			<?php
		}

		/**
		 * Callback function for display custom field for facebook connect
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_connect_facebook() {
			?>
			<div class="social_page_connect">
				<h4 class="fb-box-title"><?php esc_html_e( 'Connect your Facebook page', 'the-real-estate-voice' ); ?></h4>
				<div class="social_site_item">
					<div class="sc_item"><img src="<?php echo esc_url( TREV_CSMAP_DIR_URL ); ?>/images/facebook.png"> <span><?php esc_html_e( 'Facebook', 'the-real-estate-voice' ); ?></span> </div>
					<?php

					if ( isset( $_GET['accessTokenLong'] ) && ! empty( $_GET['accessTokenLong'] ) ) {
						$accessTokenLong = sanitize_text_field( $_GET['accessTokenLong'] );

						update_option( 'accessTokenLong', $accessTokenLong );
					}

					$callback = urlencode( get_admin_url() . 'admin.php?page=trev-csmap-settings&tab=auto-social' );

					$bytes    = random_bytes( 20 );
					$rand     = bin2hex( $bytes );
					$perm     = get_option( 'rev_api_token' );
					$fb_pages = trev_csmap_get_fb_pages();
					$pageIds  = implode( ',', $fb_pages['fb_ids'] );

					if ( empty( $fb_pages['pages'] ) ) {

						$fb_connect_url = 'https://therealestatevoice.com.au/facebook/connect.php?callback=' . $callback . '&rand=' . $rand . '&paermanent=' . $perm;

						?>
						<div class="sc_item_button"><a href="<?php echo esc_url( $fb_connect_url ); ?>" class="facebook_connect_btn"><?php esc_html_e( 'Connect', 'the-real-estate-voice' ); ?></a></div>

					<?php } else { ?>

						<div class="sc_item_button">
							<a href="javascript:void(0)" data-ids="<?php echo esc_attr( $pageIds ); ?>" class="facebook_connect_btn facebook_connect_removes">
								<?php esc_html_e( 'Disconnect', 'the-real-estate-voice' ); ?>
							</a>
						</div>


						<?php
					}
					?>

			</div>
			<div class="connected_pages">
				<?php
				foreach ( $fb_pages['fb_page_id'] as $page_id => $pageName ) {
					?>

					<div class="fb_page_name">
						<div class="fb_pg_name">
							<?php esc_html_e( 'Page:', 'the-real-estate-voice' ); ?> <strong><?php esc_html_e( $pageName, 'the-real-estate-voice' ); ?></strong>
						</div>
						
						<div class="fb_pg_del">
							<button type="button" data-id="<?php echo esc_attr( $page_id ); ?>" class="delete_fb_connect facebook_connect_remove">
								<img src="<?php echo esc_url( TREV_CSMAP_DIR_URL ); ?>/images/trash_icon_transparent.png"> 
							</button>
							
						</div>
						
					</div>
					
				<?php } ?>
			</div>
		</div>
		
			<?php

		}

		/**
		 * Callback function for display custom field for open graph tags
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_open_graph_tags_callback() {

			$selected = ( isset( $this->auto_social_options['add_open_graph_tags_1'] ) && 1 == $this->auto_social_options['add_open_graph_tags_1'] ) ? 'checked' : '';
			?>
			<input type="checkbox" id="add_open_graph_tags_1" <?php echo esc_attr( $selected ); ?>  name="auto_social_option_name[add_open_graph_tags_1]" value="1">
			<label for="add_open_graph_tags_1">
				<?php esc_html_e( 'Add Open Graph Tags', 'the-real-estate-voice' ); ?>
			</label>
			<p class="description">
				<i><?php esc_html_e( 'Check this to add Facebook tags to your articles', 'the-real-estate-voice' ); ?></i></p>	
				</div>
			<?php
		}



		public function trev_csmap_auto_post_page_init() {
			register_setting(
				'auto_post_option_group',
				'auto_post_option_name',
				array( $this, 'trev_csmap_auto_post_sanitize' )
			);

			add_settings_section(
				'auto_post_setting_section',
				esc_html__( '', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_auto_post_section_info' ),
				'auto-post-admin'
			);

			add_settings_section(
				'auto_post_fb_section',
				esc_html__( '', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_auto_share_section_info' ),
				'auto-post-admin-fb'
			);

			// add_settings_field(
			// 	'share_after_adding',
			// 	esc_html__( 'Share after adding', 'the-real-estate-voice' ),
			// 	array( $this, 'trev_csmap_share_after_adding_callback' ),
			// 	'auto-post-admin-fb',
			// 	'auto_post_fb_section'
			// );

			// add_settings_field(
			// 	'enabled_sharing_for',
			// 	esc_html__( 'Enabled Auto Sharing for', 'the-real-estate-voice' ),
			// 	array( $this, 'trev_csmap_auto_sharing_for_callback' ),
			// 	'auto-post-admin-fb',
			// 	'auto_post_fb_section'
			// );

			// add_settings_field(
			// 	'share_times',
			// 	esc_html__( 'Article Share Times', 'the-real-estate-voice' ),
			// 	array( $this, 'trev_csmap_auto_share_times_callback' ),
			// 	'auto-post-admin-fb',
			// 	'auto_post_fb_section'
			// );
			
			add_settings_field(
				'drip_feed_time',
				esc_html__( 'Preferred Time', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_share_schedule_times_callback' ),
				'auto-post-admin-fb',
				'auto_post_fb_section'
			);

			add_settings_field(
				'auto_enabled_1',
				esc_html__( 'Auto Post Enabled', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_auto_enabled_callback' ),
				'auto-post-admin',
				'auto_post_setting_section'
			);

			add_settings_field(
				'auto_attribute_posts_to_this_user_2',
				esc_html__( 'Attribute posts to this user', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_auto_attribute_posts_to_user_callback' ),
				'auto-post-admin',
				'auto_post_setting_section'
			);

			add_settings_field(
				'auto_post_status_3',
				esc_html__( 'Article status', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_auto_post_status_callback' ),
				'auto-post-admin',
				'auto_post_setting_section'
			);

			add_settings_field(
				'auto_maximum_posts_per_check_4',
				esc_html__( 'Article to Check', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_maximum_posts_per_check_callback' ),
				'auto-post-admin',
				'auto_post_setting_section'
			);

			add_settings_field(
				'auto_last_checked_6',
				esc_html__( 'Last Check', 'the-real-estate-voice' ),
				array( $this, 'trev_csmap_auto_last_checked_callback' ),
				'auto-post-admin',
				'auto_post_setting_section'
			);

			add_settings_field(
				'execute_cron_manually',
				'',
				array( $this, 'trev_csmap_execute_cron_manually' ),
				'auto-post-admin',
				'auto_post_setting_section'
			);

			// add_settings_field(
			// 	'website_platform',
			// 	esc_html__( 'Select your website platform', 'the-real-estate-voice' ),
			// 	array( $this, 'trev_csmap_general_website_platform' ),
			// 	'auto-post-admin',
			// 	'auto_post_setting_section'
			// );

			// add_settings_field(
			// 	'revoice_country',
			// 	esc_html__( 'Select Country', 'the-real-estate-voice' ),
			// 	array( $this, 'trev_csmap_country_selection_callback' ),
			// 	'auto-post-admin',
			// 	'auto_post_setting_section'
			// );

			// add_settings_field(
			// 	'trev_check_licence',
			// 	esc_html__( 'Check Licence', 'the-real-estate-voice' ),
			// 	array( $this, 'trev_csmap_check_licence_callback' ),
			// 	'auto-post-admin',
			// 	'auto_post_setting_section'
			// );
		}

		public function trev_csmap_auto_post_sanitize( $input ) {
			$sanitary_values = array();

			if ( isset( $input['auto_enabled_1'] ) ) {
				$sanitary_values['auto_enabled_1'] = $input['auto_enabled_1'];
			}

			if ( isset( $input['auto_attribute_posts_to_this_user_2'] ) ) {
				$sanitary_values['auto_attribute_posts_to_this_user_2'] = $input['auto_attribute_posts_to_this_user_2'];
			}

			if ( isset( $input['auto_post_status_3'] ) ) {
				$sanitary_values['auto_post_status_3'] = $input['auto_post_status_3'];
			}


			if ( isset( $input['auto_maximum_posts_per_check_4'] ) ) {
				$sanitary_values['auto_maximum_posts_per_check_4'] = sanitize_text_field( $input['auto_maximum_posts_per_check_4'] );
			}

			if ( isset( $input['auto_last_checked_6'] ) ) {
				$sanitary_values['auto_last_checked_6'] = sanitize_text_field( $input['auto_last_checked_6'] );
			}


			/* Auto share*/
			if ( ! trev_csmap_is_pro_license_plugin() ) {
				if ( isset( $input['enabled_sharing_for'] ) ) {
					unset( $input['enabled_sharing_for'] );
				}
			}

		
			if ( isset( $input['enabled_sharing_for'] ) ) {
				$sanitary_values['enabled_sharing_for'] = $input['enabled_sharing_for'];
			}

			if ( isset( $input['share_after_adding'] ) ) {
				$sanitary_values['share_after_adding'] = $input['share_after_adding'];
			}

			if ( ! trev_csmap_is_pro_license_plugin() ) {
				if ( isset( $input['share_times'] ) ) {
					unset( $input['share_times'] );
				}
			}

			if ( isset( $input['share_times'] ) ) {
				$sanitary_values['share_times'] = $input['share_times'];
			}

			if ( isset( $input['drip_feed_time'] ) ) {
				$sanitary_values['drip_feed_time'] = $input['drip_feed_time'];
			}
			/* Auto share*/


			return $sanitary_values;
		}

		public function trev_csmap_auto_post_section_info() {
			echo trev_csmap_get_settings_tab_buttons();?>
			<h2>
				<?php echo esc_html__('Automatically post articles','the-real-estate-voice');?>
			</h2>
			<?php			
		}

		public function trev_csmap_auto_share_section_info() {
			?>
		   <!-- added by indiasan start -->
			<!-- <h2>
				<?php echo esc_html__('Automatically Share to Facebook','the-real-estate-voice')?>
			</h2> -->
			<!-- added by indiasan end -->
			<?php 
		}



		public function trev_csmap_auto_enabled_callback() {

			if ( isset( $this->auto_post_options['auto_enabled_1'] ) && ! empty( $this->auto_post_options['auto_enabled_1'] ) ) {
				$checked = $this->auto_post_options['auto_enabled_1'];
			} else {
				$checked = 'no';
			}

			?>
			 
			<input type="checkbox" name="auto_post_option_name[auto_enabled_1]"  id="auto_enabled_1" value="yes"  <?php checked( $checked, 'yes', true ); ?> />
			<label for="auto_enabled_1"><?php esc_html_e( 'Auto Post Enabled', 'the-real-estate-voice' ); ?></label>
			<p class="description">
				<i><?php esc_html_e( 'Automatically post new articles on your website', 'the-real-estate-voice' ); ?></i>
			</p>	
			<?php
		}

		public function trev_csmap_auto_attribute_posts_to_user_callback() {

			$args = array(
				'orderby' => 'ID',
				'order'   => 'ASC',
			);

			$users = get_users( $args );

			?>
			 
			<select name="auto_post_option_name[auto_attribute_posts_to_this_user_2]" id="auto_attribute_posts_to_this_user_2" required>
				<?php $selected = ( isset( $this->auto_post_options['auto_attribute_posts_to_this_user_2'] ) && 'user' === $this->auto_post_options['auto_attribute_posts_to_this_user_2'] ) ? 'selected' : ''; ?>
				<?php
				foreach ( $users as $user ) {
					$selected = '';
					if ( isset( $this->auto_post_options['auto_attribute_posts_to_this_user_2'] ) ) {
						if ( $user->ID == $this->auto_post_options['auto_attribute_posts_to_this_user_2'] ) {
							$selected = 'selected';
						}
					}
					?>
					 
					<option value="<?php echo esc_attr( $user->ID ); ?>" <?php echo esc_attr( $selected ); ?>> <?php echo esc_html( $user->display_name ); ?></option>
				<?php } ?> 
			</select> 

			<p class="description">
				<i><?php esc_html_e( 'Articles  from the library will be posted by this user', 'the-real-estate-voice' ); ?></i>
			</p>
			<?php
		}

		public function trev_csmap_auto_post_status_callback() {
			?>
		 <select name="auto_post_option_name[auto_post_status_3]" id="auto_post_status_3" required>
			<?php $selected = ( isset( $this->auto_post_options['auto_post_status_3'] ) && $this->auto_post_options['auto_post_status_3'] === 'publish' ) ? 'selected' : ''; ?>
			<option value="publish" <?php echo esc_attr( $selected ); ?>><?php esc_html_e( 'Publish', 'the-real-estate-voice' ); ?></option>

			<?php $selected = ( isset( $this->auto_post_options['auto_post_status_3'] ) && $this->auto_post_options['auto_post_status_3'] === 'draft' ) ? 'selected' : ''; ?>
			<option value="draft" <?php echo esc_attr( $selected ); ?>><?php esc_html_e( 'Draft', 'the-real-estate-voice' ); ?> </option>

			<?php $selected = ( isset( $this->auto_post_options['auto_post_status_3'] ) && $this->auto_post_options['auto_post_status_3'] === 'pending' ) ? 'selected' : ''; ?>
			<option value="pending" <?php echo esc_attr( $selected ); ?>><?php esc_html_e( 'Pending', 'the-real-estate-voice' ); ?></option>
		</select>
		<p class="description">
			<i><?php esc_html_e( 'Publish articles immediately or put into draft mode', 'the-real-estate-voice' ); ?></i>
		</p>
			<?php
		}

		public function trev_csmap_maximum_posts_per_check_callback() {
			printf(
				'<input class="regular-text" max="5" type="number" name="auto_post_option_name[auto_maximum_posts_per_check_4]" id="auto_maximum_posts_per_check_4" value="%s" required>',
				isset( $this->auto_post_options['auto_maximum_posts_per_check_4'] ) ? esc_attr( $this->auto_post_options['auto_maximum_posts_per_check_4'] ) : ''
			);
			?>

			<p class="description">
				<i><?php esc_html_e( 'Number of articles to check each time', 'the-real-estate-voice' ); ?></i>
			</p>

			<?php
		}



		public function trev_csmap_auto_last_checked_callback() {
			$last_run = get_option( 'auto_last_run' );

			$last_item    = get_option( 'auto_last_items' );
			$last_skipped = get_option( 'auto_last_skipped' );
			if ( ! empty( $last_run ) ) {
				$last_checked = esc_html__( 'Run at: ' . $last_run->format( 'Y-m-d H:i:s' ) . ' And Added ' . $last_item . ' Post(s) (' . $last_skipped . ' Skipped as Already exists)', 'the-real-estate-voice' );
			} else {
				$last_checked = esc_html__( 'Never', 'the-real-estate-voice' );
			}

			printf(
				'<input class="regular-text" placeholder="Last Check" type="text" name="auto_post_option_name[auto_last_checked_6]" id="auto_last_checked_6" value="%s" readonly>',
				$last_checked
			);
		}

		public function trev_csmap_execute_cron_manually() {
			
		}
	}
}

if ( is_admin() ) {
	$revcp_settings = new TREV_CSMAP_Revoice_settings();
}

