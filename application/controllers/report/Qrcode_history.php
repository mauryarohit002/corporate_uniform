<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class qrcode_history extends my_controller{
	protected $menu;
    protected $sub_menu;
	public function __construct(){
		$this->menu     = 'report';
        $this->sub_menu = 'qrcode_history';
		parent::__construct($this->menu, $this->sub_menu); 
	}
	public function index(){	
		$result = isLoggedIn();
		// echo "<pre>"; print_r($_POST);exit;
		if(!$result['session'] || !$result['status'] || !$result['active']){
			redirect('login/logout?msg='.$result['msg']);
			return;
		}
		$result     = isMenuAssigned($this->menu, $this->sub_menu);
		$action_data= get_action_data($this->menu, $this->sub_menu);
		$menu_data  = get_submenu_data($this->menu, $this->sub_menu);
		if(!$result['session'] || !$result['status'] || !$result['active']){
			$this->load->view('errors/unauthorized'); return;
		}
		$record['menu']		    = $this->menu;
		$record['sub_menu']		= $this->sub_menu;
		$record['action_data']	= $action_data;
		$record['menu_name']    = $menu_data['menu_name'];
		$record['sub_menu_name']= $menu_data['sub_menu_name'];
		$record['data']			= $this->model->get_record();
		// echo "<pre>"; print_r($record); exit;

		$this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/_list', $record);
	}
	public function update_mrp(){
		$post_data  = $this->input->post();
		$id         = $post_data['id'];
		$mrp        = $post_data['mrp'];

		$result     = isMenuAssigned($this->menu, $this->sub_menu, 'update_mrp');
		if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

		$data = $this->db_operations->get_record('barcode_master', ['bm_id' => $id]);
		if(empty($data)) return ['msg' => 'Barcode not found.'];
		if($data[0]['bm_delete_status'] == 1) return ['msg' => 'Barcode is deleted.'];

		$this->db->trans_begin();
		if($this->db_operations->data_update('purchase_trans', ['pt_mrp' => $mrp], 'pt_id', $data[0]['bm_pt_id']) < 1){
			$this->db->trans_rollback();
			return ['msg' => 'Purchase data not updated'];
		}

		if($this->db_operations->data_update('barcode_master', ['bm_mrp' => $mrp], 'bm_id', $id) < 1){
			$this->db->trans_rollback();
			return ['msg' => 'Barcode data not updated'];
		}
		if ($this->db->trans_status() === FALSE){
		    $this->db->trans_rollback();
		    return ['msg' => 'Transaction Rollback.'];
	    }
	    $this->db->trans_commit();
		return ['status' => TRUE, 'data' => encrypt_decrypt("encrypt", $id, SECRET_KEY), 'msg' => 'MRP updated successfully.'];
	}
}
?>
