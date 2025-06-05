<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 
require_once APPPATH . 'core/MY_Model.php';
class sales_summary_item_wise_model extends my_model{
    public function __construct(){ parent::__construct('report', 'sales_summary_item_wise'); }
    public function get_record(){  
        $record     = [];
        $subsql 	= '';
        $having 	= '';
        if(isset($_REQUEST['_entry_no']) && !empty($_REQUEST['_entry_no'])){
            $subsql .=" AND om.om_entry_no = '".$_REQUEST['_entry_no']."'";
            $record['filter']['_entry_no']['value'] = $_REQUEST['_entry_no'];
            $record['filter']['_entry_no']['text']  = $_REQUEST['_entry_no'];
        }
        if(isset($_REQUEST['_customer_name']) && !empty($_REQUEST['_customer_name'])){
            $subsql .=" AND customer.customer_name = '".$_REQUEST['_customer_name']."'";
            $record['filter']['_customer_name']['value'] = $_REQUEST['_customer_name'];
            $record['filter']['_customer_name']['text']  = $_REQUEST['_customer_name'];
        }
        if(isset($_REQUEST['_fabric_name']) && !empty($_REQUEST['_fabric_name'])){
            $subsql .=" AND fabric.fabric_name = '".$_REQUEST['_fabric_name']."'";
            $record['filter']['_fabric_name']['value'] = $_REQUEST['_fabric_name'];
            $record['filter']['_fabric_name']['text']  = $_REQUEST['_fabric_name'];
        }
        if(isset($_REQUEST['_design_name']) && !empty($_REQUEST['_design_name'])){
            $subsql .=" AND design.design_name = '".$_REQUEST['_design_name']."'";
            $record['filter']['_design_name']['value'] = $_REQUEST['_design_name'];
            $record['filter']['_design_name']['text']  = $_REQUEST['_design_name'];
        }
        if(isset($_REQUEST['_color_name']) && !empty($_REQUEST['_color_name'])){
            $subsql .=" AND color.color_name = '".$_REQUEST['_color_name']."'";
            $record['filter']['_color_name']['value'] = $_REQUEST['_color_name'];
            $record['filter']['_color_name']['text']  = $_REQUEST['_color_name'];
        }
        if(isset($_REQUEST['_width_name']) && !empty($_REQUEST['_width_name'])){
            $subsql .=" AND width.width_name = '".$_REQUEST['_width_name']."'";
            $record['filter']['_width_name']['value'] = $_REQUEST['_width_name'];
            $record['filter']['_width_name']['text']  = $_REQUEST['_width_name'];
        }
        if(isset($_REQUEST['_hsn_name']) && !empty($_REQUEST['_hsn_name'])){
            $subsql .=" AND hsn.hsn_name = '".$_REQUEST['_hsn_name']."'";
            $record['filter']['_hsn_name']['value'] = $_REQUEST['_hsn_name'];
            $record['filter']['_hsn_name']['text']  = $_REQUEST['_hsn_name'];
        }
        if(isset($_REQUEST['_entry_date_from'])){
            if($_REQUEST['_entry_date_from'] != ''){
                $subsql .=" AND om.om_entry_date >= '".$_REQUEST['_entry_date_from']."'";
                $record['filter']['_entry_date_from'] = $_REQUEST['_entry_date_from'];
            }
        }
        if(isset($_REQUEST['_entry_date_to'])){
            if($_REQUEST['_entry_date_to'] != ''){
                $subsql .=" AND om.om_entry_date <= '".$_REQUEST['_entry_date_to']."'";
                $record['filter']['_entry_date_to'] = $_REQUEST['_entry_date_to'];
            }
        }
        if(isset($_REQUEST['_qty_from'])){
            if($_REQUEST['_qty_from'] != ''){
                $subsql .=" AND ot.ot_qty >= ".$_REQUEST['_qty_from'];
                $record['filter']['_qty_from'] = $_REQUEST['_qty_from'];
            }
        }
        if(isset($_REQUEST['_qty_to'])){
            if($_REQUEST['_qty_to'] != ''){
                $subsql .=" AND ot.ot_qty <= ".$_REQUEST['_qty_to'];
                $record['filter']['_qty_to'] = $_REQUEST['_qty_to'];
            }
        }
        if(isset($_REQUEST['_mtr_from'])){
            if($_REQUEST['_mtr_from'] != ''){
                $subsql .=" AND ot.ot_mtr >= ".$_REQUEST['_mtr_from'];
                $record['filter']['_mtr_from'] = $_REQUEST['_mtr_from'];
            }
        }
        if(isset($_REQUEST['_mtr_to'])){
            if($_REQUEST['_mtr_to'] != ''){
                $subsql .=" AND ot.ot_mtr <= ".$_REQUEST['_mtr_to'];
                $record['filter']['_mtr_to'] = $_REQUEST['_mtr_to'];
            }
        }
        if(isset($_REQUEST['_total_mtr_from'])){
            if($_REQUEST['_total_mtr_from'] != ''){
                $subsql .=" AND ot.ot_total_mtr >= ".$_REQUEST['_total_mtr_from'];
                $record['filter']['_total_mtr_from'] = $_REQUEST['_total_mtr_from'];
            }
        }
        if(isset($_REQUEST['_total_mtr_to'])){
            if($_REQUEST['_total_mtr_to'] != ''){
                $subsql .=" AND ot.ot_total_mtr <= ".$_REQUEST['_total_mtr_to'];
                $record['filter']['_total_mtr_to'] = $_REQUEST['_total_mtr_to'];
            }
        }
        if(isset($_REQUEST['_rate_from'])){
            if($_REQUEST['_rate_from'] != ''){
                $subsql .=" AND ot.ot_rate >= ".$_REQUEST['_rate_from'];
                $record['filter']['_rate_from'] = $_REQUEST['_rate_from'];
            }
        }
        if(isset($_REQUEST['_rate_to'])){
            if($_REQUEST['_rate_to'] != ''){
                $subsql .=" AND ot.ot_rate <= ".$_REQUEST['_rate_to'];
                $record['filter']['_rate_to'] = $_REQUEST['_rate_to'];
            }
        }
        if(isset($_REQUEST['_sub_amt_from'])){
            if($_REQUEST['_sub_amt_from'] != ''){
                $subsql .=" AND ot.ot_amt >= ".$_REQUEST['_sub_amt_from'];
                $record['filter']['_sub_amt_from'] = $_REQUEST['_sub_amt_from'];
            }
        }
        if(isset($_REQUEST['_sub_amt_to'])){
            if($_REQUEST['_sub_amt_to'] != ''){
                $subsql .=" AND ot.ot_amt <= ".$_REQUEST['_sub_amt_to'];
                $record['filter']['_sub_amt_to'] = $_REQUEST['_sub_amt_to'];
            }
        }
        if(isset($_REQUEST['_disc_amt_from'])){
            if($_REQUEST['_disc_amt_from'] != ''){
                $subsql .=" AND ot.ot_disc_amt >= ".$_REQUEST['_disc_amt_from'];
                $record['filter']['_disc_amt_from'] = $_REQUEST['_disc_amt_from'];
            }
        }
        if(isset($_REQUEST['_disc_amt_to'])){
            if($_REQUEST['_disc_amt_to'] != ''){
                $subsql .=" AND ot.ot_disc_amt <= ".$_REQUEST['_disc_amt_to'];
                $record['filter']['_disc_amt_to'] = $_REQUEST['_disc_amt_to'];
            }
        }
        if(isset($_REQUEST['_taxable_amt_from'])){
            if($_REQUEST['_taxable_amt_from'] != ''){
                $subsql .=" AND ot.ot_taxable_amt >= ".$_REQUEST['_taxable_amt_from'];
                $record['filter']['_taxable_amt_from'] = $_REQUEST['_taxable_amt_from'];
            }
        }
        if(isset($_REQUEST['_taxable_amt_to'])){
            if($_REQUEST['_taxable_amt_to'] != ''){
                $subsql .=" AND ot.ot_taxable_amt <= ".$_REQUEST['_taxable_amt_to'];
                $record['filter']['_taxable_amt_to'] = $_REQUEST['_taxable_amt_to'];
            }
        }
        if(isset($_REQUEST['_sgst_amt_from'])){
            if($_REQUEST['_sgst_amt_from'] != ''){
                $subsql .=" AND ot.ot_sgst_amt >= ".$_REQUEST['_sgst_amt_from'];
                $record['filter']['_sgst_amt_from'] = $_REQUEST['_sgst_amt_from'];
            }
        }
        if(isset($_REQUEST['_sgst_amt_to'])){
            if($_REQUEST['_sgst_amt_to'] != ''){
                $subsql .=" AND ot.ot_sgst_amt <= ".$_REQUEST['_sgst_amt_to'];
                $record['filter']['_sgst_amt_to'] = $_REQUEST['_sgst_amt_to'];
            }
        }
        if(isset($_REQUEST['_cgst_amt_from'])){
            if($_REQUEST['_cgst_amt_from'] != ''){
                $subsql .=" AND ot.ot_cgst_amt >= ".$_REQUEST['_cgst_amt_from'];
                $record['filter']['_cgst_amt_from'] = $_REQUEST['_cgst_amt_from'];
            }
        }
        if(isset($_REQUEST['_cgst_amt_to'])){
            if($_REQUEST['_cgst_amt_to'] != ''){
                $subsql .=" AND ot.ot_cgst_amt <= ".$_REQUEST['_cgst_amt_to'];
                $record['filter']['_cgst_amt_to'] = $_REQUEST['_cgst_amt_to'];
            }
        }
        if(isset($_REQUEST['_igst_amt_from'])){
            if($_REQUEST['_igst_amt_from'] != ''){
                $subsql .=" AND ot.ot_igst_amt >= ".$_REQUEST['_igst_amt_from'];
                $record['filter']['_igst_amt_from'] = $_REQUEST['_igst_amt_from'];
            }
        }
        if(isset($_REQUEST['_igst_amt_to'])){
            if($_REQUEST['_igst_amt_to'] != ''){
                $subsql .=" AND ot.ot_igst_amt <= ".$_REQUEST['_igst_amt_to'];
                $record['filter']['_igst_amt_to'] = $_REQUEST['_igst_amt_to'];
            }
        }
        if(isset($_REQUEST['_total_amt_from'])){
            if($_REQUEST['_total_amt_from'] != ''){
                $subsql .=" AND ot.ot_total_amt >= ".$_REQUEST['_total_amt_from'];
                $record['filter']['_total_amt_from'] = $_REQUEST['_total_amt_from'];
            }
        }
        if(isset($_REQUEST['_total_amt_to'])){
            if($_REQUEST['_total_amt_to'] != ''){
                $subsql .=" AND ot.ot_total_amt <= ".$_REQUEST['_total_amt_to'];
                $record['filter']['_total_amt_to'] = $_REQUEST['_total_amt_to'];
            }
        }
        $query="SELECT IF(om.om_status=0,'ESTIMATE','ORDER') as module_name, 
                IF(om.om_status=0,om.om_em_entry_no,om.om_entry_no) as entry_no,
                IF(om.om_status=0,DATE_FORMAT(om.om_em_entry_date, '%d-%m-%Y'),DATE_FORMAT(om.om_entry_date, '%d-%m-%Y')) as entry_date,
                UPPER(customer.customer_name) as customer_name,
                customer.customer_mobile as customer_mobile,
                ot.ot_trans_type as trans_type,
                ot.ot_qty as qty, 
                ot.ot_mtr as mtr,
                ot.ot_total_mtr as total_mtr,
                ot.ot_rate as rate,
                ot.ot_amt as sub_amt,
                ot.ot_disc_per as disc_per,
                ot.ot_disc_amt as disc_amt,
                ot.ot_taxable_amt as taxable_amt,
                ot.ot_sgst_per as sgst_per,
                ot.ot_sgst_amt as sgst_amt,
                ot.ot_cgst_per as cgst_per,
                ot.ot_cgst_amt as cgst_amt,
                ot.ot_igst_per as igst_per,
                ot.ot_igst_amt as igst_amt,
                ot.ot_total_amt as total_amt,
                IFNULL(UPPER(apparel.apparel_name), '') as apparel_name,
                IFNULL(UPPER(fabric.fabric_name), '') as fabric_name,
                IFNULL(UPPER(design.design_name), '') as design_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(width.width_name), '') as width_name,
                IFNULL(UPPER(hsn.hsn_name), '') as hsn_name,
                om.om_created_at as created_at
                FROM order_master om
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                LEFT JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                LEFT JOIN barcode_master bm ON(bm.bm_id = ot.ot_bm_id)
                LEFT JOIN fabric_master fabric ON(fabric.fabric_id = bm.bm_fabric_id)
                LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
                LEFT JOIN color_master color ON(color.color_id = bm.bm_color_id)
                LEFT JOIN width_master width ON(width.width_id = bm.bm_width_id)
                LEFT JOIN hsn_master hsn ON(hsn.hsn_id = bm.bm_hsn_id)
                WHERE om.om_delete_status = 0
                AND ot.ot_delete_status = 0
                $subsql
                ORDER BY created_at DESC";
        $data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit();
        // echo "<pre>"; print_r($data); exit();
        
