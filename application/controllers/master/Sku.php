<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'core/MY_Controller.php';
class sku extends my_controller{
	public function __construct(){ parent::__construct('master', 'sku'); }
	
	public function add_edit(){
		$post_data  = $this->input->post();
		$files 		= $_FILES;
		$id         = $post_data['id'];

		$result    = isMenuAssigned($this->menu, $this->sub_menu, ($id == 0 ? 'add' : 'edit'));
		if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

		$result = $this->get_image($post_data, $files);
		if(!isset($result['status'])) return $result;
		$post_data['sku_image'] = $result['data'];

		// echo "<pre>"; print_r($post_data);
		// echo "<pre>"; print_r($_FILES); exit;

		$post_data['design_data']		= isset($post_data['design_data']) ? json_decode($post_data['design_data'], true) : [];
		$post_data['dying_data'] 		= isset($post_data['dying_data']) ? json_decode($post_data['dying_data'], true) : [];
		$post_data['karigar_data'] 		= isset($post_data['karigar_data']) ? json_decode($post_data['karigar_data'], true) : [];
		$post_data['embroidery_data'] 	= isset($post_data['embroidery_data']) ? json_decode($post_data['embroidery_data'], true) : [];
		$post_data['other_data'] 		= isset($post_data['other_data']) ? json_decode($post_data['other_data'], true) : [];
		$post_data['image_data'] 		= isset($post_data['image_data']) ? json_decode($post_data['image_data'], true) : [];
		
		$this->db->trans_begin();
			$result = $this->add_edit_master($post_data, $id);
			if(!isset($result['status'])){
				$this->db->trans_rollback();
				return $result;
			}
			$id  = $result['data'];
			$msg = $result['msg'];
			
			$result = $this->add_edit_design_trans($post_data, $id);
			if(!isset($result['status'])){
				$this->db->trans_rollback();
				return $result;
			}

			$result = $this->add_edit_dying_trans($post_data, $id);
			if(!isset($result['status'])){
				$this->db->trans_rollback();
				return $result;
			}

			$result = $this->add_edit_karigar_trans($post_data, $id);
			if(!isset($result['status'])){
				$this->db->trans_rollback();
				return $result;
			}

			$result = $this->add_edit_embroidery_trans($post_data, $id);
			if(!isset($result['status'])){
				$this->db->trans_rollback();
				return $result;
			}

			$result = $this->add_edit_other_trans($post_data, $id);
			if(!isset($result['status'])){
				$this->db->trans_rollback();
				return $result;
			}

			$result = $this->add_edit_image_trans($post_data, $id);
			if(!isset($result['status'])){
				$this->db->trans_rollback();
				return $result;
			}

			if ($this->db->trans_status() === FALSE){
				$this->db->trans_rollback();
				return ['msg' => '1. Transaction Rollback.'];
			}
		$this->db->trans_commit();
		
		$data['id'] 	    = encrypt_decrypt("encrypt", $id, SECRET_KEY);
		$data['name'] 	    = $post_data['sku_name'];

		return ['status' => TRUE, 'data' => $data,  'msg' => $msg];
	}
	public function remove(){
		$post_data  = $this->input->post();
		$id         = $post_data['id'];
		$result     = isMenuAssigned($this->menu, $this->sub_menu, 'delete');
		if(!$result['session'] || !$result['status'] || !$result['active']) return $result;

		$this->db->trans_begin();
		
		$result = $this->delete_design_trans(['sdt_sku_id' => $id, 'sdt_delete_status' => false]);
		if(!isset($result['status'])){
			$this->db->trans_rollback();
			return $result;
		}

		$result = $this->delete_dying_trans(['sdyt_sku_id' => $id, 'sdyt_delete_status' => false]);
		if(!isset($result['status'])){
			$this->db->trans_rollback();
			return $result;
		}

		$result = $this->delete_karigar_trans(['skt_sku_id' => $id, 'skt_delete_status' => false]);
		if(!isset($result['status'])){
			$this->db->trans_rollback();
			return $result;
		}

		$result = $this->delete_embroidery_trans(['set_sku_id' => $id, 'set_delete_status' => false]);
		if(!isset($result['status'])){
			$this->db->trans_rollback();
			return $result;
		}

		$result = $this->delete_other_trans(['sot_sku_id' => $id, 'sot_delete_status' => false]);
		if(!isset($result['status'])){
			$this->db->trans_rollback();
			return $result;
		}

		$result = $this->delete_image_trans(['sit_sku_id' => $id, 'sit_delete_status' => false]);
		if(!isset($result['status'])){
			$this->db->trans_rollback();
			return $result;
		}

        $result = $this->delete_master(['sku_id' => $id, 'sku_delete_status' => false]);
		if(!isset($result['status'])){
			$this->db->trans_rollback();
			return $result;
		}

		if ($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			return ['msg' => '2. Transaction Rollback.'];
		}
		$this->db->trans_commit();

		return ['status' => TRUE, 'msg' => 'Sku deleted successfully'];
	}
	
