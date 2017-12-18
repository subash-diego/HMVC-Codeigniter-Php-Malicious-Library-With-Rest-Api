<?php


/*$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://localhost:82/HMVC-Codeigniter/Restlogin");
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query(array('email' => 'subash.diego@gmail.com','password' => 'subash')));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec ($ch);
curl_close ($ch);
print_r($server_output);*/

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_PORT => "82",
  CURLOPT_URL => "http://localhost:82/HMVC-Codeigniter/Resttest",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Cache-Control: no-cache",
    "email: subash.diego@gmail.com",
    "password: subash",
    "token: 5e23059a34a5025be2520e33997de80d"
  ),
 /* CURLOPT_POSTFIELDS => array(
  							'email' => 'subash.diego@gmail.com',
  							'password' => 'subash')*/
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}

?>