let design_data = [];
const get_barcode_data = (id) => {
    $('#design_preview').html(`<img class="img-thumbnail" src="${NOIMAGE}" style="max-width: 100%; max-height: 100%; object-fit: contain;"/>`);
    $('#design_id').val(null).trigger('change');
    $('#design_image').val(NOIMAGE);
    $('#design_rate').val(0);
    $('#design_mtr').val(0);
    $('#design_amt').val(0);
    calculate_design_charges();
    if (!id) return false;
    const path = `${link}/${sub_link}/handler`;
    const form_data = { func: "get_barcode_data", id };
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          if (data && data.length != 0) {
            $("#design_id").html(`<option value="${data[0]['design_id']}">${data[0]['design_name']}</option>`);
            $("#design_rate").val(data[0]["rate"]);
            $('#design_image').val(data[0]['design_image']);
            $('#design_preview').html(`<img 
                                            class="img-thumbnail pan form_loading" 
                                            onclick="zoom(this)" 
                                            title="click to zoom in and zoom out" 
                                            src="${LAZYLOADING}" 
                                            data-src="${data[0]['design_image']}" 
                                            data-big="${data[0]['design_image']}" 
                                            style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                        />`);
          }
          toastr.success(data[0]["bm_item_code"], msg, {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
          });
        } else {
          setTimeout(() => {
            $("#bm_id").val(null).trigger("change");
            $("#bm_id").select2("open");
          }, RELOAD_TIME);
        }
        lazy_loading('form_loading');
      },
      (errmsg) => {
        setTimeout(() => {
          $("#bm_id").val(null).trigger("change");
          $("#bm_id").select2("open");
        }, RELOAD_TIME);
      }
    );
}
const get_design_data = (id) => {
    $('#design_preview').html(`<img class="img-thumbnail" src="${NOIMAGE}" style="max-width: 100%; max-height: 100%; object-fit: contain;"/>`);
    $('#design_image').val(NOIMAGE);
    $('#design_rate').val(0);
    $('#design_mtr').val(0);
    $('#design_amt').val(0);
    calculate_design_charges();
    if(!id) return;
    const path = `master/design/handler`;
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
            $('#design_image').val(data[0]['design_image']);
            $('#design_preview').html(`<img 
                                            class="img-thumbnail pan form_loading" 
                                            onclick="zoom(this)" 
                                            title="click to zoom in and zoom out" 
                                            src="${LAZYLOADING}" 
                                            data-src="${data[0]['design_image']}" 
                                            data-big="${data[0]['design_image']}" 
                                            style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                        />`);
          }
        }
        lazy_loading('form_loading');
      },
      (errmsg) => {}
    );
};
const calculate_design_charges = () => {
    let rate = parseFloat($('#design_rate').val());
    if (isNaN(rate) || rate == "") rate = 0;

    let mtr = parseFloat($('#design_mtr').val());
    if (isNaN(mtr) || mtr == "") mtr = 0;

    let amt = parseFloat(rate) * parseFloat(mtr);
    if (isNaN(amt) || amt == "") amt = 0;
    $('#design_amt').val(amt.toFixed(2));
}
const add_design_transaction = () => { 
    notifier('fabric_id');
    notifier('design_id');
    notifier('color_id');
    notifier('width_id');
    notifier('design_mtr');
    let check = true; 
    if ($("#fabric_id").val() == null) {
        notifier("fabric_id", "Required");
        check = false;
    }
    if ($("#design_id").val() == null) {
        notifier("design_id", "Required");
        check = false;
    }
    if ($("#color_id").val() == null) {
        notifier("color_id", "Required");
        check = false;
    }
    if ($("#width_id").val() == null) {
        notifier("width_id", "Required");
        check = false;
    }
    if ($("#design_mtr").val() == "" || $("#design_mtr").val() == 0) {
        notifier("design_mtr", "Required");
        check = false;
        } else {
        if ($("#design_mtr").val() < 0) {
            notifier("design_mtr", "Invalid mtr");
            check = false;
        }
    }
    if (!check) {
        toastr.error("You forgot to enter some information.", "Oh snap!!!", {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
        });
        return;
    }
    let sdt_id = $("#sdt_id").val();
    
    const path = `${link}/${sub_link}/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=add_design_transaction`;
    ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
        if (handle_response(resp)) {
        const { data, msg } = resp;
            if (data && data.length != 0) { 
                if (sdt_id == 0) {
                    design_data.unshift(data);
                    add_wrapper_design(data);
                    lazy_loading('form_loading');
                    toastr.success(`${$("#design_id :selected").text()}`,"DESIGN ADDED TO LIST.",{ closeButton: true, progressBar: true });
                }else{
                    let index = design_data.findIndex((value) => value.sdt_id == sdt_id);
                    if (index < 0) toastr.success(`Design transaction not found`, "", {closeButton: true, progressBar: true});
                    design_data[index].sdt_fabric_id    = data["sdt_fabric_id"];
                    design_data[index].fabric_name      = data["fabric_name"];
                    design_data[index].sdt_design_id    = data["sdt_design_id"];
                    design_data[index].design_name      = data["design_name"];
                    design_data[index].design_image     = data["design_image"];
                    design_data[index].sdt_color_id    = data["sdt_color_id"];
                    design_data[index].color_name      = data["color_name"];
                    design_data[index].sdt_width_id    = data["sdt_width_id"];
                    design_data[index].width_name      = data["width_name"];
                    design_data[index].sdt_mtr          = data["sdt_mtr"];
                
                    $(`#fabric_name_${sdt_id}`).html(data["fabric_name"]);
                    $(`#design_name_${sdt_id}`).html(data["design_name"]);
                    $(`#color_name_${sdt_id}`).html(data["color_name"]);
                    $(`#width_name_${sdt_id}`).html(data["width_name"]);
                    $(`#design_mtr_${sdt_id}`).html(data["sdt_mtr"]);
                
                    toastr.success(`${$("#design_id :selected").text()}`, "FABRIC CODE UPDATED TO LIST.", { closeButton: true, progressBar: true });
                }

                $("#sdt_id").val(0);
                $("#fabric_id").val(null).trigger("change");
                $("#fabric_id").select2("open");
                $("#design_id").val(null).trigger("change");
                $("#color_id").val(null).trigger("change");
                $("#width_id").val(null).trigger("change");
                $("#design_mtr").val(0);
                $("#design_count").html(design_data.length);
                calculate_master();
            }
        }
    },
    (errmsg) => {}
    );
};
const add_wrapper_design = (data, append = false) => {
    
    let div = `<div class="col-12 col-sm-12 col-md-6 col-lg-3" id="rowdesign_${data['sdt_id']}">
                <span class="d-flex justify-content-center my-4" id="design_preview_${data['sdt_id']}" style="width: 10rem; height:10rem;">
                    <img 
                        class="img-thumbnail pan form_loading" 
                        onclick="zoom(this)" 
                        title="click to zoom in and zoom out" 
                        src="${LAZYLOADING}" 
                        data-src="${data['design_image']}" 
                        data-big="${data['design_image']}" 
                        style="max-width: 100%; max-height: 100%; object-fit: contain;"
                    />
                </span>
                <table class="table table-sm text-uppercase" style="font-size: 0.8rem;">
                    <tbody>
                        <tr>
                            <th width="30%">fabric</th>
                            <td width="70%">: <span id="fabricn_name_${data['sdt_id']}">${data['fabric_name']}</span></td>
                        </tr>
                        <tr>
                            <th width="30%">fabric code</th>
                            <td width="70%">: <span id="design_name_${data['sdt_id']}">${data['design_name']}</span></td>
                        </tr>
                        <tr>
                            <th width="30%">color</th>
                            <td width="70%">: <span id="color_name_${data['sdt_id']}">${data['color_name']}</span></td>
                        </tr>
                         <tr>
                            <th width="30%">width</th>
                            <td width="70%">: <span id="width_name_${data['sdt_id']}">${data['width_name']}</span></td>
                        </tr>
                        <tr>
                            <th width="30%">mtr</th>
                            <td width="70%">: <span id="design_mtr_${data['sdt_id']}">${data['sdt_mtr']}</span></td>
                        </tr>
                       
                        <tr>
                            <td width="30%">
                                <a 
                                    type="button" 
                                    class="btn btn-md" 
                                    onclick="edit_design_transaction(${data['sdt_id']})"
                                ><i class="text-success fa fa-edit"></i></a>
                            </td>
                            <td width="70%">
                                <a 
                                    type="button" 
                                    class="btn btn-md" 
                                    onclick="remove_design_transaction(${data['sdt_id']})"
                                ><i class="text-danger fa fa-trash"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>`;
    if (append) {
      $("#wrapper_design").append(div);
    } else {
      $("#wrapper_design").prepend(div);
    }
};
const edit_design_transaction = (sdt_id) => {
    const find = design_data.find((value) => value["sdt_id"] == sdt_id);
    $("#sdt_id").val(find['sdt_id']);
   
    find['sdt_fabric_id'] > 0 && $("#fabric_id").html(`<option value="${find['sdt_fabric_id']}">${find['fabric_name']}</option>`);
    find['sdt_color_id'] > 0 && $("#color_id").html(`<option value="${find['sdt_color_id']}">${find['color_name']}</option>`);
    find['sdt_width_id'] > 0 && $("#width_id").html(`<option value="${find['sdt_width_id']}">${find['width_name']}</option>`);

    find['sdt_design_id'] > 0 && $("#design_id").html(`<option value="${find['sdt_design_id']}">${find['design_name']}</option>`);
    $("#design_mtr").val(find['sdt_mtr']);
    if(find['design_image'] != ''){
      $(`#design_image`).html(
        `<img 
            class="img-thumbnail pan form_loading" 
            width="150px" 
            onClick="zoom()" 
            title="click to zoom in and zoom out" 
            src="${LAZYLOADING}" 
            data-src="${find['design_image']}" 
            data-big="${find['design_image']}" 
        />`
      );
    }
    lazy_loading('form_loading');
};
const remove_design_transaction = (sdt_id) => {
    design_data = design_data.filter((value) => value.sdt_id != sdt_id);
    let design_name = $(`#design_name_${sdt_id}`).html();
    toastr.success(``, `${design_name} REMOVED FROM LIST.`, {
      closeButton: true,
      progressBar: true,
    });
    $(`#rowdesign_${sdt_id}`).detach();
    $("#design_count").html(design_data.length);
    calculate_master();
};