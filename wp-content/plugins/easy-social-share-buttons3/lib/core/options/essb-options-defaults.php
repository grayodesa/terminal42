<?php

global $essb_avaiable_button_style;
global $essb_available_button_positions, $essb_avaliable_content_positions;

global $essb_available_button_positions_mobile, $essb_avaliable_content_positions_mobile, $essb_avaiable_total_counter_position_mobile, 
	 $essb_available_social_profiles;

if (!function_exists('essb_default_native_buttons')) {
	function essb_default_native_buttons() {
		$essb_default_native_buttons = array();
		$essb_default_native_buttons[] = 'google';
		$essb_default_native_buttons[] = 'twitter';
		$essb_default_native_buttons[] = 'facebook';
		$essb_default_native_buttons[] = 'linkedin';
		$essb_default_native_buttons[] = 'pinterest';
		$essb_default_native_buttons[] = 'youtube';
		$essb_default_native_buttons[] = 'managewp';
		$essb_default_native_buttons[] = 'vk';
		
		return $essb_default_native_buttons;
	}
}


if (!function_exists('essb_available_tempaltes')) {
	function essb_available_tempaltes() {
		$essb_available_tempaltes = array ();
		$essb_available_tempaltes [''] = "Default template from settings";
		$essb_available_tempaltes ['default'] = "Default";
		$essb_available_tempaltes ['metro'] = "Metro";
		$essb_available_tempaltes ['modern'] = "Modern";
		$essb_available_tempaltes ['round'] = "Round";
		$essb_available_tempaltes ['big'] = "Big";
		$essb_available_tempaltes ['metro-retina'] = "Metro (Retina)";
		$essb_available_tempaltes ['big-retina'] = "Big (Retina)";
		$essb_available_tempaltes ['light-retina'] = "Light (Retina)";
		$essb_available_tempaltes ['flat-retina'] = "Flat (Retina)";
		$essb_available_tempaltes ['tiny-retina'] = "Tiny (Retina)";
		$essb_available_tempaltes ['round-retina'] = "Round (Retina)";
		$essb_available_tempaltes ['modern-retina'] = "Modern (Retina)";
		$essb_available_tempaltes ['circles-retina'] = "Circles (Retina)";
		$essb_available_tempaltes ['blocks-retina'] = "Blocks (Retina)";
		$essb_available_tempaltes ['dark-retina'] = "Dark (Retina)";
		$essb_available_tempaltes ['grey-circles-retina'] = "Grey Circles (Retina)";
		$essb_available_tempaltes ['grey-blocks-retina'] = "Grey Blocks (Retina)";
		$essb_available_tempaltes ['clear-retina'] = "Clear (Retina)";
		$essb_available_tempaltes ['dimmed-retina'] = "Dimmed (Retina)";
		$essb_available_tempaltes ['grey-retina'] = "Grey (Retina)";
		$essb_available_tempaltes ['default-retina'] = "Default 3.0 (Retina)";
		$essb_available_tempaltes ['jumbo-retina'] = "Jumbo (Retina)";
		$essb_available_tempaltes ['jumbo-round-retina'] = "Jumbo Rounded (Retina)";
		$essb_available_tempaltes ['fancy-retina'] = "Fancy (Retina)";
		$essb_available_tempaltes ['deluxe-retina'] = "Deluxe (Retina)";
		$essb_available_tempaltes ['modern-slim-retina'] = "Modern Slim (Retina)";
		$essb_available_tempaltes ['bold-retina'] = "Bold (Retina)";
		$essb_available_tempaltes ['fancy-bold-retina'] = "Fancy Bold (Retina)";
		$essb_available_tempaltes ['retro-retina'] = "Retro (Retina)";
		$essb_available_tempaltes ['metro-bold-retina'] = "Metro Bold (Retina)";
		
		return $essb_available_tempaltes;
	}
}

