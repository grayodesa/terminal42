<?php
	wp_enqueue_script('tooltipster_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/js/jquery.tooltipster.min.js', array(), BETTER_ANALYTICS_VERSION );
	wp_enqueue_style('tooltipster_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/css/tooltipster.css', array(), BETTER_ANALYTICS_VERSION);

	wp_enqueue_script('chosen_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.jquery.min.js', array(), BETTER_ANALYTICS_VERSION );
	wp_enqueue_style('chosen_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.min.css', array(), BETTER_ANALYTICS_VERSION);

	wp_enqueue_script('better-analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
	wp_enqueue_style('better-analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

	$betterAnalyticsOptions = get_option('better_analytics');

	$betterAnalyticsInternal = DigitalPointBetterAnalytics_Helper_Api::check();

	if (DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->checkAccessToken(false))
	{
		$profiles = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getProfiles();
	}
	else
	{
		$profiles = array();
	}
?>


<div class="wrap" id="better-analytics_settings">

	<h2><?php esc_html_e( 'Better Analytics' , 'better-analytics');?></h2>

	<h3 class="nav-tab-wrapper" id="better-analytics_tabs">
		<a class="nav-tab" id="general-tab" href="#top#general"><?php esc_html_e( 'General', 'better-analytics' ); ?></a>
		<a class="nav-tab" id="dimensions-tab" href="#top#dimensions"><?php esc_html_e( 'Custom Dimensions', 'better-analytics' ); ?></a>
		<a class="nav-tab" id="social-tab" href="#top#social"><?php esc_html_e( 'Social', 'better-analytics' ); ?></a>
		<a class="nav-tab" id="advertising-tab" href="#top#advertising"><?php esc_html_e( 'Advertising', 'better-analytics' ); ?></a>
		<a class="nav-tab" id="monitor-tab" href="#top#monitor"><?php esc_html_e( 'Monitoring', 'better-analytics' ); ?></a>
		<a class="nav-tab" id="ecommerce-tab" href="#top#ecommerce"><?php esc_html_e( 'eCommerce', 'better-analytics' ); ?></a>
		<a class="nav-tab" id="api-tab" href="#top#api"><?php esc_html_e('API', 'better-analytics' ); ?></a>
		<a class="nav-tab" id="advanced-tab" href="#top#advanced"><?php esc_html_e('Advanced', 'better-analytics' ); ?></a>
	</h3>


	<form method="post" action="options.php">
		<input type="hidden" id="ba_current_tab" name="current_tab" value="general" />
		<?php
			settings_fields('better-analytics-group' );
			do_settings_sections('better-analytics-group');
		?>


		<aside id="better-analytics_sidebar_wrapper">
			<div id="better-analytics_sidebar">

				<div class="postbox support">
					<h4><?php esc_html_e('Support / Feature Requests', 'better-analytics'); ?></h4>
					<div>
						<?php esc_html_e('Better Analytics is user request driven, so if there\'s something you want it to do that it doesn\'t already do, or just have a question, simply ask!') . printf('<br /><br /><a class="button button-primary" href="%1$s">%2$s</a>', esc_url(menu_page_url('better-analytics_test', false)), esc_html__('Setup Test Tool', 'better-analytics')); ?>
					</div>
					<h4 style="margin-top:2em;"><?php esc_html_e('Support / Request Venues', 'better-analytics'); ?></h4>
					<div>
						<?php printf('<a class="button button-primary" href="%1$s" target="_blank">%2$s</a> <a class="button button-primary" href="%3$s" target="_blank"><span class="dashicons dashicons-twitter"></span> %4$s</a>',
							esc_url(BETTER_ANALYTICS_SUPPORT_URL . '#utm_source=admin_settings&utm_medium=wordpress&utm_campaign=plugin'),
							__('Forum', 'better-analytics'),
							esc_url('https://twitter.com/digitalpoint'),
							__('Twitter', 'better-analytics')
						); ?>
					</div>
				</div>

				<?php
					if (!DigitalPointBetterAnalytics_Model_Admin::isLocaleSupported($locales))
					{
						?>

						<div class="postbox translation">
							<h4><?php
									echo 'Translation / Localization';
								?></h4>

							<div>
								<?php

								/*
									if (version_compare($GLOBALS['wp_version'], '4.0.0', '<' ))
									{
										$localeName = get_locale();
									}
									else
									{
										require_once(ABSPATH . 'wp-admin/includes/translation-install.php');
										$translations = wp_get_available_translations();

										$locale = get_locale();

										if (!empty($translations[$locale]))
										{
											$localeName = $translations[$locale]['english_name'];
										}
										else
										{
											$localeName = 'Unknown';
										}
									}
									printf('Better Analytics currently supports %1$u languages, but unfortunately %3$s%2$s%4$s isn\'t one of them.', count($locales), $localeName, '<strong>', '</strong>');
									echo '<p />';
								*/
									printf('If you would like to help translate Better Analytics into your language, please visit the %1$swordpress.org translation site%2$s and you can help in translating.', '<a href="' . esc_url('https://translate.wordpress.org/projects/wp-plugins/better-analytics/dev') . '" target="_blank">', '</a>');
								?>
							</div>
						</div>
					<?php
					}
				?>


				<div class="postbox pro">
					<h4><?php esc_html_e('Extra Features In Pro Version', 'better-analytics'); ?></h4>
					<div>
						<ul>
							<li>
								<?php esc_html_e('More Advertising tracking options', 'better-analytics'); ?>
							</li>
							<li>
								<?php esc_html_e('More options for monitoring issues with site', 'better-analytics'); ?>
							</li>
							<li>
								<?php esc_html_e('More heat map metrics', 'better-analytics'); ?>
							</li>
							<li>
								<?php esc_html_e('More charting dimensions', 'better-analytics'); ?>
							</li>
							<li>
								<?php esc_html_e('More A/B testing objective metrics', 'better-analytics'); ?>
							</li>

								<?php //esc_html_e('eCommerce tracking', 'better-analytics'); ?>

							<li>
								<?php esc_html_e('Option for server-side tracking of users (or bots) with Analytics/privacy blockers', 'better-analytics'); ?>
							</li>
							<li>
								<?php esc_html_e('API calls are faster (uses a custom system for parallel requests)', 'better-analytics'); ?>
							</li>
							<li>
								<?php esc_html_e('Priority Support', 'better-analytics'); ?>
							</li>
						</ul>
						<?php

							printf(esc_html__('You can %1$sget a license here%2$s.%3$sIf you already have a license, make sure your domain is listed under %4$syour verified domains%2$s.', 'better-analytics'),
								'<a href="' . esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_settings&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">',
								'</a>',
								'<br /><br />',
								'<a href="https://forums.digitalpoint.com/marketplace/domain-verification#utm_source=admin_settings&utm_medium=wordpress&utm_campaign=plugin" target="_blank">'
							);
						?>
					</div>
				</div>

			</div>
		</aside>

		<table class="form-table" id="ba_settings">


			<tr valign="top" class="group_general tab_content">
				<th scope="row"><?php esc_html_e('Google Analytics Web Property ID', 'better-analytics');?></th>
				<td>
					<input type="text" name="better_analytics[property_id]" id="ba_property_id" placeholder="UA-000000-01" style="width:25%;min-width:110px;" value="<?php echo esc_attr( @$betterAnalyticsOptions['property_id'] ); ?>" />
					<?php


					$profilePick = array();
					if (!empty($profiles['items']))
					{
						$profilePick = DigitalPointBetterAnalytics_Base_Admin::getProfilePropertyIds($profiles['items']);
					}

					if ($profilePick)
					{
						$siteHostname = parse_url(site_url(), PHP_URL_HOST);

						echo '<select data-placeholder="' . esc_html__('Pick profile', 'better-analytics') . '" id="ba_pick_profile" class="chosen-select">';

						echo '<option value="">' . esc_html__('please pick a profile', 'better-analytics') . '</option>';

						foreach ($profilePick as $key => $profile)
						{
							$profileHostname = parse_url($profile[0], PHP_URL_HOST);

							echo '<optgroup label="' . htmlentities($profile[0]) . '"><option value="' . $key . '"' . ($profileHostname == $siteHostname ? ' style="background-color:#ffffc8"' : '') . '' . ($key == @$betterAnalyticsOptions['property_id'] ? ' selected="selected"' : '') . '>' . htmlentities($profile[1] . ' (' . $key . ')') . '</option></optgroup>';
						}
						echo '</select>';
					}
					else
					{
						echo '<a id="ba_select_profile" class="button" href="' . menu_page_url('better-analytics_auth', false) . '">' . esc_html__('Link Your Google Analytics Account', 'better-analytics') . '</a>';
					}
					?>
				</td>
			</tr>


			<tr valign="top" class="group_general tab_content">
				<th scope="row"><?php esc_html_e('General', 'better-analytics');?></th>
				<td>
					<fieldset>
						<label for="ba_link_attribution">
							<input name="better_analytics[link_attribution]" type="checkbox" id="ba_link_attribution" value="1" <?php checked('1', @$betterAnalyticsOptions['link_attribution'] ); ?>>
							<?php esc_html_e('Enhanced Link Attribution', 'better-analytics');?></label>
							<span class="dashicons-before dashicons-info tooltip" title="<?php
								printf(esc_html__('See separate information for multiple links on a page that all have the same destination. For example, if there are two links on the same page that both lead to the Contact Us page, then you see separate click information for each link.%1$sSee when one page element has multiple destinations. For example, a Search button on your page is likely to lead to multiple destinations.%1$sTrack buttons, menus, and actions driven by JavaScript.', 'better-analytics'),
									'<p />'
								); ?>"></span>
							<br />

							<label for="ba_track_userid">
								<input name="better_analytics[track_userid]" type="checkbox" id="ba_track_userid" value="1" <?php checked('1', @$betterAnalyticsOptions['track_userid'] ); ?>>
								<?php esc_html_e('Track Registered Users By User ID', 'better-analytics');?></label>
								<span class="dashicons-before dashicons-info tooltip" title="<?php echo htmlspecialchars(sprintf(__('This feature allows you to utilize %1$ssession unification%2$s within Google Analytics.', 'better-analytics'),
									'<a href="' . esc_url('https://support.google.com/analytics/answer/4574780') . '" target="_blank">',
									'</a>'
								));
								?>"></span>
					</fieldset>

				</td>
			</tr>


			<tr valign="top" class="group_general tab_content">
				<th scope="row"><?php esc_html_e('Events To Track', 'better-analytics');?></th>
				<td>
					<fieldset>
						<label for="ba_events_user_engagement">
							<input name="better_analytics[events][user_engagement]" type="checkbox" id="ba_events_user_engagement" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['user_engagement'] ); ?>>
							<?php
								esc_html_e('User Engagement','better-analytics');
							?>
						</label>
						<span class="dashicons-before dashicons-info tooltip" title="<?php echo htmlspecialchars(sprintf(__('This will give you an %1$sadjusted bounce rate%2$s.', 'better-analytics'),
							'<a href="' . esc_url('http://analytics.blogspot.com/2012/07/tracking-adjusted-bounce-rate-in-google.html') . '" target="_blank">',
							'</a>'
						));
						?>"></span>
						<br />

						<label for="ba_events_user_registration">
							<input name="better_analytics[events][user_registration]" type="checkbox" id="ba_events_user_registration" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['user_registration'] ); ?>>
							<?php esc_html_e('User Registration', 'better-analytics');?></label>
						<br />

						<label for="ba_events_create_comment">
							<input name="better_analytics[events][create_comment]" type="checkbox" id="ba_events_create_comment" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['create_comment'] ); ?>>
							<?php esc_html_e('Comments Being Created', 'better-analytics');?></label>
						<br />

						<label for="ba_events_youtube">
							<input name="better_analytics[events][youtube]" type="checkbox" id="ba_events_youtube" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['youtube'] ); ?>>
							<?php esc_html_e('YouTube Video Engagement', 'better-analytics');?></label>
							<span class="dashicons-before dashicons-info tooltip" title="<?php echo htmlspecialchars(sprintf(__('This works with the modern IFRAME YouTube embeds.  It does not work with the old OBJECT embeds.%1$sTracks things like video plays, pauses, plays to end, etc.', 'better-analytics'),
								'<p />'
							)); ?>"></span>

						<br />

						<label for="ba_events_email">
							<input name="better_analytics[events][email]" type="checkbox" id="ba_events_email" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['email'] ); ?>>
							<?php esc_html_e('Emails Sent/Opened', 'better-analytics');?></label>
						<br />

						<label for="ba_events_link_click">
							<input name="better_analytics[events][link_click]" type="checkbox" id="ba_events_link_click" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['link_click'] ); ?>>
							<?php esc_html_e('Clicks On External Links', 'better-analytics');?></label>
						<br />

						<label for="ba_events_downloads">
							<input name="better_analytics[events][downloads]" type="checkbox" id="ba_events_downloads" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['downloads'] ); ?>>
							<?php esc_html_e('File Downloads', 'better-analytics');?></label>
						<br />

						<label for="ba_events_page_scroll">
							<input name="better_analytics[events][page_scroll]" type="checkbox" id="ba_events_page_scroll" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['page_scroll'] ); ?>>
							<?php esc_html_e('Page Scroll Percent', 'better-analytics');?></label>
							<span class="dashicons-before dashicons-info tooltip" title="<?php esc_attr_e('This is the percent of the page the user scrolled down to view before leaving the page.', 'better-analytics'); ?>"></span>
						<br />

						<label for="ba_events_time_on_page">
							<input name="better_analytics[events][time_on_page]" type="checkbox" id="ba_events_time_on_page" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['time_on_page'] ); ?>>
							<?php esc_html_e('Time On Page', 'better-analytics');?></label>
						<span class="dashicons-before dashicons-info tooltip" title="<?php esc_attr_e('This is the amount of time (recorded in seconds) that the user spent on the individual page.', 'better-analytics'); ?>"></span>


					</fieldset>

				</td>
			</tr>

			<tr valign="top" class="group_general tab_content">
				<th scope="row"><?php esc_html_e('Privacy', 'better-analytics');?></th>
				<td>
					<fieldset>
						<label for="ba_anonymize_ips">
							<input name="better_analytics[anonymize_ips]" type="checkbox" id="ba_anonymize_ips" value="1" <?php checked('1', @$betterAnalyticsOptions['anonymize_ips'] ); ?>>
							<?php esc_html_e('Anonymize IPs', 'better-analytics');?></label>
						<span class="dashicons-before dashicons-info tooltip" title="<?php echo htmlspecialchars(sprintf(__('Detailed information about IP Anonymization in Google Analytics can be found %1$sover here%2$s.', 'better-analytics'),
							'<a href="' . esc_url('https://support.google.com/analytics/answer/2763052') . '" target="_blank">',
							'</a>'
						)); ?>"></span>
						<br />

						<label for="ba_demographic_tracking">
							<input name="better_analytics[demographic_tracking]" type="checkbox" id="ba_demographic_tracking" value="1" <?php checked('1', @$betterAnalyticsOptions['demographic_tracking'] ); ?>>
							<?php esc_html_e('Demographic & Interest Tracking', 'better-analytics');?></label>
						<span class="dashicons-before dashicons-info tooltip" title="<?php echo htmlspecialchars(sprintf(__('This allows you to view extra dimensions about users (Age, Gender, Affinity Categories, In-Market Segments and Other Categories.%1$sThis requires enabling the option in your %2$sGoogle Analytics account%3$s under %4$sProperty Settings%5$s.', 'better-analytics'),
							'<p />',
							'<a href="' . esc_url('https://www.google.com/analytics/web/?#management/Settings/') . '" target="_blank">',
							'</a>',
							'<strong>',
							'</strong>'
						)); ?>"></span>
						<br />

						<label for="ba_force_ssl">
							<input name="better_analytics[force_ssl]" type="checkbox" id="ba_force_ssl" value="1" <?php checked('1', @$betterAnalyticsOptions['force_ssl'] ); ?>>
							<?php esc_html_e('Force Analytics Traffic Over SSL', 'better-analytics');?></label>
						<span class="dashicons-before dashicons-info tooltip" title="<?php echo htmlspecialchars(sprintf(__('If your site is HTTPS based, Analytics traffic will always go over SSL.  If you have an insecure site, but wish Analytics traffic to still be secure, use this option.  Additionally, SSL traffic is going to be generally faster because it\'s able to utilize the %1$sSPDY protocol%2$s.', 'better-analytics'),
							'<a href="' . esc_url('https://wikipedia.org/wiki/SPDY') . '" target="_blank">',
							'</a>'
						)); ?>"></span>
					</fieldset>

				</td>
			</tr>

			<tr valign="top" class="group_general tab_content">
				<th scope="row"><?php esc_html_e('Link Source Tracking', 'better-analytics');?></th>
				<td>
					<fieldset>
						<label for="ba_souce_link_rss">
							<input name="better_analytics[source_link][rss]" type="checkbox" id="ba_souce_link_rss" value="1" <?php checked('1', @$betterAnalyticsOptions['source_link']['rss'] ); ?>>
							<?php esc_html_e('RSS', 'better-analytics');?></label>
						<span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('Links within RSS feed will be tagged to track the source/medium as being RSS.', 'better-analytics');?>"></span>
						<br />

						<label for="ba_souce_link_email">
							<input name="better_analytics[source_link][email]" type="checkbox" id="ba_souce_link_email" value="1" <?php checked('1', @$betterAnalyticsOptions['source_link']['email'] ); ?>>
							<?php esc_html_e('Email', 'better-analytics');?></label>
						<span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('Links within RSS feed will be tagged to track the source/medium as being Email.', 'better-analytics');?>"></span>

					</fieldset>

				</td>
			</tr>


			<tr valign="top" class="group_dimensions tab_content">
				<td colspan="2">
					<?php printf(__('If you want to track custom dimensions, you need to create the custom dimensions in your %1$sGoogle Analytics account settings%2$s (under %3$sCustom Definitions -> Custom Dimension%4$s).%5$sThey should be scoped as "%3$sHit%4$s".', 'better-analytics'),
						'<a href="' . esc_url('https://www.google.com/analytics/web/?#management/Settings/') . '" target="_blank">',
						'</a>',
						'<strong>',
						'</strong>',
						'<br /><br />'
					);

					if (!empty($profilePick[@$betterAnalyticsOptions['property_id']]))
					{
						$dimensions = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getDimensions($profilePick[@$betterAnalyticsOptions['property_id']][2], $betterAnalyticsOptions['property_id']);
						$dimensions = DigitalPointBetterAnalytics_Model_Reporting::parseDimensions($dimensions);
					}
					else

					{
						$dimensions = array();
					}

					?>
				</td>
			</tr>

			<tr valign="top" class="group_dimensions tab_content">
				<th scope="row"><?php esc_html_e('Dimension Indexes', 'better-analytics');?>
					<p class="description"
					   style="font-weight: normal;">
						<?php
							if (!$dimensions)
							{
								esc_html_e('Set to 0 to disable.', 'better-analytics');
							}
							else
							{
								/* translators: %1$s = <br /> */
								printf(esc_html__('Drop-down boxes are coming from%1$scustom dimensions defined within%1$syour Google Analytics account.', 'better-analytics'), '<br />');

							}

						?></p>
				</th>
				<td>
					<fieldset>
						<div style="display:table">
							<div style="display:table-row">
								<div style="display:table-cell;text-align:right;padding-right:10px;">
									<label
										for="ba_dimension_category"><?php esc_html_e('Categories: ', 'better-analytics');?></label>
								</div>
								<div style="display:table-cell;width:50%;">
									<?php

										if ($dimensions)
										{
											echo '<select data-placeholder="' . esc_html__('Pick dimension', 'better-analytics') . '" id="ba_dimension_category" name="better_analytics[dimension][category]" class="chosen-select">';

											echo '<option value="">' . esc_html__('[none]', 'better-analytics') . '</option>';

											foreach ($dimensions as $index => $name)
											{
												echo '<option value="' . $index . '"' . ($index == @$betterAnalyticsOptions['dimension']['category'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';
											}
											echo '</select>';

										}
										else
										{
											echo '<input type="number" name="better_analytics[dimension][category]"
											   id="ba_dimension_category" min="0" max="20" step="1"
											   value="' . esc_attr(intval(@$betterAnalyticsOptions['dimension']['category'])) . '"/>';
										}

									?>
								</div>
							</div>

							<div style="display:table-row">
								<div style="display:table-cell;text-align:right;padding-right:10px;">
									<label
										for="ba_dimension_author"><?php esc_html_e('Author: ', 'better-analytics');?></label>
								</div>
								<div style="display:table-cell;width:50%;">

									<?php

									if ($dimensions)
									{
										echo '<select data-placeholder="' . esc_html__('Pick dimension', 'better-analytics') . '" id="ba_dimension_author" name="better_analytics[dimension][author]" class="chosen-select">';

										echo '<option value="">' . esc_html__('[none]', 'better-analytics') . '</option>';

										foreach ($dimensions as $index => $name)
										{
											echo '<option value="' . $index . '"' . ($index == @$betterAnalyticsOptions['dimension']['author'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';
										}
										echo '</select>';

									}
									else
									{
										echo '<input type="number" name="better_analytics[dimension][author]"
											   id="ba_dimension_author" min="0" max="20" step="1"
											   value="' . esc_attr(intval(@$betterAnalyticsOptions['dimension']['author'])) . '"/>';
									}

									?>

								</div>
							</div>

							<div style="display:table-row">
								<div style="display:table-cell;text-align:right;padding-right:10px;">
									<label
										for="ba_dimension_tags"><?php esc_html_e('Tags: ', 'better-analytics');?></label>
								</div>
								<div style="display:table-cell;width:50%;">

									<?php

									if ($dimensions)
									{
										echo '<select data-placeholder="' . esc_html__('Pick dimension', 'better-analytics') . '" id="ba_dimension_tags" name="better_analytics[dimension][tag]" class="chosen-select">';

										echo '<option value="">' . esc_html__('[none]', 'better-analytics') . '</option>';

										foreach ($dimensions as $index => $name)
										{
											echo '<option value="' . $index . '"' . ($index == @$betterAnalyticsOptions['dimension']['tag'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';
										}
										echo '</select>';

									}
									else
									{
										echo '<input type="number" name="better_analytics[dimension][tag]"
											   id="ba_dimension_tags" min="0" max="20" step="1"
											   value="' . esc_attr(intval(@$betterAnalyticsOptions['dimension']['tag'])) . '"/>';
									}

									?>

								</div>
							</div>

							<div style="display:table-row">
								<div style="display:table-cell;text-align:right;padding-right:10px;">
									<label
										for="ba_dimension_year"><?php esc_html_e('Publication Year: ', 'better-analytics');?></label>
								</div>
								<div style="display:table-cell;width:50%;">

									<?php

									if ($dimensions)
									{
										echo '<select data-placeholder="' . esc_html__('Pick dimension', 'better-analytics') . '" id="ba_dimension_year" name="better_analytics[dimension][year]" class="chosen-select">';

										echo '<option value="">' . esc_html__('[none]', 'better-analytics') . '</option>';

										foreach ($dimensions as $index => $name)
										{
											echo '<option value="' . $index . '"' . ($index == @$betterAnalyticsOptions['dimension']['year'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';
										}
										echo '</select>';

									}
									else
									{
										echo '<input type="number" name="better_analytics[dimension][year]"
											   id="ba_dimension_user" min="0" max="20" step="1"
											   value="' . esc_attr(intval(@$betterAnalyticsOptions['dimension']['year'])) . '"/>';
									}

									?>

								</div>
							</div>

							<div style="display:table-row">
								<div style="display:table-cell;text-align:right;padding-right:10px;">
									<label
										for="ba_dimension_role"><?php esc_html_e('User Role: ', 'better-analytics');?></label>
								</div>
								<div style="display:table-cell;width:50%;">

									<?php

									if ($dimensions)
									{
										echo '<select data-placeholder="' . esc_html__('Pick dimension', 'better-analytics') . '" id="ba_dimension_role" name="better_analytics[dimension][role]" class="chosen-select">';

										echo '<option value="">' . esc_html__('[none]', 'better-analytics') . '</option>';

										foreach ($dimensions as $index => $name)
										{
											echo '<option value="' . $index . '"' . ($index == @$betterAnalyticsOptions['dimension']['role'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';
										}
										echo '</select>';

									}
									else
									{
										echo '<input type="number" name="better_analytics[dimension][role]"
											   id="ba_dimension_role" min="0" max="20" step="1"
											   value="' . esc_attr(intval(@$betterAnalyticsOptions['dimension']['role'])) . '"/>';
									}

									?>

								</div>
							</div>

							<div style="display:table-row">
								<div style="display:table-cell;text-align:right;padding-right:10px;">
									<label
										for="ba_dimension_user"><?php esc_html_e('Registered User: ', 'better-analytics');?></label>
								</div>
								<div style="display:table-cell;width:50%;">

									<?php

									if ($dimensions)
									{
										echo '<select data-placeholder="' . esc_html__('Pick dimension', 'better-analytics') . '" id="ba_dimension_user" name="better_analytics[dimension][user]" class="chosen-select">';

										echo '<option value="">' . esc_html__('[none]', 'better-analytics') . '</option>';

										foreach ($dimensions as $index => $name)
										{
											echo '<option value="' . $index . '"' . ($index == @$betterAnalyticsOptions['dimension']['user'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';
										}
										echo '</select>';

									}
									else
									{
										echo '<input type="number" name="better_analytics[dimension][user]"
											   id="ba_dimension_user" min="0" max="20" step="1"
											   value="' . esc_attr(intval(@$betterAnalyticsOptions['dimension']['user'])) . '"/>';
									}

									?>

								</div>
							</div>

						</div>
					</fieldset>

				</td>
			</tr>



			<tr valign="top" class="group_social tab_content">
				<th scope="row"><?php esc_html_e('Button Engagement To Track', 'better-analytics');?></th>
				<td>

					<fieldset>
						<label for="ba_social_facebook">
							<input name="better_analytics[social][facebook]" type="checkbox" id="ba_social_facebook" value="1" <?php checked('1', @$betterAnalyticsOptions['social']['facebook'] ); ?>>
							<?php esc_html_e('Facebook Like', 'better-analytics');?></label>
						<br />

						<label for="ba_social_twitter">
							<input name="better_analytics[social][twitter]" type="checkbox" id="ba_social_twitter" value="1" <?php checked('1', @$betterAnalyticsOptions['social']['twitter'] ); ?>>
							<?php esc_html_e('Twitter', 'better-analytics');?></label>
						<br />

						<label for="ba_social_google">
							<input type="checkbox" id="ba_social_google" value="1" checked="checked" disabled="disabled">
							<?php esc_html_e('Google+ (Google Analytics always tracks)', 'better-analytics');?></label>
						<br />

						<label for="ba_social_pinterest">
							<input name="better_analytics[social][pinterest]" type="checkbox" id="ba_social_pinterest" value="1" <?php checked('1', @$betterAnalyticsOptions['social']['pinterest'] ); ?>>
							<?php esc_html_e('Pinterest', 'better-analytics');?></label>
						<br />

						<label for="ba_social_linkedin">
							<input name="better_analytics[social][linkedin]" type="checkbox" id="ba_social_linkedin" value="1" <?php checked('1', @$betterAnalyticsOptions['social']['linkedin'] ); ?>>
							<?php esc_html_e('LinkedIn', 'better-analytics');?></label>

					</fieldset>

				</td>
			</tr>

			<tr valign="top" class="group_advertising tab_content">
				<th scope="row"><?php esc_html_e('Advertisement Clicks To Track', 'better-analytics');?></th>
				<td>

					<fieldset>
						<label for="ba_ads_adsense"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[ads][adsense]" type="checkbox" id="ba_ads_adsense" value="1" <?php checked('1', @$betterAnalyticsOptions['ads']['adsense']); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('AdSense', 'better-analytics');?></label>
						<br />

						<label for="ba_ads_outbrain"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[ads][outbrain]" type="checkbox" id="ba_ads_outbrain" value="1" <?php checked('1', @$betterAnalyticsOptions['ads']['outbrain'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('Outbrain', 'better-analytics');?></label>
						<br />

						<label for="ba_ads_revcontent"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[ads][revcontent]" type="checkbox" id="ba_ads_revcontent" value="1" <?php checked('1', @$betterAnalyticsOptions['ads']['revcontent'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('RevContent', 'better-analytics');?></label>
						<br />

						<label for="ba_ads_taboola"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[ads][taboola]" type="checkbox" id="ba_ads_taboola" value="1" <?php checked('1', @$betterAnalyticsOptions['ads']['taboola'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('Taboola', 'better-analytics');?></label>
						<br />

						<label for="ba_ads_digitalpoint">
							<input name="better_analytics[ads][digitalpoint]" type="checkbox" id="ba_ads_digitalpoint" value="1" <?php checked('1', @$betterAnalyticsOptions['ads']['digitalpoint'] ); ?>>
							<?php esc_html_e('Digital Point', 'better-analytics');?></label>


					</fieldset>

				</td>
			</tr>



			<tr valign="top" class="group_monitor tab_content">
				<th scope="row"><?php esc_html_e('Site Issues To Track', 'better-analytics');?></th>
				<td>
					<fieldset>

						<label for="ba_events_error_404">
							<input name="better_analytics[events][error_404]" type="checkbox" id="ba_events_error_404" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['error_404'] ); ?>>
							<?php esc_html_e('Page Not Found (404)', 'better-analytics');?></label>
						<br />

						<label for="ba_events_missing_images"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[events][missing_images]" type="checkbox" id="ba_events_missing_images" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['missing_images'] );  disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('Images Not Loading', 'better-analytics');?>
							<span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('This relies on the ability of the user\'s browser to load images which will vary between browsers and Internet connections.  This event can be a useful tool for finding missing images, but it keep in mind it will log any image that the end user\'s browser did not load (for any reason).', 'better-analytics');?>"></span>
						</label>
						<br />

						<label for="ba_events_errors_javascript"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[events][error_js]" type="checkbox" id="ba_events_errors_javascript" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['error_js'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('JavaScript Errors', 'better-analytics');?></label>
						<br />

						<label for="ba_events_errors_ajax"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[events][error_ajax]" type="checkbox" id="ba_events_errors_ajax" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['error_ajax'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('AJAX Errors', 'better-analytics');?></label>
						<br />
						<label for="ba_events_errors_console"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[events][error_console]" type="checkbox" id="ba_events_errors_console" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['error_console'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('Browser Console Errors', 'better-analytics');?></label>
						<br />
						<label for="ba_events_errors_youtube"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[events][error_youtube]" type="checkbox" id="ba_events_errors_youtube" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['error_youtube'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('YouTube Errors', 'better-analytics');?>
							<span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('This allows you to quickly find old videos that the author has removed or disabled embeding for.', 'better-analytics');?>"></span>
						</label>

					</fieldset>

				</td>
			</tr>

			<tr valign="top" class="group_monitor tab_content">
				<th scope="row"><?php esc_html_e('Debugging Events', 'better-analytics');?></th>
				<td>
					<fieldset>

						<label for="ba_events_ajax_requests">
							<input name="better_analytics[events][ajax_request]" type="checkbox" id="ba_events_ajax_requests" value="1" <?php checked('1', @$betterAnalyticsOptions['events']['ajax_request'] ); ?>>
							<?php esc_html_e('AJAX Requests', 'better-analytics');?></label>
					</fieldset>

				</td>
			</tr>

			<tr valign="top" class="group_ecommerce tab_content">
				<th scope="row">Platforms</th>
				<td>
					<fieldset>
						<label for="ba_ecommerce_woo"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
							<input name="better_analytics[ecommerce][woo]" type="checkbox" id="ba_ecommerce_woo" value="1" <?php checked('1', @$betterAnalyticsOptions['ecommerce']['woo'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php
								esc_html_e('WooCommerce', 'better-analytics');
							if (!in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
							{
								wp_enqueue_script('plugin-install');
								wp_enqueue_script('thickbox');
								wp_enqueue_style('thickbox');

								printf('<p class="description">%s <a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a></p>',
									esc_html__('WooCommerce not enabled/active.', 'better-analytics'),
									esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=woocommerce' .
										'&TB_iframe=true&width=600&height=550' ) ),
									esc_attr( sprintf( __( 'More information about %s' ), 'WooCommerce' ) ),
									esc_attr( 'WooCommerce'),
									esc_html__( 'View details' )
								);
							}

							?>

						</label>

					</fieldset>

				</td>
			</tr>



			<?php

			if ($profiles)
			{
				$formatParams = DigitalPointBetterAnalytics_Base_Admin::groupProfiles($profiles['items']);

				echo '<tr valign="top" class="group_api tab_content">
						<th scope="row">' . esc_html__('Profile Used For Reporting', 'better-analytics') . '</th>
						<td>';

				echo '<select data-placeholder="' . esc_html__('Pick profile', 'better-analytics') . '" id="ba_pick_api_profile" name="better_analytics[api][profile]" class="chosen-select">';

				echo '<option value="">' . esc_html__('please pick a profile', 'better-analytics') . '</option>';

				foreach ($formatParams as $url => $group)
				{
					echo '<optgroup label="' . htmlentities($url) . '">';

					foreach ($group as $internalWebPropertyId => $name)
					{
						echo '<option value="' . $internalWebPropertyId . '"' . ($internalWebPropertyId == @$betterAnalyticsOptions['api']['profile'] ? ' selected="selected"' : '') . '>' . htmlentities($name) . '</option>';

					}

					echo '</optgroup>';
				}
				echo '</select>
						</td>
					</tr>';
			}
				?>



			<tr valign="top" class="group_api tab_content">
				<th scope="row"></th>
				<td>

					<?php
						if (get_site_option('ba_site_tokens') && get_site_option('ba_site_tokens') != get_option('ba_tokens'))
						{
							$multisiteMode = true;
							esc_html_e('Multisite Mode: Analytics account linked at network level');
						}
						else
						{
							$multisiteMode = false;

							if (!get_option('ba_tokens'))
							{
								echo DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getCreateAccountMessage() . '<br /><br />';
							}

					?>

					<a id="ba_select_profile" class="button" href="<?php menu_page_url('better-analytics_auth'); ?>"><?php
							get_option('ba_tokens') ? esc_html_e('Link/Authenticate A Different Google Analytics Account', 'better-analytics') : esc_html_e('Link/Authenticate Your Google Analytics Account', 'better-analytics');?></a>
					<?php
						}
					?>
				</td>
			</tr>


			<?php
				if (!$multisiteMode)
				{
					?>
					<tr valign="top" class="group_api tab_content">
						<th scope="row"></th>
						<td>
							<fieldset>
								<label for="ba_api_use_own">
									<input name="better_analytics[api][use_own]" type="checkbox" id="ba_api_use_own"
										   value="1" <?php checked('1', @$betterAnalyticsOptions['api']['use_own']); ?>>
									<?php esc_html_e('Use Your Own Project Credentials', 'better-analytics');?></label>
								<span class="dashicons-before dashicons-info tooltip"
									  title="<?php echo htmlspecialchars(sprintf(__('If you have your own Google API Project that you wish to use, you can use your credentials for that web application.', 'better-analytics')));?>"></span>

							</fieldset>

						</td>
					</tr>

					<tr valign="top"
						class="group_api tab_content api_hideable"<?php echo(!@$betterAnalyticsOptions['api']['use_own'] ? ' style="display:none"' : '')?>>
						<td colspan="2">

							<?php echo '<fieldset style="border:1px solid grey;margin:10px;padding:20px;"><legend style="padding:0 5px;font-weight:bold;font-size:120%">' . esc_html__('Configuration', 'better-analytics') . '</legend>' .
								/* translators: %1$s = <strong>, %2$s = </strong> */
								sprintf(__('Project needs to have the %1$sAnalytics API%2$s enabled under %1$sAPIs & auth -> APIs%2$s.', 'better_analytics'), '<strong>', '</strong>') . '<br /><br />' .
								/* translators: %1$s = <strong>, %2$s = </strong> */
								sprintf(__('Under %1$sAPIs & auth -> Credentials%2$s, you need to %1$sCreate new Client ID%2$s that is a %1$sWeb application%2$s with an authorized redirect URI of: %1$s%3$s%2$s', 'better-analytics'), '<strong>', '</strong>', menu_page_url('better-analytics_auth', false))
								. '</fieldset>';?>
						</td>
					</tr>


					<tr valign="top"
						class="group_api tab_content api_hideable"<?php echo(!@$betterAnalyticsOptions['api']['use_own'] ? ' style="display:none"' : '')?>>
						<th scope="row"><?php esc_html_e('Client ID', 'better-analytics');?></th>
						<td>
							<input type="text" name="better_analytics[api][client_id]" id="ba_api_client_id"
								   placeholder="0000000000.apps.googleusercontent.com"
								   value="<?php echo esc_attr(@$betterAnalyticsOptions['api']['client_id']); ?>"/>
						</td>
					</tr>

					<tr valign="top"
						class="group_api tab_content api_hideable"<?php echo(!@$betterAnalyticsOptions['api']['use_own'] ? ' style="display:none"' : '')?>>
						<th scope="row"><?php esc_html_e('Client Secret', 'better-analytics');?></th>
						<td>
							<input type="text" name="better_analytics[api][client_secret]" id="ba_api_client_secret"
								   value="<?php echo esc_attr(@$betterAnalyticsOptions['api']['client_secret']); ?>"/>
						</td>
					</tr>
			<?php
				}
			?>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Roles To Not Track', 'better-analytics');?> <span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('If a logged in user is part of one of these groups, Analytics will not track them.', 'better-analytics');?>"></span></th>
				<td>

					<select data-placeholder="<?php esc_html_e('Pick roles to not track', 'better-analytics'); ?>" id="ba_roles_no_track" name="better_analytics[roles_no_track][]" multiple class="chosen-select">
						<option value=""></option>

						<?php
							global $wp_roles;
							foreach ($wp_roles->roles as $role => $val)
							{
								echo '<option value="' . $role . '"' . (in_array($role, (array)@$betterAnalyticsOptions['roles_no_track']) ? ' selected="selected"' : '') . '>' . $val['name'] . '</option>';
							}
						?>
					</select>
				</td>
			</tr>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Roles Able To View Reports/Dashboard', 'better-analytics');?></th>
				<td>

					<select data-placeholder="<?php esc_html_e('Pick roles that are able to view reports', 'better-analytics'); ?>" id="ba_roles_view_reports" name="better_analytics[roles_view_reports][]" multiple class="chosen-select">
						<option value=""></option>

						<?php
							foreach ($wp_roles->roles as $role => $val)
							{
								echo '<option value="' . $role . '"' . (in_array($role, (array)@$betterAnalyticsOptions['roles_view_reports']) ? ' selected="selected"' : '') . '>' . $val['name'] . '</option>';
							}
						?>
					</select>

					<fieldset style="padding-top:3px;">
						<label for="ba_author_view_reports">
							<input name="better_analytics[author_view_reports]" type="checkbox" id="ba_author_view_reports" value="1" <?php checked('1', @$betterAnalyticsOptions['author_view_reports'] ); ?>>
							<?php esc_html_e('Author Can View Page Analytics', 'better-analytics');?></label>
					</fieldset>

				</td>
			</tr>

			<?php
				$currentUser = wp_get_current_user();
			?>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Permissions To Settings', 'better-analytics');?></th>
				<td>
					<fieldset>
						<label for="ba_lock_settings_user_id">
							<input name="better_analytics[lock_settings_user_id]" type="checkbox" id="ba_lock_settings_user_id" value="<?php echo @$currentUser->ID; ?>" <?php checked('1', @$betterAnalyticsOptions['lock_settings_user_id'] ); ?>>
							<?php esc_html_e('Only Your User Account', 'better-analytics');?></label>
						<span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('If you want to disable all other admin accounts from having access to the Better Analytics settings, use this option.  Keep in mind, that only your account will have access to change any Better Analytics settings.', 'better-analytics');?>"></span>

					</fieldset>

				</td>
			</tr>



			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('File Extensions To Track As Downloads', 'better-analytics');?> <span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('If a logged in user is part of one of these groups, Analytics will not track them.', 'better-analytics');?>"></span></th>
				<td>
					<select data-placeholder="<?php esc_html_e('Pick file extensions', 'better-analytics'); ?>" id="ba_file_extensions" name="better_analytics[file_extensions][]" multiple class="chosen-select">
						<option value=""></option>

						<?php
							$fileTypes = array(
								'avi' => esc_html__('Audio Video Interleave (.avi)', 'better-analytics'),
								'dmg' => esc_html__('Apple Disk Image (.dmg)', 'better-analytics'),
								'doc' => esc_html__('Word (.doc)', 'better-analytics'),
								'exe' => esc_html__('Executable (.exe)', 'better-analytics'),
								'gz' => esc_html__('gzip (.gz)', 'better-analytics'),
								'mpg' => esc_html__('MPEG-1 Video (.mpg)', 'better-analytics'),
								'mp3' => esc_html__('MP3 (.mp3)', 'better-analytics'),
								'pdf' => esc_html__('Acrobat (.pdf)', 'better-analytics'),
								'ppt' => esc_html__('PowerPoint (.ppt)', 'better-analytics'),
								'psd' => esc_html__('Photoshop (.psd)', 'better-analytics'),
								'rar' => esc_html__('RAR (.rar)', 'better-analytics'),
								'wmv' => esc_html__('Windows Media Video (.wmv)', 'better-analytics'),
								'xls' => esc_html__('Excel (.xls)', 'better-analytics'),
								'zip' => esc_html__('Zip Archive (.zip)', 'better-analytics')
							);

							foreach ($fileTypes as $extension => $type)
							{
								echo '<option value="' . $extension . '"' . (in_array($extension, @$betterAnalyticsOptions['file_extensions']) ? ' selected="selected"' : '') . '>' . $type . '</option>';
							}
							?>
					</select>
				</td>
			</tr>


			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Track Users With Analytics Blockers', 'better-analytics');?> <span class="dashicons-before dashicons-info tooltip" title="<?php

					/* translators: %1$s = <strong>, %2$s = </strong>, %3$s = <p /> */
					echo htmlspecialchars(sprintf(__('If a user has a mechanism that is blocking them from being tracked with Google Analytics, you can use this option to do server-side page view tracking of that user.%3$s  For the most accuracy, %1$sit\'s not recommended to use this option for anything beyond "Registered Users"%2$s (having an account allows the system to track them uniquely on the backend).%3$s  Logging page views for "Humans" will lead to a lot of niche bots being tracked (ones not popular enough to be widely known as a spider).', 'better-analytics'), '<strong>', '</strong>', '<p />'));
					?>"></span></th>
				<td>
					<fieldset id="ba_track_blocked"<?php echo (!intval(@$betterAnalyticsInternal) ? ' class="pro"' : ''); ?>>
						<label>
							<input name="better_analytics[track_blocked]" type="radio" value="never" <?php checked( 'never', @$betterAnalyticsOptions['track_blocked'] ); ?>>
							<?php esc_html_e('Never', 'better-analytics');?></label>
						<br />

						<label>
							<input name="better_analytics[track_blocked]" type="radio" value="logged_in" <?php checked( 'logged_in', @$betterAnalyticsOptions['track_blocked'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('Registered Users', 'better-analytics');?></label>
						<br />

						<label>
							<input name="better_analytics[track_blocked]" type="radio" value="humans" <?php checked( 'humans', @$betterAnalyticsOptions['track_blocked'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('Humans', 'better-analytics');?></label>
						<br />

						<label>
							<input name="better_analytics[track_blocked]" type="radio" value="everything" <?php checked( 'everything', @$betterAnalyticsOptions['track_blocked'] ); disabled(0, intval(@$betterAnalyticsInternal)); ?>>
							<?php esc_html_e('Everything', 'better-analytics');?></label>

					</fieldset>

				</td>
			</tr>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Location For Analytics Code', 'better-analytics');?></th>
				<td>
					<fieldset id="ba_javascript_location">
						<label>
							<input name="better_analytics[javascript][location]" type="radio" value="header" <?php checked( 'header', @$betterAnalyticsOptions['javascript']['location'] ); ?>>
							<?php esc_html_e('Header', 'better-analytics');?></label>
						<br />

						<label>
							<input name="better_analytics[javascript][location]" type="radio" value="footer" <?php checked( 'footer', @$betterAnalyticsOptions['javascript']['location'] ); ?>>
							<?php esc_html_e('Footer', 'better-analytics');?></label>
						<br />

						<label>
							<input name="better_analytics[javascript][location]" type="radio" value="none" <?php checked( 'none', @$betterAnalyticsOptions['javascript']['location'] ); ?>>
							<?php esc_html_e('No JavaScript (you have your own/other already)', 'better-analytics');?></label>


						<br />
						<label for="ba_use_in_admin">
							<input name="better_analytics[javascript][use_in_admin]" type="checkbox" id="ba_use_in_admin" value="1" <?php checked('1', @$betterAnalyticsOptions['javascript']['use_in_admin'] ); ?>>
							<?php esc_html_e('Use In Staff Area (wp-admin)', 'better-analytics');?></label>
					</fieldset>
				</td>
			</tr>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('When To Run Analytics Code', 'better-analytics');?></th>
				<td>
					<fieldset id="ba_javascript_run_time">
						<label>
							<input name="better_analytics[javascript][run_time]" type="radio" value="immediately" <?php checked( 'immediately', @$betterAnalyticsOptions['javascript']['run_time'] ); ?>>
							<?php esc_html_e('Immediately', 'better-analytics');?></label>
						<br />

						<label>
							<input name="better_analytics[javascript][run_time]" type="radio" value="ready" <?php checked( 'ready', @$betterAnalyticsOptions['javascript']['run_time'] ); ?>>
							<?php esc_html_e('After Page Loads', 'better-analytics');?></label>

					</fieldset>
				</td>
			</tr>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Campaign Tracking Within URLs', 'better-analytics');?></th>
				<td>
					<fieldset id="ba_javascript_campaign_tracking">
						<label>
							<input name="better_analytics[campaign_tracking]" type="radio" value="anchor" <?php checked( 'anchor', @$betterAnalyticsOptions['campaign_tracking'] ); ?>>
							<?php esc_html_e('In Anchor', 'better-analytics');?></label>
						<br />

						<label>
							<input name="better_analytics[campaign_tracking]" type="radio" value="params" <?php checked( 'params', @$betterAnalyticsOptions['campaign_tracking'] ); ?>>
							<?php esc_html_e('In Parameters', 'better-analytics');?></label>

					</fieldset>
				</td>
			</tr>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Sample Rate', 'better-analytics');?> <span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('Specifies what percentage of users should be tracked.  Very large sites may need to use a lower sample rate to stay within Google Analytics processing limits.', 'better-analytics');?>"></span>
				</th>
				<td>

					<fieldset>
						<div style="display:table">
							<div style="display:table-row">
								<div style="display:table-cell">
									<input type="number" name="better_analytics[sample_rate]" id="ba_sample_rate" min="1" max="100" step="1" value="<?php echo esc_attr( intval(@$betterAnalyticsOptions['sample_rate']) >= 1 && intval(@$betterAnalyticsOptions['sample_rate']) <= 100 ? intval(@$betterAnalyticsOptions['sample_rate']) : 100 ); ?>" /> %
								</div>
							</div>
						</div>
					</fieldset>

				</td>
			</tr>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('User Engagement Time', 'better-analytics');?> <span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('This is the time (in seconds) before we consider the user "engaged".  This setting only applies if you use the "User Engagement" event tracking (under General tab).', 'better-analytics');?>"></span>
				</th>
				<td>

					<fieldset>
						<div style="display:table">
							<div style="display:table-row">
								<div style="display:table-cell">
									<input type="number" name="better_analytics[engagement_time]" id="ba_engagement_time" min="1" max="600" step="1" value="<?php echo esc_attr( intval(@$betterAnalyticsOptions['engagement_time']) >= 1 && intval(@$betterAnalyticsOptions['engagement_time']) <= 600 ? intval(@$betterAnalyticsOptions['engagement_time']) : 15 ); ?>" /> <?php esc_html_e('seconds', 'better-analytics');?>
								</div>
							</div>
						</div>
					</fieldset>

				</td>
			</tr>


			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Extra JavaScript', 'better-analytics');?> <span class="dashicons-before dashicons-info tooltip" title="<?php echo htmlspecialchars(sprintf(__('This allows you to add your own JavaScript code to the Analytics tracking code.  This JavaScript is inserted right before the %1$s function.', 'better-analytics'), '<strong>ga(\'send\', \'pageview\');</strong>')); ?>"></span></th>
				<td>
					<textarea name="better_analytics[extra_js]" rows="10" cols="50" id="ba_extra_js" class="large-text code"><?php echo esc_textarea( @$betterAnalyticsOptions['extra_js'] ); ?></textarea>
				</td>
			</tr>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Debugging', 'better-analytics');?></th>
				<td>
					<fieldset>
						<label for="ba_debugging">
							<input name="better_analytics[debugging]" type="checkbox" id="ba_debugging" value="1" <?php checked('1', @$betterAnalyticsOptions['debugging'] ); ?>>
							<?php esc_html_e('Enable Google Analytics Debugging', 'better-analytics');?></label>
							<span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('This will display Google Analytics debug data in the browser console.', 'better-analytics');?>"></span>

					</fieldset>

				</td>
			</tr>

			<tr valign="top" class="group_advanced tab_content">
				<th scope="row"><?php esc_html_e('Suppress Dashboard Notices', 'better-analytics');?></th>
				<td>
					<fieldset>
						<label for="ba_hide_api_message">
							<input name="better_analytics[hide_api_message]" type="checkbox" id="ba_hide_api_message" value="1" <?php checked('1', @$betterAnalyticsOptions['hide_api_message'] ); ?>>
							<?php esc_html_e('Hide "API Not Linked" Notice', 'better-analytics');?></label>
						<span class="dashicons-before dashicons-info tooltip" title="<?php esc_html_e('If you choose not to link your Google Analytics account for reporting and management, you can disable the notice about it with this option (the notice will still show on Better Analytics config/reporting pages).', 'better-analytics');?>"></span>

					</fieldset>

				</td>
			</tr>

		</table>

		<?php submit_button(); ?>
	</form>

</div>