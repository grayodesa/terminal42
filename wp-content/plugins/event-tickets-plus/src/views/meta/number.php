<?php
/**
 * Renders number field
 *
 * @version 4.1
 *
 */
?>
<div class="tribe-tickets-meta tribe-tickets-meta-number <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>">
	<label for="tribe-tickets-meta_<?php echo esc_attr( $this->slug ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	<input type="number" id="tribe-tickets-meta_<?php echo esc_attr( $this->slug ); ?>" class="ticket-meta" name="tribe-tickets-meta[][<?php echo esc_attr( $this->slug ); ?>]" value="<?php echo esc_attr( $value ); ?>" <?php echo $required ? 'required' : ''; ?>>
</div>
