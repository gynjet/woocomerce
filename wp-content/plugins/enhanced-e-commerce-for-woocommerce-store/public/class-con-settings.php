<?php
class Con_Settings
{
    protected $ga_EC;
    protected $is_WC;
    //content grouping start
    protected $ga_optimize_id;
    protected $ga_CG;
    //content grouping end
    public $tvc_eeVer = PLUGIN_TVC_VERSION;
    protected $ga_LC;
    protected $ga_Dname;

    protected $ga_id;
    protected $gm_id;
    protected $google_ads_id;
    protected $google_merchant_id;

    protected $tracking_option;

    protected $ga_ST;
    protected $ga_eeT;
    protected $ga_gUser;

    protected $ga_imTh;

    protected $ga_IPA;
    //protected $ga_OPTOUT;

    protected $ads_ert;
    protected $ads_edrt;
    protected $ads_tracking_id;

    protected $ga_PrivacyPolicy;

    protected $ga_gCkout;
    protected $ga_DF;
    protected $tvc_options;
    protected $TVC_Admin_Helper;
    protected $remarketing_snippet_id;
    protected $remarketing_snippets;
    protected $conversio_send_to;
    protected $ee_options;
    protected $fb_pixel_id;
    protected $c_t_o; //custom_tracking_options
    protected $tracking_method;

    protected $microsoft_ads_pixel_id;
    protected $twitter_ads_pixel_id;

    protected $twitter_ads_form_submit_event_id;
    protected $twitter_ads_email_click_event_id;
    protected $twitter_ads_phone_click_event_id;
    protected $twitter_ads_address_click_event_id;
    protected $twitter_ads_add_to_cart_event_id;
    protected $twitter_ads_checkout_initiated_event_id;
    protected $twitter_ads_payment_info_event_id;
    protected $twitter_ads_purchase_event_id;

    protected $pinterest_ads_pixel_id;
    protected $snapchat_ads_pixel_id;
    protected $linkedin_insight_id;
    protected $tiKtok_ads_pixel_id;

    protected $want_to_use_your_gtm;
    protected $use_your_gtm_id;
    protected $tiKtok_business_id;
    protected $tiktok_access_token;
    protected $plan_id;
    protected $net_revenue_setting;
    protected $msbing_conversion;
    protected $hotjar_pixel_id;
    protected $crazyegg_pixel_id;
    protected $msclarity_pixel_id;
    protected $ga_excT;
    protected $exception_tracking;
    protected $ga_elaT;
    protected $google_ads_conversion_tracking;
    protected $tiktok_business_id;
    protected $snapchat_access_token;
    protected $gads_conversions;
    protected $conv_disabled_users;
    protected $google_ads_currency;
    protected $microsoft_ads_conversions;
    protected $gads_remarketing_id;

    public function __construct()
    {
        if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            $this->ga_LC = get_woocommerce_currency(); //Local Currency from Back end
            $this->is_WC = true;
        } else {
            $this->is_WC = false;
        }
        $this->TVC_Admin_Helper = new TVC_Admin_Helper();
        $this->plan_id = $this->TVC_Admin_Helper->get_plan_id();

        add_action('wp_head', array($this, 'con_set_yith_current_currency'));
        $this->ga_CG = $this->get_option('ga_CG') == "on" ? true : false; // Content Grouping
        $this->ga_optimize_id = sanitize_text_field($this->get_option("ga_optimize_id"));
        $this->ee_options = $this->TVC_Admin_Helper->get_ee_options_settings();

        $this->tracking_method = sanitize_text_field($this->get_option("tracking_method"));

