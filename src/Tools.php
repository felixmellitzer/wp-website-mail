<?php
namespace WPWM;

use \Monolog\Logger;
use \Monolog\Handler\ErrorLogHandler;

class Tools {

	protected static $logger;

	public static function setupGlobalLogger() {
		self::$logger = new Logger('wp-website-mail');
		self::$logger->pushHandler(new ErrorLogHandler);
	}

	public static function logger() {
		return self::$logger;
	}

	public static function log( $message, $context = array() ) {
		self::$logger->info( $message, $context );
	}

	public static function getSiteDomain() {
		return parse_url( home_url() )['host'];
	}
}