<?php

namespace WPWM;

class API
{
	const API_URI = 'https://cloud.website-mail.com/api/v1';

	protected $session_id;
	protected $session_key;

	public function __construct($session_id = null, $session_key = null)
	{
		$this->session_id = $session_id;
		$this->session_key = $session_key;
	}

	public function registerWebsite()
	{
		$path = '/websites/register';
		return $this->post($path);
	}

	public function addDomain($domain)
	{
		$path = '/websites/domains';
		return $this->post($path, array('domain' => array('domain' => $domain)));
	}

	public function getDomain($domain_id)
	{
		$path = '/websites/domains' . $domain_id;
		return $this->get($path);
	}

	public function requestDomainVerification($domain_id)
	{
		$path = '/websites/domains/' . $domain_id . '/verify';
		return $this->post($path);
	}

	public function sendEmail($domain_id, $to, $cc = '', $bcc = '', $subject, $message_text, $message_html = '')
	{
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
		return $this->post($path, $body);
	}

	protected function get($path, $params = array(), $headers = array(), $args = array())
	{
		$this->log('POST request initiated', array($path));

		$headers['Authorization'] = $this->getAPISessionHeader();
		$args['headers'] = $headers;

		$response = wp_remote_get($this->getURL($path, $params), $args);

		$response_body = wp_remote_retrieve_body($response);
		$response_status_code = wp_remote_retrieve_response_code($response);

		if (isset($response_body)) {
			$response_body = json_decode($response_body);
		}

		$this->log('POST request ended', array($path, $response_status_code));
		return array(
			$response_body,
			$response_status_code
		);
	}

	protected function post($path, $body = array(), $headers = array(), $args = array())
	{
		$this->log('POST request initiated', array($path));

		$headers['Authorization'] = $this->getAPISessionHeader();
		$json = wp_json_encode($body);
		$args['body'] = $json;
		$headers['Content-Type'] = 'application/json';

		$args['headers'] = $headers;

		$response = wp_remote_post($this->getURL($path), $args);

		$response_body = wp_remote_retrieve_body($response);
		$response_status_code = wp_remote_retrieve_response_code($response);

		if (isset($response_body)) {
			$response_body = json_decode($response_body);
		}

		$this->log('POST request ended', array($path, $response_status_code));
		return array(
			$response_body,
			$response_status_code
		);
	}

	public function getSessionID()
	{
		return $this->session_id;
	}

	public function getSessionKey()
	{
		return $this->session_key;
	}

	public function setSessionID($val)
	{
		$this->session_id = $val;
	}

	public function setSessionKey($val)
	{
		$this->session_key = $val;
	}

	private function getURL($path, $params = array())
	{
		$uri = add_query_arg($params, self::API_URI . $path);
		return $uri;
	}

	private function getAPISessionHeader()
	{
		if (!isset( $this->session_id ) || !isset($this->session_key)) {
			$this->log('Session ID or Session Key not set.');
		}
		return 'Bearer ' . $this->session_id . ':' . $this->session_key;
	}

	protected function log($message, $context = array())
	{
		array_unshift($context, 'API Context');
		Tools::log($message, $context);
	}
}