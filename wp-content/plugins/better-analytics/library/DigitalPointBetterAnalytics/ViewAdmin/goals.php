<?php

wp_enqueue_script('better_analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

$betterAnalyticsOptions = get_option('better_analytics');

$nOnceSalt = @$betterAnalyticsOptions['property_id'] . '-' . @$betterAnalyticsOptions['api']['profile'];

$noticeAtTop = '';

if (@$_REQUEST['action'] == 'create_edit')
{
	$goalId = absint(@$_REQUEST['id']);

	$goalsAll = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getGoals();

	if ($goalId)
	{
		$goal = DigitalPointBetterAnalytics_Model_Reporting::getGoalByGoalId($goalsAll, @$betterAnalyticsOptions['property_id'], @$betterAnalyticsOptions['api']['profile'], $goalId);
	}

	if ($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		echo '<div class="wrap goal_create">
				<h2>' . ($goalId ? esc_html__('Edit Goal', 'better-analytics') : esc_html__('Create Goal', 'better-analytics')) . '</h2>

				<form method="post" action="' . esc_url(menu_page_url('better-analytics_goals', false)) . '">
					<input type="hidden" name="page" value="better-analytics_goals"/>
					<input type="hidden" name="action" value="create_edit"/>';
					wp_nonce_field('create_edit-goal' . $nOnceSalt);

		?>

		<table class="form-table">

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Slot', 'better-analytics');?></th>
				<td>
					<?php

					if ($goalId)
					{
						echo '<input type="hidden" name="id" value="' . $goalId . '"/>';
						$goalSet = DigitalPointBetterAnalytics_Model_Goals::getGoalSetByGoal($goalId);
						printf(esc_html__('Goal ID %1$u / Goal Set %2$u', 'better-analytics'), $goal['id'], $goalSet);
					}
					else
					{
						echo '<select name="slot" data-placeholder="' . esc_html__('Pick slot', 'better-analytics') . '" id="ba_slot" class="chosen-select">';

						$goalsAll = DigitalPointBetterAnalytics_Model_Reporting::filterGoalsByProfile($goalsAll, @$betterAnalyticsOptions['property_id'], @$betterAnalyticsOptions['api']['profile'], $totals);

						foreach (range(1, 20) as $number)
						{
							$goalSet = DigitalPointBetterAnalytics_Model_Goals::getGoalSetByGoal($number);
							echo '<option value="' . $number . '"' . (!empty($goalsAll[$number]) ? ' disabled="disabled"' : '') . '>' . htmlentities(sprintf(esc_html__('Goal ID %1$u / Goal Set %2$u', 'better-analytics'), $number, $goalSet)) . '</option>';
						}
						echo '</select>';
					}
					?>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Name', 'better-analytics');?></th>
				<td>
					<input type="text" name="name" class="regular-text" id="ba_name" value="<?php echo esc_attr( @$goal['name'] ); ?>" required />
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Type', 'better-analytics');?></th>
				<td>

					<?php
						echo '<select name="type" data-placeholder="' . esc_html__('Pick type', 'better-analytics') . '" id="ba_type" class="chosen-select">';

						$types = DigitalPointBetterAnalytics_Model_Goals::getTypes();

						foreach ($types as $key => $type)
						{
							echo '<option value="' . $key . '"' . ($key == @$goal['type'] ? ' selected="selected"' : '') . '>' . htmlentities($type) . '</option>';
						}
						echo '</select>';
					?>

				</td>
			</tr>

			<tr valign="top">
				<th scope="row"></th>
				<td>
					<fieldset>
						<label for="ba_active">
							<input name="active" type="checkbox" id="ba_active" value="1" <?php checked('1', (@$goal ? @$goal['active'] : '1')); ?>>
							<?php esc_html_e('Active', 'better-analytics');?></label>
					</fieldset>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php esc_html_e('Value', 'better-analytics');?><span class="optional"><?php esc_html_e('Optional', 'better-analytics');?></span></th>
				<td>
					<input type="number" name="value" id="ba_value" min="0" step="0.01" value="<?php echo number_format(@$goal['value'], 2); ?>" />
				</td>
			</tr>

			<tr valign="top" class="dynamic_options URL_DESTINATION">
				<th scope="row"><?php esc_html_e('Destination', 'better-analytics');?></th>
				<td>

					<?php
						echo '<select name="destination_match_type" data-placeholder="' . esc_html__('Pick type', 'better-analytics') . '" id="ba_destination_match_type" class="chosen-select">';

						$types = DigitalPointBetterAnalytics_Model_Goals::getMatchTypes();

						foreach ($types as $key => $type)
						{
							echo '<option value="' . $key . '"' . ($key == @$goal['urlDestinationDetails']['matchType'] ? ' selected="selected"' : '') . '>' . htmlentities($type) . '</option>';
						}
						echo '</select>';
					?>

					<input type="text" name="destination_url" class="regular-text" id="ba_destination_url" placeholder="<?php echo esc_attr(esc_html__('URL', 'better-analytics'));?>" value="<?php echo esc_attr( @$goal['urlDestinationDetails']['url'] ); ?>" />

					<label for="ba_case_sensitive">
						<input name="case_sensitive" type="checkbox" id="ba_case_sensitive" value="1" <?php checked('1', (@$goal['urlDestinationDetails']['caseSensitive'] ? '1' : '')); ?>>
						<?php esc_html_e('Case sensitive', 'better-analytics');?></label>
				</td>
			</tr>

			<tr valign="top" class="dynamic_options URL_DESTINATION funnel">
				<th scope="row"><?php esc_html_e('Funnel', 'better-analytics'); ?><span
						class="optional"><?php esc_html_e('Optional', 'better-analytics'); ?></span></th>
				<td>
					<div>
						<ol>
							<?php

							// show first line when creating new
							if (empty($goal['urlDestinationDetails']['steps']))
							{
								$goal['urlDestinationDetails']['steps'] = array(true);
							}

							if (!empty($goal['urlDestinationDetails']['steps']) && is_array($goal['urlDestinationDetails']['steps']))
							{
								$i = 0;
								foreach ($goal['urlDestinationDetails']['steps'] as $step)
								{
									echo '<li class="funnel_step">
											<span><input type="text" placeholder="' . esc_attr(esc_html__('Name', 'better-analytics')) .'" name="funnel[name][]" value="' . esc_attr(@$step['name']) .'" /></span>
											<span><input type="text" placeholder="' . esc_attr(esc_html__('URL', 'better-analytics')) .'" class="regular-text" name="funnel[url][]" value="' . esc_attr(@$step['url']) .'" /></span>';

									echo '<a class="delete"><span class="dashicons dashicons-dismiss"></span></a>';

									if (!$i)
									{
										echo '<span><label for="ba_first_step_required">
												<input name="first_step_required" type="checkbox" id="ba_first_step_required" value="1"' . checked('1', (@$goal['urlDestinationDetails']['firstStepRequired'] ? '1' : ''), false) .'>' .
											esc_html__('Required', 'better-analytics') . '</label>';
									}

										echo '</li>';
									$i++;
								}
							}
							?>
						</ol>
					</div>
					<a id="new_step" class="button"><?php esc_html_e('Add another step', 'better-analytics') ?></a>
				</td>
			</tr>

			<tr valign="top" class="dynamic_options VISIT_TIME_ON_SITE">
				<th scope="row"><?php esc_html_e('Session Duration', 'better-analytics');?></th>
				<td>
					<?php
						$durations = explode(":", gmdate('j:H:i:s', @$goal['visitTimeOnSiteDetails']['comparisonValue']));
					?>
					<div>
						<div>
							<span></span>
							<span><?php esc_html_e('Hours', 'better-analytics') ?></span>
							<span><?php esc_html_e('Minutes', 'better-analytics') ?></span>
							<span><?php esc_html_e('Seconds', 'better-analytics') ?></span>
						</div>
						<div>
							<span style="padding-right:5px;"><?php esc_html_e('Greater than', 'better-analytics') ?></span>
							<span>
								<input type="number" name="hours" min="0" id="ba_hours" value="<?php echo esc_attr((absint($durations[0] - 1) * 24) + absint($durations[1])); ?>" />
							</span>
							<span>
								<input type="number" name="minutes" min="0" id="ba_minutes" value="<?php echo esc_attr(absint($durations[2])); ?>" />
							</span>
							<span>
								<input type="number" name="seconds" min="0" id="ba_seconds" value="<?php echo esc_attr(absint($durations[3])); ?>" />
							</span>
						</div>
					</div>
				</td>
			</tr>

			<tr valign="top" class="dynamic_options VISIT_NUM_PAGES">
				<th scope="row"><?php esc_html_e('Pages Per Session', 'better-analytics');?></th>
				<td>
					<div>
						<div>
							<span style="padding-right:5px;"><?php esc_html_e('Greater than', 'better-analytics') ?></span>
							<span>
								<input type="number" name="pages" min="0" id="ba_pages" value="<?php echo esc_attr(absint(@$goal['visitNumPagesDetails']['comparisonValue'])); ?>" />
							</span>
						</div>
					</div>
				</td>
			</tr>

			<tr valign="top" class="dynamic_options EVENT">
				<th scope="row"><?php esc_html_e('Conditions', 'better-analytics');?></th>
				<td>
					<div>
						<?php

						$matchTypes = DigitalPointBetterAnalytics_Model_Goals::getMatchTypes(true);
						$comparisonTypes = DigitalPointBetterAnalytics_Model_Goals::getComparisonTypes();
						$eventConditionTypes = DigitalPointBetterAnalytics_Model_Goals::getEventConditionTypes();

						foreach ($eventConditionTypes as $conditionCode => $conditionLabel)
						{
							if ($conditionCode == 'VALUE')
							{
								$condition = array(
									'type' => $conditionCode,
									'comparisonType' => true
								);
							}
							else
							{
								$condition = array(
									'type' => $conditionCode,
									'matchType' => true
								);
							}

							if (!empty($goal['eventDetails']['eventConditions']) && count($goal['eventDetails']['eventConditions']) > 0)
							{
								foreach ($goal['eventDetails']['eventConditions'] as $existingCondition)
								{
									if (@$existingCondition['type'] == $conditionCode)
									{
										$condition = $existingCondition;
										break;
									}
								}
							}

							echo '<div><span>' . @$eventConditionTypes[$condition['type']] . ':</span><span>';

							echo '<select name="event_' . strtolower($condition['type']) . '" id="ba_event_' . strtolower($condition['type']) . '" class="chosen-select">';

							foreach (!empty($condition['matchType']) ? $matchTypes : $comparisonTypes as $key => $type)
							{
								$selected = '';
								if (!empty($condition['matchType']))
								{
									if ($key === $condition['matchType'])
									{
										$selected = ' selected="selected"';
									}
								}
								elseif (!empty($condition['comparisonType']))
								{
									if ($key === $condition['comparisonType'])
									{
										$selected = ' selected="selected"';
									}
								}

								echo '<option value="' . $key . '"' . $selected . '>' . htmlentities($type) . '</option>';
							}
							echo '</select></span><span>';

							echo ' <input type="text" name="event_' . strtolower($condition['type']) . '_value" id="ba_event_' . strtolower($condition['type']) . '_value" value="' . esc_attr(@$condition['expression'] ? @$condition['expression'] : @$condition['comparisonValue']) . '" /></span></div>';

						}

						?>

					</div>
					<fieldset>
						<label for="ba_use_event_value">
							<input name="use_event_value" type="checkbox" id="ba_use_event_value" value="1" <?php checked('1', (@$goal['eventDetails']['useEventValue'] ? '1' : '')); ?>>
							<?php esc_html_e('Use the Event value as the Goal Value for the conversion', 'better-analytics');?></label>
					</fieldset>

				</td>
			</tr>
		</table>


		<?php
			submit_button();
			echo '</form>
		</div>';
	}
	else
	{
		check_admin_referer('create_edit-goal' . $nOnceSalt);

		$errorMessage = array();

		$goalObject = array(
			'name' => sanitize_text_field(@$_POST['name']),
			'value' => (@$_POST['value'] + 0),
			'type' => sanitize_text_field(@$_POST['type']),
			'active' => (@$_POST['active'] ? true : false),
			'urlDestinationDetails' => array(),
			'visitTimeOnSiteDetails' => array(),
			'visitNumPagesDetails' => array(),
			'eventDetails' => array(),
		);

		if (!$goalObject['name'])
		{
			$errorMessage[] = esc_html__('Goal name is required.', 'better-analytics');
		}

		// calling out slot on insert
		if ($slot = absint(@$_POST['slot']))
		{
			$goalObject['id'] = $slot;
		}

		if (@$_POST['type'] == 'URL_DESTINATION')
		{
			$goalObject['urlDestinationDetails'] = array(
				'url' => esc_url(@$_POST['destination_url']),
				'caseSensitive' => (@$_POST['case_sensitive'] ? true : false),
				'matchType' => @$_POST['destination_match_type'],
				'firstStepRequired' => (@$_POST['first_step_required'] ? true : false),
			);
			if (!empty($_POST['funnel']) && is_array($_POST['funnel']))
			{
				$goalObject['urlDestinationDetails']['steps'] = array();

				if (!empty($_POST['funnel']['url'][0]))
				{
					foreach ($_POST['funnel']['name'] as $step => $name)
					{
						$goalObject['urlDestinationDetails']['steps'][] = array(
							'number' => ($step + 1),
							'name' => sanitize_text_field($name),
							'url' => esc_url($_POST['funnel']['url'][$step])
						);
					}
				}
			}

			if (!$goalObject['urlDestinationDetails']['url'])
			{
				$errorMessage[] = esc_html__('Destination URL is required.', 'better-analytics');
			}

		}
		elseif (@$_POST['type'] == 'VISIT_TIME_ON_SITE')
		{
			$goalObject['visitTimeOnSiteDetails'] = array(
				'comparisonType' => 'GREATER_THAN',
				'comparisonValue' => (absint(@$_POST['hours']) * 3600) + (absint(@$_POST['minutes']) * 60) + absint(@$_POST['seconds']),
			);
		}
		elseif (@$_POST['type'] == 'VISIT_NUM_PAGES')
		{
			$goalObject['visitNumPagesDetails'] = array(
				'comparisonType' => 'GREATER_THAN',
				'comparisonValue' => absint(@$_POST['pages']),
			);
		}
		elseif (@$_POST['type'] == 'EVENT')
		{
			$goalObject['eventDetails'] = array(
				'useEventValue' => (@$_POST['use_event_value'] ? true : false),
				'eventConditions' => array()
			);

			if (!empty($_POST['event_category_value']))
			{
				$goalObject['eventDetails']['eventConditions'][] = array(
					'type' => 'CATEGORY',
					'matchType' => $_POST['event_category'],
					'expression' => $_POST['event_category_value']
				);
			}

			if (!empty($_POST['event_action_value']))
			{
				$goalObject['eventDetails']['eventConditions'][] = array(
					'type' => 'ACTION',
					'matchType' => $_POST['event_action'],
					'expression' => $_POST['event_action_value']
				);
			}

			if (!empty($_POST['event_label_value']))
			{
				$goalObject['eventDetails']['eventConditions'][] = array(
					'type' => 'LABEL',
					'matchType' => $_POST['event_label'],
					'expression' => $_POST['event_label_value']
				);
			}

			if (!empty($_POST['event_value_value']))
			{
				$goalObject['eventDetails']['eventConditions'][] = array(
					'type' => 'VALUE',
					'comparisonType' => $_POST['event_value'],
					'comparisonValue' => $_POST['event_value_value']
				);
			}

			if (count($goalObject['eventDetails']['eventConditions']) == 0)
			{
				$errorMessage[] = esc_html__('You must specify a Category, Action or Label.', 'better-analytics');
			}


		}

		$reportingClass = DigitalPointBetterAnalytics_Helper_Reporting::getInstance();

		if (!$errorMessage && $profile = $reportingClass->getProfileByProfileId(@$betterAnalyticsOptions['api']['profile']))
		{
			if ($goalId)
			{
				$goal = $reportingClass->patchGoal($profile['accountId'], $profile['webPropertyId'], $profile['id'], $goalId, $goalObject);
			}
			else
			{
				$goal = $reportingClass->insertGoal($profile['accountId'], $profile['webPropertyId'], $profile['id'], $goalObject);
			}

			$reportingClass->deleteGoalCache();

			$goals = $reportingClass->getGoals();
			$goals = DigitalPointBetterAnalytics_Model_Reporting::filterGoalsByProfile($goals, @$betterAnalyticsOptions['property_id'], @$betterAnalyticsOptions['api']['profile'], $totals);

			if ($goalId)
			{
				/* translators: %1$s = <strong>, %2$s = </strong> */
				$noticeAtTop = '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Goal %1$supdated%2$s.'), '<strong>', '</strong>') . '</p></div>';
			}
			else
			{
				/* translators: %1$s = <strong>, %2$s = </strong> */
				$noticeAtTop = '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Goal %1$screated%2$s.'), '<strong>', '</strong>') . '</p></div>';
			}
		}
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
		echo '<div class="wrap goals">
				<h2>' . esc_html__('Goals', 'better-analytics') .
			' <a href="' . add_query_arg(array('action' => 'create_edit'), esc_url(menu_page_url('better-analytics_goals', false))) . '" class="add-new-h2">' . esc_html__('Add New', 'better-analytics') . '</a>' .
			'</h2>

		<form method="post" action="' . esc_url(menu_page_url('better-analytics_goals', false)) . '">
			<input type="hidden" name="page" value="better-analytics_goals"/>';

		echo $noticeAtTop;

		if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'activate')
		{
			/* translators: %1$s = <strong>, %2$s = </strong> */
			echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Goal %1$sactivated%2$s.'), '<strong>', '</strong>') . '</p></div>';
		}
		elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'activate-selected')
		{
			/* translators: %1$s = <strong>, %2$s = </strong> */
			echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Selected goals %1$sactivated%2$s.'), '<strong>', '</strong>') . '</p></div>';
		}
		elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deactivate')
		{
			/* translators: %1$s = <strong>, %2$s = </strong> */
			echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Goal %1$sdeactivated%2$s.'), '<strong>', '</strong>') . '</p></div>';
		}
		elseif (isset($_REQUEST['action']) && $_REQUEST['action'] == 'deactivate-selected')
		{
			/* translators: %1$s = <strong>, %2$s = </strong> */
			echo '<div id="message" class="updated notice is-dismissible"><p>' . sprintf(esc_html__('Selected goals %1$sdeactivated%2$s.'), '<strong>', '</strong>') . '</p></div>';
		}

		$goalTable = new DigitalPointBetterAnalytics_Formatting_GoalTable(array('plural' => 'goals', 'goals' => $goals));

		$goalTable->prepare_items();
		$goalTable->views();
		$goalTable->display();

		echo '</form></div>';
	}
}