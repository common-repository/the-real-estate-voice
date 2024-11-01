<?php
$revoice_insta_pages = get_option( 'revoice_get_insta_data' );
$fb_ids              = get_option( 'fb_pages_ids' );
if ( isset( $revoice_insta_pages['ig_user_ids'] ) ) {
	$ig_user_ids = $revoice_insta_pages['ig_user_ids'];
}
if ( empty( $fb_ids ) && ! empty( $ig_user_ids ) ) {
	$active_tab = 'instagram';
} else {
	$active_tab = 'facebook';
}
if ( isset( $_GET['tab'] ) ) {
	$active_tab = sanitize_text_field( wp_unslash( $_GET['tab'] ) );
}
if ( isset( $_GET['paged'] ) ) {
	$page = sanitize_text_field( wp_unslash( $_GET['paged'] ) );
}
?>


	<h2 class="nav-tab-wrapper" id="trev-nav-tabs">
		<?php if ( ! empty( $fb_ids ) ) { ?> 
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-dashboard&tab=facebook' ) ); ?>" class="nav-tab <?php echo ( $active_tab == 'facebook' ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Facebook ', 'the-real-estate-voice' ); ?></a>
			<?php
		}
		?>

		<?php if ( ! empty( $ig_user_ids ) ) { ?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-dashboard&tab=instagram' ) ); ?>" class="nav-tab <?php echo ( $active_tab == 'instagram' ) ? esc_attr( 'nav-tab-active' ) : ''; ?>"><?php esc_html_e( 'Instagram ', 'the-real-estate-voice' ); ?></a>
			<?php
		}
		?>

	</h2>

	<div class="tabs-content social-graph-tabs">
		<div id="loader" ><div class="loader-inner"></div></div>
		<?php
		if ( 'facebook' == $active_tab ) {
			if ( trev_csmap_is_pro_license_plugin() ) {
				include TREV_CSMAP_DIR_PATH . 'includes/revoice-facebook-insight.php';
			} else {
				?>
						<a class="social-graph-unlock" href="<?php echo esc_url( TREV_CSMAP_SUBSCRIBE_LINK ); ?>" target="_blank">
							<img src="<?php echo esc_url( TREV_CSMAP_SOCAIL_BANNER ); ?>" alt="<?php esc_attr_e( 'Unlock All Features' ); ?>">
						</a>
					<?php
			}
		} else {
			if ( trev_csmap_is_pro_license_plugin() ) {
				include TREV_CSMAP_DIR_PATH . 'includes/revoice-instagram-insight.php';
			} else {
				?>
						<a class="social-graph-unlock" href="<?php echo esc_url( TREV_CSMAP_SUBSCRIBE_LINK ); ?>" target="_blank">
							<img src="<?php echo esc_url( TREV_CSMAP_SOCAIL_BANNER ); ?>" alt="<?php esc_attr_e( 'Unlock All Features' ); ?>">
						</a>
					<?php
			}
		}
		?>

	</div>

<?php
