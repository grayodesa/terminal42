<?php
	wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

	$betterAnalyticsOptions = get_option('better_analytics');

	$property = null;
	$checks = array();
	$hasTokens = false;
	$hasAccounts = true;

	$dimensions = array('Categories', 'Author', 'Tags', 'Year', 'Role', 'User');

	$reportingClass = DigitalPointBetterAnalytics_Helper_Reporting::getInstance();

	if ($reportingClass->checkAccessToken(false))
	{
		$hasTokens = true;
		$checks['profiles'] = $reportingClass->getProfiles();
		$checks['matchingProfile'] = $reportingClass->getProfileByProfileId($betterAnalyticsOptions['api']['profile']);

		$checks['siteSearchSetup'] = @$checks['matchingProfile']['siteSearchQueryParameters'] == 's';
		$checks['ecommerceTracking'] = @$checks['matchingProfile']['eCommerceTracking'];
		$checks['enhancedEcommerceTracking'] = @$checks['matchingProfile']['enhancedECommerceTracking'];

		$property = $reportingClass->getPropertyByPropertyId(@$checks['matchingProfile']['accountId'], $betterAnalyticsOptions['property_id']);

		$checks['level'] = @$property['level'];
		$checks['industryVertical'] = @$property['industryVertical'];

		$checks['dimensions'] = $reportingClass->getDimensionsByPropertyId(@$checks['matchingProfile']['accountId'], $betterAnalyticsOptions['property_id'], $dimensions);
	}

	$checks['licensed'] = DigitalPointBetterAnalytics_Helper_Api::check(true);

	if (@$_REQUEST['action'] == 'config')
	{
		if (!$hasTokens)
		{
			esc_html_e('You need to first link a Google Analytics account to auto-configure it.', 'better-analytics');
		}
		elseif (empty($_REQUEST['vertical']))
		{
			wp_enqueue_script('chosen_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.jquery.min.js', array(), BETTER_ANALYTICS_VERSION );
			wp_enqueue_style('chosen_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.min.css', array(), BETTER_ANALYTICS_VERSION);

			wp_enqueue_script('better-analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );

			$verticals = $reportingClass->getIndustryVerticals();

			if (!$checks['matchingProfile'])
			{
				if ($accounts = $reportingClass->getAccounts())
				{
					$accounts = DigitalPointBetterAnalytics_Model_Reporting::parseAccounts($accounts, true);
				}
				if(!$accounts)
				{
					$hasAccounts = false;
				}
			}
			else
			{
				$accounts = null;
			}

			echo '<div class="wrap test-configure">

			<h2>' . esc_html__('Auto-Configure' , 'better-analytics') . '</h2>';

			if ($hasAccounts)
			{
				echo '<div class="error"><p>' . esc_html__('Only use this option if you are sure you want to auto-configure everything.', 'better-analytics') . '</p></div>';

				echo '<form action="' . esc_url(menu_page_url('better-analytics_test', false)) . '" method="POST" style="padding:15px">';
				echo '<input type="hidden" name="action" value="config">';

				if ($accounts)
				{
					esc_html_e('Please select the Google Analytics account that you wish to create a new Web Property within:', 'better-analytics');

					echo '<select style="width:auto;" name="account_id" data-placeholder="' . esc_html__('Pick account', 'better-analytics') . '" id="ba_pick_account" class="chosen-select">';

					foreach ($accounts as $accountId => $accountName)
					{
						echo '<option value="' . absint($accountId) . '">' . htmlentities($accountName) . '</option>';
					}
					echo '</select><br /><br />';
				}

				esc_html_e('Please select the industry vertical that you wish your Web Property to be assigned to:', 'better-analytics');

				echo '<select style="width:auto;" name="vertical" data-placeholder="' . esc_html__('Pick industry vertical', 'better-analytics') . '" id="ba_pick_vertical" class="chosen-select">';

				foreach ($verticals as $vertical)
				{
					echo '<option' . ($vertical == $property['industryVertical'] ? ' selected="selected"' : '') . '>' . htmlentities($vertical) . '</option>';
				}
				echo '</select><br /><br />';

				esc_html_e('This will perform the following actions:', 'better-analytics');
				echo '<ul style="list-style: initial;padding-left:30px;">';

				if (!$checks['matchingProfile'])
				{
					echo '<li>' . esc_html__('Create a new Web Property within your selected Google Analytics account', 'better-analytics') . '</li>';
				}
				echo '<li>' . sprintf(esc_html__('Set Site Search Query Parameter to %s on Analytics account', 'better-analytics'), '<strong>"s"</strong>') . '</li>';
				echo '<li>' . esc_html__('Enable Ecommerce options on Analytics account', 'better-analytics') . '</li>';
				echo '<li>' . esc_html__('Set Industry Vertical on Analytics account', 'better-analytics') . '</li>';
				echo '<li>' . sprintf(esc_html__('Create %1$u custom dimensions for this Analytics property if they do not already exist (%2$s)', 'better-analytics'), count($dimensions), implode(', ', $dimensions)) . '</li>';
				echo '<li>' . sprintf(esc_html__('Map the %1$u custom dimensions in your Better Analytics settings', 'better-analytics'), count($dimensions)) . '</li>';
				echo '</ul>';

				submit_button(esc_html__('Auto-Configure', 'better-analytics'));

				echo '</form>';
			}
			else
			{
				echo '<div class="error"><p>' .
						esc_html__('Your linked Google account doesn\'t appear to have any Google Analytics accounts within it.  You can link a different Google account or create a Google Analytics account within it.', 'better-analytics') .
					'<br/><br/><a class="button button-primary" href="' . esc_url(menu_page_url('better-analytics_auth', false)) .'">' . esc_html__('Link/Authenticate A Different Google Analytics Account', 'better-analytics') . '</a>' .
					' &nbsp; <a class="button button-primary" href="' . esc_url('https://www.google.com/analytics/') .'" target="_blank">' . esc_html__('Create Analytics Account Manually Within Your Google Account', 'better-analytics') . '</a>' .

				'</p></div>';
			}
			echo '</div>';
		}
		elseif($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$vertical = sanitize_text_field($_REQUEST['vertical']);
			$accountId = absint(@$_REQUEST['account_id']);

			$overQuota = false;

			if ($accountId = absint(@$_REQUEST['account_id']))
			{
				$property = $reportingClass->insertWebProperty($accountId, array(
					'industryVertical' => $vertical,
					'kind' => 'analytics#webproperty',
					'name' => get_option('blogname'),
					'websiteUrl' => get_option('siteurl'),
				));

				if ($reportingClass->checkApiErrorType($property, 'noQuota'))
				{
					$overQuota = true;
				}

				if (!$overQuota && !empty($property['id']))
				{
					$profile = $reportingClass->insertProfile($property['accountId'], $property['id'], array(
						'kind' => 'analytics#profile',
						'type' => 'WEB',
						'name' => 'All Web Site Data',
						'timezone' => get_option('timezone_string'),
						'websiteUrl' => get_option('siteurl'),
						'eCommerceTracking' => true,
						'enhancedECommerceTracking' => true,
						'siteSearchQueryParameters' => 's',
						'stripSiteSearchQueryParameters' => true
					));

					if ($reportingClass->checkApiErrorType($profile, 'noQuota'))
					{
						$overQuota = true;
					}
					else
					{
						$betterAnalyticsOptions['property_id'] = $profile['webPropertyId'];
						$betterAnalyticsOptions['api']['profile'] = $profile['id'];
					}

					// Really seems like an Analytics bug because the enhancedECommerceTracking option doesn't take upon insert, so for now, PATCHing after the fact.
					// See:  https://code.google.com/p/analytics-issues/issues/detail?id=688
					if (!$overQuota && !empty($profile['id']) && empty($profile['enhancedECommerceTracking']))
					{
						$profile = $reportingClass->patchProfile($property['accountId'], $property['id'], $profile['id'], array(
							'enhancedECommerceTracking' => true,
						));

						if ($reportingClass->checkApiErrorType($profile, 'noQuota'))
						{
							$overQuota = true;
						}
					}
				}
			}
			else
			{
				if (!empty($property['accountId']) && !empty($property['id']) && @$property['industryVertical'] != $vertical)
				{
					$property = $reportingClass->patchWebProperty($property['accountId'], $property['id'], array(
						'industryVertical' => $vertical
					));

					if ($reportingClass->checkApiErrorType($property, 'noQuota'))
					{
						$overQuota = true;
					}
				}

				if (
					!$overQuota && !empty($property['accountId']) && !empty($property['id']) && !empty($betterAnalyticsOptions['api']['profile']) &&
					(
						empty($check['matchingProfile']['eCommerceTracking']) ||
						empty($check['matchingProfile']['enhancedECommerceTracking']) ||
						@$check['matchingProfile']['siteSearchQueryParameters'] != 's' ||
						empty($check['matchingProfile']['stripSiteSearchQueryParameters'])
					)
				)
				{
					$profile = $reportingClass->patchProfile($property['accountId'], $property['id'], $betterAnalyticsOptions['api']['profile'], array(
						'eCommerceTracking' => true,
						'enhancedECommerceTracking' => true,
						'siteSearchQueryParameters' => 's',
						'stripSiteSearchQueryParameters' => true
					));

					if ($reportingClass->checkApiErrorType($profile, 'noQuota'))
					{
						$overQuota = true;
					}
				}
			}

			foreach ($dimensions as $dimension)
			{
				if (empty($checks['dimensions'][$dimension]))
				{
					$results = $reportingClass->insertCustomDimension($property['accountId'], $property['id'], array(
						'name' => $dimension,
						'scope' => 'HIT',
						'active' => true
					));

					if ($reportingClass->checkApiErrorType($results, 'noQuota'))
					{
						$overQuota = true;
						break;
					}
				}
			}

			if ($overQuota)
			{
				set_transient('ba_last_error', esc_html__('Google API Quota Exceeded (you will need to configure your Google Analytics account manually).'), 10);
			}

			$reportingClass->deleteProfileCache();
			$reportingClass->deleteProfileCache($property['accountId'], null);
			$reportingClass->deleteDimensionCache($property['accountId'], $property['id']);

			$checks['dimensions'] = $reportingClass->getDimensionsByPropertyId($property['accountId'], $property['id'], $dimensions);

			foreach ($dimensions as $dimension)
			{
				if (!empty($checks['dimensions'][$dimension]))
				{
					$dimLower = strtolower($dimension);
					// Blah... ugly renaming
					if ($dimLower == 'tags')
					{
						$dimLower = 'tag';
					}
					elseif ($dimLower == 'categories')
					{
						$dimLower = 'category';
					}

					$betterAnalyticsOptions['dimension'][$dimLower] = $checks['dimensions'][$dimension]['index'];
				}
			}
			update_option('better_analytics', $betterAnalyticsOptions);

			wp_redirect(esc_url(menu_page_url('better-analytics_test', false)), 302);
		}

	}
	else {

		?>

		<div class="wrap">

			<h2><?php esc_html_e('Test Analytics Integration', 'better-analytics');?></h2>

			<table class="form-table" id="ba_test">
				<?php esc_html_e('This is a checklist of things for full integration with Google Analytics. You can click on any of the titles to be taken where you need to go to configure that item.', 'better-analytics');?>

				<tr>
					<td colspan="3">
						<h3><?php esc_html_e('Things That Can Be Automatically Checked', 'better-analytics');?></h3>
					</td>
				</tr>

				<tr valign="top">

					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://php.net/manual/en/curl.installation.php', esc_html__('cURL Installed', 'better-analytics'));?></th>

					<?php
						echo (function_exists('curl_multi_init') ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">
						<?php esc_html_e('Not required, but recommended to have the cURL extensions enabled in PHP.', 'better-analytics');?>
					</td>
				</tr>

				<?php
				// not really needed since most people will not use their own project credentials
				/*
				?>
				<tr valign="top">
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#api', esc_html__('API Project Credentials', 'better-analytics'));?></th>
						<?php
							echo ($betterAnalyticsOptions['api']['client_id'] && $betterAnalyticsOptions['api']['client_secret'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
						?>
					<td>
					</td>
				</tr>
				*/
				?>

				<tr valign="top">
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#api', esc_html__('Google Analytics Account Linked', 'better-analytics'));?></th>
					<?php
						echo ($hasTokens ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
				</tr>

				<?php
				if ($hasTokens)
				{
					?>
					<tr>
						<td colspan="3" style="text-align:center">
							<?php esc_html_e('Items highlighted in yellow can be auto-configured via the Google Analytics API if you wish.', 'better-analytics'); ?>
							<br/><br/><a class="button button-primary" href="<?php echo add_query_arg(array('action' => 'config'), esc_url(menu_page_url('better-analytics_test', false))); ?>"><?php esc_html_e('Auto-Configure', 'better-analytics');?></a>
						</td>
					</tr>
				<?php
				}
				?>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#general', esc_html__('Web Property ID Defined', 'better-analytics'));?></th>
					<?php
					echo ($betterAnalyticsOptions['property_id'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description" rowspan="2">
						<?php
						esc_html_e('If you would like the Auto-Configure system to create a new Web Property and profile on your Google Analytics account, don\'t select an existing Web Property.', 'better-analytics');
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#api', esc_html__('Analytics Profile Selected', 'better-analytics'));?></th>
					<?php
						echo ($betterAnalyticsOptions['api']['profile'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>

				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>

					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Site Search Setup', 'better-analytics'));?></th>
					<?php
						echo (@$checks['siteSearchSetup'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">
						<?php
						/* translators: %1$s = <strong>, %2$s = </strong> */
						printf(__('Found in Google Analytics account under %1$sView Settings -> Query Parameter%2$s (should be set to "%1$ss%2$s").', 'better-analytics'), '<strong>', '</strong>');
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Ecommerce Tracking Enabled', 'better-analytics'));?></th>
					<?php
						echo (@$checks['ecommerceTracking'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">

						<?php
						/* translators: %1$s = <strong>, %2$s = </strong> */
						printf(__('Found in Google Analytics account under %1$sEcommerce Settings%2$s.', 'better-analytics'), '<strong>', '</strong>');
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Enhanced Ecommerce Tracking Enabled', 'better-analytics'));?></th>
					<?php
						echo (@$checks['enhancedEcommerceTracking'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">
						<?php
						/* translators: %1$s = <strong>, %2$s = </strong> */
						printf(esc_html__('Found in Google Analytics account under %1$sEcommerce Settings%2$s.', 'better-analytics'), '<strong>', '</strong>');
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Industry Vertical Set', 'better-analytics'));?></th>
					<?php
						echo (!empty($checks['industryVertical']) && $checks['industryVertical'] != 'UNSPECIFIED' ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">
						<?php
						/* translators: %1$s = <strong>, %2$s = </strong>, %3$s = current setting in Google Analytics account */
						printf(esc_html__('Found in Google Analytics account under %1$sProperty Settings%2$s (currently set to %1$s%3$s%2$s).', 'better-analytics'), '<strong>', '</strong>', ($checks['industryVertical'] ? $checks['industryVertical'] : 'UNSPECIFIED'));
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For Category Tracking', 'better-analytics'));?></th>
					<?php
						echo (!empty($checks['dimensions']['Categories']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">
						<?php
						/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
						printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Categories');
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For Author Tracking', 'better-analytics'));?></th>
					<?php
						echo (!empty($checks['dimensions']['Author']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>

					<td class="description">
						<?php
						/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
						printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Author');
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For Tag Tracking', 'better-analytics'));?></th>
					<?php
						echo (!empty($checks['dimensions']['Tags']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">
						<?php
						/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
						printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Tags');
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For Publication Year Tracking', 'better-analytics'));?></th>
					<?php
						echo (!empty($checks['dimensions']['Year']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">

						<?php
						/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
						printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Year');
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For User Role Tracking', 'better-analytics'));?></th>
					<?php
						echo (!empty($checks['dimensions']['Role']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">

						<?php
						/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
						printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'Role');
						?>
					</td>
				</tr>


				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Custom Dimension For User Tracking', 'better-analytics'));?></th>
					<?php
						echo (!empty($checks['dimensions']['User']) ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">

						<?php
						/* translators: %1$s = <strong>, %2$s = </strong> ... %3$s can't be translated - leave */
						printf(esc_html__('Found in Google Analytics account under %1$sCustom Definitions -> Custom Dimensions%2$s (should be named "%1$s%3$s%2$s" and scoped for "%1$sHit%2$s").', 'better-analytics'), '<strong>', '</strong>', 'User');
						?>
					</td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>

					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Category Tracking Dimension Index Set', 'better-analytics'));?></th>
					<?php
						echo (@$betterAnalyticsOptions['dimension']['category'] > 0 && @$checks['dimensions']['Categories']['index'] == $betterAnalyticsOptions['dimension']['category'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td></td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>

					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Author Tracking Dimension Index Set', 'better-analytics'));?></th>
					<?php
						echo (@$betterAnalyticsOptions['dimension']['author'] > 0 && @$checks['dimensions']['Author']['index'] == $betterAnalyticsOptions['dimension']['author'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td></td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Tag Tracking Dimension Index Set', 'better-analytics'));?></th>
					<?php
						echo (@$betterAnalyticsOptions['dimension']['tag'] > 0 && @$checks['dimensions']['Tags']['index'] == $betterAnalyticsOptions['dimension']['tag'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td></td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Publication Year Tracking Dimension Index Set', 'better-analytics'));?></th>
					<?php
						echo (@$betterAnalyticsOptions['dimension']['year'] > 0 && @$checks['dimensions']['Year']['index'] == $betterAnalyticsOptions['dimension']['year'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td></td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('User Role Tracking Dimension Index Set', 'better-analytics'));?></th>
					<?php
						echo (@$betterAnalyticsOptions['dimension']['role'] > 0 && @$checks['dimensions']['Role']['index'] == $betterAnalyticsOptions['dimension']['role'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td></td>
				</tr>

				<tr valign="top"<?php echo $hasTokens ? ' class="can_auto_config"' : ''?>>
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', menu_page_url('better-analytics', false) . '#top#dimensions', esc_html__('Registered User Tracking Dimension Index Set', 'better-analytics'));?></th>
					<?php
						echo (@$betterAnalyticsOptions['dimension']['user'] > 0 && @$checks['dimensions']['User']['index'] == $betterAnalyticsOptions['dimension']['user'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_test&utm_medium=wordpress&utm_campaign=plugin', esc_html__('Better Analytics Pro License', 'better-analytics'));?></th>
					<?php
						echo ($checks['licensed'] ? '<td class="good">✓</td>' : '<td class="bad">✗</td>');
					?>
					<td class="description">
						<?php esc_html_e('A few extra advanced features are available when you license the Better Analytics Pro plugin.  Please don\'t steal, a crazy amount of work went into this.  Some of the extra features:', 'better-analytics'); ?>
						<ul>
							<li>
								<?php
								printf(esc_html__('More metrics available for %1$sHeat Maps%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics_heatmaps', false)) . '" target="_blank">', '</a>');
								?>
							</li>
							<li>
								<?php
								printf(esc_html__('More dimensions available for %1$sCharts%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics_areacharts', false)) . '" target="_blank">', '</a>');
								?>
							</li>
							<li>
								<?php
								printf(esc_html__('More %1$sadvertising platform click tracking%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#advertising') . '" target="_blank">', '</a>');
								?>
							</li>
							<li>
								<?php
								printf(esc_html__('More %1$sissue monitoring options%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#monitor') . '" target="_blank">', '</a>');
								?>
							</li>
							<li>
								<?php
								printf(esc_html__('More %1$sContent Experiment (A/B Testing) options%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics_experiments', false)) . '" target="_blank">', '</a>');
								?>
							</li>
							<li>
								<?php
								printf(esc_html__('%1$seCommerce tracking%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#ecommerce') . '" target="_blank">', '</a>');
								?>
							</li>
							<li>
								<?php
								printf(esc_html__('Ability to do %1$sserver-side tracking of users%2$s', 'better_analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#advanced') . '" target="_blank">', '</a>');
								?>
							</li>
							<li>
								<?php
								esc_html_e('Faster API calls (utilizes a multi-threaded mechanism)', 'better_analytics');
								?>
							</li>
							<li>
								<?php
								printf(esc_html__('%1$sPriority support%2$s', 'better_analytics'), '<a href="' . esc_url(BETTER_ANALYTICS_SUPPORT_URL . '#utm_source=admin_test&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">', '</a>');
								?>
							</li>
							<li>
								<?php
								esc_html_e('A Warm & Fuzzy Feeling knowing you are helping to continue future development', 'better_analytics');
								?>
							</li>


						</ul>
					</td>
				</tr>


				<tr>
					<td colspan="3">
						<h3><?php esc_html_e('Things That Need To Be Checked Manually', 'better-analytics');?></h3></td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('User-ID Tracking', 'better-analytics'));?></th>
					<td class="good">&nbsp;</td>
					<td class="description">
						<?php printf(esc_html__('Found in Google Analytics account under %1$sTracking Info -> User-ID%2$s.', 'better-analytics'), '<strong>', '</strong>');?>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php printf('<a href="%1$s" target="_blank">%2$s</a>:', 'https://www.google.com/analytics/web/?#management/Settings/', esc_html__('Demographic and Interest Reports', 'better-analytics'));?></th>
					<td class="good">&nbsp;</td>
					<td class="description">
						<?php printf(esc_html__('Found in Google Analytics account under %1$sProperty Settings%1$s.', 'better-analytics'), '<strong>', '</strong>');?>
					</td>
				</tr>


			</table>
		</div>

	<?php
	}