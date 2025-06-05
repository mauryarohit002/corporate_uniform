$(document).ready(function () {
  $(`#sku_id`).select2(select2_default({
    url: `master/sku/get_select2/_id`,
    placeholder: "select",
    param: true,
  })).on("change", (event) => get_sku_data(event.target.value));
});

// core_functions
  let sku_trans_data = [];
  let design_data = [];
  const calculate_sku_transaction = (fromDiscPer = false) => {
    let sku_qty = parseInt($("#sku_qty").val());
    if (isNaN(sku_qty) || sku_qty == "") sku_qty = 0;

    let sku_mrp = parseFloat($("#sku_mrp").val());
    if (isNaN(sku_mrp) || sku_mrp == "") sku_mrp = 0;

    let sku_amt = parseFloat(sku_qty) * parseFloat(sku_mrp);
    if (isNaN(sku_amt) || sku_amt == "") sku_amt = 0;
    $("#sku_amt").val(sku_amt.toFixed(2));

    let disc_per = parseFloat($("#sku_disc_per").val());
    if (isNaN(disc_per) || disc_per == "") disc_per = 0;
    let disc_amt = parseFloat($("#sku_disc_amt").val());
    if (isNaN(disc_amt) || disc_amt == "") disc_amt = 0;

    if (fromDiscPer) {
      disc_amt = (parseFloat(sku_amt) * parseFloat(disc_per)) / 100;
      if (isNaN(disc_amt) || disc_amt == "") disc_amt = 0;

      if (disc_amt == 0) $(`#sku_disc_amt`).val(0);
      if (disc_amt > 0) $(`#sku_disc_amt`).val(disc_amt.toFixed(2));
    } else {
      disc_per = parseFloat(sku_amt) > 0 ? (parseFloat(disc_amt) * 100) / parseFloat(sku_amt) : 0;
      if (isNaN(disc_per) || disc_per == "") disc_per = 0;
      if (disc_per == 0) $(`#sku_disc_per`).val(0);
      if (disc_per > 0) $(`#sku_disc_per`).val(disc_per.toFixed(2));
    }

    let taxable_amt = parseFloat(sku_amt) - parseFloat(disc_amt);
    if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;

    let sgst_per = 2.50;
    let cgst_per = 2.50;
    let igst_per = 5.00;

    if(taxable_amt > 1000){
      sgst_per = 6.00;
      cgst_per = 6.00;
      igst_per = 12.00;
    }

    $("#sku_sgst_per").val(sgst_per.toFixed(2));
    if (isNaN(sgst_per) || sgst_per == "") sgst_per = 0;

    $("#sku_cgst_per").val(cgst_per.toFixed(2));
    if (isNaN(cgst_per) || cgst_per == "") cgst_per = 0;

    $("#sku_igst_per").val(igst_per.toFixed(2));
    if (isNaN(igst_per) || igst_per == "") igst_per = 0;

    if ($(`#om_bill_type`).is(":checked")) {
      let deduct_amt = (parseFloat(taxable_amt) * igst_per) / (100 + parseFloat(igst_per));
      if (isNaN(deduct_amt) || deduct_amt == "") deduct_amt = 0;

      taxable_amt = parseFloat(taxable_amt) - parseFloat(deduct_amt);
      if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
    }
    $("#sku_taxable_amt").val(taxable_amt.toFixed(2));

    let sgst_amt = 0;
    let cgst_amt = 0;
    let igst_amt = 0;

    if ($("#om_gst_type").val() == 0) { // WITHIN
      sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
      if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;

      cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
      if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
    } else { // OUTSIDE
      igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
      if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
    }
    $("#sku_sgst_amt").val(sgst_amt.toFixed(2));
    $("#sku_cgst_amt").val(cgst_amt.toFixed(2));
    $("#sku_igst_amt").val(igst_amt.toFixed(2));
    let total_amt = parseFloat(taxable_amt) + parseFloat(sgst_amt) + parseFloat(cgst_amt) + parseFloat(igst_amt);
    if (isNaN(total_amt) || total_amt == "") total_amt = 0;
    $("#sku_total_amt").val(total_amt.toFixed(2));
  };
  const check_sku_transaction = () => {
    let ot_id = 0;
    let bm_id = 0;
    let flag = true;
    if (sku_trans_data.length > 0) {
      sku_trans_data.forEach((value) => {
        if(value['ot_ot_id'] == 0){
          if (value['ot_apparel_qty'] == 0 || value['ot_apparel_qty'] == "") {
            ot_id = id;
            flag = false;
          } else if (value['ot_apparel_qty'] < 0) {
            ot_id = id;
            flag = false;
          } else {
          }
  
          if (value['ot_sku_mtr'] == 0 || value['ot_sku_mtr'] == "") {
            ot_id = id;
            flag = false;
          } else if (value['ot_sku_mtr'] < 0) {
            ot_id = id;
            flag = false;
          } else {
          }
  
          if (value['ot_apparel_mrp'] == 0 || value['ot_apparel_mrp'] == "") {
            ot_id = id;
            flag = false;
          } else if (value['ot_apparel_mrp'] < 0) {
            ot_id = id;
            flag = false;
          } else {
          }
        }
      });
    }
    if (!flag) {
      if (ot_id != 0 && bm_id != 0) {
        // $(`#bm_roll_no_${bm_id}`).focus();
        // if ($(`#bm_roll_no_${bm_id}`).length) {
        //   $(window).scrollTop(
        //     $(`#bm_roll_no_${bm_id}`).offset().top - $(window).height() / 2
        //   );
        // }
      }
    }
    return flag;
  };
  const add_sku_transaction = () => {
    remove_sku_notifier();
    let check = true;
    if ($("#om_memo_no").val() == '') {
      notifier("om_memo_no", "Required");
      check = false;
      $("body, html").animate({ scrollTop: 0 }, 1000);
    }
    if ($("#om_billing_id").val() == null) {
      notifier("om_billing_id", "Required");
      check = false;
      $("body, html").animate({ scrollTop: 0 }, 1000);
    }
    if ($("#om_customer_id").val() == null) {
      notifier("om_customer_id", "Required");
      check = false;
      $("body, html").animate({ scrollTop: 0 }, 1000);
    }
    if ($("#sku_id").val() == null) {
      notifier("sku_id", "Required");
      check = false;
    }
    ['sku_mtr', 'sku_qty', 'sku_mrp'].forEach(field => {
      if ($(`#${field}`).val() == "" || $(`#${field}`).val() == 0) {
        notifier(`${field}`, "Required");
        check = false;
      } else {
        if ($(`#${field}`).val() < 0) {
          notifier(`${field}`, "Invalid.");
          check = false;
        }
      }
    }); 
    if ($("#sku_amt").val() == "" || $("#sku_amt").val() == 0) {
      notifier("sku_amt", "Required");
      check = false;
    } else {
      if ($("#sku_amt").val() < 0) {
        notifier("sku_amt", "Invalid amt");
        check = false;
      }
    }
    if ($("#sku_disc_per").val() == "" || $("#sku_disc_per").val() == 0) {
      // notifier("sku_disc_per", "Required");
      // check = false;
    } else {
      if ($("#sku_disc_per").val() < 0) {
        notifier("sku_disc_per", "Invalid disc per");
        check = false;
      }
    }
    if ($("#sku_disc_amt").val() == "" || $("#sku_disc_amt").val() == 0) {
      // notifier("sku_disc_amt", "Required");
      // check = false;
    } else {
      if ($("#sku_disc_amt").val() < 0) {
        notifier("sku_disc_amt", "Invalid disc amt");
        check = false;
      }
    }
    if ($("#sku_taxable_amt").val() == "" || $("#sku_taxable_amt").val() == 0) {
      notifier("sku_taxable_amt", "Required");
      check = false;
    } else {
      if ($("#sku_taxable_amt").val() < 0) {
        notifier("sku_taxable_amt", "Invalid taxable amt");
        check = false;
      }
    }
    if ($("#sku_sgst_amt").val() == "" || $("#sku_sgst_amt").val() == 0) {
      // notifier("sku_sgst_amt", "Required");
      // check = false;
    } else {
      if ($("#sku_sgst_amt").val() < 0) {
        notifier("sku_sgst_amt", "Invalid sgst amt");
        check = false;
      }
    }
    if ($("#sku_cgst_amt").val() == "" || $("#sku_cgst_amt").val() == 0) {
      // notifier("sku_cgst_amt", "Required");
      // check = false;
    } else {
      if ($("#sku_cgst_amt").val() < 0) {
        notifier("sku_cgst_amt", "Invalid cgst amt");
        check = false;
      }
    }
    if ($("#sku_igst_amt").val() == "" || $("#sku_igst_amt").val() == 0) {
      // notifier("sku_igst_amt", "Required");
      // check = false;
    } else {
      if ($("#sku_igst_amt").val() < 0) {
        notifier("sku_igst_amt", "Invalid igst amt");
        check = false;
      }
    }
    if ($("#sku_sgst_per").val() == "" || $("#sku_sgst_per").val() == 0) {
      // notifier("sku_sgst_per", "Required");
      // check = false;
    } else {
      if ($("#sku_sgst_per").val() < 0) {
        notifier("sgst_per", "Invalid sgst per");
        check = false;
      }
    }
    if ($("#sku_cgst_per").val() == "" || $("#sku_cgst_per").val() == 0) {
      // notifier("sku_cgst_per", "Required");
      // check = false;
    } else {
      if ($("#sku_cgst_per").val() < 0) {
        notifier("cgst_per", "Invalid cgst per");
        check = false;
      }
    }
    if ($("#sku_igst_per").val() == "" || $("#sku_igst_per").val() == 0) {
      // notifier("sku_igst_per", "Required");
      // check = false;
    } else {
      if ($("#sku_igst_per").val() < 0) {
        notifier("sku_igst_per", "Invalid igst per");
        check = false;
      }
    }
    if ($("#sku_total_amt").val() == "" || $("#sku_total_amt").val() == 0) {
      notifier("sku_total_amt", "Required");
      check = false;
    } else {
      if ($("#sku_total_amt").val() < 0) {
        notifier("sku_total_amt", "Invalid total amt");
        check = false;
      }
    }
    if (!check) {
      toastr.error("You forgot to enter some information.", "Oh snap!!!", {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
      });
      return false;
    }
    let ot_id = $("#ot_id").val();
    const path = `${link}/${sub_link}/handler`;
    let form_id = document.getElementById("_form");
    let form_data = new FormData(form_id);
    form_data.append("func", "add_sku_transaction");
    form_data.append("design_data", JSON.stringify(design_data));
    fileUpAjaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          design_data = [];
          if (data && data.length != 0) {
            if (ot_id == 0) {
              let _ot_id = 0;
              data.forEach((value) => {
                sku_trans_data.unshift(value);
                if(parseInt(value['ot_ot_id']) == 0){
                  _ot_id = value['ot_id']
                  add_sku_wrapper_data(value);
                }
              });
              get_sku_measurement_data(_ot_id);
              toastr.success(`${$("#sku_id :selected").text()}`, "ITEM ADDED TO LIST.", { closeButton: true, progressBar: true });
            } else {
              data.forEach((value) => {
                let index = sku_trans_data.findIndex((item) => item.ot_id == value.ot_id);
                if (index < 0) toastr.success(`Transaction not found`, "", { closeButton: true, progressBar: true,});
                sku_trans_data[index].sku_name       = value["sku_name"];
                sku_trans_data[index].ot_sku_id      = value["ot_sku_id"];
                sku_trans_data[index].ot_sku_mtr      = value["ot_sku_mtr"];
                sku_trans_data[index].ot_apparel_qty = value["ot_apparel_qty"];
                sku_trans_data[index].ot_apparel_mrp = value["ot_apparel_mrp"];
                sku_trans_data[index].ot_apparel_amt = value["ot_apparel_amt"];
  
                sku_trans_data[index].ot_amt         = value["ot_amt"];
                sku_trans_data[index].ot_disc_per    = value["ot_disc_per"];
                sku_trans_data[index].ot_disc_amt    = value["ot_disc_amt"];
                sku_trans_data[index].ot_taxable_amt = value["ot_taxable_amt"];
                sku_trans_data[index].ot_sgst_per    = value["ot_sgst_per"];
                sku_trans_data[index].ot_sgst_amt    = value["ot_sgst_amt"];
                sku_trans_data[index].ot_cgst_per    = value["ot_cgst_per"];
                sku_trans_data[index].ot_cgst_amt    = value["ot_cgst_amt"];
                sku_trans_data[index].ot_igst_per    = value["ot_igst_per"];
                sku_trans_data[index].ot_igst_amt    = value["ot_igst_amt"];
                sku_trans_data[index].ot_total_amt   = value["ot_total_amt"];
                sku_trans_data[index].ot_description = value["ot_description"];
                sku_trans_data[index].design_data    = value['design_data'];
                if(parseInt(value['ot_ot_id']) == 0){
                  $(`#sku_name_${ot_id}`).html(value["sku_name"]);
                  $(`#sku_mtr_${ot_id}`).html(value["ot_sku_mtr"]);
                  $(`#sku_qty_${ot_id}`).html(value["ot_apparel_qty"]);
                  $(`#sku_mrp_${ot_id}`).html(value["ot_apparel_mrp"]);
                  $(`#sku_amt_${ot_id}`).html(value["ot_apparel_amt"]);
                  
                  $(`#sku_disc_per_${ot_id}`).html(value["ot_disc_per"]);
                  $(`#sku_disc_amt_${ot_id}`).html(value["ot_disc_amt"]);
                  $(`#sku_taxable_amt_${ot_id}`).html(value["ot_taxable_amt"]);
                  $(`#sku_sgst_per_${ot_id}`).html(value["ot_sgst_per"]);
                  $(`#sku_sgst_amt_${ot_id}`).html(value["ot_sgst_amt"]);
                  $(`#sku_cgst_per_${ot_id}`).html(value["ot_cgst_per"]);
                  $(`#sku_cgst_amt_${ot_id}`).html(value["ot_cgst_amt"]);
                  $(`#sku_igst_per_${ot_id}`).html(value["ot_igst_per"]);
                  $(`#sku_igst_amt_${ot_id}`).html(value["ot_igst_amt"]);
                  $(`#sku_total_amt_${ot_id}`).html(value["ot_total_amt"]);
                  $(`#sku_description_${ot_id}`).html(value["ot_description"]);
                }
              });
              toastr.success('', `${$("#sku_id :selected").text()} UPDATED TO LIST.`, { closeButton: true, progressBar: true });
              $("#ot_id").val(0);
            }
            $("#sku_id").val(null).trigger("change");
            $("#sku_id").select2("close");
            calculate_sku_transaction(true);
            calculate_master();
            set_item_disc();
            $("#sku_transaction_count").html($('#sku_transaction_wrapper > tr').length);
          }
          
        }else{
          const { data } = resp;
          if(data && data == 1) sku_mtr_popup();  
        }
      },
      (errmsg) => {}
    );
  };
  const add_sku_wrapper_data = (data, append = false) => {
    if(parseInt(data['ot_ot_id']) == 0){
      let tr = `<tr id="rowsku_${data['ot_id']}">
                  <td>
                    <span id="sku_name_${data['ot_id']}">${data['sku_name']}</span>
                    <span>${data['apparels']}</span>
                  </td>
                  <td id="sku_mtr_${data['ot_id']}">${data['ot_sku_mtr']}</td>
                  <td id="sku_mrp_${data['ot_id']}">${data['ot_apparel_mrp']}</td>
                  <td id="sku_amt_${data['ot_id']}">${data['ot_apparel_amt']}</td>
                  <td id="sku_disc_per_${data['ot_id']}">${data['ot_disc_per']}</td>
                  <td id="sku_disc_amt_${data['ot_id']}">${data['ot_disc_amt']}</td>
                  <td id="sku_taxable_amt_${data['ot_id']}">${data['ot_taxable_amt']}</td>
                  <td id="sku_sgst_per_${data['ot_id']}">${data['ot_sgst_per']}</td>
                  <td id="sku_sgst_amt_${data['ot_id']}">${data['ot_sgst_amt']}</td>
                  <td id="sku_cgst_per_${data['ot_id']}">${data['ot_cgst_per']}</td>
                  <td id="sku_cgst_amt_${data['ot_id']}">${data['ot_cgst_amt']}</td>
                  <td id="sku_igst_per_${data['ot_id']}">${data['ot_igst_per']}</td>
                  <td id="sku_igst_amt_${data['ot_id']}">${data['ot_igst_amt']}</td>
                  <td id="sku_total_amt_${data['ot_id']}">${data['ot_total_amt']}</td>
                  <td id="sku_description_${data['ot_id']}">${data['ot_description']}</td>
                <td>
                        <div class="navigationn_wrapper">
                          <div class="navigationn">
                            <div class="menuToggle" id="menu_toggle_${data['ot_id']}" onclick="toggle_menuu(this)"></div>
                            <div class="menuu">
                              <ul>
                                <li>
                                  <a 
                                    type="button" 
                                    class="btn btn-md" 
                                    target="_blank"
                                    href="${base_url}/${link}/${sub_link}/measurement_print/${data['ot_om_id']}/${data['ot_id']}"
                                    ><i class="text-info fa fa-print"></i></a>
                                </li>
                                ${
                                  (data['ot_apparel_id'] > 0)
                                  ? `<li>
                                        <a 
                                          type="button" 
                                          class="btn btn-md" 
                                          onclick="get_sku_measurement_data(${data['ot_id']})"
                                          ><i class="text-info fa fa-eye"></i></a>
                                      </li>`
                                  : ``
                                }
                                ${
                                  data['isExist']
                                  ? ``
                                  : `<li>
                                        <a 
                                          type="button" 
                                          class="btn btn-md" 
                                          onclick="edit_sku_transaction(${data['ot_id']})"
                                          ><i class="text-success fa fa-edit"></i></a>
                                    </li>`
                                }
                                <li>
                                  ${
                                    data['isExist']
                                      ? `<button 
                                          type="button" 
                                          class="btn btn-md"
                                          ><i class="text-danger fa fa-ban"></i></button>`
                                      : `<a 
                                          type="button" 
                                          class="btn btn-md" 
                                          onclick="remove_sku_transaction(${data['ot_id']})"
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
        $("#sku_transaction_wrapper").append(tr);
      } else {
        $("#sku_transaction_wrapper").prepend(tr);
      }
  
      $(`#rowsku_${data['ot_id']}`).mouseover((event) => {
        $("#sku-image-preview").html(`<img 
                                    class="img-thumbnail pan form_loading" 
                                    onclick="zoom_image(${data['ot_id']})" 
                                    title="click to zoom in and zoom out" 
                                    src="${LAZYLOADING}" 
                                    data-src="${data['sku_image']}" 
                                    data-big="${data['sku_image']}" 
                                    onerror="this.onerror=null; this.src='${NOIMAGE}';"
                                    style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                />`);
        lazy_loading("form_loading");
      });
      $(`#rowsku_${data['ot_id']}`).mouseout(() => $("#sku-image-preview").html(``));
    }

  };
  const remove_sku_notifier = () => {
    notifier("om_memo_no");
    notifier("om_customer_id");
    notifier("om_billing_id");
    notifier("sku_id");
    notifier("sku_mtr");
    notifier("sku_qty");
    notifier("sku_mrp");
    notifier("sku_amt");
  };
  const remove_sku_transaction = (ot_id) => {
    sku_trans_data = sku_trans_data.filter((value) => value.ot_id != ot_id);
    let sku_name = $(`#sku_name_${ot_id}`).html();
    toastr.success(``, `${sku_name} REMOVED FROM LIST.`, {closeButton: true,progressBar: true,});
    $(`#rowsku_${ot_id}`).detach();
    $("#sku_transaction_count").html($('#sku_transaction_wrapper > tr').length);
    calculate_master();
  };
  const edit_sku_transaction = (ot_id) => { 
    const find = sku_trans_data.find((value) => value["ot_id"] == ot_id);
    $("#ot_id").val(find['ot_id']);
    find['ot_sku_id'] > 0 && $("#sku_id").html(`<option value="${find['ot_sku_id']}">${find['sku_name']}</option>`);
    $("#sku_mtr").val(find['ot_sku_mtr']);
    $("#sku_qty").val(find['ot_apparel_qty']);
    $("#sku_mrp").val(find['ot_apparel_mrp']);
    $("#sku_amt").val(find['ot_apparel_amt']);
    $("#sku_disc_per").val(find['ot_disc_per']);
    $("#sku_disc_amt").val(find['ot_disc_amt']);
    $("#sku_taxable_amt").val(find['ot_taxable_amt']);
    $("#sku_sgst_per").val(find['ot_sgst_per']);
    $("#sku_sgst_amt").val(find['ot_sgst_amt']);
    $("#sku_cgst_per").val(find['ot_cgst_per']);
    $("#sku_cgst_amt").val(find['ot_cgst_amt']);
    $("#sku_igst_per").val(find['ot_igst_per']);
    $("#sku_igst_amt").val(find['ot_igst_amt']);
    $("#sku_total_amt").val(find['ot_total_amt']);
    $("#sku_description").val(find['ot_description']);
    if(find['sku_image'] != ''){
      $(`#sku_image_span`).html(
        `<img 
            class="img-thumbnail pan form_loading" 
            width="150px" 
            onClick="zoom()" 
            title="click to zoom in and zoom out" 
            src="${LAZYLOADING}" 
            data-src="${find['sku_image']}" 
            data-big="${find['sku_image']}" 
            onerror="this.onerror=null; this.src='${NOIMAGE}';"
        />`
      );
    }
    toggle_menuu({ id: ot_id });
    lazy_loading('form_loading');
    if(find['ot_sku_id'] > 0){
      if(find['design_data'] && find['design_data'].length != 0){
        design_data = find['design_data'];
        $("#sku_mtr_btn").removeClass('d-none');
      }
    }
  };
