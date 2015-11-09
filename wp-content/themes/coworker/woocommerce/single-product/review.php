<?php
/**
 * Review Comments Template
 *
 * Closing li is left out on purpose!
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post,$comment;
$rating = intval( get_comment_meta( $comment->comment_ID, 'rating', true ) );
?>
<li itemprop="reviews" itemscope itemtype="http://schema.org/Review" <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment_container">

		<div class="comment-meta">
        
            <div class="comment-author vcard">
            
                <span class="comment-avatar clearfix"><?php echo get_avatar( $comment, 40 ); ?></span>
            
            </div>
        
        </div>

        <div class="comment-content clearfix">

        	<?php if ( get_option('woocommerce_enable_review_rating') == 'yes' ) : ?>

			<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" class="star-rating" title="<?php echo sprintf(__( 'Rated %d out of 5', 'woocommerce' ), $rating) ?>">
				<span style="width:<?php echo ( intval( get_comment_meta( $GLOBALS['comment']->comment_ID, 'rating', true ) ) / 5 ) * 100; ?>%"><strong itemprop="ratingValue"><?php echo intval( get_comment_meta( $GLOBALS['comment']->comment_ID, 'rating', true ) ); ?></strong> <?php _e( 'out of 5', 'woocommerce' ); ?></span>
			</div>

			<?php endif; ?>
        
            <?php if ($comment->comment_approved == '0') : ?>
            <p class="comment-awaiting-moderation"><?php _e( 'This comment is awaiting moderation.', 'coworker' ); ?></p>
            <?php else: ?>    
            <div class="comment-author"><?php echo get_comment_author_link(); ?><span><?php if ( get_option('woocommerce_review_rating_verification_label') == 'yes' ) { if ( woocommerce_customer_bought_product( $GLOBALS['comment']->comment_author_email, $GLOBALS['comment']->user_id, $post->ID ) ) { echo '<em class="verified">' . __( 'Verified Owner', 'woocommerce' ) . '</em> &middot; '; } } ?><time itemprop="datePublished" datetime="<?php echo get_comment_date('c'); ?>"><?php echo get_comment_date(__( get_option('date_format'), 'woocommerce' )); ?></time></span></div>
            
        	<?php comment_text(); ?>
            
            <?php endif; ?>
        
        </div>
        
        <div class="clear"></div>

	</div>
