<?php

/*-------------------------------------------------

	Plugin Name: Tabbed Posts
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget to show Popular/Recent/Comments Tabbed Posts
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/

class SEMI_Tabbed extends WP_Widget {

	function SEMI_Tabbed() {
	$widget_ops = array( 'classname' => 'tabbed-widget', 'description' => __( 'Displays your Popular/Recent/Comments Tabbed Posts', 'coworker' ) );
		$this->WP_Widget( 'tabbed_widget', __( 'CoWorker: Tabbed Posts', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Recent Posts', 'number' => 3) );

        $title = esc_attr($instance['title']);
		$number = absint($instance['number']);

?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               <?php _e('Title:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
               <?php _e('No. of Posts:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>


<?php
    }

	function update($new_instance, $old_instance) {
        $instance=$old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = $new_instance['number'];
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

		$number = absint( $instance['number'] );
		
		
		echo $before_widget;
	
		if($title){
			echo $before_title;
			echo $title; 
			echo $after_title;
		}
        
        $getmrand = mt_rand(1,1000);
        
        ?>
        
        
        <div class="tab_widget nobottommargin" id="tabbed-widget-<?php echo $getmrand; ?>">
        
            <ul class="tabs">
                <li><a href="#tab-popular-<?php echo $getmrand; ?>" data-href="#tab-popular-<?php echo $getmrand; ?>"><?php _e( 'Popular', 'coworker' ); ?></a></li>
                <li><a href="#tab-recent-<?php echo $getmrand; ?>" data-href="#tab-recent-<?php echo $getmrand; ?>"><?php _e( 'Recent', 'coworker' ); ?></a></li>
                <li><a href="#tab-comments-<?php echo $getmrand; ?>" data-href="#tab-comments-<?php echo $getmrand; ?>"><i class="icon-comments-alt norightmargin"></i></a></li>
            </ul>
            
            <div class="tab_container">
                
                <div id="tab-popular-<?php echo $getmrand; ?>" class="tab_content clearfix">
                
                    <?php
                    
                    $popularargs = array( 'post_type' => 'post', 'posts_per_page' => $number, 'orderby' => 'comment_count' );
            		
                    $popularpostswidget = new WP_Query( $popularargs );
                    
                    if( $popularpostswidget->have_posts() ):
                    
                    echo '<ul class="sposts-list clearfix">';
                    
                    while ( $popularpostswidget->have_posts() ) : $popularpostswidget->the_post();
                    
                    $popularthumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full');
                    
                    $popularthumb = semi_resize( $popularthumb[0], 64, 64, true, false );
            		
                    ?>
            
            			<li class="clearfix">
            
                            <?php if ( $popularthumb[0] ) { ?>
                            
                            <div class="spost-image">
                                <a href="<?php the_permalink(); ?>"><img src="<?php echo $popularthumb[0]; ?>" width="<?php echo $popularthumb[1]; ?>" height="<?php echo $popularthumb[2]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
                            </div>
                            
                            <?php } ?>
                        
                            <div class="spost-content clearfix">
                                <div class="spost-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></div>
                                <div class="spost-meta clearfix">
                                    <ul>
                                        <li><a href="<?php echo get_permalink().'#comments'; ?>"><i class="icon-comments-alt"></i> <?php comments_number( __( 'No Comments', 'coworker' ) , __( '1 Comment', 'coworker' ), __( '% Comments', 'coworker' ) ); ?></a></li>
                                    </ul>
                                </div>
                            </div>
            
                        </li>
                    
                    <?php
                    
                    endwhile;
                    
                    echo '</ul>';
                    
                    else:
                    
                    _e( 'No Popular Posts.', 'coworker' );
                    
                    endif;
                        
                    wp_reset_postdata();
                    
                    ?>
                
                </div>
                
                <div id="tab-recent-<?php echo $getmrand; ?>" class="tab_content clearfix">
                
                    <?php
                    
                    $recentargs = array( 'post_type' => 'post', 'posts_per_page' => $number, 'orderby' => 'date' );
            		
                    $recentpostswidget = new WP_Query( $recentargs );
                    
                    if( $recentpostswidget->have_posts() ):
                    
                    echo '<ul class="sposts-list clearfix">';
                    
                    while ( $recentpostswidget->have_posts() ) : $recentpostswidget->the_post();
                    
                    $recentthumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full');
                    
                    $recentthumb = semi_resize( $recentthumb[0], 64, 64, true, false );
            		
                    ?>
            
            			<li class="clearfix">
            
                            <?php if ( $recentthumb[0] ) { ?>
                            
                            <div class="spost-image">
                                <a href="<?php the_permalink(); ?>"><img src="<?php echo $recentthumb[0]; ?>" width="<?php echo $recentthumb[1]; ?>" height="<?php echo $recentthumb[2]; ?>" alt="<?php the_title_attribute(); ?>" /></a>
                            </div>
                            
                            <?php } ?>
                        
                            <div class="spost-content clearfix">
                                <div class="spost-title"><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></div>
                                <div class="spost-meta clearfix">
                                    <ul>
                                        <li><i class="icon-calendar"></i> <?php the_time( __( 'jS F', 'coworker' ) ); ?></li>
                                    </ul>
                                </div>
                            </div>
            
                        </li>
                    
                    <?php
                    
                    endwhile;
                    
                    echo '</ul>';
                    
                    else:
                    
                    _e( 'No Recent Posts.', 'coworker' );
                    
                    endif;
                        
                    wp_reset_postdata();
                    
                    ?>
                
                </div>
                
                <div id="tab-comments-<?php echo $getmrand; ?>" class="tab_content clearfix">
                
                    <?php
                    
                        $getcomments = get_comments( array( 'status' => 'approve', 'number' => $number ) );
                        
                        $getcommentscount = count( $getcomments );
                        
                        if( $getcommentscount > 0 ) {
                            
                    ?>
                    
                    <ul class="sposts-list clearfix">
                    
                    <?php foreach( $getcomments as $getcomment ) : ?>
                    
                        <li class="clearfix">
            
                            <div class="spost-image">
                                <?php echo get_avatar( $getcomment, 64 ); ?>
                            </div>
                        
                            <div class="spost-content clearfix"><strong><?php echo $getcomment->comment_author; ?>:</strong> <?php echo custom_textlimit( strip_tags( $getcomment->comment_content ), 10 ); ?></div>
            
                        </li>
                        
                    <?php endforeach; ?>
                        
                    </ul>
                    
                    <?php
                    
                        } else {
                            _e( 'No Comments Yet.', 'coworker' );
                        }
                    
                    ?>
                
                </div>
                
            </div>
            
        </div>
        
        <script type="text/javascript">
        
            jQuery(document).ready(function($) {
                
                tab_widget( '#tabbed-widget-<?php echo $getmrand; ?>' );
            
            });
        
        </script>
        
		<?php
        
		echo $after_widget;
		
	}

}


add_action( 'widgets_init', 'semi_widget_tabbed' );
function semi_widget_tabbed() {
	register_widget('SEMI_Tabbed');
}
    
?>