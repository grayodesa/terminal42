<?php

if( is_singular( array( 'post' ) ) ) {

    if( get_post_meta( get_the_ID(), 'semi_sidebar', true ) != 'left' ) {
        $align = ' col_last';
    }

} else {

    if( get_post_meta( get_queried_object_id(), 'semi_page_sidebar', true ) != 'left' ) {
        $align = ' col_last';
    }

}

?>
<div class="sidebar nobottommargin clearfix<?php echo $align; ?>">
                    
                        <?php get_template_part( 'include/sidebar' ); ?>
                    
                    </div>