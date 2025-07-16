
<?php
/**
 * MSE Admin Preview
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

if (defined('ABSPATH') === false) {
    exit;
}


?>
<div class="mystickyelements-preview" <?php if( isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-live-chatway' ):?> style="display:none;" <?php endif;?>>
	<div class="myStickyelements-preview-tab">
		<div class="myStickyelements-preview-screen">
			<style>
				.preview-contactform-field::placeholder  {
					color:  <?php echo $general_settings['placeholder_color']; ?>;
				}
			</style>
			<div class="preview-element-contact-form <?php echo esc_attr($general_settings['position'])?>_pos  <?php echo esc_attr($general_settings['position_mobile'])?>_pos_mobile  ">
                <div class="preview-element-contact-form-wrap">

                    <h2 style="color:<?php if((isset($contact_form['headine_text_color']) )){ echo esc_attr($contact_form['headine_text_color']);} ?>"><?php echo esc_html($contact_form['contact_title_text']); ?></h2>
					<div class="preview-element-contact-form-wrap-div">
                    <input type="text" id="contact-form-name" class = "preview-contactform-field" placeholder="<?php echo (isset($contactform['name_value']) && $contactform['name_value']!= '' ) ? $contactform['name_value'] : 'Name';?>" data-slug = "name" style ="<?php if( !isset($contact_form['name']) || (isset($contact_form['name']) && $contact_form['name'] != '1' )){echo "display:none";} else{echo "display:block";} ?>" >
        
                    <input type="text" id="contact-form-phone" class = "preview-contactform-field" placeholder="<?php echo (isset($contactform['phone_value']) && trim($contactform['phone_value'])!= '' ) ? $contactform['phone_value'] : 'Phone';?>" data-slug="phone" style ="<?php if( !isset($contact_form['phone']) || (isset($contact_form['phone']) && $contact_form['phone'] != '1' )){echo "display:none";} else{echo "display:block";} ?>">

                    <input type="text" id="contact-form-email" class = "preview-contactform-field" placeholder="<?php echo (isset($contactform['email_value']) && $contactform['email_value']!= '' ) ? $contactform['email_value'] : 'Email';?>" data-slug="email" style ="<?php if( !isset($contact_form['email']) || (isset($contact_form['email']) && $contact_form['email'] != '1' )){echo "display:none";} else{echo "display:block";} ?>">

                    <textarea  class = "preview-contactform-field" id="contact-form-message" placeholder="Message"  data-slug="message" style ="<?php if( !isset($contact_form['message']) || (isset($contact_form['message']) && $contact_form['message'] != '1' )){echo "display:none";} else{echo "display:block";} ?>"></textarea>
                    
                    <select name="contact-form[dropdown_value]" id="contact-form-dropdown-value" style ="<?php if( !isset($contact_form['dropdown']) || (isset($contact_form['dropdown']) && $contact_form['dropdown'] != '1' )){echo "display:none";} else{echo "display:block";} ?>">
                        <option value=""><?php if((isset($contact_form['dropdown-placeholder']) )){echo esc_attr($contact_form['dropdown-placeholder']);}else{echo "Submit";}?></option>
                    </select>
                    
                    <input type="submit" id="preview-stickyelements-submit-form" style="background:<?php if((isset($contact_form['submit_button_background_color']) )){echo esc_attr($contact_form['submit_button_background_color']);}else{echo "#7761DF";}?>; color:<?php if((isset($contact_form['submit_button_text_color']) )){echo esc_attr($contact_form['submit_button_text_color']);}else{echo "#FFFFFF";}?>;" value="<?php if((isset($contact_form['submit_button_text']) )){echo esc_attr($contact_form['submit_button_text']);}else{echo "Submit";}?>" disabled>
					</div>	
                </div>
            </div>

			<div class="mystickyelements-fixed  <?php echo esc_attr((isset($contact_form['direction']) && $contact_form['direction'] == "RTL")?"is-rtl":"") ?> mystickyelements-position-<?php echo esc_attr($general_settings['position'])?> <?php echo esc_attr((isset($general_settings['position_on_screen']) && $general_settings['position_on_screen']!= '') ? 'mystickyelements-position-screen-' .$general_settings['position_on_screen'] : 'mystickyelements-position-screen-center');?> mystickyelements-position-mobile-<?php echo esc_attr($general_settings['position_mobile'])?> <?php echo esc_attr((isset($general_settings['widget-size']) && $general_settings['widget-size']!= '') ? 'mystickyelements-size-' .$general_settings['widget-size'] : 'mystickyelements-size-medium');?> <?php echo esc_attr((isset($general_settings['mobile-widget-size']) && $general_settings['mobile-widget-size']!= '') ? 'mystickyelements-mobile-size-' .$general_settings['mobile-widget-size'] : 'mystickyelements-mobile-size-medium');?> <?php echo esc_attr((isset($general_settings['entry-effect']) && $general_settings['entry-effect']!= '') ? 'mystickyelements-entry-effect-' .$general_settings['entry-effect'] : 'mystickyelements-entry-effect-slide-in');?> <?php echo esc_attr((isset($general_settings['templates']) && $general_settings['templates']!= '') ? 'mystickyelements-templates-' .$general_settings['templates'] : 'mystickyelements-templates-default');?> ">
				<ul class="myStickyelements-preview-ul <?php if ( !isset($general_settings['minimize_tab'])) :?>remove-minimize <?php endif;?> ">
					<?php if ( isset($general_settings['minimize_tab'])) :?>
					<li class="mystickyelements-minimize">
						<span class="mystickyelements-minimize minimize-position-<?php echo esc_attr($general_settings['position'])?> minimize-position-mobile-<?php echo esc_attr($general_settings['position_mobile'])?>"  <?php if (isset($general_settings['minimize_tab_background_color']) && $general_settings['minimize_tab_background_color'] != ''): ?>style="background: <?php echo esc_attr($general_settings['minimize_tab_background_color']); ?>" <?php endif;
						?>>
						<?php
						if ( $general_settings['position'] == 'left' ) :
							echo "&larr;";
						endif;
						if( $general_settings['position'] == 'right' ):
							echo "&rarr;";
						endif;
						if( $general_settings['position'] == 'bottom' ):
							echo "&darr;";
						endif;
						?>
						</span>
					</li>
					<?php endif;?>
					<li id="myStickyelements-preview-contact" class="mystickyelements-contact-form element-desktop-on element-mobile-on <?php if (!isset($contact_form['enable'])) : ?> mystickyelements-contact-form-hide <?php endif; ?>" <?php if ( !isset($contact_form['enable'])) : ?> style="display:none;" <?php endif;?>>
					<?php
					$contact_form_text_class = '';
					if ($contact_form['text_in_tab'] == '') {
						$contact_form_text_class = "mystickyelements-contact-notext";
					}?>
						<span class="mystickyelements-social-icon <?php echo esc_attr($contact_form_text_class)?>" style="background-color: <?php echo esc_attr($contact_form['tab_background_color']);?>; color: <?php echo esc_attr($contact_form['tab_text_color']);?>;">
							<i class="far fa-envelope"></i><?php echo isset($contact_form['text_in_tab'])?$contact_form['text_in_tab']:"Contact Us";?>
						</span>
					</li>
					<?php
					if (!empty($social_channels_tabs) && !isset($social_channels_tabs['is_empty'])) {
						foreach( $social_channels_tabs as $key=>$value) {
							
							if (  strpos($key, 'custom_channel') !== false || strpos($key, 'custom_shortcode') !== false ) {
								$custom_channel_key_temp = '';
								if( strpos($key, 'custom_channel') !== false){
									$custom_channel_key_temp = $key;
									$key = 'custom_channel';
								} 
								if( strpos($key, 'custom_shortcode') !== false){
									$custom_channel_key_temp = $key;
									$key = 'custom_shortcode';
								} 
								
								$social_channels_lists = mystickyelements_custom_social_channels();
								$social_channels_list  = $social_channels_lists[$key];
								
								if ( isset($value['channel_type']) && $value['channel_type'] != '' && $value['channel_type'] != 'custom') {
									$custom_social_channels_lists = mystickyelements_social_channels();
									$social_channels_list['class'] = $custom_social_channels_lists[$value['channel_type']]['class'];
									$social_channels_list['fontawesome_icon'] = $custom_social_channels_lists[$value['channel_type']]['class'];
									if ( isset($custom_social_channels_lists[$value['channel_type']]['custom_svg_icon'])) {														
										$social_channels_list['custom_svg_icon'] = $custom_social_channels_lists[$value['channel_type']]['custom_svg_icon'];
									}
								}
								
								if ( $custom_channel_key_temp != '') {
									$key = $custom_channel_key_temp;
								}
					
							} else {
								$social_channels_lists 	= mystickyelements_social_channels();
								$social_channels_list 	= (isset( $social_channels_lists[$key] )) ? $social_channels_lists[$key] : '';
								if( empty( $social_channels_list ) ) {
									return;
								}
							}
							if ( empty($value)) {
								$value['bg_color'] = $social_channels_list['background_color'];
							}
							$element_class = '';
							if (isset($value['desktop']) && $value['desktop'] == 1) {
								$element_class .= ' element-desktop-on';
							}
							if (isset($value['mobile']) && $value['mobile'] == 1) {
								$element_class .= ' element-mobile-on';
							}
							$value['is_locked'] = (isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && !$is_pro_active)?1:0;
							$social_channels_list['class'] = isset($social_channels_list['class']) ? $social_channels_list['class'] : '';
							?>
							<li id="mystickyelements-social-<?php echo esc_attr($key);?>" class="mystickyelements-social-<?php echo esc_attr($key);?> mystickyelements-social-preview  <?php echo esc_attr($element_class);?>"  >
								<?php
								/*diamond template css*/
								if ( isset($value['bg_color']) && $value['bg_color'] != '' ) {
									?>
									<style>
										.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-bottom.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>,.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-bottom.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>,
										.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-top.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>,.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-top.mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?> {
											background-color: <?php echo esc_attr($value['bg_color']); ?> !important;
										}
										<?php
										if( isset($general_settings['templates']) && $general_settings['templates'] == 'diamond' ) {
										?>
											.mystickyelements-templates-diamond li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo esc_attr($value['bg_color']); ?>;
											}
										<?php
										}
										if( isset($general_settings['templates']) && $general_settings['templates'] == 'arrow' ) {
										?>
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-position-left.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-left-color: <?php echo esc_attr( $value['bg_color']); ?>;
											}
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-position-right.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-right-color: <?php echo esc_attr( $value['bg_color']); ?>;
											}
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-position-bottom.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-bottom-color: <?php echo esc_attr( $value['bg_color']); ?>;
											}
											.myStickyelements-preview-screen.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-left.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-left-color: <?php echo esc_attr( $value['bg_color']); ?>;
											}
											.myStickyelements-preview-screen.myStickyelements-preview-mobile-screen .mystickyelements-position-mobile-right.mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												border-right-color: <?php echo esc_attr( $value['bg_color']); ?>;
											}
											<?php if( $key == 'insagram' ) { ?>
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo esc_attr( $value['bg_color']); ?>;
											}
											.myStickyelements-preview-screen.myStickyelements-preview-mobile-screen .mystickyelements-templates-arrow li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo esc_attr( $value['bg_color']); ?>;
											}
											<?php } ?>
										<?php
										}
										if( isset($general_settings['templates']) && $general_settings['templates'] == 'triangle' ) {
										?>
											.myStickyelements-preview-screen:not(.myStickyelements-preview-mobile-screen) .mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo esc_attr( $value['bg_color']); ?>;
											}
											.myStickyelements-preview-screen.myStickyelements-preview-mobile-screen .mystickyelements-templates-triangle li:not(.mystickyelements-contact-form) span.mystickyelements-social-icon.social-<?php echo esc_attr($key);?>::before {
												background: <?php echo esc_attr( $value['bg_color']); ?>;
											}
										<?php
										}
										?>
									</style>
									<?php
								}
								$channel_type = (isset($value['channel_type'])) ? $value['channel_type'] : '';
								$social_channels_list['class'] = isset($social_channels_list['class']) ? $social_channels_list['class'] : '';
									
								if( !isset($value['bg_color']) ){
									$value['bg_color'] = $social_channels_list['background_color'];
								}
								$channel_class	= (isset($social_channels_list['channel_class'])) ? $social_channels_list['channel_class'] : '';
								?>
								<span class="mystickyelements-social-icon <?php echo esc_attr($channel_class);?> social-<?php echo esc_attr($key);?> social-<?php echo esc_attr($channel_type); ?>" style="background: <?php echo esc_attr($value['bg_color']);?>">
									<?php
								if (isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && $value['custom_icon'] != '' && $value['fontawesome_icon'] == '') :?>
									<img class="<?php echo ( isset($value['stretch_custom_icon']) && $value['stretch_custom_icon'] == 1 ) ? 'mystickyelements-stretch-custom-img' : '';  ?>" src="<?php echo esc_url($value['custom_icon']);?>" width="40" height="40" />
							   <?php else :
								   if (isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && $value['fontawesome_icon'] != '') {
									   $social_channels_list['class'] = $value['fontawesome_icon'];
								   }

								   if (isset($social_channels_list['custom_svg_icon']) && $social_channels_list['custom_svg_icon'] != '') {
									   echo $social_channels_list['custom_svg_icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								   } else {
										?>
									<i class="<?php echo esc_attr($social_channels_list['class']);?>" <?php if (isset($value['icon_color']) && $value['icon_color'] != '') :
										echo "style='color:".$value['icon_color']."'";
								   endif; ?>></i>    
									   <?php
								   }
							   endif;											
									$icon_text_size = "display: none;";
									$value['icon_text'] = ( isset($value['icon_text']) && $value['icon_text'] != '' ) ? $value['icon_text'] : '';
									
									if ( isset($value['icon_text']) && $value['icon_text'] != '' && isset($general_settings['templates']) && $general_settings['templates'] == 'default' ) {
										$icon_text_size .= "display: block;";
										if ( isset($value['icon_text_size']) && $value['icon_text_size'] != '') {
											$icon_text_size .= "font-size: " . esc_attr($value['icon_text_size']) . "px;";
										}
										if (isset($value['icon_text_color']) && $value['icon_text_color'] != '') {
										   $icon_text_size .= "color: ".$value['icon_text_color'];
									   }
									}
									echo "<span class='mystickyelements-icon-below-text' style='".esc_attr($icon_text_size)."'>" . esc_attr($value['icon_text']) . "</span>";
									if ( $key == 'line') {
										echo "<style>.mystickyelements-social-icon.social-". esc_attr($key) ." svg .fil1{ fill:" .esc_attr($value['icon_color']). "}</style>";
									}
									if ($key == 'qzone') {
										echo "<style>.mystickyelements-social-icon.social-".$key." svg .qzone-fill{ fill:".$value['icon_color']."}</style>";
									}
									if ($key == 'instagram_dm') {
										echo "<style>.mystickyelements-social-icon.social-".$key." svg .fil2{ fill:".$value['icon_color']."}</style>";
									}
									?>
								</span>
							</li>
							<?php
						}
					}
					?>
				</ul>
			</div>
		</div>
		<p class="description" id="myStickyelements_mobile_templete_desc" style="display: none;">
			<strong><?php esc_html_e( 'The default template is the only template that is currently available for the mobile bottom position', 'mystickyelements');?></strong>
		</p>
		<div class="mystickyelements-preivew-below-sec" data-id="<?php if(isset($is_widgest_create)) : echo esc_attr($is_widgest_create); endif;?>">
			<div class="myStickyelements-header-title">
				<h3><?php _e('Live Preview', 'mystickyelements'); ?>				
				</h3>
				<span class="myStickyelements-preview-window">
					<ul>
						<li class="preview-desktop preview-active"><i class="fas fa-desktop"></i></li>
						<li class="preview-mobile"><i class="fas fa-mobile-alt"></i></li>
					</ul>
				</span>
			</div>			
		</div>	
	</div>
