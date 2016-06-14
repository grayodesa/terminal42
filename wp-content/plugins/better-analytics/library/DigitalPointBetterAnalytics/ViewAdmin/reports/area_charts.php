<?php

wp_enqueue_script('jsapi', 'https://www.google.com/jsapi?autoload=%7B%22modules%22%3A%5B%7B%22name%22%3A%22visualization%22%2C%22version%22%3A%221.1%22%2C%22packages%22%3A%5B%22corechart%22%5D%7D%5D%7D', array(), null );

wp_enqueue_script('chosen_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.jquery.min.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('chosen_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.min.css', array(), BETTER_ANALYTICS_VERSION);

wp_enqueue_script('better_analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);
echo '<h2>' . esc_html__('Reports & Charts', 'better-analytics') . '</h2>';

?>


<h3 class="nav-tab-wrapper">
	<a class="nav-tab" href="<?php echo menu_page_url('better-analytics_heatmaps', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Weekly Heat Maps', 'better-analytics' ); ?></a>
	<a class="nav-tab nav-tab-active" href="<?php echo menu_page_url('better-analytics_areacharts', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Charts', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php echo menu_page_url('better-analytics_events', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Events', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php echo menu_page_url('better-analytics_monitor', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Issue Monitoring', 'better-analytics' ); ?></a>
</h3>

<div id="chart_loading" class="dashicons dashicons-update"></div>
<div id="area_chart"></div>

<form>
	<table id="parameters" class="form-table">
		<?php
		echo '<tr valign="top">
						<th scope="row">' . esc_html__('Dimension', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_dimension" name="dimension" class="chosen-charts">';

		foreach ($dimensions as $label => $group)
		{
			echo '<optgroup label="' . htmlentities($label) . '">';

			foreach ($group as $key => $name)
			{
				echo '<option value="' . $key . '"' . selected($key, 'browser') . '>' . htmlentities($name) . '</option>';

			}

			echo '</optgroup>';
		}
		echo '</select>
						</td>
					</tr>';


		echo '<tr valign="top">
						<th scope="row">' . esc_html__('Time Frame', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_time_frame" name="time_frame" class="chosen-charts">';

			echo '<option value="30">' . esc_html__('1 Month', 'better-analytics') . '</option>';
			echo '<option value="365" selected="selected">' . esc_html__('1 Year', 'better-analytics') . '</option>';
			echo '<option value="730">' . esc_html__('2 Years', 'better-analytics') . '</option>';
			echo '<option value="1825">' . esc_html__('5 Years', 'better-analytics') . '</option>';
			echo '<option value="3650">' . esc_html__('10 Years', 'better-analytics') . '</option>';

		echo '</select>
						</td>
					</tr>';


		echo '<tr valign="top">
						<th scope="row">' . esc_html__('Scope', 'better-analytics') . '</th>
						<td>';

		echo '<select id="ba_scope" name="scope" class="chosen-charts">';

			echo '<option value="day">' . esc_html__('Day', 'better-analytics') . '</option>';
			echo '<option value="month" selected="selected">' . esc_html__('Month', 'better-analytics') . '</option>';
			echo '<option value="year">' . esc_html__('Year', 'better-analytics') . '</option>';

		echo '</select>
						</td>
					</tr>';

		echo '<tr valign="top">
						<th scope="row">' . esc_html__('Minimum Value To Plot', 'better-analytics') . '</th>
						<td>';

		echo '<input type="number" id="ba_minimum" name="minimum" value="' . (empty($_REQUEST['page_path']) ? 100 : 1) . '" min="0" step="100">
						</td>
					</tr>';

		echo '<tr valign="top">
						<th scope="row">' . esc_html__('Display Chart As', 'better-analytics') . '</th>
						<td>';

		echo '<label><input name="chart_type" type="radio" value="percent"  checked="checked">' . esc_html__('Stacked Area Percent', 'better-analytics') . '</label> &nbsp;  &nbsp; ';
		echo '<label><input name="chart_type" type="radio" value="absolute">' . esc_html__('Stacked', 'better-analytics') . '</label> &nbsp;  &nbsp; ';
		echo '<label><input name="chart_type" type="radio" value="">' . esc_html__('Overlap', 'better-analytics') . '</label>


						</td>
					</tr>';


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