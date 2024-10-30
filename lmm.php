<?php
/**
 * Plugin Name: Labinator Minimal Maintenance Mode
 * Plugin URI: https://labinator.com/wordpress-marketplace/plugins/maintenance-mode/
 * Description: Activates a maintenance mode or coming soon page for your website.
 * Version: 1.0.9
 * Requires at least: 6.6
 * Requires PHP: 8.1
 * Author: Labinator
 * Author URI: https://labinator.com/
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: labinator-minimal-maintenance-mode
 * Domain Path: /languages
 * @package WordPress
 * @subpackage Lmm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LMM_VERSION', '1.0.9' );

$lmm_get_upload_dir = wp_upload_dir();
define( 'LMM_BASE_PATH', plugin_dir_path( __FILE__ ) );
define( 'LMM_RELATIVE_BASE_PATH', substr( LMM_BASE_PATH, strlen( ABSPATH ) - 1 ) );
define( 'LMM_BASE_URL', plugin_dir_url( __FILE__ ) );

define( 'LMM_DATA_PATH', $lmm_get_upload_dir['basedir'] . '/lmm/' );
define( 'LMM_RELATIVE_DATA_PATH', substr( LMM_DATA_PATH, strlen( ABSPATH ) - 1 ) );
define( 'LMM_DATA_URL', $lmm_get_upload_dir['baseurl'] . '/lmm/' );

register_activation_hook(
	__FILE__,
	function () {
		add_option( 'lmm_activation_welcome', 'pending' );
	}
);

// Include main Lmm classes.
require LMM_BASE_PATH . 'inc/class-lmm.php';
require LMM_BASE_PATH . 'inc/class-lmm-module.php';

// Include Lmm modules.
$lmm = new LMM();
require LMM_BASE_PATH . 'inc/maintenance/class-lmm-maintenance-module.php';

// Main init.
add_action(
	'init',
	function () {
		global $lmm;

		load_plugin_textdomain( 'lmm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			define( 'LMM_CLI_INIT', true );
		} elseif ( ! is_admin() ) {
			define( 'LMM_FRONT_INIT', true );
			require_once 'lmm-frontend.php';
		} else {
			define( 'LMM_ADMIN_INIT', true );
			require_once 'lmm-admin.php';
		}
	}
);
