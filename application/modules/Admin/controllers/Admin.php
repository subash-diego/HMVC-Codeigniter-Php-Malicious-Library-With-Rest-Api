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

class Admin extends REST_Controller{

	/* authendicate user */

	private $user_details;

	public function __construct(){
		parent::__construct();
		
		// is user logged in 
		$credentials = read_user_session(!empty($_SERVER['HTTP_TOKEN'])?$_SERVER['HTTP_TOKEN']:NULL,$_SERVER['REMOTE_ADDR']);
		$this->user_details = $credentials['data'];


		//////////////////////// LOADING MODELS

		$this->load->model('Adminmodel');
		$this->load->model('Common/Commonmysqlmodel');
        $this->load->model('Common/Commonmongodbmodel');


	}

	//REQUEST WARNING 

	public function index_get($param = ''){
		$this->response(
				array('status' => false,'message' => 'Undefined get function requested','status_code' => 404),404
			);
	}

	public function index_post($param = ''){
		$this->response(
				array('status' => false,'message' => 'Undefined post function requested','status_code' => 404),404
			);
	}

	public function index_put($param = ''){
		$this->response(
				array('status' => false,'message' => 'Undefined put function requested','status_code' => 404),404
			);
	}

	public function index_delete($param = ''){
		$this->response(
				array('status' => false,'message' => 'Undefined delete function requested','status_code' => 404),404
			);
	}


	///////////////////////////////// GET MASTER REQUESTS //////////////////

	
	/////////////////////////////// ADD CATEGORY

	public function category_syntax_get($param = ''){

		$this->response(array(
			'status' => true,
			'data' => array(
				'insert' => array(
					'name' => 'eg : Cake',
					'alias' => 'eg : cake'
				),
				'update' => array(
					'id' => 'eg : 1',
					'name'    => 'eg : Cake',
					'alias'	  => 'eg : cake',
					'is_status' => 'eg : 0 or 1'
				),
				'delete' => array(
					'id' =>  'eg : 1'
				)
			)
		),200);

	}

	public function category_get(){


		$start = $this->get('start');
		$limit= $this->get('limit');
		$id   = $this->get('id');
		$is_status = $this->get('is_status');

		if(isset($id)){
			$this->db->where('id',$id);
		}

		if(isset($is_status)){
			$this->db->where('is_status',$is_status);
		}

		if(isset($limit) and isset($start)){
			$this->db->limit($limit,$start);
		}
		
		$result  = $this->db->get($this->db->dbprefix('category'));
		echo $this->db->last_query();
		$this->response(array('status' => true, 'message' => 'data returned success','data' => $result->result()));

	}

	public function category_post($param = ''){

		$name = $this->post('name');
		$alias = $this->post('alias');

		if(trim($name)=='' or trim($alias)==''){
			$this->response(array(
				'status' => false,
				'message' => 'input should not empty',
				'error' => 'input_empty'
			));
		}else{

			$returned = $this->Adminmodel->add_category(array('name' => $name,'alias' => $alias));

			if($returned == TRUE){
				$this->response(array('status' => true, 'message' => 'category inserted success'));
			}else{
				$this->response(array('status' => false, 'message' => 'category insertion failure'));
			}
		}
	}

	public function category_put($param = ''){

		$name  = $this->put('name');
		$alias = $this->put('alias');
		$id    = $this->put('id');
		$is_status = $this->put('is_status')?$this->put('is_status'):0;

		if(trim($name)==NULL or trim($alias)==NULL or trim($id)==NULL){
			$this->response(array('status' => false,'message' => 'inputs should not empty','error' => 'input_empty'));
		}else{
			$returned = $this->Adminmodel->edit_category($id,array('name' => $name,'alias' => $alias,'is_status' => $is_status));
			if($returned == TRUE){
				$this->response(array('status' => true,'message' => 'category updated success'));
			}else{
				$this->response(array('status' => false,'message' => 'category updation failure'));
			}
		}
	}

	public function category_delete($id =''){
		
		if(trim($id)==NULL){
			$this->response(array('status' => false,'message' => 'delete id missing','error' => 'input_empty'));
		}else{
			$returned = $this->Adminmodel->delete_category($id);
			if($returned == TRUE){
				$this->response(array('status' => true,'message' => 'category deleted success'));
			}else{
				$this->response(array('status' => false,'message' => 'category deletion failure'));
			}
		}
	}

	////////////////////////// SUB CATEGORY

