$(document).ready(function () {
  $(`#om_billing_id`)
    .select2(
      select2_default({
        url: `master/customer/get_select2/_id`,
        placeholder: "select",
        param: true,
      })
    ) 
    .on("change", (event) => get_customer_data(event.target.value)); 

  $(`#om_customer_id`).select2(
    select2_default({
      url: `master/customer/get_select2/_id`,
      placeholder: "select",
      param: true,
    })
  );
  $(`#om_salesman_id`).select2(
    select2_default({
      url: `${link}/${sub_link}/get_select2/_user_id`,
      placeholder: "select",
      param: true,
      param1:()=>'salesman'
    })
  );

  $(`#om_master_id`).select2(
    select2_default({
      url: `${link}/${sub_link}/get_select2/_user_id`,
      placeholder: "select",
      param: true,
      param1:()=>'master'
    })
  );


});
// core_functions

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
            const {sku_data, package_data} = data;
              if (sku_data && sku_data.length != 0) {
                sku_trans_data = sku_data;
                sku_trans_data.forEach((value) => add_sku_wrapper_data(value, true));
              }
              if (package_data && package_data.length != 0) {
                trans_data = package_data;
                let result = paginate(trans_data, page);
                if (result && result.length != 0) result.forEach((value) => add_wrapper_data(value, true));
              }

              $("#sku_transaction_count").html($('#sku_transaction_wrapper > tr').length);
              $("#transaction_count").html(trans_data.length);
              calculate_master();
          }
        },
        (errmsg) => {}
      );
    }
  }
};

const set_item_disc = () => {
  const amt = parseFloat($("#om_sub_amt").val());
  const disc_amt = parseFloat($("#om_bill_disc_amt").val());

  let disc_per = parseFloat(amt) > 0 ? (parseFloat(disc_amt) * 100) / parseFloat(amt) : 0;
  if (isNaN(disc_per) || disc_per == "") disc_per = 0;

  $("#om_bill_disc_per").val(disc_per.toFixed(2));
  $("#om_bill_disc_amt").val(disc_amt.toFixed(2));
};

