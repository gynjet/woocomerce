<?php

////////////////////////////
// FUNCTIONS
////////////////////////////

//INSERT PIXEL
function fca_pc_parse_pixels( $options ) {
	$parsed_pixels = array();
	$options_pixels = empty( $options['pixels'] ) ? array() : $options['pixels'];
	
	foreach( $options_pixels as $p ) {
		$parsed_pixels[] = json_decode( stripslashes_deep( $p ), true );
	}
	
	return $parsed_pixels;
}

function fca_pc_get_active_pixels( $options ) {
	$parsed_pixels = fca_pc_parse_pixels( $options );
	$active_pixels = array();
	
	if ( empty( $parsed_pixels ) ) {
		return $active_pixels;
	}
		
	$post_id = get_the_ID();
	$categories = wp_get_post_categories( $post_id );
	$tags = wp_get_post_tags( $post_id );

	
	forEach ( $parsed_pixels as $pixel ) {
		$type = empty( $pixel['type'] ) ? '' : $pixel['type'];
		$paused = empty( $pixel['paused'] ) ? false : true;
		$excludes = empty( $pixel['excludes'] ) ? array() : $pixel['excludes'];
		
		if ( $paused ) {
			//skip this one
			continue;
		}

		if ( !empty( $excludes ) ) {
			$post_id_match = in_array( $post_id, $excludes );
			//CHECK CATEGORIES & TAGS
			$category_match = count( array_intersect( array_map( 'fca_pc_cat_id_fiter', $categories ), $excludes ) ) > 0;
			$tag_match = count( array_intersect( array_map( 'fca_pc_tag_id_fiter', $tags ), $excludes ) ) > 0;
			$front_page_match = is_front_page() && in_array( 'front', $excludes );
			$blog_page_match = is_home() && in_array( 'blog', $excludes );
			if ( in_array( 'all', $excludes ) OR $post_id_match OR $category_match OR $front_page_match OR $blog_page_match OR $tag_match ) {
				continue;
			}
		}
		
		$active_pixels[] = $pixel;
		

	}
	

	return $active_pixels;
}

function fca_pc_get_active_pixel_types( $options = array() ) {
	
	$parsed_pixels = fca_pc_parse_pixels( $options );
	$active_pixel_types = array();
	
	if ( empty( $parsed_pixels ) ) {
		return $active_pixel_types;
	}
		
	$post_id = get_the_ID();
	$categories = wp_get_post_categories( $post_id );
	$tags = wp_get_post_tags( $post_id );

	
	forEach ( $parsed_pixels as $pixel ) {
		$type = empty( $pixel['type'] ) ? '' : $pixel['type'];
		$paused = empty( $pixel['paused'] ) ? false : true;
		$excludes = empty( $pixel['excludes'] ) ? array() : $pixel['excludes'];
		
		if ( $paused ) {
			//skip this one
			continue;
		}

		if ( !empty( $excludes ) ) {
			$post_id_match = in_array( $post_id, $excludes );
			//CHECK CATEGORIES & TAGS
			$category_match = count( array_intersect( array_map( 'fca_pc_cat_id_fiter', $categories ), $excludes ) ) > 0;
			$tag_match = count( array_intersect( array_map( 'fca_pc_tag_id_fiter', $tags ), $excludes ) ) > 0;
			$front_page_match = is_front_page() && in_array( 'front', $excludes );
			$blog_page_match = is_home() && in_array( 'blog', $excludes );
			if ( in_array( 'all', $excludes ) OR $post_id_match OR $category_match OR $front_page_match OR $blog_page_match OR $tag_match ) {
				continue;
			}
		}
		
		$active_pixel_types[] = $type;
		

	}
	

	return array_values( array_unique( $active_pixel_types ) );
}

