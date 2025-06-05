let body_structure_arr = [];
const get_body_measurement_data = () => {
  const customer_id = $('#om_customer_id').val()
  const customer_name = $('#om_customer_id :selected').text()
  if(!customer_id){
    toastr.error("Select customer first.", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });    
    return false;
  }
  const path = `transaction/order/handler/`;
  const form_data = {func : "get_body_measurement_data", id : customer_id};
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data } = resp;
        let measurement_table = get_body_measurement_table(data);
        let title = `<div class="col-12 col-sm-12 col-md-12 col-lg-12">
                      <p class="text-uppercase text-center font-weight-bold">body measurement of ${customer_name} </p>
                    </div>`;
        let body = `             
                      <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-form">
                            <div class="d-flex flex-wrap">${measurement_table}</div>
                        </div>             
                      </div>              
                    `;
      
          let footer = `<button 
                              type="button" 
                              id="sbt_btn" 
                              class="btn btn-sm btn-secondary btn-block text-uppercase mx-3" 
                              onclick="update_body_measurement(${customer_id})"
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

      }
    },
    (errmsg) => {}
  );
};

const get_body_measurement_table = (data) => {
    let div = `<p class="text-center text-uppercase text-danger w-100">no measurement added !!!</p>`;
    if (data && data.length != 0) {
      console.log(data);
        div = "";
        div += `<table class="table">
                  <tr>`;
        data.forEach((app_row) => {
                div += `<td>
                            <table>
                                  <tr><td colspan="2" align="center"><b>${app_row[0]['apparel_name']}</b></td></tr>`;
                app_row.forEach((row) => { 
                    const { cmt_id,measurement_id, measurement_name,measurement_priority,value1, value2,apparel_id, apparel_name,remark} = row;
                    const unique_id = `${cmt_id}_${apparel_id}_${measurement_id}`;
                    div += `<tr>
                              <td>(${measurement_priority}) ${measurement_name}
                                    <input type="hidden"
                                        id="cmt_id_${unique_id}"
                                        name="cmt_id[${unique_id}]"
                                        value="${cmt_id}"/>
                                  <input type="hidden"
                                        id="cmt_apparel_id_${unique_id}"
                                        name="cmt_apparel_id[${unique_id}]"
                                        value="${apparel_id}"/> 
                                   <input type="hidden"
                                        id="cmt_measurement_id_${unique_id}"
                                        name="cmt_measurement_id[${unique_id}]"
                                        value="${measurement_id}"/> 


                              </td>
                              <td>  
                              <input 
                                  type="text" 
                                  class="form-control floating-input ${apparel_id == 2 ? `` : `measurement_id_${measurement_id}`}" 
                                  id="cmt_value1_${unique_id}"
                                  name="cmt_value1[${unique_id}]"
                                  value="${value1}" 
                                  ${apparel_id == 2 ? `onkeyup="get_same_field_data_fetched('${unique_id}');"` : ''}
                                />
                              </td>
                        </tr>`;
                           
                });

                div +=`<tr>
                    <td> Remark</td>
                    <td>  
                        <textarea
                        class="form-control floating-textarea"
                        id="cmt_remark_${app_row[0]['apparel_id']}"
                        name="cmt_remark[${app_row[0]['apparel_id']}]"
                        >${app_row[0]["remark"]}</textarea>
                      </td>
                    </tr>
                `

                div += `</table></td>`;
        });        

        div += `</tr></table>`;
    }
    return div;
};

const get_same_field_data_fetched = (unique_id) => {
    var myarr = unique_id.split("_");
    var measurement_id = myarr[2];
    let value = $(`#cmt_value1_${unique_id}`).val();
    // console.log({myarr, value});
    $(`.measurement_id_${measurement_id}`).val(value);

}

const update_body_measurement = (id) => {
  event.preventDefault();
  let check = true;
  if (!check) {
    toastr.error("You forgot to enter some information.", "Oh snap!!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
  } else {
    const path = `transaction/order/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=update_body_measurement&id=${id}`;
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          const { data, msg } = resp;
          $("#popup_modal_sm").modal("hide");
          toastr.success("", msg, {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
          });
        }
      },
      (errmsg) => {}
    );
  }
};

