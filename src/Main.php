<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://tfm.agency
 * @since      1.0.0
 *
 * @package    Wp_Website_Mail
 * @subpackage Wp_Website_Mail/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wp_Website_Mail
 * @subpackage Wp_Website_Mail/includes
 * @author     TFM Agency GmbH <hello@tfm.agency>
 */

namespace WPWM;

class Main
{
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Website_Mail_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('WP_WEBSITE_MAIL_VERSION')) {
			$this->version = WP_WEBSITE_MAIL_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-website-mail';

		$this->loadDependencies();
		$this->setLocale();
		$this->defineAdminHooks();
		$this->definePublicHooks();
		$this->defineHooks();

	}
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Website_Mail_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Website_Mail_i18n. Defines internationalization functionality.
	 * - Wp_Website_Mail_Admin. Defines all hooks for the admin area.
	 * - Wp_Website_Mail_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function loadDependencies()
	{
		$this->loader = new Loader();
	}
	
	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Website_Mail_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function setLocale() {

		$plugin_i18n = new I18n();

		$this->loader->addAction('plugins_loaded', $plugin_i18n, 'loadPluginTextdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function defineAdminHooks()
	{
		$plugin_admin = new Admin\Controller($this->getPluginName(), $this->getVersion());

		$this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->addAction('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function definePublicHooks()
	{
		$plugin_public = new Admin\Controller($this->getPluginName(), $this->getVersion());

		$this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->addAction('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
	}


	private function defineHooks()
	{
		$mail_manager = new MailManager();

		add_action('init', '\WPWM\RegistrationManager::getVerificationTokenForVerification');
		
		$this->loader->addAction('plugin_loaded', $mail_manager, 'replaceWPMailer');

		// Request verification
		if (!Options::hasVerificationStatus() && Options::get_domain_id()) {
			$registration_manager = new RegistrationManager();
			$this->loader->addAction('init', $registration_manager, 'requestAPIForVerification');
		}
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function getPluginName()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Website_Mail_Loader    Orchestrates the hooks of the plugin.
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function getVersion()
	{
		return $this->version;
	}

}
