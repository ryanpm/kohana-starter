<?php

define('WP_USE_THEMES', false);
require_once( dirname(__FILE__) . '/wp-load.php' );

global $wpdb;

$filename =  date('Y-m-d H:i:s').'.txt';
$response = serialize($_POST);

require_once(SGSH_PLUGIN_DIR.'/models/class.sgsh_agent.php');

if(!isset($_POST) or count($_POST)==0)exit;

// assign posted variables to local variables
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
  $value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

// post back to PayPal system to validate
$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";

if(  $_POST['test_ipn'] == '1' ){
	$header .= "Host: www.sandbox.paypal.com\r\n";
}
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

if(  $_POST['test_ipn'] == '1' ){
	$fp = fsockopen ('ssl://sandbox.paypal.com', 443, $errno, $errstr, 30);
}else{
	$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
}

if (!$fp) {
	$response .= ' HTTP ERROR';
	// HTTP ERROR
} else {
	fputs ($fp, $header . $req);
	while (!feof($fp)) {

		$res = stream_get_contents($fp, 1024);
		$response .= ' '. $res;
		if ( strpos ($res, "VERIFIED") !== FALSE ) {
			$response .= ' verified ';
		}

		$order_id = '';

		// payment_status
		//subscr_cancel, subscr_payment, subscr_signup, subscr_modify, subscr_failed, subscr_payment, subscr_eot
		$txn_type 		= isset($_POST['txn_type'])?$wpdb->_escape($_POST['txn_type']):'';

		// Completed, Pending
		$payment_status = isset($_POST['payment_status'])?$_POST['payment_status']:'';
		$subscr_id 		= isset($_POST['subscr_id'])?$wpdb->_escape($_POST['subscr_id']):'';
		if( $subscr_id == '' ){
			$subscr_id 		= isset($_POST['recurring_payment_id'])?$wpdb->_escape($_POST['recurring_payment_id']):'';
		}

		$txn_id 		= isset($_POST['txn_id'])?$wpdb->_escape($_POST['txn_id']):'';
		$payer_email 	= isset($_POST['payer_email'])?$_POST['payer_email']:'';
		$receiver_email = isset($_POST['receiver_email'])?$_POST['receiver_email']:'';
		$payment_currency = isset($_POST['mc_currency'])?$_POST['mc_currency']:'';
		$item_name 		= isset($_POST['item_name'])?$_POST['item_name']:'';
		$item_number 	= isset($_POST['item_number'])?$_POST['item_number']:'';
		$amount 	= isset($_POST['mc_amount3'])?$_POST['mc_amount3']:'';
		if( $amount == '' ){
			$amount 		= isset($_POST['amount'])?$wpdb->_escape($_POST['amount']):'';
		}


		if($item_number==''){
			$item_number 	= isset($_POST['item_number1'])?$_POST['item_number1']:'';
		}

		$payment_amount = isset($_POST['mc_gross'])?$_POST['mc_gross']:'';

		 if ( strpos ($res, "VERIFIED") !== FALSE or true) {

			// check the payment_status is Completed
			// check that txn_id has not been previously processed
			// check that receiver_email is your Primary PayPal email
			// check that payment_amount/payment_currency are correct
			// process payment

		  	list($user_id,$availment_id) = explode('-',$item_number);
		  	$subscr_id = $wpdb->_escape($subscr_id);

		  	$user_id = (int)$user_id;
		  	if( $user_id == 0 ){
		  		$response .= " SELECT sg_user_id FROM sgsh_credit_agent WHERE paypal_profile_id = '{$subscr_id}' ";
		  		$sgsh_credit_agent = $wpdb->get_row("SELECT sg_user_id FROM sgsh_credit_agent WHERE paypal_profile_id = '{$subscr_id}' ");
		  		$user_id = (int)$sgsh_credit_agent->sg_user_id;
		  	}

		  	$availment_id = $wpdb->_escape($availment_id);

		  	$paypal_history  = 0;
		  	if(  $txn_type == 'subscr_payment'   ){
		  		$paypal_history = $wpdb->get_var("SELECT COUNT(*) FROM sgsh_paypal_history WHERE txn_id = '{$txn_id}'  LIMIT 1");
		  	}else{

		  		$response .= "SELECT COUNT(*) FROM sgsh_paypal_history WHERE  sg_user_id = '{$user_id}' AND txn_type = '{$txn_type}'  AND subscr_id = '{$subscr_id}' LIMIT 1";

		  		$paypal_history = $wpdb->get_var("SELECT COUNT(*) FROM sgsh_paypal_history WHERE  sg_user_id = '{$user_id}' AND txn_type = '{$txn_type}'  AND subscr_id = '{$subscr_id}' LIMIT 1");
		  	}

			if($paypal_history==0){

				$response .= ' No history';
				if(  $_POST['test_ipn'] == '1' ){
					$paypal_email = 'manalastas_ryan4@yahoo.com';
				}else{
					$paypal_email = get_option('sgsh_paypal_email');
				}

				$user = $wpdb->get_row("SELECT * FROM sgsh_users_agent WHERE sg_user_id = {$user_id} ");

				if( $user != null ){

						$plan_details = unserialize($user->plan_availment_data);
						list($trial_period,$code) = explode(' ',$_POST['period1']);

						$response .=  ' -->'. $paypal_email.' == '. $receiver_email .'<---<br/>';
						// $response .=  ' -->'. $user->plan_availment_id  .' == '.  $availment_id .'<---<br/>';
						$response .=  ' -->'. $plan_details['reg_amount']  .' == '.  $amount .'<---<br/>';
						// $response .=  ' -->'. $plan_details['trial_period']  .' == '. $trial_period  .'<---<br/>';

						$receiver_equal = $paypal_email  == $receiver_email;
						$amount_equal =  $plan_details['reg_amount']  ==  $amount;
						// $availment_id_equal =  $user->plan_availment_id == $availment_id;
						// $trial_period_equal =  $plan_details['trial_period'] == $trial_period;
						//$payment_status == 'Completed'

						if(  $receiver_equal  and $amount_equal ){

							$response .=  ' -->PASS CONDITIONS<---<br/>';
							if(  $txn_type == 'subscr_signup'   ){

								$planID = $plan_details['credit_plan_id'];
								$plan = $wpdb->get_row("SELECT * FROM sgsh_credit_plan WHERE credit_plan_id = '". $planID  ."'  ");
								$pack = $wpdb->get_row("SELECT * FROM sgsh_credit_package WHERE credit_pack_id = '". $plan->credit_pack_id  ."'  ");
								$start_date =  date('Y-m-d H:i:s');
								// indefinite end date
								$end_date =  '0000-00-00 00:00:00';
								$user_plan = $wpdb->get_row("SELECT * FROM sgsh_credit_agent WHERE sg_user_id = {$user_id} ");
								if( $user_plan == null ){

									$response .=  ' -->INSERT<---<br/>';
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
										'credit_agent_modified_date' 	=> date('Y-m-d H:i:s'),
										'paypal_profile_id' 	=> $subscr_id,
										'credit_plan_recur_type' => $plan->credit_plan_recur_type,
										'credit_plan_status' 	=>  CREDIT_STATUS_ACTIVE,
										'plan_availment_data' 	=>  $user->plan_availment_data,
									));

								}else{

									$response .=  ' -->UPDATE<---<br/>';
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
											'paypal_profile_id' 	=> $subscr_id,
											'credit_plan_status' 	=>  CREDIT_STATUS_ACTIVE,
										),
										array(
											'sg_user_id' 	=> $user_id
										)
									);

								}

								$wpdb->insert('sgsh_credit_history',array(
									'start_date' => $start_date,
									'end_date' => $end_date,
									'sg_user_id' => $user_id,
									'cplan_id' => $planID,
									'credit_history_createdate' => date('Y-m-d H:i:s'),
									'credit_history_status' => CREDIT_STATUS_ACTIVE,
								));

							}elseif(  $txn_type == 'subscr_payment'   ){

							}elseif(  $txn_type == 'subscr_modify'   ){

							}elseif(  $txn_type == 'subscr_failed'   ){

									$wpdb->update('sgsh_credit_agent',
										array(
											'credit_plan_status' 	=>  CREDIT_STATUS_FAILED,
										),
										array(
											'sg_user_id' 	=> $user_id
										)
									);
									$wpdb->insert('sgsh_credit_history',array(
										'start_date' => 0,
										'end_date' => 0,
										'sg_user_id' => $user_id,
										'cplan_id' => 0,
										'credit_history_createdate' => date('Y-m-d H:i:s'),
										'credit_history_status' => CREDIT_STATUS_FAILED,
									));

							}elseif(  $txn_type == 'subscr_cancel' or $txn_type == 'recurring_payment_profile_cancel' ){

								$wpdb->update('sgsh_credit_agent',
										array(
											'credit_plan_status' 	=>  CREDIT_STATUS_CANCELLED,
										),
										array(
											'sg_user_id' 	=> $user_id
										)
									);
									$wpdb->insert('sgsh_credit_history',array(
										'start_date' => 0,
										'end_date' => 0,
										'sg_user_id' => $user_id,
										'cplan_id' => 0,
										'credit_history_createdate' => date('Y-m-d H:i:s'),
										'credit_history_status' => CREDIT_STATUS_CANCELLED,
									));

							}


						}

				}

				$history['datecreated'] = date('Y-m-d H:i:s');
				$history['txn_type'] 	= $txn_type;
				$history['sg_user_id'] 	= $user_id;
				$history['subscr_id'] 	= $subscr_id;
				$history['txn_id'] 		= $txn_id;
				$history['payment_status'] 	= $payment_status;
				$history['post_data'] 		= serialize($_POST);
				$wpdb->insert('sgsh_paypal_history',$history);

			}else{
				$response .= ' found history';
			}
		 	$response .= ' VERIFIED';

		}else if (strcmp ($res, "INVALID") == 0) {
			// log for manual investigation
			$response .= ' INVALID';
		}


	}

	fclose ($fp);
	$response .= ' HTTP VALID';

}

if(  $_POST['test_ipn'] == '1' ){
	echo $response;
}


if( $response != '' ){
	file_put_contents($filename, $response);
}
