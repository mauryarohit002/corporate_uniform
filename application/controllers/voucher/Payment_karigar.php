<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class payment_karigar extends my_controller{
    protected $menu;
    protected $sub_menu;
	public function __construct(){
        $this->menu     = 'voucher'; 
        $this->sub_menu = 'payment_karigar'; 
        parent::__construct($this->menu, $this->sub_menu); 
    }
    public function remove(){
        $post_data  = $this->input->post();
		$id         = $post_data['id'];
		$result     = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
		if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

        $data = $this->db_operations->get_record($this->sub_menu.'_master', ['payment_id' => $id, 'payment_delete_status' => false]);
        if(empty($data)) return ['status' => REFRESH, 'msg' => '2. payment not found'];

        if($this->model->isExist($id)) return ['msg' => '1. Not allowed to delete.'];
        
        $this->db->trans_begin();
        $trans_data = $this->db_operations->get_record('payment_hisab_trans', ['pht_payment_id' => $id, 'pht_delete_status' => false]);
        if(!empty($trans_data)){
            foreach ($trans_data as $key => $value) {
                if($this->model->isOrderTransExist($value['pht_id'])){
                    $this->db->trans_rollback();
                    return ['msg' => '2. Order transaction not allowed to delete.'];
                }
            
                $result = $this->delete_hisab($value);
                if(!isset($result['status'])){
                    $this->db->trans_rollback();
                    return $result;
                }
            
                $update_data 						= [];
                $update_data['pht_delete_status'] 	= true;
                $update_data['pht_updated_by'] 		= $_SESSION['user_id'];
                $update_data['pht_updated_at'] 		= date('Y-m-d H:i:s');;
                if($this->db_operations->data_update('payment_hisab_trans', $update_data, 'pht_id', $value['pht_id']) < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Order transaction not deleted.'];
                }
            }
        }

        $prev_data = $this->db_operations->get_record('payment_karigar_payment_mode_trans', ['pkpmt_payment_id' => $id, 'pkpmt_delete_status' => false]);
        if(!empty($prev_data)){
            $update_data 						= [];
            $update_data['pkpmt_delete_status'] 	= true; 
            $update_data['pkpmt_updated_by'] 	= $_SESSION['user_id']; 
            $update_data['pkpmt_updated_at'] 	= date('Y-m-d H:i:s'); 
            if($this->db_operations->data_update('payment_karigar_payment_mode_trans', $update_data, 'pkpmt_payment_id', $id) < 1){
                $this->db->trans_rollback();
                return ['msg' => '1. Payment not deleted.'];
            }
        }

        $update_data 							= [];
        $update_data['payment_entry_no'] 		= $data[0]['payment_entry_no'].''.$id; 
        $update_data['payment_delete_status'] 	= true; 
        $update_data['payment_updated_by'] 		= $_SESSION['user_id']; 
        $update_data['payment_updated_at'] 		= date('Y-m-d H:i:s'); 
        if($this->db_operations->data_update($this->sub_menu.'_master', $update_data, 'payment_id', $id) < 1){
            $this->db->trans_rollback();
            return ['msg' => 'payment not deleted.'];
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return ['msg' => '2. Transaction Rollback'];
        }
        $this->db->trans_commit();
        return ['status' => TRUE, 'msg' => 'Deleted successfully'];
    }
    public function get_karigar_from_hisab(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $data       = $this->model->get_karigar_from_hisab($id);
        return ['status' => TRUE, 'data' => $data, 'msg' => 'Record fetched successfully.'];
    }
    public function get_karigar_data(){
        $post_data      = $this->input->post();
        $id             = $post_data['id'];
        $balance_data 	= $this->model->get_karigar_balance_amt($id);
        $hisab_data 	= $this->model->get_hisab_data($id);
        $hisab_bal 		= $this->model->get_hisab_balance($id);
		
        $data['hisab_bal'] 		= $hisab_bal;;
		$data['balance_data'] 	= $balance_data;
		$data['hisab_data'] 	= $hisab_data;
		 // echo "<pre>"; print_r($data);die;
        return ['status' => TRUE, 'data' => $data, 'msg' => 'Record fetched successfully.'];
    }
    public function get_data_for_adjustment(){
        $post_data                  = $this->input->post();
        $id                         = $post_data['id'];
        $data['hisab_data'] 		= $this->model->get_hisab_data($id);
        $data['balance_data'] 		= $this->model->get_balance_data($id);
        return ['status' => TRUE, 'data' => $data, 'msg' => 'Record fetched successfully.'];
    }

    public function add_edit(){
        $post_data  = $this->input->post();
        // echo "<pre>"; print_r($post_data); exit;
        $id         = $post_data['id'];
        $result     = isMenuAssigned($this->menu, $this->sub_menu, ($id == 0 ? 'add' : 'edit'));
        if(!$result['session'] || !$result['status'] || !$result['active']) return $result;
        
        // Master array
            $master_data['payment_uuid']			= trim($post_data['payment_uuid']);
            $master_data['payment_entry_no']		= trim($post_data['payment_entry_no']);
            $master_data['payment_entry_date'] 		= $post_data['payment_entry_date'];				
            $master_data['payment_karigar_id'] 	    = isset($post_data['payment_karigar_id']) ? $post_data['payment_karigar_id'] : $post_data['karigar_id'];
            $master_data['payment_notes'] 			= trim($post_data['payment_notes']);
            $master_data['payment_opening_amt'] 	= trim($post_data['payment_opening_amt']);
            $master_data['payment_hisab_amt'] 	    = trim($post_data['payment_hisab_amt']);
            $master_data['payment_amt'] 			= trim($post_data['payment_amt']);
            $master_data['payment_updated_by'] 		= $_SESSION['user_id'];
            $master_data['payment_updated_at'] 		= date('Y-m-d H:i:s');
        // Master array
        
        $this->db->trans_begin();
        if($id == 0){
            $master_data['payment_entry_no'] 		= $this->model->get_max_entry_no(['entry_no' => 'payment_entry_no', 'delete_status' => 'payment_delete_status', 'fin_year' => 'payment_fin_year']);
            $master_data['payment_created_by'] 		= $_SESSION['user_id'];
            $master_data['payment_created_at'] 		= date('Y-m-d H:i:s');
            $master_data['payment_fin_year'] 		= $_SESSION['fin_year'];
            $master_data['payment_branch_id'] 		= $_SESSION['user_branch_id'];
            $uuidExist 								= $this->db_operations->get_cnt($this->sub_menu.'_master', ['payment_uuid' => $master_data['payment_uuid']]);
            if($uuidExist > 0){
                $this->db->trans_rollback();
                return ['msg' => '1. Form already submitted.'];
            }
            $id  = $this->db_operations->data_insert($this->sub_menu.'_master', $master_data);
            $msg = 'Added successfully';
            if($id < 1){
                $this->db->trans_rollback();
                return ['msg' => '1. payment not added.'];
            }
        }else{
            $msg = 'Updated successfully';
            $prev_data = $this->db_operations->get_record($this->sub_menu.'_master', ['payment_id' => $id, 'payment_delete_status' => false]);
            if(empty($prev_data)){
                $this->db->trans_rollback();
                return ['msg' => '1. payment not found.'];
            }
            if($this->db_operations->data_update($this->sub_menu.'_master', $master_data, 'payment_id', $id) < 1){
                $this->db->trans_rollback();
                return ['msg' => 'payment not updated.'];
            }
        }
        
        $result = $this->add_update_hisab($post_data, $id);
        if(!isset($result['status'])){
            $this->db->trans_rollback();
            return $result;
        }
        
        $result = $this->add_update_payment_mode($post_data, $id);
        if(!isset($result['status'])){
            $this->db->trans_rollback();
            return $result;
        }

        if($this->db_operations->data_update($this->sub_menu.'_master', ['payment_adjust_status' => $this->model->isAdjusted($id)], 'payment_id', $id) < 1){
            $this->db->trans_rollback();
            return ['msg' => 'Adjusted status not updated.'];
        }

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return ['msg' => '1. Transaction Rollback.'];
        }
        $this->db->trans_commit();
        $data['id'] = $id;
        return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
    }

    // payment_order_trans
		public function add_update_hisab($post_data, $id){
			$trans_db_data = $this->db_operations->get_record('payment_hisab_trans', ['pht_payment_id' => $id, 'pht_delete_status' => false]);
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					$flag = false;
					if(isset($post_data['pht_checked'])){
						if(!array_key_exists($value['pht_hm_id'], $post_data['pht_checked'])){
							$flag = true;
						}
					}else{
						$flag = true;
					}
					if($flag){
						$result = $this->delete_hisab($value);
						if(!isset($result['status'])) return $result;

						$update_data 						= [];
						$update_data['pht_delete_status'] 	= true;
						$update_data['pht_updated_by'] 		= $_SESSION['user_id'];
						$update_data['pht_updated_at'] 		= date('Y-m-d H:i:s');
						if($this->db_operations->data_update('payment_hisab_trans', $update_data, 'pht_id', $value['pht_id']) < 1){
							return ['msg' => '4. payment not deleted.'];
						}
					}
				}
			}
			if(isset($post_data['pht_checked']) && !empty($post_data['pht_checked'])){
				foreach ($post_data['pht_checked'] as $key => $value){
					if($post_data['pht_adjust_amt'][$key] > 0){
						$trans_data							= [];
						$trans_data['pht_payment_id']		= $id;
						$trans_data['pht_payment_uuid']		= $post_data['payment_uuid'];
						$trans_data['pht_hm_id']			= $post_data['pht_hm_id'][$key];
						$trans_data['pht_entry_no']			= $post_data['pht_entry_no'][$key];
						$trans_data['pht_entry_date']		= $post_data['pht_entry_date'][$key];
						$trans_data['pht_total_amt']		= $post_data['pht_total_amt'][$key];
						$trans_data['pht_adjust_amt']		= $post_data['pht_adjust_amt'][$key];
						$trans_data['pht_delete_status']	= false;
						$trans_data['pht_updated_by'] 		= $_SESSION['user_id'];
						$trans_data['pht_updated_at'] 		= date('Y-m-d H:i:s');
					
						if(empty($post_data['pht_id'][$key])){
							$trans_data['pht_created_by'] 	= $_SESSION['user_id'];
							$trans_data['pht_created_at'] 	= date('Y-m-d H:i:s');
							if($this->db_operations->data_insert('payment_hisab_trans', $trans_data) < 1) return ['msg' => '1. Payment not added.'];

							$result = $this->update_hisab($trans_data);
								
							if(!isset($result['status'])) return $result;
						}else{
							$prev_data = $this->db_operations->get_record('payment_hisab_trans', ['pht_id' => $post_data['pht_id'][$key], 'pht_delete_status' => false]);
							if(empty($prev_data)) return ['msg' => '1. Payment transaction not found.'];
						
							if($this->db_operations->data_update('payment_hisab_trans', $trans_data, 'pht_id', $prev_data[0]['pht_id']) < 1){
								return ['msg' => '1. Payment transaction not updated.'];
							}
						
							$result = $this->delete_hisab($prev_data[0]);
							if(!isset($result['status'])) return $result;
						
							$result = $this->update_hisab($trans_data);
							if(!isset($result['status'])) return $result;
						}
					}
				}
			}
			return ['status' => TRUE];
		}
	// payment_order_trans
    
    // payment_karigar_payment_mode_trans
        public function get_payment_mode_data(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            $data       = $this->model->get_payment_mode_data($id);
            if(empty($data)) return ['msg' => '1. Payment mode not define.'];
            return ['status' => TRUE, 'data' => $data, 'msg' => 'Record fetched successfully.'];
        }
		public function add_update_payment_mode($post_data, $id){
			$trans_db_data = $this->db_operations->get_record('payment_karigar_payment_mode_trans', ['pkpmt_payment_id' => $id, 'pkpmt_delete_status' => false]);
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['pkpmt_id'], $post_data['pkpmt_id'])){
						$update_data 						= [];
						$update_data['pkpmt_delete_status'] = true;
						$update_data['pkpmt_updated_by'] 	= $_SESSION['user_id'];
						$update_data['pkpmt_updated_at'] 	= date('Y-m-d H:i:s');
						if($this->db_operations->data_update('payment_karigar_payment_mode_trans', $update_data, 'pkpmt_id', $value['pkpmt_id']) < 1){
							return ['msg' => '1. Payment mode not deleted.'];
						}
					}
				}
				foreach ($post_data['pkpmt_amt'] as $key => $value) {
					if($value <= 0){
						$update_data 						= [];
						$update_data['pkpmt_delete_status'] = true;
						$update_data['pkpmt_updated_by'] 	= $_SESSION['user_id'];
						$update_data['pkpmt_updated_at'] 	= date('Y-m-d H:i:s');
						if($this->db_operations->data_update('payment_karigar_payment_mode_trans', $update_data, 'pkpmt_id', $post_data['pkpmt_id'][$key]) < 1){
							return ['msg' => '1. Payment mode not deleted.'];
						}
					}
				}
			}
			foreach ($post_data['pkpmt_amt'] as $key => $value){
				if($value > 0){
					$trans_data							= [];
					$trans_data['pkpmt_payment_id']		= $id;
					$trans_data['pkpmt_payment_uuid']	= $post_data['payment_uuid'];
					$trans_data['pkpmt_payment_mode_id']= $post_data['pkpmt_payment_mode_id'][$key];
					$trans_data['pkpmt_amt']			= $post_data['pkpmt_amt'][$key];
					$trans_data['pkpmt_delete_status']	= false;
					$trans_data['pkpmt_updated_by'] 	= $_SESSION['user_id'];
					$trans_data['pkpmt_updated_at'] 	= date('Y-m-d H:i:s');
					
					if(empty($post_data['pkpmt_id'][$key])){
						$trans_data['pkpmt_created_by'] 	= $_SESSION['user_id'];
						$trans_data['pkpmt_created_at'] 	= date('Y-m-d H:i:s');
						if($this->db_operations->data_insert('payment_karigar_payment_mode_trans', $trans_data) < 1){
							return ['msg' => '1. Payment mode not added.'];
						}
					}
				}
			}
			return ['status' => TRUE];
		}
	// payment_karigar_payment_mode_trans

    // hisab_master
		public function update_hisab($temp){
			$data = $this->model->get_hisab($temp['pht_hm_id']);
			if(empty($data)) return ['msg' => '1. Hisab not found.'];

			if($temp['pht_adjust_amt'] > $data[0]['balance_amt']) return ['msg' => '1. Hisab adjusted amt should be less than order balance amt.'];

			$master_data = [];
			$master_data['hm_allocated_amt'] = $data[0]['hm_allocated_amt'] + $temp['pht_adjust_amt'];

			if($this->db_operations->data_update('hisab_master', $master_data, 'hm_id', $data[0]['hm_id']) < 1) return ['msg' => '1. Hisab not updated.'];

			return ['status' => TRUE];
		}
		public function delete_hisab($temp){
			$data = $this->db_operations->get_record('hisab_master', ['hm_id' => $temp['pht_hm_id'], 'hm_delete_status' => false]);
			if(empty($data)) return ['msg' => '2. Hisab not found.'];

			if($temp['pht_adjust_amt'] > $data[0]['hm_allocated_amt']) return ['msg' => '1. Hisab adjusted amt should be less than order allocated amt.'];

			$master_data = [];
			$master_data['hm_allocated_amt'] = $data[0]['hm_allocated_amt'] - $temp['pht_adjust_amt'];

			if($this->db_operations->data_update('hisab_master', $master_data, 'hm_id', $data[0]['hm_id']) < 1) return ['msg' => '2. Hisab not updated.'];

			return ['status' => TRUE];
		}
	// hisab_master
}
?>