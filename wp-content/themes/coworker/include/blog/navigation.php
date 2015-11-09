<?php if( semi_option( 'blog_pagination' ) == 'number' ):

semi_pagination();

else:

if( get_previous_posts_link() OR get_next_posts_link() ): ?>
                            
                            <ul class="pager nobottommargin">
                                <li class="previous">
                                    <?php next_posts_link( __( '&larr; Older Posts', 'coworker' ) ); ?>
                                </li>
                                <li class="next">
                                    <?php previous_posts_link( __( 'Newer Posts &rarr;', 'coworker' ) ); ?>
                                </li>
                            </ul>
                            
                            <?php endif; endif; ?>