<?php

class ESSBCoreHelper {
	public static function generate_network_list() {
		global $essb_networks;
		
		$network_order = array();
		
		foreach ($essb_networks as $key => $data) {
			$network_order[] = $key;
		}
		
		return $network_order;
	}
	
	public static function template_folder ($template_id) {
		$folder = 'default';
	
		if ($template_id == 1) {
			$folder = "default";
		}
		if ($template_id == 2) {
			$folder = "metro";
		}
		if ($template_id == 3) {
			$folder = "modern";
		}
		if ($template_id == 4) {
			$folder = "round";
		}
		if ($template_id == 5) {
			$folder = "big";
		}
		if ($template_id == 6) {
			$folder = "metro-retina";
		}
		if ($template_id == 7) {
			$folder = "big-retina";
		}
		if ($template_id == 8) {
			$folder = "light-retina";
		}
		if ($template_id == 9) {
			$folder = "flat-retina";
		}
		if ($template_id == 10) {
			$folder = "tiny-retina";
		}
		if ($template_id == 11) {
			$folder = "round-retina";
		}
		if ($template_id == 12) {
			$folder = "modern-retina";
		}
		if ($template_id == 13) {
			$folder = "circles-retina";
		}
		if ($template_id == 14) {
			$folder = "blocks-retina";
		}
		if ($template_id == 15) {
			$folder = "dark-retina";
		}
		if ($template_id == 16) {
			$folder = "grey-circles-retina";
		}
		if ($template_id == 17) {
			$folder = "grey-blocks-retina";
		}
		if ($template_id == 18) {
			$folder = "clear-retina";
		}
		if ($template_id == 19) {
			$folder = "copy-retina";
		}
		if ($template_id == 20) {
			$folder = "dimmed-retina";
		}
		if ($template_id == 21) {
			$folder = "grey-retina";
		}
		if ($template_id == 22) {
			$folder = "default-retina";
		}
		if ($template_id == 23) {
			$folder = "jumbo-retina";
		}
		if ($template_id == 24) {
			$folder = "jumbo-round-retina";
		}
		if ($template_id == 25) {
			$folder = "fancy-retina";
		}
		if ($template_id == 26) {
			$folder = "deluxe-retina";
		}
		if ($template_id == 27) {
			$folder = "modern-slim-retina";
		}
		if ($template_id == 28) {
			$folder = "bold-retina";
		}
		if ($template_id == 29) {
			$folder = "fancy-bold-retina";
		}
		if ($template_id == 30) {
			$folder = "retro-retina";
		}
		if ($template_id == 31) {
			$folder = "metro-bold-retina";
		}
		// fix when using template_slug instead of template_id
		if (intval($template_id) == 0 && $template_id != '') {
			$folder = $template_id;
		}
	
		return $folder;
	}
	
	public static function urlencode($str) {
		$str = str_replace(" ", "%20", $str);
		$str = str_replace("'", "%27", $str);
		$str = str_replace("\"", "%22", $str);
		$str = str_replace("#", "%23", $str);
		$str = str_replace("+", "%2B", $str);
		$str = str_replace("$", "%24", $str);
		$str = str_replace("&", "%26", $str);
		$str = str_replace(",", "%2C", $str);
		$str = str_replace("/", "%2F", $str);
		$str = str_replace(":", "%3A", $str);
		$str = str_replace(";", "%3B", $str);
		$str = str_replace("=", "%3D", $str);
		$str = str_replace("?", "%3F", $str);
		$str = str_replace("@", "%40", $str);
		$str = str_replace("\%27", "%27", $str);
		
		return $str;
	}
	
	public static function generate_list_networks($all_networks = false) {
		global $essb_networks, $essb_options;
		$networks = array();
		
		$listOfNetworks = ($all_networks) ? self::generate_network_list() : ESSBOptionValuesHelper::options_value($essb_options, 'networks');
		
		foreach ($listOfNetworks as $single) {
			if ($single != 'more') {
				$networks[] = $single;
			}
		}
		
		return $networks;
	}
	
	public static function generate_fullwidth_key($style) {
		
	}
	
