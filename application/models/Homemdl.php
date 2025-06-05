<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Homemdl extends CI_model{
		protected $start_date;
		protected $end_date;
		public function __construct(){
			parent::__construct();
			$this->start_date 	= isset($_SESSION['start_year']) ? $_SESSION['start_year']." 00:00:01" : date('Y-m-d H:i:s');
			$this->end_date 	= isset($_SESSION['end_year']) ? $_SESSION['end_year']." 23:59:59" : date('Y-m-d H:i:s');

			// $this->load->model('report/DailyProfitmdl');
		}
		public function get_first(){
			$pur_query ="
							SELECT SUM(pm.pm_total_mtr) as qty, 0 as ret_qty
							FROM purchase_master pm
							WHERE pm.pm_delete_status = 0 
							AND pm.pm_created_at <= '".$this->end_date."'
							AND pm.pm_branch_id = ".$_SESSION['user_branch_id']."
						";
			// echo "<pre>"; print_r($pur_query); exit;
			$pur_data = $this->db->query($pur_query)->result_array();

			$order_query ="
							SELECT SUM(om.om_total_mtr) as qty, 0 as ret_qty
							FROM order_master om
							WHERE om.om_delete_status = 0 
							AND om.om_created_at <= '".$this->end_date."'
							AND om.om_branch_id = ".$_SESSION['user_branch_id']."
						";
			// echo "<pre>"; print_r($pur_query); exit;
			$sales_data = $this->db->query($order_query)->result_array();

			// echo "<pre>"; print_r($pur_data); exit;

			$pur_qty  = 0;
			$pret_qty = 0;
			$sale_qty = 0;
			$sret_qty = 0;

			if(!empty($pur_data)){
				$pur_qty  = (float)$pur_data[0]['qty'];
				$pret_qty = (float)$pur_data[0]['ret_qty'];
			}
			if(!empty($sales_data)){
				$sale_qty = (float)$sales_data[0]['qty'];
				$sret_qty = (float)$sales_data[0]['ret_qty'];
			}

			$bal_qty = (float)(($pur_qty + $sret_qty) - ($sale_qty + $pret_qty));
			return [
					'pur_qty' 	=> round($pur_qty, 2), 
					'pret_qty' 	=> round($pret_qty, 2), 
					'sale_qty' 	=> round($sale_qty, 2), 
					'sret_qty' 	=> round($sret_qty, 2),
					'bal_qty' 	=> round($bal_qty, 2)
				];
		}
		public function get_second($start_date, $end_date){
			$start_date = date('Y-m-d', strtotime($start_date));
			$end_date 	= date('Y-m-d', strtotime($end_date));
			$record = [];
			
			$record = [];
			if(!empty($data)){
				foreach ($data as $key => $value) {
					$from_date 	= date('Y-m-01', strtotime($value['sm_entry_date']));
					$to_date 	= date('Y-m-t', strtotime($value['sm_entry_date']));
					$record[$key] = $this->DailyProfitmdl->get_data(true, $from_date, $to_date);  
					// echo "<pre>"; print_r($record); exit;
					$record[$key]['month_year'] = date('M-Y', strtotime($value['sm_entry_date']));  
				}
			}
			// echo "<pre>"; print_r($record); exit;

			return $record;
		}
		public function get_third($start_date, $end_date){
			$record 	= [];
			$modes 		= $this->config->item('payment_mode'); 
			$start_date = date('Y-m-d', strtotime($start_date));
			$end_date 	= date('Y-m-d', strtotime($end_date));
			// echo "<pre>"; print_r($record);exit();
			return $record;
		}
		public function get_fourth($start_date, $end_date){
			$record 	= [];
			$start_date = date('Y-m-d', strtotime($start_date));
			$end_date 	= date('Y-m-d', strtotime($end_date));
			// echo "<pre>"; print_r($query); exit;
			return $record;
		}
		public function get_fifth($start_date, $end_date){
			$record 	= [];
			$start_date = date('Y-m-d', strtotime($start_date));
			$end_date 	= date('Y-m-d', strtotime($end_date));
			return $record;
		}
	}
?>