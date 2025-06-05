<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class Purchase_readymade extends my_controller{ 
    protected $menu;
    protected $sub_menu;
	public function __construct(){
        $this->menu     = 'transaction'; 
        $this->sub_menu = 'purchase_readymade'; 
        parent::__construct($this->menu, $this->sub_menu); 
    }
    public function get_supplier_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $data 		= 0;
		$supplier_data = $this->model->get_supplier_state($id);
        if(!empty($supplier_data) && ($supplier_data[0]['state_id'] != 0)){
			$state_data = $this->model->get_state();
			if(!empty($state_data)){
				// echo "<pre>"; print_r($state_data);die;
				$data = ($state_data[0]['state_id'] == $supplier_data[0]['state_id']) ? 0 : 1;
			}
		}
        return['status' => TRUE, 'data' => $data, 'msg' => 'Supplier fetched successfully.'];
	}
    public function remove(){
		$post_data  = $this->input->post();
		$id         = $post_data['id'];
		$result     = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
		if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

		$data = $this->db_operations->get_record('purchase_readymade_master', ['prmm_id' => $id, 'prmm_delete_status' => false]);
		if(empty($data)) return ['status' => REFRESH, 'msg' => '3. Purchase not found.'];	

		if($this->model->isExist($id)) return ['msg' => '1. Not allowed to delete.'];	

		$trans_data = $this->db_operations->get_record('purchase_readymade_trans', ['prmt_prmm_id' => $id, 'prmt_delete_status' => false]);
		if(empty($trans_data)) return ['msg' => '2. Transaction not found.'];
		
		$this->db->trans_begin();
		
		foreach ($trans_data as $key => $value) {
			if($this->model->isTransExist($value['prmt_id'])){
				$this->db->trans_rollback();
				return ['msg' => '2. Not allowed to delete transaction.'];
			}
			$result = $this->delete_barcode($value['prmt_id']);
			if(!isset($result['status'])) return $result;

			$update_data 						= [];
			$update_data['prmt_delete_status'] 	= true; 
			$update_data['prmt_updated_by'] 	= $_SESSION['user_id']; 
			$update_data['prmt_updated_at'] 	= date('Y-m-d H:i:s'); 
			if($this->db_operations->data_update('purchase_readymade_trans', $update_data, 'prmt_id', $value['prmt_id']) < 1){
				$this->db->trans_rollback();
				return ['msg' => '2. Transaction not deleted.'];
			}
		}

		$update_data 						= [];
		$update_data['prmm_entry_no'] 		= $data[0]['prmm_entry_no'].''.$id; 
		$update_data['prmm_delete_status'] 	= true; 
		$update_data['prmm_updated_by'] 	= $_SESSION['user_id']; 
		$update_data['prmm_updated_at'] 	= date('Y-m-d H:i:s'); 
		if($this->db_operations->data_update('purchase_readymade_master', $update_data, 'prmm_id', $id) < 1){
			$this->db->trans_rollback();
			return ['msg' => 'Purchase not deleted.'];
		}

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return ['msg' => '2. Transaction Rollback.'];
		}
		$this->db->trans_commit();

		return ['status' => TRUE, 'msg' => 'Purchase deleted successfully'];
	}
    // purchase_readymade_master
        public function add_edit(){
			$post_data  = $this->input->post();
            $id         = $post_data['id'];
			if(!isset($post_data['trans_data']) || (isset($post_data['trans_data']) && empty($post_data['trans_data']))) return ['msg' => '1. Item not aded in list.'];
			$post_data['trans_data'] = json_decode($post_data['trans_data'], true);

			// master_data
                $master_data['prmm_uuid'] 				    = trim($post_data['prmm_uuid']);
                $master_data['prmm_entry_no'] 				= trim($post_data['prmm_entry_no']);
                $master_data['prmm_entry_date'] 			= date('Y-m-d', strtotime($post_data['prmm_entry_date']));
                $master_data['prmm_bill_no'] 				= trim($post_data['prmm_bill_no']);
                $master_data['prmm_bill_date'] 				= date('Y-m-d', strtotime($post_data['prmm_bill_date']));
                $master_data['prmm_supplier_id'] 			= trim($post_data['prmm_supplier_id']);
                $master_data['prmm_gst_type'] 				= trim($post_data['prmm_gst_type']);
                $master_data['prmm_total_qty'] 				= trim($post_data['prmm_total_qty']);
                $master_data['prmm_notes'] 					= trim($post_data['prmm_notes']);
                $master_data['prmm_sub_amt'] 				= trim($post_data['prmm_sub_amt']);
                $master_data['prmm_disc_amt'] 				= trim($post_data['prmm_disc_amt']);
                $master_data['prmm_taxable_amt'] 			= trim($post_data['prmm_taxable_amt']);
                $master_data['prmm_extra_amt'] 				= trim($post_data['prmm_extra_amt']);
                $master_data['prmm_sgst_amt']				= trim($post_data['prmm_sgst_amt']);
                $master_data['prmm_cgst_amt']				= trim($post_data['prmm_cgst_amt']);
                $master_data['prmm_igst_amt']				= trim($post_data['prmm_igst_amt']);
                $master_data['prmm_round_off']				= trim($post_data['prmm_round_off']);
                $master_data['prmm_bill_disc_per']			= trim($post_data['prmm_bill_disc_per']);
                $master_data['prmm_bill_disc_amt']			= trim($post_data['prmm_bill_disc_amt']);
                $master_data['prmm_total_amt']				= trim($post_data['prmm_total_amt']);
                $master_data['prmm_updated_by'] 			= $_SESSION['user_id'];
            // master_data

            $temp = $this->db_operations->get_record($this->sub_menu.'_master', ['prmm_id !=' => $id, 'prmm_bill_no' => $master_data['prmm_bill_no'],'prmm_supplier_id'=>$master_data['prmm_supplier_id'], 'prmm_delete_status' => false]);
			if(!empty($temp)) return ['msg' => 'Bill no already exists.'];

			$this->db->trans_begin();
			if($id == 0){
				$master_data['prmm_entry_no'] 	= $this->model->get_max_entry_no(['entry_no' => 'prmm_entry_no', 'delete_status' => 'prmm_delete_status', 'fin_year' => 'prmm_fin_year']);
				$master_data['prmm_created_by'] = $_SESSION['user_id'];
				$master_data['prmm_created_at'] = date('Y-m-d H:i:s');
				$master_data['prmm_fin_year'] 	= $_SESSION['fin_year'];
				$master_data['prmm_branch_id'] 	= $_SESSION['user_branch_id'];
				$uuidExist 						= $this->db_operations->get_cnt($this->sub_menu.'_master', ['prmm_uuid' => $master_data['prmm_uuid']]);
				if($uuidExist > 0){
					$this->db->trans_rollback();
					return ['msg' => 'Form already submited.'];
				}
				$id = $this->db_operations->data_insert('purchase_readymade_master', $master_data);
				$msg = 'Purchase added successfully.';
				if($id < 1){
					$this->db->trans_rollback();
					return ['msg' => '1. Purchase not added.'];
				}
			}else{
				$prev_data = $this->db_operations->get_record('purchase_readymade_master', ['prmm_id' => $id, 'prmm_delete_status' => false]);
				if(empty($prev_data)){
					$this->db->trans_rollback();
					return ['status' => REFRESH, 'msg' => '1. Purchase not found.'];
				}
				$msg = 'Purchase updated successfully.';
				if($this->db_operations->data_update('purchase_readymade_master', $master_data, 'prmm_id', $id) < 1){
					$this->db->trans_rollback();
					return ['msg' => '1. Purchase not updated.'];
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
			$data['name'] 	= strtoupper($master_data['prmm_entry_no']);
			return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
		}
    // purchase_readymade_master

    // purchase_readymade_trans
        public function add_transaction(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            
			if(!isset($post_data['product_id']) || (isset($post_data['product_id']) && empty($post_data['product_id']))) return ['msg' => '1. Product is required.'];

			if(!isset($post_data['readymade_category_id']) || (isset($post_data['readymade_category_id']) && empty($post_data['readymade_category_id']))) return ['msg' => '1. Category is required.'];

			if(!isset($post_data['qty']) || (isset($post_data['qty']) && empty($post_data['qty']))){
                return ['msg' => '1. Qty is required.'];
            }else{
                if($post_data['qty'] <= 0) return ['msg' => '1. Invalid Qty.'];
            }
            if(!isset($post_data['rate']) || (isset($post_data['rate']) && empty($post_data['rate']))){
                return ['msg' => '1. Rate is required.'];
            }else{
                if($post_data['rate'] <= 0) return ['msg' => '1. Invalid Rate.'];	
            }
            
            $trans_data 					= [];
            $trans_data['prmt_product_id'] 	= isset($post_data['product_id']) ? $post_data['product_id'] : 0;
            $trans_data['prmt_design_id'] 	= isset($post_data['design_id']) ? $post_data['design_id'] : 0;

            $trans_data['prmt_readymade_category_id'] 	= isset($post_data['readymade_category_id']) ? $post_data['readymade_category_id'] : 0;
            $trans_data['prmt_color_id'] 	= isset($post_data['color_id']) ? $post_data['color_id'] : 0;
            $trans_data['prmt_size_id'] 	= isset($post_data['size_id']) ? $post_data['size_id'] : 0;
            $trans_data['prmt_gender_id'] 	= isset($post_data['gender_id']) ? $post_data['gender_id'] : 0;
            $trans_data['prmt_cost_char'] 	= trim($post_data['cost_char']);
            $trans_data['prmt_mrp'] 		= trim($post_data['mrp']);
            $trans_data['prmt_qty'] 		= trim($post_data['qty']);
            $trans_data['prmt_rate'] 		= trim($post_data['rate']);
            $trans_data['prmt_amt'] 		= trim($post_data['amt']);
            $trans_data['prmt_disc_per'] 	= trim($post_data['disc_per']);
            $trans_data['prmt_disc_amt'] 	= trim($post_data['disc_amt']);
            $trans_data['prmt_taxable_amt'] = trim($post_data['taxable_amt']);
            $trans_data['prmt_extra_amt'] 	= trim($post_data['extra_amt']);
            $trans_data['prmt_actual_taxable_amt'] 	= trim($post_data['actual_taxable_amt']);
            $trans_data['prmt_sgst_per'] 	= trim($post_data['sgst_per']);
            $trans_data['prmt_sgst_amt'] 	= trim($post_data['sgst_amt']);
            $trans_data['prmt_cgst_per'] 	= trim($post_data['cgst_per']);
            $trans_data['prmt_cgst_amt'] 	= trim($post_data['cgst_amt']);
            $trans_data['prmt_igst_per'] 	= trim($post_data['igst_per']);
            $trans_data['prmt_igst_amt'] 	= trim($post_data['igst_amt']);
            $trans_data['prmt_total_amt'] 	= trim($post_data['total_amt']);
            $trans_data['prmt_description'] = trim($post_data['description']);
            $trans_data['prmt_created_by'] 	= $_SESSION['user_id'];
            $trans_data['prmt_updated_by'] 	= $_SESSION['user_id'];
            $trans_data['prmt_created_at'] 	= date('Y-m-d H:i:s');
            $trans_data['prmt_updated_at'] 	= date('Y-m-d H:i:s');
			
            if($trans_data['prmt_readymade_category_id'] ==1){
            	if(!isset($post_data['design_id']) || (isset($post_data['design_id']) && empty($post_data['design_id']))) return ['msg' => '1. Fabric code is required.'];

            	if(!isset($post_data['mrp']) || (isset($post_data['mrp']) && empty($post_data['mrp']))){
                	return ['msg' => '1. Mrp is required.'];
	            }else{
	                if($post_data['mrp'] <= 0) return ['msg' => '1. Invalid Mrp.'];	
	            }
            }
			if(empty($post_data['prmt_id'])){
				$trans_data['prmt_id'] = $this->db_operations->data_insert('purchase_readymade_trans', $trans_data);
				if($trans_data['prmt_id'] < 1) return ['msg' => '1. Purchase Transaction not added.'];
				$trans_data['isExist'] = false;
			}else{
				$trans_data['prmt_id'] = $post_data['prmt_id'];
			}
			
            $trans_data['encrypt_prmt_id']= encrypt_decrypt("encrypt", $trans_data['prmt_id'], SECRET_KEY);
            $trans_data['product_name'] 	= $this->model->get_name('product', $trans_data['prmt_product_id']);
            $trans_data['design_image'] = $this->model->get_design_image($trans_data['prmt_design_id']);
            $trans_data['design_name'] 	= $this->model->get_name('design', $trans_data['prmt_design_id']);
            $trans_data['readymade_category_name'] 	= $this->model->get_name('readymade_category', $trans_data['prmt_readymade_category_id']);
            $trans_data['color_name'] 	= $this->model->get_name('color', $trans_data['prmt_color_id']);
            $trans_data['size_name'] 	= $this->model->get_name('size', $trans_data['prmt_size_id']);
            $trans_data['gender_name'] 	= $this->model->get_name('gender', $trans_data['prmt_gender_id']);
			
            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Purchase Transaction added successfully.'];
        }
        public function add_update_trans($post_data, $id){
			$trans_db_data = $this->db_operations->get_record('purchase_readymade_trans', ['prmt_prmm_id' => $id, 'prmt_delete_status' => false]);
			$ids 	   	   = $this->get_id($post_data['trans_data'],'prmt_id');
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['prmt_id'], $ids)){
						if($this->model->isTransExist($value['prmt_id'])) return ['msg' => '1. Not allowed to delete transaction.'];
						$result = $this->delete_barcode($value['prmt_id']);
						if(!isset($result['status'])) return $result;
	
						$update_data 						= [];
						$update_data['prmt_delete_status'] 	= true; 
						$update_data['prmt_updated_by'] 		= $_SESSION['user_id']; 
						$update_data['prmt_updated_at'] 		= date('Y-m-d H:i:s'); 
						if($this->db_operations->data_update('purchase_readymade_trans', $update_data, 'prmt_id', $value['prmt_id']) < 1) return ['msg' => '1. Transaction not deleted.'];
					}
				}
			}
			foreach ($post_data['trans_data'] as $key => $value){
				if($value['prmt_id'] != 0){
					$prev_data = $this->db_operations->get_record('purchase_readymade_trans', ['prmt_id' => $value['prmt_id'], 'prmt_delete_status' => false]);
					if(empty($prev_data)) return ['msg' => '1. Transaction not found.'];

					$trans_data 					= [];
					$trans_data['prmt_prmm_id'] 	= $id;
					$trans_data['prmt_prmm_uuid'] 	= $post_data['prmm_uuid'];
					$trans_data['prmt_product_id'] 	= isset($value['prmt_product_id']) ? $value['prmt_product_id'] : 0;
					$trans_data['prmt_design_id'] 	= isset($value['prmt_design_id']) ? $value['prmt_design_id'] : 0;
					$trans_data['prmt_readymade_category_id'] 	= isset($value['prmt_readymade_category_id']) ? $value['prmt_readymade_category_id'] : 0;
					$trans_data['prmt_color_id'] 	= isset($value['prmt_color_id']) ? $value['prmt_color_id'] : 0;
					$trans_data['prmt_size_id'] 	= isset($value['prmt_size_id']) ? $value['prmt_size_id'] : 0;
					$trans_data['prmt_gender_id'] 	= isset($value['prmt_gender_id']) ? $value['prmt_gender_id'] : 0;
					$trans_data['prmt_cost_char'] 	= trim($value['prmt_cost_char']);
					$trans_data['prmt_mrp'] 		= trim($value['prmt_mrp']);
					$trans_data['prmt_qty'] 		= trim($value['prmt_qty']);
					$trans_data['prmt_rate'] 		= trim($value['prmt_rate']);
					$trans_data['prmt_amt'] 		= trim($value['prmt_amt']);
					$trans_data['prmt_disc_per'] 	= trim($value['prmt_disc_per']);
					$trans_data['prmt_disc_amt'] 	= trim($value['prmt_disc_amt']);
					$trans_data['prmt_taxable_amt'] = trim($value['prmt_taxable_amt']);
					$trans_data['prmt_extra_amt'] 	= trim($value['prmt_extra_amt']);
					$trans_data['prmt_actual_taxable_amt'] 	= trim($value['prmt_actual_taxable_amt']);
					$trans_data['prmt_sgst_per'] 	= trim($value['prmt_sgst_per']);
					$trans_data['prmt_sgst_amt'] 	= trim($value['prmt_sgst_amt']);
					$trans_data['prmt_cgst_per'] 	= trim($value['prmt_cgst_per']);
					$trans_data['prmt_cgst_amt'] 	= trim($value['prmt_cgst_amt']);
					$trans_data['prmt_igst_per'] 	= trim($value['prmt_igst_per']);
					$trans_data['prmt_igst_amt'] 	= trim($value['prmt_igst_amt']);
					$trans_data['prmt_total_amt'] 	= trim($value['prmt_total_amt']);
					$trans_data['prmt_description'] = trim($value['prmt_description']);
					$trans_data['prmt_updated_by'] 	= $_SESSION['user_id'];
					$trans_data['prmt_updated_at'] 	= date('Y-m-d H:i:s');
					if($this->db_operations->data_update('purchase_readymade_trans', $trans_data, 'prmt_id', $value['prmt_id']) < 1){
						return ['msg' => 'Transaction not updated.'];
					}
					$trans_data['prmm_supplier_id'] = isset($post_data['prmm_supplier_id']) ? $post_data['prmm_supplier_id'] : 0;
					if(empty($prev_data[0]['prmt_prmm_id'])){  
						$result = $this->add_barcode($id, $value['prmt_id'], $trans_data);
						if(!isset($result['status'])) return $result;
					}else{
						$result = $this->update_barcode($id, $value['prmt_id'], $trans_data);
						if(!isset($result['status'])) return $result;
					}
				}
			}
			return ['status' => TRUE];
		}
		
    // purchase_readymade_trans

    // barcode_master
        public function add_barcode($prmm_id, $prmt_id, $trans_data){ 
        	
        	$prmt_qty = ($trans_data['prmt_readymade_category_id'] == 3) ? 1 : $trans_data['prmt_qty'];
        	$brmm_prmt_qty = ($trans_data['prmt_readymade_category_id'] == 3) ? $trans_data['prmt_qty'] : 1;

			for ($i = 1; $i <= $prmt_qty ; $i++) {  
				$year  									= date('y')+70;
				$month 									= date('m');
				$barcode_master 						= [];	
				$barcode_master['brmm_barcode_year'] 	= date('Y');
				$barcode_master['brmm_barcode_month'] 	= $month;
				$barcode_master['brmm_counter']			= $this->model->generate_barcode();
				$barcode_master['brmm_item_code'] 		= $year.''.$month.''.$barcode_master['brmm_counter'];
				$barcode_master['brmm_roll_no'] 		= $barcode_master['brmm_item_code'];
				$barcode_master['brmm_prmm_id']			= $prmm_id;
				$barcode_master['brmm_prmt_id']			= $prmt_id;
				$barcode_master['brmm_supplier_id']		= $trans_data['prmm_supplier_id'];
				$barcode_master['brmm_product_id']		= $trans_data['prmt_product_id'];
				$barcode_master['brmm_design_id']		= $trans_data['prmt_design_id'];

				$barcode_master['brmm_readymade_category_id'] = $trans_data['prmt_readymade_category_id'];
				$barcode_master['brmm_color_id']		= $trans_data['prmt_color_id'];
				$barcode_master['brmm_size_id']			= $trans_data['prmt_size_id'];
				$barcode_master['brmm_gender_id']		= $trans_data['prmt_gender_id'];
				$barcode_master['brmm_cost_char']		= $trans_data['prmt_cost_char'];
				$barcode_master['brmm_description']		= $trans_data['prmt_description'];
				$barcode_master['brmm_prmt_qty']		= $brmm_prmt_qty;
				$barcode_master['brmm_mrp']			    = $trans_data['prmt_mrp'];
				$barcode_master['brmm_prmt_rate']		= $trans_data['prmt_rate'];
				$barcode_master['brmm_prmt_amt']		= $trans_data['prmt_rate'] * $trans_data['prmt_qty'];
				$barcode_master['brmm_disc_amt']		= $trans_data['prmt_disc_amt'] / $trans_data['prmt_qty'];
				// $barcode_master['brmm_taxable_amt']	= $trans_data['prmt_taxable_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_taxable_amt']		= $trans_data['prmt_actual_taxable_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_sgst_amt']		= $trans_data['prmt_sgst_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_cgst_amt']		= $trans_data['prmt_cgst_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_igst_amt']		= $trans_data['prmt_igst_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_total_amt']		= $trans_data['prmt_total_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_delete_status'] 	= false;
				$barcode_master['brmm_branch_id'] 		= $_SESSION['user_branch_id'];
				$barcode_master['brmm_fin_year'] 		= $_SESSION['fin_year'];
				$barcode_master['brmm_created_by'] 		= $_SESSION['user_id'];
				$barcode_master['brmm_created_at'] 		= date('Y-m-d H:i:s');
				$barcode_master['brmm_updated_by'] 		= $_SESSION['user_id'];
				$barcode_master['brmm_updated_at'] 		= date('Y-m-d H:i:s');

				if($this->db_operations->data_insert('barcode_readymade_master', $barcode_master) < 1) return ['msg' => '1. Barcode readymade not added.'];
			}
			return ['status' => TRUE];
		}
		public function update_barcode($prmm_id, $prmt_id, $trans_data){
			$prev_data = $this->db_operations->get_record('barcode_readymade_master', ['brmm_prmt_id' => $prmt_id, 'brmm_delete_status' => false]);
			if(empty($prev_data)) return ['msg' => '1. Barcode readymade not found.'];
			
			$brm_qty = $this->model->get_barcode_qty($prmt_id);
			$qty_cnt = 0;

			foreach ($prev_data as $key => $value) {
				$qty_cnt++;
				$barcode_master 						= [];	
				$barcode_master['brmm_supplier_id']		= $trans_data['prmm_supplier_id'];
				$barcode_master['brmm_product_id']		= $trans_data['prmt_product_id'];
				$barcode_master['brmm_design_id']		= $trans_data['prmt_design_id'];

				$barcode_master['brmm_readymade_category_id']= $trans_data['prmt_readymade_category_id'];
				$barcode_master['brmm_color_id']		= $trans_data['prmt_color_id'];
				$barcode_master['brmm_size_id']			= $trans_data['prmt_size_id'];
				$barcode_master['brmm_gender_id']		= $trans_data['prmt_gender_id'];
				$barcode_master['brmm_cost_char']		= $trans_data['prmt_cost_char'];
				$barcode_master['brmm_description']		= $trans_data['prmt_description'];
				$barcode_master['brmm_mrp']			    = $trans_data['prmt_mrp'];
				$barcode_master['brmm_prmt_rate']		= $trans_data['prmt_rate'];
				$barcode_master['brmm_prmt_amt']		= $trans_data['prmt_rate'] * $trans_data['prmt_qty'];
				$barcode_master['brmm_disc_amt']		= $trans_data['prmt_disc_amt'] / $trans_data['prmt_qty'];
				// $barcode_master['brnm_taxable_amt']	= $trans_data['prmt_taxable_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_taxable_amt']		= $trans_data['prmt_actual_taxable_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_sgst_amt']		= $trans_data['prmt_sgst_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_cgst_amt']		= $trans_data['prmt_cgst_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_igst_amt']		= $trans_data['prmt_igst_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_total_amt']		= $trans_data['prmt_total_amt'] / $trans_data['prmt_qty'];
				$barcode_master['brmm_updated_by'] 		= $_SESSION['user_id'];
				$barcode_master['brmm_updated_at'] 		= date('Y-m-d H:i:s');
				if($qty_cnt <= $trans_data['prmt_qty']){
					if($this->db_operations->data_update('barcode_readymade_master', $barcode_master, 'brmm_id', $value['brmm_id']) < 1) return ['msg' => '1. Barcode readymade not update.'];
				}else{
					$update_data 						= [];
					$update_data['brmm_delete_status'] 	= true; 
					$update_data['brmm_updated_by'] 	= $_SESSION['user_id']; 
					$update_data['brmm_updated_at'] 	= date('Y-m-d H:i:s');

					if($this->db_operations->data_update('barcode_readymade_master', $update_data, 'brmm_id', $value['brmm_id']) < 1) return ['msg' => '1. Barcode readymade delete status not set as true'];
				}
			}
			if($trans_data['prmt_qty'] > $brm_qty){
				for($i=1; $i <= ($trans_data['prmt_qty'] - $brm_qty); $i++){
					$year  									= date('y')+70;
					$month 									= date('m');

					$barcode_master 						= [];	
					$barcode_master['brmm_barcode_year'] 		= date('Y');
					$barcode_master['brmm_barcode_month'] 	= $month;
					$barcode_master['brmm_counter']			= $this->model->generate_barcode();
					$barcode_master['brmm_item_code'] 		= $year.''.$month.''.$barcode_master['brmm_counter'];
					$barcode_master['brmm_roll_no'] 		= $barcode_master['brmm_item_code'];
					$barcode_master['brmm_prmm_id']			= $prmm_id;
					$barcode_master['brmm_prmt_id']			= $prmt_id;
					$barcode_master['brmm_supplier_id']		= $trans_data['prmm_supplier_id'];
					$barcode_master['brmm_product_id']		= $trans_data['prmt_product_id'];
					$barcode_master['brmm_design_id']		= $trans_data['prmt_design_id'];

					$barcode_master['brmm_readymade_category_id']= $trans_data['prmt_readymade_category_id'];
					$barcode_master['brmm_color_id']		= $trans_data['prmt_color_id'];
					$barcode_master['brmm_size_id']			= $trans_data['prmt_size_id'];
					$barcode_master['brmm_gender_id']		= $trans_data['prmt_gender_id'];
					$barcode_master['brmm_cost_char']		= $trans_data['prmt_cost_char'];
					$barcode_master['brmm_description']		= $trans_data['prmt_description'];
					$barcode_master['brmm_prmt_qty']		= 1;
					$barcode_master['brmm_mrp']			    = $trans_data['prmt_mrp'];
					$barcode_master['brmm_prmt_rate']		= $trans_data['prmt_rate'];
					$barcode_master['brmm_prmt_amt']		= $trans_data['prmt_rate'] * $trans_data['prmt_qty'];
					$barcode_master['brmm_disc_amt']		= $trans_data['prmt_disc_amt'] / $trans_data['prmt_qty'];
					// $barcode_master['brmm_taxable_amt']	= $trans_data['prmt_taxable_amt'] / $trans_data['prmt_qty'];
					$barcode_master['brmm_taxable_amt']		= $trans_data['prmt_actual_taxable_amt'] / $trans_data['prmt_qty'];
					$barcode_master['brmm_sgst_amt']		= $trans_data['prmt_sgst_amt'] / $trans_data['prmt_qty'];
					$barcode_master['brmm_cgst_amt']		= $trans_data['prmt_cgst_amt'] / $trans_data['prmt_qty'];
					$barcode_master['brmm_igst_amt']		= $trans_data['prmt_igst_amt'] / $trans_data['prmt_qty'];
					$barcode_master['brmm_total_amt']		= $trans_data['prmt_total_amt'] / $trans_data['prmt_qty'];
					$barcode_master['brmm_delete_status'] 	= false;
					$barcode_master['brmm_branch_id'] 		= $_SESSION['user_branch_id'];
					$barcode_master['brmm_fin_year'] 		= $_SESSION['fin_year'];
					$barcode_master['brmm_created_by'] 		= $_SESSION['user_id'];
					$barcode_master['brmm_created_at'] 		= date('Y-m-d H:i:s');
					$barcode_master['brmm_updated_by'] 		= $_SESSION['user_id'];
					$barcode_master['brmm_updated_at'] 		= date('Y-m-d H:i:s');
					if($this->db_operations->data_insert('barcode_readymade_master', $barcode_master) < 1) return ['msg' => '1. Barcode readymade not added.'];
				}
			}
			return ['status' => TRUE];
		}
		public function delete_barcode($prmt_id){
			$data = $this->db_operations->get_record('barcode_readymade_master', ['brmm_prmt_id' => $prmt_id, 'brmm_delete_status' => false]);
			if(empty($data)) return ['msg' => '2. Barcode readymade not found.'];

			foreach ($data as $key => $value){
				if($this->model->isBarcodeExist($value['brmm_id'])) return ['msg' => '2. Not allowed to delete barcode readymade.'];
				$update_data 						= [];
				$update_data['brmm_delete_status'] 	= true; 
				$update_data['brmm_updated_by'] 	= $_SESSION['user_id']; 
				$update_data['brmm_updated_at'] 	= date('Y-m-d H:i:s'); 
				if($this->db_operations->data_update('barcode_readymade_master', $update_data, 'brmm_id', $value['brmm_id']) < 1){
					return ['msg' => '2. Barcode readymade delete status not set as true'];
				}
			}
			return ['status' => TRUE];
		}
    // barcode_master
}
?>