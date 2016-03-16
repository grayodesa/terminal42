/* global appointment_form_params */
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
	
	var xhr;
	
	var wc_appointments_time_picker = {
		init: function() {
			$( '.slot-picker' ).on( 'click', 'a', this.time_picker_init );
			$( '#wc_appointments_field_staff' ).on( 'change', this.show_available_time_slots );
			$( '.wc-appointments-appointment-form fieldset' ).on( 'date-selected', this.show_available_time_slots );	
		},
		time_picker_init: function() {
			var value  = $(this).data('value');
			var target = $(this).parents('.form-field').find('input');

			target.val( value ).change();
			$(this).parents('.form-field').find('li').removeClass('selected');
			$(this).parents('li').addClass('selected');

			return false;
		},
		show_available_time_slots: function() {
			var cart_form		= $( this ).closest('form');
			var slot_picker     = cart_form.find('.slot-picker');
			var fieldset        = cart_form.find('fieldset');

			var year  = parseInt( fieldset.find( 'input.appointment_date_year' ).val(), 10 );
			var month = parseInt( fieldset.find( 'input.appointment_date_month' ).val(), 10 );
			var day   = parseInt( fieldset.find( 'input.appointment_date_day' ).val(), 10 );

			if ( ! year || ! month || ! day ) {
				return;
			}

			// clear slots
			slot_picker.closest('div').find('input').val( '' ).change();
			slot_picker.closest('div').block({message: null, overlayCSS: {background: '#fff', backgroundSize: '16px 16px', opacity: 0.6}}).show();

			// Get slots via ajax
			if ( xhr ) {
				xhr.abort();
			}

			xhr = $.ajax({
				type: 'POST',
				url: wc_appointment_form_params.ajax_url,
				data: {
					action: 'wc_appointments_get_slots',
					form: cart_form.serialize()
				},
				success: function( code ) {
					slot_picker.html( code );
					//this.resize_slots();
					slot_picker.closest('div').unblock();
				},
				dataType: 'html'
			});
		},
		resize_slots: function() {
			var max_width  = 0;
			var max_height = 0;

			$('.slot-picker a').each(function() {
				var width  = $(this).width();
				var height = $(this).height();
				if ( width > max_width ) {
					max_width = width;
				}
				if ( height > max_height ) {
					max_height = height;
				}
			});

			$('.slot-picker a').width( max_width );
			$('.slot-picker a').height( max_height );
		}
	};

	wc_appointments_time_picker.init();
});
