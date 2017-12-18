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

/* create token for user */

function create_token(){

	return md5(uniqid(rand(10,10000000), true));

}

function user_session_data($id = ''){

	$CI =& get_instance();
	$CI->load->library('user_agent');


	/* get codeigniter session */
	$codeigniter_session = $CI->session->all_userdata();
	/* browser data */
	$all_server_data     = $CI->downloads->get_all_info();
	/* browser & operating system */
	$browser			 = $CI->agent->is_browser()?$CI->agent->browser():FALSE;
	$operating_system    = $CI->agent->platform();
	//user session 

	$data = array(
		'token' 	=> create_token(),
		'framework_token' =>!empty($codeigniter_session['__ci_last_regenerate'])?$codeigniter_session['__ci_last_regenerate']:FALSE,
		'userid'	=> $id,
		'created_on'=> date('Y-m-d h:i:s'),
		'browser'   => $browser,
		'operating_system' => $operating_system,
		'valid_on'	=> date('Y-m-d h:i:s')
	);

	return $data;
}










?>