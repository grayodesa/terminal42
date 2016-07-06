<?php

function essb_register_settings_metabox_visual() {
	global $post;
	
	if (isset ( $_GET ['action'] )) {
	
		$custom = get_post_custom ( $post->ID );
		//$essb_post_share_message = isset ( $custom ["essb_post_share_message"] ) ? $custom ["essb_post_share_message"] [0] : "";
		
		$essb_post_button_style = isset ( $custom ["essb_post_button_style"] ) ? $custom ["essb_post_button_style"] [0] : "";
		$essb_post_template = isset ( $custom ["essb_post_template"] ) ? $custom ["essb_post_template"] [0] : "";
		$essb_post_counters = isset ( $custom ["essb_post_counters"] ) ? $custom ["essb_post_counters"] [0] : "";
		$essb_post_counter_pos = isset ( $custom ["essb_post_counter_pos"] ) ? $custom ["essb_post_counter_pos"] [0] : "";
		$essb_post_total_counter_pos = isset ( $custom ["essb_post_total_counter_pos"] ) ? $custom ["essb_post_total_counter_pos"] [0] : "";
		$essb_post_customizer = isset ( $custom ["essb_post_customizer"] ) ? $custom ["essb_post_customizer"] [0] : "";
		$essb_post_animations = isset ( $custom ["essb_post_animations"] ) ? $custom ["essb_post_animations"] [0] : "";
		$essb_post_optionsbp = isset ( $custom ["essb_post_optionsbp"] ) ? $custom ["essb_post_optionsbp"] [0] : "";
		$essb_post_content_position = isset ( $custom ["essb_post_content_position"] ) ? $custom ["essb_post_content_position"] [0] : "";
		
		foreach (essb_available_button_positions() as $position => $name) {
			$essb_post_button_position_{$position} = isset ( $custom ["essb_post_button_position_".$position] ) ? $custom ["essb_post_button_position_".$position] [0] : "";
		}
		
		$essb_post_native = isset ( $custom ["essb_post_native"] ) ? $custom ["essb_post_native"] [0] : "";
		$essb_post_native_skin = isset ( $custom ["essb_post_native_skin"] ) ? $custom ["essb_post_native_skin"] [0] : "";
		
		ESSBMetaboxInterface::draw_form_start ( 'essb_social_share_visual' );
		$sidebar_options = array();
		
		$sidebar_options[] = array(
				'field_id' => 'visual1',
				'title' => __('Button Style', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
		
		$sidebar_options[] = array(
				'field_id' => 'visual2',
				'title' => __('Button Display', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
		
		$sidebar_options[] = array(
				'field_id' => 'visual3',
				'title' => __('Native Buttons', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
		
		$converted_button_styles = essb_avaiable_button_style();
		$converted_button_styles[""] = "Default style from settings";
		
		$converted_counter_pos = essb_avaliable_counter_positions();
		$converted_counter_pos[""] = "Default value from settings";

		$converted_total_counter_pos = essb_avaiable_total_counter_position();
		$converted_total_counter_pos[""] = "Default value from settings";
		
		$converted_content_position = array();//$essb_avaliable_content_positions;
		$converted_content_position[""] = "Default value from settings";
		$converted_content_position["no"] = "No display inside content (deactivate content positions)";
		foreach (essb_avaliable_content_positions() as $position => $data) {
			$converted_content_position[$position] = $data["label"];
		}
		
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
		
		$yesno_object = array();
		$yesno_object[""] = "Default value from settings";
		$yesno_object["yes"] = "Yes";
		$yesno_object["no"] = "No";
		//$converted_button_styles = array_unshift($converted_button_styles, array("" => "Default value from settings"));
		
		ESSBMetaboxInterface::draw_first_menu_activate('visual');
		ESSBMetaboxInterface::draw_sidebar($sidebar_options, 'visual');
		ESSBMetaboxInterface::draw_content_start('300', 'visual');		
		
		ESSBMetaboxInterface::draw_content_section_start('visual1');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Button Style', ESSB3_TEXT_DOMAIN), '3');
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Button style', ESSB3_TEXT_DOMAIN), __('Change default button style.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_button_style', $converted_button_styles, false, 'essb_metabox', $essb_post_button_style);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Template', ESSB3_TEXT_DOMAIN), __('Change default template.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_template', essb_available_tempaltes(), false, 'essb_metabox', $essb_post_template);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Counters', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_counters', $yesno_object, false, 'essb_metabox', $essb_post_counters);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Counter position', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_counter_pos', $converted_counter_pos, false, 'essb_metabox', $essb_post_counter_pos);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Total counter position', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_total_counter_pos', $converted_total_counter_pos, false, 'essb_metabox', $essb_post_total_counter_pos);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Activate style customizer', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_customizer', $yesno_object, false, 'essb_metabox', $essb_post_customizer);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Activate animations', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_animations', $animations_container, false, 'essb_metabox', $essb_post_animations);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Activate options by button position', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_optionsbp', $yesno_object, false, 'essb_metabox', $essb_post_optionsbp);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxInterface::draw_content_section_end();
		
		ESSBMetaboxInterface::draw_content_section_start('visual2');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Button Position', ESSB3_TEXT_DOMAIN), '3');
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Content position', ESSB3_TEXT_DOMAIN), __('Change default content position', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_content_position', $converted_content_position, false, 'essb_metabox', $essb_post_content_position);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		foreach (essb_available_button_positions() as $position => $name) {
			ESSBMetaboxOptionsFramework::draw_options_row_start(__('Activate '.$name["label"], ESSB3_TEXT_DOMAIN), __('Activate additional display position', ESSB3_TEXT_DOMAIN));
			ESSBMetaboxOptionsFramework::draw_select_field('essb_post_button_position_'.$position, $yesno_object, false, 'essb_metabox', $essb_post_button_position_{$position});
			ESSBMetaboxOptionsFramework::draw_options_row_end();			
		}
		
		ESSBMetaboxInterface::draw_content_section_end();

		ESSBMetaboxInterface::draw_content_section_start('visual3');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Native Buttons', ESSB3_TEXT_DOMAIN), '3');
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Activate native buttons', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_native', $yesno_object, false, 'essb_metabox', $essb_post_native);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Activate native buttons skin', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_select_field('essb_post_native_skin', $yesno_object, false, 'essb_metabox', $essb_post_native_skin);
		ESSBMetaboxOptionsFramework::draw_options_row_end();		
		
		ESSBMetaboxInterface::draw_content_section_end();
		
		ESSBMetaboxInterface::draw_content_end();
		ESSBMetaboxInterface::draw_form_end ();
		
	}
}

