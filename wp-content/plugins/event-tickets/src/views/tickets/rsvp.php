<?php
/**
 * This template renders the RSVP ticket form
 *
 * @version 4.1
 *
 */

$is_there_any_product         = false;
$is_there_any_product_to_sell = false;

ob_start();
$messages = Tribe__Tickets__RSVP::get_instance()->get_messages();
$messages_class = $messages ? 'tribe-rsvp-message-display' : '';
?>
<form action="" class="cart <?php echo esc_attr( $messages_class ); ?>" method="post" enctype='multipart/form-data'>
	<h2 class="tribe-events-tickets-title"><?php esc_html_e( 'Регистрация', 'event-tickets' ) ?></h2>
	<div class="tribe-rsvp-messages">
		<?php
		if ( $messages ) {
			foreach ( $messages as $message ) {
				?>
				<div class="tribe-rsvp-message tribe-rsvp-message-<?php echo esc_attr( $message->type ); ?>">
					<?php echo esc_html( $message->message ); ?>
				</div>
				<?php
			}//end foreach
		}//end if
		?>
		<div class="tribe-rsvp-message tribe-rsvp-message-error tribe-rsvp-message-confirmation-error" style="display:none;">
			<?php echo esc_html_e( 'Пожалуйста, введите данные для регистрации.', 'event-tickets' ); ?>
		</div>
	</div>
	<table width="100%" class="tribe-events-tickets tribe-events-tickets-rsvp">
		<?php
		foreach ( $tickets as $ticket ) {
			// if the ticket isn't an RSVP ticket, then let's skip it
			if ( 'Tribe__Tickets__RSVP' !== $ticket->provider_class ) {
				continue;
			}

			if ( $ticket->date_in_range( time() ) ) {
				$is_there_any_product = true;

				?>
				<tr>
					<td class="tribe-ticket quantity" data-product-id="<?php echo esc_attr( $ticket->ID ); ?>">
						<input type="hidden" name="product_id[]" value="<?php echo absint( $ticket->ID ); ?>">
						<?php
						if ( $ticket->is_in_stock() ) {
							$is_there_any_product_to_sell = true;
							?>
							<input type="number" class="tribe-ticket-quantity" min="1" max="1" name="quantity_<?php echo absint( $ticket->ID ); ?>" value="1">
							<?php

							if ( $ticket->managing_stock() ) {
								?>
								<span class="tribe-tickets-remaining">
									<?php
									echo sprintf( esc_html__( '%1$s out of %2$s available', 'event-tickets' ), $ticket->remaining(), $ticket->original_stock() );
									?>
								</span>
								<?php
							}
						}//end if
						else {
							?>
							<span class="tickets_nostock"><?php esc_html_e( 'Билеты кончились!', 'event-tickets' ); ?></span>
							<?php
						}
						?>
					</td>
					<td class="tickets_name">
						<?php echo esc_html( $ticket->name ); ?>
					</td>
					<td class="tickets_description" colspan="2">
						<?php echo esc_html( $ticket->description ); ?>
					</td>
				</tr>
				<?php

				/**
				 * Allows injection of HTML after an RSVP ticket table row
				 *
				 * @var Event ID
				 * @var Tribe__Tickets__Ticket_Object
				 */
				do_action( 'event_tickets_rsvp_after_ticket_row', tribe_events_get_ticket_event( $ticket->id ), $ticket );
			}
		}//end foreach

		if ( $is_there_any_product_to_sell ) {
			?>
			<tr class="tribe-tickets-meta-row">
				<td colspan="4" class="tribe-tickets-attendees">
					<header><?php esc_html_e( 'Выслать подтверждение по адресу:', 'event-tickets-plus' ); ?></header>
					<?php
					/**
					 * Allows injection of HTML before RSVP ticket confirmation fields
					 *
					 * @var array of Tribe__Tickets__Ticket_Object
					 */
					do_action( 'event_tickets_rsvp_before_confirmation_fields', $tickets );
					?>
					<table>
						<tr class="tribe-tickets-full-name-row">
							<td>
								<label for="tribe-tickets-full-name"><?php esc_html_e( 'Имя и фамилия', 'event-tickets' ); ?>:</label>
							</td>
							<td colspan="3">
								<input type="text" name="attendee[full_name]" id="tribe-tickets-full-name">
							</td>
						</tr>
						<tr class="tribe-tickets-email-row">
							<td>
								<label for="tribe-tickets-email"><?php esc_html_e( 'Email', 'event-tickets' ); ?>:</label>
							</td>
							<td colspan="3">
								<input type="email" name="attendee[email]" id="tribe-tickets-email">
							</td>
						</tr>
						<tr class="tribe-tickets-attendees-list-optout">
							<td colspan="4">
								<input type="checkbox" name="attendee[optout]" id="tribe-tickets-attendees-list-optout">
								<label for="tribe-tickets-attendees-list-optout"><?php esc_html_e( 'Don\'t list me on the public attendee list', 'event-tickets' ); ?></label>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="4" class="add-to-cart">
					<button type="submit" name="tickets_process" value="1" class="button alt"><?php esc_html_e( 'Зарегистрироваться', 'event-tickets' );?></button>
				</td>
			</tr>
			<?php
		}
		?>
	</table>
</form>

<?php
$content = ob_get_clean();
if ( $is_there_any_product ) {
	echo $content;
}
