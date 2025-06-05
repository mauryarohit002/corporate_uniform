<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
    require_once APPPATH . 'core/MY_Controller.php';
    class my_report extends my_controller {
        protected $menu;
        protected $sub_menu;
        public function __construct($menu, $sub_menu) {
            parent::__construct($menu, $sub_menu);
            $this->menu     = $menu;
            $this->sub_menu = $sub_menu;
        }
        // Server side
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
                $record['url']          = $menu_data['url'];
                $record['data']			= $this->model->get_record();
                // echo "<pre>"; print_r($record); exit;
                $record['total_rows']	= isset($record['data']['totals']['rows']) ? $record['data']['totals']['rows'] : 0;

                $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/_list', $record);
            }
            public function pdf(){
                $record = $this->model->get_record(true);
                $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/pdf/report', $record);
            }
        // Server side
    }
?>