$(document).ready(function () {
  $(`#qm_customer_id`)
    .select2(
      select2_default({
        url: `master/customer/get_select2/_id`, 
        placeholder: "select",
        param: true,
      })
    ).on("change", (event) => get_customer_data(event.target.value)); 
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
              
              $("#sku_transaction_count").html($('#sku_transaction_wrapper > tr').length);
              calculate_master();
          }
        },
        (errmsg) => {}
      );
    }
  }
};

const set_item_disc = () => {
  const amt = parseFloat($("#qm_sub_amt").val());
  const disc_amt = parseFloat($("#qm_bill_disc_amt").val());
  let disc_per = parseFloat(amt) > 0 ? (parseFloat(disc_amt) * 100) / parseFloat(amt) : 0;
  if (isNaN(disc_per) || disc_per == "") disc_per = 0;
  $("#qm_bill_disc_per").val(disc_per.toFixed(2));
  $("#qm_bill_disc_amt").val(disc_amt.toFixed(2));
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

   sku_trans_data.forEach((value, index) => {
    let sgst_amt = 0;
    let cgst_amt = 0;
    let igst_amt = 0;

    let taxable_amt = parseFloat(value['qt_amt']) - parseFloat(value['qt_disc_amt']);
    if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
    
    if ($(`#qm_bill_type`).is(":checked")) {
      let deduct_amt =(parseFloat(taxable_amt) * value['qt_igst_per']) /(100 + parseFloat(value['qt_igst_per']));
      if (isNaN(deduct_amt) || deduct_amt == "") deduct_amt = 0;
      taxable_amt = parseFloat(taxable_amt) - parseFloat(deduct_amt);
      if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
    }

    if ($("#qm_gst_type").val() == 0) { // WITHIN
      sgst_amt = (parseFloat(taxable_amt) * parseFloat(value['qt_sgst_per'])) / 100;
      if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;

      cgst_amt = (parseFloat(taxable_amt) * parseFloat(value['qt_cgst_per'])) / 100;
      if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
    } else { // OUTSIDE
      igst_amt = (parseFloat(taxable_amt) * parseFloat(value['qt_igst_per'])) / 100;
      if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
    }
    let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
    if (isNaN(total_amt) || total_amt == "") total_amt = 0;

    taxable_amt = taxable_amt > 0 ? taxable_amt.toFixed(2) : 0.0;
    sgst_amt = sgst_amt > 0 ? sgst_amt.toFixed(2) : 0.0;
    cgst_amt = cgst_amt > 0 ? cgst_amt.toFixed(2) : 0.0;
    igst_amt = igst_amt > 0 ? igst_amt.toFixed(2) : 0.0;
    total_amt = total_amt > 0 ? total_amt.toFixed(2) : 0.0;

    sku_trans_data[index].qt_taxable_amt= taxable_amt;
    sku_trans_data[index].qt_sgst_amt   = sgst_amt;
    sku_trans_data[index].qt_cgst_amt   = cgst_amt;
    sku_trans_data[index].qt_igst_amt   = igst_amt;
    sku_trans_data[index].qt_total_amt  = total_amt;

    $(`#sku_taxable_amt_${value['qt_id']}`).html(taxable_amt);
    $(`#sku_sgst_amt_${value['qt_id']}`).html(sgst_amt);
    $(`#sku_cgst_amt_${value['qt_id']}`).html(cgst_amt);
    $(`#sku_igst_amt_${value['qt_id']}`).html(igst_amt);
    $(`#sku_total_amt_${value['qt_id']}`).html(total_amt);

    total_qty = parseInt(total_qty) + parseInt(value['qt_qty']);
    if (isNaN(total_qty) || total_qty == "") total_qty = 0;

    total_sub_amt = parseFloat(total_sub_amt) + parseFloat(value['qt_amt']);
    if (isNaN(total_sub_amt) || total_sub_amt == "") total_sub_amt = 0;

    total_disc_amt = parseFloat(total_disc_amt) + parseFloat(value['qt_disc_amt']);
    if (isNaN(total_disc_amt) || total_disc_amt == "") total_disc_amt = 0;

    total_taxable_amt = parseFloat(total_taxable_amt) + parseFloat(value['qt_taxable_amt']);
    if (isNaN(total_taxable_amt) || total_taxable_amt == "") total_taxable_amt = 0;

    total_sgst_amt = parseFloat(total_sgst_amt) + parseFloat(value['qt_sgst_amt']);
    if (isNaN(total_sgst_amt) || total_sgst_amt == "") total_sgst_amt = 0;

    total_cgst_amt = parseFloat(total_cgst_amt) + parseFloat(value['qt_cgst_amt']);
    if (isNaN(total_cgst_amt) || total_cgst_amt == "") total_cgst_amt = 0;

    total_igst_amt = parseFloat(total_igst_amt) + parseFloat(value['qt_igst_amt']);
    if (isNaN(total_igst_amt) || total_igst_amt == "") total_igst_amt = 0;

    total_total_amt = parseFloat(total_total_amt) + parseFloat(value['qt_total_amt']);
    if (isNaN(total_total_amt) || total_total_amt == "") total_total_amt = 0;
  });

  $("#qm_total_qty").val(total_qty);
  // $("#qm_total_mtr").val(total_mtr.toFixed(2));
  $("#qm_sub_amt").val(total_sub_amt.toFixed(2));
  $("#qm_disc_amt").val(total_disc_amt.toFixed(2));
  $("#qm_taxable_amt").val(total_taxable_amt.toFixed(2));
  $("#qm_sgst_amt").val(total_sgst_amt.toFixed(2));
  $("#qm_cgst_amt").val(total_cgst_amt.toFixed(2));
  $("#qm_igst_amt").val(total_igst_amt.toFixed(2));
  $("#qm_total_amt").val(total_total_amt.toFixed(2));

  let bill_disc_per = $(`#qm_bill_disc_per`).val();
  if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;

  let bill_disc_amt = $(`#qm_bill_disc_amt`).val();
  if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;

  if (fromDiscPer) {
    bill_disc_amt =
      (parseFloat(total_total_amt) * parseFloat(bill_disc_per)) / 100;
    if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;
    $(`#qm_bill_disc_amt`).val(bill_disc_amt.toFixed(2));
  } else {
    bill_disc_per =
      (parseFloat(bill_disc_amt) * 100) / parseFloat(total_total_amt);
    if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;
    $(`#qm_bill_disc_per`).val(bill_disc_per.toFixed(2));
  }
  let round_off = $(`#qm_round_off`).val();
  if (isNaN(round_off) || round_off == "") round_off = 0;

  let total_amt =
    parseFloat(total_total_amt) -
    (parseFloat(round_off) + parseFloat(bill_disc_amt));
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;
  $("#qm_total_amt").val(total_amt.toFixed(2));

  let advance_amt = parseFloat($(`#qm_advance_amt`).val());
  if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;

  let balance_amt = parseFloat(total_amt) - parseFloat(advance_amt);
  if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;
  $("#qm_balance_amt").val(balance_amt.toFixed(2));

  if (total_amt > 0) {
    $(".master_block_btn").prop("disabled", false);
    notifier("qm_total_amt");
  } else {
    $(".master_block_btn").prop("disabled", true);
    notifier("qm_total_amt", "Required");
  }
};

