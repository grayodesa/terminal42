<?php
/**
 * Renders select field
 *
 * @version 4.1
 *
 */
$options = null;
if ( isset( $field['extra'] ) && ! empty( $field['extra']['options'] ) ) {
	$options = $field['extra']['options'];
}

if ( ! $options ) {
	return;
}

?>
<div class="tribe-tickets-meta tribe-tickets-meta-select <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>">
	<label for="tribe-tickets-meta_<?php echo esc_attr( $this->slug ); ?>" class="tribe-tickets-meta-field-header"><?php echo esc_html( $field['label'] ); ?></label>
	<select id="tribe-tickets-meta_<?php echo esc_attr( $this->slug ); ?>" class="ticket-meta" name="tribe-tickets-meta[][<?php echo esc_attr( $this->slug ); ?>]" <?php echo $required ? 'required' : ''; ?>>
		<option></option>
		<?php
		foreach ( $options as $option ) {
			?>
			<option <?php selected( $option, $value ); ?>><?php echo esc_html( $option ); ?></option>
			<?php
		}
		?>
	</select>
</div>
