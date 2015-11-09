<?php

if( get_post_meta( get_the_ID(), 'semi_port_type', TRUE ) == 'video' ):

    get_template_part( 'include/portfolio/single/video' );

elseif( get_post_meta( get_the_ID(), 'semi_port_type', TRUE ) == 'gallery' ):

    get_template_part( 'include/portfolio/single/gallery', 'full' );

else:

    get_template_part( 'include/portfolio/single/image', 'full' );

endif;

?>