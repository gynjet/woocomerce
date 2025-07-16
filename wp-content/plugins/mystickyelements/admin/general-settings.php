<?php
//include('mystickyelements_timezone.php');	

$traffic_source  		= ( isset($general_settings['traffic-source'])) ? $general_settings['traffic-source'] : '';
$direct_visit  	 		= ( isset($traffic_source['direct-visit'])) ? $traffic_source['direct-visit'] : '';
$social_network  		= ( isset($traffic_source['social-network'])) ? $traffic_source['social-network'] : '';
$search_engines  		= ( isset($traffic_source['search-engines'])) ? $traffic_source['search-engines'] : '';
$google_ads      		= ( isset($traffic_source['google-ads'])) ? $traffic_source['google-ads'] : '';
$other_source_option	= ( isset($traffic_source['other-source-option'])) ? $traffic_source['other-source-option'] : '';
$other_source_url      	= ( isset($traffic_source['other-source-url'])) ? $traffic_source['other-source-url'] : array('');

$general_settings['flyout'] 				= isset($general_settings['flyout']) ? $general_settings['flyout']: 'enable';
$general_settings['mobile_behavior'] 		= isset($general_settings['mobile_behavior']) ? $general_settings['mobile_behavior']: 'disable';
$general_settings['google_analytics'] 		= isset($general_settings['google_analytics']) ? $general_settings['google_analytics'] : '';
$general_settings['form_open_automatic'] 	= isset($general_settings['form_open_automatic']) ? $general_settings['form_open_automatic'] : '';
$general_settings['minimize_desktop'] 		= isset($general_settings['minimize_desktop']) ? $general_settings['minimize_desktop'] : '';
$general_settings['minimize_mobile'] 		= isset($general_settings['minimize_mobile']) ? $general_settings['minimize_mobile'] : '';
$general_settings['load_google_fonts'] 		= isset($general_settings['load_google_fonts']) ? $general_settings['load_google_fonts'] : '';
$general_settings['time_delay'] 			= isset($general_settings['time_delay']) ? $general_settings['time_delay'] : '';
$general_settings['custom_position'] 		= isset($general_settings['custom_position']) ? $general_settings['custom_position'] : '';
$general_settings['custom_position_mobile'] = isset($general_settings['custom_position_mobile']) ? $general_settings['custom_position_mobile'] : '';
$general_settings['minimize_tab'] 			= isset($general_settings['minimize_tab']) ? $general_settings['minimize_tab'] : '';
$general_settings['font_family'] 			= isset($general_settings['font_family']) ? $general_settings['font_family'] : '';
$furl = false;
foreach( $other_source_url as $surl ){
	if ( $surl != '') {
		$furl = true;
	}
}
if ( !$furl){
	$other_source_url = array();
}
?>