        $this->ga_Dname = "auto";
        $this->ga_id = sanitize_text_field($this->get_option("ga_id"));
        $this->ga_eeT = sanitize_text_field($this->get_option("ga_eeT"));
        $this->ga_ST = sanitize_text_field($this->get_option("ga_ST")); //add_gtag_snippet
        $this->gm_id = sanitize_text_field($this->get_option("gm_id")); //measurement_id
        $this->google_ads_id = sanitize_text_field($this->get_option("google_ads_id"));
        $this->ga_excT = sanitize_text_field($this->get_option("ga_excT")); //exception_tracking
        $this->exception_tracking = sanitize_text_field($this->get_option("exception_tracking")); //exception_tracking
        $this->ga_elaT = sanitize_text_field($this->get_option("ga_elaT")); //enhanced_link_attribution_tracking
        $this->google_merchant_id = sanitize_text_field($this->get_option("google_merchant_id"));
        $this->tracking_option = sanitize_text_field($this->get_option("tracking_option"));
        $this->ga_gCkout = sanitize_text_field($this->get_option("ga_gCkout") == "on" ? true : false); //guest checkout
        $this->ga_gUser = sanitize_text_field($this->get_option("ga_gUser") == "on" ? true : false); //guest checkout
        $this->ga_DF = sanitize_text_field($this->get_option("ga_DF") == "on" ? true : false);
        $this->ga_imTh = sanitize_text_field($this->get_option("ga_Impr") == "" ? 6 : $this->get_option("ga_Impr"));
        //$this->ga_OPTOUT = sanitize_text_field($this->get_option("ga_OPTOUT") == "on" ? true : false); //Google Analytics Opt Out
        $this->ga_PrivacyPolicy = sanitize_text_field($this->get_option("ga_PrivacyPolicy") == "on" ? true : false);
        $this->ga_IPA = sanitize_text_field($this->get_option("ga_IPA") == "on" ? true : false); //IP Anony.
        $this->ads_ert = get_option('ads_ert'); //Enable remarketing tags
        $this->ads_edrt = get_option('ads_edrt'); //Enable dynamic remarketing tags
        $this->ads_tracking_id = sanitize_text_field(get_option('ads_tracking_id'));
        $this->google_ads_currency = sanitize_text_field(get_option("conv_gads_currency"));
        $this->google_ads_conversion_tracking = get_option('google_ads_conversion_tracking');
        $this->ga_EC = get_option("ga_EC");
        $this->conversio_send_to = get_option('ee_conversio_send_to');
        $this->tiktok_business_id = sanitize_text_field($this->get_option("tiktok_business_id"));
        $this->tiktok_access_token = sanitize_text_field($this->get_option("tiktok_access_token"));
        $this->snapchat_access_token = sanitize_text_field($this->get_option("snapchat_access_token"));

        $this->gads_conversions = [];
        if (!empty($this->get_option("gads_conversions"))) {
            $gads_conversions = (array) $this->get_option("gads_conversions");
            unset($gads_conversions["PURCHASE"]);
            $this->gads_conversions = $gads_conversions;
        }
        $this->microsoft_ads_conversions = [];
        if (!empty($this->get_option("microsoft_ads_conversions"))) {
            $microsoft_ads_conversions = (array) $this->get_option("microsoft_ads_conversions");
            $this->microsoft_ads_conversions = $microsoft_ads_conversions;
        }
        $remarketing = unserialize(get_option('ee_remarketing_snippets'));
        if (!empty($remarketing) && isset($remarketing['snippets']) && esc_attr($remarketing['snippets'])) {
            $this->remarketing_snippets = base64_decode($remarketing['snippets']);
            $this->remarketing_snippet_id = sanitize_text_field(isset($remarketing['id']) ? esc_attr($remarketing['id']) : "");
        }

        /*pixels*/
        $this->gads_remarketing_id = sanitize_text_field($this->get_option('gads_remarketing_id'));
        $this->fb_pixel_id = sanitize_text_field($this->get_option('fb_pixel_id'));
        $this->microsoft_ads_pixel_id = sanitize_text_field($this->get_option('microsoft_ads_pixel_id'));
        $this->twitter_ads_pixel_id = sanitize_text_field($this->get_option('twitter_ads_pixel_id'));

        $this->twitter_ads_form_submit_event_id = sanitize_text_field($this->get_option('twitter_ads_form_submit_event_id'));
        $this->twitter_ads_email_click_event_id = sanitize_text_field($this->get_option('twitter_ads_email_click_event_id'));
        $this->twitter_ads_phone_click_event_id = sanitize_text_field($this->get_option('twitter_ads_phone_click_event_id'));
        $this->twitter_ads_address_click_event_id = sanitize_text_field($this->get_option('twitter_ads_address_click_event_id'));

        $this->twitter_ads_add_to_cart_event_id = sanitize_text_field($this->get_option('twitter_ads_add_to_cart_event_id'));
        $this->twitter_ads_checkout_initiated_event_id = sanitize_text_field($this->get_option('twitter_ads_checkout_initiated_event_id'));
        $this->twitter_ads_payment_info_event_id = sanitize_text_field($this->get_option('twitter_ads_payment_info_event_id'));
        $this->twitter_ads_purchase_event_id = sanitize_text_field($this->get_option('twitter_ads_purchase_event_id'));

