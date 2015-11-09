<?php

/*--------------------------------------------------------

    This File contains all the necessary shortcodes
    required for the ease of use of certain features of
    this Theme. Do not Edit if you are not sure about
    what are you upto..!!

--------------------------------------------------------*/


function my_formatter($content) {
       $new_content = '';
       $pattern_full = '{(\[raw\].*?\[/raw\])}is';
       $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
       $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

       foreach ($pieces as $piece) {
               if (preg_match($pattern_contents, $piece, $matches)) {
                       $new_content .= $matches[1];
               } else {
                       $new_content .= wptexturize(wpautop($piece));
               }
       }

       return $new_content;
}


function semi_fix_shortcodes($content){
    $array = array (
        '<p>[' => '[',
        ']</p>' => ']',
        ']<br />' => ']'
    );

    $content = strtr($content, $array);
    return $content;
}

add_filter('the_content', 'semi_fix_shortcodes');


/*--------------------------------------------------------
    Column Grids
--------------------------------------------------------*/


function semi_sh_col_full( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_full'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('full', 'semi_sh_col_full');


function semi_sh_col_half( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_half'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('half', 'semi_sh_col_half');


function semi_sh_col_half_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_half'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('half_last', 'semi_sh_col_half_l');


function semi_sh_col_onethird( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_one_third'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_third', 'semi_sh_col_onethird');


function semi_sh_col_onethird_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_one_third'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_third_last', 'semi_sh_col_onethird_l');


function semi_sh_col_twothird( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_two_third'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('two_third', 'semi_sh_col_twothird');


function semi_sh_col_twothird_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_two_third'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('two_third_last', 'semi_sh_col_twothird_l');


function semi_sh_col_onefourth( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_one_fourth'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_fourth', 'semi_sh_col_onefourth');


function semi_sh_col_onefourth_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_one_fourth'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_fourth_last', 'semi_sh_col_onefourth_l');


function semi_sh_col_threefourth( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_three_fourth'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('three_fourth', 'semi_sh_col_threefourth');


function semi_sh_col_threefourth_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_three_fourth'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('three_fourth_last', 'semi_sh_col_threefourth_l');


function semi_sh_col_onefifth( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_one_fifth'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_fifth', 'semi_sh_col_onefifth');


function semi_sh_col_onefifth_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_one_fifth'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_fifth_last', 'semi_sh_col_onefifth_l');


function semi_sh_col_twofifth( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_two_fifth'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('two_fifth', 'semi_sh_col_twofifth');


function semi_sh_col_twofifth_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_two_fifth'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('two_fifth_last', 'semi_sh_col_twofifth_l');


function semi_sh_col_threefifth( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_three_fifth'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('three_fifth', 'semi_sh_col_threefifth');


function semi_sh_col_threefifth_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_three_fifth'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('three_fifth_last', 'semi_sh_col_threefifth_l');


function semi_sh_col_fourfifth( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_four_fifth'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('four_fifth', 'semi_sh_col_fourfifth');


function semi_sh_col_fourfifth_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_four_fifth'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('four_fifth_last', 'semi_sh_col_fourfifth_l');


function semi_sh_col_onesixth( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_one_sixth'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('one_sixth', 'semi_sh_col_onesixth');


function semi_sh_col_onesixth_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_one_sixth'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('one_sixth_last', 'semi_sh_col_onesixth_l');


function semi_sh_col_fivesixth( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_five_sixth'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('five_sixth', 'semi_sh_col_fivesixth');


function semi_sh_col_fivesixth_l( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="col_five_sixth'. $class .' col_last">' . do_shortcode($content) . '</div><div class="clear"></div>';
}

add_shortcode('five_sixth_last', 'semi_sh_col_fivesixth_l');


/*--------------------------------------------------------
    Dividers
--------------------------------------------------------*/


function semi_sh_line( $atts, $content = null ) {
    return '<div class="line"></div>';
}

add_shortcode('line', 'semi_sh_line');


function semi_sh_doubleline( $atts, $content = null ) {
    return '<div class="double-line"></div>';
}

add_shortcode('doubleline', 'semi_sh_doubleline');

function semi_sh_dottedline( $atts, $content = null ) {
    return '<div class="dotted-divider"></div>';
}

add_shortcode('dottedline', 'semi_sh_dottedline');


function semi_sh_clear( $atts, $content = null ) {
    return '<div class="clear"></div>';
}

add_shortcode('clear', 'semi_sh_clear');


function semi_sh_divider( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'type' => ''
	), $atts));

    if( $type == 'line' ) {
        return do_shortcode( '[line]' );
    } elseif( $type == 'doubleline' ) {
        return do_shortcode( '[doubleline]' );
    } elseif( $type == 'clear' ) {
        return do_shortcode( '[clear]' );
    } elseif( $type == 'dottedline' ) {
        return do_shortcode( '[dottedline]' );
    }

}

add_shortcode('divider', 'semi_sh_divider');


/*--------------------------------------------------------
    Buttons
--------------------------------------------------------*/


function semi_sh_simple_button( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'url' => '#',
        'scrollid' => '',
        'size' => '',
        'type' => '',
        'icon' => '',
        'target' => '_self',
        'class' => ''
	), $atts));

    if( $size == 'large' ) { $size = ' large'; } else { $size = ''; }

    if( $icon != '' AND $icon != 'none' ) { $icon = '<i class="' . $icon . '"></i> '; } else { $icon = ''; }

    if( $type == 'inverse' ) { $type = ' inverse'; } else { $type = ''; }

    if( $scrollid != '' ) { $scrollid = ' data-scrollto="#' . $scrollid . '"'; }

    if( $class != '' ) { $class = ' ' . $class; }

    return '<a href="'. $url .'" target="'. $target .'" class="simple-button' . $size . $type . $class . '"' . $scrollid . '>'. $icon . trim($content) .'</a>';

}

add_shortcode('button', 'semi_sh_simple_button');


function semi_sh_border_button( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'url' => '#',
        'scrollid' => '',
        'size' => '',
        'type' => '',
        'icon' => '',
        'target' => '_self',
        'class' => ''
    ), $atts));

    if( $size == 'large' ) { $size = ' large'; } else { $size = ''; }

    if( $icon != '' AND $icon != 'none' ) { $icon = '<i class="' . $icon . '"></i> '; } else { $icon = ''; }

    if( $type == 'inverse' ) { $type = ' inverse'; } else { $type = ''; }

    if( $scrollid != '' ) { $scrollid = ' data-scrollto="#' . $scrollid . '"'; }

    if( $class != '' ) { $class = ' ' . $class; }

    return '<a href="'. $url .'" target="'. $target .'" class="border-button' . $size . $type . $class . '"' . $scrollid . '>'. $icon . trim($content) .'</a>';

}

add_shortcode('borderbutton', 'semi_sh_border_button');


function semi_sh_alt_button( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'url' => '#',
        'scrollid' => '',
        'style' => '',
        'icon' => '',
        'target' => '_self',
        'class' => ''
	), $atts));

    if( $style != '' ) { $style = ' ' . $style . '_btn'; }

    if( $icon != '' AND $icon != 'none' ) { $icon = '<i class="' . $icon . '"></i> '; } else { $icon = ''; }

    if( $scrollid != '' ) { $scrollid = ' data-scrollto="#' . $scrollid . '"'; }

    if( $class != '' ) { $class = ' ' . $class; }

    return '<a href="'. $url .'" target="'. $target .'" class="button' . $style . $class . '"' . $scrollid . '><span>'. $icon . trim($content) .'</span></a>';

}

add_shortcode('altbutton', 'semi_sh_alt_button');


/*--------------------------------------------------------
    Styled Boxes
--------------------------------------------------------*/


function semi_sh_stylebox( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'type' => ''
    ), $atts));

    if( $type ) { $type = $type . 'msg '; }

	$output .= '<div class="style-msg ' . $type . 'clearfix"><div class="sb_msg clearfix">' . $content . '</div></div>';

    return $output;

}

add_shortcode('stylebox', 'semi_sh_stylebox');


function semi_sh_stylebox2( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'title' => '',
		'type' => ''
    ), $atts));

    if( $type ) { $type = $type . 'msg '; }

    $output .= '<div class="style-msg2 ' . $type . 'clearfix"><div class="msgtitle clearfix">'. $title .'</div><div class="sb_msg clearfix">' . $content . '</div></div>';

    return $output;

}

