<?php
if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

	// Use nonce for verification
		wp_nonce_field( 'mc4wp_save_form', '_mc4wp_nonce' );
?>

<p class="mc4wp-notice" style="display: none;"></p>

<h4 class="mc4wp-title"><?php _e( 'Lists this form subscribes to', 'mailchimp-for-wp' ); ?></h4>
<p><?php
if( ! $lists || empty( $lists ) ) {
	printf( __( 'No lists found, <a href="%s">are you connected to MailChimp</a>?', 'mailchimp-for-wp' ), admin_url( 'admin.php?page=mailchimp-for-wp' ) );
} else { ?>

	<ul id="mc4wp-lists">
		<?php foreach($lists as $list) {
			?><li><label><input type="checkbox" name="mc4wp_form[lists][<?php echo esc_attr( $list->id ); ?>]" value="<?php echo esc_attr( $list->id ); ?>"  <?php checked( array_key_exists( $list->id, $individual_form_settings['lists'] ), true ); ?>> <?php echo esc_html( $list->name ); ?></label></li><?php
} ?>
	</ul>
	<?php
} ?></p>

<div id="mc4wp-fw">

	<h4 class="mc4wp-title">
		<label for="mc4wp-fw-mailchimp-fields"><?php _e( 'Add a new field', 'mailchimp-for-wp' ); ?></label>
	</h4>
	<p>
		<select class="widefat" id="mc4wp-fw-mailchimp-fields">
			<option class="default" value="" disabled selected><?php _e( 'Select MailChimp field..' , 'mailchimp-for-wp' ); ?></option>
			<optgroup label="MailChimp Fields" class="merge-fields"></optgroup>
			<optgroup label="Groupings" class="groupings"></optgroup>
			<optgroup label="Other" class="other">
				<option class="default" value="submit"><?php _e( 'Submit button' ,'mailchimp-for-wp' ); ?></option>
				<option class="default" value="_lists"><?php _e( 'List choice' ,'mailchimp-for-wp' ); ?></option>
				<option class="default" value="_action"><?php _e( 'Subscribe / unsubscribe action' ,'mailchimp-for-wp' ); ?></option>
			</optgroup>
		</select>
	</p>

	<div id="mc4wp-fw-fields">

		<p class="row label">
			<label for="mc4wp-fw-label"><?php _e( 'Label', 'mailchimp-for-wp' ); ?> <small><?php _e( '(optional)', 'mailchimp-for-wp' ); ?></small></label>
			<input class="widefat" type="text" id="mc4wp-fw-label" />
		</p>

		<p class="row placeholder">
			<label for="mc4wp-fw-placeholder"><?php _e( 'Placeholder', 'mailchimp-for-wp' ); ?> <small><?php _e( '(optional)', 'mailchimp-for-wp' ); ?></small></label>
			<input class="widefat" type="text" id="mc4wp-fw-placeholder" />
		</p>

		<p class="row value">
			<label for="mc4wp-fw-value"><span id="mc4wp-fw-value-label"><?php _e( 'Initial value', 'mailchimp-for-wp' ); ?> <small><?php _e( '(optional)', 'mailchimp-for-wp' ); ?></small></span></label>
			<input class="widefat" type="text" id="mc4wp-fw-value" />
		</p>

		<p class="row values" id="mc4wp-fw-values">
			<label for="mc4wp-fw-values"><?php _e( 'Labels', 'mailchimp-for-wp' ); ?> <small><?php _e( '(leave empty to hide)', 'mailchimp-for-wp' ); ?></small></label>
		</p>
		

		<p class="row wrap-p">
			<label for="mc4wp-fw-wrap-p"><input type="checkbox" id="mc4wp-fw-wrap-p" value="1" checked /> <?php printf( __( 'Wrap in paragraph %s tags?', 'mailchimp-for-wp' ), '(<code>&lt;p&gt;</code>)' ); ?></label>
		</p>

		<p class="row required">
			<label for="mc4wp-fw-required"><input type="checkbox" id="mc4wp-fw-required" value="1" /> <?php _e( 'Required field?' ,'mailchimp-for-wp' ); ?></label>
		</p>

		<p>
			<input class="button button-large" type="button" id="mc4wp-fw-add-to-form" value="&laquo; <?php _e( 'Add to form' ,'mailchimp-for-wp' ); ?>" />
		</p>

		<p>
			<label for="mc4wp-fw-preview"><?php _e( 'Generated HTML', 'mailchimp-for-wp' ); ?></label>
			<textarea class="widefat" id="mc4wp-fw-preview" rows="5"></textarea>
		</p>

		

	</div>
</div>

<h4 class="mc4wp-title"><?php _e( 'Form usage' ,'mailchimp-for-wp' ); ?></h4>
<p class="mc4wp-form-usage"><?php printf( __( 'Use the shortcode %s to display this form inside a post, page or text widget.' ,'mailchimp-for-wp' ), '<input type="text" onfocus="this.select();" readonly="readonly" value="[mc4wp_form id=&quot;' . $post->ID . '&quot;]" class="mc4wp-shortcode-example">' ); ?></p>