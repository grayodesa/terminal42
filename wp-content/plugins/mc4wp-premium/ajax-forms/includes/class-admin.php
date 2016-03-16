<?php

class MC4WP_AJAX_Forms_Admin {

	/**
	 * @param MC4WP_Plugin $plugin
	 */
	public function __construct( MC4WP_Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Add hooks
	 */
	public function add_hooks() {
		add_action( 'mc4wp_admin_form_after_behaviour_settings_rows', array( $this, 'show_setting' ), 10, 2 );
	}

	/**
	 * @param            $opts
	 * @param MC4WP_Form $form
	 */
	public function show_setting( $opts, $form ) {
		?>
		<tr valign="top">
			<th scope="row"><?php _e( 'Enable AJAX form submission?', 'mailchimp-for-wp' ); ?></th>
			<td>
				<label>
					<input type="radio" name="mc4wp_form[settings][ajax]" value="1" <?php checked( $opts['ajax'], 1 ); ?> />
					<?php _e( 'Yes', 'mailchimp-for-wp' ); ?>
				</label> &nbsp;
				<label>
					<input type="radio" name="mc4wp_form[settings][ajax]" value="0" <?php checked( $opts['ajax'], 0 ); ?> />
					<?php _e( 'No', 'mailchimp-for-wp' ); ?>
				</label> &nbsp;
				<p class="help"><?php _e( 'Select "yes" if you want to use AJAX (JavaScript) to submit forms.', 'mailchimp-for-wp' ); ?></p>
			</td>
		</tr>
		<?php
	}

}