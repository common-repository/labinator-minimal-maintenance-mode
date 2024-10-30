<?php
/**
 * Lmm Maintenance Module class
 *
 * @package WordPress
 * @subpackage Lmm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Lmm Maintenance Module class
 */
class LMM_MAINTENANCE_MODULE extends LMM_MODULE {

	public $preview_base_url;
	public $preview_url;
	public $magic_base_url;
	public $magic_url;

	/**
	 * Module constructor, init method overrides parent module default params
	 */
	public function __construct() {
		$this->init(
			array(
				'slug'        => 'maintenance',
				'title'       => __( 'Maintenance Mode', 'lmm' ),
				'full_title'  => __( 'Maintenance Mode', 'lmm' ),
				'description' => __( 'Customizable maintenance page to close your site during updates or development. It has a "magic link" to grant temporary access.', 'lmm' ),
				'role'        => 'publish_posts', // targeting Author role.
			)
		);
		$this->default_settings = array(
			'page_id'     => '',
			'site_status' => 'online',
			'token'       => strtoupper( substr( MD5( rand() ), 0, 12 ) ), // phpcs:ignore
		);
	}
	/**
	 * Executes code related to the front-end.
	 * Adds a maintenance status button to the admin bar
	 */
	public function frontend() {
		$this->read_settings();
		if ( count( $this->settings ) > 0 ) {
			if ( is_admin_bar_showing() ) {
				require_once $this->path . 'admin-bar.php';
			}
			require $this->path . 'class-lmm-maintenance-page.php';
			$lmm_maintenance = new LMM_MAINTENANCE_PAGE( $this->settings, $this->path );
		}
	}
	/**
	 * Executes code related to the WordPress admin.
	 * Adds a maintenance status button to the admin bar
	 */
	public function admin() {

		$this->read_settings();
		// The maintenance token should be saved as soon as possible.
		// To keep it from changing on every page load.
		if ( ! get_option( 'lmm_' . $this->params['slug'] . '_settings' ) ) {
			// default option values saved WITHOUT autoload.
			update_option( 'lmm_' . $this->params['slug'] . '_settings', $this->default_settings, 'no' );
		}

		if ( null !== filter_input( INPUT_POST, 'lmm-maintenance-saved' ) ) {
			check_admin_referer( 'lmm_save_maintenance' );
			$this->save_settings(
				filter_input_array(
					INPUT_POST,
					array(
						'page_id'     => FILTER_VALIDATE_INT,
						'site_status' => FILTER_DEFAULT,
						'token'       => FILTER_DEFAULT,
					)
				)
			);
		}

		$this->preview_base_url = home_url( '/?mct_preview=' . wp_create_nonce( 'maintenance_preview_nonce' ) );

		if ( $this->settings['page_id'] ) {
			$this->preview_url = $this->preview_base_url . '&mct_page_id=' . $this->settings['page_id'];
		} else {
			$this->preview_url = $this->preview_base_url;
		}

		$this->magic_base_url = home_url( '/?mct_token=' );
		$this->magic_url      = home_url( '/?mct_token=' . $this->settings['token'] );

		add_action( 'admin_menu', array( $this, 'register_sub_menu' ) );

		if ( is_admin_bar_showing() ) {
			require_once $this->path . 'admin-bar.php';
		}
	}

	/**
	 * Saves options to database
	 *
	 * @param array $options options array, normally $_POST.
	 * @param bool  $silent  prevent the function from generating admin notices.
	 */
	protected function save_settings( $options = array(), $silent = false ) {

		$settings = $this->read_settings();

		if ( in_array( $options['site_status'], array( 'online', 'coming_soon', 'maintenance' ), true ) ) {
			$settings['site_status'] = $options ['site_status'];
		}

		if ( null !== $options['token'] ) {
			$settings['token'] = sanitize_text_field( $options['token'] );
		}
		if ( false === $options['page_id'] ) {
			// Selected "use default content".
			$settings['page_id'] = '';
		} elseif ( null !== $options['page_id'] ) {
			$settings['page_id'] = (int) sanitize_text_field( $options['page_id'] );
			if ( empty( $options['page_id'] ) ) {
				if ( ! $silent ) {
					$this->notice( __( 'Content page id is not a valid page id', 'lmm' ), 'warning' );
				}
				return false;
			}
		}

		if ( $this->is_equal_array( $this->settings, $settings ) ) {
			if ( ! $silent ) {
				$this->save_no_changes_notice();
			}
			return true;
		}

		// Option saved WITH autoload.
		if ( update_option( 'lmm_maintenance_settings', $settings, 'yes' ) ) {
			$this->settings = $settings;
			if ( ! $silent ) {
				$this->save_success_notice();
			}
			return true;
		} else {
			if ( ! $silent ) {
				$this->save_error_notice();
			}
			return false;
		}
	}
}
$lmm->modules['maintenance'] = new LMM_MAINTENANCE_MODULE();
