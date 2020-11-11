<?php

namespace WPWM;

class Options
{
    private static $_options = array(
        'session_id',
        'session_key',
        'domain_id',
        'verification_token',
        'verified',
        'denied',
    );

    private static $_current_options = array();

    public static function __callStatic($name, $args)
    {
        $name_exploded = explode('_', $name, 2);
        $prefix = $name_exploded[0];
        $key = $name_exploded[1];

        if (false !== array_search($key, self::$_options)) {
            if ($prefix == 'get') {
                return self::get($key);
            }
            if ($prefix == 'set') {
                return self::set($key, $args[0]);
            }
        }
        trigger_error('Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR);
    }

    public static function hasVerificationStatus()
    {
        return self::get_verified() || self::get_denied();
    }

    public static function deleteAllOptions()
    {
        foreach (self::$_options as $key) {
            self::delete($key);
        }
    }

    protected static function add($option, $value = '', $deprecated = '', $autoload = 'yes')
    {
        self::$_current_options[$option] = $value;
        return add_option('WPWM_' . $option, $value, $deprecated, $autoload);
    }

    protected static function delete($option)
    {
        unset(self::$_current_options[$option]);
        return delete_option('WPWM_' . $option);
    }

    protected static function get($option, $default = false)
    {
        if (isset(self::$_current_options[$option])) {
            return self::$_current_options[$option];
        }

        return self::$_current_options[$option] = get_option('WPWM_' . $option, $default);
    }

    protected static function update($option, $value, $autoload = null)
    {
        self::$_current_options[$option] = $value;
        return update_option('WPWM_' . $option, $value, $autoload);
    }

    protected static function set($option, $value = '')
    {
        $current_value = self::get($option);

        if (is_null($value)) {
            // Delete from database if new value null.
            return self::delete($option);
        } elseif (isset($current_value)) {
            // Update value if already present in database.
            return self::update($option, $value);
        } else {
            // Add value if not present in database yet.
            return self::add($option, $value);
        }
    }
}
