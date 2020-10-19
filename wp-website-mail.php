<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://tfm.agency
 * @since             1.0.0
 * @package           Wp_Website_Mail
 *
 * @wordpress-plugin
 * Plugin Name:       website-mail.com
 * Plugin URI:        https://website-mail.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            TFM Agency GmbH
 * Author URI:        https://tfm.agency
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-website-mail
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (! defined( 'WPINC')) {
	die;
}

require __DIR__ . '/vendor/autoload.php';


/**
 * Setup logging functionality.
 */
WPWM\Tools::setupGlobalLogger();

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WP_WEBSITE_MAIL_VERSION', '1.0.0');


register_activation_hook(__FILE__, 'WPWM\Activator::activate');
register_deactivation_hook(__FILE__, 'WPWM\Deactivator::deactivate');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_website_mail()
{
	$plugin = new WPWM\Main();
	$plugin->run();
}

run_wp_website_mail();