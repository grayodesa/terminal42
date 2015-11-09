<div id="comments" class="clearfix">
    <div id="comment_icon"></div>
	<?php if ( post_password_required() ) : ?>
		<p class="nocomments nomargin"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'coworker' ); ?></p>
	</div><!-- #comments -->
	<?php
        return;
		endif;
	?>

	<?php if ( have_comments() ) : ?>
		<h3 id="comments-title"><?php comments_number(__('No <span>Comments</span>', 'coworker'), __('1 <span>Comment</span>', 'coworker'), __('% <span>Comments</span>', 'coworker')); ?></h3>

		<ol class="commentlist clearfix">
			<?php wp_list_comments( array( 'callback' => 'coworker_comment' ) ); ?>
		</ol>
        
        <div class="clear"></div>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<ul class="pager">
			<li class="previous"><?php previous_comments_link( __( '&larr; Older Comments', 'coworker' ) ); ?></li>
			<li class="next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'coworker' ) ); ?></li>
		</ul>
		<?php endif; // check for comment navigation ?>
        
        <div class="doubleline"></div>

	<?php
		elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments nomargin"><?php _e( 'Comments are closed.', 'coworker' ); ?></p>
	<?php endif; ?>

	<?php
    
    $commenter = wp_get_current_commenter();
    $req = get_option( 'require_name_email' );
    $aria_req = ( $req ? " aria-required='true'" : '' );
    
    $commentfields =  array(
	'author' => '<div class="col_one_third"><label for="author">' . __( 'Name', 'coworker' ) . ( $req ? ' <span>*</span>' : '' ) . '</label>' .
                '<input id="author" name="author" class="input-block-level" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></div>',
	'email'  => '<div class="col_one_third"><label for="email">' . __( 'Email', 'coworker' ) . ( $req ? ' <span>*</span>' . __(' <small>(will not be published)</small>', 'coworker') : '' ) . '</label>',
                '<input id="email" name="email" class="input-block-level" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></div>',
	'url'    => '<div class="col_one_third col_last"><label for="url">' . __( 'Website', 'coworker' ) . '</label>' .
                '<input id="url" name="url" class="input-block-level" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></div>'
    );
    
    $comment_args = array(
        'fields' => apply_filters( 'comment_form_default_fields', $commentfields ),
        'comment_field' => '<div class="col_full comment-form-comment"><textarea name="comment" class="input-block-level" id="comment" cols="58" rows="10" tabindex="4" aria-required="true"></textarea></div>',
        'comment_notes_before' => '',
        'title_reply' => __( 'Leave a <span>Comment</span>', 'coworker' ),
        'label_submit' => __( 'Submit Comment', 'coworker' ),
        'logged_in_as' => '<div class="col_full"><div class="alert alert-info logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'coworker' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</div></div>'
    );
    
    comment_form( $comment_args ); ?>
    
    <script type="text/javascript"> jQuery(document).ready( function($) { $('.form-submit input').addClass('btn').css('font-family','sans-serif'); }); </script>
    
</div>