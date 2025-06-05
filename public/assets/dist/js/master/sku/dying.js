let dying_data = [];
const add_dying_transaction = () => {
    notifier('dying_id');
    notifier('dying_rate');
    let check = true;
    if ($("#dying_id").val() == null) {
        notifier("dying_id", "Required");
        check = false;
    }
    if ($("#dying_rate").val() == "" || $("#dying_rate").val() == 0) {
        notifier("dying_rate", "Required");
        check = false;
    } else {
        if ($("#dying_rate").val() < 0) {
        notifier("dying_rate", "Invalid rate");
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
    let sdyt_id = $("#sdyt_id").val();
    if(sdyt_id == 0){
        let index = dying_data.findIndex((value) => value['sdyt_dying_id'] == $('#dying_id').val());
        if (index > -1) {
            notifier('dying_id', 'Already added');
            return;
        }
    }

    const path = `${link}/${sub_link}/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=add_dying_transaction`;
    ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
        if (handle_response(resp)) {
        const { data, msg } = resp;
            if (data && data.length != 0) { 
                if (sdyt_id == 0) {
                    dying_data.unshift(data);
                    add_wrapper_dying(data);
                    toastr.success(`${$("#dying_id :selected").text()}`,"DYING ADDED TO LIST.",
                        { closeButton: true, progressBar: true }
                    );
                }else{
                    let index = dying_data.findIndex((value) => value.sdyt_id == sdyt_id);
                    if (index < 0) {
                        toastr.success(`Dying transaction not found`, "", {
                        closeButton: true,
                        progressBar: true,
                        });
                    }
                    dying_data[index].sdyt_dying_id    = data["sdyt_dying_id"];
                    dying_data[index].dying_name      = data["dying_name"];
                    dying_data[index].sdyt_rate         = data["sdyt_rate"];
                    
                    $(`#dying_name_${sdyt_id}`).html(data["dying_name"]);
                    $(`#dying_rate_${sdyt_id}`).html(data["sdyt_rate"]);
                    
                    toastr.success(
                        `${$("#dying_id :selected").text()}`, "DYING UPDATED TO LIST.", { 
                        closeButton: true, 
                        progressBar: true 
                    });
                }

                $("#sdyt_id").val(0);
                $("#dying_id").val(null).trigger("change");
                $("#dying_id").select2("open");
                $("#dying_rate").val(0);
                $("#dying_count").html(dying_data.length);
                calculate_master();
            }
        }
    },
    (errmsg) => {}
    );
};
const add_wrapper_dying = (data, append = false) => {
    let tr = `<tr id="rowdying_${data['sdyt_id']}">
                <td id="dying_name_${data['sdyt_id']}">${data['dying_name']}</td>
                <td id="dying_rate_${data['sdyt_id']}">${data['sdyt_rate']}</td>
                <td>
                    <a 
                        type="button" 
                        class="btn btn-md" 
                        onclick="edit_dying_transaction(${data['sdyt_id']})"
                    ><i class="text-success fa fa-edit"></i></a>
                </td>
                <td>
                    <a 
                        type="button" 
                        class="btn btn-md" 
                        onclick="remove_dying_transaction(${data['sdyt_id']})"
                    ><i class="text-danger fa fa-trash"></i></a>
                </td>
            </tr>`;
    if (append) {
      $("#wrapper_dying").append(tr);
    } else {
      $("#wrapper_dying").prepend(tr);
    }
};
const edit_dying_transaction = (sdyt_id) => {
    const find = dying_data.find((value) => value["sdyt_id"] == sdyt_id);
    $("#sdyt_id").val(find['sdyt_id']);
    find['sdyt_dying_id'] > 0 && $("#dying_id").html(`<option value="${find['sdyt_dying_id']}">${find['dying_name']}</option>`);
    $("#dying_rate").val(find['sdyt_rate']);
};
const remove_dying_transaction = (sdyt_id) => {
    dying_data = dying_data.filter((value) => value.sdyt_id != sdyt_id);
    let dying_name = $(`#dying_name_${sdyt_id}`).html();
    toastr.success(``, `${dying_name} REMOVED FROM LIST.`, {
      closeButton: true,
      progressBar: true,
    });
    $(`#rowdying_${sdyt_id}`).detach();
    $("#dying_count").html(dying_data.length);
    calculate_master();
};