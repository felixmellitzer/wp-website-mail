<?php
namespace WPWM;

class MailManager
{
	public function replaceWPMailer()
	{
		if (!Options::get_verified()) {
			return;
		}

		require_once dirname(__FILE__) . '/wp/wp_mail.php'; 
	}
}