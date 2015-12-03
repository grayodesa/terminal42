<?php
ob_start();
$is_there_any_product = false;
$is_there_any_product_to_sell = false;

foreach ( $tickets as $ticket ) {

	if ( $ticket->date_in_range( current_time( 'timestamp' ) ) ) {

		$is_there_any_product = true;

		echo '<tr>';

		echo "<td width='115' class='wpec'>";
		if ( wpsc_product_has_stock( $ticket->ID ) ) {

			$is_there_any_product_to_sell = true;
			if ( get_option( 'addtocart_or_buynow' ) == '1' ) {
				echo wpsc_buy_now_button( $ticket->ID );
			} else {
				?>
				<fieldset>
					<legend><?php esc_html_e( 'Quantity', 'event-tickets-plus' ); ?></legend>
					<div class="wpsc_quantity_update">
						<input type="text" id="wpec_tickets_quantity_<?php echo esc_attr( $ticket->ID ); ?>" name="wpec_tickets_quantity[]" size="2" value="0" />
						<input type="hidden" value="<?php echo esc_attr( $ticket->ID ); ?>" name="wpec_tickets_product_id[]" />
					</div>
					<!--close wpsc_quantity_update-->
				</fieldset>
				<?php
			}

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
			echo '<span class="tickets_nostock">' . esc_html__( 'Sold Out', 'event-tickets-plus' ) . '</span>';
		}
		echo '</td>';

		echo '<td class="tickets_name">';
		echo esc_html( $ticket->name );
		echo '</td>';

		echo '<td class="tickets_price">';
		echo $this->get_price_html( $ticket->ID );
		echo '</td>';

		echo '<td class="tickets_description">';
		echo $ticket->description;
		echo '</td>';

		echo '</tr>';
	}
}

$contents = ob_get_clean();

if ( $is_there_any_product ) { ?>

	<?php if ( $is_there_any_product_to_sell && ( get_option( 'addtocart_or_buynow' ) != 1 ) ) { ?>
		<form action="<?php echo esc_url( get_option( 'shopping_cart_url' ) ); ?>" class="cart" method="post" enctype="multipart/form-data">
	<?php } else { ?>
		<div class="cart">
	<?php } ?>

			<h2 class="tribe-events-tickets-title"><?php esc_html_e( 'Tickets', 'event-tickets-plus' ); ?></h2>

			<table width="100%" class="tribe-events-tickets">

				<?php echo $contents; ?>

				<?php if ( $is_there_any_product_to_sell && ( get_option( 'addtocart_or_buynow' ) != 1 ) ) { ?>
					<tr>
						<td colspan="4" class="wpeccommerce">
							<button type="submit" class="button alt">
								<?php esc_html_e( 'Add to cart', 'event-tickets-plus' ); ?>
							</button>
						</td>
					</tr>
				<?php } ?>
			</table>

	<?php if ( $is_there_any_product_to_sell && ( get_option( 'addtocart_or_buynow' ) != 1 ) ) { ?>
		</form>
	<?php
	} else {
		?>
		</div><!-- .cart -->
		<?php
	}
}
