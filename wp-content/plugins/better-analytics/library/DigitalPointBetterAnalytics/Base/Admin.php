<?php

class DigitalPointBetterAnalytics_Base_Admin
{
	protected static $_instance;

	/**
	 * Protected constructor. Use {@link getInstance()} instead.
	 */
	protected function __construct()
	{
	}

	public static final function getInstance()
	{
		if (!self::$_instance)
		{
			$class = __CLASS__;
			self::$_instance = new $class;

			self::$_instance->_initHooks();
		}

		return self::$_instance;
	}

	protected function _initHooks()
	{
		add_action('admin_init', array($this, 'admin_init'), 20);
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('network_admin_menu', array($this, 'network_admin_menu'));

		add_action('admin_head', array($this, 'admin_head'));

		add_action('wp_dashboard_setup', array($this, 'dashboard_setup'));

		add_action('wp_ajax_better-analytics_heatmaps', array($this, 'display_page'));
		add_action('wp_ajax_better-analytics_area_charts', array($this, 'display_page'));
		add_action('wp_ajax_better-analytics_monitor', array($this, 'display_page'));
		add_action('wp_ajax_better-analytics_events', array($this, 'display_page'));

		add_action('wp_ajax_better-analytics_charts', array($this, 'display_charts'));

		add_filter('plugin_action_links', array($this, 'plugin_action_links' ), 10, 2);
		add_filter('wp_redirect', array($this, 'filter_redirect'));
		add_filter('admin_footer_text', array($this, 'admin_footer_text' ));

		add_filter('all_plugins', array($this, 'all_plugins'));
		add_filter('plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

		add_filter('wpmu_options', array($this, 'show_network_settings'));
		add_action('update_wpmu_options', array($this, 'save_network_settings'));

		add_filter('removable_query_args', array($this, 'removable_query_args'));

		$betterAnalyticsOptions = get_option('better_analytics');
		if (!$betterAnalyticsOptions['property_id'])
		{
			add_action('admin_notices', array($this, 'not_configured' ) );
		}

		if (strpos(@$_SERVER['REQUEST_URI'], 'page=better-analytics') !== false || @!$betterAnalyticsOptions['hide_api_message'])
		{
			if (!get_site_option('ba_site_tokens') && !get_option('ba_tokens'))
			{
				add_action('admin_notices', array($this, 'cant_auto_configure' ) );
			}
			elseif (!$betterAnalyticsOptions['api']['profile'])
			{
				add_action('admin_notices', array($this, 'can_auto_configure' ) );
			}
		}

		if (get_transient('ba_last_error'))
		{
			add_action('admin_notices', array($this, 'last_error' ) );
		}
	}

	public function admin_init()
	{
		register_setting('better-analytics-group', 'better_analytics', array($this, 'sanitize_settings'));

		// allows us to use a redirect on the better_analytics-auth page
		ob_start();
	}


	public function admin_menu()
	{
		$betterAnalyticsOptions = get_option('better_analytics');
		$currentUser = wp_get_current_user();
		$canViewSettings = (empty($betterAnalyticsOptions['lock_settings_user_id']) || $betterAnalyticsOptions['lock_settings_user_id'] == $currentUser->ID);

		add_management_page( esc_html__('Test Analytics Setup', 'better-analytics'), esc_html__('Test Analytics Setup', 'better-analytics'), 'manage_options', 'better-analytics_test', array($this, 'display_test_page') );
		add_management_page( esc_html__('OAuth2 Endpoint', 'better-analytics'), esc_html__('OAuth2 Endpoint', 'better-analytics'), 'manage_options', 'better-analytics_auth', array($this, 'api_authentication') );

		if (DigitalPointBetterAnalytics_Base_Public::getInstance()->canViewReports())
		{
			add_menu_page(esc_html__('Analytics', 'better-analytics'), esc_html__('Analytics', 'better-analytics'), 'read', 'better-analytics_heatmaps', null, 'dashicons-chart-line', 3.1975123 );
			add_submenu_page( 'better-analytics_heatmaps', esc_html__('Heat Maps', 'better-analytics'), esc_html__('Reports', 'better-analytics'), 'read', 'better-analytics_heatmaps', array($this, 'display_page') );

			add_submenu_page( 'better-analytics_heatmaps', esc_html__('Charts', 'better-analytics'), esc_html__('Charts', 'better-analytics'), 'read', 'better-analytics_areacharts', array($this, 'display_page') );
			add_submenu_page( 'better-analytics_heatmaps', esc_html__('Issue Monitor', 'better-analytics'), esc_html__('Issue Monitor', 'better-analytics'), 'read', 'better-analytics_monitor', array($this, 'display_page') );
			add_submenu_page( 'better-analytics_heatmaps', esc_html__('Events', 'better-analytics'), esc_html__('Events', 'better-analytics'), 'read', 'better-analytics_events', array($this, 'display_page') );
		}

		add_submenu_page( 'better-analytics_heatmaps', esc_html__('Goals', 'better-analytics'), esc_html__('Goals', 'better-analytics'), 'manage_options', 'better-analytics_goals', array($this, 'display_page') );
		add_submenu_page( 'better-analytics_heatmaps', esc_html__('A/B Testing', 'better-analytics'), esc_html__('A/B Testing', 'better-analytics'), 'manage_options', 'better-analytics_experiments', array($this, 'display_page') );

		if ($canViewSettings)
		{
			add_submenu_page('better-analytics_heatmaps', esc_html__('Settings', 'better-analytics'), esc_html__('Settings', 'better-analytics'), 'manage_options', 'options-general.php' . '?page=better-analytics');
		}

		add_submenu_page( 'better-analytics_heatmaps', esc_html__('Test Setup', 'better-analytics'), esc_html__('Test Setup', 'better-analytics'), 'manage_options', 'tools.php' . '?page=better-analytics_test' );

		if ($canViewSettings)
		{
			$hook = add_options_page( esc_html__('Better Analytics', 'better-analytics'), esc_html__('Better Analytics', 'better-analytics'), 'manage_options', 'better-analytics', array($this, 'display_configuration_page'));
			add_action( "load-$hook", array($this, 'admin_help'));
		}

	}

	public function network_admin_menu()
	{
		add_submenu_page( 'settings.php', esc_html__('OAuth2 Endpoint', 'better-analytics'), esc_html__('OAuth2 Endpoint', 'better-analytics'), 'manage_network', 'better-analytics_auth', array($this, 'api_authentication') );
	}


	public function plugin_action_links( $links, $file)
	{
		if ($file == plugin_basename(BETTER_ANALYTICS_PLUGIN_DIR . '/better-analytics.php'))
		{
			$betterAnalyticsInternal = get_transient('ba_int');

			wp_enqueue_style('better_analytics_admin_css', BETTER_ANALYTICS_PLUGIN_URL . 'assets/digitalpoint/css/admin.css', array(), BETTER_ANALYTICS_VERSION);

			$links['settings'] = '<a href="' . esc_url(menu_page_url('better-analytics', false)) . '">' . esc_html__('Settings' , 'better-analytics').'</a>';

			krsort($links);
			end($links);
			$key = key($links);
			$links[$key] .= '<p class="' . (DigitalPointBetterAnalytics_Base_Pro::$installed && @$betterAnalyticsInternal['v'] && @$betterAnalyticsInternal['l'] == DigitalPointBetterAnalytics_Base_Pro::$version ? 'green' : 'orange') . '"> ' .
				(DigitalPointBetterAnalytics_Base_Pro::$installed ?
					(@$betterAnalyticsInternal['v'] ?
						(@$betterAnalyticsInternal['l'] != DigitalPointBetterAnalytics_Base_Pro::$version ?
							sprintf('<a href="%1$s" target="_blank">%2$s</a><br />%3$s %4$s<br />%5$s %6$s',
								esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_plugins&utm_medium=wordpress&utm_campaign=plugin'),
								esc_html__('Pro version not up to date.', 'better-analytics'),
								esc_html__('Installed:', 'better-analytics'),
								DigitalPointBetterAnalytics_Base_Pro::$version,
								esc_html__('Latest:', 'better-analytics'),
								@$betterAnalyticsInternal['l']
							) :
							sprintf('<a href="%1$s" target="_blank">%2$s</a> (%3$s)',
								esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_plugins&utm_medium=wordpress&utm_campaign=plugin'),
								esc_html__('Pro version installed', 'better-analytics'),
								@$betterAnalyticsInternal['l']
							)
						) :
						sprintf(esc_html__('Pro version installed, but not active.  Did you %1$sverify ownership of your domain%2$s?', 'better-analytics'),
							'<a href="' . esc_url('https://forums.digitalpoint.com/marketplace/domain-verification#utm_source=admin_plugins&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">',
							'</a>'
						)
					) :
					sprintf('<a href="%1$s" target="_blank">%2$s</a>',
						esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_plugins&utm_medium=wordpress&utm_campaign=plugin'),
						esc_html__('Pro version not installed.', 'better-analytics')
					)
				) .
				'</p>';
		}

		return $links;
	}

	public function admin_head()
	{
		remove_submenu_page('tools.php', 'better-analytics_auth');
		remove_submenu_page('settings.php', 'better-analytics_auth');

		$_reportingPages = array(
			'better-analytics_heatmaps',
			'better-analytics_areacharts',
			'better-analytics_monitor',
			'better-analytics_events'
		);

		$currentPage = (empty($GLOBALS['plugin_page']) || array_search($GLOBALS['plugin_page'], $_reportingPages) === false ? $_reportingPages[0] : $GLOBALS['plugin_page']);

		foreach($_reportingPages as $page)
		{
			if ($currentPage != $page)
			{
				remove_submenu_page( 'better-analytics_heatmaps', $page);
			}
		}
		
		$betterAnalyticsOptions = get_option('better_analytics');
		$currentUser = wp_get_current_user();

		if (@$betterAnalyticsOptions['javascript']['use_in_admin'] && !array_intersect((array)$currentUser->roles, (array)@$betterAnalyticsOptions['roles_no_track']))
		{
			DigitalPointBetterAnalytics_Base_Public::getInstance()->insert_code();
			include(BETTER_ANALYTICS_PLUGIN_DIR . 'js/universal.php');
		}
	}

	public function dashboard_setup()
	{
		if (DigitalPointBetterAnalytics_Base_Public::getInstance()->canViewReports())
		{
			wp_add_dashboard_widget(
				'better-analytics',
				esc_html__('Better Analytics', 'better-analytics'),
				array($this, 'dashboard_display')
			);
		}
	}

	public function dashboard_display()
	{
		$this->view('dashboard');
	}

	public function filter_redirect($location)
	{
		// Kind of a janky way to redirect back to the right tab... boo.
		if (strpos($location, '/wp-admin/options-general.php?page=better-analytics&settings-updated=true') !== false && !empty($_POST['current_tab']))
		{
			$location .= '#top#' . $_POST['current_tab'];
		}

		return $location;
	}

	public function not_configured()
	{
		$this->_displayError(
			sprintf('%1$s<p><a href="%2$s" class="button button-primary">%3$s</a></p>', esc_html__('Google Analytics Web Property ID not selected.', 'better-analytics'), esc_url(menu_page_url('better-analytics', false)), esc_html__('Settings', 'better-analytics'))
		);
	}

	public function cant_auto_configure()
	{
		$this->_displayError(
			sprintf('%1$s<p><a href="%2$s" class="button button-primary">%3$s</a></p>', esc_html__('Google Analytics account not linked for API functions.', 'better-analytics'), esc_url(menu_page_url('better-analytics', false) . '#top#api'), esc_html__('API Settings', 'better-analytics'))
		);
	}

	public function can_auto_configure()
	{
		$this->_displayError(
			sprintf('%1$s<p><a href="%2$s" class="button button-primary">%3$s</a></p>', esc_html__('Google Analytics account not yet configured.', 'better-analytics'), esc_url(menu_page_url('better-analytics_test', false)), esc_html__('Test Setup / Auto-Configure', 'better-analytics'))
		);
	}

	public function last_error()
	{
		$this->_displayError(sprintf('<strong>%1$s</strong><br /><br />%2$s', esc_html__('Last Analytics Error:'), get_transient('ba_last_error')));
	}

	protected function _displayError($error)
	{
		echo '<div class="error"><p>' . $error . '</p></div>';

	}


	public function display_configuration_page()
	{
		$this->view('config');
	}

	public function display_test_page()
	{
		$this->view('test');
	}

	public function display_page()
	{
		if (DigitalPointBetterAnalytics_Base_Public::getInstance()->canViewReports())
		{
			global $plugin_page;

			$method = 'action' . ucwords(strtolower(preg_replace('#[^a-z0-9]#i', '', substr($plugin_page ? $plugin_page : @$_REQUEST['action'], 17))));

			$controller = $this->_getController();
			if (method_exists($controller, $method))
			{
				$this->_getController()->$method();
			}
			else
			{
				echo sprintf('%1$s %2$s', esc_html__('Invalid method:', 'better-analytics'), $method);
			}
		}
	}


	public function display_charts()
	{
		if (DigitalPointBetterAnalytics_Base_Public::getInstance()->canViewReports())
		{
			$this->_getController()->actionCharts();
		}
	}

	public function removable_query_args($args)
	{
		if (strpos(@$_SERVER['REQUEST_URI'], 'page=better-analytics') !== false && (@$_REQUEST['action'] == 'start' || @$_REQUEST['action'] == 'stop' || @$_REQUEST['action'] == 'delete' || @$_REQUEST['action'] == 'activate' || @$_REQUEST['action'] == 'deactivate'))
		{
			$args[] = 'id';
			$args[] = 'action';
		}
		$args[] = '_wpnonce';
		return $args;
	}

	public function api_authentication()
	{
		if(!empty($_REQUEST['code']))
		{
			$code = $_REQUEST['code'];

			$response = DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->exchangeCodeForToken($code);

			if (!empty($response->error) && !empty($response->error_description))
			{
				echo sprintf('%1$s<br /><br /><b>%2$s</b>: %3$s', esc_html__('Invalid Google API Code:', 'better-analytics'), $response->error, $response->error_description);
				return;
			}

			if (empty($response->expires_in))
			{
				echo sprintf('%1$s:<br /><br />%2$s', esc_html__('Unknown Google API Error:', 'better-analytics'), nl2br(var_export($response, true)));
				return;
			}

			$response->expires_at = time() + $response->expires_in - 100;
			unset($response->expires_in);

			DigitalPointBetterAnalytics_Base_Public::getInstance()->updateTokens($response, is_network_admin());
			DigitalPointBetterAnalytics_CronEntry_Jobs::hour(true);

			// Checks for access
			$reportingClass = DigitalPointBetterAnalytics_Helper_Reporting::getInstance();
			$reportingClass->deleteProfileCache();
			$reportingClass->getProfiles();

			if (is_network_admin())
			{
				wp_redirect(self_admin_url('settings.php'), 302);
			}
			else
			{
				wp_redirect(menu_page_url('better-analytics', false) . '#top#api', 302);
			}
			return;
		}

		$url = menu_page_url('better-analytics_auth', false);

		// Hacky fix for WordPress bug:  https://core.trac.wordpress.org/ticket/28226
		if (strpos($url, 'wp-admin/settings.php'))
		{
			$url = str_replace('wp-admin/settings.php', 'wp-admin/network/settings.php', $url);
		}

		wp_redirect(DigitalPointBetterAnalytics_Helper_Reporting::getInstance()->getAuthenticationUrl($url, true), 302);
	}


	/**
	 * Add help to the Better Analytics page
	 *
	 * @return false if not the Better Analytics page
	 */
	public function admin_help() {
		$current_screen = get_current_screen();

		// Screen Content
		if ( current_user_can( 'manage_options' ))
		{
			//configuration page
			$current_screen->add_help_tab(
				array(
					'id'		=> 'overview',
					'title'		=> esc_html__( 'Overview' , 'better-analytics'),
					'content'	=>
						'<p><strong>' . esc_html__( 'Better Analytics' , 'better-analytics') . '</strong></p>' .
						'<p>' . esc_html__( 'At the most basic level, it will automatically add Google Analytics Universal code to your website.  It gives you the flexibility to track virtually everything about your site.  From page views to YouTube video engagement (and everything in between).' , 'better-analytics') . '</p>',
				)
			);

			$current_screen->add_help_tab(
				array(
					'id'		=> 'pro',
					'title'		=> esc_html__( 'Pro' , 'better-analytics'),
					'content'	=>
						'<p><strong>' . esc_html__( 'Pro Version' , 'better-analytics') . '</strong></p>' .
						'<p>' . esc_html__( 'There is a Pro version of this plugin that gives you a few added features.  More metrics/dimensions, more tracking options, etc.' , 'better-analytics') . '</p>' .
						''
				)
			);
		}

		// Help Sidebar
		$current_screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'For more information:' , 'better-analytics') . '</strong></p>' .
			'<p><a href="' . esc_url(BETTER_ANALYTICS_PRODUCT_URL . '#utm_source=admin_settings_help&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">'     . esc_html__( 'Info' , 'better-analytics') . '</a></p>' .
			'<p><a href="' . esc_url(BETTER_ANALYTICS_SUPPORT_URL . '#utm_source=admin_settings_help&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">' . esc_html__( 'Support' , 'better-analytics') . '</a></p>' .
			'<p><a href="' . esc_url(BETTER_ANALYTICS_PRO_PRODUCT_URL . '#utm_source=admin_settings_help&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">' . esc_html__( 'Pro' , 'better-analytics') . '</a></p>'

		);
	}


	public function admin_footer_text($footerText)
	{
		$currentScreen = get_current_screen();

		if (isset($currentScreen->id) && strpos($currentScreen->id, 'better-analytics') !== false)
		{
			$_type = array(esc_html__('colossal', 'better-analytics'), esc_html__('elephantine', 'better-analytics'), esc_html__('glorious', 'better-analytics'), esc_html__('grand', 'better-analytics'), esc_html__('huge', 'better-analytics'), esc_html__('mighty', 'better-analytics'), sprintf('<span class="tooltip" title="%1$s">%2$s</span>', esc_html__('WTF?', 'better-analytics'), esc_html__('sexy', 'better-analytics')));
			$_type = $_type[array_rand($_type)];
			if (strpos($_type, '"tooltip"') !== false)
			{
				wp_enqueue_script('tooltipster_js', esc_url(BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/js/jquery.tooltipster.min.js'), array(), BETTER_ANALYTICS_VERSION );
				wp_enqueue_style('tooltipster_css', esc_url(BETTER_ANALYTICS_PLUGIN_URL . 'assets/tooltipster/css/tooltipster.css'), array(), BETTER_ANALYTICS_VERSION);
			}

			$footerText = sprintf(esc_html__('If you like %1$s, please leave us a %2$s rating. A %3$s thank you in advance!', 'better-analytics'),
				'<strong>' . esc_html__('Better Analytics', 'better-analytics') . '</strong>',
				'<a href="https://wordpress.org/support/view/plugin-reviews/better-analytics?filter=5#postform" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
				$_type
			);
		}
		return $footerText;
	}

	public function all_plugins($plugins)
	{
		unset($plugins['better-analytics-pro/better-analytics-pro.php']);
		unset($plugins['better-analytics-ecommerce/better-analytics-ecommerce.php']);
		return $plugins;
	}

	public function plugin_row_meta($links, $file)
	{
		if ($file == plugin_basename(BETTER_ANALYTICS_PLUGIN_DIR . '/better-analytics.php'))
		{
			$links['support'] = '<a href="' . esc_url(BETTER_ANALYTICS_SUPPORT_URL ) . '" title="' . esc_attr( esc_html__( 'Visit Support Forum', 'better-analytics' ) ) . '">' . esc_html__( 'Support', 'better-analytics' ) . '</a>';

			$betterAnalyticsInternal = get_transient('ba_int');
			if (DigitalPointBetterAnalytics_Base_Pro::$installed && empty($betterAnalyticsInternal['v']))
			{
				$links['verify_domain'] = '<a href="' . esc_url('https://forums.digitalpoint.com/marketplace/domain-verification#utm_source=admin_plugins&utm_medium=wordpress&utm_campaign=plugin') . '" target="_blank">' . esc_html__( 'Verify Domain', 'better-analytics' ) . '</a>';
			}
		}

		return $links;
	}

	public function sanitize_settings($input)
	{
		if (is_array($input))
		{
			foreach($input as $name => &$item)
			{
				if (is_array($item))
				{
					foreach($item as &$subItem)
					{
						if (!is_array($subItem))
						{
							$subItem = strip_tags($subItem);
						}
					}
				}
				else
				{
					if ($name != 'extra_js' || !current_user_can('unfiltered_html'))
					{
						$item = strip_tags($item);
					}
				}
			}
		}

		return $input;
	}

	public function show_network_settings()
	{
		$this->view('config_network');
	}

	public function save_network_settings()
	{
		if (@$_POST['better_analytics']['api']['delete_tokens'])
		{
			delete_site_option('ba_site_tokens');
		}

		$options = array('api' => array());

		$options['api']['use_own'] = absint(@$_POST['better_analytics']['api']['use_own']);
		if ($options['api']['use_own'])
		{
			$options['api']['client_id'] = sanitize_text_field(@$_POST['better_analytics']['api']['client_id']);
			$options['api']['client_secret'] = sanitize_text_field(@$_POST['better_analytics']['api']['client_secret']);
		}
		else
		{
			$options['api']['client_id'] = $options['api']['client_secret'] = '';
		}

		update_site_option( 'better_analytics_site', $options);
	}


	public static function getProfilePropertyIds($profiles)
	{
		$profilesOutput = array();

		if (count($profiles) > 0)
		{
			foreach ($profiles as $profile)
			{
				if (empty($profilesOutput[$profile['webPropertyId']]))
				{
					$profilesOutput[$profile['webPropertyId']] = array($profile['websiteUrl'], $profile['name'], $profile['accountId']);
				}
			}
		}

		return $profilesOutput;
	}


	public static function groupProfiles($profiles)
	{
		$profileOptions = array();

		if (!empty($profiles))
		{
			$internalWebPropertyId = null;
			$groupName = null;
			$group = array();

			foreach ($profiles as &$profile)
			{

				if ($profile['internalWebPropertyId'] != $internalWebPropertyId)
				{
					if (!empty($groupName))
					{
						$profileOptions[$groupName] = $group;
					}
					$group = array();
					$groupName = $profile['websiteUrl'];
				}
				$group[$profile['id']] = $profile['name'];

				$internalWebPropertyId = $profile['internalWebPropertyId'];
			}
			$profileOptions[$groupName] = $group;

		}
		return $profileOptions;
	}

	public function view($name, array $args = array())
	{
		// Shouldn't happen, but sanitize anyway
		$name = preg_replace('#[^a-z0-9\/\_\-]#i' ,'', $name);

		$args = apply_filters('better_analytics_view_arguments', $args, $name);

		foreach ($args AS $key => $val)
		{
			$$key = $val;
		}

		include(BETTER_ANALYTICS_PLUGIN_DIR . 'library/DigitalPointBetterAnalytics/ViewAdmin/'. $name . '.php');
	}

	protected function _getController()
	{
		return new DigitalPointBetterAnalytics_ControllerAdmin_Analytics();
	}

}