        $record['totals']['rows']           = count($data);
        $record['totals']['qty']            = 0;
        $record['totals']['total_mtr']      = 0;
        $record['totals']['sub_amt']        = 0;
        $record['totals']['disc_amt']       = 0;
        $record['totals']['taxable_amt']    = 0;
        $record['totals']['sgst_amt']       = 0;
        $record['totals']['cgst_amt']       = 0;
        $record['totals']['igst_amt']       = 0;
        $record['totals']['total_amt']      = 0;
        $record['data']                     = [];
        if(!empty($data)){
            foreach ($data as $key => $value) {
                array_push($record['data'], [
                                                'module_name'       => $value['module_name'],
                                                'entry_no'          => (int)$value['entry_no'],
                                                'entry_date' 	    => (int)strtotime($value['entry_date']),
                                                'entry_date1' 	    => $value['entry_date'],
                                                'customer_name'     => $value['customer_name'],
                                                'trans_type'        => $value['trans_type'],
                                                'qty' 		        => (int)$value['qty'],
                                                'mtr' 		        => (int)$value['mtr'],
                                                'rate' 		        => (float)$value['rate'],
                                                'total_mtr' 		=> (float)$value['total_mtr'],
                                                'sub_amt' 		    => (float)$value['sub_amt'],
                                                'disc_amt' 		    => (float)$value['disc_amt'],
                                                'taxable_amt' 		=> (float)$value['taxable_amt'],
                                                'sgst_amt' 		    => (float)$value['sgst_amt'],
                                                'cgst_amt' 		    => (float)$value['cgst_amt'],
                                                'igst_amt' 		    => (float)$value['igst_amt'],
                                                'total_amt'         => (float)$value['total_amt'],
                                                'apparel_name'      => $value['apparel_name'],
                                                'fabric_name'       => $value['fabric_name'],
                                                'design_name'       => $value['design_name'],
                                                'color_name'        => $value['color_name'],
                                                'width_name'        => $value['width_name'],
                                                'hsn_name'          => $value['hsn_name'],
                                                'created_at'        => strtotime($value['created_at']),
                                            ]);

                $record['totals']['total_mtr'] 	    = $record['totals']['total_mtr'] 		+ $value['total_mtr'];
                $record['totals']['sub_amt'] 	    = $record['totals']['sub_amt'] 		    + $value['sub_amt'];
                $record['totals']['disc_amt'] 	    = $record['totals']['disc_amt'] 		+ $value['disc_amt'];
                $record['totals']['taxable_amt'] 	= $record['totals']['taxable_amt'] 		+ $value['taxable_amt'];
                $record['totals']['sgst_amt'] 	    = $record['totals']['sgst_amt'] 		+ $value['sgst_amt'];
                $record['totals']['cgst_amt'] 	    = $record['totals']['cgst_amt'] 		+ $value['cgst_amt'];
                $record['totals']['igst_amt'] 	    = $record['totals']['igst_amt'] 		+ $value['igst_amt'];
                $record['totals']['total_amt'] 	    = $record['totals']['total_amt'] 		+ $value['total_amt'];
            }
        }
        
