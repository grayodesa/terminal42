<?php

/**
 * Generate predefined CSS and javascript code snippets based on settings
 *
 */

define('ESSB_RESOURCE_BUILDER_FOLDER', ESSB3_PLUGIN_ROOT . 'lib/core/resource-snippets/');

class ESSBResourceBuilderSnippets {
	
	public static $snippet = array();
	
	public static $snippet_builder;
	
	
	/*
	 * Code pattern snippet builder
	 */
	public static function snippet_start_old() {
		ESSBResourceBuilderSnippets::$snippet = array();
	}
	
	public static function snippet_add_old($code) {
		ESSBResourceBuilderSnippets::$snippet[] = $code;
	}
	
	public static function snippet_end_old() {
		$output = '';
		foreach (ESSBResourceBuilderSnippets::$snippet as $line) {
			$output .= $line;
		}
		return $output;
	}
	
	public static function snippet_start() {
		self::$snippet_builder = '';
	}
	
	public static function snippet_add($code) {
		self::$snippet_builder .= $code;
	}
	
	public static function snippet_end() {
		return self::$snippet_builder;
	}
	
	/*
	 * end: Code pattern snippet builder
	 */
	
	/*
	 * --------------------------------------------------------------
	 * CSS
	 * --------------------------------------------------------------
	 */
	
	public static function css_build_animation_code($animation) {
		self::snippet_start();
		
		$singleTransition = '.essb_links a { -webkit-transition: all 0.2s linear;-moz-transition: all 0.2s linear;-ms-transition: all 0.2s linear;-o-transition: all 0.2s linear;transition: all 0.2s linear;}';
		self::snippet_add($singleTransition);
		
		switch ($animation) {
			case "pop":
				self::snippet_add('.essb_links a:hover {transform: translateY(-5px);-webkit-transform:translateY(-5px);-moz-transform:translateY(-5px);-o-transform:translateY(-5px); }');
				break;
			case "zoom":
				self::snippet_add('.essb_links a:hover {transform: scale(1.2);-webkit-transform:scale(1.2);-moz-transform:scale(1.2);-o-transform:scale(1.2); }');
				break;
			case "flip":
				self::snippet_add('.essb_links a:hover {transform: rotateZ(360deg);-webkit-transform:rotateZ(360deg);-moz-transform:rotateZ(360deg);-o-transform:rotateZ(360deg); }');
				break;		
			case "pop-right":
				self::snippet_add('.essb_links a:hover { padding-left: 30px !important; }');
				break;
			case "pop-left":
				self::snippet_add('.essb_links a:hover { padding-right: 30px !important; }');
				break;
			case "pop-both":
				self::snippet_add('.essb_links a:hover { padding-left: 30px !important; padding-right: 30px !important; }');
				break;
		}		
		return self::snippet_end();
	}
	
	public static function css_build_postbar_customizations() {
		if (!function_exists('essb_rs_css_build_postbar_customizations')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_postbar_customizations.php');
		}
		
		return essb_rs_css_build_postbar_customizations();
	}
	
	public static function css_build_counter_style() {
		global $essb_options;
		self::snippet_start();
		$options = $essb_options;
		$activate_total_counter_text = isset($options['activate_total_counter_text']) ? $options['activate_total_counter_text'] : '';
		
		if ($activate_total_counter_text != '') {
			self::snippet_add('.essb_links_list li.essb_totalcount_item .essb_t_l_big .essb_t_nb:after, .essb_links_list li.essb_totalcount_item .essb_t_r_big .essb_t_nb:after { '.
					'color: #777777;'.
					'content: "'.$activate_total_counter_text.'";'.
					'display: block;'.
					'font-size: 11px;'.
					'font-weight: normal;'.
					'text-align: center;'.
					'text-transform: uppercase;'.
					'margin-top: -5px; } ');
			
			self::snippet_add('.essb_links_list li.essb_totalcount_item .essb_t_l_big, .essb_links_list li.essb_totalcount_item .essb_t_r_big { text-align: center; }');
			self::snippet_add('.essb_displayed_sidebar .essb_links_list li.essb_totalcount_item .essb_t_l_big .essb_t_nb:after, .essb_displayed_sidebar .essb_links_list li.essb_totalcount_item .essb_t_r_big .essb_t_nb:after { '.					
					'margin-top: 0px; } ');
			self::snippet_add('.essb_displayed_sidebar_right .essb_links_list li.essb_totalcount_item .essb_t_l_big .essb_t_nb:after, .essb_displayed_sidebar_right .essb_links_list li.essb_totalcount_item .essb_t_r_big .essb_t_nb:after { '.					
					'margin-top: 0px; } ');
		}
		
		self::snippet_add('.essb_totalcount_item_before, .essb_totalcount_item_after { display: block !important; }');
		self::snippet_add('.essb_totalcount_item_before .essb_totalcount, .essb_totalcount_item_after .essb_totalcount { border: 0px !important; }');
		self::snippet_add('.essb_counter_insidebeforename { margin-right: 5px; font-weight: bold; }');
		return self::snippet_end();
	}
	
