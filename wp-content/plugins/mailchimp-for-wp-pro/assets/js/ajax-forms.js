window.mc4wpAjaxForms = (function($) {
	'use strict';

	/**
	 * @var object The context in which a form was submitted
	 */
	var $context;

	/**
	 * @var DOMElement
	 */
	var $formWrappers;

	/**
	 * @var object
	 */
	var $ajaxForms;

	/**
	 * @var object Fallback for IE8
	 */
	var console = window.console || { log: function() {} };

	/**
	 * Initializes the MailChimp for WordPress JS functionality
	 */
	function load( tryAgain ) {

		// Query all forms in DOM
		$formWrappers = $('.mc4wp-ajax');

		// If we failed to find any forms on the first try, try again at a later hook.
		if( typeof( tryAgain ) === "boolean" && tryAgain && ! $formWrappers.length ) {
			$(document).ready(load);
			return;
		}

		// find all forms
		$ajaxForms = $formWrappers.find('form');

		// preload AJAX loader image if it is enabled
		if( mc4wp_vars.ajaxloader.enabled ) {
			var img = new Image();
			img.src = mc4wp_vars.ajaxloader.imgurl;
		}

		backwardsCompatibleEventBinds();
	}

	/**
	 * Creates an AJAX request, calls the various callbacks.
	 *
	 * @param event
	 * @returns {boolean}
	 */
	function ajaxifyForm(event) {
		// prevent regular form submit
		event.preventDefault();

		// create data string out of form fields
		var data = $(this).serialize();

		// check for filled email field
		if( typeof( this.elements['EMAIL'] ) === "object" ) {

			this.elements['EMAIL'].required = true;
			var value = this.elements['EMAIL'].value;

			// do nothing if EMAIl field is empty
			if( value.trim() === '' ) {
				return false;
			}
		}

		beforeSubmit(data, $(this));

		// build the request object
		$.ajax({
			type: "POST",
			url: mc4wp_vars.ajaxurl,
			success: onAjaxSuccess,
			error: onAjaxError,
			data: data,
			dataType: 'json'
		});

		return false;
	}

	/**
	 * Runs before an AJAX form is submitted
	 *
	 * Triggers: submit.mc4wp
	 *
	 * @param data
	 * @param $form
	 */
	function beforeSubmit( data, $form ) {

		var $submitButton, submitButton;

		$context = $form.parent('.mc4wp-form');

		// Remove possibly added classes
		$context.removeClass('mc4wp-form-success');
		$context.removeClass('mc4wp-form-error');

		// Hide possible errors from previous sign-up request
		$context.find('.mc4wp-alert').remove();

		// find loader and button in form mark-up
		$submitButton = $form.find('[type="submit"], [type="image"]').last();
		submitButton = $submitButton.get(0);

		// Disable submit button to prevent double sign-up
		submitButton.disabled = true;

		// Find loading text out of data attribute
		var $loadingText = $submitButton.data('loading-text');

		// If loading text is set, store the original button text and change text
		if( $loadingText ) {
			$submitButton.data('original-text',buttonText(submitButton));
			buttonText( submitButton, $loadingText );
		} else {

			// Loading text was not set, use the AJAX loader
			// Insert loader after submit button
			var $ajaxLoader = $('<span />')
				.addClass('mc4wp-ajax-loader').css({
					'display': 'inline-block',
					'vertical-align': 	'middle',
					'height': 			'16px',
					'width': 			'16px',
					'border': 			0,
					'background': 		'url("' + mc4wp_vars.ajaxloader.imgurl + '") no-repeat center center'
			}).insertAfter($submitButton);

			// Add small left-margin if loader doesn't have one yet
			if( parseInt( $ajaxLoader.css('margin-left')) < 5 ) {
				$ajaxLoader.css('margin-left', '5px');
			}
		}

		// Event: submit.mc4wp
		$context.trigger( {
			type: "submit.mc4wp",
			formData: data
		} );
	}

	/**
	 * Get or set the text of a submit button
	 *
	 * @param buttonElement string
	 * @param newText string
	 * @returns string
	 */
	function buttonText( buttonElement, newText ) {
		var isButtonElement = (buttonElement.tagName === 'BUTTON');

		if(typeof(newText) === "string") {
			if( isButtonElement ) {
				buttonElement.innerText = newText;
			} else {
				buttonElement.value = newText;
			}
		}
		return isButtonElement ? buttonElement.innerText : buttonElement.value;
	}

	/**
	 * Runs after every successful AJAX request
	 *
	 * Triggers: ajaxSuccess.mc4wp
	 *
	 * @param response
	 */
	function onAjaxSuccess( response ) {

		var $submitButton, submitButton;

		// Event: ajaxSuccess.mc4wp
		$context.trigger( {
			type: "ajaxSuccess.mc4wp",
			response: response
		} );

		// remove ajax loaders
		$('.mc4wp-ajax-loader').remove();

		// show response
		$context.find('.mc4wp-response').html( response.data.html );

		// Find submit button
		$submitButton = $context.find('[type="submit"], [type="image"]').last();
		submitButton = $submitButton.get(0);

		// Restore button text
		var originalText = $submitButton.data('original-text');
		if( originalText ) {
			buttonText( submitButton, originalText );
		}

		// Re-enable submit button
		submitButton.disabled = false;

		// Act on response parameters
		if(response.success) {
			onSubscribeSuccess( response.data.redirect, response.data.hide_form, response.data.data );
		} else {
			onSubscribeError( response.data.error, response.data.data );
		}


	}

	/**
	 * Runs after every failed AJAX request
	 *
	 * Triggers: ajaxError.mc4wp
	 *
	 * @param response
	 */
	function onAjaxError( response ) {

		// Event: ajaxError.mc4wp
		$context.trigger( {
			type: "ajaxError.mc4wp",
			response: response
		} );

		// Just log the request to the console
		console.log(response);
	}

	/**
	 * Runs for every successful sign-up.
	 *
	 * Triggers: subscribe.mc4wp
	 *
	 * @param redirect
	 * @param hide_form
	 * @param data
	 */
	function onSubscribeSuccess( redirect, hide_form, data ) {

		// Find form element
		var $form = $context.find('form');

		// add class for successful forms
		$context.addClass('mc4wp-form-success');

		// Reset form to original state
		$form.trigger( 'reset' );

		// Redirect to the specified location
		if( redirect && redirect.url != '' ) {
			window.setTimeout(function() {
				window.location.replace( redirect.url );
			}, redirect.delay );
		}

		// Hide the form if the "hide form" option is selected
		if( hide_form ) {
			$form.get(0).style.display = 'none';
		}

		// Event: subscribe.mc4wp
		$context.trigger( {
			type: "subscribe.mc4wp",
			formData: data
		} );
	}

	/**
	 * Runs for every sign-up error
	 *
	 * Triggers: error.mc4wp
	 *
	 * @param error
	 * @param data
	 */
	function onSubscribeError( error, data ) {
		// add class for error forms
		$context.addClass('mc4wp-form-error');

		// Trigger mc4wp.error JS event to hook into
		$context.trigger({
			type: "error.mc4wp",
			formData: data,
			error: error.type
		} );
	}

	/**
	 * Bind new events to their old names w/ parameters.
	 * @since 2.6.1
	 */
	function backwardsCompatibleEventBinds() {

		var triggerOldEvent = function(event) {

			if( event.type === "subscribe" ) {
				event.type = "success";
			}
			var oldEventName = event.namespace + '.' + event.type;

			// Trigger old event on document
			jQuery(document).trigger({
				type: oldEventName,
				form: $(this),
				formData: event.formData,
				error: event.error,
				response: event.response
			});
		};

		$formWrappers.on('subscribe.mc4wp', triggerOldEvent);
		$formWrappers.on('error.mc4wp', triggerOldEvent);
	}

	// run this straight away
	load(true);

	// hook into all form submits of forms with our ajax class
	$(document).on('submit', '.mc4wp-ajax form', ajaxifyForm );

})(window.jQuery);