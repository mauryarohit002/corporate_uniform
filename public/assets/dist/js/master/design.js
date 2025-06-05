$(document).ready(function () {});
const set_design_field = (id) => {
  const path = `master/design/handler`; 
  const form_data = { func: "get_data", id };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        if (data && data.length != 0) {
          $(`#design_name`).val(data[0][`design_name`]);
          $(`#design_supplier_id`).html(
            `<option value="${data[0][`design_supplier_id`]}">${
              data[0]["supplier_name"]
            }</option>`
          );
          $(`#design_status`).bootstrapToggle(
            data[0][`design_status`] == 1 ? "on" : "off"
          );
          $(`#design_pic`).val(data[0][`design_image`]);
          $(`#preview`).html(
            `<img 
                class="img-thumbnail pan popup_loading" 
                width="150px" 
                onClick="zoom()" 
                title="click to zoom in and zoom out" 
                src="${LAZYLOADING}" 
                data-src="${data[0][`design_image`]}" 
                data-big="${data[0][`design_image`]}" 
            />`
          );
          $("#popup_modal_sm").modal("show");
          lazy_loading("popup_loading");
        }
      }
    },
    (errmsg) => {}
  );
};
const design_popup = (args) => {
  const { action = "add", id = 0, field = undefined } = args;
  let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                  <p class="text-uppercase text-center font-weight-bold">${action} design</p>
                </div>`;
  let data = `<form class="form-horizontal" id="design_form" onsubmit="add_update_design(${id}, ${field})">              
                <div class="row pt-1">
                    <div class="col-12">
                        <div class="d-flex flex-wrap form-group floating-form">
                            <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                                <input 
                                    type="text" 
                                    class="form-control floating-input" 
                                    id="design_name" 
                                    name="design_name" 
                                    onkeyup="validate_textfield(this, ${true})" 
                                    placeholder=" " 
                                    autocomplete="off" 
                                />   
                                <label class="text-uppercase">design <span class="text-danger">*</span></label>
                                <small class="form-text text-muted helper-text" id="design_name_msg"></small>
                            </div>
                            <div class="col-12 col-sm-12 col-md-12 col-lg-8 floating-label">
                             <p class="text-uppercase">supplier&nbsp;<span class="text-danger">*</span></p>
                                <select 
                                    class="form-control floating-select" 
                                    id="design_supplier_id" 
                                    name="design_supplier_id" 
                                    placeholder=" " 
                                    onkeyup="validate_dropdown(this, ${true})"
                                ></select>
                                <small class="form-text text-muted helper-text" id="design_supplier_id_msg"></small>
                            </div>
                            ${
                              field == undefined
                                ? `<div class="col-12 col-sm-12 col-md-12 col-lg-4 floating-label">
                                      <input 
                                          type="checkbox" 
                                          id="design_status" 
                                          name="design_status" 
                                          data-toggle="toggle" 
                                          data-on="ACTIVE" 
                                          data-off="INACTIVE" 
                                          data-onstyle="primary" 
                                          data-offstyle="primary" 
                                          data-width="100" 
                                          data-size="normal" 
                                          checked
                                      />
                                      </div>`
                                : `<div class="col-12 col-sm-12 col-md-12 col-lg-4 floating-label"><input type="hidden" name="design_status" value="1"></div>`
                            }  
                            <div class="col-sm-12 col-md-12 col-lg-6 floating-label">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="font-weight-bold" style="font-size:10px;">UPLOAD IMAGE</span>
                                    <input type="file" name="design_photo" class="form-control floating-input my-1" id="design_photo" onchange="preview_image(this)"/>   
                                    <small class="text-danger" style="font-size:10px;">(.png, .jpg, .jpeg, .gif) only</small>
                                </div>
                                <button type="button" class="btn btn-sm btn-block btn-primary mt-4" onclick="remove_select_image()">REMOVE</button>   
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-6">
                                <div class="d-flex flex-column align-items-center">
                                    <span id="preview"><img class="img-thumbnail" width="150px" src="${NOIMAGE}"/></span>
                                    <input type="hidden" name="design_pic" id="design_pic" value="${NOIMAGE}" />
                                </div>
                            </div>                  
                        </div>              
                    </div>              
                </div>              
            </form>`;

  let btn = `<button 
              type="button" 
              class="btn btn-sm btn-primary" 
              id="sbt_btn" 
              onclick="add_update_design(${id}, ${field})" 
              style="width:15%;"
              ${id == 0 && "disabled"}
            >
              <div class="stage d-none"><div class="dot-flashing"></div></div>
              <div class="dot-text text-primary text-uppercase">${action}</div>
            </button>
            <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>`;

  $(".modal-title-sm").html(title);
  $(".modal-body-sm").html(data);
  $(".modal-footer-sm").html(action == "read" ? "" : btn);
  if (id == 0) {
    $("#popup_modal_sm").modal("show");
    $(`#design_status`).bootstrapToggle();
    setTimeout(() => {
      $(`#design_name`).focus();
    }, RELOAD_TIME);
  } else {
    set_design_field(id);
    setTimeout(() => {
      $(`#pincode`).focus();
    }, RELOAD_TIME);
  } 

  $("#design_supplier_id").select2(
    select2_default({
      url: `master/supplier/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );

   
  if(field =='design_id')
  {
    var supp_name=$('#pm_supplier_id').text();
    var supp_val=$('#pm_supplier_id').val();
    if(supp_val > 0 && supp_val !='undefined'){
      $(`#design_supplier_id`).html(
            `<option value="${supp_val}">${supp_name}</option>`);
    }
    var supp_name1=$('#prmm_supplier_id').text();
    var supp_val1=$('#prmm_supplier_id').val();
    if(supp_val1 > 0 && supp_val1 !='undefined'){
      $(`#design_supplier_id`).html(
            `<option value="${supp_val1}">${supp_name1}</option>`);
    }
  }
};
const remove_design_notifier = () => {
  notifier(`design_name`);
  notifier(`design_supplier_id`);
};
const add_update_design = (id, field) => {
  event.preventDefault();
  let check = true;
  remove_design_notifier();
  if ($(`#design_name`).val() == "") {
    notifier(`design_name`, "Required");
    check = false;
  }
  if ($(`#design_supplier_id`).val() == null) {
    notifier(`design_supplier_id`, "Required");
    check = false;
  }
  if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    $("body, html").animate({ scrollTop: 0 }, 1000);
  } else {
    compress_image("design_photo")
      .then((compressedImage) => {
        const path = `master/design/handler`;
        let form_id = document.getElementById(`design_form`);
        let form_data = new FormData(form_id);
        form_data.append("func", "add_update");
        form_data.append("id", id);
        if (compressedImage.name) {
          form_data.append(
            `design_photo`,
            compressedImage,
            compressedImage.name
          );
        }
        fileUpAjaxCall(
          "POST",
          path,
          form_data,
          "JSON",
          (resp) => {
            if (handle_response(resp)) {
              const { data, msg } = resp;
              if (id == 0) {
                if (field != undefined) {
                  $("#popup_modal_sm").modal("hide");
                  refresh_dropdown_select2(data, field);
                } else {
                  $("#design_form")[0].reset();
                  $("#preview").html(
                    `<img class="img-thumbnail" width="150px" src="${NOIMAGE}"/>`
                  );
                  $("#design_pic").val(NOIMAGE);
                  $(`#design_name`).val("").focus();
                  remove_design_notifier();
                }
              } else {
                $("#popup_modal_sm").modal("hide");
              }
              toastr.success("", msg, { closeButton: true, progressBar: true });
              $("body, html").animate({ scrollTop: 0 }, 1000);
            }
          },
          (errmsg) => {}
        );
      })
      .catch((err) => {
        console.log(err);
      });
  }
};
const design_remove = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.design_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                <tbody>
                    <tr>
                        <td class="font-weight-bold" width="30%" align="right">design : </td>
                        <td width="70%">${data.design_name}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" width="30%" align="right">supplier : </td>
                        <td width="70%">${data.supplier_name}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" width="30%" align="right">STATUS : </td>
                        <td width="70%">
                        ${data.design_status == 1 ? "active" : "inactive"}
                        </td>
                    </tr>
                </tbody>
            </table>`;
  remove_datav3({ path, form_data, html });
};
const remove_select_image = () => {
  $("#design_photo").val("");
  $("#design_pic").val(NOIMAGE);
  $("#preview").html(
    `<img class="img-thumbnail" width="150px" src="${NOIMAGE}" />`
  );
};
