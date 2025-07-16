<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
/**
 * @since      4.0.2
 * Description: Conversios Onboarding page, It's call while active the plugin
 */
if (!class_exists('Conversios_Onboarding')) {
	class Conversios_Onboarding
	{
		protected $TVC_Admin_Helper;
		protected $subscriptionId;
		protected $version;
		protected $connect_url;
		protected $customApiObj;
		protected $app_id = CONV_APP_ID;
		protected $plan_id = 1;
		protected $tvc_data = array();
		protected $last_login;
		protected $is_refresh_token_expire;
		protected $ee_options = array();
		public function __construct()
		{
			if (!is_admin()) {
				return;
			}
			$this->includes();

			/**
			 *  Set Var
			 */
			$this->version = PLUGIN_TVC_VERSION;
			$this->customApiObj = new CustomApi();
			$this->TVC_Admin_Helper = new TVC_Admin_Helper();
			$ee_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
			//$this->url = $this->TVC_Admin_Helper->get_onboarding_page_url();
			$this->connect_url =  $this->TVC_Admin_Helper->get_connect_url();
			$this->tvc_data = $this->TVC_Admin_Helper->get_store_data();
			$this->is_refresh_token_expire = false;
			//get last onboarded user settings
			$this->ee_options = $this->TVC_Admin_Helper->get_ee_options_settings();
			//$this->convBadgeVal = isset($this->ee_options['conv_show_badge']) ? $this->ee_options['conv_show_badge'] : "";

			if (isset($ee_additional_data['ee_last_login']) && $ee_additional_data['ee_last_login'] != "") {
				$this->last_login = $ee_additional_data['ee_last_login'];
				$current = current_time('timestamp');
				$diffrent_days = floor(($current - $this->last_login) / (60 * 60 * 24));
				if ($diffrent_days < 100) {
					$this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();

					// For google account
					$g_mail = get_option('ee_customer_gmail');
					$this->tvc_data['g_mail'] = "";
					if ($g_mail) {
						$this->tvc_data['g_mail'] = sanitize_email($g_mail);
					}
					// For microsoft account
					$microsoft_mail = get_option('ee_customer_msmail');
					$this->tvc_data['microsoft_mail'] = "";
					if ($microsoft_mail) {
						$this->tvc_data['microsoft_mail'] = sanitize_email($microsoft_mail);
					}
				}
			}
		}
		public function includes()
		{
			if (!class_exists('CustomApi.php')) {
				require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
			}
		}

		public function get_countries($user_country)
		{
			global $wp_filesystem;
			$getCountris = $wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");

			$contData = json_decode($getCountris);
			if (!empty($user_country)) {
				$data = "<select id='selectCountry' name='country' class='form-control slect2bx' readonly='true'>";
				$data .= "<option value=''>" . esc_html__("Please select country", "enhanced-e-commerce-for-woocommerce-store") . "</option>";
				foreach ($contData as $key => $value) {
					$selected = ($value->code == $user_country) ? "selected='selected'" : "";
					$data .= "<option value=" . esc_attr($value->code) . " " . esc_attr($selected) . " >" . esc_html($value->name) . "</option>";
				}
				$data .= "</select>";
			} else {
				$data = "<select id='selectCountry' name='country' class='form-control slect2bx'>";
				$data .= "<option value=''>" . esc_html__("Please select country", "enhanced-e-commerce-for-woocommerce-store") . "</option>";
				foreach ($contData as $key => $value) {
					$data .= "<option value=" . esc_attr($value->code) . ">" . esc_html($value->name) . "</option>";
				}
				$data .= "</select>";
			}
			return $data;
		}
		public function is_checked($tracking_option, $is_val)
		{
			if ($tracking_option == $is_val) {
				return 'checked="checked"';
			}
		}
		/**
		 * onboarding page HTML
		 */
		public function welcome_screen()
		{
		}
	} //End Conversios_Onboarding Class
}
new Conversios_Onboarding();