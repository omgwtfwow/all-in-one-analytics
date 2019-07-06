<?php

class All_In_One_Analytics_Encrypt {

	/**
	 * Encrypt and decrypt
	 *
	 * @param string $string string to be encrypted/decrypted
	 * @param string $action what to do with this? e for encrypt, d for decrypt
	 *
	 * @return bool|string
	 * @link http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
	 *
	 * @author Nazmul Ahsan <n.mukto@gmail.com>
	 */
	static function encrypt_decrypt( $string, $action = 'e' ) {
		// you may change these values to your own
		$secret_key     = sanitize_text_field( get_site_url() );
		$secret_iv      = md5( $secret_key );
		$output         = false;
		$encrypt_method = "AES-256-CBC";
		$key            = hash( 'sha256', $secret_key );
		$iv             = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		if ( $action == 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		} else if ( $action == 'd' ) {
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}

		return $output;
	}
}
