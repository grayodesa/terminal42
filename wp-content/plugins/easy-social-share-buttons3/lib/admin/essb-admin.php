<?php

class ESSBAdminControler {
	
	private static $instance = null;
	public static function get_instance() {
	
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	
		return self::$instance;
	
	} // end get_instance;
	
	function __construct() {
		
		//add_action ('init',array ($this, 'essb_settings_redirect' )  );
		add_action ( 'admin_menu', 	array ($this, 'register_menu' ) );
		add_action ( 'admin_enqueue_scripts', array ($this, 'register_admin_assets' ), 99 );	
		$hook = (defined ( 'WP_NETWORK_ADMIN' ) && WP_NETWORK_ADMIN) ? 'network_admin_menu' : 'admin_menu';
		add_action ( $hook, array ($this, 'handle_save_settings' ) );
		
		if (is_admin()) {
			add_action ( 'wp_ajax_essb_settings_save', array ($this, 'actions_download_settings' ) );
		}
		
		// admin meta boxes
		add_action('add_meta_boxes', array ($this, 'handle_essb_metabox' ) );
		add_action('save_post',  array ($this, 'handle_essb_save_metabox'));
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
		
		
		$dismiss_import = isset($_REQUEST['dismiss_import']) ? $_REQUEST['dismiss_import'] : '';
		if ($dismiss_import == 'true') {
			update_option('essb2_converted', 'true');
		}
		
		$options_2 = get_option('easy-social-share-buttons');
		if (is_array($options_2)) {
			$converted_options_2 = get_option('essb2_converted');
			if (empty($converted_options_2)) {
				add_action ( 'admin_notices', array ($this, 'add_notice_import_options' ) );
			}
		}
		
		if (defined('ESSB_VERSION')) {
			add_action ( 'admin_notices', array ($this, 'add_notice_essb2_running' ) );
		}
		
		if (ESSB3_ADDONS_ACTIVE) {
			include_once(ESSB3_PLUGIN_ROOT . 'lib/admin/addons/essb-addons-helper.php');
			ESSBAddonsHelper::get_instance();
		}
		
		if (ESSB3_ADDONS_ACTIVE && class_exists('ESSBAddonsHelper')) {
			
			$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
			
			if (strpos($page, 'essb_') === false) {
				
				$addons = ESSBAddonsHelper::get_instance();
				$new_addons = $addons->get_new_addons_count();
			
				if ($new_addons > 0) {
					add_action ( 'admin_notices', array ($this, 'add_notice_new_addon' ) );
				}
			}
 		}
 		
 		$easymode = isset($_REQUEST['easymode']) ? $_REQUEST['easymode'] : '';
 		if (!empty($easymode)) {
 			if ($easymode == "activate") {
 				update_option(ESSB3_EASYMODE_NAME, 'true');
 			}
 			if ($easymode == "deactivate") {
 				update_option(ESSB3_EASYMODE_NAME, 'false');
 			}
 			
 			$this->essb_settings_redirect_after_easymode();
 		}
	}
	
	/**
	 * Add news dashboard widget
	 * 
	 * @since 3.6
	 */
	public function add_dashboard_widget() {
		// Create the widget
		wp_add_dashboard_widget( 'appscreo_news', apply_filters( 'appscreo_dashboard_widget_title', __( 'AppsCreo News', 'essb' ) ), array( $this, 'display_news_dashboard_widget' ) );
		
		// Make sure our widget is on top off all others
		global $wp_meta_boxes;
		
		// Get the regular dashboard widgets array
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		
		// Backup and delete our new dashboard widget from the end of the array
		$avada_widget_backup = array( 'appscreo_news' => $normal_dashboard['appscreo_news'] );
		unset( $normal_dashboard['appscreo_news'] );
		
		// Merge the two arrays together so our widget is at the beginning
		$sorted_dashboard = array_merge( $avada_widget_backup, $normal_dashboard );
		
		// Save the sorted array back into the original metaboxes
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}
	
	public function display_news_dashboard_widget() {
		// Create two feeds, the first being just a leading article with data and summary, the second being a normal news feed
		$feeds = array(
				'first' => array(
						'link'         => 'http://appscreo.com/',
						'url'          => 'http://appscreo.com/feed/',
						'title'        => __( 'AppsCreo News', 'essb' ),
						'items'        => 1,
						'show_summary' => 1,
						'show_author'  => 0,
						'show_date'    => 1,
				),
				'news' => array(
						'link'         => 'http://appscreo.com/',
						'url'          => 'http://appscreo.com/feed/',
						'title'        => __( 'AppsCreo News', 'essb' ),
						'items'        => 4,
						'show_summary' => 0,
						'show_author'  => 0,
						'show_date'    => 0,
				),
		);
		
		wp_dashboard_primary_output( 'appscreo_news', $feeds );
		
		print '<div class="essb-admin-widget">';
		print '<h4><strong>Subscribe to our mailing list and get interesting stuff and updates to your email inbox.</strong></h4>';
		print '<form action="//appscreo.us13.list-manage.com/subscribe/post?u=a1d01670c240536f6a70e7778&amp;id=c896311986" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>';
		print '<div class="input-text-wrap" id="title-wrap">';
		//print '<label class="screen-reader-text prompt" for="mce-EMAIL" id="title-prompt-text">Enter your email</label>';
		print '<input type="email" name="EMAIL" id="mce-EMAIL" autocomplete="off" placeholder="Enter your email" />';
		print '</div>';		
		print '<p><input type="submit" name="subscribe" id="mc-embedded-subscribe" class="button button-primary" value="Subscribe"></p>';
		print '</form>';
		print '</div>';
		
	}
	
	public function add_notice_new_addon() {
		if (ESSB3_ADDONS_ACTIVE && class_exists('ESSBAddonsHelper')) {
			$addons = ESSBAddonsHelper::get_instance();
			$new_addons = $addons->get_new_addons();
		
			$dismiss_keys = "";
			$new_addons_list = "";
			$cnt = 0;
			foreach ($new_addons as $key => $data) {
				/*$all_addons_button = '<a href="'.admin_url ("admin.php?page=essb_addons").'"  text="' . __ ( 'Extensions', ESSB3_TEXT_DOMAIN ) . '" class="button" style="margin-right: 5px; float: right; margin-top: -5px;"><span class="dashicons dashicons-admin-plugins" style="margin-top: 3px;"></span>&nbsp;' . __ ( 'View list of all extensions', ESSB3_TEXT_DOMAIN ) . '</a>';
		
				$dismiss_url = esc_url_raw(add_query_arg(array('dismiss' => 'true', 'addon' => $key), admin_url ("admin.php?page=essb_options")));
		
				$dismiss_addons_button = '<a href="'.$dismiss_url.'"  text="' . __ ( 'Extensions', ESSB3_TEXT_DOMAIN ) . '" class="button" style="float: right; margin-top:-5px;"><span class="dashicons dashicons-no" style="margin-top: 3px;"></span>' . __ ( 'Close & hide this message', ESSB3_TEXT_DOMAIN ) . '</a>';
				printf ( '<div class="updated fade"><p style="padding-top: 5px; padding-bottom: 5px;">New add-on for <b>Easy Social Share Buttons for WordPress</b> is available: <a href="%2$s" target="_blank"><b>%1$s</b></a> %4$s%3$s</p></div>', $data['title'], $data['url'], $all_addons_button, $dismiss_addons_button );*/
				
				if ($dismiss_keys != "") { $dismiss_keys .= ','; }
				$dismiss_keys .= $key;
			
				$cnt++;
				
				if ($new_addons_list != '') {
					$new_addons_list .= ', ';
				}
				//$new_addons_list .= sprintf('<a href="%2$s" target="_blank"><b>%1$s</b></a>', $data['title'], $data['url']); 
				$new_addons_list .= sprintf('<a href="%2$s"><b>%1$s</b></a>', $data['title'], admin_url ("admin.php?page=essb_addons"));
			}
			
			$single_text = __('New extension for <b>Easy Social Share Buttons for WordPress</b>: ', 'essb');
			$plural_text = __('New extensions for <b>Easy Social Share Buttons for WordPress</b>: ', 'essb');
			
			$display_text = ($cnt > 1) ? $plural_text : $single_text;
			$dismiss_url = esc_url_raw(add_query_arg(array('dismiss' => 'true', 'addon' => $dismiss_keys), admin_url ("admin.php?page=essb_options")));
			$dismiss_addons_button = '<a href="'.$dismiss_url.'"  text="' . __ ( 'Hide notice', 'essb' ) . '" class="button"><span class="dashicons dashicons-no" style="margin-top: 3px;"></span></a>';
			printf ( '<div class="updated fade"><div><div style="padding-top: 10px; padding-bottom: 10px; display: inline-block; width:%4$s;">%1$s%2$s</div><div style="display: inline-block; width:%5$s; text-align: right; vertical-align: top; margin-top:5px;">%3$s</div></div></div>', $display_text, $new_addons_list, $dismiss_addons_button, '95%', '5%' );
		}
		
	}
	
