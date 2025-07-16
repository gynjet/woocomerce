$=jQuery;
function loaderSection(isShow) {
  if (isShow){
    jQuery('#loader-section').show();
  }else{
    jQuery('#loader-section').hide();
  }
}
var tvc_time_out="";
function add_message(type, msg, is_close = true){
  let tvc_popup_box = document.getElementById('tvc_onboarding_popup_box');
  tvc_popup_box.classList.remove("tvc_popup_box_close");
  tvc_popup_box.classList.add("tvc_popup_box");
  if(type == "success"){
    document.getElementById('tvc_onboarding_popup_box').innerHTML ="<div class='alert tvc-alert-success'>"+msg+"</div>";
  }else if(type == "error"){
    document.getElementById('tvc_onboarding_popup_box').innerHTML ="<div class='alert tvc-alert-error'>"+msg+"</div>";
  }else if(type == "warning"){
    document.getElementById('tvc_onboarding_popup_box').innerHTML ="<div class='alert tvc-alert-warning'>"+msg+"</div>";
  }
  if(is_close){
    tvc_time_out = setTimeout(function(){  //tvc_popup_box.style.display = "none";       
      tvc_popup_box.classList.add("tvc_popup_box_close");
      tvc_popup_box.classList.remove("tvc_popup_box");        
    }, 4000);
  } 
}

