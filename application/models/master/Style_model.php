<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class style_model extends my_model{
	public function __construct(){ parent::__construct('master', 'style'); }
	public function isExist($id){
		// $data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_style_id = $id LIMIT 1")->result_array();
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
        
        if(isset($_GET['_name']) && !empty($_GET['_name'])){
            $subsql .="AND style_name LIKE '%".$_GET['_name']."%'";
            $record['filter']['_name']['value'] = $_GET['_name'];
            $record['filter']['_name']['text'] = $_GET['_name'];
        }
        if(isset($_GET['_group']) && !empty($_GET['_group'])){
            $subsql .="AND style_group LIKE '%".$_GET['_group']."%'";
            $record['filter']['_group']['value'] = $_GET['_group'];
            $record['filter']['_group']['text'] = $_GET['_group'];
        }
        if(isset($_GET['_status'])){
            $status = $_GET['_status'] == 2 ? 0 : $_GET['_status'];
            $subsql .=" AND style_status = ".$status;
            $record['filter']['_status'] = $this->Commonmdl->get_status($_GET['_status']);
        }
        $query="SELECT *,
                style_default as isExist
                FROM style_master
                WHERE 1
                $subsql
                ORDER BY style_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist']    = $value['isExist'] ?  $value['isExist'] : $this->isExist($value['style_id']);
            }
        }
        return $record;
    }
	public function get_data($id){
		$query="SELECT *
				FROM style_master
				WHERE style_id = $id";
		return $this->db->query($query)->result_array();
	}
	public function _group(){
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
            $subsql .= " AND (style_group LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param 	= $_GET['param'];
            $subsql .= " AND (city_status = $param) ";
        }
        $query="SELECT style_group as id, UPPER(style_group) as name
                FROM style_master
                WHERE 1
                $subsql
                GROUP BY style_group ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
}
?>
