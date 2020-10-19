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

namespace WPWM;

class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$registration_manager = new RegistrationManager();
		$registration_manager->run();

		Tools::log( 'Plugin has been activated.' );
	}

}
