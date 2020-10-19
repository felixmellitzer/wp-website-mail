<?php
namespace WPWM;

use \Monolog\Logger;
use \Monolog\Handler\ErrorLogHandler;

class Tools {

	protected static $logger;

	public static function setup_global_logger() {
		self::$logger = new Logger('wp-website-mail');
		self::$logger->pushHandler(new ErrorLogHandler);
	}

	public static function logger() {
		return self::$logger;
	}

	public static function log( $message, $context = array() ) {
		self::$logger->info( $message, $context );
	}

	public static function get_site_domain() {
		return parse_url( home_url() )['host'];
	}
}
