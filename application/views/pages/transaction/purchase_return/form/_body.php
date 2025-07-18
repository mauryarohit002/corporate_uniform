<?php 
    $id         = empty($master_data) ? 0 : $master_data[0]['prm_id'];
    $uuid       = empty($master_data) ? $prm_uuid : $master_data[0]['prm_uuid'];
    $tabindex   = 1;
?>

<style>  
   .floating-label { 
       margin-bottom:7px !important;
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
                                    id="prm_uuid" 
                                    name="prm_uuid" 
                                    value="<?php echo $uuid; ?>"
                                />
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_entry_no" 
                                    name="prm_entry_no" 
                                    value="<?php echo empty($master_data) ? $prm_entry_no : $master_data[0]['prm_entry_no'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">entry no</label>
                                <small class="form-text text-muted helper-text" id="prm_entry_no_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 floating-label">
                                <input 
                                    type="date" 
                                    class="form-control floating-input" 
                                    id="prm_entry_date" 
                                    name="prm_entry_date" 
                                    value="<?php echo empty($master_data) ? date('Y-m-d') : date('Y-m-d', strtotime($master_data[0]['prm_entry_date'])) ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">entry date</label>
                                <small class="form-text text-muted helper-text" id="prm_entry_date_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                <p class="text-uppercase">qrcode</p>
                                <select 
                                    class="form-control floating-select" 
                                    id="bm_id" 
                                    placeholder=" " 
                                    tabindex= "<?php echo $tabindex++; ?>"
                                ></select>
                                <small class="form-text text-muted helper-text" id="bm_id_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                                <input 
                                    type="hidden" 
                                    id="prm_supplier_id" 
                                    name="prm_supplier_id" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_supplier_id'] ?>" 
                                />
                                <input 
                                    type="text" 
                                    class="form-control floating-input" 
                                    id="supplier_name" 
                                    value="<?php echo empty($master_data) ? '' : $master_data[0]['supplier_name'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">supplier</label>
                                <small class="form-text text-muted helper-text" id="prm_supplier_id_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_total_qty" 
                                    name="prm_total_qty" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_total_qty'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">total rolls</label>
                                <small class="form-text text-muted helper-text" id="prm_total_qty_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_total_mtr" 
                                    name="prm_total_mtr" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_total_mtr'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">total mtr</label>
                                <small class="form-text text-muted helper-text" id="prm_total_mtr_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                                <textarea
                                    class="form-control floating-textarea"
                                    id="prm_notes"
                                    name="prm_notes"
                                    placeholder=" "
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                ><?php echo empty($master_data) ? '' : $master_data[0]['prm_notes']; ?></textarea>
                                <label class="text-uppercase">notes</label>
                                <small class="form-text text-muted helper-text" id="prm_notes_msg"></small>
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
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_sub_amt" 
                                    name="prm_sub_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_sub_amt'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">gross amt</label>
                                <small class="form-text text-muted helper-text" id="prm_sub_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_disc_amt" 
                                    name="prm_disc_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_disc_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">item disc amt</label>
                                <small class="form-text text-muted helper-text" id="prm_disc_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_taxable_amt" 
                                    name="prm_taxable_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_taxable_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">taxable amt</label>
                                <small class="form-text text-muted helper-text" id="prm_taxable_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_sgst_amt" 
                                    name="prm_sgst_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_sgst_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">sgst amt</label>
                                <small class="form-text text-muted helper-text" id="prm_sgst_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_cgst_amt" 
                                    name="prm_cgst_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_cgst_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">cgst amt</label>
                                <small class="form-text text-muted helper-text" id="prm_cgst_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_igst_amt" 
                                    name="prm_igst_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_igst_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">igst amt</label>
                                <small class="form-text text-muted helper-text" id="prm_igst_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_bill_disc_per" 
                                    name="prm_bill_disc_per" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_bill_disc_per'] ?>" 
                                    onkeyup="calculate_master(true, false)"
                                    placeholder=" " 
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                />   
                                <label class="text-uppercase">bill disc %</label>
                                <small class="form-text text-muted helper-text" id="prm_bill_disc_per_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_bill_disc_amt" 
                                    name="prm_bill_disc_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_bill_disc_amt'] ?>" 
                                    onkeyup="calculate_master()"
                                    placeholder=" " 
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                />   
                                <label class="text-uppercase">bill disc amt</label>
                                <small class="form-text text-muted helper-text" id="prm_bill_disc_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="prm_round_off" 
                                    name="prm_round_off" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_round_off'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">round off</label>
                                <small class="form-text text-muted helper-text" id="prm_round_off_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input font-weight-bold" 
                                    id="prm_total_amt" 
                                    name="prm_total_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['prm_total_amt'] ?>" 
                                    placeholder=" " 
                                    readonly
                                    style="font-size: 1.5rem;"
                                />   
                                <label class="text-uppercase">net amt</label>
                                <small class="form-text text-muted helper-text" id="prm_total_amt_msg"></small>
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
                                id="added_fabric_tabs"
                                data-toggle="collapse" 
                                data-target="#added_fabric_tab" 
                                aria-expanded="true" 
                                aria-controls="added_fabric_tab"
                            >added qrcode list (<span id="transaction_count">0</span>)</a>
                        </h5>
                    </div>
                    <div id="added_fabric_tab" class="collapse show" aria-labelledby="added_fabric_tabs" data-parent="#accordion">
                        <div class="card-body p-0" style="max-width:100vw; max-height:50vh; overflow:auto;" id="div_wrapper">
                            <table class="table table-sm text-uppercase">
                                <tbody class="table-dark border-0">
                                    <tr style="font-weight:bold; font-size: 0.8rem;">
                                        <td class="border-bottom border-top-0" >qrcode&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >bill&nbsp;no&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >bill&nbsp;date&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >fabric&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >design&nbsp;no&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >color&nbsp;no&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >width&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >hsn&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >qty&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >mtr&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >total&nbsp;mtr&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >rate&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >disc&nbsp;%&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >disc&nbsp;amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                        <td class="border-bottom border-top-0" >taxable&nbsp;amt&nbsp;</td>
                                        <td class="border-bottom border-top-0" >sgst&nbsp;%&nbsp;</td>
                                        <td class="border-bottom border-top-0" >sgst&nbsp;amt&nbsp;</td>
                                        <td class="border-bottom border-top-0" >cgst&nbsp;%&nbsp;</td>
                                        <td class="border-bottom border-top-0" >cgst&nbsp;amt&nbsp;</td>
                                        <td class="border-bottom border-top-0" >igst&nbsp;%&nbsp;</td>
                                        <td class="border-bottom border-top-0" >igst&nbsp;amt&nbsp;</td>
                                        <td class="border-bottom border-top-0" >total&nbsp;amt&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
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