<?php 
	$this->load->view('templates/header'); 
	$term   		= 'item_category';
	$action_data 	= get_action_data('master', $term);
	$search_status 	= !isset($_GET['search_status']);
?>
<script>
    let link 	= "master";
    let sub_link= "<?php echo $term; ?>";
</script>
<section class="container-fluid sticky_top">
	<form class="form-horizontal" id="search_form" action="<?php echo base_url("master/$term")?>" method="get">
		<div class="d-flex justify-content-between">
			<nav aria-label="breadcrumb">
			  <ol class="breadcrumb">
			  	<li class="breadcrumb-item"><a href="<?php echo base_url("master/$term"); ?>">MASTER</a></li>
			    <li class="breadcrumb-item active text-uppercase" aria-current="page">
					<?php echo str_replace('_', ' ', $term); ?>(<span id="count_reload"><i id="total_rows"><?php echo $total_rows;?></i></span>)
			    </li>
				<?php if(in_array('add', $action_data)): ?>
			        <li class="breadcrumb-item" aria-current="add-page">
			    	    <a 
							type="button" 
							class="btn btn-sm btn-primary" 
							onclick="item_category_popup(0, 'add')" 
							data-toggle="tooltip" 
							data-placement="bottom" 
							title="ADD NEW"
						><i class="text-success fa fa-plus"></i></a>
			        </li>
                <?php endif; ?>
			    <li class="breadcrumb-item" aria-current="search-page">
			    	<button 
						type="submit" 
						class="btn btn-sm btn-primary mr-2" 
						id="btn_search" 
						data-toggle="tooltip" 
						data-placement="bottom" 
						title="SEARCH"
					><i class="text-warning fa fa-search"></i></button>
			    </li>
			    <li class="breadcrumb-item" aria-current="refresh-page">
					<a 
						type="button" 
						class="btn btn-sm btn-primary" 
						href="<?php echo base_url("master/$term"); ?>"
						data-toggle="tooltip" 
						data-placement="bottom" 
						title="REFRESH"
					><i class="text-info fa fa-undo"></i></a>
			    </li>
			    <?php if($_SESSION['user_type'] == 1): ?>
				    <li class="breadcrumb-item" aria-current="sync-page">
				    	<button 
				    		type="button" 
				    		class="btn btn-sm btn-primary" 
				    		target="_blank"
				    		onclick="sync('<?php echo $term; ?>')" 
				    		data-toggle="tooltip" 
				    		data-placement="bottom" 
				    		title="SYNC"
			    		><i class="text-dark fa fa-retweet"></i></button>
				    </li>
			    <?php endif; ?>
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
				<div class="col-6 col-sm-6 col-md-4 col-lg-4 floating-label">
					<?php if(isset($filters['_name'])): ?><p class="text-uppercase"><?php echo str_replace('_', ' ', $term); ?></p><?php endif; ?>
					<select class="form-control floating-select" id="_name" name="_name">
                    	<?php if(isset($filters['_name']) && !empty($filters['_name'])): ?>
                        	<option value="<?php echo $filters['_name']['value']; ?>" selected>
                            	<?php echo $filters['_name']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
                	</select>
				</div>
				<div class="col-6 col-sm-6 col-md-4 col-lg-4 floating-label">
					<?php if(isset($filters['_status'])): ?><p class="text-uppercase">status</p><?php endif; ?>
					<select class="form-control floating-select" id="_status" name="_status">
                    	<?php if(isset($filters['_status']) && !empty($filters['_status'])): ?>
                        	<option value="<?php echo $filters['_status']['value']; ?>" selected>
                            	<?php echo $filters['_status']['text']; ?> 
                        	</option>
                    	<?php endif; ?>
	                </select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-dark text-uppercase">
					<thead>
						<tr>
			                <th width="3%">#</th>
			                <th width="10%">name</th>
			                <th width="10%">created by / date</th>
			                <th width="10%">updated by / date</th>
			                <th width="5%">status</th>
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
			<table class="table table-sm table-hover text-uppercase" id="table_reload">
				<tbody id="table_tbody">
					<?php 
						if(!empty($data['data'])): 
							foreach ($data['data'] as $key => $value):
                    			$id = encrypt_decrypt("encrypt", $value[$term.'_id'], SECRET_KEY);
					?>

								<tr class="<?php echo $value[$term.'_status'] == 0 ? 'text-danger' : '' ?>">
									<td width="3%"><?php echo $key+1; ?></td>
									<td width="10%"><?php echo $value[$term.'_name']; ?></td>
									<td width="10%"><?php echo $value['created_by']; ?> / <?php echo date('d-m-Y', strtotime($value[$term.'_created_at'])); ?></td>
									<td width="10%"><?php echo $value['updated_by']; ?> / <?php echo date('d-m-Y', strtotime($value[$term.'_updated_at'])); ?></td>
									<td width="5%"><?php echo $value[$term.'_status'] == 1 ? 'active' : 'inactive'; ?></td>
									<?php if(in_array('read', $action_data)): ?>
                                        <td width="3%">
                                            <a 
												type="button" 
												class="btn btn-sm btn-primary" 
												onclick="item_category_popup(<?php echo $value[$term.'_id']; ?>, 'view')"
											><i class="text-info fa fa-eye"></i></a>										
                                        </td>
                                    <?php endif; ?>
                                    <?php if(in_array('edit', $action_data)): ?>
                                        <td width="3%">
                                            <a 
												type="button" 
												class="btn btn-sm btn-primary" 
												onclick="item_category_popup(<?php echo $value[$term.'_id']; ?>, 'edit')"
											><i class="text-success fa fa-edit"></i></a>										
                                        </td>
                                    <?php endif; ?>
                                    <?php if(in_array('delete', $action_data)): ?>
                                        <td width="3%">
                                            <?php if($value['isExist']): ?>
                                                <button type="button" class="btn btn-sm btn-primary"><i class="text-danger fa fa-ban"></i></button>
                                            <?php else: ?>
                                                <a 
                                                    type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    onclick='remove_item_category(<?php echo json_encode($value); ?>);'
                                                ><i class="text-danger fa fa-trash"></i></a>
                                            <?php endif; ?>												                                        
                                        </td>
                                    <?php endif; ?>
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
<script src="<?php echo assets('dist/js/master/item_category.js')?>"></script>
</body>
</html>