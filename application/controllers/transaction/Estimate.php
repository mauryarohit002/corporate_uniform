<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class estimate extends my_controller{
    protected $menu;
    protected $sub_menu;
    protected $sub_menu1;
	public function __construct(){
        $this->menu     = 'transaction'; 
        $this->sub_menu = 'estimate'; 
        $this->sub_menu1 = 'order'; 

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

            // $result = $this->delete_trans(['ot_om_id' => $id, 'ot_delete_status' => false]);
            $result = $this->delete_trans(['ot_om_id' => $id, 'ot_delete_status' => false, 'ot_ot_id' => 0]);
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
    
    public function delete_master($clause){
            $data = $this->db_operations->get_record($this->sub_menu1.'_master', $clause);
            if(empty($data)) return ['msg' => '3. Order not found.'];

            foreach ($data as $key => $value){
                if($this->model->isExist($value['om_id'])) return ['msg' => '1. Not allowed to delete.'];
                
                $prev_data = $this->db_operations->get_record('order_master', ['om_id' => $value['om_id']]);
                if(!empty($prev_data)){
                    if($this->db_operations->delete_record('order_master', ['om_id' => $value['om_id']]) < 1) return ['msg' => '1. Entry no. not deleted.'];
                }

                $update_data                        = [];
                $update_data['om_em_entry_no']      = $data[0]['om_em_entry_no'].''.$value['om_id']; 
                $update_data['om_delete_status']    = true; 
                $update_data['om_updated_by']       = $_SESSION['user_id']; 
                $update_data['om_updated_at']       = date('Y-m-d H:i:s');
                if($this->db_operations->data_update($this->sub_menu1.'_master', $update_data, 'om_id', $value['om_id']) < 1) return ['msg' => '1. Order not deleted.'];
            }
            return ['status' => TRUE];
        }
    // order_master
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
            if(empty($measurement_data) && empty($style_data)) return['msg' => '2. Measurement not found.'];
            array_push($data, ['apparel_data' => $value, 'measurement_data' => $measurement_data, 'style_data' => $style_data]);
        }

        return['status' => TRUE, 'data' => $data, 'msg' => 'Measurement fetched successfully.'];
    }
    
    public function measurement_print($om_id, $ot_id = 0){
        $record = $this->model->get_data_for_measurement_print($om_id, $ot_id);
        // echo "<pre>"; print_r($record); exit;
        $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/pdf/measurement', $record);
    }

    
    // measurement
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

           
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return ['msg' => '2. Transaction Rollback.'];
            }
        $this->db->trans_commit();
        return ['status' => TRUE, 'msg' => 'Measurement added successfully.'];
    }

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
                $trans_data['cmt_bill_no']          = $post_data['om_em_entry_no'];
                $trans_data['cmt_bill_date']        = $post_data['om_em_entry_date'];
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

    public function add_update_customer_style_trans($post_data){
        $ot_ids = $this->model->get_ot_id($post_data['_ot_id']);
        $trans_db_data = $this->model->get_customer_style_data($ot_ids);

        if(!empty($trans_db_data)){
            foreach ($trans_db_data as $key => $value){
                $unique_id  = $value['cst_ot_id'].'_'.$value['cst_apparel_id'].'_'.$value['cst_style_id'];
                if(!isset($post_data['cst_value'][$unique_id])){
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
                $trans_data['cst_bill_no']          = $post_data['om_em_entry_no'];
                $trans_data['cst_bill_date']        = $post_data['om_em_entry_date'];
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

    // measurement
    
    // order_master
        public function add_edit(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            if(!isset($post_data['trans_data']) || (isset($post_data['trans_data']) && empty($post_data['trans_data']))) return ['msg' => '1. Item not aded in list.'];
            $post_data['trans_data'] = json_decode($post_data['trans_data'], true);
            // echo "<pre>"; print_r($post_data); exit;
            // master_data
                $master_data['om_uuid']                     = trim($post_data['om_uuid']);
                $master_data['om_em_entry_no']              = trim($post_data['om_em_entry_no']);
                $master_data['om_em_entry_date']            = date('Y-m-d', strtotime($post_data['om_em_entry_date']));
                $master_data['om_status']                   = false;
                $master_data['om_trial_date']               = $post_data['om_trial_date'];
                $master_data['om_delivery_date']            = $post_data['om_delivery_date'];
                // $master_data['om_mojdi']                     = trim($post_data['om_mojdi']);
                // $master_data['om_mojdi_size']                = trim($post_data['om_mojdi_size']);
                $master_data['om_billing_id']               = isset($post_data['om_billing_id'])?trim($post_data['om_billing_id']):0;
                $master_data['om_customer_id']              = isset($post_data['om_customer_id'])?trim($post_data['om_customer_id']):0;
                $master_data['om_salesman_id']              = isset($post_data['om_salesman_id'])?trim($post_data['om_salesman_id']):0;
                $master_data['om_master_id']              = isset($post_data['om_master_id'])?trim($post_data['om_master_id']):0;
                $master_data['om_gst_type']                 = trim($post_data['om_gst_type']);
                $master_data['om_bill_type']                = isset($post_data['om_bill_type']);
                $master_data['om_notes']                    = trim($post_data['om_notes']);
                $master_data['om_total_qty']                = trim($post_data['om_total_qty']);
                $master_data['om_total_mtr']                = trim($post_data['om_total_mtr']);
                $master_data['om_sub_amt']                  = trim($post_data['om_sub_amt']);
                $master_data['om_disc_amt']                 = trim($post_data['om_disc_amt']);
                $master_data['om_taxable_amt']              = trim($post_data['om_taxable_amt']);
                $master_data['om_sgst_amt']                 = trim($post_data['om_sgst_amt']);
                $master_data['om_cgst_amt']                 = trim($post_data['om_cgst_amt']);
                $master_data['om_igst_amt']                 = trim($post_data['om_igst_amt']);
                $master_data['om_bill_disc_per']            = trim($post_data['om_bill_disc_per']);
                $master_data['om_bill_disc_amt']            = trim($post_data['om_bill_disc_amt']);
                $master_data['om_round_off']                = trim($post_data['om_round_off']);
                $master_data['om_total_amt']                = trim($post_data['om_total_amt']);
                $master_data['om_advance_amt']              = trim($post_data['om_advance_amt']);
                $master_data['om_balance_amt']              = trim($post_data['om_balance_amt']);
                $master_data['om_updated_by']               = $_SESSION['user_id'];
            // master_data
                $cnt =$this->db_operations->get_cnt('order_master',['om_id!='=>$id,'om_em_entry_no'=>$master_data['om_em_entry_no'],'om_fin_year'=>$_SESSION['fin_year'],'om_delete_status'=>0]);
                if($cnt>0){
                     return ['msg' => 'Duplicate Orde No found!!'];
                }
            $this->db->trans_begin();
            if($id == 0){ 
                // $master_data['om_em_entry_no']     = $this->model->get_max_entry_no(['entry_no' => 'om_em_entry_no', 'delete_status' => 'om_delete_status', 'fin_year' => 'om_fin_year','table'=>'order_master']);
                $master_data['om_created_by']   = $_SESSION['user_id'];
                $master_data['om_created_at']   = date('Y-m-d H:i:s');
                $master_data['om_fin_year']     = $_SESSION['fin_year'];
                $master_data['om_branch_id']    = $_SESSION['user_branch_id'];
                $uuidExist                      = $this->db_operations->get_cnt('order_master', ['om_uuid' => $master_data['om_uuid']]);
                if($uuidExist > 0){
                    $this->db->trans_rollback();
                    return ['msg' => 'Form already submited.'];
                }
                // echo "<pre>"; print_r($master_data);die;
                $id = $this->db_operations->data_insert('order_master', $master_data);
                $msg = 'Estimate added successfully.';
                if($id < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Estimate not added.'];
                }
            }else{
                $prev_data = $this->db_operations->get_record('order_master', ['om_id' => $id,'om_status'=>false, 'om_delete_status' => false]);
                if(empty($prev_data)){
                    $this->db->trans_rollback();
                    return ['status' => REFRESH, 'msg' => '1. Estimate not found.'];
                }
                $msg = 'Estimate updated successfully.';
                if($this->db_operations->data_update('order_master', $master_data, 'om_id', $id) < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Estimate not updated.'];
                }
            }

            $result = $this->add_update_trans($post_data, $id);
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

            $data['id']     = encrypt_decrypt("encrypt", $id, SECRET_KEY);
            $data['name']   = strtoupper($master_data['om_em_entry_no']);
            return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
        }
    // order_master
    
    // order_trans
        public function add_transaction(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            $data       = ($post_data['trans_type'] == "READYMADE")?(isset($post_data['brmm_id'])?$this->model->get_readymade_barcode_data($post_data['brmm_id']) : 0) :(isset($post_data['bm_id'])?$this->model->get_barcode_data($post_data['bm_id']) : 0);
            if(!empty($post_data['ot_id'])){
                $old_data = $this->db_operations->get_record('order_trans',['ot_id'=>$post_data['ot_id'],'ot_delete_status' => false]);
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
            $trans_data                     = [];
            $trans_data['ot_om_uuid']      = trim($post_data['om_uuid']);
            $trans_data['ot_trans_type']    = isset($post_data['trans_type']) ? $post_data['trans_type'] : 0;
            $trans_data['ot_apparel_id']    = isset($post_data['apparel_id']) ? $post_data['apparel_id'] : 0;
            $trans_data['ot_bm_id']         = isset($post_data['bm_id']) ? $post_data['bm_id'] : 0;
            $trans_data['ot_brmm_id']       = isset($post_data['brmm_id']) ? $post_data['brmm_id'] : 0;
            $trans_data['ot_qty']           = trim($post_data['qty']);
            $trans_data['ot_mtr']           = trim($post_data['mtr']);
            $trans_data['ot_total_mtr']     = trim($post_data['total_mtr']);
            $trans_data['ot_rate']          = trim($post_data['rate']);
            $trans_data['ot_amt']           = trim($post_data['amt']);
            $trans_data['ot_disc_per']      = trim($post_data['disc_per']);
            $trans_data['ot_disc_amt']      = trim($post_data['disc_amt']);
            $trans_data['ot_taxable_amt']   = trim($post_data['taxable_amt']);
            $trans_data['ot_sgst_per']      = trim($post_data['sgst_per']);
            $trans_data['ot_sgst_amt']      = trim($post_data['sgst_amt']);
            $trans_data['ot_cgst_per']      = trim($post_data['cgst_per']);
            $trans_data['ot_cgst_amt']      = trim($post_data['cgst_amt']);
            $trans_data['ot_igst_per']      = trim($post_data['igst_per']);
            $trans_data['ot_igst_amt']      = trim($post_data['igst_amt']);
            $trans_data['ot_total_amt']     = trim($post_data['total_amt']);
            $trans_data['ot_description']   = trim($post_data['description']);
            $trans_data['ot_created_by']    = $_SESSION['user_id'];
            $trans_data['ot_updated_by']    = $_SESSION['user_id'];
            $trans_data['ot_created_at']    = date('Y-m-d H:i:s');
            $trans_data['ot_updated_at']    = date('Y-m-d H:i:s');

            if(empty($post_data['ot_id'])){
                $trans_data['ot_id'] = $this->db_operations->data_insert('order_trans', $trans_data);
                if($trans_data['ot_id'] < 1) return ['msg' => '1. Estimate Transaction not added.'];
                $trans_data['isExist'] = false;
                $trans_data['ot_om_id'] = 0;
            }else{
                $trans_data['ot_om_id'] = $id;
                $trans_data['ot_id']    = $post_data['ot_id'];
            }
            $trans_data['apparel_name'] = $this->model->get_name('apparel', $trans_data['ot_apparel_id']);
            $trans_data['item_code']    = ($post_data['trans_type'] == "READYMADE")?$this->model->get_readymade_item_code($trans_data['ot_brmm_id']) : $this->model->get_item_code($trans_data['ot_bm_id']);
            $trans_data['apparel_data'] = $this->add_apparel_transaction($trans_data);
            // echo "<pre>"; print_r($trans_data);die;
            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Estimate Transaction added successfully.'];
        }

        public function add_apparel_transaction($temp){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            // echo "<pre>"; print_r($post_data); exit;

            if($post_data['trans_type'] == 'FABRIC') return [];
            if($post_data['trans_type'] == 'READYMADE') return [];
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
             $trans_db_data = $this->db_operations->get_record($this->sub_menu1.'_trans', ['ot_om_id' => $id, 'ot_delete_status' => false,'ot_ot_id' => 0]);
            $ids  = $this->get_id($post_data['trans_data'], 'ot_id');
            if(!empty($trans_db_data)){
                foreach ($trans_db_data as $key => $value){
                    if(!in_array($value['ot_id'], $ids)){ 
                        $result = $this->delete_trans(['ot_id' => $value['ot_id'], 'ot_delete_status' => false]);
                        if(!isset($result['status'])) return $result;
                       
                    } 
                }
            }
            foreach ($post_data['trans_data'] as $key => $value){
                $trans_data                         = [];
                $trans_data['ot_om_id']             = $id;
                $trans_data['ot_apparel_id']        = $value['ot_apparel_id'];
                $trans_data['ot_bm_id']             = $value['ot_bm_id'];
                $trans_data['ot_brmm_id']           = $value['ot_brmm_id'];
                $trans_data['ot_qty']               = $value['ot_qty'];
                $trans_data['ot_mtr']               = $value['ot_mtr'];
                $trans_data['ot_total_mtr']         = $value['ot_total_mtr'];
                $trans_data['ot_rate']              = $value['ot_rate'];
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
                
                if($value['ot_id'] != 0){
                    $prev_data = $this->db_operations->get_record('order_trans', ['ot_id' => $value['ot_id']]);
                    if(empty($prev_data)) return ['msg' => '5. Transaction not found.'];
                    if(!$this->model->isTransExist($value['ot_id'])){
                        if($this->db_operations->data_update('order_trans', $trans_data, 'ot_id', $value['ot_id']) < 1){
                            return ['msg' => 'Transaction not updated.'];
                        }
                        $trans_data['ot_id'] = $value['ot_id'];
                        // echo "<pre>"; print_r($trans_data);die;
                        if(empty($prev_data[0]['ot_om_id'])){ 
                            // if(!empty($trans_data['ot_apparel_id'])){
                            //     $result = $this->add_barcode($id, $value['ot_id'], $trans_data);
                            //     if(!isset($result['status'])) return $result;
                            // }
                        }else{ 
                            // if(!empty($trans_data['ot_apparel_id'])){
                            //     $result = $this->update_barcode($id, $value['ot_id'], $trans_data);
                            //     if(!isset($result['status'])) return $result;
                            // }
                            if(!empty($trans_data['ot_bm_id'])){
                                $result = $this->update_barcode_delete($prev_data[0]);
                                if(!isset($result['status'])) return $result;
                            }
                            if(!empty($trans_data['ot_brmm_id'])){
                                $result = $this->update_readymade_barcode_delete($prev_data[0]);
                                if(!isset($result['status'])) return $result;
                            }
                        }

                        if(!empty($trans_data['ot_bm_id'])){ 
                            $result = $this->update_barcode_add($trans_data);
                            if(!isset($result['status'])) return $result;
                        }
                        if(!empty($trans_data['ot_brmm_id'])){ 
                            $result = $this->update_readymade_barcode_add($trans_data);
                            if(!isset($result['status'])) return $result;
                        }

                        if(!isset($value['apparel_data']) || (isset($value['apparel_data']) && empty($value['apparel_data'])))
                        { 
                            $trans_data['ot_id'] = $value['ot_id'];
                            $measurement_clause  = ['cmt_ot_id' => $value['ot_id'], 'cmt_delete_status' => true];
                            $style_clause        = ['cst_ot_id' => $value['ot_id'], 'cst_delete_status' => true];
                            
                            if(empty($prev_data[0]['ot_om_id']) && !empty($prev_data[0]['ot_apparel_id'])) {
                                $result = $this->add_measurement_barcode_trans($trans_data);
                                if(!isset($result['status'])) return $result;
                            }else{
                                $measurement_clause  = ['cmt_ot_id' => $value['ot_id']];
                                $style_clause        = ['cst_ot_id' => $value['ot_id']];
                                $style_image_clause  = ['csit_ot_id'=> $value['ot_id']];
                                if(!empty($trans_data['ot_apparel_id'])){
                                    $result = $this->update_measurement_barcode_trans($trans_data);
                                    if(!isset($result['status'])) return $result;
                                }
                            }

                            $result = $this->update_customer_measurement_trans(['om_id' => $id, 'customer_id' => $post_data['om_customer_id']], $measurement_clause);
                            if(!isset($result['status'])) return $result;
        
                            $result = $this->update_customer_style_trans(['om_id' => $id, 'customer_id' => $post_data['om_customer_id']], $style_clause);
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
            foreach ($data['apparel_data'] as $key => $value){
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
                    $prev_data = $this->db_operations->get_record('order_trans', ['ot_id' => $value['ot_id']]);
                    if(empty($prev_data)) return ['msg' => '5. Transaction not found.'];

                    if($this->db_operations->data_update('order_trans', $trans_data, 'ot_id', $value['ot_id']) < 1) return ['msg' => 'Transaction not updated.'];

                    $trans_data['ot_id']            = $value['ot_id'];
                    $trans_data['ot_mtr']           = 0;
                    $trans_data['ot_rate']          = 0;
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
                        $result = $this->add_measurement_barcode_trans($trans_data);
                        if(!isset($result['status'])) return $result;
                    }else{
                        $measurement_clause  = ['cmt_ot_id' => $value['ot_id']];
                        $style_clause        = ['cst_ot_id' => $value['ot_id']];
                        $style_image_clause  = ['csit_ot_id'=> $value['ot_id']];
                        if(!empty($trans_data['ot_apparel_id'])){
                            $result = $this->update_measurement_barcode_trans($trans_data);
                            if(!isset($result['status'])) return $result;
                        }
                    }

                    $result = $this->update_customer_measurement_trans(['om_id' => $data['ot_om_id'], 'customer_id' => $data['om_customer_id']], $measurement_clause);
                    if(!isset($result['status'])) return $result;

                    $result = $this->update_customer_style_trans(['om_id' => $data['ot_om_id'], 'customer_id' => $data['om_customer_id']], $style_clause);
                    if(!isset($result['status'])) return $result;

                }
            }
            return ['status' => TRUE];
        }

        public function delete_trans($clause){
            $data = $this->db_operations->get_record($this->sub_menu1.'_trans', $clause);
            // echo "<pre>"; print_r($data); exit;
            if(empty($data)) return ['status' => TRUE];

            foreach ($data as $key => $value){
                $child_data = $this->db_operations->get_record($this->sub_menu1.'_trans', ['ot_ot_id' => $value['ot_id'], 'ot_delete_status' => false]);
                if(empty($child_data))
                {
                    if($this->model->isTransExist($value['ot_id'])) return ['msg' => '1. Not allowed to delete transaction.'];

                    if($this->model->isTransExist($value['ot_id'])) return ['msg' => '1. Not allowed to delete transaction.'];

                    if(!empty($value['ot_bm_id'])){
                        $result = $this->update_barcode_delete($value);
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
                if($this->db_operations->data_update($this->sub_menu1.'_trans', $update_data, 'ot_id', $value['ot_id']) < 1) return ['msg' => '2. Transaction not deleted.'];
               
            }
            return ['status' => TRUE];
        }

        public function delete_barcode_trans($clause){
            $data = $this->db_operations->get_record($this->sub_menu1.'_barcode_trans', $clause);
            if(empty($data)) return ['msg' => '5. Barcode not found.'];

            foreach ($data as $key => $value){
                if($this->model->isBarcodeExist($value['obt_id'])) return ['msg' => '1. Not allowed to delete barcode.'];
                $update_data                        = [];
                $update_data['obt_delete_status']   = true; 
                $update_data['obt_updated_by']      = $_SESSION['user_id']; 
                $update_data['obt_updated_at']      = date('Y-m-d H:i:s'); 
                if($this->db_operations->data_update($this->sub_menu1.'_barcode_trans', $update_data, 'obt_id', $value['obt_id']) < 1) return ['msg' => '1. Barcode is not deleted.'];
            }
            return ['status' => TRUE];
        }

        public function add_measurement_barcode_trans($trans_data){   
            // $app_trans = $this->db_operations->get_record('apparel_apparel_trans', ['apparel_id' => $trans_data['ot_apparel_id']]);

            // if (!empty($app_trans)) {
            //     foreach ($app_trans as $key => $value) {
            //         for ($i = 1; $i <= $trans_data['ot_qty']; $i++) { 
            //             $year = date('y');
            //             $month = date('m');
            //             $barcode_master = [];   
            //             $barcode_master['obt_barcode_year'] = date('Y');
            //             $barcode_master['obt_barcode_month'] = $month;
            //             $barcode_master['obt_counter']  = $this->model->generate_barcode();
            //             $barcode_master['obt_item_code']        = $year . $month . $barcode_master['obt_counter'];
            //             $barcode_master['obt_roll_no']          = $barcode_master['obt_item_code'];
            //             $barcode_master['obt_om_id']            = $trans_data['ot_om_id'];
            //             $barcode_master['obt_ot_id']            = $trans_data['ot_id'];
            //             $barcode_master['obt_apparel_id']       = $trans_data['ot_apparel_id']; 
            //             $barcode_master['obt_apparel_id1']      = $value['aat_apparel_id']; 
            //             $barcode_master['obt_qty'] = 1;
            //             $barcode_master['obt_rate'] = $trans_data['ot_rate'];
            //             $barcode_master['obt_amt'] = $trans_data['ot_rate'];
            //             $barcode_master['obt_disc_amt'] = $trans_data['ot_disc_amt'] / $trans_data['ot_qty'];
            //             $barcode_master['obt_taxable_amt'] = $trans_data['ot_taxable_amt'] / $trans_data['ot_qty'];
            //             $barcode_master['obt_sgst_amt'] = $trans_data['ot_sgst_amt'] / $trans_data['ot_qty'];
            //             $barcode_master['obt_cgst_amt'] = $trans_data['ot_cgst_amt'] / $trans_data['ot_qty'];
            //             $barcode_master['obt_igst_amt'] = $trans_data['ot_igst_amt'] / $trans_data['ot_qty'];
            //             $barcode_master['obt_total_amt'] = $trans_data['ot_total_amt'] / $trans_data['ot_qty'];
            //             $barcode_master['obt_description'] = $trans_data['ot_description'];
            //             $barcode_master['obt_delete_status'] = false;
            //             $barcode_master['obt_branch_id'] = $_SESSION['user_branch_id'];
            //             $barcode_master['obt_fin_year'] = $_SESSION['fin_year'];
            //             $barcode_master['obt_created_by'] = $_SESSION['user_id'];
            //             $barcode_master['obt_created_at'] = date('Y-m-d H:i:s');
            //             $barcode_master['obt_updated_by'] = $_SESSION['user_id'];
            //             $barcode_master['obt_updated_at'] = date('Y-m-d H:i:s');
            //            // echo "<pre>"; print_r($barcode_master);die;
            //             if ($this->db_operations->data_insert('order_barcode_trans', $barcode_master) < 1) {
            //                 return ['msg' => '1. Barcode not added.'];
            //             }
            //         }
            //     }
            // } else {
                    for ($i = 1; $i <= $trans_data['ot_qty']; $i++) {
                        $year = date('y');
                        $month = date('m');
                        $barcode_master = [];   
                        $barcode_master['obt_barcode_year'] = date('Y');
                        $barcode_master['obt_barcode_month'] = $month;
                        $barcode_master['obt_counter'] = $this->model->generate_barcode();
                        $barcode_master['obt_item_code'] = $year . $month . $barcode_master['obt_counter'];
                        $barcode_master['obt_roll_no'] = $barcode_master['obt_item_code'];
                        $barcode_master['obt_om_id']            = $trans_data['ot_om_id'];
                        $barcode_master['obt_ot_id']            = $trans_data['ot_id'];
                        $barcode_master['obt_apparel_id'] = $trans_data['ot_apparel_id']; 
                        $barcode_master['obt_apparel_id1'] =$trans_data['ot_apparel_id']; 
                        $barcode_master['obt_qty'] = 1;

                        $barcode_master['obt_rate'] = $trans_data['ot_rate'];
                        $barcode_master['obt_amt'] = $trans_data['ot_rate'];
                        $barcode_master['obt_disc_amt'] = $trans_data['ot_disc_amt'] / $trans_data['ot_qty'];
                        $barcode_master['obt_taxable_amt'] = $trans_data['ot_taxable_amt'] / $trans_data['ot_qty'];
                        $barcode_master['obt_sgst_amt'] = $trans_data['ot_sgst_amt'] / $trans_data['ot_qty'];
                        $barcode_master['obt_cgst_amt'] = $trans_data['ot_cgst_amt'] / $trans_data['ot_qty'];
                        $barcode_master['obt_igst_amt'] = $trans_data['ot_igst_amt'] / $trans_data['ot_qty'];
                        $barcode_master['obt_total_amt'] = $trans_data['ot_total_amt'] / $trans_data['ot_qty'];
                        $barcode_master['obt_description'] = $trans_data['ot_description'];
                        $barcode_master['obt_delete_status'] = false;
                        $barcode_master['obt_branch_id'] = $_SESSION['user_branch_id'];
                        $barcode_master['obt_fin_year'] = $_SESSION['fin_year'];
                        $barcode_master['obt_created_by'] = $_SESSION['user_id'];
                        $barcode_master['obt_created_at'] = date('Y-m-d H:i:s');
                        $barcode_master['obt_updated_by'] = $_SESSION['user_id'];
                        $barcode_master['obt_updated_at'] = date('Y-m-d H:i:s');
                        if ($this->db_operations->data_insert('order_barcode_trans', $barcode_master) < 1) {
                            return ['msg' => '1. Barcode not added.'];
                        }
                }
            // }

            return ['status' => TRUE];
        }

        public function update_measurement_barcode_trans($trans_data){
            // echo "<pre>"; print_r($trans_data);die;
            $prev_data = $this->db_operations->get_record($this->sub_menu1.'_barcode_trans', ['obt_ot_id' => $trans_data['ot_id'], 'obt_delete_status' => false]);
            if(empty($prev_data)) return ['msg' => '2. Barcode not found. '.$trans_data['ot_id']];
            foreach ($prev_data as $key => $value) {
                $barcode_master                         = [];   
                $barcode_master['obt_apparel_id']       = $trans_data['ot_apparel_id'];
                $barcode_master['obt_description']      = $trans_data['ot_description'];
                $barcode_master['obt_rate']             = $trans_data['ot_rate'];
                $barcode_master['obt_amt']              = $trans_data['ot_rate'] * $trans_data['ot_qty'];
                $barcode_master['obt_disc_amt']         = $trans_data['ot_disc_amt'] / $trans_data['ot_qty'];
                $barcode_master['obt_taxable_amt']      = $trans_data['ot_taxable_amt'] / $trans_data['ot_qty'];
                $barcode_master['obt_sgst_amt']         = $trans_data['ot_sgst_amt'] / $trans_data['ot_qty'];
                $barcode_master['obt_cgst_amt']         = $trans_data['ot_cgst_amt'] / $trans_data['ot_qty'];
                $barcode_master['obt_igst_amt']         = $trans_data['ot_igst_amt'] / $trans_data['ot_qty'];
                $barcode_master['obt_total_amt']        = $trans_data['ot_total_amt'] / $trans_data['ot_qty'];
                $barcode_master['obt_updated_by']       = $_SESSION['user_id'];
                $barcode_master['obt_updated_at']       = date('Y-m-d H:i:s');
                if($this->db_operations->data_update($this->sub_menu1.'_barcode_trans', $barcode_master, 'obt_id', $value['obt_id']) < 1) return ['msg' => '1. Barcode not update.'];
            }

            return ['status' => TRUE];
        }

        
    // estimate_trans
    // estimate_barcode_trans
        // public function add_barcode($om_id, $ot_id, $trans_data){
        //     for ($i = 1; $i <= $trans_data['ot_qty'] ; $i++) { 
        //         $year                                   = date('y');
        //         $month                                  = date('m');
                
        //         $barcode_master                         = [];   
        //         $barcode_master['obt_barcode_year']     = date('Y');
        //         $barcode_master['obt_barcode_month']    = $month;
        //         $barcode_master['obt_counter']          = $this->model->generate_barcode();
        //         $barcode_master['obt_item_code']        = $year.''.$month.''.$barcode_master['obt_counter'];
        //         $barcode_master['obt_roll_no']          = $barcode_master['obt_item_code'];
        //         $barcode_master['obt_om_id']            = $om_id;
        //         $barcode_master['obt_ot_id']            = $ot_id;
        //         $barcode_master['obt_apparel_id']       = $trans_data['ot_apparel_id'];
        //         $barcode_master['obt_qty']              = 1;
        //         $barcode_master['obt_mtr']              = $trans_data['ot_mtr'];
        //         $barcode_master['obt_rate']             = $trans_data['ot_rate'];
        //         $barcode_master['obt_amt']              = $trans_data['ot_rate'] * $trans_data['ot_mtr'];
        //         $barcode_master['obt_disc_amt']         = $trans_data['ot_disc_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_taxable_amt']      = $trans_data['ot_taxable_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_sgst_amt']         = $trans_data['ot_sgst_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_cgst_amt']         = $trans_data['ot_cgst_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_igst_amt']         = $trans_data['ot_igst_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_total_amt']        = $trans_data['ot_total_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_description']      = $trans_data['ot_description'];
        //         $barcode_master['obt_delete_status']    = false;
        //         $barcode_master['obt_branch_id']        = $_SESSION['user_branch_id'];
        //         $barcode_master['obt_fin_year']         = $_SESSION['fin_year'];
        //         $barcode_master['obt_created_by']       = $_SESSION['user_id'];
        //         $barcode_master['obt_created_at']       = date('Y-m-d H:i:s');
        //         $barcode_master['obt_updated_by']       = $_SESSION['user_id'];
        //         $barcode_master['obt_updated_at']       = date('Y-m-d H:i:s');

        //         if($this->db_operations->data_insert('order_barcode_trans', $barcode_master) < 1) return ['msg' => '1. Barcode not added.'];
        //     }
        //     return ['status' => TRUE];
        // }
        // public function update_barcode($om_id, $ot_id, $trans_data){
        //     $prev_data = $this->db_operations->get_record('order_barcode_trans', ['obt_ot_id' => $ot_id, 'obt_delete_status' => false]);
        //     if(empty($prev_data)) return ['msg' => '1. Barcode not found..'];

        //     foreach ($prev_data as $key => $value) {
        //         $barcode_master                         = [];   
        //         $barcode_master['obt_apparel_id']       = $trans_data['ot_apparel_id'];
        //         $barcode_master['obt_description']      = $trans_data['ot_description'];
        //         $barcode_master['obt_rate']             = $trans_data['ot_rate'];
        //         $barcode_master['obt_mtr']              = $trans_data['ot_mtr'];
        //         $barcode_master['obt_amt']              = $trans_data['ot_rate'] * $trans_data['ot_mtr'];
        //         $barcode_master['obt_disc_amt']         = $trans_data['ot_disc_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_taxable_amt']      = $trans_data['ot_taxable_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_sgst_amt']         = $trans_data['ot_sgst_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_cgst_amt']         = $trans_data['ot_cgst_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_igst_amt']         = $trans_data['ot_igst_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_total_amt']        = $trans_data['ot_total_amt'] / $trans_data['ot_qty'];
        //         $barcode_master['obt_updated_by']       = $_SESSION['user_id'];
        //         $barcode_master['obt_updated_at']       = date('Y-m-d H:i:s');
    
        //         if($this->db_operations->data_update('order_barcode_trans', $barcode_master, 'obt_id', $value['obt_id']) < 1) return ['msg' => '1. Barcode not update.'];
        //     }
        //     return ['status' => TRUE];
        // }
        // public function delete_barcode($ot_id){ 
        //     $data = $this->db_operations->get_record('order_barcode_trans', ['obt_ot_id' => $ot_id, 'obt_delete_status' => false]);
        //     // echo "<pre>"; print_r($ot_id);die;
        //     if(empty($data)) return ['msg' => '1. Barcode not found'];

        //     foreach ($data as $key => $value){
        //         if($this->model->isBarcodeExist($value['obt_id'])) return ['msg' => '2. Not allowed to delete barcode.'];
        //         $update_data                        = [];
        //         $update_data['obt_delete_status']   = true; 
        //         $update_data['obt_updated_by']      = $_SESSION['user_id']; 
        //         $update_data['obt_updated_at']      = date('Y-m-d H:i:s'); 
        //         if($this->db_operations->data_update('order_barcode_trans', $update_data, 'obt_id', $value['obt_id']) < 1){
        //             return ['msg' => '2. Barcode delete status not set as true'];
        //         }
        //     }
        //     return ['status' => TRUE];
        // }
    // order_barcode_trans
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
                        $update_data                        = [];
                        $update_data['opmt_delete_status']  = true;
                        $update_data['opmt_updated_by']     = $_SESSION['user_id'];
                        $update_data['opmt_updated_at']     = date('Y-m-d H:i:s');
                        if($this->db_operations->data_update('order_payment_mode_trans', $update_data, 'opmt_id', $value['opmt_id']) < 1){
                            return ['msg' => '1. Payment mode not deleted.'];
                        }
                    }
                }
                foreach ($post_data['opmt_amt'] as $key => $value) {
                    if($value <= 0){
                        $update_data                        = [];
                        $update_data['opmt_delete_status']  = true;
                        $update_data['opmt_updated_by']     = $_SESSION['user_id'];
                        $update_data['opmt_updated_at']     = date('Y-m-d H:i:s');
                        if($this->db_operations->data_update('order_payment_mode_trans', $update_data, 'opmt_id', $post_data['opmt_id'][$key]) < 1){
                            return ['msg' => '1. Payment mode not deleted.'];
                        }
                    }
                }
            }
            foreach ($post_data['opmt_amt'] as $key => $value){
                if($value > 0){
                    $trans_data                         = [];
                    $trans_data['opmt_om_id']           = $id;
                    $trans_data['opmt_om_uuid']         = $post_data['om_uuid'];
                    $trans_data['opmt_payment_mode_id'] = $post_data['opmt_payment_mode_id'][$key];
                    $trans_data['opmt_amt']             = $post_data['opmt_amt'][$key];
                    $trans_data['opmt_delete_status']   = false;
                    $trans_data['opmt_updated_by']      = $_SESSION['user_id'];
                    $trans_data['opmt_updated_at']      = date('Y-m-d H:i:s');
                    
                    if(empty($post_data['opmt_id'][$key])){
                        $trans_data['opmt_created_by']  = $_SESSION['user_id'];
                        $trans_data['opmt_created_at']  = date('Y-m-d H:i:s');
                        if($this->db_operations->data_insert('order_payment_mode_trans', $trans_data) < 1){
                            return ['msg' => '1. Payment mode not added.'];
                        }
                    }
                }
            }
            return ['status' => TRUE];
        }
    // payment_mode_trans

    // barcode_master
        public function update_barcode_add($trans_data){
            $data = $this->model->get_barcode_data($trans_data['ot_bm_id']);
            if(empty($data)) return ['msg' => '3. Barcode not found.'];
            if($data[0]['bm_delete_status'] == 1) return ['msg' => '2. Barcode is deleted.'];
            if($trans_data['ot_total_mtr'] > $data[0]['bal_qty']) return ['msg' => '3. Barcode not available.'];            
            $barcode_master = [];
            $barcode_master['bm_ot_mtr']= $data[0]['bm_ot_mtr'] + $trans_data['ot_total_mtr'];
            if($this->db_operations->data_update('barcode_master', $barcode_master, 'bm_id', $trans_data['ot_bm_id']) < 1){
                return ['msg' => 'Barcode not updated.'];   
            }
            return ['status' => TRUE];  
        }
        public function update_barcode_delete($trans_data){
            if($this->model->isTransExist($trans_data['ot_id'])) return ['msg' => '6. Not allowed to delete.'];
            $data = $this->model->get_barcode_data($trans_data['ot_bm_id']);
            if(empty($data)) return ['msg' => '3. Barcode not found'];            
            if($trans_data['ot_total_mtr'] > ($data[0]['bm_ot_mtr'])) return ['msg' => '4. Barcode not available.'];
            $barcode_master['bm_ot_mtr']= $data[0]['bm_ot_mtr'] - $trans_data['ot_total_mtr'];
            if($this->db_operations->data_update('barcode_master', $barcode_master, 'bm_id', $trans_data['ot_bm_id']) < 1){
                return ['msg' => 'Barcode not updated.'];   
            }
            return ['status' => TRUE];  
        }
    // barcode_master


    // order_master
        public function add_edit_order(){  
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            // echo "<pre>"; print_r($post_data); exit;
            $prev_data = $this->model->get_data($id); 
            if(empty($prev_data)) return ['msg' => '1. Estimate not found.'];
            if($prev_data[0]['om_allocated_amt']>0) return ['msg' => '1. Can not tranfer to order.'];
            $this->load->model('transaction/order_model'); 
            // master_data
                $master_data['om_entry_no']  = $this->order_model->get_max_entry_no(['entry_no' => 'om_entry_no', 'delete_status' => 'om_delete_status', 'fin_year' => 'om_fin_year', 'branch_id' => 'om_branch_id']);
                $master_data['om_entry_date']  = date('Y-m-d');
                $master_data['om_status']      =1;
           
            $this->db->trans_begin();
            $om_id = $this->db_operations->data_update('order_master', $master_data,['om_id'=>$id]);
            if($om_id < 1){
                $this->db->trans_rollback();
                return ['msg' => '1. Order not added.'];
            }
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return ['msg' => '1. Transaction Rollback.'];
            }

            $this->db->trans_commit();
            return ['status' => TRUE, 'msg' => 'Estimate converted to order successfully.'];
        }
    // order_master

      

}?>