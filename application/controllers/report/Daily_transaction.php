<?php defined('BASEPATH') OR exit('No direct script access allowed');
class daily_transaction extends CI_Controller{
    protected $menu;
    protected $sub_menu;
	public function __construct(){
		parent::__construct();

        $this->menu     = 'report';
        $this->sub_menu = 'daily_transaction';

		$this->load->model($this->menu.'/'.$this->sub_menu.'_model', 'model');
		$this->config->load('extra');
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
		// $record['total_rows']	= $record['data']['totals']['rows'];
		// echo "<pre>"; print_r($record); exit;

		$this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/_list', $record);
	}
	
	public function pdf(){
		$record['data'] = $this->model->get_record();
		// echo "<pre>";print_r($record);die;
		$this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/pdf/report', $record);
	}

	public function get_select2($func){
		$json = [];
		$data = $this->model->$func();
		foreach ($data as $key => $value){
			$json[] = ['id'=>$value['id'], 'text'=>$value['name']];
		}
		echo json_encode($json);
	}
}
?>
