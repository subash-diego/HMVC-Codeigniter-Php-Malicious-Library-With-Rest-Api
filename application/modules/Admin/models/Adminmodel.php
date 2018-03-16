<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Adminmodel extends CI_Model{

	/*

	@root is get all path 
	@author R subash chandar
	@credit subash

	*/

	/////////////////////////// CATEGORY ADD UPDATE DELETE

	public function add_category($data)
	{
		$result = $this->db->insert($this->db->dbprefix('category'),$data);
		return $result;
	}

	public function edit_category($id,$data)
	{
		$this->db->where('id',$id);
		$result = $this->db->update($this->db->dbprefix('category'),$data);
		return $result;
	}

	public function delete_category($id)
	{	
		$this->db->where('id', $id);
		$result = $this->db->delete($this->db->dbprefix('category'));
		return $result;
	}

	//////////////////////////// SUB CATEGORY UPDATE DELETE

	//////////////////////////// MASTER INSERT,DELETE,UPDATE

	public function insert($table,$data){
		$query = $this->db->insert($this->db->dbprefix($table),$data);
		return $query;
	}

	public function update($table,$condition,$data){
		$this->db->where($condition);
		$query = $this->db->update($this->db->dbprefix($table),$data);
		return $query;
	}

	public function delete($table,$condition){
		$this->db->where($condition);
		$query = $this->db->delete($this->db->dbprefix($table));
		return $query;
	}

	
}