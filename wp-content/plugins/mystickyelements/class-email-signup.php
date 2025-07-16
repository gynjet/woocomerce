<?php
/**
 * Update Popup Class
 *
 * @author  : Premio <contact@premio.io>
 * @license : GPL2
 * */

 if (defined('ABSPATH') === false) {
	exit;
}

/**
 * Class MSE_SIGNUP_CLASS
 *
 * Handles signup modal status, update actions, and manages display configurations
 * for the My Sticky Elements plugin update-related content.
 */
class MSE_SIGNUP_CLASS {

    /**
     * Option name used to store the update message for the "mysticky_element" feature.
     */
    private static $update_message_option = 'mysticky_element_update_message';

    /**
     * Option name used to store the date for the next signup related to the "mysticky_element" feature.
     */
    private static $next_signup_date = 'mysticky_element_next_signup_date';

    /**
     * Stores the status of the modal to be shown.
     */
    private static $show_modal_name = 'mysticky_element_show_signup_modal';

    /**
     * Constructor method for the class.
     *
     * Initializes the MSE_UPDATE_POPUP_CONTENT constant with predefined array values
     * containing plugin metadata, asset URLs, and visual elements.
     * Also registers the WordPress AJAX callback for updating status.
     *
     * @return void
     */
    public function __construct() {
        // ajax callback
        add_action( 'wp_ajax_sticky_element_update_status', array($this, 'update_status'));


	}//end __construct()


    public static function load_signup_settings()
    {
        if(defined('MSE_UPDATE_POPUP_CONTENT')) {
            return;
        }

        define('MSE_UPDATE_POPUP_CONTENT', array(
            'plugin_name'           => esc_html__('My Sticky Elements', 'mystickyelements'),
            'trust_user'            => esc_html__('Join the list 40,000+ users trust', 'mystickyelements'),
            'website_owners'        => esc_html__('40,000+', 'mystickyelements'),
            'rating'                => esc_html__('4.9/5 Rating', 'mystickyelements'),
            'review'                => esc_html__('Based on 500+ Reviews', 'mystickyelements'),
            'trust_user_img'        => MYSTICKYELEMENTS_URL . "images/signup/user-trust.svg",
            'font_url'              => MYSTICKYELEMENTS_URL . "fonts/Lato-Regular.woff",
            'background_image'      => MYSTICKYELEMENTS_URL . "images/signup/premio-update-bg.svg",
            'shape_bottom'          => MYSTICKYELEMENTS_URL . "images/signup/premio-update-bg-btm.png",
            'shape_bottom_right'    => MYSTICKYELEMENTS_URL . "images/signup/premio-update-bg-right.png",
            'mail_icon'             => MYSTICKYELEMENTS_URL . "images/signup/mail-icon.svg",
            'user_icon'             => MYSTICKYELEMENTS_URL . "images/signup/users.svg",
            'slash_icon'            => MYSTICKYELEMENTS_URL . "images/signup/slash.svg",
            'star_icon'             => MYSTICKYELEMENTS_URL . "images/signup/star.svg",
            'arrow_right'           => MYSTICKYELEMENTS_URL . "images/signup/arrow-right.svg",
            'check_circle'          => MYSTICKYELEMENTS_URL . "images/signup/check-circle.svg",
            'pre_loader'            => MYSTICKYELEMENTS_URL . "images/signup/pre-loader.svg",
        ));
    }


    /**
     * Checks the status of a modal and determines whether it should be displayed.
     *
     * This method evaluates various conditions, such as predefined options and the HTTP referrer,
     * to decide if the modal should be shown. It may also update specific modal-related options
     * based on the results of these checks.
     *
     * @return bool Returns true if the modal should be displayed; otherwise, false.
     */
    public static function check_modal_status() {
        if(get_option(self::$update_message_option) == -1 || get_option(self::$update_message_option) == 2) {
            return false;
        }

        $referer = isset($_SERVER['HTTP_REFERER']) ? sanitize_text_field($_SERVER['HTTP_REFERER']) : '';

        if (!str_contains($referer, 'my-sticky-elements')) {
            $elements_widgets = get_option( 'mystickyelements-widgets' );

            if(!empty($elements_widgets) && $elements_widgets != false){
                add_option(self::$show_modal_name, 1);
            }
        }

        if (get_option(self::$show_modal_name)) {
            $next_signup_date = get_option(self::$next_signup_date);
            if($next_signup_date === false) {
                self::load_signup_settings();
                return true;
            } else {
                if($next_signup_date < date('Y-m-d')) {
                    self::load_signup_settings();
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Handles the AJAX request to update the status of the plugin.
     *
     * Verifies the nonce for security, processes the provided status and email,
     * and executes specific actions based on the status, including sending data
     * to an external API or updating WordPress options.
     *
     * @return void
     */
    public function update_status() {
        if(!empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'my_sticky_elements_update_nonce')) {
            $status = sanitize_text_field($_REQUEST['status']);
            $email = sanitize_text_field($_REQUEST['email']);
            if($status == 1) {
                update_option(self::$update_message_option, -1);
                $url = 'https://premioapps.com/premio/signup/email.php';
                $apiParams = [
                    'plugin' => 'elements',
                    'email'  => $email,
                ];

                // Signup Email for Chaty
                $apiResponse = wp_safe_remote_post($url, ['body' => $apiParams, 'timeout' => 15, 'sslverify' => true]);

                if (is_wp_error($apiResponse)) {
                    wp_safe_remote_post($url, ['body' => $apiParams, 'timeout' => 15, 'sslverify' => false]);
                }
            } else {
                $next_date = date('Y-m-d', strtotime('+7 days'));
                $next_signup_date = get_option(self::$next_signup_date);
                if($next_signup_date === false) {
                    add_option(self::$next_signup_date, $next_date);
                } else {
                    update_option(self::$update_message_option, -1);
                }
            }
        }
        echo "1";
        die;
    }
    
}
new MSE_SIGNUP_CLASS();