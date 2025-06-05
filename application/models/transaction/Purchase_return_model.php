<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class purchase_return_model extends my_model{
    public function __construct(){ parent::__construct('transaction', 'purchase_return'); }
    public function isExist($id){
        // $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_pm_id = $id AND (bm_ot_mtr) > 0 LIMIT 1")->result_array();
        // if(!empty($data)) return true;

        return false;
    }
    public function isTransExist($id){
        // $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_prt_id = $id AND (bm_ot_mtr) > 0 LIMIT 1")->result_array();
        // if(!empty($data)) return true;

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
            $subsql .=" AND prm.prm_entry_no = '".$_GET['_entry_no']."'";
            $record['filter']['_entry_no']['value'] = $_GET['_entry_no'];
            $record['filter']['_entry_no']['text'] = $_GET['_entry_no'];
        }
        if(isset($_GET['_entry_date_from']) && !empty($_GET['_entry_date_from'])){
            $_entry_date_from = date('Y-m-d', strtotime($_GET['_entry_date_from']));
            $subsql .= " AND prm.prm_entry_date >= '".$_entry_date_from."'";
        }
        if(isset($_GET['_entry_date_to']) && !empty($_GET['_entry_date_to'])){
            $_entry_date_to = date('Y-m-d', strtotime($_GET['_entry_date_to']));
            $subsql .= " AND prm.prm_entry_date <= '".$_entry_date_to."'";
        }
        if(isset($_GET['_supplier_name']) && !empty($_GET['_supplier_name'])){
            $subsql .=" AND supplier.supplier_name = '".$_GET['_supplier_name']."'";
            $record['filter']['_supplier_name']['value'] = $_GET['_supplier_name'];
            $record['filter']['_supplier_name']['text'] = $_GET['_supplier_name'];
        }
        $query="SELECT prm.*,
                UPPER(supplier.supplier_name) as supplier_name
                FROM purchase_return_master prm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = prm.prm_supplier_id)
                WHERE prm.prm_delete_status = 0
                $subsql
                ORDER BY prm.prm_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['prm_id']);
            }
        }
        return $record;
    }
    public function get_data_for_add(){
        $record['prm_entry_no'] = $this->get_max_entry_no(['entry_no' => 'prm_entry_no', 'delete_status' => 'prm_delete_status', 'fin_year' => 'prm_fin_year','branch_id' => 'prm_branch_id']);
        $record['prm_uuid'] 	= $_SESSION['user_id'].''.time();
        return $record;
    }
    public function get_data_for_edit($id){
        $query="SELECT prm.*,
                UPPER(supplier.supplier_name) as supplier_name
                FROM purchase_return_master prm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = prm.prm_supplier_id)
                WHERE prm.prm_id = $id
                AND prm.prm_delete_status = 0";
        $record['master_data'] = $this->db->query($query)->result_array();
        if(!empty($record['master_data'])){
            $record['master_data'][0]['isExist'] = $this->isExist($id);
        }
        return $record;
    }
    public function get_transaction($prm_id){
        $query="SELECT prt.*,
                bm.bm_item_code as qrcode,
                pm.pm_bill_no as bill_no,
                pm.pm_bill_date as bill_date,
                UPPER(fabric.fabric_name) as fabric_name,
                UPPER(design.design_name) as design_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(width.width_name), '') as width_name,
                IFNULL(UPPER(hsn.hsn_name), '') as hsn_name,
                design.design_image
                FROM purchase_return_trans prt
                INNER JOIN barcode_master bm ON(bm.bm_id = prt.prt_bm_id)
                INNER JOIN purchase_master pm ON(pm.pm_id = bm.bm_pm_id)
                INNER JOIN purchase_trans pt ON(pt.pt_id = bm.bm_pt_id)
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = pt.pt_fabric_id)
                INNER JOIN design_master design ON(design.design_id = pt.pt_design_id)
                LEFT JOIN color_master color ON(color.color_id = pt.pt_color_id)
                LEFT JOIN width_master width ON(width.width_id = pt.pt_width_id)
                LEFT JOIN hsn_master hsn ON(hsn.hsn_id = pt.pt_hsn_id)
                WHERE prt.prt_prm_id = $prm_id
                AND prt.prt_delete_status = 0
                ORDER BY prt.prt_id DESC";
        $record = $this->db->query($query)->result_array();
        if(!empty($record)){
            foreach ($record as $key => $value) {
                $record[$key]['isExist'] = $this->isTransExist($value['prt_id']);
            }
        }
        return $record;
    }
    public function get_data_for_qrcode_print($clause, $_id){
        $rollno= ENV == PROD ? 'bm.bm_roll_no' : 0;
        $query ="SELECT 
                UPPER(fabric.fabric_name) as fabric_name,
                UPPER(design.design_name) as design_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(width.width_name), '') as width_name,
                UPPER(bm.bm_description) as description,
                bm.bm_roll_no as qrcode, 
                bm.bm_prt_mtr as mtr, 
                bm.bm_mrp as mrp, 
                bm.bm_prt_rate as rate,
                CONCAT('R', '', bm.bm_cost_char) as cost_char,
                $rollno as roll_no
                FROM barcode_master bm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = bm.bm_supplier_id)
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = bm.bm_fabric_id)
                INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
                LEFT JOIN color_master color ON(color.color_id = bm.bm_color_id)
                LEFT JOIN width_master width ON(width.width_id = bm.bm_width_id)
                WHERE bm.bm_delete_status = 0 
                AND ".$clause." = $_id";
        // echo "<pre>"; print_r($query); exit();
        $data['barcode_data'] = $this->db->query($query)->result_array();
        $data['company_data'] = $this->db_operations->get_record('company_master', ['company_id' => 1]);
        return $data;
    }
    public function get_data_for_print($prm_id){
        $query="SELECT prm.prm_entry_no as entry_no, 
                DATE_FORMAT(prm.prm_entry_date, '%d-%m-%Y') as entry_date,
                DATE_FORMAT(prm.prm_bill_date, '%d-%m-%Y') as bill_date,
                prm.prm_bill_no as bill_no,
                prm.prm_notes as notes,
                prm.prm_total_qty as total_qty,
                prm.prm_total_mtr as total_mtr,
                prm.prm_total_amt as total_amt,
                UPPER(supplier.supplier_name) as supplier_name,
                UPPER(supplier.supplier_address) as supplier_address
                FROM purchase_return_master prm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = prm.prm_supplier_id)
                WHERE prm.prm_delete_status = 0 
                AND prm.prm_id = $prm_id";
        // echo "<pre>"; print_r($query); exit();
        $record['master_data'] = $this->db->query($query)->result_array();

        $query="SELECT 
                UPPER(fabric.fabric_name) as fabric_name,
                UPPER(design.design_name) as design_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(width.width_name), '') as width_name,
                SUM(prt.prt_qty) as qty, 
                SUM(prt.prt_mtr) as mtr,
                SUM(prt.prt_total_mtr) as total_mtr,
                prt.prt_rate as rate,
                SUM(prt.prt_amt) as amt
                FROM purchase_return_trans prt
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = prt.prt_fabric_id)
                INNER JOIN design_master design ON(design.design_id = prt.prt_design_id)
                LEFT JOIN color_master color ON(color.color_id = prt.prt_color_id)
                LEFT JOIN width_master width ON(width.width_id = prt.prt_width_id)
                WHERE prt.prt_delete_status = 0 
                AND prt.prt_pm_id = $prm_id
                GROUP BY fabric.fabric_id, design.design_id, color.color_id, prt.prt_rate
                ORDER BY fabric.fabric_name, design.design_name, color.color_name, prt.prt_rate ASC";
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
    public function get_barcode_data($id){
		$query="SELECT 
                0 as prt_id,
                bm.bm_pm_id as prt_pm_id,
                bm.bm_pt_id as prt_pt_id,
                bm.bm_id as prt_bm_id,
                bm.bm_item_code as qrcode,
                bm.bm_delete_status as delete_status,
                bm.bm_supplier_id as supplier_id,
                pm.pm_bill_no as bill_no,
                pm.pm_bill_date as bill_date,
                1 as prt_qty,
                bm.bm_pt_rate as prt_rate,
                pt.pt_disc_per as prt_disc_per,
                pt.pt_sgst_per as prt_sgst_per,
                pt.pt_cgst_per as prt_cgst_per,
                pt.pt_igst_per as prt_igst_per,
                ((bm.bm_pt_mtr - bm.bm_prt_mtr) - (bm.bm_ot_mtr + bm.bm_et_mtr)) as bal_mtr,
                UPPER(supplier.supplier_name) as supplier_name,
                UPPER(fabric.fabric_name) as fabric_name,
                UPPER(design.design_name) as design_name,
                IFNULL(UPPER(color.color_name), '') as color_name,
                IFNULL(UPPER(width.width_name), '') as width_name,
                IFNULL(UPPER(hsn.hsn_name), '') as hsn_name,
                design.design_image,
                0 as prt_amt,
                0 as prt_disc_amt,
                0 as prt_taxable_amt,
                0 as prt_sgst_amt,
                0 as prt_cgst_amt,
                0 as prt_igst_amt,
                0 as prt_total_amt,
                bm.bm_prt_mtr
				FROM barcode_master bm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = bm.bm_supplier_id)
                INNER JOIN purchase_master pm ON(pm.pm_id = bm.bm_pm_id)
                INNER JOIN purchase_trans pt ON(pt.pt_id = bm.bm_pt_id)
                INNER JOIN fabric_master fabric ON(fabric.fabric_id = pt.pt_fabric_id)
                INNER JOIN design_master design ON(design.design_id = pt.pt_design_id)
                LEFT JOIN color_master color ON(color.color_id = pt.pt_color_id)
                LEFT JOIN width_master width ON(width.width_id = pt.pt_width_id)
                LEFT JOIN hsn_master hsn ON(hsn.hsn_id = pt.pt_hsn_id)
				WHERE bm.bm_id = $id";
		$data = $this->db->query($query)->result_array();
        // echo "<pre>"; print_r($query); exit;
        // echo "<pre>"; print_r($data); exit;
        if(!empty($data)){
            foreach ($data as $key => $value) {
                $gst_type = $this->model->get_supplier_state($value['supplier_id']);
                $data[$key]['prt_mtr']          = $value['bal_mtr'];
                $data[$key]['prt_total_mtr']    = $value['bal_mtr'];
                $data[$key]['prt_sgst_per']     = $gst_type == 0 ? $value['prt_sgst_per']: 0;
                $data[$key]['prt_cgst_per']     = $gst_type == 0 ? $value['prt_cgst_per']: 0;
                $data[$key]['prt_igst_per']     = $gst_type == 1 ? $value['prt_igst_per']: 0;
            }
        }
        return $data;
	}
    public function get_supplier_state($id){
        $query="SELECT supplier.supplier_state_id as state_id
                FROM supplier_master supplier
                WHERE supplier.supplier_id = $id";
        $supplier_data = $this->db->query($query)->result_array();
        if(empty($supplier_data)) return 0;

        $state_data = $this->model->get_state();
        if(empty($state_data)) return 0;
        return ($state_data[0]['state_id'] == $supplier_data[0]['state_id']) ? 0 : 1;
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
			$subsql .= " AND (prm.prm_entry_no LIKE '%".$name."%') ";
		}
		$query="SELECT prm.prm_entry_no as id, UPPER(prm.prm_entry_no) as name
				FROM purchase_return_master prm
				WHERE 1
				$subsql
				GROUP BY prm.prm_entry_no ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}
    public function _supplier_name(){
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
			$subsql .= " AND (supplier.supplier_name LIKE '%".$name."%') ";
		}
		$query="SELECT supplier.supplier_name as id, UPPER(supplier.supplier_name) as name
				FROM purchase_return_master prm
                INNER JOIN supplier_master supplier ON(supplier.supplier_id = prm.prm_supplier_id)
				WHERE 1
				$subsql
				GROUP BY supplier.supplier_name ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}
}
?>