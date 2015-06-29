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
define('DB_NAME', 'wordpress');

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
define('AUTH_KEY',         'e,Io5t774yd do`2Rxc{_E-V+ ENY>}v7R(#l`Q,f9o2%JE|Z8{q1]hqYF+=w_d1');
define('SECURE_AUTH_KEY',  't%{@Xk&XC2i7R9/fK37I1X<Z~X,Zl4|!hqryejb)IF6X--jJ]sLX>VumxHz-fmZ?');
define('LOGGED_IN_KEY',    '``p0m]Oc*+@6Y+?LHeuqn.C-L*<av;W]/]$|XsVJ0~rW)SSE^*|= YlMH 9Uze<[');
define('NONCE_KEY',        'JP=RVg-6|VESfrcw:Ftl28WaCQl.t)Xev-|frmAf;lt@,5QRp?GeHS/~6MA5Sc7K');
define('AUTH_SALT',        'W.H={{$+y|ll?5)ea-)O,|eS-tVX.bCkRXBDRu2{TTZ~hp/>I=ieh mn7|/SB!-<');
define('SECURE_AUTH_SALT', 'z!$kDVNdEX^s<9=Dh;Vh5zg:q,xa/=2Q_?dzx~cq!,a3324xa7~*nuC%5W+ZEN2|');
define('LOGGED_IN_SALT',   '<Mf4${2shrFjL.f6ml40B8D(QQgkD0Bb4>H?H=jx[rfr[?!#$1&ok^OgbdY6R[~%');
define('NONCE_SALT',       '}j8Vau1[CZ,m|+M=,(sdMwe;{d%DbJ|oY.}2fMJMeyKx<+3:]IeP-?|K(aq6b+Ri');

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
require_once(ABSPATH . 'wp-settings.php');
