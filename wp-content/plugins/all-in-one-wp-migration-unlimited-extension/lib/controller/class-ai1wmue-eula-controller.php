<?php
/**
 * Copyright (C) 2014-2020 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Kangaroos cannot jump here' );
}

class Ai1wmue_Eula_Controller {

	/**
	 * Check if EULA needs to be displayed
	 *
	 * @return boolean
	 */
	public static function should_display_eula() {
		// Only show on admin pages
		if ( ! is_admin() ) {
			return false;
		}

		// Check if user has capability to manage plugins
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return false;
		}

		// Additional check: verify the plugin is actually active
		if ( ! is_plugin_active( AI1WMUE_PLUGIN_BASENAME ) ) {
			return false;
		}

		// Check if EULA has been accepted for current version
		$accepted_version = get_option( 'ai1wmue_eula_accepted_version', '' );

		// Show EULA if not accepted or if version has changed
		return ( $accepted_version !== AI1WMUE_EULA_VERSION );
	}

	/**
	 * Display EULA modal
	 *
	 * @return void
	 */
	public static function display_eula_modal() {
		if ( ! self::should_display_eula() ) {
			return;
		}

		// Read EULA content securely
		$eula_path    = AI1WMUE_PATH . DIRECTORY_SEPARATOR . 'End-User-License-Agreement.txt';
		$eula_content = '';

		// Validate file path is within plugin directory
		$real_path   = realpath( $eula_path );
		$plugin_path = realpath( AI1WMUE_PATH );

		if ( $real_path && $plugin_path && strpos( $real_path, $plugin_path ) === 0 && file_exists( $eula_path ) ) {
			$eula_content = file_get_contents( $eula_path );
		}

		// Enqueue styles
		if ( is_rtl() ) {
			wp_enqueue_style(
				'ai1wmue_eula_modal',
				Ai1wm_Template::asset_link( 'css/eula-modal.min.rtl.css', 'AI1WMUE' ),
				array( 'ai1wm_servmask' )
			);
		} else {
			wp_enqueue_style(
				'ai1wmue_eula_modal',
				Ai1wm_Template::asset_link( 'css/eula-modal.min.css', 'AI1WMUE' ),
				array( 'ai1wm_servmask' )
			);
		}

		// Enqueue scripts
		wp_enqueue_script(
			'ai1wmue_eula_modal',
			Ai1wm_Template::asset_link( 'javascript/eula-modal.min.js', 'AI1WMUE' ),
			array( 'jquery' )
		);

		// Check if this is an update scenario (only for versions after 1.0)
		$is_update = get_option( 'ai1wmue_eula_accepted', false ) && get_option( 'ai1wmue_eula_accepted_version', '' ) !== AI1WMUE_EULA_VERSION && AI1WMUE_EULA_VERSION !== '1.0';

		// Localize script
		wp_localize_script(
			'ai1wmue_eula_modal',
			'ai1wmue_eula',
			array(
				'ajax'            => array(
					'url' => wp_make_link_relative( admin_url( 'admin-ajax.php?action=ai1wmue_eula_response' ) ),
				),
				'secret_key'      => get_option( AI1WM_SECRET_KEY ),
				'nonce'           => wp_create_nonce( 'ai1wmue_eula_action' ),
				'show_eula'       => true,
				'is_update'       => $is_update,
				'confirm_decline' => __( 'Are you sure you want to decline the EULA? The Unlimited Extension plugin will be deactivated.', AI1WMUE_PLUGIN_NAME ),
				'plugins_url'     => network_admin_url( 'plugins.php' ),
			)
		);

		// Render modal
		Ai1wm_Template::render(
			'eula/eula-modal',
			array(
				'eula_content' => $eula_content,
				'is_update'    => $is_update,
			),
			AI1WMUE_TEMPLATES_PATH
		);
	}

	/**
	 * Handle EULA response
	 *
	 * @return void
	 */
	public static function eula_response() {
		// Verify request
		ai1wm_setup_environment();

		// Params
		$params = stripslashes_deep( $_POST );

		// Verify nonce
		if ( ! isset( $params['nonce'] ) || ! wp_verify_nonce( $params['nonce'], 'ai1wmue_eula_action' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed. Please refresh the page and try again.', AI1WMUE_PLUGIN_NAME ) ) );
		}

		// Verify secret key
		$secret_key = isset( $params['secret_key'] ) ? trim( $params['secret_key'] ) : null;
		try {
			ai1wm_verify_secret_key( $secret_key );
		} catch ( Ai1wm_Not_Valid_Secret_Key_Exception $e ) {
			wp_send_json_error( array( 'message' => $e->getMessage() ) );
		}

		// Check capability
		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient permissions.', AI1WMUE_PLUGIN_NAME ) ) );
		}

		// Sanitize and validate action
		$user_action = isset( $params['eula_action'] ) ? sanitize_text_field( $params['eula_action'] ) : '';

		// Whitelist allowed actions
		$allowed_actions = array( 'accept', 'decline' );
		if ( ! in_array( $user_action, $allowed_actions, true ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid action.', AI1WMUE_PLUGIN_NAME ) ) );
		}

		if ( $user_action === 'accept' ) {
			// Store acceptance with version
			update_option( 'ai1wmue_eula_accepted', true );
			update_option( 'ai1wmue_eula_accepted_version', AI1WMUE_EULA_VERSION );
			update_option( 'ai1wmue_eula_accepted_date', current_time( 'mysql' ) );
			update_option( 'ai1wmue_eula_accepted_by', get_current_user_id() );

			wp_send_json_success();
		} elseif ( $user_action === 'decline' ) {
			// Deactivate plugin
			deactivate_plugins( AI1WMUE_PLUGIN_BASENAME );

			wp_send_json_success();
		}
	}
}
