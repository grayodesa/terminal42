<?php
/**
 * Renders text field
 *
 * @version 4.1
 *
 */
$multiline = isset( $field['extra'] ) && isset( $field['extra']['multiline'] ) ? $field['extra']['multiline'] : '';
?>
<div class="tribe-tickets-meta tribe-tickets-meta-text <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>">
	<label for="tribe-tickets-meta_<?php echo esc_attr( $this->slug ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
	<?php
	if ( $multiline ) {
		?>
		<textarea
			id="tribe-tickets-meta_<?php echo esc_attr( $this->slug ); ?>"
			class="ticket-meta"
			name="tribe-tickets-meta[][<?php echo esc_attr( $this->slug ); ?>]"
			<?php echo $required ? 'required' : ''; ?>
		><?php echo esc_textarea( $value ); ?></textarea>
		<?php
	} else {
		?>
		<input
			type="text"
			id="tribe-tickets-meta_<?php echo esc_attr( $this->slug ); ?>"
			class="ticket-meta"
			name="tribe-tickets-meta[][<?php echo esc_attr( $this->slug ); ?>]"
			value="<?php echo esc_attr( $value ); ?>"
			<?php echo $required ? 'required' : ''; ?>
		>
		<?php
	}
	?>
</div>
