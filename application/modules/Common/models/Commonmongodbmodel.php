<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 
class Commonmongodbmodel extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Mongo DB Operations Performs Here except CRUD Operations.
    public function getMongoDbAction($arguments){

        if(isset($arguments['table']) && $arguments['table']){

            $table      = $arguments['table'];

            $returnData = (isset($arguments['returnData']) && count($arguments['returnData'])) 
                            ? $arguments['returnData']
                            : array();
            
            $dbCondition= (isset($arguments['dbCondition']) && count($arguments['dbCondition'])) 
                            ? $arguments['dbCondition']
                            : array();

            $orderBy    = (isset($arguments['orderBy']) && count($arguments['orderBy'])) 
                            ? $arguments['orderBy']
                            : array();

            $limit      = (isset($arguments['limit']) && $arguments['limit']) 
                            ? explode(',',$arguments['limit'])
                            : array(); 

            $returnType = (isset($arguments['returnType']) && $arguments['returnType']) 
                            ? $arguments['returnType']
                            : 0; 

            $collection = $this->mongo_db->get($this->db->dbprefix($table));

            if($returnType == 0 || $returnType == 2){
                $queryResult  = $collection->find($dbCondition,$returnData);
                if($returnType == 0){

                    if($orderBy){
                        $queryResult  = $queryResult->sort($orderBy);
                    } 

                    if(count($limit) > 1){
                        $queryResult  = $queryResult->skip($limit['1']);
                        $queryResult  = $queryResult->limit($limit['0']);
                    }else if(count($limit) == 1){
                        $queryResult  = $queryResult->limit($limit['0']);
                    }

                }else{
                    $queryResult  = $queryResult->count();
                }
            }else if($returnType == 1){
                $queryResult  = $collection->findOne($dbCondition,$returnData);
            }

            return $queryResult;

        }
    }

    // Basic DB CRUD Operations
    public function dbActionScript($arguments){

        if(isset($arguments['table']) && $arguments['table']){

            $table      = $arguments['table'];

            $action     = (isset($arguments['action']) && $arguments['action']) 
                            ? $arguments['action']
                            : '';

            $dbCondition= (isset($arguments['dbCondition']) && count($arguments['dbCondition'])) 
                            ? $arguments['dbCondition']
                            : array();

            $dbData     = (isset($arguments['dbData']) && count($arguments['dbData'])) 
                            ? $arguments['dbData']
                            : array();



            $result  = array();

            $collection = $this->mongo_db->db->selectCollection($this->db->dbprefix($table));

            if($action == 'add' && $dbData){
                $result = $collection->insert($dbData);
            }else if($action == 'edit'){
                $result = $collection->update($dbCondition, array('$set' => $dbData),array("upsert" => false));
            }else if($action == 'delete'){
                $result = $collection->remove($dbCondition);
            }

            return $result;

        }
        
    }

}