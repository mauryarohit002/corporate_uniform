$(document).ready(function () {
	  $(`#emp_designation_id`).select2(select2_default({
	    url: `master/designation/get_select2/_id`,
	    placeholder: "select",
	    param: true,
	  }));
});
 
  const add_emp_transaction = () => {  
    remove_emp_notifier();
    let check = true;
    // if ($("#emp_code").val() == '') {
    //   notifier("emp_code", "Required");
    //   check = false;
    //   return;
    // }
    if ($("#emp_name").val() == '') {
      notifier("emp_name", "Required");
      check = false;
    }
   //  if ($("#emp_mobile").val() == '') {
   //    notifier("emp_mobile", "Required");
   //    check = false;
   //  }	

  	// if ($("#emp_email").val() == '') {
   //    notifier("emp_email", "Required");
   //    check = false;
   //  }
   //  if ($("#emp_designation_id").val() == null) {
   //    notifier("emp_designation_id", "Required");
   //    check = false;
   //    $("body, html").animate({ scrollTop: 0 }, 1000);
   //  }

    if (!check) { 
      toastr.error("You forgot to enter some information.", "Oh snap!!!", {
        closeButton: true,
        progressBar: true,
        preventDuplicates: true,
      });
      return false;
    }

    let oet_id = $("#oet_id").val();
    const path = `${link}/${sub_link}/handler`;
    let form_id = document.getElementById("_employee_form");
    let form_data = new FormData(form_id);
    form_data.append("func", "add_emp_transaction");
    fileUpAjaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {   
          const { data, msg } = resp;
          if (data && data.length != 0) {  
            if (oet_id == 0) {  
                employee_trans_data.unshift(data);
                add_employee_wrapper_data(data);
                toastr.success(`${$("#emp_code :selected").text()}`, "ITEM ADDED TO LIST.", { closeButton: true, progressBar: true });
               	if (data["apparel_data"]?.length) { 
	                get_sku_measurement_data(data["oet_id"]);
	            } else {
	                if (data["ot_apparel_id"] > 0) get_measurement_data(data["oet_id"]);
	            }

            } else {
                let index = employee_trans_data.findIndex((value) => value.oet_id == oet_id);
                if (index < 0) {
                  toastr.success(`Transaction not found`, "", {
                    closeButton: true,
                    progressBar: true,
                  });
                } 
                // employee_trans_data[index].oet_id       		= data["oet_id"];
                employee_trans_data[index].oet_code       		= data["oet_code"];
                employee_trans_data[index].oet_name       		= data["oet_name"];
                employee_trans_data[index].oet_mobile     		= data["oet_mobile"];
                employee_trans_data[index].oet_email      		= data["oet_email"];
                employee_trans_data[index].oet_description      = data["oet_description"];
                employee_trans_data[index].oet_designation_id      	= data["oet_designation_id"];
                employee_trans_data[index].designation_name 	= data["designation_name"];
                
                $(`#emp_code_${oet_id}`).html(data["oet_code"]);
                $(`#emp_name_${oet_id}`).html(data["oet_name"]);
                $(`#emp_mobile_${oet_id}`).html(data["oet_mobile"]);
                $(`#emp_email_${oet_id}`).html(data["oet_email"]);
                $(`#emp_description_${oet_id}`).html(data["oet_description"]);
                $(`#designation_name_${oet_id}`).html(data["designation_name"]);

              toastr.success('', `${$("#emp_code :selected").text()} UPDATED TO LIST.`, { closeButton: true, progressBar: true });
              
            }

            $("#emp_code").val('');
            $("#emp_name").val('');
            $("#emp_mobile").val('');
            $("#emp_email").val('');
            $("#emp_description").val('');
            // $("#emp_designation_id").val(null).trigger("change");
            // $("#emp_designation_id").select2("close");
            $("#oet_id").val(0);
            $("#employee_transaction_count").html($('#employee_transaction_wrapper > tr').length);
          }
          
        }else{
          const { data } = resp;
          
        }
      },
      (errmsg) => {}
    );
  };
  const add_employee_wrapper_data = (data, append = false) => {  
      let tr = `<tr id="rowemp_${data['oet_id']}">
                  <td><span id="emp_code_${data['oet_id']}">${data['oet_code']}</span></td>
                  <td><span id="emp_name_${data['oet_id']}">${data['oet_name']}</span></td>
                  <td><span id="emp_mobile_${data['oet_id']}">${data['oet_mobile']}</span></td>
                  <td><span id="emp_email_${data['oet_id']}">${data['oet_email']}</span></td>
                  <td><span id="designation_name_${data['oet_id']}">${data['designation_name']}</span></td>
                  <td>
                        <div class="navigationn_wrapper">
                          <div class="navigationn">
                            <div class="menuToggle" id="menu_toggle_${data['oet_id']}" onclick="toggle_menuu(this)"></div>
                            <div class="menuu">
                              <ul> 
                                 ${data['apparel_data']?.length
		                            ?
		                            `<li>
		                                  <a 
		                                    type="button" 
		                                    class="btn btn-md" 
		                                    onclick="get_sku_measurement_data(${data['oet_id']})"
		                                    ><i class="text-info fa fa-eye"></i></a>
		                                </li>`
		                            :
		                            data['oet_apparel_id'] > 0
		                              ? `<li>
		                                    <a 
		                                      type="button" 
		                                      class="btn btn-md" 
		                                      onclick="get_measurement_data(${data['oet_id']})"
		                                      ><i class="text-primary fa fa-eye"></i></a>
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
                                          onclick="edit_emp_transaction(${data['oet_id']})"
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
                                          onclick="remove_emp_transaction(${data['oet_id']})"
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
        $("#employee_transaction_wrapper").append(tr);
      } else {
        $("#employee_transaction_wrapper").prepend(tr);
      }

      $(`#rowemp_${data['oet_id']}`).mouseout(() => $("#sku-image-preview").html(``));
    
  };
  const remove_emp_notifier = () => {
    notifier("emp_code");
    notifier("emp_name");
    notifier("emp_mobile");
    notifier("emp_email");
    notifier("emp_designation");
    notifier("emp_description");
  };
  const remove_emp_transaction = (oet_id) => {
    employee_trans_data = employee_trans_data.filter((value) => value.oet_id != oet_id);
    let emp_name = $(`#emp_name_${oet_id}`).html();
    toastr.success(``, `${emp_name} REMOVED FROM LIST.`, {closeButton: true,progressBar: true,});
    $(`#rowemp_${oet_id}`).detach();
    $("#employee_transaction_count").html($('#employee_transaction_wrapper > tr').length);
  };
  const edit_emp_transaction = (oet_id) => { 
    const find = employee_trans_data.find((value) => value["oet_id"] == oet_id);
    $("#oet_id").val(find['oet_id']);
    find['oet_designation_id'] > 0 && $("#emp_designation_id").html(`<option value="${find['oet_designation_id']}">${find['designation_name']}</option>`);
    $("#emp_code").val(find['oet_code']);
    $("#emp_name").val(find['oet_name']);
    $("#emp_mobile").val(find['oet_mobile']);
    $("#emp_email").val(find['oet_email']);
    $("#emp_description").val(find['oet_description']);
  };

  const get_employee_transaction = () => {   
    let id = $("#ot_id").val();
    let om_id = get_url_string("om_id");
    if (id && om_id >0 ) {
      const path = `${link}/${sub_link}/handler`;
      const form_data = { func: "get_employee_transaction", id };
      ajaxCall(
        "POST",
        path,
        form_data,
        "JSON",
        (resp) => {
          if (handle_response(resp)) {
            const { data, msg } = resp;
              if (data && data.length != 0) {
                employee_trans_data = data;
                employee_trans_data.forEach((value) => add_employee_wrapper_data(value, true));
              }
              $("#employee_transaction_count").html($('#sku_transaction_wrapper > tr').length);
          }
        },
        (errmsg) => {}
      );
    }
  
};


