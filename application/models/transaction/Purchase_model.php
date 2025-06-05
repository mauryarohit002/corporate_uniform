<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class purchase_model extends my_model{
    public function __construct(){ parent::__construct('transaction', 'purchase'); }
    public function isExist($id){
        $data = $this->db->query("SELECT pm_id FROM purchase_master WHERE pm_delete_status = 0 AND pm_allocated_amt > 0 AND pm_id = $id LIMIT 1")->result_array();
        if(!empty($data)) return true;

        $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_pm_id = $id AND (bm_ot_mtr) > 0 LIMIT 1")->result_array();
        if(!empty($data)) return true;

        return false; 
    }
    public function isTransExist($id){
        $query="SELECT pm.pm_id 
                FROM purchase_master pm
                INNER JOIN purchase_trans pt ON(pt.pt_pm_id = pm.pm_id)
                WHERE pm.pm_delete_status = 0 
                AND pm.pm_allocated_amt > 0
                AND pt.pt_id = $id 
                LIMIT 1";
        $data = $this->db->query($query)->result_array();
        if(!empty($data)) return true;

        $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_pt_id = $id AND (bm_ot_mtr) > 0 LIMIT 1")->result_array();
        if(!empty($data)) return true;

        return false;
    }
    public function isBarcodeExist($id){
        $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_id = $id AND (bm_ot_mtr) > 0 LIMIT 1")->result_array();
        if(!empty($data)) return true;
        
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
            $subsql .=" AND pm.pm_entry_no = '".$_GET['_entry_no']."'";
            $record['filter']['_entry_no']['value'] = $_GET['_entry_no'];
            $record['filter']['_entry_no']['text'] = $_GET['_entry_no'];
        }
        if(isset($_GET['_bill_no']) && !empty($_GET['_bill_no'])){
            $subsql .=" AND pm.pm_bill_no = '".$_GET['_bill_no']."'";
            $record['filter']['_bill_no']['value'] = $_GET['_bill_no'];
            $record['filter']['_bill_no']['text'] = $_GET['_bill_no'];
        }
        if(isset($_GET['_order_no']) && !empty($_GET['_order_no'])){
            $subsql .=" AND pm.pm_order_no = '".$_GET['_order_no']."'";
            $record['filter']['_order_no']['value'] = $_GET['_order_no'];
            $record['filter']['_order_no']['text'] = $_GET['_order_no'];
        }
        if(isset($_GET['_entry_date_from']) && !empty($_GET['_entry_date_from'])){
            $_entry_date_from = date('Y-m-d', strtotime($_GET['_entry_date_from']));
            $subsql .= " AND pm.pm_entry_date >= '".$_entry_date_from."'";
        }
        if(isset($_GET['_entry_date_to']) && !empty($_GET['_entry_date_to'])){
            $_entry_date_to = date('Y-m-d', strtotime($_GET['_entry_date_to']));
            $subsql .= " AND pm.pm_entry_date <= '".$_entry_date_to."'";
        }
        if(isset($_GET['_supplier_name']) && !empty($_GET['_supplier_name'])){
            $subsql .=" AND supplier.supplier_name = '".$_GET['_supplier_name']."'";
            $record['filter']['_supplier_name']['value'] = $_GET['_supplier_name'];
            $record['filter']['_supplier_name']['text'] = $_GET['_supplier_name'];
        } 
        $query="SELECT pm.*, 
                UPPER(supplier.supplier_name) as supplier_name
                FROM purchase_master pm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = pm.pm_supplier_id)
                WHERE pm.pm_delete_status = 0
                $subsql
                ORDER BY pm.pm_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['pm_id']);
            }
        }
        return $record;
    }
    public function get_data_for_add(){
        $record['pm_entry_no'] 	= $this->get_max_entry_no(['entry_no' => 'pm_entry_no', 'delete_status' => 'pm_delete_status', 'fin_year' => 'pm_fin_year','branch_id' => 'pm_branch_id']);
        $record['pm_uuid'] 	    = $_SESSION['user_id'].''.time();
        $record['cost_char'] 	= $this->db_operations->get_record('cost_char_master', ['cost_char_id' => 1]);
        return $record;
    }
    public function get_supplier_state($id){
        $query="SELECT supplier.supplier_state_id as state_id
                FROM supplier_master supplier
                WHERE supplier.supplier_id = $id";
        return $this->db->query($query)->result_array();
    }
    public function generate_barcode(){
        $year   = date('Y');
        $month  = date('m');
        $query  = "SELECT bm.bm_counter as counter 
                    FROM barcode_master bm 
                    WHERE bm.bm_barcode_year = '$year' 
                    AND bm.bm_barcode_month = '$month'
                    ORDER BY bm.bm_counter DESC
                    LIMIT 1";
        // echo "<pre>"; print_r($query); exit;
        $data = $this->db->query($query)->result_array();
        return empty($data[0]['counter']) ? 10000001 : ($data[0]['counter'] + 1);
    }
    public function get_data_for_edit($id){
        $query="SELECT pm.*,
                UPPER(supplier.supplier_name) as supplier_name
                FROM purchase_master pm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = pm.pm_supplier_id)
                WHERE pm.pm_id = $id
                AND pm.pm_delete_status = 0";
        $record['master_data'] = $this->db->query($query)->result_array();
        if(!empty($record['master_data'])){
            $record['master_data'][0]['isExist'] = $this->isExist($id);
        }
        $record['cost_char'] 	= $this->db_operations->get_record('cost_char_master', ['cost_char_id' => 1]);
        return $record;
    }
    public function get_transaction($pm_id){
        $query="SELECT pt.*,
                UPPER(fabric.fabric_name) as fabric_name,
                UPPER(design.design_name) as design_name,
                IFNULL(UPPER(category.category_name), '') as category_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(width.width_name), '') as width_name,
                IFNULL(UPPER(hsn.hsn_name), '') as hsn_name,
                design.design_image
                FROM purchase_trans pt
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = pt.pt_fabric_id)
                INNER JOIN design_master design ON(design.design_id = pt.pt_design_id)
                LEFT JOIN category_master category ON(category.category_id = pt.pt_category_id)
                LEFT JOIN color_master color ON(color.color_id = pt.pt_color_id)
                LEFT JOIN width_master width ON(width.width_id = pt.pt_width_id)
                LEFT JOIN hsn_master hsn ON(hsn.hsn_id = pt.pt_hsn_id)
                WHERE pt.pt_pm_id = $pm_id
                AND pt.pt_delete_status = 0
                ORDER BY pt.pt_id DESC";
        $record = $this->db->query($query)->result_array();
        if(!empty($record)){
            foreach ($record as $key => $value) {
                $record[$key]['sr_no']          = $key+1;
                $record[$key]['isExist'] 		= $this->isTransExist($value['pt_id']);
                $record[$key]['encrypt_pt_id'] 	= encrypt_decrypt("encrypt", $value['pt_id'], SECRET_KEY);
            }
        }
        return $record;
    }
    public function get_data_for_qrcode_print($clause, $_id){ 
        $rollno= ENV == PROD ? 'bm.bm_roll_no' : 0;
        $query ="SELECT 
                UPPER(fabric.fabric_name) as fabric_name,
                UPPER(design.design_name) as design_name,
                IFNULL(UPPER(category.category_name), '') as category_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(width.width_name), '') as width_name,
                UPPER(bm.bm_description) as description,
                bm.bm_roll_no as qrcode, 
                bm.bm_pt_mtr as mtr, 
                bm.bm_mrp as mrp,
                bm.bm_shirt_mrp as shirt_mrp,
                bm.bm_trouser_mrp as trouser_mrp,
                bm.bm_2pc_suit_mrp as twopc_suit_mrp,
                bm.bm_3pc_suit_mrp as threepc_suit_mrp, 
                bm.bm_jacket_mrp as jacket_mrp, 

                bm.bm_pt_rate as rate,
                -- CONCAT('R', '', bm.bm_cost_char) as cost_char,
                bm.bm_cost_char as cost_char,
                $rollno as roll_no
                FROM barcode_master bm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = bm.bm_supplier_id)
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = bm.bm_fabric_id)
                INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
                LEFT JOIN category_master category ON(category.category_id = bm.bm_category_id)
                LEFT JOIN color_master color ON(color.color_id = bm.bm_color_id)
                LEFT JOIN width_master width ON(width.width_id = bm.bm_width_id)
                WHERE bm.bm_delete_status = 0 
                AND ".$clause." = $_id";
        // echo "<pre>"; print_r($query); exit();
        $data['barcode_data'] = $this->db->query($query)->result_array();
        $data['company_data'] = $this->db_operations->get_record('company_master', ['company_id' => 1]);
          // echo "<pre>"; print_r($data); exit();
        return $data;
    }
    public function get_data_for_print($pm_id){
        $query="SELECT 
                pm.*,
                pm.pm_entry_no as entry_no, 
                DATE_FORMAT(pm.pm_entry_date, '%d-%m-%Y') as entry_date,
                DATE_FORMAT(pm.pm_bill_date, '%d-%m-%Y') as bill_date,
                pm.pm_bill_no as bill_no,
                pm.pm_notes as notes,
                pm.pm_total_qty as total_qty,
                pm.pm_total_mtr as total_mtr,
                pm.pm_total_amt as total_amt,
                UPPER(supplier.supplier_name) as supplier_name,
                UPPER(supplier.supplier_address) as supplier_address
                FROM purchase_master pm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = pm.pm_supplier_id)
                WHERE pm.pm_delete_status = 0 
                AND pm.pm_id = $pm_id";
        // echo "<pre>"; print_r($query); exit();
        $record['master_data'] = $this->db->query($query)->result_array();

        $query="SELECT 
                UPPER(fabric.fabric_name) as fabric_name,
                UPPER(design.design_name) as design_name,
                IFNULL(UPPER(category.category_name), '') as category_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(width.width_name), '') as width_name,
                SUM(pt.pt_qty) as qty, 
                SUM(pt.pt_mtr) as mtr,
                SUM(pt.pt_total_mtr) as total_mtr,
                pt.pt_rate as rate,
                SUM(pt.pt_amt) as amt
                FROM purchase_trans pt
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = pt.pt_fabric_id)
                INNER JOIN design_master design ON(design.design_id = pt.pt_design_id)
                LEFT JOIN category_master category ON(category.category_id = pt.pt_category_id)
                LEFT JOIN color_master color ON(color.color_id = pt.pt_color_id)
                LEFT JOIN width_master width ON(width.width_id = pt.pt_width_id)
                WHERE pt.pt_delete_status = 0 
                AND pt.pt_pm_id = $pm_id
                GROUP BY fabric.fabric_id, design.design_id, category.category_id,color.color_id, pt.pt_rate
                ORDER BY fabric.fabric_name, design.design_name, color.color_name, pt.pt_rate ASC";
        // echo "<pre>"; print_r($query); exit();
        $record['trans_data'] = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($record); exit();

        return $record;
    }
    public function get_design_image($id){
        $query="SELECT design.design_image as image
                FROM design_master design
                WHERE design.design_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? assets(NOIMAGE) : $data[0]['image'];
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
            $subsql .= " AND (pm.pm_entry_no LIKE '%".$name."%') ";
        }
        $query="SELECT pm.pm_entry_no as id, UPPER(pm.pm_entry_no) as name
                FROM purchase_master pm
                WHERE 1
                $subsql
                GROUP BY pm.pm_entry_no ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _bill_no(){
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
            $subsql .= " AND (pm.pm_bill_no LIKE '%".$name."%') ";
        }
        $query="SELECT pm.pm_bill_no as id, UPPER(pm.pm_bill_no) as name
                FROM purchase_master pm
                WHERE 1
                $subsql
                GROUP BY pm.pm_bill_no ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _supplier_name(){
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
            $subsql .= " AND (supplier.supplier_name LIKE '%".$name."%') ";
        }
        $query="SELECT supplier.supplier_name as id, UPPER(supplier.supplier_name) as name
                FROM purchase_master pm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = pm.pm_supplier_id)
                WHERE 1
                $subsql
                GROUP BY supplier.supplier_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }


    public function get_data_for_barcode_print($_id){
        $rollno= ENV == PROD ? 'bm.bm_roll_no' : 0;
        $query ="SELECT 
                design.design_id,
                UPPER(fabric.fabric_name) as fabric_name,
                UPPER(design.design_name) as design_name,
                UPPER(hsn.hsn_name) as hsn_name,
                IFNULL(UPPER(category.category_name), '') as category_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(width.width_name), '') as width_name,
                UPPER(bm.bm_description) as description,
                bm.bm_roll_no as qrcode, 
                bm.bm_pt_mtr as mtr, 
                bm.bm_mrp as mrp, 
                bm.bm_pt_rate as rate,
                CONCAT('R', '', bm.bm_cost_char) as cost_char,
                $rollno as roll_no
                FROM barcode_master bm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = bm.bm_supplier_id)
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = bm.bm_fabric_id)
                INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
                LEFT JOIN category_master category ON(category.category_id = bm.bm_category_id)
                LEFT JOIN color_master color ON(color.color_id = bm.bm_color_id)
                LEFT JOIN width_master width ON(width.width_id = bm.bm_width_id)
                LEFT JOIN hsn_master hsn ON(hsn.hsn_id = design.design_hsn_id)
                WHERE bm.bm_delete_status = 0 
                AND bm.bm_pm_id = $_id";
        // echo "<pre>"; print_r($query); exit();
        $data['barcode_data'] = $this->db->query($query)->result_array();
        if(!empty($data['barcode_data']))
        {
            foreach ($data['barcode_data'] as $key => $value) {
                 $query ="SELECT 
                    dt.design_trans_consumption as consumption,
                     UPPER(apparel.apparel_charges) as apparel_charges,
                    UPPER(apparel.apparel_name) as apparel_name,
                    UPPER(width.width_name) as width_name
                    FROM design_trans dt
                    INNER JOIN apparel_master apparel ON(dt.design_trans_apparel_id = apparel.apparel_id)
                    LEFT JOIN width_master width ON(dt.design_trans_width_id = width.width_id)
                    WHERE dt.design_trans_design_id ='".$value['design_id']."' ";
            $data['barcode_data'][$key]['trans_cnt'] = $this->db->query($query)->num_rows();   
            $data['barcode_data'][$key]['design_trans'] = $this->db->query($query)->result_array(); 
            }
        }
      
        return $data;
    }

}?>