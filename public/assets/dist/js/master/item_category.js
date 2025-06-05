$(document).ready(function () {
  $("#_name")
    .select2(
      select2_default({
        url: `master/item_category/get_select2/_name`,
        placeholder: "name",
      })
    )
    .on("change", () => trigger_search());
});
const set_item_category_field = (id) => {
  const term = "item_category";
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
const item_category_popup = (id, action = "add", field = undefined) => {
  const term = "item_category";
  let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                    <p class="text-uppercase text-center font-weight-bold">${action} ${term.replace(
    "_",
    " "
  )}</p>
                </div>`;
  let data = `<form class="form-horizontal" id="${term}_form" onsubmit="add_update_${term}(${id}, ${field})">              
                    <div class="row pt-1">
                        <div class="col-12">
                            <div class="d-flex flex-wrap form-group floating-form">
                                <div class="col-12 floating-label">
                                    <input type="text" name="${term}_name" class="form-control floating-input" id="${term}_name" onkeyup="validate_textfield(this, ${true})" placeholder=" " autocomplete="off" />   
                                    <label class="text-uppercase">name <span class="text-danger">*</span></label>
                                    <small class="form-text text-muted helper-text" id="${term}_name_msg"></small>
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
    set_item_category_field(id);
  }
  setTimeout(() => {
    $(`#${term}_name`).focus();
  }, RELOAD_TIME);
};
const remove_item_category_notifier = (term) => {
  notifier(`${term}_name`);
};
const add_update_item_category = (id, field) => {
  event.preventDefault();
  const term = "item_category";
  let check = true;
  remove_item_category_notifier(term);
  if ($(`#${term}_name`).val() == "") {
    notifier(`${term}_name`, "Required");
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
              remove_item_category_notifier();
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
const remove_item_category = (data) => {
  let path = `master/item_category/remove/${data.item_category_id}`;
  let html = `<table class="table table-sm table-hover text-uppercase">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold" width="30%" align="right">name : </td>
                            <td width="70%">${data.item_category_name}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold" width="30%" align="right">status : </td>
                            <td width="70%">${
                              data.item_category_status == 1
                                ? "active"
                                : "inactive"
                            }</td>
                        </tr>
                    </tbody>
                </table>`;
  remove_datav2(html, path);
};