const get_measurement_data = (oet_id) => { 
  toggle_menuu({ id: oet_id });
  const find = employee_trans_data.find((value) => value.oet_id == oet_id);
  const ot_id = $("#ot_id").val();
  const ids = [];
  ids.push({
    oet_id: find['oet_id'],
    apparel_id: parseInt(find['oet_apparel_id']),
    apparel_name: find['apparel_name']
  });
  if (find['oet_apparel_id']) { 
    const path = `${link}/${sub_link}/handler`;
    const form_data = { 
      func: "get_sku_measurement_data",
      ot_id,
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
            let title = `<div class="px-2 mx-2" style="font-size: 0.8rem;">${get_sku_tab(data)}</div>`;
            let body = `<div class="d-flex flex-column font-weight-bold" style="font-size: 0.8rem;">${get_sku_tab_content(data)}</div>`;
            let footer = `<button 
                            type="button" 
                            id="sbt_btn" 
                            class="btn btn-sm btn-secondary btn-block text-uppercase mx-3" 
                            onclick="add_edit_measurement_and_style(${oet_id})"
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

const get_sku_measurement_data = (oet_id) => { 
  toggle_menuu({ id: oet_id });

  let temp = employee_trans_data.find(value => value['oet_id'] == oet_id);
  if (temp['apparel_data'].length <= 0) {
    toastr.error("Apparel not defined in sku.", "", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    return;
  }
  const ot_id = $("#ot_id").val();
  const ids = [];
  temp['apparel_data'].forEach(value => {
    ids.push({ oet_id: value['oet_id'], apparel_id: parseInt(value['oet_apparel_id']), apparel_name: value['apparel_name'] });
  });
  const path = `${link}/${sub_link}/handler`;
  const form_data = {
    func: "get_sku_measurement_data",
    ot_id,
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
          let title = `<div class="px-2 mx-2" style="font-size: 0.8rem;">${get_sku_tab(data)}</div>`;
          let body = `<div class="d-flex flex-column font-weight-bold" style="font-size: 0.8rem;">${get_sku_tab_content(data)}</div>`;
          let footer = `<button 
                          type="button" 
                          id="sbt_btn" 
                          class="btn btn-sm btn-secondary btn-block text-uppercase mx-3" 
                          onclick="add_edit_measurement_and_style(${oet_id})"
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
                  <td class="text-left border-0" width="50%"></td>
                  <td class="text-right border-0" width="50%"></td>
                </tr>
              </table>`;
    return html;
}

const get_sku_tab_content = data => {
  let html = `<div class="tab-content" id="pills-tabContent">`;
  data.forEach((value, index) => {
      const {apparel_data, measurement_data, style_data} = value;
      const measurement_bill_no   = measurement_data && measurement_data.length != 0 ? measurement_data[0]["bill_no"]: "";
      const measurement_bill_date = measurement_data && measurement_data.length != 0 ? measurement_data[0]["bill_date"] : "";
      const style_bill_no         = style_data && style_data.length != 0 ? style_data[0]["bill_no"] : "";
      const style_bill_date       = style_data && style_data.length != 0 ? style_data[0]["bill_date"] : "";
      let measurement_table       = get_measurement_table(measurement_data);
      let style_table             = get_style_table(style_data);
      // let { style_priority_li, style_priority_tab } = get_style_priority(style_priority_data);
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
                    
                  </ul>
                  </div>
                  <div class="tab-content" id="pills-tabContent-${apparel_data['apparel_id']}">
                  <div class="tab-pane fade show active" id="style_without_image_content_${apparel_data['apparel_id']}" role="tabpanel" aria-labelledby="style_without_image_tab_${apparel_data['apparel_id']}">
                      <div class="col-12 col-sm-12 col-md-12 col-lg-12" style="max-height: 60vh; overflow-y: auto; overflow-x: hidden;">
                      <div class="d-flex flex-wrap" id="style_wrapper_${apparel_data['apparel_id']}">${style_table}</div>
                      </div>
                  </div>
                  <div class="tab-pane fade" id="style_with_image_content_${apparel_data['apparel_id']}" role="tabpanel" aria-labelledby="style_with_image_tab_${apparel_data['apparel_id']}">
                     
                  </div>
                  </div>
              </div>
              </div>`;
  });
  html += `</div>`;
  return html;
}

