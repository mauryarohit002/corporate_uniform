<?php 
    $this->load->view('templates/header');
    $menu 				= 'transaction';
	$sub_menu 			= 'sample_issue_process';
	$search_status 		= !isset($_GET['search_status']);
    $action 	        = (isset($_GET['action'])) ? $_GET['action'] : "";

    $action_data 		= get_action_data($menu, $sub_menu);
    $customer_action    = get_action_data('master', 'customer');
    $courier_action     = get_action_data('master', 'courier');
    $sample_size_action = get_action_data('master', 'sample_size');

    $sim_status         = empty($master_data) ? 2 : $master_data[0]['sim_status'];
?>
<script>
    let link 	= '<?php echo $menu ?>';
    let sub_link= '<?php echo $sub_menu ?>';
</script>
<section class="sticky_top">
    <div class="row d-flex flex-wrap">
        <div class="col-12 col-sm-12 col-md-5 col-lg-5">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb">
                <li class="breadcrumb-item text-uppercase">
                    <a href="<?php echo base_url($menu.'/'.$sub_menu.'?action=list'); ?>"><?php echo str_replace('_', ' ', $sub_menu); ?></a>
                </li>
                <li class="breadcrumb-item active text-uppercase" aria-current="page"><?php echo $action; ?></li>
                <?php if($action != 'view'): ?>
                    <?php if(empty($master_data)): ?>
                        <?php if(in_array('add', $action_data)): ?>
                            <li class="breadcrumb-item" aria-current="save-page">
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-primary master_block_btn" 
                                    data-toggle="tooltip" 
                                    data-placement="bottom" 
                                    title="SAVE" 
                                    tabindex="99" 
                                    onclick="add_update(0)" 
                                    disabled
                                ><i class="text-success fa fa-save"></i></button>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        <?php if(in_array('edit', $action_data)): ?>
                            <li class="breadcrumb-item" aria-current="save-page">
                                <button 
                                    type="button" 
                                    class="btn btn-sm btn-primary master_block_btn" 
                                    data-toggle="tooltip" 
                                    data-placement="bottom" 
                                    title="UPDATE" 
                                    tabindex="99" 
                                    onclick="add_update(<?php echo $master_data[0]['sim_id']; ?>)" 
                                ><i class="text-success fa fa-edit"></i></button>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
                <li class="breadcrumb-item" aria-current="cancel-page">
                    <a 
                        type="button" 
                        class="btn btn-sm btn-primary" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="CANCEL" 
                        tabindex="100"
                        href="<?php echo base_url($menu.'/'.$sub_menu.'?action=list')?>" 
                    ><i class="text-danger fa fa-close"></i></a>
                </li>
              </ol>
            </nav>
        </div>
    </div>
