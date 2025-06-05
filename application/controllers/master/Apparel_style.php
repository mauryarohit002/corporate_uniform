<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class apparel_style extends my_controller{
    protected $menu;
    protected $sub_menu;
	public function __construct(){
        $this->menu     = 'master'; 
        $this->sub_menu = 'apparel_style'; 
        parent::__construct($this->menu, $this->sub_menu); 
    }
    public function remove(){
		$post_data  = $this->input->post();
		$id         = $post_data['id'];
		$result     = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
		if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

		$data = $this->db_operations->get_record('apparel_style_master', ['asm_id' => $id, 'asm_delete_status' => false]);
		if(empty($data)) return ['status' => REFRESH, 'msg' => '3. Apparel style not found.'];	

		if($this->model->isExist($id)) return ['msg' => '1. Not allowed to delete.'];	

		$trans_data = $this->db_operations->get_record('apparel_style_trans', ['ast_asm_id' => $id, 'ast_delete_status' => false]);
		if(empty($trans_data)) return ['msg' => '2. Transaction not found.'];
		
		$this->db->trans_begin();
		
		foreach ($trans_data as $key => $value) {
			if($this->model->isTransExist($value['ast_id'])){
				$this->db->trans_rollback();
				return ['msg' => '2. Not allowed to delete transaction.'];
			}
            $update_data 						= [];
			$update_data['ast_delete_status'] 	= true; 
			$update_data['ast_updated_by'] 		= $_SESSION['user_id']; 
			$update_data['ast_updated_at'] 		= date('Y-m-d H:i:s'); 
			if($this->db_operations->data_update('apparel_style_trans', $update_data, 'ast_id', $value['ast_id']) < 1){
				$this->db->trans_rollback();
				return ['msg' => '2. Transaction not deleted.'];
			}
		}

        $update_data 						= [];
		$update_data['asm_name'] 		    = $data[0]['asm_name'].''.$id; 
        $update_data['asm_delete_status'] 	= true; 
		$update_data['asm_updated_by'] 		= $_SESSION['user_id']; 
		$update_data['asm_updated_at'] 		= date('Y-m-d H:i:s'); 
		if($this->db_operations->data_update('apparel_style_master', $update_data, 'asm_id', $id) < 1){
			$this->db->trans_rollback();
			return ['msg' => 'Apparel style not deleted.'];
		}

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return ['msg' => '2. Transaction Rollback.'];
		}
		$this->db->trans_commit();

		return ['status' => TRUE, 'msg' => 'Apparel style deleted successfully'];
	}
    
    // apparel_style_master
        public function add_edit(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
			$post_data['trans_data'] = json_decode($post_data['trans_data'], true);
            if(!isset($post_data['trans_data']) || (isset($post_data['trans_data']) && empty($post_data['trans_data']))) return ['msg' => '1. Item not aded in list.'];
            // echo "<pre>"; print_r($post_data); exit;
            // master_data
                $master_data['asm_uuid']		= trim($post_data['asm_uuid']);
                $master_data['asm_name'] 		= trim($post_data['asm_name']);
				$master_data['asm_status']		= isset($post_data['asm_status']);
                $master_data['asm_updated_by'] 	= $_SESSION['user_id'];
                $master_data['asm_updated_at'] 	= date('Y-m-d H:i:s');
            // master_data

            $temp = $this->db_operations->get_record($this->sub_menu.'_master', ['asm_id !=' => $id, 'asm_name' => $master_data['asm_name'], 'asm_delete_status' => false]);
            if(!empty($temp)) return ['msg' => 'Apparel style already exists.'];

            $this->db->trans_begin();
            if($id == 0){
                $master_data['asm_created_by'] 	= $_SESSION['user_id'];
                $master_data['asm_created_at'] 	= date('Y-m-d H:i:s');
                $uuidExist 						= $this->db_operations->get_cnt($this->sub_menu.'_master', ['asm_uuid' => $master_data['asm_uuid']]);
                if($uuidExist > 0){
                    $this->db->trans_rollback();
                    return ['msg' => 'Form already submited.'];
                }
                $id = $this->db_operations->data_insert('apparel_style_master', $master_data);
                $msg = 'Apparel style added successfully.';
                if($id < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Apparel style not added.'];
                }
            }else{
                $prev_data = $this->db_operations->get_record('apparel_style_master', ['asm_id' => $id, 'asm_delete_status' => false]);
                if(empty($prev_data)){
                    $this->db->trans_rollback();
                    return ['status' => REFRESH, 'msg' => '1. Apparel style not found.'];
                }
                $msg = 'Apparel style updated successfully.';
                if($this->db_operations->data_update('apparel_style_master', $master_data, 'asm_id', $id) < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Apparel style not updated.'];
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
            $data['name'] 	= $master_data['asm_name'];
            return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
        }
    // apparel_style_master
    
    // apparel_style_trans
        public function add_transaction(){
            $post_data  = $this->input->post();
			$files 		= $_FILES;
            $id         = $post_data['id'];
            
            if(!isset($post_data['type']) || (isset($post_data['type']) && empty($post_data['type']))){
                return ['msg' => '1. Type is required.'];
            }

			// echo "<pre>"; print_r($post_data);
			// echo "<pre>"; print_r($files);exit;

			$result = $this->get_image($post_data, $files);
			if(!isset($result['status'])) return $result;

            $trans_data 					= [];
            $trans_data['ast_uuid'] 		= trim($post_data['asm_uuid']);
            $trans_data['ast_name'] 		= trim($post_data['type']);
            $trans_data['ast_image'] 		= $result['data'];
            $trans_data['ast_delete_status']= true;
            $trans_data['ast_created_by'] 	= $_SESSION['user_id'];
            $trans_data['ast_updated_by'] 	= $_SESSION['user_id'];
            $trans_data['ast_created_at'] 	= date('Y-m-d H:i:s');
            $trans_data['ast_updated_at'] 	= date('Y-m-d H:i:s');

			$prev_data = $this->db_operations->get_record('apparel_style_trans', ['ast_id != ' => $post_data['ast_id'], 'ast_uuid' => $trans_data['ast_uuid'], 'ast_name' => $trans_data['ast_name']]);
			if(!empty($prev_data)) return ['msg' => '1. Type already exists.'];

            if(empty($post_data['ast_id'])){
                $trans_data['ast_id'] = $this->db_operations->data_insert('apparel_style_trans', $trans_data);
                if($trans_data['ast_id'] < 1) return ['msg' => '1. Apparel style Transaction not added.'];
                $trans_data['isExist'] = false;
                $trans_data['ast_asm_id'] = 0;
            }else{
                $trans_data['ast_asm_id'] = $id;
                $trans_data['ast_id']    = $post_data['ast_id'];
            }
            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Apparel style Transaction added successfully.'];
        }
        public function add_update_trans($post_data, $id){
            $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ast_asm_id' => $id, 'ast_delete_status' => false]);
            $ids 	   	   = $this->get_id($post_data['trans_data'], 'ast_id');
            if(!empty($trans_db_data)){
                foreach ($trans_db_data as $key => $value){
                    if(!in_array($value['ast_id'], $ids)){
                        if($this->model->isTransExist($value['ast_id'])) return ['msg' => '4. Not allowed to delete.'];

                        $update_data 						= [];
                        $update_data['ast_delete_status'] 	= true;
                        $update_data['ast_updated_by'] 		= $_SESSION['user_id'];
                        $update_data['ast_updated_at'] 		= date('Y-m-d H:i:s');
                        if($this->db_operations->data_update($this->sub_menu.'_trans', $update_data, 'ast_id', $value['ast_id']) < 1){
                            return ['msg' => '4. Transaction not deleted.'];
                        }
                    } 
                }
            }
            foreach ($post_data['trans_data'] as $key => $value){
                $trans_data							= [];
                $trans_data['ast_asm_id']			= $id;
                $trans_data['ast_uuid'] 			= $value['ast_uuid'];
                $trans_data['ast_name'] 		    = $value['ast_name'];
                $trans_data['ast_image']			= $value['ast_image'];
                $trans_data['ast_default']			= $value['ast_default'];
                $trans_data['ast_delete_status'] 	= false;
                $trans_data['ast_updated_by'] 		= $_SESSION['user_id'];
                $trans_data['ast_updated_at'] 		= date('Y-m-d H:i:s');
                
                if($value['ast_id'] != 0){
                    $prev_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ast_id' => $value['ast_id']]);
                    if(empty($prev_data)) return ['msg' => '5. Transaction not found.'];
                    if(!$this->model->isTransExist($value['ast_id'])){
                        if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'ast_id', $value['ast_id']) < 1){
                            return ['msg' => 'Transaction not updated.'];
                        }
                    }
                }
            }
            return ['status' => TRUE];
        }
        
    // apparel_style_trans
}
?>