<?php 

/**
 * Function to update/add property meta (Agent Point Plateform)
 *
 * @package The Real Estate Voice
 * @since 1.1.9
 */
function trev_csmap_update_preperty_meta( $pro_id, $meta_key, $meta_value ){	
	global $wpdb;
	$table = 'property_meta';
	$qry_property = "SELECT meta_id FROM property_meta WHERE property_id=$pro_id AND meta_key='$meta_key'";
	
	$property_meta = $wpdb->get_results( $qry_property );
		
	if( trev_csmap_exist_preperty_meta($pro_id, $meta_key) ){

		$res = $wpdb->query( $wpdb->prepare("UPDATE property_meta SET meta_value = %s WHERE meta_id = %d", $meta_value, $property_meta[0]->meta_id ) );

	}else{
		
		$res = $wpdb->insert( $table, array('property_id'=>$pro_id, 'meta_key'=> $meta_key,'meta_value'=> $meta_value), array( '%d', '%s','%s' ) );
	}

	return $res;
}

/**
 * Function to get property meta (Agent Point Plateform)
 *
 * @package The Real Estate Voice
 * @since 1.1.9
 */
function trev_csmap_get_preperty_meta( $pro_id, $meta_key ){	
	global $wpdb;
	$res =false;

	if( trev_csmap_exist_preperty_meta($pro_id, $meta_key) ){

		$qry_property = "SELECT meta_value FROM property_meta WHERE property_id=$pro_id AND meta_key='$meta_key'";
		
		$property_meta = $wpdb->get_results( $qry_property );
		
		return $property_meta[0]->meta_value;
		
	}

	return $res;
}

/**
 * Function to check property meta exist (Agent Point Plateform)
 *
 * @package The Real Estate Voice
 * @since 1.1.9
 */
function trev_csmap_exist_preperty_meta( $pro_id, $meta_key ){	
	global $wpdb;
	$res =false;		
	
	$qry_property = "SELECT meta_id FROM property_meta WHERE property_id=$pro_id AND meta_key='$meta_key'";
	
	$property_meta = $wpdb->get_results( $qry_property );
	
	if(is_array($property_meta) && !empty($property_meta) && !empty($property_meta[0]->meta_id) ){

		$res = true;

	}

	return $res;
}

/**
 * Function to delete property meta (Agent Point Plateform)
 *
 * @package The Real Estate Voice
 * @since 1.1.9
 */
function trev_delete_preperty_meta( $pro_id, $meta_key ){	
	global $wpdb;
		
	$qry_property ="DELETE FROM property_meta WHERE property_id=$pro_id AND meta_key='$meta_key'";
	
	$property_meta = $wpdb->get_results( $qry_property );

}

add_filter( 'aioseo_meta_views', 'trev_csmap_filter_meta_views' );

function trev_csmap_filter_meta_views( $views ) {

	$general_tab_option_name = get_option( 'general_tab_option_name' );
	
	if ( isset( $general_tab_option_name['website_platform'] ) && 'Agentpoint' == $general_tab_option_name['website_platform'] ) {
		 return [];

	}
   
   
   return $views;
}