add_shortcode('stylebox2', 'semi_sh_stylebox2');


function semi_sh_alert( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'close' => 'true',
		'type' => ''
    ), $atts));

    if( $type ) { $type = 'alert-' . $type; }

    if( $close == 'true' ) { $close = '<button type="button" class="close" data-dismiss="alert">&times;</button>'; } else { $close = ''; }

    $output .= '<div class="alert ' . $type . '">' . $close . $content . '</div>';

    return $output;

}

add_shortcode('alert', 'semi_sh_alert');


/*--------------------------------------------------------
    Feature Blocks
--------------------------------------------------------*/


function semi_sh_feature( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'icon' => '',
        'iconurl' => '',
        'url' => '',
        'title' => '',
        'style' => ''
	), $atts));

    $icon_start = '';
    $title_start = '';
    $link_end = '';

    if( $icon != '' AND $icon != 'none' AND !checkimage( $iconurl ) ) { $icon = '<span class="' . $icon . '"></span> '; } else { $icon = ''; }

    if( checkimage( $iconurl ) ) { $icon = '<img src="' . $iconurl . '" alt="' . strip_tags( $title ) . '" />'; }

    if( $style == 2 ) {
        $style = ' product-feature2';
    } elseif( $style == 3 ) {
        $style = ' product-feature3';
    } else {
        $style = '';
    }

    if( checkurl( $url ) ) {
        $icon_start = '<a href="' . $url . '" class="pf-icon">';
        $title_start = '<a href="' . $url . '">';
        $link_end = '</a>';
    }

    return '<div class="product-feature' . $style . '">' . $icon_start . $icon . $link_end . '<h3>' . $title_start . $title . $link_end . '</h3><p>' . trim( $content ) . '</p></div>';

}

add_shortcode('feature', 'semi_sh_feature');


/*--------------------------------------------------------
    Audio
--------------------------------------------------------*/


function semi_sh_audio( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'mp3' => '',
        'ogg' => '',
        'wav' => ''
    ), $atts));

    $output = '<audio preload="auto" controls>';
    $output .= ( checkurl( $mp3 )  ? '<source src="' . $mp3 . '">' : '' );
    $output .= ( checkurl( $ogg )  ? '<source src="' . $ogg . '">' : '' );
    $output .= ( checkurl( $wav )  ? '<source src="' . $wav . '">' : '' );
    $output .= '</audio><div class="clear"></div>';

    return $output;

}

add_shortcode('audio', 'semi_sh_audio');


/*--------------------------------------------------------
    Featured Icons
--------------------------------------------------------*/


function semi_sh_icon( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'icon' => '',
        'class' => '',
        'style' => '',
        'type' => '',
        'color' => '',
        'url' => '',
        'target' => ''
	), $atts));

    if( $style == 'light' ) {
        $style = ' icon-light';
    } else { $style = ''; }

    if( $type == 'circle' ) {
        $type = 'icon-circled';
    } elseif( $type == 'plain' ) {
        $type = 'icon-plain';
    } else { $type = 'icon-rounded'; }

    if( $icon != '' AND $icon != 'none' ) { $icon = ' ' . $icon; } else { $icon = ''; }

    if( $color != '' ) { $color = ' style="background-color: ' . $color . ';"'; }

    if( $class != '' ) { $class = ' ' . $class; }

    if( checkurl( $url ) ) {
        return '<a href="' . $url . '" target="' . $target . '" class="' . $type . $style . $icon . $class . '"' . $color . '></a>';
    } else {
        return '<i class="' . $type . $style . $icon . $class . '"' . $color . '></i>';
    }

}

add_shortcode('icon', 'semi_sh_icon');


/*--------------------------------------------------------
    Drop Caps &amp; Highlights
--------------------------------------------------------*/


function semi_sh_dropcap( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'style' => '',
        'text' => '',
        'color' => ''
	), $atts));

    if($color != '') {
        $color = ' style="background-color: '. $color .' !important;"';
    } else { $color = ''; }

    if( $style == 1 ) {
        $style = '';
        $color = '';
    }

	return $text ? '<span class="dropcap'. $style .'"'. $color .'>' . $text . '</span>' : '';

}

add_shortcode('dropcap', 'semi_sh_dropcap');


function semi_sh_highlight( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'color' => ''
	), $atts));

    if($color != '') {
        $color = ' style="background-color: '. $color .' !important;"';
    } else { $color = ''; }

	return '<span class="highlight"'. $color .'>' . $content . '</span>';

}

add_shortcode('highlight', 'semi_sh_highlight');


/*--------------------------------------------------------
    Blockquotes
--------------------------------------------------------*/


function semi_sh_blockquote( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'style' => '',
        'align' => ''
	), $atts));

    if( $style != '' ) {
        $style = ' quote';
    }

    if( $align == 'left' ) {
        $align = ' quote-left';
    } elseif( $align == 'right' ) {
        $align = ' quote-right';
    }

    return '<blockquote class="'. $style . $align .'"><p>' . $content . '</p></blockquote>';

}

add_shortcode('blockquote', 'semi_sh_blockquote');


/*--------------------------------------------------------
    Pricing Boxes
--------------------------------------------------------*/


function semi_sh_pricing( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'col' => '',
        'style' => '',
        'cssstyle' => '',
        'class' => ''
	), $atts));

    if( $col != '' ) {

        $col = intval( $col );

    } else { $col = 4; }

    if( $class != '' ) { $class = ' ' . $class; }

    if( $style == '2' ) { $style = ' pricing-style2'; } else { $style = ''; }

    if( is_int( $col ) AND $col >= 3 AND $col <= 5 ) {

        if( $col == 4 ) { $col = ''; } else { $col = ' pricing' . $col; }

        return '<div class="pricing' . $col . $style . $class . ' clearfix" style="' . $cssstyle . '">'. do_shortcode( $content ) .'</div>';

    }

}

add_shortcode('pricing', 'semi_sh_pricing');


function semi_sh_price( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'define' => '',
        'title' => '',
        'subtitle' => '',
        'price' => '',
        'pricesub' => '',
        'tenure' => '',
        'button' => 'Buy Now',
        'icon' => '',
        'url' => '#',
        'best' => ''
	), $atts));

    if( $subtitle != '' ) { $subtitle = '<span>' . $subtitle . '</span>'; }

    if( $tenure != '' ) { $tenure = '<span class="price-tenure">' . $tenure . '</span>'; }

    if( $pricesub != '' ) { $pricesub = '<span class="price-sub">' . $pricesub . '</span>'; }

    if( $best == 'true' ) { $best = ' best-price'; $bestbtn = ' inverse'; } else { $bestbtn = ''; }

    if( $icon != '' AND $icon != 'none' ) { $icon = '<i class="' . $icon .'"></i> '; } else { $icon = ''; }

    if( $define == 'true' ) {
        return '<div class="pricing-wrap pricing-defines"><div class="pricing-inner"><div class="pricing-features"><ul>'. strip_tags( do_shortcode( $content ), '<a><span><li><strong><em><del>' ) .'</ul></div></div></div>';
    } else {
        return '<div class="pricing-wrap' . $best . '"><div class="pricing-inner"><div class="pricing-title"><h4>' . $title . $subtitle . '</h4></div><div class="pricing-price">' . $price . $pricesub . $tenure . '</div><div class="pricing-features"><ul>'. strip_tags( $content, '<a><span><li><strong><em>' ) .'</ul></div><div class="pricing-action"><a href="' . $url . '" class="simple-button' . $bestbtn . '">' . $icon . $button . '</a></div></div></div>';
    }

}

add_shortcode('price', 'semi_sh_price');


function semi_sh_pricingfaq( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'text' => ''
	), $atts));

    return '<a href="#" class="icon-question-sign etip" title="' . $text . '"></a>';

}

add_shortcode('pricingfaq', 'semi_sh_pricingfaq');


/*--------------------------------------------------------
    Google Map
--------------------------------------------------------*/


