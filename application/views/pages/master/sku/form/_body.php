<?php
    $checked    = !empty($master_data) && $master_data[0]['sku_status'] == 0 ? '' : 'checked'; 
    $id         = empty($master_data) ? 0 : $master_data[0]['sku_id'];
    $uuid       = empty($master_data) ? $sku_uuid : $master_data[0]['sku_uuid'];
    $tabindex   = 1;
?>
<div class="row"> 
    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="card-header text-uppercase">sku image</div>
            <div class="card-body p-0">
                <div class="form-group floating-form d-flex flex-wrap">
                    <div class="d-none">
                        <input 
                            type="hidden" 
                            id="sku_uuid" 
                            name="sku_uuid" 
                            value="<?php echo $uuid; ?>"
                        />
                        <input 
                            type="hidden" 
                            id="id" 
                            name="id" 
                            value="<?php echo $id; ?>"
                        />
                        <input 
                            type="hidden" 
                            id="sdt_id" 
                            name="sdt_id" 
                            value="0"
                        />
                        <input 
                            type="hidden" 
                            id="sdyt_id" 
                            name="sdyt_id" 
                            value="0"
                        />
                        <input 
                            type="hidden" 
                            id="skt_id" 
                            name="skt_id" 
                            value="0"
                        />
                        <input 
                            type="hidden" 
                            id="set_id" 
                            name="set_id" 
                            value="0"
                        />
                        <input 
                            type="hidden" 
                            id="sot_id" 
                            name="sot_id" 
                            value="0"
                        />
                        <input 
                            type="hidden" 
                            id="sku_mtr" 
                            name="sku_mtr" 
                            value="<?php echo empty($master_data) ? 0 : $master_data[0]['sku_mtr']; ?>" 
                        />
                        <input 
                            type="hidden" 
                            id="sku_rate" 
                            name="sku_rate" 
                            value="<?php echo empty($master_data) ? 0 : $master_data[0]['sku_rate']; ?>" 
                        />
                        <input 
                            type="hidden" 
                            id="sku_pic" 
                            name="sku_pic" 
                            value="<?php echo empty($master_data) ? assets(NOIMAGE) : $master_data[0]['sku_image']; ?>"
                        />
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 d-flex flex-wrap">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                            <span class="d-flex justify-content-center mb-3" id="preview" style="width: 13rem; height:15rem;">
                                <img 
                                    class="img-thumbnail pan form_loading" 
                                    onclick="zoom(this)" 
                                    title="click to zoom in and zoom out" 
                                    src="<?php echo assets(LAZYLOADING) ?>" 
                                    data-src="<?php echo empty($master_data) ? assets(NOIMAGE) : $master_data[0]['sku_image']; ?>" 
                                    data-big="<?php echo empty($master_data) ? assets(NOIMAGE) : $master_data[0]['sku_image']; ?>" 
                                    style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                />
                            </span>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-6">
                            <div class="d-flex flex-column align-items-center p-2">
                                <label class="text-uppercase"> <small class="text-danger font-weight-bold">(.jpg, .jpeg,) only</small></label>
                                <div class="d-flex flex-column">
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <input 
                                            type="file"  
                                            id="sku_photo" 
                                            name="sku_photo" 
                                            class="form-control floating-input mb-3" 
                                            onchange="preview_image(this)"
                                            tabindex="<?php echo $tabindex++; ?>"
                                            accept="image/*"
                                        />
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-block btn-primary mb-3" 
                                            onclick="remove_sku_image()"
                                            tabindex="<?php echo $tabindex++; ?>"
                                        >REMOVE <i class="text-danger fa fa-trash"></i></button>
                                    </div>
                                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <input 
                                            type="checkbox" 
                                            id="sku_status" 
                                            name="sku_status" 
                                            data-toggle="toggle" 
                                            data-on="ACTIVE" 
                                            data-off="INACTIVE" 
                                            data-onstyle="primary" 
                                            data-offstyle="primary" 
                                            data-width="120" 
                                            data-size="normal" 
                                            tabindex="<?php echo $tabindex++; ?>"
                                            <?php echo $checked ?>
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="card-header text-uppercase">general detail</div>
            <div class="card-body p-0"> 
                <div class="form-group floating-form d-flex flex-wrap mt-4">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                        <p class="text-uppercase">company Name <span class="text-danger">*</span></p>
                        <select 
                            class="form-control floating-select" 
                            id="sku_customer_id" 
                            name="sku_customer_id" 
                            placeholder=" " 
                            tabindex="<?php echo $tabindex++; ?>"
                            onkeyup="validate_dropdown(this, true)">
                            <?php if(!empty($master_data) && !empty($master_data[0]['sku_customer_id'])): ?>
                                <option value="<?php echo $master_data[0]['sku_customer_id'] ?>" selected>
                                    <?php echo $master_data[0]['customer_name']; ?> 
                                </option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted helper-text" id="sku_customer_id_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                        <p class="text-uppercase">department<span class="text-danger">*</span></p>
                        <select 
                            class="form-control floating-select" 
                            id="sku_department_id" 
                            name="sku_department_id" 
                            placeholder=" " 
                            tabindex="<?php echo $tabindex++; ?>"
                            onkeyup="validate_dropdown(this, true)">
                            <?php if(!empty($master_data) && !empty($master_data[0]['sku_department_id'])): ?>
                                <option value="<?php echo $master_data[0]['sku_department_id'] ?>" selected>
                                    <?php echo $master_data[0]['department_name']; ?> 
                                </option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted helper-text" id="sku_department_id_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                        <p class="text-uppercase">apparel <span class="text-danger">*</span></p>
                        <select 
                            class="form-control floating-select" 
                            id="sku_apparel_id" 
                            name="sku_apparel_id" 
                            placeholder=" " 
                            tabindex="<?php echo $tabindex++; ?>"
                            onkeyup="validate_dropdown(this, true)"
                        >
                            <?php if(!empty($master_data) && !empty($master_data[0]['sku_apparel_id'])): ?>
                                <option value="<?php echo $master_data[0]['sku_apparel_id'] ?>" selected>
                                    <?php echo $master_data[0]['apparel_name']; ?> 
                                </option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted helper-text" id="sku_apparel_id_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                        <input 
                            type="text" 
                            class="form-control floating-input" 
                            id="sku_name" 
                            name="sku_name" 
                            value="<?php echo empty($master_data) ? '' : $master_data[0]['sku_name']; ?>" 
                            onkeyup="validate_textfield(this, true)"
                            placeholder=" " 
                            autocomplete="off" 
                            tabindex="<?php echo $tabindex++; ?>"
                        />   
                        <label class="text-uppercase">name <span class="text-danger">*</span></label>
                        <small class="form-text text-muted helper-text" id="sku_name_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_mrp" 
                            name="sku_mrp" 
                            value="<?php echo empty($master_data) ? '' : $master_data[0]['sku_mrp']; ?>" 
                            onkeyup="validate_textfield(this, true)"
                            placeholder=" " 
                            autocomplete="off" 
                            tabindex="<?php echo $tabindex++; ?>"
                        />   
                        <label class="text-uppercase">mrp</label>
                        <small class="form-text text-muted helper-text" id="sku_mrp_msg"></small>
                    </div>       
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                        <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="sku_piece" 
                            name="sku_piece" 
                            value="<?php echo empty($master_data) ? '' : $master_data[0]['sku_piece']; ?>" 
                            onkeyup="validate_textfield(this, true)"
                            placeholder=" " 
                            autocomplete="off" 
                            tabindex="<?php echo $tabindex++; ?>"
                        />   
                        <label class="text-uppercase">no. of pieces <span class="text-danger">*</span></label>
                        <small class="form-text text-muted helper-text" id="sku_piece_msg"></small>
                    </div>       
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                        <p class="text-uppercase">color</p>
                        <select 
                            class="form-control floating-select" 
                            id="sku_color_id" 
                            name="sku_color_id" 
                            placeholder=" " 
                            tabindex="<?php echo $tabindex++; ?>"
                        >
                            <?php if(!empty($master_data) && !empty($master_data[0]['sku_color_id'])): ?>
                                <option value="<?php echo $master_data[0]['sku_color_id'] ?>" selected>
                                    <?php echo $master_data[0]['color_name']; ?> 
                                </option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted helper-text" id="sku_color_id_msg"></small>
                    </div>     
                    <div class="col-12 col-sm-12 col-md-12 col-lg-6 floating-label">
                        <textarea
                            class="form-control floating-textarea"
                            id="sku_notes"
                            name="sku_notes"
                            placeholder=" "
                            autocomplete="off"
                            tabindex= "<?php echo $tabindex++; ?>"
                        ><?php echo empty($master_data) ? '' : $master_data[0]['sku_notes']; ?></textarea>
                        <label class="text-uppercase">notes</label>
                        <small class="form-text text-muted helper-text d-none" id="sku_notes_msg"></small>
                    </div> 
                </div> 
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-4 col-lg-4">
        <div class="card">
            <div class="card-header text-uppercase d-none">amount detail</div>
            <div class="card-body p-0">
                <table class="table table-sm text-uppercase border font-weight-bold">
                    <thead class="table-dark">
                        <tr>
                            <th width="60%">description</th>
                            <th width="20%">mtr</th>
                            <th width="20%">amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>fabric consumption</td>
                            <td id="total_design_mtr">0</td>
                            <td id="total_design_amt">0</td>
                        </tr>
                        <tr>
                            <td>dying consumption</td>
                            <td id="total_dying_mtr">0</td>
                            <td id="total_dying_amt">0</td>
                        </tr>
                        <tr>
                            <td>karigar charges</td>
                            <td id="total_karigar_mtr">0</td>
                            <td id="total_karigar_amt">0</td>
                        </tr>
                        <tr>
                            <td>embroidery charges</td>
                            <td id="total_embroidery_mtr">0</td>
                            <td id="total_embroidery_amt">0</td>
                        </tr>
                        <tr>
                            <td>other charges</td>
                            <td id="total_other_mtr">0</td>
                            <td id="total_other_amt">0</td>
                        </tr>
                    </tbody>
                    <thead class="table-dark">
                        <tr>
                            <th >total</th>
                            <th id="total_mtr">0</th>
                            <th id="total_amt">0</th>
                        </tr>
                    </thead>
                </table>
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
                            id="design_tab" 
                            data-toggle="tab"
                            href="#design_content" 
                            role="tab" 
                            aria-controls="design_content" 
                            aria-selected="true"
                            style="font-size:0.8rem;"
                        >fabric consumption (<span id="design_count">0</span>)</a>
                    </li>
                    <li class="nav-item">
                        <a 
                            class="nav-link text-uppercase" 
                            id="dying_tab" 
                            data-toggle="tab"
                            href="#dying_content" 
                            role="tab" 
                            aria-controls="dying_content" 
                            aria-selected="false"
                            style="font-size:0.8rem;"
                        >dying consumption (<span id="dying_count">0</span>)</a>
                    </li>
                    <li class="nav-item">
                        <a 
                            class="nav-link text-uppercase" 
                            id="karigar_tab" 
                            data-toggle="tab"
                            href="#karigar_content" 
                            role="tab" 
                            aria-controls="karigar_content" 
                            aria-selected="false"
                            style="font-size:0.8rem;"
                        >karigar charges (<span id="karigar_count">0</span>)</a>
                    </li>
                    <li class="nav-item">
                        <a 
                            class="nav-link text-uppercase" 
                            id="embroidery_tab" 
                            data-toggle="tab"
                            href="#embroidery_content" 
                            role="tab" 
                            aria-controls="embroidery_content" 
                            aria-selected="false"
                            style="font-size:0.8rem;"
                        >embroidery charges (<span id="embroidery_count">0</span>)</a>
                    </li>
                    <li class="nav-item">
                        <a 
                            class="nav-link text-uppercase" 
                            id="other_tab" 
                            data-toggle="tab"
                            href="#other_content" 
                            role="tab" 
                            aria-controls="other_content" 
                            aria-selected="false"
                            style="font-size:0.8rem;"
                        >other charges (<span id="other_count">0</span>)</a>
                    </li>
                    <li class="nav-item">
                        <a 
                            class="nav-link text-uppercase" 
                            id="image_tab" 
                            data-toggle="tab"
                            href="#image_content" 
                            role="tab" 
                            aria-controls="image_content" 
                            aria-selected="false"
                            style="font-size:0.8rem;"
                        >images (<span id="image_count">0</span>)</a>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="pills-tabContent">
                    <div 
                        class="tab-pane fade show active" 
                        id="design_content" 
                        role="tabpanel" 
                        aria-labelledby="design_tab"
                    ><?php $this->load->view("pages/$menu/$sub_menu/form/design", ['tabindex' => $tabindex]) ?></div>
                    <div 
                        class="tab-pane fade" 
                        id="dying_content" 
                        role="tabpanel" 
                        aria-labelledby="dying_tab"
                    ><?php $this->load->view("pages/$menu/$sub_menu/form/dying", ['tabindex' => $tabindex]) ?></div>
                    <div 
                        class="tab-pane fade" 
                        id="karigar_content" 
                        role="tabpanel" 
                        aria-labelledby="karigar_tab"
                    ><?php $this->load->view("pages/$menu/$sub_menu/form/karigar", ['tabindex' => $tabindex]) ?></div>
                    <div 
                        class="tab-pane fade" 
                        id="embroidery_content" 
                        role="tabpanel" 
                        aria-labelledby="embroidery_tab"
                    ><?php $this->load->view("pages/$menu/$sub_menu/form/embroidery", ['tabindex' => $tabindex]) ?></div>
                    <div 
                        class="tab-pane fade" 
                        id="other_content" 
                        role="tabpanel" 
                        aria-labelledby="other_tab"
                    ><?php $this->load->view("pages/$menu/$sub_menu/form/other", ['tabindex' => $tabindex]) ?></div>
                    <div 
                        class="tab-pane fade" 
                        id="image_content" 
                        role="tabpanel" 
                        aria-labelledby="image_tab"
                    ><?php $this->load->view("pages/$menu/$sub_menu/form/image", ['tabindex' => $tabindex]) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>