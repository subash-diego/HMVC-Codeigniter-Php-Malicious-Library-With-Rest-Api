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
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */

class Home extends REST_Controller{

	/* authendicate user */

	private $user_details;

	public function __construct(){
		parent::__construct();
		
		// is user logged in 
		$credentials = read_user_session(!empty($_SERVER['HTTP_TOKEN'])?$_SERVER['HTTP_TOKEN']:NULL,$_SERVER['REMOTE_ADDR']);
		$this->user_details = $credentials['data'];

	}

	public function index_get($param = ''){
		$this->index_post();
	}

	public function index_post($param = ''){

		$this->response($this->user_details);

	}
	
}