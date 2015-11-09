<ul id="portfolio-filter" class="bottommargin clearfix">
                        
                        
                            <li class="activeFilter"><a href="#filter=*" data-filter="*"><?php _e('All', 'coworker'); ?></a></li>
                            <?php
                            
                                $terms = get_terms( "port-group", array( 'include' => choosen_port_cat() ) );
                                $count = count( $terms );
                                if ( $count > 0 ){

                                    if( get_post_meta( get_the_ID(), 'semi_page_port_hash_history', true ) == 1 ) {

                                        foreach ( $terms as $term ) {
                                            echo '<li><a href="#filter=.pf-' . $term->slug . '" data-filter=".pf-' . $term->slug . '">' . $term->name . '</a></li>';
                                        }

                                    } else {

                                        foreach ( $terms as $term ) {
                                            echo '<li><a href="#" data-filter=".pf-' . $term->slug . '">' . $term->name . '</a></li>';
                                        }

                                    }

                                }
                            ?>
                        
                        
                        </ul>