<?php
if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
?>
<h2><?php _e( 'Sign-Up Forms', 'mailchimp-for-wp' ); ?> <a href="<?php echo admin_url( 'post-new.php?post_type=mc4wp-form' ); ?>" class="add-new-h2"><?php _e( 'Create New Form', 'mailchimp-for-wp' ); ?></a></h2>

	<?php $table->display(); ?>

    <form method="post" action="options.php">

		<?php settings_fields( 'mc4wp_form_settings' ); ?>

		<h3 class="mc4wp-title"><?php _e( 'General form settings', 'mailchimp-for-wp' ); ?></h3>
        <table class="form-table">
            <tr valign="top">
				<th scope="row"><label for="mc4wp_load_stylesheet_select"><?php _e( 'Load form styles (CSS)?' ,'mailchimp-for-wp' ); ?></label></th>
                <td class="nowrap">
                    <select name="mc4wp_form[css]" id="mc4wp_load_stylesheet_select">
						<option value="0" <?php selected( $opts['css'], 0 ); ?>><?php _e( 'No', 'mailchimp-for-wp' ); ?></option>
						<option value="default" <?php selected( $opts['css'], 'default' ); ?><?php selected( $opts['css'], 1 ); ?>><?php _e( 'Yes, load basic form styles', 'mailchimp-for-wp' ); ?></option>
						<option value="custom" <?php selected( $opts['css'], 'custom' ); ?>><?php _e( 'Yes, load my custom form styles', 'mailchimp-for-wp' ); ?></option>
						<optgroup label="<?php _e( 'Yes, load default form theme', 'mailchimp-for-wp' ); ?>">
							<option value="light" <?php selected( $opts['css'], 'light' ); ?>><?php _e( 'Light Theme', 'mailchimp-for-wp' ); ?></option>
							<option value="red" <?php selected( $opts['css'], 'red' ); ?>><?php _e( 'Red Theme', 'mailchimp-for-wp' ); ?></option>
							<option value="green" <?php selected( $opts['css'], 'green' ); ?>><?php _e( 'Green Theme', 'mailchimp-for-wp' ); ?></option>
							<option value="blue" <?php selected( $opts['css'], 'blue' ); ?>><?php _e( 'Blue Theme', 'mailchimp-for-wp' ); ?></option>
							<option value="dark" <?php selected( $opts['css'], 'dark' ); ?>><?php _e( 'Dark Theme', 'mailchimp-for-wp' ); ?></option>
							<option value="custom-color" <?php selected( $opts['css'], 'custom-color' ); ?>><?php _e( 'Custom Color Theme', 'mailchimp-for-wp' ); ?></option>
                        </optgroup>
                    </select>
                </td>
                <td class="desc">
					<?php printf( __( 'If you %screated a custom stylesheet%s and want it to be loaded, select "custom form styles". Otherwise, choose the basic formatting styles or one of the default themes.', 'mailchimp-for-wp' ), '<a href="' . admin_url( 'admin.php?page=mailchimp-for-wp-form-settings&tab=css-builder' ) .'">', '</a>' ); ?>
                </td>
            </tr>
			<tr id="mc4wp-custom-color" style="<?php if( $opts['css'] !== 'custom-color' ) { echo 'display: none;'; } ?>">
				<th><label for="mc4wp-custom-color-input"><?php _e( 'Select Color', 'mailchimp-for-wp' ); ?></label></th>
                <td>
					<input id="mc4wp-custom-color-input" name="mc4wp_form[custom_theme_color]" type="text" class="color-field" value="<?php echo esc_attr( $opts['custom_theme_color'] ); ?>" />
                </td>
            </tr>

        </table>

		<?php submit_button( __( 'Save all changes', 'mailchimp-for-wp' ) ); ?>

		<h3 class="mc4wp-title"><?php _e( 'Default MailChimp settings', 'mailchimp-for-wp' ); ?></h3>
		<p><?php _e( 'The following settings apply to <strong>all</strong> forms but can be overridden on a per-form basis.' ,'mailchimp-for-wp' ); ?></p>

        <table class="form-table">
            <tr valign="top">
				<th scope="row"><label for="mc4wp_form_hide_after_success"><?php _e( 'Double opt-in?' ,'mailchimp-for-wp' ); ?></label></th>
                <td class="nowrap">
                    <label>
						<input type="radio" name="mc4wp_form[double_optin]" value="1" <?php checked( $opts['double_optin'], 1 ); ?> />
						<?php _e( 'Yes', 'mailchimp-for-wp' ); ?>
                    </label> &nbsp;
                    <label>
						<input type="radio" name="mc4wp_form[double_optin]" value="0" <?php checked( $opts['double_optin'], 0 ); ?> />
						<?php _e( 'No', 'mailchimp-for-wp' ); ?>
                    </label>
                </td>
				<td class="desc"><?php _e( 'Select "yes" if you want people to confirm their email address before being subscribed (recommended)', 'mailchimp-for-wp' ); ?></td>
            </tr>
			<?php $enabled = ! $opts['double_optin']; ?>
			<tr id="mc4wp-send-welcome"  valign="top" <?php if( ! $enabled ) { ?>class="hidden"<?php } ?>>
				<th scope="row"><?php _e( 'Send Welcome Email?', 'mailchimp-for-wp' ); ?></th>
                <td class="nowrap">
					<label><input type="radio" name="mc4wp_form[send_welcome]" value="1" <?php if( $enabled ) { checked( $opts['send_welcome'], 1 ); } else { echo 'disabled'; } ?> /> <?php _e( 'Yes', 'mailchimp-for-wp' ); ?></label> &nbsp;
					<label><input type="radio" name="mc4wp_form[send_welcome]" value="0" <?php if( $enabled ) { checked( $opts['send_welcome'], 0 ); } else { echo 'disabled'; } ?> /> <?php _e( 'No', 'mailchimp-for-wp' ); ?></label>
                </td>
				<td class="desc"><?php _e( 'Select "yes" if you want to send your lists Welcome Email if a subscribe succeeds (only when double opt-in is disabled).' ,'mailchimp-for-wp' ); ?></td>
            </tr>
            <tr valign="top">
				<th scope="row"><?php _e( 'Update existing subscribers?', 'mailchimp-for-wp' ); ?></th>
                <td class="nowrap">
                    <label>
						<input type="radio" name="mc4wp_form[update_existing]" value="1" <?php checked( $opts['update_existing'], 1 ); ?> />
						<?php _e( 'Yes', 'mailchimp-for-wp' ); ?>
                    </label> &nbsp;
                    <label>
						<input type="radio" name="mc4wp_form[update_existing]" value="0" <?php checked( $opts['update_existing'], 0 ); ?> />
						<?php _e( 'No', 'mailchimp-for-wp' ); ?>
                    </label>
                </td>
				<td class="desc"><?php _e( 'Select "yes" if you want to update existing subscribers (instead of showing the "already subscribed" message).', 'mailchimp-for-wp' ); ?></td>
            </tr>
			<?php $enabled = $opts['update_existing']; ?>
			<tr id="mc4wp-replace-interests" valign="top" <?php if( ! $enabled ) { ?>class="hidden"<?php } ?>>
				<th scope="row"><?php _e( 'Replace interest groups?', 'mailchimp-for-wp' ); ?></th>
                <td class="nowrap">
                    <label>
						<input type="radio" name="mc4wp_form[replace_interests]" value="1" <?php if( $enabled ) { checked( $opts['replace_interests'], 1 ); } else { echo 'disabled'; } ?> />
						<?php _e( 'Yes', 'mailchimp-for-wp' ); ?>
                    </label> &nbsp;
                    <label>
						<input type="radio" name="mc4wp_form[replace_interests]" value="0" <?php if( $enabled ) { checked( $opts['replace_interests'], 0 ); } else { echo 'disabled'; } ?> />
						<?php _e( 'No', 'mailchimp-for-wp' ); ?>
                    </label>
                </td>
				<td class="desc"><?php _e( 'Select "yes" if you want to replace the interest groups with the groups provided instead of adding the provided groups to the member\'s interest groups (only when updating a subscriber).', 'mailchimp-for-wp' ); ?></td>
            </tr>
        </table>

		<h3 class="mc4wp-title"><?php _e( 'Default Form Settings', 'mailchimp-for-wp' ); ?></h3>
		<p><?php _e( 'The following settings apply to <strong>all</strong> forms but can be overridden on a per-form basis.', 'mailchimp-for-wp' ); ?></p>

        <table class="form-table">
            <tr valign="top">
				<th scope="row"><?php _e( 'Enable AJAX form submission?', 'mailchimp-for-wp' ); ?></th>
                <td class="nowrap">
					<label><input type="radio" name="mc4wp_form[ajax]" value="1" <?php checked( $opts['ajax'], 1 ); ?> /> <?php _e( 'Yes', 'mailchimp-for-wp' ); ?></label> &nbsp;
					<label><input type="radio" name="mc4wp_form[ajax]" value="0" <?php checked( $opts['ajax'], 0 ); ?> /> <?php _e( 'No', 'mailchimp-for-wp' ); ?></label>
                </td>
				<td class="desc"><?php _e( 'Select "yes" if you want to use AJAX (JavaScript) to submit forms.', 'mailchimp-for-wp' ); ?></td>
            </tr>
            <tr valign="top">
				<th scope="row"><label for="mc4wp_form_hide_after_success"><?php _e( 'Hide form after a successful sign-up?', 'mailchimp-for-wp' ); ?></label></th>
                <td class="nowrap">
					<label><input type="radio" name="mc4wp_form[hide_after_success]" value="1" <?php checked( $opts['hide_after_success'], 1 ); ?> /> <?php _e( 'Yes', 'mailchimp-for-wp' ); ?></label> &nbsp;
					<label><input type="radio" name="mc4wp_form[hide_after_success]" value="0" <?php checked( $opts['hide_after_success'], 0 ); ?> /> <?php _e( 'No', 'mailchimp-for-wp' ); ?></label>
                </td>
				<td class="desc"><?php _e( 'Select "yes" to hide the form fields after a successful sign-up.', 'mailchimp-for-wp' ); ?></td>
            </tr>
            <tr valign="top">
				<th scope="row"><label for="mc4wp_form_redirect"><?php _e( 'Redirect to URL after successful sign-ups', 'mailchimp-for-wp' ); ?></label></th>
                <td colspan="2">
					<input type="text" class="widefat" name="mc4wp_form[redirect]" id="mc4wp_form_redirect" value="<?php echo esc_attr( $opts['redirect'] ); ?>" />
	                <p class="help"><?php _e( 'Leave empty for no redirect. Otherwise, use complete (absolute) URLs, including <code>http://</code>.', 'mailchimp-for-wp' ); ?></p>
                </td>
            </tr>
        </table>

		<h3 class="mc4wp-title"><?php _e( 'Default Messages', 'mailchimp-for-wp' ); ?></h3>
        <table class="form-table">
	        <tr valign="top">
		        <th scope="row"><label for="mc4wp_form_text_subscribed"><?php _e( 'Successfully subscribed', 'mailchimp-for-wp' ); ?></label></th>
		        <td colspan="2" >
			        <input type="text" class="widefat" id="mc4wp_form_text_subscribed" name="mc4wp_form[text_subscribed]" value="<?php echo esc_attr( $opts['text_subscribed'] ); ?>" required />
			        <p class="help"><?php _e( 'The text that shows when an email address is successfully subscribed to the selected list(s).', 'mailchimp-for-wp' ); ?></p>
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><label for="mc4wp_form_text_invalid_email"><?php _e( 'Invalid email address', 'mailchimp-for-wp' ); ?></label></th>
		        <td colspan="2" >
			        <input type="text" class="widefat" id="mc4wp_form_text_invalid_email" name="mc4wp_form[text_invalid_email]" value="<?php echo esc_attr( $opts['text_invalid_email'] ); ?>" required />
			        <p class="help"><?php _e( 'The text that shows when an invalid email address is given.', 'mailchimp-for-wp' ); ?></p>
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><label for="mc4wp_form_text_required_field_missing"><?php _e( 'Required field missing', 'mailchimp-for-wp' ); ?></label></th>
		        <td colspan="2" >
			        <input type="text" class="widefat" id="mc4wp_form_text_required_field_missing" name="mc4wp_form[text_required_field_missing]" value="<?php echo esc_attr( $opts['text_required_field_missing'] ); ?>" required />
			        <p class="help"><?php _e( 'The text that shows when a required field for the selected list(s) is missing.', 'mailchimp-for-wp' ); ?></p>
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><label for="mc4wp_form_text_already_subscribed"><?php _e( 'Already subscribed', 'mailchimp-for-wp' ); ?></label></th>
		        <td colspan="2" >
			        <input type="text" class="widefat" id="mc4wp_form_text_already_subscribed" name="mc4wp_form[text_already_subscribed]" value="<?php echo esc_attr( $opts['text_already_subscribed'] ); ?>" required />
			        <p class="help"><?php _e( 'The text that shows when the given email is already subscribed to the selected list(s).', 'mailchimp-for-wp' ); ?></p>
		        </td>
	        </tr>
	        <?php if( true === $this->has_captcha_plugin ) { ?>
		        <tr valign="top">
			        <th scope="row"><label for="mc4wp_form_text_invalid_captcha"><?php _e( 'Invalid CAPTCHA', 'mailchimp-for-wp' ); ?></label></th>
			        <td colspan="2" ><input type="text" class="widefat" id="mc4wp_form_text_invalid_captcha" name="mc4wp_form[text_invalid_captcha]" value="<?php echo esc_attr( $opts['text_invalid_captcha'] ); ?>" required /></td>
		        </tr>
	        <?php } ?>
	        <tr valign="top">
		        <th scope="row"><label for="mc4wp_form_text_error"><?php _e( 'General error' ,'mailchimp-for-wp' ); ?></label></th>
		        <td colspan="2" >
			        <input type="text" class="widefat" id="mc4wp_form_text_error" name="mc4wp_form[text_error]" value="<?php echo esc_attr( $opts['text_error'] ); ?>" required />
			        <p class="help"><?php _e( 'The text that shows when a general error occured.', 'mailchimp-for-wp' ); ?></p>
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><label for="mc4wp_form_text_unsubscribed"><?php _e( 'Unsubscribed', 'mailchimp-for-wp' ); ?></label></th>
		        <td colspan="2" >
			        <input type="text" class="widefat" id="mc4wp_form_text_unsubscribed" name="mc4wp_form[text_unsubscribed]" value="<?php echo esc_attr( $opts['text_unsubscribed'] ); ?>" required />
			        <p class="help"><?php _e( 'When using the unsubscribe method, this is the text that shows when the given email address is successfully unsubscribed from the selected list(s).', 'mailchimp-for-wp' ); ?></p>
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><label for="mc4wp_form_text_not_subscribed"><?php _e( 'Not subscribed', 'mailchimp-for-wp' ); ?></label></th>
		        <td colspan="2" >
			        <input type="text" class="widefat" id="mc4wp_form_text_not_subscribed" name="mc4wp_form[text_not_subscribed]" value="<?php echo esc_attr( $opts['text_not_subscribed'] ); ?>" required />
			        <p class="help"><?php _e( 'When using the unsubscribe method, this is the text that shows when the given email address is not on the selected list(s).', 'mailchimp-for-wp' ); ?></p>
		        </td>
	        </tr>
	        <tr>
		        <th></th>
		        <td colspan="2">
			        <p class="help"><?php printf( __( 'HTML tags like %s are allowed in the message fields.', 'mailchimp-for-wp' ), '<code>' . esc_html( '<strong><em><a>' ) . '</code>' ); ?></p>
		        </td>
	        </tr>
        </table>

		<?php submit_button(); ?>

    </form>
