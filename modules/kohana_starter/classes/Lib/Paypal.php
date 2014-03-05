<?php

class Lib_Paypal{

    const STATUS_PENDING = 0;
    const STATUS_FAILED = 1;
    const STATUS_SUCCESS = 2;

	public $paypal_email;
	public $paypal_url;
	public $paypal_api_url;

	public $currency;
	public $notify_url;
	public $return_url;
	public $cancel_url;

	public $api_un;
	public $api_pw;
	public $api_sig;
	public $api_ver;

	public $token;

	public $instance;

	public $response;
	public $profile_id;

	public $token_details;

	public function instance()
	{
		if( self::$instance == null ){
			self::$instance = new  self();
		}
		return self::$instance;
	}

	public function __construct(){

		// $this->currency = 'SGD';
		// $this->sandbox = true;

		// if($this->sandbox){

		// 	//sandbox
		// 	$this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		// 	// $this->paypal_email = 'G725YRJS2AW92';
		// 	$this->paypal_email = 'manalastas_ryan4@yahoo.com';
		// 		$this->api_un = 'manalastas_ryan4_api1.yahoo.com';
		// 		$this->api_pw = '1375077669';
		// 		$this->api_sig = 'Ahm5F0260Lu30tbpDM64QZtSFJWkAWC3jinNbqkLRM0-ybetGE0eSGkZ';
		// }else{
		// 	$this->api_un = ' ';
		// 		$this->api_pw = ' ';
		// 		$this->api_sig = ' ';
		// 	$this->paypal_url = "https://www.paypal.com/cgi-bin/webscr";
		// 	$this->paypal_email = 'manalastas_ryan4@yahoo.com';
		// }

		// if($this->sandbox){
		// 	$this->paypal_api_url = 'https://api-3t.sandbox.paypal.com/nvp';
		// }else{
		// 	$this->paypal_api_url = 'https://api-3t.paypal.com/nvp';
		// }

 		$this->api_ver = '84';
		//$this->notify_url = SGSH_PLUGIN_URL.'/standard_ipn.php';
		$this->return_url =  '';
		$this->cancel_url = '';

	}

	protected function preparePaymentUrl($parameters){
        reset($parameters);
		$i=0;
		$url = $this->paypal_url;
		while (list($key, $value) = each($parameters)) {
			$i++;
			$url .= ($i==1)? '?':'&';
			$url .= '&'.$key.'='.urlencode($value);
        }
		return $url;
	}
	public function getFrequencyCode($id)
	{
		if( $id == 'Y' ){
			return 'Year';
		}elseif( $id == 'M' ){
			return 'Month';
		}
	}

	public function getNextPayment($id)
	{
		if( $id == 'Y' ){
			$days = 365;
		}elseif( $id == 'M' ){
			$days = 30;
		}
		return  date('Y-m-d',time()+(60*60*24*$days))  .'T'.  gmdate('G:i:s',time()+(60*60*24*$days)) . 'Z';
	}

	public function getReponseArray($res){
		$ret = array();
		parse_str($res,$ret);
		return $ret;
	}

	public function getResponse($method,$data)
	{

		$url = '';
		$values = array();


		$def_data['USER'] 		= $this->api_un;
		$def_data['PWD'] 		= $this->api_pw;
		$def_data['SIGNATURE'] 	= $this->api_sig;
		$def_data['VERSION'] 	= $this->api_ver;
		$data = $def_data + $data;

		$postdata = array('METHOD'=>$method)+$data;

		$values                  = $this->getContent_Curl($postdata);
		if( $values != null ){

			$this->response          = $values;
			$this->profile_id        = isset($values['PROFILEID'])?$values['PROFILEID']:'';
			$history                 = ORM::factory('PaypalHistory');
			$history->date_created   = date('Y-m-d H:i:s');
			$history->txn_type       = $method;
			$history->user_id        = Lib_App::user()->getID();
			$history->subscr_id      = $this->profile_id;
			$history->txn_id         = isset($values['PAYMENTINFO_0_TRANSACTIONID'])?$values['PAYMENTINFO_0_TRANSACTIONID']:'';
			$history->payment_status = isset($values['PAYMENTINFO_0_PAYMENTSTATUS'])?$values['PAYMENTINFO_0_PAYMENTSTATUS']:'';
			$history->token          = isset($data['TOKEN'])?$data['TOKEN']:'';
			$history->post_data      = serialize($values);
			$history->request_data   = serialize($data+array('paypal_api_url'=>$this->paypal_api_url,'paypal_url'=>$this->paypal_url));
			$history->save();

		}
		return $values;

	}

