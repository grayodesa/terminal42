<?php
if (!function_exists('essb_rs_css_build_mobilesharebar_fix_code')) {
	function essb_rs_css_build_mobilesharebar_fix_code() {
		$snippet = '';
		
		$snippet .= ('.essb-mobile-sharebottom .essb_links { margin: 0px !important; }');
		$snippet .= ('.essb-mobile-sharebottom .essb_width_columns_2 li a { width: 100% !important; }');
		$snippet .= ('.essb-mobile-sharebottom .essb_width_columns_3 li a { width: 100% !important; }');
		$snippet .= ('.essb-mobile-sharebottom .essb_width_columns_4 li a { width: 100% !important; }');
		$snippet .= ('.essb-mobile-sharebottom .essb_width_columns_5 li a { width: 100% !important; }');
		$snippet .= ('.essb-mobile-sharebottom .essb_width_columns_6 li a { width: 100% !important; }');
		
		return $snippet;
	}
}