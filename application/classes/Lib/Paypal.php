<?php

if (!function_exists("get_option")) {
  die;  // Silence is golden, direct call is prohibited
}

class Lib_Paypal{

  	protected $paypal_email;
	protected $paypal_URL;
	private $validateAccount;
	private $currency;
	private $notify_url;
	private $return_url;
	private $cancel_url;
 
	private $api_un;
	private $api_pw;
	private $api_sig;
	private $api_ver;

	public function __construct(){

		$this->currency = 'SGD';
		$this->sandbox = true;

		if($this->sandbox){
			//sandbox
			$this->paypal_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			// $this->paypal_email = 'G725YRJS2AW92';
			$this->paypal_email = 'manalastas_ryan4@yahoo.com';

	 		$this->api_un = 'manalastas_ryan4_api1.yahoo.com';
	 		$this->api_pw = '1375077669';
	 		$this->api_sig = 'Ahm5F0260Lu30tbpDM64QZtSFJWkAWC3jinNbqkLRM0-ybetGE0eSGkZ';

		}else{

			$this->api_un = ' ';
	 		$this->api_pw = ' ';
	 		$this->api_sig = ' ';

			$this->paypal_URL = "https://www.paypal.com/cgi-bin/webscr";
			$this->paypal_email = get_option('sgsh_paypal_email');

		}
 		$this->api_ver = '98.0';

		$this->validateAccount = false;
		if(SGSH_common::validateEmail($this->paypal_email)){
			$this->validateAccount = true;
		}

		global $sgsh_pages;
		// $this->notify_url = SGSH_PLUGIN_URL.'/standard_ipn.php';
		$this->return_url = $sgsh_pages->prepare_url($sgsh_pages->user_page_id, array('vpg'=>'pln','vptp'=>'done') );
		$this->cancel_url = $sgsh_pages->prepare_url($sgsh_pages->user_page_id, array('vpg'=>'pln','vptp'=>'summary') );

	} 
   
	public function get_reponse_array($res){
		$ret = array();
		parse_str($res,$ret);
		return $ret;
	}

	public function get_response($method,$data)
	{

	global $sgsh_current_user,$wpdb;
		$url = '';
		$values = array();

		if($this->sandbox){
			$url = 'api-3t.sandbox.paypal.com';
		}else{
			$url = 'api-3t.paypal.com';
		}

		$def_data['USER'] 		= $this->api_un;
		$def_data['PWD'] 		= $this->api_pw;
		$def_data['SIGNATURE'] 	= $this->api_sig;
		$def_data['VERSION'] 	= $this->api_ver;
		$data = $def_data + $data;

		// $postdata = http_build_query(
		//     array('METHOD'=>$method)+$data
		// );

		// $opts = array('http' =>
		//     array(
		//         'method'  => 'POST',
		//         'header'  => 'Content-type: application/x-www-form-urlencoded',
		//         'content' => $postdata
		//     )
		// );
		// $context  = stream_context_create($opts);
		// $result = file_get_contents('https://api-3t.sandbox.paypal.com/nvp', false, $context);
		// $values = $this->get_reponse_array($result);

		$req = 'METHOD='.$method;
		foreach ($data as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
		$header = "POST /nvp HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		if($this->sandbox){
			$fp = fsockopen ('ssl://api-3t.sandbox.paypal.com', 443, $errno, $errstr, 30);
		}else{
			$fp = fsockopen ('ssl://api-3t.paypal.com', 443, $errno, $errstr, 30);
		}
		$values = array();
		if ($fp) {
			fputs ($fp, $header . $req);
			$res = '';
			while (!feof($fp)) {
				$res .= stream_get_contents($fp, 2048);
			}
			$parsed = explode("\r\n\r\n",$res);
			if( count($parsed) > 1 ){
				$values = $this->get_reponse_array($parsed[1]);
			}

		}

		$history['datecreated'] = date('Y-m-d H:i:s');
		$history['txn_type'] 	= $method;
		$history['sg_user_id'] 	= $sgsh_current_user->get_current_user_id();
		$history['subscr_id'] 	=  isset($values['PROFILEID'])?$values['PROFILEID']:'';
		$history['txn_id'] 		=  isset($values['PAYMENTINFO_0_TRANSACTIONID'])?$values['PAYMENTINFO_0_TRANSACTIONID']:'';
		$history['payment_status'] =  isset($values['PAYMENTINFO_0_PAYMENTSTATUS'])?$values['PAYMENTINFO_0_PAYMENTSTATUS']:'';
		$history['token'] 		= isset($data['TOKEN'])?$data['token']:'';
		$history['post_data'] 		= serialize($values);
		$history['request_data'] 		= serialize($data);
		$wpdb->insert('sgsh_paypal_history',$history);
 

		return $values;
	}

