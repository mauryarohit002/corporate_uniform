$(document).ready(function () {
  // search_functions
  $("#_name")
    .select2(
      select2_default({
        url: `master/item_group/get_select2/_name`,
        placeholder: "name",
      })
    )
    .on("change", () => trigger_search());
  $("#_merchant")
    .select2(
      select2_default({
        url: `master/item_group/get_select2/_merchant`,
        placeholder: "merchant",
      })
    )
    .on("change", () => trigger_search());
  // search_functions

  // form_search_functions
  $(`#item_id`).select2(
    select2_default({
      url: `master/item/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );
  // form_search_functions
});
// core_functions
const get_transaction = () => {
  if (["edit", "read"].includes(get_url_string("action"))) {
    let id = get_url_string("id");
    if (id) {
      let path = `master/item_group/get_transaction/${id}`;
      ajaxCall(
        "GET",
        path,
        "",
        "JSON",
        (resp) => {
          if (handle_response(resp)) {
            const { data, msg } = resp;
            if (data && data.length != 0) {
              data.forEach((row) => add_wrapper_data(row));
            }
            set_serial_no();
          }
        },
        (errmsg) => {}
      );
    }
  }
};
const check_transaction = () => {
  let last_id = 0;
  let flag = true;
  let total_tr = $("#transaction_wrapper > tr").length;
  if (total_tr > 0) {
    for (let i = 1; i <= total_tr; i++) {
      let id = $(`#transaction_wrapper > tr:nth-child(${i})`).attr("id");
      let explode = id.split("_");
      let cnt = explode[1];

      if ($(`#igt_code_${cnt}`).val() == "") {
        $(`#igt_code_${cnt}`).addClass("bg-danger");
        last_id = cnt;
        flag = false;
      } else {
        $(`#igt_code_${cnt}`).removeClass("bg-danger");
      }
    }
  }
  if (!flag) {
    $(window).scrollTop(
      $(`#row_${last_id}`).offset().top - $(window).height() / 2
    );
  }
  return flag;
};
const check_duplicate = (cntCheck) => {
  let total_tr = $("#transaction_wrapper > tr").length;
  let new_code = $(`#igt_code_${cntCheck}`).val();
  for (let i = 1; i <= total_tr; i++) {
    let id = $(`#transaction_wrapper > tr:nth-child(${i})`).attr("id");
    let explode = id.split("_");
    let cnt = explode[1];
    if (cntCheck != cnt) {
      let old_code = $(`#igt_code_${cnt}`).val();

      if (new_code == old_code) {
        $(`#igt_code_${cntCheck}`).addClass("bg-danger");
        $(`#igt_code_${cnt}`).addClass("bg-danger");
        return 0;
      }
    }
  }
  return 1;
};
const add_transaction = (id) => {
  remove_transaction_notifier();
  let check = true;
  let dup_item = true;
  let dup_name = true;
  let total_tr = $("#transaction_wrapper > tr").length;
  if ($("#item_id").val() == null) {
    notifier("item_id", "Required");
    check = false;
  }
  if ($("#code").val() == "") {
    notifier("code", "Required");
    check = false;
  }
  if (total_tr > 0) {
    let new_item_id = $("#item_id").val();
    let new_code = $("#code").val();
    for (let i = 1; i <= total_tr; i++) {
      let cnt = $(`#transaction_wrapper > tr:nth-child(${i})`).attr("id");
      let explode = cnt.split("_");
      let id = explode[1];

      let old_item_id = $(`#igt_item_id_${id}`).val();
      let old_code = $(`#igt_code_${id}`).val();
      if (new_item_id == old_item_id) {
        notifier("item_id", "Already added.");
        dup_item = false;
      }
      if (new_code == old_code) {
        notifier("code", "Already added.");
        dup_name = false;
      }
    }
  }
  if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else if (!dup_item) {
    toastr.error("Duplicate item found!!!", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else if (!dup_name) {
    toastr.error("Duplicate short name found!!!", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else {
    add_wrapper_data({
      igt_id: 0,
      igt_item_group_id: 0,
      igt_item_id: $("#item_id").val(),
      item_name: $("#item_id :selected").text(),
      igt_code: $("#code").val(),
      isExist: false,
    });
    toastr.success(
      `${$("#item_id :selected").text()} - ${$("#code").val()}`,
      "ITEM ADDED TO LIST.",
      { closeButton: true, progressBar: true }
    );
    $("#item_id").val(null).trigger("change");
    $("#item_id").select2("open");
    $("#code").val("");
    set_serial_no();
  }
};
const add_wrapper_data = (data) => {
  const {
    igt_id,
    igt_item_group_id,
    igt_item_id,
    item_name,
    igt_code,
    isExist,
  } = data;

  let tr = `<tr id="row_${igt_item_id}">
                    <td class="border-0 floating-label" width="10%">
                        <input 
                            type="hidden" 
                            name="igt_id[${igt_item_id}]" 
                            id="igt_id_${igt_item_id}" 
                            value="${igt_id}" 
                        />
                        <input 
                            type="hidden" 
                            name="igt_item_group_id[${igt_item_id}]" 
                            id="igt_item_group_id_${igt_item_id}" 
                            value="${igt_item_group_id}" 
                        />
                        <input 
                            type="text" 
                            class="form-control floating-input" 
                            id="igt_serial_no_${igt_item_id}" 
                            value=""
                            placeholder=" "
                            readonly
                        />
                    </td>
                    <td class="border-0 floating-label" width="10%">
                        <input 
                            type="hidden" 
                            name="igt_item_id[${igt_item_id}]" 
                            id="igt_item_id_${igt_item_id}" 
                            value="${igt_item_id}" 
                        />
                        <input 
                            type="text" 
                            class="form-control floating-input" 
                            id="item_name_${igt_item_id}" 
                            value="${item_name}"
                            placeholder=" "
                            readonly
                        />
                    </td>
                    <td class="border-0 floating-label" width="8%">
                        <input 
                            type="text" 
                            class="form-control floating-input" 
                            name="igt_code[${igt_item_id}]" 
                            id="igt_code_${igt_item_id}" 
                            value="${igt_code}"
                            placeholder=" "
                            autocomplete="off"
                            ${isExist ? "readonly" : ""}
                        />
                    </td>
                    <td class="border-0" width="3%">
                        ${
                          isExist
                            ? `<button 
                                    type="button" 
                                    class="btn btn-md btn-primary" 
                                ><i class="text-danger fa fa-ban"></i></button>`
                            : `<button 
                                    type="button" 
                                    class="btn btn-md btn-primary" 
                                    onclick="remove_transaction(${igt_item_id})"
                                ><i class="text-danger fa fa-trash"></i></button>`
                        }
                    </td>
                </tr>`;
  $("#transaction_wrapper").prepend(tr);
};
const add_update_item_group = (id, field) => {
  event.preventDefault();
  remove_master_notifier();

  let check = true;
  let duplicate_row = true;
  let required_row = true;

  if (check_transaction()) {
    if ($("#transaction_wrapper > tr").length > 0) {
      for (let i = 1; i <= $("#transaction_wrapper > tr").length; i++) {
        let cnt = $(`#transaction_wrapper > tr:nth-child(${i})`).attr("id");
        let explode = cnt.split("_");
        let id = explode[1];
        if (check_duplicate(id) == 0) {
          duplicate_row = false;
        }
      }
    }
  } else {
    required_row = false;
  }

  if ($(`#item_group_name`).val() == "") {
    notifier(`item_group_name`, "Required");
    check = false;
  }
  if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    $("body, html").animate({ scrollTop: 0 }, 1000);
  } else if (!required_row) {
    toastr.error("You forgot to enter some item information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else if (!duplicate_row) {
    toastr.error("Duplicate short name found!!!", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else {
    let path = `master/item_group/add_update/${id}`;
    let form_data = $(`#item_group_form`).serialize();
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        const { status, data, msg } = resp;
        if (handle_response(resp)) {
          if (id == 0) {
          } else {
          }
          remove_master_notifier();
          toastr.success("", msg, {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
          });
          $("body, html").animate({ scrollTop: 0 }, 1000);
          setTimeout(function () {
            window.location.reload();
          }, 2000);
        }

        if (!status) {
          const { trans_cnt } = data;
          if (trans_cnt) {
            if ($(`#row_${trans_cnt}`).length > 0) {
              $(window).scrollTop(
                $(`#row_${trans_cnt}`).offset().top - $(window).height() / 5
              );
            }
          }
        }
      },
      (errmsg) => {}
    );
  }
};
const remove_transaction_notifier = () => {
  notifier("item_id");
  notifier("code");
};
const remove_master_notifier = () => {
  notifier(`item_group_name`);
};
const remove_transaction = (igt_id) => {
  let item_name = $(`#item_name_${igt_id}`).val();
  let code = $(`#igt_code_${igt_id}`).val();
  toastr.success(`${item_name} - ${code}`, "ITEM REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#row_${igt_id}`).detach();
  $("#transaction_count").html($("#transaction_wrapper > tr").length);
  set_serial_no();
};
const remove_item_group = (data) => {
  let path = `master/item_group/remove/${data.item_group_id}`;
  let html = `<table class="table table-sm table-hover text-uppercase">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold" width="30%" align="right">name : </td>
                                <td width="70%">${data.item_group_name}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold" width="30%" align="right">merchant : </td>
                                <td width="70%">${data.item_group_merchant}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold" width="30%" align="right">rate : </td>
                                <td width="70%">${data.item_group_rate}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold" width="30%" align="right">status : </td>
                                <td width="70%">${
                                  data.item_group_status == 1
                                    ? "active"
                                    : "inactive"
                                }</td>
                            </tr>
                        </tbody>
                    </table>`;
  remove_datav2(html, path);
};
const set_serial_no = () => {
  let total_tr = $("#transaction_wrapper > tr").length;
  let sr_no = total_tr;
  $("#transaction_count").html(total_tr);
  for (let i = 1; i <= total_tr; i++) {
    let cnt = $(`#transaction_wrapper > tr:nth-child(${i})`).attr("id");
    let explode = cnt.split("_");
    let id = explode[1];
    $(`#igt_serial_no_${id}`).val(sr_no);
    sr_no = sr_no - 1;
  }
};
// core_functions

// additional_functions
const print_popup = (item_group_id) => {
  if (item_group_id) {
    let path = `master/item_group/get_data/${item_group_id}`;
    ajaxCall(
      "GET",
      path,
      "",
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data) {
            let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                        <p class="text-uppercase font-weight-bold">${data["master_data"][0]["item_group_name"]}</p>
                                        <div class="d-flex flex-wrap pt-3 floating-form">
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-3 floating-label m-0">
                                                <input 
                                                    type="number" 
                                                    class="form-control floating-input" 
                                                    id="item_group_rate" 
                                                    value="${data["master_data"][0]["item_group_rate"]}" 
                                                    placeholder="" 
                                                    autocomplete="off"
                                                />   
                                                <p class="d-flex text-uppercase">
                                                    rate
                                                    <span class="custom-control custom-checkbox pr-3">
                                                        <label class="custom-control material-checkbox" for="with_rate">
                                                            <input 
                                                                type="checkbox" 
                                                                class="material-control-input" 
                                                                id="with_rate"
                                                            >
                                                            <span class="material-control-indicator"></span>
                                                            <span class="material-control-description text-uppercase"></span>
                                                        </label>
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-5 floating-label m-0">
                                                <input 
                                                    type="text" 
                                                    class="form-control floating-input" 
                                                    id="item_group_merchant" 
                                                    value="${data["master_data"][0]["item_group_merchant"]}" 
                                                    placeholder="" 
                                                    autocomplete="off"
                                                />   
                                                <p class="d-flex text-uppercase">
                                                    merchant
                                                    <span class="custom-control custom-checkbox pr-3">
                                                        <label class="custom-control material-checkbox" for="with_merchant">
                                                            <input 
                                                                type="checkbox" 
                                                                class="material-control-input" 
                                                                id="with_merchant"
                                                            >
                                                            <span class="material-control-indicator"></span>
                                                            <span class="material-control-description text-uppercase"></span>
                                                        </label>
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-6 col-lg-4 floating-label m-0">
                                                <input 
                                                    type="text" 
                                                    class="form-control floating-input" 
                                                    id="with_mtr" 
                                                    value="0" 
                                                    placeholder="" 
                                                    autocomplete="off"
                                                    min="0"
                                                    onkeyup="check_with_mtr()"
                                                />   
                                                <label class="text-uppercase">available qty</label>
                                            </div>
                                        </div>
                                     </div>`;
            let body = `<form id="print_form">
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                                <table class="table table-sm table-dark font-weight-bold text-uppercase" style="font-size: 0.7rem;">
                                                    <thead>
                                                        <tr>
                                                            <th class="border-0 text-center" width="15%">
                                                                <div class="custom-control custom-checkbox pr-3">
                                                                    <label class="custom-control material-checkbox-secondary" for="parent_checkbox">
                                                                        <input 
                                                                            type="checkbox" 
                                                                            class="material-control-input-secondary" 
                                                                            id="parent_checkbox" 
                                                                            onchange="select_deselect()" 
                                                                            checked
                                                                        >
                                                                        <span class="material-control-indicator-secondary"></span>
                                                                        <span class="material-control-description text-uppercase">#</span>
                                                                    </label>
                                                                </div>
                                                            </th>
                                                            <th class="border-0 align-baseline" width="20%">short name</th>
                                                            <th class="border-0 align-baseline" width="40%">color no</th>
                                                            <th class="border-0 align-baseline" width="30%">available qty</th>
                                                        </tr>
                                                    </thead>
                                                </table>`;
            if (data["ict_data"] && data["ict_data"].length != 0) {
              body += `<table class="table table-sm table-hover table-responsive text-uppercase font-weight-bold" style="max-height: 54vh; font-size: 0.7rem;">
                                                        <tbody id="color_material_wrapper">`;
              data["ict_data"].forEach((row, index) => {
                body += `<tr id="itemrow_${row["ict_id"]}">
                                                                        <td width="5%" align="left">
                                                                            <div class="custom-control custom-checkbox pr-3">
                                                                                <label class="custom-control material-checkbox" for="ict_id_${
                                                                                  row[
                                                                                    "ict_id"
                                                                                  ]
                                                                                }">
                                                                                    <input 
                                                                                        type="checkbox" 
                                                                                        class="material-control-input checkboxes" 
                                                                                        id="ict_id_${
                                                                                          row[
                                                                                            "ict_id"
                                                                                          ]
                                                                                        }" 
                                                                                        name="ict_id[]" 
                                                                                        value="${
                                                                                          row[
                                                                                            "ict_id"
                                                                                          ]
                                                                                        }"
                                                                                        onchange="select_deselect(${
                                                                                          row[
                                                                                            "ict_id"
                                                                                          ]
                                                                                        })" 
                                                                                        checked
                                                                                    >
                                                                                    <span class="material-control-indicator"></span>
                                                                                    <span class="material-control-description text-uppercase">${
                                                                                      index +
                                                                                      1
                                                                                    }</span>
                                                                                </label>
                                                                            </div>
                                                                        </td>
                                                                        <td width="20%">${
                                                                          row[
                                                                            "igt_code"
                                                                          ]
                                                                        }</td>
                                                                        <td width="50%">${
                                                                          row[
                                                                            "ict_color_no"
                                                                          ]
                                                                        }</td>
                                                                        <td width="15%">
                                                                            ${
                                                                              row[
                                                                                "with_mtr"
                                                                              ]
                                                                            }
                                                                            <input 
                                                                                type="hidden"
                                                                                id="with_mtr_${
                                                                                  row[
                                                                                    "ict_id"
                                                                                  ]
                                                                                }"
                                                                                value="${
                                                                                  row[
                                                                                    "with_mtr"
                                                                                  ]
                                                                                }"
                                                                            />
                                                                        </td>
                                                                    </tr>`;
              });
              body += `</tbody>
                                                    </table>`;
            } else {
              body += `<p class="text-uppercase text-center text-danger font-weight-bold">no item added !!!</p>`;
            }
            body += `</div>
                                         </div>
                                    </form>`;
            let btn = `<div class="d-flex flex-wrap justify-content-center w-100">
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-primary mr-2 text-uppercase"
                                            onclick="print_item(${item_group_id})" 
                                        ><i class="text-success fa fa-print"></i> item</button>
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-primary mr-2 text-uppercase"
                                            id="sbt_btn" 
                                            style="width:25%;"
                                            onclick="print_color(${item_group_id})" 
                                        >
                                            <div class="stage d-none"><div class="dot-flashing"></div></div>
                                            <div class="dot-text text-primary text-uppercase"><i class="text-success fa fa-print"></i> color <span id="item_count">${data["ict_data"].length}</span></div> 
                                        </button>
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-primary ml-2" 
                                            data-dismiss="modal"
                                        >CANCEL</button>
                                    </div>`;
            $(".modal-title-sm").html(title);
            $(".modal-body-sm").html(body);
            $(".modal-footer-sm").html(btn);
            $("#popup_modal_sm").modal("show");
            check_with_mtr();
          }
        }
      },
      (errmsg) => {}
    );
  }
};
const check_with_mtr = () => {
  let filter_qty = parseValue($("#with_mtr").val());
  let total_tr = $("#color_material_wrapper tr").length;
  for (let i = 1; i <= total_tr; i++) {
    let id = $(`#color_material_wrapper tr:nth-child(${i})`).attr("id");
    let explode = id.split("_");
    let ict_id = explode[1];

    let with_mtr = $(`#with_mtr_${ict_id}`).val();
    if (isNaN(with_mtr) || with_mtr == "") with_mtr = 0;

    $(`#ict_id_${ict_id}`).prop("checked", with_mtr >= filter_qty);
  }
  let total_checked = $(".checkboxes:checked").length;
  $("#item_count").html(total_checked > 0 ? total_checked : "");
  $(`#parent_checkbox`).prop("checked", total_tr == total_checked);
};
const select_deselect = (count = 0) => {
  let total_tr = $("#color_material_wrapper tr").length;
  for (let i = 1; i <= total_tr; i++) {
    let id = $(`#color_material_wrapper tr:nth-child(${i})`).attr("id");
    let explode = id.split("_");
    let ict_id = explode[1];
    if (count == 0) {
      let parent_checked = $(`#parent_checkbox`).is(":checked");
      $(`#ict_id_${ict_id}`).prop("checked", parent_checked);
    }
  }
  let total_checked = $(".checkboxes:checked").length;
  $("#item_count").html(total_checked > 0 ? total_checked : "");
  $(`#parent_checkbox`).prop("checked", total_tr == total_checked);
};
const print_item = (item_group_id) => {
  let with_rate = $("#with_rate").is(":checked")
    ? $("#item_group_rate").val()
    : 0;
  let with_merchant = $("#with_merchant").is(":checked")
    ? encodeURIComponent($("#item_group_merchant").val())
    : 0;

  if (
    $("#with_rate").is(":checked") &&
    ($("#item_group_rate").val() == "" || $("#item_group_rate").val() <= 0)
  ) {
    toastr.error("Invalid Rate.", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    return;
  }

  if (
    $("#with_merchant").is(":checked") &&
    $("#item_group_merchant").val() == ""
  ) {
    toastr.error("Invalid Merchant.", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    return;
  }
  window.open(
    `item_group/print_item/${item_group_id}/${with_rate}/${with_merchant}`
  );
};
const print_color = (item_id) => {
  let ict_ids = "";
  let total_tr = $("#color_material_wrapper tr").length;
  for (let i = 1; i <= total_tr; i++) {
    let id = $(`#color_material_wrapper tr:nth-child(${i})`).attr("id");
    let explode = id.split("_");
    let ict_id = explode[1];
    if ($(`#ict_id_${ict_id}`).is(":checked")) {
      ict_ids += ict_ids.length == 0 ? ict_id : "," + ict_id;
    }
  }
  if (ict_ids.length <= 0) {
    toastr.error("Please select alteast one color.", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    return;
  }
  ict_ids = encodeURIComponent(ict_ids);
  window.open(`item_group/print_color/${ict_ids}`);
};
// additional_functions
