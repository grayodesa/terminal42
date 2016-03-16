/* globals wc_appointment_form_params, console */
jQuery( function( $ ) {

	/*
	if ( ! window.console ) {
		window.console = {
			log : function(str) {
				alert(str);
			}
		};
	}
	*/
	
	var wc_appointments_staff_picker = {
		init: function() {
			$( '#wc_appointments_field_staff' ).select2({
				escapeMarkup: function( m ) {
					return m;
				},
				
				templateResult: wc_appointments_staff_picker.templateStaff,
				templateSelection: wc_appointments_staff_picker.templateStaff
			});
		},
		templateStaff: function( state ) {
			if ( ! state.id ) {
				return state.text;
			}
			
			var html5data = state.element;
			
			if ( $( html5data ).data('avatar') ) {
				return '<img class="staff-avatar" src="' + $( html5data ).data('avatar') + '" alt="'+ state.text + '" />' + state.text; 
			}
			
			return state.text;

		}
	};

	wc_appointments_staff_picker.init();
});