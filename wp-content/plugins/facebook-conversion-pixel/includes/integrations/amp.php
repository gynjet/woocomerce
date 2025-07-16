<?php 
		
function fca_pc_amp_integration() {
	
	$options = get_option( 'fca_pc', array() );
	$pixels = empty( $options['pixels'] ) ? fca_pc_check_old_pixel( $options ) : $options['pixels'];

	forEach ( $pixels as $pixel ) {
		$pixel = json_decode( stripslashes_deep( $pixel ), TRUE );
		$pixel_id = fca_pc_clean_pixel_id( $pixel['pixel'] );
		echo "<amp-pixel src='https://www.facebook.com/tr?id=$pixel_id&ev=PageView&noscript=1' layout='nodisplay'></amp-pixel>";
	}
	
}
//AUTOMATTIC GOOGLE AMP PLUGIN
add_action( 'amp_post_template_footer', 'fca_pc_amp_integration' );

//AMP for WP 
add_action( 'amp_end', 'fca_pc_amp_integration' );