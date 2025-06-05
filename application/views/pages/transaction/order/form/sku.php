<div class="d-flex flex-wrap">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 ">
        <div class="d-flex form-group pt-4" style="overflow-y: auto;">
            <div class="col-12 col-sm-12 col-md-4 col-lg-2">
                <div class="d-flex flex-wrap floating-form">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                        <p class="text-uppercase">sku&nbsp;</p> 
                        <select 
                            class="form-control floating-select" 
                            id="sku_id" 
                            name="sku_id" 
                            placeholder=" "
                            onchange="validate_dropdown(this)"  
                            tabindex= "<?php echo $tabindex++; ?>"
                        ></select>
                        <small class="form-text text-muted helper-text" id="sku_id_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 d-flex flex pl-0">
                        <div class="floating-label" style="width: 100%;">
                            <input 
                                type="number" 
                                class="form-control floating-input" 
                                id="sku_mtr" 
                                name="sku_mtr" 
                                value="0" 
                                placeholder="" 
                                readonly
                            />  
                            <label class="text-uppercase">mtr</label>
                            <small class="form-text text-muted helper-text" id="sku_mtr_msg"></small>
                        </div>
                        <div>
                            <button
                                type="button"
                                class="btn btn-md btn-primary"
                                id="sku_mtr_btn"
                                data-toggle="tooltip" 
                                data-placement="bottom" 
                                title="FABRIC MTR" 
                                onclick="sku_mtr_popup()"
                            ><i class="text-info fa fa-google-wallet"></i></button>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label d-none">
                        <p class="text-uppercase">qty&nbsp;<span class="text-danger">*</span></p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_qty" 
                            name="sku_qty" 
                            value="1" 
                            placeholder="" 
                            autocomplete="off" 
                            tabindex= "<?php echo $tabindex++; ?>"
                            readonly
                        />
                        <small class="form-text text-muted helper-text" id="sku_qty_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                        <p class="text-uppercase">mrp&nbsp;<span class="text-danger">*</span></p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_mrp" 
                            name="sku_mrp" 
                            value="0" 
                            placeholder="" 
                            autocomplete="off" 
                            onkeyup="calculate_sku_transaction(), validate_textfield(this)" 
                            tabindex= "<?php echo $tabindex++; ?>"
                        />
                        <small class="form-text text-muted helper-text" id="sku_mrp_msg"></small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-4 col-lg-2 floating-form">
                <div class="d-flex flex-wrap">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                        <p class="text-uppercase">amt&nbsp;<span class="text-danger">*</span></p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_amt"
                            name="sku_amt"
                            value="0" 
                            placeholder="" 
                            autocomplete="off"
                            readonly
                        />
                        <small class="form-text text-muted helper-text" id="sku_amt_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                        <p class="text-uppercase">disc %</p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_disc_per"
                            name="sku_disc_per"
                            value="0"
                            placeholder="" 
                            autocomplete="off" 
                            onkeyup="calculate_sku_transaction(true)" 
                            tabindex= "<?php echo $tabindex++; ?>"
                        />
                        <small class="form-text text-muted helper-text" id="sku_disc_per_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                        <p class="text-uppercase">disc amt</p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_disc_amt"
                            name="sku_disc_amt"
                            value="0"
                            placeholder="" 
                            autocomplete="off" 
                            onkeyup="calculate_sku_transaction()" 
                            tabindex= "<?php echo $tabindex++; ?>"
                        />
                        <small class="form-text text-muted helper-text" id="sku_disc_amt_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                        <p class="text-uppercase">taxable amt</p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_taxable_amt"
                            name="sku_taxable_amt"
                            value="0"
                            readonly
                        />
                        <small class="form-text text-muted helper-text" id="sku_taxable_amt_msg"></small>
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
                            id="sku_sgst_per"
                            name="sku_sgst_per"
                            value="0"
                            readonly
                        />
                        <small class="form-text text-muted helper-text" id="sku_sgst_per_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                        <p class="text-uppercase">sgst amt</p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_sgst_amt"
                            name="sku_sgst_amt"
                            value="0"
                            readonly
                        />
                        <small class="form-text text-muted helper-text" id="sku_sgst_amt_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                        <p class="text-uppercase">cgst %</p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_cgst_per"
                            name="sku_cgst_per"
                            value="0"
                            readonly
                        />
                        <small class="form-text text-muted helper-text" id="sku_cgst_per_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                        <p class="text-uppercase">cgst amt</p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_cgst_amt"
                            name="sku_cgst_amt"
                            value="0"
                            readonly
                        />
                        <small class="form-text text-muted helper-text" id="sku_cgst_amt_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                        <p class="text-uppercase">igst %</p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_igst_per"
                            name="sku_igst_per"
                            value="0"
                            readonly
                        />
                        <small class="form-text text-muted helper-text" id="sku_igst_per_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                        <p class="text-uppercase">igst amt</p> 
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_igst_amt"
                            name="sku_igst_amt"
                            value="0"
                            readonly
                        />
                        <small class="form-text text-muted helper-text" id="sku_igst_amt_msg"></small>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-4 col-lg-2 floating-form">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-5">
                    <button 
                        type="button" 
                        class="btn btn-md btn-block btn-primary" 
                        id="add_row_btn"
                        data-toggle="tooltip" 
                        title="ADD SKU" 
                        data-placement="top" 
                        tabindex= "<?php echo $tabindex++; ?>"
                        onclick="add_sku_transaction()"   
                    ><i class="text-success fa fa-plus"></i></button>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                    <p class="text-uppercase">description</p> 
                    <input 
                        type="text" 
                        class="form-control floating-input" 
                        id="sku_description"
                        name="sku_description"
                        value=""
                        placeholder="" 
                        autocomplete="off" 
                        tabindex= "<?php echo $tabindex++; ?>"
                    />
                    <small class="form-text text-muted helper-text" id="sku_description_msg"></small>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                    <p class="text-uppercase">total amt</p> 
                    <input 
                        type="number" 
                        class="form-control floating-input" 
                        id="sku_total_amt"
                        name="sku_total_amt"
                        value="0"
                        readonly
                    />
                    <small class="form-text text-muted helper-text" id="sku_total_amt_msg"></small>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-4 col-lg-2">
                <div class="d-flex flex-column align-items-center">
                    <span id="sku_image_span">
                        <img 
                            class="img-thumbnail" 
                            width="150px" 
                            src="<?php echo assets(NOIMAGE); ?>"
                        />
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card mb-3">
            <div id="added_sku_list_tab" class="collapse show" aria-labelledby="added_sku_list_tabs" data-parent="#accordion">
                <div class="card-body p-0" style="max-width:100vw; max-height:50vh; overflow:auto;" id="div_wrapper">
                    <table class="table table-sm table-reponsive table-hover text-uppercase">
                        <tbody class="table-dark border-0">
                            <tr style="font-weight:bold; font-size: 0.8rem;">
                                <td class="border-bottom border-top-0" >sku&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td class="border-bottom border-top-0" >mtr&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td class="border-bottom border-top-0" >mrp&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td class="border-bottom border-top-0" >amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
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
                                <td class="border-bottom border-top-0" ><?php echo ($_SESSION['branch_default'] == 1) ? 'measurement' : 'actions'; ?></td>
                            </tr>
                        </tbody>
                        <tbody id="sku_transaction_wrapper" style="font-weight: bold; font-size: 0.8rem;"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
   
</div>