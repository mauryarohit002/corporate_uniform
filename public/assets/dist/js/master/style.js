$(document).ready(function () {});
const set_style_field = (id) => {
  const path = `master/style/handler`;
  const form_data = { func: "get_data", id };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        if (data) {
          $(`#style_name`).val(data[0][`style_name`]);
          $(`#style_group`).val(data[0][`style_group`]);
          $(`#style_status`).bootstrapToggle(
            data[0][`style_status`] == 1 ? "on" : "off"
          );
          $("#popup_modal_sm").modal("show");
        }
      }
    },
    (errmsg) => {}
  );
};
const style_popup = (args) => {
  const { action = "add", id = 0, field = undefined } = args;
  let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                      <p class="text-uppercase text-center font-weight-bold">${action} style</p>
                  </div>`;
  let data = `<form class="form-horizontal" id="style_form" onsubmit="add_update_style(${id}, ${field})">              
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
                        <label class="text-uppercase">group</label>
                        <small class="form-text text-muted helper-text" id="style_group_msg"></small>
                      </div>
                      ${
                        field == undefined
                          ? `<div class="col-12 floating-label">
                                <input type="checkbox" id="style_status" name="style_status" data-toggle="toggle" data-on="ACTIVE" data-off="INACTIVE" data-onstyle="primary" data-offstyle="primary" data-width="100" data-size="normal" checked>
                            </div>`
                          : `<input type="hidden" name="style_status" value="1">`
                      }                           
                    </div>              
                  </div>              
                </div>              
              </form>`;
  let btn = `<button 
                type="button" 
                class="btn btn-sm btn-primary" 
                id="sbt_btn" 
                onclick="add_update_style(${id}, ${field})" 
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
    $(`#style_status`).bootstrapToggle();
  } else {
    set_style_field(id);
  }
  setTimeout(() => {
    $(`#style_name`).focus();
  }, RELOAD_TIME);
};
const remove_style_notifier = () => {
  notifier(`style_name`);
  notifier(`style_group`);
};
const add_update_style = (id, field) => {
  event.preventDefault();
  let check = true;
  remove_style_notifier();
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
    $("body, html").animate({ scrollTop: 0 }, 1000);
  } else {
    const path = `master/style/handler/${id}`;
    let form_data = $(`#style_form`).serialize();
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
              $(`#style_form`)[0].reset();
              $(`#style_name`).focus();
              remove_style_notifier();
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
const style_remove = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.style_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                <tbody>
                    <tr>
                        <td class="font-weight-bold" width="30%" align="right">style : </td>
                        <td width="70%">${data.style_name}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" width="30%" align="right">group : </td>
                        <td width="70%">${data.style_group}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" width="30%" align="right">status : </td>
                        <td width="70%">
                          ${data.style_status == 1 ? "active" : "inactive"}
                        </td>
                    </tr>
                </tbody>
            </table>`;
  remove_datav3({ path, form_data, html });
};
