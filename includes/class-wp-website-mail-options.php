<?php

class WPWM_Options {

	public static function get_session_id() {
		return self::get( 'SESSION_ID' );
	}

	public static function set_session_id( $val ) {
		return self::set( 'SESSION_ID', $val );
	}

	public static function get_session_key() {
		return self::get( 'SESSION_KEY' );
	}

	public static function set_session_key( $val ) {
		return self::set( 'SESSION_KEY', $val );
	}

	public static function get_website_id() {
		return self::get( 'WEBSITE_ID' );
	}

	public static function set_website_id( $val ) {
		return self::set( 'WEBSITE_ID', $val );
	}

	public static function get_verification_token() {
		return self::get( 'VERIFICATION_TOKEN' );
	}

	public static function set_verification_token( $val ) {
		return self::set( 'VERIFICATION_TOKEN', $val );
	}

	public static function get_verified() {
		return self::get( 'VERIFIED' );
	}

	public static function set_verified( $val ) {
		return self::set( 'VERIFIED', $val );
	}

	public static function get_denied() {
		return self::get( 'DENIED' );
	}

	public static function set_denied( $val ) {
		return self::set( 'DENIED', $val );
	}


	protected static function add( $option, $value = '', $deprecated = '', $autoload = 'yes' ) {
		return add_option( 'WPWM_' . $option, $value, $deprecated, $autoload );
	}

	protected static function delete( $option ) {
		return delete_option( 'WPWM_'. $option );
	}

	protected static function get( $option, $default = false ) {
		return get_option( 'WPWM_'. $option, $default );
	}

	protected static function update( $option, $value, $autoload = null ) {
		return update_option( 'WPWM_'. $option, $value, $autoload );
	}

	protected static function set( $option, $value = '' ) {
		$current_value = self::get( $option );

		if ( is_null( $value ) ) {
			// Delete from database if new value null.
			return self::delete( $option );
		} elseif ( isset( $current_value ) ) {
			// Update value if already present in database.
			return self::update( $option, $value );
		} else {
			// Add value if not present in database yet.
			return self::add( $option, $value );
		}
	}

}
