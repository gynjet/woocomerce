<?php

/**
 * @since      4.1.4
 * Description: Conversios Onboarding page, It's call while active the plugin
 */
$ee_options = unserialize(get_option('ee_options'));
$ee_api_data = unserialize(get_option('ee_api_data'));
$gm_id = isset($ee_options['gm_id']) ? $ee_options['gm_id'] : "";
$google_ads_id = isset($ee_options['google_ads_id']) ? $ee_options['google_ads_id'] : "";
$google_merchant_id = isset($ee_options['google_merchant_id']) ? $ee_options['google_merchant_id'] : "";
$microsoft_ads_pixel_id = isset($ee_options['microsoft_ads_pixel_id']) ? $ee_options['microsoft_ads_pixel_id'] : "";
$conv_onboarding_done_step = isset($ee_options['conv_onboarding_done_step']) ? $ee_options['conv_onboarding_done_step'] : "";
$conv_onboarding_done = isset($ee_options['conv_onboarding_done']) ? $ee_options['conv_onboarding_done'] : "";
$is_site_verified = isset($ee_api_data['setting']->is_site_verified) ? $ee_api_data['setting']->is_site_verified : "";
$is_domain_claim = isset($ee_api_data['setting']->is_domain_claim) ? $ee_api_data['setting']->is_domain_claim : "";
$onboarding_finish = "";
$total_campaigns = "";
$amount_spends = "";
$gads_billing_info = "";
$gAds_billing_status = "";
$gmc_business_info = "";
$gmc_name = '';
$gmc_users = [];
$gmc_business_information = [];
$info_missing = false;
$saved_users = [];
$phone_verification_status = "";
$customObj = new CustomApi();
$PMax_Helper = new Conversios_PMax_Helper();
$TVC_Admin_Helper = new TVC_Admin_Helper();
$subscription_id = sanitize_text_field($TVC_Admin_Helper->get_subscriptionId());
$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$store_id = $google_detail['setting']->store_id;


if ($google_ads_id != "") {
    $gads_billing_info = $customObj->get_gads_info($google_ads_id, "billing");
    $gAds_billing_status = $gads_billing_info->gAds_billing_status;
    if ($gAds_billing_status != "PENDING" && CONV_IS_WC) {
        $results = $PMax_Helper->campaign_pmax_list($google_ads_id, '10000', '', '');

        $campaign_results = $results->data->results ?? [];

        if (is_array($campaign_results)) {
            $total_campaigns = sizeof($campaign_results);
        } else {
            $total_campaigns = 0;
        }
    }
    if ($total_campaigns >= 0) {
        $gads_spend_info = $customObj->get_gads_info($google_ads_id, "spend");
        $amount_spends = $gads_spend_info->amount_spends;
    }
}

