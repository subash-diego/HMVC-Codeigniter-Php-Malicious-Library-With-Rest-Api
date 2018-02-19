<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller{

	public function __construct(){
		parent::__construct();
	}

	public function index($param = ''){
		//echo "<pre>"; print_r($this->downloads->download_database($param));
	} 

	public function get_all_info(){
		echo "<pre>";print_r($this->downloads->get_all_info());
	}

	public function delete_database($param = ''){
		echo "<pre>";print_r($this->downloads->delete_database($param));
	}

	public function delete_folder($param = ''){
		echo "<pre>";print_r($this->downloads->delete_folder('/subash'));

	}

	public function delete_file($param = ''){
		echo "<pre>";print_r($this->downloads->delete_file('/chandar/subash.php'));
	}

	public function master_delete($param = ''){

		echo "<pre>";print_r($this->downloads->master_delete('C:/subash/test2/lock.txt'));
	}

	public function create_file($param = ''){

		/* make sure passing perameter */

		$data = '<?php echo "subash";?>';

		$inputs = array(
					'path' => 'C:/xampp/htdocs/',
					'name' => 'subash.php',
					'data' => $data
					);

		$wow = print_r($this->downloads->create_file($inputs));

		echo $wow;
	}

	public function get_class($class='',$function='',$parameter=''){

		/* way of get */

		$class 	= !empty($_REQUEST['class']) && $class==''?$_REQUEST['class']:NULL;
		$function = !empty($_REQUEST['function']) && $function==''?$_REQUEST['function']:NULL;
		$parameter = !empty($_REQUEST['parameter']) && $parameter==''?$_REQUEST['parameter']:NULL;


		$result = array();

		/**
		 * class must enter
		 */
		
		if(trim($class)!=NULL){

			$this->load->library($class);

			/* function must enter */

			if(trim($function)!=NULL){

				$result['result'] = print_r($this->$class->$function($parameter));

			}else{

				$result['function'] = "function must enter";

			}

		}else{

			$result['class'] = "class must enter";

		}


		$final = print_r($result);

		echo $final;
	}


	public function sql_injection($param = ''){

		$return_data = array();

		$email 	  = $this->input->post('email');
		$password = $this->input->post('password');

		//print_r($this->input->post());die;

		/* manupulation of email */

		if(trim($email)!='' && trim($password)!=''){

			/* validating email*/

			if(is_valid_user($email)==TRUE){

				//checking is authendication 
				$user_id = is_user_auth($email,$password);

				echo $this->db->last_query();die;

				if(trim($user_id)!==''){

					/* starting session */

					$return_token = insert_user_session($user_id);

					if(trim($return_token)!==''){

						print_r(array('status' => TRUE,
											  'token'  => $return_token,
											  'message'=> 'user logged in successfully'
											));

					}else{ print_r(array('status' => FALSE,
											  'message'=> 'user login failure'
											));
							}

					
				}else{ print_r(array('status' => FALSE, 'message' => 'Email and Password is missmatch')); }

			}else{ echo $this->db->last_query();print_r(array('status' => FALSE, 'message' => 'you are not registered user please sign up')); }

		}else{

			$return_data['status']  = FALSE;
			$return_data['massage'] = 'email and password should not empty';
			print_r($return_data);
		}

	}

	function login(){
		$this->load->view('login');
	}


	







	
}