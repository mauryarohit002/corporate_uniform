const get_style_priority = (data) => {
    if (data && data.length <= 0) {
      return {
        style_priority_li: "",
        style_priority_tab: `<div class="row"><p class="text-center text-uppercase text-danger w-100 mt-5 font-weight-bold">style priority not added !!!</p></div>`,
      };
    }

// _${data[0]['apparel_id']}

    const style_priority_button = get_style_priority_button(data);
    const style_priority_div = get_syle_priority_div(data); 
    const style_priority_li = `<li class="nav-item">
                                <a class="nav-link text-uppercase d-flex flex-wrap justify-content-between" 
                                  id="style_with_image_tab"
                                  data-toggle="tab"
                                  href="#style_with_image_content" 
                                  role="tab" 
                                  aria-controls="style_with_image_content" 
                                  aria-selected="false"
                                ><span>style (image)</a></li>`;
    const style_priority_tab = `<div class="row">
                                  <div class="col-12 col-sm-12 col-md-2 col-lg-2">
                                    ${style_priority_button}
                                  </div>
                                  <div class="col-12 col-sm-12 col-md-10 col-lg-10">
                                    ${style_priority_div}
                                  </div>
                                </div>`;
    return {style_priority_li,style_priority_tab};
};
const get_style_priority_button = (data) => {
  let div = `<div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">`;
  data.forEach((value, index) => {
    const { spt_id, asm_name } = value;
    div += `<button 
              class="nav-link ${index == 0 ? "active" : ""}" 
              id="v-pills-${spt_id}-tab" 
              data-toggle="pill" 
              data-target="#v-pills-${spt_id}" 
              type="button" 
              role="tab" 
              aria-controls="v-pills-${spt_id}" 
              aria-selected="true"
            >
              ${asm_name} 
              <span class="badge badge-light" id="priority_badge_${spt_id}"></span>
            </button>`;
  });
  div += `</div>`;
  return div;
};
const get_syle_priority_div = (data) => {
  let div = `<div class="tab-content" id="v-pills-tabContent">`;
  data.forEach((value, index) => {
    const { spt_id, apparel_style_data } = value;
    const apparel_style_div = get_apparel_style(spt_id, apparel_style_data);
    div += `<div 
              class="tab-pane fade ${index == 0 ? "show active" : ""}" 
              id="v-pills-${spt_id}" 
              role="tabpanel" 
              aria-labelledby="v-pills-${spt_id}-tab"
            >${apparel_style_div}</div>`;
  });
  div += `</div>`;
  return div;
};

const get_apparel_style = (spt_id, data) => {
  $(".ast_name").removeClass("text-success");
  if (data && data.length <= 0) return `<div class="row"><p class="text-center text-uppercase text-danger w-100 mt-5 font-weight-bold">apparel style not added !!!</p></div>`;
    
  let div = `<ul class="image_radio_ul">`;
  data.forEach((value, index) => {
    const { ast_id, ast_name, ast_default, ast_image, csit_ot_id, csit_apparel_id } = value;
    const unique_id = `${csit_ot_id}_${csit_apparel_id}_${ast_id}`;
    const unique_id1 = `${csit_ot_id}_${csit_apparel_id}_${spt_id}`;
    div += `<li class="d-inline-block">
              <div class="d-flex flex-column align-items-center justify-content-between">
                <input 
                  type="radio" 
                  class="d-none" 
                  id="csit_id_${unique_id}" 
                  name="csit_id[${unique_id1}]" 
                  value="${ast_id}" 
                  onclick="set_apparel_style_value(${ast_id}, ${spt_id})"
                  ${ast_default == 1 ? "checked" : ""}
                />
                <label for="csit_id_${unique_id}" class="image_radio_label">
                  <img 
                      class="form_loading" 
                      src="${LAZYLOADING}" 
                      data-src="${ast_image}" 
                      data-big="${ast_image}" 
                      onerror="this.onerror=null; this.src='${NOIMAGE}';"
                      style="max-width: 100%; max-height: 100%; aspect-ration: 3/2; object-fit: contain;"
                  />
                  <b 
                    class="text-uppercase text-center text-wrap p-2 ast_name 
                    ${ast_default == 1 ? "text-success" : ""}" 
                    id="ast_name_${ast_id}" 
                    style="width: 10rem; height: 4rem; font-size: 0.8rem;"
                  >${ast_name}</b>
                </label>
              </div>
            </li>`;
  });
  div += `</ul>`;
  return div;
};
const set_apparel_style_value = (ast_id, spt_id) => {
  $(".ast_name").removeClass("text-success");
  $(`#ast_name_${ast_id}`).addClass("text-success");
  // $(`#priority_badge_${spt_id}`).html(1);
};