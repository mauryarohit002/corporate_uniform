$(document).ready(function () {
  $(`#pm_supplier_id`)
    .select2(
      select2_default({
        url: `master/supplier/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
      })
    )
    .on("change", (event) => get_supplier_data(event.target.value));

  $(`#fabric_id`)
    .select2(
      select2_default({
        url: `master/fabric/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
      })
    )
    .on("change", (event) => get_fabric_data(event.target.value));

  $(`#design_id`).select2(
    select2_default({
      url: `master/design/get_select2/_id2`,
      placeholder: "SELECT",
      param: () => $("#pm_supplier_id").val(),
    })
  );

  $(`#category_id`).select2(
    select2_default({
      url: `master/category/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );

   $(`#color_id`).select2(
    select2_default({
      url: `master/color/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );

  $(`#hsn_id`).select2(
    select2_default({
      url: `master/hsn/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );

  $(`#width_id`).select2(
    select2_default({
      url: `master/width/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );
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
        (errmsg) => { }
      );
    }
  }
};
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
  $("#taxable_amt").val(taxable_amt.toFixed(2));

  let extra_amt = $("#extra_amt").val();
  if (isNaN(extra_amt) || extra_amt == "") extra_amt = 0;

  let actual_taxable_amt = parseFloat(taxable_amt) + parseFloat(extra_amt);
  if (isNaN(actual_taxable_amt) || actual_taxable_amt == "") actual_taxable_amt = 0;
  $("#actual_taxable_amt").val(actual_taxable_amt.toFixed(2));

  let sgst_per = $("#sgst_per").val();
  if (isNaN(sgst_per) || sgst_per == "") sgst_per = 0;

  let cgst_per = $("#cgst_per").val();
  if (isNaN(cgst_per) || cgst_per == "") cgst_per = 0;

  let igst_per = $("#igst_per").val();
  if (isNaN(igst_per) || igst_per == "") igst_per = 0;

  let sgst_amt = 0;
  let cgst_amt = 0;
  let igst_amt = 0;

  if ($("#pm_gst_type").val() == 0) {
    // WITHIN
    sgst_amt = (parseFloat(actual_taxable_amt) * parseFloat(sgst_per)) / 100;
    if (isNaN(sgst_amt) || sgst_amt == "") sgst_amt = 0;

    cgst_amt = (parseFloat(actual_taxable_amt) * parseFloat(cgst_per)) / 100;
    if (isNaN(cgst_amt) || cgst_amt == "") cgst_amt = 0;
  } else {
    // OUTSIDE
    igst_amt = (parseFloat(actual_taxable_amt) * parseFloat(igst_per)) / 100;
    if (isNaN(igst_amt) || igst_amt == "") igst_amt = 0;
  }
  $("#sgst_amt").val(sgst_amt.toFixed(2));
  $("#cgst_amt").val(cgst_amt.toFixed(2));
  $("#igst_amt").val(igst_amt.toFixed(2));

  let total_amt =
    parseFloat(actual_taxable_amt) +
    parseFloat(sgst_amt) +
    parseFloat(cgst_amt) +
    parseFloat(igst_amt);
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;
  $("#total_amt").val(total_amt.toFixed(2));
  set_cost_char();
};
const calculate_master = (fromDiscPer = false) => {
  let total_qty = 0;
  let total_total_mtr = 0;
  let total_sub_amt = 0;
  let total_disc_amt = 0;
  let total_taxable_amt = 0;
  let total_extra_amt = 0;
  let total_sgst_amt = 0;
  let total_cgst_amt = 0;
  let total_igst_amt = 0;
  let total_total_amt = 0;

  trans_data.forEach((value, index) => {
    const {
      pt_qty,
      pt_total_mtr,
      pt_amt,
      pt_disc_amt,
      pt_taxable_amt,
      pt_extra_amt,
      pt_sgst_amt,
      pt_cgst_amt,
      pt_igst_amt,
      pt_total_amt,
    } = value;

    total_qty = parseInt(total_qty) + parseInt(pt_qty);
    if (isNaN(total_qty) || total_qty == "") total_qty = 0;

    total_total_mtr = parseFloat(total_total_mtr) + parseFloat(pt_total_mtr);
    if (isNaN(total_total_mtr) || total_total_mtr == "") total_total_mtr = 0;

    total_sub_amt = parseFloat(total_sub_amt) + parseFloat(pt_amt);
    if (isNaN(total_sub_amt) || total_sub_amt == "") total_sub_amt = 0;

    total_disc_amt = parseFloat(total_disc_amt) + parseFloat(pt_disc_amt);
    if (isNaN(total_disc_amt) || total_disc_amt == "") total_disc_amt = 0;

    total_extra_amt = parseFloat(total_extra_amt) + parseFloat(pt_extra_amt);
    if (isNaN(total_extra_amt) || total_extra_amt == "") total_extra_amt = 0;

    total_taxable_amt =
      parseFloat(total_taxable_amt) + parseFloat(pt_extra_amt) + parseFloat(pt_taxable_amt);
    if (isNaN(total_taxable_amt) || total_taxable_amt == "")
      total_taxable_amt = 0;

    total_sgst_amt = parseFloat(total_sgst_amt) + parseFloat(pt_sgst_amt);
    if (isNaN(total_sgst_amt) || total_sgst_amt == "") total_sgst_amt = 0;

    total_cgst_amt = parseFloat(total_cgst_amt) + parseFloat(pt_cgst_amt);
    if (isNaN(total_cgst_amt) || total_cgst_amt == "") total_cgst_amt = 0;

    total_igst_amt = parseFloat(total_igst_amt) + parseFloat(pt_igst_amt);
    if (isNaN(total_igst_amt) || total_igst_amt == "") total_igst_amt = 0;

    total_total_amt = parseFloat(total_total_amt) + parseFloat(pt_total_amt);
    if (isNaN(total_total_amt) || total_total_amt == "") total_total_amt = 0;
  });
  $("#pm_total_qty").val(total_qty);
  $("#pm_total_mtr").val(total_total_mtr.toFixed(2));
  $("#pm_sub_amt").val(total_sub_amt.toFixed(2));
  $("#pm_disc_amt").val(total_disc_amt.toFixed(2));
  $("#pm_taxable_amt").val(total_taxable_amt.toFixed(2));
  $("#pm_extra_amt").val(total_extra_amt.toFixed(2));
  $("#pm_sgst_amt").val(total_sgst_amt.toFixed(2));
  $("#pm_cgst_amt").val(total_cgst_amt.toFixed(2));
  $("#pm_igst_amt").val(total_igst_amt.toFixed(2));

  let bill_disc_per = $(`#pm_bill_disc_per`).val();
  if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;

  let bill_disc_amt = $(`#pm_bill_disc_amt`).val();
  if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;

  if (fromDiscPer) {
    bill_disc_amt =
      (parseFloat(total_total_amt) * parseFloat(bill_disc_per)) / 100;
    if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;
    $(`#pm_bill_disc_amt`).val(bill_disc_amt.toFixed(0));
  } else {
    bill_disc_per =
      (parseFloat(bill_disc_amt) * 100) / parseFloat(total_total_amt);
    if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;
    $(`#pm_bill_disc_per`).val(bill_disc_per.toFixed(2));
  }

  let freight_amt = $(`#pm_freight_amt`).val();
  if (isNaN(freight_amt) || freight_amt == "") freight_amt = 0;

  let total_amt = (parseFloat(total_total_amt) + parseFloat(freight_amt)) - parseFloat(bill_disc_amt);
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;

  let after_decimal = parseFloat("0." + total_amt.toString().split(".")[1]);
  after_decimal = after_decimal.toFixed(2);
  after_decimal = after_decimal == 1 ? 0 : after_decimal;
  $("#pm_round_off").val(after_decimal);

  $("#pm_total_amt").val(Math.round(total_amt));

  if (total_amt > 0) {
    $(".master_block_btn").prop("disabled", false);
    notifier("pm_total_mtr");
    notifier("pm_total_amt");
  } else {
    $(".master_block_btn").prop("disabled", true);
    notifier("pm_total_mtr", "Required");
    notifier("pm_total_amt", "Required");
  }
};
const check_transaction = () => {
  let pt_id = 0;
  let bm_id = 0;
  let flag = true;
  if (trans_data.length > 0) {
    trans_data.forEach((value) => {
      const { pt_fabric_id, pt_qty, pt_mtr, pt_total_mtr, pt_rate, pt_amt } =
        value;
      if (pt_fabric_id == 0 || pt_fabric_id == "" || pt_fabric_id == null) {
        pt_id = id;
        flag = false;
      } else {
      }

      if (pt_qty == 0 || pt_qty == "") {
        pt_id = id;
        flag = false;
      } else if (pt_qty < 0) {
        pt_id = id;
        flag = false;
      } else {
      }

      if (pt_mtr == 0 || pt_mtr == "") {
        pt_id = id;
        flag = false;
      } else if (pt_mtr < 0) {
        pt_id = id;
        flag = false;
      } else {
      }

      if (pt_total_mtr == 0 || pt_total_mtr == "") {
        pt_id = id;
        flag = false;
      } else if (pt_total_mtr < 0) {
        pt_id = id;
        flag = false;
      } else {
      }

      if (pt_rate == 0 || pt_rate == "") {
        pt_id = id;
        flag = false;
      } else if (pt_rate < 0) {
        pt_id = id;
        flag = false;
      } else {
      }

      if (pt_amt == 0 || pt_amt == "") {
        pt_id = id;
        flag = false;
      } else if (pt_amt < 0) {
        pt_id = id;
        flag = false;
      } else {
      }
    });
  }
  if (!flag) {
    if (pt_id != 0 && bm_id != 0) {
      //   $(`#bm_roll_no_${bm_id}`).focus();
      //   if ($(`#bm_roll_no_${bm_id}`).length) {
      //     $(window).scrollTop(
      //       $(`#bm_roll_no_${bm_id}`).offset().top - $(window).height() / 2
      //     );
      //   }
    }
  }
  return flag;
};
const add_transaction = () => {
  remove_master_notifier();
  let check = true;
  if ($("#fabric_id").val() == null) {
    notifier("fabric_id", "Required");
    check = false;
  }
  if ($("#design_id").val() == null) {
    notifier("design_id", "Required");
    check = false;
  }
  if ($("#qty").val() == "" || $("#qty").val() == 0) {
    notifier("qty", "Required");
    check = false;
  } else {
    if ($("#qty").val() < 0) {
      notifier("qty", "Invalid qty");
      check = false;
    }
  }
  if ($("#mtr").val() == "" || $("#mtr").val() == 0) {
    notifier("mtr", "Required");
    check = false;
  } else {
    if ($("#mtr").val() < 0) {
      notifier("mtr", "Invalid mtr");
      check = false;
    }
  }
  if ($("#total_mtr").val() == "" || $("#total_mtr").val() == 0) {
    notifier("total_mtr", "Required");
    check = false;
  } else {
    if ($("#total_mtr").val() < 0) {
      notifier("total_mtr", "Invalid mtr");
      check = false;
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
  if ($("#mrp").val() == "" || $("#mrp").val() == 0) {
    notifier("mrp", "Required");
    check = false;
  } else {
    if ($("#mrp").val() < 0) {
      notifier("mrp", "Invalid mrp");
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
    let pt_id = $("#pt_id").val();
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
            if (pt_id == 0) {
              trans_data.unshift(data);
              add_wrapper_data(data);
              toastr.success(
                `${$("#fabric_id :selected").text()}`,
                "FABRIC ADDED TO LIST.",
                { closeButton: true, progressBar: true }
              );
            } else {
              let index = trans_data.findIndex((value) => value.pt_id == pt_id);
              if (index > -1) {
                
                trans_data[index].pt_fabric_id = data["pt_fabric_id"];
                trans_data[index].fabric_name = data["fabric_name"];
                $(`#fabric_name_${pt_id}`).html(data["fabric_name"]);

                trans_data[index].pt_design_id = data["pt_design_id"];
                trans_data[index].design_name = data["design_name"];
                $(`#design_name_${pt_id}`).html(data["design_name"]);

                trans_data[index].pt_category_id = data["pt_category_id"];
                trans_data[index].category_name = data["category_name"];
                $(`#category_name_${pt_id}`).html(data["category_name"]);

                trans_data[index].pt_color_id = data["pt_color_id"];
                trans_data[index].color_name = data["color_name"];
                $(`#color_name_${pt_id}`).html(data["color_name"]);

                trans_data[index].pt_width_id = data["pt_width_id"];
                trans_data[index].width_name = data["width_name"];
                $(`#width_name_${pt_id}`).html(data["width_name"]);

                trans_data[index].pt_hsn_id = data["pt_hsn_id"];
                trans_data[index].hsn_name = data["hsn_name"];
                $(`#hsn_name_${pt_id}`).html(data["hsn_name"]);

                trans_data[index].design_image = data["design_image"];
                trans_data[index].pt_cost_char = data["pt_cost_char"];
                trans_data[index].pt_mrp = data["pt_mrp"];
                trans_data[index].pt_qty = data["pt_qty"];
                trans_data[index].pt_mtr = data["pt_mtr"];
                trans_data[index].pt_total_mtr = data["pt_total_mtr"];
                trans_data[index].pt_rate = data["pt_rate"];
                trans_data[index].pt_amt = data["pt_amt"];
                trans_data[index].pt_disc_per = data["pt_disc_per"];
                trans_data[index].pt_disc_amt = data["pt_disc_amt"];
                trans_data[index].pt_taxable_amt = data["pt_taxable_amt"];

                trans_data[index].pt_extra_amt = data["pt_extra_amt"];
                trans_data[index].pt_actual_taxable_amt = data["pt_actual_taxable_amt"];

                trans_data[index].pt_sgst_per = data["pt_sgst_per"];
                trans_data[index].pt_sgst_amt = data["pt_sgst_amt"];
                trans_data[index].pt_cgst_per = data["pt_cgst_per"];
                trans_data[index].pt_cgst_amt = data["pt_cgst_amt"];
                trans_data[index].pt_igst_per = data["pt_igst_per"];
                trans_data[index].pt_igst_amt = data["pt_igst_amt"];

                trans_data[index].pt_shirt_mrp    = data["pt_shirt_mrp"];
                trans_data[index].pt_trouser_mrp  = data["pt_trouser_mrp"];
                trans_data[index].pt_2pc_suit_mrp = data["pt_2pc_suit_mrp"];
                trans_data[index].pt_3pc_suit_mrp = data["pt_3pc_suit_mrp"];
                trans_data[index].pt_jacket_mrp   = data["pt_jacket_mrp"];
                trans_data[index].pt_total_amt = data["pt_total_amt"];
                trans_data[index].pt_description = data["pt_description"];


                $(`#mrp_${pt_id}`).html(data["pt_mrp"]);
                $(`#qty_${pt_id}`).html(data["pt_qty"]);
                $(`#mtr_${pt_id}`).html(data["pt_mtr"]);
                $(`#total_mtr_${pt_id}`).html(data["pt_total_mtr"]);
                $(`#rate_${pt_id}`).html(data["pt_rate"]);
                $(`#amt_${pt_id}`).html(data["pt_amt"]);
                $(`#disc_per_${pt_id}`).html(data["pt_disc_per"]);
                $(`#disc_amt_${pt_id}`).html(data["pt_disc_amt"]);
                $(`#taxable_amt_${pt_id}`).html(data["pt_taxable_amt"]);

                $(`#extra_amt_${pt_id}`).html(data["pt_extra_amt"]);

                $(`#sgst_per_${pt_id}`).html(data["pt_sgst_per"]);
                $(`#sgst_amt_${pt_id}`).html(data["pt_sgst_amt"]);
                $(`#cgst_per_${pt_id}`).html(data["pt_cgst_per"]);
                $(`#cgst_amt_${pt_id}`).html(data["pt_cgst_amt"]);
                $(`#igst_per_${pt_id}`).html(data["pt_igst_per"]);
                $(`#igst_amt_${pt_id}`).html(data["pt_igst_amt"]);
                
                $(`#shirt_mrp_${pt_id}`).html(data["pt_shirt_mrp"]);
                $(`#trouser_mrp_${pt_id}`).html(data["pt_trouser_mrp"]);
                $(`#2pc_suit_mrp_${pt_id}`).html(data["pt_2pc_suit_mrp"]);
                $(`#3pc_suit_mrp_${pt_id}`).html(data["pt_3pc_suit_mrp"]);
                $(`#jacket_mrp_${pt_id}`).html(data["pt_jacket_mrp"]);
                
                $(`#total_amt_${pt_id}`).html(data["pt_total_amt"]);
                $(`#description_${pt_id}`).html(data["pt_description"]);

                toastr.success(
                  `${$("#fabric_id :selected").text()}`,
                  "ITEM UPDATED TO LIST.",
                  { closeButton: true, progressBar: true }
                );
              }
            }
            $("#mtr").val("").focus();
            $("#total_mtr").val(0);
            $("#amt").val(0);
            $("#pt_id").val(0);
            calculate_transaction(true);
            calculate_master(true);
            $("#transaction_count").html(trans_data.length);
          }
        }
      },
      (errmsg) => { }
    );
  }
};
const add_wrapper_data = (data, append = false) => {
  let pm_id = $("#id").val();
  const {
    encrypt_pt_id,
    pt_id,
    fabric_name,
    design_name,
    category_name,
    color_name,
    width_name,
    hsn_name,
    design_image,
    pt_mrp,
    pt_qty,
    pt_mtr,
    pt_total_mtr,
    pt_rate,
    pt_amt,
    pt_disc_per,
    pt_disc_amt,
    pt_taxable_amt,
    pt_extra_amt,
    pt_sgst_per,
    pt_sgst_amt,
    pt_cgst_per,
    pt_cgst_amt,
    pt_igst_per,
    pt_igst_amt,
    pt_shirt_mrp,
    pt_trouser_mrp,
    pt_2pc_suit_mrp,
    pt_3pc_suit_mrp,
    pt_jacket_mrp,
    pt_total_amt,
    pt_description,
    isExist,
    sr_no = ''
  } = data;
  let tr = `<tr id="row_${pt_id}">
                <td id="sr_no_${pt_id}">${sr_no}</td>
                <td id="fabric_name_${pt_id}">${fabric_name}</td>
                <td id="design_name_${pt_id}">${design_name}</td>
                <td id="category_name_${pt_id}">${category_name}</td>
                <td id="color_name_${pt_id}">${color_name}</td>
                <td id="width_name_${pt_id}">${width_name}</td>
                <td id="hsn_name_${pt_id}">${hsn_name}</td>
                <td id="mrp_${pt_id}">${pt_mrp}</td>
                <td id="qty_${pt_id}">${pt_qty}</td>
                <td id="mtr_${pt_id}">${pt_mtr}</td>
                <td id="total_mtr_${pt_id}">${pt_total_mtr}</td>
                <td id="rate_${pt_id}">${pt_rate}</td>
                <td id="amt_${pt_id}">${pt_amt}</td>
                <td id="disc_per_${pt_id}">${pt_disc_per}</td>
                <td id="disc_amt_${pt_id}">${pt_disc_amt}</td>
                <td id="taxable_amt_${pt_id}">${pt_taxable_amt}</td>
                <td id="extra_amt_${pt_id}">${pt_extra_amt}</td>
                <td id="sgst_per_${pt_id}">${pt_sgst_per}</td>
                <td id="sgst_amt_${pt_id}">${pt_sgst_amt}</td>
                <td id="cgst_per_${pt_id}">${pt_cgst_per}</td>
                <td id="cgst_amt_${pt_id}">${pt_cgst_amt}</td>
                <td id="igst_per_${pt_id}">${pt_igst_per}</td>
                <td id="igst_amt_${pt_id}">${pt_igst_amt}</td>
                
                <td id="shirt_mrp_${pt_id}">${pt_shirt_mrp}</td>
                <td id="trouser_mrp_${pt_id}">${pt_trouser_mrp}</td>
                <td id="2pc_suit_mrp_${pt_id}">${pt_2pc_suit_mrp}</td>
                <td id="3pc_suit_mrp_${pt_id}">${pt_3pc_suit_mrp}</td>
                <td id="jacket_mrp_${pt_id}">${pt_jacket_mrp}</td>
                
                <td id="total_amt_${pt_id}">${pt_total_amt}</td>
                <td id="description_${pt_id}">${pt_description}</td>
                <td>
                    <div class="navigationn_wrapper">
                        <div class="navigationn">
                            <div class="menuToggle" id="menu_toggle_${pt_id}" onclick="toggle_menuu(this)"></div>
                            <div class="menuu">
                                <ul>
                                    ${pm_id != 0
      ? `<li>
                                                <a 
                                                    type="button" 
                                                    class="btn btn-sm" 
                                                    target="_blank" 
                                                    href="${base_url}/${link}/${sub_link}?action=barcode&clause=bm.bm_pt_id&id=${encrypt_pt_id}"
                                                    ><i class="text-info fa fa-barcode"></i></a>
                                            </li>`
      : ``
    }
                                    <li>
                                        <a 
                                            type="button" 
                                            class="btn btn-md" 
                                            onclick="edit_transaction(${pt_id})"
                                            ><i class="text-success fa fa-edit"></i></a>
                                    </li>
                                    <li>
                                        ${isExist
      ? `<button 
                                                type="button" 
                                                class="btn btn-md"
                                                ><i class="text-danger fa fa-ban"></i></button>`
      : `<a 
                                                type="button" 
                                                class="btn btn-md" 
                                                onclick="remove_transaction(${pt_id})"
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
  $(`#row_${pt_id}`).mouseover((event) => {
    $("#image-preview").html(`<img 
                                      class="img-thumbnail pan form_loading" 
                                      onclick="zoom_image(${pt_id})" 
                                      title="click to zoom in and zoom out" 
                                      src="${LAZYLOADING}" 
                                      data-src="${design_image}" 
                                      data-big="${design_image}" 
                                      style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                  />`);
    lazy_loading("form_loading");
  });
  $(`#row_${pt_id}`).mouseout(() => {
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
  if ($(`#pm_supplier_id`).val() == null) {
    notifier(`pm_supplier_id`, "Required");
    check = false;
  }
  if ($(`#pm_total_mtr`).val() <= 0 || $(`#pm_total_mtr`).val() == "") {
    notifier(`pm_total_mtr`, "Required");
    check = false;
  }
  if ($(`#pm_total_amt`).val() <= 0 || $(`#pm_total_amt`).val() == "") {
    notifier(`pm_total_amt`, "Required");
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
        const { data, msg } = resp;
        if (handle_response(resp)) {
          if (id == 0) {
          } else {
          }
          Swal.fire({
            title:
              '<span class="text-info">Do you want to print barcode?</span>',
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Yes",
          }).then((result) => {
            if (result.isConfirmed) {
              window.open(
                `${base_url}/${link}/${sub_link}?action=barcode&clause=bm.bm_pm_id&id=${data.id}`,
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
      (errmsg) => { }
    );
  }
};
const remove_transaction_notifier = () => {
  notifier(`pm_supplier_id`);
  notifier(`pm_total_mtr`);
  notifier(`pm_total_amt`);
};
const remove_master_notifier = () => {
  notifier("fabric_id");
  notifier("design_id");
  notifier("category_id");
  notifier("color_id");
  notifier("qty");
  notifier("mtr");
  notifier("total_mtr");
  notifier("rate");
  notifier("amt");
};
const remove_transaction = (pt_id) => {
  trans_data = trans_data.filter((value) => value.pt_id != pt_id);
  let fabric_name = $(`#fabric_name_${pt_id}`).html();
  toastr.success(`${fabric_name}`, "ITEM REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#row_${pt_id}`).detach();
  $("#transaction_count").html(trans_data.length);
  calculate_master();
};
const edit_transaction = (pt_id) => {
  const find = trans_data.find((value) => value["pt_id"] == pt_id);
  const {
    pt_fabric_id,
    fabric_name,
    pt_design_id,
    design_name,
    pt_category_id,
    category_name,
    pt_color_id,
    color_name,
    pt_width_id,
    width_name,
    pt_hsn_id,
    hsn_name,
    pt_mrp,
    pt_qty,
    pt_mtr,
    pt_total_mtr,
    pt_rate,
    pt_amt,
    pt_disc_per,
    pt_disc_amt,
    pt_taxable_amt,
    pt_extra_amt,
    pt_sgst_per,
    pt_sgst_amt,
    pt_cgst_per,
    pt_cgst_amt,
    pt_igst_per,
    pt_igst_amt,
    
    pt_shirt_mrp,
    pt_trouser_mrp,
    pt_2pc_suit_mrp,
    pt_3pc_suit_mrp,
    pt_jacket_mrp,

    pt_total_amt,
    pt_description,
    pt_cost_char,
  } = find;
  $("#pt_id").val(pt_id);
  $("#cost_char").val(pt_cost_char);
  $("#fabric_id").html(
    `<option value="${pt_fabric_id}">${fabric_name}</option>`
  );
  $("#design_id").html(
    `<option value="${pt_design_id}">${design_name}</option>`
  );
  $("#category_id").html(`<option value="${pt_category_id}">${category_name}</option>`);
  $("#color_id").html(`<option value="${pt_color_id}">${color_name}</option>`);
  $("#width_id").html(`<option value="${pt_width_id}">${width_name}</option>`);
  $("#hsn_id").html(`<option value="${pt_hsn_id}">${hsn_name}</option>`);
  $("#mrp").val(pt_mrp);
  $("#qty").val(pt_qty);
  $("#mtr").val(pt_mtr);
  $("#total_mtr").val(pt_total_mtr);
  $("#rate").val(pt_rate);
  $("#amt").val(pt_amt);
  $("#disc_per").val(pt_disc_per);
  $("#disc_amt").val(pt_disc_amt);
  $("#taxable_amt").val(pt_taxable_amt);
  $("#extra_amt").val(pt_extra_amt);
  $("#sgst_per").val(pt_sgst_per);
  $("#sgst_amt").val(pt_sgst_amt);
  $("#cgst_per").val(pt_cgst_per);
  $("#cgst_amt").val(pt_cgst_amt);
  $("#igst_per").val(pt_igst_per);
  $("#igst_amt").val(pt_igst_amt);
  
  $("#shirt_mrp").val(pt_shirt_mrp);
  $("#trouser_mrp").val(pt_trouser_mrp);
  $("#2pc_suit_mrp").val(pt_2pc_suit_mrp);
  $("#3pc_suit_mrp").val(pt_3pc_suit_mrp);
  $("#jacket_mrp").val(jacket_mrp);

  $("#total_amt").val(pt_total_amt);
  $("#description").val(pt_description);
  toggle_menuu({ id: pt_id });
};

const record_remove = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.pm_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                  <tbody>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">entry no : </td>
                          <td width="70%">${data.pm_entry_no}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">entry date : </td>
                          <td width="70%">${data.pm_entry_date}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">supplier : </td>
                          <td width="70%">${data.supplier_name}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">total mtr : </td>
                          <td width="70%">${data.pm_total_mtr}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">total amt : </td>
                          <td width="70%">${data.pm_total_amt}</td>
                      </tr>
                  </tbody>
              </table>`;
  remove_datav3({ path, form_data, html });
};
// core_functions

// additional_functions
const get_supplier_data = (id) => {
  $("#pm_gst_type").val(0);
  if (id) {
    const path = `${link}/${sub_link}/handler`;
    const form_data = { func: "get_supplier_data", id };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          $("#pm_gst_type").val(data);
          calculate_master(false);
        }
      },
      (errmsg) => { }
    );
  }
};
const get_fabric_data = (id) => {
  $("#sgst_per").val(0);
  $("#cgst_per").val(0);
  $("#igst_per").val(0);
  if (id) {
    const path = `master/fabric/handler`;
    const form_data = { func: "get_data", id };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $("#sgst_per").val(data[0]["fabric_sgst_per"]);
            $("#cgst_per").val(data[0]["fabric_cgst_per"]);
            $("#igst_per").val(data[0]["fabric_igst_per"]);
            calculate_transaction();
          }
        }
      },
      (errmsg) => { }
    );
  }
};
const get_fabric_data_for_trans = (pt_id, id) => {
  $(`#pt_sgst_per_${pt_id}`).val(0);
  $(`#pt_cgst_per_${pt_id}`).val(0);
  $(`#pt_igst_per_${pt_id}`).val(0);
  calculate_master();
  if (id) {
    const path = `master/fabric/handler`;
    const form_data = { func: "get_data", id };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $(`#pt_sgst_per_${pt_id}`).val(data[0]["fabric_sgst_per"]);
            $(`#pt_cgst_per_${pt_id}`).val(data[0]["fabric_cgst_per"]);
            $(`#pt_igst_per_${pt_id}`).val(data[0]["fabric_igst_per"]);
            calculate_master();
          }
        }
      },
      (errmsg) => { }
    );
  }
};
const set_cost_char = () => {
  let cost_char = "";
  let rate = $(`#rate`).val();
  if (isNaN(rate) || rate == "") rate = 0;
  if (rate.length != 0) {
    for (let pos = 0; pos < rate.length; pos++) {
      let char = rate.charAt(pos);

      cost_char += char == "." ? "." : $(`#cost_char_${char}`).val();
    }
  }
  $(`#cost_char`).val(cost_char);
};
// additional_functions
