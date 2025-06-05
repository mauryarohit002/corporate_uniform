<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class design_model extends my_model{
	public function __construct(){ parent::__construct('master', 'design'); }
	public function isExist($id){
		$data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_design_id = $id LIMIT 1")->result_array();
		if(!empty($data)) return true;

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
        
        if(isset($_GET['_design_name']) && !empty($_GET['_design_name'])){
            $subsql .=" AND design.design_name LIKE '%".$_GET['_design_name']."%'";
            $record['filter']['_design_name']['value'] = $_GET['_design_name'];
            $record['filter']['_design_name']['text'] = $_GET['_design_name'];
        }
        if(isset($_GET['_supplier_name']) && !empty($_GET['_supplier_name'])){
            $subsql .=" AND supplier.supplier_name LIKE '%".$_GET['_supplier_name']."%'";
            $record['filter']['_supplier_name']['value'] = $_GET['_supplier_name'];
            $record['filter']['_supplier_name']['text'] = $_GET['_supplier_name'];
        }
        if(isset($_GET['_status'])){
            $status = $_GET['_status'] == 2 ? 0 : $_GET['_status'];
            $subsql .=" AND design.design_status = ".$status;
            $record['filter']['_status'] = $this->Commonmdl->get_status($_GET['_status']);
        }
        $query="SELECT design.*,
                UPPER(supplier.supplier_name) as supplier_name
                FROM design_master design
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = design.design_supplier_id)
                WHERE 1
                $subsql
                ORDER BY design.design_name ASC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['design_id']);
            }
        }
        return $record;
    }
    public function get_data($id){
        $query="SELECT design.*,
                UPPER(supplier.supplier_name) as supplier_name
                FROM design_master design
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = design.design_supplier_id)
                WHERE design.design_id = $id";
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
            $subsql .= " AND (design.design_name LIKE '%".$name."%') ";
        }
        // if(isset($_GET['param']) && !empty($_GET['param'])){
        //     $param 	= $_GET['param'];
        //     $subsql .= " AND (design.design_supplier_id = $param) ";
        // }else{
        //     return [0 => ['id' => 0, 'name' => 'SELECT SUPPLIER FIRST']];
        // }
        $query="SELECT 
                design.design_id as id, 
                UPPER(design.design_name) as name
                FROM design_master design
                WHERE 1
                $subsql
                GROUP BY design.design_id 
                ORDER BY design.design_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }

     public function _id2($args = []){ 
        $subsql = "";
        $limit  = PER_PAGE;
        $offset = OFFSET;
        $page   = 1;
        if(isset($_GET['limit']) && !empty($_GET['limit'])){
            $limit = $_GET['limit'];
        }
        if(isset($_GET['page']) && !empty($_GET['page'])){
            $page   = $_GET['page'];
            $offset = $limit * ($page - 1);
        }
        if(isset($_GET['name']) && !empty($_GET['name'])){
            $name   = $_GET['name'];
            $subsql .= " AND (design.design_name LIKE '%".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param   = $_GET['param'];
            $subsql .= " AND (design.design_supplier_id = $param) ";
        }else{
            return [0 => ['id' => 0, 'name' => 'SELECT SUPPLIER FIRST']];
        }
        $query="SELECT 
                design.design_id as id, 
                UPPER(design.design_name) as name
                FROM design_master design
                WHERE 1
                $subsql
                GROUP BY design.design_id 
                ORDER BY design.design_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _design_name($args = []){
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
            $subsql .= " AND (design.design_name LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param 	= $_GET['param'];
            $subsql .= " AND (design.design_status = $param) ";
        }
        $query="SELECT 
                design.design_name as id, 
                UPPER(design.design_name) as name
                FROM design_master design
                WHERE 1
                $subsql
                GROUP BY design.design_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _supplier_name($args = []){
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
            $subsql .= " AND (supplier.supplier_name LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param 	= $_GET['param'];
            $subsql .= " AND (supplier.supplier_status = $param) ";
        }
        $query="SELECT 
                supplier.supplier_name as id, 
                UPPER(supplier.supplier_name) as name
                FROM design_master design
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = design.design_supplier_id)
                WHERE 1
                $subsql
                GROUP BY supplier.supplier_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
}
?>
