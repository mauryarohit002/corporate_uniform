<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class order extends my_controller{ 
    protected $menu;
    protected $sub_menu;
	public function __construct(){
        $this->menu     = 'transaction'; 
        $this->sub_menu = 'order'; 
        parent::__construct($this->menu, $this->sub_menu);
    }

    public function remove(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $result     = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
        if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

        $this->db->trans_begin();

            $result = $this->delete_payment_mode_trans(['opmt_om_id' => $id, 'opmt_delete_status' => false]);
            if(!isset($result['status'])){ 
                $this->db->trans_rollback();
                return $result;
            }

            $result = $this->delete_trans(['ot_om_id' => $id,'ot_ot_id' => 0, 'ot_delete_status' => false]);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }

            $result = $this->delete_master(['om_id' => $id, 'om_delete_status' => false]);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return ['msg' => '3. Transaction Rollback.'];
            }
        $this->db->trans_commit();

        return ['status' => TRUE, 'msg' => 'Order deleted successfully'];
    }

    public function delete_payment_mode_trans($clause){
        $data = $this->db_operations->get_record('order_payment_mode_trans', $clause);
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $update_data                        = [];
                $update_data['opmt_delete_status'] = true;
                $update_data['opmt_updated_by']    = $_SESSION['user_id'];
                $update_data['opmt_updated_at']    = date('Y-m-d H:i:s');;
                if($this->db_operations->data_update('order_payment_mode_trans', $update_data, 'opmt_id', $value['opmt_id']) < 1){
                    return ['msg' => '1. Payment mode transaction not deleted.'];
                }
            }
        }
        return ['status' => TRUE];
    }

    public function delete_master($clause){
        $data = $this->db_operations->get_record($this->sub_menu.'_master', $clause);
        if(empty($data)) return ['msg' => '3. Order not found.'];

        foreach ($data as $key => $value){
            if($this->model->isExist($value['om_id'])) return ['msg' => '1. Not allowed to delete.'];
            
            $prev_data = $this->db_operations->get_record('order_master', ['om_id' => $value['om_id']]);
            if(!empty($prev_data)){
                if($this->db_operations->delete_record('order_master', ['om_id' => $value['om_id']]) < 1) return ['msg' => '1. Entry no. not deleted.'];
            }

            $update_data                        = [];
            $update_data['om_entry_no']         = $data[0]['om_entry_no'].''.$value['om_id']; 
            $update_data['om_delete_status']    = true; 
            $update_data['om_updated_by']       = $_SESSION['user_id']; 
            $update_data['om_updated_at']       = date('Y-m-d H:i:s');
            if($this->db_operations->data_update($this->sub_menu.'_master', $update_data, 'om_id', $value['om_id']) < 1) return ['msg' => '1. Order not deleted.'];
        }
        return ['status' => TRUE];
    }

    public function get_payment_mode_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $data       = $this->model->get_payment_mode_data($id);
        if(empty($data)) return ['msg' => '1. Payment mode not define.'];
        return ['status' => TRUE, 'data' => $data, 'msg' => 'Payment mode fetched successfully.'];
    }
    public function get_customer_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $data       = $this->model->get_customer_data($id);
        return['status' => TRUE, 'data' => $data, 'msg' => 'Customer fetched successfully.'];
	}
    public function get_barcode_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $mtr        = $post_data['mtr'];
        // echo "<pre>"; print_r($post_data); exit;
        $data = $this->model->get_barcode_data($id, $mtr);
        if((empty($data))) return ['msg' => '1. Barcode not found.'];
        // echo "<pre>"; print_r($data); exit;
        if($data[0]['bm_delete_status'] == 1) return ['msg' => '1. Barcode is deleted.'];
        if($data[0]['bal_qty'] <= 0) return ['msg' => '1. Barcode not available.'];

        return['status' => TRUE, 'data' => $data, 'msg' => 'Barcode scanned.'];
	}
    public function get_readymade_barcode_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $qty        = $post_data['qty'];
        // echo "<pre>"; print_r($post_data); exit;
        $data = $this->model->get_readymade_barcode_data($id, $qty);
        if((empty($data))) return ['msg' => '1. Readymade Barcode not found.'];
        // echo "<pre>"; print_r($data); exit;
        if($data[0]['brmm_delete_status'] == 1) return ['msg' => '1. Readymade Barcode is deleted.'];
        if($data[0]['bal_qty'] <= 0) return ['msg' => '1. Readymade Barcode not available.'];

        return['status' => TRUE, 'data' => $data, 'msg' => ' Readymade Barcode scanned.'];
    }
    public function get_apparel_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        // echo "<pre>"; print_r($post_data); exit;

        $data = $this->model->get_apparel_data($id);
        if((empty($data))) return ['msg' => '1. Apparel not found.'];
        
        return['status' => TRUE, 'data' => $data, 'msg' => 'Apparel data fetched successfully.'];
	}

    public function get_design_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        // echo "<pre>"; print_r($post_data); exit;

        $data = $this->model->get_design_data($id);
        if((empty($data))) return ['msg' => '1. Design not found.'];
        
        return['status' => TRUE, 'data' => $data, 'msg' => 'Design data fetched successfully.'];
    }
    public function get_sku_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $data = $this->model->get_sku_data($id);
        if((empty($data))) return ['msg' => '1. Sku not found.'];
        $data[0]['design_data'] = $this->model->get_sku_design_data($id);
        if((empty($data[0]['design_data']))) return ['msg' => '1. Fabric consumption not define in sku.'];

        return['status' => TRUE, 'data' => $data, 'msg' => 'Sku data fetched successfully.'];
    }
   
    public function get_new_measurement_data(){
        $post_data                  = $this->input->post();
        $ids                        = isset($post_data['ids']) ? json_decode($post_data['ids'], true) : [];
        $data = [];
        foreach ($ids as $key => $value) {
            $om_id                  = $post_data['om_id'];
            $ot_id                  = $value['ot_id'];
            $customer_id            = $post_data['customer_id'];
            $apparel_id             = $value['apparel_id'];
            $measurement_data       = $this->model->get_measurement_data($om_id, $ot_id, $customer_id, $apparel_id);
            $style_data             = $this->model->get_style_data($om_id, $ot_id, $customer_id, $apparel_id);
            $style_priority_data    = $this->model->get_style_priority_data($om_id, $ot_id, $customer_id, $apparel_id);
            if(empty($measurement_data) && empty($style_data)) return['msg' => '2. Measurement not found.'];
            array_push($data, ['apparel_data' => $value, 'measurement_data' => $measurement_data, 'style_data' => $style_data, 'style_priority_data' => $style_priority_data]);
        }

        return['status' => TRUE, 'data' => $data, 'msg' => 'Measurement fetched successfully.'];
    }

    public function get_sku_measurement_data(){
        $post_data                  = $this->input->post();
        $ids                        = isset($post_data['ids']) ? json_decode($post_data['ids'], true) : [];
        // echo "<pre>"; print_r($post_data); exit;
        $data = [];
        foreach ($ids as $key => $value) {
            $om_id                  = $post_data['om_id'];
            $ot_id                  = $value['ot_id'];
            $customer_id            = $post_data['customer_id'];
            $apparel_id             = $value['apparel_id'];
            $measurement_data       = $this->model->get_measurement_data($om_id, $ot_id, $customer_id, $apparel_id);
            $style_data             = $this->model->get_style_data($om_id, $ot_id, $customer_id, $apparel_id);
            $style_priority_data    = $this->model->get_style_priority_data($om_id, $ot_id, $customer_id, $apparel_id);
            if(empty($measurement_data) && empty($style_data)) return['msg' => '2. Measurement not found.'];
            array_push($data, ['apparel_data' => $value, 'measurement_data' => $measurement_data, 'style_data' => $style_data, 'style_priority_data' => $style_priority_data]);
        }
       
        return['status' => TRUE, 'data' => $data, 'msg' => 'Measurement fetched successfully.'];
    }
    
    public function get_style_image(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $data       = $this->model->get_style_image($id);
        return['status' => TRUE, 'data' => $data, 'msg' => 'Apparel style fetched successfully.'];
    }
    
    public function measurement_print($om_id, $ot_id = 0){
        $record = $this->model->get_data_for_measurement_print($om_id, $ot_id);
        // echo "<pre>"; print_r($record); exit;
        $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/pdf/measurement', $record);
    }

    public function add_edit_measurement_and_style(){ 
        $post_data  = $this->input->post();
        // echo "<pre>"; print_r($post_data); exit;
        
        $this->db->trans_begin();

            $result = $this->add_update_customer_measurement_trans($post_data);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }

            $result = $this->add_update_customer_style_trans($post_data);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }

            $result = $this->add_update_customer_style_image($post_data);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            return ['msg' => '2. Transaction Rollback.'];
        }
        $this->db->trans_commit();

        return ['status' => TRUE, 'msg' => 'Measurement added successfully.'];
    }
    
    // order_master
    public function add_edit(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            
            $post_data['trans_data']    = isset($post_data['trans_data']) ? json_decode($post_data['trans_data'], true) : [];
            $post_data['sku_trans_data']= isset($post_data['sku_trans_data']) ? json_decode($post_data['sku_trans_data'], true) : [];
            if(empty($post_data['trans_data']) && empty($post_data['sku_trans_data'])) return ['msg' => '1. Item not aded in list.'];
            // echo "<pre>"; print_r($post_data); exit;
            // master_data
                $master_data['om_uuid'] 				    = trim($post_data['om_uuid']);
                $master_data['om_entry_no'] 				= trim($post_data['om_entry_no']);
                $master_data['om_entry_date'] 				= date('Y-m-d', strtotime($post_data['om_entry_date']));
                $master_data['om_status']                   = true;
                $master_data['om_trial_date'] 				= $post_data['om_trial_date'];
                $master_data['om_delivery_date'] 			= $post_data['om_delivery_date'];
                $master_data['om_billing_id'] 				= isset($post_data['om_billing_id'])?trim($post_data['om_billing_id']):0;
                $master_data['om_customer_id']              = isset($post_data['om_customer_id'])?trim($post_data['om_customer_id']):0;
                $master_data['om_salesman_id'] 				= isset($post_data['om_salesman_id'])?trim($post_data['om_salesman_id']):0;
                $master_data['om_master_id']              = isset($post_data['om_master_id'])?trim($post_data['om_master_id']):0; 
                $master_data['om_gst_type'] 				= trim($post_data['om_gst_type']);
                $master_data['om_bill_type'] 				= isset($post_data['om_bill_type']);
                $master_data['om_notes'] 			        = trim($post_data['om_notes']);
                $master_data['om_total_qty'] 				= trim($post_data['om_total_qty']);
                $master_data['om_total_mtr'] 				= trim($post_data['om_total_mtr']);
                $master_data['om_sub_amt'] 				    = trim($post_data['om_sub_amt']);
                $master_data['om_disc_amt'] 				= trim($post_data['om_disc_amt']);
                $master_data['om_taxable_amt'] 				= trim($post_data['om_taxable_amt']);
                $master_data['om_sgst_amt'] 				= trim($post_data['om_sgst_amt']);
                $master_data['om_cgst_amt'] 				= trim($post_data['om_cgst_amt']);
                $master_data['om_igst_amt'] 				= trim($post_data['om_igst_amt']);
                $master_data['om_bill_disc_per']			= trim($post_data['om_bill_disc_per']);
                $master_data['om_bill_disc_amt']			= trim($post_data['om_bill_disc_amt']);
                $master_data['om_round_off']				= trim($post_data['om_round_off']);
                $master_data['om_total_amt']				= trim($post_data['om_total_amt']);
                $master_data['om_advance_amt']				= trim($post_data['om_advance_amt']);
                $master_data['om_balance_amt']				= trim($post_data['om_balance_amt']);
                $master_data['om_updated_by'] 				= $_SESSION['user_id'];
            // master_data
                $cnt =$this->db_operations->get_cnt('order_master',['om_id!='=>$id,'om_entry_no'=>$master_data['om_entry_no'],'om_fin_year'=>$_SESSION['fin_year'],'om_delete_status'=>0]);
                if($cnt>0){
                     return ['msg' => 'Duplicate Orde No found!!'];
                }

            $this->db->trans_begin();
            if($id == 0){
                // $master_data['om_entry_no'] 	= $this->model->get_max_entry_no(['entry_no' => 'om_entry_no', 'delete_status' => 'om_delete_status', 'fin_year' => 'om_fin_year']);
                $master_data['om_created_by'] 	= $_SESSION['user_id'];
                $master_data['om_created_at'] 	= date('Y-m-d H:i:s');
                $master_data['om_fin_year'] 	= $_SESSION['fin_year'];
                $master_data['om_branch_id'] 	= $_SESSION['user_branch_id'];
                $uuidExist 						= $this->db_operations->get_cnt($this->sub_menu.'_master', ['om_uuid' => $master_data['om_uuid']]);
                if($uuidExist > 0){
                    $this->db->trans_rollback();
                    return ['msg' => 'Form already submited.'];
                }
                $id = $this->db_operations->data_insert('order_master', $master_data);
                $msg = 'Order added successfully.';
                if($id < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Order not added.'];
                }
            }else{
                $prev_data = $this->db_operations->get_record('order_master', ['om_id' => $id,'om_status'=>true, 'om_delete_status' => false]);
                if(empty($prev_data)){
                    $this->db->trans_rollback();
                    return ['status' => REFRESH, 'msg' => '1. Order not found.'];
                }
                $msg = 'Order updated successfully.';
                if($this->db_operations->data_update('order_master', $master_data, 'om_id', $id) < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Order not updated.'];
                }
            }

            $result = $this->add_update_trans($post_data, $id);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }

            $result = $this->add_update_sku_trans($post_data, $id);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }

            $result = $this->add_update_payment_mode($post_data, $id);
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
            $data['name'] 	= strtoupper($master_data['om_entry_no']);
            return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
        }
    // order_master
    
    // order_trans
    public function add_transaction(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            $data       = ($post_data['trans_type'] == "READYMADE")?(isset($post_data['brmm_id'])?$this->model->get_readymade_barcode_data($post_data['brmm_id']) : 0) : (($post_data['trans_type'] == "SWATCH")?(isset($post_data['design_id'])?$this->model->get_design_data($post_data['design_id']) : 0) :(isset($post_data['bm_id'])?$this->model->get_barcode_data($post_data['bm_id']) : 0));

            if(!empty($post_data['ot_id'])){
                $old_data = $this->db_operations->get_record($this->sub_menu.'_trans',['ot_id'=>$post_data['ot_id'],'ot_delete_status' => false]);
                if(!empty($old_data)){
                    if($post_data['trans_type'] == "READYMADE" || $post_data['trans_type'] == "STITCHING"){
                        $data[0]['bal_qty'] = $data[0]['bal_qty'] + $old_data[0]['ot_qty'];
                    }else{
                        $data[0]['bal_qty'] = $data[0]['bal_qty'] + $old_data[0]['ot_mtr'];
                    }    
                }
            }
            if(!isset($post_data['trans_type']) || (isset($post_data['trans_type']) && empty($post_data['trans_type']))) return ['msg' => '1. Type is required.'];
            
            if($post_data['trans_type'] == 'READYMADE' || $post_data['trans_type'] == 'STITCHING'){
                if(!isset($post_data['qty']) || (isset($post_data['qty']) && empty($post_data['qty']))){
                    return ['msg' => '1. Qty is required.'];
                }else{
                    if($post_data['qty'] <= 0){
                        return ['msg' => '1. Invalid Qty.'];
                    }
                }
            }else{
                if(!isset($post_data['total_mtr']) || (isset($post_data['total_mtr']) && empty($post_data['total_mtr']))){
                    return ['msg' => '1. Mtr is required.'];
                }else{
                    if($post_data['total_mtr'] <= 0) {
                        return ['msg' => '1. Invalid Mtr.'];
                    }else{
                        $mtr = empty($id) ? 0 : $post_data['total_mtr'];
                        $data = isset($post_data['bm_id']) ? $this->model->get_barcode_data($post_data['bm_id'], $mtr) : 0;
                        if(!empty($data) && ($post_data['mtr'] > $data[0]['bal_qty'])) return ['msg' => '1. Mtr should be less than available mtr.'];
                    }
                }
            }
            if(!isset($post_data['rate']) || (isset($post_data['rate']) && empty($post_data['rate']))){
                return ['msg' => '1. Rate is required.'];
            }else{
                if($post_data['rate'] <= 0) return ['msg' => '1. Invalid Rate.'];	
            }
            
            // echo "<pre>"; print_r($post_data); exit;
            $trans_data 					= [];
            $trans_data['ot_om_uuid']      = trim($post_data['om_uuid']);
            $trans_data['ot_trans_type'] 	= isset($post_data['trans_type']) ? $post_data['trans_type'] : 0;
            $trans_data['ot_apparel_id'] 	= isset($post_data['apparel_id']) ? $post_data['apparel_id'] : 0;
            $trans_data['ot_bm_id']         = isset($post_data['bm_id']) ? $post_data['bm_id'] : 0;
            $trans_data['ot_brmm_id'] 	    = isset($post_data['brmm_id']) ? $post_data['brmm_id'] : 0;
            $trans_data['ot_design_id']     = isset($post_data['design_id']) ? $post_data['design_id'] : 0;
            $trans_data['ot_qty'] 			= trim($post_data['qty']);
            $trans_data['ot_mtr'] 			= trim($post_data['mtr']);
            $trans_data['ot_total_mtr'] 	= trim($post_data['total_mtr']);
            $trans_data['ot_rate'] 			= trim($post_data['rate']);
            $trans_data['ot_amt'] 			= trim($post_data['amt']);
            $trans_data['ot_disc_per'] 		= trim($post_data['disc_per']);
            $trans_data['ot_disc_amt'] 		= trim($post_data['disc_amt']);
            $trans_data['ot_taxable_amt'] 	= trim($post_data['taxable_amt']);
            $trans_data['ot_sgst_per'] 	    = trim($post_data['sgst_per']);
            $trans_data['ot_sgst_amt'] 	    = trim($post_data['sgst_amt']);
            $trans_data['ot_cgst_per'] 	    = trim($post_data['cgst_per']);
            $trans_data['ot_cgst_amt'] 	    = trim($post_data['cgst_amt']);
            $trans_data['ot_igst_per'] 	    = trim($post_data['igst_per']);
            $trans_data['ot_igst_amt'] 	    = trim($post_data['igst_amt']);
            $trans_data['ot_total_amt'] 	= trim($post_data['total_amt']);
            $trans_data['ot_description'] 	= trim($post_data['description']);
            $trans_data['ot_created_by'] 	= $_SESSION['user_id'];
            $trans_data['ot_updated_by'] 	= $_SESSION['user_id'];
            $trans_data['ot_created_at'] 	= date('Y-m-d H:i:s');
            $trans_data['ot_updated_at'] 	= date('Y-m-d H:i:s');

            if(empty($post_data['ot_id'])){
                $trans_data['ot_id'] = $this->db_operations->data_insert('order_trans', $trans_data);
                if($trans_data['ot_id'] < 1) return ['msg' => '1. Order Transaction not added.'];
                $trans_data['isExist'] = false;
                $trans_data['ot_om_id'] = 0;
            }else{
                $trans_data['ot_om_id'] = $id;
                $trans_data['ot_id']    = $post_data['ot_id'];
            }
            $trans_data['apparel_name'] = $this->model->get_name('apparel', $trans_data['ot_apparel_id']);
            $trans_data['design_name'] = $this->model->get_name('design', $trans_data['ot_design_id']);
            $trans_data['item_code']    = ($post_data['trans_type'] == "READYMADE")?$this->model->get_readymade_item_code($trans_data['ot_brmm_id']) : $this->model->get_item_code($trans_data['ot_bm_id']);
             $trans_data['apparel_data'] = $this->add_apparel_transaction($trans_data);
            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Order Transaction added successfully.'];
        }

        public function add_apparel_transaction($temp){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            
            if($post_data['trans_type'] == 'FABRIC') return [];
            if($post_data['trans_type'] == 'READYMADE') return [];
            if($post_data['READYMADE'] == 'SWATCH') return [];


            $apparel_data = $this->model->get_apparel_apparel_data($temp['ot_id'], $temp['ot_apparel_id']);
            if(empty($apparel_data)) return [];
            $resp = [];
            foreach ($apparel_data as $key => $value) {
                $trans_data                     = [];
                $trans_data['ot_ot_id']         = $temp['ot_id'];
                $trans_data['ot_trans_type']    = $temp['ot_trans_type'];
                $trans_data['ot_apparel_id']    = $value['ot_apparel_id'];
                $trans_data['ot_om_uuid']       = $temp['ot_om_uuid'];
                $trans_data['ot_qty']           = $temp['ot_qty'];
                $trans_data['ot_description']   = $temp['ot_description'];
                $trans_data['ot_delete_status'] = true;
                $trans_data['ot_created_by']    = $_SESSION['user_id'];
                $trans_data['ot_updated_by']    = $_SESSION['user_id'];
                $trans_data['ot_created_at']    = date('Y-m-d H:i:s');
                $trans_data['ot_updated_at']    = date('Y-m-d H:i:s');
    
                if(empty($post_data['ot_id'])){
                    $trans_data['ot_id'] = $this->db_operations->data_insert('order_trans', $trans_data);
                    if($trans_data['ot_id'] < 1) return ['msg' => '2. Order Transaction not added.'];
                    $trans_data['isExist'] = false;
                    $trans_data['ot_om_id'] = 0;
                }else{
                    $trans_data['ot_om_id'] = $id;
                    $trans_data['ot_id']    = $value['ot_id'];
                }

                $trans_data['apparel_name'] = $value['apparel_name'];
                array_push($resp, $trans_data);
            }
            return $resp;
        }

        public function add_update_trans($post_data, $id){  
            $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_om_id' => $id, 'ot_delete_status' => false,'ot_ot_id' => 0, 'ot_trans_type !=' => 'SKU']);
            $ids           = $this->get_id($post_data['trans_data'], 'ot_id');
            if(!empty($trans_db_data)){
                foreach ($trans_db_data as $key => $value){ 
                    if(!in_array($value['ot_id'], $ids)){
                        $result = $this->delete_trans(['ot_id' => $value['ot_id'], 'ot_delete_status' => false, 'ot_trans_type !=' => 'SKU']);
                        if(!isset($result['status'])) return $result;
                    }
                }
            }

            foreach ($post_data['trans_data'] as $key => $value){ 
                // trans_data
                    $trans_data                         = [];
                    $trans_data['ot_om_id']             = $id;
                    $trans_data['ot_trans_type']        = $value['ot_trans_type'];

                    $trans_data['ot_apparel_id']        = $value['ot_apparel_id'];
                    $trans_data['ot_apparel_qty']       = $value['ot_qty'];
                    $trans_data['ot_apparel_mrp']       = $value['ot_rate'];
                    $trans_data['ot_apparel_amt']       = $value['ot_amt'];

                    $trans_data['ot_qty']               = $value['ot_qty'];
                    $trans_data['ot_rate']              = $value['ot_rate'];
                    $trans_data['ot_amt']               = $value['ot_amt'];
                    
                    $trans_data['ot_bm_id']             = $value['ot_bm_id'];
                    $trans_data['ot_brmm_id']            = $value['ot_brmm_id'];
                    // $trans_data['ot_it_id']             = $value['ot_it_id'];
                    $trans_data['ot_fabric_mtr']        = $value['ot_mtr'];
                    $trans_data['ot_fabric_total_mtr']  = $value['ot_total_mtr'];
                    $trans_data['ot_fabric_mrp']        = $value['ot_rate'];
                    $trans_data['ot_fabric_rate']       = $value['ot_rate'];
                    $trans_data['ot_fabric_amt']        = $value['ot_amt'];

                    $trans_data['ot_stitching_rate']    = $value['ot_rate'];
                    $trans_data['ot_mtr']               = $value['ot_mtr'];
                    $trans_data['ot_total_mtr']         = $value['ot_total_mtr'];
                    
                    $trans_data['ot_amt']               = $value['ot_amt'];
                    $trans_data['ot_disc_per']          = $value['ot_disc_per'];
                    $trans_data['ot_disc_amt']          = $value['ot_disc_amt'];
                    $trans_data['ot_taxable_amt']       = $value['ot_taxable_amt'];
                    $trans_data['ot_sgst_per']          = $value['ot_sgst_per'];
                    $trans_data['ot_sgst_amt']          = $value['ot_sgst_amt'];
                    $trans_data['ot_cgst_per']          = $value['ot_cgst_per'];
                    $trans_data['ot_cgst_amt']          = $value['ot_cgst_amt'];
                    $trans_data['ot_igst_per']          = $value['ot_igst_per'];
                    $trans_data['ot_igst_amt']          = $value['ot_igst_amt'];
                    $trans_data['ot_total_amt']         = $value['ot_total_amt'];
                    $trans_data['ot_description']       = $value['ot_description'];
                    $trans_data['ot_delete_status']     = false;
                    $trans_data['ot_updated_by']        = $_SESSION['user_id'];
                    $trans_data['ot_updated_at']        = date('Y-m-d H:i:s');
                // trans_data
                
                if(!empty($value['ot_id'])){ 
                    $prev_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_id' => $value['ot_id']]);
                    if(empty($prev_data)) return ['msg' => '1. Transaction not found.'];
                    if(!$this->model->isTransExist($value['ot_id']))
                    {
                        if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'ot_id', $value['ot_id']) < 1) return ['msg' => '1. Transaction not updated.'];
                        if(!isset($value['apparel_data']) || (isset($value['apparel_data']) && empty($value['apparel_data'])))
                        {

                            $trans_data['ot_id']    = $value['ot_id'];
                            $trans_data['ot_qty']   = $value['ot_qty'];
                            $trans_data['ot_mrp']   = $value['ot_rate'];

                            $measurement_clause     = ['cmt_ot_id' => $value['ot_id'], 'cmt_delete_status' => true];
                            $style_clause           = ['cst_ot_id' => $value['ot_id'], 'cst_delete_status' => true];
                            $style_image_clause     = ['csit_ot_id'=> $value['ot_id'], 'csit_delete_status'=> true];
                            if(empty($prev_data[0]['ot_om_id'])){
                                // echo "<pre>";print_r($trans_data);die;
                                $result = $this->add_barcode_trans($trans_data);
                                if(!isset($result['status'])) return $result;
                            }else{

                                $measurement_clause  = ['cmt_ot_id' => $value['ot_id']];
                                $style_clause        = ['cst_ot_id' => $value['ot_id']];
                                $style_image_clause  = ['csit_ot_id'=> $value['ot_id']];
                                
                                if(!empty($trans_data['ot_apparel_id'])){
                                $result = $this->update_barcode_trans($trans_data);
                                if(!isset($result['status'])) return $result;
                                }
                                if(!empty($trans_data['ot_brmm_id'])){
                                    $result = $this->update_readymade_barcode_delete($prev_data[0]);
                                    if(!isset($result['status'])) return $result;
                                }
                                if(!empty($value['ot_bm_id'])){
                                    $result = $this->delete_barcode_master(['bm_id' => $prev_data[0]['ot_bm_id'], 'mtr' => $prev_data[0]['ot_fabric_total_mtr']]);
                                    if(!isset($result['status'])) return $result;
                                }
                            }
                            if(!empty($value['ot_bm_id'])){
                                $result = $this->add_barcode_master(['bm_id' => $trans_data['ot_bm_id'], 'mtr' => $trans_data['ot_fabric_total_mtr']]);
                                if(!isset($result['status'])) return $result;
                            }
                            if(!empty($trans_data['ot_brmm_id'])){
                                $result = $this->update_readymade_barcode_add($trans_data);
                                if(!isset($result['status'])) return $result;
                            }
                            $result = $this->update_customer_measurement_trans(['om_id' => $id, 'customer_id' => $post_data['om_customer_id']], $measurement_clause);
                            if(!isset($result['status'])) return $result;
                            $result = $this->update_customer_style_trans(['om_id' => $id, 'customer_id' => $post_data['om_customer_id']], $style_clause);
                            if(!isset($result['status'])) return $result;
                            $result = $this->update_customer_style_image_trans(['om_id' => $id, 'customer_id' => $post_data['om_customer_id']], $style_image_clause);
                            if(!isset($result['status'])) return $result;
                        
                        }else{
                            $trans_data['om_customer_id']   = $post_data['om_customer_id'];
                            $trans_data['apparel_data']     = $value['apparel_data'];
                            $result = $this->add_update_apparel_trans($trans_data);
                            if(!isset($result['status'])) return $result;
                        }
                            
                    }
                }
            }

            return ['status' => TRUE];
        }

        public function add_update_apparel_trans($data){
              // echo "<pre>"; print_r($data);die; 
            foreach ($data['apparel_data'] as $key => $value){
                // trans_data
                    $trans_data                         = [];
                    $trans_data['ot_om_id']             = $data['ot_om_id'];
                    $trans_data['ot_ot_id']             = $value['ot_ot_id'];
                    $trans_data['ot_trans_type']        = $value['ot_trans_type'];
                    $trans_data['ot_apparel_id']        = $value['ot_apparel_id'];
                    $trans_data['ot_qty']               = $value['ot_qty'];
                    $trans_data['ot_description']       = $value['ot_description'];
                    $trans_data['ot_delete_status']     = false;
                    $trans_data['ot_updated_by']        = $_SESSION['user_id'];
                    $trans_data['ot_updated_at']        = date('Y-m-d H:i:s');
                // trans_data

                if(!empty($value['ot_id'])){
                    $prev_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_id' => $value['ot_id']]);
                    if(empty($prev_data)) return ['msg' => '5. Transaction not found.'];
                    if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'ot_id', $value['ot_id']) < 1) return ['msg' => 'Transaction not updated.'];

                    $trans_data['ot_id']            = $value['ot_id'];
                    $trans_data['ot_mtr']           = 0;
                    $trans_data['ot_rate']          = 0;
                    $trans_data['ot_amt']           = 0;
                    $trans_data['ot_disc_amt']      = 0;
                    $trans_data['ot_taxable_amt']   = 0;
                    $trans_data['ot_sgst_amt']      = 0;
                    $trans_data['ot_cgst_amt']      = 0; 
                    $trans_data['ot_igst_amt']      = 0;
                    $trans_data['ot_total_amt']     = 0;

                    $measurement_clause  = ['cmt_ot_id' => $value['ot_id'], 'cmt_delete_status' => true];
                    $style_clause        = ['cst_ot_id' => $value['ot_id'], 'cst_delete_status' => true];
                    $style_image_clause  = ['csit_ot_id'=> $value['ot_id'], 'csit_delete_status'=> true];
                    
                    if(empty($prev_data[0]['ot_om_id'])){
                         // echo "<pre>"; print_r($trans_data);die; 
                        $result = $this->add_barcode_trans($trans_data);
                        if(!isset($result['status'])) return $result;
                    }else{
                        $measurement_clause  = ['cmt_ot_id' => $value['ot_id']];
                        $style_clause        = ['cst_ot_id' => $value['ot_id']];
                        $style_image_clause  = ['csit_ot_id'=> $value['ot_id']];
                        $result = $this->update_barcode_trans($trans_data);
                        if(!isset($result['status'])) return $result;
                    }

                    $result = $this->update_customer_measurement_trans(['om_id' => $data['ot_om_id'], 'customer_id' => $data['om_customer_id']], $measurement_clause);
                    if(!isset($result['status'])) return $result;

                    $result = $this->update_customer_style_trans(['om_id' => $data['ot_om_id'], 'customer_id' => $data['om_customer_id']], $style_clause);
                    if(!isset($result['status'])) return $result;

                }
            }
            return ['status' => TRUE];
        }

        
  
       
    // readymade_barcode_master
        public function update_readymade_barcode_add($trans_data){
            $data = $this->model->get_readymade_barcode_data($trans_data['ot_brmm_id']);
            if(empty($data)) return ['msg' => '2. Readymade Barcode not found.'];
            if($data[0]['brmm_delete_status'] == 1) return ['msg' => '2. Readymade Barcode is deleted.'];
            if($trans_data['ot_qty'] > $data[0]['bal_qty']) return ['msg' => '3. Readymade Barcode not available.'];
            $barcode_readymade_master = [];
            $barcode_readymade_master['brmm_ot_qty'] = $data[0]['brmm_ot_qty'] + $trans_data['ot_qty'];
            if($this->db_operations->data_update('barcode_readymade_master', $barcode_readymade_master, 'brmm_id', $trans_data['ot_brmm_id']) < 1){
                return ['msg' => 'Readymade Barcode not updated.'];   
            }
            return ['status' => TRUE];
        }
        public function update_readymade_barcode_delete($trans_data){
            if($this->model->isTransExist($trans_data['ot_id'])) return ['msg' => '6. Not allowed to delete.'];
            $data = $this->model->get_readymade_barcode_data($trans_data['ot_brmm_id']);
            if(empty($data)) return ['msg' => '3. Readymade Barcode not found'];
            if($trans_data['ot_qty'] > ($data[0]['brmm_ot_qty'])) return ['msg' => '4. Readymade Barcode not available.'];
            $barcode_readymade_master['brmm_ot_qty']= $data[0]['brmm_ot_qty'] - $trans_data['ot_qty'];
            if($this->db_operations->data_update('barcode_readymade_master', $barcode_readymade_master, 'brmm_id', $trans_data['ot_brmm_id']) < 1){
                return ['msg' => 'Readymade Barcode not updated.'];   
            }
            return ['status' => TRUE];
        }
    // readymade_barcode_master
    // payment_mode_trans
        public function add_update_payment_mode($post_data, $id){
            $trans_db_data = $this->db_operations->get_record('order_payment_mode_trans', ['opmt_om_id' => $id, 'opmt_delete_status' => false]);
            if(!empty($trans_db_data)){
                foreach ($trans_db_data as $key => $value){
                    if(!in_array($value['opmt_id'], $post_data['opmt_id'])){
                        $update_data 						= [];
                        $update_data['opmt_delete_status'] 	= true;
                        $update_data['opmt_updated_by'] 	= $_SESSION['user_id'];
                        $update_data['opmt_updated_at'] 	= date('Y-m-d H:i:s');
                        if($this->db_operations->data_update('order_payment_mode_trans', $update_data, 'opmt_id', $value['opmt_id']) < 1){
                            return ['msg' => '1. Payment mode not deleted.'];
                        }
                    }
                }
                foreach ($post_data['opmt_amt'] as $key => $value) {
                    if($value <= 0){
                        $update_data 						= [];
                        $update_data['opmt_delete_status'] 	= true;
                        $update_data['opmt_updated_by'] 	= $_SESSION['user_id'];
                        $update_data['opmt_updated_at'] 	= date('Y-m-d H:i:s');
                        if($this->db_operations->data_update('order_payment_mode_trans', $update_data, 'opmt_id', $post_data['opmt_id'][$key]) < 1){
                            return ['msg' => '1. Payment mode not deleted.'];
                        }
                    }
                }
            }
            foreach ($post_data['opmt_amt'] as $key => $value){
                if($value > 0){
                    $trans_data							= [];
                    $trans_data['opmt_om_id']			= $id;
                    $trans_data['opmt_om_uuid']			= $post_data['om_uuid'];
                    $trans_data['opmt_payment_mode_id']	= $post_data['opmt_payment_mode_id'][$key];
                    $trans_data['opmt_amt']				= $post_data['opmt_amt'][$key];
                    $trans_data['opmt_delete_status']	= false;
                    $trans_data['opmt_updated_by'] 		= $_SESSION['user_id'];
                    $trans_data['opmt_updated_at'] 		= date('Y-m-d H:i:s');
                    
                    if(empty($post_data['opmt_id'][$key])){
                        $trans_data['opmt_created_by'] 	= $_SESSION['user_id'];
                        $trans_data['opmt_created_at'] 	= date('Y-m-d H:i:s');
                        if($this->db_operations->data_insert('order_payment_mode_trans', $trans_data) < 1){
                            return ['msg' => '1. Payment mode not added.'];
                        }
                    }
                }
            }
            return ['status' => TRUE];
        }
    // payment_mode_trans
          // sku
            public function add_sku_transaction(){
                $post_data  = $this->input->post();
                // echo "<pre>"; print_r($post_data); exit;
                $arr = $arr = ['sku_mtr', 'sku_qty', 'sku_mrp', 'sku_amt'];
                foreach ($arr as $key => $value) {
                    $name = strtoupper(str_replace('_', ' ', $value));
                    if(!isset($post_data[$value]) || (isset($post_data[$value]) && empty($post_data[$value]))){
                        return ['msg' => "1. $name is required."];
                    }else{
                        if($post_data[$value] <= 0) {
                            return ['msg' => "1. Invalid $name."];
                        }
                    }
                }
                $resp = [];
                $this->db->trans_begin();
                    $result = $this->add_sku_fabric_trans($resp);
                    if(!isset($result['status'])){
                        $this->db->trans_rollback();
                        return $result;
                    }
                    // echo "<pre>"; print_r($result); exit;
                    $result = $this->add_sku_apparel_trans($result['data']);
                    if(!isset($result['status'])){
                        $this->db->trans_rollback();
                        return $result;
                    }

                    if ($this->db->trans_status() === FALSE){
                        $this->db->trans_rollback();
                        return ['msg' => '4. Transaction Rollback.'];
                    }
                $this->db->trans_commit();
               
                return ['status' => TRUE, 'data' => $result['data'], 'msg' => 'Order Sku Transaction added successfully.'];
            }
            public function add_sku_fabric_trans(){
                $post_data      = $this->input->post();
                $id             = $post_data['id'];
                $design_data    = isset($post_data['design_data']) ? json_decode($post_data['design_data'], true) : [];
                if(empty($design_data)) return ['msg' => '1. Fabric not define in sku master.'];
                // echo "<pre>"; print_r($design_data); exit;
                
                foreach ($design_data as $key => $value) {
                    foreach ($value['qrcode_data'] as $k => $v) {
                        // trans_data
                            $trans_data                         = [];
                            $trans_data['ot_trans_type']        = $value['ot_trans_type'];
                            $trans_data['ot_om_uuid']           = $post_data['om_uuid'];
                            $trans_data['ot_sku_id']            = $post_data['sku_id'];
                            $trans_data['ot_bm_id']             = $v['ot_bm_id'];
                            $trans_data['ot_sdt_id']            = $value['ot_sdt_id'];
                            $trans_data['ot_fabric_mtr']        = $v['ot_fabric_mtr'];
                            $trans_data['ot_fabric_total_mtr']  = $v['ot_fabric_mtr'];
                            $trans_data['ot_fabric_rate']       = $value['ot_fabric_rate'];
                            $trans_data['ot_description']       = $post_data['sku_description'];
                            $trans_data['ot_delete_status']     = true;
                            $trans_data['ot_created_by']        = $_SESSION['user_id'];
                            $trans_data['ot_updated_by']        = $_SESSION['user_id'];
                            $trans_data['ot_created_at']        = date('Y-m-d H:i:s');
                            $trans_data['ot_updated_at']        = date('Y-m-d H:i:s');
                        // trans_data
    
                        if(empty($post_data['ot_id'])){
                            if($trans_data['ot_fabric_mtr'] > 0){
                                $data = $this->model->get_barcode_data($v['ot_bm_id']);
                                if(empty($data)) return ['msg' => '3. Barcode not found.'];
        
                                if($data[0]['bm_delete_status'] == 1) return ['msg' => '4. Barcode is deleted.'];
                                
                                if($v['ot_fabric_mtr'] > $data[0]['bal_qty']) return ['data' => 1, 'msg' => '3. Barcode not available.'];
        
                                $trans_data['ot_id'] = $this->db_operations->data_insert($this->sub_menu.'_trans', $trans_data);
        
                                if($trans_data['ot_id'] < 1) return ['msg' => '1. Sku Design Transaction not added.'];
                            }
                            $trans_data['isExist'] = false;
                            $trans_data['ot_om_id'] = 0;
                        }else{
                            $trans_data['ot_om_id'] = $id;
                            $trans_data['ot_id']    = $v['ot_id'];
                        }
                        $design_data[$key]['qrcode_data'][$k]['ot_id']     = isset($trans_data['ot_id']) ? $trans_data['ot_id'] : 0;
                        $design_data[$key]['qrcode_data'][$k]['ot_om_id']  = $trans_data['ot_om_id'];
                    }
                }
                return ['status' => TRUE, 'data' => $design_data];
            }
            public function add_sku_apparel_trans($temp){
                $post_data  = $this->input->post();
                $id         = $post_data['id'];
                $data       = [];
                if(empty($post_data['ot_id'])){
                    $apparel_data   = $this->model->get_sku_apparel_data($post_data['sku_id']);
                    if(empty($apparel_data)) return ['msg' => '1. Apparel not define in sku master.'];
                    // echo "<pre>"; print_r($post_data); exit;
                    $ot_id= 0;
                    foreach ($apparel_data as $key => $value) {
                        // trans_data
                            $trans_data                         = [];
                            $trans_data['ot_trans_type']        = 'SKU';
                            $trans_data['ot_ot_id']             = $ot_id;
                            $trans_data['ot_sku_id']            = $post_data['sku_id'];
                            $trans_data['ot_apparel_id']        = $value['apparel_id'];
                            $trans_data['ot_om_uuid']           = $post_data['om_uuid'];
                            $trans_data['ot_apparel_qty']       = empty($key) ? $post_data['sku_qty'] : 0;
                            $trans_data['ot_sku_mtr']           = empty($key) ? $post_data['sku_mtr'] : 0;
                            $trans_data['ot_apparel_mrp']       = empty($key) ? $post_data['sku_mrp'] : 0;
                            $trans_data['ot_apparel_amt']       = empty($key) ? $post_data['sku_amt'] : 0;
                            $trans_data['ot_amt']               = empty($key) ? $post_data['sku_amt'] : 0;
                            $trans_data['ot_disc_per']          = empty($key) ? $post_data['sku_disc_per'] : 0;
                            $trans_data['ot_disc_amt']          = empty($key) ? $post_data['sku_disc_amt'] : 0;
                            $trans_data['ot_taxable_amt']       = empty($key) ? $post_data['sku_taxable_amt'] : 0;
                            $trans_data['ot_sgst_per']          = empty($key) ? $post_data['sku_sgst_per'] : 0;
                            $trans_data['ot_sgst_amt']          = empty($key) ? $post_data['sku_sgst_amt'] : 0;
                            $trans_data['ot_cgst_per']          = empty($key) ? $post_data['sku_cgst_per'] : 0;
                            $trans_data['ot_cgst_amt']          = empty($key) ? $post_data['sku_cgst_amt'] : 0;
                            $trans_data['ot_igst_per']          = empty($key) ? $post_data['sku_igst_per'] : 0;
                            $trans_data['ot_igst_amt']          = empty($key) ? $post_data['sku_igst_amt'] : 0;
                            $trans_data['ot_total_amt']         = empty($key) ? $post_data['sku_total_amt'] : 0;
                            $trans_data['ot_stitching_rate']    = empty($key) ? $value['stitching_rate'] : 0;
                            $trans_data['ot_description']       = $post_data['sku_description'];
                            $trans_data['ot_delete_status']     = true;
                            $trans_data['ot_created_by']        = $_SESSION['user_id'];
                            $trans_data['ot_updated_by']        = $_SESSION['user_id'];
                            $trans_data['ot_created_at']        = date('Y-m-d H:i:s');
                            $trans_data['ot_updated_at']        = date('Y-m-d H:i:s');
                        // trans_data
    
                        if(empty($post_data['ot_id'])){
                            $trans_data['ot_id']    = $this->db_operations->data_insert('order_trans', $trans_data);
    
                            if($trans_data['ot_id'] < 1) return ['msg' => '2. Order Transaction not added.'];
                            $trans_data['isExist']  = false;
                            $trans_data['ot_om_id'] = 0;
                            $ot_id                  = empty($key) ? $trans_data['ot_id'] : $ot_id;
                        }
    
                        $trans_data['sku_name']     = $value['sku_name'];
                        $trans_data['apparel_name'] = $value['apparel_name'];
                        $trans_data['sku_image']    = $value['sku_image'];
                        $trans_data['design_data']  = empty($key) ? $temp : [];
                        $trans_data['apparels']     = empty($key) ? $this->model->get_apparels($trans_data['ot_sku_id']) : '';
    
                        array_push($data, $trans_data);
                    }
                }else{
                    $prev_data = $this->model->get_sku_trans_data($post_data['ot_id']);
                    if(empty($prev_data)) return ['msg' => '1. SKU Transaction not found.'];
                    // echo "<pre>"; print_r($prev_data); exit;
                    foreach ($prev_data as $key => $value) {
                        // trans_data
                            $value['ot_apparel_qty']    = empty($value['ot_ot_id']) ? $post_data['sku_qty'] : 0;
                            $value['ot_sku_mtr']        = empty($value['ot_ot_id']) ? $post_data['sku_mtr'] : 0;
                            $value['ot_apparel_mrp']    = empty($value['ot_ot_id']) ? $post_data['sku_mrp'] : 0;
                            $value['ot_apparel_amt']    = empty($value['ot_ot_id']) ? $post_data['sku_amt'] : 0;
                            $value['ot_amt']            = empty($value['ot_ot_id']) ? $post_data['sku_amt'] : 0;
                            $value['ot_disc_per']       = empty($value['ot_ot_id']) ? $post_data['sku_disc_per'] : 0;
                            $value['ot_disc_amt']       = empty($value['ot_ot_id']) ? $post_data['sku_disc_amt'] : 0;
                            $value['ot_taxable_amt']    = empty($value['ot_ot_id']) ? $post_data['sku_taxable_amt'] : 0;
                            $value['ot_sgst_per']       = empty($value['ot_ot_id']) ? $post_data['sku_sgst_per'] : 0;
                            $value['ot_sgst_amt']       = empty($value['ot_ot_id']) ? $post_data['sku_sgst_amt'] : 0;
                            $value['ot_cgst_per']       = empty($value['ot_ot_id']) ? $post_data['sku_cgst_per'] : 0;
                            $value['ot_cgst_amt']       = empty($value['ot_ot_id']) ? $post_data['sku_cgst_amt'] : 0;
                            $value['ot_igst_per']       = empty($value['ot_ot_id']) ? $post_data['sku_igst_per'] : 0;
                            $value['ot_igst_amt']       = empty($value['ot_ot_id']) ? $post_data['sku_igst_amt'] : 0;
                            $value['ot_total_amt']      = empty($value['ot_ot_id']) ? $post_data['sku_total_amt'] : 0;
                            $value['ot_description']    = $post_data['sku_description'];
                            $value['design_data']       = empty($value['ot_ot_id']) ? $temp : [];
                            $value['apparels']          = empty($value['ot_ot_id']) ? $this->model->get_apparels($value['ot_sku_id']) : '';
                        // trans_data
                        array_push($data, $value);
                    }
                    // echo "<pre>"; print_r($data); exit;
                }
                return ['status' => TRUE, 'data' => $data];
            }
            public function add_update_sku_trans($post_data, $id){
             
                $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_om_id' => $id, 'ot_delete_status' => false, 'ot_trans_type' => 'SKU', 'ot_apparel_id !=' => 0]);
                $ids    = $this->get_id($post_data['sku_trans_data'], 'ot_id');
                if(!empty($trans_db_data)){
                    foreach ($trans_db_data as $key => $value){
                        if(!in_array($value['ot_id'], $ids)){
                            $result = $this->delete_trans(['ot_id' => $value['ot_id'], 'ot_delete_status' => false, 'ot_trans_type' => 'SKU']);
                            if(!isset($result['status'])) return $result;
                        }
                    }
                } 
                 // echo "<pre>"; print_r($post_data);die;
                foreach ($post_data['sku_trans_data'] as $key => $value){ 
                    // trans_data
                        $trans_data                         = [];
                        $trans_data['ot_om_id']             = $id;
                        $trans_data['ot_trans_type']        = $value['ot_trans_type'];
    
                        $trans_data['ot_ot_id']             = $value['ot_ot_id'];
                        $trans_data['ot_sku_id']            = $value['ot_sku_id'];
                        $trans_data['ot_sku_mtr']           = $value['ot_sku_mtr'];
    
                        $trans_data['ot_apparel_id']        = $value['ot_apparel_id'];
                        $trans_data['ot_apparel_qty']       = $value['ot_apparel_qty'];
                        $trans_data['ot_apparel_mrp']       = $value['ot_apparel_mrp'];
                        $trans_data['ot_apparel_amt']       = $value['ot_apparel_amt'];
                        
                        $trans_data['ot_qty']               = $value['ot_apparel_qty'];
                        $trans_data['ot_rate']              = $value['ot_stitching_rate'];
                        $trans_data['ot_mrp']               = $value['ot_apparel_mrp'];
                        $trans_data['ot_amt']               = $value['ot_amt'];

                        $trans_data['ot_fabric_mtr']        = 0;
                        $trans_data['ot_fabric_total_mtr']  = 0;
                        $trans_data['ot_fabric_mrp']        = 0;
                        $trans_data['ot_fabric_rate']       = 0;
                        $trans_data['ot_fabric_amt']        = 0;

                        $trans_data['ot_stitching_rate']    = $value['ot_stitching_rate'];
                        $trans_data['ot_mtr']               = $value['ot_sku_mtr'];
                        $trans_data['ot_total_mtr']         = $value['ot_sku_mtr'];

                        $trans_data['ot_disc_per']          = $value['ot_disc_per'];
                        $trans_data['ot_disc_amt']          = $value['ot_disc_amt'];
                        $trans_data['ot_taxable_amt']       = $value['ot_taxable_amt'];
                        $trans_data['ot_sgst_per']          = $value['ot_sgst_per'];
                        $trans_data['ot_sgst_amt']          = $value['ot_sgst_amt'];
                        $trans_data['ot_cgst_per']          = $value['ot_cgst_per'];
                        $trans_data['ot_cgst_amt']          = $value['ot_cgst_amt'];
                        $trans_data['ot_igst_per']          = $value['ot_igst_per'];
                        $trans_data['ot_igst_amt']          = $value['ot_igst_amt'];
                        $trans_data['ot_total_amt']         = $value['ot_total_amt'];
                        $trans_data['ot_stitching_rate']    = $value['ot_stitching_rate'];
                        $trans_data['ot_description']       = $value['ot_description'];
                        $trans_data['ot_delete_status']     = false;
                        $trans_data['ot_updated_by']        = $_SESSION['user_id'];
                        $trans_data['ot_updated_at']        = date('Y-m-d H:i:s');
                    // trans_data
                    
                    if(!empty($value['ot_id'])){
                        $prev_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_id' => $value['ot_id']]);
                        if(empty($prev_data)) return ['msg' => '2. Transaction not found.'];
    
                        // if(!$this->model->isTransExist($value['ot_id'])) return ['msg' => '1. Not allowed to update transaction.'];
    
                        if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'ot_id', $value['ot_id']) < 1) return ['msg' => '2. Transaction not updated.'];
                        
                        $trans_data['ot_id']            = $value['ot_id'];
                        $trans_data['ot_mrp']           = $value['ot_apparel_mrp'];
                        $trans_data['ot_qty']           = 1;
                        $trans_data['ot_fabric_mtr']    = 0;
                        $trans_data['ot_fabric_rate']   = 0;
                        $trans_data['design_data']      = $value['design_data'];
    
                        $measurement_clause     = ['cmt_ot_id' => $value['ot_id'], 'cmt_delete_status' => true];
                        $style_clause           = ['cst_ot_id' => $value['ot_id'], 'cst_delete_status' => true];
                        $style_image_clause     = ['csit_ot_id'=> $value['ot_id'], 'csit_delete_status'=> true];
    
                        if(empty($prev_data[0]['ot_om_id'])){
                            $result = $this->add_barcode_trans($trans_data);
                            if(!isset($result['status'])) return $result;
                        }else{
                            $measurement_clause  = ['cmt_ot_id' => $value['ot_id']];
                            $style_clause        = ['cst_ot_id' => $value['ot_id']];
                            $style_image_clause  = ['csit_ot_id'=> $value['ot_id']];
                            
                            $result = $this->update_barcode_trans($trans_data);
                            if(!isset($result['status'])) return $result;
                        }
    
                        $result = $this->add_update_sku_fabric_trans($trans_data);
                        if(!isset($result['status'])) return $result;
                        
                        $result = $this->update_customer_measurement_trans(['om_id' => $id, 'customer_id' => $post_data['om_customer_id']], $measurement_clause);
                        if(!isset($result['status'])) return $result;
    
                        $result = $this->update_customer_style_trans(['om_id' => $id, 'customer_id' => $post_data['om_customer_id']], $style_clause);
                        if(!isset($result['status'])) return $result;
                    }
                }
    
                return ['status' => TRUE];
            }

            public function add_update_sku_fabric_trans($temp){
                // echo "<pre>"; print_r($temp); exit;
                foreach ($temp['design_data'] as $key => $value){
                    foreach ($value['qrcode_data'] as $k => $v) {
                        // trans_data
                            $trans_data                         = [];
                            $trans_data['ot_om_id']             = $temp['ot_om_id'];
                            $trans_data['ot_ot_id']             = $temp['ot_id'];
                            $trans_data['ot_trans_type']        = $temp['ot_trans_type'];
        
                            $trans_data['ot_sku_id']            = $temp['ot_sku_id'];
                            $trans_data['ot_sdt_id']            = $value['ot_sdt_id'];
        
                            $trans_data['ot_bm_id']             = $v['ot_bm_id'];
                            $trans_data['ot_fabric_mtr']        = $v['ot_fabric_mtr'];
                            $trans_data['ot_fabric_total_mtr']  = $v['ot_fabric_mtr'];
                            $trans_data['ot_fabric_rate']       = $value['ot_fabric_rate'];
                            $trans_data['ot_description']       = $temp['ot_description'];
                            $trans_data['ot_delete_status']     = false;
                            $trans_data['ot_updated_by']        = $_SESSION['user_id'];
                            $trans_data['ot_updated_at']        = date('Y-m-d H:i:s');
                        // trans_data
                        
                        if(empty($v['ot_id'])){
                            if($v['checked'] == 1){
                                if($v['ot_fabric_mtr'] > 0){
                                    if($this->db_operations->data_insert($this->sub_menu.'_trans', $trans_data) < 1) return ['msg' => '4. Transaction not added.'];
                                    
                                    $result = $this->add_barcode_master(['bm_id' => $trans_data['ot_bm_id'], 'mtr' => $trans_data['ot_fabric_mtr']]);
                                    if(!isset($result['status'])) return $result;            
                                }
                            }    
                        }else{
                            $prev_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_id' => $v['ot_id']]);
                            if(empty($prev_data)) return ['msg' => '3. Transaction not found.'];

                            if($v['checked'] == 1){
                                if($v['ot_fabric_mtr'] > 0){
                                    if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'ot_id', $v['ot_id']) < 1) return ['msg' => '3. Transaction not updated.'];
        
                                    if(!empty($prev_data[0]['ot_om_id'])){
                                        // echo "<pre>"; print_r($prev_data); exit;
                                        $result = $this->delete_barcode_master(['bm_id' => $prev_data[0]['ot_bm_id'], 'mtr' => $prev_data[0]['ot_fabric_mtr']]);
                                        if(!isset($result['status'])) return $result;    
                                    }

                                    $result = $this->add_barcode_master(['bm_id' => $trans_data['ot_bm_id'], 'mtr' => $trans_data['ot_fabric_mtr']]);
                                    if(!isset($result['status'])) return $result;            
                                }else{
                                    $trans_data['ot_delete_status'] = true;
                                    if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'ot_id', $v['ot_id']) < 1) return ['msg' => '3. Transaction not updated.'];
        
                                    if(!empty($prev_data[0]['ot_om_id'])){
                                        // echo "<pre>"; print_r($prev_data); exit;
                                        $result = $this->delete_barcode_master(['bm_id' => $prev_data[0]['ot_bm_id'], 'mtr' => $prev_data[0]['ot_fabric_mtr']]);
                                        if(!isset($result['status'])) return $result;    
                                    }
                                }
                            }else{
                                $trans_data['ot_delete_status'] = true;
                                if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'ot_id', $v['ot_id']) < 1) return ['msg' => '3. Transaction not updated.'];
    
                                if(!empty($prev_data[0]['ot_om_id'])){
                                    // echo "<pre>"; print_r($prev_data); exit;
                                    $result = $this->delete_barcode_master(['bm_id' => $prev_data[0]['ot_bm_id'], 'mtr' => $prev_data[0]['ot_fabric_mtr']]);
                                    if(!isset($result['status'])) return $result;    
                                }
                            }
                        }
                    }
                    // echo "<pre>"; print_r($value);
                }
    
                return ['status' => TRUE];
            }
        // sku

        // order_branch_barcode_trans
        public function add_barcode_trans($trans_data){
            // echo "<pre>";print_r($trans_data);die;
            for ($i = 1; $i <= $trans_data['ot_qty'] ; $i++) { 
                $year                                   = date('y');
                $month                                  = date('m');
                
                $barcode_master                         = [];   
                $barcode_master['obt_barcode_year']     = date('Y');
                $barcode_master['obt_barcode_month']    = $month;
                $barcode_master['obt_counter']          = $this->model->generate_barcode();
                $barcode_master['obt_item_code']        = $year.''.$month.''.$barcode_master['obt_counter'];
                $barcode_master['obt_roll_no']          = $barcode_master['obt_item_code'];
                $barcode_master['obt_om_id']            = $trans_data['ot_om_id'];
                $barcode_master['obt_ot_id']            = $trans_data['ot_id'];
                $barcode_master['obt_sku_id']           = isset($trans_data['ot_sku_id']) ? $trans_data['ot_sku_id'] : 0;
                $barcode_master['obt_apparel_id']       = $trans_data['ot_apparel_id'];
                $barcode_master['obt_qty']              = 1;
                $barcode_master['obt_mtr']              = (isset($trans_data['ot_fabric_mtr']) && !empty($trans_data['ot_fabric_mtr'])) ? ($trans_data['ot_fabric_mtr'] / $trans_data['ot_qty']) : 1;
                $barcode_master['obt_rate']             = isset($trans_data['ot_rate'])?$trans_data['ot_rate']:0;
                $barcode_master['obt_stitching_rate']   = isset($trans_data['ot_rate'])?$trans_data['ot_rate']:$trans_data['ot_stitching_rate'];
                $barcode_master['obt_fabric_rate']      = isset($trans_data['ot_rate'])?$trans_data['ot_rate']:$trans_data['ot_fabric_rate'];
                $barcode_master['obt_mrp']              = isset($trans_data['ot_mrp'])?$trans_data['ot_mrp']:0;
                $barcode_master['obt_amt']              = isset($trans_data['ot_mrp'])?$trans_data['ot_mrp']:$trans_data['ot_amt'];
                $barcode_master['obt_description']      = $trans_data['ot_description'];
                $barcode_master['obt_delete_status']    = false;
                $barcode_master['obt_branch_id']        = $_SESSION['user_branch_id'];
                $barcode_master['obt_fin_year']         = $_SESSION['fin_year'];
                $barcode_master['obt_created_by']       = $_SESSION['user_id'];
                $barcode_master['obt_created_at']       = date('Y-m-d H:i:s');
                $barcode_master['obt_updated_by']       = $_SESSION['user_id'];
                $barcode_master['obt_updated_at']       = date('Y-m-d H:i:s');
                $barcode_master['obt_id']               = $this->db_operations->data_insert($this->sub_menu.'_barcode_trans', $barcode_master);
                if($barcode_master['obt_id'] < 1) return ['msg' => '1. Barcode not added.'];

            }
            return ['status' => TRUE];
        }
        public function update_barcode_trans($trans_data){
            $prev_data = $this->db_operations->get_record($this->sub_menu.'_barcode_trans', ['obt_ot_id' => $trans_data['ot_id'], 'obt_delete_status' => false]);
            // echo "<pre>"; print_r($trans_data);die;
            // echo "<pre>"; print_r($prev_data); exit;
            if(empty($prev_data)) return ['msg' => '4. Barcode not found.'.$trans_data['ot_id']];
            // if(empty($prev_data)) return ['status' => true];

            foreach ($prev_data as $key => $value) {
                $update_data                        = [];   
                $update_data['obt_apparel_id']      = $trans_data['ot_apparel_id'];
                $update_data['obt_description']     = $trans_data['ot_description'];
                $update_data['obt_stitching_rate']  = $trans_data['ot_rate'];
                $update_data['obt_rate']            = $trans_data['ot_rate'];
                $update_data['obt_fabric_rate']     = $trans_data['ot_rate'];
                $update_data['obt_mrp']             = $trans_data['ot_rate'];
                $update_data['obt_amt']             = $trans_data['ot_amt'];
                $update_data['obt_updated_by']      = $_SESSION['user_id'];
                $update_data['obt_updated_at']      = date('Y-m-d H:i:s');
    
                if($this->db_operations->data_update($this->sub_menu.'_barcode_trans', $update_data, 'obt_id', $value['obt_id']) < 1) return ['msg' => '1. Barcode not updated.'];
            }

            return ['status' => TRUE];
        }

         public function delete_barcode_trans($clause){
            $data = $this->db_operations->get_record($this->sub_menu.'_barcode_trans', $clause);
            if(empty($data)) return ['msg' => '5. Barcode not found.'];

            foreach ($data as $key => $value){
                if($this->model->isBarcodeExist($value['obt_id'])) return ['msg' => '1. Not allowed to delete barcode.'];
                $update_data                        = [];
                $update_data['obt_delete_status']   = true; 
                $update_data['obt_updated_by']      = $_SESSION['user_id']; 
                $update_data['obt_updated_at']      = date('Y-m-d H:i:s'); 
                if($this->db_operations->data_update($this->sub_menu.'_barcode_trans', $update_data, 'obt_id', $value['obt_id']) < 1) return ['msg' => '1. Barcode is not deleted.'];
            }
            return ['status' => TRUE];
        }
    // order_branch_barcode_trans
            // barcode_master
        public function add_barcode_master($clause){
            $data = $this->model->get_barcode_data($clause['bm_id']);
            if(empty($data)) return ['msg' => '6. Barcode not found.'];
            if($data[0]['bm_delete_status'] == 1) return ['msg' => '5. Barcode is deleted.'];
            if($clause['mtr'] > $data[0]['bal_qty']) return ['msg' => '4. Barcode not available.'];
            $update_data['bm_ot_mtr'] = $data[0]['bm_ot_mtr'] + $clause['mtr'];
            if($this->db_operations->data_update('barcode_master', $update_data, 'bm_id', $clause['bm_id']) < 1) return ['msg' => '2. Barcode not updated.'];
            return ['status' => TRUE];  
        }
        public function delete_barcode_master($clause){  
            $data = $this->model->get_barcode_data($clause['bm_id']);
            if(empty($data)) return ['msg' => '7. Barcode not found.'];
            if($clause['mtr'] > $data[0]['bm_ot_mtr']) return ['msg' => '5. Barcode not available.'];
            $update_data['bm_ot_mtr'] = $data[0]['bm_ot_mtr'] - $clause['mtr'];
            if($this->db_operations->data_update('barcode_master', $update_data, 'bm_id', $clause['bm_id']) < 1) return ['msg' => '3. Barcode not updated.'];   
            return ['status' => TRUE];  
        }
    // barcode_master

        public function add_update_customer_measurement_trans($post_data){
            $ot_ids = $this->model->get_ot_id($post_data['_ot_id']);
            // echo "<pre>"; print_r($ot_ids); exit;

            $trans_db_data = $this->model->get_customer_measurement_data($ot_ids);
            // echo "<pre>"; print_r($post_data);
            // echo "<pre>"; print_r($trans_db_data); exit;
            if(!empty($trans_db_data)){
                foreach ($trans_db_data as $key => $value){
                    if(!in_array($value['cmt_id'], $post_data['cmt_id'])){
                        // echo "<pre>"; print_r($value);
                        // echo "<pre>"; print_r($post_data['cmt_id']); exit;
                        $result = $this->delete_customer_measurement_trans(['cmt_id' => $value['cmt_id']]);
                        if(!isset($result['status'])) return $result;
                    }
                }
                foreach ($post_data['cmt_value1'] as $key => $value) {
                    if(empty($value)){
                        if($this->db_operations->delete_record('customer_measurement_trans', ['cmt_id' => $post_data['cmt_id'][$key]]) < 1) return ['msg' => '1. Customer measurement not deleted.'];
                    }
                }
            }
            // echo "<pre>"; print_r($post_data);die;
            foreach ($post_data['cmt_id'] as $unique_id => $cmt_id){ 
                if(!empty($post_data['cmt_value1'][$unique_id])){
                    $explode        = explode('_', $unique_id);
                    $ot_id          = $explode[0];
                    $apparel_id     = $explode[1];
                    $measurement_id = $explode[2];

                    $trans_data                         = [];
                    $trans_data['cmt_om_id']            = $post_data['id'];
                    $trans_data['cmt_om_uuid']          = $post_data['om_uuid'];
                    $trans_data['cmt_ot_id']            = $ot_id;
                    $trans_data['cmt_bill_no']          = $post_data['om_entry_no'];
                    $trans_data['cmt_bill_date']        = $post_data['om_entry_date'];
                    $trans_data['cmt_customer_id']      = $post_data['om_customer_id'];
                    $trans_data['cmt_apparel_id']       = $apparel_id;
                    $trans_data['cmt_measurement_id']   = $measurement_id;
                    $trans_data['cmt_value1']           = $post_data['cmt_value1'][$unique_id];
                    // $trans_data['cmt_value2']           = $post_data['cmt_value2'][$unique_id];
                    $trans_data['cmt_remark']           = $post_data['cmt_remark'][$apparel_id];
                    $trans_data['cmt_updated_by']       = $_SESSION['user_id'];
                    $trans_data['cmt_updated_at']       = date('Y-m-d H:i:s');
                    
                    if(empty($cmt_id)){
                        $trans_data['cmt_delete_status']    = true;
                        $trans_data['cmt_created_by']       = $_SESSION['user_id'];
                        $trans_data['cmt_created_at']       = date('Y-m-d H:i:s');
                        if($this->db_operations->data_insert('customer_measurement_trans', $trans_data) < 1) return ['msg' => '1. Measurement not added.'];
                    }else{
                        if($this->db_operations->data_update('customer_measurement_trans', $trans_data, 'cmt_id', $cmt_id) < 1) return ['msg' => '1. Measurement not updated.'];
                    }
                }
            }
            return ['status' => TRUE];
        }

        public function update_customer_measurement_trans($temp, $clause){
            $data = $this->db_operations->get_record('customer_measurement_trans', $clause);
            if(empty($data)) return ['status' => TRUE];
            foreach ($data as $key => $value){
                $update_data                        = [];
                $update_data['cmt_om_id']          = $temp['om_id'];
                $update_data['cmt_customer_id']     = $temp['customer_id'];
                $update_data['cmt_delete_status']   = false;
                $update_data['cmt_updated_by']      = $_SESSION['user_id'];
                $update_data['cmt_updated_at']      = date('Y-m-d H:i:s');
                if($this->db_operations->data_update('customer_measurement_trans', $update_data, 'cmt_id', $value['cmt_id']) < 1) return ['msg' => '1. Customer measurement not updated.'];
            }
            return ['status' => TRUE];
        }

        public function delete_customer_measurement_trans($clause){
            $data = $this->db_operations->get_record('customer_measurement_trans', $clause);
            if(empty($data)) return ['status' => TRUE];

            foreach ($data as $key => $value){
                $update_data                        = [];
                $update_data['cmt_delete_status']   = true;
                $update_data['cmt_updated_by']      = $_SESSION['user_id'];
                $update_data['cmt_updated_at']      = date('Y-m-d H:i:s');
                if($this->db_operations->data_update('customer_measurement_trans', $update_data, 'cmt_id', $value['cmt_id']) < 1) return ['msg' => '1. Customer measurement not deleted.'];
            }
            return ['status' => TRUE];
        }

        // customer_style_trans
        public function add_update_customer_style_trans($post_data){
            $ot_ids = $this->model->get_ot_id($post_data['_ot_id']);
            // echo "<pre>"; print_r($ot_ids); exit;

            $trans_db_data = $this->model->get_customer_style_data($ot_ids);
            // echo "<pre>"; print_r($post_data['cst_value']);
            // echo "<pre>"; print_r($trans_db_data); exit;
            
            if(!empty($trans_db_data)){
                foreach ($trans_db_data as $key => $value){
                    $unique_id  = $value['cst_ot_id'].'_'.$value['cst_apparel_id'].'_'.$value['cst_style_id'];
                    if(!isset($post_data['cst_value'][$unique_id])){
                        // echo "<pre>"; print_r($value);exit;
                        $result = $this->delete_customer_style_trans(['cst_id' => $value['cst_id']]);
                        if(!isset($result['status'])) return $result;
                    }
                }
            }
            
            if(isset($post_data['cst_value']) && !empty($post_data['cst_value'])){
                foreach ($post_data['cst_value'] as $unique_id => $value){
                    $explode    = explode('_', $unique_id);
                    $ot_id      = $explode[0];
                    $apparel_id = $explode[1];
                    $style_id   = $explode[2];
                    $trans_data                         = [];
                    $trans_data['cst_om_id']            = $post_data['id'];
                    $trans_data['cst_om_uuid']          = $post_data['om_uuid'];
                    $trans_data['cst_ot_id']            = $ot_id;
                    $trans_data['cst_bill_no']          = $post_data['om_memo_no'];
                    $trans_data['cst_bill_date']        = $post_data['om_entry_date'];
                    $trans_data['cst_customer_id']      = $post_data['om_customer_id'];
                    $trans_data['cst_apparel_id']       = $apparel_id;
                    $trans_data['cst_style_id']         = $style_id;
                    $trans_data['cst_value']            = isset($post_data['cst_value'][$unique_id]);
                    $trans_data['cst_updated_by']       = $_SESSION['user_id'];
                    $trans_data['cst_updated_at']       = date('Y-m-d H:i:s');
                    
                    $prev_data = $this->db_operations->get_record('customer_style_trans', ['cst_ot_id' => $trans_data['cst_ot_id'], 'cst_style_id' => $trans_data['cst_style_id']]);
                    // echo "<pre>"; print_r($prev_data);
                    // echo "<pre>"; print_r($trans_data); exit;
                    if(empty($prev_data)){
                        $trans_data['cst_delete_status']    = true;
                        $trans_data['cst_created_by']       = $_SESSION['user_id'];
                        $trans_data['cst_created_at']       = date('Y-m-d H:i:s');
                        if($this->db_operations->data_insert('customer_style_trans', $trans_data) < 1) return ['msg' => '1. Style not added.'];
                    }else{
                        if($this->db_operations->data_update('customer_style_trans', $trans_data, 'cst_id', $prev_data[0]['cst_id']) < 1) return ['msg' => '1. Style not updated.'];
                    }
                }
            }
            return ['status' => TRUE];
        }
        public function update_customer_style_trans($temp, $clause){
            $data = $this->db_operations->get_record('customer_style_trans', $clause);
            if(empty($data)) return ['status' => TRUE];

            foreach ($data as $key => $value){
                $update_data                        = [];
                $update_data['cst_om_id']           = $temp['om_id'];
                $update_data['cst_customer_id']     = $temp['customer_id'];
                $update_data['cst_delete_status']   = false;
                $update_data['cst_updated_by']      = $_SESSION['user_id'];
                $update_data['cst_updated_at']      = date('Y-m-d H:i:s');
                if($this->db_operations->data_update('customer_style_trans', $update_data, 'cst_id', $value['cst_id']) < 1) return ['msg' => '1. Customer style not updated.'];
            }
            return ['status' => TRUE];
        }
        public function delete_customer_style_trans($clause){
            $data = $this->db_operations->get_record('customer_style_trans', $clause);
            if(empty($data)) return ['status' => TRUE];
            // echo "<pre>"; print_r($data); exit;

            foreach ($data as $key => $value){
                // echo "<pre>"; print_r($value); exit;
                $update_data                        = [];
                $update_data['cst_value']           = 0;
                $update_data['cst_delete_status']   = true;
                $update_data['cst_updated_by']      = $_SESSION['user_id'];
                $update_data['cst_updated_at']      = date('Y-m-d H:i:s');
                if($this->db_operations->data_update('customer_style_trans', $update_data, 'cst_id', $value['cst_id']) < 1) return ['msg' => '2. Customer style not deleted.'];                        
            }
            return ['status' => TRUE];
        }

        // customer_style_trans

        // customer_style_image_trans
        public function add_update_customer_style_image($post_data){ 
            $ot_ids = $this->model->get_ot_id($post_data['_ot_id']);
            $trans_db_data = $this->model->get_customer_style_image_data($ot_ids);
            if(!empty($trans_db_data)){
                foreach ($trans_db_data as $key => $value){
                    $unique_id = $value['csit_ot_id'].'_'.$value['csit_apparel_id'].'_'.$value['csit_spt_id'];
                    if(isset($post_data['csit_id'][$unique_id]) && ($post_data['csit_id'][$unique_id] != $value['csit_ast_id'])){
                        $result = $this->delete_customer_style_image_trans(['csit_id' => $value['csit_id']]);
                        if(!isset($result['status'])) return $result;
                    }
                }
            }
            // echo "<pre>"; print_r($post_data); exit;
            if(isset($post_data['csit_id']) && !empty($post_data['csit_id'])){
                foreach ($post_data['csit_id'] as $unique_id => $ast_id){
                    $explode    = explode('_', $unique_id);
                    $ot_id     = $explode[0];
                    $apparel_id = $explode[1];
                    $spt_id     = $explode[2];
                    
                    $trans_data                         = [];
                    $trans_data['csit_obm_id']          = $post_data['id'];
                    $trans_data['csit_obm_uuid']        = $post_data['om_uuid'];
                    $trans_data['csit_ot_id']          = $ot_id;
                    $trans_data['csit_bill_no']         = $post_data['om_entry_no'];
                    $trans_data['csit_bill_date']       = $post_data['om_entry_date'];
                    $trans_data['csit_customer_id']     = $post_data['om_customer_id'];
                    $trans_data['csit_apparel_id']      = $apparel_id;
                    $trans_data['csit_spt_id']          = $spt_id;
                    $trans_data['csit_ast_id']          = $ast_id;
                    $trans_data['csit_delete_status']   = true;
                    $trans_data['csit_updated_by']      = $_SESSION['user_id'];
                    $trans_data['csit_updated_at']      = date('Y-m-d H:i:s');
                    
                    
                    $prev_data = $this->db_operations->get_record('customer_style_image_trans', ['csit_ot_id' => $trans_data['csit_ot_id'], 'csit_ast_id' => $trans_data['csit_ast_id']]);
                    // echo "<pre>"; print_r($prev_data);
                    if(empty($prev_data)){
                        $trans_data['csit_created_by']  = $_SESSION['user_id'];
                        $trans_data['csit_created_at']  = date('Y-m-d H:i:s');
                        if($this->db_operations->data_insert('customer_style_image_trans', $trans_data) < 1) return ['msg' => '1. Style image not added.'];
                    }else{
                        if($this->db_operations->data_update('customer_style_image_trans', $trans_data, 'csit_id', $prev_data[0]['csit_id']) < 1) return ['msg' => '1. Style image not updated.'];
                    }
                }
            }
            return ['status' => TRUE];
        }
        public function update_customer_style_image_trans($temp, $clause){
            $data = $this->db_operations->get_record('customer_style_image_trans', $clause);
            if(empty($data)) return ['status' => TRUE];

            foreach ($data as $key => $value){
                $update_data                        = [];
                $update_data['csit_om_id']         = $temp['om_id'];
                $update_data['csit_customer_id']    = $temp['customer_id'];
                $update_data['csit_delete_status']  = false;
                $update_data['csit_updated_by']     = $_SESSION['user_id'];
                $update_data['csit_updated_at']     = date('Y-m-d H:i:s');
                if($this->db_operations->data_update('customer_style_image_trans', $update_data, 'csit_id', $value['csit_id']) < 1) return ['msg' => '1. Customer style image not updated.'];
            }
            return ['status' => TRUE];
        }
        public function delete_customer_style_image_trans($clause){
            $data = $this->db_operations->get_record('customer_style_image_trans', $clause);
            if(empty($data)) return ['status' => TRUE];
            foreach ($data as $key => $value){
                if($this->db_operations->delete_record('customer_style_image_trans', ['csit_id' => $value['csit_id']]) < 1) return ['msg' => '4. Style image not deleted.'];
            }
            return ['status' => TRUE];
        }
    // customer_style_image_trans

        public function delete_trans($clause){ 
            $data = $this->db_operations->get_record($this->sub_menu.'_trans', $clause);
            // echo "<pre>"; print_r($data); exit;
            if(empty($data)) return ['status' => TRUE];

            foreach ($data as $key => $value){ 
                
                $child_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_ot_id' => $value['ot_id'], 'ot_delete_status' => false]);
                if(empty($child_data)){

                    if($this->model->isTransExist($value['ot_id'])) return ['msg' => '1. Not allowed to delete transaction.'];
                    if(!empty($value['ot_bm_id'])){
                        $result = $this->delete_barcode_master(['bm_id' => $value['ot_bm_id'], 'mtr' => $value['ot_fabric_total_mtr']]);
                        if(!isset($result['status'])) return $result;
                    }else if(!empty($value['ot_brmm_id'])){
                        $result = $this->update_readymade_barcode_delete($value);
                        if(!isset($result['status'])) return $result;
                    }else{
                        $result = $this->delete_barcode_trans(['obt_ot_id' => $value['ot_id'], 'obt_delete_status' => false]);
                        if(!isset($result['status'])) return $result;
                    }

                    $result = $this->delete_customer_measurement_trans(['cmt_ot_id' => $value['ot_id'], 'cmt_delete_status' => false]);
                    if(!isset($result['status'])) return $result;

                    $result = $this->delete_customer_style_trans(['cst_ot_id' => $value['ot_id'], 'cst_delete_status' => false]);
                    if(!isset($result['status'])) return $result;

                    $result = $this->delete_customer_style_image_trans(['csit_ot_id' => $value['ot_id'], 'csit_delete_status' => false]);
                    if(!isset($result['status'])) return $result;
                
                }else{
                    foreach ($child_data as $k => $v) {
                        $result = $this->delete_trans(['ot_id' => $v['ot_id'], 'ot_delete_status' => false]);
                        if(!isset($result['status'])) return $result;
                    }
                }
                   
                $update_data                        = [];
                $update_data['ot_delete_status']    = true; 
                $update_data['ot_updated_by']       = $_SESSION['user_id']; 
                $update_data['ot_updated_at']       = date('Y-m-d H:i:s'); 
                if($this->db_operations->data_update($this->sub_menu.'_trans', $update_data, 'ot_id', $value['ot_id']) < 1) return ['msg' => '2. Transaction not deleted.'];
            }
            return ['status' => TRUE];
        }

        public function get_body_measurement_data(){ 
            $this->load->model('master/Customer_model', 'customer_model');  
            $post_data  = $this->input->post();
            $id         = $post_data['id'];

            $query="SELECT apparel.apparel_id 
                FROM measurement_setting_master measurement_setting
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = measurement_setting.measurement_setting_apparel_id)
                INNER JOIN measurement_master measurement ON(measurement.measurement_id = measurement_setting.measurement_setting_measurement_id)
                WHERE measurement_setting.measurement_setting_status = 1 GROUP BY apparel.apparel_id";
            $app_data = $this->db->query($query)->result_array();
            if(empty($app_data)) return['status' => FALSE, 'data' => $data, 'msg' => 'No Measurement Available!!']; 
            $record=[];
            foreach ($app_data as $key => $value) {
                $data = $this->customer_model->get_body_measurement_setting($id,$value['apparel_id']);
                if(empty($data)) return['status' => FALSE, 'data' => $data, 'msg' => 'No Record fetched!!'];
                array_push($record, $data);
            }

            // echo "<pre>"; print_r($record);die;
            return['status' => TRUE, 'data' => $record, 'msg' => 'Record fetched successfully.'];  
           
        }

        public function update_body_measurement(){ 
            $post_data  = $this->input->post();
            // echo "<pre>"; print_r($post_data); exit;
            if(empty($post_data)){
                 return ['msg' => '1. Form Data is empty.'];
            }
            $id = $post_data['id'];
            $this->db->trans_begin();
            $bill_no    = $post_data['om_uuid'];
            $bill_date  = $post_data['om_entry_date'];
            foreach ($post_data['cmt_id'] as $key => $cmt_id){
                $trans_data                         = [];
                $trans_data['cmt_value1']           = $post_data['cmt_value1'][$key];
                // $trans_data['cmt_remark']           = $post_data['cmt_remark'];
                $trans_data['cmt_updated_by']       = $_SESSION['user_id'];
                $trans_data['cmt_updated_at']       = date('Y-m-d H:i:s');
                
                if(empty($cmt_id)){
                    $trans_data['cmt_bill_no']          = $bill_no;
                    $trans_data['cmt_bill_date']        = $bill_date;
                    $trans_data['cmt_customer_id']      = $id;
                    $trans_data['cmt_customer_uuid']    = $bill_no;
                    $trans_data['cmt_apparel_id']       = $post_data['cmt_apparel_id'][$key];
                    $trans_data['cmt_measurement_id']   = $post_data['cmt_measurement_id'][$key];
                    $trans_data['cmt_created_by']       = $_SESSION['user_id'];
                    $trans_data['cmt_created_at']       = date('Y-m-d H:i:s');
                    if(!empty($trans_data['cmt_value1'])){
                        if($this->db_operations->data_insert('customer_measurement_trans', $trans_data) < 1) {
                            $this->db->trans_rollback();
                            return ['msg' => '1. Measurement not added.'];
                        }
                    }
                   
                }else{
                    if($this->db_operations->data_update('customer_measurement_trans', $trans_data, 'cmt_id', $cmt_id) < 1){
                        $this->db->trans_rollback();
                        return ['msg' => '1. Measurement not updated.'];
                    }
                }
            }   
            
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return ['msg' => '1. Transaction Rollback.'];
            }
            $this->db->trans_commit();

            return ['status' => TRUE, 'msg' => 'Measurement added successfully.'];
        }

}
?>