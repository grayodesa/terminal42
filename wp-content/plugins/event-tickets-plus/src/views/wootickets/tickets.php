<?php
global $woocommerce;

$is_there_any_product         = false;
$is_there_any_product_to_sell = false;

ob_start();
?>
<form action="<?php echo esc_url( $woocommerce->cart->get_cart_url() ) ?>" class="cart" method="post" enctype='multipart/form-data'>
	<h2 class="tribe-events-tickets-title"><?php esc_html_e( 'Tickets', 'event-tickets-plus' ) ?></h2>

	<table width="100%" class="tribe-events-tickets">
		<?php
		foreach ( $tickets as $ticket ) {

			global $product;

			if ( class_exists( 'WC_Product_Simple' ) ) {
				$product = new WC_Product_Simple( $ticket->ID );
			} else {
				$product = new WC_Product( $ticket->ID );
			}

			if ( $ticket->date_in_range( current_time( 'timestamp' ) ) ) {

				$is_there_any_product = true;

				echo sprintf( '<input type="hidden" name="product_id[]" value="%d">', esc_attr( $ticket->ID ) );

				echo '<tr>';
				echo '<td class="woocommerce">';

				if ( $product->is_in_stock() ) {
					// Max quantity will be left open if backorders allowed, restricted to 1 if the product is
					// constrained to be sold individually or else set to the available stock quantity
					$max_quantity = $product->backorders_allowed() ? '' : $product->get_stock_quantity();
					$max_quantity = $product->is_sold_individually() ? 1 : $max_quantity;

					woocommerce_quantity_input( array(
						'input_name'  => 'quantity_' . $ticket->ID,
						'input_value' => 1,
						'min_value'   => 1,
						'max_value'   => $max_quantity,
					) );

					$is_there_any_product_to_sell = true;

					$remaining = $ticket->remaining();

					if ( $remaining ) {
						?>
						<span class="tribe-tickets-remaining">
							<?php
							echo sprintf( esc_html__( '%1$s из %2$s доступны', 'event-tickets-plus' ), esc_html( $remaining ), esc_html( $ticket->original_stock() ) );
							?>
						</span>
						<?php
					}

					do_action( 'wootickets_tickets_after_quantity_input', $ticket, $product );
				} else {
					echo '<span class="tickets_nostock">' . esc_html__( 'Билеты закончились!', 'event-tickets-plus' ) . '</span>';
				}
				echo '</td>';

				echo '<td class="tickets_name">';
				echo $ticket->name;
				echo '</td>';

				echo '<td class="tickets_price">';
				echo $this->get_price_html( $product );
				echo '</td>';

				echo '<td class="tickets_description">';
				echo $ticket->description;
				echo '</td>';

				echo '</tr>';
			}
		}

		if ( $is_there_any_product_to_sell ) {
			?>
			<tr>
				<td colspan="4" class="woocommerce add-to-cart">

					<button type="submit" name="wootickets_process" value="1"
					        class="button alt"><?php esc_html_e( 'Купить билет', 'event-tickets-plus' );?></button>
				</td>
			</tr>
			<?php
		} ?>
	</table>
</form>

<?php
$content = ob_get_clean();
if ( $is_there_any_product ) {
	echo $content;
}
