<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_trev_csmap_toggle_auto_post', 'trev_csmap_toggle_auto_post' );

/**
 * Ajax Callback function to enable/desable auto post
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_toggle_auto_post() {
        check_ajax_referer( 'revoice_nonce_custom', 'security' );
        if(isset($_POST['status'])){
            $status = $_POST['status'];

            $temp = get_option( 'auto_post_option_name' );
            
            if(isset($temp['auto_enabled_1']) && 'yes' == $temp['auto_enabled_1']){
                unset($temp['auto_enabled_1']);
                update_option( 'auto_post_option_name',$temp );
            }else{
                $temp['auto_enabled_1'] = 'yes';
                update_option( 'auto_post_option_name',$temp );
            }

           
            $data = array('success'=>true);
            wp_send_json($data);

            exit;
        }
}

add_action( 'wp_ajax_trev_csmap_toggle_share', 'trev_csmap_toggle_share' );

/**
 * Ajax Callback function to enable/desable share article
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_toggle_share() {
    check_ajax_referer( 'revoice_nonce_custom', 'security' );
        if(isset($_POST['id'])){
             $fb_ids = get_option( 'fb_pages_ids' );
             
            
             if(!empty($fb_ids)){ 
                 $temp = get_option( 'auto_post_option_name' );

                    if('toggle_share_article' == $_POST['id']){

                        if(isset($temp['enabled_sharing_for']['post']) && 1 == $temp['enabled_sharing_for']['post']){

                            unset( $temp['enabled_sharing_for']['post'] );
                            update_option( 'auto_post_option_name', $temp );

                        }else{

                            $temp['enabled_sharing_for']['post'] = 1;
                            update_option( 'auto_post_option_name', $temp );

                        }
                    }

                    if('toggle_share_listing' == $_POST['id']){

                        if(isset($temp['enabled_sharing_for']['listing']) && 1 == $temp['enabled_sharing_for']['listing']) {
                             unset( $temp['enabled_sharing_for']['listing'] );
                            update_option( 'auto_post_option_name', $temp );
                        }else{

                            $temp['enabled_sharing_for']['listing'] = 1;
                            update_option( 'auto_post_option_name', $temp );

                        }
                    }
                
                $data = array('success'=>true);

            }else{

                $fb_connect_url =admin_url('admin.php?page=trev-csmap-settings&tab=auto-social');
                $data = array('success'=>false,'redirect'=> $fb_connect_url);

            }
      
        wp_send_json($data);

        exit;
    }
}


add_action( 'wp_ajax_trev_csmap_schedule_article', 'trev_csmap_schedule_article' );

/**
 * Ajax Callback function to make schedule for article
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_schedule_article() {
    check_ajax_referer( 'revoice_nonce_fb', 'security' );
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
  
    $general_tab_options = get_option( 'general_tab_option_name' );

    $bcats = sanitize_text_field( wp_unslash( $_POST['categories'] ) );

    $categories = json_decode( $bcats );

    $title = sanitize_text_field( wp_unslash( $_POST['title'] ) );

    $content = wp_kses_post( $_POST['content'] );

    $image = esc_url_raw( wp_unslash( $_POST['image'] ) );

    $post_excerpt = sanitize_text_field( wp_unslash( $_POST['post_excerpt'] ) );

    $revcp_settings_options = get_option( 'revcp_settings_option_name' );

    $attribute_posts_to_this_user_1 = $revcp_settings_options['attribute_posts_to_this_user_1'];

    $date_time    = sanitize_text_field( wp_unslash( $_POST['datetime'] ) );

    $postdate     = date( 'Y-m-d H:i:s', strtotime( $date_time ) );

    $postdate_gmt = get_gmt_from_date( $date_time, 'Y-m-d H:i:s' );
    
    $cats = array();

    if ( is_array( $categories ) ) {

        foreach ( $categories as $cat ) {

            $parent_term = term_exists( $cat, 'category' );

            if ( ! is_array( $parent_term ) ) {
                $term = wp_insert_term( $cat, 'category' );

                if ( is_array( $term ) ) {
                    $cats[] = $term['term_id'];
                }
            } else {
                $cats[] = $parent_term['term_id'];
            }
        }
    }

    $my_post = array(
        'post_title'    => wp_strip_all_tags( $title ),
        'post_content'  => $content,
        'post_excerpt'  => $post_excerpt,
        'post_status'   => 'future',
        'post_type'     => 'post',
        'post_date_gmt' => $postdate_gmt,
        'post_date'     => $postdate,
        'edit_date'     => 'true',
        'post_author'   => $attribute_posts_to_this_user_1,
        'post_category' => $cats,

    );

    $thumbnailWithSize = array();
    $post_id           = wp_insert_post( $my_post );
    if ( ! is_wp_error( $post_id ) ) {
        update_post_meta( $post_id, 'revoice_sharing_status', 'Never Shared' );
    }

    $response = wp_remote_get(
        $image,
        array(
            'timeout'   => 20,
            'sslverify' => false,
        )
    );

    if ( ! is_wp_error( $response ) ) {

        $bits         = wp_remote_retrieve_body( $response );
        $filename     = strtotime( 'now' ) . '_' . uniqid() . '.jpg';
        $upload       = wp_upload_bits( $filename, null, $bits );
        $data['guid'] = $upload['url'];

        if ( ! empty( $upload['url'] ) ) {

            $data['post_mime_type'] = 'image/jpeg';
            $attach_id              = wp_insert_attachment( $data, $upload['file'], 0 );
            set_post_thumbnail( $post_id, $attach_id );

            if ( isset( $general_tab_options['website_platform'] ) && 'SiteLoft' == $general_tab_options['website_platform'] && is_plugin_active( 'wordpress-sync-plugin/functions.php' ) ) {

                update_post_meta( $post_id, 'hero_image', $attach_id );
                $all_sizes   = get_intermediate_image_sizes();
                $all_sizes[] = 'full';

                if ( ! empty( $all_sizes ) ) {
                    foreach ( $all_sizes as $key => $reg_size ) {
                        $thumbnailWithSize[ $reg_size ] = get_the_post_thumbnail_url( $post_id, $reg_size );
                    }
                }
                update_post_meta( $post_id, 'hero_image_thumbs', $thumbnailWithSize );

            }
        }
    }

    $data = array();

    $data['status'] = 'future';

    $data['post_id'] = $post_id;

    $data['editpage'] = get_edit_post_link( $post_id );

    wp_send_json( $data );
    die();
}

add_action( 'wp_ajax_trev_csmap_check_licence_ajax', 'trev_csmap_check_licence_ajax' );

/**
 * Ajax Callback function to licence manually callback
 *
 * @package The Real Estate Voice
 * @since 1.0.0
 */
function trev_csmap_check_licence_ajax() {
    check_ajax_referer( 'revoice_nonce_custom', 'security' );

    trev_csmap_action_check_license_pro();

    $args = array(
        'page' => 'trev-csmap-settings',
        // 'tab'  => 'general',
    );

    $redirect = add_query_arg(
        $args,
        admin_url('admin.php')
    );  

    $data = array(
        'success'  => true,
        'redirect' => $redirect
    );

    wp_send_json($data);

    exit;
    
}