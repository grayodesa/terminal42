<?php
if( ! defined( 'MC4WP_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
?>
<div class="mc4wp-column" style="width:55%">

	<p>
		<?php _e( 'Use the fields below to create custom styling rules for your forms.', 'mailchimp-for-wp' ); ?>
	</p>

	<p class="help">
		<span class="dashicons dashicons-info" style="color: #999;"></span>
		<?php printf( __( 'Tip: have a look at our <a href="%s">knowledge base</a> articles on <a href="%s">creating an inline form</a> or <a href="%s">styling your form</a> in general.', 'mailchimp-for-wp' ), 'https://mc4wp.com/kb/', 'https://mc4wp.com/kb/single-line-forms/', 'https://mc4wp.com/kb/category/styling-your-form/' ); ?>
	</p>

	<?php if( $opts['css'] !== 'custom' ) { ?>
		<div class="updated">
			<p>
				<?php echo sprintf( __( 'You are not loading your custom stylesheet at this moment. To apply these styles on your site, select "load custom form styles" in the <a href="%s">MailChimp for WordPress form settings</a>.', 'mailchimp-for-wp' ), admin_url( 'admin.php?page=mailchimp-for-wp-form-settings' ) ); ?>
			</p>
		</div>
	<?php } ?>

	<form action="" method="get">
        <table class="form-table">
            <tr valign="top">
				<th style="width: 250px"><label><?php _e( 'Select form to build styles for:', 'mailchimp-for-wp' ); ?></label></th>
                <td>
					<?php if( count( $forms ) > 0 ) { ?>
                        <select name="form_id" class="widefat mc4wp-form-select">
							<?php foreach( $forms as $form ) {
								$title = strlen( $form->post_title ) > 40 ? substr( $form->post_title, 0, 40 ) . '..' : $form->post_title;
								?>
								<option value="<?php echo $form->ID; ?>" <?php selected( $form->ID, $form_id ); ?>><?php echo "$form->ID | {$title}"; ?></option>
							<?php } ?>
                        </select>
					<?php } else { ?>
						<p><?php _e( 'Create at least one form first.', 'mailchimp-for-wp' ); ?></p>
					<?php } ?>
                </td>
            </tr>
        </table>

        <input type="hidden" name="page" value="mailchimp-for-wp-form-settings" />
        <input type="hidden" name="tab" value="css-builder" />
        <input type="submit" value="Apply" style="display: none;" />
    </form>

    <form action="options.php" method="post">
	    <input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>" />
	    <input type="submit" name="submit" class="button button-primary" value="Build CSS File" style="display: none;">
		<?php settings_fields( 'mc4wp_form_styles_settings' ); ?>

		<noscript><p><?php _e( 'You need to have JavaScript enabled to see a preview of your form.', 'mailchimp-for-wp' ); ?></p></noscript>

        <div class="mc4wp-css-settings mc4wp-accordion" id="mc4wp-css-form">

	    <div>
	        <h4><?php _e( 'Form container style', 'mailchimp-for-wp' ); ?></h4>
	        <div>
	            <table class="form-table">
	                <tr valign="top">
	                    <th width="1"><?php _e( 'Form width', 'mailchimp-for-wp' ); ?><br /><span class="help"><?php _e( 'px or %', 'mailchimp-for-wp' ); ?></span></th>
						<td class="nowrap">
							<input id="form-width" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_width]" type="text" value="<?php echo esc_attr( $styles['form_width'] ); ?>" class="mc4wp-option" pattern="((\d+)(px|%)*)" />
						</td>
		                <th width="1"><?php _e( 'Text alignment', 'mailchimp-for-wp' ); ?></th>
		                <td class="nowrap">
			                <select name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_text_align]" id="form-text-align" class="mc4wp-option">
				                <option value="" <?php selected( $styles['form_text_align'], '' ); ?>><?php _e( 'Choose alignment', 'mailchimp-for-wp' ); ?></option>
				                <option value="left" <?php selected( $styles['form_text_align'], 'left' ); ?>><?php _e( 'Left', 'mailchimp-for-wp' ); ?></option>
				                <option value="center" <?php selected( $styles['form_text_align'], 'center' ); ?>><?php _e( 'Center', 'mailchimp-for-wp' ); ?></option>
				                <option value="right" <?php selected( $styles['form_text_align'], 'right' ); ?>><?php _e( 'Right', 'mailchimp-for-wp' ); ?></option>
			                </select>
		                </td>
	                </tr>
	                <tr valign="top">
	                    <th width="1"><?php _e( 'Background color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="form-background-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_background_color]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['form_background_color'] ); ?>" /></td>
	                    <th width="1"><?php _e( 'Padding', 'mailchimp-for-wp' ); ?></th>
	                    <td>
		                    <label><input id="form-padding" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_padding]" type="number" class="small-text mc4wp-option" max="99" min="0" value="<?php echo esc_attr( $styles['form_padding'] ); ?>"  /></label> &nbsp;
	                    </td>
	                </tr>
	                <tr valign="top">
	                    <th width="1"><?php _e( 'Border color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="form-border-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_border_color]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['form_border_color'] ); ?>" /></td>
	                    <th width="1"><?php _e( 'Border width', 'mailchimp-for-wp' ); ?></th>
						<td><input id="form-border-width" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_border_width]" type="number" class="small-text mc4wp-option" max="99" min="0" value="<?php echo esc_attr( $styles['form_border_width'] ); ?>" /></td>
	                </tr>
	                <tr valign="top">
	                    <th width="1"><?php _e( 'Text color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="form-font-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_font_color]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['form_font_color'] ); ?>" /></td>
	                    <th><?php _e( 'Text size', 'mailchimp-for-wp' ); ?></th>
						<td><input id="form-font-size" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_font_size]" type="number" class="small-text mc4wp-option" max="99" min="0" value="<?php echo esc_attr( $styles['form_font_size'] ); ?>"  /></td>
	                </tr>
		            <tr>
			            <th width="1"><?php _e( 'Background image', 'mailchimp-for-wp' ); ?></th>
			            <td colspan="3">
				            <input type="text" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_background_image]" id="form-background-image" class="mc4wp-option" value="<?php echo esc_attr( $styles['form_background_image'] ); ?>" />
				            <input type="button" class="button upload-image" value="Upload Image" />
				            <select class="mc4wp-option" id="form-background-repeat" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][form_background_repeat]">
					            <option value="repeat" <?php selected( $styles['form_background_repeat'], 'repeat' ); ?>><?php _e( 'Repeat image', 'mailchimp-for-wp' ); ?></option>
					            <option value="repeat-x" <?php selected( $styles['form_background_repeat'], 'repeat-x' ); ?>><?php _e( 'Repeat horizontally', 'mailchimp-for-wp' ); ?>'</option>
					            <option value="repeat-y" <?php selected( $styles['form_background_repeat'], 'repeat-y' ); ?>><?php _e( 'Repeat vertically', 'mailchimp-for-wp' ); ?>'</option>
					            <option value="no-repeat" <?php selected( $styles['form_background_repeat'], 'no-repeat' ); ?>><?php _e( 'Do not repeat', 'mailchimp-for-wp' ); ?></option>
				            </select>
			            </td>
		            </tr>
	            </table>
	        </div>
	    </div>

	    <div>
	        <h4><?php _e( 'Label styles', 'mailchimp-for-wp' ); ?></h4>
	        <div>
	            <table class="form-table">
	                <tr valign="top">
	                    <th width="1"><?php _e( 'Label width', 'mailchimp-for-wp' ); ?><br /><span class="help"><?php _e( 'px or %', 'mailchimp-for-wp' ); ?></span></th>
						<td class="nowrap">
							<input id="labels-width" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][labels_width]" type="text" value="<?php echo esc_attr( $styles['labels_width'] ); ?>" class="mc4wp-option" pattern="((\d+)(px|%)*)" />
						</td>
	                    <th></th>
	                    <td class="nowrap"></td>
	                </tr>
	                <tr valign="top">
	                    <th><?php _e( 'Text color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="labels-font-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][labels_font_color]" value="<?php echo esc_attr( $styles['labels_font_color'] ); ?>" type="text" class="color-field mc4wp-option" /></td>
	                    <th><?php _e( 'Text size', 'mailchimp-for-wp' ); ?></th>
						<td><input id="labels-font-size" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][labels_font_size]" type="number" class="small-text mc4wp-option" max="99" min="0" value="<?php echo esc_attr( $styles['labels_font_size'] ); ?>"  /></td>
	                </tr>
	                <tr valign="top">
	                    <th><?php _e( 'Text style', 'mailchimp-for-wp' ); ?></th>
	                    <td>
							<select id="labels-font-style" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][labels_font_style]" class="mc4wp-option">
								<option value="" <?php selected( $styles['labels_font_style'], '' ); ?>><?php _e( 'Choose text style..', 'mailchimp-for-wp' ); ?></option>
								<option value="normal" <?php selected( $styles['labels_font_style'], 'normal' ); ?>><?php _e( 'Normal', 'mailchimp-for-wp' ); ?></option>
								<option value="bold" <?php selected( $styles['labels_font_style'], 'bold' ); ?>><?php _e( 'Bold', 'mailchimp-for-wp' ); ?></option>
								<option value="italic" <?php selected( $styles['labels_font_style'], 'italic' ); ?>><?php _e( 'Italic', 'mailchimp-for-wp' ); ?></option>
								<option value="bolditalic" <?php selected( $styles['labels_font_style'], 'bolditalic' ); ?>><?php _e( 'Bold & Italic', 'mailchimp-for-wp' ); ?></option>
	                        </select>
	                    </td>
	                    <th><?php _e( 'Display', 'mailchimp-for-wp' ); ?></th>
	                    <td>
		                    <select id="labels-display" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][labels_display]" class="mc4wp-option">
			                    <option value=""></option>
			                    <option value="inline-block" <?php selected( $styles['labels_display'], 'inline-block' ); ?>> <?php _e( 'Inline', 'mailchimp-for-wp' ); ?></option>
			                    <option value="block" <?php selected( $styles['labels_display'], 'block' ); ?>> <?php _e( 'New line', 'mailchimp-for-wp' ); ?></option>
		                    </select>
	                    </td>
	                </tr>
	            </table>
	        </div>
	    </div>

        <div>
	        <h4><?php _e( 'Field styles', 'mailchimp-for-wp' ); ?></h4>
	        <div>
	            <table class="form-table">
	                <tr valign="top">
	                    <th><?php _e( 'Field width', 'mailchimp-for-wp' ); ?><br /><span class="help"><?php _e( 'px or %', 'mailchimp-for-wp' ); ?></span></th>
						<td class="nowrap">
							<input id="fields-width" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][fields_width]" type="text" value="<?php echo esc_attr( $styles['fields_width'] ); ?>" class="mc4wp-option" pattern="((\d+)(px|%)*)" />
						</td>
	                    <th><?php _e( 'Field height', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="fields-height" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][fields_height]" min="0" type="number" class="small-text mc4wp-option" value="<?php echo esc_attr( $styles['fields_height'] ); ?>" /></td>
	                </tr>
	                <tr valign="top">
	                    <th><?php _e( 'Border color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="fields-border-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][fields_border_color]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['fields_border_color'] ); ?>" /></td>
	                    <th><?php _e( 'Border width', 'mailchimp-for-wp' ); ?></th>
						<td><input id="fields-border-width" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][fields_border_width]" type="number" class="small-text mc4wp-option" max="99" min="0" value="<?php echo esc_attr( $styles['fields_border_width'] ); ?>" /></td>
	                </tr>
	                <tr>
	                    <th><?php _e( 'Display', 'mailchimp-for-wp' ); ?></th>
	                    <td>
		                    <select id="fields-display" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][fields_display]" class="widefat mc4wp-option">
			                    <option value=""></option>
			                    <option value="inline-block" <?php selected( $styles['fields_display'], 'inline-block' ); ?>> <?php _e( 'Inline', 'mailchimp-for-wp' ); ?></option>
			                    <option value="block" <?php selected( $styles['fields_display'], 'block' ); ?>> <?php _e( 'New line', 'mailchimp-for-wp' ); ?></option>
		                    </select>
	                    </td>
		                <th><?php _e( 'Border radius', 'mailchimp-for-wp' ); ?></th>
		                <td><input id="fields-border-radius" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][fields_border_radius]" type="number" class="small-text mc4wp-option" max="99" min="0" value="<?php echo esc_attr( $styles['fields_border_radius'] ); ?>" /></td>
	                </tr>
		            <tr valign="top">
			            <th><?php _e( 'Focus outline', 'mailchimp-for-wp' ); ?></th>
			            <td class="nowrap">
				            <input id="fields-focus-outline-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][fields_focus_outline_color]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['fields_focus_outline_color'] ); ?>" />
			            </td>
			            <th></th>
			            <td></td>
		            </tr>
	            </table>
	        </div>
        </div>

	    <div>
	        <h4><?php _e( 'Button styles', 'mailchimp-for-wp' ); ?></h4>
	        <div>
	            <table class="form-table">
	                <tr valign="top">
	                    <th><?php _e( 'Button width', 'mailchimp-for-wp' ); ?><br /><span class="help"><?php _e( 'px or %', 'mailchimp-for-wp' ); ?></span></th>
						<td class="nowrap">
							<input id="buttons-width" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_width]" type="text" value="<?php echo esc_attr( $styles['buttons_width'] ); ?>" class="mc4wp-option" pattern="((\d+)(px|%)*)" />
						</td>
	                    <th><?php _e( 'Button height', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="buttons-height" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_height]" min="0" type="number" class="small-text mc4wp-option" value="<?php echo esc_attr( $styles['buttons_height'] ); ?>" /></td>
	                </tr>
	                <tr valign="top">
	                    <th width="1"><?php _e( 'Background color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap">
							<input id="buttons-background-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_background_color]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['buttons_background_color'] ); ?>" />
							<input id="buttons-hover-background-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_hover_background_color]" type="hidden" class="mc4wp-option" value="<?php echo esc_attr( $styles['buttons_hover_background_color'] ); ?>" />
						</td>
		                <th><?php _e( 'Border width', 'mailchimp-for-wp' ); ?></th>
		                <td><input id="buttons-border-width" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_border_width]" type="number" class="small-text mc4wp-option" max="99" min="0" value="<?php echo esc_attr( $styles['buttons_border_width'] ); ?>" /></td>
	                </tr>
	                <tr valign="top">
	                    <th><?php _e( 'Border color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap">
							<input id="buttons-border-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_border_color]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['buttons_border_color'] ); ?>" />
							<input id="buttons-hover-border-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_hover_border_color]" type="hidden" class="mc4wp-option" value="<?php echo esc_attr( $styles['buttons_hover_border_color'] ); ?>" />
						</td>
	                    <th><?php _e( 'Border radius', 'mailchimp-for-wp' ); ?></th>
		                <td><input id="buttons-border-radius" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_border_radius]" type="number" class="small-text mc4wp-option" max="99" min="0" value="<?php echo esc_attr( $styles['buttons_border_radius'] ); ?>" /></td>
	                </tr>
	                <tr valign="top">
	                    <th><?php _e( 'Text color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="buttons-font-color" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_font_color]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['buttons_font_color'] ); ?>" /></td>
	                    <th><?php _e( 'Text size', 'mailchimp-for-wp' ); ?></th>
						<td><input id="buttons-font-size" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][buttons_font_size]" type="number" class="small-text mc4wp-option" max="99" min="0" value="<?php echo esc_attr( $styles['buttons_font_size'] ); ?>"  /></td>
	                </tr>
	            </table>
	        </div>
	    </div>

	    <div>
	        <h4><?php _e( 'Error and success messages', 'mailchimp-for-wp' ); ?></h4>
	        <div>
	            <table class="form-table">
	                <tr valign="top">
	                    <th><?php _e( 'Success text color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="messages-font-color-success" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][messages_font_color_success]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['messages_font_color_success'] ); ?>" /></td>
	                    <th><?php _e( 'Error text color', 'mailchimp-for-wp' ); ?></th>
						<td class="nowrap"><input id="messages-font-color-error" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][messages_font_color_error]" type="text" class="color-field mc4wp-option" value="<?php echo esc_attr( $styles['messages_font_color_error'] ); ?>" /></td>
	                </tr>
	            </table>
	        </div>
	    </div>

	    <div>
	        <h4><?php _e( 'Advanced', 'mailchimp-for-wp' ); ?></h4>
	        <div>
	            <table class="form-table">
	                <tr valign="top">
	                    <th><label><?php _e( 'CSS Selector Prefix', 'mailchimp-for-wp' ); ?></label></th>
						<td><input type="text" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][selector_prefix]" value="<?php echo esc_attr( $styles['selector_prefix'] ); ?>" placeholder="Example: #content" class="mc4wp-option" /></td>
	                    <td class="desc"><?php _e( 'Use this to create a more specific (and thus more "important") CSS selector.', 'mailchimp-for-wp' ); ?></td>
	                </tr>
	                <tr>
	                    <th colspan="1"><?php _e( 'Manual CSS', 'mailchimp-for-wp' ); ?></th><td colspan="2" class="desc"><?php _e( 'The CSS rules you enter here will be appended to the custom stylesheet.', 'mailchimp-for-wp' ); ?></td>
	                </tr>
	                <tr>
	                    <td colspan="3">
		                    <?php $rows = substr_count( $styles['manual'], PHP_EOL ) + 6; ?>
							<textarea class="widefat mc4wp-option" rows="<?php echo esc_attr( $rows ); ?>" cols="50" name="mc4wp_form_styles[form-<?php echo $form_id; ?>][manual]" id="manual-css" placeholder="Example: .mc4wp-form { background: url('http://...'); }"><?php echo esc_textarea( $styles['manual'] ); ?></textarea>
	                    </td>
	                </tr>
		            <tr valign="top">
			            <th><label><?php _e( 'Copy styles from other form', 'mailchimp-for-wp' );?></label></th>
			            <td colspan="2">
				            <?php if( count( $forms ) > 0 ) { ?>
					            <select name="copy_from_form_id">
						            <?php foreach( $forms as $form ) {

							            // skip current form
							            if( $form->ID === $form_id ) { continue; }

							            $title = strlen( $form->post_title ) > 40 ? substr( $form->post_title, 0, 40 ) . '..' : $form->post_title;
							            ?>
							            <option value="<?php echo $form->ID; ?>"><?php echo "$form->ID | {$title}"; ?></option>
						            <?php } ?>
					            </select>
					            <input type="submit" name="_mc4wp_copy_form_styles" value="<?php _e( 'Copy Styles', 'mailchimp-for-wp' ); ?>" class="button-secondary" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to copy form styles from another form? This will overwrite current styles for this form.', 'mailchimp-for-wp' ); ?>');"/>
				            <?php } else { ?>
					            <p><?php _e( 'Create at least one form first.', 'mailchimp-for-wp' ); ?></p>
				            <?php } ?>
			            </td>
		            </tr>
	            </table>

	        </div>
	    </div>

    </div><!-- End Accordion -->

	    <p class="submit">
		    <input type="submit" name="submit" id="submit" class="button button-primary" value="Build CSS File">
	        <button type="submit" tabindex="9999" name="_mc4wp_delete_form_styles" value="<?php echo $form_id; ?>" class="button-secondary" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete all custom styles for this form?', 'mailchimp-for-wp' ); ?>');"><?php _e( 'Delete Form Styles', 'mailchimp-for-wp' ); ?></button>
	    </p>

    </form>

</div>
<div class="mc4wp-column mc4wp-column-right" style="width:42.5%;">
    <h3><?php _e( 'Form preview', 'mailchimp-for-wp' ); ?></h3>
	<iframe id="mc4wp-css-preview" data-src-url="<?php echo esc_attr( $preview_url ); ?>" src="<?php echo esc_attr( $preview_url ) ?>"></iframe>
</div>

<br class="clear" />
