<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'trev_csmap_get_settings_tab_buttons' ) ) {

	/**
	 * Function to display setting tabs
	 *
	 * @package The Real Estate Voice
	 * @since 1.0.0
	 */
	function trev_csmap_get_settings_tab_buttons() {

		if ( trev_csmap_is_pro_license_plugin() ) {
			$free_link = '';
			echo '<img class="banner_image" src="' . esc_url( TREV_CSMAP_SETTINGS_BANNER ) . '" alt="' . esc_attr( 'Settings' ) . '" />';
		} else {
			echo '<a href="' . esc_url( TREV_CSMAP_LIB_BANNER_LINK ) . '" target="_blank">';
			echo '<img class="banner_image" src="' . esc_url( TREV_CSMAP_SOCIAL_BANNER_URL ) . '">';
			echo '</a>';
		}

		ob_start();

		if ( isset( $_GET['tab'] ) && isset( $_GET['page'] ) ) {
			$current_page = sanitize_text_field( wp_unslash( $_GET['page'] ) ) . '&tab=' . sanitize_text_field( wp_unslash( $_GET['tab'] ) );
		} else {
			$current_page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
		}
		// added by indiasan start 
		$pages = array(
			// 'trev-csmap-settings'                       => esc_html__( 'Library', 'the-real-estate-voice' ),
			'trev-csmap-settings'         => esc_html__( 'Automation', 'the-real-estate-voice' ),
			// 'trev-csmap-settings&tab=auto-social'       => esc_html__( 'Facebook', 'the-real-estate-voice' ),
			// 'trev-csmap-settings&tab=auto-social-insta' => esc_html__( 'Instagram', 'the-real-estate-voice' ),
			// 'trev-csmap-settings&tab=general'           => esc_html__( 'General', 'the-real-estate-voice' ),
		);
		// added by indiasan end 
		?>
		<div class="settings_tab_area">
			<?php
			foreach ( $pages as $key => $value ) {
				
				$active_class = '';
				if ( $current_page == $key ) {
					$active_class = 'active';
				}
				?>

				<a class="settings_tab_button <?php echo esc_attr( $active_class ); ?>"   href="<?php echo esc_url( admin_url( 'admin.php?page=' . $key ) ); ?>">
					<?php esc_html_e( $value, 'the-real-estate-voice' ); ?></a>
			<?php } ?>

		</div>

		<?php
		return ob_get_clean();
	}
}
