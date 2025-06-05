let other_data = [];
const add_other_transaction = () => {
    notifier('other_id');
    notifier('other_rate');
    let check = true;
    if ($("#other_id").val() == null) {
        notifier("other_id", "Required");
        check = false;
    }
    if ($("#other_rate").val() == "" || $("#other_rate").val() == 0) {
        notifier("other_rate", "Required");
        check = false;
    } else {
        if ($("#other_rate").val() < 0) {
        notifier("other_rate", "Invalid rate");
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
    let sot_id = $("#sot_id").val();
    if(sot_id == 0){
        let index = other_data.findIndex((value) => value['sot_other_id'] == $('#other_id').val());
        if (index > -1) {
            notifier('other_id', 'Already added');
            return;
        }
    }

    const path = `${link}/${sub_link}/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=add_other_transaction`;
    ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
        if (handle_response(resp)) {
        const { data, msg } = resp;
            if (data && data.length != 0) { 
                if (sot_id == 0) {
                    other_data.unshift(data);
                    add_wrapper_other(data);
                    toastr.success(`${$("#other_id :selected").text()}`,"OTHER CHARGES ADDED TO LIST.",
                        { closeButton: true, progressBar: true }
                    );
                }else{
                    let index = other_data.findIndex((value) => value.sot_id == sot_id);
                    if (index < 0) {
                        toastr.success(`Other transaction not found`, "", {
                        closeButton: true,
                        progressBar: true,
                        });
                    }
                    other_data[index].sot_other_id    = data["sot_other_id"];
                    other_data[index].other_name      = data["other_name"];
                    other_data[index].sot_rate         = data["sot_rate"];
                    
                    $(`#other_name_${sot_id}`).html(data["other_name"]);
                    $(`#other_rate_${sot_id}`).html(data["sot_rate"]);
                    
                    toastr.success(
                        `${$("#other_id :selected").text()}`, "OTHER CHARGES UPDATED TO LIST.", { 
                        closeButton: true, 
                        progressBar: true 
                    });
                }

                $("#sot_id").val(0);
                $("#other_id").val(null).trigger("change");
                $("#other_id").select2("open");
                $("#other_rate").val(0);
                $("#other_count").html(other_data.length);
                calculate_master();
            }
        }
    },
    (errmsg) => {}
    );
};
const add_wrapper_other = (data, append = false) => {
    let tr = `<tr id="rowother_${data['sot_id']}">
                <td id="other_name_${data['sot_id']}">${data['other_name']}</td>
                <td id="other_rate_${data['sot_id']}">${data['sot_rate']}</td>
                <td>
                    <a 
                        type="button" 
                        class="btn btn-md" 
                        onclick="edit_other_transaction(${data['sot_id']})"
                    ><i class="text-success fa fa-edit"></i></a>
                </td>
                <td>
                    <a 
                        type="button" 
                        class="btn btn-md" 
                        onclick="remove_other_transaction(${data['sot_id']})"
                    ><i class="text-danger fa fa-trash"></i></a>
                </td>
            </tr>`;
    if (append) {
      $("#wrapper_other").append(tr);
    } else {
      $("#wrapper_other").prepend(tr);
    }
};
const edit_other_transaction = (sot_id) => {
    const find = other_data.find((value) => value["sot_id"] == sot_id);
    $("#sot_id").val(find['sot_id']);
    find['sot_other_id'] > 0 && $("#other_id").html(`<option value="${find['sot_other_id']}">${find['other_name']}</option>`);
    $("#other_rate").val(find['sot_rate']);
};
const remove_other_transaction = (sot_id) => {
    other_data = other_data.filter((value) => value.sot_id != sot_id);
    let other_name = $(`#other_name_${sot_id}`).html();
    toastr.success(``, `${other_name} REMOVED FROM LIST.`, {
      closeButton: true,
      progressBar: true,
    });
    $(`#rowother_${sot_id}`).detach();
    $("#other_count").html(other_data.length);
    calculate_master();
};