const calculate_master = (fromDiscPer = false) => {
  let total_qty = 0;
  let total_mtr = 0;
  let total_sub_amt = 0;
  let total_disc_amt = 0;
  let total_taxable_amt = 0;
  let total_sgst_amt = 0;
  let total_cgst_amt = 0;
  let total_igst_amt = 0;
  let total_total_amt = 0;
  trans_data.forEach((value, index) => {
    const {
      ot_id,
      ot_qty,
      ot_total_mtr,
      ot_amt,
      ot_disc_amt,
      ot_sgst_per,
      ot_cgst_per,
      ot_igst_per,
    } = value;

    let sgst_amt = 0;
    let cgst_amt = 0;
    let igst_amt = 0;

    let taxable_amt = parseFloat(ot_amt) - parseFloat(ot_disc_amt);
    if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;

    if ($(`#om_bill_type`).is(":checked")) {
      let deduct_amt =
        (parseFloat(taxable_amt) * ot_igst_per) /
        (100 + parseFloat(ot_igst_per));
      if (isNaN(deduct_amt) || deduct_amt == "") deduct_amt = 0;
      taxable_amt = parseFloat(taxable_amt) - parseFloat(deduct_amt);
      if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
    }

    if ($("#om_gst_type").val() == 0) {
      // WITHIN
      sgst_amt = (parseFloat(taxable_amt) * parseFloat(ot_sgst_per)) / 100;
      if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;

      cgst_amt = (parseFloat(taxable_amt) * parseFloat(ot_cgst_per)) / 100;
      if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
    } else {
      // OUTSIDE
      igst_amt = (parseFloat(taxable_amt) * parseFloat(ot_igst_per)) / 100;
      if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
    }
    let total_amt =
      parseFloat(taxable_amt) +
      parseFloat(sgst_amt) +
      parseFloat(cgst_amt) +
      parseFloat(igst_amt);
    if (isNaN(total_amt) || total_amt == "") total_amt = 0;

    taxable_amt = taxable_amt > 0 ? taxable_amt.toFixed(2) : 0.0;
    sgst_amt = sgst_amt > 0 ? sgst_amt.toFixed(2) : 0.0;
    cgst_amt = cgst_amt > 0 ? cgst_amt.toFixed(2) : 0.0;
    igst_amt = igst_amt > 0 ? igst_amt.toFixed(2) : 0.0;
    total_amt = total_amt > 0 ? total_amt.toFixed(2) : 0.0;

    trans_data[index].ot_taxable_amt = taxable_amt;
    trans_data[index].ot_sgst_amt = sgst_amt;
    trans_data[index].ot_cgst_amt = cgst_amt;
    trans_data[index].ot_igst_amt = igst_amt;
    trans_data[index].ot_total_amt = total_amt;

    $(`#taxable_amt_${ot_id}`).html(taxable_amt);
    $(`#sgst_amt_${ot_id}`).html(sgst_amt);
    $(`#cgst_amt_${ot_id}`).html(cgst_amt);
    $(`#igst_amt_${ot_id}`).html(igst_amt);
    $(`#total_amt_${ot_id}`).html(total_amt);

    total_qty = parseInt(total_qty) + parseInt(ot_qty);
    if (isNaN(total_qty) || total_qty == "") total_qty = 0;

    total_mtr = parseFloat(total_mtr) + parseFloat(ot_total_mtr);
    if (isNaN(total_mtr) || total_mtr == "") total_mtr = 0;

    total_sub_amt = parseFloat(total_sub_amt) + parseFloat(ot_amt);
    if (isNaN(total_sub_amt) || total_sub_amt == "") total_sub_amt = 0;

    total_disc_amt = parseFloat(total_disc_amt) + parseFloat(ot_disc_amt);
    if (isNaN(total_disc_amt) || total_disc_amt == "") total_disc_amt = 0;

    total_taxable_amt = parseFloat(total_taxable_amt) + parseFloat(taxable_amt);
    if (isNaN(total_taxable_amt) || total_taxable_amt == "")
      total_taxable_amt = 0;

    total_sgst_amt = parseFloat(total_sgst_amt) + parseFloat(sgst_amt);
    if (isNaN(total_sgst_amt) || total_sgst_amt == "") total_sgst_amt = 0;

    total_cgst_amt = parseFloat(total_cgst_amt) + parseFloat(cgst_amt);
    if (isNaN(total_cgst_amt) || total_cgst_amt == "") total_cgst_amt = 0;

    total_igst_amt = parseFloat(total_igst_amt) + parseFloat(igst_amt);
    if (isNaN(total_igst_amt) || total_igst_amt == "") total_igst_amt = 0;

    total_total_amt = parseFloat(total_total_amt) + parseFloat(total_amt);
    if (isNaN(total_total_amt) || total_total_amt == "") total_total_amt = 0;
  });

   sku_trans_data.forEach((value, index) => {
    let sgst_amt = 0;
    let cgst_amt = 0;
    let igst_amt = 0;

    let taxable_amt = parseFloat(value['ot_amt']) - parseFloat(value['ot_disc_amt']);
    if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
    
    if ($(`#om_bill_type`).is(":checked")) {
      let deduct_amt =(parseFloat(taxable_amt) * value['ot_igst_per']) /(100 + parseFloat(value['ot_igst_per']));
      if (isNaN(deduct_amt) || deduct_amt == "") deduct_amt = 0;
      taxable_amt = parseFloat(taxable_amt) - parseFloat(deduct_amt);
      if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
    }

    if ($("#om_gst_type").val() == 0) { // WITHIN
      sgst_amt = (parseFloat(taxable_amt) * parseFloat(value['ot_sgst_per'])) / 100;
      if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;

      cgst_amt = (parseFloat(taxable_amt) * parseFloat(value['ot_cgst_per'])) / 100;
      if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
    } else { // OUTSIDE
      igst_amt = (parseFloat(taxable_amt) * parseFloat(value['ot_igst_per'])) / 100;
      if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
    }
    let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
    if (isNaN(total_amt) || total_amt == "") total_amt = 0;

    taxable_amt = taxable_amt > 0 ? taxable_amt.toFixed(2) : 0.0;
    sgst_amt = sgst_amt > 0 ? sgst_amt.toFixed(2) : 0.0;
    cgst_amt = cgst_amt > 0 ? cgst_amt.toFixed(2) : 0.0;
    igst_amt = igst_amt > 0 ? igst_amt.toFixed(2) : 0.0;
    total_amt = total_amt > 0 ? total_amt.toFixed(2) : 0.0;

    sku_trans_data[index].ot_taxable_amt= taxable_amt;
    sku_trans_data[index].ot_sgst_amt   = sgst_amt;
    sku_trans_data[index].ot_cgst_amt   = cgst_amt;
    sku_trans_data[index].ot_igst_amt   = igst_amt;
    sku_trans_data[index].ot_total_amt  = total_amt;

    $(`#sku_taxable_amt_${value['ot_id']}`).html(taxable_amt);
    $(`#sku_sgst_amt_${value['ot_id']}`).html(sgst_amt);
    $(`#sku_cgst_amt_${value['ot_id']}`).html(cgst_amt);
    $(`#sku_igst_amt_${value['ot_id']}`).html(igst_amt);
    $(`#sku_total_amt_${value['ot_id']}`).html(total_amt);

    total_qty = parseInt(total_qty) + parseInt(value['ot_apparel_qty']);
    if (isNaN(total_qty) || total_qty == "") total_qty = 0;

    total_mtr = parseFloat(total_mtr) + parseFloat(value['ot_sku_mtr']);
    if (isNaN(total_mtr) || total_mtr == "") total_mtr = 0;

    total_sub_amt = parseFloat(total_sub_amt) + parseFloat(value['ot_amt']);
    if (isNaN(total_sub_amt) || total_sub_amt == "") total_sub_amt = 0;

    total_disc_amt = parseFloat(total_disc_amt) + parseFloat(value['ot_disc_amt']);
    if (isNaN(total_disc_amt) || total_disc_amt == "") total_disc_amt = 0;

    total_taxable_amt = parseFloat(total_taxable_amt) + parseFloat(value['ot_taxable_amt']);
    if (isNaN(total_taxable_amt) || total_taxable_amt == "") total_taxable_amt = 0;

    total_sgst_amt = parseFloat(total_sgst_amt) + parseFloat(value['ot_sgst_amt']);
    if (isNaN(total_sgst_amt) || total_sgst_amt == "") total_sgst_amt = 0;

    total_cgst_amt = parseFloat(total_cgst_amt) + parseFloat(value['ot_cgst_amt']);
    if (isNaN(total_cgst_amt) || total_cgst_amt == "") total_cgst_amt = 0;

    total_igst_amt = parseFloat(total_igst_amt) + parseFloat(value['ot_igst_amt']);
    if (isNaN(total_igst_amt) || total_igst_amt == "") total_igst_amt = 0;

    total_total_amt = parseFloat(total_total_amt) + parseFloat(value['ot_total_amt']);
    if (isNaN(total_total_amt) || total_total_amt == "") total_total_amt = 0;
  });

  $("#om_total_qty").val(total_qty);
  $("#om_total_mtr").val(total_mtr.toFixed(2));
  $("#om_sub_amt").val(total_sub_amt.toFixed(2));
  $("#om_disc_amt").val(total_disc_amt.toFixed(2));
  $("#om_taxable_amt").val(total_taxable_amt.toFixed(2));
  $("#om_sgst_amt").val(total_sgst_amt.toFixed(2));
  $("#om_cgst_amt").val(total_cgst_amt.toFixed(2));
  $("#om_igst_amt").val(total_igst_amt.toFixed(2));
  $("#om_total_amt").val(total_total_amt.toFixed(2));

  let bill_disc_per = $(`#om_bill_disc_per`).val();
  if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;

  let bill_disc_amt = $(`#om_bill_disc_amt`).val();
  if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;

  if (fromDiscPer) {
    bill_disc_amt =
      (parseFloat(total_total_amt) * parseFloat(bill_disc_per)) / 100;
    if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;
    $(`#om_bill_disc_amt`).val(bill_disc_amt.toFixed(2));
  } else {
    bill_disc_per =
      (parseFloat(bill_disc_amt) * 100) / parseFloat(total_total_amt);
    if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;
    $(`#om_bill_disc_per`).val(bill_disc_per.toFixed(2));
  }
  let round_off = $(`#om_round_off`).val();
  if (isNaN(round_off) || round_off == "") round_off = 0;

  let total_amt =
    parseFloat(total_total_amt) -
    (parseFloat(round_off) + parseFloat(bill_disc_amt));
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;
  $("#om_total_amt").val(total_amt.toFixed(2));

  let advance_amt = parseFloat($(`#om_advance_amt`).val());
  if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;

  let balance_amt = parseFloat(total_amt) - parseFloat(advance_amt);
  if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;
  $("#om_balance_amt").val(balance_amt.toFixed(2));

  if (total_amt > 0) {
    $(".master_block_btn").prop("disabled", false);
    notifier("om_total_amt");
  } else {
    $(".master_block_btn").prop("disabled", true);
    notifier("om_total_amt", "Required");
  }
};

