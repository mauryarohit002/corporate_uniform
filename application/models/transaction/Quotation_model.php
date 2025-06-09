<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class quotation_model extends my_model{
    public function __construct(){ parent::__construct('transaction', 'quotation'); }
    public function isExist($id){
        $data = $this->db->query("SELECT qm_id FROM quotation_master WHERE qm_delete_status = 0 AND qm_allocated_amt > 0 AND qm_id = $id LIMIT 1")->result_array();
        if(!empty($data)) return true;

        return false; 
    }
    public function isTransExist($id){     
        $query="SELECT qm.qm_id 
                FROM quotation_master qm
                INNER JOIN quotation_trans qt ON(qt.qt_qm_id = qm.qm_id)
                WHERE qm.qm_delete_status = 0 
                AND qt.qt_delete_status = 0
                AND qm.qm_allocated_amt > 0
                AND qt.qt_id = $id 
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)) return true;
        return false;
    }
    public function isSkuTransExist($id){
        $query="SELECT qm.qm_id 
                FROM quotation_master qm
                INNER JOIN quotation_trans qt ON(qt.qt_qm_id = qm.qm_id)
                WHERE qm.qm_delete_status = 0 
                AND qt.qt_delete_status = 0
                AND qm.qm_allocated_amt > 0
                AND qt.qt_id = $id 
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)) return true;
        return false;
    }
    public function isBarcodeExist($id){
        // $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_id = $id AND (bm_qt_mtr) > 0 LIMIT 1")->result_array();
        // if(!empty($data)) return true;
        
        return false;
    }
    public function get_list($wantCount, $per_page = 20, $offset = 0){
        $record     = [];
        $subsql     = '';
        $limit      = '';
        $ofset      = '';
        
        if(!$wantCount){
            $limit .= " LIMIT $per_page";
            $ofset .= " OFFSET $offset";
        }
        
        if(isset($_GET['_entry_no']) && !empty($_GET['_entry_no'])){
            $subsql .=" AND qm.qm_entry_no = '".$_GET['_entry_no']."'";
            $record['filter']['_entry_no']['value'] = $_GET['_entry_no'];
            $record['filter']['_entry_no']['text'] = $_GET['_entry_no'];
        }
        if(isset($_GET['_entry_date_from']) && !empty($_GET['_entry_date_from'])){
            $_entry_date_from = date('Y-m-d', strtotime($_GET['_entry_date_from']));
            $subsql .= " AND qm.qm_entry_date >= '".$_entry_date_frqm."'";
        }
        if(isset($_GET['_entry_date_to']) && !empty($_GET['_entry_date_to'])){
            $_entry_date_to = date('Y-m-d', strtotime($_GET['_entry_date_to']));
            $subsql .= " AND qm.qm_entry_date <= '".$_entry_date_to."'";
        }
        if(isset($_GET['_customer_name']) && !empty($_GET['_customer_name'])){
            $subsql .=" AND customer.customer_name = '".$_GET['_customer_name']."'";
            $record['filter']['_customer_name']['value'] = $_GET['_customer_name'];
            $record['filter']['_customer_name']['text'] = $_GET['_customer_name'];
        }
        
        $query="SELECT qm.*,
                UPPER(customer.customer_name) as customer_name
                FROM quotation_master qm
                INNER JOIN customer_master customer ON(customer.customer_id = qm.qm_customer_id)
                WHERE qm.qm_delete_status = 0
                $subsql
                ORDER BY qm.qm_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['qm_id']);
            }
        }
          // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_data_for_add(){
        $record['qm_entry_no']  = $this->get_max_entry_no(['entry_no' => 'qm_entry_no', 'delete_status' => 'qm_delete_status', 'fin_year' => 'qm_fin_year','branch_id' => 'qm_branch_id']);
        $record['qm_uuid']      = $_SESSION['user_id'].''.time();
        return $record;
    }
    public function get_data_for_edit($id){ 
        $query="SELECT qm.*,
                UPPER(customer.customer_name) as customer_name
                FROM quotation_master qm
                INNER JOIN customer_master customer ON(customer.customer_id = qm.qm_customer_id)
                WHERE qm.qm_id = $id
                AND qm.qm_delete_status = 0";
        $record['master_data'] = $this->db->query($query)->result_array();
        if(!empty($record['master_data'])){
            $record['master_data'][0]['isExist'] = $this->isExist($id);
        }
        // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_transaction($qm_id){ 
        $query="SELECT qt.*,
                UPPER(sku.sku_name) as sku_name,
                sku.sku_image
                FROM quotation_trans qt
                INNER JOIN sku_master sku ON(sku.sku_id = qt.qt_sku_id)
                WHERE qt.qt_qm_id = $qm_id
                AND qt.qt_delete_status = 0
                AND qt.qt_trans_type = 'SKU'
                ORDER BY qt.qt_id DESC";
        $record['sku_data'] = $this->db->query($query)->result_array();
        if(!empty($record['sku_data'])){
            foreach ($record['sku_data'] as $key => $value) {
                $record['sku_data'][$key]['isExist'] = $this->isSkuTransExist($value['qt_id']);
             
            }
        }
        // echo "<pre>"; print_r($record);die;
        return $record;
    }
    
    public function get_customer_data($id){
        $query="SELECT customer.*,
                0 as gst_type,
                IFNULL(UPPER(city.city_name), '') as city_name,
                IFNULL(UPPER(state.state_name), '') as state_name,
                IFNULL(UPPER(country.country_name), '') as country_name
                FROM customer_master customer
                LEFT JOIN city_master city ON(city.city_id = customer.customer_city_id)
                LEFT JOIN state_master state ON(state.state_id = customer.customer_state_id)
                LEFT JOIN country_master country ON(country.country_id = customer.customer_country_id)
                WHERE customer.customer_id = $id";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query);
        // echo "<pre>"; print_r($data);exit;
        if(!empty($data)){
            $state_data = $this->get_state();
            if(!empty($state_data)){
                $data[0]['gst_type'] = ($state_data[0]['state_id'] == $data[0]['customer_state_id']) ? 0 : 1;
            } 
        }
        return $data;
    }
    public function get_sku_data($id){
        $query="SELECT 
                sku.sku_id,
                UPPER(sku.sku_name) as sku_name,
                sku.sku_mrp as mrp,
                sku.sku_image as image
                FROM sku_master sku
                LEFT JOIN sku_design_trans sdt ON(sdt.sdt_sku_id = sku.sku_id)
                WHERE 1
                AND sku.sku_delete_status = 0
                AND sdt.sdt_delete_status = 0
                AND sku.sku_id = $id
                LIMIT 1";
        return $this->db->query($query)->result_array();
    }

    public function get_name($term, $id){
        $query="SELECT UPPER(".$term."_name) as name FROM ".$term."_master WHERE ".$term."_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? '' : $data[0]['name'];
    }
   
    public function _entry_no(){
        $subsql = "";
        $limit  = PER_PAGE;
        $offset = OFFSET;
        $page   = 1;
        if(isset($_GET['limit']) && !empty($_GET['limit'])){
            $limit = $_GET['limit'];
        }
        if(isset($_GET['page']) && !empty($_GET['page'])){
            $page   = $_GET['page'];
            $offset = $limit * ($page - 1);
        }
        if(isset($_GET['name']) && !empty($_GET['name'])){
            $name   = $_GET['name'];
            $subsql .= " AND (qm.qm_entry_no LIKE '%".$name."%') ";
        }
        $query="SELECT qm.qm_entry_no as id, UPPER(qm.qm_entry_no) as name
                FROM quotation_master qm
                WHERE 1
                $subsql
                GROUP BY qm.qm_entry_no ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _customer_name(){
        $subsql = "";
        $limit  = PER_PAGE;
        $offset = OFFSET;
        $page   = 1;
        if(isset($_GET['limit']) && !empty($_GET['limit'])){
            $limit = $_GET['limit'];
        }
        if(isset($_GET['page']) && !empty($_GET['page'])){
            $page   = $_GET['page'];
            $offset = $limit * ($page - 1);
        }
        if(isset($_GET['name']) && !empty($_GET['name'])){
            $name   = $_GET['name'];
            $subsql .= " AND (customer.customer_name LIKE '%".$name."%') ";
        }
        $query="SELECT customer.customer_name as id, UPPER(customer.customer_name) as name
                FROM quotation_master qm
                INNER JOIN customer_master customer ON(customer.customer_id = qm.qm_customer_id)
                WHERE 1
                $subsql
                GROUP BY customer.customer_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
   
}
?>