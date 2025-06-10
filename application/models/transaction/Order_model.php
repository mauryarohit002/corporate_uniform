<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class order_model extends my_model{
    public function __construct(){ parent::__construct('transaction', 'order'); }
    public function isExist($id){
        $data = $this->db->query("SELECT om_id FROM order_master WHERE om_delete_status = 0 AND om_allocated_amt > 0 AND om_id = $id LIMIT 1")->result_array();
        if(!empty($data)) return true;

        return false; 
    }
    public function isTransExist($id){     
        $query="SELECT om.om_id 
                FROM order_master om
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                WHERE om.om_delete_status = 0 
                AND ot.ot_delete_status = 0
                AND om.om_allocated_amt > 0
                AND ot.ot_id = $id 
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)) return true;
        return false;
    }
    public function isSkuTransExist($id){
        $query="SELECT om.om_id 
                FROM order_master om
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                WHERE om.om_delete_status = 0 
                AND ot.ot_delete_status = 0
                AND om.om_allocated_amt > 0
                AND ot.ot_id = $id 
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)) return true;
        return false;
    }
    public function isBarcodeExist($id){
        // $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_id = $id AND (bm_ot_mtr) > 0 LIMIT 1")->result_array();
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
            $subsql .=" AND om.om_entry_no = '".$_GET['_entry_no']."'";
            $record['filter']['_entry_no']['value'] = $_GET['_entry_no'];
            $record['filter']['_entry_no']['text'] = $_GET['_entry_no'];
        }
        if(isset($_GET['_entry_date_from']) && !empty($_GET['_entry_date_from'])){
            $_entry_date_from = date('Y-m-d', strtotime($_GET['_entry_date_from']));
            $subsql .= " AND om.om_entry_date >= '".$_entry_date_from."'";
        }
        if(isset($_GET['_entry_date_to']) && !empty($_GET['_entry_date_to'])){
            $_entry_date_to = date('Y-m-d', strtotime($_GET['_entry_date_to']));
            $subsql .= " AND om.om_entry_date <= '".$_entry_date_to."'";
        }
        if(isset($_GET['_customer_name']) && !empty($_GET['_customer_name'])){
            $subsql .=" AND customer.customer_name = '".$_GET['_customer_name']."'";
            $record['filter']['_customer_name']['value'] = $_GET['_customer_name'];
            $record['filter']['_customer_name']['text'] = $_GET['_customer_name'];
        }
        
        $query="SELECT om.*,
                UPPER(customer.customer_name) as customer_name
                FROM order_master om
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
                WHERE om.om_delete_status = 0
                $subsql
                ORDER BY om.om_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['om_id']);
            }
        }
          // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_data_for_add(){
        $record['om_entry_no']  = $this->get_max_entry_no(['entry_no' => 'om_entry_no', 'delete_status' => 'om_delete_status', 'fin_year' => 'om_fin_year','branch_id' => 'om_branch_id']);
        $record['om_uuid']      = $_SESSION['user_id'].''.time();
        return $record;
    }
    public function get_data_for_edit($id){ 
        $query="SELECT om.*,
                UPPER(customer.customer_name) as customer_name
                FROM order_master om
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
                WHERE om.om_id = $id
                AND om.om_delete_status = 0";
        $record['master_data'] = $this->db->query($query)->result_array();
        if(!empty($record['master_data'])){
            $record['master_data'][0]['isExist'] = $this->isExist($id);
        }
        // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_transaction($om_id){ 
        $query="SELECT ot.*,
                UPPER(sku.sku_name) as sku_name,
                UPPER(apparel.apparel_name) as apparel_name,
                sku.sku_image
                FROM order_trans ot
                INNER JOIN sku_master sku ON(sku.sku_id = ot.ot_sku_id)
                LEFT JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                WHERE ot.ot_om_id = $om_id
                AND ot.ot_delete_status = 0
                AND ot.ot_trans_type = 'SKU'
                ORDER BY ot.ot_id DESC";
        $record['sku_data'] = $this->db->query($query)->result_array();
        if(!empty($record['sku_data'])){ 
            foreach ($record['sku_data'] as $key => $value) {
                $record['sku_data'][$key]['isExist'] = $this->isSkuTransExist($value['ot_id']);
             
            }
        }
        // echo "<pre>"; print_r($record);die;
        return $record;
    }

    public function get_employee_transaction($ot_id){ 
        $query="SELECT oet.*,
                IFNULL(UPPER(designation.designation_name),'') as designation_name
                FROM order_employee_trans oet
                LEFT JOIN designation_master designation ON(designation.designation_id = oet.oet_designation_id)
                WHERE oet.oet_ot_id = $ot_id
                AND oet.oet_oet_id = 0
                AND oet.oet_delete_status = 0
                ORDER BY oet.oet_id DESC";
        $record = $this->db->query($query)->result_array();
        if(!empty($record)){
            foreach ($record as $key => $value) {
                // $record[$key]['isExist'] = $this->isTransExist($value['oet']);
                $record[$key]['apparel_data']   = $this->get_apparel_transaction($value['oet_id']);
            }
        }
        // echo "<pre>"; print_r($record);die;
        return $record;
       
    }

    public function get_apparel_transaction($oet_id){
        $query="SELECT oet.*,
                UPPER(apparel.apparel_name) as apparel_name
                FROM order_employee_trans oet
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = oet.oet_apparel_id)
                WHERE 1
                AND oet.oet_delete_status = 0
                AND oet.oet_oet_id = $oet_id
                ORDER BY oet.oet_id ASC";
         // print_r($query);die;       
        return $this->db->query($query)->result_array();
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

    // employee trans 

    public function get_apparel_apparel_data($oet_id, $apparel_id){ 
        $query="SELECT 
                oet.oet_id,
                oet.oet_apparel_id,
                UPPER(apparel.apparel_name) as apparel_name
                FROM order_employee_trans oet 
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = oet.oet_apparel_id)
                WHERE 1
                AND oet.oet_delete_status = 0
                AND oet.oet_oet_id = $oet_id";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)) return $data;

        $query="SELECT 
                0 as oet_id,
                aat.aat_apparel_id as oet_apparel_id,
                UPPER(apparel.apparel_name) as apparel_name
                FROM apparel_apparel_trans aat
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = aat.aat_apparel_id)
                WHERE 1
                AND aat.apparel_id = $apparel_id";
        return $this->db->query($query)->result_array();
    }

    public function get_measurement_data($ot_id, $oet_id, $customer_id, $apparel_id){  
        $query="SELECT cmt.cmt_id,
                cmt.cmt_measurement_id,
                UPPER(measurement.measurement_name) as cmt_measurement_name,
                cmt.cmt_value1,
                cmt.cmt_value2,
                measurement_setting.measurement_setting_priority,
                cmt.cmt_bill_no as bill_no,
                DATE_FORMAT(cmt.cmt_bill_date, '%d-%m-%Y') as bill_date,
                IFNULL(UPPER(cmt.cmt_remark), '') as cmt_remark,
                cmt.cmt_oet_id,
                cmt.cmt_apparel_id
                FROM customer_measurement_trans cmt
                INNER JOIN measurement_master measurement ON(measurement.measurement_id = cmt.cmt_measurement_id)
                INNER JOIN  measurement_setting_master  measurement_setting ON(measurement_setting.measurement_setting_measurement_id = cmt.cmt_measurement_id AND measurement_setting.measurement_setting_apparel_id=$apparel_id)
                WHERE cmt.cmt_oet_id = $oet_id
                GROUP BY measurement.measurement_id
                ORDER BY measurement_setting.measurement_setting_priority ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)) return $this->get_all_measurement($oet_id, $apparel_id, $data);

        $latest_data = $this->get_latest_measurement($customer_id,$oet_id, $apparel_id);
        if(!empty($latest_data['bill_no'])){  
            $query="SELECT 0 as cmt_id,
                    cmt.cmt_measurement_id,
                    UPPER(measurement.measurement_name) as cmt_measurement_name,
                    cmt.cmt_value1,
                    cmt.cmt_value2,
                    measurement_setting.measurement_setting_priority, 
                    cmt.cmt_bill_no as bill_no,
                    DATE_FORMAT(cmt.cmt_bill_date, '%d-%m-%Y') as bill_date,
                    IFNULL(UPPER(cmt.cmt_remark), '') as cmt_remark,
                    $oet_id as cmt_oet_id,
                    cmt.cmt_apparel_id
                    FROM customer_measurement_trans cmt
                    INNER JOIN measurement_master measurement ON(measurement.measurement_id = cmt.cmt_measurement_id)
                    INNER JOIN  measurement_setting_master  measurement_setting ON(measurement_setting.measurement_setting_measurement_id = cmt.cmt_measurement_id AND measurement_setting.measurement_setting_apparel_id=$apparel_id)
                    WHERE cmt.cmt_customer_id = $customer_id
                    AND cmt.cmt_oet_id  = $oet_id
                    AND cmt.cmt_apparel_id = $apparel_id
                    AND cmt.cmt_bill_no = '".$latest_data['bill_no']."'
                    AND cmt.cmt_bill_date = '".$latest_data['bill_date']."'
                    AND cmt.cmt_delete_status = 0
                    GROUP BY measurement.measurement_id
                    ORDER BY measurement_setting.measurement_setting_priority ASC";
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($query); exit;
            // echo "<pre>"; print_r($data); exit;
            if(!empty($data)) return $this->get_all_measurement($oet_id, $apparel_id, $data);
        }

        $query="SELECT 0 as cmt_id,
                measurement.measurement_id as cmt_measurement_id,
                UPPER(measurement.measurement_name) as cmt_measurement_name,
                '' as cmt_value1,
                '' as cmt_value2,
                '' as bill_no,
                measurement_setting.measurement_setting_priority ,
                '' as bill_date,
                '' as cmt_remark,
                $oet_id as cmt_oet_id,
                apparel.apparel_id as cmt_apparel_id
                FROM measurement_setting_master measurement_setting
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = measurement_setting.measurement_setting_apparel_id)
                INNER JOIN measurement_master measurement ON(measurement.measurement_id = measurement_setting.measurement_setting_measurement_id)
                WHERE measurement_setting.measurement_setting_apparel_id = $apparel_id
                AND measurement_setting.measurement_setting_deleted_by IS NULL
                ORDER BY measurement_setting.measurement_setting_priority ASC";
        return $this->db->query($query)->result_array();
    }
    public function get_latest_measurement($customer_id,$oet_id, $apparel_id){
        $query="SELECT cmt.cmt_ot_id as ot_id,
                cmt.cmt_oet_id as oet_id,
                cmt.cmt_bill_no as bill_no,
                cmt.cmt_bill_date as bill_date
                FROM customer_measurement_trans cmt
                WHERE 1
                AND cmt.cmt_customer_id = $customer_id
                AND cmt.cmt_oet_id = $oet_id
                AND cmt.cmt_apparel_id = $apparel_id
                ORDER BY cmt.cmt_id DESC
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)) return ['ot_id' => $data[0]['ot_id'], 'oet_id' => $data[0]['oet_id'], 'bill_no' => $data[0]['bill_no'], 'bill_date' => $data[0]['bill_date']];
        return ['ot_id' => '', 'oet_id' => '', 'bill_no' => '', 'bill_date' => ''];
    }
    public function get_all_measurement($oet_id,$apparel_id, $data){
        $ids  = '';
        $subsql='';
        $record=[];
        foreach ($data as $key => $value) {
            array_push($record, $value);
            $ids .= empty($ids) ? $value['cmt_measurement_id'] : ', '.$value['cmt_measurement_id'];
        }
        $subsql .=" AND measurement.measurement_id NOT IN(".$ids.")";

        $query="SELECT 0 as cmt_id,
                measurement.measurement_id as cmt_measurement_id,
                UPPER(measurement.measurement_name) as cmt_measurement_name,
                 measurement_setting.measurement_setting_priority ,
                '' as cmt_value1,
                '' as cmt_value2,
               '' as bill_no,
                '' as bill_date,
                '' as cmt_remark,
                $oet_id as cmt_oet_id,
                $apparel_id as cmt_apparel_id
                FROM measurement_setting_master measurement_setting
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = measurement_setting.measurement_setting_apparel_id)
                INNER JOIN measurement_master measurement ON(measurement.measurement_id = measurement_setting.measurement_setting_measurement_id)
                WHERE measurement_setting.measurement_setting_apparel_id = $apparel_id
                AND measurement_setting.measurement_setting_deleted_by IS NULL
                $subsql
                GROUP BY measurement.measurement_id
                ORDER BY measurement_setting.measurement_setting_priority ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)){
            foreach ($data as $key => $value) {
                array_push($record, $value);
            }
        }
        if(!empty($record)){
            usort($record, function($a, $b){
                 return $a['measurement_setting_priority'] - $b['measurement_setting_priority'];
            });
        }
        return $record;
    }

    public function get_style_data($ot_id, $oet_id, $customer_id, $apparel_id){
        $query="SELECT cst.cst_id,
                cst.cst_style_id,
                UPPER(style.style_name) as cst_style_name,
                cst.cst_value,
                cst.cst_bill_no as bill_no,
                DATE_FORMAT(cst.cst_bill_date, '%d-%m-%Y') as bill_date,
                cst.cst_oet_id,
                cst.cst_apparel_id
                FROM customer_style_trans cst
                INNER JOIN style_master style ON(style.style_id = cst.cst_style_id)
                WHERE cst.cst_oet_id = $oet_id
                ORDER BY style.style_id ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;

        if(!empty($data)) return $this->get_all_style($oet_id, $apparel_id, $data);
    
        $latest_data = empty($ot_id) ? $this->get_latest_style($customer_id,$oet_id, $apparel_id) : [];
        // echo "<pre>"; print_r($latest_data); exit;
        if(!empty($latest_data['bill_no'])){
            $subsql = " AND cst.cst_bill_no = '".$latest_data['bill_no']."'";
            $subsql.= " AND cst.cst_bill_date = '".$latest_data['bill_date']."'";
            if(!empty($latest_data['ot_id'])) $subsql = " AND cst.cst_ot_id = '".$latest_data['ot_id']."'";
            $query="SELECT 
                    cst.cst_id,
                    cst.cst_style_id,
                    UPPER(style.style_name) as cst_style_name,
                    cst.cst_value,
                    cst.cst_bill_no as bill_no,
                    DATE_FORMAT(cst.cst_bill_date, '%d-%m-%Y') as bill_date,
                    $oet_id as cst_oet_id,
                    cst.cst_apparel_id
                    FROM customer_style_trans cst
                    INNER JOIN style_master style ON(style.style_id = cst.cst_style_id)
                    WHERE 1
                    AND cst.cst_customer_id = $customer_id
                    AND cst.cst_oet_id = $oet_id
                    AND cst.cst_apparel_id = $apparel_id
                    $subsql
                    GROUP BY style.style_id
                    ORDER BY style.style_id ASC";
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($query); exit;
            // echo "<pre>"; print_r($data); exit;
            if(!empty($data)) return $this->get_all_style($oet_id, $apparel_id, $data);
        }
    
       $query="SELECT 
                0 as cst_id,
                style.style_id as cst_style_id,
                UPPER(style.style_name) as cst_style_name,
                '' as cst_value,
                '' as bill_no,
                '' as bill_date,
                $oet_id as cst_oet_id,
                apparel.apparel_id as cst_apparel_id
                FROM style_setting_master style_setting
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = style_setting.style_setting_apparel_id)
                INNER JOIN style_master style ON(style.style_id = style_setting.style_setting_style_id)
                WHERE style_setting.style_setting_status = 1
                AND style_setting.style_setting_apparel_id = $apparel_id
                ORDER BY style_setting.style_setting_id ASC";
            // echo "<pre>"; print_r($query); exit;    
        return $this->db->query($query)->result_array();
    }

    public function get_all_style($oet_id, $apparel_id, $data){
        $ids  = '';
        $subsql='';
        $record=[];
        foreach ($data as $key => $value) {
            array_push($record, $value);
            $ids .= empty($ids) ? $value['cst_style_id'] : ', '.$value['cst_style_id'];
        }
        $subsql .=" AND style.style_id NOT IN(".$ids.")";

        $query="SELECT 0 as cst_id,
                style.style_id as cst_style_id,
                UPPER(style.style_name) as cst_style_name,
                '' as cst_value,
                '' as bill_no,
                '' as bill_date,
                $oet_id as cst_oet_id,
                $apparel_id as cst_apparel_id
                FROM style_setting_master style_setting
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = style_setting.style_setting_apparel_id)
                INNER JOIN style_master style ON(style.style_id = style_setting.style_setting_style_id)
                WHERE style_setting.style_setting_status = 1
                AND style_setting.style_setting_apparel_id = $apparel_id
                $subsql
                ORDER BY style_setting.style_setting_id ASC";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)){
            foreach ($data as $key => $value) {
                array_push($record, $value);
            }
        }
        usort($record, function($a, $b){
            return $a['cst_style_name'] > $b['cst_style_name'];
        });
        return $record;
    }

    public function get_latest_style($customer_id,$oet_id, $apparel_id){
        $query="SELECT cst.cst_ot_id as ot_id,
                cst.cst_oet_id as oet_id,
                cst.cst_bill_no as bill_no,
                cst.cst_bill_date as bill_date
                FROM customer_style_trans cst
                WHERE 1
                AND cst.cst_customer_id = $customer_id
                AND cst.cst_oet_id = $oet_id
                AND cst.cst_apparel_id = $apparel_id
                ORDER BY cst.cst_id DESC
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)) return ['ot_id' => $data[0]['ot_id'], 'oet_id' => $data[0]['oet_id'], 'bill_no' => $data[0]['bill_no'], 'bill_date' => $data[0]['bill_date']];
        return ['ot_id' => '', 'oet_id' => '', 'bill_no' => '', 'bill_date' => ''];
    }
    public function get_customer_id($ot_id){
        $query="SELECT om.om_customer_id
                FROM order_master om
                INNER JOIN order_trans ot ON(om.om_id = ot.ot_om_id)
                WHERE ot.ot_id = $ot_id";
        $data = $this->db->query($query)->result_array();
        return (!empty($data)) ? $data[0]['om_customer_id'] : 0;
    }

    public function get_customer_measurement_data($ids){
        $query="SELECT cmt.*
                FROM customer_measurement_trans cmt
                WHERE cmt.cmt_oet_id IN ($ids)";
        // echo "<pre>"; print_r($query); exit;
        return $this->db->query($query)->result_array();
    }
    public function get_customer_style_data($ids){
        $query="SELECT cst.*
                FROM customer_style_trans cst
                WHERE cst.cst_value = 1
                AND cst.cst_oet_id IN ($ids)";
        // echo "<pre>"; print_r($query); exit;
        return $this->db->query($query)->result_array();
    }

    public function get_ot_id($oet_id){
        $query="SELECT oet.oet_id as id
                FROM order_employee_trans oet
                WHERE oet.oet_apparel_id > 0
                AND oet.oet_oet_id = $oet_id
                ORDER BY oet.oet_id ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        $id = $oet_id;
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $id = empty($id) ? $value['id'] : $id.', '.$value['id'];
            }
        }
        return $id;
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
            $subsql .= " AND (om.om_entry_no LIKE '%".$name."%') ";
        }
        $query="SELECT om.om_entry_no as id, UPPER(om.om_entry_no) as name
                FROM order_master om
                WHERE 1
                $subsql
                GROUP BY om.om_entry_no ASC
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
                FROM order_master om
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
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