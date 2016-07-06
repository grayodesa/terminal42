<?php

class ESSBSocialShareOptimization {
	private static $instance = null;
	
	public $default_image = "";
	public $apply_the_content = false;
	
	// Facebook additional
	public $ogtags_active = false;
	public $fbadmins = "";
	public $fbpage = "";
	public $fbapp = "";
	
	// Twitter Cards
	public $twitter_cards_active = false;
	public $card_type = "";
	public $twitter_user = "";
		
	// Google Schema.org
	public $google_authorship = false;
	public $google_markup = false;
	public $google_publisher = "";
	public $google_author = "";
	
	// Post defaults
	private $post_title = "";
	private $post_description = "";
	private $post_image = "";
	private $post_url = "";
	private $site_name = "";
	
	// Yoast SEO details: new from version 3.3
	private $yoast_og_title = "";
	private $yoast_og_description = "";
	private $yoast_og_image = "";
	private $yoast_seo_title = "";
	private $yoast_seo_description = "";
	
	private $fb_title = "";
	private $fb_description = "";
	private $fb_image = "";
	private $fb_video_url = "";
	private $fb_video_h = "";
	private $fb_video_w = "";
	private $fb_author_profile = "";
	
	private $twitter_title = "";
	private $twitter_description = "";
	private $twitter_image = "";
	
	private $google_title = "";
	private $google_description = "";
	private $google_image = "";
	
	private $sso_active = true;
	
	private $options;
	
	private $meta = array();
	
	public static function get_instance() {
	
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
	
		return self::$instance;
	
	} // end get_instance;
	
	function __construct() {
		
		global $essb_options;		
		$this->options = $essb_options;
		
		$this->loadDefaultFromOptions();
		
		if ($this->sso_active) {
			add_filter ( 'language_attributes', array ($this, 'insertLanguageAttributes' ) );
			add_action ( 'wp_head', array ($this, 'outputMeta' ), 1 );
				
			// stop Jetpack tags
			if (class_exists ( 'JetPack' )) {
				add_filter ( 'jetpack_enable_opengraph', '__return_false', 99 );
				add_filter ( 'jetpack_enable_open_graph', '__return_false', 99 );
			}
				
			// stop Yoast SEO from generating double tags
			if (defined('WPSEO_VERSION')) {
				global $wpseo_og;
				if (isset($wpseo_og)) {
					remove_action( 'wpseo_head', array( $wpseo_og, 'opengraph' ), 30 );
				}
			}
		}
	}
	
	private function loadDefaultFromOptions() {
		
		$this->default_image = ESSBOptionValuesHelper::options_value($this->options, 'sso_default_image');
				
		if (ESSBOptionValuesHelper::options_bool_value($this->options, 'sso_apply_the_content')) {
			$this->apply_the_content = true;
		}
		
		if (ESSBOptionValuesHelper::options_bool_value($this->options, 'opengraph_tags')) {
			$this->ogtags_active = true;
			$this->fbadmins = isset ( $this->options ['opengraph_tags_fbadmins'] ) ? $this->options ['opengraph_tags_fbadmins'] : '';
			$this->fbpage = isset ( $this->options ['opengraph_tags_fbpage'] ) ? $this->options ['opengraph_tags_fbpage'] : '';
			$this->fbapp = isset ( $this->options ['opengraph_tags_fbapp'] ) ? $this->options ['opengraph_tags_fbapp'] : '';
			$this->fb_author_profile = isset($this->options['opengraph_tags_fbauthor']) ? $this->options['opengraph_tags_fbauthor'] : '';
		}
				
		if (ESSBOptionValuesHelper::options_bool_value($this->options, 'twitter_card')) {
			$this->twitter_cards_active = true;
			$this->card_type = isset ( $this->options ['twitter_card_type'] ) ? $this->options ['twitter_card_type'] : '';
			$this->twitter_user = isset ( $this->options ['twitter_card_user'] ) ? $this->options ['twitter_card_user'] : '';
		}
		
		if (ESSBOptionValuesHelper::options_bool_value($this->options, 'sso_google_author')) {
			$this->google_authorship = true;
			$this->google_author = isset ( $this->options ['ss_google_author_profile'] ) ? $this->options ['ss_google_author_profile'] : '';
			$this->google_publisher = isset ( $this->options ['ss_google_author_publisher'] ) ? $this->options ['ss_google_author_publisher'] : '';
		}
		
		if (ESSBOptionValuesHelper::options_bool_value($this->options, 'sso_google_markup')) {
			$this->google_markup = true;
		}
		
		if ($this->ogtags_active || $this->twitter_cards_active || $this->google_authorship || $this->google_markup) {
			$this->sso_active = true;
		}
	}
	
