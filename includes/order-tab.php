<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	
	
	$categories             = array();
	$dcats                  = array();
	$revcp_settings_options = get_option( 'revcp_settings_option_name' );

	if ( isset( $revcp_settings_options['library_cats'] ) && ! empty( $revcp_settings_options['library_cats'] ) ) {
		$categories = $revcp_settings_options['library_cats'];
	}

	$feed_page = '';

	if(isset($_GET['paged'])){
		$csrp_paged = sanitize_text_field( wp_unslash( $_GET['paged'] ) );
		$current_page = '&csrp_paged=' . $csrp_paged;
	}else{
		$current_page = '&csrp_paged=' . $page;
	}
	
	
	$csrp_cat ='';
	if ( isset( $_GET['csrp_cat'] ) ) {

		$csrp_cat = sanitize_text_field( wp_unslash( $_GET['csrp_cat'] ) );
		$current_page = $current_page . '&csrp_cat=' . $csrp_cat ;
		
	} 
		
	if ( strpos( TREV_CSMAP_FEED_URL, 'csrp_posts_per_page' ) == false ) {
		$original_feed  = TREV_CSMAP_FEED_URL .'&csrp_posts_per_page=-1'.$current_page;
		$original_rss   = simplexml_load_file( $original_feed );	
		

	} else {

		$original_ex  = explode('&', TREV_CSMAP_FEED_URL);
		foreach ($original_ex as $key => $value) {
			if ( strpos( $value, 'csrp_posts_per_page' ) !== false ) {
				unset($original_ex[$key]);
			}	
		}
		
		$original_ex[] = 'csrp_posts_per_page=-1';

		$original_feed_ex = implode('&', $original_ex);

		$original_feed  = $original_feed_ex.$current_page;
		$original_rss   = simplexml_load_file( $original_feed );	
	}

	
	$final_feed     = TREV_CSMAP_FEED_URL . $current_page;

	$rss            = simplexml_load_file( $final_feed );
					
	$checked        = '';
	$eparm          = '';
	$include_exists = '';
	

	if ( isset( $_GET['include_exists'] ) ) {
		$checked        = 'checked';
		$eparm          = '&include_exists=1';
		$include_exists = 1;
	} else {
		if ( ! trev_csmap_is_pro_license_plugin() ) {
			$checked = 'checked';
		}
	}


$auto_instant_share    = false;
$auto_schedule_share   = false;
$get_status_auto_share = get_option( 'auto_post_option_name' );

if ( isset( $get_status_auto_share['share_times'] ) && 0 == $get_status_auto_share['share_times'] ) {
	$auto_instant_share = true;
} elseif ( isset( $get_status_auto_share['share_times'] ) && 1 == $get_status_auto_share['share_times'] ) {
	$auto_schedule_share = true;
}

?>
<div style="display:none">

	<input type="hidden" name="auto_instant_share" id="auto_instant_share" value="<?php echo esc_attr( $auto_instant_share ); ?>">
	<input type="hidden" name="auto_schedule_share" id="auto_schedule_share" value="<?php echo esc_attr( $auto_schedule_share ); ?>">

</div>

