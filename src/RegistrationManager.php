<?php
namespace WPWM;

class RegistrationManager {
	protected $api;

	public function __construct() {
		$this->api = new API();
	}

	public function register_new_website() {
		Tools::log( 'Register new website with API', ['RegistrationManager'] );

		$registration_response = $this->api->register_website();
		$response_status = $registration_response[1];
		$response_body = $registration_response[0];

		if ( $response_status >= 200 && $response_status <= 299 ) {
			Tools::log( 'Registration with API successful', ['RegistrationManager'] );

			$website = $response_body->website;
			$api_session = $response_body->api_session;

			Options::set_session_id( $api_session->id );
			Options::set_session_key( $api_session->api_key );

			$this->api->set_session_id( $api_session->id );
			$this->api->set_session_key( $api_session->api_key );

			return true;
		} else {
			Tools::log( 'Registration with API UNsuccessful', ['RegistrationManager'] );

			return false;
		}
	}

	public function add_domain_to_website() {
		Tools::log( 'Add new domain to website with API', ['RegistrationManager'] );

		$this->set_session_details_from_db();

		$api_response = $this->api->add_domain( Tools::get_site_domain() );
		$response_status = $api_response[1];
		$response_body = $api_response[0];

		if ( $response_status >= 200 && $response_status <= 299 ) {
			Tools::log( 'Adding domain to website successful', ['RegistrationManager'] );

			Options::set_domain_id( $response_body->id );
			Options::set_verification_token( $response_body->verification_token );

			return true;
		} else {
			Tools::log( 'Adding domain to website UNsuccessful', ['RegistrationManager'] );

			return false;
		}
	}

	public function request_api_for_verification() {
		Tools::log( 'Request API for verification', ['RegistrationManager'] );

		$this->set_session_details_from_db();

		$api_response = $this->api->request_domain_verification( Options::get_domain_id() );
		$response_status = $api_response[1];
		$response_body = $api_response[0];

		if ( $response_status >= 200 && $response_status <= 299 ) {
			Tools::log( 'Verification request successful', ['RegistrationManager'] );

			if ( isset( $response_body->verified_at ) ) {
				Tools::log( 'Verification was VERIFIED', ['RegistrationManager'] );
				Options::set_verified( true );

				return true;
			} elseif ( isset( $response_body->denied_at ) ) {
				Tools::log( 'Verification was DENIED', ['RegistrationManager'] );
				Options::set_denied( true );

				return false;
			}
		} else {
			Tools::log( 'Verification request UNsuccessful', ['RegistrationManager'] );

			return false;
		}
	}

	public function run() {
		if ( ! $this->register_new_website() ) {
			Tools::log( 'Website registration UNsuccessful', ['RegistrationManager', 'run'] );
			return;
		}

		if ( ! $this->add_domain_to_website() ) {
			Tools::log( 'Adding domain to website UNsuccessful', ['RegistrationManager', 'run'] );
			return;
		}
	}


	private function set_session_details_from_db() {
		$api_session_id = $this->api->get_session_id();
		$api_session_key = $this->api->get_session_key();

		if ( ! isset( $api_session_id ) || ! isset( $api_session_key ) ) {
			$this->api->set_session_id( Options::get_session_id() );
			$this->api->set_session_key( Options::get_session_key() );
		}
	}


	public static function get_verification_token_for_verification() {
		if ( isset( $_GET['get_wm_verification_token'] ) ) {
			Tools::log( 'Verification token was requested', ['RegistrationManager'] );
			echo Options::get_verification_token();
			exit;
		}
	}
}
