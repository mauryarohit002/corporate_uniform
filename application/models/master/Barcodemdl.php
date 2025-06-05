<?php defined('BASEPATH') OR exit('No direct script access allowed');
	class Barcodemdl extends CI_model{
		protected $master;
		public function __construct(){
			parent::__construct();

			$this->master = 'barcode_master';
		}
	    public function get_state($data){
	    	if(
				$data[0]['bm_pt_qty'] == 0 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 0
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'INVALID', 'msg' => 'Invalid Barcode.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 0 
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'PURCHASE', 'msg' => 'Barcode used in purchase.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 1 &&
				$data[0]['bm_ot_mtr'] == 0 
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'PURCHASE', 'msg' => 'Barcode used in purchase.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 1 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 0 
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'PURCHASE RETURN', 'msg' => 'Barcode used in purchase return.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 0 
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'SALES', 'msg' => 'Barcode is sold.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 1 
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'OUTWARD', 'msg' => 'Barcode used in outward.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 1 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 1 
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'OUTWARD', 'msg' => 'Barcode used in outward.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 1 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 0 
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'SALES RETURN', 'msg' => 'Barcode used in sales return.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 1 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 1 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 0 
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'PURCHASE RETURN', 'msg' => 'Barcode used in purchase return.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 1 
				// $data[0]['bm_it_qty'] == 1
			){
				return['state' => 'INWARD', 'msg' => 'Barcode used in inward.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 0 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 1 &&
				$data[0]['bm_ot_mtr'] == 1 
				// $data[0]['bm_it_qty'] == 1
			){
				return['state' => 'INWARD', 'msg' => 'Barcode used in inward.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 1 
				// $data[0]['bm_it_qty'] == 1
			){
				return['state' => 'SALES', 'msg' => 'Barcode is sold.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 1 &&
				// $data[0]['bm_at_qty'] == 0 &&
				$data[0]['bm_ot_mtr'] == 1 
				// $data[0]['bm_it_qty'] == 1
			){
				return['state' => 'SALES RETURN', 'msg' => 'Barcode used in sales return.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 1 &&
				$data[0]['bm_ot_mtr'] == 0 
				// $data[0]['bm_it_qty'] == 0
			){
				return['state' => 'ALTERATION', 'msg' => 'Barcode used in alteration.'];	
			}
			if(
				$data[0]['bm_pt_qty'] == 1 && 
				$data[0]['bm_prt_qty'] == 0 && 
				$data[0]['bm_st_qty'] == 1 &&
				$data[0]['bm_srt_qty'] == 0 &&
				// $data[0]['bm_at_qty'] == 1 &&
				$data[0]['bm_ot_mtr'] == 1 
				// $data[0]['bm_it_qty'] == 1
			){
				return['state' => 'ALTERATION', 'msg' => 'Barcode used in alteration.'];	
			}
			// return['state' => 'INVALID STATE', 'msg' => 'Barcode is in invalid state.'];	
	    }
	    public function _bm_id(){
            $subsql = "";
            $name 	= "";
            if(isset($_GET['name']) && !empty($_GET['name'])){
                $name   = $_GET['name'];
                $subsql .= " AND (bm.bm_item_code = '".$name."' OR bm.bm_shop_code = '".$name."')";
            }
            $query ="
                        SELECT bm.bm_id as id, ".$name." as name
                        FROM barcode_master bm
                        WHERE 1
                        $subsql
                        ORDER BY bm.bm_id DESC
                        LIMIT 1
                    ";
            // echo $query; exit;
            return $this->db->query($query)->result_array();
        }
	}
?>