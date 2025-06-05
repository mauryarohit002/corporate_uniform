<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class fabric_model extends my_model{
	public function __construct(){ parent::__construct('master', 'fabric'); }
	public function isExist($id){
		$data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_fabric_id = $id LIMIT 1")->result_array();
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
        
        if(isset($_GET['_name']) && !empty($_GET['_name'])){
            $subsql .=" AND fabric.fabric_name LIKE '%".$_GET['_name']."%'";
            $record['filter']['_name']['value'] = $_GET['_name'];
            $record['filter']['_name']['text'] = $_GET['_name'];
        }
        if(isset($_GET['_status'])){
            $status = $_GET['_status'] == 2 ? 0 : $_GET['_status'];
            $subsql .=" AND fabric_status = ".$status;
            $record['filter']['_status'] = $this->Commonmdl->get_status($_GET['_status']);
        }
        $query="SELECT fabric.*,
                UPPER(fabric.fabric_name) as fabric_name
                FROM fabric_master fabric
                WHERE 1
                $subsql
                ORDER BY fabric.fabric_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['fabric_id']);
            }
        }
        return $record;
    }
	public function get_data($id){
		$query="SELECT *
				FROM fabric_master
				WHERE fabric_id = $id";
		$data = $this->db->query($query)->result_array();
		if(!empty($data)) $data[0]['isExist'] = $this->isExist($data[0]['fabric_id']);
		return $data;
	}
}
?>
