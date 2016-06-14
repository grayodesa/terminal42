<?php
wp_enqueue_script('tooltipster_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/js/jquery.tooltipster.min.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('tooltipster_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/css/tooltipster.css', array(), BETTER_ANALYTICS_VERSION);

wp_enqueue_script('better_analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

wp_enqueue_script('chosen_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.jquery.min.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('chosen_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.min.css', array(), BETTER_ANALYTICS_VERSION);

$betterAnalyticsOptions = get_option('better_analytics');

$reportingClass = DigitalPointBetterAnalytics_Helper_Reporting::getInstance();

$nOnceSalt = @$betterAnalyticsOptions['property_id'] . '-' . @$betterAnalyticsOptions['api']['profile'];

$noticeAtTop = '';

$accountId = null;

$experiment = array();
$readOnly = '';

$experimentId = sanitize_text_field(@$_REQUEST['id']);

if ($experimentId && $accountId = DigitalPointBetterAnalytics_Model_Reporting::getSiteAccountId())
{
	$experiment = DigitalPointBetterAnalytics_Model_Experiments::getExperimentByExperimentId($accountId, @$betterAnalyticsOptions['property_id'], @$betterAnalyticsOptions['api']['profile'], $experimentId);
	if ($experiment && (!$experiment['fromBetterAnalytics'] || $experiment['status'] == 'ENDED' || $experiment['status'] == 'RUNNING'))
	{
		$readOnly = ' readonly="readonly" disabled="disabled"';
	}
}

if (@$_REQUEST['action'] == 'create_edit')
{
	if (empty($experiment['status']))
	{
		$experiment['status'] = 'DRAFT';
		$experiment['fromBetterAnalytics'] = true;
	}
	if (empty($experiment['optimizationType']))
	{
		$experiment['optimizationType'] = 'MAXIMUM';
	}

	if (empty($experiment['minimumExperimentLengthInDays']))
	{
		$experiment['minimumExperimentLengthInDays'] = 7;
	}

	if (empty($experiment['trafficCoverage']))
	{
		$experiment['trafficCoverage'] = 1;
	}
	$trafficCoverage = $experiment['trafficCoverage'] * 100;

	if (empty($experiment['winnerConfidenceLevel']))
	{
		$experiment['winnerConfidenceLevel'] = 0.95;
	}
	$winnerConfidenceLevel = $experiment['winnerConfidenceLevel'] * 100;

	if ($_SERVER['REQUEST_METHOD'] != 'POST')
	{

		echo '<div class="wrap experiment_create">
				<h2>' . ($experimentId ? ($readOnly ? esc_html__('View Experiment', 'better-analytics') : esc_html__('Edit Experiment', 'better-analytics')) : esc_html__('Create Experiment', 'better-analytics')) . '</h2>';

		if ($readOnly)
		{
			if ($experiment['status'] == 'ENDED')
			{
				echo '<div class="wrap">
					<div class="error"><p>' . esc_html__('This experiment has ended.  You can view it for informational purposes, but you cannot make changes to it.', 'better-analytics') . '</p></div>
				</div>';
			}
			elseif ($experiment['status'] == 'RUNNING')
			{
				echo '<div class="wrap">
					<div class="error"><p>' . esc_html__('This experiment is currently running.  You can view it for informational purposes, but you cannot make changes to it.', 'better-analytics') . '</p></div>
				</div>';
			}
			else
			{
				echo '<div class="wrap">
					<div class="error"><p>' . esc_html__('This experiment was not created by Better Analytics.  You can view it for informational purposes, but you cannot make changes to it.', 'better-analytics') . '</p></div>
				</div>';
			}
		}


		echo '<form method="post" action="' . esc_url(menu_page_url('better-analytics_experiments', false)) . '">
					<input type="hidden" name="page" value="better-analytics_experiments"/>
					<input type="hidden" name="action" value="create_edit"/>';
			if ($experimentId)
			{
				echo '<input type="hidden" name="id" value="' . esc_attr($experimentId) . '"/>';
			}

			wp_nonce_field('create_edit-experiment' . $nOnceSalt);

		?>

		<table class="form-table">


			<tr valign="top">
				<th scope="row"><?php esc_html_e('Status', 'better-analytics');?></th>
				<td>

					<?php
						$statues = DigitalPointBetterAnalytics_Model_Experiments::getStatuses();
						echo $statues[$experiment['status']];
						echo '<input type="hidden" name="status" value="' . $experiment['status'] . '" />';
					?>

				</td>
			</tr>

			<?php

			if ($experiment['status'] == 'RUNNING')
			{
				if(isset($experiment['variationWinning']))
				{
					echo '<tr valign="top">
						<th scope="row">' . esc_html__('Variation Status', 'better-analytics') . '</th>
						<td>';

					if ($experiment['variationWinning'] > 0)
					{
						printf(esc_html__('Variation %u is winning', 'better-analytics'), $experiment['variationWinning']);
					}
					else
					{
						esc_html_e('Original is winning', 'better-analytics');
					}

					echo '</td>
					</tr>';
				}
			}
			elseif($experiment['status'] == 'ENDED')
			{
				if(isset($experiment['variationWinner']))
				{
					echo '<tr valign="top">
						<th scope="row">' . esc_html__('Variation Winner', 'better-analytics') . '</th>
						<td>';

					if ($experiment['variationWinner'] > 0)
					{
						printf(esc_html__('Variation %u won', 'better-analytics'), $experiment['variationWinner']);
					}
					else
					{
						esc_html_e('Original won', 'better-analytics');
					}

					echo '</td>
					</tr>';
				}
			}

			?>



			<tr valign="top">
				<th scope="row"><?php esc_html_e('Name', 'better-analytics');?></th>
				<td>
					<input type="text" name="name" class="regular-text" id="ba_name" value="<?php echo esc_attr(@$experiment['name']); ?>" required="required"<?php echo $readOnly; ?> />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Description', 'better-analytics');?><span class="optional"><?php esc_html_e('Optional', 'better-analytics');?></span></th>
				<td>
					<textarea name="description" rows="4" cols="50" id="ba_description" class="large-text"<?php echo $readOnly; ?>><?php echo esc_textarea( @$experiment['extraData']['description'] ); ?></textarea>
				</td>
			</tr>


			<tr valign="top">
				<th scope="row"><?php esc_html_e('Objective Metric', 'better-analytics');?></th>
				<td>
					<?php
						echo '<select name="optimization_type" data-placeholder="' . esc_html__('Pick optimization type', 'better-analytics') . '" id="ba_optimization_type" style="width:20%"' . $readOnly . '>';
							echo '<option value="MAXIMUM"' . ('MAXIMUM' == @$experiment['optimizationType'] ? ' selected="selected"' : '') . '>' . htmlentities(esc_html__('Most', 'better-analytics')) . '</option>';
							echo '<option value="MINIMUM"' . ('MINIMUM' == @$experiment['optimizationType'] ? ' selected="selected"' : '') . '>' . htmlentities(esc_html__('Least', 'better-analytics')) . '</option>';
						echo '</select>';

						echo '<select name="objective_metric" data-placeholder="' . esc_html__('Pick objective metric', 'better-analytics') . '" id="ba_objective_metric" style="width:79%"' . $readOnly . '>';

						$objectiveMetrics = DigitalPointBetterAnalytics_Model_Experiments::getObjectiveMetrics();

						$total = 0;
						$check = DigitalPointBetterAnalytics_Helper_Api::check();

						foreach ($objectiveMetrics as $category => $metrics)
						{
							echo '<optgroup label="' . htmlentities($category) . '">';

							foreach ($metrics as $metric => $name)
							{
								$total++;
								echo '<option value="' . $metric . '"' . ($metric == @$experiment['objectiveMetric'] ? ' selected="selected"' : '') . ($total > 3 && !$check ? ' disabled="disabled"' : '') . '>' . htmlentities($name) . ($total > 3 && !$check  ? ' ' . htmlentities(esc_html__('(Pro option)', 'better-analytics')) : '') . '</option>';
							}
							echo '</optgroup>';
						}

						echo '</select>';

					?>
				</td>
			</tr>


			<tr valign="top">
				<th scope="row"><?php esc_html_e('Minimum Days To Run', 'better-analytics');?></th>
				<td>
					<input type="number" name="minimum_days_to_run" id="ba_minimum_days_to_run" min="3" max="90" step="1" value="<?php echo absint(@$experiment['minimumExperimentLengthInDays']); ?>"<?php echo $readOnly; ?> />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Traffic To Experiment On', 'better-analytics');?></th>
				<td>
					<input type="number" name="traffic_coverage" id="ba_traffic_coverage" min="1" max="100" step="1" value="<?php echo $trafficCoverage; ?>"<?php echo $readOnly; ?> /> <?php esc_html_e('%', 'better-analytics');?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Traffic Distribution Type', 'better-analytics');?> <span class="dashicons-before dashicons-info tooltip" title="<?php
					echo htmlspecialchars(sprintf(__('Use this option to assign how traffic is divided between the original and variations for life of the experiment.  By default, Content Experiments follows the behavior of %1$sadjusting traffic dynamically based on variation performance%2$s.', 'better-analytics'),
							'<a href="https://support.google.com/analytics/answer/2844870" target="_blank">',
							'</a>'
						)
					);
					?>"></span></th>
				<td>
					<fieldset id="ba_equal_weighting">
						<label>
							<input name="equal_weighting" type="radio" value="0" <?php checked( 0, empty($experiment['equalWeighting']) ? 0 : 1 ); ?><?php echo $readOnly; ?>>
							<?php esc_html_e('Automatically adjust traffic dynamically ', 'better-analytics');?></label>
						<br />

						<label>
							<input name="equal_weighting" type="radio" value="1" <?php checked( 1, empty($experiment['equalWeighting']) ? 0 : 1 ); ?><?php echo $readOnly; ?>>
							<?php esc_html_e('Distribute traffic evenly across all variations', 'better-analytics');?></label>

					</fieldset>

				</td>
			</tr>


			<tr valign="top">
				<th scope="row"><?php esc_html_e('Confidence Threshold', 'better-analytics');?> <span class="dashicons-before dashicons-info tooltip" title="<?php
					echo htmlspecialchars(__('Set the minimum confidence threshold that must be achieved before Analytics can declare a winner.  The higher the threshold, the more confident you can be in the result.  A higher threshold can result in Analytics taking longer to declare a winner.', 'better-analytics'));
					?>"></span></th>
				<td>
					<input type="number" name="winner_confidence_level" id="ba_winner_confidence_level" min="25" max="99.5" step="0.1" value="<?php echo $winnerConfidenceLevel; ?>"<?php echo $readOnly; ?> /> <?php esc_html_e('%', 'better-analytics');?>
				</td>
			</tr>


			<?php
			if (@$experiment['fromBetterAnalytics'])
			{
				?>


			<tr valign="top">
				<th scope="row"><?php esc_html_e('Type', 'better-analytics');?></th>
				<td>

					<?php

					$allPosts = get_posts(array(
						'posts_per_page' => 100000
					));

					$allPages = get_pages();

					echo '<select name="type" data-placeholder="' . esc_html__('Pick type', 'better-analytics') . '" id="ba_type"' . $readOnly . ' >';

					$types = array(
						'POST_TITLE' => array(
							'title' => esc_html__('Post Title', 'better-analytics'),
							'disabled' => (count($allPosts) == 0 || !current_user_can('edit_posts'))
						),
						'PAGE_TITLE' => array(
							'title' => esc_html__('Page Title', 'better-analytics'),
							'disabled' => (count($allPages) == 0 || !current_user_can('edit_pages'))
						),
						'CSS' => array(
							'title' => esc_html__('CSS', 'better-analytics'),
							'disabled' => !current_user_can('edit_themes')
						),
						'THEME' => array(
							'title' => esc_html__('Theme', 'better-analytics'),
							'disabled' => !current_user_can('switch_themes')
						),
					);

					$types = apply_filters('better_analytics_experiment_types', $types);

					foreach ($types as $key => $type)
					{
						echo '<option value="' . $key . '"' . ($key == @$experiment['extraData']['type'] ? ' selected="selected"' : '') . ($type['disabled'] ? ' disabled="disabled"' : '') . '>' . htmlentities($type['title']) . '</option>';
					}
					echo '</select>';
					?>

				</td>
			</tr>

			<tr valign="top" class="dynamic_options POST_TITLE">
				<th scope="row"><?php esc_html_e('Post', 'better-analytics');?></th>
				<td>

					<?php

						echo '<select name="extra_data[post_title][post_id]" data-placeholder="' . esc_html__('Pick post', 'better-analytics') . '" id="ba_post_id"' . ($readOnly ? $readOnly : ' class="chosen-select"') . ' >';

						foreach ($allPosts as $post)
						{
							echo '<option value="' . absint($post->ID) . '"' . ($post->ID == @$experiment['extraData']['post_title']['post_id'] ? ' selected="selected"' : '') . '>' . htmlentities($post->post_title) . '</option>';
						}

						unset($allPosts);

						echo '</select>';
					?>
				</td>
			</tr>

			<tr valign="top" class="dynamic_options POST_TITLE">
				<th scope="row"><?php esc_html_e('Title Variations', 'better-analytics');?></th>
				<td>
					<ol>
						<?php
							if (is_array(@$experiment['extraData']['post_title']['titles']))
							{
								foreach ($experiment['extraData']['post_title']['titles'] as $key => $variation)
								{
									echo '<li><input type="text" name="extra_data[post_title][titles][]" class="regular-text" value="' . esc_attr($variation) .'"  placeholder="' . esc_attr( esc_html__('Enter a post title variation here', 'better-analytics') ) . '"' . $readOnly . ' />';

									if (!$readOnly)
									{
										echo '<a class="delete"><span class="dashicons dashicons-dismiss"></span></a>';
									}

									echo '</li>';
								}
							}
							else
							{
								echo '<li><input type="text" name="extra_data[post_title][titles][]" class="regular-text" value="" placeholder="' . esc_attr( esc_html__('Enter a post title variation here', 'better-analytics') ) . '" />';

								if (!$readOnly)
								{
									echo '<a class="delete"><span class="dashicons dashicons-dismiss"></span></a>';
								}

								echo '</li>';
							}
						?>
					</ol>
					<?php
						if (!$readOnly)
						{
							echo '<a id="new_post_title_variation" class="button variation_button">' . esc_html__('Add another title variation', 'better-analytics') . '</a>';
						}
					?>

				</td>
			</tr>



			<tr valign="top" class="dynamic_options PAGE_TITLE">
				<th scope="row"><?php esc_html_e('Page', 'better-analytics');?></th>
				<td>

					<?php

					echo '<select name="extra_data[page_title][post_id]" data-placeholder="' . esc_html__('Pick page', 'better-analytics') . '" id="ba_page_id"' . ($readOnly ? $readOnly : ' class="chosen-select"') . ' >';

					foreach ($allPages as $page)
					{
						echo '<option value="' . absint($page->ID) . '"' . ($page->ID == @$experiment['extraData']['page_title']['post_id'] ? ' selected="selected"' : '') . '>' . htmlentities($page->post_title) . '</option>';
					}

					unset($allPages);

					echo '</select>';
					?>
				</td>
			</tr>

			<tr valign="top" class="dynamic_options PAGE_TITLE">
				<th scope="row"><?php esc_html_e('Title Variations', 'better-analytics');?></th>
				<td>
					<ol>
						<?php
						if (is_array(@$experiment['extraData']['page_title']['titles']))
						{
							foreach ($experiment['extraData']['page_title']['titles'] as $key => $variation)
							{
								echo '<li><input type="text" name="extra_data[page_title][titles][]" class="regular-text" value="' . esc_attr($variation) .'"  placeholder="' . esc_attr( esc_html__('Enter a page title variation here', 'better-analytics') ) . '"' . $readOnly . ' />';

								if (!$readOnly)
								{
									echo '<a class="delete"><span class="dashicons dashicons-dismiss"></span></a>';
								}

								echo '</li>';
							}
						}
						else
						{
							echo '<li><input type="text" name="extra_data[page_title][titles][]" class="regular-text" value="" placeholder="' . esc_attr( esc_html__('Enter a page title variation here', 'better-analytics') ) . '" />';

							if (!$readOnly)
							{
								echo '<a class="delete"><span class="dashicons dashicons-dismiss"></span></a>';
							}

							echo '</li>';
						}
						?>
					</ol>

					<?php
						if (!$readOnly)
						{
							echo '<a id="new_page_title_variation" class="button variation_button">' . esc_html__('Add another title variation', 'better-analytics') . '</a>';
						}
					?>

				</td>
			</tr>


			<tr valign="top" class="dynamic_options CSS">
				<th scope="row"><?php esc_html_e('Custom CSS', 'better-analytics');?></th>
				<td>
					<ol>
						<?php
						if (is_array(@$experiment['extraData']['css']['code']))
						{
							foreach ($experiment['extraData']['css']['code'] as $key => $variation)
							{
								echo '<li>
											<textarea name="extra_data[css][code][]" rows="8" cols="50" class="large-text code" placeholder=".some_class {
	color: #AABBCC;
}

.something_else {
	padding: 5px 20px;
	color: green;
}"' . $readOnly . ' >' . esc_textarea(@$variation) . '</textarea>
											' . (!$readOnly ? '<a class="delete"><span class="dashicons dashicons-dismiss"></span></a>' : '') . '
										</li>';
							}
						}
						else
						{
							echo '<li>
										<textarea name="extra_data[css][code][]" rows="8" cols="50" class="large-text code" placeholder=".some_class {
	color: #AABBCC;
}

.something_else {
	padding: 5px 20px;
	color: green;
}"' . $readOnly . ' ></textarea>
											' . (!$readOnly ? '<a class="delete"><span class="dashicons dashicons-dismiss"></span></a>' : '') . '
										</li>';
						}
						?>
					</ol>

					<?php
						if (!$readOnly)
						{
							echo '<a id="new_css_variation" class="button variation_button">' . esc_html__('Add another CSS variation', 'better-analytics') . '</a>';
						}
					?>
				</td>
			</tr>


			<tr valign="top" class="dynamic_options THEME">
				<th scope="row"><?php esc_html_e('Theme Variations', 'better-analytics');?></th>
				<td>
					<ol>

						<?php
						$allThemes = wp_get_themes();

						$durations = explode(":", gmdate('j:H:i:s', @$goal['visitTimeOnSiteDetails']['comparisonValue']));

						$currentTheme = wp_get_theme();
						$currentThemeTextDomain = $currentTheme->get('TextDomain');

						if (is_array(@$experiment['extraData']['theme']['themes']))
						{
							foreach ($experiment['extraData']['theme']['themes'] as $key => $variation)
							{
								echo '<li>';

								echo '<select name="extra_data[theme][themes][]" data-placeholder="' . esc_html__('Pick theme', 'better-analytics') . '">';

								echo '<option value="" disabled="disabled" selected="selected" style="display:none;">' . esc_html__('Pick theme', 'better-analytics') . '</option>';

								foreach ($allThemes as $textDomain => $theme)
								{
									echo '<option value="' . esc_attr($textDomain) . '"' . ($currentThemeTextDomain == $textDomain ? ' disabled="disabled"' : '') . ($textDomain == $variation ? ' selected="selected"' : '') . '>' . htmlentities($theme->get('Name')) . '</option>';
								}
								echo '</select>';

								echo '<a class="delete"><span class="dashicons dashicons-dismiss"></span></a>';

								echo '</li>';
							}
						}
						else
						{
							echo '<li>';

							echo '<select name="extra_data[theme][themes][]" data-placeholder="' . esc_html__('Pick theme', 'better-analytics') . '">';

							echo '<option value="" disabled="disabled" selected="selected" style="display:none;">' . esc_html__('Pick theme', 'better-analytics') . '</option>';

							foreach ($allThemes as $textDomain => $theme)
							{
								echo '<option value="' . esc_attr($textDomain) . '"' . ($currentThemeTextDomain == $textDomain ? ' disabled="disabled"' : '') . '>' . htmlentities($theme->get('Name')) . '</option>';
							}
							echo '</select>';

							echo '<a class="delete"><span class="dashicons dashicons-dismiss"></span></a>';

							echo '</li>';
						}

						unset($allThemes);

						?>
					</ol>
					<a id="new_theme_variation" class="button variation_button"><?php esc_html_e('Add another theme variation', 'better-analytics') ?></a>

				</td>
			</tr>

			<?php
				do_action('better_analytics_experiment_types_html', $experiment);
			}
				?>
		</table>


		<?php
		if (!$readOnly)
		{
			submit_button();
		}
		echo '</form>
		</div>';
	}
	else
	{
		check_admin_referer('create_edit-experiment' . $nOnceSalt);

		$errorMessage = array();

		$experimentObject = array(
			'status' => sanitize_text_field(@$_POST['status']),
			'servingFramework' => 'API',
			'editableInGaUi' => false,
		);

		if (@$experiment['status'] != 'ENDED')
		{
			$experimentObject['minimumExperimentLengthInDays'] = absint(@$_POST['minimum_days_to_run']);
			$experimentObject['equalWeighting'] = (@$_POST['equal_weighting'] ? true : false);
			$experimentObject['name'] = sanitize_text_field(@$_POST['name']);
			$experimentObject['trafficCoverage'] = absint(@$_POST['traffic_coverage']) / 100;
			$experimentObject['winnerConfidenceLevel'] = absint(@$_POST['winner_confidence_level']) / 100;
		}

		if (@$experiment['status'] != 'RUNNING' && @$experiment['status'] != 'ENDED')
		{
			$experimentObject['optimizationType'] = sanitize_text_field(@$_POST['optimization_type']);
			$experimentObject['objectiveMetric'] = sanitize_text_field(@$_POST['objective_metric']);
		}

		$extraData = array(
			'description' => sanitize_text_field(@$_POST['description']),
			'type' => sanitize_text_field(@$_POST['type'])
		);


		$extraDataInput = @$_POST['extra_data'];

		$variations = array(array(
			'name' => 'original',
			'status' => 'ACTIVE'
		));

		if (is_array($extraDataInput))
		{
			switch (@$_POST['type'])
			{
				case 'POST_TITLE':

					$extraData['post_title']['post_id'] = absint(@$extraDataInput['post_title']['post_id']);

					if (is_array(@$extraDataInput['post_title']['titles']))
					{
						foreach($extraDataInput['post_title']['titles'] as $title)
						{
							if ($title = sanitize_text_field($title))
							{
								$extraData['post_title']['titles'][] = $title;
								$variations[] = array(
									'name' => $title,
									'status' => 'ACTIVE',
							//		'url' => 'none'
								);
							}
						}
					}

					break;

				case 'PAGE_TITLE':

					$extraData['page_title']['post_id'] = absint(@$extraDataInput['page_title']['post_id']);

					if (is_array(@$extraDataInput['page_title']['titles']))
					{
						foreach($extraDataInput['page_title']['titles'] as $title)
						{
							if ($title = sanitize_text_field($title))
							{
								$extraData['page_title']['titles'][] = $title;
								$variations[] = array(
									'name' => $title,
									'status' => 'ACTIVE',
							//		'url' => 'none'
								);
							}
						}
					}
					break;

				case 'CSS':
					if (is_array(@$extraDataInput['css']['code']))
					{
						foreach($extraDataInput['css']['code'] as $code)
						{
							if ($code = wp_strip_all_tags($code))
							{
								$extraData['css']['code'][] = $code;
								$variations[] = array(
									'name' => 'CSS variation',
									'status' => 'ACTIVE',
								//	'url' => 'none'
								);
							}
						}
					}
					break;

				case 'THEME':
					if (is_array(@$extraDataInput['theme']['themes']))
					{
						foreach($extraDataInput['theme']['themes'] as $theme)
						{
							if ($theme = sanitize_text_field($theme))
							{
								$extraData['theme']['themes'][] = $theme;
								$variations[] = array(
									'name' => $theme,
									'status' => 'ACTIVE',
									//	'url' => 'none'
								);
							}
						}
					}
					break;

				default:

					list($extraData, $variations) = apply_filters('better_analytics_experiment_save', $extraData, $variations, $extraDataInput);
			}

		}

		if (count($variations) > 0)
		{
			$experimentObject['variations'] = $variations;
		}

		$experimentObject['description'] = json_encode($extraData);

		if (!$experimentObject['name'] && !$experiment['name'])
		{
			$errorMessage[] = esc_html__('Experiment name is required.', 'better-analytics');
		}

		if (!$errorMessage && $profile = $reportingClass->getProfileByProfileId(@$betterAnalyticsOptions['api']['profile']))
		{
			if ($experimentId)
			{
				$experiment = $reportingClass->patchExperiment($profile['accountId'], $profile['webPropertyId'], $profile['id'], $experimentId, $experimentObject);
			}
			else
			{
				$experiment = $reportingClass->insertExperiment($profile['accountId'], $profile['webPropertyId'], $profile['id'], $experimentObject);
			}

			$reportingClass->deleteExperimentCache($profile['accountId'], $profile['webPropertyId'], $profile['id']);
			if (!empty($experiment['id']))
			{
				$reportingClass->deleteExperimentCache($profile['accountId'], $profile['webPropertyId'], $profile['id'], $experiment['id']);
			}

			if ($experimentId)
			{
				/* translators: %1$s = <strong>, %2$s = </strong> */
				$noticeAtTop = '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Experiment %1$supdated%2$s.'), '<strong>', '</strong>') . '</p></div>';
			}
			else
			{
				/* translators: %1$s = <strong>, %2$s = </strong> */
				$noticeAtTop = '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Experiment %1$screated%2$s.'), '<strong>', '</strong>') . '</p></div>';
			}
		}
	}
}
elseif ($experiment && (@$_REQUEST['action'] == 'start' ||  @$_REQUEST['action'] == 'end' ||  @$_REQUEST['action'] == 'delete'))
{
	$reportingClass->deleteExperimentCache($experiment['accountId'], $experiment['webPropertyId'], $experiment['profileId']);
	$reportingClass->deleteExperimentCache($experiment['accountId'], $experiment['webPropertyId'], $experiment['profileId'], $experimentId);

	if (@$_REQUEST['action'] == 'start')
	{
		$experiment = $reportingClass->patchExperiment($experiment['accountId'], $experiment['webPropertyId'], $experiment['profileId'], $experimentId, array('status' => 'RUNNING'));

		/* translators: %1$s = <strong>, %2$s = </strong> */
		$noticeAtTop = '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Experiment %1$sstarted%2$s.'), '<strong>', '</strong>') . '</p></div>';
	}
	elseif (@$_REQUEST['action'] == 'end')
	{
		$experiment = $reportingClass->patchExperiment($experiment['accountId'], $experiment['webPropertyId'], $experiment['profileId'], $experimentId, array('status' => 'ENDED'));

		/* translators: %1$s = <strong>, %2$s = </strong> */
		$noticeAtTop = '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Experiment %1$sended%2$s.'), '<strong>', '</strong>') . '</p></div>';
	}
	elseif (@$_REQUEST['action'] == 'delete')
	{
		$reportingClass->deleteExperiment($experiment['accountId'], $experiment['webPropertyId'], $experiment['profileId'], $experimentId);

		/* translators: %1$s = <strong>, %2$s = </strong> */
		$noticeAtTop = '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Experiment %1$sdeleted%2$s.'), '<strong>', '</strong>') . '</p></div>';
	}
}