    public function sub_category_syntax_get($param = ''){

		$this->response(array(
			'status' => true,
			'data' => array(
				'insert' => array(
					'name' => 'eg : Cake',
					'alias' => 'eg : cake',
					'category_id' => 'eg : 15'
				),
				'update' => array(
					'id' => 'eg : 1',
					'name'    => 'eg : Cake',
					'alias'	  => 'eg : cake',
					'is_status' => 'eg : 0 or 1',
					'category_id' => 'eg : 15'
				),
				'delete' => array(
					'id' =>  'eg : 1'
				)
			)
		),200);

	}

	public function sub_category_get(){


		$start = $this->get('start');
		$limit= $this->get('limit');
		$id   = $this->get('id');
		$is_status = $this->get('is_status');
		$alias = $this->get('alias');
		$category_id = $this->get('category_id');


		if(isset($category_id)){
			$this->db->where('category_id',$category_id);
		}

		if(isset($alias)){
			$this->db->like('alias',$alias);
		}

		if(isset($id)){
			$this->db->where('id',$id);
		}

		if(isset($is_status)){
			$this->db->where('is_status',$is_status);
		}

		if(isset($limit) and isset($start)){
			$this->db->limit($limit,$start);
		}
		
		$result  = $this->db->get($this->db->dbprefix('subcategory'));
		$this->response(array('status' => true, 'message' => 'data returned success','data' => $result->result()));

	}

	public function sub_category_post($param = ''){

		$name = $this->post('name');
		$alias = $this->post('alias');
		$category_id = $this->post('category_id');


		if(trim($name)=='' or trim($alias)==''  or trim($category_id)==''){
			$this->response(array(
				'status' => false,
				'message' => 'input should not empty, must need name,category_id,alias',
				'error' => 'input_empty'
			));
		}else{

			$returned = $this->Adminmodel->insert('subcategory',array('name' => $name,'alias' => $alias,'category_id' =>$category_id));

			if($returned == TRUE){
				$this->response(array('status' => true, 'message' => 'sub_category inserted success'));
			}else{
				$this->response(array('status' => false, 'message' => 'sub_category insertion failure'));
			}
		}
	}

	public function sub_category_put($param = ''){

		$name  = $this->put('name');
		$alias = $this->put('alias');
		$id    = $this->put('id');
		$category_id = $this->put('category_id');
		$is_status = $this->put('is_status')?$this->put('is_status'):0;

		if(trim($name)=='' or trim($alias)=='' or trim($id)=='' or trim($category_id)==''){
			$this->response(array('status' => false,'message' => 'inputs should not empty','error' => 'input_empty'));
		}else{
			$returned = $this->Adminmodel->update('subcategory',array('id'=> $id),array('name' => $name,'alias' => $alias,'is_status' => $is_status,'category_id'=>$category_id));
			if($returned == TRUE){
				$this->response(array('status' => true,'message' => 'sub_category updated success'));
			}else{
				$this->response(array('status' => false,'message' => 'sub_category updation failure'));
			}
		}
	}

	public function sub_category_delete($id =''){
		
		if(trim($id)==''){
			$this->response(array('status' => false,'message' => 'delete id missing','error' => 'input_empty'));
		}else{
			$returned = $this->Adminmodel->delete('subcategory',array('id' => $id));
			if($returned == TRUE){
				$this->response(array('status' => true,'message' => 'sub_category deleted success'));
			}else{
				$this->response(array('status' => false,'message' => 'sub_category deletion failure'));
			}
		}
	}

	/////////////////////////////////// COUNTRY

	public function country_syntax_get(){
		$this->response(array('status' =>true,'data' => array('id','limit','start','sortname')));
	}

	public function country_get(){
		
		$id   = $this->get('id');
		$limit= $this->get('limit');
		$start= $this->get('start');
		$sortname = $this->get('sortname');

		if(isset($sortname)){
			$this->db->where('sortname',$sortname);
		}

		if(isset($id)){
			$this->db->where('id',$id);
		}

		if(isset($limit) and isset($start)){
			$this->db->limit($limit,$start);
		}
		
		$result  = $this->db->get($this->db->dbprefix('countries'));
		$this->response(array('status' => true, 'message' => 'data returned success','data' => $result->result()));

	}

	// public function country_post($param = ''){

	// 	$name = $this->post('name');
	// 	$alias = $this->post('alias');
	// 	$category_id = $this->post('category_id');


	// 	if(trim($name)=='' or trim($alias)==''  or trim($category_id)==''){
	// 		$this->response(array(
	// 			'status' => false,
	// 			'message' => 'input should not empty, must need name,category_id,alias',
	// 			'error' => 'input_empty'
	// 		));
	// 	}else{

