<?php
/*
	Plugin Name: Pixel Cat Lite
	Plugin URI: https://fatcatapps.com/pixel-cat
	Description: Provides an easy way to embed Facebook pixels
	Text Domain: facebook-conversion-pixel
	Domain Path: /languages
	Author: Fatcat Apps
	Author URI: https://fatcatapps.com/
	License: GPLv2
	Version: 3.1.1
*/


// BASIC SECURITY
defined( 'ABSPATH' ) or die( 'Unauthorized Access!' );



if ( !defined( 'FCA_PC_PLUGIN_DIR' ) ) {

	//DEFINE SOME USEFUL CONSTANTS
	define( 'FCA_PC_DEBUG', FALSE );
	define( 'FCA_PC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	define( 'FCA_PC_PLUGINS_URL', plugins_url( '', __FILE__ ) );
	define( 'FCA_PC_PLUGINS_BASENAME', plugin_basename(__FILE__) );
	define( 'FCA_PC_PLUGIN_FILE', __FILE__ );
	define( 'FCA_PC_PLUGIN_PACKAGE', 'Lite' ); //DONT CHANGE THIS - BREAKS AUTO UPDATER
	define( 'FCA_PC_PLUGIN_NAME', 'Pixel Cat Premium: ' . FCA_PC_PLUGIN_PACKAGE );

	if ( FCA_PC_DEBUG ) {
		define( 'FCA_PC_PLUGIN_VER', '3.1.' . time() );
	} else {
		define( 'FCA_PC_PLUGIN_VER', '3.1.1' );
	}

	//LOAD CORE
	include_once( FCA_PC_PLUGIN_DIR . '/includes/functions.php' );
	include_once( FCA_PC_PLUGIN_DIR . '/includes/api.php' );

	$options = get_option( 'fca_pc', array() );

	//LOAD OPTIONAL MODULES
	if ( !empty( $options['woo_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events.php' );
	}
	if ( !empty( $options['woo_integration_ga'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events-ga.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events-ga.php' );
	}	
	if ( !empty( $options['woo_integration_pinterest'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events-pinterest.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events-pinterest.php' );
	}
	if ( !empty( $options['woo_integration_snapchat'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events-snapchat.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events-snapchat.php' );
	}
	if ( !empty( $options['woo_integration_tiktok'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events-tiktok.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-events-tiktok.php' );
	}
	if ( !empty( $options['woo_feed'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-feed.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/woo-feed.php' );
	}
	if ( !empty( $options['edd_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events.php' );
	}
	if ( !empty( $options['edd_integration_ga'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events-ga.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events-ga.php' );
	}
	if ( !empty( $options['edd_integration_pinterest'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events-pinterest.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events-pinterest.php' );
	}
	if ( !empty( $options['edd_integration_snapchat'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events-snapchat.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events-snapchat.php' );
	}
	if ( !empty( $options['edd_integration_tiktok'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events-tiktok.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-events-tiktok.php' );
	}
	if ( !empty( $options['edd_feed'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-feed.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/edd-feed.php' );
	}
	if ( !empty( $options['quizcat_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/quizcat.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/quizcat.php' );
	}
	if ( !empty( $options['optincat_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/optincat.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/optincat.php' );
	}
	if ( !empty( $options['landingpagecat_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/landingpagecat.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/landingpagecat.php' );
	}
	if ( !empty( $options['ept_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/ept.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/ept.php' );
	}
	if ( !empty( $options['amp_integration'] ) && file_exists ( FCA_PC_PLUGIN_DIR . '/includes/integrations/amp.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/integrations/amp.php' );
	}

	//LOAD MODULES
	include_once( FCA_PC_PLUGIN_DIR . '/includes/editor/editor.php' );
	if ( file_exists ( FCA_PC_PLUGIN_DIR . '/includes/editor/editor-premium.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/editor/editor-premium.php' );
	}

	if ( file_exists ( FCA_PC_PLUGIN_DIR . '/includes/licensing/licensing.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/licensing/licensing.php' );
	}

	if( file_exists( FCA_PC_PLUGIN_DIR . '/includes/notices/notices.php' ) ) {
		include_once( FCA_PC_PLUGIN_DIR . '/includes/notices/notices.php' );
	}
		
	function fca_pc_register_scripts() {
		if ( FCA_PC_DEBUG ) {
			wp_register_script( 'fca_pc_client_js', FCA_PC_PLUGINS_URL . '/pixel-cat.js', array( 'jquery' ), FCA_PC_PLUGIN_VER, true );
		} else {
			wp_register_script( 'fca_pc_client_js', FCA_PC_PLUGINS_URL . '/pixel-cat.min.js', array( 'jquery' ), FCA_PC_PLUGIN_VER, true );
		}
	}
	add_action( 'init', 'fca_pc_register_scripts' );

	function fca_pc_add_plugin_action_links( $links ) {

		$configure_url = admin_url( 'admin.php?page=fca_pc_settings_page' );
		$support_url = FCA_PC_PLUGIN_PACKAGE === 'Lite' ? 'https://wordpress.org/support/plugin/facebook-conversion-pixel' : 'https://fatcatapps.com/support';

		$new_links = array(
			'configure' => "<a href='" . esc_url( $configure_url ) . "' >" . esc_attr__( 'Configure Pixel', 'facebook-conversion-pixel' ) . '</a>',
			'support' => "<a target='_blank' href='" . esc_url( $support_url ) . "' >" . esc_attr__( 'Support', 'facebook-conversion-pixel' ) . '</a>'
		);

		$links = array_merge( $new_links, $links );

		return $links;

	}
	add_filter( 'plugin_action_links_' . FCA_PC_PLUGINS_BASENAME, 'fca_pc_add_plugin_action_links' );

	// LOCALIZATION
	function fca_pc_load_localization() {
		load_plugin_textdomain( 'pixel-cat', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	add_action( 'init', 'fca_pc_load_localization' );

	//ADD NAG IF NO PIXEL IS SET
	function fca_pc_admin_notice() {

		$show_upgrade_info = get_option( 'fca_pc_after_upgrade_info', false );

		if ( isSet( $_GET['fca_pc_dismiss_upgrade_info'] ) && current_user_can( 'manage_options' ) ) {
			$show_upgrade_info = false;
			update_option( 'fca_pc_after_upgrade_info', false );
		}

		if ( $show_upgrade_info ) {
			$settings_url = admin_url( 'admin.php?page=fca_pc_settings_page' );
			$read_more_url = 'https://fatcatapps.com/migrate-new-facebook-pixel/';
			$dismiss_url = add_query_arg( 'fca_pc_dismiss_upgrade_info', true );

			echo '<div id="fca-pc-setup-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
				echo '<p style="margin-top: 0;"><strong>' .  esc_attr__( "Pixel Cat: ", 'facebook-conversion-pixel' ) . '</strong>' .  esc_attr__( "Thanks for upgrading to the new Facebook Pixel. We've prepared a handy guide that explains what you'll need to do to complete setup.", 'facebook-conversion-pixel' ) . '</p>';
				echo '<p>'.  esc_attr__( "Want to revert to the old Facebook Conversion Pixel? Go to your", 'facebook-conversion-pixel' ) . " <a href='" . esc_url( $settings_url ) . "'>" . esc_attr__( "Facebook Pixel settings</a> and click 'Click here to downgrade' at the very bottom of the screen.", 'facebook-conversion-pixel' ) . '</p>';
				echo "<a style='margin-right: 16px; margin-top: 32px;' href='$read_more_url' class='button button-primary' target='_blank' >" . esc_attr__( 'Read the Facebook Pixel migration guide', 'facebook-conversion-pixel' ) . "</a> ";
				echo "<a style='margin-right: 16px; position: relative;	top: 36px;' href='" . esc_url( $dismiss_url ) . "'>" . esc_attr__( 'Close', 'facebook-conversion-pixel' ) . "</a> ";
				echo '<br style="clear:both">';
			echo '</div>';

		}

		$dismissed = get_option( 'fca_pc_no_pixel_dismissed', false );
		$options = get_option( 'fca_pc', array() );
		$screen = get_current_screen();

		if ( isSet( $_GET['fca_pc_dismiss_no_pixel'] ) && current_user_can( 'manage_options' ) ) {
			$dismissed = true;
			update_option( 'fca_pc_no_pixel_dismissed', true );
		}

		if ( !$dismissed && empty( $options['pixels'] ) && $screen->id != 'toplevel_page_fca_pc_settings_page'  ) {
			$url = admin_url( 'admin.php?page=fca_pc_settings_page' );
			$dismiss_url = add_query_arg( 'fca_pc_dismiss_no_pixel', true );

			echo '<div id="fca-pc-setup-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
				echo '<p><strong>' . esc_attr__( "Thank you for installing Pixel Cat.", 'facebook-conversion-pixel' ) . '</strong></p>';
				echo '<p>' . esc_attr__( "It looks like you haven't configured your Facebook Pixel yet. Ready to get started?", 'facebook-conversion-pixel' ) . '</p>';
				echo "<a href='" . esc_url( $url ) . "' class='button button-primary' style='margin-top: 25px;'>" . esc_attr__( 'Set up my Pixel', 'facebook-conversion-pixel' ) . "</a> ";
				echo "<a style='position: relative; top: 30px; left: 16px;' href='" . esc_url( $dismiss_url ) . "' >" . esc_attr__( 'Dismiss', 'facebook-conversion-pixel' ) . "</a> ";
				echo '<br style="clear:both">';
			echo '</div>';
		}

	}
	add_action( 'admin_notices', 'fca_pc_admin_notice' );


	//TURN OFF EDD/WOOCOMMERCE INTEGRATIONS WHEN PLUGINS ARE DISABLED
	function fca_pc_plugin_check( $plugin ) {

		$options = get_option( 'fca_pc', array() );

		if ( $plugin == 'woocommerce/woocommerce.php' ) {
			$options['woo_integration'] = false;
			$options['woo_feed'] = false;
		}
	
		update_option( 'fca_pc', $options );

	}
	add_action( 'deactivated_plugin', 'fca_pc_plugin_check', 10, 1 );
	
	function fca_pc_backward_compatibility_260( ){
		
		$options = get_option( 'fca_pc', array() );
		$updated = get_option( 'fca_pc_version' ) ? version_compare( get_option( 'fca_pc_version' ), '2.6.0', '>=' ) : 0;
		$pixels = empty( $options['pixels'] ) ? false : true;

		//if fca_pc_version doesn't exist, take old ids and create new db structure
		if( !$updated && !$pixels ){

			$old_pixels = array();
			$pixel_count = 1;
			$pixel = empty( $options['id'] ) ? '' : $options['id'];
			$pixels = empty( $options['ids'] ) ? array() : $options['ids'];

			if( $pixel ){
				$old_pixel = array(
					'pixel' => $pixel,
					'capi' => '',
					'test' => '',
					'paused' => '',
					'type' => 'Facebook Pixel',
					'ID' => 'old_pixel_' . $pixel_count
				);
				array_push( $old_pixels, json_encode( $old_pixel ) );
				$pixel_count += 1;
			}

			if( $pixels ){
				forEach( $pixels as $pixel ){
					$old_pixel = array(
						'pixel' => $pixel,
						'capi' => '',
						'test' => '',
						'paused' => '',
						'type' => 'Facebook Pixel',
						'ID' => 'old_pixel_' . $pixel_count
					);
					array_push( $old_pixels, json_encode( $old_pixel ) );
					$pixel_count += 1;
				}
			}

			// add old pixels to 'pixels' array
			$options['pixels'] = $old_pixels;
			update_option( 'fca_pc', $options );
			update_option( 'fca_pc_version', '2.6.0' );

		}
	
	}
	add_action( 'admin_init', 'fca_pc_backward_compatibility_260' );
	
}
