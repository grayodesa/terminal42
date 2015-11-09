<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.14
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $woocommerce, $product;

?>

<div class="col_half nobottommargin">

	<?php
		if ( has_post_thumbnail() ) {

			$image_title 		= esc_attr( get_the_title( get_post_thumbnail_id() ) );
			$image_link  		= wp_get_attachment_url( get_post_thumbnail_id() );
			$image       		= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
				'title' => $image_title
				) );

			$attachment_count = count( $product->get_gallery_attachment_ids() );

			if( $attachment_count > 0 ) { ?>

				<div class="fslider shop-product-slider" data-slideshow="false" data-animate="fade" data-speed="500" data-easing="easeOutQuad">

					<div class="flexslider">

						<div class="slider-wrap" data-lightbox="gallery">

							<div class="slide">

								<?php echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" title="%s" data-lightbox="gallery-item">%s</a>', $image_link, $image_title, $image ), $post->ID ); ?>

							</div>

							<?php

							$attachment_ids = $product->get_gallery_attachment_ids();
					
							if ( $attachment_ids ) {
					
								foreach ( $attachment_ids as $attachment_id ) {

									$image_link = wp_get_attachment_url( $attachment_id );
				
									if ( ! $image_link )
										continue;

									$image_title = esc_attr( get_the_title( $attachment_id ) );

									$image = wp_get_attachment_image_src( $attachment_id, 'shop_single' );

									$image = '<img src="' . $image[0] . '" width="' . $image[1] . '" height="' . $image[2] . '" alt="' . $image_title . '" />';

									echo '<div class="slide">';
						
									echo '<a href="' . $image_link . '" itemprop="image" title="' . $image_title . '" data-lightbox="gallery-item">' . $image . '</a>';

									echo '</div>';

								}
							
							}

							?>

						</div>

					</div>

				</div>

			<?php } else {

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a href="%s" itemprop="image" class="woocommerce-main-image zoom" title="%s">%s</a>', $image_link, $image_title, $image ), $post->ID );

			}

		} else {

			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="Placeholder" />', woocommerce_placeholder_img_src() ), $post->ID );

		}
	?>

</div>