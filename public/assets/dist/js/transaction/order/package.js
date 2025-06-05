$(document).ready(function () {

  $(`#bm_id`)
    .select2(
      select2_default({
        url: `${link}/${sub_link}/get_select2/_bm_id`,
        placeholder: "scan",
        barcode: "bm_id",
      })
    ).on("change", (event) => get_barcode_data(event.target.value));

  $(`#brmm_id`)
    .select2(
      select2_default({
        url: `${link}/${sub_link}/get_select2/_brmm_id`,
        placeholder: "scan",
        barcode: "brmm_id",
      })
    ).on("change", (event) => get_readymade_barcode_data(event.target.value));


    $(`#design_id`)
    .select2(
      select2_default({
        url: `${link}/${sub_link}/get_select2/_design_id`,
        placeholder: "scan",
        barcode: "design_id",
      })
    );

  $(`#apparel_id`)
    .select2(
      select2_default({
        url: `master/apparel/get_select2/_id`,
        placeholder: "select",
        param: true,
      })
    ).on("change", (event) => get_apparel_data(event.target.value));
});

let trans_data = [];
const calculate_transaction = (fromDiscPer = false) => {
  let qty = $("#qty").val();
  if (isNaN(qty) || qty == "") qty = 0;

  let mtr = $("#mtr").val();
  if (isNaN(mtr) || mtr == "") mtr = 0;

  let total_mtr = parseFloat(qty) * parseFloat(mtr);
  if (isNaN(total_mtr) || total_mtr == "") total_mtr = 0;
  $("#total_mtr").val(total_mtr.toFixed(2));

  let rate = $("#rate").val();
  if (isNaN(rate) || rate == "") rate = 0;

  let amt = parseFloat(total_mtr) * parseFloat(rate);
  if ($("#trans_type").val() == "READYMADE" || $("#trans_type").val() == "STITCHING" || $("#trans_type").val() == "PACKAGE"  || $("#trans_type").val() == "SWATCH")
    amt = parseFloat(qty) * parseFloat(rate);
  if (isNaN(amt) || amt == "") amt = 0;
  $("#amt").val(amt.toFixed(2));

  let disc_per = $("#disc_per").val();
  if (isNaN(disc_per) || disc_per == "") disc_per = 0;

  let disc_amt = $("#disc_amt").val();
  if (isNaN(disc_amt) || disc_amt == "") disc_amt = 0;

  if (fromDiscPer) {
    disc_amt = (parseFloat(amt) * parseFloat(disc_per)) / 100;
    if (isNaN(disc_amt) || disc_amt == "") disc_amt = 0;
    $("#disc_amt").val(disc_amt.toFixed(0));
  } else {
    disc_per = (parseFloat(disc_amt) * 100) / parseFloat(amt);
    if (isNaN(disc_per) || disc_per == "") disc_per = 0;
    $("#disc_per").val(disc_per.toFixed(2));
  }

  let taxable_amt = parseFloat(amt) - parseFloat(disc_amt);
  if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;

  let sgst_per = $("#sgst_per").val();
  if (isNaN(sgst_per) || sgst_per == "") sgst_per = 0;

  let cgst_per = $("#cgst_per").val();
  if (isNaN(cgst_per) || cgst_per == "") cgst_per = 0;

  let igst_per = $("#igst_per").val();
  if (isNaN(igst_per) || igst_per == "") igst_per = 0;

  if ($(`#om_bill_type`).is(":checked")) {
    let deduct_amt =
      (parseFloat(taxable_amt) * igst_per) / (100 + parseFloat(igst_per));
    if (isNaN(deduct_amt) || deduct_amt == "") deduct_amt = 0;

    taxable_amt = parseFloat(taxable_amt) - parseFloat(deduct_amt);
    if (isNaN(taxable_amt) || taxable_amt == "") taxable_amt = 0;
  }
  $("#taxable_amt").val(taxable_amt.toFixed(2));

  let sgst_amt = 0;
  let cgst_amt = 0;
  let igst_amt = 0;

  if ($("#om_gst_type").val() == 0) {
    // WITHIN
    sgst_amt = (parseFloat(taxable_amt) * parseFloat(sgst_per)) / 100;
    if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;

    cgst_amt = (parseFloat(taxable_amt) * parseFloat(cgst_per)) / 100;
    if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
  } else {
    // OUTSIDE
    igst_amt = (parseFloat(taxable_amt) * parseFloat(igst_per)) / 100;
    if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
  }
  $("#sgst_amt").val(sgst_amt.toFixed(2));
  $("#cgst_amt").val(cgst_amt.toFixed(2));
  $("#igst_amt").val(igst_amt.toFixed(2));
  let total_amt =
    parseFloat(taxable_amt) +
    parseFloat(sgst_amt) +
    parseFloat(cgst_amt) +
    parseFloat(igst_amt);
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;
  $("#total_amt").val(total_amt.toFixed(2));
};
const check_transaction = () => {
  let ot_id = 0;
  let bm_id = 0;
  let flag = true;
  if (trans_data.length > 0) {
    trans_data.forEach((value) => {
      const { ot_trans_type, ot_qty, ot_mtr, ot_total_mtr, ot_rate, ot_amt } =
        value;
      if (ot_trans_type == "READYMADE" || ot_trans_type == "STITCHING") {
        if (ot_qty == 0 || ot_qty == "") {
          ot_id = id;
          flag = false;
        } else if (ot_qty < 0) {
          ot_id = id;
          flag = false;
        } else {
        }
      } else {
        if (ot_mtr == 0 || ot_mtr == "") {
          ot_id = id;
          flag = false;
        } else if (ot_mtr < 0) {
          ot_id = id;
          flag = false;
        } else {
        }

        if (ot_total_mtr == 0 || ot_total_mtr == "") {
          ot_id = id;
          flag = false;
        } else if (ot_total_mtr < 0) {
          ot_id = id;
          flag = false;
        } else {
        }
      }

      if (ot_rate == 0 || ot_rate == "") {
        ot_id = id;
        flag = false;
      } else if (ot_rate < 0) {
        ot_id = id;
        flag = false;
      } else {
      }

      if (ot_amt == 0 || ot_amt == "") {
        ot_id = id;
        flag = false;
      } else if (ot_amt < 0) {
        ot_id = id;
        flag = false;
      } else {
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


const add_transaction = () => { 
  remove_master_notifier();
  let check = true;
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
  if ($("#trans_type").val() == "READYMADE" || $("#trans_type").val() == "STITCHING" || $("#trans_type").val() == "SWATCH") {
    if ($("#qty").val() == "" || $("#qty").val() == 0) {
      notifier("qty", "Required");
      check = false;
    } else {
      if ($("#qty").val() < 0) {
        notifier("qty", "Invalid qty");
        check = false;
      }else{
        if($("#trans_type").val() == "READYMADE"){
          if (
            $("#brmm_id").val() != null &&
            $("#brmm_id").val() != 0 &&
            $("#brmm_id").val() != ""
          ) {
            if (
              parseFloat($("#qty").val()) >
              parseFloat($("#available_qty").val())
            ) {
              toastr.error("Qty should less than available Qty.", "", {
                closeButton: true,
                progressBar: true,
                preventDuplicates: true,
              });
              return false;
            }
          }
        }
      }
    }
  } else {
    if ($("#total_mtr").val() == "" || $("#total_mtr").val() == 0) {
      notifier("mtr", "Required");
      check = false;
    } else {
      if ($("#total_mtr").val() < 0) {
        notifier("mtr", "Invalid mtr");
        check = false;
      } else {
        if (
          $("#bm_id").val() != null &&
          $("#bm_id").val() != 0 &&
          $("#bm_id").val() != ""
        ) {
          if (
            parseFloat($("#total_mtr").val()) >
            parseFloat($("#available_mtr").val())
          ) {
            toastr.error("Total mtr should less than available.", "", {
              closeButton: true,
              progressBar: true,
              preventDuplicates: true,
            });
            return false;
          }
        }
      }
    }
  }
  if ($("#rate").val() == "" || $("#rate").val() == 0) {
    notifier("rate", "Required");
    check = false;
  } else {
    if ($("#rate").val() < 0) {
      notifier("rate", "Invalid rate");
      check = false;
    }
  }
  if ($("#amt").val() == "" || $("#amt").val() == 0) {
    notifier("amt", "Required");
    check = false;
  } else {
    if ($("#amt").val() < 0) {
      notifier("amt", "Invalid amt");
      check = false;
    }
  }
  if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else {
    let ot_id = $("#ot_id").val();
    const path = `${link}/${sub_link}/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=add_transaction`;  
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            if (ot_id == 0) {
              trans_data.unshift(data);
              add_wrapper_data(data);
              toastr.success(
                `${$("#trans_type :selected").text()}`,
                "ITEM ADDED TO LIST.",
                { closeButton: true, progressBar: true }
              );
              
              if (data["apparel_data"]?.length) { 
                get_new_measurement_data(data["ot_id"]);
              } else {
                if (data["ot_apparel_id"] > 0) get_measurement_data(data["ot_id"]);
              }

            } else {
              let index = trans_data.findIndex((value) => value.ot_id == ot_id);
              if (index < 0) {
                toastr.success(`Transaction not found`, "", {
                  closeButton: true,
                  progressBar: true,
                });
              }
              trans_data[index].ot_apparel_id = data["ot_apparel_id"];
              trans_data[index].apparel_name = data["apparel_name"];
              trans_data[index].ot_bm_id = data["ot_bm_id"];
              trans_data[index].ot_brmm_id = data["ot_brmm_id"];
              trans_data[index].ot_design_id = data["ot_design_id"];
              trans_data[index].item_code = data["item_code"];
              trans_data[index].ot_trans_type = data["ot_trans_type"];
              trans_data[index].ot_qty = data["ot_qty"];
              trans_data[index].ot_mtr = data["ot_mtr"];
              trans_data[index].ot_total_mtr = data["ot_total_mtr"];
              trans_data[index].ot_rate = data["ot_rate"];
              trans_data[index].ot_amt = data["ot_amt"];
              trans_data[index].ot_disc_per = data["ot_disc_per"];
              trans_data[index].ot_disc_amt = data["ot_disc_amt"];
              trans_data[index].ot_taxable_amt = data["ot_taxable_amt"];
              trans_data[index].ot_sgst_per = data["ot_sgst_per"];
              trans_data[index].ot_sgst_amt = data["ot_sgst_amt"];
              trans_data[index].ot_cgst_per = data["ot_cgst_per"];
              trans_data[index].ot_cgst_amt = data["ot_cgst_amt"];
              trans_data[index].ot_igst_per = data["ot_igst_per"];
              trans_data[index].ot_igst_amt = data["ot_igst_amt"];
              trans_data[index].ot_total_amt = data["ot_total_amt"];
              trans_data[index].ot_description = data["ot_description"];

              $(`#apparel_name_${ot_id}`).html(data["apparel_name"]);
              $(`#item_code_${ot_id}`).html(data["item_code"]);
              $(`#design_name_${ot_id}`).html(data["design_name"]);
              $(`#trans_type_${ot_id}`).html(data["ot_trans_type"]);
              $(`#qty_${ot_id}`).html(data["ot_qty"]);
              $(`#mtr_${ot_id}`).html(data["ot_mtr"]);
              $(`#total_mtr_${ot_id}`).html(data["ot_total_mtr"]);
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
              toastr.success(
                `${$("#fabric_id :selected").text()}`,
                "ITEM UPDATED TO LIST.",
                { closeButton: true, progressBar: true }
              );
            }
            $("#bm_id").val(null).trigger("change");
            $("#bm_id").select2("close");
            $("#apparel_id").val(null).trigger("change");
            $("#apparel_id").select2("close");
            $("#brmm_id").val(null).trigger("change");
            $("#brmm_id").select2("close");

            if ($("#trans_type").val() == "READYMADE" || $("#trans_type").val() == "STITCHING" || $("#trans_type").val() == "PACKAGE"  || $("#trans_type").val() == "SWATCH") {
              $("#qty").val(0);
              $("#qty").attr("onkeyup", "calculate_transaction()");
              $("#qty").removeAttr("readonly");

              $("#mtr").val(0);
              $("#mtr").attr("readonly", "readonly");
              $("#mtr").removeAttr("onkeyup");

              $(".mtr_area").addClass("d-none");
              $("#amt_area")
                .removeClass("col-md-6")
                .removeClass("col-lg-6")
                .addClass("col-md-12")
                .addClass("col-lg-12");
            } else {
              $("#qty").val(1);
              $("#qty").attr("readonly", "readonly");
              $("#qty").removeAttr("onkeyup");

              $("#mtr").val(0);
              $("#mtr").attr("onkeyup", "calculate_transaction()");
              $("#mtr").removeAttr("readonly");

              $(".mtr_area").removeClass("d-none");
              $("#amt_area")
                .removeClass("col-md-12")
                .removeClass("col-lg-12")
                .addClass("col-md-6")
                .addClass("col-lg-6");
            }
            $("#total_mtr").val(0);
            $("#rate").val(0);
            $("#ot_id").val(0);
            calculate_transaction(true);
            calculate_master(true);
            $("#transaction_count").html(trans_data.length);
          }
        }
      },
      (errmsg) => {}
    );
  }
};
const add_wrapper_data = (data, append = false) => {
  const {
    ot_id,
    ot_om_id,
    ot_trans_type,
    ot_apparel_id,
    apparel_name,
    design_name,
    item_code,
    ot_qty,
    ot_mtr,
    ot_total_mtr,
    ot_rate,
    ot_amt,
    ot_disc_per,
    ot_disc_amt,
    ot_taxable_amt,
    ot_sgst_per,
    ot_sgst_amt,
    ot_cgst_per,
    ot_cgst_amt,
    ot_igst_per,
    ot_igst_amt,
    ot_total_amt,
    ot_description,
    apparel_data,
    isExist,
  } = data;
  let tr = `<tr id="row_${ot_id}">
                <td id="serial_no_${ot_id}"></td>
                <td id="trans_type_${ot_id}">${ot_trans_type}</td>
                <td id="apparel_name_${ot_id}">${apparel_name}</td>
                <td id="item_code_${ot_id}">${item_code}</td>
                <td id="design_name_${ot_id}">${design_name}</td>
                <td id="qty_${ot_id}">${ot_qty}</td>
                <td id="mtr_${ot_id}">${ot_mtr}</td>
                <td id="total_mtr_${ot_id}">${ot_total_mtr}</td>
                <td id="rate_${ot_id}">${ot_rate}</td>
                <td id="amt_${ot_id}">${ot_amt}</td>
                <td id="disc_per_${ot_id}">${ot_disc_per}</td>
                <td id="disc_amt_${ot_id}">${ot_disc_amt}</td>
                <td id="taxable_amt_${ot_id}">${ot_taxable_amt}</td>
                <td id="sgst_per_${ot_id}">${ot_sgst_per}</td>
                <td id="sgst_amt_${ot_id}">${ot_sgst_amt}</td>
                <td id="cgst_per_${ot_id}">${ot_cgst_per}</td>
                <td id="cgst_amt_${ot_id}">${ot_cgst_amt}</td>
                <td id="igst_per_${ot_id}">${ot_igst_per}</td>
                <td id="igst_amt_${ot_id}">${ot_igst_amt}</td>
                <td id="total_amt_${ot_id}">${ot_total_amt}</td>
                <td id="description_${ot_id}">${ot_description}</td>
                <td>
                  <div class="navigationn_wrapper">
                    <div class="navigationn">
                      <div class="menuToggle" id="menu_toggle_${ot_id}" onclick="toggle_menuu(this)"></div>
                      <div class="menuu">
                        <ul>
                          <li>
                            <a 
                              type="button" 
                              class="btn btn-md" 
                              target="_blank"
                              href="${base_url}/${link}/${sub_link}/measurement_print/${ot_om_id}/${ot_id}"
                              ><i class="text-info fa fa-print"></i></a>
                          </li>
                           ${apparel_data?.length
                            ?
                            `<li>
                                    <a 
                                      type="button" 
                                      class="btn btn-md" 
                                      onclick="get_new_measurement_data(${ot_id})"
                                      ><i class="text-info fa fa-eye"></i></a>
                                  </li>`
                            :
                            ot_apparel_id > 0
                              ? `<li>
                                      <a 
                                        type="button" 
                                        class="btn btn-md" 
                                        onclick="get_measurement_data(${ot_id})"
                                        ><i class="text-info fa fa-eye"></i></a>
                                    </li>`
                              : ``
                          }
                          ${
                            isExist
                              ? ``
                              : `<li>
                                    <a 
                                        type="button" 
                                        class="btn btn-md" 
                                        onclick="edit_transaction(${ot_id})"
                                        ><i class="text-success fa fa-edit"></i></a>
                                </li>`
                          }
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
                                    onclick="remove_transaction(${ot_id})"
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
};
const edit_transaction = (ot_id) => {
  const find = trans_data.find((value) => value["ot_id"] == ot_id);
  const {
    ot_om_id,
    ot_trans_type,
    ot_apparel_id,
    apparel_name,
    ot_bm_id,
    ot_brmm_id,
    ot_design_id,
    item_code,
    design_name,
    ot_qty,
    ot_mtr,
    ot_total_mtr,
    ot_rate,
    ot_amt,
    ot_disc_per,
    ot_disc_amt,
    ot_taxable_amt,
    ot_sgst_per,
    ot_sgst_amt,
    ot_cgst_per,
    ot_cgst_amt,
    ot_igst_per,
    ot_igst_amt,
    ot_total_amt,
    ot_description,
  } = find;

  $("#ot_id").val(ot_id);
  $("#trans_type").val(ot_trans_type).trigger("change");
  set_area();
  $("#apparel_id").html(
    `<option value="${ot_apparel_id}">${apparel_name}</option>`
  );
  $("#bm_id").html(`<option value="${ot_bm_id}">${item_code}</option>`);
  $("#brmm_id").html(`<option value="${ot_brmm_id}">${item_code}</option>`);
  $("#design_id").html(`<option value="${ot_design_id}">${design_name}</option>`);
  $("#qty").val(ot_qty);
  $("#mtr").val(ot_mtr);
  if ($("#trans_type").val() == "READYMADE" || $("#trans_type").val() == "STITCHING" || $("#trans_type").val() == "PACKAGE"  || $("#trans_type").val() == "SWATCH") {
    $("#qty").attr("onkeyup", "calculate_transaction()");
    $("#qty").removeAttr("readonly");

    $("#mtr").attr("readonly", "readonly");
    $("#mtr").removeAttr("onkeyup");
  } else {
    $("#qty").attr("readonly", "readonly");
    $("#qty").removeAttr("onkeyup");

    $("#mtr").attr("onkeyup", "calculate_transaction()");
    $("#mtr").removeAttr("readonly");
  }
  $("#total_mtr").val(ot_total_mtr);
  $("#rate").val(ot_rate);
  $("#available_mtr").val(0);
  $("#available_qty").val(0);
  $("#amt").val(ot_amt);
  $("#disc_per").val(ot_disc_per);
  $("#disc_amt").val(ot_disc_amt);
  $("#taxable_amt").val(ot_taxable_amt);
  $("#sgst_per").val(ot_sgst_per);
  $("#sgst_amt").val(ot_sgst_amt);
  $("#cgst_per").val(ot_cgst_per);
  $("#cgst_amt").val(ot_cgst_amt);
  $("#igst_per").val(ot_igst_per);
  $("#igst_amt").val(ot_igst_amt);
  $("#total_amt").val(ot_total_amt);
  $("#description").val(ot_description);
  toggle_menuu({ id: ot_id });
  if (ot_bm_id > 0) {
    let mtr = 0;
    if (ot_om_id == 0) {
      trans_data.forEach((value) => {
        if (value["ot_id"] != ot_id && value["ot_bm_id"] == ot_bm_id) {
          mtr =
            parseFloat(mtr) +
            parseFloat(-1) * parseFloat(value["ot_total_mtr"]);
        }
      });
    } else {
      trans_data.forEach((value) => {
        if (
          value["ot_om_id"] != 0 &&
          value["ot_id"] == ot_id &&
          value["ot_bm_id"] == ot_bm_id
        ) {
          mtr = parseFloat(mtr) + parseFloat(value["ot_total_mtr"]);
        }
        if (
          value["ot_om_id"] == 0 &&
          value["ot_id"] != ot_id &&
          value["ot_bm_id"] == ot_bm_id
        ) {
          mtr = parseFloat(mtr) - parseFloat(value["ot_total_mtr"]);
        }
      });
    }
    const path = `${link}/${sub_link}/handler`;
    const form_data = { func: "get_barcode_data", id: ot_bm_id, mtr };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $("#available_mtr").val(data[0]["bal_qty"]);
          }
        }
      },
      (errmsg) => {}
    );
  }
  if (ot_brmm_id > 0) {
    let qty = 0;
    if (ot_om_id == 0) {
      trans_data.forEach((value) => {
        if (value["ot_id"] != ot_id && value["ot_brmm_id"] == ot_brmm_id) {
          qty =
            parseFloat(qty) +
            parseFloat(-1) * parseFloat(value["ot_qty"]);
        }
      });
    } else {
      trans_data.forEach((value) => {
        if (
          value["ot_om_id"] != 0 &&
          value["ot_id"] == ot_id &&
          value["ot_brmm_id"] == ot_brmm_id
        ) {
          qty = parseFloat(qty) + parseFloat(value["ot_qty"]);
        }
        if (
          value["ot_om_id"] == 0 &&
          value["ot_id"] != ot_id &&
          value["ot_brmm_id"] == ot_brmm_id
        ) {
          qty = parseFloat(qty) - parseFloat(value["ot_qty"]);
        }
      });
    }
    const path = `${link}/${sub_link}/handler`;
    const form_data = { func: "get_readymade_barcode_data", id: ot_brmm_id, qty };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $("#available_qty").val(data[0]["bal_qty"]);
          }
        }
      },
      (errmsg) => {}
    ); 
  }

  if (ot_design_id > 0) {
    let qty = 0;
    if (ot_om_id == 0) {
      trans_data.forEach((value) => {
        if (value["ot_id"] != ot_id && value["ot_design_id"] == ot_design_id) {
          qty =
            parseFloat(qty) +
            parseFloat(-1) * parseFloat(value["ot_qty"]);
        }
      });
    } else {
      trans_data.forEach((value) => {
        if (
          value["ot_om_id"] != 0 &&
          value["ot_id"] == ot_id &&
          value["ot_design_id"] == ot_design_id
        ) {
          qty = parseFloat(qty) + parseFloat(value["ot_qty"]);
        }
        if (
          value["ot_om_id"] == 0 &&
          value["ot_id"] != ot_id &&
          value["ot_design_id"] == ot_design_id
        ) {
          qty = parseFloat(qty) - parseFloat(value["ot_qty"]);
        }
      });
    }
    const path = `${link}/${sub_link}/handler`;
    const form_data = { func: "get_design_data", id: ot_design_id, qty };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $("#available_qty").val(data[0]["bal_qty"]);
          }
        }
      },
      (errmsg) => {}
    ); 
  }
};
const get_barcode_data = (id) => {
  $("#rate").val(0);
  $("#available_mtr").val(0);
  $("#sgst_per").val(0);
  $("#cgst_per").val(0);
  $("#igst_per").val(0);
  if (id) {
    let mtr = 0;
    trans_data.forEach((value) => {
      if (value["ot_om_id"] == 0 && value["ot_bm_id"] == id) {
        mtr =
          parseFloat(mtr) + parseFloat(-1) * parseFloat(value["ot_total_mtr"]);
      }
    });
    const path = `${link}/${sub_link}/handler`;
    const form_data = { func: "get_barcode_data", id, mtr };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $("#rate").val(data[0]["bm_mrp"]);
            $("#available_mtr").val(data[0]["bal_qty"]);
            $("#sgst_per").val(data[0]["sgst_per"]);
            $("#cgst_per").val(data[0]["cgst_per"]);
            $("#igst_per").val(data[0]["igst_per"]);
            calculate_transaction();
          }
          toastr.success(data[0]["bm_item_code"], msg, {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
          });
          setTimeout(() => {
            // $("#bm_id").val(null).trigger("change");
            // $("#bm_id").select2("open");
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
  }
};
const get_readymade_barcode_data = (id)=>{
    $("#rate").val(0);
    $("#available_qty").val(0);
    $("#sgst_per").val(0);
    $("#cgst_per").val(0);
    $("#igst_per").val(0);
    if (id) {
      let qty = trans_data.reduce((n, { ot_brmm_id, ot_qty }) => {
        return ot_brmm_id == id ? parseFloat(n) + parseFloat(ot_qty) : 0;
      }, 0);
      const path = `${link}/${sub_link}/handler`;
      const form_data = { func: "get_readymade_barcode_data", id, qty};
      ajaxCall(
        "POST",
        path,
        form_data,
        "JSON",
        (resp) => {
          if (handle_response(resp)) {
            const { data, msg } = resp;
            if (data && data.length != 0) {
              $("#rate").val(data[0]["brmm_mrp"]);
              $("#available_qty").val(data[0]["bal_qty"]);
              $("#qty").val(1);
              $("#sgst_per").val(data[0]["sgst_per"]);
              $("#cgst_per").val(data[0]["cgst_per"]);
              $("#igst_per").val(data[0]["igst_per"]);
              $("#add_row_btn").focus();
              calculate_transaction();
            }
            toastr.success(data[0]["brmm_item_code"], msg, {
              closeButton: true,
              progressBar: true,
              preventDuplicates: true,
            });
            setTimeout(() => {
              // $("#bm_id").val(null).trigger("change");
              // $("#bm_id").select2("open");
            }, RELOAD_TIME);
          } else {
            setTimeout(() => {
              $("#brmm_id").val(null).trigger("change");
              $("#brmm_id").select2("open");
            }, RELOAD_TIME);
          }
        },
        (errmsg) => {
          setTimeout(() => {
            $("#brmm_id").val(null).trigger("change");
            $("#brmm_id").select2("open");
          }, RELOAD_TIME);
        }
      );
    }
}
const get_apparel_data = (id) => {
  if ($("#trans_type").val() == "STITCHING" || $("#trans_type").val() == "SWATCH") {
    $("#rate").val(0);
    $("#available_mtr").val(0);
    $("#sgst_per").val(0);
    $("#cgst_per").val(0);
    $("#igst_per").val(0);
    if (id) {
      const path = `${link}/${sub_link}/handler`;
      const form_data = { func: "get_apparel_data", id };
      ajaxCall(
        "POST",
        path,
        form_data,
        "JSON",
        (resp) => {
          if (handle_response(resp)) {
            const { data, msg } = resp;
            if (data && data.length != 0) {
              $("#rate").val(data[0]["charges"]);
              $("#sgst_per").val(data[0]["sgst_per"]);
              $("#cgst_per").val(data[0]["cgst_per"]);
              $("#igst_per").val(data[0]["igst_per"]);
              calculate_transaction();
            }
            toastr.success(data[0]["apparel_name"], msg, {
              closeButton: true,
              progressBar: true,
              preventDuplicates: true,
            });
            setTimeout(() => {
              // $("#bm_id").val(null).trigger("change");
              // $("#bm_id").select2("open");
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
    }
  }
};

const get_new_measurement_data = (ot_id) => {
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

  let temp = trans_data.find(value => value['ot_id'] == ot_id);
  if (temp['apparel_data'].length <= 0) {
    toastr.error("Apparel not defined in sku.", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    return;
  }
  const om_id = $("#id").val();
  const ids = [];
  temp['apparel_data'].forEach(value => {
    ids.push({ ot_id: value['ot_id'], apparel_id: parseInt(value['ot_apparel_id']), apparel_name: value['apparel_name'] });
  });
  const path = `${link}/${sub_link}/handler`;
  const form_data = {
    func: "get_new_measurement_data",
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
        if (data && data.length) {
          let title = `<div class="px-2 mx-2" style="font-size: 0.8rem;">${get_new_tab(data)}</div>`;
          let body = `<div class="d-flex flex-column font-weight-bold" style="font-size: 0.8rem;">${get_new_tab_content(data)}</div>`;
          let footer = `<button 
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
    (errmsg) => { }
  );
};

