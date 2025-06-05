<?php 
    $apparel_action = get_action_data('master', 'apparel');
    $id             = empty($master_data) ? 0 : $master_data[0]['spm_id'];
    $uuid           = empty($master_data) ? $spm_uuid : $master_data[0]['spm_uuid'];
    $checked        = !empty($master_data) && $master_data[0]['spm_status'] == 0 ? '' : 'checked'; 
    $tabindex       = 1;

?>
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="master_content" role="tabpanel" aria-labelledby="master_tab">
                <div class="d-flex flex-wrap">
                    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                        <div class="card mb-3">
                            <div class="card-header text-uppercase">general detail</div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap pt-2 form-group floating-form">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 d-none">
                                        <input 
                                            type="hidden" 
                                            id="id" 
                                            name="id" 
                                            value="<?php echo $id; ?>"
                                        />
                                        <input 
                                            type="hidden" 
                                            id="spm_uuid" 
                                            name="spm_uuid" 
                                            value="<?php echo $uuid; ?>"
                                        />
                                        <input 
                                            type="hidden" 
                                            id="spt_id" 
                                            name="spt_id" 
                                            value="0"
                                        />
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-8 floating-label">
                                        <p class="text-uppercase">apparel&nbsp;<span class="text-danger">*</span>
                                            <?php if(empty($master_data)): ?>
                                                <?php if(in_array('add', $apparel_action)): ?>
                                                    <span>
                                                        <a
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="ADD APPAREL"
                                                            style="cursor: pointer;"
                                                            onclick='apparel_popup(<?php echo json_encode(["field" => "spm_apparel_id"]) ?>)'
                                                        ><i class="fa fa-plus"></i></a>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </p>
                                        <select 
                                            class="form-control floating-select" 
                                            id="spm_apparel_id" 
                                            name="spm_apparel_id" 
                                            placeholder=" " 
                                            tabindex= "<?php echo $tabindex++; ?>"
                                            onchange="validate_dropdown(this, true)" 
                                            <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'disabled="disabled"' : ''; ?>
                                        >
                                            <?php if(!empty($master_data) && !empty($master_data[0]['spm_apparel_id'])): ?>
                                                <option value="<?php echo $master_data[0]['spm_apparel_id'] ?>" selected>
                                                    <?php echo $master_data[0]['apparel_name']; ?> 
                                                </option>
                                            <?php endif; ?>
                                        </select>
                                        <small class="form-text text-muted helper-text" id="spm_apparel_id_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-8 floating-label">
                                        <p class="text-uppercase">priority copy from</p>
                                        <select 
                                            class="form-control floating-select" 
                                            id="_spm_id" 
                                            placeholder=" " 
                                            tabindex= "<?php echo $tabindex++; ?>"
                                        ></select>
                                        <small class="form-text text-muted helper-text" id="_spm_id_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-4 floating-label">
                                        <input 
                                            type="checkbox" 
                                            id="spm_status" 
                                            name="spm_status" 
                                            data-toggle="toggle" 
                                            data-on="ACTIVE" 
                                            data-off="INACTIVE" 
                                            data-onstyle="primary" 
                                            data-offstyle="primary" 
                                            data-width="100" 
                                            data-size="normal" 
                                            tabindex="<?php echo $tabindex++; ?>"
                                            <?php echo $checked ?>
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-3">
                            <div class="card-header text-uppercase">add style with priority</div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap pt-2 form-group floating-form">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <input 
                                            type="number" 
                                            class="form-control floating-input" 
                                            id="priority" 
                                            name="priority" 
                                            value="1" 
                                            placeholder=" " 
                                            autocomplete="off" 
                                            tabindex= "<?php echo $tabindex++; ?>"
                                            onkeyup="validate_textfield(this)"
                                        />
                                        <label class="text-uppercase">priority&nbsp;<span class="text-danger">*</span></label> 
                                        <small class="form-text text-muted helper-text" id="priority_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <p class="text-uppercase">style&nbsp;<span class="text-danger">*</span></p>
                                        <select 
                                            class="form-control floating-select" 
                                            id="asm_id" 
                                            name="asm_id" 
                                            placeholder=" " 
                                            tabindex= "<?php echo $tabindex++; ?>"
                                            onchange="validate_dropdown(this)"
                                        ></select>
                                        <small class="form-text text-muted helper-text" id="asm_id_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <button 
                                            type="button" 
                                            class="btn btn-md btn-block btn-primary" 
                                            id="add_row_btn"
                                            data-toggle="tooltip" 
                                            title="ADD ITEM" 
                                            data-placement="top" 
                                            tabindex= "<?php echo $tabindex++; ?>"
                                            onclick="add_transaction()"   
                                        ><i class="text-success fa fa-plus"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                        <div class="card mb-3">
                            <div class="card-header text-uppercase">
                                <h5 class="mb-0">
                                    <a 
                                        type="button" 
                                        class="btn btn-sm btn-secondary" 
                                        id="added_itspm_list_tabs"
                                        data-toggle="collapse" 
                                        data-target="#added_itspm_list_tab" 
                                        aria-expanded="true" 
                                        aria-controls="added_itspm_list_tab"
                                    >added item list (<span id="transaction_count">0</span>)</a>
                                </h5>
                            </div>
                            <div id="added_itspm_list_tab" class="collapse show" aria-labelledby="added_itspm_list_tabs" data-parent="#accordion">
                                <div class="card-body p-0" style="max-width:100vw; max-height:75vh; overflow:auto;" id="div_wrapper">
                                    <table class="table table-sm table-reponsive table-hover text-uppercase">
                                        <tbody class="table-dark border-0">
                                            <tr style="font-weight:bold; font-size: 0.8rem;">
                                                <td class="border-bottom border-top-0" >priority</td>
                                                <td class="border-bottom border-top-0" >style</td>
                                                <td class="border-bottom border-top-0" >edit</td>
                                                <td class="border-bottom border-top-0" >delete</td>
                                            </tr>
                                        </tbody>
                                        <tbody id="transaction_wrapper" style="font-weight: bold; font-size: 0.8rem;"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>