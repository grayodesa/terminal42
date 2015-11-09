<div class="sidebar-widgets-wrap clearfix">
                    
                    <?php

                    if( function_exists( 'is_woocommerce' ) AND is_woocommerce() ) {

                    	if ( !function_exists( 'generated_dynamic_sidebar' ) || !generated_dynamic_sidebar( 'WooCommerce' ) )
                    		generated_dynamic_sidebar( 'WooCommerce' );

                    } else {

                         if ( !function_exists( 'generated_dynamic_sidebar' ) || !generated_dynamic_sidebar( 'Sidebar' ) )
                              generated_dynamic_sidebar( 'Sidebar' );

				}

				?>
                    
                    </div>
                    
                    <div class="clear"></div>