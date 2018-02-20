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

class Login extends REST_Controller{

	/* authendicate user */

	private $user_details;
	private $user_token;

	public function __construct(){
		parent::__construct();

		//user token
		$this->user_token = !empty($_SERVER['HTTP_TOKEN'])?$_SERVER['HTTP_TOKEN']:NULL;

		//user details
		$credentials = read_user_session(!empty($_SERVER['HTTP_TOKEN'])?$_SERVER['HTTP_TOKEN']:NULL,$_SERVER['REMOTE_ADDR']);
		$this->user_details = $credentials['data'];
		
		//loading login model
		$this->load->model('Loginmodel');

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
		if($this->user_details==NULL){
		
			if(trim($email)!='' && trim($password)!=''){

				/* validating email*/

				if(is_valid_user($email)==TRUE){

					//checking is authendication 
					$user_id = is_user_auth($email,$password);

					if(trim($user_id)!==''){

						/* starting session */

						$return_token = insert_user_session($user_id,$_SERVER['REMOTE_ADDR']);

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
		}else{
			$this->response(array('status' => TRUE,
								  'token'  => $this->user_details->token,	
								  'message'=> 'user already logged in'
												));
		}

	}

	//GETTING USER DETAILS 

	public function get_user_details_get($param = ''){

		if($this->user_details!=NULL){

			$user_detail = $this->Loginmodel->get_user_data($this->user_details->userid);
			$filter_data;

			//filter data 
			foreach ($user_detail as $key => $value) {
				if($key!='password'){$filter_data[$key] = $value;}
			}

			//echo data
			$this->response(array('status'=>TRUE,'data' => $filter_data));
		}

		$this->response(array('status'=>FALSE,'message' => 'token not valid'));

	}

	public function logout_get($param=''){

		if($this->user_token!=NULL){
			if(user_logout($this->user_token)==TRUE){
				$this->response(array('status' => TRUE,
									  'message'=> 'user logged out successfully'
											));
			}
		}else{

			$this->response(array('status' => FALSE,
								  'message'=> 'user logged out unsuccessfull'
											));
		}

	}
	
}