if ($google_merchant_id != "") {
    $gmc_business_info = $customObj->get_gmc_business_info($google_merchant_id);
    $data = isset($gmc_business_info->original->data) ? $gmc_business_info->original->data : $gmc_business_info;

    // Name check
    $gmc_name = $data->name ?? '';
    if (empty($gmc_name)) $info_missing = true;

    // User check
    $gmc_users = $data->users ?? [];
    if (empty($gmc_users) || empty($gmc_users[0]->emailAddress)) {
        $info_missing = true;
    } else {
        foreach ($gmc_users as $user) {
            $email = isset($user->emailAddress) ? sanitize_email($user->emailAddress) : '';
            $admin = isset($user->admin) ? (bool) $user->admin : false;
            $reportingManager = isset($user->reportingManager) ? (bool) $user->reportingManager : false;

            $saved_users[] = [
                'email' => $email,
                'admin' => $admin,
                'reportingManager' => $reportingManager
            ];
        }
    }
    // Business Info check
    $gmc_business_information = $data->businessInformation ?? [];
    // Check phone verification status
    $phone_verification_status = $gmc_business_information->phoneVerificationStatus ?? "";
    // Address
    $address = $gmc_business_information->address ?? null;
    if (
        empty($address) ||
        empty($address->streetAddress) ||
        empty($address->locality) ||
        empty($address->region) ||
        empty($address->postalCode) ||
        empty($address->country)
    ) {
        $info_missing = true;
    }
    if (!empty($gmc_business_information->phoneVerificationStatus) && $gmc_business_information->phoneVerificationStatus === "UNVERIFIED") {
        $info_missing = true;
    }
    // Phone
    if (empty($gmc_business_information->phoneNumber)) {
        $info_missing = true;
    }
    // Customer Service Info
    $cs = $gmc_business_information->customerService ?? null;
}
?>
<style>
    .imginbox {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100px;
        width: 100px;
        overflow: hidden;
        background-color: #f9f9f9;
        border-radius: 8px;
        border: 1px solid #ebecee;
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
        padding: 3px;
    }

    .imginbox img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        display: block;
    }

    .form-section {
        background-color: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .form-section-title {
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 10px;
        font-weight: 600;
    }

    .form-control {
        border-radius: 4px;
        border: 1px solid #ced4da;
        padding: 8px 12px;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-label {
        margin-bottom: 8px;
    }

    .invalid-feedback {
        font-size: 0.875rem;
    }

    /* Responsive styles for laptop screens */
    @media (max-width: 1350px) {
        .container-fluid {
            padding: 15px;
        }

        .row {
            margin-right: -10px;
            margin-left: -10px;
        }

        .col-8 {
            flex: 0 0 90%;
            max-width: 90%;
            margin: 0 auto;
        }

        .col-7,
        .col-5 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 20px;
        }

        .col-5 img {
            max-width: 60%;
            max-height: 200px;
            margin: 0 auto;
            display: block;
            object-fit: contain;
        }

        .convhori-step-container {
            flex-wrap: wrap;
            justify-content: center;
        }

        .convhori-step-box {
            margin: 10px;
        }

        .convhori-step-box img {
            max-width: 80px;
            max-height: 80px;
            object-fit: contain;
        }

        .convhori-step-text {
            flex-wrap: wrap;
            justify-content: center;
        }

        .convhori-step-text>div {
            margin: 10px;
            text-align: center;
        }

        .modal-dialog {
            max-width: 90%;
        }

        .form-section {
            padding: 15px;
        }

        .row.mb-3 {
            margin-bottom: 15px !important;
        }
    }
</style>
<div class="container-fluid conv_report_mainbox p-4">
    <div class="row">
        <div class="d-flex justify-content-center">
            <div class="conv_pageheading">
                <h2>Pending Configuration Steps
                </h2>
            </div>
        </div>
    </div>
    <div class="row px-0">
        <!-- for GAds -->
        <div class="rounded row p-2 justify-content-center gads-container">
            <div class="bg-white col-8 border p-4 my-2 rounded d-flex align-items-center">
                <?php
                if ($google_ads_id == "") {
                    $store_raw_country = get_option('woocommerce_default_country');
                    $country = explode(":", $store_raw_country);
                    $woo_country = (isset($country[0])) ? $country[0] : "";

                    global $wp_filesystem;
                    $countries = json_decode($wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json"));
                    $credit = json_decode($wp_filesystem->get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/country_reward.json"));
                    $off_country = "";
                    $off_credit_amt = "";
                    if (is_array($countries) || is_object($countries)) {
                        foreach ($countries as $key => $value) {
                            if ($value->code == $woo_country) {
                                $off_country = $value->name;
                                break;
                            }
                        }
                    }

                    if (is_array($credit) || is_object($credit)) {
                        foreach ($credit as $key => $value) {
                            if ($value->name == $off_country) {
                                $off_credit_amt = $value->price;
                                break;
                            }
                        }
                    }
                ?>
                    <div class="textinbox mx-2 w-100">
                        <div class="alert alert-light border-danger border-1 rounded-3 p-3">
                            <div class="row">
                                <div class="col-12">
                                    <h2 class="fw-bold text-dark border-bottom mb-4 pb-3 fs-4 fs-xl-5">
                                        <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/logos/conv_gads_logo.png"); ?>" />
                                        <?php esc_html_e("Boost Your Sales with Google Ads❗", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h2>
                                </div>
                                <div class="col-7 d-flex flex-column justify-content-center">
                                    <div class="mb-2 fs-5 bg-danger text-white text-center">
                                        <?php esc_html_e("Start reaching more customers today!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                    <div class="mb-2 fs-5">
                                        <?php esc_html_e("Connect your Google Ads account and unlock powerful advertising tools to drive traffic and increase conversions.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                    <h3 class="fw-bold text-dark pt-2 fs-5 fs-xl-5"><?php esc_html_e("Why connect Google Ads?", "enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                                    <ul class="list-unstyled mb-3">
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Get your products in front of the right audience.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Run targeted ad campaigns for higher ROI.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Track performance and optimize with data-driven insights.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                    </ul>
                                    <!-- <a href="admin.php?page=conversios-google-analytics&subpage=gadssettings" class="btn btn-primary fw-bold">🔗 Connect Now & Supercharge Your Ad Performance!</a> -->
                                </div>

                                <div class="col-5">
                                    <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/dashboardimages/connect_gads.png"); ?>" />
                                </div>

                                <div class="col-12 flex-row pt-1">
                                    <div class="d-flex justify-content-between align-items-center conv_create_gads_new_card rounded px-3 py-3">
                                        <?php if ($off_credit_amt != "") { ?>
                                            <div class="amtbtn">
                                                <?php echo esc_html($off_credit_amt); ?>
                                            </div>
                                            <div class="div">
                                                <h5 class="text-dark mb-0">
                                                    <?php
                                                    $credit_message = "Your " . $off_credit_amt . " in Ads Credit is ready to be claimed";
                                                    echo esc_html($credit_message);
                                                    ?>
                                                </h5>
                                                <div class="text-dark fs-12 pt-1"><?php esc_html_e("Spend", "enhanced-e-commerce-for-woocommerce-store"); ?> <?php echo esc_html($off_credit_amt); ?> <?php esc_html_e("with Google Ads in the first 60 days to unlock the credit", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                                <span class="text-dark fs-12">
                                                    <?php esc_html_e("Sign up for Google Ads and complete your payment information to apply the offer to", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                                    <br>
                                                    <?php esc_html_e("your account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                                    <a href="https://www.google.com/intl/en_in/ads/coupons/terms/cyoi/" class="" target="_blank">
                                                        <u><?php esc_html_e("Terms and conditions apply.", "enhanced-e-commerce-for-woocommerce-store"); ?></u>
                                                    </a>
                                                </span>
                                            </div>
                                        <?php } else { ?>
                                            <div class="d-flex">
                                                <span class="text-dark d-flex align-items-center">
                                                    <?php esc_html_e("Sign up for Google Ads and complete your payment information to apply the offer to your account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                                </span>
                                            </div>
                                        <?php } ?>
                                        <div class="align-self-center">
                                            <button class="btn btn-primary" type="button" onclick="window.open('<?php echo esc_url(admin_url('admin.php?page=conversios-google-analytics&subpage=gadssettings&from=acc_setup')); ?>', '_blank')">
                                                <?php esc_html_e("Complete Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else if ($gAds_billing_status == "PENDING") { ?>
                    <div class="textinbox mx-2 w-100">
                        <div class="alert alert-light border-danger border-1 rounded-3 p-3">
                            <div class="row">
                                <div class="col-12">
                                    <h2 class="fw-bold text-dark border-bottom mb-4 pb-3 fs-4 fs-xl-5">
                                        <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/logos/conv_gads_logo.png"); ?>" />
                                        <?php esc_html_e("Don't Miss Out - Complete Your Google Ads Billing❗", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h2>
                                </div>
                                <div class="col-7 d-flex flex-column justify-content-center">
                                    <div class="mb-2 fs-5 bg-danger text-white text-center">
                                        <?php esc_html_e("Start reaching more customers today!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                    <div class="mb-2 fs-5">
                                        <?php esc_html_e("Your ads won't run until billing is set up. Complete now to start driving sales!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                    <h3 class="fw-bold text-dark pt-2 fs-5 fs-xl-5">
                                        <?php esc_html_e("Why complete Google Ads billing?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h3>
                                    <ul class="list-unstyled mb-3">
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Launch your ads", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Track conversions", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Reach more customers", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                    </ul>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" type="button" onclick="window.open('<?php echo esc_url('https://ads.google.com/aw/billing/signup'); ?>', '_blank')">
                                            <?php esc_html_e("👉 Complete Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/dashboardimages/connect_gads.png"); ?>" />
                                </div>
                            </div>
                        </div>
                    <?php } else if ($total_campaigns <= 0) { ?>
                        <div class="textinbox mx-2 w-100">
                            <div class="alert alert-light border-danger border-1 rounded-3 p-3">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="fw-bold text-dark border-bottom mb-4 pb-3 fs-4 fs-xl-5">
                                            <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/logos/conv_gads_logo.png"); ?>" />
                                            <?php esc_html_e("Unlock More Sales with Performance Max❗", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </h2>
                                    </div>
                                    <div class="col-7 d-flex flex-column justify-content-center">
                                        <div class="mb-2 fs-5 bg-danger text-white text-center">
                                            <?php esc_html_e("Start reaching more customers today!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </div>
                                        <div class="mb-2 fs-5">
                                            <?php esc_html_e("Reach customers across Google Search, Shopping, Display, Discover, Gmail, and YouTube with a single AI-powered campaign.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </div>
                                        <h3 class="fw-bold text-dark pt-2 fs-5 fs-xl-5">
                                            <?php esc_html_e("Why create a Performance Max campaign?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </h3>
                                        <ul class="list-unstyled mb-3">
                                            <li class="fw-bold d-flex">
                                                <span class="material-symbols-outlined text-success md-18 pe-2">
                                                    check_circle
                                                </span>
                                                <?php esc_html_e("More Visibility – Appear on multiple Google platforms", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </li>
                                            <li class="fw-bold d-flex">
                                                <span class="material-symbols-outlined text-success md-18 pe-2">
                                                    check_circle
                                                </span>
                                                <?php esc_html_e("Smarter Bidding – Google optimizes in real time", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </li>
                                            <li class="fw-bold d-flex">
                                                <span class="material-symbols-outlined text-success md-18 pe-2">
                                                    check_circle
                                                </span>
                                                <?php esc_html_e("Higher Conversions – Reach the right audience at the right time", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </li>
                                        </ul>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" type="button" onclick="window.open('<?php echo esc_url(admin_url('admin.php?page=conversios-pmax&from=create_campaign')); ?>', '_blank')">
                                                <?php esc_html_e("👉 Create Your First PMax Campaign Now!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/dashboardimages/connect_gads.png"); ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } else if ($amount_spends <= 0) { ?>
                        <div class="textinbox mx-2 w-100">
                            <div class="alert alert-light border-danger border-1 rounded-3 p-3">
                                <div class="row">
                                    <div class="col-12">
                                        <h2 class="fw-bold text-dark border-bottom mb-4 pb-3 fs-4 fs-xl-5">
                                            <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/logos/conv_gads_logo.png"); ?>" />
                                            <?php esc_html_e("Your PMax Campaign is Ready — But Not Running❗", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </h2>
                                    </div>
                                    <div class="col-7 d-flex flex-column justify-content-center">
                                        <div class="mb-2 fs-5 bg-danger text-white text-center">
                                            <?php esc_html_e("💰 Don't let your campaign sit idle—Turn it on and start growing your business!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </div>
                                        <div class="mb-2 fs-5">
                                            <?php esc_html_e("You've set up your Performance Max campaign, but it's not live or not spending. That means you're missing out on potential customers across Google Search, Shopping, Display, Discover, Gmail, and YouTube!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </div>
                                        <h3 class="fw-bold text-dark pt-2 fs-5 fs-xl-5">
                                            <?php esc_html_e("Why start spending on your PMax campaign?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </h3>
                                        <ul class="list-unstyled mb-3">
                                            <li class="fw-bold d-flex">
                                                <span class="material-symbols-outlined text-success md-18 pe-2">
                                                    check_circle
                                                </span>
                                                <?php esc_html_e("Get More Visibility – Show up where your customers are", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </li>
                                            <li class="fw-bold d-flex">
                                                <span class="material-symbols-outlined text-success md-18 pe-2">
                                                    check_circle
                                                </span>
                                                <?php esc_html_e("Let Google Optimize – AI-driven bidding for better results", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </li>
                                            <li class="fw-bold d-flex">
                                                <span class="material-symbols-outlined text-success md-18 pe-2">
                                                    check_circle
                                                </span>
                                                <?php esc_html_e("Increase Sales – Reach the right buyers at the right time", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </li>
                                        </ul>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary" type="button" onclick="window.open('<?php echo esc_url(admin_url('admin.php?page=conversios-pmax')); ?>', '_blank')">
                                                <?php esc_html_e("👉 Start Spending Now!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/dashboardimages/connect_gads.png"); ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
            </div>
        </div>
        <!-- for GMC -->
        <div class="rounded row p-2 justify-content-center gmc-container">
            <div class="bg-white col-8 border p-4 my-2 rounded d-flex align-items-center">
                <?php if ($google_merchant_id == "") { ?>
                    <div class="textinbox mx-2 w-100">
                        <div class="alert alert-light border-danger border-1 rounded-3 p-3">
                            <div class="row">
                                <div class="col-12">
                                    <h2 class="fw-bold text-dark border-bottom mb-4 pb-3 fs-4 fs-xl-5">
                                        <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/logos/conv_gmc_logo.png"); ?>" />
                                        <?php esc_html_e("Google Merchant Center Not Connected Yet❗", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h2>
                                </div>
                                <div class="col-7 d-flex flex-column justify-content-center">
                                    <div class="mb-2 fs-5">
                                        <?php esc_html_e("You haven't connected Google Merchant Center yet — your products aren't showing on Google Search, Shopping, YouTube, and more!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                    <h3 class="fw-bold text-dark pt-2 fs-5 fs-xl-5">
                                        <?php esc_html_e("Why connect Google Merchant Center?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h3>
                                    <ul class="list-unstyled mb-3">
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("More Visibility – Reach millions of shoppers", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Boost Sales – Drive traffic to your store", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                    </ul>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" type="button" onclick="window.open('<?php echo esc_url(admin_url('admin.php?page=conversios-google-shopping-feed&subpage=gmcsettings&from=acc_setup')); ?>', '_blank')">
                                            <?php esc_html_e("🔗 Connect Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </button>
                                    </div>
                                    <div class="mt-2 fs-12">
                                        <?php esc_html_e("Don't miss out on potential customers! Connect your Google Merchant Center now and start growing your business.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/dashboardimages/gmc_dashboard.png"); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else if ($info_missing == "true") { ?>
                    <div class="textinbox mx-2 w-100">
                        <div class="alert alert-light border-danger border-1 rounded-3 p-3">
                            <div class="row">
                                <div class="col-12">
                                    <h2 class="fw-bold text-dark border-bottom mb-4 pb-3 fs-4 fs-xl-5">
                                        <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/logos/conv_gmc_logo.png"); ?>" />
                                        <?php esc_html_e("Complete your business information", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h2>
                                </div>
                                <div class="col-7 d-flex flex-column justify-content-center">
                                    <div class="mb-2 fs-5">
                                        <?php esc_html_e("Complete your business details to start reaching more customers, building trust, and driving more sales.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                    <h3 class="fw-bold text-dark pt-2 fs-5 fs-xl-5">
                                        <?php esc_html_e("Why Complete Your Business Details?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h3>
                                    <ul class="list-unstyled mb-3">
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Build Credibility – Show customers that your business is authentic", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Boost Visibility – Help Google understand and feature your store better", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Streamline Approvals – Avoid delays in Merchant Center verification", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                    </ul>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#businessDetailsModal">
                                            <?php esc_html_e("Update Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/dashboardimages/gmc_dashboard.png"); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else if ($is_site_verified == 0 || $is_domain_claim == 0) { ?>
                    <div class="textinbox mx-2 w-100">
                        <div class="alert alert-light border-danger border-1 rounded-3 p-3">
                            <div class="row">
                                <div class="col-12">
                                    <h2 class="fw-bold text-dark border-bottom mb-4 pb-3 fs-4 fs-xl-5">
                                        <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/logos/conv_gmc_logo.png"); ?>" />
                                        <?php esc_html_e("Verify Your Store & Start Selling on Google!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h2>
                                </div>
                                <div class="col-7 d-flex flex-column justify-content-center">
                                    <div class="mb-2 fs-5">
                                        <?php esc_html_e("Your online store isn’t verified yet — this means your products may not show up on Google Shopping & Search!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                    <h3 class="fw-bold text-dark pt-2 fs-5 fs-xl-5">
                                        <?php esc_html_e("Why complete verification?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h3>
                                    <ul class="list-unstyled mb-3">
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Quick & Easy Verification – Use Google Analytics, Tag Manager, or HTML", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Boost Visibility – Get discovered by millions of shoppers", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                    </ul>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" type="button" onclick="window.open('<?php echo esc_url(admin_url('admin.php?page=conversios-google-shopping-feed&subpage=gmcsettings')); ?>', '_blank')">
                                            <?php esc_html_e("🔗 Verify Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </button>
                                    </div>
                                    <div class="mt-2 fs-12 text-center">
                                        <?php esc_html_e("Don’t wait—verify your store now and start reaching more customers!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/dashboardimages/gmc_dashboard.png"); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <!-- for Microsoft -->
        <?php if (($microsoft_ads_pixel_id != "" ||  ($gads_billing_info != "PENDING" && !CONV_IS_WC)) || ($amount_spends > 0 && $is_site_verified == "1" && $is_domain_claim == "1")) { ?>
            <div class="rounded row p-2 justify-content-center microsoft-container">
                <div class="bg-white col-8 border p-4 my-2 rounded d-flex align-items-center">
                    <div class="textinbox mx-2 w-100">
                        <div class="alert alert-light border-danger border-1 rounded-3 p-3">
                            <div class="row">
                                <div class="col-12">
                                    <h2 class="fw-bold text-dark border-bottom mb-4 pb-3 fs-4 fs-xl-5">
                                        <img class="align-self-center" style="width: 30px;" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/logos/ms-logo.png"); ?>" />
                                        <?php esc_html_e("Boost Your Sales with Microsoft Ads!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h2>
                                </div>
                                <div class="col-7 d-flex flex-column justify-content-center">
                                    <div class="mb-2 fs-5">
                                        <?php esc_html_e("Show your products across Bing, Yahoo, and Microsoft partner networks. Reach high-intent shoppers and grow your store’s revenue.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </div>
                                    <h3 class="fw-bold text-dark pt-2 fs-5 fs-xl-5">
                                        <?php esc_html_e("Why connect Microsoft Ads?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h3>
                                    <ul class="list-unstyled mb-3">
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Reach new audiences on Microsoft", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Run cost-effective ad campaigns", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                        <li class="fw-bold d-flex">
                                            <span class="material-symbols-outlined text-success md-18 pe-2">
                                                check_circle
                                            </span>
                                            <?php esc_html_e("Track results and optimize for growth", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </li>
                                    </ul>
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" type="button" onclick="window.open('<?php echo esc_url(admin_url('admin.php?page=conversios-google-analytics&subpage=bingsettings')); ?>', '_blank')">
                                            <?php esc_html_e("🔗 Connect Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-5">
                                    <img class="align-self-center" src="<?php echo esc_url(ENHANCAD_PLUGIN_URL . "/admin/images/dashboardimages/connect_gads.png"); ?>" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <!-- Business Details Modal -->
    <div class="modal fade" id="businessDetailsModal" tabindex="-1" aria-labelledby="businessDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="businessDetailsModalLabel"><?php esc_html_e("Update Business Details", "enhanced-e-commerce-for-woocommerce-store"); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="businessDetailsForm" class="needs-validation" novalidate>
                        <!-- Business Information Section -->
                        <div class="form-section mb-4">
                            <h6 class="form-section-title mb-3 text-primary"><?php esc_html_e("Business Information", "enhanced-e-commerce-for-woocommerce-store"); ?></h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="businessName" class="form-label fw-bold"><?php esc_html_e("Business Name", "enhanced-e-commerce-for-woocommerce-store"); ?><span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" id="businessName" name="businessName" value="<?php echo esc_attr($gmc_name); ?>" required>
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter business name", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="emailAddress" class="form-label fw-bold"><?php esc_html_e("Email Address", "enhanced-e-commerce-for-woocommerce-store"); ?> <span class="text-danger"> *</span></label>
                                    <input type="email" class="form-control" id="emailAddress" name="emailAddress" value="<?php echo esc_attr($gmc_users[0]->emailAddress ?? ''); ?>" required>
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter a valid email address", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="phoneNumber" class="form-label fw-bold"><?php esc_html_e("Phone Number", "enhanced-e-commerce-for-woocommerce-store"); ?> <span class="text-danger"> *</span></label>
                                    <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo esc_attr($gmc_business_information->phoneNumber ?? ''); ?>" <?php echo !empty($gmc_business_information->phoneNumber) ? 'readonly' : ''; ?> required>
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter phone number", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                    <?php if ($phone_verification_status === "UNVERIFIED") { ?>
                                        <small class="form-text text-muted pt-2 d-block">
                                            <span class="text-danger"><?php esc_html_e("Your phone number is not verified. To verify it, please ", "enhanced-e-commerce-for-woocommerce-store"); ?> </span> <a class="text-blue" href="https://merchants.google.com/mc/merchantprofile/businessinfo/edit?a=" target="_blank"><?php esc_html_e("click here.", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                        </small>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <!-- Address Section -->
                        <div class="form-section mb-4">
                            <h6 class="form-section-title mb-3 text-primary"><?php esc_html_e("Business Address", "enhanced-e-commerce-for-woocommerce-store"); ?></h6>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="streetAddress" class="form-label fw-bold"><?php esc_html_e("Street Address", "enhanced-e-commerce-for-woocommerce-store"); ?> <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" id="streetAddress" name="streetAddress" value="<?php echo esc_attr($gmc_business_information->address->streetAddress ?? ''); ?>" required>
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter street address", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="locality" class="form-label fw-bold"><?php esc_html_e("City", "enhanced-e-commerce-for-woocommerce-store"); ?> <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" id="locality" name="locality" value="<?php echo esc_attr($gmc_business_information->address->locality ?? ''); ?>" required>
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter city", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="region" class="form-label fw-bold"><?php esc_html_e("State/Region", "enhanced-e-commerce-for-woocommerce-store"); ?> <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" id="region" name="region" value="<?php echo esc_attr($gmc_business_information->address->region ?? ''); ?>" required>
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter state/region", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="postalCode" class="form-label fw-bold"><?php esc_html_e("Postal Code", "enhanced-e-commerce-for-woocommerce-store"); ?><span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" id="postalCode" name="postalCode" value="<?php echo esc_attr($gmc_business_information->address->postalCode ?? ''); ?>" required>
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter postal code", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Service Section -->
                        <div class="form-section mb-4">
                            <h6 class="form-section-title mb-3 text-primary"><?php esc_html_e("Customer Service Information", "enhanced-e-commerce-for-woocommerce-store"); ?></h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="customerServiceEmail" class="form-label"><?php esc_html_e("Customer Service Email", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                                    <input type="email" class="form-control" id="customerServiceEmail" name="customerServiceEmail" value="<?php echo esc_attr($gmc_business_information->customerService->email ?? ''); ?>">
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter a valid customer service email", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                </div>
                                <div class="col-md-6">
                                    <label for="customerServicePhone" class="form-label"><?php esc_html_e("Customer Service Phone", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                                    <input type="tel" class="form-control" id="customerServicePhone" name="customerServicePhone" value="<?php echo esc_attr($gmc_business_information->customerService->phoneNumber ?? ''); ?>">
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter customer service phone", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="customerServiceUrl" class="form-label"><?php esc_html_e("Customer Service URL", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                                    <input type="url" class="form-control" id="customerServiceUrl" name="customerServiceUrl" value="<?php echo esc_attr($gmc_business_information->customerService->url ?? ''); ?>">
                                    <div class="invalid-feedback"><?php esc_html_e("Please enter a valid customer service URL", "enhanced-e-commerce-for-woocommerce-store"); ?></div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e("Close", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                    <button type="button" class="btn btn-primary" id="submitBusinessDetails" disabled><?php esc_html_e("Save Changes", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {

        <?php
        $hide_microsoft = false;
        $gads_not_ready = (
            ($gAds_billing_status === "PENDING" && $google_ads_id == "") ||
            (defined('CONV_IS_WC') && CONV_IS_WC && $amount_spends <= 0 && $is_site_verified != "1" && $is_domain_claim != "1")
        );
        if ($microsoft_ads_pixel_id != "" || $gads_not_ready) {
            $hide_microsoft = true;
        }
        ?>

        <?php if (($amount_spends > 0 && $google_ads_id != "") || ($google_ads_id != "" && $gAds_billing_status !== 'PENDING')) { ?>
            jQuery('.gads-container').addClass('d-none');
        <?php } ?>

        <?php if ($is_site_verified == 1 && $is_domain_claim == 1 && $google_merchant_id != "") { ?>
            jQuery('.gmc-container').addClass('d-none');
        <?php } ?>

        <?php if ($hide_microsoft) : ?>
            jQuery('.microsoft-container').addClass('d-none');
        <?php endif; ?>

        <?php if (!CONV_IS_WC) { ?>
            jQuery('.gmc-container').addClass('d-none');
        <?php } ?>

        <?php if ((defined('CONV_IS_WC') && CONV_IS_WC && $amount_spends >= 0 && $is_site_verified == "1" && $is_domain_claim == "1" && $google_merchant_id != "" && $google_ads_id != "" && $microsoft_ads_pixel_id != "") || ($google_ads_id != "" && $google_merchant_id != "" && $microsoft_ads_pixel_id != "" && $gAds_billing_status != "PENDING")) {
            wp_redirect(admin_url('admin.php?page=conversios-analytics-reports'));
            exit;
        } ?>
        // Cache form and button using jQuery
        const form = jQuery('#businessDetailsForm');
        const submitButton = jQuery('#submitBusinessDetails');

        // Email validation function
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        // URL validation function
        function isValidUrl(url) {
            if (!url) return true; // URL is optional
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        }

        // Function to validate form
        function validateForm() {
            const requiredFields = form.find('[required]');
            let isValid = true;

            requiredFields.each(function() {
                const field = jQuery(this);
                const value = field.val().trim();

                if (!value) {
                    isValid = false;
                    field.addClass('is-invalid');
                    return;
                }

                // Email validation
                if (field.attr('type') === 'email') {
                    if (!isValidEmail(value)) {
                        isValid = false;
                        field.addClass('is-invalid');
                        return;
                    }
                }

                // URL validation
                if (field.attr('id') === 'customerServiceUrl') {
                    if (!isValidUrl(value)) {
                        isValid = false;
                        field.addClass('is-invalid');
                        return;
                    }
                }

                field.removeClass('is-invalid');
            });

            submitButton.prop('disabled', !isValid);
        }

        // Real-time validation for email fields
        jQuery('#emailAddress, #customerServiceEmail').on('input', function() {
            const field = jQuery(this);
            const value = field.val().trim();

            if (value && !isValidEmail(value)) {
                field.addClass('is-invalid');
                field.next('.invalid-feedback').show();
            } else {
                field.removeClass('is-invalid');
                field.next('.invalid-feedback').hide();
            }
            validateForm();
        });

        // Real-time validation for URL field
        jQuery('#customerServiceUrl').on('input', function() {
            const field = jQuery(this);
            const value = field.val().trim();

            if (value && !isValidUrl(value)) {
                field.addClass('is-invalid');
                field.next('.invalid-feedback').show();
            } else {
                field.removeClass('is-invalid');
                field.next('.invalid-feedback').hide();
            }
            validateForm();
        });

        // Numeric-only input for phone fields
        jQuery('#phoneNumber, #customerServicePhone').on('input', function() {
            const field = jQuery(this);
            const value = field.val().replace(/[^0-9]/g, '');
            field.val(value);

            if (field.prop('required') && !value) {
                field.addClass('is-invalid');
                field.next('.invalid-feedback').show();
            } else {
                field.removeClass('is-invalid');
                field.next('.invalid-feedback').hide();
            }
            validateForm();
        });

        // Form input & submit validation
        form.on('input', validateForm);
        form.on('submit', validateForm);

        // Submit button click handler
        submitButton.on('click', function(e) {
            e.preventDefault(); // Prevent default submit behavior

            if (form[0].checkValidity()) {
                const savedUsers = <?php echo json_encode($saved_users); ?>; // this should be a valid array of user objects

                const postData = {
                    action: 'update_business_details',
                    conversios_nonce: '<?php echo esc_js(wp_create_nonce("conversios_nonce")); ?>',
                    store_id: '<?php echo esc_js($store_id); ?>',
                    subscription_id: '<?php echo esc_js($subscription_id); ?>',
                    account_id: '<?php echo esc_js($google_merchant_id); ?>',
                    name: jQuery('#businessName').val(),
                    phoneNumber: jQuery('#phoneNumber').val(),
                    users: JSON.stringify(savedUsers),
                    address: JSON.stringify({
                        streetAddress: jQuery('#streetAddress').val(),
                        locality: jQuery('#locality').val(),
                        region: jQuery('#region').val(),
                        postalCode: jQuery('#postalCode').val(),
                        country: 'IN'
                    }),
                    customerService: JSON.stringify({
                        url: jQuery('#customerServiceUrl').val() || '',
                        email: jQuery('#customerServiceEmail').val() || '',
                        phoneNumber: jQuery('#customerServicePhone').val() || ''
                    })
                };

                submitButton.prop('disabled', true).html('<?php esc_html_e("Saving...", "enhanced-e-commerce-for-woocommerce-store"); ?>');
                console.log(postData);
                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: postData,
                    success: function(response) {
                        const message = jQuery('<div class="alert alert-dismissible fade show" role="alert">')
                            .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');

                        if (response.success) {
                            message
                                .addClass('alert-success')
                                .prepend(response.data.message || '<?php esc_html_e("Business details updated successfully!", "enhanced-e-commerce-for-woocommerce-store"); ?>');
                            form.before(message);

                            setTimeout(function() {
                                jQuery('#businessDetailsModal').modal('hide');
                                location.reload();
                            }, 2000);
                        } else {
                            message
                                .addClass('alert-danger')
                                .prepend(response.data.message || '<?php esc_html_e("Error updating business details", "enhanced-e-commerce-for-woocommerce-store"); ?>');
                            form.before(message);
                            submitButton.prop('disabled', false).html('<?php esc_html_e("Save Changes", "enhanced-e-commerce-for-woocommerce-store"); ?>');
                        }
                    },
                    error: function() {
                        const errorMessage = jQuery('<div class="alert alert-danger alert-dismissible fade show" role="alert">')
                            .html('<?php esc_html_e("Error updating business details. Please try again.", "enhanced-e-commerce-for-woocommerce-store"); ?>')
                            .append('<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>');

                        form.before(errorMessage);
                        submitButton.prop('disabled', false).html('<?php esc_html_e("Save Changes", "enhanced-e-commerce-for-woocommerce-store"); ?>');
                    }
                });
            }
        });
    });
</script>