const get_style_table = (data) => {
  let div = `<p class="text-center text-uppercase text-danger w-100">no style added !!!</p>`;
  if (data && data.length != 0) {
      div = "";
      data.forEach((row) => {
      const { cst_id, cst_style_id, cst_style_name, cst_value, cst_ot_id, cst_apparel_id } = row;
      const unique_id = `${cst_ot_id}_${cst_apparel_id}_${cst_style_id}`;
      div += `<div class="col-12 col-sm-12 col-md-3 col-lg-2 mt-4">
                <label class="custom-control material-checkbox">
                    <input 
                      type="checkbox" 
                      class="material-control-input advance_checkboxes" 
                      id="cst_value_${unique_id}" 
                      name="cst_value[${unique_id}]" 
                      value="${cst_style_id}" 
                      ${cst_value == 1 ? "checked" : ""}
                    />
                    <span class="material-control-indicator"></span>
                    <span class="material-control-description">${cst_style_name}</span>
                </label>
              </div>`;
      });
  }
  return div;
};
const style_popup = (apparel_id, apparel_name, wrapper = 'style_wrapper') => {
    let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                      <p class="text-uppercase text-center font-weight-bold">add style to ${apparel_name}</p>
                  </div>`;
    let body = `<form class="form-horizontal" id="style_form" onsubmit="add_style('${wrapper}')">              
                  <div class="row pt-1">
                    <div class="col-12">
                      <div class="d-flex flex-wrap form-group floating-form">
                        <div class="col-12 floating-label">
                          <input 
                            type="text" 
                            name="style_name" 
                            class="form-control floating-input" 
                            id="style_name" 
                            onkeyup="validate_textfield(this, ${true})" 
                            placeholder=" " 
                            autocomplete="off" 
                          />   
                          <label class="text-uppercase">style <span class="text-danger">*</span></label>
                          <small class="form-text text-muted helper-text" id="style_name_msg"></small>
                        </div>
                        <div class="col-12 floating-label">
                          <input 
                            type="text" 
                            name="style_group" 
                            class="form-control floating-input" 
                            id="style_group" 
                            placeholder=" " 
                            autocomplete="off" 
                          />  
                          <input type="hidden" name="style_status" value="1"> 
                          <input type="hidden" name="apparel_id" value="${apparel_id}"> 
                          <label class="text-uppercase">group</label>
                          <small class="form-text text-muted helper-text" id="style_group_msg"></small>
                        </div>                     
                      </div>              
                    </div>              
                  </div>              
                </form>`;
    let footer = `<button 
                    type="button" 
                    class="btn btn-sm btn-primary" 
                    id="sbt_btn" 
                    onclick="add_style('${wrapper}')" 
                    style="width:15%;"
                  >
                      <div class="stage d-none"><div class="dot-flashing"></div></div>
                      <div class="dot-text text-primary text-uppercase">add</div>
                  </button>
                  <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>`;

    $(".modal-title-sm").html(title);
    $(".modal-body-sm").html(body);
    $(".modal-footer-sm").html(footer);
    $("#popup_modal_sm").modal("show");
    setTimeout(() => {
      $(`#style_name`).focus();
    }, RELOAD_TIME);
};
const add_style = (wrapper) => {
  event.preventDefault();
  let check = true;
  notifier("style_name");
  if ($(`#style_name`).val() == "") {
      notifier(`style_name`, "Required");
      check = false;
  }
  if (!check) {
      toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
      });
      return false;
  }

  const path = `${link}/${sub_link}/handler/`;
  let form_data = $(`#style_form`).serialize();
  form_data += `&func=add_style&id=0`;
  ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
      if (handle_response(resp)) {
          const { data, msg } = resp;
          toastr.success("", msg, { closeButton: true, progressBar: true });
          $("#popup_modal_sm").modal("hide");
          if (data && data.length != 0) {
          let style_table = get_style_table(data);
          $(`#${wrapper}`).prepend(style_table);
          }
      }
      },
      (errmsg) => {}
  );
};