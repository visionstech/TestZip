<?php

require '../../../vendor/autoload.php';
//Dotenv::load(__DIR__);
//kdfgXXESMNI987987987KJDKDRTYUUJJUJ
//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
$sendgrid_apikey = "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";
$sendgrid = new SendGrid($sendgrid_apikey);
$url = 'https://api.sendgrid.com/';
$pass = $sendgrid_apikey;
$template_id = 'e8ac01d5-a07a-4a71-b14c-4721136fe6aa';
$js = array(
  'sub' => array(':name' => array('Elmer')),
  'filters' => array('templates' => array('settings' => array('enable' => 1, 'template_id' => $template_id)))
);

$params = array(
    'to'        => "prabhat@visions.net.in",
    'toname'    => "Example User",
    'from'      => "rahul.baweja@visions.net.in",
    'fromname'  => "Your Name",
    'subject'   => "PHP Test",
    'text'      => "I'm text!",
    'html'      => "<strong>I'm HTML!</strong>",
    'x-smtpapi' => json_encode($js),
  );

$request =  $url.'api/mail.send.json';

// Generate curl request
$session = curl_init($request);
// Tell PHP not to use SSLv3 (instead opting for TLS)
curl_setopt($session, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $sendgrid_apikey));
// Tell curl to use HTTP POST
curl_setopt ($session, CURLOPT_POST, true);
// Tell curl that this is the body of the POST
curl_setopt ($session, CURLOPT_POSTFIELDS, $params);
// Tell curl not to return headers, but do return the response
curl_setopt($session, CURLOPT_HEADER, false);
curl_setopt($session, CURLOPT_RETURNTRANSFER, true);

// obtain response
$response = curl_exec($session);
curl_close($session);

// print everything out
print_r($response);

?>