	public function get_api_ulr($token)
	{
		return $this->paypal_URL .'?cmd=_express-checkout&token='. urlencode($token);
	}

	public function set_express_checkout($planID, $amount, $itemname, $frequency)
	{
		global $sgsh_current_user, $wpdb;
		$freq_name =$this->get_frequency_code($frequency).'ly';

		$data['PAYMENTREQUEST_0_PAYMENTACTION'] 	=  'Sale';
		$data['L_BILLINGTYPE0'] = "RecurringPayments";
		$data['L_BILLINGAGREEMENTDESCRIPTION0'] = "Show House - {$itemname}({$freq_name}) - {$this->currency} ". number_format($amount,2);
		$data['L_PAYMENTTYPE0'] = "Any";

		$data['PAYMENTREQUEST_0_AMT'] = $amount;
		$data["PAYMENTREQUEST_0_CURRENCYCODE"] = $this->currency;
		$data["PAYMENTREQUEST_0_ITEMAMT"] = $amount;
		$data["PAYMENTREQUEST_0_DESC"] = "Show House - Payment";

		$data['L_PAYMENTREQUEST_0_NAME0'] = "{$itemname}({$freq_name})";
		$data['L_PAYMENTREQUEST_0_AMT0'] = $amount;
		$data['L_PAYMENTREQUEST_0_QTY0'] = 1;
		$data['L_PAYMENTREQUEST_0_NUMBER0'] = $sgsh_current_user->get_current_user_id().'-'. $planID;

		$data['RETURNURL'] = $this->return_url;
		$data['CANCELURL'] = $this->cancel_url;

		$values = $this->get_response('SetExpressCheckout',$data);
		if( isset($values['ACK']) ){
			if( $values['ACK'] == 'Success' and isset($values['TOKEN']) ){
				return $values['TOKEN'];
			}
		}
		return false;
	}

