<?php

class ESSBResourceBuilder {

	private $version = ESSB3_VERSION;
	private $class_version = "2.1";
	
	private $resource_version = ESSB3_VERSION;

	// resource builder options
	private $minified_css = false;
	private $minified_js = false;
	private $scripts_in_head = false;
	
	private $inline_css_footer = false;
	
	private $js_async = false;
	private $js_defer = false;
	private $js_head = false;
	private $js_delayed = false;
	
	// css code
	public $css_head = array();
	public $css_footer = array();
	public $css_static = array();
	public $css_static_footer = array();
	
	// javascript code
	private $js_code_head = array();
	private $js_code = array();
	private $js_code_noncachable = array();
	private $js_static = array();
	private $js_static_nonasync = array();
	private $js_static_footer = array();
	private $js_static_noasync_footer = array();
		
	private $js_social_apis = array();
	
	private $precompiled_css_queue = array();
	private $precompiled_js_queue = array();
	
	private static $instance = null;
	
	public static function get_instance() {
	
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	
		return self::$instance;
	
	} // end get_instance;
	
	
	function __construct() {
		global $essb_options;
		
		$this->inline_css_footer = ESSBOptionValuesHelper::options_bool_value($essb_options, 'load_css_footer');
		
		// dynamic CSS in footer
		$precompiled_mode = false;
		if (defined('ESSB3_PRECOMPILED_RESOURCE')) {
			$this->inline_css_footer = true;
			$precompiled_mode = true;
		}
		
		if (!$precompiled_mode) {
			if ($this->inline_css_footer) {
				add_action('wp_footer', array($this, 'generate_custom_css'), 997);
			}
			else {
				add_action('wp_head', array($this, 'generate_custom_css'));
			}
			add_action('wp_footer', array($this, 'generate_custom_footer_css'), 998);
		}
		else {
			add_action('wp_footer', array($this, 'generate_custom_css_precompiled'), 996);
		}
		
		add_action('wp_head', array($this, 'generate_custom_js'));

		if (!$precompiled_mode) {
			add_action('wp_footer', array($this, 'generate_custom_footer_js'), 998);	
		}
		else {
			add_action('wp_footer', array($this, 'generte_custom_js_precompiled'), 996);
		}
		// static CSS and javascripts sources enqueue
		add_action ( 'wp_enqueue_scripts', array ($this, 'register_front_assets' ), 10 );
		
		// initalize resource builder options based on settings
		$this->minified_css = ESSBOptionValuesHelper::options_bool_value($essb_options, 'use_minified_css');
		$this->minified_js = ESSBOptionValuesHelper::options_bool_value($essb_options, 'use_minified_js');
		$this->js_head = ESSBOptionValuesHelper::options_bool_value($essb_options, 'scripts_in_head');
		$this->js_async = ESSBOptionValuesHelper::options_bool_value($essb_options, 'load_js_async');
		$this->js_defer = ESSBOptionValuesHelper::options_bool_value($essb_options, 'load_js_defer');
		$this->js_delayed = ESSBOptionValuesHelper::options_bool_value($essb_options, 'load_js_delayed');
		
		$remove_ver_resource = ESSBOptionValuesHelper::options_bool_value($essb_options, 'remove_ver_resource');
		if ($remove_ver_resource) { 
			$this->resource_version = '';
		}
	}
	
	
	/**
	 * Cloning disabled
	 */
	private function __clone() {
	}
	
	/**
	 * Serialization disabled
	 */
	private function __sleep() {
	}
	
	/**
	 * De-serialization disabled
	 */
	private function __wakeup() {
	}
	
		
	function is_plugin_deactivated_on() {
		global $essb_options;
		if (is_admin()) {
			return;
		}
	
		$reset_postdata = ESSBOptionValuesHelper::options_bool_value($essb_options, 'reset_postdata');
		if ($reset_postdata) {
			wp_reset_postdata();
		}
	
		//display_deactivate_on
		$is_deactivated = false;
		$display_deactivate_on = ESSBOptionValuesHelper::options_value($essb_options, 'display_deactivate_on');
		if ($display_deactivate_on != '') {
			$excule_from = explode(',', $display_deactivate_on);
				
			$excule_from = array_map('trim', $excule_from);
			if (in_array(get_the_ID(), $excule_from, false)) {
				$is_deactivated = true;
			}
		}
		return $is_deactivated;
	}
	
