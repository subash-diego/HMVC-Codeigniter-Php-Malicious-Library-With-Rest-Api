<?php 

if(isset($_SERVER['HTTPS'])){
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
    }
    else{
        $protocol = 'http';
    }
   $mainURL = $protocol . "://" . $_SERVER['HTTP_HOST'].'/';

/*---- Config.php Settings --- */

define('HOMEURL',$mainURL);
define('SITEURL',$mainURL);
define('BASEURL',$mainURL);

/* Database settings */

define('db_database','hmvc');
define('db_hostname','localhost');
define('db_username','subash');
define('db_password','hanuman');
define('db_prefix','hmvc');

/* */


?>

