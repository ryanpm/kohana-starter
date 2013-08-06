<?

echo gmdate('M d Y H:i:s');
echo date('M d Y H:i:s');
echo (isset($_GET['date']))?urldecode($_GET['date']):'';

$api_un = 'manalastas_ryan4_api1.yahoo.com';
$api_pw = '1375077669';
$api_sig = 'Ahm5F0260Lu30tbpDM64QZtSFJWkAWC3jinNbqkLRM0-ybetGE0eSGkZ';

if( isset($_GET['token']) ){


        // $data['USER']       = $api_un;
        // $data['PWD']        = $api_pw;
        // $data['SIGNATURE']  = $api_sig;
        // $data['TOKEN']        = $_GET['token'];
        // $data['VERSION']    = '';
        // $req = 'METHOD=GetExpressCheckoutDetails';
        // foreach ($data as $key => $value) {
        //     $value = urlencode(stripslashes($value));
        //     $req .= "&$key=$value";
        // }

        // $header = "POST /nvp HTTP/1.0\r\n";
        // $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        // $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
        // $fp = fsockopen ('ssl://api-3t.sandbox.paypal.com', 443, $errno, $errstr, 30);
        // if ($fp) {
        //     fputs ($fp, $header . $req);
        //     while (!feof($fp)) {
        //         $res = stream_get_contents($fp, 1024);
        //         $response_pair = explode('&',$res);
        //         foreach ($response_pair as $pair) {
        //             $values = explode('=',$pair);
        //             print_r($values);
        //             if( isset($values[0]) and isset($values[1])   ){
        //                 if( strtolower($values[0]) == 'ack' and  strtolower($values[1])  == 'success' ){
        //                 }
        //             }
        //         }
        //     }
        // }


//     $data['USER']       = $api_un;
//     $data['PWD']        = $api_pw;
//     $data['SIGNATURE']  = $api_sig;
//     $data['TOKEN']        = $_GET['token'];
//     $data['VERSION']    = '98.0';

//     $data["PROFILESTARTDATE"]  = gmdate('Y-d-mTG:i:sz');

//     $data["BILLINGPERIOD"]  = "Month";
//     $data["BILLINGFREQUENCY"]  = "2";
//     $data["TOTALBILLINGCYCLES"]  = "0";
//     $data["AMT"]  = "30";

//     $data["TRIALBILLINGPERIOD"]  = "Day";
//     $data["TRIALBILLINGFREQUENCY"]  = "2";
//     $data["TRIALTOTALBILLINGCYCLES"]  = "1";
//     $data["TRIALAMT"]  = "15";

//     $data["CURRENCYCODE"]  = "SGD";

// print_r($data);
//         $req = 'METHOD=CreateRecurringPaymentsProfile';
//         foreach ($data as $key => $value) {
//             $value = urlencode(stripslashes($value));
//             $req .= "&$key=$value";
//         }

//         $header = "POST /nvp HTTP/1.0\r\n";
//         $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
//         $header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
//         $fp = fsockopen ('ssl://api-3t.sandbox.paypal.com', 443, $errno, $errstr, 30);
//         if ($fp) {
//             fputs ($fp, $header . $req);
//             while (!feof($fp)) {
//                 $res = stream_get_contents($fp, 1024);
//                 $response_pair = explode('&',$res);
//                 foreach ($response_pair as $pair) {
//                     $values = explode('=',$pair);
//                     print_r($values);
//                     if( isset($values[0]) and isset($values[1])   ){
//                         if( strtolower($values[0]) == 'ack' and  strtolower($values[1])  == 'success' ){
//                         }
//                     }
//                 }
//             }
//         }

       // exit;

}

?>