const get_measurement_data = (ot_id) => { 
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
  
  const find = trans_data.find((value) => value.ot_id == ot_id);
  const om_id = $("#id").val();
  const ids = [];
  ids.push({
    ot_id: find['ot_id'],
    apparel_id: parseInt(find['ot_apparel_id']),
    apparel_name: find['apparel_name']
  });
  if (find['ot_apparel_id']) {
    const path = `${link}/${sub_link}/handler`;
    const form_data = {
      func: "get_new_measurement_data",
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
          if (data && data.length) {
            let title = `<div class="px-2 mx-2" style="font-size: 0.8rem;">${get_new_tab(data)}</div>`;
            let body = `<div class="d-flex flex-column font-weight-bold" style="font-size: 0.8rem;">${get_new_tab_content(data)}</div>`;
            let footer = `<button 
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
        } else {
          setTimeout(() => {
            $("#apparel_id").val(null).trigger("change");
            $("#apparel_id").select2("open");
          }, RELOAD_TIME);
        }
      },
      (errmsg) => {
        setTimeout(() => {
          $("#apparel_id").val(null).trigger("change");
          $("#apparel_id").select2("open");
        }, RELOAD_TIME);
      }
    );
  }
};


const get_new_tab = data => {
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
                  <td class="text-left border-0" width="50%">entry no.: ${$("#om_entry_no").val()}</td>
                  <td class="text-right border-0" width="50%">customer : ${$("#om_customer_id :selected").text()}</td>
                </tr>
              </table>`;
    return html;
}

const get_new_tab_content = data => {
  let html = `<div class="tab-content" id="pills-tabContent">`;
  data.forEach((value, index) => {
      const {apparel_data, measurement_data, style_data,style_priority_data} = value;
      const measurement_bill_no   = measurement_data && measurement_data.length != 0 ? measurement_data[0]["bill_no"]: "";
      const measurement_bill_date = measurement_data && measurement_data.length != 0 ? measurement_data[0]["bill_date"] : "";
      const style_bill_no         = style_data && style_data.length != 0 ? style_data[0]["bill_no"] : "";
      const style_bill_date       = style_data && style_data.length != 0 ? style_data[0]["bill_date"] : "";
      let measurement_table       = get_measurement_table(measurement_data);
      let style_table             = get_style_table(style_data);
      let { style_priority_li, style_priority_tab } =
              get_style_priority(style_priority_data);
      html += `<div 
              class="tab-pane fade ${index == 0 ? 'show active' : ''}" 
              id="apparel_${apparel_data['apparel_id']}_content" 
              role="tabpanel" 
              aria-labelledby="apparel_${apparel_data['apparel_id']}_tab">
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
                  <ul class="nav nav-pills nav-fill nav-pills-secondary" id="pills-tab-${apparel_data['apparel_id']}" role="tablist" style="width: 97%;background-color:white">
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
                      <li class="nav-item">
                        <a 
                            class="nav-link text-uppercase d-flex flex-wrap justify-content-between" 
                            id="style_with_image_tab_${apparel_data['apparel_id']}" 
                            data-toggle="tab"
                            href="#style_with_image_content_${apparel_data['apparel_id']}" 
                            role="tab" 
                            aria-controls="style_with_image_content_${apparel_data['apparel_id']}" 
                            aria-selected="true"
                        ><span>style image</span> <span>${style_bill_no} / ${style_bill_date}</span></a>
                      </li>
                     
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



const set_area = () => {
  $("#apparel_id").val(null).trigger("change");
  $("#bm_id").val(null).trigger("change");
  $("#brmm_id").val(null).trigger("change");
  $("#design_id").val(null).trigger("change");
  $("._stitching_area").addClass("d-none");
  $("._fabric_area").addClass("d-none");
  $("._readymade_fabric_area").addClass("d-none");
  $("._display_qty").addClass("d-none");
  $("._display_mtr").removeClass("d-none");
  if ($("#trans_type").val() == "FABRIC") {
    $("._fabric_area").removeClass("d-none");
    $("#qty").val(1);
    $("#qty").attr("readonly", "readonly");
    $("#qty").removeAttr("onkeyup");

    $("#mtr").val(0);
    $("#mtr").attr("onkeyup", "calculate_transaction()");
    $("#mtr").removeAttr("readonly");

    $(".mtr_area").removeClass("d-none");
    $("#amt_area")
      .removeClass("col-md-12")
      .removeClass("col-lg-12")
      .addClass("col-md-6")
      .addClass("col-lg-6");
  }else if ($("#trans_type").val() == "STITCHING") {
    $("._stitching_area").removeClass("d-none");
    $("#qty").val(0);
    $("#qty").attr("onkeyup", "calculate_transaction()");
    $("#qty").removeAttr("readonly");

    $("#mtr").val(0);
    $("#mtr").attr("readonly", "readonly");
    $("#mtr").removeAttr("onkeyup");

    $(".mtr_area").addClass("d-none");
    $("#amt_area")
      .removeClass("col-md-6")
      .removeClass("col-lg-6")
      .addClass("col-md-12")
      .addClass("col-lg-12");
  }else if ($("#trans_type").val() == "READYMADE") {
    $("._readymade_fabric_area").removeClass("d-none");
    $("._display_qty").removeClass("d-none");
    $("._display_mtr").addClass("d-none");
    $("#qty").val(0);
    $("#qty").attr("onkeyup", "calculate_transaction()");
    $("#qty").removeAttr("readonly");

    $("#mtr").val(0);
    $("#mtr").attr("readonly", "readonly");
    $("#mtr").removeAttr("onkeyup");

    $(".mtr_area").addClass("d-none");
    $("#amt_area")
      .removeClass("col-md-6")
      .removeClass("col-lg-6")
      .addClass("col-md-12")
      .addClass("col-lg-12");
  }
  else if ($("#trans_type").val() == "SWATCH") {
    $("._stitching_area").removeClass("d-none");
    $("._swatch_fabric_area").removeClass("d-none");
    $("._display_qty").removeClass("d-none");
    $("._display_mtr").addClass("d-none");
    $("#qty").val(1);
    $("#qty").attr("onkeyup", "calculate_transaction()");
    $("#qty").removeAttr("readonly");

    $("#mtr").val(0);
    $("#mtr").removeAttr("readonly");

    $(".mtr_area").removeClass("d-none");
    $("#amt_area")
      .removeClass("col-md-12")
      .removeClass("col-lg-12")
      .addClass("col-md-6")
      .addClass("col-lg-6");
  }
  else {
    $("._stitching_area").removeClass("d-none");
    $("._fabric_area").removeClass("d-none");

    $("#qty").val(1);
    $("#qty").attr("readonly", "readonly");
    $("#qty").removeAttr("onkeyup");

    $("#mtr").val(0);
    $("#mtr").attr("onkeyup", "calculate_transaction()");
    $("#mtr").removeAttr("readonly");

    $(".mtr_area").removeClass("d-none");
    $("#amt_area")
      .removeClass("col-md-12")
      .removeClass("col-lg-12")
      .addClass("col-md-6")
      .addClass("col-lg-6");
  }
};