	public function get_barcode_data(){
        $post_data  = $this->input->post();
        $id         = $post_data['id'];
        // echo "<pre>"; print_r($post_data); exit;

        $data = $this->model->get_barcode_data($id);
        if((empty($data))) return ['msg' => '1. Barcode not found.'];
        // echo "<pre>"; print_r($data); exit;
        return['status' => TRUE, 'data' => $data, 'msg' => 'Barcode scanned.'];
	}
	// sku_master
		public function add_edit_master($post_data, $id){
			// master_data
				$master_data['sku_uuid'] 		= trim($post_data['sku_uuid']);
				$master_data['sku_apparel_id'] 	= isset($post_data['sku_apparel_id']) ? $post_data['sku_apparel_id'] : 0;
				$master_data['sku_name'] 		= trim($post_data['sku_name']);
				$master_data['sku_mrp'] 		= trim($post_data['sku_mrp']);
				$master_data['sku_piece'] 		= trim($post_data['sku_piece']);
				$master_data['sku_color_id'] 	= isset($post_data['sku_color_id']) ? $post_data['sku_color_id'] : 0;
				$master_data['sku_image'] 		= trim($post_data['sku_image']);
				$master_data['sku_notes'] 	    = trim($post_data['sku_notes']);
				$master_data['sku_mtr'] 	    = trim($post_data['sku_mtr']);
				$master_data['sku_rate'] 	    = trim($post_data['sku_rate']);
				$master_data['sku_status'] 		= isset($post_data['sku_status']);
				$master_data['sku_updated_by'] 	= $_SESSION['user_id'];
				$master_data['sku_updated_at'] 	= date('Y-m-d H:i:s');
			// master_data

			$temp = $this->db_operations->get_record($this->sub_menu.'_master', [$this->sub_menu.'_id !=' => $id, $this->sub_menu.'_name'  => $master_data[$this->sub_menu.'_name'], $this->sub_menu.'_delete_status'  => false]);
			if(!empty($temp)) return ['msg' => 'Sku already exist.'];	

			if($id == 0){
				$master_data['sku_created_by'] 	= $_SESSION['user_id'];
				$master_data['sku_created_at'] 	= date('Y-m-d H:i:s');
				$uuidExist 						= $this->db_operations->get_cnt($this->sub_menu.'_master', ['sku_uuid' => $master_data['sku_uuid']]);
				if($uuidExist > 0) return ['msg' => 'Form already submited.'];
				$id = $this->db_operations->data_insert($this->sub_menu.'_master', $master_data);
				if($id < 1) return ['msg' => '1. Sku not added.'];
				return ['status' => TRUE, 'data' => $id, 'msg' => 'Sku added successfully.'];
			}

			$prev_data = $this->db_operations->get_record($this->sub_menu.'_master', ['sku_id' => $id, 'sku_delete_status' => false]);
			if(empty($prev_data)) return ['msg' => '1. Sku not found.'];
			
			if($this->db_operations->data_update($this->sub_menu.'_master', $master_data, 'sku_id', $id) < 1) return ['msg' => '1. Sku not updated.'];

			return ['status' => TRUE, 'data' => $id, 'msg' => 'Sku updated successfully.'];
		}
		public function delete_master($clause){
            $data = $this->db_operations->get_record($this->sub_menu.'_master', $clause);
			if(empty($data)) return ['msg' => '2. Sku not found.'];

			foreach ($data as $key => $value){
				if($this->model->isMasterExist($value['sku_id'])) return ['msg' => '1. Not allowed to delete.'];
				$update_data 						= [];
				$update_data['sku_name'] 			= $value['sku_name'].''.$value['sku_id'].''.$value['sku_uuid']; 
				$update_data['sku_delete_status'] 	= true; 
				$update_data['sku_updated_by'] 		= $_SESSION['user_id']; 
				$update_data['sku_updated_at'] 		= date('Y-m-d H:i:s');
				if($this->db_operations->data_update($this->sub_menu.'_master', $update_data, 'sku_id', $value['sku_id']) < 1) return ['msg' => '1. Sku not deleted.'];
			}
			return ['status' => TRUE];
        }
	// sku_master

