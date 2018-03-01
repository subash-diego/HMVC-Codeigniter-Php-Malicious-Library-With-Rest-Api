<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Commonmysqlmodel extends CI_Model {

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

            
            
            if(!empty($dbCondition))
            {
               $dbCondition ? $this->db->where($dbCondition) : '';
            }
            
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
           //echo $this->db->last_query(); die;
        
            return json_encode($queryResult);
            //return $arguments;
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

    // Basic CRM DB CRUD Operations
    public function crmDbActionScript($arguments){

        if(isset($arguments['table']) && $arguments['table']){

            $table      = $arguments['table'];

            $action = (isset($arguments['action']) && $arguments['action']) 
                            ? $arguments['action']
                            : '';

            $returnData  = array();

            if($action == 'add'){
                $result = $this->crmdb->insert($this->crmdb->dbprefix($table),$arguments['dbData']);
                $returnData = array(
                    'result'        =>  $result,
                    'insertId'      =>  $result ? $this->crmdb->insert_id() : 0,
                    'error'         =>  $this->crmdb->error()
                );
            }else if($action == 'edit'){
                $this->crmdb->where($arguments['dbCondition']);
                $result = $this->crmdb->update($this->crmdb->dbprefix($table),$arguments['dbData']);
                $returnData = array(
                    'result'        =>  $result,
                    'affectedRows'  =>  $result ? $this->crmdb->affected_rows() : 0,
                    'error'         =>  $this->crmdb->error()
                );
            }else if($action == 'delete'){
                $this->crmdb->where($arguments['dbCondition']);
                $result = $this->crmdb->delete($this->crmdb->dbprefix($table));
                $returnData = array(
                    'result'        =>  $result,
                    'affectedRows'  =>  $result ? $this->crmdb->affected_rows() : 0,
                    'error'         =>  $this->crmdb->error()
                );
            }

            return $returnData;

        }
        
    }

    // All CRM MySQl DB Operations Performs Here except CRUD Operations.
    public function getCrmMasterList($arguments){
        
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

            $this->crmdb->select($returnData);

            $this->crmdb->from($this->crmdb->dbprefix($table).$tableAlias);

            $dbCondition ? $this->crmdb->where($dbCondition) : '';

            if(count($joins)){
                foreach ($joins as $field) {
                    if(isset($field['table'])){
                        $type = isset($field['type']) ? $field['type'] : '';
                        $match = isset($field['match']) ? $field['match'] : '';
                        $this->crmdb->join($this->crmdb->dbprefix($field['table'])." AS ".$field['alias'],$match,$type);
                    }
                }
            }

            $like ? $this->crmdb->like($like) : '';

            $orLike ? $this->crmdb->or_like($orLike) : '';

            $notLike ? $this->crmdb->not_like($notLike) : '';

            if(count($orderBy)){
                foreach ($orderBy as $field => $order) {
                    $this->crmdb->order_by($field,$order);
                }
            }

            $groupBy ? $this->crmdb->group_by($groupBy) : '';

            $limit ? (count($limit) > 1 ? $this->crmdb->limit($limit['0'],$limit['1']) : $this->crmdb->limit($limit['0'])) : '';

            $query  = $this->crmdb->get();

            $queryResult = $returnType ? ($returnType == 1 ? $query->row_array() : $query->num_rows()) : $query->result_array();

            //for check last execute query
            //echo $this->db->last_query();

            return $queryResult;
        }

    }

    // Get all study level list 
    public function getStudyLevelList($studyLevelId=''){

        $dbCondition = array();
        $dbCondition['status ='] = 0;
        $dbCondition['isDeleted ='] = 0;
        if($studyLevelId){
            $dbCondition['studyLevelId ='] = $studyLevelId;
        }

        $arguments  =   array(
            'table'         =>  "studylevel",
            'dbCondition'   =>  $dbCondition,
            'returnType'    =>  $studyLevelId ? 1 : 0,
        );

        return $this->getMasterList($arguments);
    }

    // Get study level details by level Id
    public function getStudyLevelDetails($studyLevelId){
        return $this->getStudyLevelList($studyLevelId);
    }

    // Get all study area list 
    public function getStudyAreaList($studyAreaId=''){

        $dbCondition = array();
        $dbCondition['status ='] = 0;
        $dbCondition['isDeleted ='] = 0;
        if($studyAreaId){
            $dbCondition['studyAreaId ='] = $studyAreaId;
        }

        $arguments  =   array(
            'table'         =>  "studyarea",
            'dbCondition'   =>  $dbCondition,
            'returnType'    =>  $studyAreaId ? 1 : 0,
        );

        return $this->getMasterList($arguments);
    }

    // Get study area details by area Id 
    public function getStudyAreaDetails($studyAreaId){
        return $this->getStudyAreaList($studyAreaId);
    }


    // Get all institutiontype list 
    public function getInstitutionTypeList($institutionId=''){

        $dbCondition = array();
        $dbCondition['status ='] = 0;
        $dbCondition['isDeleted ='] = 0;

        if($institutionId){
            $dbCondition['institutionId ='] = $institutionId;
        }

        $arguments  =   array(
            'table'         =>  "institution",
            'dbCondition'   =>  $dbCondition,
            'returnType'    =>  $institutionId ? 1 : 0,
        );

        return $this->getMasterList($arguments);
    }

    // Get Institution type details by institutionId
    public function getInstitutionTypeDetails($institutionId){
        return $this->getInstitutionTypeList($institutionId);
    }


    // Get all branch list 
    public function getBranchList($branchId='', $studyAreaId=''){

        $dbCondition = array();
        $dbCondition['status ='] = 0;
        $dbCondition['isDeleted ='] = 0;
        if($studyAreaId){
            $dbCondition['studyAreaId ='] = $studyAreaId;
        }

        if($branchId){
            $dbCondition['branchId ='] = $branchId;
        }

        $arguments  =   array(
            'table'         =>  "branches",
            'dbCondition'   =>  $dbCondition,
            'returnType'    =>  $branchId ? 1 : 0,
        );

        return $this->getMasterList($arguments);
    }

    // Get branch details by branch Id 
    public function getBranchDetails($branchId, $studyAreaId=''){
        return $this->getBranchList($branchId, $studyAreaId);
    }

    // Get all Blogs list 
    public function getBlogList($params=array()){

        $arguments  =   array(
            'table'      =>  "blogdetails",
            'returnData' =>  "blogId,blogTitle,blogUrl,featuredPost,metaTitle,metaTagDescription,metaTagKeyword,dateCreated,coverImage,viewCount,status,description",
            'dbCondition'=>  isset($params['blogId']) ? array('blogId =' => $params['blogId']) : '',
            'orderBy'    =>  array('viewCount', 'DESC'),
            'limit'      =>  isset($params['limit']) ? $params['limit'] : '',
        );

        return $this->getMasterList($arguments);
    }

    // Get Blog details by Blog Id 
    public function getBlogDetails($blogId){
        $arguments['blogId'] = $blogId;
        return $this->getBlogList($arguments);
    }

    // Get all colleges list based by masters
    public function getCollegesList($dbCondition=array(), $limit='',$start=''){
        
        $joins  =   array(   
                        array(
                            'table' => 'course',
                            'alias' => 'CO',
                            'match' => 'CO.collegeId = C.collegeId',
                            'type'  => 'INNER'
                        ),
                        array(
                            'table' => 'studylevel',
                            'alias' => 'SL',
                            'match' => 'SL.studyLevelId = CO.studyLevelId',
                            'type'  => 'INNER'
                        ),
                        array(
                            'table' => 'studyarea',
                            'alias' => 'SA',
                            'match' => 'SA.studyAreaId = CO.studyAreaId',
                            'type'  => 'INNER'
                        ),
                        array(
                            'table' => 'branches',
                            'alias' => 'B',
                            'match' => 'CO.branchId = B.branchId',
                            'type'  => 'INNER'
                        ),
                        array(
                            'table' => 'city',
                            'alias' => 'CI',
                            'match' => 'C.cityId = CI.cityId',
                            'type'  => 'LEFT'
                        ),
                        array(
                            'table' => 'state',
                            'alias' => 'S',
                            'match' => 'C.stateId = S.stateId',
                            'type'  => 'LEFT'
                        ),
                        array(
                            'table' => 'country',
                            'alias' => 'COU',
                            'match' => 'C.countryId = COU.countryId',
                            'type'  => 'INNER'
                        ),
                        array(
                            'table' => 'institution',
                            'alias' => 'I',
                            'match' => 'C.institutionType = I.institutionId',
                            'type'  => 'INNER'
                        )
                    );
        if(!empty($dbCondition['collegeOrder']))
        {
            $collegeOrder = isset($dbCondition['collegeOrder']['collegeOrderType']) ? $dbCondition['collegeOrder']['collegeOrderType'] : '';
            $orderBy = array('C.collegeName' => $collegeOrder);
        }
        if(!empty($dbCondition['ratingOrder']))
        {
            $ratingOrder = isset($dbCondition['ratingOrder']['ratingsOrderType']) ? $dbCondition['ratingOrder']['ratingsOrderType'] : '';
            $orderBy = array('C.setara' => $ratingOrder);
        }
        if(empty($dbCondition['ratingOrder']) && empty($dbCondition['collegeOrder']))
        {
            $orderBy = array('C.collegeName' => 'ASC');
        }
        $basicCondition['COMMON'] = array(
            'C.status ='        => 0,
            'C.isDeleted ='     => 0
        );

        $dbCondition = array_merge($basicCondition,$dbCondition);

        $arguments  =   array(
            'table'        =>  "college",
            'tableAlias'   =>  "C",
            'returnData'   =>  "DISTINCT(C.collegeId), C.collegeName, C.description, C.logo, CI.name as city, I.name as institutionTypeName, S.name as state, C.establishedYear, C.officialWebsite, C.address, C.email1, COU.name as country, C.phoneNumber1, C.fbUrl, C.twitterUrl, C.facility, C.cityId, C.institutionType, C.seoURI, C.postalCode, C.rate, C.setara, SL.seoURI as levelUri, SA.seoURI as areaUri, C.collegeCode,C.seoTitle,C.seoDescription,C.campusSize,C.CampusLocationDescription, C.campusLocationDescription2, C.isClosed,C.collegeCode",
            'dbCondition'  =>  $dbCondition ? $dbCondition : '',
            'joins'        =>  $joins,
            'groupBy'      =>  array('C.collegeId'),
            'orderBy'      =>  $orderBy,
            'limit'        =>  $limit ? $limit : '',
            'returnType'   =>  isset($dbCondition['COMMON']['C.collegeId=']) ? 1 : '',
        );
        
        $collegesList     = $this->getMasterList($arguments);
        return $collegesList;
    }

    // Get all Scholarship list 
    public function getScholarshipListDetails($dbCondition=array(), $limit='',$start=''){
        $joins  =   array(   
                        array(
                            'table' => 'nationality',
                            'alias' => 'NA',
                            'match' => 'SD.scholarship_nationality = NA.nationalityId',
                            'type'  => 'LEFT'
                        ),
                        array(
                            'table' => 'college',
                            'alias' => 'C',
                            'match' => 'C.collegeId = SD.collegeId',
                            'type'  => 'INNER'
                        )
                    );
        if(!empty($dbCondition['deadlineOrder']))
        {
            $deadlineOrder = isset($dbCondition['deadlineOrder']['deadlineOrderType']) ? $dbCondition['deadlineOrder']['deadlineOrderType'] : '';
            $orderBy = array('SD.deadline' => $deadlineOrder);
        }
        if(!empty($dbCondition['featuredOrder']))
        {
            $featuredOrder = isset($dbCondition['featuredOrder']['featuredOrderType']) ? $dbCondition['featuredOrder']['featuredOrderType'] : '';
            $orderBy = array('C.isClosed' => $featuredOrder);
        }
        if(empty($dbCondition['deadlineOrder']) && empty($dbCondition['featuredOrder']))
        {
            $orderBy = array('SD.deadline' => 'ASC');
        }
        $arguments   =   array(
            'table'        =>  "scholarshipdetails",
            'tableAlias'   =>  "SD",
            'returnData'   =>  "SD.*,NA.name as nationality,C.isClosed as featured",
            'dbCondition'  =>  $dbCondition,
            'joins'        =>  $joins,
            'orderBy'      =>  $orderBy,
            'limit'        =>  $limit ? $limit : '',
            'returnType'   =>  isset($dbCondition['COMMON']['SD.scholarshipId=']) ? 1 : '',
        );
        
        return $this->getMasterList($arguments);
    }

    public function getCollegeDetailsByCollegeId($collegeId){
        $dbCondition['COMMON'] = array('C.isDeleted =' => 0, 'C.status =' => 0, 'C.collegeId=' => $collegeId);
        return $this->getCollegesList($dbCondition);
    }

    // Get college rating details for all colleges
    public function getCollegeRatingCount(){
        $arguments   =   array(
            'table'        =>  "collegeallrating",
            'returnData'   =>  "collegeId, count(collegeId) as totalReviews, (sum(`facultyCount`) + sum(`placementCount`) + sum(`infrastructureCount`) + sum(`collegeLifeCount`)) as totalCount,(sum(`placementRating`) + sum(`infrastructureRating`) + sum(`collegeLifeRating`) + sum(`facultyRating`)) as totalRating",
            'groupBy'      =>  array('collegeId')
        );
        $results = $this->getMasterList($arguments);

        $rating=array();
        if(count($results)){
            foreach ($results as $key => $value) {
                $rating[$value['collegeId']] = round($value['totalRating']/$value['totalCount']);
                $rating['total-reviews-'.$value['collegeId']] = $value['totalReviews'];
            }
        }
        return $rating;
    }

    // Get all Scholarship list 
    public function getScholarshipList($collegeId='',$studyAreaId=''){
        $joins  =   array(   
                        array(
                            'table' => 'scholarshipdetails',
                            'alias' => 'SD',
                            'match' => 'S.scholarshipId = SD.scholarshipId',
                            'type'  => 'LEFT'
                        ),
                        array(
                            'table' => 'studylevel',
                            'alias' => 'SL',
                            'match' => 'SD.studyLevelId = SL.studyLevelId',
                            'type'  => 'LEFT'
                        ),
                        array(
                            'table' => 'studyarea',
                            'alias' => 'SA',
                            'match' => 'SD.studyAreaId = SA.studyAreaId',
                            'type'  => 'LEFT'
                        )
                    );

        $dbCondition = array();
        if($collegeId){
            $dbCondition['SD.collegeId ='] = $collegeId;
        }
        if($studyAreaId){
            $dbCondition['SD.studyAreaId ='] = $studyAreaId;
        }

        $arguments   =   array(
            'table'        =>  "scholarship",
            'tableAlias'   =>  "S",
            'returnData'   =>  "S.scholarshipName,SD.*,SL.name as studyLevel,SA.name as studyArea",
            'dbCondition'  =>  $dbCondition,
            'joins'        =>  $joins
        );
        
        return $this->getMasterList($arguments);
    }

    // Get Scholarship Details based by collegeId
    public function getScholarshipDetails($collegeId,$studyAreaId=''){
        return $this->getScholarshipList($collegeId,$studyAreaId);
    }

    // Get applied scholarship Ids based by Dynamic Application Id
    public function getAppliedScholarshipByDynamicApplicationId($dynamicApplicationId=''){

        $arguments   =   array(
            'table'        =>  "appliedscholarshipdetails",
            'returnData'   =>  "scholarshipdetailsId",
            'dbCondition'  =>  $dynamicApplicationId ? array('dynamicApplicationId =' => $dynamicApplicationId) : ''
        );
        $appliedIds = $this->getMasterList($arguments);

        $results = array();
        if(!empty($appliedIds)){
            foreach ($appliedIds as $applied) {
                $results[] = $applied['scholarshipdetailsId'];
            }
        }
        return $results;
    }

    //Get Location Details
    public function getLocationDetails($table,$dbCondition=array()){

        $arguments   =   array(
            'table'        =>  $table,
            'returnData'   =>  "name,seoURI,".$table."Id",
            'dbCondition'  =>  $dbCondition ? $dbCondition : array()
        );
        $locationDetails = $this->getMasterList($arguments);
        return $locationDetails;
    }

    //Get Country List
    public function getCountryList($dbCondition=array()){
        return $this->getLocationDetails('country',$dbCondition);
    }

    //Get State List
    public function getStateList($dbCondition=array()){
        return $this->getLocationDetails('state',$dbCondition);
    }

    //Get city List
    public function getCityList($dbCondition=array()){
        return $this->getLocationDetails('city',$dbCondition);
    }


    //Get Applied Course List Details
    public function getAppliedCoursesList($arguments){
        return $this->getMasterList($arguments);
    }

    //Get Applied Course List Details By Dynamic Application Id
    public function getAppliedCoursesByDynamicApplicationId($dynamicApplicationId){
        $arguments   =   array(
            'table'        =>  "appliedcourses",
            'dbCondition'  =>  array('dynamicApplicationId =' => $dynamicApplicationId, 'courseId !=' => '', 'collegeid !=' => '')
        );
        return $this->getMasterList($arguments);
    }

    //Get Enquiry College List Details By Dynamic Application Id
    public function getCollegeEnquiriesByDynamicApplicationId($dynamicApplicationId){

        $dbCondition = array(
            'dynamicApplicationId =' => $dynamicApplicationId,
            'requestFor ='           => "college"        
        );

        $arguments   =   array(
            'table'        =>  "requestedinfo",
            'returnData'   =>  "collegeId",
            'dbCondition'  =>  $dbCondition
        );
        $collegeEnquiries = $this->getMasterList($arguments);
        if(!empty($collegeEnquiries)){
            foreach ($collegeEnquiries as $enquiry) {
                $results[] = $enquiry['collegeId'];
            }
        }
        return $results;
    }

    //Get Enquiry Course List Details By Dynamic Application Id
    public function getCourseEnquiriesByDynamicApplicationId($dynamicApplicationId){

        $dbCondition = array(
            'dynamicApplicationId =' => $dynamicApplicationId,
            'requestFor ='           => "course"        
        );

        $arguments   =   array(
            'table'        =>  "requestedinfo",
            'returnData'   =>  "courseId",
            'dbCondition'  =>  $dbCondition
        );
        $courseEnquiries = $this->getMasterList($arguments);
        if(!empty($courseEnquiries)){
            foreach ($courseEnquiries as $enquiry) {
                $results[] = $enquiry['courseId'];
            }
        }
        return $results;
    }


    //get all course list
    public function getCourseList($dbCondition=array(), $limit){

        $joins  =   array(  
                        array(
                            'table' => 'college',
                            'alias' => 'C',
                            'match' => 'C.collegeId = CO.collegeId'
                        ), 
                        array(
                            'table' => 'studylevel',
                            'alias' => 'SL',
                            'match' => 'SL.studyLevelId = CO.studyLevelId'
                        ),
                        array(
                            'table' => 'studyarea',
                            'alias' => 'SA',
                            'match' => 'SA.studyAreaId = CO.studyAreaId'
                        ),                        
                        array(
                            'table' => 'branches',
                            'alias' => 'B',
                            'match' => 'CO.branchId = B.branchId',
                            'type'  => 'LEFT'
                        ),
                        array(
                            'table' => 'courseallrating',
                            'alias' => 'CR',
                            'match' => 'CO.courseId = CR.courseId',
                            'type'  => 'LEFT'
                        ),
                        array(
                            'table' => 'modeofstudy',
                            'alias' => 'M',
                            'match' => 'CO.modeOfStudy = M.modeOfStudyId',
                            'type'  => 'LEFT'
                        ),                        
                        array(
                            'table' => 'city',
                            'alias' => 'CI',
                            'match' => 'C.cityId=CI.cityId',
                            'type'  => 'LEFT'
                        ),
                        array(
                            'table' => 'state',
                            'alias' => 'S',
                            'match' => 'C.stateId=S.stateId',
                            'type'  => 'LEFT'
                        ),
                        array(
                            'table' => 'institution',
                            'alias' => 'I',
                            'match' => 'C.institutionType=I.institutionId',
                            'type'  => 'LEFT'
                        )
                    );

        //basic filters
        $defaultCondition['COMMON'] = array(
            'CO.status ='        => 0,
            'CO.isDeleted ='     => 0,
            'CO.isVisible ='     => 1,
            'C.isDeleted ='      => 0,
        );

        //if group by flag appear means (for get studyarea count)
        if(isset($dbCondition['groupBy='])){
           $groupBy =  array($dbCondition['groupBy=']); 
           unset($dbCondition['groupBy=']);
        }

        //merge both filters
        $dbCondition = array_merge($defaultCondition['COMMON'],$dbCondition);
        
        $arguments  =   array(
            'table'        =>  "course",
            'tableAlias'   =>  "CO",
            'returnData'   =>  "CO.courseId, C.collegeId, C.collegeName, C.establishedYear, C.logo, SL.studyLevelId, SL.name as studyLevel, CR.totalReviews, CR.avgRating, CO.studyAreaId, SA.name as studyAreaName, SA.seoURI, CO.seoURI as courseURI, CO.courseName, CO.branchId,B.branchName, SA.seoURI as studyAreaURI, CO.courseDuration, CO.intakesId, CO.localFeesAmount, CO.localFeesCurrency, M.modeOfStudy, CO.description, C.seoURI as collegeURI,CO.seoURI as courseURI, SL.seoURI as studyLevelURI, count(CO.courseId) as courseCount, CO.entryRequirementsData,S.name as state, CI.name as city, I.name AS institutionType,C.seoURI as collegeSeoURI,C.collegeCode", 

            'dbCondition'  =>  $dbCondition ? $dbCondition : '',
            'limit'        =>  $limit ? $limit : '',
            'joins'        =>  $joins,
            'groupBy'      =>  isset($groupBy) ? $groupBy : array('CO.courseId'),
            'orderBy'      =>  array('CO.courseId' => 'ASC'),
            'returnType'   =>  isset($dbCondition['CO.courseId=']) ? 1 : ''

        );
        
        $courseList  = $this->getMasterList($arguments);
        return $courseList;
        
    }

    //get course list by collegeId
    public function getCourseDetailsByCollegeId($dbCondition){
        return $this->getCourseList($dbCondition);
    }

    //get course list by CourseId
    public function getCourseDetailsByCourseId($dbCondition, $limit=''){
        //echo $limit;exit;
        return $this->getCourseList($dbCondition, $limit);
    }

    // Update Student Academic Details
    public function updateAcademicDetails($dynamicApplicationId, $applciationDetails){
        if($applciationDetails){
            $collection = $this->mongo_db->db->selectCollection($this->db->dbprefix('academicdetails'));
            $collection->remove(array('$or' => array(array("studentId" => $dynamicApplicationId),array("userBrowserId" => (int)$_COOKIE['userBrowserId']))));

            $this->crmdb->where('studentId', $dynamicApplicationId);
            $this->crmdb->or_where('userBrowserId', (int)$_COOKIE['userBrowserId']);
            $this->crmdb->delete($this->crmdb->dbprefix('academicdetails'));

            foreach($applciationDetails['academic'] as $key => $academic){
                $insertData = array();
                if($academic['subject'] && $academic['grade']){
                    $insertData['qualification']  = $applciationDetails['qualification'];
                    $insertData['subject']        = $academic['subject'];
                    $insertData['grade']          = $academic['grade'];
                    $insertData['studentId']      = $dynamicApplicationId;
                    $insertData['userBrowserId']  = (int)$_COOKIE['userBrowserId'];
                    $insertData['dateAdded']      = new MongoDate(strtotime(date('Y-m-d H:i:s')));
                    $collection->insert($insertData);
                    unset($insertData['dateAdded']);unset($insertData['_id']);
                    $this->crmdb->insert($this->crmdb->dbprefix('academicdetails'),$insertData);
                }
            }
        }
    }

}
