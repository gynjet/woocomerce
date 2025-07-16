(function ($) {
  'use strict';
  /**
   * This enables you to define handlers, for when the DOM is ready:
   * $(function() { });
   * When the window is loaded:
   * $( window ).load(function() { }); 
   */
})(jQuery);

class TVC_GTM_WP_Enhanced {

  /**
   * Contact form 7 - formSubmit, Tracks form submissions for Contact Form 7 forms (excluding admin submissions).
   * Pushes data to the dataLayer for analytics purposes.
   * 
   * @param {Event} e The form submit event object.
   */
  formsubmit_cf7_tracking(e) {
    if (this.options.is_admin == true) {
      return;
    }

    var form_submit_datalayer = {
      event: "form_lead_submit",
      cov_form_name: e.target.ariaLabel,
      cov_form_type: "CF7 Plugin",
      cov_form_id: e.detail.contactFormId,
    };

    dataLayer.push(form_submit_datalayer);
  }

  /**
   * Ninja form - formSubmit, Handles form submission for Ninja Forms with dataLayer push.
   *
   * This function is triggered when a Ninja Form is submitted. It checks if the user is in the admin area, and if not, it pushes form submission data to the dataLayer for tracking purposes.
   *
   * @param {Event} e The DOM event object for the form submission.
   * @param {Object} r The response object from the Ninja Forms submission.
   * @param {number} id The ID of the submitted Ninja Form.
   * @returns {void}
   */
  formsubmit_ninja_tracking(e, r, id) {
    if (this.options.is_admin == true) {
      return;
    }
    var title = e.target.ariaLabel ? e.target.ariaLabel : r.response.data.settings.title;
    var form_submit_datalayer = {
      event: "form_lead_submit",
      cov_form_name: title,
      cov_form_type: "Ninja Form Plugin",
      cov_form_id: r.id,
    };

    dataLayer.push(form_submit_datalayer);
  }

  /**
   * WpForm & Formidable - FormSubmit, Submits a form using AJAX and pushes data to the dataLayer.
   *
   * @param {any[]} fdata The data to be pushed to the dataLayer.
   * @param {string} is_plugin (optional) A flag indicating if this call originates from a plugin. Defaults to an empty string.
   */
  formsubmit_ajax_tracking(fdata = [], is_plugin = '') {
    var postdata = {
      action: "datalayer_push",
      nonce: ConvAioGlobal.nonce,
      is_plugin: is_plugin,
    };

    if (typeof fdata === 'string') {
      const parts = fdata.split('&');
      const params = {};
      for (let i = 0; i < parts.length; i++) {
        const keyVal = parts[i].split('=');
        params[decodeURIComponent(keyVal[0])] = decodeURIComponent(keyVal[1] || '');
      }

      // Formidable plugin related
      if ('action' in params && params.action == "frm_entries_create") {
        postdata['form_id'] = params.form_id;
        postdata['form_action'] = params.action;
      }
    } else {
      // WpForm plugin related
      if ('action' in fdata && fdata['action'] == 'wpforms_submit') {
        postdata['form_id'] = fdata['form_id'];
        postdata['form_action'] = fdata['action'];
      }
    }

    jQuery.ajax({
      type: "POST",
      url: this.options.tvc_ajax_url,
      data: postdata,
      success: function (datalayer) {
        window.dataLayer = window.dataLayer || [];
        dataLayer.push(datalayer);
      }
    });
  }

} // End Class::TVC_GTM_WP_Enhanced

class TVC_GTM_Enhanced extends TVC_GTM_WP_Enhanced {

  constructor(options = {}) {
    super();
    this.options = {
      tracking_option: 'UA'
    };
    if (options) {
      Object.assign(this.options, options);
    }
  }
  singleProductaddToCartEventBindings(variations_data, product_detail_addtocart_selector) {
    var single_btn = "";
    if (product_detail_addtocart_selector != "") {
      single_btn = document.querySelectorAll("button[class*='btn-buy-shop'],button[class*='single_add_to_cart_button'], button[class*='add_to_cart']" + product_detail_addtocart_selector);
    } else {
      single_btn = document.querySelectorAll("button[class*='btn-buy-shop'],button[class*='single_add_to_cart_button'], button[class*='add_to_cart']");
    }
    if (single_btn.length > 0) {
      single_btn.forEach((aCartBut) => {
        aCartBut.addEventListener("click", (event) => {
          this.add_to_cart_click(variations_data, "Product Pages", event);
        });
      });
    }
  }
  ListProductaddToCartEventBindings() {
    var elements = "";
    elements = document.querySelectorAll("a[href*=add-to-cart]");
    if (elements.length > 0) {
      for (var i = 0; i < elements.length; i++) {
        if (elements[i]) {
          elements[i].addEventListener("click", (event) => this.list_add_to_cart_click(event));
        }
      }
    }
  }
  ListProductSelectItemEventBindings() {
    var elements = "";
    elements = document.querySelectorAll("li.product a:not([href*=add-to-cart],.product_type_variable, .product_type_grouped");
    if (elements.length > 0) {
      for (var i = 0; i < elements.length; i++) {
        if (elements[i]) {
          elements[i].addEventListener("click", (event) => this.list_select_item_click(event));
        }
      }
    }
  }
  RemoveItemCartEventBindings() {
    var elements = "";
    elements = document.querySelectorAll("a[href*=\"?remove_item\"]");
    if (elements.length > 0) {
      for (var i = 0; i < elements.length; i++) {
        if (elements[i]) {
          elements[i].addEventListener("click", () => this.remove_item_click());
        }
      }
    }
  }
  /*
   * check remarketing option 
   */
  is_add_remarketing_tags() {
    if (this.options.is_admin == false && this.options.ads_tracking_id != "" && (this.options.remarketing_tags == 1 || this.options.dynamic_remarketing_tags == 1)) {
      return true;
    } else {
      return false;
    }
  }

