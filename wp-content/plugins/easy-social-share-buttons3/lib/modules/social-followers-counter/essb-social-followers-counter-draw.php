<?php

/**
 * ESSBSocialFollowersCounterDraw
 * 
 * Followers counter draw engine
 * 
 * @author appscreo
 * @package EasySocialShareButtons
 * @since 3.4
 *
 */
class ESSBSocialFollowersCounterDraw {
	
	public static function followers_number($count) {
		$format = ESSBSocialFollowersCounterHelper::get_option ( 'format' );
		
		$result = "";
		
		switch ($format) {
			case 'full' :
				$result = number_format ( $count, 0, '', ',' );
				break;
			case 'fulldot' :
				$result = number_format ( $count, 0, '', '.' );
				break;
			case 'short' :
				$result = self::followers_number_shorten ( $count );
				break;
			default :
				$result = $count;
				break;
		}
		
		return $result;
	}
	
	public static function followers_number_shorten($count) {
		if (! is_numeric ( $count ))
			return $count;
		
		if ($count >= 1000000) {
			return round ( ($count / 1000) / 1000, 1 ) . "M";
		} elseif ($count >= 100000) {
			return round ( $count / 1000, 0 ) . "k";
		} else if ($count >= 1000) {
			return round ( $count / 1000, 1 ) . "k";
		} else {
			return @number_format ( $count );
		}
	}
	
	/**
	 * draw_followers
	 *
	 * Display instance of generated followers counter
	 *
	 * @param $options array       	
	 * @param $draw_title boolean       	
	 * @since 3.4
	 */
	public static function draw_followers($options, $draw_title = false) {
		$hide_title = isset ( $options ['hide_title'] ) ? $options ['hide_title'] : 0;
		if (intval ( $hide_title ) == 1) {
			$draw_title = false;
		}
		
		$instance_title = isset ( $options ['title'] ) ? $options ['title'] : '';
		$instance_new_window = isset ( $options ['new_window'] ) ? $options ['new_window'] : 0;
		$instance_nofollow = isset ( $options ['nofollow'] ) ? $options ['nofollow'] : 0;
		$instance_show_total = isset ( $options ['show_total'] ) ? $options ['show_total'] : 0;
		$instance_total_type = isset ( $options ['total_type'] ) ? $options ['total_type'] : 'button_single';
		$instance_columns = isset ( $options ['columns'] ) ? $options ['columns'] : 3;
		$instance_template = isset ( $options ['template'] ) ? $options ['template'] : 'flat';
		$instance_animation = isset ( $options ['animation'] ) ? $options ['animation'] : '';
		$instance_bgcolor = isset ( $options ['bgcolor'] ) ? $options ['bgcolor'] : '';
		$instance_nospace = isset ( $options ['nospace'] ) ? $options ['nospace'] : 0;
		
		// compatibility with previous template slugs
		if (!empty($instance_template)) {
			if ($instance_template == "lite") {
				$instance_template = "light";
			}
			if ($instance_template == "grey-transparent") {
				$instance_template = "grey";
			}
			if ($instance_template == "color-transparent") {
				$instance_template = "color";
			}
		}
		
		$class_template = (! empty ( $instance_template )) ? " essbfc-template-" . $instance_template : '';
		$class_animation = (! empty ( $instance_animation )) ? " essbfc-icon-" . $instance_animation : '';
		$class_columns = (! empty ( $instance_columns )) ? " essbfc-col-" . $instance_columns : '';
		$class_nospace = (intval ( $instance_nospace ) == 1) ? " essbfc-nospace" : "";
		
		$style_bgcolor = (! empty ( $instance_bgcolor )) ? ' style="background-color:' . $instance_bgcolor . ';"' : '';
		
		$link_nofollow = (intval ( $instance_nofollow ) == 1) ? ' rel="nofollow"' : '';
		$link_newwindow = (intval ( $instance_new_window ) == 1) ? ' target="_blank"' : '';
		
		// loading animations
		if (! empty ( $class_animation )) {
			essb_resource_builder ()->add_static_footer_css ( ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/css/hover.css', 'essb-social-followers-counter-animations', 'css' );
		}
		
		// followers main element
		printf ( '<div class="essbfc-container%1$s%2$s%3$s%5$s"%4$s>', '', $class_columns, $class_template, $style_bgcolor, $class_nospace );
		
		if ($draw_title && ! empty ( $instance_title )) {
			printf ( '<h3>%1$s</h3>', $instance_title );
		}
		
		// get current state of followers counter
		$followers_count = essb_followers_counter ()->get_followers ();
		
		$display_total = (intval ( $instance_show_total ) == 1) ? true : false;
		$total_followers = 0;
		if ($display_total) {
			foreach ( $followers_count as $network => $count ) {
				if (intval ( $count ) > 0) {
					$total_followers += intval ( $count );
				}
			}
		}
		
		if ($display_total && $instance_total_type == "text_before") {
			printf ( '<div class="essbfc-totalastext">%1$s %2$s</div>', self::followers_number ( $total_followers ), ESSBSocialFollowersCounterHelper::get_option ( 'total_text' ) );
		}
		
		echo '<ul>';
		
		foreach ( essb_followers_counter ()->active_social_networks () as $social ) {
			$social_followers_text = ESSBSocialFollowersCounterHelper::get_option ( $social . '_text' );
			$social_followers_counter = isset ( $followers_count [$social] ) ? $followers_count [$social] : 0;
			
			$social_display = $social;
			if ($social_display == "instgram") {
				$social_display = "instagram";
			}
			
			printf ( '<li class="essbfc-%1$s">', $social_display );
			
			$follow_url = essb_followers_counter ()->create_follow_address ( $social );
			if (! empty ( $follow_url )) {
				printf ( '<a href="%1$s"%2$s%3$s>', $follow_url, $link_newwindow, $link_nofollow );
			}
			
			echo '<div class="essbfc-network">';
			printf ( '<i class="essbfc-icon essbfc-icon-%1$s%2$s"></i>', $social_display, $class_animation );
			printf ( '<span class="essbfc-followers-count">%1$s</span>', self::followers_number ( $social_followers_counter ) );
			printf ( '<span class="essbfc-followers-text">%1$s</span>', $social_followers_text );
			echo '</div>';
			
			if (! empty ( $follow_url )) {
				echo '</a>';
			}
			echo '</li>';
		}
		
		if ($display_total && $instance_total_type == "button_single") {
			$social = 'total';
			printf ( '<li class="essbfc-%1$s">', $social );
			echo '<div class="essbfc-network">';
			printf ( '<i class="essbfc-icon  essbfc-icon-%1$s%2$s"></i>', $social, $class_animation );
			printf ( '<span class="essbfc-followers-count">%1$s</span>', self::followers_number ( $total_followers ) );
			printf ( '<span class="essbfc-followers-text">%1$s</span>', ESSBSocialFollowersCounterHelper::get_option ( 'total_text' ) );
			echo '</div>';
			echo '</li>';
		}
		
		echo '</ul>';
		
		if ($display_total && $instance_total_type == "text_after") {
			printf ( '<div class="essbfc-totalastext">%1$s %2$s</div>', self::followers_number ( $total_followers ), ESSBSocialFollowersCounterHelper::get_option ( 'total_text' ) );
		}
		
		echo '</div>';
		// followers: end
	}

}