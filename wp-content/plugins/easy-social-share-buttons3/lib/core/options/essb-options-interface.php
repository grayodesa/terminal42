<?php

class ESSBOptionsInterface {
	
	public static function draw_form_start($custom = false, $group = '') {
		global $_REQUEST, $current_tab, $essb_options;
		
		$active_section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
		$active_subsection = isset($_REQUEST['subsection']) ? $_REQUEST['subsection'] : '';
		
		$active_section = sanitize_text_field($active_section);
		$active_subsection = sanitize_text_field($active_subsection);
		
		$admin_template = ESSBOptionValuesHelper::options_value($essb_options, 'admin_template');
		if (!empty($admin_template)) {
			$admin_template = "essb-template-".$admin_template;
		}
		
		echo '<div id="essb-scroll-top"></div>';
		echo '<form id="essb_options_form" enctype="multipart/form-data" method="post" action="">';
		if ($custom && !empty($group)) {
			settings_fields( $group );
		}
		else {
			settings_fields( 'essb_settings_group' );
		}
		echo '<input id="section" name="section" type="hidden" value="'.sanitize_text_field($active_section).'"/>';
		echo '<input id="subsection" name="subsection" type="hidden" value="'.sanitize_text_field($active_subsection).'"/>';
		echo '<input id="tab" name="tab" type="hidden" value="'.sanitize_text_field($current_tab).'"/>';
		echo '<div class="essb-options '.$admin_template.'" id="essb-options">';
	}
	
	public static function draw_header($title = '', $hide_update_button = false, $wizard_tab = false) {
		if ($hide_update_button) {
			echo '<div class="essb-options-header" id="essb-options-header">
			<div class="essb-options-title">
			' . $title . '
			</div>
			<a href="#" text="Back to top" class="button button-essb essb-button-backtotop">' . __ ( 'Back To Top', ESSB3_TEXT_DOMAIN ) . '</a>
			
			</div>';
		
		} 
		else {
			$update_button_text = __('Update Settings', ESSB3_TEXT_DOMAIN);
			$next_prev_buttons = "";
			if ($wizard_tab) {
				$update_button_text = __('Save Settings', ESSB3_TEXT_DOMAIN);
				$next_prev_buttons = '<a name="prevbutton" id="prevbutton" class="button essb-wizard-prev">< Previous</a>&nbsp;<a name="nextbutton" id="nextbutton" class="button essb-wizard-next">Next ></a>&nbsp;&nbsp;&nbsp;';
			}
			
			echo '<div class="essb-options-header" id="essb-options-header">
				<div class="essb-options-title">
			  	' . $title . '<span class="essb-options-subtitle"></span>
				</div>		
				<a href="#" text="Back to top" class="button button-essb essb-button-backtotop">' . __ ( 'Back To Top', ESSB3_TEXT_DOMAIN ) . '</a>
				'.$next_prev_buttons.'
				<input type="Submit" name="Submit" value="' . $update_button_text . '" class="button-primary" />				
			</div>';
		}
	}
	
	public static function draw_sidebar($options = array()) {
		
		echo '<div class="essb-options-sidebar" id="essb-options-sidebar">';

		echo '<ul class="essb-options-group-menu" id="sticky-navigation">';
		
		foreach ($options as $single) {
			$type = $single['type'];
			$field_id = isset($single['field_id']) ? $single['field_id'] : '';
			$title = isset($single['title']) ? $single['title'] : '';
			$sub_menuaction = isset($single['action']) ? $single['action'] : '';
			$default_child = isset($single['default_child']) ? $single['default_child'] : '';
			$icon = isset($single['icon']) ? $single['icon'] : '';
			
			$level2 = isset($single['level2']) ? $single['level2'] : '';
			
			if ($icon == 'default') {
				$icon = 'gear';
			}
			
			if ($level2 == 'true') {
				$icon = 'circle essb-navigation-small-icon';
			}
			
			if ($icon != '') {
				$icon = sprintf('<i class="essb-sidebar-icon fa fa-%1$s"></i>', $icon);
			}
			
			$css_class = "";
			switch ($type) {
				case "menu_item":
					$css_class = "essb-menu-item";
					
					if ($sub_menuaction == "activate_first") {
						$css_class .= " essb-activate-first";
					}
					break;
				case "sub_menu_item":
					$css_class = "essb-submenu-item";
					
					if ($sub_menuaction == 'menu') {
						$css_class .= " essb-submenu-menuitem";
					}
					
					if ($level2 == 'true') {
						$css_class .= " level2";
					}
					
					if ($level2 != 'title') {
						$css_class .= ' essb-submenu-with-action';
					}
					if ($level2 == 'title') {
						$css_class .= ' essb-submenu-title';
					}
					
					break;
				case "heading":
					$css_class = "essb-title";
					break;
				default:
					$css_class = "essb-menu-item";
					break;
			}
			
			printf('<li class="%1$s" data-menu="%2$s" data-activate-child="%4$s" id="essb-menu-%2$s"><a href="#">%5$s%3$s</a></li>', $css_class, $field_id, $title, $default_child, $icon);
		}
		
		echo '</ul>';
		
		echo '</div>';
		
	}
	
	public static function draw_content($options = array(), $custom = false, $user_settings = array()) {
		echo '<div class="essb-options-container" style="min-height: 840px;">';
		
		//print_r($options);
		
		foreach($options as $section => $fields) {
			printf('<div id="essb-container-%1$s" class="essb-data-container">',$section);
									
			echo '<table border="0" cellpadding="5" cellspacing="0" width="100%">
						<col width="25%" />
						<col width="75%" />';
			
			$section_options = $fields;
			
			ESSBOptionsFramework::reset_row_status();
			
			foreach ($section_options as $option) {
				ESSBOptionsFramework::draw_options_field($option, $custom, $user_settings);
			}
			
			echo '</table>';
			
			if (count(ESSBOptionsFramework::$heading_navigations) > 1) {
				echo '<div class="essb-internal-navigation">';
				echo '<div class="essb-internal-navigation-title">Quick Navigate <a class="essb-internal-navigation-close" href="#"></a></div>';
				echo '<div class="essb-internal-navigation-inner">';
				foreach (ESSBOptionsFramework::$heading_navigations as $navigation_item) {
					echo '<a href="#'.$navigation_item['id'].'" data-goto="'.$navigation_item['id'].'" class="essb-internal-navigation-item">'.$navigation_item['title'].'</a>';
				}
				echo '</div></div>';
			}
			
			echo '</div>';
		}
		
		echo '</div>';
	}	
	
	public static function draw_form_end() {
		echo '</div>';
		echo '</form>';
	}
	
}

?>