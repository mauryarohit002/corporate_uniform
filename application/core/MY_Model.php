<?php 
    class my_model extends CI_model{
        protected $menu;
        protected $sub_menu;
		public $Commonmdl;
		public function __construct($menu, $sub_menu){
			parent::__construct();

            $this->menu     = $menu;
            $this->sub_menu = $sub_menu;

			$this->load->model('master/Commonmdl');
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
				$subsql .=" AND ".$this->sub_menu."_name LIKE '%".$_GET['_name']."%'";
				$record['filter']['_name']['value'] = $_GET['_name'];
				$record['filter']['_name']['text'] = $_GET['_name'];
			}
			if(isset($_GET['_status'])){
				$status = $_GET['_status'] == 2 ? 0 : $_GET['_status'];
				$subsql .=" AND ".$this->sub_menu."_status = ".$status;
				$record['filter']['_status'] = $this->Commonmdl->get_status($_GET['_status']);
			}
			$query="SELECT 
                    ".$this->sub_menu."_id as id, 
					".$this->sub_menu."_name as name, 
					".$this->sub_menu."_status as status,
					".$this->sub_menu."_default as isExist
					FROM ".$this->sub_menu."_master
					WHERE 1
					$subsql
					ORDER BY ".$this->sub_menu."_id DESC
					$limit
					$ofset";
			// echo "<pre>"; print_r($query); exit;
			if($wantCount){
				return $this->db->query($query)->num_rows();
			}
			$record['data'] = $this->db->query($query)->result_array();
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value){
					$record['data'][$key]['isExist'] = $value['isExist'] ?  $value['isExist'] : $this->isExist($value['id']);
				}
			}
			return $record;
		}
		public function get_data($id){
			$query="SELECT 
                    ".$this->sub_menu."_id as id, 
                    ".$this->sub_menu."_name as name,
                    ".$this->sub_menu."_status as status,
                    ".$this->sub_menu."_default as isExist
					FROM ".$this->sub_menu."_master
					WHERE ".$this->sub_menu."_id = $id";
			$data = $this->db->query($query)->result_array();
			if(!empty($data)) $data[0]['isExist'] = $this->isExist($id);
			return $data;
		}
		public function get_max_entry_no($args){
			$query="SELECT ".$args['entry_no']." as max_no
					FROM ".$this->sub_menu."_master
					WHERE ".$args['delete_status']." = 0
					AND ".$args['branch_id']." = '".$_SESSION['user_branch_id']."'
					AND ".$args['fin_year']." = '".$_SESSION['fin_year']."'
					ORDER BY ".$args['entry_no']." DESC
					LIMIT 1";
			$data = $this->db->query($query)->result_array();
			return empty($data) ? 1 : $data[0]['max_no']+1;
		}
		public function get_state(){
			$query="SELECT company.company_state_id as state_id
					FROM company_master company
					WHERE company.company_constant != ''";
			return $this->db->query($query)->result_array();
		}
		public function get_id_or_add($term, $name){
            $data = $this->db_operations->get_record($term.'_master', [$term.'_name' => $name]);
            if(!empty($data)) return $data[0][$term.'_id'];
            
            $master_data[$term.'_name']        = $name;
            $master_data[$term.'_status']      = 1;
            $master_data[$term.'_created_by']  = $_SESSION['user_id'];
            $master_data[$term.'_updated_by']  = $_SESSION['user_id'];
            $master_data[$term.'_created_at']  = date('Y-m-d H:i:s');
            return $this->db_operations->data_insert($term.'_master', $master_data);
        }
		// select2
			public function select_2($func){
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
				return $this->$func(['limit' => $limit, 'offset' => $offset]);
			}
			// list
				public function _id($args = []){
					$subsql = "";
					$limit  = isset($args['limit']) ? $args['limit'] : PER_PAGE;
					$offset = isset($args['offset']) ? $args['offset'] : OFFSET;

					if(isset($_GET['name']) && !empty($_GET['name'])){
						$param 	 = $_GET['name'];
						$subsql .= " AND (".$this->sub_menu."_name LIKE '".$param."%') ";
					}

					if(isset($_GET['args']['status'])){
						$param   = $_GET['args']['status'];
						$subsql .= " AND (".$this->sub_menu."_status = $param) ";
					}

					$query="SELECT 
							".$this->sub_menu."_id as id, 
							UPPER(".$this->sub_menu."_name) as name
							FROM ".$this->sub_menu."_master
							WHERE 1
							$subsql
							GROUP BY ".$this->sub_menu."_id
							ORDER BY ".$this->sub_menu."_name ASC
							LIMIT $limit
							OFFSET $offset";
					// echo $query; exit();
					return $this->db->query($query)->result_array();
				}
			// list

			// form
				public function _name($args = []){
					$subsql = "";
					$limit  = isset($args['limit']) ? $args['limit'] : PER_PAGE;
					$offset = isset($args['offset']) ? $args['offset'] : OFFSET;

					if(isset($_GET['name']) && !empty($_GET['name'])){
						$param 	 = $_GET['name'];
						$subsql .= " AND (".$this->sub_menu."_name LIKE '".$param."%') ";
					}

					if(isset($_GET['args']['status'])){
						$param   = $_GET['args']['status'];
						$subsql .= " AND (".$this->sub_menu."_status = $param) ";
					}
					
					$query="SELECT 
							".$this->sub_menu."_name as id, 
							UPPER(".$this->sub_menu."_name) as name
							FROM ".$this->sub_menu."_master
							WHERE 1
							$subsql
							GROUP BY ".$this->sub_menu."_name ASC
							LIMIT $limit
							OFFSET $offset";
					// echo $query; exit();
					return $this->db->query($query)->result_array();
				}
			// form
		// select2
	}
?>