	// 		$returned = $this->Adminmodel->insert('subcategory',array('name' => $name,'alias' => $alias,'category_id' =>$category_id));

	// 		if($returned == TRUE){
	// 			$this->response(array('status' => true, 'message' => 'sub_category inserted success'));
	// 		}else{
	// 			$this->response(array('status' => false, 'message' => 'sub_category insertion failure'));
	// 		}
	// 	}
	// }

	// public function country_put($param = ''){

	// 	$name  = $this->put('name');
	// 	$alias = $this->put('alias');
	// 	$id    = $this->put('id');
	// 	$category_id = $this->put('category_id');
	// 	$is_status = $this->put('is_status')?$this->put('is_status'):0;

	// 	if(trim($name)=='' or trim($alias)=='' or trim($id)=='' or trim($category_id)==''){
	// 		$this->response(array('status' => false,'message' => 'inputs should not empty','error' => 'input_empty'));
	// 	}else{
	// 		$returned = $this->Adminmodel->update('subcategory',array('id'=> $id),array('name' => $name,'alias' => $alias,'is_status' => $is_status,'category_id'=>$category_id));
	// 		if($returned == TRUE){
	// 			$this->response(array('status' => true,'message' => 'sub_category updated success'));
	// 		}else{
	// 			$this->response(array('status' => false,'message' => 'sub_category updation failure'));
	// 		}
	// 	}
	// }

	// public function country_delete($id =''){
		
	// 	if(trim($id)==''){
	// 		$this->response(array('status' => false,'message' => 'delete id missing','error' => 'input_empty'));
	// 	}else{
	// 		$returned = $this->Adminmodel->delete('subcategory',array('id' => $id));
	// 		if($returned == TRUE){
	// 			$this->response(array('status' => true,'message' => 'sub_category deleted success'));
	// 		}else{
	// 			$this->response(array('status' => false,'message' => 'sub_category deletion failure'));
	// 		}
	// 	}
	// } 

	/////////////////////////////// STATE

	public function state_syntax_get(){

		$this->response(array('status' =>true,'data' => array('id','limit','start','country_id')));

	}

	public function state_get(){
		
		$id   = $this->get('id');
		$limit= $this->get('limit');
		$start= $this->get('start');
		$country_id = $this->get('country_id');

		if(isset($id)){
			$this->db->where('id',$id);
		}

		if(isset($country_id)){
			$this->db->where('country_id',$country_id);
		}

		if(isset($limit) and isset($start)){
			$this->db->limit($limit,$start);
		}
		
		$result  = $this->db->get($this->db->dbprefix('states'));
		$this->response(array('status' => true, 'message' => 'data returned success','data' => $result->result()));

	}

	/////////////////////////////////// HOME TOP BANNER
	
	public function hometopbanner_get($args=""){

		$id   = $this->get('id');
		$limit= $this->get('limit');
		$start= $this->get('start');
		$home_page_type = $this->get('home_page_type');
		$name = $this->get('name');
		$is_status = $this->get('is_status');

		if(isset($home_page_type)){
			$this->db->where('home_page_type',$home_page_type);
		}

		if(isset($name)){
			$this->db->where('name',$name);
		}

		if(isset($is_status)){
			$this->db->where('is_status',$is_status);
		}
		
		if(isset($id)){
			$this->db->where('id',$id);
		}

		if(isset($limit) and isset($start)){
			$this->db->limit($limit,$start);
		}
		
		$result  = $this->db->get($this->db->dbprefix('hometopbanner'));
		$this->response(array('status' => true, 'message' => 'data returned success','data' => $result->result()));
	}


	public function hometopbanner_post($args=""){

        $home_page_type = $this->post('home_page_type');
        $name 			= $this->post('name');
        $action_url 	= $this->post('action_url');
        $header 		= $this->post('header');
        $small_header 	= $this->post('small_header');
        $picture        = NULL;

        if(!empty($_FILES['picture']['name'])){

            $config['upload_path']     = UPLOADHOMETOPBANNER;
            $config['allowed_types']   = 'gif|jpg|png|jpeg';
            $config['file_name']       = random_string('alnum', 16);
            
            
            $this->load->library('upload');
            $this->upload->initialize($config);

            if ($this->upload->do_upload('picture')){

                $imagedata       = $this->upload->data();
                $picture 		 = $imagedata['file_name'];

            }

        }

        print_r($_FILES);

    }






	
}