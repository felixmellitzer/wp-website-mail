<?php

class Website_Mail_API {
	const API_URI = 'https://cloud.website-mail.com/api/v1';

	protected $session_id;
	protected $session_key;

	public function __construct( $session_id, $session_key ) {
		$this->session_id = $session_id;
		$this->session_key = $session_key;
	}

	public function register_website() {
		$path = '/websites/register';

		return self::post( $path );
	}


	public function add_domain( $domain ) {
		$path = '/websites/domains';

		return self::post( $path, array( 'domain' => array( 'domain' => $domain ) ) );
	}

	public function get_domain( $domain_id ) {
		$path = '/websites/domains' . $domain_id;

		return self::get( $path );
	}

	public function request_domain_verification( $domain_id ) {
		$path = '/websites/domains/' . $domain_id . 'verify';

		return self::post( $path );
	}

	public function send_email( $domain_id, $recipient, $subject, $message ) {
		$path = '/emails';

		$body = array(
			'email' => array(
				'website_domain_id' => $domain_id,
				'recipient' => $recipient,
				'subject' => $subject,
				'message' => $message,
			)
		);

		return self::post( $path, $body );
	}


	public function unauth_get( $path, $params = array(), $headers = array(), $args = array() ) {
		
		$args['headers'] = $headers;

		$response = wp_remote_get( get_url($path, $params), $args );

		return wp_remote_retrieve_body( $response );
	}

	public function get( $path, $params = array(), $headers = array(), $args = array() ) {
		$headers['Authorization'] = get_api_session_header();
	}

	public function post( $path, $body = array(), $headers = array(), $args = array() ) {
		$headers['Authorization'] = get_api_session_header();
		$json = wp_json_encode( $body );
		$args['body'] = $json;
		$headers['Content-Type'] = 'application/json';

		$args['headers'] = $headers;

		$response = wp_remote_post( get_url($path), $args );

		return wp_remote_retrieve_body( $response );
	}


	private function get_url( $path, $params = array() ) {
		$uri = add_query_arg( $params, self::API_URI . path );
		return $uri
	}

	private function get_api_session_header() {
		return 'Bearer ' . $this->session_id . ':' . $this->session_key;
	}
}