function essb_register_settings_metabox_onoff() {
	global $post, $essb_options;
	
	if (isset ( $_GET ['action'] )) {

		$custom = get_post_custom ( $post->ID );
		$essb_off = isset ( $custom ["essb_off"] ) ?  $custom ["essb_off"] [0]: "false";
		$essb_pc_twitter = isset ( $custom ["essb_pc_twitter"] ) ?  $custom ["essb_pc_twitter"] [0]: "";
		
		$twitter_counters = ESSBOptionValuesHelper::options_value($essb_options, 'twitter_counters');
		
		ESSBMetaboxInterface::draw_form_start ( 'essb_global_metabox' );
		
		ESSBMetaboxOptionsFramework::draw_section_start ();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start ( __ ( 'Turn off Easy Social Share Buttons', ESSB3_TEXT_DOMAIN ), __ ( 'Turn off automatic button display for that post/page of social share buttons', ESSB3_TEXT_DOMAIN ), '', '2', false );
		ESSBMetaboxOptionsFramework::draw_options_row_end ();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start ( '', '', '', '2', false );
		ESSBMetaboxOptionsFramework::draw_switch_field ( 'essb_off', 'essb_metabox', $essb_off, __ ( 'Yes', ESSB3_TEXT_DOMAIN ), __ ( 'No', ESSB3_TEXT_DOMAIN ) );
		ESSBMetaboxOptionsFramework::draw_options_row_end ();

		if ($twitter_counters == "self") {
			ESSBMetaboxOptionsFramework::draw_options_row_start ( __ ( 'Twitter Internal Share Counter', ESSB3_TEXT_DOMAIN ), __ ( 'Customize value of Twitter internal share counter', ESSB3_TEXT_DOMAIN ), '', '2', false );
			ESSBMetaboxOptionsFramework::draw_options_row_end ();
			
			ESSBMetaboxOptionsFramework::draw_options_row_start ( '', '', '', '2', false );
			ESSBMetaboxOptionsFramework::draw_input_field('essb_pc_twitter', true, 'essb_metabox', $essb_pc_twitter);
			ESSBMetaboxOptionsFramework::draw_options_row_end ();
		}
		
		ESSBMetaboxOptionsFramework::draw_section_end ();
		
		ESSBMetaboxInterface::draw_form_end ();
	}
}

