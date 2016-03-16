/* globals wc_appointment_form_params */
jQuery(document).ready(function($) {

	/*
	if ( ! window.console ) {
		window.console = {
			log : function(str) {
				alert(str);
			}
		};
	}
	*/

	var xhr = [];

	$( '.wc-appointments-appointment-form' ).parent()
		.on( 'change', 'input, select', function() {
			var name  = $(this).attr( 'name' );

			var $fieldset = $(this).closest( 'fieldset' );
			var $picker   = $fieldset.find( '.picker:eq(0)' );
			if ( $picker.data( 'is_range_picker_enabled' ) ) {
				if ( 'wc_appointments_field_duration' !== name ) {
					return;
				}
			}
			
			var index = $('.wc-appointments-appointment-form').index(this);

			if ( xhr[index] ) {
				xhr[index].abort();
			}

			var $form = $(this).closest('form');

			var required_fields = $form.find('input.required_for_calculation');
			var filled          = true;
			$.each( required_fields, function( index, field ) {
				var value = $(field).val();
				if ( ! value ) {
					filled = false;
				}
			});
			if ( ! filled ) {
				$form.find('.wc-appointments-appointment-cost').hide();
				$form.find('.wc-appointments-appointment-hook').hide();
				return;
			}

			$form.find('.wc-appointments-appointment-cost').block({message: null, overlayCSS: {background: '#fff', backgroundSize: '16px 16px', opacity: 0.6}}).show();

			xhr[index] = $.ajax({
				type: 'POST',
				url: wc_appointment_form_params.ajax_url,
				data: {
					action: 'wc_appointments_calculate_costs',
					form: $form.serialize()
				},
				success: 	function( code ) {
					if ( code.charAt(0) !== '{' ) {
						// console.log( code );
						code = '{' + code.split(/\{(.+)?/)[1];
					}

					var result = $.parseJSON( code );

					if ( result.result === 'ERROR' ) {
						$form.find('.wc-appointments-appointment-cost').html( result.html );
						$form.find('.wc-appointments-appointment-cost').unblock();
						$form.find('.single_add_to_cart_button').addClass('disabled');
					} else if ( result.result === 'SUCCESS' ) {
						$form.find('.wc-appointments-appointment-cost').html( result.html );
						$form.find('.wc-appointments-appointment-cost').unblock();
						$form.find('.wc-appointments-appointment-hook').show();
						$form.find('.single_add_to_cart_button').removeClass('disabled');
					} else {
						$form.find('.wc-appointments-appointment-cost').hide();
						$form.find('.wc-appointments-appointment-hook').hide();
						$form.find('.single_add_to_cart_button').addClass('disabled');
						// console.log( code );
					}
				},
				error: function() {
					$form.find('.wc-appointments-appointment-cost').hide();
					$form.find('.single_add_to_cart_button').addClass('disabled');
				},
				dataType: 'html'
			});
		})
		.each(function(){
			var button = $(this).closest('form').find('.single_add_to_cart_button');

			button.addClass('disabled');
		});

	$( '.single_add_to_cart_button' ).on( 'click', function( event ) {
		if ( $(this).hasClass( 'disabled' ) ) {
			alert( wc_appointment_form_params.i18n_choose_options );
			event.preventDefault();
			return false;
		}
	})

	$('.wc-appointments-appointment-form, .wc-appointments-appointment-form-button').show().removeAttr( 'disabled' );

});