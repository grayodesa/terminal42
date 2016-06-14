<?php
wp_enqueue_script('tooltipster_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/js/jquery.tooltipster.min.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('tooltipster_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/css/tooltipster.css', array(), BETTER_ANALYTICS_VERSION);

wp_enqueue_script('better-analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('better-analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

$betterAnalyticsSiteOptions = get_site_option('better_analytics_site');


?>
	<input type="hidden" id="ba_current_tab" value="api">
	<h3><?php _e('Better Analytics Settings', 'better-analytics'); ?></h3>
	<table id="menu" class="form-table">

	<tr valign="top">
		<th scope="row"></th>
		<td>
			<a id="ba_select_profile" class="button" href="<?php echo network_admin_url('settings.php?page=better-analytics_auth'); ?>"><?php
				get_site_option('ba_site_tokens') ? esc_html_e('Link/Authenticate A Different Google Analytics Account', 'better-analytics') : esc_html_e('Link/Authenticate Your Google Analytics Account', 'better-analytics');
				?></a>

			<?php
				if (get_site_option('ba_site_tokens'))
				{
					echo ' &nbsp; <label for="ba_api_delete_tokens"><input name="better_analytics[api][delete_tokens]" type="checkbox" id="ba_api_delete_tokens" value="1"> ' . esc_html__('Delete Existing Tokens', 'better-analytics') . '</label>';
				}
			?>

		</td>
	</tr>

	<tr valign="top" class="group_api tab_content">
		<th scope="row"></th>
		<td>
			<fieldset>
				<label for="ba_api_use_own">
					<input name="better_analytics[api][use_own]" type="checkbox" id="ba_api_use_own" value="1" <?php checked('1', @$betterAnalyticsSiteOptions['api']['use_own'] ); ?>>
					<?php esc_html_e('Use Your Own Project Credentials', 'better-analytics');?></label>
				<span class="dashicons-before dashicons-info tooltip" title="<?php echo htmlspecialchars(sprintf(__('If you have your own Google API Project that you wish to use, you can use your credentials for that web application.', 'better-analytics')));?>"></span>

			</fieldset>

		</td>
	</tr>

	<tr valign="top" class="group_api tab_content api_hideable"<?php echo (!@$betterAnalyticsSiteOptions['api']['use_own'] ? ' style="display:none"' : '')?>>
		<td colspan="2">

			<?php echo '<fieldset style="border:1px solid grey;margin:10px;padding:20px;"><legend style="padding:0 5px;font-weight:bold;font-size:120%">' . esc_html__('Configuration', 'better-analytics') . '</legend>' .
				/* translators: %1$s = <strong>, %2$s = </strong> */
				sprintf(__('Project needs to have the %1$sAnalytics API%2$s enabled under %1$sAPIs & auth -> APIs%2$s.', 'better_analytics'), '<strong>', '</strong>') . '<br /><br />' .
				/* translators: %1$s = <strong>, %2$s = </strong> */
				sprintf(__('Under %1$sAPIs & auth -> Credentials%2$s, you need to %1$sCreate new Client ID%2$s that is a %1$sWeb application%2$s with an authorized redirect URI of: %1$s%3$s%2$s', 'better-analytics'), '<strong>', '</strong>', self_admin_url('settings.php?page=better-analytics_auth'))
				. '</fieldset>';?>
		</td>
	</tr>


	<tr valign="top" class="group_api tab_content api_hideable"<?php echo (!@$betterAnalyticsSiteOptions['api']['use_own'] ? ' style="display:none"' : '')?>>
		<th scope="row"><?php esc_html_e('Client ID', 'better-analytics');?></th>
		<td>
			<input type="text" name="better_analytics[api][client_id]" id="ba_api_client_id" placeholder="0000000000.apps.googleusercontent.com" value="<?php echo esc_attr( @$betterAnalyticsSiteOptions['api']['client_id'] ); ?>" />
		</td>
	</tr>

	<tr valign="top" class="group_api tab_content api_hideable"<?php echo (!@$betterAnalyticsSiteOptions['api']['use_own'] ? ' style="display:none"' : '')?>>
		<th scope="row"><?php esc_html_e('Client Secret', 'better-analytics');?></th>
		<td>
			<input type="text" name="better_analytics[api][client_secret]" id="ba_api_client_secret" value="<?php echo esc_attr( @$betterAnalyticsSiteOptions['api']['client_secret'] ); ?>" />
		</td>
	</tr>

<?php
echo '</table>';