<?php
    class my_controller extends CI_Controller {
        protected $menu;
        protected $sub_menu;
        public $model;
        public $Commonmdl;
        public $pagination;

        public function __construct($menu, $sub_menu) {
           parent::__construct();

            $this->menu     = $menu;
            $this->sub_menu = $sub_menu;

            $this->load->model($menu.'/'.$sub_menu.'_model', 'model');
            $this->load->library('pagination');
		    $this->config->load('extra');
        }
        // Server side
            public function index(){	
                $result = isLoggedIn();
                if(!$result['session'] || !$result['status'] || !$result['active']){
                    redirect('login/logout?msg='.$result['msg']);
                    return;
                }
                // pre($result);exit;
                $result     = isMenuAssigned($this->menu, $this->sub_menu);
                $action_data= get_action_data($this->menu, $this->sub_menu);
                $menu_data  = get_submenu_data($this->menu, $this->sub_menu);
                if(!$result['session'] || !$result['status'] || !$result['active']){
                    $this->load->view('errors/unauthorized'); return;
                }
                if(!isset($_GET['action']) || (isset($_GET['action']) && empty($_GET['action']))){
                    $_GET['action'] = 'list';
                }
                if($_GET['action'] == 'add'){
                    $result = isMenuAssigned($this->menu, $this->sub_menu, $_GET['action']);
                    if(!$result['session'] || !$result['status'] || !$result['active']){
                        $this->load->view('errors/unauthorized'); return;
                    }
                    $record                 = $this->model->get_data_for_add();
                    $record['menu']		    = $this->menu;
                    $record['sub_menu']		= $this->sub_menu;
                    $record['action_data']	= $action_data;
                    $record['menu_name']    = $menu_data['menu_name'];
                    $record['sub_menu_name']= $menu_data['sub_menu_name'];
                    $record['url']          = $menu_data['url'];
                    // echo "<pre>"; print_r($record); exit;
                    $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/_form', $record); return ;
                }
                if(in_array($_GET['action'], ['edit'])){
                    $result = isMenuAssigned($this->menu, $this->sub_menu, $_GET['action']);
                    if(!$result['session'] || !$result['status'] || !$result['active']){
                        $this->load->view('errors/unauthorized'); return;
                    }
                    if(!isset($_GET['id']) || (isset($_GET['id']) && empty($_GET['id']))){
                        $this->load->view('errors/not_found'); return;
                    }
                    $id = encrypt_decrypt("decrypt", $_GET['id'], SECRET_KEY);
                    if(empty($id)){
                        $this->load->view('errors/not_found');return;	
                    }
                    $record                 = $this->model->get_data_for_edit($id);
                    $record['menu']		    = $this->menu;
                    $record['sub_menu']		= $this->sub_menu;
                    $record['action_data']	= $action_data;
                    $record['menu_name']    = $menu_data['menu_name'];
                    $record['sub_menu_name']= $menu_data['sub_menu_name'];
                    $record['url']          = $menu_data['url'];

                    if(empty($record['master_data'])){
                        $this->load->view('errors/not_found');return;	
                    }

                    // echo "<pre>"; print_r($record); exit;
                    $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/_form', $record); return ;
                }
                if(in_array($_GET['action'], ['read'])){
                    $result = isMenuAssigned($this->menu, $this->sub_menu, $_GET['action']);
                    if(!$result['session'] || !$result['status'] || !$result['active']){
                        $this->load->view('errors/unauthorized'); return;
                    }
                    if(!isset($_GET['id']) || (isset($_GET['id']) && empty($_GET['id']))){
                        $this->load->view('errors/not_found'); return;
                    }
                    $id = encrypt_decrypt("decrypt", $_GET['id'], SECRET_KEY);
                    if(empty($id)){
                        $this->load->view('errors/not_found');return;	
                    }
                    $record                 = $this->model->get_data_for_read($id);
                    $record['menu']		    = $this->menu;
                    $record['sub_menu']		= $this->sub_menu;
                    $record['action_data']	= $action_data;
                    $record['menu_name']    = $menu_data['menu_name'];
                    $record['sub_menu_name']= $menu_data['sub_menu_name'];
                    $record['url']          = $menu_data['url'];

                    if(empty($record['master_data'])){
                        $this->load->view('errors/not_found');return;	
                    }

                    // echo "<pre>"; print_r($record); exit;
                    $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/_read', $record); return ;
                }
                if($_GET['action'] == 'qrcode'){
                    if(!isset($_GET['id']) || (isset($_GET['id']) && empty($_GET['id']))){
                        $this->load->view('errors/not_found');
                        return;
                    }
                    $clause = $_GET['clause'];
                    if(empty($clause)){
                        $this->load->view('errors/not_found');
                        return;	
                    }
                    $id = encrypt_decrypt("decrypt", $_GET['id'], SECRET_KEY);
                    if(empty($id)){
                        $this->load->view('errors/not_found');
                        return;	
                    }
                    $record['data'] = $this->model->get_data_for_qrcode_print($clause, $id);
                    // echo "<pre>"; print_r($record); exit;
                    $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/pdf/qrcode', $record);
                    return;	
                }
                if($_GET['action'] == 'qrcode_with_measurement'){
                    if(!isset($_GET['id']) || (isset($_GET['id']) && empty($_GET['id']))){
                        $this->load->view('errors/not_found');
                        return;
                    }
                    $clause = $_GET['clause'];
                    if(empty($clause)){
                        $this->load->view('errors/not_found');
                        return;	
                    }
                    $id = encrypt_decrypt("decrypt", $_GET['id'], SECRET_KEY);
                    if(empty($id)){
                        $this->load->view('errors/not_found');
                        return;	
                    }
                    $record['data'] = $this->model->get_data_for_qrcode_print($clause, $id);
                    // echo "<pre>"; print_r($record); exit;
                    $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/pdf/qrcode_with_measurement', $record);
                    return;	
                }
                if($_GET['action'] == 'print'){
                    if(!isset($_GET['id']) || (isset($_GET['id']) && empty($_GET['id']))){
                        $this->load->view('errors/not_found');
                        return;
                    }
                    $id = encrypt_decrypt("decrypt", $_GET['id'], SECRET_KEY);
                    if(empty($id)){
                        $this->load->view('errors/not_found');
                        return;	
                    }
                    $record = $this->model->get_data_for_print($id);
                    // echo "<pre>"; print_r($record); exit;
                    $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/pdf/bill', $record);
                    return;	
                }
                $config 				= array();
                $config 				= $this->config->item('pagination');	
                $config['total_rows'] 	= $this->model->get_list(true);
                $config['base_url'] 	= base_url($this->menu.'/'.$this->sub_menu.'?search=true');
        
                foreach ($_GET as $key => $value){
                    if($key != 'search' && $key != 'offset'){
                        $config['base_url'] .= "&" . $key . "=" .$value;
                    }
                }
        
                $offset = (!empty($_GET['offset'])) ? $_GET['offset'] : 0;
                $this->pagination->initialize($config);
        
                $record['menu']		    = $this->menu;
                $record['sub_menu']		= $this->sub_menu;
                $record['action_data']	= $action_data;
                $record['menu_name']    = $menu_data['menu_name'];
                $record['sub_menu_name']= $menu_data['sub_menu_name'];
                $record['url']			= $menu_data['url'];
                $record['count']		= $offset;
                $record['total_rows'] 	= $config['total_rows'];
                $record['data']			= $this->model->get_list(false, $config['per_page'], $offset);
                // echo "<pre>"; print_r($record); exit;
                
                $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/_list', $record);
            }
            public function pdf(){	
                $result = isLoggedIn();
                // echo "<pre>"; print_r($result);exit;
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
                
                if(!isset($_GET['type'])) {
                    $this->load->view('errors/not_found'); return ;    
                }

                if(isset($_GET['type']) && empty($_GET['type'])) {
                    $this->load->view('errors/not_found'); return ;    
                }

                $type   = $_GET['type'];
                // $result = isMenuAssigned($this->menu, $this->sub_menu, $type);
                // if(!$result['session'] || !$result['status'] || !$result['active']){
                //     $this->load->view('errors/unauthorized'); return;
                // }
                
                if(!isset($_GET['clause']) || (isset($_GET['clause']) && empty($_GET['clause']))){
                    $this->load->view('errors/not_found');
                    return;
                }
                if(!isset($_GET['id']) || (isset($_GET['id']) && empty($_GET['id']))){
                    $this->load->view('errors/not_found');
                    return;
                }
                $_GET['id'] = encrypt_decrypt("decrypt", $_GET['id'], SECRET_KEY);
                if(empty($_GET['id'])){
                    $this->load->view('errors/not_found');
                    return;	
                }
                $func           = "get_data_for_$type";
                $record['data'] = $this->model->$func();
                // echo "<pre>"; print_r($record); exit;
                $this->load->view('pages/'.$this->menu.'/'.$this->sub_menu.'/pdf/'.$type, $record);
            }
        // Server side

        // Client side
            public function select_2($func){
                $json = [];
                $data = $this->model->select_2($func);
                foreach ($data as $key => $value){
                    $json[] = ['id'=>$value['id'], 'text'=>$value['name']];
                }
                echo json_encode($json);
            }
            public function get_select2($func){
                $json = [];
                $data = $this->model->$func();
                foreach ($data as $key => $value){
                    $json[] = ['id'=>$value['id'], 'text'=>$value['name']];
                }
                echo json_encode($json);
            }
            public function get_select2_status(){
                $json = [];
                $data = $this->config->item('status');
                foreach ($data as $key => $value){
                    $json[] = ['id'=>$key, 'text'=>$value];
                }
                echo json_encode($json);
            }
            public function handler(){
                $result = isLoggedIn();
                // echo "<pre>"; print_r($result); exit;
                if(!$result['session'] || !$result['status'] || !$result['active']){
                    echo json_encode($result);
                    return;
                }
                $post_data = $this->input->post();
                if(empty($post_data)){
                    echo json_encode(['session' => TRUE, 'status' => FALSE, 'data' => [], 'msg' => '1. Form data is empty.']);
                    return;
                }
                $func = $post_data['func'];
                $result = $this->$func();
        
                $resp['session'] = isset($result['session']) ? $result['session'] : TRUE;
                $resp['status']  = isset($result['status']) ? $result['status'] : FALSE;
                $resp['data']    = isset($result['data'])   ? $result['data']   : [];
                $resp['msg']     = isset($result['msg'])    ? $result['msg']    : '';
                echo json_encode($resp);
            }
            public function get_data(){
                $post_data  = $this->input->post();
                $id         = $post_data['id'];
                // $result     = isMenuAssigned($this->menu, $this->sub_menu, ($id == 0 ? 'add': 'edit'));
                // if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

                $data = $this->model->get_data($id);
                if(empty($data)) return['msg' => ucfirst($this->sub_menu).' not found.'];	
                return['status' => TRUE, 'data' => $data, 'msg' => ucfirst($this->sub_menu).' fetched successfully.'];
            }
            public function get_default(){
                $post_data  = $this->input->post();
                $post_data['trans_data'] = json_decode($post_data['trans_data'], true);
                if(!isset($post_data['trans_data']) || (isset($post_data['trans_data']) && empty($post_data['trans_data']))) return ['msg' => '1. Default list is empty.'];
                // echo "<pre>"; print_r($post_data); exit;
                $data = [];
                foreach ($post_data['trans_data'] as $key => $value) {
                    $data[$value['resp']] = $this->db_operations->get_record($value['table'], [$value['field'] => $value['value']]);
                }
                return['status' => TRUE, 'data' => $data, 'msg' => 'Record fetched successfully.'];
            }
            public function get_transaction(){
                $post_data  = $this->input->post();
                $id         = $post_data['id'];
                
                $id = encrypt_decrypt("decrypt", $id, SECRET_KEY);
                if(empty($id)) return ['msg' => '1. Id not define.'];	

                $data = $this->model->get_transaction($id);
                if(empty($data)) return['msg' => ucfirst($this->sub_menu).' not found.'];	
                return['status' => TRUE, 'data' => $data, 'msg' => ucfirst($this->sub_menu).' fetched successfully.'];
            }
            public function get_record(){
                if(isset($_POST['sub_func'])){
                    $func   = $_POST['sub_func'];
                    if(isset($_POST['filters'])) {
                        $filters= json_decode($_POST['filters'], true);
                        foreach ($filters as $key => $value) $_REQUEST[$key] = $value;
                    }
                    $data = $this->model->$func();
                    if(empty($data)) return['msg' => 'Record not found.'];	
                    return['status' => TRUE, 'data' => $data, 'msg' => 'Record fetched successfully.'];
                }
                $func = $_POST['func'];
                return $this->$func();
            }
            public function remove(){
                $post_data  = $this->input->post();
                $id         = $post_data['id'];
                $result     = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
                if(!$result['session'] || !$result['status'] || !$result['active']) return $result;
        
                $data = $this->db_operations->get_record($this->sub_menu.'_master', [$this->sub_menu.'_id' => $id]);
                if(empty($data)) return ['status' => REFRESH, 'msg' => ucfirst($this->sub_menu).' not found.'];	
                
                if(isset($data[0][$this->sub_menu.'_default'])){
                    if($data[0][$this->sub_menu.'_default'] == 1) return ['msg' => '1. Not allowed to delete.'];
                }

                if(isset($data[0][$this->sub_menu.'_constant'])){
                    if(!empty($data[0][$this->sub_menu.'_constant'])) return ['msg' => '2. Not allowed to delete.'];
                }
                
                if($this->model->isExist($id)) return ['msg' => '3. Not allowed to delete.'];	
        
                if($this->db_operations->delete_record($this->sub_menu.'_master', [$this->sub_menu.'_id' => $id]) < 1) return ['msg' => ucfirst($this->sub_menu).' not deleted.'];
        
                return ['status' => TRUE, 'msg' => ucfirst($this->sub_menu).' deleted successfully'];
            }
            public function add_update(){
                $post_data  = $this->input->post();
                $id         = $post_data['id'];
                $result     = isMenuAssigned($this->menu, $this->sub_menu, ($id == 0 ? 'add' : 'edit'));
                if(!$result['session'] || !$result['status'] || !$result['active']) return $result;
                
                unset($post_data['func']);
                unset($post_data['id']);

                $post_data[$this->sub_menu.'_status'] 		= isset($post_data[$this->sub_menu.'_status']);
                $post_data[$this->sub_menu.'_updated_by'] 	= $_SESSION['user_id'];
                $post_data[$this->sub_menu.'_updated_at'] 	= date('Y-m-d H:i:s');
        
                $temp = $this->db_operations->get_record($this->sub_menu.'_master', [$this->sub_menu.'_id !=' => $id, $this->sub_menu.'_name' => $post_data[$this->sub_menu.'_name']]);
                if(!empty($temp)) return ['msg' => ucfirst($this->sub_menu).' already exist.'];	
        
                $this->db->trans_begin();
                if($id == 0){
                    $post_data[$this->sub_menu.'_created_by'] 	= $_SESSION['user_id'];
                    $post_data[$this->sub_menu.'_created_at'] 	= date('Y-m-d H:i:s');
        
                    $id 	= $this->db_operations->data_insert($this->sub_menu.'_master', $post_data);
                    $msg 	= ucfirst($this->sub_menu).' added successfully.';
                    if($id < 1){
                        $this->db->trans_rollback();
                        return ['msg' => ucfirst($this->sub_menu).' not added.'];
                    }
                }else{
                    $prev_data = $this->db_operations->get_record($this->sub_menu.'_master', [$this->sub_menu.'_id' => $id]);
                    if(empty($prev_data)){
                        $this->db->trans_rollback();
                        return['status' => REFRESH, 'msg' => ucfirst($this->sub_menu).' not found.'];
                    }
                    if(isset($prev_data[0][$this->sub_menu.'_default'])){
                        if($prev_data[0][$this->sub_menu.'_default'] == 1){
                            $post_data[$this->sub_menu.'_name'] 	= $prev_data[0][$this->sub_menu.'_name'];
                            $post_data[$this->sub_menu.'_status'] 	= $prev_data[0][$this->sub_menu.'_status'];
                        }
                    }
                    $msg = ucfirst($this->sub_menu).' updated successfully.';
                    if($this->db_operations->data_update($this->sub_menu.'_master', $post_data, $this->sub_menu.'_id', $id) < 1){
                        $this->db->trans_rollback();
                        return ['msg' => ucfirst($this->sub_menu).' not updated.'];
                    }
                }
        
                if ($this->db->trans_status() === FALSE){
                    $this->db->trans_rollback();
                    return ['msg' => '1. Transaction Rollback.'];
                }
                $this->db->trans_commit();
        
                $data['id'] 	= $id;
                $data['name'] 	= strtoupper($post_data[$this->sub_menu.'_name']);
                return['session' => TRUE, 'status' => TRUE, 'data' => $data,  'msg' => $msg];
            }
            public function get_id($arr, $id){
                $record = [];
                foreach ($arr as $key => $value) array_push($record, $value[$id]);
                return $record;
            }
            public function upload_document(){
                $files 		= $_FILES;
                // echo "<pre>"; print_r($files);exit;
                
                if(empty($files)) return ['msg' => '1. Document is empty.'];			
                $cnt = isset($files[$this->sub_menu.'_attachment']['name']) ? count($files[$this->sub_menu.'_attachment']['name']) : 0;
                $data=[];
                for($i = 0; $i < $cnt; $i++){
                    if($files[$this->sub_menu.'_attachment']['error'][$i] != 0) return ['msg' => 'Error in Image.'];
    
                    $_FILES[$this->sub_menu.'_attachment']['name']		= $files[$this->sub_menu.'_attachment']['name'][$i];
                    $_FILES[$this->sub_menu.'_attachment']['type']		= $files[$this->sub_menu.'_attachment']['type'][$i];
                    $_FILES[$this->sub_menu.'_attachment']['tmp_name']	= $files[$this->sub_menu.'_attachment']['tmp_name'][$i];
                    $_FILES[$this->sub_menu.'_attachment']['error']		= $files[$this->sub_menu.'_attachment']['error'][$i];
                    $_FILES[$this->sub_menu.'_attachment']['size']		= $files[$this->sub_menu.'_attachment']['size'][$i];
    
                    unset($config);
                    $config 					= array();
                    $config['upload_path'] 		= 'public/uploads/'.$this->sub_menu.'/';
                    $config['allowed_types'] 	= 'gif|jpg|png|jpeg|pdf';
                    $file_name 					= $files[$this->sub_menu.'_attachment']['name'][$i];
    
                    $ext 						= strtolower(substr($file_name, strrpos($file_name, '.') + 1));
                    $filename 					= $i.''.time().'.'.$ext;
                    $config['file_name'] 		= $filename;
    
                    if(!file_exists($config['upload_path'])) mkdir($config['upload_path'], 0777);
                    $this->upload->initialize($config);
                    if(!$this->upload->do_upload($this->sub_menu.'_attachment')) return ['msg' => 'Document not uploaded.'];
                    $imageinfo = $this->upload->data();
                    $full_path = $imageinfo['full_path'];
                        
                    // check EXIF and autorotate if needed
                    // $this->db_operations->image_autorotate_resize(array('filepath' => $full_path), TRUE);		
                    $result = $this->add_attachment(['path' => uploads($this->sub_menu.'/'.$filename)]);
                    if(!isset($result['status'])) return $result;
                    array_push($data, $result['data']);
                }
                return ['status' => TRUE, 'data' => $data,  'msg' => 'Document added successfully.'];
            }
            public function get_image(){
                $post_data  = $this->input->post();
                $files 		= $_FILES;
                if(empty($files)) return ['msg' => '2. Document is empty.'];	
                if($files[$this->sub_menu.'_photo']['error'] == 0){
                    $_FILES[$this->sub_menu.'_photo']['name']		= $files[$this->sub_menu.'_photo']['name'];
                    $_FILES[$this->sub_menu.'_photo']['type']		= $files[$this->sub_menu.'_photo']['type'];
                    $_FILES[$this->sub_menu.'_photo']['tmp_name']	= $files[$this->sub_menu.'_photo']['tmp_name'];
                    $_FILES[$this->sub_menu.'_photo']['error']		= $files[$this->sub_menu.'_photo']['error'];
                    $_FILES[$this->sub_menu.'_photo']['size']		= $files[$this->sub_menu.'_photo']['size'];
        
                    // echo "<pre>"; print_r($_FILES); exit;
                    unset($config);
                    $config 					= array();
                    $config['upload_path'] 		= 'public/uploads/'.$this->sub_menu.'/';
                    $config['allowed_types'] 	= 'gif|jpg|png|jpeg';
                    $file_name 					= $files[$this->sub_menu.'_photo']['name'];
                    $ext 						= strtolower(substr($file_name, strrpos($file_name, '.') + 1));
                    $filename 					= time().'.'.$ext;
                    $config['file_name'] 		= $filename;
    
                    if(!file_exists($config['upload_path'])) mkdir($config['upload_path'], 0777);
                    
                    $this->upload->initialize($config);
                    if(!$this->upload->do_upload($this->sub_menu.'_photo')) return ['msg' => $this->upload->display_errors()];
                    $imageinfo = $this->upload->data();
                    $full_path = $imageinfo['full_path'];
                    
                    // check EXIF and autorotate if needed
                    // $this->db_operations->image_autorotate_resize(array('filepath' => $full_path), TRUE);		
                    return ['status' => TRUE, 'data' => uploads($this->sub_menu.'/'.$filename), 'msg' => ''];
                }
                return ['status' => TRUE, 'data' => $post_data[$this->sub_menu.'_pic'], 'msg' => ''];
            }
            public function get_image1($key){
                $post_data  = $this->input->post();
                $files 		= $_FILES;
                if(empty($files[$key])) return ['msg' => '2. Document is empty.'];	
                if($files[$key]['error'] == 0){
                    $_FILES[$key]['name']		= $files[$key]['name'];
                    $_FILES[$key]['type']		= $files[$key]['type'];
                    $_FILES[$key]['tmp_name']	= $files[$key]['tmp_name'];
                    $_FILES[$key]['error']		= $files[$key]['error'];
                    $_FILES[$key]['size']		= $files[$key]['size'];
        
                    // echo "<pre>"; print_r($_FILES); exit;
                    unset($config);
                    $config 					= array();
                    $config['upload_path'] 		= 'public/uploads/'.$this->sub_menu.'/';
                    $config['allowed_types'] 	= 'gif|jpg|png|jpeg';
                    $file_name 					= $files[$key]['name'];
                    $ext 						= strtolower(substr($file_name, strrpos($file_name, '.') + 1));
                    $filename 					= $key.'_'.time().'.'.$ext;
                    $config['file_name'] 		= $filename;
    
                    if(!file_exists($config['upload_path'])) mkdir($config['upload_path'], 0777);
                    
                    $this->upload->initialize($config);
                    if(!$this->upload->do_upload($key)) return ['msg' => $this->upload->display_errors()];
                    $imageinfo = $this->upload->data();
                    $full_path = $imageinfo['full_path'];
                    
                    // check EXIF and autorotate if needed
                    // $this->db_operations->image_autorotate_resize(array('filepath' => $full_path), TRUE);		
                    return ['status' => TRUE, 'data' => uploads($this->sub_menu.'/'.$filename), 'msg' => ''];
                }
                return ['status' => TRUE, 'data' => $post_data[$key.'_pic'], 'msg' => ''];
            }
            public function get_ids($arr, $id){
                $record = [];
                foreach ($arr as $key => $value) array_push($record, $value[$id]);
                return $record;
            }
        // Client side
    }
?>