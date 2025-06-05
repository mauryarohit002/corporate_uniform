<?php defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class karigar_ledger_model extends my_model{
    public function __construct(){ parent::__construct('report', 'karigar_ledger'); }
    public function get_record(){  
        $record     = [];
        $karigar_name = 'XXX';
        // echo "<pre>"; print_r($_REQUEST);die; 
        if(isset($_REQUEST['_karigar_name']) && !empty($_REQUEST['_karigar_name'])){
            $karigar_name = $_REQUEST['_karigar_name'];
            $record['filter']['_karigar_name']['value'] = $_REQUEST['_karigar_name'];
            $record['filter']['_karigar_name']['text']  = $_REQUEST['_karigar_name'];
        }
        $hisab_query="SELECT 'HISAB' as action, 
                        UPPER(karigar.karigar_name) as karigar_name,
                        hm.hm_entry_no as entry_no,
                        DATE_FORMAT(hm.hm_entry_date, '%d-%m-%Y') as entry_date,
                        hm.hm_total_amt as hisab_amt,
                        0 as payment_amt,
                        hm.hm_created_at as created_at
                        FROM hisab_master hm
                        INNER JOIN karigar_master karigar ON(karigar.karigar_id = hm.hm_karigar_id)
                        WHERE hm.hm_delete_status = 0
                        AND karigar.karigar_name = '".$karigar_name."'";
        
        $payment_query="SELECT 'PAYMENT' as action, 
                        UPPER(karigar.karigar_name) as karigar_name,
                        payment.payment_entry_no as entry_no,
                        DATE_FORMAT(payment.payment_entry_date, '%d-%m-%Y') as entry_date,
                        0 as hisab_amt,
                        payment.payment_amt as payment_amt,
                        payment.payment_created_at as created_at
                        FROM payment_karigar_master payment
                        INNER JOIN karigar_master karigar ON(karigar.karigar_id = payment.payment_karigar_id)
                        WHERE payment.payment_delete_status = 0
                        AND karigar.karigar_name = '".$karigar_name."'";                        

        $query="SELECT ledger.*,
                ((ledger.hisab_amt) - (ledger.payment_amt)) as closing_amt,
                IF(((ledger.hisab_amt) - (ledger.payment_amt)) < 0, 'TO RECEIVE', 'TO PAY') as label
                FROM($hisab_query UNION ALL $payment_query) as ledger
                ORDER BY ledger.entry_date DESC, ledger.created_at ASC";
        $record['data'] = $this->db->query($query)->result_array();
        $record['totals']['rows'] = count($record['data']);
        // echo "<pre>"; print_r($query); exit();
        // echo "<pre>"; print_r($record); exit();
        if(!empty($record['data'])){
            $closing_amt = 0;
            foreach ($record['data'] as $key => $value) {
                $label       = 'TO PAY';
                $closing_amt = $closing_amt + $value['closing_amt'];
                if($closing_amt < 0) $label = 'TO RECEIVE';
                $record['data'][$key]['closing_amt'] = $closing_amt;
                $record['data'][$key]['label']       = $label;
            }
        }
        return $record;
    }

    public function _karigar_name(){  
        $subsql = '';
        $limit  = PER_PAGE;
        $offset = OFFSET;
        $page   = 1;
        if(isset($_REQUEST['limit']) && !empty($_REQUEST['limit'])){
            $limit = $_REQUEST['limit'];
        }
        if(isset($_REQUEST['page']) && !empty($_REQUEST['page'])){
            $page   = $_REQUEST['page'];
            $offset = $limit * ($page - 1);
        }
        if(isset($_REQUEST['name']) && !empty($_REQUEST['name'])){
            $name   = $_REQUEST['name'];
            $subsql .= " AND (karigar.karigar_name LIKE '".$name."%') ";
        }
        
        $query="SELECT karigar.karigar_name as id, 
                UPPER(karigar.karigar_name) as name
                FROM  karigar_master karigar
                WHERE karigar.karigar_status = 1
                $subsql
                GROUP BY karigar.karigar_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }

}?>