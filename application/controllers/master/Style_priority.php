<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class style_priority extends my_controller{
    protected $menu;
    protected $sub_menu;
	public function __construct(){
        $this->menu     = 'master'; 
        $this->sub_menu = 'style_priority'; 
        parent::__construct($this->menu, $this->sub_menu); 
    }
    public function remove(){
		$post_data  = $this->input->post();
		$id         = $post_data['id'];
		$result     = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
		if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

		$data = $this->db_operations->get_record('style_priority_master', ['spm_id' => $id, 'spm_delete_status' => false]);
		if(empty($data)) return ['status' => REFRESH, 'msg' => '3. Style not found.'];	

		if($this->model->isExist($id)) return ['msg' => '1. Not allowed to delete.'];	

		$trans_data = $this->db_operations->get_record('style_priority_trans', ['spt_spm_id' => $id, 'spt_delete_status' => false]);
		if(empty($trans_data)) return ['msg' => '2. Transaction not found.'];
		
		$this->db->trans_begin();
		
		foreach ($trans_data as $key => $value) {
			if($this->model->isTransExist($value['spt_id'])){
				$this->db->trans_rollback();
				return ['msg' => '2. Not allowed to delete transaction.'];
			}
            $update_data 						= [];
			$update_data['spt_delete_status'] 	= true; 
			$update_data['spt_updated_by'] 		= $_SESSION['user_id']; 
			$update_data['spt_updated_at'] 		= date('Y-m-d H:i:s'); 
			if($this->db_operations->data_update('style_priority_trans', $update_data, 'spt_id', $value['spt_id']) < 1){
				$this->db->trans_rollback();
				return ['msg' => '2. Transaction not deleted.'];
			}
		}

        $update_data 						= [];
		$update_data['spm_apparel_id'] 		= $data[0]['spm_apparel_id'].''.$id; 
        $update_data['spm_delete_status'] 	= true; 
		$update_data['spm_updated_by'] 		= $_SESSION['user_id']; 
		$update_data['spm_updated_at'] 		= date('Y-m-d H:i:s'); 
		if($this->db_operations->data_update('style_priority_master', $update_data, 'spm_id', $id) < 1){
			$this->db->trans_rollback();
			return ['msg' => 'Style not deleted.'];
		}

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return ['msg' => '2. Transaction Rollback.'];
		}
		$this->db->trans_commit();

		return ['status' => TRUE, 'msg' => 'Style deleted successfully'];
	}
    public function get_priority_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        
        $data = $this->model->get_priority_data($id);
        return['status' => TRUE, 'data' => $data, 'msg' => 'Record fetched successfully.'];
    }
    // style_priority_master
        public function add_edit(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
			$post_data['trans_data'] = json_decode($post_data['trans_data'], true);
            if(!isset($post_data['trans_data']) || (isset($post_data['trans_data']) && empty($post_data['trans_data']))) return ['msg' => '1. Item not aded in list.'];
            // echo "<pre>"; print_r($post_data); exit;
            // master_data
                $master_data['spm_uuid']		= trim($post_data['spm_uuid']);
                $master_data['spm_apparel_id'] 	= trim($post_data['spm_apparel_id']);
				$master_data['spm_status']		= isset($post_data['spm_status']);
                $master_data['spm_updated_by'] 	= $_SESSION['user_id'];
                $master_data['spm_updated_at'] 	= date('Y-m-d H:i:s');
            // master_data

            $temp = $this->db_operations->get_record($this->sub_menu.'_master', ['spm_id !=' => $id, 'spm_apparel_id' => $master_data['spm_apparel_id'], 'spm_delete_status' => false]);
            if(!empty($temp)) return ['msg' => 'Apparel already exists.'];

            $this->db->trans_begin();
            if($id == 0){
                $master_data['spm_created_by'] 	= $_SESSION['user_id'];
                $master_data['spm_created_at'] 	= date('Y-m-d H:i:s');
                $uuidExist 						= $this->db_operations->get_cnt($this->sub_menu.'_master', ['spm_uuid' => $master_data['spm_uuid']]);
                if($uuidExist > 0){
                    $this->db->trans_rollback();
                    return ['msg' => 'Form already submited.'];
                }
                $id = $this->db_operations->data_insert('style_priority_master', $master_data);
                $msg = 'Style priority added successfully.';
                if($id < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Style priority not added.'];
                }
            }else{
                $prev_data = $this->db_operations->get_record('style_priority_master', ['spm_id' => $id, 'spm_delete_status' => false]);
                if(empty($prev_data)){
                    $this->db->trans_rollback();
                    return ['status' => REFRESH, 'msg' => '1. Style priority not found.'];
                }
                $msg = 'Style priority updated successfully.';
                if($this->db_operations->data_update('style_priority_master', $master_data, 'spm_id', $id) < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Style priority not updated.'];
                }
            }

            $result = $this->add_update_trans($post_data, $id);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return ['msg' => '1. Transaction Rollback.'];
            }
            $this->db->trans_commit();

            $data['id'] 	= encrypt_decrypt("encrypt", $id, SECRET_KEY);
            $data['name'] 	= '';
            return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
        }
    // style_priority_master
    
    // style_priority_trans
        public function add_transaction(){
            $post_data  = $this->input->post();
			$id         = $post_data['id'];
            
            if(!isset($post_data['priority']) || (isset($post_data['priority']) && empty($post_data['priority']))) return ['msg' => '1. Priority is required.'];
            if(!isset($post_data['asm_id']) || (isset($post_data['asm_id']) && empty($post_data['asm_id']))) return ['msg' => '1. Style is required.'];

			// echo "<pre>"; print_r($post_data);exit;
            $trans_data 					= [];
            $trans_data['spt_uuid'] 		= trim($post_data['spm_uuid']);
            $trans_data['spt_priority'] 	= trim($post_data['priority']);
            $trans_data['spt_asm_id'] 	    = trim($post_data['asm_id']);
            $trans_data['spt_delete_status']= true;
            $trans_data['spt_created_by'] 	= $_SESSION['user_id'];
            $trans_data['spt_updated_by'] 	= $_SESSION['user_id'];
            $trans_data['spt_created_at'] 	= date('Y-m-d H:i:s');
            $trans_data['spt_updated_at'] 	= date('Y-m-d H:i:s');

            if(empty($post_data['spt_id'])){
                $trans_data['spt_id'] = $this->db_operations->data_insert('style_priority_trans', $trans_data);
                if($trans_data['spt_id'] < 1) return ['msg' => '1. Style Transaction not added.'];
                $trans_data['isExist'] = false;
                $trans_data['spt_spm_id'] = 0;
            }else{
                $trans_data['spt_spm_id'] = $id;
                $trans_data['spt_id']     = $post_data['spt_id'];
            }
            $trans_data['asm_name'] = $this->model->get_name($trans_data['spt_asm_id']);
            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Style Transaction added successfully.'];
        }
        public function add_update_trans($post_data, $id){
            $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['spt_spm_id' => $id, 'spt_delete_status' => false]);
            $ids 	   	   = $this->get_id($post_data['trans_data'], 'spt_id');
            if(!empty($trans_db_data)){
                foreach ($trans_db_data as $key => $value){
                    if(!in_array($value['spt_id'], $ids)){
                        if($this->model->isTransExist($value['spt_id'])) return ['msg' => '4. Not allowed to delete.'];

                        $update_data 						= [];
                        $update_data['spt_delete_status'] 	= true;
                        $update_data['spt_updated_by'] 		= $_SESSION['user_id'];
                        $update_data['spt_updated_at'] 		= date('Y-m-d H:i:s');
                        if($this->db_operations->data_update($this->sub_menu.'_trans', $update_data, 'spt_id', $value['spt_id']) < 1){
                            return ['msg' => '4. Transaction not deleted.'];
                        }
                    } 
                }
            }
            foreach ($post_data['trans_data'] as $key => $value){
                $trans_data							= [];
                $trans_data['spt_spm_id']			= $id;
                $trans_data['spt_uuid'] 			= $value['spt_uuid'];
                $trans_data['spt_priority'] 		= $value['spt_priority'];
                $trans_data['spt_asm_id']			= $value['spt_asm_id'];
                $trans_data['spt_delete_status'] 	= false;
                $trans_data['spt_updated_by'] 		= $_SESSION['user_id'];
                $trans_data['spt_updated_at'] 		= date('Y-m-d H:i:s');
                
                $prev_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['spt_id' => $value['spt_id']]);
                if(empty($prev_data)){
                    if($this->db_operations->data_insert($this->sub_menu.'_trans', $trans_data) < 1){
                        return ['msg' => '2. Style Transaction not added.'];
                    }
                }else{
                    if(empty($prev_data)) return ['msg' => '5. Transaction not found.'];
                    if(!$this->model->isTransExist($value['spt_id'])){
                        if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'spt_id', $value['spt_id']) < 1){
                            return ['msg' => 'Transaction not updated.'];
                        }
                    }
                }
            }
            return ['status' => TRUE];
        }
        
    // style_priority_trans
}
?>