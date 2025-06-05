<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class maap_model extends my_model{
	public function __construct(){ parent::__construct('master', 'maap'); }
	public function isExist($id){
		// $data = $this->db->query("SELECT pt_id FROM purchase_trans WHERE pt_maap_id = $id LIMIT 1")->result_array();
		// if(!empty($data)) return true;

		return false;
	}
}
?>
