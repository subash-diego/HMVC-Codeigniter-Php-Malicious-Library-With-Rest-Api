<?php


/* registered user */

function is_valid_user($email = ''){

	$CI =& get_instance();
	$CI->db->where('email',$email);
	$result = $CI->db->get($CI->db->dbprefix('user'));

	if($result->num_rows()>0){
		return TRUE;
	}
}

/* match email and password */

function is_user_auth($email,$password){

	$CI =& get_instance();
	$CI->db->where(array('email' => $email, 'password' => sha1($password)));
	$result = $CI->db->get($CI->db->dbprefix('user'));

	if($result->num_rows() > 0){
		
		$data = $result->row();
		return (!empty($data->userid));
	}
}

/* return user data */

function get_userdata($id){

	$CI =& get_instance();
	$CI->db->where('userid',$id);
	$result = $CI->db->get($CI->db->dbprefix('user'));

	if($result->num_rows()){
		return $result->row();
	}
}

/* get user data using token */

function get_data_by_token($token){

	$CI =& get_instance();
	$CI->db->where('token',$token);

}

/* create token for user */

function create_token(){

	return md5(uniqid(rand(10,10000000), true));

}

function insert_user_session($id = '',$ip=''){

	$CI =& get_instance();
	$CI->load->library('user_agent');


	/* get codeigniter session */
	$codeigniter_session = $CI->session->all_userdata();
	/* browser data */
	$all_server_data     = '';
	/* browser & operating system */
	$browser			 = $CI->agent->is_browser()?$CI->agent->browser():FALSE;
	$operating_system    = $CI->agent->platform();
	//user session 

	/* expiring upto 1 day */
	$startDate = time();
	$expired_to= date('Y-m-d H:i:s', strtotime('+1 day', $startDate));


	$data = array(
		'token' 	=> create_token(),
		'framework_token' =>!empty($codeigniter_session['__ci_last_regenerate'])?$codeigniter_session['__ci_last_regenerate']:FALSE,
		'userid'	=> $id,
		'created_on'=> date('Y-m-d h:i:s'),
		'updated_on'=> date('Y-m-d h:i:s'),
		'browser'   => $browser,
		'operating_system' => $operating_system,
		'expired_to' => $expired_to,
		'ip'		=> $ip
		);

	// true

	if($CI->db->insert($CI->db->dbprefix('session_management'),$data)==TRUE){
		return $data['token'];
	}
}

/* reading user session in codeigniter */

function read_user_session($token='',$ip=''){

	$CI =& get_instance();

	/* validate cookie and manupulate */

	$CI->db->where(array('token' => $token,'ip' => $ip));
	$result = $CI->db->get($CI->db->dbprefix('session_management'));

	/* current time */

	$current_time = strtotime(date('Y-m-d H:i:s'));
	
	/* validation */

	if($result->num_rows()){

		$returned_result = $result->row();

		$return_time  = strtotime($returned_result->expired_to);
		$updated_time = strtotime($returned_result->updated_on);

		if($return_time > $updated_time){

			//updating token session timings
			$expired_to= date('Y-m-d H:i:s', strtotime('+1 day', time()));
			$CI->db->where('token',$token);
			$CI->db->update($CI->db->dbprefix('session_management'),
				array('updated_on' => date('Y-m-d H:i:s'),
					  'expired_to' => $expired_to
			));

			return array('result' =>true,'data' =>$returned_result);
		}else{

			//inserting tracking and deleting management session
			$filter_data 	 = NULL;
			foreach ($returned_result as $key => $value) {	
				if($key != 'id'){
				$filter_data[$key] = $value;
				}
			}

			$CI->db->insert($CI->db->dbprefix('user_tracking'),$filter_data);

			//deleting cookies from session mangement
			$CI->db->where('token',$token);
			$CI->db->delete($CI->db->dbprefix('session_management'));
			
			return array('result' =>false,'data' =>NULL);
		}

	}

}

/* logout rest user */

function user_logout($token){
	
	$CI =& get_instance();

	//getting datas from token
	$CI->db->where(array('token' => $token));
	$result = $CI->db->get($CI->db->dbprefix('session_management'));

	if($result->num_rows()){

		//converting to array
		$returned_result = $result->row();
		$filter_data 	 = NULL;
		foreach ($returned_result as $key => $value) {
			if($key != 'id'){
			$filter_data[$key] = $value;
			}
		}

		// inserting session track
		$CI->db->insert($CI->db->dbprefix('user_tracking'),$filter_data);

		//deleting management row
			$CI->db->where('token',$token);
			if($CI->db->delete($CI->db->dbprefix('session_management'))==TRUE){
				return TRUE;
			}
		}

}

function is_logged_in($token='',$ip='')
{
	$CI =& get_instance();

	$CI->db->where(array('token' => $token,'ip' => $ip));
	$query = $CI->db->get($CI->db->dbprefix('session_management'));

	if($query->num_rows() > 0){
		return TRUE;
	}
}










?>