	public function add_notice_import_options() {
		?>
<div class="updated fade">
	<p style="padding: 5px 5px;">
		<strong><a
			href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo"
			target="_blank">Easy Social Share Buttons for WordPress</a></strong>
		found that you you have options set with previous version of product.
		</strong> <a
			href="<?php echo admin_url('admin.php?page=essb_redirect_advanced&tab=advanced&section=convert&subsection&dismiss_import=true');?>"
			class="button"
			style="float: right; margin-top: -8px; margin-left: 5px;">Dismiss</a><a
			href="<?php echo admin_url('admin.php?page=essb_redirect_advanced&tab=advanced&section=convert&subsection');?>"
			class="button" style="float: right; margin-top: -8px;">Click here to
			convert and import your previous options</a>
	</p>

</div>
<?php
	}
	
	public function add_notice_essb2_running() {
		?>
	<div class="updated fade">
		<p style="padding: 5px 5px;">
			<strong><a
				href="http://codecanyon.net/item/easy-social-share-buttons-for-wordpress/6394476?ref=appscreo"
				target="_blank">Easy Social Share Buttons for WordPress</a></strong>
			detects that older version of plugin is running. Please go to your Installed Plugins screen and deactivate old version of plugin (versions 2.x or 1.x) .
			</strong> <a
				href="<?php echo admin_url('plugins.php?s=easy+social+share+buttons');?>"
				class="button"
				style="float: right; margin-top: -8px; margin-left: 5px;">Click here to go to Installed Plugins list</a>
		</p>
	
	</div>
	<?php
		}
	