const add_edit = () => {
  event.preventDefault();
  remove_transaction_notifier();
  let check = true;
  let required_row = true;
  if (!check_transaction()) {
    required_row = false;
  }
  if ($(`#om_billing_id`).val() == null) {
    notifier(`om_billing_id`, "Required");
    check = false;
  }
  if ($(`#om_customer_id`).val() == null) {
    notifier(`om_customer_id`, "Required");
    check = false;
  }
  if ($(`#om_total_amt`).val() <= 0 || $(`#om_total_amt`).val() == "") {
    notifier(`om_total_amt`, "Required");
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
     form_data.append("sku_trans_data", JSON.stringify(sku_trans_data));
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
          Swal.fire({
            title: '<span class="text-info">Do you want to print bill?</span>',
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Yes",
          }).then((result) => {
            if (result.isConfirmed) {
              window.open(
                `${base_url}/${link}/${sub_link}?action=print&id=${data.id}`,
                "_blank",
                "width=1024, height=768"
              );
            }
            window.location.reload();
          });
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
  notifier(`om_billing_id`);
  notifier(`om_customer_id`);
  notifier(`om_total_amt`);
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
const remove_transaction = (ot_id) => {
  trans_data = trans_data.filter((value) => value.ot_id != ot_id);
  let trans_type = $(`#trans_type_${ot_id}`).html();
  toastr.success(`${trans_type}`, "ITEM REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#row_${ot_id}`).detach();
  $("#transaction_count").html(trans_data.length);
  calculate_master();
};

const set_bill_type = () => {
  calculate_transaction();
  calculate_master(true);
};
const record_remove = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.om_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                <tbody>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">order no : </td>
                    <td width="70%">${data.om_entry_no}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">order date : </td>
                    <td width="70%">${data.om_entry_date}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">customer : </td>
                    <td width="70%">${data.billing_name}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">total qty : </td>
                    <td width="70%">${data.om_total_qty}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">total mtr : </td>
                    <td width="70%">${data.om_total_mtr}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">total amt : </td>
                    <td width="70%">${data.om_total_amt}</td>
                  </tr>
              </tbody>
            </table>`;
  remove_datav3({ path, form_data, html });
};
// core_functions

// additional_functions
const get_customer_data = (id) => {
  $("#om_gst_type").val(0);
  set_bill_type();
  if (id) {
    const path = `${link}/${sub_link}/handler`;
    const form_data = { func: "get_customer_data", id };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $("#om_gst_type").val(data[0]["gst_type"]);
            set_bill_type();
            if($(`#om_customer_id`).val()==null){
              let cname = $(`#om_billing_id :selected`).text();
              $(`#om_customer_id`).html(`<option value="${id}">${cname}</option>`);
            }
          }
        }
      },
      (errmsg) => {}
    );
  }
};

