<?php

$options = '';

if ( $extra && ! empty( $extra['options'] ) ) {
	$options = implode( "\n", $extra['options'] );
}

?>
<div class="tribe-tickets-input tribe-tickets-input-textarea">
	<label for="tickets_attendee_info_field">Options (one per line)</label>
	<textarea name="tribe-tickets-input[<?php echo $field_id; ?>][extra][options]" class="ticket_field" value="" rows="5"><?php echo esc_textarea( $options ); ?></textarea>
</div>
