<?php
/**
 * Lmm code only usable in the WordPress front-end

 * @package WordPress
 * @subpackage Lmm
 */

if (!defined('ABSPATH')) {
	exit;
}

foreach ($lmm->modules as $lmm_module) {
	$lmm_module->frontend();
}