// core_functions

// sku
  const get_sku_data = (id) => {
    design_data = [];
    $("#sku_mtr_btn").addClass('d-none');
    $("#sku_qty").val(1);
    $("#sku_mtr").val(0);
    $("#sku_mrp").val(0);
    $("#sku_amt").val(0);
    $("#sku_image_span").html(`<img class="img-thumbnail" width="150px" src="${NOIMAGE}" />`);
    calculate_sku_transaction();
    if (!id) return false;
    const path = `${link}/${sub_link}/handler`;
    const form_data = { func: "get_sku_data", id };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $("#sku_qty").val(1);
            $("#sku_mtr").val(data[0]["mtr"]);
            $("#sku_mrp").val(data[0]["mrp"]);
            $(`#sku_image_span`).html(
              `<img 
                  class="img-thumbnail pan form_loading" 
                  width="150px" 
                  onClick="zoom()" 
                  title="click to zoom in and zoom out" 
                  src="${LAZYLOADING}" 
                  data-src="${data[0][`image`]}" 
                  data-big="${data[0][`image`]}" 
                  onerror="this.onerror=null; this.src='${NOIMAGE}';"
              />`
            );
            calculate_sku_transaction();
            lazy_loading('form_loading');

            if(data[0]['design_data'] && data[0]['design_data'].length != 0){
              design_data = data[0]['design_data'];
              $("#sku_mtr_btn").removeClass('d-none');
            }
          }
        }
      },
      (errmsg) => {}
    );
  };
  const sku_mtr_popup = () => {
    const id = $('#sku_id').val();
    if(!id) {
      toastr.error('', 'SELECT SKU FIRST', {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
      });
      return false;
    }
    
    let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                  <p class="text-uppercase text-center font-weight-bold">fabric consumption of ${design_data[0]['sku_name']}</p>
                  <div>${get_sku_fabric_tab(design_data)}</div>
                </div>`;
    let body  = `<form class="form-horizontal" id="sku_mtr_form">
                  <div class="row pt-1">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                      <div class="card">
                        <div class="card-body p-0">${get_sku_fabric_content(design_data)}</div>
                      </div>
                    </div>
                  </div>
                  </form>`;
    let footer= `<button 
                    type="button" 
                    class="btn btn-sm btn-primary text-uppercase" 
                    id="sdt_btn" 
                    onclick="update_sku_mtr()" 
                    style="width:15%;"
                  >update</button>
                  <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>`;

    $(".modal-title-lg").html(title);
    $(".modal-body-lg").html(body);
    $(".modal-footer-lg").html(footer);
    $("#popup_modal_lg").modal("show");
    lazy_loading('popup_loading');
  }
  const get_sku_fabric_tab = data => {
    let content = `<p class="w-100 text-center text-danger text-uppercase font-weight-bold">fabric consumption not define in sku.</p>`;
    if(data.length == 0) return content;
    content = `<ul class="nav nav-pills nav-fill nav-pills-secondary w-100" id="pills-tab" role="tablist">`;
    data.forEach((value, index) => {
      content += `<li class="nav-item">
                    <a 
                      class="nav-link text-uppercase ${index == 0 ? 'active' : ''}" 
                      id="design_tab_${value['uuid']}" 
                      data-toggle="tab"
                      href="#design_content_${value['uuid']}" 
                      role="tab" 
                      aria-controls="design_content_${value['uuid']}" 
                      aria-selected="true"
                      style="font-size:0.8rem;"
                    >${value['design_name']} (${value['qrcode_data'].length})</a>
                </li>`;
    });
    content += '</ul>';
    return content;
  }
  const get_sku_fabric_content = data => {
    if(data.length == 0) return '';
    content = `<div class="tab-content" id="pills-tabContent">`;
    data.forEach((value, index) => {
      content += `<div 
                    class="tab-pane fade ${index == 0 ? 'show active' : ''}" 
                    id="design_content_${value['uuid']}" 
                    role="tabpanel" 
                    aria-labelledby="design_tab_${value['uuid']}"
                  >
                    <div class="d-flex flex-wrap">
                      <div class="col-12 col-sm-12 col-md-12 col-lg-4">${get_sku_design_content(value)}</div>
                      <div class="col-12 col-sm-12 col-md-12 col-lg-8">
                        <table class="table table-sm text-uppercase mt-2">
                          <tbody class="table-dark font-weight-bold">
                            <tr>
                              <td width="20%">
                                <label class="custom-control material-checkbox-secondary">
                                  <input 
                                    type="checkbox" 
                                    class="material-control-input-secondary" 
                                    id="design_checkbox_${value['uuid']}" 
                                    onclick="design_select_deselect('${value['uuid']}', 0)" 
                                    checked="checked"
                                  />
                                  <span class="material-control-indicator-secondary"></span>
                                  <span class="material-control-description-secondary">qrcode</span>
                                </label>
                              </td>
                              <td width="10%">available mtr</td>
                              <td width="10%">assign mtr</td>
                              <td width="10%">balance mtr</td>
                            </tr>
                          </tbody>
                          <tbody>${get_sku_design_barcode_content(value)}</tbody>
                        </table>
                      </div>
                    </div>
                  </div>`;
    });
    content += '</div>';
    return content;
  }
  const get_sku_design_content = value => {
    return `<div class="col-12 col-sm-12 col-md-12 col-lg-12 mt-2">
              <span class="d-flex justify-content-center" style="width: 14rem; height:12rem;">
                <img 
                  class="img-thumbnail pan popup_loading" 
                  onclick="zoom(this)" 
                  title="click to zoom in and zoom out" 
                  src="${LAZYLOADING}" 
                  data-src="${value['design_image']}" 
                  data-big="${value['design_image']}" 
                  style="max-width: 100%; max-height: 100%; object-fit: contain;"
                  onerror="this.onerror=null; this.src='${NOIMAGE}';"
                />
              </span>
              <table class="table table-sm text-uppercase" style="font-size: 0.8rem;">
                  <tbody>
                      <tr>
                          <th width="45%">design</th>
                          <th width="5%">:</th>
                          <td width="50%">${value['design_name']}</td>
                      </tr>
                      <tr>
                          <th width="45%">rate</th>
                          <th width="5%">:</th>
                          <td width="50%"><span id="osdt_rate_${value['uuid']}">${value['ot_fabric_rate']}</span></td>
                      </tr>
                      <tr>
                          <th width="45%">mtr</th>
                          <th width="5%">:</th>
                          <td width="50%">
                            <input 
                              type="number" 
                              class="form-control floating-input" 
                              id="osdt_mtr_${value['uuid']}"
                              value="${value['ot_fabric_mtr']}" 
                              onkeyup="calculate_sku_mtr()" 
                              placeholder=" " 
                              autocomplete="off" 
                              style="height: 20px;"
                            />    
                          </td>
                      </tr>
                      <tr>
                          <th width="45%">amt</th>
                          <th width="5%">:</th>
                          <td width="50%"><span id="osdt_amt_${value['uuid']}">${value['ot_fabric_amt']}</span></td>
                      </tr>
                      <tr>
                          <th width="45%">avail. mtr</th>
                          <th width="5%">:</th>
                          <td width="50%">${value['bal_qty']}</td>
                      </tr>
                      <tr>
                          <td width="100%" colspan="3"><span class="text-danger font-weight-bold" id="sdt_error_${value['uuid']}">${value['isErrorExist'] == 1 ? 'Mtr should be less than available mtr.' : ''}</span></td>
                      </tr>
                  </tbody>
              </table>
            </div>`;
  }
  const get_sku_design_barcode_content = value => {
    let content = `<tr><td class="text-center text-uppercase text-danger font-weight-bold" width="100%" colspan"5">no qrcode available</td></tr>`;
    if(value['qrcode_data'] && value['qrcode_data'].length == 0) return content;
    content = ``;
    value['qrcode_data'].forEach((item) => {
      content += `<tr>
                    <td width="20%">
                      <label class="custom-control material-checkbox">
                        <input 
                          type="checkbox" 
                          class="material-control-input design_checkboxes_${value['uuid']}" 
                          id="qrcode_${item['ot_bm_id']}"
                          onclick="design_select_deselect('${value['uuid']}', ${item['ot_bm_id']})" 
                          ${item['checked'] == 1 ? "checked" : ""}
                        />
                        <span class="material-control-indicator"></span>
                        <span class="material-control-description">${item['qrcode']}</span>
                      </label>
                    </td>
                    <td width="10%" id="avail_mtr_${item['ot_bm_id']}">${item['avail_mtr']}</td>
                    <td width="10%" id="assign_mtr_${item['ot_bm_id']}">${item['ot_fabric_mtr']}</td>
                    <td width="10%" id="bal_mtr_${item['ot_bm_id']}">${item['bal_mtr']}</td>
                  </tr>`
    });
    return content;
  }
  const design_select_deselect = (uuid, bm_id) => {
    let parent_checked = $(`#design_checkbox_${uuid}`).is(":checked");
    if (bm_id == 0) $(`.design_checkboxes_${uuid}`).prop("checked", parent_checked);
    let {count, checked_count} = get_total_checked_count(uuid);
    $(`#design_checkbox_${uuid}`).prop("checked", (count == checked_count));
    calculate_bal_mtr(uuid);
  };
  const get_total_checked_count = uuid => {
    const checkboxes = document.querySelectorAll(`.design_checkboxes_${uuid}`);
    const count = checkboxes.length;
    const checked_count = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
    return {count, checked_count};
  }
  const calculate_bal_mtr = (uuid) => {
    let defined_mtr = parseFloat($(`#osdt_mtr_${uuid}`).val());
    if (isNaN(defined_mtr) || defined_mtr == "") defined_mtr = 0;

    design_data.forEach((design) => {
      if(design['uuid'] == uuid){
        design.qrcode_data.forEach((qrcode) => {
          let assign_mtr= 0;
          let avail_mtr = parseFloat(qrcode['avail_mtr']);

          if($(`#qrcode_${qrcode['ot_bm_id']}`).is(':checked')){
            if(defined_mtr > 0){
              if(avail_mtr > 0){
                if(avail_mtr > defined_mtr){
                  assign_mtr = defined_mtr;
                }else{
                  assign_mtr = avail_mtr;
                }
              }
            }
          }
          defined_mtr = parseFloat(defined_mtr) - parseFloat(assign_mtr);
          if (isNaN(defined_mtr) || defined_mtr == "") defined_mtr = 0;

          bal_mtr = parseFloat(avail_mtr) - parseFloat(assign_mtr);
          if (isNaN(bal_mtr) || bal_mtr == "") bal_mtr = 0;
          
          $(`#assign_mtr_${qrcode['ot_bm_id']}`).html(assign_mtr.toFixed(2));
          $(`#bal_mtr_${qrcode['ot_bm_id']}`).html(bal_mtr.toFixed(2));
        });
      }
    });
  }
  const sku_mtr_popup1 = () => {
    const id = $('#sku_id').val();
    if(!id) {
      toastr.error('', 'SELECT SKU FIRST', {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
      });
      return false;
    }
    
    let content = ``
    design_data.forEach((value, index) => {
      content += `<div class="col-12 col-sm-12 col-md-6 col-lg-4">
                    <span class="d-flex justify-content-center my-4" style="width: 14rem; height:10rem;">
                        <img 
                            class="img-thumbnail pan popup_loading" 
                            onclick="zoom(this)" 
                            title="click to zoom in and zoom out" 
                            src="${LAZYLOADING}" 
                            data-src="${value['design_image']}" 
                            data-big="${value['design_image']}" 
                            style="max-width: 100%; max-height: 100%; object-fit: contain;"
                            onerror="this.onerror=null; this.src='${NOIMAGE}';"
                        />
                    </span>
                    <table class="table table-sm text-uppercase" style="font-size: 0.8rem;">
                        <tbody>
                            <tr>
                                <th width="45%">design</th>
                                <th width="5%">:</th>
                                <td width="50%">${value['design_name']}</td>
                            </tr>
                            <tr>
                                <th width="45%">rate</th>
                                <th width="5%">:</th>
                                <td width="50%"><span id="osdt_rate_${value['uuid']}">${value['ot_fabric_rate']}</span></td>
                            </tr>
                            <tr>
                                <th width="45%">mtr</th>
                                <th width="5%">:</th>
                                <td width="50%">
                                  <input 
                                    type="number" 
                                    class="form-control floating-input" 
                                    id="osdt_mtr_${value['uuid']}"
                                    value="${value['ot_fabric_mtr']}" 
                                    onkeyup="calculate_sku_mtr()" 
                                    placeholder=" " 
                                    autocomplete="off" 
                                    style="height: 20px;"
                                  />    
                                </td>
                            </tr>
                            <tr>
                                <th width="45%">amt</th>
                                <th width="5%">:</th>
                                <td width="50%"><span id="osdt_amt_${value['uuid']}">${value['ot_fabric_amt']}</span></td>
                            </tr>
                            <tr>
                                <th width="45%">avail. mtr</th>
                                <th width="5%">:</th>
                                <td width="50%">${value['bal_qty']}</td>
                            </tr>
                            <tr>
                                <td width="100%" colspan="3"><span class="text-danger font-weight-bold" id="sdt_error_${value['uuid']}">${value['isErrorExist'] == 1 ? 'Mtr should be less than available mtr.' : ''}</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>`;
    });
    let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                  <p class="text-uppercase text-center font-weight-bold">fabric consumption of ${design_data[0]['sku_name']}</p>
                </div>`;
    let body  = `<form class="form-horizontal" id="sku_mtr_form">
                  <div class="row pt-1">
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 d-flex flex-wrap">${content}</div>
                  </div>
                  </form>`;
    let footer= `<button 
                    type="button" 
                    class="btn btn-sm btn-primary text-uppercase" 
                    id="sdt_btn" 
                    onclick="update_sku_mtr()" 
                    style="width:15%;"
                  >update</button>
                  <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">CANCEL</button>`;

    $(".modal-title-lg").html(title);
    $(".modal-body-lg").html(body);
    $(".modal-footer-lg").html(footer);
    $("#popup_modal_lg").modal("show");
    lazy_loading('popup_loading');
  }
  const update_sku_mtr = () => {
    let total_mtr = 0;
    let flag = false;
    design_data.forEach((value, index) => {
      let mtr = parseFloat($(`#osdt_mtr_${value['uuid']}`).val());
      if (isNaN(mtr) || mtr == "") mtr = 0;
      design_data[index].ot_fabric_mtr = mtr;
  
      let rate = parseFloat(value['ot_fabric_rate']);
      if (isNaN(rate) || rate == "") rate = 0;
  
      let amt = parseFloat(mtr) * parseFloat(rate);
      if (isNaN(amt) || amt == "") amt = 0;
      amt = amt > 0 ? amt.toFixed(2) : 0.0;
      design_data[index].ot_fabric_amt = amt;
      $(`#osdt_amt_${value['uuid']}`).html(amt);
  
      $(`#sdt_error_${value['uuid']}`).html('');
      design_data[index].isErrorExist = 0;

      let total_assign_mtr = 0;
      value['qrcode_data'].forEach(qrcode => {
        let assign_mtr = parseFloat($(`#assign_mtr_${qrcode['ot_bm_id']}`).html());
        total_assign_mtr = parseFloat(total_assign_mtr) + parseFloat(assign_mtr);
        if (isNaN(total_assign_mtr) || total_assign_mtr == "") total_assign_mtr = 0;  
      });

      if(mtr > 0){
        if(mtr > parseFloat(value['bal_qty'])){
          $(`#sdt_error_${value['uuid']}`).html('Mtr should be less than available mtr.');
          design_data[index].isErrorExist = 1;
          flag = true
        }
      }else if(mtr == 0){
        $(`#sdt_error_${value['uuid']}`).html('Mtr should not be equal to zero.');
        design_data[index].isErrorExist = 1;
        flag = true
      }else{
        $(`#sdt_error_${value['uuid']}`).html('Mtr should not be less than zero.');
        design_data[index].isErrorExist = 1;
        flag = true
      }

      if(parseFloat(total_assign_mtr) <= 0){
        $(`#sdt_error_${value['uuid']}`).html('Qrcode not selected.');
        design_data[index].isErrorExist = 1;
        flag = true
        $(`#design_tab_${value['uuid']}`).tab('show');
      }else{
        if(parseFloat(total_assign_mtr) != parseFloat(mtr)){
          $(`#sdt_error_${value['uuid']}`).html('Mtr & total assign mtr not equal.');
          design_data[index].isErrorExist = 1;
          flag = true
          $(`#design_tab_${value['uuid']}`).tab('show');
        }
      }

      value['qrcode_data'].forEach(qrcode => {
        let assign_mtr= parseFloat($(`#assign_mtr_${qrcode['ot_bm_id']}`).html());
        let bal_mtr   = parseFloat($(`#bal_mtr_${qrcode['ot_bm_id']}`).html());

        qrcode['checked']       = assign_mtr > 0 ? 1 : 0;
        qrcode['ot_fabric_mtr'] = assign_mtr;
        qrcode['bal_mtr']       = bal_mtr;
      });
      
      total_mtr = parseFloat(total_mtr) + parseFloat(mtr);
      if (isNaN(total_mtr) || total_mtr == "") total_mtr = 0;
    });
    notifier("sku_mtr", total_mtr > 0 ? '' : 'Required');
    if(flag){
      toastr.error("You forgot to added some information.", "Oh Snap !!", {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
      });
      return;
    }
    if($('#sku_mtr').val() != total_mtr){
      toastr.success("Fabric consumption updated successfully.", "", {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
      });
    }
    $("#sku_mtr").val(total_mtr.toFixed(2));
    $("#popup_modal_lg").modal("hide");
    console.log({design_data});
  };
  const calculate_sku_mtr = () => {
    flag = false;
    design_data.forEach((value) => {
      let mtr = parseFloat($(`#osdt_mtr_${value['uuid']}`).val());
      if (isNaN(mtr) || mtr == "") mtr = 0;
  
      let rate = parseFloat(value['ot_fabric_rate']);
      if (isNaN(rate) || rate == "") rate = 0;
      
      let amt = parseFloat(mtr) * parseFloat(rate);
      if (isNaN(amt) || amt == "") amt = 0;
      amt = amt > 0 ? amt.toFixed(2) : 0.0;
      $(`#osdt_amt_${value['uuid']}`).html(amt);
      
      $(`#sdt_error_${value['uuid']}`).html('');
      if(mtr > 0){
        if(mtr > parseFloat(value['bal_qty'])){
          $(`#sdt_error_${value['uuid']}`).html('Mtr should be less than available mtr.');
          flag = true;
        }
      }else if(mtr == 0){
        $(`#sdt_error_${value['uuid']}`).html('Mtr should not be equal to zero.');
        flag = true;
      }else{
        $(`#sdt_error_${value['uuid']}`).html('Mtr should not be less than zero.');
        flag = true;
      }

      calculate_bal_mtr(value['uuid']);
    });
    $('#sdt_btn').prop("disabled", flag);
  }
