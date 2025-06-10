<?php 
    $id             = empty($master_data) ? $_GET['id'] : $master_data[0]['ot_id'];
    $tabindex       = 1;  
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
                        <div class="card"> 
                            <div class="card-body p-0">
                                <div class="tab-content" id="pills-tabContent">
                                <input 
                                    type="hidden" 
                                    id="ot_id" 
                                    name="ot_id" 
                                    value="<?php echo $id; ?>"/>
                                 <input 
                                    type="hidden" 
                                    id="oet_id" 
                                    name="oet_id" 
                                    value="0"/>    
                                    <div class="d-flex flex-wrap">
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 ">
                                            <div class="d-flex form-group pt-4" style="overflow-y: auto;">
                                                <div class="col-12 col-sm-12 col-md-4 col-lg-3">
                                                    <div class="d-flex flex-wrap floating-form">
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                                            <p class="text-uppercase">employee&nbsp;code</p> 
                                                            <input 
                                                                type="text" 
                                                                class="form-control floating-input" 
                                                                id="emp_code" 
                                                                name="emp_code" 
                                                                placeholder="" 
                                                                autocomplete="off" 
                                                                tabindex= "<?php echo $tabindex++; ?>"/>
                                                            <small class="form-text text-muted helper-text" id="emp_code_msg"></small>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                                            <p class="text-uppercase">employee&nbsp;Name<span class="text-danger">*</span></p> 
                                                            <input 
                                                                type="text" 
                                                                class="form-control floating-input" 
                                                                id="emp_name" 
                                                                name="emp_name" 
                                                                placeholder="" 
                                                                autocomplete="off" 
                                                                tabindex= "<?php echo $tabindex++; ?>"/>
                                                            <small class="form-text text-muted helper-text" id="emp_name_msg"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-4 col-lg-3">
                                                    <div class="d-flex flex-wrap floating-form">
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                                            <p class="text-uppercase">Mobile</p> 
                                                            <input 
                                                                type="number" 
                                                                class="form-control floating-input" 
                                                                id="emp_mobile" 
                                                                name="emp_mobile"
                                                                onfocusout="validate_mobile_no(this)"
                                                                placeholder="" 
                                                                autocomplete="off" 
                                                                tabindex= "<?php echo $tabindex++; ?>"/>
                                                            <small class="form-text text-muted helper-text" id="emp_mobile_msg"></small>
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                                            <p class="text-uppercase">Email</p> 
                                                            <input 
                                                                type="text" 
                                                                class="form-control floating-input" 
                                                                id="emp_email" 
                                                                name="emp_email"
                                                                onkeyup="validate_email(this)"  
                                                                placeholder="" 
                                                                autocomplete="off" 
                                                                tabindex= "<?php echo $tabindex++; ?>"/>
                                                            <small class="form-text text-muted helper-text" id="emp_email_msg"></small>
                                                        </div>
                                                    </div>
                                                </div>
                                               <div class="col-12 col-sm-12 col-md-4 col-lg-3">
                                                    <div class="d-flex flex-wrap floating-form">
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                                             <p class="text-uppercase">designation&nbsp;</p> 
                                                                <select 
                                                                    class="form-control floating-select" 
                                                                    id="emp_designation_id" 
                                                                    name="emp_designation_id" 
                                                                    placeholder=" "
                                                                    onchange="validate_dropdown(this)"  
                                                                    tabindex= "<?php echo $tabindex++; ?>"
                                                                ></select>
                                                                
                                                        </div>
                                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                                        <p class="text-uppercase">description</p> 
                                                        <input 
                                                            type="text" 
                                                            class="form-control floating-input" 
                                                            id="emp
                                                            cription"
                                                            name="emp_description"
                                                            value=""
                                                            placeholder="" 
                                                            autocomplete="off" 
                                                            tabindex= "<?php echo $tabindex++; ?>"
                                                        />
                                                        <small class="form-text text-muted helper-text" id="emp_description_msg"></small>
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
                                                            title="ADD EMPLOYEE" 
                                                            data-placement="top" 
                                                            tabindex= "<?php echo $tabindex++; ?>"
                                                            onclick="add_emp_transaction()"   
                                                        ><i class="text-success fa fa-plus"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="card mb-3">
                                                <div id="added_employee_list_tab" class="collapse show" aria-labelledby="added_employee_list_tabs" data-parent="#accordion">
                                                    <div class="card-body p-0" style="max-width:100vw; max-height:50vh; overflow:auto;" id="div_wrapper">
                                                        <table class="table table-sm table-reponsive table-hover text-uppercase">
                                                            <tbody class="table-dark border-0">
                                                                <tr style="font-weight:bold; font-size: 0.8rem;">
                                                                    <td class="border-bottom border-top-0" >emp&nbsp;code&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                    <td class="border-bottom border-top-0" >name&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                    <td class="border-bottom border-top-0" >mobile&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                    <td class="border-bottom border-top-0" >email&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                    <td class="border-bottom border-top-0" >Designation&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                    <td class="border-bottom border-top-0" >action&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                                                </tr>
                                                            </tbody>
                                                            <tbody id="employee_transaction_wrapper" style="font-weight: bold; font-size: 0.8rem;"></tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>                         
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="top-panel-wrapper" id="measurement_wrapper"><?php $this->load->view('pages/component/panel/_top'); ?></div>