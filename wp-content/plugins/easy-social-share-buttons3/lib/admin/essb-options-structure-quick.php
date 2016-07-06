<?php
//---- quick setup wizard steps
ESSBOptionsStructureHelper::menu_item('quick', 'quick-1', __('1. Template', ESSB3_TEXT_DOMAIN), 'bolt');
ESSBOptionsStructureHelper::menu_item('quick', 'quick-2', __('2. Button Style', ESSB3_TEXT_DOMAIN), 'bolt');
ESSBOptionsStructureHelper::menu_item('quick', 'quick-3', __('3. Social Share Buttons', ESSB3_TEXT_DOMAIN), 'bolt');
ESSBOptionsStructureHelper::menu_item('quick', 'quick-4', __('4. Counters', ESSB3_TEXT_DOMAIN), 'bolt');
ESSBOptionsStructureHelper::menu_item('quick', 'quick-5', __('5. Display Buttons On', ESSB3_TEXT_DOMAIN), 'bolt');
ESSBOptionsStructureHelper::menu_item('quick', 'quick-6', __('6. Position Of Buttons', ESSB3_TEXT_DOMAIN), 'bolt');
ESSBOptionsStructureHelper::menu_item('quick', 'quick-7', __('7. Mobile', ESSB3_TEXT_DOMAIN), 'bolt');
ESSBOptionsStructureHelper::menu_item('quick', 'quick-8', __('8. Final Settings', ESSB3_TEXT_DOMAIN), 'bolt');

