<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class purchase extends my_controller{ 
    protected $menu;
    protected $sub_menu;
	public function __construct(){
        $this->menu     = 'transaction'; 
        $this->sub_menu = 'purchase'; 
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

		$data = $this->db_operations->get_record('purchase_master', ['pm_id' => $id, 'pm_delete_status' => false]);
		if(empty($data)) return ['status' => REFRESH, 'msg' => '3. Purchase not found.'];	

		if($this->model->isExist($id)) return ['msg' => '1. Not allowed to delete.'];	

		$trans_data = $this->db_operations->get_record('purchase_trans', ['pt_pm_id' => $id, 'pt_delete_status' => false]);
		if(empty($trans_data)) return ['msg' => '2. Transaction not found.'];
		
		$this->db->trans_begin();
		
		foreach ($trans_data as $key => $value) {
			if($this->model->isTransExist($value['pt_id'])){
				$this->db->trans_rollback();
				return ['msg' => '2. Not allowed to delete transaction.'];
			}

			$result = $this->delete_barcode($value['pt_id']);
			if(!isset($result['status'])) return $result;

			$update_data 						= [];
			$update_data['pt_delete_status'] 	= true; 
			$update_data['pt_updated_by'] 		= $_SESSION['user_id']; 
			$update_data['pt_updated_at'] 		= date('Y-m-d H:i:s'); 
			if($this->db_operations->data_update('purchase_trans', $update_data, 'pt_id', $value['pt_id']) < 1){
				$this->db->trans_rollback();
				return ['msg' => '2. Transaction not deleted.'];
			}
		}

		$update_data 						= [];
		$update_data['pm_entry_no'] 		= $data[0]['pm_entry_no'].''.$id; 
		$update_data['pm_delete_status'] 	= true; 
		$update_data['pm_updated_by'] 		= $_SESSION['user_id']; 
		$update_data['pm_updated_at'] 		= date('Y-m-d H:i:s'); 
		if($this->db_operations->data_update('purchase_master', $update_data, 'pm_id', $id) < 1){
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
    // purchase_master
        public function add_edit(){
			$post_data  = $this->input->post();
            $id         = $post_data['id'];
			if(!isset($post_data['trans_data']) || (isset($post_data['trans_data']) && empty($post_data['trans_data']))) return ['msg' => '1. Item not aded in list.'];
			$post_data['trans_data'] = json_decode($post_data['trans_data'], true);

			// master_data
                $master_data['pm_uuid'] 				    = trim($post_data['pm_uuid']);
                $master_data['pm_entry_no'] 				= trim($post_data['pm_entry_no']);
                $master_data['pm_entry_date'] 				= date('Y-m-d', strtotime($post_data['pm_entry_date']));
                $master_data['pm_bill_no'] 					= trim($post_data['pm_bill_no']);
                $master_data['pm_bill_date'] 				= date('Y-m-d', strtotime($post_data['pm_bill_date']));
                $master_data['pm_supplier_id'] 				= trim($post_data['pm_supplier_id']);
                $master_data['pm_gst_type'] 				= trim($post_data['pm_gst_type']);
                $master_data['pm_total_qty'] 				= trim($post_data['pm_total_qty']);
                $master_data['pm_total_mtr'] 				= trim($post_data['pm_total_mtr']);
                $master_data['pm_notes'] 					= trim($post_data['pm_notes']);
                $master_data['pm_sub_amt'] 				    = trim($post_data['pm_sub_amt']);
                $master_data['pm_disc_amt'] 				= trim($post_data['pm_disc_amt']);
                $master_data['pm_taxable_amt'] 				= trim($post_data['pm_taxable_amt']);
                $master_data['pm_extra_amt'] 				= trim($post_data['pm_extra_amt']);
                $master_data['pm_sgst_amt']				    = trim($post_data['pm_sgst_amt']);
                $master_data['pm_cgst_amt']				    = trim($post_data['pm_cgst_amt']);
                $master_data['pm_igst_amt']					= trim($post_data['pm_igst_amt']);
                $master_data['pm_round_off']				= trim($post_data['pm_round_off']);
                $master_data['pm_freight_amt']				= trim($post_data['pm_freight_amt']);
                $master_data['pm_bill_disc_per']			= trim($post_data['pm_bill_disc_per']);
                $master_data['pm_bill_disc_amt']			= trim($post_data['pm_bill_disc_amt']);
                $master_data['pm_total_amt']				= trim($post_data['pm_total_amt']);
                $master_data['pm_updated_by'] 				= $_SESSION['user_id'];
            // master_data

            $temp = $this->db_operations->get_record($this->sub_menu.'_master', ['pm_id !=' => $id, 'pm_bill_no' => $master_data['pm_bill_no'],'pm_supplier_id'=>$master_data['pm_supplier_id'], 'pm_delete_status' => false]);
			if(!empty($temp)) return ['msg' => 'Bill no already exists.'];

			$this->db->trans_begin();
			if($id == 0){
				$master_data['pm_entry_no'] 	= $this->model->get_max_entry_no(['entry_no' => 'pm_entry_no', 'delete_status' => 'pm_delete_status', 'fin_year' => 'pm_fin_year']);
				$master_data['pm_created_by'] 	= $_SESSION['user_id'];
				$master_data['pm_created_at'] 	= date('Y-m-d H:i:s');
				$master_data['pm_fin_year'] 	= $_SESSION['fin_year'];
				$master_data['pm_branch_id'] 	= $_SESSION['user_branch_id'];
				$uuidExist 						= $this->db_operations->get_cnt($this->sub_menu.'_master', ['pm_uuid' => $master_data['pm_uuid']]);
				if($uuidExist > 0){
					$this->db->trans_rollback();
					return ['msg' => 'Form already submited.'];
				}
				$id = $this->db_operations->data_insert('purchase_master', $master_data);
				$msg = 'Purchase added successfully.';
				if($id < 1){
					$this->db->trans_rollback();
					return ['msg' => '1. Purchase not added.'];
				}
			}else{
				$prev_data = $this->db_operations->get_record('purchase_master', ['pm_id' => $id, 'pm_delete_status' => false]);
				if(empty($prev_data)){
					$this->db->trans_rollback();
					return ['status' => REFRESH, 'msg' => '1. Purchase not found.'];
				}
				$msg = 'Purchase updated successfully.';
				if($this->db_operations->data_update('purchase_master', $master_data, 'pm_id', $id) < 1){
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
			$data['name'] 	= strtoupper($master_data['pm_entry_no']);
			return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
		}
    // purchase_master

    // purchase_trans
        public function add_transaction(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            
			if(!isset($post_data['fabric_id']) || (isset($post_data['fabric_id']) && empty($post_data['fabric_id']))) return ['msg' => '1. Fabric is required.'];
			
			if(!isset($post_data['design_id']) || (isset($post_data['design_id']) && empty($post_data['design_id']))) return ['msg' => '1. Design is required.'];

			if(!isset($post_data['qty']) || (isset($post_data['qty']) && empty($post_data['qty']))){
                return ['msg' => '1. Qty is required.'];
            }else{
                if($post_data['qty'] <= 0) return ['msg' => '1. Invalid Qty.'];
            }
            if(!isset($post_data['mtr']) || (isset($post_data['mtr']) && empty($post_data['mtr']))){
                return ['msg' => '1. Mtr is required.'];
            }else{
                if($post_data['mtr'] <= 0) return ['msg' => '1. Invalid Mtr.'];	
            }
            if(!isset($post_data['rate']) || (isset($post_data['rate']) && empty($post_data['rate']))){
                return ['msg' => '1. Rate is required.'];
            }else{
                if($post_data['rate'] <= 0) return ['msg' => '1. Invalid Rate.'];	
            }
            if(!isset($post_data['mrp']) || (isset($post_data['mrp']) && empty($post_data['mrp']))){
                return ['msg' => '1. Mrp is required.'];
            }else{
                if($post_data['mrp'] <= 0) return ['msg' => '1. Invalid Mrp.'];	
            }
            $trans_data 					= [];
            $trans_data['pt_fabric_id'] 	= isset($post_data['fabric_id']) ? $post_data['fabric_id'] : 0;
            $trans_data['pt_design_id'] 	= isset($post_data['design_id']) ? $post_data['design_id'] : 0;
            $trans_data['pt_category_id'] 	= isset($post_data['category_id']) ? $post_data['category_id'] : 0;
            $trans_data['pt_color_id'] 		= isset($post_data['color_id']) ? $post_data['color_id'] : 0;
            $trans_data['pt_hsn_id'] 		= isset($post_data['hsn_id']) ? $post_data['hsn_id'] : 0;
            $trans_data['pt_width_id'] 		= isset($post_data['width_id']) ? $post_data['width_id'] : 0;
            $trans_data['pt_cost_char'] 	= trim($post_data['cost_char']);
            $trans_data['pt_mrp'] 			= trim($post_data['mrp']);
            $trans_data['pt_qty'] 			= trim($post_data['qty']);
            $trans_data['pt_mtr'] 			= trim($post_data['mtr']);
            $trans_data['pt_total_mtr'] 	= trim($post_data['total_mtr']);
            $trans_data['pt_rate'] 			= trim($post_data['rate']);
            $trans_data['pt_amt'] 			= trim($post_data['amt']);
            $trans_data['pt_disc_per'] 		= trim($post_data['disc_per']);
            $trans_data['pt_disc_amt'] 		= trim($post_data['disc_amt']);
            $trans_data['pt_taxable_amt'] 	= trim($post_data['taxable_amt']);
            $trans_data['pt_extra_amt'] 	= trim($post_data['extra_amt']);
            $trans_data['pt_actual_taxable_amt'] 	= trim($post_data['actual_taxable_amt']);
            $trans_data['pt_sgst_per'] 	    = trim($post_data['sgst_per']);
            $trans_data['pt_sgst_amt'] 	    = trim($post_data['sgst_amt']);
            $trans_data['pt_cgst_per'] 	    = trim($post_data['cgst_per']);
            $trans_data['pt_cgst_amt'] 	    = trim($post_data['cgst_amt']);
            $trans_data['pt_igst_per'] 	    = trim($post_data['igst_per']);
            $trans_data['pt_igst_amt'] 	    = trim($post_data['igst_amt']);

            $trans_data['pt_shirt_mrp'] 	= trim($post_data['shirt_mrp']);
            $trans_data['pt_trouser_mrp'] 	= trim($post_data['trouser_mrp']);
            $trans_data['pt_2pc_suit_mrp'] 	= trim($post_data['2pc_suit_mrp']);
            $trans_data['pt_3pc_suit_mrp'] 	= trim($post_data['3pc_suit_mrp']);
            $trans_data['pt_jacket_mrp'] 	= trim($post_data['jacket_mrp']);

            $trans_data['pt_total_amt'] 	= trim($post_data['total_amt']);
			$trans_data['pt_description'] 	= trim($post_data['description']);
            $trans_data['pt_created_by'] 	= $_SESSION['user_id'];
            $trans_data['pt_updated_by'] 	= $_SESSION['user_id'];
            $trans_data['pt_created_at'] 	= date('Y-m-d H:i:s');
            $trans_data['pt_updated_at'] 	= date('Y-m-d H:i:s');
            
			if(empty($post_data['pt_id'])){
				$trans_data['pt_id'] = $this->db_operations->data_insert('purchase_trans', $trans_data);
				if($trans_data['pt_id'] < 1) return ['msg' => '1. Purchase Transaction not added.'];
				$trans_data['isExist'] = false;
			}else{
				$trans_data['pt_id'] = $post_data['pt_id'];
			}
			
            $trans_data['encrypt_pt_id']= encrypt_decrypt("encrypt", $trans_data['pt_id'], SECRET_KEY);
            $trans_data['design_image'] = $this->model->get_design_image($trans_data['pt_design_id']);
            $trans_data['fabric_name'] 	= $this->model->get_name('fabric', $trans_data['pt_fabric_id']);
            $trans_data['design_name'] 	= $this->model->get_name('design', $trans_data['pt_design_id']);
            $trans_data['category_name'] 	= $this->model->get_name('category', $trans_data['pt_category_id']);
            $trans_data['color_name'] 	= $this->model->get_name('color', $trans_data['pt_color_id']);
            $trans_data['width_name'] 	= $this->model->get_name('width', $trans_data['pt_width_id']);
            $trans_data['hsn_name'] 	= $this->model->get_name('hsn', $trans_data['pt_hsn_id']);
			
            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Purchase Transaction added successfully.'];
        }
        public function add_update_trans($post_data, $id){
			$trans_db_data = $this->db_operations->get_record('purchase_trans', ['pt_pm_id' => $id, 'pt_delete_status' => false]);
			$ids  = $this->get_id($post_data['trans_data'],'pt_id');
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['pt_id'], $ids)){
						if($this->model->isTransExist($value['pt_id'])) return ['msg' => '1. Not allowed to delete transaction.'];
						$result = $this->delete_barcode($value['pt_id']);
						if(!isset($result['status'])) return $result;
	
						$update_data 						= [];
						$update_data['pt_delete_status'] 	= true; 
						$update_data['pt_updated_by'] 		= $_SESSION['user_id']; 
						$update_data['pt_updated_at'] 		= date('Y-m-d H:i:s'); 
						if($this->db_operations->data_update('purchase_trans', $update_data, 'pt_id', $value['pt_id']) < 1) return ['msg' => '1. Transaction not deleted.'];
					}
				}
			}
			foreach ($post_data['trans_data'] as $key => $value){
				if($value['pt_id'] != 0){
					$prev_data = $this->db_operations->get_record('purchase_trans', ['pt_id' => $value['pt_id'], 'pt_delete_status' => false]);
					if(empty($prev_data)) return ['msg' => '1. Transaction not found.'];

					$trans_data 					= [];
					$trans_data['pt_pm_id'] 		= $id;
					$trans_data['pt_pm_uuid'] 		= $post_data['pm_uuid'];
					$trans_data['pt_fabric_id'] 	= isset($value['pt_fabric_id']) ? $value['pt_fabric_id'] : 0;
					$trans_data['pt_design_id'] 	= isset($value['pt_design_id']) ? $value['pt_design_id'] : 0;
					$trans_data['pt_category_id'] 	= isset($value['pt_category_id']) ? $value['pt_category_id'] : 0;
					$trans_data['pt_color_id'] 		= isset($value['pt_color_id']) ? $value['pt_color_id'] : 0;

					$trans_data['pt_width_id'] 		= isset($value['pt_width_id']) ? $value['pt_width_id'] : 0;
					$trans_data['pt_hsn_id'] 		= isset($value['pt_hsn_id']) ? $value['pt_hsn_id'] : 0;
					$trans_data['pt_cost_char'] 	= trim($value['pt_cost_char']);
					$trans_data['pt_mrp'] 		    = trim($value['pt_mrp']);
					$trans_data['pt_qty'] 			= trim($value['pt_qty']);
					$trans_data['pt_mtr'] 			= trim($value['pt_mtr']);
					$trans_data['pt_total_mtr'] 	= trim($value['pt_total_mtr']);
					$trans_data['pt_rate'] 			= trim($value['pt_rate']);
					$trans_data['pt_amt'] 			= trim($value['pt_amt']);
					$trans_data['pt_disc_per'] 	    = trim($value['pt_disc_per']);
					$trans_data['pt_disc_amt'] 		= trim($value['pt_disc_amt']);
					$trans_data['pt_taxable_amt'] 	= trim($value['pt_taxable_amt']);
					$trans_data['pt_extra_amt'] 	= trim($value['pt_extra_amt']);
					$trans_data['pt_actual_taxable_amt'] 	= trim($value['pt_actual_taxable_amt']);
					$trans_data['pt_sgst_per'] 	    = trim($value['pt_sgst_per']);
					$trans_data['pt_sgst_amt'] 	    = trim($value['pt_sgst_amt']);
					$trans_data['pt_cgst_per'] 	    = trim($value['pt_cgst_per']);
					$trans_data['pt_cgst_amt'] 	    = trim($value['pt_cgst_amt']);
					$trans_data['pt_igst_per'] 	    = trim($value['pt_igst_per']);
					$trans_data['pt_igst_amt'] 	    = trim($value['pt_igst_amt']);

					$trans_data['pt_shirt_mrp'] 	= trim($value['pt_shirt_mrp']);
            		$trans_data['pt_trouser_mrp'] 	= trim($value['pt_trouser_mrp']);
            		$trans_data['pt_2pc_suit_mrp'] 	= trim($value['pt_2pc_suit_mrp']);
            		$trans_data['pt_3pc_suit_mrp'] 	= trim($value['pt_3pc_suit_mrp']);
            		$trans_data['pt_jacket_mrp'] 	= trim($value['pt_jacket_mrp']);

					$trans_data['pt_total_amt'] 	= trim($value['pt_total_amt']);
					$trans_data['pt_description'] 	= trim($value['pt_description']);
					$trans_data['pt_updated_by'] 	= $_SESSION['user_id'];
					$trans_data['pt_updated_at'] 	= date('Y-m-d H:i:s');
					if($this->db_operations->data_update('purchase_trans', $trans_data, 'pt_id', $value['pt_id']) < 1){
						return ['msg' => 'Transaction not updated.'];
					}
					$trans_data['pm_supplier_id'] = isset($post_data['pm_supplier_id']) ? $post_data['pm_supplier_id'] : 0;
					if(empty($prev_data[0]['pt_pm_id'])){
						$result = $this->add_barcode($id, $value['pt_id'], $trans_data);
						if(!isset($result['status'])) return $result;
					}else{
						$result = $this->update_barcode($id, $value['pt_id'], $trans_data);
						if(!isset($result['status'])) return $result;
					}
				}
			}
			return ['status' => TRUE];
		}
		
    // purchase_trans

    // barcode_master
        public function add_barcode($pm_id, $pt_id, $trans_data){
			for ($i = 1; $i <= $trans_data['pt_qty'] ; $i++) { 
				$year  									= date('y');
				$month 									= date('m');
				
				$barcode_master 						= [];	
				$barcode_master['bm_barcode_year'] 		= date('Y');
				$barcode_master['bm_barcode_month'] 	= $month;
				$barcode_master['bm_counter']			= $this->model->generate_barcode();
				$barcode_master['bm_item_code'] 		= $year.''.$month.''.$barcode_master['bm_counter'];
				$barcode_master['bm_roll_no'] 			= $barcode_master['bm_item_code'];
				$barcode_master['bm_pm_id']				= $pm_id;
				$barcode_master['bm_pt_id']				= $pt_id;
				$barcode_master['bm_supplier_id']		= $trans_data['pm_supplier_id'];
				$barcode_master['bm_fabric_id']			= $trans_data['pt_fabric_id'];
				$barcode_master['bm_design_id']			= $trans_data['pt_design_id'];
				$barcode_master['bm_category_id']		= $trans_data['pt_category_id'];
				$barcode_master['bm_color_id']			= $trans_data['pt_color_id'];

				$barcode_master['bm_hsn_id']			= $trans_data['pt_hsn_id'];
				$barcode_master['bm_width_id']			= $trans_data['pt_width_id'];
				$barcode_master['bm_cost_char']			= $trans_data['pt_cost_char'];
				$barcode_master['bm_description']		= $trans_data['pt_description'];
				$barcode_master['bm_pt_qty']			= 1;
				$barcode_master['bm_mrp']			    = $trans_data['pt_mrp'];
				$barcode_master['bm_pt_rate']			= $trans_data['pt_rate'];
				$barcode_master['bm_pt_mtr']			= $trans_data['pt_mtr'];
				$barcode_master['bm_pt_amt']			= $trans_data['pt_rate'] * $trans_data['pt_mtr'];
				$barcode_master['bm_disc_amt']			= $trans_data['pt_disc_amt'] / $trans_data['pt_qty'];
				// $barcode_master['bm_taxable_amt']		= $trans_data['pt_taxable_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_taxable_amt']		= $trans_data['pt_actual_taxable_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_sgst_amt']			= $trans_data['pt_sgst_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_cgst_amt']			= $trans_data['pt_cgst_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_igst_amt']			= $trans_data['pt_igst_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_total_amt']			= $trans_data['pt_total_amt'] / $trans_data['pt_qty'];

				$barcode_master['bm_shirt_mrp'] 		= $trans_data['pt_shirt_mrp'];
        		$barcode_master['bm_trouser_mrp'] 		= $trans_data['pt_trouser_mrp'];
        		$barcode_master['bm_2pc_suit_mrp'] 		= $trans_data['pt_2pc_suit_mrp'];
        		$barcode_master['bm_3pc_suit_mrp'] 		= $trans_data['pt_3pc_suit_mrp'];
        		$barcode_master['bm_jacket_mrp'] 		= $trans_data['pt_jacket_mrp'];

				$barcode_master['bm_delete_status'] 	= false;
				$barcode_master['bm_branch_id'] 		= $_SESSION['user_branch_id'];
				$barcode_master['bm_fin_year'] 			= $_SESSION['fin_year'];
				$barcode_master['bm_created_by'] 		= $_SESSION['user_id'];
				$barcode_master['bm_created_at'] 		= date('Y-m-d H:i:s');
				$barcode_master['bm_updated_by'] 		= $_SESSION['user_id'];
				$barcode_master['bm_updated_at'] 		= date('Y-m-d H:i:s');

				if($this->db_operations->data_insert('barcode_master', $barcode_master) < 1) return ['msg' => '1. Barcode not added.'];
			}
			return ['status' => TRUE];
		}
		public function update_barcode($pm_id, $pt_id, $trans_data){
			$prev_data = $this->db_operations->get_record('barcode_master', ['bm_pt_id' => $pt_id, 'bm_delete_status' => false]);
			if(empty($prev_data)) return ['msg' => '1. Barcode not found.'];

			foreach ($prev_data as $key => $value) {
				if($trans_data['pt_mtr'] < $value['bm_ot_mtr']) return ['msg' => '1. Purchase mtr should not be less than order mtr.'];
				
				$barcode_master 						= [];	
				$barcode_master['bm_supplier_id']		= $trans_data['pm_supplier_id'];
				$barcode_master['bm_fabric_id']			= $trans_data['pt_fabric_id'];
				$barcode_master['bm_design_id']			= $trans_data['pt_design_id'];
				$barcode_master['bm_category_id']		= $trans_data['pt_category_id'];
				$barcode_master['bm_color_id']			= $trans_data['pt_color_id'];

				$barcode_master['bm_hsn_id']			= $trans_data['pt_hsn_id'];
				$barcode_master['bm_width_id']			= $trans_data['pt_width_id'];
				$barcode_master['bm_cost_char']			= $trans_data['pt_cost_char'];
				$barcode_master['bm_description']		= $trans_data['pt_description'];
				$barcode_master['bm_mrp']			    = $trans_data['pt_mrp'];
				$barcode_master['bm_pt_rate']			= $trans_data['pt_rate'];
				$barcode_master['bm_pt_mtr']			= $trans_data['pt_mtr'];
				$barcode_master['bm_pt_amt']			= $trans_data['pt_rate'] * $trans_data['pt_mtr'];
				$barcode_master['bm_disc_amt']			= $trans_data['pt_disc_amt'] / $trans_data['pt_qty'];
				// $barcode_master['bm_taxable_amt']		= $trans_data['pt_taxable_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_taxable_amt']		= $trans_data['pt_actual_taxable_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_sgst_amt']			= $trans_data['pt_sgst_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_cgst_amt']			= $trans_data['pt_cgst_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_igst_amt']			= $trans_data['pt_igst_amt'] / $trans_data['pt_qty'];
				$barcode_master['bm_total_amt']			= $trans_data['pt_total_amt'] / $trans_data['pt_qty'];

				$barcode_master['bm_shirt_mrp'] 		= $trans_data['pt_shirt_mrp'];
        		$barcode_master['bm_trouser_mrp'] 		= $trans_data['pt_trouser_mrp'];
        		$barcode_master['bm_2pc_suit_mrp'] 		= $trans_data['pt_2pc_suit_mrp'];
        		$barcode_master['bm_3pc_suit_mrp'] 		= $trans_data['pt_3pc_suit_mrp'];
        		$barcode_master['bm_jacket_mrp'] 		= $trans_data['pt_jacket_mrp'];

				$barcode_master['bm_updated_by'] 		= $_SESSION['user_id'];
				$barcode_master['bm_updated_at'] 		= date('Y-m-d H:i:s');
	
				if($this->db_operations->data_update('barcode_master', $barcode_master, 'bm_id', $value['bm_id']) < 1) return ['msg' => '1. Barcode not update.'];
			}


			return ['status' => TRUE];
		}
		public function delete_barcode($pt_id){
			$data = $this->db_operations->get_record('barcode_master', ['bm_pt_id' => $pt_id, 'bm_delete_status' => false]);
			if(empty($data)) return ['msg' => '2. Barcode not found.'];

			foreach ($data as $key => $value){
				if($this->model->isBarcodeExist($value['bm_id'])) return ['msg' => '2. Not allowed to delete barcode.'];
				$update_data 						= [];
				$update_data['bm_delete_status'] 	= true; 
				$update_data['bm_updated_by'] 		= $_SESSION['user_id']; 
				$update_data['bm_updated_at'] 		= date('Y-m-d H:i:s'); 
				if($this->db_operations->data_update('barcode_master', $update_data, 'bm_id', $value['bm_id']) < 1){
					return ['msg' => '2. Barcode delete status not set as true'];
				}
			}
			return ['status' => TRUE];
		}
    // barcode_master
}
?>