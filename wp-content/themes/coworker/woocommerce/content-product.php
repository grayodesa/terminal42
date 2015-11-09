<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

?>

<div <?php post_class(); ?>>
                        
    <div class="product-image">
    
        <?php
        
        if ( has_post_thumbnail() ) {
            $image_html = wp_get_attachment_image( get_post_thumbnail_id(), 'shop_catalog' );                   
        }

        $attachment_ids = $product->get_gallery_attachment_ids();
        
        $img_count = 0;
        
        if ($attachment_ids) {
            
            echo '<a data-order="1" href="' . get_permalink() . '">' . $image_html . '</a>';    
            
            foreach ( $attachment_ids as $attachment_id ) {
                
                if ( get_post_meta( $attachment_id, '_woocommerce_exclude_image', true ) )
                    continue;
                
                echo '<a data-order="2" href="' . get_permalink() . '">' . wp_get_attachment_image( $attachment_id, 'shop_catalog' ) . '</a>';  
                
                $img_count++;
                
                if ($img_count == 1) break;
    
            }
                        
        } else {
        
            echo '<a href="' . get_permalink() . '">'.$image_html.'</a>';
            
        }

        ?>
        
        <div class="product-overlay">
        
            <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
        
        </div>

        <?php if( $product->is_on_sale() ): ?>

        	<div class="product-sale"><?php _e( 'Sale', 'coworker' ); ?></div>

        <?php endif; ?>
    
    </div>
    
    <div class="product-title">
    
        <h3 title="<?php the_title_attribute(); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        
        <div class="product-cats">
            <?php
                $size = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
                echo $product->get_categories( ', ', '<span>' . _n( '', '', $size, 'woocommerce' ) . ' ', '</span>' );
            ?>
        </div>
        
        <div class="product-price"><?php echo $product->get_price_html(); ?></div>
    
    </div>

</div>