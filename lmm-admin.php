<?php
/**
 * Lmm code only usable in the WordPress admin

 * @package WordPress
 * @subpackage Lmm
 */

if ( ! defined( 'LMM_ADMIN_INIT' ) ) {
	exit;
}

add_action(
	'current_screen',
	function () {
		if (false === strpos(get_current_screen()->id, 'lmm')) {
			return;
		}

		// Enqueue admin styles.
		add_action(
			'admin_enqueue_scripts',
			function () {
				wp_enqueue_style(
					'lmm_admin',
					plugin_dir_url(__FILE__) . 'css/admin.css',
					array(),
					LMM_VERSION
				);
			}
		);
	}
);

// Add "settings" link to Lmm in the plugin list.
add_filter(
	'plugin_action_links',
	function ( $plugin_actions, $plugin_file ) {
		$new_actions = array();
		if ( basename( __DIR__ ) . '/lmm.php' === $plugin_file ) {
			/* translators: %s: url of plugin settings page */
			$new_actions['sc_settings'] = sprintf( __( '<a href="%s">Settings</a>', 'lmm-maintenance' ), esc_url( add_query_arg( array( 'page' => 'lmm-maintenance' ), admin_url( 'admin.php' ) ) ) );
		}
		return array_merge( $new_actions, $plugin_actions );
	},
	10,
	2
);

// Add lmm to the Admin sidemenu.
add_action(
	'admin_menu',
	function () {
		global $lmm;
		add_menu_page(
			'Labinator Minimal Maintenance Mode',
			'Maintenance',
			'publish_posts', // targeting Author role.
			'lmm-maintenance',
			function () {
				global $lmm;
				require LMM_BASE_PATH . 'inc/maintenance/class-lmm-maintenance-page.php';
			},
			'dashicons-hammer'
		);
	}
);

foreach ($lmm->modules as $lmm_module) {
	$lmm_module->admin();
}