	public static function css_build_morepopup_css() {
		if (!function_exists('essb_rs_css_build_morepopup_css')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_morepopup_css.php');
		}
		
		return essb_rs_css_build_morepopup_css();
	}
	
	public static function css_build_generate_column_width() {
		self::snippet_start();
		
		self::snippet_add('.essb_width_columns_1 li { width: 100%; }');
		self::snippet_add('.essb_width_columns_1 li a { width: 92%; }');

		self::snippet_add('.essb_width_columns_2 li { width: 49%; }');
		self::snippet_add('.essb_width_columns_2 li a { width: 86%; }');

		self::snippet_add('.essb_width_columns_3 li { width: 32%; }');
		self::snippet_add('.essb_width_columns_3 li a { width: 80%; }');
		
		self::snippet_add('.essb_width_columns_4 li { width: 24%; }');
		self::snippet_add('.essb_width_columns_4 li a { width: 70%; }');		

		self::snippet_add('.essb_width_columns_5 li { width: 19.5%; }');
		self::snippet_add('.essb_width_columns_5 li a { width: 60%; }');

		self::snippet_add('.essb_width_columns_6 li { width: 16%; }');
		self::snippet_add('.essb_width_columns_6 li a { width: 55%; }');
		
		self::snippet_add('.essb_links li.essb_totalcount_item_before, .essb_width_columns_1 li.essb_totalcount_item_after { width: 100%; text-align: left; }');
		
		self::snippet_add('.essb_network_align_center a { text-align: center; }');
		self::snippet_add('.essb_network_align_right .essb_network_name { float: right;}');
		
		return self::snippet_end();
	}
	
	public static function css_build_sidebar_options() {
		global $essb_options;
		
		$custom_sidebarpos = ESSBOptionValuesHelper::options_value($essb_options, 'sidebar_fixedtop');
		$custom_appearance_pos = ESSBOptionValuesHelper::options_value($essb_options, 'sidebar_leftright_percent');
		
		self::snippet_start();
		
		if ($custom_sidebarpos != '') {
			self::snippet_add('.essb_displayed_sidebar_right, .essb_displayed_sidebar { top: '.$custom_sidebarpos.' !important;}');
		}
		if ($custom_appearance_pos != '') {
			self::snippet_add('.essb_displayed_sidebar_right, .essb_displayed_sidebar { display: none; -webkit-transition: all 0.5s; -moz-transition: all 0.5s;-ms-transition: all 0.5s;-o-transition: all 0.5s;transition: all 0.5s;}');
		}
		
		return self::snippet_end();
	}
	
	public static function css_build_generate_align_code() {
		self::snippet_start();
		
		self::snippet_add('.essb_links_right { text-align: right; }');
		self::snippet_add('.essb_links_center { text-align: center; }');
		self::snippet_add('.essb_hide_icon .essb_icon { display: none !important; }');
		
		return self::snippet_end();
	}
	
	public static function css_build_fixedwidth_button($salt, $width, $align) {
		self::snippet_start();

		$main_class = sprintf('essb_fixedwidth_%1$s', $width.'_'.$align);
		
		self::snippet_add(sprintf('.%1$s a { width: %2$spx;}', $main_class, $width));
		if ($align == '') {
			self::snippet_add(sprintf('.%1$s a { text-align: center;}', $main_class));
		}
		if ($align == 'right') {
			self::snippet_add(sprintf('.%1$s .essb_network_name { float: right;}', $main_class));
		}		
		
		return self::snippet_end();
	}
	
