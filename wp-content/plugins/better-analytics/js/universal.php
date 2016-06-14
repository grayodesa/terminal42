<?php
$betterAnalyticsOptions = get_option('better_analytics');
$betterAnalyticsInternal = get_transient('ba_int');

$createOptions = array();

$baCategories = $baTags = array();
$baAuthor = $baRole = null;
$baYear = 0;

// get category ID from category archives pages
global $wp_query;
if (is_object($wp_query))
{
	$obj = $wp_query->get_queried_object();
	if (is_object($obj) && !empty($obj->cat_ID))
	{
		$baCategories[$obj->cat_ID] = $obj->name;
	}

	if (is_object($obj) && !empty($obj->term_id) && empty($obj->cat_ID))
	{
		$baTags[$obj->term_id] = $obj->name;
	}

}

if (!count($baCategories) && !is_front_page())
{
	$categoryList = get_the_category();
	if (count($categoryList))
	{
		foreach ($categoryList as $item)
		{
			if (intval($item->cat_ID) > 0)
			{
				$baCategories[$item->cat_ID] = $item->name;
			}
		}
	}

	if ($post = get_post())
	{
		$baYear = absint(substr(@$post->post_date, 0, 4));

		if ($post->post_author > 0)
		{
			$baAuthor = get_the_author_meta('display_name', $post->post_author);
		}

		if (!$baTags)
		{
			$tagList = wp_get_post_tags($post->ID);
			if (count($tagList))
			{
				foreach ($tagList as $tag)
				{
					$baTags[$tag->term_id] = $tag->name;
				}
			}
		}
	}

}

$jsonOptions = array('tid' => @$betterAnalyticsOptions['property_id']);

$currentUser = wp_get_current_user();
if (!$baRole = implode(',', (array)@$currentUser->roles))
{
	$baRole = 'guest';
}

if (@$betterAnalyticsOptions['track_userid'] && @$currentUser->ID > 0)
{
	$createOptions['userId'] = intval($currentUser->ID);
}

if (@$betterAnalyticsOptions['sample_rate'] > 0 && $betterAnalyticsOptions['sample_rate'] < 100)
{
	$createOptions['sampleRate'] = intval($betterAnalyticsOptions['sample_rate']);
}

if (@$betterAnalyticsOptions['events']['user_engagement'])
{
	$jsonOptions['et'] = (@$betterAnalyticsOptions['engagement_time'] > 0 && $betterAnalyticsOptions['engagement_time'] <= 600 ? intval($betterAnalyticsOptions['engagement_time']) : 15);
}

if (!$createOptions)
{
	$createOptions = 'auto';
}

$jsonOptions['co'] = $createOptions;


if (!empty($betterAnalyticsOptions['extra_js']))
{
	$jsonOptions['js'] = $betterAnalyticsOptions['extra_js'];
}

$jsonOptions['g'] =
	(@$betterAnalyticsInternal['v'] ? 1 : 0) +
	(@$betterAnalyticsOptions['link_attribution'] ? 4 : 0) +
	(@$betterAnalyticsOptions['track_userid'] ? 8 : 0) +
	(@$betterAnalyticsOptions['anonymize_ips'] ? 16 : 0) +
	(@$betterAnalyticsOptions['demographic_tracking'] ? 32 : 0) +
	(@$betterAnalyticsOptions['force_ssl'] ? 64 : 0) +
	(@$betterAnalyticsOptions['events']['user_engagement'] ? 128 : 0) +
	(@$betterAnalyticsOptions['events']['youtube'] ? 256 : 0) +
	(@$betterAnalyticsOptions['events']['link_click'] ? 512 : 0) +
	(@$betterAnalyticsOptions['events']['missing_images'] ? 1024 : 0) +
	(@$betterAnalyticsOptions['events']['ajax_request'] ? 2048 : 0) +
	(@$betterAnalyticsOptions['events']['error_js'] ? 4096 : 0) +
	(@$betterAnalyticsOptions['events']['error_ajax'] ? 8192 : 0) +
	(@$betterAnalyticsOptions['events']['error_console'] ? 16384 : 0) +
	(@$betterAnalyticsOptions['events']['error_youtube'] ? 32768 : 0) +
	(@$betterAnalyticsOptions['events']['error_404'] ? 65536 : 0) +
	(@$betterAnalyticsOptions['events']['page_scroll'] ? 131072 : 0) +
	(@$betterAnalyticsOptions['events']['time_on_page'] ? 262144 : 0) +

	(@$betterAnalyticsOptions['javascript']['run_time'] == 'immediately' ? 1073741824 : 0) +
	(@$betterAnalyticsOptions['debugging'] ? 2147483648 : 0)

