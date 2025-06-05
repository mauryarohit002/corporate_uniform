$(document).ready(function () {
  $("#hm_id")
    .select2(
      select2_default({
        url: `voucher/payment_karigar/get_select2/_hm_id`,
        placeholder: "SELECT",
        param: true,
      })
    )
    .on("change", (event) => get_karigar_from_hisab(event.target.value));

  $("#payment_karigar_id")
    .select2(
      select2_default({
        url: `master/karigar/get_select2/_id`,
        placeholder: "SELECT",
        param: true,
      })
    )
    .on("change", () => get_karigar_data());
});

// core_functions
const get_transaction = () => {
  if (["edit", "read"].includes(get_url_string("action"))) {
    let id = get_url_string("id");
    if (id) {
      $("#btn_adjustment").addClass("d-none");
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
            const { hisab_data } = data;
            if (hisab_data && hisab_data.length != 0) {
              hisab_data.forEach((row) => add_hisab_wrapper(row));
            } else {
              $("#btn_adjustment").removeClass("d-none");
            }
            set_checkboxes();
          }
        },
        (errmsg) => { }
      );
    }
  }
};
const set_checkboxes = () => {
  let hisab_row = $("#hisab_wrapper tr").length;
  let hisab_checked = $(".hisab_checkboxes:checked").length;
  $("#hisab_count").html(hisab_row);
  $("#hisab_select_count").html(hisab_checked);
  $(`#hisab_checkbox`).prop("checked", hisab_row > 0 ? hisab_row == hisab_checked : false);
};
const set_default = () => {
  $("#payment_hisab_amt").val(0);
  $("#payment_balance_amt_show").val("");
  $("#payment_balance_amt").val(0);
  $("#payment_balance_type").val("");

  $("#hisab_wrapper").html("");
  $("#hisab_count").html(0);
  $("#hisab_select_count").html(0);
  $(`#hisab_checkbox`).prop("checked", false);
};
const get_karigar_from_hisab = (id) => {
  if (!id) return false;
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "get_karigar_from_hisab", id };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        if (data && data.length != 0) {
          $("#payment_karigar_id").html(`<option value="${data[0]["karigar_id"]}">${data[0]["karigar_name"]}</option>`);
          $(`#payment_karigar_id`).val(data[0]["karigar_id"]).trigger("change");
          $(`#payment_karigar_id`).select2("close");
        }
      }
    },
    (errmsg) => { }
  );
};
const get_karigar_data = () => {
  set_default();
  const id = $("#payment_karigar_id").val();
  if (!id) return false;
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "get_karigar_data", id };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        const { hisab_data, hisab_bal, balance_data } = data;
        $("#payment_hisab_amt").val(hisab_bal);
        $("#payment_balance_amt").val(balance_data["balance_amt"]);
        $("#payment_balance_type").val(balance_data["type"]);
        $("#payment_balance_amt_show").val(`${balance_data["balance_amt"]} ${balance_data["type"]}`);

        if (hisab_data && hisab_data.length != 0) hisab_data.forEach((row) => add_hisab_wrapper(row));
        set_checkboxes();
        calculate_master();
      }
    },
    (errmsg) => { }
  );
};
const get_data_for_adjustment = () => {
  const id = $("#payment_karigar_id").val();
  if (!id) return false;
  const path = `${link}/${sub_link}/handler`;
  let form_data = { func: "get_data_for_adjustment", id };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        const { hisab_data } = data;
        if (hisab_data && hisab_data.length != 0) hisab_data.forEach((row) => add_hisab_wrapper(row));
        set_checkboxes();
      }
    },
    (errmsg) => { }
  );
};
const calculate_master = () => {
  let payment_amt = $("#payment_amt").val();
  if (isNaN(payment_amt) || payment_amt == "") payment_amt = 0;

  let balance_amt = $("#payment_balance_amt").val();
  if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;

  let balance_type = $(`#payment_balance_type`).val();

  let closing_amt = parseFloat(balance_amt) + parseFloat(payment_amt);
  if (isNaN(closing_amt) || closing_amt == "") closing_amt = 0;

  if (closing_amt < 0) {
    closing_amt = Math.abs(closing_amt);
    if (balance_type == TO_RECEIVE) {
      balance_type = TO_PAY;
    } else {
      balance_type = TO_RECEIVE;
    }
  }
  $("#payment_balance_amt_show").val(`${closing_amt} ${balance_type}`);

  let hisab_amt = 0;
  let hisab_row = $("#hisab_wrapper tr").length;
  for (let i = 1; i <= hisab_row; i++) {
    let attr = $(`#hisab_wrapper tr:nth-child(${i})`).attr("id");
    let explode = attr.split("_");
    let cnt = explode[1];

    let balance_amt = $(`#pht_balance_amt_${cnt}`).val();
    if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;
    if ($(`#pht_checked_${cnt}`).is(":checked")) {
      let current_amt = parseFloat(payment_amt) - parseFloat(hisab_amt);
      if (isNaN(current_amt) || current_amt == "") current_amt = 0;

      let allocated_amt =
        current_amt > balance_amt
          ? parseFloat(balance_amt)
          : parseFloat(current_amt);
      if (isNaN(allocated_amt) || allocated_amt == "") allocated_amt = 0;
      $(`#pht_adjust_amt_${cnt}`).val(allocated_amt.toFixed(2));

      balance_amt = parseFloat(balance_amt) - parseFloat(allocated_amt);
      if (isNaN(allocated_amt) || allocated_amt == "") allocated_amt = 0;
      $(`#pht_balance_amt_show_${cnt}`).val(balance_amt.toFixed(2));

      hisab_amt = parseFloat(hisab_amt) + parseFloat(allocated_amt);
      if (isNaN(hisab_amt) || hisab_amt == "") hisab_amt = 0;
    } else {
      $(`#pht_adjust_amt_${cnt}`).val(0);
      $(`#pht_balance_amt_show_${cnt}`).val(balance_amt);
    }
  }
  set_checkboxes();
};
const remove_master_notifier = () => {
  notifier("payment_karigar_id");
  notifier("payment_amt");
};
const add_edit = (id) => {
  remove_master_notifier();
  let check = true;
  if ($("#payment_karigar_id").val() == null) {
    notifier("payment_karigar_id", "Required");
    check = false;
  }
  if ($("#payment_amt").val() <= 0) {
    notifier("payment_amt", "Required");
    check = false;
  } else {
    if ($("#payment_amt").val() < 0) {
      notifier("payment_amt", "Invalid payment amt");
      check = false;
    }
  }

  if (!check) {
    toastr.error("You forgot to enter some information", "Oh snap !!!", {
      closeButton: true,
      progressBar: true,
      preventDuplicates: true,
    });
    $("body, html").animate({ scrollTop: 0 }, 1000);
  } else {
    const path = `${link}/${sub_link}/handler`;
    let form_data = $("#_form").serialize();
    form_data += `&func=add_edit`;
    ajaxCall(
      "POST",
      path,
      form_data,
      "JSON",
      (resp) => {
        if (handle_response(resp)) {
          let { msg } = resp;
          if (id == 0) {
          } else {
          }
          remove_master_notifier();
          toastr.success("", msg, { closeButton: true, progressBar: true });
          $("body, html").animate({ scrollTop: 0 }, 1000);
          setTimeout(function () {
            window.location.reload();
          }, RELOAD_TIME);
        }
      },
      (errmsg) => { }
    );
  }
};
const remove_record = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.payment_id };
  let html = `<table class="table table-sm table-hover" style="font-size:0.8rem;">
                <tbody>
                    <tr>
                        <td class="font-weight-bold text-uppercase" width="30%" align="right">entry no : </td>
                        <td width="70%">${data.payment_entry_no}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-uppercase" width="30%" align="right">entry date : </td>
                        <td width="70%">${data.entry_date}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-uppercase" width="30%" align="right">karigar : </td>
                        <td width="70%">${data.karigar_name}</td>
                    </tr>
                    <tr>
                        <td class="font-weight-bold text-uppercase" width="30%" align="right">payment amt : </td>
                        <td class="font-weight-bold text-uppercase" width="70%">${data.payment_amt}</td>
                    </tr>
                </tbody>
            </table>`;
  remove_datav3({ path, form_data, html });
};
// core_functions

