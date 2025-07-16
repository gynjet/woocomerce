$ = jQuery;

jQuery(function () {
  var tvc_time_out = "";
  var conversion_funnel_chart = "";
  var conversion_bar_chart = "";
  var checkout_funnel_chart = "";
  var checkout_bar_chart = "";
});
var chart_ids = {};
var tvc_helper = {
  add_message: function (type, msg, is_close = true) {
    let tvc_popup_box = document.getElementById("tvc_pmax_popup_box");
    tvc_popup_box.classList.remove("tvc_popup_box_close");
    tvc_popup_box.classList.add("tvc_popup_box");
    if (type == "success") {
      document.getElementById("tvc_pmax_popup_box").innerHTML =
        "<div class='alert tvc-alert-success'>" + msg + "</div>";
    } else if (type == "error") {
      document.getElementById("tvc_pmax_popup_box").innerHTML =
        "<div class='alert tvc-alert-error'>" + msg + "</div>";
    } else if (type == "warning") {
      document.getElementById("tvc_pmax_popup_box").innerHTML =
        "<div class='alert tvc-alert-warning'>" + msg + "</div>";
    }
    if (is_close) {
      tvc_time_out = setTimeout(function () {
        //tvc_popup_box.style.display = "none";
        tvc_popup_box.classList.add("tvc_popup_box_close");
        tvc_popup_box.classList.remove("tvc_popup_box");
      }, 8000);
    }
  },
  tvc_alert: function (
    msg_type = null,
    msg_subject = null,
    msg,
    auto_close = false,
    tvc_time = 7000
  ) {
    document.getElementById("tvc_msg_title").innerHTML = "";
    document.getElementById("tvc_msg_content").innerHTML = "";
    document.getElementById("tvc_msg_icon").innerHTML = "";

    if (msg != "") {
      let tvc_popup_box = document.getElementById("tvc_popup_box");
      tvc_popup_box.classList.remove("tvc_popup_box_close");
      tvc_popup_box.classList.add("tvc_popup_box");

      //tvc_popup_box.style.display = "block";
      document.getElementById("tvc_msg_title").innerHTML =
        this.tvc_subject_title(msg_type, msg_subject);
      document.getElementById("tvc_msg_content").innerHTML = msg;
      if (msg_type == "success") {
        document.getElementById("tvc_msg_icon").innerHTML =
          '<i class="fas fa-check-circle fa-3x tvc-success"></i>';
      } else {
        document.getElementById("tvc_msg_icon").innerHTML =
          '<i class="fas fa-exclamation-circle fa-3x"></i>';
      }
      if (auto_close == true) {
        setTimeout(function () {
          //tvc_popup_box.style.display = "none";
          tvc_popup_box.classList.add("tvc_popup_box_close");
          tvc_popup_box.classList.remove("tvc_popup_box");
        }, tvc_time);
      }
    }
  },
  tvc_subject_title: function (msg_type = null, msg_subject = null) {
    if (msg_subject == null || msg_subject == "") {
      if (msg_type == "success") {
        return '<span class="tvc-success">Success!!</span>';
      } else {
        return '<span class="tvc-error">Oops!</span>';
      }
    } else {
      if (msg_type == "success") {
        return '<span class="tvc-success">' + msg_subject + "</span>";
      } else {
        return "<span>" + msg_subject + "</span>";
      }
    }
  },
  tvc_close_msg: function () {
    let tvc_popup_box = document.getElementById("tvc_popup_box");
    tvc_popup_box.classList.add("tvc_popup_box_close");
    tvc_popup_box.classList.remove("tvc_popup_box");
    //tvc_popup_box.style.display = "none";
  },
  loaderSection: function (isShow) {
    if (isShow) {
      jQuery("#feed-spinner").show();
    } else {
      jQuery("#feed-spinner").hide();
    }
  },
  get_call_ajax_request: function (post_data) {
    this.cleare_data();
    this.add_loader();
    this.google_ads_pmax_call_api(post_data);
  },
  google_ads_pmax_call_api: function (post_data) {
    // Shopping and Google Ads Performance
    //post_data['action']='get_google_ads_reports_chart';
    var v_this = this;
    /*$.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: post_data,
      success: function (response) {
      	console.log(response);
      	if(response.error == false){
      		if(Object.keys(response.data).length > 0){
      			v_this.set_google_ads_reports_chart_value(response.data, post_data);
      		}
      	}else{
      		if(response.status == "423" || response.status == "400"){
      			v_this.tvc_alert("error","", "If Google Ads Performance Data is not generated please make sure your Google Ads account should link with our MCC account");
      		}else{
        		v_this.tvc_alert("error", "", response?.errors);
        	}
      	}
        v_this.remove_loader_for_analytics_reports();
      }
    });*/
    //Compaign Performance List
    post_data["action"] = "get_pmax_campaign_list";
    var v_this = this;
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: post_data,
      success: function (response) {
        if (response.error == false) {
          if (
            response.data.hasOwnProperty("results") &&
            Object.keys(response.data.results).length > 0
          ) {
            v_this.set_google_ads_reports_campaign_performance_value(
              response.data.results,
              post_data
            );
          } else {
            v_this.set_google_ads_reports_campaign_performance_value(
              "",
              post_data
            );
          }
        } else {
          if (response.errors != "") {
            //v_this.tvc_alert("error","",response.errors);
          }
        }
        var page_token = post_data["page_token"];
        var page = post_data["page"];
        jQuery("#page_no").html(page);
        //console.log("page"+page+ response.data.nextPageToken);
        var pre_token = jQuery(".pgnextbtn").attr("data-token");
        //console.log("pre_token"+pre_token);
        if (page > 2) {
          jQuery(".pgprevbtn").attr("data-token", pre_token);
          jQuery(".pgprevbtn").prop("disabled", false);
        } else if (page == 2) {
          jQuery(".pgprevbtn").attr("data-token", "");
          jQuery(".pgprevbtn").prop("disabled", false);
        } else if (page == 1) {
          jQuery(".pgprevbtn").attr("data-token", "");
          jQuery(".pgprevbtn").prop("disabled", true);
          jQuery(".pgnextbtn").prop("disabled", false);
        }
        if (
          response.error == false &&
          response.data?.nextPageToken != "" &&
          response.data?.nextPageToken != undefined
        ) {
          jQuery(".pgnextbtn").attr("data-token", response.data.nextPageToken);
        } else {
          jQuery(".pgnextbtn").attr("data-token", "");
          jQuery(".pgnextbtn").prop("disabled", true);
        }
        v_this.remove_loader_for_analytics_reports();
      },
    });
  },
  set_google_ads_reports_campaign_performance_value: function (
    data,
    post_data
  ) {
    //if(data.hasOwnProperty('data')){
    //var p_p_r = data.product_performance_report.products;
    //console.log(p_p_r);
    var table_row = "";
    var table_row_last = "";
    var product_revenue_per = 0;
    var status = "";
    var daily_budget = "";
    var cost_micros = "";
    var v_this = this;
    var currency_symbol = post_data["currency_symbol"];
    if (data != undefined && Object.keys(data).length > 0) {
      var i = 0;
      jQuery.each(data, function (propKey, propValue) {
        //console.log(propValue);
        //if(i<5){
        //table_row = ''; table_row_last = '';
        status = propValue.campaign.status == "ENABLED" ? "Enabled" : "Paused";
        daily_budget = parseInt(
          propValue.campaignBudget.amountMicros / 1000000
        ).toFixed(2);
        if (parseInt(daily_budget) < 0 || daily_budget == "undefined") {
          daily_budget = "";
        }
        cost_micros = (propValue.metrics.costMicros / 1000000).toFixed(2);
        if (status == "Enabled") {
          table_row +=
            '<tr><td class="prdnm-cell">' + propValue.campaign.name + "</td>";
          table_row += "<td>" + v_this.numberWithCommas(daily_budget) + "</td>";
          table_row += "<td>" + status + "</td>";
          table_row += "<td>" + propValue.metrics.clicks + "</td>";
          table_row += "<td>" + v_this.numberWithCommas(cost_micros) + "</td>";
          table_row +=
            "<td>" +
            v_this.numberWithCommas(propValue.metrics.conversions.toFixed(2)) +
            "</td>";
          table_row +=
            "<td>" +
            v_this.numberWithCommas(
              propValue.metrics.conversionsValue.toFixed(2)
            ) +
            '</td><td><a href="admin.php?page=conversios-pmax&tab=pmax_edit&id=' +
            propValue.campaign.id +
            '">Edit Campaign</a></td></tr>';
        } else {
          table_row_last +=
            '<tr><td class="prdnm-cell">' + propValue.campaign.name + "</td>";
          table_row_last += "<td>" + daily_budget + "</td>";
          table_row_last += "<td>" + status + "</td>";
          table_row_last += "<td>" + propValue.metrics.clicks + "</td>";
          table_row_last +=
            "<td>" + v_this.numberWithCommas(cost_micros) + "</td>";
          table_row_last +=
            "<td>" +
            v_this.numberWithCommas(propValue.metrics.conversions.toFixed(2)) +
            "</td>";
          table_row_last +=
            "<td>" +
            v_this.numberWithCommas(
              propValue.metrics.conversionsValue.toFixed(2)
            ) +
            '</td><td><a href="admin.php?page=conversios-pmax&tab=pmax_edit&id=' +
            propValue.campaign.id +
            '">Edit Campaign</a></td></tr>';
        }
        i = i + 1;
        //}
      });
      jQuery("#campaign_pmax_list table tbody").append(table_row);
      jQuery("#campaign_pmax_list table tbody").append(table_row_last);
    } else {
      jQuery("#campaign_pmax_list table tbody").append(
        "<tr><td colspan='7'>Data not available</td></tr>"
      );
    }
    //}
  },
  set_google_ads_reports_chart_value: function (data, post_data) {
    var v_this = this;
    var s_1_div_id = {
      daily_clicks: {
        id: "dailyClicks",
        type: "number",
        is_chart: true,
        chart_type: "line",
        chart_value_field_id: "clicks",
        chart_title: "Clicks",
        chart_id: "dailyClicks",
      },
      daily_cost: {
        id: "dailyCost",
        type: "currency",
        is_chart: true,
        chart_type: "line",
        chart_value_field_id: "costs",
        chart_title: "Cost",
        chart_id: "dailyCost",
      },
      daily_conversions: {
        id: "dailyConversions",
        type: "number",
        is_chart: true,
        chart_type: "line",
        chart_value_field_id: "conversions",
        chart_title: "Conversions",
        chart_id: "dailyConversions",
      },
      daily_sales: {
        id: "dailySales",
        type: "number",
        is_chart: true,
        chart_type: "line",
        chart_value_field_id: "sales",
        chart_title: "Sales",
        chart_id: "dailySales",
      },
    };
    if (Object.keys(s_1_div_id).length > 0) {
      var labels_key = "";
      if (data.hasOwnProperty("graph_type")) {
        labels_key = data["graph_type"];
      }
      jQuery.each(s_1_div_id, function (propKey, propValue) {
        if (data.hasOwnProperty(propValue["id"])) {
          if (
            propValue["chart_id"] != undefined &&
            propValue["is_chart"] != undefined &&
            propValue["chart_type"] != undefined
          ) {
            var chart_id = propValue["chart_id"];
            var field_id = propValue["chart_value_field_id"];
            var chart_title = propValue["chart_title"];
            //console.log(propValue['chart_type']+"call"+chart_id);
            if (propValue["chart_type"] == "line") {
              v_this.drow_google_ads_chart(
                chart_id,
                data[propValue["id"]],
                field_id,
                chart_title,
                labels_key
              );
            }
          }
        }
      });
    }
  },
  drow_google_ads_chart: function (
    chart_id,
    alldata,
    field_key,
    d_label,
    labels_key
  ) {
    var chart_data = alldata;
    var ctx = document.getElementById(chart_id).getContext("2d");
    var gradientFill = ctx.createLinearGradient(0, 0, 0, 500);
    if (chart_id == "dailyClicks") {
      gradientFill.addColorStop(0.4, "rgba(153, 170, 255, 0.9)");
      gradientFill.addColorStop(0.85, "rgba(255, 255, 255, 0.7)");
    } else if (chart_id == "dailyCost") {
      gradientFill.addColorStop(0.4, "rgba(110, 245, 197, 0.9)");
      gradientFill.addColorStop(0.85, "rgba(255, 255, 255, 0.7)");
    } else if (chart_id == "dailyConversions") {
      gradientFill.addColorStop(0.4, "rgba(255, 229, 139, 0.9)");
      gradientFill.addColorStop(0.85, "rgba(255, 255, 255, 0.7)");
    } else if (chart_id == "dailySales") {
      gradientFill.addColorStop(0.4, "rgba(107, 232, 255, 0.9)");
      gradientFill.addColorStop(0.85, "rgba(255, 255, 255, 0.75)");
    }
    const labels = [];
    const chart_val = [];
    var t_labels = "";

    //var d_backgroundColors = ['#FF6384','#22CFCF','#0ea50b','#FF9F40','#FFCD56']
    jQuery.each(chart_data, function (key, value) {
      if (labels_key != "" && value.hasOwnProperty(labels_key)) {
        t_labels = value[labels_key];
      } else {
        t_labels = value["date"];
      }
      labels.push(t_labels.toString());
      //chart_val.push(value[field_key]);
      chart_val.push(value[field_key] != null ? value[field_key] : 0);
    });
    //console.log(alldata);
    //console.log(field_key);
    //console.log(chart_val);
    const data = {
      labels: labels,
      datasets: [
        {
          data: chart_val,
          borderColor: "#002BFC",
          pointBorderColor: "#002BFC",
          pointBackgroundColor: "#fff",
          pointBorderWidth: 1,
          pointRadius: 2,
          fill: true,
          backgroundColor: gradientFill,
          borderWidth: 1,
        },
      ],
    };
    const config = {
      type: "line",
      data: data,
      options: {
        animation: {
          easing: "easeInOutBack",
        },
        plugins: {
          legend: false,
        },
        responsive: true,
        scales: {
          y: {
            fontColor: "#ffffff",
            fontStyle: "normal",
            beginAtZero: true,
            maxTicksLimit: 5,
            padding: 30,
            grid: {
              borderWidth: 0,
            },
            ticks: {
              stepSize: 1000,
              callback: function (value) {
                var ranges = [
                  { divider: 1e6, suffix: "M" },
                  { divider: 1e3, suffix: "k" },
                ];
                function formatNumber(n) {
                  for (var i = 0; i < ranges.length; i++) {
                    if (n >= ranges[i].divider) {
                      return (
                        (n / ranges[i].divider).toString() + ranges[i].suffix
                      );
                    }
                  }
                  return n;
                }
                return "" + formatNumber(value);
              },
            },
          },
          x: {
            padding: 10,
            fontColor: "#ffffff",
            fontStyle: "normal",
            grid: {
              display: false,
            },
          },
        },
      },
    };
    chart_ids[chart_id] = new Chart(ctx, config);
  },
  google_analytics_reports_call_api: function (post_data) {
    var v_this = this;
    var g_mail = post_data.g_mail;
    jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: post_data,
      success: function (response) {
        console.log(response);
        if (response.error == false) {
          if (Object.keys(response.data).length > 0) {
            v_this.set_google_analytics_reports_value(response.data, post_data);
          }
        } else if (response.error == true && response.errors != undefined) {
          const errors = response.errors;
          if (response.errors == "access_token_error") {
            if (g_mail != "") {
              v_this.tvc_alert(
                "error",
                "",
                "It seems the token to access your Google Analytics account is expired. Sign in with " +
                  g_mail +
                  " again to reactivate the token. <span class='google_connect_url'>Click here..</span>"
              );
            } else {
              v_this.tvc_alert(
                "error",
                "",
                "It seems the token to access your Google Analytics account is expired. Sign in with the connected email again to reactivate the token. <span class='google_connect_url'>Click here..</span>"
              );
            }
          } else {
            v_this.tvc_alert("error", "Error", errors);
          }
        } else {
          v_this.tvc_alert(
            "error",
            "Error",
            "Analytics report data not fetched"
          );
        }
        v_this.remove_loader_for_analytics_reports();
      },
    });
  },
  display_field_val: function (
    div_id,
    field,
    field_val,
    field_type,
    currency_code,
    plugin_url
  ) {
    if (field_type == "currency") {
      if (Math.floor(field_val) != field_val) {
        field_val = parseFloat(field_val).toFixed(2);
      }
      var currency = this.get_currency_symbols(currency_code);
      jQuery(div_id).html(currency + "" + field_val);
    } else if (field_type == "rate") {
      field_val = parseFloat(field_val).toFixed(2);
      var img = "";
      if (plugin_url != "" && plugin_url != undefined) {
        img = '<img src="' + plugin_url + '/admin/images/red-down.png">';
        if (field_val > 0) {
          img = '<img src="' + plugin_url + '/admin/images/green-up.png">';
        }
      }
      jQuery(div_id).html(img + field_val + "%");
    } else {
      if (Math.floor(field_val) != field_val) {
        field_val = parseFloat(field_val).toFixed(2);
      }

      jQuery(div_id).html(field_val);
    }
  },
  remove_loader_for_analytics_reports: function () {
    var reg_section = this.get_sections_list();
    if (Object.keys(reg_section).length > 0) {
      jQuery.each(reg_section, function (propKey, propValue) {
        if (
          propValue.hasOwnProperty("main-class") &&
          propValue.hasOwnProperty("loading-type")
        ) {
          if (propValue["loading-type"] == "bgcolor") {
            //jQuery("."+propValue['main-class']).addClass("is_loading");
            if (Object.keys(propValue["ajax_fields"]).length > 0) {
              jQuery.each(
                propValue["ajax_fields"],
                function (propKey, propValue) {
                  jQuery("." + propValue["class"]).removeClass(
                    "loading-bg-effect"
                  );
                }
              );
            }
          } else if (propValue["loading-type"] == "gif") {
            jQuery("." + propValue["main-class"]).removeClass("is_loading");
          }
        }
      });
    }
  },
  cleare_data: function () {
    var v_this = this;
    jQuery("#campaign_pmax_list table tbody").html("");
    var canvas = document.getElementById("ecomfunchart");
    if (canvas != null) {
      var is_blank = this.is_canvas_blank(canvas);
      if (!is_blank) {
        conversion_bar_chart.destroy();
        //const canvas = document.getElementById('ecomfunchart');
        //canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
      }
    }
    canvas = document.getElementById("ecomcheckoutfunchart");
    if (canvas != null) {
      var is_blank = this.is_canvas_blank(canvas);
      if (!is_blank) {
        checkout_bar_chart.destroy();
      }
    }

    if (Object.keys(chart_ids).length > 0) {
      jQuery.each(chart_ids, function (propKey, propValue) {
        var canvas = document.getElementById(propKey);
        if (canvas != null) {
          var is_blank = v_this.is_canvas_blank(canvas);
          //console.log(propValue+"-"+canvas+"-"+is_blank);
          if (!is_blank) {
            chart_ids[propKey].destroy();
          }
        }
      });
    }
  },
  add_loader: function () {
    var reg_section = this.get_sections_list();
    if (Object.keys(reg_section).length > 0) {
      jQuery.each(reg_section, function (propKey, propValue) {
        if (
          propValue.hasOwnProperty("main-class") &&
          propValue.hasOwnProperty("loading-type")
        ) {
          if (propValue["loading-type"] == "bgcolor") {
            //jQuery("."+propValue['main-class']).addClass("is_loading");
            if (Object.keys(propValue["ajax_fields"]).length > 0) {
              jQuery.each(
                propValue["ajax_fields"],
                function (propKey, propValue) {
                  jQuery("." + propValue["class"]).addClass(
                    "loading-bg-effect"
                  );
                }
              );
            }
          } else if (propValue["loading-type"] == "gif") {
            jQuery("." + propValue["main-class"]).addClass("is_loading");
          }
        }
      });
    }
  },
  get_sections_list: function () {
    return {
      dashboard_summary: {
        "loading-type": "bgcolor",
        "main-class": "dashsmry-item",
        "sub-clsass": "dashsmrybx",
        ajax_fields: {
          field_1: {
            class: "dash-smry-title",
          },
          field_2: {
            class: "dash-smry-value",
          },
          field_3: {
            class: "dash-smry-compare-val",
          },
          field_4: {
            class: "dshsmryprdtxt",
          },
        },
      },
      campaign_pmax_list: {
        "loading-type": "gif",
        "main-class": "campaign_pmax_list",
      },
    };
  },
  numberWithCommas: function (x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  },
  get_currency_symbols: function (code) {
    var currency_symbols = {
      USD: "$", // US Dollar
      EUR: "€", // Euro
      CRC: "₡", // Costa Rican Colón
      GBP: "£", // British Pound Sterling
      ILS: "₪", // Israeli New Sheqel
      INR: "₹", // Indian Rupee
      JPY: "¥", // Japanese Yen
      KRW: "₩", // South Korean Won
      NGN: "₦", // Nigerian Naira
      PHP: "₱", // Philippine Peso
      PLN: "zł", // Polish Zloty
      PYG: "₲", // Paraguayan Guarani
      THB: "฿", // Thai Baht
      UAH: "₴", // Ukrainian Hryvnia
      VND: "₫", // Vietnamese Dong
    };
    if (currency_symbols[code] !== undefined) {
      return currency_symbols[code];
    } else {
      return code;
    }
  },
  is_canvas_blank: function (canvas) {
    const context = canvas.getContext("2d");
    const pixelBuffer = new Uint32Array(
      context.getImageData(0, 0, canvas.width, canvas.height).data.buffer
    );
    return !pixelBuffer.some((color) => color !== 0);
  },
};
