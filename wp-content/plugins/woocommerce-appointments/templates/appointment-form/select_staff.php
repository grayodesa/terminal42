<?php
wp_enqueue_script( 'wc-appointments-select2' );
wp_enqueue_script( 'wc-appointments-staff-picker' );
extract( $field );
?>
<p class="form-field form-field-wide <?php echo implode( ' ', $class ); ?>">
	<label for="<?php echo $name; ?>"><?php echo $label; ?>:</label>
	<select name="<?php echo $name; ?>" id="<?php echo $name; ?>">
		<option value=""><?php _e( '&mdash; No Preference &mdash;', 'woocommerce-appointments' ); ?></option>
		<?php foreach ( $options as $key => $value ) : ?>
			<?php
			$get_avatar = get_avatar( $key, 24 );
			preg_match( "@src='([^']+)'@" , $get_avatar, $match ); # single quote
			$avatar = array_pop( $match );
			preg_match( '@src="([^"]+)"@' , $get_avatar, $match ); # double quote
			$avatar2 = array_pop( $match );
			
			/* Also works, but avatar WP plugins do not support get_avatar_url() yet
			$avatar = get_avatar_url( $key, array(
				'size'  => 24,
			));
			$data_avatar = $avatar ? 'data-avatar="'. $avatar .'"' : '';
			*/
			$data_avatar = $avatar ? 'data-avatar="'. $avatar .'"' : ( $avatar2 ? 'data-avatar="'. $avatar2 .'"' : '' );
			?>
			<option value="<?php echo $key; ?>"<?php echo $data_avatar; ?>><?php echo $value; ?></option>
		<?php endforeach; ?>
	</select>
</p>