	// sku_design_trans
		public function add_design_transaction(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            
			// echo "<pre>"; print_r($post_data); exit;

            if(!isset($post_data['design_rate']) || (isset($post_data['design_rate']) && empty($post_data['design_rate']))){
                return ['msg' => '1. Rate is required.'];
            }else{
                if($post_data['design_rate'] <= 0) return ['msg' => '1. Invalid Rate.'];	
            }

			if(!isset($post_data['design_mtr']) || (isset($post_data['design_mtr']) && empty($post_data['design_mtr']))){
                return ['msg' => '1. Rate is required.'];
            }else{
                if($post_data['design_mtr'] <= 0) return ['msg' => '1. Invalid Mtr.'];	
            }

            
            // echo "<pre>"; print_r($post_data); exit;
            $trans_data 					= [];
			$trans_data['sdt_sku_uuid'] 	= trim($post_data['sku_uuid']);
            $trans_data['sdt_bm_id'] 		= isset($post_data['bm_id']) ? $post_data['bm_id'] : 0;
            $trans_data['sdt_design_id'] 	= isset($post_data['design_id']) ? $post_data['design_id'] : 0;
            $trans_data['sdt_rate'] 		= trim($post_data['design_rate']);
            $trans_data['sdt_mtr'] 			= trim($post_data['design_mtr']);
            $trans_data['sdt_amt'] 			= trim($post_data['design_amt']);
            $trans_data['sdt_delete_status']= true;
            $trans_data['sdt_created_by'] 	= $_SESSION['user_id'];
            $trans_data['sdt_updated_by'] 	= $_SESSION['user_id'];
            $trans_data['sdt_created_at'] 	= date('Y-m-d H:i:s');
            $trans_data['sdt_updated_at'] 	= date('Y-m-d H:i:s');

            if(empty($post_data['sdt_id'])){
                $trans_data['sdt_id'] = $this->db_operations->data_insert('sku_design_trans', $trans_data);
                if($trans_data['sdt_id'] < 1) return ['msg' => '1. Design transaction not added.'];
                $trans_data['isExist'] 		= false;
                $trans_data['sdt_sku_id'] 	= 0;
            }else{
                $trans_data['sdt_sku_id'] 	= $id;
                $trans_data['sdt_id']    	= $post_data['sdt_id'];
            }
            $trans_data['design_name'] = $this->model->get_name('design', $trans_data['sdt_design_id']);
            $trans_data['design_image']= $post_data['design_image'];

            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Design Transaction added successfully.'];
        }
		public function add_edit_design_trans($post_data, $id){
            $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_design_trans', ['sdt_sku_id' => $id, 'sdt_delete_status' => false]);
			$ids  = $this->get_ids($post_data['design_data'], 'sdt_id');

			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['sdt_id'], $ids)){
						$result = $this->delete_design_trans(['sdt_id' => $value['sdt_id'], 'sdt_delete_status' => false]);
						if(!isset($result['status'])) return $result;
					}
				}
			}
			foreach ($post_data['design_data'] as $key => $value){
				// trans_data
                    $trans_data							= [];
                    $trans_data['sdt_sku_id']			= $id;
                    $trans_data['sdt_bm_id'] 			= $value['sdt_bm_id'];
                    $trans_data['sdt_design_id'] 		= $value['sdt_design_id'];
                    $trans_data['sdt_mtr']		        = $value['sdt_mtr'];
                    $trans_data['sdt_rate'] 		    = $value['sdt_rate'];
                    $trans_data['sdt_amt']		        = $value['sdt_amt'];
                    $trans_data['sdt_delete_status']	= false;
                    $trans_data['sdt_updated_by'] 		= $_SESSION['user_id'];
                    $trans_data['sdt_updated_at'] 		= date('Y-m-d H:i:s'); 
                // trans_data
                
                $prev_data = $this->db_operations->get_record($this->sub_menu.'_design_trans', ['sdt_id' => $value['sdt_id']]);
				if(empty($prev_data)) return ['msg' => '1. Design transaction not found.'];

                if(!empty($value['sdt_id'])){
                    if(!$this->model->isDesignTransExist($value['sdt_id'])){
                        if($this->db_operations->data_update($this->sub_menu.'_design_trans', $trans_data, 'sdt_id', $value['sdt_id']) < 1) return ['msg' => '1. Design transaction not updated.'];
                    }
                }
				
			}
			return ['status' => TRUE];
        }
		public function delete_design_trans($clause){
            $data = $this->db_operations->get_record($this->sub_menu.'_design_trans', $clause);
			if(empty($data)) return ['status' => TRUE];

			foreach ($data as $key => $value){
				if($this->model->isDesignTransExist($value['sdt_id'])) return ['msg' => '1. Not allowed to delete transaction.'];
				$update_data 						= [];
				$update_data['sdt_delete_status'] 	= true; 
				$update_data['sdt_updated_by'] 		= $_SESSION['user_id']; 
				$update_data['sdt_updated_at'] 		= date('Y-m-d H:i:s'); 
				if($this->db_operations->data_update($this->sub_menu.'_design_trans', $update_data, 'sdt_id', $value['sdt_id']) < 1) return ['msg' => '2. Design transaction not deleted.'];
			}
			return ['status' => TRUE];
        }
	// sku_design_trans

	// sku_dying_trans
		public function add_dying_transaction(){
			$post_data  = $this->input->post();
			$id         = $post_data['id'];
			
			// echo "<pre>"; print_r($post_data); exit;

			if(!isset($post_data['dying_rate']) || (isset($post_data['dying_rate']) && empty($post_data['dying_rate']))){
				return ['msg' => '1. Rate is required.'];
			}else{
				if($post_data['dying_rate'] <= 0) return ['msg' => '1. Invalid Rate.'];	
			}

			// echo "<pre>"; print_r($post_data); exit;
			$trans_data 					= [];
			$trans_data['sdyt_sku_uuid'] 	= trim($post_data['sku_uuid']);
			$trans_data['sdyt_dying_id'] 	= isset($post_data['dying_id']) ? $post_data['dying_id'] : 0;
			$trans_data['sdyt_rate'] 		= trim($post_data['dying_rate']);
			$trans_data['sdyt_delete_status']= true;
			$trans_data['sdyt_created_by'] 	= $_SESSION['user_id'];
			$trans_data['sdyt_updated_by'] 	= $_SESSION['user_id'];
			$trans_data['sdyt_created_at'] 	= date('Y-m-d H:i:s');
			$trans_data['sdyt_updated_at'] 	= date('Y-m-d H:i:s');

			if(empty($post_data['sdyt_id'])){
				$trans_data['sdyt_id'] = $this->db_operations->data_insert($this->sub_menu.'_dying_trans', $trans_data);
				if($trans_data['sdyt_id'] < 1) return ['msg' => '1. Dying transaction not added.'];
				$trans_data['isExist'] 		= false;
				$trans_data['sdyt_sku_id'] 	= 0;
			}else{
				$trans_data['sdyt_sku_id'] 	= $id;
				$trans_data['sdyt_id']    	= $post_data['sdyt_id'];
			}
			$trans_data['dying_name'] = $this->model->get_name('dying', $trans_data['sdyt_dying_id']);

			return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Dying Transaction added successfully.'];
		}
		public function add_edit_dying_trans($post_data, $id){
			$trans_db_data = $this->db_operations->get_record($this->sub_menu.'_dying_trans', ['sdyt_sku_id' => $id, 'sdyt_delete_status' => false]);
			$ids 	   	   = $this->get_ids($post_data['dying_data'], 'sdyt_id');
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['sdyt_id'], $ids)){
						$result = $this->delete_dying_trans(['sdyt_id' => $value['sdyt_id'], 'sdyt_delete_status' => false]);
						if(!isset($result['status'])) return $result;
					}
				}
			}
			foreach ($post_data['dying_data'] as $key => $value){
				// trans_data
					$trans_data							= [];
					$trans_data['sdyt_sku_id']			= $id;
					$trans_data['sdyt_dying_id'] 		= $value['sdyt_dying_id'];
					$trans_data['sdyt_rate'] 		    = $value['sdyt_rate'];
					$trans_data['sdyt_delete_status']	= false;
					$trans_data['sdyt_updated_by'] 		= $_SESSION['user_id'];
					$trans_data['sdyt_updated_at'] 		= date('Y-m-d H:i:s');
				// trans_data
				
				$prev_data = $this->db_operations->get_record($this->sub_menu.'_dying_trans', ['sdyt_id' => $value['sdyt_id']]);
				if(empty($prev_data)) return ['msg' => '1. Dying transaction not found.'];

				if(!empty($value['sdyt_id'])){
					if(!$this->model->isDyingTransExist($value['sdyt_id'])){
						if($this->db_operations->data_update($this->sub_menu.'_dying_trans', $trans_data, 'sdyt_id', $value['sdyt_id']) < 1) return ['msg' => '1. Dying transaction not updated.'];
					}
				}
				
			}
			return ['status' => TRUE];
		}
		public function delete_dying_trans($clause){
			$data = $this->db_operations->get_record($this->sub_menu.'_dying_trans', $clause);
			if(empty($data)) return ['status' => TRUE];

			foreach ($data as $key => $value){
				if($this->model->isDyingTransExist($value['sdyt_id'])) return ['msg' => '1. Not allowed to delete transaction.'];
				$update_data 						= [];
				$update_data['sdyt_delete_status'] 	= true; 
				$update_data['sdyt_updated_by'] 	= $_SESSION['user_id']; 
				$update_data['sdyt_updated_at'] 	= date('Y-m-d H:i:s'); 
				if($this->db_operations->data_update($this->sub_menu.'_dying_trans', $update_data, 'sdyt_id', $value['sdyt_id']) < 1) return ['msg' => '2. Dying transaction not deleted.'];
			}
			return ['status' => TRUE];
		}
	// sku_dying_trans

	// sku_karigar_trans
		public function add_karigar_transaction(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            
			// echo "<pre>"; print_r($post_data); exit;

            if(!isset($post_data['karigar_rate']) || (isset($post_data['karigar_rate']) && empty($post_data['karigar_rate']))){
                return ['msg' => '1. Rate is required.'];
            }else{
                if($post_data['karigar_rate'] <= 0) return ['msg' => '1. Invalid Rate.'];	
            }

			// echo "<pre>"; print_r($post_data); exit;
            $trans_data 					= [];
			$trans_data['skt_sku_uuid'] 	= trim($post_data['sku_uuid']);
            $trans_data['skt_karigar_id'] 	= isset($post_data['karigar_id']) ? $post_data['karigar_id'] : 0;
            $trans_data['skt_apparel_id'] 	= isset($post_data['apparel_id']) ? $post_data['apparel_id'] : 0;
            $trans_data['skt_rate'] 		= trim($post_data['karigar_rate']);
            $trans_data['skt_delete_status']= true;
            $trans_data['skt_created_by'] 	= $_SESSION['user_id'];
            $trans_data['skt_updated_by'] 	= $_SESSION['user_id'];
            $trans_data['skt_created_at'] 	= date('Y-m-d H:i:s');
            $trans_data['skt_updated_at'] 	= date('Y-m-d H:i:s');

            if(empty($post_data['skt_id'])){
                $trans_data['skt_id'] = $this->db_operations->data_insert($this->sub_menu.'_karigar_trans', $trans_data);
                if($trans_data['skt_id'] < 1) return ['msg' => '1. Karigar charges transaction not added.'];
                $trans_data['isExist'] 		= false;
                $trans_data['skt_sku_id'] 	= 0;
            }else{
                $trans_data['skt_sku_id'] 	= $id;
                $trans_data['skt_id']    	= $post_data['skt_id'];
            }
            $trans_data['karigar_name'] = $this->model->get_name('karigar', $trans_data['skt_karigar_id']);
            $trans_data['apparel_name'] = $this->model->get_name('apparel', $trans_data['skt_apparel_id']);

            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Karigar charges Transaction added successfully.'];
        }
		public function add_edit_karigar_trans($post_data, $id){
            $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_karigar_trans', ['skt_sku_id' => $id, 'skt_delete_status' => false]);
			$ids 	   	   = $this->get_ids($post_data['karigar_data'], 'skt_id');
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['skt_id'], $ids)){
						$result = $this->delete_karigar_trans(['skt_id' => $value['skt_id'], 'skt_delete_status' => false]);
						if(!isset($result['status'])) return $result;
					}
				}
			}
			foreach ($post_data['karigar_data'] as $key => $value){
				// trans_data
                    $trans_data							= [];
                    $trans_data['skt_sku_id']			= $id;
                    $trans_data['skt_karigar_id'] 		= $value['skt_karigar_id'];
                    $trans_data['skt_apparel_id'] 		= $value['skt_apparel_id'];
                    $trans_data['skt_rate'] 		    = $value['skt_rate'];
                    $trans_data['skt_delete_status']	= false;
                    $trans_data['skt_updated_by'] 		= $_SESSION['user_id'];
                    $trans_data['skt_updated_at'] 		= date('Y-m-d H:i:s');
                // trans_data
                
                $prev_data = $this->db_operations->get_record($this->sub_menu.'_karigar_trans', ['skt_id' => $value['skt_id']]);
				if(empty($prev_data)) return ['msg' => '1. Karigar charges transaction not found.'];

                if(!empty($value['skt_id'])){
                    if(!$this->model->isKarigarTransExist($value['skt_id'])){
                        if($this->db_operations->data_update($this->sub_menu.'_karigar_trans', $trans_data, 'skt_id', $value['skt_id']) < 1) return ['msg' => '1. Karigar charges transaction not updated.'];
                    }
                }
				
			}
			return ['status' => TRUE];
        }
		public function delete_karigar_trans($clause){
            $data = $this->db_operations->get_record($this->sub_menu.'_karigar_trans', $clause);
			if(empty($data)) return ['status' => TRUE];

			foreach ($data as $key => $value){
				if($this->model->isKarigarTransExist($value['skt_id'])) return ['msg' => '1. Not allowed to delete transaction.'];
				$update_data 						= [];
				$update_data['skt_delete_status'] 	= true; 
				$update_data['skt_updated_by'] 	= $_SESSION['user_id']; 
				$update_data['skt_updated_at'] 	= date('Y-m-d H:i:s'); 
				if($this->db_operations->data_update($this->sub_menu.'_karigar_trans', $update_data, 'skt_id', $value['skt_id']) < 1) return ['msg' => '2. Karigar charges transaction not deleted.'];
			}
			return ['status' => TRUE];
        }
	// sku_karigar_trans

	// sku_embroidery_trans
		public function add_embroidery_transaction(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            
			// echo "<pre>"; print_r($post_data); exit;

            if(!isset($post_data['embroidery_rate']) || (isset($post_data['embroidery_rate']) && empty($post_data['embroidery_rate']))){
                return ['msg' => '1. Rate is required.'];
            }else{
                if($post_data['embroidery_rate'] <= 0) return ['msg' => '1. Invalid Rate.'];	
            }

			// echo "<pre>"; print_r($post_data); exit;
            $trans_data 					= [];
			$trans_data['set_sku_uuid'] 	= trim($post_data['sku_uuid']);
            $trans_data['set_embroidery_id']= isset($post_data['embroidery_id']) ? $post_data['embroidery_id'] : 0;
            $trans_data['set_rate'] 		= trim($post_data['embroidery_rate']);
            $trans_data['set_delete_status']= true;
            $trans_data['set_created_by'] 	= $_SESSION['user_id'];
            $trans_data['set_updated_by'] 	= $_SESSION['user_id'];
            $trans_data['set_created_at'] 	= date('Y-m-d H:i:s');
            $trans_data['set_updated_at'] 	= date('Y-m-d H:i:s');

            if(empty($post_data['set_id'])){
                $trans_data['set_id'] = $this->db_operations->data_insert($this->sub_menu.'_embroidery_trans', $trans_data);
                if($trans_data['set_id'] < 1) return ['msg' => '1. Embroidery charges transaction not added.'];
                $trans_data['isExist'] 		= false;
                $trans_data['set_sku_id'] 	= 0;
            }else{
                $trans_data['set_sku_id'] 	= $id;
                $trans_data['set_id']    	= $post_data['set_id'];
            }
            $trans_data['embroidery_name'] = $this->model->get_name('embroidery', $trans_data['set_embroidery_id']);

            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Embroidery charges Transaction added successfully.'];
        }
		public function add_edit_embroidery_trans($post_data, $id){
            $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_embroidery_trans', ['set_sku_id' => $id, 'set_delete_status' => false]);
			$ids 	   	   = $this->get_ids($post_data['embroidery_data'], 'set_id');
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['set_id'], $ids)){
						$result = $this->delete_embroidery_trans(['set_id' => $value['set_id'], 'set_delete_status' => false]);
						if(!isset($result['status'])) return $result;
					}
				}
			}
			foreach ($post_data['embroidery_data'] as $key => $value){
				// trans_data
                    $trans_data							= [];
                    $trans_data['set_sku_id']			= $id;
                    $trans_data['set_embroidery_id'] 		= $value['set_embroidery_id'];
                    $trans_data['set_rate'] 		    = $value['set_rate'];
                    $trans_data['set_delete_status']	= false;
                    $trans_data['set_updated_by'] 		= $_SESSION['user_id'];
                    $trans_data['set_updated_at'] 		= date('Y-m-d H:i:s');
                // trans_data
                
                $prev_data = $this->db_operations->get_record($this->sub_menu.'_embroidery_trans', ['set_id' => $value['set_id']]);
				if(empty($prev_data)) return ['msg' => '1. Embroidery charges transaction not found.'];

                if(!empty($value['set_id'])){
                    if(!$this->model->isEmbroideryTransExist($value['set_id'])){
                        if($this->db_operations->data_update($this->sub_menu.'_embroidery_trans', $trans_data, 'set_id', $value['set_id']) < 1) return ['msg' => '1. Embroidery charges transaction not updated.'];
                    }
                }
				
			}
			return ['status' => TRUE];
        }
		public function delete_embroidery_trans($clause){
            $data = $this->db_operations->get_record($this->sub_menu.'_embroidery_trans', $clause);
			if(empty($data)) return ['status' => TRUE];

			foreach ($data as $key => $value){
				if($this->model->isEmbroideryTransExist($value['set_id'])) return ['msg' => '1. Not allowed to delete transaction.'];
				$update_data 						= [];
				$update_data['set_delete_status'] 	= true; 
				$update_data['set_updated_by'] 	= $_SESSION['user_id']; 
				$update_data['set_updated_at'] 	= date('Y-m-d H:i:s'); 
				if($this->db_operations->data_update($this->sub_menu.'_embroidery_trans', $update_data, 'set_id', $value['set_id']) < 1) return ['msg' => '2. Embroidery charges transaction not deleted.'];
			}
			return ['status' => TRUE];
        }
	// sku_embroidery_trans

	// sku_other_trans
		public function add_other_transaction(){
            $post_data  = $this->input->post();
            $id         = $post_data['id'];
            
			// echo "<pre>"; print_r($post_data); exit;

            if(!isset($post_data['other_rate']) || (isset($post_data['other_rate']) && empty($post_data['other_rate']))){
                return ['msg' => '1. Rate is required.'];
            }else{
                if($post_data['other_rate'] <= 0) return ['msg' => '1. Invalid Rate.'];	
            }

			// echo "<pre>"; print_r($post_data); exit;
            $trans_data 					= [];
			$trans_data['sot_sku_uuid'] 	= trim($post_data['sku_uuid']);
            $trans_data['sot_other_id']		= isset($post_data['other_id']) ? $post_data['other_id'] : 0;
            $trans_data['sot_rate'] 		= trim($post_data['other_rate']);
            $trans_data['sot_delete_status']= true;
            $trans_data['sot_created_by'] 	= $_SESSION['user_id'];
            $trans_data['sot_updated_by'] 	= $_SESSION['user_id'];
            $trans_data['sot_created_at'] 	= date('Y-m-d H:i:s');
            $trans_data['sot_updated_at'] 	= date('Y-m-d H:i:s');

            if(empty($post_data['sot_id'])){
                $trans_data['sot_id'] = $this->db_operations->data_insert($this->sub_menu.'_other_trans', $trans_data);
                if($trans_data['sot_id'] < 1) return ['msg' => '1. Other charges transaction not added.'];
                $trans_data['isExist'] 		= false;
                $trans_data['sot_sku_id'] 	= 0;
            }else{
                $trans_data['sot_sku_id'] 	= $id;
                $trans_data['sot_id']    	= $post_data['sot_id'];
            }
            $trans_data['other_name'] = $this->model->get_name('other', $trans_data['sot_other_id']);

            return ['status' => TRUE, 'data' => $trans_data,  'msg' => 'Other charges Transaction added successfully.'];
        }
		public function add_edit_other_trans($post_data, $id){
            $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_other_trans', ['sot_sku_id' => $id, 'sot_delete_status' => false]);
			$ids 	   	   = $this->get_ids($post_data['other_data'], 'sot_id');
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['sot_id'], $ids)){
						$result = $this->delete_other_trans(['sot_id' => $value['sot_id'], 'sot_delete_status' => false]);
						if(!isset($result['status'])) return $result;
					}
				}
			}
			foreach ($post_data['other_data'] as $key => $value){
				// trans_data
                    $trans_data							= [];
                    $trans_data['sot_sku_id']			= $id;
                    $trans_data['sot_other_id'] 		= $value['sot_other_id'];
                    $trans_data['sot_rate'] 		    = $value['sot_rate'];
                    $trans_data['sot_delete_status']	= false;
                    $trans_data['sot_updated_by'] 		= $_SESSION['user_id'];
                    $trans_data['sot_updated_at'] 		= date('Y-m-d H:i:s');
                // trans_data
                
                $prev_data = $this->db_operations->get_record($this->sub_menu.'_other_trans', ['sot_id' => $value['sot_id']]);
				if(empty($prev_data)) return ['msg' => '1. Other charges transaction not found.'];

                if(!empty($value['sot_id'])){
                    if(!$this->model->isOtherTransExist($value['sot_id'])){
                        if($this->db_operations->data_update($this->sub_menu.'_other_trans', $trans_data, 'sot_id', $value['sot_id']) < 1) return ['msg' => '1. Other charges transaction not updated.'];
                    }
                }
				
			}
			return ['status' => TRUE];
        }
		public function delete_other_trans($clause){
            $data = $this->db_operations->get_record($this->sub_menu.'_other_trans', $clause);
			if(empty($data)) return ['status' => TRUE];

			foreach ($data as $key => $value){
				if($this->model->isOtherTransExist($value['sot_id'])) return ['msg' => '1. Not allowed to delete transaction.'];
				$update_data 						= [];
				$update_data['sot_delete_status'] 	= true; 
				$update_data['sot_updated_by'] 	= $_SESSION['user_id']; 
				$update_data['sot_updated_at'] 	= date('Y-m-d H:i:s'); 
				if($this->db_operations->data_update($this->sub_menu.'_other_trans', $update_data, 'sot_id', $value['sot_id']) < 1) return ['msg' => '2. Other charges transaction not deleted.'];
			}
			return ['status' => TRUE];
        }
	// sku_other_trans

	// sku_image_trans
		public function upload_sku_image(){
			$result = isLoggedIn();
			if(!$result['session'] || !$result['status'] || !$result['active']){
				echo json_encode($result);
				return;
			}
			$files = $_FILES;
			// echo "<pre>"; print_r($files);exit;
			if(empty($files)){
				echo json_encode(['session' => TRUE, 'status' => FALSE, 'data' => [],  'msg' => 'Document is empty.']);
				return;			
			}
			$cnt = isset($files['sku_images']['name']) ? count($files['sku_images']['name']) : 0;
			$data=[];
			for($i = 0; $i < $cnt; $i++){
				if($files['sku_images']['error'][$i] != 0){
					echo json_encode(['session' => TRUE, 'status' => FALSE, 'data' => [],  'msg' => 'Error in Image.']);
					return;					
				}

				$_FILES['sku_images']['name']		= $files['sku_images']['name'][$i];
				$_FILES['sku_images']['type']		= $files['sku_images']['type'][$i];
				$_FILES['sku_images']['tmp_name']	= $files['sku_images']['tmp_name'][$i];
				$_FILES['sku_images']['error']		= $files['sku_images']['error'][$i];
				$_FILES['sku_images']['size']		= $files['sku_images']['size'][$i];

				unset($config);
				$config 					= array();
				$config['upload_path'] 		= 'public/uploads/sku/';
				$config['allowed_types'] 	= 'gif|jpg|png|jpeg';
				$file_name 					= $files['sku_images']['name'][$i];

				$ext 						= strtolower(substr($file_name, strrpos($file_name, '.') + 1));
				$filename 					= $i.''.time().'.'.$ext;
				$config['file_name'] 		= $filename;
				if(!file_exists($config['upload_path'])){
					mkdir($config['upload_path'], 0777);
				}
				$this->upload->initialize($config);
				if(!$this->upload->do_upload('sku_images')){
					echo json_encode(['session' => TRUE, 'status' => FALSE, 'data' => [],  'msg' => 'Document not uploaded.']);
					return;					
				}
				$imageinfo = $this->upload->data();
				$full_path = $imageinfo['full_path'];
					
				// check EXIF and autorotate if needed
				// $this->db_operations->image_autorotate_resize(array('filepath' => $full_path), TRUE);		
				$sku_image_trans 					= [];
				$sku_image_trans['sit_path'] 		= uploads('sku/'.$filename);
				$sku_image_trans['sit_created_by'] 	= $_SESSION['user_id'];
				$sku_image_trans['sit_updated_by'] 	= $_SESSION['user_id'];
				$sku_image_trans['sit_created_at']	= date('Y-m-d H:i:s');
				$sku_image_trans['sit_updated_at']	= date('Y-m-d H:i:s');
				$id = $this->db_operations->data_insert('sku_image_trans', $sku_image_trans);
				if($id < 1){
					echo json_encode(['session' => TRUE, 'status' => FALSE, 'data' => [],  'msg' => 'Document not inserted in database.']);
					return;						
				}
				array_push($data, ['sit_id' => $id, 'sit_sku_id' => 0, 'sit_path' => uploads('sku/'.$filename)]);
			}
			echo json_encode(['session' => TRUE, 'status' => TRUE, 'data' => $data,  'msg' => 'Image added successfully.']);
		}
		public function add_edit_image_trans($post_data, $id){
            $trans_db_data = $this->db_operations->get_record($this->sub_menu.'_image_trans', ['sit_sku_id' => $id, 'sit_delete_status' => false]);
			$ids 	= $this->get_ids($post_data['image_data'], 'sit_id');
			if(!empty($trans_db_data)){
				foreach ($trans_db_data as $key => $value){
					if(!in_array($value['sit_id'], $ids)){
						$result = $this->delete_image_trans(['sit_id' => $value['sit_id'], 'sit_delete_status' => false]);
						if(!isset($result['status'])) return $result;
					}
				}
			}
			foreach ($post_data['image_data'] as $key => $value){
				// trans_data
                    $trans_data							= [];
                    $trans_data['sit_sku_id']			= $id;
                    $trans_data['sit_delete_status']	= false;
                    $trans_data['sit_updated_by'] 		= $_SESSION['user_id'];
                    $trans_data['sit_updated_at'] 		= date('Y-m-d H:i:s');
                // trans_data
                
                $prev_data = $this->db_operations->get_record($this->sub_menu.'_image_trans', ['sit_id' => $value['sit_id']]);
				if(empty($prev_data)) return ['msg' => '1. Image transaction not found.'];

                if(!empty($value['sit_id'])){
					if($this->db_operations->data_update($this->sub_menu.'_image_trans', $trans_data, 'sit_id', $value['sit_id']) < 1) return ['msg' => '1. Image transaction not updated.'];
                }
				
			}
			return ['status' => TRUE];
        }
		public function delete_image_trans($clause){
            $data = $this->db_operations->get_record($this->sub_menu.'_image_trans', $clause);
			if(empty($data)) return ['status' => TRUE];

			foreach ($data as $key => $value){
				$update_data 						= [];
				$update_data['sit_delete_status'] 	= true; 
				$update_data['sit_updated_by'] 		= $_SESSION['user_id']; 
				$update_data['sit_updated_at'] 		= date('Y-m-d H:i:s'); 
				if($this->db_operations->data_update($this->sub_menu.'_image_trans', $update_data, 'sit_id', $value['sit_id']) < 1) return ['msg' => '2. Image transaction not deleted.'];
			}
			return ['status' => TRUE];
        }
	// sku_image_trans

        public function get_ids($data,$id){
			$record = [];
			foreach ($data as $key => $value) {
				array_push($record, $value[$id]);
			}
			return $record;
		}
}
?>
