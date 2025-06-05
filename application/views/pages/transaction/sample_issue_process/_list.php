<?php 
	$this->load->view('templates/header'); 
	$menu 				= 'transaction';
	$sub_menu 			= 'sample_issue_process';
	$search_status 		= !isset($_GET['search_status']);
	$action 			= (isset($_GET['action'])) ? $_GET['action'] : "";
    $action_data 		= get_action_data($menu, $sub_menu);

	$_entry_date_from 	= (isset($_GET['_entry_date_from'])) ? $_GET['_entry_date_from'] : "";
	$_entry_date_to 	= (isset($_GET['_entry_date_to'])) ? $_GET['_entry_date_to'] : "";
	$sim_status 	    = (isset($_GET['sim_status'])) ? $_GET['sim_status'] : 0;
?>
<script>
    let link 	= '<?php echo $menu ?>';
    let sub_link= '<?php echo $sub_menu ?>';
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url($menu.'/'.$sub_menu)?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			  	<li class="breadcrumb-item text-uppercase">
					<a href="<?php echo base_url($menu.'/'.$sub_menu.'?action=list'); ?>"><?php echo $menu; ?></a>
				</li>
			    <li class="breadcrumb-item active text-uppercase" aria-current="page">
			    	<?php echo str_replace('_', ' ', $sub_menu); ?> (<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button 
						type="submit" 
						class="btn btn-sm btn-primary mr-2" 
						id="btn_search" 
						data-toggle="tooltip" 
						data-placement="bottom" 
						title="SEARCH"
					><i class="text-warning fa fa-search"></i></button>
			    	<input type="hidden" name="action" value='<?php echo $action; ?>'>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
			    	<a 
			    		type="button" 
			    		class="btn btn-sm btn-primary" 
			    		data-toggle="tooltip" 
			    		data-placement="bottom" 
			    		title="REFRESH"
			    		href="<?php echo base_url($menu.'/'.$sub_menu.'/?action=list&sim_status=3'); ?>"
		    		><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <li class="breadcrumb-item" aria-current="search-box">
			    	<input 
			    		type="checkbox" 
			    		id="search_status" 
			    		name="search_status" 
			    		data-toggle="toggle" 
			    		data-on="FILTER <i class='fa fa-eye'></i>" 
			    		data-off="FILTER <i class='fa fa-eye-slash'></i>" 
			    		data-onstyle="primary" 
			    		data-offstyle="primary" 
			    		data-width="100" 
			    		data-size="mini" 
			    		data-style="show-hide" 
			    		onchange="set_search_box()" <?php echo empty($search_status) ? 'checked' : ''; ?>
		    		/>
			    </li>
			  </ol>
			</nav>
			<div class="d-none d-sm-block height_60_px">
				<?= $this->pagination->create_links(); ?>
			</div>
		</div>
		<div class="row collapse mt-2 <?php echo empty($search_status) ? '' : 'show'  ?>" id="search_box">
			<div class="d-flex flex-wrap justify-content-center floating-form">
				<div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<?php if(isset($filters['_entry_no'])): ?><p class="text-uppercase">entry no</p><?php endif; ?>
					<select class="form-control floating-select" id="_entry_no" name="_entry_no">
                    	<?php if(isset($filters['_entry_no']) && !empty($filters['_entry_no'])): ?>
                        	<option value="<?php echo $filters['_entry_no']['value']; ?>" selected>
                            	<?php echo $filters['_entry_no']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="d-flex col-6 col-sm-6 col-md-4 col-lg-3">
					<div class="floating-label">
						<input 
							type="date" 
							class="form-control floating-input" 
							id="_entry_date_from" 
							name="_entry_date_from" 
							value="<?php echo $_entry_date_from ?>" 
							placeholder=" " 
							autocomplete="off"
						/>   
	                    <label class="text-uppercase">entry date <small class="font-weight-bold">from</small></label>
					</div>
					<div class="floating-label">
						<input 
							type="date" 
							class="form-control floating-input" 
							id="_entry_date_to" 
							name="_entry_date_to" 
							value="<?php echo $_entry_date_to ?>" 
							placeholder=" " 
							autocomplete="off"
						/>   
	                    <label class="text-uppercase">entry date <small class="font-weight-bold">to</small></label>
					</div>
				</div>
				<div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<?php if(isset($filters['_customer_name'])): ?><p class="text-uppercase">customer</p><?php endif; ?>
					<select class="form-control floating-select" id="_customer_name" name="_customer_name">
                    	<?php if(isset($filters['_customer_name']) && !empty($filters['_customer_name'])): ?>
                        	<option value="<?php echo $filters['_customer_name']['value']; ?>" selected>
                            	<?php echo $filters['_customer_name']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
                <div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<?php if(isset($filters['_courier_name'])): ?><p class="text-uppercase">courier</p><?php endif; ?>
					<select class="form-control floating-select" id="_courier_name" name="_courier_name">
                    	<?php if(isset($filters['_courier_name']) && !empty($filters['_courier_name'])): ?>
                        	<option value="<?php echo $filters['_courier_name']['value']; ?>" selected>
                            	<?php echo $filters['_courier_name']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
                <div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<?php if(isset($filters['_pod'])): ?><p class="text-uppercase">pod</p><?php endif; ?>
					<select class="form-control floating-select" id="_pod" name="_pod">
                    	<?php if(isset($filters['_pod']) && !empty($filters['_pod'])): ?>
                        	<option value="<?php echo $filters['_pod']['value']; ?>" selected>
                            	<?php echo $filters['_pod']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
                <div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<?php if(isset($filters['_ref_no'])): ?><p class="text-uppercase">ref no</p><?php endif; ?>
					<select class="form-control floating-select" id="_ref_no" name="_ref_no">
                    	<?php if(isset($filters['_ref_no']) && !empty($filters['_ref_no'])): ?>
                        	<option value="<?php echo $filters['_ref_no']['value']; ?>" selected>
                            	<?php echo $filters['_ref_no']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
                <div class="col-6 col-sm-6 col-md-3 col-lg-2 floating-label">
					<p class="text-uppercase">status</p>
					<select class="form-control floating-select select2" id="sim_status" name="sim_status" onchange="trigger_search()">
                        <option value="0" <?php echo $sim_status == 0 ? 'selected' : ''; ?>>ALL</option>
                        <option value="3" <?php echo $sim_status == 3 ? 'selected' : ''; ?>>TO BE SENT</option>
                        <option value="4" <?php echo $sim_status == 4 ? 'selected' : ''; ?>>PARTIAL SENT</option>
                        <option value="5" <?php echo $sim_status == 5 ? 'selected' : ''; ?>>COMPLETED</option>
                	</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark text-uppercase">
					<thead>
						<tr>
			                <th width="5%">entry no</th>
			                <th width="5%">entry date</th>
			                <th width="10%">customer</th>
			                <th width="10%">courier</th>
			                <th width="10%">othercourier</th>
			                <th width="5%">pod</th>
			                <th width="5%">ref no.</th>
			                <th width="5%">status</th>
			                <th width="8%">(total qty/pending qty)</th>
			                <th width="3%">print</th>
			                <?php if(in_array('read', $action_data)): ?>
			                	<th width="3%">view</th> 
							<?php endif; ?>
							<?php if(in_array('edit', $action_data)): ?>
			                	<th width="3%">edit</th> 
							<?php endif; ?>
							<?php if(in_array('delete', $action_data)): ?>
			                	<th width="3%">delete</th>
							<?php endif; ?>
			            </tr>
					</thead>
				</table>
			</div>
		</div>
	</form>
</section>
<section class="container-fluid">
	<div class="row">
		<div class="col-12">
			<table class="table table-sm table-hover text-uppercase font-weight-bold" id="table_reload" style="font-size: 0.8rem;">
				<tbody id="table_tbody">
					<?php 
						if(!empty($data['data'])): 
							foreach ($data['data'] as $key => $value):
                    			$id = encrypt_decrypt("encrypt", $value['sim_id'], SECRET_KEY);
					?>

								<tr>
									<td width="5%"><?php echo $value['sim_entry_no']; ?></td>
									<td width="5%"><?php echo $value['sim_entry_date'] == '' ? '' : date('d-m-Y', strtotime($value['sim_entry_date'])); ?></td>
									<td width="10%"><?php echo $value['customer_name']; ?></td>
									<td width="10%"><?php echo $value['courier_name']; ?></td>
									<td width="10%"><?php echo strtoupper($value['sim_other_courier']); ?></td>
									<td width="5%"><?php echo $value['sim_pod']; ?></td>
									<td width="5%"><?php echo $value['sim_ref_no']; ?></td>
									<td width="5%"><?php echo $value['_status']; ?></td>
									<td width="8%">(<?php echo $value['sim_total_qty']; ?> / <?php echo $value['pending_qty']; ?>)</td>
									<td width="3%">
										<a 
											type="button" 
											class="btn btn-sm btn-primary" 
											target="_blank"
											href="<?php echo base_url('transaction/sample_issue_process?action=print&id='.$id); ?>"
										><i class="text-info fa fa-print"></i></a>										
									</td>
									<?php if(in_array('read', $action_data)): ?>
										<td width="3%">
											<a 
												type="button" 
												class="btn btn-sm btn-primary" 
												href="<?php echo base_url($menu.'/'.$sub_menu.'?action=list&id='.$id); ?>"
											><i class="text-info fa fa-eye"></i></a>										
										</td>
									<?php endif;?>
									<?php if(in_array('edit', $action_data)): ?>
										<td width="3%">
											<a 
												type="button" 
												class="btn btn-sm btn-primary" 
												href="<?php echo base_url($menu.'/'.$sub_menu.'?action=edit&id='.$id); ?>"
											><i class="text-success fa fa-edit"></i></a>										
										</td>
									<?php endif;?>
									<?php if(in_array('delete', $action_data)): ?>
										<td width="3%">
											<?php if($value['isExist']): ?>
												<button type="button" class="btn btn-sm btn-primary"><i class="text-danger fa fa-ban"></i></button>
											<?php else: ?>
												<a 
													type="button" 
													class="btn btn-sm btn-primary" 
													onclick='remove_record(<?php echo json_encode($value); ?>);'
												><i class="text-danger fa fa-trash"></i></a>
											<?php endif; ?>                         
										</td>
									<?php endif;?>
								</tr>
					<?php 
							endforeach;
						else: 
					?>
						<tr>
							<td class="text-danger font-weight-bold text-center" colspan="10">NO RECORD FOUND!!!</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
<?= $this->pagination->create_links(); ?>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/transaction/sample_issue_process.js?v=2')?>"></script>
</body>
</html>