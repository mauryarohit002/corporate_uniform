<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class product_model extends my_model{
	public function __construct(){ parent::__construct('master', 'product'); }
	public function isExist($id){
		// $data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_product_id = $id LIMIT 1")->result_array();
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
        
        if(isset($_GET['_product_name']) && !empty($_GET['_product_name'])){
            $subsql .=" AND product.product_name LIKE '%".$_GET['_product_name']."%'";
            $record['filter']['_product_name']['value'] = $_GET['_product_name'];
            $record['filter']['_product_name']['text'] = $_GET['_product_name'];
        }
      
        if(isset($_GET['_status'])){
            $status = $_GET['_status'] == 2 ? 0 : $_GET['_status'];
            $subsql .=" AND product_status = ".$status;
            $record['filter']['_status'] = $this->Commonmdl->get_status($_GET['_status']);
        }
        $query="SELECT product.product_id, 
                UPPER(product.product_name) as product_name, 
                UPPER(product.product_specification) as product_specification, 
                product.product_status
                FROM product_master product
                WHERE 1
                $subsql
                ORDER BY product.product_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['product_id']);
            }
        }
        return $record;
    }
	public function get_data($id){
        $query="SELECT product.*
                FROM product_master product
                WHERE product.product_id = $id";
        return $this->db->query($query)->result_array();
    }


     public function _product_name(){
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
            $subsql .= " AND (product.product_name LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param  = $_GET['param'];
            $subsql .= " AND (product.product_status = $param) ";
        }
        $query="SELECT 
                product.product_name as id, 
                UPPER(product.product_name) as name
                FROM product_master product
                WHERE 1
                $subsql
                GROUP BY product.product_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }

    public function _category_name(){
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
            $subsql .= " AND (category.readymade_category_name LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param  = $_GET['param'];
            $subsql .= " AND (category.readymade_category_status = $param) ";
        }
        $query="SELECT 
                category.readymade_category_name as id, 
                UPPER(category.readymade_category_name) as name
                FROM readymade_category_master category
                WHERE 1
                $subsql
                GROUP BY category.readymade_category_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
}
?>
