<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$_GET['cat_tab'] = ! isset( $_GET['cat_tab'] ) ? '19' : sanitize_text_field( wp_unslash( $_GET['cat_tab'] ) );

$dcats                  = array();
$revcp_settings_options = get_option( 'revcp_settings_option_name' );
$pagify                 = TREV_CSMAP_INSTAGRAM_FEED_URL;
$feed_page              = '';
$current_page           = 'csrp_posts_per_page=' . $post_per_page . '&csrp_paged=' . $page;

if ( isset( $_GET['cat_tab'] ) ) {
	$current_page = $current_page . '&csrp_cat=' . sanitize_text_field( wp_unslash( $_GET['cat_tab'] ) );
	$final_feed   = TREV_CSMAP_INSTAGRAM_FEED_URL . '&' . $current_page;

}
$specific_item = false;
if ( isset( $_GET['cat_tab'] ) && ( 19 == $_GET['cat_tab'] || 21 == $_GET['cat_tab'] ) ) {
	$specific_item = true;

	$final_feed = TREV_CSMAP_INSTAGRAM_FEED_URL . '&csrp_orderby=date&csrp_post_status=publish,future&csrp_date_before=2022-05-30&csrp_cat=' . sanitize_text_field( wp_unslash( $_GET['cat_tab'] ) );

}


$rss          = simplexml_load_file( $final_feed );
$checked      = '';
$eparm        = '';
$exists_posts = 0;

if ( isset( $_GET['include_exists'] ) ) {

	$checked = 'checked';
	$eparm   = '&include_exists=1';
} else {
	if ( ! trev_csmap_is_pro_license_plugin() ) {
		$checked = 'checked';
	}
}

foreach ( $rss->channel->item as $item ) {

	if ( $checked != 'checked' ) {

		$alreay_exist = post_exists( $item->title, '', '', 'post' );

		if ( $alreay_exist ) {
			$exists_posts++;
		}
	}
}

if ( $exists_posts > 0 ) {

	$pagify        = TREV_CSMAP_INSTAGRAM_FEED_URL;
	$post_per_page = $post_per_page + $exists_posts;
	$feed_page     = '';
	$current_page  = 'csrp_posts_per_page=' . $post_per_page . '&csrp_paged=' . $page;

	if ( isset( $_GET['cat_tab'] ) ) {
		$current_page = $current_page . '&csrp_cat=' . sanitize_text_field( wp_unslash( $_GET['cat_tab'] ) );
	}
	$final_feed = TREV_CSMAP_INSTAGRAM_FEED_URL . '&' . $current_page;
	$rss        = simplexml_load_file( $final_feed );
}

if ( isset( $_GET['cat_tab'] ) ) {
	$selected = sanitize_text_field( wp_unslash( $_GET['cat_tab'] ) );
}
?>

<input type="hidden" id="include_exists" data-url="<?php echo esc_url( admin_url( 'admin.php?page=media-hub&tab=main&paged="' . $page . '"&csrp_cat="' . $selected ) ); ?>" name="include_exists" value="1">

<div class="content-library-list instagram-library">
	<?php
	 $feed_count = 0;
	  $num_pages = 0;
	if ( isset( $_GET['cat_tab'] ) ) {

		$current_page = '&csrp_cat=' . sanitize_text_field( wp_unslash( $_GET['cat_tab'] ) );
		$rssTemp      = simplexml_load_file( TREV_CSMAP_INSTAGRAM_FEED_URL . $current_page );
		foreach ( $rssTemp->channel->item as $item ) {
			if ( 19 == $_GET['cat_tab'] || 21 == $_GET['cat_tab'] ) {
				continue;}
			$feed_count++;
		}

		$num_pages = ceil( $feed_count / $post_per_page );

	}



	$icount = 0;


	foreach ( $rss->channel->item as $item ) {
		$publish_date = $item->pubDate;

		$display_date = date( 'F j<\s\up>S<\/\s\up>, Y', strtotime( $publish_date ) );
		$data_date    = date( 'Y-m-d', strtotime( $publish_date ) );

		$feed_cat     = $item->category;
		$alreay_exist = post_exists( $item->title, '', '', 'post' );

			$icount++;
			$featured_img_url = $item->children( 'media', true )->content->attributes()['url'];
		?>

			<div class="blog_item insta_item">
				<div class="blog_image">
					<img class="map_image insta_share_btn" data-image="<?php echo esc_url( $featured_img_url ); ?>" data-title="<?php echo esc_attr( $item->title ); ?>" data-desc="<?php echo esc_html( $item->description ); ?>" src="<?php echo esc_url( $item->children( 'media', true )->content->attributes()['url'] ); ?>" data-date="<?php echo $specific_item ? esc_attr( $data_date ) : ''; ?>">
				</div>  
			<?php if ( $specific_item ) { ?>
					<div class="badge"><?php echo wp_kses_post( $display_date ); ?></div>
				<?php } ?>

				<div class="blog_description" style="display: none;">
				   

					<h3 class="map_title"><?php echo esc_html( $item->title ); ?></h3>
					<?php
					$cats = json_decode( wp_json_encode( $item->category ), true );

					if ( is_array( $cats ) ) {
						foreach ( $cats as $cat ) {
							?>
							<div class="map_category"><?php echo esc_html( $cat ); ?></div>
							<?php
						}
					}
					?>
					<div class="library-excerpt" style="display:none">
						<?php echo esc_html( $item->description ); ?>
					</div>

					<div class="map_desc">
						<?php
						$word_count = str_word_count( trim( strip_tags( $item->description ) ) );
						if ( $word_count < 20 ) {
							echo esc_html( $item->description . '...' );
						} else {
							echo wp_trim_words( esc_html( $item->description ), 20, '...' );
						}
						?>
					</div>

					<div class="map_action">
						<button class="button button-primary insta_share_btn" data-image="<?php echo esc_url( $featured_img_url ); ?>" data-title="<?php echo esc_attr( $item->title ); ?>" data-desc="<?php echo esc_html( $item->description ); ?>"><?php esc_html_e( 'Share', 'the-real-estate-voice' ); ?></button>
					</div>
					<div class="map_content" style="display:none;">
						<?php echo esc_html( $item->children( 'content', true )->encoded ); ?>
					</div>
					<div class="map_categories" style="display:none;">
						<?php echo esc_html( wp_json_encode( $cats ) ); ?>
					</div>
				</div>   
			</div>
			<?php
	}
	?>
</div>

<div class="pagination">

	<?php
	if ( $page > 1 ) {
		$prev          = $page - 1;
		$paginationUrl = add_query_arg(
			array(
				'page'    => 'trev-csmap-instagram',
				'paged'   => $prev,
				'cat_tab' => $selected,
			),
			admin_url( 'admin.php' )
		);
		?>

		<a class="button button-primary" href="<?php echo esc_url( $paginationUrl ); ?>">  <?php esc_html_e( '<< Previous', 'the-real-estate-voice' ); ?></a>										  
														<?php
	}
	if ( $page < $num_pages ) {
		$page          = $page + 1;
		$paginationUrl = add_query_arg(
			array(
				'page'    => 'trev-csmap-instagram',
				'paged'   => $page,
				'cat_tab' => $selected,
			),
			admin_url( 'admin.php' )
		);
		?>

		<a class="button button-primary" href="<?php echo esc_url( $paginationUrl ); ?>"><?php esc_html_e( 'Next >>', 'the-real-estate-voice' ); ?> </a>
		<?php
	}
	?>

</div>
