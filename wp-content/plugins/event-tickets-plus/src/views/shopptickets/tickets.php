<?php
$is_there_any_product         = false;
$is_there_any_product_to_sell = false;

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
				echo '<td width="75" class="shopp">';

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
			}
		}

		if ( $is_there_any_product ) {
			?>
			<h2 class="tribe-events-tickets-title"><?php esc_html_e( 'Tickets', 'event-tickets-plus' );?></h2>
			<?php if ( $is_there_any_product_to_sell ) { ?>
				<tr>
					<td colspan="4" class='shopp'>
						<input type="hidden" name="cart" value="add" />
						<button type="submit" class="button alt"><?php esc_html_e( 'Add to cart', 'event-tickets-plus' );?></button>
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
}
