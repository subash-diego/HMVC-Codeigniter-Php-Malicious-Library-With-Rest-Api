<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common extends CI_Controller {

    public function __construct() {
        parent::__construct();
        //$this->load->model('Common/Commonmodel');
        $this->load->model('Common/Commonmysqlmodel');
        //$this->load->library('crmdatabase');

    }

    public function index(){
       
        mysqli_query("LOCK TABLES educliffaccomadation READ");
       
        // exit;
        // $result = $this->db->query('LOCK TABLE educliffaccomadation READ');

        // $result = $this->db->query('SELECT * FROM educliffaccomadation')->row_array();
        // echo '<pre>';print_r($result);exit;
        // $this->db->query('UNLOCK TABLES');
    }

    public function getDetails(){
        $result = $this->db->query('SELECT SQL_NO_CACHE * FROM educliffaccomadation')->row_array();
        
        print_r($result);exit;
    }

    // Fetching details for the table of Study Level, Study Area, Branches, Institution etc.. 
    public function getMasterList(){

        $table          =   'studyarea';// Mandatory

        $tableAlias     =   '';         //  Optional 

        $returnData     =   '';         //  Optional Default '*' define select in string format

        $dbCondition    =   array();    // Optional ex:array('field1 =' => 'value', 'field2 !=' => 'value');

        $like           =   array();    //  Optional ex:array('field1' => 'value', 'field2' => 'value');

        $orLike         =   array();    //  Optional ex:array('field1' => 'value', 'field2' => 'value');

        $notLike        =   array();    //  Optional ex:array('field1' => 'value', 'field2' => 'value');

        $orderBy        =   array();    //  Optional ex:array('field1' => 'value', 'field2' => 'value');

        $groupBy        =   array();    //  Optional ex:array('field1,field2')

        $limit          =   10;         //  Optional $limit,$start ex 10,0 or 0

        $returnType     =   0;          //  Optional ex:'0' => all rows, '1' => single rows, '2' => count

        $joins          =   array(      // Optional
                                array(
                                    'table' => 'course',
                                    'alias' => 'CO',
                                    'match' => 'CO.studyAreaId = educliffstudyarea.studyAreaId',
                                    'type'  => 'LEFT'
                                )
                            );

        $arguments      =   array(
            'table'         =>  $table,
            'tableAlias'    =>  $tableAlias,
            'returnData'    =>  $returnData,
            'dbCondition'   =>  $dbCondition,
            'like'          =>  $like,
            'orLike'        =>  $orLike,
            'notLike'       =>  $notLike,
            'orderBy'       =>  $orderBy,
            'groupBy'       =>  $groupBy,
            'limit'         =>  $limit,
            'returnType'    =>  $returnType,
            'joins'         =>  $joins
        );

        $dbResult = $this->Commonmodel->getMasterList($arguments);

        echo '<pre>';print_r($dbResult);
    }

    // Basic DB CRUD Operations
    public function dbActionScript(){

        $table          =   '';       // Mandatory

        $dbCondition    =   array();  // Optional ex:array('field1 =' => 'value', 'field2 !=' => 'value');

        $dbData         =   array();  // Mandatory

        $action         =   'add';    // Mandatory ex: add/edit/delete

        $arguments      =   array(
            'table'         =>  $table,
            'dbCondition'   =>  $dbCondition,
            'dbData'        =>  $dbData,
            'action'        =>  $action
        );
        
        $dbResult = $this->Commonmodel->dbActionScript($arguments);

        return $dbResult;
    }

    // Fetching details for the table of Study Level, Study Area, Branches, Institution etc.. 
    public function getMongoDbOperations(){

        $table          =   'academicdetails';// Mandatory

        $returnData     =   '';         //  Optional ex:array('field1' => 1, 'field2' => 1, 'field3' => 0);

        $dbCondition    =   array();    // Optional ex:array('field1' => 'value', 'field2' => 'value');

        $orderBy        =   array();    //  Optional ex:array('field1' => 1, 'field2' => -1); 1=asc,-1=desc

        $limit          =   10;         //  Optional $limit,$start ex '10,0' or 0

        $returnType     =   0;          //  Optional ex:'0' => all rows, '1' => single rows, '2' => count


        $arguments      =   array(
            'table'         =>  $table,
            'returnData'    =>  $returnData,
            'dbCondition'   =>  $dbCondition,
            'orderBy'       =>  $orderBy,
            'limit'         =>  $limit,
            'returnType'    =>  $returnType
        );

        $dbResult = $this->CommonMongoDbmodel->getMongoDbAction($arguments);

        echo '<pre>';print_r($dbResult);
    }

}