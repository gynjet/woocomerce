<?php
if (! defined('ABSPATH')) {
    exit;
}
$data = array(
    'help_icon' => esc_url(CHT_PLUGIN_URL."admin/assets/images/help/help-icon.svg"),
    'close_icon' => esc_url(CHT_PLUGIN_URL."admin/assets/images/help/close.svg"),
    'pro_icon' => esc_url(CHT_PLUGIN_URL."admin/assets/images/help/pro.svg"),
    'support_icon' => esc_url(CHT_PLUGIN_URL."admin/assets/images/help/help-circle.svg"),
    'contact_icon' => esc_url(CHT_PLUGIN_URL."admin/assets/images/help/headphones.svg"),
    'get_support_link' => esc_url("https://wordpress.org/support/plugin/chaty/"),
    'upgrade_to_pro_link' => esc_url(admin_url("admin.php?page=chaty-app-upgrade")),
    'recommended_plugins_link' => esc_url(admin_url("admin.php?page=recommended-chaty-plugins")),
    'recommended_plugins_link_status' => get_option("hide_chaty_recommended_plugin"),
    'chaty_live_chat_link' => esc_url(admin_url("admin.php?page=chaty-live-chat")),
    'chaty_live_chat_link_status' =>  apply_filters('check_for_chatway', false),
    'premio_site_info' => esc_url('https://premio.io/'),
    'help_center_link' => esc_url('https://premio.io/help/chaty/?utm_source=pluginspage'),
)