const add_edit = () => {
  event.preventDefault();
  remove_transaction_notifier();
  let check = true;
  let required_row = true;
  // if (!check_transaction()) {
  //   required_row = false;
  // }
  // if ($(`#qm_billing_id`).val() == null) {
  //   notifier(`qm_billing_id`, "Required");
  //   check = false;
  // }
  if ($(`#qm_customer_id`).val() == null) {
    notifier(`qm_customer_id`, "Required");
    check = false;
  }
  if ($(`#qm_total_amt`).val() <= 0 || $(`#qm_total_amt`).val() == "") {
    notifier(`qm_total_amt`, "Required");
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
    // form_data.append("trans_data", JSON.stringify(trans_data));
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
          // Swal.fire({
          //   title: '<span class="text-info">Do you want to print bill?</span>',
          //   icon: "info",
          //   showCancelButton: true,
          //   confirmButtonText: "Yes",
          // }).then((result) => {
          //   if (result.isConfirmed) {
          //     window.open(
          //       `${base_url}/${link}/${sub_link}?action=print&id=${data.id}`,
          //       "_blank",
          //       "width=1024, height=768"
          //     );
          //   }
          //   window.location.reload();
          // });
          
          setTimeout(() => {
            window.location.reload();
          }, RELOAD_TIME);

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
  // notifier(`qm_billing_id`);
  notifier(`qm_customer_id`);
  notifier(`qm_total_amt`);
};

const remove_transaction = (qt_id) => {
  trans_data = trans_data.filter((value) => value.qt_id != qt_id);
  let trans_type = $(`#trans_type_${qt_id}`).html();
  toastr.success(`${trans_type}`, "ITEM REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#row_${qt_id}`).detach();
  $("#transaction_count").html(trans_data.length);
  calculate_master();
};

const set_bill_type = () => {
  calculate_sku_transaction();
  calculate_master(true);
};

const record_remove = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.qm_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                <tbody>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">entry no : </td>
                    <td width="70%">${data.qm_entry_no}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">entry date : </td>
                    <td width="70%">${data.qm_entry_date}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">customer : </td>
                    <td width="70%">${data.customer_name}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">total qty : </td>
                    <td width="70%">${data.qm_total_qty}</td>
                  </tr>
                  <tr>
                    <td class="font-weight-bold" width="30%" align="right">total amt : </td>
                    <td width="70%">${data.qm_total_amt}</td>
                  </tr>
              </tbody>
            </table>`;
  remove_datav3({ path, form_data, html });
};
// core_functions

// additional_functions
const get_customer_data = (id) => {
  $("#qm_gst_type").val(0);
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
            $("#qm_gst_type").val(data[0]["gst_type"]);
            set_bill_type();
            // if($(`#qm_customer_id`).val()==null){
            //   let cname = $(`#qm_billing_id :selected`).text();
            //   $(`#qm_customer_id`).html(`<option value="${id}">${cname}</option>`);
            // }
          }
        }
      },
      (errmsg) => {}
    );
  }
};


