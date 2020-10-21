<?php

namespace WPWM;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class API {
	const API_URI = 'https://cloud.website-mail.com/api/v1/';

	protected $session_id;
	protected $session_key;

	protected $http_client;

	public function __construct( $session_id = null, $session_key = null ) {
		$this->session_id = $session_id;
		$this->session_key = $session_key;

		$this->http_client = new Client([
			'base_uri' => self::API_URI,
			'http_errors' => false
		]);
	}

	public function registerWebsite() {
		$path = 'websites/register';

		return $this->post( $path );
	}


	public function addDomain( $domain ) {
		$path = 'websites/domains';

		return $this->post( $path, array( 'domain' => array( 'domain' => $domain ) ) );
	}

	public function getDomain( $domain_id ) {
		$path = 'websites/domains' . $domain_id;

		return $this->get( $path );
	}

	public function requestDomainVerification( $domain_id ) {
		$path = 'websites/domains/' . $domain_id . '/verify';

		return $this->post( $path );
	}

	public function sendEmail($domain_id, $to, $cc = '', $bcc = '', $subject, $message_text, $message_html = '', $attachments = null) {
		$path = 'emails';

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

		$headers = array();
		$multipart = null;

		if (isset($attachments)) {
			$multipart = array();

			foreach ($attachments as $attachment) {
				array_push($multipart, [
					'name' => end(explode('/', $attachment)),
					'contents' => fopen($attachment)
				]);
			}
		}

		return $this->post($path, $body, $headers, $multipart);
	}

	protected function get($path, $params = array(), $headers = array()) {
		$this->log( 'GET request initiated', array( $path ) );

		$headers['Authorization'] = $this->getAPISessionHeader();

		$request = new Request('GET', $path, $headers);
		$request->withQueryParams($params);

		$response = $this->http_client->send($request);
		
		$response_body = $response->getBody();
		$response_status_code = $response->getStatusCode();

		if ( isset( $response_body ) ) {
			$response_body = json_decode( $response_body );
		}

		$this->log( 'GET request ended', array( $path, $response_status_code ) );

		return array(
			$response_body,
			$response_status_code
		);
	}

	protected function post($path, $body = array(), $headers = array(), $uploads = null) {
		$this->log( 'POST request initiated', array( $path ) );

		$headers['Authorization'] = $this->getAPISessionHeader();

		$request_options = [
			'form_params' => $body,
			'headers' => $headers
		];

		if (isset($multipart)) {
			$request_options['multipart'] = $multipart;
		} else {
			
		}

		$response = $this->http_client->request('POST', $path, $request_options);

		$response_body = $response->getBody();
		$response_status_code = $response->getStatusCode();

		if ( isset( $response_body ) ) {
			$response_body = json_decode( $response_body );
		}

		$this->log( 'POST request ended', array( $path, $response_status_code ) );

		return array(
			$response_body,
			$response_status_code
		);
	}

	public function getSessionID() {
		return $this->session_id;
	}

	public function getSessionKey() {
		return $this->session_key;
	}

	public function setSessionID( $val ) {
		$this->session_id = $val;
	}

	public function setSessionKey( $val ) {
		$this->session_key = $val;
	}


	private function getAPISessionHeader() {
		if ( ! isset( $this->session_id ) || ! isset( $this->session_key ) ) {
			$this->log( 'Session ID or Session Key not set.' );
		}

		return 'Bearer ' . $this->session_id . ':' . $this->session_key;
	}

	protected function log( $message, $context = array() ) {
		array_unshift( $context, 'API Context' );
		Tools::log( $message, $context );
	}
}