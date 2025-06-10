<?php $fabric_action= get_action_data('master', 'fabric');?>
<?php $design_action= get_action_data('master', 'design');?>
<?php $color_action= get_action_data('master', 'color');?>
<?php $width_action= get_action_data('master', 'width');?>
<div class="d-flex flex-wrap">
    <div class="col-12 col-sm-12 col-md-6 col-lg-4 d-flex flex-wrap border p-3">
        <div class="col-12 col-sm-12 col-md-12 col-lg-5">
            <span class="d-flex justify-content-center my-4" id="design_preview" style="width: 10rem; height:10rem;">
                <img 
                    class="img-thumbnail form_loading" 
                    src="<?php echo assets(NOIMAGE); ?>"
                    style="max-width: 100%; max-height: 100%; object-fit: contain;"
                />
            </span>
            <input 
                type="hidden"
                id="design_image"
                name="design_image"
                value="<?php echo NOIMAGE; ?>"
            />
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-7">
            <div class="d-flex flex-wrap form-group floating-form mt-4">
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                    <p class="text-uppercase">fabric&nbsp;<span class="text-danger">*</span>
                        <?php if(in_array('add', $fabric_action)): ?>
                           <!--  <span>
                                <a
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="ADD FABRIC"
                                    style="cursor: pointer;"
                                    onclick='fabric_popup(<?php //echo json_encode(["field" => "fabric_id"]); ?>)'
                                ><i class="fa fa-plus"></i></a>
                            </span> -->
                        <?php endif; ?>
                    </p> 
                    <select 
                        class="form-control floating-select" 
                        id="fabric_id" 
                        name="fabric_id" 
                        placeholder=" "
                        onchange="validate_dropdown(this)"  
                        tabindex= "<?php echo $tabindex++; ?>"
                    ></select>
                    <small class="form-text text-muted helper-text" id="fabric_id_msg"></small>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label ">
                    <p class="text-uppercase">FABRIC CODE&nbsp;<span class="text-danger">*</span>
                        <?php if(in_array('add', $design_action)): ?>
                            <span>
                               <!--  <a
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="ADD FABRIC CODE"
                                    style="cursor: pointer;"
                                    onclick='design_popup(<?php //echo json_encode(["field" => "design_id"]); ?>)'
                                ><i class="fa fa-plus"></i></a> -->
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
                <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label ">
                    <p class="text-uppercase">color&nbsp;<span class="text-danger">*</span>
                        <?php if(in_array('add', $color_action)): ?>
                            <span>
                               <!--  <a
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="ADD COLOR"
                                    style="cursor: pointer;"
                                    onclick='color_popup(<?php //echo json_encode(["field" => "color_id"]); ?>)'
                                ><i class="fa fa-plus"></i></a> -->
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
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label ">
            <p class="text-uppercase">width&nbsp;<span class="text-danger">*</span>
                <?php if(in_array('add', $width_action)): ?>
                  <!--   <span>
                        <a
                            data-toggle="tooltip"
                            data-placement="top"
                            title="ADD WIDTH"
                            style="cursor: pointer;"
                            onclick='width_popup(<?php //echo json_encode(["field" => "width_id"]); ?>)'
                        ><i class="fa fa-plus"></i></a>
                    </span> -->
                <?php endif; ?>
            </p> 
            <select 
                class="form-control floating-select" 
                id="width_id" 
                name="width_id" 
                placeholder=" "
                onchange="validate_dropdown(this)"  
                tabindex= "<?php echo $tabindex++; ?>"
            ></select>
            <small class="form-text text-muted helper-text" id="width_id_msg"></small>
        </div>
         <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
            <input 
                type="number" 
                class="form-control floating-input" 
                id="design_rate" 
                name="design_rate" 
                value="0" 
                placeholder=" " 
                autocomplete="off" 
                tabindex="<?php echo $tabindex++; ?>"
                onkeyup="calculate_design_charges()"
            />   
            <label class="text-uppercase">rate</label>
            <small class="form-text text-muted helper-text" id="design_rate_msg"></small>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
            <input 
                type="number" 
                class="form-control floating-input" 
                id="design_mtr" 
                name="design_mtr" 
                value="0" 
                placeholder=" " 
                autocomplete="off" 
                tabindex="<?php echo $tabindex++; ?>"
                onkeyup="calculate_design_charges()"
            />   
            <label class="text-uppercase">mtr</label>
            <small class="form-text text-muted helper-text" id="design_mtr_msg"></small>
        </div>
        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
            <input 
                type="number" 
                class="form-control floating-input" 
                id="design_amt" 
                name="design_amt" 
                value="0" 
                placeholder=" " 
                autocomplete="off" 
                tabindex="<?php echo $tabindex++; ?>"
                readonly
            />   
            <label class="text-uppercase">amt</label>
            <small class="form-text text-muted helper-text" id="design_amt_msg"></small>
        </div>        
        <div class="col-12 col-sm-12 col-md-6 col-lg-6">
            <button 
                type="button" 
                class="btn btn-md btn-block btn-primary" 
                id="add_row_btn"
                data-toggle="tooltip" 
                title="ADD DESIGN" 
                data-placement="top" 
                tabindex= "<?php echo $tabindex++; ?>"
                onclick="add_design_transaction()"   
            ><i class="text-success fa fa-plus"></i></button>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-8 border d-flex" id="wrapper_design" style="overflow-y: auto;"></div>
</div>