// payment_mode
const get_payment_mode_data = () => {
  let title = `<p>payment mode</p>`;
  let subtitle = `<div class="d-flex justify-content-around">
                    <p class="d-flex flex-column justify-content-around">
                      <span class="pb-1 border-bottom">total amt</span>
                      <span class="_total_amt">0</span>
                    </p>
                    <p class="d-flex flex-column justify-content-around">
                      <span class="pb-1 border-bottom">advance amt</span>
                      <span class="_advance_amt">0</span>
                    </p>
                    <p class="d-flex flex-column justify-content-around">
                      <span class="pb-1 border-bottom">balance amt</span>
                      <span class="_balance_amt">0</span>
                    </p>
                  </div>`;
  let body = ``;
  let footer = `<button 
                  type="button" 
                  id="sbt_btn" 
                  class="btn btn-md btn-secondary btn-block text-uppercase mx-3" 
                  onclick="toggle_payment_mode_popup()"
              >close</button>`;
  $(`#payment_mode_wrapper .right-panel-title`).html(title);
  $(`#payment_mode_wrapper .right-panel-subtitle`).html(subtitle);
  $(`#payment_mode_wrapper .right-panel-body`).html(body);
  $(`#payment_mode_wrapper .right-panel-footer`).html(footer);
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "get_payment_mode_data", id: $("#id").val() };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        if (data && data.length != 0) {
          const { pay_modes } = get_pay_modes(data);
          let body = `<div class="row pt-2">
                        <div class="col-12">
                            <div style="max-height: 50vh; overflow-x: auto;">
                                <table class="table table-sm w-100">
                                    <tbody id="payment_mode_tbody">
                                        ${pay_modes}
                                    </tbody>
                                </table>
                            </div>              
                        </div>              
                    </div> `;
          $(`#payment_mode_wrapper .right-panel-body `).html(body);
        }
      }
    },
    (errmsg) => {
      console.log(errmsg);
    }
  );
};
const get_pay_modes = (data) => {
  let pay_modes = ``;
  let advance_amt = 0;
  data.forEach((row) => {
    const { opmt_id, opmt_amt, opmt_payment_mode_id, payment_mode_name } = row;
    advance_amt = parseFloat(advance_amt) + parseFloat(opmt_amt);
    if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;

    pay_modes += `<tr id="rowpm_${opmt_payment_mode_id}">
                      <td width="10%" class="border-0 font-weight-bold"></td>
                      <td width="30%" class="border-0 font-weight-bold">${payment_mode_name} : </td>
                      <td width="50%" class="border-0 floating-label">
                        <input 
                          type="hidden"
                          id="opmt_id_${opmt_payment_mode_id}" 
                          name="opmt_id[${opmt_payment_mode_id}]" 
                          value="${opmt_id}" 
                        />
                        <input 
                          type="hidden"
                          id="opmt_payment_mode_id_${opmt_payment_mode_id}" 
                          name="opmt_payment_mode_id[${opmt_payment_mode_id}]" 
                          value="${opmt_payment_mode_id}" 
                        />
                        <input 
                          type="number" 
                          class="form-control floating-input" 
                          id="opmt_amt_${opmt_payment_mode_id}" 
                          name="opmt_amt[${opmt_payment_mode_id}]" 
                          value="${opmt_amt}"
                          onkeyup="calculate_advance_amt()"
                          placeholder=" " 
                          autocomplete="off" 
                        />
                      </td>
                      <td width="10%" class="border-0 font-weight-bold"></td>
                    </tr>`;
  });
  return { advance_amt, pay_modes };
};
const toggle_payment_mode_popup = () => {
  if ($(`#payment_mode_wrapper .right-panel`).hasClass("active")) {
    $(`#payment_mode_wrapper,  #payment_mode_wrapper .right-panel`).removeClass(
      "active"
    );
  } else {
    $(`#payment_mode_wrapper, #payment_mode_wrapper .right-panel`).addClass(
      "active"
    );

    let total_amt = $(`#om_total_amt`).val();
    if (isNaN(total_amt) || total_amt == "") total_amt = 0;
    $(`._total_amt`).html(total_amt);

    let advance_amt = $(`#om_advance_amt`).val();
    if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;
    $(`._advance_amt`).html(advance_amt);

    let balance_amt = parseFloat(total_amt) - parseFloat(advance_amt);
    if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;
    $(`._balance_amt`).html(balance_amt);
  }
};
const calculate_advance_amt = () => {
  let advance_amt = 0;
  for (let i = 1; i <= $("#payment_mode_tbody > tr").length; i++) {
    let cnt = $(`#payment_mode_tbody > tr:nth-child(${i})`).attr("id");
    let explode = cnt.split("_");
    let id = explode[1];

    let amt = $(`#opmt_amt_${id}`).val();
    if (isNaN(amt) || amt == "") amt = 0;

    advance_amt = parseFloat(advance_amt) + parseFloat(amt);
    if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;
  }
  $("._advance_amt").html(advance_amt);
  $(`#om_advance_amt`).val(advance_amt);

  let total_amt = $(`#om_total_amt`).val();
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;

  let balance_amt = parseFloat(total_amt) - parseFloat(advance_amt);
  if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;
  $("._balance_amt").html(balance_amt);
  $(`#om_balance_amt`).val(balance_amt);

  if (balance_amt >= 0) {
    // $("#sbt_btn").prop("disabled", false);
    $("._balance_amt").removeClass("text-danger");
  } else {
    // $("#sbt_btn").prop("disabled", true);
    $("._balance_amt").addClass("text-danger");
  }
  calculate_master();
};
// payment_mode


