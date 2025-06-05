<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class size_model extends my_model{
	public function __construct(){ parent::__construct('master', 'size'); }
	public function isExist($id){
		// $data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_size_id = $id LIMIT 1")->result_array();
		// if(!empty($data)) return true;

		return false;
	}
    public function get_list($wantCount, $per_page = 20, $offset = 0){
        $record 	= [];
        $subsql 	= '';
        $limit  	= '';
        $ofset  	= '';
        
        if(!$wantCount){
            $limit .= " LIMIT $per_page";
            $ofset .= " OFFSET $offset";
        }
        
        if(isset($_GET['_size_name']) && !empty($_GET['_size_name'])){
            $subsql .=" AND size.size_name LIKE '%".$_GET['_size_name']."%'";
            $record['filter']['_size_name']['value'] = $_GET['_size_name'];
            $record['filter']['_size_name']['text'] = $_GET['_size_name'];
        }
       
        if(isset($_GET['_status'])){
            $status = $_GET['_status'] == 2 ? 0 : $_GET['_status'];
            $subsql .=" AND size.size_status = ".$status;
            $record['filter']['_status'] = $this->Commonmdl->get_status($_GET['_status']);
        }
        $query="SELECT size.*
                FROM size_master size
                WHERE 1
                $subsql
                ORDER BY size.size_name ASC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['size_id']);
            }
        }
        return $record;
    }
    public function get_data($id){
        $query="SELECT size.*
                FROM size_master size
                WHERE size.size_id = $id";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)) $data[0]['isExist'] = $this->isExist($id);
        return $data;
    }
    public function _id($args = []){
        $subsql = "";
        $limit  = PER_PAGE;
        $offset = OFFSET;
        $page 	= 1;
        if(isset($_GET['limit']) && !empty($_GET['limit'])){
            $limit = $_GET['limit'];
        }
        if(isset($_GET['page']) && !empty($_GET['page'])){
            $page 	= $_GET['page'];
            $offset = $limit * ($page - 1);
        }
        if(isset($_GET['name']) && !empty($_GET['name'])){
            $name 	= $_GET['name'];
            $subsql .= " AND (size.size_name LIKE '".$name."%') ";
        }
        // if(isset($_GET['param']) && !empty($_GET['param'])){
        //     $param 	= $_GET['param'];
        //     $subsql .= " AND (size.size_gender_id = $param) ";
        // }else{
        //     return [0 => ['id' => 0, 'name' => 'SELECT gender FIRST']];
        // }
        $query="SELECT 
                size.size_id as id, 
                UPPER(size.size_name) as name
                FROM size_master size
                WHERE 1
                $subsql
                GROUP BY size.size_id 
                ORDER BY size.size_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _size_name($args = []){
        $subsql = "";
        $limit  = PER_PAGE;
        $offset = OFFSET;
        $page 	= 1;
        if(isset($_GET['limit']) && !empty($_GET['limit'])){
            $limit = $_GET['limit'];
        }
        if(isset($_GET['page']) && !empty($_GET['page'])){
            $page 	= $_GET['page'];
            $offset = $limit * ($page - 1);
        }
        if(isset($_GET['name']) && !empty($_GET['name'])){
            $name 	= $_GET['name'];
            $subsql .= " AND (size.size_name LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param 	= $_GET['param'];
            $subsql .= " AND (size.size_status = $param) ";
        }
        $query="SELECT 
                size.size_name as id, 
                UPPER(size.size_name) as name
                FROM size_master size
                WHERE 1
                $subsql
                GROUP BY size.size_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
   
}
?>
