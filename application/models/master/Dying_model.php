<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class dying_model extends my_model{
	public function __construct(){ parent::__construct('master', 'dying'); }
	public function isExist($id){
		$data = $this->db->query("SELECT sdyt_id FROM sku_dying_trans WHERE sdyt_dying_id = $id LIMIT 1")->result_array();
		if(!empty($data)) return true;

		return false;
	}
}
?>
