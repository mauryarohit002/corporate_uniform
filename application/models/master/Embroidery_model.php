<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class embroidery_model extends my_model{
	public function __construct(){ parent::__construct('master', 'embroidery'); }
	public function isExist($id){
		$data = $this->db->query("SELECT set_id FROM sku_embroidery_trans WHERE set_embroidery_id = $id LIMIT 1")->result_array();
		if(!empty($data)) return true;

		return false;
	}
}
?>