	public function deactivate_actions() { 
		if ($this->inline_css_footer) {
			remove_action('wp_footer', array($this, 'generate_custom_css'), 997);
		}
		else {
			remove_action('wp_head', array($this, 'generate_custom_css'));
		}
		
		remove_action('wp_footer', array($this, 'generate_custom_footer_css'), 998);
		
		remove_action('wp_head', array($this, 'generate_custom_js'));
		remove_action('wp_footer', array($this, 'generate_custom_footer_js'), 998);
		
		// static CSS and javascripts sources enqueue
		remove_action ( 'wp_enqueue_scripts', array ($this, 'register_front_assets' ), 10 );
		remove_action('wp_footer', array($this, 'generate_custom_css_precompiled'), 996);
		remove_action('wp_footer', array($this, 'generate_custom_js_precompiled'), 996);
		
	}
	
	/**
	 * add_static_resource
	 * 
	 * @param unknown_type $file_with_path
	 * @param unknown_type $key
	 * @param unknown_type $type
	 */
	public function add_static_resource($file_with_path, $key, $type = '', $noasync = false) {
		if ($type == 'css') {
			$this->css_static[$key] = $file_with_path;
		}
		if ($type == 'js') {
			if ($noasync) {
				$this->js_static_nonasync[$key] = $file_with_path;
			}
			else {
				$this->js_static[$key] = $file_with_path;
			}
		}
	}
	
	public function add_static_resource_footer($file_with_path, $key, $type = '', $noasync = false) {
		if ($type == 'css') {
			$this->css_static_footer[$key] = $file_with_path;
		}
		if ($type == 'js') {
			if ($noasync) {
				// @since 3.0.4 - double check for the twice counter load script
				if (!isset($this->js_static_nonasync[$key])) {
					$this->js_static_noasync_footer[$key] = $file_with_path;
				}
			}
			else {
				// @since 3.0.4 - double check for the twice counter load script
				if (!isset($this->js_static[$key])) {
					$this->js_static_footer[$key] = $file_with_path;
				}
			}
		}
	}
	
	public function add_static_footer_css($file_with_path, $key) {
		$this->css_static_footer[$key] = $file_with_path;
 	}
	
	/**
	 * add_css
	 * 
	 * @param string $code
	 * @param string $key
	 * @param string $location
	 */
	public function add_css($code, $key = '', $location = 'head') {
		if ($key != '') {
			if ($location == 'head') {
				$this->css_head[$key] = $code;
			}
			else {
				$this->css_footer[$key] = $code;
			}
		}
		else {
			if ($location == 'head') {
				$this->css_head[] = $code;
			}
			else {
				$this->css_footer[] = $code;
			}
		}
	}
	
	public function remove_css($key = '', $location = 'head') {
		if ($location == 'head') {
			unset($this->css_head[$key]);
		}
		else {
			unset($this->css_footer[$key]);
		}
	}
	
	public function add_social_api($key) {
		$this->js_social_apis[$key] = 'loaded';
	}
	
	/**
	 * add_js
	 * 
	 * @param string $code
	 * @param bool $minify
	 * @param string $key
	 * @param string $position
	 */
	public function add_js($code, $minify = false, $key = '', $position = 'footer', $noncachble = false) {
		if ($minify) {
			$code = trim(preg_replace('/\s+/', ' ', $code));
		}	
		
		if ($key != '') {
			if ($position == 'footer') {
				if ($noncachble) {
					$this->js_code_noncachable[$key] = $code;
				}
				else {
					$this->js_code[$key] = $code;
				}
			}
			else {
				$this->js_code_head[$key] = $code;
			}
		}
		else {
			if ($position == 'footer') {
				if ($noncachble) {
					$this->js_code_noncachable[] = $code;
				}
				else {
					$this->js_code[] = $code;
				}				
			}
			else {
				$this->js_code_head[] = $code;
			}				
		}
	}
	
