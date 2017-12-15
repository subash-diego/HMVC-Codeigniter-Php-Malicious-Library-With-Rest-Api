<?php 

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->library('downloads');
	}

	public function index($param = ''){
		echo "<pre>"; print_r($this->downloads->download_database($param));
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


	







	
}