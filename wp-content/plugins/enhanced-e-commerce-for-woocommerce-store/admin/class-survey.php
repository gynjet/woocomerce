<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (!class_exists('TVC_Survey')) {
    class TVC_Survey
    {
        public $name;
        public $plugin;
        protected $TVC_Admin_DB_Helper;
        protected $apiCustomerId;
        protected $subscriptionId;
        protected $TVC_Admin_Helper;
        public function __construct($name = '', $plugin = '')
        {
            // @since 7.1.2 From now we are also taking local user feedback.
            /*if ( $this->is_dev_url() ) {
				return;
			}*/
            $this->name   = $name;
            $this->plugin = $plugin;
            $this->TVC_Admin_Helper = new TVC_Admin_Helper();
            $this->apiCustomerId = $this->TVC_Admin_Helper->get_api_customer_id();
            $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();

            add_action('admin_print_scripts', array($this, 'tvc_js'), 20);
            add_action('admin_print_scripts', array($this, 'tvc_css'));
            add_action('admin_footer',        array($this, 'tvc_modal'));
        }
        public function is_dev_url()
        {
            $url = network_site_url('/');
            $is_local_url = false;
            // Trim it up
            $url =  esc_url(strtolower(trim($url)));
            if (false === strpos($url, 'http://') && false === strpos($url, 'https://')) {
                $url = 'http://' . $url;
            }
            $url_parts = wp_parse_url($url);
            $host      = !empty($url_parts['host']) ? $url_parts['host'] : false;
            if (!empty($url) && !empty($host)) {
                if (false !== ip2long($host)) {
                    if (!filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        $is_local_url = true;
                    }
                } else if ('localhost' === $host) {
                    $is_local_url = true;
                }

                $tlds_to_check = array('.dev', '.local', ':8888');
                foreach ($tlds_to_check as $tld) {
                    if (false !== strpos($host, $tld)) {
                        $is_local_url = true;
                        continue;
                    }
                }
                if (substr_count($host, '.') > 1) {
                    $subdomains_to_check =  array('dev.', '*.staging.', 'beta.', 'test.');
                    foreach ($subdomains_to_check as $subdomain) {
                        $subdomain = str_replace('.', '(.)', $subdomain);
                        $subdomain = str_replace(array('*', '(.)'), '(.*)', $subdomain);
                        if (preg_match('/^(' . $subdomain . ')/', $host)) {
                            $is_local_url = true;
                            continue;
                        }
                    }
                }
            }
            return esc_url($is_local_url);
        }
        public function is_plugin_page()
        {
            $screen = function_exists('get_current_screen') ? get_current_screen() : false;
            if (empty($screen)) {
                return false;
            }
            return (!empty($screen->id) && in_array($screen->id, array('plugins', 'plugins-network'), true));
        }
        public function tvc_js()
        {

            if (!$this->is_plugin_page()) {
                return;
            }
?>
            <script type="text/javascript">
                jQuery(function($) {
                    var $deactivateLink = jQuery('#the-list').find(
                            '[data-slug="<?php echo esc_attr($this->plugin); ?>"] span.deactivate a'),
                        $overlay = jQuery('#ee-survey-<?php echo esc_attr($this->plugin); ?>'),
                        $form = $overlay.find('form'),
                        formOpen = false;
                    // Plugin listing table deactivate link.
                    $deactivateLink.on('click', function(event) {
                        event.preventDefault();
                        $overlay.css('display', 'table');
                        formOpen = true;
                    });
                    // Survey Skip & Deactivate.
                    $form.on('click', '.ee-survey-deactivate', function(event) {
                        event.preventDefault();
                        $overlay.css('display', 'none');
                    });
                    $form.on('click', '.feedback-footer .skip', function(event) {
                        jQuery(".ee-survey-modal").hide();
                        $overlay.css('display', 'none');
                        location.href = $deactivateLink.attr('href');
                    });

                    // Survey submit.
                    $form.submit(function(event) {
                        event.preventDefault();
                        if (!$form.find('.feedback-reason').hasClass('active')) {
                            $form.find('p.description').addClass('feedback-error').focus();
                            $form.find('p.description').css('animation', 'shake 0.5s');
                            $form.find('p.description').on('animationend', function() {
                                $form.find('p.description').css('animation', '');
                                $form.find('p.description').off('animationend');
                            });
                            return;
                        }
                        var data = {
                            action: 'tvc_call_add_survey',
                            customer_id: '<?php echo esc_attr($this->apiCustomerId); ?>',
                            subscription_id: '<?php echo esc_attr($this->subscriptionId); ?>',
                            radio_option_val: $form.find('.feedback-reason.active p').text(),
                            other_reason: 'New message - ' + $form.find('textarea').val(),
                            site_url: '<?php echo esc_url(home_url()); ?>',
                            plugin_name: 'ee-woocommerce-new',
                            tvc_call_add_survey: "<?php echo esc_attr(wp_create_nonce('tvc_call_add_survey-nonce')); ?>"
                        }
                        add_survey(data);
                    });
                    // Exit key closes survey when open.
                    jQuery(document).keyup(function(event) {
                        if (27 === event.keyCode && formOpen) {
                            $overlay.hide();
                            formOpen = false;
                            $deactivateLink.focus();
                        }
                    });

                    function add_survey(data) {
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: '<?php echo esc_url(admin_url('admin-ajax.php')); ?>',
                            data: data,
                            beforeSend: function() {
                                jQuery('.feedback-footer .submit').text("Thanks..").css('pointer-events', 'none');
                                //jQuery('.feedback-footer .submit').prop('disabled', true);
                                jQuery('.ee-survey-deactivate').hide();
                            },
                            success: function(response) {
                                jQuery(".ee-survey-modal").hide();
                                location.href = $deactivateLink.attr('href');
                            }
                        });
                    }
                });

                function selectReason(element) {
                    jQuery('.feedback-reason').removeClass('active');
                    jQuery(element).addClass('active');
                    jQuery('.ee-survey p').removeClass('feedback-error');
                    jQuery('textarea#question-text').attr('required', true);
                    var questionText = jQuery('.feedback-reason.active').data('question');
                    jQuery('textarea#question-text').attr('placeholder', questionText);
                    jQuery('#question-container').show();
                    jQuery('.description.text-note').show();
                }
            </script>
        <?php
        }
        public function tvc_css()
        {

            if (!$this->is_plugin_page()) {
                return;
            }
        ?>
            <style type="text/css">
                .ee-survey-modal {
                    width: 100%;
                    height: 100%;
                    display: none;
                    table-layout: fixed;
                    position: fixed;
                    z-index: 9999;
                    text-align: center;
                    font-size: 14px;
                    top: 0;
                    left: 0;
                    background: rgba(0, 0, 0, 0.8);
                }

                .ee-survey-wrap {
                    display: table-cell;
                    vertical-align: middle;
                }

                .ee-survey {
                    background-color: #fff;
                    padding: 24px;
                    width: 80%;
                    max-width: 1100px;
                    margin: 0 auto;
                    text-align: left;
                    border-radius: 10px;
                }

                .ee-survey p.description {
                    color: #333538;
                    font-size: 14px;
                    margin: 0;
                }

                .ee-survey-title {
                    display: block;
                    font-size: 20px;
                    font-weight: 500;
                    border-bottom: 1px solid #ddd;
                    border-bottom-width: 1px;
                    border-bottom-style: solid;
                    border-bottom-color: rgb(221, 221, 221);
                    padding: 0 0 15px 0;
                    margin: 0 0 15px 0;
                    font-family: inter;
                    color: #333538;
                }

                .ee-survey-title-sub {
                    display: block;
                    font-size: 14px;
                    font-weight: 500;
                    padding: 0 0 15px 0;
                    font-family: inter;
                }

                .ee-survey-title-sub-red {
                    color: #840000;
                }

                .ee-survey-title-sub-green {
                    color: #028202;
                }

                @keyframes shake {

                    0%,
                    100% {
                        transform: translateX(0);
                    }

                    25% {
                        transform: translateX(-5px);
                    }

                    75% {
                        transform: translateX(5px);
                    }
                }

                .feedback-error {
                    color: #a70000 !important;
                }

                .feedback-header {
                    font-size: 18px;
                    margin-bottom: 20px;
                }

                .feedback-reasons {
                    display: flex;
                    flex-wrap: nowrap;
                    column-gap: 16px;
                }

                .feedback-reason {
                    cursor: pointer;
                    border: 1px solid #ddd;
                    padding: 11px;
                    border-radius: 5px;
                    text-align: center;
                    margin-bottom: 15px;
                    transition: all 0.3s ease-in-out;
                    position: relative;
                    display: flex;
                    width: 200px;
                    align-items: center;
                    justify-content: center;
                }

                .feedback-reason:hover {
                    border-color: #1967D2;
                    background-color: #1967D2;
                    color: white;
                }

                .feedback-reason:hover .dashicons {
                    color: white;
                }

                .feedback-reason .dashicons {
                    font-size: 30px;
                    margin-right: 5px;
                    width: 30px;
                    height: 30px;
                    color: #1967D2;
                }

                .feedback-reason.active {
                    border-color: #1967D2;
                    background-color: #1967D2;
                }

                .feedback-reason.active,
                .feedback-reason.active .dashicons {
                    color: white;
                }

                .feedback-reason.active::after {
                    content: '';
                    position: absolute;
                    top: 100%;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 0;
                    height: 0;
                    border-left: 10px solid transparent;
                    border-right: 10px solid transparent;
                    border-top: 10px solid #1967D2;
                }

                .feedback-footer {
                    display: flex;
                    justify-content: flex-end;
                    margin-top: 20px;
                    margin-top: 15px;
                    border-top: 1px solid #ddd;
                    padding-top: 24px;
                }

                .feedback-footer .button {
                    margin-left: 10px;
                    padding: 8px 16px;
                    background-color: #f1f1f1;
                    color: #555;
                }

                .feedback-footer .cancel {
                    border-color: #D6D6D6 !important;
                }

                .feedback-footer .cancel:hover,
                .feedback-footer .cancel:focus {
                    background-color: #ffffff;
                    color: #1967D2;
                }

                .feedback-footer .button-primary {
                    background-color: #1967D2;
                    border-color: #1967D2 !important;
                    color: #fff;
                }

                .feedback-footer .button-primary:hover,
                .feedback-footer .button-primary:focus {
                    background-color: #0e74d2;
                    border-color: #0e74d2 !important;
                }

                button.button.cancel {
                    background: transparent;
                }

                #question-container textarea {
                    width: 100%;
                    margin-bottom: 12px 0 8px 0;
                    padding: 16px;
                    background: #f6f6f6;
                    border: 1px solid #71AEFA;
                    height: 100px;
                }

                #question-container textarea:focus {
                    box-shadow: 0 0 0 1px #71AEFA;
                }

                .feedback-reason p {
                    margin: 0;
                    text-align: left;
                    font-size: 14px;
                }

                .feedback-footer .skip,
                .feedback-footer .skip:hover,
                .feedback-footer .skip:focus {
                    background: transparent !important;
                    border: 0;
                    color: #c9c9c9;
                }

                .feedback-footer .skip:hover,
                .feedback-footer .skip:focus {
                    color: #1967D2;
                }

                .feedback-reason:focus {
                    border-color: #1967D2;
                    border-width: 2px;
                    transition: none;
                }

                @media (max-width: 930px) {
                    .feedback-reasons {
                        flex-wrap: wrap;
                    }

                    .feedback-reason.active {
                        color: white;
                        order: 1;
                        /*flex-direction: row-reverse;*/
                        transition: none;
                        justify-content: center;
                    }

                    .feedback-reason {
                        transition: none;
                    }
                }
            </style>
        <?php
        }
        public function tvc_modal()
        {
            if (!$this->is_plugin_page()) {
                return;
            }
        ?>
            <div class="ee-survey-modal" id="ee-survey-<?php echo esc_html($this->plugin); ?>">
                <div class="ee-survey-wrap">
                    <form class="ee-survey" method="post">
                        <span class="ee-survey-title"><?php echo ' ' . esc_html__('We appreciate your feedback on how we can improve.', 'enhanced-e-commerce-for-woocommerce-store'); ?></span>
                        <b class="ee-survey-title-sub ee-survey-title-sub-red">
                            <?php echo ' ' . esc_html__('⚠️ Caution: Your website will stop to track the events via our plugin.', 'enhanced-e-commerce-for-woocommerce-store'); ?>
                        </b>
                        <b class="ee-survey-title-sub ee-survey-title-sub-green">
                            <?php echo ' ' . esc_html__('🎧 Help: Let us setup event tracking on your website.', 'enhanced-e-commerce-for-woocommerce-store'); ?>
                            <a target="_blank" href="<?php echo esc_url('https://calendly.com/conversios/30min'); ?>">Set up a call now with us</a>
                        </b> 
                        <br>
                        <div class="feedback-reasons">
                            <div tabindex="0" class="feedback-reason" onclick="selectReason(this)" data-question="<?php esc_html_e('Which plugin are you considering?', 'enhanced-e-commerce-for-woocommerce-store') ?>">
                                <span class="dashicons dashicons-search"></span>
                                <p><?php esc_html_e('Found a better Plugin', 'enhanced-e-commerce-for-woocommerce-store') ?></p>
                            </div>
                            <div tabindex="0" class="feedback-reason" onclick="selectReason(this)" data-question='<?php esc_html_e("Could you please share more information about the limitations you are experiencing with the product?", 'enhanced-e-commerce-for-woocommerce-store') ?>'>
                                <span class="dashicons dashicons-products"></span>
                                <p><?php esc_html_e('Product Limit', 'enhanced-e-commerce-for-woocommerce-store') ?></p>
                            </div>
                            <div tabindex="0" class="feedback-reason" onclick="selectReason(this)" data-question="<?php esc_html_e("Please provide us with some information of what you didn't understand", 'enhanced-e-commerce-for-woocommerce-store') ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                                <p><?php esc_html_e("Couldn't Understand", 'enhanced-e-commerce-for-woocommerce-store') ?></p>
                            </div>
                            <div tabindex="0" class="feedback-reason" onclick="selectReason(this)" data-question="<?php esc_html_e('Could you provide some details about the specific feature that is absent in the product? (It will help us to improve our product)', 'enhanced-e-commerce-for-woocommerce-store') ?>">
                                <span class="dashicons dashicons-admin-generic"></span>
                                <p><?php esc_html_e('Missing a Specific Feature', 'enhanced-e-commerce-for-woocommerce-store') ?>
                                </p>
                            </div>
                            <div tabindex="0" class="feedback-reason" onclick="selectReason(this)" data-question="<?php esc_html_e('Could you kindly elaborate on the issue you experienced?', 'enhanced-e-commerce-for-woocommerce-store') ?>">
                                <span class="dashicons dashicons-warning"></span>
                                <p><?php esc_html_e('Bugs', 'enhanced-e-commerce-for-woocommerce-store') ?></p>
                            </div>
                        </div>
                        <div class="form-field" id="question-container" style="display: none;">
                            <textarea class="regular-text" id="question-text" rows="3" placeholder="What is your feedback?"></textarea>
                        </div>
                        <p class="description text-danger mt-3"><span style="color:red">*</span><?php esc_html_e("Please, select one reason and submit.", 'enhanced-e-commerce-for-woocommerce-store') ?>
                        </p>
                        <p class="description text-note mt-3" style="color:#FE4E4E; margin-top:8px; display:none;">
                            <b><?php esc_html_e("NOTE:", 'enhanced-e-commerce-for-woocommerce-store') ?></b>&nbsp;&nbsp;&nbsp;<?php esc_html_e("If you deactivate the plugin, the automatic scheduling of your product feed will also be turned off.", 'enhanced-e-commerce-for-woocommerce-store') ?>
                        </p>
                        <div class="feedback-footer">
                            <button type="button" class="button skip">Skip &amp; Deactivate</button>
                            <button type="button" class="button cancel ee-survey-deactivate">Cancel</button>
                            <button type="submit" class="button submit button-primary" style="width: 154px;">Submit &
                                Deactivate</button>
                        </div>

                    </form>
                </div>
            </div>
<?php
        }
    }
}