function is_validate_step(step){
  var is_valide = false;
  if(step == "step_1"){
    var web_property_id = ""; var ua_account_id = ""; var web_measurement_id = ""; var ga4_account_id = "";
    var tracking_option = jQuery('input[type=radio][name=analytic_tag_type]:checked').val();
    //console.log(tracking_option);
    if(tracking_option == "UA"){
      web_property_id = jQuery('#ua_web_property_id_option_val').attr("data-val");
      ua_account_id = jQuery("#ua_web_property_id_option_val").data('accountid');
      if(web_property_id == undefined || web_property_id == ""){        
        msg = "Please select web property id.";        
      }else{
        is_valide = true;        
      }
    }else if(tracking_option == "GA4"){
      web_measurement_id = jQuery('#ga4_web_measurement_id_option_val').attr("data-val");
      ga4_account_id = jQuery("#ga4_web_measurement_id_option_val").data('accountid');
      if(web_measurement_id == undefined || web_measurement_id == ""){
        msg = "Please select measurement id.";
      }else{
        is_valide = true;
      }
    }else{
      web_property_id = jQuery('#both_ua_web_property_id_option_val').attr("data-val");
      ua_account_id = jQuery("#both_ua_web_property_id_option_val").data('accountid');
      web_measurement_id = jQuery('#both_ga4_web_measurement_id_option_val').attr("data-val");
      ga4_account_id = jQuery("#both_ga4_web_measurement_id_option_val").data('accountid');
      //console.log(web_property_id+"=="+web_measurement_id);
      if((web_property_id == undefined || web_property_id == "") || (web_measurement_id == undefined || web_measurement_id == "") ){
        msg = "Please select property/measurement id.";
      }else{
        is_valide = true;
      }
    }
    //console.log("is_valide"+is_valide+"-"+tracking_option+"-"+web_property_id);
    if(is_valide){
      jQuery('#step_1').prop('disabled', false);
    }else{
      jQuery('#step_1').prop('disabled', true);
    }
  }else if(step == "step_2"){
    google_ads_id = jQuery('#ads-account').val();
    if(google_ads_id == ""){
      msg = "You have not selected any google ads account. Please select it from the dropdown or create a new one to continue.";
      add_message("error",msg);
    }else{
      is_valide = true;
    }
    if(is_valide){
      jQuery('#step_2').prop('disabled', false);
    }else{
      jQuery('#step_2').prop('disabled', true);
    }
    
  }
  return is_valide;
}
jQuery(document).ready(function () {
  loaderSection(false);
  //step-1
  jQuery('.tvc-dropdown-header').click(function(event){
    jQuery(this).next().toggle();
    event.stopPropagation();
  })

  jQuery(window).click(function(){
      jQuery('.tvc-dropdown-content').hide();
  })
  
/*jQuery(".google_analytics_sel").on( "change", function() {
    is_validate_step("step_1");
    jQuery(".onbrdstep-1").removeClass('selectedactivestep');
    jQuery(".onbrdstep-3").removeClass('selectedactivestep');
    jQuery(".onbrdstep-2").removeClass('selectedactivestep');
    jQuery("[data-id=step_1]").attr("data-is-done",0);
    jQuery("[data-id=step_2]").attr("data-is-done",0);
    jQuery("[data-id=step_3]").attr("data-is-done",0);
  }); */
  //step-2
  /*jQuery(".google_ads_sel").on( "change", function() {
    is_validate_step("step_2");
    //jQuery(".onbrdstep-1").removeClass('selectedactivestep');
    jQuery(".onbrdstep-3").removeClass('selectedactivestep');
    jQuery(".onbrdstep-2").removeClass('selectedactivestep');
    //jQuery("[data-id=step_1]").attr("data-is-done",0);
    jQuery("[data-id=step_2]").attr("data-is-done",0);
    jQuery("[data-id=step_3]").attr("data-is-done",0);
  }); */
  jQuery('input[type=checkbox]:not(#adult_content, #terms_conditions)').change(function() {
    //jQuery(".onbrdstep-1").removeClass('selectedactivestep');
    jQuery(".onbrdstep-3").removeClass('selectedactivestep');
    jQuery(".onbrdstep-2").removeClass('selectedactivestep');
   // jQuery("[data-id=step_1]").attr("data-is-done",0);
    jQuery("[data-id=step_2]").attr("data-is-done",0);
    jQuery("[data-id=step_3]").attr("data-is-done",0);
  });
  
  //select2
  //jQuery(".select2").select2();
  // desable to close advance settings
  jQuery(".advance-settings .dropdown-menu").click(function(e){
      e.stopPropagation();
  });
});
function validate_google_analytics_sel(){
  //is_validate_step("step_1");
  jQuery(".onbrdstep-1").removeClass('selectedactivestep');
  jQuery(".onbrdstep-3").removeClass('selectedactivestep');
  jQuery(".onbrdstep-2").removeClass('selectedactivestep');
  jQuery("[data-id=step_1]").attr("data-is-done",0);
  jQuery("[data-id=step_2]").attr("data-is-done",0);
  jQuery("[data-id=step_3]").attr("data-is-done",0);
}
//save analytics web properties and ads account while next button click
function save_google_ga_ads_data(google_ads_id, tvc_data, subscription_id, tracking_option){  
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  if(subscription_id ){
    //GA start 
    var web_measurement_id = "";
    var web_property_id = "";
    var ga4_account_id = "";
    var ua_account_id = "";
    var is_valide = true;
    var msg ="";
    if(tracking_option == "UA"){
      web_property_id = jQuery('#ua_web_property_id_option_val').attr("data-val");
      ua_account_id = jQuery("#ua_web_property_id_option_val").attr('data-accountid');          
    }else if(tracking_option == "GA4"){
      web_measurement_id = jQuery('#ga4_web_measurement_id_option_val').attr("data-val");
      ga4_account_id = jQuery("#ga4_web_measurement_id_option_val").attr('data-accountid');        
    }else if(tracking_option == "BOTH"){
      web_property_id = jQuery('#both_ua_web_property_id_option_val').attr("data-val");
      ua_account_id = jQuery("#both_ua_web_property_id_option_val").attr('data-accountid');
      web_measurement_id = jQuery('#both_ga4_web_measurement_id_option_val').attr("data-val");
      ga4_account_id = jQuery("#both_ga4_web_measurement_id_option_val").attr('data-accountid');
      if(web_property_id == "" || web_measurement_id == ""){
        is_valide = false;
        msg = "Please select property/measurement id.";
      }
    }
    loaderSection(true);
     if(is_valide != true){
       add_message("warning",msg);
       loaderSection(false);
      return false;
     }else{
      var data =[]; 
      if(web_property_id || web_measurement_id || google_ads_id){
         data = {
          action: "save_analytics_data",
          subscription_id:subscription_id,
          tracking_option: tracking_option,
          web_measurement_id: web_measurement_id,
          web_property_id: web_property_id,
          ga4_account_id: ga4_account_id,
          ua_account_id: ua_account_id,
          enhanced_e_commerce_tracking: jQuery('#enhanced_e_commerce_tracking').is(':checked'),
          user_time_tracking: jQuery('#user_time_tracking').is(':checked'),
          add_gtag_snippet: jQuery('#add_gtag_snippet').is(':checked'),
          client_id_tracking: jQuery('#client_id_tracking').is(':checked'),
          exception_tracking: jQuery('#exception_tracking').is(':checked'),
          enhanced_link_attribution_tracking: jQuery('#enhanced_link_attribution_tracking').is(':checked'),
          tvc_data:tvc_data,
          conversios_onboarding_nonce:conversios_onboarding_nonce,
          google_ads_id: google_ads_id,
          remarketing_tags: jQuery('#remarketing_tag').is(':checked'),
          dynamic_remarketing_tags: jQuery('#dynamic_remarketing_tags').is(':checked'),
          google_ads_conversion_tracking: jQuery("#google_ads_conversion_tracking").val(),
          link_google_analytics_with_google_ads: jQuery("#link_google_analytics_with_google_ads").val(),
        };
      }else{
         data = {
          action: "save_analytics_data",
          subscription_id:subscription_id,
          tracking_option: tracking_option,
          web_measurement_id: "",
          web_property_id: "",
          google_ads_id:"",
          conversios_onboarding_nonce:conversios_onboarding_nonce
        }
      }      
      //console.log("data",data);
      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: data,
        beforeSend: function () {        
        },
        success: function (response) {
          var btn_cam = 'save_ads';
          if(response.error === false) {
            var error_msg = 'null';
             //user_tracking_data(btn_cam, error_msg, 'conversios_onboarding','Save_Google_Ads_account'); 
            add_message("success","Google Analytics and Pixels successfully updated.");
            let tracking_option = jQuery('input:radio[name=analytic_tag_type]:checked').val();
            var s_tracking_option = tracking_option.toLowerCase();
            
            if (jQuery("#link_google_analytics_with_google_ads").is(':checked')) {         
              if(tracking_option == "UA" || tracking_option == "BOTH"){
                if(tracking_option == "BOTH"){
                  s_tracking_option = "both_ua";
                }
                var profile_id = jQuery("#"+s_tracking_option+"_web_property_id_option_val").attr('data-profileid');
                var UalinkData = {
                    action: "link_analytic_to_ads_account",
                    type: "UA",
                    ads_customer_id: google_ads_id,
                    analytics_id: jQuery("#"+s_tracking_option+"_web_property_id_option_val").attr('data-accountid'),
                    web_property_id: jQuery("#"+s_tracking_option+"_web_property_id_option_val").attr("data-val"),
                    profile_id: profile_id,
                    tvc_data:tvc_data,
                    conversios_onboarding_nonce:conversios_onboarding_nonce
                };
                //console.log("google_ads_id"+google_ads_id+"profile_id"+profile_id);
                //console.log(UalinkData);
                if(google_ads_id != "" && profile_id != undefined){
                  setTimeout(function(){  
                    //link_analytic_to_ads_account(UalinkData);
                    jQuery.ajax({
                      type: "POST",
                      dataType: "json",
                      url: tvc_ajax_url,
                      data: UalinkData,
                      success: function (response) {
                        clearTimeout(tvc_time_out);
                        if(response.error === false){
                          add_message("success","Google Ananlytics and Google Ads linked successfully.");
                        }else{
                          //const errors = JSON.parse(response?.errors[0]);
                          //add_message("error",errors?.message);
                        }

                        /* start GA4 */
                        if(tracking_option == "GA4" || tracking_option == "BOTH"){
                          if(tracking_option == "BOTH"){
                            s_tracking_option = "both_ga4";
                          }
                          var web_property = jQuery("#"+s_tracking_option+"_web_measurement_id_option_val").attr('data-name');
                          var Ga4linkData = {
                            action: "link_analytic_to_ads_account",
                            type: "GA4",
                            ads_customer_id: google_ads_id,
                            web_property_id: jQuery("#"+s_tracking_option+"_web_measurement_id_option_val").attr("data-val"),
                            web_property: web_property,
                            tvc_data:tvc_data,
                            conversios_onboarding_nonce:conversios_onboarding_nonce
                          };
                          //console.log("web_property"+web_property);
                          if(google_ads_id != "" && web_property != undefined){
                            //setTimeout(function(){
                              //console.log("cal GA4 link");
                              //link_analytic_to_ads_account(Ga4linkData);
                              jQuery.ajax({
                                type: "POST",
                                dataType: "json",
                                url: tvc_ajax_url,
                                data: Ga4linkData,
                                success: function (response) {
                                  //console.log(response);
                                  clearTimeout(tvc_time_out);
                                  if(response.error === false){
                                    add_message("success","Google ananlytics and google ads linked successfully.");
                                  }else{
                                    //const errors = JSON.parse(response?.errors[0]);
                                    //add_message("error",errors?.message);
                                  }
                                }
                              });
                            //}, 1000); 
                          }
                        }
                        /* end GA4 */
                      }
                    });
                  }, 1000); 
                }
                
              }else if(tracking_option == "GA4" || tracking_option == "BOTH"){
                if(tracking_option == "BOTH"){
                  s_tracking_option = "both_ga4";
                }
                var web_property = jQuery("#"+s_tracking_option+"_web_measurement_id_option_val").attr('data-name');
                var Ga4linkData = {
                  action: "link_analytic_to_ads_account",
                  type: "GA4",
                  ads_customer_id: google_ads_id,
                  web_property_id: jQuery("#"+s_tracking_option+"_web_measurement_id_option_val").attr("data-val"),
                  web_property: web_property,
                  tvc_data:tvc_data,
                  conversios_onboarding_nonce:conversios_onboarding_nonce
                };
                //console.log("web_property"+web_property);
                if(google_ads_id != "" && web_property != undefined){
                  setTimeout(function(){
                    //console.log("cal GA4 link");
                    //link_analytic_to_ads_account(Ga4linkData);
                    jQuery.ajax({
                      type: "POST",
                      dataType: "json",
                      url: tvc_ajax_url,
                      data: Ga4linkData,
                      success: function (response) {
                        //console.log(response);
                        clearTimeout(tvc_time_out);
                        if(response.error === false){
                          add_message("success","Google Ananlytics and Google Ads linked successfully.");
                        }else{
                          //const errors = JSON.parse(response?.errors[0]);
                          //add_message("error",errors?.message);
                        }
                      }
                    });
                  }, 1000); 
                }            
                /* end GA4 */
              }
              
              setTimeout(function(){
                check_oradd_conversion_list(google_ads_id, tvc_data);
              }, 3500);
                       
              loaderSection(false);
              return true;
            }         
          }else{
            var error_msg = response.errors;
             //user_tracking_data(btn_cam, error_msg, 'conversios_onboarding','Save_Google_Ads_account'); 
            add_message("error","Error while updating Google Ads."); 
          }         
          //loaderSection(false);
        }
      });
      return true;
    }/*else{
      // no any data need to save
      return true;
    }  */ 
  }else{
    //jQuery('#tvc_ads_skip_confirm').addClass('showpopup');
    //jQuery('body').addClass('scrlnone');
    //jQuery('#tvc_ads_skip_confirm').modal('show'); 
    //is_validate_step("step_2"); 
    return false;
  }
  return true;
}
function save_merchant_data(google_merchant_center_id, merchant_id, tvc_data, subscription_id, plan_id, is_skip=fals){
  if(google_merchant_center_id || is_skip == true){
    var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
    var website_url = jQuery("#url").val();
    var customer_id = jQuery("#loginCustomerId").val();
    var data = {
      action: "save_merchant_data",
      subscription_id:subscription_id,
      google_merchant_center:google_merchant_center_id,
      merchant_id: merchant_id,
      website_url:website_url,
      customer_id:customer_id,
      tvc_data:tvc_data,
      conversios_onboarding_nonce:conversios_onboarding_nonce
    };
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: data,
      beforeSend: function () { 
        loaderSection(true);       
      },
      success: function (response) {
        let google_ads_id = jQuery("#new_google_ads_id").text();
        if(google_ads_id ==null || google_ads_id ==""){
          google_ads_id = jQuery('#ads-account').val();
        }
         var btn_cam = 'save_gmc';
        
        if (response.error === false) {
          var error_msg = 'null';
          add_message("success","Google Merchant Center account successfully updated.");
          //clearTimeout(tvc_time_out);
          var link_data = {
            action: "link_google_ads_to_merchant_center",
            account_id: google_merchant_center_id,
            merchant_id: merchant_id,
            adwords_id: google_ads_id,
            tvc_data:tvc_data,
            conversios_onboarding_nonce:conversios_onboarding_nonce
          };
          if(google_merchant_center_id != "" && google_ads_id != ""){
            link_google_Ads_to_merchant_center(link_data, tvc_data, subscription_id);
          }else{
            //show_conform_popup();
            //get_subscription_details(tvc_data, subscription_id); 
          } 
        } else {         
          var error_msg = response.errors;
          add_message("error","Error while updating Google Merchant Center.");
        }
        //user_tracking_data(btn_cam, error_msg,'conversios_onboarding','Save_Google_Merchant_account');
        //loaderSection(false);        
      }
    });    
  }else{
    add_message("warning","Missing Google Merchant Center account.");
  }
}

