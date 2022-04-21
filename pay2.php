<?php  ob_start(); session_start();require('db/db.php'); require('db/functions.php');

$curl = curl_init();

$amount = $_SESSION['total_charge']*100; //Product Amount * 100 to convert to Naira from Kobo
$email  =  $_SESSION['l_email'];


curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
    'amount'=>$amount,
    'email'=>$email,
    //reference code generated - use any method to generated refence such as md5(), hash() or rand() with alphabet and store in a session before rediercting to this page
    'reference'=>$_SESSION['reference_key'],
  ]),
  CURLOPT_HTTPHEADER => [
    "authorization: Bearer sk_test_1249da60de445f3622d87a3ce44f30a25a31eaea", 
    //replace this with your own test key or live key
    "content-type: application/json",
    "cache-control: no-cache"
  ],
));

$response = curl_exec($curl);
$err = curl_error($curl);

if($err){
  // there was an error contacting the Paystack API
  die('Curl returned error: ' . $err);
}

$tranx = json_decode($response, true);

if(!$tranx->status){
  // there was an error from the API
  //print_r('API returned error: ' . $tranx['message']);
}



// redirect to page so User can pay
header('Location: ' . $tranx['data']['authorization_url']);

$proceed = $tranx['data']['authorization_url'];
//force the page to redirect to paystack collector
print "<script> window.location='$proceed';    </script>"; 

?>