<div class="<?php echo ( trev_csmap_is_pro_license_plugin() ) ? esc_attr( 'blog_filter' ) : ''; ?> content-library">

	<?php if ( trev_csmap_is_pro_license_plugin() ) { ?>
		<div class="category_filter">

			<label for="cars"></label>

			<select name="category" id="category" data-url="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-media-hub&tab=main&paged=1' ) ); ?>">

			<option value="all"><?php esc_html_e( 'All Categories', 'the-real-estate-voice' ); ?></option>

			<?php
			foreach ( $categories as $cat_name => $cat ) {

				$select = '';
				if ( $cat == $csrp_cat ) {
					$select = 'selected';
				}
				?>

				<option value="<?php echo esc_attr( $cat ); ?>" <?php echo esc_attr( $select ); ?>><?php echo esc_html( $cat_name ); ?></option>

					<?php
			}
			?>
		</select>

	</div>
	<div class="include_checkbox">
		<?php

		$adminUrl = add_query_arg(
			array(
				'page'     => 'trev-csmap-media-hub',
				'tab'      => 'main',
				'paged'    => $page,
				'csrp_cat' => $csrp_cat,

			),
			admin_url( 'admin.php' )
		);

		?>
		<input type="checkbox" id="include_exists" data-url="<?php echo esc_url( $adminUrl ); ?>" name="include_exists" value="1" <?php echo esc_attr( $checked ); ?>>

		<label for="include_exists"> <?php esc_html_e( 'Include articles already on website', 'the-real-estate-voice' ); ?></label><br>

	</div>
		<?php
	} else {
		$freeAdminUrl = add_query_arg(
			array(
				'page'     => 'trev-csmap-media-hub',
				'tab'      => 'main',
				'paged'    => $page,
				'csrp_cat' => $csrp_cat,
			),
			admin_url( 'admin.php' )
		);
		?>

	<input type="hidden" id="include_exists" data-url="<?php echo esc_url( $freeAdminUrl ); ?>" name="include_exists" value="1">
	

    <?php } ?>

<?php $get_pro_url = get_option( 'get_pro_url' );?>

<?php if( !trev_csmap_is_pro_license_plugin() || ( isset( $get_pro_url['libraryRSS'] ) && empty( $get_pro_url['libraryRSS'] ) ) ){ ?>

	<div class="country-library-section <?php echo (trev_csmap_is_pro_license_plugin())? 'premium-ver':''; ?>">
		<?php 
		
			$countryCode = trev_selected_country();
			$country     = trev_csmap_country_name($countryCode); 

		?>

			<span>
				<?php echo esc_html($country).' ' ;?>
				<?php echo esc_html('Library','the-real-estate-voice');?></span>
			<a href="<?php echo esc_url( admin_url('admin.php?page=trev-csmap-settings&tab=general'))?>" title="<?php echo esc_attr($country);?>">
			<?php echo esc_html__('Change','the-real-estate-voice');?>				
			</a>
	</div>

<?php }?>

</div>


<?php

