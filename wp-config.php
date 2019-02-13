<?php
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
define('DB_NAME', 'scent');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '|2,eTA@D59WuSD86}6m<^1!kKy&&Z1mM9Zqp Wsd~T||Gu21s]^$evg9$gh*O-|-');
define('SECURE_AUTH_KEY',  'O m0rD1rH$ofPi|jH@I,*|~qUDWJUiS,F/O6i}=4!-<FKIJz|,z&B`n1IygluQ88');
define('LOGGED_IN_KEY',    '0kb0kJMs)@%1U.qE[+,vh&}; J>?:)ByN%W!*42*,O^`bFE%|Q.q8ORT[v(M]ejw');
define('NONCE_KEY',        'aHw&mCEGjCa|%PAX@@b(]8.pg3YRjqj$N1bO&H&$.^{v?KQ0z3XwMFYFt<}%tSq ');
define('AUTH_SALT',        '=I$RM@CB_LIgxqR_D,Jh[~]_7n#fE5R++G(Pu;(eB*5ivoQ?aiY6%6A{B8|7&F !');
define('SECURE_AUTH_SALT', 'n<A[%}Vc7:B J0kbrF&,btvZWQZlacV1]PM/Gvdu~%1}Y%BenTwO+45:GN]v;Y6m');
define('LOGGED_IN_SALT',   '+O!Uipx9Yo2MghSprD7P9DeIerr+c{j~L4Bkzudc[^wdj?aCu=^1:bslhjrju$ka');
define('NONCE_SALT',       'JY86`i/u>|Ew}P9!HqGwSXW}}Wl.SqU8:EoM=(LuX11!36eD?Y6-hj,1_{7KE ++');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('WP_MEMORY_LIMIT', '512M');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