	/*
	 * Enqueue all front CSS and javascript files
	 */
	
	
	function enqueue_style_single_css($key, $file, $version) {
		if (!defined('ESSB3_PRECOMPILED_RESOURCE')) {
			wp_enqueue_style ( $key, $file, false, $this->resource_version, 'all' );
		}
		else {
			$this->precompiled_css_queue[$key] = $file;
		}
	}
		
	function register_front_assets() {
		if ($this->is_plugin_deactivated_on()) {
			return;
		}	
		
		$load_in_footer = ($this->js_head) ? false : true;
		
		// enqueue all css registered files
		foreach ($this->css_static as $key => $file) {
			if ($key == 'easy-social-share-buttons-profles') {
				if (!ESSBCoreHelper::is_module_deactivate_on('profiles')) {
					$this->enqueue_style_single_css($key, $file, $this->resource_version);
					//wp_enqueue_style ( $key, $file, false, $this->resource_version, 'all' );
				}
			}
			else if ($key == "easy-social-share-buttons-nativeskinned" || $key == "essb-fontawsome" || $key == "essb-native-privacy") {
				if (!ESSBCoreHelper::is_module_deactivate_on('native')) {
					//wp_enqueue_style ( $key, $file, false, $this->resource_version, 'all' );
					$this->enqueue_style_single_css($key, $file, $this->resource_version);						
				}
			}
			else {
				//wp_enqueue_style ( $key, $file, false, $this->resource_version, 'all' );
				$this->enqueue_style_single_css($key, $file, $this->resource_version);
			}				
		}
		
		foreach ($this->js_static_nonasync as $key => $file) {
			wp_enqueue_script ( $key, $file, array ( 'jquery' ), $this->resource_version, $load_in_footer );
		}
		
		// load scripts when no async or deferred is selected
		if (!$this->js_async && !$this->js_defer && !$this->js_delayed) {
			foreach ($this->js_static as $key => $file) {
				if (!defined('ESSB3_PRECOMPILED_RESOURCE')) {
					wp_enqueue_script ( $key, $file, array ( 'jquery' ), $this->resource_version, $load_in_footer );
				}
				else {
					$this->precompiled_js_queue[$key] = $file;
				}
			}
		}
	}
	
	/*
	 *  Code generation functions: CSS
	 */
	
	/**
	 * generate_custom_css
	 */
	function generate_custom_css() {
		global $post;
		if ($this->is_plugin_deactivated_on()) {
			return;
		}

		$this->add_css(ESSBResourceBuilderSnippets::css_build_customizer(), 'essb-customcss-header');
		
		$cache_slug = "essb-css-head";
		
		if (isset($post)) {

			if (defined('ESSB3_CACHE_ACTIVE_RESOURCE')) {
				$cache_key = $cache_slug.$post->ID;
			
				$cached_data = ESSBDynamicCache::get_resource($cache_key, 'css');
			
				if ($cached_data != '') {
					echo "<link rel='stylesheet' id='essb-cache-css-head'  href='".$cached_data."' type='text/css' media='all' />";
					return;
				}
			}
		}
		
		if (count($this->css_head) > 0) {
			$css_code = '';
			foreach ($this->css_head as $single) {
				$css_code .= $single;
			}
			//$css_code = implode(" ", $this->css_head);
			$css_code = trim(preg_replace('/\s+/', ' ', $css_code));
			if (isset($post)) {
				
				if (defined('ESSB3_CACHE_ACTIVE_RESOURCE')) {
					$cache_key = $cache_slug.$post->ID;
						
					ESSBDynamicCache::put_resource($cache_key, $css_code, 'css');
			
					$cached_data = ESSBDynamicCache::get_resource($cache_key, 'css');
						
					if ($cached_data != '') {
						echo "<link rel='stylesheet' id='essb-cache-css-head'  href='".$cached_data."' type='text/css' media='all' />";
						return;
					}
				}
			}
			echo '<style type="text/css">';
			echo $css_code;
			echo '</style>';
		}
	}
	
