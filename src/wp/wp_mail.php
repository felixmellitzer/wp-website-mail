<?php

function wp_mail( $to, $subject, $message, $headers = '', $attachments = array() ) {
	$atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );

	if ( isset( $atts['to'] ) ) {
		$to = $atts['to'];
	}

	if ( ! is_array( $to ) ) {
		$to = explode( ',', $to );
	}

	if ( isset( $atts['subject'] ) ) {
		$subject = $atts['subject'];
	}

	if ( isset( $atts['message'] ) ) {
		$message = $atts['message'];
	}

	if ( isset( $atts['headers'] ) ) {
		$headers = $atts['headers'];
	}

	if ( isset( $atts['attachments'] ) ) {
		$attachments = $atts['attachments'];
	}

	if ( ! is_array( $attachments ) ) {
		$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
	}


	// Headers.
	$cc       = array();
	$bcc      = array();
	$reply_to = array();

	if ( empty( $headers ) ) {
		$headers = array();
	} else {
		if ( ! is_array( $headers ) ) {
			// Explode the headers out, so this function can take
			// both string headers and an array of headers.
			$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		} else {
			$tempheaders = $headers;
		}
		$headers = array();

		// If it's actually got contents.
		if ( ! empty( $tempheaders ) ) {
			// Iterate through the raw headers.
			foreach ( (array) $tempheaders as $header ) {
				if ( strpos( $header, ':' ) === false ) {
					if ( false !== stripos( $header, 'boundary=' ) ) {
						$parts    = preg_split( '/boundary=/i', trim( $header ) );
						$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
					}
					continue;
				}
				// Explode them out.
				list( $name, $content ) = explode( ':', trim( $header ), 2 );

				// Cleanup crew.
				$name    = trim( $name );
				$content = trim( $content );

				switch ( strtolower( $name ) ) {
					// Mainly for legacy -- process a "From:" header if it's there.
					case 'from':
						$bracket_pos = strpos( $content, '<' );
						if ( false !== $bracket_pos ) {
							// Text before the bracketed email is the "From" name.
							if ( $bracket_pos > 0 ) {
								$from_name = substr( $content, 0, $bracket_pos - 1 );
								$from_name = str_replace( '"', '', $from_name );
								$from_name = trim( $from_name );
							}

							$from_email = substr( $content, $bracket_pos + 1 );
							$from_email = str_replace( '>', '', $from_email );
							$from_email = trim( $from_email );

							// Avoid setting an empty $from_email.
						} elseif ( '' !== trim( $content ) ) {
							$from_email = trim( $content );
						}
						break;
					case 'content-type':
						if ( strpos( $content, ';' ) !== false ) {
							list( $type, $charset_content ) = explode( ';', $content );
							$content_type                   = trim( $type );
							if ( false !== stripos( $charset_content, 'charset=' ) ) {
								$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
							} elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
								$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
								$charset  = '';
							}

							// Avoid setting an empty $content_type.
						} elseif ( '' !== trim( $content ) ) {
							$content_type = trim( $content );
						}
						break;
					case 'cc':
						$cc = array_merge( (array) $cc, explode( ',', $content ) );
						break;
					case 'bcc':
						$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
						break;
					case 'reply-to':
						$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
						break;
					default:
						// Add it to our grand headers array.
						$headers[ trim( $name ) ] = trim( $content );
						break;
				}
			}
		}
	}

	// Set Content-Type and charset.

	// If we don't have a content-type from the input headers.
	if ( ! isset( $content_type ) ) {
		$content_type = 'text/plain';
	}

	/**
	 * Filters the wp_mail() content type.
	 *
	 * @since 2.3.0
	 *
	 * @param string $content_type Default wp_mail() content type.
	 */
	$content_type = apply_filters( 'wp_mail_content_type', $content_type );


	// Set whether it's plaintext, depending on $content_type.
	if ( 'text/html' === $content_type ) {
		// TODO: Send html email
	}

	// If we don't have a charset from the input headers.
	if ( ! isset( $charset ) ) {
		$charset = get_bloginfo( 'charset' );
	}

	if ( ! empty( $attachments ) ) {
		foreach ( $attachments as $attachment ) {
			// TODO: Attachments
		}
	}

	$api = new WPWM\API( WPWM\Options::get_session_id(), WPWM\Options::get_session_key() );
	$api->sendEmail( WPWM\Options::get_domain_id(), implode( ',', $to ), implode( ',', $cc ), implode( ',', $bcc ), $subject, $message );
}