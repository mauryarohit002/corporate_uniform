$(document).ready(function () {
  lazy_loading("form_loading");
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
const add_transaction = () => {
  notifier("type");
  let check = true;
  if ($("#asm_name").val() == "") {
    notifier("asm_name", "Required");
    check = false;
  }
  if ($("#type").val() == "") {
    notifier("type", "Required");
    check = false;
  }

  if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else {
    let ast_id = $("#ast_id").val();
    compress_image("asm_image")
      .then((compressedImage) => {
        const path = `${link}/${sub_link}/handler`;
        let form_id = document.getElementById(`_form`);
        let form_data = new FormData(form_id);
        form_data.append("func", "add_transaction");
        if (compressedImage.name) {
          form_data.append(`asm_image`, compressedImage, compressedImage.name);
        }
        fileUpAjaxCall(
          "POST",
          path,
          form_data,
          "JSON",
          (resp) => {
            if (handle_response(resp)) {
              const { data, msg } = resp;
              if (data && data.length != 0) {
                if (ast_id == 0) {
                  trans_data.unshift(data);
                  add_wrapper_data(data);
                  toastr.success(`${$("#type").val()}`, "ITEM ADDED TO LIST.", {
                    closeButton: true,
                    progressBar: true,
                  });
                } else {
                  let index = trans_data.findIndex(
                    (value) => value.ast_id == ast_id
                  );
                  if (index < 0) {
                    toastr.success(`Transaction not found`, "", {
                      closeButton: true,
                      progressBar: true,
                    });
                  }
                  trans_data[index].ast_name = data["ast_name"];
                  trans_data[index].ast_image = data["ast_image"];

                  $(`#type_${ast_id}`).html(data["ast_name"]);
                  $(`#preview_${ast_id}`).html(`<img 
                                                  class="pan form_loading_${ast_id}" 
                                                  onclick="zoom(this)" 
                                                  title="click to zoom in and zoom out" 
                                                  src="${LAZYLOADING}" 
                                                  data-src="${data["ast_image"]}" 
                                                  data-big="${data["ast_image"]}" 
                                                  style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                              />`);
                  lazy_loading(`form_loading_${ast_id}`);
                  toastr.success(
                    `${$("#type").val()}`,
                    "ITEM UPDATED TO LIST.",
                    { closeButton: true, progressBar: true }
                  );
                }
                $("#ast_id").val(0);
                $("#type").val("").focus();
                remove_asm_image();
                $("#transaction_count").html(trans_data.length);
              }
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
const add_wrapper_data = (data, append = false) => {
  const { ast_id, ast_name, ast_image, ast_default, isExist } = data;
  const div = `<div class="col-12 col-sm-12 col-md-4 col-lg-3" id="row_${ast_id}">
                <div class="d-flex flex-wrap border my-1 pb-3">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                        <div class="d-flex flex-column align-items-center">
                            <span class="d-flex justify-content-center" id="preview_${ast_id}" style="width: 8rem; height:8rem;">
                                <img 
                                    class="pan form_loading_${ast_id}" 
                                    onclick="zoom(this)" 
                                    title="click to zoom in and zoom out" 
                                    src="${LAZYLOADING}" 
                                    data-src="${ast_image}" 
                                    data-big="${ast_image}" 
                                    style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                />
                            </span>
                            <b class="text-uppercase text-center p-2" id="type_${ast_id}" style="height: 3rem; font-size: 0.8rem;">${ast_name}</b>
                            <div class="d-flex flex-wrap mt-3" style="gap: 10px;">
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
                                      onclick="edit_transaction(${ast_id})"
                                    ><i class="text-info fa fa-edit"></i></button>`
                              }
                              ${
                                isExist
                                  ? `<button 
                                      type="button" 
                                      class="btn btn-sm btn-primary"
                                    ><i class="text-danger fa fa-ban"></i></button>`
                                  : `<a 
                                      type="button" 
                                      class="btn btn-sm btn-primary" 
                                      onclick="remove_transaction(${ast_id})"
                                    ><i class="text-danger fa fa-trash"></i></a>`
                              }
                            </div>
                            <div class="form-check mt-3">
                              <input 
                                class="form-check-input" 
                                type="radio" 
                                id="default_${ast_id}" 
                                name="ast_default"
                                ${ast_default == 1 ? "checked" : ""}
                                ${
                                  isExist
                                    ? "disabled"
                                    : `onclick="set_default(${ast_id})"`
                                }>
                              <label class="form-check-label text-uppercase font-weight-bold pb-2" for="default_${ast_id}" style="font-size: 0.8rem;">
                                default
                              </label>
                            </div>
                        </div>
                    </div>
                </div>
              </div>`;
  if (append) {
    $("#transaction_wrapper").append(div);
  } else {
    $("#transaction_wrapper").prepend(div);
  }
  lazy_loading(`form_loading_${ast_id}`);
};
const add_edit = () => {
  event.preventDefault();
  notifier(`asm_name`);
  let check = true;
  let required_row = true;
  if (!check_transaction()) {
    required_row = false;
  }
  if ($(`#asm_name`).val() == "") {
    notifier(`asm_name`, "Required");
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
const remove_transaction = (ast_id) => {
  trans_data = trans_data.filter((value) => value.ast_id != ast_id);
  let type = $(`#type_${ast_id}`).html();
  toastr.success(`${type}`, "ITEM REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#row_${ast_id}`).detach();
  $("#transaction_count").html(trans_data.length);
};
const edit_transaction = (ast_id) => {
  const find = trans_data.find((value) => value["ast_id"] == ast_id);
  const { ast_name, ast_image } = find;

  $("#ast_id").val(ast_id);
  $("#type").val(ast_name);
  $("#asm_pic").val(ast_image);
  $("#preview").html(`<img 
                          class="pan form_loading" 
                          onclick="zoom(this)" 
                          title="click to zoom in and zoom out" 
                          src="${LAZYLOADING}" 
                          data-src="${ast_image}" 
                          data-big="${ast_image}" 
                          style="max-width: 100%; max-height: 100%; object-fit: contain;"
                        />`);
  toggle_menuu({ id: ast_id });
  lazy_loading(`form_loading`);
};
// core_functions

// additional_functions
const remove_asm_image = () => {
  $("#preview").html(`<img 
							class="img-thumbnail pan form_loading" 
							onclick="zoom(this)" 
							title="click to zoom in and zoom out" 
							src="${LAZYLOADING}" 
							data-src="${NOIMAGE}" 
							data-big="${NOIMAGE}" 
							style="max-width: 100%; max-height: 100%; object-fit: contain;"
						/>`);
  lazy_loading("form_loading");
  $("#asm_image").val("");
  $("#asm_pic").val(NOIMAGE);
};
const set_default = (ast_id) => {
  trans_data.forEach((value, index) => {
    if (value.ast_id == ast_id) {
      trans_data[index].ast_default = 1;
    } else {
      trans_data[index].ast_default = 0;
    }
  });
};
// additional_functions
