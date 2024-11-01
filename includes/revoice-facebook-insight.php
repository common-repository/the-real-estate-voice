<?php

$get_page = '';
if ( isset( $_GET['get_page'] ) && ! empty( $_GET['get_page'] ) ) {
	$get_page = sanitize_text_field( wp_unslash( $_GET['get_page'] ) );
}

$get_preset = '';
if ( isset( $_GET['date_preset'] ) && ! empty( $_GET['date_preset'] ) ) {
	$get_preset = sanitize_text_field( wp_unslash( $_GET['date_preset'] ) );
}

$get_fb_pages = trev_csmap_get_fb_pages();

if ( ! empty( $get_fb_pages['pages'] ) ) { ?>
	<div class="header-dropdown-section">
		<!-- Facebook page dropdown -->
		<div class="revoic-fb-pages">
			<label><?php echo esc_html__( 'Select Page', 'the-real-estate-voice' ); ?></label>
			<select id="dashboard_fb_pages" name="dashboard_fb_pages">
				<?php
				foreach ( $get_fb_pages['pages'] as $key => $fb_data ) {
					$selected = '';
					if ( ! empty( $get_page ) ) {
						if ( $get_page == $fb_data->fb_id ) {
							$selected = 'selected';
						}
					}
					?>
				<option value="<?php echo esc_attr( $fb_data->fb_id ); ?>" <?php echo esc_attr( $selected ); ?> ><?php echo esc_html( $fb_data->name ); ?> </option>  
					<?php
				}
				?>
			</select>
		</div><!-- Facebook page dropdown -->

		<!-- Timeframe Dropdown -->
		<div class="timeframe-dropdown">
		<?php
		$date_presets = array(
			'last_14d' => esc_html__( '14 days', 'the-real-estate-voice' ),
			'last_30d' => esc_html__( '30 days', 'the-real-estate-voice' ),
			'last_90d' => esc_html__( '90 days', 'the-real-estate-voice' ),
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

$fb_page_ids = get_option( 'fb_pages_ids' );
if ( ! empty( $get_page ) ) {
	$fb_id = $get_page;
} else {
	if ( ! empty( $fb_page_ids ) ) {
		$fb_id = $fb_page_ids[0];
	}
}
if ( ! empty( $get_preset ) ) {
	$preset_key = $get_preset;
} else {
	$preset_key = 'last_14d';
}

if ( ! empty( $fb_id ) ) {

	$revoice_get_reactions_data = trev_csmap_get_reactions_data( $fb_id );
	$fb_impressions_data        = trev_csmap_fb_insights_data( $fb_id );

	$metric_key = $all_insight_data = array();
	if ( ! empty( $fb_impressions_data ) ) {
		foreach ( $fb_impressions_data as $preset => $preset_data ) {
			foreach ( $preset_data as $key => $fb_api_data ) {
				$metric_key[]                        = $key;
				$all_insight_data[ $preset ][ $key ] = array_values( $fb_api_data );
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

	const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun","Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ];

	const metric = '<?php echo esc_html( wp_json_encode( $metric_key ) ); ?>';

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
		var last_14d =[];

		for (var i = dateArray.length; i >= 0; i--) {
			var DateDay = new Date(dateArray[i]);
			last_14d[i]=DateDay.getDate()+" "+monthNames[DateDay.getMonth()];
		}

		last_14d.pop();
		last_14d.reverse();	

		const labels = last_14d;
		const data = {
		labels: labels,
		datasets: [
		{
			label: '<?php echo esc_html__( 'Daily Total Reach', 'the-real-estate-voice' ); ?>',
			data:<?php echo esc_html( wp_json_encode( $all_insight_data[ $preset_key ]['page_impressions_unique'] ) ); ?>,
			borderColor: [
			'rgba(255, 99, 132, 1)',

			],
			backgroundColor: [
			'rgba(255, 99, 132, 1)',

			],
		},
		{
			label: '<?php echo esc_html__( 'Daily Total Impressions', 'the-real-estate-voice' ); ?>',
			data:<?php echo esc_html( wp_json_encode( $all_insight_data[ $preset_key ]['page_impressions'] ) ); ?>,
			borderColor: [
			'#3398c2',

			],
			backgroundColor: [
			'#3398c2',

			],
		},
			{
			label: '<?php echo esc_html__( 'Daily Page Engaged Users', 'the-real-estate-voice' ); ?>',
			data:<?php echo esc_html( wp_json_encode( $all_insight_data[ $preset_key ]['page_engaged_users'] ) ); ?>,
			borderColor: [
			'#e7b558',

			],
			backgroundColor: [
			'#e7b558',

			],
		}
		]
		};
		const myChart = new Chart(ctx, {
		type: 'line',
		data: data,
		options: {
			scales:{
				x: {
				suggestedMin: 14,
				suggestedMax: 14
			},
			},
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
				text: '<?php echo esc_html__( 'Facebook Insights', 'the-real-estate-voice' ); ?>'
			}
			}
		},
		});
</script>
