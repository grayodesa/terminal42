<ul class="entry_meta clearfix">
                                    
                                        <?php if( is_single() ): ?>
                                        <li><i class="icon-calendar"></i><?php the_date( get_option('date_format') ); ?></li>
                                        <li><span>/</span><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><i class="icon-user"></i><?php the_author_meta( 'display_name' ); ?></a></li>
                                        <?php else: ?>
                                        <li><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><i class="icon-user"></i><?php the_author_meta( 'display_name' ); ?></a></li>
                                        <?php endif; ?>
                                        <?php if( has_category() ): ?>
                                        <li><span>/</span><i class="icon-copy"></i><?php the_category( ', ' ); ?></li>
                                        <?php endif; ?>
                                        <li><span>/</span><a href="<?php the_permalink(); ?>#comments"><i class="icon-comments"></i><?php show_comment_count(); ?></a></li>
                                    
                                    </ul>