function essb_register_settings_metabox_customization() {
	global $post;
	$essb_post_share_message = "";
	$essb_post_share_url = "";
	$essb_post_share_image = "";
	$essb_post_share_text = "";
	$essb_post_fb_url = "";
	$essb_post_plusone_url = "";
	
	$essb_post_og_desc = "";
	$essb_post_og_title = "";
	$essb_post_og_image = "";
	
	$essb_post_twitter_desc = "";
	$essb_post_twitter_title = "";
	$essb_post_twitter_image = "";
	
	$essb_post_google_desc = "";
	$essb_post_google_title = "";
	$essb_post_google_image = "";
	
	$essb_post_twitter_hashtags = "";
	$essb_post_twitter_username = "";
	$essb_post_twitter_tweet = "";
	$essb_post_og_video = "";
	$essb_post_og_video_w = "";
	$essb_post_og_video_h = "";
	$essb_activate_sharerecovery = "";
	
	$essb_post_pin_image = "";
	
	$post_address = "";
	
	if (isset ( $_GET ['action'] )) {
	
		$custom = get_post_custom ( $post->ID );
	
		$post_address = get_permalink ( $post->ID );
	
		$essb_post_share_message = isset ( $custom ["essb_post_share_message"] ) ? $custom ["essb_post_share_message"] [0] : "";
		$essb_post_share_url = isset ( $custom ["essb_post_share_url"] ) ? $custom ["essb_post_share_url"] [0] : "";
		$essb_post_share_image = isset ( $custom ["essb_post_share_image"] ) ? $custom ["essb_post_share_image"] [0] : "";
		$essb_post_share_text = isset ( $custom ["essb_post_share_text"] ) ? $custom ["essb_post_share_text"] [0] : "";
		$essb_post_fb_url = isset ( $custom ["essb_post_fb_url"] ) ? $custom ["essb_post_fb_url"] [0] : "";
		$essb_post_plusone_url = isset ( $custom ["essb_post_plusone_url"] ) ? $custom ["essb_post_plusone_url"] [0] : "";
	
		$essb_post_share_message = stripslashes ( $essb_post_share_message );
		$essb_post_share_text = stripslashes ( $essb_post_share_text );
	
	
		$essb_post_twitter_hashtags = isset ( $custom ['essb_post_twitter_hashtags'] ) ? $custom ['essb_post_twitter_hashtags'] [0] : "";
		$essb_post_twitter_username = isset ( $custom ['essb_post_twitter_username'] ) ? $custom ['essb_post_twitter_username'] [0] : "";
		$essb_post_twitter_tweet = isset ( $custom ['essb_post_twitter_tweet'] ) ? $custom ['essb_post_twitter_tweet'] [0] : "";
		$essb_activate_ga_campaign_tracking = isset($custom['essb_activate_ga_campaign_tracking']) ? $custom['essb_activate_ga_campaign_tracking'][0] : "";

		$essb_post_pin_image = isset ( $custom ["essb_post_pin_image"] ) ? $custom ["essb_post_pin_image"] [0] : "";
				
		$essb_activate_sharerecovery = isset($custom['essb_activate_sharerecovery']) ? $custom['essb_activate_sharerecovery'][0] : '';
		
		ESSBMetaboxInterface::draw_form_start ( 'essb_social_share_customization' );
		$sidebar_options = array();
	
		$sidebar_options[] = array(
				'field_id' => 'twittertag',
				'title' => __('Customize Tweet Message', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
	
		$sidebar_options[] = array(
				'field_id' => 'pinterest',
				'title' => __('Customize Pinterest Image', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
		
		$sidebar_options[] = array(
				'field_id' => 'share',
				'title' => __('Customize Share Message (Advanced)', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
	
		$sidebar_options[] = array(
				'field_id' => 'native',
				'title' => __('Customize Facebook & Google+ Native Button Addresses', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
	
		$sidebar_options[] = array(
				'field_id' => 'ga',
				'title' => __('Customize Google Analytics Campaign Tracking Options', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
	
		if (defined('ESSB3_SHARED_COUNTER_RECOVERY')) {
			$sidebar_options[] = array(
					'field_id' => 'sharerecover',
					'title' => __('Share Recovery', ESSB3_TEXT_DOMAIN),
					'icon' => 'default',
					'type' => 'menu_item',
					'action' => 'default',
					'default_child' => ''
			);
		}
		
		ESSBMetaboxInterface::draw_first_menu_activate('sharecustom');
		ESSBMetaboxInterface::draw_sidebar($sidebar_options, 'sharecustom');
		ESSBMetaboxInterface::draw_content_start('300', 'sharecustom');
	
	
	
	
		ESSBMetaboxInterface::draw_content_section_start('twittertag');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Customize Tweet Message', ESSB3_TEXT_DOMAIN), '3');
	
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Hashtags', ESSB3_TEXT_DOMAIN), __('Provide custom hashtags for that post if you wish to personalize the global set.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_twitter_hashtags', true, 'essb_metabox', $essb_post_twitter_hashtags);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
	
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Username', ESSB3_TEXT_DOMAIN), __('Provide custom username for that post if you wish to personalize the global set.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_twitter_username', true, 'essb_metabox', $essb_post_twitter_username);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
	
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Customize Tweet', ESSB3_TEXT_DOMAIN), __('Default Tweet message for every post is post title. Provide a custom tweet in that field if you wish to change it.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_twitter_tweet', true, 'essb_metabox', $essb_post_twitter_tweet);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		ESSBMetaboxInterface::draw_content_section_end();
	
		ESSBMetaboxInterface::draw_content_section_start('pinterest');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Customize Pinned Image', ESSB3_TEXT_DOMAIN), __('Provide custom Pin image when Pinterest Sniff for Images function is disabled', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_fileselect_field('essb_post_pin_image', 'essb_metabox', $essb_post_pin_image);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		ESSBMetaboxInterface::draw_content_section_end();
		
		ESSBMetaboxInterface::draw_content_section_start('share');
		
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Customize Share Message (Advanced)', ESSB3_TEXT_DOMAIN), '3');

		ESSBMetaboxOptionsFramework::draw_options_row_start(__('URL', ESSB3_TEXT_DOMAIN), __('Provide custom URL to be shared.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_share_url', true, 'essb_metabox', $essb_post_share_url);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Message', ESSB3_TEXT_DOMAIN), __('Provide custom message to be shared (not all social networks support that option)', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_share_message', true, 'essb_metabox', $essb_post_share_message);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Image', ESSB3_TEXT_DOMAIN), __('Custom image is support by Facebook when advanced sharing is enabled and Pinterest when sniff for images is disabled', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_fileselect_field('essb_post_share_image', 'essb_metabox', $essb_post_share_image);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Description', ESSB3_TEXT_DOMAIN), __('Custom description is support by Facebook when advanced sharing is enabled and Pinterest when sniff for images is disabled', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_textarea_field('essb_post_share_text', 'essb_metabox', $essb_post_share_text);
		ESSBMetaboxOptionsFramework::draw_options_row_end();		
		ESSBMetaboxInterface::draw_content_section_end();
			
		ESSBMetaboxInterface::draw_content_section_start('native');
		
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Customize Facebook & Google+ Native Button Addresses', ESSB3_TEXT_DOMAIN), '3');
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Custom address of Facebook Like button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_fb_url', true, 'essb_metabox', $essb_post_fb_url);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Custom address of Google +1 button', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_plusone_url', true, 'essb_metabox', $essb_post_plusone_url);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxInterface::draw_content_section_end();

		
		ESSBMetaboxInterface::draw_content_section_start('ga');
		
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Customize Google Analytics Campaign Tracking Options', ESSB3_TEXT_DOMAIN), '3');
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Add Custom Google Analytics Campaign parameters to your URLs', ESSB3_TEXT_DOMAIN), __('Paste your custom campaign parameters in this field and they will be automatically added to shared addresses on social networks. Please note as social networks count shares via URL as unique key this option is not compatible with active social share counters as it will make the start from zero.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_activate_ga_campaign_tracking', true, 'essb_metabox', $essb_activate_ga_campaign_tracking);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('', ESSB3_TEXT_DOMAIN), __('', ESSB3_TEXT_DOMAIN), '', '2', false);
		print "<span style='font-weight: 400;'>You can visit <a href='https://support.google.com/analytics/answer/1033867?hl=en' target='_blank'>this page</a> for more information on how to use and generate these parameters.
To include the social network into parameters use the following code <b>{network}</b>. When that code is reached it will be replaced with the network name (example: facebook). An example campaign trakcing code include network will look like this utm_source=essb_settings&utm_medium=needhelp&utm_campaign={network} - in this configuration when you press Facebook button {network} will be replaced with facebook, if you press Twitter button it will be replaced with twitter.</span>";
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxInterface::draw_content_section_end();
			
		if (defined('ESSB3_SHARED_COUNTER_RECOVERY')) {
			ESSBMetaboxInterface::draw_content_section_start('sharerecover');
			
			ESSBMetaboxOptionsFramework::reset_row_status();
			ESSBMetaboxOptionsFramework::draw_heading(__('Share Counter Recovery', ESSB3_TEXT_DOMAIN), '3');
			
			ESSBMetaboxOptionsFramework::draw_options_row_start(__('Previous post url address', ESSB3_TEXT_DOMAIN), __('Provide custom previous url address of post if the automatic share counter recovery is not possible to guess the previous post address.', ESSB3_TEXT_DOMAIN));
			ESSBMetaboxOptionsFramework::draw_input_field('essb_activate_sharerecovery', true, 'essb_metabox', $essb_activate_sharerecovery);
			ESSBMetaboxOptionsFramework::draw_options_row_end();
			
			ESSBMetaboxInterface::draw_content_section_end();
		}
		ESSBMetaboxInterface::draw_content_end();
		
		ESSBMetaboxInterface::draw_form_end ();
	}
}

function essb_register_settings_metabox_optimization() {
	global $post;
	
	$essb_post_og_desc = "";
	$essb_post_og_title = "";
	$essb_post_og_image = "";
	
	$essb_post_twitter_desc = "";
	$essb_post_twitter_title = "";
	$essb_post_twitter_image = "";
	
	$essb_post_google_desc = "";
	$essb_post_google_title = "";
	$essb_post_google_image = "";
	
	$essb_post_twitter_hashtags = "";
	$essb_post_twitter_username = "";
	$essb_post_twitter_tweet = "";
	$essb_post_og_video = "";
	$essb_post_og_video_w = "";
	$essb_post_og_video_h = "";
	$essb_post_og_author = "";
	
	$post_address = "";
	
	if (isset ( $_GET ['action'] )) {
	
		$custom = get_post_custom ( $post->ID );
		
		$post_address = get_permalink ( $post->ID );		
		
		$essb_post_og_desc = isset ( $custom ["essb_post_og_desc"] ) ? $custom ["essb_post_og_desc"] [0] : "";
		$essb_post_og_title = isset ( $custom ["essb_post_og_title"] ) ? $custom ["essb_post_og_title"] [0] : "";
		$essb_post_og_image = isset ( $custom ["essb_post_og_image"] ) ? $custom ["essb_post_og_image"] [0] : "";
		$essb_post_og_desc = stripslashes ( $essb_post_og_desc );
		$essb_post_og_title = stripslashes ( $essb_post_og_title );
		$essb_post_og_video = isset ( $custom ["essb_post_og_video"] ) ? $custom ["essb_post_og_video"] [0] : "";
		$essb_post_og_video_w = isset ( $custom ["essb_post_og_video_w"] ) ? $custom ["essb_post_og_video_w"] [0] : "";
		$essb_post_og_video_h = isset ( $custom ["essb_post_og_video_h"] ) ? $custom ["essb_post_og_video_h"] [0] : "";
		
		$essb_post_twitter_desc = isset ( $custom ["essb_post_twitter_desc"] ) ? $custom ["essb_post_twitter_desc"] [0] : "";
		$essb_post_twitter_title = isset ( $custom ["essb_post_twitter_title"] ) ? $custom ["essb_post_twitter_title"] [0] : "";
		$essb_post_twitter_image = isset ( $custom ["essb_post_twitter_image"] ) ? $custom ["essb_post_twitter_image"] [0] : "";
		$essb_post_twitter_desc = stripslashes ( $essb_post_twitter_desc );
		$essb_post_twitter_title = stripslashes ( $essb_post_twitter_title );
		
		$essb_post_google_desc = isset ( $custom ["essb_post_google_desc"] ) ? $custom ["essb_post_google_desc"] [0] : "";
		$essb_post_google_title = isset ( $custom ["essb_post_google_title"] ) ? $custom ["essb_post_google_title"] [0] : "";
		$essb_post_google_image = isset ( $custom ["essb_post_google_image"] ) ? $custom ["essb_post_google_image"] [0] : "";
		$essb_post_google_desc = stripslashes ( $essb_post_google_desc );
		$essb_post_google_title = stripslashes ( $essb_post_google_title );
		
		$essb_post_og_author = isset($custom['essb_post_og_author']) ? $custom['essb_post_og_author'][0] : '';
		$essb_post_og_author = stripslashes($essb_post_og_author);
		
		ESSBMetaboxInterface::draw_form_start ( 'essb_social_share_optimization' );
		$sidebar_options = array();
		$sidebar_options[] = array(
				'field_id' => 'opengraph',
				'title' => __('Facebook Sharing Tags', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
				);
		$sidebar_options[] = array(
				'field_id' => 'twittercard',
				'title' => __('Twitter Card Tags', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
		
		$sidebar_options[] = array(
				'field_id' => 'googletag',
				'title' => __('Google+ Tags', ESSB3_TEXT_DOMAIN),
				'icon' => 'default',
				'type' => 'menu_item',
				'action' => 'default',
				'default_child' => ''
		);
	
		ESSBMetaboxInterface::draw_first_menu_activate('sso');
		
		ESSBMetaboxInterface::draw_sidebar($sidebar_options, 'sso');
		ESSBMetaboxInterface::draw_content_start('300', 'sso');
		
		
		ESSBMetaboxInterface::draw_content_section_start('opengraph');
		
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Facebook Sharing Tags', ESSB3_TEXT_DOMAIN), '3');
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Title', ESSB3_TEXT_DOMAIN), __('Add a custom title for your post. This will be used to post on an user\'s wall when they like/share your post.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_og_title', true, 'essb_metabox', $essb_post_og_title);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Image', ESSB3_TEXT_DOMAIN), __('If an image is provided it will be used in share data', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_fileselect_field('essb_post_og_image', 'essb_metabox', $essb_post_og_image);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Description', ESSB3_TEXT_DOMAIN), __('Add a custom description for your post. This will be used to post on an user\'s wall when they like/share your post.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_textarea_field('essb_post_og_desc', 'essb_metabox', $essb_post_og_desc);
		ESSBMetaboxOptionsFramework::draw_options_row_end();

		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Article Author', ESSB3_TEXT_DOMAIN), __('Add link to Facebook profile page of article author if you wish it to appear in shared information.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_og_author_of_post', true, 'essb_metabox', $essb_post_og_author);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Video URL', ESSB3_TEXT_DOMAIN), __('Please use the FULL URL to the video (e.g. http://www.yourdomain.com/videos/video.mp4).', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_og_video', true, 'essb_metabox', $essb_post_og_video);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Video Width', ESSB3_TEXT_DOMAIN), __('Enter the width of your video. (Example: 320).', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_og_video_w', false, 'essb_metabox', $essb_post_og_video_w);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Video Height', ESSB3_TEXT_DOMAIN), __('Enter the height of your video. (Example: 320).', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_og_video_h', false, 'essb_metabox', $essb_post_og_video_h);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		
		ESSBMetaboxInterface::draw_content_section_end();
		
		ESSBMetaboxInterface::draw_content_section_start('googletag');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Google+ Tags', ESSB3_TEXT_DOMAIN), '3');		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Title', ESSB3_TEXT_DOMAIN), __('Add a custom title for your post. This will be used to post on an user\'s wall when they like/share your post.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_google_title', true, 'essb_metabox', $essb_post_google_title);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Image', ESSB3_TEXT_DOMAIN), __('If an image is provided it will be used in share data', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_fileselect_field('essb_post_google_image', 'essb_metabox', $essb_post_google_image);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Description', ESSB3_TEXT_DOMAIN), __('Add a custom description for your post. This will be used to post on an user\'s wall when they like/share your post.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_textarea_field('essb_post_google_desc', 'essb_metabox', $essb_post_google_desc);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		ESSBMetaboxInterface::draw_content_section_end();
		
		
		
		ESSBMetaboxInterface::draw_content_section_start('twittercard');
		ESSBMetaboxOptionsFramework::reset_row_status();
		ESSBMetaboxOptionsFramework::draw_heading(__('Twitter Card Tags', ESSB3_TEXT_DOMAIN), '3');
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Title', ESSB3_TEXT_DOMAIN), __('Add a custom title for your post. This will be used to post on an user\'s wall when they like/share your post.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_input_field('essb_post_twitter_title', true, 'essb_metabox', $essb_post_twitter_title);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Image', ESSB3_TEXT_DOMAIN), __('If an image is provided it will be used in share data', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_fileselect_field('essb_post_twitter_image', 'essb_metabox', $essb_post_twitter_image);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		
		ESSBMetaboxOptionsFramework::draw_options_row_start(__('Description', ESSB3_TEXT_DOMAIN), __('Add a custom description for your post. This will be used to post on an user\'s wall when they like/share your post.', ESSB3_TEXT_DOMAIN));
		ESSBMetaboxOptionsFramework::draw_textarea_field('essb_post_twitter_desc', 'essb_metabox', $essb_post_twitter_desc);
		ESSBMetaboxOptionsFramework::draw_options_row_end();
		ESSBMetaboxInterface::draw_content_section_end();
		
		ESSBMetaboxInterface::draw_content_end();
		
		
		ESSBMetaboxInterface::draw_form_end ();
	}
}

function essb_register_settings_metabox_stats() {
	global $post, $essb_networks;
	
	if (isset ( $_GET ['action'] )) {
	
		$post_id = $post->ID;
		ESSBSocialShareAnalyticsBackEnd::init_addional_settings();
		
		// overall stats by social network
		$overall_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks ('', $post_id);
		$position_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_position('', $post_id);
		
		// print_r($overall_stats);
		
		$calculated_total = 0;
		$networks_with_data = array ();
		
		if (isset ( $overall_stats )) {
			$cnt = 0;
			foreach ( $essb_networks as $k => $v ) {
		
				$calculated_total += intval ( $overall_stats->{$k} );
				if (intval ( $overall_stats->{$k} ) != 0) {
					$networks_with_data [$k] = $k;
				}
			}
		}
		
		$device_stats = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_device ('', $post_id);
		
		$essb_date_to = "";
		$essb_date_from = "";
		
		if ($essb_date_to == '') {
			$essb_date_to = date ( "Y-m-d" );
		}
		
		if ($essb_date_from == '') {
			$essb_date_from = date ( "Y-m-d", strtotime ( date ( "Y-m-d", strtotime ( date ( "Y-m-d" ) ) ) . "-1 month" ) );
		}
		
		$sqlMonthsData = ESSBSocialShareAnalyticsBackEnd::essb_stats_by_networks_by_date_for_post($essb_date_from, $essb_date_to, $post_id);
		
		
		?>
		<div class="essb-dashboard essb-metabox-dashboard">
		<!--  dashboard type2  -->
	<div class="essb-dashboard-panel">
		<div class="essb-dashboard-panel-title">
			<h4>Total clicks on social buttons since statistics is activated</h4>
		</div>
		<div class="essb-dashboard-panel-content">

			<div class="row">
				<div class="oneforth">
					<div class="essb-stats-panel shadow panel100 total">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">Total clicks on share buttons</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($calculated_total); ?>
						</div>
						</div>
						
				
				
				<?php
				
				if (isset ( $device_stats )) {
					$desktop = $device_stats->desktop;
					$mobile = $device_stats->mobile;
					
					if ($calculated_total > 0) {
						$percentd = $desktop * 100 / $calculated_total;
					}
					else {
						$percentd = 0;
					}
					$print_percentd = round ( $percentd, 2 );
					$percentd = round ( $percentd );
					
					if ($percentd > 90) {
						$percentd -= 2;
					}
					
					if ($calculated_total > 0) {
						$percentm = $mobile * 100 / $calculated_total;
					}
					else {
						$percentm = 0;
					}
					$print_percentm = round ( $percentm, 2 );
					$percentm = round ( $percentm );
					if ($percentm > 90) {
						$percentm -= 2;
					}
				}
				
				?>
				</div>
					<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">
								Desktop <span class="percent"><?php echo $print_percentd;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($desktop); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-mwp" style="width: <?php echo $percentd;?>%;"></div>

						</div>
					</div>

					<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text">
								Mobile <span class="percent"><?php echo $print_percentm;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($mobile); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-mwp" style="width: <?php echo $percentm;?>%;"></div>

						</div>
					</div>
					<h5>Stats by position</h5>
					<!-- begin stats by displayed position -->
<?php

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( ESSBSocialShareAnalyticsBackEnd::$positions as $k ) {
		
		$key = "position_".$k;
		
		$single = intval ( $position_stats->{$key} );
		
		if ($single > 0) {
			if ($calculated_total != 0) {
				$percent = $single * 100 / $calculated_total;
			}
			else {
				$percent = 0;
			}
			$print_percent = round ( $percent, 2 );
			$percent = round ( $percent );
			?>
			
			<div class="essb-stats-panel shadow panel50">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text"><?php echo $k; ?> <span
									class="percent"><?php echo $print_percent;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-ok" style="width: <?php echo $percent;?>%;"></div>

						</div>
					</div>
									
									<?php
		}
	}
}

?>					
				</div>



				<div class="threeforth">



					
<?php

if (isset ( $overall_stats )) {
	$cnt = 0;
	foreach ( $essb_networks as $k => $v ) {
		
		$single = intval ( $overall_stats->{$k} );
		
		if ($single > 0) {
			$percent = $single * 100 / $calculated_total;
			$print_percent = round ( $percent, 2 );
			$percent = round ( $percent );
			?>
			
			<div class="essb-stats-panel shadow panel20">
						<div class="essb-stats-panel-inner">
							<div class="essb-stats-panel-text"><?php echo $v["name"]; ?> <span
									class="percent"><?php echo $print_percent;?> %</span>
							</div>
							<div class="essb-stats-panel-value"><?php echo ESSBSocialShareAnalyticsBackEnd::prettyPrintNumber($single); ?>
						</div>
						</div>
						<div class="essb-stats-panel-graph">

							<div class="graph widget-color-<?php echo $k; ?>" style="width: <?php echo $percent;?>%;"></div>

						</div>
					</div>
									
									<?php
		}
	}
}

?>
				</div>

			</div>



		</div>
	</div>
	<div class="clear"></div>
	<!--  end dashboard 2 -->
		<div class="essb-dashboard-panel">
		<div class="essb-dashboard-panel-title">
			<h4>Social activity for the last 30 days</h4>

		</div>
		<div class="essb-dashboard-panel-content">
			<?php ESSBSocialShareAnalyticsBackEnd::essb_stat_admin_detail_by_month ($sqlMonthsData, $networks_with_data, '', 'Date'); ?>
			</div>
	</div>

	<div class="clear"></div>
		</div>
		<?php 
	}
}

?>