<?php $format = get_post_format(); ?>

<div class="entry_image"<?php if( 'audio' == $format ) echo ' style="overflow: visible;"'; ?>>
                                
                                    <?php

                                    if( 'audio' == $format ) {

                                    	if( get_native_audio_file( 'semi_post_audio_mp3' ) != '' OR get_native_audio_file( 'semi_post_audio_ogg' ) != '' OR get_native_audio_file( 'semi_post_audio_wav' ) != '' ) { ?>

								<audio preload="auto" controls>
		                                        <?php if( get_native_audio_file( 'semi_post_audio_mp3' ) != '' ) { echo '<source src="' . get_native_audio_file( 'semi_post_audio_mp3' ) . '">'; } ?>
		                                        <?php if( get_native_audio_file( 'semi_post_audio_ogg' ) != '' ) { echo '<source src="' . get_native_audio_file( 'semi_post_audio_ogg' ) . '">'; } ?>
		                                        <?php if( get_native_audio_file( 'semi_post_audio_wav' ) != '' ) { echo '<source src="' . get_native_audio_file( 'semi_post_audio_wav' ) . '">'; } ?>
		                                    </audio>

                                    	<?php } else {

                                    		echo stripslashes( htmlspecialchars_decode( get_post_meta( get_the_ID(), 'semi_post_embed', TRUE ) ) );

                                    	}

                                    } else {

                                    	echo stripslashes( htmlspecialchars_decode( get_post_meta( get_the_ID(), 'semi_post_embed', TRUE ) ) );

                                    }

                                    ?>
                                
                                </div>