	private function loadPostSettings() {
		global $post;
		
		if (is_single () || is_page ()) {
			easy_share_deactivate();
			
			// loading post default data
			$this->post_title = get_the_title ();
			$this->site_name = get_bloginfo('name');
			$this->post_url = get_permalink($post->ID);
			
			$content_post = get_post ( $post->ID );
			$working_post_content = $content_post->post_content;
			
			if ($this->apply_the_content) {
				$working_post_content = apply_filters('the_content', $working_post_content);
			}
			//$essb_post_og_desc = strip_shortcodes($essb_post_og_desc);
			$post_shortdesc = $content_post->post_excerpt;
			if ($post_shortdesc != '') {
				$working_post_content = $post_shortdesc;
			}
			
			if (is_attachment() && empty($working_post_content)) {
				$attachment_alt_text = get_post_meta($post->ID, '_wp_attachment_image_alt', true);
				if (!empty($attachment_alt_text)) {
					$working_post_content = $attachment_alt_text;
				}
			}
			
			$working_post_content = strip_tags ( $working_post_content );
			$working_post_content = preg_replace( '/\s+/', ' ', $working_post_content );
			$working_post_content = strip_shortcodes($working_post_content);
			$working_post_content = trim ( $working_post_content );
			$working_post_content = substr ( $working_post_content, 0, 300 );
			$working_post_content .= '&hellip;';
			
			$this->post_description = $working_post_content;
			
			if (defined('WPSEO_VERSION')) {
			
				// Collect their Social Descriptions as backups if they're not defined in ours
				$this->yoast_og_title 		= get_post_meta( $post->ID, '_yoast_wpseo_opengraph-title' , true );
				$this->yoast_og_description 	= get_post_meta( $post->ID, '_yoast_wpseo_opengraph-description' , true );
				$this->yoast_og_image 		= get_post_meta( $post->ID, '_yoast_wpseo_opengraph-image' , true );
				
				// Collect their SEO fields as 3rd string backups in case we need them
				$this->yoast_seo_title		= get_post_meta( $post->ID, '_yoast_wpseo_title' , true );
				$this->yoast_seo_description	= get_post_meta( $post->ID, '_yoast_wpseo_metadesc' , true );			
				
				if (empty($this->yoast_og_description)) {
					$this->yoast_og_description = $this->yoast_seo_description;
				}
				if (empty($this->yoast_og_title)) {
					$this->yoast_og_title = $this->yoast_seo_title;
				}
			}
			
			//$fb_image = wp_get_attachment_image_src ( get_post_thumbnail_id ( get_the_ID () ), 'full' );
			//if ($fb_image) {
			//	$this->post_image = $fb_image [0];
			//}
			$this->post_image = ESSBCoreHelper::get_post_featured_image(get_the_ID());
				
			if ($this->post_image == "") {
				$this->post_image = $this->default_image;
			}
			
			// end loading post defaults
			
			if ($this->ogtags_active) {
				$this->fb_description = get_post_meta ( $post->ID, 'essb_post_og_desc', true );
				$this->fb_title = get_post_meta ( $post->ID, 'essb_post_og_title', true );
				$this->fb_image = get_post_meta ( $post->ID, 'essb_post_og_image', true );
				
				// since 2.0
				$this->fb_video_url = get_post_meta ( $post->ID, 'essb_post_og_video', true );
				$this->fb_video_h = get_post_meta ( $post->ID, 'essb_post_og_video_h', true );
				$this->fb_video_w = get_post_meta ( $post->ID, 'essb_post_og_w', true );
				
				$onpost_fb_authorship = get_post_meta ($post->ID, 'essb_post_og_author_of_post', true);
				
				if (!empty($onpost_fb_authorship)) {
					$this->fb_author_profile = get_post_meta ($post->ID, 'essb_post_og_author_of_post', true);
				}
				
				if (empty($this->fb_description) && !empty($this->yoast_og_description)) {
					$this->fb_description = $this->yoast_og_description;
				} 

				if (empty($this->fb_title) && !empty($this->yoast_og_title)) {
					$this->fb_title = $this->yoast_og_title;
				}
				
				if (empty($this->fb_image) && !empty($this->yoast_og_image)) {
					$this->fb_image = $this->yoast_og_image;
				}
				
				if ($this->fb_description == "") {
					$this->fb_description = $this->post_description;
				}				
				if ($this->fb_title == "") {
					$this->fb_title = $this->post_title;
				}
				if ($this->fb_image == "") {
					$this->fb_image = $this->post_image;
				}
			}
			
			if ($this->twitter_cards_active) {
				$this->twitter_description =  get_post_meta($post->ID,'essb_post_twitter_desc',true);
				$this->twitter_title =  get_post_meta($post->ID,'essb_post_twitter_title',true);
				$this->twitter_image =  get_post_meta($post->ID,'essb_post_twitter_image',true);
				
				if ($this->twitter_description == "") {
					$this->twitter_description = $this->fb_description;
				}
				if ($this->twitter_title == "") {
					$this->twitter_title = $this->fb_title;
				}
				if ($this->twitter_image == "") {
					$this->twitter_image = $this->fb_image;
				}
				
				if (empty($this->twitter_description) && !empty($this->yoast_og_description)) {
					$this->twitter_description = $this->yoast_og_description;
				}
				
				if (empty($this->twitter_title) && !empty($this->yoast_og_title)) {
					$this->twitter_title = $this->yoast_og_title;
				}
				
				if (empty($this->twitter_image) && !empty($this->yoast_og_image)) {
					$this->twitter_image = $this->yoast_og_image;
				}
				
				if ($this->twitter_image == "") {
					$this->twitter_image = $this->post_image;
				}				
				
				if ($this->twitter_description == "") {
					$this->twitter_description = $this->post_description;
				}
				if ($this->twitter_title == "") {
					$this->twitter_title = $this->post_title;
				}
			}
			
			if ($this->google_markup) {
				$this->google_description =  get_post_meta($post->ID,'essb_post_google_desc',true);
				$this->google_title =  get_post_meta($post->ID,'essb_post_google_title',true);
				$this->google_image =  get_post_meta($post->ID,'essb_post_google_image',true);
			
				if ($this->google_description == "") {
					$this->google_description = $this->fb_description;
				}
				if ($this->google_title == "") {
					$this->google_title = $this->fb_title;
				}
				if ($this->google_image == "") {
					$this->google_image = $this->fb_image;
				}
				
				if (empty($this->google_description) && !empty($this->yoast_og_description)) {
					$this->google_description = $this->yoast_og_description;
				}
				
				if (empty($this->google_title) && !empty($this->yoast_og_title)) {
					$this->google_title = $this->yoast_og_title;
				}
				
				if (empty($this->google_image) && !empty($this->yoast_og_image)) {
					$this->google_image = $this->yoast_og_image;
				}
				
				if ($this->google_description == "") {
					$this->google_description = $this->post_description;
				}
				if ($this->google_title == "") {
					$this->google_title = $this->post_title;
				}
				if ($this->google_image == "") {
					$this->google_image = $this->post_image;
				}
			}
			
			easy_share_reactivate();
		
		}
		
	}
	
