<?php
/**
 * Renders a link displayed to customers when they must first login
 * before being able to purchase tickets.
 *
 * @version 4.2
 */

$login_url = Tribe__Tickets__Tickets::get_login_url();
?>

<a href="<?php echo esc_attr( $login_url ); ?>"><?php esc_html_e( 'Login to purchase', 'event-tickets' ); ?></a>