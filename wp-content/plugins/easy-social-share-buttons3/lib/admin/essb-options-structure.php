<?php
$essb_navigation_tabs = array();
$essb_sidebar_sections = array();
$essb_sidebar_sections = array();
if (!class_exists('ESSBSocialFollowersCounterHelper')) {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/modules/social-followers-counter/essb-social-followers-counter-helper.php');
}

$essb_options = essb_options();

ESSBOptionsStructureHelper::init();
ESSBOptionsStructureHelper::tab('social', __('Social Buttons', ESSB3_TEXT_DOMAIN), __('Social Buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::tab('display', __('Display Settings', ESSB3_TEXT_DOMAIN), __('Display Settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::tab('advanced', __('Advanced Settings', ESSB3_TEXT_DOMAIN), __('Advanced Settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::tab('style', __('Style Settings', ESSB3_TEXT_DOMAIN), __('Style Settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::tab('shortcode', __('Shortcode Generator', ESSB3_TEXT_DOMAIN), __('Shortcode Generator', ESSB3_TEXT_DOMAIN), true);
ESSBOptionsStructureHelper::tab('analytics', __('Analytics Dashboard', ESSB3_TEXT_DOMAIN), __('Analytics Dashboard', ESSB3_TEXT_DOMAIN), true);
ESSBOptionsStructureHelper::tab('import', __('Import/Export', ESSB3_TEXT_DOMAIN), __('Import / Export Options', ESSB3_TEXT_DOMAIN), true);
ESSBOptionsStructureHelper::tab('update', __('Update', ESSB3_TEXT_DOMAIN), __('Automatic Updates', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::tab('quick', __('Quick Setup', ESSB3_TEXT_DOMAIN), __('Quick Setup Wizard', ESSB3_TEXT_DOMAIN), false, true, true);

//-- menu
$user_active_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
if ($user_active_tab == "quick") {
	include_once (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-options-structure-quick.php');	
}

//---- automatic updates
ESSBOptionsStructureHelper::menu_item('update', 'automatic', __('Automatic Updates', ESSB3_TEXT_DOMAIN), 'refresh');
ESSBOptionsStructureHelper::field_heading('update', 'automatic', 'heading1', __('Activate Automatic Plugin Updates', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('update', 'automatic', 'purchase_code', __('Purchase code', ESSB3_TEXT_DOMAIN), __('To activate automatic plugin updates you need to fill your purchase code.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('update', 'automatic', 'essb3_text_automatic_updates', '', '');

//---- import export
ESSBOptionsStructureHelper::menu_item('import', 'backup', __('Export Settings', ESSB3_TEXT_DOMAIN), 'database');
ESSBOptionsStructureHelper::menu_item('import', 'backupimport', __('Import Settings', ESSB3_TEXT_DOMAIN), 'database');
ESSBOptionsStructureHelper::menu_item('import', 'readymade', __('Apply Ready Made Style', ESSB3_TEXT_DOMAIN), 'square');
ESSBOptionsStructureHelper::field_heading('import', 'backup', 'heading1', __('Export Plugin Settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('import', 'backup', 'essb3_text_backup', __('Export plugin settings', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_func('import', 'backup', 'essb3_text_backup1', __('Save plugin settings to file', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_func('import', 'backupimport', 'essb3_text_backup_import', __('Import plugin settings', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_func('import', 'backupimport', 'essb3_text_backup_import1', __('Import plugin settings from file', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_func('import', 'readymade', 'essb3_text_readymade', __('Ready made configurations', ESSB3_TEXT_DOMAIN), 'WARNING! Importing configuration will overwrite all existing option values that you have set and load the predefined in the ready made configuration.');

//---- social
ESSBOptionsStructureHelper::menu_item('social', 'sharing', __('Social Sharing', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'sharing-11');
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-1', __('Template & Style', ESSB3_TEXT_DOMAIN), 'chevron-down', 'menu', 'title');
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-11', __('Template', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-12', __('Style & Align', ESSB3_TEXT_DOMAIN), 'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-13', __('Button Width', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-2', __('Social Networks', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-3', __('Additional Network Options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-4', __('Counters', ESSB3_TEXT_DOMAIN), 'chevron-down', 'menu', 'title');
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-14', __('Display counters', ESSB3_TEXT_DOMAIN), 'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-15', __('Counters update', ESSB3_TEXT_DOMAIN), 'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-5', __('Network Names', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-6', __('Analytics', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-7', __('Sharing Optimization', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-8', __('Custom Share', ESSB3_TEXT_DOMAIN));
//ESSBOptionsStructureHelper::submenu_item('social', 'sharing-16', __('Affiliate Integration', ESSB3_TEXT_DOMAIN), 'chevron-down', 'menu', 'title');
ESSBOptionsStructureHelper::submenu_item('social', 'sharing-9', __('Affiliate & Point Plugin Integration', ESSB3_TEXT_DOMAIN));
//ESSBOptionsStructureHelper::submenu_item('social', 'sharing-10', __('AffiliateWP Integration', ESSB3_TEXT_DOMAIN), 'default', 'menu', 'true');
//ESSBOptionsStructureHelper::submenu_item('social', 'sharing-17', __('Affiliates Integration', ESSB3_TEXT_DOMAIN), 'default', 'menu', 'true');

ESSBOptionsStructureHelper::menu_item('social', 'native', __('Like, Follow & Subscribe', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'native-1');
ESSBOptionsStructureHelper::submenu_item('social', 'native-1', __('Social Networks', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'native-2', __('Skinned buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'native-3', __('Social Privacy', ESSB3_TEXT_DOMAIN));


ESSBOptionsStructureHelper::menu_item('social', 'follow', __('Social Followers Counter', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'follow-1');
ESSBOptionsStructureHelper::submenu_item('social', 'follow-1', __('Settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'follow-2', __('Social Networks', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::menu_item('social', 'profiles', __('Social Profiles', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'profiles-1');
ESSBOptionsStructureHelper::submenu_item('social', 'profiles-1', __('Settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'profiles-2', __('Social Networks', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::menu_item('social', 'after-share', __('After Share Actions', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'after-share-1');
ESSBOptionsStructureHelper::submenu_item('social', 'after-share-1', __('Action Type', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'after-share-2', __('Like/Follow Options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'after-share-3', __('Custom HTML Message', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('social', 'after-share-4', __('Custom Code', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::menu_item('social', 'social-metrics', __('Social Metrics Lite', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('social', 'shorturl', __('Short URL', ESSB3_TEXT_DOMAIN), 'default');

//---- display
ESSBOptionsStructureHelper::menu_item('display', 'settings', __('Where to display', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'settings-1');
ESSBOptionsStructureHelper::submenu_item('display', 'settings-1', __('Post Types', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'settings-2', __('WooCommerce', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'settings-2', __('JigoShop', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'settings-3', __('WP e-Commerce', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'settings-4', __('JigoShop', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'settings-5', __('iThemes Exchange', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'settings-6', __('bbPress', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'settings-7', __('BuddyPress', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::menu_item('display', 'positions', __('Display Positions', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'positions-1');

ESSBOptionsStructureHelper::submenu_item('display', 'positions-1', __('Positions', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-2', __('Display Position Settings', ESSB3_TEXT_DOMAIN), 'chevron-down', 'menu', 'title');

//ESSBOptionsStructureHelper::menu_item('display', 'locations', __('Display Position Settings', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'positions-3');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-3', __('Content Top', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-4', __('Content Bottom', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-5', __('Float from top', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-6', __('Post vertical float', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-7', __('Sidebar', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-8', __('Top bar', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-9', __('Bottom bar', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-10', __('Pop up', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-11', __('Fly in', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-12', __('On media', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-13', __('Full screen hero share', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-15', __('Post share bar', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-16', __('Point', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');
ESSBOptionsStructureHelper::submenu_item('display', 'positions-14', __('Excerpt', ESSB3_TEXT_DOMAIN),  'default', 'menu', 'true');

ESSBOptionsStructureHelper::menu_item('display', 'mobile', __('Mobile', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'mobile-1');
ESSBOptionsStructureHelper::submenu_item('display', 'mobile-1', __('Display Options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'mobile-2', __('Customize buttons when viewed from mobile device', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'mobile-3', __('Share bar', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'mobile-4', __('Share point', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'mobile-5', __('Share buttons bar', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::menu_item('display', 'message', __('Message before/above buttons', ESSB3_TEXT_DOMAIN), 'default', 'activate_first', 'message-1');
ESSBOptionsStructureHelper::submenu_item('display', 'message-1', __('Before Buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'message-2', __('Above Share Buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::submenu_item('display', 'message-3', __('Above Like Buttons', ESSB3_TEXT_DOMAIN));

//---- advanced
ESSBOptionsStructureHelper::menu_item('advanced', 'optimization', __('Optimization Options', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('advanced', 'advanced', __('Advanced Options', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('advanced', 'administrative', __('Administrative Options', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('advanced', 'deactivate', __('Deactivate Functions & Modules', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('advanced', 'counterrecovery', __('Share Counter Recovery', ESSB3_TEXT_DOMAIN), 'sign-in');
ESSBOptionsStructureHelper::menu_item('advanced', 'localization', __('Translate Options', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('advanced', 'twitter', __('Twitter Counter Setup', ESSB3_TEXT_DOMAIN), 'twitter');
ESSBOptionsStructureHelper::menu_item('advanced', 'convert', __('Import settings from previous version', ESSB3_TEXT_DOMAIN), 'default');
if (ESSBOptionValuesHelper::options_bool_value($essb_options, 'advanced_custom_share')) {
	ESSBOptionsStructureHelper::menu_item('advanced', 'advancedshare', __('Advanced Custom Share', ESSB3_TEXT_DOMAIN), 'default');
	essb3_prepare_advanced_custom_share('advanced', 'advancedshare');
}

ESSBOptionsStructureHelper::menu_item('style', 'buttons', __('Color Customization', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('style', 'fans', __('Followers Counter Color Customization', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('style', 'image', __('Image Share Color Customization', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('style', 'subscribe', __('MailChimp Subscribe Form', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('style', 'css', __('Additional CSS', ESSB3_TEXT_DOMAIN), 'default');
ESSBOptionsStructureHelper::menu_item('style', 'css2', __('Additional Footer CSS', ESSB3_TEXT_DOMAIN), 'default');
//ESSBOptionsStructureHelper::menu_item('advanced', 'advancedpost', __('Display Settings by Post Type', ESSB3_TEXT_DOMAIN), 'default');
//ESSBOptionsStructureHelper::menu_item('advanced', 'advancedcat', __('Display Settings by Post Category', ESSB3_TEXT_DOMAIN), 'default');

// -- option fields: social
ESSBOptionsStructureHelper::field_heading('social', 'sharing-11', 'heading1', __('Template', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('social', 'sharing-11', 'essb3_options_template_select', __('Template', ESSB3_TEXT_DOMAIN), __('This will be your default theme for site. You are able to select different theme for each post/page.', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-12', 'heading1', __('Style', ESSB3_TEXT_DOMAIN));
/*ESSBOptionsStructureHelper::field_select('social', 'sharing-12', 'button_style', __('Buttons Style', ESSB3_TEXT_DOMAIN), __('Select your default button display style.', ESSB3_TEXT_DOMAIN), $essb_avaiable_button_style);
*/
ESSBOptionsStructureHelper::field_image_radio('social', 'sharing-12', 'button_style', __('Buttons Style', ESSB3_TEXT_DOMAIN), __('Select your default button style', ESSB3_TEXT_DOMAIN), essb_available_buttons_style());

/*ESSBOptionsStructureHelper::field_select('social', 'sharing-12', 'button_pos', __('Buttons Align', ESSB3_TEXT_DOMAIN), __('Choose how buttons
									to be aligned. Default position is left but you can also select
									Right or Center', ESSB3_TEXT_DOMAIN), array("" => "Left", "center" => "Center", "right" => "Right"));
*/
ESSBOptionsStructureHelper::field_image_radio('social', 'sharing-12', 'button_pos', __('Buttons Align', ESSB3_TEXT_DOMAIN), __('Choose how buttons
									to be aligned. Default position is left but you can also select
									Right or Center', ESSB3_TEXT_DOMAIN), essb_available_buttons_align());

ESSBOptionsStructureHelper::field_select('social', 'sharing-12', 'css_animations', __('Activate animations', ESSB3_TEXT_DOMAIN), __('Animations
									are provided with CSS transitions and work on best with retina
									templates.', ESSB3_TEXT_DOMAIN), essb_available_animations());
ESSBOptionsStructureHelper::field_switch('social', 'sharing-12', 'nospace', __('Remove spacing between buttons', ESSB3_TEXT_DOMAIN), __('Activate this option to remove default space between share buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-13', 'heading1', __('Buttons width', ESSB3_TEXT_DOMAIN));
//ESSBOptionsStructureHelper::field_select('social', 'sharing-13', 'button_width', __('Width of buttons'), __('Choose between automatic width, pre defined width or display in columns.'), array(''=>'Automatic Width', 'fixed' => 'Fixed Width', 'full' => 'Full Width', "column" => "Display in columns"));
ESSBOptionsStructureHelper::field_image_radio('social', 'sharing-13', 'button_width', __('Width of buttons', ESSB3_TEXT_DOMAIN), __('Choose between automatic width, pre defined width or display in columns.', ESSB3_TEXT_DOMAIN), essb_available_buttons_width());

ESSBOptionsStructureHelper::field_heading('social', 'sharing-13', 'heading5', __('Fixed width share buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_panels('social', 'sharing-13', __('Fixed width share buttons', ESSB3_TEXT_DOMAIN), __('Customize the fixed width options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-13', 'fixed_width_value', __('Custom buttons width', ESSB3_TEXT_DOMAIN), __('Provide custom width of button in pixels without the px symbol.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_select_panel('social', 'sharing-13', 'fixed_width_align', __('Choose alignment of network name', ESSB3_TEXT_DOMAIN), __('Provide different alignment of network name, when fixed button width is activated. When counter position is Inside or Inside name, that alignment will be applied for the counter. Default value is center.', ESSB3_TEXT_DOMAIN), array("" => "Center", "left" => "Left", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_end_panels('social', 'sharing-13');

ESSBOptionsStructureHelper::field_heading('social', 'sharing-13', 'heading5', __('Full width share buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_panels('social', 'sharing-13', __('Full width share buttons', ESSB3_TEXT_DOMAIN), __('Full width option will make buttons to take the width of your post content area.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-13', 'fullwidth_share_buttons_correction', __('Max width of button on desktop', ESSB3_TEXT_DOMAIN), __('Provide custom width of single button when full width is active. This value is number in percents without the % symbol.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-13', 'fullwidth_share_buttons_correction_mobile', __('Max width of button on mobile', ESSB3_TEXT_DOMAIN), __('Provide custom width of single button when full width is active. This value is number in percents without the % symbol.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-13', 'fullwidth_share_buttons_container', __('Max width of buttons container element', ESSB3_TEXT_DOMAIN), __('If you wish to display total counter along with full width share buttons please provide custom max width of buttons container in percent without % (example: 90). Leave this field blank for default value of 100 (100%).', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_select_panel('social', 'sharing-13', 'fullwidth_align', __('Choose alignment of network name', ESSB3_TEXT_DOMAIN), __('Provide different alignment of network name (counter when position inside or inside name). Default value is left.', ESSB3_TEXT_DOMAIN), array("left" => "Left", "center" => "Center", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_end_panels('social', 'sharing-13');

ESSBOptionsStructureHelper::field_section_start_panels('social', 'sharing-13', __('Customize width of first two buttons', ESSB3_TEXT_DOMAIN), __('Provide different width for the first two buttons in the row. The width should be entered as number in percents (without the % mark). You can fill only one of the values or both values.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-13', 'fullwidth_first_button', __('Width of first button', ESSB3_TEXT_DOMAIN), __('Provide custom width of first button when full width is active. This value is number in percents without the % symbol.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-13', 'fullwidth_second_button', __('Width of second button', ESSB3_TEXT_DOMAIN), __('Provide custom width of second button when full width is active. This value is number in percents without the % symbol.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_section_end_panels('social', 'sharing-13');

ESSBOptionsStructureHelper::field_heading('social', 'sharing-13', 'heading5', __('Buttons displayed in columns', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_panels('social', 'sharing-13', __('Display in columns'), '');
$listOfOptions = array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5");
ESSBOptionsStructureHelper::field_select_panel('social', 'sharing-13', 'fullwidth_share_buttons_columns', __('Number of columns', ESSB3_TEXT_DOMAIN), __('Choose the number of columns that buttons will be displayed.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_select_panel('social', 'sharing-13', 'fullwidth_share_buttons_columns_align', __('Choose alignment of network name', ESSB3_TEXT_DOMAIN), __('Provide different alignment of network name (counter when position inside or inside name). Default value is left.', ESSB3_TEXT_DOMAIN), array("" => "Left", "center" => "Center", "right" => "Right"));
ESSBOptionsStructureHelper::field_section_end_panels('social', 'sharing-13');

ESSBOptionsStructureHelper::field_heading('social', 'sharing-2', 'heading1', __('Social Networks', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('social', 'sharing-2', 'essb3_network_selection', __('Social Networks', ESSB3_TEXT_DOMAIN), __('Select networks that you wish to appear in your list. With drag and drop you can rearrange them.', ESSB3_TEXT_DOMAIN));
$more_options = array ("1" => "Display all active networks after more button", "2" => "Display all social networks as pop up", "3" => "Display only active social networks as pop up" );
ESSBOptionsStructureHelper::field_select('social', 'sharing-2', 'more_button_func', __('More button', ESSB3_TEXT_DOMAIN), __('Select networks that you wish to appear in your list. With drag and drop you can rearrange them.', ESSB3_TEXT_DOMAIN), $more_options);
$more_options = array ("plus" => "Plus icon", "dots" => "Dots icon" );
ESSBOptionsStructureHelper::field_select('social', 'sharing-2', 'more_button_icon', __('More button icon', ESSB3_TEXT_DOMAIN), __('Select more button icon style. You can choose from default + symbol or dots symbol', ESSB3_TEXT_DOMAIN), $more_options);

ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading1', __('Additional Network Options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Twitter', ESSB3_TEXT_DOMAIN));
//ESSBOptionsStructureHelper::field_section_start('social', 'sharing-3', __('Twitter share short url', ESSB3_TEXT_DOMAIN), __('Activate this option to share short url with Twitter.', ESSB3_TEXT_DOMAIN), 'yes');
//ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'twitter_shareshort', __('Activate', ESSB3_TEXT_DOMAIN), __('Activate short url usage.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
//$listOfOptions = array("wp" => "Build in WordPress function wp_get_shortlink()", "goo.gl" => "goo.gl", "bit.ly" => "bit.ly");
//if (defined('ESSB3_SSU_VERSION')) {
//	$listOfOptions['ssu'] = "Self-Short URL Add-on for Easy Social Share Buttons";
//}
//ESSBOptionsStructureHelper::field_select('social', 'sharing-3', 'twitter_shareshort_service', __('Short URL service', ESSB3_TEXT_DOMAIN), __('Choose the url service for Twitter', ESSB3_TEXT_DOMAIN), $listOfOptions);
//ESSBOptionsStructureHelper::field_checkbox('social', 'sharing-3', 'twitter_always_count_full', __('Make Twitter always count full post/page address when using short url', ESSB3_TEXT_DOMAIN), '');
//ESSBOptionsStructureHelper::field_section_end('social', 'sharing-3');

ESSBOptionsStructureHelper::field_section_start_panels('social', 'sharing-3', __('Username and Hashtags', ESSB3_TEXT_DOMAIN), __('Provide default Twitter username and hashtags to be included into messages.', ESSB3_TEXT_DOMAIN), 'yes');
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-3', 'twitteruser', __('Username to be mentioned:', ESSB3_TEXT_DOMAIN), __('If you wish a twitter username to be mentioned in tweet write it here. Enter your username without @ - example twittername. This text will be appended to tweet message at the end. Please note that if you activate custom share address option this will be added to custom share message.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-3', 'twitterhashtags', __('Hashtags to be added:', ESSB3_TEXT_DOMAIN), __('If you wish hashtags to be added to message write them here. You can set one or more (if more then one separate them with comma (,)) Example: demotag1,demotag2.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('social', 'sharing-3', 'twitter_message_tags_to_hashtags', __('Use post tags as hashtags', ESSB3_TEXT_DOMAIN), __('Activate this option to use your current post tags as hashtags. When this option is active the default hashtags will be replaced with post tags when there are such post tags.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('social', 'sharing-3');

ESSBOptionsStructureHelper::field_section_start('social', 'sharing-3', __('Twitter message optimization', ESSB3_TEXT_DOMAIN), __('Twitter message optimization allows you to truncate your message if it exceeds the 140 characters length of message.', ESSB3_TEXT_DOMAIN), 'yes');
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'twitter_message_optimize', __('Activate', ESSB3_TEXT_DOMAIN), __('Activate message optimization.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("1" => "Remove hashtags, remove via username, truncate message", "2" => "Remove via username, remove hashtags, truncate message", "3" => "Remove via username, truncate message", "4" => "Remove hashtags, truncate message", "5" => "Truncate only message");
ESSBOptionsStructureHelper::field_select('social', 'sharing-3', 'twitter_message_optimize_method', __('Method of optimization', ESSB3_TEXT_DOMAIN), __('Choose the order of components to be removed till reaching the limit of characters', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'twitter_message_optimize_dots', __('Add read more dots when truncate message', ESSB3_TEXT_DOMAIN), __('Add ... (read more dots) to truncated tweets.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-3');



ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Facebook', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('social', 'sharing-3', __('Facebook Advanced Sharing', ESSB3_TEXT_DOMAIN), __('For proper work of advanced Facebook sharing you need to provide application id. If you don\'t have you need to create one. To create Facebook Application use this link: http://developers.facebook.com/apps/', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'facebookadvanced', __('Activate', ESSB3_TEXT_DOMAIN), '', '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'facebookadvancedappid', __('Facebook Application ID:', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-3');

ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Pinterest', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'pinterest_sniff_disable', __('Disable Pinterest Pin any image:', ESSB3_TEXT_DOMAIN), __('If you disable Pinterest sniff for images plugin will use for share post featured image or custom share image you provide.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Subscribe button', ESSB3_TEXT_DOMAIN));
$listOfValues = array ("form" => "Open content box", "link" => "Open subscribe link", "mailchimp" => "Mailchimp Subscribe Form (Pre made design and Mailchimp Integration)" );
ESSBOptionsStructureHelper::field_select('social', 'sharing-3', 'subscribe_function', __('Specify subscribe button function', ESSB3_TEXT_DOMAIN), __('Specify if the subscribe button is opening a content box below the button or if the button is linked to the "subscribe url" below.', ESSB3_TEXT_DOMAIN), $listOfValues);
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_link', __('Subscribe URL', ESSB3_TEXT_DOMAIN), __('Link the Subscribe button to this URL. This can be the url to your subscribe page, facebook fanpage, RSS feed etc. e.g. http://yoursite.com/subscribe', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('social', 'sharing-3', 'subscribe_content', __('Subscribe content box', ESSB3_TEXT_DOMAIN), __('Define the content of the opening toggle subscribe window here. Use formulars, like button, links or any other text. Shortcodes are supported, e.g.: [contact-form-7]. Note that if you use subscribe button outside content display positions content will open as popup', ESSB3_TEXT_DOMAIN), 'htmlmixed');
ESSBOptionsStructureHelper::field_section_start('social', 'sharing-3', __('Mailchimp Subscribe Form', ESSB3_TEXT_DOMAIN), __('Customize default MailChimp subscribe form texts', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'subscribe_mc_welcome', __('Send welcome message:', ESSB3_TEXT_DOMAIN), __('Allow Mailchimp send welcome mssage upo subscribe.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_mc_api', __('Mailchimp API key', ESSB3_TEXT_DOMAIN), __('<a href="http://kb.mailchimp.com/accounts/management/about-api-keys#Finding-or-generating-your-API-key" target="_blank">Find your API key</a>', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_mc_list', __('Mailchimp List ID', ESSB3_TEXT_DOMAIN), __('<a href="http://kb.mailchimp.com/lists/managing-subscribers/find-your-list-id" target="_blank">Find your List ID</a>', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_mc_title', __('Title', ESSB3_TEXT_DOMAIN), __('Customize default title: Join our list', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_mc_text', __('Text', ESSB3_TEXT_DOMAIN), __('Customize default text: Subscribe to our mailing list and get interesting stuff and updates to your email inbox.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_mc_email', __('Email placeholder text', ESSB3_TEXT_DOMAIN), __('Customize default email placeholder text: Enter your email here', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_mc_button', __('Subscribe button text', ESSB3_TEXT_DOMAIN), __('Customize default button text: Sign Up Now', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_mc_footer', __('Footer text', ESSB3_TEXT_DOMAIN), __('Customize default footer text: We respect your privacy and take protecting it seriously', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_mc_success', __('Success messsage', ESSB3_TEXT_DOMAIN), __('Customize Success Message: Thank you for subscribing.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'subscribe_mc_error', __('Error message', ESSB3_TEXT_DOMAIN), __('Customize Error Message: Something went wrong.', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_section_end('social', 'sharing-3');

ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Email', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('social', 'sharing-3', __('Email button send options', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '');
$listOfValues = array ("form" => "Send mail using pop up form", "link" => "Send mail using mailto link and user mail client" );
ESSBOptionsStructureHelper::field_select('social', 'sharing-3', 'mail_function', __('Send to mail button function', ESSB3_TEXT_DOMAIN), __('Choose how you wish mail button to operate. By default it uses the build in pop up window with sendmail option but you can change this to link option to force use of client mail program.', ESSB3_TEXT_DOMAIN), $listOfValues);
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'use_wpmandrill', __('Use wpMandrill for send mail', ESSB3_TEXT_DOMAIN), __('To be able to send messages with wpMandrill you need to have plugin installed.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'mail_copyaddress', __('Send copy of all messages to', ESSB3_TEXT_DOMAIN), __('Provide email address if you wish to get copy of each message that is sent via form', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'mail_inline_code', __('Append inline mail send code', ESSB3_TEXT_DOMAIN), __('Activate this option if you use Initite scroll plugin and mail button do not work', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-3');

ESSBOptionsStructureHelper::field_section_start('social', 'sharing-3', __('Pop up mail form options', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '');
$listOfValues = array ("host" => "Using host mail function", "wp" => "Using WordPress mail function" );
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'mail_disable_editmessage', __('Disable editing of mail message', ESSB3_TEXT_DOMAIN), __('Activate this option to prevent users from changing the default message.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('social', 'sharing-3', 'mail_function_command', __('Use the following command to send mails when form is used', ESSB3_TEXT_DOMAIN), __('Choose the default function you will use to send mails when mail form is active. If you use external plugin in WordPress for send mail (like Easy WP SMTP) you need to choose WordPress mail function to get your messages sent.', ESSB3_TEXT_DOMAIN), $listOfValues);
$listOfValues = array ("level1" => "Advanced security check", "level2" => "Basic security check" );
ESSBOptionsStructureHelper::field_select('social', 'sharing-3', 'mail_function_security', __('Use the following security check when form is used', ESSB3_TEXT_DOMAIN), __('Security check is made to prevent unauthorized access to send mail function of plugin. The default option is to use advanced security check but if you get message invalid security key during send process switch to lower level check - Basic security check.', ESSB3_TEXT_DOMAIN), $listOfValues);
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'mail_popup_mobile', __('Allow usage of pop up mail form on mobile devices', ESSB3_TEXT_DOMAIN), __('Activate this option to allow usage of pop up form when site is browsed with mobile device. Default setting is to use build in mobile device mail application.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-3');


ESSBOptionsStructureHelper::field_section_start('social', 'sharing-3', __('Antispam Captcha Verification', ESSB3_TEXT_DOMAIN), __('Fill both fields for question and answer to prevent sending message without entering the correct answer.', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'mail_captcha', __('Captcha Message', ESSB3_TEXT_DOMAIN), __('Enter captcha question you wish to ask users to validate that they are human.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'mail_captcha_answer', __('Captcha Answer', ESSB3_TEXT_DOMAIN), __('Enter answer you wish users to put to verify them.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-3');

ESSBOptionsStructureHelper::field_section_start('social', 'sharing-3', __('Customize default mail message', ESSB3_TEXT_DOMAIN), __('You can customize texts to display when visitors share your content by mail button. To perform customization, you can use %%title%%, %%siteurl%%, %%permalink%% or %%image%% variables.', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-3', 'mail_subject', __('Subject', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textarea('social', 'sharing-3', 'mail_body', __('Message', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-3');

ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Print', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'print_use_printfriendly', __('Use for printing printfreidly.com', ESSB3_TEXT_DOMAIN), __('Activate that option to use printfriendly.com as printing service instead of default print function of browser', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('StumpleUpon', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'stumble_noshortlink', __('Do not generate shortlinks', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Buffer', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'buffer_twitter_user', __('Add Twitter username to buffer shares', ESSB3_TEXT_DOMAIN), __('Append also Twitter username into Buffer shares', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Telegram', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'telegram_alternative', __('Use alternative Telegram share', ESSB3_TEXT_DOMAIN), __('Alternative Telegram share method uses Telegram website to share data instead of direct call to mobile application. This method currently supports share to web application too.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

//ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('WhatsApp', ESSB3_TEXT_DOMAIN));
//ESSBOptionsStructureHelper::field_section_start('social', 'sharing-3', __('WhatsApp share short url', ESSB3_TEXT_DOMAIN), __('Activate this option to share short url with Twitter.', ESSB3_TEXT_DOMAIN), 'yes');
//ESSBOptionsStructureHelper::field_switch('social', 'sharing-3', 'whatsapp_shareshort', __('Activate', ESSB3_TEXT_DOMAIN), __('Activate short url usage.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
//$listOfOptions = array("wp" => "Build in WordPress function wp_get_shortlink()", "goo.gl" => "goo.gl", "bit.ly" => "bit.ly");
//if (defined('ESSB3_SSU_VERSION')) {
//	$listOfOptions['ssu'] = "Self-Short URL Add-on for Easy Social Share Buttons";
//}
//ESSBOptionsStructureHelper::field_select('social', 'sharing-3', 'whatsapp_shareshort_service', __('Short URL service', ESSB3_TEXT_DOMAIN), __('Choose the url service for WhatsApp', ESSB3_TEXT_DOMAIN), $listOfOptions);
//ESSBOptionsStructureHelper::field_section_end('social', 'sharing-3');


ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Flattr', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-3', 'flattr_username', __('Flattr Username', ESSB3_TEXT_DOMAIN), __('The Flattr account to which the buttons will be assigned.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-3', 'flattr_tags', __('Additional Flattr tags for your posts', ESSB3_TEXT_DOMAIN), __('Comma separated list of additional tags to use in Flattr buttons.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('social', 'sharing-3', 'flattr_cat', __('Default category for your posts', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), ESSBNetworks_Flattr::getCategories());
ESSBOptionsStructureHelper::field_select('social', 'sharing-3', 'flattr_lang', __('Default language for your posts', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), ESSBNetworks_Flattr::getLanguages());

ESSBOptionsStructureHelper::field_heading('social', 'sharing-3', 'heading2', __('Comments', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-3', 'comments_address', __('Comments button address', ESSB3_TEXT_DOMAIN), __('If you use external comment system like Disqus you may need to personalize address to comments element (default is #comments).', ESSB3_TEXT_DOMAIN));


ESSBOptionsStructureHelper::field_heading('social', 'sharing-14', 'heading1', __('Display Counters', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-14', 'show_counter', __('Display counter of sharing', ESSB3_TEXT_DOMAIN), __('Activate display of share counters.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-14', 'heading5', __('Button Counters', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('social', 'sharing-14', 'counter_pos', __('Position of counters', ESSB3_TEXT_DOMAIN), __('Choose your default button counter position', ESSB3_TEXT_DOMAIN), essb_avaliable_counter_positions());
ESSBOptionsStructureHelper::field_section_start('social', 'sharing-14', __('Additional Counter Options', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_switch('social', 'sharing-14', 'active_internal_counters', __('Activate internal counters for all networks that does not support API count', ESSB3_TEXT_DOMAIN), __('Activate internal
		counters for all networks that does not have access to API
		counter functions. If this option is active counters are stored
		in each post/page options and may be different from actual', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_switch('social', 'sharing-14', 'facebooktotal', __('Display Facebook Total Count', ESSB3_TEXT_DOMAIN), __('Enable this option if you wish to display total count not only share count which is displayed by default.', ESSB3_TEXT_DOMAIN), 'yes', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array ("self" => "Self-hosted counter (internally counted by click on buttons)", "newsc" => "Using NewShareCounts.com", "no" => "No counter for Twitter button" );
$counter_redirect = "";
$twitter_counters = ESSBOptionValuesHelper::options_value($essb_options, 'twitter_counters');
if ($twitter_counters == "self") {
	$counter_redirect = "<br/><br/><a href='admin.php?page=essb_redirect_advanced&tab=advanced&section=twitter'>Click here</a> to preload your Twitter self-hosted counter with initial value.";
}
ESSBOptionsStructureHelper::field_select('social', 'sharing-14', 'twitter_counters', __('Twitter share counter', ESSB3_TEXT_DOMAIN), __('Choose your Twitter counter working mode. If you select usage of NewShareCounts.com to make it work you need to visit their site and fill your site address and click sign in button using your Twitter account. Visit <a href="http://newsharecounts.com/" target="_blank">http://newsharecounts.com/</a>'.$counter_redirect, ESSB3_TEXT_DOMAIN), $listOfOptions);

ESSBOptionsStructureHelper::field_switch('social', 'sharing-14', 'deactive_internal_counters_mail', __('Deactivate counters for Mail & Print', ESSB3_TEXT_DOMAIN), __('Enable this option if you wish to deactivate internal counters for mail & print buttons. That buttons are in the list of default social networks that support counters. Deactivating them will lower down request to internal WordPress AJAX event.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-14');

ESSBOptionsStructureHelper::field_heading('social', 'sharing-14', 'heading5', __('Total Counter', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('social', 'sharing-14', 'total_counter_pos', __('Position of total counter', ESSB3_TEXT_DOMAIN), __('For vertical display methods left means before buttons (top) and right means after buttons (bottom).', ESSB3_TEXT_DOMAIN), essb_avaiable_total_counter_position());

ESSBOptionsStructureHelper::field_section_start('social', 'sharing-14', __('Total counter text options', ESSB3_TEXT_DOMAIN), __('Additional settings for total counter based on position.', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-14', 'counter_total_text', __('Change total text', ESSB3_TEXT_DOMAIN), __('This option allows you to change text Total that appear when left/right position of total counter is selected.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-14', 'activate_total_counter_text', __('Append text to total counter when big number styles are active', ESSB3_TEXT_DOMAIN), __('This option allows you to add custom text below counter when big number styles are active. For example you can add text shares.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textarea('social', 'sharing-14', 'total_counter_afterbefore_text', __('Change total counter text when before/after styles are active', ESSB3_TEXT_DOMAIN), __('Customize the text that is displayed in before/after share buttons display method. To display the total share number use the string {TOTAL} in text. Example: {TOTAL} users share us', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-14');

ESSBOptionsStructureHelper::field_heading('social', 'sharing-14', 'heading5', __('Avoid Social Negative Proof', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-14', 'button_counter_hidden_till', __('Display button counter after this value of shares is reached', ESSB3_TEXT_DOMAIN), __('You can hide your button counter until amount of shares is reached. This option is active only when you enter value in this field - if blank button counter is always displayed. (Example: 10 - this will make button counter appear when at least 10 shares are made).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-14', 'total_counter_hidden_till', __('Display total counter after this value of shares is reached', ESSB3_TEXT_DOMAIN), __('You can hide your total counter until amount of shares is reached. This option is active only when you enter value in this field - if blank total counter is always displayed.', ESSB3_TEXT_DOMAIN));


ESSBOptionsStructureHelper::field_heading('social', 'sharing-15', 'heading1', __('Counters update', ESSB3_TEXT_DOMAIN));

$counter_mode = array("" => "Real time share counters", "cached" => "Cached share counters");

ESSBOptionsStructureHelper::field_select('social', 'sharing-15', 'counter_mode', __('Counter update mode', ESSB3_TEXT_DOMAIN), __('Choose how your counters will update. Cached counters will work faster than realtime because they update on predefined period. Please note that when you use cache plugin cached counters will update when cache is expired in cache plugin', ESSB3_TEXT_DOMAIN), $counter_mode);


ESSBOptionsStructureHelper::field_heading('social', 'sharing-15', 'heading5', __('Cached share counters', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('social', 'sharing-15', 'cache_counter_refresh_new', __('Cached counters refresh interval', ESSB3_TEXT_DOMAIN), __('Choose interval of counter update when you use cached counters', ESSB3_TEXT_DOMAIN), essb_cached_counters_update());
ESSBOptionsStructureHelper::field_switch('social', 'sharing-15', 'cache_counter_refresh_cache', __('Activate cache plugin update mode', ESSB3_TEXT_DOMAIN), __('Activate this option if you use cache plugin and counters do not update often', ESSB3_TEXT_DOMAIN), 'yes', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-15', 'heading5', __('Real time share counters', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('social', 'sharing-15', __('Counter update options for all networks that does not provide direct access to counter API or does not have share counter and uses internal counters', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_switch('social', 'sharing-15', 'force_counters_admin', __('Load counters for social networks without direct access to counter API with build-in WordPress AJAX functions (using AJAX settings)', ESSB3_TEXT_DOMAIN), __('This method is more secure and required by some hosting companies but may slow down page load.', ESSB3_TEXT_DOMAIN), 'yes', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("wp" => "Build in WordPress ajax handler", "light" => "Light Easy Social Share Buttons handler");
ESSBOptionsStructureHelper::field_select('social', 'sharing-15', 'force_counters_admin_type', __('AJAX load method', ESSB3_TEXT_DOMAIN), __('Choose the default ajax method from build in WordPress or light handler', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('social', 'sharing-15', 'force_counters_admin_single', __('Use single request of counter load for all social networks that uses the ajax handler', ESSB3_TEXT_DOMAIN), __('This method will make single call to AJAX handler to get all counters instead of signle call for each network. The pros of this option is that you will make less calls to selected AJAX handler. We suggest to use this option in combination with counters cache.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-15');

ESSBOptionsStructureHelper::field_section_start('social', 'sharing-15', __('Counter cache for AJAX load method', ESSB3_TEXT_DOMAIN), __('This will reduce load because counters will be updated when cache expires', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_switch('social', 'sharing-15', 'admin_ajax_cache', __('Activate', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), 'yes', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-15', 'admin_ajax_cache_time', __('Cache expiration time', ESSB3_TEXT_DOMAIN), __('Amount of seconds for cache (default is 600 if nothing is provided)', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'sharing-15');


ESSBOptionsStructureHelper::field_heading('social', 'sharing-5', 'heading1', __('Network Names', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('social', 'sharing-5', 'essb3_network_rename', __('Social Network Names', ESSB3_TEXT_DOMAIN), __('Set different texts that will appear instead of social network names inside buttons. If you wish to hide network name for particular network enter - (dash) in network name field.', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-6', 'heading1', __('Social Share Analytics', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_heading('social', 'sharing-6', 'heading2', __('Social Share Click Logging', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-6', 'stats_active', __('Activate Statistics', ESSB3_TEXT_DOMAIN), __('Click statistics handle click on share buttons and you are able to see detailed view of user activity. Please note that plugin log clicks of buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-6', 'heading2', __('Google Analytics Tracking', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-6', 'activate_ga_tracking', __('Activate Google Analytics Tracking', ESSB3_TEXT_DOMAIN), __('Activate tracking of social share buttons click using Google Analytics (requires Google Analytics to be active on this site).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array ("simple" => "Simple", "extended" => "Extended" );
ESSBOptionsStructureHelper::field_select('social', 'sharing-6', 'ga_tracking_mode', __('Google Analytics Tracking Method', ESSB3_TEXT_DOMAIN), __('Choose your tracking method: Simple - track clicks by social networks, Extended - track clicks on separate social networks by button display position.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-6', 'activate_ga_campaign_tracking', __('Add Custom Campaign parameters to your URLs', ESSB3_TEXT_DOMAIN), __('Paste your custom campaign parameters in this field and they will be automatically added to shared addresses on social networks. Please note as social networks count shares via URL as unique key this option is not compatible with active social share counters as it will make the start from zero.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('social', 'sharing-6', 'essb3_text_analytics', '', '');


ESSBOptionsStructureHelper::field_heading('social', 'sharing-7', 'heading1', __('Social Share Optimization', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_file('social', 'sharing-7', 'sso_default_image', __('Default share image', ESSB3_TEXT_DOMAIN), __('Default share image will be used when page or post doesn\'t have featured image or custom setting for share image.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-7', 'sso_apply_the_content', __('Extract full content when generating description', ESSB3_TEXT_DOMAIN), __('If you see shortcodes in your description activate this option to extract as full rendered content. Warning! Activation of this option may affect work of other plugins.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-7', 'heading2', __('Frontpage settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-7', 'sso_frontpage_title', __('Title', ESSB3_TEXT_DOMAIN), __('Title that will be displayed on frontpage.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-7', 'sso_frontpage_description', __('Description', ESSB3_TEXT_DOMAIN), __('Description that will be displayed on frontpage', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_file('social', 'sharing-7', 'sso_frontpage_image', __('Image', ESSB3_TEXT_DOMAIN), __('Image that will be displayed on frontpage', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-7', 'heading2', __('Facebook Open Graph', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-7', 'opengraph_tags', __('Automatically generate and insert open graph meta tags for post/pages', ESSB3_TEXT_DOMAIN), __('Open Graph meta tags are used to optimize social sharing. This option will include following tags og:title, og:description, og:url, og:image, og:type, og:site_name.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-7', 'opengraph_tags_fbpage', __('Facebook Page URL', ESSB3_TEXT_DOMAIN), __('Provide your Facebook page address.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-7', 'opengraph_tags_fbadmins', __('Facebook Admins', ESSB3_TEXT_DOMAIN), __('Enter IDs of Facebook Users that are admins of current page.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-7', 'opengraph_tags_fbapp', __('Facebook Application ID', ESSB3_TEXT_DOMAIN), __('Enter ID of Facebook Application to be able to use Facebook Insights', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-7', 'opengraph_tags_fbauthor', __('Facebook Author Profile', ESSB3_TEXT_DOMAIN), __('Add link to Facebook profile page of article author if you wish it to appear in shared information.', ESSB3_TEXT_DOMAIN));


ESSBOptionsStructureHelper::field_heading('social', 'sharing-7', 'heading2', __('Twitter Cards', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-7', 'twitter_card', __('Automatically generate and insert Twitter Cards meta tags for post/pages', ESSB3_TEXT_DOMAIN), __('To allow Twitter Cards data appear in your Tweets you need to validate your site after activation of that option in Twitter Card Validator.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'sharing-7', 'twitter_card_user', __('Twitter Site Username', ESSB3_TEXT_DOMAIN), __('Enter your Twitter site username.', ESSB3_TEXT_DOMAIN));
$listOfOptions = array ("summary" => "Summary", "summaryimage" => "Summary with image" );
ESSBOptionsStructureHelper::field_select('social', 'sharing-7', 'twitter_card_type', __('Twitter Card Type', ESSB3_TEXT_DOMAIN), __('Choose the default card type that should be generated.', ESSB3_TEXT_DOMAIN), $listOfOptions);

ESSBOptionsStructureHelper::field_heading('social', 'sharing-7', 'heading2', __('Google Schema.org', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-7', 'sso_google_author', __('Activate Google Authorship and Publisher Markup', ESSB3_TEXT_DOMAIN), __('When active Google Authorship will appear only on posts from your blog - usage of authorship requires you to sign up to Google Authoship program at this address: https://plus.google.com/authorship. Publisher markup will be included on all pages and posts where it is activated.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-7', 'ss_google_author_profile', __('Google+ Author Page', ESSB3_TEXT_DOMAIN), __('Put link to your Goolge+ Profile (example: https://plus.google.com/[Google+_Profile]/posts)', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-7', 'ss_google_author_publisher', __('Google+ Publisher Page', ESSB3_TEXT_DOMAIN), __('Put link to your Google+ Page (example: https://plus.google.com/[Google+_Page_Profile])', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-7', 'sso_google_markup', __('Include Google Schema.org base markup', ESSB3_TEXT_DOMAIN), __('This will include minimal needed markup for Google schema.org (name, description and image)', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));


ESSBOptionsStructureHelper::field_heading('social', 'sharing-8', 'heading1', __('Custom Share Message', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-8', 'customshare', __('Activate custom share message', ESSB3_TEXT_DOMAIN), __('Activate this option to allow usage of custom share message.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-8', 'customshare_text', __('Custom Share Message', ESSB3_TEXT_DOMAIN), __('This option allows you to pass custom message to share (not all networks support this).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'sharing-8', 'customshare_url', __('Custom Share URL', ESSB3_TEXT_DOMAIN), __('This option allows you to pass custom url to share (all networks support this).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_file('social', 'sharing-8', 'customshare_image', __('Custom Share Image', ESSB3_TEXT_DOMAIN), __('This option allows you to pass custom image to your share message (only Facebook and Pinterest support this).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textarea('social', 'sharing-8', 'customshare_description', __('Custom Share Description', ESSB3_TEXT_DOMAIN), __('This option allows you to pass custom extended description to your share message (only Facebok and Pinterest support this).', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-9', 'heading4', __('myCred Integration', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_heading('social', 'sharing-9', 'heading5', __('Award users for clicking on share button', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_panels('social', 'sharing-9', __('Integration via points for click on links', ESSB3_TEXT_DOMAIN), __('Award users for click on share buttons using the build in hook for click on links inside myCred', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('social', 'sharing-9', 'mycred_activate', __('Activate myCred integration for click on links', ESSB3_TEXT_DOMAIN), __('In order to work the myCred integration you need to have myCred Points for click on links hook activated (if you use custom points group you need to activated inside custom points group settings).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-9', 'mycred_points', __('myCred reward points for share link click', ESSB3_TEXT_DOMAIN), __('Provide custom points to reward user when share link. If nothing is provided 1 point will be included.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('social', 'sharing-9', 'mycred_group', __('myCred custom point type', ESSB3_TEXT_DOMAIN), __('Provide custom meta key for the points that user will get to share link. To create your own please visit this tutorial: <a href="http://codex.mycred.me/get-started/multiple-point-types/" target="_blank">http://codex.mycred.me/get-started/multiple-point-types/</a>. Leave blank to use the default (mycred_default)', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('social', 'sharing-9');
ESSBOptionsStructureHelper::field_section_start_panels('social', 'sharing-9', __('Integration via Easy Social Share Buttons hook', ESSB3_TEXT_DOMAIN), __('Award users for click on share buttons using the custom hook for points for social sharing', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('social', 'sharing-9', 'mycred_activate_custom', __('Activate myCred integration for points for social sharing', ESSB3_TEXT_DOMAIN), __('Use Easy Social Share Buttons custom hook in myCred to award points for click on share buttons', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('social', 'sharing-9');
ESSBOptionsStructureHelper::field_heading('social', 'sharing-9', 'heading5', __('Award users when someone uses their share link', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-9', 'mycred_referral_activate', __('Activate myCred Referral usage', ESSB3_TEXT_DOMAIN), __('That option requires you to have the Points for referrals hook enabled. That option is not compatible with share counters because adding referral id to url will reset social share counters to zero.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-9', 'heading4', __('AffiliateWP Integration', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-9', 'affwp_active', __('Append Affiliate ID to shared address', ESSB3_TEXT_DOMAIN), __('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated. You need to have installed AffiliateWP plugin to use it: <a href="https://affiliatewp.com/" target="_blank">https://affiliatewp.com/</a>', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("id" => "User ID", "name" => "Username");
ESSBOptionsStructureHelper::field_select('social', 'sharing-9', 'affwp_active_mode', __('ID Append Mode', ESSB3_TEXT_DOMAIN), __('Choose between usage of user id or username when you add affiliate id to outgoing shares.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('social', 'sharing-9', 'affwp_active_shortcode', __('Append Affiliate ID to custom shared address in shortcodes', ESSB3_TEXT_DOMAIN), __('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated when shortcode has a custom url parameter.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-9', 'affwp_active_pretty', __('Use pretty affiliate URLs', ESSB3_TEXT_DOMAIN), __('Activate this option if you already have make it active inside AffiliateWP to allow Easy Social Share Buttons generate pretty affiliate URLs.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'sharing-9', 'heading4', __('Affiliates Integration', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-9', 'affs_active', __('Append Affiliate ID to shared address', ESSB3_TEXT_DOMAIN), __('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated. You need to have installed Affiliates plugin to use it: <a href="https://wordpress.org/plugins/affiliates/" target="_blank">https://wordpress.org/plugins/affiliates/</a>', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'sharing-9', 'affs_active_shortcode', __('Append Affiliate ID to custom shared address in shortcodes', ESSB3_TEXT_DOMAIN), __('Automatically appends an affiliate\'s ID to Easy Social Share Buttons sharing links that are generated when shortcode has a custom url parameter.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

// native buttons
ESSBOptionsStructureHelper::field_heading('social', 'native-1', 'heading1', __('Native Like, Follow & Subscribe Buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'native_active', __('Activate native buttons', ESSB3_TEXT_DOMAIN), __('Mark yes to activate usage of module.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'otherbuttons_sameline', __('Display on same line', ESSB3_TEXT_DOMAIN), __('Activate this option to display native buttons on same line with the share buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'allow_native_mobile', __('Allow display of native buttons on mobile devices', ESSB3_TEXT_DOMAIN), __('The native buttons are set off by default on mobile devices because they may affect speed of mobile site version. If you wish to use them on mobile devices set this option to <b>Yes</b>.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'allnative_counters', __('Activate native buttons counter', ESSB3_TEXT_DOMAIN), __('Activate this option to display counters for native buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_simplesort('social', 'native-1', 'native_order', __('Drag and Drop change position of display', ESSB3_TEXT_DOMAIN), __('Change order of native button display', ESSB3_TEXT_DOMAIN), essb_default_native_buttons());

ESSBOptionsStructureHelper::field_section_start('social', 'native-1', __('Facebook button', ESSB3_TEXT_DOMAIN), __('Include native Facebook button', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'facebook_like_button', __('Include Facebook Like/Follow Button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'facebook_like_button_api', __('My site already uses Facebook Api', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'facebook_like_button_api_async', __('Load Facebook API asynchronous', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'native-1', 'facebook_like_button_width', __('Set custom width of Facebook like button to fix problem with not rendering correct. Value must be number without px in it.', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'native-1', 'facebook_like_button_height', __('Set custom height of Facebook like button to fix problem with not rendering correct. Value must be number without px in it.', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'native-1', 'facebook_like_button_margin_top', __('Set custom margin-top (to move up use negative value) of Facebook like button to fix problem with not rendering correct. Value must be number without px in it.', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'native-1', 'facebook_like_button_lang', __('Custom language code for you native Facebook button', ESSB3_TEXT_DOMAIN), __('If you wish to change your native Facebook button language code from English you need to enter here your own code like es_ES. Full list of code can be found here: <a href="https://www.facebook.com/translations/FacebookLocales.xml" target="_blank">https://www.facebook.com/translations/FacebookLocales.xml</a>', ESSB3_TEXT_DOMAIN));
$listOfOptions = array ("like" => "Like page", "follow" => "Profile follow" );
ESSBOptionsStructureHelper::field_select('social', 'native-1', 'facebook_like_type', __('Button type', ESSB3_TEXT_DOMAIN), __('Choose button type you wish to use.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'facebook_follow_profile', __('Facebook Follow Profile Page URL', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'custom_url_like_address', __('Custom Facebook like button address', ESSB3_TEXT_DOMAIN), __('Provide custom address in case you wish likes to be added to that page - example fan page. Otherwise likes will be counted to page where button is displayed.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'native-1');

ESSBOptionsStructureHelper::field_section_start('social', 'native-1', __('Google button', ESSB3_TEXT_DOMAIN), __('Include native Google button', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'googleplus', __('Include Google +1/Follow Button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array ("plus" => "+1 to page", "follow" => "Profile follow" );
ESSBOptionsStructureHelper::field_select('social', 'native-1', 'google_like_type', __('Button type', ESSB3_TEXT_DOMAIN), __('Choose button type you wish to use.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'google_follow_profile', __('Google+ Follow Profile Page URL', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'custom_url_plusone_address', __('Custom Google +1 button address', ESSB3_TEXT_DOMAIN), __('Provide custom address in case you wish +1 to be added to that page - example profile page. Otherwise +1s will be counted to page where button is displayed.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'native-1');

ESSBOptionsStructureHelper::field_section_start('social', 'native-1', __('Twitter button', ESSB3_TEXT_DOMAIN), __('Include native Twitter button', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'twitterfollow', __('Twitter Tweet/Follow Button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array ("follow" => "Follow user", "tweet" => "Tweet" );
ESSBOptionsStructureHelper::field_select('social', 'native-1', 'twitter_tweet', __('Button type', ESSB3_TEXT_DOMAIN), __('Choose button type you wish to use.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'twitterfollowuser', __('Twitter Follow User', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'native-1');

ESSBOptionsStructureHelper::field_section_start('social', 'native-1', __('YouTube button', ESSB3_TEXT_DOMAIN), __('Include native YouTube button', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'youtubesub', __('YouTube channel subscribe button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'youtubechannel', __('Channel ID', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'native-1');

ESSBOptionsStructureHelper::field_section_start('social', 'native-1', __('Pinterest button', ESSB3_TEXT_DOMAIN), __('Include native Pinterest button', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'pinterestfollow', __('Include Pinterest Pin/Follow Button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array ("follow" => "Profile follow", "pin" => "Pin button" );
ESSBOptionsStructureHelper::field_select('social', 'native-1', 'pinterest_native_type', __('Button type', ESSB3_TEXT_DOMAIN), __('Choose button type you wish to use.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'pinterestfollow_disp', __('Text on button when follow type is selected', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'pinterestfollow_url', __('Profile url when follow type is selected', ESSB3_TEXT_DOMAIN), __('Provide your Pinterest URL as it is seen at the browser, for example https://www.pinterest.com/appscreo.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'native-1');

ESSBOptionsStructureHelper::field_section_start('social', 'native-1', __('LinkedIn Button', ESSB3_TEXT_DOMAIN), __('Include native LinkedIn company follow button', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'linkedin_follow', __('Include LinkedIn button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'linkedin_follow_id', __('Company ID', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'native-1');

ESSBOptionsStructureHelper::field_section_start('social', 'native-1', __('ManagedWP Button', ESSB3_TEXT_DOMAIN), __('Include native ManagedWP.org Upvote Button', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'managedwp_button', __('Include ManagedWP.org Upvote Button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'native-1');


ESSBOptionsStructureHelper::field_section_start('social', 'native-1', __('VKontankte (vk.com) Like', ESSB3_TEXT_DOMAIN), __('Include native vk.com button', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-1', 'vklike', __('Include VK.com Like Button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-1', 'vklikeappid', __('VKontakte (vk.com) Application ID', ESSB3_TEXT_DOMAIN), __('If you don\'t have application id for your site you need to generate one on VKontakte (vk.com) Dev Site. To do this visit this page http://vk.com/dev.php?method=Like and follow instructions on page', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'native-1');

ESSBOptionsStructureHelper::field_heading('social', 'native-2', 'heading1', __('Skinned native buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-2', 'skin_native', __('Apply native buttons skin', ESSB3_TEXT_DOMAIN), __('This option will hide
		native buttons inside nice flat style boxes and show them on
		hover.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$skin_list = array ("flat" => "Flat", "metro" => "Metro" );
ESSBOptionsStructureHelper::field_select('social', 'native-2', 'skin_native_skin', __('Native buttons skin', ESSB3_TEXT_DOMAIN), __('Choose skin for native buttons. It will be applied only when option above is activated.', ESSB3_TEXT_DOMAIN), $skin_list);

foreach (essb_default_native_buttons() as $network) {
	ESSBOptionsStructureHelper::field_section_start('social', 'native-2', ESSBOptionsStructureHelper::capitalize($network), __('Skinned settings for that social network', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_color('social', 'native-2', 'skinned_'.$network.'_color', __('Skinned button color replace', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_color('social', 'native-2', 'skinned_'.$network.'_hovercolor', __('Skinned button hover color replace', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_color('social', 'native-2', 'skinned_'.$network.'_textcolor', __('Skinned button text color replace', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-2', 'skinned_'.$network.'_text', __('Skinned button text replace', ESSB3_TEXT_DOMAIN), '');
	ESSBOptionsStructureHelper::field_textbox('social', 'native-2', 'skinned_'.$network.'_width', __('Skinned button width replace', ESSB3_TEXT_DOMAIN), '', '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_section_end('social', 'native-2');
}

ESSBOptionsStructureHelper::field_heading('social', 'native-3', 'heading1', __('Social privacy native buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'native-3', 'native_privacy_active', __('Activate social privacy', ESSB3_TEXT_DOMAIN), __('Social Privacy is not compatible with cache plugins or build-in cache module at this stage of development.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
foreach (essb_default_native_buttons() as $network) {
	ESSBOptionsStructureHelper::field_section_start('social', 'native-3', ESSBOptionsStructureHelper::capitalize($network), __('Skinned settings for that social network', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_textbox_stretched('social', 'native-3', 'skinned_'.$network.'_privacy_text', __('Privacy button text replace', ESSB3_TEXT_DOMAIN), '');
	ESSBOptionsStructureHelper::field_textbox('social', 'native-3', 'skinned_'.$network.'_privacy_width', __('Privacy button width replace', ESSB3_TEXT_DOMAIN), '', '', 'input60', 'fa-arrows-h', 'right');
	ESSBOptionsStructureHelper::field_section_end('social', 'native-3');
}


// Followers Counter
ESSBOptionsStructureHelper::field_heading('social', 'follow-1', 'heading1', __('Social Followers Counter', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'follow-1', 'fanscounter_active', __('Activate Social Followers Counter', ESSB3_TEXT_DOMAIN), __('Mark yes to activate usage of module.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('social', 'follow-1', 'essb3fans_update', __('Update period', ESSB3_TEXT_DOMAIN), __('Choose the time when counters will be updated. Default is 1 day if nothing is selected.', ESSB3_TEXT_DOMAIN), ESSBSocialFollowersCounterHelper::available_cache_periods());
ESSBOptionsStructureHelper::field_select('social', 'follow-1', 'essb3fans_format', __('Number format', ESSB3_TEXT_DOMAIN), __('Choose default number format', ESSB3_TEXT_DOMAIN), ESSBSocialFollowersCounterHelper::available_number_formats());
ESSBOptionsStructureHelper::field_switch('social', 'follow-1', 'essb3fans_uservalues', __('Allow user values', ESSB3_TEXT_DOMAIN), __('Activate this option to allow enter of user values for each social network. In this case when automatic value is less than user value the user value will be used', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'follow-1', 'fanscounter_clear_on_save', __('Clear stored values on settings update', ESSB3_TEXT_DOMAIN), __('Mark yes to tell plugin clear previous updated values that are stored into database. You need this option if current stored value in database is greater than your current value of followers and you wish to make it update.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'follow-1', 'heading2', __('Social Networks', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_checkbox_list_sortable('social', 'follow-1', 'essb3fans_networks', __('Social Networks', ESSB3_TEXT_DOMAIN), __('Order and activate networks you wish to use in widget and shortcodes'), ESSBSocialFollowersCounterHelper::available_social_networks(false));
//essb3fans_
if (defined('ESSB3_SFCE_OPTIONS_NAME')) {
	ESSBOptionsStructureHelper::field_heading('social', 'follow-1', 'heading2', __('Social Followers Counter Extended Add-on', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_checkbox_list_sortable('social', 'follow-1', 'essb3fans_networks_extended', __('Extended list of Social Networks', ESSB3_TEXT_DOMAIN), __('Order and activate networks you wish to use in widget and shortcodes that are associated with Social Followers Counter Extended Add-on'), ESSBSocialFollowersCounterHelper::list_of_all_available_networks_extended());	
}

ESSBOptionsStructureHelper::field_heading('social', 'follow-2', 'heading1', __('Social Profile Details', ESSB3_TEXT_DOMAIN));
essb3_draw_fanscounter_settings('social', 'follow-2');


// Social Profiles

ESSBOptionsStructureHelper::field_heading('social', 'profiles-1', 'heading1', __('Social Profile Settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('social', 'profiles-1', __('Style Settings', ESSB3_TEXT_DOMAIN), __('Choose your default social profile style', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("square" => "Square buttons", "round" => "Round buttons", "edge" => "Round edges");
ESSBOptionsStructureHelper::field_select('social', 'profiles-1', 'profiles_button_type', __('Button style', ESSB3_TEXT_DOMAIN), __('Choose your default button style', ESSB3_TEXT_DOMAIN), $listOfOptions);
$listOfOptions = array("fill" => "White icons on colored background", "colored" => "Colored icons");
ESSBOptionsStructureHelper::field_select('social', 'profiles-1', 'profiles_button_fill', __('Button color style', ESSB3_TEXT_DOMAIN), __('Choose your default color style', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('social', 'profiles-1', 'profiles_nospace', __('Remove spacing between buttons', ESSB3_TEXT_DOMAIN), __('Activate this option to remove default space between share buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("small" => "Small", "medium" => "Medium", "large" => "Large");
ESSBOptionsStructureHelper::field_select('social', 'profiles-1', 'profiles_button_size', __('Button size', ESSB3_TEXT_DOMAIN), __('Choose your button size', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_section_end('social', 'profiles-1');

ESSBOptionsStructureHelper::field_section_start('social', 'profiles-1', __('Automatic display of social profiles', ESSB3_TEXT_DOMAIN), __('Activate automatic display of social profiles', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'profiles-1', 'profiles_display', __('Automatic display of profiles', ESSB3_TEXT_DOMAIN), __('Activate this option to display automatically social profiles at selected position of screen', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("left" => "Left", "right" => "Right", "topleft" => "Top left", "topright" => "Top right", "bottomleft" => "Bottom left", "bottomright" => "Bottom right", "widget" => "Display via widget only");
ESSBOptionsStructureHelper::field_select('social', 'profiles-1', 'profiles_display_position', __('Position of social profiles', ESSB3_TEXT_DOMAIN), __('Choose your social profiles position', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('social', 'profiles-1', 'profiles_mobile_deactivate', __('Deactivate social profiles on mobile', ESSB3_TEXT_DOMAIN), __('Activate this option to turn off display on mobile devices.', ESSB3_TEXT_DOMAIN), 'recommended', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'profiles-1');

ESSBOptionsStructureHelper::field_section_start('social', 'profiles-1', __('Text display', ESSB3_TEXT_DOMAIN), __('Activate display of custom text with the icons to social profiles', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'profiles-1', 'profiles_allowtext', __('Display texts', ESSB3_TEXT_DOMAIN), __('Activate this option to display custom texts you enter in settings for each social network.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'profiles-1', 'profiles_width', __('Customize width of button when text is used', ESSB3_TEXT_DOMAIN), __('Provide custom width of buttons for social profiles when text is used (example: 150px or 50%). Leave blank for automatic width.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('social', 'profiles-1');


ESSBOptionsStructureHelper::field_heading('social', 'profiles-1', 'heading2', __('Change the order of social profiles', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_simplesort('social', 'profiles-1', 'profiles_order', __('Social networks', ESSB3_TEXT_DOMAIN), '', ESSBOptionValuesHelper::advanced_array_to_simple_array(essb_available_social_profiles()));

ESSBOptionsStructureHelper::field_heading('social', 'profiles-2', 'heading1', __('Configure social profile addresses', ESSB3_TEXT_DOMAIN));
essb_prepare_social_profiles_fields('social', 'profiles-2');

// after share actions
ESSBOptionsStructureHelper::field_heading('social', 'after-share-1', 'heading1', __('After Social Share Actions', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'after-share-1', 'afterclose_active', __('Activate after social share action', ESSB3_TEXT_DOMAIN), __('Activate this option to start display message after share dialog is closed.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'after-share-1', 'afterclose_deactive_mobile', __('Do not display after social share action for mobile devices', ESSB3_TEXT_DOMAIN), __('Avoid display after share actions on mobile devices', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'after-share-1', 'afterclose_deactive_sharedisable', __('Do not include after share actions code on pages where buttons are deactivated', ESSB3_TEXT_DOMAIN), __('Activate this option if you do not wish code for after share module to be added on pages where buttons are set to be off into settings (via on post/page options or from Display Settings).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$action_types = array ("follow" => "Like/Follow Box", "message" => "Custom html message (for example subscribe form)", "code" => "Custom user code" );
ESSBOptionsStructureHelper::field_select('social', 'after-share-1', 'afterclose_type', __('After close action type', ESSB3_TEXT_DOMAIN), __('Choose your after close action.', ESSB3_TEXT_DOMAIN), $action_types);
ESSBOptionsStructureHelper::field_section_start('social', 'after-share-1', __('Pop up message settings', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '');
ESSBOptionsStructureHelper::field_textbox('social', 'after-share-1', 'afterclose_popup_width', __('Pop up message width', ESSB3_TEXT_DOMAIN), __('Provide custom width in pixels for pop up window (number value with px in it. Example: 400). Default pop up width is 400.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_switch('social', 'after-share-1', 'afterclose_singledisplay', __('Display pop up message once for selected time', ESSB3_TEXT_DOMAIN), __('Activate this option to prevent pop up window display on every page load. This option will make it display once for selected period of days.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'after-share-1', 'afterclose_singledisplay_days', __('Days between pop up message display', ESSB3_TEXT_DOMAIN), __('Provide the value of days when pop up message will appear again. Leave blank for default value of 7 days.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-calendar', 'right');
ESSBOptionsStructureHelper::field_section_end('social', 'after-share-1');

ESSBOptionsStructureHelper::field_heading('social', 'after-share-2', 'heading1', __('Action type: Like/Follow Box', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('social', 'after-share-2', 'afterclose_like_text', __('Text before like/follow buttons', ESSB3_TEXT_DOMAIN), __('Message that will appear before buttons (html supported).', ESSB3_TEXT_DOMAIN), 'htmlmixed');
$col_values = array("onecol" => "1 Column", "twocols" => "2 Columns", "threecols" => "3 Columns");
ESSBOptionsStructureHelper::field_select('social', 'after-share-2', 'afterclose_like_cols', __('Display social profile in the following number of columns', ESSB3_TEXT_DOMAIN), __('Choose the number of columns that social profiles will appear. Please note that using greater value may require increase the pop up window width.', ESSB3_TEXT_DOMAIN), $col_values);
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share-2', 'afterclose_like_fb_like_url', __('Include Facebook Like Button for the following url', ESSB3_TEXT_DOMAIN), __('Provide url address users to like. This can be you Facebook fan page, additional page or any other page you wish users to like.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share-2', 'afterclose_like_fb_follow_url', __('Include Facebook Follow Profile button', ESSB3_TEXT_DOMAIN), __('Provide url address of profile users to follow.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share-2', 'afterclose_like_google_url', __('Include Google +1 button for the following url', ESSB3_TEXT_DOMAIN), __('Provide url address of which you have to get +1.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share-2', 'afterclose_like_google_follow_url', __('Include Google Follow Profile button', ESSB3_TEXT_DOMAIN), __('Provide url address of Google Plus profile users to follow.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'after-share-2', 'afterclose_like_twitter_profile', __('Include Twitter Follow Button', ESSB3_TEXT_DOMAIN), __('Provide Twitter username people to follow (without @)', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'after-share-2', 'afterclose_like_pin_follow_url', __('Include Pinterest Follow Profile button', ESSB3_TEXT_DOMAIN), __('Provide url address to a Pinterest profile.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'after-share-2', 'afterclose_like_youtube_channel', __('Include Youtube Subscribe Channel button', ESSB3_TEXT_DOMAIN), __('Provide your Youtube Channel ID.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'after-share-2', 'afterclose_like_linkedin_company', __('Include LinkedIn Company follow button', ESSB3_TEXT_DOMAIN), __('Provide your LinkedIn company ID.', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('social', 'after-share-3', 'heading1', __('Action type: Custom HTML Message', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('social', 'after-share-3', 'afterclose_message_text', __('Custom html message', ESSB3_TEXT_DOMAIN), __('Put code of your custom message here. This can be subscribe form or anything you wish to display (html supported, shortcodes supported).', ESSB3_TEXT_DOMAIN), 'htmlmixed');

ESSBOptionsStructureHelper::field_heading('social', 'after-share-4', 'heading1', __('Action type: Custom Code', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'after-share-4', 'afterclose_code_always_use', __('Always include custom code', ESSB3_TEXT_DOMAIN), __('Activate this option to make code always be executed even if a different message type is activated', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('social', 'after-share-4', 'afterclose_code_text', __('Custom javascript code', ESSB3_TEXT_DOMAIN), __('Provide your custom javascript code that will be executed (available parameters: oService - social network clicked by user and oPostID for the post where button is clicked).', ESSB3_TEXT_DOMAIN), 'htmlmixed');

//social-metrics
ESSBOptionsStructureHelper::field_heading('social', 'social-metrics', 'heading1', __('Social Metrics Lite', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'social-metrics', 'esml_active', __('Activate Social Metrics Lite', ESSB3_TEXT_DOMAIN), __('Activate Social Metrics Lite to start collect information for social shares.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('social', 'social-metrics', 'essb3_esml_post_type_select', __('Monitor following post types', ESSB3_TEXT_DOMAIN), __('Choose for which
									post types you want to collect information.', ESSB3_TEXT_DOMAIN));

$data_refresh = array ();
$data_refresh ['1'] = '1 hour';
$data_refresh ['2'] = '2 hours';
$data_refresh ['4'] = '4 hours';
$data_refresh ['8'] = '8 hours';
$data_refresh ['12'] = '12 hours';
$data_refresh ['24'] = '24 hours';
$data_refresh ['36'] = '36 hours';
$data_refresh ['48'] = '2 days';
$data_refresh ['72'] = '3 days';
$data_refresh ['96'] = '4 days';
$data_refresh ['120'] = '5 days';
$data_refresh ['168'] = '7 days';
ESSBOptionsStructureHelper::field_select('social', 'social-metrics', 'esml_ttl', __('Data refresh time', ESSB3_TEXT_DOMAIN), __('Length of time to store
									the statistics locally before downloading new data. A lower
									value will use more server resources. High values are
									recommended for blogs with over 50 posts.', ESSB3_TEXT_DOMAIN), $data_refresh);

$provider = array ();
$provider ['sharedcount'] = 'using sharedcount.com service';
$provider ['self'] = 'from my WordPress site with call to each social network';
ESSBOptionsStructureHelper::field_select('social', 'social-metrics', 'esml_provider', __('Choose update provider', ESSB3_TEXT_DOMAIN), __('Choose default metrics update
									provider. You can use sharedcount.com where all data is
									extracted with single call. According to high load of
									sharedcount you can use the another update method with native
									calls from your WordPress instance.', ESSB3_TEXT_DOMAIN), $provider);
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'social-metrics', 'esml_sharedcount_api', __('SharedCount.com API key', ESSB3_TEXT_DOMAIN), __('Provide your free SharedCount.com API access key. To get this key create account in SharedCount.com (in case you do not have such) and login into <a href="https://admin.sharedcount.com/admin/user/home.php" target="_blank">your profile settings</a> to find it.', ESSB3_TEXT_DOMAIN));

$listOfOptions = array("manage_options" => "Administrator", "delete_pages" => "Editor", "publish_posts" => "Author", "edit_posts" => "Contributor");
ESSBOptionsStructureHelper::field_select('social', 'social-metrics', 'esml_access', __('Plugin access', ESSB3_TEXT_DOMAIN), __('Make settings available for the following user roles (if you use multiple user roles on your site we recommend to select Administrator to disallow other users change settings of plugin).', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_switch('social', 'social-metrics', 'esml_top_posts_widget', __('Activate top social posts widget', ESSB3_TEXT_DOMAIN), __('Activate usage of top social posts widget. Widget requires Social Metrics Lite to be active for data update.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));


//shorturl
ESSBOptionsStructureHelper::field_heading('social', 'shorturl', 'heading1', __('Short URL', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('social', 'shorturl', 'shorturl_activate', __('Activate usage of short url for all social networks', ESSB3_TEXT_DOMAIN), __('Using shortlinks will generate unique shortlinks for pages/posts. If you have shared till now full address of you current post/page using shortlink will make counters of sharing to start from 0.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('social', 'shorturl', 'twitter_shareshort', __('Generate short urls for', ESSB3_TEXT_DOMAIN), __('Select networks you wish to generate short urls for - you can choose from recommended networks (Twitter, Mobile Messengers) or all social networks'), array( "true" => "Recommended social networks only (Twitter, Mobile Messengers)", "false" => "All social networks"));

$listOfOptions = array("wp" => "Build in WordPress function wp_get_shortlink()", "goo.gl" => "goo.gl", "bit.ly" => "bit.ly");
if (defined('ESSB3_SSU_VERSION')) {
	$listOfOptions['ssu'] = "Self-Short URL Add-on for Easy Social Share Buttons";
}

ESSBOptionsStructureHelper::field_select('social', 'shorturl', 'shorturl_type', __('Choose short url type', ESSB3_TEXT_DOMAIN), __('Please note that usage of bit.ly requires to fill additional fields below or short urls will not be generated. If you choose goo.gl as provider we also recommend to generate API key due to public quota limit provided by Google.'), $listOfOptions);
ESSBOptionsStructureHelper::field_heading('social', 'shorturl', 'heading5', __('bit.ly Configuration', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('social', 'shorturl', 'shorturl_bitlyuser', __('bit.ly Username', ESSB3_TEXT_DOMAIN), __('Provide your bit.ly username', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_bitlyapi', __('bit.ly API key/Access token key', ESSB3_TEXT_DOMAIN), __('Provide your bit.ly API key', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select('social', 'shorturl', 'shorturl_bitlyapi_version', __('bit.ly API version', ESSB3_TEXT_DOMAIN), __('Choose version of bit.ly API you will use. We recommend to switch to new bit.ly API with access token'), array( "previous" => "Old API version with Username and Access Key", "new" => "New API with access token"));
ESSBOptionsStructureHelper::field_heading('social', 'shorturl', 'heading5', __('goo.gl Configuration', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('social', 'shorturl', 'shorturl_googlapi', __('goo.gl API key', ESSB3_TEXT_DOMAIN), __('Goo.gl short url service can work with or without API key. If you have a high traffic site it is recommended to use API key because when anonymous usage reach amount of request for time you will not get short urls. To generate such key you need to visit <a href="https://console.developers.google.com/project" target="_blank">Google Developer Console</a>', ESSB3_TEXT_DOMAIN));


// ------------------------------------------------------
// Display Settings
// ------------------------------------------------------

ESSBOptionsStructureHelper::field_heading('display', 'settings-1', 'heading1', __('Choose post types to display buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('display', 'settings-1', 'essb3_post_type_select', __('Where to display buttons', ESSB3_TEXT_DOMAIN), __('Choose post types where you wish buttons to appear. If you are running WooCommerce store you can choose between post type Products which will display share buttons into product description or option to display buttons below price.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('display', 'settings-1', __('Display in post excerpt', ESSB3_TEXT_DOMAIN), __('Activate this option if your theme is using excerpts and you wish to display share buttons in excerpts', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-1', 'display_excerpt', __('Activate', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("top" => "Before excerpt", "bottom" => "After excerpt");
ESSBOptionsStructureHelper::field_select('display', 'settings-1', 'display_excerpt_pos', __('Buttons position in excerpt', ESSB3_TEXT_DOMAIN), __(''), $listOfOptions);
ESSBOptionsStructureHelper::field_section_end('display', 'settings-1');

ESSBOptionsStructureHelper::field_heading('display', 'settings-1', 'heading5', __('Deactivate display on', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('display', 'settings-1', 'display_exclude_from', __('Exclude automatic display on', ESSB3_TEXT_DOMAIN), __('Exclude buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". This will deactivate automated display of buttons on selected posts/pages but you are able to use shortcode on them.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('display', 'settings-1', 'display_deactivate_on', __('Deactivate plugin on', ESSB3_TEXT_DOMAIN), __('Deactivate buttons on posts/pages with these IDs. Comma separated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts. Plugin also allows to deactivate only specific functions on selected page/post ids. <a href="'.admin_url('admin.php?page=essb_redirect_advanced&tab=advanced&section=deactivate&subsection').'">Click here</a> to to that settings page.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-1', 'deactivate_homepage', __('Deactivate buttons display on homepage', ESSB3_TEXT_DOMAIN), __('Exclude display of buttons on home page.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
//ESSBOptionsStructureHelper::field_switch('display', 'settings-1', 'deactivate_lists', __('Deactivate load of plugin resources on list of articles', ESSB3_TEXT_DOMAIN), __('Activate this option to stop load plugin resources on list of articles.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('display', 'settings-1', 'heading5', __('Automatic display on', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('display', 'settings-1', 'display_include_on', __('Automatic display buttons on', ESSB3_TEXT_DOMAIN), __('Provide list of post/page ids where buttons will display no matter that post type is active or not. Comma seperated values: "11, 15, 125". This will eactivate automated display of buttons on selected posts/pages even if post type that they use is not marked as active.', ESSB3_TEXT_DOMAIN));


ESSBOptionsStructureHelper::field_heading('display', 'settings-2', 'heading1', __('WooCommerce', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-2', 'woocommece_share', __('Display buttons after product price', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-2', 'woocommece_beforeprod', __('Display buttons on top of product (before product)', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-2', 'woocommece_afterprod', __('Display buttons at the bottom of product (after product)', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('display', 'settings-3', 'heading1', __('WP e-Commerce', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-3', 'wpec_before_desc', __('Display before product description', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-3', 'wpec_after_desc', __('Display after product description', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-3', 'wpec_theme_footer', __('Display at the bottom of page', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('display', 'settings-4', 'heading1', __('JigoShop', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-4', 'jigoshop_top', __('JigoShop Before Product', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-4', 'jigoshop_bottom', __('JigoShop After Product', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('display', 'settings-5', 'heading1', __('iThemes Exchange', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-5', 'ithemes_after_title', __('Display after product title', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-5', 'ithemes_before_desc', __('Display before product description', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-5', 'ithemes_after_desc', __('Display after product description', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-5', 'ithemes_after_product', __('Display after product advanced content (after product information)', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-5', 'ithemes_after_purchase', __('Display share buttons for each product after successful purchase (when shopping cart is used)', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('display', 'settings-6', 'heading1', __('bbPress ', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-6', 'bbpress_forum', __('Display in forums', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-6', 'bbpress_topic', __('Display in topics', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('display', 'settings-7', 'heading1', __('BuddyPress', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-7', 'buddypress_activity', __('Display in activity', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'settings-7', 'buddypress_group', __('Display on group page', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));


// dispaly position
ESSBOptionsStructureHelper::field_heading('display', 'positions-1', 'heading1', __('Display Positions', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_image_radio('display', 'positions-1', 'content_position', __('Primary content display position', ESSB3_TEXT_DOMAIN), __('Choose default method that will be used to render buttons inside content', ESSB3_TEXT_DOMAIN), essb_avaliable_content_positions());
ESSBOptionsStructureHelper::field_image_checkbox('display', 'positions-1', 'button_position', __('Additional button display positions', ESSB3_TEXT_DOMAIN), __('Choose additional display methods that can be used to display buttons.', ESSB3_TEXT_DOMAIN), essb_available_button_positions());

ESSBOptionsStructureHelper::field_switch('display', 'positions-1', 'positions_by_pt', __('I wish to have different button position for different post types', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to setup different positions for each post type. Once you change state of this option press update settings button.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

// display location settings
ESSBOptionsStructureHelper::field_heading('display', 'positions-3', 'heading1', __('Display Position Settings: Content top', ESSB3_TEXT_DOMAIN));
essb_prepare_location_advanced_customization('display', 'positions-3', 'top');

ESSBOptionsStructureHelper::field_heading('display', 'positions-4', 'heading1', __('Display Position Settings: Content bottom', ESSB3_TEXT_DOMAIN));
essb_prepare_location_advanced_customization('display', 'positions-4', 'bottom');

ESSBOptionsStructureHelper::field_heading('display', 'positions-5', 'heading1', __('Display Position Settings: Float from top', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-5', __('Set custom top position of float bar', ESSB3_TEXT_DOMAIN), __('If your current theme has fixed bar or menu you may need to provide custom top position of float or it will be rendered below this sticked bar. For example you can try with value 40 (which is equal to 40px from top).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-5', 'float_top', __('Top position for non logged in users', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-5', 'float_top_loggedin', __('Top position for logged in users', ESSB3_TEXT_DOMAIN), __('If you display WordPress admin bar for logged in users you can correct float from top position for logged in users to avoid bar to be rendered below WordPress admin bar.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-5', 'float_top_disappear', __('Hide buttons after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('Provide value in percent if you wish to hide float bar - for example 80 will make bar to disappear when 80% of page content is viewed from user.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-sort-numeric-asc', 'right');
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-5');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-5', __('Background color', ESSB3_TEXT_DOMAIN), __('Change default background color of float bar (default is white #FFFFFF).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-5', 'float_bg', __('Choose background color', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-5', 'float_bg_opacity', __('Change opacity of background color', ESSB3_TEXT_DOMAIN), __('Change default opacity of background color if you wish to have a semi-transparent effect (default is 1 full color). You can enter value between 0 and 1 (example: 0.7)', ESSB3_TEXT_DOMAIN), '', 'input60');
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-5');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-5', __('Width and positioning settings', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-5', 'float_full', __('Set full width of float bar', ESSB3_TEXT_DOMAIN), __('This option will make float bar to take full width of browser window.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-5', 'float_full_maxwidth', __('Max width of buttons area', ESSB3_TEXT_DOMAIN), __('Provide custom max width of buttons area when full width float bar is active. Provide number value in pixels without the px (example 960)', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-5', 'float_remove_margin', __('Remove top space', ESSB3_TEXT_DOMAIN), __('This option will clear the blank space that may appear according to theme settings between top of window and float bar.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-5');

essb_prepare_location_advanced_customization('display', 'positions-5', 'float');

ESSBOptionsStructureHelper::field_heading('display', 'positions-6', 'heading1', __('Display Position Settings: Post Vertical Float', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_full_panels('display', 'positions-6');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-6', 'postfloat_initialtop', __('Custom top position of post float bar when loaded', ESSB3_TEXT_DOMAIN), __('Customize the initial top position of post float bar if you wish to be different from content start.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-6', 'postfloat_top', __('Top position of post float buttons when they are fixed', ESSB3_TEXT_DOMAIN), __('Filled value to change the top position if you have another fixed element (example: fixed menu).', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-6', 'postfloat_marginleft', __('Horizontal offset from content', ESSB3_TEXT_DOMAIN), __('You can provide custom left offset from content. Leave blank to use default value.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-6', 'postfloat_margintop', __('Vertical offset from content start', ESSB3_TEXT_DOMAIN), __('You can provide custom vertical offset from content start. Leave blank to use default value. (Negative values moves up, positve moves down).', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-6', 'postfloat_percent', __('Display after percent of content is passed', ESSB3_TEXT_DOMAIN), __('Provide percent of content to viewed when buttons will appear (default state if this field is provided will be hidden for that display method).', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-6', 'postfloat_always_visible', __('Do not hide post vertical float at the end of content', ESSB3_TEXT_DOMAIN), __('Activate this option to make post vertical float stay on screen when end of post content is reached.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_full_panels('display', 'positions-6');
essb_prepare_location_advanced_customization('display', 'positions-6', 'postfloat');

ESSBOptionsStructureHelper::field_heading('display', 'positions-7', 'heading1', __('Display Position Settings: Sidebar', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("" => "Left", "right" => "Right");
ESSBOptionsStructureHelper::field_select('display', 'positions-7', 'sidebar_pos', __('Sidebar Appearance', ESSB3_TEXT_DOMAIN), __('You choose different position for sidebar. Available options are Left (default), Right', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_section_start_full_panels('display', 'positions-7', __('Left or Right sidebar appearance options', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-7', 'sidebar_fixedtop', __('Fixed top position of sidebar', ESSB3_TEXT_DOMAIN), __('You can provide custom top position of sidebar in pixels or percents (ex: 100px, 15%).', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-7', 'sidebar_leftright_percent', __('Display after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('If you wish to make sidebar appear after percent of content is viewed enter value here (leave blank to appear immediately after load).', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-7', 'sidebar_leftright_percent_hide', __('Hide after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('If you wish to make sidebar disappear after percent of content is viewed enter value here (leave blank to make it always be visible).', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-7', 'sidebar_leftright_close', __('Add close sidebar button', ESSB3_TEXT_DOMAIN), __('Activate that option to add a close sidebar button.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_full_panels('display', 'positions-7');
essb_prepare_location_advanced_customization('display', 'positions-7', 'sidebar');

// top bar
ESSBOptionsStructureHelper::field_heading('display', 'positions-8', 'heading1', __('Display Position Settings: Top bar', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-8', __('Top bar appearance', ESSB3_TEXT_DOMAIN), __('If your current theme has fixed bar or menu you may need to provide custom top position of top bar or it will be rendered below this sticked bar. For example you can try with value 40 (which is equal to 40px from top).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-8', 'topbar_top', __('Top position for non logged in users', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-8', 'topbar_top_loggedin', __('Top position for logged in users', ESSB3_TEXT_DOMAIN), __('f you display WordPress admin bar for logged in users you can correct float from top position for logged in users to avoid bar to be rendered below WordPress admin bar.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-8', 'topbar_top_onscroll', __('Appear after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('If you wish top bar to appear when user starts scrolling fill here percent of conent after is viewed it will be visible.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-8', 'topbar_hide', __('Hide buttons after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('Provide value in percent if you wish to hide float bar - for example 80 will make bar to disappear when 80% of page content is viewed from user.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-sort-numeric-asc', 'right');
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-8');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-8', __('Background color', ESSB3_TEXT_DOMAIN), __('Change default background color of top bar (default is white #FFFFFF).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-8', 'topbar_bg', __('Choose background color', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-8', 'topbar_bg_opacity', __('Change opacity of background color', ESSB3_TEXT_DOMAIN), __('Change default opacity of background color if you wish to have a semi-transparent effect (default is 1 full color). You can enter value between 0 and 1 (example: 0.7)', ESSB3_TEXT_DOMAIN), '', 'input60');
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-8');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-8', __('Top bar width, height & placement', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-8', 'topbar_height', __('Height of top bar content area', ESSB3_TEXT_DOMAIN), __('Provide custom height of content area. Provide number value in pixels without the px (example 40). Leave blank for default height.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-8', 'topbar_maxwidth', __('Max width of content area', ESSB3_TEXT_DOMAIN), __('Provide custom max width of content area. Provide number value in pixels without the px (example 960). Leave blank for full width.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
$listOfOptions = array("left" => "Left", "center" => "Center", "right" => "Right");
ESSBOptionsStructureHelper::field_select_panel('display', 'positions-8', 'topbar_buttons_align', __('Align buttons', ESSB3_TEXT_DOMAIN), __('Choose your button alignment', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-8');
ESSBOptionsStructureHelper::field_heading('display', 'positions-8', 'heading4', __('Top bar custom content settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_full_panels('display', 'positions-8', __('Background color', ESSB3_TEXT_DOMAIN), __('Change default background color of top bar (default is white #FFFFFF).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-8', 'topbar_contentarea', __('Activate custom content area', ESSB3_TEXT_DOMAIN), __('Activate that option to add an extra field with content.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("left" => "Left", "right" => "Right");
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-8', 'topbar_contentarea_width', __('Custom content area % width', ESSB3_TEXT_DOMAIN), __('Provide custom width of content area (default value if nothing is filled is 30 which means 30%). Fill number value without % mark - example 40.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_select_panel('display', 'positions-8', 'topbar_contentarea_pos', __('Custom content area position', ESSB3_TEXT_DOMAIN), __('Choose your content area alignment', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_section_end_full_panels('display', 'positions-8');
ESSBOptionsStructureHelper::field_wpeditor('display', 'positions-8', 'topbar_usercontent', __('Custom content', ESSB3_TEXT_DOMAIN), '', 'htmlmixed');

//ESSBOptionsStructureHelper::field_section_end('display', 'positions-8');

essb_prepare_location_advanced_customization('display', 'positions-8', 'topbar');

// bottom bar
ESSBOptionsStructureHelper::field_heading('display', 'positions-9', 'heading1', __('Display Position Settings: Bottom bar', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-9', __('Bottom bar appearance', ESSB3_TEXT_DOMAIN), __('Use to fit the buttons to the style of your footer area.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-9', 'bottombar_top_onscroll', __('Appear after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('If you wish bottom bar to appear when user starts scrolling fill here percent of conent after is viewed it will be visible.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-9', 'bottombar_hide', __('Hide buttons after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('Provide value in percent if you wish to hide float bar - for example 80 will make bar to disappear when 80% of page content is viewed from user.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-sort-numeric-asc', 'right');
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-9');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-9', __('Background color', ESSB3_TEXT_DOMAIN), __('Change default background color of bottom bar (default is white #FFFFFF).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-9', 'bottombar_bg', __('Choose background color', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-9', 'bottombar_bg_opacity', __('Change opacity of background color', ESSB3_TEXT_DOMAIN), __('Change default opacity of background color if you wish to have a semi-transparent effect (default is 1 full color). You can enter value between 0 and 1 (example: 0.7)', ESSB3_TEXT_DOMAIN), '', 'input60');
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-9');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-9', __('Bottom bar width, height & placement', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-9', 'bottombar_height', __('Height of top bar content area', ESSB3_TEXT_DOMAIN), __('Provide custom height of content area. Provide number value in pixels without the px (example 40). Leave blank for default value.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-9', 'bottombar_maxwidth', __('Max width of content area', ESSB3_TEXT_DOMAIN), __('Provide custom max width of content area. Provide number value in pixels without the px (example 960). Leave blank for full width.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
$listOfOptions = array("left" => "Left", "center" => "Center", "right" => "Right");
ESSBOptionsStructureHelper::field_select_panel('display', 'positions-9', 'bottombar_buttons_align', __('Align buttons', ESSB3_TEXT_DOMAIN), __('Choose your content area alignment', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-9');

ESSBOptionsStructureHelper::field_heading('display', 'positions-9', 'heading4', __('Bottom bar custom content settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_full_panels('display', 'positions-9', __('Bottom bar content settings', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-9', 'bottombar_contentarea', __('Activate custom content area', ESSB3_TEXT_DOMAIN), __('Activate that option to add a close sidebar button.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("left" => "Left", "right" => "Right");
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-9', 'bottombar_contentarea_width', __('Custom content area % width', ESSB3_TEXT_DOMAIN), __('Provide custom width of content area (default value if nothing is filled is 30 which means 30%). Fill number value without % mark - example 40.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_select_panel('display', 'positions-9', 'bottombar_contentarea_pos', __('Custom content area position', ESSB3_TEXT_DOMAIN), __('Choose your button alignment', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_section_end_full_panels('display', 'positions-9');
ESSBOptionsStructureHelper::field_wpeditor('display', 'positions-9', 'bottombar_usercontent', __('Custom content', ESSB3_TEXT_DOMAIN), '', 'htmlmixed');

essb_prepare_location_advanced_customization('display', 'positions-9', 'bottombar');


ESSBOptionsStructureHelper::field_heading('display', 'positions-10', 'heading1', __('Display Position Settings: Pop up', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('display', 'positions-10', __('Pop up window settings', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('display', 'positions-10', 'popup_window_title', __('Pop up window title', ESSB3_TEXT_DOMAIN), __('Set your custom pop up window title.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('display', 'positions-10', 'popup_user_message', __('Pop up window message', ESSB3_TEXT_DOMAIN), __('Set your custom message that will appear above buttons', ESSB3_TEXT_DOMAIN), "htmlmixed");
ESSBOptionsStructureHelper::field_textbox('display', 'positions-10', 'popup_user_width', __('Pop up window width', ESSB3_TEXT_DOMAIN), __('Set your custom window width (default is 800 or window width - 60). Value if provided should be numeric without px symbols.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_section_end('display', 'positions-10');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-10', __('Pop up window display', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-10', 'popup_window_popafter', __('Display pop up window after (sec)', ESSB3_TEXT_DOMAIN), __('If you wish pop up window to appear after amount of seconds you can provide theme here. Leave blank for immediate pop up after page load.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-clock-o', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-10', 'popup_user_percent', __('Display pop up window after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('Set amount of page content after which the pop up will appear.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-10', 'popup_display_end', __('Display pop up at the end of content', ESSB3_TEXT_DOMAIN), __('Automatically display pop up when the content end is reached', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-10', 'popup_display_exit', __('Display pop up on exit intent', ESSB3_TEXT_DOMAIN), __('Automatically display pop up when exit intent is detected', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-10', 'popup_display_comment', __('Display pop up on user comment', ESSB3_TEXT_DOMAIN), __('Automatically display pop up when user leave a comment.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-10', 'popup_display_purchase', __('Display pop up after WooCommerce purchase', ESSB3_TEXT_DOMAIN), __('Display on Thank You page of WooCommerce after purchase', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-10', 'popup_user_manual_show', __('Manual window display mode', ESSB3_TEXT_DOMAIN), __('Activating manual display mode will allow you to show window when you decide with calling following javascript function essb_popup_show();', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-10', 'popup_avoid_logged_users', __('Do not show pop up for logged in users', ESSB3_TEXT_DOMAIN), __('Activate this option to avoid display of pop up when user is logged in into site.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-10');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-10', __('Pop up window close', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-10', 'popup_window_close_after', __('Automatically close pop up after (sec)', ESSB3_TEXT_DOMAIN), __('You can provide seconds and after they expire window will close automatically. User can close this window manually by pressing close button.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-10', 'popup_user_autoclose', __('Close up message customize', ESSB3_TEXT_DOMAIN), __('Set custom text announcement for closing the pop up. After your text there will be timer counting the seconds leaving.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-10', 'popup_user_notshow_onclose', __('After user close window do not show it again on this page/post for him', ESSB3_TEXT_DOMAIN), __('Activating this option will set cookie that will not show again pop up message for next 7 days for user on this post/page', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-10', 'popup_user_notshow_onclose_all', __('After user close window do not show it again on all page/post for him', ESSB3_TEXT_DOMAIN), __('Activating this option will set cookie that will not show again pop up message for next 7 days for user on all posts/pages', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-10');
essb_prepare_location_advanced_customization('display', 'positions-10', 'popup');

ESSBOptionsStructureHelper::field_heading('display', 'positions-11', 'heading1', __('Display Position Settings: Fly In', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('display', 'positions-11', __('Fly In window settings', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('display', 'positions-11', 'flyin_window_title', __('Fly in window title', ESSB3_TEXT_DOMAIN), __('Set your custom fly in window title.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('display', 'positions-11', 'flyin_user_message', __('Fly in window message', ESSB3_TEXT_DOMAIN), __('Set your custom message that will appear above buttons', ESSB3_TEXT_DOMAIN), "htmlmixed");
ESSBOptionsStructureHelper::field_textbox('display', 'positions-11', 'flyin_user_width', __('Fly in window width', ESSB3_TEXT_DOMAIN), __('Set your custom window width (default is 400 or window width - 60). If value is provided should be numeric without px symbols.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
$listOfOptions = array("right" => "Right", "left" => "Left");
ESSBOptionsStructureHelper::field_select('display', 'positions-11', 'flyin_position', __('Choose fly in display position', ESSB3_TEXT_DOMAIN), '', $listOfOptions);
ESSBOptionsStructureHelper::field_section_end('display', 'positions-11');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-11', __('Fly in window display', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-11', 'flyin_window_popafter', __('Display fly in window after (sec)', ESSB3_TEXT_DOMAIN), __('If you wish fly in window to appear after amount of seconds you can provide them here. Leave blank for immediate pop up after page load.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-clock-o', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-11', 'flyin_user_percent', __('Display fly in window after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('Set amount of page content after which the pop up will appear.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-11', 'flyin_display_end', __('Display fly in at the end of content', ESSB3_TEXT_DOMAIN), __('Automatically display fly in when the content end is reached.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-11', 'flyin_display_comment', __('Display fly in on user comment', ESSB3_TEXT_DOMAIN), __('Automatically display fly in when user leaves a comment.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-11', 'flyin_user_manual_show', __('Manual fly in display mode', ESSB3_TEXT_DOMAIN), __('Activating manual display mode will allow you to show window when you decide with calling following javascript function essb_flyin_show();', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-11');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-11', __('Fly in window close', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-11', 'flyin_window_close_after', __('Automatically close fly in after (sec)', ESSB3_TEXT_DOMAIN), __('You can provide seconds and after they expire window will close automatically. User can close this window manually by pressing close button.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-11', 'flyin_user_autoclose', __('Close up message customize', ESSB3_TEXT_DOMAIN), __('Set custom text announcement for closing the fly in. After your text there will be timer counting the seconds leaving.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-11', 'flyin_user_notshow_onclose', __('After user closes window do not show it again on this page/post for him', ESSB3_TEXT_DOMAIN), __('Activating this option will set cookie that will not show again pop up message for next 7 days for user on this post/page', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-11', 'flyin_user_notshow_onclose_all', __('After user close window do not show it again on all page/post for him', ESSB3_TEXT_DOMAIN), __('Activating this option will set cookie that will not show again pop up message for next 7 days for user on all posts/pages', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-11');
ESSBOptionsStructureHelper::field_switch('display', 'positions-11', 'flyin_noshare', __('Do not show share buttons in fly in', ESSB3_TEXT_DOMAIN), __('Activating this you will get a fly in display without share buttons in it - only the custom content you have set.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
essb_prepare_location_advanced_customization('display', 'positions-11', 'flyin');

ESSBOptionsStructureHelper::field_heading('display', 'positions-12', 'heading1', __('Display Position Settings: On Media', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('display', 'positions-12', __('Appearance', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('display', 'positions-12', 'sis_selector', __('Default selector', ESSB3_TEXT_DOMAIN), __('Selectors for images. Separate several selectors with commas.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('display', 'positions-12', 'sis_dontshow', __('Do not show on', ESSB3_TEXT_DOMAIN), __('Set image classes and IDs for which on media display buttons won\'t show. Separate several selectors with commas.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('display', 'positions-12', 'sis_dontaddclass', __('Do not move following classes', ESSB3_TEXT_DOMAIN), __('Provide image classes that you wish not to be moved to on media sharing element. If you use multiple selectors separate them with ,', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('display', 'positions-12', 'sis_minWidth', __('Minimal width', ESSB3_TEXT_DOMAIN), __('Minimum width of image for sharing. Use value without px.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('display', 'positions-12', 'sis_minHeight', __('Minimal height', ESSB3_TEXT_DOMAIN), __('Minimum height of image for sharing. Use value without px.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('display', 'positions-12');

ESSBOptionsStructureHelper::field_section_start('display', 'positions-12', __('Social Networks', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
$listOfNetworks = array( "facebook", "twitter", "google", "linkedin", "pinterest", "tumblr", "reddit", "digg", "delicious", "vkontakte", "odnoklassniki");
$listOfNetworksAdvanced = array( "facebook" => "Facebook", "twitter" => "Twitter", "google" => "Google", "linkedin" => "LinkedIn", "pinterest" => "Pinterest", "tumblr" => "Tumblr", "reddit" => "Reddit", "digg" => "Digg", "delicious" => "Delicious", "vkontakte" => "VKontakte", "odnoklassniki" => "Odnoklassniki");
ESSBOptionsStructureHelper::field_checkbox_list('display', 'positions-12', 'sis_networks', __('Activate networks', ESSB3_TEXT_DOMAIN), __('Choose active social networks', ESSB3_TEXT_DOMAIN), $listOfNetworksAdvanced);
ESSBOptionsStructureHelper::field_simplesort('display', 'positions-12', 'sis_network_order', __('Display order', ESSB3_TEXT_DOMAIN), __('Arrange network appearance using drag and drop', ESSB3_TEXT_DOMAIN), $listOfNetworks);
ESSBOptionsStructureHelper::field_section_end('display', 'positions-12');
ESSBOptionsStructureHelper::field_section_start('display', 'positions-12', __('Share Options', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'positions-12', 'sis_on_mobile', __('Enable on mobile', ESSB3_TEXT_DOMAIN), __('Enable image sharing on mobile devices', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'positions-12', 'sis_sharer', __('Share selected image<div class="essb-new"><span></span></div><div class="essb-beta"><span></span></div>', ESSB3_TEXT_DOMAIN), __('Activate this option to make plugin include selected image into share. Please note that activating that option will make share counter not to include that shares in it because url structure will change.<br/><br/>Please note that if you have long descriptions, titles or urls you will need <a href="http://appscreo.com/self-hosted-short-urls/" target="_blank"><b>Self-Hosted Short URLs add-on</b></a> for proper sharing.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'positions-12', 'sis_pinterest_alt', __('Use provided image alternative text for Pinterest share<div class="essb-new"><span></span></div><div class="essb-beta"><span></span></div>', ESSB3_TEXT_DOMAIN), __('Activate this option to allow Pinterest share take image alternative text as share description. If no alternative texts is provided it will use post title. If this option is not active Pinterest share will use post title.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('display', 'positions-12');

ESSBOptionsStructureHelper::field_section_start('display', 'positions-12', __('Display Options', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'positions-12', 'sis_always_show', __('Always visible', ESSB3_TEXT_DOMAIN), __('Activate this option to make image share buttons be always visible on images.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

$listOfTemplates = array("flat-small" => "Small", "flat" => "Regular", "box" => "Boxed", "round" => "Round");
ESSBOptionsStructureHelper::field_select('display', 'positions-12', 'sis_style', __('Template', ESSB3_TEXT_DOMAIN), '', $listOfTemplates);
$listOfOptions = array("left" => "Left", "right" => "Right", "center-x" => "Center");
ESSBOptionsStructureHelper::field_select('display', 'positions-12', 'sis_align_x', __('Horizontal Align', ESSB3_TEXT_DOMAIN), '', $listOfOptions);
$listOfOptions = array("top" => "Top", "bottom" => "Bottom", "center-y" => "Center");
//ESSBOptionsStructureHelper::field_select('display', 'positions-30', 'sis_align_y', __('Vertical Align', ESSB3_TEXT_DOMAIN), '', $listOfOptions);
$listOfOptions = array("horizontal" => "Horizontal", "vertical" => "Vertical");
ESSBOptionsStructureHelper::field_select('display', 'positions-12', 'sis_orientation', __('Orientation', ESSB3_TEXT_DOMAIN), '', $listOfOptions);
ESSBOptionsStructureHelper::field_textbox('display', 'positions-12', 'sis_offset_x', __('Move buttons horizontally', ESSB3_TEXT_DOMAIN), __('Provide custom value if you wish to move buttons horizontally from the edge of image', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('display', 'positions-12', 'sis_offset_y', __('Move buttons vertically', ESSB3_TEXT_DOMAIN), __('Provide custom value if you wish to move buttons vertically from the edge of image.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('display', 'positions-12');


ESSBOptionsStructureHelper::field_heading('display', 'positions-13', 'heading1', __('Display Position Settings: Full screen hero share', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('display', 'positions-13', 'heroshare_user_width', __('Custom window width', ESSB3_TEXT_DOMAIN), __('Set your custom window width (default is 960 or window width - 60). Value if provided should be numeric without px symbols.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
ESSBOptionsStructureHelper::field_section_start('display', 'positions-13', __('Primary content area', ESSB3_TEXT_DOMAIN), __('Primary content area is located above post information and share details. You can use it to add custom title or message that will appear on top. Leave it blank if you do not wish to have such', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('display', 'positions-13', 'heroshare_window_title', __('Window title', ESSB3_TEXT_DOMAIN), __('Set your custom pop up window title.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('display', 'positions-13', 'heroshare_user_message', __('Window message', ESSB3_TEXT_DOMAIN), __('Set your custom message that will appear above buttons', ESSB3_TEXT_DOMAIN), "htmlmixed");
ESSBOptionsStructureHelper::field_section_end('display', 'positions-13');

ESSBOptionsStructureHelper::field_section_start('display', 'positions-13', __('Additional content area', ESSB3_TEXT_DOMAIN), __('Additional content area is located below share buttons and provide various message types. If you do not wish to display it choose data type to html message and leave field for message blank', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('display', 'positions-13', 'heroshare_second_title', __('Title', ESSB3_TEXT_DOMAIN), __('Set your custom pop up window title for additional content area.', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("top" => "Top social posts (require build in analytics to be active)", "fans" => "Followers counter (require followers counter to be activated)", "html" => "Custom HTML message");
ESSBOptionsStructureHelper::field_select('display', 'positions-13', 'heroshare_second_type', __('Type of displayed data', ESSB3_TEXT_DOMAIN), __('Choose what you wish to be displayed into second widget area below share buttons. If you wish to leave it blank choose Custom HTML message and do not fill anything inside field for custom message.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_textbox_stretched('display', 'positions-13', 'heroshare_second_fans', __('Followers counter shortcode', ESSB3_TEXT_DOMAIN), __('Fill in this field you followers counter shortcode that will be used if you select in second widget area to have followers counter. Shortcode can be generated using shortcode generator.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('display', 'positions-13', 'heroshare_second_message', __('Custom HTML message', ESSB3_TEXT_DOMAIN), __('Set your custom message (for example html code for opt-in form). This field supports shortcodes.', ESSB3_TEXT_DOMAIN), "htmlmixed");
ESSBOptionsStructureHelper::field_section_end('display', 'positions-13');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-13', __('Hero share window display', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-13', 'heroshare_window_popafter', __('Display pop up window after (sec)', ESSB3_TEXT_DOMAIN), __('If you wish pop up window to appear after amount of seconds you can provide theme here. Leave blank for immediate pop up after page load.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-clock-o', 'right');
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-13', 'heroshare_user_percent', __('Display pop up window after percent of content is viewed', ESSB3_TEXT_DOMAIN), __('Set amount of page content after which the pop up will appear.', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-v', 'right');
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-13', 'heroshare_display_end', __('Display pop up at the end of content', ESSB3_TEXT_DOMAIN), __('Automatically display pop up when the content end is reached', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-13', 'heroshare_display_exit', __('Display pop up on exit intent', ESSB3_TEXT_DOMAIN), __('Automatically display pop up when exit intent is detected', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-13', 'heroshare_user_manual_show', __('Manual window display mode', ESSB3_TEXT_DOMAIN), __('Activating manual display mode will allow you to show window when you decide with calling following javascript function essb_heroshare_show();', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-13', 'heroshare_avoid_logged_users', __('Do not show pop up for logged in users', ESSB3_TEXT_DOMAIN), __('Activate this option to avoid display of pop up when user is logged in into site.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-13');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-13', __('Window close', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-13', 'heroshare_user_notshow_onclose', __('After user close window do not show it again on this page/post for him', ESSB3_TEXT_DOMAIN), __('Activating this option will set cookie that will not show again pop up message for next 7 days for user on this post/page', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-13', 'heroshare_user_notshow_onclose_all', __('After user close window do not show it again on all page/post for him', ESSB3_TEXT_DOMAIN), __('Activating this option will set cookie that will not show again pop up message for next 7 days for user on all posts/pages', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-13');
essb_prepare_location_advanced_customization('display', 'positions-13', 'heroshare');

ESSBOptionsStructureHelper::field_heading('display', 'positions-14', 'heading1', __('Display Position Settings: Excerpt', ESSB3_TEXT_DOMAIN));
essb_prepare_location_advanced_customization('display', 'positions-14', 'excerpt');

ESSBOptionsStructureHelper::field_heading('display', 'positions-15', 'heading1', __('Display Position Settings: Post share bar', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-15', __('Deactivate default components', ESSB3_TEXT_DOMAIN), __('Deactivate default active display elements', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-15', 'postbar_deactivate_prevnext', __('Deactivate previous/next articles', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to deactivate display of previous/next article buttons', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-15', 'postbar_deactivate_progress', __('Deactivate read progress bar', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to deactivate display of read progress', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-15', 'postbar_deactivate_title', __('Deactivate post title', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to deactivate display of post title', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-15');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-15', __('Activate additional components', ESSB3_TEXT_DOMAIN), __('Activate additional display elements', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-15', 'postbar_activate_category', __('Activate display of category', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to activate display of category', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-15', 'postbar_activate_author', __('Activate display of post author', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to activate display of post author', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-15', 'postbar_activate_total', __('Activate display of total shares counter', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to activate display of total shares counter', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-15', 'postbar_activate_comments', __('Activate display of comments counter', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to activate display of comments counter', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-15', 'postbar_activate_time', __('Activate display of time to read', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to activate display of time to read', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'positions-15', 'postbar_activate_time_words', __('Words per minuted for time to read', ESSB3_TEXT_DOMAIN), __('Customize the words per minute for time to read display', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-clock-o', 'right');
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-15');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-15', __('Customize colors', ESSB3_TEXT_DOMAIN), __('Customize default colors of core components', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-15', 'postbar_bgcolor', __('Change default background color', ESSB3_TEXT_DOMAIN), __('Customize the default post bar background color (#FFFFFF)', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-15', 'postbar_color', __('Change default text color', ESSB3_TEXT_DOMAIN), __('Customize the default post bar text color (#111111)', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-15', 'postbar_accentcolor', __('Change default accent color', ESSB3_TEXT_DOMAIN), __('Customize the default post bar accent color (#3D8EB9)', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-15', 'postbar_altcolor', __('Change default alt text color', ESSB3_TEXT_DOMAIN), __('Customize the default post bar alt text color (#FFFFFF) which is applied to elements with accent background color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-15');

ESSBOptionsStructureHelper::field_section_start('display', 'positions-15', __('Customize button style', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
$tab_id = 'display';
$menu_id = 'positions-15';
$location = 'postbar';
ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_button_style', __('Buttons Style', ESSB3_TEXT_DOMAIN), __('Select your button display style.', ESSB3_TEXT_DOMAIN), essb_avaiable_button_style_with_recommend());
ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_template', __('Template', ESSB3_TEXT_DOMAIN), __('Select your template for that display location.', ESSB3_TEXT_DOMAIN), essb_available_tempaltes());
ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_nospace', __('Remove spacing between buttons', ESSB3_TEXT_DOMAIN), __('Activate this option to remove default space between share buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_show_counter', __('Display counter of sharing', ESSB3_TEXT_DOMAIN), __('Activate display of share counters.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_counter_pos', __('Position of counters', ESSB3_TEXT_DOMAIN), __('Choose your default button counter position', ESSB3_TEXT_DOMAIN), essb_avaliable_counter_positions());

ESSBOptionsStructureHelper::field_section_end('display', 'positions-15');

essb_prepare_location_advanced_customization('display', 'positions-15', 'postbar');

// Point
ESSBOptionsStructureHelper::field_heading('display', 'positions-16', 'heading1', __('Display Position Settings: Point', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-16', __('Point display', ESSB3_TEXT_DOMAIN), __('Choose location of point and style', ESSB3_TEXT_DOMAIN));
$point_positions = array("bottomright" => __('Bottom Right', 'essb'), 'bottomleft' => __('Bottom Left', 'essb'), 'topright' => __('Top Right', 'essb'), 'topleft' => __('Top Left', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('display', 'positions-16', 'point_position', __('Point will appear on', ESSB3_TEXT_DOMAIN), __('Choose where you wish sharing point to appear', 'essb'), $point_positions);
ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-16', 'point_total', __('Display total counter', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to activate display of total counter on point', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
//ESSBOptionsStructureHelper::field_switch_panel('display', 'positions-16', 'point_open_end', __('Automatic share point open at the end of content', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to automatic share point open at the end of post content', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$point_open_triggers = array("no" => __("No", "essb"), "end" => __("At the end of content", "essb"), "middle" => __("After the middle of content", "essb"));
ESSBOptionsStructureHelper::field_select_panel('display', 'positions-16', 'point_open_auto', __('Automatic share point open', ESSB3_TEXT_DOMAIN), __('Select your button display style.', ESSB3_TEXT_DOMAIN), $point_open_triggers);

$point_display_style = array("simple" => __('Simple icons', 'essb'), 'advanced' => __('Advanced Panel', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('display', 'positions-16', 'point_style', __('Share buttons action type', ESSB3_TEXT_DOMAIN), __('Choose your share buttons action type. Simple buttons will just open share buttons when you click the point. Advanced panel allows you also to include custom texts before/after buttons into nice flyout panel', 'essb'), $point_display_style);
$point_display_style = array("round" => __('Round', 'essb'), 'square' => __('Square', 'essb'), 'rounded' => __('Rounded edges square', 'essb'));
ESSBOptionsStructureHelper::field_select_panel('display', 'positions-16', 'point_shape', __('Point button shape', ESSB3_TEXT_DOMAIN), __('Choose the shape of share point - default is round', 'essb'), $point_display_style);

ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-16');

ESSBOptionsStructureHelper::field_section_start_panels('display', 'positions-16', __('Customize colors', ESSB3_TEXT_DOMAIN), __('Customize default colors of core components', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-16', 'point_bgcolor', __('Change default background color', ESSB3_TEXT_DOMAIN), __('Customize the default point background color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-16', 'point_color', __('Change default text color', ESSB3_TEXT_DOMAIN), __('Customize the default point text color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-16', 'point_accentcolor', __('Change default total background color', ESSB3_TEXT_DOMAIN), __('Customize the default total background color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color_panel('display', 'positions-16', 'point_altcolor', __('Change default total text color', ESSB3_TEXT_DOMAIN), __('Customize the default total text color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('display', 'positions-16');




ESSBOptionsStructureHelper::field_section_start('display', 'positions-16', __('Customize button style', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
$tab_id = 'display';
$menu_id = 'positions-16';
$location = 'point';
ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_button_style', __('Buttons Style', ESSB3_TEXT_DOMAIN), __('Select your button display style.', ESSB3_TEXT_DOMAIN), essb_avaiable_button_style_with_recommend());
ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_template', __('Template', ESSB3_TEXT_DOMAIN), __('Select your template for that display location.', ESSB3_TEXT_DOMAIN), essb_available_tempaltes());
ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_nospace', __('Remove spacing between buttons', ESSB3_TEXT_DOMAIN), __('Activate this option to remove default space between share buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_show_counter', __('Display counter of sharing', ESSB3_TEXT_DOMAIN), __('Activate display of share counters.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_counter_pos', __('Position of counters', ESSB3_TEXT_DOMAIN), __('Choose your default button counter position. Please note that if you use Simple icons mode all Inside positions will act like Inside - network names will not appear because of visual limitations', ESSB3_TEXT_DOMAIN), essb_avaliable_counter_positions_point());
ESSBOptionsStructureHelper::field_section_end('display', 'positions-16');

ESSBOptionsStructureHelper::field_section_start('display', 'positions-16', __('Custom button content for Advanced panel display', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_wpeditor('display', 'positions-16', 'point_top_content', __('Custom content above share buttons', ESSB3_TEXT_DOMAIN), __('Optional: Provide custom content that will appear above share buttons. You can use the variables to display post related content: %%title%%, %%url%%, %%image%%, %%permalink%%', 'essb'), 'htmlmixed');
ESSBOptionsStructureHelper::field_wpeditor('display', 'positions-16', 'point_bottom_content', __('Custom content below share buttons', ESSB3_TEXT_DOMAIN), __('Optional: Provide custom content that will appear below share buttons. You can use the variables to display post related content: %%title%%, %%url%%, %%image%%, %%permalink%%', 'essb'), 'htmlmixed');
ESSBOptionsStructureHelper::field_switch('display', 'positions-16', 'point_articles', __('Display prev/next article', ESSB3_TEXT_DOMAIN), __('Activate this option to display prev/next article from same category', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('display', 'positions-16');


essb_prepare_location_advanced_customization('display', 'positions-16', 'point');


$mobile_cache_plugin_detected = "";
if (ESSBCacheDetector::is_cache_plugin_detected()) {
	$mobile_cache_plugin_detected = __(' Cache plugin detected: ', ESSB3_TEXT_DOMAIN).'<b>'.ESSBCacheDetector::cache_plugin_name().'</b>';
}

$mobile_instuctions_open = '&nbsp;<a
				href="#TB_inline?width=auto&min-height=550&inlineId=essb3-cache-instuctions"
				class="thickbox" title="Configuring cache plugins for Easy Social Share Buttons"><span><b>If you use a cache plugin click here to see how to configure it for proper work with mobile display methods.</b></span></a>';

ESSBOptionsStructureHelper::field_heading('display', 'mobile-1', 'heading1', __('Mobile: Display Options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'mobile-1', 'mobile_positions', __('Change display positions on mobile', ESSB3_TEXT_DOMAIN), __('Activate this option to personalize display positions on mobile.'.$mobile_instuctions_open.$mobile_cache_plugin_detected, ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_image_radio('display', 'mobile-1', 'content_position_mobile', __('Primary content display position', ESSB3_TEXT_DOMAIN), __('Choose default method that will be used to render buttons inside content', ESSB3_TEXT_DOMAIN), essb_avaliable_content_positions_mobile());
ESSBOptionsStructureHelper::field_image_checkbox('display', 'mobile-1', 'button_position_mobile', __('Additional button display positions', ESSB3_TEXT_DOMAIN), __('Choose additional display methods that can be used to display buttons.', ESSB3_TEXT_DOMAIN), essb_available_button_positions_mobile());
ESSBOptionsStructureHelper::field_switch('display', 'mobile-1', 'mobile_exclude_tablet', __('Do not apply mobile settings for tablets', ESSB3_TEXT_DOMAIN), __('You can avoid mobile rules for settings for tablet devices.', ESSB3_TEXT_DOMAIN), 'recommeded', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_heading('display', 'mobile-1', 'heading4', __('Share bar customizations', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('display', 'mobile-1', 'mobile_sharebar_text', __('Text on share bar', ESSB3_TEXT_DOMAIN), __('Customize the default share bar text (default is Share).', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_heading('display', 'mobile-1', 'heading4', __('Share buttons bar customizations', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start_full_panels('display', 'mobile-1', __('Share buttons bar customization', ESSB3_TEXT_DOMAIN), '');
$listOfOptions = array("1" => "1 Button", "2" => "2 Buttons", "3" => "3 Buttons", "4" => "4 Buttons", "5" => "5 Buttons", "6" => "6 Buttons");
ESSBOptionsStructureHelper::field_select_panel('display', 'mobile-1', 'mobile_sharebuttonsbar_count', __('Number of buttons in share buttons bar', ESSB3_TEXT_DOMAIN), __('Provide number of buttons you wish to see in buttons bar. If the number of activated buttons is greater than selected here the last button will be more button which will open pop up with all active buttons.', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_switch_panel('display', 'mobile-1', 'mobile_sharebuttonsbar_names', __('Display network names', ESSB3_TEXT_DOMAIN), __('Activate this option to display network names (default display is icons only).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'mobile-1', 'mobile_sharebuttonsbar_fix', __('Fix problem with buttons not displayed in full width', ESSB3_TEXT_DOMAIN), __('Some themes may overwrite the default buttons style and in this case buttons do not take the full width of screen. Activate this option to fix the overwritten styles.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'mobile-1', 'mobile_sharebuttonsbar_total', __('Display total share counter', ESSB3_TEXT_DOMAIN), __('Activate this option to display total share counter as first button. If you activate it please keep in mind that you need to set number of columns to be with one more than buttons you except to see (total counter will act as single button)', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('display', 'mobile-1', 'mobile_sharebuttonsbar_hideend', __('Hide buttons bar before end of page', ESSB3_TEXT_DOMAIN), __('This option is made to hide buttons bar once you reach 90% of page content to allow the entire footer to be visible.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_panel('display', 'mobile-1', 'mobile_sharebuttonsbar_hideend_percent', __('% of content is viewed to hide buttons bar before end of page', ESSB3_TEXT_DOMAIN), __('Customize the default percent 90 when buttons bar will hide. Enter value in percents without the % mark.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_full_panels('display', 'mobile-1');

ESSBOptionsStructureHelper::field_heading('display', 'mobile-1', 'heading4', __('Client side mobile detection', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('display', 'mobile-1', __('Client side mobile detection', ESSB3_TEXT_DOMAIN), __('Client side mobile settings should be used only when you have a cache plugin that cannot be configured to work with both mobile and desktop version of site (see instructions on how to configure most popular cache plugins on the activate mobile settings switch). <br/><br/>All settings in this section use screen size of screen to detect a mobile device. If you use this mode of detection all desktop display methods cannot have different mobile settings on mobile device - they will display same buttons just like on desktop. Personalized settings will work for mobile optimized display methods only.<br/><br/>Quick note: After activating the client side detection if you see your mobile display methods twice you do not need a client side detection and you can turn it off.<br/><br/><b>Important! After you make change in that section after updating settings you need to clear cache of plugin you use to allow new css code that controls display to be added.</b>', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'mobile-1', 'mobile_css_activate', __('Activate client side detection of mobile device', ESSB3_TEXT_DOMAIN), __('Activate this option to make settings below work', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('display', 'mobile-1', 'mobile_css_screensize', __('Width of screen', ESSB3_TEXT_DOMAIN), __('Leave blank to use the default width of 750. In case you wish to customize it fill value in numbers (without px) and all devices that have screen width below will be marked as mobile.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'mobile-1', 'mobile_css_readblock', __('Hide read blocking methods', ESSB3_TEXT_DOMAIN), __('Activate this option to remove all read blocking methods on mobile devices. Read blocking display methods are Sidebar and Post Vertical Float', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'mobile-1', 'mobile_css_all', __('Hide all share buttons on mobile', ESSB3_TEXT_DOMAIN), __('Activate this option to hide all share buttons on mobile devices including those made with shortcodes.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('display', 'mobile-1', 'mobile_css_optimized', __('Control mobile optimized display methods', ESSB3_TEXT_DOMAIN), __('Activate this option to display mobile optimized display methods when resolution meets the mobile size that is defined. Methods that are controlled with this option include: Share Buttons Bar, Share Bar and Share Point. At least one of those methods should be selected in the settings above for additional display methods.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('display', 'mobile-1');

$checkbox_list_networks = array();
foreach (essb_available_social_networks() as $key => $object) {
	$checkbox_list_networks[$key] = $object['name'];
}

essb_prepare_location_advanced_customization('display', 'mobile-2', 'mobile');
essb_prepare_location_advanced_customization_mobile('display', 'mobile-3', 'sharebar');
essb_prepare_location_advanced_customization_mobile('display', 'mobile-4', 'sharepoint');
essb_prepare_location_advanced_customization_mobile('display', 'mobile-5', 'sharebottom');
// message above/before buttons
ESSBOptionsStructureHelper::field_heading('display', 'message-1', 'heading1', __('Custom message before share buttons', ESSB3_TEXT_DOMAIN));
//ESSBOptionsStructureHelper::field_heading('display', 'message-1', 'heading4', __('Static resource load optimizations', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('display', 'message-1', 'message_share_before_buttons', __('Message before share buttons', ESSB3_TEXT_DOMAIN), __('You can use following variables to create personalized message: %%title%% - displays current post title, %%permalink%% - displays current post address.', ESSB3_TEXT_DOMAIN), 'htmlmixed');

ESSBOptionsStructureHelper::field_heading('display', 'message-2', 'heading1', __('Custom message above share buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('display', 'message-2', 'message_above_share_buttons', __('Message above share buttons', ESSB3_TEXT_DOMAIN), __('You can use following variables to create personalized message: %%title%% - displays current post title, %%permalink%% - displays current post address.', ESSB3_TEXT_DOMAIN), 'htmlmixed');

ESSBOptionsStructureHelper::field_heading('display', 'message-3', 'heading1', __('Custom message above like buttons', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('display', 'message-3', 'message_like_buttons', __('Message above like buttons', ESSB3_TEXT_DOMAIN), __('You can use following variables to create personalized message: %%title%% - displays current post title, %%permalink%% - displays current post address.', ESSB3_TEXT_DOMAIN), 'htmlmixed');

//'advanced', 'optimization'
ESSBOptionsStructureHelper::field_heading('advanced', 'optimization', 'heading1', __('Optimization Options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_heading('advanced', 'optimization', 'heading4', __('Static resource optimizations', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_section_start_panels('advanced', 'optimization', __('CSS Styles Optimizations', ESSB3_TEXT_DOMAIN), __('Activate option that will optimize load of static css resources', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('advanced', 'optimization', 'use_minified_css', __('Use minified CSS files', ESSB3_TEXT_DOMAIN), __('Minified CSS files will improve speed of load. Activate this option to use them.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('advanced', 'optimization', 'load_css_footer', __('Load plugin inline styles into footer', ESSB3_TEXT_DOMAIN), __('Activating this option will load dynamic plugin inline styles into footer.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('advanced', 'optimization');

ESSBOptionsStructureHelper::field_section_start_panels('advanced', 'optimization', __('Scripts load optimization', ESSB3_TEXT_DOMAIN), __('Activate option that will optimize load of static resources - css and javascript', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('advanced', 'optimization', 'use_minified_js', __('Use minified javascript files', ESSB3_TEXT_DOMAIN), __('Minified javascript files will improve speed of load. Activate this option to use them.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('advanced', 'optimization', 'scripts_in_head', __('Load scripts in head element', ESSB3_TEXT_DOMAIN), __('If you are using caching plugin like W3 Total Cache you may need to activate this option if counters, send mail form or float do not work.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('advanced', 'optimization', 'load_js_async', __('Load plugin javascript files asynchronous', ESSB3_TEXT_DOMAIN), __('This will load scripts during page load in non render blocking way', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('advanced', 'optimization', 'load_js_defer', __('Load plugin javascript files deferred', ESSB3_TEXT_DOMAIN), __('This will load scripts after page load in non render blocking way', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('advanced', 'optimization', 'load_js_delayed', __('Load plugin javascript files delayed', ESSB3_TEXT_DOMAIN), __('This will load scripts after 2 seconds when page is fully loaded', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('advanced', 'optimization');

ESSBOptionsStructureHelper::field_section_start_panels('advanced', 'optimization', __('Global resource load optimizations', ESSB3_TEXT_DOMAIN), __('Activate option that will optimize load of static javascript resources', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('advanced', 'optimization', 'remove_ver_resource', __('Remove version number from static resource files', ESSB3_TEXT_DOMAIN), __('Activating this option will remove added to resources version number ?ver= which will allow these files to be cached.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch_panel('advanced', 'optimization', 'precompiled_resources', __('Use plugin precompiled resources', ESSB3_TEXT_DOMAIN), __('Activating this option will precompile and cache plugin dynamic resources to save load time. Precompiled resources can be used only when you use same configuration on your entire site.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end_panels('advanced', 'optimization');

$cache_plugin_detected = "";
if (ESSBCacheDetector::is_cache_plugin_detected()) {
	$cache_plugin_detected = "<br/><br/> Cache plugin detected: <b>".ESSBCacheDetector::cache_plugin_name().'</b>. When you use cache plugin we recommend not to turn on the build in caching function because your cache plugin already does that.';
}

ESSBOptionsStructureHelper::field_heading('advanced', 'optimization', 'heading4', __('Build in cache', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_section_start('advanced', 'optimization', __('Build in cache', ESSB3_TEXT_DOMAIN), __('Activate build in cache functions to improve speed of load. If you use a site cache plugin activation of those options is not needed as that plugin will do the cache work.'.$cache_plugin_detected, ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'essb_cache_runtime', __('Activate WordPress cache', ESSB3_TEXT_DOMAIN), __('Activating WordPress cache function usage will cache button generation via default WordPress cache or via the persistant cache plugin if you use such (like W3 Total Cache)', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'essb_cache', __('Activate cache', ESSB3_TEXT_DOMAIN), __('This option is in beta and if you find any problems using it please report at our <a href="http://support.creoworx.com" target="_blank">support portal</a>. To clear cache you can simply press Update Settings button in Main Settings (cache expiration time is 1 hour)', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$cache_mode = array ("full" => "Cache button render and dynamic resources", "resource" => "Cache only dynamic resources", "buttons" => "Cache only buttons render" );
ESSBOptionsStructureHelper::field_select('advanced', 'optimization', 'essb_cache_mode', __('Cache mode', ESSB3_TEXT_DOMAIN), __('Choose between caching full render of share buttons and resources or cache only dynamic resources (CSS and Javascript).', ESSB3_TEXT_DOMAIN), $cache_mode);
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'essb_cache_static', __('Combine into single file all plugin static CSS files', ESSB3_TEXT_DOMAIN), __('This option will combine all plugin static CSS files into single file.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'optimization', 'essb_cache_static_js', __('Combine into single file all plugin static javascript files', ESSB3_TEXT_DOMAIN), __('This option will combine all plugin static javacsript files into single file. This option will not work if scripts are set to load asynchronous or deferred.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'optimization');

ESSBOptionsStructureHelper::field_heading('advanced', 'advanced', 'heading1', __('Advanced Options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox('advanced', 'advanced', 'priority_of_buttons', __('Change default priority of buttons', ESSB3_TEXT_DOMAIN), __('Provide custom value of priority when buttons will be included in content (default is 10). This will make code of plugin to execute before or after another plugin. Attention! Providing incorrect value may cause buttons not to display.', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'apply_clean_buttons', __('Clean buttons from excerpts', ESSB3_TEXT_DOMAIN), __('Activate this option to avoid buttons included in excerpts as text FacebookTwiiter and so.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$methods = array ("default" => "Clean network texts", "actionremove" => "Remove entire action", "clean2" => "Smart clean network texts", "remove2" => "Show buttons only on mail query" );
ESSBOptionsStructureHelper::field_select('advanced', 'advanced', 'apply_clean_buttons_method', __('Clean method', ESSB3_TEXT_DOMAIN), __('Choose method of buttons clean.', ESSB3_TEXT_DOMAIN), $methods);

ESSBOptionsStructureHelper::field_section_start('advanced', 'advanced', __('URL and Message encoding', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'essb_encode_url', __('Use encoded version of url for sharing', ESSB3_TEXT_DOMAIN), __('Activate this option to encode url used for sharing. This is option is recommended when you notice that parts of shared url are missing - usually when additional options are used like campaign tracking.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'essb_encode_text', __('Use encoded version of texts for sharing', ESSB3_TEXT_DOMAIN), __('Activate this option to encode texts used for sharing. You need to use this option when you have special characters which does not appear in share.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'essb_encode_text_plus', __('Fix problem with appearing + in text when shared via mobile', ESSB3_TEXT_DOMAIN), __('Activate this option to fix the problem with + sign that appears in share description (usually in Tweet when Twitter App is used).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'advanced');

ESSBOptionsStructureHelper::field_section_start('advanced', 'advanced', __('Plugin does not share correct data', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'avoid_nextpage', __('Avoid &lt;!--nextpage--&gt; and always share main post address', ESSB3_TEXT_DOMAIN), __('Activate this option if you use multi-page posts and wish to share only main page.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'force_wp_query_postid', __('Force get of current post/page', ESSB3_TEXT_DOMAIN), __('Activate this option if share doest not get correct page.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'reset_postdata', __('Reset WordPress loops', ESSB3_TEXT_DOMAIN), __('Activate this option if plugin does not detect properly post permalink.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'force_wp_fullurl', __('Allow usage of query string parameters in share address', ESSB3_TEXT_DOMAIN), __('Activate this option to allow usage of query string parameters in url (there are plugins that use this).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'force_archive_pages', __('Correct shared data when sidebar, top bar, bottom bar, pop up or fly in are used in archive pages', ESSB3_TEXT_DOMAIN), __('Activate this option if you se sidebar, top bar, bottom bar, pop up or fly in on archive pages (list of posts, posts by tag or category, homepage with latest posts) and plugin does not detect the correct shared information.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'deactivate_shorturl_cache', __('Deactivate short url cache', ESSB3_TEXT_DOMAIN), __('Activate this option to stop cache of short url (mainly it is used if incorrect address was initially cached).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'always_use_http', __('Make plugin share always http version of page', ESSB3_TEXT_DOMAIN), __('When you migrate from http to https all social share counters will go down to zero (0) because social networks count shares by the unique address of post/page. Making this will allow plugin always to use post/page http version of address.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'advanced');

ESSBOptionsStructureHelper::field_section_start('advanced', 'advanced', __('Other', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'reset_posttype', __('Dublicate check to avoid buttons appear on not associated post types', ESSB3_TEXT_DOMAIN), __('Activate this option if buttons appear on post types that are not marked as active.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'counter_curl_fix', __('Fix counter problem with limited cURL configuration', ESSB3_TEXT_DOMAIN), __('Activate this option if have troubles displaying counters for networks that do not have native access to counter API (ex: Google). To make it work you also need to activate in Display Settings -> Counters to load with WordPress admin ajax function..', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'deactivate_fa', __('Do not load FontAwsome', ESSB3_TEXT_DOMAIN), __('Activate this option if your site already uses Font Awesome font.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'use_rel_me', __('Add rel="me" instead of rel="nofollow" to social share buttons', ESSB3_TEXT_DOMAIN), __('Activate this option if your SEO strategy requires this. Default is nofollow which is suggested value.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'advanced', 'legacy_class', __('Include class names in CSS from version 2.x', ESSB3_TEXT_DOMAIN), __('Activate this option if you use class names for customization that do not exist in new version.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'advanced');


ESSBOptionsStructureHelper::field_heading('advanced', 'administrative', 'heading1', __('Administrative Options', ESSB3_TEXT_DOMAIN));
$admin_style = array ("" => "Dark", "light" => "Light" );
ESSBOptionsStructureHelper::field_select('advanced', 'administrative', 'admin_template', __('Plugin Settings Style', ESSB3_TEXT_DOMAIN), __('Change plugin default options style', ESSB3_TEXT_DOMAIN), $admin_style);


ESSBOptionsStructureHelper::field_section_start('advanced', 'administrative', __('Advanced Display Options', ESSB3_TEXT_DOMAIN), __('Activate additional advanced options for customization and sharing', ESSB3_TEXT_DOMAIN));
//ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'advanced_by_post_category', __('Activate custom style settings for post category', ESSB3_TEXT_DOMAIN), __('Activation of this option will add additional menu settings for each post category that you have which will allow to change style of buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'advanced_custom_share', __('Activate custom share by social network', ESSB3_TEXT_DOMAIN), __('Activation of this option will add additional menu settings for message share customization by social network.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
//float_onsingle_only
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'float_onsingle_only', __('Float display methods on sigle posts/pages only', ESSB3_TEXT_DOMAIN), __('Plugin will check and display float from top and post vertical float only when a single post/page is being displayed. In all other case method will be replaced with display method top.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'administrative');

ESSBOptionsStructureHelper::field_section_start('advanced', 'administrative', __('Plugin Settings Access', ESSB3_TEXT_DOMAIN), __('Control access to various plugin settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'disable_adminbar_menu', __('Disable menu in WordPress admin bar', ESSB3_TEXT_DOMAIN), __('Activation of this option will remove the quick access plugin menu from WordPress top admin bar.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
$listOfOptions = array("manage_options" => "Administrator", "delete_pages" => "Editor", "publish_posts" => "Author", "edit_posts" => "Contributor");
ESSBOptionsStructureHelper::field_select('advanced', 'administrative', 'essb_access', __('Plugin access', ESSB3_TEXT_DOMAIN), __('Make settings available for the following user roles (if you use multiple user roles on your site we recommend to select Administrator to disallow other users change settings of plugin).', ESSB3_TEXT_DOMAIN), $listOfOptions);
ESSBOptionsStructureHelper::field_section_end('advanced', 'administrative');
ESSBOptionsStructureHelper::field_section_start('advanced', 'administrative', __('Metabox visibiltiy', ESSB3_TEXT_DOMAIN), __('Control access to plugin metaboxes', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'turnoff_essb_advanced_box', __('Remove post advanced visual settings metabox', ESSB3_TEXT_DOMAIN), __('Activation of this option will remove the advanced meta box on each post that allow customizations of visual styles for post.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'turnoff_essb_optimize_box', __('Remove post share customization metabox', ESSB3_TEXT_DOMAIN), __('Activation of this option will remove the share customization meta box on each post (allows changing social share optimization tags, customize share and etc.).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'administrative', 'turnoff_essb_stats_box', __('Remove post detailed stats metabox', ESSB3_TEXT_DOMAIN), __('Activation of this option will remove the detailed stats meta box from each post/page when social share analytics option is activated.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'administrative');


ESSBOptionsStructureHelper::field_func('advanced', 'administrative', 'essb3_reset_postdata', __('Reset plugin settings', ESSB3_TEXT_DOMAIN), __('Warning! Pressing this button will restore initial plugin configuration values and all settings that you apply after plugin activation will be removed.', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('advanced', 'deactivate', 'heading1', __('Deactivate Functions & Modules', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('advanced', 'deactivate', __('Modules', ESSB3_TEXT_DOMAIN), __('Turn off build in modules that does not have option in their settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('advanced', 'deactivate', 'deactivate_ctt', __('Deactivate Sharable Quotes module', ESSB3_TEXT_DOMAIN), __('This option will deactivate and remove code used by click to tweet module', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'deactivate');

ESSBOptionsStructureHelper::field_section_start('advanced', 'deactivate', __('Plugin Functions', ESSB3_TEXT_DOMAIN), __('Deactivate functions of plugin on selected pages', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'deactivate', 'deactivate_on_share', __('Social Share Buttons', ESSB3_TEXT_DOMAIN), __('Deactivate function on posts/pages with these IDs? Comma seperated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'deactivate', 'deactivate_on_native', __('Native Buttons', ESSB3_TEXT_DOMAIN), __('Deactivate function on posts/pages with these IDs? Comma seperated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'deactivate', 'deactivate_on_fanscounter', __('Social Following (Followers Counter)', ESSB3_TEXT_DOMAIN), __('Deactivate function on posts/pages with these IDs? Comma seperated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'deactivate', 'deactivate_on_ctt', __('Sharable Quotes (Click To Tweet)', ESSB3_TEXT_DOMAIN), __('Deactivate function on posts/pages with these IDs? Comma seperated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'deactivate', 'deactivate_on_sis', __('On Media Sharing (Social Image Share)', ESSB3_TEXT_DOMAIN), __('Deactivate function on posts/pages with these IDs? Comma seperated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'deactivate', 'deactivate_on_profiles', __('Social Profiles', ESSB3_TEXT_DOMAIN), __('Deactivate function on posts/pages with these IDs? Comma seperated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'deactivate', 'deactivate_on_sso', __('Social Share Optimization Meta Tags', ESSB3_TEXT_DOMAIN), __('Deactivate function on posts/pages with these IDs? Comma seperated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'deactivate', 'deactivate_on_aftershare', __('After Share Actions', ESSB3_TEXT_DOMAIN), __('Deactivate function on posts/pages with these IDs? Comma seperated: "11, 15, 125". Deactivating plugin will make no style or scripts to be executed for those pages/posts related to this function', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'deactivate');

ESSBOptionsStructureHelper::field_heading('advanced', 'twitter', 'heading1', __('Twitter Counter Setup', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('advanced', 'twitter', 'essb3_twitter_preload', __('Set initial Twitter share counter value', ESSB3_TEXT_DOMAIN), __('Preload your Twitter share counter with value when self-hosted counter is set to be used. You have also option on each post/page to modify the Twitter share counter value individually.', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('advanced', 'counterrecovery', 'heading1', __('Share Counter Recovery', ESSB3_TEXT_DOMAIN));
if (!defined('ESSB3_CACHED_COUNTERS')) {
	ESSBOptionsStructureHelper::field_func('advanced', 'counterrecovery', 'essb3_counter_recovery', __('Activate share counter recovery', ESSB3_TEXT_DOMAIN), __('Share counter recovery allows you recover share counters when you move to secure server or from different domain.', ESSB3_TEXT_DOMAIN));	
}
else {
	ESSBOptionsStructureHelper::field_switch('advanced', 'counterrecovery', 'counter_recover_active', __('Activate share counter recovery', ESSB3_TEXT_DOMAIN), __('Share counter recovery allows you recover share counters when you move to secure server or from different domain.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
	
	$recover_type = array("unchanged" => "Unchanged", "default" => "Default", "dayname" => "Day and Name", "monthname" => "Month and Name", "numeric" => "Numeric", "postname" => "Post Name", "domain" => "Change domain name");
	ESSBOptionsStructureHelper::field_select('advanced', 'counterrecovery', 'counter_recover_mode', __('Previous url format or domain', ESSB3_TEXT_DOMAIN), __('Choose how your site address is changed', ESSB3_TEXT_DOMAIN), $recover_type);
	ESSBOptionsStructureHelper::field_switch('advanced', 'counterrecovery', 'counter_recover_slash', __('My previous url does not have trailing slash', ESSB3_TEXT_DOMAIN), __('Activate this option if your previous url does not contain trailing slash at the end.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
	
	$recover_mode = array("unchanged" => "Unchanged", "http2https" => "Switch from http to https", "https2http" => "Switch from https to http");
	ESSBOptionsStructureHelper::field_select('advanced', 'counterrecovery', 'counter_recover_protocol', __('Change of connection protocol', ESSB3_TEXT_DOMAIN), __('If you change your connection protocol then choose here the option that describes it.', ESSB3_TEXT_DOMAIN), $recover_mode);	
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'counterrecovery', 'counter_recover_domain', __('Previous domain name', ESSB3_TEXT_DOMAIN), __('If you have changed your domain name please fill in this field previous domain name with protocol (example http://example.com) and choose recovery mode to be <b>Change domain name</b>', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'counterrecovery', 'counter_recover_newdomain', __('New domain name', ESSB3_TEXT_DOMAIN), __('If plugin is not able to detect your new domain fill here its name with protocol (example http://example.com)', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_textbox('advanced', 'counterrecovery', 'counter_recover_date', __('Date of change', ESSB3_TEXT_DOMAIN), __('Fill out date when change was made. Once you fill it share counter recovery will be made for all posts that are published before this date. Date shoud be filled in format <b>yyyy-mm-dd</b>.', ESSB3_TEXT_DOMAIN));
}

ESSBOptionsStructureHelper::field_heading('advanced', 'convert', 'heading1', __('Convert and import settings from previous version 2.x/1.x', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_func('advanced', 'convert', 'essb3_convert_postdata', __('Import settings for', ESSB3_TEXT_DOMAIN), __('Warning! Pressing this button will convert and apply settings from previous versions of product. All your current options will be replaced. Please note that not all options can be automatically imported and applied like Advanced options by post type and button position because of different available settings.', ESSB3_TEXT_DOMAIN));


ESSBOptionsStructureHelper::field_heading('advanced', 'localization', 'heading1', __('Translation Options', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_start('advanced', 'localization', __('Mail form texts', ESSB3_TEXT_DOMAIN), __('Translate mail form texts', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_title', __('Share this with a friend', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_email', __('Your Email', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_recipient', __('Recipient Email', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_subject', __('Subject', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message', __('Message', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_cancel', __('Cancel', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_send', __('Send', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message_sent', __('Message sent!', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message_invalid_captcha', __('Invalid Captcha code!', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_mail_message_error_send', __('Error sending message!', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'localization');

ESSBOptionsStructureHelper::field_section_start('advanced', 'localization', __('Love this texts', ESSB3_TEXT_DOMAIN), __('Translate love this button texts', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_love_thanks', __('Thank you for loving this.', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_love_loved', __('You already love this today.', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'localization');

ESSBOptionsStructureHelper::field_section_start('advanced', 'localization', __('Text on button hover', ESSB3_TEXT_DOMAIN), __('Provide texts that will appear when you hover a social share button (example: Share this article on Facebook). Texts will appear only when they are provided - leave blank for no text.', ESSB3_TEXT_DOMAIN));
essb3_prepare_texts_on_button_hover('advanced', 'localization');
ESSBOptionsStructureHelper::field_section_end('advanced', 'localization');

ESSBOptionsStructureHelper::field_section_start('advanced', 'localization', __('Plugin module texts', ESSB3_TEXT_DOMAIN), __('Include options to translate build in module texts that have no field in module settings', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_textbox_stretched('advanced', 'localization', 'translate_clicktotweet', __('Translate Click To Tweet text', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_section_end('advanced', 'localization');


//--- Style Settings
ESSBOptionsStructureHelper::field_heading('style', 'buttons', 'heading1', __('Color Customization', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('style', 'buttons', 'customizer_is_active', __('Activate color customizer', ESSB3_TEXT_DOMAIN), __('Color customizations will not be included unless you activate this option. You are able to activate customization on specific post/pages even if this option is not set to active.<br/><span class="essb-user-notice">After switching option to <b>Yes</b> press <b>Update Settings</b> button and advanced configuration fields will appear.</span>', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

$customizer_is_active = ESSBOptionValuesHelper::options_bool_value($essb_options, 'customizer_is_active');
if ($customizer_is_active) {
	ESSBOptionsStructureHelper::field_section_start('style', 'buttons', __('Total Counter', ESSB3_TEXT_DOMAIN), __('Customize the total counter', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_color('style', 'buttons', 'customizer_totalbgcolor', __('Background color', ESSB3_TEXT_DOMAIN), __('Replace total counter background color', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_switch('style', 'buttons', 'customizer_totalnobgcolor', __('Remove background color', ESSB3_TEXT_DOMAIN), __('Activate this option to remove the background color', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_color('style', 'buttons', 'customizer_totalcolor', __('Text color', ESSB3_TEXT_DOMAIN), __('Replace total counter text color', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_textbox('style', 'buttons', 'customizer_totalfontsize', __('Total counter big style font-size', ESSB3_TEXT_DOMAIN), __('Enter value in px (ex: 21px) to change the total counter font-size', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_textbox('style', 'buttons', 'customizer_totalfontsize_after', __('Total counter big style shares text font-size', ESSB3_TEXT_DOMAIN), __('Enter value in px (ex: 10px) to change the total counter shares text font-size', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_textbox('style', 'buttons', 'customizer_totalfontsize_beforeafter', __('Total counter before/after share buttons text font-size', ESSB3_TEXT_DOMAIN), __('Enter value in px (ex: 14px) to change the total counter text font-size', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_section_end('style', 'buttons');
	
	ESSBOptionsStructureHelper::field_section_start('style', 'buttons', __('Color customization for all social networks', ESSB3_TEXT_DOMAIN), __('Choose color settings that will be applied for all social networks', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_color('style', 'buttons', 'customizer_bgcolor', __('Background color', ESSB3_TEXT_DOMAIN), __('Replace all buttons background color', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_color('style', 'buttons', 'customizer_textcolor', __('Text color', ESSB3_TEXT_DOMAIN), __('Replace all buttons text color', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_color('style', 'buttons', 'customizer_hovercolor', __('Hover background color', ESSB3_TEXT_DOMAIN), __('Replace all buttons hover background color', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_color('style', 'buttons', 'customizer_hovertextcolor', __('Hover text color', ESSB3_TEXT_DOMAIN), __('Replace all buttons hover text color', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_switch('style', 'buttons', 'customizer_remove_bg_hover_effects', __('Remove effects applied from theme on hover', ESSB3_TEXT_DOMAIN), __('Activate this option to remove the default theme hover effects (like darken or lighten color).', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_section_end('style', 'buttons');
	
	ESSBOptionsStructureHelper::field_heading('style', 'buttons', 'heading3', __('Color customization for single social networks', ESSB3_TEXT_DOMAIN));
	essb3_prepare_color_customization_by_network('style', 'buttons');
}
ESSBOptionsStructureHelper::field_heading('style', 'css', 'heading1', __('Additional CSS', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('style', 'css', 'customizer_css', __('Additional custom CSS', ESSB3_TEXT_DOMAIN), '');

ESSBOptionsStructureHelper::field_heading('style', 'css2', 'heading1', __('Additional Footer CSS', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_editor('style', 'css2', 'customizer_css_footer', __('Additional custom CSS that will be added to footer', ESSB3_TEXT_DOMAIN), __('Add custom CSS code here if you wish that code to be included into footer of site', ESSB3_TEXT_DOMAIN));

ESSBOptionsStructureHelper::field_heading('style', 'fans', 'heading1', __('Followers Color Customization', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('style', 'fans', 'activate_fanscounter_customizer', __('Activate color customizer', ESSB3_TEXT_DOMAIN), __('Color customizations will not be included unless you activate this option. You are able to activate customization on specific post/pages even if this option is not set to active.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
essb3_draw_fanscounter_customization('style', 'fans');

ESSBOptionsStructureHelper::field_heading('style', 'image', 'heading1', __('Image Share Color Customization', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('style', 'image', 'activate_imageshare_customizer', __('Activate color customizer', ESSB3_TEXT_DOMAIN), __('Color customizations will not be included unless you activate this option. You are able to activate customization on specific post/pages even if this option is not set to active.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
essb3_draw_imageshare_customization('style', 'image');

ESSBOptionsStructureHelper::field_heading('style', 'subscribe', 'heading1', __('MailChimp Subscribe Form Customizer', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('style', 'subscribe', 'activate_mailchimp_customizer', __('Activate color customizer', ESSB3_TEXT_DOMAIN), __('Color customizations will not be included unless you activate this option.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color('style', 'subscribe', 'customizer_subscribe_bgcolor', __('Background color', ESSB3_TEXT_DOMAIN), __('Replace form background color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color('style', 'subscribe', 'customizer_subscribe_textcolor', __('Text color', ESSB3_TEXT_DOMAIN), __('Replace form text color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color('style', 'subscribe', 'customizer_subscribe_hovercolor', __('Accent color', ESSB3_TEXT_DOMAIN), __('Replace form accent color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color('style', 'subscribe', 'customizer_subscribe_hovertextcolor', __('Accent text color', ESSB3_TEXT_DOMAIN), __('Replace form accent text color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_color('style', 'subscribe', 'customizer_subscribe_emailcolor', __('Email field background color', ESSB3_TEXT_DOMAIN), __('Replace email field background color', ESSB3_TEXT_DOMAIN));
ESSBOptionsStructureHelper::field_switch('style', 'subscribe', 'customizer_subscribe_noborder', __('Remove top border of form', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to remove the tiny top border from the top of form.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));


// settings by post type
$positions_by_pt = ESSBOptionValuesHelper::options_bool_value($essb_options, 'positions_by_pt');

if ($positions_by_pt) {
	add_action('admin_init', 'essb3_register_positions_by_posttypes');
}
function essb3_register_positions_by_posttypes() {
	global $wp_post_types;

	$pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
	$cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );
	$first_post_type = "";
	$key = 1;
	foreach ( $pts as $pt ) {
		if (empty ( $first_post_type )) {
			$first_post_type = $pt;
			ESSBOptionsStructureHelper::menu_item ( 'display', 'positionspost', __ ( 'Display Positions by Post Type', ESSB3_TEXT_DOMAIN ), 'default', 'activate_first', 'positionspost-1' );
		}
		ESSBOptionsStructureHelper::submenu_item ( 'display', 'positionspost-' . $key, $wp_post_types [$pt]->label );

		ESSBOptionsStructureHelper::field_heading('display', 'positionspost-' . $key, 'heading1', __('Customize button positions for: '.$wp_post_types [$pt]->label, ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_image_radio('display', 'positionspost-' . $key, 'content_position_'.$pt, __('Primary content display position', ESSB3_TEXT_DOMAIN), __('Choose default method that will be used to render buttons inside content', ESSB3_TEXT_DOMAIN), essb_avaliable_content_positions());
		ESSBOptionsStructureHelper::field_image_checkbox('display', 'positionspost-' . $key, 'button_position_'.$pt, __('Additional button display positions', ESSB3_TEXT_DOMAIN), __('Choose additional display methods that can be used to display buttons.', ESSB3_TEXT_DOMAIN), essb_available_button_positions());
		
		$key ++;
	}

	foreach ( $cpts as $cpt ) {
		ESSBOptionsStructureHelper::submenu_item ( 'display', 'positionspost-' . $key, $wp_post_types [$cpt]->label );

		ESSBOptionsStructureHelper::field_heading('display', 'positionspost-' . $key, 'heading1', __('Customize button positions for: '.$wp_post_types [$cpt]->label, ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_image_radio('display', 'positionspost-' . $key, 'content_position_'.$cpt, __('Primary content display position', ESSB3_TEXT_DOMAIN), __('Choose default method that will be used to render buttons inside content', ESSB3_TEXT_DOMAIN), essb_avaliable_content_positions());
		ESSBOptionsStructureHelper::field_image_checkbox('display', 'positionspost-' . $key, 'button_position_'.$cpt, __('Additional button display positions', ESSB3_TEXT_DOMAIN), __('Choose additional display methods that can be used to display buttons.', ESSB3_TEXT_DOMAIN), essb_available_button_positions());
		$key ++;
	}
}

add_action('admin_init', 'essb3_register_settings_by_posttypes');
function essb3_register_settings_by_posttypes() {
	global $wp_post_types;
	
	$pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
	$cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );
	$first_post_type = "";
	$key = 1;
	foreach ( $pts as $pt ) {
		if (empty ( $first_post_type )) {
			$first_post_type = $pt;
			ESSBOptionsStructureHelper::menu_item ( 'advanced', 'advancedpost', __ ( 'Display Settings by Post Type', ESSB3_TEXT_DOMAIN ), 'default', 'activate_first', 'advancedpost-1' );
		}
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedpost-' . $key, $wp_post_types [$pt]->label );

		ESSBOptionsStructureHelper::field_heading('advanced', 'advancedpost-' . $key, 'heading1', __('Advanced settings for post type: '.$wp_post_types [$pt]->label, ESSB3_TEXT_DOMAIN));
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedpost-' . $key, 'post-type-'.$pt, true );
		$key ++;
	}
	
	foreach ( $cpts as $cpt ) {
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedpost-' . $key, $wp_post_types [$cpt]->label );
		ESSBOptionsStructureHelper::field_heading('advanced', 'advancedpost-' . $key, 'heading1', __('Advanced settings for post type: '.$wp_post_types [$cpt]->label, ESSB3_TEXT_DOMAIN));
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedpost-' . $key, 'post-type-'.$cpt, true );
		$key ++;
	}

	ESSBOptionsStructureHelper::menu_item ( 'advanced', 'advancedmodule', __ ( 'Display Settings for Plugin Integration', ESSB3_TEXT_DOMAIN ), 'default', 'activate_first', 'advancedmodule-1' );
	$key = 1;
	$cpt = 'woocommerce';
	$cpt_title = 'WooCommerce';
	ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
	ESSBOptionsStructureHelper::field_heading('advanced', 'advancedmodule-' . $key, 'heading1', __('Advanced settings for plugin: '.$cpt_title, ESSB3_TEXT_DOMAIN));
	essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-'.$cpt, true );
	$key ++;
	
	$cpt = 'wpecommerce';
	$cpt_title = 'WP e-Commerce';
	ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
	ESSBOptionsStructureHelper::field_heading('advanced', 'advancedmodule-' . $key, 'heading1', __('Advanced settings for plugin: '.$cpt_title, ESSB3_TEXT_DOMAIN));
	essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-'.$cpt, true );
	$key ++;

	$cpt = 'jigoshop';
	$cpt_title = 'JigoShop';
	ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
	ESSBOptionsStructureHelper::field_heading('advanced', 'advancedmodule-' . $key, 'heading1', __('Advanced settings for plugin: '.$cpt_title, ESSB3_TEXT_DOMAIN));
	essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-'.$cpt, true );
	$key ++;
	
	$cpt = 'ithemes';
	$cpt_title = 'iThemes Exchange';
	ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
	ESSBOptionsStructureHelper::field_heading('advanced', 'advancedmodule-' . $key, 'heading1', __('Advanced settings for plugin: '.$cpt_title, ESSB3_TEXT_DOMAIN));
	essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-'.$cpt, true );
	$key ++;
	
	$cpt = 'bbpress';
	$cpt_title = 'bbPress';
	ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
	ESSBOptionsStructureHelper::field_heading('advanced', 'advancedmodule-' . $key, 'heading1', __('Advanced settings for plugin: '.$cpt_title, ESSB3_TEXT_DOMAIN));
	essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-'.$cpt, true );
	$key ++;
	
	$cpt = 'buddypress';
	$cpt_title = 'BuddyPress';
	ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
	ESSBOptionsStructureHelper::field_heading('advanced', 'advancedmodule-' . $key, 'heading1', __('Advanced settings for plugin: '.$cpt_title, ESSB3_TEXT_DOMAIN));
	essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-'.$cpt, true );
	$key ++;
	
}

function essb3_draw_imageshare_customization($tab_id, $menu_id) {
	$listOfNetworksAdvanced = array( "facebook" => "Facebook", "twitter" => "Twitter", "google" => "Google", "linkedin" => "LinkedIn", "pinterest" => "Pinterest", "tumblr" => "Tumblr", "reddit" => "Reddit", "digg" => "Digg", "delicious" => "Delicious", "vkontakte" => "VKontakte", "odnoklassniki" => "Odnoklassniki");	

	foreach ($listOfNetworksAdvanced as $network => $title) {
		ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'imagecustomizer_'.$network, $title, '');
	}
}

function essb3_draw_fanscounter_customization($tab_id, $menu_id) {
	$network_list = ESSBSocialFollowersCounterHelper::available_social_networks();
	
	if (defined('ESSB3_SFCE_VERSION')) {
		$network_list = ESSBSocialFollowersCounterHelper::list_of_all_available_networks_extended();
	}
	
	foreach ($network_list as $network => $title) {
		ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'fanscustomizer_'.$network, $title, '');
	}
}

function essb3_draw_fanscounter_settings($tab_id, $menu_id) {
	$setting_fields = ESSBSocialFollowersCounterHelper::options_structure();
	$network_list = ESSBSocialFollowersCounterHelper::available_social_networks();
	
	$networks_same_authentication = array();
	
	// @since 3.2.2 Integration with Social Followers Counter Extended
	if (defined('ESSB3_SFCE_OPTIONS_NAME')) {
		$fanscounter_extended_options = get_option(ESSB3_SFCE_OPTIONS_NAME);
		$extended_list = array();
		foreach ($network_list as $network => $title) {
			$is_active_extended = ESSBOptionValuesHelper::options_bool_value($fanscounter_extended_options, 'activate_'.$network);
			$use_same_api = ESSBOptionValuesHelper::options_bool_value($fanscounter_extended_options, 'same_access_'.$network);
			$count_extended = ESSBOptionValuesHelper::options_value($fanscounter_extended_options, 'profile_count_'.$network);
			$count_extended = intval($count_extended);
			
			$extended_list[$network] = $title;
			
			if ($is_active_extended) {
				if ($use_same_api) {
					$networks_same_authentication[$network] = "yes";
				}
				
				for ($i=1;$i<=$count_extended;$i++) {
					$extended_list[$network."_".$i] = $title." Additional Profile ".$i;
				}
			}
		}
		$network_list = array();
		foreach ($extended_list as $network => $title) {
			$network_list[$network] = $title;
		}
		
		//asort($network_list);
	}
		
	foreach ($network_list as $network => $title) {
		ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading2', $title);
		
		$default_options_key = $network;
		$is_extended_key = false;
		
		if (strpos($default_options_key, '_') !== false && $default_options_key != 'wp_posts' && $default_options_key != 'wp_comments' && $default_options_key != 'wp_users') {
			$key_array = explode('_', $default_options_key);
			$default_options_key = $key_array[0];
			$is_extended_key = true;
		}
		
		$single_network_options = isset($setting_fields[$default_options_key]) ? $setting_fields[$default_options_key] : array();
		
		foreach ($single_network_options as $field => $options) {
			$field_id = "essb3fans_".$network."_".$field;
			
			$field_type = isset($options['type']) ? $options['type'] : 'textbox';
			$field_text = isset($options['text']) ? $options['text'] : '';
			$field_description = isset($options['description']) ? $options['description'] : '';
			$field_values = isset($options['values']) ? $options['values'] : array();
			
			$is_authfield = isset($options['authfield']) ? $options['authfield'] : false;
			
			if ($is_extended_key && $is_authfield) {
				if (isset($networks_same_authentication[$default_options_key])) {
					continue;
				}
			}
			
			if ($field_type == "textbox") {
				ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, $field_id, $field_text, $field_description);
			}
			if ($field_type == "select") {
				ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $field_id, $field_text, $field_description, $field_values);
			}
		}
	}
}

function essb_prepare_social_profiles_fields($tab_id, $menu_id) {
	
	foreach (essb_available_social_profiles() as $key => $text) {
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, $text, 'Social profile details');
		ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'profile_'.$key, __('Full address to profile', ESSB3_TEXT_DOMAIN), __('Enter address to your profile in social network', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'profile_text_'.$key, __('Display text with icon', ESSB3_TEXT_DOMAIN), __('Enter custom text that will be displayed with link to your social profile. Example: Follow us on '.$text, ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
	}
}

function essb3_prepare_color_customization_by_network($tab_id, $menu_id) {
	global $essb_networks;
	
	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}
	
	foreach ($checkbox_list_networks as $key => $text) {
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, $text, '');		
		ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'customizer_'.$key.'_bgcolor', __('Background color', ESSB3_TEXT_DOMAIN), __('Replace all buttons background color', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'customizer_'.$key.'_textcolor', __('Text color', ESSB3_TEXT_DOMAIN), __('Replace all buttons text color', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'customizer_'.$key.'_hovercolor', __('Hover background color', ESSB3_TEXT_DOMAIN), __('Replace all buttons hover background color', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_color($tab_id, $menu_id, 'customizer_'.$key.'_hovertextcolor', __('Hover text color', ESSB3_TEXT_DOMAIN), __('Replace all buttons hover text color', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_file($tab_id, $menu_id, 'customizer_'.$key.'_icon', __('Icon', ESSB3_TEXT_DOMAIN), __('Replace social icon', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, 'customizer_'.$key.'_iconbgsize', __('Background size for regular icon', ESSB3_TEXT_DOMAIN), __('Provide custom background size if needed (for retina templates default used is 21px 21px)', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_file($tab_id, $menu_id, 'customizer_'.$key.'_hovericon', __('Hover icon', ESSB3_TEXT_DOMAIN), __('Replace social icon', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, 'customizer_'.$key.'_hovericonbgsize', __('Hover background size for regular icon', ESSB3_TEXT_DOMAIN), __('Provide custom background size if needed (for retina templates default used is 21px 21px)', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
	}
}

function essb3_prepare_advanced_custom_share($tab_id, $menu_id) {
	global $essb_networks;
	
	ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading1', __('Advanced custom share message by social network', ESSB3_TEXT_DOMAIN));
	
	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}
	
	foreach ($checkbox_list_networks as $key => $text) {
		if ($key == "more" || $key == "love" || $key == "mail" || $key == "print") {
			continue;
		}
		$k = $key;
		ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading2', $text);
		ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'as_'.$key.'_url', __('URL:', ESSB3_TEXT_DOMAIN), '');
		if ($k == "facebook" || $k == "twitter" || $k == "pinterest" || $k == "tumblr" || $k == "digg" || $k == "linkedin" || $k == "reddit" || $k == "del" || $k == "buffer" || $k == "whatsapp") {
			ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'as_'.$key.'_text', __('Message:', ESSB3_TEXT_DOMAIN), '');
		}
		if ($k == "facebook" || $k == "pinterest") {
			ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'as_'.$key.'_image', __('Image:', ESSB3_TEXT_DOMAIN), '');
			ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'as_'.$key.'_desc', __('Description:', ESSB3_TEXT_DOMAIN), '');
		}
	}
}

function essb3_prepare_texts_on_button_hover($tab_id, $menu_id) {
	global $essb_networks;
	
	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}
	
	foreach ($checkbox_list_networks as $key => $text) {
		ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, 'hovertext'.'_'.$key, $text, '');
	}
	
}

function essb_prepare_location_network_selection($tab_id, $menu_id, $location = '') {
	global $essb_networks, $essb_options;
	
	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}
	
}

function essb_prepare_location_advanced_customization($tab_id, $menu_id, $location = '', $post_type = false) {
	global $essb_networks, $essb_options;
	
	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}
	
	if ($location != 'mobile') {
		ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading5', __('Deactivate display of functions', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_section_start_full_panels($tab_id, $menu_id);
		
		ESSBOptionsStructureHelper::field_switch_panel($tab_id, $menu_id, $location.'_mobile_deactivate', __('Deactivate on mobile', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish that method to be hidden when site is browsed with mobile device.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
		
		if ($location != 'postbar' && $location != 'point') {
			ESSBOptionsStructureHelper::field_switch_panel($tab_id, $menu_id, $location.'_native_deactivate', __('Deactivate native buttons', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to deactivate native buttons for that display method.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
			
			if (!$post_type) {
				ESSBOptionsStructureHelper::field_switch_panel($tab_id, $menu_id, $location.'_text_deactivate', __('Hide message above, before or below', ESSB3_TEXT_DOMAIN), __('Activate this option if you wish to hide message above, before or below for that display.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
			}
		}
		ESSBOptionsStructureHelper::field_section_end_full_panels($tab_id, $menu_id);
	}	
	
	if ($location == 'mobile') {
		ESSBOptionsStructureHelper::field_checkbox_list_sortable($tab_id, $menu_id, $location.'_networks', __('Change active social networks', ESSB3_TEXT_DOMAIN), __('Do not select anything if you wish to use default network list', ESSB3_TEXT_DOMAIN), $checkbox_list_networks);
	}	
	
	if (!$post_type) {
		ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading2', __('Change default button options for that display location', ESSB3_TEXT_DOMAIN));
	}
	else {
		ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading2', __('Change default button options for that post type', ESSB3_TEXT_DOMAIN));		
	}
	ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_activate', __('I wish to personalize global button settings', ESSB3_TEXT_DOMAIN), __('Activate this option to apply personalized settings for that display location. That will overwrite the global. <br/><span class="essb-user-notice">After switching option to <b>Yes</b> press <b>Update Settings</b> button and advanced configuration fields will appear.</span>', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));

	//print "Location = ".$location.', result = '.ESSBOptionValuesHelper::options_bool_value($essb_options, $location.'_activate');
	$are_active_settings = ESSBOptionValuesHelper::options_bool_value($essb_options, $location.'_activate');
	
	if (!$are_active_settings) {
		return;
	}
	
	
	if ($location != 'postbar' && $location != 'point') {
		ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading4', __('Visual Changes', ESSB3_TEXT_DOMAIN));
		
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Set button style', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_button_style', __('Buttons Style', ESSB3_TEXT_DOMAIN), __('Select your button display style.', ESSB3_TEXT_DOMAIN), essb_avaiable_button_style());
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_button_pos', __('Buttons Align', ESSB3_TEXT_DOMAIN), __('Choose how buttons
				to be aligned. Default position is left but you can also select
				Right or Center', ESSB3_TEXT_DOMAIN), array("" => "Left", "center" => "Center", "right" => "Right"));
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_template', __('Template', ESSB3_TEXT_DOMAIN), __('Select your template for that display location.', ESSB3_TEXT_DOMAIN), essb_available_tempaltes());
		ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_nospace', __('Remove spacing between buttons', ESSB3_TEXT_DOMAIN), __('Activate this option to remove default space between share buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
	
		$more_options = array ("plus" => "Plus icon", "dots" => "Dots icon" );
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_more_button_icon', __('More button icon', ESSB3_TEXT_DOMAIN), __('Select more button icon style. You can choose from default + symbol or dots symbol', ESSB3_TEXT_DOMAIN), $more_options);
		
		$more_options = array ("" => "Default function", "1" => "Display all active networks after more button", "2" => "Display all social networks as pop up", "3" => "Display only active social networks as pop up" );
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_more_button_func', __('More button function', ESSB3_TEXT_DOMAIN), __('Select networks that you wish to appear in your list. With drag and drop you can rearrange them.', ESSB3_TEXT_DOMAIN), $more_options);
		
		$animations_container = array ();
		$animations_container[""] = "Default value from settings";
		foreach (essb_available_animations() as $key => $text) {
			if ($key != '') {
				$animations_container[$key] = $text;
			}
			else {
				$animations_container['no'] = 'No amination';
			}
		}
		
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_css_animations', __('Activate animations', ESSB3_TEXT_DOMAIN), __('Animations
				are provided with CSS transitions and work on best with retina
				templates.', ESSB3_TEXT_DOMAIN), $animations_container);
		
		
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
		
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Counter settings', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_show_counter', __('Display counter of sharing', ESSB3_TEXT_DOMAIN), __('Activate display of share counters.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_counter_pos', __('Position of counters', ESSB3_TEXT_DOMAIN), __('Choose your default button counter position', ESSB3_TEXT_DOMAIN), essb_avaliable_counter_positions());
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_total_counter_pos', __('Position of total counter', ESSB3_TEXT_DOMAIN), __('For vertical display methods left means before buttons (top) and right means after buttons (bottom).', ESSB3_TEXT_DOMAIN), essb_avaiable_total_counter_position());
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
		
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Set button width', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_button_width', __('Width of buttons'), __('Choose between automatic width, pre defined width or display in columns.'), array(''=>'Automatic Width', 'fixed' => 'Fixed Width', 'full' => 'Full Width', "column" => "Display in columns"));
	
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Fixed width share buttons', ESSB3_TEXT_DOMAIN), __('Customize the fixed width options', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, $location.'_fixed_width_value', __('Custom buttons width', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_fixed_width_align', __('Choose alignment of network name', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), array("" => "Center", "left" => "Left", "right" => "Right"));
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
		
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Full width share buttons', ESSB3_TEXT_DOMAIN), __('Full width option will make buttons to take the width of your post content area.', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, $location.'_fullwidth_share_buttons_correction', __('Max width of button on desktop', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, $location.'_fullwidth_share_buttons_correction_mobile', __('Max width of button on mobile', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
		ESSBOptionsStructureHelper::field_textbox($tab_id, $menu_id, $location.'_fullwidth_share_buttons_container', __('Max width of buttons container element', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', 'input60', 'fa-arrows-h', 'right');
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_fullwidth_align', __('Choose alignment of network name', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), array("left" => "Left", "center" => "Center", "right" => "Right"));
		
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
		
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Display in columns', ESSB3_TEXT_DOMAIN), '');
		$listOfOptions = array("1" => "1", "2" => "2", "3" => "3", "4" => "4", "5" => "5");
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_fullwidth_share_buttons_columns', __('Number of columns', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), $listOfOptions);
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_fullwidth_share_buttons_columns_align', __('Choose alignment of network name', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), array("" => "Left", "center" => "Center", "right" => "Right"));
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
		
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
	}
	//ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Personalize social networks', ESSB3_TEXT_DOMAIN), '');
	ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading4', __('Personalize social networks', ESSB3_TEXT_DOMAIN));
	
	if ($location != 'mobile') {
		ESSBOptionsStructureHelper::field_checkbox_list_sortable($tab_id, $menu_id, $location.'_networks', __('Change active social networks', ESSB3_TEXT_DOMAIN), __('Do not select anything if you wish to use default network list'. ESSB3_TEXT_DOMAIN), $checkbox_list_networks);
	}
	ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Rename displayed texts for network names', ESSB3_TEXT_DOMAIN), __('Set texts that will appear on selected display method instead of default network names. Use dash (-) if you wish to remove text for that network name.', ESSB3_TEXT_DOMAIN));
	
	foreach ($checkbox_list_networks as $key => $text) {
		ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, $location.'_'.$key.'_name', $text, '');
	}
	
	ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
	
	//ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
	
}

function essb_prepare_location_advanced_customization_mobile($tab_id, $menu_id, $location = '') {
	global $essb_networks;

	$checkbox_list_networks = array();
	foreach ($essb_networks as $key => $object) {
		$checkbox_list_networks[$key] = $object['name'];
	}


	ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading2', __('Change default button options for that display location', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_activate', __('I wish to personalize global button settings for that location', ESSB3_TEXT_DOMAIN), __('Activate this option to apply personalized settings for that display location that will overwrite the global.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
	ESSBOptionsStructureHelper::field_heading($tab_id, $menu_id, 'heading3', __('Visual Changes', ESSB3_TEXT_DOMAIN));

	ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Set button style', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));

	ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_template', __('Template', ESSB3_TEXT_DOMAIN), __('Select your template for that display location.', ESSB3_TEXT_DOMAIN), essb_available_tempaltes());
	
	if ($location != 'sharebottom') {
		ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_nospace', __('Remove spacing between buttons', ESSB3_TEXT_DOMAIN), __('Activate this option to remove default space between share buttons.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
	}

	ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);

	if ($location != 'sharebottom') {
		ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Counter settings', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_switch($tab_id, $menu_id, $location.'_show_counter', __('Display counter of sharing', ESSB3_TEXT_DOMAIN), __('Activate display of share counters.', ESSB3_TEXT_DOMAIN), '', __('Yes', ESSB3_TEXT_DOMAIN), __('No', ESSB3_TEXT_DOMAIN));
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_counter_pos', __('Position of counters', ESSB3_TEXT_DOMAIN), __('Choose your default button counter position', ESSB3_TEXT_DOMAIN), essb_avaliable_counter_positions_mobile());
		ESSBOptionsStructureHelper::field_select($tab_id, $menu_id, $location.'_total_counter_pos', __('Position of total counter', ESSB3_TEXT_DOMAIN), __('For vertical display methods left means before buttons (top) and right means after buttons (bottom).', ESSB3_TEXT_DOMAIN), essb_avaiable_total_counter_position_mobile());
		ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);
	}
	ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Personalize social networks', ESSB3_TEXT_DOMAIN), '');
	ESSBOptionsStructureHelper::field_checkbox_list_sortable($tab_id, $menu_id, $location.'_networks', __('Change active social networks', ESSB3_TEXT_DOMAIN), __('Do not select anything if you wish to use default network list'. ESSB3_TEXT_DOMAIN), $checkbox_list_networks);

	ESSBOptionsStructureHelper::field_section_start($tab_id, $menu_id, __('Rename displayed texts for network names', ESSB3_TEXT_DOMAIN), __('Set texts that will appear on selected display method instead of default network names. Use dash (-) if you wish to remove text for that network name.', ESSB3_TEXT_DOMAIN));

	foreach ($checkbox_list_networks as $key => $text) {
		ESSBOptionsStructureHelper::field_textbox_stretched($tab_id, $menu_id, $location.'_'.$key.'_name', $text, '');
	}

	ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);

	ESSBOptionsStructureHelper::field_section_end($tab_id, $menu_id);

}


/**
 * Options Creator Helper Class
 * ---
 * @author appscreo
 *
 */
class ESSBOptionsStructureHelper {
	
	public static function capitalize($text) {
		return ucfirst($text);
	}
	
	public static function init() {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;

		$essb_navigation_tabs = array();
		$essb_sidebar_sections = array();
		$essb_sidebar_sections = array();
	}

	public static function tab($tab_id, $tab_text, $tab_title, $hide_update_button = false, $hide_in_navigation = false, $wizard_tab = false) {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		
		$essb_navigation_tabs[$tab_id] = $tab_text;
		$essb_sidebar_sections[$tab_id] = array(
				'title' => $tab_title,
				'fields' => array(),
				'hide_update_button' => $hide_update_button,
				'hide_in_navigation' => $hide_in_navigation,
				'wizard_tab' => $wizard_tab
				);
		
		$essb_section_options[$tab_id] = array();
	}
	
	public static function menu_item($tab_id, $id, $title, $icon = 'default', $action = '', $default_child = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		
		$essb_sidebar_sections[$tab_id]['fields'][] = array(
				'field_id' => $id,
				'title' => $title,
				'icon' => $icon,
				'type' => 'menu_item',
				'action' => $action,
				'default_child' => $default_child
				);
	}
	
	public static function submenu_item ($tab_id, $id, $title, $icon = 'default', $action = 'menu', $level2 = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_sidebar_sections[$tab_id]['fields'][] = array(
				'field_id' => $id,
				'title' => $title,
				'icon' => $icon,
				'type' => 'sub_menu_item',
				'action' => $action,
				'level2' => $level2
		);
		
		if ($action == 'menu') {
			$essb_section_options[$tab_id][$id] = array();
		}
	}
	
	public static function field_heading($tab_id, $menu_id, $level = 'heading1', $title = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'type' => $level,
				'title' => $title
				);
		
	}
	
	public static function field_switch ($tab_id, $menu_id, $id, $title, $description, $recommended = '', $on_label = '', $off_label = '', $default_value = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'switch',
				'title' => $title,
				'description' => $description,
				'recommended' => $recommended,
				'on_label' => $on_label,
				'off_label' => $off_label,
				'default_value' => $default_value
		);
	}

	public static function field_switch_panel ($tab_id, $menu_id, $id, $title, $description, $recommended = '', $on_label = '', $off_label = '', $default_value = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'switch-in-panel',
				'title' => $title,
				'description' => $description,
				'recommended' => $recommended,
				'on_label' => $on_label,
				'off_label' => $off_label,
				'default_value' => $default_value
		);
	}
	
	public static function field_textbox ($tab_id, $menu_id, $id, $title, $description, $recommended = '', $class = '', $icon = '', $icon_position = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'text',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'class' => $class,
				'icon' => $icon,
				'icon_position' => $icon_position
		);
	}

	public static function field_textbox_panel ($tab_id, $menu_id, $id, $title, $description, $recommended = '', $class = '', $icon = '', $icon_position = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'text-in-panel',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'class' => $class,
				'icon' => $icon,
				'icon_position' => $icon_position
		);
	}
	
	public static function field_textbox_stretched ($tab_id, $menu_id, $id, $title, $description, $recommended = '', $class = '', $icon = '', $icon_position = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'text-stretched',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'class' => $class,
				'icon' => $icon,
				'icon_position' => $icon_position
		);
	}
	
	public static function field_checkbox ($tab_id, $menu_id, $id, $title, $description, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'checkbox',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended
		);
	}
	
	public static function field_checkbox_list ($tab_id, $menu_id, $id, $title, $description, $values, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'checkbox_list',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'values' => $values
		);
	}
	
	public static function field_checkbox_list_sortable ($tab_id, $menu_id, $id, $title, $description, $values, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'checkbox_list_sortable',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'values' => $values
		);
	}
	
	public static function field_select ($tab_id, $menu_id, $id, $title, $description, $values, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'select',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'values' => $values
		);
	}

	public static function field_select_panel ($tab_id, $menu_id, $id, $title, $description, $values, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'select-in-panel',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'values' => $values
		);
	}
	
	public static function field_textarea ($tab_id, $menu_id, $id, $title, $description, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'textarea',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended
		);
	}

	public static function field_editor ($tab_id, $menu_id, $id, $title, $description, $mode = 'javascript', $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'editor',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'mode' => $mode
		);
	}
	
	public static function field_wpeditor ($tab_id, $menu_id, $id, $title, $description, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'wpeditor',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended
		);
	}	

	public static function field_color ($tab_id, $menu_id, $id, $title, $description, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'color',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended
		);
	}
	
	public static function field_color_panel ($tab_id, $menu_id, $id, $title, $description, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'color-in-panel',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended
		);
	}
	
	public static function field_image_checkbox ($tab_id, $menu_id, $id, $title, $description, $values, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'image_checkbox',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'values' => $values
		);
	}

	public static function field_image_radio ($tab_id, $menu_id, $id, $title, $description, $values, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'image_radio',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'values' => $values
		);
	}

	public static function field_file ($tab_id, $menu_id, $id, $title, $description, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'file',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended
		);
	}
	
	public static function field_simplesort ($tab_id, $menu_id, $id, $title, $description, $values, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'simplesort',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'values' => $values
		);
	}
	
	public static function field_select2 ($tab_id, $menu_id, $id, $title, $description, $values, $multiple = false, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'select2',
				'title' => $title,
				'description' => $description,
				'recommeded' => $recommended,
				'values' => $values,
				'select2_options' => array('allow_clear' => false, 'multiple' => $multiple, 'placeholder' => '')
		);
	}
	
	public static function field_func ($tab_id, $menu_id, $id, $title, $description, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'id' => $id,
				'type' => 'func',
				'title' => $title,
				'description' => $description
		);
	}
	
	//								array ('type' => 'section_start', 'title' => __('Section Start', ESSB3_TEXT_DOMAIN), 'description' => 'Demo section description'),
	public static function field_section_start ($tab_id, $menu_id, $title, $description, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'type' => 'section_start',
				'title' => $title,
				'description' => $description,
				'recommended' => $recommended
		);
	}
	
	public static function field_section_end ($tab_id, $menu_id) {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'type' => 'section_end'
		);
	}

	public static function field_section_start_panels ($tab_id, $menu_id, $title, $description, $recommended = '') {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'type' => 'section_start_panels',
				'title' => $title,
				'description' => $description,
				'recommended' => $recommended
		);
	}
	
	public static function field_section_end_panels ($tab_id, $menu_id) {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'type' => 'section_end_panels'
		);
	}
	
	public static function field_section_end_full_panels ($tab_id, $menu_id) {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'type' => 'section_end_full_panels'
		);
	}
	public static function field_section_start_full_panels ($tab_id, $menu_id) {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		$essb_section_options[$tab_id][$menu_id][] = array(
				'type' => 'section_start_full_panels'
		);
	}
	
}

//-- help functions for advanced options
function essb3_options_template_select() {
	global $essb_admin_options;
	
	$options = $essb_admin_options;
		$n1 = $n2 = $n3 = $n4 = $n5 = $n6 = $n7 = $n8 = $n9 = $n10 = $n11 = $n12 = $n13 = $n14 = $n15 = $n16 = $n17 = $n18 = $n19 = $n20 = $n21 = $n22 = $n23 = $n24 = $n25 = $n26 = $n27 = $n28 = $n29 = $n30 = $n31 = "";
	if (is_array ( $options )) {
		${'n' . $options ['style']} = " checked='checked'";
	}
	else { $n1 = " checked='checked'"; }
			echo '
			<input id="essb_style_1" value="1" name="essb_options[style]" type="radio" ' . $n1 . ' />&nbsp;&nbsp;' . __ ( 'Default', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-default.png"/>
			<br/><br/>
			<input id="essb_style_2" value="2" name="essb_options[style]" type="radio" ' . $n2 . ' />&nbsp;&nbsp;' . __ ( 'Metro', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-metro.png"/>
			<br/><br/>
			<input id="essb_style_3" value="3" name="essb_options[style]" type="radio" ' . $n3 . ' />&nbsp;&nbsp;' . __ ( 'Modern', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-modern.png"/>
			<br/><br/>
			<input id="essb_style_4" value="4" name="essb_options[style]" type="radio" ' . $n4 . ' />&nbsp;&nbsp;' . __ ( 'Round', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-round.png"/><br/><span class="small">Round style works correct only with Hide Social Network Names: <strong>Yes</strong>. If this option is not set to Yes please change its value or template will not render correct.</span>
			<br/><br/>
			<input id="essb_style_5" value="5" name="essb_options[style]" type="radio" ' . $n5 . ' />&nbsp;&nbsp;' . __ ( 'Big', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-big.png"/>
			<br/><br/>
			<input id="essb_style_6" value="6" name="essb_options[style]" type="radio" ' . $n6 . ' />&nbsp;&nbsp;' . __ ( 'Metro (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-metro2.png"/>
			<br/><br/>
			<input id="essb_style_7" value="7" name="essb_options[style]" type="radio" ' . $n7 . ' />&nbsp;&nbsp;' . __ ( 'Big (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-big-retina.png"/>
			<br/><br/>
			<input id="essb_style_8" value="8" name="essb_options[style]" type="radio" ' . $n8 . ' />&nbsp;&nbsp;' . __ ( 'Light (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-light-retina.png"/>
			<br/><br/>
			<input id="essb_style_9" value="9" name="essb_options[style]" type="radio" ' . $n9 . ' />&nbsp;&nbsp;' . __ ( 'Flat (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-flat.png"/>
			<br/><br/>
			<input id="essb_style_10" value="10" name="essb_options[style]" type="radio" ' . $n10 . ' />&nbsp;&nbsp;' . __ ( 'Tiny (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-tiny.png"/>
			<br/><br/>
			<input id="essb_style_11" value="11" name="essb_options[style]" type="radio" ' . $n11 . ' />&nbsp;&nbsp;' . __ ( 'Round (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-round-retina.png"/>
			<br/><br/>
			<input id="essb_style_12" value="12" name="essb_options[style]" type="radio" ' . $n12 . ' />&nbsp;&nbsp;' . __ ( 'Modern (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-modern-retina.png"/>
			<br/><br/>
			<input id="essb_style_13" value="13" name="essb_options[style]" type="radio" ' . $n13 . ' />&nbsp;&nbsp;' . __ ( 'Circles (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-circles-retina.png"/>
			<br/><br/>
			<input id="essb_style_14" value="14" name="essb_options[style]" type="radio" ' . $n14 . ' />&nbsp;&nbsp;' . __ ( 'Blocks (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-blocks-retina.png"/>
			<br/><br/>
			<input id="essb_style_15" value="15" name="essb_options[style]" type="radio" ' . $n15 . ' />&nbsp;&nbsp;' . __ ( 'Dark (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-dark-retina.png"/>
			<br/><br/>
			<input id="essb_style_16" value="16" name="essb_options[style]" type="radio" ' . $n16 . ' />&nbsp;&nbsp;' . __ ( 'Grey Circles (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-grey-circles-retina.png"/>
			<br/><br/>
			<input id="essb_style_17" value="17" name="essb_options[style]" type="radio" ' . $n17 . ' />&nbsp;&nbsp;' . __ ( 'Grey Blocks (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-grey-blocks-retina.png"/>
			<br/><br/>
			<input id="essb_style_18" value="18" name="essb_options[style]" type="radio" ' . $n18 . ' />&nbsp;&nbsp;' . __ ( 'Clear (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-clear-retina.png"/>
			<br/><br/>
			<input id="essb_style_19" value="19" name="essb_options[style]" type="radio" ' . $n19 . ' />&nbsp;&nbsp;' . __ ( 'Copy (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-copy-retina.png"/>
			<br/><br/>
			<input id="essb_style_20" value="20" name="essb_options[style]" type="radio" ' . $n20 . ' />&nbsp;&nbsp;' . __ ( 'Dimmed (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-dimmed-retina.png"/>
			<br/><br/>
			<input id="essb_style_21" value="21" name="essb_options[style]" type="radio" ' . $n21 . ' />&nbsp;&nbsp;' . __ ( 'Grey (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-grey-retina.png"/>
			<br/><br/>
			<input id="essb_style_22" value="22" name="essb_options[style]" type="radio" ' . $n22 . ' />&nbsp;&nbsp;' . __ ( 'Default 3.0 (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-default3-retina.png"/>
			<br/><br/>
			<input id="essb_style_23" value="23" name="essb_options[style]" type="radio" ' . $n23 . ' />&nbsp;&nbsp;' . __ ( 'Jumbo (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-jumbo-retina.png"/>
			<br/><br/>
			<input id="essb_style_24" value="24" name="essb_options[style]" type="radio" ' . $n24 . ' />&nbsp;&nbsp;' . __ ( 'Jumbo Rounded (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-jumbo-rounded-retina.png"/>
			<br/><br/>
			<input id="essb_style_25" value="25" name="essb_options[style]" type="radio" ' . $n25 . ' />&nbsp;&nbsp;' . __ ( 'Fancy (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-fancy-retina.png"/>
			<br/><br/>
			<input id="essb_style_26" value="26" name="essb_options[style]" type="radio" ' . $n26 . ' />&nbsp;&nbsp;' . __ ( 'Deluxe (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-deluxe-retina.png"/>
			<br/><br/>
			<input id="essb_style_27" value="27" name="essb_options[style]" type="radio" ' . $n27 . ' />&nbsp;&nbsp;' . __ ( 'Modern Slim (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-modern-slim-retina.png"/>
			<br/><br/>
			<input id="essb_style_28" value="28" name="essb_options[style]" type="radio" ' . $n28 . ' />&nbsp;&nbsp;' . __ ( 'Bold (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-bold-retina.png"/>
			<br/><br/>
			<input id="essb_style_29" value="29" name="essb_options[style]" type="radio" ' . $n29 . ' />&nbsp;&nbsp;' . __ ( 'Fancy Bold (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-fancy-bold-retina.png"/>
			<br/><br/>
			<input id="essb_style_30" value="30" name="essb_options[style]" type="radio" ' . $n30 . ' />&nbsp;&nbsp;' . __ ( 'Retro (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-retro-retina.png"/>
			<br/><br/>
			<input id="essb_style_31" value="31" name="essb_options[style]" type="radio" ' . $n31 . ' />&nbsp;&nbsp;' . __ ( 'Metro Bold (Retina)', ESSB3_TEXT_DOMAIN ) . '<br /><img src="' . ESSB3_PLUGIN_URL . '/assets/images/demo-style-metro-bold-retina.png"/>
			';
	
}

function essb3_reset_postdata() {
	echo '<a href="admin.php?page=essb_redirect_advanced&tab=advanced&reset_settings=true" class="button">'.__('I want to reset plugin settings to default', ESSB3_TEXT_DOMAIN).'</a>';
}

function essb3_convert_postdata() {
	echo '<a href="admin.php?page=essb_redirect_advanced&tab=advanced&section=convert&import2x=settings" class="button">'.__('Import plugin settings', ESSB3_TEXT_DOMAIN).'</a>';
	echo '<br/><br/><a href="admin.php?page=essb_redirect_advanced&tab=advanced&section=convert&import2x=post" class="button">'.__('Import posts settings', ESSB3_TEXT_DOMAIN).'</a>';
	echo '<br/><br/><a href="admin.php?page=essb_redirect_advanced&tab=advanced&section=convert&import2x=fans" class="button">'.__('Import followers counter settings', ESSB3_TEXT_DOMAIN).'</a>';	
	echo '<br/><br/><a href="admin.php?page=essb_redirect_advanced&tab=advanced&section=convert&import2x=stats" class="button">'.__('Import click log data from previous versions', ESSB3_TEXT_DOMAIN).'</a>';	
}

function essb3_counter_recovery() {
	echo '<b>Share counter recovery</b> function can work only when counters are running in cached mode. This is required to prevent your site from getting down if there are two many requests to social networks. To switch counters from real time to cached counters please visit Social Buttons -> Social Sharing -> Counters.';
}

function essb3_twitter_preload() {
	
	echo '<table border="0" cellpadding="5" cellspacing="0" width="100%">
	<col width="35%" />
	<col width="65%" />';
	
	echo '<tr class="even table-border-bottom">';
	echo '<td valign="top" class="bold">Minimal share counter value<br/><span class="label">Fill in minimal value of shared counter for Twitter. This value will be applied on all posts/pages of your blog. If you recover share counter from Metrics Lite data this value will be used if Twitter share counter is less than it.</span></td>';
	echo '<td valign="top">';
	echo '<input type="text" id="essb3_twitter_min" class="input-element" />';
	echo '</td>';
	echo '</tr>';

	echo '<tr class="odd table-border-bottom">';
	echo '<td valign="top" class="bold">Apply minimal share counter value on all posts<br/><span class="label">Press this button to preload initial share counter value for Twitter on all posts with minimal share value that is filled.</span></td>';
	echo '<td valign="top">';
	echo '<a href="#" class="button" onclick="essb3_call_twitter_update(\'minimal\'); return false;">Apply minimal share counter value</a>';
	echo '</td>';
	echo '</tr>';

	echo '<tr class="even table-border-bottom">';
	echo '<td valign="top" class="bold">Recover share counter from Metrics Lite<br/><span class="label">Press this button to preload initial share counter value for Twitter on all posts based on last value available for Twitter in Social Metrics Lite. If minimal share value is filled initial share counter will be loaded with that number when data from Metrics Lite have less shares<br/><br/>This option can be used only if Social Metrics Lite was actived and have data stored.</span></td>';
	echo '<td valign="top">';
	echo '<a href="#" class="button" onclick="essb3_call_twitter_update(\'metrics\'); return false;">Recover share counter from Metrics Lite</a>';
	echo '</td>';
	echo '</tr>';

	echo '<tr class="odd table-border-bottom">';
	echo '<td valign="top" class="bold">Recover share counter from Social Share Analytics<br/><span class="label">Press this button to preload initial share counter value for Twitter on all posts based on value of clicks on Twitter button in Social Share Click Logging. If minimal share value is filled initial share counter will be loaded with that number when data from analytics have less shares.<br/><br/>This option can be used only if Social Share Click Logging was actived and have data stored.</span></td>';
	echo '<td valign="top">';
	echo '<a href="#" class="button" onclick="essb3_call_twitter_update(\'analytics\'); return false;">Recover share counter from Social Share Analytics</a>';
	echo '</td>';
	echo '</tr>';
	
	echo '</table>';		
	
	echo '<script type="text/javascript">';
	
	echo 'var essb3_call_twitter_update = function(update_from) {
		var user_minimal_value = jQuery(\'#essb3_twitter_min\').val();
		
		var url_to_redirect = "admin.php?page=essb_redirect_advanced&tab=advanced&section=twitter&recover_twitter="+user_minimal_value+"&recover_from="+update_from;
		
		window.location = url_to_redirect;
	}
	';
	
	echo '</script>';
	
}

function essb3_network_selection() {
	global $essb_admin_options, $essb_networks;
	$active_networks = array();
	
	$network_order = array();
	if (is_array($essb_admin_options)) {
		$active_networks = isset($essb_admin_options['networks']) ? $essb_admin_options['networks'] : array();
		$network_order = isset($essb_admin_options['networks_order']) ? $essb_admin_options['networks_order'] : array();
	}
	
	if (count($network_order) > 0) {
		if (!in_array('sms', $network_order)) {
			$network_order[] = "sms";			
		}
		if (!in_array('viber', $network_order)) {
			$network_order[] = 'viber';
		}
		if (!in_array('telegram', $network_order)) {
			$network_order[] = 'telegram';
		}
		if (!in_array('subscribe', $network_order)) {
			$network_order[] = 'subscribe';
		}
	}
	
	// populate the default networks for sorting;
	if (count($network_order) == 0) {
		$network_order = array();
		foreach ($essb_networks as $key => $data) {
			$network_order[] = $key;			
		}
		
	}
	print '<ul class="essb-main-network-order" id="essb-main-network-list">';
	
	foreach ($network_order as $network) {
		
		$current_network_name = isset($essb_networks[$network]) ? $essb_networks[$network]["name"] : $network;
		$current_network_supports = isset($essb_networks[$network]) ? $essb_networks[$network]["supports"] : $network;
		
		$supports = "";
		if ($current_network_supports != '') {
			$supports_object = explode(',', $current_network_supports);
			
			foreach ($supports_object as $singleSupportFeature) {
				$supports .= sprintf('<span class="essb-network-supports">%1$s</span>', $singleSupportFeature);
			}
		}
		
		$is_active_network = in_array($network, $active_networks) ? "checked=\"checked\"" : "";
		//echo '<li>'.$network.'</li>';
		printf('<li class="essb-network-select essb-network-select-%1$s">', $network);
		
		printf('<span class="essb-single-network-select essb-single-network-select-%1$s"><input type="checkbox" name="essb_options[networks][]" value="%1$s" %3$s/><span class="essb_icon essb-icon-%1$s"></span><span class="essb-sns-name">%2$s</span>%4$s<input type="checkbox" name="essb_options[networks_order][]" value="%1$s" checked="checked" style="display:none;"/></span>', $network, $current_network_name, $is_active_network, $supports);
		
		print '</li>';
	}
	
	print '</ul>';
	
	echo '<script type="text/javascript">';
	echo 'jQuery(document).ready(function(){';
    echo 'jQuery("#essb-main-network-list").sortable();';
    echo '});';
	echo '</script>';
}

function essb3_network_rename() {
	global $essb_admin_options, $essb_networks;
	
	$network_order = array();
	
	if (is_array($essb_admin_options)) {
		$active_networks = isset($essb_admin_options['networks']) ? $essb_admin_options['networks'] : array();
		$network_order = isset($essb_admin_options['networks_order']) ? $essb_admin_options['networks_order'] : array();
	}
	
	// populate the default networks for sorting;
	if (count($network_order) == 0) {
		foreach ($essb_networks as $key => $data) {
			$network_order[] = $key;
		}
	}
	
	print '<ul class="essb-main-network-order" id="essb-main-network-list">';
	
	foreach ($network_order as $network) {
	
		$current_network_name = isset($essb_networks[$network]) ? $essb_networks[$network]["name"] : $network;
		$user_network_name = isset($essb_admin_options['user_network_name_'.$network]) ? $essb_admin_options['user_network_name_'.$network] : '';
		
		if ($user_network_name == '') { $user_network_name = $current_network_name; }
		//echo '<li>'.$network.'</li>';
		printf('<li class="essb-network-name-%1$s">', $network);
	
		printf('<span class="essb_icon essb_icon_%1$s"></span><input type="text" class="input-element" name="essb_options_names[%1$s]" value="%2$s"/>&nbsp;%3$s',$network,$user_network_name, $current_network_name);
	
		print '</li>';
	}
	
	print '</ul>';
}

function essb3_text_analytics() {
	?>
	You can
									visit
<a href="https://support.google.com/analytics/answer/1033867?hl=en"
	target="_blank">this page</a>
for more information on how to use and generate these parameters.
<br />
To include the social network into parameters use the following code
<b>{network}</b>
. When that code is reached it will be replaced with the network name (example: facebook). An example campaign trakcing code include network will look like this utm_source=essb_settings&utm_medium=needhelp&utm_campaign={network} - in this configuration when you press Facebook button {network} will be replaced with facebook, if you press Twitter button it will be replaced with twitter.
To include the post title into parameters use the following code
<b>{title}</b>
. When that code is reached it will be replaced with the post title.
<?php 
}

function essb3_text_backup() {
	global $essb_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';
	
	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_options);
	}
	
	$download_settings = "admin-ajax.php?action=essb_settings_save";
	
	?>
	
	<textarea id="essb_options_configuration" name="essb_backup[configuration]" class="input-element stretched" rows="15"><?php echo $backup_string; ?></textarea>
	<a href="<?php echo $goback; ?>" class="button essb-button">Export Settings</a>
	
	<?php 
}

function essb3_text_backup_import() {
	global $essb_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';

	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_options);
	}

	$download_settings = "admin-ajax.php?action=essb_settings_save";

	?>
	
	<textarea id="essb_options_configuration1" name="essb_backup[configuration1]" class="input-element stretched" rows="15"></textarea>
	<input type="Submit" name="Submit" value="Import Settings" class="button essb-button">
		<?php 
}

function essb3_text_backup_import1() {
	global $essb_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';

	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_options);
	}

	$download_settings = "admin-ajax.php?action=essb_settings_save";

	?>
	
	<input type="file" name="essb_backup_file"/>
	<input type="Submit" name="Submit" value="Import Settings From File" class="button essb-button">
		<?php 
}


function essb3_text_backup1() {
	global $essb_options;
	$goback = esc_url_raw(add_query_arg(array('backup' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	$is_backup = isset($_REQUEST['backup']) ? $_REQUEST['backup'] : '';

	$backup_string = '';
	if ($is_backup == 'true') {
		$backup_string = json_encode($essb_options);
	}

	$download_settings = "admin-ajax.php?action=essb_settings_save";

	?>
	
	<a href="<?php echo $download_settings; ?>" class="button essb-button">Save Plugin Settings To File</a>&nbsp;
	<?php 
}
function essb3_text_readymade() {
	include_once(ESSB3_PLUGIN_ROOT . '/lib/admin/essb-readymade-styles.php');
	$goback = esc_url_raw(add_query_arg(array('import' => 'true'), 'admin.php?page=essb_redirect_import&tab=import'));
	essb_draw_readymade_list();
	
}

function essb3_text_automatic_updates() {
	?>
	A valid purchase code qualifies you for support and enables automatic updates. A purchase code may only be used for one <b>Easy Social Share Buttons for WordPress</b> installation on one WordPress site at a time. If you previosly activated your purchase code on another site, then you should deactivate it first or obtain <a href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo" target="_blank">new purchase key</a>.
	<h4>How to find my purchase code</h4>
	Here is how to find your purchase code: open <b>Easy Social Share Buttons for WordPress</b> page in CodeCanyon <a href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo" target="_blank">http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476</a> and visit the <b>Support</b> tab.
	<br/><br/>
	<img src="<?php echo ESSB3_PLUGIN_URL ?>/assets/images/purchase_code1.png"/>
	<br/><br/>
	Scroll down on that page till you find Purchase code section
	<br/><br/>
	<img src="<?php echo ESSB3_PLUGIN_URL ?>/assets/images/purchase_code2.png"/>
	<?php 
}

function essb3_post_type_select() {
	global $essb_admin_options, $wp_post_types;
	
	$pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
	$cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );
	
	$current_posttypes = array();
	if (is_array($essb_admin_options)) {
		$current_posttypes = ESSBOptionValuesHelper::options_value($essb_admin_options, 'display_in_types', array());
	}
	
	//print_r($current_posttypes);
	
	if (!is_array($current_posttypes)) { $current_posttypes = array(); }
	echo '<ul>';
	
	foreach ($pts as $pt) {
		$selected = in_array ( $pt, $current_posttypes ) ? 'checked="checked"' : '';
		printf('<li><input type="checkbox" name="essb_options[display_in_types][]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label></li>', $pt, $selected, $wp_post_types [$pt]->label);
	}
	
	foreach ($cpts as $pt) {
		$selected = in_array ( $pt, $current_posttypes  ) ? 'checked="checked"' : '';
		printf('<li><input type="checkbox" name="essb_options[display_in_types][]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label></li>', $pt, $selected, $wp_post_types [$pt]->label);
	}
	
	$selected = in_array ( 'all_lists', $current_posttypes  ) ? 'checked="checked"' : '';
	printf('<li><input type="checkbox" name="essb_options[display_in_types][]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label></li>', 'all_lists', $selected, 'Lists of articles (blog, archives, search results, etc.)');
	
	echo '</ul>';
}

function essb3_esml_post_type_select() {
	global $essb_admin_options, $wp_post_types;

	$pts = get_post_types ( array ('show_ui' => true, '_builtin' => true ) );
	$cpts = get_post_types ( array ('show_ui' => true, '_builtin' => false ) );

	$current_posttypes = array();
	if (is_array($essb_admin_options)) {
		$current_posttypes = ESSBOptionValuesHelper::options_value($essb_admin_options, 'esml_monitor_types', array());
	}

	if (!is_array($current_posttypes)) {
		$current_posttypes = array();
	}
	echo '<ul>';

	foreach ($pts as $pt) {
		$selected = in_array ( $pt, $current_posttypes ) ? 'checked="checked"' : '';
		printf('<li><input type="checkbox" name="essb_options[esml_monitor_types][]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label></li>', $pt, $selected, $wp_post_types [$pt]->label);
	}

	foreach ($cpts as $pt) {
		$selected = in_array ( $pt, $current_posttypes  ) ? 'checked="checked"' : '';
		printf('<li><input type="checkbox" name="essb_options[esml_monitor_types][]" id="%1$s" value="%1$s" %2$s> <label for="%1$s">%3$s</label></li>', $pt, $selected, $wp_post_types [$pt]->label);
	}

	echo '</ul>';
}

/*$essb_navigation_tabs = array ('social' => __ ( 'Social Buttons', ESSB3_TEXT_DOMAIN ), 
		'display' => __ ( 'Display Settings', ESSB3_TEXT_DOMAIN ), 
		'customizer' => __ ( 'Style Settings', ESSB3_TEXT_DOMAIN ), 
		'shortcode2' => __ ( 'Shortcode Generator', ESSB3_TEXT_DOMAIN ), 
		"stats" => "Click Statistics", "backup" => "Import/Export Settings", 
		"update" => "Automatic Updates" );

$essb_sidebar_sections = array(
		'social' => array(
				'title' => __('Social Share, Like, Follow & Subscribe Buttons', ESSB3_TEXT_DOMAIN),
				'fields' => array(
								
								array (
										'field_id' => 'locations',
										'title' => __('Locations', ESSB3_TEXT_DOMAIN),
										'type' => 'menu_item',
										'action' => 'activate_first',
										'default_child' => 'positions-3',
										'icon' =>  'map-marker',
								),
								array(
										'field_id' => 'positions-3',
										'title' => __('Post Types', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'locations-2',
										'title' => __('Display Positions', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'locations-3',
										'title' => __('Content Top', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'positions-6',
										'title' => __('Content Bottom', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'positions-7',
										'title' => __('Sidebar', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'positions-8',
										'title' => __('Top bar', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'locations-7',
										'title' => __('Bottom bar', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'locations-8',
										'title' => __('Float from content top', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'positions-10',
										'title' => __('Post vertical float', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'positions-30',
										'title' => __('Fly In', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'positions-31',
										'title' => __('Pop up', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'positions-32',
										'title' => __('On Media', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),						
								array (
									'field_id' => 'social-share',
									'title' => __('Social Sharing', ESSB3_TEXT_DOMAIN),
									'type' => 'menu_item',
									'action' => 'activate_first',
									'default_child' => 'social-share-1',
									'icon' =>  'share-alt',
								),
								array(
										'field_id' => 'social-share-1',
										'title' => __('Social Networks', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'social-share-2',
										'title' => __('Additional Network Options', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'social-share-3',
										'title' => __('Template & Style', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'social-share-4',
										'title' => __('Counters', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),
								array(
										'field_id' => 'social-share-5',
										'title' => __('Network Names', ESSB3_TEXT_DOMAIN),
										'type' => 'sub_menu_item',
										'action' => 'menu',
										'icon' =>  'default',
								),

								array(
									'field_id' => 'social-1',
								    'title' => __('Social Buttons', ESSB3_TEXT_DOMAIN),
									'type' => 'menu_item',
									'action' => 'activate_first',
									'default_child' => 'social-1-1', 
								    'icon' =>  'share-alt',
								),
								array(
									'field_id' => 'social-1-1',
									'title' => __('Social Buttons 1 1', ESSB3_TEXT_DOMAIN),
									'type' => 'sub_menu_item',
									'action' => 'menu',
									'icon' =>  'default',
								),
								array(
									'field_id' => 'social-1-2',
									'title' => __('Social Buttons 1 2', ESSB3_TEXT_DOMAIN),
									'type' => 'sub_menu_item',
									'action' => 'menu',
									'icon' =>  'default',										
								),
								array(
									'field_id' => 'social-2',
									'title' => __('Social Buttons 2', ESSB3_TEXT_DOMAIN),
									'type' => 'menu_item'
								
								),
								array(
									'field_id' => 'social-2-1',
									'title' => __('Social Buttons 2 1', ESSB3_TEXT_DOMAIN),
									'type' => 'sub_menu_item'
								
								),
								array(
									'field_id' => 'social-2-2',
									'title' => __('Social Buttons 2 2', ESSB3_TEXT_DOMAIN),
									'type' => 'sub_menu_item'
								
								),
								array(
								'field_id' => 'social-3',
								'title' => __('Social Buttons 3', ESSB3_TEXT_DOMAIN),
								'type' => 'menu_item'
						
								),
												
								array(
								'field_id' => 'social-3',
								'title' => __('Social Buttons 3', ESSB3_TEXT_DOMAIN),
								'type' => 'menu_item'
						
								),
					)
				
				)
		
		);
		
$essb_section_options = array (
		'social' => array (
				array (
						'section_id' => 'social-1-1', 
						'fields' => array (
								array ('type' => 'heading1', 'title' => __ ( 'Section Heading 1', ESSB3_TEXT_DOMAIN ) ), 
								array ('type' => 'heading2', 'title' => __ ( 'Section Heading 2', ESSB3_TEXT_DOMAIN ) ), 
								array ('type' => 'heading3', 'title' => __ ( 'Section Heading 3', ESSB3_TEXT_DOMAIN ) ),
								array ('type' => 'switch', 'id' => 'switch_1', 'title' => __('Switch demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'recommended' => 'true'), 
								array ('type' => 'switch', 'id' => 'switch_2', 'title' => __('Switch demo2', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description2', 'on_label' => "Yes", "off_label" => "No", "default_value" => "true"),
								array ('type' => 'text', 'id' => 'switch_1', 'title' => __('Textbox demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description'),
								array ('type' => 'text-stretched', 'id' => 'switch_1', 'title' => __('Textbox stretched demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description'),
								array ('type' => 'checkbox', 'id' => 'switch_1', 'title' => __('Checkbox demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description'),
								array ('type' => 'select', 'id' => 'switch_1', 'title' => __('Select demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'values' => array('key1' => 'Value1', 'key2' => 'Value2')),
								array ('type' => 'text', 'id' => 'switch_1', 'title' => __('Textbox with icon demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'icon' => 'fa-arrow-down', 'class' => 'input60'),
								array ('type' => 'textarea', 'id' => 'switch_1', 'title' => __('Textarea demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description'),
								array ('type' => 'editor', 'id' => 'editor_1', 'title' => __('Editor demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'mode' => 'javascript'),
								array ('type' => 'color', 'id' => 'color_1', 'title' => __('Color field', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description'),
								array ('type' => 'image_checkbox', 'id' => 'imagecheckbox_1', 'title' => __('Image Checkbox Field', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', "values" => array( "checkbox_option1" => array("image" => "assets/images/button-position-01.png", "label" => "Image Label with very long text - to make wrap to widht"), "checkbox_option2" => array("image" => "assets/images/button-position-02.png"))),
								array ('type' => 'image_radio', 'id' => 'imageradio_1', 'title' => __('Image Radio Field', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', "values" => array( "checkbox_option1" => array("image" => "assets/images/button-position-01.png", "label" => "Image Label with very long text - to make wrap to widht"), "checkbox_option2" => array("image" => "assets/images/button-position-02.png"))),
								array ('type' => 'func', 'id' => 'essb_options_function1', 'title' => __('User function demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description'),
								array ('type' => 'file', 'id' => 'file_select1', 'title' => __('File select demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description'),
								array ('type' => 'simplesort', 'id' => 'simple_sort', 'title' => __('Simple Sortable demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'values' => array("value1", "value2", "value3")),
								array ('type' => 'select2', 'id' => 'select2_1', 'title' => __('Select demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'values' => array('key1' => 'Value1', 'key2' => 'Value2'), "select2_options" => array("allow_clear" => false, "multiple" => false, "placeholder" =>'')),
								array ('type' => 'select2', 'id' => 'select2_2', 'title' => __('Select demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'values' => array('key1' => 'Value1', 'key2' => 'Value2'), "select2_options" => array("allow_clear" => false, "multiple" => true, "placeholder" =>'Choose a value')),
								
						) 
						),
				array(
						'section_id' => 'social-1-2',
						'fields' => array(
								array ('type' => 'heading1', 'title' => __ ( 'Section Heading 1 1', ESSB3_TEXT_DOMAIN ) ),
								array ('type' => 'section_start', 'title' => __('Section Start', ESSB3_TEXT_DOMAIN), 'description' => 'Demo section description'),
								array ('type' => 'text', 'id' => 'switch_1', 'title' => __('Textbox with icon demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'icon' => 'fa-arrow-down', 'class' => 'input60', 'icon_position' => 'right'),
								array ('type' => 'text', 'id' => 'switch_1', 'title' => __('Textbox with icon demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'icon' => 'fa-arrow-down', 'class' => 'input80'),
								array ('type' => 'text', 'id' => 'switch_1', 'title' => __('Textbox with icon demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'icon' => 'fa-arrow-down', 'class' => 'input80'),
								array ('type' => 'section_end'),
								array ('type' => 'text', 'id' => 'switch_1', 'title' => __('Textbox with icon demo', ESSB3_TEXT_DOMAIN), 'description' => 'Demo description', 'icon' => 'fa-arrow-down', 'class' => 'input80'),
						)
						),
				array(
						'section_id' => 'social-1-3',
						'fields' => array(
								array ('type' => 'heading1', 'title' => __ ( 'Section Heading 1 2', ESSB3_TEXT_DOMAIN ) ),
						)
				),
				array(
						'section_id' => 'social-share-1',
						'fields' => array(
								array ('type' => 'heading1', 'title' => __ ( 'Section Share 1', ESSB3_TEXT_DOMAIN ) ),
						)
				)
				
 		) // end general 
);

;
*/