	public static function css_build_fullwidth_button($button_width, $buttons_correction_width, $container_width) {
		$main_class = 'essb_fullwidth_'.$button_width.'_'.$buttons_correction_width.'_'.$container_width;
		
		self::snippet_start();
		
		self::snippet_add(sprintf('.%1$s { width: %2$s;}', $main_class, $container_width.'%'));
		self::snippet_add(sprintf('.%1$s .essb_links_list { width: %2$s;}', $main_class, '100%'));
		self::snippet_add(sprintf('.%1$s li { width: %2$s;}', $main_class, $button_width.'%'));
		self::snippet_add(sprintf('.%1$s li.essb_totalcount_item_before { width: %2$s;}', $main_class, '100%'));
		self::snippet_add(sprintf('.%1$s li a { width: %2$s;}', $main_class, $buttons_correction_width.'%'));
		
		return self::snippet_end();
	}
	
	public static function css_build_fullwidth_buttons($number_of_buttons, $container_width, $buttons_correction_width, $first_button, $second_button) {
		$button_width = intval($container_width) / intval($number_of_buttons);
		$button_width = floor($button_width);
				
		$main_class = 'essb_fullwidth_'.$button_width.'_'.$buttons_correction_width.'_'.$container_width;
		
		if (intval($first_button) != 0 || intval($second_button) != 0) {
			$recalc_count = intval($number_of_buttons);
			$recalc_container_width = intval($container_width);
			
			if (intval($first_button) != 0) {
				$recalc_count--;
				$recalc_container_width -= intval($first_button);
			}

			if (intval($second_button) != 0) {
				$recalc_count--;
				$recalc_container_width -= intval($second_button);
			}
			
			$button_width = intval($recalc_container_width) / intval($recalc_count);
			$button_width = floor($button_width);
		}
		
		self::snippet_start();
		
		self::snippet_add(sprintf('.%1$s { width: %2$s;}', $main_class, $container_width.'%'));
		self::snippet_add(sprintf('.%1$s .essb_links_list { width: %2$s;}', $main_class, '100%'));
		self::snippet_add(sprintf('.%1$s li { width: %2$s;}', $main_class, $button_width.'%'));
		self::snippet_add(sprintf('.%1$s li.essb_totalcount_item_before { width: %2$s;}', $main_class, '100%'));
		self::snippet_add(sprintf('.%1$s li a { width: %2$s;}', $main_class, $buttons_correction_width.'%'));

		if (intval($first_button) != 0) {
			self::snippet_add(sprintf('.%1$s li.essb_item_fw_first { width: %2$s;}', $main_class, $first_button.'%'));
		}
		if (intval($second_button) != 0) {
			self::snippet_add(sprintf('.%1$s li.essb_item_fw_second { width: %2$s;}', $main_class, $second_button.'%'));
		}
				
		return self::snippet_end();
	}
	