function fca_pc_maybe_add_pixel() {

	$options = get_option( 'fca_pc', array() );
	
	if ( fca_pc_role_check( $options ) ) {

		//HOOK IN OTHER INTEGRATIONS/FEATURES
		do_action( 'fca_pc_start_pixel_output', $options );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'fca_pc_client_js' );

		wp_enqueue_script( 'fca_pc_video_js', FCA_PC_PLUGINS_URL . '/video.js', array(), false, true );

		wp_localize_script( 'fca_pc_client_js', 'fcaPcEvents', fca_pc_get_active_events( $options ) );
		wp_localize_script( 'fca_pc_client_js', 'fcaPcPost', fca_pc_post_parameters( $options ) );
		wp_localize_script( 'fca_pc_client_js', 'fcaPcOptions', fca_pc_localize_pixel_options( $options ) );
		
		$search_integration = empty( $options['search_integration'] ) ? '' : $options['search_integration'];
		//ONLY USE DEFAULT SEARCH IF WE DIDNT USE WOO OR EDD SPECIFIC
		if ( is_search() && $search_integration == 'on' ) {
			wp_localize_script( 'fca_pc_client_js', 'fcaPcSearchQuery', array( 'search_string' => get_search_query() ) );
		}
		
		if ( !empty( $options['user_parameters'] ) ) {
			wp_localize_script( 'fca_pc_client_js', 'fcaPcUserParams', fca_pc_user_parameters() );
		}
		
		fca_pc_add_pixels( $options );
	}
}
add_action( 'wp_head', 'fca_pc_maybe_add_pixel', 1 );

function fca_pc_role_check( $options ) {
	$roles = wp_get_current_user()->roles;
	$exclude = empty ( $options['exclude'] ) ? array() : str_replace( ' ', '_', $options['exclude'] );
	$roles_check_passed = 0 === count( array_intersect( array_map( 'strtolower', $roles ), array_map( 'strtolower', $exclude ) ) );
	return $roles_check_passed;
}

function fca_pc_add_pixels( $options ) {
	
	$pixels = fca_pc_get_active_pixels( $options );
	
	$facebook_pixels = array();
	$ga3_pixels = array();
	$ga4_pixels = array();
	$adwords_pixels = array();
	$pinterest_pixels = array();
	$tiktok_pixels = array();
	$snapchat_pixels = array();
	
	forEach( $pixels as $pixel ) {
		$type = empty( $pixel['type'] ) ? '' : $pixel['type'];
		
		switch( $type ) {
			case 'GA3':
				$ga3_pixels[] = $pixel;
				break;
				
			case 'GA4':
				$ga4_pixels[] = $pixel;
				break;
				
			case 'Adwords':
				$adwords_pixels[] = $pixel;
				break;
				
			case 'Custom Header Script':
				$header_pixels[] = $pixel;
				break;
			
			case 'Pinterest':
				$pinterest_pixels[] = $pixel;
				break;
				
			case 'Snapchat':
				$snapchat_pixels[] = $pixel;
				break;	
				
			case 'TikTok':
				$tiktok_pixels[] = $pixel;
				break;
						
			default:
				$facebook_pixels[] = $pixel;
		}
		
	};
	
	if ( !empty( $ga4_pixels ) OR !empty( $ga3_pixels ) OR !empty( $adwords_pixels ) ) {
		fca_pc_add_google_pixels( array_merge( $ga3_pixels, $ga4_pixels, $adwords_pixels ), $options );
	}	
	if ( !empty( $pinterest_pixels ) ) {		
		fca_pc_add_pinterest_pixels( $pinterest_pixels, $options );
	}
	if ( !empty( $snapchat_pixels ) ) {		
		fca_pc_add_snapchat_pixels( $snapchat_pixels, $options );
	}
	if ( !empty( $facebook_pixels ) ) {		
		fca_pc_add_facebook_pixels( $facebook_pixels, $options );
	}
	if ( !empty( $tiktok_pixels ) ) {		
		fca_pc_add_tiktok_pixels( $tiktok_pixels, $options );
	}
	if ( !empty( $header_pixels ) ) {		
		fca_pc_add_header_pixels( $header_pixels, $options );
	}
	
}

function fca_pc_add_header_pixels( $header_pixels, $options ) {
	forEach ( $header_pixels as $pixel ) {			
		$code = $pixel['capi'];
		echo html_entity_decode( $code, ENT_QUOTES );
	}
}

