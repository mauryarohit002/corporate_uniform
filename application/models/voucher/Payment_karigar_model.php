<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class payment_karigar_model extends my_model{
    public function __construct(){ parent::__construct('voucher', 'payment_karigar'); }
    public function isExist($id){
        // $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_pm_id = $id AND (bm_sot_mtr + bm_it_mtr + bm_prt_mtr) > 0 LIMIT 1")->result_array();
        // if(!empty($data)) return true;

        return false;
    }
    public function isTransExist($id){
        // $data = $this->db->query("SELECT bm_id FROM barcode_master WHERE bm_pm_id = $id AND (bm_sot_mtr + bm_it_mtr + bm_prt_mtr) > 0 LIMIT 1")->result_array();
        // if(!empty($data)) return true;

        return false;
    }
    public function isOrderTransExist($id){
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
            $subsql .=" AND payment.payment_entry_no = ".$_GET['_entry_no'];
            $record['filter']['_entry_no']['text'] = $_GET['_entry_no'];
            $record['filter']['_entry_no']['value'] = $_GET['_entry_no'];
        }
        if(isset($_GET['_entry_date_from']) && !empty($_GET['_entry_date_from'])){
            $_entry_date_from = date('Y-m-d', strtotime($_GET['_entry_date_from']));
            $subsql .= " AND payment.payment_entry_date >= '".$_entry_date_from."'";
        }
        if(isset($_GET['_entry_date_to']) && !empty($_GET['_entry_date_to'])){
            $_entry_date_to = date('Y-m-d', strtotime($_GET['_entry_date_to']));
            $subsql .= " AND payment.payment_entry_date <= '".$_entry_date_to."'";
        }
        if(isset($_GET['_customer_name']) && !empty($_GET['_customer_name'])){
            $subsql .=" AND customer.customer_name = '".$_GET['_customer_name']."'";
            $record['filter']['_customer_name']['value'] = $_GET['_customer_name'];
            $record['filter']['_customer_name']['text'] = $_GET['_customer_name'];
        }
        if(isset($_GET['status']) && !empty($_GET['status'])){
            $subsql .=' AND payment.payment_adjust_status = '.$_GET['status'];
        }
        if(isset($_GET['_total_amt_from'])){
            if($_GET['_total_amt_from'] != ''){
                $subsql .=" AND payment.payment_amt >= ".$_GET['_total_amt_from'];
            }
        }
        if(isset($_GET['_total_amt_to'])){
            if($_GET['_total_amt_to'] != ''){
                $subsql .=" AND payment.payment_amt <= ".$_GET['_total_amt_to'];
            }
        }
        $query="SELECT payment.*,
                DATE_FORMAT(payment.payment_entry_date, '%d-%m-%Y') as entry_date,
                UPPER(karigar.karigar_name) as karigar_name
                FROM payment_karigar_master payment
                INNER JOIN karigar_master karigar ON(karigar.karigar_id = payment.payment_karigar_id)
                WHERE payment.payment_delete_status = 0
                AND payment.payment_branch_id = ".$_SESSION['user_branch_id']."
                AND payment.payment_fin_year = '".$_SESSION['fin_year']."'
                $subsql
                ORDER BY payment.payment_id DESC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isExist($value['payment_id']);
            }
        }
        return $record;
    }
    public function get_data_for_add(){
        $record['payment_entry_no'] 	= $this->get_max_entry_no(['entry_no' => 'payment_entry_no', 'delete_status' => 'payment_delete_status', 'fin_year' => 'payment_fin_year', 'branch_id' => 'payment_branch_id']);
        $record['payment_uuid'] = $_SESSION['user_id'].''.time();
        return $record;
    }
    public function get_data_for_edit($id){
        $query="SELECT payment.*,
                UPPER(karigar.karigar_name) as karigar_name
                FROM payment_karigar_master payment
                INNER JOIN karigar_master karigar ON(karigar.karigar_id = payment.payment_karigar_id)
                WHERE payment.payment_delete_status = 0
                AND payment.payment_id = $id";
        $data['master_data'] = $this->db->query($query)->result_array();
        if(!empty($data['master_data'])){
            $data['master_data'][0]['isExist'] = $this->isExist($id);
        }
        return $data;
    }
    public function get_transaction($payment_id){
        $query="SELECT pht.*,
                1 as pht_checked
                FROM payment_hisab_trans pht
                WHERE pht.pht_delete_status = 0
                AND pht.pht_payment_id = $payment_id";
        $data['hisab_data'] = $this->db->query($query)->result_array();
        if(!empty($data['hisab_data'])){
            foreach ($data['hisab_data'] as $key => $value) {
                $hisab_data   = $this->get_hisab($value['pht_hm_id']);
                $total_amt      = $value['pht_adjust_amt'] + $hisab_data[0]['balance_amt'];
                $balance_amt    = $total_amt - $value['pht_adjust_amt'];
                $data['hisab_data'][$key]['pht_total_amt']    = $total_amt;
                $data['hisab_data'][$key]['balance_amt']      = $balance_amt;
            }
            usort($data['hisab_data'], function($a, $b){
                return $b['balance_amt'] - $a['balance_amt'];
            });
        }
        return $data;
    }
    public function get_payment_mode_data($payment_id){
        $query="SELECT 
                pkpmt.pkpmt_id,
                pkpmt.pkpmt_amt as pkpmt_amt,
                pkpmt.pkpmt_payment_mode_id as pkpmt_payment_mode_id,
                UPPER(payment_mode.payment_mode_name) as payment_mode_name
                FROM payment_karigar_payment_mode_trans pkpmt
                INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = pkpmt.pkpmt_payment_mode_id)
                WHERE pkpmt.pkpmt_delete_status = 0
                AND pkpmt.pkpmt_payment_id = $payment_id
                ORDER BY payment_mode.payment_mode_name ASC";
        $data = $this->db->query($query)->result_array();
        $ids  = '';
        $subsql='';
        $record=[];
        if(!empty($data)){
            foreach ($data as $key => $value) {
                array_push($record, $value);
                $ids .= empty($ids) ? $value['pkpmt_payment_mode_id'] : ', '.$value['pkpmt_payment_mode_id'];
            }
            $subsql .=" AND payment_mode.payment_mode_id NOT IN(".$ids.")";
        }

        $query="SELECT 
                0 as pkpmt_id,
                0 as pkpmt_amt,
                payment_mode.payment_mode_id as pkpmt_payment_mode_id,
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
    public function get_karigar_from_hisab($hm_id){
        $query="SELECT karigar.karigar_id,
                CONCAT(UPPER(karigar.karigar_name), ' - ', karigar.karigar_mobile) as karigar_name
                FROM hisab_master hm
                INNER JOIN karigar_master karigar ON(karigar.karigar_id = hm.hm_karigar_id)
                WHERE hm.hm_delete_status = 0
                AND hm.hm_id = $hm_id";
        return $this->db->query($query)->result_array();
    }
    public function get_order_data($customer_id){
        $subsql = '';
        if(isset($_POST['rot_om_id']) && !empty($_POST['rot_om_id'])){
            $ids = implode(', ', $_POST['rot_om_id']);
            $subsql .= " AND om.om_id NOT IN (".$ids.")";
        }
        $query="SELECT 0 as rot_id,
                0 as rot_checked,
                om.om_id as rot_om_id,
                om.om_entry_no as rot_entry_no,
                DATE_FORMAT(om.om_entry_date, '%d-%m-%Y') as rot_entry_date,
                (om.om_total_amt - om.om_advance_amt) as rot_total_amt,
                0 as rot_adjust_amt,
                (om.om_total_amt - (om.om_advance_amt + om.om_allocated_amt)) as balance_amt
                FROM order_master om
                WHERE om.om_delete_status = 0
                AND (om.om_total_amt - (om.om_advance_amt + om.om_allocated_amt)) > 0
                AND om.om_customer_id = $customer_id
                $subsql
                ORDER BY balance_amt DESC";
        return $this->db->query($query)->result_array();
    }
    public function get_karigar_balance_amt($karigar_id){
            $balance_amt = 0;
            $type        = TO_PAY;
            $opening_amt = 0;
            $hisab_amt   = $this->get_hisab_amt($karigar_id);
            $payment_amt = $this->get_payment_amt($karigar_id);
            $closing_amt = ($opening_amt + $hisab_amt) - ($payment_amt);
            $balance_amt = $closing_amt;
            if($balance_amt < 0){
                $balance_amt= abs($balance_amt);
                $type       = TO_RECEIVE;
            }
            return ['balance_amt' => $balance_amt, 'type' => $type];
    }
    public function get_hisab_amt($id){
        $query="SELECT SUM(hm.hm_total_amt) as amt
                FROM hisab_master hm
                WHERE hm.hm_delete_status = 0
                AND hm.hm_karigar_id = $id
                GROUP BY hm.hm_karigar_id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? 0 : $data[0]['amt'];
    }
    public function get_payment_amt($id){
        $query="SELECT SUM(payment.payment_amt) as amt
                FROM payment_karigar_master payment
                WHERE payment.payment_delete_status = 0
                AND payment.payment_karigar_id = $id
                GROUP BY payment.payment_karigar_id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? 0 : $data[0]['amt'];
    }
    public function get_hisab_data($id){
        $subsql = '';
        if(isset($_POST['pht_hm_id']) && !empty($_POST['pht_hm_id'])){
            $ids = implode(', ', $_POST['pht_hm_id']);
            $subsql .= " AND hm.hm_id NOT IN (".$ids.")";
        }

        $query="SELECT 
                    0 as pht_id,
                    0 as pht_checked,
                    hm.hm_id as pht_hm_id,
                    hm.hm_entry_no as pht_entry_no,
                    DATE_FORMAT(hm.hm_entry_date, '%d-%m-%Y') as pht_entry_date,
                    hm.hm_total_amt as pht_total_amt,
                    0 as pht_adjust_amt,
                    (hm.hm_total_amt - (hm.hm_allocated_amt + hm.hm_allocated_round_off)) as balance_amt
                    FROM hisab_master hm
                    WHERE hm.hm_delete_status = 0
                    AND (hm.hm_total_amt - (hm.hm_allocated_amt + hm.hm_allocated_round_off)) > 0
                    AND hm.hm_karigar_id = $id
                    $subsql
                    ORDER BY balance_amt DESC";
        return $this->db->query($query)->result_array();
    }
    public function get_hisab_balance($id){
        $query="SELECT 
                SUM(hm.hm_total_amt - (hm.hm_allocated_amt + hm.hm_allocated_round_off)) as amt
                FROM hisab_master hm
                WHERE hm.hm_delete_status = 0
                AND hm.hm_karigar_id = $id
                GROUP BY hm.hm_karigar_id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? 0 : $data[0]['amt'];
    }
    public function isAdjusted($payment_id){
        $cnt = $this->db_operations->get_cnt('payment_hisab_trans', ['pht_payment_id' => $payment_id, 'pht_delete_status' => false]);
        if(!empty($cnt)) return true;

        return false;
    }
    public function get_hisab($hm_id){
        $query="SELECT hm.*,
                (hm.hm_total_amt - (hm.hm_allocated_amt + hm.hm_allocated_round_off)) as balance_amt
                FROM hisab_master hm
                WHERE hm.hm_delete_status = 0
                AND hm.hm_id = $hm_id";
        return $this->db->query($query)->result_array();
    }
    public function _hm_id(){
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
            $subsql .= " AND (hm.hm_entry_no LIKE '".$name."%' OR hm.hm_entry_date LIKE '".$name."%') ";
        }
        $query="SELECT hm.hm_id as id, 
                CONCAT(UPPER(hm.hm_entry_no), ' / ', DATE_FORMAT(hm.hm_entry_date, '%d-%m-%Y')) as name
                FROM hisab_master hm
                WHERE hm.hm_delete_status = 0
                $subsql
                GROUP BY hm.hm_id ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }

    // search_functions
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
                $subsql .= " AND (payment.payment_entry_no LIKE '".$name."%') ";
            }
            $query="SELECT payment.payment_entry_no as id, payment.payment_entry_no as name
                    FROM payment_karigar_master payment
                    WHERE payment.payment_delete_status = 0
                    AND payment.payment_fin_year = '".$_SESSION['fin_year']."'
                    $subsql
                    GROUP BY payment.payment_entry_no ASC
                    LIMIT $limit
                    OFFSET $offset";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function _karigar_name(){
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
                $subsql .= " AND (karigar.karigar_name LIKE '".$name."%') ";
            }
             $query="SELECT karigar.karigar_name as id, UPPER(karigar.karigar_name) as name
                    FROM payment_karigar_master payment
                    INNER JOIN karigar_master karigar ON(karigar.karigar_id = payment.payment_karigar_id)
                    WHERE payment.payment_delete_status = 0
                    AND payment.payment_fin_year = '".$_SESSION['fin_year']."'
                    $subsql
                    GROUP BY karigar.karigar_name ASC
                    LIMIT $limit
                    OFFSET $offset";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
        public function get_balance_data($karigar_id){
            $query="SELECT SUM(karigar.karigar_opening_amt) as amt
                    FROM karigar_master karigar
                    WHERE karigar.karigar_id = $karigar_id
                    GROUP BY karigar.karigar_id";
            $data = []; //$this->db->query($query)->result_array();
            $opening_amt = empty($data) ? 0 : $data[0]['amt'];

            $query="SELECT SUM(hm.hm_total_amt - hm.hm_allocated_amt) as amt
                    FROM hisab_master hm
                    WHERE hm.hm_delete_status = 0
                    AND (hm.hm_total_amt - hm.hm_allocated_amt) > 0
                    AND hm.hm_karigar_id = $karigar_id
                    GROUP BY hm.hm_karigar_id";
            $data = $this->db->query($query)->result_array();
            $hisab_amt = empty($data) ? 0 : $data[0]['amt'];

            $closing_amt = ($opening_amt + $hisab_amt);
            $balance_amt = $closing_amt;
            $type        = TO_PAY;
            if($balance_amt < 0){
                $balance_amt    = abs($balance_amt);
                $type           = TO_RECEIVE;
            }
            return [
                        'opening_amt'       => $opening_amt,
                        'hisab_amt'         => $hisab_amt,
                        'balance_amt'       => $balance_amt,
                        'type'              => $type,
                    ];
        }            
    // search_functions
}
?>