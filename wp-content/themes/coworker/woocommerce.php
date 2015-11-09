<?php get_header();

$sidebar_layout = semi_option('shop_archive_sidebar');
$columns_layout = semi_option('shop_archive_layout');

if( is_post_type_archive( 'product' ) ):

    if( $columns_layout == '4' OR $columns_layout == '3' ) {
        $sidebar_layout = 'nosidebar';
    }

endif;

?>
        
        
        <?php get_template_part( 'include/content', 'head' ); ?>
                
                <?php if( !is_product() ): if( $sidebar_layout != 'nosidebar' ): ?>

                    <div class="postcontent nobottommargin<?php echo ( $sidebar_layout == 'left' ) ? ' col_last' : ''; ?>">

                <?php endif; endif; ?>

                    <?php woocommerce_content(); ?>

                <?php if( !is_product() ): if( $sidebar_layout != 'nosidebar' ): ?>

                    </div>

                    <?php if( $sidebar_layout == 'left' ) { get_sidebar('left'); } else { get_sidebar( 'right' ); } ?>

                <?php endif; endif; ?>
                
        <?php get_template_part( 'include/content', 'foot' ); ?>
        
        
<?php get_footer(); ?>