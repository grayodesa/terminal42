<?php

add_filter ( 'tiny_mce_version', 'refresh_mce' );

// Add button to visual editor
include dirname ( __FILE__ ) . '/assets/tinymce/essb-ctt-tinymce.php';

function essb_ctt_shorten($input, $length, $ellipsis = true, $strip_html = true) {
	
	if ($strip_html) {
		$input = strip_tags ( $input );
	}
	
	/*
	 * Checks to see if the mbstring php extension is loaded, for optimal
	 * truncation. If it's not, it bails and counts the characters based on
	 * utf-8. What this means for users is that non-Roman characters will only
	 * be counted correctly if that extension is loaded. Contact your server
	 * admin to enable the extension.
	 */
	
	if (function_exists ( 'mb_internal_encoding' )) {
		if (mb_strlen ( $input ) <= $length) {
			return $input;
		}
		
		$last_space = mb_strrpos ( mb_substr ( $input, 0, $length ), ' ' );
		$trimmed_text = mb_substr ( $input, 0, $last_space );
		
		if ($ellipsis) {
			$trimmed_text .= '…';
		}
		
		return $trimmed_text;
	
	} else {
		
		if (strlen ( $input ) <= $length) {
			return $input;
		}
		
		$last_space = strrpos ( substr ( $input, 0, $length ), ' ' );
		$trimmed_text = substr ( $input, 0, $last_space );
		
		if ($ellipsis) {
			$trimmed_text .= '…';
		}
		
		return $trimmed_text;
	}
}
;

/*
 * Creates the bctt shortcode @since 0.1 @param $atts
 */

function essb_ctt_shortcode($atts) {
	global $essb_options;
	
	extract ( shortcode_atts ( array ('tweet' => '', 'via' => 'yes', 'url' => 'yes', 'nofollow' => 'no', 'user' => '', 'hashtags' => '', 'usehashtags' => 'yes', 'template' => '' )

	, $atts ) );
	
	$handle = $user;
	
	if (function_exists ( 'mb_internal_encoding' )) {
		
		$handle_length = (6 + mb_strlen ( $handle ));
	
	} else {
		
		$handle_length = (6 + strlen ( $handle ));
	
	}
	
	if (! empty ( $handle ) && $via != 'no') {
		
		$handle_code = "&amp;via=" . $handle . "&amp;related=" . $handle;
	
	} else {
		
		$handle_code = '';
	
	}
		
	if ($via != 'yes') {
		
		$handle = '';
		$handle_code = '';
		$handle_length = 0;
	
	}
	
	if ($usehashtags != 'no' && $hashtags != '') {
		$handle_code .= "&amp;hashtags=".$hashtags;
		
		if (function_exists ( 'mb_internal_encoding' )) {
		
			$handle_length = (6 + mb_strlen ( $hashtags ));
		
		} else {
		
			$handle_length = (6 + strlen ( $hashtags ));
		
		}
	}
	
	
	if ($template != '') {
		$template = ' essb-click-to-tweet-'.$template;
	}
	
	$text = $tweet;
	
	$post_url = get_permalink();
	
	// @since 3.4 - fix problem with missing url in click-to-tweet
	if ($url == '') {
		$url = $post_url;
	}
	
	$short_url = "";
	$twitter_shareshort = ESSBOptionValuesHelper::options_bool_value ( $essb_options, 'shorturl_activate' );
	if ($twitter_shareshort) {
		$provider = ESSBOptionValuesHelper::options_value($essb_options, 'shorturl_type');
		$shorturl_bitlyuser = ESSBOptionValuesHelper::options_value($essb_options, 'shorturl_bitlyuser');
		$shorturl_bitlyapi = ESSBOptionValuesHelper::options_value($essb_options, 'shorturl_bitlyapi');
		$short_url = ESSBUrlHelper::short_url ( $post_url, $provider, get_the_ID (), $shorturl_bitlyuser, $shorturl_bitlyapi );
	}
	
	if (filter_var ( $url, FILTER_VALIDATE_URL )) {
		
		$bcttURL = '&amp;url=' . $url;
	
	} elseif ($url != 'no') {
		
			if ($short_url != '') {
				$bcttURL = '&amp;url=' . $short_url.'&amp;counturl='.$post_url;
			}
			else {
				$bcttURL = '&amp;url=' . $post_url;
			}
	
	} else {
		
		$bcttURL = '';
	
	}
	
	$bcttBttn = __('Click to Tweet', ESSB3_TEXT_DOMAIN);
	$user_text = ESSBOptionValuesHelper::options_value($essb_options, 'translate_clicktotweet');
	if ($user_text != '') {
		$bcttBttn = $user_text;
	}
	
	if ($url != 'no') {
		
		$short = essb_ctt_shorten ( $text, (117 - ($handle_length)) );
	
	} else {
		
		$short = essb_ctt_shorten ( $text, (140 - ($handle_length)) );
	
	}
	
	$link_short = $short;
	//$link_short = str_replace('#', '%23', $link_short);
	
	if ($nofollow != 'no') {
		
		$rel = "rel='nofollow'";
	
	} else {
		
		$rel = '';
	
	}
	
	if (! is_feed ()) {
		
		return "<div class='essb-click-to-tweet".$template."' onclick=\"window.open('https://twitter.com/intent/tweet?text=" . urlencode ( $link_short ) . $handle_code . $bcttURL . "', 'essb_share_window', 'height=300,width=500,resizable=1,scrollbars=yes');\">
			<span class='essb-click-to-tweet-quote'>
			" . $short . "
			</span>
			<span class='essb-click-to-tweet-button'>" . $bcttBttn . "<span class='essb-click-to-tweet-button-icon'></span>
		</div>";
	} 
}

add_shortcode ( 'easy-ctt', 'essb_ctt_shortcode' );
add_shortcode ( 'easy-tweet', 'essb_ctt_shortcode' );

/*
 * Load the stylesheet to style the output. As of v4.1, defaults to a custom
 * stylesheet located in the root of the uploads folder at
 * wp-content/uploads/bcttstyle.css and falls back to the stylesheet bundled
 * with the plugin if the custom sheet is not present. @since 0.1
 */

function essb_ctt_scripts() {
	
	
		if (!ESSBCoreHelper::is_plugin_deactivated_on() && !ESSBCoreHelper::is_module_deactivate_on('ctt')) {
			//wp_register_style ( 'essb-cct-style', plugins_url ( 'assets/css/styles.css', __FILE__ ), false, ESSB3_VERSION, 'all' );
		    //$resource_builder = ESSBResourceBuilder::get_instance();
		    
		    essb_resource_builder()->add_static_resource(plugins_url ( 'assets/css/styles.css', __FILE__ ), 'essb-cct-style', 'css');
			//wp_enqueue_style ( 'essb-cct-style' );
		}
	

}
;

add_action ( 'wp_enqueue_scripts', 'essb_ctt_scripts' );	
	


