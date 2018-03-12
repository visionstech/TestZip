<?php
namespace App\Helpers;
class Payment{

	public function makePayment($paymentDetails, $paymentGateway){
		$pgResponse='';
		if (strtoupper($paymentGateway) == config('constants.pgPaypal')){
			$pgResponse = $this->payUsingPayPal($paymentDetails);
		}
		return $pgResponse;
	}
	public function payUsingPayPal($paymentDetails)
    {
		// Removed config('constants.phase1Amt') and changed it to payment_amount which will be // set in each phase makePayment function. 8th Nov, 2017
		//$paymentDetails['payment_amount'] '.$paymentDetails['payment_amount'].'
		$data = '{
		  "intent": "sale",
		  "payer": {
			"payment_method": "'.config('constants.creditCard').'",
			"funding_instruments": [{
			  "credit_card": {
				"number": "'.$paymentDetails['card_number'].'",
				"type": "'.strtolower($paymentDetails['card_type']).'",
				"expire_month": "'.$paymentDetails['ex_month'].'",
				"expire_year": "'.$paymentDetails['ex_year'].'",
				"cvv2": "'.$paymentDetails['cvv'].'",
				"first_name": "'.$paymentDetails['name_on_card'].'",
				"last_name": "'.$paymentDetails['name_on_card'].'",
				"billing_address": {
				  "line1": "'.$paymentDetails['billing_street'].'",
				  "city": "'.$paymentDetails['billing_city'].'",
				  "state": "'.$paymentDetails['billing_state'].'",
				  "postal_code": "'.$paymentDetails['billing_zipcode'].'",
				  "country_code": "'.config('constants.countryCode').'"
				}
			  }
			}]
		  },
		  "transactions": [{
			"amount": {
			 "total":"'.$paymentDetails['payment_amount'].'",
			  "currency": "'.config('constants.currency').'"
			},
			"description": "The payment transaction description."
		  }]
		}';		
		$access_token = $this->getAccessToken();		
		$header =array('Content-Type: application/json','Authorization: Bearer ' . $access_token);
		$ch = curl_init();   
		curl_setopt($ch, CURLOPT_URL, "https://api.paypal.com/v1/payments/payment");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

		$result = curl_exec($ch);
		curl_close($ch);	
		return $result;
    }
	
	
	public function saveCreditCardInVault($paymentDetails)
    {
		
		$postFields = '{
				"number": "'.$paymentDetails['card_number'].'",
				"type": "'.strtolower($paymentDetails['card_type']).'",
				"expire_month": "'.$paymentDetails['ex_month'].'",
				"expire_year": "'.$paymentDetails['ex_year'].'",
				"cvv2": "'.$paymentDetails['cvv'].'",
				"first_name": "'.$paymentDetails['name_on_card'].'",
				"last_name": "'.$paymentDetails['name_on_card'].'",
				"billing_address": {
				  "line1": "'.$paymentDetails['billing_street'].'",
				  "city": "'.$paymentDetails['billing_city'].'",
				  "state": "'.$paymentDetails['billing_state'].'",
				  "postal_code": "'.$paymentDetails['billing_zipcode'].'",
				  "country_code": "'.config('constants.countryCode').'"
				}
			  },
			 "external_customer_id":"'.$paymentDetails['uniqueId'].'"
		}';
		
		$access_token = $this->getAccessToken();
		
		$header = array("Content-Type:application/json", "Content-length: ".strlen($postFields),"Authorization: Bearer ".$access_token,"token_type:Bearer","app_id:APP-APP-80W284485P519543T");
		
		$curl = curl_init();
		   curl_setopt($curl, CURLOPT_VERBOSE, 1);
		   curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		   curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		   //curl_setopt($curl, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/vault/credit-cards/");
		   curl_setopt($curl, CURLOPT_URL, "https://api.paypal.com/v1/vault/credit-cards/");
		   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		   curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
		   curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);

		$result = curl_exec($curl);
		curl_close($curl);
		
		return $result;

    }
	public function payUsingPayPalNVP($customer, $ccDetails){
	
		// Paypal related details
		$sandbox = TRUE;
		$api_version = '119';
		$api_endpoint = $sandbox ? 'https://api-3t.sandbox.paypal.com/nvp' : 'https://api-3t.paypal.com/nvp';
		$api_username = $sandbox ? 'msharan_api1.sabersoft.net' : 'LIVE_USERNAME_GOES_HERE';
		$api_password = $sandbox ? '7CCUK2XLPC6HNT9V' : 'LIVE_PASSWORD_GOES_HERE';
		$api_signature = $sandbox ? 'AFcWxV21C7fd0v3bYYYRCpSSRl31AMf86bNpvNwNtl0mnIudddUfSK65' : 'LIVE_SIGNATURE_GOES_HERE';
		$currency = 'USD';
		
		// Card related details
		$cardnumber = $ccDetails->getCCNumber(); //'4032037396966026';
		$expmonth = $ccDetails->getExpMonth();//'07';
		$expyear = $ccDetails->getExpYear(); //'2022';
		$cvv = $ccDetails->getCvv(); //'123';
		$cardholdername = $ccDetails->getCardHolderName(); //'Meena';
	
		$orderTotal = $customer->payAmount;
		$orderTotal = 0.05;
		$request_params = array(
			  'METHOD' => 'DoDirectPayment', 
			  'USER' => $api_username, 
			  'PWD' => $api_password, 
			  'SIGNATURE' => $api_signature, 
			  'VERSION' => $api_version, 
			  'PAYMENTACTION' => 'Sale',      
			  'IPADDRESS' => '127.0.0.1',
			  'CREDITCARDTYPE' => 'Visa', 
			  'ACCT' => $cardnumber,       
			  'EXPDATE' => $expmonth.$expyear,    
			  'CVV2' => $cvv, 
			  'FIRSTNAME' => $cardholdername, 
			  'AMT' => $ordertotal,
			  'CURRENCYCODE' =>  $currency,     
		);
		$nvp_string = '';
		foreach($request_params as $var=>$val)
		{
			$nvp_string .= '&'.$var.'='.urlencode($val); 
		}
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_URL, $api_endpoint);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $nvp_string);

		$result = curl_exec($curl);
		curl_close($curl);
		
	
	
	}
	
	
	public function getAccessToken(){
		$ch = curl_init();
		//$clientId = config('constants.clientId');
		//$secret = config('constants.secret');    	
    	$clientId = 'AUsejlM1qyWzgZsKSILCBsR-KGonT7Z97-_iNY0jDmfr4geUZ9qUqFncBAHqUv8o3vksuppjYVNN5Dq1';
    	$secret ='EGy3ICRVf1a2dSZi5IRgnPbvEEYey_BDHkKCBER7nj1llzoWf_yN7cZdbEMZ6rvuZ1aAcNnywlFsXGGL';
		curl_setopt($ch, CURLOPT_URL, "https://api.paypal.com/v1/oauth2/token");
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
		$result = curl_exec($ch);
		$err = curl_error($ch);
		$access_token="";
		if ($err) {
		  echo "cURL Error #:" . $err;
		}
		else
		{
			$json = json_decode($result);
		   // print_r($json->access_token);
			$access_token = $json->access_token;
			return $access_token;
		}
	}
}
?>