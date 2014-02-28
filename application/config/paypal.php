
<?php defined('SYSPATH') OR die('No direct script access.');

return array(
	'use' => 'sandbox',
	'production' => array(
			'sandbox'        => false,
			'url'            => 'https://www.paypal.com/cgi-bin/webscr',
			'api_url'        => 'https://api-3t.paypal.com/nvp',
			'merchant_id'    => '',
			'receiver_email' => '',
			'currency'       => '',
			'return_url'     => URL::site(NULL, TRUE).'thank-you',
			'cancel_url'     => URL::site(NULL, TRUE).'cancelled',

			'api_un'         => '',
			'api_pw'         => '',
			'api_sig'        => '',

		),
	'sandbox' => array(
			'sandbox'        => true,
			'api_url'        => 'https://api-3t.sandbox.paypal.com/nvp',
			'url'            => 'https://sandbox.paypal.com/cgi-bin/webscr',
			'merchant_id'    => '',
			'receiver_email' => '',
			'currency'       => '',
			'return_url'     => URL::site(NULL, TRUE).'thank-you',
			'cancel_url'     => URL::site(NULL, TRUE).'cancelled',

			'api_un'         => '',
			'api_pw'         => '',
			'api_sig'        => '',
		)
);