	public static function css_build_compile_display_locations_code() {
		global $essb_options;
		
		self::snippet_start();
		
		// topbar customizations
		$topbar_top_pos = isset($essb_options['topbar_top']) ? $essb_options['topbar_top'] : '';
		$topbar_top_loggedin = isset($essb_options['topbar_top_loggedin']) ? $essb_options['topbar_top_loggedin'] : '';
		
		$topbar_bg_color = isset($essb_options['topbar_bg']) ? $essb_options['topbar_bg'] : '';
		$topbar_bg_color_opacity = isset($essb_options['topbar_bg_opacity']) ? $essb_options['topbar_bg_opacity'] : '';
		$topbar_maxwidth = isset($essb_options['topbar_maxwidth']) ? $essb_options['topbar_maxwidth'] : '';
		$topbar_height = isset($essb_options['topbar_height']) ? $essb_options['topbar_height'] : '';
		$topbar_contentarea_width = isset($essb_options['topbar_contentarea_width']) ? $essb_options['topbar_contentarea_width'] : '';
		if ($topbar_contentarea_width == '' && ESSBOptionValuesHelper::options_bool_value($essb_options, 'topbar_contentarea')) {
			$topbar_contentarea_width = '30';
		}
		
		$topbar_top_onscroll = isset($essb_options['topbar_top_onscroll']) ? $essb_options['topbar_top_onscroll'] : '';
		
		if (is_user_logged_in() && $topbar_top_loggedin != '') {
			$topbar_top_pos = $topbar_top_loggedin;
		}
		
		if ($topbar_bg_color_opacity != '' && $topbar_bg_color == '') {
			$topbar_bg_color = '#ffffff';
		}
		
		if ($topbar_top_pos != '') {
			self::snippet_add(sprintf('.essb_topbar { top: %1$spx !important; }', $topbar_top_pos));
		}
		if ($topbar_bg_color != '') {
			if ($topbar_bg_color_opacity != '') {
				$topbar_bg_color = self::hex2rgba($topbar_bg_color, $topbar_bg_color_opacity);
			}
			self::snippet_add(sprintf('.essb_topbar { background: %1$s !important; }', $topbar_bg_color));
		}
		if ($topbar_maxwidth != '') {
			self::snippet_add(sprintf('.essb_topbar .essb_topbar_inner { max-width: %1$spx; margin: 0 auto; padding-left: 0px; padding-right: 0px;}', $topbar_maxwidth));
		}
		if ($topbar_height != '') {
			self::snippet_add(sprintf('.essb_topbar { height: %1$spx; }', $topbar_height));
		}
		if ($topbar_contentarea_width != '') {
			$topbar_contentarea_width = str_replace('%', '', $topbar_contentarea_width);
			$topbar_contentarea_width = intval($topbar_contentarea_width);
			
			$topbar_buttonarea_width = 100 - $topbar_contentarea_width;
			self::snippet_add(sprintf('.essb_topbar .essb_topbar_inner_buttons { width: %1$s; }', $topbar_buttonarea_width.'%'));
			self::snippet_add(sprintf('.essb_topbar .essb_topbar_inner_content { width: %1$s; }', $topbar_contentarea_width.'%'));
		}
		
		if ($topbar_top_onscroll != '') {
			self::snippet_add('.essb_topbar { margin-top: -200px; }');
		}
		
		// end: topbar customizations

		// bottombar customizations
		
		$topbar_bg_color = isset($essb_options['bottombar_bg']) ? $essb_options['bottombar_bg'] : '';
		$topbar_bg_color_opacity = isset($essb_options['bottombar_bg_opacity']) ? $essb_options['bottombar_bg_opacity'] : '';
		$topbar_maxwidth = isset($essb_options['bottombar_maxwidth']) ? $essb_options['bottombar_maxwidth'] : '';
		$topbar_height = isset($essb_options['bottombar_height']) ? $essb_options['bottombar_height'] : '';
		$topbar_contentarea_width = isset($essb_options['bottombar_contentarea_width']) ? $essb_options['bottombar_contentarea_width'] : '';
		if ($topbar_contentarea_width == '' && ESSBOptionValuesHelper::options_bool_value($essb_options, 'bottombar_contentarea')) {
			$topbar_contentarea_width = '30';
		}
		
		$topbar_top_onscroll = isset($essb_options['bottombar_top_onscroll']) ? $essb_options['bottombar_top_onscroll'] : '';
				
		if ($topbar_bg_color_opacity != '' && $topbar_bg_color == '') {
			$topbar_bg_color = '#ffffff';
		}
		
		if ($topbar_bg_color != '') {
			if ($topbar_bg_color_opacity != '') {
				$topbar_bg_color = self::hex2rgba($topbar_bg_color, $topbar_bg_color_opacity);
			}
			self::snippet_add(sprintf('.essb_bottombar { background: %1$s !important; }', $topbar_bg_color));
		}
		if ($topbar_maxwidth != '') {
			self::snippet_add(sprintf('.essb_bottombar .essb_bottombar_inner { max-width: %1$spx; margin: 0 auto; padding-left: 0px; padding-right: 0px;}', $topbar_maxwidth));
		}
		if ($topbar_height != '') {
			self::snippet_add(sprintf('.essb_bottombar { height: %1$spx; }', $topbar_height));
		}
		if ($topbar_contentarea_width != '') {
			$topbar_contentarea_width = str_replace('%', '', $topbar_contentarea_width);
			$topbar_contentarea_width = intval($topbar_contentarea_width);
				
			$topbar_buttonarea_width = 100 - $topbar_contentarea_width;
			self::snippet_add(sprintf('.essb_bottombar .essb_bottombar_inner_buttons { width: %1$s; }', $topbar_buttonarea_width.'%'));
			self::snippet_add(sprintf('.essb_bottombar .essb_bottombar_inner_content { width: %1$s; }', $topbar_contentarea_width.'%'));
		}
		
		if ($topbar_top_onscroll != '') {
			self::snippet_add('.essb_bottombar { margin-bottom: -200px; }');
		}
		
		// end: bottombar customizations
		
		// float from top customizations
		$top_pos = isset($essb_options['float_top']) ? $essb_options['float_top'] : '';
		$float_top_loggedin = isset($essb_options['float_top_loggedin']) ? $essb_options['float_top_loggedin'] : '';
		
		$bg_color = isset($essb_options['float_bg']) ? $essb_options['float_bg'] : '';
		$bg_color_opacity = isset($essb_options['float_bg_opacity']) ? $essb_options['float_bg_opacity'] : '';
		$float_full = isset($essb_options['float_full']) ? $essb_options['float_full'] : '';
		$float_remove_margin = isset($essb_options['float_remove_margin']) ? $essb_options['float_remove_margin'] : '';
		$float_full_maxwidth = isset($essb_options['float_full_maxwidth']) ? $essb_options['float_full_maxwidth'] : '';
		
		if (is_user_logged_in() && $float_top_loggedin != '') {
			$top_pos = $float_top_loggedin;
		}
		
		if ($bg_color_opacity != '' && $bg_color == '') {
			$bg_color = '#ffffff';
		}
		
		if ($top_pos != '') {
			self::snippet_add(sprintf('.essb_fixed { top: %1$spx !important; }', $top_pos));
		}
		if ($bg_color != '') {
			if ($bg_color_opacity != '') {
				$bg_color = self::hex2rgba($bg_color, $bg_color_opacity);
			}
			self::snippet_add(sprintf('.essb_fixed { background: %1$s !important; }', $bg_color));
		}
		
		if ($float_full == 'true') {
			self::snippet_add('.essb_fixed { left: 0; width: 100%; min-width: 100%; padding-left: 10px; }');
		}
		if ($float_remove_margin == 'true') {
			self::snippet_add('.essb_fixed { margin: 0px !important; }');
		}
		
		if ($float_full_maxwidth != '') {
			self::snippet_add(sprintf('.essb_fixed.essb_links ul { max-width: %1$spx; margin: 0 auto !important; } .essb_fixed { padding-left: 0px; }', $float_full_maxwidth));
		}
		// end: float from top
		
		// postfloat
		
		$postfloat_marginleft = ESSBOptionValuesHelper::options_value($essb_options, 'postfloat_marginleft');
		$postfloat_margintop = ESSBOptionValuesHelper::options_value($essb_options, 'postfloat_margintop');
		$postfloat_top = ESSBOptionValuesHelper::options_value($essb_options, 'postfloat_top');
		$postfloat_percent = ESSBOptionValuesHelper::options_value($essb_options, 'postfloat_percent');
		$postfloat_initialtop = ESSBOptionValuesHelper::options_value($essb_options, 'postfloat_initialtop');
		
		if ($postfloat_marginleft != '') {
			self::snippet_add(sprintf('.essb_displayed_postfloat { margin-left: %1$spx !important; }', $postfloat_marginleft));
		}
		if ($postfloat_margintop != '') {
			self::snippet_add(sprintf('.essb_displayed_postfloat { margin-top: %1$spx !important; }', $postfloat_margintop));
		}
		if ($postfloat_top != '') {
			self::snippet_add(sprintf('.essb_displayed_postfloat.essb_postfloat_fixed { top: %1$spx !important; }', $postfloat_top));
		}
		if ($postfloat_initialtop != '') {
			self::snippet_add(sprintf('.essb_displayed_postfloat { top: %1$spx !important; }', $postfloat_initialtop));
		}
		if ($postfloat_percent != '') {
			self::snippet_add('.essb_displayed_postfloat { opacity: 0; }');			
		}
		
		// end: postfloat
		
		return self::snippet_end();
	}
	
