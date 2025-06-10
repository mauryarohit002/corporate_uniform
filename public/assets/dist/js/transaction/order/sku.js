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
  let employee_trans_data = [];
  const calculate_sku_transaction = (fromDiscPer = false) => { 
    let sku_qty = parseInt($("#sku_qty").val());
    if (isNaN(sku_qty) || sku_qty == "") sku_qty = 0;

    let sku_rate = parseFloat($("#sku_rate").val());
    if (isNaN(sku_rate) || sku_rate == "") sku_rate = 0;

    let sku_amt = parseFloat(sku_qty) * parseFloat(sku_rate);
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

  const add_sku_transaction = () => {
    remove_sku_notifier();
    let check = true;
    if ($("#om_entry_no").val() == '') {
      notifier("om_entry_no", "Required");
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
    ['sku_qty', 'sku_rate'].forEach(field => {
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
                sku_trans_data.unshift(data);
                add_sku_wrapper_data(data);
                toastr.success(`${$("#sku_id :selected").text()}`, "ITEM ADDED TO LIST.", { closeButton: true, progressBar: true });
               if (data["ot_apparel_id"] > 0) { 
                window.open(  
                    `${base_url}/${link}/${sub_link}/add_employee?om_id=${data["ot_om_id"]}&id=${data["ot_id"]}`,
                    "_blank"
                  );

              }
            } else {
                let index = sku_trans_data.findIndex((value) => value.ot_id == ot_id);
                if (index < 0) {
                  toastr.success(`Transaction not found`, "", {
                    closeButton: true,
                    progressBar: true,
                  });
                } 

                sku_trans_data[index].sku_name       = data["sku_name"];
                sku_trans_data[index].ot_sku_id      = data["ot_sku_id"];
                sku_trans_data[index].ot_apparel_id      = data["ot_apparel_id"];
                sku_trans_data[index].apparel_name       = data["apparel_name"];
                sku_trans_data[index].ot_qty         = data["ot_qty"];
                sku_trans_data[index].ot_rate        = data["ot_rate"];
                sku_trans_data[index].ot_amt         = data["ot_amt"];
                sku_trans_data[index].ot_disc_per    = data["ot_disc_per"];
                sku_trans_data[index].ot_disc_amt    = data["ot_disc_amt"];
                sku_trans_data[index].ot_taxable_amt = data["ot_taxable_amt"];
                sku_trans_data[index].ot_sgst_per    = data["ot_sgst_per"];
                sku_trans_data[index].ot_sgst_amt    = data["ot_sgst_amt"];
                sku_trans_data[index].ot_cgst_per    = data["ot_cgst_per"];
                sku_trans_data[index].ot_cgst_amt    = data["ot_cgst_amt"];
                sku_trans_data[index].ot_igst_per    = data["ot_igst_per"];
                sku_trans_data[index].ot_igst_amt    = data["ot_igst_amt"];
                sku_trans_data[index].ot_total_amt   = data["ot_total_amt"];
                sku_trans_data[index].ot_description = data["ot_description"];
                
                $(`#sku_name_${ot_id}`).html(data["sku_name"]);
                $(`#apparel_name_${ot_id}`).html(data["apparel_name"]);
                $(`#qty_${ot_id}`).html(data["ot_qty"]);
                $(`#rate_${ot_id}`).html(data["ot_rate"]);
                $(`#amt_${ot_id}`).html(data["ot_amt"]);
                
                $(`#disc_per_${ot_id}`).html(data["ot_disc_per"]);
                $(`#disc_amt_${ot_id}`).html(data["ot_disc_amt"]);
                $(`#taxable_amt_${ot_id}`).html(data["ot_taxable_amt"]);
                $(`#sgst_per_${ot_id}`).html(data["ot_sgst_per"]);
                $(`#sgst_amt_${ot_id}`).html(data["ot_sgst_amt"]);
                $(`#cgst_per_${ot_id}`).html(data["ot_cgst_per"]);
                $(`#cgst_amt_${ot_id}`).html(data["ot_cgst_amt"]);
                $(`#igst_per_${ot_id}`).html(data["ot_igst_per"]);
                $(`#igst_amt_${ot_id}`).html(data["ot_igst_amt"]);
                $(`#total_amt_${ot_id}`).html(data["ot_total_amt"]);
                $(`#description_${ot_id}`).html(data["ot_description"]);
              
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
          // if(data && data == 1) sku_mtr_popup();  
        }
      },
      (errmsg) => {}
    );
  };
  const add_sku_wrapper_data = (data, append = false) => {
    // if(parseInt(data['ot_ot_id']) == 0){
      // alert(0)
      let tr = `<tr id="rowsku_${data['ot_id']}">
                  <td>
                    <span id="sku_name_${data['ot_id']}">${data['sku_name']}</span>
                  </td>
                   <td>
                    <span id="apparel_name_${data['ot_id']}">${data['apparel_name']}</span>
                  </td>
                  <td id="qty_${data['ot_id']}">${data['ot_qty']}</td>
                  <td id="rate_${data['ot_id']}">${data['ot_rate']}</td>
                  <td id="amt_${data['ot_id']}">${data['ot_amt']}</td>
                  <td id="disc_per_${data['ot_id']}">${data['ot_disc_per']}</td>
                  <td id="disc_amt_${data['ot_id']}">${data['ot_disc_amt']}</td>
                  <td id="taxable_amt_${data['ot_id']}">${data['ot_taxable_amt']}</td>
                  <td id="sgst_per_${data['ot_id']}">${data['ot_sgst_per']}</td>
                  <td id="sgst_amt_${data['ot_id']}">${data['ot_sgst_amt']}</td>
                  <td id="cgst_per_${data['ot_id']}">${data['ot_cgst_per']}</td>
                  <td id="cgst_amt_${data['ot_id']}">${data['ot_cgst_amt']}</td>
                  <td id="igst_per_${data['ot_id']}">${data['ot_igst_per']}</td>
                  <td id="igst_amt_${data['ot_id']}">${data['ot_igst_amt']}</td>
                  <td id="total_amt_${data['ot_id']}">${data['ot_total_amt']}</td>
                  <td id="description_${data['ot_id']}">${data['ot_description']}</td>
                  <td>
                        <div class="navigationn_wrapper">
                          <div class="navigationn">
                            <div class="menuToggle" id="menu_toggle_${data['ot_id']}" onclick="toggle_menuu(this)"></div>
                            <div class="menuu">
                              <ul> 
                                ${
                                    data['isExist']
                                      ? ''
                                      : `<li onclick="window.open('${base_url}/${link}/${sub_link}/add_employee?om_id=${data["ot_om_id"]}&id=${data["ot_id"]}', '_blank')">
                                           <i class="text-primary fa fa-eye"></i>
                                         </li>`
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
    // }
  };
  const remove_sku_notifier = () => {
    notifier("om_entry_no");
    notifier("om_customer_id");
    // notifier("om_billing_id");
    notifier("sku_id");
    notifier("sku_qty");
    notifier("sku_rate");
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
    $("#sku_qty").val(find['ot_qty']);
    $("#sku_rate").val(find['ot_rate']);
    $("#sku_amt").val(find['ot_amt']);
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
   
  };
// core_functions

// sku
  const get_sku_data = (id) => {
    design_data = [];
    // $("#sku_mtr_btn").addClass('d-none');
    $("#sku_qty").val(1);
    $("#sku_rate").val(0);
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
            $("#sku_rate").val(data[0]["mrp"]);
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

          }
        }
      },
      (errmsg) => {}
    );
  };

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
// sku

function receiveEmployeeData(data) {
    console.log("Received data from child window:", data);
    employee_trans_data = data; // Set your main variable here
}
