<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MySQLmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // All MySQl DB Operations Performs Here except CRUD Operations.
    public function getMasterList($arguments){
        
        if(isset($arguments['table']) && $arguments['table']){

            $table      = $arguments['table'];

            $tableAlias = (isset($arguments['tableAlias']) && $arguments['tableAlias']) 
                            ? " AS ".$arguments['tableAlias']
                            : ''; 

            $returnData = (isset($arguments['returnData']) && $arguments['returnData']) 
                            ? $arguments['returnData']
                            : '*';
            
            $dbCondition= (isset($arguments['dbCondition']) && count($arguments['dbCondition'])) 
                            ? $arguments['dbCondition']
                            : array();

            $joins      = (isset($arguments['joins']) && count($arguments['joins'])) 
                            ? $arguments['joins']
                            : array();

            $like       = (isset($arguments['like']) && count($arguments['like'])) 
                            ? $arguments['like']
                            : array();

            $orLike     = (isset($arguments['orLike']) && count($arguments['orLike'])) 
                            ? $arguments['orLike']
                            : array();

            $notLike    = (isset($arguments['notLike']) && count($arguments['notLike'])) 
                            ? $arguments['notLike']
                            : array();

            $orderBy    = (isset($arguments['orderBy']) && count($arguments['orderBy'])) 
                            ? $arguments['orderBy']
                            : array();

            $groupBy    = (isset($arguments['groupBy']) && count($arguments['groupBy'])) 
                            ? $arguments['groupBy']
                            : array();

            $limit      = (isset($arguments['limit']) && $arguments['limit']) 
                            ? explode(',',$arguments['limit'])
                            : array(); 

            $returnType = (isset($arguments['returnType']) && $arguments['returnType']) 
                            ? $arguments['returnType']
                            : 0;

            $having    = (isset($arguments['having'])) ? $arguments['having'] : array();

            $not_in    = (isset($arguments['not_in'])) ? $arguments['not_in'] : array();


            $this->db->select($returnData);

            $this->db->from($this->db->dbprefix($table).$tableAlias);

            $dbCondition ? $this->db->where($dbCondition) : '';

            if(count($joins)){
                foreach ($joins as $field) {
                    if(isset($field['table'])){
                        $type = isset($field['type']) ? $field['type'] : '';
                        $match = isset($field['match']) ? $field['match'] : '';
                        $this->db->join($this->db->dbprefix($field['table'])." AS ".$field['alias'],$match,$type);
                    }
                }
            }

            $like ? $this->db->like($like) : '';

            $orLike ? $this->db->or_like($orLike) : '';

            $notLike ? $this->db->not_like($notLike) : '';

            if(count($orderBy)){
                foreach ($orderBy as $field => $order) {
                    $this->db->order_by($field,$order);
                }
            }

            $groupBy ? $this->db->group_by($groupBy) : '';

            $having ? $this->db->having($having) : '';

            $not_in ? $this->db->where_not_in($not_in) : '';

            $limit ? (count($limit) > 1 ? $this->db->limit($limit['0'],$limit['1']) : $this->db->limit($limit['0'])) : '';

            $query  = $this->db->get();

            $queryResult = $returnType ? ($returnType == 1 ? $query->row_array() : $query->num_rows()) : $query->result_array();

            //for check last execute query
            //echo $this->db->last_query();

            return $queryResult;
        }

    }

    // Basic DB CRUD Operations
    public function dbActionScript($arguments){

        if(isset($arguments['table']) && $arguments['table']){

            $table      = $arguments['table'];

            $action = (isset($arguments['action']) && $arguments['action']) 
                            ? $arguments['action']
                            : '';

            $returnData  = array();

            if($action == 'add'){
                $result = $this->db->insert($this->db->dbprefix($table),$arguments['dbData']);
                $returnData = array(
                    'result'        =>  $result,
                    'insertId'      =>  $result ? $this->db->insert_id() : 0,
                    'error'         =>  $this->db->error()
                );
            }else if($action == 'edit'){
                $this->db->where($arguments['dbCondition']);
                $result = $this->db->update($this->db->dbprefix($table),$arguments['dbData']);
                $returnData = array(
                    'result'        =>  $result,
                    'affectedRows'  =>  $result ? $this->db->affected_rows() : 0,
                    'error'         =>  $this->db->error()
                );
            }else if($action == 'delete'){
                $this->db->where($arguments['dbCondition']);
                $result = $this->db->delete($this->db->dbprefix($table));
                $returnData = array(
                    'result'        =>  $result,
                    'affectedRows'  =>  $result ? $this->db->affected_rows() : 0,
                    'error'         =>  $this->db->error()
                );
            }

            return $returnData;

        }
        
    }

}