function semi_sh_gmap( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'height' => '',
        'latitude' => '',
        'longitude' => '',
        'address' => '',
        'zoom' => '12',
        'type' => 'ROADMAP',
        'scrollwheel' => 'false',
        'pan' => 'true',
        'zoomc' => 'true',
        'maptypec' => 'true',
        'scale' => 'false',
        'streetview' => 'false',
        'overviewmap' => 'false',
        'markers' => '',
        'class' => '',
        'style' => ''
	), $atts));

    $id = 'mapid-' . mt_rand(10, 100000);

    $height = is_numeric( $height ) ? $height : 200;

    if( $address == '' AND is_numeric( $latitude ) AND is_numeric( $longitude ) ):

        $position = 'latitude: '. $latitude .', longitude: '. $longitude .',';

    elseif( $address != '' ):

        $position = 'address: \''. $address .'\',';

    endif;

    $markers = explode( ';', $markers );

    $markerdata = array();

    $marker_data = '';

    foreach( $markers as $marker ) {

        $marker = explode( '|', $marker );

        if( count( $marker ) == 4 ) {

            if( $marker[2] == '' AND is_numeric( $marker[0] ) AND is_numeric( $marker[1] ) ) {

                $marker_data .= 'latitude: '. $marker[0] .', longitude: '. $marker[1] .',';

            } elseif( $marker[2] != '' ) {

                $marker_data .= 'address: \''. $marker[2] .'\',';

            }

            $marker_data .= 'html: \''. esc_html( $marker[3] ) .'\'';

            $markerdata[] = '{' . $marker_data . '}';

        }

        $marker_data = '';

    }

    $markers = implode( ',', $markerdata );

    if( $markers != '' ) { $markers = 'markers:[ '.$markers.' ],'; }

    $script = '<script type="text/javascript">

                    jQuery(\'#'. $id .'\').gMap({
                        '.$position.'
                         maptype: \''.$type.'\',
                         zoom: '.$zoom.',
                         scrollwheel: '.$scrollwheel.',
                         '.$markers.'
                         controls: {
                             panControl: '.$pan.',
                             zoomControl: '.$zoomc.',
                             mapTypeControl: '.$maptypec.',
                             scaleControl: '.$scale.',
                             streetViewControl: '.$streetview.',
                             overviewMapControl: '.$overviewmap.'
                         }
                    });

                </script>';

	return '<div id="'. $id .'" style="height: '. $height .'px; '. $style .'" class="' . $class . ' gmap"></div>' . $script;

}

add_shortcode('gmap', 'semi_sh_gmap');


/*--------------------------------------------------------
    Team Member
--------------------------------------------------------*/


function semi_sh_team( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'id' => '',
        'shape' => '',
        'tsclass' => ''
	), $atts));

    $teamargs = array( 'post_type' => 'team', 'posts_per_page' => 1 );

    if( intval( $id ) ) { $teamargs['p'] = $id; }

    $team = new WP_Query( $teamargs );

    if( $team->have_posts() ):

    while ( $team->have_posts() ) : $team->the_post();

    if( $shape == 'rounded' ) {
        $thumb = get_resized_image( 400, 400 );
        $shape = ' team-rounded';
    } else {
        $thumb = get_resized_image( 400, 300 );
        $shape = '';
    }

    $skills = get_post_meta( get_the_ID(), 'semi_team_skills', TRUE );

    $skillsli = '';

    foreach( $skills as $skill ) {
        $skillsli .= '<li><span class="icon-check"></span>' . $skill . '</li>';
    }

    if( count( $skills ) > 0 ) {
        $skillsul = '<ul class="team-skills">' . $skillsli . '</ul>';
    }

    $facebook = get_post_meta( get_the_ID(), 'semi_team_facebook', TRUE ) != '' ? '<a href="' . get_post_meta( get_the_ID(), 'semi_team_facebook', TRUE ) . '"><img src="' . get_template_directory_uri() . '/images/staff/social/facebook.png" class="ntip" alt="Facebook" title="Facebook" /></a>' : '';

    $twitter = get_post_meta( get_the_ID(), 'semi_team_twitter', TRUE ) != '' ? '<a href="' . get_post_meta( get_the_ID(), 'semi_team_twitter', TRUE ) . '"><img src="' . get_template_directory_uri() . '/images/staff/social/twitter.png" class="ntip" alt="Twitter" title="Twitter" /></a>' : '';

    $dribbble = get_post_meta( get_the_ID(), 'semi_team_dribbble', TRUE ) != '' ? '<a href="' . get_post_meta( get_the_ID(), 'semi_team_dribbble', TRUE ) . '"><img src="' . get_template_directory_uri() . '/images/staff/social/dribbble.png" class="ntip" alt="Dribbble" title="Dribbble" /></a>' : '';

    $forrst = get_post_meta( get_the_ID(), 'semi_team_forrst', TRUE ) != '' ? '<a href="' . get_post_meta( get_the_ID(), 'semi_team_forrst', TRUE ) . '"><img src="' . get_template_directory_uri() . '/images/staff/social/forrst.png" class="ntip" alt="Forrst" title="Forrst" /></a>' : '';

    $flickr = get_post_meta( get_the_ID(), 'semi_team_flickr', TRUE ) != '' ? '<a href="' . get_post_meta( get_the_ID(), 'semi_team_flickr', TRUE ) . '"><img src="' . get_template_directory_uri() . '/images/staff/social/flickr.png" class="ntip" alt="Flickr" title="Flickr" /></a>' : '';

    $teamdiv = '<div class="team-member' . $shape . '">

                    <div class="team-image">

                        <img src="' . $thumb . '" alt="' . the_title_attribute( array( 'echo' => false ) ) . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '" />

                        <span>' . get_post_meta( get_the_ID(), 'semi_team_designation', TRUE ) . '</span>

                    </div>

                    <div class="team-desc">

                        <h4>' . get_the_title() . '</h4>

                        <p>' . get_post_meta( get_the_ID(), 'semi_team_description', TRUE ) . '</p>

                        <div class="team-social ' . $tsclass . '">

                            ' . $facebook . '
                            ' . $twitter . '
                            ' . $dribbble . '
                            ' . $forrst . '
                            ' . $flickr . '
                            ' . get_post_meta( get_the_ID(), 'semi_team_addicons', TRUE ) . '

                        </div>

                    </div>

                    ' . $skillsul . '

                </div>';

    endwhile;

    endif; wp_reset_postdata();

	return $teamdiv;

}

add_shortcode('team', 'semi_sh_team');


/*--------------------------------------------------------
    Tabs
--------------------------------------------------------*/


