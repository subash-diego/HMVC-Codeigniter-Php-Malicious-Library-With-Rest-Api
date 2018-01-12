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

function insert_user_session($id = ''){

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
		'browser'   => $browser,
		'operating_system' => $operating_system,
		'expired_to' => $expired_to
		);

	// true

	if($CI->db->insert($CI->db->dbprefix('session_management'),$data)==TRUE){
		return $data['token'];
	}
}

/* reading user session in codeigniter */

function read_user_session($token){

	$CI =& get_instance();

	/* validate cookie and manupulate */

	$CI->db->where('token',$token);
	$result = $CI->db->get($CI->db->dbprefix('session_management'));

	/* current time */

	$current_time = strtotime(date('Y-m-d H:i:s'));
	//return strtotime($current_time).' next'.strtotime('2017-12-18 06:30:00');

	/* validation */

	if($result->num_rows() > 0){

		$returned_result = $result->row();
		
		if((strtotime($returned_result->expired_to) > $current_time) && $returned_result->token == $token)
		{
			return $returned_result;

		}else{

			$CI->db->where('token',$token);
			$CI->db->delete($CI->db->dbprefix('session_management'));
		}

	}

}

/* logout rest user */

function user_logout($token){
	$CI =& get_instance();
	$CI->db->where('token',$token);
	if($CI->db->delete($CI->db->dbprefix('session_management'))==TRUE){
		return TRUE;
	}
}










?>