	public function loadFrontpageTags() {
		if (is_front_page()) {
			$fp_title = ESSBOptionValuesHelper::options_value($this->options, 'sso_frontpage_title');
			$fp_description = ESSBOptionValuesHelper::options_value($this->options, 'sso_frontpage_description');
			$fp_image = ESSBOptionValuesHelper::options_value($this->options, 'sso_frontpage_image');
			
			$this->fb_description = $fp_description;
			$this->fb_title = $fp_title;
			$this->fb_image = $fp_image;
			
			$this->twitter_description = $fp_description;
			$this->twitter_title = $fp_title;
			$this->twitter_image = $fp_image;
			
			$this->google_description = $fp_description;
			$this->google_title = $fp_title;
			$this->google_image = $fp_image;
			
			$this->post_url = get_site_url();
		}
	}
	
	public function insertLanguageAttributes($content) {
		if ($this->ogtags_active) {
			if ($this->fbadmins != '' || $this->fbapp != '') {
				$content .= ' prefix="og: http://ogp.me/ns#  fb: http://ogp.me/ns/fb#"';
			} else {
				$content .= ' prefix="og: http://ogp.me/ns#"';
			}
		}
		
		if ($this->google_markup && is_singular ()) {
			$content .= ' itemscope itemtype="http://schema.org/Article"';
		}

		return $content;
	}
	
