<?php if( is_single() ): ?>
<div class="entry_title"><h2 title="<?php the_title_attribute(); ?>"><?php the_title(); ?></h2></div>
<?php else: ?>
<div class="entry_title"><h2><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2></div>
<?php endif; ?>