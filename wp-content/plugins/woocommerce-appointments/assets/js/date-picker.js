/* globals wc_appointment_form_params, wc_appointment_form_params */
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
	
	var startDate;
    var endDate;
	var duration = wc_appointment_form_params.appointment_duration;
	var days_needed = ( duration < 1 ) ? 1 : duration;
	var days_highlighted = days_needed;
	var wc_appointments_date_picker = {
		init: function() {
			$( 'body' ).on( 'change', '#wc_appointments_field_staff', this.date_picker_init );
			$( '.wc-appointments-date-picker legend small.wc-appointments-date-picker-choose-date' ).show();
			$( '.wc-appointments-date-picker' ).each( function() {
				var form     = $( this ).closest( 'form' ),
				    picker   = form.find( '.picker' ),
					fieldset = $( this ).closest( 'fieldset' );

				wc_appointments_date_picker.date_picker_init( picker );

				$( '.wc-appointments-date-picker-date-fields', fieldset ).hide();
				$( '.wc-appointments-date-picker-choose-date', fieldset ).hide();
			} );
		},
		select_date_trigger: function( date, inst ) {
			var fieldset		= $( this ).closest( 'fieldset' );
			var form			= $( this ).closest( 'form' );
			var parsed_date		= date.split( '-' );
			var the_date		= new Date( date );
			var year			= the_date.getFullYear();
			var month			= the_date.getMonth();
			var day				= the_date.getDate();
			
			//* Full appointment duration length
            startDate = new Date( year, month, day );
            endDate = new Date( year, month, day - 1 + parseInt( days_highlighted ) );

			// Set fields
			fieldset.find( 'input.appointment_to_date_year' ).val( '' );
			fieldset.find( 'input.appointment_to_date_month' ).val( '' );
			fieldset.find( 'input.appointment_to_date_day' ).val( '' );

			fieldset.find( 'input.appointment_date_year' ).val( parsed_date[0] );
			fieldset.find( 'input.appointment_date_month' ).val( parsed_date[1] );
			fieldset.find( 'input.appointment_date_day' ).val( parsed_date[2] ).change();
			
			fieldset.triggerHandler( 'date-selected', date );
		},
		date_picker_init: function( element ) {
			var $picker = $( element );
			if ( $( element ).is( '.picker' ) ) {
				$picker = $( element );
			} else {
				$picker = $( this ).closest('form').find( '.picker:eq(0)' );
			}

			$picker.empty().removeClass('hasDatepicker').datepicker({
				dateFormat: $.datepicker.ISO_8601,
				showWeek: false,
				showOn: false,
				beforeShowDay: wc_appointments_date_picker.is_appointable,
				onSelect: wc_appointments_date_picker.select_date_trigger,
				minDate: $picker.data( 'min_date' ),
				maxDate: $picker.data( 'max_date' ),
				defaultDate: $picker.data( 'default_date'),
				numberOfMonths: 1,
				showButtonPanel: false,
				showOtherMonths: false,
				selectOtherMonths: false,
				closeText: wc_appointment_form_params.closeText,
				currentText: wc_appointment_form_params.currentText,
				prevText: wc_appointment_form_params.prevText,
				nextText: wc_appointment_form_params.nextText,
				monthNames: wc_appointment_form_params.monthNames,
				monthNamesShort: wc_appointment_form_params.monthNamesShort,
				dayNames: wc_appointment_form_params.dayNames,
				/* dayNamesShort: wc_appointment_form_params.dayNamesShort, */
				dayNamesMin: wc_appointment_form_params.dayNamesShort,
				firstDay: wc_appointment_form_params.firstDay,
				gotoCurrent: true
			});

			$( '.ui-datepicker-current-day' ).removeClass( 'ui-datepicker-current-day' );

			var form  = $picker.closest( 'form' );
			var year  = parseInt( form.find( 'input.appointment_date_year' ).val(), 10 );
			var month = parseInt( form.find( 'input.appointment_date_month' ).val(), 10 );
			var day   = parseInt( form.find( 'input.appointment_date_day' ).val(), 10 );

			if ( year && month && day ) {
				var date = new Date( year, month - 1, day );
				$picker.datepicker( 'setDate', date );
			}
		},
		is_appointable: function( date ) {
			var $form                      = $( this ).closest('form');
			var availability               = $( this ).data( 'availability' );
			var default_availability       = $( this ).data( 'default-availability' );
			var fully_scheduled_days       = $( this ).data( 'fully-scheduled-days' );
			var partially_scheduled_days   = $( this ).data( 'partially-scheduled-days' );
			var remaining_scheduled_days   = $( this ).data( 'remaining-scheduled-days' );
			var availability_span		   = wc_appointment_form_params.availability_span;
			var css_classes                = '';
			var staff_id 				   = 0;
			var title                      = '';

			// Get selected staff
			if ( $form.find('select#wc_appointments_field_staff').val() > 0 ) {
				staff_id = $form.find('select#wc_appointments_field_staff').val();
			} else {
				staff_id = 0;
			}

			// Get days needed for slot - this affects availability
			var the_date 	= new Date( date );
			var year     	= the_date.getFullYear();
			var month    	= the_date.getMonth() + 1;
			var day      	= the_date.getDate();
			var day_of_week	= the_date.getDay();
			var week        = $.datepicker.iso8601Week( the_date );

			// Fully scheduled?
			if ( fully_scheduled_days[ year + '-' + month + '-' + day ] ) {
				if ( fully_scheduled_days[ year + '-' + month + '-' + day ][0] || fully_scheduled_days[ year + '-' + month + '-' + day ][ staff_id ] ) {
					return [ false, 'fully_scheduled', wc_appointment_form_params.i18n_date_fully_scheduled ];
				}
			}

			if ( '' + year + month + day < wc_appointment_form_params.current_time ) {
				return [ false, 'not_appointable', wc_appointment_form_params.i18n_date_unavailable ];
			}

			// Partially scheduled?
			if ( partially_scheduled_days && partially_scheduled_days[ year + '-' + month + '-' + day ] ) {
				if ( partially_scheduled_days[ year + '-' + month + '-' + day ][0] || partially_scheduled_days[ year + '-' + month + '-' + day ][ staff_id ] ) {
					css_classes = css_classes + 'partial_scheduled ';
				}
				// Percentage remaining for scheduling
				if ( remaining_scheduled_days[ year + '-' + month + '-' + day ][0] ) {
					css_classes = css_classes + 'remaining_scheduled_' + remaining_scheduled_days[ year + '-' + month + '-' + day ][0] + ' ';
				}
				else if ( remaining_scheduled_days[ year + '-' + month + '-' + day ][ staff_id ] ) {
					css_classes = css_classes + 'remaining_scheduled_' + remaining_scheduled_days[ year + '-' + month + '-' + day ][ staff_id ] + ' ';
				}
			}
			
			//* Select all days, when duration is longer than 1 day
            if ( date >= startDate && date <= endDate ) {
				css_classes = 'ui-datepicker-selected-day';
			}
			
			if ( availability_span == 'start' ) {
				days_needed = 1;
			}
			
			var appointable = default_availability;

			// Loop all the days we need to check for this slot
			for ( var i = 0; i < days_needed; i++ ) {
				the_date     	= new Date( date );
				the_date.setDate( the_date.getDate() + i );

				year        = the_date.getFullYear();
				month       = the_date.getMonth() + 1;
				month_zero  = ("0" + (the_date.getMonth() + 1)).slice(-2);
				day         = the_date.getDate(); 
				day_zero    = ("0" + the_date.getDate()).slice(-2);
				day_of_week = the_date.getDay();
				week        = $.datepicker.iso8601Week( the_date );
				day_format 	= year + '-' + month_zero + '-' + day_zero;

				// Reset appointable for each day being checked
				appointable = default_availability;

				// Sunday is 0, Monday is 1, and so on.
				if ( day_of_week === 0 ) {
					day_of_week = 7;
				}

				$.each( availability[ staff_id ], function( index, rule ) {
					var type  = rule[0];
					var rules = rule[1];
					try {
						switch ( type ) {
							case 'months':
								if ( typeof rules[ month ] !== 'undefined' ) {
									appointable = rules[ month ];
									return false;
								}
							break;
							case 'weeks':
								if ( typeof rules[ week ] !== 'undefined' ) {
									appointable = rules[ week ];
									return false;
								}
							break;
							case 'days':
								if ( typeof rules[ day_of_week ] !== 'undefined' ) {
									appointable = rules[ day_of_week ];
									return false;
								}
							break;
							case 'custom':
								if ( typeof rules[ year ][ month ][ day ] !== 'undefined' ) {
									appointable = rules[ year ][ month ][ day ];
									return false;
								}
							break;
							case 'time_date':
								if ( rules['date'] === day_format ) {
									appointable = true;
									return false;
								}
							break;
							/*
							case 'time':
							case 'time:1':
							case 'time:2':
							case 'time:3':
							case 'time:4':
							case 'time:5':
							case 'time:6':
							case 'time:7':
								if ( false === default_availability && ( day_of_week === rules.day || 0 === rules.day ) ) {
									appointable = rules.rule;
									return false;
								}
							break;
							case 'time:range':
								if ( false === default_availability && ( typeof rules[ year ][ month ][ day ] != 'undefined' ) ) {
									appointable = rules[ year ][ month ][ day ].rule;
									return false;
								}
							break;
							*/
						}
					} catch( err ) {}

					return true;
				});

				// Fully scheduled in entire slot?
				if ( fully_scheduled_days[ year + '-' + month + '-' + day ] ) {
					if ( fully_scheduled_days[ year + '-' + month + '-' + day ][0] || fully_scheduled_days[ year + '-' + month + '-' + day ][ staff_id ] ) {
						appointable = false;
					}
				}

				if ( ! appointable ) {
					break;
				}
			}

			if ( ! appointable ) {
				return [ appointable, css_classes + ' not_appointable', wc_appointment_form_params.i18n_date_unavailable ];
			} else {
				if ( css_classes.indexOf( 'partial_scheduled' ) > -1 ) {
					title = wc_appointment_form_params.i18n_date_partially_scheduled;
				} else {
					title = wc_appointment_form_params.i18n_date_available;
				}
				
				return [ appointable, css_classes + ' appointable', title ];
			}
		}
	};

	wc_appointments_date_picker.init();
});