<div id="mystickyelements-tab-display-settings" class="mystickyelements-tab-display-settings mystickyelements-options mystickyelements-options-free-version"  style="display: <?php echo esc_attr(( isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-display-settings' ) ? 'block' : 'none'); ?>;">
	<div class="">
		
		<div class="myStickyelements-contact-form-field-advance-tab">
			<div class="myStickyelements-header-title">
				<h3><?php _e('Display & Behavior Settings', 'mystickyelements'); ?></h3>
			</div>
			<div class="myStickyelements-content-section">
				<div class="mystickyelements-content-section-main">
					<div class="mystickyelements-content-section-wrap">
						<span class="myStickyelements-label" ><?php _e( 'Templates', 'mystickyelements' );?></span>
						<div class="myStickyelements-inputs myStickyelements-label">
							<?php $general_settings['templates'] = (isset($general_settings['templates']) && $general_settings['templates']!= '') ? $general_settings['templates'] : 'default'; ?>
							<select id="myStickyelements-inputs-templete" name="general-settings[templates]" >
								<option value="default" <?php selected( @$general_settings['templates'], 'default' ); ?>><?php _e( 'Default', 'mystickyelements' );?></option>
								<option value="sharp" <?php selected( @$general_settings['templates'], 'sharp' ); ?>><?php _e( 'Sharp ', 'mystickyelements' );?></option>
								<option value="roundad" <?php selected( @$general_settings['templates'], 'roundad' ); ?>><?php _e( 'Rounded', 'mystickyelements' );?></option>
								<option value="leaf_right" <?php selected( @$general_settings['templates'], 'leaf_right' ); ?>><?php _e( 'Leaf right', 'mystickyelements' );?></option>
								<option value="round" <?php selected( @$general_settings['templates'], 'round' ); ?>><?php _e( 'Round', 'mystickyelements' );?></option>
								<option value="diamond" <?php selected( @$general_settings['templates'], 'diamond' ); ?>><?php _e( 'Diamond', 'mystickyelements' );?></option>
								<option value="leaf_left" <?php selected( @$general_settings['templates'], 'leaf_left' ); ?>><?php _e( 'Leaf left', 'mystickyelements' );?></option>
								<option value="arrow" <?php selected( @$general_settings['templates'], 'arrow' ); ?>><?php _e( 'Arrow', 'mystickyelements' );?></option>
								<option value="triangle" <?php selected( @$general_settings['templates'], 'triangle' ); ?>><?php _e( 'Triangle', 'mystickyelements' );?></option>
							</select>
						</div>
					</div>
				
					
					<div class="mystickyelements-content-section-wrap">
						<span class="myStickyelements-label" for="myStickyelements-inputs-position" ><?php _e( 'Position on desktop', 'mystickyelements' );?></span>
						<div class="myStickyelements-inputs">
							<?php
								$desktop_position = array(
									'left' => __('Left', 'mystickyelements'),
									'right' => __('Right', 'mystickyelements'),
									'bottom' => __('Bottom', 'mystickyelements'),
								);
								$selected_desktop_position = (isset($general_settings['position']) && $general_settings['position'] != '') ? $general_settings['position'] : 'left';
							?>
							<select id="myStickyelements-inputs-position" name="general-settings[position]" >
								<?php foreach( $desktop_position as $key => $value ): ?>
									<option value="<?php echo esc_attr($key); ?>" <?php selected( @$selected_desktop_position, $key ); ?>><?php echo esc_html($value); ?></option>
								<?php endforeach; ?>
							</select>
							 
						</div>
					</div>
					<div class="myStickyelements-position-on-screen-wrap" style="<?php echo esc_attr((isset($general_settings['position']) && $general_settings['position'] != 'bottom') ? 'display: none;' : ''); ?>">
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" ><?php _e( 'Position on screen', 'mystickyelements' );?></span>
							<div class="myStickyelements-inputs myStickyelements-label">
								<?php $general_settings['position_on_screen'] = (isset($general_settings['position_on_screen']) && $general_settings['position_on_screen']!= '') ? $general_settings['position_on_screen'] : 'center'; ?>
								<select id="myStickyelements-inputs-position-on-screen" name="general-settings[position_on_screen]" >
									<option value="center" <?php selected( @$general_settings['position_on_screen'], 'center' ); ?>><?php _e( 'Center', 'mystickyelements' );?></option>
									<option value="left" <?php selected( @$general_settings['position_on_screen'], 'left' ); ?>><?php _e( 'Left', 'mystickyelements' );?></option>
									<option value="right" <?php selected( @$general_settings['position_on_screen'], 'right' ); ?>><?php _e( 'Right', 'mystickyelements' );?></option>
								</select>
							</div>
						</div>
					</div>
					<div class="mystickyelements-content-section-wrap">
						<span class="myStickyelements-label" for="myStickyelements-inputs-position_mobile" ><?php _e( 'Position on mobile', 'mystickyelements' );?></span>
						<div class="myStickyelements-inputs">
							<?php
								$position_mobiles = array(
									'left' => __('Left', 'mystickyelements'),
									'right' => __('Right', 'mystickyelements'),
									'top' => __('Top', 'mystickyelements'),
									'bottom' => __('Bottom', 'mystickyelements'),
								);
								$selected_position_mobile = (isset($general_settings['position']) && $general_settings['position'] != '') ? $general_settings['position'] : 'left';
							?>
							<select id="myStickyelements-inputs-position_mobile" name="general-settings[position_mobile]" >
								<?php foreach( $position_mobiles as $key => $value ): ?>
									<option value="<?php echo esc_attr($key); ?>" <?php selected( @$selected_position_mobile, $key ); ?>><?php echo esc_html($value); ?></option>
								<?php endforeach; ?>
							</select>
							 
						</div>
					</div>
					
					<!--<div class="more-setting-rows"> -->
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<label>
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("Configure when the tabs will slide out with the full text", 'mystickyelements'); ?></p>
									</div>
									<?php _e( 'Open tabs when', 'mystickyelements' );?>
								</label>
							</span>
							<div class="myStickyelements-inputs">
								<ul>
									<li>
										<label>
											<input type="radio" name="general-settings[open_tabs_when]" value="hover" <?php checked( @$general_settings['open_tabs_when'], 'hover' );?> />
											<?php _e( 'Hover', 'mystickyelements' );?>
										</label>
									</li>
									<li>
										<label>
											<input type="radio" name="general-settings[open_tabs_when]" value="click" <?php checked( @$general_settings['open_tabs_when'], 'click' );?> />
											<?php _e( 'Click', 'mystickyelements' );?>
										</label>
									</li>
								</ul>
							</div>
						</div>
						
						
						<div class="mystickyelements-content-section-wrap" id="mystickyelements-tab-flyout" style="display:none;">
							<span class="myStickyelements-label" >
							<label>
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("If enabled, first click would open the flyout menu and second click would take users to the actual link", 'mystickyelements'); ?></p>
								</div>
								<?php _e( 'Flyout Option', 'mystickyelements' );?>
							</label>
							
							</span>
							<div class="myStickyelements-inputs">
								<ul>
									<li>
										<label>
											<input type="radio" name="general-settings[flyout]" value="enable" <?php checked( @$general_settings['flyout'], 'enable' );?> />
											<?php _e( 'First click opens the flyout', 'mystickyelements' );?>
										</label>
									</li>
									<li>
										<label>
											<input type="radio" name="general-settings[flyout]" value="disable" <?php checked( @$general_settings['flyout'], 'disable' );?> />
											<?php _e( 'First click opens the link', 'mystickyelements' );?>
										</label>
									</li>
								</ul>
							</div>
						</div>
					
						
					
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<?php _e( 'Font Family', 'mystickyelements' );?></label>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label">
								<select name="general-settings[font_family]" class="form-fonts">
									<option value=""><?php _e( 'Select font family', 'mystickyelements' );?></option>
									<?php $group= ''; foreach( mystickyelements_fonts() as $key=>$value):
												if ($value != $group){
													echo '<optgroup label="' . esc_attr($value) . '">';
													$group = $value;
												}
											?>
										<option value="<?php echo esc_attr($key);?>" <?php selected( @$general_settings['font_family'], $key ); ?>><?php echo esc_attr($key);?></option>
									<?php endforeach;?>
								</select>
								<input type="hidden" name="general-settings[is_select_google_fonts]" id="is_select_google_fonts" value="<?php echo isset($general_settings['is_select_google_fonts']) ? $general_settings['is_select_google_fonts'] : '0'; ?>">
							</div>
						</div>
						
						<div class="mystickyelements-content-section-wrap load-google-fonts-enabled" style="display:<?php echo esc_attr((isset($general_settings['is_select_google_fonts']) && $general_settings['is_select_google_fonts'] == 1) ? 'flex' : 'none');?>;">
							<span class="myStickyelements-label" >
								<label for="myStickyelements-load-google-fonts-enabled">
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("Disabling the feature will stop loading the fonts from  Google's server and you have to manually load the font to make it work", 'mystickyelements'); ?></p>
									</div>
									<?php _e( 'Load Google fonts from Google (By default stays turned on)', 'mystickyelements' );?>
								</label>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label">
								<label for="myStickyelements-load-google-fonts-enable" class="myStickyelements-switch" >
									<input type="checkbox" id="myStickyelements-load-google-fonts-enable" name="general-settings[load_google_fonts]" value="1" <?php checked( @$general_settings['load_google_fonts'], '1' );?>  />
									<span class="slider round"></span>
								</label>
							</div>
						</div>
						
					
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<?php _e( 'Desktop Widget Size', 'mystickyelements' );?>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label">
								<?php $general_settings['widget-size'] = (isset($general_settings['widget-size']) && $general_settings['widget-size']!= '') ? $general_settings['widget-size'] : 'medium'; ?>
								<select id="myStickyelements-widget-size" name="general-settings[widget-size]" >
									<option value="small" <?php selected( @$general_settings['widget-size'], 'small' ); ?>><?php _e( 'Small', 'mystickyelements' );?></option>
									<option value="medium" <?php selected( @$general_settings['widget-size'], 'medium' ); ?>><?php _e( 'Medium', 'mystickyelements' );?></option>
									<option value="large" <?php selected( @$general_settings['widget-size'], 'large' ); ?>><?php _e( 'Large', 'mystickyelements' );?></option>
									<option value="extra-large" <?php selected( @$general_settings['widget-size'], 'extra-large' ); ?>><?php _e( 'Extra Large', 'mystickyelements' );?></option>
								</select>
							</div>
						</div>
					
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<?php _e( 'Mobile Widget size', 'mystickyelements' );?>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label">
								<?php $general_settings['mobile-widget-size'] = (isset($general_settings['mobile-widget-size']) && $general_settings['mobile-widget-size']!= '') ? $general_settings['mobile-widget-size'] : 'medium'; ?>
								<select id="myStickyelements-widget-mobile-size" name="general-settings[mobile-widget-size]" >
									<option value="small" <?php selected( @$general_settings['mobile-widget-size'], 'small' ); ?>><?php _e( 'Small', 'mystickyelements' );?></option>
									<option value="medium" <?php selected( @$general_settings['mobile-widget-size'], 'medium' ); ?>><?php _e( 'Medium', 'mystickyelements' );?></option>
									<option value="large" <?php selected( @$general_settings['mobile-widget-size'], 'large' ); ?>><?php _e( 'Large', 'mystickyelements' );?></option>
								</select>
							</div>
						</div>
					
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<?php _e( 'Entry effect', 'mystickyelements' );?></label>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label">
								<?php $general_settings['entry-effect'] = (isset($general_settings['entry-effect']) && $general_settings['entry-effect']!= '') ? $general_settings['entry-effect'] : 'slide-in'; ?>
								<select id="myStickyelements-entry-effect" name="general-settings[entry-effect]" >
									<option value="none" <?php selected( @$general_settings['entry-effect'], 'none' ); ?>><?php _e( 'None', 'mystickyelements' );?></option>
									<option value="slide-in" <?php selected( @$general_settings['entry-effect'], 'slide-in' ); ?>><?php _e( 'Slide in', 'mystickyelements' );?></option>
									<option value="fade" <?php selected( @$general_settings['entry-effect'], 'fade' ); ?>><?php _e( 'Fade', 'mystickyelements' );?></option>
								</select>
							</div>
						</div>
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip" style="margin-top: 5px;">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("When the visitor opens the website, the selected channel tab or contact form will be opened by default. If the visitor closes the tab, hovers over another channel icon, or visits the channel link it would remain closed.",'mystickyelements');?></p>
								</div>
								<label for="myStickyelements-open_tab_default"><?php _e( 'Open tab by default', 'mystickyelements' );?></label>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label myStickyelements-opentab-default">
								
								<?php 
								$general_settings['opentab-channel'] = (isset($general_settings['opentab-channel']) && $general_settings['opentab-channel']!= '') ? $general_settings['opentab-channel'] : '';
								
								$general_settings['open_tab_default'] = (isset($general_settings['open_tab_default']) && $general_settings['open_tab_default']!= '') ? $general_settings['open_tab_default'] : '';
								unset($social_channels_tabs['is_empty']);
								?>
								<label for="myStickyelements-open_tab_default" class="myStickyelements-switch">
									<input type="checkbox" id="myStickyelements-open_tab_default" name="general-settings[open_tab_default]" <?php checked(@$general_settings['open_tab_default'],'1') ?> value="1" >
									<span class="slider round"></span>
								</label>
								
								
								<select id="myStickyelements-opentab-channel" name="general-settings[opentab-channel]" <?php if(!isset($general_settings['open_tab_default']) || $general_settings['open_tab_default'] != 1 ):?> style="display:none;"<?php endif;?>>
									<option value=""><?php _e( 'Select open channel', 'mystickyelements' );?></option>
									<option value="contact-form" 
										<?php selected( @$general_settings['opentab-channel'], 'contact-form' ); ?> 
										<?php if(isset($contact_form['enable']) && $contact_form['enable'] != 1):?> style="display:none;" <?php endif;?>
										>
										<?php _e( 'Contact Form', 'mystickyelements' );?>
									</option>
									<?php if( !empty($social_channels_tabs)):?>
										<?php foreach( $social_channels_tabs as $tab_key=>$tab_value): ?>
										<option value="<?php echo esc_attr($tab_key);?>" <?php selected( @$general_settings['opentab-channel'], $tab_key ); ?>><?php echo esc_html($tab_value['hover_text']);?></option>
										<?php endforeach; ?>
									<?php endif;?>
								</select>
							</div>
						</div>
							
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<label for="myStickyelements-inputs-attention-effect">
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p>
											<img src="<?php echo MYSTICKYELEMENTS_URL ?>/images/mse_attention_effects.gif"  alt="MSE Attention Effects" />
											<br>
											<br>
											<?php esc_html_e("Grab your visitors' attention and increase interaction rates with subtle animations. Choose how your widget appears when the page loads.", 'mystickyelements'); ?>
											
											
											
										</p>
									</div>
									<?php _e( 'Attention Effect', 'mystickyelements' );?>
								</label>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
								
							</span>
							<div class="myStickyelements-inputs inputs-attention-effect  myStickyelements-label <?php echo esc_attr($is_pro_active?"is-pro":"not-pro") ?>">
							
								<select id="myStickyelements-inputs-attention-effect" disabled name="general-settings[attention_effect]" >
									<option value=""><?php _e( 'Select Attention Effect', 'mystickyelements' );?></option> 
								</select> 
							</div>
						</div> 
						
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label">
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip" style="margin-top: 5px;">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("Your My Sticky Elements widget will first appear to the user according to the selected trigger. After the widget appeared for the first time, it'll always be visible on-load - once the user is aware of the widget, the user expects it to always appear",'mystickyelements');?></p>
								</div>
								<label for="myStickyelements-time-delay"><?php esc_html_e('Display after', 'mystickyelements'); ?></label>
								
								<?php if (!$is_pro_active) { ?>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php esc_html_e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							<?php } ?>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label myStickyelements-time-delay">	
								<input type="number" name="general-settings[timer_delay_sec]" value="<?php echo esc_attr(( isset($general_settings['timer_delay_sec']) && $general_settings['timer_delay_sec'] != '' ) ? $general_settings['timer_delay_sec'] : '0') ; ?>" disabled="disabled" style="width: 20%">
								&nbsp;<?php esc_html_e('seconds on the page', 'mystickyelements'); ?>
							</div>
							
						</div>
						
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label">
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip" style="margin-top: 5px;">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("Set a scroll percentage after which the widget becomes visible",'mystickyelements');?></p>
								</div>
								<label for="myStickyelements-time-delay"><?php esc_html_e('Visible after scrolling', 'mystickyelements'); ?></label>
								
								<?php if (!$is_pro_active) { ?>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php esc_html_e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							<?php } ?>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label myStickyelements-time-delay">	
								<input type="number" name="general-settings[after_page_scroll]" value="0" disabled="disabled" style="width: 20%">
								&nbsp;<?php esc_html_e('% of the page', 'mystickyelements'); ?>
							</div>
							
						</div>
						
					<!--</div> -->
					<!-- Show On Pages Rules -->
					<div class="show-on-apper page-rules-wrap">
						<div class="myStickyelements-show-on-wrap mystickyelements-content-section-wrap">
							<span class="myStickyelements-label myStickyelements-extra-label">
								<label>
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("Show or don't show the widget on specific pages. You can use rules like contains, exact match, starts with, and ends with. WordPress pages, posts, tags, and categories are also options. If you have a WooCommerce store, you can choose any products or only the products that are currently on sale.", 'mystickyelements'); ?></p>
									</div>
									<?php _e( 'Show on Pages', 'mystickyelements' );?>
								</label>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							</span>
							<div class="myStickyelements-show-on-right">
								<div class="myStickyelements-page-options myStickyelements-inputs"
									 id="myStickyelements-page-options"  style="display: none">
									 <div class="myStickyelements-page-option">
										<div class="url-content">
											<div class="myStickyelements-url-select">
												<select name="" id="url_shown_on_0_option">
													<option value="show_on">Show on</option>
													<option value="not_show_on">Don't show on</option>
												</select>
											</div>
											<div class="myStickyelements-url-option">
												<select class="myStickyelements-url-options"
														id="url_rules___count___option" >
													<option selected="selected" disabled value="">Select Rule
													</option>
													<?php 
													$url_options = array(
															'page_contains'   => esc_html__('Link that contain', "mystickyelements"),
															'page_has_url'    => esc_html__('A specific link', "mystickyelements"),
															'page_start_with' => esc_html__('Links starting with', "mystickyelements"),
															'page_end_with'   => esc_html__('Links ending with', "mystickyelements"),
															'wp_pages'        => esc_html__('WordPress Pages', "mystickyelements"),
															'wp_posts'        => esc_html__('WordPress Posts', "mystickyelements"),
															'wp_categories'   => esc_html__('WordPress Categories', "mystickyelements"),
															'wp_tags'         => esc_html__('WordPress Tags',  "mystickyelements")
														);
													if ( class_exists( 'WooCommerce' ) ) {
														$url_options['wc_products'] = esc_html__('WooCommerce products', "mystickyelements");
														$url_options['wc_products_on_sale'] = esc_html__('WooCommerce products on sale', "mystickyelements");
													}
													foreach ($url_options as $key => $value) {
														echo '<option value="' . esc_attr($key) . '">' . esc_attr($value) . '</option>';
													} ?>
												</select>
											</div>
											<div class="myStickyelements-url-box">												
												<span class='myStickyelements-url url-title active'><?php echo site_url("/"); ?></span>
												<span class='myStickyelements-wp_pages url-title' style="display:none"><?php esc_html_e("Pages", "mystickyelements"); ?></span>
												<span class='myStickyelements-wp_posts url-title' style="display:none"><?php esc_html_e("Posts", "mystickyelements"); ?></span>
												<span class='myStickyelements-wp_categories url-title' style="display:none"><?php esc_html_e("Categories", "mystickyelements"); ?></span>
												<span class='myStickyelements-wp_tags url-title' style="display:none"><?php esc_html_e("Tags", "mystickyelements"); ?></span>
												<span class='myStickyelements-wc_products url-title' style="display:none"><?php esc_html_e("Products", 'mystickyelements'); ?></span>
												<span class='myStickyelements-wc_products_on_sale url-title ' style="display:none"><?php esc_html_e("Products", 'mystickyelements'); ?></span>
											</div>
											<div class="myStickyelements-url-values">
												<input type="text" value="" name="" id="url_rules_0_value"/>
											</div>
											<div class="myStickyelements-url-buttons">
												<a class="myStickyelements-remove-rule"
												   href="javascript:;">x</a>
											</div>
											<div class="clear"></div>
										</div>
										<?php if (!$is_pro_active) { ?>
											<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
										<?php } ?>
									</div>
								</div>
								<a href="javascript:void(0);" class="create-rule" id="create-rule" data-wrap="page-rules-wrap">Add Rule</a>
								<a href="javascript:void(0);" class="create-rule remove-rule" id="remove-page-rules" data-wrap="page-rules-wrap" style="display:none" ><?php esc_html_e( "Remove Rules", "mystickyelements" );?></a>
							</div>
						</div>
					</div>
					<!-- END Show on Pages -->
					
					<!-- Show On Days & Hours -->
					<div class="show-on-apper data-and-time-rule-wrap">
						<div class="myStickyelements-show-on-wrap mystickyelements-content-section-wrap">
							<span class="myStickyelements-label myStickyelements-extra-label">
								<label>
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("Display the widget on specific days and hours based on your opening days and hours", 'mystickyelements'); ?></p>
									</div>
									<?php _e( 'Days and Hours', 'mystickyelements' );?>
								</label>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							</span>
							<div class="myStickyelements-show-on-right">
								<div class="myStickyelements-days-hours-options myStickyelements-inputs" id="myStickyelements-days-hours-options" style="display: none;">
									<div class="myStickyelements-page-option">
										<div class="url-content">
											<div class="myStickyelements-url-select">
												<select id="url_shown_on_0_option">
													<option value="0">Everyday of week</option>
												</select>
											</div>
											<div class="myStickyelements-url-option">
												<label class="myStickyelements-days-hours-label-wrap">
													<span class="myStickyelements-days-hours-label">From</span>
													<input type="text" class=" time-picker ui-timepicker-input timepicker_time"  value="" id="start_time_0" />
												</label>
											</div>
											<div class="myStickyelements-url-box">
												<label class="myStickyelements-days-hours-label-wrap">
													<span class="myStickyelements-days-hours-label">To</span>
													<input type="text" class=" time-picker ui-timepicker-input timepicker_time"  value="" id="end_time_0" />
												</label>
											</div>
											<div class="myStickyelements-url-values">
												<label class="myStickyelements-days-hours-label-wrap">
													<span class="myStickyelements-days-hours-label">Time Zone</span>
													<select class=" gmt-data stickyelement-gmt-timezone gmt-timezone" id="url_shown_on_0_option">
														<option selected="selected" value="">Select a city or country</option>
													</select>
												</label>
											</div>
											<div class="myStickyelements-url-buttons">
												<a class="myStickyelements-remove-rule" href="javascript:;">x</a>
											</div>
											<div class="clear"></div>
										</div>
										<span class="upgrade-myStickyelements">
											<a href="<?php echo esc_url($upgrade_url); ?>" target="_blank">
												<i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?>
											</a>
										</span>
									</div>
								</div>
								<a href="javascript:void(0);" class="create-rule" id="create-data-and-time-rule" data-wrap="data-and-time-rule-wrap"><?php esc_html_e( "Add Rule", "mystickyelements" );?></a>
								<a href="javascript:void(0);" class="create-rule remove-rule" id="remove-data-and-time-rule" data-wrap="data-and-time-rule-wrap" style="display:none" ><?php esc_html_e( "Remove Rules", "mystickyelements" );?></a>
							</div>
						</div>
					</div>
					<!-- END Days and Hours -->
					<!-- Date Scheduling -->
					<div class="mystickyelements-content-section-wrap">
						<span class="myStickyelements-label myStickyelements-extra-label" >
							<label for="traffic-add-other-source">
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e('Set the date and time for when you want the widget to start showing and the time you would like it to stop showing. You can add up to 12 combinations of "on and off" triggers. This feature may be useful when you have an upcoming limited-time offer.', 'mystickyelements'); ?></p>
								</div>
								<?php esc_html_e( "Date Scheduling", 'mystickyelements' );?>
							</label>
							<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
						</span>
						<div class="myStickyelements-show-on-right myStickyelements-inputs">							
							<div id="myStickyelements-date-schedule-options" class=" myStickyelements-label myStickyelements-date-schedule-options date-schedule-options" style="display:none;" >
								
								<div class="myStickyelements-date-schedule-time-zone">
									<label><?php esc_html_e( 'Timezone', 'mystickyelements');?></label>
									<select class="gmt-data myStickyelements-gmt-timezone gmt-timezone" name="general-settings[date_schedule_timezone]" <?php echo !$is_pro_active?"disabled":"" ?>>
										<option selected="selected" value=""><?php esc_html_e( "Select a city or country", 'mystickyelements' );?></option>
									</select>
								</div>
								<div class="myStickyelements-date-schedule-boxes myStickyelements-date-schedules">
									<div class="myStickyelements-date-schedule-box setting-content-relative">
										<div class="date-time-box">
											<div class="date-select-option">
												<label>
													<?php esc_html_e( 'Start date ', 'mystickyelements');?>
													<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
														<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
														<p><?php esc_html_e('Schedule a date from which the widget will be displayed (the starting date is included)',"mystickyelements");?></p>
													</div>
												</label>
												<input autocomplete="off" type="text" class="myStickyelements-datepicker" id="date_schedule___count___start_date">
											</div>
											<div class="time-select-option">
												<label><?php esc_html_e( 'Start time ', 'mystickyelements');?></label>
												<input autocomplete="off" type="text" class="myStickyelements-timepicker" id="date_schedule___count___start_time">
											</div>
										</div>
										<div class="date-time-box">
											<div class="date-select-option">
												<label>
													<?php esc_html_e( 'End date ', 'mystickyelements');?>
													<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
														<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
														<p><?php esc_html_e('Schedule a date from which the widget will stop being displayed (the end date is included)',"mystickyelements");?></p>
													</div>
												</label>
												<input autocomplete="off" type="text" class="myStickyelements-datepicker" id="date_schedule___count___end_date">
											</div>
											<div class="time-select-option">
												<label><?php esc_html_e( 'End time ', 'mystickyelements');?></label>
												<input autocomplete="off" type="text" class="myStickyelements-timepicker" id="date_schedule___count___end_time">
											</div>
											<div class="myStickyelements-url-buttons">
												<a class="myStickyelements-remove-date-schedule" href="javascript:void(0);">x</a>
											</div>
										</div>
									</div>									
								</div>
								<span class="upgrade-myStickyelements">
									<a href="<?php echo esc_url($upgrade_url); ?>" target="_blank">
										<i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?>
									</a>
								</span>
							</div>
							
							<a href="javascript:void(0);" class="create-rule" id="myStickyelements-date-schedling"><?php esc_html_e( "Add Rule", "mystickyelements" );?></a>
							<a href="javascript:void(0);" class="create-rule remove-rule" id="remove-myStickyelements-date-schedling" style="display:none;"><?php esc_html_e( "Remove Rules", "mystickyelements" );?></a>
						</div>
					</div>
					<!-- End Date Scheduling -->
					
				
					
					<!--<div class="more-setting-rows"> -->
						<div class="mystickyelements-content-section-wrap mystickyelements-content-section-wrap">
							<span class="myStickyelements-label myStickyelements-extra-label" >
								<label for="countries_list">
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("Target your widget to specific countries. You can create different widgets for different countries", 'mystickyelements'); ?></p>
									</div>
									<?php _e( "Country targeting", 'mystickyelements' );?>
								</label>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							</span>
							<div class="myStickyelements-inputs myStickyelements-country-inputs <?php echo esc_attr($is_pro_active?"is-pro":"not-pro") ?>">
							
								<button type="button" class="myStickyelements-country-button"><?php _e("All countries", 'mystickyelements'); ?></button>
								<div class="myStickyelements-country-list-box">
									
									<select name="general-settings[countries_list][]" placeholder="Select Country" class="myStickyelements-country-list">
										<option value=""><?php _e("All countries", 'mystickyelements'); ?></option>
									</select>
								</div>
								<span class="upgrade-myStickyelements">
									<a href="<?php echo esc_url($upgrade_url); ?>" target="_blank">
										<i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?>
									</a>
								</span>
							</div>
						</div>
					<!--</div>	-->	
                    <div class="myStickyelements-page-options-html" style="display: none">
                        <div class="myStickyelements-page-option">
                            <div class="url-content">
                                <div class="myStickyelements-url-select">
                                     <select name="general-settings[page_settings][__count__][shown_on]" id="url_shown_on___count___option" <?php echo !$is_pro_active ? "disabled" : "" ?>>
                                         <option value="show_on">Show on</option> 
                                         <option value="not_show_on">Don't show on</option>
                                    </select>
                                </div>
                                <div class="myStickyelements-url-option">
                                    <select class="myStickyelements-url-options" name="general-settings[page_settings][__count__][option]" id="url_rules___count___option" <?php echo !$is_pro_active ? "disabled" : "" ?>>
                                        <option selected="selected" disabled value="">Select Rule</option>
                                        <?php 
                                        $url_options = array(
												'page_contains'   => esc_html__('Link that contain', "mystickyelements"),
												'page_has_url'    => esc_html__('A specific link', "mystickyelements"),
												'page_start_with' => esc_html__('Links starting with', "mystickyelements"),
												'page_end_with'   => esc_html__('Links ending with', "mystickyelements"),
												'wp_pages'        => esc_html__('WordPress Pages', "mystickyelements"),
												'wp_posts'        => esc_html__('WordPress Posts', "mystickyelements"),
												'wp_categories'   => esc_html__('WordPress Categories', "mystickyelements"),
												'wp_tags'         => esc_html__('WordPress Tags',  "mystickyelements")
											);
										if ( class_exists( 'WooCommerce' ) ) {
											$url_options['wc_products'] = esc_html__('WooCommerce products', "mystickyelements");
											$url_options['wc_products_on_sale'] = esc_html__('WooCommerce products on sale', "mystickyelements");
										}
                                        foreach ($url_options as $key => $value) {
                                            echo '<option value="' . esc_attr($key) . '">' . esc_attr($value) . '</option>';
                                        } ?>
                                    </select>
                                </div>
                                <div class="myStickyelements-url-box">
                                    <span class='myStickyelements-url'><?php echo site_url("/"); ?></span>
                                </div>
                                <div class="myStickyelements-url-values">
                                    <input type="text" value=""name="general-settings[page_settings][__count__][value]" id="url_rules___count___value" <?php echo !$is_pro_active ? "disabled" : "" ?> />
                                </div>
                                <div class="myStickyelements-url-buttons">
                                    <a class="myStickyelements-remove-rule" href="javascript:void(0);">x</a>
                                </div>
                                <div class="clear"></div>
                            </div>
                             <?php if (!$is_pro_active) { ?>
                                <span class="upgrade-myStickyelements">
                                    <a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a>
                                </span>
                            <?php } ?>
                        </div>
                    </div>
					<div class="mystickyelements-content-section-wrap">
						<p class="next show-on-apper mystickyelements-more-setting-btn" id="next-show-on-apper">
							<button type="submit" name="more" id="btn-more" class="button button-primary"><?php _e('More Settings', 'mystickyelements');?>&nbsp;&nbsp;<i class="fas fa-angle-down"></i></button>
						</p>
					</div>
					<div class="more-setting-rows">
						<div class="myStickyelements-position-desktop-wrap" style="<?php echo esc_attr((isset($general_settings['position']) && $general_settings['position'] == 'bottom') ? 'display: none;' : ''); ?>">
							<div class="mystickyelements-content-section-wrap">
								<span class="myStickyelements-label" >
									<label for="custom_position"><?php _e( 'On-Screen Position Y Desktop', 'mystickyelements' );?></label>
									<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
								</span>
								<div class="myStickyelements-inputs">
									<div class="px-wrap px-wrap-left">
										<input type="number" id="custom_position" name="general-settings[custom_position]" value="<?php echo @$general_settings['custom_position']; ?>" placeholder="[optional]" disabled />
										<span class="input-px">PX</span>
									</div>
									<div class="px-wrap px-wrap-right">
										<select name="general-settings[custom_position_from]" >
											<option value="bottom">From bottom</option>
											<option value="top">From top</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="more-setting-rows">	
						<div class="myStickyelements-position-mobile-wrap" style="<?php echo esc_attr((isset($general_settings['position_mobile']) && ($general_settings['position'] == 'bottom' || $general_settings['position'] == 'top' )) ? 'display: none;' : ''); ?>">
							<div class="mystickyelements-content-section-wrap">
								<span class="myStickyelements-label" >
									<label for="custom_position_mobile"><?php _e( 'On-Screen Position Y Mobile', 'mystickyelements' );?></label>
									<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
								</span>

								<div class="myStickyelements-inputs">
									<div class="px-wrap px-wrap-left">
										<input type="number" id="custom_position_mobile"  name="general-settings[custom_position_mobile]" value="<?php echo @$general_settings['custom_position_mobile'];?>" placeholder="[optional]" disabled />
										<span class="input-px">PX</span>
									</div>
									<div class="px-wrap px-wrap-right">
										<select name="general-settings[custom_position_from_mobile]">
											<option value="bottom" >From bottom</option>
											<option value="top" >From top</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="more-setting-rows">
						<div class="mystickyelements-content-section-wrap" id="mystickyelements-tab-hover-bebahvior" >
							<span class="myStickyelements-label" >
							<label>
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("When turned on (on mobile), the first tap, will show the hover text first and it'll stay until the second tap", 'mystickyelements'); ?></p>
								</div>
								<?php _e( 'Improved mobile behavior', 'mystickyelements' );?>
							</label>
							
							</span>
							<div class="myStickyelements-inputs">
								<ul>
									<li>
										<label>
											<input type="radio" name="general-settings[mobile_behavior]" value="disable" <?php checked( @$general_settings['mobile_behavior'], 'disable' );?> />
											<?php _e( 'First tap opens link', 'mystickyelements' );?>
										</label>
									</li>
									<li>
										<label>
											<input type="radio" name="general-settings[mobile_behavior]" value="enable" <?php checked( @$general_settings['mobile_behavior'], 'enable' );?> />
											<?php _e( 'First tap opens flyout', 'mystickyelements' );?>
										</label>
									</li>
									
								</ul>
							</div>
						</div>
					</div>
					<div class="more-setting-rows">
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<label for="myStickyelements-google-alanytics-enabled">
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("If enabled, you can track clicks to your widget using ", 'mystickyelements'); ?><a href='https://premio.io/help/mystickyelements/how-do-i-track-clicks-using-google-analytics/' target='_blank'><?php esc_html_e("Google Analytics","mystickyelements"); ?></a></p>
									</div>
									<?php _e( 'Google Analytics Events', 'mystickyelements' );?>
								</label>
								
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label">
								<label for="myStickyelements-google-alanytics-enabled" class="myStickyelements-switch" >
									<input type="checkbox" id="myStickyelements-google-alanytics-enabled"name="general-settings[google_analytics]" value="1" <?php checked(@$general_settings['google_analytics'], '1'); ?>disabled />
									<span class="slider round"></span>
								</label>
							</div>
						</div>
					</div>
						<!-- Traffic Source -->
					<div class="more-setting-rows">
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label myStickyelements-extra-label" >
								<label for="traffic-add-other-source">
									<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
										<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
										<p><?php esc_html_e("Show the widget only to visitors who come from specific traffic sources including direct traffic, social networks, search engines, Google Ads, or any other traffic source", 'mystickyelements'); ?></p>
									</div>
									<?php _e( "Traffic source", 'mystickyelements' );?>
								</label>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							</span>
							<div class="myStickyelements-show-on-right myStickyelements-inputs myStickyelements-traffic-source-right">
								<div class=" myStickyelements-label myStickyelements-traffic-source-inputs traffic-source-option not-pro" style="display:none;">
									<div class="traffic-direct-source clear">
										<label class="myStickyelements-switch">
											<input type="checkbox" id="myStickyelements-direct-traffic-source" value="1"  disabled />
											<span class="slider round"></span>
										</label>
										<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
											<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
											<p><?php esc_html_e("Show the widget to visitors who arrived to your website from direct traffic", 'mystickyelements'); ?></p>
										</div>
										<label for="myStickyelements-direct-traffic-source">
											Direct visit
											
										</label>
									</div>
									<br />
									<div class="traffic-social-network-source clear">
										<label class="myStickyelements-switch">
											<input type="checkbox" id="myStickyelements-social-network-traffic-source" value="1" disabled />
											<span class="slider round"></span>
										</label>
										<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
											<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
											<p><?php esc_html_e("Show the widget to visitors who arrived to your website from social networks including: Facebook, Twitter, Pinterest, Instagram, Google+, LinkedIn, Delicious, Tumblr, Dribbble, StumbleUpon, Flickr, Plaxo, Digg and more", 'mystickyelements'); ?></p>
										</div>
										<label for="myStickyelements-social-network-traffic-source">
											Social networks
											
										</label>
									</div>
									<br />
									<div class="traffic-search-engines-source clear">
										<label class="myStickyelements-switch">
											<input type="checkbox" id="myStickyelements-search-engines-traffic-source" value="1" disabled />
											<span class="slider round"></span>
										</label>
										<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
											<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
											<p><?php esc_html_e("Show the widget to visitors who arrived from search engines including: Google, Bing, Yahoo!, Yandex, AOL, Ask, WOW,  WebCrawler, Baidu and more", 'mystickyelements'); ?></p>
										</div>
										<label for="myStickyelements-search-engines-traffic-source">
											Search engines
											
										</label>
									</div>
									<br />
									<div class="traffic-google-ads-source clear">
										<label class="myStickyelements-switch">
											<input type="checkbox" id="myStickyelements-google-ads-traffic-source" value="1" disabled />
											<span class="slider round"></span>
										</label>
										<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
											<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
											<p><?php esc_html_e("Show the widget to visitors who arrived from search engines including: Google, Bing, Yahoo!, Yandex, AOL, Ask, WOW,  WebCrawler, Baidu and more", 'mystickyelements'); ?></p>
										</div>
										<label for="myStickyelements-google-ads-traffic-source">
										
											Google Ads
											
										</label>
									</div>
									<br />
									<div class="traffic-other-source clear">
										<div class="other-source-features clear">
											<table id="custom-traffic-source-lists" width="100%">
												<thead>
													<tr>
														<th colspan="3">Specific URL</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>
															<select disabled >
																<option value="contain" >Contains</option>
																<option value="not_contain" >Not contains</option>
															</select>
														</td>
														<td>
															<input type="text" value="" placeholder="http://www.example.com" disabled />
														</td>
														<td>
															<div class="day-buttons">
															</div>
														</td>
													</tr>
												</tbody>
											</table>							
										</div>
									</div>
									<span class="upgrade-myStickyelements">
										<a href="<?php echo esc_url($upgrade_url); ?>" target="_blank">
											<i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?>
										</a>
									</span>
								</div>
								<a href="javascript:void(0);" class="traffic-add-other-source create-rule" id="traffic-add-other-source"><?php esc_html_e( "Add Rule", "mystickyelements" );?></a>
								<a href="javascript:void(0);" class="create-rule remove-rule" id="remove-traffic-add-other-source"  style="display:none"><?php esc_html_e( "Remove Rules", "mystickyelements" );?></a>
							</div>
						</div>
					</div> 
					<!-- END Traffic Source -->
					<div class="more-setting-rows">
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("The form will automatically open up on page load until the user closes the form or fills out the form", 'mystickyelements'); ?></p>
								</div>
								<label for="myStickyelements-form_open_automatic"><?php _e( 'Open the form automatically', 'mystickyelements' );?></label>
							</span>
							
							<div class="myStickyelements-inputs myStickyelements-label myStickyelements-form-open">
								<label for="myStickyelements-form_open_automatic" class="myStickyelements-switch" >
									<input type="checkbox" id="myStickyelements-form_open_automatic" name="general-settings[form_open_automatic]"<?php checked( @$general_settings['form_open_automatic'], '1' );?>  value="1" />
									<span class="slider round"></span>
								</label>
								&nbsp;
								<span class="input-form-open-automatic-sec input-line-section" style="margin-left: 10px;margin-top: -8px; display:<?php echo ( isset($general_settings['form_open_automatic']) && $general_settings['form_open_automatic'] == 1 ) ? 'inline-block' : 'none';?>">
									<?php esc_html_e("Display after ",'mystickyelements');?>
									<input type="number" style="width: 70px;"  name="general-settings[form_open_delay_sec]"  min="0" value="<?php echo ( isset($general_settings['form_open_delay_sec']) && $general_settings['form_open_delay_sec'] != '' ) ? $general_settings['form_open_delay_sec'] : '0' ; ?>">
									<?php esc_html_e(" seconds on the page",'mystickyelements'); ?>
								</span>
							</div>
						</div>
					</div>
					<div class="more-setting-rows">
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label">
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("If enabled, a small black button will appear on top to minimize the widget", 'mystickyelements'); ?></p>
								</div>
								<label for="myStickyelements-minimize-tab">
									<?php esc_html_e( 'Minimize tab', 'mystickyelements' );?>
								</label>
							</span>
							<div class="myStickyelements-inputs myStickyelements-label myStickyelements-minimize-tab">
								<label for="myStickyelements-minimize-tab" class="myStickyelements-switch" >
									<input type="checkbox" id="myStickyelements-minimize-tab" name="general-settings[minimize_tab]"<?php checked( @$general_settings['minimize_tab'], '1' );?>  value="1" />
									<span class="slider round"></span>
								</label>
								&nbsp;
								<input type="text" id="minimize_tab_background_color" name="general-settings[minimize_tab_background_color]" class="mystickyelement-color" value="<?php echo esc_attr($general_settings['minimize_tab_background_color']);?>" />
							</div>
						</div>
					</div>
					<div class="myStickyelements-minimized more-setting-rows">
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label">
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("If enabled, the widget will be hidden by default and will show an icon instead to restore to its full size", 'mystickyelements'); ?></p>
								</div>
								<label>
									<?php esc_html_e( 'Minimized bar on load', 'mystickyelements' );?>
								</label>
							</span>
							<div class="myStickyelements-inputs">
								<ul>
									<li>
										<label>
											<input type="checkbox" name="general-settings[minimize_desktop]" value="desktop" <?php checked( @$general_settings['minimize_desktop'], 'desktop' );?> />
											<?php _e( 'Desktop', 'mystickyelements' );?>
										</label>
									</li>
									<li>
										<label>
											<input type="checkbox" name="general-settings[minimize_mobile]" value="mobile" <?php checked( @$general_settings['minimize_mobile'], 'mobile' );?> />
											<?php _e( 'Mobile', 'mystickyelements' );?>
										</label>
									</li>
								</ul>
							</div>
						</div>
					</div>
					
					<div class="more-setting-rows">
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("Write custom CSS to customize the tabs", 'mystickyelements'); ?></p>
								</div>
								<label for="general-settings-tabs-css"><?php _e( 'Tabs CSS', 'mystickyelements' );?></label>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							</span>
							<div class="myStickyelements-inputs">
								<textarea  <?php echo !$is_pro_active?"disabled":"" ?> name="general-settings[tabs_css]" rows="5" cols="50" id="general-settings-tabs-css" class="code" placeholder=".example { background-color: green;}"><?php echo esc_attr((isset($general_settings['tabs_css'])) ? stripslashes($general_settings['tabs_css']) : '');?></textarea>
							</div>
						</div>
					</div>
					 
					<div class="more-setting-rows">
						<div class="mystickyelements-content-section-wrap">
							<span class="myStickyelements-label" >
								<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
									<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
									<p><?php esc_html_e("Write custom CSS to customize the form", 'mystickyelements'); ?></p>
								</div>
								<label> <?php _e('Form CSS','mystickyelements');?></label>
								<span class="upgrade-myStickyelements"><a href="<?php echo esc_url($upgrade_url); ?>" target="_blank"><i class="fas fa-lock"></i><?php _e('UPGRADE NOW', 'mystickyelements'); ?></a></span>
							</span>

							 <div class="myStickyelements-inputs">
								<textarea  <?php echo !$is_pro_active?"disabled":"" ?> name="general-settings[form_css]" rows="5" cols="50" id="general-settings-form-css" class="code" placeholder=".example { background-color: green;}"><?php echo esc_attr(( isset($general_settings['form_css'])) ? stripslashes($general_settings['form_css']) : '');?></textarea>
							</div>
						</div>
					</div>
								
                    
					<div class="mystickyelements-more-setting-btn mystickyelements-less-setting-btn">
						<button type="submit" name="less" id="btn-less" class="button button-primary" style="display:none;"><?php _e('Less Settings', 'mystickyelements');?>&nbsp;&nbsp;<i class="fas fa-angle-up"></i></button>
					</div>
				</div>
				<input type="hidden" id="myStickyelements_site_url" value="<?php echo site_url("/") ?>" >
				
			</div>
			
		</div> 
	</div>
</div>

