<?php


// ADD OUR RECOMMENDED MENU
function fca_pc_featured_plugins_menu() {
	add_submenu_page(
		'fca_pc_settings_page',
		esc_attr__('Featured Plugins', 'facebook-conversion-pixel'),
		esc_attr__('Featured Plugins', 'facebook-conversion-pixel'),
		'manage_options',
		'fca-pc-featured-plugins',
		'fca_pc_render_featured_plugins'
	);
}
add_action( 'admin_menu', 'fca_pc_featured_plugins_menu' );

function fca_pc_render_featured_plugins(){
	$content = '<figure class="wp-block-embed is-type-wp-embed is-provider-plugin-directory wp-block-embed-plugin-directory"><div class="wp-block-embed__wrapper">
https://wordpress.org/plugins/quiz-cat/
</div></figure><figure class="wp-block-embed is-type-wp-embed is-provider-plugin-directory wp-block-embed-plugin-directory"><div class="wp-block-embed__wrapper">
https://wordpress.org/plugins/easy-pricing-tables/
</div></figure><figure class="wp-block-embed is-type-wp-embed is-provider-plugin-directory wp-block-embed-plugin-directory"><div class="wp-block-embed__wrapper">
https://wordpress.org/plugins/landing-page-cat/
</div></figure>';
	wp_enqueue_style( 'fca_pc_admin_stylesheet', FCA_PC_PLUGINS_URL . '/includes/editor/admin.css', array(), FCA_PC_PLUGIN_VER );
	echo fca_pc_admin_header_nav() ?>
	<div class="wrap">
		<h2><?php esc_html_e( 'Featured Plugins', 'facebook-conversion-pixel' ) ?></h2>	
		<p><?php esc_html_e( 'Problems, Suggestions?', 'facebook-conversion-pixel' ) ?> 
		<a href="https://wordpress.org/support/plugin/facebook-conversion-pixel/" target="_blank"><?php esc_html_e( 'Visit the support forum', 'facebook-conversion-pixel' ) ?></a> | 
		<a href="https://fatcatapps.com/article-categories/pixelcat/" target="_blank"><?php esc_html_e( 'Knowledge Base', 'facebook-conversion-pixel' ) ?></a> | 
		<a href="https://youtu.be/pGVuBYU2D54" target="_blank"><?php esc_html_e( 'Watch Demo', 'facebook-conversion-pixel' ) ?></a> |
		<a href="http://fatcatapps.com/pixelcat/premium/" target="_blank"><?php esc_html_e( 'Get Pixel Cat Premium', 'facebook-conversion-pixel' ) ?></a>
		</p>		
		<div class="fca-featured-plugins">
			<?php echo apply_filters( 'the_content', $content ) ?>
		</div>
	</div>	
	<?php
}

