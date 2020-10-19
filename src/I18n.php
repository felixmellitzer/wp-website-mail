<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://tfm.agency
 * @since      1.0.0
 *
 * @package    Wp_Website_Mail
 * @subpackage Wp_Website_Mail/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_Website_Mail
 * @subpackage Wp_Website_Mail/includes
 * @author     TFM Agency GmbH <hello@tfm.agency>
 */

namespace WPWM;

class I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function loadPluginTextdomain() {

		load_plugin_textdomain(
			'wp-website-mail',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
