<?php

/**
 * Fired during plugin activation
 *
 * @link       https://tfm.agency
 * @since      1.0.0
 *
 * @package    Wp_Website_Mail
 * @subpackage Wp_Website_Mail/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wp_Website_Mail
 * @subpackage Wp_Website_Mail/includes
 * @author     TFM Agency GmbH <hello@tfm.agency>
 */
class Wp_Website_Mail_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-website-mail-registration-manager.php';

		$registration_manager = new WP_Website_Mail_Registration_Manager();
		$registration_manager->run();

		WPWM_Tools::log( 'Plugin has been activated.' );
	}

}
