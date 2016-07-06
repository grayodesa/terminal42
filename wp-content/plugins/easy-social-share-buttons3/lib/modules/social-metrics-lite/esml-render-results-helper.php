<?php

class ESMLRenderResultsHelper {
	public $total_results = array();
	public $services = array();
	public $top_content = array();
	
	public $data_report = array();
	
	function __construct() {
		$this->services = array(
				'facebook'   => 'Facebook',
				'twitter'    => 'Twitter',
				'googleplus' => 'Google Plus',
				'linkedin'   => 'LinkedIn',
				'pinterest'  => 'Pinterest',
				'diggs'      => 'Digg.com',
				'delicious'	 => 'Delicious',
				'facebook_comments'	 => 'Facebook Comments',
				'stumbleupon'=> 'Stumble Upon'
		);
	}
	
	public function generate_data($options) {
		global $wpdb;
		
		$post_types = $this->get_post_types ();
		add_filter ( 'posts_where', array ($this, 'date_range_filter' ) );
		
		$querydata = new WP_Query ( array ('posts_per_page' => - 1, 'post_status' => 'publish', 'post_type' => $post_types ) );
		
		remove_filter ( 'posts_where', array ($this, 'date_range_filter' ) );
		
		$data = array ();
		
		// foreach ($querydata as $querydatum ) {
		if ($querydata->have_posts ()) :
			while ( $querydata->have_posts () ) :
				$querydata->the_post ();
				global $post;
				
				$item ['ID'] = $post->ID;
				$item ['post_title'] = $post->post_title;
				$item ['post_date'] = $post->post_date;
				$item ['comment_count'] = $post->comment_count;
				$item ['esml_socialcount_total'] = (get_post_meta ( $post->ID, "esml_socialcount_TOTAL", true )) ? get_post_meta ( $post->ID, "esml_socialcount_TOTAL", true ) : 0;
				$item ['esml_socialcount_LAST_UPDATED'] = get_post_meta ( $post->ID, "esml_socialcount_LAST_UPDATED", true );
				$item ['permalink'] = get_permalink ( $post->ID );
				
				if (! isset ( $this->total_results ['esml_socialcount_total'] )) {
					$this->total_results ['esml_socialcount_total'] = 0;
				}
				$this->total_results ['esml_socialcount_total'] = $this->total_results ['esml_socialcount_total'] + $item ['esml_socialcount_total'];
				
				foreach ( $this->services as $slug => $name ) {
					$item ['esml_socialcount_' . $slug] = get_post_meta ( $post->ID, "esml_socialcount_$slug", true );
					
					if (! isset ( $this->total_results ['esml_socialcount_' . $slug] )) {
						$this->total_results ['esml_socialcount_' . $slug] = 0;
					}
					$this->total_results ['esml_socialcount_' . $slug] = $this->total_results ['esml_socialcount_' . $slug] + $item ['esml_socialcount_' . $slug];
					
					if (! isset ( $this->top_content ['esml_socialcount_' . $slug] )) {
						$blank = array ("title" => "", "permalink" => "", "value" => "0" );
						$this->top_content ['esml_socialcount_' . $slug] = $blank;
					}
					
					if ($item ['esml_socialcount_' . $slug] > $this->top_content ['esml_socialcount_' . $slug] ["value"]) {
						$this->top_content ['esml_socialcount_' . $slug] ["value"] = $item ['esml_socialcount_' . $slug];
						$this->top_content ['esml_socialcount_' . $slug] ["title"] = $item ['post_title'] = $post->post_title;
						$this->top_content ['esml_socialcount_' . $slug] ["permalink"] = $item ['permalink'];
					}
				}
				
				array_push ( $data, $item );
			endwhile
			;
		
		endif;
		
		$this->data_report = $data;
	}
	
	function date_range_filter($where = '') {
		
		$range = (isset ( $_GET ['range'] )) ? $_GET ['range'] : '0';
		
		if ($range <= 0)
			return $where;
		
		$range_bottom = " AND post_date >= '" . date ( "Y-m-d", strtotime ( '-' . $range . ' month' ) );
		$range_top = "' AND post_date <= '" . date ( "Y-m-d" ) . "'";
		
		$where .= $range_bottom . $range_top;
		return $where;
	}
	
