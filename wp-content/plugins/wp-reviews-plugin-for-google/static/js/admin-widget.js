jQuery(document).ready(function() {
	let wpContent = jQuery('#wpbody-content').length ? jQuery('#wpbody-content') : jQuery('body');

	// open help tooltip
	wpContent.on('click', '.btn-insert-tooltip', function(event) {
		event.preventDefault();

		jQuery(this).closest('label').after(jQuery('.block-help-template:last').clone().removeAttr('id').removeClass('block-help-template'));
	});

	// close help tooltip
	wpContent.on('click', '.dashicons-dismiss', function(event) {
		event.preventDefault();

		jQuery(this).closest('.help-block').remove();
	});

	// select Trustindex widget ID
	wpContent.on('click', '.btn-copy-widget-id', function(event) {
		event.preventDefault();

		let link = jQuery(this);
		link.closest('form').find('.form-control').val(link.data('ti-id')).trigger('change');

		TImanageCopyLinks(link.closest('form'), link);
	});

	wpContent.on('blur', '.trustindex-widget-admin .form-control', function() {
		let input = jQuery(this);

		if (input.attr('required') !== 'required' || input.val()) {
			input.prev().removeClass('text-danger');
		}
		else {
			input.prev().addClass('text-danger');
		}

		TImanageCopyLinks(input.closest('form'), input.closest('form').find('[data-ti-id="'+ input.val() +'"]'));
	});
});

function TImanageCopyLinks(form, selectedLink)
{
	let selectedClass = 'text-danger';

	// reset
	form.find('.btn-copy-widget-id.' + selectedClass).each(function(i, item) {
		jQuery(item).removeClass(selectedClass).find('.dashicons').attr('class', 'dashicons dashicons-admin-post');
	});

	// select
	if (selectedLink) {
		selectedLink.addClass(selectedClass).find('.dashicons').attr('class', 'dashicons dashicons-yes');
	}
}