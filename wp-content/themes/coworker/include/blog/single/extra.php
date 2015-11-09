<?php $pinterestthumb = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'large');

if( semi_option( 'blog_single_tags' ) == 1 ):

?>
<div class="tagcloud clearfix">
                                
                                    <?php the_tags( '', '' ); ?>
                                
                                </div>
                                
                                <?php endif;
                                
                                if( semi_option( 'blog_single_social' ) == 1 ):
                                
                                ?>
                                
                                <div class="entry_share clearfix">
                                
                                    <span><strong><?php _e( 'Share this Post:', 'coworker' ); ?></strong></span>
                                    
                                    <a href="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>&amp;t=<?php the_title(); ?>" target="_blank" class="ntip" title="<?php _e( 'Share on Facebook', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/facebook.png" alt="Facebook" /></a>
                                    <a href="https://twitter.com/intent/tweet?source=coworkertheme&amp;text=<?php the_title(); ?>&amp;url=<?php the_permalink(); ?>" target="_blank" class="ntip" title="<?php _e( 'Tweet on Twitter', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/twitter.png" alt="Twitter" /></a>
                                    <a href="http://pinterest.com/pin/create/button/?url=<?php the_permalink();?>&amp;media=<?php echo $pinterestthumb[0]; ?>&amp;description=<?php echo strip_tags( get_the_excerpt() ); ?>" target="_blank" class="ntip" title="<?php _e( 'Pin it on Pinterest', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/pinterest.png" alt="Pinterest" /></a>
                                    <a href="https://plus.google.com/share?url=<?php the_permalink();?>" target="_blank" class="ntip" title="<?php _e( 'Share on Google Plus', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/googleplus.png" alt="Google Plus" /></a>
                                    <a href="http://www.stumbleupon.com/submit?url=<?php the_permalink();?>&amp;title=<?php the_title();?>" target="_blank" class="ntip" title="<?php _e( 'Share on StumbleUpon', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/stumbleupon.png" alt="StumbleUpon" /></a>
                                    <a href="http://reddit.com/submit?url=<?php the_permalink();?>&amp;title=<?php the_title();?>" target="_blank" class="ntip" title="<?php _e( 'Share on Reddit', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/reddit.png" alt="Reddit" /></a>
                                    <a href="<?php echo get_post_comments_feed_link( get_the_ID(), 'rss2' ); ?>" target="_blank" class="ntip" title="<?php _e( 'RSS Feed', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/rss.png" alt="RSS" /></a>
                                    <a href="mailto:?subject=<?php the_title();?>&amp;body=<?php echo strip_tags( get_the_excerpt() ); ?> <?php the_permalink();?>" target="_blank" class="ntip" title="<?php _e( 'Email this Post', 'coworker' ); ?>"><img src="<?php echo get_stylesheet_directory_uri() ?>/images/icons/social/post/email.png" alt="Email" /></a>
                                
                                </div>
                                
                                <?php endif; ?>