// hisab module
const add_hisab_wrapper = (data) => {
  let tr = `<tr id="rowhisab_${data['pht_hm_id']}">
				<td width="2%" >
					<input 
						type="hidden" 
						name="pht_id[${data['pht_hm_id']}]" 
						id="pht_id_${data['pht_hm_id']}" 
						value="${data['pht_id']}" 
					/>
					<label class="custom-control material-checkbox">
						<input 
							type="checkbox" 
							class="material-control-input hisab_checkboxes" 
							id="pht_checked_${data['pht_hm_id']}" 
							name="pht_checked[${data['pht_hm_id']}]" 
							value="${data['pht_hm_id']}"
							onclick="hisab_select_deselect(${data['pht_hm_id']})" 
							${data['pht_checked'] == 1 ? "checked" : ""}
						/>
						<span class="material-control-indicator"></span>
					</label>
				</td>
				<td width="5%">
					<input 
						type="hidden" 
						name="pht_hm_id[${data['pht_hm_id']}]" 
						id="pht_hm_id_${data['pht_hm_id']}" 
						value="${data['pht_hm_id']}" 
					/>
					<input 
						type="text" 
						class="border-0 text-center" 
						name="pht_entry_no[${data['pht_hm_id']}]" 
						id="pht_entry_no_${data['pht_hm_id']}" 
						value="${data['pht_entry_no']}" 
						readonly 
					/>
				</td>
				<td width="5%">
					<input 
						type="text" 
						class="border-0" 
						name="pht_entry_date[${data['pht_hm_id']}]" 
						id="pht_entry_date_${data['pht_hm_id']}" 
						value="${data['pht_entry_date']}" 
						readonly 
					/>
				</td>
				<td width="5%">
					<input 
						type="number" 
						class="border-0" 
						name="pht_total_amt[${data['pht_hm_id']}]" 
						id="pht_total_amt_${data['pht_hm_id']}" 
						value="${data['pht_total_amt']}" 
						readonly 
					/>
				</td>
				<td width="5%">
					<input 
						type="number" 
						class="border-0" 
						name="pht_adjust_amt[${data['pht_hm_id']}]" 
						id="pht_adjust_amt_${data['pht_hm_id']}" 
						value="${data['pht_adjust_amt']}" 
						readonly
					/>
				</td>
				<td width="5%">
					<input 
						type="number" 
						class="border-0" 
						id="pht_balance_amt_show_${data['pht_hm_id']}" 
						value="${data['balance_amt']}" 
						readonly 
					/>
					<input 
						type="hidden" 
						id="pht_balance_amt_${data['pht_hm_id']}" 
						value="${data['pht_id'] == 0 ? data['balance_amt'] : data['pht_total_amt']}" 
						readonly 
					/>
				</td>
			</tr>`;
  $("#hisab_wrapper").prepend(tr);
};
const hisab_select_deselect = (count = 0) => {
  let parent_checked = $(`#hisab_checkbox`).is(":checked");
  if (count == 0) $(`.hisab_checkboxes`).prop("checked", parent_checked);
  calculate_master();
};
// hisab module

