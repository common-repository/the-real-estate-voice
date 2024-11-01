<?php

	$get_page = '';
	if ( isset( $_GET['get_page'] ) && ! empty( $_GET['get_page'] ) ) {
		$get_page = sanitize_text_field( wp_unslash( $_GET['get_page'] ) );
	}

	$get_preset = '';
	if ( isset( $_GET['date_preset'] ) && ! empty( $_GET['date_preset'] ) ) {
		$get_preset = sanitize_text_field( wp_unslash( $_GET['date_preset'] ) );
	}

	$get_insta_pages = get_option( 'revoice_get_insta_data' );
	if ( ! empty( $get_insta_pages['ig_user_data'] ) ) { ?>
		<div class="header-dropdown-section">
			<div class="revoic-fb-pages">
				<label><?php echo esc_html__( 'Select Page', 'the-real-estate-voice' ); ?></label>
				<select id="dashboard_fb_pages" name="dashboard_fb_pages">
					<?php
					foreach ( $get_insta_pages['ig_user_data'] as $key => $insta_data ) {
						$selected = '';
						if ( ! empty( $insta_data ) ) {
							if ( $get_page == $insta_data['id'] ) {
								$selected = 'selected';
							}
						}
						?>
						<option value="<?php echo esc_attr( $insta_data['id'] ); ?>" <?php echo esc_attr( $selected ); ?> ><?php echo esc_html( $insta_data['displayname'] ); ?> </option>  
						<?php
					}
					?>
				</select>
			</div>

			<!-- Timeframe Dropdown -->
			<div class="timeframe-dropdown">
				<?php
				$date_presets = array(
					'last_14d' => esc_html__( '14 days', 'the-real-estate-voice' ),
					'last_29d' => esc_html__( '29 days', 'the-real-estate-voice' ),
					
				);
				?>
				<label><?php echo esc_html__( 'Select Duration', 'the-real-estate-voice' ); ?></label>
				<select id="fb_date_preset" name="fb_date_preset">

					<?php
					foreach ( $date_presets as $date_preset => $days ) {
						$selected = '';
						if ( ! empty( $get_preset ) ) {
							if ( $get_preset == $date_preset ) {
								$selected = 'selected';
							}
						}
						?>
						<option value="<?php echo esc_attr( $date_preset ); ?>" <?php echo esc_attr( $selected ); ?> >
							<?php echo esc_html( $days ); ?> 
						</option>  
						<?php
					}
					?>
				</select>
			</div>
			<!-- Timeframe Dropdown -->
		</div>
		<?php
	}

	if ( ! empty( $get_insta_pages['ig_user_ids'] ) ) {
		$ig_user_ids = $get_insta_pages['ig_user_ids'];
	}

	if ( ! empty( $get_page ) ) {
		$insta_id = $get_page;
	} else {
		if ( ! empty( $ig_user_ids ) ) {
			$insta_id = $ig_user_ids[0];
		}
	}

	if ( ! empty( $get_preset ) ) {
		$preset_key = $get_preset;
	} else {
		$preset_key = 'last_14d';
	}


	if ( ! empty( $insta_id ) ) {

		$get_insta_insight = trev_csmap_get_insta_insight( $insta_id );
		$metric_key = $all_insight_data = array();

		if ( ! empty( $get_insta_insight ) ) {
			foreach ( $get_insta_insight as $preset => $preset_data ) {
				foreach ( $preset_data as $key => $insta_api_data ) {
					$metric_key[]                        = $key;
					$all_insight_data[ $preset ][ $key ] = array_values( $insta_api_data );
				}
			}
		}
	}

$days     = explode( '_', $preset_key );
$day      = explode( 'd', $days[1] );
$duration = ( $day[0] - 1 ) / 14;
if ( $duration >= 2 ) {
	$duration = $duration / 2;
}

?>

<canvas id="lineChartFb" width="" height=""></canvas>

<script type="text/javascript">

	const ctx = document.getElementById('lineChartFb');
	const DATA_COUNT = 7;
	const NUMBER_CFG = {count: DATA_COUNT, min: -100, max: 100};
	const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

	Date.prototype.addDays = function(days) {
		var dat = new Date(this.valueOf())
		dat.setDate(dat.getDate() - days);
		return dat;
	}
	let a = Math.round(<?php echo esc_html( $duration ); ?>);
	function getDates(startDate, stopDate) {
		var dateArray = new Array();
		var currentDate = startDate;
		while (currentDate >= stopDate) {
			dateArray.push(currentDate)
			currentDate = currentDate.addDays(1);
		}
		return dateArray;
	}
	var addday = <?php echo esc_html( $day[0] - 1 ); ?>;
	var dateArray = getDates(new Date(), (new Date()).addDays(addday));
	
	var last_30d =[];
	for (var i = dateArray.length; i >= 0; i--) {
	var DateDay = new Date(dateArray[i]);

	last_30d[i]=DateDay.getDate()+" "+monthNames[DateDay.getMonth()];

	}
	last_30d.pop();
	last_30d.reverse();

	const labels = last_30d;
	const data = {
		labels: labels,
		datasets: [
		{
			label: '<?php echo esc_html__( 'Profile View', 'the-real-estate-voice' ); ?>',
			data:<?php echo esc_html( wp_json_encode( $all_insight_data[ $preset_key ]['profile_views'] ) ); ?>,
			borderColor: [
			'rgba(255, 99, 132, 1)',

			],
			backgroundColor: [
			'rgba(255, 99, 132, 1)',

			],
		},
		{
			label:  '<?php echo esc_html__( 'Total Reach', 'the-real-estate-voice' ); ?>',
			data:<?php echo esc_html( wp_json_encode( $all_insight_data[ $preset_key ]['reach'] ) ); ?>,
			borderColor: [
			'#3398c2',

			],
			backgroundColor: [
			'#3398c2',

			],
		},
		{
			label:  '<?php echo esc_html__( 'Impressions', 'the-real-estate-voice' ); ?>',
			data:<?php echo esc_html( wp_json_encode( $all_insight_data[ $preset_key ]['impressions'] ) ); ?>,
			borderColor: [
			'#e7b558',

			],
			backgroundColor: [
			'#e7b558',

			],
		}]
	};

	const myChart = new Chart(ctx, {
		type: 'line',
		data: data,
		options: {
			elements: {
			line: {
				tension: 0.4
			}
			},
			responsive: true,

			aspectRatio:3,
			plugins: {
			legend: {
				position: 'top',
			},
			title: {
				display: true,
				text: '<?php echo esc_html__( 'Instagram Insights', 'the-real-estate-voice' ); ?>',
			}
			}
		},
	});
</script>