/* get conversion list */
function check_oradd_conversion_list(google_ads_id,  tvc_data){
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  if(google_ads_id ){
    var data = {
        action: "get_conversion_list",
        customer_id:google_ads_id,
        tvc_data:tvc_data,
        conversios_onboarding_nonce:conversios_onboarding_nonce
      };
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: data,
      success: function (response) {
        //console.log(response);
        clearTimeout(tvc_time_out);
        if(response.error === false){
          setTimeout(function(){
            add_message("success",response.message);
          }, 2000);
        }else{
          //const errors = JSON.parse(response.errors[0]);
          if(response.errors){
            setTimeout(function(){
              add_message("error",response.errors);
            }, 2000);
          }
          
        }
      }
    });
  }
}
/* link account code */
/*function link_analytic_to_ads_account(data) {
  $.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: data,
    success: function (response) {
      console.log(response);
      clearTimeout(tvc_time_out);
      if(response.error === false){
        add_message("success","Google ananlytics and google ads linked successfully.");
      }else{
        //const errors = JSON.parse(response?.errors[0]);
        //add_message("error",errors?.message);
      }
    }
  });
}*/

function link_google_Ads_to_merchant_center(link_data, tvc_data, subscription_id){
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: link_data,
    beforeSend: function(){
      //loaderSection(true);
    },
    success: function (response) {
      clearTimeout(tvc_time_out);
      if(response.error === false){        
        add_message("success",response.data.message);
      }else if(response.error == true && response.errors != undefined){
        //const errors = JSON.parse(response.errors[0]);
        //add_message("error",errors.message);
      }else{
        add_message("error","There was an error while link account");
      }
      //get_subscription_details(tvc_data, subscription_id);
      //show_conform_popup();
      //loaderSection(false);      
    }
  });
}
function show_conform_popup(){
  loaderSection(false);
  jQuery('#tvc_confirm_submite').addClass('showpopup');
  jQuery('body').addClass('scrlnone');
}
/* get subscription details */
function get_subscription_details(tvc_data, subscription_id) { 
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val(); 
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "get_subscription_details", tvc_data:tvc_data, subscription_id:subscription_id, conversios_onboarding_nonce:conversios_onboarding_nonce},
    beforeSend: function () {
    },
    success: function (response) {
      if (response.error === false) {
        jQuery("#google_analytics_property_id_info").hide();
        jQuery("#google_analytics_measurement_id_info").hide();
        jQuery("#google_ads_info").hide();
        jQuery("#google_merchant_center_info").hide();
        if(response.data.property_id != ""){
          jQuery("#selected_google_analytics_property").text(response.data.property_id);
          jQuery("#google_analytics_property_id_info").show();
        }
        if(response.data.measurement_id != ""){
          jQuery("#selected_google_analytics_measurement").text(response.data.measurement_id);
          jQuery("#google_analytics_measurement_id_info").show();
        }
        if(response.data.google_ads_id != ""){
          jQuery("#selected_google_ads_account").text(response.data.google_ads_id);
          jQuery("#google_ads_info").show();
        }
        if(response.data.google_merchant_center_id != ""){
          jQuery("#selected_google_merchant_center").text(response.data.google_merchant_center_id);
          jQuery("#google_merchant_center_info").show();
        } 
        jQuery('#tvc_confirm_submite').addClass('showpopup');
        jQuery('body').addClass('scrlnone');       
        //jQuery('#tvc_confirm_submite').modal('show');
      } else {
        add_message("error","Error while fetching subscription data");
      } 
      loaderSection(false);
    }
  });
}
/* List function */
//call get list properties function base on tracking_option
function call_list_analytics_account(tvc_data,page =1){
    let account_list_length = jQuery('#ua_account_id_option option').length;
    if(page == 1){
      list_analytics_account(tvc_data,page);
    }else if(page > 1 && account_list_length > 2){
      list_analytics_account(tvc_data,page);
    }
    /*if(account_list_length < 2 || page != 1){
      list_analytics_account(tvc_data,page);
    }else if( page != 1){
      list_analytics_account(tvc_data,page);
    }*/
}
// get list of google analytics account
function list_analytics_account(tvc_data, page =1) {
  loaderSection(true);
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "get_analytics_account_list",tvc_data:tvc_data,page:page,conversios_onboarding_nonce:conversios_onboarding_nonce},
    success: function (response) {
      if (response && response.error == false) {
           var error_msg = 'null';
           if(response?.data?.items.length > 0){
             var AccOptions = ''; 
             var selected = ''; 
             response?.data?.items.forEach(function(item){
                AccOptions = AccOptions + '<option value="' + item.id + '" ' + selected + 'data-cat="accounts" data-accountid="' + item.id  + '"> ' + item.name +'-'+ item.id +'</option>';
            });
            jQuery('#ga4_account_id_option > .tvc-select-items').append(AccOptions); //GA4 
            jQuery('#ua_account_id_option > .tvc-select-items').append(AccOptions); //GA3 
            jQuery('#both_ua_account_id_option > .tvc-select-items').append(AccOptions); //BOTH GA3
            jQuery('#both_ga4_account_id_option > .tvc-select-items').append(AccOptions); //BOTH GA4
            jQuery(".tvc-edit-accounts").removeClass('tvc-disable-edits');
            jQuery(".tvc-edit-acc_fire").hide();
         }else if(page > 1){//load more error message
            jQuery('.tvc_load_more_acc').hide(); //hide load more
            add_message("error","There are no more Google Analytics accounts associated with this email.");
         }else{
            add_message("error","There are no Google Analytics accounts associated with this email."); 
         }  
         
      }else if( response && response.error == true && response.error != undefined){
        const errors = response.error[0];
        add_message("error",errors);
        var error_msg = errors;
      }else{
         add_message("error","There are no Google Analytics accounts associated with this email.");
      }
      /*setTimeout(function(){
         user_tracking_data(btn_cam, error_msg,'conversios_onboarding','Get_Google_Analytics_account_list'); 
      }, 15000);*/ 
      jQuery("#tvc-ga4-acc-edit-acc_box")?.removeClass('tvc-disable-edits');
      //is_validate_step("step_1");
      loaderSection(false);
    }
  });
}

