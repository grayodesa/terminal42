<?php
global $edd_options;

$is_there_any_product = false;
$is_there_any_product_to_sell = false;
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

					echo sprintf( '<input type="hidden" name="product_id[]"" value="%d">', esc_attr( $ticket->ID ) );

					echo '<tr>';
					echo '<td width="75" class="edd">';


					if ( $stock->available_units( $product->ID ) ) {

						$remaining = $ticket->remaining();

						$max = '';
						if ( $ticket->managing_stock() ) {
							$max = 'max="' . absint( $remaining ) . '"';
						}

						echo '<input type="number" class="edd-input" min="0" ' . $max . ' name="quantity_' . esc_attr( $ticket->ID ) . '" value="0"/>';

						$is_there_any_product_to_sell = true;

						if ( $remaining ) {
							?>
							<span class="tribe-tickets-remaining">
								<?php
								echo sprintf( esc_html__( '%1$s out of %2$s available', 'event-tickets-plus' ), esc_html( $remaining ), esc_html( $ticket->original_stock() ) );
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
				}
			}
			?>

		<?php if ( $is_there_any_product_to_sell ) :
			$color = isset( $edd_options[ 'checkout_color' ] ) ? $edd_options[ 'checkout_color' ] : 'gray';
			$color = ( $color == 'inherit' ) ? '' : $color;
			?>
			<tr>
				<td colspan="4" class="eddtickets-add">

					<button type="submit" class="edd-submit button <?php echo esc_attr( $color ); ?>"><?php esc_html_e( 'Add to cart', 'event-tickets-plus' );?></button>
				</td>
			</tr>
		<?php endif ?>
	</table>
</form>

<?php
$contents = ob_get_clean();
if ( $is_there_any_product ) {
	echo $contents;
}
