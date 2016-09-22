<?php
/**
 * Renders the EDD tickets table/form
 *
 * @version 4.2.7
 *
 * @var bool $must_login
 */
global $edd_options;

$is_there_any_product = false;
$is_there_any_product_to_sell = false;
$unavailability_messaging = is_callable( array( $this, 'do_not_show_tickets_unavailable_message' ) );
$stock = Tribe__Tickets_Plus__Commerce__EDD__Main::get_instance()->stock();

ob_start();
?>
<h2 class="tribe-events-tickets-title"><?php esc_html_e( 'Tickets', 'event-tickets-plus' );?></h2>

<form action="<?php echo esc_url( add_query_arg( 'eddtickets_process', 1, edd_get_checkout_uri() ) ); ?>" class="cart" method="post" enctype='multipart/form-data'>
	<table width="100%" class="tribe-events-tickets">
			<?php
			foreach ( $tickets as $ticket ) {

				$product = edd_get_download( $ticket->ID );

				if ( $ticket->date_in_range( current_time( 'timestamp' ) ) ) {

					$is_there_any_product = true;
					$data_product_id = 'data-product-id="' . esc_attr( $ticket->ID ) . '"';

					echo sprintf( '<input type="hidden" name="product_id[]"" value="%d">', esc_attr( $ticket->ID ) );

					echo '<tr>';
					echo '<td width="75" class="edd quantity" data-product-id="' . esc_attr( $ticket->ID ) . '">';


					if ( $stock->available_units( $product->ID ) ) {

						// For global stock enabled tickets with a cap, use the cap as the max quantity
						if ( $global_stock_enabled && Tribe__Tickets__Global_Stock::CAPPED_STOCK_MODE === $ticket->global_stock_mode()) {
							$remaining = $ticket->global_stock_cap();
						}
						else {
							$remaining = $ticket->remaining();
						}

						$max = '';
						if ( $ticket->managing_stock() ) {
							$max = 'max="' . absint( $remaining ) . '"';
						}

						echo '<input type="number" class="edd-input" min="0" ' . $max . ' name="quantity_' . esc_attr( $ticket->ID ) . '" value="0" ' . disabled( $must_login, true, false ) . '/>';

						$is_there_any_product_to_sell = true;

						if ( $remaining ) {
							?>
							<span class="tribe-tickets-remaining">
								<?php
								echo sprintf( esc_html__( '%1$s available', 'event-tickets-plus' ),
									'<span class="available-stock" ' . $data_product_id . '>' . esc_html( $remaining ) . '</span>'
								);
								?>
							</span>
							<?php
						}
					}
					else {
						echo '<span class="tickets_nostock">' . esc_html__( 'Out of stock!', 'event-tickets-plus' ) . '</span>';
					}

					echo '</td>';

					echo '<td class="tickets_name">';
					echo $ticket->name;
					echo '</td>';

					echo '<td class="tickets_price">';
					echo edd_price( $product->ID );
					echo '</td>';

					echo '<td class="tickets_description">';
					echo $ticket->description;
					echo '</td>';

					echo '</tr>';

					echo
					'<tr class="tribe-tickets-attendees-list-optout">' .
						'<td colspan="4">' .
							'<input type="checkbox" name="optout_'  . esc_attr( $ticket->ID ) . '" id="tribe-tickets-attendees-list-optout-edd">' .
							'<label for="tribe-tickets-attendees-list-optout-edd">' .
								esc_html__( 'Don\'t list me on the public attendee list', 'event-tickets' ) .
							'</label>' .
						'</td>' .
					'</tr>';

					include Tribe__Tickets_Plus__Main::instance()->get_template_hierarchy( 'meta.php' );
				}
			}
			?>

		<?php if ( $is_there_any_product_to_sell ) :
			$color = isset( $edd_options[ 'checkout_color' ] ) ? $edd_options[ 'checkout_color' ] : 'gray';
			$color = ( $color == 'inherit' ) ? '' : $color;
			?>
			<tr>
				<td colspan="4" class="eddtickets-add">
					<?php if ( $must_login ): ?>
						<?php include Tribe__Tickets_Plus__Main::instance()->get_template_hierarchy( 'login-to-purchase' ); ?>
					<?php else: ?>
						<button type="submit" class="edd-submit button <?php echo esc_attr( $color ); ?>"><?php esc_html_e( 'Add to cart', 'event-tickets-plus' );?></button>
					<?php endif; ?>
				</td>
			</tr>
		<?php endif ?>
	</table>
</form>

<?php
$contents = ob_get_clean();
if ( $is_there_any_product ) {
	echo $contents;

	// @todo remove safeguard in 4.3 or later
	if ( $unavailability_messaging ) {
		// If we have rendered tickets there is generally no need to display a 'tickets unavailable' message
		// for this post
		$this->do_not_show_tickets_unavailable_message();
	}
} else {
	// @todo remove safeguard in 4.3 or later
	if ( $unavailability_messaging ) {
		$unavailability_message = $this->get_tickets_unavailable_message( $tickets );

		// if there isn't an unavailability message, bail
		if ( ! $unavailability_message ) {
			return;
		}
	}
}
