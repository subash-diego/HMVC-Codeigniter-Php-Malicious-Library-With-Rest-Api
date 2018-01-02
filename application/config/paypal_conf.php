<?php 

/* @author subashchandar
   @param 

*/

//set your paypal credentials

$config['client_id'] = '';
$config['secret']	 = '';

// SDK Configuration

$config['settings'] = array(
	'mode' => 'sandbox',
	'http.ConnectionTimeOut' => 1000,
	'log.LogEnabled' => true,
	'log.FileName'   => 'application/logs/paypal.log',
	'log.LogLevel'  => 'FINE'
	);

// watch this youtube video

//https://youtu.be/Wx6W3_1LE5w

?>