	public static function is_plugin_deactivated_on() {
		global $essb_options;
		
	
		if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'reset_postdata')) {
			wp_reset_postdata();
		}
	
		//display_deactivate_on
		$is_deactivated = false;
		$exclude_from = ESSBOptionValuesHelper::options_value($essb_options, 'display_deactivate_on');
		if (!empty($exclude_from)) {
			$excule_from = explode(',', $exclude_from);
				
			$excule_from = array_map('trim', $excule_from);
			if (in_array(get_the_ID(), $excule_from, false)) {
				$is_deactivated = true;
			}
		}
				
		
		return $is_deactivated;
	}
	
	public static function is_module_deactivate_on($module = 'share') {
		global $essb_options;
		
		
		if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'reset_postdata')) {
			wp_reset_postdata();
		}
		
		//display_deactivate_on
		$is_deactivated = false;
		$exclude_from = ESSBOptionValuesHelper::options_value($essb_options, 'deactivate_on_'.$module);
		if (!empty($exclude_from)) {
			$excule_from = explode(',', $exclude_from);
		
			$excule_from = array_map('trim', $excule_from);
			if (in_array(get_the_ID(), $excule_from, false)) {
				$is_deactivated = true;
			}
		}
		return $is_deactivated;
	}
	
	public static function post_details_to_content($content) {
		global $post;
		
		if (isset($post)) {
			$url = get_permalink();
			$title_plain = $post->post_title;
			$post_image = has_post_thumbnail( $post->ID ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ) : '';
			$image = ($post_image != '') ? $post_image[0] : '';
			$description = $post->post_excerpt;
			
			$content = preg_replace(array('#%%title%%#', '#%%url%%#', '#%%image%%#', '#%%excerpt%%#'), array($title_plain, $url, $image, $description), $content);
				
		}
		
		return $content;
	}
	
	public static function leading_posts_from_analytics_for7days() {
		global $wpdb;
		$table_name = $wpdb->prefix . ESSB3_TRACKER_TABLE;
		
		$toDate = date ( "Y-m-d" );
		$fromDate = date ( "Y-m-d", strtotime ( date ( "Y-m-d", strtotime ( date ( "Y-m-d" ) ) ) . "-8 days" ) );
		
		$query = "";
		//foreach($essb_networks as $k => $v) {
		$query .= "SELECT essb_post_id, COUNT( essb_post_id ) AS cnt";
		
		$query .= ' FROM  '.$table_name .'
		WHERE essb_date BETWEEN "'.$fromDate.'" AND "'.$toDate.'"
		GROUP BY essb_post_id
		ORDER BY cnt DESC';
		
		$post_stats = $wpdb->get_results ( $query );
		
		$limit = 4;
		$cnt = 0;
		$result_posts = array();
		
		if (isset($post_stats)) {
			foreach ( $post_stats as $rec ) {

				$post_permalink = get_permalink($rec->essb_post_id);
				$post_title = get_the_title($rec->essb_post_id);
				$post_excerpt = self::get_excerpt_by_id($rec->essb_post_id);
				
				$post_image = has_post_thumbnail( $rec->essb_post_id ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $rec->essb_post_id ), 'single-post-thumbnail' ) : '';
				$image = ($post_image != '') ? $post_image[0] : '';
				$cnt++;
				
				$result_posts[] = array("title" => $post_title, "url" => $post_permalink, "excerpt" => $post_excerpt, "image" => $image);
				
				if ($limit < $cnt) {
					break;
				}
			}
		}
		
		return $result_posts;
	}
	
	public static function get_excerpt_by_id($post_id) {
		$the_post = get_post ( $post_id ); // Gets post ID
		$the_excerpt = $the_post->post_content; // Gets post_content to be used as
		                                        // a basis for the excerpt
		$excerpt_length = 35; // Sets excerpt length by word count
		$the_excerpt = strip_tags ( strip_shortcodes ( $the_excerpt ) ); // Strips tags
		                                                           // and images
		$words = explode ( ' ', $the_excerpt, $excerpt_length + 1 );
		if (count ( $words ) > $excerpt_length) :
			array_pop ( $words );
			array_push ( $words, '…' );
			$the_excerpt = implode ( ' ', $words );
		
		endif;
		$the_excerpt = '<p>' . $the_excerpt . '</p>';
		return $the_excerpt;
	}
	
	public static function prepare_leadingposts_html($posts_data) {
		$output = "";
		
		foreach ($posts_data as $post_object) {
			$output .= '<div class="essb-heroshare-leading-post">';
			
			if (!empty($post_object['image'])) {
				$output .= '<a href="'.$post_object['url'].'"><img src="'.$post_object['image'].'" class="essb-heroshare-leading-post-image"/></a>';
			}
			
			$output .= '<a href="'.$post_object['url'].'"><h4>'.$post_object['title'].'</h4>';
			$output .= $post_object['excerpt'].'</a>';
			
			$output .= '</div>';
		}
		
		return $output;
	}
	
	/**
	 * get_post_featured_image
	 *
	 * Generate post featured image that will be used for sharing on networks that support image as optional parameter
	 *
	 * @param number $post_id
	 * @returns string
	 * @since 3.4.1
	 */
	public static function get_post_featured_image($post_id) {
		$post_cached_image = get_post_meta($post_id, 'essb_cached_image', true);
	
		if (empty($post_cached_image)) {
			$post_image = has_post_thumbnail( $post_id ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ) : '';
			$post_cached_image = ($post_image != '') ? $post_image[0] : '';
				
			if (!empty($post_cached_image)) {
				update_post_meta ( $post_id, 'essb_cached_image', $post_cached_image );
			}
		}
	
		return $post_cached_image;
	}
}

?>