  get_variation_data_by_id(variations_data, variation_id) {
    var r_val = "";
    if (variations_data.available_variations.length > 0) {
      variations_data.available_variations.forEach((element, index) => {
        if (element.variation_id == variation_id) {
          r_val = element;
        }
      });
      return r_val;
    }
  }
  get_variation_attribute_name(p_attributes) {
    var p_v_title = "";
    if (Object.keys(p_attributes).length > 0) {
      for (var index in p_attributes) {
        p_v_title += (p_v_title == "") ? p_attributes[index] : ' | ' + p_attributes[index];
      }
      return p_v_title;
    }
  }
  get_event_data_layer(event_name) {
    if (event_name != "") {
      if (Object.keys(dataLayer).length > 0) {
        for (var dataLayer_item in dataLayer) {
          event = dataLayer[dataLayer_item].event;
          if (event_name == event) {
            return dataLayer[dataLayer_item];
          }
        }
      }
    }
  }
  get_product_from_product_list(product_id) {
    if (product_id != "") {
      if (Object.keys(conProductList).length > 0) {
        for (var dataLayer_item in conProductList[0]) {
          if (conProductList[0][dataLayer_item].hasOwnProperty('id')) {
            var id = conProductList[0][dataLayer_item].id;
            if (product_id == id) {
              return conProductList[0][dataLayer_item];
            }
          }
        }
      }
    }
  }
  get_product_from_product_list_by_url(prod_obj, key, product_url) {
    if (product_url != "") {
      if (Object.keys(prod_obj).length > 0) {
        for (var dataLayer_item in prod_obj[0]) {
          if (prod_obj[0][dataLayer_item].hasOwnProperty(key)) {
            var map_val = prod_obj[0][dataLayer_item][key];
            if (product_url == map_val) {
              return prod_obj[0][dataLayer_item];
            }
          }
        }
      }
    }
  }
  list_select_item_click(event) {
    var this_var = event.currentTarget;
    var href = this_var.getAttribute('href');
    var item = this.get_product_from_product_list_by_url(conProductList, 'productlink', href);
    var add_to_cart_datalayer = {
      event: "select_item",
      ecommerce: {
        items: [
          {
            affiliation: this.options.affiliation,
            item_id: item.id,
            item_name: item.name,
            currency: this.options.currency,
            item_category: item.category,
            price: item.price,
            quantity: 1
          }]
      }
    };
    dataLayer.push(add_to_cart_datalayer);
  }
  remove_item_click(this_var) {
    var href = this_var.getAttribute('href');
    if (href) {
      var item = this.get_product_from_product_list_by_url(conCarttList, 'remove_cart_link', href);

      var ecomval = (item.price * item.quantity).toFixed(2);

      var add_to_cart_datalayer = {
        event: "remove_from_cart",
        ecommerce: {
          currency: this.options.currency,
          value: parseFloat(ecomval),
          items: [
            {
              affiliation: this.options.affiliation,
              item_id: item.id,
              item_name: item.name,
              currency: this.options.currency,
              item_category: item.category,
              price: item.price,
              quantity: item.quantity

            }]
        }
      };
      dataLayer.push(add_to_cart_datalayer);
    }

  }
  list_add_to_cart_click(event) {
    var this_var = event.currentTarget;
    var href = this_var.getAttribute('href');
    var product_id = this.getParameterByName("add-to-cart", href);
    var item = this.get_product_from_product_list(product_id);
    var quantity = 1;
    if (jQuery(this_var).attr("data-quantity")) {
      quantity = jQuery(this_var).attr("data-quantity");
    }
    var add_to_cart_datalayer = {
      event: "add_to_cart",
      ecommerce: {
        currency: this.options.currency,
        value: item.price,
        items: [
          {
            affiliation: this.options.affiliation,
            item_id: item.id,
            item_name: item.name,
            currency: this.options.currency,
            item_category: item.category,
            price: item.price,
            quantity: parseInt(quantity)
          }]
      }
    };

    if (this.options.fb_pixel_id != undefined && this.options.fb_pixel_id != null && this.options.fb_pixel_id != "") {
      add_to_cart_datalayer.fb_event_id = this.options.fb_event_id + 'p' + item.id;
    }

    dataLayer.push(add_to_cart_datalayer);
  }
  getParameterByName(name, url = window.location.href) {
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
      results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
  }
  /*
   * below code run while add to cart on product page. 
   * ( Event=>  add_to_cart)
   */
  add_to_cart_click(variations_data, page_type = "Product Pages", event) {
    var this_var = event.currentTarget;
    var item_dataLayer = this.get_event_data_layer("view_item");
    if (this.options.is_admin == true) {
      return;
    }
    if (Object.keys(item_dataLayer.ecommerce.items[0]).length > 0) {
      var item = item_dataLayer.ecommerce.items[0];
      var variation_attribute_name = "";
      var vari_data = "";
      var variation_id = "";
      var variation_id_obj = document.getElementsByClassName("variation_id");
      if (variation_id_obj.length > 0) {
        variation_id = document.getElementsByClassName("variation_id")[0].value;
      }
      var varPrice = item.price;
      if (variation_id != "" && variation_id != "0") {
        vari_data = this.get_variation_data_by_id(variations_data, variation_id);
        var p_attributes = vari_data.attributes;
        if (Object.keys(p_attributes).length > 0) {
          variation_attribute_name = this.get_variation_attribute_name(p_attributes);
        }
        if (vari_data.display_price) {
          varPrice = vari_data.display_price;
        } else if (vari_data.display_regular_price) {
          varPrice = vari_data.display_regular_price;
        }
      }

      var ecomval = (item.price * jQuery(this_var).parent().find("input[name=quantity]").val()).toFixed(2)

      var add_to_cart_datalayer = {
        event: "add_to_cart",
        ecommerce: {
          currency: this.options.currency,
          value: parseFloat(ecomval),
          items: [
            {
              affiliation: item.affiliation,
              item_id: item.item_id,
              item_name: item.item_name,
              currency: item.currency,
              item_category: item.item_category,
              price: varPrice,
              quantity: parseInt(jQuery(this_var).parent().find("input[name=quantity]").val()),
              item_variant: variation_attribute_name

            }]
        }
      };
      if (this.options.fb_pixel_id != undefined && this.options.fb_pixel_id != null && this.options.fb_pixel_id != "") {
        add_to_cart_datalayer.fb_event_id = this.options.fb_event_id + 'p' + item.item_id;
      }
      dataLayer.push(add_to_cart_datalayer);

    }
  }
  /*
  *
  */
  checkout_step_2_tracking() {
    var item_dataLayer = this.get_event_data_layer("begin_checkout");
    var item_dataLayer_asi = this.get_event_data_layer("add_shipping_info");
    if (item_dataLayer_asi == undefined) {
      if (this.options.is_admin == true) {
        return;
      }
      if (Object.keys(item_dataLayer.ecommerce.items[0]).length > 0) {
        var checkout_step_2_datalayer = {
          event: "add_shipping_info",
          ecommerce: {
            currency: this.options.currency,
            value: item_dataLayer.ecommerce.value,
            items: item_dataLayer.ecommerce.items
          }
        };
        dataLayer.push(checkout_step_2_datalayer);
      }
    }
  }
  checkout_step_3_tracking() {
    var item_dataLayer = this.get_event_data_layer("begin_checkout");
    if (this.options.is_admin == true) {
      return;
    }
    if (Object.keys(item_dataLayer.ecommerce.items[0]).length > 0) {
      var checkout_step_3_datalayer = {
        event: "add_payment_info",
        ecommerce: {
          currency: this.options.currency,
          value: item_dataLayer.ecommerce.value,
          items: item_dataLayer.ecommerce.items
        }
      };
      dataLayer.push(checkout_step_3_datalayer);
    }
  }

  getCurrentTime() {
    if (!Date.now) {
      return new Date().getTime();
    } else {
      //Math.floor(Date.now() / 1000)
      return Date.now();
    }
  }
  getClientId() {
    let client_id = this.getCookie("_ga");
    if (client_id != null && client_id != "") {
      return client_id;
    } else {
      return;
    }
  }
  setCookie(name, value, days) {
    var expires = "";
    if (days) {
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }
  getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }
  eraseCookie(name) {
    document.cookie = name + "=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  }
}