if (!function_exists('essb_available_social_networks')) {
	function essb_available_social_networks() {
		$essb_available_social_networks = array (
				'facebook' => array ('name' => 'Facebook', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'twitter' => array ('name' => 'Twitter', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'google' => array ('name' => 'Google+', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'pinterest' => array ('name' => 'Pinterest', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'linkedin' => array ('name' => 'LinkedIn', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'digg' => array ('name' => 'Digg', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'del' => array ('name' => 'Del', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'stumbleupon' => array ('name' => 'StumbleUpon', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'tumblr' => array ('name' => 'Tumblr', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'vk' => array ('name' => 'VKontakte', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'print' => array ('name' => 'Print', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'mail' => array ('name' => 'Email', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'flattr' => array ('name' => 'Flattr', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'reddit' => array ('name' => 'Reddit', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'buffer' => array ('name' => 'Buffer', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'love' => array ('name' => 'Love This', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'weibo' => array ('name' => 'Weibo', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'pocket' => array ('name' => 'Pocket', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'xing' => array ('name' => 'Xing', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'ok' => array ('name' => 'Odnoklassniki', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'mwp' => array ('name' => 'ManageWP.org', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'more' => array ('name' => 'More Button', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'whatsapp' => array ('name' => 'WhatsApp', 'type' => 'buildin', 'supports' => 'mobile' ),
				'meneame' => array ('name' => 'Meneame', 'type' => 'buildin', 'supports' => 'desktop,mobile' ),
				'blogger' => array ('name' => 'Blogger', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'amazon' => array ('name' => 'Amazon', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'yahoomail' => array ('name' => 'Yahoo Mail', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'gmail' => array ('name' => 'Gmail', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'aol' => array ('name' => 'AOL', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'newsvine' => array ('name' => 'Newsvine', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'hackernews' => array ('name' => 'HackerNews', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'evernote' => array ('name' => 'Evernote', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'myspace' => array ('name' => 'MySpace', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'mailru' => array ('name' => 'Mail.ru', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'viadeo' => array ('name' => 'Viadeo', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only' ),
				'line' => array ('name' => 'Line', 'type' => 'buildin', 'supports' => 'mobile,retina templates only' ),
				/*'embedly' => array(
				 'name' => 'embed.ly',
						'type' => 'buildin'
				),*/
				'flipboard' => array ('name' => 'Flipboard', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only'),
				'comments' => array( 'name' => 'Comments', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only'),
				'yummly' => array( 'name' => 'Yummly', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only'),
				'sms' => array( 'name' => 'SMS', 'type' => 'buildin', 'supports' => 'mobile,retina templates only'),
				'viber' => array( 'name' => 'Viber', 'type' => 'buildin', 'supports' => 'mobile,retina templates only'),
				'telegram' => array( 'name' => 'Telegram', 'type' => 'buildin', 'supports' => 'mobile,retina templates only'),
				'subscribe' => array( 'name' => 'Subscribe', 'type' => 'buildin', 'supports' => 'desktop,mobile,retina templates only')
				
		);
		
		return $essb_available_social_networks;
	}
}

// Line => http://media.line.me/howto/en/

if (!function_exists('essb_avaliable_counter_positions')) {
	function essb_avaliable_counter_positions() {
		$essb_avaliable_counter_positions = array ();
		$essb_avaliable_counter_positions ['left'] = "Left";
		$essb_avaliable_counter_positions ['right'] = "Right";
		$essb_avaliable_counter_positions ['inside'] = "Inside button instead of network name";
		$essb_avaliable_counter_positions ['insidename'] = "Inside button after network name";
		$essb_avaliable_counter_positions ['insidebeforename'] = "Inside button before network name";
		$essb_avaliable_counter_positions ['insidehover'] = "Inside button and appear when you hover button over the network name";
		$essb_avaliable_counter_positions ['hidden'] = "Hidden (use this position if you wish to have only total counter)";
		$essb_avaliable_counter_positions ['leftm'] = "Left Modern";
		$essb_avaliable_counter_positions ['rightm'] = "Right Modern";
		$essb_avaliable_counter_positions ['top'] = "Top Modern";
		$essb_avaliable_counter_positions ['topm'] = "Top Mini";
		$essb_avaliable_counter_positions ['bottom'] = "Bottom";
		$essb_avaliable_counter_positions ['topn'] = "Top";
		
		return $essb_avaliable_counter_positions;
	}
}

if (!function_exists('essb_avaliable_counter_positions_point')) {
	function essb_avaliable_counter_positions_point() {
		$essb_avaliable_counter_positions = array ();
		$essb_avaliable_counter_positions ['inside'] = "Inside button instead of network name";
		$essb_avaliable_counter_positions ['insidename'] = "Inside button after network name";
		$essb_avaliable_counter_positions ['insidebeforename'] = "Inside button before network name";
		$essb_avaliable_counter_positions ['topm'] = "Top Mini";
		$essb_avaliable_counter_positions ['bottom'] = "Bottom";
		$essb_avaliable_counter_positions ['topn'] = "Top";

		return $essb_avaliable_counter_positions;
	}
}


if (!function_exists('essb_avaliable_counter_positions_mobile')) {
	function essb_avaliable_counter_positions_mobile() {
		$essb_avaliable_counter_positions_mobile = array ();
		$essb_avaliable_counter_positions_mobile ['inside'] = "Inside button instead of network name";
		$essb_avaliable_counter_positions_mobile ['insidename'] = "Inside button after network name";
		$essb_avaliable_counter_positions_mobile ['insidebeforename'] = "Inside button before network name";
		$essb_avaliable_counter_positions_mobile ['insidehover'] = "Inside button and appear when you hover button over the network name";
		$essb_avaliable_counter_positions_mobile ['hidden'] = "Hidden (use this position if you wish to have only total counter)";
		
		return $essb_avaliable_counter_positions_mobile;
	}
}


if (!function_exists('essb_avaiable_total_counter_position')) {
	function essb_avaiable_total_counter_position() {
		$essb_avaiable_total_counter_position = array ();
		$essb_avaiable_total_counter_position ['right'] = "Right";
		$essb_avaiable_total_counter_position ['left'] = "Left";
		$essb_avaiable_total_counter_position ['rightbig'] = "Right Big Number (with option for custom text)";
		$essb_avaiable_total_counter_position ['leftbig'] = "Left Big Nubmer (with option for custom text)";
		$essb_avaiable_total_counter_position ['before'] = "Before social share buttons";
		$essb_avaiable_total_counter_position ['after'] = "After social share buttons";
		$essb_avaiable_total_counter_position ['hidden'] = "This will hide the total counter and make only button counters be visible";
		
		return $essb_avaiable_total_counter_position;
	}
}


if (!function_exists('essb_avaiable_total_counter_position_mobile')) {
	function essb_avaiable_total_counter_position_mobile() {
		$essb_avaiable_total_counter_position_mobile = array ();
		$essb_avaiable_total_counter_position_mobile ['before'] = "Before social share buttons";
		$essb_avaiable_total_counter_position_mobile ['after'] = "After social share buttons";
		$essb_avaiable_total_counter_position_mobile ['hidden'] = "This will hide the total counter and make only button counters be visible";
		
		return $essb_avaiable_total_counter_position_mobile;
	}
}

if (!function_exists('essb_avaiable_button_style')) {
	function essb_avaiable_button_style() {
		$essb_avaiable_button_style = array ();
		$essb_avaiable_button_style ['button'] = 'Display as share button with icon and network name';
		$essb_avaiable_button_style ['button_name'] = 'Display as share button with network name and without icon';
		$essb_avaiable_button_style ['icon'] = 'Display share buttons only as icon without network names';
		$essb_avaiable_button_style ['icon_hover'] = 'Display share buttons as icon with network name appear when button is pointed';
		
		return $essb_avaiable_button_style;
	}
}

if (!function_exists('essb_avaiable_button_style_with_recommend')) {
	function essb_avaiable_button_style_with_recommend() {
		$essb_avaiable_button_style = array ();
		$essb_avaiable_button_style ['recommended'] = 'Recommended style for selected display method';
		$essb_avaiable_button_style ['button'] = 'Display as share button with icon and network name';
		$essb_avaiable_button_style ['button_name'] = 'Display as share button with network name and without icon';
		$essb_avaiable_button_style ['icon'] = 'Display share buttons only as icon without network names';
		$essb_avaiable_button_style ['icon_hover'] = 'Display share buttons as icon with network name appear when button is pointed';

		return $essb_avaiable_button_style;
	}
}

if (!function_exists('essb_avaliable_content_positions')) {
	function essb_avaliable_content_positions() {
		$essb_avaliable_content_positions = array ();
		$essb_avaliable_content_positions ['content_top'] = array ("image" => "assets/images/display-positions-02.png", "label" => "Content top" );
		$essb_avaliable_content_positions ['content_bottom'] = array ("image" => "assets/images/display-positions-03.png", "label" => "Content bottom" );
		$essb_avaliable_content_positions ['content_both'] = array ("image" => "assets/images/display-positions-04.png", "label" => "Content top and bottom" );
		$essb_avaliable_content_positions ['content_float'] = array ("image" => "assets/images/display-positions-05.png", "label" => "Float from content top" );
		$essb_avaliable_content_positions ['content_floatboth'] = array ("image" => "assets/images/display-positions-06.png", "label" => "Float from content top and bottom" );
		$essb_avaliable_content_positions ['content_nativeshare'] = array ("image" => "assets/images/display-positions-07.png", "label" => "Native social buttons top, share buttons bottom" );
		$essb_avaliable_content_positions ['content_sharenative'] = array ("image" => "assets/images/display-positions-08.png", "label" => "Share buttons top, native buttons bottom" );
		$essb_avaliable_content_positions ['content_manual'] = array ("image" => "assets/images/display-positions-09.png", "label" => "Manual display with shortcode only" );
		
		return $essb_avaliable_content_positions;
	}
}

if (!function_exists('essb_avaliable_content_positions_light')) {
	function essb_avaliable_content_positions_light() {
		$essb_avaliable_content_positions = array ();
		$essb_avaliable_content_positions ['content_top'] = array ("image" => "assets/images/display-positions-02.png", "label" => "Content top" );
		$essb_avaliable_content_positions ['content_bottom'] = array ("image" => "assets/images/display-positions-03.png", "label" => "Content bottom" );
		$essb_avaliable_content_positions ['content_both'] = array ("image" => "assets/images/display-positions-04.png", "label" => "Content top and bottom" );
		$essb_avaliable_content_positions ['content_float'] = array ("image" => "assets/images/display-positions-05.png", "label" => "Float from content top" );
		$essb_avaliable_content_positions ['content_floatboth'] = array ("image" => "assets/images/display-positions-06.png", "label" => "Float from content top and bottom" );
		$essb_avaliable_content_positions ['content_manual'] = array ("image" => "assets/images/display-positions-09.png", "label" => "Manual display with shortcode only" );

		return $essb_avaliable_content_positions;
	}
}

if (!function_exists('essb_avaliable_content_positions_mobile')) {
	function essb_avaliable_content_positions_mobile() {
		$essb_avaliable_content_positions_mobile = array ();
		$essb_avaliable_content_positions_mobile ['content_top'] = array ("image" => "assets/images/display-positions-02.png", "label" => "Content top" );
		$essb_avaliable_content_positions_mobile ['content_bottom'] = array ("image" => "assets/images/display-positions-03.png", "label" => "Content bottom" );
		$essb_avaliable_content_positions_mobile ['content_both'] = array ("image" => "assets/images/display-positions-04.png", "label" => "Content top and bottom" );
		$essb_avaliable_content_positions_mobile ['content_float'] = array ("image" => "assets/images/display-positions-05.png", "label" => "Float from content top" );
		$essb_avaliable_content_positions_mobile ['content_manual'] = array ("image" => "assets/images/display-positions-09.png", "label" => "Manual display with shortcode only" );
		
		return $essb_avaliable_content_positions_mobile;
	}
}

if (!function_exists('essb_available_button_positions')) {
	function essb_available_button_positions() {
		$essb_available_button_positions = array ();
		$essb_available_button_positions ['sidebar'] = array ("image" => "assets/images/display-positions-10.png", "label" => "Sidebar" );
		$essb_available_button_positions ['popup'] = array ("image" => "assets/images/display-positions-11.png", "label" => "Pop up" );
		$essb_available_button_positions ['flyin'] = array ("image" => "assets/images/display-positions-12.png", "label" => "Fly in" );
		$essb_available_button_positions ['postfloat'] = array ("image" => "assets/images/display-positions-13.png", "label" => "Post vertical float" );
		$essb_available_button_positions ['topbar'] = array ("image" => "assets/images/display-positions-14.png", "label" => "Top bar" );
		$essb_available_button_positions ['bottombar'] = array ("image" => "assets/images/display-positions-15.png", "label" => "Bottom bar" );
		$essb_available_button_positions ['onmedia'] = array ("image" => "assets/images/display-positions-16.png", "label" => "On media" );
		$essb_available_button_positions ['heroshare'] = array ("image" => "assets/images/display-positions-22.png", "label" => "Full screen hero share" );
		$essb_available_button_positions ['postbar'] = array ("image" => "assets/images/display-positions-23.png", "label" => "Post share bar" );
		$essb_available_button_positions ['point'] = array ("image" => "assets/images/display-positions-24.png", "label" => "Share Point (Advanced Version)" );
		
		return $essb_available_button_positions;
	}
}

if (!function_exists('essb_available_button_positions_light')) {
	function essb_available_button_positions_light() {
		$essb_available_button_positions = array ();
		$essb_available_button_positions ['sidebar'] = array ("image" => "assets/images/display-positions-10.png", "label" => "Sidebar" );
		$essb_available_button_positions ['popup'] = array ("image" => "assets/images/display-positions-11.png", "label" => "Pop up" );
		$essb_available_button_positions ['flyin'] = array ("image" => "assets/images/display-positions-12.png", "label" => "Fly in" );
		$essb_available_button_positions ['postfloat'] = array ("image" => "assets/images/display-positions-13.png", "label" => "Post vertical float" );
		$essb_available_button_positions ['topbar'] = array ("image" => "assets/images/display-positions-14.png", "label" => "Top bar" );
		$essb_available_button_positions ['bottombar'] = array ("image" => "assets/images/display-positions-15.png", "label" => "Bottom bar" );
		$essb_available_button_positions ['onmedia'] = array ("image" => "assets/images/display-positions-16.png", "label" => "On media" );
		
		return $essb_available_button_positions;
	}
}

if (!function_exists('essb_available_button_positions_mobile')) {
	function essb_available_button_positions_mobile() {
		$essb_available_button_positions_mobile = array ();
		$essb_available_button_positions_mobile ['sidebar'] = array ("image" => "assets/images/display-positions-10.png", "label" => "Sidebar" );
		$essb_available_button_positions_mobile ['topbar'] = array ("image" => "assets/images/display-positions-14.png", "label" => "Top bar" );
		$essb_available_button_positions_mobile ['bottombar'] = array ("image" => "assets/images/display-positions-15.png", "label" => "Bottom bar" );
		$essb_available_button_positions_mobile ['sharebottom'] = array ("image" => "assets/images/display-positions-17.png", "label" => "Share buttons bar (Mobile Only Display Method)" );
		$essb_available_button_positions_mobile ['sharebar'] = array ("image" => "assets/images/display-positions-18.png", "label" => "Share bar (Mobile Only Display Method)" );
		$essb_available_button_positions_mobile ['sharepoint'] = array ("image" => "assets/images/display-positions-19.png", "label" => "Share point (Mobile Only Display Method)" );
		$essb_available_button_positions_mobile ['point'] = array ("image" => "assets/images/display-positions-24.png", "label" => "Share Point (Advanced Version)" );
		
		return $essb_available_button_positions_mobile;
	}
}

if (!function_exists('essb_available_buttons_align')) {
	function essb_available_buttons_align() {
		$essb_available_buttons_align = array();
		$essb_available_buttons_align [''] = array ("image" => "assets/images/button-align-01.png", "label" => "<b>Left</b>" );
		$essb_available_buttons_align ['center'] = array ("image" => "assets/images/button-align-03.png", "label" => "<b>Center</b>" );
		$essb_available_buttons_align ['right'] = array ("image" => "assets/images/button-align-02.png", "label" => "<b>Right</b>" );
		
		return $essb_available_buttons_align;
	}
}

if (!function_exists('essb_available_buttons_style')) {
	function essb_available_buttons_style() {
		$essb_available_buttons_style = array();
		$essb_available_buttons_style ['button'] = array ("image" => "assets/images/button-style-01.png", "label" => "<b>Display as share button with icon and network name</b>" );
		$essb_available_buttons_style ['button_name'] = array ("image" => "assets/images/button-style-04.png", "label" => "<b>Display as share button with network name and without icon</b>" );
		$essb_available_buttons_style ['icon'] = array ("image" => "assets/images/button-style-02.png", "label" => "<b>Display share buttons only as icon without network names</b>" );
		$essb_available_buttons_style ['icon_hover'] = array ("image" => "assets/images/button-style-03.png", "label" => "<b>Display share buttons as icon with network name appear when button is pointed</b>" );
		
		return $essb_available_buttons_style;
	}
}

if (!function_exists('essb_available_buttons_width')) {
	function essb_available_buttons_width() {
		$essb_available_buttons_width = array();
		$essb_available_buttons_width [''] = array ("image" => "assets/images/button-width-01.png", "label" => "<b style='padding-bottom:10px;'>Automatic width</b><br/>" );
		$essb_available_buttons_width ['fixed'] = array ("image" => "assets/images/button-width-04.png", "label" => "<b>Fixed width</b><br/>" );
		$essb_available_buttons_width ['full'] = array ("image" => "assets/images/button-width-02.png", "label" => "<b>Full width</b>" );
		$essb_available_buttons_width ['column'] = array ("image" => "assets/images/button-width-03.png", "label" => "<b>Display in columns</b>" );
		
		return $essb_available_buttons_width;
	}
}

if (!function_exists('essb_available_social_profiles')) {
	function essb_available_social_profiles() {
		$essb_available_social_profiles = array ("twitter" => "Twitter", "facebook" => "Facebook", "google" => "Google+", "pinterest" => "Pinterest", "foursquare" => "foursquare", "yahoo" => "Yahoo!", "skype" => "skype", "yelp" => "yelp", "feedburner" => "FeedBurner", "linkedin" => "Linkedin", "viadeo" => "Viadeo", "xing" => "Xing", "myspace" => "Myspace", "soundcloud" => "soundcloud", "spotify" => "Spotify", "grooveshark" => "grooveshark", "lastfm" => "last.fm", "youtube" => "YouTube", "vimeo" => "vimeo", "dailymotion" => "Dailymotion", "vine" => "Vine", "flickr" => "flickr", "500px" => "500px", "instagram" => "Instagram", "wordpress" => "WordPress", "tumblr" => "tumblr", "blogger" => "Blogger", "technorati" => "Technorati", "reddit" => "reddit", "dribbble" => "dribbble", "stumbleupon" => "StumbleUpon", "digg" => "Digg", "envato" => "Envato", "behance" => "Behance", "delicious" => "Delicious", "deviantart" => "deviantART", "forrst" => "Forrst", "play" => "Play Store", "zerply" => "Zerply", "wikipedia" => "Wikipedia", "apple" => "Apple", "flattr" => "Flattr", "github" => "GitHub", "chimein" => "Chime.in", "friendfeed" => "FriendFeed", "newsvine" => "NewsVine", "identica" => "Identica", "bebo" => "bebo", "zynga" => "zynga", "steam" => "steam", "xbox" => "XBOX", "windows" => "Windows", "outlook" => "Outlook", "coderwall" => "coderwall", "tripadvisor" => "tripadvisor", "appnet" => "appnet", "goodreads" => "goodreads", "tripit" => "Tripit", "lanyrd" => "Lanyrd", "slideshare" => "SlideShare", "buffer" => "Buffer", "rss" => "RSS", "vkontakte" => "VKontakte", "disqus" => "DISQUS", "houzz" => "houzz", "mail" => "Mail", "patreon" => "Patreon", "paypal" => "Paypal", "playstation" => "PlayStation", "smugmug" => "SmugMug", "swarm" => "Swarm", "triplej" => "triplej", "yammer" => "Yammer", "stackoverflow" => "stackoverflow", "drupal" => "Drupal", "odnoklassniki" => "Odnoklassniki", "android" => "Android", "meetup" => "Meeptup", "persona" => "Mozilla Persona" );

		return $essb_available_social_profiles;
	}
}

if (!function_exists('essb_available_animations')) {
	function essb_available_animations($add_default = false) {
		$animations = array();
		$animations[''] = 'No animations';
		$animations['essb_button_animation_legacy1'] = 'Pop out';
		$animations['essb_button_animation_legacy2'] = 'Zoom out';
		$animations['essb_button_animation_legacy3'] = 'Flip';
		$animations['essb_button_animation_legacy4'] = 'Pop right';
		$animations['essb_button_animation_legacy5'] = 'Pop left';
		$animations['essb_button_animation_legacy6'] = 'Pop horizontal';

		$animations['essb_icon_animation1'] = 'Icon animation 1: Slide from right';
		$animations['essb_icon_animation2'] = 'Icon animation 2: Pop in';
		$animations['essb_icon_animation3'] = 'Icon animation 3: Fade in';
		$animations['essb_icon_animation4'] = 'Icon animation 4: Jump';
		$animations['essb_icon_animation5'] = 'Icon animation 5: Swing';
		$animations['essb_icon_animation6'] = 'Icon animation 6: Tada';
		$animations['essb_icon_animation7'] = 'Icon animation 7: Fade in from right';
		$animations['essb_icon_animation8'] = 'Icon animation 8: Fade in from left';
		$animations['essb_icon_animation9'] = 'Icon animation 9: Fade in from top';
		$animations['essb_icon_animation10'] = 'Icon animation 10: Fade in from bottom';
		$animations['essb_icon_animation11'] = 'Icon animation 11: Flash';
		$animations['essb_icon_animation12'] = 'Icon animation 12: Shake';
		$animations['essb_icon_animation13'] = 'Icon animation 13: Rubber band';
		$animations['essb_icon_animation14'] = 'Icon animation 14: Wooble';
		
		$animations['essb_button_animation1'] = 'Button animation 1: Slide from right';
		$animations['essb_button_animation2'] = 'Button animation 2: Pop in';
		$animations['essb_button_animation3'] = 'Button animation 3: Fade in';
		$animations['essb_button_animation4'] = 'Button animation 4: Jump';
		$animations['essb_button_animation5'] = 'Button animation 5: Swing';
		$animations['essb_button_animation6'] = 'Button animation 6: Tada';
		$animations['essb_button_animation7'] = 'Button animation 7: Fade in from right';
		$animations['essb_button_animation8'] = 'Button animation 8: Fade in from left';
		$animations['essb_button_animation9'] = 'Button animation 9: Fade in from top';
		$animations['essb_button_animation10'] = 'Button animation 10: Fade in from bottom';
		$animations['essb_button_animation11'] = 'Button animation 11: Flash';
		$animations['essb_button_animation12'] = 'Button animation 12: Shake';
		$animations['essb_button_animation13'] = 'Button animation 13: Rubber band';
		$animations['essb_button_animation14'] = 'Button animation 14: Wooble';
		
		return $animations;
	}
}

if (! function_exists ( 'essb_cached_counters_update' )) {
	function essb_cached_counters_update() {
		$periods = array ();
		$periods [1] = '1 Minute';
		$periods [5] = '5 Minutes';
		$periods [10] = '10 Minutes';
		$periods [15] = '15 Minutes';
		$periods [30] = '30 Minutes';
		$periods [45] = '45 Minutes';
		$periods [60] = '1 Hour';
		$periods [120] = '2 Hours';
		$periods [180] = '3 Hours';
		$periods [240] = '4 Hours';
		$periods [300] = '5 Hours';
		$periods [360] = '6 Hours';
		$periods [540] = '9 Hours';
		$periods [720] = '12 Hours';
		$periods [1080] = '18 Hours';
		$periods [1440] = '1 Day';
		$periods [4320] = '3 Days';
		$periods [7200] = '5 Days';
		$periods [10800] = '7 Days';
		
		return $periods;
	}
}

