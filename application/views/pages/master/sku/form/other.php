<?php $other_action= get_action_data('master', 'other');?>
<div class="d-flex flex-wrap">
    <div class="col-12 col-sm-12 col-md-4 col-lg-4 d-flex flex-wrap border p-3">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
            <div class="d-flex flex-wrap form-group floating-form mt-4">
                <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                    <p class="text-uppercase">other&nbsp;<span class="text-danger">*</span>
                        <?php if(in_array('add', $other_action)): ?>
                            <span>
                                <a
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="ADD OTHER"
                                    style="cursor: pointer;"
                                    onclick='popup(<?php echo json_encode(["sub_menu" => "other", "field" => "other_id"]) ?>)'
                                ><i class="fa fa-plus"></i></a>
                            </span>
                        <?php endif; ?>
                    </p>
                    <select 
                        class="form-control floating-select" 
                        id="other_id" 
                        name="other_id" 
                        placeholder=" " 
                        tabindex="<?php echo $tabindex++; ?>"
                        onkeyup="validate_dropdown(this, true)"
                    ></select>
                    <small class="form-text text-muted helper-text" id="other_id_msg"></small>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-4 floating-label">
                    <input 
                        type="number" 
                        class="form-control floating-input" 
                        id="other_rate" 
                        name="other_rate" 
                        value="0" 
                        placeholder=" " 
                        autocomplete="off" 
                        tabindex="<?php echo $tabindex++; ?>"
                    />   
                    <label class="text-uppercase">rate</label>
                    <small class="form-text text-muted helper-text" id="other_rate_msg"></small>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-2">
                    <button 
                        type="button" 
                        class="btn btn-md btn-block btn-primary" 
                        id="add_row_btn"
                        data-toggle="tooltip" 
                        title="ADD OTHER CHARGE" 
                        data-placement="top" 
                        tabindex= "<?php echo $tabindex++; ?>"
                        onclick="add_other_transaction()"   
                    ><i class="text-success fa fa-plus"></i></button>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-8 col-lg-8 border d-flex" style="max-width:100vw; max-height:50vh; overflow:auto;">
        <table class="table table-sm table-reponsive table-hover text-uppercase">
            <thead class="table-dark">
                <tr>
                    <th width="70%">other</th>
                    <th width="10%">rate</th>
                    <th width="10%">edit</th>
                    <th width="10%">delete</th>
                </tr>
            </thead>
            <tbody class="border-0" id="wrapper_other"></tbody>
        </table>
    </div>
</div>