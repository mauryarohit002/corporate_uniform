<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class daily_transaction_model extends CI_model{
		public function __construct(){
			parent::__construct();
		}
		public function get_record(){
			$record     = [];
			if(isset($_GET['_type']) && !empty($_GET['_type'])){
				$record['search']['_type']['value'] = $_GET['_type'];
				$record['search']['_type']['text']  = $_GET['_type'];
			}
			if(isset($_GET['_payment_mode_name']) && !empty($_GET['_payment_mode_name'])){
				$record['search']['_payment_mode_name']['value'] = $_GET['_payment_mode_name'];
				$record['search']['_payment_mode_name']['text']  = $_GET['_payment_mode_name'];
			}
			$date_from 	= date('Y-m-d');
			$date_to 	= date('Y-m-d');
			if((isset($_GET['_date_from'])) && ($_GET['_date_from'] != '')) $date_from = $_GET['_date_from'];
			if((isset($_GET['_date_to'])) && ($_GET['_date_to'] != '')) $date_to = $_GET['_date_to'];
			$start = strtotime($date_from);
			$end   = strtotime($date_to);
			$diff  = ceil(abs($end - $start) / 86400);
			// echo "<pre>"; print_r($diff); exit;
			for ($i = 0; $i <= $diff ; $i++) { 
				$strtotime = strtotime($date_from." + ".$i." days");
				$data = $this->get_transaction_data(date('Y-m-d', $strtotime));
				if(!empty($data['data'])){
					$record[$strtotime] = $data;

				}
			}
			
			return $record;
		}
		public function get_transaction_data($date){ 
			$subsql 	= '';
			$having 	= '';

			if(isset($_GET['_payment_mode_name']) && !empty($_GET['_payment_mode_name'])){
				$subsql .=" AND payment_mode.payment_mode_name = '".$_GET['_payment_mode_name']."'";
				// $having .=" AND payment_mode_name = '".$_GET['_payment_mode_name']."'";
			} 
			$order_query="SELECT 0 as sr_no,
							om.om_entry_no as entry_no,
							'ORDER' as action,
							UPPER(customer.customer_name) as customer_name,
							SUM(opmt.opmt_amt) as amt,
							UPPER(payment_mode.payment_mode_name) as payment_mode_name,
							om.om_created_at as created_at
							FROM order_master om
							INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
							INNER JOIN order_payment_mode_trans opmt ON(opmt.opmt_om_id = om.om_id)
							INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = opmt.opmt_payment_mode_id)
							WHERE om.om_delete_status = 0 AND om.om_advance_amt > 0
							AND opmt.opmt_delete_status = 0
							AND om.om_status=1
							AND om.om_entry_date = '".$date."'
							AND om.om_branch_id='".$_SESSION['user_branch_id']."'
							$subsql
							GROUP BY om.om_id, payment_mode.payment_mode_id
							HAVING 1
							$having";

			$estimate_query="SELECT 0 as sr_no,
							om.om_em_entry_no as entry_no,
							'ESTIMATE' as action,
							UPPER(customer.customer_name) as customer_name,
							SUM(opmt.opmt_amt) as amt,
							UPPER(payment_mode.payment_mode_name) as payment_mode_name,
							om.om_created_at as created_at
							FROM order_master om
							INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
							INNER JOIN order_payment_mode_trans opmt ON(opmt.opmt_om_id = om.om_id)
							INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = opmt.opmt_payment_mode_id)
							WHERE om.om_delete_status = 0 AND om.om_advance_amt > 0
							AND opmt.opmt_delete_status = 0
							AND om.om_status=0
							AND om.om_entry_date = '".$date."'
							AND om.om_branch_id='".$_SESSION['user_branch_id']."'
							$subsql
							GROUP BY om.om_id, payment_mode.payment_mode_id
							HAVING 1
							$having";	

			$receipt_query="SELECT 0 as sr_no,
							receipt.receipt_entry_no as entry_no,
							'RECEIPT' as action,
							UPPER(customer.customer_name) as customer_name,
							SUM(rpmt.rpmt_amt) as amt,
							UPPER(payment_mode.payment_mode_name) as payment_mode_name,
							receipt.receipt_created_at as created_at
							FROM receipt_master receipt
							INNER JOIN customer_master customer ON(customer.customer_id = receipt.receipt_customer_id)
							INNER JOIN receipt_payment_mode_trans rpmt ON(rpmt.rpmt_receipt_id = receipt.receipt_id)
							INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = rpmt.rpmt_payment_mode_id)
							WHERE receipt.receipt_delete_status = 0
							AND rpmt.rpmt_delete_status = 0
							AND receipt.receipt_entry_date = '".$date."'
							 AND receipt.receipt_branch_id='".$_SESSION['user_branch_id']."'
							$subsql
							GROUP BY receipt.receipt_id, payment_mode.payment_mode_id
							HAVING 1
							$having";

			$payment_query="SELECT 0 as sr_no,
							payment.payment_entry_no as entry_no,
							'PAYMENT' as action,
							UPPER(supplier.supplier_name) as customer_name,
							SUM(ppmt.ppmt_amt) as amt,
							UPPER(payment_mode.payment_mode_name) as payment_mode_name,
							payment.payment_created_at as created_at
							FROM payment_master payment
							INNER JOIN supplier_master supplier ON(supplier.supplier_id = payment.payment_supplier_id)
							INNER JOIN payment_payment_mode_trans ppmt ON(ppmt.ppmt_payment_id = payment.payment_id)
							INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = ppmt.ppmt_payment_mode_id)
							WHERE payment.payment_delete_status = 0
							AND ppmt.ppmt_delete_status = 0 
							AND payment.payment_entry_date = '".$date."'
							AND payment.payment_branch_id='".$_SESSION['user_branch_id']."'
							$subsql 
							GROUP BY payment.payment_id, payment_mode.payment_mode_id
							HAVING 1
							$having";

			$karigar_query="SELECT 0 as sr_no,
							payment.payment_entry_no as entry_no,
							'KARIGAR' as action,
							UPPER(karigar.karigar_name) as customer_name,
							SUM(pkpmt.pkpmt_amt) as amt,
							UPPER(payment_mode.payment_mode_name) as payment_mode_name,
							payment.payment_created_at as created_at
							FROM payment_karigar_master payment
							INNER JOIN karigar_master karigar ON(karigar.karigar_id = payment.payment_karigar_id)
							INNER JOIN  payment_karigar_payment_mode_trans pkpmt ON(pkpmt.pkpmt_payment_id = payment.payment_karigar_id )
							INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = pkpmt.pkpmt_payment_mode_id)
							WHERE payment.payment_delete_status = 0
							AND pkpmt.pkpmt_delete_status = 0 
							AND payment.payment_entry_date = '".$date."'
							 AND payment.payment_branch_id='".$_SESSION['user_branch_id']."'
							$subsql 
							GROUP BY payment.payment_id, payment_mode.payment_mode_id
							HAVING 1
							$having";

			// $general_query="SELECT 0 as sr_no,
			// 				payment_general.payment_general_entry_no as entry_no,
			// 				'GENERAL' as action,
			// 				UPPER(general.general_name) as customer_name,
			// 				SUM(pgpmt.pgpmt_amt) as amt,
			// 				UPPER(payment_mode.payment_mode_name) as payment_mode_name,
			// 				payment_general.payment_general_created_at as created_at
			// 				FROM payment_general_master payment_general
			// 				INNER JOIN general_master general ON(general.general_id = payment_general.payment_general_general_id)
			// 				INNER JOIN  payment_general_payment_mode_trans pgpmt ON(pgpmt.pgpmt_payment_general_id = payment_general.payment_general_id)
			// 				INNER JOIN payment_mode_master payment_mode ON(payment_mode.payment_mode_id = pgpmt.pgpmt_payment_mode_id)
			// 				WHERE payment_general.payment_general_delete_status = 0
			// 				AND pgpmt.pgpmt_delete_status = 0 
			// 				AND payment_general.payment_general_entry_date = '".$date."'
			// 				 AND payment_general.payment_general_branch_id='".$_SESSION['user_branch_id']."'
			// 				$subsql 
			// 				GROUP BY payment_general.payment_general_id, payment_mode.payment_mode_id
			// 				HAVING 1
			// 				$having";					
													
			// echo "<pre>"; print_r($payment_query); exit;					
			$query="SELECT daily.sr_no,
					daily.entry_no,
					daily.action,
					daily.customer_name,
					daily.amt,
					daily.payment_mode_name,
					daily.created_at
					FROM ($order_query UNION ALL $estimate_query UNION ALL $receipt_query UNION ALL $payment_query UNION ALL $karigar_query) as daily
					ORDER BY daily.created_at ASC";
			$record['data'] = $this->db->query($query)->result_array();
			// echo "<pre>"; print_r($query); exit();
			// echo "<pre>"; print_r($record['data']); exit();
			$record['total'] 		= [];
			$record['total']['TOTAL']= 0;
			if(!empty($record['data'])){
				foreach ($record['data'] as $key => $value) {
					$record['data'][$key]['sr_no'] = ($key+1);
					$record['total'][$value['payment_mode_name']] 	= isset($record['total'][$value['payment_mode_name']]) ? ($record['total'][$value['payment_mode_name']] + $value['amt']) : $value['amt'];
					$record['total']['TOTAL'] 						= isset($record['total']['TOTAL']) ? ($record['total']['TOTAL'] + $value['amt']) : $value['amt'];
				}
			}
			return $record;
		}
		
		public function _payment_mode_name(){
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
                $subsql .= " AND (payment_mode.payment_mode_name LIKE '%".$name."%') ";
            }
            $query="SELECT payment_mode.payment_mode_name as id, UPPER(payment_mode.payment_mode_name) as name
                    FROM payment_mode_master payment_mode
                    WHERE 1
                    $subsql
                    GROUP BY payment_mode.payment_mode_name ASC
                    LIMIT $limit
                    OFFSET $offset";
            // echo $query; exit();
            return $this->db->query($query)->result_array();
        }
	}
?>