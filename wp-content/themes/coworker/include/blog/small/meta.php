<?php
$categories = get_the_category();
$separator = ', ';
$output = '';
$count = 0;
if($categories){
	foreach($categories as $category) {
        $count++;
        $output .= '<a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( __( "View all posts in %s", 'coworker' ), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
        if( $count == 2 ) { break; }
	}
}
?>
<ul class="entry_meta clearfix">
                                    
                                        <li><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><i class="icon-user"></i><?php the_author_meta( 'display_name' ); ?></a></li>
                                        <?php if( $categories ): ?>
                                        <li><span>/</span><i class="icon-copy"></i><?php echo trim($output, $separator); ?></li>
                                        <?php endif; ?>
                                        <li><span>/</span><a href="<?php the_permalink(); ?>#comments"><i class="icon-comments"></i><?php show_comment_count(); ?></a></li>
                                    
                                    </ul>