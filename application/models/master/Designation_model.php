<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Model.php';
class designation_model extends my_model{
	public function __construct(){ parent::__construct('master', 'designation'); }
	public function isExist($id){
		return false;
	}
}
?>
