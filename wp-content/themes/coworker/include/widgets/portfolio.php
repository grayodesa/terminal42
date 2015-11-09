<?php

/*-------------------------------------------------

	Plugin Name: Portfolio Carousel
	Plugin URI: http://themeforest.net/user/SemiColonWeb
	Description: Widget to show the Portfolio Carousel
	Version: 1.0
	Author: SemiColonWeb
	Author URI: http://themeforest.net/user/SemiColonWeb

---------------------------------------------------*/

class SEMI_Porfolio extends WP_Widget {

	function SEMI_Porfolio() {
	$widget_ops = array( 'classname' => 'portfolio-widget', 'description' => __( 'Show off your Portfolio Items in a Carousel', 'coworker' ) );
		$this->WP_Widget( 'portfolio_widget', __( 'CoWorker: Portfolio Scroller', 'coworker' ), $widget_ops);
	}
	
	function form($instance) {
	
		
		$instance = wp_parse_args( (array) $instance, array('title' => 'Portfolio', 'number' => 3, 'display' => 'recent', 'speed' => '900') );

        $title = esc_attr($instance['title']);
		$number = absint($instance['number']);
        $display = $instance['display'];
        $speed = $instance['speed'];

?>
		<p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
               <?php _e('Title:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>

		<p>
            <label for="<?php echo $this->get_field_id('number'); ?>">
               <?php _e('No. of Items:', 'coworker'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('display'); ?>">
               <?php _e('Display Type:', 'coworker'); ?>
            </label>
            <select id="<?php echo $this->get_field_id('display'); ?>" name="<?php echo $this->get_field_name('display'); ?>">
                <option value="recent" <?php if ( $display == 'recent' ) echo 'selected'; ?>>Recent</option>
                <option value="popular" <?php if ( $display == 'popular' ) echo 'selected'; ?>>Popular</option>
                <option value="random" <?php if ( $display == 'random' ) echo 'selected'; ?>>Random</option>
                <option value="menu_order" <?php if ( $display == 'menu_order' ) echo 'selected'; ?>>Sort Order</option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('speed'); ?>">
               <?php _e('Animation Speed:', 'coworker'); ?>
            </label>
            <input id="<?php echo $this->get_field_id('speed'); ?>" name="<?php echo $this->get_field_name('speed'); ?>" type="text" value="<?php echo $speed; ?>" size="5" />
        </p>


<?php
    }

	function update($new_instance, $old_instance) {
        $instance=$old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = $new_instance['number'];
        $instance['display'] = $new_instance['display'];
        $instance['speed'] = $new_instance['speed'];
        return $instance;

    }

	function widget($args, $instance) {
	
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) ) $title = false;

		$number = absint( $instance['number'] );
        $display = $instance['display'];
        $speed = is_numeric( $instance['speed'] ) ? $instance['speed'] : 900;
			
		echo $before_widget;
	
		if($title){
			echo $before_title;
			echo $title; 
			echo $after_title;
		}
        
        
        if( $display == 'recent' ) {
            
            $args = array( 'post_type' => 'portfolio', 'posts_per_page' => $number, 'orderby' => 'date' );
            
        } elseif( $display == 'popular' ) {
            
            $args = array( 'post_type' => 'portfolio', 'posts_per_page' => $number, 'orderby' => 'comment_count' );
            
        } elseif( $display == 'random' ) {
            
            $args = array( 'post_type' => 'portfolio', 'posts_per_page' => $number, 'orderby' => 'rand' );
            
        } elseif( $display == 'menu_order' ) {
            
            $args = array( 'post_type' => 'portfolio', 'posts_per_page' => $number, 'orderby' => 'menu_order', 'order' => 'ASC' );
            
        } else {
            
            $args = array( 'post_type' => 'portfolio', 'posts_per_page' => $number, 'orderby' => 'date' );
            
        }
        
        $getmrand = mt_rand(1,1000);
		
        $portfoliowidget = new WP_Query( $args );
        
        if( $portfoliowidget->have_posts() ):
        
        ?>
        
        <div id="widget-portfolio-<?php echo $getmrand; ?>" class="portfolio-widget-scroll">
        
        <?php while ( $portfoliowidget->have_posts() ) : $portfoliowidget->the_post();
        
            get_portfolio_items( 231, 180, 5 );
        
        endwhile; ?>
        
        </div>
        
        <div id="widget-portfolio-<?php echo $getmrand; ?>-prev" class="widget-scroll-prev"></div>
        <div id="widget-portfolio-<?php echo $getmrand; ?>-next" class="widget-scroll-next"></div>
        
        <script type="text/javascript">
        
            jQuery(document).ready(function($) {
                
                var portfolioCarousel = $("#widget-portfolio-<?php echo $getmrand; ?>");
                
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
                            max: 1
                        }
                    },
                    scroll : {
                        wipe : true
                    },
                    prev : {	
                        button : "#widget-portfolio-<?php echo $getmrand; ?>-prev",
                        key : "left"
                    },
                    next : { 
                        button : "#widget-portfolio-<?php echo $getmrand; ?>-next",
                        key : "right"
                    },
                    onCreate : function () {
                        $(window).on('resize', function(){
                            portfolioCarousel.parent().add(portfolioCarousel).css('height', portfolioCarousel.children().first().outerHeight() + 'px');
                        }).trigger('resize');
                    }
                });
            
            });
        
        </script>
        
        <?php
        
        endif;
            
        wp_reset_postdata();
        		
		echo $after_widget;
		
	}

}


add_action( 'widgets_init', 'semi_widget_portfolio' );
function semi_widget_portfolio() {
	register_widget('SEMI_Porfolio');
}
    
?>