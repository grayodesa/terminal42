<?php
/**
 * Renders select field
 *
 * @version 4.2
 *
 */
$options = null;
if ( isset( $field['extra'] ) && ! empty( $field['extra']['options'] ) ) {
	$options = $field['extra']['options'];
}

if ( ! $options ) {
	return;
}

$option_id = "tribe-tickets-meta_{$this->slug}" . ( $attendee_id ? '_' . $attendee_id : '' );

?>
<div class="tribe-tickets-meta tribe-tickets-meta-select <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>">
	<label for="<?php echo esc_attr( $option_id ); ?>" class="tribe-tickets-meta-field-header"><?php echo esc_html( $field['label'] ); ?></label>
	<select	<?php disabled( $this->is_restricted( $attendee_id ) ); ?> id="<?php echo esc_attr( $option_id ); ?>" class="ticket-meta" name="tribe-tickets-meta[<?php echo $attendee_id ?>][<?php echo esc_attr( $this->slug ); ?>]" <?php echo $required ? 'required' : ''; ?>>
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
