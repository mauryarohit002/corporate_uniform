 <?php $design_action  = get_action_data('master', 'design');?>
 <div class="d-flex flex-wrap">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 ">
            <div class="d-flex form-group pt-4" style="overflow-y: auto;">
                <div class="col-12 col-sm-12 col-md-4 col-lg-2">
                    <div class="d-flex flex-wrap floating-form">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                            <p class="text-uppercase">type&nbsp;<span class="text-danger">*</span></p> 
                            <select 
                                class="form-control floating-select select2" 
                                id="trans_type" 
                                name="trans_type" 
                                placeholder=" "
                                onchange="set_area()"  
                                tabindex= "<?php echo $tabindex++; ?>"
                            >
                                <option value="FABRIC">FABRIC</option>
                                <option value="STITCHING">STITCHING</option>
                                <option value="PACKAGE">PACKAGE</option>
                                <option value="READYMADE">READYMADE</option>
                                <option value="SWATCH">SWATCH</option>

                            </select>
                            <small class="form-text text-muted helper-text" id="trans_type"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label d-none _stitching_area">
                            <p class="text-uppercase">apparel&nbsp;</p> 
                            <select 
                                class="form-control floating-select" 
                                id="apparel_id" 
                                name="apparel_id" 
                                placeholder=" "
                                onchange="validate_dropdown(this)"  
                                tabindex= "<?php echo $tabindex++; ?>"
                            ></select>
                            <small class="form-text text-muted helper-text" id="apparel_id_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label _fabric_area">
                            <p class="text-uppercase">barcode</p> 
                            <select 
                                class="form-control floating-select" 
                                id="bm_id" 
                                name="bm_id" 
                                placeholder=" "
                                onchange="validate_dropdown(this)"  
                                tabindex= "<?php echo $tabindex++; ?>"
                            ></select>
                            <small class="form-text text-muted helper-text" id="bm_id_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label d-none _readymade_fabric_area">
                            <p class="text-uppercase">barcode</p> 
                            <select 
                                class="form-control floating-select" 
                                id="brmm_id" 
                                name="brmm_id" 
                                placeholder=" "
                                onchange="validate_dropdown(this)"  
                                tabindex= "<?php echo $tabindex++; ?>"
                            ></select>
                            <small class="form-text text-muted helper-text" id="brmm_id_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label d-none _swatch_fabric_area">
                            <p class="text-uppercase">design&nbsp;<span class="text-danger">*</span>
                                <?php if(in_array('add', $design_action)): ?>
                                    <span>
                                        <a
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            title="ADD DESIGN"
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
                                placeholder="" 
                                autocomplete="off" 
                                tabindex= "<?php echo $tabindex++; ?>"
                                readonly
                            />
                            <small class="form-text text-muted helper-text" id="qty_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label mtr_area">
                            <p class="text-uppercase">mtr&nbsp;<span class="text-danger">*</span></p> 
                            <input 
                                type="number" 
                                class="form-control floating-input" 
                                id="mtr" 
                                name="mtr" 
                                value="0" 
                                placeholder="" 
                                autocomplete="off" 
                                onkeyup="calculate_transaction()" 
                                tabindex= "<?php echo $tabindex++; ?>"
                            />
                            <small class="form-text text-muted helper-text" id="mtr_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label mtr_area">
                            <p class="text-uppercase">total&nbsp;mtr&nbsp;<span class="text-danger">*</span></p> 
                            <input 
                                type="number" 
                                class="form-control floating-input" 
                                id="total_mtr" 
                                name="total_mtr" 
                                value="0" 
                                placeholder="" 
                                readonly
                            />
                            <small class="form-text text-muted helper-text" id="total_mtr_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                            <p class="text-uppercase">rate&nbsp;<span class="text-danger">*</span></p> 
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
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label" id="amt_area">
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
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label _display_mtr">
                            <p class="text-uppercase">available&nbsp;mtr</p> 
                            <input 
                                type="number" 
                                class="form-control floating-input" 
                                id="available_mtr"
                                name="available_mtr"
                                value="0" 
                                placeholder="" 
                                autocomplete="off"
                                readonly
                            />
                            <small class="form-text text-muted helper-text" id="available_mtr_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label _display_qty d-none">
                            <p class="text-uppercase">available&nbsp;qty</p> 
                            <input 
                                type="number" 
                                class="form-control floating-input" 
                                id="available_qty"
                                name="available_qty"
                                value="0" 
                                placeholder="" 
                                autocomplete="off"
                                readonly
                            />
                            <small class="form-text text-muted helper-text" id="available_qty_msg"></small>
                        </div>
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
                                value="0"
                                readonly
                            />
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
                                readonly
                            />
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
                                readonly
                            />
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
                </div>
                <div class="col-12 col-sm-12 col-md-1 col-lg-1">
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
              <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="card mb-3">
                        
                        <div id="added_item_list_tab" class="collapse show" aria-labelledby="added_item_list_tabs" data-parent="#accordion">
                            <div class="card-body p-0" style="max-width:100vw; max-height:50vh; overflow:auto;" id="div_wrapper">
                                <table class="table table-sm table-reponsive table-hover text-uppercase">
                                    <tbody class="table-dark border-0">
                                        <tr style="font-weight:bold; font-size: 0.8rem;">
                                            <td class="border-bottom border-top-0" >#&nbsp;</td>
                                            <td class="border-bottom border-top-0" >type&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td class="border-bottom border-top-0" >apparel&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td class="border-bottom border-top-0" >barcode&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td class="border-bottom border-top-0" >design&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td class="border-bottom border-top-0" >qty</td>
                                            <td class="border-bottom border-top-0" >mtr</td>
                                            <td class="border-bottom border-top-0" >total&nbsp;mtr</td>
                                            <td class="border-bottom border-top-0" >rate</td>
                                            <td class="border-bottom border-top-0" >amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td class="border-bottom border-top-0" >disc&nbsp;%</td>
                                            <td class="border-bottom border-top-0" >disc&nbsp;amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td class="border-bottom border-top-0" >taxable&nbsp;amt&nbsp;</td>
                                            <td class="border-bottom border-top-0" >sgst&nbsp;%&nbsp;</td>
                                            <td class="border-bottom border-top-0" >sgst&nbsp;amt&nbsp;</td>
                                            <td class="border-bottom border-top-0" >cgst&nbsp;%&nbsp;</td>
                                            <td class="border-bottom border-top-0" >cgst&nbsp;amt&nbsp;</td>
                                            <td class="border-bottom border-top-0" >igst&nbsp;%&nbsp;</td>
                                            <td class="border-bottom border-top-0" >igst&nbsp;amt&nbsp;</td> 
                                            <td class="border-bottom border-top-0" >total&nbsp;amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td class="border-bottom border-top-0" >description&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                            <td class="border-bottom border-top-0" >actions</td>
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