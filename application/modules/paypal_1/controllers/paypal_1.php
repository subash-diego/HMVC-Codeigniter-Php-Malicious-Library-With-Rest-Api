<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class paypal_1 extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->output->enable_profiler(TRUE);
	}

	public function index($param = ''){
		echo "paypal integration 1";
	}


	
}