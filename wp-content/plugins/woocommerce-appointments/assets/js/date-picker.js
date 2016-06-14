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
	
	var startDate,
		endDate,
		duration = wc_appointment_form_params.appointment_duration,
		days_needed = ( duration < 1 ) ? 1 : duration,
		days_highlighted = days_needed,
		wc_appointments_date_picker = {
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
		select_date_trigger: function( date ) {
			var fieldset		= $( this ).closest( 'fieldset' ),
				parsed_date		= date.split( '-' ),
				year  			= parseInt( parsed_date[0], 10 ),
				month 			= parseInt( parsed_date[1], 10 ),
				day   			= parseInt( parsed_date[2], 10 );
			
			//* Full appointment duration length
            startDate = new Date( year, month - 1, day );
            endDate = new Date( year, month - 1, day + ( parseInt( days_highlighted, 10 ) - 1 ) );
			
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

			var form  = $picker.closest( 'form' ),
				year  = parseInt( form.find( 'input.appointment_date_year' ).val(), 10 ),
				month = parseInt( form.find( 'input.appointment_date_month' ).val(), 10 ),
				day   = parseInt( form.find( 'input.appointment_date_day' ).val(), 10 );

			if ( year && month && day ) {
				var date = new Date( year, month - 1, day );
				$picker.datepicker( 'setDate', date );
			}
		},
		is_appointable: function( date ) {
			var $form                      = $( this ).closest('form'),
				availability               = $( this ).data( 'availability' ),
				default_availability       = $( this ).data( 'default-availability' ),
				fully_scheduled_days       = $( this ).data( 'fully-scheduled-days' ),
				partially_scheduled_days   = $( this ).data( 'partially-scheduled-days' ),
				remaining_scheduled_days   = $( this ).data( 'remaining-scheduled-days' ),
				padding_days               = $( this ).data( 'padding-days' ),
				discounted_days            = $( this ).data( 'discounted-days' ),
				availability_span		   = wc_appointment_form_params.availability_span,
				has_staff                  = wc_appointment_form_params.has_staff,
				staff_assignment           = wc_appointment_form_params.staff_assignment,
				staff_id 				   = 0,
				css_classes                = '',
				title                      = '',
				discounted_title           = '';

			// Get selected staff
			if ( $form.find('select#wc_appointments_field_staff').val() > 0 ) {
				staff_id = $form.find('select#wc_appointments_field_staff').val();
			}

			// Get days needed for slot - this affects availability
			var the_date 	= new Date( date ),
				year     	= the_date.getFullYear(),
				month    	= the_date.getMonth() + 1,
				day      	= the_date.getDate();

			// Fully scheduled?
			if ( fully_scheduled_days[ year + '-' + month + '-' + day ] ) {
				if ( fully_scheduled_days[ year + '-' + month + '-' + day ][0] || fully_scheduled_days[ year + '-' + month + '-' + day ][ staff_id ] ) {
					return [ false, 'fully_scheduled', wc_appointment_form_params.i18n_date_fully_scheduled ];
				}
			}
			
			// Padding days?
			if ( 'undefined' !== typeof padding_days && padding_days[ year + '-' + month + '-' + day ] ) {
				return [ false, 'not_appointable', wc_appointment_form_params.i18n_date_unavailable ];
			}

			if ( '' + year + month + day < wc_appointment_form_params.current_time ) {
				return [ false, 'not_appointable', wc_appointment_form_params.i18n_date_unavailable ];
			}

			// Apply Partially scheduled class.
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
			
			//* Discounted day?
			if ( 'undefined' !== typeof discounted_days && discounted_days[ year + '-' + month + '-' + day ] ) {
				css_classes = css_classes + ' discounted_day';
				discounted_title = discounted_title + discounted_days[ year + '-' + month + '-' + day ];
			}
			
			if ( availability_span === 'start' ) {
				days_needed = 1;
			}
			
			var slot_args = {
				start_date				: date,
				number_of_days			: days_needed,
				fully_scheduled_days	: fully_scheduled_days,
				availability			: availability,
				default_availability	: default_availability,
				has_staff				: has_staff,
				staff_id				: staff_id,
				staff_assignment		: staff_assignment
			};
			
			var appointable = wc_appointments_date_picker.is_slot_appointable( slot_args );
			
			if ( ! appointable ) {
				return [ appointable, css_classes + ' not_appointable', wc_appointment_form_params.i18n_date_unavailable ];
			} else {
				if ( css_classes.indexOf( 'partial_scheduled' ) > -1 ) {
					title = wc_appointment_form_params.i18n_date_partially_scheduled;
				} else if ( css_classes.indexOf( 'discounted_day' ) > -1 ) {
					title = discounted_title;
				} else {
					title = wc_appointment_form_params.i18n_date_available;
				}
				return [ appointable, css_classes + ' appointable', title ];
			}
		},
		is_slot_appointable: function( args ) {
			var appointable = args.default_availability;

			// Loop all the days we need to check for this slot.
			for ( var i = 0; i < args.number_of_days; i++ ) {
				var the_date     = new Date( args.start_date );
				the_date.setDate( the_date.getDate() + i );

				var year        = the_date.getFullYear(),
					month       = the_date.getMonth() + 1,
					day         = the_date.getDate(),
					day_of_week = the_date.getDay();

				// Sunday is 0, Monday is 1, and so on.
				if ( day_of_week === 0 ) {
					day_of_week = 7;
				}

				// Is staff available in current date?
				// Note: staff_id = 0 is product's availability rules.
				// Each staff rules also contains product's rules.
				var staff_args = {
					staff_rules: args.availability[ args.staff_id ],
					date: the_date,
					default_availability: args.default_availability
				};
				appointable = wc_appointments_date_picker.is_staff_available( staff_args );

				// In case of automatic assignment we want to make sure at least one staff is available.
				if ( 'automatic' === args.staff_assignment || ( args.has_staff && 0 === args.staff_id ) ) {
					var automatic_staff_args = $.extend(
						{
							availability: args.availability,
							fully_scheduled_days: args.fully_scheduled_days
						},
						staff_args
					);

					appointable = wc_appointments_date_picker.has_available_staff( automatic_staff_args );
				}

				// Fully scheduled in entire slot?
				if ( args.fully_scheduled_days[ year + '-' + month + '-' + day ] ) {
					if ( args.fully_scheduled_days[ year + '-' + month + '-' + day ][0] || args.fully_scheduled_days[ year + '-' + month + '-' + day ][ args.staff_id ] ) {
						appointable = false;
					}
				}

				if ( ! appointable ) {
					break;
				}
			}

			return appointable;
		},
		is_staff_available: function( args ) {
			var availability = args.default_availability,
				year         = args.date.getFullYear(),
				month        = args.date.getMonth() + 1,
				day          = args.date.getDate(),
				day_of_week  = args.date.getDay(),
				week         = $.datepicker.iso8601Week( args.date );

			// Sunday is 0, Monday is 1, and so on.
			if ( day_of_week === 0 ) {
				day_of_week = 7;
			}

			// `args.fully_scheduled_days` and `args.staff_id` only available
			// when checking 'automatic' staff assignment.
			if ( args.fully_scheduled_days && args.fully_scheduled_days[ year + '-' + month + '-' + day ] && args.fully_scheduled_days[ year + '-' + month + '-' + day ][ args.staff_id ] ) {
				return false;
			}

			$.each( args.staff_rules, function( index, rule ) {
				var type  = rule[0];
				var rules = rule[1];
				try {
					switch ( type ) {
						case 'months':
							if ( typeof rules[ month ] !== 'undefined' ) {
								availability = rules[ month ];
								return false;
							}
						break;
						case 'weeks':
							if ( typeof rules[ week ] !== 'undefined' ) {
								availability = rules[ week ];
								return false;
							}
						break;
						case 'days':
							if ( typeof rules[ day_of_week ] !== 'undefined' ) {
								availability = rules[ day_of_week ];
								return false;
							}
						break;
						case 'custom':
							if ( typeof rules[ year ][ month ][ day ] !== 'undefined' ) {
								availability = rules[ year ][ month ][ day ];
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
							if ( false === args.default_availability && ( day_of_week === rules.day || 0 === rules.day ) ) {
								availability = rules.rule;
								return false;
							}
						break;
						case 'time:range':
							if ( false === args.default_availability && ( typeof rules[ year ][ month ][ day ] !== 'undefined' ) ) {
								availability = rules[ year ][ month ][ day ].rule;
								return false;
							}
						break;
						*/
					}

				} catch( err ) {}

				return true;
			});

			return availability;
		},
		has_available_staff: function( args ) {
			for ( var staff_id in args.availability ) {
				staff_id = parseInt( staff_id, 10 );

				// Skip staff_id '0' that has been performed before.
				if ( 0 === staff_id ) {
					continue;
				}

				args.staff_rules = args.availability[ staff_id ];
				args.staff_id = staff_id;
				if ( wc_appointments_date_picker.is_staff_available( args ) ) {
					return true;
				}
			}

			return false;
		}
	};

	wc_appointments_date_picker.init();
});