	public static function hex2rgba($color, $opacity = false) {
	
		$default = 'rgb(0,0,0)';
	
		//Return default if no color provided
		if(empty($color))
			return $default;
	
		//Sanitize $color if "#" is provided
		if ($color[0] == '#' ) {
			$color = substr( $color, 1 );
		}
	
		//Check if color has 6 or 3 characters and get values
		if (strlen($color) == 6) {
			$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
		} elseif ( strlen( $color ) == 3 ) {
			$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
		} else {
			return $default;
		}
	
		//Convert hexadec to rgb
		$rgb =  array_map('hexdec', $hex);
	
		//Check if opacity is set(rgba or rgb)
		if($opacity){
			if(abs($opacity) > 1)
				$opacity = 1.0;
			$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
		} else {
			$output = 'rgb('.implode(",",$rgb).')';
		}
	
		//Return rgb(a) color string
		return $output;
	}
	
	public static function css_build_customizer() {
		global $post, $essb_options, $essb_networks;
		
		$options = $essb_options;
	
		$is_active = ESSBOptionValuesHelper::options_bool_value($essb_options, 'customizer_is_active');
	
		if (isset ( $post )) {
			$post_activate_customizer = get_post_meta ( $post->ID, 'essb_post_customizer', true );
				
			if ($post_activate_customizer != '') {
				if ($post_activate_customizer == 'yes') {
					$is_active = true;
				} else {
					$is_active = false;
				}
			}
		}
	
		self::snippet_start();
		if ($is_active) {
			if (!function_exists('essb_rs_css_build_customizer')) {
				include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_customizer.php');
			}
			
			self::snippet_add(essb_rs_css_build_customizer());
		}
		
		$is_active_subscribe = ESSBOptionValuesHelper::options_bool_value($essb_options, 'activate_mailchimp_customizer');
		if ($is_active_subscribe) {
			if (!function_exists('essb_rs_css_build_customizer_mailchimp')) {
				include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_customizer_mailchimp.php');
			}
				
			self::snippet_add(essb_rs_css_build_customizer_mailchimp());
		}
	
		$global_user_defined_css = isset ( $options ['customizer_css'] ) ? $options ['customizer_css'] : '';
		$global_user_defined_css = stripslashes ( $global_user_defined_css );
	
		if ($global_user_defined_css != '') {			
			self::snippet_add($global_user_defined_css);
		}

		
		return self::snippet_end();
	
	}
	