	public function charge_amount($planID, $amount, $itemname, $frequency)
	{

		global $sgsh_current_user, $wpdb;
		$freq_name =$this->get_frequency_code($frequency).'ly';
		$data['PAYMENTREQUEST_0_PAYMENTACTION'] 	='Sale';
		$data['PAYERID'] 	= $_GET['PayerID'];
		$data['TOKEN'] = $_GET['token'];
		$data['RETURNFMFDETAILS'] 	= 1;

		$data["PAYMENTREQUEST_0_AMT"] = $amount;
		$data["PAYMENTREQUEST_0_CURRENCYCODE"] = $this->currency;
		$data["PAYMENTREQUEST_0_ITEMAMT"] = $amount;
		$data["PAYMENTREQUEST_0_DESC"] = "Show House - Payment";

		$data['L_PAYMENTREQUEST_0_NAME0'] = "{$itemname}({$freq_name})";
		$data['L_PAYMENTREQUEST_0_AMT0'] 	= $amount;
		$data['L_PAYMENTREQUEST_0_QTY0'] = 1;
		$data['L_PAYMENTREQUEST_0_NUMBER0'] = $sgsh_current_user->get_current_user_id().'-'. $planID;

		$values = $this->get_response('DoExpressCheckoutPayment',$data);

		if( isset($values['ACK']) and isset($values['PAYMENTINFO_0_PAYMENTSTATUS']) ){
			if( $values['ACK'] == 'Success' and $values['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed'){
				return true;
			}
		}
		return false;

	}

	public function create_recurring_payments($planID, $amount, $payer_name ,$itemname, $frequency)
	{
		global $sgsh_current_user, $wpdb;

		$freq_name =$this->get_frequency_code($frequency).'ly';
		$data['TOKEN'] = $_GET['token'];
		$data["SUBSCRIBERNAME"] = $payer_name;
		$data['PROFILESTARTDATE'] 	= $this->get_next_payment($frequency);
		$data["PROFILEREFERENCE"] = $sgsh_current_user->get_current_user_id();
		$data["MAXFAILEDPAYMENTS"] = "3";
		$data["AUTOBILLOUTAMT"] = "AddToNextBilling";
		$data["BILLINGPERIOD"] = $this->get_frequency_code($frequency);
		$data["BILLINGFREQUENCY"] = "1";
		$data["TOTALBILLINGCYCLES"] = "0";
		$data["AMT"] = $amount;
		$data["DESC"] = "Show House - {$itemname}({$freq_name}) - {$this->currency} ". number_format($amount,2);;
		$data["CURRENCYCODE"] = $this->currency;

		$data["PAYMENTREQUEST_0_AMT"] = $amount;
		$data["PAYMENTREQUEST_0_CURRENCYCODE"] = $this->currency;
		$data["PAYMENTREQUEST_0_ITEMAMT"] = $amount;
		$data["PAYMENTREQUEST_0_DESC"] = "Show House - Payment";

		$data['L_PAYMENTREQUEST_0_NAME0'] = "{$itemname}({$freq_name})";
		$data['L_PAYMENTREQUEST_0_AMT0'] 	= $amount;
		$data['L_PAYMENTREQUEST_0_QTY0'] = 1;
		$data['L_PAYMENTREQUEST_0_NUMBER0'] = $sgsh_current_user->get_current_user_id().'-'. $planID;


 		$values = $this->get_response('CreateRecurringPaymentsProfile',$data);

		if( isset($values['ACK']) ){
			if( $values['ACK'] == 'Success'){
				$user_id =  $sgsh_current_user->get_current_user_id();
				$user_details = $sgsh_current_user->get_agent_detail();
				$plan = $wpdb->get_row("SELECT * FROM sgsh_credit_plan WHERE credit_plan_id = '". $planID  ."'  ");
				$pack = $wpdb->get_row("SELECT * FROM sgsh_credit_package WHERE credit_pack_id = '". $plan->credit_pack_id  ."'  ");
				$user_plan = $wpdb->get_row("SELECT * FROM sgsh_credit_agent WHERE sg_user_id = {$user_id} ");

				$start_date =  date('Y-m-d H:i:s');
				$end_date =  '0000-00-00 00:00:00';

				if( $user_plan == null ){
					$wpdb->insert('sgsh_credit_agent',array(
						'sg_user_id' 	=> $user_id,
						'total_credit' 	=> $pack->credit_pack_credit + $plan->credit_plan_bonus,
						'plan_start_date' 	=> $start_date,
						'plan_end_date' 	=> $end_date,
						'total_avai_day' 	=> 0,
						'cpack_name' 			=> $pack->credit_pack_name,
						'cpack_credit' 			=> $pack->cpack_credit,
						'credit_plan_f_email' 	=> $pack->credit_pack_f_email,
						'credit_plan_f_sms' 	=> $pack->credit_pack_f_sms,
						'credit_plan_f_report' 	=> $pack->credit_pack_f_report,
						'credit_pack_id' 		=> $plan->credit_pack_id,
						'credit_plan_id' 		=> $planID,
						'credit_agent_modified_date'  => date('Y-m-d H:i:s'),
						'paypal_profile_id' 	=>  isset($values['PROFILEID'])?$values['PROFILEID']:'',
						'credit_plan_recur_type' => $plan->credit_plan_recur_type,
						'credit_plan_status' 	=>  CREDIT_STATUS_ACTIVE,
						'plan_availment_data' 	=>  $user_details->plan_availment_data,
					));
				}else{

					$wpdb->update('sgsh_credit_agent',
						array(
							'total_credit' 		=> $pack->credit_pack_credit + $plan->credit_plan_bonus,
							'plan_start_date' 	=> $start_date,
							'plan_end_date' 	=> $end_date,
							'total_avai_day' 	=> 0,
							'credit_plan_f_email' 	=> $pack->credit_pack_f_email,
							'credit_plan_f_sms' 	=> $pack->credit_pack_f_sms,
							'credit_plan_f_report' 	=> $pack->credit_pack_f_report,
							'credit_pack_id' 		=> $plan->credit_pack_id,
							'credit_plan_id' 		=> $planID,
							'credit_agent_modified_date' 	=> date('Y-m-d H:i:s'),
							'paypal_profile_id' 	=>  isset($values['PROFILEID'])?$values['PROFILEID']:'',
							'credit_plan_status' 	=>  CREDIT_STATUS_ACTIVE,
						),
						array(
							'sg_user_id' 	=> $user_id
						)
					);

				}

				return true;
			}
		}
		return false;
	}

	public function get_express_checkout_details($token)
	{
		$data['TOKEN'] = $token;
		return $this->get_response('GetExpressCheckoutDetails',$data);
	}

	public function cancel_subscription($profile_id)
	{

		$data['PROFILEID'] 	=  $profile_id;
		$data['ACTION'] 	= 'Cancel';
		$values = $this->get_response('ManageRecurringPaymentsProfileStatus',$data);
		if( isset($values['ACK']) ){
			if( $values['ACK'] == 'Success' ){
				return true;
			}
		}
		return false;

	}

	public function upgrade_subscription($profile_id,$amount)
	{
		$data['USER'] 		= $this->api_un;
		$data['PWD'] 		= $this->api_pw;
		$data['SIGNATURE'] 	= $this->api_sig;
		$data['PROFILEID'] 	= $profile_id;
		$data['AMT'] 		= $amount;
		$values = $this->get_response('UpdateRecurringPaymentsProfile',$data);
		if( isset($values['ACK']) ){
			if( $values['ACK'] == 'Success' ){
				return true;
			}
		}
		return false;
	}

}
?>