	function generate_custom_footer_css() {
		global $post;
		
		if ($this->is_plugin_deactivated_on()) {
			return;
		}		
		
		$this->add_css(ESSBResourceBuilderSnippets::css_build_footer_css(), 'essb-footer-css', 'footer');
		
		if (count($this->css_static_footer) > 0) {
			foreach ($this->css_static_footer as $key => $file) {
				printf('<link rel="stylesheet" id="%1$s"  href="%2$s" type="text/css" media="all" />', $key, $file);
			}
		}
		
		$cache_slug = "essb-css-footer";
		
		if (isset($post)) {
		
			if (defined('ESSB3_CACHE_ACTIVE_RESOURCE')) {
				$cache_key = $cache_slug.$post->ID;
					
				$cached_data = ESSBDynamicCache::get_resource($cache_key, 'css');
					
				if ($cached_data != '') {
					echo "<link rel='stylesheet' id='essb-cache-css-footer'  href='".$cached_data."' type='text/css' media='all' />";
					return;
				}
			}
		}
		
		if (count($this->css_footer) > 0) {
			//$css_code = implode(" ", $this->css_footer);
			$css_code = '';
			foreach ($this->css_footer as $single) {
				$css_code .= $single;
			}
			
			$css_code = trim(preg_replace('/\s+/', ' ', $css_code));
				
			if (isset($post)) {
		
				if (defined('ESSB3_CACHE_ACTIVE_RESOURCE')) {
					$cache_key = $cache_slug.$post->ID;
		
					ESSBDynamicCache::put_resource($cache_key, $css_code, 'css');
						
					$cached_data = ESSBDynamicCache::get_resource($cache_key, 'css');
		
					if ($cached_data != '') {
						echo "<link rel='stylesheet' id='essb-cache-css-footer'  href='".$cached_data."' type='text/css' media='all' />";
						return;
					}
				}
			}
			echo '<style type="text/css">';
			echo $css_code;
			echo '</style>';
		}
		
	}
	
	
	function generate_custom_css_precompiled() {
		if ($this->is_plugin_deactivated_on()) {
			return;
		}
		
		$cache_key = "essb-precompiled".(essb_is_mobile() ? "-mobile": "");
		
		$cached_data = ESSBPrecompiledResources::get_resource($cache_key, 'css');
			
		if ($cached_data != '') {
			echo "<link rel='stylesheet' id='essb-compiled-css'  href='".$cached_data."' type='text/css' media='all' />";
			return;
		}
		
		// generation of all styles
		$this->add_css(ESSBResourceBuilderSnippets::css_build_customizer(), 'essb-customcss-header');
		$this->add_css(ESSBResourceBuilderSnippets::css_build_footer_css(), 'essb-footer-css', 'footer');
		
		$static_content = array();
		
		$styles = array();
		if (count($this->css_head) > 0) {
			$css_code = implode(" ", $this->css_head);
			$css_code = trim(preg_replace('/\s+/', ' ', $css_code));
			$styles[] = $css_code;
		}
		
		// parsing inlinde enqueue styles
		$current_site_url = get_site_url();
		foreach ($this->precompiled_css_queue as $key => $file) {
			$relative_path = ESSBPrecompiledResources::get_asset_relative_path($current_site_url, $file);
			$css_code = file_get_contents( ABSPATH . $relative_path );
			$css_code = trim(preg_replace('/\s+/', ' ', $css_code));
			
			if ($key == "essb-social-image-share") {
				$css_code = str_replace('../', ESSB3_PLUGIN_URL . '/lib/modules/social-image-share/assets/', $css_code);
			}
			if ($key == "easy-social-share-buttons-profiles" || $key == "easy-social-share-buttons-display-methods") {
				$css_code = str_replace('../', ESSB3_PLUGIN_URL . '/assets/', $css_code);
			}
			if ($key == "essb-social-followers-counter") {
				$css_code = str_replace('../', ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/', $css_code);
			}
			
			$styles[] = $css_code;
			
			$static_content[$key] = $file;
		}
		
		foreach ($this->css_static_footer as $key => $file) {
			$relative_path = ESSBPrecompiledResources::get_asset_relative_path($current_site_url, $file);
			$css_code = file_get_contents( ABSPATH . $relative_path );
			$css_code = trim(preg_replace('/\s+/', ' ', $css_code));
			
			if ($key == "essb-social-image-share") {
				$css_code = str_replace('../', ESSB3_PLUGIN_URL . '/lib/modules/social-image-share/assets/', $css_code);
			}
			if ($key == "easy-social-share-buttons-profiles") {
				$css_code = str_replace('../', ESSB3_PLUGIN_URL . '/assets/', $css_code);
			}
			if ($key == "essb-social-followers-counter") {
				$css_code = str_replace('../', ESSB3_PLUGIN_URL . '/lib/modules/social-followers-counter/assets/', $css_code);
			}
			
			$styles[] = $css_code;
				
			$static_content[$key] = $file;
		}

		if (count($this->css_footer) > 0) {
			$css_code = implode(" ", $this->css_footer);
			$css_code = trim(preg_replace('/\s+/', ' ', $css_code));
			$styles[] = $css_code;
		}
		
		$toc = array();
		
		foreach ( $static_content as $handle => $item_content )
			$toc[] = sprintf( ' - %s', $handle.'-'.$item_content );
		
		$styles[] = sprintf( "\n\n\n/* TOC:\n%s\n*/", implode( "\n", $toc ) );
		
		ESSBPrecompiledResources::put_resource($cache_key, implode(' ', $styles), 'css');
		
		$cached_data = ESSBPrecompiledResources::get_resource($cache_key, 'css');
		
		if ($cached_data != '') {
			echo "<link rel='stylesheet' id='essb-compiled-css'  href='".$cached_data."' type='text/css' media='all' />";
			return;
		}
	}

	/*
	 *  Code generation functions: Javascript
	*/
	
	/**
	 * generate_custom_js
	 * 
	 * Generate custom javascript code in head of page that will not be cached
	 */
	
	function generte_custom_js_precompiled() {
		if ($this->is_plugin_deactivated_on()) {
			return;
		}
	
		// -- loading non cachble and noasync code first
		if (count($this->js_social_apis) > 0) {
			if (!ESSBCoreHelper::is_module_deactivate_on('native')) {
				foreach ($this->js_social_apis as $network => $loaded) {
					$this->load_social_api_code($network);
				}
			}
		}
		
		if (count($this->js_static_noasync_footer)) {
			foreach ($this->js_static_noasync_footer as $key => $file) {
				$this->manual_script_load($key, $file);
			}
		}
		
		// loading in precompiled cache mode
		$cache_key = "essb-precompiled".(essb_is_mobile() ? "-mobile": "");
		
		$cached_data = ESSBPrecompiledResources::get_resource($cache_key, 'js');
			
		if ($cached_data != '') {
			echo "<script type='text/javascript' src='".$cached_data."' async></script>";
			return;
		}
				
		$static_content = array();		
		$scripts = array();
		$current_site_url = get_site_url();
		
		$scripts[] = implode(" ", $this->js_code);
		
		if (count($this->js_static) > 0) {
			foreach ($this->js_static as $key => $file) {
				$relative_path = ESSBPrecompiledResources::get_asset_relative_path($current_site_url, $file);
				$code = file_get_contents( ABSPATH . $relative_path );
				
				$scripts[] = $code;
					
				$static_content[$key] = $file;
			}
		}
		
		if (count($this->js_static_footer)) {
			foreach ($this->js_static_footer as $key => $file) {
				$relative_path = ESSBPrecompiledResources::get_asset_relative_path($current_site_url, $file);
				$code = file_get_contents( ABSPATH . $relative_path );
			
				$scripts[] = $code;
					
				$static_content[$key] = $file;
			}
		}
		
		$toc = array();
		
		foreach ( $static_content as $handle => $item_content )
			$toc[] = sprintf( ' - %s', $handle.'-'.$item_content );
		
		$scripts[] = sprintf( "\n\n\n/* TOC:\n%s\n*/", implode( "\n", $toc ) );
		
		ESSBPrecompiledResources::put_resource($cache_key, implode(' ', $scripts), 'js');
		
		$cached_data = ESSBPrecompiledResources::get_resource($cache_key, 'js');
		
		if ($cached_data != '') {
			echo "<script type='text/javascript' src='".$cached_data."' async></script>";
		}
	}
	
	function generate_custom_js() {
		if ($this->is_plugin_deactivated_on()) {
			return;
		}		
		
		if (count($this->js_code_head) > 0) {
			//$js_code = implode(" ", $this->js_code_head);
			$js_code = '';
			foreach ($this->js_code_head as $code) {
				$js_code .= $code;
			}
			
			print "\n";
			printf('<script type="text/javascript">%1$s</script>', $js_code);
		}
	}
	
	function generate_custom_footer_js() {
		global $post;
		
		if ($this->is_plugin_deactivated_on()) {
			return;
		}
		
		$cache_slug = "essb-js-footer";
		if (count($this->js_social_apis) > 0) {
			if (!ESSBCoreHelper::is_module_deactivate_on('native')) {
				foreach ($this->js_social_apis as $network => $loaded) {
					$this->load_social_api_code($network);
				}
			}
		}
		// load of static scripts async or deferred
		if (count($this->js_static) > 0) {
			if ($this->js_defer || $this->js_async) {
				$js_code_patterns = array();
				$load_mode = ($this->js_async) ? "po.async=true;" : "po.defer=true;";
				
				foreach ($this->js_static as $key => $file) {
					$js_code_patterns[] = sprintf('
					(function() {
					var po = document.createElement(\'script\'); po.type = \'text/javascript\'; %2$s;
					po.src = \'%1$s\';
					var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
					})();', $file, $load_mode);
				}
				
				printf('<script type="text/javascript">%1$s</script>', implode(" ", $js_code_patterns));
			}
			else if ($this->js_delayed) {
				// delayed script loading
				$js_code_patterns = array();
				$load_mode = "po.async=true;";
				
				foreach ($this->js_static as $key => $file) {
					$js_code_patterns[] = sprintf('
							(function() {
							var po = document.createElement(\'script\'); po.type = \'text/javascript\'; %2$s;
							po.src = \'%1$s\';
							var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
					})();', $file, $load_mode);
				}
				
				printf('<script type="text/javascript">jQuery( document ).ready(function() { setTimeout(function() { %1$s }, 2000); });</script>', implode(" ", $js_code_patterns));
			}
		}
		
		if (count($this->js_static_footer)) {
			if ($this->js_defer || $this->js_async) {
				$js_code_patterns = array();
				$load_mode = ($this->js_async) ? "po.async=true;" : "po.defer=true;";
				
				foreach ($this->js_static_footer as $key => $file) {
					$js_code_patterns[] = sprintf('
							(function() {
							var po = document.createElement(\'script\'); po.type = \'text/javascript\'; %2$s;
							po.src = \'%1$s\';
							var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
				})();', $file, $load_mode);
				}
				
				printf('<script type="text/javascript">%1$s</script>', implode(" ", $js_code_patterns));
			}
			else if ($this->js_delayed) {
				// delayed script loading
				$js_code_patterns = array();
				$load_mode = "po.async=true;";
			
				foreach ($this->js_static_footer as $key => $file) {
					$js_code_patterns[] = sprintf('
							(function() {
							var po = document.createElement(\'script\'); po.type = \'text/javascript\'; %2$s;
							po.src = \'%1$s\';
							var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
				})();', $file, $load_mode);
				}
			
				printf('<script type="text/javascript">jQuery( document ).ready(function() { setTimeout(function() { %1$s }, 2000); });</script>', implode(" ", $js_code_patterns));
			}
			else {
				foreach ($this->js_static_footer as $key => $file) {
					//wp_enqueue_script ( $key, $file, array ( 'jquery' ), $this->resource_version, false );
					$this->manual_script_load($key, $file);
				}
			}
		}
		
		if (count($this->js_static_noasync_footer)) {
			foreach ($this->js_static_noasync_footer as $key => $file) {
				//wp_enqueue_script ( $key, $file, array ( 'jquery' ), $this->resource_version, true );
				$this->manual_script_load($key, $file);
			}
		}
		
		if (count($this->js_code_noncachable)) {
			// load non cachable javascript code
			echo implode(" ", $this->js_code_noncachable);
		}
		
		// dynamic footer javascript that can be cached
		$cache_slug = "essb-js-footer";
		
		if (isset($post)) {
			if (defined('ESSB3_CACHE_ACTIVE_RESOURCE')) {
				$cache_key = $cache_slug.$post->ID;
		
				$cached_data = ESSBDynamicCache::get_resource($cache_key, 'js');
		
				if ($cached_data != '') {
					echo "<script type='text/javascript' src='".$cached_data."' defer></script>";
					return;
				}
			}
		}
			
			
		//$js_code = implode(" ", $this->js_code);
		$js_code = '';
		foreach ($this->js_code as $single) {
			$js_code .= $single;
		}
			
		if (isset($post)) {
			if (defined('ESSB3_CACHE_ACTIVE_RESOURCE')) {
				$cache_key = $cache_slug.$post->ID;
		
				ESSBDynamicCache::put_resource($cache_key, $js_code, 'js');
		
				$cached_data = ESSBDynamicCache::get_resource($cache_key, 'js');
		
				if ($cached_data != '') {
					echo "<script type='text/javascript' src='".$cached_data."' defer></script>";
					return;
				}
			}
		}
		echo '<script type="text/javascript">';
		echo $js_code;
		echo '</script>';	
			
	}
	
	public function manual_script_load($key, $file) {
		$ver_string = "";
		
		if (!empty($this->resource_version)) {
			$ver_string = "?ver=".$this->resource_version;
		}
		
		printf('<script type="text/javascript" src="%1$s%2$s"></script>', $file, $ver_string);
	}
	
	public function load_social_api_code($network = '') {
		global $essb_options;
		
		if ($this->is_plugin_deactivated_on()) {
			return;
		}		
		
		$facebook_lang = "en_US";
		$user_defined_language_code = ESSBOptionValuesHelper::options_value($essb_options, 'facebook_like_button_lang');
		if (!empty($user_defined_language_code)) {
			$facebook_lang = $user_defined_language_code;
		}
		
		$facebook_appid = "";
		$facebook_async = ESSBOptionValuesHelper::options_bool_value($essb_options, 'facebook_like_button_api_async') ? 'true' : 'false';
		
		$vk_application = ESSBOptionValuesHelper::options_value($essb_options, 'vklikeappid');
		
		if ($network == 'facebook') {
			echo $this->generate_facebook_api_code($facebook_lang, $facebook_appid, $facebook_async);
		}
		if ($network == 'google') {
			echo $this->generate_google_api_code();
		}
		if ($network == 'vk') {
			echo $this->generate_vk_api_code($vk_application);
		}
		if ($network == "pinterest") {
			echo $this->generate_pinterst_code();
		}
		if ($network == "twitter") {
			echo $this->generate_twitter_code();
		}
	}
	
	public function generate_facebook_api_code($lang = 'en_US', $app_id = '', $async_load = 'false') {
		if ($app_id != '') {
			$app_id = "&appId=".$app_id;
		}
		
		$js_async = "";
		if ($async_load == 'true') {
			$js_async = " js.async = true;";
		}
		
		$result = '<div id="fb-root"></div>
		<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id; '.$js_async.'
		js.src = "//connect.facebook.net/'.$lang.'/sdk.js#version=v2.3&xfbml=1'.$app_id.'"
		fjs.parentNode.insertBefore(js, fjs);
		}(document, \'script\', \'facebook-jssdk\'));</script>';
		
		return $result;
	}
	
	public function generate_google_api_code() {
	
		$script = '
		<script type="text/javascript">
		(function() {
		var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
		po.src = \'https://apis.google.com/js/platform.js\';
		var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
	})();
	</script>';
	
		return $script;
	}
	
	public function generate_vk_api_code($appid = '') {
		$script = '<script type="text/javascript" src="//vk.com/js/api/openapi.js?115"></script>
		<script type="text/javascript">
		VK.init({apiId: '.$appid.', onlyWidgets: true});
		</script>';
	
		return $script;
	
	}
	
	public function generate_pinterst_code() {
		
		$script = '
		<script type="text/javascript">
		(function() {
		var po = document.createElement(\'script\'); po.type = \'text/javascript\'; po.async = true;
		po.src = \'//assets.pinterest.com/js/pinit.js\';
		var s = document.getElementsByTagName(\'script\')[0]; s.parentNode.insertBefore(po, s);
		})();
		</script>';
		
		return $script;
	
	}
	
	public function generate_twitter_code() {
		$script = '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document, \'script\', \'twitter-wjs\');</script>';
		
		return $script;
	}
	
}

?>