<?php
/**
 * Renders number field
 *
 * @version 4.2
 *
 */

$option_id = "tribe-tickets-meta_{$this->slug}" . ( $attendee_id ? '_' . $attendee_id : '' );
?>
<div class="tribe-tickets-meta tribe-tickets-meta-number <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>">
	<label for="<?php echo esc_attr( $option_id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	<input <?php disabled( $this->is_restricted( $attendee_id ) ); ?> type="number" id="<?php echo esc_attr( $option_id ); ?>" class="ticket-meta" name="tribe-tickets-meta[<?php echo $attendee_id ?>][<?php echo esc_attr( $this->slug ); ?>]" value="<?php echo esc_attr( $value ); ?>" <?php echo $required ? 'required' : ''; ?>>
</div>
