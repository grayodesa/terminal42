<?php if( has_post_thumbnail() ):

$thumb = get_sized_image( 'large', true );

?>

<div class="entry_image">
                                
                                    <a href="<?php echo ( get_post_format() == 'image' ? get_full_image() : get_permalink() ); ?>" class="image_fade" <?php echo ( get_post_format() == 'image' ? 'data-lightbox="image"' : '' ); ?>><img src="<?php echo $thumb[0]; ?>" width="<?php echo $thumb[1]; ?>" height="<?php echo $thumb[2]; ?>" title="<?php the_title_attribute(); ?>" alt="<?php the_title_attribute(); ?>" /></a>
                                
                                </div>
                                
                                <?php endif; ?>