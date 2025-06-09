<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class quotation extends my_controller{  
    protected $menu;
    protected $sub_menu;
    public function __construct(){
        $this->menu     = 'transaction'; 
        $this->sub_menu = 'quotation'; 
        parent::__construct($this->menu, $this->sub_menu);
    }

    public function remove(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        $result     = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
        if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

        $this->db->trans_begin();

            $result = $this->delete_trans(['qt_qm_id' => $id,'qt_delete_status' => false]);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }

            $result = $this->delete_master(['qm_id' => $id, 'qm_delete_status' => false]);
            if(!isset($result['status'])){
                $this->db->trans_rollback();
                return $result;
            }

            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                return ['msg' => '3. Transaction Rollback.'];
            }
        $this->db->trans_commit();

        return ['status' => TRUE, 'msg' => 'quotation deleted successfully'];
    }

    public function delete_master($clause){  
        $data = $this->db_operations->get_record($this->sub_menu.'_master', $clause);
        if(empty($data)) return ['msg' => '3. quotation not found.'];
        foreach ($data as $key => $value){
            if($this->model->isExist($value['qm_id'])) return ['msg' => '1. Not allowed to delete.'];
            $prev_data = $this->db_operations->get_record('quotation_master', ['qm_id' => $value['qm_id']]);
            if(empty($prev_data)) return ['msg' => '1. Quotation not found.'];
            $update_data                        = [];
            $update_data['qm_entry_no']         = $data[0]['qm_entry_no'].''.$value['qm_id']; 
            $update_data['qm_delete_status']    = true; 
            $update_data['qm_updated_by']       = $_SESSION['user_id']; 
            $update_data['qm_updated_at']       = date('Y-m-d H:i:s');
            if($this->db_operations->data_update($this->sub_menu.'_master', $update_data, 'qm_id', $value['qm_id']) < 1) return ['msg' => '1. quotation not deleted.'];
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

    // quotation_master
    public function add_edit(){ 
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
          
            $post_data['sku_trans_data']= isset($post_data['sku_trans_data']) ? json_decode($post_data['sku_trans_data'], true) : [];
            if(empty($post_data['trans_data']) && empty($post_data['sku_trans_data'])) return ['msg' => '1. Item not aded in list.'];
            // echo "<pre>"; print_r($post_data); exit;
            // master_data
                $master_data['qm_uuid']                     = trim($post_data['qm_uuid']);
                $master_data['qm_entry_no']                 = trim($post_data['qm_entry_no']);
                $master_data['qm_entry_date']               = date('Y-m-d', strtotime($post_data['qm_entry_date']));
                $master_data['qm_customer_id']              = isset($post_data['qm_customer_id'])?trim($post_data['qm_customer_id']):0;
                $master_data['qm_gst_type']                 = trim($post_data['qm_gst_type']);
                $master_data['qm_bill_type']                = isset($post_data['qm_bill_type']);
                $master_data['qm_notes']                    = trim($post_data['qm_notes']);
                $master_data['qm_total_qty']                = trim($post_data['qm_total_qty']);
                $master_data['qm_sub_amt']                  = trim($post_data['qm_sub_amt']);
                $master_data['qm_disc_amt']                 = trim($post_data['qm_disc_amt']);
                $master_data['qm_taxable_amt']              = trim($post_data['qm_taxable_amt']);
                $master_data['qm_sgst_amt']                 = trim($post_data['qm_sgst_amt']);
                $master_data['qm_cgst_amt']                 = trim($post_data['qm_cgst_amt']);
                $master_data['qm_igst_amt']                 = trim($post_data['qm_igst_amt']);
                $master_data['qm_bill_disc_per']            = trim($post_data['qm_bill_disc_per']);
                $master_data['qm_bill_disc_amt']            = trim($post_data['qm_bill_disc_amt']);
                $master_data['qm_round_off']                = trim($post_data['qm_round_off']);
                $master_data['qm_total_amt']                = trim($post_data['qm_total_amt']);
                $master_data['qm_updated_by']               = $_SESSION['user_id'];
            // master_data
                $cnt =$this->db_operations->get_cnt('quotation_master',['qm_id!='=>$id,'qm_entry_no'=>$master_data['qm_entry_no'],'qm_fin_year'=>$_SESSION['fin_year'],'qm_delete_status'=>0]);
                if($cnt>0){
                     return ['msg' => 'Duplicate Entry No found!!'];
                }

            $this->db->trans_begin();
            if($id == 0){
                // $master_data['qm_entry_no']  = $this->model->get_max_entry_no(['entry_no' => 'qm_entry_no', 'delete_status' => 'qm_delete_status', 'fin_year' => 'qm_fin_year']);
                $master_data['qm_created_by']   = $_SESSION['user_id'];
                $master_data['qm_created_at']   = date('Y-m-d H:i:s');
                $master_data['qm_fin_year']     = $_SESSION['fin_year'];
                $master_data['qm_branch_id']    = $_SESSION['user_branch_id'];
                $uuidExist                      = $this->db_operations->get_cnt($this->sub_menu.'_master', ['qm_uuid' => $master_data['qm_uuid']]);
                if($uuidExist > 0){
                    $this->db->trans_rollback();
                    return ['msg' => 'Form already submited.'];
                }
                $id = $this->db_operations->data_insert('quotation_master', $master_data);
                $msg = 'quotation added successfully.';
                if($id < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. quotation not added.'];
                }
            }else{
                $prev_data = $this->db_operations->get_record('quotation_master', ['qm_id' => $id, 'qm_delete_status' => false]);
                if(empty($prev_data)){
                    $this->db->trans_rollback();
                    return ['status' => REFRESH, 'msg' => '1. quotation not found.'];
                }
                $msg = 'quotation updated successfully.';
                if($this->db_operations->data_update('quotation_master', $master_data, 'qm_id', $id) < 1){
                    $this->db->trans_rollback();
                    return ['msg' => '1. quotation not updated.'];
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
            $data['name']   = strtoupper($master_data['qm_entry_no']);
            return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
    }
    // quotation_master
    public function add_update_sku_trans($post_data, $id){
             
                $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['qt_qm_id' => $id, 'qt_delete_status' => false, 'qt_trans_type' => 'SKU']);
                $ids    = $this->get_id($post_data['sku_trans_data'], 'qt_id');
                if(!empty($trans_db_data)){
                    foreach ($trans_db_data as $key => $value){
                        if(!in_array($value['qt_id'], $ids)){
                            $result = $this->delete_trans(['qt_id' => $value['qt_id'], 'qt_delete_status' => false, 'qt_trans_type' => 'SKU']);
                            if(!isset($result['status'])) return $result;
                        }
                    }
                } 
                 // echo "<pre>"; print_r($post_data);die;
                foreach ($post_data['sku_trans_data'] as $key => $value){  
                    // trans_data
                        $trans_data                         = [];
                        $trans_data['qt_qm_id']             = $id;
                        $trans_data['qt_trans_type']        = $value['qt_trans_type'];
                        $trans_data['qt_sku_id']            = $value['qt_sku_id'];
                        $trans_data['qt_qty']               = $value['qt_qty'];
                        $trans_data['qt_rate']              = $value['qt_rate'];
                        $trans_data['qt_amt']               = $value['qt_amt'];

                        $trans_data['qt_disc_per']          = $value['qt_disc_per'];
                        $trans_data['qt_disc_amt']          = $value['qt_disc_amt'];
                        $trans_data['qt_taxable_amt']       = $value['qt_taxable_amt'];
                        $trans_data['qt_sgst_per']          = $value['qt_sgst_per'];
                        $trans_data['qt_sgst_amt']          = $value['qt_sgst_amt'];
                        $trans_data['qt_cgst_per']          = $value['qt_cgst_per'];
                        $trans_data['qt_cgst_amt']          = $value['qt_cgst_amt'];
                        $trans_data['qt_igst_per']          = $value['qt_igst_per'];
                        $trans_data['qt_igst_amt']          = $value['qt_igst_amt'];
                        $trans_data['qt_total_amt']         = $value['qt_total_amt'];
                        $trans_data['qt_description']       = $value['qt_description'];
                        $trans_data['qt_delete_status']     = false;
                        $trans_data['qt_updated_by']        = $_SESSION['user_id'];
                        $trans_data['qt_updated_at']        = date('Y-m-d H:i:s');
                    // trans_data

                    if(!empty($value['qt_id'])){
                        $prev_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['qt_id' => $value['qt_id']]);
                        if(empty($prev_data)) return ['msg' => '2. Transaction not found.'];
                        if($this->db_operations->data_update($this->sub_menu.'_trans', $trans_data, 'qt_id', $value['qt_id']) < 1) return ['msg' => '2. Transaction not updated.'];
                    }
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
           
            $trans_data                         = [];
            $trans_data['qt_trans_type']        = 'SKU';
            $trans_data['qt_qm_uuid']           = $post_data['qm_uuid'];
            $trans_data['qt_sku_id']            = $post_data['sku_id'];
            $trans_data['qt_qty']               = $post_data['sku_qty'];
            $trans_data['qt_rate']               = $post_data['sku_rate'];
            $trans_data['qt_amt']               = $post_data['sku_amt']; 
            $trans_data['qt_disc_per']          = $post_data['sku_disc_per']; 
            $trans_data['qt_disc_amt']          = $post_data['sku_disc_amt']; 
            $trans_data['qt_taxable_amt']       = $post_data['sku_taxable_amt']; 
            $trans_data['qt_sgst_per']          = $post_data['sku_sgst_per'];
            $trans_data['qt_cgst_per']          = $post_data['sku_cgst_per']; 
            $trans_data['qt_igst_per']          = $post_data['sku_igst_per'];
            $trans_data['qt_sgst_amt']          = $post_data['sku_sgst_amt']; 
            $trans_data['qt_cgst_amt']          = $post_data['sku_cgst_amt']; 
            $trans_data['qt_igst_amt']          = $post_data['sku_igst_amt']; 
            $trans_data['qt_total_amt']         = $post_data['sku_total_amt']; 
            $trans_data['qt_description']       = $post_data['sku_description'];
            $trans_data['qt_delete_status']     = true;
            $trans_data['qt_created_by']        = $_SESSION['user_id'];
            $trans_data['qt_updated_by']        = $_SESSION['user_id'];
            $trans_data['qt_created_at']        = date('Y-m-d H:i:s');
            $trans_data['qt_updated_at']        = date('Y-m-d H:i:s');
            if(empty($post_data['qt_id'])){
                $trans_data['qt_id'] = $this->db_operations->data_insert($this->sub_menu.'_trans', $trans_data);
                if($trans_data['qt_id'] < 1) return ['msg' => '1. Sku Design Transaction not added.'];
                $trans_data['isExist'] = false;
                $trans_data['qt_qm_id'] = 0;
            }else{
                $trans_data['qt_qm_id'] = $id;
                $trans_data['qt_id']    = $post_data['qt_id'];
            }

            $trans_data['sku_name'] = $this->model->get_name('sku', $trans_data['qt_sku_id']);
             // echo "<pre>"; print_r($result['data']); exit;
            return ['status' => TRUE, 'data' => $trans_data, 'msg' => 'quotation Sku Transaction added successfully.'];
        }
           

        public function delete_trans($clause){ 
            $data = $this->db_operations->get_record($this->sub_menu.'_trans', $clause);
            // echo "<pre>"; print_r($data); exit;
            if(empty($data)) return ['status' => TRUE];

            foreach ($data as $key => $value){ 
                $child_data = $this->db_operations->get_record($this->sub_menu.'_trans', ['qt_id' => $value['qt_id'], 'qt_delete_status' => false]);
                $update_data                        = [];
                $update_data['qt_delete_status']    = true; 
                $update_data['qt_updated_by']       = $_SESSION['user_id']; 
                $update_data['qt_updated_at']       = date('Y-m-d H:i:s'); 
                if($this->db_operations->data_update($this->sub_menu.'_trans', $update_data, 'qt_id', $value['qt_id']) < 1) return ['msg' => '2. Transaction not deleted.'];
            }
            return ['status' => TRUE];
        }

      
}
?>