function semi_sh_tabs( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'type' => '',
        'style' => '',
        'class' => '',
        'icons' => '',
        'titletype' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    $id = 'tabwidget-' . mt_rand(10, 100000);

    if( $type == 'tour' ) {
        $type = ' side-tabs';
    } elseif( $type != '' ) {
        $type = '';
    }

    if( $icons == 'true' ) {

        preg_match_all('/tab title="([^\"]+)" icon="([^\"]+)" id="([^\"]+)"](.*?)\[\/tab\]/is', $content, $matches, PREG_OFFSET_CAPTURE);

        if( isset( $matches[1] ) ) {

            $tab_titles = $matches[1];

            $tabt = array();

            foreach( $tab_titles as $tab_title ) {
                $tabt[] = $tab_title[0];
            }

        }

        if( isset( $matches[2] ) ) {

            $tab_icons = $matches[2];

            $tabi = array();

            foreach( $tab_icons as $tab_icon ) {
                $tabi[] = $tab_icon[0];
            }

        }

        if( isset( $matches[3] ) ) {

            $tab_ids = $matches[3];

            $tabid = array();

            foreach( $tab_ids as $tab_id ) {

                if( $tab_id[0] != '' ) {
                    $tabid[] = $tab_id[0];
                } else {
                    $tabid[] = 'noid';
                }

            }

        }

        if( isset( $matches[4] ) ) {

            $tab_contents = $matches[4];

            $tabc = array();

            foreach( $tab_contents as $tab_content ) {
                $tabc[] = $tab_content[0];
            }

        }

    } else {

        preg_match_all('/tab title="([^\"]+)" id="([^\"]+)"](.*?)\[\/tab\]/is', $content, $matches, PREG_OFFSET_CAPTURE);

        if( isset( $matches[1] ) ) {

            $tab_titles = $matches[1];

            $tabt = array();

            foreach( $tab_titles as $tab_title ) {
                $tabt[] = $tab_title[0];
            }

        }

        if( isset( $matches[2] ) ) {

            $tab_ids = $matches[2];

            $tabid = array();

            foreach( $tab_ids as $tab_id ) {

                if( $tab_id[0] != '' ) {
                    $tabid[] = $tab_id[0];
                } else {
                    $tabid[] = 'noid';
                }

            }

        }

        if( isset( $matches[3] ) ) {

            $tab_contents = $matches[3];

            $tabc = array();

            foreach( $tab_contents as $tab_content ) {
                $tabc[] = $tab_content[0];
            }

        }

    }

    if( isset( $tabt ) AND isset( $tabc ) ) {

        $titlec = count( $tabt );

        $contentc = count( $tabc );

        $tablist = '';

        $tabcontent = '';

        if( $titlec === $contentc ) {

            for( $i = 0; $i < $titlec; $i++ ) {

                if( $tabid[$i] != '' AND $tabid[$i] != 'noid' ) {
                    $itabid = 'tabid-' . strtolower( strtolower( str_replace( array("!","@","#","$","%","^","&","*",")","(","+","=","[","]","/","\\",";","{","}","|",'"',":","<",">","?","~","`"," "), "", $tabid[$i] ) ) ); } else {
                    $itabid = 'tabid-' . mt_rand(10, 100000) . '-' . strtolower( strtolower( str_replace( array("!","@","#","$","%","^","&","*",")","(","+","=","[","]","/","\\",";","{","}","|",'"',":","<",">","?","~","`"," "), "", $tabt[$i] ) ) );
                }

                if( isset( $tabi ) AND $tabi[$i] != '' AND $tabi[$i] != 'none' ) {
                    $icon = '<i class="' . $tabi[$i] . '"></i> ';

                    if( $titletype == 'icon' AND $icons == true AND $type != ' side-tabs' ) {
                        $icon = '<i class="' . $tabi[$i] . ' norightmargin"></i>';
                    }

                } else { $icon = ''; }

                if( $titletype == 'icon' AND $type != ' side-tabs' ) {
                    $tablist .= '<li class="ntip" title="' . $tabt[$i] . '"><a href="#'. $itabid .'" data-href="#'. $itabid .'">'. $icon .'</a></li>';
                } else {
                    $tablist .= '<li><a href="#'. $itabid .'" data-href="#'. $itabid .'">'. $icon . $tabt[$i] .'</a></li>';
                }

                $tabcontent .= '<div id="'. $itabid .'" class="tab_content clearfix">'. do_shortcode( $tabc[$i] ) .'</div>';

            }

        }

    }

    $taboutput = '<ul class="tabs">'. $tablist .'</ul><div class="tab_container">'. $tabcontent .'</div>';

    $tabscript = '<script type="text/javascript">jQuery(document).ready(function($) { tab_widget( \'#'. $id .'\' ); });</script>';

	return '<div class="tab_widget'. $type . $class .' clearfix" id="'. $id .'" style="'. $style .'">' . $taboutput . '</div><div class="clear"></div>' . $tabscript;

}

add_shortcode('tabs', 'semi_sh_tabs');


/*--------------------------------------------------------
    Toggles
--------------------------------------------------------*/


function semi_sh_toggle( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'title' => ''
	), $atts));

	return '<div class="toggle clearfix"><div class="togglet"><span>' . $title . '</span></div><div class="togglec clearfix">' . do_shortcode( trim( $content ) ) . '</div></div><div class="clear"></div>';

}

add_shortcode('toggle', 'semi_sh_toggle');


/*--------------------------------------------------------
    Accordions
--------------------------------------------------------*/


function semi_sh_accordions( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'class' => ''
	), $atts));

    return '<div class="accordion ' . $class . ' clearfix">' . do_shortcode( trim( $content ) ) . '</div><div class="clear"></div>';

}

add_shortcode('accordions', 'semi_sh_accordions');


function semi_sh_accordion( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'title' => ''
	), $atts));

	return '<div class="acctitle"><span>' . $title . '</span></div><div class="acc_content clearfix">' . do_shortcode( $content ) . '</div>';

}

add_shortcode('accordion', 'semi_sh_accordion');


/*--------------------------------------------------------
    Callout Box
--------------------------------------------------------*/


function semi_sh_promo( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'button' => '',
        'url' => '#',
        'title' => '',
        'icon' => '',
        'target' => '_self',
        'style' => '',
        'class' => ''
	), $atts));

    if( $target == '' ) { $target = '_self'; }

    if( $icon != '' AND $icon != 'none' ) { $icon = '<i class="' . $icon . '"></i> '; } else { $icon = ''; }

    return '<div class="promo '. $class .'" style="'. $style .'"><div class="promo-desc"><h3>'. $title .'</h3>'. ( $content ? '<span>' . $content . '</span>' : '' ) .'</div><div class="promo-action"><a href="'. $url .'" target="'. $target .'">'. $icon . $button .'</a></div></div>';

}

add_shortcode('promo', 'semi_sh_promo');


function semi_sh_callout( $atts, $content = null ) {
    extract(shortcode_atts(array(
		'class' => ''
	), $atts));

    if( $class != '' ) { $class = ' ' . $class; }

    return '<div class="well callout'. $class .'">' . do_shortcode($content) . '</div>';
}

add_shortcode('callout', 'semi_sh_callout');


/*--------------------------------------------------------
    Skills
--------------------------------------------------------*/


function semi_sh_skills( $atts, $content = null ) {

    return '<ul class="skills">' . do_shortcode( trim( $content ) ) . '</ul>';

}

add_shortcode('skills', 'semi_sh_skills');


function semi_sh_skill( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'text' => '',
        'percent' => '',
        'style' => ''
	), $atts));

    if( $style == 'success' ) {
        $style = ' bar-success';
    } elseif( $style == 'warning' ) {
        $style = ' bar-warning';
    } elseif( $style == 'danger' ) {
        $style = ' bar-danger';
    }

    return '<li><span>' . $text . ' &middot; ' . $percent . '%</span><div class="progress"><div class="bar' . $style . '" data-width="' . $percent . '"></div></div></li>';

}

add_shortcode('skill', 'semi_sh_skill');


/*--------------------------------------------------------
    Icon List
--------------------------------------------------------*/


function semi_sh_iconlist( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'icon' => '',
        'class' => '',
        'style' => ''
    ), $atts));

    return '<ul class="' . $class . '" style="' . $style . '" data-icon="' . $icon . '">' . strip_tags( $content, '<li><a><strong><em>' ) . '</ul>';

}

add_shortcode('iconlist', 'semi_sh_iconlist');



/*--------------------------------------------------------
    Testimonials
--------------------------------------------------------*/


function semi_sh_testimonials( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'number' => '',
        'display' => '',
        'auto' => '',
        'speed' => '',
        'pause' => '',
        'tlimit' => '',
        'include' => ''
	), $atts));

    $number = intval( $number );

    if( $display == 'recent' ) {

        $testiargs = array( 'post_type' => 'testimonials', 'posts_per_page' => $number, 'orderby' => 'date' );

    } elseif( $display == 'random' ) {

        $testiargs = array( 'post_type' => 'testimonials', 'posts_per_page' => $number, 'orderby' => 'rand' );

    } elseif( $display == 'menu_order' ) {

        $testiargs = array( 'post_type' => 'testimonials', 'posts_per_page' => $number, 'orderby' => 'menu_order', 'order' => 'ASC' );

    } else {

        $testiargs = array( 'post_type' => 'testimonials', 'posts_per_page' => $number, 'orderby' => 'date' );

    }

    if( $include != '' ) {
        $include = explode( ',', $include );
        if( count( $include ) > 0 ) {
            $testiargs['post__in'] = $include;
        }
    }

    $output = '';

    if( $auto == 'true' ) { $auto = ' data-auto="true"'; } else { $auto = ''; }

    if( $speed != '' AND is_numeric( $speed ) ) { $speed = ' data-speed="' . $speed . '"'; } else { $speed = ''; }

    if( $pause != '' AND is_numeric( $pause ) ) { $pause = ' data-pause="' . $pause . '"'; } else { $pause = ''; }

    $getmrand = mt_rand(1,1000);

    $testimonials = new WP_Query( $testiargs );

    if( $testimonials->have_posts() ):

    $output .= '<div class="testimonial-scroller" data-prev="#testimonials-widget-' . $getmrand . '-prev" data-next="#testimonials-widget-' . $getmrand . '-next" ' . $auto . $speed . $pause . '><div class="testimonials">';

    while ( $testimonials->have_posts() ) : $testimonials->the_post();

    $testimonial_text = strip_tags( get_post_meta( get_the_ID(), 'semi_testimonials_text', true ) );

    $testitext = $tlimit ? custom_textlimit( $testimonial_text, $tlimit ) : $testimonial_text;

    $authorname = get_post_meta( get_the_ID(), 'semi_testimonials_company', true ) ? get_post_meta( get_the_ID(), 'semi_testimonials_company', true ) : get_post_meta( get_the_ID(), 'semi_testimonials_url', true );

    if( checkurl( get_post_meta( get_the_ID(), 'semi_testimonials_url', true ) ) ) {
        $testi_span = '<span><a href="' . get_post_meta( get_the_ID(), 'semi_testimonials_url', true ) . '" target="_blank">' . $authorname . '</a></span>';
    } elseif( get_post_meta( get_the_ID(), 'semi_testimonials_company', true ) != '' AND !checkurl( get_post_meta( get_the_ID(), 'semi_testimonials_url', true ) ) ) {
        $testi_span = '<span>' . get_post_meta( get_the_ID(), 'semi_testimonials_company', true ) . '</span>';
    } else { $testi_span = ''; }

    $output .= '<div class="testimonial-item">

                    <div class="testi-content">' . $testitext . '</div>

                    <div class="testi-author">' . get_post_meta( get_the_ID(), 'semi_testimonials_author', true ) . $testi_span . '</div>

                </div>';

    endwhile;

    $output .= '</div></div><div id="testimonials-widget-' . $getmrand . '-prev" class="widget-scroll-prev"></div><div id="testimonials-widget-' . $getmrand . '-next" class="widget-scroll-next"></div>';

    endif; wp_reset_postdata();

    return $output;

}

