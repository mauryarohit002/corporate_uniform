<?php 
    $id             = empty($master_data) ? 0 : $master_data[0]['asm_id'];
    $uuid           = empty($master_data) ? $asm_uuid : $master_data[0]['asm_uuid'];
    $checked        = !empty($master_data) && $master_data[0]['asm_status'] == 0 ? '' : 'checked'; 
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
                                            id="asm_uuid" 
                                            name="asm_uuid" 
                                            value="<?php echo $uuid; ?>"
                                        />
                                        <input 
                                            type="hidden" 
                                            id="ast_id" 
                                            name="ast_id" 
                                            value="0"
                                        />
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-8 floating-label">
                                        <input 
                                            type="text" 
                                            class="form-control floating-input" 
                                            id="asm_name" 
                                            name="asm_name" 
                                            value="<?php echo empty($master_data) ? '' : $master_data[0]['asm_name']; ?>" 
                                            placeholder=" " 
                                            autocomplete="off"
                                            tabindex= "<?php echo $tabindex++; ?>"
                                            onkeyup="validate_textfield(this, true)"
                                        />
                                        <label class="text-uppercase">style&nbsp;name&nbsp;<span class="text-danger">*</span></label>
                                        <small class="form-text text-muted helper-text" id="asm_name_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-4 floating-label">
                                        <input 
                                            type="checkbox" 
                                            id="asm_status" 
                                            name="asm_status" 
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
                            <div class="card-header text-uppercase">add type</div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap pt-2 form-group floating-form">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <div class="d-flex flex-column align-items-center">
                                            <span class="d-flex justify-content-center" id="preview" style="width: 9rem; height:9rem;">
                                                <img 
                                                    class="pan form_loading" 
                                                    onclick="zoom(this)" 
                                                    title="click to zoom in and zoom out" 
                                                    src="<?php echo assets(LAZYLOADING) ?>" 
                                                    data-src="<?php echo assets(NOIMAGE); ?>" 
                                                    data-big="<?php echo assets(NOIMAGE); ?>" 
                                                    style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                                />
                                            </span>
                                            <label class="text-uppercase"> <small class="text-danger font-weight-bold">(.jpg, .jpeg, .png) only</small></label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-8 mb-5">
                                        <input 
                                            type="file"  
                                            id="asm_image" 
                                            name="asm_image" 
                                            class="form-control floating-input" 
                                            onchange="preview_image(this)"
                                            tabindex="<?php echo $tabindex++; ?>"
                                            accept="image/*"
                                        />
                                        <input 
                                            type="hidden"  
                                            id="asm_pic" 
                                            name="asm_pic" 
                                            value="<?php echo assets(NOIMAGE); ?>"
                                        />
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-5">
                                        <button 
                                            type="button" 
                                            class="btn btn-md btn-block btn-primary" 
                                            onclick="remove_asm_image()"
                                            tabindex="<?php echo $tabindex++; ?>"
                                        ><i class="text-danger fa fa-trash"></i></button>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-8 floating-label">
                                        <input 
                                            type="text" 
                                            class="form-control floating-input" 
                                            id="type" 
                                            name="type" 
                                            value="" 
                                            placeholder=" " 
                                            autocomplete="off" 
                                            tabindex= "<?php echo $tabindex++; ?>"
                                            onkeyup="validate_textfield(this)"
                                        />
                                        <label class="text-uppercase">type&nbsp;<span class="text-danger">*</span></label> 
                                        <small class="form-text text-muted helper-text" id="type_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-4">
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
                                        id="added_itasm_list_tabs"
                                        data-toggle="collapse" 
                                        data-target="#added_itasm_list_tab" 
                                        aria-expanded="true" 
                                        aria-controls="added_itasm_list_tab"
                                    >added item list (<span id="transaction_count">0</span>)</a>
                                </h5>
                            </div>
                            <div id="added_itasm_list_tab" class="collapse show" aria-labelledby="added_itasm_list_tabs" data-parent="#accordion">
                                <div class="card-body p-0" style="max-width:100vw; max-height:75vh; overflow:auto;" id="div_wrapper">
                                    <div class="d-flex flex-wrap" id="transaction_wrapper"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>