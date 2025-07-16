<?php

if ( !class_exists('MyStickyElementsPage_pro') ) {

	class MyStickyElementsPage_pro {

		public function __construct() { 
			add_action( 'plugins_loaded', array( $this, 'mystickyelements_load_plugin_textdomain' ) );
			add_action( 'admin_enqueue_scripts',  array( $this, 'mystickyelements_admin_enqueue_script' ), 99 );
			add_action( 'admin_menu', array( $this, 'add_mystickyelement_plugin_page' ) );
			add_action( 'wp_ajax_mystickyelement-social-tab', array( $this, 'mystickyelement_social_tab_add' ) );
			add_action( 'wp_ajax_mystickyelement_delete_db_record', array( $this, 'mystickyelement_delete_db_record' ) );
			
			add_action( 'wp_ajax_myStickyelements_intro_popup_action', array( $this, 'myStickyelements_intro_popup_action' ) );
			add_action( 'wp_ajax_mystickyelement_widget_status', array( $this, 'mystickyelement_widget_status' ) );
			add_action( 'wp_ajax_mystickyelement_widget_rename', array( $this, 'mystickyelement_widget_rename' ) );
			add_action( 'wp_ajax_mystickyelement_widget_delete', array( $this, 'mystickyelement_widget_delete' ) );
			add_filter( 'plugin_action_links_'.MYSTICKYELEMENTS_BASE, array( $this, 'settings_link' )  );
			
			add_action( 'admin_footer', array( $this, 'mystickyelements_deactivate' ) );			
			/* Send message to owner */
			add_action( 'wp_ajax_mystickyelements_admin_send_message_to_owner', array( $this, 'mystickyelements_admin_send_message_to_owner' ) );
			add_action( 'wp_ajax_mystickyelements_plugin_deactivate', array( $this, 'mystickyelements_plugin_deactivate' ) );
 

			add_action( 'wp_ajax_my_sticky_elements_bulks', array( $this, 'my_sticky_elements_bulks' ) );
			
			add_action( 'wp_ajax_mystickyelements_review_box', [$this, "mystickyelements_review_box"]);
			add_action( 'wp_ajax_mystickyelements_review_box_message', [$this, "mystickyelements_review_box_message"]);

			
		}
 
	
		public function settings_link($links) {
			$settings_link = '<a href="'.admin_url("admin.php?page=my-sticky-elements").'">Settings</a>';
			$links['need_help'] = '<a href="https://wordpress.org/support/plugin/mystickyelements/" target="_blank">'.__( 'Need help?', 'mystickyelements' ).'</a>';
			
			$links['go_pro'] = '<a href="'.admin_url("admin.php?page=my-sticky-elements-upgrade").'" style="color: #FF5983; font-weight: bold; display: inline-block; border: solid 1px #FF5983; border-radius: 4px; padding: 0 5px;">'.__( 'Upgrade', 'mystickyelements' ).'</a>';			
			
			array_unshift($links, $settings_link);
			return $links;
		}

		/*
		 * Load Plugin text domain.
		 */

		public function mystickyelements_load_plugin_textdomain() {
			load_plugin_textdomain('mystickyelements', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
		}

		/*
		 * enqueue admin side script and style.
		 */
		public  function mystickyelements_admin_enqueue_script( ) {
            $min = MSE_DEV_MODE ? '' : '.min';
			if ( isset($_GET['page']) && ( $_GET['page'] == 'my-sticky-elements' || $_GET['page'] == 'my-sticky-elements-leads' || $_GET['page'] == 'my-sticky-elements-new-widget' || $_GET['page'] == 'recommended-plugins' || $_GET['page'] == 'my-sticky-elements-analytics' || $_GET['page'] == 'my-sticky-elements-integration' || $_GET['page'] == 'my-sticky-elements-upgrade' ) ) {
           
				$is_shown = MSE_SIGNUP_CLASS::check_modal_status();
                if($is_shown) {
                    wp_enqueue_script( 'mailcheck-js', plugins_url('/js/mailcheck'.esc_attr($min).'.js', __FILE__), ['jquery'], MY_STICKY_ELEMENT_VERSION);
                    wp_enqueue_script('autocomplete-email-js', plugins_url('/js/jquery.email-autocomplete'.esc_attr($min).'.js', __FILE__), ['jquery'], MY_STICKY_ELEMENT_VERSION, true);
                    wp_enqueue_style('mystickyelements-help-css', plugins_url('/css/mystickyelements-help'.esc_attr($min).'.css', __FILE__), array(), MY_STICKY_ELEMENT_VERSION);
                    wp_style_add_data('mystickyelements-help-css', 'rtl', 'replace');
                } else {
                    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Poppins:400,500,600,700');
                    wp_enqueue_style('font-awesome-css', plugins_url('/css/font-awesome.min.css', __FILE__), array(), MY_STICKY_ELEMENT_VERSION);
                    wp_enqueue_style('wp-color-picker');
                    wp_enqueue_style('mystickyelements-admin-css', plugins_url('/css/mystickyelements-admin'.esc_attr($min).'.css', __FILE__), array(), MY_STICKY_ELEMENT_VERSION);
                    wp_style_add_data('mystickyelements-admin-css', 'rtl', 'replace');
					
					wp_enqueue_style('select2-css', plugins_url('/css/select2.min.css', __FILE__), array(), MY_STICKY_ELEMENT_VERSION);
                    wp_enqueue_style('mystickyelements-front-css', plugins_url('/css/mystickyelements-front'.esc_attr($min).'.css', __FILE__), array(), MY_STICKY_ELEMENT_VERSION);
                    wp_enqueue_style('mystickyelements-help-css', plugins_url('/css/mystickyelements-help'.esc_attr($min).'.css', __FILE__), array(), MY_STICKY_ELEMENT_VERSION);
                    wp_style_add_data('mystickyelements-help-css', 'rtl', 'replace');
					wp_enqueue_style( 'wp-jquery-ui-dialog' );
					wp_enqueue_script( 'jquery-ui-dialog' );
                    wp_enqueue_script('wp-color-picker');
                    wp_enqueue_script('jquery-ui-sortable');
                    wp_enqueue_script('jquery-effects-shake');
                    wp_enqueue_media();
					// include the javascript
					wp_enqueue_script('thickbox', null, array('jquery'));
					
					wp_enqueue_style('jquery-ui-css', plugins_url('/css/datepicker.min.css', __FILE__), [], MY_STICKY_ELEMENT_VERSION);

					wp_enqueue_script('jquery-ui-datepicker');

					// include the thickbox styles
					wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
					
					wp_enqueue_style("mystickyelements-star-rating-svg", plugins_url('css/star-rating-svg.css', __FILE__), [], MY_STICKY_ELEMENT_VERSION);
					wp_enqueue_script("mystickyelements-star-rating-svg", plugins_url('js/jquery.star-rating-svg.min.js', __FILE__), ['jquery'], MY_STICKY_ELEMENT_VERSION);
					
					//wp_enqueue_script('plugin-install', admin_url('/js/plugin-install.min', __FILE__), array( 'jquery' ), MY_STICKY_ELEMENT_VERSION, true ) ;
					wp_enqueue_script('select2-js', plugins_url('/js/select2.min.js', __FILE__), array( 'jquery' ), MY_STICKY_ELEMENT_VERSION, true ) ;
					wp_enqueue_script('timepicker-js', plugins_url('/js/timepicker.min.js', __FILE__), ['jquery'], MY_STICKY_ELEMENT_VERSION, false);
					wp_enqueue_script('confetti-js', plugins_url('/js/confetti.min.js', __FILE__), array( 'jquery' ), MY_STICKY_ELEMENT_VERSION, true ) ;
					
					wp_enqueue_script( 'mailcheck-js', plugins_url('/js/mailcheck'.esc_attr($min).'.js', __FILE__), ['jquery'], MY_STICKY_ELEMENT_VERSION);
					wp_enqueue_script('autocomplete-email-js', plugins_url('/js/jquery.email-autocomplete'.esc_attr($min).'.js', __FILE__), ['jquery'], MY_STICKY_ELEMENT_VERSION, true);
					
                    wp_enqueue_script('mystickyelements-js', plugins_url('/js/mystickyelements-admin'.esc_attr($min).'.js', __FILE__), array('jquery'), MY_STICKY_ELEMENT_VERSION, true);
					
					$locale_settings = array(
						'ajaxurl' => admin_url('admin-ajax.php'),
						'ajax_nonce' => wp_create_nonce('mystickyelements'),
						'has_js_access' => current_user_can("unfiltered_html") ? true : false,
						'js_message' => esc_html__("Please remove the JavaScript from the channels or ask the website's administrator to give you access to add JavaScript.", "mystickyelements"),
						'remove' => esc_html__("Remove", "mystickyelements")
					);
					wp_localize_script('mystickyelements-js', 'mystickyelements', $locale_settings);
                }
			}
			
			if ( isset($_GET['page']) && $_GET['page'] == 'my-sticky-elements-upgrade'  ) {
				wp_enqueue_style('mystickyelements-pricing-table', plugins_url('/css/pricing-table'.esc_attr($min).'.css', __FILE__), [], MY_STICKY_ELEMENT_VERSION);
				$queryArgs = [
					'family' => 'Poppins:wght@400;500;600;700&display=swap',
					'subset' => 'latin,latin-ext',
				];
				wp_enqueue_style('google-poppins-fonts', add_query_arg($queryArgs, "//fonts.googleapis.com/css2"), [], null);
				
				wp_enqueue_script('mystickyelements-slick', plugins_url('/js/slick.min.js', __FILE__), ['jquery'], MY_STICKY_ELEMENT_VERSION);
			}
		}

		/*
		 * Add My Sticky Element Page in admin menu.
		 */
		public function add_mystickyelement_plugin_page() {			
			
			 
            $getData = filter_input_array(INPUT_GET); 
            if (isset($getData['hide_mserecommended_plugin']) && isset($getData['nonce'])) {
                if (current_user_can('manage_options')) {
                    $nonce = $getData['nonce'];
                    if (wp_verify_nonce($nonce, "mse_recommended_plugin")) {
                        update_option('hide_mserecommended_plugin', true);
                    }
                }
            }
			$hide_mserecommended_plugin = get_option('hide_mserecommended_plugin');
			$elements_widgets 			= get_option( 'mystickyelements-widgets' );
			
			$new_widget_link 			= ( !empty($elements_widgets)) ? 'my-sticky-elements-new-widget' : 'my-sticky-elements&widget=0';
			$default_widget_name = 'Dashboard';
			add_menu_page(
				'Settings Admin',
				'myStickyelements',
				'manage_options',
				'my-sticky-elements',
				array( $this, 'mystickyelements_admin_settings_page' ),
				'dashicons-sticky'
			);
			add_submenu_page(
				'my-sticky-elements',
				'Settings Admin',
				'Dashboard',
				'manage_options',
				'my-sticky-elements',
				array( $this, 'mystickyelements_admin_settings_page' )
			);
			add_submenu_page(
				'my-sticky-elements',
				'Settings Admin',
				'+ Create New Widget',
				'manage_options',
				$new_widget_link,
				array( $this, 'mystickyelements_admin_new_widget_page' )
			);
			
			if( class_exists( 'Chatway' ) ) {
				add_submenu_page(
					'my-sticky-elements',
					'Settings Admin',
					'Chatway Live Chat',
					'manage_options',
					'manage-chatway-plugin',
					array( $this, 'mystickyelements_manage_chatway_plugin' )
				);
			} else {
				add_submenu_page(
					'my-sticky-elements',
					'Settings Admin',
					'Chatway Live Chat',
					'manage_options',
					'my-sticky-elements-chatway-plugin',
					array( $this, 'mystickyelements_install_chatway_plugin' )
				);
			}
			add_submenu_page(
				'my-sticky-elements',
				'Settings Admin',
				'Integrations',
				'manage_options',
				'my-sticky-elements-integration',
				array( $this, 'mystickyelements_admin_integration_page' )
			);


			add_submenu_page(
                'my-sticky-elements',
                'Settings Admin',
                'Widget Analytics',
                'manage_options',
                'my-sticky-elements-analytics',
                [
                    $this,
                    'mystickyelements_admin_widget_analytics_page',
                ]
            );
			
			add_submenu_page(
				'my-sticky-elements',
				'Settings Admin',
				'Contact Form Leads',
				'manage_options',
				'my-sticky-elements-leads',
				array( $this, 'mystickyelements_admin_leads_page' )
			);
			if ( !$hide_mserecommended_plugin){
				add_submenu_page(
					'my-sticky-elements',
					'Recommended Plugins',
					'Recommended Plugins',
					'manage_options',
					'recommended-plugins',
					array( $this, 'mystickyelements_recommended_plugins' )
				);
			}
			
			
			add_submenu_page(
				'my-sticky-elements',
				'Upgrade to Pro ⭐️',
				'Upgrade to Pro ⭐️',
				'manage_options',
				'my-sticky-elements-upgrade',
				array( $this, 'mystickyelements_admin_upgrade_to_pro' )
			);			
			
		}

		public function mystickyelements_admin_widget_analytics_page(){
            $is_shown = MSE_SIGNUP_CLASS::check_modal_status();
			if($is_shown) {
				/* Signup Form When first time activate plugin */				
				include_once MYSTICKYELEMENTS_PATH . '/admin/email-signup.php';
				
			} else {
				include('mystickyelements-admin-widgetanalytics.php');				
			} 	
        }

		public static function sanitize_options($value, $type = "") {
			if ( !is_array($value)) {
				$value = stripslashes($value);
			}
			if($type == "int") {
				$value = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
			} else if($type == "email") {
				$value = sanitize_email($value);
			} else if($type == "url") {
				$value = esc_url_raw($value);
			} else if($type == "sql") {
				$value = esc_sql($value);
			} else {
				$value = sanitize_text_field($value);
			}
			return $value;
		}

		public function mystickyelements_admin_upgrade_to_pro() {
			$is_shown = MSE_SIGNUP_CLASS::check_modal_status();
			if($is_shown) {
				/* Signup Form When first time activate plugin */				
				include_once MYSTICKYELEMENTS_PATH . '/admin/email-signup.php';			
			} else {
				include_once 'upgrade-to-pro.php';
			} 
		}		
		
		/*
		 * My Sticky Elements Settings Page
		 *
		 */
		public function mystickyelements_admin_settings_page() {
			global $wpdb;
			$is_shown = MSE_SIGNUP_CLASS::check_modal_status();
			if($is_shown != 1) {
				/* Signup Form When first time activate plugin */	 
				require_once MYSTICKYELEMENTS_PATH . 'admin/stickyelements-review-popup.php';	
			}
			
			$widget_tab_index = 'mystickyelements-contact-form';
			if ( isset($_POST['mystickyelement-submit']) && !wp_verify_nonce( $_POST['mystickyelement-submit'], 'mystickyelement-submit' ) ) {
				
				echo '<div class="error settings-error notice is-dismissible "><p><strong>' . esc_html__('Unable to complete your request','mystickyelements'). '</p></strong></div>';

			} else if (  isset($_POST['general-settings']) && !empty($_POST['general-settings']) && wp_verify_nonce( $_POST['mystickyelement-submit'], 'mystickyelement-submit' )) {
				
				/* Save/Update Contact Form tab */				
				$widget_tab_index = isset($_POST['hide_tab_index']) ? $_POST['hide_tab_index'] : 'mystickyelements-contact-form';
				
				$elements_widgets[] = $_POST['widget_name'];
				
				
				update_option( 'mystickyelements-widgets', $elements_widgets );
		
				$contact_field = filter_var_array( $_POST['contact-field'] );
				update_option('mystickyelements-contact-field', $contact_field);

				$post = array();
				if(isset($_POST['contact-form'])) {
					$contact = $_POST['contact-form'];

					if(isset($contact['enable'])) {
						$post['enable'] = self::sanitize_options($contact['enable'], "int");
					}

					if(isset($contact['name'])) {
						$post['name'] = self::sanitize_options($contact['name'], "int");
					}

					if(isset($contact['name_require'])) {
						$post['name_require'] = self::sanitize_options($contact['name_require'], "int");
					}

					if(isset($contact['name_value'])) {
						$post['name_value'] = self::sanitize_options($contact['name_value']);
					}

					if(isset($contact['phone'])) {
						$post['phone'] = self::sanitize_options($contact['phone'], "int");
					}

					if(isset($contact['phone_require'])) {
						$post['phone_require'] = self::sanitize_options($contact['phone_require'], "int");
					}
					
					if(isset($contact['phone_formate'])) {
						$post['phone_formate'] = self::sanitize_options($contact['phone_formate'], "int");
					}

					if(isset($contact['phone_value'])) {
						$post['phone_value'] = self::sanitize_options($contact['phone_value']);
					}

					if(isset($contact['email'])) {
						$post['email'] = self::sanitize_options($contact['email'], "int");
					}

					if(isset($contact['email_require'])) {
						$post['email_require'] = self::sanitize_options($contact['email_require'], "int");
					}

					if(isset($contact['email_value'])) {
						$post['email_value'] = self::sanitize_options($contact['email_value']);
					}

					if(isset($contact['message'])) {
						$post['message'] = self::sanitize_options($contact['message'], "int");
					}

					if(isset($contact['message_require'])) {
						$post['message_require'] = self::sanitize_options($contact['message_require'], "int");
					}

					if(isset($contact['message_value'])) {
						$post['message_value'] = self::sanitize_options($contact['message_value']);
					}
					if(isset($contact['dropdown'])) {
						$post['dropdown'] = self::sanitize_options($contact['dropdown'], "int");
					}

					if(isset($contact['dropdown_require'])) {
						$post['dropdown_require'] = self::sanitize_options($contact['dropdown_require'], "int");
					}
					
					if(isset($contact['submit_button_background_color'])) {
						$post['submit_button_background_color'] = self::sanitize_options($contact['submit_button_background_color']);
					}

					if(isset($contact['submit_button_text_color'])) {
						$post['submit_button_text_color'] = self::sanitize_options($contact['submit_button_text_color']);
					}

					if(isset($contact['submit_button_text'])) {
						$post['submit_button_text'] = self::sanitize_options($contact['submit_button_text']);
					}

					if(isset($contact['tab_background_color'])) {
						$post['tab_background_color'] = self::sanitize_options($contact['tab_background_color']);
					}

					if(isset($contact['tab_text_color'])) {
						$post['tab_text_color'] = self::sanitize_options($contact['tab_text_color']);
					}
					if(isset($contact['form_bg_color'])) {
						$post['form_bg_color'] = self::sanitize_options($contact['form_bg_color']);
					}
					if(isset($contact['headine_text_color'])) {
						$post['headine_text_color'] = self::sanitize_options($contact['headine_text_color']);
					}

					if(isset($contact['text_in_tab'])) {
						$post['text_in_tab'] = self::sanitize_options($contact['text_in_tab']);
					}

					if(isset($contact['contact_title_text'])) {
						$post['contact_title_text'] = self::sanitize_options($contact['contact_title_text']);
					}

					if(isset($contact['send_leads'])) {
						$post['send_leads'] = self::sanitize_options($contact['send_leads']);
					}

					if(isset($contact['sent_to_mail'])) {
						$post['sent_to_mail'] = self::sanitize_options($contact['sent_to_mail']);
					}

					if(isset($contact['direction'])) {
						$post['direction'] = self::sanitize_options($contact['direction']);
					}

					if(isset($contact['direction'])) {
						$post['direction'] = self::sanitize_options($contact['direction']);
					}

					if(isset($contact['desktop'])) {
						$post['desktop'] = self::sanitize_options($contact['desktop'], "int");
					}

					if(isset($contact['mobile'])) {
						$post['mobile'] = self::sanitize_options($contact['mobile'], "int");
					}
					if(isset($contact['dropdown-placeholder'])) {
						$post['dropdown-placeholder'] = self::sanitize_options($contact['dropdown-placeholder']);
					}
					if(isset($contact['dropdown-option'])) {
						$post['dropdown-option'] = filter_var_array( $contact['dropdown-option'] );
					}
					if(isset($contact['redirect'])) {
						$post['redirect'] = self::sanitize_options($contact['redirect'], "int");
					}
					if(isset($contact['redirect_link'])) {
						$post['redirect_link'] = self::sanitize_options($contact['redirect_link']);
					}
					
					if (isset($contact['close_form_automatic'])) {
                        $post['close_form_automatic'] = self::sanitize_options($contact['close_form_automatic'], "int");
                    }

                    if (isset($contact['close_after'])) {
                        $post['close_after'] = self::sanitize_options($contact['close_after'], "int");
                    }
				}
				update_option('mystickyelements-contact-form', $post);

				/* Save/Update Social Channels tabs */
				$social_channels = array();
				if(isset($_POST['social-channels'])) {
					if(!empty($_POST['social-channels'])) {
						$social_channels = $_POST['social-channels'];
						foreach($social_channels as $key=>$val) {
							$social_channels[$key] = self::sanitize_options($val, "int");
						}
					}
				}
				update_option('mystickyelements-social-channels', $social_channels);

				$social_channels_tab = array();
				if(isset($_POST['social-channels-tab'])) {
					if(!empty($_POST['social-channels-tab'])) {						
						foreach($_POST['social-channels-tab'] as $key=>$option) {
							if(isset($option['text'])) {
								if (strpos($key, 'custom_channel') !== false ) {
									 if( is_super_admin() ) {
										$option['text'] = self::sanitize_options($option['text']);
									} else {										
										$option['text'] = esc_url($option['text']);
									}
								}elseif (strpos($key, 'custom_shortcode') !== false ) {
									$option['text'] = $option['text'];
								} else {
									$option['text'] = self::sanitize_options($option['text']);
								}
							}
							if(isset($option['desktop'])) {
								$option['desktop'] = self::sanitize_options($option['desktop'], "int");
							}
							if(isset($option['mobile'])) {
								$option['mobile'] = self::sanitize_options($option['mobile'], "int");
							}
							if(isset($option['bg_color'])) {
								$option['bg_color'] = self::sanitize_options($option['bg_color']);
							}
							if(isset($option['icon_text'])) {
								$option['icon_text'] = self::sanitize_options($option['icon_text']);
							}							
							if(isset($option['hover_text'])) {
								$option['hover_text'] = self::sanitize_options($option['hover_text']);
							}
							if (isset($option['open_new_tab'])) {
                                $option['open_new_tab'] = self::sanitize_options($option['open_new_tab']);
                            }
							if (isset($option['pre_set_message'])) {
                                $option['pre_set_message'] = self::sanitize_options($option['pre_set_message']);
                            }
							
							$social_channels_tab[$key] = $option;
						}
					}
				}
				update_option( 'mystickyelements-social-channels-tabs', $social_channels_tab);

				/* Save/Update General Settings */
				$general_setting = array();
				if(isset($_POST['general-settings'])) {
					if(!empty($_POST['general-settings'])) {
						foreach($_POST['general-settings'] as $key=>$value) {
							$general_setting[$key] = self::sanitize_options($value);
						}
					}
				}
				update_option('mystickyelements-general-settings', $general_setting);

				/* Send Email Afte set email */
				if ( isset($_POST['contact-form']['send_leads']) && $_POST['contact-form']['send_leads'] == 'mail' && $_POST['contact-form']['sent_to_mail'] != '' && !get_option('mystickyelements-contact-mail-sent') ) {
					$send_mail = $_POST['contact-form']['sent_to_mail'];

					$subject = "Great job! You created your contact form successfully";
					$message = 'Thanks for using MyStickyElements! If you see this message in your spam folder, please click on "Report not spam" so you will get the next leads into your inbox.';


					$blog_name = get_bloginfo('name');
					$blog_email = get_bloginfo('admin_email');

					$headers = "MIME-Version: 1.0\r\n";
					$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
					$headers .= 'From: ' . $blog_name . ' <' . $blog_email . '>' ."\r\n";
					$headers .= 'X-Mailer: PHP/' . phpversion() . "\r\n";

					if ( wp_mail( $send_mail, $subject, $message, $headers ) ) {
						update_option( 'mystickyelements-contact-mail-sent', true );
					}
				}
				$this->mystickyelements_clear_all_caches();	
				
				
				if(isset($_POST['save_view']) && $_POST['save_view'] == 'Save View'){
					echo '<script type="text/javascript"> jQuery("#loader").show(); </script>';
					
					if( isset($_POST['widgest_status']) && $_POST['widgest_status'] == 0 ){
						echo '<script>window.location.href = "'.admin_url( "admin.php?page=my-sticky-elements&first_widget=1" ).'";</script>';
					} else {
						echo '<script>window.location.href = "'.admin_url( "admin.php?page=my-sticky-elements" ).'";</script>';
					}
				}
				
				if(( isset($_POST['submit']) && $_POST['submit'] == 'Save' ) || ( isset($_POST['submit']) && $_POST['submit'] == 'Publish' )){
					if( isset($_POST['widgest_status']) && $_POST['widgest_status'] == 0 ){
						$this->show_save_popup();
					}
					echo '<script type="text/javascript"> jQuery("#flash_message").addClass("show");setTimeout(function(){jQuery("#flash_message").removeClass("show");}, 3000);</script>';
				}
							
			}
			
			$contact_field = get_option( 'mystickyelements-contact-field' );
			$is_widgest_create = 1;
			if ( empty( $contact_field ) ) {
				$is_widgest_create = 'my-sticky-elements-new-widget';
				$contact_field = array( 'name', 'phone', 'email', 'message', 'dropdown' );
			}
			$contact_form = get_option( 'mystickyelements-contact-form');
			$social_channels = get_option( 'mystickyelements-social-channels');
			$social_channels_tabs = get_option( 'mystickyelements-social-channels-tabs');
			$general_settings = get_option( 'mystickyelements-general-settings');
			
			if ( !$contact_form && !$social_channels && !$social_channels_tabs && !$general_settings ) {
				$contact_form 			= mystickyelement_default_settings( 'contact_form' );
				$social_channels 		= mystickyelement_default_settings( 'social_channels' );
				$social_channels_tabs 	= mystickyelement_default_settings( 'social_channels_tabs' );
				$general_settings 		= mystickyelement_default_settings( 'general_settings' );
			}
			if ( !isset($general_settings['position']) ) {
				$general_settings['position'] = 'left';
			}
			if ( !isset($general_settings['position_mobile']) ) {
				$general_settings['position_mobile'] = 'left';
			}
			$social_channels_lists = mystickyelements_social_channels();

			$upgrade_url = admin_url("admin.php?page=my-sticky-elements-upgrade");
			$is_pro_active = false;
            $is_shown = MSE_SIGNUP_CLASS::check_modal_status();
			if($is_shown) {
				/* Signup Form When first time activate plugin */				
				include_once MYSTICKYELEMENTS_PATH . '/admin/email-signup.php';
				
			} else {
				
				$default_fonts = array('Arial', 'Tahoma', 'Verdana', 'Helvetica', 'Times New Roman', 'Trebuchet MS', 'Georgia', 'Open Sans Hebrew');
				
					if (isset($general_settings['font_family']) && $general_settings['font_family'] !="" ) :
						if ( !in_array( $general_settings['font_family'], $default_fonts) ):
				?>
                <link href="https://fonts.googleapis.com/css?family=<?php echo esc_attr($general_settings['font_family']); ?>:400,500,600,700"
                      rel="stylesheet" type="text/css" class="sfba-google-font">
					  <?php endif;?>
                <style>
                    .myStickyelements-preview-ul .mystickyelements-social-icon {
                        font-family: <?php echo esc_attr(( isset( $general_settings['font_family'] ) && $general_settings['font_family'] == 'System Stack' ) ? '-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Helvetica Neue,sans-serif'  : $general_settings['font_family']);?>}
                </style>
				<?php endif;
				if ( !isset($_GET['widget']) && isset($_GET['page'])) {
					include_once( 'admin/stickyelements-dashboard.php');
				} else {
					include_once( 'admin/stickyelements-settings.php');
					
					$mystickyelements_popup_status = get_option( 'mystickyelements_intro_popup' );
					if( $mystickyelements_popup_status == 'show' ) {					
						?>
						
						<div class="contactform-sendleads-upgrade-popup mystickyelements-action-popup-open mystickyelements-intro-popup mystickyelements-blue-popup" style="display:block;">
							<div class="popup-ui-widget-header">
								<span id="ui-id-1" class="ui-dialog-title"><?php esc_html_e("Welcome to My Sticky Elements","mystickyelements");?></span>
								
								<span class="close-dialog" data-from="intro-popup">						
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36"><path fill="#31373D" d="M22.238 18.004l9.883-9.883c1.172-1.171 1.172-3.071 0-4.243-1.172-1.171-3.07-1.171-4.242 0l-9.883 9.883-9.883-9.882c-1.171-1.172-3.071-1.172-4.243 0-1.171 1.171-1.171 3.071 0 4.243l9.883 9.882-9.907 9.907c-1.171 1.171-1.171 3.071 0 4.242.585.586 1.354.879 2.121.879s1.536-.293 2.122-.879l9.906-9.906 9.882 9.882c.586.586 1.354.879 2.121.879s1.535-.293 2.121-.879c1.172-1.171 1.172-3.071 0-4.242l-9.881-9.883z"/></svg>
								</span>
							</div>
							<div class="ui-widget-content">
								<p><?php _e( 'Select your contact form fields, chat, and social channels. Need help? Visit our ', 'mystickyelements'); ?><a href="https://premio.io/help/mystickyelements/?utm_soruce=wordpressmystickyelements" target="_blank"><?php _e( 'Help Center', 'mystickyelements'); ?></a><?php _e( ' and check the video.', 'mystickyelements'); ?></p>
								
								<iframe width="420" height="240" src="https://www.youtube.com/embed/VR9S_yuN1ko" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
								<input type="hidden" id="myStickyelements_update_popup_status" value="<?php echo wp_create_nonce("myStickyelements_update_popup_status") ?>">
							</div>
							<div class="popup-ui-dialog-buttonset">
								<a href="#" class="new-upgrade-button mystickyelement-goto-button">Go to My Sticky Elements</a>
							</div>
						</div>
						<div id="mystickyelement_intro_popup_overlay" class="stickyelement-overlay" style="display:block;"></div>
						<?php
					}
				}				
				
				?>
                
                <?php
				$table_name = $wpdb->prefix . "mystickyelement_contact_lists";
				$result = $wpdb->get_results ( "SELECT count(*) as count FROM {$table_name} ORDER BY ID DESC" );

				if ( $result[0]->count != 0 && !get_option( 'myStickyelements_show_leads' )) { ?>
					<div id="myStickyelements-new-lead-confirm" style="display:none;" title="<?php esc_attr_e( 'Congratulations 🎉', 'mystickyelements' ); ?>">
						<p><?php _e('You just got your first My Sticky Elements lead. Click on the Show Me button to display your contact form leads', 'mystickyelements' ); ?></p>
						<p><?php _e('<a style="color: #000;" href="'.esc_url($upgrade_url).'" target="_blank"><strong>Upgrade to Pro</strong></a> to get leads on your email with more customization and awesome features 🚀', 'mystickyelements' ); ?></p>
					</div>
					<script>
						( function( $ ) {
							"use strict";
							$(document).ready(function(){
								jQuery( "#myStickyelements-new-lead-confirm" ).dialog({
									resizable: false,
									modal: true,
									draggable: false,
									height: 'auto',
									width: 400,
									buttons: {
										"Show Me": {
												click: function () {
													window.location = "<?php echo admin_url('admin.php?page=my-sticky-elements-leads')?>";
													//$(this).dialog('close');
												},
												text: 'Show Me',
												class: 'purple-btn'
											},
											"Not Now": {
												click: function () {
													confetti.remove();
													$(this).dialog('close');
												},
												text: 'Not Now',
												class: 'gray-btn'
											},
									}
								});
								confetti.start();
								$('#myStickyelements-new-lead-confirm').bind('dialogclose', function(event) {
									confetti.remove();
								});
							});
						})( jQuery );
					</script>
					<?php
					update_option( 'myStickyelements_show_leads', 1 );
				}
				?>
				<div id="mystickyelement-save-confirm" style="display:none;" title="<?php esc_attr_e( 'Icons\' text isn\'t supported in this template', 'mystickyelements' ); ?>">
					<p><?php _e("The selected template doesn't support icons'text, please change to the Default templates. Would you like to publish it anyway?", 'mystickyelements' ); ?></p>
				</div>
				<?php
				 
				
				
				if( isset($_GET['first_widget']) && $_GET['first_widget'] == 1 ) {
					$this->show_save_popup();
				}
				
            }
		}
		
		public function show_save_popup(){
			?>
			<div class="main-popup-mystickyelement-bg first-widget-popup">
				<div class="main-popup-mystickyelement-bg mystickyelement_container_popupbox">
					<div class="firstwidget-popup-contain">
						<img src="<?php echo MYSTICKYELEMENTS_URL; ?>/images/firstwidget_congratulations.svg">
						<h4><?php _e('Congratulations! 🎉','mystickyelements'); ?></h4>
						<p><?php _e('Your first widget is now up and running on your website!','mystickyelements'); ?></p>
						<div class="first-widget-popup-contant">
							<h4><?php _e('Upgrade to pro today','mystickyelements'); ?></h4>
							<p> <?php _e('🛠️ Show unlimited social icon such as Whatsapp,twitter,phone and so on','mystickyelements') ?> </p>
							<p> <?php _e('📱Create multiple widgets for different devices, pages and languages.','mystickyelements') ?> </p>
							<p> <?php _e('📈 Unlock analytics about each channel usage and different widgets ','mystickyelements') ?> </p>
							<p> <?php _e('📩 Get contact form leads to Your email and integrate with MailChimp and MailPoet','mystickyelements') ?> </p>
						</div>
						<a href="<?php echo esc_url(admin_url("admin.php?page=my-sticky-elements-upgrade"));?>" class="mystickymenu btn-black btn-back-dashboard"><?php _e('Upgrade to Pro','mystickyelements');?></a><br>
						<a href="#" class="mystickymenu btn-black btn-dashboard btn-close-dashboard"><?php _e('Close','mystickyelements');?></a>
					</div>
					<div class="popup-modul-close-btn firstwidget-model"><a href="<?php echo esc_url(admin_url( "admin.php?page=my-sticky-elements" )); ?>" class="close-chaty-maxvisitor-popup stickyelement-save-close-wrap" id="close-first-popup"><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 5L5 15" stroke="#4A4A4A" stroke-width="2.08" stroke-linecap="round" stroke-linejoin="round"/><path d="M5 5L15 15" stroke="#4A4A4A" stroke-width="2.08" stroke-linecap="round" stroke-linejoin="round"/></svg></a></div>
				</div>
			</div>
			<div class="stickyelement-overlay" id="success-popup-overlay"  data-id=""></div>
			<?php
		}
			
		public function mystickyelement_social_tab_add( $key, $element_widget_no = '' ) {
			global $social_channel_count;
            if (isset($_POST['is_ajax']) && $_POST['is_ajax'] == true) {
                if (!current_user_can('manage_options')) {
                    wp_die(0);
                }
                check_ajax_referer('mystickyelements', 'wpnonce');
            }

            $plugins_allowedtags = [
                'a'       => [
                    'href'   => [],
                    'title'  => [],
                    'target' => [],
                ],
                'abbr'    => [ 'title' => [] ],
                'acronym' => [ 'title' => [] ],
                'code'    => [],
                'pre'     => [],
                'em'      => [],
                'strong'  => [],
                'ul'      => [],
                'ol'      => [],
                'li'      => [],
                'p'       => [],
                'br'      => [],
                'svg'  => [
                    'xmlns'       => [],
                    'fill'        => [],
                    'viewbox'     => [],
                    'role'        => [],
                    'focusable'   => [],
                    'height'      => [],
                    'width'       => [],
                    'style'       => [],  
                ],
                'g' => [
                         "id" => [],
                         "fill" => [],
                ],
                'path' => [
                    'class' => [],
                    'style' => true,
                    'd'    => [],
                    'fill' => [],
                ]
            ];
           
            $social_channel = (isset($_POST['social_channel'])) ? $_POST['social_channel'] : $key;

            if ($social_channel != '') {
                $social_channels_tabs = get_option('mystickyelements-social-channels-tabs' . $element_widget_no, true);
                /* Return when Is Empty key found and isajax not set */
                if (isset($social_channels_tabs['is_empty']) && $social_channels_tabs['is_empty'] == 1 && !isset($_POST['is_ajax'])) {
                    return;
                }
				$social_channel_value = (isset($social_channels_tabs[$key])) ? $social_channels_tabs[$key] : array();
                if (strpos($social_channel, 'custom_channel') !== false || strpos($social_channel, 'custom_shortcode') !== false) {
                    $custom_channel_temp = '';
                    if (strpos($social_channel, 'custom_channel') !== false) {
                        $custom_channel_temp = $social_channel;
                        $social_channel = 'custom_channel';
                    }
                    if (strpos($social_channel, 'custom_shortcode') !== false) {
                        $custom_channel_temp = $social_channel;
                        $social_channel = 'custom_shortcode';
                    }

                    $social_channels_lists = mystickyelements_custom_social_channels();
                    $social_channels_list = $social_channels_lists[$social_channel];
					
					if ( isset($social_channel_value['channel_type']) && $social_channel_value['channel_type'] != '' && $social_channel_value['channel_type'] != 'custom') {
						$custom_social_channels_lists = mystickyelements_social_channels();						
						$social_channel_value['class'] = $custom_social_channels_lists[$social_channel_value['channel_type']]['class'];
						$social_channels_list['class'] = $custom_social_channels_lists[$social_channel_value['channel_type']]['class'];
						$social_channel_value['fontawesome_icon'] = $custom_social_channels_lists[$social_channel_value['channel_type']]['class'];
						$social_channels_list['fontawesome_icon'] = $custom_social_channels_lists[$social_channel_value['channel_type']]['class'];
						if ( isset($custom_social_channels_lists[$social_channel_value['channel_type']]['custom_svg_icon'])) {
							$social_channel_value['custom_svg_icon'] = $custom_social_channels_lists[$social_channel_value['channel_type']]['custom_svg_icon'];
							$social_channels_list['custom_svg_icon'] = $custom_social_channels_lists[$social_channel_value['channel_type']]['custom_svg_icon'];
						}
					}

                    if ($custom_channel_temp != '') {
                        $social_channel = $custom_channel_temp;
                    }
                    if (isset($_POST['channel_key']) && $_POST['channel_key'] != '') {
                        $social_channel = $social_channel . '_' . $_POST['channel_key'];
                    }
                } else {
                    $social_channels_lists 	= mystickyelements_social_channels();
                    $social_channels_list 	= ( isset($social_channels_lists[$social_channel])) ? $social_channels_lists[$social_channel] : [];
					if( empty($social_channels_list)) {
						return;
					}
                }			                
				
                $social_channels_list['text'] = isset($social_channels_list['text']) ? $social_channels_list['text'] : "";
                $social_channels_list['icon_text'] = isset($social_channels_list['icon_text']) ? $social_channels_list['icon_text'] : "";
                $social_channels_list['icon_text_size'] = isset($social_channels_list['icon_text_size']) ? $social_channels_list['icon_text_size'] : "";
                $social_channels_list['background_color'] = isset($social_channels_list['background_color']) ? $social_channels_list['background_color'] : "";
                $social_channels_list['hover_text'] = isset($social_channels_list['hover_text']) ? $social_channels_list['hover_text'] : "";

                if (empty($social_channel_value)) {

                    $social_channel_value['text'] = '';//$social_channels_list['text'];

                    $social_channel_value['bg_color'] = $social_channels_list['background_color'];
                    $social_channel_value['icon_text'] = $social_channels_list['icon_text'];
                    $social_channel_value['icon_text_size'] = $social_channels_list['icon_text_size'];
                    $social_channel_value['hover_text'] = $social_channels_list['hover_text'];
                    $social_channel_value['desktop'] = 1;
                    $social_channel_value['mobile'] = 1;
                    $social_channel_value['icon_color'] = '';
                }

                if (!isset($social_channel_value['icon_text'])) {
                    $social_channel_value['icon_text'] = '';
                }
                if (!isset($social_channel_value['icon_text_size'])) {
                    $social_channel_value['icon_text_size'] = '';
                }
                if (!isset($social_channel_value['icon_color'])) {
                    $social_channel_value['icon_color'] = '';
                }
                if (!isset($social_channel_value['pre_set_message'])) {
                    $social_channel_value['pre_set_message'] = '';
                }

                if (isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && isset($social_channel_value['fontawesome_icon']) && $social_channel_value['fontawesome_icon'] != '') {
                    $social_channels_list['class'] = $social_channel_value['fontawesome_icon'];
                } else {
                    $social_channel_value['fontawesome_icon'] = '';
                }
				

                if (!isset($social_channels_list['custom_icon']) && !isset($social_channel_value['custom_icon'])) {
                    $social_channel_value['custom_icon'] = '';
                }

                if ($key == 'line') {
                    echo "<style>.social-channels-item .social-channel-input-box-section .social-" . $key . " svg .fil1{ fill:" . $social_channel_value['icon_color'] . "}</style>";
                }
                if ($key == 'qzone') {
                    echo "<style>.social-channels-item .social-channel-input-box-section .social-" . $key . " svg .fil2{ fill:" . $social_channel_value['icon_color'] . "}</style>";
                }

                $social_channel_value['text'] = str_replace('\"', '"', $social_channel_value['text']);
                $social_channel_value['channel_type'] = (isset($social_channel_value['channel_type'])) ? $social_channel_value['channel_type'] : '';
                $social_channel_value['open_new_tab'] = (isset($social_channel_value['open_new_tab'])) ? $social_channel_value['open_new_tab'] : '';
				$social_channel_value['use_whatsapp_web'] = (isset($social_channel_value['use_whatsapp_web'])) ? $social_channel_value['use_whatsapp_web'] : '';
				$social_channel_value['mobile'] = (isset($social_channel_value['mobile'])) ? $social_channel_value['mobile'] : '';
                $social_channel_value['desktop'] = (isset($social_channel_value['desktop'])) ? $social_channel_value['desktop'] : '';
                $channel_type = (isset($social_channel_value['channel_type'])) ? $social_channel_value['channel_type'] : '';
				$social_channel_value['icon_text_color'] = (isset($social_channel_value['icon_text_color'])) ? $social_channel_value['icon_text_color'] : '#ffffff';
                if ($channel_type != 'custom' && $channel_type != '') {                    
                    if ($channel_type == 'whatsapp') {
                        $social_channels_list['is_pre_set_message'] = 1;
                    }
                }				
				$channel_class	= (isset($social_channels_list['channel_class'])) ? $social_channels_list['channel_class'] : '';
                ?>
                <div id="social-channel-<?php echo esc_attr($social_channel); ?>" class="social-channels-item"
                     data-slug="<?php echo esc_attr($social_channel); ?>">
                    <div class="mystickyelements-move-handle"></div>
                    <div class="social-channels-item-title social-channel-input-box-section">
                        <label>

							<span class="social-channels-list <?php echo esc_attr($channel_class);?> social-<?php echo esc_attr($social_channel); ?> social-<?php echo esc_attr($channel_type); ?>"
                                  style="background-color: <?php echo esc_attr($social_channel_value['bg_color']) ?>; color: <?php echo esc_attr($social_channel_value['icon_color']) ?>; position:relative;">
								<?php if (isset($social_channels_list['custom']) && $social_channels_list['custom'] == 1 && isset($social_channel_value['custom_icon']) && $social_channel_value['custom_icon'] != '' && isset($social_channel_value['fontawesome_icon']) && $social_channel_value['fontawesome_icon'] == ''): ?>
								
								
                                    <img class="<?php echo (isset($social_channel_value['stretch_custom_icon']) && $social_channel_value['stretch_custom_icon'] == 1) ? 'mystickyelements-stretch-custom-img' : ''; ?>"
                                         src="<?php echo esc_url($social_channel_value['custom_icon']); ?>" width="25"
                                         height="25"/>
                                <?php
                                else:
                                    if (isset($social_channels_list['custom_svg_icon']) && $social_channels_list['custom_svg_icon'] != '') :
                                        echo $social_channels_list['custom_svg_icon']; 
                                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    else:?>
                                        <i class="<?php echo esc_attr($social_channels_list['class']) ?>"></i>
                                    <?php endif;
                                endif; ?>
							</span>
                            <span><?php echo esc_html($social_channels_list['text'] . " Settings"); ?> </span>
                        </label>
                    </div>
                    <div class="myStickyelements-setting-wrap-list input-link">
                        <label><?php if (isset($social_channels_list['icon_label']) && $social_channels_list['icon_label'] != "") {
                                echo _e($social_channels_list['icon_label'], 'mystickyelements');
                            } else {
                                esc_html_e('Icon link', 'mystickyelements');
                            } ?></label>
                        <div class="px-wrap myStickyelements-inputs">
                            <!-- toolteap -->
                            <?php if (isset($social_channels_list['tooltip']) && $social_channels_list['tooltip'] != "") : ?>
                                <label class="social-tooltip">
									<span>
										<i class="fas fa-info"></i>
										<span class="social-tooltip-popup">
											<?php echo wp_kses_post($social_channels_list['tooltip']); ?>
										</span>
									</span>
                                </label>
                            <?php endif; if ( strpos($social_channel, 'custom_shortcode') !== false) { ?>
                                 <textarea  class="mystickyelement-social-links-input<?php if (isset($social_channels_list['number_validation']) && $social_channels_list['number_validation'] == 1) : ?> mystickyelement-social-text-input<?php endif; ?>" name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][text]" rows="3" cols="50" placeholder="<?php echo esc_attr($social_channels_list['placeholder']) ?>"><?php echo esc_attr(stripslashes($social_channel_value['text'])); ?></textarea>
                            <?php
                            } else { ?>
                                <input type="text"
                                   class="mystickyelement-social-links-input<?php echo esc_attr($channel_type) ; ?><?php if (isset($social_channels_list['number_validation']) && $social_channels_list['number_validation'] == 1) : ?> mystickyelement-social-text-input<?php endif; ?>"
                                   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][text]"
                                   value="<?php echo esc_attr(stripslashes($social_channel_value['text'])); ?>"
                                   placeholder="<?php echo esc_attr($social_channels_list['placeholder']) ?>"/>
                            <?php 
                                
                            }
                            ?>
							
                        </div>
                    </div>
                    <div class="myStickyelements-setting-wrap-list device-option">
                        <label><?php esc_html_e('Devices', 'mystickyelements'); ?></label>
                        <div class="px-wrap myStickyelements-inputs">
                            <ul>
                                <li>
                                    <label>
                                        <input type="checkbox"
                                               name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][desktop]"
                                               data-social-channel-view="<?php echo esc_attr($social_channel); ?>"
                                               value="1" class="social-channel-view-desktop"
                                               id="social_channel_<?php echo esc_attr($social_channel); ?>_desktop" <?php checked(@$social_channel_value['desktop'], '1'); ?> />&nbsp
                                        <span>
											<i class="fas fa-desktop">&nbsp</i><?php esc_html_e('Desktop', 'mystickyelements'); ?>
										</span>
                                    </label>
                                </li>
                                <li>
                                    <label>
                                        <input type="checkbox"
                                               name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][mobile]"
                                               data-social-channel-view="<?php echo esc_attr($social_channel); ?>"
                                               value="1" class="social-channel-view-mobile"
                                               id="social_channel_<?php echo esc_attr($social_channel); ?>_mobile" <?php checked(@$social_channel_value['mobile'], '1'); ?> />&nbsp
                                        <span>
											<i class="fas fa-mobile-alt"></i>&nbsp<?php esc_html_e('Mobile', 'mystickyelements'); ?>
										</span>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php if (!isset($social_channels_list['icon_new_tab'])) : ?>
                        <div class="myStickyelements-setting-wrap-list open-new-link-tab">
                            <label><?php esc_html_e('Open link in a new tab', 'mystickyelements'); ?></label>
                            <div class="px-wrap myStickyelements-inputs">
                                <input type="checkbox"
                                       name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][open_new_tab]"
                                       value="1" class="social-channel-view-desktop"
                                       id="social_channel_<?php echo esc_attr($social_channel); ?>_open_tab" <?php checked(@$social_channel_value['open_new_tab'], '1'); ?> />
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="myStickyelements-setting-wrap-list myStickyelements-channel-view">
                        <label>
                            <i class="fas fa-palette"></i>&nbsp&nbsp
                            <span class="social-setting"
                                  data-slug="<?php echo esc_html($social_channel); ?>"><?php esc_html_e('Appearance Settings', 'mystickyelements'); ?>&nbsp<i
                                        class="fas fa-chevron-down"></i></span>
                        </label>
                    </div>
                    <div class="social-channel-setting" style="display:none;">
                        <table>
                            <tr class="myStickyelements-custom-icon-image"
                                <?php if (!isset($social_channels_list['custom']) || ($channel_type != '' && $channel_type != 'custom')) : ?>style="display:none;" <?php endif; ?>>
                                <td colspan="2" style="text-align:left;">
                                    <div class="myStickyelements-custom-image-icon">
                                        <div class="myStickyelements-custom-image">
                                            <input type="button" data-slug="<?php echo esc_attr($social_channel); ?>"
                                                   name="social-channels-icon"
                                                   class="button-secondary social-custom-icon-upload-button"
                                                   value="<?php esc_html_e('Upload Custom Icon', 'mystickyelements'); ?>"/>

                                            <div id="social-channel-<?php echo esc_attr($social_channel); ?>-icon"
                                                 class="social-channel-icon" style="display:none; ">
                                                <img src="<?php echo esc_url($social_channel_value['custom_icon']) ?>"
                                                     id="social-channel-<?php echo esc_attr($social_channel); ?>-custom-icon-img"
                                                     width="38" height="38"/>
                                                <span class="social-channel-icon-close"
                                                      data-slug="<?php echo esc_attr($social_channel); ?>">x</span>
                                            </div>

                                            <input type="hidden"
                                                   id="social-channel-<?php echo esc_attr($social_channel); ?>-custom-icon"
                                                   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][custom_icon]"
                                                   value="<?php echo esc_url($social_channel_value['custom_icon']) ?>"/>
                                            <div class="myStickyelements-setting-wrap-list myStickyelements-stretch-icon-wrap">
                                                <label>
                                                    <input type="checkbox"
                                                           data-slug="<?php echo esc_attr($social_channel); ?>"
                                                           name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][stretch_custom_icon]"
                                                           value="1" <?php if (isset($social_channel_value['stretch_custom_icon']) && $social_channel_value['stretch_custom_icon'] == 1) {
                                                        echo 'checked="checked"';
                                                    } ?> />&nbsp;<?php _e('Stretch custom icon', 'mystickyelements'); ?>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="myStickyelements-custom-icon">
                                            <span>Or</span>
                                            <?php $fontawesome_icons = mystickyelements_fontawesome_icons(); ?>
                                            <select id="mystickyelements-<?php echo esc_attr($social_channel); ?>-custom-icon"
                                                    data-slug="<?php echo esc_attr($social_channel); ?>"
                                                    name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][fontawesome_icon]"
                                                    class="social-channel-fontawesome-icon">
                                                <option value=""><?php esc_html_e('Select FontAwesome Icon', 'mystickyelements'); ?></option>
                                                <?php foreach ($fontawesome_icons as $icons):
                                                    $icon_html = '<i class="' . $icons . '"></i>';
                                                    ?>
                                                    <option value="<?php echo esc_attr($icons) ?>" <?php selected($social_channel_value['fontawesome_icon'], $icons) ?>><?php echo esc_html($icons); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr <?php if (!isset($social_channels_list['custom']) || (isset($social_channels_list['custom_html']))) : ?>style="display:none;" <?php endif; ?>>
                                <td>
                                    <div class="myStickyelements-setting-wrap-list">
                                        <label><?php _e('Channel Type', 'mystickyelements'); ?></label>
                                        <div class="px-wrap myStickyelements-inputs">
                                            <select class="social-custom-channel-type"
                                                    name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][channel_type]"
                                                    data-id="social-channel-<?php echo esc_attr($social_channel); ?>"
                                                    data-slug="social-<?php echo esc_attr($social_channel); ?>">
                                                <option value="custom"
                                                        data-social-channel='<?php echo wp_json_encode($social_channels_list); ?>' <?php selected($social_channel_value['channel_type'], 'custom', true) ?>><?php _e('Custom channel', 'mystickyelements'); ?></option>
                                                <?php foreach (mystickyelements_social_channels() as $csc_key => $csc_val):
                                                    if (isset($csc_val['custom']) && $csc_val['custom'] == 1) {
                                                        continue;
                                                    }
													unset($csc_val['tooltip']);
                                                    ?>
                                                    <option value="<?php echo esc_attr($csc_key); ?>"
                                                            data-social-channel='<?php echo wp_json_encode($csc_val); ?>' <?php selected($social_channel_value['channel_type'], $csc_key, true) ?>><?php echo esc_html($csc_val['hover_text']); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <div class="myStickyelements-setting-wrap-list myStickyelements-background-color">
                                        <label><?php _e('Background Color', 'mystickyelements'); ?></label>
										<div class="px-wrap myStickyelements-inputs">
											<input type="text" data-slug="<?php echo esc_attr($social_channel); ?>"
												   id="social-<?php echo esc_attr($social_channel); ?>-bg_color"
												   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][bg_color]"
												   class="mystickyelement-color"
												   value="<?php echo esc_attr($social_channel_value['bg_color']); ?>"/>
										</div>
                                    </div>
                                    <?php if (isset($social_channels_list['icon_color']) && $social_channels_list['icon_color'] == 1) : ?>
                                        <div class="myStickyelements-setting-wrap-list myStickyelements-custom-icon-color">
                                            <label><?php _e('Icon Color', 'mystickyelements'); ?></label>
											<div class="px-wrap myStickyelements-inputs">
												<input type="text"
													   data-soical-icon="<?php echo esc_attr($social_channel); ?>"
													   id="social-<?php echo esc_attr($social_channel); ?>-icon_color"
													   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][icon_color]"
													   class="mystickyelement-color"
													   value="<?php echo esc_attr($social_channel_value['icon_color']); ?>"/>
											</div>
                                        </div>
                                    <?php endif; ?>
									<div class="myStickyelements-setting-wrap-list myStickyelements-custom-icon-text-color">
										<label><?php _e('Icon Text Color', 'mystickyelements'); ?></label>
										<div class="px-wrap myStickyelements-inputs">
											<input type="text"
												   data-soical-text-color="<?php echo esc_attr($social_channel); ?>"
												   id="social-<?php echo esc_attr($social_channel); ?>-icon_text_color"
												   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][icon_text_color]"
												   class="mystickyelement-color"
												   value="<?php echo esc_attr($social_channel_value['icon_text_color']); ?>"/>
										</div>
									</div>
                                    <div class="myStickyelements-setting-wrap-list">
                                        <label><?php _e('Icon Text', 'mystickyelements'); ?></label>
                                        <div class="px-wrap myStickyelements-inputs">
                                            <input type="text" class="myStickyelements-icon-text-input"
                                                   id="social-<?php echo esc_attr($social_channel); ?>-icon_text"
                                                   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][icon_text]"
                                                   value="<?php echo esc_attr($social_channel_value['icon_text']); ?>"
                                                   data-icontext="<?php echo esc_attr($social_channel); ?>"
                                                   placeholder="<?php esc_html_e('Enter text here...', 'mystickyelements'); ?>"/>
                                        </div>
                                    </div>
									
                                    <div class="myStickyelements-setting-wrap-list">
                                        <label><?php _e('Icon Text Size', 'mystickyelements'); ?></label>
                                        <div class="px-wrap myStickyelements-inputs">
                                            <input type="number" class="myStickyelements-icon-text-size"
                                                   id="social-<?php echo esc_attr($social_channel); ?>-icon_text_size"
                                                   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][icon_text_size]"
                                                   value="<?php echo esc_attr($social_channel_value['icon_text_size']); ?>"
                                                   min="0" data-icontextsize="<?php echo esc_attr($social_channel); ?>"
                                                   placeholder="<?php esc_html_e('Enter font size here...', 'mystickyelements'); ?>"/>
                                            <span class="input-px">PX</span>
                                        </div>
                                    </div>
                                    <div class="myStickyelements-setting-wrap-list myStickyelements-on-hover-text">
                                        <label><?php _e('Flyout Text', 'mystickyelements'); ?></label>
                                        <div class="px-wrap myStickyelements-inputs">
                                            <input type="text"
												   class="social_channels_hover_text"
                                                   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][hover_text]"
                                                   value="<?php echo esc_attr($social_channel_value['hover_text']); ?>"
                                                   data-icontext="<?php echo esc_attr($social_channel); ?>"
												   placeholder="<?php esc_html_e('Enter text here...', 'mystickyelements'); ?>"/>
                                        </div>
                                    </div>

                                    <div class="myStickyelements-setting-wrap-list myStickyelements-custom-pre-message"
                                         <?php if (!isset($social_channels_list['is_pre_set_message'])) : ?>style="display:none;" <?php endif; ?>>
                                        <label>
											<?php _e('Pre Set Message', 'mystickyelements'); ?>
											<div class="mystickyelements-custom-fields-tooltip myStickyelements-country-tooltip">
												<a href="javascript:void(0);" class="mystickyelements-tooltip mystickyelements-new-custom-btn"><i class="fas fa-info"></i></a>
												<p><?php esc_html_e('Add your own pre-set message that\'s automatically added to the user\'s message. You can also use merge tags and add the URL or the title of the current visitor\'s page. E.g. you can add the current URL of a product to the message so you know which product the visitor is talking about when the visitor messages you', 'mystickyelements'); ?></p>
											</div>											
										</label>
                                        <div class="px-wrap myStickyelements-inputs">
                                            <input type="text"
                                                   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][pre_set_message]"
                                                   value="<?php echo esc_attr($social_channel_value['pre_set_message']); ?>"
                                                   placeholder="<?php esc_html_e('Enter message here...', 'mystickyelements'); ?>"/>
											
											
											<span class="supported-tags mt-2 text-cht-gray-150">
												<a href="<?php echo admin_url("admin.php?page=my-sticky-elements-upgrade");?>" target="_blank">Upgrade to Pro 🎉</a> to use <label class="social-custom-tooltip social-tooltip"><span>{title}<span class="social-tooltip-popup"><?php esc_html_e( '{title} tag grabs the page title of the webpage', 'mystickyelements');?></span></span></label>
												 and  <label class="social-custom-tooltip social-tooltip"><span>{url}<span class="social-tooltip-popup"><?php esc_html_e( '{url} tag grabs the URL of the webpage', 'mystickyelements');?></span></span></label> tags</span>
                                        </div>
                                    </div>
									
									<div class="myStickyelements-setting-wrap-list myStickyelements-use-whatsapp-web" <?php if (!isset($social_channels_list['use_whatsapp_web'])) : ?>style="display:none;" <?php endif; ?>>
										<label><?php _e('Use WhatsApp Web directly on desktop', 'mystickyelements'); ?></label>
										<div class="px-wrap myStickyelements-inputs">
											<label class="social-tooltip">
												<span>
													<i class="fas fa-info"></i>
													<span class="social-tooltip-popup"><?php esc_html_e( 'Automatically send desktop visitors to WhatsApp Web by turning on the feature', 'mystickyelements');?></span>
												</span>
											</label>
											<input type="checkbox"
												   name="social-channels-tab[<?php echo esc_attr($social_channel); ?>][use_whatsapp_web]"
												   value="1" class="social-channel-view-desktop"
												   id="social_channel_<?php echo esc_attr($social_channel); ?>_use_whatsapp_web" <?php checked($social_channel_value['use_whatsapp_web'], '1'); ?> />
										</div>
									</div>
									
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="close-tooltip">
                        <svg class="social-channel-close" data-slug="<?php echo esc_attr($social_channel); ?>"
                             width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="12" fill="#FDF2F2"/>
                            <path d="M6.66666 8.66667H17.3333M16.6667 8.66667L16.0887 16.7613C16.0647 17.0977 15.9142 17.4125 15.6674 17.6424C15.4206 17.8722 15.0959 18 14.7587 18H9.24132C8.90408 18 8.57937 17.8722 8.33258 17.6424C8.08579 17.4125 7.93527 17.0977 7.91132 16.7613L7.33332 8.66667H16.6667ZM10.6667 11.3333V15.3333V11.3333ZM13.3333 11.3333V15.3333V11.3333ZM14 8.66667V6.66667C14 6.48986 13.9298 6.32029 13.8047 6.19526C13.6797 6.07024 13.5101 6 13.3333 6H10.6667C10.4898 6 10.3203 6.07024 10.1953 6.19526C10.0702 6.32029 9.99999 6.48986 9.99999 6.66667V8.66667H14Z"
                                  stroke="#C81E1E" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>

                        <span class="tooltiptext"><a href="#"
                                                     data-slug="<?php echo esc_attr($social_channel); ?>"><?php esc_html_e('Remove channel', 'mystickyelements'); ?></a></span>
                    </div>
                </div>
                <!-- end social channel tabs-->
                <?php

            }
            if (isset($_POST['is_ajax']) && $_POST['is_ajax'] == true) {
                wp_die();
            }
		}	
		
		/*
		 * My Sticky Elements Integration page
		 *
		 */
		public function mystickyelements_admin_integration_page(){
			$is_shown = MSE_SIGNUP_CLASS::check_modal_status();;
			if($is_shown) {
				/* Signup Form When first time activate plugin */				
				include_once MYSTICKYELEMENTS_PATH . '/admin/email-signup.php';
				
			} else {			
				include( 'mystickyelements-admin-integration.php' );
			}	 		
		}

		/*
		 * My Sticky Elements Contact Leads
		 *
		 */
		public function mystickyelements_admin_leads_page(){
			global $wpdb;
			
			$is_shown = MSE_SIGNUP_CLASS::check_modal_status();;
			if($is_shown) {
				/* Signup Form When first time activate plugin */				
				include_once MYSTICKYELEMENTS_PATH . '/admin/email-signup.php';				
			} else {
				$where_search = '';
				$whereCond = [];
				$table_name = $wpdb->prefix . "mystickyelement_contact_lists";
				
				if ( isset($_POST['stickyelement-contatc-submit']) && !wp_verify_nonce( $_POST['stickyelement-contatc-submit'], 'stickyelement-contatc-submit' ) ) {

					echo '<div class="error settings-error notice is-dismissible "><p><strong>' . esc_html__('Unable to complete your request','mystickyelements'). '</p></strong></div>';

				} else if ( isset($_POST['stickyelement-contatc-submit']) && wp_verify_nonce( $_POST['stickyelement-contatc-submit'], 'stickyelement-contatc-submit' )  ) {
					if ( isset($_POST['delete_message']) && !empty($_POST['delete_message']) && !wp_doing_ajax()) {
					
						$count = count($_POST['delete_message']);					
						foreach ( $_POST['delete_message'] as $key=>$ID) {
							if ( is_int($ID) ) {
								$ID = sanitize_text_field($ID);
								$query = "DELETE FROM {$table_name} WHERE ID = %d";
								$query = $wpdb->prepare($query, $ID);
								//$wpdb->query($query);							
								$wpdb->delete( $table_name, array( 'ID' => sanitize_text_field($ID) ), array( '%d' ) );
							}
						}
						echo '<div class="updated settings-error notice is-dismissible "><p><strong>' . esc_html__( $count . ' message deleted.','mystickyelements'). '</p></strong></div>';
					
					}
				}
				?>
				<div class="wrap mystickyelement-contact-wrap">
					<h2><?php _e( 'Contact Form Leads', 'mystickyelements' ); ?></h2>
					<p class="description">
						<strong><?php esc_html_e("Contact's data is saved locally do make backup or export before uninstalling plugin", 'mystickyelements');?></strong>
					</p>
					<div>


					<div class="mystickyelement-btnmbox">
							<div class="mystickyelement-btnbx">
								<strong><?php esc_html_e('Download & Export All Subscriber to CSV file:','mystickyelements' );?> </strong>

								<a href="<?php echo plugins_url('mystickyelements-contact-leads.php?download_file=mystickyelements_contact_leads.csv',__FILE__); ?>" class="wpappp_buton" id="wpappp_export_to_csv" value="Export to CSV" href="#"><?php esc_html_e('Download & Export to CSV', 'mystickyelements' );?></a>

							</div>
							<div class="mystickyelement-btnbx">
								<strong><?php esc_html_e('Delete All Subscibers from Database:','mystickyelements');?> </strong>

								<input type="button" class="wpappp_buton" id="mystickyelement_delete_all_leads" value="<?php esc_attr_e('Delete All Data', 'mystickyelements' );?>" />
							</div>	
						</div>

						
						<input type="hidden" id="delete_nonce" name="delete_nonce" value="<?php echo wp_create_nonce("mysticky_elements_delete_nonce") ?>" />
					</div>
					<?php
						if ( isset($_REQUEST['search-contact']) && $_REQUEST['search-contact'] != '' ) {
							$searchContent = sanitize_text_field($_REQUEST['search-contact']);
							$where_search = "WHERE contact_name like %s OR contact_email like %s OR contact_phone like %s OR contact_message like %s";
							$whereCond = [
								'%'.$wpdb->esc_like($searchContent).'%',
								'%'.$wpdb->esc_like($searchContent).'%',
								'%'.$wpdb->esc_like($searchContent).'%',
								'%'.$wpdb->esc_like($searchContent).'%'
							];
						}
					?>
					<div>					
						<div class="tablenav top">
							<form action="<?php echo admin_url("admin.php?page=my-sticky-elements-leads");?>" method="post">
							<div class="alignleft actions bulkactions">
								<select name="action" id="bulk-action-selector-top">
								<option value="">Bulk Actions</option>
								<option value="delete_message">Delete</option>								
								</select>
								<input type="submit" id="doaction" class="button action" value="Apply">
								<?php wp_nonce_field( 'stickyelement-contatc-submit', 'stickyelement-contatc-submit' );  ?>
							</div>
							</form>
							<form action="<?php echo admin_url("admin.php?page=my-sticky-elements-leads");?>" method='get'>				
								<input type="hidden" name="page" value='my-sticky-elements-leads'/>
								<p class="search-box">
									<label class="screen-reader-text" for="post-search-input"><?php esc_html_e( 'Search', 'mystickyelements');?></label>
									<input type="search" id="post-search-input" name="search-contact" value="<?php echo esc_attr((isset($_GET['search-contact']) && $_GET['search-contact'] != '') ? esc_attr($_GET['search-contact']) : ''); ?>">
									<input type="submit" id="search-submit" class="button" value="<?php esc_html_e( 'Search', 'mystickyelements');?>">
								</p>								
							</form>
						</div>
							<table border="1" class="responstable">
								<tr>
									<th style="width:1%"><?php esc_html_e( 'Bulk', 'mystickyelements' );?></th>
									<th><?php esc_html_e( 'ID', 'mystickyelements');?></th>
									<th><?php esc_html_e( 'Widget Name', 'mystickyelements');?></th>
									<th><?php esc_html_e( 'Name', 'mystickyelements');?></th>
									<th><?php esc_html_e( 'Phone', 'mystickyelements');?></th>
									<th><?php esc_html_e( 'Email', 'mystickyelements');?></th>
									<th><?php esc_html_e( 'Option', 'mystickyelements');?></th>
									<th><?php esc_html_e( 'Message', 'mystickyelements');?></th>
									<th><?php esc_html_e( 'Consent', 'mystickyelements');?></th>
									<?php
									if (!empty($custom_fields)){
										foreach( $custom_fields as $value ) {
											?>
											<th><?php echo esc_attr($value);?></th>
											<?php
										}
									}
									?>
									<th><?php esc_html_e( 'Date', 'mystickyelements');?></th>
									<th><?php esc_html_e( 'URL', 'mystickyelements');?></th>
									<th><?php esc_html_e( 'IP Address', 'mystickyelements');?></th>
									<th style="width:11%"><?php esc_html_e( 'Delete', 'mystickyelements');?></th>
								</tr>
								<?php
								$items_per_page 	= 20;
								$page             	= ( isset( $_GET['cpage'] ) ) ? abs( (int) $_GET['cpage'] ) : 1;
								$offset         	= ( $page * $items_per_page ) - $items_per_page;

								$customPagHTML     	= "";
								if(!empty($whereCond)) {
									$total_query     	= "SELECT count(*) FROM {$table_name} {$where_search} ORDER BY ID DESC";
									$total_query        = $wpdb->prepare($total_query, $whereCond);

									$whereCond[]        = $offset;
									$whereCond[]        = $items_per_page;
									$query 				= "SELECT * FROM {$table_name} {$where_search} ORDER BY ID DESC LIMIT %d, %d";
									$query              = $wpdb->prepare($query, $whereCond);

								} else {
									$total_query     	= "SELECT count(*) FROM {$table_name} ORDER BY ID DESC";

									$whereCond          = [$offset, $items_per_page];
									$query 				= "SELECT * FROM {$table_name} ORDER BY ID DESC LIMIT %d, %d";
									$query              = $wpdb->prepare($query, $whereCond);
								}
								$total             	= $wpdb->get_var( $total_query );
								$result         	= $wpdb->get_results( $query );
								$total_page         = ceil($total / $items_per_page);
								if($result){
									foreach ( $result as $res ) { ?>
										<tr>
											<td><input id="cb-select-80" class="cb-select-blk" type="checkbox" name="delete_message[]" value="<?php echo esc_attr($res->ID);?>"></td>
											<td><?php echo esc_attr($res->ID);?></td>
											<td><?php echo esc_attr(@$res->widget_element_name);?></td>
											<td><?php echo esc_attr($res->contact_name);?></td>
											<td><?php echo esc_attr($res->contact_phone);?></td>
											<td><?php echo esc_attr($res->contact_email);?></td>
											<td><?php echo esc_attr($res->contact_option);?></td>
											<td><?php echo wpautop(esc_attr($res->contact_message));?></td>
											<td><?php echo esc_attr(( isset($res->consent_checkbox) && $res->consent_checkbox == 1 ) ? "True" : "False");?></td>
											<?php
											if (!empty($custom_fields)){
												$custom_field = json_decode($res->custom_fields, true );
												foreach( $custom_fields as $value ) {
													?>
													<td>
														<ul class="leads-img-list">
														<?php 
														//if( isset( $custom_field[$value] ) && $custom_field[$value] != '' && strpos( $custom_field[$value], 'http' ) !== false ) {													
														if( isset($custom_field[$value]) && $custom_field[$value] != '' ){
															if( !is_array($custom_field[$value]) ){
																$custom_field[$value] = array($custom_field[$value]); 
															}
															foreach( $custom_field[$value] as $key => $index_val ){
																if( isset( $custom_field[$value][$key] ) && $custom_field[$value][$key] != '' && ( substr( $custom_field[$value][$key], 0, 7 ) == 'http://' || substr( $custom_field[$value][$key], 0, 7 ) == 'https:/' ) ) {	
																	
																	$parts = parse_url($custom_field[$value][$key]);
																	if ( isset($parts['path']) ) {
																		echo "<li>";
																		$file_name = basename($parts['path']);
																		$file_url = '<a href="'.esc_url($custom_field[$value][$key]).'" target="_blank" download >'.$file_name.'</a>';
																		echo esc_attr(( isset( $custom_field[$value][$key] ) && $custom_field[$value][$key] != '' ) ? $file_url : $custom_field[$value][$key]);
																		echo "</li>";
																	} else {
																		echo esc_attr($custom_field[$value][$key]);
																	}
																} else {
																	echo esc_attr(( isset( $custom_field[$value][$key] ) && $custom_field[$value][$key] != '' ) ? wp_kses_post($custom_field[$value][$key]) : '');
																} 
															}
														}
														?>
														</ul>
													</td>
													<?php
												}
											}
											?>
											<td><?php echo esc_attr( isset($res->message_date)  ? $res->message_date : '-' );?></td>
											<td>
												<?php if ( $res->page_link) :?>
												<a href="<?php echo esc_url($res->page_link);?>" target="_blank"><i class="fas fa-external-link-alt"></i></a>
												<?php endif;?>
											</td>
											<td>
												<?php echo @$res->ip_address; ?>
											</td>
											<td>
												<input type="button" data-delete="<?php echo esc_attr($res->ID);?>" class="mystickyelement-delete-entry" value="<?php esc_attr_e('Delete', 'mystickyelements');?>" />
											</td>
										</tr>
									<?php }
								} else { ?>
									<tr>
										<td colspan="6" align="center">
											<p class="mystickyelement-no-contact"> <?php esc_html_e('No Contact Form Leads Found!','mystickyelements');?>
											</p>
										</td>
									</tr>
								<?php }	?>

							</table>
							<?php if($total_page > 1){ ?>
								<div class="contactleads-pagination">			
									<?php 
									$big = 999999999; // need an unlikely integer			
									echo paginate_links( array(
										'base' => add_query_arg( 'cpage', '%#%' ),
										'format' => '',
										'current' => $page,
										'total' =>  $total_page
									) );?>
								</div>
							<?php }?>
						</form>
					</div>
				</div>
				<?php
			} 
		}
		
		public function mystickyelements_recommended_plugins(){
			$is_shown = MSE_SIGNUP_CLASS::check_modal_status();;
			if($is_shown) {
				/* Signup Form When first time activate plugin */				
				include_once MYSTICKYELEMENTS_PATH . '/admin/email-signup.php';			
			} else {
				include_once 'recommended-plugins.php';
			} 
		}
		
		public function mystickyelements_manage_chatway_plugin() {
			?>
			<script>
				window.location.href= '<?php echo admin_url( "admin.php?page=chatway" )?>'
			</script>
			<?php
			exit;
		}
		public function mystickyelements_install_chatway_plugin() {
			include_once 'admin/chatway-plugin.php';			
		}

		
		/*
		 * My Sticky Elements Create New Widget
		 *
		 */
		public function mystickyelements_admin_new_widget_page(){
			//  update 2nd time if update mail is not submited by user
			$is_shown = MSE_SIGNUP_CLASS::check_modal_status();;
			if($is_shown) {
				/* Signup Form When first time activate plugin */				
				include_once MYSTICKYELEMENTS_PATH . '/admin/email-signup.php';	
				return;		
			}

			$upgrade_url = admin_url("admin.php?page=my-sticky-elements-upgrade");
			?>
			<div class="mystickyelement-new-widget-wrap">
				<?php include_once MYSTICKYELEMENTS_PATH . 'mystickyelements-widget.php';?>				
			</div>
			<?php 
		}

		public function mystickyelement_delete_db_record(){
			global $wpdb;
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(0); 
			}
			check_ajax_referer( 'mystickyelements', 'wpnonce' );
			if ( isset($_POST['ID']) && $_POST['ID'] != '' && wp_verify_nonce($_POST['delete_nonce'], "mysticky_elements_delete_nonce") ) {
				$ID = sanitize_text_field($_POST['ID']);
				$table = $wpdb->prefix . 'mystickyelement_contact_lists';
				$ID = self::sanitize_options($ID, "sql");
				$delete_sql = $wpdb->prepare("DELETE FROM {$table} WHERE id = %d",$ID);
				$delete = $wpdb->query($delete_sql);
			}

			if ( isset($_POST['all_leads']) && $_POST['all_leads'] == 1 && wp_verify_nonce($_POST['delete_nonce'], "mysticky_elements_delete_nonce")) {
				$table = $wpdb->prefix . 'mystickyelement_contact_lists';
				$delete = $wpdb->query("TRUNCATE TABLE $table");
			}
			wp_die();
		}
		
		public function myStickyelements_intro_popup_action() {
			if( !empty( $_REQUEST['nonce'] ) && wp_verify_nonce( $_REQUEST['nonce'], 'myStickyelements_update_popup_status' ) ) {
				update_option( "mystickyelements_intro_popup", "hide" );
			}
			echo esc_attr("1");
			die;
		}
		
		public function mystickyelements_admin_send_message_to_owner() {
			$response = array();
			$response['status'] = 0;
			$response['error'] = 0;
			$response['errors'] = array();
			$response['message'] = "";
			$errorArray = [];
			$errorMessage = esc_html__("%s is required", "mystickyelements");
			$postData = $_POST;
			if(!isset($postData['textarea_text']) || trim($postData['textarea_text']) == "") {
				$error = array(
					"key"   => "textarea_text",
					"message" => esc_html__("Please enter your message",'mystickyelements')
				);
				$errorArray[] = $error;
			}
			if(!isset($postData['user_email']) || trim($postData['user_email']) == "") {
				$error = array(
					"key"   => "user_email",
					"message" => sprintf($errorMessage,__("Email",'mystickyelements'))
				);
				$errorArray[] = $error;
			} else if(!filter_var($postData['user_email'], FILTER_VALIDATE_EMAIL)) {
				$error = array(
					'key' => "user_email",
					"message" => "Email is not valid"
				);
				$errorArray[] = $error;
			}
			if(empty($errorArray)) {
				if(!isset($_REQUEST['nonce']) || empty($_REQUEST['nonce'])) {
					$error = array(
						'key' => "nonce",
						"message" => "Your request is not valid"
					);
					$errorArray[] = $error;
				} else if(!wp_verify_nonce($_REQUEST['nonce'], "mystickyelements_send_message_to_owner")) {
					$error = array(
						'key' => "nonce",
						"message" => "Your request is not valid"
					);
					$errorArray[] = $error;
				}
			}
			if(empty($errorArray)) {
				global $current_user;
				$text_message = $postData['textarea_text'];
				$email = $postData['user_email'];
				$domain = site_url();
				$user_name = $current_user->first_name." ".$current_user->last_name;

				$response['status'] = 1;

				/* sending message to Crisp */
				$post_message = array();

				$message_data = array();
				$message_data['key'] = "Plugin";
				$message_data['value'] = "My Sticky Elements";
				$post_message[] = $message_data;

				$message_data = array();
				$message_data['key'] = "Domain";
				$message_data['value'] = $domain;
				$post_message[] = $message_data;

				$message_data = array();
				$message_data['key'] = "Email";
				$message_data['value'] = $email;
				$post_message[] = $message_data;

				$message_data = array();
				$message_data['key'] = "Message";
				$message_data['value'] = $text_message;
				$post_message[] = $message_data;

				$api_params = array(
					'domain' => $domain,
					'email' => $email,
					'url' => site_url(),
					'name' => $user_name,
					'message' => $post_message,
					'plugin' => "MSE",
					'type' => "Need Help",
				);

				/* Sending message to Crisp API */
				$crisp_response = wp_safe_remote_post("https://premioapps.com/premio/send-message-api.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => true));

				if (is_wp_error($crisp_response)) {
					wp_safe_remote_post("https://premioapps.com/premio/send-message-api.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => false));
				}
			} else {
				$response['error'] = 1;
				$response['errors'] = $errorArray;
			}
			wp_send_json($response);
			wp_die();
		}
		
		public function mystickyelement_widget_status() {
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(0); 
			}
			check_ajax_referer( 'mystickyelements', 'wpnonce' );
			
			if ( isset($_POST['widget_id']) && $_POST['widget_id'] != '' && isset($_POST['widget_status']) && $_POST['widget_status'] != ''  ) {
				$stickyelements_widgets = get_option('stickyelements_widgets');
				$widget_id = $_POST['widget_id'];
				$widget_status = $_POST['widget_status'];
				$stickyelements_widgets[$widget_id]['status'] = $widget_status;				
				update_option( 'stickyelements_widgets',$stickyelements_widgets);
				
			}
			wp_die();
		}
		
		public function my_sticky_elements_bulks(){
			global $wpdb;

			check_ajax_referer( 'mystickyelements', 'wpnonce' );
			
			if( isset($_POST['wpnonce']) ){
				$bulks = isset($_POST['bulks']) ? $_POST['bulks'] : array();
				foreach( $bulks as $key => $bulk ){
					$ID = sanitize_text_field($bulk);
					$table = $wpdb->prefix . 'mystickyelement_contact_lists';
					$ID = self::sanitize_options($ID, "sql");
					$delete_sql = $wpdb->prepare("DELETE FROM {$table} WHERE id = %d",$ID);
					$delete = $wpdb->query($delete_sql);		
				}
			}
			wp_die();
		}
		
		public function mystickyelement_widget_rename(){
			
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(0); 
			}
			check_ajax_referer( 'mystickyelements', 'wpnonce' );
			
			if ( isset($_POST['widget_id']) && $_POST['widget_id'] != '' && isset($_POST['widget_rename']) && $_POST['widget_rename'] != ''  ) {
				
				$stickyelements_widgets = get_option('mystickyelements-widgets');
				$widget_id = $_POST['widget_id'];
				$widget_rename = $_POST['widget_rename'];
				
				$stickyelements_widgets[$widget_id] = $widget_rename;				
				update_option( 'mystickyelements-widgets' ,$stickyelements_widgets);
			}
			wp_die();
		}
		
		public function mystickyelement_widget_delete(){
			
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(0); 
			}
			check_ajax_referer( 'mystickyelements', 'wpnonce' );
			
			if ( isset($_POST['widget_id']) && $_POST['widget_id'] != '' && isset($_POST['widget_delete']) && $_POST['widget_delete'] == 1  ) {
				
				$elements_widgets = get_option( 'mystickyelements-widgets' );
				$stickyelements_widgets_status = get_option('stickyelements_widgets');
				$mystickyelements_widget = self::sanitize_options($_POST['widget_id']);				
				
				foreach( $elements_widgets as $key => $widget_value ){
					$element_widget_no = '';
					if ( $key != 0 ) {
						$element_widget_no = '-' . esc_attr($key);
					}
					delete_option( 'mystickyelements-contact-field' . $element_widget_no );
					delete_option( 'mystickyelements-contact-form' . $element_widget_no );
					delete_option( 'mystickyelements-social-channels' . $element_widget_no );
					delete_option( 'mystickyelements-social-channels-tabs' . $element_widget_no );
					delete_option( 'mystickyelements-general-settings' . $element_widget_no );
				}
				
				delete_option( 'mystickyelements-widgets' );
				delete_option( 'stickyelements_widgets' );
				
			}
			wp_die();
		}
		
		
		public function mystickyelements_deactivate() {
			global $pagenow;

			if ( 'plugins.php' !== $pagenow ) {
				return;
			}			

			include MYSTICKYELEMENTS_PATH . 'mystickyelements-deactivate-form.php';
		}
		
		public function mystickyelements_plugin_deactivate() {
			global $current_user;
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die(0); 
			}
			check_ajax_referer( 'mystickyelements_deactivate_nonce', 'nonce' );
			
			$postData = $_POST;
			$errorCounter = 0;
			$response = array();
			$response['status'] = 0;
			$response['message'] = "";
			$response['valid'] = 1;
			if(!isset($postData['reason']) || empty($postData['reason'])) {
				$errorCounter++;
				$response['message'] = "Please provide reason";
			} else if(!isset($postData['reason']) || empty($postData['reason'])) {
                $errorCounter++;
                $response['message'] = "Please provide reason";
            } else {
				$nonce = $postData['nonce'];
				if(!wp_verify_nonce($nonce, 'mystickyelements_deactivate_nonce')) {
					$response['message'] = esc_html__("Your request is not valid", "mystickyelements");
					$errorCounter++;
					$response['valid'] = 0;
				}
			}
			if($errorCounter == 0) {
				global $current_user;
				$plugin_info = get_plugin_data( MYSTICKYELEMENTS_PATH. 'mystickyelements.php');
				$postData = $_POST;
				$email = "none@none.none";

                if (isset($postData['email_id']) && !empty($postData['email_id']) && filter_var($postData['email_id'], FILTER_VALIDATE_EMAIL)) {
                    $email = $postData['email_id'];
                }
				$domain = site_url();
				$user_name = $current_user->first_name . " " . $current_user->last_name;

				$response['status'] = 1;

				/* sending message to Crisp */
				$post_message = array();

				$message_data = array();
				$message_data['key'] = "Plugin";
				$message_data['value'] = "My Sticky Elements";
				$post_message[] = $message_data;

				$message_data = array();
				$message_data['key'] = "Plugin Version";
				$message_data['value'] = $plugin_info['Version'];
				$post_message[] = $message_data;

				$message_data = array();
				$message_data['key'] = "Domain";
				$message_data['value'] = $domain;
				$post_message[] = $message_data;

				$message_data = array();
				$message_data['key'] = "Email";
				$message_data['value'] = $email;
				$post_message[] = $message_data;

				$message_data = array();
				$message_data['key'] = "WordPress Version";
				$message_data['value'] = esc_attr(get_bloginfo('version'));
				$post_message[] = $message_data;

				$message_data = array();
				$message_data['key'] = "PHP Version";
				$message_data['value'] = PHP_VERSION;
				$post_message[] = $message_data;

				$message_data = array();
				$message_data['key'] = "Message";
				$message_data['value'] = $postData['reason'];
				$post_message[] = $message_data;

				$api_params = array(
					'domain' => $domain,
					'email' => $email,
					'url' => site_url(),
					'name' => $user_name,
					'message' => $post_message,
					'plugin' => "MSE",
					'type' => "Uninstall",
				);

				/* Sending message to Crisp API */
				$crisp_response = wp_safe_remote_post("https://premioapps.com/premio/send-message-api.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => true));

				if (is_wp_error($crisp_response)) {
					wp_safe_remote_post("https://premioapps.com/premio/send-message-api.php", array('body' => $api_params, 'timeout' => 15, 'sslverify' => false));
				}
			}
			echo json_encode($response);
			wp_die();
		}
		
		/*
		 * clear cache when any option is updated 
		 *
		 */	
		public function mystickyelements_clear_all_caches(){
			
			try {
				global $wp_fastest_cache;
				// if W3 Total Cache is being used, clear the cache
				if (function_exists('w3tc_flush_all')) {
					w3tc_flush_all();                
				} 
				/* if WP Super Cache is being used, clear the cache */
				if (function_exists('wp_cache_clean_cache')) {
					global $file_prefix, $supercachedir;
					if (empty($supercachedir) && function_exists('get_supercache_dir')) {
						$supercachedir = get_supercache_dir();
					}
					wp_cache_clean_cache($file_prefix);
				} 
				
				if (class_exists('WpeCommon')) {
					//be extra careful, just in case 3rd party changes things on us
					if (method_exists('WpeCommon', 'purge_memcached')) {
						//WpeCommon::purge_memcached();
					}
					if (method_exists('WpeCommon', 'clear_maxcdn_cache')) {
						//WpeCommon::clear_maxcdn_cache();
					}
					if (method_exists('WpeCommon', 'purge_varnish_cache')) {
						//WpeCommon::purge_varnish_cache();
					}
				}
				
				if (method_exists('WpFastestCache', 'deleteCache') && !empty($wp_fastest_cache)) {
					$wp_fastest_cache->deleteCache();
				} 
				if (function_exists('rocket_clean_domain')) {
					rocket_clean_domain();
					// Preload cache.
					if (function_exists('run_rocket_sitemap_preload')) {
						run_rocket_sitemap_preload();
					}
				} 
				
				if (class_exists("autoptimizeCache") && method_exists("autoptimizeCache", "clearall")) {
					autoptimizeCache::clearall();
				}
				
				if (class_exists("LiteSpeed_Cache_API") && method_exists("autoptimizeCache", "purge_all")) {
					LiteSpeed_Cache_API::purge_all();
				}
				
				if ( class_exists( '\Hummingbird\Core\Utils' ) ) {
	
					$modules   = \Hummingbird\Core\Utils::get_active_cache_modules();					
					foreach ( $modules as $module => $name ) {
						$mod = \Hummingbird\Core\Utils::get_module( $module );

						if ( $mod->is_active() ) {
							if ( 'minify' === $module ) {
								$mod->clear_files();
							} else {
								$mod->clear_cache();
							}
						}
					}	
				}
				
				/* Clear nitropack plugin cache */
				if ( function_exists( 'nitropack_purge_cache' ) && function_exists( 'nitropack_sdk_purge' ) ) {
					nitropack_sdk_purge(NULL, NULL, 'Manual purge of all pages');
				}

			} catch (Exception $e) {
				return 1;
			}
		}
		
		public function mystickyelements_review_box() {			
			
			if (current_user_can('manage_options')) {
				$nonce = filter_input(INPUT_POST, 'nonce');
				$days  = filter_input(INPUT_POST, 'days');
				if (!empty($nonce) && wp_verify_nonce($nonce, 'mystickyelements')) {
					if ($days == -1) {
						add_option("mystickyelements_hide_review_box", "1");
						update_option("get_mystickyelements_page_views", -1);
					} else {
						$date = date("Y-m-d", strtotime("+".$days." days"));
						update_option("mystickyelements_show_review_box_after", $date);
						update_option("get_mystickyelements_page_views", 4);
					}
				}				
			}
			wp_die();
		}
		public function mystickyelements_review_box_message (){
			if (current_user_can('manage_options')) {
				$nonce = filter_input(INPUT_POST, 'nonce');
				if (!empty($nonce) && wp_verify_nonce($nonce, 'mystickyelements')) {
					add_option("mystickyelements_hide_review_box", "1");
					update_option("get_mystickyelements_page_views", -1);
					$rating  = filter_input(INPUT_POST, 'rating');
					$message = filter_input(INPUT_POST, 'message');
					
					if ( $message != '' ) { 
						global $current_user;
						$postMessage = [];

						$domain    = site_url();
						$user_name = $current_user->first_name." ".$current_user->last_name;
						$email     = $current_user->user_email;

						$messageData          = [];
						$messageData['key']   = "email";
						$messageData['value'] = $email;
						$postMessage[]        = $messageData;
						
						$messageData          = [];
						$messageData['key']   = "website";
						$messageData['value'] = $domain;
						$postMessage[]        = $messageData;

						$messageData          = [];
						$messageData['key']   = "message";
						$messageData['value'] = $message;
						$postMessage[]        = $messageData;
						
						$messageData          = [];
						$messageData['key']   = "rating";
						$messageData['value'] = $rating;
						$postMessage[]        = $messageData;

						$apiParams = [
							'title'   => 'Review for My Sticky Elements WordPress',
							'domain'  => $domain,
							'email'   => "contact@premio.io",
							'url'     => site_url(),
							'name'    => $user_name,
							'message' => $postMessage,
							'plugin'  => 'My Sticky Elements',
							'type'    => "Review",
						];

						// Sending message to Crisp API
						$apiResponse = wp_safe_remote_post("https://premioapps.com/premio/send-feedback-api.php", ['body' => $apiParams, 'timeout' => 15, 'sslverify' => true]);

						if (is_wp_error($apiResponse)) {
							wp_safe_remote_post("https://premioapps.com/premio/send-feedback-api.php", ['body' => $apiParams, 'timeout' => 15, 'sslverify' => false]);
						}
					}
				}
				wp_die();
			}
		}

 
	}
}


if( is_admin() ) {
    $my_settings_page = new MyStickyElementsPage_pro();
    //include_once "class-review-box.php";
	include_once "class-upgrade-box.php";
}
