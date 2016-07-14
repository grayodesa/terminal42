<?php
/**
 * Renders the meta fields for order editing
 *
 * @version 4.2
 *
 */
$meta = Tribe__Tickets_Plus__Main::instance()->meta();
$ticket = get_post( $attendee['product_id'] );
if ( $meta->meta_enabled( $ticket->ID ) ) {
	?>
	<div class="tribe-event-tickets-plus-meta" id="tribe-event-tickets-plus-meta-<?php echo esc_attr( $ticket->ID ); ?>" data-ticket-id="<?php echo esc_attr( $ticket->ID ); ?>">
		<a class="attendee-meta toggle show"><?php esc_html_e( 'Toggle attendee info', 'event-tickets-plus' ); ?></a>
		<div class="attendee-meta-row">
			<?php
			if ( ! $meta->meta_enabled( $ticket->ID ) ) {
				$meta_fields = Tribe__Tickets_Plus__Main::instance()->meta()->get_meta_fields_by_ticket( $attendee['product_id'] );
				$meta_data = get_post_meta( $attendee['attendee_id'], Tribe__Tickets_Plus__Meta::META_KEY, true );
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

					if ( '' != $value ) {
						?>
						<div class="attendee-meta-details">
							<span class="event-tickets-meta-label <?php echo esc_attr( $field->slug ); ?>"><?php echo esc_html( $field->label ); ?></span>
							<span class="event-tickets-meta-data <?php echo esc_attr( $field->slug ); ?>"><?php echo $value ? esc_html( $value ) : '&nbsp;'; ?></span>
						</div>
						<?php
					}
				}
			} else {
				$meta_fields = $meta->get_meta_fields_by_ticket( $ticket->ID );
				foreach ( $meta_fields as $field ) {
					echo $field->render( $attendee['attendee_id'] );
				}
			}
			?>
		</div>
	</div>
<?php }