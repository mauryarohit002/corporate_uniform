<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class branch_model extends my_model{
	public function __construct(){ parent::__construct('master', 'branch'); }
	public function isExist($id){
		$data = $this->db->query("SELECT user_id FROM user_master WHERE user_branch_id = $id LIMIT 1")->result_array();
		if(!empty($data)) return true;

		return false;
	}
}
?>