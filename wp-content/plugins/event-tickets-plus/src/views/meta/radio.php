<?php
/**
 * Renders radio field
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

?>
<div class="tribe-tickets-meta tribe-tickets-meta-radio <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>">
	<header class="tribe-tickets-meta-label">
		<?php echo esc_html( $field['label'] ); ?>
	</header>
	<?php
	foreach ( $options as $option ) {
		$option_slug = sanitize_title( $option );
		$option_id = "tribe-tickets-meta_{$this->slug}" . ( $attendee_id ? '_' . $attendee_id : '' ) . "_{$option_slug}" ;
		?>
		<label for="<?php echo esc_attr( $option_id ); ?>" class="tribe-tickets-meta-field-header">
			<input
				type="radio"
				id="<?php echo esc_attr( $option_id ); ?>"
				class="ticket-meta"
				name="tribe-tickets-meta[<?php echo $attendee_id ?>][<?php echo esc_attr( $this->slug ); ?>]"
				value="<?php echo esc_attr( $option ); ?>"
				<?php checked( $option, $value ); ?>
				<?php disabled( $this->is_restricted( $attendee_id ) ); ?>
			>
			<span class="tribe-tickets-meta-option-label">
				<?php echo esc_html( $option ); ?>
			</span>
		</label>
		<?php
	}
	?>
</div>
