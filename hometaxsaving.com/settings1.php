<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'hometaxsavings_wdb');

/** MySQL database username */
define('DB_USER', 'hometaxsavings');

/** MySQL database password */
define('DB_PASSWORD', 'Hometaxsavings@2018!');

/** MySQL hostname */
define('DB_HOST', '173.236.130.244');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'P,kViBVF$u#/]$ `gL9R+(BqL7|Fs4*ScR|r3b]AE dTa!yI/3%/]#]gekuLTME+');
define('SECURE_AUTH_KEY',  '][1ecZ=WKq+-FqL<waxbERtdc.P!n@IC,GBr!`gun}!Cz,x8}>vDaF49%|q1&7Qd');
define('LOGGED_IN_KEY',    '9F>wsTg}v=Wnbmx;B5OAjwII&dK$TL?}HO.b> J%:aC{YZk9@+&H(eYxBH V<#]O');
define('NONCE_KEY',        'oQA~.Mm3Bs=h23a9$k/17i)>z?u2x;2iRL~ypzF1M5(e(AeO3%7~FIUY{oMmlNFD');
define('AUTH_SALT',        'q.,E7}~}Lcd?6@.|@j>nMR7n%H?P(zIjDfm[,^dTIp:G9?%q_P<x5omI6l}x+%Hi');
define('SECURE_AUTH_SALT', 'ZqOI+vB!gjVe(?(u:o4#&]it2?YRElG%>4!+$a3,9}Ro}:`ys>a{Ddz<EU?7CSH9');
define('LOGGED_IN_SALT',   '@5L:1,I`X%-GEb%r=VXnMi+KHL1$@S2dVq#dOy7 `1^^fQHo~C0Z41#<n>#bDrr<');
define('NONCE_SALT',       's][tS94 ;tPPMs-u1!v+F:$pWpTs^+dT[uq(&i1le4a6X&bo9DV<!>pbnI:Iroon');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpch_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
 require_once(ABSPATH . 'settings2.php');