// get list properties dropdown options
function list_analytics_web_properties(type, tvc_data,account_id) {
  loaderSection(true);
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "get_analytics_web_properties",account_id: account_id,type: type,tvc_data:tvc_data, conversios_onboarding_nonce:conversios_onboarding_nonce},
    success: function (response) {
      if (response && response.error == false) {
           var error_msg = 'null';
              if(type == "GA4"){
                jQuery('#both_ga4_web_measurement_id_option_val').html('Select Measurement Id');
                jQuery('#both_ga4_web_measurement_id_option_val').attr("data-val","");
                jQuery('#both_ga4_web_measurement_id_option_val').attr("data-name","");
                jQuery('#both_ga4_web_measurement_id_option_val').attr("data-accountid","");

                jQuery('#ga4_web_measurement_id_option_val').html('Select Measurement Id');
                jQuery('#ga4_web_measurement_id_option_val').attr("data-val","");
                jQuery('#ga4_web_measurement_id_option_val').attr("data-name","");
                jQuery('#ga4_web_measurement_id_option_val').attr("data-accountid","");

                jQuery('#ga4_web_measurement_id_option > .tvc-select-items').html(''); //GA4 
                jQuery('#both_ga4_web_measurement_id_option > .tvc-select-items').html(''); //Both GA4

               if(response?.data?.wep_measurement.length > 0){
                 var streamOptions = '<option value="">Select Measurement Id</option>'; 
                 var selected = ''; 
                 response?.data?.wep_measurement.forEach(function(item){ let dataName = item.name.split("/");
                    streamOptions = streamOptions + '<option value="' + item.measurementId + '" data-cat="dataStreams" data-name="'+ dataName[1] +'" data-accountid="' + item.accountId  + '">' + item.measurementId +' - '+  item.displayName +'</option>';
                });
                jQuery('#ga4_web_measurement_id_option > .tvc-select-items').append(streamOptions); //GA4 
                jQuery('#both_ga4_web_measurement_id_option > .tvc-select-items').append(streamOptions); //BOTH 
                jQuery("#tvc-ga4-web-edit_box").removeClass('tvc-disable-edits');
                jQuery("#both-tvc-ga4-web-edit_box").removeClass('tvc-disable-edits');
                jQuery("#both-tvc-ga4-acc-edit").hide();
                jQuery("#tvc-ga4-web-edit").hide();
                }else{
                  add_message("error","There are no Google Analytics 4 Properties associated with this email.");
               }
              }
            if(type == "UA"){ 
                jQuery('#ua_web_property_id_option_val').html('Select Property Id');
                jQuery('#ua_web_property_id_option_val').attr("data-val","");
                jQuery('#ua_web_property_id_option_val').attr("data-profileid","");
                jQuery('#ua_web_property_id_option_val').attr("data-accountid","");

                jQuery('#both_ua_web_property_id_option_val').html('Select Property Id');
                jQuery('#both_ua_web_property_id_option_val').attr("data-val","");
                jQuery('#both_ua_web_property_id_option_val').attr("data-profileid","");
                jQuery('#both_ua_web_property_id_option_val').attr("data-accountid","");

                jQuery('#ua_web_property_id_option > .tvc-select-items').html(''); //GA3 
                jQuery('#both_ua_web_property_id_option > .tvc-select-items').html(''); //BOTH GA3

              if(response?.data?.wep_properties.length > 0){ 
                var PropOptions = '<option value="">Select Property Id</option>'; 
                var selected = ''; 
                 response?.data?.wep_properties.forEach(function(item){
                    PropOptions = PropOptions + '<option value="' + item.webPropertyId + '" data-profileid="'+item.id+'" data-cat="webProperties" data-accountid="' + item.accountId  + '"> ' + item.webPropertyId +' - '+ item.name +'</option>';
                });
                jQuery('#ua_web_property_id_option > .tvc-select-items').append(PropOptions); //GA3 
                jQuery('#both_ua_web_property_id_option > .tvc-select-items').append(PropOptions); //BOTH
                jQuery("#tvc-ua-web-edit_box").removeClass('tvc-disable-edits');
                jQuery("#both-tvc-ua-web-edit_box").removeClass('tvc-disable-edits');
                jQuery("#both-tvc-ua-acc-edit").hide();
                jQuery("#tvc-ua-web-edit").hide();
             }else{
                add_message("error","There are no Google Analytics Properties associated with this email.");
             }
            }    
      }else if( response && response.error == true && response.error != undefined){
        const errors = response.error[0];
        add_message("error",errors);
        var error_msg = errors;
      }else{
         add_message("error","There are no Google Analytics Properties associated with this email.");
      }
      loaderSection(false);
    }
  });
}
function call_list_googl_ads_account(tvc_data){
  //let ads_account_length = jQuery('#ads-account option').length;
  //if(ads_account_length < 2){
    list_googl_ads_account(tvc_data);
  //}
}
// get list google ads dropdown options
function list_googl_ads_account(tvc_data) {
  loaderSection(true);
  var selectedValue = jQuery("#subscriptionGoogleAdsId").val();
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "list_googl_ads_account", tvc_data:tvc_data, conversios_onboarding_nonce:conversios_onboarding_nonce},
    success: function (response) {
      var btn_cam = 'ads_list';
      if (response.error === false) {
        var error_msg = 'null';
        jQuery('#ads-account').empty();
        /*jQuery('#ads-account').append(jQuery('<option>', {
            value: "",
            text: "Select Google Ads Account"
        }));*/
        if (response.data.length == 0) {
          add_message("warning","There are no Google ads accounts associated with email.");
        } else {
          if(response.data.length > 0){
            jQuery('#ads-account').append(jQuery('<option>', { value: '', text: 'Select Google Ads Account',}));
            jQuery.each(response.data, function (key, value) {
              if (selectedValue == value) {
                jQuery('#ads-account').append(jQuery('<option>', { value: value, text: value,selected: "selected"}));
              } else {
                if(selectedValue == "" && key == 0){                
                  jQuery('#ads-account').append(jQuery('<option>', { value: value, text: value,selected: "selected"}));
                }else{
                  jQuery('#ads-account').append(jQuery('<option>', { value: value, text: value,}));
                }
              }
            });
            jQuery('#tvc-gaAds-acc-edit').hide();
          }
        }
      } else {
        add_message("warning","There are no Google ads accounts associated with email.");
        var error_msg = response.errors;
      }
        /*setTimeout(function(){
         user_tracking_data(btn_cam, error_msg,'conversios_onboarding','Get_Google_Ads_account_list'); 
        }, 5000); */
       
      loaderSection(false);
      jQuery('#ads-account').prop('disabled', false);
    }
  });
}

