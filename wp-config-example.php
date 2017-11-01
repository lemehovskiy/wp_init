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
define('DB_NAME', '');

/** MySQL database username */
define('DB_USER', '');

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

define('AUTH_KEY',         's0_5q:d_n#w3zVJ0j&X-m3fGV8er5W{_/>bX9:AE{*j2J6I[(@A)<!7WHWe9EEd7');
define('SECURE_AUTH_KEY',  'u.T_6xU``:S2%_yHO~(pgAcAZ1u2fNb-xU+PM-9EqPBbF;y Zg{{]qpf|HH+[(+/');
define('LOGGED_IN_KEY',    '*VbP `-]gV9o&HxKx:.++9{<]%.HR72v*pK`}ez&h5]9yY`;F%01=gbqt_&c!-qx');
define('NONCE_KEY',        'fH^ibww8]^eG9j83dN7&Vxo>Mf6fkRX]p;`|_+7XUOG^kZ^8A%.dz1mJ1MFJ-IZ{');
define('AUTH_SALT',        'K1|U&GIa9hyyu]d+q A5s5G~1U/cl(F|AN)n:f|XVHxOxsf><+:|0Shrr9-~c14v');
define('SECURE_AUTH_SALT', 'HI7oY#.g!w!DCQERD)~)$ w+kP}!3y|^+:{{Aqp+fLH=^gU{sh#F{(SRds,KaV6v');
define('LOGGED_IN_SALT',   '$8?p4phzQfATsy%R`AA%xG|wbXocp-n_m>tf8+<x|#?|MK^W]4MPaLGF>2]a>3+7');
define('NONCE_SALT',       'GVxvA`D}Lt_b&a&(p<:t_?9_k0Mj#-orE0G+ 8I36OVp>v$Y]Kg+4c5<!-}i1GY!');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp__b86b6_';

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
require_once(ABSPATH . 'wp-settings.php');