        // echo "<pre>"; print_r($record); exit();
        return $record;
    }
    public function _entry_no(){
        $subsql = '';
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
        $query="SELECT om.om_entry_no as id , 
                UPPER(om.om_entry_no) as name 
                FROM order_master om 
                WHERE om.om_delete_status = 0
                GROUP BY om.om_entry_no ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _customer_name(){
        $subsql = '';
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
        $query="SELECT customer.customer_name as id , 
                UPPER(customer.customer_name) as name 
                FROM order_master om 
                INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
                WHERE om.om_delete_status = 0
                $subsql
                GROUP BY customer.customer_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _apparel_name(){
        $subsql = '';
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
            $subsql .= " AND (apparel.apparel_name LIKE '%".$name."%') ";
        }
        $query="SELECT apparel.apparel_name as id, 
                UPPER(apparel.apparel_name) as name 
                FROM order_master om 
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = ot.ot_apparel_id)
                WHERE om.om_delete_status = 0
                AND ot.ot_delete_status = 0
                $subsql
                GROUP BY apparel.apparel_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _fabric_name(){
        $subsql = '';
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
            $subsql .= " AND (fabric.fabric_name LIKE '%".$name."%') ";
        }
        $query="SELECT fabric.fabric_name as id, 
                UPPER(fabric.fabric_name) as name 
                FROM order_master om 
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_om_id)
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = bm.bm_fabric_id)
                WHERE om.om_delete_status = 0
                AND ot.ot_delete_status = 0
                $subsql
                GROUP BY fabric.fabric_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _design_name(){
        $subsql = '';
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
            $subsql .= " AND (design.design_name LIKE '%".$name."%') ";
        }
        $query="SELECT design.design_name as id, 
                UPPER(design.design_name) as name 
                FROM order_master om 
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_om_id)
                LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
                WHERE om.om_delete_status = 0
                AND ot.ot_delete_status = 0
                $subsql
                GROUP BY design.design_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _color_name(){
        $subsql = '';
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
            $subsql .= " AND (color.color_name LIKE '%".$name."%') ";
        }
        $query="SELECT color.color_name as id, 
                UPPER(color.color_name) as name 
                FROM order_master om 
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_om_id)
                INNER JOIN color_master color ON(color.color_id = bm.bm_color_id)
                WHERE om.om_delete_status = 0
                AND ot.ot_delete_status = 0
                $subsql
                GROUP BY color.color_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _width_name(){
        $subsql = '';
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
            $subsql .= " AND (width.width_name LIKE '%".$name."%') ";
        }
        $query="SELECT width.width_name as id, 
                UPPER(width.width_name) as name 
                FROM order_master om 
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_om_id)
                INNER JOIN width_master width ON(width.width_id = bm.bm_width_id)
                WHERE om.om_delete_status = 0
                AND ot.ot_delete_status = 0
                $subsql
                GROUP BY width.width_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _hsn_name(){
        $subsql = '';
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
            $subsql .= " AND (hsn.hsn_name LIKE '%".$name."%') ";
        }
        $query="SELECT hsn.hsn_name as id, 
                UPPER(hsn.hsn_name) as name 
                FROM order_master om 
                INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
                INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_om_id)
                INNER JOIN hsn_master hsn ON(hsn.hsn_id = bm.bm_hsn_id)
                WHERE om.om_delete_status = 0
                AND ot.ot_delete_status = 0
                $subsql
                GROUP BY hsn.hsn_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
}
?>