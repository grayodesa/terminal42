/* global wc_appointments_writepanel_js_params, alert, confirm */
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
	
	var wc_appointments_writepanel = {
		init: function() {
			$( '#appointments_availability, #appointments_pricing' ).on( 'change', '.wc_appointment_availability_type select, .wc_appointment_pricing_type select', this.wc_appointments_table_grid );
			$( 'body' ).on( 'row_added', this.wc_appointments_row_added );
			$( 'body' ).on( 'woocommerce-product-type-change', this.wc_appointments_trigger_change_events );
			$( 'input#_virtual' ).on( 'change', this.wc_appointments_trigger_change_events );			
			$( '#_wc_appointment_user_can_cancel' ).on( 'change', this.wc_appointments_user_cancel );
			$( '#_wc_appointment_has_price_label' ).on( 'change', this.wc_appointments_price_label );
			$( '#_wc_appointment_has_pricing' ).on( 'change', this.wc_appointments_pricing );
			$( '#_wc_appointment_staff_assignment' ).on( 'change', this.wc_appointments_staff_assignment );
			$( '#_wc_appointment_duration_unit' ).on( 'change', this.wc_appointment_duration_unit );
			$( 'body' ).on( 'click', '.add_row', this.wc_appointments_table_grid_add_row );
			$( 'body' ).on( 'click', 'td.remove', this.wc_appointments_table_grid_remove_row );
			$( '#appointments_staff' ).on( 'click', 'button.add_staff', this.wc_appointments_add_staff );
			$( '#appointments_staff' ).on( 'click', 'button.remove_appointment_staff', this.wc_appointments_remove_staff );
			
			wc_appointments_writepanel.wc_appointments_trigger_change_events();
			
			$( '#availability_rows, #pricing_rows' ).sortable({
				items: 'tr',
				cursor: 'move',
				axis: 'y',
				handle: '.sort',
				scrollSensitivity:40,
				forcePlaceholderSize: true,
				helper: 'clone',
				opacity: 0.65,
				placeholder: {
					element: function() {
						return $( '<tr class="wc-metabox-sortable-placeholder"><td colspan=99>&nbsp;</td></tr>' )[0];
					},
					update: function() {}
				},
				start: function(event,ui){
					ui.item.css('background-color','#f6f6f6');
				},
				stop: function(event,ui){
					ui.item.removeAttr('style');
				}
			});

			$( '.date-picker' ).datepicker({
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showButtonPanel: true,
				showOn: 'button',
				firstDay: wc_appointments_writepanel_js_params.firstDay,
				buttonText: '<span class="dashicons dashicons-calendar-alt"></span>'
			});

			$( '.woocommerce_appointable_staff' ).sortable({
				items: '.woocommerce_appointment_staff',
				cursor: 'move',
				axis: 'y',
				handle: 'h3',
				scrollSensitivity: 40,
				forcePlaceholderSize: true,
				helper: 'clone',
				opacity: 0.65,
				placeholder: 'wc-metabox-sortable-placeholder',
				start: function( event, ui ) {
					ui.item.css( 'background-color', '#f6f6f6' );
				},
				stop: function ( event, ui ) {
					ui.item.removeAttr( 'style' );
					wc_appointments_writepanel.staff_row_indexes();
				}
			});
				
		},
		wc_appointments_table_grid: function() {
			var value = $(this).val();
			var row   = $(this).closest('tr');

			$(row).find( '.from_date, .from_day_of_week, .from_month, .from_week, .from_time, .from' ).hide();
			$(row).find( '.to_date, .to_day_of_week, .to_month, .to_week, .to_time, .to, .on_date' ).hide();
			$( '.repeating-label' ).hide();
			$(row).find( '.appointments-datetime-select-to' ).removeClass( 'appointments-datetime-select-both' );
			$(row).find( '.appointments-datetime-select-from' ).removeClass( 'appointments-datetime-select-both' );

			if ( value === 'custom' ) {
				$(row).find('.from_date, .to_date').show();
			}
			if ( value === 'time_date' ) {
				$(row).find('.on_date').show();
				$(row).find('.on_date_empty').hide();
			}
			if ( value === 'months' ) {
				$(row).find('.from_month, .to_month').show();
			}
			if ( value === 'weeks' ) {
				$(row).find('.from_week, .to_week').show();
			}
			if ( value === 'days' ) {
				$(row).find('.from_day_of_week, .to_day_of_week').show();
			}
			if ( value.match( '^time' ) ) {
				$(row).find('.from_time, .to_time').show();
				//* Show the date range as well if "time range for custom dates" is selected
				if ( 'time:range' === value ) {
					$(row).find('.from_date, .to_date').show();
					$( '.repeating-label' ).show();
					$(row).find( '.appointments-datetime-select-to' ).addClass( 'appointments-datetime-select-both' );
					$(row).find( '.appointments-datetime-select-from' ).addClass( 'appointments-datetime-select-both' );
				}
			}
			if ( value === 'duration' || value === 'slots' ) {
				$(row).find('.from, .to').show();
			}
			return false;
		},
		wc_appointments_row_added: function() {
			$( '.wc_appointment_availability_type select, .wc_appointment_pricing_type select' ).change();
			$( '.date-picker' ).datepicker({
				dateFormat: 'yy-mm-dd',
				numberOfMonths: 1,
				showButtonPanel: true,
				showOn: 'button',
				firstDay: wc_appointments_writepanel_js_params.firstDay,
				buttonText: '<span class="dashicons dashicons-calendar-alt"></span>'
			});
			return false;
		},
		wc_appointments_trigger_change_events: function() {
			$( '.wc_appointment_availability_type select, .wc_appointment_pricing_type select, #_wc_appointment_user_can_cancel, #_wc_appointment_has_price_label, #_wc_appointment_has_pricing, #_wc_appointment_duration_unit, #_wc_appointment_staff_assignment' ).change();
			return false;
		},
		wc_appointments_user_cancel: function() {
			if ( $(this).is( ':checked' ) ) {
				$( '.form-field.appointment-cancel-limit' ).show();
			} else {
				$( '.form-field.appointment-cancel-limit' ).hide();
			}
			return false;
		},
		wc_appointments_price_label: function() {
			if ( $(this).is( ':checked' ) ) {
				$( '.options_group.pricing' ).show();
				$( '.form-field._wc_appointment_price_label_field' ).show();
			} else {
				$( '.options_group.pricing' ).show();
				$( '.form-field._wc_appointment_price_label_field' ).hide();
			}
			return false;
		},
		wc_appointments_pricing: function() {
			if ( $(this).is( ':checked' ) ) {
				$( '#appointments_pricing' ).show();
			} else {
				$( '#appointments_pricing' ).hide();
			}
			return false;
		},
		wc_appointments_staff_assignment: function() {
			if ( $(this).val() === 'customer' ) {
				$( '.form-field._wc_appointment_staff_label_field' ).show();
			} else {
				$( '.form-field._wc_appointment_staff_label_field' ).hide();
			}
			return false;
		},
		wc_appointment_duration_unit: function() {
			if ( $(this).val() === 'day' ) {
				$( '.form-field._wc_appointment_padding_duration_wrap' ).hide();
				$( '.form-field._wc_appointment_interval_duration_wrap' ).hide();
			} else {
				$( '.form-field._wc_appointment_padding_duration_wrap' ).show();
				$( '.form-field._wc_appointment_interval_duration_wrap' ).show();
			}
			return false;
		},
		wc_appointments_table_grid_add_row: function() {
			$(this).closest('table').find('tbody').append( $( this ).data( 'row' ) );
			$('body').trigger('row_added');
			return false;
		},
		wc_appointments_table_grid_remove_row: function() {
			$(this).closest('tr').remove();
			return false;
		},
		wc_appointments_add_staff: function() {
			var loop           = jQuery( '.woocommerce_appointment_staff' ).length;
			var add_staff_id   = jQuery( 'select.add_staff_id' ).val();
			var add_staff_name = '';
			
			jQuery( '.woocommerce_appointable_staff' ).block({ message: null });

			var data = {
				action:            'woocommerce_add_appointable_staff',
				post_id:           wc_appointments_writepanel_js_params.post,
				loop:              loop,
				add_staff_id:      add_staff_id,
				add_staff_name:    add_staff_name,
				security:          wc_appointments_writepanel_js_params.nonce_add_staff
			};

			jQuery.post( wc_appointments_writepanel_js_params.ajax_url, data, function( response ) {
				if ( response.error ) {
					alert( response.error );
					jQuery( '.woocommerce_appointable_staff' ).unblock();
				} else {
					jQuery( '.woocommerce_appointable_staff' ).append( response.html ).unblock();
					jQuery( '.woocommerce_appointable_staff' ).sortable( 'refresh' );
					/*
					if ( add_staff_id ) {
						jQuery( '.add_staff_id' ).find( 'option[value=' + add_staff_id + ']' ).prop('disabled', true);
					}
					*/
				}
			});

			return false;
		},
		wc_appointments_remove_staff: function( element ) {
			element.preventDefault();
			var answer = confirm( wc_appointments_writepanel_js_params.i18n_remove_staff );
			if ( answer ) {

				var el      = jQuery(this).parent().parent();
				var staff 	= jQuery(this).attr('rel');

				jQuery( el ).block({ message: null });

				var data = {
					action:     'woocommerce_remove_appointable_staff',
					post_id:    wc_appointments_writepanel_js_params.post,
					staff_id: 	staff,
					security:   wc_appointments_writepanel_js_params.nonce_delete_staff
				};

				jQuery.post( wc_appointments_writepanel_js_params.ajax_url, data, function() {
					jQuery( el ).fadeOut( '300', function(){
						jQuery( el ).remove();
					});
				});
			}
			return false;
		},
		staff_row_indexes: function() {
			$( '.woocommerce_appointable_staff .woocommerce_appointment_staff' ).each( function( index, el ) {
				$( '.staff_menu_order', el ).val( parseInt( $(el).index( '.woocommerce_appointable_staff .woocommerce_appointment_staff' ), 10 ) );
			});
			return false;
		}
	};

	wc_appointments_writepanel.init();
});