add_shortcode('testimonials', 'semi_sh_testimonials');


/*--------------------------------------------------------
    Clients
--------------------------------------------------------*/


function semi_sh_clients( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'display' => '',
        'number' => 12,
        'include' => ''
	), $atts));

    $number = intval( $number );

    if( $display == 'recent' ) {

        $args = array( 'post_type' => 'clients', 'posts_per_page' => $number, 'orderby' => 'date' );

    } elseif( $display == 'random' ) {

        $args = array( 'post_type' => 'clients', 'posts_per_page' => $number, 'orderby' => 'rand' );

    } elseif( $display == 'menu_order' ) {

        $args = array( 'post_type' => 'clients', 'posts_per_page' => $number, 'orderby' => 'menu_order', 'order' => 'ASC' );

    } else {

        $args = array( 'post_type' => 'clients', 'posts_per_page' => $number, 'orderby' => 'date' );

    }

    if( $include != '' ) {
        $include = explode( ',', $include );
        if( count( $include ) > 0 ) {
            $args['post__in'] = $include;
        }
    }

    $getmrand = mt_rand(1,1000);

    $clients = new WP_Query( $args );

    if( $clients->have_posts() ):

    $clientdiv = '<ul id="clients-scroller-' . $getmrand . '" class="our-clients clearfix">';

    while ( $clients->have_posts() ) : $clients->the_post();

    $thumb = get_resized_image( 140, 90 );

    $clientdiv .= '<li><a href="' . get_post_meta( get_the_ID(), 'semi_clients_url', true ) . '" target="_blank"><img src="' . $thumb . '" alt="' . the_title_attribute( array( 'echo' => false ) ) . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '" /></a></li>';

    endwhile;

    $clientdiv .= '</ul><div class="widget-scroll-prev" id="ourclients-prev-' . $getmrand . '"></div><div class="widget-scroll-next" id="ourclients-next-' . $getmrand . '"></div>';

    $clientdiv .= '<script type="text/javascript">

                        jQuery(document).ready(function($) {

                            var clientsCarousel = $("#clients-scroller-' . $getmrand . '");

                            clientsCarousel.carouFredSel({
                                width : "100%",
                                height : "auto",
                                circular : false,
                                responsive : true,
                                infinite : false,
                                auto : false,
                                items : {
                                    width : 160,
                                    visible: {
                                        min: 1,
                                        max: 6
                                    }
                                },
                                scroll : {
                                    wipe : true
                                },
                                prev : {
                                    button : "#ourclients-prev-' . $getmrand . '",
                                    key : "left"
                                },
                                next : {
                                    button : "#ourclients-next-' . $getmrand . '",
                                    key : "right"
                                },
                                onCreate : function () {
                                    $(window).on(\'resize\', function(){
                                        clientsCarousel.parent().add(clientsCarousel).css(\'height\', clientsCarousel.children().first().outerHeight() + \'px\');
                                    }).trigger(\'resize\');
                                }
                            });

                        });

                    </script>';

    endif; wp_reset_postdata();

	return $clientdiv;

}

add_shortcode('clients', 'semi_sh_clients');


/*--------------------------------------------------------
    Individual Posts
--------------------------------------------------------*/


function semi_sh_ipost( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'id' => '',
        'layout' => '',
        'media' => '',
        'meta' => '',
        'desc' => '',
        'tlimit' => ''
	), $atts));

    $output = '';

    if( $layout == 'full' ) {
        $layout_s .= '<div class="col_full">';
        $layout_e .= '<div class="col_full nobottommargin col_last">';
    } else {
        $layout_s .= '<div class="col_half nobottommargin">';
        $layout_e .= '<div class="col_half nobottommargin col_last">';
    }

    $tlimit = is_numeric( $tlimit ) ? $tlimit : 20;

    $ipost = new WP_Query( array( 'p' => $id ) );

    if( $ipost->have_posts() ):

    $output .= '<div class="ipost clearfix">';

    while ( $ipost->have_posts() ) : $ipost->the_post();

    if( $media != 'false' ) {

        $output .= $layout_s;

        if( get_post_format() == 'video' OR get_post_format() == 'audio' ) {

            $output .= stripslashes( htmlspecialchars_decode( get_post_meta( get_the_ID(), 'semi_post_embed', TRUE ) ) );

        } elseif( get_post_format() == 'gallery' ) {

            $output .= '<div class="ipost-image"><div class="fslider" ' . get_fslider_ops( 'semi_post_' ) . '><div class="flexslider"><div class="slider-wrap">';

            if( has_post_thumbnail() ):

            $thumb = get_sized_image( 'medium' );

            $output .= '<div class="slide"><a href="' . get_permalink() . '"><img src="' . $thumb . '" alt="' . the_title_attribute( array( 'echo' => false ) ) . '" /></a></div>';

            endif;

            $gallery = rwmb_meta( 'semi_post_gallery', 'type=image&size=medium' );

            foreach ( $gallery as $gallery_image ):

            $output .= '<div class="slide"><a href="' . get_permalink() . '"><img src="' . $gallery_image['url'] . '" alt="' . $gallery_image['alt'] . '" /></a></div>';

            endforeach;

            $output .= '</div></div></div><div class="post-overlay icon-' . get_post_icon() . '"></div></div>';

        } else {

            if( has_post_thumbnail() ):

            $thumb = get_sized_image( 'medium' );

            $output .= '<div class="ipost-image"><a href="' . get_permalink() . '" class="image_fade"><img src="' . $thumb . '" title="' . the_title_attribute( array( 'echo' => false ) ) . '" alt="' . the_title_attribute( array( 'echo' => false ) ) . '" /></a><div class="post-overlay icon-' . get_post_icon() . '"></div></div>';

            endif;

        }

        $output .= '</div>';

    }

    $output .= $layout_e;

    $output .= '<div class="ipost-title"><h5><a href="' . get_permalink() . '" rel="bookmark" title="' . the_title_attribute( array( 'echo' => false ) ) . '">' . get_the_title() . '</a></h5></div>';

    if( $meta != 'false' ) {

        $output .= '<ul class="ipost-meta clearfix"><li><i class="icon-calendar"></i> ' . get_the_date( __( 'j/n/Y', 'coworker' ) ) . '</li><li><span>&middot;</span><a href="#"><i class="icon-comments"></i> ' . get_comments_number() . __( ' Comments', 'coworker' ) . '</a></li></ul>';

    }

    if( $desc != 'false' ) {

        $output .= '<div class="ipost-content"><p class="nobottommargin">' . custom_excerpt( $tlimit ) . '</p></div>';

    }

    $output .= '</div>';

    endwhile;

    $output .= '</div>';

    endif; wp_reset_postdata();

	return $output;

}

add_shortcode('ipost', 'semi_sh_ipost');


/*--------------------------------------------------------
    Posts Block
--------------------------------------------------------*/


