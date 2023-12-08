<?php
define( 'WP_CACHE', true );
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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'safety_gear_db' );

/** Database username */
define( 'DB_USER', 'user' );

/** Database password */
define( 'DB_PASSWORD', 'user' );

/** Database hostname */
define( 'DB_HOST', 'mysql' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'x9g5w7IJ8PjJynCZ-0DmLE_Z!] Ntu_B_Y{N? N.N=8bC_D9429vG~P/~nInMttF' );
define( 'SECURE_AUTH_KEY',   'ug| 7JX!I@}3j4gR-;3q[90C>%.gVBV(DE-2?E5`dW3d`nQ<oKP5~%CZIp>U:[mG' );
define( 'LOGGED_IN_KEY',     'CJm$47v92Qf?-knU`<: -j:!Rrj}(%LN]{hE53Moav6!S|?vV:]xulnx[J0uqRLV' );
define( 'NONCE_KEY',         '9XIRk<#-tYB cy/];d.[6UYG+OaV8ra((dg10zN_tGYl` )[XWE0gb9,k?677b4l' );
define( 'AUTH_SALT',         'WuPn 4gBE~:ecEaNQISsfo)1?@u!;Lg<ms1433;0O&fE~V~Z:nIsDvINrVJ3vITo' );
define( 'SECURE_AUTH_SALT',  'WzsWBG)|q/I2~[aMMA[VSz1Rlup8t|8IJ`KE/[tOFCGD?.R|i;takkU1=!%R7{7S' );
define( 'LOGGED_IN_SALT',    ')7ct`aA)75YIi]Y@|kpf3;|sVaqIpL!xy`8~LU&fp+>@r:5SOM0{v*mXTo:TUN_-' );
define( 'NONCE_SALT',        '$osQf;8Ft-LP;Ay-d;omd6l59:PZDQ*600##:2R^2O3;^TPODQ~=?-R-}.AkwPp,' );
define( 'WP_CACHE_KEY_SALT', 'y]H#A0}=(`56#eiYv`It7#Mg2l45D3t/(*:`lk]!gVHB;QO3o+;/Q(yRZi82<Y=.' );


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



define( 'FS_METHOD', 'direct' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
