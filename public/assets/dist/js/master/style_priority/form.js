$(document).ready(function () {
  $(`#spm_apparel_id`).select2(
    select2_default({
      url: `master/apparel/get_select2/_id`,
      placeholder: "select",
      param: true,
    })
  );
  $(`#asm_id`).select2(
    select2_default({
      url: `master/apparel_style/get_select2/_id`,
      placeholder: "select",
      param: true,
    })
  );
  $(`#_spm_id`)
    .select2(
      select2_default({
        url: `${link}/${sub_link}/get_select2/_spm_id`,
        placeholder: "select",
        param: () => $("#id").val(),
      })
    )
    .on("change", (event) => get_priority_data(event.target.value));
});
// core_functions
let trans_data = [];
const get_transaction = () => {
  if (["edit", "read"].includes(get_url_string("action"))) {
    let id = get_url_string("id");
    if (id) {
      const path = `${link}/${sub_link}/handler`;
      const form_data = { func: "get_transaction", id };
      ajaxCall(
        "POST",
        path,
        form_data,
        "JSON",
        (resp) => {
          if (handle_response(resp)) {
            const { data, msg } = resp;
            if (data && data.length != 0) {
              trans_data = data;
              // let result = paginate(trans_data, page);
              // if (result && result.length != 0) {
              //   result.forEach((value) => add_wrapper_data(value, true));
              // }
              if (trans_data && trans_data.length != 0) {
                trans_data.forEach((value) => add_wrapper_data(value, true));
              }
            }
            $("#transaction_count").html(trans_data.length);
            $("#priority").val(trans_data.length + 1);
          }
        },
        (errmsg) => {}
      );
    }
  }
};
const check_transaction = () => {
  let flag = true;
  if (trans_data.length <= 0) return false;
  trans_data.forEach((value) => {});
  return flag;
};
const check_duplicate = () => {
  if (trans_data.length <= 0) return false;

  const priority = $("#priority").val();
  const asm_id = $("#asm_id").val();
  return trans_data
    .map((value) => {
      if ($("#spt_id").val() != value["spt_id"]) {
        return (
          value["spt_priority"] == priority || value["spt_asm_id"] == asm_id
        );
      }
    })
    .includes(true);
};
const add_transaction = () => {
  notifier("spm_apparel_id");
  notifier("asm_id");
  notifier("priority");
  let check = true;
  if ($("#spm_apparel_id").val() == null) {
    notifier("spm_apparel_id", "Required");
    check = false;
  }
  if ($("#asm_id").val() == null) {
    notifier("asm_id", "Required");
    check = false;
  }
  if ($("#priority").val() == "") {
    notifier("priority", "Required");
    check = false;
  } else {
    if ($(`#em_total_amt`).val() <= 0) {
      notifier(`priority`, "Invalid");
      check = false;
    }
  }
  if (check_duplicate()) {
    toastr.error("Priority or style already added", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    return false;
  }
  if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else {
    let spt_id = $("#spt_id").val();
    const path = `${link}/${sub_link}/handler`;
    let form_id = document.getElementById(`_form`);
    let form_data = new FormData(form_id);
    form_data.append("func", "add_transaction");
    fileUpAjaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            if (spt_id == 0) {
              trans_data.unshift(data);
              add_wrapper_data(data);
              toastr.success(
                `${$("#asm_id :selected").text()}`,
                "ITEM ADDED TO LIST.",
                {
                  closeButton: true,
                  progressBar: true,
                }
              );
            } else {
              let index = trans_data.findIndex(
                (value) => value.spt_id == spt_id
              );
              if (index < 0) {
                toastr.success(`Transaction not found`, "", {
                  closeButton: true,
                  progressBar: true,
                });
              }
              trans_data[index].spt_priority = data["spt_priority"];
              trans_data[index].spt_asm_id = data["spt_asm_id"];

              $(`#priority_${spt_id}`).html(data["spt_priority"]);
              $(`#asm_name_${spt_id}`).html(data["asm_name"]);
              toastr.success(
                `${$("#asm_id :selected").text()}`,
                "ITEM UPDATED TO LIST.",
                {
                  closeButton: true,
                  progressBar: true,
                }
              );
            }
            $("#spt_id").val(0);
            $("#asm_id").val(null).trigger("change");
            $("#asm_id").select2("open");
            $("#priority").val(parseInt(data["spt_priority"]) + 1);
            $("#transaction_count").html(trans_data.length);
          }
        }
      },
      (errmsg) => {}
    );
  }
};
const add_wrapper_data = (data, append = false) => {
  const { spt_id, spt_priority, asm_name, isExist } = data;
  let tr = `<tr id="row_${spt_id}">
              <td id="priority_${spt_id}">${spt_priority}</td>
              <td id="asm_name_${spt_id}">${asm_name}</td>
              <td>
                ${
                  isExist
                    ? `<button 
                          type="button" 
                          class="btn btn-sm btn-primary"
                        ><i class="text-danger fa fa-ban"></i></button>`
                    : `<button 
                        type="button" 
                        class="btn btn-sm btn-primary" 
                        class="btn btn-sm" 
                        onclick="edit_transaction('${spt_id}')"
                      ><i class="text-info fa fa-edit"></i></button>`
                }
              </td>
              <td>
                ${
                  isExist
                    ? `<button 
                        type="button" 
                        class="btn btn-sm btn-primary"
                      ><i class="text-danger fa fa-ban"></i></button>`
                    : `<a 
                        type="button" 
                        class="btn btn-sm btn-primary" 
                        onclick="remove_transaction('${spt_id}')"
                      ><i class="text-danger fa fa-trash"></i></a>`
                }
              </td>
            </tr>`;
  if (append) {
    $("#transaction_wrapper").append(tr);
  } else {
    $("#transaction_wrapper").append(tr);
  }
};
const add_edit = () => {
  event.preventDefault();
  notifier("spm_apparel_id");
  let check = true;
  let required_row = true;
  if (!check_transaction()) {
    required_row = false;
  }
  if ($(`#spm_apparel_id`).val() == null) {
    notifier(`spm_apparel_id`, "Required");
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
    toastr.error("You forgot to enter some item information.", "Oh snap!!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else {
    const path = `${link}/${sub_link}/handler`;
    let form_id = document.getElementById("_form");
    let form_data = new FormData(form_id);
    form_data.append("func", "add_edit");
    form_data.append("trans_data", JSON.stringify(trans_data));
    fileUpAjaxCall(
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
          notifier(`asm_name`);
          toastr.success("", msg, {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
          });
          $("body, html").animate({ scrollTop: 0 }, 1000);
          setTimeout(() => {
            window.location.reload();
          }, RELOAD_TIME);
        }
      },
      (errmsg) => {}
    );
  }
};
const remove_transaction = (spt_id) => {
  trans_data = trans_data.filter((value) => value.spt_id != spt_id);
  let priority = $(`#priority_${spt_id}`).html();
  toastr.success(`${priority}`, "ITEM REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#row_${spt_id}`).detach();
  $("#transaction_count").html(trans_data.length);
};
const edit_transaction = (spt_id) => {
  const find = trans_data.find((value) => value["spt_id"] == spt_id);
  const { spt_priority, spt_asm_id, asm_name } = find;

  $("#spt_id").val(spt_id);
  $("#priority").val(spt_priority);
  $("#asm_id").html(`<option value="${spt_asm_id}">${asm_name}</option>`);
  toggle_menuu({ id: spt_id });
};
// core_functions

// additional_functions
const get_priority_data = (id) => {
  if (!id) return false;
  trans_data = [];
  $("#transaction_wrapper").html("");
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "get_priority_data", id };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        if (data && data.length != 0) {
          trans_data = data;
          data.forEach((value) => add_wrapper_data(value, true));
        }
        $("#transaction_count").html(trans_data.length);
        $("#priority").val(trans_data.length + 1);
      }
    },
    (errmsg) => {}
  );
};
// additional_functions
