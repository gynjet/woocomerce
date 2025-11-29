if (typeof TrustindexJsLoaded === 'undefined') {
	var TrustindexJsLoaded = {};
}

TrustindexJsLoaded.connect = true;

// autocomplete config
var TrustindexConnect = null;
jQuery(document).ready(function($) {
	/*************************************************************************/
	/* NO REG MODE */
	TrustindexConnect = {
		button: $('.ti-connect-platform .ti-btn'),
		form: $('#ti-connect-platform-form'),
		asyncRequest: function(callback, btn) {
			// get url params
			let params = new URLSearchParams({
				type: 'google',
				page_id: $('#ti-noreg-page-id').val().trim(),
				access_token: $('#ti-noreg-access-token').length ? $('#ti-noreg-access-token').val() : "",
				webhook_url: $('#ti-noreg-webhook-url').val(),
				email: $('#ti-noreg-email').val(),
				token: $('#ti-noreg-connect-token').val(),
				version: $('#ti-noreg-version').val()
			});

			// open window
			let tiWindow = window.open('https://admin.trustindex.io/source/wordpressPageRequest?' + params.toString(), 'trustindex', 'width=850,height=850,menubar=0' + popupCenter(850, 850));

			// wait for process complete
			window.addEventListener('message', function(event) {
				if (event.origin.startsWith('https://admin.trustindex.io/'.replace(/\/$/,'')) && event.data.success) {
					tiWindow.close();

					callback($('#ti-noreg-connect-token').val(), event.data.request_id, (event.data.manual_download | 0), event.data.place || null);
				}
			});

			// show popup info
			$('#ti-connect-info').removeClass('ti-d-none');
			let timer = setInterval(function() {
				if (tiWindow.closed) {
					$('#ti-connect-info').addClass('ti-d-none');

					if (!dontRemoveLoading) {
						button.removeClass('ti-btn-loading');
					}

					clearInterval(timer);
				}
			}, 1000);
		}
	};

	
		$('.btn-connect-public').click(function(event) {
			event.preventDefault();

			let button = $(this);
			let token = $('#ti-noreg-connect-token').val();

			button.addClass('ti-btn-loading').blur();

			let dontRemoveLoading = false;

			// get url params
			let params = new URLSearchParams({
				type: 'Google',
				referrer: 'public',
				webhook_url: $('#ti-noreg-webhook-url').val(),
				token: token,
				version: $('#ti-noreg-version').val()
			});

			let tiWindow = window.open('https://admin.trustindex.io/source/edit2?' + params.toString(), 'trustindex', 'width=850,height=850,menubar=0' + popupCenter(850, 850));

			window.addEventListener('message', function(event) {
				if (event.origin.startsWith('https://admin.trustindex.io/'.replace(/\/$/,'')) && event.data.id) {
					dontRemoveLoading = true;

					tiWindow.close();
					$('#ti-connect-info').removeClass('ti-d-none');

					$('#ti-noreg-page-details').val(JSON.stringify(event.data));

					button.closest('form').submit();
				}
			});

			$('#ti-connect-info').removeClass('ti-d-none');
			let timer = setInterval(function() {
				if (tiWindow.closed) {
					$('#ti-connect-info').addClass('ti-d-none');

					if (!dontRemoveLoading) {
						button.removeClass('ti-btn-loading');
					}

					clearInterval(timer);
				}
			}, 1000);
		});

	
		// try reply again
		jQuery(document).on('click', '.btn-try-reply-again', function(event) {
			event.preventDefault();

			let btn = jQuery(this);
			let replyBox = btn.closest('td').find('.ti-reply-box');

			replyBox.attr('data-state', btn.data('state'));
			replyBox.find('.state-'+ btn.data('state') +' .btn-post-reply').attr('data-reconnect', 1).trigger('click');
		});

	// make async request on review download
	$('.btn-download-reviews').on('click', function(event) {
		event.preventDefault();

		let btn = jQuery(this);

		TrustindexConnect.asyncRequest(function(token, request_id, manual_download, place) {
			if (place) {
				$.ajax({
					type: 'POST',
					data: {
						_wpnonce: btn.data('nonce'),
						download_data: JSON.stringify(place)
					}
				}).always(() => location.reload());
			}
			else {
				$.ajax({
					type: 'POST',
					data: {
						_wpnonce: btn.data('nonce'),
						review_download_request: token,
						review_download_request_id: request_id,
						manual_download: manual_download
					}
				}).always(() => location.reload());
			}
		}, btn);
	});

	// manual download
	$('#ti-review-manual-download').on('click', function(event) {
		event.preventDefault();

		let btn = $(this);
		btn.addClass('ti-btn-loading').blur();

		$.ajax({
			url: location.search.replace(/&tab=[^&]+/, '&tab=free-widget-configurator'),
			type: 'POST',
			data: {
				command: 'review-manual-download',
				_wpnonce: btn.data('nonce')
			},
			success: () => location.reload(),
			error: function() {
				btn.removeClass('ti-btn-loading');

				btn.removeClass('ti-toggle-tooltip').addClass('ti-show-tooltip');
				setTimeout(() => btn.removeClass('ti-show-tooltip').addClass('ti-toggle-tooltip'), 3000);
			}
		});
	});
});