function fca_pc_add_facebook_pixels( $facebook_pixels, $options ) {
	
	$advanced_matching = empty( $options['advanced_matching'] ) ? false : true;
	$code = ''; //INIT CODE FOR PIXEL
	
	forEach ( $facebook_pixels as $pixel ) {		
		$pixel_id = empty( $pixel['pixel'] ) ? '' : $pixel['pixel'];
		
		if( $pixel_id ){
			if ( $advanced_matching ) {
				$code .= "fbq( 'init', '$pixel_id', " . fca_pc_advanced_matching() . " );";
			} else {
				$code .= "fbq( 'init', '$pixel_id' );";
			}
		}
	}
	ob_start(); ?>
	<!-- Pixel Cat Facebook Pixel Code -->
	<script>
	!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
	n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
	t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
	document,'script','https://connect.facebook.net/en_US/fbevents.js' );
	<?php echo $code ?>
	</script>
	<!-- DO NOT MODIFY -->
	<!-- End Facebook Pixel Code -->
	<?php 
	echo ob_get_clean();
	
}

function fca_pc_add_tiktok_pixels( $tiktok_pixels, $options ) {
	$code = ''; //INIT CODE FOR PIXEL
	
	forEach ( $tiktok_pixels as $pixel ) {		
		$pixel_id = empty( $pixel['pixel'] ) ? '' : $pixel['pixel'];
		
		if( $pixel_id ){			
			$code .= "ttq.load( '$pixel_id' );";
		}
	}
	
	ob_start(); ?>
	<!-- TikTok  -->
	<script>
	!function (w, d, t) {
			  w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++
	)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};n=document.createElement("script");n.type="text/javascript",n.async=!0,n.src=i+"?sdkid="+e+"&lib="+t;e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(n,e)};
		<?= $code ?>
		ttq.page();
	}(window, document, 'ttq');
	</script>
	<!-- End TikTok -->
	<?php 
	echo ob_get_clean();
	
}

function fca_pc_add_google_pixels( $google_pixels ) {
	
	forEach ( $google_pixels as $pixel ) {		
		$pixel_id = empty( $pixel['pixel'] ) ? '' : $pixel['pixel'];
		
		if( $pixel_id ){
			
			ob_start(); ?>
			<!-- Pixel Cat  Global site tag (gtag.js) - Google Analytics -->
			<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $pixel_id ?>"></script>
			<script>
				window.dataLayer = window.dataLayer || [];
				function gtag(){dataLayer.push(arguments);}
				gtag('js', new Date());
				gtag( 'config', '<?php echo $pixel_id ?>' );
			</script>
			<?php 
			echo ob_get_clean();
			
		}
	}
		
}

