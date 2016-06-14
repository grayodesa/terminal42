<?php
/**
 * @package BetterAnalytics
 */
class DigitalPointBetterAnalytics_Widget_PopularPosts extends WP_Widget
{

	function __construct()
	{
		parent::__construct(
			'better-analytics_popular_widget',
			esc_html__( 'Better Analytics: Popular' , 'better-analytics'),
			array( 'description' => esc_html__( 'Display the most popular content right now.', 'better-analytics'))
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
		$title  = !empty( $instance['title'] ) ? esc_attr( $instance['title'] ) : esc_attr('Popular Right Now', 'better-analytics');
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;

		$betterAnalyticsOptions = get_option('better_analytics');

		if (!DigitalPointBetterAnalytics_Base_Public::getInstance()->getTokens() || !$betterAnalyticsOptions['api']['profile'])
		{
			echo '<p>' . sprintf(esc_html__('No Linked Google Analytics Account (API access required for this widget).  You can link one in the %1$sBetter Analytics API settings%2$s.', 'better-analytics'), '<a href="' . esc_url(menu_page_url('better-analytics', false) . '#top#api') . '">', '</a>') . '</p>';
		}

		?>

		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e( 'Number of URLs to show:', 'better-analytics' ); ?></label>
			<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

	<?php
	}

	function update( $new_instance, $old_instance )
	{
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = absint( $new_instance['number'] );
		return $instance;
	}

	function widget($args, $instance)
	{
		$realTime = get_transient('ba_realtime');

		if (!empty($realTime['page_path']))
		{
			$numberToShow = (empty($instance['number']) ? 5 : absint($instance['number']));

			$pagesOutput = array();
			foreach ($realTime['page_path'] as $page => $count)
			{
				if (substr($page, 0, 1) == '/' && strlen($page) > 1 && strpos($page, '/wp-admin/') === false)
				{
					$pagesOutput[] = $page;
					if (count($pagesOutput) >= $numberToShow)
					{
						break;
					}
				}
			}

			if (count($pagesOutput) > 0)
			{
				echo $args['before_widget'];
				if (!empty($instance['title']))
				{
					echo $args['before_title'];
					echo esc_html( $instance['title'] );
					echo $args['after_title'];
				}

				echo '<ul>';

				foreach ($pagesOutput as $page)
				{
					$url = htmlspecialchars($page);
					echo '<li><a href="' . $url . '">' . $url . '</a></li>';
				}

				echo '</ul>';
				echo $args['after_widget'];

			}

		}

	}

	static function register_widget()
	{
		register_widget(__CLASS__);
	}
}