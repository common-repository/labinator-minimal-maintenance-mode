<?php
/**
 * Uninstall script
 *
 * This file contains all the logic required to uninstall the plugin
 *
 * @package WordPress
 * @subpackage Lmm
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

delete_option('lmm_maintenance_settings');