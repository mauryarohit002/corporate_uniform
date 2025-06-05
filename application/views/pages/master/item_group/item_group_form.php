<?php 
    $this->load->view('templates/header');
    $action_data= get_action_data('master', 'item_group');
    $action 	= (isset($_GET['action'])) ? $_GET['action'] : "";
    $checked    = !empty($master_data) && $master_data[0]['item_group_status'] == 0 ? '' : 'checked'; 
?>
<script>
    let link    = "master";
    let sub_link= "item_group";
</script>
<section class="d-flex flex-column sticky_top">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item text-uppercase"><a href="<?php echo base_url('master/item_group?action=list'); ?>">item group</a></li>
        <li class="breadcrumb-item active text-uppercase" aria-current="page"><?php echo $action; ?></li>
        <?php if($action != 'read'): ?>
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
                            onclick="add_update_item_group(0)" 
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
                            onclick="add_update_item_group(<?php echo $master_data[0]['item_group_id']; ?>)" 
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
                href="<?php echo base_url('master/item_group?action=list')?>" 
            ><i class="text-danger fa fa-close"></i></a>
        </li>
      </ol>
    </nav>
</section>
<section class="container-fluid my-3">
    <form class="form-horizontal" id="item_group_form">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header text-uppercase">general detail</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap form-group floating-form">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                <input 
                                    type="text" 
                                    class="form-control floating-input" 
                                    id="item_group_name" 
                                    name="item_group_name" 
                                    value="<?php echo empty($master_data) ? '' : $master_data[0]['item_group_name']; ?>" 
                                    onkeyup="validate_textfield(this, true)" 
                                    placeholder=" " 
                                    autocomplete="off" 
                                    tabindex="1" 
                                />   
                                <label class="text-uppercase">name <span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="item_group_name_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                <input 
                                    type="text" 
                                    class="form-control floating-input" 
                                    id="item_group_merchant" 
                                    name="item_group_merchant" 
                                    value="<?php echo empty($master_data) ? '' : $master_data[0]['item_group_merchant']; ?>" 
                                    placeholder=" " 
                                    autocomplete="off" 
                                    tabindex="2"
                                />   
                                <label class="text-uppercase">merchant</label>
                                <small class="form-text text-muted helper-text" id="item_group_merchant_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="item_group_rate" 
                                    name="item_group_rate" 
                                    value="<?php echo empty($master_data) ? '' : $master_data[0]['item_group_rate']; ?>" 
                                    placeholder=" " 
                                    autocomplete="off" 
                                    tabindex="3"
                                />   
                                <label class="text-uppercase">rate</label>
                                <small class="form-text text-muted helper-text" id="item_group_rate_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                <input 
                                    type="checkbox" 
                                    id="item_group_status" 
                                    name="item_group_status" 
                                    data-toggle="toggle" 
                                    data-on="ACTIVE" 
                                    data-off="INACTIVE" 
                                    data-onstyle="primary" 
                                    data-offstyle="primary" 
                                    data-width="100" 
                                    data-size="normal" 
                                    tabindex="4" 
                                    <?php echo $checked ?>
                                />
                            </div>
                            <div class="col-12 col-sm-12 col-md-5 col-lg-5 floating-label">
                                <p class="text-uppercase">item&nbsp;
                                    <span class="text-danger">*</span>
                                </p>
                                <select 
                                    class="form-control floating-select" 
                                    id="item_id" 
                                    placeholder=" " 
                                    tabindex="5"
                                    onchange="validate_dropdown(this)" 
                                ></select>
                                <small class="form-text text-muted helper-text" id="item_id_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-5 col-lg-5 floating-label">
                                <input 
                                    type="text" 
                                    class="form-control floating-input" 
                                    id="code"
                                    placeholder=" " 
                                    autocomplete="off" 
                                    tabindex="6"
                                    onkeyup="validate_textfield(this)" 
                                />   
                                <label class="text-uppercase">short name <span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="code_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                <button 
                                    type="button" 
                                    class="btn btn-md btn-primary btn-block" 
                                    id="add_row_btn"
                                    data-toggle="tooltip" 
                                    title="ADD ITEM" 
                                    data-placement="top" 
                                    tabindex="7" 
                                    onclick="<?php echo empty($master_data) ? 'add_transaction(0);' : "add_transaction(".$master_data[0]['item_group_id'].");"  ?>"   
                                ><i class="text-success fa fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header text-uppercase">added item detail</div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead class="table-dark">
                                <tr class="font-weight-bold text-uppercase" style="font-size: 0.7rem;">
                                    <th class="border-0" width="10%">#</th>
                                    <th class="border-0" width="40%">item</th>
                                    <th class="border-0" width="40%">short name</th>
                                    <th class="border-0" width="10%">remove</th>
                                </tr>
                            </thead>
                            <tbody id="transaction_wrapper"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
<?php $this->load->view('templates/footer'); ?>
<script src="<?php echo assets('dist/js/master/item_group.js?v=3')?>"></script>
<script type="text/javascript">get_transaction()</script>
</body>
</html>