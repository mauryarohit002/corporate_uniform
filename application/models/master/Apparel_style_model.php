<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class apparel_style_model extends my_model{
    public function __construct(){ parent::__construct('transaction', 'apparel_style'); }
    public function isExist($id){
        // $data = $this->db->query("SELECT asm_id FROM apparel_style_master WHERE asm_delete_status = 0 AND asm_id = $id LIMIT 1")->result_array();
        // if(!empty($data)) return true;

        return false;
    }
    public function isTransExist($id){
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
        
        if(isset($_GET['_name']) && !empty($_GET['_name'])){
            $subsql .=" AND asm.asm_name = '".$_GET['_name']."'";
            $record['filter']['_name']['value'] = $_GET['_name'];
            $record['filter']['_name']['text'] = $_GET['_name'];
        }
        $query="SELECT asm.*,
                (SELECT COUNT(ast.ast_id) as cnt FROM apparel_style_trans ast WHERE ast.ast_delete_status = 0 AND ast.ast_asm_id = asm.asm_id) as count
                FROM apparel_style_master asm
                WHERE asm.asm_delete_status = 0
                $subsql
                ORDER BY asm.asm_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['asm_id']);
            }
        }
        return $record;
    }
    public function get_data_for_add(){
        $record['asm_uuid'] = $_SESSION['user_id'].''.time();
        return $record;
    }
    public function get_data_for_edit($id){
        $query="SELECT asm.*
                FROM apparel_style_master asm
                WHERE asm.asm_id = $id
                AND asm.asm_delete_status = 0";
        $record['master_data'] = $this->db->query($query)->result_array();
        if(!empty($record['master_data'])){
            $record['master_data'][0]['isExist'] = $this->isExist($id);
        }
        // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_transaction($asm_id){
        $query="SELECT ast.*
                FROM apparel_style_trans ast
                WHERE ast.ast_asm_id = $asm_id
                AND ast.ast_delete_status = 0
                ORDER BY ast.ast_name ASC";
        $record = $this->db->query($query)->result_array();
        if(!empty($record)){
            foreach ($record as $key => $value) {
                $record[$key]['isExist'] = $this->isTransExist($value['ast_id']);
            }
        }
        return $record;
    }
    public function get_name($term, $id){
        $query="SELECT UPPER(".$term."_name) as name FROM ".$term."_master WHERE ".$term."_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? '' : $data[0]['name'];
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
			$subsql .= " AND (asm.asm_name LIKE '".$name."%') ";
		}
		$query="SELECT asm.asm_id as id, UPPER(asm.asm_name) as name
				FROM apparel_style_master asm
				WHERE asm.asm_delete_status = 0
				$subsql
				GROUP BY asm.asm_id
				ORDER BY asm.asm_name ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}

    public function _name($args = []){
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
			$subsql .= " AND (asm.asm_name LIKE '".$name."%') ";
		}
		$query="SELECT asm.asm_name as id, UPPER(asm.asm_name) as name
				FROM apparel_style_master asm
				WHERE asm.asm_delete_status = 0
				$subsql
				GROUP BY asm.asm_name ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}
}
?>