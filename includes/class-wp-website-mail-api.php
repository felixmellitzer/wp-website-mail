<?php

class Website_Mail_API {
	const API_URI = 'https://cloud.website-mail.com/api/v1';

	protected $session_id;
	protected $session_key;

	public function __construct( $session_id = null, $session_key = null ) {
		$this->session_id = $session_id;
		$this->session_key = $session_key;
	}

	public function register_website() {
		$path = '/websites/register';

		return $this->post( $path );
	}


	public function add_domain( $domain ) {
		$path = '/websites/domains';

		return $this->post( $path, array( 'domain' => array( 'domain' => $domain ) ) );
	}

	public function get_domain( $domain_id ) {
		$path = '/websites/domains' . $domain_id;

		return $this->get( $path );
	}

	public function request_domain_verification( $domain_id ) {
		$path = '/websites/domains/' . $domain_id . '/verify';

		return $this->post( $path );
	}

	public function send_email( $domain_id, $to, $cc = '', $bcc = '', $subject, $message_text, $message_html = '' ) {
		$path = '/emails';

		$body = array(
			'email' => array(
				'website_domain_id' => $domain_id,
				'to' => $to,
				'cc' => $cc,
				'bcc' => $bcc,
				'subject' => $subject,
				'message_text' => $message_text,
				'message_html' => $message_html,
			)
		);

		return $this->post( $path, $body );
	}

	protected function get( $path, $params = array(), $headers = array(), $args = array() ) {
		$this->log( 'POST request initiated', array( $path ) );

		$headers['Authorization'] = get_api_session_header();
		$args['headers'] = $headers;

		$response = wp_remote_get( $this->get_url($path, $params), $args );

		$response_body = wp_remote_retrieve_body( $response );
		$response_status_code = wp_remote_retrieve_response_code( $response );

		if ( isset( $response_body ) ) {
			$response_body = json_decode( $response_body );
		}

		$this->log( 'POST request ended', array( $path, $response_status_code ) );

		return array(
			$response_body,
			$response_status_code
		);
	}

	protected function post( $path, $body = array(), $headers = array(), $args = array() ) {
		$this->log( 'POST request initiated', array( $path ) );

		$headers['Authorization'] = $this->get_api_session_header();
		$json = wp_json_encode( $body );
		$args['body'] = $json;
		$headers['Content-Type'] = 'application/json';

		$args['headers'] = $headers;

		$response = wp_remote_post( $this->get_url($path), $args );

		$response_body = wp_remote_retrieve_body( $response );
		$response_status_code = wp_remote_retrieve_response_code( $response );

		if ( isset( $response_body ) ) {
			$response_body = json_decode( $response_body );
		}

		$this->log( 'POST request ended', array( $path, $response_status_code ) );

		return array(
			$response_body,
			$response_status_code
		);
	}

	public function get_session_id() {
		return $this->session_id;
	}

	public function get_session_key() {
		return $this->session_key;
	}

	public function set_session_id( $val ) {
		$this->session_id = $val;
	}

	public function set_session_key( $val ) {
		$this->session_key = $val;
	}


	private function get_url( $path, $params = array() ) {
		$uri = add_query_arg( $params, self::API_URI . $path );
		return $uri;
	}

	private function get_api_session_header() {
		if ( ! isset( $this->session_id ) || ! isset( $this->session_key ) ) {
			$this->log( 'Session ID or Session Key not set.' );
		}

		return 'Bearer ' . $this->session_id . ':' . $this->session_key;
	}

	protected function log( $message, $context = array() ) {
		array_unshift( $context, 'API Context' );
		WPWM_Tools::log( $message, $context );
	}
}