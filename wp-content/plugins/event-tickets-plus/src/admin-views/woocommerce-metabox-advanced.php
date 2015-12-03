<?php include dirname( __FILE__ ) . '/price-fields.php'; ?>
<tr class="<?php $this->tr_class(); ?>">
	<td><label for="ticket_woo_stock"><?php esc_html_e( 'Stock:', 'event-tickets-plus' ); ?></label></td>
	<td>
		<input type='text' id='ticket_woo_stock' name='ticket_woo_stock' class="ticket_field" size='7'
		       value='<?php echo esc_attr( $stock ); ?>'/>
		<p class="description"><?php esc_html_e( "(Total available # of this ticket type. Once they're gone, ticket type is sold out.)", 'event-tickets-plus' ); ?></p>
	</td>
</tr>
<?php
if ( apply_filters( 'tribe_tickets_default_purchase_limit', 0 ) ) {
	?>
	<tr class="<?php $this->tr_class(); ?>">
		<td><label for="ticket_purchase_limit"><?php esc_html_e( 'Purchase limit:', 'event-tickets-plus' ); ?></label></td>
		<td>
			<input type='text' id='ticket_purchase_limit' name='ticket_purchase_limit' class="ticket_field" size='7' data-default-value='<?php echo esc_attr( $purchase_limit ); ?>'/>
			<p class="description"><?php esc_html_e( 'The maximum number of tickets per order. (0 means there\'s no limit)', 'event-tickets-plus' ); ?></p>
		</td>
	</tr>
	<?php
}

if ( apply_filters( 'tribe_events_tickets_woo_display_sku', true ) ) {
	?>
	<tr class="<?php $this->tr_class(); ?>">
		<td><label for="ticket_woo_sku"><?php esc_html_e( 'SKU:', 'event-tickets-plus' ); ?></label></td>
		<td>
			<input type='text' id='ticket_woo_sku' name='ticket_woo_sku' class="ticket_field" size='7' value='<?php echo esc_attr( $sku ); ?>'/>
			<p class="description"><?php esc_html_e( "(A unique identifying code for each ticket type you're selling)", 'event-tickets-plus' ); ?></p>
		</td>
	</tr>
	<?php
}
if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
	?>
	<tr class="<?php $this->tr_class(); ?>">
		<td colspan="2" class="tribe_sectionheader updated">
			<p>
				<?php esc_html_e( 'Selling tickets for recurring events', 'event-tickets-plus' ); ?> <span id="selling-tickets-info" class="target dashicons dashicons-editor-help bumpdown-trigger"></span>
			</p>
			<div class="bumpdown" data-trigger="selling-tickets-info">
				<?php esc_html_e( 'Currently, WooTickets will only show up on the frontend once per full event. For PRO users this means the same ticket will appear across all events in the series. Please configure your events accordingly.', 'event-tickets-plus' ); ?>
			</div>
		</td>
	</tr>
	<?php
}
