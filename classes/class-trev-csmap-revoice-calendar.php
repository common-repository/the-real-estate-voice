<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'TREV_CSMAP_Revoice_Calendar' ) ) {

	class TREV_CSMAP_Revoice_Calendar {

		private $active_year, $active_month, $active_day;
		private $events = array();

		/**
		 * Construct function
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function __construct( $date = null ) {
			$this->active_year  = $date != null ? date( 'Y', strtotime( $date ) ) : date( 'Y' );
			$this->active_month = $date != null ? date( 'm', strtotime( $date ) ) : date( 'm' );
			$this->active_day   = $date != null ? date( 'd', strtotime( $date ) ) : date( 'd' );
		}

		/**
		 * Add event to the calander
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */
		public function trev_csmap_add_event( $txt, $date, $time = '', $post_id = '', $days = 1, $color = '' ) {
			$color          = $color ? ' ' . $color : $color;
			$this->events[] = array( $txt, $date, $days, $color, $time, $post_id );
		}

		/**
		 * Function to convert into the string
		 *
		 * @package The Real Estate Voice
		 * @since 1.0.0
		 */

		public function trev_csmap_display_calendar() {

			$general_tab_option_name = get_option( 'general_tab_option_name' );

			$agentPoint = false;

			if ( isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform'] ) {

				$agentPoint = true;

			}
			$general_tab_options = get_option( 'general_tab_option_name' );	
			$current_time_zone   = get_option( 'timezone_string' );

			if( !empty( $current_time_zone ) ){
				date_default_timezone_set( $current_time_zone );	
			}
			
			

			$args        = array(
				'posts_per_page' => 1,
				'post_type'      => 'post_share_schedules',
				'meta_key'       => 'revoice_scheduled',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
				'post_status'    => 'future',
			);
			$small_value = get_posts( $args );
			$start_year  = date( 'Y' );
			if ( is_array( $small_value ) ) {
				foreach ( $small_value as $sm ) {
					$scheduled  = get_post_meta( $sm->ID, 'revoice_scheduled', true );
					$start_year = date( 'Y', strtotime( $scheduled ) );
				}
			}

			$args = array(
				'posts_per_page' => 1,
				'post_type'      => 'post_share_schedules',
				'meta_key'       => 'revoice_scheduled',
				'orderby'        => 'meta_value_num',
				'order'          => 'DESC',
				'post_status'    => 'future',
			);

			$big_value = get_posts( $args );
			$end_year  = date( 'Y', strtotime( '+1 year' ) );

			if ( is_array( $big_value ) ) {
				foreach ( $big_value as $bg ) {
					$scheduled = get_post_meta( $bg->ID, 'revoice_scheduled', true );
					$end_year  = date( 'Y', strtotime( $scheduled ) );
				}
			}

			$mnames = array(
				esc_html__( 'January', 'the-real-estate-voice' ),
				esc_html__( 'February', 'the-real-estate-voice' ),
				esc_html__( 'March', 'the-real-estate-voice' ),
				esc_html__( 'April', 'the-real-estate-voice' ),
				esc_html__( 'May', 'the-real-estate-voice' ),
				esc_html__( 'June', 'the-real-estate-voice' ),
				esc_html__( 'July', 'the-real-estate-voice' ),
				esc_html__( 'August', 'the-real-estate-voice' ),
				esc_html__( 'September', 'the-real-estate-voice' ),
				esc_html__( 'October', 'the-real-estate-voice' ),
				esc_html__( 'November', 'the-real-estate-voice' ),
				esc_html__( 'December', 'the-real-estate-voice' ),
			);

			$num_days            = date( 't', strtotime( $this->active_day . '-' . $this->active_month . '-' . $this->active_year ) );
			$active_date         = date( 'd-m-Y', strtotime( $this->active_day . '-' . $this->active_month . '-' . $this->active_year ) );
			$num_days_last_month = date( 'j', strtotime( 'last day of previous month', strtotime( $this->active_day . '-' . $this->active_month . '-' . $this->active_year ) ) );
			$days                = array(
				0 => 'Sun',
				1 => 'Mon',
				2 => 'Tue',
				3 => 'Wed',
				4 => 'Thu',
				5 => 'Fri',
				6 => 'Sat',
			);

			$first_day_of_week = array_search( date( 'D', strtotime( $this->active_year . '-' . $this->active_month . '-1' ) ), $days );

			$year_array   = array();
			$year_array[] = date( 'Y' );
			$year_array[] = date( 'Y', strtotime( '+1 year' ) );
			$html         = '<div class="calendar">';
			$html        .= '<div class="header">';
			$html        .= '<div class="month-year">';
			$html        .= date( 'F Y', strtotime( $this->active_year . '-' . $this->active_month . '-' . $this->active_day ) );
			$html        .= '</div>';
			$html        .= '<form method="get">
            <input type="hidden" name="page" value="trev-csmap-calendar">
          
            <div class="select-month-year">';
			$html        .= "<select name='month'>";
			$mid          = 1;

			foreach ( $mnames as $mn ) {
				$selected_class = '';
				if ( $this->active_month == $mid ) {
					$selected_class = 'selected';
				}
				$main_id = $mid;
				if ( $mid < 10 ) {
					$main_id = '0' . $main_id;
				}
				$html .= '<option value="' . esc_attr( $main_id ) . '" ' . esc_attr( $selected_class ) . '>' . esc_html( $mn ) . '</option>';
				$mid   = $mid + 1;
			}

			$html .= '</select>';
			$html .= "<select name='year'>";

			foreach ( $year_array as $key => $year ) {

				$selected_class = '';

				if ( $this->active_year == $year ) {
					$selected_class = 'selected';
				}

				$html .= '<option value="' . esc_attr( $year ) . '" ' . esc_attr( $selected_class ) . '>' . esc_html( $year ) . '</option>';
			}

			$html .= '</select>';
			$html .= '<button type="submit" class="button button-primary">' . esc_html__( 'Go', 'the-real-estate-voice' ) . '</button>';
			$html .= '</div></form>';
			$html .= '</div>';
			$html .= '<div class="days">';

			foreach ( $days as $day ) {
				$html .= '
                <div class="day_name">
                ' . esc_html( $day ) . '
                </div>
                ';
			}

			for ( $i = $first_day_of_week; $i > 0; $i-- ) {
				$html .= '
                <div class="day_num ignore">
                ' . esc_html( ( $num_days_last_month - $i + 1 ) ) . '
                </div>
                ';
			}
			$addable = true;

			for ( $i = 1; $i <= $num_days; $i++ ) {

				$selected = '';

				if ( $i == $this->active_day && date( 'm' ) == $this->active_month && date( 'Y' ) == $this->active_year ) {
					$selected = ' selected';
				}

				$html .= '<div class="day_num' . esc_attr( $selected ) . '">';
				$html .= '<span>' . esc_html( $i ) . '</span>';
				
				foreach ( $this->events as $event ) {

					for ( $d = 0; $d <= ( $event[2] - 1 ); $d++ ) {
						$listing_color = '';
						
						$post_id       = $event[4];

						$content_post  = get_post($post_id);

						$page_ids      = get_post_meta( $post_id, 'revoice_scheduled_page_id', true );

						$insta_ids = get_post_meta( $post_id, 'revoice_scheduled_insta_id', true );

						$page_listing = get_post_meta( $post_id, 'revoice_listingpage', true );
					
						$datetime     = $event[1] . ' ' . $event[5];
						$current_date = date( 'Y-m-d', strtotime( $datetime ) );

						$post_color        = '';
						$revoice_post_type = get_post_meta($post_id, 'revoice_post_type', true );

						if ( $datetime < current_time( 'mysql' ) ) {

							$listing_color        = '#a0a0a0';
							$insta_schedule_color = '#a0a0a0';
						} else {

							if ( 'post' == $revoice_post_type ) {
								$listing_color = '#132c36';
							} elseif ( 'listing' == $revoice_post_type ) {
								$listing_color = '#3399c3';
							}
							$insta_schedule_color = '#FF0000';
						}

						$parent_id       = get_post_meta( $post_id, 'revoice_scheduled_post_id', true );
							
						$parent_content  = get_the_excerpt( $parent_id );

						$listing_details = trev_csmap_get_web_plateform_data($revoice_post_type, $parent_id);
						
						if( 'property' == $revoice_post_type ){
															
							$get_post_data = trev_csmap_get_agentpoint_data( $post_id );
							$permalink = get_site_url().'/'.$parent_id;	
							$listing_details['listing_link'] 	= $permalink;
							
						}else{

							$get_post_data = trev_csmap_get_post_data( $post_id );	
						}
	
							$bedroom_emoji   = '';
							if ( ! empty( $listing_details['bedroom'] ) ) {
								$bedroom_emoji                    = '&#128716;';
							}

							$bathroom_emoji  = '';
							if ( ! empty( $listing_details['bathroom'] ) ) {
								$bathroom_emoji                    = '&#128704;';
							}

							$garage_emoji    = '';
							if ( ! empty( $listing_details['garage'] ) ) {
								$garage_emoji 					   = '&#128663;';
							}

							$js_code         = json_encode( $listing_details );

						if ( date( 'y-m-d', strtotime( $this->active_year . '-' . $this->active_month . '-' . $i . ' -' . $d . ' day' ) ) == date( 'y-m-d', strtotime( $event[1] ) ) ) {

							$html .= '<div class="event' . $event[3] . '">';

							$revoice_platform = get_post_meta( $post_id, 'revoice_platform', true );

							if ( 'instagram' == $revoice_platform ) {

								$get_post_image = get_post_meta( $post_id, 'revoice_post_image', true );

								$html .= '<a href="javascript:void(0)" style="color:' . esc_attr( $insta_schedule_color ) . '" class="insta_edit_schedule edit_post_cl" data-event_time="' . esc_attr( $datetime ) . '" data-event="' . esc_attr( $event[0] ) . '" data-id="' . esc_attr( $event[4] ) . '" data-image="' . esc_url( $get_post_image ) . '"  data-content-page="' . esc_url('scheduled') . '" data-instaid="' . esc_attr( wp_json_encode( $insta_ids ) ) . '" data-post-type="' . esc_attr( $revoice_post_type ) . '" data-desc="' . esc_attr( $content_post->post_content ) . '"> ' . esc_html( $event[5] ) . '  ' . esc_html( $event[0] ) . '</a>';

							} else {

								if ( 'listings' == $revoice_post_type || 'listing' == $revoice_post_type || 'property' == $revoice_post_type) {
									$plugin_status = false;
									if ( ! empty( $event[1] ) && $event[1] < current_time( 'mysql' ) ) {
										$event[1] = '';

										
									}
									ob_start();
									if ( trev_csmap_is_pro_license_plugin() ) {
										$plugin_status = trev_csmap_is_pro_license_plugin();
									}

									?>
									<a style="color:<?php echo esc_attr( $listing_color ); ?>" href="javascript:void(0)" class="edit_schedule edit_post_cl" data-event_time="<?php echo esc_attr( $scheduled ); ?>" data-paglisting="<?php echo esc_attr( $page_listing ); ?>" data-listing-details='<?php echo esc_attr( $js_code ); ?>'  data-parent-content="<?php echo esc_attr( $parent_content ); ?>"  data-post-type="<?php echo esc_attr( $revoice_post_type ); ?>" data-event_time="<?php echo esc_attr( $datetime ); ?>" data-pageid="<?php echo esc_attr( $page_ids ); ?>" data-event="<?php echo esc_attr( $event[0] ); ?>" data-id="<?php echo esc_attr( $event[4] ); ?>" data-image="<?php echo esc_url( $get_post_data['image_url'] ); ?>"  data-content-page="<?php echo esc_attr( 'scheduled' ); ?>" data-desc="<?php echo esc_attr( $get_post_data['desc'] ); ?>" data-plugin-status ="<?php echo esc_attr( $plugin_status ); ?>"> <?php echo esc_html( $event[5] ) . ' ' . esc_html( $event[0] ); ?></a>

									<?php

									$cal_sch = ob_get_contents();
									ob_end_clean();
									$html .= $cal_sch;

								} else {

									if ( ! empty( $event[1] ) && $event[1] < current_time( 'mysql' ) ) {
										$event[1] = '';

									}
									$html .= '<a href="javascript:void(0)" style="color:' . esc_attr( $listing_color ) . '" class="edit_schedule edit_post_cl" data-event_time="' . esc_attr( $datetime ) . '" data-pageid="' . esc_attr( $page_ids ) . '" data-paglisting ="' . $page_listing . '" data-event="' . esc_attr( $event[0] ) . '" data-id="' . esc_attr( $event[4] ) . '" data-image="' . esc_url( $get_post_data['image_url'] ) . '"  data-content-page="' . esc_attr( 'scheduled' ) . '" data-parent-content="' . esc_attr( $parent_content ) . '"  data-post-type="' . esc_attr( $revoice_post_type ) . '" data-desc="' . esc_attr( $get_post_data['desc'] ) . '"> ' . esc_html( $event[5] ) . '  ' . esc_html( $event[0] ) . '</a>';
								}
							}
							$html       .= '</div>';
							$addable     = false;
							$schedule_id = $event[4];
						}
					}
				}
				if ( ! $addable ) {
					$html .= '';
				}
				$addable = true;
				$html   .= '</div>';
			}
			for ( $i = 1; $i <= ( 42 - $num_days - max( $first_day_of_week, 0 ) ); $i++ ) {
				$html .= '
                <div class="day_num ignore">
                ' . esc_html( $i ) . '
                </div>
                ';
			}
			$html .= '</div>';
			$html .= '</div>';
			return $html;
		}
	}
}
