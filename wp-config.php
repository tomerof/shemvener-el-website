<?php
// BEGIN A2 CRON DISABLE
define('DISABLE_WP_CRON', true);
// END A2 CRON DISABLE

$isNotLocalDevEnvironment = strpos($_SERVER['HTTP_HOST'], 'localhost') === false && strpos($_SERVER['HTTP_HOST'], 'host.docker.internal') === false;

if (isset($_SERVER['HTTP_HOST']) && $isNotLocalDevEnvironment) {
    $_SERVER['HTTPS'] = 'on';
}

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'kerenell_nvsh25' );

/** Database username */
define( 'DB_USER', getenv('WORDPRESS_DB_USER') ?: 'kerenell_nvsh25' );

/** Database password */
define( 'DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'p6@v[26S65' );

/** Database hostname */
define( 'DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'localhost' );

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
define( 'AUTH_KEY',         'en1cvy2ud6nzykkryutuvghe9z6le3r6lyohnflnn5lnycfhn3vrf8qgk2pid186' );
define( 'SECURE_AUTH_KEY',  'cgpxtnidcqq19edztb3qhtqcx2y0tw9osv2hsjbulhzxlcbrmb7rommonq078ogo' );
define( 'LOGGED_IN_KEY',    'rm7ixrc7jj84rz4vi2r4ejhmceap8o3fuxphxqfn3pmzvjqfvg0g5wjspodnaedd' );
define( 'NONCE_KEY',        '3wjnbflumylbc8o9wwxbzdflpn3j3ajbedkq3pyk1q5viu304ghm46mryvxccqe4' );
define( 'AUTH_SALT',        '5npe6ybzpbr82atjgnsewmgnitxtkvvz0voyhwhlua3fipizxtcyghpqtenxirpp' );
define( 'SECURE_AUTH_SALT', 'xlmygmnt5hwc7nqctnri5zw4hnfu9qjzs6fgj6ocwlju2noccsmyd9rblsrogyoe' );
define( 'LOGGED_IN_SALT',   '1fzm7ykcygong2biuvblie9obuz5mz9f2pdnufiaip9xr047vphsbifcm0tb70qg' );
define( 'NONCE_SALT',       'sykmebsjozwa2l6fmbozimigzjsudenvi7hjjl2uznldljsefywt3wayifd0xbwm' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'nvsh25_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
//define( 'FS_METHOD', 'direct');
define( 'DISALLOW_FILE_EDIT', true );


/* Add any custom values between this line and the "stop editing" line. */



define( 'WP_MEMORY_LIMIT', '1024M' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
