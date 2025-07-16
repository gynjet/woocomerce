<?php

function fca_pc_woo_ajax_add_to_cart() {

	$p = fca_pc_get_woo_product( sanitize_text_field( $_POST['product_id'] ) );

	if ( $p ) {
		
		$options = get_option( 'fca_pc', array() );
		$woo_id_mode = empty( $options['woo_product_id'] ) ? 'post_id' : $options['woo_product_id'];
		$id = $woo_id_mode === 'post_id' ? $p->get_id() : $p->get_sku();
		$content_type = $p->get_type() === 'variable' ? 'product_group' : 'product';
		$value = wc_get_price_to_display( $p );
		$currency = get_woocommerce_currency();
		
		$data = array(
			'facebook' => array(
				'value' => $value,
				'currency' => $currency,
				'content_name' => $p->get_title(),
				'content_ids' => array( $id ),
				'content_type' => $content_type,
			),	
			'tiktok' => array(
				'value' => $value,
				'currency' => $currency,
				'content_name' => $p->get_title(),
				'content_ids' => array( $id ),
				'content_type' => $content_type,
			),
			'ga' => array(
				'value' => $value,
				'currency' => $currency,
				'items' => array( $id ),
			),
			'pinterest' => array(
				'value' => $value,
				'currency' => $currency,
				'product_name' =>$p->get_title(),
				'product_id' => $id,
			),
			'snapchat' => array(
				'price' => $value,
				'currency' => $currency,
				'description' =>$p->get_title(),
				'item_ids' => array( $id ),
			),		
		);
		
		wp_send_json_success( $data );
		
	}
	
	wp_send_json_error();
}
add_action( 'wp_ajax_fca_pc_woo_ajax_add_to_cart', 'fca_pc_woo_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_fca_pc_woo_ajax_add_to_cart', 'fca_pc_woo_ajax_add_to_cart' );


function fca_pc_capi_event() {
	
	$nonce = sanitize_text_field( $_POST['nonce'] );
	
	if( wp_verify_nonce( $nonce, 'fca_pc_capi_nonce' ) === false ){
		wp_send_json_error( 'Unauthorized, please log in and try again.' );
	}
		
	$options = get_option( 'fca_pc', array() );

	$pixels = fca_pc_get_active_pixels( $options );
	forEach( $pixels as $pixel ){
		
		$pixel_id = empty( $pixel['pixel'] ) ? '' : $pixel['pixel'];
		$pixel_type = empty( $pixel['type'] ) ? '' : $pixel['type'];
		$capi_token = empty( $pixel['capi'] ) ? '' : $pixel['capi'];
		$test_code = empty( $pixel['test'] ) ? '' : $pixel['test'];
		
		if( ( ( $pixel_type === 'Conversions API' ) && $pixel_id && $capi_token ) ) {
			fca_pc_fb_api_call( $pixel_id, $capi_token, $test_code );
		}

	}
	
	wp_send_json_success();

}
add_action( 'wp_ajax_fca_pc_capi_event', 'fca_pc_capi_event' );
add_action( 'wp_ajax_nopriv_fca_pc_capi_event', 'fca_pc_capi_event' );

function fca_pc_fb_api_call( $pixel, $capi_token, $test_code ){

	$url = "https://graph.facebook.com/v11.0/$pixel/events?access_token=$capi_token";
	$event_name = sanitize_text_field( $_POST['event_name'] );
	$event_time = sanitize_text_field( $_POST['event_time'] );
	$external_id = sanitize_text_field( $_POST['external_id'] );
	$fbc = empty( $_COOKIE['_fbc'] ) ? '' : sanitize_text_field( $_COOKIE['_fbc'] );
	$click_id = sanitize_text_field( $_POST['click_id'] );
	$event_id = sanitize_text_field( $_POST['event_id'] );
	$ip_addr = fca_pc_get_client_ip();
	$client_user_agent = sanitize_text_field( $_POST['client_user_agent'] );
	$event_source_url = sanitize_text_field( $_POST['event_source_url'] );
	$custom_data = empty( $_POST['custom_data'] ) ? '' : json_decode( stripslashes_deep( sanitize_text_field( $_POST['custom_data'] ) ) );
	
	$options = get_option( 'fca_pc', array() );
	$advanced_matching = empty ( $options['advanced_matching'] ) ? false : true;
		
	$user_data = (object) array(
		'external_id' => $external_id,
		'client_ip_address' => $ip_addr,
		'client_user_agent' => $client_user_agent
	);
	
	if( $fbc ) {
		$user_data->fbc = $fbc;
	} else if ( $click_id ) {
		$fbc_value = 'fb.1.' . ( 1000 * $event_time ) . '.' . $click_id;
		$user_data->fbc = $fbc_value;
		setcookie( '_fbc', $fbc_value, ( $event_time + 90 * DAY_IN_SECONDS ) );
	}
	
	if( $advanced_matching ) {
		$user_data = fca_pc_advanced_matching( true );
	}
			
	$fb_data = array(
		'action_source' => 'website',
		'event_name' => $event_name,
		'event_time'  => $event_time,
		'event_id'  =>  $event_id,
		'event_source_url'  => $event_source_url,
		'user_data' => $user_data,		
	);
		
	if( $custom_data ) {
		$fb_data['custom_data'] = $custom_data;
	}
	
	$body = (object) array(
		'data' => array( $fb_data )
	);
		
	if( $test_code ) {
		$body = (object) array(
			'data' => array( $fb_data ),
			'test_event_code' => $test_code
		);
	}
	
	$request = wp_remote_request( $url, array(
		'headers'   => array( 'Content-Type' => 'application/json' ),
		'body'      => json_encode( $body ),
		'method'    => 'POST',
		'data_format' => 'body'
	));
	
	$response = wp_remote_retrieve_body( $request );
	
}