function fca_pc_add_pinterest_pixels( $pinterest_pixels, $options ) {
	
	$code = ''; //INIT CODE FOR PIXEL
	
	forEach ( $pinterest_pixels as $pixel ) {		
		$pixel_id = empty( $pixel['pixel'] ) ? '' : $pixel['pixel'];
		
		if( $pixel_id ){			
			$code .= "pintrk( 'load', '$pixel_id' );";			
		}
	}
	ob_start(); ?>
	<!-- Pixel Cat Pinterest Tag -->
	<script>
	!function(e){if(!window.pintrk){window.pintrk = function () {
	window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
	  n=window.pintrk;n.queue=[],n.version="3.0";var
	  t=document.createElement("script");t.async=!0,t.src=e;var
	  r=document.getElementsByTagName("script")[0];
	  r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");
	<?php echo $code ?>
	pintrk('page');
	</script>
	<noscript>
	<img height="1" width="1" style="display:none;" alt=""
	  src="https://ct.pinterest.com/v3/?event=init&tid=2613944587473&pd[em]=<hashed_email_address>&noscript=1" />
	</noscript>
	<!-- end Pinterest Tag -->
	<?php 
	echo ob_get_clean();
	
}

function fca_pc_add_snapchat_pixels( $snapchat_pixels, $options ) {
	
	$code = ''; //INIT CODE FOR PIXEL
	
	forEach ( $snapchat_pixels as $pixel ) {		
		$pixel_id = empty( $pixel['pixel'] ) ? '' : $pixel['pixel'];
		
		if( $pixel_id ){			
			$code .= "snaptr('init', '$pixel_id' );";
		}
	}
	
	ob_start(); ?>
<!-- Pixel Cat Snap Pixel Code -->
<script type='text/javascript'>
(function(e,t,n){if(e.snaptr)return;var a=e.snaptr=function()
{a.handleRequest?a.handleRequest.apply(a,arguments):a.queue.push(arguments)};
a.queue=[];var s='script';r=t.createElement(s);r.async=!0;
r.src=n;var u=t.getElementsByTagName(s)[0];
u.parentNode.insertBefore(r,u);})(window,document,
'https://sc-static.net/scevent.min.js');
<?php echo $code ?>
snaptr( 'track', 'PAGE_VIEW' );
</script>
<!-- End Snap Pixel Code -->
	<?php 
	echo ob_get_clean();
	
}

function fca_pc_post_parameters( $options ) {
	global $post;

	$post_id = empty ( $post->ID ) ? 0 : $post->ID;
	$edd_active = fca_pc_is_edd_active();
	$edd_currency = $edd_active ? edd_get_option( 'currency', 'USD' ) : 'USD';
	
	return array(
		'title' => empty ( $post->post_title ) ? '' : $post->post_title,
		'type' => empty ( $post->post_type ) ? '' : $post->post_type,
		'id' => $post_id,
		'categories' => fca_pc_get_category_names( $post_id ),
	);
}

function fca_pc_localize_pixel_options( $options ) {
	global $post;

	$edd_active = fca_pc_is_edd_active();
	$edd_currency = $edd_active ? edd_get_option( 'currency', 'USD' ) : 'USD';
	
	return array(
		'pixel_types' => fca_pc_get_active_pixel_types( $options ),
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'debug' => FCA_PC_DEBUG,
		'edd_currency' => $edd_currency,		
		'nonce' => wp_create_nonce( 'fca_pc_capi_nonce' ),
		'utm_support' => empty( $options['utm_support'] ) ? false : true,
		'user_parameters' => empty( $options['user_parameters'] ) ? false : true,
		'edd_enabled' => fca_pc_edd_auto_events_enabled( $options ),
		'edd_delay' => empty( $options['edd_delay'] ) ? 0 : intVal($options['edd_delay']),
		'woo_enabled' => fca_pc_woo_auto_events_enabled( $options ),
		'woo_delay' => empty( $options['woo_delay'] ) ? 0 : intVal($options['woo_delay']),
		'woo_order_cookie' => empty( $options['woo_order_cookie'] ) ? false : true,
		'video_enabled' => empty( $options['video_events'] ) ? false : true,
	);
}

function fca_pc_edd_auto_events_enabled( $options ) {
	return ( !empty( $options['edd_integration'] )OR !empty( $options['edd_integration_ga'] ) OR !empty( $options['edd_integration_pinterest'] ) OR !empty( $options['edd_integration_snapchat'] ) OR !empty( $options['edd_integration_tiktok'] ) );
}

function fca_pc_woo_auto_events_enabled( $options ) {
	return ( !empty( $options['woo_integration'] )OR !empty( $options['woo_integration_ga'] ) OR !empty( $options['woo_integration_pinterest'] ) OR !empty( $options['woo_integration_snapchat'] ) OR !empty( $options['woo_integration_tiktok'] ) );
}

function fca_pc_get_active_events( $options ) {

	$id = get_the_id();
	$events = empty( $options['events'] ) ? array() : stripslashes_deep( $options['events'] );

	$categories = wp_get_post_categories( $id );
	$tags = wp_get_post_tags( $id );

	$active_events = array();
	if ( !empty ( $events ) ) {
		forEach ( $events as $event ) {
			$event = json_decode( $event );

			if ( !empty( $event->paused ) ) {
				//skip this one
				continue;
			}

			if ( is_array( $event->trigger ) ) {
				$post_id_match = in_array( $id, $event->trigger );
				//CHECK CATEGORIES & TAGS
				$category_match = count( array_intersect( array_map( 'fca_pc_cat_id_fiter', $categories ), $event->trigger ) ) > 0;
				$tag_match = count( array_intersect( array_map( 'fca_pc_tag_id_fiter', $tags ), $event->trigger ) ) > 0;
				$front_page_match = is_front_page() && in_array( 'front', $event->trigger );
				$blog_page_match = is_home() && in_array( 'blog', $event->trigger );
				if ( in_array( 'all', $event->trigger ) OR $post_id_match OR $category_match OR $front_page_match OR $blog_page_match OR $tag_match ) {
					$active_events[] = $event;
				}
			} else {
				//CSS TRIGGERS
				$active_events[] = $event;
			}

		}
	}

	return $active_events;

}

function fca_pc_advanced_matching( $hashed = false ) {
	
	$ip_addr = fca_pc_get_client_ip();
	$fbc = empty( $_COOKIE['_fbc'] ) ? '' : sanitize_text_field( $_COOKIE['_fbc'] );
	$client_user_agent = empty( $_POST['client_user_agent'] ) ? '' : sanitize_text_field( $_POST['client_user_agent'] );
	$external_id = empty( $_POST['external_id'] ) ? '' : sanitize_text_field( $_POST['external_id'] );
			
	if ( !empty( $_COOKIE['fca_pc_advanced_matching'] ) ) {
		return stripslashes_deep( $_COOKIE['fca_pc_advanced_matching'] );
	} else if ( is_user_logged_in() ) {

		$user = wp_get_current_user();

		$fn = empty( $user->first_name ) ? $user->billing_first_name : $user->first_name;
		$ln = empty( $user->last_name ) ? $user->billing_last_name : $user->last_name;
		$user_data = array (
			'em' => strtolower( $user->user_email ),
			'fn' => strtolower( $fn ),
			'ln' => strtolower( $ln ),
			'ph' => strtolower( $user->billing_phone ),
			'ct' => strtolower( $user->billing_city ),
			'st' => strtolower( $user->billing_state ),
			'zp' => strtolower( $user->billing_postcode ),
			'country' => strtolower( $user->billing_country ),
			'external_id' => $external_id,
			'client_ip_address' => $ip_addr,
			'client_user_agent' => $client_user_agent,
			'fbc' => $fbc
		);
				
		if( $hashed ) {
			return array (
				'em' => fca_pc_maybe_hash( $user_data['em'] ),
				'fn' => fca_pc_maybe_hash( $user_data['fn'] ),
				'ln' => fca_pc_maybe_hash( $user_data['ln'] ),
				'ph' => fca_pc_maybe_hash( $user_data['ph'] ),
				'ct' => fca_pc_maybe_hash( $user_data['ct'] ),
				'st' => fca_pc_maybe_hash( $user_data['st'] ),
				'zp' => fca_pc_maybe_hash( $user_data['zp'] ),
				'country' => fca_pc_maybe_hash( $user_data['country'] ),
				'external_id' => $external_id,
				'client_ip_address' => $ip_addr,
				'client_user_agent' => $client_user_agent,				
				'fbc' => $fbc
			);
		}
		return json_encode( array_filter( $user_data ) );

	} else if ( function_exists( 'is_order_received_page' ) && is_order_received_page() ) {

		global $wp;
		$order_id = isset( $wp->query_vars['order-received'] ) ? intval( $wp->query_vars['order-received'] ) : 0;
		$order = new WC_Order( $order_id );

		$user_data = array (
			'em' => strtolower( $order->get_billing_email() ),
			'fn' => strtolower( $order->get_billing_first_name() ),
			'ln' => strtolower( $order->get_billing_last_name() ),
			'ct' => strtolower( $order->get_billing_city() ),
			'st' => strtolower( $order->get_billing_state() ),
			'zp' => strtolower( $order->get_billing_postcode() ),
			'country' => strtolower( $order->get_billing_country() ),
			'external_id' => $external_id,
			'client_ip_address' => $ip_addr,
			'client_user_agent' => $client_user_agent,
			'fbc' => $fbc
		);
				
		if( $hashed ) {
			return array (
				'em' => fca_pc_maybe_hash( $user_data['em'] ),
				'fn' => fca_pc_maybe_hash( $user_data['fn'] ),
				'ln' => fca_pc_maybe_hash( $user_data['ln'] ),
				'ph' => fca_pc_maybe_hash( $user_data['ph'] ),
				'ct' => fca_pc_maybe_hash( $user_data['ct'] ),
				'st' => fca_pc_maybe_hash( $user_data['st'] ),
				'zp' => fca_pc_maybe_hash( $user_data['zp'] ),
				'country' => fca_pc_maybe_hash( $user_data['country'] ),
				'external_id' => $external_id,
				'client_ip_address' => $ip_addr,
				'client_user_agent' => $client_user_agent,
				'fbc' => $fbc
			);
		}
		
		return json_encode( array_filter( $user_data ) );

	}

	return false;
}

function fca_pc_maybe_hash( $string ) {
	return empty( $string ) ? '' : hash( 'sha256', $string );
}

function fca_pc_encode_xml( $string ) {
	return htmlspecialchars( strip_tags ( $string ) );
}

function fca_pc_user_parameters( $id = '' ) {

	if ( !$id ) {
		$id = get_the_id();
	}

	$lang = empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? 'en-US' : $_SERVER['HTTP_ACCEPT_LANGUAGE'] ;

	return array(
		'referrer' => wp_get_raw_referer(),
		'language' => sanitize_text_field( $lang ),
		'logged_in' => is_user_logged_in() ? 'true' : 'false',
		'post_tag' => implode( ', ', array_map( 'fca_pc_tag_name_fiter', wp_get_post_tags( $id ) ) ),
		'post_category' => implode( ', ', fca_pc_get_category_names( $id ) ),
	);
}

function fca_pc_get_category_names( $id = '' ){

	if ( !$id ) {
		$id = get_the_id();
	}

	$category_names = array();
	$categories = wp_get_post_categories( $id );
	if ( is_array( $categories ) ) {
		forEach ( $categories as $cat_id ) {
			$category_names[] = get_cat_name( $cat_id );
		}
	}
	return $category_names;
}

function fca_pc_woo_product_cat_and_tags() {

	$return = array();

	$tags = get_terms( 'product_tag' );
	if ( !is_array( $tags ) ) {
		$tags = array();
	}
	$cats = get_terms( 'product_cat' );
	if ( !is_array( $cats ) ) {
		$cats = array();
	}
	forEach ( array_merge( $cats, $tags ) as $obj ) {
		$return[$obj->term_id] = $obj->name;
	}
	return $return;
}

function fca_pc_edd_product_cat_and_tags() {

	$return = array();

	$tags = get_terms( 'download_tag' );
	if ( !is_array( $tags ) ) {
		$tags = array();
	}
	$cats = get_terms( 'download_category' );
	if ( !is_array( $cats ) ) {
		$cats = array();
	}
	forEach ( array_merge( $cats, $tags ) as $obj ) {
		$return[$obj->term_id] = $obj->name;
	}
	return $return;
}

//SINGLE-SELECT
function fca_pc_select( $name, $selected = '', $options = array(), $atts = '' ) {

	$name = esc_attr( $name );
	$html = "<select name='fca_pc[$name]' $atts style='width:100%' >";

		if ( empty( $options ) && !empty( $selected ) ) {
			$selected = esc_attr( $selected );
			$html .= "<option selected='selected' value='$selected'>" . esc_attr__('Loading...', 'facebook-conversion-pixel') . "</option>";
		} else {
			forEach ( $options as $key => $text ) {
				$sel = $key === $selected ? 'selected="selected"' : '';
				$key = esc_attr( $key );
				$text = esc_attr( $text );
				$html .= "<option $sel value='$key'>$text</option>";
			}
		}

	$html .= '</select>';
	
	return $html;

}

//MULTI-SELECT
function fca_pc_select_multiple( $name, $selected = array(), $options = array(), $atts = '' ) {
	
	$name = esc_attr( $name );
	
	$html = "<select name='fca_pc[$name][]' $atts class='fca_pc_multiselect' multiple style='width:100%'  >";
	
		forEach ( $options as $key => $text ) {

			$sel = '';
			if ( in_array( $key, $selected ) ) {

				$sel = 'selected="selected"';

			}

			$key = esc_attr( $key );
			$text = esc_attr( $text );
			$html .= "<option $sel value='$key'>$text</option>";

		}
		
	$html .= '</select>';
	
	return $html;

}

//RETURN GENERIC INPUT HTML
function fca_pc_input ( $name, $placeholder = '', $value = '', $type = 'text', $atts = '' ) {

	$name = esc_attr( $name );
	$placeholder = esc_attr( $placeholder );

	$html = "<div class='fca-pc-field fca-pc-field-$type'>";

		switch ( $type ) {

			case 'checkbox':
				$checked = !empty( $value ) ? "checked='checked'" : '';

				$html .= "<div class='onoffswitch'>";
					$html .= "<input $atts style='display:none;' type='checkbox' id='fca_pc[$name]' class='onoffswitch-checkbox fca-pc-input-$type fca-pc-$name' name='fca_pc[$name]' $checked>";
					$html .= "<label class='onoffswitch-label' for='fca_pc[$name]'><span class='onoffswitch-inner' data-content-on='ON' data-content-off='OFF'><span class='onoffswitch-switch'></span></span></label>";
				$html .= "</div>";
				break;
			case 'textarea':
				$value = esc_textarea( $value );
				$html .= "<textarea $atts placeholder='$placeholder' class='fca-pc-input-$type fca-pc-$name' name='fca_pc[$name]'>$value</textarea>";
				break;
			default:
				$value = esc_attr( $value );
				$html .= "<input $atts type='$type' placeholder='$placeholder' class='fca-pc-input-$type fca-pc-$name' name='fca_pc[$name]' value='$value'>";
		}

	$html .= '</div>';

	return $html;
}

function fca_pc_sanitize_text_array( $array ) {
	if ( !is_array( $array ) ) {
		return sanitize_text_field ( $array );
	}
	foreach ( $array as $key => &$value ) {
		if ( is_array( $value ) ) {
			$value = fca_sp_sanitize_text_array( $value );
		} else {
			$value = sanitize_text_field( $value );
		}
	}

	return $array;
}


function fca_pc_get_client_ip(){

	$ip_addr = null;
	if( $_SERVER['REMOTE_ADDR'] ){
		$ip_addr = $_SERVER['REMOTE_ADDR'];
	} else if ( $_SERVER['HTTP_X_FORWARDED_FOR'] ){
		$ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else if ( $_SERVER['HTTP_CLIENT_IP'] ){
		$ip_addr = $_SERVER['HTTP_CLIENT_IP'];
	}

	if( $ip_addr ){
		// check in case multiple addresses were returned
		return explode( ',', $ip_addr )[0];
	} else {
		return null;
	}

}

function fca_pc_delete_icons() {
	ob_start(); ?>
		<span class='dashicons dashicons-trash fca_delete_icon fca_delete_button' title='<?php esc_attr_e( 'Delete', 'facebook-conversion-pixel' ) ?>'></span>
		<span class='dashicons dashicons-yes fca_delete_icon fca_delete_icon_confirm' title='<?php esc_attr_e( 'Confirm Delete', 'facebook-conversion-pixel' ) ?>' style='display:none;'></span>
		<span class='dashicons dashicons-no fca_delete_icon fca_delete_icon_cancel' title='<?php esc_attr_e( 'Cancel', 'facebook-conversion-pixel' ) ?>' style='display:none;'></span>
	<?php
	return ob_get_clean();
}

function fca_pc_tooltip( $text = 'Tooltip', $icon = 'dashicons dashicons-editor-help' ) {
	return "<span class='$icon fca_pc_tooltip' title='" . htmlentities( $text ) . "'></span>";
}

function fca_pc_clean_pixel_id( $value ) {
	
	return preg_replace("/[^0-9]/", '', $value);
}

//HELPER FILTERS
function fca_pc_cat_id_fiter ( $cat_id ) {
	return 'cat' . $cat_id;
}
function fca_pc_tag_id_fiter ( $tag_id ) {
	return 'tag' . $tag_id->term_id;
}
function fca_pc_tag_name_fiter ( $tag ) {
	return $tag->name;
}
function fca_pc_term_id_fiter ( $obj ) {
	return $obj->term_id;
}



function fca_pc_get_woo_product ( $product_id = '' ) {
	$p = empty( $product_id ) ? wc_get_product() : wc_get_product( $product_id );
	if ( $p ) {
		return $p;
	}
	return false;
}

function fca_pc_get_woo_ltv( $email ) {
	
	$ltv = 0;
	
	$args = array(
		'post_type'      => 'shop_order',
		'post_status'    => 'wc-completed',
		'meta_key'       => '_billing_email',
		'meta_value'     => $email,
		'posts_per_page' => -1,
	);

	$orders_query = new WP_Query( $args );

	foreach( $orders_query->posts as $order ) {
		$WC_Order = new WC_Order( $order );
		$ltv += $WC_Order->get_total();
	}

	wp_reset_query();
	
	return $ltv;
	
}

function fca_pc_get_post_triggers() {
	

	$triggers = array(
		'all' => esc_attr__( 'All Pages', 'facebook-conversion-pixel' ),
		'front' => esc_attr__( 'Front Page', 'facebook-conversion-pixel' ),
		'blog' => esc_attr__( 'Blog Page', 'facebook-conversion-pixel' )
	);
	
	$options = get_option( 'fca_pc', array() );
	
	$cpt_support = empty ( $options['cpt_support'] ) ? array() : $options['cpt_support'];
	
	$custom_post_type_triggers = apply_filters( 'fca_pc_custom_post_support', $cpt_support );

	if ( is_array( $custom_post_type_triggers ) && count( $custom_post_type_triggers ) > 0 ) {
		forEach ( $custom_post_type_triggers as $cpt_slug ) {
			$cpt_obj = get_post_type_object( $cpt_slug );
			
			if ( $cpt_obj ) {
				$cpt_name = $cpt_obj->labels->singular_name;

				forEach ( get_posts( array( 'posts_per_page' => -1, 'post_type' => $cpt_slug ) ) as $p ) {
					$triggers[$p->ID] = $cpt_name . ' ' . $p->ID . ' - ' . $p->post_title;
				}
			}
		}
	}

	forEach ( get_posts( array( 'posts_per_page' => -1, 'post_type' => 'product' ) ) as $product ) {
		$triggers[$product->ID] = 'Product ' . $product->ID . ' - ' . $product->post_title;
	}

	forEach ( get_posts( array( 'posts_per_page' => -1, 'post_type' => 'download' ) ) as $download ) {
		$triggers[$download->ID] = 'Download ' . $download->ID . ' - ' . $download->post_title;
	}

	forEach ( get_pages( array( 'posts_per_page' => -1 ) ) as $page ) {
		$triggers[$page->ID] = 'Page ' . $page->ID . ' - ' . $page->post_title;
	}
	forEach ( get_posts( array( 'posts_per_page' => -1 ) ) as $post ) {
		$triggers[$post->ID] = 'Post ' . $post->ID . ' - ' . $post->post_title;
	}

	forEach ( get_categories() as $cat ) {
		$triggers['cat' . $cat->cat_ID] = 'Category ' . $cat->cat_ID . ' - ' . $cat->category_nicename;
	}

	forEach ( get_tags() as $tag ) {
		$triggers['tag' . $tag->term_id] = 'Tag ' . $tag->term_id  . ' - ' . $tag->name;
	}

	//REMOVE BLOG PAGE FROM OPTIONS - USE BLOG SETTING INSTEAD
	$blog_id = get_option( 'page_for_posts' );
	if ( $blog_id !== 0 ) {
		unset ( $triggers[$blog_id] );
	}
	
	return $triggers;
}

function fca_pc_admin_header_nav(){
		
	ob_start();?>
<div class='fca_pc_admin_header' >
	<img height=60 width=60 style='float:left;margin-right:16px;' src='<?php echo FCA_PC_PLUGINS_URL . '/assets/fatcatapps-logo.png' ?>'>
	<h1>Pixel Cat</h1>
	<?php if ( FCA_PC_PLUGIN_PACKAGE === 'Lite' ) { ?>
	<a target='_blank' style='color:white;' class='highlighted' href='https://fatcatapps.com/pixelcat/premium/?utm_medium=plugin&utm_source=Pixel%20Cat%20Free&utm_campaign=free-plugin'><span class="dashicons dashicons-dashboard"></span><?php esc_attr_e( 'Upgrade to Premium', 'facebook-conversion-pixel' ) ?></a>	
	<?php } ?>
</div>	
<?php 
	echo ob_get_clean();

}

function fca_pc_is_edd_active() {	
	return ( fca_pc_is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ) OR fca_pc_is_plugin_active( 'easy-digital-downloads-pro/easy-digital-downloads.php' ) );
}

function fca_pc_is_plugin_active( $plugin ) {
	return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
}