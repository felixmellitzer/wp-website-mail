<?php
namespace WPWM;

class MailManager {
	public function replaceWPMailer() {
		if ( ! Options::get_verified() ) {
			return;
		}

		require_once __DIR__ . '/wp/wp_mail.php';;
	}
}
