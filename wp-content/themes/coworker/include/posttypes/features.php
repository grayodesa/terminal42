<?php

require_once('posttypes.class.php');

$features_posttype = new Add_post_types;

$options = array(
  'single' => __( 'Features', 'coworker' ),
  'plural' => __( 'Features', 'coworker' ),
  'singlealt' => __( 'Feature', 'coworker' ),
  'pluralalt' => __( 'Features', 'coworker' ),
  'type' => 'features',
  'support' => array('title','editor','thumbnail'),
  'rewrite' => array( 'slug' => 'feature' ),
  'icon' => 'dashicons-lightbulb'
);

$features_posttype->init($options);

add_action('init', array(&$features_posttype, 'add_post_type'));
add_filter('post_updated_messages', array(&$features_posttype, 'add_messages'));


function semi_enable_features_sort() {
    add_submenu_page('edit.php?post_type=features', __( 'Sort Features', 'coworker' ), __( 'Sort Features', 'coworker' ), 'edit_posts', 'sort_features_items', 'semi_sort_features');
}
add_action('admin_menu' , 'semi_enable_features_sort');
 
 
function semi_sort_features() {
	$features = new WP_Query('post_type=features&posts_per_page=-1&orderby=menu_order&order=ASC');
?>
	<div class="wrap">
	<h2><?php _e( 'Sort Features', 'coworker' ) ?> <img src="<?php echo get_template_directory_uri(); ?>/images/preloader.gif" id="loading-animation" /></h2>
	<ul id="portfolio-item-list">
	<?php while ( $features->have_posts() ) : $features->the_post(); ?>
		<li id="<?php the_ID(); ?>"><?php the_title(); ?></li>			
	<?php endwhile; ?>
	</div><!-- End div#wrap //-->
 
<?php
}


function semi_featuressort_print_scripts() {
	global $pagenow;
 
	$pages = array('edit.php');
	if (in_array($pagenow, $pages)) {
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('portfoliosortJS', get_template_directory_uri() . '/include/posttypes/portfoliosort.js');
	}
}
add_action( 'admin_print_scripts', 'semi_featuressort_print_scripts' );


function semi_featuressort_print_styles() {
	global $pagenow;
 
	$pages = array('edit.php');
	if (in_array($pagenow, $pages))
		wp_enqueue_style('portfoliosortCSS', get_template_directory_uri() . '/include/posttypes/portfoliosort.css');
}
add_action( 'admin_print_styles', 'semi_featuressort_print_styles' );

 
function semi_save_featuressort_order() {
	global $wpdb;
 
	$order = explode(',', $_POST['portfoliosortorder']);
	$counter = 0;
 
	foreach ($order as $portfolio_id) {
		$wpdb->update($wpdb->posts, array( 'menu_order' => $counter ), array( 'ID' => $portfolio_id) );
		$counter++;
	}
	die(1);
}
add_action('wp_ajax_features_sort', 'semi_save_featuressort_order');


?>