function fca_pc_admin_review_notice() {
	
	$action = empty( $_GET['fca_pc_review_notice'] ) ? false : sanitize_text_field( $_GET['fca_pc_review_notice'] );
	
	if( $action ) {
		
		$nonce = empty( $_GET['fca_pc_nonce'] ) ? false : sanitize_text_field( $_GET['fca_pc_nonce'] );
		$nonceVerified = wp_verify_nonce( $nonce, 'fca_pc_leave_review' );
		if( $nonceVerified == false ) {
			wp_die( "Unauthorized. Please try logging in again." );
		}
		
		update_option( 'fca_pc_show_review_notice', false );
		if( $action == 'review' ) {
			echo "<script>document.location='https://wordpress.org/support/plugin/facebook-conversion-pixel/reviews/'</script>";
		}
				
		if( $action == 'later' ) {
			//MAYBE MAKE SURE ITS NOT ALREADY SET
			if( wp_next_scheduled( 'fca_pc_schedule_review_notice' ) == false ) {
				wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_pc_schedule_review_notice' );
			}
		}
		
		if( $action == 'dismiss' ) {
			//DO NOTHING
		}		
	}	
	
	$show_review_option = get_option( 'fca_pc_show_review_notice', null );
	if ( $show_review_option === null ) {
	
		//MAYBE MAKE SURE ITS NOT ALREADY SET
		if( wp_next_scheduled( 'fca_pc_schedule_review_notice' ) == false ) {
			wp_schedule_single_event( time() + 30 * DAY_IN_SECONDS, 'fca_pc_schedule_review_notice' );
		}
		add_option( 'fca_pc_show_review_notice', false );
	}
	
	if( $show_review_option ) {

		$nonce = wp_create_nonce( 'fca_pc_leave_review' );
		$review_url = esc_url( add_query_arg( array( 'fca_pc_review_notice' => 'review', 'fca_pc_nonce' => $nonce ) ) );
		$postpone_url = esc_url( add_query_arg( array( 'fca_pc_review_notice' => 'later', 'fca_pc_nonce' => $nonce ) ) );
		$forever_dismiss_url = esc_url( add_query_arg( array( 'fca_pc_review_notice' => 'dismiss', 'fca_pc_nonce' => $nonce ) ) );

		echo '<div id="fca-qc-review-notice" class="notice notice-success is-dismissible" style="padding-bottom: 8px; padding-top: 8px;">';
		
			echo '<p>' . __( "Hi! You've been using Quiz Cat for a while now, so who better to ask for a review than you? Would you please mind leaving us one? It really helps us a lot!", 'facebook-conversion-pixel' ) . '</p>';
			
			echo "<a href='$review_url' class='button button-primary' style='margin-top: 2px;'>" . __( 'Leave review', 'facebook-conversion-pixel' ) . "</a> ";
			echo "<a style='position: relative; top: 10px; left: 7px;' href='$postpone_url' >" . __( 'Maybe later', 'facebook-conversion-pixel' ) . "</a> ";
			echo "<a style='position: relative; top: 10px; left: 16px;' href='$forever_dismiss_url' >" . __( 'No thank you', 'facebook-conversion-pixel' ) . "</a> ";
			echo '<br style="clear:both">';
			
		echo '</div>';
	
	}

}
add_action( 'admin_notices', 'fca_pc_admin_review_notice' );

function fca_pc_enable_review_notice(){
	update_option( 'fca_pc_show_review_notice', true );
	wp_clear_scheduled_hook( 'fca_pc_schedule_review_notice' );
}
add_action ( 'fca_pc_schedule_review_notice', 'fca_pc_enable_review_notice' );

//DEACTIVATION SURVEY
function fca_pc_admin_deactivation_survey( $hook ) {
	if ( $hook === 'plugins.php' ) {
		
		ob_start(); ?>		
		<div id="fca-deactivate" style="position: fixed; left: 232px; top: 191px; border: 1px solid #979797; background-color: white; z-index: 9999; padding: 12px; max-width: 669px;">
			<h3 style="font-size: 14px; border-bottom: 1px solid #979797; padding-bottom: 8px; margin-top: 0;"><?php esc_attr_e( 'Sorry to see you go', 'facebook-conversion-pixel' ) ?></h3>
			<p><?php esc_attr_e( 'Hi, this is David, the creator of Pixel Cat. Thanks so much for giving my plugin a try. I’m sorry that you didn’t love it.', 'facebook-conversion-pixel' ) ?>
			</p>
			<p><?php esc_attr_e( 'I have a quick question that I hope you’ll answer to help us make Pixel Cat better: what made you deactivate?', 'facebook-conversion-pixel' ) ?>
			</p>
			<p><?php esc_attr_e( 'You can leave me a message below. I’d really appreciate it.', 'facebook-conversion-pixel' ) ?>
			</p>
			<p><b><?php esc_attr_e( 'If you\'re upgrading to Pixel Cat Premium and have questions or need help, click <a href=' . 'https://fatcatapps.com/article-categories/gen-getting-started/' . ' target="_blank">here</a></b>', 'facebook-conversion-pixel' ) ?>
			</p>
			<p><textarea style='width: 100%;' id='fca-pc-deactivate-textarea' placeholder='<?php esc_attr_e( 'What made you deactivate?', 'facebook-conversion-pixel' ) ?>'></textarea></p>

			<div style='float: right;' id='fca-deactivate-nav'>
				<button style='margin-right: 5px;' type='button' class='button button-secondary' id='fca-pc-deactivate-skip'><?php esc_attr_e( 'Skip', 'facebook-conversion-pixel' ) ?></button>
				<button type='button' class='button button-primary' id='fca-pc-deactivate-send'><?php esc_attr_e( 'Send Feedback', 'facebook-conversion-pixel' ) ?></button>
			</div>
		</div>		
		<?php
			
		$html = ob_get_clean();
		
		$data = array(
			'html' => $html,
			'nonce' => wp_create_nonce( 'fca_pc_uninstall_nonce' ),
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		);
					
		wp_enqueue_script('fca_pc_deactivation_js', FCA_PC_PLUGINS_URL . '/includes/notices/deactivation.min.js', false, FCA_PC_PLUGIN_VER, true );
		wp_localize_script( 'fca_pc_deactivation_js', "fca_pc", $data );
	}
	
	
}	
add_action( 'admin_enqueue_scripts', 'fca_pc_admin_deactivation_survey' );

