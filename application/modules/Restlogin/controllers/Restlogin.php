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

class Restlogin extends REST_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('downloads');
	}

	/* return format of login */

	public function index_get($param = ''){

		$format = array('status' => TRUE,'inputs' => array('email' =>'' ,'password' => ''));

		$this->response($format);
	}

	/* return is login */

	public function index_post($param = ''){

		$return_data = array();

		$email 	  = $this->input->post('email');
		$password = $this->input->post('password');

		/* manupulation of email */

		if(trim($email)!='' && trim($password)!=''){

			/* validating email*/

			if(is_valid_user($email)==TRUE){

				//checking is authendication 
				$user_id = is_user_auth($email,$password);

				if(trim($user_id)!==''){

					/* starting session */

					$return_token = insert_user_session($user_id);

					if(trim($return_token)!==''){

						$this->response(array('status' => TRUE,
											  'token'  => $return_token,
											  'message'=> 'user logged in successfully'
											));

					}else{ $this->response(array('status' => FALSE,
											  'message'=> 'user login failure'
											));
							}

					
				}else{ $this->response(array('status' => FALSE, 'message' => 'Email and Password is missmatch')); }

			}else{ $this->response(array('status' => FALSE, 'message' => 'you are not registered user please sign up')); }

		}else{

			$return_data['status']  = FALSE;
			$return_data['massage'] = 'email and password should not empty';
			$this->response($return_data);
		}

	}

	public function test_post(){
		$this->response(read_user_session($_SERVER['HTTP_TOKEN']));
		//$this->response(user_session_data(101));
	}




	
}