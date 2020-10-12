<?php

class WPWM_Tools {

	protected static $logger;

	public static function setup_global_logger() {
		self::$logger = new Monolog\Logger('wp-website-mail');
		self::$logger->pushHandler(new Monolog\Handler\ErrorLogHandler);
		self::$logger->info('My WPWM LOGGER is now ready');
	}

	public static function logger() {
		return self::$logger;
	}
}
