<?php if( get_post_meta( get_the_ID(), 'semi_post_comments_system', TRUE ) == 'themeoption' ):

    if( semi_option('blog_comments_type') == 'disqus' ): ?>

<div id="comments" class="clearfix">
                        
                            <div id="disqus_thread"></div>
                            <script type="text/javascript">
                                /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                                var disqus_shortname = '<?php echo semi_option('disqus_shortname'); ?>'; // required: replace example with your forum shortname
                                var disqus_identifier = '<?php the_ID(); ?>';
                                var disqus_title = '<?php the_title(); ?>';
                                var disqus_url = '<?php the_permalink(); ?>';                                    
                                
                                /* * * DON'T EDIT BELOW THIS LINE * * */
                                (function() {
                                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                                })();
                            </script>
                        
                        </div>

<?php elseif( semi_option('blog_comments_type') == 'facebook' ): ?>

<div id="comments" class="clearfix">
                            
                                <h3 id="comments-title"><span><fb:comments-count href="<?php the_permalink(); ?>"></fb:comments-count></span> Comments</h3>
    
                        		<div class="fb-comments" data-href="<?php the_permalink(); ?>" data-width="960px" data-num-posts="10"></div>
                            
                            </div>

<?php elseif( semi_option('blog_comments_type') == 'gplus' ): ?>

<div id="comments" class="gpluscomments clearfix">
    
                        		<div class="g-comments" data-href="<?php the_permalink(); ?>" data-width="720" data-first_party_property="BLOGGER" data-view_type="FILTERED_POSTMOD"></div>
                                
                                <script src="https://apis.google.com/js/plusone.js"></script>
                            
                            </div>

<?php else:

    comments_template('', true);
    
    endif;
    
elseif( get_post_meta( get_the_ID(), 'semi_post_comments_system', TRUE ) == 'disqus' ): ?>

<div id="comments" class="clearfix">
                        
                            <div id="disqus_thread"></div>
                            <script type="text/javascript">
                                /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                                var disqus_shortname = '<?php echo semi_option('disqus_shortname'); ?>'; // required: replace example with your forum shortname
                                var disqus_identifier = '<?php the_ID(); ?>';
                                var disqus_title = '<?php the_title(); ?>';
                                var disqus_url = '<?php the_permalink(); ?>';                                    
                                
                                /* * * DON'T EDIT BELOW THIS LINE * * */
                                (function() {
                                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                                })();
                            </script>
                        
                        </div>

<?php elseif( get_post_meta( get_the_ID(), 'semi_post_comments_system', TRUE ) == 'facebook' ): ?>

<div id="comments" class="clearfix">
                            
                                <h3 id="comments-title"><span><fb:comments-count href="<?php the_permalink(); ?>"></fb:comments-count></span> Comments</h3>
    
                        		<div class="fb-comments" data-href="<?php the_permalink(); ?>" data-width="960px" data-num-posts="10"></div>
                            
                            </div>

<?php elseif( get_post_meta( get_the_ID(), 'semi_post_comments_system', TRUE ) == 'gplus' ): ?>

<div id="comments" class="gpluscomments clearfix">
                            
                                <div class="g-comments" data-href="<?php the_permalink(); ?>" data-width="720" data-first_party_property="BLOGGER" data-view_type="FILTERED_POSTMOD"></div>
                                
                                <script src="https://apis.google.com/js/plusone.js"></script>
                            
                            </div>

<?php else:

comments_template('', true);

endif; ?>