	public function outputMeta() {
		global $post;
		
		if (ESSBCoreHelper::is_module_deactivate_on('sso')) {
			return "";
		}
		
		$cache_key = "";
		if (isset($post)) {
			
			$cache_key = "essb_ogtags_".$post->ID;
			
			if (defined('ESSB_CACHE_ACTIVE')) {
				$cached_data = ESSBCache::get($cache_key);
					
				if ($cached_data != '') {
					echo "\r\n";
					echo $cached_data;
					echo "\r\n";
					return;
				}
			}
		}
		
		$this->loadPostSettings();
		
		// @since 3.3 front page optimization tags
		$this->loadFrontpageTags();
		
		if ($this->ogtags_active) {
			$this->buildFacebookMeta();
		}
		if ($this->twitter_cards_active) {
			$this->buildTwitterMeta();
		}
		
		if ($this->google_authorship || $this->google_markup) {
			$this->buildGoogleMeta();
		}
		
		$output_meta = "";
		
		echo "\r\n";
		foreach ($this->meta as $single) {
			$output_meta .= $single."\r\n";
		}
		echo $output_meta;
		echo "\r\n";
		
		if (defined('ESSB_CACHE_ACTIVE')) {
			if ($cache_key != '') {
				ESSBCache::put($cache_key, $output_meta);
			}
		}
	}
	
	// Google
	
	private function buildGoogleMeta() {
		if ($this->google_authorship) {
			if (is_singular () && !is_front_page()) {
				$this->googleAuthorshipBuilder('author', $this->google_author);
			}
			$this->googleAuthorshipBuilder('publisher', $this->google_publisher);
		}
		
		if ($this->google_markup) {
			$this->googleMetaBuilder('name', $this->google_title, true);
			$this->googleMetaBuilder('description', $this->google_description, true);
			$this->googleMetaBuilder('image', $this->google_image);
		}
	}
	
	private function googleMetaBuilder($property = '', $value = '', $apply_filters = false) {
		if ($apply_filters) {
			$value = str_replace ( '\'', "&#8217;", $value );
			$value = str_replace ( '"', "&qout;", $value );
				
			$value = addslashes(strip_tags($value));
		}
		
		if ($property != '' && $value != '') {
			$this->meta[] = '<meta itemprop="'.$property.'" content="'.$value.'" />';
		}
	}
	
	private function googleAuthorshipBuilder($property = '', $value = '') {
		if ($property != '' && $value != '') {
			$this->meta[] = '<link rel="'.$property.'" href="'.$value.'"/>';
		}
	}
	
	// Twitter
	
	private function buildTwitterMeta() {
		if ($this->card_type == "") {
			$this->card_type = "summary";
		}
		else if ($this->card_type == "summaryimage") {
			$this->card_type = "summary_large_image";
		}
		
		$this->twitterMetaTagBuilder('card', $this->card_type);
		if ($this->twitter_user != '') { 
			$this->twitterMetaTagBuilder('site', '@'.$this->twitter_user);
		}
		$this->twitterMetaTagBuilder('title', $this->twitter_title, true);
		$this->twitterMetaTagBuilder('description', $this->twitter_description);
		//$this->twitterMetaTagBuilder('site', $this->card_type);
		$this->twitterMetaTagBuilder('url', $this->post_url);
		
		if ($this->card_type == "summary_large_image" && $this->twitter_image != '') {
			$this->twitterMetaTagBuilder('image:src', $this->twitter_image);
		}
		$this->twitterMetaTagBuilder('domain', $this->site_name, true);
	}
	