if ( ! empty( $categories ) || ! trev_csmap_is_pro_license_plugin() ) {
	?>
	<div class="content-library-list">
			<?php

			$rss_items 	 = array();		
			$icount    	 = 0;
			$feed_count  = 0;

			foreach ( $original_rss->channel->item as $item ) {

				$alreay_exist = post_exists( $item->title, '', '', 'post' );

				if( $alreay_exist && !isset($_GET['include_exists']) ) {
					continue;
				}

				$rss_items[] = $item;
				$feed_count++;
			}

			$start = 1;	
			
			if( !empty($csrp_paged) && $csrp_paged > 1 ) {

				$start = $start + $post_per_page * $csrp_paged;
			}
			
			if( $feed_count >= 50 ){

				$new_rss_items = array_slice( $rss_items, $start, $post_per_page );

			}else{

				$new_rss_items = $original_rss->channel->item;	
			}
			

			$num_pages 		= ceil( $feed_count / $post_per_page );			
				
			if( !empty($new_rss_items) ){

				foreach ( $new_rss_items as $item ) {
					
					$feed_cat     = $item->category;
					$alreay_exist = post_exists( $item->title, '', '', 'post' );
					$show         = true;
							
					if ( $alreay_exist ) {
						if ( 'checked' != $checked ) {
							$show = false;
						}

					}

					if ( $show ) {
						$icount++;
						
						$featured_img_url = $item->children( 'media', true )->content->attributes() ['url']; ?>

					<div class="blog_item">

						<div class="blog_image">
							<img class="map_image" src="<?php echo esc_url( $item->children( 'media', true )->content->attributes() ['url'] ); ?>">
						</div>   

						<div class="blog_description">

								<?php if ( $alreay_exist ) { ?> 

								<div class="badge"><?php esc_html_e( 'On Website', 'the-real-estate-voice' ); ?></div>

									<?php
								}
								?>

								<h3 class="map_title">
									<?php echo esc_html( $item->title ); ?>
								</h3>
								<?php
									$cats = json_decode( json_encode( $item->category ), true );
								if ( is_array( $cats ) ) {
									foreach ( $cats as $cat ) {
										?>
								<div class="map_category"><?php echo esc_html( $cat ); ?></div>

											<?php
									}
								}
								?>
							<div class="library-excerpt" style="display:none">
								<?php echo esc_html( $item->description ); ?></div>
							<div class="map_desc">
								<?php
								$word_count = str_word_count( trim( strip_tags( $item->description ) ) );
								if ( $word_count < 20 ) {
									echo esc_html( $item->description ) . '... <span class="blog_action"><a href="javascript:void(0);" class="add_blog_button" data-image="' . esc_url( $featured_img_url ) . '" data-schedule-id="" data-title="' . esc_attr( $item->title ) . '" data-desc="' . esc_attr( $item->description ) . '"> ' . esc_html__( 'Add Article', 'the-real-estate-voice' ) . '</a></span>';
								} else {
									echo wp_trim_words( esc_html( $item->description ), 20, '... <span class="blog_action"><a href="javascript:void(0);" class="add_blog_button" data-image="' . esc_url( $featured_img_url ) . '" data-schedule-id="" data-title="' . esc_attr( $item->title ) . '" data-desc="' . esc_attr( $item->description ) . '" data-add-from=""> ' . esc_html__( 'Add Article', 'the-real-estate-voice' ) . '</a></span>' );
								}
								?>

							</div>

							<div class="map_content" style="display:none;"><?php echo wp_kses_post( $item->children( 'content', true )->encoded ); ?>					
							</div>


							<div class="map_categories" style="display:none;"><?php echo esc_html( wp_json_encode( $cats ) ); ?></div>
						</div>   

					</div>  

						<?php
					}
					
				}
		}else{
			?>

		<div class="blog_filter post-not-found">
			<p><?php esc_html_e( 'Article not found', 'the-real-estate-voice' ); ?></p>
		</div>
		<?php
		}


			?>
	</div>

	<?php
} else {
	?>

	<div class="blog_filter post-not-found">
		<p><?php esc_html_e( 'Article not found', 'the-real-estate-voice' ); ?></p>
	</div>
	<?php
}

?>

<?php if ( ! empty( $categories ) ) { ?>
	<div class="pagination">

		<?php
		if ( $page > 1 ) {
			$prev = $page - 1;
			if ( isset( $_GET['include_exists'] ) ) {
				$arr = array(
					'page'           => 'trev-csmap-media-hub',
					'tab'            => 'main',
					'paged'          => $prev,
					'csrp_cat'       => $csrp_cat,
					'include_exists' => 1,

				);
			} else {
				$arr = array(
					'page'     => 'trev-csmap-media-hub',
					'tab'      => 'main',
					'paged'    => $prev,
					'csrp_cat' => $csrp_cat,

				);
			}
			$paginationUrl = add_query_arg(
				$arr,
				admin_url( 'admin.php' )
			);
			?>
			<a class="button button-primary" href="<?php echo esc_url( $paginationUrl ); ?>">  <?php esc_html_e( '<< Previous', 'the-real-estate-voice' ); ?></a>

			<?php
		}
	
		if ( $page < $num_pages && $icount >= $post_per_page) {
		 $page = $page + 1;
			
			if ( isset( $_GET['include_exists'] ) ) {
				$arr = array(
					'page'           => 'trev-csmap-media-hub',
					'tab'            => 'main',
					'paged'          => $page,
					'csrp_cat'       => $csrp_cat,
					'include_exists' => 1,

				);
			} else {
				$arr = array(
					'page'     => 'trev-csmap-media-hub',
					'tab'      => 'main',
					'paged'    => $page,
					'csrp_cat' => $csrp_cat,

				);
			}

			$paginationUrl = add_query_arg(
				$arr,
				admin_url( 'admin.php' )
			);
			?>
			   

				<a  class="button button-primary" href="<?php echo esc_url( $paginationUrl ); ?>">
				<?php esc_html_e( 'Next >>', 'the-real-estate-voice' ); ?> </a>

				<?php
		}
		?>

		</div>
	<?php
}
?>
