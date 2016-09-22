<?php
/**
 * Renders the Shopp tickets table/form
 *
 * @version 4.2.7
 *
 * @var bool $must_login
 */
$is_there_any_product         = false;
$is_there_any_product_to_sell = false;
$unavailability_messaging     = is_callable( array( $this, 'do_not_show_tickets_unavailable_message' ) );

ob_start();
?>
<form action="<?php echo esc_url( shopp( 'cart.url' ) ); ?>" class="cart" method="post" enctype="multipart/form-data">
	<table width="100%" class="tribe-events-tickets">
		<?php
		foreach ( $tickets as $ticket ) {
			$product = shopp_product( $ticket->ID );
			$in_stock = ( 'off' === $product->inventory || $product->stock > $product->qty_sold ) ? true : false;

			if ( $ticket->date_in_range( current_time( 'timestamp' ) ) ) {

				$is_there_any_product = true;

				echo sprintf( '<input type="hidden" name="product_id[]" value="%d">', esc_attr( $ticket->ID ) );

				echo '<tr>';
				echo '<td width="75" class="shopp quantity" data-product-id="' . esc_attr( $ticket->ID ) . '">';

				if ( $in_stock ) {

					echo $this->quantity_selector( $product );
					$is_there_any_product_to_sell = true;

					$remaining = $ticket->remaining();

					if ( $remaining ) {
						?>
						<span class="tribe-tickets-remaining">
							<?php
							echo sprintf( esc_html__( '%1$s out of %2$s available', 'event-tickets-plus' ), esc_html( $remaining ), esc_html( $ticket->original_stock() ) );
							?>
						</span>
						<?php
					}
				} else {
					echo '<span class="tickets_nostock">' . esc_html__( 'Out of stock!', 'event-tickets-plus' ) . '</span>';
				}
				echo '</td>';

				echo '<td class="tickets_name">';
				echo $ticket->name;
				echo '</td>';

				echo '<td class="tickets_price">';
				echo $this->get_price_html( $product->id );
				echo '</td>';

				echo '<td class="tickets_description">';
				echo $ticket->description;
				echo '</td>';

				echo '</tr>';

				echo
					'<tr class="tribe-tickets-attendees-list-optout">' .
						'<td colspan="4">' .
							'<input type="checkbox" name="tribe_shopp_optout" id="tribe-tickets-attendees-list-optout-shopp">' .
							'<label for="tribe-tickets-attendees-list-optout-shopp">' .
								esc_html__( 'Don\'t list me on the public attendee list', 'event-tickets' ) .
							'</label>' .
						'</td>' .
					'</tr>';

				include Tribe__Tickets_Plus__Main::instance()->get_template_hierarchy( 'meta.php' );
			}
		}

		if ( $is_there_any_product ) {
			?>
			<h2 class="tribe-events-tickets-title"><?php esc_html_e( 'Tickets', 'event-tickets-plus' );?></h2>
			<?php if ( $is_there_any_product_to_sell ) { ?>
				<tr>
					<td colspan="4" class='shopp'>
						<?php if ( $must_login ): ?>
							<?php include Tribe__Tickets_Plus__Main::instance()->get_template_hierarchy( 'login-to-purchase' ); ?>
						<?php else: ?>
							<input type="hidden" name="cart" value="add" />
							<button type="submit" class="button alt"><?php esc_html_e( 'Add to cart', 'event-tickets-plus' );?></button>
						<?php endif; ?>
					</td>
				</tr>
				<?php
			}
		}
		?>

	</table>
</form>
<?php
$contents = ob_get_clean();
if ( $is_there_any_product_to_sell ) {
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
