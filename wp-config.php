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
define('WP_CACHE', true);
define( 'WPCACHEHOME', 'C:\xampp\htdocs\thenovita\wp-content\plugins\wp-super-cache/' );
define('DB_NAME', 'thenovita');

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
define('AUTH_KEY',         '$!-:dT52q7!!U ij0>l6Ij2z7dEt6bYPfZm04YtO/%B$-K210PRvO][o _bh2j!_');
define('SECURE_AUTH_KEY',  'G*{i0:ddf(Wr.vxtr~hU*eF((XW}Ar1^vsEU#7Bzb=:wQ^K8DgHN4AB|FRvLIr,2');
define('LOGGED_IN_KEY',    '1qRMUKAoBDa<O5E`_c`d 95T2t79X=$?A3&u`?bxU%c=t3K:/htNjQ(| j6HSz]M');
define('NONCE_KEY',        'BHXN@-eB9nQt`F(YV.[Tm|~q{N~!W}yo`^hD@2PG-3; ) [I1OgT]JBQM,</fL]q');
define('AUTH_SALT',        'rIy&sJq~nfoU*`pez=zNYF(X%e8RV+YR{8_zBWzxx1w!./R$|]EeQF6%kh]f^WhI');
define('SECURE_AUTH_SALT', 'B-)Jb<VHr<8m&9,,o*NZ|3f@nOdQZWr%B161a8CS%XJJn(lmG`7 aoF$Q7D(:#|a');
define('LOGGED_IN_SALT',   'siuS:=6R<WKW?kqB%}HWa)b[Gt-WF4y*}6W6+aEBjb^3Nmy8dG]C>bf-EF?gB}J|');
define('NONCE_SALT',       'm!fK@5:f6:KmA{,$?payVwR,bl)95*g~K,p.QS]q|c.jqch_cJN?;60xe>EKfCNR');

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
define( 'WP_MEMORY_LIMIT', '64M' );

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
