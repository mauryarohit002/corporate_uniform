const toggle_measurement_popup = () => {
    if ($(`#measurement_wrapper .top-panel`).hasClass("active")) {
      $(`#measurement_wrapper, #measurement_wrapper .top-panel `).removeClass("active");
      $(`#measurement_wrapper .top-panel-title`).html("");
      $(`#measurement_wrapper .top-panel-subtitle`).html("");
      $(`#measurement_wrapper .top-panel-body`).html("");
      $(`#measurement_wrapper .top-panel-footer`).html("");
    } else {
      $(`#measurement_wrapper, #measurement_wrapper .top-panel`).addClass("active");
    }
};


const get_measurement_table = (data) => {
  let div = `<p class="text-center text-uppercase text-danger w-100">no measurement added !!!</p>`;
  if (data && data.length != 0) {
    div = "";
    data.forEach((row) => {
      const {
        cmt_id,
        cmt_measurement_id,
        cmt_measurement_name,
        cmt_value1,
        cmt_value2,
        cmt_ot_id,
        cmt_apparel_id
      } = row;  

      const unique_id = `${cmt_ot_id}_${cmt_apparel_id}_${cmt_measurement_id}`;

      div += `<div class="col-12 col-sm-12 col-md-2 col-lg-1 floating-label mt-4" id="rowmeasurement_${cmt_measurement_id}">
                <p class="text-uppercase">${cmt_measurement_name}</p> 
                <input
                  type="hidden"
                  id="cmt_id_${unique_id}"
                  name="cmt_id[${unique_id}]"
                  value="${cmt_id}"
                />
                <div class="d-flex flex-column" style="gap: 10px;">
                  <input 
                      type="text" 
                      class="form-control floating-input" 
                      id="cmt_value1_${unique_id}"
                      name="cmt_value1[${unique_id}]"
                      value="${cmt_value1}"
                      onfocusout="change_to_super('cmt_value1_${unique_id}')"
                  />
                 
                </div>
              </div>`;
    });
    div += `<div class="col-12 col-sm-12 col-md-4 col-lg-4 floating-label mt-4" id="rowmeasurement_0">
              <p class="text-uppercase">remark</p> 
              <textarea
                class="form-control floating-textarea"
                id="cmt_remark_${data[0]['cmt_apparel_id']}"
                name="cmt_remark[${data[0]['cmt_apparel_id']}]"
              >${data[0]["cmt_remark"]}</textarea>
            </div>`;
  }
  return div;
};

const add_edit_measurement_and_style = (ot_id) => {
    event.preventDefault();
    const find = trans_data.find((value) => value.ot_id == ot_id);
    const path = `${link}/${sub_link}/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=add_edit_measurement_and_style`;
    form_data += `&_ot_id=${ot_id}`;
     form_data += `&_apparel_id=${find.ot_apparel_id}`;
    ajaxCall(
        "POST",
        path,
        form_data,
        "JSON",
        (resp) => {
        const { data, msg } = resp;
        if (handle_response(resp)) {
            toggle_measurement_popup();
            toastr.success("", msg, {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
            });
        }
        },
        (errmsg) => {}
    );
};