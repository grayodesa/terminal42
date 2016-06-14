<?php

wp_enqueue_script('jsapi', 'https://www.google.com/jsapi?autoload=%7B%22modules%22%3A%5B%7B%22name%22%3A%22visualization%22%2C%22version%22%3A%221.1%22%2C%22packages%22%3A%5B%22table%22%5D%7D%5D%7D', array(), null );

wp_enqueue_script('better_analytics_admin_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/js/admin.js', array(), BETTER_ANALYTICS_VERSION );
wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);
echo '<h2>' . esc_html__('Reports & Charts', 'better-analytics') . '</h2>';

?>


<h3 class="nav-tab-wrapper">
	<a class="nav-tab" href="<?php echo menu_page_url('better-analytics_heatmaps', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Weekly Heat Maps', 'better-analytics' ); ?></a>
	<a class="nav-tab" href="<?php echo menu_page_url('better-analytics_areacharts', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Charts', 'better-analytics' ); ?></a>
	<a class="nav-tab<?php echo ($type == 'events' ? ' nav-tab-active' : ''); ?>" href="<?php echo menu_page_url('better-analytics_events', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Events', 'better-analytics' ); ?></a>
	<a class="nav-tab<?php echo ($type == 'monitor' ? ' nav-tab-active' : ''); ?>" href="<?php echo menu_page_url('better-analytics_monitor', false) . (!empty($_REQUEST['page_path']) ? '&page_path=' . urlencode($_REQUEST['page_path']) : ''); ?>"><?php esc_html_e( 'Issue Monitoring', 'better-analytics' ); ?></a>
</h3>


<div id="chart_loading" class="dashicons dashicons-update"></div>

<form class="ba_monitor_form">
	<table id="parameters" class="form-table">
		<?php

		echo '<tr valign="top">
						<th scope="row">' . esc_html__('Days Back', 'better-analytics') . '</th>
						<td>';

		echo '<input type="number" id="ba_days" name="days" value="7" min="1" max="3650" step="10">
						</td>
					</tr>';

		if (!empty($_REQUEST['page_path']))
		{
			echo '<tr valign="top">
							<th scope="row">' . esc_html__('Page Path', 'better-analytics') . '</th>
							<td style="padding-top:18px">';
			echo '<input type="hidden" id="ba_page_path" value="' . htmlspecialchars($_REQUEST['page_path']) . '">';

			echo esc_html($_REQUEST['page_path']) . '
							</td>
						</tr>';
		}


		?>
	</table>
</form>

<div id="ba_monitor" data-type="<?php echo $type; ?>"></div>
