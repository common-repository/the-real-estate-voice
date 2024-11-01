<?php
	global $wpdb;

	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}

	global $rev_post_types;

	$tax_query               = array( 'relation' => 'OR' );
	$general_tab_option_name = get_option( 'general_tab_option_name' );

	$agentPoint = false;
	$siteloft   = false;
	if ( isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform'] ) {
		$agentPoint = true;

	}

	if ( isset( $general_tab_option_name['website_platform'] ) && 'SiteLoft' == $general_tab_option_name['website_platform'] ) {

		$listing = 'listings';
		$siteloft = true;
		
	}
	elseif ( isset( $general_tab_option_name['website_platform'] ) && ( 'Easy Property Listings' == $general_tab_option_name['website_platform'] || $agentPoint )) { 


		$listing = 'property';

	}else {

		$listing = 'listing';

	}

	$args = array(
		'posts_per_page' => $post_per_page,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_type'      => $listing,
		'paged'          => $page,
	);




	if ( isset( $_GET['category'] ) ) {
		$selected = sanitize_text_field( wp_unslash( $_GET['category'] ) );
	} else {
		$selected = esc_html__( 'all', 'the-real-estate-voice' );
	}


	
	$current_post_type = $listing;


	if ( isset( $_GET['status'] ) && ! empty( $_GET['status'] ) ) {
		$get_status = sanitize_text_field( wp_unslash( $_GET['status'] ) );
		if ( 'all' != $get_status ) {
			$args['meta_key']   = 'revoice_sharing_status';
			$args['meta_value'] = $get_status;
		}
	}

	$unset_cat = array(
		'post_tag',
		'nav_menu',
		'link_category',
		'post_format',
		'wp_theme',
		'wp_template_part_area',
	);


	
		

	if($agentPoint){
		
		$cate_ap 	  = array();
		$categories   = array();	
		$properties   = 'properties';
		$qry_property = "SELECT property_type FROM ".$properties;
		$res_property = $wpdb->get_results( $qry_property );
		
		foreach ($res_property as $key => $value) {
			$cate_ap[] = $value->property_type; 
		}

		$cate_ap = array_unique($cate_ap);

		foreach ($cate_ap as $key => $cate) {
			
			$categories_ap   = new stdClass();	
			$categories_ap->term_id   = $cate; 
			$categories_ap->name = $cate;
			$categories[] = $categories_ap;

		}

	}

	if($siteloft) {

		$cate_ap 	  = array();
		$categories   = array();

		$listCategories = json_decode(str_replace('\\', '', get_option('siteloft_settings_filter_category_options')));

        if (($listCategories = get_option('siteloft_settings_filter_category_options'))) {
            $listCategories = json_decode(str_replace('\\', '', $listCategories), true);
        }

        $listCategories = array_column($listCategories ?: [], 'value', 'key');

        $listCategories = is_array($listCategories) ? $listCategories : [];

        if(!empty($listCategories)){
        	foreach ($listCategories as $key => $cate_sl) {
        		$categories_ap   = new stdClass();	
        		$categories_ap->term_id   = $cate_sl; 
        		$categories_ap->name = $cate_sl;
        		$categories[] = $categories_ap;
        	}
        }

        

	    if ( $selected != 'all' ) {
	    	$args['meta_query'] =  array(
	    		'relation' => 'OR',
	    		array(
	    			'key'       => 'subcategories',
	    			'value'     => $selected,
	    			'compare'   => 'LIKE',
	    		)   	
	    	);
        }


	}
	else{

		$taxonomies_objects = get_object_taxonomies( $current_post_type, 'objects' );

		if ( ! empty( $taxonomies_objects ) ) {

			$taxonomies=get_taxonomies( array( 'object_type' => array( $current_post_type ) ) );
			foreach ( $unset_cat as  $taxonomy ) {
				unset( $taxonomies[ $taxonomy ] );

			}

			foreach ( $taxonomies as  $taxonomy ) {
				$hierarchical = $taxonomies_objects[ $taxonomy ]->hierarchical;
				if ( $hierarchical ) {
					$args_taxonomy = array(
						'taxonomy'   => $taxonomy,
						'orderby'    => 'name',
						'order'      => 'ASC',
						'hide_empty' => false,
					);


					if ( $selected != 'all' ) {
						$tax_query[]       = array(
							'taxonomy' => $taxonomy,
							'field'    => 'id',
							'terms'    => $selected,
							'operator' => 'IN',
						);
						$args['tax_query'] = $tax_query;
					}
				}
			}

			$categories = get_categories( $args_taxonomy );

		}
	}

	if ( isset( $_GET['post_status'] ) ) {

		$current_post_status = sanitize_text_field( wp_unslash( $_GET['post_status'] ) );
		if ( $current_post_status != 'all' ) {
			$args['post_status'] = $current_post_status;
		}
	} else {

		$current_post_status = esc_html__( 'all', 'the-real-estate-voice' );

	}

	// Getting Post type.
	$post_type_sharable = array();
	$post_type_sync     = get_option( 'auto_post_option_name' );

	if ( isset( $post_type_sync['enabled_sharing_for'] ) && ! empty( $enabled_sharing_for['enabled_sharing_for'] ) ) {
		$enabled_sharing_for = $post_type_sync['enabled_sharing_for'];

		foreach ( $enabled_sharing_for as $key => $value ) {
			if ( isset( $enabled_sharing_for[ $key ] ) && 1 == $enabled_sharing_for[ $key ] ) {
				$post_type_sharable[] = $key;
			}
		}
	}

	$reset = false;
	if ( isset( $_GET['filter'] ) ) {
		$reset = true;
	}
	?>

