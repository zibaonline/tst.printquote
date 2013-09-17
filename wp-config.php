<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'printquotedb');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
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
define('AUTH_KEY',         'oU%Kosg7ew|+TuxjFF@u7cYC<oIo/Ux`#Ge(LC7MGM*9lqh>0i<USA~9h5NK<P#,');
define('SECURE_AUTH_KEY',  'CELWSYTg#`&tP*^}4?(~5,:<9k0&#&R*reJxC]<-b.EB4cWM|0&2W?B*T-QV%@d9');
define('LOGGED_IN_KEY',    'Xoq}Qt qYlt98obpcR0)MMXN[cJT#brz}:_($wK Qap| 1.5o+vRuKTmA,h. R?W');
define('NONCE_KEY',        'C(`Frbr|Lb6`x;bLfYenXfgCHy&&]pQO5dMH-?a$}o4sM(7iEn1&FS3ufqMXM&Kj');
define('AUTH_SALT',        'q&tWbCc3xTd&XHu>`9Itgr*n;B(FAiHD]vd@E#AW*cy9heY@<pAgZE{ pVrKV_Rt');
define('SECURE_AUTH_SALT', 'a)z?:2|ts]6$2b[,ZpH0D*JGDXM>D81c~K:QiGQ>$u:!O{XTe(1l<AW/3bMH6 #A');
define('LOGGED_IN_SALT',   'u+9g!I^J3W<c`0~wQ]#o1R}7dCuhg=G-iJdF3E #%V)^wHYXD5@8W5@=JeC2++)T');
define('NONCE_SALT',       'Q9,`9a:[#3G(6W4rKU+v*a)$E0|i%U(ckPF%qv]wOGL5=i<sZ*+<BXw:A>a4KGS?');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

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
require_once(ABSPATH . 'wp-settings.php');
