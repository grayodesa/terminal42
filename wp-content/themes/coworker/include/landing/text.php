<?php if( get_post_meta( get_the_ID(), 'semi_page_landing_title', TRUE ) != '' ): ?>
                    
                    <h1><?php echo get_post_meta( get_the_ID(), 'semi_page_landing_title', TRUE ); ?></h1>
                    
                    <?php endif; ?>
                    
                    <?php if( get_post_meta( get_the_ID(), 'semi_page_landing_description', TRUE ) != '' ): ?>
                    
                    <p class="landing-desc"><?php echo get_post_meta( get_the_ID(), 'semi_page_landing_description', TRUE ); ?></p>
                    
                    <?php endif; ?>
                    
                    <?php if( get_post_meta( get_the_ID(), 'semi_page_landing_features', TRUE ) != '' ): ?>
                    
                    <ul class="landing-features">
                    
                        <?php echo get_post_meta( get_the_ID(), 'semi_page_landing_features', TRUE ); ?>
                    
                    </ul>
                    
                    <?php endif; ?>
                    
                    <?php if( get_post_meta( get_the_ID(), 'semi_page_landing_action', TRUE ) != '' ): ?>
                    
                    <div class="landing-action">
                    
                        <?php echo do_shortcode( get_post_meta( get_the_ID(), 'semi_page_landing_action', TRUE ) ); ?>
                    
                    </div>
                    
                    <?php endif; ?>