<form action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" method="get">
	<input type="hidden" name="page" value="trev-csmap-social-media">
	<input type="hidden" name="tab" value="share_listing">
	<input type="hidden" name="paged" value="1">
	<input type="hidden" name="filter" value="1">
	<div class="blog_filter">
		<div>
			<p><label for="cars"><?php esc_html_e( 'Select filters:', 'the-real-estate-voice' ); ?></label> </p>
		</div>

		<div class="category_filter">
			<select name="category" id="category" data-url="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-social-media&tab=share_listing&paged=1' ) ); ?>">

				<option value="all">
					<?php esc_html_e( 'All Categories', 'the-real-estate-voice' ); ?></option>

				<?php
				foreach ( $categories as $cat ) {
					$select = '';

					if ( $cat->term_id == $selected ) {
						$select = 'selected';
					}
					?>
				<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php echo esc_attr( $select ); ?>><?php echo esc_html( $cat->name ); ?></option>
				<?php } ?>
			</select>
		</div>

		<div class="category_filter">
			<?php global $sharing_status; ?>

			<select name="status" id="status" data-url="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-social-media&tab=share_listing&paged=1' ) ); ?>">

				<option value="all"><?php esc_html_e( 'Select Status', 'the-real-estate-voice' ); ?></option>
				<?php
				foreach ( $sharing_status as $fb_status ) {
					$selected_status = '';

					if ( ! empty( $get_status ) && $get_status == $fb_status ) {
						$selected_status = 'selected';
					}
					?>

					<option value="<?php echo esc_attr( $fb_status ); ?>" <?php echo esc_attr( $selected_status ); ?>>
						<?php echo esc_html( $fb_status ); ?>
					</option>
				<?php } ?> 
			</select>
		</div>

		<div class="category_filter">
			<button type="submit" class="button button-primary "><?php esc_html_e( 'Filter', 'the-real-estate-voice' ); ?></button>
		</div>

		<?php if ( $reset ) { ?> 
			<div class="category_filter">
				
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-social-media&tab=share_listing' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Reset Filter', 'the-real-estate-voice' ); ?>
				</a>

			</div>
		<?php } ?>
	</div>
</form>