<form method="post" action="paypal_ipn.php" >

    <input type="text" name="txn_type" value="subscr_signup" /><br/>
    <input type="text" name="subscr_id" value="I-HP4N3LJ2CN07" /><br/>
    <input type="text" name="last_name" value="Manalastas" /><br/>
    <input type="text" name="residence_country" value="US" /><br/>
    <input type="text" name="mc_currency" value="SGD" /><br/>
    <input type="text" name="item_name" value="SG Showhouse] Professional Package" /><br/>
    <input type="text" name="business" value="manalastas_ryan4@yahoo.com" /><br/>
    <input type="text" name="recurring" value="1" /><br/>
    <input type="text" name="verify_sign" value="AGtgW1catmzXXvaWQ.-uMCiHlxKFAkAuXv4kzs1UWHQild1CksWy.Zus" /><br/>
    <input type="text" name="payer_status" value="verified" /><br/>
    <input type="text" name="test_ipn" value="1" /><br/>
    <input type="text" name="payer_email" value="manalastas_ryan2@yahoo.com" /><br/>
    <input type="text" name="first_name" value="Ryan" /><br/>
    <input type="text" name="receiver_email" value="manalastas_ryan4@yahoo.com" /><br/>
    <input type="text" name="payer_id" value="VQPN74W52FK9G" /><br/>
    <input type="text" name="reattempt" value="1" /><br/>
    <input type="text" name="item_number" value="9-C13A423177" /><br/>
    <input type="text" name="subscr_date" value="21:46:19 Jul 29, 2013 PDT" /><br/>
    <input type="text" name="charset" value="windows-1252" /><br/>
    <input type="text" name="notify_version" value="3.7" /><br/>
    <input type="text" name="period1" value="2 D" /><br/>
    <input type="text" name="mc_amount1" value="0.00" /><br/>
    <input type="text" name="period3" value="1 Y" /><br/>
    <input type="text" name="mc_amount3" value="360.00" /><br/>
    <input type="text" name="ipn_track_id" value="1d094f0fa93f" /><br/>
    <input type="submit"  name="submit" id="submit" value="Submit" />

</form>

<br/><br/>


<form method=post action="https://api-3t.sandbox.paypal.com/nvp">
    <input type="text" name="USER" value="<?= $api_un ?>"><br/>
    <input type="text" name="PWD" value="<?= $api_pw ?>"><br/>
    <input type="text" name="SIGNATURE" value="<?= $api_sig ?>"><br/>
    <input type="text" name="VERSION" value="84"><br/>

    <input name="PAYMENTACTION" value="Order " />
    <input name="NAME" value="J Smith " />
    <input name="SHIPTOSTREET" value="1 Main St " />
    <input name="SHIPTOCITY" value="San Jose " />
    <input name="SHIPTOSTATE" value="CA " />
    <input name="SHIPTOCOUNTRYCODE" value="US " />
    <input name="SHIPTOZIP" value="95131 " />

    <input name="L_NAME0" value="10% Decaf Kona Blend Coffee" />
    <input name="L_NUMBER0" value="623083" />
    <input name="L_DESC0" value="Size: 8.8-oz" />
    <input name="L_AMT0" value="9.95" />
    <input name="L_QTY0" value="2" />

    <input name="L_NAME1" value="Coffee Filter bags" />
    <input name="L_NUMBER1" value="623084" />
    <input name="L_DESC1" value="Size: Two 24-piece boxes" />
    <input name="L_AMT1" value="39.70" />
    <input name="L_QTY1" value="2" />

    <input name="ITEMAMT" value="99.30" />
    <input name="TAXAMT" value="2.58" />
    <input name="SHIPPINGAMT" value="3.00" />
    <input name="HANDLINGAMT" value="2.99" />
    <input name="SHIPDISCAMT" value="-3.00" />
    <input name="INSURANCEAMT" value="1.00" />
    <input name="AMT" value="105.87" />
    <input name="CURRENCYCODE" value="USD" />
    <input name="ALLOWNOTE" value="1" />

    <input type="text" name="RETURNURL" value="http://edifice2.edifice.com.sg/sgshowhouse/paypal_payment_test.php?s=return"><br/>
    <input type="text" name="CANCELURL" value="http://edifice2.edifice.com.sg/sgshowhouse/paypal_payment_test.php?s=cancel"><br/>
    <input type="submit" name="METHOD" value="SetExpressCheckout"><br/>
</form>

