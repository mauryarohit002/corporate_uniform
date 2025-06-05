<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class other_model extends my_model{
	public function __construct(){ parent::__construct('master', 'other'); }
	public function isExist($id){
		$data = $this->db->query("SELECT sot_id FROM sku_other_trans WHERE sot_other_id = $id LIMIT 1")->result_array();
		if(!empty($data)) return true;

		return false;
	}
}
?>
