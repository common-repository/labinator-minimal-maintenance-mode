<?php
/**
 * Shows the maintenance status in the WordPress admin bar.
 *
 * @package WordPress
 * @subpackage Lmm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$lmm_maintenance_settings = get_option( 'lmm_maintenance_settings' );
if ( $lmm_maintenance_settings ) {
	if ( ! empty( $lmm_maintenance_settings['site_status'] ) &&
		( 'online' !== $lmm_maintenance_settings['site_status'] ) ) {

		/**
		 * Admin bar code specific to the coming_soon status.
		 */
		function lmm_coming_soon_admin_bar() {
			global $wp_admin_bar;

			if ( current_user_can( 'manage_options' ) ) {
				$href = admin_url( 'admin.php?page=lmm-maintenance' );
			} else {
				$href = admin_url( 'index.php' );
			}

			// Adds the main admin menu item.
			$wp_admin_bar->add_menu(
				array(
					'id'     => 'lmm-maintenance-notice',
					'href'   => $href,
					'parent' => 'top-secondary',
					'title'  => __( 'Coming Soon', 'lmm' ),
					'meta'   => array( 'class' => 'lmm-coming-soon-active' ),
				)
			);
		}
		/**
		 * Admin bar code specific to the maintenance status.
		 */
		function lmm_maintenance_admin_bar() {
			global $wp_admin_bar;

			if ( current_user_can( 'manage_options' ) ) {
				$href = admin_url( 'admin.php?page=lmm-maintenance' );
			} else {
				$href = admin_url( 'index.php' );
			}

			// Adds the main admin menu item.
			$wp_admin_bar->add_menu(
				array(
					'id'     => 'lmm-maintenance-notice',
					'href'   => $href,
					'parent' => 'top-secondary',
					'title'  => __( 'Maintenance', 'lmm' ),
					'meta'   => array( 'class' => 'lmm-maintenance-active' ),
				)
			);
		}

		if ( 'maintenance' === $lmm_maintenance_settings['site_status'] ) {
			add_action( 'admin_bar_menu', 'lmm_maintenance_admin_bar', 1000 );
		}
		if ( 'coming_soon' === $lmm_maintenance_settings['site_status'] ) {
			add_action( 'admin_bar_menu', 'lmm_coming_soon_admin_bar', 1000 );
		}
	}
}
