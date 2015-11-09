(function($) {
	'use strict';

	/**
	 * Variables
	 */
	var $context = $('#mc4wp-admin');

	/**
	 * Functions
	 */
	function askForConfirmation() {
		return confirm( "Are you sure you want to delete this form?" );
	}

	function toggleSendWelcomeFields() {
		var $el = $(document.getElementById('mc4wp-send-welcome'));
		if($(this).val() == 0) {
			$el.removeClass('hidden').find(':input').removeAttr('disabled');
		} else {
			$el.addClass('hidden').find(':input').attr('disabled', 'disabled').prop('checked', false);
		}
	}

	function toggleWooCommerceFields() {
		$(document.getElementById('woocommerce-settings')).toggle( $(this).prop( 'checked') );
	}

	function toggleReplaceInterestsFields() {
		var $el = $(document.getElementById('mc4wp-replace-interests'));
		if($(this).val() == 1) {
			$el.removeClass('hidden').find(':input').removeAttr('disabled');
		} else {
			$el.addClass('hidden').find(':input').attr('disabled', 'disabled').attr('checked', false);
		}
	}

	function toggleCustomColorField() {
		var show = ($(this).val() === 'custom-color');
		$(document.getElementById('mc4wp-custom-color')).toggle(show);
	}

	/**
	 * Bind event handlers
	 */

	// Ask for confirmation when someone tries to delete a form
	$context.find('.forms .submitdelete').click(askForConfirmation);

	// Show send-welcome field only when double opt-in is disabled
	$context.find('input[name$="[double_optin]"]').change(toggleSendWelcomeFields);

	// Show woocommerce settings only when woocommerce checkout is checked
	$context.find('input[name$="[show_at_woocommerce_checkout]"]').change(toggleWooCommerceFields);

	// Show replace-interests field only when update existing is enabled
	$context.find('input[name$="[update_existing]"]').change(toggleReplaceInterestsFields);

	// Show custom color only only when `custom-color` is selected
	$context.find("select[name$='[css]']").change(toggleCustomColorField);

	// init color picker
	$context.find('.color-field').wpColorPicker();

})(jQuery);