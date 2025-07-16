<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

if (isset($_GET['subscription_id']) && isset($_GET['microsoft_mail'])) {
    $ee_options['microsoft_ads_manager_id'] = "";
    $ee_options['microsoft_ads_subaccount_id'] = "";
    $ee_options['microsoft_ads_pixel_id'] = "";
    $ee_options['microsoft_merchant_center_id'] = "";
    $ee_options['ms_catalog_id'] = "";
    update_option("ee_options", serialize($ee_options));
}

$microsoft_ads_manager_id = isset($ee_options['microsoft_ads_manager_id']) ? $ee_options['microsoft_ads_manager_id'] : "";
$microsoft_ads_subaccount_id = isset($ee_options['microsoft_ads_subaccount_id']) ? $ee_options['microsoft_ads_subaccount_id'] : "";
$microsoft_ads_pixel_id = isset($ee_options['microsoft_ads_pixel_id']) ? $ee_options['microsoft_ads_pixel_id'] : "";

$is_sel_disable = 'disabled';
$ms_email = $tvc_data["microsoft_mail"];

$store_country = get_option('woocommerce_default_country');
$store_country = explode(":", $store_country);
if ($store_country[0]) {
    $country = $store_country[0];
} else {
    $country = '';
}
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
    <form id="bingsetings_form" class="convpixsetting-inner-box">

        <?php
        // not needed for now as addd in ms-signin.php $confirm_url = "admin.php?page=conversios-google-analytics&subpage=bingsettings"; // return to page after login success
        require_once("ms-signin.php");
        //$site_url_feedlist = "admin.php?page=conversios-google-shopping-feed&tab=feed_list";
        ?>

        <div id="msbing_box">
            <div class="row">
                <div class="col-6">
                    <!-- MS Bing Ads manager -->
                    <div class="py-1">
                        <div class="row pt-2">
                            <div class="col-12">
                                <h5 class="d-flex align-items-center mb-1 text-dark">
                                    <b><?php esc_html_e("Microsoft Ads Manager Account:", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                                    <?php if (!empty($microsoft_ads_manager_id)) { ?>
                                        <span class="material-symbols-outlined text-success ms-1 fs-6">check_circle</span>
                                    <?php } ?>
                                    <!-- <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="The Microsoft Ads pixel ID looks like. 343003931">
                                        info
                                    </span> -->
                                </h5>
                                <div class="d-flex align-items-center">
                                    <select id="microsoft_ads_manager_id" name="microsoft_ads_manager_id" class="form-select form-select-lg mb-3 selecttwo microsoft_ads_manager_id" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                                        <?php if (!empty($microsoft_ads_manager_id)) { ?>
                                            <option value="<?php echo esc_attr($microsoft_ads_manager_id); ?>" selected><?php echo esc_attr($microsoft_ads_manager_id); ?></option>
                                        <?php } ?>
                                        <option value="">Select Account</option>
                                    </select>
                                    <button type="button" class="btn btn-primary ms-4 btn-sm d-flex conv-enable-selection align-items-center">
                                        <span class="px-1"><?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MS Bing Ads manager End-->

                    <!-- MS Bing Ads SubAccount -->
                    <div class="py-1">
                        <div class="row pt-2">
                            <div class="col-12">
                                <h5 class="d-flex align-items-center mb-1 text-dark">
                                    <b><?php esc_html_e("Microsoft Ads Sub Account:", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                                    <?php if (!empty($microsoft_ads_subaccount_id)) { ?>
                                        <span class="material-symbols-outlined text-success ms-1 fs-6">check_circle</span>
                                    <?php } ?>
                                    <!-- <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="The Microsoft Ads pixel ID looks like. 343003931">
                                        info
                                    </span> -->
                                </h5>
                                <div class="d-flex align-items-center">
                                    <select id="microsoft_ads_subaccount_id" name="microsoft_ads_subaccount_id" class="form-select form-select-lg mb-3 selecttwo microsoft_ads_subaccount_id" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                                        <?php if (!empty($microsoft_ads_subaccount_id)) { ?>
                                            <option value="<?php echo esc_attr($microsoft_ads_subaccount_id); ?>" selected><?php echo esc_attr($microsoft_ads_subaccount_id); ?></option>
                                        <?php } ?>
                                        <option value="">Select Account</option>
                                    </select>
                                    <button type="button" class="btn btn-primary ms-3 btn-sm d-flex conv-enable-selection align-items-center">
                                        <span class="px-1"><?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- MS Bing Ads SubAccount End-->
                </div>
                <div class="col-6">
                    <div id="conv-microsoft-ads" class="col-12 flex-row pt-1 mt-3 disabledsection">
                        <!-- <h5 class="fw-bold mb-1 text-dark">
                            <?php //esc_html_e("Don't have a Microsoft Bing Ads account?", "enhanced-e-commerce-for-woocommerce-store"); 
                            ?>
                        </h5> -->

                        <div class="d-flex justify-content-between align-items-center conv_create_new_bing_card rounded px-3 py-3">
                            <div class="pe-2">
                                <h5 class="text-dark mb-0">
                                    <?php echo esc_html__("Create a Microsoft Bing Ads account", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </h5>
                                <span class="text-dark fs-12">
                                    <?php esc_html_e("By using Pmax Campaign and Feed Sync with a Microsoft Bing Ads account, you can simplify campaign management, improve conversions, and increase product visibility, ultimately driving more sales and revenue for your business.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    <br>
                                    <?php //esc_html_e("your account.", "enhanced-e-commerce-for-woocommerce-store"); 
                                    ?>
                                    <!-- <a href="https://www.google.com/intl/en_in/ads/coupons/terms/cyoi/" class="" target="_blank">
                                        <u><?php //esc_html_e("Terms and conditions apply.", "enhanced-e-commerce-for-woocommerce-store"); 
                                            ?></u>
                                    </a> -->
                                </span>
                            </div>

                            <div class="align-self-center <?php echo (isset($tvc_data['microsoft_mail']) && $tvc_data['microsoft_mail'] != "") ? esc_attr($tvc_data['microsoft_mail']) : 'disabledsection'; ?>">
                                <button id="conv_create_new_bing_btn" type="button" class="btn btn-primary px-5">
                                    <?php esc_html_e("Create Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MS Bing Pixel -->
            <div class="py-1">
                <div class="row pt-2 align-items-end">
                    <div class="col-6">
                        <h5 class="d-flex align-items-center mb-1 text-dark">
                            <b><?php esc_html_e("Microsoft Ads Pixel Id: (UET tag)", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                            <?php if (!empty($microsoft_ads_pixel_id)) { ?>
                                <span class="material-symbols-outlined text-success ms-1 fs-6">check_circle</span>
                            <?php } ?>
                            <!-- <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="The Microsoft Ads pixel ID looks like. 343003931">
                                info
                            </span> -->
                        </h5>
                        <div class="d-flex align-items-center">
                            <select id="microsoft_ads_pixel_id" name="microsoft_ads_pixel_id" class="form-select form-select-lg mb-3 selecttwo microsoft_ads_pixel_id" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                                <?php if (!empty($microsoft_ads_pixel_id)) { ?>
                                    <option value="<?php echo esc_attr($microsoft_ads_pixel_id); ?>" selected><?php echo esc_attr($microsoft_ads_pixel_id); ?></option>
                                <?php } ?>
                                <option value="">Select Account</option>
                            </select>
                            <button type="button" class="btn btn-primary ms-4 btn-sm d-flex conv-enable-selection conv-enable-selection-ads-pixel align-items-center">
                                <span class="px-1"><?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </button>
                        </div>
                    </div>
                    <div class="col-6">
                        <div id="create_uet_tag" class="d-flex align-items-center mb-1 text-dark disabledsection">
                            <b><?php esc_html_e("Don't have a UET tag?", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                            <button class="button btn-outline-primary px-2 ms-2" onClick="create_uet_tag(event)">
                                <?php esc_html_e("Create Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- MS Bing Pixel End-->

        </div>
    </form>
    <input type="hidden" id="valtoshow_inpopup" value="Microsoft Ads (Bing) Pixel:" />

</div>
<!-- Accordion start -->
<div class="accordion accordion-flush microsoft_ads_conversion_acc disabledsection" id="accordionFlushExample">

    <div class="accordion-item mt-3 rounded-3 shadow-sm">
        <h2 class="accordion-header" id="flush-headingTwo">
            <button class="accordion-button collapsed conv-link-blue" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                <?php esc_html_e("Measure Your Campaign Conversion", "enhanced-e-commerce-for-woocommerce-store"); ?>
                <small class="ms-2 m-0 fw-normal">
                    <?php esc_html_e("(For microsoft ads conversion tracking)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </small>
            </button>
        </h2>
        <div id="flush-collapseTwo" class="accordion-collapse collapse show row row-x-0" aria-labelledby="flush-headingTwo">
            <div class="col-7 accordion-body pt-0">
                <ul class="ps-0">
                    <li class="<?php echo !CONV_IS_WC ? 'hidden' : 'd-flex align-items-center my-2' ?>">
                        <div class="inlist_text_pre ms-2" conversion_name="Purchase">
                            <h5 class="mb-0 d-flex align-items-center"><?php esc_html_e("Purchase", "enhanced-e-commerce-for-woocommerce-store"); ?>(Woocommerce)
                                <span class="material-symbols-outlined text-success ms-1 fs-6 d-none">check_circle</span>
                                <span class="material-symbols-outlined text-error me-1 fs-16 d-none">cancel</span>
                            </h5>
                            <div class="inlist_text_notconnected d-none">
                                <?php esc_html_e("You can track all the Purchase events by adding the conversion label", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </div>
                            <div class="inlist_text_connected d-flex d-none">
                                <div class="text-success"><?php esc_html_e("Connected with Conversion ID:", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                <div class="inlist_text_connected_convid"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-auto convcon_create_but convcon_create_Purchase px-3 py-1" conversion_name="Purchase">
                            <?php esc_html_e("Enable now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </button>
                    </li>
                    <li class="<?php echo !CONV_IS_WC ? 'hidden' : 'd-flex align-items-center my-2' ?>">
                        <div class="inlist_text_pre_pro ms-2 disabledsection-no" conversion_name="AddToCart">
                            <h5 class="mb-0 d-flex align-items-center"><?php esc_html_e("Add to Cart (Woocommerce)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                <span class="material-symbols-outlined text-success ms-1 fs-6 d-none">check_circle</span>
                                <span class="material-symbols-outlined text-error me-1 fs-16 d-none">cancel</span>
                            </h5>
                            <div class="inlist_text_notconnected d-none">
                                <?php esc_html_e("Track 'add to cart' events to evaluate campaign effectiveness.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </div>
                            <div class="inlist_text_connected d-flex d-none">
                                <div class="text-success"><?php esc_html_e("Connected with Conversion ID:", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                <div class="inlist_text_connected_convid ps-2"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-auto convcon_create_but convcon_create_AddToCart px-3 py-1" conversion_name="AddToCart">
                            <?php esc_html_e("Enable now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </button>
                    </li>
                    <li class="<?php echo !CONV_IS_WC ? 'hidden' : 'd-flex align-items-center my-2' ?>">
                        <div class="inlist_text_pre_pro ms-2 disabledsection-no" conversion_name="BeginCheckout">
                            <h5 class="mb-0 d-flex align-items-center"><?php esc_html_e("Begin Checkout (Woocommerce)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                <span class="material-symbols-outlined text-success ms-1 fs-6 d-none">check_circle</span>
                                <span class="material-symbols-outlined text-error me-1 fs-16 d-none">cancel</span>
                            </h5>
                            <div class="inlist_text_notconnected d-none">
                                <?php esc_html_e("Track 'begin checkout' events to evaluate campaign effectiveness.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </div>
                            <div class="inlist_text_connected d-flex d-none">
                                <div class="text-success"><?php esc_html_e("Connected with Conversion ID:", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                <div class="inlist_text_connected_convid ps-2"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-auto convcon_create_but convcon_create_BeginCheckout px-3 py-1" conversion_name="BeginCheckout">
                            <?php esc_html_e("Enable now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </button>
                    </li>
                    <li class="d-flex align-items-center my-2">
                        <div class="inlist_text_pre ms-2" conversion_name="SubmitLeadForm">
                            <h5 class="mb-0 d-flex align-items-center"><?php esc_html_e("Form Lead Submit", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                <span class="material-symbols-outlined text-success ms-1 fs-6 d-none">check_circle</span>
                                <span class="material-symbols-outlined text-error me-1 fs-16 d-none">cancel</span>
                            </h5>
                            <div class="inlist_text_notconnected d-none">
                                <?php esc_html_e("You can track all the Form Submit events by adding the conversion label", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </div>
                            <div class="inlist_text_connected d-flex d-none">
                                <div class="text-success"><?php esc_html_e("Connected with Conversion ID:", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                <div class="inlist_text_connected_convid ps-2"></div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm ms-auto convcon_create_but convcon_create_SubmitLeadForm px-3 py-1" conversion_name="SubmitLeadForm">
                            <?php esc_html_e("Enable now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Accordion End -->
<!--Modal -->
<div class="modal fade" id="conv_create_new_bing" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form id="bingForm" onfocus="this.className='focused'">
            <div class="modal-content">

                <div class="modal-header bg-light p-2 ps-4">
                    <h5 class="modal-title fs-16 fw-500" id="feedType">
                        <?php esc_html_e("Create New Microsoft Bing Ads Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h5>
                    <button type="button" class="btn-close pe-4 closeButton" data-bs-dismiss="modal" aria-label="Close" onclick=""></button>
                </div>
                <!-- <div id="create_bing_loader" class="progress-materializecss d-none ps-2 pe-2" style="width:98%">
                    <div class="indeterminate"></div>
                </div> -->
                <div class="modal-body text-start">
                    <div class="row">
                        <div class="col-12 pe-4">
                            <div id="before_gadsacccreated_text" class="mb-1 fs-6 before-bing-acc-creation">
                                <div id="create_bing_error" class="alert alert-danger d-none" role="alert">
                                    <small></small>
                                </div>
                                <form id="conv_form_new_bing">
                                    <div class="mb-3">


                                        <div class="form-group mt-2">
                                            <span class="inner-text">Your Manager Account Name</span> <span class="text-danger">*</span>
                                            <input class="form-control mb-2" type="text" id="bing_sub_account_name" name="sub_account_name" value="<?php echo isset($tvc_data['sub_account_name']) ? esc_attr($tvc_data['sub_account_name']) : ''; ?>" placeholder="" required>
                                        </div>

                                        <div class="form-group mt-2">
                                            <span class="inner-text">Your Ads Account Name</span> <span class="text-danger">*</span>
                                            <input class="form-control mb-2" type="text" id="bing_account_name" name="account_name" value="<?php echo isset($tvc_data['account_name']) ? esc_attr($tvc_data['account_name']) : ''; ?>" placeholder="" required>
                                        </div>

                                        <!-- <span class="inner-text">Currency Code</span><span class="text-danger"> *</span><br>
                                        <input class="form-control mb-0" type="text" id="bing_store_name" name="store_name" value="" placeholder="Enter Store Name" required> -->


                                        <div class="form-group mt-3 border border-3 p-3">
                                            <h4>Address</h4>
                                            <label class="fs-14" for="bussiness_name">Bussiness Name <span class="text-danger"> *</span></label>
                                            <input type="text" class="form-control" id="bussiness_name" name="bussiness_name" placeholder="" required>
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="fs-14" for="bussiness_name">Country <span class="text-danger"> *</span></label>
                                                    <select id="bing_country" name="country" class="form-select form-select-lg mb-3" style="width: 100%" data-placeholder="Select Country" required>
                                                        <option value="">Select Country</option>
                                                        <option value="AF">Afghanistan</option>
                                                        <option value="AL">Albania</option>
                                                        <option value="DZ">Algeria</option>
                                                        <option value="AS">American Samoa</option>
                                                        <option value="AD">Andorra</option>
                                                        <option value="AO">Angola</option>
                                                        <option value="AI">Anguilla</option>
                                                        <option value="AQ">Antarctica</option>
                                                        <option value="AG">Antigua and Barbuda</option>
                                                        <option value="AR">Argentina</option>
                                                        <option value="AM">Armenia</option>
                                                        <option value="AW">Aruba</option>
                                                        <option value="AU">Australia</option>
                                                        <option value="AT">Austria</option>
                                                        <option value="AZ">Azerbaijan</option>
                                                        <option value="BS">Bahamas, The</option>
                                                        <option value="BH">Bahrain</option>
                                                        <option value="BD">Bangladesh</option>
                                                        <option value="BB">Barbados</option>
                                                        <option value="BY">Belarus</option>
                                                        <option value="BE">Belgium</option>
                                                        <option value="BZ">Belize</option>
                                                        <option value="BJ">Benin</option>
                                                        <option value="BM">Bermuda</option>
                                                        <option value="BT">Bhutan</option>
                                                        <option value="BO">Bolivia</option>
                                                        <option value="AN">Bonaire, Curaçao, Saba, Sint Eustatius, Sint Maarten</option>
                                                        <option value="BA">Bosnia and Herzegovina</option>
                                                        <option value="BW">Botswana</option>
                                                        <option value="BR">Brazil</option>
                                                        <option value="BN">Brunei</option>
                                                        <option value="BG">Bulgaria</option>
                                                        <option value="BF">Burkina Faso</option>
                                                        <option value="BI">Burundi</option>
                                                        <option value="CV">Cabo Verde</option>
                                                        <option value="KH">Cambodia</option>
                                                        <option value="CM">Cameroon</option>
                                                        <option value="CA">Canada</option>
                                                        <option value="KY">Cayman Islands</option>
                                                        <option value="CF">Central African Republic</option>
                                                        <option value="TD">Chad</option>
                                                        <option value="CL">Chile</option>
                                                        <option value="CN">China</option>
                                                        <option value="CX">Christmas Island</option>
                                                        <option value="CC">Cocos (Keeling) Islands</option>
                                                        <option value="CO">Colombia</option>
                                                        <option value="KM">Comoros</option>
                                                        <option value="CG">Congo</option>
                                                        <option value="CD">Congo (DRC)</option>
                                                        <option value="CK">Cook Islands</option>
                                                        <option value="CR">Costa Rica</option>
                                                        <option value="CI">Côte d'Ivoire</option>
                                                        <option value="HR">Croatia</option>
                                                        <option value="CY">Cyprus</option>
                                                        <option value="CZ">Czech Republic</option>
                                                        <option value="DK">Denmark</option>
                                                        <option value="DJ">Djibouti</option>
                                                        <option value="DM">Dominica</option>
                                                        <option value="DO">Dominican Republic</option>
                                                        <option value="EC">Ecuador</option>
                                                        <option value="EG">Egypt</option>
                                                        <option value="SV">El Salvador</option>
                                                        <option value="GQ">Equatorial Guinea</option>
                                                        <option value="ER">Eritrea</option>
                                                        <option value="EE">Estonia</option>
                                                        <option value="ET">Ethiopia</option>
                                                        <option value="FK">Falkland Islands</option>
                                                        <option value="FO">Faroe Islands</option>
                                                        <option value="FJ">Fiji Islands</option>
                                                        <option value="FI">Finland</option>
                                                        <option value="FR">France</option>
                                                        <option value="GF">French Guiana</option>
                                                        <option value="PF">French Polynesia</option>
                                                        <option value="GA">Gabon</option>
                                                        <option value="GM">Gambia, The</option>
                                                        <option value="GE">Georgia</option>
                                                        <option value="DE">Germany</option>
                                                        <option value="GH">Ghana</option>
                                                        <option value="GI">Gibraltar</option>
                                                        <option value="GR">Greece</option>
                                                        <option value="GL">Greenland</option>
                                                        <option value="GD">Grenada</option>
                                                        <option value="GP">Guadeloupe</option>
                                                        <option value="GU">Guam</option>
                                                        <option value="GT">Guatemala</option>
                                                        <option value="GN">Guinea</option>
                                                        <option value="GW">Guinea-Bissau</option>
                                                        <option value="GY">Guyana</option>
                                                        <option value="HT">Haiti</option>
                                                        <option value="HN">Honduras</option>
                                                        <option value="HK">Hong Kong SAR</option>
                                                        <option value="HU">Hungary</option>
                                                        <option value="IS">Iceland</option>
                                                        <option value="IN">India</option>
                                                        <option value="ID">Indonesia</option>
                                                        <option value="IQ">Iraq</option>
                                                        <option value="IE">Ireland</option>
                                                        <option value="IL">Israel</option>
                                                        <option value="IT">Italy</option>
                                                        <option value="JM">Jamaica</option>
                                                        <option value="JP">Japan</option>
                                                        <option value="JO">Jordan</option>
                                                        <option value="KZ">Kazakhstan</option>
                                                        <option value="KE">Kenya</option>
                                                        <option value="KI">Kiribati</option>
                                                        <option value="KR">Korea</option>
                                                        <option value="KW">Kuwait</option>
                                                        <option value="KG">Kyrgyzstan</option>
                                                        <option value="LA">Laos</option>
                                                        <option value="LV">Latvia</option>
                                                        <option value="LB">Lebanon</option>
                                                        <option value="LS">Lesotho</option>
                                                        <option value="LR">Liberia</option>
                                                        <option value="LY">Libya</option>
                                                        <option value="LI">Liechtenstein</option>
                                                        <option value="LT">Lithuania</option>
                                                        <option value="LU">Luxembourg</option>
                                                        <option value="MO">Macao SAR</option>
                                                        <option value="MK">North Macedonia, Former Yugoslav Republic of</option>
                                                        <option value="MG">Madagascar</option>
                                                        <option value="MW">Malawi</option>
                                                        <option value="MY">Malaysia</option>
                                                        <option value="MV">Maldives</option>
                                                        <option value="ML">Mali</option>
                                                        <option value="MT">Malta</option>
                                                        <option value="MH">Marshall Islands</option>
                                                        <option value="MQ">Martinique</option>
                                                        <option value="MR">Mauritania</option>
                                                        <option value="MU">Mauritius</option>
                                                        <option value="YT">Mayotte</option>
                                                        <option value="MX">Mexico</option>
                                                        <option value="FM">Micronesia</option>
                                                        <option value="MD">Moldova</option>
                                                        <option value="MC">Monaco</option>
                                                        <option value="MN">Mongolia</option>
                                                        <option value="ME">Montenegro</option>
                                                        <option value="MS">Montserrat</option>
                                                        <option value="MA">Morocco</option>
                                                        <option value="MZ">Mozambique</option>
                                                        <option value="MM">Myanmar</option>
                                                        <option value="NA">Namibia</option>
                                                        <option value="NR">Nauru</option>
                                                        <option value="NP">Nepal</option>
                                                        <option value="NL">Netherlands</option>
                                                        <option value="NC">New Caledonia</option>
                                                        <option value="NZ">New Zealand</option>
                                                        <option value="NI">Nicaragua</option>
                                                        <option value="NE">Niger</option>
                                                        <option value="NG">Nigeria</option>
                                                        <option value="NU">Niue</option>
                                                        <option value="NF">Norfolk Island</option>
                                                        <option value="MP">Northern Mariana Islands</option>
                                                        <option value="NO">Norway</option>
                                                        <option value="OM">Oman</option>
                                                        <option value="PK">Pakistan</option>
                                                        <option value="PW">Palau</option>
                                                        <option value="PS">Palestinian Authority</option>
                                                        <option value="PA">Panama</option>
                                                        <option value="PG">Papua New Guinea</option>
                                                        <option value="PY">Paraguay</option>
                                                        <option value="PE">Peru</option>
                                                        <option value="PH">Philippines</option>
                                                        <option value="PN">Pitcairn Islands</option>
                                                        <option value="PL">Poland</option>
                                                        <option value="PT">Portugal</option>
                                                        <option value="PR">Puerto Rico</option>
                                                        <option value="QA">Qatar</option>
                                                        <option value="RE">Reunion</option>
                                                        <option value="RO">Romania</option>
                                                        <option value="RU">Russia</option>
                                                        <option value="RW">Rwanda</option>
                                                        <option value="SH">Saint Helena</option>
                                                        <option value="KN">Saint Kitts and Nevis</option>
                                                        <option value="LC">Saint Lucia</option>
                                                        <option value="PM">Saint Pierre and Miquelon</option>
                                                        <option value="VC">Saint Vincent and the Grenadines</option>
                                                        <option value="WS">Samoa</option>
                                                        <option value="SM">San Marino</option>
                                                        <option value="ST">Sao Tomé and Príncipe</option>
                                                        <option value="SA">Saudi Arabia</option>
                                                        <option value="SN">Senegal</option>
                                                        <option value="RS">Serbia</option>
                                                        <option value="SC">Seychelles</option>
                                                        <option value="SL">Sierra Leone</option>
                                                        <option value="SG">Singapore</option>
                                                        <option value="SK">Slovakia</option>
                                                        <option value="SI">Slovenia</option>
                                                        <option value="SB">Solomon Islands</option>
                                                        <option value="SO">Somalia</option>
                                                        <option value="ZA">South Africa</option>
                                                        <option value="ES">Spain</option>
                                                        <option value="LK">Sri Lanka</option>
                                                        <option value="SR">Suriname</option>
                                                        <option value="SZ">Swaziland</option>
                                                        <option value="SE">Sweden</option>
                                                        <option value="CH">Switzerland</option>
                                                        <option value="TW">Taiwan</option>
                                                        <option value="TJ">Tajikistan</option>
                                                        <option value="TZ">Tanzania</option>
                                                        <option value="TH">Thailand</option>
                                                        <option value="TL">Timor-Leste</option>
                                                        <option value="TG">Togo</option>
                                                        <option value="TK">Tokelau</option>
                                                        <option value="TO">Tonga</option>
                                                        <option value="TT">Trinidad and Tobago</option>
                                                        <option value="TN">Tunisia</option>
                                                        <option value="TR">Türkiye</option>
                                                        <option value="TM">Turkmenistan</option>
                                                        <option value="TC">Turks and Caicos Islands</option>
                                                        <option value="TV">Tuvalu</option>
                                                        <option value="UG">Uganda</option>
                                                        <option value="UA">Ukraine</option>
                                                        <option value="AE">United Arab Emirates</option>
                                                        <option value="GB">United Kingdom</option>
                                                        <option value="US">United States</option>
                                                        <option value="UY">Uruguay</option>
                                                        <option value="UZ">Uzbekistan</option>
                                                        <option value="VU">Vanuatu</option>
                                                        <option value="VA">Vatican City</option>
                                                        <option value="VE">Venezuela</option>
                                                        <option value="VN">Vietnam</option>
                                                        <option value="VG">Virgin Islands, British</option>
                                                        <option value="VI">Virgin Islands, U.S.</option>
                                                        <option value="WF">Wallis and Futuna</option>
                                                        <option value="YE">Yemen</option>
                                                        <option value="ZM">Zambia</option>
                                                        <option value="ZW">Zimbabwe</option>
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="fs-14" for="address">State ISO CODE<span class="text-danger"> *</span></label>
                                                    <input type="text" class="form-control" id="state" name="state" placeholder="" required>
                                                    <small style=" font-size: 10px; ">(For "New York" is "NY")</small>
                                                </div>
                                            </div>
                                            <label class="fs-14" for="address">Address 1 <span class="text-danger"> *</span></label>
                                            <input type="text" class="form-control" id="address_1" name="address_1" placeholder="" required>
                                            <label class="fs-14" for="address">Address 2</label>
                                            <input type="text" class="form-control" id="address_2" name="address_2" placeholder="">
                                            <div class="row">
                                                <div class="col-6">
                                                    <label class="fs-14" for="address">City <span class="text-danger"> *</span></label>
                                                    <input type="text" class="form-control" id="city" name="city" placeholder="" required>
                                                </div>
                                                <div class="col-6">
                                                    <label class="fs-14" for="address">Postal or Zip Code <span class="text-danger"> *</span></label>
                                                    <input type="text" class="form-control" id="zip" name="zip" placeholder="" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="form-group col-6 mt-2 time_zone">
                                                <span class="inner-text">Select Timezone</span><span class="text-danger"> *</span><br>
                                                <select id="timezones" data-placeholder="Select Timezone">
                                                    <option value="">Select Timezone</option>
                                                    <option value="AbuDhabiMuscat">Abu Dhabi, Muscat</option>
                                                    <option value="Adelaide">Adelaide</option>
                                                    <option value="Alaska">Alaska</option>
                                                    <option value="AlmatyNovosibirsk">Almaty, Novosibirsk</option>
                                                    <option value="AmsterdamBerlinBernRomeStockholmVienna">Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                                                    <option value="Arizona">Arizona</option>
                                                    <option value="AstanaDhaka">Astana, Dhaka</option>
                                                    <option value="AthensBuckarestIstanbul">Athens, Bucharest, Istanbul</option>
                                                    <option value="AtlanticTimeCanada">Atlantic Time (Canada)</option>
                                                    <option value="AucklandWellington">Auckland, Wellington</option>
                                                    <option value="Azores">Azores</option>
                                                    <option value="Baghdad">Baghdad</option>
                                                    <option value="BakuTbilisiYerevan">Baku, Tbilisi, Yerevan</option>
                                                    <option value="BangkokHanoiJakarta">Bangkok, Hanoi, Jakarta</option>
                                                    <option value="BeijingChongqingHongKongUrumqi">Beijing, Chongqing, Hong Kong, Urumqi</option>
                                                    <option value="BelgradeBratislavaBudapestLjubljanaPrague">Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                                                    <option value="BogotaLimaQuito">Bogota, Lima, Quito</option>
                                                    <option value="Brasilia">Brasilia</option>
                                                    <option value="Brisbane">Brisbane</option>
                                                    <option value="BrusselsCopenhagenMadridParis">Brussels, Copenhagen, Madrid, Paris</option>
                                                    <option value="Bucharest">Bucharest</option>
                                                    <option value="BuenosAiresGeorgetown">Buenos Aires, Georgetown</option>
                                                    <option value="Cairo">Cairo</option>
                                                    <option value="CanberraMelbourneSydney">Canberra, Melbourne, Sydney</option>
                                                    <option value="CapeVerdeIsland">Cape Verde Island</option>
                                                    <option value="CaracasLaPaz">Caracas, La Paz</option>
                                                    <option value="CasablancaMonrovia">Casablanca, Monrovia</option>
                                                    <option value="CentralAmerica">Central America</option>
                                                    <option value="CentralTimeUSCanada">Central Time (US & Canada)</option>
                                                    <option value="ChennaiKolkataMumbaiNewDelhi">Chennai, Kolkata, Mumbai, New Delhi</option>
                                                    <option value="ChihuahuaLaPazMazatlan">Chihuahua, La Paz, Mazatlan</option>
                                                    <option value="Darwin">Darwin</option>
                                                    <option value="EasternTimeUSCanada">Eastern Time (US & Canada)</option>
                                                    <option value="Ekaterinburg">Ekaterinburg</option>
                                                    <option value="FijiKamchatkaMarshallIsland">Fiji, Kamchatka, Marshall Island</option>
                                                    <option value="Greenland">Greenland</option>
                                                    <option value="GreenwichMeanTimeDublinEdinburghLisbonLondon">Greenwich Mean Time, Dublin, Edinburgh, Lisbon, London</option>
                                                    <option value="GuadalajaraMexicoCityMonterrey">Guadalajara, Mexico City, Monterrey</option>
                                                    <option value="GuamPortMoresby">Guam, Port Moresby</option>
                                                    <option value="HararePretoria">Harare, Pretoria</option>
                                                    <option value="Hawaii">Hawaii</option>
                                                    <option value="HelsinkiKyivRigaSofiaTallinnVilnius">Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
                                                    <option value="Hobart">Hobart</option>
                                                    <option value="IndianaEast">Indiana (East)</option>
                                                    <option value="InternationalDateLineWest">International Date Line West</option>
                                                    <option value="IrkutskUlaanBataar">Irkutsk, Ulaan Bataar</option>
                                                    <option value="IslamabadKarachiTashkent">Islamabad, Karachi, Tashkent</option>
                                                    <option value="Jerusalem">Jerusalem</option>
                                                    <option value="Kabul">Kabul</option>
                                                    <option value="Kathmandu">Kathmandu</option>
                                                    <option value="Krasnoyarsk">Krasnoyarsk</option>
                                                    <option value="KualaLumpurSingapore">Kuala Lumpur, Singapore</option>
                                                    <option value="KuwaitRiyadh">Kuwait, Riyadh</option>
                                                    <option value="MidAtlantic">Mid-Atlantic</option>
                                                    <option value="MidwayIslandAndSamoa">Midway Island and Samoa</option>
                                                    <option value="MoscowStPetersburgVolgograd">Moscow, St. Petersburg, Volgograd</option>
                                                    <option value="MountainTimeUSCanada">Mountain Time (US & Canada)</option>
                                                    <option value="Nairobi">Nairobi</option>
                                                    <option value="Newfoundland">Newfoundland</option>
                                                    <option value="Nukualofa">Nukualofa</option>
                                                    <option value="OsakaSapporoTokyo">Osaka, Sapporo, Tokyo</option>
                                                    <option value="PacificTimeUSCanadaTijuana">Pacific Time (US & Canada), Tijuana</option>
                                                    <option value="Perth">Perth</option>
                                                    <option value="Rangoon">Rangoon</option>
                                                    <option value="Santiago">Santiago</option>
                                                    <option value="SarajevoSkopjeWarsawZagreb">Sarajevo, Skopje, Warsaw, Zagreb</option>
                                                    <option value="Saskatchewan">Saskatchewan</option>
                                                    <option value="Seoul">Seoul</option>
                                                    <option value="SolomonIslandNewCaledonia">Solomon Island, New Caledonia</option>
                                                    <option value="SriJayawardenepura">Sri Jayawardenepura</option>
                                                    <option value="Taipei">Taipei</option>
                                                    <option value="Tehran">Tehran</option>
                                                    <option value="Vladivostok">Vladivostok</option>
                                                    <option value="WestCentralAfrica">West Central Africa</option>
                                                    <option value="Yakutsk">Yakutsk</option>
                                                </select>
                                            </div>

                                            <div class="form-group col-6 mt-2">
                                                <span class="inner-text">Marketing Language</span><span class="text-danger"> *</span><br>
                                                <select id="language" data-placeholder="Select Marketing Language">
                                                    <option value="">Select Marketing Language</option>
                                                    <option value="Arabic">Arabic</option>
                                                    <option value="Bulgarian">Bulgarian</option>
                                                    <option value="Croatian">Croatian</option>
                                                    <option value="Czech">Czech</option>
                                                    <option value="Danish">Danish</option>
                                                    <option value="Dutch">Dutch</option>
                                                    <option value="English">English</option>
                                                    <option value="Filipino">Filipino</option>
                                                    <option value="Finnish">Finnish</option>
                                                    <option value="French">French</option>
                                                    <option value="German">German</option>
                                                    <option value="Greek">Greek</option>
                                                    <option value="Hebrew">Hebrew</option>
                                                    <option value="Hindi">Hindi</option>
                                                    <option value="Hungarian">Hungarian</option>
                                                    <option value="Indonesian">Indonesian</option>
                                                    <option value="Italian">Italian</option>
                                                    <option value="Japanese">Japanese</option>
                                                    <option value="Korean">Korean</option>
                                                    <option value="Lithuanian">Lithuanian</option>
                                                    <option value="Malay">Malay</option>
                                                    <option value="Norwegian">Norwegian</option>
                                                    <option value="Polish">Polish</option>
                                                    <option value="Portuguese">Portuguese</option>
                                                    <option value="Romanian">Romanian</option>
                                                    <option value="Russian">Russian</option>
                                                    <option value="SimplifiedChinese">SimplifiedChinese</option>
                                                    <option value="Spanish">Spanish</option>
                                                    <option value="Swedish">Swedish</option>
                                                    <option value="Thai">Thai</option>
                                                    <option value="TraditionalChinese">TraditionalChinese</option>
                                                    <option value="Turkish">Turkish</option>
                                                    <option value="Ukrainian">Ukrainian</option>
                                                    <option value="Vietnamese">Vietnamese</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group mt-2">
                                            <span class="inner-text">Currency Code</span><span class="text-danger"> *</span><br>
                                            <select id="currency_code" data-placeholder="Select Currency Code">
                                                <option value="">Select Currency Code</option>
                                                <option value="AED">AED</option>
                                                <option value="ALL">ALL</option>
                                                <option value="AMD">AMD</option>
                                                <option value="ARS">ARS</option>
                                                <option value="AUD">AUD</option>
                                                <option value="AZM">AZM</option>
                                                <option value="BGL">BGL</option>
                                                <option value="BHD">BHD</option>
                                                <option value="BND">BND</option>
                                                <option value="BOB">BOB</option>
                                                <option value="BRL">BRL</option>
                                                <option value="BYB">BYB</option>
                                                <option value="BZD">BZD</option>
                                                <option value="CAD">CAD</option>
                                                <option value="CHF">CHF</option>
                                                <option value="CLP">CLP</option>
                                                <option value="CNY">CNY</option>
                                                <option value="COP">COP</option>
                                                <option value="CRC">CRC</option>
                                                <option value="CZK">CZK</option>
                                                <option value="DKK">DKK</option>
                                                <option value="DOP">DOP</option>
                                                <option value="DZD">DZD</option>
                                                <option value="EEK">EEK</option>
                                                <option value="EGP">EGP</option>
                                                <option value="EUR">EUR</option>
                                                <option value="GBP">GBP</option>
                                                <option value="GEL">GEL</option>
                                                <option value="GTQ">GTQ</option>
                                                <option value="HKD">HKD</option>
                                                <option value="HNL">HNL</option>
                                                <option value="HRK">HRK</option>
                                                <option value="HUF">HUF</option>
                                                <option value="IDR">IDR</option>
                                                <option value="ILS">ILS</option>
                                                <option value="INR">INR</option>
                                                <option value="IQD">IQD</option>
                                                <option value="IRR">IRR</option>
                                                <option value="ISK">ISK</option>
                                                <option value="JMD">JMD</option>
                                                <option value="JOD">JOD</option>
                                                <option value="JPY">JPY</option>
                                                <option value="KES">KES</option>
                                                <option value="KGS">KGS</option>
                                                <option value="KRW">KRW</option>
                                                <option value="KWD">KWD</option>
                                                <option value="KZT">KZT</option>
                                                <option value="LBP">LBP</option>
                                                <option value="LTL">LTL</option>
                                                <option value="LVL">LVL</option>
                                                <option value="LYD">LYD</option>
                                                <option value="MAD">MAD</option>
                                                <option value="MKD">MKD</option>
                                                <option value="MNT">MNT</option>
                                                <option value="MOP">MOP</option>
                                                <option value="MVR">MVR</option>
                                                <option value="MXN">MXN</option>
                                                <option value="MYR">MYR</option>
                                                <option value="NGN">NGN</option>
                                                <option value="NIO">NIO</option>
                                                <option value="NOK">NOK</option>
                                                <option value="NZD">NZD</option>
                                                <option value="OMR">OMR</option>
                                                <option value="PAB">PAB</option>
                                                <option value="PEN">PEN</option>
                                                <option value="PHP">PHP</option>
                                                <option value="PKR">PKR</option>
                                                <option value="PLN">PLN</option>
                                                <option value="PYG">PYG</option>
                                                <option value="QAR">QAR</option>
                                                <option value="ROL">ROL</option>
                                                <option value="RUR">RUR</option>
                                                <option value="SAR">SAR</option>
                                                <option value="SEK">SEK</option>
                                                <option value="SGD">SGD</option>
                                                <option value="SIT">SIT</option>
                                                <option value="SKK">SKK</option>
                                                <option value="SYP">SYP</option>
                                                <option value="THB">THB</option>
                                                <option value="TND">TND</option>
                                                <option value="TRY">TRY</option>
                                                <option value="TTD">TTD</option>
                                                <option value="TWD">TWD</option>
                                                <option value="UAH">UAH</option>
                                                <option value="USD">USD</option>
                                                <option value="UYU">UYU</option>
                                                <option value="UZS">UZS</option>
                                                <option value="VEF">VEF</option>
                                                <option value="VND">VND</option>
                                                <option value="YER">YER</option>
                                                <option value="YUN">YUN</option>
                                                <option value="ZAR">ZAR</option>
                                                <option value="ZWD">ZWD</option>
                                            </select>
                                        </div>

                                        <div class="form-group p-3 border border-3 mt-3 tax_info_card d-none">
                                            <h4>Tax Information</h4>
                                            <select id="tax_info_key" class="d-none">
                                                <option value=""></option>
                                                <option value="Australia">AUGSTNumber</option>
                                                <option value="Brazil">CCM / CNPJ / CPF </option>
                                                <option value="India">GSTINNumber</option>
                                                <option value="Singapore">GSTNumber</option>
                                                <option value="Chile">IsIVAOrVATTaxPayer</option>
                                                <option value="Chile">IsWithholdingTaxExempted</option>
                                                <option value="New Zealand">NZGSTNumber</option>
                                                <option value="India">PanNumber</option>
                                                <option value="Austria">VATNumber</option>
                                                <option value="Belgium">VATNumber</option>
                                                <option value="Bulgaria">VATNumber</option>
                                                <option value="Cyprus">VATNumber</option>
                                                <option value="Czech Republic">VATNumber</option>
                                                <option value="Germany">VATNumber</option>
                                                <option value="Denmark">VATNumber</option>
                                                <option value="Estonia">VATNumber</option>
                                                <option value="Greece">VATNumber</option>
                                                <option value="Finland">VATNumber</option>
                                                <option value="France">VATNumber</option>
                                                <option value="United Kingdom">VATNumber</option>
                                                <option value="Croatia">VATNumber</option>
                                                <option value="Hungary">VATNumber</option>
                                                <option value="Ireland">VATNumber</option>
                                                <option value="Italy">VATNumber</option>
                                                <option value="Lithuania">VATNumber</option>
                                                <option value="Luxembourg">VATNumber</option>
                                                <option value="Latvia">VATNumber</option>
                                                <option value="Malta">VATNumber</option>
                                                <option value="Monaco">VATNumber</option>
                                                <option value="The Netherlands">VATNumber</option>
                                                <option value="Poland">VATNumber</option>
                                                <option value="Portugal">VATNumber</option>
                                                <option value="Romania">VATNumber</option>
                                                <option value="Spain">VATNumber</option>
                                                <option value="Sweden">VATNumber</option>
                                                <option value="Slovenia">VATNumber</option>
                                                <option value="Slovakia">VATNumber</option>
                                                <option value="Chile">VATNumber</option>
                                                <option value="Hungary">VATNumber</option>
                                                <option value="Liechtenstein">VATNumber</option>
                                                <option value="Nigeria">VATNumber</option>
                                                <option value="Portugal">VATNumber</option>
                                                <option value="Serbia">VATNumber</option>
                                                <option value="Switzerland">VATNumber</option>
                                                <option value="Thailand">VATNumber</option>
                                            </select>
                                            <div class="form-group mt-3 tax_info_value">
                                                <label class="fs-14" for="tax_info_value"></label>
                                                <input type="text" class="form-control" id="tax_info_value" name="tax_info_value" placeholder="">
                                            </div>
                                        </div>


                                        <div class="form-group mt-2">
                                            <input id="bing_concent" name="concent" class="form-check-input" type="checkbox" value="1" required style="float:none">
                                            <label class="form-check-label fs-12" for="concent">
                                                <?php esc_html_e("I accept the", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                                <a class="fs-14" target="_blank" href="<?php echo esc_url("https://www.microsoft.com/en-gb/servicesagreement"); ?>"><?php esc_html_e("terms & conditions", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                                <span class="text-danger"> *</span>
                                            </label>
                                        </div>

                                    </div>

                                </form>
                            </div>
                            <!-- Show this after creation -->
                            <div class="onbrdpp-body alert alert-primary text-start d-none after-bing-acc-creation">
                                New Microsoft Bing Ads Account With Id: <span id="new_bing_id"></span> is created
                                successfully.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" style="width:112px; height:38px; border-radius: 4px; padding: 8px; gap:10px; border: 1px solid #ccc" class="btn btn-light fs-14 fw-medium" id="model_close_bing_creation">Close</button>
                    <button type="submit" style="height:38px; border-radius: 4px; padding: 8px; gap:10px;" class="btn btn-primary fs-14 fw-medium">
                        <span id="gadsinviteloader" class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
                        Create Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Create New Ads Account Modal -->
<div class="modal fade" id="conv_create_mads_new" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <span id="after_madsacccreated_title" class=""><?php esc_html_e("Account Created", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <span id="before_madsacccreated_text" class="mb-1 lh-lg fs-6 before-ads-acc-creation d-none">
                    <?php esc_html_e("You’ll receive an invite from Microsoft, on your email. Accept the invitation to enable your Microsoft Ads Account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </span>

                <div class="onbrdpp-body alert alert-primary text-start after-ads-acc-creation" id="new_microsoft_ads_section">
                    <p>
                        <?php esc_html_e("Your Microsoft Ads Account has been created", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <strong>
                            (<b><span id="new_microsoft_ads_id"></span></b>).
                        </strong>
                    </p>
                    <h6>
                        <?php esc_html_e("Steps to claim your Microsoft Ads Account:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h6>
                    <ol>
                        <li>
                            <?php esc_html_e("Accept invitation mail from Microsoft Ads sent to your email address", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <em><?php echo (isset($tvc_data['microsoft_mail'])) ? esc_attr($tvc_data['microsoft_mail']) : ""; ?></em>
                            <span id="invitationLink">
                                <br>
                                <em><?php esc_html_e("OR", "enhanced-e-commerce-for-woocommerce-store"); ?></em>
                                <?php esc_html_e("Open", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                <a href="" target="_blank" id="ads_invitationLink"><?php esc_html_e("Invitation Link", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                            </span>
                        </li>
                        <li><?php esc_html_e("Log into your Microsoft Ads account and set up your billing preferences", "enhanced-e-commerce-for-woocommerce-store"); ?></li>
                    </ol>
                </div>

            </div>
            <div class="modal-footer">
                <button id="ads-continue-close" class="btn btn-secondary m-auto text-white after-ads-acc-creation" data-bs-dismiss="modal">
                    <?php esc_html_e("Already done, Or Will Do via Mail Invitation", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="convmicrosoftadseditconfirm" tabindex="-1" aria-labelledby="convmicrosoftadseditconfirmLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="convmicrosoftadseditconfirmLabel">Change Microsoft Ads Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Changing Microsoft Ads Account will remove selected conversions ID and Labels
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="conv_changemicrosoftadsacc_but" type="button" class="btn btn-primary">
                    Change Now
                    <div class="spinner-border spinner-border-sm d-none" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
    let subscription_id = "<?php echo esc_attr($subscriptionId); ?>";
    let plan_id = "<?php echo (isset($plan_id)) ? esc_attr($plan_id) : ''; ?>";
    let app_id = "<?php echo esc_attr($app_id); ?>";
    let selected_microsoft_ads_pixel_id = jQuery("#microsoft_ads_pixel_id").val();
    jQuery("#conv_changemicrosoftadsacc_but").click(function() {
        var account_id = jQuery("#microsoft_ads_manager_id").val();
        var subaccount_id = jQuery("#microsoft_ads_subaccount_id").val();
        // jQuery("#conv_changemicrosoftadsacc_but").addClass("disabled");
        // jQuery("#conv_changemicrosoftadsacc_but").find(".spinner-border").removeClass("d-none");
        conv_change_loadingbar("show");
        jQuery(".conv-enable-selection").addClass('disabled');
        list_microsoft_ads_get_UET_tag(account_id, subaccount_id);
        // clearmicrosoftadsconversions();
        conv_change_loadingbar("hide");
    });

    jQuery(".conv-enable-selection-ads-pixel").click(function() {
        jQuery("#convmicrosoftadseditconfirm").modal('show');
    });

    jQuery("#microsoft_ads_pixel_id").on("change", function() {
        clearmicrosoftadsconversions();
    })


    function clearmicrosoftadsconversions() {
        let microsoft_ads_pixel_id = jQuery("#microsoft_ads_pixel_id").val();
        let clearconversions = "no";
        if (microsoft_ads_pixel_id != selected_microsoft_ads_pixel_id) {
            clearconversions = "yes";
        }
        var data = {
            action: "conv_save_microsoft_ads_conversion",
            clearmicrosoftadsconversions: clearconversions,
            CONVNonce: "<?php echo esc_js(wp_create_nonce('conv_save_microsoft_ads_conversion-nonce')); ?>",
        };
        jQuery.ajax({
            type: "POST",
            url: tvc_ajax_url,
            data: data,
            success: function(response) {
                // jQuery('.inlist_text_pre').find(".inlist_text_notconnected").removeClass("d-none");
                // jQuery('.inlist_text_pre').find(".inlist_text_connected").addClass("d-none");
                // jQuery('.inlist_text_pre').find(".inlist_text_connected").find(".inlist_text_connected_convid").html("");
                // jQuery('.inlist_text_pre').next().html("Add");
                jQuery("#convmicrosoftadseditconfirm").modal("hide");
            }
        });
    }

    function savemicrosoftadsconversions(category) {
        let categoryObj = {};
        if (Array.isArray(category)) {
            category.forEach(cat => {
                categoryObj[cat] = 1;
            });
        } else {
            // If category is a single string, convert it to an object
            categoryObj[category] = 1;
        }
        var data = {
            action: "conv_save_microsoft_ads_conversion",
            category: JSON.stringify(categoryObj),
            CONVNonce: "<?php echo esc_js(wp_create_nonce('conv_save_microsoft_ads_conversion-nonce')); ?>"
        };
        jQuery.ajax({
            type: "POST",
            url: tvc_ajax_url,
            data: data,
            success: function(response) {
                // jQuery('.inlist_text_pre').find(".inlist_text_notconnected").removeClass("d-none");
                // jQuery('.inlist_text_pre').find(".inlist_text_connected").addClass("d-none");
                // jQuery('.inlist_text_pre').find(".inlist_text_connected").find(".inlist_text_connected_convid").html("");
                // jQuery('.inlist_text_pre').next().html("Add");
                // jQuery("#convmicrosoftadseditconfirm").modal("hide");
            }
        });
    }


    function list_microsoft_ads_get_UET_tag(account_id, subaccount_id) {
        //uery("#ee_conversio_send_to_static").removeClass("conv-border-danger");
        jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
        jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-microsoft");
        jQuery(".conv-btn-connect").text('Save');

        //cleargadsconversions();
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "list_microsoft_ads_get_UET_tag",
                tvc_data: tvc_data,
                account_id: account_id,
                subaccount_id: subaccount_id,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            beforeSend: function() {
                conv_change_loadingbar("show");
                jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
            },
            success: function(response) { //console.log(response.data);
                var btn_cam = 'ads_list';
                if (response.error === false) {
                    var error_msg = 'null';
                    if (response.data.length == 0) {
                        jQuery("#create_uet_tag").removeClass("disabledsection");
                        jQuery('#microsoft_ads_pixel_id').html('<option value="">No Microsoft ads UET tag Found</option>');
                        jQuery('#microsoft_ads_pixel_id').prop("disabled", false);
                        getAlertMessageAll(
                            'info',
                            'Error',
                            message = 'There are no Microsoft ads UET tag associated with email.',
                            icon = 'info',
                            buttonText = 'Ok',
                            buttonColor = '#FCCB1E',
                            iconImageSrc =
                            '<img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >'
                        );
                    } else {
                        if (response.data.length > 0) {
                            jQuery('#microsoft_ads_pixel_id').html('<option value="">Select Account</option>');
                            var AccOptions = '';
                            let selectedval = "";
                            response?.data.forEach(function(item, i) {
                                AccOptions = AccOptions + '<option value="' + item.Id + '">' + item.Id + '</option>';
                                if (i == 0 || i == "0") {
                                    selectedval = item.Id;
                                }
                            });
                            jQuery('#microsoft_ads_pixel_id').append(AccOptions);
                            jQuery('#microsoft_ads_pixel_id').prop("disabled", false);
                            //jQuery('#microsoft_ads_pixel_id').parent().find('.conv-enable-selection').addClass('d-none');

                            jQuery("#accordionFlushExample .accordion-body").removeClass("disabledsection");
                            jQuery(".accordion-button").removeClass("text-dark");
                            jQuery("#microsoft_ads_pixel_id").val(selectedval);
                            jQuery("#microsoft_ads_pixel_id").trigger("change");
                        }
                    }
                } else {
                    var error_msg = response.errors;
                    var error_msg = response.errors;
                    getAlertMessageAll(
                        'info',
                        'Error',
                        message = 'Your Authorization has been expired or not valid. Erro:' + error_msg,
                        icon = 'info',
                        buttonText = 'Ok',
                        buttonColor = '#FCCB1E',
                        iconImageSrc =
                        '<img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >'
                    );
                }
                jQuery('#ads-account').prop('disabled', false);
                jQuery("#convmicrosoftadseditconfirm").modal("hide");
            },
            complete: function() {
                conv_change_loadingbar("hide");
                jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
            }

        });

        jQuery("#conv_conversion_select").trigger("change");
    }

    jQuery(document).ready(function() {
        var microsoft_ads_pixel_id = jQuery('#microsoft_ads_pixel_id').val();
        if (microsoft_ads_pixel_id === "") {
            jQuery(".microsoft_ads_conversion_acc").addClass('disabledsection');
        } else {
            jQuery(".microsoft_ads_conversion_acc").removeClass('disabledsection');
        }
    });

    jQuery(document).ready(function() {
        function fetchMicrosoftAdsConversion() {
            var pix_id = jQuery('#microsoft_ads_pixel_id').val();
            jQuery(".convcon_create_Purchase, .convcon_create_AddToCart, .convcon_create_BeginCheckout, .convcon_create_SubmitLeadForm").removeClass('disabledsection');
            jQuery(".microsoft_ads_conversion_acc").addClass('disabledsection');
            var data = {
                action: "conv_get_microsoft_ads_conversion",
                customer_id: jQuery('#microsoft_ads_manager_id').val(),
                account_id: jQuery('#microsoft_ads_subaccount_id').val(),
                tag_id: pix_id,
                TVCNonce: "<?php echo esc_js(wp_create_nonce('con_get_conversion_list-nonce')); ?>"
            };

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                success: function(response) {
                    if (response.status == "200" && response.data != undefined && response.data != "") {
                        const conversionGoals = response.data.ConversionGoals.map(goal => goal.Name);
                        const conversions = {};
                        conversions[`Conversios_Purchase_${pix_id}`] = ".convcon_create_Purchase";
                        conversions[`Conversios_AddToCart_${pix_id}`] = ".convcon_create_AddToCart";
                        conversions[`Conversios_BeginCheckout_${pix_id}`] = ".convcon_create_BeginCheckout";
                        conversions[`Conversios_SubmitLeadForm_${pix_id}`] = ".convcon_create_SubmitLeadForm";
                        let conversionTypes = [];
                        Object.keys(conversions).forEach(name => {
                            if (conversionGoals.includes(name)) {
                                jQuery(conversions[name]).removeClass('btn-outline-primary').addClass('pe-none btn-outline-success').text('Enabled');
                                let conversionType = conversions[name].split('_').pop();
                                conversionTypes.push(conversionType);
                            } else {
                                jQuery(conversions[name])
                                    .removeClass('pe-none btn-outline-success')
                                    .addClass('btn-outline-primary')
                                    .text('Enable now');
                            }
                        });
                        savemicrosoftadsconversions(conversionTypes);
                        jQuery(".microsoft_ads_conversion_acc").removeClass('disabledsection');
                    }
                }
            });
        }

        if (jQuery("#microsoft_ads_pixel_id").val() != "") {
            fetchMicrosoftAdsConversion();
        }

        jQuery("#microsoft_ads_pixel_id").on('change', function() {
            fetchMicrosoftAdsConversion();
        });
    });


    function microsoft_ads_conversion(conversionCategory) {
        var category = conversionCategory;
        var pixelid = jQuery('#microsoft_ads_pixel_id').val();
        var action_value = "";
        if (category == "Purchase") {
            action_value = "purchase";
        } else if (category == "AddToCart") {
            action_value = "add_to_cart";
        } else if (category == "BeginCheckout") {
            action_value = "begin_checkout";
        } else if (category == "SubmitLeadForm") {
            action_value = "form_lead_submit";
        }
        var data = {
            action: "conv_create_microsoft_ads_conversion",
            customer_id: jQuery('#microsoft_ads_manager_id').val(),
            account_id: jQuery('#microsoft_ads_subaccount_id').val(),
            tag_id: pixelid,
            name: `Conversios_${conversionCategory}_${pixelid}`,
            conversionCategory: conversionCategory,
            action_value: action_value,
            TVCNonce: "<?php echo esc_js(wp_create_nonce('con_get_conversion_list-nonce')); ?>"
        };
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            success: function(response) {
                if (response.status == "200" && response.data != undefined && response.data != "") {
                    if (category === "Purchase") {
                        jQuery(".convcon_create_Purchase").removeClass('btn-outline-primary').addClass('pe-none btn-outline-success').text('Enabled');
                        savemicrosoftadsconversions(category);
                    }
                    if (category === "AddToCart") {
                        jQuery(".convcon_create_AddToCart").removeClass('btn-outline-primary').addClass('pe-none btn-outline-success').text('Enabled');
                        savemicrosoftadsconversions(category);
                    }
                    if (category === "BeginCheckout") {
                        jQuery(".convcon_create_BeginCheckout").removeClass('btn-outline-primary').addClass('pe-none btn-outline-success').text('Enabled');
                        savemicrosoftadsconversions(category);
                    }
                    if (category === "SubmitLeadForm") {
                        jQuery(".convcon_create_SubmitLeadForm").removeClass('btn-outline-primary').addClass('pe-none btn-outline-success').text('Enabled');
                        savemicrosoftadsconversions(category);
                    }
                }
            }
        });
    }

    jQuery(".convcon_create_but").click(function() {
        var conversion_name = jQuery(this).attr("conversion_name");
        microsoft_ads_conversion(conversion_name);
    });

    function create_uet_tag(e) {
        e.preventDefault();
        var account_id = jQuery("#microsoft_ads_manager_id").val();
        var subaccount_id = jQuery("#microsoft_ads_subaccount_id").val();
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "create_microsoft_ads_UET_tag",
                subscription_id: subscription_id,
                account_id: account_id,
                subaccount_id: subaccount_id,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            beforeSend: function() {
                conv_change_loadingbar("show");
                //jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
            },
            success: function(response) { //console.log(response.data);

                if (response.error === false) {
                    var error_msg = 'null';
                    if (response.data.length == 0) {
                        jQuery('#microsoft_ads_pixel_id').html('<option value="">No Microsoft ads UET tag Found</option>');
                        jQuery('#microsoft_ads_pixel_id').prop("disabled", false);
                        getAlertMessageAll(
                            'info',
                            'Error',
                            message = 'Microsoft ads UET tag not created!. Something wrong contact the support team.',
                            icon = 'info',
                            buttonText = 'Ok',
                            buttonColor = '#FCCB1E',
                            iconImageSrc =
                            '<img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >'
                        );
                    } else {
                        if (response.data.length > 0) {
                            jQuery('#microsoft_ads_pixel_id').html('<option value="">Select Account</option>');
                            var AccOptions = '';
                            var selectedval = '';
                            response?.data.forEach(function(item, i) {
                                AccOptions = AccOptions + '<option value="' + item.Id + '">' + item.Id + '</option>';
                                if (i == 0 || i == "0") {
                                    selectedval = item.Id;
                                }
                            });

                            jQuery('#microsoft_ads_pixel_id').append(AccOptions);
                            jQuery('#microsoft_ads_pixel_id').prop("disabled", false);
                            //jQuery('#microsoft_ads_pixel_id').parent().find('.conv-enable-selection').addClass('d-none');

                            jQuery("#accordionFlushExample .accordion-body").removeClass("disabledsection");
                            jQuery(".accordion-button").removeClass("text-dark");

                            jQuery("#microsoft_ads_pixel_id").val(selectedval);
                            jQuery("#microsoft_ads_pixel_id").trigger("change");
                        }
                    }
                } else {
                    var error_msg = response.errors;
                    getAlertMessageAll(
                        'info',
                        'Error',
                        message = error_msg,
                        icon = 'info',
                        buttonText = 'Ok',
                        buttonColor = '#FCCB1E',
                        iconImageSrc = '<?php echo wp_kses(
                                            enhancad_get_plugin_image('/admin/images/logos/conv_error_logo.png', '', '', ''),
                                            array(
                                                'img' => array(
                                                    'src' => true,
                                                    'alt' => true,
                                                    'class' => true,
                                                    'style' => true,
                                                ),
                                            )
                                        ); ?>'
                    );
                }
                jQuery('#ads-account').prop('disabled', false);
            },
            complete: function() {
                conv_change_loadingbar("hide");
                jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
            }

        });

        jQuery("#conv_conversion_select").trigger("change");
    }

    // get list microsoft ads dropdown options
    function list_microsoft_ads_account(tvc_data, new_ads_id) {
        //uery("#ee_conversio_send_to_static").removeClass("conv-border-danger");
        jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
        jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-microsoft");
        jQuery(".conv-btn-connect").text('Save');

        //cleargadsconversions();

        var selectedValue = jQuery("#microsoft_ads_manager_id").val();
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "list_microsoft_ads_account",
                tvc_data: tvc_data,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            beforeSend: function() {
                conv_change_loadingbar("show");
                jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
            },
            success: function(response) { //console.log(response.data);
                var btn_cam = 'ads_list';
                if (response.error === false) {
                    var error_msg = 'null';
                    if (response.data.length == 0) {
                        jQuery('#conv-microsoft-ads').removeClass('disabledsection');
                        jQuery('#microsoft_ads_manager_id').html("<option value=''>No Manager's Accounts Found</option>");
                        jQuery('#microsoft_ads_manager_id').prop("disabled", false);
                        getAlertMessageAll(
                            'info',
                            'Error',
                            message = 'There are no Microsoft ads accounts associated with email.',
                            icon = 'info',
                            buttonText = 'Ok',
                            buttonColor = '#FCCB1E',
                            iconImageSrc =
                            '<img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >'
                        );
                    } else {
                        if (response.data.length > 0) {

                            jQuery('#microsoft_ads_manager_id').html('<option value="">Select Account</option>');

                            var AccOptions = '';
                            var selected = '';
                            if (new_ads_id != "" && new_ads_id != undefined) {
                                AccOptions = AccOptions + '<option value="' + new_ads_id + '" selected>' + new_ads_id + '</option>';
                            }
                            response?.data.forEach(function(item) {
                                AccOptions = AccOptions + '<option value="' + item.Id + '">' + item.Name + '(' + item.Id + ')' + '</option>';
                            });
                            jQuery('#microsoft_ads_manager_id').append(AccOptions);
                            jQuery('#microsoft_ads_manager_id').prop("disabled", false);
                            //jQuery('#microsoft_ads_manager_id').parent().find('.conv-enable-selection').addClass('d-none');

                            jQuery("#accordionFlushExample .accordion-body").removeClass("disabledsection");
                            jQuery(".accordion-button").removeClass("text-dark");
                        }
                    }
                } else {
                    var error_msg = response.errors;
                    getAlertMessageAll(
                        'info',
                        'Error',
                        message = 'No bing ads account found associated with this email, create new account',
                        icon = 'info',
                        buttonText = 'Ok',
                        buttonColor = '#FCCB1E',
                        iconImageSrc =
                        '<img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >'
                    );
                    jQuery('#conv-microsoft-ads').removeClass('disabledsection');
                }
                jQuery('#ads-account').prop('disabled', false);
            },
            complete: function() {
                conv_change_loadingbar("hide");
                jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
            }

        });

        jQuery("#conv_conversion_select").trigger("change");
    }

    function list_microsoft_ads_subaccount(account_id) {
        //uery("#ee_conversio_send_to_static").removeClass("conv-border-danger");
        jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
        jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-microsoft");
        jQuery(".conv-btn-connect").text('Save');

        //cleargadsconversions();
        //console.log(tvc_data);

        var selectedValue = jQuery("#microsoft_ads_manager_id").val();
        var conversios_onboarding_nonce = "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "list_microsoft_ads_subaccount",
                tvc_data: tvc_data,
                account_id: account_id,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            beforeSend: function() {
                conv_change_loadingbar("show");
                jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
            },
            success: function(response) { //console.log(response.data);
                var btn_cam = 'ads_list';
                if (response.error === false) {
                    var error_msg = 'null';
                    if (response.data.length == 0) {
                        jQuery('#microsoft_ads_subaccount_id').html('<option value="">No Microsoft ads Sub Accounts Found</option>');
                        jQuery('#microsoft_ads_subaccount_id').prop("disabled", false);
                        getAlertMessageAll(
                            'info',
                            'Error',
                            message = 'There are no Microsoft ads Sub Accounts associated with email.',
                            icon = 'info',
                            buttonText = 'Ok',
                            buttonColor = '#FCCB1E',
                            iconImageSrc =
                            '<img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >'
                        );
                    } else {
                        if (response.data.length > 0) {
                            jQuery('#microsoft_ads_subaccount_id').html('<option value="">Select Account</option>');
                            var AccOptions = '';
                            var selected = '';
                            response?.data.forEach(function(item) {
                                AccOptions = AccOptions + '<option value="' + item.Id + '">' + item.Number + '(' + item.Id + ')' + '</option>';
                            });
                            jQuery('#microsoft_ads_subaccount_id').append(AccOptions);
                            jQuery('#microsoft_ads_subaccount_id').prop("disabled", false);
                            //jQuery('#microsoft_ads_subaccount_id').parent().find('.conv-enable-selection').addClass('d-none');

                            jQuery("#accordionFlushExample .accordion-body").removeClass("disabledsection");
                            jQuery(".accordion-button").removeClass("text-dark");
                        }
                    }
                } else {
                    var error_msg = response.errors;
                    getAlertMessageAll(
                        'info',
                        'Error',
                        message = 'Your Authorization has been expired or not valid. Erro:' + error_msg,
                        icon = 'info',
                        buttonText = 'Ok',
                        buttonColor = '#FCCB1E',
                        iconImageSrc =
                        '<img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >'
                    );
                }
                jQuery('#ads-account').prop('disabled', false);
            },
            complete: function() {
                conv_change_loadingbar("hide");
                jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
            }

        });

        jQuery("#conv_conversion_select").trigger("change");
    }

    function showMAdsModalPopUp(url) {
        //calcualte popUp size
        var h = Math.max(800, window.screen.availHeight * 0.66) // try to use 66% of height, but no smaller than 800
        var w = Math.max(500, window.screen.availWidth * 0.25) // try to use 25% of width, but no smaller than 800
        //find popUp center
        var windowLocation = {
            left: (window.screen.availLeft + (window.screen.availWidth / 2)) - (w / 2),
            top: (window.screen.availTop + (window.screen.availHeight / 2)) - (h / 2)
        };
        const confignew = "ModalPopUp" +
            ", toolbar=no" +
            ", scrollbars=no," +
            ", location=no" +
            ", statusbar=no" +
            ", menubar=no" +
            ", resizable=0" +
            ", width=" + w +
            ", height=" + h +
            ", left=" + windowLocation.left +
            ", top=" + windowLocation.top;
        newWindow_wgadswin = window.open(url, 'Microsoft Ads', confignew);
        if (newWindow_wgadswin) {
            newWindow_wgadswin.focus();
            jQuery("#conv_create_mads_new").modal("hide");
        }
    }

    jQuery(document).ready(function($) {
        jQuery('#conv_create_new_bing').on('shown.bs.modal', function() {
            jQuery("#bing_country, #timezones, #currency_code, #language").select2({
                dropdownParent: $("#conv_create_new_bing"),
                //width: '400px',
                placeholder: function() {
                    jQuery(this).data('placeholder');
                }
            });
        });
    });
    jQuery(function() {


        <?php if ((isset($_GET['subscription_id']) || !$microsoft_ads_manager_id || strlen($microsoft_ads_manager_id) < 3) && $ms_email != "") { ?>
            list_microsoft_ads_account(tvc_data);
        <?php } ?>

        jQuery("#ads-continue-close").click(function() {
            list_microsoft_ads_account(tvc_data);
        });

        jQuery(document).on('select2:select', '.microsoft_ads_manager_id', function(e) {
            if (jQuery(this).val() != "" && jQuery(this).val() != undefined) {
                var account_id = jQuery(e.target).val();
                var acctype = jQuery(e.target).attr('acctype');
                var thisselid = e.target.getAttribute('id');
                // console.log(acctype);
                list_microsoft_ads_subaccount(account_id);
                jQuery(".microsoft_ads_manager_id").closest(".conv-hideme-gasettings").find("select").prop(
                    "disabled", false);
            } else {
                jQuery(".microsoft_ads_manager_id").closest(".conv-hideme-gasettings").find("select").prop(
                    "disabled", false);
            }

        });
        jQuery(document).on('select2:select', '.microsoft_ads_subaccount_id', function(e) {
            if (jQuery(this).val() != "" && jQuery(this).val() != undefined) {
                var account_id = jQuery("#microsoft_ads_manager_id").val();
                var subaccount_id = jQuery(e.target).val();
                var acctype = jQuery(e.target).attr('acctype');
                var thisselid = e.target.getAttribute('id');
                // console.log(acctype);
                // list_microsoft_ads_get_UET_tag(account_id, subaccount_id);
                jQuery("#convmicrosoftadseditconfirm").modal('show');
                jQuery(".microsoft_ads_subaccount_id").closest(".conv-hideme-gasettings").find("select").prop(
                    "disabled", false);
            } else {
                jQuery(".microsoft_ads_subaccount_id").closest(".conv-hideme-gasettings").find("select").prop(
                    "disabled", false);
            }

        });
        jQuery(document).on('select2:select', '#bing_country', function(e) {
            if (jQuery(this).val() != "" && jQuery(this).val() != undefined) {
                console.log(jQuery("#bing_country option:selected").text().trim());
                jQuery("#tax_info_key").val(jQuery("#bing_country option:selected").text().trim());
                if (jQuery("#tax_info_key option:selected").val() != undefined) {
                    console.log('tax info required:' + jQuery("#tax_info_key option:selected").val());
                    jQuery(".tax_info_card").removeClass('d-none');
                    jQuery(".tax_info_value label").html(jQuery("#tax_info_key option:selected").text().trim() + '<span class="text-danger"> *</span>');
                    jQuery(".tax_info_value input").attr('required', true);
                } else {
                    console.log('tax info Not required');
                    jQuery(".tax_info_card").addClass('d-none');
                    jQuery("#tax_info_value label").html("");
                    jQuery(".tax_info_value input").removeAttr('required');
                }
            }
        });

        jQuery(".selecttwo").select2({
            minimumResultsForSearch: -1,
            placeholder: function() {
                jQuery(this).data('placeholder');
            }
        });

        jQuery("#conv_create_new_bing_btn").on('click', function(e) {
            if (jQuery("#new_microsoft_ads_id").text() != "") {
                jQuery("#conv_create_mads_new").modal("show");
            } else {
                jQuery("#conv_create_new_bing").modal("show");
            }
        });

        // Create Bing Ads Account
        jQuery("#bingForm").on('submit', function(e) {
            e.preventDefault();

            var website_url = jQuery("#bing_account_name").val();
            var email_address = jQuery("#gmc_email_address").val();
            var store_name = jQuery("#gmc_store_name").val();
            var country = jQuery("#gmc_country").val();
            var customer_id = '<?php echo isset($googleDetail->customer_id) ? esc_html($googleDetail->customer_id) : ''; ?>';
            var adult_content = jQuery("#gmc_adult_content").is(':checked');

            var data = {
                action: "conv_create_bing_account",
                subscription_id: subscription_id,
                account_name: jQuery("#bing_account_name").val(),
                sub_account_name: jQuery("#bing_sub_account_name").val(),
                currency_code: jQuery("#currency_code").val(),
                time_zone: jQuery("#timezones").val(),
                tax_info_key: jQuery("#tax_info_key").val(),
                tax_info_val: jQuery("#tax_info_value").val(),
                market_country: jQuery("#bing_country").val(),
                market_language: jQuery("#language").val(),
                bussiness_name: jQuery("#bussiness_name").val(),
                line1: jQuery("#address_1").val(),
                line2: jQuery("#address_2").val(),
                city: jQuery("#city").val(),
                state: jQuery("#state").val(),
                postal_code: jQuery("#zip").val(),
                tvc_data: tvc_data,
                conversios_onboarding_nonce: "<?php echo esc_js(wp_create_nonce('conversios_onboarding_nonce')); ?>"
            };
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {
                    jQuery('#gadsinviteloader').removeClass('d-none');
                },
                success: function(response, status) {
                    console.log(response);

                    if (response.error === true) {

                        jQuery("#conv_create_new_bing").modal('hide');

                        var error_msg = response.errors;

                        getAlertMessageAll(
                            'info',
                            'Error',
                            message = error_msg,
                            icon = 'info',
                            buttonText = 'Ok',
                            buttonColor = '#FCCB1E',
                            iconImageSrc =
                            '<img src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >'
                        );

                    } else {

                        jQuery("#conv_create_new_bing").modal('hide');

                        jQuery("#new_microsoft_ads_id").text(response.data.AccountId);
                        if (response.data.invitationLink != "") {
                            activationLink = "https://ads.microsoft.com/ActivateCustomer?cid=" + response.data.CustomerId + "&aid=" + response.data.AccountId;
                            jQuery("#ads_invitationLink").attr("href", activationLink);
                            showMAdsModalPopUp(activationLink);
                        } else {
                            jQuery("#invitationLink").html("");
                        }
                        jQuery("#conv_create_mads_new").modal("show");

                        /*getAlertMessageAll(
                            'info',
                            'Success',
                            message = "Please Accept Invitation on your mail, After accepting invitation, you can select your Bing Ads Account.",
                            icon = 'info',
                            buttonText = 'Ok',
                            buttonColor = '#FCCB1E',
                            iconImageSrc =
                            '<img src="<?php //echo esc_url(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); 
                                        ?>"/ >'
                        );*/

                    }

                },
                complete: function() {
                    jQuery('#gadsinviteloader').addClass('d-none');
                }
            });

        });

        // On edit property
        jQuery('.conv-enable-selection').click(function() {
            jQuery(this).addClass('disabled');
            /*var selele = jQuery('.conv-enable-selection').closest(".conv-hideme-gasettings").find(
                "select.ga_analytic_account_id");
            var currele = jQuery(this).closest(".conv-hideme-gasettings").find(
                "select.ga_analytic_account_id");*/

            if (jQuery(this).parent().find('select').attr('id') == 'microsoft_ads_manager_id') {
                list_microsoft_ads_account(tvc_data);
            }
            if (jQuery(this).parent().find('select').attr('id') == 'microsoft_ads_subaccount_id') {
                var account_id = jQuery("#microsoft_ads_manager_id").val();
                list_microsoft_ads_subaccount(account_id);
            }
            // if (jQuery(this).parent().find('select').attr('id') == 'microsoft_ads_pixel_id') {
            //     var account_id = jQuery("#microsoft_ads_manager_id").val();
            //     var subaccount_id = jQuery("#microsoft_ads_subaccount_id").val();
            //     if (account_id != "" && subaccount_id != "") {
            //         list_microsoft_ads_get_UET_tag(account_id, subaccount_id);
            //     }
            // }
        });


        jQuery(document).on("click", ".conv-btn-connect-enabled-microsoft", function(e) {
            microsoft_ads_conversion("Purchase");
            e.preventDefault();
            var has_error = 0;
            var selected_vals = {};
            //selected_vals["microsoft_ads_manager_id"] = "";
            //selected_vals["microsoft_ads_subaccount_id"] = "";
            selected_vals["subscription_id"] = "<?php echo esc_html($tvc_data['subscription_id']) ?>";
            jQuery("#msbing_box").find("select").each(function() {
                if (!jQuery(this).val() || jQuery(this).val() == "" || jQuery(this).val() ==
                    "undefined") {
                    has_error = 1;
                    return;
                } else {
                    selected_vals[jQuery(this).attr('name')] = jQuery(this).val();
                }
            });
            // console.log(selected_vals);
            if (has_error == 1) {
                jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
                jQuery(".conv-btn-connect").removeClass("conv-btn-connect-enabled-microsoft");
                jQuery(".conv-btn-connect").text('Save');
                alert("Please select required fields to continue.");
            } else {
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: tvc_ajax_url,
                    data: {
                        action: "conv_save_pixel_data",
                        pix_sav_nonce: "<?php echo esc_js(wp_create_nonce('pix_sav_nonce_val')); ?>",
                        conv_options_data: selected_vals,
                        conv_options_type: ["eeoptions", "eeapidata", "middleware"],
                        conv_tvc_data: tvc_data,
                    },
                    beforeSend: function() {
                        jQuery(".conv-btn-connect-enabled-microsoft").text("Saving...");
                        conv_change_loadingbar("show");
                        jQuery(this).addClass('disabled');
                    },
                    success: function(response) {
                        var user_modal_txt =
                            "Congratulations, you have successfully connected your";
                        var user_modal_txt2 = "<br>Manager Account ID: " + selected_vals[
                            'microsoft_ads_manager_id'];
                        var user_modal_txt3 = "<br>Sub Account ID: " + selected_vals[
                            'microsoft_ads_subaccount_id'];
                        var user_modal_txt4 = "<br>Pixel ID: " + selected_vals[
                            'microsoft_ads_pixel_id'];

                        user_modal_txt = user_modal_txt + " " + user_modal_txt2 + " " + user_modal_txt3 + " " + user_modal_txt4;

                        if (response == "0" || response == "1") {
                            jQuery(".conv-btn-connect-enabled-microsoft").text("Connect");
                            jQuery("#conv_save_success_txt").html(user_modal_txt);
                            jQuery("#conv_save_success_modal").modal("show");
                        }

                    },
                    complete: function() {
                        conv_change_loadingbar("hide");
                    }
                });
            }
            return false;
        });
    });
</script>

<script>
    jQuery(function() {
        //jQuery("#upgradetopro_modal_link").attr("href", '<?php echo esc_url($TVC_Admin_Helper->get_conv_pro_link_adv("popup", "twittersettings",  "conv-link-blue fw-bold", "linkonly")); ?>');

        let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        let tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>