// sku

// measurement
  const get_sku_measurement_data = (ot_id) => {
    toggle_menuu({ id: ot_id });
    const customer_id = $("#om_customer_id").val();
    notifier("om_customer_id");
    if (customer_id == null) {
      notifier("om_customer_id", "Required");
      toastr.error("Select customer first.", "", {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
      });
      return;
    }
    
    if(sku_trans_data.length <= 0) {
      toastr.error("Apparel not defined in sku.", "", {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
      });
      return;
    }
    const om_id = $("#id").val();
    const ids = [];
    sku_trans_data.forEach(value => {
      if(parseInt(value['ot_id']) == parseInt(ot_id)) {
        ids.push({ot_id : value['ot_id'], apparel_id : parseInt(value['ot_apparel_id']), apparel_name : value['apparel_name']})
      }
      if(parseInt(value['ot_ot_id']) == parseInt(ot_id)){
        ids.push({ot_id : value['ot_id'], apparel_id : parseInt(value['ot_apparel_id']), apparel_name : value['apparel_name']});
      }
    });
    const path = `${link}/${sub_link}/handler`;
    const form_data = {
      func: "get_sku_measurement_data",
      om_id,
      customer_id,
      ids: JSON.stringify(ids),
    };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if(data && data.length){
            let title = `<div class="px-2 mx-2" style="font-size: 0.8rem;">${get_sku_tab(data)}</div>`;
            let body  = `<div class="d-flex flex-column font-weight-bold" style="font-size: 0.8rem;">${get_sku_tab_content(data)}</div>`;
            let footer= `<button 
                            type="button" 
                            id="sbt_btn" 
                            class="btn btn-sm btn-secondary btn-block text-uppercase mx-3" 
                            onclick="add_edit_measurement_and_style(${ot_id})"
                          >add</button>
                          <button 
                            type="button" 
                            id="cnl_btn" 
                            class="btn btn-sm btn-secondary btn-block text-uppercase mx-3 mt-0" 
                            onclick="toggle_measurement_popup()"
                        >close</button>`;
            $(`#measurement_wrapper .top-panel-title`).html(title);
            $(`#measurement_wrapper .top-panel-body`).html(body);
            $(`#measurement_wrapper .top-panel-footer`).html(footer);
            toggle_measurement_popup();
            lazy_loading(`form_loading`);
          }
        }
      },
      (errmsg) => {}
    );
  };
  const get_sku_tab = data => {
    let html = `<ul class="nav nav-pills nav-fill nav-pills-secondary" id="pills-tab" role="tablist">`;
    data.forEach((value, index) => {
      const {apparel_data} = value;
      html += `<li class="nav-item">
                    <a 
                        class="nav-link ${index == 0 ? 'active' : ''} text-uppercase" 
                        id="apparel_${apparel_data['apparel_id']}_tab" 
                        data-toggle="tab"
                        href="#apparel_${apparel_data['apparel_id']}_content" 
                        role="tab" 
                        aria-controls="apparel_${apparel_data['apparel_id']}_content" 
                        aria-selected="${index == 0 ? true : false}"
                        style="font-size:0.8rem;"
                    >${apparel_data['apparel_name']}</a>
                </li>`;
    });
    html += `</ul>`;
    html += `<table class="table table-sm table-dark w-100 text-uppercase m-0">
                <tr>
                  <td class="text-left border-0" width="50%">memo no.: ${$("#om_memo_no").val()}</td>
                  <td class="text-right border-0" width="50%">customer : ${$("#om_customer_id :selected").text()}</td>
                </tr>
              </table>`;
    return html;
  }
  const get_sku_tab_content = data => { 
    let html = `<div class="tab-content" id="pills-tabContent">`;
    data.forEach((value, index) => {
      const {apparel_data, measurement_data, style_data, style_priority_data} = value;
      const measurement_bill_no   = measurement_data && measurement_data.length != 0 ? measurement_data[0]["bill_no"]: "";
      const measurement_bill_date = measurement_data && measurement_data.length != 0 ? measurement_data[0]["bill_date"] : "";
      const style_bill_no         = style_data && style_data.length != 0 ? style_data[0]["bill_no"] : "";
      const style_bill_date       = style_data && style_data.length != 0 ? style_data[0]["bill_date"] : "";
      let measurement_table       = get_measurement_table(measurement_data);
      let style_table             = get_style_table(style_data);
      let { style_priority_li, style_priority_tab } = get_style_priority(style_priority_data);
      html += `<div 
                class="tab-pane fade ${index == 0 ? 'show active' : ''}" 
                id="apparel_${apparel_data['apparel_id']}_content" 
                role="tabpanel" 
                aria-labelledby="apparel_${apparel_data['apparel_id']}_tab"
              >
                <div class="d-flex flex-column w-100 mb-2">
                  <div class="d-flex flex-wrap justify-content-between bg-secondary text-white text-uppercase p-2 m-0">
                    <h5 class="mb-0">
                      <a 
                        type="button" 
                        class="btn btn-sm btn-secondary" 
                        id="measurement_${apparel_data['apparel_id']}_tabs"
                        data-toggle="collapse" 
                        data-target="#measurement_${apparel_data['apparel_id']}_tab" 
                        aria-expanded="true" 
                        aria-controls="measurement_${apparel_data['apparel_id']}_tab"
                      >measurement</a>
                    </h5>
                    <span>${measurement_bill_no} / ${measurement_bill_date}</span>
                  </div>
                  <div 
                    id="measurement_${apparel_data['apparel_id']}_tab" 
                    class="collapse show" 
                    aria-labelledby="measurement_${apparel_data['apparel_id']}_tabs" 
                    data-parent="#accordion"
                  >
                    <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-form">
                      <div class="d-flex flex-wrap">${measurement_table}</div>
                    </div>
                  </div>
                </div>
                <div class="d-flex flex-column w-100">
                  <div class="d-flex justify-content-between">
                    <button 
                      type="button"
                      class="btn btn-md btn-secondary d-none" 
                      onclick="style_popup(${apparel_data['apparel_id']}, '${apparel_data['apparel_name']}', 'style_wrapper_${apparel_data['apparel_id']}')"
                    ><i class="text-info fa fa-plus"></i></button>
                    <ul class="nav nav-pills nav-fill nav-pills-secondary" id="pills-tab-${apparel_data['apparel_id']}" role="tablist" style="width: 97%;">
                      <li class="nav-item">
                        <a 
                          class="nav-link active text-uppercase d-flex flex-wrap justify-content-between" 
                          id="style_without_image_tab_${apparel_data['apparel_id']}" 
                          data-toggle="tab"
                          href="#style_without_image_content_${apparel_data['apparel_id']}" 
                          role="tab" 
                          aria-controls="style_without_image_content_${apparel_data['apparel_id']}" 
                          aria-selected="true"
                        ><span>style</span> <span>${style_bill_no} / ${style_bill_date}</span></a>
                      </li>
                      ${style_priority_li}  
                    </ul>
                  </div>
                  <div class="tab-content" id="pills-tabContent-${apparel_data['apparel_id']}">
                    <div class="tab-pane fade show active" id="style_without_image_content_${apparel_data['apparel_id']}" role="tabpanel" aria-labelledby="style_without_image_tab_${apparel_data['apparel_id']}">
                      <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="max-height: 60vh; overflow-y: auto; overflow-x: hidden;">
                        <div class="d-flex flex-wrap" id="style_wrapper_${apparel_data['apparel_id']}">${style_table}</div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="style_with_image_content_${apparel_data['apparel_id']}" role="tabpanel" aria-labelledby="style_with_image_tab_${apparel_data['apparel_id']}">
                      ${style_priority_tab}
                    </div>
                  </div>
                </div>
              </div>`;
    });
    html += `</div>`;
    return html;
  }
// measurement