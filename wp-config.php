<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

/** MySQL database username */

/** MySQL database password */

/** MySQL hostname */
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/var/www/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */

define('AUTH_KEY',         '[,tf!c1E?sx].d|(5$r-n]n*0kY|W6W>>&of*uDT|$T`y~E_,[<Ve[7ZUOq}-wtX');
define('SECURE_AUTH_KEY',  ';bl:4a.t]we[LwWH^4I`qG{ &*bbO-|UQ|H@p|]?i<:TWZ8o62PWnO|q_-J]`BAu');
define('LOGGED_IN_KEY',    '<gO#+?/N~~M%zj+$>9xT<uv%oqij@Q%D]z_rHaK)AO{{|j>p|$T@DYsR%)K?:W5U');
define('NONCE_KEY',        'Y,q;T$kz0uU+eC`R{u4}Gfp(-xV@Mqwmyuz[.|>^LL(gfo4|2j[Af^o+)E:/b{rH');
define('AUTH_SALT',        '{HIyovFOYQg7~3;B$O}kp zcwCU7KH$t#+]PD,oPjIxg4_u0iO)0;T2@>+.$iEP4');
define('SECURE_AUTH_SALT', '(h7|;kwy}QHl>qAH]Q-QVG0!rLW],UojbOsn3,W.xyS;3,DWt!I+4d9z#hOm}2ov');
define('LOGGED_IN_SALT',   '[IV[T`@YZ6 $cVe`P1Dl7D~ZM~4Qhta2I&^|}h7.W-7Y03pjV^#}j+(Iuo?,VBg4');
define('NONCE_SALT',       '~6ktKKH-p&&_HC09j{zXvA0WHS6i55G*y+T!1:~HmN~x7dR3_/u/~H}+-R=_mC6J');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
define('FS_METHOD', 'direct');
define('DB_NAME', 'wordpress');
define('DB_USER', 'wordpress');
define('DB_PASSWORD', 'iYFGOf3RzH');
require_once(ABSPATH . 'wp-settings.php');
define ('WPLANG', 'ru_RU');
define('FORCE_SSL_ADMIN', true);