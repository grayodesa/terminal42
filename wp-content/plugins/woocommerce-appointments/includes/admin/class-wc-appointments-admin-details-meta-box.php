<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Appointments_Admin_Details_Meta_Box {
	public $id;
	public $title;
	public $context;
	public $priority;
	public $post_types;

	public function __construct() {
		$this->id = 'woocommerce-appointment-data';
		$this->title = __( 'Appointment Details', 'woocommerce-appointments' );
		$this->context = 'normal';
		$this->priority = 'high';
		$this->post_types = array( 'wc_appointment' );

		add_action( 'save_post', array( $this, 'meta_box_save' ), 10, 1 );
	}

	public function meta_box_inner( $post ) {
		wp_nonce_field( 'wc_appointments_details_meta_box', 'wc_appointments_details_meta_box_nonce' );

		// Scripts.
		if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
			wp_enqueue_script( 'ajax-chosen' );
			wp_enqueue_script( 'chosen' );
		} else {
			wp_enqueue_script( 'wc-enhanced-select' );
		}
		wp_enqueue_script( 'jquery-ui-datepicker' );

		$customer_id = get_post_meta( $post->ID, '_appointment_customer_id', true );
		$order_parent_id = apply_filters( 'woocommerce_order_number', _x( '#', 'hash before order number', 'woocommerce-appointments' ) . $post->post_parent, $post->post_parent );

		// Sanity check saved dates
		$start_date = get_post_meta( $post->ID, '_appointment_start', true );
		$end_date   = get_post_meta( $post->ID, '_appointment_end', true );
		$product_id = get_post_meta( $post->ID, '_appointment_product_id', true );

		if ( $start_date && strtotime( $start_date ) > strtotime( '+ 2 year', current_time( 'timestamp' ) ) ) {
			echo '<div class="updated highlight"><p>' . __( 'This appointment is scheduled over 2 years into the future. Please ensure this is correct.', 'woocommerce-appointments' ) . '</p></div>';
		}
		if ( $product_id && ( $product = get_product( $product_id ) ) && ( $max = $product->get_max_date() ) ) {
			$max_date = strtotime( "+{$max['value']} {$max['unit']}", current_time( 'timestamp' ) );
			if ( strtotime( $start_date ) > $max_date || strtotime( $end_date ) > $max_date ) {
				echo '<div class="updated highlight"><p>' . sprintf( __( 'This appointment is scheduled over the products allowed max appointment date (%s). Please ensure this is correct.', 'woocommerce-appointments' ), date_i18n( wc_date_format(), $max_date ) ) . '</p></div>';
			}
		}
		if ( strtotime( $start_date ) && strtotime( $end_date ) && strtotime( $start_date ) > strtotime( $end_date ) ) {
			echo '<div class="error"><p>' . __( 'This appointment has an end date set before the start date.', 'woocommerce-appointments' ) . '</p></div>';
		}
		$product_check = get_product( $product_id );
		if ( $product_check->is_skeleton() ) {
			echo '<div class="error"><p>' . sprintf( __( 'This appointment is missing a required add-on (product type: %s). Some information is shown below but might be incomplete. Please install the missing add-on through the plugins screen.', 'woocommerce-appointments' ), $product_check->product_type ) . '</p></div>';
		}
		?>
		<style type="text/css">
			#post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
		</style>
		<div class="panel-wrap woocommerce">
			<div id="appointment_data" class="panel">

			<h2><?php printf( __( 'Appointment #%s', 'woocommerce-appointments' ), esc_html( $post->ID ) ); ?></h2>
			<p class="appointment_number"><?php

				if ( $post->post_parent ) {
					$order = new WC_Order( $post->post_parent );
					printf( ' ' . __( 'Order: %s.', 'woocommerce-appointments' ), '<a href="' . admin_url( 'post.php?post=' . absint( $post->post_parent ) . '&action=edit' ) . '">#' . esc_html( $order->get_order_number() ) . '</a>' );
				}
				
				if ( $product->is_appointments_addon() ) {
					printf( ' ' . __( 'Appointment type: %s', 'woocommerce-appointments' ), $product->appointments_addon_title() );
				}

			?></p>

			<div class="appointment_data_column_container">
				<div class="appointment_data_column">

					<h4><?php _e( 'General Details', 'woocommerce-appointments' ); ?></h4>

					<p class="form-field form-field-wide">
						<label for="_appointment_order_id"><?php _e( 'Order ID:', 'woocommerce-appointments' ); ?></label>
						<?php
						$order_string = '';
						if ( ! empty( $post->post_parent ) ) {
							$order_string = $order_parent_id . ' &ndash; ' . esc_html( get_the_title( $post->post_parent ) );
						}
						if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) : ?>
							<select id="_appointment_order_id" name="_appointment_order_id" data-placeholder="<?php _e( 'Select an order&hellip;', 'woocommerce-appointments' ); ?>">
								<option value=""><?php _e( 'N/A', 'woocommerce-appointments' ); ?></option>
								<?php
									if ( $post->post_parent ) {
										echo '<option value="' . esc_attr( $post->post_parent ) . '" ' . selected( 1, 1, false ) . '>' . esc_attr( $order_string ) . '</option>';
									}
								?>
							</select>
						<?php else : ?>
							<input type="hidden" id="_appointment_order_id" name="_appointment_order_id" data-placeholder="<?php _e( 'N/A', 'woocommerce-appointments' ); ?>" data-selected="<?php echo esc_attr( $order_string ); ?>" value="<?php echo esc_attr( $post->post_parent ? $post->post_parent : '' ); ?>" data-allow_clear="true" />
						<?php endif; ?>
					</p>

					<p class="form-field form-field-wide"><label for="appointment_date"><?php _e( 'Date created:', 'woocommerce-appointments' ); ?></label>
						<input type="text" class="date-picker-field" name="appointment_date" id="appointment_date" maxlength="10" value="<?php echo date_i18n( 'Y-m-d', strtotime( $post->post_date ) ); ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" /> @ <input type="number" class="hour" placeholder="<?php _e( 'h', 'woocommerce-appointments' ); ?>" name="appointment_date_hour" id="appointment_date_hour" maxlength="2" size="2" value="<?php echo date_i18n( 'H', strtotime( $post->post_date ) ); ?>" pattern="\-?\d+(\.\d{0,})?" />:<input type="number" class="minute" placeholder="<?php _e( 'm', 'woocommerce-appointments' ); ?>" name="appointment_date_minute" id="appointment_date_minute" maxlength="2" size="2" value="<?php echo date_i18n( 'i', strtotime( $post->post_date ) ); ?>" pattern="\-?\d+(\.\d{0,})?" />
					</p>

					<?php
						$statuses = array_unique( array_merge( get_wc_appointment_statuses(), get_wc_appointment_statuses( 'user' ), get_wc_appointment_statuses( 'cancel') ) );
						$statuses = $this->get_labels_for_statuses( $statuses );
					?>

					<p class="form-field form-field-wide">
						<label for="_appointment_status"><?php _e( 'Appointment Status:', 'woocommerce-appointments' ); ?></label>
						<select id="_appointment_status" name="_appointment_status" class="wc-enhanced-select">
							<?php
								foreach ( $statuses as $key => $value ) {
									echo '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $post->post_status, false ) . '>' . esc_html__( $value, 'woocommerce-appointments' ) . '</option>';
								}
							?>
						</select>
					</p>

					<p class="form-field form-field-wide">
						<label for="_appointment_customer_id"><?php _e( 'Customer:', 'woocommerce-appointments' ); ?></label>
						<?php
						$user_string = '';
						if ( ! empty( $customer_id ) ) {
							$user        = get_user_by( 'id', $customer_id );
							$user_string = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email );
						} else {
							$customer_id = '';
						}
						if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) : ?>
							<select id="_appointment_customer_id" name="_appointment_customer_id" class="ajax_chosen_select_customer">
								<option value=""><?php _e( 'Guest', 'woocommerce-appointments' ); ?></option>
								<?php
									if ( $customer_id ) {
										$user = get_user_by( 'id', $customer_id );
										echo '<option value="' . esc_attr( $customer_id ) . '" ' . selected( 1, 1, false ) . '>' . esc_html( $user_string ) . ')</option>';
									}
								?>
							</select>
						<?php else : ?>
							<input type="hidden" class="wc-customer-search" id="_appointment_customer_id" name="_appointment_customer_id" data-placeholder="<?php _e( 'Guest', 'woocommerce-appointments' ); ?>" data-selected="<?php echo htmlspecialchars( $user_string ); ?>" value="<?php echo $customer_id; ?>" data-allow_clear="true" />
						<?php endif; ?>
					</p>

					<?php do_action( 'woocommerce_admin_appointment_data_after_appointment_details', $post->ID ); ?>

				</div>
				<div class="appointment_data_column">

					<h4><?php _e( 'Appointment Specification', 'woocommerce-appointments' ); ?></h4>

					<?php

					$appointable_products = array( '' => __( 'N/A', 'woocommerce-appointments' ) );

					$products = WC_Appointments_Admin::get_appointment_products();

					foreach ( $products as $product ) {
						$appointable_products[ $product->ID ] = $product->post_title;

						$staff = wc_appointment_get_product_staff( $product->ID );

						foreach ( $staff as $staff ) {
							$appointable_products[ $product->ID . '=>' . $staff->ID ] = '&nbsp;&nbsp;&nbsp;' . $staff->display_name;
						}
					}

					$product_id  = get_post_meta( $post->ID, '_appointment_product_id', true );
					$staff_id = get_post_meta( $post->ID, '_appointment_staff_id', true );

					woocommerce_wp_select( array( 'id' => 'product_or_staff_id', 'class' => 'wc-enhanced-select', 'label' => __( 'Scheduled Product', 'woocommerce-appointments' ), 'options' => $appointable_products, 'value' => ( $staff_id ? $product_id . '=>' . $staff_id : $product_id ) ) );

					woocommerce_wp_text_input( array( 'id' => '_appointment_parent_id', 'label' => __( 'Parent Appointment ID', 'woocommerce-appointments' ), 'placeholder' => 'N/A' ) );
					
					$saved_qty = get_post_meta( $post->ID, '_appointment_qty', true );
					$product = wc_get_product( $product_id );

					if ( ! empty ( $product ) ) {
						if ( ! empty( $saved_qty ) ) {
							echo '<br class="clear" />';
							echo '<h4>' . __( 'Appointment Quantity', 'woocommerce-bookings' ) . '</h4>';

							woocommerce_wp_text_input( array( 'id' => '_appointment_qty', 'label' => __( 'Number of customers', 'woocommerce-appointments' ), 'type' => 'number', 'placeholder' => '0', 'value' => $saved_qty, 'wrapper_class' => 'appointment-qty' ) );
						}
					}
					?>
				</div>
				<div class="appointment_data_column">

					<h4><?php _e( 'Appointment Date/Time', 'woocommerce-appointments' ); ?></h4>

					<?php

					woocommerce_wp_text_input( array( 'id' => 'appointment_start_date', 'label' => __( 'Start Date', 'woocommerce-appointments' ), 'placeholder' => 'yyyy-mm-dd', 'value' => date( 'Y-m-d', strtotime( get_post_meta( $post->ID, '_appointment_start', true ) ) ), 'class' => 'date-picker-field' ) );

					woocommerce_wp_text_input( array( 'id' => 'appointment_end_date', 'label' => __( 'End Date', 'woocommerce-appointments' ), 'placeholder' => 'yyyy-mm-dd', 'value' => date( 'Y-m-d', strtotime( get_post_meta( $post->ID, '_appointment_end', true ) ) ), 'class' => 'date-picker-field' ) );

					woocommerce_wp_checkbox( array( 'id' => '_appointment_all_day', 'label' => __( 'All Day', 'woocommerce-appointments' ), 'description' => __( 'Check this box if the appointment is for all day.', 'woocommerce-appointments' ), 'value' => get_post_meta( $post->ID, '_appointment_all_day', true ) ? 'yes' : 'no' ) );

					woocommerce_wp_text_input( array( 'id' => 'appointment_start_time', 'label' => __( 'Start Time', 'woocommerce-appointments' ), 'placeholder' => 'hh:mm', 'value' => date( 'H:i', strtotime( get_post_meta( $post->ID, '_appointment_start', true ) ) ), 'class' => 'datepicker' ) );

					woocommerce_wp_text_input( array( 'id' => 'appointment_end_time', 'label' => __( 'End Time', 'woocommerce-appointments' ), 'placeholder' => 'hh:mm', 'value' => date( 'H:i', strtotime( get_post_meta( $post->ID, '_appointment_end', true ) ) ) ) );

					?>

				</div>
			</div>
			<div class="clear"></div>
		</div>

		<?php
			if ( version_compare( WOOCOMMERCE_VERSION, '2.3', '<' ) ) {
				wc_enqueue_js( "
					$( 'select#_appointment_status' ).chosen({
						disable_search: true
					});
					$( 'select#product_or_staff_id' ).chosen();
					$( 'select#_appointment_order_id' ).ajaxChosen({
						method:         'GET',
						url:            '" . admin_url( 'admin-ajax.php' ) . "',
						dataType:       'json',
						afterTypeDelay: 100,
						minTermLength:  1,
						data: {
							action:   'wc_appointments_json_search_order',
							security: '" . wp_create_nonce( 'search-appointment-order' ) . "'
						}
					}, function ( data ) {

						var orders = {};

						$.each( data, function ( i, val ) {
							orders[i] = val;
						});

						return orders;
					});
					$( 'select.ajax_chosen_select_customer' ).ajaxChosen({
						method:         'GET',
						url:            '" . admin_url( 'admin-ajax.php' ) . "',
						dataType:       'json',
						afterTypeDelay: 100,
						minTermLength:  1,
						data: {
							action:   'woocommerce_json_search_customers',
							security: '" . wp_create_nonce( 'search-customers' ) . "'
						}
					}, function ( data ) {

						var terms = {};

						$.each( data, function ( i, val ) {
							terms[i] = val;
						});

						return terms;
					});
				" );
			} else {
				wc_enqueue_js( "
					$( '#_appointment_order_id' ).filter( ':not(.enhanced)' ).each( function() {
						var select2_args = {
							allowClear:  true,
							placeholder: $( this ).data( 'placeholder' ),
							minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
							escapeMarkup: function( m ) {
								return m;
							},
							ajax: {
						        url:         '" . admin_url( 'admin-ajax.php' ) . "',
						        dataType:    'json',
						        quietMillis: 250,
						        data: function( term, page ) {
						            return {
										term:     term,
										action:   'wc_appointments_json_search_order',
										security: '" . wp_create_nonce( 'search-appointment-order' ) . "'
						            };
						        },
						        results: function( data, page ) {
						        	var terms = [];
							        if ( data ) {
										$.each( data, function( id, text ) {
											terms.push( { id: id, text: text } );
										});
									}
						            return { results: terms };
						        },
						        cache: true
						    }
						};
						select2_args.multiple = false;
						select2_args.initSelection = function( element, callback ) {
							var data = {id: element.val(), text: element.attr( 'data-selected' )};
							return callback( data );
						};
						$( this ).select2( select2_args ).addClass( 'enhanced' );
					});
				" );
			}
			wc_enqueue_js( "
				$( '#_appointment_all_day' ).change( function () {
					if ( $( this ).is( ':checked' ) ) {
						$( '#appointment_start_time, #appointment_end_time' ).closest( 'p' ).hide();
					} else {
						$( '#appointment_start_time, #appointment_end_time' ).closest( 'p' ).show();
					}
				}).change();
				$( '.date-picker-field' ).datepicker({
					dateFormat: 'yy-mm-dd',
					numberOfMonths: 1,
					showButtonPanel: true,
				});
			" );
	}
	
	/**
	 * Returns an array of labels (statuses wrapped in gettext)
	 * @param  array  $statuses
	 * @return array
	 */
	public function get_labels_for_statuses( $statuses = array() ) {
		$labels = array();
		foreach ( $statuses as $status ) {
			$labels[ $status ] = __( $status, 'woocommerce-appointments' );
		}
		return $labels;
	}

	public function meta_box_save( $post_id ) {
		if ( ! isset( $_POST['wc_appointments_details_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['wc_appointments_details_meta_box_nonce'], 'wc_appointments_details_meta_box' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! in_array( $_POST['post_type'], $this->post_types ) ) {
			return $post_id;
		}

		global $wpdb, $post;

		// Save simple fields
		$appointment_order_id = absint( $_POST['_appointment_order_id'] );
		$appointment_status   = wc_clean( $_POST['_appointment_status'] );
		$customer_id      = absint( $_POST['_appointment_customer_id'] );
		$product_id       = wc_clean( $_POST['product_or_staff_id'] );
		$parent_id        = absint( $_POST['_appointment_parent_id'] );
		$all_day          = isset( $_POST['_appointment_all_day'] ) ? '1' : '0';
		$product 		  = wc_get_product( $product_id );
		$quantity		  = isset( $_POST['_appointment_qty'] ) ? absint( $_POST[ '_appointment_qty' ] ) : 1;

		// Update post_parent and status via query to prevent endless loops
		$wpdb->update( $wpdb->posts, array( 'post_parent' => $appointment_order_id ), array( 'ID' => $post_id ) );
		$wpdb->update( $wpdb->posts, array( 'post_status' => $appointment_status ), array( 'ID' => $post_id ) );

		// Old status
		$old_status = $post->post_status;
		
		// Shortcut Trigger actions manually
		do_action( 'woocommerce_appointment_before_' . $appointment_status, $post_id );
		do_action( 'woocommerce_appointment_before_' . $old_status . '_to_' . $appointment_status, $post_id );

		// Note in the order
		if ( $appointment_order_id && function_exists( 'wc_get_order' ) && ( $order = wc_get_order( $appointment_order_id ) ) ) {
			$order->add_order_note( sprintf( __( 'Appointment #%d status changed manually from "%s" to "%s"', 'woocommerce-appointments' ), $post_id, $old_status, $appointment_status ) );
		}

		// Save product and staff
		if ( strstr( $product_id, '=>' ) ) {
			list( $product_id, $staff_id ) = explode( '=>', $product_id );
		} else {
			$staff_id = 0;
		}
		
		// Product has changed?
		$old_product_id = get_post_meta( $post_id, '_appointment_product_id', true );
		$old_product_id_exists = get_post_meta( $post_id, '_appointment_product_id_orig', true );
		
		if ( $old_product_id != $product_id && ! $old_product_id_exists ) {
			update_post_meta( $post_id, '_appointment_product_id_orig', $old_product_id );
		}

		update_post_meta( $post_id, '_appointment_staff_id', $staff_id );
		update_post_meta( $post_id, '_appointment_product_id', $product_id );

		// Update meta
		update_post_meta( $post_id, '_appointment_customer_id', $customer_id );
		update_post_meta( $post_id, '_appointment_parent_id', $parent_id );
		update_post_meta( $post_id, '_appointment_all_day', $all_day );
		
		// Quantity
		$saved_qty = get_post_meta( $post_id, '_appointment_qty', true );
		
		if ( ! empty ( $product ) ) {
			if ( ! empty( $saved_qty ) ) {
				update_post_meta( $post_id, '_appointment_qty', $quantity );
			}
		}

		// Update date
		if ( empty( $_POST['appointment_date'] ) ) {
			$date = current_time('timestamp');
		} else {
			$date = strtotime( $_POST['appointment_date'] . ' ' . (int) $_POST['appointment_date_hour'] . ':' . (int) $_POST['appointment_date_minute'] . ':00' );
		}

		$date = date_i18n( 'Y-m-d H:i:s', $date );

		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->posts SET post_date = %s, post_date_gmt = %s WHERE ID = %s", $date, get_gmt_from_date( $date ), $post_id ) );

		// Do date and time magic and save them in one field
		$start_date = explode( '-', wc_clean( $_POST['appointment_start_date'] ) );
		$end_date   = explode( '-', wc_clean( $_POST['appointment_end_date'] ) );
		$start_time = explode( ':', wc_clean( $_POST['appointment_start_time'] ) );
		$end_time   = explode( ':', wc_clean( $_POST['appointment_end_time'] ) );

		$start = mktime( $start_time[0], $start_time[1], 0, $start_date[1], $start_date[2], $start_date[0] );
		$end   = mktime( $end_time[0], $end_time[1], 0, $end_date[1], $end_date[2], $end_date[0] );

		update_post_meta( $post_id, '_appointment_start', date( 'YmdHis', $start ) );
		update_post_meta( $post_id, '_appointment_end', date( 'YmdHis', $end ) );
		
		if ( ! empty( $order ) && $appointment_order_id ) {
						
			// Update order metas
			foreach ( $order->get_items() as $item_id => $item ) {
				if ( 'line_item' != $item['type'] || ! in_array( $post_id, $item['item_meta']['Appointment ID'] ) ) {
					continue;
				}

				$is_all_day = isset( $_POST['_appointment_all_day'] ) && $_POST['_appointment_all_day'] == 'yes';
				
				if ( ! metadata_exists( 'order_item', $item_id, __( 'Appointment ID', 'woocommerce-appointments' ) ) ) {
					wc_add_order_item_meta( $item_id, __( 'Appointment ID', 'woocommerce-appointments' ), intval( $post_id ) );
				}
				
				//* Update product ID
				if ( ! empty ( $product ) ) {
					if ( metadata_exists( 'order_item', $item_id, '_product_id' ) ) {
						wc_update_order_item_meta( $item_id, '_product_id', $product_id );
						wc_update_order_item( $item_id, array( 'order_item_name' => $product->get_title() ) );
					}
				}

				// Update date
				$date = mktime( 0, 0, 0, $start_date[1], $start_date[2], $start_date[0] );
				if ( metadata_exists( 'order_item', $item_id, __( 'Date', 'woocommerce-appointments' ) ) ) {
					wc_update_order_item_meta( $item_id, __( 'Date', 'woocommerce-appointments' ), date_i18n( wc_date_format(), $date ) );
				} else {
					wc_add_order_item_meta( $item_id, __( 'Date', 'woocommerce-appointments' ), date_i18n( wc_date_format(), $date ) );
				}

				// Update time
				if ( ! $is_all_day ) {
					$time = mktime( $start_time[0], $start_time[1], 0, $start_date[1], $start_date[2], $start_date[0] );
					if ( metadata_exists( 'order_item', $item_id, __( 'Time', 'woocommerce-appointments' ) ) ) {
						wc_update_order_item_meta( $item_id, __( 'Time', 'woocommerce-appointments' ), date_i18n( wc_time_format(), $time ) );
					} else {
						wc_add_order_item_meta( $item_id, __( 'Time', 'woocommerce-appointments' ), date_i18n( wc_time_format(), $time ) );
					}
				}

				// Update staff
				$staff = wc_appointment_get_product_staff( $product_id, $staff_id );
				if ( metadata_exists( 'order_item', $item_id, __( 'Staff', 'woocommerce-appointments' ) ) ) {
					wc_update_order_item_meta( $item_id, __( 'Staff', 'woocommerce-appointments' ), $staff->get_title() );
				} else {
					if ( ! empty ( $staff ) && method_exists( $staff, 'get_title' ) ) {
						wc_add_order_item_meta( $item_id, __( 'Staff', 'woocommerce-appointments' ), $staff->get_title() );
					}
				}
				
				//* Update quantity
				if ( ! empty ( $product ) ) {
					if ( metadata_exists( 'order_item', $item_id, '_qty' ) ) {
						wc_update_order_item_meta( $item_id, '_qty', $quantity );
					}
				}

				// Update duration
				$start_diff = wc_clean( $_POST['appointment_start_date'] );
				$end_diff   = wc_clean( $_POST['appointment_end_date'] );

				if ( ! $is_all_day ) {
					$start_diff .= ' ' . wc_clean( $_POST['appointment_start_time'] );
					$end_diff   .= ' ' . wc_clean( $_POST['appointment_end_time'] );
				}

				$start = new DateTime( $start_diff );
				$end   = new DateTime( $end_diff );

				// Add one day because DateTime::diff does not include the last day
				if ( $is_all_day ) {
					$end->modify( '+1 day' );
				}

				$diffs = $end->diff( $start );

				$duration = array();
				foreach ( $diffs as $type => $diff ) {
					if ( $diff != 0 ) {
						switch( $type ) {
							case 'y': $duration[] = _n( '%y year', '%y years', $diff, 'woocommerce-appointments' );     break;
							case 'm': $duration[] = _n( '%m month', '%m months', $diff, 'woocommerce-appointments' );   break;
							case 'd': $duration[] = _n( '%d day', '%d days', $diff, 'woocommerce-appointments' );       break;
							case 'h': $duration[] = _n( '%h hour', '%h hours', $diff, 'woocommerce-appointments' );     break;
							case 'i': $duration[] = _n( '%i minute', '%i minutes', $diff, 'woocommerce-appointments' ); break;
						}
					}
				}

				$duration = implode( ', ', $duration );
				$duration = $diffs->format( $duration );

				if ( metadata_exists( 'order_item', $item_id, __( 'Duration', 'woocommerce-appointments' ) ) ) {
					wc_update_order_item_meta( $item_id, __( 'Duration', 'woocommerce-appointments' ), $duration );
				} else {
					if ( ! empty( $duration ) ) {
						wc_add_order_item_meta( $item_id, __( 'Duration', 'woocommerce-appointments' ), $duration );
					}
				}				
			}
		}
		
		// Trigger actions manually
		do_action( 'woocommerce_appointment_' . $appointment_status, $post_id );
		do_action( 'woocommerce_appointment_' . $old_status . '_to_' . $appointment_status, $post_id );
		clean_post_cache( $post_id );

		WC_Cache_Helper::get_transient_version( 'appointments', true );

		do_action( 'woocommerce_appointment_process_meta', $post_id );
	}
}

return new WC_Appointments_Admin_Details_Meta_Box();