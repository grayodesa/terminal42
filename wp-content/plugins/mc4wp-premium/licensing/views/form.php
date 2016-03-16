<?php
defined( 'ABSPATH' ) or exit;

/**
 * @var DVK_Plugin_License_Manager $license_manager
 */
$license_manager = $this;

/**
 * @var MC4WP_Product $product
 */
$product = $license_manager->product;
$license_field_is_empty = $this->get_license_key() === '';

?>
	<h3><?php echo __( 'Plugin License Settings', $product->text_domain ); ?></h3>

<?php

// Don't show form to site admins if plugin is network activated
if( $this->is_network_activated && ! is_super_admin() ) {
	echo '<p>' . sprintf( __( '%s is network activated. Please contact your site administrator to manage the license.', $product->text_domain ), $this->product->item_name ) . '</p>';
	return;
}


// Output form tags if we're not embedded in another form
if( ! $embedded ) {
	echo '<form method="post">';
}

wp_nonce_field( $nonce_name, $nonce_name ); ?>
<table class="form-table yoast-license-form">
	<tbody>
		<tr valign="top">
			<th scope="row" valign="top"><?php _e( 'Status', $product->text_domain ); ?></th>
			<td>
				<?php if( $license_manager->license_is_valid() ) { ?>
					<span class="status positive"><?php _e( 'ACTIVE', $product->text_domain ); ?></span> &nbsp; - &nbsp; <?php _e( 'you are receiving plugin updates', $product->text_domain ); ?>
				<?php } else { ?>
					<span class="status negative"><?php _e( 'INACTIVE', $product->text_domain ); ?></span> &nbsp; - &nbsp; <?php _e( 'you are <strong>not</strong> receiving plugin updates.', $product->text_domain ); ?>
				<?php } ?>
			</td>
		</tr>

		<tr valign="top">
			<th scope="row" valign="top"><?php _e( 'License Key', $product->text_domain ); ?></th>
			<td>
				<input name="<?php echo esc_attr( $key_name ); ?>" type="text" class="widefat yoast-license-key-field <?php if ( $obfuscate ) { ?>yoast-license-obfuscate<?php } ?>" value="<?php echo esc_attr( $visible_license_key ); ?>" placeholder="<?php echo esc_attr( sprintf( __( 'Paste your license key here, as found in the email receipt.', $product->text_domain ), $product->item_name ) ); ?>" <?php if ( $readonly ) {
					echo 'readonly="readonly"';
				} ?> />

				<p class="help">
					<?php

					echo __( 'The license key you got when purchasing the plugin.', 'mailchimp-for-wp' );
					echo sprintf( ' <a href="%s" target="_blank">' . __( 'Find your license key here', 'mailchimp-for-wp' ) . '</a>', $product->get_tracking_url( '/account/' ) );

					if ( $license_field_is_empty ) {
						echo ' ' . sprintf( __( 'or <a href="%s">purchase a new license</a>', 'mailchimp-for-wp' ), $product->item_url );
					}

					echo '.';
					?>
				</p>
			</td>
		</tr>

		<tr valign="top" style="<?php if( $license_field_is_empty ) { echo 'display: none;'; } ?>">
			<th scope="row" valign="top"><?php _e( 'Toggle license status', $product->text_domain ); ?></th>
			<td class="yoast-license-toggler">

				<?php if( $license_manager->license_is_valid() ) { ?>
					<button name="<?php echo esc_attr( $action_name ); ?>" type="submit" class="button-secondary yoast-license-deactivate" value="deactivate"><?php echo esc_html_e( 'Deactivate License', $product->text_domain ); ?></button> &nbsp;
					<small><?php _e( '(deactivate your license so you can activate it on another WordPress site)', $product->text_domain ); ?></small>
				<?php } else {
					echo '<button name="' . esc_attr( $action_name ) .'" type="submit" class="button-secondary yoast-license-activate" value="activate">' . esc_html( 'Activate License', $product->text_domain ) . '</button> &nbsp';
				}
				?>
			</td>
		</tr>

	</tbody>
</table>

<?php
if( '' !== $license_manager->get_license_key() ) {
	if( $license_manager->get_license_status() === 'expired' ) {
		printf( __( 'Your plugin license has expired. You will no longer have access to plugin updates unless you <a href="%s">renew your license</a>.', 'mailchimp-for-wp' ), $product->get_tracking_url( '/checkout/?edd_license_key=' . $license_manager->get_license_key(), 'renewal_link' ) );
	} else {
		$expiry_date = $license_manager->get_license_expiry_date();

		if( ! empty( $expiry_date ) ) {
			echo '<p>';

			if ( strtotime( 'now' ) > $expiry_date ) {
				// license has expired
				printf( __( 'Your plugin license has expired. You will no longer have access to plugin updates unless you <a href="%s">renew your license</a>.', 'mailchimp-for-wp' ), $product->get_tracking_url( '/checkout/?edd_license_key=' . $license_manager->get_license_key(), 'renewal_link' ) );

			} else {
				// license is valid
				printf( __( 'Your %s license will expire on %s.', $product->text_domain ), $product->item_name, date( 'F jS Y', $expiry_date ) );

				// add link to renew license is less than 3 months left
				if ( strtotime( '+3 months' ) > $expiry_date ) {
					printf( ' <a href="%s" target="_blank">' . __( 'Renew your license now', $product->text_domain ) . '</a>', $product->get_tracking_url( '/checkout/?edd_license_key=' . $license_manager->get_license_key(), 'renewal_link' ) );
				}
			}

			echo '</p>';
		}
	}
}

// Only show a "Save Changes" button and closing form tag if we're not embedded in another form.
if( ! $embedded ) {

	// only show "Save Changes" button if license is not activated
	if( $readonly === false ) {
		submit_button();
	}

	echo '</form>';
}