;

if (!empty($betterAnalyticsOptions['events']['downloads']) && !empty($betterAnalyticsOptions['file_extensions']))
{
	$jsonOptions['dl'] = '\.' . implode('|\.', $betterAnalyticsOptions['file_extensions']);
}

$jsonOptions['s'] =
	(@$betterAnalyticsInternal['v'] ? 1 : 0) +
	(@$betterAnalyticsOptions['social']['facebook'] ? 2 : 0) +
	(@$betterAnalyticsOptions['social']['twitter'] ? 4 : 0) +
//	(@$betterAnalyticsOptions['social']['google'] ? 8 : 0) +
	(@$betterAnalyticsOptions['social']['pinterest'] ? 16 : 0) +
	(@$betterAnalyticsOptions['social']['linkedin'] ? 32 : 0)
;

$jsonOptions['a'] =
	(@$betterAnalyticsInternal['v'] ? 1 : 0) +
	(@$betterAnalyticsOptions['ads']['adsense'] ? 2 : 0) +
	(@$betterAnalyticsOptions['ads']['outbrain'] ? 4 : 0) +
	(@$betterAnalyticsOptions['ads']['taboola'] ? 8 : 0) +
	(@$betterAnalyticsOptions['ads']['digitalpoint'] ? 16 : 0) +
	(@$betterAnalyticsOptions['ads']['revcontent'] ? 32 : 0)
;

if (!empty($betterAnalyticsOptions['dimension']['category']) && $baCategories)
{
	$jsonOptions['d']['c'] = array(
		intval($betterAnalyticsOptions['dimension']['category']),
		implode(',', $baCategories)
	);
}

if (!empty($betterAnalyticsOptions['dimension']['author']) && $baAuthor)
{
	$jsonOptions['d']['a'] = array(
		intval($betterAnalyticsOptions['dimension']['author']),
		$baAuthor
	);
}

if (!empty($betterAnalyticsOptions['dimension']['tag']) && $baTags)
{
	$jsonOptions['d']['t'] = array(
		intval($betterAnalyticsOptions['dimension']['tag']),
		implode(',', $baTags)
	);
}

if (!empty($betterAnalyticsOptions['dimension']['year']) && $baYear)
{
	$jsonOptions['d']['y'] = array(
		intval($betterAnalyticsOptions['dimension']['year']),
		$baYear
	);
}

if (!empty($betterAnalyticsOptions['dimension']['role']) && $baRole)
{
	$jsonOptions['d']['r'] = array(
		intval($betterAnalyticsOptions['dimension']['role']),
		$baRole
	);
}

if (!empty($betterAnalyticsOptions['dimension']['user']) && @$currentUser->ID > 0)
{
	$jsonOptions['d']['u'] = array(
		intval($betterAnalyticsOptions['dimension']['user']),
		intval($currentUser->ID)
	);
}

if (DigitalPointBetterAnalytics_Base_Public::getInstance()->experimentId !== null)
{
	$jsonOptions['e'] = array(
		'i' => DigitalPointBetterAnalytics_Base_Public::getInstance()->experimentId,
		'v' => DigitalPointBetterAnalytics_Base_Public::getInstance()->experimentVariation
	);
}

echo "<meta id=\"ba_s\" property=\"options\" content=\"\" data-o=\"" . htmlentities(json_encode($jsonOptions)) . "\" />";