	private function getContent_Curl($postdata){

		$data_array = array();
		foreach ($postdata as $key => $value) {
			$data_array[] = $key .'='.$value;
		}
		$data = implode('&', $data_array);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->paypal_api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          "Content-type: application/x-www-form-urlencoded",
          "Content-length: ".strlen($data))
        );

        try {
            $result = curl_exec($ch);
            if (FALSE === $result)  throw new Exception(curl_error($ch), curl_errno($ch));
            curl_close($ch);
            return $this->getReponseArray($result);
        } catch(Exception $e) {

            trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);

        }
        return null;

	}

	private function getContent_FGC($postdata){
		$postdata = http_build_query($postdata);

		$opts = array('https' =>
		    array(
		        'method'  => 'POST',
		        'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
		        			  "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8\r\n".
		        			  "Accept-Encoding: gzip,deflate,sdch\r\n".
		        			  "Cache-Control: max-age=0\r\n".
		        			  "Connection: keep-alive\r\n".
		        			  "Host: ".$this->paypal_api_url."\r\n".
		        			  "Origin: http://local.ideabank.com\r\n".
		        			  "User-Agent: Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.3\r\n".
		        			  'Accept-Language: en-US,en;q=0.8',
		        'content' => $postdata
		    )
		);

		$context                 = stream_context_create($opts);
		$result                  = file_get_contents($this->paypal_api_url,  false, $context);
		return $this->getReponseArray($result);
	}

	public function getCheckoutUlr()
	{
		return $this->paypal_url .'?cmd=_express-checkout&token='. urlencode($this->token);
	}

	public function setExpressCheckout($options)
	{

		$default = array(
				'item_id' =>'',
				'description' =>'',
				'itemname' =>'',
				'amount' => 0,
				'subcr_amount' => 0,
				'frequency' =>'',
				'is_recurring' => false,
				'custom' => null,
			);

		$options = $options+$default;

		$data['PAYMENTREQUEST_0_PAYMENTACTION'] 	=  'Sale';
		if( $options['is_recurring'] ){
			$data['L_BILLINGTYPE0'] = "RecurringPayments";
		}
		$data['L_BILLINGAGREEMENTDESCRIPTION0'] = "Idea Bank - ". $options['description'] ." {$this->currency} ". number_format($options['amount'],2);
		$data['L_PAYMENTTYPE0'] = "Any";

		$data['PAYMENTREQUEST_0_AMT'] = $options['amount'];
		$data["PAYMENTREQUEST_0_CURRENCYCODE"] = $this->currency;
		$data["PAYMENTREQUEST_0_ITEMAMT"] = $options['amount'];
		$data["PAYMENTREQUEST_0_DESC"] = "Idea Bank - Payment";

		$data['L_PAYMENTREQUEST_0_NAME0'] = $options['itemname'];
		$data['L_PAYMENTREQUEST_0_AMT0']   = $options['amount'];
		// $data['L_PAYMENTREQUEST_0_QTY0'] = 1;
		$data['L_PAYMENTREQUEST_0_NUMBER0'] = $options['item_id'];// $sgsh_current_user->user_id().'-'. $options['planID'];

		$data['RETURNURL'] = $this->return_url;
		$data['CANCELURL'] = $this->cancel_url;

		$values = $this->getResponse('SetExpressCheckout',$data);

		if( isset($values['ACK']) ){
			if( $values['ACK'] == 'Success' and isset($values['TOKEN']) ){
				$this->token = $values['TOKEN'];
				return $values['TOKEN'];
			}
		}
		return false;
	}

	public function chargeAmount()
	{
		$this->token_details = $details  = $this->getExpressCheckoutDetails($_GET['token']);
		if( isset($details['ACK']) and $details['ACK'] == 'Success' ){

			$data['PAYMENTREQUEST_0_PAYMENTACTION'] ='Sale';
			$data['PAYERID']                        = $_GET['PayerID'];
			$data['TOKEN']                          = $_GET['token'];
			$data['RETURNFMFDETAILS']               = 1;

			$data["PAYMENTREQUEST_0_AMT"]           = $details['PAYMENTREQUEST_0_AMT'];
			$data["PAYMENTREQUEST_0_CURRENCYCODE"]  = $this->currency;
			$data["PAYMENTREQUEST_0_ITEMAMT"]       = $details['PAYMENTREQUEST_0_ITEMAMT'];
			$data["PAYMENTREQUEST_0_DESC"]          = $details['PAYMENTREQUEST_0_DESC'];

			$data['L_PAYMENTREQUEST_0_NAME0']       = $details['L_PAYMENTREQUEST_0_NAME0'];
			$data['L_PAYMENTREQUEST_0_AMT0']        = $details['L_PAYMENTREQUEST_0_AMT0'];
			$data['L_PAYMENTREQUEST_0_QTY0']        = $details['L_PAYMENTREQUEST_0_QTY0'];

			$values = $this->getResponse('DoExpressCheckoutPayment',$data);
			if( isset($values['ACK']) and isset($values['PAYMENTINFO_0_PAYMENTSTATUS']) ){
				if( $values['ACK'] == 'Success' and $values['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed'){
					return true;
				}
			}
		}
		return false;

	}

	// public function chargeAmount( $amount, $itemname, $frequency)
	// {

	// 	$data['PAYMENTREQUEST_0_PAYMENTACTION'] ='Sale';
	// 	$data['PAYERID']                        = $_GET['PayerID'];
	// 	$data['TOKEN']                          = $_GET['token'];
	// 	$data['RETURNFMFDETAILS']               = 1;

	// 	$data["PAYMENTREQUEST_0_AMT"]           = $amount;
	// 	$data["PAYMENTREQUEST_0_CURRENCYCODE"]  = $this->currency;
	// 	$data["PAYMENTREQUEST_0_ITEMAMT"]       = $amount;
	// 	$data["PAYMENTREQUEST_0_DESC"]          = "Idea Bank - Payment";

	// 	$data['L_PAYMENTREQUEST_0_NAME0']       = $itemname;
	// 	$data['L_PAYMENTREQUEST_0_AMT0']        = $amount;
	// 	$data['L_PAYMENTREQUEST_0_QTY0']        = 1;
	// 	// $data['L_PAYMENTREQUEST_0_NUMBER0']  = '';//$sgsh_current_user->user_id().'-'. $planID;

	// 	$values = $this->getResponse('DoExpressCheckoutPayment',$data);
	// 	if( isset($values['ACK']) and isset($values['PAYMENTINFO_0_PAYMENTSTATUS']) ){
	// 		if( $values['ACK'] == 'Success' and $values['PAYMENTINFO_0_PAYMENTSTATUS'] == 'Completed'){
	// 			return true;
	// 		}
	// 	}
	// 	return false;

	// }

	public function createRecurringPayments($token, $amount, $payer_name , $itemname, $frequency)
	{

		$freq_name =$this->getFrequencyCode($frequency).'ly';
		$data['TOKEN'] 			= $token;
		$data["SUBSCRIBERNAME"] = $payer_name;
		$data['PROFILESTARTDATE'] 	= $this->getNextPayment($frequency);
		$data["PROFILEREFERENCE"] = 0;//$sgsh_current_user->user_id();
		$data["MAXFAILEDPAYMENTS"] = "3";
		$data["AUTOBILLOUTAMT"] = "AddToNextBilling";
		$data["BILLINGPERIOD"] = $this->getFrequencyCode($frequency);
		$data["BILLINGFREQUENCY"] = "1";
		$data["TOTALBILLINGCYCLES"] = "0";
		$data["AMT"] = $amount;
		$data["DESC"] = "Nest Finance - {$itemname}({$freq_name}) - {$this->currency} ". number_format($amount,2);;
		$data["CURRENCYCODE"] = $this->currency;

		$data["PAYMENTREQUEST_0_AMT"] = $amount;
		$data["PAYMENTREQUEST_0_CURRENCYCODE"] = $this->currency;
		$data["PAYMENTREQUEST_0_ITEMAMT"] = $amount;
		$data["PAYMENTREQUEST_0_DESC"] = "Nest Finance - Payment";

		$data['L_PAYMENTREQUEST_0_NAME0'] = "{$itemname}({$freq_name})";
		$data['L_PAYMENTREQUEST_0_AMT0'] 	= $amount;
		$data['L_PAYMENTREQUEST_0_QTY0'] = 1;
		// $data['L_PAYMENTREQUEST_0_NUMBER0'] = 0;

 		$values = $this->getResponse('CreateRecurringPaymentsProfile',$data);
		if( isset($values['ACK']) ){
			if( $values['ACK'] == 'Success'){
				return true;
			}
		}
		return false;
	}

	public function getProfileDetails($profile_id)
	{
		$data['PROFILEID'] = $profile_id;
		return $this->getResponse('GetRecurringPaymentsProfileDetails',$data);
	}

	public function getExpressCheckoutDetails($token)
	{
		$data['TOKEN'] = $token;
		return $this->getResponse('GetExpressCheckoutDetails',$data);
	}

	public function cancelSubscription($profile_id)
	{

		$data['PROFILEID'] 	=  $profile_id;
		$data['ACTION'] 	= 'Cancel';
		$values = $this->getResponse('ManageRecurringPaymentsProfileStatus',$data);
		if( isset($values['ACK']) ){
			if( $values['ACK'] == 'Success' ){
				return true;
			}
		}
		return false;

	}

	public function upgradeSubscription($planID, $profile_id,$amount, $itemname, $frequency)
	{
		$freq_name =$this->getFrequencyCode($frequency).'ly';
		$data['USER'] 		= $this->api_un;
		$data['PWD'] 		= $this->api_pw;
		$data['SIGNATURE'] 	= $this->api_sig;
		$data['PROFILEID'] 	= $profile_id;
		$data['AMT'] 		= $amount;
		$data["DESC"] = "Nest Finance - {$itemname}({$freq_name}) - {$this->currency} ". number_format($amount,2);;
		$data["CURRENCYCODE"] = $this->currency;
		$values = $this->getResponse('UpdateRecurringPaymentsProfile',$data);

		if( isset($values['ACK']) ){
			if( $values['ACK'] == 'Success' ){
				return true;
			}
		}
		return false;
	}

}
?>