	public static function css_build_followerscounter_customizer() {
		if (!function_exists('essb_rs_css_build_followerscounter_customizer')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_followerscounter_customizer.php');
		}
		
		return essb_rs_css_build_followerscounter_customizer();
	}
	
	public static function css_build_imageshare_customizer() {
		if (!function_exists('essb_rs_css_build_imageshare_customizer')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_imageshare_customizer.php');
		}
		
		return essb_rs_css_build_imageshare_customizer();
	}
	
	public static function css_build_footer_css() {
		global $essb_options;
		
		self::snippet_start();
		
		$global_user_defined_css = isset ( $essb_options ['customizer_css_footer'] ) ? $essb_options ['customizer_css_footer'] : '';
		$global_user_defined_css = stripslashes ( $global_user_defined_css );
		
		if ($global_user_defined_css != '') {
			self::snippet_add($global_user_defined_css);
		}
		
		return self::snippet_end();
	}
	
	public static function css_build_mobile_compatibility() {
		if (!function_exists('essb_rs_css_build_mobile_compatibility')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_mobile_compatibility.php');
		}
		
		return essb_rs_css_build_mobile_compatibility();
	}
	
	public static function css_build_mobilesharebar_fix_code() {
		if (!function_exists('essb_rs_css_build_mobilesharebar_fix_code')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_css_build_mobilesharebar_fix_code.php');
		}
		
		return essb_rs_css_build_mobilesharebar_fix_code();
	}
	
	/*
	 * -----------------------------------------------------------------
	 * Javascript
	 * -----------------------------------------------------------------
	 */
	
