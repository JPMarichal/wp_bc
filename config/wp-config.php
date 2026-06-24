<?php
define('DB_NAME',     getenv('WORDPRESS_DB_NAME') ?: 'bc_wp');
define('DB_USER',     getenv('WORDPRESS_DB_USER') ?: 'wpuser');
define('DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: 'wppass');
define('DB_HOST',     getenv('WORDPRESS_DB_HOST') ?: 'db');
define('DB_CHARSET',  getenv('WORDPRESS_DB_CHARSET') ?: 'utf8mb4');
define('DB_COLLATE',  getenv('WORDPRESS_DB_COLLATE') ?: 'utf8mb4_unicode_ci');

define('AUTH_KEY',         'x{]XVKA1<@H`0!dV+GBKv}!3[2X22xQ`}8P0de1OMebxKgA419;Cq-RTeD$XqK$u');
define('SECURE_AUTH_KEY',  '/Y,=+71or]2!!F`;||ITVB?+ gJk[O_G^X%a|<pXVD!q$}4Ur@b^5AiSN~$YbN0P');
define('LOGGED_IN_KEY',    'M+&a}_([H6D|0q|(DJ?]=qOwGl2#)U dt,`D$K7+Ap5;u~+c3;_bZ&HbEln_G6*1');
define('NONCE_KEY',        'i]~,|EDDQ9/g5m~P_@2^X+VE7U%/e( HJ_ tlH7ea@<8u.?GERD|CX7Tu(UDyc?t');
define('AUTH_SALT',        'T8CzCcT/e+{8.Kn_Xic53S0IMmz;[2hj(PA)NxNf+&C@)>?oyenhYsqlh,HiK*fY');
define('SECURE_AUTH_SALT', 'E>6!@r?%.uZ{TFrg_84+`]2<#H&j-5J*0~P-w-H?b#7E5wM}O!{r;[eKzPL#|Fn`');
define('LOGGED_IN_SALT',   'jemX-vn~ridz8>q7E^!P8N%7S3Y!:i_SOp -P9i{2-pOF&UxnYa_Wh+fdL)?/[d@');
define('NONCE_SALT',       ':uA|m<]@4Eci1h1%4]LdTxoK0 ?+EK/9M4R[8/LZs4ws<6aUCYl(&4eHEq|CeFb3');

$table_prefix = getenv('WORDPRESS_TABLE_PREFIX') ?: 'wp_';

define('WP_HOME',    getenv('WP_HOME') ?: 'http://localhost:8080');
define('WP_SITEURL', getenv('WP_SITEURL') ?: 'http://localhost:8080');

define('WP_DEBUG',         true);
define('WP_DEBUG_LOG',     true);
define('WP_DEBUG_DISPLAY', false);
define('WP_POST_REVISIONS', 3);
define('FORCE_SSL_ADMIN',   false);
define('FS_METHOD',         'direct');
define('WP_AUTO_UPDATE_CORE', false);
define('DISALLOW_FILE_EDIT', false);
define('WP_CACHE', true);

if (!defined('ABSPATH')) define('ABSPATH', __DIR__ . '/');
require_once ABSPATH . 'wp-settings.php';