	public function handle_essb_metabox() {
		global $essb_options;
		
		$display_in_types = ESSBOptionValuesHelper::options_value($essb_options, 'display_in_types');
		$turnoff_essb_optimize_box = ESSBOptionValuesHelper::options_bool_value($essb_options, 'turnoff_essb_optimize_box');
		$turnoff_essb_stats_box = ESSBOptionValuesHelper::options_bool_value($essb_options, 'turnoff_essb_stats_box');
		$turnoff_essb_advanced_box = ESSBOptionValuesHelper::options_bool_value($essb_options, 'turnoff_essb_advanced_box');
		
		$stats_are_activated = ESSBOptionValuesHelper::options_bool_value($essb_options, 'stats_active');
		if (!$stats_are_activated) {
			$turnoff_essb_stats_box = true;
		}
		
		if (!is_array($display_in_types)) {
			$display_in_types = array();
		}
		
		// get post types
		$pts	 = get_post_types( array('show_ui' => true, '_builtin' => true) );
		$cpts	 = get_post_types( array('show_ui' => true, '_builtin' => false) );
		foreach ( $pts as $pt ) {
			if (defined('ESSB3_SSO_ACTIVE') && !$turnoff_essb_optimize_box) {
				add_meta_box('essb_metabox_sso', __('Easy Social Share Buttons: Social Share Optimization', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_optimization', $pt, 'normal', 'high');				
			}
			if (in_array($pt, $display_in_types)) {
				add_meta_box('essb_metabox', __('Easy Social Share Buttons', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_onoff', $pt, 'side', 'high');
				
				if (!$turnoff_essb_optimize_box) {
					add_meta_box('essb_metabox_share', __('Easy Social Share Buttons: Share Customization', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_customization', $pt, 'normal', 'high');
				}
				
				if (!$turnoff_essb_advanced_box) {
					add_meta_box('essb_metabox_visual', __('Easy Social Share Buttons: Visual Customization', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_visual', $pt, 'normal', 'high');
				}
				
				if (!$turnoff_essb_stats_box) {
					add_meta_box('essb_metabox_stats', __('Easy Social Share Buttons: Stats', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_stats', $pt, 'normal', 'core');
				}
				
				
			}
				
		}
		
		foreach ( $cpts as $cpt ) {
			if (defined('ESSB3_SSO_ACTIVE') && !$turnoff_essb_optimize_box) {
				add_meta_box('essb_metabox_sso', __('Easy Social Share Buttons: Social Share Optimization', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_optimization', $cpt, 'normal', 'high');				
			}
			if (in_array($cpt, $display_in_types)) {
				add_meta_box('essb_metabox', __('Easy Social Share Buttons', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_onoff', $cpt, 'side', 'high');
				
				if (!$turnoff_essb_optimize_box) {
					add_meta_box('essb_metabox_share', __('Easy Social Share Buttons: Share Customization', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_customization', $cpt, 'normal', 'high');
				}
				
				if (!$turnoff_essb_advanced_box) {
					add_meta_box('essb_metabox_visual', __('Easy Social Share Buttons: Visual Customization', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_visual', $cpt, 'normal', 'high');
				}

				if (!$turnoff_essb_stats_box) {
					add_meta_box('essb_metabox_stats', __('Easy Social Share Buttons: Stats', ESSB3_TEXT_DOMAIN), 'essb_register_settings_metabox_stats', $cpt, 'normal', 'core');
				}
				
			}
		}
	}
	
	public function handle_essb_save_metabox() {
		global $post, $post_id;
		
		if (! $post) {
			return $post_id;
		}
		
		if (! $post_id) {
			$post_id = $post->ID;
		}
		
		$essb_metabox = isset($_REQUEST['essb_metabox']) ? $_REQUEST['essb_metabox'] : array();
		
		$this->save_metabox_value ( $post_id, 'essb_off', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_button_style', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_template', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_counters', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_counter_pos', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_total_counter_pos', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_customizer', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_animations', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_optionsbp', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_content_position', $essb_metabox );
		foreach ( essb_available_button_positions() as $position => $name ) {
			$this->save_metabox_value ( $post_id, "essb_post_button_position_{$position}", $essb_metabox );
		}
		$this->save_metabox_value ( $post_id, 'essb_post_native', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_native_skin', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_share_message', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_share_url', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_share_image', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_share_text', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_pin_image', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_fb_url', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_plusone_url', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_hashtags', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_username', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_tweet', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_activate_ga_campaign_tracking', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_og_desc', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_og_author_of_post', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_og_title', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_og_image', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_og_video', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_og_video_w', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_og_video_h', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_desc', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_title', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_twitter_image', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_google_desc', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_google_title', $essb_metabox );
		$this->save_metabox_value ( $post_id, 'essb_post_google_image', $essb_metabox);
		$this->save_metabox_value ( $post_id, 'essb_activate_sharerecovery', $essb_metabox);
				
		// Twitter Custom Share Value
		$essb_pc_twitter = ESSBOptionValuesHelper::options_value($essb_metabox, 'essb_pc_twitter');
		if (!empty($essb_pc_twitter)) {
			$this->save_metabox_value_simple($post_id, 'essb_pc_twitter', $essb_pc_twitter);
		}
		
		// @since 3.4.1
		// apply on save clearing and caching of post meta values that will be used within plugin
		$post_image = has_post_thumbnail( $post_id ) ? wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' ) : '';
		$image = ($post_image != '') ? $post_image[0] : '';		
		$this->save_metabox_value_simple_with_clear($post_id, 'essb_cached_image', $image);
		
		// clear cached counters ttl
		delete_post_meta($post_id, 'essb_cache_timestamp');
	}
	
	public function save_metabox_value($post_id, $option, $valueContainer) {
		$value = ESSBOptionValuesHelper::options_value($valueContainer, $option);
		if (!empty($value)) {
			update_post_meta ( $post_id, $option, $value );
		}
		else {
			delete_post_meta ( $post_id, $option );
		}
	}
	
	public function save_metabox_value_simple($post_id, $option, $value) {
		update_post_meta ( $post_id, $option, $value );
	}
	
	public function save_metabox_value_simple_with_clear($post_id, $option, $value) {
		if (!empty($value)) {
			update_post_meta ( $post_id, $option, $value );
		}
		else {
			delete_post_meta ( $post_id, $option );
		}
	}
	
	public function register_menu() {
		global $essb_all_options, $essb_navigation_tabs, $essb_options;
		
		//$menu_pos = ESSBOptionValuesHelper::options_bool_value($essb_all_options, 'register_menu_under_settings');
		$menu_pos = false;
		$essb_access = ESSBOptionValuesHelper::options_value($essb_options, 'essb_access'); 
		if (empty($essb_access)) {
			$essb_access = "edit_pages";
		}
		
		if ($menu_pos) {
			add_options_page ( "Easy Social Share Buttons", "Easy Social Share Buttons", 'edit_pages', "essb_options", array ($this, 'essb_settings_load' ), ESSB3_PLUGIN_URL . '/assets/images/essb_16.png', 114 );
		}
		else {
			add_menu_page ( "Easy Social Share Buttons", "Easy Social Share Buttons", $essb_access, "essb_options", array ($this, 'essb_settings_load' ) );
			
			$is_first = true;
			foreach ( $essb_navigation_tabs as $name => $label ) {
				if ($is_first) {
					add_submenu_page( 'essb_options', $label, $label, $essb_access, 'essb_options', array ($this, 'essb_settings_load' ));
					$is_first = false;
				}
				else {
					add_submenu_page( 'essb_options', $label, $label, $essb_access, 'essb_redirect_'.$name, array ($this, 'essb_settings_redirect1' ));
				}
			}
			// submenu init
			//		add_submenu_page( 'se_settings', 'Settings', 'Settings', 'edit_pages', 'se_settings', array($this, 'es_settings_load'));
			
			add_submenu_page( 'essb_options', __('About', ESSB3_TEXT_DOMAIN), __('About', ESSB3_TEXT_DOMAIN), $essb_access, 'essb_about', array ($this, 'essb_settings_redirect_about' ));
			if (ESSB3_ADDONS_ACTIVE) {
				add_submenu_page( 'essb_options', __('Extensions', ESSB3_TEXT_DOMAIN), '<span style="color:#f39c12;">'.__('Extensions', ESSB3_TEXT_DOMAIN).'</span>', $essb_access, 'essb_addons', array ($this, 'essb_settings_redirect_addons' ));
			}
		}
	}
	
	public function essb_settings_redirect1() {
		
		if ($this->require_easy_mode_screen()) {
			include (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-choose-mode.php');
		}
		
		if (defined('ESSB3_LIGHTMODE')) {
			include (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-settings-light.php');
		}
		else {
			include (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-settings.php');
		}
		
	}

	public function essb_settings_redirect_about() {
		include (ESSB3_PLUGIN_ROOT . 'lib/admin/welcome/essb-welcome.php');
	}
	
	public function essb_settings_redirect_addons() {
		include (ESSB3_PLUGIN_ROOT . 'lib/admin/addons/essb-addons.php');
	}
	
	public function essb_settings_redirect() {
		$requested = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";
		
		if (strpos($requested, 'essb_redirect_') !== false) {
			$options_page = str_replace('essb_redirect_', '', $requested);
			//print $options_page;
			//print admin_url ( 'admin.php?page=essb_options&tab=' . $options_page );
			if ($options_page != '') {
				wp_redirect(admin_url ( 'admin.php?page=essb_options&tab=' . $options_page ));
			}
		}
	}

	public function essb_settings_redirect_after_easymode() {
		$requested = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";
	
		if (strpos($requested, 'essb_redirect_') !== false) {
			$options_page = str_replace('essb_redirect_', '', $requested);
			//print $options_page;
			//print admin_url ( 'admin.php?page=essb_options&tab=' . $options_page );
			if ($options_page != '') {
				wp_redirect(admin_url ( 'admin.php?page=essb_options&tab=' . $options_page ));
			}
		}
		else if ($requested == "essb_options") {
			wp_redirect(admin_url ( 'admin.php?page=essb_options' ));
		}
	}
	
	
	public function essb_settings_load() {
	
		if ($this->require_easy_mode_screen()) {
			include (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-choose-mode.php');
		}
		
		if (defined('ESSB3_LIGHTMODE')) {
			include (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-settings-light.php');
		}
		else {
			include (ESSB3_PLUGIN_ROOT . 'lib/admin/essb-settings.php');
		}
	}	
	
	public function require_easy_mode_screen() {
		
		if (!defined('ESSB3_LIGHTMODE')) {
			return false;
		}
		else {
			return true;
		}
	}
	
	public function register_admin_assets($hook) {	
		global $essb_admin_options;	
		
		$requested = isset($_REQUEST['page']) ? $_REQUEST['page'] : "";
		
		wp_register_style ( 'essb-admin-icon', ESSB3_PLUGIN_URL . '/assets/admin/easysocialshare.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-admin-icon' );

		// loading main plugin CSS to allow usage of extended controls
		wp_register_style ( 'essb-admin3', ESSB3_PLUGIN_URL . '/assets/admin/essb-admin3.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-admin3' );
		
		wp_enqueue_script ( 'essb-admin3', ESSB3_PLUGIN_URL . '/assets/admin/essb-admin3.js', array ('jquery' ), ESSB3_VERSION, true );
		
		$deactivate_fa = ESSBOptionValuesHelper::options_bool_value($essb_admin_options, 'deactivate_fa');
		// styles
		
		if (!$deactivate_fa) {
			wp_enqueue_style ( 'essb-fontawsome', ESSB3_PLUGIN_URL . '/assets/admin/font-awesome.min.css', array (), ESSB3_VERSION );
		}
		
		// register global admin assets only on required plugin settings pages
		if (strpos($requested, 'essb_') === false && strpos($requested, 'easy-social-metrics-lite') === false) {
			return;
		}
		
		

		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_style( 'wp-color-picker');
		wp_enqueue_script( 'wp-color-picker');
		
		// scripts
		//wp_enqueue_script ( 'essb-admin', ESSB3_PLUGIN_URL . '/assets/admin/essb-admin.js', array ('jquery' ), ESSB3_VERSION, true );
		
		//wp_enqueue_script ( 'essb-select2', ESSB3_PLUGIN_URL . '/assets/admin/select2.full.js', array ('jquery' ), ESSB3_VERSION, true );
		//wp_enqueue_style ( 'essb-select2', ESSB3_PLUGIN_URL . '/assets/admin/select2.min.css', array (), ESSB3_VERSION );
		
		wp_register_style ( 'essb-datatable', ESSB3_PLUGIN_URL . '/assets/admin/datatable/jquery.dataTables.css', array (), ESSB3_VERSION );
		wp_enqueue_style ( 'essb-datatable' );
		wp_enqueue_script ( 'essb-datatable', ESSB3_PLUGIN_URL . '/assets/admin/datatable/jquery.dataTables.js', array ('jquery' ), ESSB3_VERSION, true );
		
		wp_enqueue_style ( 'essb-morris-styles', ESSB3_PLUGIN_URL.'/assets/admin/morris.min.css',array (), ESSB3_VERSION );
		
		wp_enqueue_script ( 'essb-morris', ESSB3_PLUGIN_URL . '/assets/admin/morris.min.js', array ('jquery' ), ESSB3_VERSION );
		wp_enqueue_script ( 'essb-raphael', ESSB3_PLUGIN_URL . '/assets/admin/raphael-min.js', array ('jquery' ), ESSB3_VERSION );
		
		//wp_enqueue_script ( 'essb-chartjs', ESSB3_PLUGIN_URL . '/assets/admin/chart.min.js', array ('jquery' ), ESSB3_VERSION );
	}
	
	public function handle_save_settings() {
		if (@$_POST && isset ( $_POST ['option_page'] )) {
			$changed = false;
			if ('essb_settings_group' == $this->getval($_POST, 'option_page' )) {
				$this->update_optons();
				$this->update_fanscounter_options();
				$this->restore_settings();
				$this->apply_readymade();
				//$this->apply_import();
				$changed = true;
				
				if (class_exists('ESSBDynamicCache')) {
					ESSBDynamicCache::flush();
				}
				
				if (class_exists('ESSBPrecompiledResources')) {
					ESSBPrecompiledResources::flush();
				}
				
				if (function_exists ( 'purge_essb_cache_static_cache' )) {
					purge_essb_cache_static_cache ();
				} 
			}
				
			if ($changed) {
				if (defined('ESSB3_SOCIALFANS_ACTIVE')) {
					if (class_exists('ESSBSocialFollowersCounter')) {
						essb_followers_counter()->settle_immediate_update();
						
						$current_options = get_option(ESSB3_OPTIONS_NAME);
						$fanscounter_clear_on_save = ESSBOptionValuesHelper::options_bool_value($current_options, 'fanscounter_clear_on_save');
						if ($fanscounter_clear_on_save) {
							essb_followers_counter()->clear_stored_values();
							//print "clear active";
						}
					}
				}
				
				$user_section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
				$user_subsection = isset($_REQUEST['subsection']) ? $_REQUEST['subsection'] : '';
				
				//$goback = add_query_arg ( 'settings-updated', 'true', wp_get_referer () );
				$goback = esc_url_raw(add_query_arg(array('settings-updated' => 'true', 'section' => $user_section, 'subsection' => $user_subsection), wp_get_referer ()));
				//$goback = str_replace('#038;', '', $goback);
				wp_redirect ( $goback );
				die ();
			}
		}
		
		if (@$_REQUEST && isset($_REQUEST['import2x'])) {
			$this->apply_import();
			
			$user_section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
			$user_subsection = isset($_REQUEST['subsection']) ? $_REQUEST['subsection'] : '';
			
			//$goback = add_query_arg ( 'settings-updated', 'true', wp_get_referer () );
			$goback = remove_query_arg('import2x');
			$goback = esc_url_raw(add_query_arg(array('settings-imported' => 'true', 'section' => $user_section, 'subsection' => $user_subsection), wp_get_referer ()));
			
			//$goback = str_replace('#038;', '', $goback);
			wp_redirect ( $goback );
			die ();
		}
		
		if (@$_REQUEST && isset($_REQUEST['ready_style'])) {
			$this->apply_readymade();
			
			$user_section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
			$user_subsection = isset($_REQUEST['subsection']) ? $_REQUEST['subsection'] : '';
				
			//$goback = add_query_arg ( 'settings-updated', 'true', wp_get_referer () );
			$goback = remove_query_arg('ready_style');
			$goback = esc_url_raw(add_query_arg(array('settings-imported' => 'true', 'section' => $user_section, 'subsection' => $user_subsection), wp_get_referer ()));
				
			//$goback = str_replace('#038;', '', $goback);
			wp_redirect ( $goback );
			die ();
		}
	}
	
	public function apply_import() {
		$import = isset($_REQUEST['import2x']) ? $_REQUEST['import2x'] : '';
		
		if (!empty($import)) {
			if ($import == "post") {
				$this->import_post_settings();
			}
			if ($import == "settings") {
				$this->import_plugin_settings();
			}
			if ($import == "fans") {
				$this->import_fanscounter();
			}
			if ($import == "stats") {
				$this->import_stats_data();
			}
 		}
	}
	
	public function import_stats_data() {
		global $wpdb;
		
		$table_name = $wpdb->prefix . ESSB3_TRACKER_TABLE;
		$table_version_2x = $wpdb->prefix . 'essb_click_stats';
		
		$sql = "INSERT INTO ".$table_name.' (essb_date,essb_blog_id,essb_post_id,essb_service,essb_mobile) SELECT essb_date, essb_blog_id,essb_post_id,essb_service,"false" FROM '.$table_version_2x;
		
		$wpdb->query($sql);
	}
	
	public function import_fanscounter() {
		$previous_options = get_option('essb-fans-options');
		$essb_previous_supported_items = array ('facebook', 'twitter', 'google', 'youtube', 'vimeo', 'dribbble', 'github', 'envato', 'soundcloud', 'behance', 'delicious', 'instagram', 'pinterest', 'love', 'vk', 'rss', 'posts', 'comments', 'users', 'mailchimp', 'linkedin', 'tumblr', 'steam', 'flickr', 'total' );
		
		$defaults = ESSBSocialFollowersCounterHelper::options_structure();
		
		$facebook_id = $previous_options['social']['facebook']['id'];
		$facebook_token = $previous_options['social']['facebook']['token'];
		
		$defaults['facebook']['id'] = $facebook_id;
		$defaults['facebook']['access_token'] = $facebook_token;

		$twitter_id = $previous_options['social']['twitter']['id'];
		$twitter_key = $previous_options['social']['twitter']['key'];
		$twitter_secret = $previous_options['social']['twitter']['secret'];
		$twitter_token = $previous_options['social']['twitter']['token'];
		$twitter_tokensecret = $previous_options['social']['twitter']['tokensecret'];
		
		$defaults['twitter']['id'] = $twitter_id;
		$defaults['twitter']['consumer_key'] = $twitter_key;
		$defaults['twitter']['consumer_secret'] = $twitter_secret;
		$defaults['twitter']['access_token'] = $twitter_token;
		$defaults['twitter']['access_token_secret'] = $twitter_tokensecret;
		
		$google_id = $previous_options['social']['google']['id'];
		$google_type = $previous_options['social']['google']['type'];
		$google_api = $previous_options['social']['google']['api'];

		$defaults['google']['id'] = $google_id;
		$defaults['google']['api_key'] = $google_api;

		$defaults['youtube']['id'] = $previous_options['social']['youtube']['id'];
		$defaults['vimeo']['id'] = $previous_options['social']['vimeo']['id'];
		$defaults['pinterest']['id'] = $previous_options['social']['pinterest']['id'];
		$defaults['vk']['id'] = $previous_options['social']['vk']['id'];
		
		$defaults['instgram']['id'] = $previous_options['social']['instagram']['id'];
		$defaults['instgram']['api_key'] = $previous_options['social']['instagram']['api'];

		$defaults['mailchimp']['list_id'] = $previous_options['social']['mailchimp']['id'];
		$defaults['mailchimp']['api_key'] = $previous_options['social']['mailchimp']['api'];
		$defaults['mailchimp']['list_url'] = $previous_options['social']['mailchimp']['url'];
		
		$defaults['tumblr']['id'] = $previous_options['social']['tumblr']['id'];
		$defaults['tumblr']['api_key'] = $previous_options['social']['tumblr']['key'];
		$defaults['tumblr']['api_secret'] = $previous_options['social']['tumblr']['secret'];
		$defaults['tumblr']['access_token'] = $previous_options['social']['tumblr']['token'];
		$defaults['tumblr']['access_token_secret'] = $previous_options['social']['tumblr']['tokensecret'];
		
		$new_options = array();
		foreach ($defaults as $network => $options) {
			foreach ($options as $key => $value) {
				$settings_key = "essb3fans_".$network."_".$key;
				$new_options[$settings_key] = $value;
			}
		}
		
		update_option(ESSB3_OPTIONS_NAME_FANSCOUNTER, $new_options);
	}
	
	public function import_plugin_settings() {
		$previous_options = get_option('easy-social-share-buttons');
		
		if (!is_array($previous_options)) {
			return;
		}
		
		// basic change of settings;
		//activate_total_counter_text
		$activate_total_counter_text_value = ESSBOptionValuesHelper::options_value($previous_options, 'activate_total_counter_text_value');
		if (!empty($activate_total_counter_text_value)) {
			$previous_options['activate_total_counter_text'] = $activate_total_counter_text_value;
		}
		
		// button_style
		$hide_social_name = ESSBOptionValuesHelper::options_value($previous_options, 'hide_social_name'); // 1
		$force_hide_social_name = ESSBOptionValuesHelper::options_value($previous_options, 'force_hide_social_name'); // true
		$force_hide_icons = ESSBOptionValuesHelper::options_value($previous_options, 'force_hide_icons'); // true
		$force_hide_total_count = ESSBOptionValuesHelper::options_value($previous_options, 'force_hide_total_count'); // true
		
		$button_style = 'button';
		if ($hide_social_name == '1') {
			$button_style = 'icon_hover';
		}
		if ($force_hide_social_name == 'true') {
			$button_style = 'icon';
		}
		if ($force_hide_icons == 'true') {
			$button_style = 'button_name';
		}
		$previous_options ['button_style'] = $button_style;
		
		if ($force_hide_total_count == 'true') {
			$previous_options ['total_counter_pos'] = 'hidden';
		}
		
		$previous_options ['button_position'] = array();
		// display position
		$display_where = ESSBOptionValuesHelper::options_value ( $previous_options, 'display_where' );
		if ($display_where == "top" || $display_where == "bottom" || $display_where == "both" || $display_where == "float") {
			$previous_options['content_position'] = 'content_'.$display_where;
		} else if ($display_where == 'likeshare') {
			$previous_options['content_position'] = 'content_nativeshare';
		} else if ($display_where == 'sharelike') {
			$previous_options['content_position'] = 'content_sharenative';
		} else {
			
			if ($display_where == 'sidebar') {
				$previous_options['button_position'][] = 'sidebar';
			}
			if ($display_where == 'popup') {
				$previous_options['button_position'][] = 'popup';
			}
			if ($display_where == 'flyin') {
				$previous_options['button_position'][] = 'flyin';
			}
			if ($display_where == 'postfloat') {
				$previous_options['button_position'][] = 'postfloat';
			}
		}
		
		// active networks
		$networks = ESSBOptionValuesHelper::options_value ( $previous_options, 'networks' ); // array
		$user_networks = array ();
		if (is_array ( $networks )) {
			foreach ( $networks as $k => $v ) {
				$is_active = ($v [0] == 1) ? true : false;
				$network_name = $v [1];
				if (empty ( $network_name )) {
					$network_name = "-";
				}
				
				$network_option_value = "user_network_name_" . $k;
				
				if ($is_active) {
					$user_networks [] = $k;
				}
				$previous_options [$network_option_value] = $network_name;
			}
		}
		
		$previous_options ['networks'] = $user_networks;
		
		// for compatibility we activate native buttons
		$previous_options ['native_active'] = true;
		$previous_options ['fanscounter_active'] = true;
		
		update_option(ESSB3_OPTIONS_NAME, $previous_options);
		update_option('essb2_converted', 'true');
		
		$stats_active = ESSBOptionValuesHelper::options_value($previous_options, 'stats_active');
		if ($stats_active) {
			ESSBSocialShareAnalyticsBackEnd::install();
		}
	}
	
	public function import_post_settings() {
		global $wpdb;
		$post_types = array ("post", "page" );
		$querydata = new WP_Query ( array ('posts_per_page' => - 1, 'post_status' => 'publish', 'post_type' => $post_types ) );
		$translation_settings_map = array ();
		$translation_settings_map ['essb_position'] = 'complex';
		$translation_settings_map ['essb_theme'] = 'essb_post_template';
		$translation_settings_map ['essb_names'] = 'complex';
		$translation_settings_map ['essb_counter'] = 'essb_post_counters';
		$translation_settings_map ['essb_counter_pos'] = 'essb_post_counter_pos';
		$translation_settings_map ['essb_total_counter_pos'] = 'essb_post_total_counter_pos';
		$translation_settings_map ['essb_hidefb'] = 'complex';
		$translation_settings_map ['essb_hideplusone'] = 'complex';
		$translation_settings_map ['essb_hidevk'] = 'complex';
		$translation_settings_map ['essb_hidetwitter'] = 'complex';
		$translation_settings_map ['essb_hideyoutube'] = 'complex';
		$translation_settings_map ['essb_hidepinfollow'] = 'complex';
		$translation_settings_map ['essb_another_display_sidebar'] = 'essb_post_button_position_sidebar';
		$translation_settings_map ['essb_another_display_popup'] = 'essb_post_button_position_popup';
		$translation_settings_map ['essb_another_display_postfloat'] = 'essb_post_button_position_postfloat';
		$translation_settings_map ['essb_another_display_flyin'] = 'essb_post_button_position_flyin';
		$translation_settings_map ['essb_activate_customizer'] = 'essb_post_animations';
		$translation_settings_map ['essb_opt_by_bp'] = 'essb_post_optionsbp';
		$translation_settings_map ['essb_animation'] = 'essb_post_animations';
		$translation_settings_map ['essb_activate_nativeskinned'] = 'essb_post_native_skin';
		
		if ($querydata->have_posts ()) {
			while ( $querydata->have_posts () ) {
				$querydata->the_post ();
				global $post;
				$post_id = $post->ID;
				
				foreach ( $translation_settings_map as $old_key => $new_key ) {
					$value = get_post_meta ( $post_id, $old_key, true );
					
					if (empty ( $value )) {
						continue;
					}
					
					if ($old_key == "essb_theme") {
						$value = ESSBCoreHelper::template_folder($value);
					}
					
					if ($new_key != 'complex') {
						if ($value == '0') {
							$value = 'no';
						}
						if ($value == '1') {
							$value = 'yes';
						}
						
						
						
						$this->save_metabox_value_simple ( $post_id, $new_key, $value );
					} else {
						switch ($old_key) {
							case "essb_position" :
								break;
							case "essb_names" :
								$new_value = "";
								if ($value == "0") {
									$new_value = "button";
								} else {
									$new_value = "icon_hover";
								}
								$this->save_metabox_value_simple ( $post_id, 'essb_post_button_style', $new_value );
								break;
							case "essb_hidefb" :
							case "essb_hideplusone" :
							case "essb_hidevk" :
							case "essb_hidetwitter" :
							case "essb_hideyoutube" :
							case "essb_hidepinfollow" :
								if ($value == "1") {
									$this->save_metabox_value_simple ( $post_id, 'essb_post_native', 'no' );
								}
								break;
							case "essb_position" :
								if ($value == "top" || $value == "bottom" || $value == "both" || $value == "float") {
									$this->save_metabox_value_simple ( $post_id, 'essb_post_content_position', 'content_' . $value );
								} else if ($value == 'likeshare') {
									$this->save_metabox_value_simple ( $post_id, 'essb_post_content_position', 'content_nativeshare' );
								} else if ($value == 'sharelike') {
									$this->save_metabox_value_simple ( $post_id, 'essb_post_content_position', 'content_sharenative' );
								} else {
									$this->save_metabox_value_simple ( $post_id, 'essb_post_content_position', 'no' );
									
									if ($value == 'sidebar') {
										$this->save_metabox_value_simple ( $post_id, 'essb_post_button_position_sidebar', 'yes' );
									}
									if ($value == 'popup') {
										$this->save_metabox_value_simple ( $post_id, 'essb_post_button_position_popup', 'yes' );
									}
									if ($value == 'flyin') {
										$this->save_metabox_value_simple ( $post_id, 'essb_post_button_position_flyin', 'yes' );
									}
									if ($value == 'postfloat') {
										$this->save_metabox_value_simple ( $post_id, 'essb_post_button_position_postfloat', 'yes' );
									}
								}
								break;
						}
					}
				}
			}
		}
	}
	
	public function restore_settings() {
		//essb_backup[configuration]
		$result = false;
		
		$backup_element = isset($_REQUEST['essb_backup']) ? $_REQUEST['essb_backup'] : array();
		
		$backup_string = isset($backup_element['configuration1']) ? $backup_element['configuration1'] : '';
		if ($backup_string != '') {
			$backup_string = htmlspecialchars_decode ( $backup_string );
			$backup_string = stripslashes ( $backup_string );
				
			$imported_options = json_decode ( $backup_string, true );
			
			if (is_array($imported_options)) {
				$result = true;
				update_option(ESSB3_OPTIONS_NAME, $imported_options);
			}
		}
		
		if (isset($_FILES['essb_backup_file'])) {
			$import_file = $_FILES['essb_backup_file']['tmp_name'];
			if( !empty( $import_file ) ) {
			// Retrieve the settings from the file and convert the json object to an array.
				$settings = (array) json_decode( file_get_contents( $import_file ) );
				update_option( ESSB3_OPTIONS_NAME, $settings );
			}
		}
		
		return $result;
	}
	
	public function apply_readymade() {
		$ready_made_string = isset($_REQUEST['ready_style']) ? $_REQUEST['ready_style'] : '';

		if ($ready_made_string != '') {
			include_once(ESSB3_PLUGIN_ROOT . '/lib/admin/essb-readymade-styles.php');
			
			$exist_setting = isset($ready_made_styles[$ready_made_string]) ? $ready_made_styles[$ready_made_string] : '';
			
			if (!empty($exist_setting)) {
				$new_options = ESSB_Manager::convert_ready_made_option($exist_setting);
				update_option(ESSB3_OPTIONS_NAME, $new_options);
			}
			
		}
	}
	
	public function update_fanscounter_options() {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		
		$current_options = get_option(ESSB3_OPTIONS_NAME_FANSCOUNTER);
		if (!is_array($current_options)) {
			$current_options = array();
		}
		
		$current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
		$user_options = isset($_REQUEST['essb_options_fans']) ? $_REQUEST['essb_options_fans'] : array();
		
		if ($current_tab == '') {
			return;
		}
		
		$options = $essb_section_options[$current_tab];
		
		foreach($options as $section => $fields) {
			$section_options = $fields;
				
			foreach ($section_options as $option) {
				$type = $option['type'];
				$id = isset($option['id']) ? $option['id'] : '';
		
				if ($id == '') {
					continue;
				}
		
				if (strpos($id, 'essb3fans_') === false) {
					continue;
				}
						
		
				switch ($type) {
					case "checkbox_list_sortable":
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';
						$current_options[$id] = $option_value;
		
						$option_value = isset($user_options[$id.'_order']) ? $user_options[$id.'_order'] : '';
						$current_options[$id.'_order'] = $option_value;
						break;
					default:
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';
						$current_options[$id] = $option_value;
				
						break;
				}
			}
		}
		//print_r($current_options);
		update_option(ESSB3_OPTIONS_NAME_FANSCOUNTER, $current_options);
		
		// clear cached timeouts for social networks
		if (defined('ESSB3_SOCIALFANS_ACTIVE')) {
			if (class_exists('ESSBSocialFollowersCounter')) {
				essb_followers_counter()->settle_immediate_update();
			}
		}

	}
	
	public function update_optons() {
		global $essb_navigation_tabs, $essb_sidebar_sections, $essb_section_options;
		
		$current_options = get_option(ESSB3_OPTIONS_NAME);
		if (!is_array($current_options)) { $current_options = array(); }
		
		$current_tab = isset($_REQUEST['tab']) ? $_REQUEST['tab'] : '';
		$user_options = isset($_REQUEST['essb_options']) ? $_REQUEST['essb_options'] : array();
		
		$reset_settings = isset($_REQUEST['reset_settings']) ? $_REQUEST['reset_settings'] : '';
		
		//print_r($user_options);
		
		if ($current_tab == '') { return; }
		
		if ($current_tab == 'advanced') {
			$this->temporary_activate_post_type_settings();
		}
		
		if ($current_tab == "display") {
			$this->temporary_activate_positions_by_posttypes();
		}
		
		$options = $essb_section_options[$current_tab];
		
		foreach($options as $section => $fields) {
			$section_options = $fields;
			
			foreach ($section_options as $option) {
				$type = $option['type'];
				$id = isset($option['id']) ? $option['id'] : '';
				
				if ($id == '') { continue; }
				
				if (strpos($id, 'essb3fans_') !== false) { continue; }
				
				// custom ID parser for functions
				if ($id == 'essb3_options_template_select') {
					$id = 'style';
				}
				
				if ($id == 'essb3_network_selection') {
					$type = "network_select";
				}
				if ($id == "essb3_network_rename") {
					$type = "network_rename";
				}
				if ($id == "essb3_post_type_select") {
					$id = "display_in_types";
				}
				if ($id == "essb3_esml_post_type_select") {
					$id = "esml_monitor_types";
				}
				
				if ($id == 'essb3_network_selection' && defined('ESSB3_LIGHTMODE')) {
					$twitteruser =  isset($user_options['twitteruser']) ? $user_options['twitteruser'] : '';
					$current_options['twitteruser'] = $twitteruser;

					$twitterhashtags =  isset($user_options['twitterhashtags']) ? $user_options['twitterhashtags'] : '';
					$current_options['twitterhashtags'] = $twitterhashtags;
				}
				
				// quick setup options
				if ($id == "quick_setup_recommended") {
					$current_options['twitter_shareshort'] = 'true';
					$current_options['twitter_shareshort_service'] = 'wp';
					$current_options['twitter_message_optimize'] = 'true';
					$current_options['facebookadvanced'] = 'false';
					$current_options['buffer_twitter_user'] = 'true';
				}
				
				if ($id == "quick_setup_static") {
					$current_options['use_minified_css'] = 'true';
					$current_options['use_minified_js'] = 'true';
					$current_options['load_js_async'] = 'true';
					$current_options['load_css_footer'] = 'true';
				}
				
				if ($id == 'quick_setup_easy') {
					update_option(ESSB3_EASYMODE_NAME, 'true');
				}
				
				switch ($type) {
					case "network_rename":
						$option_value = isset($_REQUEST['essb_options_names']) ? $_REQUEST['essb_options_names'] : array();
						
						foreach ($option_value as $key => $value) {
							$network_option_value = "user_network_name_".$key;
							$current_options[$network_option_value] = $value;
						}
						
						break;
					case "network_select":
						$option_value = isset($user_options['networks']) ? $user_options['networks'] : array();
						$current_options['networks'] = $option_value;
						$option_value = isset($user_options['networks_order']) ? $user_options['networks_order'] : array();
						$current_options['networks_order'] = $option_value;
						break;
					case "checkbox_list_sortable":
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';
						$current_options[$id] = $option_value;
						
						$option_value = isset($user_options[$id.'_order']) ? $user_options[$id.'_order'] : '';
						$current_options[$id.'_order'] = $option_value;
						break;
					default:
						$option_value = isset($user_options[$id]) ? $user_options[$id] : '';
						$current_options[$id] = $option_value;
						
						if ($id == "stats_active") {
							if ($option_value == "true") {
								ESSBSocialShareAnalyticsBackEnd::install();
							}
						}
						
						break;
				}
			}
		}
		
		$current_options = $this->clean_blank_values($current_options);
		
		// initially reset plugin settings to default one
		if ($reset_settings == 'true') {
			$current_options = array();
			
			$default_options = 'eyJidXR0b25fc3R5bGUiOiJidXR0b24iLCJzdHlsZSI6IjIyIiwiY3NzX2FuaW1hdGlvbnMiOiJubyIsImZ1bGx3aWR0aF9zaGFyZV9idXR0b25zX2NvbHVtbnMiOiIxIiwibmV0d29ya3MiOlsiZmFjZWJvb2siLCJ0d2l0dGVyIiwiZ29vZ2xlIiwicGludGVyZXN0IiwibGlua2VkaW4iXSwibmV0d29ya3Nfb3JkZXIiOlsiZmFjZWJvb2siLCJ0d2l0dGVyIiwiZ29vZ2xlIiwicGludGVyZXN0IiwibGlua2VkaW4iLCJkaWdnIiwiZGVsIiwic3R1bWJsZXVwb24iLCJ0dW1ibHIiLCJ2ayIsInByaW50IiwibWFpbCIsImZsYXR0ciIsInJlZGRpdCIsImJ1ZmZlciIsImxvdmUiLCJ3ZWlibyIsInBvY2tldCIsInhpbmciLCJvayIsIm13cCIsIm1vcmUiLCJ3aGF0c2FwcCIsIm1lbmVhbWUiLCJibG9nZ2VyIiwiYW1hem9uIiwieWFob29tYWlsIiwiZ21haWwiLCJhb2wiLCJuZXdzdmluZSIsImhhY2tlcm5ld3MiLCJldmVybm90ZSIsIm15c3BhY2UiLCJtYWlscnUiLCJ2aWFkZW8iLCJsaW5lIiwiZmxpcGJvYXJkIiwiY29tbWVudHMiLCJ5dW1tbHkiXSwibW9yZV9idXR0b25fZnVuYyI6IjEiLCJtb3JlX2J1dHRvbl9pY29uIjoicGx1cyIsInR3aXR0ZXJfc2hhcmVzaG9ydF9zZXJ2aWNlIjoid3AiLCJtYWlsX2Z1bmN0aW9uIjoiZm9ybSIsIndoYXRzYXBwX3NoYXJlc2hvcnRfc2VydmljZSI6IndwIiwiZmxhdHRyX2xhbmciOiJzcV9BTCIsImNvdW50ZXJfcG9zIjoibGVmdCIsImZvcmNlX2NvdW50ZXJzX2FkbWluX3R5cGUiOiJ3cCIsInRvdGFsX2NvdW50ZXJfcG9zIjoicmlnaHQiLCJ1c2VyX25ldHdvcmtfbmFtZV9mYWNlYm9vayI6IkZhY2Vib29rIiwidXNlcl9uZXR3b3JrX25hbWVfdHdpdHRlciI6IlR3aXR0ZXIiLCJ1c2VyX25ldHdvcmtfbmFtZV9nb29nbGUiOiJHb29nbGUrIiwidXNlcl9uZXR3b3JrX25hbWVfcGludGVyZXN0IjoiUGludGVyZXN0IiwidXNlcl9uZXR3b3JrX25hbWVfbGlua2VkaW4iOiJMaW5rZWRJbiIsInVzZXJfbmV0d29ya19uYW1lX2RpZ2ciOiJEaWdnIiwidXNlcl9uZXR3b3JrX25hbWVfZGVsIjoiRGVsIiwidXNlcl9uZXR3b3JrX25hbWVfc3R1bWJsZXVwb24iOiJTdHVtYmxlVXBvbiIsInVzZXJfbmV0d29ya19uYW1lX3R1bWJsciI6IlR1bWJsciIsInVzZXJfbmV0d29ya19uYW1lX3ZrIjoiVktvbnRha3RlIiwidXNlcl9uZXR3b3JrX25hbWVfcHJpbnQiOiJQcmludCIsInVzZXJfbmV0d29ya19uYW1lX21haWwiOiJFbWFpbCIsInVzZXJfbmV0d29ya19uYW1lX2ZsYXR0ciI6IkZsYXR0ciIsInVzZXJfbmV0d29ya19uYW1lX3JlZGRpdCI6IlJlZGRpdCIsInVzZXJfbmV0d29ya19uYW1lX2J1ZmZlciI6IkJ1ZmZlciIsInVzZXJfbmV0d29ya19uYW1lX2xvdmUiOiJMb3ZlIFRoaXMiLCJ1c2VyX25ldHdvcmtfbmFtZV93ZWlibyI6IldlaWJvIiwidXNlcl9uZXR3b3JrX25hbWVfcG9ja2V0IjoiUG9ja2V0IiwidXNlcl9uZXR3b3JrX25hbWVfeGluZyI6IlhpbmciLCJ1c2VyX25ldHdvcmtfbmFtZV9vayI6Ik9kbm9rbGFzc25pa2kiLCJ1c2VyX25ldHdvcmtfbmFtZV9td3AiOiJNYW5hZ2VXUC5vcmciLCJ1c2VyX25ldHdvcmtfbmFtZV9tb3JlIjoiTW9yZSBCdXR0b24iLCJ1c2VyX25ldHdvcmtfbmFtZV93aGF0c2FwcCI6IldoYXRzQXBwIiwidXNlcl9uZXR3b3JrX25hbWVfbWVuZWFtZSI6Ik1lbmVhbWUiLCJ1c2VyX25ldHdvcmtfbmFtZV9ibG9nZ2VyIjoiQmxvZ2dlciIsInVzZXJfbmV0d29ya19uYW1lX2FtYXpvbiI6IkFtYXpvbiIsInVzZXJfbmV0d29ya19uYW1lX3lhaG9vbWFpbCI6IllhaG9vIE1haWwiLCJ1c2VyX25ldHdvcmtfbmFtZV9nbWFpbCI6IkdtYWlsIiwidXNlcl9uZXR3b3JrX25hbWVfYW9sIjoiQU9MIiwidXNlcl9uZXR3b3JrX25hbWVfbmV3c3ZpbmUiOiJOZXdzdmluZSIsInVzZXJfbmV0d29ya19uYW1lX2hhY2tlcm5ld3MiOiJIYWNrZXJOZXdzIiwidXNlcl9uZXR3b3JrX25hbWVfZXZlcm5vdGUiOiJFdmVybm90ZSIsInVzZXJfbmV0d29ya19uYW1lX215c3BhY2UiOiJNeVNwYWNlIiwidXNlcl9uZXR3b3JrX25hbWVfbWFpbHJ1IjoiTWFpbC5ydSIsInVzZXJfbmV0d29ya19uYW1lX3ZpYWRlbyI6IlZpYWRlbyIsInVzZXJfbmV0d29ya19uYW1lX2xpbmUiOiJMaW5lIiwidXNlcl9uZXR3b3JrX25hbWVfZmxpcGJvYXJkIjoiRmxpcGJvYXJkIiwidXNlcl9uZXR3b3JrX25hbWVfY29tbWVudHMiOiJDb21tZW50cyIsInVzZXJfbmV0d29ya19uYW1lX3l1bW1seSI6Ill1bW1seSIsImdhX3RyYWNraW5nX21vZGUiOiJzaW1wbGUiLCJ0d2l0dGVyX2NhcmRfdHlwZSI6InN1bW1hcnkiLCJuYXRpdmVfb3JkZXIiOlsiZ29vZ2xlIiwidHdpdHRlciIsImZhY2Vib29rIiwibGlua2VkaW4iLCJwaW50ZXJlc3QiLCJ5b3V0dWJlIiwibWFuYWdld3AiLCJ2ayJdLCJmYWNlYm9va19saWtlX3R5cGUiOiJsaWtlIiwiZ29vZ2xlX2xpa2VfdHlwZSI6InBsdXMiLCJ0d2l0dGVyX3R3ZWV0IjoiZm9sbG93IiwicGludGVyZXN0X25hdGl2ZV90eXBlIjoiZm9sbG93Iiwic2tpbl9uYXRpdmVfc2tpbiI6ImZsYXQiLCJwcm9maWxlc19idXR0b25fdHlwZSI6InNxdWFyZSIsInByb2ZpbGVzX2J1dHRvbl9maWxsIjoiZmlsbCIsInByb2ZpbGVzX2J1dHRvbl9zaXplIjoic21hbGwiLCJwcm9maWxlc19kaXNwbGF5X3Bvc2l0aW9uIjoibGVmdCIsInByb2ZpbGVzX29yZGVyIjpbInR3aXR0ZXIiLCJmYWNlYm9vayIsImdvb2dsZSIsInBpbnRlcmVzdCIsImZvdXJzcXVhcmUiLCJ5YWhvbyIsInNreXBlIiwieWVscCIsImZlZWRidXJuZXIiLCJsaW5rZWRpbiIsInZpYWRlbyIsInhpbmciLCJteXNwYWNlIiwic291bmRjbG91ZCIsInNwb3RpZnkiLCJncm9vdmVzaGFyayIsImxhc3RmbSIsInlvdXR1YmUiLCJ2aW1lbyIsImRhaWx5bW90aW9uIiwidmluZSIsImZsaWNrciIsIjUwMHB4IiwiaW5zdGFncmFtIiwid29yZHByZXNzIiwidHVtYmxyIiwiYmxvZ2dlciIsInRlY2hub3JhdGkiLCJyZWRkaXQiLCJkcmliYmJsZSIsInN0dW1ibGV1cG9uIiwiZGlnZyIsImVudmF0byIsImJlaGFuY2UiLCJkZWxpY2lvdXMiLCJkZXZpYW50YXJ0IiwiZm9ycnN0IiwicGxheSIsInplcnBseSIsIndpa2lwZWRpYSIsImFwcGxlIiwiZmxhdHRyIiwiZ2l0aHViIiwiY2hpbWVpbiIsImZyaWVuZGZlZWQiLCJuZXdzdmluZSIsImlkZW50aWNhIiwiYmVibyIsInp5bmdhIiwic3RlYW0iLCJ4Ym94Iiwid2luZG93cyIsIm91dGxvb2siLCJjb2RlcndhbGwiLCJ0cmlwYWR2aXNvciIsImFwcG5ldCIsImdvb2RyZWFkcyIsInRyaXBpdCIsImxhbnlyZCIsInNsaWRlc2hhcmUiLCJidWZmZXIiLCJyc3MiLCJ2a29udGFrdGUiLCJkaXNxdXMiLCJob3V6eiIsIm1haWwiLCJwYXRyZW9uIiwicGF5cGFsIiwicGxheXN0YXRpb24iLCJzbXVnbXVnIiwic3dhcm0iLCJ0cmlwbGVqIiwieWFtbWVyIiwic3RhY2tvdmVyZmxvdyIsImRydXBhbCIsIm9kbm9rbGFzc25pa2kiLCJhbmRyb2lkIiwibWVldHVwIiwicGVyc29uYSJdLCJhZnRlcmNsb3NlX3R5cGUiOiJmb2xsb3ciLCJhZnRlcmNsb3NlX2xpa2VfY29scyI6Im9uZWNvbCIsImVzbWxfdHRsIjoiMSIsImVzbWxfcHJvdmlkZXIiOiJzaGFyZWRjb3VudCIsImVzbWxfYWNjZXNzIjoibWFuYWdlX29wdGlvbnMiLCJzaG9ydHVybF90eXBlIjoid3AiLCJkaXNwbGF5X2luX3R5cGVzIjpbInBvc3QiXSwiZGlzcGxheV9leGNlcnB0X3BvcyI6InRvcCIsInRvcGJhcl9idXR0b25zX2FsaWduIjoibGVmdCIsInRvcGJhcl9jb250ZW50YXJlYV9wb3MiOiJsZWZ0IiwiYm90dG9tYmFyX2J1dHRvbnNfYWxpZ24iOiJsZWZ0IiwiYm90dG9tYmFyX2NvbnRlbnRhcmVhX3BvcyI6ImxlZnQiLCJmbHlpbl9wb3NpdGlvbiI6InJpZ2h0Iiwic2lzX25ldHdvcmtfb3JkZXIiOlsiZmFjZWJvb2siLCJ0d2l0dGVyIiwiZ29vZ2xlIiwibGlua2VkaW4iLCJwaW50ZXJlc3QiLCJ0dW1ibHIiLCJyZWRkaXQiLCJkaWdnIiwiZGVsaWNpb3VzIiwidmtvbnRha3RlIiwib2Rub2tsYXNzbmlraSJdLCJzaXNfc3R5bGUiOiJmbGF0LXNtYWxsIiwic2lzX2FsaWduX3giOiJsZWZ0Iiwic2lzX2FsaWduX3kiOiJ0b3AiLCJzaXNfb3JpZW50YXRpb24iOiJob3Jpem9udGFsIiwibW9iaWxlX3NoYXJlYnV0dG9uc2Jhcl9jb3VudCI6IjIiLCJzaGFyZWJhcl9jb3VudGVyX3BvcyI6Imluc2lkZSIsInNoYXJlYmFyX3RvdGFsX2NvdW50ZXJfcG9zIjoiYmVmb3JlIiwic2hhcmViYXJfbmV0d29ya3Nfb3JkZXIiOlsiZmFjZWJvb2t8RmFjZWJvb2siLCJ0d2l0dGVyfFR3aXR0ZXIiLCJnb29nbGV8R29vZ2xlKyIsInBpbnRlcmVzdHxQaW50ZXJlc3QiLCJsaW5rZWRpbnxMaW5rZWRJbiIsImRpZ2d8RGlnZyIsImRlbHxEZWwiLCJzdHVtYmxldXBvbnxTdHVtYmxlVXBvbiIsInR1bWJscnxUdW1ibHIiLCJ2a3xWS29udGFrdGUiLCJwcmludHxQcmludCIsIm1haWx8RW1haWwiLCJmbGF0dHJ8RmxhdHRyIiwicmVkZGl0fFJlZGRpdCIsImJ1ZmZlcnxCdWZmZXIiLCJsb3ZlfExvdmUgVGhpcyIsIndlaWJvfFdlaWJvIiwicG9ja2V0fFBvY2tldCIsInhpbmd8WGluZyIsIm9rfE9kbm9rbGFzc25pa2kiLCJtd3B8TWFuYWdlV1Aub3JnIiwibW9yZXxNb3JlIEJ1dHRvbiIsIndoYXRzYXBwfFdoYXRzQXBwIiwibWVuZWFtZXxNZW5lYW1lIiwiYmxvZ2dlcnxCbG9nZ2VyIiwiYW1hem9ufEFtYXpvbiIsInlhaG9vbWFpbHxZYWhvbyBNYWlsIiwiZ21haWx8R21haWwiLCJhb2x8QU9MIiwibmV3c3ZpbmV8TmV3c3ZpbmUiLCJoYWNrZXJuZXdzfEhhY2tlck5ld3MiLCJldmVybm90ZXxFdmVybm90ZSIsIm15c3BhY2V8TXlTcGFjZSIsIm1haWxydXxNYWlsLnJ1IiwidmlhZGVvfFZpYWRlbyIsImxpbmV8TGluZSIsImZsaXBib2FyZHxGbGlwYm9hcmQiLCJjb21tZW50c3xDb21tZW50cyIsInl1bW1seXxZdW1tbHkiXSwic2hhcmVwb2ludF9jb3VudGVyX3BvcyI6Imluc2lkZSIsInNoYXJlcG9pbnRfdG90YWxfY291bnRlcl9wb3MiOiJiZWZvcmUiLCJzaGFyZXBvaW50X25ldHdvcmtzX29yZGVyIjpbImZhY2Vib29rfEZhY2Vib29rIiwidHdpdHRlcnxUd2l0dGVyIiwiZ29vZ2xlfEdvb2dsZSsiLCJwaW50ZXJlc3R8UGludGVyZXN0IiwibGlua2VkaW58TGlua2VkSW4iLCJkaWdnfERpZ2ciLCJkZWx8RGVsIiwic3R1bWJsZXVwb258U3R1bWJsZVVwb24iLCJ0dW1ibHJ8VHVtYmxyIiwidmt8VktvbnRha3RlIiwicHJpbnR8UHJpbnQiLCJtYWlsfEVtYWlsIiwiZmxhdHRyfEZsYXR0ciIsInJlZGRpdHxSZWRkaXQiLCJidWZmZXJ8QnVmZmVyIiwibG92ZXxMb3ZlIFRoaXMiLCJ3ZWlib3xXZWlibyIsInBvY2tldHxQb2NrZXQiLCJ4aW5nfFhpbmciLCJva3xPZG5va2xhc3NuaWtpIiwibXdwfE1hbmFnZVdQLm9yZyIsIm1vcmV8TW9yZSBCdXR0b24iLCJ3aGF0c2FwcHxXaGF0c0FwcCIsIm1lbmVhbWV8TWVuZWFtZSIsImJsb2dnZXJ8QmxvZ2dlciIsImFtYXpvbnxBbWF6b24iLCJ5YWhvb21haWx8WWFob28gTWFpbCIsImdtYWlsfEdtYWlsIiwiYW9sfEFPTCIsIm5ld3N2aW5lfE5ld3N2aW5lIiwiaGFja2VybmV3c3xIYWNrZXJOZXdzIiwiZXZlcm5vdGV8RXZlcm5vdGUiLCJteXNwYWNlfE15U3BhY2UiLCJtYWlscnV8TWFpbC5ydSIsInZpYWRlb3xWaWFkZW8iLCJsaW5lfExpbmUiLCJmbGlwYm9hcmR8RmxpcGJvYXJkIiwiY29tbWVudHN8Q29tbWVudHMiLCJ5dW1tbHl8WXVtbWx5Il0sInNoYXJlYm90dG9tX25ldHdvcmtzX29yZGVyIjpbImZhY2Vib29rfEZhY2Vib29rIiwidHdpdHRlcnxUd2l0dGVyIiwiZ29vZ2xlfEdvb2dsZSsiLCJwaW50ZXJlc3R8UGludGVyZXN0IiwibGlua2VkaW58TGlua2VkSW4iLCJkaWdnfERpZ2ciLCJkZWx8RGVsIiwic3R1bWJsZXVwb258U3R1bWJsZVVwb24iLCJ0dW1ibHJ8VHVtYmxyIiwidmt8VktvbnRha3RlIiwicHJpbnR8UHJpbnQiLCJtYWlsfEVtYWlsIiwiZmxhdHRyfEZsYXR0ciIsInJlZGRpdHxSZWRkaXQiLCJidWZmZXJ8QnVmZmVyIiwibG92ZXxMb3ZlIFRoaXMiLCJ3ZWlib3xXZWlibyIsInBvY2tldHxQb2NrZXQiLCJ4aW5nfFhpbmciLCJva3xPZG5va2xhc3NuaWtpIiwibXdwfE1hbmFnZVdQLm9yZyIsIm1vcmV8TW9yZSBCdXR0b24iLCJ3aGF0c2FwcHxXaGF0c0FwcCIsIm1lbmVhbWV8TWVuZWFtZSIsImJsb2dnZXJ8QmxvZ2dlciIsImFtYXpvbnxBbWF6b24iLCJ5YWhvb21haWx8WWFob28gTWFpbCIsImdtYWlsfEdtYWlsIiwiYW9sfEFPTCIsIm5ld3N2aW5lfE5ld3N2aW5lIiwiaGFja2VybmV3c3xIYWNrZXJOZXdzIiwiZXZlcm5vdGV8RXZlcm5vdGUiLCJteXNwYWNlfE15U3BhY2UiLCJtYWlscnV8TWFpbC5ydSIsInZpYWRlb3xWaWFkZW8iLCJsaW5lfExpbmUiLCJmbGlwYm9hcmR8RmxpcGJvYXJkIiwiY29tbWVudHN8Q29tbWVudHMiLCJ5dW1tbHl8WXVtbWx5Il0sImNvbnRlbnRfcG9zaXRpb24iOiJjb250ZW50X2JvdHRvbSIsImVzc2JfY2FjaGVfbW9kZSI6ImZ1bGwiLCJ0dXJub2ZmX2Vzc2JfYWR2YW5jZWRfYm94IjoidHJ1ZSIsImVzc2JfYWNjZXNzIjoibWFuYWdlX29wdGlvbnMiLCJhcHBseV9jbGVhbl9idXR0b25zX21ldGhvZCI6ImRlZmF1bHQifQ==';
				
			$options_base = ESSB_Manager::convert_ready_made_option($default_options);
			//print_r($options_base);
			if ($options_base) {
				$current_options = $options_base;
			}
		}
		update_option(ESSB3_OPTIONS_NAME, $current_options);
		
		$esml_active = ESSBOptionValuesHelper::options_bool_value($current_options, 'esml_active');
		if (!$esml_active) {
			delete_option ( "esml_version" );
			$this->removeAllQueuedMetricsUpdates();
		}
	}
	
	function temporary_activate_positions_by_posttypes() {
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
	
	
	function temporary_activate_post_type_settings() {
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
		
		$key = 1;
		$cpt = 'woocommerce';
		$cpt_title = 'WooCommerce';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, ESSB3_TEXT_DOMAIN ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;
		
		$cpt = 'wpecommerce';
		$cpt_title = 'WP e-Commerce';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, ESSB3_TEXT_DOMAIN ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;
		
		$cpt = 'jigoshop';
		$cpt_title = 'JigoShop';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, ESSB3_TEXT_DOMAIN ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;
		
		$cpt = 'ithemes';
		$cpt_title = 'iThemes Exchange';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, ESSB3_TEXT_DOMAIN ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;
		
		$cpt = 'bbpress';
		$cpt_title = 'bbPress';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, ESSB3_TEXT_DOMAIN ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;
		
		$cpt = 'buddypress';
		$cpt_title = 'BuddyPress';
		ESSBOptionsStructureHelper::submenu_item ( 'advanced', 'advancedmodule-' . $key, $cpt_title );
		ESSBOptionsStructureHelper::field_heading ( 'advanced', 'advancedmodule-' . $key, 'heading1', __ ( 'Advanced settings for plugin: ' . $cpt_title, ESSB3_TEXT_DOMAIN ) );
		essb_prepare_location_advanced_customization ( 'advanced', 'advancedmodule-' . $key, 'post-type-' . $cpt, true );
		$key ++;
	}
	
	function clean_blank_values($object) {
		foreach ($object as $key => $value) {
			if (!is_array($value)) {
				$value = trim($value);
				
				if (empty($value)) {
					unset($object[$key]);
				}
			}
			else {
				if (count($value) == 0) {
					unset($object[$key]);
				}
			}
		}
		
		return $object;
	}
	
	function getval ($from, $what, $default=false) {
		if (is_object($from) && isset($from->$what)) return $from->$what;
		else if (is_array($from) && isset($from[$what])) return $from[$what];
		else return $default;
	}
	
	public function removeAllQueuedMetricsUpdates() {
		$crons = _get_cron_array();
		if ( !empty( $crons ) ) {
			foreach( $crons as $timestamp => $cron ) {
				// Remove single post updates
				if ( ! empty( $cron['easy_social_metrics_update_single_post'] ) )  {
					unset( $crons[$timestamp]['easy_social_metrics_update_single_post'] );
				}
	
				// Remove full post updates
				if ( ! empty( $cron['easy_social_metrics_lite_automatic_update'] ) )  {
					unset( $crons[$timestamp]['easy_social_metrics_lite_automatic_update'] );
				}
			}
			_set_cron_array( $crons );
				
			wp_clear_scheduled_hook('easy_social_metrics_update_single_post');
			wp_clear_scheduled_hook('easy_social_metrics_lite_automatic_update');
		}
	
		return;
	} // end removeAllQueuedUpdates()
	
	public function actions_download_settings() {
		global $essb_options;
		
		$backup_string = json_encode($essb_options);
		ignore_user_abort( true );
		nocache_headers();
		header('Content-disposition: attachment; filename=essb3-options-' . date( 'm-d-Y' ) . '.json');
		header('Content-type: application/json');
		header("Expires: 0" );
		echo $backup_string;
		exit;
	}
}

?>