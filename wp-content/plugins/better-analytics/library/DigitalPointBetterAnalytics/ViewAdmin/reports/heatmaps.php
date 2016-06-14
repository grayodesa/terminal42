<?php
	wp_enqueue_script('chosen_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.jquery.min.js', array(), BETTER_ANALYTICS_VERSION );
	wp_enqueue_style('chosen_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.min.css', array(), BETTER_ANALYTICS_VERSION);

	wp_enqueue_script('better_analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
	wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

	echo '<h2>' . esc_html__('Reports & Charts', 'better-analytics') . '</h2>';
?>


<h3 class="nav-tab-wrapper">
	<a class="nav-tab nav-tab-active" href="<?php echo menu_page_url('better-analytics_heatmaps', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Weekly Heat Maps', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php echo menu_page_url('better-analytics_areacharts', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Charts', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php echo menu_page_url('better-analytics_events', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Events', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php echo menu_page_url('better-analytics_monitor', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Issue Monitoring', 'better-analytics' ); ?></a>
</h3>


<div id="chart_loading" class="dashicons dashicons-update"></div>

<div id="Heatmap" class="table">
	<div class="row">
		<div class="cell"></div>

		<div class="cell"><?php
				/* translators: Sunday (day of the week), with everything between %s hidden on smaller screens (responsive) */
				printf(esc_html__('Sun%sday%s', 'better-analytics'), '<span class="responsiveHide">', '</span>');
			?></div>

		<div class="cell"><?php
			/* translators: Monday (day of the week), with everything between %s hidden on smaller screens (responsive) */
			printf(esc_html__('Mon%sday%s', 'better-analytics'), '<span class="responsiveHide">', '</span>');
			?></div>

		<div class="cell"><?php
			/* translators: Tuesday (day of the week), with everything between %s hidden on smaller screens (responsive) */
			printf(esc_html__('Tue%ssday%s', 'better-analytics'), '<span class="responsiveHide">', '</span>');
			?></div>

		<div class="cell"><?php
			/* translators: Wednesday (day of the week), with everything between %s hidden on smaller screens (responsive) */
			printf(esc_html__('Wed%snesday%s', 'better-analytics'), '<span class="responsiveHide">', '</span>');
			?></div>

		<div class="cell"><?php
			/* translators: Thursday (day of the week), with everything between %s hidden on smaller screens (responsive) */
			printf(esc_html__('Thu%srsday%s', 'better-analytics'), '<span class="responsiveHide">', '</span>');
			?></div>

		<div class="cell"><?php
			/* translators: Friday (day of the week), with everything between %s hidden on smaller screens (responsive) */
			printf(esc_html__('Fri%sday%s', 'better-analytics'), '<span class="responsiveHide">', '</span>');
			?></div>

		<div class="cell"><?php
			/* translators: Saturday (day of the week), with everything between %s hidden on smaller screens (responsive) */
			printf(esc_html__('Sat%surday%s', 'better-analytics'), '<span class="responsiveHide">', '</span>');
			?></div>
	</div>
	<?php
		foreach ($heatmap_data as $hour_key => $hour_data)
		{
			echo '<div class="row"><div class="cell">' . $hour_map[$hour_key]. '</div>';
			foreach ($hour_data as $day_key => $day_data)
			{
				echo '<div id="slot' . $hour_key . '-' . $day_key . '" class="cell" data-val="' . $day_data . '"></div>';
			}
			echo '</div>';
		}
	?>
</div>

<form>
	<table id="parameters" class="form-table">
		<?php
		echo '<tr valign="top">
						<th scope="row">' . esc_html__('Metric', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_metric" name="metric" class="chosen-charts">';

		foreach ($metrics as $label => $group)
		{
			echo '<optgroup label="' . htmlentities($label) . '">';

			foreach ($group as $key => $name)
			{
				echo '<option value="' . $key . '"' . selected($key, 'ga:sessions') . '>' . htmlentities($name) . '</option>';

			}

			echo '</optgroup>';
		}
		echo '</select>
						</td>
					</tr>';


		echo '<tr valign="top">
						<th scope="row">' . esc_html__('Segment', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_segment" name="segment" class="chosen-charts">';

		foreach ($segments as $label => $group)
		{
			echo '<optgroup label="' . htmlentities($label) . '">';

			foreach ($group as $key => $name)
			{
				echo '<option value="' . $key . '">' . htmlentities($name) . '</option>';

			}

			echo '</optgroup>';
		}
		echo '</select>
						</td>
					</tr>';

		?>
		<tr>
			<th></th>
			<td>
				<input type="number" name="weeks" id="ba_weeks" size="5" min="1" max="1000" step="1" value="4" /> &nbsp; <?php esc_html_e('Weeks Of Data, Ending', 'better-analytics') ?> &nbsp;
				<input type="number" name="end" id="ba_end" size="5" min="0" max="10000" step="1" value="1" /> &nbsp; <?php esc_html_e('Days Ago', 'better-analytics') ?>
			</td>
		</tr>

		<?php
			if (!empty($_REQUEST['page_path']))
			{
				echo '<tr valign="top">
							<th scope="row">' . esc_html__('Page Path', 'better-analytics') . '</th>
							<td style="padding-top:15px">';
				echo '<input type="hidden" id="ba_page_path" value="' . htmlspecialchars($_REQUEST['page_path']) . '">';

				echo esc_html($_REQUEST['page_path']) . '
							</td>
						</tr>';
			}
		?>

	</table>
</form>