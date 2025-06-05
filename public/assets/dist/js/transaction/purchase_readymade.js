$(document).ready(function () { 
  $(`#prmm_supplier_id`)
    .select2(
      select2_default({
        url: `master/supplier/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
      })
    )
    .on("change", (event) => get_supplier_data(event.target.value));

  $(`#product_id`).select2(
    select2_default({
      url: `master/product/get_select2/_id`,
      placeholder: "SELECT",
    })
  );

  $(`#product_id`).select2(
    select2_default({
      url: `master/product/get_select2/_id`,
      placeholder: "SELECT",
    })
  );

  $(`#design_id`).select2(
    select2_default({
      url: `master/design/get_select2/_id`,
      placeholder: "SELECT",
      param: () => $("#prmm_supplier_id").val(),
    })
  );

  $(`#readymade_category_id`).select2(
    select2_default({
      url: `master/readymade_category/get_select2/_id`,
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

  $(`#gender_id`).select2(
    select2_default({
      url: `master/gender/get_select2/_id`,
      placeholder: "SELECT",
      param: true,
    })
  );

  $(`#size_id`).select2(
    select2_default({
      url: `master/size/get_select2/_id`,
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
            set_gst_area();
            $("#transaction_count").html(trans_data.length);
          }
        },
        (errmsg) => {}
      );
    }
  }
};
const calculate_transaction = (fromDiscPer = false) => {
  let qty = $("#qty").val();
  if (isNaN(qty) || qty == "") qty = 0;

  let rate = $("#rate").val();
  if (isNaN(rate) || rate == "") rate = 0;

  let amt = parseFloat(qty) * parseFloat(rate);
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

  if ($("#prmm_gst_type").val() == 0) {
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
      prmt_qty,
      prmt_amt,
      prmt_disc_amt,
      prmt_taxable_amt,
      prmt_extra_amt,
      prmt_sgst_amt,
      prmt_cgst_amt,
      prmt_igst_amt,
      prmt_total_amt,
    } = value;

    total_qty = parseInt(total_qty) + parseInt(prmt_qty);
    if (isNaN(total_qty) || total_qty == "") total_qty = 0;

    total_sub_amt = parseFloat(total_sub_amt) + parseFloat(prmt_amt);
    if (isNaN(total_sub_amt) || total_sub_amt == "") total_sub_amt = 0;

    total_disc_amt = parseFloat(total_disc_amt) + parseFloat(prmt_disc_amt);
    if (isNaN(total_disc_amt) || total_disc_amt == "") total_disc_amt = 0;

    total_extra_amt = parseFloat(total_extra_amt) + parseFloat(prmt_extra_amt);
    if (isNaN(total_extra_amt) || total_extra_amt == "") total_extra_amt = 0;

    total_taxable_amt =
      parseFloat(total_taxable_amt) + parseFloat(prmt_extra_amt) + parseFloat(prmt_taxable_amt);
    if (isNaN(total_taxable_amt) || total_taxable_amt == "")
      total_taxable_amt = 0;

    total_sgst_amt = parseFloat(total_sgst_amt) + parseFloat(prmt_sgst_amt);
    if (isNaN(total_sgst_amt) || total_sgst_amt == "") total_sgst_amt = 0;

    total_cgst_amt = parseFloat(total_cgst_amt) + parseFloat(prmt_cgst_amt);
    if (isNaN(total_cgst_amt) || total_cgst_amt == "") total_cgst_amt = 0;

    total_igst_amt = parseFloat(total_igst_amt) + parseFloat(prmt_igst_amt);
    if (isNaN(total_igst_amt) || total_igst_amt == "") total_igst_amt = 0;

    total_total_amt = parseFloat(total_total_amt) + parseFloat(prmt_total_amt);
    if (isNaN(total_total_amt) || total_total_amt == "") total_total_amt = 0;
  });
  $("#prmm_total_qty").val(total_qty);
  $("#prmm_sub_amt").val(total_sub_amt.toFixed(2));
  $("#prmm_disc_amt").val(total_disc_amt.toFixed(2));
  $("#prmm_taxable_amt").val(total_taxable_amt.toFixed(2));
  $("#prmm_extra_amt").val(total_extra_amt.toFixed(2));
  $("#prmm_sgst_amt").val(total_sgst_amt.toFixed(2));
  $("#prmm_cgst_amt").val(total_cgst_amt.toFixed(2));
  $("#prmm_igst_amt").val(total_igst_amt.toFixed(2));

  let bill_disc_per = $(`#prmm_bill_disc_per`).val();
  if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;

  let bill_disc_amt = $(`#prmm_bill_disc_amt`).val();
  if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;

  if (fromDiscPer) {
    bill_disc_amt =
      (parseFloat(total_total_amt) * parseFloat(bill_disc_per)) / 100;
    if (isNaN(bill_disc_amt) || bill_disc_amt == "") bill_disc_amt = 0;
    $(`#prmm_bill_disc_amt`).val(bill_disc_amt.toFixed(0));
  } else {
    bill_disc_per =
      (parseFloat(bill_disc_amt) * 100) / parseFloat(total_total_amt);
    if (isNaN(bill_disc_per) || bill_disc_per == "") bill_disc_per = 0;
    $(`#prmm_bill_disc_per`).val(bill_disc_per.toFixed(2));
  }

  let total_amt = parseFloat(total_total_amt) - parseFloat(bill_disc_amt);
  if (isNaN(total_amt) || total_amt == "") total_amt = 0;

  let after_decimal = parseFloat("0." + total_amt.toString().split(".")[1]);
  after_decimal = after_decimal.toFixed(2);
  after_decimal = after_decimal == 1 ? 0 : after_decimal;
  $("#prmm_round_off").val(after_decimal);

  $("#prmm_total_amt").val(Math.round(total_amt));

  if (total_amt > 0) {
    $(".master_block_btn").prop("disabled", false);
    notifier("prmm_total_amt");
  } else {
    $(".master_block_btn").prop("disabled", true);
    notifier("prmm_total_amt", "Required");
  }
};
const check_transaction = () => { 
  let prmt_id = 0;
  let brmm_id = 0;
  let flag = true;
  if (trans_data.length > 0) {
    trans_data.forEach((value) => {
      const { prmt_qty, prmt_rate, prmt_amt } =
        value;
     
      if (prmt_qty == 0 || prmt_qty == "") {
        prmt_id = id;
        flag = false;
      } else if (prmt_qty < 0) {
        prmt_id = id;
        flag = false;
      } else {
      }

      if (prmt_rate == 0 || prmt_rate == "") {
        prmt_id = id;
        flag = false;
      } else if (prmt_rate < 0) {
        prmt_id = id;
        flag = false;
      } else {
      }

      if (prmt_amt == 0 || prmt_amt == "") {
        prmt_id = id;
        flag = false;
      } else if (prmt_amt < 0) {
        prmt_id = id;
        flag = false;
      } else {
      }
    });
  }
  if (!flag) {
    if (prmt_id != 0 && brmm_id != 0) {
      //   $(`#brmm_roll_no_${brmm_id}`).focus();
      //   if ($(`#brmm_roll_no_${brmm_id}`).length) {
      //     $(window).scrollTop(
      //       $(`#brmm_roll_no_${brmm_id}`).offset().top - $(window).height() / 2
      //     );
      //   }
    }
  }
  return flag;
};
const add_transaction = () => {
  remove_master_notifier();
  let check = true;

  if ($("#product_id").val() == null) {
    notifier("product_id", "Required");
    check = false;
  }
  if ($("#readymade_category_id").val() == null) {
    notifier("readymade_category_id", "Required");
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
    let prmt_id = $("#prmt_id").val();
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
            if (prmt_id == 0) {
              trans_data.unshift(data);
              add_wrapper_data(data);
              toastr.success(
                `${$("#readymade_product_id :selected").text()}`,
                "ITEM ADDED TO LIST.",
                { closeButton: true, progressBar: true }
              );
            } else {
              let index = trans_data.findIndex((value) => value.prmt_id == prmt_id);
              if (index > -1) {
                trans_data[index].prmt_readymade_category_id = data["prmt_readymade_category_id"];
                trans_data[index].readymade_category_name = data["readymade_category_name"];
                $(`#readymade_category_name_${prmt_id}`).html(data["readymade_category_name"]);

                trans_data[index].prmt_product_id = data["prmt_product_id"];
                trans_data[index].product_name = data["product_name"];
                $(`#product_name_${prmt_id}`).html(data["product_name"]);

                trans_data[index].prmt_design_id = data["prmt_design_id"];
                trans_data[index].design_name = data["design_name"];
                $(`#design_name_${prmt_id}`).html(data["design_name"]);

                trans_data[index].prmt_color_id = data["prmt_color_id"];
                trans_data[index].color_name = data["color_name"];
                $(`#color_name_${prmt_id}`).html(data["color_name"]);

                trans_data[index].prmt_size_id = data["prmt_size_id"];
                trans_data[index].size_name = data["size_name"];
                $(`#size_name_${prmt_id}`).html(data["size_name"]);

                trans_data[index].prmt_gender_id = data["prmt_gender_id"];
                trans_data[index].gender_name = data["gender_name"];
                $(`#gender_name_${prmt_id}`).html(data["gender_name"]);

                trans_data[index].design_image = data["design_image"];
                trans_data[index].prmt_cost_char = data["prmt_cost_char"];
                trans_data[index].prmt_mrp = data["prmt_mrp"];
                trans_data[index].prmt_qty = data["prmt_qty"];
                trans_data[index].prmt_rate = data["prmt_rate"];
                trans_data[index].prmt_amt = data["prmt_amt"];
                trans_data[index].prmt_disc_per = data["prmt_disc_per"];
                trans_data[index].prmt_disc_amt = data["prmt_disc_amt"];
                trans_data[index].prmt_taxable_amt = data["prmt_taxable_amt"];

                trans_data[index].prmt_extra_amt = data["prmt_extra_amt"];
                trans_data[index].prmt_actual_taxable_amt = data["prmt_actual_taxable_amt"];

                trans_data[index].prmt_sgst_per = data["prmt_sgst_per"];
                trans_data[index].prmt_sgst_amt = data["prmt_sgst_amt"];
                trans_data[index].prmt_cgst_per = data["prmt_cgst_per"];
                trans_data[index].prmt_cgst_amt = data["prmt_cgst_amt"];
                trans_data[index].prmt_igst_per = data["prmt_igst_per"];
                trans_data[index].prmt_igst_amt = data["prmt_igst_amt"];
                trans_data[index].prmt_total_amt = data["prmt_total_amt"];
                trans_data[index].prmt_description = data["prmt_description"];


                $(`#mrp_${prmt_id}`).html(data["prmt_mrp"]);
                $(`#qty_${prmt_id}`).html(data["prmt_qty"]);
                $(`#rate_${prmt_id}`).html(data["prmt_rate"]);
                $(`#amt_${prmt_id}`).html(data["prmt_amt"]);
                $(`#disc_per_${prmt_id}`).html(data["prmt_disc_per"]);
                $(`#disc_amt_${prmt_id}`).html(data["prmt_disc_amt"]);
                $(`#taxable_amt_${prmt_id}`).html(data["prmt_taxable_amt"]);

                $(`#extra_amt_${prmt_id}`).html(data["prmt_extra_amt"]);

                $(`#sgst_per_${prmt_id}`).html(data["prmt_sgst_per"]);
                $(`#sgst_amt_${prmt_id}`).html(data["prmt_sgst_amt"]);
                $(`#cgst_per_${prmt_id}`).html(data["prmt_cgst_per"]);
                $(`#cgst_amt_${prmt_id}`).html(data["prmt_cgst_amt"]);
                $(`#igst_per_${prmt_id}`).html(data["prmt_igst_per"]);
                $(`#igst_amt_${prmt_id}`).html(data["prmt_igst_amt"]);
                $(`#total_amt_${prmt_id}`).html(data["prmt_total_amt"]);
                $(`#description_${prmt_id}`).html(data["prmt_description"]);

                toastr.success(
                  `${$("#readymade_product_id :selected").text()}`,
                  "ITEM UPDATED TO LIST.",
                  { closeButton: true, progressBar: true }
                );
              }
            }
            $("#amt").val(0);
            $("#prmt_id").val(0);
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
  let prmm_id = $("#id").val();
  const {
    encrypt_prmt_id,
    prmt_id,
    readymade_category_name,
    product_name,
    design_name,
    color_name,
    size_name,
    gender_name,
    design_image,
    prmt_mrp,
    prmt_qty,
    prmt_rate,
    prmt_amt,
    prmt_disc_per,
    prmt_disc_amt,
    prmt_taxable_amt,
    prmt_extra_amt,
    prmt_sgst_per,
    prmt_sgst_amt,
    prmt_cgst_per,
    prmt_cgst_amt,
    prmt_igst_per,
    prmt_igst_amt,
    prmt_total_amt,
    prmt_description,
    isExist,
  } = data;
  let tr = `<tr id="row_${prmt_id}">
                <td id="product_name_${prmt_id}">${product_name}</td>
                <td id="design_name_${prmt_id}">${design_name}</td>
                <td id="readymade_category_name_${prmt_id}">${readymade_category_name}</td>
                <td id="color_name_${prmt_id}">${color_name}</td>
                <td id="size_name_${prmt_id}">${size_name}</td>
                <td id="gender_name_${prmt_id}">${gender_name}</td>
                <td id="mrp_${prmt_id}">${prmt_mrp}</td>
                <td id="qty_${prmt_id}">${prmt_qty}</td>
                <td id="rate_${prmt_id}">${prmt_rate}</td>
                <td id="amt_${prmt_id}">${prmt_amt}</td>
                <td id="disc_per_${prmt_id}">${prmt_disc_per}</td>
                <td id="disc_amt_${prmt_id}">${prmt_disc_amt}</td>
                <td id="taxable_amt_${prmt_id}">${prmt_taxable_amt}</td>
                 <td id="extra_amt_${prmt_id}">${prmt_extra_amt}</td>
                <td id="sgst_per_${prmt_id}">${prmt_sgst_per}</td>
                <td id="sgst_amt_${prmt_id}">${prmt_sgst_amt}</td>
                <td id="cgst_per_${prmt_id}">${prmt_cgst_per}</td>
                <td id="cgst_amt_${prmt_id}">${prmt_cgst_amt}</td>
                <td id="igst_per_${prmt_id}">${prmt_igst_per}</td>
                <td id="igst_amt_${prmt_id}">${prmt_igst_amt}</td>
                <td id="total_amt_${prmt_id}">${prmt_total_amt}</td>
                <td id="description_${prmt_id}">${prmt_description}</td>
                <td>
                    <div class="navigationn_wrapper">
                        <div class="navigationn">
                            <div class="menuToggle" id="menu_toggle_${prmt_id}" onclick="toggle_menuu(this)"></div>
                            <div class="menuu">
                                <ul>
                                    ${
                                      prmm_id != 0
                                        ? `<li>
                                                <a 
                                                    type="button" 
                                                    class="btn btn-sm" 
                                                    target="_blank" 
                                                    href="${base_url}/${link}/${sub_link}?action=barcode&clause=brmm.brmm_prmt_id&id=${encrypt_prmt_id}"
                                                    ><i class="text-info fa fa-barcode"></i></a>
                                            </li>`
                                        : ``
                                    }
                                    <li>
                                        <a 
                                            type="button" 
                                            class="btn btn-md" 
                                            onclick="edit_transaction(${prmt_id})"
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
                                                onclick="remove_transaction(${prmt_id})"
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
  $(`#row_${prmt_id}`).mouseover((event) => {
    $("#image-preview").html(`<img 
                                      class="img-thumbnail pan form_loading" 
                                      onclick="zoom_image(${prmt_id})" 
                                      title="click to zoom in and zoom out" 
                                      src="${LAZYLOADING}" 
                                      data-src="${design_image}" 
                                      data-big="${design_image}" 
                                      style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                  />`);
    lazy_loading("form_loading");
  });
  $(`#row_${prmt_id}`).mouseout(() => {
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
  if ($(`#prmm_supplier_id`).val() == null) {
    notifier(`prmm_supplier_id`, "Required");
    check = false;
  }
  if ($(`#prmm_total_amt`).val() <= 0 || $(`#prmm_total_amt`).val() == "") {
    notifier(`prmm_total_amt`, "Required");
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
                `${base_url}/${link}/${sub_link}?action=barcode&clause=brmm.brmm_prmm_id&id=${data.id}`,
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
  notifier(`prmm_supplier_id`);
  notifier(`prmm_total_amt`);
};
const remove_master_notifier = () => {
  notifier("product_id");
  notifier("design_id");
  notifier("color_id");
  notifier("qty");
  notifier("rate");
  notifier("amt");
};
const remove_transaction = (prmt_id) => {
  trans_data = trans_data.filter((value) => value.prmt_id != prmt_id);
  let product_name = $(`#product_name_${prmt_id}`).html();
  toastr.success(`${product_name}`, "ITEM REMOVED FROM LIST.", {
    closeButton: true,
    progressBar: true,
  });
  $(`#row_${prmt_id}`).detach();
  $("#transaction_count").html(trans_data.length);
  calculate_master();
};
const edit_transaction = (prmt_id) => {
  const find = trans_data.find((value) => value["prmt_id"] == prmt_id);
  const {
    prmt_readymade_category_id,
    readymade_category_name,
    prmt_product_id,
    product_name,
    prmt_design_id,
    design_name,
    prmt_color_id,
    color_name,
    prmt_size_id,
    size_name,
    prmt_gender_id,
    gender_name,
    prmt_mrp,
    prmt_qty,
    prmt_rate,
    prmt_amt,
    prmt_disc_per,
    prmt_disc_amt,
    prmt_taxable_amt,
    prmt_extra_amt,
    prmt_sgst_per,
    prmt_sgst_amt,
    prmt_cgst_per,
    prmt_cgst_amt,
    prmt_igst_per,
    prmt_igst_amt,
    prmt_total_amt,
    prmt_description,
    prmt_cost_char,
  } = find;
  $("#prmt_id").val(prmt_id);
  $("#cost_char").val(prmt_cost_char);
  $("#readymade_category_id").html(
    `<option value="${prmt_readymade_category_id}">${readymade_category_name}</option>`
  );
 
  $("#product_id").html(
    `<option value="${prmt_product_id}">${product_name}</option>`
  );
  $("#design_id").html(
    `<option value="${prmt_design_id}">${design_name}</option>`
  );
  $("#color_id").html(`<option value="${prmt_color_id}">${color_name}</option>`);
  $("#size_id").html(`<option value="${prmt_size_id}">${size_name}</option>`);
  $("#gender_id").html(`<option value="${prmt_gender_id}">${gender_name}</option>`);
  $("#mrp").val(prmt_mrp);
  $("#qty").val(prmt_qty);
  $("#rate").val(prmt_rate);
  $("#amt").val(prmt_amt);
  $("#disc_per").val(prmt_disc_per);
  $("#disc_amt").val(prmt_disc_amt);
  $("#taxable_amt").val(prmt_taxable_amt);
  $("#extra_amt").val(prmt_extra_amt);
  $("#sgst_per").val(prmt_sgst_per);
  $("#sgst_amt").val(prmt_sgst_amt);
  $("#cgst_per").val(prmt_cgst_per);
  $("#cgst_amt").val(prmt_cgst_amt);
  $("#igst_per").val(prmt_igst_per);
  $("#igst_amt").val(prmt_igst_amt);
  $("#total_amt").val(prmt_total_amt);
  $("#description").val(prmt_description);
  toggle_menuu({ id: prmt_id });
};

const record_remove = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.prmm_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                  <tbody>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">entry no : </td>
                          <td width="70%">${data.prmm_entry_no}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">entry date : </td>
                          <td width="70%">${data.prmm_entry_date}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">supplier : </td>
                          <td width="70%">${data.supplier_name}</td>
                      </tr>
                      <tr>
                          <td class="font-weight-bold" width="30%" align="right">total amt : </td>
                          <td width="70%">${data.prmm_total_amt}</td>
                      </tr>
                  </tbody>
              </table>`;
  remove_datav3({ path, form_data, html });
};
// core_functions

// additional_functions
const get_supplier_data = (id) => {
  $("#prmm_gst_type").val(0);
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
          $("#prmm_gst_type").val(data);
          calculate_master(false);
          set_gst_area();
        }
      },
      (errmsg) => {}
    );
  }
};

const set_gst_area = () => {
    $("#sgst_per").attr("readonly", "readonly");
    $("#igst_per").attr("readonly", "readonly");
    if($("#prmm_gst_type").val()==0){
        $("#sgst_per").removeAttr("readonly")
    }else{
      $("#igst_per").removeAttr("readonly")
    }
}
const get_readymade_category_data_for_trans = (prmt_id, id) => {
  $(`#prmt_sgst_per_${prmt_id}`).val(0);
  $(`#prmt_cgst_per_${prmt_id}`).val(0);
  $(`#prmt_igst_per_${prmt_id}`).val(0);
  calculate_master();
  if (id) {
    const path = `master/readymade_category/handler`;
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
            $(`#prmt_sgst_per_${prmt_id}`).val(data[0]["readymade_category_sgst_per"]);
            $(`#prmt_cgst_per_${prmt_id}`).val(data[0]["readymade_category_cgst_per"]);
            $(`#prmt_igst_per_${prmt_id}`).val(data[0]["readymade_category_igst_per"]);
            calculate_master();
          }
        }
      },
      (errmsg) => {}
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

const calculate_readymade_gst = () => {
  let sgst_per = $("#sgst_per").val();
  let igst_per = parseFloat(sgst_per) * 2;
  if (isNaN(igst_per) || igst_per == "") igst_per = 0;
  $("#cgst_per").val(sgst_per);
  // $("#igst_per").val(igst_per.toFixed(2));
  calculate_transaction();
};