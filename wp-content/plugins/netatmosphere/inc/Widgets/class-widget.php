<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

include_once(ABSPATH . 'wp-includes/widgets.php');

/**
 * Adds NAS_Widget widget.
 */
class NAS_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'netatmosphere_widget', // Base ID
			__( 'NetAtmoSphere', 'netatmosphere' ), // Name
			array( 'description' => __( 'Widget to display actual weather data', 'netatmosphere' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		$showLatest = false; // $instance['show_latest'] === 'on';
		$showDailyAvg = $instance['show_daily_avg'] === 'on';
		$showMap = $instance['show_map'] === 'on';
        $chartIds = !empty($instance['show_chart_id']) ? $instance['show_chart_id'] : "";
        /*echo "showLatest: " . $showLatest . "<br/>";
        echo "showDailyAvg: " . $showDailyAvg . "<br/>";
        echo "showMap: " . $showMap . "<br/>";
        echo "chartIds: " . $chartIds . "<br/>";*/

        $showTable = $showLatest || $showDailyAvg;
        
        $latest = null; $dailyAvg = null; $coordinates = null;
        // prepare variables
        if( $showLatest )
            $latest = NAS_Data_Adapter::getLatest(NAS_Device_Locations::Outdoor);
        if( $showDailyAvg )
            $dailyAvg = NAS_Data_Adapter::getAvgOfDay(null, NAS_Device_Locations::Outdoor);
        if( $showMap ) 
            $coordinates = NAS_Devices_Adapter::getLocation();
        
        if( $showTable ) {
            
            $table = new NAS_Data_Table();
            
            if( null !== $latest && count ( $latest ) > 0 ) 
                $table->addRow( NAS_Data_Adapter::results2row($latest, __('Latest', 'netatmosphere'), __('Today', 'netatmosphere') ), true );
            if( null !== $dailyAvg && count ( $dailyAvg ) > 0 )
                $table->addRow( NAS_Data_Adapter::results2row($dailyAvg, __('Avg.', 'netatmosphere'), __('Today', 'netatmosphere') ), true );
            
            $table->mergeColumns();
            
            echo $table->htmlTable(null, 'netatmosphere_widget_table');
        }
        
        if( $showMap ) {
            
            echo '<iframe frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=' . $coordinates['lat'] . '%2C%20' . $coordinates['lng'] . '&amp;key=AIzaSyAerG7YMfNlrQpWRyG1wvH1O9wcmNWOSHs&amp;zoom=12" allowfullscreen></iframe>';
        }
        
        if( ! empty( $chartIds ) && strlen( $chartIds ) > 0 ) {
            
            if( NAS_Options::getInstance()->IsChartActive() && true === function_exists ( 'get_html_4_chart' ) ) {
                $chartIds = array_unique ( explode ( ",", $chartIds ) );
                
                // see for details:
                // wp-business-intelligence-lite/tinymce.php : function wpbi_mce_tag
                // $code = get_html_4_chart($id);                
                foreach ( $chartIds as $chartId ) {
                    if ( $chartId > 0 ) {
                        echo get_html_4_chart( $chartId );
                    }
                }
                
            } else {
                echo "<p>" . sprintf ( __( 'To display a chart, please install "%s" plugin', 'netatmosphere'), 'WP Business Intelligence Lite') . "</p>";
            }
            
        }
        
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Weather @ home', 'netatmosphere' );
        $instance['show_latest']    = false; // ! empty( $instance['show_latest'] ) ? $instance['show_latest'] : false;
        $instance['show_daily_avg'] = ! empty( $instance['show_daily_avg'] ) ? $instance['show_daily_avg'] : false;
        $instance['show_map']       = ! empty( $instance['show_map'] ) ? $instance['show_map'] : false;
        $instance['show_chart_id']  = ! empty( $instance['show_chart_id'] ) ? $instance['show_chart_id'] : "";
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" disabled="disabled" <?php checked($instance['show_latest'], 'on'); ?> id="<?php echo $this->get_field_id('show_latest'); ?>" name="<?php echo $this->get_field_name('show_latest'); ?>" title="<?php _e('Its disabled due to database performance issues!', 'netatmosphere'); ?>" /> 
			<label for="<?php echo $this->get_field_id('show_latest'); ?>" title="<?php _e('Its disabled due to database performance issues!', 'netatmosphere'); ?>"><?php _e('Show latest measures', 'netatmosphere'); ?></label>
		</p>
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['show_daily_avg'], 'on'); ?> id="<?php echo $this->get_field_id('show_daily_avg'); ?>" name="<?php echo $this->get_field_name('show_daily_avg'); ?>" /> 
			<label for="<?php echo $this->get_field_id('show_daily_avg'); ?>"><?php _e('Show daily avg. measures', 'netatmosphere'); ?></label>
		</p>
        <p>
			<input class="checkbox" type="checkbox" <?php checked($instance['show_map'], 'on'); ?> id="<?php echo $this->get_field_id('show_map'); ?>" name="<?php echo $this->get_field_name('show_map'); ?>" /> 
			<label for="<?php echo $this->get_field_id('show_map'); ?>"><?php _e('Show location on map', 'netatmosphere'); ?></label>
		</p>
        <p>
			<label for="<?php echo $this->get_field_id('show_chart_id'); ?>"><?php _e('Show chart with that ID(s) on the widget:', 'netatmosphere'); ?></label>
			<input class="" type="text" id="<?php echo $this->get_field_id('show_chart_id'); ?>" name="<?php echo $this->get_field_name('show_chart_id'); ?>" value="<?php echo $instance['show_chart_id']; ?>" title='<?php _e('Zero or empty means no chart to display, seperate multiple with comma!', 'netatmosphere'); ?>' />
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        
		$instance['show_latest']    = false; // disable always! query is too expensive... :( filter_var ( $new_instance['show_latest'], FILTER_SANITIZE_STRING );
		$instance['show_daily_avg'] = filter_var ( $new_instance['show_daily_avg'], FILTER_SANITIZE_STRING );
		$instance['show_map']       = filter_var ( $new_instance['show_map'], FILTER_SANITIZE_STRING );
		
        // sanitize chart id's (normalize seperator, remove duplicates)
        $chartIds  = filter_var ( $new_instance['show_chart_id'], FILTER_SANITIZE_STRING );
        $chartIds = str_replace ( ";", ",", $chartIds );
        $chartIds = array_unique ( explode ( ',', $chartIds ) );
        $instance['show_chart_id'] = implode ( ',', $chartIds );
		
		return $instance;
	}
}

?>