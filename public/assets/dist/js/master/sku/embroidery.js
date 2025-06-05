let embroidery_data = [];
const add_embroidery_transaction = () => {
    notifier('embroidery_id');
    notifier('embroidery_rate');
    let check = true;
    if ($("#embroidery_id").val() == null) {
        notifier("embroidery_id", "Required");
        check = false;
    }
    if ($("#embroidery_rate").val() == "" || $("#embroidery_rate").val() == 0) {
        notifier("embroidery_rate", "Required");
        check = false;
    } else {
        if ($("#embroidery_rate").val() < 0) {
        notifier("embroidery_rate", "Invalid rate");
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
    let set_id = $("#set_id").val();
    if(set_id == 0){
        let index = embroidery_data.findIndex((value) => value['set_embroidery_id'] == $('#embroidery_id').val());
        if (index > -1) {
            notifier('embroidery_id', 'Already added');
            return;
        }
    }

    const path = `${link}/${sub_link}/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=add_embroidery_transaction`;
    ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
        if (handle_response(resp)) {
        const { data, msg } = resp;
            if (data && data.length != 0) { 
                if (set_id == 0) {
                    embroidery_data.unshift(data);
                    add_wrapper_embroidery(data);
                    toastr.success(`${$("#embroidery_id :selected").text()}`,"EMBROIDERY ADDED TO LIST.",
                        { closeButton: true, progressBar: true }
                    );
                }else{
                    let index = embroidery_data.findIndex((value) => value.set_id == set_id);
                    if (index < 0) {
                        toastr.success(`Embroidery transaction not found`, "", {
                        closeButton: true,
                        progressBar: true,
                        });
                    }
                    embroidery_data[index].set_embroidery_id    = data["set_embroidery_id"];
                    embroidery_data[index].embroidery_name      = data["embroidery_name"];
                    embroidery_data[index].set_rate         = data["set_rate"];
                    
                    $(`#embroidery_name_${set_id}`).html(data["embroidery_name"]);
                    $(`#embroidery_rate_${set_id}`).html(data["set_rate"]);
                    
                    toastr.success(
                        `${$("#embroidery_id :selected").text()}`, "EMBROIDERY UPDATED TO LIST.", { 
                        closeButton: true, 
                        progressBar: true 
                    });
                }

                $("#set_id").val(0);
                $("#embroidery_id").val(null).trigger("change");
                $("#embroidery_id").select2("open");
                $("#embroidery_rate").val(0);
                $("#embroidery_count").html(embroidery_data.length);
                calculate_master();
            }
        }
    },
    (errmsg) => {}
    );
};
const add_wrapper_embroidery = (data, append = false) => {
    let tr = `<tr id="rowembroidery_${data['set_id']}">
                <td id="embroidery_name_${data['set_id']}">${data['embroidery_name']}</td>
                <td id="embroidery_rate_${data['set_id']}">${data['set_rate']}</td>
                <td>
                    <a 
                        type="button" 
                        class="btn btn-md" 
                        onclick="edit_embroidery_transaction(${data['set_id']})"
                    ><i class="text-success fa fa-edit"></i></a>
                </td>
                <td>
                    <a 
                        type="button" 
                        class="btn btn-md" 
                        onclick="remove_embroidery_transaction(${data['set_id']})"
                    ><i class="text-danger fa fa-trash"></i></a>
                </td>
            </tr>`;
    if (append) {
      $("#wrapper_embroidery").append(tr);
    } else {
      $("#wrapper_embroidery").prepend(tr);
    }
};
const edit_embroidery_transaction = (set_id) => {
    const find = embroidery_data.find((value) => value["set_id"] == set_id);
    $("#set_id").val(find['set_id']);
    find['set_embroidery_id'] > 0 && $("#embroidery_id").html(`<option value="${find['set_embroidery_id']}">${find['embroidery_name']}</option>`);
    $("#embroidery_rate").val(find['set_rate']);
};
const remove_embroidery_transaction = (set_id) => {
    embroidery_data = embroidery_data.filter((value) => value.set_id != set_id);
    let embroidery_name = $(`#embroidery_name_${set_id}`).html();
    toastr.success(``, `${embroidery_name} REMOVED FROM LIST.`, {
      closeButton: true,
      progressBar: true,
    });
    $(`#rowembroidery_${set_id}`).detach();
    $("#embroidery_count").html(embroidery_data.length);
    calculate_master();
};