//UNINSTALL ENDPOINT
function fca_pc_uninstall_ajax() {
	
	$msg = sanitize_text_field( $_POST['msg'] );
	$nonce = sanitize_text_field( $_POST['nonce'] );
	$nonceVerified = wp_verify_nonce( $nonce, 'fca_pc_uninstall_nonce') == 1;

	if ( $nonceVerified && !empty( $msg ) ) {
		
		$url =  "https://api.fatcatapps.com/api/feedback.php";
				
		$body = array(
			'product' => 'pixelcat',
			'msg' => $msg,		
		);
		
		$args = array(
			'timeout'     => 15,
			'redirection' => 15,
			'body' => json_encode( $body ),	
			'blocking'    => true,
			'sslverify'   => false
		); 		
		
		$return = wp_remote_post( $url, $args );
		
		wp_send_json_success( $msg );

	}
	wp_send_json_error( $msg );

}
add_action( 'wp_ajax_fca_pc_uninstall', 'fca_pc_uninstall_ajax' );

function fca_pc_upgrade_menu() {
	$page_hook = add_submenu_page(
		'fca_pc_settings_page',
		esc_attr__('Upgrade to Premium', 'facebook-conversion-pixel'),
		esc_attr__('Upgrade to Premium', 'facebook-conversion-pixel'),
		'manage_options',
		'pixel-cat-upgrade',
		'fca_pc_upgrade_ob_start'
	);
	add_action('load-' . $page_hook , 'fca_pc_upgrade_page');
}
add_action( 'admin_menu', 'fca_pc_upgrade_menu' );

function fca_pc_upgrade_ob_start() {
    ob_start();
}

function fca_pc_upgrade_page() {
    wp_redirect('https://fatcatapps.com/pixelcat/premium?utm_medium=plugin&utm_source=Pixel%20Cat%20Free&utm_campaign=free-plugin', 301);
    exit();
}

function fca_pc_upgrade_to_premium_menu_js() {
    ?>
    <script type="text/javascript">
    	jQuery(document).ready(function ($) {
            $('a[href="admin.php?page=pixel-cat-upgrade"]').on('click', function () {
        		$(this).attr('target', '_blank')
            })
        })
    </script>
    <style>
        a[href="admin.php?page=pixel-cat-upgrade"] {
            color: #6bbc5b !important;
        }
        a[href="admin.php?page=pixel-cat-upgrade"]:hover {
            color: #7ad368 !important;
        }
    </style>
    <?php 
}
add_action( 'admin_footer', 'fca_pc_upgrade_to_premium_menu_js');