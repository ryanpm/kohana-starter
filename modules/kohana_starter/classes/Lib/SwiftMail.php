<?php defined('SYSPATH') or die('No direct script access.');

include_once( MODAPP .'/vendor/swiftmail/swiftmail/Swift-5.0.3/lib/swift_required.php');

class Lib_SwiftMail{

	public static function message()
	{
		return Swift_Message::newInstance();
	}

	public static function send(Swift_Message $message)
	{
		$transport = Swift_SmtpTransport::newInstance('smtp.sendgrid.net', 25)
		->setUsername('azure_5558a36fa0619586680c4f01d015b232@azure.com')
		->setPassword('tnfimmmg');
		$mailer = Swift_Mailer::newInstance($transport);
		return $result = $mailer->send($message);

	}

}