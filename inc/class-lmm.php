<?php
/**
 * Lmm main class, used as controller of all the modules

 * @package WordPress
 * @subpackage Lmm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Lmm main class
 */
class LMM {
	/**
	 * Container for module instances
	 *
	 * @var Array $modules
	 **/
	public $modules = array();

	/**
	 * Container for admin notices
	 *
	 * @var Array $modules
	 **/
	public $notices = array();

	/**
	 * Placeholder for the old tabs navigation.
	 *
	 * @param string $current Current (active) tab slug.
	 */
	public function admin_tabs( $current = '' ) {
		echo '<div class="lmm-wrap-divider" id="' . esc_attr( $current . '-divider' ) . '"></div>';
	}
	/**
	 * Displays navigation tabs on Lmm tabs (deactivated)
	 *
	 * @param string $current Current (active) tab slug.
	 */
	public function admin_tabs_old( $current = '' ) {

		$is_admin = current_user_can( 'manage_options' ) ? true : false;

		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->modules as $module ) {

			$params = $module->params;

			if ( ! $is_admin && ( 'manage_options' === $params['role'] ) ) {
				continue;
			}
			if ( ! $params['is_active'] ) {
				continue;
			}
			if ( ! $params['has_config'] ) {
				continue;
			}
			$slug = 'lmm-' . $params['slug'];

			$allowed_title_tags = array(
				'span' => array(
					'style' => array(),
				),
			);

			if ( $slug === $current ) {
				echo '<a href="#" class="nav-tab-active nav-tab ' . esc_attr( $slug ) . '-tab">' . wp_kses( $params['title'], $allowed_title_tags ) . '</a>';
			} else {
				$tab_link = add_query_arg( array( 'page' => $slug ), admin_url( 'admin.php' ) );
				echo '<a href="' . esc_url( $tab_link ) . '" class="nav-tab ' . esc_attr( $slug ) . '-tab">' . wp_kses( $params['title'], $allowed_title_tags ) . '</a>';
			}
		}
		echo '</h2>';
	}

	/* Dashboard notices */

	/**
	 * Displays standar WordPress dashboard notice.
	 *
	 * @param string $message     Message to display.
	 * @param string $level       Can be error, warning, info or success.
	 * @param bool   $dismissible determines if the notice can be dismissed via javascript.
	 */
	public function notice( $message, $level = 'info', $dismissible = true ) {

		$notice_obj = new \stdClass();

		$notice_obj->notice_message = $message;

		if ( ! in_array( $level, array( 'error', 'warning', 'info', 'success' ), true ) ) {
			$level = 'info';
		}
		$notice_obj->notice_class = 'notice notice-' . $level;
		if ( $dismissible ) {
			$notice_obj->notice_class .= ' is-dismissible';
		}
		$this->notices[] = $notice_obj;
		add_action( 'admin_notices', array( $this, 'display_notice' ) );
	}
	/**
	 * Callback function for the admin_notices action in the notice() function.
	 */
	public function display_notice() {
		$lmm_allowed_notice_tags = array(
			'strong' => array(),
			'a'      => array(
				'href' => array(),
			),
		);
		foreach ( $this->notices as $notice ) {
			if ( ! empty( $notice->notice_message ) ) {
				?>
			<div class="<?php echo esc_attr( $notice->notice_class ); ?>">
				<p><?php echo wp_kses( $notice->notice_message, $lmm_allowed_notice_tags ); ?></p>
			</div>
				<?php
			}
		}
	}
}