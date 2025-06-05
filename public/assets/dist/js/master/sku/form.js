$(document).ready(function () {
    $("#sku_apparel_id").select2(select2_default({
        url: `master/apparel/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));

    $("#sku_color_id").select2(select2_default({
        url: `master/color/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));
    
    // $("#bm_id").select2(select2_default({
    //     url: `${link}/${sub_link}/get_select2/_bm_id`,
    //     placeholder: "SELECT",
    //     param: true,
    // })).on('change', event => get_barcode_data(event.target.value))

    $("#fabric_id").select2(select2_default({
        url: `master/fabric/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));
    $("#design_id").select2(select2_default({
        url: `${link}/${sub_link}/get_select2/_design_id`,
        placeholder: "SELECT",
        param: true,
    }));
    $("#color_id").select2(select2_default({
        url: `master/color/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));

    $("#width_id").select2(select2_default({
        url: `master/width/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));

    $("#dying_id").select2(select2_default({
        url: `master/dying/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));

    $("#karigar_id").select2(select2_default({
        url: `master/karigar/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));

    $("#apparel_id").select2(select2_default({
        url: `master/apparel/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));

    $("#embroidery_id").select2(select2_default({
        url: `master/embroidery/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));

    $("#other_id").select2(select2_default({
        url: `master/other/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
    }));
});
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
              const {design_trans, dying_trans, karigar_trans, embroidery_trans, other_trans, image_trans} = data;
              if (design_trans && design_trans.length != 0) {
                design_data = design_trans;
                design_data.forEach((value) => add_wrapper_design(value, true));
              }
              if (dying_trans && dying_trans.length != 0) {
                dying_data = dying_trans;
                dying_data.forEach((value) => add_wrapper_dying(value, true));
              }
              if (karigar_trans && karigar_trans.length != 0) {
                karigar_data = karigar_trans;
                karigar_data.forEach((value) => add_wrapper_karigar(value, true));
              }
              if (embroidery_trans && embroidery_trans.length != 0) {
                embroidery_data = embroidery_trans;
                embroidery_data.forEach((value) => add_wrapper_embroidery(value, true));
              }
              if (other_trans && other_trans.length != 0) {
                other_data = other_trans;
                other_data.forEach((value) => add_wrapper_other(value, true));
              }
              if (image_trans && image_trans.length != 0) {
                image_data = image_trans;
                image_data.forEach((value) => add_wrapper_image(value, true));
              }
              $("#design_count").html(design_data.length);
              $("#dying_count").html(dying_data.length);
              $("#karigar_count").html(karigar_data.length);
              $("#embroidery_count").html(embroidery_data.length);
              $("#other_count").html(other_data.length);
              $("#image_count").html(image_data.length);
              calculate_master();
              lazy_loading('form_loading');
            }
          },
          (errmsg) => {}
        );
      }
    }
};
const remove_sku_image = () => {
    $("#sku_photo").val("");
    $("#sku_pic").val(NOIMAGE);
    $("#preview").html(`<img class="img-thumbnail" width="145px" src="${NOIMAGE}" />`);
};
const calculate_master = () => {
    let total_mtr = 0;
    let total_amt = 0;

    let total_design_mtr = 0;
    let total_design_amt = 0;
    let total_dying_amt = 0;
    let total_karigar_amt = 0;
    let total_embroidery_amt = 0;
    let total_other_amt = 0;

    design_data.forEach((value) => {
        let mtr = parseFloat(value['sdt_mtr']);
        if (isNaN(mtr) || mtr == "") mtr = 0;

        let rate = parseFloat(value['sdt_rate']);
        if (isNaN(rate) || rate == "") rate = 0;

        let amt = parseFloat(mtr) * parseFloat(rate);
        if (isNaN(amt) || amt == "") amt = 0;

        total_design_mtr = parseFloat(total_design_mtr) + parseFloat(mtr);
        if (isNaN(total_design_mtr) || total_design_mtr == "") total_design_mtr = 0;

        total_design_amt = parseFloat(total_design_amt) + parseFloat(amt);
        if (isNaN(total_design_amt) || total_design_amt == "") total_design_amt = 0;
    });

    dying_data.forEach((value) => {
      let rate = parseFloat(value['sdyt_rate']);
      if (isNaN(rate) || rate == "") rate = 0;

      total_dying_amt = parseFloat(total_dying_amt) + parseFloat(rate);
      if (isNaN(total_dying_amt) || total_dying_amt == "") total_dying_amt = 0;
    });
    
    karigar_data.forEach((value) => {
      let rate = parseFloat(value['skt_rate']);
      if (isNaN(rate) || rate == "") rate = 0;

      total_karigar_amt = parseFloat(total_karigar_amt) + parseFloat(rate);
      if (isNaN(total_karigar_amt) || total_karigar_amt == "") total_karigar_amt = 0;
    });

    embroidery_data.forEach((value) => {
      let rate = parseFloat(value['set_rate']);
      if (isNaN(rate) || rate == "") rate = 0;

      total_embroidery_amt = parseFloat(total_embroidery_amt) + parseFloat(rate);
      if (isNaN(total_embroidery_amt) || total_embroidery_amt == "") total_embroidery_amt = 0;
    });

    other_data.forEach((value) => {
      let rate = parseFloat(value['sot_rate']);
      if (isNaN(rate) || rate == "") rate = 0;

      total_other_amt = parseFloat(total_other_amt) + parseFloat(rate);
      if (isNaN(total_other_amt) || total_other_amt == "") total_other_amt = 0;
    });

    $('#total_design_mtr').html(total_design_mtr.toFixed(2));
    $('#total_design_amt').html(total_design_amt.toFixed(2));
    $('#total_dying_amt').html(total_dying_amt.toFixed(2));
    $('#total_karigar_amt').html(total_karigar_amt.toFixed(2));
    $('#total_embroidery_amt').html(total_embroidery_amt.toFixed(2));
    $('#total_other_amt').html(total_other_amt.toFixed(2));

    total_amt = parseFloat(total_design_amt) + parseFloat(total_dying_amt) + parseFloat(total_karigar_amt) + parseFloat(total_embroidery_amt) + parseFloat(total_other_amt);
    if (isNaN(total_amt) || total_amt == "") total_amt = 0;
    $('#total_amt').html(total_amt.toFixed(2));
    $('#sku_rate').val(total_amt.toFixed(2));

    total_mtr = parseFloat(total_design_mtr);
    if (isNaN(total_mtr) || total_mtr == "") total_mtr = 0;
    $('#total_mtr').html(total_mtr.toFixed(2));
    $('#sku_mtr').val(total_mtr.toFixed(2));

    $(".master_block_btn").prop("disabled", false);
  
    if (total_amt <= 0) {
        toastr.error("Rate not defined.", "", {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
        });
        $(".master_block_btn").prop("disabled", true);
    }
}
const add_edit = () => {
    event.preventDefault();
    notifier('sku_apparel_id');
    notifier('sku_name');
    notifier('sku_piece');
    let check = true;
    
    if ($(`#sku_apparel_id`).val() == null) {
      notifier(`sku_apparel_id`, "Required");
      check = false;
    }
    
    if ($(`#sku_name`).val() == "") {
      notifier(`sku_name`, "Required");
      check = false;
    }
    
    if ($(`#sku_piece`).val() <= 0 || $(`#sku_piece`).val() == "") {
        notifier(`sku_piece`, "Required");
        check = false;
    }

    if (!check) {
      toastr.error("You forgot to enter some information.", "Oh snap!!!", {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
      });
      return;
    }
    if ($(`#total_amt`).html() <= 0 || $(`#total_amt`).html() == "") {
        toastr.error("Total amt is required.", "", {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
        });
        return;
    } 

    const path = `${link}/${sub_link}/handler`;
    let form_id = document.getElementById("_form");
    let form_data = new FormData(form_id);
    form_data.append("func", "add_edit");
    form_data.append("design_data", JSON.stringify(design_data));
    form_data.append("dying_data", JSON.stringify(dying_data));
    form_data.append("karigar_data", JSON.stringify(karigar_data));
    form_data.append("embroidery_data", JSON.stringify(embroidery_data));
    form_data.append("other_data", JSON.stringify(other_data));
    form_data.append("image_data", JSON.stringify(image_data));
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
            
            notifier('sku_apparel_id');
            notifier('sku_name');
            notifier('sku_piece');
            toastr.success("", msg, {
                closeButton: true,
                progressBar: true,
                preventDuplicates: true,
            });
            setTimeout(() => {
              window.location.reload();
            }, RELOAD_TIME);
        }
    },
    (errmsg) => {}
    );
  };