function call_list_google_merchant_account(tvc_data){
  //let mcc_account_length = jQuery('#google_merchant_center_id option').length;
  //if(mcc_account_length < 2){
    list_google_merchant_account(tvc_data);
  //}
}
function list_google_merchant_account(tvc_data){
  //loaderSection(true);
  var selectedValue = jQuery("#subscriptionMerchantCenId").val();
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "list_google_merchant_account", tvc_data:tvc_data, conversios_onboarding_nonce:conversios_onboarding_nonce},
    success: function (response) {
     
      var btn_cam = 'gmc_list';
      if (response.error === false){
         var error_msg = 'null';
        jQuery('#google_merchant_center_id').empty();
        jQuery('#google_merchant_center_id').append(jQuery('<option>', {value: "", text: "Select Google Merchant Center"}));
        if (response.data.length > 0) {        
          jQuery.each(response.data, function (key, value) {
            if(selectedValue == value.account_id){
              jQuery('#google_merchant_center_id').append(jQuery('<option>', {value: value.account_id, "data-merchant_id": value.merchant_id, text: value.account_id,selected: "selected"}));
            }else{
              if(selectedValue == "" && key == 0){ 
                jQuery('#google_merchant_center_id').append(jQuery('<option>', {value: value.account_id, "data-merchant_id": value.merchant_id, text: value.account_id,selected: "selected"}));
              }else{
                jQuery('#google_merchant_center_id').append(jQuery('<option>', {value: value.account_id,"data-merchant_id": value.merchant_id, text: value.account_id, }));
              }
            }
          });
          jQuery('#tvc-gmc-acc-edit').hide();
        }else{
          add_message("error","There are no Google merchant center accounts associated with email.");
        }
           
      }else{
        var error_msg = response.errors;
        add_message("error","There are no Google merchant center accounts associated with email.");
      }   
      /*setTimeout(function(){
           user_tracking_data(btn_cam, error_msg,'conversios_onboarding','Get_Google_Merchant_account_list');   
         }, 10000); */
      setTimeout(function(){ 
        loaderSection(false);
      }, 2000);
      jQuery('#google_merchant_center_id').prop('disabled', false);
    }
  });
}
/* Create function */
function create_google_ads_account(tvc_data){
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  var error_msg = 'null';
  var btn_cam = 'create_new';
  var ename = 'conversios_onboarding';
  var event_label = 'ads';
  //user_tracking_data(btn_cam, error_msg,ename,event_label);   
  jQuery.ajax({
    type: "POST",
    dataType: "json",
    url: tvc_ajax_url,
    data: {action: "create_google_ads_account", tvc_data:tvc_data, conversios_onboarding_nonce:conversios_onboarding_nonce},
    beforeSend: function () {
      loaderSection(true);
    },
    success: function (response) {
      if (response.error === false) {
        error_msg = 'null';
        var btn_cam = 'complate_new';
        var ename = 'conversios_onboarding';
        var event_label = 'ads';

        jQuery('#step_2').prop('disabled', false);
        add_message("success",response.data.message);
        jQuery("#new_google_ads_id").text(response.data.adwords_id);
        if(response.data.invitationLink != ""){
          jQuery("#ads_invitationLink").attr("href",response.data.invitationLink);
        }else{
          jQuery("#invitationLink").html("");
        }
        
        jQuery(".tvc_ads_section").slideUp();
        jQuery("#new_google_ads_section").slideDown();
        //localStorage.setItem("new_google_ads_id", response.data.adwords_id);
        //listGoogleAdsAccount();
      } else {
        var error_msg = response.errors;
        add_message("error",response.data.message);
      }
        //user_tracking_data(btn_cam, error_msg,ename,event_label);   
       
      loaderSection(false);
    }
  });
}

