<?php
$multiline = isset( $extra['multiline'] ) ? $extra['multiline'] : '';
?>
<div class="tribe-tickets-input tribe-tickets-input-checkbox tribe-tickets-required">
	<label for="tickets_attendee_info_field" class="prompt">
		<input
			type="checkbox"
			name="tribe-tickets-input[<?php echo esc_attr( $field_id ); ?>][extra][multiline]"
			class="ticket_field"
			value="yes"
			<?php checked( 'yes', $multiline ); ?>
		>
		<?php echo esc_html_e( 'Multi-line text field?', 'events-tickets-plus' ); ?>
	</label>
</div>