<div class="fb-content-list">

	<?php

	$icount    = 0;
	
	if($agentPoint){
	
		$properties = 'properties';
		$attachment = 'attachments';
		
		if(!empty($get_status) && 'all' == $get_status && 'all' != $selected && isset($_GET['filter'])){
			$qry_post="SELECT * FROM $properties WHERE property_type='$selected'";	
		
		}elseif ( isset($get_status) && 'all' != $get_status  && !empty( $get_status)  && isset($_GET['filter']) && 'all' == $selected  ) {
			
			$qry_post="SELECT * FROM $properties as p LEFT JOIN property_meta as m ON p.id = m.property_id WHERE m.meta_value='$get_status' AND m.meta_key='revoice_sharing_status'";	
		

		}elseif ( isset($_GET['status'] ) && isset($_GET['category']) && 'all' != $selected   ) {
			
			$qry_post="SELECT * FROM $properties as p LEFT JOIN property_meta as m ON p.id = m.property_id WHERE p.property_type='$selected' AND m.meta_value='$get_status' AND m.meta_key='revoice_sharing_status'";	
			
		}else{

			$qry_post="SELECT * FROM ".$properties;	

		}
		
				
		$posts = $wpdb->get_results( $qry_post );
		
		$post_count          = count( $posts );
	}else{
		$posts     = get_posts( $args );	
		$eparm     = '';
		$args_temp = $args;
		unset( $args_temp['posts_per_page'] );

		$args_temp['posts_per_page'] = -1;

		$posts_temp          = get_posts( $args_temp );
		$post_count          = count( $posts_temp );
	}
	
	
	
	$general_tab_options = get_option( 'general_tab_option_name' );

	$periodArray = array(
		'rental_period_1' => esc_html__( 'per Month', 'the-real-estate-voice' ),
		'rental_period_2' => esc_html__( 'per Week', 'the-real-estate-voice' ),
		'rental_period_3' => esc_html__( 'per Year', 'the-real-estate-voice' ),
		'rental_period_4' => esc_html__( 'per Day', 'the-real-estate-voice' ),
	);

	$num_pages = ceil( $post_count / $post_per_page );
	$revoice_scheduled_id = '';
		
	if ( $post_count > 0 ) {

		foreach ( $posts as $key => $post ) {
			$last_shared = '';
			$featured_img_url='';
			
			if($agentPoint){
					
				$post_id = $post->id;
				$qry_img ="SELECT * FROM ".$attachment." WHERE parent_id =$post_id";
				$image_res = $wpdb->get_results( $qry_img );

				if(!empty($image_res)){
					$featured_img_url= $image_res[0]->url;
				}
			
				$permalink = get_site_url().'/'.$post_id;
				$title = $post->headline;
				$description = $post->description;
				$listing_details=trev_csmap_get_web_plateform_data($current_post_type, $post_id);

				$listing_details['listing_link'] 	= $permalink;
				$listing_details['listing_desc'] 	= $description;

			
				if ( trev_csmap_exist_preperty_meta( $post_id, 'revoice_shared' ) ) {

					$last_shared = trev_csmap_get_preperty_meta( $post_id, 'revoice_shared' );
						
					// if(!empty($revoice_shared)){
					// 	$last_shared    = date( 'd/m/Y', strtotime( $revoice_shared ) );	
					// }

				}
				

				if ( trev_csmap_exist_preperty_meta( $post_id, 'revoice_sharing_status' ) ) {
					
					$status = trev_csmap_get_preperty_meta( $post_id, 'revoice_sharing_status', true );
		
						
				} else {

					$res = trev_csmap_update_preperty_meta( $post_id, 'revoice_sharing_status', 'Never Shared' );
					$status = esc_html__( 'Never Shared', 'the-real-estate-voice' );
				}

				$revoice_scheduled_id = trev_csmap_get_preperty_meta( $post_id, 'revoice_scheduled_id' );	

			}else{

				$post_id    = $post->ID;
				$permalink 	= get_permalink( $post_id );
				$title 		= get_the_title( $post_id );

				$featured_img_url     = get_the_post_thumbnail_url( $post_id, 'full' );
				if ( empty( $featured_img_url ) && isset( $general_tab_options['website_platform'] ) && 'SiteLoft' == $general_tab_options['website_platform'] ) {

					$hero_image_thumbs = get_post_meta( $post_id, 'thumbnails', true );

					if(!empty($hero_image_thumbs) && ! empty( $hero_image_thumbs[0]['full'] )){

						$featured_img_url = $hero_image_thumbs[0]['full'];

					}else{

						$hero_image_full = get_post_meta( $post_id, 'images', true );

						if(!empty($hero_image_full)){
							$featured_img_url = $hero_image_full[0]['full'];	
						}
					}

				}

				if($siteloft){
					$categories 	= get_post_meta($post_id,'subcategories',true);	
				
				}else{
					$categories     = get_the_category( $post_id );	
				}
				
				$parent_content     = get_the_excerpt( $post_id );

				if ( metadata_exists( 'post', $post_id, 'revoice_shared' ) ) {
					$revoice_shared = get_post_meta( $post_id, 'revoice_shared', true );
					$last_shared    = date( 'd/m/Y', strtotime( $revoice_shared->date ) );
				}

				$listing_details 	= trev_csmap_get_web_plateform_data($current_post_type, $post_id);

				if ( metadata_exists( 'post', $post_id, 'revoice_sharing_status' ) ) {

					$status = get_post_meta( $post_id, 'revoice_sharing_status', true );

				} else {

					update_post_meta( $post_id, 'revoice_sharing_status', 'Never Shared' );
					$status = esc_html__( 'Never Shared', 'the-real-estate-voice' );
				}
				
				$revoice_scheduled_id = get_post_meta( $post_id, 'revoice_scheduled_id', true );
			}

				
				$page_ids  = get_post_meta( $revoice_scheduled_id, 'revoice_scheduled_page_id', true );

				$scheduled = get_post_meta( $revoice_scheduled_id, 'revoice_scheduled', true );
				if ( $scheduled < current_time( 'mysql' ) ) {
					$scheduled = '';
				}
				
				$post_content = get_post( $revoice_scheduled_id );

			
			$bedroom_emoji = '';
			if ( ! empty( $listing_details['bedroom'] ) ) {
				$bedroom_emoji                    = '&#128716;';
			}

			$bathroom_emoji = '';
			if ( ! empty( $listing_details['bathroom'] ) ) {
				$bathroom_emoji                    = '&#128704;';
			}

			$garage_emoji = '';
			if ( ! empty( $listing_details['garage'] ) ) {
				$garage_emoji 					   = '&#128663;';
			}
			
			$js_code              = json_encode( $listing_details );

			$version_status = false;
			if ( trev_csmap_is_pro_license_plugin() ) {
				$version_status = true;
			}
	

			$autshare = false;
			?>

			<div class="blog_item <?php echo esc_attr($post_id)?>">

				<div class="blog_image">
					<img class="map_image" src="<?php echo esc_url( $featured_img_url ); ?>"></div>  
					<h3 class="map_date">


						<?php
						if ( ! empty( $last_shared ) && $last_shared != '' ) {
							?>
							<span><?php esc_html_e( 'Last Shared: ', 'the-real-estate-voice' ); ?> <?php echo esc_html( $last_shared ); ?></span>
							<span class="saperater">  <?php esc_html_e( '| ', 'the-real-estate-voice' ); ?> </span>
						<?php } ?> 

						<?php
						if ( $status != '' || ! empty( $status ) ) {
							?>
							<span> <?php esc_html_e( 'Status: ', 'the-real-estate-voice' ); ?><?php echo esc_html( $status ); ?></span>
						<?php } ?>
					</h3>

					<div class="blog_description social-media-desc">

						<h3 class="map_title">
							<a href="<?php echo esc_url( $permalink ); ?>" class="" target="_blank">
								<?php echo esc_html( $title ); ?> 
							</a>
						</h3>       
						<?php
						
						if($agentPoint){

							$qry_property = "SELECT property_type FROM $properties WHERE id=".$post_id;

							$res_property = $wpdb->get_results( $qry_property );
							?>

							<div class="map_category">
								<?php echo esc_html( $res_property[0]->property_type ); ?>
							</div>

						<?php
						 }else{
						 
							if ( is_array( $categories ) ) {
								foreach ( $categories as $cat ) {
									?>
									<div class="map_category">
										<?php
										if($siteloft){
										 	echo esc_html( $cat ); 
										}else{
											echo esc_html( $cat->name ); 
										}?>
									</div>
									<?php
								}
							}	
						}



							
												

						?>


						<div class="map_desc test">
							<?php

							if($agentPoint){
								$get_content = $description;
								echo wp_kses_post($description);


							}else{

								$get_content = $post->post_excerpt;

								if ( ! empty( $get_content ) ) {

									echo wp_kses_post( $get_content );

								} else {

									$get_content = apply_filters( 'the_content', $post->post_content );
									$get_content = preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', $get_content );

									echo wp_kses_post( $get_content );
								}

							}
				

							?>

						</div>
						<div class="map_content" style="display:none;">

							<?php
								if($agentPoint){
									echo wp_kses_post( $description );
								}else{
									echo wp_kses_post( $post->post_content );		
								}
							  ?></div>


					</div>   


					<div class="blog_action">
						<?php

						if ( isset($status) && 'Currently scheduled' == $status && trev_csmap_is_pro_license_plugin() ) {
							?>

							<button class="button button-primary edit_schedule edit_post_cl" data-bedroom-emoji="<?php echo esc_attr( $bedroom_emoji ); ?>" data-event_time="<?php echo esc_attr( $scheduled ); ?>" data-pageid="<?php echo esc_attr( $page_ids ); ?>" data-id="<?php echo esc_attr( $revoice_scheduled_id ); ?>" data-post-id="<?php echo esc_attr( $post_id ); ?>" data-event="<?php echo esc_attr(  $title  ); ?>" data-bathroom-emoji="<?php echo esc_attr( $bathroom_emoji ); ?>" data-garage-emoji="<?php echo esc_attr( $garage_emoji ); ?>"  data-post-type="<?php echo esc_attr( $current_post_type ); ?>" data-address="<?php echo esc_attr( $listing_details['map_address'] ); ?>" data-listing-link="<?php echo esc_attr( $listing_details['listing_link'] ); ?>" data-bedroom="<?php echo esc_attr( $listing_details['bedroom'] ); ?>" data-bathroom="<?php echo esc_attr( $listing_details['bathroom'] ); ?>" data-garage="<?php echo esc_attr( $listing_details['garage'] ); ?>" data-image="<?php echo esc_url( $featured_img_url ); ?>" data-schedule-id="<?php echo esc_attr( $revoice_scheduled_id ); ?>" data-id="<?php echo esc_attr( $post_id ); ?>" data-title="<?php echo esc_attr( $title ); ?>" data-listing-details='<?php echo esc_attr( $js_code ); ?>' data-content-page="<?php esc_attr( 'content' ); ?>" data-parent-content="<?php echo esc_attr( $listing_details['listing_desc'] ); ?>" data-desc="<?php echo wp_kses_post( $post_content->post_content ); ?>" ><?php esc_html_e( 'Edit', 'the-real-estate-voice' ); ?></button>

						<?php } else { ?>
							<button class="button button-primary map_share_btn" data-bedroom-emoji="<?php echo esc_attr( $bedroom_emoji ); ?>" data-bathroom-emoji="<?php echo esc_attr( $bathroom_emoji ); ?>" data-garage-emoji="<?php echo esc_attr( $garage_emoji ); ?>"    data-post-type="<?php echo esc_attr( $current_post_type ); ?>" data-address="<?php echo esc_attr( $listing_details['map_address'] ); ?>" data-listing-link="<?php echo esc_url( $listing_details['listing_link'] ); ?>" data-bedroom="<?php echo esc_attr( $listing_details['bedroom'] ); ?>" data-bathroom="<?php echo esc_attr( $listing_details['bathroom'] ); ?>" data-garage="<?php echo esc_attr( $listing_details['garage'] ); ?>" data-image="<?php echo esc_url( $featured_img_url ); ?>" data-schedule-id="<?php echo esc_attr( $revoice_scheduled_id ); ?>" data-id="<?php echo esc_attr( $post_id ); ?>" data-title="<?php echo esc_attr($title ); ?>" data-listing-details='<?php echo esc_attr( $js_code ); ?>' data-plugin-version="<?php esc_attr_e( $version_status ); ?>" data-desc="<?php echo esc_html( strip_tags( $get_content ) ); ?>" ><?php esc_html_e( 'Share', 'the-real-estate-voice' ); ?></button>

						<?php } ?>

					</div>  

				</div>

			<?php

			$icount++;
		}
	} else {
		?>
		<div class="blog_filter post-not-found">
			<p><?php esc_html_e( 'Listing not found', 'the-real-estate-voice' ); ?></p>
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
				'page'     => 'trev-csmap-social-media',
				'tab'      => 'share_listing',
				'paged'    => $prev,
				'type'     => $current_post_type,
				'category' => $selected,
			),
			admin_url( 'admin.php' )
		);
		?>

		<a class="button button-primary" href="<?php echo esc_url( $paginationUrl ); ?>"><?php esc_html_e( '<< Previous', 'the-real-estate-voice' ); ?></a>

		<?php
	}

	if ( $page < $num_pages ) {

		$page          = $page + 1;

		$paginationUrl = add_query_arg(
			array(
				'page'     => 'trev-csmap-social-media',
				'tab'      => 'share_listing',
				'paged'    => $page,
				'type'     => $current_post_type,
				'category' => $selected,
			),
			admin_url( 'admin.php' )
		);

		?>

			<a  class="button button-primary" href="<?php echo esc_url( $paginationUrl ); ?>"><?php esc_html_e( 'Next >>', 'the-real-estate-voice' ); ?></a>

		<?php } ?>

	</div>
