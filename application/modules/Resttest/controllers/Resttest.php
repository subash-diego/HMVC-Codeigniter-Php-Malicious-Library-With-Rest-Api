<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          subash chandar
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */

class Resttest extends REST_Controller{

	private $user_details;

	public function __construct(){

		parent::__construct();

		// validating user 

		if(!empty($_SERVER['HTTP_TOKEN'])){
			$readed_data = read_user_session($_SERVER['HTTP_TOKEN']);
			if(!empty($readed_data)){
				$this->user_details = $readed_data;
			}else{
				$this->user_details = NULL;
			}
		}else{
			$this->user_details = NULL;
		}
		
	}

	public function index_get($param = ''){

		print_r($this->user_details);
	}

	public function index_post($parma = ''){

		$this->index_get();

	}

	




	
}