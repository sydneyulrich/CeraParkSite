<?php
$webmaster_email = "warre334@umn.edu";


$feedback_page = "contact.html";
$error_page = "contact.html";
$thankyou_page = "contact.html";
/*
This next bit loads the form field data into variables.
If you add a form field, you will need to add it here.
*/
$email = $_REQUEST['email'] ;
$message = $_REQUEST['message'] ;
$name = $_REQUEST['name'] ;
$msg = 
"Name: " . $name . "\r\n" . 
"Email: " . $email . "\r\n" . 
"Message: " . $message ;

$response = $_POST["g-recaptcha-response"];
	$url = 'https://www.google.com/recaptcha/api/siteverify';
	$data = array(
		'secret' => '6Le8L6cUAAAAADvYlG5yMC66bZr6KdIfpLn3xHaW',
		'response' => $_POST["g-recaptcha-response"]
	);
	$options = array(
		'http' => array (
			'method' => 'POST',
			'content' => http_build_query($data)
		)
	);
	$context  = stream_context_create($options);
	$verify = file_get_contents($url, false, $context);
	$captcha_success=json_decode($verify);
/*
The following function checks for email injection.
Specifically, it checks for carriage returns - typically used by spammers to inject a CC list.
*/
function isInjected($str) {
	$injections = array('(\n+)',
	'(\r+)',
	'(\t+)',
	'(%0A+)',
	'(%0D+)',
	'(%08+)',
	'(%09+)'
	);
	$inject = join('|', $injections);
	$inject = "/$inject/i";
	if(preg_match($inject,$str)) {
		return true;
	}
	else {
		return false;
	}
}

// If the user tries to access this script directly, redirect them to the feedback form,
if (!isset($_REQUEST['email'])) {
header( "Location: $feedback_page" );
}

// If the form fields are empty, redirect to the error page.
elseif (empty($name) || empty($email)) {
header( "Location: $error_page" );
}

/* 
If email injection is detected, redirect to the error page.
If you add a form field, you should add it here.
*/
elseif ( isInjected($email) || isInjected($name)  || isInjected($message) ) {
header( "Location: $error_page" );
}

else if ($captcha_success->success==false) {
		echo "<p>You are a bot! Go away!</p>";
	} 
else if ($captcha_success->success==true) {
		echo "<p>You are not not a bot!</p>";
	
// If we passed all previous tests, send the email then redirect to the thank you page.

	mail( "$webmaster_email", "New Form Submitted", $msg );

	header( "Location: $thankyou_page" );
}
?>


