<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class purchase_readymade_model extends my_model{
    public function __construct(){ parent::__construct('transaction', 'purchase_readymade'); }
    public function isExist($id){
        $data = $this->db->query("SELECT brmm_id FROM barcode_readymade_master WHERE brmm_prmm_id = $id AND (brmm_ot_qty) > 0 LIMIT 1")->result_array();
        if(!empty($data)) return true;

        $data = $this->db->query("SELECT brmm_id FROM barcode_readymade_master WHERE brmm_prmm_id = $id AND (brmm_prrt_qty) > 0 LIMIT 1")->result_array();
        if(!empty($data)) return true;

        return false; 
    }
    public function isTransExist($id){
        $data = $this->db->query("SELECT brmm_id FROM barcode_readymade_master WHERE brmm_prmt_id = $id AND (brmm_ot_qty) > 0 LIMIT 1")->result_array();
        if(!empty($data)) return true;

        $data = $this->db->query("SELECT brmm_id FROM barcode_readymade_master WHERE brmm_prmt_id = $id AND (brmm_prrt_qty) > 0 LIMIT 1")->result_array();
        if(!empty($data)) return true;

        return false;
    }
    public function isBarcodeExist($id){
        $data = $this->db->query("SELECT brmm_id FROM barcode_readymade_master WHERE brmm_id = $id AND (brmm_ot_qty) > 0 LIMIT 1")->result_array();
        if(!empty($data)) return true;

        $data = $this->db->query("SELECT brmm_id FROM barcode_readymade_master WHERE brmm_id = $id AND (brmm_prrt_qty) > 0 LIMIT 1")->result_array();
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
            $subsql .=" AND prmm.prmm_entry_no = '".$_GET['_entry_no']."'";
            $record['filter']['_entry_no']['value'] = $_GET['_entry_no'];
            $record['filter']['_entry_no']['text'] = $_GET['_entry_no'];
        }
        if(isset($_GET['_bill_no']) && !empty($_GET['_bill_no'])){
            $subsql .=" AND prmm.prmm_bill_no = '".$_GET['_bill_no']."'";
            $record['filter']['_bill_no']['value'] = $_GET['_bill_no'];
            $record['filter']['_bill_no']['text'] = $_GET['_bill_no'];
        }
        if(isset($_GET['_order_no']) && !empty($_GET['_order_no'])){
            $subsql .=" AND prmm.prmm_order_no = '".$_GET['_order_no']."'";
            $record['filter']['_order_no']['value'] = $_GET['_order_no'];
            $record['filter']['_order_no']['text'] = $_GET['_order_no'];
        }
        if(isset($_GET['_entry_date_from']) && !empty($_GET['_entry_date_from'])){
            $_entry_date_from = date('Y-m-d', strtotime($_GET['_entry_date_from']));
            $subsql .= " AND prmm.prmm_entry_date >= '".$_entry_date_from."'";
        }
        if(isset($_GET['_entry_date_to']) && !empty($_GET['_entry_date_to'])){
            $_entry_date_to = date('Y-m-d', strtotime($_GET['_entry_date_to']));
            $subsql .= " AND prmm.prmm_entry_date <= '".$_entry_date_to."'";
        }
        if(isset($_GET['_supplier_name']) && !empty($_GET['_supplier_name'])){
            $subsql .=" AND supplier.supplier_name = '".$_GET['_supplier_name']."'";
            $record['filter']['_supplier_name']['value'] = $_GET['_supplier_name'];
            $record['filter']['_supplier_name']['text'] = $_GET['_supplier_name'];
        } 
        $query="SELECT prmm.*, 
                UPPER(supplier.supplier_name) as supplier_name
                FROM purchase_readymade_master prmm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = prmm.prmm_supplier_id)
                WHERE prmm.prmm_delete_status = 0
                $subsql
                ORDER BY prmm.prmm_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['prmm_id']);
            }
        }
        return $record;
    }
    public function get_data_for_add(){
        $record['prmm_entry_no'] 	= $this->get_max_entry_no(['entry_no' => 'prmm_entry_no', 'delete_status' => 'prmm_delete_status', 'fin_year' => 'prmm_fin_year', 'branch_id' => 'prmm_branch_id']);
        $record['prmm_uuid'] 	    = $_SESSION['user_id'].''.time();
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
        $query  = "SELECT brmm.brmm_counter as counter 
                    FROM barcode_readymade_master brmm 
                    WHERE brmm.brmm_barcode_year = '$year' 
                    AND brmm.brmm_barcode_month = '$month'
                    ORDER BY brmm.brmm_counter DESC
                    LIMIT 1";
        // echo "<pre>"; print_r($query); exit;
        $data = $this->db->query($query)->result_array();
        return empty($data[0]['counter']) ? 10000001 : ($data[0]['counter'] + 1);
    }
    public function get_data_for_edit($id){
        $query="SELECT prmm.*,
                UPPER(supplier.supplier_name) as supplier_name
                FROM purchase_readymade_master prmm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = prmm.prmm_supplier_id)
                WHERE prmm.prmm_id = $id
                AND prmm.prmm_delete_status = 0";
        $record['master_data'] = $this->db->query($query)->result_array();
        if(!empty($record['master_data'])){
            $record['master_data'][0]['isExist'] = $this->isExist($id);
        }
        $record['cost_char'] 	= $this->db_operations->get_record('cost_char_master', ['cost_char_id' => 1]);
        return $record;
    }
    public function get_transaction($prmm_id){
        $query="SELECT prmt.*,
                UPPER(readymade_category.readymade_category_name) as readymade_category_name,
                UPPER(product.product_name) as product_name,
                IFNULL(UPPER(design.design_name), '') as design_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(size.size_name), '') as size_name,
                IFNULL(UPPER(gender.gender_name), '') as gender_name,
                design.design_image
                FROM purchase_readymade_trans prmt
                INNER JOIN readymade_category_master readymade_category ON(readymade_category.readymade_category_id = prmt.prmt_readymade_category_id)
                LEFT JOIN product_master product ON(product.product_id = prmt.prmt_product_id)
                LEFT JOIN design_master design ON(design.design_id = prmt.prmt_design_id)
                LEFT JOIN color_master color ON(color.color_id = prmt.prmt_color_id)
                LEFT JOIN size_master size ON(size.size_id = prmt.prmt_size_id)
                LEFT JOIN gender_master gender ON(gender.gender_id = prmt.prmt_gender_id)
                WHERE prmt.prmt_prmm_id = $prmm_id
                AND prmt.prmt_delete_status = 0
                ORDER BY prmt.prmt_id DESC";
        $record = $this->db->query($query)->result_array();
        if(!empty($record)){
            foreach ($record as $key => $value) {
                $record[$key]['isExist'] 		= $this->isTransExist($value['prmt_id']);
                $record[$key]['encrypt_prmt_id'] 	= encrypt_decrypt("encrypt", $value['prmt_id'], SECRET_KEY);
            }
        }
        return $record;
    }
    public function get_data_for_qrcode_print($clause, $_id){ 
        $rollno= ENV == PROD ? 'brmm.brmm_roll_no' : 0;
        $query ="SELECT 
                UPPER(product.product_name) as product_name,
                UPPER(design.design_name) as design_name,
                UPPER(readymade_category.readymade_category_name) as readymade_category_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(size.size_name), '') as size_name,
                UPPER(brmm.brmm_description) as description,
                brmm.brmm_roll_no as qrcode,
                brmm.brmm_prmt_qty as qty, 
                brmm.brmm_mrp as mrp, 
                brmm.brmm_prmt_rate as rate,
                -- CONCAT('R', '', brmm.brmm_cost_char) as cost_char,
                brmm.brmm_cost_char as cost_char,
                $rollno as roll_no
                FROM barcode_readymade_master brmm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = brmm.brmm_supplier_id)
                INNER JOIN readymade_category_master readymade_category ON(readymade_category.readymade_category_id = brmm.brmm_readymade_category_id)
                LEFT JOIN product_master product ON(product.product_id = brmm.brmm_product_id)
                LEFT JOIN design_master design ON(design.design_id = brmm.brmm_design_id)
                LEFT JOIN color_master color ON(color.color_id = brmm.brmm_color_id)
                LEFT JOIN size_master size ON(size.size_id = brmm.brmm_size_id)
                WHERE brmm.brmm_delete_status = 0 
                AND ".$clause." = $_id";
        // echo "<pre>"; print_r($query); exit();
        $data['barcode_data'] = $this->db->query($query)->result_array();
        $data['company_data'] = $this->db_operations->get_record('company_master', ['company_id' => 1]);
        return $data;
    }
    public function get_data_for_print($prmm_id){
        $query="SELECT 
                prmm.*,
                prmm.prmm_entry_no as entry_no, 
                DATE_FORMAT(prmm.prmm_entry_date, '%d-%m-%Y') as entry_date,
                DATE_FORMAT(prmm.prmm_bill_date, '%d-%m-%Y') as bill_date,
                prmm.prmm_bill_no as bill_no,
                prmm.prmm_notes as notes,
                prmm.prmm_total_qty as total_qty,
                prmm.prmm_total_amt as total_amt,
                UPPER(supplier.supplier_name) as supplier_name,
                UPPER(supplier.supplier_address) as supplier_address
                FROM purchase_readymade_master prmm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = prmm.prmm_supplier_id)
                WHERE prmm.prmm_delete_status = 0 
                AND prmm.prmm_id = $prmm_id";
        // echo "<pre>"; print_r($query); exit();
        $record['master_data'] = $this->db->query($query)->result_array();

        $query="SELECT 
                UPPER(readymade_category.readymade_category_name) as readymade_category_name,
                UPPER(product.product_name) as product_name,
                 IFNULL(UPPER(design.design_name), '') as design_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(size.size_name), '') as size_name,
                SUM(prmt.prmt_qty) as qty, 
                prmt.prmt_rate as rate,
                SUM(prmt.prmt_amt) as amt
                FROM purchase_readymade_trans prmt
                INNER JOIN readymade_category_master readymade_category ON(readymade_category.readymade_category_id = prmt.prmt_readymade_category_id)
                LEFT JOIN product_master product ON(product.product_id = prmt.prmt_product_id)
                LEFT JOIN design_master design ON(design.design_id = prmt.prmt_design_id)
                LEFT JOIN color_master color ON(color.color_id = prmt.prmt_color_id)
                LEFT JOIN size_master size ON(size.size_id = prmt.prmt_size_id)
                WHERE prmt.prmt_delete_status = 0 
                AND prmt.prmt_prmm_id = $prmm_id
                GROUP BY readymade_category.readymade_category_id, product.product_id,design.design_id, color.color_id, prmt.prmt_rate
                ORDER BY readymade_category.readymade_category_name, design.design_name, color.color_name, prmt.prmt_rate ASC";
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
    public function get_barcode_qty($prmt_id){
        $data = $this->db->query("SELECT SUM(brmm_prmt_qty) as qty FROM barcode_readymade_master WHERE brmm_delete_status = false AND brmm_prmt_id = $prmt_id ")->result_array();
        if(!empty($data)){
            $qty = $data[0]["qty"];
        }else{
            $qty = 0;
        }
        return $qty;
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
            $subsql .= " AND (prmm.prmm_entry_no LIKE '%".$name."%') ";
        }
        $query="SELECT prmm.prmm_entry_no as id, UPPER(prmm.prmm_entry_no) as name
                FROM purchase_readymade_master prmm
                WHERE 1
                $subsql
                GROUP BY prmm.prmm_entry_no ASC
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
            $subsql .= " AND (prmm.prmm_bill_no LIKE '%".$name."%') ";
        }
        $query="SELECT prmm.prmm_bill_no as id, UPPER(prmm.prmm_bill_no) as name
                FROM purchase_readymade_master prmm
                WHERE 1
                $subsql
                GROUP BY prmm.prmm_bill_no ASC
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
                FROM purchase_readymade_master prmm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = prmm.prmm_supplier_id)
                WHERE 1
                $subsql
                GROUP BY supplier.supplier_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }


    public function get_data_for_barcode_print($_id){
        $rollno= ENV == PROD ? 'brmm.brmm_roll_no' : 0;
        $query ="SELECT 
                product.product_id, 
                design.design_id,
                UPPER(readymade_category.readymade_category_name) as readymade_category_name,
                UPPER(product.product_name) as product_name,
                UPPER(design.design_name) as design_name,
                UPPER(gender.gender_name) as gender_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(size.size_name), '') as size_name,
                UPPER(brmm.brmm_description) as description,
                brmm.brmm_roll_no as qrcode, 
                brmm.brmm_mrp as mrp, 
                brmm.brmm_prmt_rate as rate,
                CONCAT('R', '', brmm.brmm_cost_char) as cost_char,
                $rollno as roll_no
                FROM barcode_readymade_master brmm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = brmm.brmm_supplier_id)
                INNER JOIN readymade_category_master readymade_category ON(readymade_category.readymade_category_id = brmm.brmm_readymade_category_id)
                LEFT JOIN product_master product ON(product.product_id = brmm.brmm_product_id)
                INNER JOIN design_master design ON(design.design_id = brmm.brmm_design_id)
                LEFT JOIN color_master color ON(color.color_id = brmm.brmm_color_id)
                LEFT JOIN size_master size ON(size.size_id = brmm.brmm_size_id)
                LEFT JOIN gender_master gender ON(gender.gender_id = design.design_gender_id)
                WHERE brmm.brmm_delete_status = 0 
                AND brmm.brmm_prmm_id = $_id";
        // echo "<pre>"; print_r($query); exit();
        $data['barcode_data'] = $this->db->query($query)->result_array();
        if(!empty($data['barcode_data']))
        {
            foreach ($data['barcode_data'] as $key => $value) {
                 $query ="SELECT 
                    dt.design_trans_consumption as consumption,
                     UPPER(apparel.apparel_charges) as apparel_charges,
                    UPPER(apparel.apparel_name) as apparel_name,
                    UPPER(size.size_name) as size_name
                    FROM design_trans dt
                    INNER JOIN apparel_master apparel ON(dt.design_trans_apparel_id = apparel.apparel_id)
                    LEFT JOIN size_master size ON(dt.design_trans_size_id = size.size_id)
                    WHERE dt.design_trans_design_id ='".$value['design_id']."' ";
            $data['barcode_data'][$key]['trans_cnt'] = $this->db->query($query)->num_rows();   
            $data['barcode_data'][$key]['design_trans'] = $this->db->query($query)->result_array(); 
            }
        }
      // echo "<pre>"; print_r($data); exit();
        return $data;
    }

}?>