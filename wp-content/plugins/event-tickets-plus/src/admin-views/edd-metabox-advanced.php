<?php
/**
 * @var Tribe__Tickets_Plus__Commerce__EDD__Main $this
 * @var string $global_stock_mode
 * @var int $global_stock_cap
 */

include dirname( __FILE__ ) . '/price-fields.php';
?>

<?php if ( $this->supports_global_stock() ): ?>
	<tr class="<?php $this->tr_class(); ?> global-stock-mode">
		<td><label for="ticket_edd_global_stock"><?php esc_html_e( 'Global stock mode:', 'event-tickets-plus' ); ?></label></td>
		<td>
			<?php echo $this->global_stock_mode_selector( $global_stock_mode ); ?>
		</td>
	</tr>

	<tr class="<?php $this->tr_class(); ?> global-stock-mode sales-cap-field">
		<td><label for="ticket_edd_global_stock_cap"><?php esc_html_e( 'Cap sales:', 'event-tickets-plus' ); ?></label></td>
		<td>
			<input type='text' id='ticket_edd_global_stock_cap' name='ticket_edd_global_stock_cap' class="ticket_field" size='7'
			       value='<?php echo esc_attr( $global_stock_cap); ?>'/>
			<p class="description"><?php esc_html_e( "(This is the maximum allowed number of sales for this ticket.)", 'event-tickets-plus' ); ?></p>
		</td>
	</tr>
<?php endif; ?>

<tr class="<?php $this->tr_class(); ?> stock">
	<td><label for="ticket_edd_stock"><?php esc_html_e( 'Stock:', 'event-tickets-plus' ); ?></label></td>
	<td>
		<input type='text' id='ticket_edd_stock' name='ticket_edd_stock' class="ticket_field" size='7'
		       value='<?php echo esc_attr( $stock ); ?>'/>
		<p class="description"><?php esc_html_e( "(Total available # of this ticket type. Once they're gone, ticket type is sold out.)", 'event-tickets-plus' ); ?></p>
	</td>
</tr>

<tr class="<?php $this->tr_class(); ?>">
	<td><label for="ticket_edd_sku"><?php esc_html_e( 'SKU:', 'event-tickets-plus' ); ?></label></td>
	<td>
		<input type='text' id='ticket_edd_sku' name='ticket_edd_sku' class="ticket_field" size='7'
		       value='<?php echo esc_attr( $sku ); ?>'/>
		<p class="description"><?php esc_html_e( "(A unique identifying code for each ticket type you're selling)", 'event-tickets-plus' ); ?></p>
	</td>

</tr>
<?php
if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
	?>
	<tr class="<?php $this->tr_class(); ?>">
		<td colspan="2" class="tribe_sectionheader updated">
			<p>
				<?php esc_html_e( 'Selling tickets for recurring events', 'event-tickets-plus' ); ?> <span id="selling-tickets-info" class="target dashicons dashicons-editor-help bumpdown-trigger"></span>
			</p>
			<div class="bumpdown" data-trigger="selling-tickets-info">
				<?php
					if ( is_admin() ) {
						esc_html_e( 'Currently, eddTickets will only show up on the frontend once per full event. For PRO users this means the same ticket will appear across all events in the series. Please configure your events accordingly.', 'event-tickets-plus' );
					} else {
						esc_html_e( 'If you are creating a recurring event, Tickets will only show up once per Event Series, meaning that the same ticket will apper across all events. Please configure your events accordingly.', 'event-tickets-plus' );
					}
				?>
			</div>
		</td>
	</tr>
	<?php
}
