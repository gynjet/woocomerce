/* jshint asi: true */
//////////////////
//CONFIG
//////////////////
var basic_params = [
	'value',
	'currency',
	'predicted_ltv',
	'content_name',
	'content_type',
	'content_ids',
	'content_category',
	'search_string',
	'num_items',
	'status',
	'custom'
]

var supported_params = {
	
	'AddPaymentInfo': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'AddToCart': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'AddToWishlist': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'CompleteRegistration': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 1,
		'custom': 1
	},
	
	'Contact': {
		'value': 0,
		'currency': 0,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
		
	'CustomizeProduct': {
		'value': 0,
		'currency': 0,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'Donate': {
		'value': 0,
		'currency': 0,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
		
	'FindLocation': {
		'value': 0,
		'currency': 0,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'InitiateCheckout': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 0,
		'num_items': 1,
		'status': 0,
		'custom': 1
	},
	
	'Lead': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'Purchase': {
		'value': 2,
		'currency': 2,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 0,
		'num_items': 1,
		'status': 0,
		'custom': 1
	},
	
	'Schedule': {
		'value': 0,
		'currency': 0,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'Search': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 1,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'StartTrial': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 1,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'SubmitApplication': {
		'value': 0,
		'currency': 0,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'Subscribe': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 1,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'ViewContent': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'ViewContentTiktok': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'AddToCartTiktok': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'AddToWishlistTiktok': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'InitiateCheckoutTiktok': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 0,
		'num_items': 1,
		'status': 0,
		'custom': 1
	},

	'AddPaymentInfoTiktok': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'PurchaseTiktok': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 0,
		'search_string': 0,
		'num_items': 1,
		'status': 0,
		'custom': 1
	},
		
	'CompleteRegistrationTiktok': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 1,
		'custom': 1
	},
	
	'ViewContentPinterest': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'AddToCartPinterest': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'PurchasePinterest': {
		'value': 2,
		'currency': 2,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 1,
		'content_ids': 1,
		'content_category': 0,
		'search_string': 0,
		'num_items': 1,
		'status': 0,
		'custom': 1
	},

	'LeadPinterest': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 1,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'CompleteRegistrationPinterest': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 1,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 1,
		'custom': 1
	},
	
	'ViewContentSnapchat': {
		'value': 0,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'PurchaseSnapchat': {
		'value': 0,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	
	'AddToCartSnapchat': {
		'value': 0,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
		
	'AddToWishlistSnapchat': {
		'value': 0,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'InitiateCheckoutSnapchat': {
		'value': 0,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'AddPaymentInfoSnapchat': {
		'value': 0,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'AddPaymentInfoGA': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'AddToCartGA': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'AddToWishlistGA': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
		
	'InitiateCheckoutGA': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},
	
	'ViewContentGA': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},

	'PurchaseGA': {
		'value': 1,
		'currency': 1,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	},


	'custom': {
		'value': 0,
		'currency': 0,
		'predicted_ltv': 0,
		'content_name': 0,
		'content_type': 0,
		'content_ids': 0,
		'content_category': 0,
		'search_string': 0,
		'num_items': 0,
		'status': 0,
		'custom': 1
	}

}

var time_delay_events = ['ViewContent', 'custom']

jQuery(document).ready(function($){

	//////////////////
	//INIT
	//////////////////
	if ( fcaPcAdminData.debug ) {
		console.log ( fcaPcAdminData )
	}

	if ( !fcaPcAdminData.premium ) {
		$(  '.fca-pc-user_parameters, ' +
			'.fca-pc-utm_support, ' +
			'#fca-pc-cpt_support, ' +
			'#fca-pc-modal-delay-input, ' +
			'#fca-pc-modal-scroll-input, ' +
			'#fca-pc-add-custom-param, ' +
			'.fca-pc-woo_extra_params, ' +
			'.fca-pc-woo_order_cookie, ' +
			'.fca-pc-woo_delay, ' +
			'.fca-pc-edd_extra_params, ' +
			'.fca-pc-edd_delay, ' +
			'.fca-pc-advanced_matching, ' +
			'.fca-pc-amp_integration,' +
			'.fca-pc-video_events,' +
			'.fca-pc-edd_integration_pinterest,' +
			'.fca-pc-edd_integration_tiktok,' +
			'.fca-pc-edd_integration_snapchat,' + 
			'.fca-pc-woo_integration_pinterest,' +
			'.fca-pc-woo_integration_tiktok,' +
			'.fca-pc-woo_integration_snapchat,' +
			'#fca-pc-pixel-excluded-pages'
		).prop('checked', false).prop('disabled', true).closest('tr').addClass('fca-pc-integration-disabled')
		
	} else {

		//PREMIUM BUILDS
		if ( fcaPcAdminData.woo_active == false ) {
			$( 	'.fca-pc-woo_extra_params, ' +
				'.fca-pc-woo_delay' ).prop('checked', false).prop('disabled', true).closest('tr').addClass('fca-pc-integration-disabled')
		}
		if ( fcaPcAdminData.edd_active == false ) {
			$(	'.fca-pc-edd_extra_params, ' +
				'.fca-pc-edd_delay' ).prop('checked', false).prop('disabled', true).closest('tr').addClass('fca-pc-integration-disabled')
		}
	}
		
	//wp.codeEditor.initialize( $( '#fca-pc-modal-header-code' ), fcaPcAdminData.code_editor )


	function draw_pixels() {
		$('.fca_pc_pixel_row').each(function(){
			draw_pixel( $(this), JSON.parse( $(this).find('.fca-pc-pixel-json').val() ) )
		})
		
		var active_pixels = fca_pc_get_active_pixel_types()
		
		$('#fca-pc-modal-type-select option').each(function(){
			if( $(this).val() === "GA3" ) {
				if( active_pixels.indexOf("GA3") !== -1 ) {
					$(this).show()
				} else {
					$(this).hide()
				}
			}
		})
		
	}
	draw_pixels()


	function draw_events() {
		$('.fca_pc_event_row').each(function(){
			draw_event( $(this), JSON.parse( $(this).find('.fca-pc-json').val() ) )
		})
	}
	draw_events()

	$('.fca_pc_multiselect').select2()
	$('.fca-pc-validation-helptext').not('.tooltipstered').tooltipster( { trigger: 'custom', timer: 6000, maxWidth: 350, theme: ['tooltipster-borderless', 'tooltipster-pixel-cat'] } )
	
	$('.fca_pc_tooltip, .fca_pc_event_tooltip, .fca_delete_icon, .fca_controls_icon, #fca_pc_new_pixel_id').not('.tooltipstered').tooltipster( { contentAsHTML: true, theme: ['tooltipster-borderless', 'tooltipster-pixel-cat'] } )

	//////////////////
	//EVENT HANDLERS
	//////////////////

	//PIXEL ID VALIDATION
	$('#fca-pc-modal-pixel-input').on( 'input', function(e){
		var value = $(this).val()
		$('#fca-pc-pixel-helptext').tooltipster('hide')
		
		if ( !(/^\d+$/.test(value) ) && value !== '' ) {
			$(this).val( value.replace(/[^0-9\.]+/g,"") )
			$('#fca-pc-pixel-helptext').tooltipster('open')
		}
	})
	
	//PINTEREST TAG ID VALIDATION
	$('#fca-pc-modal-pinterest-input').on( 'input', function(e){
		var value = $(this).val()
		$('#fca-pc-pinterest-helptext').tooltipster('hide')
		
		if ( !(/^\d+$/.test(value) ) && value !== '' ) {
			$(this).val( value.replace(/[^0-9\.]+/g,"") )
			$('#fca-pc-pinterest-helptext').tooltipster('open')
		}
	})

	//GA3 ID VALIDATION
	$('#fca-pc-modal-ga3-input').on( 'input', function(e){
		var value = $(this).val()
		
		$('#fca-pc-ga3-helptext').tooltipster('hide')
		if ( (/^([UA-])+(([a-zA-Z0-9]){2,})$/.test(value) ) === false ) {
			$('#fca-pc-ga3-helptext').tooltipster('open')
		}
	})
	
	//GA4 ID VALIDATION
	$('#fca-pc-modal-ga4-input').on( 'input', function(e){
		var value = $(this).val()
		$('#fca-pc-ga4-helptext').tooltipster('hide')
		
		if ( (/^([G-])+(([a-zA-Z0-9]){2,})$/.test(value) ) === false ) {
			$('#fca-pc-ga4-helptext').tooltipster('open')
		}
	})
	
	//ADWORDS ID VALIDATION
	$('#fca-pc-modal-adwords-input').on( 'input', function(e){
		var value = $(this).val()
		$('#fca-pc-adwords-helptext').tooltipster('hide')
		
		if ( (/^([G-])+(([a-zA-Z0-9]){2,})$/.test(value) ) === false ) {
			$('#fca-pc-adwords-helptext').tooltipster('open')
		}
	})

	//NAV
	$('.fca-pc-nav a').on( 'click', function(){
		$('.fca-pc-nav a.selected').removeClass('selected')
		$(this).addClass('selected')
		$('.fca-pc-nav a').each(function(){
			$( $(this).data('target') ).hide()
		})
		$( $(this).data('target') ).show()
		$(this).trigger( 'blur' )
	})
	$('.fca-pc-nav a').first().trigger('click')

	//TABLE HEADINGS TOGGLE CHECKBOXES
	$('.fca_pc_integrations_table th').on( 'click', function(){
		$(this).next().find('input').trigger('click')
	})

	//NEW EVENT BUTTON
	$('#fca_pc_new_fb_event').on( 'click', function(){
		$('#fca-pc-event-save').data('eventID', '')

		//SET DEFAULTS
		$('.fca-pc-content_name').val('{post_title}')
		$('.fca-pc-content_type').val('product')
		$('.fca-pc-content_ids').val('{post_id}')
		$('.fca-pc-content_category').val('{post_category}')
		$('.fca-pc-search_string').val('')
		$('.fca-pc-num_items').val('')
		$('.fca-pc-status').val('')
		$('.fca-pc-value').val('')
		$('.fca-pc-currency').val('')
		$('.fca-pc-predicted_ltv').val('')
		$('.fca-pc-event_name' ).val('')
		
		$('#fca-pc-modal-post-trigger-input').val('').trigger( 'change' )
		$('#fca-pc-modal-css-trigger-input').val('')
		$('#fca-pc-modal-url-trigger-input').val('')
		$('#fca-pc-modal-exact_url-trigger-input').val('')
		
		$('#fca-pc-modal-delay-input').val(0)
		$('#fca-pc-modal-scroll-input').val(0)

		
		fca_pc_filter_events( "Facebook" )
		$('#fca-pc-modal-event-pixel-type').val("Facebook")
		$('#fca-pc-event-pixel-type-span').text("Facebook")
		$('#fca-pc-modal-event-input').val('ViewContent').trigger( 'change' )
		//SET VISIBILITY BY TRIGGERING SHOW/HIDE CLICK HANDLER
		$('.fca-pc-param-toggle').not(':visible').trigger('click') 
		
		
		$('#fca-pc-event-modal').show()
		$('#fca-pc-overlay').show()

	})
	
	
	$('#fca_pc_new_tiktok_event').on( 'click', function(){
		$('#fca-pc-event-save').data('eventID', '')

		//SET DEFAULTS
		$('.fca-pc-content_name').val('{post_title}')
		$('.fca-pc-content_type').val('product')
		$('.fca-pc-content_ids').val('{post_id}')
		$('.fca-pc-content_category').val('{post_category}')
		$('.fca-pc-search_string').val('')
		$('.fca-pc-num_items').val('')
		$('.fca-pc-status').val('')
		$('.fca-pc-value').val('')
		$('.fca-pc-currency').val('')
		$('.fca-pc-predicted_ltv').val('')
		$('.fca-pc-event_name' ).val('')
		
		$('#fca-pc-modal-post-trigger-input').val('').trigger( 'change' )
		$('#fca-pc-modal-css-trigger-input').val('')
		$('#fca-pc-modal-url-trigger-input').val('')
		$('#fca-pc-modal-exact_url-trigger-input').val('')
		
		$('#fca-pc-modal-delay-input').val(0)
		$('#fca-pc-modal-scroll-input').val(0)

		
		fca_pc_filter_events( "TikTok" )
		$('#fca-pc-modal-event-pixel-type').val("TikTok")
		$('#fca-pc-event-pixel-type-span').text("TikTok")
		$('#fca-pc-modal-event-input').val('ViewContentTiktok').trigger( 'change' )
		//SET VISIBILITY BY TRIGGERING SHOW/HIDE CLICK HANDLER
		$('.fca-pc-param-toggle').not(':visible').trigger('click')
		
		$('#fca-pc-event-modal').show()
		$('#fca-pc-overlay').show()

	})
	
	$('#fca_pc_new_ga_event').on( 'click', function(){
		$('#fca-pc-event-save').data('eventID', '')

		//SET DEFAULTS
		$('.fca-pc-content_name').val('{post_title}')
		$('.fca-pc-content_type').val('product')
		$('.fca-pc-content_ids').val('{post_id}')
		$('.fca-pc-content_category').val('{post_category}')
		$('.fca-pc-search_string').val('')
		$('.fca-pc-num_items').val('')
		$('.fca-pc-status').val('')
		$('.fca-pc-value').val('')
		$('.fca-pc-currency').val('')
		$('.fca-pc-predicted_ltv').val('')
		$('.fca-pc-event_name' ).val('')
		
		$('#fca-pc-modal-post-trigger-input').val('').trigger( 'change' )
		$('#fca-pc-modal-css-trigger-input').val('')
		$('#fca-pc-modal-url-trigger-input').val('')
		$('#fca-pc-modal-exact_url-trigger-input').val('')
		
		$('#fca-pc-modal-delay-input').val(0)
		$('#fca-pc-modal-scroll-input').val(0)

		
		$('#fca-pc-modal-event-pixel-type').val( "Google Analytics" )
		$('#fca-pc-event-pixel-type-span').text("Google Analytics")
		fca_pc_filter_events( "Google Analytics" )
		$('#fca-pc-modal-event-input').val('ViewContentGA').trigger( 'change' )

		$('#fca-pc-event-modal').show()
		$('#fca-pc-overlay').show()

	})
	
	$('#fca_pc_new_pinterest_event').on( 'click', function(){
		$('#fca-pc-event-save').data('eventID', '')

		//SET DEFAULTS
		$('.fca-pc-content_name').val('{post_title}')
		$('.fca-pc-content_type').val('product')
		$('.fca-pc-content_ids').val('{post_id}')
		$('.fca-pc-content_category').val('{post_category}')
		$('.fca-pc-search_string').val('')
		$('.fca-pc-num_items').val('')
		$('.fca-pc-status').val('')
		$('.fca-pc-value').val('')
		$('.fca-pc-currency').val('')
		$('.fca-pc-predicted_ltv').val('')
		$('.fca-pc-event_name' ).val('')
		
		$('#fca-pc-modal-post-trigger-input').val('').trigger( 'change' )
		$('#fca-pc-modal-css-trigger-input').val('')
		$('#fca-pc-modal-url-trigger-input').val('')
		$('#fca-pc-modal-exact_url-trigger-input').val('')
		
		$('#fca-pc-modal-delay-input').val(0)
		$('#fca-pc-modal-scroll-input').val(0)

		
		fca_pc_filter_events( "Pinterest" )
		$('#fca-pc-modal-event-pixel-type').val("Pinterest")
		$('#fca-pc-event-pixel-type-span').text("Pinterest")
		$('#fca-pc-modal-event-input').val('ViewContentPinterest').trigger( 'change' )
		//SET VISIBILITY BY TRIGGERING SHOW/HIDE CLICK HANDLER
		$('.fca-pc-param-toggle').not(':visible').trigger('click')
		
		$('#fca-pc-event-modal').show()
		$('#fca-pc-overlay').show()

	})
	
	$('#fca_pc_new_snapchat_event').on( 'click', function(){
		$('#fca-pc-event-save').data('eventID', '')

		//SET DEFAULTS
		$('.fca-pc-currency').val('')
		$('.fca-pc-predicted_ltv').val('')
		$('.fca-pc-event_name' ).val('')
		
		$('#fca-pc-modal-post-trigger-input').val('').trigger( 'change' )
		$('#fca-pc-modal-css-trigger-input').val('')
		$('#fca-pc-modal-url-trigger-input').val('')
		$('#fca-pc-modal-exact_url-trigger-input').val('')
		
		$('#fca-pc-modal-delay-input').val(0)
		$('#fca-pc-modal-scroll-input').val(0)

		
		fca_pc_filter_events( "Snapchat" )
		$('#fca-pc-modal-event-pixel-type').val("Snapchat")
		$('#fca-pc-event-pixel-type-span').text("Snapchat")
		$('#fca-pc-modal-event-input').val('ViewContentSnapchat').trigger( 'change' )
		//SET VISIBILITY BY TRIGGERING SHOW/HIDE CLICK HANDLER
		$('.fca-pc-param-toggle').not(':visible').trigger('click')
		
		$('#fca-pc-event-modal').show()
		$('#fca-pc-overlay').show()

	})
	
	//ONLY SHOW EVENTS FOR THIS API/PIXEL TYPE
	function fca_pc_filter_events( pixel_type ) {
		//DEFAULT TO VIEW CONTENT EVENT
		
		
		//FACEBOOK EVENTS
		var supported_events = [
			'ViewContent',
			'Lead',
			'AddToCart',
			'AddToWishlist',
			'InitiateCheckout',
			'AddPaymentInfo',
			'Purchase',
			'CompleteRegistration',
			'Contact',
			'CustomizeProduct',
			'Donate',
			'FindLocation',
			'Schedule',
			'StartTrial',
			'Subscribe',
			'SubmitApplication',
			'custom'
		]
		
		switch( pixel_type ) {
			case "Google Analytics":
				supported_events = [
					'ViewContentGA',
					'AddToCartGA',
					'AddToWishlistGA',
					'InitiateCheckoutGA',
					'AddPaymentInfoGA',
					'PurchaseGA',
					//'LeadGA',
					//'CompleteRegistrationGA',
					'custom'
				]
				break	

			case "TikTok":
				supported_events = [
					'ViewContentTiktok',
					'AddToCartTiktok',
					'AddToWishlistTiktok',
					'InitiateCheckoutTiktok',
					'AddPaymentInfoTiktok',
					'PurchaseTiktok',
					'CompleteRegistrationTiktok',
					'custom'
				]
				break	

			case "Pinterest":
				supported_events = [
					'ViewContentPinterest',
					'AddToCartPinterest',
					'PurchasePinterest',
					'CompleteRegistrationPinterest',
					'LeadPinterest',
					'custom'
				]
				break
				
			case "Snapchat":
				supported_events = [
					'ViewContentSnapchat',
					'AddToCartSnapchat',
					'AddToWishlistSnapchat',
					'InitiateCheckoutSnapchat',
					'AddPaymentInfoSnapchat',
					'PurchaseSnapchat'
				]
				break
				
			default:
				
		}
		
		
		$('#fca-pc-modal-event-input option').each(function(){
			if( supported_events.indexOf( $(this).attr('value') ) !== -1 ) {
				$(this).show()
			} else {
				$(this).hide()
			}
		})
		
	}
	
	//NEW PIXEL ID
	$('#fca_pc_new_pixel_id').on( 'click', function(){

		$('#fca-pc-pixel-save').data('pixelID', '')

		//Set default type to pixel
		$('#fca-pc-modal-type-select').val('Facebook Pixel').trigger( 'change' )

		//Clear inputboxes
		$('#fca-pc-modal-pixel-input').val( '' )
		$('#fca-pc-modal-capi-input').val( '' )
		$('#fca-pc-modal-test-input').val( '' )
		$('#fca-pc-modal-ga3-input').val( '' )
		$('#fca-pc-modal-ga4-input').val( '' )
		$('#fca-pc-modal-adwords-input').val( '' )
		$('#fca-pc-modal-pinterest-input').val( '' )
		$('#fca-pc-modal-tiktok-input').val( '' )
		$('#fca-pc-modal-header-code').val( '' )
		$('#fca-pc-pixel-excluded-pages').val( '' ).trigger('change')
		
		//Hide CAPI inputs by default
		$('#fca-pc-capi-input-tr').hide()
		$('#fca-pc-test-input-tr').hide()

		//Show modal & overlay
		$('#fca-pc-pixel-modal').show()
		$('#fca-pc-overlay').show()
	})

	// Show / hide CAPI settings on select change
	$('#fca-pc-modal-type-select').on( 'change', function(){
		var input_value = $(this).val()
		$('#fca-pc-capi-input-tr').hide()
		$('#fca-pc-test-input-tr').hide()
		$('#fca-pc-pixel-input-tr').hide()
		$('#fca-pc-ga3-input-tr').hide()
		$('#fca-pc-ga4-input-tr').hide()
		$('#fca-pc-adwords-input-tr').hide()
		$('.fca-pc-header-input-tr').hide()
		$('#fca_pc_capi_info').hide()
		$('#fca-pc-pinterest-input-tr').hide()
		$('#fca-pc-snapchat-input-tr').hide()
		$('#fca-pc-tiktok-input-tr').hide()
		
		switch( input_value ) {
			case 'Conversions API':
				$('#fca-pc-pixel-input-tr').show()
				$('#fca-pc-capi-input-tr').show()
				$('#fca-pc-test-input-tr').show()
				$('#fca_pc_capi_info').show()
				break 

			case 'Facebook Pixel':
				$('#fca-pc-pixel-input-tr').show()
				break
				
			case 'GA3':
				$('#fca-pc-ga3-input-tr').show()
				break
				
			case 'GA4':
				$('#fca-pc-ga4-input-tr').show()
				break
				
			case 'Adwords':
				$('#fca-pc-adwords-input-tr').show()
				break
				
			case 'Pinterest':
				$('#fca-pc-pinterest-input-tr').show()
				break
				
			case 'Snapchat':
				$('#fca-pc-snapchat-input-tr').show()
				break
				
			case 'TikTok':
				$('#fca-pc-tiktok-input-tr').show()
				break
				
			case 'Custom Header Script':
				$('.fca-pc-header-input-tr').show()
				break
				
				
		}
	})

	//Link to ecommerce tab
	$('#fca_pc_woo_toggle_link').on( 'click', function(){
		$('#fca-pc-event-modal').hide()
		$('.fca_pc_tooltip').tooltipster('hide')
		$('#fca-pc-overlay').hide()
		$("[data-target|='#fca-pc-e-commerce']").trigger('click')
		//$('.nav-tab').eq(2).trigger('click')
	})

	//CANCEL EVENT DIALOG
	$('#fca-pc-event-cancel').on( 'click', function(){
		$('#fca-pc-event-modal').hide()
		$('.fca_pc_tooltip').tooltipster('hide')
		$('#fca-pc-overlay').hide()
	})

	//CANCEL PIXEL DIALOG
	$('#fca-pc-pixel-cancel').on( 'click', function(){
		$('#fca-pc-pixel-modal').hide()
		$('.fca_pc_tooltip').tooltipster('hide')
		$('#fca-pc-overlay').hide()
	})
	
	//MAKE SURE WE HAVE A PIXEL SETUP
	$('.fca-pc-edd_integration').on( 'click', function(){
		var active_pixels = fca_pc_get_active_pixel_types()
		if( active_pixels.indexOf("Facebook Pixel") !== -1 || active_pixels.indexOf("Conversions API") !== -1 ) {
			//OK
		} else {
			alert("Please set up a Facebook Pixel to use this feature")
			$(this).prop('checked', false)
		}
	})
	//MAKE SURE WE HAVE A PIXEL SETUP
	$('.fca-pc-woo_integration').on( 'click', function(){
		var active_pixels = fca_pc_get_active_pixel_types()
		if( active_pixels.indexOf("Facebook Pixel") !== -1 || active_pixels.indexOf("Conversions API") !== -1 ) {
			//OK
		} else {
			alert("Please set up a Facebook Pixel to use this feature")
			$(this).prop('checked', false)
		}
	})
	
	//MAKE SURE WE HAVE A GA SETUP
	$('.fca-pc-edd_integration_ga').on( 'click', function(){
		var active_pixels = fca_pc_get_active_pixel_types()
		if( active_pixels.indexOf("GA3") !== -1 || active_pixels.indexOf("GA4") !== -1 || active_pixels.indexOf("Adwords") !== -1 ) {
			//OK
		} else {
			alert("Please set up a Google Analytics pixel to use this feature")
			$(this).prop('checked', false)
		}
	})
	//MAKE SURE WE HAVE A GA SETUP
	$('.fca-pc-woo_integration_ga').on( 'click', function(){
		var active_pixels = fca_pc_get_active_pixel_types()
		if( active_pixels.indexOf("GA3") !== -1 || active_pixels.indexOf("GA4") !== -1 || active_pixels.indexOf("Adwords") !== -1 ) {
			//OK
		} else {
			alert("Please set up a Google Analytics pixel to use this feature")
			$(this).prop('checked', false)
		}
	})
	
	//MAKE SURE WE HAVE A PINTEREST SETUP
	$('.fca-pc-woo_integration_pinterest').on( 'click', function(){
		var active_pixels = fca_pc_get_active_pixel_types()
		if( active_pixels.indexOf("Pinterest") !== -1 ) {
			//OK
		} else {
			alert("Please set up your Pinterest Tag ID to use this feature")
			$(this).prop('checked', false)
		}
	})
	//MAKE SURE WE HAVE A PINTEREST SETUP
	$('.fca-pc-edd_integration_pinterest').on( 'click', function(){
		var active_pixels = fca_pc_get_active_pixel_types()
		if( active_pixels.indexOf("Pinterest") !== -1 ) {
			//OK
		} else {
			alert("Please set up your Pinterest Tag ID to use this feature")
			$(this).prop('checked', false)
		}
	})

	//KEYBINDS
	$( document ).on( 'keyup', function(e) {
		var select2Open = $(e.target).hasClass('select2-search__field')
		if ( e.key == "Escape" && !select2Open ){
			if( $( '#fca-pc-event-modal' ).is( ':visible' ) ) {
				$('#fca-pc-event-cancel').trigger('click')		
			}
			if( $( '#fca-pc-pixel-modal' ).is( ':visible' ) ) {
				$('#fca-pc-pixel-cancel').trigger('click')	
			}
		}
	})
	
	//SAVE PIXEL DIALOG CLICK
	$('#fca-pc-pixel-save').on( 'click', function(){
		var pixelType = $('#fca-pc-modal-type-select').val()
		$('.fca_pc_tooltip').tooltipster('hide')
		$('.fca-pc-validation-helptext').tooltipster('hide')
		
		if( pixelType === 'Facebook Pixel' && $('#fca-pc-modal-pixel-input').val() == '' ){
			alert( 'Please enter the Pixel ID' )
			return
		}
		if( pixelType === 'Pinterest' && $('#fca-pc-modal-pinterest-input').val() == '' ){
			alert( 'Please enter the Pixel ID' )
			return
		} 
		if ( pixelType === 'Conversions API' && $('#fca-pc-modal-capi-input').val() == '' ) {
			alert( 'Conversions API Token is required for this pixel type' )
			return
		}
		if ( pixelType === 'GA3' && $('#fca-pc-modal-ga3-input').val() == '' ) {
			alert( 'Google Universal Analytics ID is required for this pixel type' )
			return
		}
		if ( pixelType === 'GA4' && $('#fca-pc-modal-ga4-input').val() == '' ) {
			alert( 'Google Analytics Property ID is required for this pixel type' )
			return
		}
		if ( pixelType === 'Adwords' && $('#fca-pc-modal-adwords-input').val() == '' ) {
			alert( 'Google Ads Property ID is required for this pixel type' )
			return
		}		
		if ( pixelType === 'TikTok' && $('#fca-pc-modal-tiktok-input').val() == '' ) {
			alert( 'Please enter the Pixel ID' )
			return
		}
				
		if ( pixelType === 'Custom Header Script' && $('#fca-pc-modal-header-code').val() == '' ) {
			alert( 'Please enter a value for header script' )
			return
		}
		
		var newPixel = {}
		// GET VALUES
		newPixel.type = $('#fca-pc-modal-type-select').val()
		newPixel.capi = ''
		newPixel.test = ''
		
		switch( newPixel.type ) {
			case 'Conversions API':
				newPixel.test = $('#fca-pc-modal-test-input').val()
				newPixel.pixel = $('#fca-pc-modal-pixel-input').val()
				newPixel.capi = $('#fca-pc-modal-capi-input').val()
				break
				
			case 'GA3':
				newPixel.pixel = $('#fca-pc-modal-ga3-input').val()
				break
				
			case 'GA4':
				newPixel.pixel = $('#fca-pc-modal-ga4-input').val()
				break
				
			case 'Adwords':
				newPixel.pixel = $('#fca-pc-modal-adwords-input').val()
				break
				
			case 'Pinterest':
				newPixel.pixel = $('#fca-pc-modal-pinterest-input').val()
				break
				
			case 'Snapchat':
				newPixel.pixel = $('#fca-pc-modal-snapchat-input').val()
				break
			
			case 'TikTok':
				newPixel.pixel = $('#fca-pc-modal-tiktok-input').val()
				break
				
			case 'Custom Header Script':
				newPixel.capi = escape_html( $('#fca-pc-modal-header-code').val() )
				newPixel.pixel = escape_html( $('#fca-pc-modal-header-code').val().substr(0, 48) )
				break
				
			default:
				newPixel.pixel = $('#fca-pc-modal-pixel-input').val()

		}
		
		newPixel.excludes = $('#fca-pc-pixel-excluded-pages').val()
				
		//ID
		if ( $(this).data('pixelID') ) {
			newPixel.ID = $(this).data('pixelID')
			//PAUSED
			newPixel.paused = $('#' + newPixel.ID ).hasClass('paused')
			draw_pixel( $('#' + newPixel.ID ), newPixel )
			
		} else {
			newPixel.paused = false
			newPixel.ID = fca_pc_new_GUID()
			draw_pixel( false, newPixel )
		}
		
		$('.fca_pc_onboarding').hide()
		$('#fca-pc-events-table').removeClass('disabled')
		$('#fca-pc-overlay').hide()
		$('#fca-pc-pixel-modal').hide()
		set_event_button_state()
	})


	//SAVE DIALOG CLICK
	$('#fca-pc-event-save').on( 'click', function(){

		var valid = true
		//DATA VALIDATION / TOOLTIPS
		$('.fca_pc_tooltip').tooltipster('hide')
		var trigger = $('#fca-pc-modal-trigger-type-input').val()
		switch( trigger ) {
			case 'post':
				selected = $('#fca-pc-modal-post-trigger-input').val()
				if( selected.length === 0 ) {
					$('#fca-pc-modal-post-trigger-input').closest('tr').find('.fca_pc_tooltip').tooltipster('show')
					valid = false	
				}
				break
				
			case 'css':
				selected = $('#fca-pc-modal-css-trigger-input').val()
				if( selected.length === 0 ) {
					$('#fca-pc-modal-css-trigger-input').closest('tr').find('.fca_pc_tooltip').tooltipster('show')
					valid = false	
				}
				break
				
			case 'hover':
				selected = $('#fca-pc-modal-css-trigger-input').val()
				if( selected.length === 0 ) {
					$('#fca-pc-modal-css-trigger-input').closest('tr').find('.fca_pc_tooltip').tooltipster('show')
					valid = false	
				}
				break
				
			case 'url':
				selected = $('#fca-pc-modal-url-trigger-input').val()
				if( selected.length === 0 ) {
					$('#fca-pc-modal-url-trigger-input').closest('tr').find('.fca_pc_tooltip').tooltipster('show')
					valid = false	
				}
				break	
				
			case 'exact_url':
				selected = $('#fca-pc-modal-exact_url-trigger-input').val()
				if( selected.length === 0 ) {
					$('#fca-pc-modal-exact_url-trigger-input').closest('tr').find('.fca_pc_tooltip').tooltipster('show')
					valid = false	
				}
				break			
			
		}
		
		$('.fca-required-param').each( function(){
			if ( !$(this).find('input').val() ) {
				$('#fca-pc-show-param').trigger('click')
				$(this).find('.fca_pc_tooltip').tooltipster('show')
				valid = false
			}
		})

		if ( !$('.fca-pc-event_name').val() && $('#fca-pc-modal-event-input').val() === 'custom' ) {
			$('.fca-pc-event_name').closest('tr').find('.fca_pc_tooltip').tooltipster('show')
			valid = false
		}

		if ( !valid ) {
			return false
		}

		var fbEvent = {}

		fbEvent.triggerType = $('#fca-pc-modal-trigger-type-input').val()

		switch ( fbEvent.triggerType ) {

			case 'css':
				fbEvent.trigger = $('#fca-pc-modal-css-trigger-input').val()
				break

			case 'hover':
				fbEvent.trigger = $('#fca-pc-modal-css-trigger-input').val()
				break

			case 'post':
				fbEvent.trigger = $('#fca-pc-modal-post-trigger-input').val()
				break

			case 'url':
				fbEvent.trigger = $('#fca-pc-modal-url-trigger-input').val()
				break
				
			case 'exact_url':
				fbEvent.trigger = $('#fca-pc-modal-exact_url-trigger-input').val()
				break

			default:
				break


		}

		//PARAMETERS
		fbEvent.parameters = {}
		fbEvent.pixel_type = $('#fca-pc-modal-event-pixel-type').val()
		if ( $('#fca-pc-modal-event-input').val() === 'custom' ) {
			fbEvent.event = $('.fca-pc-event_name').val()

		} else {
			//ONLY SAVE PARAMETERS THAT ARE VALID FOR THIS EVENT (?)
			fbEvent.event = $('#fca-pc-modal-event-input').val()
			for ( var i = 0; i < basic_params.length; i++ ) {
				var thisParam = basic_params[i]
				if ( supported_params[fbEvent.event][thisParam] > 0 && $('.fca-pc-' + thisParam ).val() != '' ) {
					fbEvent.parameters[thisParam] = $('.fca-pc-' + thisParam ).val()
				}
			}
		}

		//SET CUSTOM PARAMETERS
		$('.fca-pc-input-parameter-name').each(function(index, element){
			var name = $( this ).val()
			var value = $( this ).parent('td').next().find('.fca-pc-input-parameter-value').val()
			if( name && value ) {
				fbEvent.parameters[ name ] = value
			}
		})
		
		//DELAY
		fbEvent.delay = $('#fca-pc-modal-delay-input').val()

		//SCROLL
		fbEvent.scroll = $('#fca-pc-modal-scroll-input').val()

		fbEvent.apiAction = $('#fca-pc-modal-event-input').val() === 'custom' ? 'trackCustom' : 'track'

		//ID
		if ( $(this).data('eventID') ) {
			fbEvent.ID = $(this).data('eventID')
			//PAUSED
			fbEvent.paused = $('#' + fbEvent.ID ).hasClass('paused')

			draw_event( $('#' + fbEvent.ID ), fbEvent )

		} else {
			fbEvent.ID = fca_pc_new_GUID()
			draw_event( false, fbEvent )
		}

		$('#fca-pc-overlay').hide()
		$('#fca-pc-event-modal').hide()
	})

	//EVENT CHANGE HANDLER
	$('#fca-pc-modal-event-input').on( 'change', function(){

		var event = $(this).val()

		//RESET STATE
		$('.fca-active-param').removeClass('fca-active-param')
		$('.fca-required-param').removeClass('fca-required-param')
		$('.fca_pc_tooltip').tooltipster('hide')
		$('#fca-pc-hide-param').trigger('click')

		//HIDE TOOLTIPS
		$('#fca_pc_tooltip_viewcontent, #fca_pc_tooltip_lead').hide()
		$('.fca-required-param-tooltip' ).hide()

		//SET WHAT PARAMETERS ARE AVAIALBLE FOR THE EVENT
		for ( item in supported_params[event] ) {
			if ( supported_params[event][item] > 0 ) {
				//GREATER THAN 0 MEANS IT SUPPORTS THIS PARAM
				$('#fca_pc_param_' + item ).addClass('fca-active-param')
				//2 MEANS IT REQUIRES THIS PARAM
				if ( supported_params[event][item] === 2 ) {
					$('#fca_pc_param_' + item ).addClass('fca-required-param')
					$('.fca-required-param-tooltip' ).show()
					//IF WE HAVE REQUIRED PARAMS, EXPAND THE PARAMETER LIST
					$('#fca-pc-show-param').trigger('click')

				}
			}
		}
		
		//MAYBE SHOW THE TIME DELAY & SCROLL % OPTIONS
		if ( $('#fca-pc-modal-trigger-type-input').val() === 'post' || $('#fca-pc-modal-trigger-type-input').val() === 'exact_url' ) {
			$('#fca-pc-modal-delay-input, #fca-pc-modal-scroll-input').closest('tr').show()
			$('#fca_pc_tooltip_viewcontent').show()
		} else {
			$('#fca-pc-modal-delay-input, #fca-pc-modal-scroll-input').closest('tr').hide()
			$('#fca_pc_tooltip_lead').show()
		}


		$('#fca_pc_param_custom' ).addClass('fca-active-param')
		//EXTRA STUFF FOR CUSTOM EVENT
		if ( event === 'custom' ) {
			$('#fca_pc_param_event_name' ).show()
		} else {
			$('#fca_pc_param_event_name' ).hide()
		}

	})

	//PARAMETER TOGGLE BUTTON
	$('.fca-pc-param-toggle').on( 'click', function(){
		$('.fca-pc-param-row').hide()
		if ( $(this).attr('id') === 'fca-pc-show-param' ) {
			$('#fca-pc-show-param').hide()
			$('#fca-pc-hide-param').show()
			$('#fca-pc-param-help').show()
			$('.fca-active-param').show()
		} else {
			$('#fca-pc-hide-param').hide()
			$('#fca-pc-show-param').show()
			$('#fca-pc-param-help').hide()
		}
	})

	//WOO FEED SETTING TOGGLE
	$('.fca-pc-woo_feed').on( 'change', function(){
		if ( this.checked ) {
			$('.fca-pc-woo-feed-settings').not('.fca-pc-woo-advanced-feed-settings').show('fast')
		} else {
			$('.fca-pc-woo-feed-settings').hide('fast')
		}

	}).trigger( 'change' )

	//WOO ADVANCED FEED SETTINGS TOGGLE
	$('.fca-pc-feed-toggle').on( 'click', function(){
		$('.fca-pc-feed-toggle').hide()
		if ( $(this).attr('id') === 'fca-pc-show-feed-settings' ) {
			$('#fca-pc-show-feed-settings').hide()
			$('#fca-pc-hide-feed-settings').show()

			$('.fca-pc-woo-advanced-feed-settings').show()
		} else {
			$('#fca-pc-show-feed-settings').show()
			$('#fca-pc-hide-feed-settings').hide()

			$('.fca-pc-woo-advanced-feed-settings').hide()
		}
	})

	//EDD FEED SETTING TOGGLE
	$('.fca-pc-edd_feed').on( 'change', function(){
		if ( this.checked ) {
			$('.fca-pc-edd-feed-settings').show('fast')
		} else {
			$('.fca-pc-edd-feed-settings').hide('fast')
		}

	}).trigger( 'change' )

	//CUSTOM PARAMETER TABLE
	if ( fcaPcAdminData.premium ) {
		$('#fca-pc-add-custom-param').on( 'click', function(){
			$('#fca_pc_custom_param_table').append( fcaPcPremiumData.customParamTemplate )
			attach_row_actions()
		})
	}

	//SET INPUT VISIBILITY
	$('#fca-pc-modal-trigger-type-input').on( 'change', function(){
		//HIDE SELECT2 - BUGGY WHEN HIDING
		$('.fca_pc_multiselect').select2('close')

		$('#fca-pc-post-input-tr, #fca-pc-css-input-tr, #fca-pc-url-input-tr, #fca-pc-exact_url-input-tr').hide()
		$('#fca-pc-modal-event-input').trigger( 'change' )

		$('#fca-pc-' + $(this).val() + '-input-tr').show()
		if ( $(this).val() === 'hover' ) {
			$('#fca-pc-css-input-tr').show()
		}

	}).trigger( 'change' )
	
	//EVENT ROW ACTIONS
	function attach_row_actions() {

		$('.fca_delete_icon_confirm').off( 'click' )
		$('.fca_delete_icon_confirm').on( 'click', function( e ){
			//DONT SHOW OVERLAY IF YOU CLICK DELETE
			confirmUnload = true
			e.stopPropagation()
			$( this ).closest( '.fca_deletable_item' ).hide( 'fast', function() {
				$( this ).remove()
			})
		})

		// PIXEL PAUSE
		$('.fca_controls_icon_pixel_pause').off( 'click' )
		$('.fca_controls_icon_pixel_pause').on( 'click', function( e ){
			e.stopPropagation()
			confirmUnload = true
			var $jsonInput = $(this).closest('.fca_pc_pixel_row').find('.fca-pc-pixel-json')
			var pixel = JSON.parse( $jsonInput.val() )

			pixel.paused = true
			$jsonInput.val( JSON.stringify(pixel) )

			$(this).closest('tr').addClass('paused')
			$(this).hide().siblings('.fca_controls_icon_pixel_play').show()
		})

		// PIXEL PLAY
		$('.fca_controls_icon_pixel_play').off( 'click' )
		$('.fca_controls_icon_pixel_play').on( 'click', function( e ){
			e.stopPropagation()
			confirmUnload = true
			var $jsonInput = $(this).closest('.fca_pc_pixel_row').find('.fca-pc-pixel-json')
			var pixel = JSON.parse( $jsonInput.val() )

			pixel.paused = false
			$jsonInput.val( JSON.stringify(pixel) )


			$(this).closest('tr').removeClass('paused')
			$(this).hide().siblings('.fca_controls_icon_pixel_pause').show()

		})

		// EVENT PAUSE
		$('.fca_controls_icon_pause').off( 'click' )
		$('.fca_controls_icon_pause').on( 'click', function( e ){
			e.stopPropagation()
			confirmUnload = true
			var $jsonInput = $(this).closest('.fca_pc_event_row').find('.fca-pc-json')
			var event = JSON.parse( $jsonInput.val() )

			event.paused = true
			$jsonInput.val( JSON.stringify(event) )

			$(this).closest('tr').addClass('paused')
			$(this).hide().siblings('.fca_controls_icon_play').show()
		})

		// EVENT PLAY
		$('.fca_controls_icon_play').off( 'click' )
		$('.fca_controls_icon_play').on( 'click', function( e ){
			e.stopPropagation()
			confirmUnload = true
			var $jsonInput = $(this).closest('.fca_pc_event_row').find('.fca-pc-json')
			var event = JSON.parse( $jsonInput.val() )

			event.paused = false
			$jsonInput.val( JSON.stringify(event) )


			$(this).closest('tr').removeClass('paused')
			$(this).hide().siblings('.fca_controls_icon_pause').show()

		})

		$('.fca_delete_icon_cancel').off( 'click' )
		$('.fca_delete_icon_cancel').on( 'click', function( e ){
			e.stopPropagation()
			
			$(this).hide()
			$(this).siblings('.fca_delete_icon').hide()
			$(this).siblings('.fca_delete_button').show()
		})

		$('.fca_delete_button').off( 'click' )
		$('.fca_delete_button').on( 'click', function( e ){
			e.stopPropagation()
			
			$(this).hide().siblings('.fca_delete_icon').show()

		})

		//EDIT PIXEL CLICK
		$('.fca_pc_pixel_row').off( 'click' )
		$('.fca_pc_pixel_row').on( 'click', function(){

			//SET THE SAVE BUTTON TO THIS ID
			$('#fca-pc-pixel-save').data( 'pixelID', $(this).attr('ID') )
			
			var pixel = JSON.parse( $(this).find('.fca-pc-pixel-json').val() )
			
			switch( pixel.type ) {
				
				case 'Conversions API': 
					$('#fca-pc-modal-pixel-input').val( pixel.pixel )
					$('#fca-pc-modal-capi-input').val( pixel.capi )
					$('#fca-pc-modal-test-input').val( pixel.test )
					break
					
				case 'GA3':
					$('#fca-pc-ga3-input-tr').show()
					$('#fca-pc-modal-ga3-input').val( pixel.pixel )
					break
					
				case 'GA4':
					$('#fca-pc-modal-ga4-input').val( pixel.pixel )
					break
					
				case 'Adwords':
					$('#fca-pc-modal-adwords-input').val( pixel.pixel )
					break
					
				case 'Pinterest':
					$('#fca-pc-modal-pinterest-input').val( pixel.pixel )
					break
					
				case 'Snapchat':
					$('#fca-pc-modal-snapchat-input').val( pixel.pixel )
					break
					
				case 'TikTok':
					$('#fca-pc-modal-tiktok-input').val( pixel.pixel )
					break
					
				case 'Custom Header Script':
					$('#fca-pc-modal-header-code').val( unescape_html ( pixel.capi ) )
					break
					
				default:
					$('#fca-pc-modal-pixel-input').val( pixel.pixel )
			}
			
			$('#fca-pc-modal-type-select').val( pixel.type ).trigger('change')
			$('#fca-pc-pixel-excluded-pages').val( pixel.excludes ).trigger('change')
			$('#fca-pc-pixel-modal').show()
			$('#fca-pc-overlay').show()

		})

		//EDIT EVENT CLICK
		$('.fca_pc_event_row').off( 'click' )
		$('.fca_pc_event_row').on( 'click', function(){

			//SET THE SAVE BUTTON TO THIS ID
			$('#fca-pc-event-save').data( 'eventID', $(this).attr('ID') )
			load_event( JSON.parse( $(this).find('.fca-pc-json').val() ) )
			$('#fca-pc-event-modal').show()
			$('#fca-pc-overlay').show()

		})

		//ATTACH TOOLTIPS
		$('.fca_delete_icon, .fca_controls_icon').not('.tooltipstered').tooltipster( { contentAsHTML: true, theme: ['tooltipster-borderless', 'tooltipster-pixel-cat'] } )

	}
	attach_row_actions()

	//TRIGGER NAVIGATION CONFIRM PROMPT FOR THIS PAGE
	var confirmUnload = null
	window.onbeforeunload = function() {
		return confirmUnload
	}

	$('input, select').on( 'input, change', function() {
		confirmUnload = true
	})

	$('#fca_pc_save').on( 'click', function() {
		confirmUnload = null
	})

	//THE PAGE IS HIDDEN UNTIL NOW
	$('#fca_pc_main_form, #fca-pc-marketing-metabox').show()
	
	//OPEN ADD PIXEL MODAL IF NONE ARE PRESENT
	if ( $('.fca-pc-pixel-json').length === 0 ) {
		$('#fca_pc_new_pixel_id').trigger( 'click' )
		$('#fca-pc-events-table').addClass('disabled')
	} else {
		$('.fca_pc_onboarding').hide()
	}
	
	set_event_button_state()
	function set_event_button_state() {
		var pixel_types = []
		$('#fca_pc_new_fb_event').hide()
		$('#fca_pc_new_ga_event').hide()
		$('#fca_pc_new_snapchat_event').hide()
		$('#fca_pc_new_pinterest_event').hide()
		$('#fca_pc_new_tiktok_event').hide()
		
		$('.fca-pc-pixel-json').each(function(){
			var pixel = JSON.parse( $(this).val() )
			pixel_types.push( pixel.type )
		})
		
		if ( pixel_types.indexOf("GA3") !== -1 || pixel_types.indexOf("GA4") !== -1 || pixel_types.indexOf("Adwords") !== -1 ) {
			$('#fca_pc_new_ga_event').show()
		}
		if ( pixel_types.indexOf("Pinterest") !== -1 ) {
			$('#fca_pc_new_pinterest_event').show()
		}
		if ( pixel_types.indexOf("Snapchat") !== -1 ) {
			$('#fca_pc_new_snapchat_event').show()
		}
		if ( pixel_types.indexOf("TikTok") !== -1 ) {
			$('#fca_pc_new_tiktok_event').show()
		}
		if ( pixel_types.indexOf("Facebook Pixel") !== -1 || pixel_types.indexOf("Conversions API") !== -1 ) {
			$('#fca_pc_new_fb_event').show()
		}
	}
			
	///////////////////
	//HELPER FUNCTIONS
	///////////////////
	

	function draw_pixel( $target, pixel ) {
		if ( fcaPcAdminData.debug ) {
			console.log ( $target, pixel )
		}
		if ( $target ) {

			$target.attr('id', pixel.ID )
			var type = pixel.type
			if( pixel.type === 'GA3' ) {
				type = "Google Analytics (GA3)"
			}
			if( pixel.type === 'GA4' ) {
				type = "Google Analytics (GA4)"
			}			
			if( pixel.type === 'Adwords' ) {
				type = "Google Ads"
			}
			
			$target.find('.fca-pc-type-td').text( type )
			$target.find('.fca-pc-pixel-td').text( unescape_html( pixel.pixel ) )
			
			$target.removeClass('paused')
			$target.find('.fca_controls_icon_pixel_play').hide().siblings('.fca_controls_icon_pixel_pause').show()

			if ( pixel.paused ) {
				$target.addClass('paused')
				$target.find('.fca_controls_icon_pixel_pause').hide().siblings('.fca_controls_icon_pixel_play').show()

			}

			if( pixel.test ){
				var type = $target.find('.fca-pc-type-td')
				type.text( type.text() + ' (test mode active)' )
			} 

		} else {
			$('#fca-pc-pixels').append(
				fcaPcAdminData.pixelTemplate.replace( '{{TYPE}}', pixel.type )
											.replace( '{{PIXEL}}', pixel.pixel )
											.replace( '{{TEST}}', pixel.test )
											.replace( '{{ID}}', pixel.ID )
			)
			attach_row_actions()
		}
		
		$('#' + pixel.ID ).find('.fca-pc-pixel-json').val( JSON.stringify( pixel ) )
	}
	
	function escape_html( str ) {
		return str
			 .replace(/&/g, "&amp;")
			 .replace(/</g, "&lt;")
			 .replace(/>/g, "&gt;")
			 .replace(/"/g, "&quot;")
			 .replace(/'/g, "&#039;")
	 }
	
	function unescape_html( str ) {
		return str
			.replace(/&lt;/g , "<") 
			.replace(/&gt;/g , ">")  
			.replace(/&quot;/g , "\"")
			.replace(/&#39;/g , "\'")
			.replace(/&amp;/g , "&")
	}

	

	function draw_event( $target, event ) {
		if ( fcaPcAdminData.debug ) {
			console.log ( $target, event )
		}
		var event_pixel_type = event.hasOwnProperty('pixel_type') ? event.pixel_type : 'Facebook'
		
		if ( $target ) {
			$target.attr('id', event.ID )

			$target.find('.fca-pc-event-pixel-td').text( event_pixel_type )
			$target.find('.fca-pc-event-td').text( event.event )
			$target.find('.fca-pc-trigger-td').text( get_event_trigger_names( event.trigger ) )


			$target.removeClass('paused')
			$target.find('.fca_controls_icon_play').hide().siblings('.fca_controls_icon_pause').show()

			if ( event.paused ) {
				$target.addClass('paused')
				$target.find('.fca_controls_icon_pause').hide().siblings('.fca_controls_icon_play').show()

			}
		} else {
			$('#fca-pc-events').append(
				fcaPcAdminData.eventTemplate.replace( '{{EVENT}}', event.event )
											.replace( '{{TYPE}}', get_event_trigger_names( event.pixel_type ) )
											.replace( '{{TRIGGER}}', get_event_trigger_names( event.trigger ) )
											.replace( '{{ID}}', event.ID )
			)
			attach_row_actions()
		}
		$('#' + event.ID ).find('.fca-pc-json').val( JSON.stringify( event ) )
	}

	function get_event_trigger_names( triggers ) {
		if ( typeof triggers === 'string' ) {
			return triggers
		} else {
			var array = []
			$('#fca-pc-modal-post-trigger-input option').filter( function( index, element ){
				return triggers.indexOf( $(element).val() ) !== -1
			}).each( function() {
				array.push( $(this).text() )
			})
			return array.join(', ')
		}
	}

	//LOAD THE MODAL WITH EVENT DATA
	function load_event( event ) {
		
		//CLEAR CUSTOM PARAMETERS
		$( '#fca_pc_custom_param_table' ).find( '.fca_delete_icon_confirm' ).trigger('click')

		//STANDARD EVENTS
		if ( supported_params.hasOwnProperty( event.event ) ) {
			$('#fca-pc-modal-event-input').val( event.event ).trigger( 'change' )

			//SET PARAMS
			for ( var i = 0; i < basic_params.length; i++) {
				if ( event.parameters.hasOwnProperty( basic_params[i] ) ) {
					//set
					$('.fca-pc-' + basic_params[i] ).val( event.parameters[basic_params[i]] )
				} else {
					//reset
					$('.fca-pc-' + basic_params[i] ).val('')
				}
			}

			//SET CUSTOM PARAMS
			for ( var eventParam in event.parameters ) {

				if ( basic_params.indexOf( eventParam ) === -1 ) {
					$( '#fca-pc-add-custom-param' ).trigger('click')
					$( '.fca-pc-input-parameter-name' ).last().val( eventParam )
					$( '.fca-pc-input-parameter-value' ).last().val( event.parameters[eventParam] )
				}

			}

		} else {
			//CUSTOM EVENTS
			$('#fca-pc-modal-event-input').val( 'custom' ).trigger( 'change' )
			$('.fca-pc-event_name').val( event.event )

			//SET PARAMS
			for ( var eventParam in event.parameters ) {
				$( '#fca-pc-add-custom-param' ).trigger('click') 
				$( '.fca-pc-input-parameter-name' ).last().val( eventParam )
				$( '.fca-pc-input-parameter-value' ).last().val( event.parameters[eventParam] )
			}
		}

		var delay = event.hasOwnProperty('delay') ? event.delay : 0
		$('#fca-pc-modal-delay-input').val( delay )

		var scroll = event.hasOwnProperty('scroll') ? event.scroll : 0
		$('#fca-pc-modal-scroll-input').val( scroll )

		//LOAD TRIGGERS

		$('#fca-pc-modal-trigger-type-input').val( event.triggerType ).trigger( 'change' )
		switch ( event.triggerType ) {

			case 'post':
				$('#fca-pc-modal-post-trigger-input').children().each(function(){
					if ( event.trigger.indexOf( $(this).val() ) !== -1 ) {
						$(this).prop('selected', true)
					} else {
						$(this).prop('selected', false)
					}
				}).trigger( 'change' )
				break
			case 'css':
				$('#fca-pc-modal-css-trigger-input').val( event.trigger )
				break

			case 'hover':
				$('#fca-pc-modal-css-trigger-input').val( event.trigger )
				break

			case 'url':
				$('#fca-pc-modal-url-trigger-input').val( event.trigger )
				break
				
			case 'exact_url':
				$('#fca-pc-modal-exact_url-trigger-input').val( event.trigger )
				break
		}
		
		var event_pixel_type = event.hasOwnProperty('pixel_type') ? event.pixel_type : 'Facebook'
		$('#fca-pc-modal-event-pixel-type').val( event_pixel_type )
		$('#fca-pc-event-pixel-type-span').text( event_pixel_type )
		fca_pc_filter_events( event_pixel_type )
	}
	
	//GUID Generation ( http://stackoverflow.com/questions/105034/create-guid-uuid-in-javascript/21963136#21963136 )
	var fca_pc_hash_seed = []
	for (var i=0; i<256; i++) {
		fca_pc_hash_seed[i] = (i<16?'0':'')+(i).toString(16)
	}
	function fca_pc_new_GUID() {
		var d0 = Math.random()*0x100000000>>>0
		var d1 = Math.random()*0x100000000>>>0
		var d2 = Math.random()*0x100000000>>>0
		var d3 = Math.random()*0x100000000>>>0

		return fca_pc_hash_seed[d0&0xff]+fca_pc_hash_seed[d0>>8&0xff]+fca_pc_hash_seed[d0>>16&0xff]+fca_pc_hash_seed[d0>>24&0xff]+'-'+
		fca_pc_hash_seed[d1&0xff]+fca_pc_hash_seed[d1>>8&0xff]+'-'+fca_pc_hash_seed[d1>>16&0x0f|0x40]+fca_pc_hash_seed[d1>>24&0xff]+'-'+
		fca_pc_hash_seed[d2&0x3f|0x80]+fca_pc_hash_seed[d2>>8&0xff]+'-'+fca_pc_hash_seed[d2>>16&0xff]+fca_pc_hash_seed[d2>>24&0xff]+
		fca_pc_hash_seed[d3&0xff]+fca_pc_hash_seed[d3>>8&0xff]+fca_pc_hash_seed[d3>>16&0xff]+fca_pc_hash_seed[d3>>24&0xff]
	}
	
	function fca_pc_get_active_pixel_types() {
		var pixel_types = []
		
		$('.fca-pc-pixel-json').each(function(){
			var pixel = JSON.parse( $(this).val() )
			pixel_types.push( pixel.type )
		})
		
		return pixel_types
	}
})
