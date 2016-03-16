<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
?>
<div class="woocommerce_product_addon wc-metabox closed">
	<h3>
		<button type="button" class="remove_addon button"><?php _e( 'Remove', 'woocommerce-appointments' ); ?></button>
		<div class="handlediv" title="<?php _e( 'Click to toggle', 'woocommerce-appointments' ); ?>"></div>
		<strong><?php _e( 'Group', 'woocommerce-appointments' ); ?> <span class="group_name"><?php if ( $addon['name'] ) echo '"' . esc_attr( $addon['name'] ) . '"'; ?></span> &mdash; </strong>
		<select name="product_addon_type[<?php echo $loop; ?>]" class="product_addon_type">
			<option <?php selected('checkbox', $addon['type']); ?> value="checkbox"><?php _e( 'Checkboxes', 'woocommerce-appointments' ); ?></option>
			<option <?php selected('radiobutton', $addon['type']); ?> value="radiobutton"><?php _e( 'Radio buttons', 'woocommerce-appointments' ); ?></option>
			<option <?php selected('select', $addon['type']); ?> value="select"><?php _e( 'Select box', 'woocommerce-appointments' ); ?></option>
			<option <?php selected('custom_textarea', $addon['type']); ?> value="custom_textarea"><?php _e( 'Custom input (textarea)', 'woocommerce-appointments' ); ?></option>
			<option <?php selected('file_upload', $addon['type']); ?> value="file_upload"><?php _e( 'File upload', 'woocommerce-appointments' ); ?></option>
			<option <?php selected('custom_price', $addon['type']); ?> value="custom_price"><?php _e( 'Custom price input', 'woocommerce-appointments' ); ?></option>
            <option <?php selected('input_multiplier', $addon['type']); ?> value="input_multiplier"><?php _e( 'Custom Input Multiplier', 'woocommerce-appointments' ); ?></option>
			<optgroup label="<?php esc_attr_e( 'Custom input (text)', 'woocommerce-appointments' ); ?>">
				<option <?php selected( 'custom', $addon['type']); ?> value="custom"><?php _e( 'Any text', 'woocommerce-appointments' ); ?></option>
				<option <?php selected( 'custom_letters_only', $addon['type'] ); ?> value="custom_letters_only"><?php _e( 'Only letters', 'woocommerce-appointments' ); ?></option>
				<option <?php selected( 'custom_digits_only', $addon['type'] ); ?> value="custom_digits_only"><?php _e( 'Only numbers', 'woocommerce-appointments' ); ?></option>
				<option <?php selected( 'custom_letters_or_digits', $addon['type'] ); ?> value="custom_letters_or_digits"><?php _e( 'Only letters and numbers', 'woocommerce-appointments' ); ?></option>
				<option <?php selected( 'custom_email', $addon['type'] ); ?> value="custom_email"><?php _e( 'Email address', 'woocommerce-appointments' ); ?></option>
			</optgroup>
		</select>
		<input type="hidden" name="product_addon_position[<?php echo $loop; ?>]" class="product_addon_position" value="<?php echo $loop; ?>" />
	</h3>
	<table cellpadding="0" cellspacing="0" class="wc-metabox-content">
		<tbody>
			<tr>
				<td class="addon_name" width="50%">
					<label for="addon_name_<?php echo $loop; ?>"><?php _e( 'Group Name', 'woocommerce-appointments' ); ?></label>
					<input type="text" id="addon_name_<?php echo $loop; ?>" name="product_addon_name[<?php echo $loop; ?>]" value="<?php echo esc_attr( $addon['name'] ) ?>" />
				</td>
				<td class="addon_required" width="50%">
					<label for="addon_required_<?php echo $loop; ?>"><?php _e( 'Required fields?', 'woocommerce-appointments' ); ?></label>
					<input type="checkbox" id="addon_required_<?php echo $loop; ?>" name="product_addon_required[<?php echo $loop; ?>]" <?php checked( $addon['required'], 1 ) ?> />
				</td>
			</tr>
			<tr>
				<td class="addon_description" colspan="2">
					<label for="addon_description_<?php echo $loop; ?>"><?php _e( 'Group Description', 'woocommerce-appointments' ); ?></label>
					<textarea cols="20" id="addon_description_<?php echo $loop; ?>" rows="3" name="product_addon_description[<?php echo $loop; ?>]"><?php echo esc_textarea( $addon['description'] ) ?></textarea>
				</td>
			</tr>
			<?php do_action( 'woocommerce_product_addons_panel_before_options', $post, $addon, $loop ); ?>
			<tr>
				<td class="data" colspan="3">
					<table cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th><?php _e( 'Option Label', 'woocommerce-appointments' ); ?></th>
								<th class="price_column"><?php _e( 'Option Price', 'woocommerce-appointments' ); ?></th>
								<th class="minmax_column"><?php _e( 'Min', 'woocommerce-appointments' ); ?></th>
								<th class="minmax_column"><?php _e( 'Max', 'woocommerce-appointments' ); ?></th>
								<?php do_action( 'woocommerce_product_addons_panel_head_option', $post, $addon, $loop ); ?>
								<th width="1%"></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5"><button type="button" class="add_addon_option button"><?php _e( 'New&nbsp;Option', 'woocommerce-appointments' ); ?></button></td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							foreach ( $addon['options'] as $option )
								include( 'html-addon-option.php' );
							?>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>