</div>

<div class="mystickyelements-action-popup-open  mystickyelements-action-popup-status" id="mystickyelements-load-google-enable-popup" style="display:none;">
	
	<div class="popup-ui-widget-header">
		<span id="ui-id-1" class="ui-dialog-title"><?php esc_html_e("Are you sure?",'mystickyelements')?></span>
		<span class="close-dialog" data-id="0" data-from='load-google-fonts'>&#10006</span>
	</div>
	<div id="widget-delete-confirm" class="ui-widget-content">
		<p><?php _e("You're about to turn off loading Google fonts from Google server. By turning it off, fonts will not be loaded by default and you have to manually load them to use properly. Are you sure?",'mystickyelements');?></p>
	</div>
	
	<div class="popup-ui-dialog-buttonset"><button type="button" class="mystickyelement-cancel-widget-btn" id="mystickyelement-disable-loadfonts"  data-popupfrom=""><?php esc_html_e("Disable anyway",'mystickyelements');?></button><button type="button" class="mystickyelement-btn-orange mystickyelement-btn-ok" id="mystickyelement-button-keep-loadfonts"><?php esc_html_e('Keep using','mystickyelements');?></button></div>
</div>
<div id="mystickyelement-load-google-popup-overlay" class="stickyelement-overlay" style="display:none;"></div>
<div class="mystickyelements-action-popup-open mystickyelements-missing-link-popup mystickyelements-action-popup-status" id="mystickyelements-missing-link-popup" style="display:none;">
											
	<div class="popup-ui-widget-header">
		<span id="ui-id-1" class="ui-dialog-title"><?php esc_html_e('Missing link','mystickyelements')?></span>
		<span class="close-dialog" data-id="0" data-from='widget-social-link'>&#10006</span>
	</div>
	<div id="widget-delete-confirm" class="ui-widget-content">
		<p>Please fill out the link information for all the selected channels</p>
	</div>
	
	<div class="popup-ui-dialog-buttonset"><button type="button" class="mystickyelement-cancel-widget-btn mystickyelement-dolater-widget-btn"  data-popupfrom=""><?php esc_html_e("I'll do it later",'mystickyelements');?></button><button type="button" class="mystickyelement-btn-orange mystickyelement-btn-ok"><?php esc_html_e('Ok','mystickyelements');?></button></div>
</div>
<div id="mystickyelement-missing-link-overlay" class="stickyelement-overlay" style="display:none;"></div>