//------- wizard menu
ESSBOptionsStructureHelper::field_heading('quick', 'quick-1', 'heading1', __('1. Template', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('quick', 'quick-1', 'essb3_options_template_select', __('Template', ESSB3_TEXT_DOMAIN), __('This will be your default theme for site. You are able to select different theme for each post/page.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('quick', 'quick-1', 'css_animations', __('Activate animations', ESSB3_TEXT_DOMAIN), __('Animations
		are provided with CSS transitions and work on best with retina
		templates.', ESSB3_TEXT_DOMAIN), essb_available_animations());

ESSBOptionsStructureHelper::field_heading('quick', 'quick-2', 'heading1', __('2. Button Style', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_image_radio('quick', 'quick-2', 'button_style', __('Buttons Style', ESSB3_TEXT_DOMAIN), __('Select your default button style', ESSB3_TEXT_DOMAIN), essb_available_buttons_style());
ESSBOptionsStructureHelper::field_image_radio('quick', 'quick-2', 'button_pos', __('Buttons Align', ESSB3_TEXT_DOMAIN), __('Choose how buttons
									to be aligned. Default position is left but you can also select
									Right or Center', ESSB3_TEXT_DOMAIN), essb_available_buttons_align());
ESSBOptionsStructureHelper::field_switch('quick', 'quick-2', 'nospace', __('Remove spacing between buttons', ESSB3_TEXT_DOMAIN), __('Activate this option to remove default space between share buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_heading('quick', 'quick-2', 'heading2', __('Buttons width', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_image_radio('quick', 'quick-2', 'button_width', __('Width of buttons', ESSB3_TEXT_DOMAIN), __('Choose between automatic width, pre defined width or display in columns.', ESSB3_TEXT_DOMAIN), essb_available_buttons_width());
ESSBOptionsStructureHelper::field_section_start('quick', 'quick-2', __('Fixed width share buttons', ESSB3_TEXT_DOMAIN), __('Customize the fixed width options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('quick', 'quick-2', 'fixed_width_value', __('Custom buttons width', ESSB3_TEXT_DOMAIN), __('Provide custom width of button in pixels without the px symbol.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_select('quick', 'quick-2', 'fixed_width_align', __('Choose alignment of network name', ESSB3_TEXT_DOMAIN), __('Provide different alignment of network name, when fixed button width is activated. When counter position is Inside or Inside name, that alignment will be applied for the counter. Default value is center.', ESSB3_TEXT_DOMAIN), array("" => "Center", "left" => "Left", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_end('quick', 'quick-2');
ESSBOptionsStructureHelper::field_section_start('quick', 'quick-2', __('Full width share buttons', ESSB3_TEXT_DOMAIN), __('Full width option will make buttons to take the width of your post content area.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('quick', 'quick-2', 'fullwidth_share_buttons_correction', __('Max width of button on desktop', ESSB3_TEXT_DOMAIN), __('Provide custom width of single button when full width is active. This value is number in percents without the % symbol.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox('quick', 'quick-2', 'fullwidth_share_buttons_correction_mobile', __('Max width of button on mobile', ESSB3_TEXT_DOMAIN), __('Provide custom width of single button when full width is active. This value is number in percents without the % symbol.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox('quick', 'quick-2', 'fullwidth_share_buttons_container', __('Max width of buttons container element', ESSB3_TEXT_DOMAIN), __('If you wish to display total counter along with full width share buttons please provide custom max width of buttons container in percent without % (example: 90). Leave this field blank for default value of 100 (100%).', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_select('quick', 'quick-2', 'fullwidth_align', __('Choose alignment of network name', ESSB3_TEXT_DOMAIN), __('Provide different alignment of network name (counter when position inside or inside name). Default value is left.', ESSB3_TEXT_DOMAIN), array("left" => "Left", "center" => "Center", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_end('quick', 'quick-2');
ESSBOptionsStructureHelper::field_section_start('quick', 'quick-2', __('Display in columns'), '');
$listOfOptions = array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5");
ESSBOptionsStructureHelper::field_select('quick', 'quick-2', 'fullwidth_share_buttons_columns', __('Number of columns', ESSB3_TEXT_DOMAIN), __('Choose the number of columns that buttons will be displayed.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_select('quick', 'quick-2', 'fullwidth_share_buttons_columns_align', __('Choose alignment of network name', ESSB3_TEXT_DOMAIN), __('Provide different alignment of network name (counter when position inside or inside name). Default value is left.', ESSB3_TEXT_DOMAIN), array("" => "Left", "center" => "Center", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_end('quick', 'quick-2');

ESSBOptionsStructureHelper::field_heading('quick', 'quick-3', 'heading1', __('3. Social Share Buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('quick', 'quick-3', 'essb3_network_selection', __('Social Networks', ESSB3_TEXT_DOMAIN), __('Select networks that you wish to appear in your list. With drag and drop you can rearrange them.', ESSB3_TEXT_DOMAIN));
$more_options = array ("1" => "Display all active networks after more button", "2" => "Display all social networks as pop up", "3" => "Display only active social networks as pop up" );
ESSBOptionsStructureHelper::field_select('quick', 'quick-3', 'more_button_func', __('More button', ESSB3_TEXT_DOMAIN), __('Select networks that you wish to appear in your list. With drag and drop you can rearrange them.', ESSB3_TEXT_DOMAIN), $more_options);
$more_options = array ("plus" => "Plus icon", "dots" => "Dots icon" );
ESSBOptionsStructureHelper::field_select('quick', 'quick-3', 'more_button_icon', __('More button icon', ESSB3_TEXT_DOMAIN), __('Select more button icon style. You can choose from default + symbol or dots symbol', ESSB3_TEXT_DOMAIN), $more_options);

ESSBOptionsStructureHelper::field_heading('quick', 'quick-4', 'heading1', __('4. Counters', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('quick', 'quick-4', 'show_counter', __('Display counter of sharing', ESSB3_TEXT_DOMAIN), __('Activate display of share counters.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$counter_mode = array("" => "Real time share counters", "cached" => "Cached share counters");
ESSBOptionsStructureHelper::field_select('quick', 'quick-4', 'counter_mode', __('Counter update mode', ESSB3_TEXT_DOMAIN), __('Choose how your counters will update. Cached counters will work faster than realtime because they update on predefined period. Please note that when you use cache plugin cached counters will update when cache is expired in cache plugin', ESSB3_TEXT_DOMAIN), $counter_mode);
ESSBOptionsStructureHelper::field_heading('quick', 'quick-4', 'heading2', __('Button Counters', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('quick', 'quick-4', 'counter_pos', __('Position of counters', ESSB3_TEXT_DOMAIN), __('Choose your default button counter position', ESSB3_TEXT_DOMAIN), essb_avaliable_counter_positions());
ESSBOptionsStructureHelper::field_switch('quick', 'quick-4', 'facebooktotal', __('Display Facebook Total Count', ESSB3_TEXT_DOMAIN), __('Enable this option if you wish to display total count not only share count which is displayed by default.', ESSB3_TEXT_DOMAIN), 'yes', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_heading('quick', 'quick-4', 'heading2', __('Total Counter', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('quick', 'quick-4', 'total_counter_pos', __('Position of total counter', ESSB3_TEXT_DOMAIN), __('For vertical display methods left means before buttons (top) and right means after buttons (bottom).', ESSB3_TEXT_DOMAIN), essb_avaiable_total_counter_position());

ESSBOptionsStructureHelper::field_section_start('quick', 'quick-4', __('Total counter design options', ESSB3_TEXT_DOMAIN), __('Additional settings for total counter based on position.', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_textbox('quick', 'quick-4', 'counter_total_text', __('Change total text', ESSB3_TEXT_DOMAIN), __('This option allows you to change text Total that appear when left/right postion of total counter is selected.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('quick', 'quick-4', 'activate_total_counter_text', __('Append text to total counter when big number styles are active', ESSB3_TEXT_DOMAIN), __('This option allows you to add custom text below counter when big number styles are active. For example you can add text shares.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textarea('quick', 'quick-4', 'total_counter_afterbefore_text', __('Before/after social share buttons counter text', ESSB3_TEXT_DOMAIN), __('Customize the text that is displayed in before/ater share buttons display method. To display the total share number use the string {TOTAL} in text. Example: {TOTAL} users share us', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('quick', 'quick-4');

ESSBOptionsStructureHelper::field_heading('quick', 'quick-5', 'heading1', __('5. Display Buttons On', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('quick', 'quick-5', 'essb3_post_type_select', __('Where to display buttons', ESSB3_TEXT_DOMAIN), __('Choose post types where you wish buttons to appear. If you are running WooCommerce store you can choose between post type Products which will display share buttons into product description or option to display buttons below price.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('quick', 'quick-5', __('Display in post excerpt', ESSB3_TEXT_DOMAIN), __('Activate this option if your theme is using excerpts and you wish to display share buttons in excerpts', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('quick', 'quick-5', 'display_excerpt', __('Activate', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("top" => "Before excerpt", "bottom" => "After excerpt");
ESSBOptionsStructureHelper::field_select('quick', 'quick-5', 'display_excerpt_pos', __('Buttons position in excerpt', ESSB3_TEXT_DOMAIN), __(''), $listOfOptions);
ESSBOptionsStructureHelper::field_section_end('quick', 'quick-5');

ESSBOptionsStructureHelper::field_heading('quick', 'quick-6', 'heading1', __('6. Position Of Buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_image_radio('quick', 'quick-6', 'content_position', __('Primary content display position', ESSB3_TEXT_DOMAIN), __('Choose default method that will be used to render buttons inside content', ESSB3_TEXT_DOMAIN), essb_avaliable_content_positions());
ESSBOptionsStructureHelper::field_image_checkbox('quick', 'quick-6', 'button_position', __('Additional button display positions', ESSB3_TEXT_DOMAIN), __('Choose additional display methods that can be used to display buttons.', ESSB3_TEXT_DOMAIN), essb_available_button_positions());

ESSBOptionsStructureHelper::field_heading('quick', 'quick-7', 'heading1', __('7. Mobile', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('quick', 'quick-7', 'mobile_positions', __('Change display positions on mobile', ESSB3_TEXT_DOMAIN), __('Activate this option to personalize display positions on mobile', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_image_radio('quick', 'quick-7', 'content_position_mobile', __('Primary content display position', ESSB3_TEXT_DOMAIN), __('Choose default method that will be used to render buttons inside content', ESSB3_TEXT_DOMAIN), essb_avaliable_content_positions_mobile());
ESSBOptionsStructureHelper::field_image_checkbox('quick', 'quick-7', 'button_position_mobile', __('Additional button display positions', ESSB3_TEXT_DOMAIN), __('Choose additional display methods that can be used to display buttons.', ESSB3_TEXT_DOMAIN), essb_available_button_positions_mobile());

global $essb_networks;
$checkbox_list_networks = array();
foreach ($essb_networks as $key => $object) {
	$checkbox_list_networks[$key] = $object['name'];
}
ESSBOptionsStructureHelper::field_checkbox_list_sortable('quick', 'quick-7', 'mobile_networks', __('Change active social networks', ESSB3_TEXT_DOMAIN), __('Do not select anything if you wish to use default network list'. ESSB3_TEXT_DOMAIN), $checkbox_list_networks);


ESSBOptionsStructureHelper::field_switch('quick', 'quick-7', 'mobile_exclude_tablet', __('Do not apply mobile settings for tablets', ESSB3_TEXT_DOMAIN), __('You can avoid mobile rules for settings for tablet devices.', ESSB3_TEXT_DOMAIN), 'recommeded', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('quick', 'quick-7', __('Share bar customization', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_textbox('quick', 'quick-7', 'mobile_sharebar_text', __('Text on share bar', ESSB3_TEXT_DOMAIN), __('Customize the default share bar text (default is Share).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('quick', 'quick-7');
ESSBOptionsStructureHelper::field_section_start('quick', 'quick-7', __('Share buttons bar customization', ESSB3_TEXT_DOMAIN), '');
$listOfOptions = array("2" => "2 Buttons", "3" => "3 Buttons", "4" => "4 Buttons", "5" => "5 Buttons");
ESSBOptionsStructureHelper::field_select('quick', 'quick-7', 'mobile_sharebuttonsbar_count', __('Number of buttons in share buttons bar', ESSB3_TEXT_DOMAIN), __('Provide number of buttons you wish to see in buttons bar. If the number of activated buttons is greater than selected here the last button will be more button which will open pop up with all active buttons.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('quick', 'quick-7', 'mobile_sharebuttonsbar_names', __('Display network names', ESSB3_TEXT_DOMAIN), __('Activate this option to display network names (default is display is icons only).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('quick', 'quick-7');

ESSBOptionsStructureHelper::field_heading('quick', 'quick-8', 'heading1', __('8. Final Settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('quick', 'quick-8', 'quick_setup_recommended', __('Apply social networks recommended settings', ESSB3_TEXT_DOMAIN), __('Activate this option to activate recommended for each social network options (like Short URL for Twitter)', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('quick', 'quick-8', 'opengraph_tags', __('Activate social share optimization meta tags', ESSB3_TEXT_DOMAIN), __('If you do not use SEO plugin or other plugin that insert social share optimization meta tags it is highly recommended to activate this option. It will generated required for better sharing meta tags and also will allow you to change the values that social network read from your site.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('quick', 'quick-8', 'stats_active', __('Activate social share buttons click statistics', ESSB3_TEXT_DOMAIN), __('Click statistics hanlde click on share buttons and you are able to see detailed view of user activity. Please note that plugin log clicks of buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('quick', 'quick-8', __('Optimizations', ESSB3_TEXT_DOMAIN), __('Select which optimization options you wish to use', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('quick', 'quick-8', 'quick_setup_static', __('Optimize static plugin resources load', ESSB3_TEXT_DOMAIN), __('Activate this option to apply the recommended options for static resources load.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('quick', 'quick-8', 'precompiled_resources', __('Use plugin precompiled resources', ESSB3_TEXT_DOMAIN), __('Activating this option will precompile and cache plugin dynamic resources to save load time. Precompiled resources can be used only when you use same configuration on your entire site.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_section_end('quick', 'quick-8');
ESSBOptionsStructureHelper::field_switch('quick', 'quick-8', 'quick_setup_easy', __('Activate Easy Mode', ESSB3_TEXT_DOMAIN), __('Easy Mode is recommended for initial start of work with plugin. With that mode you get access to most common used options and set of predefined settings. Easy Mode can be turned off at any time', ESSB3_TEXT_DOMAIN), 'recommended', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN), 'recommended');

?>