	public function get_post_types() {
		global $essb_options;
		
		$types_to_track = array ();
		
		$pts = get_post_types ( array ('public' => true, 'show_ui' => true, '_builtin' => true ) );
		$cpts = get_post_types ( array ('public' => true, 'show_ui' => true, '_builtin' => false ) );
		$options = $essb_options;
		
		if (is_array ( $options )) {
			if (! isset ( $options ['esml_monitor_types'] )) {
				$options ['esml_monitor_types'] = array ();
			}
		}
		
		if (is_array ( $options ) && isset ( $options ['esml_monitor_types'] ) && is_array ( $options ['esml_monitor_types'] )) {
			
			global $wp_post_types;
			// classical post type listing
			foreach ( $pts as $pt ) {
				
				$selected = in_array ( $pt, $options ['esml_monitor_types'] ) ? '1' : '0';
				
				if ($selected == '1') {
					$types_to_track [] = $pt;
				}
			
			}
			
			// custom post types listing
			if (is_array ( $cpts ) && ! empty ( $cpts )) {
				foreach ( $cpts as $cpt ) {
					
					$selected = in_array ( $cpt, $options ['esml_monitor_types'] ) ? '1' : '0';
					
					if ($selected == '1') {
						$types_to_track [] = $cpt;
					}
					
					$selected = in_array ( $cpt, $options ['esml_monitor_types'] ) ? 'checked="checked"' : '';
				}
			}
		}
		
		return $types_to_track;
	
	}
	
	public function output_total_chart() {
		$parse_list = array("facebook" => "Facebook", "twitter" => "Twitter", "googleplus" => "Google+", "pinterest" => "Pinterest", "linkedin" => "LinkedIn", "stumbleupon" => "StumbleUpon");
		$color_list = array("facebook" => "#4769A5", "twitter" => "#65CCEF", "googleplus" => "#bf3727", "pinterest" => "#cd252b", "linkedin" => "#2ba3e1", "stumbleupon" => "#eb4723");
		$output = "";
		
		foreach ($parse_list as $singleValueCode => $singleValue) {
			$single_value = $this->total_results['esml_socialcount_'.$singleValueCode];
			$network_color = $color_list[$singleValueCode];
			$single_value = intval($single_value);
			
			if ($single_value > 0) {
				if ($output != "") { $output .= ","; }
				$output .= '{value:'.$single_value.', label:"'.$singleValue.'", color: "'.$network_color.'"}';
			}
		}
		
		$html = "";
		
		if ($output != '') {
			$html = '<canvas id="chartByNetwork" width="300" height="300" ></canvas>';
			$html .= '
			<script type="text/javascript">
			jQuery(document).ready(function() {
			var data = ['.$output.'];
			var ctx = document.getElementById("chartByNetwork").getContext("2d");
			var myPieChart = new Chart(ctx).Pie(data);
			} );
			</script>
			';
			
		}
		
		print $html;
	}
	
	public function output_total_results() {
		 
		if (!isset($this->total_results['esml_socialcount_total'])) {
			$this->total_results['esml_socialcount_total'] = 0;
		}
		
		echo '<table border="0" cellpadding="3" cellspacing="0" width="100%">';
		echo '<col width="30%"/>';
		echo '<col width="30%"/>';
		echo '<col width="40%"/>';
		 
		echo '<tr>';
		echo '<td><strong>Total Social Shares:</strong></td>';
		echo '<td align="right"><strong>'.number_format($this->total_results['esml_socialcount_total']).'</strong></td>';
		echo '<td>&nbsp;</td>';
		echo '</tr>';
		 
		$total = $this->total_results['esml_socialcount_total'];
		$parse_list = array("facebook" => "Facebook", "twitter" => "Twitter", "googleplus" => "Google+", "pinterest" => "Pinterest", "linkedin" => "LinkedIn", "stumbleupon" => "StumbleUpon");
	
		foreach ($parse_list as $singleValueCode => $singleValue) {
			$single_value = isset($this->total_results['esml_socialcount_'.$singleValueCode]) ? $this->total_results['esml_socialcount_'.$singleValueCode] : 0;
	
			if ($total != 0) {
				$display_percent = number_format($single_value * 100 / $total, 2);
				$percent = number_format($single_value * 100 / $total);
			}
			else {
				$display_percent = "0.00";
				$percent = "0";
			}
	
			if (intval($percent) == 0 && intval($single_value) != 0) {
				$percent = 1;
			}
	
			echo '<tr>';
			echo '<td>'.$singleValue.' <span style="background-color: #2980b9; padding: 2px 5px; color: #fff; font-size: 10px; border-radius: 3px;">'.$display_percent.' %</span></td>';
			echo '<td align="right"><strong>'.number_format($single_value).'</strong></td>';
			echo '<td><div style="background-color: #2980b9; display: block; height: 24px; width:'.$percent.'%;">&nbsp;</div></td>';
			echo '</tr>';
		}
		 
		echo '</table>';
	}
	
