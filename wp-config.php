<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'OLUdn8=$7vun^MxV(i>M:zTT|Uzs49^MF*$$PW2B$uXcb?8;RQP#9y-]fQmlB:!H' );
define( 'SECURE_AUTH_KEY',  'lp(VDk(e)PL5U|+;t5;,&q;&lFV~ZV1]Gtf11GN-?o6J4c*~.rwh@HbM7%Is=A`T' );
define( 'LOGGED_IN_KEY',    'XK+#cZL[_?8ZrGew3A^7bqU{rz|,]btCys()Tanl>F@:zt[}N)^1]^Ug4=I7iR.8' );
define( 'NONCE_KEY',        'Q3ljO=+JGr[QY5t9GSnbwlxV&/NSX#IS4A.2W/T]{92(k;jt*n}S6UtI.W9~IHf@' );
define( 'AUTH_SALT',        ')+-5l1NP-|RYNow{>oRPKg<sP}~(f 19Usl1je7ZHL6)?%o38/#|Uz|u7yA x50b' );
define( 'SECURE_AUTH_SALT', 'HYW+|]l&&fYQer7FmreC7-bbi)Euh?~,|^e6Ej-:@wW]IE~PZc/i}m!^b_N.mOt}' );
define( 'LOGGED_IN_SALT',   'j-ub!4la-frHDR>8<2 en[~k_A{h1$N sb-@th(v|CTEC1HxQ3O`?n~G2;mZ9p3l' );
define( 'NONCE_SALT',       'n$Rn_,b|MZH]Xm~(0B.<V$RRr)C*cd[2:3q0F_-n8lB|plAaD$4+AUo>Jn=}lFU.' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
