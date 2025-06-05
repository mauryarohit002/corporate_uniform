<?php defined('BASEPATH') OR exit('No direct script access allowed');
	require_once APPPATH . 'core/MY_Model.php';
	class measurement_model extends my_model{
		public function __construct(){ parent::__construct('master', 'measurement'); }
        public function isExist($id){
            // $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_pm_id = $id AND (bm_sot_mtr + bm_it_mtr + bm_prt_mtr) > 0 LIMIT 1")->result_array();
            // if(!empty($data)) return true;
    
            return false;
        }
        public function isTransExist($id){
            // $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_pt_id = $id AND (bm_sot_mtr + bm_it_mtr + bm_prt_mtr) > 0 LIMIT 1")->result_array();
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
			
			if(isset($_GET['_apparel_name']) && !empty($_GET['_apparel_name'])){
				$subsql .=' AND apparel.apparel_name = "'.$_GET['_apparel_name'].'"';
				$record['filter']['_apparel_name']['value'] = $_GET['_apparel_name'];
				$record['filter']['_apparel_name']['text'] = $_GET['_apparel_name'];
			}
            if(isset($_GET['_status'])){
				$status = $_GET['_status'] == 2 ? 0 : $_GET['_status'];
				$subsql .=" AND menu_status = ".$status;
				$record['filter']['_status'] = $this->Commonmdl->get_status($_GET['_status']);
			}
			$query="SELECT measurement.*,
                    UPPER(apparel.apparel_name) as apparel_name
					FROM measurement_master measurement
                    INNER JOIN apparel_master apparel ON(apparel.apparel_id = measurement.measurement_apparel_id)
					WHERE 1
					$subsql
					ORDER BY apparel.apparel_name ASC
					$limit
					$ofset";
			// echo "<pre>"; print_r($query); exit;
			if($wantCount){
				return $this->db->query($query)->num_rows();
			}
			$record['data'] = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($record); exit;
			return $record;
		}
		public function get_data_for_add(){
		}
		public function get_data_for_edit($id){
			$query="SELECT measurement.*,
					UPPER(apparel.apparel_name) as apparel_name
					FROM measurement_master measurement
					INNER JOIN apparel_master apparel ON(apparel.apparel_id = measurement.measurement_apparel_id)
					WHERE measurement.measurement_id = $id";
			$record['master_data'] = $this->db->query($query)->result_array();
			return $record;
		}
        public function get_transaction($id){
			$query="SELECT mmt.*,
					UPPER(maap.maap_name) as maap_name
					FROM measurement_maap_trans mmt
					INNER JOIN maap_master maap ON(maap.maap_id = mmt.mmt_maap_id)
					WHERE mmt.mmt_measurement_id = $id
					ORDER BY mmt.mmt_id ASC";
			$data['maap_data'] = $this->db->query($query)->result_array();
			$query="SELECT mst.*,
					UPPER(style.style_name) as style_name
					FROM measurement_style_trans mst
					INNER JOIN style_master style ON(style.style_id = mst.mst_style_id)
					WHERE mst.mst_measurement_id = $id
					ORDER BY mst.mst_id ASC";
			$data['style_data'] = $this->db->query($query)->result_array();
			return $data;
		}
        public function get_maap_data($id){
			$query="SELECT 0 as mmt_id,
					maap.maap_id as mmt_maap_id,
					UPPER(maap.maap_name) as maap_name
					FROM measurement_master measurement
					INNER JOIN measurement_maap_trans mmt ON(mmt.mmt_measurement_id = measurement.measurement_id)
					INNER JOIN maap_master maap ON(maap.maap_id = mmt.mmt_maap_id)
					WHERE measurement.measurement_status = 1
					AND measurement.measurement_id = $id
					ORDER BY mmt.mmt_id ASC";
			return $this->db->query($query)->result_array();
		}
		public function get_style_data($id){
			$query="SELECT 0 as mst_id,
					style.style_id as mst_style_id,
					UPPER(style.style_name) as style_name
					FROM measurement_master measurement
					INNER JOIN measurement_style_trans mst ON(mst.mst_measurement_id = measurement.measurement_id)
					INNER JOIN style_master style ON(style.style_id = mst.mst_style_id)
					WHERE measurement.measurement_status = 1
					AND measurement.measurement_id = $id
					ORDER BY mst.mst_id ASC";
			return $this->db->query($query)->result_array();
		}
		public function _id(){
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
				$subsql .= " AND (measurement_name LIKE '%".$name."%') ";
			}
			if(isset($_GET['param']) && !empty($_GET['param'])){
				$param 	= $_GET['param'];
				$subsql .= " AND (measurement_status = $param) ";
			}
			$query="SELECT measurement_id as id, UPPER(measurement_name) as name
					FROM measurement_master
					WHERE 1
					$subsql
					ORDER BY measurement_name ASC
					LIMIT $limit
					OFFSET $offset";
			// echo $query; exit();
			return $this->db->query($query)->result_array();
		}
		public function _apparel_name(){
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
				$subsql .= " AND (apparel_name LIKE '%".$name."%') ";
			}
			if(isset($_GET['param']) && !empty($_GET['param'])){
				$param 	= $_GET['param'];
				$subsql .= " AND (apparel_status = $param) ";
			}
			$query="SELECT apparel_name as id, UPPER(apparel_name) as name
					FROM apparel_master
					WHERE 1
					$subsql
					GROUP BY apparel_name ASC
					LIMIT $limit
					OFFSET $offset";
			// echo $query; exit();
			return $this->db->query($query)->result_array();
		}
		public function _maap_id(){
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
				$subsql .= " AND (apparel.apparel_name LIKE '%".$name."%') ";
			}
			if(isset($_GET['param']) && !empty($_GET['param'])){
				$param 	= $_GET['param'];
				$subsql .= " AND (measurement.measurement_status = $param) ";
			}
			$query="SELECT measurement.measurement_id as id, 
					UPPER(apparel.apparel_name) as name
					FROM measurement_master measurement
					INNER JOIN apparel_master apparel ON(apparel.apparel_id = measurement.measurement_apparel_id)
					INNER JOIN measurement_maap_trans mmt ON(mmt.mmt_measurement_id = measurement.measurement_id)
					WHERE 1
					$subsql
					GROUP BY apparel.apparel_id 
					ORDER BY apparel.apparel_name ASC
					LIMIT $limit
					OFFSET $offset";
			// echo $query; exit();
			return $this->db->query($query)->result_array();
		}
		public function _style_id(){
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
				$subsql .= " AND (apparel.apparel_name LIKE '%".$name."%') ";
			}
			if(isset($_GET['param']) && !empty($_GET['param'])){
				$param 	= $_GET['param'];
				$subsql .= " AND (measurement.measurement_status = $param) ";
			}
			$query="SELECT measurement.measurement_id as id, 
					UPPER(apparel.apparel_name) as name
					FROM measurement_master measurement
					INNER JOIN apparel_master apparel ON(apparel.apparel_id = measurement.measurement_apparel_id)
					INNER JOIN measurement_style_trans mst ON(mst.mst_measurement_id = measurement.measurement_id)
					WHERE 1
					$subsql
					GROUP BY apparel.apparel_id 
					ORDER BY apparel.apparel_name ASC
					LIMIT $limit
					OFFSET $offset";
			// echo $query; exit();
			return $this->db->query($query)->result_array();
		}
	}
?>