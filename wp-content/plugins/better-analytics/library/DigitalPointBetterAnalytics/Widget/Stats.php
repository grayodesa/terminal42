<?php
/**
 * @package BetterAnalytics
 */
class DigitalPointBetterAnalytics_Widget_Stats extends WP_Widget {

	function __construct()
	{
		parent::__construct(
			'better-analytics_stats_widget',
			esc_html__( 'Better Analytics: Stats' , 'better-analytics'),
			array('description' => esc_html__('Display your Analytics stats publicly.', 'better-analytics'))
		);

		if (is_active_widget(false, false, $this->id_base))
		{
			add_action('wp_head', array( $this, 'css'));
		}
	}

	function css() {
		?>

		<style type="text/css">
			.widget_better_analytics_popular_widget li {
				overflow: hidden;
				text-overflow: ellipsis;
			}

			.widget_better_analytics_popular_widget li a {
				white-space: nowrap;
			}
		</style>

	<?php
	}

	function form( $instance )
	{
		wp_enqueue_script('chosen_js', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.jquery.min.js', array(), BETTER_ANALYTICS_VERSION );
		wp_enqueue_style('chosen_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/chosen/chosen.min.css', array(), BETTER_ANALYTICS_VERSION);

		$title  = isset($instance['title']) ? esc_attr( $instance['title'] ) : esc_attr('Analytics Stats', 'better-analytics');
		$metric = isset($instance['metric']) ? $instance['metric'] : 'ga:sessions';
		$days = isset($instance['days']) ? absint( $instance['days'] ) : 1;
		$format = isset($instance['format']) ? $instance['format'] : '%1$s: <strong>%2$s</strong>';
		$thisPageOnly = (isset($instance['this_page_only']) ? $instance['this_page_only'] : '');
		$private = (isset($instance['private']) ? $instance['private'] : '');

		$betterAnalyticsOptions = get_option('better_analytics');

		if (!DigitalPointBetterAnalytics_Base_Public::getInstance()->getTokens() || !$betterAnalyticsOptions['api']['profile'])
		{
			echo '<p>' . sprintf(esc_html__('No Linked Google Analytics Account (API access required for this widget).  You can link one in the %1$sBetter Analytics API settings%2$s.', 'better-analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#api') . '">', '</a>') . '</p>';
		}

		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>


		<?php

			$metrics = DigitalPointBetterAnalytics_Model_Reporting::getMetrics();

			echo '<select id="' . $this->get_field_id('metric') . '" name="' . $this->get_field_name('metric') . '" class="chosen-select">';

			foreach ($metrics as $label => $group)
			{
				echo '<optgroup label="' . htmlentities($label) . '">';

				foreach ($group as $key => $name)
				{
					echo '<option value="' . $key . '"' . selected($key, $metric) . '>' . htmlentities($name) . '</option>';
				}
				echo '</optgroup>';
			}
			echo '</select>
			<script>
				jQuery( document ).ready(function() {
					jQuery("#widgets-right .chosen-select").chosen({
							search_contains: true,
							width: "100%"
						});
				});
			</script>
';

		?>


		<p><label for="<?php echo $this->get_field_id('days'); ?>"><?php esc_html_e('Number of days to show:', 'better-analytics'); ?></label>
			<input id="<?php echo $this->get_field_id('days'); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" type="text" value="<?php echo $days; ?>" min="1" style="width:80px;"/></p>

		<p><label for="<?php echo $this->get_field_id('format'); ?>"><?php esc_html_e('HTML Format For Widget:', 'better-analytics'); ?></label>
			<textarea id="<?php echo $this->get_field_id('format'); ?>" name="<?php echo $this->get_field_name('format'); ?>" type="text" style="display:block;width:100%"><?php echo esc_textarea($format); ?></textarea>
			<span class="description"><?php esc_html_e('You can add HTML formatting here if you wish.  %1$s = metric name, %2$s = value', 'better-analytics'); ?></span>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('this_page_only'); ?>">
				<input id="<?php echo $this->get_field_id('this_page_only'); ?>" name="<?php echo $this->get_field_name( 'this_page_only' ); ?>" type="checkbox" value="1" <?php checked('1', $thisPageOnly ); ?>>
				<?php esc_html_e('Stats for current page only', 'better-analytics');?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('private'); ?>">
				<input id="<?php echo $this->get_field_id('private'); ?>" name="<?php echo $this->get_field_name( 'private' ); ?>" type="checkbox" value="1" <?php checked('1', $private ); ?>>
				<?php esc_html_e('Private (adheres to role/author permissions)', 'better-analytics');?></label>
		</p>

	<?php
	}

	function update( $new_instance, $old_instance )
	{
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['days'] = absint($new_instance['days']);
		$instance['format'] = $new_instance['format'];
		$instance['metric'] = $new_instance['metric'];
		$instance['this_page_only'] = $new_instance['this_page_only'];
		$instance['private'] = $new_instance['private'];

		if (!DigitalPointBetterAnalytics_Model_Reporting::getMetricNameByKey($instance['metric']))
		{
			$instance['metric'] = 'ga:sessions';
		}

		DigitalPointBetterAnalytics_Model_Widget::getStatsWidgetData(
			array($instance)
		);

		return $instance;
	}

	function widget($args, $instance)
	{
		if (!empty($instance['private']))
		{
			if (!DigitalPointBetterAnalytics_Base_Public::getInstance()->canViewReports())
			{
				return;
			}
		}

		$stats = get_transient('ba_stats_' . md5(@$instance['metric'] . '-' . @$instance['days'] . '-' . (@$instance['this_page_only'] ? $_SERVER['REQUEST_URI'] : '')));

		if (@$stats === false && !empty($instance['this_page_only']))
		{
			$cacheKey = DigitalPointBetterAnalytics_Model_Widget::getStatsWidgetStart($instance);
			$stats = DigitalPointBetterAnalytics_Model_Widget::getStatsWidgetEnd($instance, $cacheKey);
		}

		if (!empty($stats))
		{
			$metricTitle =  DigitalPointBetterAnalytics_Model_Reporting::getMetricNameByKey($instance['metric']);

			echo $args['before_widget'];
			if (!empty($instance['title']))
			{
				echo $args['before_title'];
				echo esc_html($instance['title']);
				echo $args['after_title'];
			}
			printf($instance['format'], $metricTitle, number_format_i18n($stats));
			echo $args['after_widget'];
		}
	}

	static function register_widget()
	{
		register_widget(__CLASS__);
	}
}