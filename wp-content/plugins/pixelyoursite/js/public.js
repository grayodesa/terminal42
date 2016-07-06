jQuery(document).ready(function( $ ) {

    // AddToCart button
    $(".ajax_add_to_cart").click(function(e){

        var code = jQuery(this).attr('data-pixelcode') || false;
        console.log( code );

        if( code != false && code != '' ) {
            eval( code );
        }

    });

});