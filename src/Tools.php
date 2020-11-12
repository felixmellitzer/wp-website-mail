<?php

namespace WPWM;

use \Monolog\Logger;
use \Monolog\Handler\ErrorLogHandler;

/**
 * All Tool functions.
 *
 * This class describes all functions, who are called as a tool.
 *
 * @since 1.0.0
 */
class Tools
{
    /**
     * Holds the Monolog Logger object.
     *
     * @since   1.0.0
     * @access  protected
     * @var     Looger
     */
    protected static $logger;

    public static function setupGlobalLogger()
    {
        self::$logger = new Logger('wp-website-mail');
        self::$logger->pushHandler(new ErrorLogHandler);
    }

    public static function logger()
    {
        return self::$logger;
    }

    /**
     * Logs a message in a certain context.
     *
     * @since 1.0.0
     *
     * @param string $message write log message.
     * @param string|array $context describe in wich context is the log message.
     */
    public static function log($message, $context = array())
    {
        self::$logger->info($message, $context);
    }

    /**
     * Gets the Site Domain.
     *
     * @since 1.0.0
     * @return string
     */
    public static function getSiteDomain()
    {
        return parse_url(home_url())['host'];
    }
}
