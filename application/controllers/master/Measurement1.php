<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class measurement extends my_controller{
    protected $menu;
    protected $sub_menu;
	public function __construct(){
        $this->menu     = 'master'; 
        $this->sub_menu = 'measurement'; 
        parent::__construct($this->menu, $this->sub_menu); 
    }
	public function remove(){
		$post_data  = $this->input->post();
		$id         = $post_data['id'];

		$result = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
		if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

		$data = $this->db_operations->get_record($this->sub_menu.'_master', ['measurement_id' => $id]);
		if(empty($data)) return ['status' => REFRESH, 'msg' => '1. Measurement not found.'];	

		if($this->model->isExist($id)) return ['msg' => 'Not allowed to delete.'];

		$this->db->trans_begin();
		
		$trans_data = $this->db_operations->get_record($this->sub_menu.'_maap_trans', ['mmt_measurement_id' => $id]);
		if(!empty($trans_data)){
			if($this->db_operations->delete_record($this->sub_menu.'_maap_trans', ['mmt_measurement_id' => $id]) < 1){
				$this->db->trans_rollback();
				return ['msg' => '1. Maap transaction not deleted.'];
			}
		}

		$trans_data = $this->db_operations->get_record($this->sub_menu.'_style_trans', ['mst_measurement_id' => $id]);
		if(!empty($trans_data)){
			if($this->db_operations->delete_record($this->sub_menu.'_style_trans', ['mst_measurement_id' => $id]) < 1){
				$this->db->trans_rollback();
				return ['msg' => '1. Style transaction not deleted.'];
			}
		}

		
		if($this->db_operations->delete_record($this->sub_menu.'_master', ['measurement_id' => $id]) < 1){
			$this->db->trans_rollback();
			return ['msg' => '1. Measurement not deleted.'];
		}

		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		    return ['msg' => '1. Transaction Rollback.'];
	    }
	    $this->db->trans_commit();

		return ['status' => TRUE, 'msg' => 'Measurement deleted successfully'];
	}
	// measurement_master
		public function add_edit(){
			$post_data  = $this->input->post();
			$id         = $post_data['id'];
			$result     = isMenuAssigned($this->menu, $this->sub_menu, ($id == 0 ? 'add' : 'edit'));
			if(!$result['session'] || !$result['status'] || !$result['active']) return $result;
			// echo "<pre>"; print_r($post_data); exit;

			// master_data
				$master_data['measurement_apparel_id'] 	= isset($post_data['measurement_apparel_id']) ? $post_data['measurement_apparel_id'] : 0;
				$master_data['measurement_status'] 		= isset($post_data['measurement_status']);
				$master_data['measurement_updated_by'] 	= $_SESSION['user_id'];
				$master_data['measurement_updated_at'] 	= date('Y-m-d H:i:s');
			// master_data

			$temp = $this->db_operations->get_record($this->sub_menu.'_master', ['measurement_id !=' => $id, 'measurement_apparel_id' => $master_data['measurement_apparel_id']]);
			if(!empty($temp)) return ['msg' => '1. Measurement already exist.'];	
			$this->db->trans_begin();
			if($id == 0){
				$master_data['measurement_created_by'] = $_SESSION['user_id'];
				$master_data['measurement_created_at'] = date('Y-m-d H:i:s');
				$id  = $this->db_operations->data_insert($this->sub_menu.'_master', $master_data);
				$msg = 'Measurement added successfully.';
				if($id < 1){
					$this->db->trans_rollback();
					return ['msg' => '1. Measurement not added.'];
				}
			}else{
				$prev_data = $this->db_operations->get_record($this->sub_menu.'_master', ['measurement_id' => $id]);
				if(empty($prev_data)){
					$this->db->trans_rollback();
					return ['status' => REFRESH, 'msg' => '2. Measurement not found.'];
				}
				$msg = 'Measurement updated successfully.';
				if($this->db_operations->data_update($this->sub_menu.'_master', $master_data, 'measurement_id', $id) < 1){
					$this->db->trans_rollback();
					return ['msg' => '1. Measurement not updated.'];
				}
			}

			if(isset($post_data['mmt_id'])){
				$result = $this->add_edit_maap($post_data, $id);
				if(!isset($result['status'])){
					$this->db->trans_rollback();
					return $result;
				}	
			}else{
				$trans_data = $this->db_operations->get_record($this->sub_menu.'_maap_trans', ['mmt_measurement_id' => $id]);
				if(!empty($trans_data)){
					if($this->db_operations->delete_record($this->sub_menu.'_maap_trans', ['mmt_measurement_id' => $id]) < 1){
						$this->db->trans_rollback();
						return ['msg' => '2. Maap transaction not deleted.'];
					}
				}
			}

			if(isset($post_data['mst_id'])){
				$result = $this->add_edit_style($post_data, $id);
				if(!isset($result['status'])){
					$this->db->trans_rollback();
					return $result;
				}	
			}else{
				$trans_data = $this->db_operations->get_record($this->sub_menu.'_style_trans', ['mst_measurement_id' => $id]);
				if(!empty($trans_data)){
					if($this->db_operations->delete_record($this->sub_menu.'_style_trans', ['mst_measurement_id' => $id]) < 1){
						$this->db->trans_rollback();
						return ['msg' => '2. Style transaction not deleted.'];
					}
				}
			}

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return ['msg' => '2. Transaction Rollback.'];
			}
			$this->db->trans_commit();

			$data['id'] 	= $id;
			$data['name'] 	= '';
			return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
		}
	// measurement_master

	// measurement_maap_trans
		public function add_edit_maap($post_data, $id){
			$trans_db_data = $this->db_operations->get_record($this->sub_menu.'_maap_trans', ['mmt_measurement_id' => $id]);
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['mmt_id'], $post_data['mmt_id'])){
						if($this->db_operations->delete_record($this->sub_menu.'_maap_trans', ['mmt_id' => $value['mmt_id']]) < 1) return ['msg' => '3. Maap transaction not deleted.'];
					} 
				}
			}
			foreach ($post_data['mmt_id'] as $key => $value){
				$trans_data	                    	= [];
				$trans_data['mmt_measurement_id']	= $id;
				$trans_data['mmt_maap_id']		    = $post_data['mmt_maap_id'][$key];
				$trans_data['mmt_updated_by'] 		= $_SESSION['user_id'];
				$trans_data['mmt_updated_at'] 		= date('Y-m-d H:i:s');
				if($value == 0){
					$trans_data['mmt_created_by'] 	= $_SESSION['user_id'];
					$trans_data['mmt_created_at'] 	= date('Y-m-d H:i:s');
					if($this->db_operations->data_insert($this->sub_menu.'_maap_trans', $trans_data) < 1) return ['msg' => '1. Maap transaction not added.'];
				}else{
					$prev_data = $this->db_operations->get_record($this->sub_menu.'_maap_trans', ['mmt_id' => $value]);
					if(empty($prev_data)) return ['msg' => '1. Maap transaction not found.'];
					if($this->db_operations->data_update($this->sub_menu.'_maap_trans', $trans_data, 'mmt_id', $value) < 1) return ['msg' => '1. Maap transaction not updated.'];
				}
			}
			return ['status' => TRUE];
		}
	// measurement_maap_trans

	// measurement_style_trans
		public function add_edit_style($post_data, $id){
			$trans_db_data = $this->db_operations->get_record($this->sub_menu.'_style_trans', ['mst_measurement_id' => $id]);
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['mst_id'], $post_data['mst_id'])){
						if($this->db_operations->delete_record($this->sub_menu.'_style_trans', ['mst_id' => $value['mst_id']]) < 1) return ['msg' => '3. Style transaction not deleted.'];
					} 
				}
			}
			foreach ($post_data['mst_id'] as $key => $value){
				$trans_data	                    	= [];
				$trans_data['mst_measurement_id']	= $id;
				$trans_data['mst_style_id']		    = $post_data['mst_style_id'][$key];
				$trans_data['mst_updated_by'] 		= $_SESSION['user_id'];
				$trans_data['mst_updated_at'] 		= date('Y-m-d H:i:s');
				if($value == 0){
					$trans_data['mst_created_by'] 	= $_SESSION['user_id'];
					$trans_data['mst_created_at'] 	= date('Y-m-d H:i:s');
					if($this->db_operations->data_insert($this->sub_menu.'_style_trans', $trans_data) < 1) return ['msg' => '1. Style transaction not added.'];
				}else{
					$prev_data = $this->db_operations->get_record($this->sub_menu.'_style_trans', ['mst_id' => $value]);
					if(empty($prev_data)) return ['msg' => '1. Style transaction not found.'];
					if($this->db_operations->data_update($this->sub_menu.'_style_trans', $trans_data, 'mst_id', $value) < 1) return ['msg' => '1. Style transaction not updated.'];
				}
			}
			return ['status' => TRUE];
		}
	// measurement_style_trans

}
?>