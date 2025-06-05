$(document).ready(function () {
  $("#_name")
    .select2(
      select2_default({
        url: `master/hsn/get_select2/_name`,
        placeholder: "hsn",
      })
    )
    .on("change", () => trigger_search());
  $("#_desc")
    .select2(
      select2_default({
        url: `master/hsn/get_select2/_desc`,
        placeholder: "desc",
      })
    )
    .on("change", () => trigger_search());
  $("#_chapter")
    .select2(
      select2_default({
        url: `master/hsn/get_select2/_chapter`,
        placeholder: "chapter",
      })
    )
    .on("change", () => trigger_search());
});
const set_hsn_field = (id) => {
  const term = "hsn";
  const path = `master/${term}/get_data/${id}`;
  ajaxCall(
    "GET",
    path,
    "",
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        if (data) {
          $(`#${term}_name`).val(data[0][`${term}_name`]);
          $(`#${term}_desc`).val(data[0][`${term}_desc`]);
          $(`#${term}_chapter`).val(data[0][`${term}_chapter`]);
          $(`#${term}_status`).bootstrapToggle(
            data[0][`${term}_status`] == 1 ? "on" : "off"
          );
          $("#popup_modal_sm").modal("show");
        }
      }
    },
    (errmsg) => {}
  );
};
const hsn_popup = (id, action = "add", field = undefined) => {
  const term = "hsn";
  let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <p class="text-uppercase text-center font-weight-bold">${action} ${term}</p>
                </div>`;
  let data = `<form class="form-horizontal" id="${term}_form" onsubmit="add_update_${term}(${id}, ${field})">              
                    <div class="row pt-1">
                        <div class="col-12">
                            <div class="d-flex flex-wrap form-group floating-form">
                                <div class="col-12 floating-label">
                                    <input type="text" name="${term}_name" class="form-control floating-input" id="${term}_name" onkeyup="validate_textfield(this, ${true})" placeholder=" " autocomplete="off" />   
                                    <label class="text-uppercase">${term} <span class="text-danger">*</span></label>
                                    <small class="form-text text-muted helper-text" id="${term}_name_msg"></small>
                                </div>
                                <div class="col-12 floating-label">
                                    <textarea name="${term}_desc" class="form-control floating-textarea" id="${term}_desc" onkeyup="validate_textfield(this, ${true})" placeholder=" " autocomplete="off"></textarea>
                                    <label class="text-uppercase">desc <span class="text-danger">*</span></label>
                                    <small class="form-text text-muted helper-text" id="${term}_desc_msg"></small>
                                </div>
                                <div class="col-12 floating-label">
                                    <input type="text" name="${term}_chapter" class="form-control floating-input" id="${term}_chapter" onkeyup="validate_textfield(this, ${true})" placeholder=" " autocomplete="off" />   
                                    <label class="text-uppercase">chapter <span class="text-danger">*</span></label>
                                    <small class="form-text text-muted helper-text" id="${term}_chapter_msg"></small>
                                </div>
                                ${
                                  field == undefined
                                    ? `<div class="col-12 floating-label">
                                    <input type="checkbox" id="${term}_status" name="${term}_status" data-toggle="toggle" data-on="ACTIVE" data-off="INACTIVE" data-onstyle="primary" data-offstyle="primary" data-width="100" data-size="normal" checked>
                                </div>`
                                    : `<input type="hidden" name="${term}_status" value="1">`
                                }                            
                            </div>              
                        </div>              
                    </div>              
                </form>`;
  let btn = `<button type="button" class="btn btn-sm btn-primary" id="sbt_btn" ${
    id == 0 && "disabled"
  } onclick="add_update_${term}(${id}, ${field})" style="width:15%;">
                    <div class="stage d-none"><div class="dot-flashing"></div></div>
                    <div class="dot-text text-primary text-uppercase">${action}</div>
                </button>
                <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>`;

  $(".modal-title-sm").html(title);
  $(".modal-body-sm").html(data);
  $(".modal-footer-sm").html(action == "read" ? "" : btn);
  if (id == 0) {
    $("#popup_modal_sm").modal("show");
    $(`#${term}_status`).bootstrapToggle();
  } else {
    set_hsn_field(id);
  }
  setTimeout(() => {
    $(`#${term}_name`).focus();
  }, RELOAD_TIME);
};
const remove_hsn_notifier = (term) => {
  notifier(`${term}_name`);
  notifier(`${term}_desc`);
  notifier(`${term}_chapter`);
};
const add_update_hsn = (id, field) => {
  event.preventDefault();
  const term = "hsn";
  let check = true;
  remove_hsn_notifier(term);
  if ($(`#${term}_name`).val() == "") {
    notifier(`${term}_name`, "Required");
    check = false;
  }
  if ($(`#${term}_desc`).val() == "") {
    notifier(`${term}_desc`, "Required");
    check = false;
  }
  if ($(`#${term}_chapter`).val() == "") {
    notifier(`${term}_chapter`, "Required");
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
    let path = `master/${term}/add_update/${id}`;
    let form_data = $(`#${term}_form`).serialize();
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
              $(`#${term}_form`)[0].reset();
              $(`#${term}_name`).focus();
              remove_hsn_notifier();
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
const remove_hsn = (data) => {
  let path = `master/hsn/remove/${data.hsn_id}`;
  let html = `<table class="table table-sm table-hover text-uppercase">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold" width="30%" align="right">hsn : </td>
                            <td width="70%">${data.hsn_name}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" width="30%" align="right">desc : </td>
                            <td width="70%">${data.hsn_desc}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" width="30%" align="right">chapter : </td>
                            <td width="70%">${data.hsn_chapter}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" width="30%" align="right">status : </td>
                            <td width="70%">${
                              data.hsn_status == 1 ? "active" : "inactive"
                            }</td>
                        </tr>
                    </tbody>
                </table>`;
  remove_datav2(html, path);
};
