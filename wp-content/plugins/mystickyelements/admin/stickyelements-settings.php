<?php
$mystickyelement_class =  ( isset($_GET['page']) && $_GET['page'] == 'my-sticky-elements-settings' && !isset($_GET['widget']) ) ? 'mystickyelement-wrap-default' : '' ;
?>
<div class="wrap mystickyelement-wrap <?php echo esc_attr($mystickyelement_class); ?>">
	<h2 class="mystickyelement-empty-h2" style="font-size: 0px;margin-bottom: 0px;"></h2>
	<div class="mystickyelements-wrap">
		<form class="mystickyelements-form" method="post" action="#">
			 <?php			 
				$active_step1_class = '';
				$completed_step1_class = '';
				$active_step2_class = '';
				$completed_step2_class = '';
				$active_step3_class = '';
				$completed_step3_class = '';
				$active_step4_class = '';
				if(isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-contact-form'){

					$active_step1_class = 'active';
					$completed_step1_class = '';
					$active_step2_class = '';
					$completed_step2_class = '';
					$active_step3_class = '';
					$completed_step3_class = '';
					$active_step4_class = '';
				}
				else if( isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-social-media' ){
					$active_step1_class = '';
					$completed_step1_class = 'completed';
					$active_step2_class = 'active';
					$completed_step2_class = '';
					$active_step3_class = '';
					$completed_step3_class = '';
					$active_step4_class = '';
				}
				else if( isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-display-settings' ){
					$active_step1_class = '';
					$completed_step1_class = 'completed';
					$active_step2_class = '';
					$completed_step2_class = 'completed';					
					$active_step3_class = 'active';
					$completed_step3_class = '';
					$active_step4_class = '';
				}
				else{
					$active_step1_class = '';
					$completed_step1_class = 'completed';
					$active_step2_class = '';
					$completed_step2_class = 'completed';
					$active_step3_class = '';
					$completed_step3_class = 'completed';
					$active_step4_class = 'active';
				}
			?>
		
			<ul class="mystickyelements-tabs">
				<li>
					<a href="javascript:void(0)" class="mystickyelements-tab <?php echo esc_attr($active_step1_class . $completed_step1_class) ;?>" data-tab-id="mystickyelements-tab-contact-form" id="mystickyelements-contact-form" data-tab="first" data-tab-index= "<?php  if(isset($widget_tab_index) && $widget_tab_index=='mystickyelements-contact-form'){
					echo esc_attr($widget_tab_index);}else{ if($widget_tab_index == 'mystickyelements-contact-form' ){echo esc_attr($widget_tab_index); } } ?>">
						<span class="mystickyelements-tabs-heading"> <?php esc_html_e('Add Contact Form','mystickyelements');?> </span>
						<span class="mystickyelements-tabs-subheading"> <?php esc_html_e('Collect form responses','mystickyelements');?></span>
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="mystickyelements-tab <?php echo esc_attr($active_step2_class . $completed_step2_class) ;?>" data-tab-id="mystickyelements-tab-social-media" id="mystickyelements-social-media" data-tab="middle" data-tab-index= "<?php echo esc_attr((isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-social-media') ? $widget_tab_index : ''); ?>">
						<span class="mystickyelements-tabs-heading"><?php esc_html_e('Add Chat and Social media','mystickyelements');?></span>
						<span class="mystickyelements-tabs-subheading"><?php esc_html_e('Integrate chat & social media','mystickyelements');?></span> 
					</a>
				</li>				
				<li>
					<a href="javascript:void(0)" class="mystickyelements-tab <?php echo esc_attr($active_step3_class . $completed_step3_class); ?>" data-tab-id="mystickyelements-tab-display-settings" id="mystickyelements-display-settings" data-tab="middle" data-tab-index="<?php echo esc_attr((isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-display-settings') ? $widget_tab_index : ''); ?>">
						<span class="mystickyelements-tabs-heading"><?php esc_html_e('Display & Behavior Settings','mystickyelements');?></span>
						<span class="mystickyelements-tabs-subheading"><?php esc_html_e('Triggers & targeting','mystickyelements');?></span> 
					</a>
				</li>
				<li>
					<a href="javascript:void(0)" class="mystickyelements-tab <?php echo esc_attr($active_step4_class) ;?>" data-tab-id="mystickyelements-tab-live-chatway" id="mystickyelements-live-chatway" data-tab="last" data-tab-index= "<?php echo (isset($widget_tab_index) && $widget_tab_index == 'mystickyelements-live-chatway') ? $widget_tab_index : ''; ?>">
						<span class="mystickyelements-tabs-heading"><?php esc_html_e('Live Chat','mystickyelements');?></span>
						<span class="mystickyelements-tabs-subheading"><?php esc_html_e('Add a Chatway live chat widget','mystickyelements');?></span> 
					</a>
				</li>
			</ul>
			<div class="mystickyelements-tabs-wrap mystickyelements-common-form-section">
				<div id="flash_message"> <?php esc_html_e('Settings saved.','mystickyelements'); ?> <span><a href="#" class="close_flash_popup">&#x2715;</a></span></div>
				
				<div id="loader" class="center" style="display:none;"><svg  version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve" style="width:150px;height:150px;"><path fill="#fff" d="M73,50c0-12.7-10.3-23-23-23S27,37.3,27,50 M30.9,50c0-10.5,8.5-19.1,19.1-19.1S69.1,39.5,69.1,50"><animateTransform attributeName="transform" attributeType="XML" type="rotate" dur="1s" from="0 50 50" to="360 50 50" repeatCount="indefinite"></animateTransform></path></svg></div>
				<?php 
					include( 'contact-forms.php' );
					include( 'social-media.php' );
					include( 'general-settings.php' );
					include( 'chatway.php' );
					include( 'sticktelements-preview.php' );
				?>
			</div>
			
			<div class="mystickyelements-progress-bar-wrap show-on-apper-main">
				<p class="prev mystickyelements-prev-btn-wrap" id="prev-show-on-apper">
					<button type="submit" name="prev" id="btn-prev" class="button button-primary" style="display: none;"><i class="fas fa-arrow-circle-left"></i>&nbsp;&nbsp;<?php _e('Back', 'mystickyelements');?></button>
				</p>				
				<p class="next mystickyelements-next-btn-wrap" id="next-show-on-apper">
					<button type="submit" name="next" id="btn-next" class="button button-primary"><i class="fas fa-arrow-circle-right"></i>&nbsp;&nbsp;<?php _e('Next', 'mystickyelements');?></button>
				</p>				
				<p class="save mystickyelements-save-btn-wrap" id="save-show-on-apper">
					<button type="submit" name="submit" value="Save" id="save" class="button button-primary save-button"><?php _e('Save', 'mystickyelements');?></button>
				</p>
				<p class="save_view mystickyelements-save-view-btn-wrap" id="save-view-show-on-apper">
					<button type="submit" name="save_view" value="Save View" id="save_view" class="button button-primary"><?php _e('Save & View Dashboard', 'mystickyelements');?></button>
				</p>
			</div>
			
			<input type="hidden" id="mystickyelement_save_confirm_status" name="mystickyelement_save_confirm_status" value=""/>
			<?php wp_nonce_field( 'mystickyelement-submit', 'mystickyelement-submit' ); ?>
		</form>		
	</div>
</div>
