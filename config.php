<?php 

if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
   $mainURL = $protocol . "://" . $_SERVER['HTTP_HOST'].'/';

/*---- Config.php Settings --- */
//$mainURL = 'http://www.cakedesert.com/';
//$mainURL = $_SERVER['HTTP_HOST'];

define('HOMEURL',$mainURL);
define('SITEURL',$mainURL);
define('BASEURL',$mainURL);

?>