Create Express Checkout Token - Recurring Payment
<form method=post action="https://api-3t.sandbox.paypal.com/nvp">
    <input type="text" name="USER" value="<?= $api_un ?>"><br/>
    <input type="text" name="PWD" value="<?= $api_pw ?>"><br/>
    <input type="text" name="SIGNATURE" value="<?= $api_sig ?>"><br/>
    <input type="text" name="VERSION" value="84"><br/>

    <!-- <input type="text" name="CURRENCYCODE" value="SGD"><br/> -->
    <input type="text" name="PAYMENTREQUEST_0_PAYMENTACTION" value="Sale">(Authorization, Sale,Order)<br/>
    <input type="text" name="PAYMENTREQUEST_0_CURRENCYCODE" value="SGD"><br/>
<!--     <input type="text" name="PAYMENTACTION" value="Sale"><br/>
 -->
    <input name="L_BILLINGTYPE0" value="RecurringPayments" />
    <input name="L_BILLINGAGREEMENTDESCRIPTION0" value="Show House - Professional Package (Monthly)" />
    <input name="L_PAYMENTTYPE0" value="Any" />
    <input name="PAYMENTREQUEST_0_AMT" value="30" />

    <input type="text" name="RETURNURL" value="http://edifice2.edifice.com.sg/sgshowhouse/paypal_payment_test.php?s=return"><br/>
    <input type="text" name="CANCELURL" value="http://edifice2.edifice.com.sg/sgshowhouse/paypal_payment_test.php?s=cancel"><br/>
    <input type="submit" name="METHOD" value="SetExpressCheckout"><br/>
</form>

  <a href="https://www.sandbox.paypal.com/cgi-bin/webscr/?cmd=_express-checkout&token=<?= isset($_GET['token'])?urldecode($_GET['token']):'' ?>" > Go Checkout </a>
<form method=get action="https://www.sandbox.paypal.com/cgi-bin/webscr/">
    <input type="submit" name="" value="_express-checkout"><br/>
    <input type="text" name="TOKEN" value="<?= isset($_GET['token'])?urlencode($_GET['token']):'' ?>"><br/>
</form>

Get Details
<form method=post action="https://api-3t.sandbox.paypal.com/nvp">
    <input type="text" name="USER" value="<?= $api_un ?>"><br/>
    <input type="text" name="PWD" value="<?= $api_pw ?>"><br/>
    <input type="text" name="SIGNATURE" value="<?= $api_sig ?>"><br/>
    <input type="text" name="VERSION" value="98.0"><br/>
    <input type="text" name="TOKEN" value="<?= isset($_GET['token'])?urldecode($_GET['token']):'' ?>"><br/>
    <input type="submit" name="METHOD" value="GetExpressCheckoutDetails"><br/>
</form>

<br/><br/>
Cancel
<form method=post action="https://api-3t.sandbox.paypal.com/nvp">
    <input type="text" name="USER" value="<?= $api_un ?>"><br/>
    <input type="text" name="PWD" value="<?= $api_pw ?>"><br/>
    <input type="text" name="SIGNATURE" value="<?= $api_sig ?>"><br/>
    <input type="text" name="VERSION" value="98.0"><br/>
    <input type="text" name="PROFILEID" value="I-72NU5HMF4R34"><br/>
    <input type="text" name="ACTION" value="Cancel">
    <input type="submit" name="METHOD" value="ManageRecurringPaymentsProfileStatus"><br/>
</form>
Details
<form method=post action="https://api-3t.sandbox.paypal.com/nvp">
    <input type="text" name="USER" value="<?= $api_un ?>"><br/>
    <input type="text" name="PWD" value="<?= $api_pw ?>"><br/>
    <input type="text" name="SIGNATURE" value="<?= $api_sig ?>"><br/>
    <input type="text" name="VERSION" value="53.0"><br/>
    <input type="text" name="PROFILEID" value="I-72NU5HMF4R34"><br/>
    <input type="submit" name="METHOD" value="GetRecurringPaymentsProfileDetails"><br/>
</form>