</section>
<section class="container-fluid my-3">
    <form class="form-horizontal" id="sample_issue_form">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="d-flex flex-wrap">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card mb-3">
                            <div class="card-header text-uppercase d-flex justify-content-between">
                                <div>general detail</div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap pt-2 form-group floating-form">
                                    <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                        <input 
                                            type="hidden" 
                                            id="sim_uuid" 
                                            name="sim_uuid" 
                                            value="<?php echo empty($master_data) ? $sim_uuid : $master_data[0]['sim_uuid'] ?>" 
                                        />  
                                        <input 
                                            type="number" 
                                            class="form-control floating-input" 
                                            id="sim_entry_no" 
                                            name="sim_entry_no" 
                                            value="<?php echo empty($master_data) ? $sim_entry_no : $master_data[0]['sim_entry_no'] ?>" 
                                            placeholder=" " 
                                            readonly
                                        />   
                                        <label class="text-uppercase">entry no</label>
                                        <small class="form-text text-muted helper-text" id="sim_entry_no_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                        <input 
                                            type="date" 
                                            class="form-control floating-input" 
                                            id="sim_entry_date" 
                                            name="sim_entry_date" 
                                            value="<?php echo empty($master_data) ? date('Y-m-d') : $master_data[0]['sim_entry_date']; ?>" 
                                            placeholder=" " 
                                            readonly
                                        />   
                                        <label class="text-uppercase">entry date</label>
                                        <small class="form-text text-muted helper-text" id="sim_entry_date_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-3 floating-label">
                                        <p class="text-uppercase">customer&nbsp;<span class="text-danger">*</span></p>
                                        <select 
                                            class="form-control floating-select" 
                                            id="sim_customer_id" 
                                            name="sim_customer_id" 
                                            placeholder=" " 
                                            tabindex="1"
                                            disabled="disabled"
                                        >
                                            <?php if(!empty($master_data) && !empty($master_data[0]['sim_customer_id'])): ?>
                                                <option value="<?php echo $master_data[0]['sim_customer_id'] ?>" selected>
                                                    <?php echo $master_data[0]['customer_name']; ?> 
                                                </option>
                                            <?php endif; ?>
                                        </select>
                                        <input type="hidden" name="_customer_id" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sim_customer_id']; ?>" />
                                        <small class="form-text text-muted helper-text" id="sim_customer_id_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                        <p class="text-uppercase">courier&nbsp;</p>
                                        <select 
                                            class="form-control floating-select" 
                                            id="sim_courier_id" 
                                            name="sim_courier_id" 
                                            placeholder=" " 
                                            tabindex="2"
                                            disabled="disabled"
                                        >
                                            <?php if(!empty($master_data) && !empty($master_data[0]['sim_courier_id'])): ?>
                                                <option value="<?php echo $master_data[0]['sim_courier_id'] ?>" selected>
                                                    <?php echo $master_data[0]['courier_name']; ?> 
                                                </option>
                                            <?php endif; ?>
                                            <input type="hidden" name="_courier_id" value="<?php echo empty($master_data) ? 0 : $master_data[0]['sim_courier_id']; ?>" />
                                        </select>
                                        <small class="form-text text-muted helper-text" id="sim_courier_id_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-2 floating-label">
                                        <input 
                                            type="text" 
                                            class="form-control floating-input" 
                                            id="sim_other_courier" 
                                            name="sim_other_courier" 
                                            value="<?php echo empty($master_data) ? '' : $master_data[0]['sim_other_courier'] ?>" 
                                            placeholder=" " 
                                            readonly
                                        />   
                                        <label class="text-uppercase">other courier</label>
                                        <small class="form-text text-muted helper-text" id="sim_other_courier_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-3 col-lg-2 floating-label">
                                        <input 
                                            type="text" 
                                            class="form-control floating-input" 
                                            id="sim_pod" 
                                            name="sim_pod" 
                                            value="<?php echo empty($master_data) ? '' : $master_data[0]['sim_pod'] ?>" 
                                            placeholder=" "
                                            autocomplete="off"
                                            tabindex="1"
                                        />   
                                        <label class="text-uppercase">pod</label>
                                        <small class="form-text text-muted helper-text" id="sim_pod_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-3 col-lg-2 floating-label">
                                        <p class="text-uppercase">
                                            ref no.&nbsp;
                                            <span>
                                                <a 
                                                    style="cursor: pointer; margin-right: 1rem;" 
                                                    onclick="get_ref_no(<?php echo empty($master_data) ? 0 : $master_data[0]['sim_id']; ?>)" 
                                                    data-toggle="tooltip" 
                                                    data-placement="top" 
                                                    title="GET REF NO"
                                                ><i class="text-info fa fa-undo"></i></a>
                                                <a 
                                                    style="cursor: pointer;" 
                                                    onclick="clear_ref_no(<?php echo empty($master_data) ? 0 : $master_data[0]['sim_id']; ?>)" 
                                                    data-toggle="tooltip" 
                                                    data-placement="top" 
                                                    title="CLEAR REF NO"
                                                ><i class="text-danger fa fa-window-close"></i></a>
                                            </span>
                                        </p>
                                        <input 
                                            type="text" 
                                            class="form-control floating-input" 
                                            id="sim_ref_no" 
                                            name="sim_ref_no" 
                                            value="<?php echo empty($master_data) ? 0 : $master_data[0]['sim_ref_no']; ?>" 
                                            placeholder=" " 
                                            readonly="readonly" 
                                        />   
                                        <small class="form-text text-muted helper-text" id="sim_ref_no_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-3 col-lg-2 floating-label">
                                        <p class="text-uppercase">status</p>
                                        <select
                                            class="form-control floating-select select2"
                                            id="sim_status"
                                            name="sim_status"
                                            placeholder=" "
                                            tabindex="2"
                                            onchange="set_transaction_status()"
                                        >
                                            <option value="3" <?php echo $sim_status == 3 ? 'selected' : ''; ?>>TO BE SENT</option>
                                            <option value="4" <?php echo $sim_status == 4 ? 'selected' : ''; ?>>PARTIAL SENT</option>
                                            <option value="5" <?php echo $sim_status == 5 ? 'selected' : ''; ?>>SAMPLE SENT</option>
                                        </select>
                                        <small class="form-text text-muted helper-text" id="sim_status_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                        <textarea
                                            class="form-control floating-textarea"
                                            id="sim_notes"
                                            name="sim_notes"
                                            placeholder=" "
                                            autocomplete="off"
                                            readonly
                                        ><?php echo empty($master_data) ? '' : $master_data[0]['sim_notes'] ?></textarea>
                                        <label class="text-uppercase">narration</label>
                                        <small class="form-text text-muted helper-text" id="sim_notes_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-3 col-lg-2 floating-label">
                                        <input 
                                            type="number" 
                                            class="form-control floating-input" 
                                            id="sim_total_qty" 
                                            name="sim_total_qty" 
                                            value="<?php echo empty($master_data) ? 0 : $master_data[0]['sim_total_qty'] ?>" 
                                            placeholder=" " 
                                            readonly
                                        />   
                                        <label class="text-uppercase">total qty</label>
                                        <small class="form-text text-muted helper-text" id="sim_total_qty_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                        <textarea
                                            class="form-control floating-textarea"
                                            id="customer_address"
                                            placeholder=" "
                                            autocomplete="off"
                                            readonly
                                        ><?php echo empty($master_data) ? '' : $master_data[0]['customer_address'] ?></textarea>
                                        <label class="text-uppercase">office address</label>
                                        <small class="form-text text-muted helper-text" id="customer_address_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-4 col-lg-4 floating-label">
                                        <input 
                                            type="hidden"
                                            id="sim_executive_id" 
                                            name="sim_executive_id" 
                                            value="<?php echo empty($master_data) ? 0 : $master_data[0]['sim_executive_id'] ?>" 
                                        />
                                        <input 
                                            type="text" 
                                            class="form-control floating-input" 
                                            id="executive_name" 
                                            value="<?php echo empty($master_data) ? '' : $master_data[0]['executive_name'] ?>" 
                                            placeholder=" " 
                                            readonly
                                        />   
                                        <label class="text-uppercase">executive&nbsp;<span class="text-danger">*</span></label>
                                        <small class="form-text text-muted helper-text" id="executive_name_msg"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card mb-3">
                            <div class="card-header text-uppercase">
                                <h5 class="mb-0">
                                    <a 
                                        type="button" 
                                        class="btn btn-sm btn-secondary" 
                                        id="added_item_list_tabs"
                                        data-toggle="collapse" 
                                        data-target="#added_item_list_tab" 
                                        aria-expanded="true" 
                                        aria-controls="added_item_list_tab"
                                    >added sample list ( <span id="transaction_count">0</span> )</a>
                                </h5>
                            </div>
                            <div id="added_item_list_tab" class="collapse show" aria-labelledby="added_item_list_tabs" data-parent="#accordion">
                                <div class="card-body">
                                    <table class="table table-sm text-uppercase font-weight-bold" style="font-size: 0.8rem;">
                                        <tbody>
                                            <tr>
                                                <td class="border-0" width="15%">item</td>
                                                <td class="border-0" width="5%">strip</td>
                                                <td class="border-0" width="5%">rate</td>
                                                <td class="border-0" width="10%">size</td>
                                                <td class="border-0" width="15%">category</td>
                                                <td class="border-0" width="10%">status</td>
                                                <td class="border-0" width="15%">narration</td>
                                                <td class="border-0" width="5%">remove</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div style="max-height: 20vh; overflow-y: auto;">
                                        <table class="table table-sm table-bordered">
                                            <tbody id="transaction_wrapper"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/transaction/sample_issue_process.js?v=2')?>"></script>
<script type="text/javascript">get_transaction()</script>
</body>
</html>