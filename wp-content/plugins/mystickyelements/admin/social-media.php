<?php 
$custom_fields = array();
foreach ( $contact_field as $key=>$value ) {
	if ( isset($value['custom_fields']) && is_array($value['custom_fields']) ) {
		$custom_fields[] = $value['custom_fields'][0];
	}
} 
?>
<div id="mystickyelements-tab-social-media" class="mystickyelements-tab-social-media mystickyelements-options"  style="display: <?php echo esc_attr(( isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-social-media' ) ? 'block' : 'none'); ?>;">
	<div class="" >
		<!-- Social Channels Tabs Section -->
		<div class="myStickyelements-container myStickyelements-social-channels-tabs">
		
			<div class="myStickyelements-header-title">
				<h3><?php _e('Show Chat & Social Icons', 'mystickyelements'); ?></h3>
				<label for="myStickyelements-social-channels-enabled" class="myStickyelements-switch">
					<input type="checkbox" id="myStickyelements-social-channels-enabled" name="social-channels[enable]" value="1" <?php checked( @$social_channels['enable'], '1' );?> />
					<span class="slider round"></span>
				</label>
				<p class="social-disable-info" style="display: none;"><i class="fas fa-info-circle"></i>&nbsp;&nbsp;<span><?php esc_html_e('Social channels in sticky bar has been turned off.','mystickyelements');?></span>&nbsp;&nbsp;<a href="javascript:void(0)" class="mystickyelements-turnit-on" data-turnit="myStickyelements-social-channels-enabled"><?php esc_html_e( 'Turn it on', 'mystickyelements' );?></a><?php esc_html_e( ' to collect user submitted forms from sidebar.', 'mystickyelements' );?></p>
			</div>
			<div class="mystickyelements-header-sub-title">
				<h4><?php _e( 'Enable your preferred social channels', 'mystickyelements' ); ?></h4>
			</div>
			<div class="mystickyelements-disable-wrap">
				<div class="mystickyelements-disable-content-wrap" style="display:none;">
					<div class="mystickyelements-disable-content">
						<i class="fas fa-eye-slash"></i>
						<p><?php esc_html_e( 'DISABLED', 'mystickyelements' );?></p>
					</div>
				</div>

				<div class="mystickyelements-action-popup-open mystickyelements-action-popup-status" id="socialform-status-popup" style="display:none;">
					<div class="popup-ui-widget-header">
						<span id="ui-id-1" class="ui-dialog-title"><?php esc_html_e( 'Disable Social Channels?', 'mystickyelements');?></span><span class="close-dialog" data-from ='social-form'> &#10006 </span>
					</div>	
					<div id="widget-delete-confirm" class="ui-widget-content">
						<p>
							<?php 
							_e( "You are about to disable the <span>social channels and chat options</span>. Once disabled, they will no longer be visible. Only the contact form will appear if it is enabled.", 'mystickyelements');
							?> 
						</p>
					</div>
					<div class="popup-ui-dialog-buttonset"><button type="button" class="btn-disable-cancel button-social-popup-disable"><?php esc_html_e('Disable anyway','mystickyelements');?></button><button type="button" class="mystickyelement-keep-widget-btn button-social-popup-keep" data-from = "contact-form" ><?php esc_html_e('Keep using','mystickyelements');?></button></div>
				</div>
				<div id="mystickyelement-social-popup-overlay" class="stickyelement-overlay" data-from = "social-form" style="display:none;"></div>
				<div class="myStickyelements-social-search">
					<label><?php _e( 'Quick Search', 'mystickyelements' ); ?></label>
					<div class="myStickyelements-social-search-wrap">
						<input type="text" placeholder="facebook" id="myStickyelements-social-search-input"/><i class="fas fa-search"></i>
					</div>
				</div>
				<div class="myStickyelements-social-channels-lists-section">
					<ul class="myStickyelements-social-channels-lists mystickyelements-free-version">
						<?php 
							$custom_channel = ['custom_one', 'custom_two', 'custom_three', 'custom_four', 'custom_five', 'custom_six'];
							$custom_shortcode = ['custom_seven', 'custom_eight', 'custom_nine', 'custom_ten', 'custom_eleven', 'custom_twelve'];
							$social_channels_lists = set_social_channel_order( $social_channels_lists , $social_channels_tabs );
							
							foreach ( $social_channels_lists as $key => $value ): 
							
								if (isset($value['is_locked']) && $value['is_locked'] == 1) {
									continue;
								} 
								$search_channel = (isset($value['search_class']) ) ? $key. ' ' . $value['search_class'] : $key;
								
								if( !isset($value['custom']) ) { 
								$channel_class	= (isset($value['channel_class'])) ? $value['channel_class'] : '';						
								?>
									<li data-search="<?php echo str_replace("_", " ", $search_channel); ?>" <?php if ( isset( $value['is_locked'] ) && $value['is_locked'] == 1 ): ?> class="upgrade-myStickyelements" <?php endif; ?>>
										<label>
											<span class="social-channels-list <?php echo esc_attr($channel_class);?> social-<?php echo esc_attr($key); ?> <?php if( isset($social_channels[$key]) && $social_channels[$key] == '1' ) : ?>social-checked-active<?php endif; ?>" style="background-color: <?php echo set_span_bg_color( $key , $value ); ?>">
												
												<i class="<?php echo social_channel_icon_class( $key , $value );?>"></i>
												<?php $social_channels[$key] = isset($social_channels[$key]) ? $social_channels[$key] : '';?>
											</span>
											<input type="checkbox" data-social-channel-text="<?php echo esc_attr($value['hover_text']);?>" data-social-channel="<?php echo esc_attr($key); ?>" class="social-channel" name="social-channels[<?php echo esc_attr($key); ?>]" value="1" <?php checked(@$social_channels[$key], '1'); ?> <?php if (isset($value['is_locked']) && $value['is_locked'] == 1) { echo "disabled"; } ?>/>
										</label>
										<span class="social-tooltip-popup">
											<?php 
											if ( isset($value['custom_tooltip']) && $value['custom_tooltip'] != "" ) {
												 echo esc_attr($value['custom_tooltip']);
											 } else {
												echo str_replace("_", " ", $value['hover_text']);
											 }											
											?>
										</span>
									</li>
						<?php }
						endforeach; ?>						
					</ul>
				</div>
				<input type="hidden" id="myStickyelements-custom-channel-lenght" name = "general-settings[custom_channel_count]" value="<?php echo esc_attr((isset($general_settings['custom_channel_count']) && $general_settings['custom_channel_count']!='') ? $general_settings['custom_channel_count'] : 0) ; ?>" />
				
				<input type="hidden" id="myStickyelements-custom-shortcode-lenght" name = "general-settings[custom_shortcode_count]" value="<?php echo esc_attr((isset($general_settings['custom_shortcode_count']) && $general_settings['custom_shortcode_count']!='') ? $general_settings['custom_shortcode_count'] : 0) ; ?>" />
				
				<div class="mystickyelement-social-buttons">
					<div class="mystickyelement-more-less-channel">
						<a id="myStickyelements-more-social" name="button_more_social_btn" href="javascript:void(0);">
							<svg class="more-icon" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9 1.5L5 5.5L1 1.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<svg class="less-icon" width="10" height="7" viewBox="0 0 10 7" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M1 5.5L5 1.5L9 5.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>

							<span><?php _e( 'Show More Channels', 'mystickyelements' ); ?></span>
						</a>
					</div>
					<div class="mystickyelement-custom-channel">
						<button id="myStickyelements-add-custom-social" name="button_add_custom_social" value = "<?php _e( 'Custom Channel', 'mystickyelements' ); ?>"><?php _e( 'Custom Channel', 'mystickyelements' ); ?><svg style="fill: #3F83F8;" id="plus-circle" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 36 36"><path id="Path_1928" data-name="Path 1928" d="M18,7.875A1.125,1.125,0,0,1,19.125,9v9A1.125,1.125,0,0,1,18,19.125H9a1.125,1.125,0,0,1,0-2.25h7.875V9A1.125,1.125,0,0,1,18,7.875Z" fill-rule="evenodd"/><path id="Path_1929" data-name="Path 1929" d="M16.875,18A1.125,1.125,0,0,1,18,16.875h9a1.125,1.125,0,0,1,0,2.25H19.125V27a1.125,1.125,0,0,1-2.25,0Z" fill-rule="evenodd"/><path id="Path_1930" data-name="Path 1930" d="M18,33.75A15.75,15.75,0,1,0,2.25,18,15.75,15.75,0,0,0,18,33.75ZM18,36A18,18,0,1,0,0,18,18,18,0,0,0,18,36Z" fill-rule="evenodd"/></svg></button>
						<label>
							<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
								<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
								<p><?php esc_html_e("Use custom channels to link to any web page, social service or even custom JavaScript code. ", 'mystickyelements'); ?><a href="https://premio.io/help/mystickyelements/how-do-i-create-a-custom-link-or-javascript-channel/" target="_blank"><?php esc_html_e('Learn more','mystickyelements');?></a></p>
							</div><a class = "custom-social-channel-info-link" href="https://premio.io/help/mystickyelements/how-do-i-create-a-custom-link-or-javascript-channel/" target="_blank"><?php _e( 'How custom channels work?', 'mystickyelements' ); ?></a>
						</label>
					</div>
					<div class="mystickyelement-custom-shortcode">
						<button id="myStickyelements-add-custom-shortcode" name="button_add_custom_shortcode" value = "<?php _e( 'Custom Shortcode', 'mystickyelements' ); ?>"  > <?php _e( 'Custom Shortcode', 'mystickyelements' ); ?><svg style="fill: #9061F9;" id="plus-circle" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 36 36"><path id="Path_1928" data-name="Path 1928" d="M18,7.875A1.125,1.125,0,0,1,19.125,9v9A1.125,1.125,0,0,1,18,19.125H9a1.125,1.125,0,0,1,0-2.25h7.875V9A1.125,1.125,0,0,1,18,7.875Z" fill-rule="evenodd"/><path id="Path_1929" data-name="Path 1929" d="M16.875,18A1.125,1.125,0,0,1,18,16.875h9a1.125,1.125,0,0,1,0,2.25H19.125V27a1.125,1.125,0,0,1-2.25,0Z" fill-rule="evenodd"/><path id="Path_1930" data-name="Path 1930" d="M18,33.75A15.75,15.75,0,1,0,2.25,18,15.75,15.75,0,0,0,18,33.75ZM18,36A18,18,0,1,0,0,18,18,18,0,0,0,18,36Z" fill-rule="evenodd"/></svg></button>
						<label>
							<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
								<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
								<p><?php esc_html_e("Add a custom shortcode/Iframe/HTML to your widget. Learn more or check out ", 'mystickyelements'); ?><a href="https://premio.io/help/mystickyelements/how-to-add-a-custom-shortcode-html-channel-to-your-widget/" target="_blank"><?php esc_html_e('some inspirations','mystickyelements');?></a></p>
							</div><a class = "custom-social-channel-info-link" href="https://premio.io/help/mystickyelements/how-to-add-a-custom-shortcode-html-channel-to-your-widget/" target="_blank"><?php _e( 'How shortcodes work?', 'mystickyelements' ); ?></a>
						</label>
					</div>
				</div>
				<div class="social-channel-popover" style="display:none;">
					<p>
						<strong>Upgrade to MyStickyElements Pro</strong> ‍🚀 for unlimited channels, custom fields, send leads to email, integrate with MailChimp/MailPoet, with more triggers & targeting rules
						<br/><br/>
						<span class="mystickyelement-tab-integration-action mystickyelement-upgrade-action">
							<a class="upgradenow-box-btn" href="<?php echo esc_url($upgrade_url); ?>" target="_blank" class="btn">Upgrade Now</a>
						</span>					
						<a href="javascript:;" class="dismiss-btn premio-upgrade-dismiss-btn"><span class="dashicons dashicons-no-alt"></span></a>
					</p>
					
					<!--a href="<?php echo esc_url($upgrade_url) ?>" target="_blank">
						<?php _e('Get unlimited channels in the Pro plan', 'mystickyelements'); ?>
						<strong><?php _e('Upgrade Now', 'mystickyelements'); ?></strong>
					</a-->
				</div>
				<div class="myStickyelements-social-channels-info">
					<div class="mystickyelements-header-sub-title">
						<h4><?php _e( 'Customize Channel Specific Behavior', 'mystickyelements' ); ?></h4>
					</div>
					<div class="social-channels-tab">
						<?php
						if (!empty($social_channels_tabs)) {
							global $social_channel_count;
							$social_channel_count = 1;
							foreach( $social_channels_tabs as $key=>$value) {
								$this->mystickyelement_social_tab_add( $key, '' );
							}
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>	
</div>
<?php 

	function social_channel_icon_class( $key , $value ){
		if( strpos($key, 'custom_channel') !== false && $value['custom_icon'] == '' && $value['fontawesome_icon'] == '' ){
			return 'fas fa-cloud-upload-alt';
		}else if( strpos($key, 'custom_channel') !== false && $value['custom_icon'] == '' && $value['fontawesome_icon'] != '' ){
			return $value['fontawesome_icon'];
		}else if( strpos($key, 'custom_shortcode') !== false && $value['custom_icon'] == '' && $value['fontawesome_icon'] == '' ){
			return 'fas fa-code';
		}else if( strpos($key, 'custom_shortcode') !== false && $value['custom_icon'] == '' && $value['fontawesome_icon'] != '' ){
			return $value['fontawesome_icon'];
		}else if(isset($value['class'])){
			return $value['class'];
		}
	}
	
	function set_span_bg_color( $key , $value ){
		$custom_channel = ['custom_one', 'custom_two', 'custom_three', 'custom_four', 'custom_five', 'custom_six'];
		$custom_shortcode = ['custom_seven', 'custom_eight', 'custom_nine', 'custom_ten', 'custom_eleven', 'custom_twelve'];
		
		if( strpos($key, 'custom_channel') !== false || in_array($key, $custom_channel)){
			return '#7761DF';
		}else if( strpos($key, 'custom_shortcode') !== false || in_array($key, $custom_shortcode)){
			return '#7761DF';
		}else{
			return (isset($value['background_color'])) ? $value['background_color'] : '';
		}
	}

	function set_social_channel_order( $social_channels_lists , $social_channels_tabs ){
		$selected_social_channels = array();
		foreach ( $social_channels_tabs as $key1 => $social_channel ) {	
			if (strpos($key1, 'custom') !== false) {
			   $selected_social_channels[$key1] =  $social_channel;
			}
		}
		$social_channels_lists = array_merge( $social_channels_lists , $selected_social_channels );	
		return $social_channels_lists;
	}
?>
