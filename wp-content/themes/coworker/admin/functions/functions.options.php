<?php

add_action('init','of_options');

include( 'googlefonts.php' );

if (!function_exists('of_options'))
{
	function of_options()
	{
		//Access the WordPress Pages via an Array
		$of_pages = array( 0 => __( 'Select a Page:', 'coworker' ) );
		$of_pages_obj = get_pages( 'sort_column=post_parent,menu_order');
        
		if( count( $of_pages_obj ) > 0 ) { foreach ( $of_pages_obj as $of_page ) { $of_pages[$of_page->ID] = $of_page->post_title; } }
        
        $of_features = array( 0 => __( 'Select a Feature:', 'coworker' ) );
        $of_features_obj = new WP_Query('post_type=features&posts_per_page=-1&orderby=menu_order&order=ASC');
        
		if( $of_features_obj->have_posts() ): while ( $of_features_obj->have_posts() ) : $of_features_obj->the_post();
		  
          $of_features[get_the_ID()] = get_the_title();
        
        endwhile; endif;
            
        $bgpatternsurl_light = get_template_directory_uri() . '/admin/assets/patterns/light/';
        
        $bgpatternsurl_dark = get_template_directory_uri() . '/admin/assets/patterns/dark/';
        
        $bgpatterns = array();
        
        $bgpatternsd = array();
        
        $bgpatternsl = array();
        
        for( $lpatterns = 1; $lpatterns <=28; $lpatterns++ ) { $bgpatternsl['light/pattern' . $lpatterns . '.png'] = $bgpatternsurl_light . 'pattern' . $lpatterns . '.png'; }
        
        for( $dpatterns = 1; $dpatterns <=20; $dpatterns++ ) { $bgpatternsd['dark/pattern' . $dpatterns . '.png'] = $bgpatternsurl_dark . 'pattern' . $dpatterns . '.png'; }
        
        $bgpatterns = array_merge( $bgpatternsl, $bgpatternsd );


/*-----------------------------------------------------------------------------------*/
/* The Options Array */
/*-----------------------------------------------------------------------------------*/

global $of_options;
$of_options = array();

$of_options[] = array( 	"name" => __( 'General Settings', 'coworker' ),
						"type" => "heading"
				);
                
$of_options[] = array(  "name" => __( "Page Title - Right Area", 'coworker' ),
    					"desc" => __( "Select what to show on the Page Title - Right Area", 'coworker' ),
    					"id" => "pagetitle_right",
    					"std" => "search",
    					"type" => "select",
    					"options" => array(
                                '' => __( 'Select One', 'coworker' ),
                                'search' => __( 'Search Form', 'coworker' ),
                                'breadcrumb' => __( 'Breadcrumbs', 'coworker' )
                            )
                );

$of_options[] = array(  "name" => __( 'Admin Login Logo', 'coworker' ),
    					"desc" => __( 'Upload your Customised Wordpress Login Logo', 'coworker' ),
    					"id" => "loginlogo",
    					"std" => "",
    					"type" => "media"
                );
					
$of_options[] = array( 	"name" => __( 'Login Logo - Width', 'coworker' ),
						"desc" => __( 'Width of the Login Logo.', 'coworker' ),
						"id" => "loginlogo_width",
						"std" => "",
						"min" => "50",
						"step" => "1",
						"max" => "300",
						"type" => "sliderui"
				);
					
$of_options[] = array( 	"name" => __( 'Login Logo - Height', 'coworker' ),
						"desc" => __( 'Height of the Login Logo', 'coworker' ),
						"id" => "loginlogo_height",
						"std" => "",
						"min" => "10",
						"step" => "1",
						"max" => "200",
						"type" => "sliderui"
				);

$of_options[] = array(  "name" => __( 'Favicon', 'coworker' ),
    					"desc" => __( 'Upload your 16px x 16px png/ico Favicon Image', 'coworker' ),
    					"id" => "favicon",
    					"std" => "",
    					"type" => "media"
                );
                
$of_options[] = array(  "name" => __( 'Apple iPhone Icon', 'coworker' ),
    					"desc" => __( 'Upload your 57px x 57px png Icon for iPhone Devices', 'coworker' ),
    					"id" => "iphoneicon",
    					"std" => "",
    					"type" => "media"
                );
                
$of_options[] = array(  "name" => __( 'Apple iPhone Retina Icon', 'coworker' ),
    					"desc" => __( 'Upload your 114px x 114px png Icon for iPhone Devices with Retina Display Support', 'coworker' ),
    					"id" => "iphoneretinaicon",
    					"std" => "",
    					"type" => "media"
                );
                
$of_options[] = array(  "name" => __( 'Apple iPad Icon', 'coworker' ),
    					"desc" => __( 'Upload your 72px x 72px png Icon for iPad Devices', 'coworker' ),
    					"id" => "ipadicon",
    					"std" => "",
    					"type" => "media"
                );
                
$of_options[] = array(  "name" => __( 'Apple iPad Retina Icon', 'coworker' ),
    					"desc" => __( 'Upload your 144px x 144px png Icon for iPad Devices with Retina Display Support', 'coworker' ),
    					"id" => "ipadretinaicon",
    					"std" => "",
    					"type" => "media"
                );
                                               
$of_options[] = array(  "name" => __( "Google Analytics", 'coworker' ),
    					"desc" => __( "Paste your Google Analytics tracking code here.", 'coworker' ),
    					"id" => "ganalytics",
    					"std" => "",
    					"type" => "textarea"
                );

$of_options[] = array( 	"name" => __( 'Header Options', 'coworker' ),
						"type" => "heading"
				);
                
$of_options[] = array(  "name" => __( 'Logo', 'coworker' ),
    					"desc" => __( 'Upload your Website Logo. Maximum 230px in Width and 120px in Height', 'coworker' ),
    					"id" => "logo",
    					"std" => "",
    					"type" => "media"
                );
                
$of_options[] = array(  "name" => __( 'Retina Logo', 'coworker' ),
    					"desc" => __( 'Upload your Website Retina Logo. Should be Double in size of the Original Logo', 'coworker' ),
    					"id" => "retinalogo",
    					"std" => "",
    					"type" => "media"
                );
					
$of_options[] = array( 	"name" => __( 'Logo Size - Width', 'coworker' ),
						"desc" => __( 'Select your Regular Logo Width', 'coworker' ),
						"id" => "logo_width",
						"std" => "",
						"min" => "10",
						"step" => "1",
						"max" => "230",
						"type" => "sliderui"
				);
					
$of_options[] = array( 	"name" => __( 'Logo Size - Height', 'coworker' ),
						"desc" => __( 'Select your Regular Logo Height', 'coworker' ),
						"id" => "logo_height",
						"std" => "",
						"min" => "10",
						"step" => "1",
						"max" => "120",
						"type" => "sliderui"
				);
                
$of_options[] = array( 	"name" => __( 'Top Bar', 'coworker' ),
						"desc" => __( 'Enable/Disable Top Bar', 'coworker' ),
						"id" => "topbar",
						"std" => 1,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array(  "name" => __( "Top Bar - Content Alignment", 'coworker' ),
    					"desc" => __( "Select the Alignment of the Contents in the Top Bar", 'coworker' ),
    					"id" => "topbar_content",
    					"std" => "menu-social",
    					"type" => "select",
    					"options" => array(
                                'menu-social' => __( 'Menu Left / Social Right', 'coworker' ),
                                'social-menu' => __( 'Social Left / Menu Right', 'coworker' )
                            )
                );
                
$of_options[] = array(  "name" => __( "Header Style", 'coworker' ),
    					"desc" => __( "Select your Header Style", 'coworker' ),
    					"id" => "header_style",
    					"std" => "header1",
    					"type" => "select",
    					"options" => array(
                                'header1' => __( 'Style 1', 'coworker' ),
                                'header2' => __( 'Style 2', 'coworker' ),
                                'header3' => __( 'Style 3', 'coworker' ),
                                'header4' => __( 'Style 4', 'coworker' ),
                                'header5' => __( 'Style 5', 'coworker' ),
                                'header6' => __( 'Style 6', 'coworker' )
                            )
                );
                
$of_options[] = array(  "name" => __( "Header - Right Content", 'coworker' ),
    					"desc" => __( "Select the Right Area Content for the Header. This only works for Header Style 2,3 &amp; 5", 'coworker' ),
    					"id" => "header_right",
    					"std" => "search",
    					"type" => "select",
    					"options" => array(
                                'search' => __( 'Search Form', 'coworker' ),
                                'contact' => __( 'Contact', 'coworker' )
                            )
                );
					
$of_options[] = array( 	"name" => __( 'Sub Menu - Width', 'coworker' ),
						"desc" => __( 'Select your Dropdown Sub Menu Width', 'coworker' ),
						"id" => "submenu_width",
						"std" => "200",
						"min" => "100",
						"step" => "1",
						"max" => "400",
						"type" => "sliderui"
				);
                
$of_options[] = array( 	"name" => __( 'Sticky Menu', 'coworker' ),
						"desc" => __( 'Enable/Disable Sticky Menu', 'coworker' ),
						"id" => "sticky_menu",
						"std" => 1,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);

$of_options[] = array(  "name" => __( 'Sticky Menu - Logo', 'coworker' ),
    					"desc" => __( 'Upload a Logo for your Sticky Menu. If no Logo is Uploaded, then the Default Logo will be used', 'coworker' ),
    					"id" => "sticky_logo",
    					"std" => "",
    					"type" => "media"
                );

$of_options[] = array( 	"name" => __( 'Styling Options', 'coworker' ),
						"type" => "heading"
				);
					
$url =  ADMIN_DIR . 'assets/images/';
$of_options[] = array( 	"name" => __( 'Layout', 'coworker' ),
						"desc" => __( 'Select your Website Layout', 'coworker' ),
						"id" => "layout",
						"std" => "boxed",
						"type" => "images",
						"options" => array(
							'full' => $url . '1col.png',
							'boxed' => $url . '3cm.png'
						)
				);
                
$of_options[] = array( 	"name" => __( 'Disable Responsiveness', 'coworker' ),
						"desc" => __( 'Disable Responsive Layout', 'coworker' ),
						"id" => "nonresponsive",
						"std" => 0,
						"on" => __( 'Yes', 'coworker' ),
						"off" => __( 'No', 'coworker' ),
						"type" => "switch"
				);
				
$of_options[] = array( 	"name" => __( 'Color Scheme', 'coworker' ),
						"desc" => __( 'Pick a Color Scheme for your Website', 'coworker' ),
						"id" => "colorscheme",
						"std" => "",
						"type" => "color"
				);
					
$of_options[] = array( 	"name" => __( 'Boxed Width Top/Bottom Margin', 'coworker' ),
						"desc" => __( 'Select your Boxed Width Top/Bottom Margin', 'coworker' ),
						"id" => "boxedmargin",
						"std" => "50",
						"min" => "0",
						"step" => "5",
						"max" => "100",
						"type" => "sliderui"
				);
                
$of_options[] = array( 	"name" => __( 'Background Image', 'coworker' ),
						"desc" => __( 'Enable/Disable Background Image', 'coworker' ),
						"id" => "bgimage_enable",
						"std" => 0,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array(  "name" => __( 'Upload Background Image', 'coworker' ),
    					"desc" => __( 'Upload a Background Image', 'coworker' ),
    					"id" => "bgimage",
    					"std" => "",
    					"type" => "media"
                );
				
$of_options[] = array( 	"name" => __( 'Background Color', 'coworker' ),
						"desc" => __( 'Pick a Background Color', 'coworker' ),
						"id" => "bgcolor",
						"std" => "",
						"type" => "color"
				);
                
$of_options[] = array( 	"name" => __( 'Background Pattern', 'coworker' ),
						"desc" => __( 'Enable/Disable Background Pattern', 'coworker' ),
						"id" => "bgpattern_enable",
						"std" => 0,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array( 	"name" => __( 'Choose Background Patterns', 'coworker' ),
						"desc" => __( 'Choose your Background Patterns', 'coworker' ),
						"id" => "bgpattern",
						"std" => "",
						"type" => "images",
						"options" => $bgpatterns
				);
                
$of_options[] = array(  "name" => __( 'Upload Background Pattern', 'coworker' ),
    					"desc" => __( 'Upload a Background Pattern', 'coworker' ),
    					"id" => "bgpattern_upload",
    					"std" => "",
    					"type" => "media"
                );
                
$of_options[] = array(  "name" => __( 'Typography', 'coworker' ),
                        "type" => "heading"
                );
				
$of_options[] = array( 	"name" => __( 'Body Font', 'coworker' ),
						"desc" => __( 'Select your Body Font', 'coworker' ),
						"id" => "bodyfont",
						"std" => "none",
						"type" => "select_google_font",
						"preview" => array(
										"text" => __( 'This is a Font Preview', 'coworker' ),
										"size" => "28px"
						),
						"options" => get_googlefonts()
				);
				
$of_options[] = array( 	"name" => __( 'Primary Font', 'coworker' ),
						"desc" => __( 'Select your Primary Font', 'coworker' ),
						"id" => "primaryfont",
						"std" => "none",
						"type" => "select_google_font",
						"preview" => array(
										"text" => __( 'This is a Font Preview', 'coworker' ),
										"size" => "28px"
						),
						"options" => get_googlefonts()
				);
				
$of_options[] = array( 	"name" => __( 'Secondary Font', 'coworker' ),
						"desc" => __( 'Select a Secondary Font', 'coworker' ),
						"id" => "secondaryfont",
						"std" => "none",
						"type" => "select_google_font",
						"preview" => array(
										"text" => __( 'This is a Font Preview', 'coworker' ),
										"size" => "28px"
						),
						"options" => get_googlefonts()
				);
				
$of_options[] = array( 	"name" => __( 'Body Text Size', 'coworker' ),
						"desc" => __( 'Select Body Font Size', 'coworker' ),
						"id" => "bodytextsize",
						"std" => "12",
						"min" => "10",
						"step" => "1",
						"max" => "20",
						"type" => "sliderui"
				);
				
$of_options[] = array( 	"name" => __( 'H1 Size', 'coworker' ),
						"desc" => __( 'Select H1 Size', 'coworker' ),
						"id" => "h1size",
						"std" => "28",
						"min" => "20",
						"step" => "1",
						"max" => "50",
						"type" => "sliderui"
				);
				
$of_options[] = array( 	"name" => __( 'H2 Size', 'coworker' ),
						"desc" => __( 'Select H2 Size', 'coworker' ),
						"id" => "h2size",
						"std" => "24",
						"min" => "18",
						"step" => "1",
						"max" => "40",
						"type" => "sliderui"
				);
				
$of_options[] = array( 	"name" => __( 'H3 Size', 'coworker' ),
						"desc" => __( 'Select H3 Size', 'coworker' ),
						"id" => "h3size",
						"std" => "20",
						"min" => "16",
						"step" => "1",
						"max" => "32",
						"type" => "sliderui"
				);
				
$of_options[] = array( 	"name" => __( 'H4 Size', 'coworker' ),
						"desc" => __( 'Select H4 Size', 'coworker' ),
						"id" => "h4size",
						"std" => "18",
						"min" => "14",
						"step" => "1",
						"max" => "24",
						"type" => "sliderui"
				);
				
$of_options[] = array( 	"name" => __( 'H5 Size', 'coworker' ),
						"desc" => __( 'Select H5 Size', 'coworker' ),
						"id" => "h5size",
						"std" => "16",
						"min" => "14",
						"step" => "1",
						"max" => "20",
						"type" => "sliderui"
				);
				
$of_options[] = array( 	"name" => __( 'H6 Size', 'coworker' ),
						"desc" => __( 'Select H6 Size', 'coworker' ),
						"id" => "h6size",
						"std" => "12",
						"min" => "10",
						"step" => "1",
						"max" => "18",
						"type" => "sliderui"
				);
                
$of_options[] = array(  "name" => __( 'Social Options', 'coworker' ),
                        "type" => "heading"
                );
                
$of_options[] = array( 	"name" => __( 'Facebook', 'coworker' ),
						"desc" => __( 'Your Facebook Profile URL. Include http://', 'coworker' ),
						"id" => "ts_facebook",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Twitter', 'coworker' ),
						"desc" => __( 'Your Twitter Profile URL. Include http://', 'coworker' ),
						"id" => "ts_twitter",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Google+', 'coworker' ),
						"desc" => __( 'Your Google+ Profile URL. Include http://', 'coworker' ),
						"id" => "ts_gplus",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Dribbble', 'coworker' ),
						"desc" => __( 'Your Dribbble Profile URL. Include http://', 'coworker' ),
						"id" => "ts_dribbble",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Instagram', 'coworker' ),
						"desc" => __( 'Your Instagram Profile URL. Include http://', 'coworker' ),
						"id" => "ts_instagram",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Forrst', 'coworker' ),
						"desc" => __( 'Your Forrst Profile URL. Include http://', 'coworker' ),
						"id" => "ts_forrst",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Blogger', 'coworker' ),
						"desc" => __( 'Your Blogger Profile URL. Include http://', 'coworker' ),
						"id" => "ts_blogger",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Flickr', 'coworker' ),
						"desc" => __( 'Your Flickr Profile URL. Include http://', 'coworker' ),
						"id" => "ts_flickr",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Pinterest', 'coworker' ),
						"desc" => __( 'Your Pinterest Profile URL. Include http://', 'coworker' ),
						"id" => "ts_pinterest",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Vimeo', 'coworker' ),
						"desc" => __( 'Your Vimeo Profile URL. Include http://', 'coworker' ),
						"id" => "ts_vimeo",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Youtube', 'coworker' ),
						"desc" => __( 'Your Youtube Profile URL. Include http://', 'coworker' ),
						"id" => "ts_youtube",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'LinkedIn', 'coworker' ),
						"desc" => __( 'Your LinkedIn Profile URL. Include http://', 'coworker' ),
						"id" => "ts_linkedin",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Phone Number', 'coworker' ),
						"desc" => __( 'Your Phone Number. Do not use Special Characters. You can use "+", ".", or spaces', 'coworker' ),
						"id" => "ts_phone",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Call Phone Text', 'coworker' ),
						"desc" => __( 'Text for Call Phone Number. Do not use Special Characters. You can use "+", ".", or spaces. Optional', 'coworker' ),
						"id" => "ts_phone_text",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Email Address', 'coworker' ),
						"desc" => __( 'Your Email Address. Do not use Special Characters. You can use "+", ".", or spaces', 'coworker' ),
						"id" => "ts_email",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Email Address Text', 'coworker' ),
						"desc" => __( 'Your Email Address Text. Do not use Special Characters. You can use "+", ".", or spaces', 'coworker' ),
						"id" => "ts_email_text",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'RSS Link', 'coworker' ),
						"desc" => __( 'Enable/Disable RSS Link in the Top Social Area', 'coworker' ),
						"id" => "ts_rss",
						"std" => 0,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);

$of_options[] = array( 	"name" => __( 'Custom Icons', 'coworker' ),
						"desc" => "",
						"id" => "customiconsintro",
						"std" => __( '<h3 style="margin: 0 0 10px;">Custom Icons</h3> You can add your Custom Icons here. Simply Add an Icon here with Name, Icon Image and a URL. But please Enable Custom Icons Option below first.</a>', 'coworker' ),
						"icon" => true,
						"type" => "info"
				);
                
$of_options[] = array( 	"name" => __( 'Use Custom Icons', 'coworker' ),
						"desc" => __( 'Enable/Disable Custom Icons in the Top Social Area. Enabling this will disable the Default Social Icons of the Theme.', 'coworker' ),
						"id" => "ts_customicons",
						"std" => 0,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);

$of_options[] = array(  "name" => __( 'Add Custom Icons', 'coworker' ),
    					"desc" => __( 'Add your Custom Social Icons Here', 'coworker' ),
    					"id" => "customsocialicons",
    					"std" => "",
    					"type" => "customicons"
                );

$of_options[] = array( 	"name" => __( 'Blog Options', 'coworker' ),
						"type" => "heading"
				);
					
$of_options[] = array( 	"name" => __( 'Posts Excerpt Length', 'coworker' ),
						"desc" => __( 'Select no. of Words to show in Post Excerpt', 'coworker' ),
						"id" => "blog_excerpt",
						"std" => "70",
						"min" => "10",
						"step" => "1",
						"max" => "300",
						"type" => "sliderui"
				);
                
$of_options[] = array(  "name" => __( "Default Layout", 'coworker' ),
    					"desc" => __( "Select Blog Layout", 'coworker' ),
    					"id" => "blog_layout",
    					"std" => "default",
    					"type" => "select",
    					"options" => array(
                                'default' => 'Default',
                				'alt' => 'Alternate',
                				'full' => 'Full',
                				'full-alt' => 'Full Alternate',
                				'small' => 'Small Thumbs',
                				'small-full' => 'Small Thumbs Full'
                            )
                );
                
$of_options[] = array(  "name" => __( "Default Sidebar Layout", 'coworker' ),
    					"desc" => __( "Select Blog Default Layout Sidebar Position", 'coworker' ),
    					"id" => "blog_sidebar",
    					"std" => "right",
    					"type" => "select",
    					"options" => array(
                                'right' => __( 'Right Sidebar', 'coworker' ),
                                'left' => __( 'Left Sidebar', 'coworker' )
                            )
                );
                
$of_options[] = array( 	"name" => __( 'Show Tags', 'coworker' ),
						"desc" => __( 'Enable/Disable Tags on Blog Single', 'coworker' ),
						"id" => "blog_single_tags",
						"std" => 1,
						"on" => __( 'Yes', 'coworker' ),
						"off" => __( 'No', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array( 	"name" => __( 'Show Social Share Icons', 'coworker' ),
						"desc" => __( 'Enable/Disable Social Share Icons on Blog Single', 'coworker' ),
						"id" => "blog_single_social",
						"std" => 1,
						"on" => __( 'Yes', 'coworker' ),
						"off" => __( 'No', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array( 	"name" => __( 'Show Related Posts', 'coworker' ),
						"desc" => __( 'Enable/Disable Related Posts on Blog Single', 'coworker' ),
						"id" => "blog_single_related",
						"std" => 1,
						"on" => __( 'Yes', 'coworker' ),
						"off" => __( 'No', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array(  "name" => __( "Blog - Pagination Type", 'coworker' ),
    					"desc" => __( "Select Pagination Type on Blog Pages", 'coworker' ),
    					"id" => "blog_pagination",
    					"std" => "pager",
    					"type" => "select",
    					"options" => array(
                                'pager' => __( 'Older / Newer Posts', 'coworker' ),
                                'number' => __( 'Numbered Pagination', 'coworker' )
                            )
                );
                
$of_options[] = array(  "name" => __( "Comment System", 'coworker' ),
    					"desc" => __( "Select type of Comment System for Blog Posts", 'coworker' ),
    					"id" => "blog_comments_type",
    					"std" => "default",
    					"type" => "select",
    					"options" => array(
                                'default' => __( 'Default System', 'coworker' ),
                                'disqus' => __( 'Disqus Comments', 'coworker' ),
                                'facebook' => __( 'Facebook Comments', 'coworker' ),
                                'gplus' => __( 'Google+ Comments', 'coworker' )
                            )
                );
                
$of_options[] = array( 	"name" => __( 'Disqus Shortname', 'coworker' ),
						"desc" => __( 'Enter your Disqus Forum Shortname. <a href="http://disqus.com/admin/settings/general/" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "disqus_shortname",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Facebook APP ID', 'coworker' ),
						"desc" => __( 'Enter your Facebook APP ID. <a href="https://developers.facebook.com/apps" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "facebook_app",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Portfolio Options', 'coworker' ),
						"type" => "heading"
				);
                
$of_options[] = array(  "name" => __( "Portfolio Page", 'coworker' ),
    					"desc" => __( "Select Portfolio Page", 'coworker' ),
    					"id" => "portfolio_page",
    					"std" => "",
    					"type" => "select",
    					"options" => $of_pages
                );
                
$of_options[] = array( 	"name" => __( 'Portfolio Archive - Page Title', 'coworker' ),
						"desc" => __( 'Enter Portfolio Archive - Page Title.', 'coworker' ),
						"id" => "portfolio_archive_title",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Portfolio Archive - Page Tagline', 'coworker' ),
						"desc" => __( 'Enter Portfolio Archive - Page Tagline.', 'coworker' ),
						"id" => "portfolio_archive_tagline",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Project Description Title', 'coworker' ),
						"desc" => __( 'Enter Project Description Title.', 'coworker' ),
						"id" => "portfolio_title_desc",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Project Meta Title', 'coworker' ),
						"desc" => __( 'Enter Project Meta Title.', 'coworker' ),
						"id" => "portfolio_title_meta",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Project Meta - Clients Title', 'coworker' ),
						"desc" => __( 'Enter Project Meta - Clients Title.', 'coworker' ),
						"id" => "portfolio_title_meta_clients",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Project Meta - Author Title', 'coworker' ),
						"desc" => __( 'Enter Project Meta - Author Title.', 'coworker' ),
						"id" => "portfolio_title_meta_author",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Project Meta - Date Title', 'coworker' ),
						"desc" => __( 'Enter Project Meta - Date Title.', 'coworker' ),
						"id" => "portfolio_title_meta_date",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Project Meta - Skills Title', 'coworker' ),
						"desc" => __( 'Enter Project Meta - Skills Title.', 'coworker' ),
						"id" => "portfolio_title_meta_skills",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Project Meta - Categories Title', 'coworker' ),
						"desc" => __( 'Enter Project Meta - Categories Title.', 'coworker' ),
						"id" => "portfolio_title_meta_categories",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Project Meta - URL Title', 'coworker' ),
						"desc" => __( 'Enter Project Meta - URL Title.', 'coworker' ),
						"id" => "portfolio_title_meta_url",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Project Meta - Copyrights Title', 'coworker' ),
						"desc" => __( 'Enter Project Meta - Copyrights Title.', 'coworker' ),
						"id" => "portfolio_title_meta_copyrights",
						"std" => "",
						"type" => "text"
				);

$of_options[] = array( 	"name" => __( 'Page Options', 'coworker' ),
						"type" => "heading"
				);
                
$of_options[] = array( 	"name" => __( 'Blog Title', 'coworker' ),
						"desc" => __( 'Enter Blog Page Title.', 'coworker' ),
						"id" => "blog_title",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Blog Tagline', 'coworker' ),
						"desc" => __( 'Enter Blog Page Tagline.', 'coworker' ),
						"id" => "blog_tagline",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array(  "name" => __( "Breadcrumb Feature Page", 'coworker' ),
    					"desc" => __( 'Select a Page you want to show when clicked on the Breadcrumb "Features" Link.', 'coworker' ),
    					"id" => "features_page",
    					"std" => "",
    					"type" => "select",
    					"options" => $of_features
                );
                
$of_options[] = array( 	"name" => __( '404 Page - Title', 'coworker' ),
						"desc" => __( 'Enter 404 Page Title.', 'coworker' ),
						"id" => "error_title",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( '404 Page - Tagline', 'coworker' ),
						"desc" => __( 'Enter 404 Page Tagline.', 'coworker' ),
						"id" => "error_tagline",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( '404 Page - Message', 'coworker' ),
						"desc" => __( 'Enter 404 Page Message.', 'coworker' ),
						"id" => "error_message",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Under Construction Mode', 'coworker' ),
						"desc" => __( 'Enable/Disable Under Construction Mode', 'coworker' ),
						"id" => "comingsoon",
						"std" => 0,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array(  "name" => __( "Under Construction Page", 'coworker' ),
    					"desc" => __( "Select Under Construction Page", 'coworker' ),
    					"id" => "comingsoon_page",
    					"std" => "",
    					"type" => "select",
    					"options" => $of_pages
                );

if( SM_WOOCOMMERCE_ACTIVE ):

$of_options[] = array( 	"name" => __( 'Shop Settings', 'coworker' ),
						"type" => "heading"
				);

$of_options[] = array( 	"name" => __( 'Shop Title', 'coworker' ),
						"desc" => __( 'Enter Shop Page Title.', 'coworker' ),
						"id" => "shop_title",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Shop Tagline', 'coworker' ),
						"desc" => __( 'Enter Shop Page Tagline.', 'coworker' ),
						"id" => "shop_tagline",
						"std" => "",
						"type" => "text"
				);

$of_options[] = array(  "name" => __( "Layout", 'coworker' ),
    					"desc" => __( "Select Layout for Shop Archive Page", 'coworker' ),
    					"id" => "shop_archive_layout",
    					"std" => "4",
    					"type" => "select",
    					"options" => array(
                                '4' => __( '4 Columns', 'coworker' ),
                                '3' => __( '3 Columns', 'coworker' ),
                                '3s' => __( '3 Columns with Sidebar', 'coworker' ),
                                '2s' => __( '2 Columns with Sidebar', 'coworker' )
                            )
                );

$of_options[] = array(  "name" => __( "Sidebar", 'coworker' ),
    					"desc" => __( "Select Sidebar Position for Shop Archive Page", 'coworker' ),
    					"id" => "shop_archive_sidebar",
    					"std" => "nosidebar",
    					"type" => "select",
    					"options" => array(
                                'nosidebar' => __( 'No Sidebar', 'coworker' ),
                                'right' => __( 'Right Sidebar', 'coworker' ),
                                'left' => __( 'Left Sidebar', 'coworker' )
                            )
                );

$of_options[] = array( 	"name" => __( 'No. of Products', 'coworker' ),
						"desc" => __( 'Enter the No. of Items to show on the Shop Products Page. Enter "-1" to show all.', 'coworker' ),
						"id" => "shop_items",
						"std" => "12",
						"type" => "text",
                        "mod" => 'mini'
				);

endif;
                
$of_options[] = array( 	"name" => __( 'Contact Settings', 'coworker' ),
						"type" => "heading"
				);
                
$of_options[] = array( 	"name" => __( 'Receiver Full Name', 'coworker' ),
						"desc" => __( 'Full Name of the Receiver', 'coworker' ),
						"id" => "toname",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Receiver Email Address', 'coworker' ),
						"desc" => __( 'Email Address to which you want to receive your Form Responses', 'coworker' ),
						"id" => "toemail",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Contact Form - Success Message', 'coworker' ),
						"desc" => __( 'Message to display on Successful Form Processing', 'coworker' ),
						"id" => "contact_success",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Contact Form - Error Message', 'coworker' ),
						"desc" => __( 'Message to display on Unsuccessful Form Processing', 'coworker' ),
						"id" => "contact_error",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Contact Form - reCaptcha Error Message', 'coworker' ),
						"desc" => __( 'Message to display on Invalid reCaptcha Authentication', 'coworker' ),
						"id" => "contact_rc_error",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'reCaptcha Public Key', 'coworker' ),
						"desc" => __( 'Enter your reCaptcha Public Key. <a href="http://www.google.com/recaptcha" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "contact_rc_pubkey",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'reCaptcha Private Key', 'coworker' ),
						"desc" => __( 'Enter your reCaptcha Private Key. <a href="http://www.google.com/recaptcha" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "contact_rc_prikey",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Use SMTP', 'coworker' ),
						"desc" => __( 'Enable/Disable SMTP Mode for Sending Form Responses', 'coworker' ),
						"id" => "contact_smtp",
						"std" => 0,
						"on" => __( 'Yes', 'coworker' ),
						"off" => __( 'No', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array( 	"name" => __( 'SMTP Host', 'coworker' ),
						"desc" => __( 'Enter your SMTP Host. Eg. mail.yourdomain.com', 'coworker' ),
						"id" => "smtp_host",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'SMTP Port', 'coworker' ),
						"desc" => __( 'Enter your SMTP Port. Eg. 26', 'coworker' ),
						"id" => "smtp_port",
						"std" => "",
						"type" => "text",
                        "mod" => 'mini'
				);
                
$of_options[] = array( 	"name" => __( 'SMTP Username', 'coworker' ),
						"desc" => __( 'Enter your SMTP Username', 'coworker' ),
						"id" => "smtp_username",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'SMTP Password', 'coworker' ),
						"desc" => __( 'Enter your SMTP Password', 'coworker' ),
						"id" => "smtp_password",
						"std" => "",
						"type" => "text"
				);

$of_options[] = array( 	"name" => __( 'Custom CSS', 'coworker' ),
						"type" => "heading"
				);
                
$of_options[] = array( 	"name" => __( 'Custom CSS', 'coworker' ),
						"desc" => "",
						"id" => "customcssintro",
						"std" => __( '<h3 style="margin: 0 0 10px;">Custom CSS</h3> Paste your Custom CSS Code in the Box below:</a>', 'coworker' ),
						"icon" => true,
						"type" => "info"
				);

$of_options[] = array(  "name" => __( "Custom CSS", 'coworker' ),
    					"desc" => __( "Paste your Custom CSS Code here. Do not add &lt;style&gt; Tags", 'coworker' ),
    					"id" => "customcss",
    					"std" => "",
    					"type" => "textarea"
                );

$of_options[] = array(  "name" => __( 'Sidebar Options', 'coworker' ),
                        "type" => "heading"
                );

$of_options[] = array(  "name" => __( 'Sidebars', 'coworker' ),
    					"desc" => __( 'Register Sidebars Here. Sidebar Names should be <strong>Unique</strong>. Do not use the names of the Existing Sidebars', 'coworker' ),
    					"id" => "sidebargenerator",
    					"std" => "",
    					"type" => "sidebargen"
                );

$of_options[] = array(  "name" => __( 'Footer Settings', 'coworker' ),
                        "type" => "heading"
                );
                
$of_options[] = array( 	"name" => __( 'Twitter Panel', 'coworker' ),
						"desc" => __( 'Enable/Disable Twitter Panel', 'coworker' ),
						"id" => "twitter_panel",
						"std" => 1,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array( 	"name" => __( 'Twitter Panel - Username', 'coworker' ),
						"desc" => __( 'Enter your Username for the Twitter Panel.', 'coworker' ),
						"id" => "twitter_panel_username",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Twitter Panel - Limit', 'coworker' ),
						"desc" => __( 'Enter the No. of Tweets for the Twitter Panel.', 'coworker' ),
						"id" => "twitter_panel_limit",
						"std" => "",
						"type" => "text",
						"mod" => "mini"
				);
                
$of_options[] = array( 	"name" => __( 'Twitter Panel - Speed', 'coworker' ),
						"desc" => __( 'Enter the Fade Speed for the Tweets of the Twitter Panel.', 'coworker' ),
						"id" => "twitter_panel_speed",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Twitter Panel - Follow text', 'coworker' ),
						"desc" => __( 'Enter the Text of the Follow Button for the Twitter Panel.', 'coworker' ),
						"id" => "twitter_panel_follow_text",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Footer Area', 'coworker' ),
						"desc" => __( 'Enable/Disable Footer Area', 'coworker' ),
						"id" => "footer",
						"std" => 1,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array(  "name" => __( "Footer - Color Scheme", 'coworker' ),
    					"desc" => __( "Select Footer Color Scheme", 'coworker' ),
    					"id" => "footer_color",
    					"std" => "dark",
    					"type" => "select",
    					"options" => array(
                                'dark' => __( 'Dark', 'coworker' ),
                                'light' => __( 'Light', 'coworker' )
                            )
                );
                
$of_options[] = array( 	"name" => __( 'Copyrights Area', 'coworker' ),
						"desc" => __( 'Enable/Disable Copyrights Area', 'coworker' ),
						"id" => "copyrights",
						"std" => 1,
						"on" => __( 'Enable', 'coworker' ),
						"off" => __( 'Disable', 'coworker' ),
						"type" => "switch"
				);
                
$of_options[] = array(  "name" => __( "Copyrights - Color Scheme", 'coworker' ),
    					"desc" => __( "Select Copyrights Color Scheme", 'coworker' ),
    					"id" => "copyrights_color",
    					"std" => "dark",
    					"type" => "select",
    					"options" => array(
                                'dark' => __( 'Dark', 'coworker' ),
                                'light' => __( 'Light', 'coworker' )
                            )
                );
                
$of_options[] = array(  "name" => __( "Copyrights - Left Content", 'coworker' ),
    					"desc" => __( "Content to show in the Copyrights - Left Area. HTML Supported", 'coworker' ),
    					"id" => "copyrights_left",
    					"std" => "",
    					"type" => "textarea"
                );
                                               
$of_options[] = array(  "name" => __( "Copyrights - Right Content", 'coworker' ),
    					"desc" => __( "Content to show in the Copyrights - Right Area. HTML Supported", 'coworker' ),
    					"id" => "copyrights_right",
    					"std" => "",
    					"type" => "textarea"
                );
                                               
$of_options[] = array(  "name" => __( "Footer Code", 'coworker' ),
    					"desc" => __( "Paste your Code here to place it in the Footer before the &lt;/body&gt; Tag", 'coworker' ),
    					"id" => "footercode",
    					"std" => "",
    					"type" => "textarea"
                );
                
$of_options[] = array( 	"name" => __( 'API Keys', 'coworker' ),
						"type" => "heading"
				);
                
$of_options[] = array( 	"name" => __( 'Twitter - Consumer Key', 'coworker' ),
						"desc" => __( 'Enter your Twitter Consumer Key. <a href="https://dev.twitter.com/" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "api_twitter_consumer",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Twitter - Consumer Secret', 'coworker' ),
						"desc" => __( 'Enter your Twitter Consumer Secret. <a href="https://dev.twitter.com/" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "api_twitter_consumer_secret",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Twitter - Access Token', 'coworker' ),
						"desc" => __( 'Enter your Twitter Access Token. <a href="https://dev.twitter.com/" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "api_twitter_access",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Twitter - Access Token Secret', 'coworker' ),
						"desc" => __( 'Enter your Twitter Access Token Secret. <a href="https://dev.twitter.com/" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "api_twitter_access_secret",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Instagram - Access Token', 'coworker' ),
						"desc" => __( 'Enter your Instagram Access Token. <a href="http://instagram.com/developer/" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "api_instagram_access",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'Instagram - Client ID', 'coworker' ),
						"desc" => __( 'Enter your Instagram Client ID. <a href="http://instagram.com/developer/" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "api_instagram_client",
						"std" => "",
						"type" => "text"
				);
                
$of_options[] = array( 	"name" => __( 'MailChimp - API Key', 'coworker' ),
						"desc" => __( 'Enter your MailChimp API Key. <a href="https://admin.mailchimp.com/account/api/" target="_blank">Get it Here</a>', 'coworker' ),
						"id" => "api_mailchimp",
						"std" => "",
						"type" => "text"
				);

$of_options[] = array( 	"name" => __( 'Backup Options', 'coworker' ),
						"type" => "heading"
				);

$of_options[] = array( 	"name" => __( 'Backup and Restore Options', 'coworker' ),
						"id" => "of_backup",
						"std" => "",
						"type" => "backup",
						"desc" => __( 'You can use the two buttons below to backup your current options, and then restore it back at a later time. This is useful if you want to experiment on the options but would like to keep the old settings in case you need it back', 'coworker' )
				);

$of_options[] = array( 	"name" => __( 'Transfer Theme Options Data', 'coworker' ),
						"id" => "of_transfer",
						"std" => "",
						"type" => "transfer",
						"desc" => __( 'You can tranfer the saved options data between different installs by copying the text inside the text box. To import data from another install, replace the data in the text box with the one from another install and click "Import Options"', 'coworker' )
				);

	}
    
}

?>