function semi_sh_postsblock( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'layout' => '',
        'number' => '',
        'pagination' => '',
        'paginationtype' => '',
        'include' => '',
        'category' => '',
        'order' => '',
        'orderby' => '',
        'author' => '',
        'tag' => '',
        'search' => '',
        'postformat' => ''
	), $atts));

    $output = '';

    if( $layout == 'alt' OR $layout == 'full' OR $layout == 'full-alt' OR $layout == 'small' OR $layout == 'small-full' ) {
        $layout = $layout;
    } else {
        $layout = 'default';
    }

    if( $layout == 'small' OR $layout == 'small-full' ) {
        $layoutcss = 'small-posts ';
    } else { $layoutcss = ''; }

    $number = is_numeric( $number ) ? $number : 5;

    $postargs = array( 'post_type' => 'post', 'showposts' => $number );

    if( $pagination == 'true' ) { $postargs['paged'] = get_query_var('paged'); }

    if( $include != '' ) {
        $include = explode( ',', $include );
        if( count( $include ) > 0 ) {
            $postargs['post__in'] = $include;
        }
    }

    if( $category != '' ) {
        $category = explode( ',', $category );
        if( count( $category ) > 0 ) {
            $postargs['category__in'] = $category;
        }
    }

    if( $order != '' ) { $postargs['order'] = $order; }

    if( $orderby != '' ) { $postargs['orderby'] = $orderby; }

    if( $author != '' ) { $postargs['author_name'] = $author; }

    if( $tag != '' ) {
        $tag = explode( ',', $tag );
        if( count( $tag ) > 0 ) {
            $postargs['tag_slug__in'] = $tag;
        }
    }

    if( $search != '' ) { $postargs['s'] = $search; }

    if( $postformat != '' ) {
        $postargs['tax_query'] = array( array( 'taxonomy' => 'post_format', 'field' => 'slug', 'terms' => array( 'post-format-' . $postformat ) ) );
    }


    query_posts( $postargs );

    if ( have_posts() ) :

    $output .= '<div id="posts" class="' . $layoutcss . 'clearfix">';

    while ( have_posts() ) : the_post();

    $getclasses = get_post_class('entry clearfix');

    $getclasses = implode( ' ', $getclasses );

    $output .= '<div id="post-' . get_the_ID() . '" class="' . $getclasses . '">';

    $format = get_post_format();

    ob_start();

    if( $format AND $format != '' ) {
        get_template_part( 'include/blog/' . $layout . '/post', $format );
    } else {
        get_template_part( 'include/blog/' . $layout . '/post', 'standard' );
    }

    $output .= ob_get_contents();

    ob_end_clean();

    $output .= '</div>';

    endwhile;

    if( $pagination == 'true' ) {

        if( $paginationtype == 'numbers' ) {

            ob_start();

            semi_pagination();

            $output .= ob_get_contents();

            ob_end_clean();

        } else {

            if( get_previous_posts_link() OR get_next_posts_link() ):

            $output .= '<ul class="pager nobottommargin"><li class="previous">' . get_previous_posts_link( __( '&larr; Previous Posts', 'coworker' ) ) . '</li><li class="next">' . get_next_posts_link( __( 'Next Posts &rarr;', 'coworker' ) ) . '</li></ul>';

            endif;

        }

    }

    $output .= '</div>';

    else:

    $output .= '<div class="alert alert-error">' . __( "<strong>Sorry!</strong> No Posts available. Please try later.", "coworker" ) . '</div>';

    endif; wp_reset_query();

    return $output;

}

add_shortcode('posts', 'semi_sh_postsblock');


/*--------------------------------------------------------
    FAQs
--------------------------------------------------------*/


function semi_sh_faqs( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'number' => '',
        'include' => '',
        'category' => '',
        'order' => '',
        'orderby' => ''

	), $atts));

    $output = '';

    $number = is_numeric( $number ) ? $number : 10;

    $faqargs = array( 'post_type' => 'faqs', 'posts_per_page' => $number );

    if( $include != '' ) {
        $include = explode( ',', $include );
        if( count( $include ) > 0 ) {
            $faqargs['post__in'] = $include;
        }
    }

    if( $category != '' ) {
        $category = explode( ',', $category );
        if( count( $category ) > 0 ) {
            $faqargs['tax_query'] = array( array( 'taxonomy' => 'faqs-group', 'field' => 'id', 'terms' => $category ) );
        }
    }

    if( $order != '' ) { $faqargs['order'] = $order; }

    if( $orderby != '' ) { $faqargs['orderby'] = $orderby; }

    $faqs = new WP_Query( $faqargs );

    if( $faqs->have_posts() ):

    $output .= '<div class="clearfix" style="position: relative; margin: 0 0 -15px 0;">';

    while ( $faqs->have_posts() ) : $faqs->the_post();

    $output .= '<div class="toggle faq clearfix"><div class="togglet"><i class="' . get_post_meta( get_the_ID(), 'semi_faq_icon', TRUE ) . '"></i>' . the_title_attribute( array( 'echo' => false ) ) . '</div><div class="togglec clearfix">' . get_the_content() . '</div></div>';

    endwhile;

    $output .= '</div>';

    endif; wp_reset_postdata();

	return $output;

}

add_shortcode('faqs', 'semi_sh_faqs');


/*--------------------------------------------------------
    Portfolio Carousel
--------------------------------------------------------*/


function semi_sh_portcarousel( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'number' => '',
        'include' => '',
        'group' => '',
        'order' => '',
        'orderby' => '',
        'type' => ''
	), $atts));

    $output = '';

    $number = is_numeric( $number ) ? $number : 12;

    $portargs = array( 'post_type' => 'portfolio', 'posts_per_page' => $number );

    if( $include != '' ) {
        $include = explode( ',', $include );
        if( count( $include ) > 0 ) {
            $portargs['post__in'] = $include;
        }
    }

    if( $group != '' ) {
        $group = explode( ',', $group );
        if( count( $group ) > 0 ) {
            $portargs['tax_query'] = array( array( 'taxonomy' => 'port-group', 'field' => 'id', 'terms' => $group ) );
        }
    }

    if( $order != '' ) { $portargs['order'] = $order; }

    if( $orderby != '' ) { $portargs['orderby'] = $orderby; }

    if( $type == 'image' OR $type == 'gallery' OR $type == 'video' ) {
        $portargs['meta_key'] = 'semi_port_type';
        $portargs['meta_value'] = $type;
    }


    $portfolio = new WP_Query( $portargs );

    if( $portfolio->have_posts() ):

    $output .= '<div id="portfolio" class="scroll-portfolio clearfix">';

    while ( $portfolio->have_posts() ) : $portfolio->the_post();

    ob_start();

    get_portfolio_items( 231, 180, 5, true, false, true );

    $output .= ob_get_contents();

    ob_end_clean();

    endwhile;

    $output .= '</div><div id="scroller-portfolio-prev" class="widget-scroll-prev"></div><div id="scroller-portfolio-next" class="widget-scroll-next"></div>';

    $output .= '<script type="text/javascript">

                    jQuery(document).ready(function($) {

                        var portfolioCarousel = $("#portfolio");

                        portfolioCarousel.carouFredSel({
                            width : "100%",
                            height : "auto",
                            circular : false,
                            responsive : true,
                            infinite : false,
                            auto : false,
                            items : {
                                width : 280,
                                visible: {
                                    min: 1,
                                    max: 4
                                }
                            },
                            scroll : {
                                wipe : true
                            },
                            prev : {
                                button : "#scroller-portfolio-prev",
                                key : "left"
                            },
                            next : {
                                button : "#scroller-portfolio-next",
                                key : "right"
                            },
                            onCreate : function () {
                                $(window).on(\'resize\', function(){
                                    portfolioCarousel.parent().add(portfolioCarousel).css(\'height\', portfolioCarousel.children().first().outerHeight() + \'px\');
                                }).trigger(\'resize\');
                            }
                        });

                    });

                </script>';

    endif; wp_reset_postdata();

	return $output;

}

add_shortcode('portfoliocarousel', 'semi_sh_portcarousel');


/*--------------------------------------------------------
    Portfolio Block
--------------------------------------------------------*/