	public static function js_build_admin_ajax_access_code() {
		global $essb_options;
		
		$code_options = array();
		$code_options['ajax_url'] = admin_url ('admin-ajax.php');
		$code_options['essb3_nonce'] = wp_create_nonce('essb3_ajax_nonce');
		$code_options['essb3_plugin_url'] = ESSB3_PLUGIN_URL;
		$code_options['essb3_facebook_total'] = ESSBOptionValuesHelper::options_bool_value($essb_options, 'facebooktotal');
		$code_options['essb3_admin_ajax'] = ESSBOptionValuesHelper::options_bool_value($essb_options, 'force_counters_admin');
		$code_options['essb3_internal_counter'] = ESSBOptionValuesHelper::options_bool_value($essb_options, 'active_internal_counters');
		$code_options['essb3_stats'] = ESSBOptionValuesHelper::options_bool_value($essb_options, 'stats_active');
		$code_options['essb3_ga'] = ESSBOptionValuesHelper::options_bool_value($essb_options, 'activate_ga_tracking');
		$code_options['essb3_ga_mode'] = ESSBOptionValuesHelper::options_value($essb_options, 'ga_tracking_mode');
		$code_options['essb3_counter_button_min'] = intval(ESSBOptionValuesHelper::options_value($essb_options, 'button_counter_hidden_till'));
		$code_options['essb3_counter_total_min'] = intval(ESSBOptionValuesHelper::options_value($essb_options, 'total_counter_hidden_till'));
		$code_options['blog_url'] = get_site_url().'/';
		$code_options['ajax_type'] = ESSBOptionValuesHelper::options_value($essb_options, 'force_counters_admin_type');
		$code_options['essb3_postfloat_stay'] = ESSBOptionValuesHelper::options_bool_value($essb_options, 'postfloat_always_visible');
		$code_options['essb3_no_counter_mailprint'] = ESSBOptionValuesHelper::options_bool_value($essb_options, 'deactive_internal_counters_mail');
		$code_options['essb3_single_ajax'] = ESSBOptionValuesHelper::options_bool_value($essb_options, 'force_counters_admin_single');
		$code_options['twitter_counter'] = ESSBOptionValuesHelper::options_value($essb_options, 'twitter_counters'); 
		$code_options['post_id'] = get_the_ID();
		
		$postfloat_top = ESSBOptionValuesHelper::options_value($essb_options, 'postfloat_top');
		if (!empty($postfloat_top)) {
			$code_options['postfloat_top'] = $postfloat_top;
		}
		
		$hide_float_from_top = ESSBOptionValuesHelper::options_value($essb_options, 'float_top_disappear');
		if (!empty($hide_float_from_top)) {
			$code_options['hide_float'] = $hide_float_from_top;
		}
		$top_pos = isset($essb_options['float_top']) ? $essb_options['float_top'] : '';
		$float_top_loggedin = isset($essb_options['float_top_loggedin']) ? $essb_options['float_top_loggedin'] : '';
		if (is_user_logged_in() && $float_top_loggedin != '') {
			$top_pos = $float_top_loggedin;
		}
		if (!empty($top_pos)) {
			$code_options['float_top'] = $top_pos;
		}
		
		self::snippet_start();
		
		self::snippet_add(sprintf('var essb_settings = %1$s;', json_encode($code_options)));
		
		if (defined('ESSB3_CACHED_COUNTERS')) {
			if (ESSBGlobalSettings::$cached_counters_cache_mode) {
				$update_url = ESSBUrlHelper::get_current_page_url();
				self::snippet_add('var essb_buttons_exist = !!document.getElementsByClassName("essb_links"); if(essb_buttons_exist == true) { document.addEventListener("DOMContentLoaded", function(event) { var ESSB_CACHE_URL = "'.$update_url.'"; if(ESSB_CACHE_URL.indexOf("?") > -1) { ESSB_CACHE_URL += "&essb_counter_cache=rebuild"; } else { ESSB_CACHE_URL += "?essb_counter_cache=rebuild"; }; var xhr = new XMLHttpRequest(); xhr.open("GET",ESSB_CACHE_URL,true); xhr.send(); });}');
			}
		}
		
		return self::snippet_end();
	}
	
