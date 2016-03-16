<?php
/**
 * Dashboard template: Login form
 *
 * This template is displayed when no API key was found.
 * Once the user logged into the WPMUDEV account this template is not used
 * anymore (until the user loggs out again).
 *
 * Following variables are passed into the template:
 *   $key_valid
 *   $connection_error
 *   $urls (urls of all dashboard menu items)
 *
 * @since  4.0.0
 * @package WPMUDEV_Dashboard
 */

$register_url = 'http://premium.wpmudev.org/#pricing';
$reset_url = 'http://premium.wpmudev.org/wp-login.php?action=lostpassword';

$last_user = WPMUDEV_Dashboard::$site->get_option( 'auth_user' );

// Check for errors.
$errors = array();
if ( isset( $_GET['api_error'] ) ) {
	$errors[] = sprintf(
		'%s<br><a href="%s" target="_blank">%s</a>',
		__( 'Invalid Username or Password. Please try again.', 'wpmudev' ),
		$reset_url,
		__( 'Forgot your password?', 'wpmudev' )
	);
} elseif ( $connection_error ) {
	// Variable `$connection_error` is set by the UI function `render_dashboard`.
	$errors[] = sprintf(
		'%s<br><br>%s<br><br><em>%s</em>',
		sprintf(
			__( 'Your server had a problem connecting to WPMU DEV: "%s". Please try again.', 'wpmudev' ),
			WPMUDEV_Dashboard::$api->api_error
		),
		__( 'If this problem continues, please contact your host with this error message and ask:', 'wpmudev' ),
		sprintf(
			__( '"Is php on my server properly configured to be able to contact %s with a POST HTTP request via fsockopen or CURL?"', 'wpmudev' ),
			WPMUDEV_Dashboard::$api->rest_url( '' )
		)
	);
} elseif ( ! $key_valid ) {
	// Variable `$key_valod` is set by the UI function `render_dashboard`.
	$errors[] = __( 'Your API Key was invalid. Please try again.', 'wpmudev' );
}

// Get the login URL.
$form_action = WPMUDEV_Dashboard::$api->rest_url( 'authenticate' );

?>
<section id="login" class="login-form-wrapper">
	<h1><?php esc_html_e( 'Let the games begin.', 'wpmudev' ); ?></h1>

	<?php
	// Display the errors.
	if ( count( $errors ) ) {
		?><div class="login-errors dev-tip">
		<?php
		foreach ( $errors as $message ) {
			?>
			<p><?php
			// @codingStandardsIgnoreStart: Message contains HTML, no escaping!
			echo $message;
			// @codingStandardsIgnoreEnd
			?></p>
			<?php
		}
		?>
		</div><?php
	} ?>

	<div class="login-image"></div>

	<div class="dev-box login-box">
		<div class="box-title">
			<h3><?php esc_html_e( 'Login', 'wpmudev' ) ?></h3>
		</div>
		<div class="box-content">
		<form action="<?php echo esc_url( $form_action ); ?>" method="post" class="loign-form">
			<div>
				<label for="user_name">
					<?php esc_html_e( 'Email', 'wpmudev' ); ?>
				</label>
				<input
					type="text"
					name="username"
					id="user_name"
					autocomplete="off"
					placeholder="<?php echo esc_attr__( 'Your email address', 'wpmudev' ); ?>"
					value="<?php echo esc_attr( $last_user ); ?>" />
			</div>
			<div>
				<label for="password">
					<?php esc_html_e( 'Password', 'wpmudev' ); ?>
				</label>
				<input
					type="password"
					name="password"
					id="password"
					autocomplete="off"
					placeholder="<?php echo esc_attr__( 'Your password', 'wpmudev' ); ?>" />
				<input type="hidden" name="redirect_url" value="<?php echo esc_url( $urls->dashboard_url ); ?>" />
			</div>
			<div class="buttons">
				<?php
				printf(
					'<a href="%s" target="_blank" class="button button-text">%s</a>',
					esc_url( $reset_url ),
					esc_html__( 'Forgot password', 'wpmudev' )
				);
				?>
				<button type="submit" class="button one-click">
					<?php esc_html_e( 'Login', 'wpmudev' ); ?>
				</button>
			</div>
		</form>

		</div>
	</div>
	<p class="box-footer">
		<?php
		printf(
			esc_html__( 'No account? %sSign up%s to WPMU DEV today!', 'wpmudev' ),
			'<a href="' . esc_url( $register_url ) . '" target="_blank">',
			'</a>'
		);
		?>
	</p>
</section>