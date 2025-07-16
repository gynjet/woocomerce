<?php
class TVC_Account
{
  protected $TVC_Admin_Helper = "";
  protected $url = "";
  protected $subscriptionId = "";
  protected $google_detail;
  protected $customApiObj;
  public function __construct()
  {
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->customApiObj = new CustomApi();
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();
    $this->google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    $this->TVC_Admin_Helper->add_spinner_html();
    $this->create_form();
  }

  public function create_form()
  {
    $message = "";
    $class = "";
    $googleDetail = [];
    $plan_name =  esc_html__("Free Plan", "enhanced-e-commerce-for-woocommerce-store");
    $plan_price = esc_html__("Free", "enhanced-e-commerce-for-woocommerce-store");
    $api_licence_key = "";
    $paypal_subscr_id = "";
    $product_sync_max_limit = "100";
    $activation_date = "";
    $next_payment_date = "";
    //$subscription_type = "";
    if (isset($this->google_detail['setting'])) {
      if ($this->google_detail['setting']) {
        $googleDetail = $this->google_detail['setting'];
      }
    }
?>
    <div class="con-tab-content">
      <?php if ($message) {
        printf('<div class="%1$s"><div class="alert">%2$s</div></div>', esc_attr($class), esc_html($message));
      } ?>
      <div class="tab-pane show active" id="tvc-account-page">
        <div class="tab-card">
          <div class="row">
            <div class="col-md-10 col-lg-10 border-right">

              <div class="licence tvc-licence">
                <div class="tvc_licence_key_wapper ">
                  <p>
                    <?php esc_html_e("Level up your E-commerce business with full control. Integrate GTM, GA4, FB Conversions API, Google Ads Conversion, Unlimited Product Feed, Dedicated success manager and more.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    <a target="_blank" href="https://www.conversios.io/pricing/?utm_source=woo_aiofree_plugin&utm_medium=notice&utm_campaign=account_summary&plugin_name=aio">Upgrade Now</a>
                  </p>
                </div>
                <div class="google-account-analytics tvc_licence_key_change_wapper tvc-hide ">
                  <div class="acc-num">
                    <label class="ga-title tvc_licence_key_title"><?php esc_html_e("Licence key:", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    <p class="ga-text tvc_licence_key"><?php echo esc_html($api_licence_key); ?></p>
                    <p class="ga-text text-right tvc_licence_key_change">
                      <?php echo wp_kses(
                        enhancad_get_plugin_image('/admin/images/icon/refresh.svg', 'active licence key'),
                        array(
                          'img' => array(
                            'src' => true,
                            'alt' => true,
                            'class' => true,
                            'style' => true,
                          ),
                        )
                      ); ?>
                    </p>
                  </div>
                </div>
              </div>

              <div class="tvc-table">
                <strong><?php esc_html_e("Account Summary", "enhanced-e-commerce-for-woocommerce-store"); ?></strong>
                <table>
                  <tbody>
                    <tr>
                      <th><?php esc_html_e("Plan name", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                      <td><?php echo esc_html($plan_name); ?></td>
                    </tr>
                    <tr>
                      <th><?php esc_html_e("Plan price", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                      <td><?php echo esc_html($plan_price); ?></td>
                    </tr>
                    <tr>
                      <th><?php esc_html_e("Product sync limit", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                      <td><?php echo esc_html($product_sync_max_limit); ?></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-6 col-lg-4"></div>
          </div>
        </div>
      </div>
    </div>
    <?php
    echo wp_kses_post(get_connect_google_popup_html_to_active_licence());
    ?>
    <script>
      jQuery(document).ready(function() {
        jQuery(document).on('click', '#tvc_google_connect_active_licence_close', function(event) {
          jQuery('#tvc_google_connect_active_licence').modal('hide');
        });
        jQuery(document).on('click', '.tvc_licence_key_change', function(event) {
          jQuery(".tvc_licence_key_change_wapper").slideUp(500);
          jQuery(".tvc_licence_key_wapper").slideDown(700);
        });
        jQuery(document).on('submit', 'form#tvc-licence-active', function(event) {
          event.preventDefault();
          let licence_key = jQuery("#licence_key").val();
          var form_data = jQuery("#tvc-licence-active").serialize();
          if (licence_key != "") {
            var data = {
              action: "tvc_call_active_licence",
              licence_key: licence_key,
              conv_licence_nonce: "<?php echo esc_js(wp_create_nonce('conv_lic_nonce')); ?>"
            };
            jQuery.ajax({
              type: "POST",
              dataType: "json",
              url: tvc_ajax_url,
              data: data,
              beforeSend: function() {
                tvc_helper.loaderSection(true);
              },
              success: function(response) {
                if (response.error === false) {
                  tvc_helper.tvc_alert("success", "", response.message);
                  setTimeout(function() {
                    location.reload();
                  }, 2000);
                } else {
                  if (response.is_connect == false) {
                    jQuery('#tvc_google_connect_active_licence').modal('show');
                  } else {
                    tvc_helper.tvc_alert("error", "", response.message);
                  }
                }
                tvc_helper.loaderSection(false);
              }
            });
          } else {
            tvc_helper.tvc_alert("error", "Licence key is required.");
          }
        });
      });
    </script>
<?php
  }
}
?>