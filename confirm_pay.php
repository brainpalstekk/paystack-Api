<?php ob_start(); session_start();
require('db/db.php'); //Replace Your db connection
 require('db/functions.php'); // Replace with your functions
if(!isset($_SESSION['reference_key']) ){  header('location:students/'); die();} 
//Validating that refernce is set

/******************************

This Confirm pay can be modified, in my case, it allows you to send the pdf version of the book
to the buyer once payment is confirm.

The way your host handles it might be different. 
You can also choose to take users or clients to another page where the services they paid for is 
provided to them.

reach me on brainpalstekk@gmail.com for any issues you have and we sort it out.


*//////////////////////////////
?>

<?php
//This will be set in my Call Back as pay-appreciation.php
$result = array();
//The parameter after verify/ is the transaction reference to be verified
//$url = 'https://api.paystack.co/transaction/verify/'.$_SESSION['reference_key'];

$url = 'https://api.paystack.co/transaction/verify/'.$_SESSION['reference_key']; //ref from pay2.php

//$url = 'https://api.paystack.co/transaction/verify/'.$_SESSION['reference_key'];


// sk_test_1249da60de445f3622d87a3ce44f30a25a31eaea
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt(
  $ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer sk_test_1249da60de445f3622d87a3ce44f30a25a31eaea']
);
$request = curl_exec($ch);
curl_close($ch);
//https://developers.paystack.co/reference#verifying-transactions
if ($request) {
    $result = json_decode($request, true);
    // print_r($result);
    if($result){
      if($result['data']){
        //something came in
        if($result['data']['status'] == 'success'){

     //find The reference Key
    if($db->query("SELECT COUNT(*) FROM paytab WHERE transaction_ref ='".$_SESSION['reference_key']."' ")->fetchColumn()>0){
      $_SESSION['book_sales'] = 'book';
$db->query("UPDATE paytab SET status='1',time_confirmed ='".time()."' where transaction_ref ='".$_SESSION['reference_key']."' ");
$_SESSION['donation_state'] = 1;
//send ebook here
$booker = get_book_details($_SESSION['book_code']);
$book_location = $booker['url'];

 require_once "Mail.php"; // PEAR Mail package
require_once ('Mail/mime.php'); // PEAR Mail_Mime packge

 $from = "noreply@grbizhub.com"; //enter your email address
 $to = $_SESSION['l_email']; //enter the email address of the contact your sending to
 $subject = "Congratulations on Successful Purchase of our Ebook"; // subject of your email


$headers = array ('From' => $from,'To' => $to, 'Subject' => $subject);

$text = ''; // text versions of email.
$html = "<html><body>
<style type='text/css'>
p{ font-weight:bold;}
</style>

<p>Hi, thank you for trusting grbizhub organics Ebooks.<br/> We do hope
you will learn alot from this material as it is one of our best.<br/>
Below is a copy of your ebook, download and enjoy. <br/> You can also Choose to
take any of our subsidised organic courses <br> available on your dashboard.
<br><br/> Best Regards, <br><br> Sales Unit <br> Glow Radiance Care, Nigeria.</p> </body></html>"; // html versions of email.

$crlf = "\n";

$file_name =$book_location;

$mime = new Mail_mime($crlf);
$mime->setTXTBody($text);
$mime->setHTMLBody($html);
$mime->addAttachment($file_name);
//do not ever try to call these lines in reverse order
$body = $mime->get();
$headers = $mime->headers($headers);

 $host = "localhost"; // your mail server i.e mail.mydomain.com
 $username = "noreply@grbizhub.com"; //  your email address (same as webmail username)
 $password = "Digital2017+"; // your password (same as webmail password)

$smtp = Mail::factory('smtp', array ('host' => $host, 'auth' => true,
'username' => $username,'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
echo("<p>" . $mail->getMessage() . "</p>");
}
else {
//echo("<p>Message successfully sent!</p>");
// header("Location: http://www.example.com/");

}


header('location:pay_message.php');

    }elseif($db->query("SELECT COUNT(*) FROM classes WHERE pay_ref ='".$_SESSION['reference_key']."' ")->fetchColumn()>0){
      //add the class


    }else{
          $_SESSION['donation_state'] = 0;
          // the transaction was not successful, do not deliver value'
          // print_r($result);  //uncomment this line to inspect the result, to check why it failed.
          //echo "Transaction was not successful: Last gateway response was: ".$result['data']['gateway_response'];
            header('location:students/');

        }
      }else{
        echo $result['message'];
      }

    }else{
      //print_r($result);
      die("Something went wrong while trying to convert the request variable to json. Uncomment the print_r command to see what is in the result variable.");
    }
  }else{
    //var_dump($request);
    $_SESSION['donation_state'] = 0;
    header('location:creators/pay_message'); //Payment Verification Page whether Failed or Successful

    //die("Something went wrong while executing curl. Uncomment the var_dump line above this line to see what the issue is.
     // Please check your CURL command to make sure everything is ok");


  }

}

?>