	private function twitterMetaTagBuilder($property = '', $value = '', $apply_filters = false, $prefix = 'twitter') {
		if ($apply_filters) {
			$value = str_replace ( '\'', "&#8217;", $value );
			$value = str_replace ( '"', "&qout;", $value );
			
			$value = addslashes(strip_tags($value));
		}
		
		if ($property != '' && $value != '') {
			$this->meta[] = '<meta property="'.$prefix.':'.$property.'" content="'.$value.'" />';
		}
	}
	
	// Facebook
	
	private function buildFacebookMeta() {
		$this->openGraphMetaTagBuilder('app_id', $this->fbapp, false, 'fb');
		$this->openGraphMetaTagBuilder('admins', $this->fbadmins, false, 'fb');
		
		$this->openGraphMetaTagBuilder('title', $this->fb_title, true);
		$this->openGraphMetaTagBuilder('description', $this->fb_description, true);
		$this->openGraphMetaTagBuilder('url', $this->post_url);
		$this->openGraphMetaTagBuilder('image', $this->fb_image);
		
		$content_type = (is_single () || is_page ()) ? "article" : "website";
		$this->openGraphMetaTagBuilder('type', $content_type);
		$this->openGraphMetaTagBuilder('site_name', $this->site_name, true);
		
		// @since 2.0 output video meta tags
		if ($this->fb_video_url != '') {
			$this->openGraphMetaTagBuilder('video', esc_url($this->fb_video_url), false);
			$this->openGraphMetaTagBuilder('video:height', esc_attr($this->fb_video_h), false);
			$this->openGraphMetaTagBuilder('video:width', esc_attr($this->fb_video_w), false);
			$this->openGraphMetaTagBuilder('video:type', 'application/x-shockwave-flash', false);
		}
		
		if ($this->fb_author_profile != '') {
			$this->openGraphMetaTagBuilder('author', esc_attr($this->fb_author_profile), false, 'article');
		}
		
		// only for posts
		if (is_singular () && !is_front_page() ) {
			$this->og_tags();
			$this->og_category();
			$this->og_publish_date();
			if ($this->fbpage != '') {
				$this->openGraphMetaTagBuilder('publisher', $this->fbpage, false, 'article');
			}		
		}
	}
	
	private function openGraphMetaTagBuilder($property = '', $value = '', $apply_filters = false, $prefix = 'og') {
		if ($apply_filters) {
			
			$value = str_replace ( '\'', "&#8217;", $value );
			$value = str_replace ( '"', "&qout;", $value );
			$value = addslashes(strip_tags($value));
		}
		
		if ($property != '' && $value != '') {
			$this->meta[] = '<meta property="'.$prefix.':'.$property.'" content="'.$value.'" />';
		}
		
	}
	
	function og_tags() {
		if (! is_singular ()) {
			return;
		}
	
		$tags = get_the_tags ();
		if (! is_wp_error ( $tags ) && (is_array ( $tags ) && $tags !== array ())) {
			foreach ( $tags as $tag ) {
				$this->openGraphMetaTagBuilder('tag', $tag->name, false, 'article');
			}
		}
	}
	
	public function og_category() {
		if ( ! is_singular() ) {
			return;
		}
	
		$terms = get_the_category();
		if ( ! is_wp_error( $terms ) && ( is_array( $terms ) && $terms !== array() ) ) {
			foreach ( $terms as $term ) {
				$this->openGraphMetaTagBuilder('section', $term->name, false, 'article');
			}
		}
	}
	
	public function og_publish_date() {
		if ( ! is_singular() ) {
			return;
		}
	
		$pub = get_the_date( 'c' );
		$this->openGraphMetaTagBuilder('published_time', $pub, false, 'article');
	
		$mod = get_the_modified_date( 'c' );
		if ( $mod != $pub ) {
			$this->openGraphMetaTagBuilder('modified_time', $mod, false, 'article');
			$this->openGraphMetaTagBuilder('updated_time', $mod);
		}
	}
}

?>