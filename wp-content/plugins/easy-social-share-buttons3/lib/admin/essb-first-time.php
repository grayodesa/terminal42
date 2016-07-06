<?php 
update_option(ESSB3_FIRST_TIME_NAME, 'false');
?>
<div class="wrap essb-page-welcome about-wrap">
	<h1><?php echo sprintf( __( 'Welcome to Easy Social Share Buttons for WordPress %s', ESSB3_TEXT_DOMAIN ), preg_replace( '/^(\d+)(\.\d+)?(\.\d)?/', '$1$2', ESSB3_VERSION ) ) ?></h1>

	<div class="about-text">
		<?php _e( 'Easy Social Share Buttons for WordPress is all-in-one social share solution that allows you share, monitor and increase your social popularity by AppsCreo', ESSB3_TEXT_DOMAIN )?>
	</div>
	<div class="wp-badge essb-page-logo">
		<?php echo sprintf( __( 'Version %s', ESSB3_TEXT_DOMAIN ), ESSB3_VERSION )?>
	</div>

	<!-- welcome content -->
	<div class="essb_welcome-tab changelog">

		<div class="essb_welcome-feature feature-section col three-col">
			<h3>Congratulations! You are runing Easy Social Share Buttons for
				first time.</h3>
			<span></span>
			<div>
				<span class="essb-firsttime-center"> <i
					class="fa fa-bolt fa-lg essb-firsttime-icon"></i>
					<h4>Quick setup wizard</h4>
				</span>
				<p>Quick setup wizard is a great way to make a quick work through
					plugin settings and make initial plugin setup.</p>
				
			</div>
			<div>
				<span class="essb-firsttime-center"> <i
					class="fa fa-send fa-lg essb-firsttime-icon"></i>
					<h4>Start with ready made styles</h4>
				</span>

				<p>We hand pick and configure most popular ready made styles that
					you can import and customize</p>
				
			</div>
			<div class="last-feature">
				<span class="essb-firsttime-center"> <i
					class="fa fa-check-square-o fa-lg essb-firsttime-icon"></i>
					<h4>Choose settings mode</h4>
				</span>

				<p>Choose settings mode that you wish to work with. For beginner
					users we recommend to start with Easy Mode (you can turn it off at
					any time). Easy mode is a great way to start work with plugin as it
					contains only most popular functions and some preset settings.</p>
				
			</div>
			<div><p align="center">
					<a
						href="<?php echo esc_attr( admin_url( 'admin.php?page=essb_redirect_quick&tab=quick' ) ) ?>"
						class="button button-primary" style="width: 95%; text-align: center;"><?php _e( 'Start quick setup wizard', ESSB3_TEXT_DOMAIN ) ?></a>
				</p></div>
			<div><p align="center">
					<a
						href="<?php echo esc_attr( admin_url( 'admin.php?page=essb_redirect_import&tab=import&section=readymade' ) ) ?>"
						class="button button-primary" style="width: 95%; text-align: center;"><?php _e( 'Go to ready made style import', ESSB3_TEXT_DOMAIN ) ?></a>
				</p></div>
			<div class="last-feature"><p align="center">
					<a
						href="<?php echo esc_attr( admin_url( 'admin.php?page=essb_options' ) ) ?>"
						class="button button-primary" style="width: 95%; text-align: center; margin-bottom: 10px;"><?php _e( 'Continue with full settings', ESSB3_TEXT_DOMAIN ) ?></a>
					<a
						href="<?php echo esc_attr( admin_url( 'admin.php?page=essb_options&easymode=activate' ) ) ?>"
						class="button button-primary" style="width: 95%; text-align: center;"><?php _e( 'Continue in Easy Mode', ESSB3_TEXT_DOMAIN ) ?></a>
				</p></div>
		</div>



		<p class="essb-thank-you">
			Thank you for choosing <b>Easy Social Share Buttons for WordPress</b>.
			If you like our work please <a href="http://codecanyon.net/downloads"
				target="_blank">rate Easy Social Share Buttons for WordPress <i
				class="fa fa-star"></i><i class="fa fa-star"></i><i
				class="fa fa-star"></i><i class="fa fa-star"></i><i
				class="fa fa-star"></i></a>
		</p>

	</div>

</div>

