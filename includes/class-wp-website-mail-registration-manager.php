<?php

class WP_Website_Mail_Registration_Manager {
	protected $api;

	public function __construct() {
		$this->api = new Website_Mail_API();
	}

	public function register_new_website() {
		WPWM_Tools::log( 'Register new website with API', ['RegistrationManager'] );

		$registration_response = $this->api->register_website();
		$response_status = $registration_response[1];
		$response_body = $registration_response[0];

		if ( $response_status >= 200 && $response_status <= 299 ) {
			WPWM_Tools::log( 'Registration with API successful', ['RegistrationManager'] );

			$website = $response_body->website;
			$api_session = $response_body->api_session;

			WPWM_Options::set_session_id( $api_session->id );
			WPWM_Options::set_session_key( $api_session->api_key );

			$this->api->set_session_id( $api_session->id );
			$this->api->set_session_key( $api_session->api_key );

			return true;
		} else {
			WPWM_Tools::log( 'Registration with API UNsuccessful', ['RegistrationManager'] );

			return false;
		}
	}

	public function add_domain_to_website() {
		WPWM_Tools::log( 'Add new domain to website with API', ['RegistrationManager'] );

		$this->set_session_details_from_db();

		$api_response = $this->api->add_domain( WPWM_Tools::get_site_domain() );
		$response_status = $api_response[1];
		$response_body = $api_response[0];

		if ( $response_status >= 200 && $response_status <= 299 ) {
			WPWM_Tools::log( 'Adding domain to website successful', ['RegistrationManager'] );

			WPWM_Options::set_website_id( $response_body->id );
			WPWM_Options::set_verification_token( $response_body->verification_token );

			return true;
		} else {
			WPWM_Tools::log( 'Adding domain to website UNsuccessful', ['RegistrationManager'] );

			return false;
		}
	}

	public function request_api_for_verfication() {
		WPWM_Tools::log( 'Request API for verification', ['RegistrationManager'] );

		$this->set_session_details_from_db();

		$api_response = $this->api->request_domain_verification( WPWM_Options::get_website_id() );
		$response_status = $api_response[1];
		$response_body = $api_response[0];

		if ( $response_status >= 200 && $response_status <= 299 ) {
			WPWM_Tools::log( 'Verification request successful', ['RegistrationManager'] );

			if ( isset( $response_body->verified_at ) ) {
				WPWM_Tools::log( 'Verification was VERIFIED', ['RegistrationManager'] );
				WPWM_Options::set_verified( true );

				return true;
			} elseif ( isset( $response_body->denied_at ) ) {
				WPWM_Tools::log( 'Verification was DENIED', ['RegistrationManager'] );
				WPWM_Options::set_denied( true );

				return false;
			}
		} else {
			WPWM_Tools::log( 'Verification request UNsuccessful', ['RegistrationManager'] );

			return false;
		}
	}

	public function run() {
		if ( ! $this->register_new_website() ) {
			WPWM_Tools::log( 'Website registration UNsuccessful', ['RegistrationManager', 'run'] );
			return;
		}

		if ( ! $this->add_domain_to_website() ) {
			WPWM_Tools::log( 'Adding domain to website UNsuccessful', ['RegistrationManager', 'run'] );
			return;
		}

		if ( ! $this->request_api_for_verfication() ) {
			WPWM_Tools::log( 'Domain request verification UNsuccessful', ['RegistrationManager', 'run'] );
			return;
		}
	}


	private function set_session_details_from_db() {
		$api_session_id = $this->api->get_session_id();
		$api_session_key = $this->api->get_session_key();

		if ( ! isset( $api_session_id ) || ! isset( $api_session_key ) ) {
			$this->api->set_session_id( WPWM_Options::get_session_id() );
			$this->api->set_session_key( WPWM_Options::get_session_key() );
		}
	}


	public static function get_verification_token_for_verification() {
		if ( isset( $_GET['get_wm_verification_token'] ) ) {
			echo WPWM_Options::get_verification_token();
			exit;
		}
	}
}