function create_google_merchant_center_account(tvc_data){
  var conversios_onboarding_nonce = jQuery("#conversios_onboarding_nonce").val();
  var is_valide = true;
  var website_url = jQuery("#url").val();
  var email_address = jQuery("#get-mail").val();
  var store_name = jQuery("#store_name").val();
  var country = jQuery("#selectCountry").val();
  var customer_id = jQuery("#loginCustomerId").val();
  var adult_content = jQuery("#adult_content").is(':checked');
  if(website_url == ""){
    add_message("error","Missing value of website url.");
    is_valide = false;
  }else if(email_address == ""){
    add_message("error","Missing value of email address.");
    is_valide = false;
  }else if(store_name == ""){
    add_message("error","Missing value of store name.");
    is_valide = false;
  }else if(country == ""){
    add_message("error","Missing value of country.");
    is_valide = false;
  } else if(jQuery('#terms_conditions').prop('checked') == false){
    add_message("error","Please I accept the terms and conditions.");
    is_valide = false;
  }
  if(is_valide == true){
     var error_msg = 'null';
     var btn_cam = 'create_new';
     var ename = 'conversios_onboarding';
     var event_label = 'gmc';
     //user_tracking_data(btn_cam, error_msg,ename,event_label);  

    var data = {
      action: "create_google_merchant_center_account",
      website_url: website_url,
      email_address: email_address,
      store_name: store_name,
      country: country,
      concent: 1,
      customer_id: customer_id,
      adult_content:adult_content,
      tvc_data:tvc_data,
      conversios_onboarding_nonce:conversios_onboarding_nonce
    };
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: data,
      beforeSend: function () {
        loaderSection(true);
      },
      success: function (response, status) {
        var error_msg = 'null';
        var btn_cam = 'complate_new';
        var ename = 'Confirm Google Merchant account';
        var event_label = 'gmc';
        if (response.error === false || response.merchant_id != undefined) {
          add_message("success","New merchant center created successfully.");              
          jQuery("#new_merchant_id").text(response.account.id);
          jQuery(".tvc_merchant_section").slideUp();
          jQuery("#new_merchant_section").slideDown();
        } else if (response.error === true) {
          const errors = JSON.parse(response.errors[0]);
          add_message("error",errors.message);
          var error_msg = response.errors;
        } else {
          add_message("error","There was error to create merchant center account");
        }
         //user_tracking_data(btn_cam, error_msg,ename,event_label);
         
        jQuery("#createmerchantpopup").removeClass('showpopup');
        jQuery('body').removeClass('scrlnone');
        //jQuery("#merchantconfirmModal").modal('hide');
        loaderSection(false);
      }
    });
    
  }
}

function user_tracking_data(event_name,error_msg,screen_name,event_label=''){
  // var date = new Date();
  // var timestamp = date.getFullYear() + ("0" + (date.getMonth() + 1)).slice(-2) + ("0" + date.getDate()).slice(-2) + ("0" + date.getHours() ).slice(-2) + ("0" + date.getMinutes()).slice(-2) + ("0" + date.getSeconds()).slice(-2);
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: {action: "update_user_tracking_data", event_name:event_name, error_msg:error_msg, screen_name:screen_name,
          event_label:event_label,
          TVCNonce : "<?php echo esc_js(wp_create_nonce('update_user_tracking_data-nonce')); ?>"
      },
      success: function (response) {
           //console.log('user tracking');       
      }
    });
  }
