<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $rev_post_types;
$tax_query = array( 'relation' => 'OR' );

$args = array(
	'posts_per_page' => $post_per_page,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'post_type'      => 'post',
	'paged'          => $page,
);

if ( isset( $_GET['category'] ) ) {
	$selected = sanitize_text_field( wp_unslash( $_GET['category'] ) );
} else {

	$selected = esc_html__( 'all', 'the-real-estate-voice' );

}

if ( isset( $_GET['type'] ) ) {

	$current_post_type = sanitize_text_field( wp_unslash( $_GET['type'] ) );

	if ( 'all' != $current_post_type ) {
		$args['post_type'] = $current_post_type;
	}
} else {
	$current_post_type = 'post';
}

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

$taxonomies_objects = get_object_taxonomies( $current_post_type, 'objects' );

if ( ! empty( $taxonomies_objects ) ) {
	$taxonomies = get_taxonomies( array( 'object_type' => array( $current_post_type ) ) );
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


			if ( 'all' != $selected ) {
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

// Post Status Related.
if ( isset( $_GET['post_status'] ) ) {

	$current_post_status = sanitize_text_field( wp_unslash( $_GET['post_status'] ) );
	if ( 'all' != $current_post_status ) {
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
	<input type="hidden" name="tab" value="share">
	<input type="hidden" name="paged" value="1">
	<input type="hidden" name="filter" value="1">
	<div class="blog_filter">
		<div>
			<p> <label for="cars"> <?php esc_html_e( 'Select filters:', 'the-real-estate-voice' ); ?></label> </p>
		</div>

		<div class="category_filter">
			<select name="category" id="category" data-url="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-social-media&tab=share&paged=1' ) ); ?>">

				<option value="all"><?php esc_html_e( 'All Categories', 'the-real-estate-voice' ); ?></option>
				<?php
				foreach ( $categories as $cat ) {
					$select = '';
					if ( $cat->term_id == $selected ) {
						$select = 'selected';
					}
					?>
					<option value="<?php echo esc_attr( $cat->term_id ); ?>" <?php echo esc_attr( $select ); ?>><?php echo esc_html( $cat->name ); ?></option>
					<?php
				}
				?>
			</select>
		</div>

		<div class="category_filter">
			<?php global $sharing_status; ?>
			<select name="status" id="status" data-url="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-social-media&tab=share&paged=1' ) ); ?>">
				<option value="all"><?php esc_html_e( 'Select Status' ); ?></option>
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
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=trev-csmap-social-media&tab=share' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Reset Filter', 'the-real-estate-voice' ); ?></a>

		</div>
		<?php } ?>
	</div>
</form>


	<div class="fb-content-list">

		<?php

		$icount    = 0;
		$posts     = get_posts( $args );
		$eparm     = '';
		$args_temp = $args;
		unset( $args_temp['posts_per_page'] );

		$args_temp['posts_per_page'] = -1;
		$posts_temp                  = get_posts( $args_temp );
		$post_count                  = count( $posts_temp );
		$num_pages                   = ceil( $post_count / $post_per_page );
		$general_tab_options         = get_option( 'general_tab_option_name' );

		if ( $post_count > 0 ) {

			foreach ( $posts as $post ) {
				$last_shared      = '';
				$post_id          = $post->ID;
				$status           = get_post_meta( $post_id, 'revoice_sharing_status', true );
				$featured_img_url = get_the_post_thumbnail_url( $post_id, 'full' );

				if ( empty( $featured_img_url ) && isset( $general_tab_options['website_platform'] ) && 'SiteLoft' == $general_tab_options['website_platform'] ) {

					$hero_image_thumbs = get_post_meta( $post_id, 'hero_image_thumbs', true );
					if ( ! empty( $hero_image_thumbs['full'] ) ) {
						$featured_img_url = $hero_image_thumbs['full'];
					}
				}

				$categories = get_the_category( $post_id );

				$revoice_scheduled_id = get_post_meta( $post_id, 'revoice_scheduled_id', true );
				
				$parent_content = get_the_excerpt( $post_id );

				$page_ids = get_post_meta( $revoice_scheduled_id, 'revoice_scheduled_page_id', true );

				$scheduled = get_post_meta( $revoice_scheduled_id, 'revoice_scheduled', true );
				if ( $scheduled < current_time( 'mysql' ) ) {
					$scheduled = '';
				}

				$post_content = get_post( $revoice_scheduled_id );

				$autshare = false;

				if ( metadata_exists( 'post', $post_id, 'revoice_shared' ) ) {

					$revoice_shared = get_post_meta( $post_id, 'revoice_shared', true );
					$last_shared    = date( 'd/m/Y', strtotime( $revoice_shared->date ) );
				}

				?>

				<div class="blog_item">

					<div class="blog_image"><img class="map_image" src="<?php echo esc_url( $featured_img_url ); ?>"></div>   
					<h3 class="map_date"> 

						<?php
						if ( ! empty( $last_shared ) && '' != $last_shared ) {
							?>
						<span><?php esc_html_e( 'Last Shared: ', 'the-real-estate-voice' ); ?> <?php echo esc_html( $last_shared ); ?></span>
						<span class="saperater">  <?php esc_html_e( '| ', 'the-real-estate-voice' ); ?> </span>
					<?php } ?> 

					<?php
					if ( '' != $status || ! empty( $status ) ) {
						?>

					<span><?php esc_html_e( 'Status: ', 'the-real-estate-voice' ); ?>
						<?php echo esc_html( $status ); ?>
						</span>

				<?php } ?>
			</h3>
			<div class="blog_description social-media-desc">

				<h3 class="map_title">
					<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" class="" target="_blank">
						<?php echo esc_html( get_the_title( $post_id ) ); ?> 
					</a>
				</h3>       
				<?php
				if ( is_array( $categories ) ) {

					foreach ( $categories as $cat ) {
						?>

					<div class="map_category"><?php echo esc_html( $cat->cat_name ); ?></div>
						<?php
					}
				}

				?>
			<div class="map_desc">
				<?php

				$get_content = $post->post_excerpt;

				if ( ! empty( $get_content ) ) {
					echo wp_kses_post( $get_content );

				} else {
					$get_content = apply_filters( 'the_content', $post->post_content );
					$get_content = preg_replace( '~(?:\[/?)[^/\]]+/?\]~s', '', $get_content );
					echo wp_kses_post( $get_content );

				}
				?>
			</div>
			<div class="map_content" style="display:none;"><?php echo wp_kses_post( $post->post_content ); ?></div>
		</div>

		<div class="blog_action">
				<?php

				if ( 'Currently scheduled' == $status && trev_csmap_is_pro_license_plugin() ) {
					?>

				<button class="button button-primary edit_schedule edit_post_cl" data-post-type="<?php echo esc_attr( $current_post_type ); ?>" data-event_time="<?php echo esc_attr( $scheduled ); ?>" data-pageid="<?php echo esc_attr( $page_ids ); ?>" data-event="<?php echo esc_attr( get_the_title( $revoice_scheduled_id ) ); ?>" data-id="<?php echo esc_attr( $revoice_scheduled_id ); ?>" data-post-id="<?php echo esc_attr( $post->ID ); ?>" data-content-page="<?php echo esc_attr( 'content' ); ?>" data-image="<?php echo esc_url( $featured_img_url ); ?>" data-parent-content="<?php echo esc_html( $parent_content ); ?>" data-desc="<?php echo esc_html( $post_content->post_content ); ?>"> <?php esc_html_e( 'Edit', 'the-real-estate-voice' ); ?> </button>

				<?php } else { ?>

				<button class="button button-primary map_share_btn" data-post-type="<?php echo esc_attr( $current_post_type ); ?>" data-image="<?php echo esc_url( $featured_img_url ); ?>" data-schedule-id="<?php echo esc_attr( $revoice_scheduled_id ); ?>" data-id="<?php echo esc_attr( $post->ID ); ?>" data-title="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" data-desc="<?php echo esc_html( strip_tags( $get_content ) ); ?>">
					<?php esc_html_e( 'Share', 'the-real-estate-voice' ); ?></button>

			<?php } ?>


		</div>  

	</div>

				<?php

				$icount++;
			}
		} else {
			?>
		<div class="blog_filter post-not-found">
			<p><?php esc_html_e( 'Article not found', 'the-real-estate-voice' ); ?></p>
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
					'tab'      => 'share',
					'paged'    => $prev,
					'type'     => $current_post_type,
					'category' => $selected,
				),
				admin_url( 'admin.php' )
			);
			?>

			<a class="button button-primary" href="<?php echo esc_url( $paginationUrl ); ?>">
				<?php esc_html_e( '<< Previous', 'the-real-estate-voice' ); ?></a>

				<?php
		}

		if ( $page < $num_pages ) {
			$page          = $page + 1;
			$paginationUrl = add_query_arg(
				array(
					'page'     => 'trev-csmap-social-media',
					'tab'      => 'share',
					'paged'    => $page,
					'type'     => $current_post_type,
					'category' => $selected,
				),
				admin_url( 'admin.php' )
			);
			?>

				<a  class="button button-primary" href="<?php echo esc_url( $paginationUrl ); ?>">
				<?php esc_html_e( 'Next >>', 'the-real-estate-voice' ); ?></a>

				<?php } ?>

	</div>