function semi_sh_portfolioblock( $atts, $content = null ) {

    extract(shortcode_atts(array(
        'columns' => '',
		'number' => '',
        'filter' => '',
        'pagination' => '',
        'include' => '',
        'group' => '',
        'order' => '',
        'orderby' => '',
        'type' => ''
	), $atts));

    $output = '';

    $number = is_numeric( $number ) ? $number : 12;

    if( $columns == '2' OR $columns == '2s' ) {
        $columncss = 'portfolio-2 ';
    } elseif( $columns == '3' OR $columns == '3s' ) {
        $columncss = 'portfolio-3 ';
    } elseif( $columns == '5' ) {
        $columncss = 'portfolio-5 ';
    } else {
        $columncss = '';
    }

    $portargs = array( 'post_type' => 'portfolio', 'posts_per_page' => $number );

    if( $include != '' ) {
        $include = explode( ',', $include );
        if( count( $include ) > 0 ) {
            $portargs['post__in'] = $include;
        }
    }

    if( $group != '' ) {
        $group = explode( ',', $group );
        if( count( $group ) > 0 ) {
            $portargs['tax_query'] = array( array( 'taxonomy' => 'port-group', 'field' => 'id', 'terms' => $group ) );
        }
    }

    if( $order != '' ) { $portargs['order'] = $order; }

    if( $orderby != '' ) { $portargs['orderby'] = $orderby; }

    if( $type == 'image' OR $type == 'gallery' OR $type == 'video' ) {
        $portargs['meta_key'] = 'semi_port_type';
        $portargs['meta_value'] = $type;
    }

    if( $pagination == 'true' ) { $portargs['paged'] = get_query_var('paged'); }


    $portfolio = new WP_Query( $portargs );

    if( $portfolio->have_posts() ):

    if( $pagination != 'true' ) {

        if( $filter == 'true' ) {

            if( $group == '' OR count( $group ) > 1 ) {

                $output .= '<ul id="portfolio-filter" class="bottommargin clearfix"><li class="activeFilter"><a href="#" data-filter="*">' . __('All', 'coworker') . '</a></li>';

                $terms = get_terms( "port-group", array( 'include' => $group ) );
                $count = count( $terms );
                if ( $count > 0 ){
                    foreach ( $terms as $term ) {
                        $output .= '<li><a href="#" data-filter=".pf-' . $term->slug . '">' . $term->name . '</a></li>';
                    }
                }

                $output .= '</ul>';

            }

        }

    }

    $output .= '<div id="portfolio" class="' . $columncss . 'clearfix">';

    while ( $portfolio->have_posts() ) : $portfolio->the_post();

    ob_start();

    if( $columns == '2' OR $columns == '2s' OR $columns == '3' OR $columns == '3s' OR $columns == '5' ) {
        get_template_part( 'include/portfolio/items' , $columns );
    } elseif( $columns == '4s' ) {
        get_template_part( 'include/portfolio/items', 's' );
    } else {
        get_template_part( 'include/portfolio/items' );
    }

    $output .= ob_get_contents();

    ob_end_clean();

    endwhile;

    $output .= '</div>';

    if( $pagination == 'true' ) {

        ob_start();

        semi_pagination( $portfolio->max_num_pages );

        $output .= ob_get_contents();

        ob_end_clean();

    } else {

        if( $filter == 'true' ) {

            if( $columns == '5' ) {

                $output .= '<script type="text/javascript">

                                jQuery(document).ready(function($){

                                    portfolioHeightAdjust=function(){

                                    $(".portfolio-item .portfolio-image").each(function(){

                                        var portfolioWidth = $(this).outerWidth();
                                        var portfolioImageHeight = $(this).find("a:not(.hidden) img").attr("height");
                                        var portfolioImageH = ( portfolioWidth * portfolioImageHeight / 188 )
                                        $(this).find("a img").css("height", portfolioImageH + "px");

                                    });

                                    }; portfolioHeightAdjust();

                                    var $container = $(\'#portfolio\');

                                    $container.isotope();

                                    $(\'#portfolio-filter a\').click(function(){

                                        $(\'#portfolio-filter li\').removeClass(\'activeFilter\');
                                        $(this).parent(\'li\').addClass(\'activeFilter\');
                                        var selector = $(this).attr(\'data-filter\');
                                        $container.isotope({ filter: selector });
                                        return false;

                                    });

                                    $(window).resize(function() {
                                        $container.isotope(\'reLayout\');
                                        portfolioHeightAdjust();
                                    });

                                });

                            </script>';

            } else {

                $output .= '<script type="text/javascript">

                                jQuery(document).ready(function($){

                                    var $container = $(\'#portfolio\');

                                    $container.isotope();

                                    $(\'#portfolio-filter a\').click(function(){

                                        $(\'#portfolio-filter li\').removeClass(\'activeFilter\');
                                        $(this).parent(\'li\').addClass(\'activeFilter\');
                                        var selector = $(this).attr(\'data-filter\');
                                        $container.isotope({ filter: selector });
                                        return false;

                                    });

                                    $(window).resize(function() {
                                        $container.isotope(\'reLayout\');
                                    });

                                });

                            </script>';

            }

        }

    }

    endif; wp_reset_postdata();

	return $output;

}

add_shortcode('portfolio', 'semi_sh_portfolioblock');


/*--------------------------------------------------------
    Slider
--------------------------------------------------------*/


function semi_sh_slider( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'width' => '',
        'height' => '',
        'number' => '',
        'include' => '',
        'group' => '',
        'order' => '',
        'orderby' => '',
        'caption' => '',
        'animate' => '',
        'easing' => '',
        'direction' => '',
        'slideshow' => '',
        'pause' => '',
        'speed' => '',
        'video' => '',
        'class' => '',
        'style' => ''
	), $atts));

    $output = '';

    $width = is_numeric( $width ) ? $width : 480;

    $height = is_numeric( $height ) ? $height : 300;

    $number = is_numeric( $number ) ? $number : 12;

    $pause = is_numeric( $pause ) ? $pause : 5000;

    $speed = is_numeric( $speed ) ? $speed : 500;

    $easing = ( $easing != '' ) ? $easing : 'easeOutExpo';

    $sliderops = array();

    if( $animate == 'fade' ) { $sliderops['animate'] = 'data-animate="fade"'; }

    if( $direction == 'vertical' ) { $sliderops['direction'] = 'data-direction="vertical"'; }

    if( $easing ) { $sliderops['easing'] = 'data-easing="' . $easing . '"'; }

    if( $slideshow == 'false' ) { $sliderops['slideshow'] = 'data-slideshow="false"'; }

    if( $pause ) { $sliderops['pause'] = 'data-pause="' . $pause . '"'; }

    if( $speed ) { $sliderops['speed'] = 'data-speed="' . $speed . '"'; }

    if( $video == 'true' ) { $sliderops['video'] = 'data-video="true"'; }

    $sliderops = implode( ' ', $sliderops );

    $sliderargs = array( 'post_type' => 'slider', 'posts_per_page' => $number );

    if( $include != '' ) {
        $include = explode( ',', $include );
        if( count( $include ) > 0 ) {
            $sliderargs['post__in'] = $include;
        }
    }

    if( $group != '' ) {
        $group = explode( ',', $group );
        if( count( $group ) > 0 ) {
            $sliderargs['tax_query'] = array( array( 'taxonomy' => 'slider-group', 'field' => 'id', 'terms' => $group ) );
        }
    }

    if( $order != '' ) { $sliderargs['order'] = $order; }

    if( $orderby != '' ) { $sliderargs['orderby'] = $orderby; }

    $slider = new WP_Query( $sliderargs );

    if( $slider->have_posts() ):

    $output .= '<div class="fslider ' . $class . '" style="' . $style . '" ' . $sliderops . '><div class="flexslider"><div class="slider-wrap">';

    while ( $slider->have_posts() ) : $slider->the_post();

    $thumb = get_resized_image( $width, $height, true );

    $slideops = array();
    $slideops['caption'] = get_post_meta( get_the_ID(), 'semi_slider_caption', TRUE );
    $slideops['caption_type'] = get_post_meta( get_the_ID(), 'semi_slider_caption_type', TRUE );
    $slideops['caption_position'] = get_post_meta( get_the_ID(), 'semi_slider_caption_position', TRUE );
    $slideops['url'] = get_post_meta( get_the_ID(), 'semi_slider_url', TRUE );
    $slideops['target'] = get_post_meta( get_the_ID(), 'semi_slider_target', TRUE );
    $slideops['video'] = get_post_meta( get_the_ID(), 'semi_slider_video', TRUE );

    $captionclass = array();

    if( $slideops['caption_type'] == 'chunky' ) { $captionclass[] = 'slide-caption2'; }

    if( $slideops['caption_position'] == 'left' ) { $captionclass[] = 'slide-caption-left'; }

    $captionclass = implode( ' ', $captionclass );

    $captionoutput = '<div class="slide-caption ' . $captionclass . '">' . $slideops['caption'] . '</div>';

	if( $slideops['video'] AND $video == 'true' ) {

        $output .= '<div class="slide">' . stripslashes( htmlspecialchars_decode( $slideops['video'] ) );

        if( $slideops['caption'] AND $caption == 'true' ) {

            $output .= $captionoutput;

        }

        $output .= '</div>';

    } else {

        if( $thumb ):

            $output .= '<div class="slide">';

            if( checkurl( $slideops['url'] ) ) {

            $output .= '<a href="' . $slideops['url'] . '" target="' . $slideops['target'] . '"><img src="' . $thumb . '" alt="' . the_title_attribute( array( 'echo' => false ) ) . '" /></a>';

            } else {

            $output .= '<img src="' . $thumb . '" alt="' . the_title_attribute( array( 'echo' => false ) ) . '" />';

            }

            if( $slideops['caption'] AND $caption == 'true' ) {

                $output .= $captionoutput;

            }

            $output .= '</div>';

        endif;

    }

    endwhile;

    $output .= '</div></div></div>';

    endif; wp_reset_postdata();

    return $output;

}

add_shortcode('slider', 'semi_sh_slider');


/*--------------------------------------------------------
    Subscribe Form
--------------------------------------------------------*/


function semi_sh_subscribe( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'listid' => '',
        'inputtext' => __( 'Enter your Email to get notified..', 'coworker' ),
        'buttontext' => __( 'Subscribe Now', 'coworker' )
	), $atts));

	return '<div class="lp-subscribe">

                        <div id="subscribe-form-result"></div>

                        <form id="comingsoon-subscribe" method="post" action="' . get_template_directory_uri() . '/include/mailchimp.php">

                            <div class="lp-subscribe-input">
                                <input type="text" id="lp-subscribe-email" name="lp-subscribe-email" value="" placeholder="' . $inputtext . '" class="required email" />
                            </div>

                            <div class="lp-subscribe-submit">
                                <input type="submit" id="lp-subscribe-email-submit" name="lp-subscribe-email-submit" value="' . $buttontext . '" />
                            </div>

                            <input type="hidden" name="lp-subscribe-listid" value="' . $listid . '" />

                        </form>

                    </div>

                    <script type="text/javascript">

                        jQuery("#comingsoon-subscribe").validate({
                            messages: {
                                \'lp-subscribe-email\': \'\'
                            },
                    		submitHandler: function(form) {

                                jQuery(form).ajaxSubmit({
                    				target: \'#subscribe-form-result\',
                                    success: function() {
                                        jQuery("#comingsoon-subscribe").fadeOut(500, function(){
                                            jQuery(\'#subscribe-form-result\').fadeIn(500);
                                        });
                                    },
                                    error: function() {
                                        jQuery(\'#subscribe-form-result\').fadeIn(500);
                                    }
                    			});

                    		}
                    	});

                    </script>';

}

add_shortcode('subscribe', 'semi_sh_subscribe');


/*--------------------------------------------------------
    Responsive Content
--------------------------------------------------------*/


function semi_sh_responsive( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'layout' => ''
	), $atts));

	return '<div class="'. $layout .' clearfix">' . do_shortcode( trim( $content ) ) . '</div><div class="clear"></div>';

}

add_shortcode('responsive', 'semi_sh_responsive');


/*--------------------------------------------------------
    Media Embeds
--------------------------------------------------------*/


function semi_sh_youtubeembed( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'id' => '',
        'width' => 640,
        'height' => 360
	), $atts));

	return '<iframe width="' . $width . '" height="' . $height . '" src="http://www.youtube.com/embed/' . $id . '" frameborder="0" allowfullscreen></iframe>';

}

