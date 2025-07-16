<?php
/**
 * Yoast SEO: Local plugin file.
 *
 * Contains all the deprecated functions.
 *
 * @package WPSEO_Local/Deprecated
 */

/**
 * Generates output for formatted address.
 *
 * @deprecated 3.3.1. Use WPSEO_Local_Address_Format->get_address_format() instead.
 *
 * @param string $business_address The address of the business.
 * @param bool   $oneline          Whether to show the address on one line or not.
 * @param string $business_zipcode The business zipcode.
 * @param string $business_city    The business city.
 * @param string $business_state   The business state.
 * @param bool   $show_state       Whether to show the state or not.
 * @param bool   $escape_output    Whether to escape the output or not.
 * @param bool   $use_tags         Whether to use HTML tags in the outpput or not.
 *
 * @return string
 */
function wpseo_local_get_address_format( $business_address = '', $oneline = false, $business_zipcode = '', $business_city = '', $business_state = '', $show_state = false, $escape_output = false, $use_tags = true ) {
	_deprecated_function( 'wpseo_local_get_address_format', '3.3.1', 'WPSEO_Local_Address_Format->get_address_format()' );

	$options        = get_option( 'wpseo_local' );
	$address_format = 'address-state-postal';
	if ( ! empty( $options['address_format'] ) ) {
		$address_format = $options['address_format'];
	}

	$address_details = [
		'business_address' => $business_address,
		'oneline'          => $oneline,
		'business_zipcode' => $business_zipcode,
		'business_city'    => $business_city,
		'business_state'   => $business_state,
		'show_state'       => $show_state,
		'escape_output'    => $escape_output,
		'use_tags'         => $use_tags,
	];

	$format = new WPSEO_Local_Address_Format();
	$output = $format->get_address_format( $address_format, $address_details );

	return trim( $output );
}


/**
 * Get the custom marker from categories or general Local SEO settings.
 *
 * @deprecated 4.7 Use WPSEO_Local_Locations_Repository->get_custom_marker() instead.
 *
 * @param int|null $post_id The post id.
 *
 * @return false|string
 */
function wpseo_local_get_custom_marker( $post_id = null ) {
	_deprecated_function( 'wpseo_local_get_custom_marker', '4.5', 'WPSEO_Local_Locations_Repository->get_custom_marker()' );

	$repo = new WPSEO_Local_Locations_Repository();

	return $repo->cb_postmeta_custom_marker( $post_id );
}

/**
 * Get the location details
 *
 * @deprecated 4.7 Use WPSEO_Local_Locations_Repository->get() instead.
 *
 * @param string $location_id Optional. Only use this when multiple locations are enabled in the website.
 *
 * @return array|bool Array with location details.
 */
function wpseo_get_location_details( $location_id = '' ) {
	_deprecated_function( 'wpseo_get_location_details', '4.5', 'WPSEO_Local_Locations_Repository->get()' );
	$options          = get_option( 'wpseo_local' );
	$location_details = [];

	if ( wpseo_has_multiple_locations() && $location_id == '' ) {
		return false;
	}
	elseif ( wpseo_has_multiple_locations() ) {
		if ( $location_id == null ) {
			return false;
		}

		$location_details = [
			'business_address'     => get_post_meta( $location_id, '_wpseo_business_address', true ),
			'business_city'        => get_post_meta( $location_id, '_wpseo_business_city', true ),
			'business_state'       => get_post_meta( $location_id, '_wpseo_business_state', true ),
			'business_zipcode'     => get_post_meta( $location_id, '_wpseo_business_zipcode', true ),
			'business_country'     => get_post_meta( $location_id, '_wpseo_business_country', true ),
			'business_phone'       => get_post_meta( $location_id, '_wpseo_business_phone', true ),
			'business_phone_2nd'   => get_post_meta( $location_id, '_wpseo_business_phone_2nd', true ),
			'business_coords_lat'  => get_post_meta( $location_id, '_wpseo_coordinates_lat', true ),
			'business_coords_long' => get_post_meta( $location_id, '_wpseo_coordinates_long', true ),
		];
	}
	elseif ( wpseo_has_multiple_locations() ) {
		$location_details = [
			'business_address'     => $options['location_address'],
			'business_city'        => $options['location_city'],
			'business_state'       => $options['location_state'],
			'business_zipcode'     => $options['location_zipcode'],
			'business_country'     => $options['location_country'],
			'business_phone'       => $options['location_phone'],
			'business_phone_2nd'   => isset( $options['location_phone_2nd'] ) ? $options['location_phone_2nd'] : '',
			'business_coords_lat'  => $options['location_coords_lat'],
			'business_coords_long' => $options['location_coords_long'],
		];
	}

	return $location_details;
}


/**
 * Returns the API Browser key
 *
 * @deprecated 11.9 Use WPSEO_Local_Api_Keys_Repository->get_api_key() instead.
 *
 * @return string API key.
 */
function yoast_wpseo_local_get_api_key_browser() {
	_deprecated_function( 'yoast_wpseo_local_get_api_key_browser', '11.9', 'WPSEO_Local_Api_Keys_Repository->get_api_key()' );
	$api_repository = new WPSEO_Local_Api_Keys_Repository();

	return $api_repository->get_api_key( 'browser' );
}

/**
 * Returns the API Server key
 *
 * @deprecated 11.9 Use WPSEO_Local_Api_Keys_Repository->get_api_key() instead.
 *
 * @return string API key.
 */
function yoast_wpseo_local_get_api_key_server() {
	_deprecated_function( 'yoast_wpseo_local_get_api_key_server', '11.9', 'WPSEO_Local_Api_Keys_Repository->get_api_key()' );
	$api_repository = new WPSEO_Local_Api_Keys_Repository();

	return $api_repository->get_api_key( 'server' );
}

/**
 * Geocode the given address.
 *
 * @param string $address The address that needs to be geocoded.
 *
 * @return array|WP_Error
 */
function wpseo_geocode_address( $address ) {
	_deprecated_function( 'wpseo_geocode_address', '12.1' );
	$geocode_url    = 'https://maps.google.com/maps/api/geocode/json?address=' . urlencode( $address ) . '&oe=utf8&sensor=false';
	$api_repository = new WPSEO_Local_Api_Keys_Repository();

	$api_key = $api_repository->get_api_key( 'browser' );
	if ( ! empty( $api_key ) ) {
		$geocode_url .= '&key=' . $api_key;
	}

	$response = wp_remote_get( $geocode_url );

	if ( is_wp_error( $response ) || $response['response']['code'] != 200 || empty( $response['body'] ) ) {
		return new WP_Error( 'wpseo-no-response', "Didn't receive a response from Maps API" );
	}

	$response_body = json_decode( $response['body'] );

	if ( $response_body->status !== 'OK' ) {
		$error_code = 'wpseo-zero-results';
		if ( $response_body->status === 'OVER_QUERY_LIMIT' ) {
			$error_code = 'wpseo-query-limit';
		}

		return new WP_Error( $error_code, $response_body->status );
	}

	return $response_body;
}
