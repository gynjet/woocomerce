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
?>

<div id="ai1wmue-eula-modal-overlay" class="ai1wmue-modal-overlay">
	<div class="ai1wmue-modal-container">
		<div class="ai1wmue-modal-header">
			<h2><?php _e( 'All-in-One WP Migration Unlimited Extension EULA', AI1WMUE_PLUGIN_NAME ); ?></h2>
			<?php if ( isset( $is_update ) && $is_update ) : ?>
				<p class="ai1wmue-eula-update-notice">
					<?php _e( 'The End-User License Agreement has been updated. Please review and accept the new terms to continue using the plugin.', AI1WMUE_PLUGIN_NAME ); ?>
				</p>
			<?php endif; ?>
		</div>
		<div class="ai1wmue-modal-content">
			<div class="ai1wmue-eula-content">
				<?php echo nl2br( esc_html( $eula_content ) ); ?>
			</div>
		</div>
		<div class="ai1wmue-modal-footer">
			<button type="button" class="ai1wm-button-red" id="ai1wmue-eula-decline">
				<?php _e( 'Decline', AI1WMUE_PLUGIN_NAME ); ?>
			</button>
			<button type="button" class="ai1wm-button-green" id="ai1wmue-eula-accept">
				<?php _e( 'Accept', AI1WMUE_PLUGIN_NAME ); ?>
			</button>
		</div>
	</div>
</div>
