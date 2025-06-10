<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class order extends my_controller{  
    protected $menu;
    protected $sub_menu;
    protected $sub_menu1;
    public function __construct(){
        $this->menu     = 'transaction'; 
        $this->sub_menu = 'order'; 
        $this->sub_menu1 = 'order_employee'; 

        parent::__construct($this->menu, $this->sub_menu);
    }

    public function remove(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $result     = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
        if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

        $this->db->trans_begin();

            $result = $this->delete_trans(['ot_om_id' => $id,'ot_delete_status' => false]);
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
        return ['status' => TRUE, 'msg' => 'order deleted successfully'];
    }

    public function delete_master($clause){    
        $data = $this->db_operations->get_record($this->sub_menu.'_master', $clause);
        if(empty($data)) return ['msg' => '3. order not found.'];
        foreach ($data as $key => $value){
            if($this->model->isExist($value['om_id'])) return ['msg' => '1. Not allowed to delete.'];
            $prev_data = $this->db_operations->get_record('order_master', ['om_id' => $value['om_id']]);
            if(empty($prev_data)) return ['msg' => '1. order not found.'];
            $update_data                        = [];
            $update_data['om_entry_no']         = $data[0]['om_entry_no'].''.$value['om_id']; 
            $update_data['om_delete_status']    = true; 
            $update_data['om_updated_by']       = $_SESSION['user_id']; 
            $update_data['om_updated_at']       = date('Y-m-d H:i:s');
            if($this->db_operations->data_update($this->sub_menu.'_master', $update_data, 'om_id', $value['om_id']) < 1) return ['msg' => '1. order not deleted.'];
        }
        return ['status' => TRUE];
    }

    public function get_customer_data(){ 
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $data       = $this->model->get_customer_data($id);
        return['status' => TRUE, 'data' => $data, 'msg' => 'Customer fetched successfully.'];
    }
  
    public function get_sku_data(){  
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $data = $this->model->get_sku_data($id);
        if((empty($data))) return ['msg' => '1. Sku not found.'];
        // echo "<pre>"; print_r($data);die;
        return['status' => TRUE, 'data' => $data, 'msg' => 'Sku data fetched successfully.'];
    }

    // order_master
    public function add_edit(){    
            $post_data  = $this->input->post();
            $id         = $post_data['id'];   
            $post_data['sku_trans_data']= isset($post_data['sku_trans_data']) ? json_decode($post_data['sku_trans_data'], true) : [];
            if(empty($post_data['trans_data']) && empty($post_data['sku_trans_data'])) return ['msg' => '1. Item not aded in list.'];
            $post_data['employee_trans_data']= isset($post_data['employee_trans_data']) ? json_decode($post_data['employee_trans_data'], true) : [];
            // echo "<pre>"; print_r($post_data); exit; 
            // master_data
                $master_data['om_uuid']                     = trim($post_data['om_uuid']);
                $master_data['om_entry_no']                 = trim($post_data['om_entry_no']);
                $master_data['om_entry_date']               = date('Y-m-d', strtotime($post_data['om_entry_date']));
                $master_data['om_customer_id']              = isset($post_data['om_customer_id'])?trim($post_data['om_customer_id']):0;
                $master_data['om_gst_type']                 = trim($post_data['om_gst_type']);
                $master_data['om_bill_type']                = isset($post_data['om_bill_type']);
                $master_data['om_notes']                    = trim($post_data['om_notes']);
                $master_data['om_total_qty']                = trim($post_data['om_total_qty']);
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
                $master_data['om_updated_by']               = $_SESSION['user_id'];
            // master_data
                $cnt =$this->db_operations->get_cnt('order_master',['om_id!='=>$id,'om_entry_no'=>$master_data['om_entry_no'],'om_fin_year'=>$_SESSION['fin_year'],'om_delete_status'=>0]);
                if($cnt>0){
                     return ['msg' => 'Duplicate Entry No found!!'];
                }

            $this->db->trans_begin();
            if($id == 0){
                // $master_data['om_entry_no']  = $this->model->get_max_entry_no(['entry_no' => 'om_entry_no', 'delete_status' => 'om_delete_status', 'fin_year' => 'om_fin_year']);
                $master_data['om_created_by']   = $_SESSION['user_id'];
                $master_data['om_created_at']   = date('Y-m-d H:i:s');
                $master_data['om_fin_year']     = $_SESSION['fin_year'];
                $master_data['om_branch_id']    = $_SESSION['user_branch_id'];
                $uuidExist                      = $this->db_operations->get_cnt($this->sub_menu.'_master', ['om_uuid' => $master_data['om_uuid']]);
                if($uuidExist > 0){
                    $this->db->trans_rollback();
                    return ['msg' => 'Form already submited.'];
                }
                $id = $this->db_operations->data_insert('order_master', $master_data);
                $msg = 'order added successfully.';
                if($id < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. order not added.'];
                }
            }else{
                $prev_data = $this->db_operations->get_record('order_master', ['om_id' => $id, 'om_delete_status' => false]);
                if(empty($prev_data)){
                    $this->db->trans_rollback();
                    return ['status' => REFRESH, 'msg' => '1. order not found.'];
                }
                $msg = 'order updated successfully.';
                if($this->db_operations->data_update('order_master', $master_data, 'om_id', $id) < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. order not updated.'];
                }
            }

            $result = $this->add_update_sku_trans($post_data, $id);
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
            $data['name']   = strtoupper($master_data['om_entry_no']);
            return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
    }
    // order_master
    public function add_update_sku_trans($post_data, $id){
                $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_om_id' => $id, 'ot_delete_status' => false, 'ot_trans_type' => 'SKU']);
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
                        $trans_data['ot_sku_id']            = $value['ot_sku_id'];
                        $trans_data['ot_qty']               = $value['ot_qty'];
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
                    // trans_data

                    if(!empty($value['ot_id'])){  
                        $prev_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_id' => $value['ot_id']]);
                        if(empty($prev_data)) return ['msg' => '2. Transaction not found.'];
                        if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'ot_id', $value['ot_id']) < 1) return ['msg' => '2. Transaction not updated.'];

                        $result = $this->add_update_employee_trans($post_data, $value['ot_id'],$id);
                        if(!isset($result['status'])){
                            $this->db->trans_rollback();
                            return $result;
                        }
                    }
                }
    
                return ['status' => TRUE];
    }

    public function add_update_employee_trans($post_data, $id,$om_id){   
         $trans_db_data = $this->db_operations->get_record($this->sub_menu1.'_trans', ['oet_ot_id' => $id, 'oet_delete_status' => false,'oet_oet_id' => 0]);
        $ids    = $this->get_id($post_data['employee_trans_data'], 'oet_id');
        if(!empty($trans_db_data)){
            foreach ($trans_db_data as $key => $value){ 
                if(!in_array($value['oet_id'], $ids)){ 
                    $result = $this->delete_employee_trans(['oet_id' => $value['oet_id'], 'oet_delete_status' => false]);
                    if(!isset($result['status'])) return $result;
                } 
            }
        }
        foreach ($post_data['employee_trans_data'] as $key => $value){ 
            $trans_data                         = [];
            $trans_data['oet_ot_id']            = $id;
            $trans_data['oet_om_id']            = $om_id;
            $trans_data['oet_code']             = $value['oet_code'];
            $trans_data['oet_name']             = $value['oet_name'];
            $trans_data['oet_mobile']           = $value['oet_mobile'];
            $trans_data['oet_email']            = $value['oet_email'];
            $trans_data['oet_description']      = $value['oet_description'];
            $trans_data['oet_designation_id']   = $value['oet_designation_id'];
            $trans_data['oet_apparel_id']       = $value['oet_apparel_id'];
            $trans_data['oet_customer_id']      = $post_data['om_customer_id'];
            $trans_data['oet_delete_status']     = false;
            $trans_data['oet_updated_by']        = $_SESSION['user_id'];
            $trans_data['oet_updated_at']        = date('Y-m-d H:i:s');
            
            if($value['oet_id'] != 0){ 
                $prev_data = $this->db_operations->get_record('order_employee_trans', ['oet_id' => $value['oet_id']]);
                if(empty($prev_data)) return ['msg' => '5. employee Transaction not found.'];
                if(!$this->model->isTransExist($value['oet_id'])){
                    if($this->db_operations->data_update('order_employee_trans', $trans_data, 'oet_id', $value['oet_id']) < 1){
                        return ['msg' => 'Employee Transaction not updated.'];
                    }
                    $trans_data['oet_id'] = $value['oet_id'];
                    if(!isset($value['apparel_data']) || (isset($value['apparel_data']) && empty($value['apparel_data'])))
                    { 
                        $trans_data['oet_id'] = $value['oet_id'];
                        $measurement_clause  = ['cmt_oet_id' => $value['oet_id'], 'cmt_delete_status' => true];
                        $style_clause      = ['cst_oet_id' => $value['oet_id'], 'cst_delete_status' => true];
                        if(empty($prev_data[0]['oet_ot_id']) && !empty($prev_data[0]['oet_apparel_id'])) { 
                            // $result = $this->add_measurement_barcode_trans($trans_data);
                            // if(!isset($result['status'])) return $result;
                        }else{
                            $measurement_clause  = ['cmt_oet_id' => $value['oet_id']];
                            $style_clause        = ['cst_oet_id' => $value['oet_id']];
                            // if(!empty($trans_data['oet_apparel_id'])){
                            //     $result = $this->update_measurement_barcode_trans($trans_data);
                            //     if(!isset($result['status'])) return $result;
                            // }
                        }

                        $result = $this->update_customer_measurement_trans(['ot_id' => $id, 'customer_id' => $post_data['om_customer_id']], $measurement_clause);
                        if(!isset($result['status'])) return $result;
    
                        $result = $this->update_customer_style_trans(['ot_id' => $id, 'customer_id' => $post_data['om_customer_id']], $style_clause);
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
                    $trans_data['oet_ot_id']            = $data['oet_ot_id'];
                    $trans_data['oet_oet_id']           = $value['oet_oet_id'];
                    $trans_data['oet_apparel_id']       = $value['oet_apparel_id'];
                    $trans_data['oet_description']       = $value['oet_description'];
                    $trans_data['oet_delete_status']     = false;
                    $trans_data['oet_updated_by']        = $_SESSION['user_id'];
                    $trans_data['oet_updated_at']        = date('Y-m-d H:i:s');
                // trans_data

                if(!empty($value['oet_id'])){
                    $prev_data = $this->db_operations->get_record('order_employee_trans', ['oet_id' => $value['oet_id']]);
                    if(empty($prev_data)) return ['msg' => '5. Employee Transaction not found.'];
                    if($this->db_operations->data_update('order_employee_trans', $trans_data, 'oet_id', $value['oet_id']) < 1) return ['msg' => 'Employee Transaction not updated.'];

                    $trans_data['oet_id']            = $value['oet_id'];
                   
                    $measurement_clause  = ['cmt_oet_id' => $value['oet_id'], 'cmt_delete_status' => true];
                    $style_clause        = ['cst_oet_id' => $value['oet_id'], 'cst_delete_status' => true];
                    
                    if(empty($prev_data[0]['oet_ot_id'])){ 
                        // $result = $this->add_measurement_barcode_trans($trans_data);
                        // if(!isset($result['status'])) return $result;
                    }else{
                        $measurement_clause  = ['cmt_oet_id' => $value['oet_id']];
                        $style_clause        = ['cst_oet_id' => $value['oet_id']];
                        // if(!empty($trans_data['ot_apparel_id'])){
                        //     $result = $this->update_measurement_barcode_trans($trans_data);
                        //     if(!isset($result['status'])) return $result;
                        // }
                    }

                    $result = $this->update_customer_measurement_trans(['ot_id' => $data['oet_ot_id'], 'customer_id' => $data['om_customer_id']], $measurement_clause);
                    if(!isset($result['status'])) return $result;
                    $result = $this->update_customer_style_trans(['ot_id' => $data['oet_ot_id'], 'customer_id' => $data['om_customer_id']], $style_clause);
                    if(!isset($result['status'])) return $result;

                }
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

      // sku
        public function add_sku_transaction(){ 
            $post_data  = $this->input->post();
             $id         = $post_data['id'];
            // echo "<pre>"; print_r($post_data); exit;
            $arr = $arr = ['sku_qty', 'sku_rate', 'sku_amt'];
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
            
            $sku_data=$this->db_operations->get_record('sku_master',['sku_id'=>$post_data['sku_id']]);
            if(empty($sku_data))  return ['msg' => '1. Sku not found.'];

            $trans_data                         = [];
            $trans_data['ot_trans_type']        = 'SKU';
            $trans_data['ot_om_uuid']           = $post_data['om_uuid'];
            $trans_data['ot_sku_id']            = $post_data['sku_id'];
            $trans_data['ot_apparel_id']        = $sku_data[0]['sku_apparel_id']; 
            $trans_data['ot_qty']               = $post_data['sku_qty'];
            $trans_data['ot_rate']              = $post_data['sku_rate'];
            $trans_data['ot_amt']               = $post_data['sku_amt']; 
            $trans_data['ot_disc_per']          = $post_data['sku_disc_per']; 
            $trans_data['ot_disc_amt']          = $post_data['sku_disc_amt']; 
            $trans_data['ot_taxable_amt']       = $post_data['sku_taxable_amt']; 
            $trans_data['ot_sgst_per']          = $post_data['sku_sgst_per'];
            $trans_data['ot_cgst_per']          = $post_data['sku_cgst_per']; 
            $trans_data['ot_igst_per']          = $post_data['sku_igst_per'];
            $trans_data['ot_sgst_amt']          = $post_data['sku_sgst_amt']; 
            $trans_data['ot_cgst_amt']          = $post_data['sku_cgst_amt']; 
            $trans_data['ot_igst_amt']          = $post_data['sku_igst_amt']; 
            $trans_data['ot_total_amt']         = $post_data['sku_total_amt']; 
            $trans_data['ot_description']       = $post_data['sku_description'];
            $trans_data['ot_delete_status']     = true;
            $trans_data['ot_created_by']        = $_SESSION['user_id'];
            $trans_data['ot_updated_by']        = $_SESSION['user_id'];
            $trans_data['ot_created_at']        = date('Y-m-d H:i:s');
            $trans_data['ot_updated_at']        = date('Y-m-d H:i:s');
            if(empty($post_data['ot_id'])){
                $trans_data['ot_id'] = $this->db_operations->data_insert($this->sub_menu.'_trans', $trans_data);
                if($trans_data['ot_id'] < 1) return ['msg' => '1. Sku Design Transaction not added.'];
                $trans_data['isExist'] = false;
                $trans_data['ot_om_id'] = 0;
            }else{
                $trans_data['ot_om_id'] = $id;
                $trans_data['ot_id']    = $post_data['ot_id'];
            }

            $record[$key]['encrypt_ot_id']  = encrypt_decrypt("encrypt", $trans_data['ot_id'], SECRET_KEY);
            $trans_data['sku_name'] = $this->model->get_name('sku', $trans_data['ot_sku_id']);
            $trans_data['apparel_name'] = $this->model->get_name('apparel', $trans_data['ot_apparel_id']);
             // echo "<pre>"; print_r($result['data']); exit;
            return ['status' => TRUE, 'data' => $trans_data, 'msg' => 'order Sku Transaction added successfully.'];
        }
        public function add_employee(){ 
            if(!isset($_GET['id']) || (isset($_GET['id']) && empty($_GET['id']))){
                $this->load->view('errors/not_found');
                return;
            }
            // $id = encrypt_decrypt("decrypt", $_GET['id'], SECRET_KEY);
            // if(empty($id)){
            //     $this->load->view('errors/not_found');
            //     return; 
            // }
            $action_data= get_action_data($this->menu, $this->sub_menu);
             $menu_data  = get_submenu_data($this->menu, $this->sub_menu);
            $record   = $this->model->get_data_for_add();
            $record['menu']            = $this->menu;
            $record['sub_menu']     = $this->sub_menu;
            $record['action_data']  = $action_data;
            $record['menu_name']    = $menu_data['menu_name'];
            $record['sub_menu_name']= $menu_data['sub_menu_name'];
            $record['url']          = $menu_data['url'];

            // echo "<pre>"; print_r($record); exit;
            $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/_employee_form', $record); return ;
        }   

    public function delete_trans($clause){   
        $data = $this->db_operations->get_record($this->sub_menu.'_trans', $clause);
        // echo "<pre>"; print_r($data); exit;
        if(empty($data)) return ['status' => TRUE];

        foreach ($data as $key => $value){  
            $child_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['ot_id' => $value['ot_id'], 'ot_delete_status' => false]);
            $update_data                        = [];
            $update_data['ot_delete_status']    = true; 
            $update_data['ot_updated_by']       = $_SESSION['user_id']; 
            $update_data['ot_updated_at']       = date('Y-m-d H:i:s'); 
            if($this->db_operations->data_update($this->sub_menu.'_trans', $update_data, 'ot_id', $value['ot_id']) < 1) return ['msg' => '2. Transaction not deleted.'];
            $result = $this->delete_employee_trans(['oet_ot_id' => $value['ot_id'], 'oet_delete_status' => false]);
        }

        return ['status' => TRUE];
    }

    public function delete_employee_trans($clause){   
        $data = $this->db_operations->get_record($this->sub_menu1.'_trans', $clause);
        // echo "<pre>"; print_r($data); exit;
        if(empty($data)) return ['status' => TRUE];

        foreach ($data as $key => $value){
            $child_data = $this->db_operations->get_record($this->sub_menu1.'_trans', ['oet_oet_id' => $value['oet_id'], 'oet_delete_status' => false]);
            if(empty($child_data))  
            {
                $result = $this->delete_customer_measurement_trans(['cmt_oet_id' => $value['oet_id'], 'cmt_delete_status' => false]);
                if(!isset($result['status'])) return $result;

                $result = $this->delete_customer_style_trans(['cst_oet_id' => $value['oet_id'], 'cst_delete_status' => false]);
                if(!isset($result['status'])) return $result;

            }else{
                foreach ($child_data as $k => $v) {
                    $result = $this->delete_employee_trans(['oet_id' => $v['oet_id'], 'oet_delete_status' => false]);
                    if(!isset($result['status'])) return $result;
                }
            }
               
            $update_data                        = [];
            $update_data['oet_delete_status']    = true; 
            $update_data['oet_updated_by']       = $_SESSION['user_id']; 
            $update_data['oet_updated_at']       = date('Y-m-d H:i:s'); 
            if($this->db_operations->data_update($this->sub_menu1.'_trans', $update_data, 'oet_id', $value['oet_id']) < 1) return ['msg' => '2. Employee Transaction not deleted.'];
           
        }

        return ['status' => TRUE];
    }

    public function add_emp_transaction(){       
        $post_data  = $this->input->post();
        $id   = $post_data['ot_id'];
        $ot_data = $this->db_operations->get_record('order_trans',['ot_id'=>$id]);
        if(empty($ot_data)) return ['msg' => 'Order Transaction not found !!'];
         $customer_id = $this->model->get_customer_id($id); 
           // echo "<pre>"; print_r($id);die;  
        $trans_data                     = [];
        $trans_data['oet_ot_id']       = $id;  
        $trans_data['oet_code']         = trim($post_data['emp_code']);
        $trans_data['oet_name']         = trim($post_data['emp_name']);
        $trans_data['oet_mobile']       = trim($post_data['emp_mobile']);
        $trans_data['oet_email']        = trim($post_data['emp_email']);
        $trans_data['oet_description']  = trim($post_data['emp_description']);
        $trans_data['oet_designation_id']= isset($post_data['emp_designation_id']) ? $post_data['emp_designation_id'] : 0; 
        $trans_data['oet_customer_id']   = $customer_id;
        $trans_data['oet_apparel_id']    = $ot_data[0]['ot_apparel_id'];
        $trans_data['oet_delete_status'] = true;
        $trans_data['oet_created_by']    = $_SESSION['user_id'];
        $trans_data['oet_updated_by']    = $_SESSION['user_id'];
        $trans_data['oet_created_at']    = date('Y-m-d H:i:s');
        $trans_data['oet_updated_at']    = date('Y-m-d H:i:s');
        
        $validated = [
                    'oet_id!='           => $post_data['oet_id'], 
                    // 'ot_uuid'        => $trans_data['ot_uuid'], 
                    'oet_code'          => $trans_data['oet_code'],
                    'oet_name'          => $trans_data['oet_name'],
                    'oet_mobile'        => $trans_data['oet_mobile'],
                    'oet_email'        => $trans_data['oet_email'],
                    'oet_email'        => $trans_data['oet_email'],
                    'oet_delete_status'  => false
                ];

        $prev_data = $this->db_operations->get_record('order_employee_trans', $validated);
        if(!empty($prev_data)) return ['msg' => '1. Duplicate Item added.'];
                    
        if(empty($post_data['oet_id'])){   
            $trans_data['oet_id'] = $this->db_operations->data_insert('order_employee_trans', $trans_data);
            if($trans_data['oet_id'] < 1) return ['msg' => '1. Estimate Transaction not added.'];
            $trans_data['isExist'] = false;
        }else{ 
            $trans_data['oet_id']    = $post_data['oet_id'];
        }
        $trans_data['designation_name'] = $this->model->get_name('designation', $trans_data['oet_designation_id']);
        $trans_data['apparel_data'] = $this->add_apparel_transaction($trans_data);
        return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Estimate Transaction added successfully.'];
    }
    public function add_apparel_transaction($temp){ 
        $post_data  = $this->input->post();
        $id         = $post_data['oet_id'];
        // echo "<pre>"; print_r($post_data); exit;
        $apparel_data = $this->model->get_apparel_apparel_data($temp['oet_id'], $temp['oet_apparel_id']);
        if(empty($apparel_data)) return [];
        $resp = [];
        foreach ($apparel_data as $key => $value) {
            $trans_data                     = [];
            $trans_data['oet_oet_id']       = $temp['oet_id'];
            $trans_data['oet_ot_id']        = $temp['oet_ot_id'];

            $trans_data['oet_apparel_id']    = $value['oet_apparel_id'];
            // $trans_data['oet_om_uuid']       = $temp['ot_om_uuid'];
            $trans_data['oet_description']   = $temp['oet_description'];
            $trans_data['oet_delete_status'] = true;
            $trans_data['oet_created_by']    = $_SESSION['user_id'];
            $trans_data['oet_updated_by']    = $_SESSION['user_id'];
            $trans_data['oet_created_at']    = date('Y-m-d H:i:s');
            $trans_data['oet_updated_at']    = date('Y-m-d H:i:s');

            if(empty($post_data['oet_id'])){
                $trans_data['oet_id'] = $this->db_operations->data_insert('order_employee_trans', $trans_data);
                if($trans_data['oet_id'] < 1) return ['msg' => '2. Order employee Transaction not added.'];
                $trans_data['isExist'] = false;
                $trans_data['oet_ot_id'] = 0;
            }else{
                $trans_data['oet_ot_id'] = $id;
                $trans_data['oet_id']    = $value['oet_id'];
            }

            $trans_data['apparel_name'] = $value['apparel_name'];
            array_push($resp, $trans_data);
        }

        return $resp;
    }

    public function get_sku_measurement_data(){ 
        $post_data   = $this->input->post();
        $ids         = isset($post_data['ids']) ? json_decode($post_data['ids'], true) : [];
        // echo "<pre>"; print_r($post_data); exit;
        $data = [];
        foreach ($ids as $key => $value) { 
            $ot_id                  = $post_data['ot_id'];
            $oet_id                 = $value['oet_id'];
            // $customer_id            = $post_data['customer_id'];
            $apparel_id             = $value['apparel_id'];
            $customer_id = $this->model->get_customer_id($ot_id);
            $measurement_data       = $this->model->get_measurement_data($ot_id, $oet_id, $customer_id, $apparel_id);
            $style_data             = $this->model->get_style_data($ot_id, $oet_id, $customer_id, $apparel_id);
            if(empty($measurement_data) && empty($style_data)) return['msg' => '2. Measurement not found.'];
            array_push($data, ['apparel_data' => $value, 'measurement_data' => $measurement_data, 'style_data' => $style_data]);
        }

        return['status' => TRUE, 'data' => $data, 'msg' => 'Measurement fetched successfully.'];
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
        $oet_ids = $this->model->get_ot_id($post_data['_oet_id']);
        // echo "<pre>"; print_r($oet_ids); exit;

        $trans_db_data = $this->model->get_customer_measurement_data($oet_ids);
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
                $oet_id          = $explode[0];
                $apparel_id     = $explode[1];
                $measurement_id = $explode[2];

                $customer_id = $this->model->get_customer_id($post_data['ot_id']);

                $trans_data                         = [];
                $trans_data['cmt_ot_id']            = $post_data['ot_id'];
                // $trans_data['cmt_om_uuid']          = $post_data['om_uuid'];
                $trans_data['cmt_oet_id']            = $oet_id;
                // $trans_data['cmt_bill_no']          = $post_data['omentry_no'];
                // $trans_data['cmt_bill_date']        = $post_data['omentry_date'];
                $trans_data['cmt_customer_id']      = $customer_id;
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
            $update_data['cmt_ot_id']           = $temp['ot_id'];
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
        $oet_ids = $this->model->get_ot_id($post_data['_oet_id']);
        $trans_db_data = $this->model->get_customer_style_data($oet_ids);

        if(!empty($trans_db_data)){
            foreach ($trans_db_data as $key => $value){
                $unique_id  = $value['cst_oet_id'].'_'.$value['cst_apparel_id'].'_'.$value['cst_style_id'];
                if(!isset($post_data['cst_value'][$unique_id])){
                    $result = $this->delete_customer_style_trans(['cst_id' => $value['cst_id']]);
                    if(!isset($result['status'])) return $result;
                }
            }
        }
        
        if(isset($post_data['cst_value']) && !empty($post_data['cst_value'])){
            foreach ($post_data['cst_value'] as $unique_id => $value){
                $explode    = explode('_', $unique_id);
                $oet_id      = $explode[0];
                $apparel_id = $explode[1];
                $style_id   = $explode[2];

                $customer_id = $this->model->get_customer_id($post_data['ot_id']);

                $trans_data                         = [];
                $trans_data['cst_ot_id']            = $post_data['ot_id'];
                // $trans_data['cst_om_uuid']          = $post_data['om_uuid'];
                $trans_data['cst_oet_id']            = $oet_id;
                // $trans_data['cst_bill_no']          = $post_data['om_entry_no'];
                // $trans_data['cst_bill_date']        = $post_data['om_entry_date'];
                $trans_data['cst_customer_id']      = $customer_id; 
                $trans_data['cst_apparel_id']       = $apparel_id;
                $trans_data['cst_style_id']         = $style_id;
                $trans_data['cst_value']            = isset($post_data['cst_value'][$unique_id]);
                $trans_data['cst_updated_by']       = $_SESSION['user_id'];
                $trans_data['cst_updated_at']       = date('Y-m-d H:i:s');
                
                $prev_data = $this->db_operations->get_record('customer_style_trans', ['cst_oet_id' => $trans_data['cst_oet_id'], 'cst_style_id' => $trans_data['cst_style_id']]);
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
            $update_data['cst_ot_id']           = $temp['ot_id'];
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
     public function get_employee_transaction(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
       
        // $id = encrypt_decrypt("decrypt", $id, SECRET_KEY);
        // if(empty($id)) return ['msg' => '1. Id not define.'];   

        $data = $this->model->get_employee_transaction($id);
        if(empty($data)) return['msg' => ucfirst($this->sub_menu).' not found.'];   
        return['status' => TRUE, 'data' => $data, 'msg' => ucfirst($this->sub_menu).' fetched successfully.'];
    }

      
}
?>