if (@$_REQUEST['action'] != 'create_edit' || @$_SERVER['REQUEST_METHOD'] == 'POST')
{
	if (!empty($errorMessage))
	{
		echo '<div class="wrap">';

		foreach($errorMessage as $error)
		{
			echo '<div class="error"><p>' . $error . '</p></div>';
		}
		echo '</div>';
	}
	else
	{
		echo '<div class="wrap experiments">
				<h2>' . esc_html__('Experiments', 'better-analytics') .
			' <a href="' . add_query_arg(array('action' => 'create_edit'), esc_url(menu_page_url('better-analytics_experiments', false))) . '" class="add-new-h2">' . esc_html__('Add New', 'better-analytics') . '</a>' .
			'</h2>

		<form method="post" action="' . esc_url(menu_page_url('better-analytics_experiments', false)) . '">
			<input type="hidden" name="page" value="better-analytics_experiments"/>';

		echo $noticeAtTop;

		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'activate')
		{
			/* translators: %1$s = <strong>, %2$s = </strong> */
			echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Experiment %1$sactivated%2$s.'), '<strong>', '</strong>') . '</p></div>';
		}
		elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'activate-selected')
		{
			/* translators: %1$s = <strong>, %2$s = </strong> */
			echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Selected experiments %1$sactivated%2$s.'), '<strong>', '</strong>') . '</p></div>';
		}
		elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deactivate')
		{
			/* translators: %1$s = <strong>, %2$s = </strong> */
			echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Experiment %1$sdeactivated%2$s.'), '<strong>', '</strong>') . '</p></div>';
		}
		elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deactivate-selected')
		{
			/* translators: %1$s = <strong>, %2$s = </strong> */
			echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Selected experiments %1$sdeactivated%2$s.'), '<strong>', '</strong>') . '</p></div>';
		}

		global $totals;

		$experiments = $totals = array();

		if ($profile = $reportingClass->getProfileByProfileId($betterAnalyticsOptions['api']['profile']))
		{
			$experiments = DigitalPointBetterAnalytics_Model_Experiments::getAllExperiments($profile['accountId'], $profile['webPropertyId'], $profile['id']);

			$totals = DigitalPointBetterAnalytics_Model_Reporting::getExperimentTotals($experiments);
		}
		$goalTable = new DigitalPointBetterAnalytics_Formatting_ExperimentTable(array('plural' => 'experiments', 'experiments' => $experiments));

		$goalTable->prepare_items();
		$goalTable->views();
		$goalTable->display();

		echo '</form></div>';
	}
}