<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if ( ! class_exists( 'TREV_CSMAP_Schedules_Table' ) ) {

	class TREV_CSMAP_Schedules_Table extends WP_List_Table {

		/**
		 * Constructor, we override the parent to pass our own arguments
		 *
		 * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		function __construct() {
			parent::__construct(
				array(
					'singular' => 'rev_schedules_list_table',
					'plural'   => 'rev_schedules_list_tables',
					'ajax'     => false,
				)
			);
		}

		/**
		 * Override Columns
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function get_columns() {
			return $columns = array(
				'col_title'    => esc_html__( 'Title', 'the-real-estate-voice' ),
				'col_desc'     => esc_html__( 'Description', 'the-real-estate-voice' ),
				'col_datetime' => esc_html__( 'Scheduled at', 'the-real-estate-voice' ),
				'col_type'     => esc_html__( 'Event type', 'the-real-estate-voice' ),
				'col_actions'  => esc_html__( 'Actions', 'the-real-estate-voice' ),
			);
		}


		/**
		 * Decide which columns to activate the sorting functionality on
		 *
		 * @return array $sortable, the array of columns that can be sorted by the user
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function get_sortable_columns() {
			return $sortable = array(
				'col_id'       => array( 'ID', true ),
				'col_title'    => array( 'post_title', true ),
				'col_desc'     => array( 'post_content', true ),
				'col_datetime' => array( 'datetime', true ),
				'col_type'     => array( 'event_type', true ),
			);
		}

		/**
		 * Prepare the table with different parameters, pagination, columns and table elements
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function prepare_items() {
			global $usersearch;

			$usersearch = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( trim( $_REQUEST['s'] ) ) ) : '';

			$per_page      = ( $this->is_site_users ) ? 'site_posts_network_per_page' : 'posts_per_page';
			$post_per_page = $this->get_items_per_page( $per_page );
			$paged         = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

			$args = array(
				'posts_per_page' => $post_per_page,
				'post_type'      => 'post_share_schedules',
				'paged'          => $paged,
				'post_status'    => 'future',
			);

			if ( ! empty( $args['search'] ) ) {
				$args['s'] = '*' . $args['search'] . '*';
			}

			if ( isset( $_REQUEST['orderby'] ) ) {
				if ( 'datetime' == $_REQUEST['orderby'] ) {
					$args['orderby']  = 'meta_value';
					$args['meta_key'] = 'revoice_scheduled';
				} elseif ( 'status' == $_REQUEST['orderby'] ) {
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = 'revoice_scheduled_status';
				} elseif ( 'event_type' == $_REQUEST['orderby'] ) {
					$args['orderby']  = 'meta_value';
					$args['meta_key'] = 'revoice_platform';
				} else {
					$args['orderby'] = sanitize_text_field( $_REQUEST['orderby'] );
				}
			}

			if ( isset( $_REQUEST['order'] ) ) {
				$args['order'] = sanitize_text_field( $_REQUEST['order'] );
			}

			$wp_post_search = new WP_Query( $args );
			$this->items    = $wp_post_search->posts;

			$this->set_pagination_args(
				array(
					'total_items' => $wp_post_search->found_posts,
					'per_page'    => $post_per_page,
				)
			);

			$columns               = $this->get_columns();
			$sortable              = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, array(), $sortable );
		}

		/**
		 * Display the rows of records in the table
		 *
		 * @return string, echo the markup of the rows
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function display_rows() {

			// Get the records registered in the prepare_items method
			$posts = $this->items;

			// Get the columns registered in the get_columns and get_sortable_columns methods
			list( $columns, $hidden ) = $this->get_column_info();
			$general_tab_options      = get_option( 'general_tab_option_name' );
			// Loop for each record
			if ( ! empty( $posts ) ) {
				foreach ( $posts as $post ) {
					

					// Current fb page id of schedule post
					$page_ids               = get_post_meta( $post->ID, 'revoice_scheduled_page_id', true );
					$insta_ids              = get_post_meta( $post->ID, 'revoice_scheduled_insta_id', true );
					$revoice_post_type      = get_post_meta( $post->ID, 'revoice_post_type', true );

					$parent_id              = get_post_meta( $post->ID, 'revoice_scheduled_post_id', true );

					$revoice_listing_detail = get_post_meta( $post->ID, 'revoice_listing_detail', true );

					$parent_content         = get_the_excerpt( $parent_id );

					$listing_details 		= trev_csmap_get_web_plateform_data($revoice_post_type ,$parent_id);

					$get_post_data = trev_csmap_get_post_data( $post->ID );

					if( 'property' == $revoice_post_type ){

						$get_post_data = trev_csmap_get_agentpoint_data( $post->ID );
						$permalink = get_site_url().'/'.$parent_id;	
						$listing_details['listing_link'] 	= $permalink;

					}else{

						$get_post_data = trev_csmap_get_post_data( $post->ID );	
					}

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

					$js_code                = json_encode( $listing_details );
					
					echo '<tr id="record_' . esc_attr( $post->ID ) . '">';

					$scheduled = get_post_meta( $post->ID, 'revoice_scheduled', true );

					$scheduled_dt = date( 'l, F jS, Y H:i', strtotime( $scheduled ) );

					$scheduled_status = get_post_meta( $post->ID, 'revoice_scheduled_status', true );
					$revoice_platform = get_post_meta( $post->ID, 'revoice_platform', true );
					

					foreach ( $columns as $column_name => $column_display_name ) {

						$class = "class='$column_name column-$column_name'";
						$style = '';
						if ( in_array( $column_name, $hidden ) ) {
							$style = ' style="display:none;"';
						}
						$attributes = $class . $style;

						$editlink    = '#';
						$description = $post->post_content;

						switch ( $column_name ) {
							case 'col_id':
								echo '<td ' . esc_attr( $attributes ) . '><a href="' . $editlink . '">' . esc_html( $post->ID ) . '</a></td>';
								break;
							case 'col_title':
								echo '<td ' . esc_attr( $attributes ) . '>' . esc_html( $post->post_title ) . '</td>';
								break;
							case 'col_desc':
								echo '<td ' . esc_attr( $attributes ) . '>' . esc_html( $post->post_content ) . '</td>';
								break;
							case 'col_datetime':
								echo '<td ' . esc_attr( $attributes ) . '>' . esc_html( $scheduled_dt ) . '</td>';
								break;

							case 'col_type':
								echo '<td ' . esc_attr( $attributes ) . '>' . esc_html( ucfirst( $revoice_platform )) . '</td>';
								break;
							case 'col_actions':
								

								if ( 'instagram' == $revoice_platform ) {

									$get_post_image = get_post_meta( $post->ID, 'revoice_post_image', true );

									echo '<td ' . esc_attr( $attributes ) . '>

									<button class="button button-danger delete_insta_schedule" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Delete', 'the-real-estate-voice' ) . '</button>


									<button class="button button-primary insta_edit_schedule edit_post_cl" data-event_time="' . esc_attr( $scheduled ) . '" data-pageid="' . esc_attr( $page_ids ) . '" data-event="' . esc_attr( $post->post_title ) . '" data-id="' . esc_attr( $post->ID ) . '" data-post-type="' . esc_attr( $revoice_post_type ) . '" data-content-page="' . esc_attr( 'scheduled' ) . '" data-image="' . esc_url( $get_post_image ) . '" data-instaid="' . esc_attr( wp_json_encode( $insta_ids ) ) . '" data-parent-content="' . esc_attr( $parent_content ) . '" data-desc="' . esc_attr( $post->post_content ) . '"> ' . esc_html__( 'Edit', 'the-real-estate-voice' ) . ' </button></td>';
									break;

								} else {

									if ( 'listings' == $revoice_post_type || 'listing' == $revoice_post_type || 'property' == $revoice_post_type) {
										if ( ! empty( $scheduled ) && $scheduled < current_time( 'mysql' ) ) {
											$scheduled = '';
										}
										?>
								<td <?php echo esc_attr( $attributes ); ?> >
									<button class="button button-danger delete_schedule" data-id="<?php echo esc_attr( $post->ID ); ?>"><?php esc_html_e( 'Delete', 'the-real-estate-voice' ); ?></button>

									<button class="button button-primary edit_schedule edit_post_cl" data-event_time="<?php echo esc_attr( $scheduled ); ?>" data-bedroom-emoji="<?php echo esc_attr( $bedroom_emoji ); ?>" data-bathroom-emoji="<?php echo esc_attr( $bathroom_emoji ); ?>" data-garage-emoji="<?php echo esc_attr( $garage_emoji ); ?>" data-address="<?php echo esc_attr( $listing_details['map_address'] ); ?>" data-listing-link="<?php echo esc_url( $listing_details['listing_link'] ); ?>" data-bedroom="<?php echo esc_attr( $listing_details['bedroom'] ); ?>" data-bathroom="<?php echo esc_attr( $listing_details['bathroom'] ); ?>" data-garage="<?php echo esc_attr( $listing_details['garage'] ); ?>" data-pageid="<?php echo esc_attr( $page_ids ); ?>" data-event="<?php echo esc_attr( $post->post_title ); ?>" data-id="<?php echo esc_attr( $post->ID ); ?>" data-post-type="<?php echo esc_attr( $revoice_post_type ); ?>" data-content-page="scheduled"  data-listing-details='<?php echo esc_attr( $js_code ); ?>' data-image="<?php echo esc_url( $get_post_data['image_url'] ); ?>" data-parent-content="<?php echo esc_attr( $parent_content ); ?>" data-desc="<?php echo esc_attr( $post->post_content ); ?>"> <?php esc_html_e( 'Edit', 'the-real-estate-voice' ); ?> </button></td>
										<?php
										break;

									} else {

										if ( ! empty( $scheduled ) && $scheduled < current_time( 'mysql' ) ) {
											$scheduled = '';
										}
										echo '<td ' . esc_attr( $attributes ) . '>
									<button class="button button-danger delete_schedule" data-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Delete', 'the-real-estate-voice' ) . '</button>

									<button class="button button-primary edit_schedule edit_post_cl" data-event_time="' . esc_attr( $scheduled ) . '" data-pageid="' . esc_attr( $page_ids ) . '" data-event="' . esc_attr( $post->post_title ) . '" data-id="' . esc_attr( $post->ID ) . '" data-post-type="' . esc_attr( $revoice_post_type ) . '" data-content-page="scheduled" data-image="' . esc_url( $get_post_data['image_url'] ) . '" data-parent-content="' . esc_attr( $parent_content ) . '" data-desc="' . esc_attr( $post->post_content ) . '"> ' . esc_html__( 'Edit', 'the-real-estate-voice' ) . ' </button></td>';
										break;

									}
								}
						}
					}

					echo '</tr>';
				}
			}
		}

	}
}
