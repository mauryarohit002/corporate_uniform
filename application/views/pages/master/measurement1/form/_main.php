<?php
    $maap_action    = get_action_data('master', 'maap');
    $style_action   = get_action_data('master', 'style');

    $checked        = !empty($master_data) && $master_data[0]['measurement_status'] == 0 ? '' : 'checked'; 
    $id             = empty($master_data) ? 0 : $master_data[0]['measurement_id'];
    $tabindex       = 1;
?>
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header text-uppercase">apparel detail</div>
            <div class="card-body">
                <div class="d-flex flex-wrap form-group floating-form">
                    <div class="col-12 col-sm-12 col-md-8 col-lg-8 floating-label">
                        <input 
                            type="hidden"
                            id="id"
                            name="id"
                            value="<?php echo $id; ?>"
                        />
                        <p class="text-uppercase">apparel <span class="text-danger">*</span></p>
                        <select
                            class="form-control floating-select"
                            id="measurement_apparel_id"
                            name="measurement_apparel_id"
                            tabindex="<?php echo $tabindex++; ?>"
                        >
                            <?php if(!empty($master_data)): ?>
                                <option value="<?php echo $master_data[0]['measurement_apparel_id'] ?>"><?php echo $master_data[0]['apparel_name']; ?></option>
                            <?php endif; ?>
                        </select>
                        <small class="form-text text-muted helper-text" id="measurement_apparel_id_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-2 col-lg-2 floating-label">
                        <input 
                            type="checkbox" 
                            id="measurement_status" 
                            name="measurement_status" 
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
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header text-uppercase">copy maap & style</div>
            <div class="card-body">
                <div class="d-flex flex-wrap form-group floating-form">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                        <p class="text-uppercase">copy maap</p>
                        <select
                            class="form-control floating-select"
                            id="mmt_maap_id"
                            name="mmt_maap_id"
                            placeholder=" " 
                            tabindex= "<?php echo $tabindex++; ?>"
                        ></select>
                        <small class="form-text text-muted helper-text" id="mmt_maap_id_msg"></small>
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                        <p class="text-uppercase">copy style</p>
                        <select
                            class="form-control floating-select"
                            id="mst_style_id"
                            name="mst_style_id"
                            placeholder=" " 
                            tabindex= "<?php echo $tabindex++; ?>"
                        ></select>
                        <small class="form-text text-muted helper-text" id="mst_style_id_msg"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header text-uppercase">maap detail</div>
            <div class="card-body">
                <div class="d-flex flex-wrap form-group floating-form">
                    <table class="table table-sm mb-2">
                        <tbody>
                            <tr class="floating-form">
                                <td class="floating-label border-0" width="75%">
                                    <p class="text-uppercase">maap&nbsp;
                                        <?php if(in_array('add', $maap_action)): ?>
                                            <span>
                                                <a
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="ADD MAAP"
                                                    style="cursor: pointer;"
                                                    onclick='popup(<?php echo json_encode(["sub_menu" => "maap", "field" => "maap_id"]) ?>)'
                                                ><i class="fa fa-plus"></i></a>
                                            </span>
                                        <?php endif; ?>
                                    </p> 
                                    <select
                                        class="form-control floating-select"
                                        id="maap_id"
                                        tabindex="<?php echo $tabindex++; ?>"
                                    ></select>
                                    
                                </td> 
                                <td class="border-0" width="5%">
                                    <button 
                                        type="button" 
                                        class="btn btn-md btn-primary" 
                                        data-toggle="tooltip" 
                                        title="ADD MAAP" 
                                        data-placement="top" 
                                        tabindex="<?php echo $tabindex++; ?>"
                                        onclick="add_maap()"   
                                    ><i class="text-success fa fa-plus"></i></button>
                                </td>                  
                                <td class="border-0" width="20%">
                                    <small class="form-text text-muted helper-text" id="maap_id_msg"></small>
                                </td> 
                            </tr>
                        </tbody>
                    </table>
                    <div style="width: 100%; max-height: 30vh; overflow-x: auto;">
                        <table class="table table-sm">
                            <tbody id="maap_wrapper"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
        <div class="card">
            <div class="card-header text-uppercase">style detail</div>
            <div class="card-body">
                <div class="d-flex flex-wrap form-group floating-form">
                    <table class="table table-sm mb-2">
                        <tbody>
                            <tr class="floating-form">
                                <td class="floating-label border-0" width="75%">
                                    <p class="text-uppercase">style&nbsp;
                                        <?php if(in_array('add', $style_action)): ?>
                                            <span>
                                                <a
                                                    data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="ADD MAAP"
                                                    style="cursor: pointer;"
                                                    onclick='popup(<?php echo json_encode(["sub_menu" => "style", "field" => "style_id"]) ?>)'
                                                ><i class="fa fa-plus"></i></a>
                                            </span>
                                        <?php endif; ?>
                                    </p> 
                                    <select
                                        class="form-control floating-select"
                                        id="style_id"
                                        tabindex="<?php echo $tabindex++; ?>"
                                    ></select>
                                    
                                </td> 
                                <td class="border-0" width="5%">
                                    <button 
                                        type="button" 
                                        class="btn btn-md btn-primary" 
                                        data-toggle="tooltip" 
                                        title="ADD MAAP" 
                                        data-placement="top" 
                                        tabindex="<?php echo $tabindex++; ?>"
                                        onclick="add_style()"   
                                    ><i class="text-success fa fa-plus"></i></button>
                                </td>                  
                                <td class="border-0" width="20%">
                                    <small class="form-text text-muted helper-text" id="style_id_msg"></small>
                                </td> 
                            </tr>
                        </tbody>
                    </table>
                    <div style="width: 100%; max-height: 30vh; overflow-x: auto;">
                        <table class="table table-sm">
                            <tbody id="style_wrapper"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>