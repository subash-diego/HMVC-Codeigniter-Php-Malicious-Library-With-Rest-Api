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

		//////////////////////// LOADING MODELS

		$this->load->model('Common/Commonmysqlmodel');
        $this->load->model('Common/Commonmongodbmodel');

	}

	/* return format of login */

	public function index_get($param = ''){
		$this->response(
				array('status' => false,'message' => 'Undefined get function requested','status_code' => 404)
			);
	}

	public function index_post($param = ''){
		$this->response(
				array('status' => false,'message' => 'Undefined post function requested','status_code' => 404)
			);
	}

	public function index_put($param = ''){
		$this->response(
				array('status' => false,'message' => 'Undefined put function requested','status_code' => 404)
			);
	}

	public function index_delete($param = ''){
		$this->response(
				array('status' => false,'message' => 'Undefined delete function requested','status_code' => 404)
			);
	}
	

	/* return is login */

	public function Logged_in_post($param = ''){

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

	///////////////////////// LOGOUT

	public function logout_get($param=''){

		if($this->user_token!=NULL){
			if(user_logout($this->user_token)==TRUE){
				$this->response(array('status' => TRUE,
									  'message'=> 'user logged out successfully'
											));
			}else{

				$this->response(array('status' => false,
									  'message'=> 'token not valid',
									  'error' => 'token_not_valid'
											));	
			}
		}else{

			$this->response(array('status' => FALSE,
								  'message'=> 'user logged out unsuccessfull'
											));
		}

	}

	//////////////////////// REGISTRATION

	public function registration_post($var=''){

		$firstname = $this->input->post('firstname');
		$lastname = $this->input->post('lastname');
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		$terms = $this->input->post('terms');


		if(trim($firstname)!=='' && trim($lastname)!='' && trim($email)!=='' && trim($password)!==''){

			$register = array(
                'firstname' => $firstname,
                'lastname'  => $lastname,
                'email'     => $email,
                'password'  => sha1($password),
                'inserted_date' => date('Y-m-d h:i:s')
            );

            //////////// VERIFIYING EMAIL

            $dbCondition = array('table' => 'user','dbCondition' => array('email' => $email),'returnType' => 1,'returnData' => 'userid');

            $returnData = json_decode($this->Commonmysqlmodel->getMasterList($dbCondition),TRUE);

            if(count($returnData)>0){

            	$this->response(array('status' => false,'message' => 'Email already registered'));

            }else{

            	////////////////// INSERTING REGISTERED DATA

            	$upCondition = array(
                    'table'  => 'user',
                    'action' => 'add',
                    'dbData' => $register
                );

                $updateData = $this->Commonmysqlmodel->dbActionScript($upCondition);

                $actionStatus = !empty($updateData['error']['code']) ? $updateData['error']['code'] : '';

                //////////////// RETURNING RESULT

                if($actionStatus==0){

                	$this->response(array('status' => true, 'message' => 'Registration success'));

                }else{

                	$this->response(array('status' => false, 'message' => 'Registration failed'));

                }

                /////////////////////
            }

		}else{

			$this->response(array('status' => false,'message' => 'should not inputs empty','inputs' => array('firstname','lastname','email','password')));
		}       

    }


    ///////////////////////// SET UP GEO LOCATION

    public function geoaccess_post($param=''){

         $lang = $this->input->post('lang');
         $latt = $this->input->post('latt');

         if($lang=='' or $latt==''){

         	$this->response(array('status' => false,'message' =>'geo inputs empty','inputs' => array('lang','latt')));die;

         }

         try{
				
         $geodata = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?latlng=".$latt.",".$lang."&key=AIzaSyAQFvoxJbkstHuorLu3P8W3lNfV_elngZE"));

         }catch(Exception $e){

         	$this->response(array('status' => false,'message' => 'Error : '.getMessage()));die;
         }


         $postal_code = '';
         $route       = '';
         $street_number = '';
         $sublocality_level_1 = '';
         $sublocality_level_2 = '';
         $sublocality_level_3 = '';
         $city        = '';
         $state       = '';
         $country     = '';

         if(count($geodata)){

	        foreach($geodata->results as $results)
	        {

	            foreach($results->address_components as $address_components)
	            {
	                // add code for requirement

	                if(isset($address_components->types) && $address_components->types[0] == 'postal_code')
	                { $postal_code = $address_components->long_name; }

	                if(isset($address_components->types) && $address_components->types[0] == 'street_number')
	                    { $street_number = $address_components->long_name; }

	                if(isset($address_components->types) && $address_components->types[0] == 'route')
	                    { $route = $address_components->long_name; }

	                if(isset($address_components->types) && !empty($address_components->types[2])?$address_components->types[2]:'' == 'sublocality_level_3')
	                    { $sublocality_level_3 = $address_components->long_name; }

	                if(isset($address_components->types) && !empty($address_components->types[2])?$address_components->types[2]:'' == 'sublocality_level_2')
	                    { $sublocality_level_2 = $address_components->long_name; }

	                if(isset($address_components->types) && !empty($address_components->types[2])?$address_components->types[2]:'' == 'sublocality_level_1')
	                    { $sublocality_level_1 = $address_components->long_name; }

	                if(isset($address_components->types) && $address_components->types[0] == 'locality')
	                    { $city = $address_components->long_name; }

	                if(isset($address_components->types) && $address_components->types[0] == 'administrative_area_level_1')
	                    { $state = $address_components->long_name; }

	                if(isset($address_components->types) && $address_components->types[0] == 'country')
	                    { $country = $address_components->long_name; }

	            }
	        }

	        $usergeodata = array(

                    'postal_code' => $postal_code,
                    'street_number' => $street_number,
                    'route'       => $route,
                    'street_number' => $street_number,
                    'sublocality_level_1' => $sublocality_level_1,
                    'sublocality_level_2' => $sublocality_level_2,
                    'sublocality_level_3' => $sublocality_level_3,
                    'city'        => $city,
                    'state'       => $state,
                    'country'     => $country

                    );

        	$this->response(array('status' => true, 'data' => $usergeodata));
	    }

        
    }

    //////////////////////////
	
}