        $this->pinterest_ads_pixel_id = sanitize_text_field($this->get_option('pinterest_ads_pixel_id'));
        $this->snapchat_ads_pixel_id = sanitize_text_field($this->get_option('snapchat_ads_pixel_id'));
        $this->linkedin_insight_id = sanitize_text_field($this->get_option('linkedin_insight_id'));
        $this->tiKtok_ads_pixel_id = sanitize_text_field($this->get_option('tiKtok_ads_pixel_id'));
        /* GTM*/
        $this->want_to_use_your_gtm = sanitize_text_field($this->get_option('want_to_use_your_gtm'));
        $this->use_your_gtm_id = sanitize_text_field($this->get_option('use_your_gtm_id'));

        //Disabled user roles
        $this->conv_disabled_users = $this->get_option('conv_disabled_users');
        $this->c_t_o = $this->TVC_Admin_Helper->get_ee_options_settings();

        //net_revenue_setting
        $this->net_revenue_setting = $this->get_option('net_revenue_setting');

        //New pixels
        $this->msbing_conversion = sanitize_text_field($this->get_option('msbing_conversion'));
        $this->hotjar_pixel_id = sanitize_text_field($this->get_option('hotjar_pixel_id'));
        $this->crazyegg_pixel_id = sanitize_text_field($this->get_option('crazyegg_pixel_id'));
        $this->msclarity_pixel_id = sanitize_text_field($this->get_option('msclarity_pixel_id'));
    }

    public function con_set_yith_current_currency()
    {
        if (in_array("yith-multi-currency-switcher-for-woocommerce/init.php", apply_filters('active_plugins', get_option('active_plugins')))) {
            $this->ga_LC = yith_wcmcs_get_current_currency_id();
        }
?>
        <script data-cfasync="false" data-no-optimize="1" data-pagespeed-no-defer>
            var tvc_lc = '<?php echo esc_js($this->ga_LC); ?>';
        </script>
<?php
    }

    public function get_option($key)
    {
        if (empty($this->ee_options)) {
            $this->ee_options = $this->TVC_Admin_Helper->get_ee_options_settings();
        }
        if (isset($this->ee_options[$key])) {
            return $this->ee_options[$key];
        }
    }

    public function wp_version_compare($codeSnippet)
    {

        ////////// DEPRECATED after 7.0.12+ ///////////////
        /*
        global $woocommerce;
        if (version_compare($woocommerce->version, "2.1", ">=")) {
        wc_enqueue_js($codeSnippet);
        } else {
        $woocommerce->add_inline_js($codeSnippet);
        }*/

        wp_add_inline_script('enhanced-ecommerce-google-analytics', $codeSnippet);
    }
    public function get_selector_val_fron_array($obj, $key)
    {
        if (isset($obj[$key . '_val']) && $obj[$key . '_val'] && isset($obj[$key . '_type']) && $obj[$key . '_type'] == "id") {
            return ",#" . $obj[$key . '_val'];
        } else if (isset($obj[$key . '_val']) && $obj[$key . '_val'] && isset($obj[$key . '_type']) && $obj[$key . '_type'] == "class") {
            $class_list = explode(",", $obj[$key . '_val']);
            if (!empty($class_list)) {
                $class_selector = "";
                foreach ($class_list as $class) {
                    $class_selector .= ",." . trim($class);
                }
                return $class_selector;
            }
        }
    }

    public function get_selector_val_from_array_for_gmt($obj, $key)
    {
        if (isset($obj[$key . '_val']) && $obj[$key . '_val'] && isset($obj[$key . '_type']) && $obj[$key . '_type'] == "id") {
            return "#" . $obj[$key . '_val'];
        } else if (isset($obj[$key . '_val']) && $obj[$key . '_val'] && isset($obj[$key . '_type']) && $obj[$key . '_type'] == "class") {
            $class_list = explode(",", $obj[$key . '_val']);
            if (!empty($class_list)) {
                $class_selector = "";
                foreach ($class_list as $class) {
                    $class_selector .= ($class_selector) ? ",." . trim($class) : "." . trim($class);
                }
                return $class_selector;
            }
        }
    }

    public function conv_check_user_role_disabled()
    {
        $current_user_roles = array();
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $current_user_roles = (array) $user->roles;
        }
        if (!empty($this->conv_disabled_users)) {
            $matched_user_roles = array_intersect($current_user_roles, $this->conv_disabled_users);
            if (is_array($matched_user_roles) && count($matched_user_roles) > 0) {
                return true;
            }
        }
    }

    public function conv_check_pixel_event_disabled($pixel_event)
    {
        $conv_selected_events = unserialize(get_option('conv_selected_events'));
        if (isset($conv_selected_events['ga']) && ($pixel_event != "" && !in_array($pixel_event, $conv_selected_events["ga"]))) {
            return true;
        }
    }

    public function disable_tracking($type, $pixel_event = "")
    {
        $is_user_role_disabled = $this->conv_check_user_role_disabled();
        $is_pixel_event_disabled = $this->conv_check_pixel_event_disabled($pixel_event);
        if ($type == "" || $is_user_role_disabled || $is_pixel_event_disabled) {
            return true;
        }
    }
    public function tvc_get_order_with_url_order_key()
    {
        $_get = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (isset($_get['key'])) {
            $order_key = $_get['key'];
            return wc_get_order(wc_get_order_id_by_order_key($order_key));
        }
    }
    public function tvc_get_order_from_query_vars()
    {
        global $wp;
        $order_id = absint($wp->query_vars['order-received']);
        if ($order_id && 0 != $order_id && wc_get_order($order_id)) {
            return wc_get_order($order_id);
        }
    }
    public function tvc_get_order_from_order_received_page()
    {
        if ($this->tvc_get_order_from_query_vars()) {
            return $this->tvc_get_order_from_query_vars();
        } else {
            if ($this->tvc_get_order_with_url_order_key()) {
                return $this->tvc_get_order_with_url_order_key();
            } else {
                return false;
            }
        }
    }
    public function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED']));
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_FORWARDED_FOR']));
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['HTTP_FORWARDED']));
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }
    public function get_fb_event_id()
    {
        $data = openssl_random_pseudo_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
    }
    public function generate_unique_event_id()
    {
        // Generate a unique ID based on the current timestamp
        $eventId = uniqid();

        return $eventId;
    }
    public function get_facebook_user_data($enhanced_conversion = array())
    {
        $user_data = array(
            "client_ip_address" => $this->get_client_ip(),
            "client_user_agent" => isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '', /*,
        "fbc" => "",
        "fbp" => ""*/
        );
        if (isset($enhanced_conversion["email"]) && $enhanced_conversion["email"] != "") {
            $user_data["em"] = [hash("sha256", esc_js($enhanced_conversion["email"]))];
        }
        if (isset($enhanced_conversion["address"]["first_name"]) && $enhanced_conversion["address"]["first_name"] != "") {
            $user_data["fn"] = [hash("sha256", esc_js($enhanced_conversion["address"]["first_name"]))];
        }
        if (isset($enhanced_conversion["address"]["last_name"]) && $enhanced_conversion["address"]["last_name"] != "") {
            $user_data["ln"] = [hash("sha256", esc_js($enhanced_conversion["address"]["last_name"]))];
        }
        if (isset($enhanced_conversion["phone_number"]) && $enhanced_conversion["phone_number"] != "") {
            $user_data["ph"] = [hash("sha256", esc_js($enhanced_conversion["phone_number"]))];
        }
        if (isset($enhanced_conversion["address"]["city"]) && $enhanced_conversion["address"]["city"] != "") {
            $user_data["ct"] = [hash("sha256", esc_js($enhanced_conversion["address"]["city"]))];
        }
        if (isset($enhanced_conversion["address"]["street"]) && $enhanced_conversion["address"]["street"] != "") {
            $user_data["st"] = [hash("sha256", esc_js($enhanced_conversion["address"]["street"]))];
        }
        if (isset($enhanced_conversion["address"]["region"]) && $enhanced_conversion["address"]["region"] != "") {
            $user_data["country"] = [hash("sha256", esc_js($enhanced_conversion["address"]["region"]))];
        }
        if (isset($enhanced_conversion["address"]["postal_code"]) && $enhanced_conversion["address"]["postal_code"] != "") {
            $user_data["zp"] = [hash("sha256", esc_js($enhanced_conversion["address"]["postal_code"]))];
        }
        return $user_data;
    }
}
?>