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
        $record 	= [];
        $subsql 	= '';
        $limit  	= '';
        $ofset  	= '';
        
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
            $subsql .=" AND billing.customer_name = '".$_GET['_customer_name']."'";
            $record['filter']['_customer_name']['value'] = $_GET['_customer_name'];
            $record['filter']['_customer_name']['text'] = $_GET['_customer_name'];
        }
        
        $query="SELECT om.*,
                UPPER(billing.customer_name) as billing_name
                FROM order_master om
                INNER JOIN customer_master billing ON(billing.customer_id = om.om_billing_id)
                WHERE om.om_delete_status = 0 AND om.om_status = 1 
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
        return $record;
    }
    public function get_data_for_add(){
        $record['om_entry_no'] 	= $this->get_max_entry_no(['entry_no' => 'om_entry_no', 'delete_status' => 'om_delete_status', 'fin_year' => 'om_fin_year','branch_id' => 'om_branch_id']);
        $record['om_uuid'] 	    = $_SESSION['user_id'].''.time();
        return $record;
    }
    public function get_data_for_edit($id){
        $query="SELECT om.*,
                UPPER(billing.customer_name) as billing_name,
                UPPER(customer.customer_name) as customer_name,
                UPPER(user.user_fullname) as salesman_name,
                 UPPER(master.user_fullname) as master_name
                FROM order_master om
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
                INNER JOIN customer_master billing ON(billing.customer_id = om.om_billing_id)
                LEFT JOIN user_master user ON(user.user_id = om.om_salesman_id)
                LEFT JOIN user_master master ON(master.user_id = om.om_master_id)
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
                IFNULL(UPPER(apparel.apparel_name), '') as apparel_name,
                IFNULL(UPPER(design.design_name), '') as design_name,
                IFNULL(UPPER(bm.bm_item_code), (IFNULL(UPPER(brmm.brmm_item_code),''))) as item_code
                FROM order_trans ot
                LEFT JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                LEFT JOIN barcode_master bm ON(bm.bm_id = ot.ot_bm_id)
                LEFT JOIN barcode_readymade_master brmm ON(brmm.brmm_id = ot.ot_brmm_id)
                LEFT JOIN design_master design ON(design.design_id = ot.ot_design_id)
                WHERE ot.ot_om_id = $om_id
                AND ot.ot_delete_status = 0
                AND ot.ot_ot_id = 0
                AND ot.ot_trans_type != 'SKU'
                ORDER BY ot.ot_id DESC";
        $record['package_data'] = $this->db->query($query)->result_array();
        if(!empty($record['package_data'])){
            foreach ($record['package_data'] as $key => $value) {
                $record['package_data'][$key]['isExist'] = $this->isTransExist($value['ot_id']);
                $record['package_data'][$key]['apparel_data']   = $this->get_apparel_transaction($value['ot_id']);
            }
        }
        
        $query="SELECT ot.*,
                UPPER(sku.sku_name) as sku_name,
                UPPER(apparel.apparel_name) as apparel_name,
                sku.sku_image
                FROM order_trans ot
                INNER JOIN sku_master sku ON(sku.sku_id = ot.ot_sku_id)
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                WHERE ot.ot_om_id = $om_id
                AND ot.ot_delete_status = 0
                AND ot.ot_apparel_id > 0
                AND ot.ot_trans_type = 'SKU'
                ORDER BY ot.ot_id DESC";
        $record['sku_data'] = $this->db->query($query)->result_array();
        if(!empty($record['sku_data'])){
            foreach ($record['sku_data'] as $key => $value) {
                $record['sku_data'][$key]['isExist']        = $this->isSkuTransExist($value['ot_id']);
                $record['sku_data'][$key]['design_data']    = empty($value['ot_ot_id']) ? $this->get_sku_fabric_trans_data($value['ot_id']) : [];
                $record['sku_data'][$key]['apparels']       = empty($value['ot_ot_id']) ? $this->get_apparels($value['ot_sku_id']) : [];
                $record['sku_data'][$key]['branch_default'] = $_SESSION['branch_default'];
            }
        }
        // echo "<pre>"; print_r($record);die;
        return $record;
    }

    public function get_apparel_transaction($ot_id){
        $query="SELECT ot.*,
                UPPER(apparel.apparel_name) as apparel_name
                FROM order_trans ot
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                WHERE 1
                AND ot.ot_delete_status = 0
                AND ot.ot_ot_id = $ot_id
                ORDER BY ot.ot_id ASC";
        return $this->db->query($query)->result_array();
    }


    public function get_sku_design_data($id){
        $query="SELECT 
                UUID_SHORT() as uuid,
                'SKU' as ot_trans_type,
                0 as ot_id,
                sdt.sdt_id as ot_sdt_id,
                bm.bm_id as ot_bm_id,
                sdt.sdt_rate as ot_fabric_rate,
                sdt.sdt_mtr as ot_fabric_mtr,
                sdt.sdt_amt as ot_fabric_amt,
                0 as bal_qty,
                0 as isErrorExist,
                UPPER(sku.sku_name) as sku_name,
                UPPER(design.design_name) as design_name,
                design.design_image,
                design.design_id
                FROM sku_master sku
                INNER JOIN sku_design_trans sdt ON(sdt.sdt_sku_id = sku.sku_id)
                INNER JOIN barcode_master bm ON(bm.bm_id = sdt.sdt_bm_id)
                INNER JOIN design_master design ON(design.design_id = sdt.sdt_design_id)
                WHERE 1
                AND sku.sku_delete_status = 0
                AND sdt.sdt_delete_status = 0
                AND bm.bm_delete_status = 0
                AND sku.sku_id = $id
                ORDER BY design.design_name ASC";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $temp                       = $this->get_barcode_data_by_design($value);
                $data[$key]['bal_qty']      = $temp['bal_mtr'];
                $data[$key]['isErrorExist'] = ($value['ot_fabric_mtr'] > $data[$key]['bal_qty']) ? 1 : 0;
                $data[$key]['qrcode_data']  = $temp['qrcode_data'];
            }
        }
        return $data;
    }
    public function get_barcode_data_by_design($value){
        $bm_id      = $value['ot_bm_id'];
        $design_id  = $value['design_id'];
        $defined_mtr= $value['ot_fabric_mtr'];
        $bal_mtr    = 0;
        $quer1="SELECT 
                0 as ot_id,
                bm.bm_id as ot_bm_id,
                0 as ot_fabric_mtr,
                bm.bm_roll_no as qrcode,
                (((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_ott_mtr + bm.bm_et_mtr + bm.bm_prt_mtr)) + bm.bm_am_mtr)) as avail_mtr,
                0 as bal_mtr,
                1 checked,
                -1 as sr_no
                FROM barcode_master bm
                WHERE bm.bm_delete_status = 0 
                AND bm.bm_id = $bm_id";
        $quer2="SELECT 
                0 as ot_id,
                bm.bm_id as ot_bm_id,
                0 as ot_fabric_mtr,
                bm.bm_roll_no as qrcode,
                (((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_ott_mtr + bm.bm_et_mtr + bm.bm_prt_mtr)) + bm.bm_am_mtr)) as avail_mtr,
                (((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_ott_mtr + bm.bm_et_mtr + bm.bm_prt_mtr)) + bm.bm_am_mtr)) as bal_mtr,
                1 checked,
                bm.bm_id as sr_no
                FROM barcode_master bm
                WHERE bm.bm_delete_status = 0 
                AND bm.bm_id NOT IN ($bm_id)
                AND bm.bm_design_id = $design_id";
        $query="SELECT merge.*
                FROM ($quer1 UNION $quer2) as merge
                WHERE 1
                ORDER BY merge.sr_no ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        $record = [];
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $avail_mtr = $value['avail_mtr'];
                if($defined_mtr > 0){
                    $assign_mtr = 0;
                    if($avail_mtr > 0 && $avail_mtr <= 2){
                        $margin     = floatval(($avail_mtr * 25) / 100);
                        $avail_mtr  = floatval($avail_mtr + $margin);
                    }
    
                    if($avail_mtr > 0){
                        if($avail_mtr > $defined_mtr){
                            $assign_mtr = $defined_mtr;
                        }else{
                            $assign_mtr = $avail_mtr;
                        }
                    }
                    $value['avail_mtr']    = round($avail_mtr, 2);
                    $value['ot_fabric_mtr']= round($assign_mtr, 2);
                    $value['bal_mtr']      = round(($avail_mtr - $assign_mtr),2);
                    $defined_mtr           = round(($defined_mtr - $assign_mtr),2);
                }
                if($avail_mtr > 0) array_push($record, $value);
                $bal_mtr = round(($bal_mtr + $avail_mtr), 2);
            }
        }
        return ['bal_mtr' => $bal_mtr, 'qrcode_data' => $record];
    }
    public function get_sku_fabric_trans_data($id){
        $query="SELECT 
                UUID_SHORT() as uuid,
                ot.ot_trans_type,
                ot.ot_id,
                ot.ot_sdt_id,
                ot.ot_bm_id,
                ot.ot_fabric_rate,
                SUM(ot.ot_fabric_mtr) as ot_fabric_mtr,
                SUM(ot.ot_fabric_rate * ot.ot_fabric_mtr) as ot_fabric_amt,
                0 as bal_qty,
                0 as isErrorExist,
                UPPER(sku.sku_name) as sku_name,
                UPPER(design.design_name) as design_name,
                design.design_image,
                design.design_id,
                ot.ot_ot_id
                FROM order_trans ot
                INNER JOIN sku_master sku ON(sku.sku_id = ot.ot_sku_id)
                INNER JOIN sku_design_trans sdt ON(sdt.sdt_id = ot.ot_sdt_id)
                INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_bm_id)
                INNER JOIN design_master design ON(design.design_id = sdt.sdt_design_id)
                WHERE ot.ot_delete_status = 0
                AND sku.sku_delete_status = 0
                AND sdt.sdt_delete_status = 0
                AND bm.bm_delete_status = 0
                AND ot.ot_bm_id > 0
                AND ot.ot_ot_id = $id
                GROUP BY sdt.sdt_id
                ORDER BY design.design_name ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;

        if(!empty($data)){
            foreach ($data as $key => $value) {
                $temp                       = $this->get_barcode_data_by_design1($value);
                $data[$key]['bal_qty']      = $temp['bal_mtr'];
                $data[$key]['isErrorExist'] = ($value['ot_fabric_mtr'] > $data[$key]['bal_qty']) ? 1 : 0;
                $data[$key]['qrcode_data']  = $temp['qrcode_data'];
            }
        }
        return $data;
    }
    public function get_barcode_data_by_design1($value){
        $ot_id      = $value['ot_ot_id'];
        $sdt_id     = $value['ot_sdt_id'];
        $design_id  = $value['design_id'];
        $defined_mtr= $value['ot_fabric_mtr'];
        $bal_mtr    = 0;
        $query="SELECT 
                ot.ot_id,
                ot.ot_bm_id,
                ot.ot_fabric_mtr,
                bm.bm_roll_no as qrcode,
                (((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_ott_mtr + bm.bm_et_mtr + bm.bm_prt_mtr)) + bm.bm_am_mtr) + ot.ot_fabric_mtr) as avail_mtr,
                (((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_ott_mtr + bm.bm_et_mtr + bm.bm_prt_mtr)) + bm.bm_am_mtr)) as bal_mtr,
                1 checked,
                bm.bm_id as sr_no
                FROM order_trans ot
                INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_bm_id)
                WHERE ot.ot_delete_status = 0
                AND ot.ot_ot_id = $ot_id
                AND ot.ot_sdt_id = $sdt_id";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        $record = [];
        $subsql = '';
        $ids    = '';
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $bal_mtr        = round(($bal_mtr + $value['avail_mtr']), 2);
                $ids            = empty($ids) ? $value['ot_bm_id'] : $ids.', '.$value['ot_bm_id'];
                $defined_mtr    = round(($defined_mtr - $value['ot_fabric_mtr']), 2);

                array_push($record, $value);
            }
            $subsql = " AND bm.bm_id NOT IN ($ids)"; 
        }
        $query="SELECT 
                0 as ot_id,
                bm.bm_id as ot_bm_id,
                0 as ot_fabric_mtr,
                bm.bm_roll_no as qrcode,
                (((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_ott_mtr + bm.bm_et_mtr + bm.bm_prt_mtr)) + bm.bm_am_mtr)) as avail_mtr,
                (((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_ott_mtr + bm.bm_et_mtr + bm.bm_prt_mtr)) + bm.bm_am_mtr)) as bal_mtr,
                0 checked,
                bm.bm_id as sr_no
                FROM barcode_master bm
                WHERE bm.bm_delete_status = 0 
                AND bm.bm_design_id = $design_id
                $subsql";
        $temp = $this->db->query($query)->result_array();
        if(!empty($temp)){
            foreach ($temp as $key => $value) {
                $avail_mtr = $value['avail_mtr'];
                if($defined_mtr > 0){
                    $assign_mtr= 0;
                    if($avail_mtr > 0 && $avail_mtr <= 2){
                        $margin     = floatval(($avail_mtr * 25) / 100);
                        $avail_mtr  = floatval($avail_mtr + $margin);
                    }
    
                    if($avail_mtr > 0){
                        if($avail_mtr > $defined_mtr){
                            $assign_mtr = $defined_mtr;
                        }else{
                            $assign_mtr = $avail_mtr;
                        }
                    }
                    $value['avail_mtr']    = round($avail_mtr, 2);
                    $value['ot_fabric_mtr']= round($assign_mtr, 2);
                    $value['bal_mtr']      = round(($avail_mtr - $assign_mtr),2);
                    $defined_mtr           = round(($defined_mtr - $assign_mtr),2);
                }
                if($avail_mtr > 0) array_push($record, $value);
                $bal_mtr = floatval($bal_mtr) + floatval($avail_mtr);
            }
        }
        return ['bal_mtr' => $bal_mtr, 'qrcode_data' => $data];
    }
    public function get_sku_apparel_data($id){
        $fabric_rate_query="SELECT 
                            SUM(sdt.sdt_mtr * sdt.sdt_rate) as fabric_rate
                            FROM sku_design_trans sdt 
                            WHERE sdt.sdt_delete_status = 0 
                            AND sdt.sdt_sku_id = sku.sku_id
                            GROUP BY sdt.sdt_sku_id";
        $query="SELECT 
                apparel.apparel_id,
                UPPER(sku.sku_name) as sku_name,
                sku.sku_image,
                UPPER(apparel.apparel_name) as apparel_name,
                (sku.sku_rate - IFNULL(($fabric_rate_query), 0)) as stitching_rate
                FROM sku_master sku
                INNER JOIN sku_karigar_trans skt ON(skt.skt_sku_id = sku.sku_id)
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = skt.skt_apparel_id)
                WHERE sku.sku_delete_status = 0
                AND skt.skt_delete_status = 0
                AND sku.sku_id = $id
                ORDER BY skt.skt_id ASC";
        return $this->db->query($query)->result_array();
    }
    public function get_apparel_rate($id){
        $query="SELECT abt.abt_rate as rate
                FROM apparel_branch_trans abt
                WHERE 1
                AND abt.abt_apparel_id = $id
                AND abt.abt_branch_id = ".$_SESSION['user_branch_id']."
                ORDER BY abt.abt_id DESC
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? 0 : $data[0]['rate'];
    }
    public function get_sku_rate($id){
        $query="SELECT sku.sku_rate as rate
                FROM sku_master sku
                WHERE sku.sku_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? 0 : $data[0]['rate'];
    }
    public function get_sku_stitching_rate($id){
        $dq="SELECT 
                SUM(sdyt_rate) as rate
                FROM sku_dying_trans
                WHERE sdyt_delete_status = 0
                AND sdyt_sku_id = $id
                GROUP BY sdyt_sku_id";
        $eq="SELECT 
                SUM(set_rate) as rate
                FROM sku_embroidery_trans
                WHERE set_delete_status = 0
                AND set_sku_id = $id
                GROUP BY set_sku_id";
        $kq="SELECT 
                SUM(skt_rate) as rate
                FROM sku_karigar_trans
                WHERE skt_delete_status = 0
                AND skt_sku_id = $id
                GROUP BY skt_sku_id";
        $oq="SELECT 
                SUM(sot_rate) as rate
                FROM sku_other_trans
                WHERE sot_delete_status = 0
                AND sot_sku_id = $id
                GROUP BY sot_sku_id";
        $query="SELECT 
                IFNULL(SUM(temp.rate), 0) as rate
                FROM ($dq UNION $eq UNION $kq UNION $oq) temp
                WHERE 1";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? 0 : $data[0]['rate'];
    }
    public function get_sku_fabric_rate($id){
        $query="SELECT SUM(sdt.sdt_amt) as rate
                FROM sku_design_trans sdt
                WHERE sdt.sdt_delete_status = 0
                AND sdt.sdt_sku_id = $id
                GROUP BY sdt.sdt_sku_id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? 0 : $data[0]['rate'];
    }
    public function get_fabric_rate($id){
        $query="SELECT bm.bm_pt_rate as rate
                FROM barcode_master bm
                WHERE bm.bm_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? 0 : $data[0]['rate'];
    }
    public function get_apparels($sku_id) {
        $query="SELECT
                UPPER(apparel.apparel_name) as name
                FROM sku_karigar_trans skt
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = skt.skt_apparel_id)
                WHERE skt.skt_delete_status = 0
                AND skt.skt_sku_id = $sku_id";
        $data =$this->db->query($query)->result_array();
        $text = '';
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $text = empty($text) ? $value['name'] : $text.', '.$value['name'];
            }
        }
        return $text;
    }
    public function get_designs($ot_id, $desc) {
        $query="SELECT
                CONCAT(bm.bm_item_code, ' / ', UPPER(design.design_name)) as design_name,
                design.design_image,
                ot.ot_fabric_mtr as fabric_mtr,
                IFNULL(UPPER(rack.rack_name), 'NA') as rack_name
                FROM order_trans ot
                INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_bm_id)
                INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
                LEFT JOIN rack_master rack ON(rack.rack_id = design.design_rack_id)
                WHERE ot.ot_delete_status = 0
                AND ot.ot_ot_id = $ot_id";
        // echo "<pre>"; print_r($query); exit;
        return $this->db->query($query)->result_array();
    }
    public function get_ot_id($ot_id){
        $query="SELECT ot.ot_id as id
                FROM order_trans ot
                WHERE ot.ot_apparel_id > 0
                AND ot.ot_ot_id = $ot_id
                ORDER BY ot.ot_id ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        $id = $ot_id;
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $id = empty($id) ? $value['id'] : $id.', '.$value['id'];
            }
        }
        return $id;
    }
    public function get_customer_measurement_data($ids){
        $query="SELECT cmt.*
                FROM customer_measurement_trans cmt
                WHERE cmt.cmt_ot_id IN ($ids)";
        // echo "<pre>"; print_r($query); exit;
        return $this->db->query($query)->result_array();
    }
    public function get_customer_style_data($ids){
        $query="SELECT cst.*
                FROM customer_style_trans cst
                WHERE cst.cst_value = 1
                AND cst.cst_ot_id IN ($ids)";
        // echo "<pre>"; print_r($query); exit;
        return $this->db->query($query)->result_array();
    }
    public function get_customer_style_image_data($ids){
        $query="SELECT csit.*
                FROM customer_style_image_trans csit
                WHERE csit.csit_ot_id IN ($ids)";
        // echo "<pre>"; print_r($query); exit;
        return $this->db->query($query)->result_array();
    }
    public function get_sku_trans_data($ot_id) {
        $query="SELECT ot.*,
                UPPER(apparel.apparel_name) as apparel_name,
                UPPER(sku.sku_name) as sku_name,
                sku.sku_image
                FROM order_trans ot
                INNER JOIN sku_master sku ON(sku.sku_id = ot.ot_sku_id)
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                WHERE 1
                AND ot.ot_apparel_id > 0
                AND (ot.ot_id = $ot_id OR ot.ot_ot_id = $ot_id)";
        // echo "<pre>"; print_r($query); exit;
        return $this->db->query($query)->result_array();
    }
    public function get_sku_data($id){
        $query="SELECT 
                sku.sku_id,
                UPPER(sku.sku_name) as sku_name,
                sku.sku_mrp as mrp,
                sku.sku_image as image,
                IFNULL(SUM(sdt.sdt_mtr), 0) as mtr
                FROM sku_master sku
                LEFT JOIN sku_design_trans sdt ON(sdt.sdt_sku_id = sku.sku_id)
                WHERE 1
                AND sku.sku_delete_status = 0
                AND sdt.sdt_delete_status = 0
                AND sku.sku_id = $id
                LIMIT 1";
        return $this->db->query($query)->result_array();
    }
    public function generate_barcode(){
        $year   = date('Y');
        $month  = date('m');
        $query  = "SELECT obt.obt_counter as counter 
                    FROM order_barcode_trans obt 
                    WHERE obt.obt_barcode_year = '$year' 
                    AND obt.obt_barcode_month = '$month'
                    ORDER BY obt.obt_counter DESC
                    LIMIT 1";
        // echo "<pre>"; print_r($query); exit;
        $data = $this->db->query($query)->result_array();
        return empty($data[0]['counter']) ? 30000001 : ($data[0]['counter'] + 1);
    }
    public function get_payment_mode_data($id){
        $query="SELECT opmt.opmt_id,
                opmt.opmt_amt as opmt_amt,
                opmt.opmt_payment_mode_id as opmt_payment_mode_id,
                UPPER(payment_mode.payment_mode_name) as payment_mode_name
                FROM order_payment_mode_trans opmt
                INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = opmt.opmt_payment_mode_id)
                WHERE opmt.opmt_delete_status = 0
                AND opmt.opmt_om_id = $id
                ORDER BY payment_mode.payment_mode_name ASC";
        $data = $this->db->query($query)->result_array();
        $ids  = '';
        $subsql='';
        $record=[];
        if(!empty($data)){
            foreach ($data as $key => $value) {
                array_push($record, $value);
                $ids .= empty($ids) ? $value['opmt_payment_mode_id'] : ', '.$value['opmt_payment_mode_id'];
            }
            $subsql .=" AND payment_mode.payment_mode_id NOT IN(".$ids.")";
        }

        $query="SELECT 0 as opmt_id,
                0 as opmt_amt,
                payment_mode.payment_mode_id as opmt_payment_mode_id,
                UPPER(payment_mode.payment_mode_name) as payment_mode_name
                FROM payment_mode_master payment_mode
                WHERE payment_mode.payment_mode_status = 1
                $subsql
                ORDER BY payment_mode.payment_mode_name ASC";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)){
            foreach ($data as $key => $value) {
                array_push($record, $value);
            }
        }
        usort($record, function($a, $b){
            return $a['payment_mode_name'] > $b['payment_mode_name'];
        });
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
    public function get_barcode_data($id, $mtr = 0){
		$query="SELECT bm.*,
                ((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_et_mtr)) + $mtr) as bal_qty,
                fabric.fabric_sgst_per as sgst_per,
                fabric.fabric_cgst_per as cgst_per,
                fabric.fabric_igst_per as igst_per
				FROM barcode_master bm
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = bm.bm_fabric_id)
				WHERE bm.bm_id = $id";
        // echo "<pre>"; print_r($query); exit;
		return $this->db->query($query)->result_array();
	}
    public function get_readymade_barcode_data($id, $qty = 0){
        $query="SELECT brmm.*,
                ((brmm.brmm_prmt_qty - brmm.brmm_prrt_qty) - (brmm.brmm_et_qty + brmm.brmm_ot_qty) + $qty) as bal_qty,
                IF(brmm_mrp > 1000,6,2.5) as sgst_per,
                IF(brmm_mrp > 1000,6,2.5) as cgst_per,
                IF(brmm_mrp > 1000,12,2.5) as igst_per
                FROM barcode_readymade_master brmm
                WHERE brmm.brmm_id = $id";
        return $this->db->query($query)->result_array();
    }
    public function get_apparel_data($id){
		$query="SELECT apparel.apparel_id,
                UPPER(apparel.apparel_name) as apparel_name,
                apparel.apparel_charges as charges,
                apparel.apparel_sgst_per as sgst_per,
                apparel.apparel_cgst_per as cgst_per,
                apparel.apparel_igst_per as igst_per
				FROM apparel_master apparel
				WHERE apparel.apparel_id = $id";
		return $this->db->query($query)->result_array();
	}
    
    public function get_apparel_apparel_data($ot_id, $apparel_id){
        $query="SELECT 
                ot.ot_id,
                ot.ot_apparel_id,
                UPPER(apparel.apparel_name) as apparel_name
                FROM order_trans ot
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                WHERE 1
                AND ot.ot_delete_status = 0
                AND ot.ot_ot_id = $ot_id";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)) return $data;

        $query="SELECT 
                0 as ot_id,
                aat.aat_apparel_id as ot_apparel_id,
                UPPER(apparel.apparel_name) as apparel_name
                FROM apparel_apparel_trans aat
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = aat.aat_apparel_id)
                WHERE 1
                AND aat.apparel_id = $apparel_id";
        return $this->db->query($query)->result_array();
    }

    public function get_design_data($id){
        $query="SELECT design.design_id,
                UPPER(design.design_name) as design_name
                FROM design_master design
                WHERE design.design_id = $id";
        return $this->db->query($query)->result_array();
    }
    public function get_measurement_data($om_id, $ot_id, $customer_id, $apparel_id){
        $subsql = "";
        // if(empty($om_id)) $subsql = " AND cmt.cmt_delete_status = 1";
        $query="SELECT 
                cmt.cmt_id,
                cmt.cmt_measurement_id,
                UPPER(measurement.measurement_name) as cmt_measurement_name,
                cmt.cmt_value1,
                cmt.cmt_value2,
                cmt.cmt_bill_no as bill_no,
                DATE_FORMAT(cmt.cmt_bill_date, '%d-%m-%Y') as bill_date,
                IFNULL(UPPER(cmt.cmt_remark), '') as cmt_remark,
                cmt.cmt_ot_id,
                cmt.cmt_apparel_id
                FROM customer_measurement_trans cmt
                INNER JOIN measurement_master measurement ON(measurement.measurement_id = cmt.cmt_measurement_id)
                WHERE cmt.cmt_ot_id = $ot_id
                AND cmt.cmt_customer_id = $customer_id
                AND cmt.cmt_apparel_id = $apparel_id
                $subsql
                ORDER BY measurement.measurement_id ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)) return $this->get_all_measurement($ot_id, $apparel_id, $data);

        $latest_data = $this->get_latest_measurement($customer_id, $apparel_id);
          // echo "<pre>"; print_r($latest_data); exit;
        if(!empty($latest_data['bill_no'])){
            $subsql = " AND cmt.cmt_bill_no = '".$latest_data['bill_no']."'";
            $subsql.= " AND cmt.cmt_bill_date = '".$latest_data['bill_date']."'";
            if(!empty($latest_data['om_id'])) $subsql = " AND cmt.cmt_obm_id = '".$latest_data['om_id']."'";
            $query="SELECT 
                    0 as cmt_id,
                    cmt.cmt_measurement_id,
                    UPPER(measurement.measurement_name) as cmt_measurement_name,
                    cmt.cmt_value1,
                    cmt.cmt_value2,
                    cmt.cmt_bill_no as bill_no,
                    DATE_FORMAT(cmt.cmt_bill_date, '%d-%m-%Y') as bill_date,
                    IFNULL(UPPER(cmt.cmt_remark), '') as cmt_remark,
                    $ot_id as cmt_ot_id,
                    cmt.cmt_apparel_id

                    FROM customer_measurement_trans cmt
                    INNER JOIN measurement_master measurement ON(measurement.measurement_id = cmt.cmt_measurement_id)
                    WHERE 1
                    AND cmt.cmt_customer_id = $customer_id
                    AND cmt.cmt_apparel_id = $apparel_id
                    $subsql
                    GROUP BY measurement.measurement_id
                    ORDER BY measurement.measurement_id ASC";
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($query); exit;
            // echo "<pre>"; print_r($data); exit;
            if(!empty($data)) return $this->get_all_measurement($ot_id, $apparel_id, $data);
        }

        $query="SELECT 
                0 as cmt_id,
                measurement.measurement_id as cmt_measurement_id,
                UPPER(measurement.measurement_name) as cmt_measurement_name,
                UPPER(apparel.apparel_name) as apparel_name,
                '' as cmt_value1,
                '' as cmt_value2,
                '' as bill_no,
                '' as bill_date,
                '' as cmt_remark,
                $ot_id as cmt_ot_id,

                apparel.apparel_id as cmt_apparel_id
                FROM measurement_setting_master measurement_setting
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = measurement_setting.measurement_setting_apparel_id)
                INNER JOIN measurement_master measurement ON(measurement.measurement_id = measurement_setting.measurement_setting_measurement_id)
                WHERE measurement_setting.measurement_setting_apparel_id = $apparel_id
                AND measurement_setting.measurement_setting_status = 1
                ORDER BY measurement.measurement_id ASC";
        return $this->db->query($query)->result_array();
    }
    public function get_all_measurement($ot_id, $apparel_id, $data){
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
                UPPER(apparel.apparel_name) as apparel_name,
                '' as cmt_value1,
                '' as cmt_value2,
                '' as bill_no,
                '' as bill_date,
                '' as cmt_remark,
                $ot_id as cmt_ot_id,
                $apparel_id as cmt_apparel_id
                FROM measurement_setting_master measurement_setting
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = measurement_setting.measurement_setting_apparel_id)
                INNER JOIN measurement_master measurement ON(measurement.measurement_id = measurement_setting.measurement_setting_measurement_id)
                WHERE measurement_setting.measurement_setting_apparel_id = $apparel_id
                AND measurement_setting.measurement_setting_status = 1
                $subsql
                ORDER BY measurement.measurement_id ASC";
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
                return $a['cmt_measurement_id'] - $b['cmt_measurement_id'];
            });
        }
        return $record;
    }
    public function get_style_data($om_id, $ot_id, $customer_id, $apparel_id){
        $subsql = " ";
        // if(empty($om_id)) $subsql = " AND cst.cst_delete_status = 1";
        $query="SELECT 
                cst.cst_id,
                cst.cst_style_id,
                UPPER(style.style_name) as cst_style_name,
                cst.cst_value,
                cst.cst_bill_no as bill_no,
                DATE_FORMAT(cst.cst_bill_date, '%d-%m-%Y') as bill_date,
                cst.cst_ot_id,
                cst.cst_apparel_id
                FROM customer_style_trans cst
                INNER JOIN style_master style ON(style.style_id = cst.cst_style_id)
                WHERE cst.cst_ot_id = $ot_id
                AND cst.cst_customer_id = $customer_id
                AND cst.cst_apparel_id = $apparel_id
                $subsql
                ORDER BY style.style_id ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        

        if(!empty($data)) return $this->get_all_style($ot_id, $apparel_id, $data);
    
        $latest_data = empty($om_id) ? $this->get_latest_style($customer_id, $apparel_id) : [];
        // echo "<pre>"; print_r($latest_data); exit;        
        if(!empty($latest_data['bill_no'])){
            $subsql = " AND cst.cst_bill_no = '".$latest_data['bill_no']."'";
            $subsql.= " AND cst.cst_bill_date = '".$latest_data['bill_date']."'";
            if(!empty($latest_data['om_id'])) $subsql = " AND cst.cst_obm_id = '".$latest_data['om_id']."'";
            $query="SELECT 
                    cst.cst_id,
                    cst.cst_style_id,
                    UPPER(style.style_name) as cst_style_name,
                    cst.cst_value,
                    cst.cst_bill_no as bill_no,
                    DATE_FORMAT(cst.cst_bill_date, '%d-%m-%Y') as bill_date,
                    $ot_id as cst_ot_id,
                    cst.cst_apparel_id
                    FROM customer_style_trans cst
                    INNER JOIN style_master style ON(style.style_id = cst.cst_style_id)
                    WHERE 1
                    AND cst.cst_customer_id = $customer_id
                    AND cst.cst_apparel_id = $apparel_id
                    $subsql
                    GROUP BY style.style_id
                    ORDER BY style.style_id ASC";
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($query); exit;
            // echo "<pre>"; print_r($data); exit;
            if(!empty($data)) return $this->get_all_style($ot_id, $apparel_id, $data);
        }
    
        $query="SELECT 
                0 as cst_id,
                style.style_id as cst_style_id,
                UPPER(style.style_name) as cst_style_name,
                '' as cst_value,
                '' as bill_no,
                '' as bill_date,
                $ot_id as cst_ot_id,
                apparel.apparel_id as cst_apparel_id
                FROM style_setting_master style_setting
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = style_setting.style_setting_apparel_id)
                INNER JOIN style_master style ON(style.style_id = style_setting.style_setting_style_id)
                WHERE style_setting.style_setting_status = 1
                AND style_setting.style_setting_apparel_id = $apparel_id
                ORDER BY style_setting.style_setting_id ASC";
        return $this->db->query($query)->result_array();
    }
    public function get_latest_style($customer_id, $apparel_id){
        $query="SELECT cst.cst_obm_id as om_id,
                cst.cst_ot_id as ot_id,
                cst.cst_bill_no as bill_no,
                cst.cst_bill_date as bill_date
                FROM customer_style_trans cst
                WHERE 1
                AND cst.cst_customer_id = $customer_id
                AND cst.cst_apparel_id = $apparel_id
                ORDER BY cst.cst_id DESC
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)) return ['om_id' => $data[0]['om_id'], 'ot_id' => $data[0]['ot_id'], 'bill_no' => $data[0]['bill_no'], 'bill_date' => $data[0]['bill_date']];
        return ['om_id' => '', 'ot_id' => '', 'bill_no' => '', 'bill_date' => ''];
    }
    public function get_latest_measurement($customer_id, $apparel_id){
        $query="SELECT cmt.cmt_obm_id as om_id,
                cmt.cmt_ot_id as ot_id,
                cmt.cmt_bill_no as bill_no,
                cmt.cmt_bill_date as bill_date
                FROM customer_measurement_trans cmt
                WHERE 1
                AND cmt.cmt_customer_id = $customer_id
                AND cmt.cmt_apparel_id = $apparel_id
                ORDER BY cmt.cmt_id DESC
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)) return ['om_id' => $data[0]['om_id'], 'ot_id' => $data[0]['ot_id'], 'bill_no' => $data[0]['bill_no'], 'bill_date' => $data[0]['bill_date']];
        return ['om_id' => '', 'ot_id' => '', 'bill_no' => '', 'bill_date' => ''];
    }
    public function get_all_style($ot_id, $apparel_id, $data){
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
                $ot_id as cst_ot_id,
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
    public function get_style_priority_data($om_id, $ot_id, $customer_id, $apparel_id){
        $query="SELECT 
                spt.spt_id,
                asm.asm_id,
                UPPER(asm.asm_name) as asm_name,
                spm.spm_apparel_id as apparel_id
                FROM style_priority_master spm
                INNER JOIN style_priority_trans spt ON(spt.spt_spm_id = spm.spm_id)
                INNER JOIN apparel_style_master asm ON(asm.asm_id = spt.spt_asm_id)
                WHERE asm.asm_status = 1
                AND spm.spm_status = 1
                AND spm.spm_delete_status = 0
                AND spt.spt_delete_status = 0
                AND asm.asm_delete_status = 0
                AND spm.spm_apparel_id = $apparel_id
                ORDER BY spt.spt_priority ASC";
        // echo "<pre>"; print_r($query); exit;
        $data = $this->db->query($query)->result_array();
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                $data[$key]['apparel_style_data'] = $this->get_style_image_data($om_id, $ot_id, $customer_id, $apparel_id, $value['asm_id']);
            }
        }
        return $data;
    }
    public function get_style_image_data($om_id, $ot_id, $customer_id, $apparel_id, $asm_id){
        $subsql = " ";
        // if(empty($om_id)) $subsql = " AND csit.csit_delete_status = 1";
        $query="SELECT 
                csit.csit_id,
                ast.ast_id,
                UPPER(ast.ast_name) as ast_name,
                ast.ast_image,
                1 as ast_default,
                csit.csit_bill_no as bill_no,
                DATE_FORMAT(csit.csit_bill_date, '%d-%m-%Y') as bill_date,
                csit.csit_ot_id,
                csit.csit_apparel_id
                FROM customer_style_image_trans csit
                INNER JOIN apparel_style_trans ast ON(ast.ast_id = csit.csit_ast_id)
                WHERE csit.csit_ot_id = $ot_id
                AND csit.csit_customer_id = $customer_id
                AND csit.csit_apparel_id = $apparel_id
                AND ast.ast_asm_id = $asm_id
                $subsql
                ORDER BY ast.ast_name ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;

        if(!empty($data)) return $this->get_all_style_image($ot_id, $apparel_id, $asm_id, $data);
    
        $latest_data = $this->get_latest_style_image($customer_id, $apparel_id);
        if(!empty($latest_data['bill_no'])){
            $subsql = " AND csit.csit_bill_no = '".$latest_data['bill_no']."'";
            $subsql.= " AND csit.csit_bill_date = '".$latest_data['bill_date']."'";
            if(!empty($latest_data['om_id'])) $subsql = " AND csit.csit_obm_id = '".$latest_data['om_id']."'";
            $query="SELECT 
                    csit.csit_id,
                    ast.ast_id,
                    UPPER(ast.ast_name) as ast_name,
                    ast.ast_image,
                    1 as ast_default,
                    csit.csit_bill_no as bill_no,
                    DATE_FORMAT(csit.csit_bill_date, '%d-%m-%Y') as bill_date,
                    $ot_id as csit_ot_id,
                    csit.csit_apparel_id
                    FROM customer_style_image_trans csit
                    INNER JOIN apparel_style_trans ast ON(ast.ast_id = csit.csit_ast_id)
                    WHERE 1
                    AND csit.csit_customer_id = $customer_id
                    AND csit.csit_apparel_id = $apparel_id
                    AND ast.ast_asm_id = $asm_id
                    $subsql
                    GROUP BY ast.ast_id
                    ORDER BY ast.ast_name ASC";
            $data = $this->db->query($query)->result_array();
            // echo "<pre>"; print_r($query); exit;
            // echo "<pre>"; print_r($data); exit;
            if(!empty($data)) return $this->get_all_style_image($ot_id, $apparel_id, $asm_id, $data);
        }
    
        $query="SELECT 
                0 as csit_id,
                ast.ast_id,
                UPPER(ast.ast_name) as ast_name,
                ast.ast_image,
                ast.ast_default,
                '' as bill_no,
                '' as bill_date,
                $ot_id as csit_ot_id,
                $apparel_id as csit_apparel_id
                FROM style_priority_master spm
                INNER JOIN style_priority_trans spt ON(spt.spt_spm_id = spm.spm_id)
                INNER JOIN apparel_style_master asm ON(asm.asm_id = spt.spt_asm_id)
                INNER JOIN apparel_style_trans ast ON(ast.ast_asm_id = asm.asm_id)
                WHERE spm.spm_status = 1
                AND asm.asm_status = 1
                AND spm.spm_delete_status = 0
                AND spt.spt_delete_status = 0
                AND asm.asm_delete_status = 0
                AND ast.ast_delete_status = 0
                AND spm.spm_apparel_id = $apparel_id
                AND asm.asm_id = $asm_id
                ORDER BY ast.ast_name ASC";
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;                
        return $this->db->query($query)->result_array();
    }
    public function get_all_style_image($ot_id, $apparel_id, $asm_id, $data){
        $ids  = '';
        $subsql='';
        $record=[];
        
        foreach ($data as $key => $value) {
            array_push($record, $value);
            $ids .= empty($ids) ? $value['ast_id'] : ', '.$value['ast_id'];
        }
        $subsql .=" AND ast.ast_id NOT IN(".$ids.")";

        $query="SELECT 
                0 as csit_id,
                ast.ast_id,
                UPPER(ast.ast_name) as ast_name,
                ast.ast_image,
                0 as ast_default,
                '' as bill_no,
                '' as bill_date,
                $ot_id as csit_ot_id,
                $apparel_id as csit_apparel_id
                FROM style_priority_master spm
                INNER JOIN style_priority_trans spt ON(spt.spt_spm_id = spm.spm_id)
                INNER JOIN apparel_style_master asm ON(asm.asm_id = spt.spt_asm_id)
                INNER JOIN apparel_style_trans ast ON(ast.ast_asm_id = asm.asm_id)
                WHERE spm.spm_status = 1
                AND asm.asm_status = 1
                AND spm.spm_delete_status = 0
                AND spt.spt_delete_status = 0
                AND asm.asm_delete_status = 0
                AND ast.ast_delete_status = 0
                AND spm.spm_apparel_id = $apparel_id
                AND asm.asm_id = $asm_id
                $subsql
                ORDER BY ast.ast_name ASC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)){
            foreach ($data as $key => $value) {
                array_push($record, $value);
            }
        }
        usort($record, function($a, $b){
            return $a['ast_name'] > $b['ast_name'];
        });
        // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_latest_style_image($customer_id, $apparel_id){
        $query="SELECT csit.csit_obm_id as om_id,
                csit.csit_ot_id as ot_id,
                csit.csit_bill_no as bill_no,
                csit.csit_bill_date as bill_date
                FROM customer_style_image_trans csit
                WHERE 1
                AND csit.csit_customer_id = $customer_id
                AND csit.csit_apparel_id = $apparel_id
                ORDER BY csit.csit_id DESC
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)) return ['om_id' => $data[0]['om_id'], 'ot_id' => $data[0]['ot_id'], 'bill_no' => $data[0]['bill_no'], 'bill_date' => $data[0]['bill_date']];
        return ['om_id' => '', 'ot_id' => '', 'bill_no' => '', 'bill_date' => ''];
    }
    public function get_data_for_qrcode_print($clause, $_id){  
        $rollno= ENV == PROD ? 'obt.obt_roll_no' : 0;
        $query ="SELECT 
                om.om_entry_no as entry_no,
                DATE_FORMAT(om.om_entry_date, '%d-%m-%Y') as entry_date,
                UPPER(customer.customer_name) as customer_name,
                UPPER(customer.customer_mobile) as customer_mobile,
                UPPER(apparel.apparel_name) as apparel_name,
                obt.obt_roll_no as qrcode, 
                $rollno as roll_no
                FROM order_master om
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                INNER JOIN order_barcode_trans obt ON(obt.obt_ot_id = ot.ot_id)
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
                WHERE om.om_delete_status = 0
                AND ot.ot_delete_status = 0
                AND obt.obt_delete_status = 0
                AND ".$clause." = $_id";
        $data['barcode_data'] = $this->db->query($query)->result_array();
        $data['company_data'] = $this->db_operations->get_record('company_master', ['company_id' => 1]);
        // echo "<pre>"; print_r($query); exit();
        // echo "<pre>"; print_r($data); exit();
        return $data;
    }
    public function get_data_for_print($om_id){
        $query="SELECT  UPPER(company.company_name) as company_name,
                UPPER(company.company_gstin) as gstin,
                LOWER(company.company_email) as email,
                LOWER(company.company_mobile) as mobile,
                UPPER(company.company_address) as address,
                company.company_pincode as pincode,
                IFNULL(UPPER(city.city_name), '') as city_name,
                IFNULL(UPPER(state.state_name), '') as state_name,
                IFNULL(UPPER(state.state_code), '') as state_code,
                IFNULL(UPPER(country.country_name), '') as country_name
                FROM company_master company
                LEFT JOIN city_master city ON(city.city_id = company.company_city_id)
                LEFT JOIN state_master state ON(state.state_id = company.company_state_id)
                LEFT JOIN country_master country ON(country.country_id = company.company_country_id)
                WHERE 1 AND company.company_id = 1";
                // company.company_constant != ''
        // echo "<pre>"; print_r($query); exit();
        $record['company_data'] = $this->db->query($query)->result_array();

        // $gst_amt = $this->get_order_gst_amt($om_id);

        $query="SELECT om.om_entry_no as entry_no, 
                DATE_FORMAT(om.om_entry_date, '%d-%m-%Y') as entry_date,
                DATE_FORMAT(om.om_trial_date, '%d-%m-%Y') as trial_date,
                DATE_FORMAT(om.om_delivery_date, '%d-%m-%Y') as delivery_date,
                om.om_notes as notes,
                om.om_total_qty as total_qty,
                om.om_total_mtr as total_mtr,
                om.om_sub_amt as sub_amt,
                (om.om_disc_amt +om.om_bill_disc_amt + om.om_round_off) as disc_amt,
                om.om_taxable_amt as taxable_amt,
                om.om_sgst_amt as sgst_amt,
                om.om_cgst_amt as cgst_amt,
                om.om_igst_amt as igst_amt,
                (om.om_sgst_amt + om.om_cgst_amt + om.om_igst_amt) as gst_amt,
                (om.om_taxable_amt + om.om_sgst_amt + om.om_cgst_amt + om.om_igst_amt) as net_amt,
                om.om_bill_disc_per as bill_disc_per,
                om.om_bill_disc_amt as bill_disc_amt,
                om.om_round_off as round_off,
                om.om_total_amt as total_amt,
                om.om_advance_amt as advance_amt,
                om.om_balance_amt as balance_amt,
                UPPER(om.om_notes) as notes,
                UPPER(billing.customer_name) as billing_name,
                UPPER(billing.customer_mobile) as billing_mobile,
                UPPER(billing.customer_gst_no) as billing_gst_no,
                UPPER(billing.customer_address) as billing_address,
                billing.customer_pincode as billing_pincode,
                UPPER(customer.customer_name) as customer_name,
                UPPER(customer.customer_mobile) as customer_mobile,
                UPPER(customer.customer_gst_no) as customer_gst_no,
                UPPER(customer.customer_address) as customer_address,
                customer.customer_pincode as customer_pincode,
                IFNULL(UPPER(city.city_name), '') as city_name,
                IFNULL(UPPER(state.state_name), '') as state_name,
                IFNULL(UPPER(country.country_name), '') as country_name,
                IFNULL(UPPER(user.user_fullname), '') as salesman_name
                FROM order_master om
                INNER JOIN customer_master billing ON(billing.customer_id = om.om_billing_id)
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
                LEFT JOIN city_master city ON(city.city_id = customer.customer_city_id)
                LEFT JOIN state_master state ON(state.state_id = customer.customer_state_id)
                LEFT JOIN country_master country ON(country.country_id = customer.customer_country_id)
                LEFT JOIN user_master user ON(user.user_id = om.om_salesman_id)
                WHERE om.om_delete_status = 0 
                AND om.om_id = $om_id";
        // echo "<pre>"; print_r($query); exit();
        $record['master_data'] = $this->db->query($query)->result_array();
         // echo "<pre>"; print_r($record); exit();
       
        $query="SELECT ot.ot_trans_type as trans_type,
                IFNULL(UPPER(apparel.apparel_name), '') as apparel_name,
                IFNULL(UPPER(fabric.fabric_name), '') as fabric_name,
                IFNULL(UPPER(design.design_name), '') as design_name,
                IFNULL(UPPER(hsn.hsn_name), '') as hsn_name,
                IFNULL(UPPER(pt.pt_description), '') as description,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(size.size_name), '') as size_name,
                IFNULL(UPPER(readymade_category.readymade_category_name), '') as category_name,
                (ot.ot_qty) as qty,
                (ot.ot_mtr) as mtr,
                (ot.ot_total_mtr) as total_mtr,
                ot.ot_rate as rate,
                (ot.ot_amt) as amt,
                ot.ot_disc_per as disc_per,
                (ot.ot_disc_amt) as disc_amt,
                (ot.ot_taxable_amt) as taxable_amt,
                (ot.ot_sgst_per) as sgst_per,
                (ot.ot_sgst_amt) as sgst_amt,
                ot.ot_cgst_per as cgst_per,
                (ot.ot_cgst_amt) as cgst_amt,
                ot.ot_igst_per as igst_per,
                (ot.ot_igst_amt) as igst_amt,
                IF(ot.ot_igst_amt > 0, (ot.ot_igst_per), (ot.ot_sgst_per + ot.ot_cgst_per)) as gst_per,
                (ot.ot_sgst_amt + ot.ot_cgst_amt + ot.ot_igst_amt) as gst_amt,
                (ot.ot_total_amt) as total_amt
                FROM order_trans ot
                LEFT JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                LEFT JOIN barcode_master bm ON(bm.bm_id = ot.ot_bm_id)
                LEFT JOIN barcode_readymade_master brmm ON(brmm.brmm_id = ot.ot_brmm_id) 
                LEFT JOIN purchase_trans pt ON(pt.pt_id = bm.bm_pt_id)
                LEFT JOIN hsn_master hsn ON(hsn.hsn_id = bm.bm_hsn_id)
                LEFT JOIN fabric_master fabric ON(fabric.fabric_id = bm.bm_fabric_id)
                LEFT JOIN design_master design ON(design.design_id = ot.ot_design_id)
                LEFT JOIN color_master color ON(color.color_id = brmm.brmm_color_id OR color.color_id = bm.bm_color_id)
                LEFT JOIN size_master size ON(size.size_id = brmm.brmm_size_id)
                LEFT JOIN readymade_category_master readymade_category ON(readymade_category.readymade_category_id = brmm.brmm_readymade_category_id)
                WHERE ot.ot_delete_status = 0 
                AND ot.ot_ot_id = 0
                AND ot.ot_om_id = $om_id
                GROUP BY ot.ot_id
                ORDER BY ot.ot_trans_type, ot.ot_rate ASC";

                // LEFT JOIN design_master design ON(design.design_id = brmm.brmm_design_id OR design.design_id = bm.bm_design_id)
        $record['trans_data'] = $this->db->query($query)->result_array();

        // echo "<pre>"; print_r($query); exit();
        // echo "<pre>"; print_r($data); exit();

        // echo "<pre>"; print_r($record); exit();
        return $record;
    }
    public function get_data_for_measurement_print($om_id, $ot_id){
        $subsql = empty($ot_id) ? '' : " AND ot.ot_id = $ot_id";
        $query="SELECT om.om_entry_no as entry_no,
                DATE_FORMAT(om.om_entry_date, '%d-%m-%Y') as entry_date,
                DATE_FORMAT(ADDDATE(om.om_entry_date, INTERVAL 2 DAY), '%d-%m-%Y') as _date,
                IF(om.om_trial_date = '', IF(om.om_delivery_date = '', '', CONCAT(DATE_FORMAT(ADDDATE(om.om_delivery_date, INTERVAL -4 DAY), '%d-%m-%Y'), ' (DELIVERY)')), CONCAT(DATE_FORMAT(ADDDATE(om.om_trial_date, INTERVAL -4 DAY), '%d-%m-%Y'), ' (TRIAL)'))  as _date1,
                ot.ot_id,
                apparel.apparel_id,
                UPPER(apparel.apparel_name) as apparel_name,
                ot.ot_qty as apparel_cnt,
                customer.customer_id,
                UPPER(customer.customer_name) as customer_name
                FROM order_master om
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
                WHERE om.om_delete_status = 0
                AND ot.ot_delete_status = 0
                AND om.om_id = $om_id
                AND ot.ot_id NOT IN (SELECT ott.ot_ot_id FROM order_trans ott WHERE ott.ot_om_id = om.om_id)
                $subsql
                ORDER BY apparel.apparel_name ASC";
        $record['trans_data'] = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit();
        // echo "<pre>"; print_r($record); exit();

        if(!empty($record['trans_data'])){
         
            foreach ($record['trans_data'] as $key => $value) {
                $record['trans_data'][$key]['measurement_data']     = $this->get_order_measurement($value['ot_id']);
                $record['trans_data'][$key]['style_data']           = $this->get_order_style($value['ot_id']);
                $record['trans_data'][$key]['style_image_data']     = $this->get_order_style_image($value['ot_id']);
            }
        }
        // echo "<pre>"; print_r($record); exit();
        return $record;
    }
    public function get_order_measurement($ot_id){
        $query="SELECT UPPER(measurement.measurement_name) as measurement_name,
                cmt.cmt_value1 as value1,
                cmt.cmt_value2 as value2,
                cmt.cmt_remark as remark
                FROM customer_measurement_trans cmt
                INNER JOIN measurement_master measurement ON(measurement.measurement_id = cmt.cmt_measurement_id)
                WHERE 1
                AND cmt.cmt_ot_id = $ot_id";
        return $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit();
        // echo "<pre>"; print_r($record); exit();
    }
    public function get_order_style($ot_id){
        $query="SELECT UPPER(style.style_name) as style_name
                FROM customer_style_trans cst
                INNER JOIN style_master style ON(style.style_id = cst.cst_style_id)
                WHERE cst.cst_value = 1
                AND  cst.cst_ot_id = $ot_id
                ORDER BY style.style_id ASC";
        return $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit();
        // echo "<pre>"; print_r($record); exit();
    }
    public function get_order_style_image($ot_id){
        $query="SELECT 
                UPPER(ast.ast_name) as ast_name,
                ast.ast_image as ast_image
                FROM customer_style_image_trans csit
                INNER JOIN apparel_style_trans ast ON(ast.ast_id = csit.csit_ast_id)
                WHERE csit.csit_ot_id = $ot_id
                ORDER BY ast.ast_id ASC";
        return $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit();
        // echo "<pre>"; print_r($record); exit();
    }
    public function get_gst_amt($om_id){
        $cgst_amt = 0;
        $sgst_amt = 0;
        $igst_amt = 0;

        $query="SELECT SUM(ot.ot_cgst_amt) as cgst_amt,
                SUM(ot.ot_sgst_amt) as sgst_amt,
                SUM(ot.ot_igst_amt) as igst_amt
                FROM order_trans ot
                WHERE ot.ot_delete_status = 0
                AND ot.ot_om_id = $om_id
                AND ot.ot_ot_id = 0
                GROUP BY ot.ot_om_id";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)){
            $cgst_amt = $data[0]['cgst_amt'];
            $sgst_amt = $data[0]['sgst_amt'];
            $igst_amt = $data[0]['igst_amt'];
        }
        return ['cgst_amt' => $cgst_amt, 'sgst_amt' => $sgst_amt, 'igst_amt' => $igst_amt];
    }
    public function _bm_id(){
		$subsql = "";
		$limit  = PER_PAGE;
		$offset = OFFSET;
		$page 	= 1;
		if(isset($_GET['limit']) && !empty($_GET['limit'])){
			$limit = $_GET['limit'];
		}
		if(isset($_GET['page']) && !empty($_GET['page'])){
			$page 	= $_GET['page'];
			$offset = $limit * ($page - 1);
		}
		if((isset($_GET['name']) && !empty($_GET['name']))){
            $name 	= $_GET['name'];
            $subsql .= " AND (bm.bm_item_code LIKE '".$name."%' OR design.design_name LIKE '".$name."%')";
        }else{
            if(ENV != DEV){
                $subsql .= " AND (bm.bm_item_code = 'XXX') ";
            }
        }
		$query="SELECT bm.bm_id as id, 
				CONCAT(bm.bm_item_code, ' - ', UPPER(design.design_name)) as name
				FROM barcode_master bm
                INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
				WHERE 1
				$subsql
				GROUP BY bm.bm_id
				ORDER BY bm.bm_item_code ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}
    public function _brmm_id(){
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
        if((isset($_GET['name']) && !empty($_GET['name']))){
            $name   = $_GET['name'];
            $subsql .= " AND (brmm_item_code LIKE '".$name."%')";
        }else{
            if(ENV != DEV){
                $subsql .= " AND (brmm_item_code = 'XXX') ";
            }
        }
        $query="SELECT brmm_id as id, 
                brmm_item_code as name
                FROM barcode_readymade_master
                WHERE 1 
                $subsql
                GROUP BY brmm_id
                ORDER BY brmm_item_code ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _design_id(){
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
        if((isset($_GET['name']) && !empty($_GET['name']))){
            $name   = $_GET['name'];
            $subsql .= " AND (design_name LIKE '".$name."%')";
        }else{
            // if(ENV != DEV){
            //     $subsql .= " AND (design_name = 'XXX') ";
            // }
        }
        $query="SELECT design_id as id, 
                design_name as name
                FROM design_master
                WHERE 1 
                $subsql
                GROUP BY design_id
                ORDER BY design_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function get_name($term, $id){
        $query="SELECT UPPER(".$term."_name) as name FROM ".$term."_master WHERE ".$term."_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? '' : $data[0]['name'];
    }
    public function get_item_code($id){
        $query="SELECT bm_item_code as name FROM barcode_master WHERE bm_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? '' : $data[0]['name'];
    }
    public function get_readymade_item_code($id){
        $query="SELECT brmm_item_code as name FROM barcode_readymade_master WHERE brmm_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? '' : $data[0]['name'];
    }
    public function _entry_no(){
		$subsql = "";
		$limit  = PER_PAGE;
		$offset = OFFSET;
		$page 	= 1;
		if(isset($_GET['limit']) && !empty($_GET['limit'])){
			$limit = $_GET['limit'];
		}
		if(isset($_GET['page']) && !empty($_GET['page'])){
			$page 	= $_GET['page'];
			$offset = $limit * ($page - 1);
		}
		if(isset($_GET['name']) && !empty($_GET['name'])){
			$name 	= $_GET['name'];
			$subsql .= " AND (om.om_entry_no LIKE '%".$name."%') ";
		}
		$query="SELECT om.om_entry_no as id, UPPER(om.om_entry_no) as name
				FROM order_master om
				WHERE om.om_status = 1
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
		$page 	= 1;
		if(isset($_GET['limit']) && !empty($_GET['limit'])){
			$limit = $_GET['limit'];
		}
		if(isset($_GET['page']) && !empty($_GET['page'])){
			$page 	= $_GET['page'];
			$offset = $limit * ($page - 1);
		}
		if(isset($_GET['name']) && !empty($_GET['name'])){
			$name 	= $_GET['name'];
			$subsql .= " AND (customer.customer_name LIKE '%".$name."%') ";
		}
		$query="SELECT customer.customer_name as id, UPPER(customer.customer_name) as name
				FROM order_master om
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
				WHERE om.om_status = 1
				$subsql
				GROUP BY customer.customer_name ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}
    public function _user_id(){
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
            $subsql .= " AND (user.user_fullname LIKE '%".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $name   = $_GET['param'];
            $subsql .= " AND (user.user_status = ".$name.") ";
        }
        if(isset($_GET['param1']) && !empty($_GET['param1'])){
            $name   = $_GET['param1'];
            $subsql .= " AND (role.role_name like '%".$name."%') ";
        }
        $query="SELECT user.user_id as id, UPPER(user.user_fullname) as name
                FROM user_master user
                left JOIN role_master role ON(role.role_id = user.user_role_id)
                WHERE 1
                $subsql
                GROUP BY user.user_fullname ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
}
?>