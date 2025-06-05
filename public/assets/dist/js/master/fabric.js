$(document).ready(function () {});
const set_fabric_field = (id) => {
  const path = `master/fabric/handler`;
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
          $(`#fabric_name`).val(data[0][`fabric_name`]);
          $(`#fabric_sgst_per`).val(data[0][`fabric_sgst_per`]);
          $(`#fabric_cgst_per`).val(data[0][`fabric_cgst_per`]);
          $(`#fabric_igst_per`).val(data[0][`fabric_igst_per`]);
          $(`#fabric_status`).bootstrapToggle(
            data[0][`fabric_status`] == 1 ? "on" : "off"
          );
          $("#popup_modal_sm").modal("show");
        }
      }
    },
    (errmsg) => {}
  );
};
const fabric_popup = (args) => {
  const { action = "add", id = 0, field = undefined } = args;
  let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <p class="text-uppercase text-center font-weight-bold">${action} fabric</p>
                </div>`;
  let data = `<form class="form-horizontal" id="fabric_form" onsubmit="add_update_fabric( ${id}, ${field})">              
                  <div class="row pt-1">
                    <div class="col-12">
                      <div class="d-flex flex-wrap form-group floating-form">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                          <input 
                            type="text" 
                            class="form-control floating-input" 
                            id="fabric_name" 
                            name="fabric_name" 
                            onkeyup="validate_textfield(this, ${true})" 
                            placeholder=" " 
                            autocomplete="off" 
                          />   
                          <label class="text-uppercase">fabric <span class="text-danger">*</span></label>
                          <small class="form-text text-muted helper-text" id="fabric_name_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                          <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="fabric_sgst_per" 
                            name="fabric_sgst_per" 
                            value="0.00"
                            onkeyup="calculate_gst()" 
                            placeholder=" " 
                            autocomplete="off" 
                          />   
                          <label class="text-uppercase">sgst % <span class="text-danger">*</span></label>
                          <small class="form-text text-muted helper-text" id="fabric_sgst_per_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                          <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="fabric_cgst_per" 
                            name="fabric_cgst_per" 
                            value="0.00"
                            readonly
                          />   
                          <label class="text-uppercase">cgst % <span class="text-danger">*</span></label>
                          <small class="form-text text-muted helper-text" id="fabric_cgst_per_msg"></small>
                        </div>
                        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label">
                          <input 
                            type="number" 
                            class="form-control floating-input" 
                            id="fabric_igst_per" 
                            name="fabric_igst_per" 
                            value="0.00"
                            readonly
                          />   
                          <label class="text-uppercase">igst % <span class="text-danger">*</span></label>
                          <small class="form-text text-muted helper-text" id="fabric_igst_per_msg"></small>
                        </div>
                        ${
                          field == undefined
                            ? `<div class="col-4 floating-label">
                                <input 
                                  type="checkbox" 
                                  id="fabric_status" 
                                  name="fabric_status" 
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
                            : `<input type="hidden" name="fabric_status" value="1">`
                        }                            
                      </div>              
                    </div>              
                  </div>              
                </form>`;
  let btn = `<button 
                type="button" 
                class="btn btn-sm btn-primary" 
                id="sbt_btn" 
                onclick="add_update_fabric( ${id}, ${field})" 
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
    $(`#fabric_status`).bootstrapToggle();
  } else {
    set_fabric_field(id);
  }
  setTimeout(() => {
    $(`#fabric_name`).focus();
  }, RELOAD_TIME);
};
const remove_fabric_notifier = () => notifier(`fabric_name`);
const add_update_fabric = (id, field) => {
  event.preventDefault();
  let check = true;
  remove_fabric_notifier(fabric_name);
  if ($(`#fabric_name`).val() == "") {
    notifier(`fabric_name`, "Required");
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
    let path = `master/fabric/handler/${id}`;
    let form_data = $(`#fabric_form`).serialize();
    form_data += `&func=add_update&id=${id}`;
    ajaxCall(
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
              $(`#fabric_form`)[0].reset();
              $(`#fabric_name`).focus();
              remove_fabric_notifier();
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
  }
};
const fabric_remove = (data) => {
  const path = `master/fabric/handler`;
  const form_data = { func: "remove", id: data.fabric_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                  <tbody>
                    <tr>
                        <td class="font-weight-bold text-uppercase" width="30%" align="right">fabric : </td>
                        <td width="70%">${data.fabric_name}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-uppercase" width="30%" align="right">sgst : </td>
                        <td width="70%">${data.fabric_sgst_per}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-uppercase" width="30%" align="right">cgst : </td>
                        <td width="70%">${data.fabric_cgst_per}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-uppercase" width="30%" align="right">igst : </td>
                        <td width="70%">${data.fabric_igst_per}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-uppercase" width="30%" align="right">status : </td>
                        <td width="70%">
                            ${data.fabric_status == 1 ? "active" : "inactive"}
                        </td>
                    </tr>
                  </tbody>
              </table>`;
  remove_datav3({ path, form_data, html });
};

const calculate_gst = () => {
  let sgst_per = $("#fabric_sgst_per").val();
  let igst_per = parseFloat(sgst_per) * 2;
  if (isNaN(igst_per) || igst_per == "") igst_per = 0;
  $("#fabric_cgst_per").val(sgst_per);
  $("#fabric_igst_per").val(igst_per.toFixed(2));
};