// measurement
const toggle_measurement_popup = () => {
  if ($(`#measurement_wrapper .top-panel`).hasClass("active")) {
    $(`#measurement_wrapper, #measurement_wrapper .top-panel `).removeClass(
      "active"
    );
    $(`#measurement_wrapper .top-panel-title`).html("");
    $(`#measurement_wrapper .top-panel-subtitle`).html("");
    $(`#measurement_wrapper .top-panel-body`).html("");
    $(`#measurement_wrapper .top-panel-footer`).html("");
  } else {
    $(`#measurement_wrapper, #measurement_wrapper .top-panel`).addClass(
      "active"
    );
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
        cmt_oet_id,
        cmt_apparel_id
      } = row;  

      const unique_id = `${cmt_oet_id}_${cmt_apparel_id}_${cmt_measurement_id}`;

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
                      value="${cmt_value1}" />
                 
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

const get_style_table = (data) => {
  let div = `<p class="text-center text-uppercase text-danger w-100">no style added !!!</p>`;
  if (data && data.length != 0) {
    div = "";
    data.forEach((row) => {
      const { cst_id, cst_style_id, cst_style_name, cst_value,cst_oet_id, cst_apparel_id} = row;
      const unique_id = `${cst_oet_id}_${cst_apparel_id}_${cst_style_id}`;

      div += `<div class="col-12 col-sm-12 col-md-3 col-lg-2 mt-4" id="rowstyle_${cst_style_id}">
                <input
                  type="hidden"
                  id="cst_id_${unique_id}"
                  name="cst_id[${unique_id}]"
                  value="${cst_id}"
                />
                <input
                  type="hidden"
                  id="cst_style_id_${unique_id}"
                  name="cst_style_id[${unique_id}]"
                  value="${cst_style_id}"
                />
                <label class="custom-control material-checkbox">
                  <input 
                    type="checkbox" 
                    class="material-control-input advance_checkboxes" 
                    id="cst_value_${unique_id}" 
                    name="cst_value[${unique_id}]" 
                    value="${cst_value}" 
                    ${cst_value == 1 ? "checked" : ""}
                  />
                  <span class="material-control-indicator"></span>
                  <span class="material-control-description">${cst_style_name}</span>
                </label>
              </div>`;
    });
  }
  return div;
};


const add_edit_measurement_and_style = (oet_id) => { 
  event.preventDefault();
  const find = employee_trans_data.find((value) => value.oet_id == oet_id);
  const path = `${link}/${sub_link}/handler`;
  let form_data = $(`#_employee_form`).serialize();
  form_data += `&func=add_edit_measurement_and_style`;
  form_data += `&_oet_id=${oet_id}`;
  form_data += `&_apparel_id=${find.oet_apparel_id}`;
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
// measurement

const add_edit_employee = () => {
    if (window.opener && typeof window.opener.receiveEmployeeData === 'function') {
        window.opener.receiveEmployeeData(employee_trans_data);
    }
    window.close();
};