?>
<style>
    /* Old CSS */
    /* Help btn */
        #wpfooter{
       display: none; 
      }
    .premio-help-form {
        position: fixed;
        right: 25px;
        border: 1px solid #e9edf0;
        bottom: 100px;
        background: #fff;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px;
        width: 320px;
        z-index: 1001;
        direction: ltr;
        visibility: hidden;
        opacity: 0;
        transition: .4s;
        -webkit-transition: .4s;
        -moz-transition: .4s
    }
    .premio-help-form.active {
        opacity: 1;
        visibility: visible;
        pointer-events: inherit
    }
    .premio-help-header {
        background: #f4f4f4;
        border-bottom: solid 1px #e9edf0;
        padding: 5px 20px;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        border-radius: 10px 10px 0 0;
        font-size: 16px;
        text-align: right
    }
    .premio-help-header b {
        float: left
    }
    .premio-help-content {
        margin-bottom: 10px;
        padding: 20px 20px 10px
    }
    .premio-help-form p {
        margin: 0 0 1em
    }
    .premio-form-field {
        margin-bottom: 10px
    }
    .premio-form-field input, .premio-form-field textarea {
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        padding: 5px;
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #c5c5c5
    }
    .premio-form-field textarea {
        height: 70px
    }
    .premio-help-button {
        border: none;
        padding: 8px 0;
        width: 100%;
        background: #ff6624;
        color: #fff;
        border-radius: 18px
    }
    .premio-help-form .error-message {
        font-weight: 400;
        font-size: 14px
    }
    .premio-help-form input.input-error, .premio-help-form textarea.input-error {
        border-color: #dc3232
    } 
    p.error-p, p.success-p {
        margin: 0;
        font-size: 14px;
        text-align: center
    } 
    /* Help From */
    p.success-p {
        color: green
    }
    p.error-p {
        color: #dc3232
    }
    html[dir=rtl] .premio-help-btn {
        left: 20px;
        right: auto
    }
    html[dir=rtl] .premio-help-form {
        left: 85px;
        right: auto
    }
    /* Old CSS */
    
    .premio-footer-help {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 0 80px;
        color: #50575e;
        margin-top: 50px;
    }
    .premio-footer-help *{
        box-sizing: border-box
    }
    .premio-help-wrap {
        display: flex;
        justify-content: space-between; 
        padding-left: 180px;
        height: 60px;
    }
    .premio-help-menu {
        display: flex;
        gap: 24px;
        padding: 20px 20px 10px;
    }
    .premio-help-menu a {
        color: #7459B3;
        text-align: center;
        /* font-family: Poppins; */
        font-size: 12px;
        font-style: normal;
        font-weight: 600;
        line-height: 140%; 
        text-decoration: none;
    }
     .premio-help-menu a:focus {
        outline: none !important;
        border: none !important;
        box-shadow: none !important;
    }
    .premio-help-content p {
        color: #83A1B7;
        text-align: center;
        /* font-family: Poppins; */
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 140%;
        margin: 0 !important;
    }
    .premio-help-content p a {
        color: #7459B3;
        font-weight: 700; 
        text-decoration: none;
    }

    .premio-help-btn-wrap { 
        height: 64px;
        width: 64px; 
        position: fixed; 
        bottom: 25px;
        right: 25px; 
    }
    .premio-help-btn-wrap img {
        height: 100%;
        width: 100%;
        margin: 0 auto;
    }
     .premio-help-btn, .premio-help-close-btn  {
        background-color: #7459B3;
        display: inline-block;
        height: 64px;
        width: 64px;
        padding: 11px;
        border-radius: 50%; 
         transition: 0.4s;
    }
    .premio-help-btn.hide{
        display: none;
    }
     .premio-help-close-btn{
        background-color: #000 !important;
        padding: 16px;
        display: none;
    }
    .premio-help-close-btn.show{ 
        display: inline-block;
    }
    .premio-help-btn-wrap span.tooltiptext {
        position: absolute;
        background: #000;
        font-size: 12px;
        color: #fff;
        top: -40px;
        max-width: 140%;
        text-align: center;
        left: -14%;
        border-radius: 5px;
        direction: ltr;
        visibility: visible;
        opacity: 1;
        padding: 8px 12px;
        font-weight: 600;
    } 
    .premio-help-btn-wrap span.tooltiptext:after {
        bottom: -9px;
        content: "";
        transform: translateX(-50%); 
        border-width: 10px 5px 0;
        border-style: solid;
        border-color: #000 transparent transparent;
        left: 75%;
        position: absolute;
    } 
    .premio-form-field input,
    .premio-form-field textarea {
        min-height: 1px;
        line-height: 1.4;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        padding: 5px 10px;
        display: block;
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #c5c5c5;
    }
    .help-form-footer {
        text-align: center;
    }
    .help-form-footer p {
        margin: 0;
        padding: 0;
    }
    .help-form-footer p + p {
        margin: 0;
        padding: 10px 0;
    }
    .premio-help-form input.input-error,
    .premio-help-form textarea.input-error {
        border-color: #dc3232;
    }


    .premio-help-absulate-content {
        position: absolute;
        right: 0;
        bottom: 100%;
        width: 200px;
        visibility: hidden;
        opacity: 0;
        transform: translateY(10px); /* optional slide effect */
        transition: opacity 0.4s ease, transform 0.4s ease;
        pointer-events: none; /* prevent interaction when hidden */
    }
    /* .premio-help-btn-wrap:hover .premio-help-absulate-content, */
    .premio-help-absulate-content.active
    {
        visibility: visible;
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
     .premio-help-absulate-content-single {
           opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(20px);
    }
     .premio-help-absulate-content.active .premio-help-absulate-content-single {
        animation: slideInBottom 0.4s forwards ease;
        pointer-events: auto;
        visibility: visible;
    }
     .premio-help-absulate-content.hide .premio-help-absulate-content-single {
       animation: slideOutTop 0.4s forwards ease;
    }
    @keyframes slideInBottom {
        0% {
            opacity: 0;
            transform: translateY(20px);
            visibility: visible;
        }
        100% {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }
    }
    @keyframes slideOutTop {
        0% {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }
        100% {
            opacity: 0;
            transform: translateY(20px);
            visibility: hidden;
        }
    }
    .premio-help-absulate-content a {
        display: flex;
        justify-content: end;
        gap: 8px;
        align-items: center;
        margin-bottom: 8px;
    }
    .premio-help-absulate-content-single span {
        background-color: #7459B3;
        color: #fff;
    }
    .premio-help-absulate-content-single .icon-img {
        padding: 16px;
        height: 64px;
        width: 64px;
        display: inline-block;
        border-radius: 50%;
    }
    .premio-help-absulate-content-single .icon-img.pro {  
	    background: var(--Main-gradient, linear-gradient(180deg, #F69D01 0%, #F65901 100%));
    }
    .premio-help-absulate-content-single .text {
        padding: 8px 16px;
        border-radius: 8px;
        box-shadow: 0px 12px 16px -4px rgba(10, 13, 18, 0.08), 0px 4px 6px -2px rgba(10, 13, 18, 0.03);
    }
    .premio-form-response p{
        margin: 10px 0;
    }
    @media (max-width: 960px) {
        .premio-help-wrap{
            padding-left: 20px;
        }
    }
    @media (max-width: 576px) {
        .premio-help-menu {
            gap: 8px;
            padding: 0;
        }
        .premio-help-content{
            padding: 0;
        }
    }
    @media (max-width: 480px) {
        .premio-help-menu {
            display: none;
        } 
    }
</style>
<div class="premio-footer-help">
    <div class="premio-help-wrap">
        <div class="premio-help-menu">
                <a target="_blank" href="<?php echo esc_url($data['get_support_link']) ?>"><?php esc_html_e("Get Support", "chaty") ?></a>
                <a target="_blank" href="<?php echo esc_url($data['upgrade_to_pro_link']) ?>"><?php esc_html_e("Upgrade to Pro", "chaty") ?></a>
            <?php if($data['recommended_plugins_link_status'] != true): ?>
                <a target="_blank" href="<?php echo esc_url($data['recommended_plugins_link']) ?>"><?php esc_html_e("Recommended Plugins", "chaty") ?></a>
            <?php endif; ?>
            <?php if($data['chaty_live_chat_link_status'] != true): ?> 
                <a target="_blank" href="<?php echo esc_url($data['chaty_live_chat_link']) ?>"><?php esc_html_e("Add Live Chat", "chaty") ?></a>
            <?php endif; ?>
        </div>
        <div class="premio-help-content">
            <p><?php esc_html_e("Powered by ", "chaty") ?><a target="_blank" href="<?php echo esc_url($data['premio_site_info']) ?>"><?php esc_html_e("Premio", "chaty") ?></a></p>
        </div>
    </div>
    <div class="premio-help-btn-wrap">
    <!-- Free/Pro Only URL Change -->
        <a class="premio-help-btn" href="javascript:;"><img src="<?php echo esc_url($data['help_icon']) ?>" alt="<?php esc_html_e("Need help?", 'chaty'); ?>"  /></a>
        <a class="premio-help-close-btn" href="javascript:;"><img src="<?php echo esc_url($data['close_icon']) ?>" alt="<?php esc_html_e("Close", 'chaty'); ?>"  /></a>
        
        <?php 
            $option = get_option("hide_chaty_cta");
            if ($option !== "yes") { ?>
                <span class="tooltiptext"><?php esc_html_e("Support", "chaty") ?></span>
        <?php  } ?> 
        <div class="premio-help-absulate-content">
            <a target="_blank" href="<?php echo esc_url($data['upgrade_to_pro_link']) ?>" class="premio-help-absulate-content-single premio-click-to-close">
                <span class="text"><?php esc_html_e("Upgrade to Pro", "chaty") ?></span>
                <span class="icon-img pro"><img src="<?php echo esc_url($data['pro_icon']) ?>" alt=""></span>
            </a>
            <a target="_blank"  href="<?php echo esc_url($data['get_support_link']) ?>" class="premio-help-absulate-content-single premio-click-to-close">
                <span class="text"><?php esc_html_e("Get Support", "chaty") ?></span>
                <span class="icon-img"><img src="<?php echo esc_url($data['support_icon']) ?>" alt=""></span>
            </a>
            <a href="javascript:;" class="premio-help-absulate-content-single contact-us-btn">
                <span class="text"><?php esc_html_e("Contact Us", "chaty") ?></span>
                <span class="icon-img"><img src="<?php echo esc_url($data['contact_icon']) ?>" alt=""></span>
            </a>
        </div>

    </div>
    <div class="premio-help-form">
    
        <form action="<?php echo esc_url(admin_url('admin-ajax.php')) ?>" method="post" id="premio-help-form">
            <div class="premio-help-header">
                <b>Gal Dubinski</b>  <?php esc_html_e("Co-Founder at Premio", "chaty") ?>
            </div>
            <div class="premio-help-content">
                <p><?php esc_html_e("Hello! Are you experiencing any problems with Chaty? Please let me know :)", 'chaty'); ?></p>
                <div class="premio-form-field">
                    <input type="text" name="user_email" id="user_email" placeholder="<?php esc_html_e("Email", 'chaty'); ?>">
                </div>
                <div class="premio-form-field">
                    <textarea type="text" name="textarea_text" id="textarea_text" placeholder="<?php esc_html_e("How can I help you?", 'chaty'); ?>"></textarea>
                </div>
                <div class="form-button">
                    <button type="submit" class="premio-help-button" ><?php esc_html_e("Chat", 'chaty') ?></button>
                    <input type="hidden" name="action" value="wcp_admin_send_message_to_owner"  >
                    <input type="hidden" id="nonce" name="nonce" value="<?php echo esc_attr(wp_create_nonce('chaty_send_message_to_owner')) ?>"  >
                </div>
            </div>
            <div class="help-form-footer">
                <p><?php esc_html_e("Or", 'chaty'); ?></p>
                <p><a href="<?php echo esc_url($data['help_center_link']) ?>" target="_blank"><?php esc_html_e("Visit our Help Center >>", 'chaty'); ?></a></p>
            </div>
        </form> 
        <div class="premio-form-response"></div>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        jQuery(".premio-help-btn").click(function(e){
            e.stopPropagation();
             jQuery(".premio-help-btn-wrap .tooltiptext").hide();
            jQuery(".premio-help-close-btn").addClass('show');
            jQuery(".premio-help-btn").addClass('hide');
            jQuery(".premio-help-absulate-content").addClass('active');
            jQuery(".premio-help-absulate-content").removeClass('hide');
             
        });
        jQuery(".premio-help-close-btn").click(function(e){
            e.stopPropagation(); 
            jQuery(".premio-help-close-btn").removeClass('show');
            jQuery(".premio-help-btn").removeClass('hide');
            jQuery(".premio-help-absulate-content").removeClass('active');
            jQuery(".premio-help-absulate-content").addClass('hide');
            jQuery(".premio-help-form").hide();
             
        });
        jQuery(".premio-click-to-close").click(function(e){ 
            jQuery(".premio-help-close-btn").removeClass('show');
            jQuery(".premio-help-btn").removeClass('hide');
            jQuery(".premio-help-absulate-content").removeClass('active');
            jQuery(".premio-help-absulate-content").addClass('hide'); 
             
        });
        jQuery("#premio-help-form").submit(function(){
            jQuery(".premio-help-button").attr("disabled",true);
            jQuery(".premio-help-button").text("<?php esc_html_e("Sending Request...", "chaty") ?>");
            formData = jQuery(this).serialize();
            jQuery.ajax({
                url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                data: formData,
                type: "post",
                success: function(responseArray){
                    jQuery("#premio-help-form").find(".error-message").remove();
                    jQuery("#premio-help-form").find(".input-error").removeClass("input-error");
                    if(responseArray.error == 1) {
                        jQuery(".premio-help-button").attr("disabled",false);
                        jQuery(".premio-help-button").text("<?php esc_html_e("Chat", 'chaty'); ?>");
                        for(i=0;i<responseArray.errors.length;i++) {
                            jQuery("#"+responseArray.errors[i]['key']).addClass("input-error");
                            jQuery("#"+responseArray.errors[i]['key']).after('<span class="error-message">'+responseArray.errors[i]['message']+'</span>');
                        }
                    } else if(responseArray.status == 1) {
                        jQuery(".premio-help-button").text("<?php esc_html_e("Done!", 'chaty'); ?>");
                        setTimeout(function(){
                            jQuery("#user_email").val("");
                            jQuery("#textarea_text").val("");
                            jQuery("#premio-help-form").hide();
                            jQuery(".premio-help-header").hide();
                            jQuery(".help-form-footer").hide();
                            jQuery(".premio-form-response").html("<p class='success-p'><?php esc_html_e("Your message is sent successfully.", 'chaty'); ?></p>");
                        },1000);
                    } else if(responseArray.status == 0) {
                        jQuery("#premio-help-form").hide();
                        jQuery(".premio-help-header").hide();
                        jQuery(".help-form-footer").hide();
                        jQuery(".premio-form-response").html("<p class='error-p'><?php printf(esc_html__("There is some problem in sending request. Please send us mail on %1\$s", 'chaty'), "<a href='mailto:contact@premio.io'>contact@premio.io</a>"); ?></p>");
                    }
                }
            });
            return false;
        });
        jQuery(".contact-us-btn").click(function(e){
            e.stopPropagation(); 
            jQuery(".premio-help-form").show(); 
            jQuery(".premio-help-form").addClass('active');  
            jQuery(".premio-help-absulate-content").removeClass('active');
            jQuery(".premio-help-absulate-content").addClass('hide');
            if(jQuery(".premio-help-btn-wrap .tooltiptext").length) {
                jQuery(".premio-help-btn-wrap .tooltiptext").remove();
                jQuery.ajax({
                    url: "<?php echo esc_url(admin_url('admin-ajax.php')) ?>",
                    data: {
                        nonce: "<?php echo esc_attr(wp_create_nonce("hide_chaty_cta")) ?>",
                        action: "hide_chaty_cta"
                    },
                    type: "post",
                    success: function (responseText) {

                    }
                });
            }

        });
      
        jQuery(".premio-help-form").click(function(e){
            e.stopPropagation();
        });
        jQuery("body").click(function(){
            if(jQuery(".premio-help-form").hasClass("active")) { 
                jQuery(".premio-help-btn").addClass('show'); 
                jQuery(".premio-help-btn").removeClass('hide'); 
                
                jQuery(".premio-help-close-btn").addClass('hide');  
                jQuery(".premio-help-close-btn").removeClass('show'); 
            }
            
            jQuery(".premio-help-form").removeClass("active");
        });
    });
</script>
