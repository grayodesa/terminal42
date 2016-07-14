<?php

class Tribe__Tickets_Plus__Meta__Render {
	public function __construct() {
		add_action( 'tribe_tickets_ticket_email_ticket_bottom', array( $this, 'ticket_email_meta' ) );
		add_action( 'event_tickets_attendees_table_after_row', array( $this, 'table_meta_data' ) );
		add_filter( 'event_tickets_attendees_table_ticket_column', array( $this, 'add_meta_toggle_to_ticket_column' ) );
	}

	public function add_meta_toggle_to_ticket_column( $item ) {
		$meta_data = get_post_meta( $item['attendee_id'], Tribe__Tickets_Plus__Meta::META_KEY, true );

		if ( ! $meta_data ) {
			return;
		}

		?>
		<a href="" class="event-tickets-meta-toggle">
			<span class="event-tickets-meta-toggle-view"><?php esc_html_e( 'View details', 'event-tickets-plus' ); ?></span>
			<span class="event-tickets-meta-toggle-hide"><?php esc_html_e( 'Hide details', 'event-tickets-plus' ); ?></span>
		</a>
		<?php
	}

	public function table_meta_data( $item ) {
		wp_enqueue_style( 'event-tickets-meta' );
		wp_enqueue_script( 'event-tickets-meta-report' );

		$meta_fields = Tribe__Tickets_Plus__Main::instance()->meta()->get_meta_fields_by_ticket( $item['product_id'] );
		$meta_data = get_post_meta( $item['attendee_id'], Tribe__Tickets_Plus__Meta::META_KEY, true );

		?>
		<tr class="event-tickets-meta-row">
			<td colspan="3"></td>
			<td colspan="4">
				<dl>
					<?php
					foreach ( $meta_fields as $field ) {
						if ( 'checkbox' === $field->type && isset( $field->extra['options'] ) ) {
							$values = array();
							foreach ( $field->extra['options'] as $option ) {
								$key = $field->slug . '_' . sanitize_title( $option );

								if ( isset( $meta_data[ $key ] ) ) {
									$values[] = $meta_data[ $key ];
								}
							}

							$value = implode( ', ', $values );
						} elseif ( isset( $meta_data[ $field->slug ] ) ) {
							$value = $meta_data[ $field->slug ];
						} else {
							continue;
						}

						if ( '' === trim( $value ) ) {
							$value = '&nbsp;';
						}

						?>
						<dt class="event-tickets-meta-label_<?php echo esc_attr( $field->slug ); ?>"><?php echo esc_html( $field->label ); ?></dt>
						<dd class="event-tickets-meta-data_<?php echo esc_attr( $field->slug ); ?>"><?php echo $value ? esc_html( $value ) : '&nbsp;'; ?></dd>
						<?php
					}
					?>
				</dl>
			</td>
		</tr>
		<?php
	}

	/**
	 * Inject custom meta in to tickets
	 *
	 * @param array $item Attendee data
	 */
	public function ticket_email_meta( $item ) {
		$meta_fields = Tribe__Tickets_Plus__Main::instance()->meta()->get_meta_fields_by_ticket( $item['product_id'] );
		$meta_data = get_post_meta( $item['qr_ticket_id'], Tribe__Tickets_Plus__Meta::META_KEY, true );

		if ( ! $meta_fields || ! $meta_data ) {
			return;
		}

		?>
		<table class="inner-wrapper" border="0" cellpadding="0" cellspacing="0" width="620" bgcolor="#f7f7f7" style="margin:0 auto !important; width:620px; padding:0;">
			<tr>
				<td valign="top" class="ticket-content" align="left" width="580" border="0" cellpadding="20" cellspacing="0" style="padding:20px; background:#f7f7f7;" colspan="2">
					<h6 style="color:#909090 !important; margin:0 0 4px 0; font-family: 'Helvetica Neue', Helvetica, sans-serif; text-transform:uppercase; font-size:13px; font-weight:700 !important;"><?php esc_html_e( 'Attendee Information', 'event-tickets-plus' ); ?></h6>
				</td>
			</tr>
			<?php
			foreach ( $meta_fields as $field ) {
				if ( 'checkbox' === $field->type && isset( $field->extra['options'] ) ) {
					$values = array();
					foreach ( $field->extra['options'] as $option ) {
						$key = $field->slug . '_' . sanitize_title( $option );

						if ( isset( $meta_data[ $key ] ) ) {
							$values[] = $meta_data[ $key ];
						}
					}

					$value = implode( ', ', $values );
				} elseif ( isset( $meta_data[ $field->slug ] ) ) {
					$value = $meta_data[ $field->slug ];
				} else {
					continue;
				}

				?>
				<tr>
					<th valign="top" class="event-tickets-meta-label_<?php echo esc_attr( $field->slug ); ?>" align="left" border="0" cellpadding="20" cellspacing="0" style="padding:0 20px; background:#f7f7f7;min-width:100px;">
						<?php echo esc_html( $field->label ); ?>
					</th>
					<td valign="top" class="event-tickets-meta-data_<?php echo esc_attr( $field->slug ); ?>" align="left" border="0" cellpadding="20" cellspacing="0" style="padding:0 20px; background:#f7f7f7;">
						<?php echo esc_html( $value ); ?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php
	}
}
