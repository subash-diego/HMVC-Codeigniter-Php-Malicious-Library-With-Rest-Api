<?php 

/* @author subashchandar
   @param 

*/

//set your paypal credentials

$config['client_id'] = 'Aef2gNQuSe_ZSJgfNJfvmyvstSFHsLeN5tRuM_ME_2csDsfX73Fq_jMZOalqQTwKjI_xnTOacmjtvnXD';
$config['secret']	 = 'EAdAlYww1RXTy6Ov_XZ9o0q3tOhtp8I9J-YrzgZ_IAXEbMAiZmoPQJlXZkeruErPcvoUlBhCELJsRuJ0';

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
