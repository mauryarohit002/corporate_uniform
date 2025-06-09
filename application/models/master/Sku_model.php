<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class sku_model extends my_model{
	public function __construct(){ parent::__construct('master', 'sku'); }
	public function isMasterExist($id){
		$data = $this->db->query("SELECT qt_id FROM quotation_trans WHERE qt_sku_id = $id AND qt_delete_status=0 LIMIT 1")->result_array();
		if(!empty($data)) return true; 
		return false;
	}
    public function isDesignTransExist($id){ 
		// $data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_sku_id = $id LIMIT 1")->result_array();
		// if(!empty($data)) return true;

		return false;
	}
    public function isDyingTransExist($id){
		// $data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_sku_id = $id LIMIT 1")->result_array();
		// if(!empty($data)) return true;

		return false;
	}
    public function iskarigarTransExist($id){
		// $data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_sku_id = $id LIMIT 1")->result_array();
		// if(!empty($data)) return true;

		return false;
	}
    public function isEmbroideryTransExist($id){
		// $data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_sku_id = $id LIMIT 1")->result_array();
		// if(!empty($data)) return true;

		return false;
	}
    public function isOtherTransExist($id){
		// $data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_sku_id = $id LIMIT 1")->result_array();
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
        
        if(isset($_GET['_sku_name']) && !empty($_GET['_sku_name'])){
            $subsql .=" AND sku.sku_name LIKE '%".$_GET['_sku_name']."%'";
            $record['filter']['_sku_name']['value'] = $_GET['_sku_name'];
            $record['filter']['_sku_name']['text'] = $_GET['_sku_name'];
        }
       
        if(isset($_GET['_customer_name']) && !empty($_GET['_customer_name'])){
            $subsql .=" AND customer.customer_name LIKE '%".$_GET['_customer_name']."%'";
            $record['filter']['_customer_name']['value'] = $_GET['_customer_name'];
            $record['filter']['_customer_name']['text'] = $_GET['_customer_name'];
        }
        if(isset($_GET['_department_name']) && !empty($_GET['_department_name'])){
            $subsql .=" AND department.department_name LIKE '%".$_GET['_department_name']."%'";
            $record['filter']['_department_name']['value'] = $_GET['_department_name'];
            $record['filter']['_department_name']['text'] = $_GET['_department_name'];
        }
        if(isset($_GET['_apparel_name']) && !empty($_GET['_apparel_name'])){
            $subsql .=" AND apparel.apparel_name LIKE '%".$_GET['_apparel_name']."%'";
            $record['filter']['_apparel_name']['value'] = $_GET['_apparel_name'];
            $record['filter']['_apparel_name']['text'] = $_GET['_apparel_name'];
        }
        if(isset($_GET['_color_name']) && !empty($_GET['_color_name'])){
            $subsql .=" AND color.color_name LIKE '%".$_GET['_color_name']."%'";
            $record['filter']['_color_name']['value'] = $_GET['_color_name'];
            $record['filter']['_color_name']['text'] = $_GET['_color_name'];
        }
        if(isset($_GET['_status'])){
            $status = $_GET['_status'] == 2 ? 0 : $_GET['_status'];
            $subsql .=" AND sku.sku_status = ".$status;
            $record['filter']['_status'] = $this->Commonmdl->get_status($_GET['_status']);
        }
        $query="SELECT sku.*,
                UPPER(apparel.apparel_name) as apparel_name,
                IFNULL(UPPER(customer.customer_name), '') as customer_name,
                IFNULL(UPPER(department.department_name), '') as department_name,
                IFNULL(UPPER(color.color_name), '') as color_name
                FROM sku_master sku
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = sku.sku_apparel_id)
                LEFT JOIN customer_master customer ON(customer.customer_id = sku.sku_customer_id)
                LEFT JOIN department_master department ON(department.department_id = sku.sku_department_id)
                LEFT JOIN color_master color ON(color.color_id = sku.sku_color_id)
                WHERE sku.sku_delete_status = 0
                $subsql
                ORDER BY sku.sku_name ASC
                $limit
                $ofset";
        // echo "<pre>"; print_r($query); exit;
        if($wantCount){
            return $this->db->query($query)->num_rows();
        }
        $record['data'] = $this->db->query($query)->result_array();
        if(!empty($record['data'])){
            foreach ($record['data'] as $key => $value) {
                $record['data'][$key]['isExist'] = $this->isMasterExist($value['sku_id']);
            }
        }
         // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_data_for_add(){
        $record['sku_uuid'] = $_SESSION['user_id'].''.time();
        return $record;
    }
    public function get_data_for_edit($id){
        $query="SELECT sku.*,
                UPPER(apparel.apparel_name) as apparel_name,
                IFNULL(UPPER(customer.customer_name), '') as customer_name,
                IFNULL(UPPER(department.department_name), '') as department_name,
                IFNULL(UPPER(color.color_name), '') as color_name
                FROM sku_master sku
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = sku.sku_apparel_id)
                LEFT JOIN customer_master customer ON(customer.customer_id = sku.sku_customer_id)
                LEFT JOIN department_master department ON(department.department_id = sku.sku_department_id)
                LEFT JOIN color_master color ON(color.color_id = sku.sku_color_id)
                WHERE sku.sku_id = $id
                AND sku.sku_delete_status = 0";
        $record['master_data'] = $this->db->query($query)->result_array();
        if(!empty($record['master_data'])){
            $record['master_data'][0]['isExist'] = $this->isMasterExist($id);
        }
        // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_transaction($sku_id){  
        $query="SELECT sdt.*,
                UPPER(fabric.fabric_name) as fabric_name,
                UPPER(color.color_name) as color_name,
                UPPER(width.width_name) as width_name,
                UPPER(design.design_name) as design_name,
                design.design_image
                FROM sku_design_trans sdt
                INNER JOIN design_master design ON(design.design_id = sdt.sdt_design_id)
                LEFT JOIN fabric_master fabric ON(fabric.fabric_id = sdt.sdt_fabric_id)
                LEFT JOIN color_master color ON(color.color_id = sdt.sdt_color_id)
                LEFT JOIN width_master width ON(width.width_id = sdt.sdt_width_id)
                WHERE sdt.sdt_sku_id = $sku_id
                AND sdt.sdt_delete_status = 0
                ORDER BY sdt.sdt_id DESC";
        $record['design_trans'] = $this->db->query($query)->result_array();
        if(!empty($record['design_trans'])){
            foreach ($record['design_trans'] as $key => $value) {
                $record['design_trans'][$key]['isExist'] = $this->isDesignTransExist($value['sdt_id']);
            }
        }

        $query="SELECT sdyt.*,
                UPPER(dying.dying_name) as dying_name
                FROM sku_dying_trans sdyt
                INNER JOIN dying_master dying ON(dying.dying_id = sdyt.sdyt_dying_id)
                WHERE sdyt.sdyt_sku_id = $sku_id
                AND sdyt.sdyt_delete_status = 0
                ORDER BY sdyt.sdyt_id DESC";
        $record['dying_trans'] = $this->db->query($query)->result_array();

        $query="SELECT skt.*,
                UPPER(karigar.karigar_name) as karigar_name,
                UPPER(apparel.apparel_name) as apparel_name
                FROM sku_karigar_trans skt
                INNER JOIN karigar_master karigar ON(karigar.karigar_id = skt.skt_karigar_id)
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = skt.skt_apparel_id)
                WHERE skt.skt_sku_id = $sku_id
                AND skt.skt_delete_status = 0
                ORDER BY skt.skt_id DESC";
        $record['karigar_trans'] = $this->db->query($query)->result_array();

        $query="SELECT sett.*,
                UPPER(embroidery.embroidery_name) as embroidery_name
                FROM sku_embroidery_trans sett
                INNER JOIN embroidery_master embroidery ON(embroidery.embroidery_id = sett.set_embroidery_id)
                WHERE sett.set_sku_id = $sku_id
                AND sett.set_delete_status = 0
                ORDER BY sett.set_id DESC";
        $record['embroidery_trans'] = $this->db->query($query)->result_array();

        $query="SELECT sot.*,
                UPPER(other.other_name) as other_name
                FROM sku_other_trans sot
                INNER JOIN other_master other ON(other.other_id = sot.sot_other_id)
                WHERE sot.sot_sku_id = $sku_id
                AND sot.sot_delete_status = 0
                ORDER BY sot.sot_id DESC";
        $record['other_trans'] = $this->db->query($query)->result_array();

        $query="SELECT sit.*
                FROM sku_image_trans sit
                WHERE sit.sit_sku_id = $sku_id
                AND sit.sit_delete_status = 0
                ORDER BY sit.sit_id DESC";
        $record['image_trans'] = $this->db->query($query)->result_array();

        // echo "<pre>"; print_r($record); exit;
        return $record;
    }
    public function get_barcode_data($id){
		$query="SELECT 
                design.design_id,
                design.design_name,
                design.design_image,
                bm.bm_pt_rate as rate,
                (((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_ott_mtr + bm.bm_et_mtr + bm.bm_prt_mtr)) + bm.bm_am_mtr)) as bal_qty
				FROM barcode_master bm
                INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
				WHERE bm.bm_delete_status = 0
                AND bm.bm_id = $id
                HAVING bal_qty > 0";
		return $this->db->query($query)->result_array();
	}
    public function get_name($term, $id){
        $query="SELECT UPPER(".$term."_name) as name FROM ".$term."_master WHERE ".$term."_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? '' : $data[0]['name'];
    }
    public function get_design_id($id){
        $query="SELECT bm.bm_design_id as id FROM barcode_master bm WHERE bm.bm_id = $id";
        $data = $this->db->query($query)->result_array();
        return empty($data) ? 0 : $data[0]['id'];
    }
    public function _id($args = []){
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
            $subsql .= " AND (sku.sku_name LIKE '".$name."%' OR apparel.apparel_name LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param 	= $_GET['param'];
            $subsql .= " AND (sku.sku_status = $param) ";
        }
        $query="SELECT 
                sku.sku_id as id, 
                CONCAT(UPPER(sku.sku_name), ' - ', UPPER(apparel.apparel_name)) as name
                FROM sku_master sku
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = sku.sku_apparel_id)
                WHERE sku.sku_delete_status = 0
                $subsql
                GROUP BY sku.sku_id 
                ORDER BY sku.sku_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _design_id($args = []){
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
            $subsql .= " AND (design.design_name LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param 	= $_GET['param'];
            $subsql .= " AND (design.design_status = $param) ";
        }
        $query="SELECT 
                design.design_id as id, 
                UPPER(design.design_name) as name
                FROM design_master design
                WHERE 1
                $subsql
                GROUP BY design.design_id 
                ORDER BY design.design_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _bm_id($args = []){
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
                // $subsql .= " AND (bm.bm_item_code = 'XXX') ";
            }
        }  
		$query="SELECT bm.bm_id as id, 
				CONCAT(bm.bm_item_code, ' - ', UPPER(design.design_name)) as name,
                (((bm.bm_pt_mtr - (bm.bm_ot_mtr + bm.bm_ott_mtr + bm.bm_et_mtr + bm.bm_prt_mtr)) + bm.bm_am_mtr)) as bal_qty
				FROM barcode_master bm
                INNER JOIN design_master design ON(design.design_id = bm.bm_design_id)
				WHERE bm.bm_delete_status = 0
                $subsql
				GROUP BY bm.bm_id
                HAVING bal_qty > 0
				ORDER BY bm.bm_item_code ASC
				LIMIT $limit
				OFFSET $offset";
		// echo $query; exit();
		return $this->db->query($query)->result_array();
	}
    public function _sku_name($args = []){
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
            $subsql .= " AND (sku.sku_name LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param 	= $_GET['param'];
            $subsql .= " AND (sku.sku_status = $param) ";
        }
        $query="SELECT 
                sku.sku_name as id, 
                UPPER(sku.sku_name) as name
                FROM sku_master sku
                WHERE sku.sku_delete_status = 0
                $subsql
                GROUP BY sku.sku_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
    public function _apparel_name($args = []){
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
            $subsql .= " AND (apparel.apparel_name LIKE '".$name."%') ";
        }
        if(isset($_GET['param']) && !empty($_GET['param'])){
            $param 	= $_GET['param'];
            $subsql .= " AND (apparel.apparel_status = $param) ";
        }
        $query="SELECT 
                apparel.apparel_name as id, 
                UPPER(apparel.apparel_name) as name
                FROM sku_master sku
                INNER JOIN apparel_master apparel ON(apparel.apparel_id = sku.sku_apparel_id)
                WHERE sku.sku_delete_status = 0
                $subsql
                GROUP BY apparel.apparel_name ASC
                LIMIT $limit
                OFFSET $offset";
        // echo $query; exit();
        return $this->db->query($query)->result_array();
    }
}
?>
