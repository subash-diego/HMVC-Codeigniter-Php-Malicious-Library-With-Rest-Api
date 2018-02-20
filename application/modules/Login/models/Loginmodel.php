<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Loginmodel extends CI_Model{

	/*

	@root is get all path 
	@author R subash chandar
	@credit subash

	*/


	public function __construct(){

		parent::__construct();


	}

	public function get_user_data($userID){

		$this->db->where('userid',$userID);
		$query = $this->db->get($this->db->dbprefix('user'));

		if($query->num_rows()){
			return $query->row();
		}

	}

   
}