Upgrade
<form method=post action="https://api-3t.sandbox.paypal.com/nvp">
    <input type="text" name="USER" value="<?= $api_un ?>"><br/>
    <input type="text" name="PWD" value="<?= $api_pw ?>"><br/>
    <input type="text" name="SIGNATURE" value="<?= $api_sig ?>"><br/>
    <input type="text" name="VERSION" value="54.0"><br/>
    <input type="text" name="AMT" value="50"><br/>
    <input type="text" name="PROFILEID" value="I-1AUPTUX73VE0"><br/>
    <input type="submit" name="METHOD" value="UpdateRecurringPaymentsProfile"><br/>
</form>

Do ExpressPayment
<form method=post action="https://api-3t.sandbox.paypal.com/nvp">
    <input type="text" name="USER" value="<?= $api_un ?>"><br/>
    <input type="text" name="PWD" value="<?= $api_pw ?>"><br/>
    <input type="text" name="SIGNATURE" value="<?= $api_sig ?>"><br/>
    <input type="text" name="VERSION" value="84"><br/>
    <input type="text" name="TOKEN" value="<?= isset($_GET['token'])?urlencode($_GET['token']):'' ?>"><br/>

    <input type="text" name="PAYMENTREQUEST_0_PAYMENTACTION" value="Sale"><br/>
   PAYERID: <input type="text" name="PAYERID" value=""><br/>
   <input type="text" name="RETURNFMFDETAILS" value="1"><br/>

    <input type="text" name="PAYMENTREQUEST_0_AMT" value="30"><br/>
    <input type="text" name="PAYMENTREQUEST_0_CURRENCYCODE" value="SGD"><br/>
    <input type="text" name="PAYMENTREQUEST_0_ITEMAMT" value="30"><br/>
    <input type="text" name="PAYMENTREQUEST_0_SHIPPINGAMT" value="0"><br/>
    <input type="text" name="PAYMENTREQUEST_0_DESC" value="Show House - Professional Package (Monthly)"><br/>

    <input type="text" name="L_PAYMENTREQUEST_0_NAME0" value="Show House - Professional Package (Monthly)"><br/>
    <input type="text" name="L_PAYMENTREQUEST_0_DESC0" value="Show House - Professional Package (Monthly)"><br/>
    <input type="text" name="L_PAYMENTREQUEST_0_AMT0" value="30"><br/>
    <input type="text" name="L_PAYMENTREQUEST_0_NUMBER0" value="123123"><br/>
    <input type="text" name="L_PAYMENTREQUEST_0_QTY0" value="1"><br/>

    <input type="submit" name="METHOD" value="DoExpressCheckoutPayment"><br/>
</form>

Create Recurring
<form method=post action="https://api-3t.sandbox.paypal.com/nvp">
    <input type="text" name="USER" value="<?= $api_un ?>"><br/>
    <input type="text" name="PWD" value="<?= $api_pw ?>"><br/>
    <input type="text" name="SIGNATURE" value="<?= $api_sig ?>"><br/>
    <input type="text" name="VERSION" value="84"><br/>

    <input type="text" name="SUBSCRIBERNAME" value="Ryan Manalastas"><br/>
    <input type="text" name="PROFILESTARTDATE" value="<?= gmdate('Y-d-m',time()+(60*60*24*3))  .'T'.  gmdate('G:i:s',time()+(60*60*24*3)) . 'Z' ?>"><br/>

    <input type="text" name="PROFILEREFERENCE" value="12312312"><br/>

    <input type="text" name="MAXFAILEDPAYMENTS" value="3"><br/>
    <input type="text" name="AUTOBILLOUTAMT" value="AddToNextBilling"><br/>

    <input type="text" name="BILLINGPERIOD" value="Month"><br/>
    <input type="text" name="BILLINGFREQUENCY" value="1"><br/>
    <input type="text" name="TOTALBILLINGCYCLES" value="0"><br/>

    <input type="text" name="AMT" value="30"><br/>
    <input type="text" name="CURRENCYCODE" value="SGD"><br/>

    <input name="DESC" value="Show House - Professional Package (Monthly)" />

    <input type="text" name="TOKEN" value="<?= isset($_GET['token'])?urlencode($_GET['token']):'' ?>"><br/>
    <input type="submit" name="METHOD" value="CreateRecurringPaymentsProfile"><br/>
</form>
