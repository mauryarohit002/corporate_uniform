<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class qrcode_history_model extends my_model{
    public function __construct(){ parent::__construct('report', 'qrcode_history'); }
	public function get_record(){
		$record['detail_data']	= $this->model->get_detail();
		$record['history_data']	= $this->model->get_history();
		return $record;
	}
    public function get_detail(){
		$_item_code = isset($_REQUEST['_item_code']) && !empty($_REQUEST['_item_code']) ? $_REQUEST['_item_code'] : 'XXX';
		$type = (isset($_REQUEST['type']) && ($_REQUEST['type']=='true'))?'checked':'';
		if(isset($_REQUEST['type']) && ($_REQUEST['type']=='true')){
			$query="SELECT brmm.brmm_id as bm_id,
				brmm.brmm_item_code as _item_code,
				UPPER(supplier.supplier_name) as supplier_name,
                '' as fabric_name,
                '' as width_name,
                IFNULL(UPPER(readymade_category.readymade_category_name), '') as category_name, 
                UPPER(design.design_name) as design_name,
                UPPER(color.color_name) as color_name,
                brmm.brmm_cost_char as cost_char,
				brmm.brmm_mrp as mrp, 
				 ((brmm.brmm_prmt_qty - brmm.brmm_prrt_qty) - (brmm.brmm_ot_qty + brmm.brmm_et_qty)) as bal_qty,
				IF(brmm.brmm_delete_status = 0, 'active', 'deleted') as delete_status,
				brmm.brmm_delete_status as bm_delete_status
				FROM barcode_readymade_master brmm
				INNER JOIN supplier_master supplier ON(supplier.supplier_id = brmm.brmm_supplier_id)
				LEFT JOIN design_master design ON(design.design_id = brmm.brmm_design_id)
                INNER JOIN color_master color ON(color.color_id = brmm.brmm_color_id)
                LEFT JOIN size_master size ON(size.size_id = brmm.brmm_size_id)
                LEFT JOIN readymade_category_master readymade_category ON(readymade_category.readymade_category_id = brmm.brmm_readymade_category_id)
				WHERE brmm.brmm_item_code = '".$_item_code."'
				LIMIT 1";
		}else{
			$query="SELECT bm.bm_id,
				bm.bm_item_code as _item_code,
				UPPER(supplier.supplier_name) as supplier_name,
                UPPER(fabric.fabric_name) as fabric_name,
                '' as  category_name,
                UPPER(design.design_name) as design_name,
                UPPER(color.color_name) as color_name,
                UPPER(width.width_name) as width_name,
				bm.bm_cost_char as cost_char,
				bm.bm_mrp as mrp,
				((bm.bm_pt_mtr-bm.bm_prt_mtr) - (bm.bm_ot_mtr)) as bal_qty,
				IF(bm.bm_delete_status = 0, 'active', 'deleted') as delete_status,
				bm.bm_delete_status
				FROM barcode_master bm
				INNER JOIN supplier_master supplier ON(supplier.supplier_id = bm.bm_supplier_id)
				INNER JOIN fabric_master fabric ON(fabric.fabric_id = bm.bm_fabric_id)
                LEFT JOIN design_master design ON(design.design_id = bm.bm_design_id)
                INNER JOIN color_master color ON(color.color_id = bm.bm_color_id)
                INNER JOIN width_master width ON(width.width_id = bm.bm_width_id)
				WHERE bm.bm_item_code = '".$_item_code."'
				LIMIT 1";
		}
		$data = $this->db->query($query)->result_array();
		// echo "<pre>"; print_r($query); exit();
        // echo "<pre>"; print_r($data); exit();
		return $this->get_detail_html($data,$type);
    }
	public function get_detail_html($data,$type){
		$html = "<div>
					<h6 class='text-center text-light text-uppercase neu_flat_secondary py-1'>product detail &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<input 
                                type='checkbox' 
                                id='type' 
                                name='type' 
                                data-toggle='toggle' 
                                data-on='OTHER' 
                                data-off='FABRIC' 
                                data-onstyle='secondary' 
                                data-offstyle='secondary' 
                                data-width='130' 
                                data-size='small'
                                ".$type."
                            /></h6>
					<table class='table table-sm table-reponsive'>
						<tbody class='font-weight-bold text-uppercase' style='font-size:0.8em;'>
							<tr>
								<td width='32%'>barcode</td>
								<td width='68%'>
									<select 
										class='form-control floating-select select2' 
										id='_item_code' 
										name='_item_code'
										><option value='".(empty($data) ? '' : $data[0]['_item_code'])."'>".(empty($data) ? '' : $data[0]['_item_code'])."</option>
									</select>
								</td>
							</tr>
							<tr>
								<td width='32%'>supplier</td>
								<td width='68%'>: ".(empty($data) ? '---' : $data[0]['supplier_name'])."</td>
							</tr>
							<tr>
								<td width='32%'>fabric</td>
								<td width='68%'>: ".(empty($data) ? '---' : $data[0]['fabric_name'])."</td>
							</tr>
							<tr>
								<td width='32%'>category</td>
								<td width='68%'>: ".(empty($data) ? '---' : $data[0]['category_name'])."</td>
							</tr>
							<tr>
								<td width='32%'>design</td>
								<td width='68%'>: ".(empty($data) ? '---' : $data[0]['design_name'])."</td>
							</tr>
							<tr>
								<td width='32%'>color</td>
								<td width='68%'>: ".(empty($data) ? '---' : $data[0]['color_name'])."</td>
							</tr>
							<tr>
								<td width='32%'>width</td>
								<td width='68%'>: ".(empty($data) ? '---' : $data[0]['width_name'])."</td>
							</tr>
							<tr>
								<td width='32%'>purchase rate</td>
								<td width='68%'>: ".(empty($data) ? '---' : $data[0]['cost_char'])."</td>
							</tr>
							<tr>
								<td width='32%'>mrp</td>
								<td width='68%' class='d-flex'>: ";
									if(empty($data)):
										$html .="---";
									else:
										if($data[0]['bm_delete_status'] == 0 && $data[0]['bal_qty'] > 0):
											$html .="<div class='d-flex'>
														<input
															type='number' 
															class='form-control text-dark font-weight-bold mx-1' 
															id='bm_mrp' 
															value='{$data[0]['mrp']}'
															min='{$data[0]['mrp']}' 
															oninput='this.value = Math.abs(this.value)'
															placeholder=' 
															autocomplete='off'
															style='width: 80px; height: 25px; font-size:0.8rem; background: var(--bg-color-primary); border-color: var(--bg-color-secondary);'
														/>
														<a 
															type='button' 
															class='btn btn-sm'
															data-toggle='tooltip' 
															data-placement='top' 
															title='EDIT MRP'
															onclick='update_mrp({$data[0]['bm_id']})'
														><i class='text-info fa fa-save'></i></a>  
													</div>";
										else:
											$html .=$data[0]['mrp'];
										endif;
									endif;
						$html .= "</td>
							</tr>
							<tr>
								<td width='32%'>balance mtr</td>
								<td width='68%'>: ".(empty($data) ? '---' : $data[0]['bal_qty'])."</td>
							</tr>
							<tr>
								<td width='32%'>status</td>
								<td width='68%'>: ".(empty($data) ? '---' : $data[0]['delete_status'])."</td>
							</tr>
						<tbody/>
					</table>
				</div>";
		return $html;
	}
	public function get_history(){
		$_item_code = isset($_REQUEST['_item_code']) && !empty($_REQUEST['_item_code']) ? $_REQUEST['_item_code'] : 'XXX';
		$type = (isset($_REQUEST['type']) && ($_REQUEST['type']=='true'))?'checked':'';
        $record = [];
        if(isset($_REQUEST['type']) && ($_REQUEST['type']=='true')){
        	$query="SELECT 'PURCHASE OTHER' as module,
				prmm.prmm_entry_no as entry_no,
				DATE_FORMAT(prmm.prmm_entry_date, '%d-%m-%Y') as entry_date,
				brmm.brmm_prmt_qty as qty,
				'' as mtr,
				UPPER(supplier.supplier_name) as party_name,
				UPPER(user.user_fullname) as user_name,
				prmm.prmm_created_at as created_at,
				DATE_FORMAT(prmm.prmm_created_at, '%r') as entry_time,
				CONCAT('transaction/purchase_readymade?action=list&_entry_no=', prmm.prmm_entry_no) as url
				FROM purchase_readymade_master prmm
				INNER JOIN supplier_master supplier ON(supplier.supplier_id = prmm.prmm_supplier_id)
				INNER JOIN user_master user ON(user.user_id = prmm.prmm_created_by)
				INNER JOIN branch_master branch ON(branch.branch_id = prmm.prmm_branch_id)
				INNER JOIN purchase_readymade_trans prmt ON(prmt.prmt_prmm_id = prmm.prmm_id)
				INNER JOIN barcode_readymade_master brmm ON(brmm.brmm_prmt_id = prmt.prmt_id)
				LEFT JOIN design_master design ON(design.design_id = prmt.prmt_design_id)
				WHERE brmm.brmm_item_code = '".$_item_code."'";
	        // echo "<pre>"; print_r($query); exit();
	        $data = $this->db->query($query)->result_array();
	        // echo "<pre>"; print_r($data); exit();

	        if (!empty($data)) {
	            foreach ($data as $key => $value) {
	                array_push($record, $value);
	            }
	        }
	        $query="SELECT 'ESTIMATE' as module,
					om.om_em_entry_no as entry_no,
					DATE_FORMAT(om.om_em_entry_date, '%d-%m-%Y') as entry_date,
					SUM(-1 * ot.ot_qty) as qty,
					'' as mtr,
					UPPER(customer.customer_name) as party_name,
					UPPER(user.user_fullname) as user_name,
					om.om_created_at as created_at,
					DATE_FORMAT(om.om_created_at, '%r') as entry_time,
					CONCAT('transaction/estimate?action=list&_entry_no=', om.om_em_entry_no) as url
					FROM order_master om
					INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
					INNER JOIN user_master user ON(user.user_id = om.om_created_by)
					INNER JOIN branch_master branch ON(branch.branch_id = om.om_branch_id)
					INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
					INNER JOIN barcode_readymade_master brmm ON(brmm.brmm_id = ot.ot_brmm_id)
					WHERE om.om_delete_status = 0
					AND ot.ot_delete_status = 0
					AND brmm.brmm_delete_status = 0
					AND om.om_status = 0
					AND brmm.brmm_item_code = '".$_item_code."'
					GROUP BY om.om_id";
	        // echo "<pre>"; print_r($query); exit();
	        $data = $this->db->query($query)->result_array();
	        // echo "<pre>"; print_r($data); exit();

	        if (!empty($data)) {
	            foreach ($data as $key => $value) {
	                array_push($record, $value);
	            }
	        }

	        $query="SELECT 'ORDER' as module,
					om.om_entry_no as entry_no,
					DATE_FORMAT(om.om_entry_date, '%d-%m-%Y') as entry_date,
					SUM(-1 * ot.ot_qty) as qty,
					'' as mtr,
					UPPER(customer.customer_name) as party_name,
					UPPER(user.user_fullname) as user_name,
					om.om_created_at as created_at,
					DATE_FORMAT(om.om_created_at, '%r') as entry_time,
					CONCAT('transaction/order?action=list&_entry_no=', om.om_entry_no) as url
					FROM order_master om
					INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
					INNER JOIN user_master user ON(user.user_id = om.om_created_by)
					INNER JOIN branch_master branch ON(branch.branch_id = om.om_branch_id)
					INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
					INNER JOIN barcode_readymade_master brmm ON(brmm.brmm_id = ot.ot_brmm_id)
					WHERE om.om_delete_status = 0
					AND ot.ot_delete_status = 0
					AND brmm.brmm_delete_status = 0
					AND om.om_status = 1
					AND brmm.brmm_item_code = '".$_item_code."'
					GROUP BY om.om_id";
	        // echo "<pre>"; print_r($query); exit();
	        $data = $this->db->query($query)->result_array();
	        // echo "<pre>"; print_r($data); exit();

	        if (!empty($data)) {
	            foreach ($data as $key => $value) {
	                array_push($record, $value);
	            }
	        }
        }else{
        	$query="SELECT 'PURCHASE' as module,
				pm.pm_entry_no as entry_no,
				DATE_FORMAT(pm.pm_entry_date, '%d-%m-%Y') as entry_date,
				bm.bm_pt_mtr as mtr,
				'' as qty,
				UPPER(supplier.supplier_name) as party_name,
				UPPER(user.user_fullname) as user_name,
				pm.pm_created_at as created_at,
				DATE_FORMAT(pm.pm_created_at, '%r') as entry_time,
				CONCAT('transaction/purchase?action=list&_entry_no=', pm.pm_entry_no) as url
				FROM purchase_master pm
				INNER JOIN supplier_master supplier ON(supplier.supplier_id = pm.pm_supplier_id)
				INNER JOIN user_master user ON(user.user_id = pm.pm_created_by)
				INNER JOIN branch_master branch ON(branch.branch_id = pm.pm_branch_id)
				INNER JOIN purchase_trans pt ON(pt.pt_pm_id = pm.pm_id)
				INNER JOIN barcode_master bm ON(bm.bm_pt_id = pt.pt_id)
				LEFT JOIN design_master design ON(design.design_id = pt.pt_design_id)
				WHERE bm.bm_item_code = '".$_item_code."'";
	        // echo "<pre>"; print_r($query); exit();
	        $data = $this->db->query($query)->result_array();
	        // echo "<pre>"; print_r($data); exit();

	        if (!empty($data)) {
	            foreach ($data as $key => $value) {
	                array_push($record, $value);
	            }
	        }
	        $query="SELECT 'ESTIMATE' as module,
					om.om_em_entry_no as entry_no,
					DATE_FORMAT(om.om_em_entry_date, '%d-%m-%Y') as entry_date,
					SUM(-1 * ot.ot_mtr) as mtr,
					'' as qty,
					UPPER(customer.customer_name) as party_name,
					UPPER(user.user_fullname) as user_name,
					om.om_created_at as created_at,
					DATE_FORMAT(om.om_created_at, '%r') as entry_time,
					CONCAT('transaction/estimate?action=list&_entry_no=', om.om_em_entry_no) as url
					FROM order_master om
					INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
					INNER JOIN user_master user ON(user.user_id = om.om_created_by)
					INNER JOIN branch_master branch ON(branch.branch_id = om.om_branch_id)
					INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
					INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_bm_id)
					WHERE om.om_delete_status = 0
					AND ot.ot_delete_status = 0
					AND bm.bm_delete_status = 0
					AND om.om_status = 0
					AND bm.bm_item_code = '".$_item_code."'
					GROUP BY om.om_id";
	        // echo "<pre>"; print_r($query); exit();
	        $data = $this->db->query($query)->result_array();
	        // echo "<pre>"; print_r($data); exit();

	        if (!empty($data)) {
	            foreach ($data as $key => $value) {
	                array_push($record, $value);
	            }
	        }

	        $query="SELECT 'ORDER' as module,
					om.om_entry_no as entry_no,
					DATE_FORMAT(om.om_entry_date, '%d-%m-%Y') as entry_date,
					SUM(-1 * ot.ot_mtr) as mtr,
					'' as qty,
					UPPER(customer.customer_name) as party_name,
					UPPER(user.user_fullname) as user_name,
					om.om_created_at as created_at,
					DATE_FORMAT(om.om_created_at, '%r') as entry_time,
					CONCAT('transaction/order?action=list&_entry_no=', om.om_entry_no) as url
					FROM order_master om
					INNER JOIN customer_master customer ON(customer.customer_id = om.om_customer_id)
					INNER JOIN user_master user ON(user.user_id = om.om_created_by)
					INNER JOIN branch_master branch ON(branch.branch_id = om.om_branch_id)
					INNER JOIN order_trans ot ON(ot.ot_om_id = om.om_id)
					INNER JOIN barcode_master bm ON(bm.bm_id = ot.ot_bm_id)
					WHERE om.om_delete_status = 0
					AND ot.ot_delete_status = 0
					AND bm.bm_delete_status = 0
					AND om.om_status = 1
					AND bm.bm_item_code = '".$_item_code."'
					GROUP BY om.om_id";
	        // echo "<pre>"; print_r($query); exit();
	        $data = $this->db->query($query)->result_array();
	        // echo "<pre>"; print_r($data); exit();

	        if (!empty($data)) {
	            foreach ($data as $key => $value) {
	                array_push($record, $value);
	            }
	        }
	    }    
        if (!empty($record)) {
            usort($record, function ($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });
        }
        return $this->get_history_html($record);
    }
	public function get_history_html($data){
		$html="<div>
					<h6 class='text-center text-light text-uppercase neu_flat_secondary py-1'>history detail</h6>
					<table class='table table-sm table-reponsive font-weight-bold text-uppercase'>
						<thead>
							<tr style='font-size:0.8em;'>
								<td width='2%'>#</td>
								<td width='6%'>action</td>
								<td width='6%'>entry no</td>
								<td width='7%'>entry date</td>
								<td width='7%'>entry time</td>
								<td width='8%'>entry by</td>
								<td width='15%'>party</td>
								<td width='8%'>opening mtr</td>
								<td width='5%'>mtr</td>
								<td width='8%'>closing mtr</td>
								<td width='8%'>opening qty</td>
								<td width='5%'>qty</td>
								<td width='8%'>closing qty</td>
							</tr>
						</thead>
						<tbody>";
						if(empty($data)):
							$html .="<tr><td colspan='9' class='text-center text-danger'>no record found !!!</td></tr>";
						else:
							$open_mtr	= 0;
							$close_mtr	= 0;
							$open_qty	= 0;
							$close_qty	= 0;
							foreach ($data as $key => $value):
								$sr_no 		= $key+1;
								$close_mtr  = $close_mtr + $value['mtr'];
								$close_qty  = $close_qty + $value['qty'];
								$html .="<tr style='font-size:0.7em;'>
											<td >{$sr_no}</td>
											<td >{$value['module']}</td>
											<td >
												<a 
													type='button' 
													class='btn btn-sm font-weight-bold' 
													target='_blank' 
													data-toggle='tooltip' 
													data-placement='top' 
													title='SHOW ENTRY'
													href='".base_url($value['url'])."'
													style='font-size:0.8rem;'
												>{$value['entry_no']} <i class='text-info fa fa-external-link'></i></a>    
											</td>
											<td >{$value['entry_date']}</td>
											<td >{$value['entry_time']}</td>
											<td >{$value['user_name']}</td>
											<td >{$value['party_name']}</td>
											<td >".number_format($open_mtr, 2, '.', '')."</td>
											<td >{$value['mtr']}</td>
											<td >".number_format($close_mtr, 2, '.', '')."</td>
											<td >".number_format($open_qty, 2, '.', '')."</td>
											<td >{$value['qty']}</td>
											<td >".number_format($close_qty, 2, '.', '')."</td>
										</tr>";
								$open_mtr = $close_mtr;
								$open_qty = $close_qty;
							endforeach;
						endif;
			$html .= "	</tbody>
					</table>
				</div>";
		return $html;
	}
     public function _item_code(){
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
      	
	    if(isset($_GET['param']) && $_GET['param']=='true') { 
		
			if((isset($_GET['name']) && !empty($_GET['name']))){   
				$name 	= $_GET['name'];
	            $subsql .= " AND (brmm.brmm_item_code = '".$name."') ";
			}else{
				$subsql .= " AND (brmm.brmm_item_code = 'XXX') ";
			}
			$query="SELECT brmm.brmm_item_code as id, brmm.brmm_item_code as name
					FROM barcode_readymade_master brmm
					WHERE 1
					$subsql
					GROUP BY brmm.brmm_item_code 
					ORDER BY brmm.brmm_item_code ASC
					LIMIT 1";
			// echo "<pre>"; print_r($query); exit;
			return $this->db->query($query)->result_array();
		}else{
           
			if((isset($_GET['name']) && !empty($_GET['name']))){
				$name 	= $_GET['name'];
	            $subsql .= " AND (bm.bm_item_code = '".$name."') ";
			}else{
				$subsql .= " AND (bm.bm_item_code = 'XXX') ";
			}
			$query="SELECT bm.bm_item_code as id, bm.bm_item_code as name
					FROM barcode_master bm
					WHERE 1
					$subsql
					GROUP BY bm.bm_item_code 
					ORDER BY bm.bm_item_code ASC
					LIMIT 1";
			// echo "<pre>"; print_r($query); exit;
			return $this->db->query($query)->result_array();
		}	
    }
}
?>