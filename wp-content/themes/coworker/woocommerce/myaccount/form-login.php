<?php
/**
 * Login Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php wc_print_notices(); ?>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

<div class="col2-set" id="customer_login">

	<div class="col_one_third nobottommargin">

<?php endif; ?>

		<div id="login-form" style="max-width: 400px;">

			<h3><?php _e( 'Login to your Account', 'woocommerce' ); ?></h3>

			<form method="post" class="login nobottommargin">

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="form-row form-row-wide">
					<label for="username"><?php _e( 'Username or email address', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text input-block-level" name="username" id="username" />
				</p>
				<p class="form-row form-row-wide">
					<label for="password"><?php _e( 'Password', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input class="input-text input-block-level" type="password" name="password" id="password" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row">
					<?php wp_nonce_field( 'woocommerce-login' ); ?>
					<input type="submit" class="simple-button noleftmargin" name="login" value="<?php _e( 'Login', 'woocommerce' ); ?>" /> 
					<label for="rememberme" class="inline">
						<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php _e( 'Remember', 'woocommerce' ); ?>
					</label>
					<a class="lost_password fright" href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php _e( 'Lost password?', 'woocommerce' ); ?></a>
				</p>

				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>

		</div>

<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) : ?>

	</div>

	<div class="col_two_third col_last nobottommargin">

		<h3><?php _e( 'Don\'t have an Account? Register Now.', 'woocommerce' ); ?></h3>

		<?php if( semi_option( 'shop_register_message' ) != '' ): ?>

		<p><?php echo semi_option( 'shop_register_message' ); ?></p>

		<?php endif; ?>

		<form method="post" class="register nobottommargin" id="woocommerce-register-form">

			<?php do_action( 'woocommerce_register_form_start' ); ?>

			<?php if ( get_option( 'woocommerce_registration_generate_username' ) === 'no' ) : ?>

				<div class="col_half nobottommargin">
					<label for="reg_username"><?php _e( 'Username', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text input-block-level required" name="username" id="reg_username" value="<?php if ( ! empty( $_POST['username'] ) ) esc_attr_e( $_POST['username'] ); ?>" />
				</div>

				<div class="col_half col_last nobottommargin">
					<label for="reg_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text input-block-level required" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) esc_attr_e( $_POST['email'] ); ?>" />
				</div>

			<?php else: ?>

				<div class="col_full nobottommargin">
					<label for="reg_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="text" class="input-text input-block-level required" name="email" id="reg_email" value="<?php if ( ! empty( $_POST['email'] ) ) esc_attr_e( $_POST['email'] ); ?>" />
				</div>

			<?php endif; ?>

			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

			<div class="col_half nobottommargin">
				<label for="reg_password"><?php _e( 'Choose Password', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text input-block-level required" name="password" id="reg_password" value="<?php if ( ! empty( $_POST['password'] ) ) esc_attr_e( $_POST['password'] ); ?>" />
			</div>

			<div class="col_half nobottommargin col_last">
				<label for="reg_password"><?php _e( 'Re-enter Password:', 'woocommerce' ); ?> <span class="required">*</span></label>
				<input type="password" class="input-text input-block-level required" name="re_password" id="re_reg_password" value="" />
			</div>

			<?php endif; ?>

			<!-- Spam Trap -->
			<div style="<?php echo ( ( is_rtl() ) ? 'right' : 'left' ); ?>: -999em; position: absolute;"><label for="trap"><?php _e( 'Anti-spam', 'woocommerce' ); ?></label><input type="text" name="email_2" id="trap" tabindex="-1" /></div>

			<?php do_action( 'woocommerce_register_form' ); ?>
			<?php do_action( 'register_form' ); ?>

			<div class="col_full nobottommargin">
				<?php wp_nonce_field( 'woocommerce-register', 'register' ); ?>
				<input type="submit" class="simple-button noleftmargin" name="register" value="<?php _e( 'Register Now', 'woocommerce' ); ?>" />
			</div>

			<?php do_action( 'woocommerce_register_form_end' ); ?>

		</form>

	</div>

	<script type="text/javascript">

		jQuery("#woocommerce-register-form").validate({
    		rules: {
				username: {
					required: true
				},
				password: {
					required: true
				},
				re_password: {
					required: true,
					equalTo: "#reg_password"
				},
				email: {
					required: true,
					email: true
				}
			},
			messages: {
				username: {
					required: ""
				},
				password: {
					required: ""
				},
				re_password: {
					required: "",
					equalTo: ""
				},
				email: ""
			}
    	});

	</script>

</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>