	public function output_total_content() {
	
		echo '<table border="0" cellpadding="5" cellspacing="0" width="100%">';
		echo '<col width="20%"/>';
		echo '<col width="20%"/>';
		echo '<col width="60%"/>';
	
		$parse_list = array("facebook" => "Facebook", "twitter" => "Twitter", "googleplus" => "Google+", "pinterest" => "Pinterest", "linkedin" => "LinkedIn", "stumbleupon" => "StumbleUpon");
	
		foreach ($parse_list as $singleValueCode => $singleValue) {
			if (!isset($this->top_content['esml_socialcount_'.$singleValueCode])) { continue; }
			$single_value = $this->top_content['esml_socialcount_'.$singleValueCode]['value'];
			$title = $this->top_content['esml_socialcount_'.$singleValueCode]['title'];
			$permalink = $this->top_content['esml_socialcount_'.$singleValueCode]['permalink'];
	
	
			echo '<tr>';
			echo '<td>'.$singleValue.'</td>';
			echo '<td align="right"><strong>'.number_format($single_value).'</strong></td>';
			echo '<td><a href="'.$permalink.'" target="_blank">'.$title.'</a></td>';
			echo '</tr>';
		}
	
		echo '</table>';
	}
	
	public function output_main_result() {
		echo '<table id="esml-result" class="display hover row-border stripe" cellspacing="0" width="100%">';
		echo '<thead>';
		echo '<tr>';
		echo '<th>Title</th>';
		echo '<th>Total</th>';
		echo '<th>Facebook</th>';
		echo '<th>Twitter</th>';
		echo '<th>Google+</th>';
		echo '<th>LinkedIn</th>';
		echo '<th>Pinterest</th>';
		echo '<th>StumbleUpon</th>';
		echo '<th>Post Comments</th>';
		echo '<th>Facebook Comments</th>';
		echo '</tr>';
		echo '</thead>';
		
		echo '<tbody>';
		
		foreach ($this->data_report as $item) {
			
			$total_value = number_format(intval($item['esml_socialcount_total']));
			
			$item_actions = sprintf('<a href="post.php?post=%s&action=edit">Edit Post</a>',$item['ID'],'edit',$item['ID']) .
			'&nbsp;<a href="'.esc_url(add_query_arg( 'esml_sync_now', $item['ID'])).'">Update Stats</a>&nbsp;' .
			sprintf('Updated %s',EasySocialMetricsLite::timeago($item['esml_socialcount_LAST_UPDATED']));
			
			echo '<tr>';
			
			echo '<td>';
			printf('%1$s <br/><span class="esml-navigation-item">(id:%2$s) %3$s</span>',
					/*$1%s*/ $item['post_title'],
					/*$2%s*/ $item['ID'],
					/*$3%s*/ $item_actions);
			echo '</td>';
			echo '<td align="right">'.$total_value.'</td>';
			
			foreach ($this->services as $key => $text) {
				if ($key == "diggs") { continue; }
				echo '<td align="right">'.number_format(intval($item['esml_socialcount_'.$key])).'</td>';
			}
			
			echo '</tr>';
		}
		
		echo '</tbody>';
		
		echo '</table>';
	}
}

?>