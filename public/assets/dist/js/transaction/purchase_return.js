$(document).ready(function () {
  $(`#bm_id`)
    .select2(
      select2_default({
        url: `${link}/${sub_link}/get_select2/_bm_id`,
        placeholder: "scan",
        barcode: "bm_id",
      })
    )
    .on("change", (event) => get_barcode_data(event.target.value));
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
              let result = paginate(trans_data, page);
              if (result && result.length != 0) {
                result.forEach((value) => add_wrapper_data(value, true));
              }
            }
            calculate_master();
            $("#transaction_count").html(trans_data.length);
          }
        },
        (errmsg) => {}
      );
    }
  }
};
const calculate_master = (fromDiscPer = false) => {
  let total_qty = 0;
  let total_total_mtr = 0;
  let total_sub_amt = 0;
  let total_disc_amt = 0;
  let total_taxable_amt = 0;
  let total_sgst_amt = 0;
  let total_cgst_amt = 0;
  let total_igst_amt = 0;
  let total_total_amt = 0;

  trans_data.forEach((value, index) => {
    const {
      prt_qty,
      prt_total_mtr,
      prt_amt,
      prt_disc_amt,
      prt_taxable_amt,
      prt_sgst_amt,
      prt_cgst_amt,
      prt_igst_amt,
      prt_total_amt,
    } = value;
    total_qty = parseInt(total_qty) + parseInt(prt_qty);
    if (isNaN(total_qty) || total_qty == "") total_qty = 0;

    total_total_mtr = parseFloat(total_total_mtr) + parseFloat(prt_total_mtr);
    if (isNaN(total_total_mtr) || total_total_mtr == "") total_total_mtr = 0;

    total_sub_amt = parseFloat(total_sub_amt) + parseFloat(prt_amt);
    if (isNaN(total_sub_amt) || total_sub_amt == "") total_sub_amt = 0;

    total_disc_amt = parseFloat(total_disc_amt) + parseFloat(prt_disc_amt);
    if (isNaN(total_disc_amt) || total_disc_amt == "") total_disc_amt = 0;

    total_taxable_amt =
      parseFloat(total_taxable_amt) + parseFloat(prt_taxable_amt);
    if (isNaN(total_taxable_amt) || total_taxable_amt == "")
      total_taxable_amt = 0;

    total_sgst_amt = parseFloat(total_sgst_amt) + parseFloat(prt_sgst_amt);
    if (isNaN(total_sgst_amt) || total_sgst_amt == "") total_sgst_amt = 0;

    total_cgst_amt = parseFloat(total_cgst_amt) + parseFloat(prt_cgst_amt);
    if (isNaN(total_cgst_amt) || total_cgst_amt == "") total_cgst_amt = 0;

    total_igst_amt = parseFloat(total_igst_amt) + parseFloat(prt_igst_amt);
    if (isNaN(total_igst_amt) || total_igst_amt == "") total_igst_amt = 0;

    total_total_amt = parseFloat(total_total_amt) + parseFloat(prt_total_amt);
    if (isNaN(total_total_amt) || total_total_amt == "") total_total_amt = 0;
  });
  $("#prm_total_qty").val(total_qty);
  $("#prm_total_mtr").val(total_total_mtr.toFixed(2));
  $("#prm_sub_amt").val(total_sub_amt.toFixed(2));
  $("#prm_disc_amt").val(total_disc_amt.toFixed(2));
  $("#prm_taxable_amt").val(total_taxable_amt.toFixed(2));
  $("#prm_sgst_amt").val(total_sgst_amt.toFixed(2));
  $("#prm_cgst_amt").val(total_cgst_amt.toFixed(2));
  $("#prm_igst_amt").val(total_igst_amt.toFixed(2));

  let bill_disc_per = $(`#prm_bill_disc_per`).val();
  if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;

  let bill_disc_amt = $(`#prm_bill_disc_amt`).val();
  if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;

  if (fromDiscPer) {
    bill_disc_amt =
      (parseFloat(total_total_amt) * parseFloat(bill_disc_per)) / 100;
    if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;
    $(`#prm_bill_disc_amt`).val(bill_disc_amt.toFixed(0));
  } else {
    bill_disc_per =
      (parseFloat(bill_disc_amt) * 100) / parseFloat(total_total_amt);
    if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;
    $(`#prm_bill_disc_per`).val(bill_disc_per.toFixed(2));
  }

  let total_amt = parseFloat(total_total_amt) - parseFloat(bill_disc_amt);
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;

  let after_decimal = parseFloat("0." + total_amt.toString().split(".")[1]);
  after_decimal = after_decimal.toFixed(2);
  after_decimal = after_decimal == 1 ? 0 : after_decimal;
  $("#prm_round_off").val(after_decimal);

  $("#prm_total_amt").val(Math.round(total_amt));

  if (total_amt > 0) {
    $(".master_block_btn").prop("disabled", false);
    notifier("prm_total_mtr");
    notifier("prm_total_amt");
  } else {
    $(".master_block_btn").prop("disabled", true);
    notifier("prm_total_mtr", "Required");
    notifier("prm_total_amt", "Required");
  }
};
const check_transaction = () => {
  let flag = true;
  if (trans_data.length > 0) {
    trans_data.forEach((value) => {
      const { prt_mtr, prt_total_mtr, prt_amt } = value;
      if (prt_mtr == 0 || prt_mtr == "") {
        prt_id = id;
        flag = false;
      } else if (prt_mtr < 0) {
        prt_id = id;
        flag = false;
      } else {
      }

      if (prt_total_mtr == 0 || prt_total_mtr == "") {
        prt_id = id;
        flag = false;
      } else if (prt_total_mtr < 0) {
        prt_id = id;
        flag = false;
      } else {
      }

      if (prt_amt == 0 || prt_amt == "") {
        prt_id = id;
        flag = false;
      } else if (prt_amt < 0) {
        prt_id = id;
        flag = false;
      } else {
      }
    });
  }
  return flag;
};
const add_wrapper_data = (data, append = false) => {
  const {
    prt_bm_id,
    bill_no,
    bill_date,
    qrcode,
    fabric_name,
    design_name,
    color_name,
    width_name,
    hsn_name,
    design_image,
    prt_qty,
    prt_mtr,
    prt_total_mtr,
    prt_rate,
    prt_amt,
    prt_disc_per,
    prt_disc_amt,
    prt_taxable_amt,
    prt_sgst_per,
    prt_sgst_amt,
    prt_cgst_per,
    prt_cgst_amt,
    prt_igst_per,
    prt_igst_amt,
    prt_total_amt,
    isExist,
  } = data;
  let tr = `<tr id="row_${prt_bm_id}">
                <td id="qrcode_${prt_bm_id}">${qrcode}</td>
                <td id="bill_no_${prt_bm_id}">${bill_no}</td>
                <td id="bill_date_${prt_bm_id}">${bill_date}</td>
                <td id="fabric_name_${prt_bm_id}">${fabric_name}</td>
                <td id="design_name_${prt_bm_id}">${design_name}</td>
                <td id="color_name_${prt_bm_id}">${color_name}</td>
                <td id="width_name_${prt_bm_id}">${width_name}</td>
                <td id="hsn_name_${prt_bm_id}">${hsn_name}</td>
                <td id="qty_${prt_bm_id}">${prt_qty}</td>
                <td id="mtr_${prt_bm_id}">${prt_mtr}</td>
                <td id="total_mtr_${prt_bm_id}">${prt_total_mtr}</td>
                <td id="rate_${prt_bm_id}">${prt_rate}</td>
                <td id="amt_${prt_bm_id}">${prt_amt}</td>
                <td id="disc_per_${prt_bm_id}">${prt_disc_per}</td>
                <td id="disc_amt_${prt_bm_id}">${prt_disc_amt}</td>
                <td id="taxable_amt_${prt_bm_id}">${prt_taxable_amt}</td>
                <td id="sgst_per_${prt_bm_id}">${prt_sgst_per}</td>
                <td id="sgst_amt_${prt_bm_id}">${prt_sgst_amt}</td>
                <td id="cgst_per_${prt_bm_id}">${prt_cgst_per}</td>
                <td id="cgst_amt_${prt_bm_id}">${prt_cgst_amt}</td>
                <td id="igst_per_${prt_bm_id}">${prt_igst_per}</td>
                <td id="igst_amt_${prt_bm_id}">${prt_igst_amt}</td>
                <td id="total_amt_${prt_bm_id}">${prt_total_amt}</td>
                <td>
                    <div class="navigationn_wrapper">
                        <div class="navigationn">
                            <div class="menuToggle" id="menu_toggle_${prt_bm_id}" onclick="toggle_menuu(this)"></div>
                            <div class="menuu">
                                <ul>
                                    <li>
                                        <a 
                                            type="button" 
                                            class="btn btn-md" 
                                            onclick="edit_transaction(${prt_bm_id})"
                                            ><i class="text-success fa fa-edit"></i></a>
                                    </li>
                                    <li>
                                        ${
                                          isExist
                                            ? `<button 
                                                type="button" 
                                                class="btn btn-md"
                                                ><i class="text-danger fa fa-ban"></i></button>`
                                            : `<a 
                                                type="button" 
                                                class="btn btn-md" 
                                                onclick="remove_transaction(${prt_bm_id})"
                                                ><i class="text-danger fa fa-trash"></i></a>`
                                        }
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>`;
  if (append) {
    $("#transaction_wrapper").append(tr);
  } else {
    $("#transaction_wrapper").prepend(tr);
  }
  $(`#row_${prt_bm_id}`).mouseover((event) => {
    $("#image-preview").html(`<img 
                                class="img-thumbnail pan form_loading" 
                                onclick="zoom_image(${prt_bm_id})" 
                                title="click to zoom in and zoom out" 
                                src="${LAZYLOADING}" 
                                data-src="${design_image}" 
                                data-big="${design_image}" 
                                style="max-width: 100%; max-height: 100%; object-fit: contain;"
                            />`);
    lazy_loading("form_loading");
  });
  $(`#row_${prt_bm_id}`).mouseout(() => {
    $("#image-preview").html(``);
  });
};
const add_edit = () => {
  event.preventDefault();
  remove_transaction_notifier();
  let check = true;
  let required_row = true;
  if (!check_transaction()) {
    required_row = false;
  }
  if ($(`#prm_supplier_id`).val() == null) {
    notifier(`prm_supplier_id`, "Required");
    check = false;
  }
  if ($(`#prm_total_mtr`).val() <= 0 || $(`#prm_total_mtr`).val() == "") {
    notifier(`prm_total_mtr`, "Required");
    check = false;
  }
  if ($(`#prm_total_amt`).val() <= 0 || $(`#prm_total_amt`).val() == "") {
    notifier(`prm_total_amt`, "Required");
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
  } else {
    const path = `${link}/${sub_link}/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=add_edit`;
    form_data += `&trans_data=${JSON.stringify(trans_data)}`;
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        const { data, msg } = resp;
        if (handle_response(resp)) {
          if (id == 0) {
          } else {
          }
          window.location.reload();
          remove_transaction_notifier();
          toastr.success("", msg, {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
          });
          $("body, html").animate({ scrollTop: 0 }, 1000);
        }
      },
      (errmsg) => {}
    );
  }
};
const remove_transaction_notifier = () => {
  notifier(`prm_supplier_id`);
  notifier(`prm_total_mtr`);
  notifier(`prm_total_amt`);
};
const remove_master_notifier = () => {
  notifier("fabric_id");
  notifier("design_id");
  notifier("color_id");
  notifier("qty");
  notifier("mtr");
  notifier("total_mtr");
  notifier("rate");
  notifier("amt");
};
const remove_transaction = (prt_bm_id) => {
  trans_data = trans_data.filter((value) => value.prt_bm_id != prt_bm_id);
  let qrcode = $(`#qrcode_${prt_bm_id}`).html();
  toastr.success(`${qrcode}`, "ITEM REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#row_${prt_bm_id}`).detach();
  $("#transaction_count").html(trans_data.length);
  calculate_master();
};
const edit_transaction = (prt_bm_id) => {
  const find = trans_data.find((value) => value["prt_bm_id"] == prt_bm_id);
  const { bal_mtr, prt_mtr } = find;
  let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                  <p class="text-uppercase text-center font-weight-bold">change mtr</p>
                </div>`;
  let data = `<div class="row pt-1">
                  <div class="col-12">
                    <div class="d-flex flex-wrap form-group floating-form">
                      <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                        <input 
                          type="number" 
                          class="form-control floating-input" 
                          id="bal_mtr" 
                          value="${bal_mtr}"
                          readonly
                        />   
                        <label class="text-uppercase">balance mtr</label>
                        <small class="form-text text-muted helper-text" id="bal_mtr_msg"></small>
                      </div>
                      <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label">
                        <input 
                          type="number" 
                          class="form-control floating-input" 
                          id="return_mtr" 
                          name="return_mtr" 
                          value="${prt_mtr}"
                          onkeyup="check_bal_mtr()"
                          placeholder=" " 
                          autocomplete="off"
                        />   
                        <label class="text-uppercase">return_mtr</label>
                        <small class="form-text text-muted helper-text" id="return_mtr_msg"></small>
                      </div>
                  </div>              
                </div>`;
  let btn = `<button 
                type="button" 
                class="btn btn-sm btn-primary" 
                id="sbt_btn" 
                onclick="update_mtr(${prt_bm_id})" 
                style="width:15%;"
              >
                <div class="stage d-none"><div class="dot-flashing"></div></div>
                <div class="dot-text text-primary text-uppercase">update</div>
              </button>
              <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>`;
  $(".modal-title-sm").html(title);
  $(".modal-body-sm").html(data);
  $(".modal-footer-sm").html(btn);
  $("#popup_modal_sm").modal("show");
  setTimeout(() => {
    $("#return_mtr").focus();
  }, RELOAD_TIME);
  toggle_menuu({ id: prt_bm_id });
};
const check_bal_mtr = () => {
  let bal_mtr = $("#bal_mtr").val();
  if (isNaN(bal_mtr) || bal_mtr == "") bal_mtr = 0;

  let return_mtr = parseValue($("#return_mtr").val());
  if (isNaN(return_mtr) || return_mtr == "") return_mtr = 0;

  notifier("return_mtr");
  $("#sbt_btn").prop("disabled", false);
  if (return_mtr <= 0) {
    notifier("return_mtr", "Invalid return mtr");
    $("#sbt_btn").prop("disabled", true);
    return false;
  }
  if (return_mtr > bal_mtr) {
    notifier("return_mtr", "Return mtr should be less than balance mtr.");
    $("#sbt_btn").prop("disabled", true);
    return false;
  }
  return true;
};
const update_mtr = (prt_bm_id) => {
  if (!check_bal_mtr()) return false;
  let return_mtr = parseValue($("#return_mtr").val());
  if (isNaN(return_mtr) || return_mtr == "") return_mtr = 0;
  let index = trans_data.findIndex((value) => value.prt_bm_id == prt_bm_id);
  if (index < 0) return false;
  trans_data[index].prt_mtr = return_mtr;
  trans_data[index].prt_total_mtr = return_mtr;
  $(`#mtr_${prt_bm_id}`).html(return_mtr.toFixed(2));
  $(`#total_mtr_${prt_bm_id}`).html(return_mtr.toFixed(2));
  calculate_transaction(prt_bm_id);
  calculate_master(true);
  toastr.success(
    `${trans_data[index]["qrcode"]}`,
    "RETURN MTR UPDATED SUCCESSFULLY.",
    {
      closeButton: true,
      progressBar: true,
    }
  );
  $("#popup_modal_sm").modal("hide");
};
const calculate_transaction = (prt_bm_id) => {
  let index = trans_data.findIndex((value) => value.prt_bm_id == prt_bm_id);
  if (index < 0) return false;
  const {
    prt_qty,
    prt_mtr,
    prt_rate,
    prt_disc_per,
    prt_sgst_per,
    prt_cgst_per,
    prt_igst_per,
  } = trans_data[index];

  let total_mtr = parseFloat(prt_qty) * parseFloat(prt_mtr);
  if (isNaN(total_mtr) || total_mtr == "") total_mtr = 0;
  trans_data[index].prt_total_mtr = total_mtr;
  $(`#total_mtr_${prt_bm_id}`).html(total_mtr.toFixed(2));

  let amt = parseFloat(prt_rate) * parseFloat(total_mtr);
  if (isNaN(amt) || amt == "") amt = 0;
  trans_data[index].prt_amt = amt;
  $(`#amt_${prt_bm_id}`).html(amt.toFixed(2));

  let disc_amt = (parseFloat(amt) * parseFloat(prt_disc_per)) / 100;
  if (isNaN(disc_amt) || disc_amt == "") disc_amt = 0;
  trans_data[index].prt_disc_amt = disc_amt;
  $(`#disc_amt_${prt_bm_id}`).html(disc_amt.toFixed(2));

  let taxable_amt = parseFloat(amt) - parseFloat(disc_amt);
  if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
  trans_data[index].prt_taxable_amt = taxable_amt;
  $(`#taxable_amt_${prt_bm_id}`).html(taxable_amt.toFixed(2));

  let sgst_amt = (parseFloat(taxable_amt) * parseFloat(prt_sgst_per)) / 100;
  if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;
  trans_data[index].prt_sgst_amt = sgst_amt;
  $(`#sgst_amt_${prt_bm_id}`).html(sgst_amt.toFixed(2));

  let cgst_amt = (parseFloat(taxable_amt) * parseFloat(prt_cgst_per)) / 100;
  if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
  trans_data[index].prt_cgst_amt = cgst_amt;
  $(`#cgst_amt_${prt_bm_id}`).html(cgst_amt.toFixed(2));

  let igst_amt = (parseFloat(taxable_amt) * parseFloat(prt_igst_per)) / 100;
  if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
  trans_data[index].prt_igst_amt = igst_amt;
  $(`#igst_amt_${prt_bm_id}`).html(igst_amt.toFixed(2));

  let total_amt =
    parseFloat(taxable_amt) +
    parseFloat(sgst_amt) +
    parseFloat(cgst_amt) +
    parseFloat(igst_amt);
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;
  trans_data[index].prt_total_amt = total_amt;
  $(`#total_amt_${prt_bm_id}`).html(total_amt.toFixed(2));
};
const record_remove = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.prm_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                  <tbody>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">entry no : </td>
                          <td width="70%">${data.prm_entry_no}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">entry date : </td>
                          <td width="70%">${data.prm_entry_date}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">supplier : </td>
                          <td width="70%">${data.supplier_name}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">total mtr : </td>
                          <td width="70%">${data.prm_total_mtr}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">total amt : </td>
                          <td width="70%">${data.prm_total_amt}</td>
                      </tr>
                  </tbody>
              </table>`;
  remove_datav3({ path, form_data, html });
};
// core_functions

// additional_functions
const get_barcode_data = (id) => {
  if (id == null || id == "" || id == 0) return false;
  let find = trans_data.find((value) => value.prt_bm_id == id);
  if (find != undefined) {
    toastr.error("Barcode already added.", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    setTimeout(() => {
      $("#bm_id").val(null).trigger("change");
      $("#bm_id").select2("open");
    }, RELOAD_TIME);
    return false;
  }
  const supplier_id = $("#prm_supplier_id").val();
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "get_barcode_data", id, supplier_id };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        if (data && data.length != 0) {
          data.forEach((value) => {
            trans_data.push(value);
            add_wrapper_data(value);
            if (supplier_id == 0) {
              $("#prm_supplier_id").val(value["supplier_id"]);
              $("#supplier_name").val(value["supplier_name"]);
            }
          });
          $("#transaction_count").html(trans_data.length);
          calculate_transaction(id);
          calculate_master(false);
          console.log({ trans_data });
        }
        toastr.success(data[0]["bm_item_code"], msg, {
          closeButton: true,
          progressBar: true,
          preventDuplicates: true,
        });
        setTimeout(() => {
          $("#bm_id").val(null).trigger("change");
          $("#bm_id").select2("open");
        }, RELOAD_TIME);
      } else {
        setTimeout(() => {
          $("#bm_id").val(null).trigger("change");
          $("#bm_id").select2("open");
        }, RELOAD_TIME);
      }
    },
    (errmsg) => {
      setTimeout(() => {
        $("#bm_id").val(null).trigger("change");
        $("#bm_id").select2("open");
      }, RELOAD_TIME);
    }
  );
};
// additional_functions
