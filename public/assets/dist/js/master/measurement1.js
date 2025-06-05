$(document).ready(function () {
  $("#measurement_apparel_id").select2(
    select2_default({
      url: `master/apparel/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );
  $("#maap_id").select2(
    select2_default({
      url: `master/maap/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );
  $("#style_id").select2(
    select2_default({
      url: `master/style/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );
  $("#mmt_maap_id")
    .select2(
      select2_default({
        url: `master/measurement/get_select2/_maap_id`,
        placeholder: "SELECT",
        param: true,
      })
    )
    .on("change", (event) => get_maap_data(event.target.value));
  $("#mst_style_id")
    .select2(
      select2_default({
        url: `master/measurement/get_select2/_style_id`,
        placeholder: "SELECT",
      })
    )
    .on("change", (event) => get_style_data(event.target.value));
});
let maap_cnt = 1;
let style_cnt = 1;
const get_transaction = () => {
  if (get_url_string("action") == "edit") {
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
            const { maap_data, style_data } = data;
            if (maap_data && maap_data.length != 0) {
              maap_data.forEach((row) => add_maap_wrapper(row));
            }
            if (style_data && style_data.length != 0) {
              style_data.forEach((row) => add_style_wrapper(row));
            }
          }
        },
        (errmsg) => {}
      );
    }
  }
};
// maap
const add_maap = () => {
  notifier("maap_id");
  let check = true;
  let dup_check = true;
  let total_tr = $("#maap_wrapper > tr").length;
  if ($("#maap_id").val() == null) {
    notifier("maap_id", "Required");
    check = false;
  }
  if (total_tr > 0) {
    let new_maap_id = $("#maap_id").val();
    for (let i = 1; i <= total_tr; i++) {
      let cnt = $(`#maap_wrapper > tr:nth-child(${i})`).attr("id");
      let explode = cnt.split("_");
      let id = explode[1];

      let old_maap_id = $(`#mmt_maap_id_${id}`).val();
      if (new_maap_id == old_maap_id) {
        notifier("maap_id", "Already added.");
        dup_check = false;
      }
    }
  }
  if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    $("body, html").animate({ scrollTop: 0 }, 1000);
  } else if (!dup_check) {
    toastr.error("Duplicate maap found!!!", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    $("body, html").animate({ scrollTop: 0 }, 1000);
    setTimeout(() => {
      $("#maap_id").val(null).trigger("change");
      $("#maap_id").select2("open");
      notifier("maap_id");
    }, RELOAD_TIME);
  } else {
    add_maap_wrapper({
      mmmt_id: 0,
      mmt_maap_id: $("#maap_id").val(),
      maap_name: $("#maap_id :selected").text(),
    });

    toastr.success($("#maap_id :selected").text(), "MAAP ADDED TO LIST.", {
      closeButton: true,
      progressBar: true,
    });
    $("#maap_id").val(null).trigger("change");
    $("#maap_id").select2("open");
  }
};
const add_maap_wrapper = (data) => {
  const { mmt_id, mmt_maap_id, maap_name } = data;
  let tbody = `<tr class="floating-form" id="rowmaap_${maap_cnt}">
                <td class="floating-label border-0" width="75%">
                  <input 
                    type="hidden" 
                    id="mmt_id_${maap_cnt}" 
                    name="mmt_id[${maap_cnt}]" 
                    value="${mmt_id}" 
                  />
                  <select
                    class="form-control floating-select"
                    id="mmt_maap_id_${maap_cnt}"
                    name="mmt_maap_id[${maap_cnt}]"
                  ><option value="${mmt_maap_id}">${maap_name}</option></select>
                </td>
                <td class="border-0" width="5%">
                  <button 
                    type="button" 
                    class="btn btn-md btn-primary" 
                    onclick="remove_maap(${maap_cnt})"
                  ><i class="text-danger fa fa-trash"></i></button>
                </td>
                <td class="border-0" width="20%">
                  <small class="form-text text-muted helper-text" id="mmt_maap_id_${maap_cnt}_msg"></small>
                </td>
            </tr>`;
  $("#maap_wrapper").prepend(tbody);
  $(`#mmt_maap_id_${maap_cnt}`)
    .select2(
      select2_default({
        url: `master/maap/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
      })
    )
    .on("change", () => {
      if (check_maap_trans()) {
        for (let i = 1; i <= $("#maap_wrapper > tr").length; i++) {
          let cnt = $(`#maap_wrapper > tr:nth-child(${i})`).attr("id");
          let explode = cnt.split("_");
          let id = explode[1];
          check_duplicate_maap(id);
        }
      }
    });
  maap_cnt++;
};
const remove_maap = (cnt) => {
  let maap_name = $(`#mmt_maap_id_${cnt} :selected`).text();
  toastr.success(maap_name, "MAAP REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#rowmaap_${cnt}`).detach();
};
const check_maap_trans = () => {
  let last_id = 0;
  let flag = true;
  let total_tr = $("#maap_wrapper > tr").length;
  if (total_tr > 0) {
    for (let i = 1; i <= total_tr; i++) {
      let cnt = $(`#maap_wrapper > tr:nth-child(${i})`).attr("id");
      let explode = cnt.split("_");
      let id = explode[1];
      if ($(`#mmt_maap_id_${id}`).val() == null) {
        notifier(`mmt_maap_id_${id}`, "Required");
        last_id = id;
        flag = false;
      } else {
        notifier(`mmt_maap_id_${id}`);
      }
    }
  }
  return flag;
};
const check_duplicate_maap = (cntCheck) => {
  let total_tr = $("#maap_wrapper > tr").length;
  let new_maap_id = $(`#mmt_maap_id_${cntCheck}`).val();
  for (let i = 1; i <= total_tr; i++) {
    let cnt = $(`#maap_wrapper > tr:nth-child(${i})`).attr("id");
    let explode = cnt.split("_");
    let id = explode[1];
    if (cntCheck != id) {
      let old_maap_id = $(`#mmt_maap_id_${id}`).val();
      if (new_maap_id == old_maap_id) {
        console.log({ new_maap_id, old_maap_id });
        notifier(`mmt_maap_id_${cntCheck}`, "Already added.");
        notifier(`mmt_maap_id_${id}`, "Already added.");
        return 0;
      } else {
        // notifier(`mmt_maap_id_${cntCheck}`);
        // notifier(`mmt_maap_id_${id}`);
      }
    }
  }
  return 1;
};
const get_maap_data = (id) => {
  if (id) {
    const path = `master/measurement/handler`;
    const form_data = {
      func: "get_record",
      sub_func: "get_maap_data",
      id,
    };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            data.forEach((row) => add_maap_wrapper(row));
            toastr.success(
              $("#mmt_maap_id :selected").text(),
              "MAAP COPIED TO LIST.",
              {
                closeButton: true,
                progressBar: true,
              }
            );
          }
        }
      },
      (errmsg) => {}
    );
  }
};
// maap
// style
const add_style = () => {
  notifier("style_id");
  let check = true;
  let dup_check = true;
  let total_tr = $("#style_wrapper > tr").length;
  if ($("#style_id").val() == null) {
    notifier("style_id", "Required");
    check = false;
  }
  if (total_tr > 0) {
    let new_style_id = $("#style_id").val();
    for (let i = 1; i <= total_tr; i++) {
      let cnt = $(`#style_wrapper > tr:nth-child(${i})`).attr("id");
      let explode = cnt.split("_");
      let id = explode[1];

      let old_style_id = $(`#mst_style_id_${id}`).val();
      if (new_style_id == old_style_id) {
        notifier("style_id", "Already added.");
        dup_check = false;
      }
    }
  }
  if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    $("body, html").animate({ scrollTop: 0 }, 1000);
  } else if (!dup_check) {
    toastr.error("Duplicate style found!!!", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    $("body, html").animate({ scrollTop: 0 }, 1000);
    setTimeout(() => {
      $("#style_id").val(null).trigger("change");
      $("#style_id").select2("open");
      notifier("style_id");
    }, RELOAD_TIME);
  } else {
    add_style_wrapper({
      mmst_id: 0,
      mst_style_id: $("#style_id").val(),
      style_name: $("#style_id :selected").text(),
    });

    toastr.success($("#style_id :selected").text(), "STYLE ADDED TO LIST.", {
      closeButton: true,
      progressBar: true,
    });
    $("#style_id").val(null).trigger("change");
    $("#style_id").select2("open");
  }
};
const add_style_wrapper = (data) => {
  const { mst_id, mst_style_id, style_name } = data;
  let tbody = `<tr class="floating-form" id="rowstyle_${style_cnt}">
                <td class="floating-label border-0" width="75%">
                  <input 
                    type="hidden" 
                    id="mst_id_${style_cnt}" 
                    name="mst_id[${style_cnt}]" 
                    value="${mst_id}" 
                  />
                  <select
                    class="form-control floating-select"
                    id="mst_style_id_${style_cnt}"
                    name="mst_style_id[${style_cnt}]"
                  ><option value="${mst_style_id}">${style_name}</option></select>
                </td>
                <td class="border-0" width="5%">
                  <button 
                    type="button" 
                    class="btn btn-md btn-primary" 
                    onclick="remove_style(${style_cnt})"
                  ><i class="text-danger fa fa-trash"></i></button>
                </td>
                <td class="border-0" width="20%">
                  <small class="form-text text-muted helper-text" id="mst_style_id_${style_cnt}_msg"></small>
                </td>
            </tr>`;
  $("#style_wrapper").prepend(tbody);
  $(`#mst_style_id_${style_cnt}`)
    .select2(
      select2_default({
        url: `master/style/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
      })
    )
    .on("change", () => {
      if (check_style_trans()) {
        for (let i = 1; i <= $("#style_wrapper > tr").length; i++) {
          let cnt = $(`#style_wrapper > tr:nth-child(${i})`).attr("id");
          let explode = cnt.split("_");
          let id = explode[1];
          check_duplicate_style(id);
        }
      }
    });
  style_cnt++;
};
const remove_style = (cnt) => {
  let style_name = $(`#mst_style_id_${cnt} :selected`).text();
  toastr.success(style_name, "STYLE REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#rowstyle_${cnt}`).detach();
};
const check_style_trans = () => {
  let last_id = 0;
  let flag = true;
  let total_tr = $("#style_wrapper > tr").length;
  if (total_tr > 0) {
    for (let i = 1; i <= total_tr; i++) {
      let cnt = $(`#style_wrapper > tr:nth-child(${i})`).attr("id");
      let explode = cnt.split("_");
      let id = explode[1];
      if ($(`#mst_style_id_${id}`).val() == null) {
        notifier(`mst_style_id_${id}`, "Required");
        last_id = id;
        flag = false;
      } else {
        notifier(`mst_style_id_${id}`);
      }
    }
  }
  return flag;
};
const check_duplicate_style = (cntCheck) => {
  let total_tr = $("#style_wrapper > tr").length;
  let new_style_id = $(`#mst_style_id_${cntCheck}`).val();
  for (let i = 1; i <= total_tr; i++) {
    let cnt = $(`#style_wrapper > tr:nth-child(${i})`).attr("id");
    let explode = cnt.split("_");
    let id = explode[1];
    if (cntCheck != id) {
      let old_style_id = $(`#mst_style_id_${id}`).val();
      if (new_style_id == old_style_id) {
        console.log({ new_style_id, old_style_id });
        notifier(`mst_style_id_${cntCheck}`, "Already added.");
        notifier(`mst_style_id_${id}`, "Already added.");
        return 0;
      } else {
        // notifier(`mst_style_id_${cntCheck}`);
        // notifier(`mst_style_id_${id}`);
      }
    }
  }
  return 1;
};
const get_style_data = (id) => {
  if (id) {
    const path = `master/measurement/handler`;
    const form_data = {
      func: "get_record",
      sub_func: "get_style_data",
      id,
    };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            data.forEach((row) => add_style_wrapper(row));
            toastr.success(
              $("#mmt_style_id :selected").text(),
              "STYLE COPIED TO LIST.",
              {
                closeButton: true,
                progressBar: true,
              }
            );
          }
        }
      },
      (errmsg) => {}
    );
  }
};
// style
const add_edit = () => {
  event.preventDefault();
  let check = true;
  let required_maap = true;
  let duplicate_maap = true;
  notifier(`measurement_apparel_id`);
  if ($(`#measurement_apparel_id`).val() == null) {
    notifier(`measurement_apparel_id`, "Required");
    check = false;
  }
  if ($("#maap_wrapper > tr").length > 0) {
    if (check_maap_trans()) {
      for (let i = 1; i <= $("#maap_wrapper > tr").length; i++) {
        let cnt = $(`#maap_wrapper > tr:nth-child(${i})`).attr("id");
        let explode = cnt.split("_");
        let id = explode[1];
        if (check_duplicate_maap(id) == 0) {
          duplicate_maap = false;
        }
      }
    } else {
      required_maap = false;
    }
  }

  if (!required_maap) {
    toastr.error("You forgot to enter some maap information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else if (!duplicate_maap) {
    toastr.error("Duplicate maap found!!!", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    $("body, html").animate({ scrollTop: 0 }, 1000);
  } else {
    const id = $("#measurement_id").val();
    const path = `${link}/${sub_link}/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=add_edit`;
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { msg } = resp;
          if (id == 0) {
          } else {
          }
          notifier(`measurement_apparel_id`);
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
      },
      (errmsg) => {}
    );
  }
};
const measurement_remove = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.measurement_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                <tbody>
                    <tr>
                        <td class="font-weight-bold" width="30%" align="right">name : </td>
                        <td width="70%">${data.apparel_name}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold" width="30%" align="right">status : </td>
                        <td width="70%">
                          ${
                            data.measurement_status == 1 ? "active" : "inactive"
                          }
                        </td>
                    </tr>
                </tbody>
            </table>`;
  remove_datav3({ path, form_data, html });
};
