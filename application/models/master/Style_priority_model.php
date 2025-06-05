<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class style_priority_model extends my_model{
    public function __construct(){ parent::__construct('transaction', 'style_priority'); }
    public function isExist($id){
        // $data = $this->db->query("SELECT spm_id FROM style_priority_master WHERE spm_delete_status = 0 AND spm_id = $id LIMIT 1")->result_array();
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
        
        if(isset($_GET['_apparel_name']) && !empty($_GET['_apparel_name'])){
            $subsql .=" AND apparel.apparel_name = '".$_GET['_apparel_name']."'";
            $record['filter']['_apparel_name']['value'] = $_GET['_apparel_name'];
            $record['filter']['_apparel_name']['text'] = $_GET['_apparel_name'];
        }
        $query="SELECT spm.*,
                UPPER(apparel.apparel_name) as apparel_name,
                (SELECT COUNT(spt.spt_id) as cnt FROM style_priority_trans spt WHERE spt.spt_delete_status = 0 AND spt.spt_spm_id = spm.spm_id) as count
                FROM style_priority_master spm
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = spm.spm_apparel_id)
                WHERE spm.spm_delete_status = 0
                $subsql
                ORDER BY spm.spm_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['spm_id']);
            }
        }
        return $record;
    }
    public function get_data_for_add(){
        $record['spm_uuid'] = $_SESSION['user_id'].''.time();
        return $record;
    }
    public function get_data_for_edit($id){
        $query="SELECT spm.*,
                UPPER(apparel.apparel_name) as apparel_name
                FROM style_priority_master spm
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = spm.spm_apparel_id)
                WHERE spm.spm_id = $id
                AND spm.spm_delete_status = 0";
        $record['master_data'] = $this->db->query($query)->result_array();
        if(!empty($record['master_data'])){
            $record['master_data'][0]['isExist'] = $this->isExist($id);
        }
        // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_transaction($spm_id){
        $query="SELECT spt.*,
                UPPER(asm.asm_name) as asm_name
                FROM style_priority_trans spt
                INNER JOIN apparel_style_master asm ON(asm.asm_id = spt.spt_asm_id)
                WHERE spt.spt_spm_id = $spm_id
                AND spt.spt_delete_status = 0
                ORDER BY spt.spt_priority ASC";
        $record = $this->db->query($query)->result_array();
        if(!empty($record)){
            foreach ($record as $key => $value) {
                $record[$key]['isExist'] = $this->isTransExist($value['spt_id']);
            }
        }
       
        return $record;
    }
    public function get_priority_data($spm_id){
        $query="SELECT spt.*,
                UUID() as spt_id,
                0 as spt_spm_id,
                UPPER(asm.asm_name) as asm_name
                FROM style_priority_trans spt
                INNER JOIN apparel_style_master asm ON(asm.asm_id = spt.spt_asm_id)
                WHERE spt.spt_spm_id = $spm_id
                AND spt.spt_delete_status = 0
                ORDER BY spt.spt_priority ASC";
        return $this->db->query($query)->result_array();
    }
    public function get_name($id){
        $query="SELECT UPPER(asm_name) as name FROM apparel_style_master WHERE asm_id = $id";
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
			$subsql .= " AND (spm.spm_name LIKE '".$name."%') ";
		}
		$query="SELECT spm.spm_id as id, UPPER(spm.spm_name) as name
				FROM style_priority_master spm
				WHERE spm.spm_delete_status = 0
				$subsql
				GROUP BY spm.spm_id
				ORDER BY spm.spm_name ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}

    public function _spm_id($args = []){
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
			$subsql .= " AND (spm.spm_name LIKE '".$name."%') ";
		}
        if(isset($_GET['param']) && !empty($_GET['param'])){
			$param 	= $_GET['param'];
			$subsql .= " AND (spm.spm_id != $param) ";
		}
		$query="SELECT spm.spm_id as id, UPPER(apparel.apparel_name) as name
				FROM style_priority_master spm
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = spm.spm_apparel_id)
				WHERE spm.spm_delete_status = 0
				$subsql
				GROUP BY spm.spm_id
				ORDER BY apparel.apparel_name ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}

    public function _apparel_name($args = []){
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
			$subsql .= " AND (apparel.apparel_name LIKE '".$name."%') ";
		}
		$query="SELECT apparel.apparel_name as id, UPPER(apparel.apparel_name) as name
				FROM style_priority_master spm
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = spm.spm_apparel_id)
				WHERE spm.spm_delete_status = 0
				$subsql
				GROUP BY apparel.apparel_name ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}
}
?>