<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current = date( 'Y-m-d' );
$year    = date( 'Y' );
$month   = date( 'm' );

if ( isset( $_GET['year'] ) ) {
	if ( isset( $_GET['month'] ) ) {
		$year    = sanitize_text_field( wp_unslash( $_GET['year'] ) );
		$month   = sanitize_text_field( wp_unslash( $_GET['month'] ) );
		$current = sanitize_text_field( wp_unslash( $_GET['year'] ) ) . '-' . sanitize_text_field( wp_unslash( $_GET['month'] ) ) . '-' . date( 'd' );
	}
}

$calendar   = new TREV_CSMAP_Revoice_Calendar( $current );
$start_date = date( $year . '-' . $month . '-' . '01' );
$end_date   = date( $year . '-' . $month . '-' . 't' );

$the_query = new WP_Query(
	array(
		'posts_per_page' => -1,
		'post_type'      => 'post_share_schedules',
		'meta_key'       => 'revoice_scheduled',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
		'post_status'    => array( 'publish', 'future', 'draft' ),
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => 'revoice_scheduled',
				'value'   => array( $start_date, $end_date ),
				'compare' => 'BETWEEN',
				'type'    => 'DATE',
			),

		),
	)
);


if ( $the_query->have_posts() ) {

	while ( $the_query->have_posts() ) {
		$the_query->the_post();

		$post_id    = get_the_ID();
		$date_time  = get_post_meta( $post_id, 'revoice_scheduled', true );
		$post_title = get_the_title( $post_id );
		$datetime   = date( 'Y-m-d', strtotime( $date_time ) );
		$time       = date( 'H:i', strtotime( $date_time ) );

		$calendar->trev_csmap_add_event( $post_title, $datetime, $post_id, $time );
	}
}

wp_reset_postdata();

echo $calendar->trev_csmap_display_calendar();

