<?php 
    $supplier_action= get_action_data('master', 'supplier');
    $product_action  = get_action_data('master', 'product');
    $design_action  = get_action_data('master', 'design');

    $readymade_category_action = get_action_data('master', 'readymade_category');
    $color_action   = get_action_data('master', 'color');
    $size_action    = get_action_data('master', 'size');
    $gender_action  = get_action_data('master', 'gender');
    $id             = empty($master_data) ? 0 : $master_data[0]['prmm_id'];
    $uuid           = empty($master_data) ? $prmm_uuid : $master_data[0]['prmm_uuid'];
    $tabindex       = 1;
?>
<style>  
   .floating-label { 
       margin-bottom:8px !important;
  }
</style> 
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="d-flex flex-wrap mb-2">
            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                <div class="card mb-3">
                    <div class="card-header text-uppercase">general detail</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap pt-2 form-group floating-form">
                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 floating-label">
                                <input 
                                    type="hidden" 
                                    id="id" 
                                    name="id" 
                                    value="<?php echo $id; ?>"
                                />
                                <input 
                                    type="hidden" 
                                    id="prmm_uuid" 
                                    name="prmm_uuid" 
                                    value="<?php echo $uuid; ?>"
                                />
                                <input 
                                    type="hidden" 
                                    id="prmt_id" 
                                    name="prmt_id" 
                                    value="0"
                                />
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_entry_no" 
                                    name="prmm_entry_no" 
                                    value="<?php echo empty($master_data) ? $prmm_entry_no : $master_data[0]['prmm_entry_no'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">entry no</label>
                                <small class="form-text text-muted helper-text" id="prmm_entry_no_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 floating-label">
                                <input 
                                    type="date" 
                                    class="form-control floating-input" 
                                    id="prmm_entry_date" 
                                    name="prmm_entry_date" 
                                    value="<?php echo empty($master_data) ? date('Y-m-d') : date('Y-m-d', strtotime($master_data[0]['prmm_entry_date'])) ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">entry date</label>
                                <small class="form-text text-muted helper-text" id="prmm_entry_date_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 floating-label">
                                <input 
                                    type="text" 
                                    class="form-control floating-input" 
                                    id="prmm_bill_no" 
                                    name="prmm_bill_no" 
                                    value="<?php echo empty($master_data) ? '' : $master_data[0]['prmm_bill_no'] ?>" 
                                    placeholder=" " 
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                />   
                                <label class="text-uppercase">bill no</label>
                                <small class="form-text text-muted helper-text" id="prmm_bill_no_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 floating-label">
                                <input 
                                    type="date" 
                                    class="form-control floating-input" 
                                    id="prmm_bill_date" 
                                    name="prmm_bill_date" 
                                    value="<?php echo empty($master_data) ? date('Y-m-d') : date('Y-m-d', strtotime($master_data[0]['prmm_bill_date'])) ?>" 
                                    placeholder=" " 
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                />   
                                <label class="text-uppercase">bill date</label>
                                <small class="form-text text-muted helper-text" id="prmm_bill_date_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                <p class="text-uppercase">supplier&nbsp;<span class="text-danger">*</span>
                                    <?php if(empty($master_data)): ?>
                                        <?php if(in_array('add', $supplier_action)): ?>
                                            <span>
                                                <a
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="ADD SUPPLIER"
                                                    style="cursor: pointer;"
                                                    onclick='supplier_popup(<?php echo json_encode([]) ?>)'
                                                ><i class="fa fa-plus"></i></a>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                </p>
                                <select 
                                    class="form-control floating-select" 
                                    id="prmm_supplier_id" 
                                    name="prmm_supplier_id" 
                                    placeholder=" " 
                                    tabindex= "<?php echo $tabindex++; ?>"
                                    onchange="validate_dropdown(this)" 
                                    <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'disabled="disabled"' : ''; ?>
                                >
                                    <?php if(!empty($master_data) && !empty($master_data[0]['prmm_supplier_id'])): ?>
                                        <option value="<?php echo $master_data[0]['prmm_supplier_id'] ?>" selected>
                                            <?php echo $master_data[0]['supplier_name']; ?> 
                                            <input type="hidden" name="prmm_supplier_id" value="<?php echo $master_data[0]['prmm_supplier_id']; ?>" />
                                        </option>
                                    <?php endif; ?>
                                </select>
                                <input 
                                    type="hidden"
                                    id="prmm_gst_type"
                                    name="prmm_gst_type"
                                    value="0"
                                />
                                <small class="form-text text-muted helper-text" id="prmm_supplier_id_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-4 col-lg-3 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_total_qty" 
                                    name="prmm_total_qty" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_total_qty'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">total qty</label>
                                <small class="form-text text-muted helper-text" id="prmm_total_qty_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                <textarea
                                    class="form-control floating-textarea"
                                    id="prmm_notes"
                                    name="prmm_notes"
                                    placeholder=" "
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                ><?php echo empty($master_data) ? '' : $master_data[0]['prmm_notes']; ?></textarea>
                                <label class="text-uppercase">notes</label>
                                <small class="form-text text-muted helper-text" id="prmm_notes_msg"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                <div class="card mb-3">
                    <div class="card-header text-uppercase">amt detail</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap pt-2 form-group floating-form">
                            <div class="col-12 col-sm-12 col-md-4 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_sub_amt" 
                                    name="prmm_sub_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_sub_amt'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">gross amt</label>
                                <small class="form-text text-muted helper-text" id="prmm_sub_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_disc_amt" 
                                    name="prmm_disc_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_disc_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">item disc amt</label>
                                <small class="form-text text-muted helper-text" id="prmm_disc_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_taxable_amt" 
                                    name="prmm_taxable_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_taxable_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">taxable amt</label>
                                <small class="form-text text-muted helper-text" id="prmm_taxable_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_extra_amt" 
                                    name="prmm_extra_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_extra_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">Extra amt</label>
                                <small class="form-text text-muted helper-text" id="prmm_extra_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_sgst_amt" 
                                    name="prmm_sgst_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_sgst_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">sgst amt</label>
                                <small class="form-text text-muted helper-text" id="prmm_sgst_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_cgst_amt" 
                                    name="prmm_cgst_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_cgst_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">cgst amt</label>
                                <small class="form-text text-muted helper-text" id="prmm_cgst_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_igst_amt" 
                                    name="prmm_igst_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_igst_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">igst amt</label>
                                <small class="form-text text-muted helper-text" id="prmm_igst_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_bill_disc_per" 
                                    name="prmm_bill_disc_per" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_bill_disc_per'] ?>" 
                                    onkeyup="calculate_master(true, false)"
                                    placeholder=" " 
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                />   
                                <label class="text-uppercase">bill disc %</label>
                                <small class="form-text text-muted helper-text" id="prmm_bill_disc_per_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_bill_disc_amt" 
                                    name="prmm_bill_disc_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_bill_disc_amt'] ?>" 
                                    onkeyup="calculate_master()"
                                    placeholder=" " 
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                />   
                                <label class="text-uppercase">bill disc amt</label>
                                <small class="form-text text-muted helper-text" id="prmm_bill_disc_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prmm_round_off" 
                                    name="prmm_round_off" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_round_off'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">round off</label>
                                <small class="form-text text-muted helper-text" id="prmm_round_off_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input font-weight-bold" 
                                    id="prmm_total_amt" 
                                    name="prmm_total_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prmm_total_amt'] ?>" 
                                    placeholder=" " 
                                    readonly
                                    style="font-size: 1.5rem;"
                                />   
                                <label class="text-uppercase">net amt</label>
                                <small class="form-text text-muted helper-text" id="prmm_total_amt_msg"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header text-uppercase">
                        <h5 class="mb-0">
                            <a 
                                type="button" 
                                class="btn btn-sm btn-secondary" 
                                id="add_product_tabs"
                                data-toggle="collapse" 
                                data-target="#add_product_tab" 
                                aria-expanded="true" 
                                aria-controls="add_product_tab"
                            >add product</a>
                        </h5>
                    </div>
                    <div id="add_product_tab" class="collapse show" aria-labelledby="add_product_tabs" data-parent="#accordion">
                        <div class="card-body">
                            <div class="d-flex form-group pt-3" style="overflow-y: auto;">
                                <div class="col-12 col-sm-12 col-md-4 col-lg-3">
                                    <div class="d-flex flex-wrap floating-form">
                                         <input 
                                                type="hidden" 
                                                id="cost_char" 
                                                name="cost_char" 
                                                value=" "
                                            />
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">Product&nbsp;<span class="text-danger">*</span>
                                                <?php if(in_array('add', $product_action)): ?>
                                                    <span>
                                                        <a
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="ADD PRODUCT"
                                                            style="cursor: pointer;"
                                                            onclick='product_popup(<?php echo json_encode(["field" => "product_id"]); ?>)'
                                                        ><i class="fa fa-plus"></i></a>
                                                    </span>
                                                <?php endif; ?>
                                            </p> 
                                            <select 
                                                class="form-control floating-select" 
                                                id="product_id" 
                                                name="product_id" 
                                                placeholder=" "
                                                onchange="validate_dropdown(this)"  
                                                tabindex= "<?php echo $tabindex++; ?>"
                                            ></select>
                                            <small class="form-text text-muted helper-text" id="product_id_msg"></small>
                                        </div>

                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">fabric code<?php if(in_array('add', $design_action)): ?>
                                                    <span>
                                                        <a
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="ADD FABRIC CODE"
                                                            style="cursor: pointer;"
                                                            onclick='design_popup(<?php echo json_encode(["field" => "design_id"]); ?>)'
                                                        ><i class="fa fa-plus"></i></a>
                                                    </span>
                                                <?php endif; ?>
                                            </p> 
                                            <select 
                                                class="form-control floating-select" 
                                                id="design_id" 
                                                name="design_id" 
                                                placeholder=" "
                                                onchange="validate_dropdown(this)"  
                                                tabindex= "<?php echo $tabindex++; ?>"
                                            ></select>
                                            <small class="form-text text-muted helper-text" id="design_id_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">category&nbsp;
                                                <?php if(in_array('add', $readymade_category_action)): ?>
                                                    <span>
                                                        <a
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="ADD READYMADE CATEGORY"
                                                            style="cursor: pointer;"
                                                             onclick='popup(<?php echo json_encode(["sub_menu" => "readymade_category", "field" => "readymade_category_id"]); ?>)'
                                                        ><i class="fa fa-plus"></i></a>
                                                    </span>
                                                <?php endif; ?>
                                            </p> 
                                            <select 
                                                class="form-control floating-select" 
                                                id="readymade_category_id" 
                                                name="readymade_category_id" 
                                                placeholder=" "
                                                onchange="validate_dropdown(this)"  
                                                tabindex= "<?php echo $tabindex++; ?>"
                                            ></select>
                                            <small class="form-text text-muted helper-text" id="readymade_category_id_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">color
                                                <?php if(in_array('add', $color_action)): ?>
                                                    <span>
                                                        <a
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="ADD COLOR"
                                                            style="cursor: pointer;"
                                                            onclick='popup(<?php echo json_encode(["sub_menu" => "color", "field" => "color_id"]); ?>)'
                                                        ><i class="fa fa-plus"></i></a>
                                                    </span>
                                                <?php endif; ?>
                                            </p> 
                                            <select 
                                                class="form-control floating-select" 
                                                id="color_id" 
                                                name="color_id" 
                                                placeholder=" "
                                                onchange="validate_dropdown(this)"  
                                                tabindex= "<?php echo $tabindex++; ?>"
                                            ></select>
                                            <small class="form-text text-muted helper-text" id="color_id_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">size&nbsp;
                                                <?php if(in_array('add', $size_action)): ?>
                                                    <span>
                                                        <a
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="ADD SIZE"
                                                            style="cursor: pointer;"
                                                            onclick='popup(<?php echo json_encode(["sub_menu" => "size", "field" => "size_id"]); ?>)'
                                                        ><i class="fa fa-plus"></i></a>
                                                    </span>
                                                <?php endif; ?>
                                            </p> 
                                            <select 
                                                class="form-control floating-select" 
                                                id="size_id" 
                                                name="size_id" 
                                                placeholder=" "
                                                tabindex= "<?php echo $tabindex++; ?>"
                                            ></select>
                                            <small class="form-text text-muted helper-text" id="size_id_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">gender&nbsp;
                                                <?php if(in_array('add', $gender_action)): ?>
                                                    <span>
                                                        <a
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="ADD GENDER"
                                                            style="cursor: pointer;"
                                                            onclick='popup(<?php echo json_encode(["sub_menu" => "gender", "field" => "gender_id"]); ?>)'
                                                        ><i class="fa fa-plus"></i></a>
                                                    </span>
                                                <?php endif; ?>
                                            </p> 
                                            <select 
                                                class="form-control floating-select" 
                                                id="gender_id" 
                                                name="gender_id" 
                                                placeholder=" "
                                                tabindex= "<?php echo $tabindex++; ?>">
                                              <option value="1" selected>MALE</option>  
                                                
                                            </select>
                                            <small class="form-text text-muted helper-text" id="gender_id_msg"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-2 floating-form">
                                    <div class="d-flex flex-wrap">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">qty&nbsp;<span class="text-danger">*</span></p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="qty" 
                                                name="qty" 
                                                value="1" 
                                                onkeyup="calculate_transaction()" 
                                                placeholder="" 
                                                autocomplete="off"
                                                tabindex= "<?php echo $tabindex++; ?>"
                                                min="0" 
                                                oninput="this.value = Math.abs(this.value)"  
                                            />
                                            <small class="form-text text-muted helper-text" id="qty_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">MRP&nbsp;</p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="mrp" 
                                                name="mrp" 
                                                value="0" 
                                                placeholder="" 
                                                autocomplete="off"
                                                tabindex= "<?php echo $tabindex++; ?>"
                                            />
                                            <small class="form-text text-muted helper-text" id="mrp_msg"></small>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">pur.&nbsp;price<span class="text-danger">*</span></p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="rate" 
                                                name="rate" 
                                                value="0" 
                                                placeholder="" 
                                                autocomplete="off" 
                                                onkeyup="calculate_transaction()" 
                                                tabindex= "<?php echo $tabindex++; ?>"
                                            />
                                            <small class="form-text text-muted helper-text" id="rate_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">amt&nbsp;<span class="text-danger">*</span></p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="amt"
                                                name="amt"
                                                value="0" 
                                                placeholder="" 
                                                autocomplete="off"
                                                readonly
                                            />
                                            <small class="form-text text-muted helper-text" id="amt_msg"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-2 floating-form">
                                    <div class="d-flex flex-wrap">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">disc %</p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="disc_per"
                                                name="disc_per"
                                                value="0"
                                                placeholder="" 
                                                autocomplete="off" 
                                                onkeyup="calculate_transaction(true)" 
                                                tabindex= "<?php echo $tabindex++; ?>"
                                            />
                                            <small class="form-text text-muted helper-text" id="disc_per_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">disc amt</p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="disc_amt"
                                                name="disc_amt"
                                                value="0"
                                                placeholder="" 
                                                autocomplete="off" 
                                                onkeyup="calculate_transaction()" 
                                                tabindex= "<?php echo $tabindex++; ?>"
                                            />
                                            <small class="form-text text-muted helper-text" id="disc_amt_msg"></small>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <p class="text-uppercase">taxable amt</p> 
                                        <input 
                                            type="number" 
                                            class="form-control floating-input" 
                                            id="taxable_amt"
                                            name="taxable_amt"
                                            value="0"
                                            readonly
                                        />
                                        <small class="form-text text-muted helper-text" id="taxable_amt_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <p class="text-uppercase">extra amt</p> 
                                        <input  
                                            type="number" 
                                            class="form-control floating-input" 
                                            id="extra_amt"
                                            name="extra_amt"
                                            onkeyup="calculate_transaction()"
                                            value="0"
                                        />
                                        <small class="form-text text-muted helper-text" id="extra_amt_msg"></small>

                                         <input  
                                            type="hidden" 
                                            class="form-control floating-input" 
                                            id="actual_taxable_amt"
                                            name="actual_taxable_amt"
                                            value="0"
                                        />
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-2 floating-form">
                                    <div class="d-flex flex-wrap">
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">sgst %</p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="sgst_per"
                                                name="sgst_per"
                                                onkeyup="calculate_readymade_gst()" 
                                                value="0"
                                                readonly />
                                            <small class="form-text text-muted helper-text" id="sgst_per_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">sgst amt</p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="sgst_amt"
                                                name="sgst_amt"
                                                value="0"
                                                readonly/>
                                            <small class="form-text text-muted helper-text" id="sgst_amt_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">cgst %</p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="cgst_per"
                                                name="cgst_per"
                                                value="0"
                                                readonly/>
                                            <small class="form-text text-muted helper-text" id="cgst_per_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">cgst amt</p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="cgst_amt"
                                                name="cgst_amt"
                                                value="0"
                                                readonly
                                            />
                                            <small class="form-text text-muted helper-text" id="cgst_amt_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">igst %</p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="igst_per"
                                                name="igst_per"
                                                onkeyup="calculate_readymade_gst()" 
                                                value="0"
                                                readonly
                                            />
                                            <small class="form-text text-muted helper-text" id="igst_per_msg"></small>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                            <p class="text-uppercase">igst amt</p> 
                                            <input 
                                                type="number" 
                                                class="form-control floating-input" 
                                                id="igst_amt"
                                                name="igst_amt"
                                                value="0"
                                                readonly
                                            />
                                            <small class="form-text text-muted helper-text" id="igst_amt_msg"></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12 col-md-4 col-lg-2 floating-form">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <p class="text-uppercase">total amt</p> 
                                        <input 
                                            type="number" 
                                            class="form-control floating-input" 
                                            id="total_amt"
                                            name="total_amt"
                                            value="0"
                                            readonly
                                        />
                                        <small class="form-text text-muted helper-text" id="total_amt_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                        <p class="text-uppercase">description</p> 
                                        <input 
                                            type="text" 
                                            class="form-control floating-input" 
                                            id="description"
                                            name="description"
                                            value=""
                                            placeholder="" 
                                            autocomplete="off" 
                                            tabindex= "<?php echo $tabindex++; ?>"
                                        />
                                        <small class="form-text text-muted helper-text" id="description_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12" >
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
                                        <?php 
                                            if(!empty($cost_char)): 
                                                foreach ([1, 2, 3, 4, 5, 6, 7, 8, 9, 0] as $key => $value):
                                        ?>
                                                    <input
                                                        type="hidden"
                                                        id="cost_char_<?php echo $value; ?>"
                                                        value="<?php echo $cost_char[0]['cost_char_'.$value]; ?>"
                                                    />
                                        <?php 
                                                endforeach;
                                            endif; 
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap">
            <div class="col-12 col-sm-12 col-md-10 col-lg-10 p-0">
                <div class="card">
                    <div class="card-header text-uppercase">
                        <h5 class="mb-0">
                            <a 
                                type="button" 
                                class="btn btn-sm btn-secondary" 
                                id="added_product_tabs"
                                data-toggle="collapse" 
                                data-target="#added_product_tab" 
                                aria-expanded="true" 
                                aria-controls="added_product_tab"
                            >added product list (<span id="transaction_count">0</span>)</a>
                        </h5>
                    </div>
                    <div id="added_product_tab" class="collapse show" aria-labelledby="added_product_tabs" data-parent="#accordion">
                        <div class="card-body p-0" style="max-width:100vw; max-height:50vh; overflow:auto;" id="div_wrapper">
                            <table class="table table-sm text-uppercase">
                                <tbody class="table-dark border-0">
                                    <tr style="font-weight:bold; font-size: 0.8rem;">
                                        <td class="border-bottom border-top-0" >product&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >fabric&nbsp;code&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >category&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >color&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >size&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >gender&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >MRP&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >qty&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >purchase price&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >disc&nbsp;%&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >disc&nbsp;amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >taxable&nbsp;amt&nbsp;</td>
                                        <td class="border-bottom border-top-0" >extra&nbsp;amt&nbsp;</td>
                                        <td class="border-bottom border-top-0" >sgst&nbsp;%&nbsp;</td>
                                        <td class="border-bottom border-top-0" >sgst&nbsp;amt&nbsp;</td>
                                        <td class="border-bottom border-top-0" >cgst&nbsp;%&nbsp;</td>
                                        <td class="border-bottom border-top-0" >cgst&nbsp;amt&nbsp;</td>
                                        <td class="border-bottom border-top-0" >igst&nbsp;%&nbsp;</td>
                                        <td class="border-bottom border-top-0" >igst&nbsp;amt&nbsp;</td>
                                        <td class="border-bottom border-top-0" >total&nbsp;amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >description&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >actions</td>
                                    </tr>
                                </tbody>
                                <tbody id="transaction_wrapper" style="font-weight: bold; font-size: 0.8rem;"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-2 col-lg-2">
                <div class="card" >
                    <div class="card-header text-uppercase">image</div>
                    <div class="card-body p-1" id="image-preview" style="width: auto; height:12rem;"></div>
                </div>
            </div>
        </div>
    </div>
</div>