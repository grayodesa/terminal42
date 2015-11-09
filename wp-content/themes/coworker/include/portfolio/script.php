<script type="text/javascript">
                        
                        <?php if( get_post_meta( get_the_ID(), 'semi_page_port_hash_history', true ) == 1 ): ?>

                        jQuery(document).ready(function($){
                        
                            <?php if( is_page_template('template-portfolio5.php') ) { ?>
                            
                            portfolioHeightAdjust=function(){
                            
                            $(".portfolio-item .portfolio-image").each(function(){
                                
                                var portfolioWidth = $(this).outerWidth();
                                var portfolioImageHeight = $(this).find("a:not(.hidden) img").attr("height");
                                var portfolioImageH = ( portfolioWidth * portfolioImageHeight / 188 )
                                $(this).find("a img").css("height", portfolioImageH + "px");
                                
                            });
                            
                            }; portfolioHeightAdjust();
                            
                            <?php } ?>
                            
                            var $container = $('#portfolio');
                            
                            $container.isotope();
                            
                            $('#portfolio-filter a').click(function(){
                                
                                $('#portfolio-filter li').removeClass('activeFilter');
                                $(this).parent('li').addClass('activeFilter');
                                var selector = $(this).attr('data-filter');
                                $container.isotope({ filter: selector });
                                return false;
                                
                            });
                            
                            $(window).resize(function() {
                                $container.isotope('reLayout');
                                <?php if( is_page_template('template-portfolio5.php') ) { ?>portfolioHeightAdjust();<?php } ?>
                            });
                        
                        });

                        <?php else: ?>

                        jQuery(document).ready(function($){
                            
                            var $container = $('#portfolio'),
                                $portfolioFilter = $('#portfolio-filter'),
                                isFilterClicked = false;
                            
                            $container.isotope();
                            
                            function changeSelectedFilter( $elem ) {
                            
                                $elem.parents('#portfolio-filter').find('.activeFilter').removeClass('activeFilter');
                                
                                $elem.parent('li').addClass('activeFilter');
                            
                            }
                            
                            $portfolioFilter.find('a').click(function(){
                            
                                var $this = $(this);
                                
                                if ( $this.parent('li').hasClass('activeFilter') ) {
                                    return;
                                }
                                
                                changeSelectedFilter( $this );
                                
                                var portHashhref = $(this).attr('href').replace( /^#/, '' ),
                                
                                portHashoption = $.deparam( portHashhref, true );
                                
                                $.bbq.pushState( portHashoption );
                                
                                isFilterClicked = true;
                                
                                return false;
                            
                            });
                            
                            var hashChanged = false;
                            
                            $(window).bind( 'hashchange', function( event ){
                                
                                var hashOptions = $.deparam.fragment();
                                
                                $container.isotope( hashOptions );
                                
                                if ( !isFilterClicked ) {
                                
                                    var hrefObj, hrefValue, $selectedLink;
                                    
                                    for ( var key in hashOptions ) {
                                    
                                        hrefObj = {};
                                        hrefObj[ key ] = hashOptions[ key ];
                                        hrefValue = $.param( hrefObj );
                                        $selectedLink = $portfolioFilter.find('a[href="#' + hrefValue + '"]');
                                        changeSelectedFilter( $selectedLink );
                                    
                                    }
                                
                                }
                                
                                isFilterClicked = false;
                                hashChanged = true;
                            
                            }).trigger('hashchange');
                            
                            $(window).resize(function() {
                                $container.isotope('reLayout');
                            });
                        
                        });

                        <?php endif; ?>
                    
                    </script>