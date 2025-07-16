<?php
 
function fca_pc_edd_event_view_product_ga() {
	global $post;

	if ( $post && $post->post_type === 'download' ) {
		$download = new EDD_Download( $post->ID );
		
		$items = array(
			'item_id' => $post->ID,
			'item_name' => esc_html( strip_tags( get_the_title(  $post->ID ) ) ),
		);
		
		$ga_data = array(
			'value' => $download->get_price(),
			'currency' => edd_get_option( 'currency', 'USD' ),
			'items' => $items,
		);
		
		wp_localize_script( 'fca_pc_client_js', 'fcaPcEddProductGA', $ga_data );
	}
}
add_action( 'fca_pc_start_pixel_output', 'fca_pc_edd_event_view_product_ga' );

function fca_pc_edd_event_initiate_checkout_ga() {
	if ( function_exists('edd_is_checkout') && edd_is_checkout() ) {
		wp_localize_script( 'fca_pc_client_js', 'fcaPcEddCheckoutCartGA', fca_pc_edd_format_cart_data_ga() );
	}
}
add_action( 'fca_pc_start_pixel_output', 'fca_pc_edd_event_initiate_checkout_ga' );


function fca_pc_edd_purchase_ga( $payment_id ) {
	$options = get_option( 'fca_pc', array() );
	$edd_extra_params = empty( $options['edd_extra_params'] ) ? false : true;
	
	$cart = fca_pc_edd_format_cart_data_ga( $payment_id, $edd_extra_params );
	setcookie( 'fca_pc_edd_purchase_ga', json_encode( $cart ), 0, '/' );
	
}
add_action( 'edd_complete_purchase', 'fca_pc_edd_purchase_ga', 9999, 1 );

function fca_pc_edd_format_cart_data_ga( $payment_id = false, $extra_params = false ) {
	if ( $payment_id ) {
		$cart_contents = edd_get_payment_meta_cart_details( $payment_id );
	} else {
		$cart_contents = edd_get_cart_contents();
	}
	$value = 0;
	$items = array();
		
	forEach ( $cart_contents as $item ) {
		$download = new EDD_Download( $item['id'] );
		
		if ( !empty( $item['price'] ) ) {
			$value = $value + $item['price'];
		} else if ( $download->has_variable_prices() ) {
			$price_id = $item['options']['price_id'];
			$value = $value + $download->get_prices()[$price_id]['amount'];
		} else {
			$value = $value + $download->get_price();
		}
		
		$i = array(
			'item_id' => $item['id'],
			'item_name' => esc_html( strip_tags( get_the_title( $item['id'] ) ) ),
			'price' => $download->get_price(),
		);
		
		$category = get_the_terms( $item['id'], 'download_category' );
		
		if ( $category ) {
			$max_product_categories = 5;
			$n = 1;
			forEach ( $category as $term  ) {
				
				if( $n === 1 ) {
					$i['item_category'] = $term->name;
				} else if ( $n <= $max_product_categories ) {
					$keyname = "item_category$n"; //e.g. item_category2 
					$i[$keyname] = $term->name;
				} else {
					//ONLY SUPPORTS 5 CATEGORIES CURRENTLY
					//https://developers.google.com/analytics/devguides/collection/ga4/reference/events#begin_checkout
				}
				
			}
		}	
		$items[] = $i;
	}
	
	$ga_data = array(
		'value' => $value,
		'currency' => edd_get_option( 'currency', 'USD' ),
		'items' => $items,
		'transaction_id' => $payment_id,
	);
	
	if ( $extra_params ) {
		$payment = new EDD_Payment( $payment_id );
		$customer = new EDD_Customer( get_current_user_id(), true );

		$ga_data['gateway'] = $payment->gateway;
		$address = $payment->address;
		
		$address_keys = array(
			'city' => 'billing_city',
			'state' => 'billing_state',
		);
		forEach ( $address_keys as $key => $value ) {
			if ( $address[$key] ) {
				$ga_data[$value] = $address[$key];	
			}
		}
				
		$ga_data['lifetime_value'] = round( $customer->purchase_value, 2);	
		
		if ( edd_get_cart_discounts() ) {
			$ga_data['coupon'] = edd_get_cart_discounts();
		}
	}
	
	return $ga_data;
}
