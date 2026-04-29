<?php
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
define( 'DB_NAME', 'alawaresort' );

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
define( 'AUTH_KEY',         'r>(MU!m@^b^eUi.th]){3]{7`cMm2HF:-YZ1&~!fjBn+GH|1]h`h3>a`Bt2Alt]c' );
define( 'SECURE_AUTH_KEY',  '2!??`2L!^}@5JLyjFlUA Aq=81;I,(LYRj_(le_b|NO{Jv`P*k50I2@e.[{>A+S6' );
define( 'LOGGED_IN_KEY',    'rPV/z[J{Et^-:Tz`pr1wpnQXt%HH1<,fZ+F+ J2PZ=M$*3p.^.X20]c8J6KidXfV' );
define( 'NONCE_KEY',        'N[Dwzf-.q1bRlcMYD9*-MI>Et6^+0$[s)B<QK8BP?ZZQ_A)uO%s5^c&Y}{VTT_hp' );
define( 'AUTH_SALT',        'eL^%lp_`XC^I]ut yyl]}8?~RkP@l4iEEiC3{.1ji!Fn$YRKr`Y!@LxBpHk$KDZT' );
define( 'SECURE_AUTH_SALT', 'WEUaM@_;C`D_p,YaZChJ,A-j^0gE:NvG0O)/h;b/yN@>O)ifs<j$7m//)0!/lMkC' );
define( 'LOGGED_IN_SALT',   'E`W0!f@]=:(|Tib?8EPT>7feM)j [)kHWKNztBUE~GeDKhX;a-(+]p`er}h0KR#3' );
define( 'NONCE_SALT',       '~*Bqzk=Pn@_ ADFwHMGCs@?tDgn_#}W?oz,26[EcEz.@ajUKxQc,B>m5R*pnk/E{' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
set_time_limit( 300 );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