	public static function js_build_ga_tracking_code() {
		$script = '
		var essb_ga_tracking = function(oService, oPosition, oURL) {
				var essb_ga_type = essb_settings.essb3_ga_mode;
				
				if ( \'ga\' in window && window.ga !== undefined && typeof window.ga === \'function\' ) {
					if (essb_ga_type == "extended") {
						ga(\'send\', \'event\', \'social\', oService + \' \' + oPosition, oURL);
					}
					else {
						ga(\'send\', \'event\', \'social\', oService, oURL);
					}
				}
			};
		';
		
		return $script;
	}
	
	public static function js_build_window_print_code() {
		$script = '
var essb_print = function (oInstance) {	
	essb_tracking_only(\'\', \'print\', oInstance);
	window.print();
};
		';
		
		return $script;
	}
	
	public static function js_build_window_open_code() {
		$script = '
var essb_window = function(oUrl, oService, oInstance) {
	var element = jQuery(\'.essb_\'+oInstance);
	var instance_post_id = jQuery(element).attr("data-essb-postid") || "";
	var instance_position = jQuery(element).attr("data-essb-position") || "";
	var wnd;
	var w = 800 ; var h = 500;
	if (oService == "twitter") { 
		w = 500; h= 300; 
	} 
	var left = (screen.width/2)-(w/2); 
	var top = (screen.height/2)-(h/2); 
	
	if (oService == "twitter") { 
		wnd = window.open( oUrl, "essb_share_window", "height=300,width=500,resizable=1,scrollbars=yes,top="+top+",left="+left ); 
	}  
	else { 
		wnd = window.open( oUrl, "essb_share_window", "height=500,width=800,resizable=1,scrollbars=yes,top="+top+",left="+left ); 
	} 
	
	if (typeof(essb_settings) != "undefined") {
		if (essb_settings.essb3_stats) {
			if (typeof(essb_handle_stats) != "undefined") {
				essb_handle_stats(oService, instance_post_id, oInstance);
			}
		}	

		if (essb_settings.essb3_ga) {
			essb_ga_tracking(oService, oUrl, instance_position);
		}
	}
	essb_self_postcount(oService, instance_post_id); 
	
	var pollTimer = window.setInterval(function() {
		if (wnd.closed !== false) { 
			window.clearInterval(pollTimer); 
			essb_smart_onclose_events(oService, instance_post_id);
		}
	}, 200);  
};

var essb_self_postcount = function(oService, oCountID) {
	if (typeof(essb_settings) != "undefined") {
		oCountID = String(oCountID);

		jQuery.post(essb_settings.ajax_url, {
			\'action\': \'essb_self_postcount\',
			\'post_id\': oCountID,
			\'service\': oService,
			\'nonce\': essb_settings.essb3_nonce
		}, function (data) { if (data) {
			
		}},\'json\');
	}	
};

var essb_smart_onclose_events = function(oService, oPostID) { 
	if (typeof (essbasc_popup_show) == \'function\') {   
		essbasc_popup_show(); 
	} 
	if (typeof essb_acs_code == \'function\') {   
		essb_acs_code(oService, oPostID); 
	} 
};

var essb_tracking_only = function(oUrl, oService, oInstance, oAfterShare) {
	var element = jQuery(\'.essb_\'+oInstance);
	
	if (oUrl == "") {
		oUrl = document.URL;
	}
	
	var instance_post_id = jQuery(element).attr("data-essb-postid") || "";
	var instance_position = jQuery(element).attr("data-essb-position") || "";

	if (typeof(essb_settings) != "undefined") {
		if (essb_settings.essb3_stats) {
			if (typeof(essb_handle_stats) != "undefined") {
				essb_handle_stats(oService, instance_post_id, oInstance);
			}
		}	

		if (essb_settings.essb3_ga) {
			essb_ga_tracking(oService, oUrl, instance_position);
		}
	}
	essb_self_postcount(oService, instance_post_id); 
	
	if (oAfterShare) {
		essb_smart_onclose_events(oService, instance_post_id);
	}	  	
};

var essb_pinterest_picker = function(oInstance) {
	essb_tracking_only(\'\', \'pinterest\', oInstance);
	var e=document.createElement(\'script\');
	e.setAttribute(\'type\',\'text/javascript\');
	e.setAttribute(\'charset\',\'UTF-8\');
	e.setAttribute(\'src\',\'//assets.pinterest.com/js/pinmarklet.js?r=\'+Math.random()*99999999);document.body.appendChild(e);	
};
		';
		
		return $script;
	}
	
	
	public static function js_build_generate_popup_mailform() {
		if (!function_exists('essb_rs_js_build_generate_popup_mailform')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_js_build_generate_popup_mailform.php');
		}
		
		return essb_rs_js_build_generate_popup_mailform();
	}
	
	public static function js_build_generate_more_button_inline() {
		if (!function_exists('essb_rs_js_build_generate_more_button_inline')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_js_build_generate_more_button_inline.php');
		}
		
		return essb_rs_js_build_generate_more_button_inline();
	}
	
	public static function js_build_generate_more_button_popup() {
		if (!function_exists('essb_rs_js_build_generate_more_button_popup')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_js_build_generate_more_button_popup.php');
		}
		
		return essb_rs_js_build_generate_more_button_popup();
	}
	
	public static function js_build_generate_sidebar_reveal_code() {
		if (!function_exists('essb_rs_js_build_generate_sidebar_reveal_code')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_js_build_generate_sidebar_reveal_code.php');
		}
		
		return essb_rs_js_build_generate_sidebar_reveal_code();
	}

	public static function js_build_generate_postfloat_reveal_code() {
		if (!function_exists('essb_rs_js_build_generate_postfloat_reveal_code')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_js_build_generate_postfloat_reveal_code.php');
		}
		
		return essb_rs_js_build_generate_postfloat_reveal_code();
	}
	

	public static function js_build_generate_topbar_reveal_code() {
		if (!function_exists('essb_rs_js_build_generate_topbar_reveal_code')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_js_build_generate_topbar_reveal_code.php');
		}
		
		return essb_rs_js_build_generate_topbar_reveal_code();
	}
	
	public static function js_build_generate_bottombar_reveal_code() {
		if (!function_exists('essb_rs_js_build_generate_bottombar_reveal_code')) {
			include_once (ESSB_RESOURCE_BUILDER_FOLDER . 'essb_rs_js_build_generate_bottombar_reveal_code.php');
		}
		
		return essb_rs_js_build_generate_bottombar_reveal_code();
	}	
}

?>