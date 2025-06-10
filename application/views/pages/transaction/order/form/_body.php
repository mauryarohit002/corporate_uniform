<?php 
    $customer_action= get_action_data('master', 'customer');
    $id             = empty($master_data) ? 0 : $master_data[0]['om_id'];
    $uuid           = empty($master_data) ? $om_uuid : $master_data[0]['om_uuid'];
    $tabindex       = 1;
    $bill_type      = (!empty($master_data) && ($master_data[0]['om_bill_type'] == 0))  ? '' : 'checked';
    $gst_type       = (!empty($master_data) && ($master_data[0]['om_gst_type'] == 1))  ? 1 : 0;
?>
<style>   
   .floating-label { 
        margin-bottom:10px !important;
        height: 60px !important;   
  }
  .floating-input{
     padding : 0px 5px !important;
  }
</style> 
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="master_content" role="tabpanel" aria-labelledby="master_tab">
                <div class="d-flex flex-wrap">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card mb-3">
                            <div class="card-header text-uppercase">general detail</div>
                            <div class="card-body">
                                <div class="d-flex flex-wrap pt-2 form-group floating-form">
                                    <div class="col-12 col-sm-12 col-md-1 col-lg-1 floating-label">
                                        <input 
                                            type="hidden" 
                                            id="id" 
                                            name="id" 
                                            value="<?php echo $id; ?>"
                                        />
                                        <input 
                                            type="hidden" 
                                            id="om_uuid" 
                                            name="om_uuid" 
                                            value="<?php echo $uuid; ?>"
                                        />
                                        <input 
                                            type="hidden" 
                                            id="ot_id" 
                                            name="ot_id" 
                                            value="0"
                                        />
                                        <input 
                                            type="number" 
                                            class="form-control floating-input" 
                                            id="om_entry_no" 
                                            name="om_entry_no" 
                                            value="<?php echo empty($master_data) ? $om_entry_no : $master_data[0]['om_entry_no'] ?>" 
                                            placeholder=" " />   
                                        <label class="text-uppercase">entry no</label>
                                        <small class="form-text text-muted helper-text" id="om_entry_no_msg"></small>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                        <input 
                                            type="date" 
                                            class="form-control floating-input" 
                                            id="om_entry_date" 
                                            name="om_entry_date" 
                                            value="<?php echo empty($master_data) ? date('Y-m-d') : date('Y-m-d', strtotime($master_data[0]['om_entry_date'])) ?>" 
                                            placeholder=" " 
                                            autocomplete="off"
                                            tabindex= "<?php echo $tabindex++; ?>"
                                        />   
                                        <label class="text-uppercase">entry date</label>
                                        <small class="form-text text-muted helper-text" id="om_entry_date_msg"></small>
                                    </div>
                                  
                                    <div class="col-12 col-sm-12 col-md-3 col-lg-3 floating-label">
                                        <p class="text-uppercase">company&nbsp;<span class="text-danger">*</span>
                                            <?php if(empty($master_data)): ?>
                                                <?php if(in_array('add', $customer_action)): ?>
                                                    <span>
                                                        <a
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="ADD COMPANY"
                                                            style="cursor: pointer;"
                                                            onclick='customer_popup(<?php echo json_encode(["field" => "om_customer_id"]) ?>)'
                                                        ><i class="fa fa-plus"></i></a>
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                        </p>
                                        <select 
                                            class="form-control floating-select" 
                                            id="om_customer_id" 
                                            name="om_customer_id" 
                                            placeholder=" " 
                                            tabindex= "<?php echo $tabindex++; ?>"
                                            onchange="validate_dropdown(this)" 
                                            <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'disabled="disabled"' : ''; ?>>
                                            <?php if(!empty($master_data) && !empty($master_data[0]['om_customer_id'])): ?>
                                                <option value="<?php echo $master_data[0]['om_customer_id'] ?>" selected>
                                                    <?php echo $master_data[0]['customer_name']; ?> 
                                                </option>
                                            <?php endif; ?>
                                        </select>
                                        <small class="form-text text-muted helper-text" id="om_customer_id_msg"></small>
                                         <input 
                                            type="hidden"
                                            id="om_gst_type"
                                            name="om_gst_type"
                                            value="<?php echo $gst_type; ?>"
                                        />
                                        <small class="form-text text-muted helper-text" id="om_billing_id_msg"></small>
                                    </div>

                                    <div class="col-12 col-sm-12 col-md-3 col-lg-3 floating-label">
                                        <textarea
                                            class="form-control floating-textarea"
                                            id="om_notes"
                                            name="om_notes"
                                            placeholder=" "
                                            autocomplete="off"
                                            tabindex= "<?php echo $tabindex++; ?>"
                                        ><?php echo empty($master_data) ? '' : $master_data[0]['om_notes']; ?></textarea>
                                        <label class="text-uppercase">notes</label>
                                        <small class="form-text text-muted helper-text" id="om_notes_msg"></small>
                                    </div> 
                                  
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="card"> 
                            <div class="card-header text-uppercase">
                                <ul class="nav nav-pills nav-fill nav-pills-secondary" id="pills-tab" role="tablist">
                                  
                                  <li class="nav-item">
                                        <a 
                                            class="nav-link active text-uppercase" 
                                            id="sku_tab" 
                                            data-toggle="tab"
                                            href="#sku_content" 
                                            role="tab" 
                                            aria-controls="sku_content" 
                                            aria-selected="true"
                                            style="font-size:0.8rem;"
                                        >sku (<span id="sku_transaction_count">0</span>)</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body p-0">
                                <div class="tab-content" id="pills-tabContent">
                                    <div 
                                        class="tab-pane fade show active" 
                                        id="sku_content" 
                                        role="tabpanel" 
                                        aria-labelledby="sku_tab"
                                    ><?php $this->load->view("pages/$menu/$sub_menu/form/sku", ['tabindex' => $tabindex]) ?></div>
                                   
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          

            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card mb-3">
                    <div class="card-header text-uppercase d-flex justify-content-between">
                        <div>amount detail</div>
                        <div>
                            <input 
                                type="checkbox" 
                                id="om_bill_type" 
                                name="om_bill_type" 
                                data-toggle="toggle" 
                                data-on="INCLUSIVE" 
                                data-off="EXCLUSIVE" 
                                data-onstyle="secondary" 
                                data-offstyle="secondary" 
                                data-width="130" 
                                data-size="small" 
                                <?php echo (!empty($master_data) && $master_data[0]['isExist']) ? 'disabled' : 'onchange="set_bill_type()"' ?>
                                <?php echo $bill_type; ?>
                            />
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap pt-2 form-group floating-form">
                            <div class="col-12 col-sm-12 col-md-1 col-lg-1 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_total_qty" 
                                    name="om_total_qty" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_total_qty'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">total qty</label>
                                <small class="form-text text-muted helper-text" id="om_total_qty_msg"></small>
                            </div>
                           
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_sub_amt" 
                                    name="om_sub_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_sub_amt'] ?>" 
                                    placeholder=" " 
                                    readonly="readonly" 
                                />   
                                <label class="text-uppercase">gross amt</label>
                                <small class="form-text text-muted helper-text" id="om_sub_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_disc_amt" 
                                    name="om_disc_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_disc_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">item disc amt</label>
                                <small class="form-text text-muted helper-text" id="om_disc_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_taxable_amt" 
                                    name="om_taxable_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_taxable_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">taxable amt</label>
                                <small class="form-text text-muted helper-text" id="om_taxable_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-1 col-lg-1 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_sgst_amt" 
                                    name="om_sgst_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_sgst_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">sgst amt</label>
                                <small class="form-text text-muted helper-text" id="om_sgst_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-1 col-lg-1 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_cgst_amt" 
                                    name="om_cgst_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_cgst_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">cgst amt</label>
                                <small class="form-text text-muted helper-text" id="om_cgst_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-1 col-lg-1 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_igst_amt" 
                                    name="om_igst_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_igst_amt'] ?>" 
                                    readonly
                                />   
                                <label class="text-uppercase">igst amt</label>
                                <small class="form-text text-muted helper-text" id="om_igst_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_bill_disc_per" 
                                    name="om_bill_disc_per" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_bill_disc_per'] ?>" 
                                    onkeyup="calculate_master(true)"
                                    placeholder=" " 
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                />   
                                <label class="text-uppercase">bill disc %</label>
                                <small class="form-text text-muted helper-text" id="om_bill_disc_per_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_bill_disc_amt" 
                                    name="om_bill_disc_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_bill_disc_amt'] ?>" 
                                    onkeyup="calculate_master()"
                                    placeholder=" " 
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                />   
                                <label class="text-uppercase">bill disc amt</label>
                                <small class="form-text text-muted helper-text" id="om_bill_disc_amt_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="om_round_off" 
                                    name="om_round_off" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_round_off'] ?>" 
                                    onkeyup="calculate_master()"
                                    placeholder=" " 
                                    autocomplete="off"
                                    tabindex= "<?php echo $tabindex++; ?>"
                                />   
                                <label class="text-uppercase">round off</label>
                                <small class="form-text text-muted helper-text" id="om_round_off_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                                <input 
                                    type="number" 
                                    class="form-control floating-input font-weight-bold" 
                                    id="om_total_amt" 
                                    name="om_total_amt" 
                                    value="<?php echo empty($master_data) ? 0 : $master_data[0]['om_total_amt'] ?>" 
                                    placeholder=" " 
                                    readonly
                                    style="font-size: 1.5rem;"
                                />   
                                <label class="text-uppercase">net amt</label>
                                <small class="form-text text-muted helper-text" id="om_total_amt_msg"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="right-panel-wrapper" id="payment_mode_wrapper"><?php $this->load->view('pages/component/panel/_right'); ?></div> 