add_shortcode('youtube', 'semi_sh_youtubeembed');


function semi_sh_vimeoembed( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'id' => '',
        'width' => 640,
        'height' => 360
	), $atts));

	return '<iframe src="http://player.vimeo.com/video/' . $id . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';

}

add_shortcode('vimeo', 'semi_sh_vimeoembed');


function semi_sh_dailymotionembed( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'id' => '',
        'width' => 640,
        'height' => 360
	), $atts));

	return '<iframe frameborder="0" width="' . $width . '" height="' . $height . '" src="http://www.dailymotion.com/embed/video/' . $id . '"></iframe>';

}

add_shortcode('dailymotion', 'semi_sh_dailymotionembed');


function semi_sh_soundcloudembed( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'url' => '',
        'width' => '100%',
        'height' => 166
	), $atts));

	return '<iframe width="' . $width . '" height="' . $height . '" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=' . urlencode( $url ) . '"></iframe>';

}

add_shortcode('soundcloud', 'semi_sh_soundcloudembed');


/*--------------------------------------------------------
    Portfolio Meta
--------------------------------------------------------*/


function semi_sh_portfoliometa( $atts, $content = null ) {

    extract(shortcode_atts(array(
		'title' => ''
	), $atts));

	return '<div class="port-terms"><h5>' . strip_tags( $title ) . '</h5><span>' . trim( $content ) . '</span></div>';

}

add_shortcode('portmeta', 'semi_sh_portfoliometa');


/*--------------------------------------------------------
    Latest Posts - Home Layout 3
--------------------------------------------------------*/


function semi_sh_latestposts_ipost( $atts, $content = null ) {

    $latest_iposts = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 2 ) );

    if( $latest_iposts->have_posts() ):

    $counter = 0;

    $output = '';

    while ( $latest_iposts->have_posts() ) : $latest_iposts->the_post();

    $counter++;

    if( $counter == 2 ) {
        $output .= '<div class="col_half col_last">' . do_shortcode( '[ipost id="' . get_the_ID() . '"]' ) . '</div>';
    } else {
        $output .= '<div class="col_half">' . do_shortcode( '[ipost id="' . get_the_ID() . '"]' ) . '</div>';
    }

    endwhile;

    endif; wp_reset_postdata();

	return '<div class="clear"></div>';

}

add_shortcode('latestposts_ipost', 'semi_sh_latestposts_ipost');


/*--------------------------------------------------------
    Gallery
--------------------------------------------------------*/


add_filter( 'post_gallery', 'my_post_gallery', 10, 2 );

function my_post_gallery( $output, $attr) {
    global $post, $wp_locale;

    static $instance = 0;
    $instance++;

    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
    if ( isset( $attr['orderby'] ) ) {
        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
        if ( !$attr['orderby'] )
            unset( $attr['orderby'] );
    }

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order',
        'id'         => $post->ID,
        'itemtag'    => 'dl',
        'icontag'    => 'dt',
        'captiontag' => 'dd',
        'columns'    => 3,
        'size'       => 'thumbnail',
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $id = intval($id);
    $postid = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $output = apply_filters('gallery_style', "<div id='$selector' class='gallery galleryid-{$id}' data-lightbox='gallery'>");

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {
        $link = isset($attr['link']) && 'post' == $attr['link'] ? wp_get_attachment_link($id, $size, true, false) : wp_get_attachment_link($id, $size, false, false);

        if( !isset($attr['link']) && 'post' != $attr['link'] ) {
            $link_full_img = wp_get_attachment_image_src( $id, 'full' );
            $link = '<a data-lightbox="gallery-item" href="' . $link_full_img[0] . '" title="' . get_the_title( $id ) . '">' . wp_get_attachment_image( $id, $size ) . '</a>';
        }

        $output .= "\n<{$itemtag} class='gallery-item col-{$columns}'>";
        $output .= "
            <{$icontag} class='gallery-icon'>
                $link
            </{$icontag}>";

        if ( $captiontag && trim($attachment->post_excerpt) ) {
            $output .= "
                <{$captiontag} class='gallery-caption'>
                " . wptexturize($attachment->post_excerpt) . "
                </{$captiontag}>";
        }

        $output .= "</{$itemtag}>";
        if ( $columns > 0 && ++$i % $columns == 0 )
            $output .= "\n<br />";
    }

    $output .= "<br /></div>\n";

    return $output;
}


?>