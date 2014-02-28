<?php defined('SYSPATH') or die('No direct script access.');

class Lib_Email{

  public static $header = "From: admin@ideabank.com.sg\r\nReply-To: admin@ideabank.com.sg\r\n";

  public static function sendNewPassword($email, $data)
  {

    $default = array(
      'urlsite'      => URL::site(NULL, TRUE),
      'url'      => '',
      'email'    => '',
      'fullname' => ''
      );

    $data = $data + $default;
    $body = Lib_View::factory('_email/request_password',$data)->render();
    self::email($email,'New Password Request', $body);

  }

  public static function registerNotifyAdmin($email, $data)
  {

    $default = array(
      'approve_link' => URL::base(TRUE)  .'admin/index.php/user/list?status=0',
      'username'     => ''
      );
    $data = $data + $default;
    $body = Lib_View::factory('_email/register_notify_admin',$data)->render();
    self::email($email,'New Student Joined', $body);

  }

  public static function approvedNotifyUser($email, $data)
  {

    $default = array(
      'fullname'  => '',
      'url_login' => URL::site(NULL, TRUE) . 'login',
      );
    $data = $data + $default;
    $body = Lib_View::factory('_email/account_approved',$data)->render();
    self::email($email,'Account Approved', $body);

  }

  public static function amountTransferredNotifyUser($email, $data)
  {

    $default = array(
    'idea'      => '',
    'url_login' => URL::site(NULL, TRUE) . 'login',
    );
    $data = $data + $default;
    $body = Lib_View::factory('_email/amount_transferred_to_user',$data)->render();
    self::email($email,'Cash Transferred', $body);
  }

  public static function sendEnquiry($email, $data)
  {
    $body = Lib_View::factory('_email/enquiry',$data)->render();
    self::email($email,'Enquiry', $body);
  }

  public static function notifyCompletedRequestFunding($email, $data)
  {

    // send to member
    $data['name'] = $data['idea']->user->fullname;
    $data['message'] = 'Congratualtion, your project has met the request funding.';
    $body = Lib_View::factory('_email/request_funding_completed',$data)->render();
    self::email($email,'Request Funding Completed', $body);

    // send to admin
    $data['name'] = 'Admin';
    $data['message'] = 'Request funding has met the goal';
    $body = Lib_View::factory('_email/request_funding_completed',$data)->render();
    self::email('admin@ideabank.com.sg','Request Funding Completed', $body);

  }

  public static function notifyPublishedProject($email, $data)
  {

    // send to member
    $data['name'] = $data['idea']->user->fullname;
    $data['message'] = 'You have published your project.';
    $body = Lib_View::factory('_email/publish_notification',$data)->render();
    self::email($email,'Idea Bank: Notification', $body);

    // send to admin
    $data['name'] = 'Admin';
    $data['message'] = 'User has publish a project.';
    $body = Lib_View::factory('_email/publish_notification',$data)->render();
    self::email('admin@ideabank.com.sg','Publish Notification', $body);
  }

  public static function email($email,$subject,$body,$html=true)
  {
      if( $email == '' ){
        throw new Exception("Email is empty");
      }
      if( $html ){

        $body = View::factory('_email/template', array(
          'urlbase' => URL::base(),
          'urlsite' => URL::site(NULL, TRUE),
          'subject' => $subject,
          'body'    => $body
          ))->render();
        static::$header .=  "MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
      }

      @file_put_contents( APPPATH .'test/email/'.date('Y-m-d H-i-s') .'.'. (rand(111,999))  .'.txt' , $email."\n".$subject."\n".$body);

      // $re = explode(',', $email);
      // foreach ($re as $e) {
      //    @mail(trim($e),$subject,$body, static::$header);
      //  }

      $message =  SwiftMail::message()
      ->setSubject($subject)
      ->setFrom(array('admin@ideabank.com.sg'=>'Admin'))
      ->setTo(explode(',',$email))
      ->setBody($body, 'text/html')
      ->addPart($body);

      // And optionally an alternative body
      // ->addPart($body, 'text/html');
      // Optionally add any attachments
      // ->attach(Swift_Attachment::fromPath('my-document.pdf'));

      return SwiftMail::send($message);

  }

}