// payment mode
const get_payment_mode_data = () => {
  let title = `<p>payment mode</p>`;
  let subtitle = `<div class="d-flex justify-content-around">
                    <p class="d-flex flex-column justify-content-around">
                        <span class="pb-1 border-bottom">payment amt</span>
                        <span class="_payment_amt">0</span>
                    </p>
                    </p>
                    <p class="d-flex flex-column justify-content-around">
                        <span class="pb-1 border-bottom">balance amt</span>
                        <span class="_balance_amt">0</span>
                    </p>
                </div>`;
  let body = ``;
  let footer = `<button 
					type="button" 
					id="sbt_btn" 
					class="btn btn-md btn-secondary btn-block text-uppercase mx-3" 
					onclick="toggle_payment_mode_popup()"
				>close</button>`;
  $(`#payment_mode_wrapper .right-panel-title`).html(title);
  $(`#payment_mode_wrapper .right-panel-subtitle`).html(subtitle);
  $(`#payment_mode_wrapper .right-panel-body`).html(body);
  $(`#payment_mode_wrapper .right-panel-footer`).html(footer);
  const id = $("#id").val();
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "get_payment_mode_data", id };
  ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
      if (handle_response(resp)) {
        const { data, msg } = resp;
        if (data && data.length != 0) {
          const { pay_modes } = get_pay_modes(data);
          let body = `<div class="row pt-2">
                        <div class="col-12">
                            <div style="max-height: 50vh; overflow-x: auto;">
                                <table class="table table-sm w-100">
                                    <tbody id="payment_mode_tbody">
                                        ${pay_modes}
                                    </tbody>
                                </table>
                            </div>              
                        </div>              
                    </div> `;
          $(`#payment_mode_wrapper .right-panel-body `).html(body);
        }
      }
    },
    (errmsg) => {
      console.log(errmsg);
    }
  );
};
const get_pay_modes = (data) => {
  let pay_modes = ``;
  let advance_amt = 0;
  data.forEach((row) => {
    const { pkpmt_id, pkpmt_amt, pkpmt_payment_mode_id, payment_mode_name } = row;
    advance_amt = parseFloat(advance_amt) + parseFloat(pkpmt_amt);
    if (isNaN(advance_amt) || advance_amt == "") advance_amt = 0;

    pay_modes += `<tr id="rowhm_${pkpmt_payment_mode_id}">
						<td width="10%" class="border-0 font-weight-bold"></td>
						<td width="30%" class="border-0 font-weight-bold">${payment_mode_name} : </td>
						<td width="50%" class="border-0 floating-label">
							<input 
								type="hidden"
								id="pkpmt_id_${pkpmt_payment_mode_id}" 
								name="pkpmt_id[${pkpmt_payment_mode_id}]" 
								value="${pkpmt_id}" 
							/>
							<input 
								type="hidden"
								id="pkpmt_payment_mode_id_${pkpmt_payment_mode_id}" 
								name="pkpmt_payment_mode_id[${pkpmt_payment_mode_id}]" 
								value="${pkpmt_payment_mode_id}" 
							/>
							<input 
								type="number" 
								class="form-control floating-input" 
								id="pkpmt_amt_${pkpmt_payment_mode_id}" 
								name="pkpmt_amt[${pkpmt_payment_mode_id}]" 
								value="${pkpmt_amt}"
								onkeyup="calculate_payment_amt()"
								placeholder=" " 
								autocomplete="off" 
							/>
						</td>
						<td width="10%" class="border-0 font-weight-bold"></td>
					</tr>`;
  });
  return { advance_amt, pay_modes };
};
const toggle_payment_mode_popup = () => {
  if ($(`#payment_mode_wrapper .right-panel`).hasClass("active")) {
    $(`#payment_mode_wrapper .right-panel `).removeClass("active");
  } else {
    $(`#payment_mode_wrapper .right-panel `).addClass("active");

    let payment_amt = $(`#payment_amt`).val();
    if (isNaN(payment_amt) || payment_amt == "") payment_amt = 0;
    $(`._payment_amt`).html(payment_amt);

    let credit_note_amt = $("#payment_credit_note_amt").val();
    if (isNaN(credit_note_amt) || credit_note_amt == "") credit_note_amt = 0;
    $(`._credit_note_amt`).html(credit_note_amt);

    let balance_amt = $("#payment_balance_amt").val();
    if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;

    let closing_amt =
      parseFloat(balance_amt) +
      parseFloat(credit_note_amt) -
      parseFloat(payment_amt);
    if (isNaN(closing_amt) || closing_amt == "") closing_amt = 0;

    $("._balance_amt").html(closing_amt);
  }
};
const calculate_payment_amt = () => {
  let payment_amt = 0;
  for (let i = 1; i <= $("#payment_mode_tbody > tr").length; i++) {
    let cnt = $(`#payment_mode_tbody > tr:nth-child(${i})`).attr("id");
    let explode = cnt.split("_");
    let id = explode[1];

    let amt = $(`#pkpmt_amt_${id}`).val();
    if (isNaN(amt) || amt == "") amt = 0;

    payment_amt = parseFloat(payment_amt) + parseFloat(amt);
    if (isNaN(payment_amt) || payment_amt == "") payment_amt = 0;
  }
  $("._payment_amt").html(payment_amt);
  $(`#payment_amt`).val(payment_amt);

  let credit_note_amt = $("#payment_credit_note_amt").val();
  if (isNaN(credit_note_amt) || credit_note_amt == "") credit_note_amt = 0;

  let balance_amt = $("#payment_balance_amt").val();
  if (isNaN(balance_amt) || balance_amt == "") balance_amt = 0;

  let closing_amt =
    parseFloat(balance_amt) +
    parseFloat(credit_note_amt) -
    parseFloat(payment_amt);
  if (isNaN(closing_amt) || closing_amt == "") closing_amt = 0;

  $("._balance_amt").html(closing_amt);
  if (closing_amt >= 0) {
    // $("#sbt_btn").prop("disabled", false);
    $("._balance_amt").removeClass("text-danger");
  } else {
    // $("#sbt_btn").prop("disabled", true);
    $("._balance_amt").addClass("text-danger");
  }
  calculate_master();
};
// payment mode
