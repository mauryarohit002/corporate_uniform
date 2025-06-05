let karigar_data = [];
const add_karigar_transaction = () => {
    notifier('karigar_id');
    notifier('apparel_id');
    notifier('karigar_rate');
    let check = true;
    if ($("#karigar_id").val() == null) {
        notifier("karigar_id", "Required");
        check = false;
    }
    if ($("#apparel_id").val() == null) {
        notifier("apparel_id", "Required");
        check = false;
    }
    if ($("#karigar_rate").val() == "" || $("#karigar_rate").val() == 0) {
        notifier("karigar_rate", "Required");
        check = false;
    } else {
        if ($("#karigar_rate").val() < 0) {
        notifier("karigar_rate", "Invalid rate");
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
    let skt_id = $("#skt_id").val();
    if(skt_id == 0){
        let index = karigar_data.findIndex((value) => (value['skt_karigar_id'] == $('#karigar_id').val() && value['skt_apparel_id'] == $('#apparel_id').val()));
        if (index > -1) {
            notifier('karigar_id', 'Already added');
            notifier('apparel_id', 'Already added');
            return;
        }
    }

    const path = `${link}/${sub_link}/handler`;
    let form_data = $(`#_form`).serialize();
    form_data += `&func=add_karigar_transaction`;
    ajaxCall(
    "POST",
    path,
    form_data,
    "JSON",
    (resp) => {
        if (handle_response(resp)) {
        const { data, msg } = resp;
            if (data && data.length != 0) { 
                if (skt_id == 0) {
                    karigar_data.unshift(data);
                    add_wrapper_karigar(data);
                    toastr.success(`${$("#karigar_id :selected").text()}`,"KARIGAR CHARGES ADDED TO LIST.",
                        { closeButton: true, progressBar: true }
                    );
                }else{
                    let index = karigar_data.findIndex((value) => value.skt_id == skt_id);
                    if (index < 0) {
                        toastr.success(`Karigar charges transaction not found`, "", {
                        closeButton: true,
                        progressBar: true,
                        });
                    }
                    karigar_data[index].skt_karigar_id    = data["skt_karigar_id"];
                    karigar_data[index].karigar_name      = data["karigar_name"];
                    karigar_data[index].skt_apparel_id    = data["skt_apparel_id"];
                    karigar_data[index].apparel_name      = data["apparel_name"];
                    karigar_data[index].skt_rate          = data["skt_rate"];
                    
                    $(`#karigar_name_${skt_id}`).html(data["karigar_name"]);
                    $(`#apparel_name_${skt_id}`).html(data["apparel_name"]);
                    $(`#karigar_rate_${skt_id}`).html(data["skt_rate"]);
                    
                    toastr.success(
                        `${$("#karigar_id :selected").text()}`, "KARIGAR CHARGES UPDATED TO LIST.", { 
                        closeButton: true, 
                        progressBar: true 
                    });
                }

                $("#skt_id").val(0);
                $("#karigar_id").val(null).trigger("change");
                $("#karigar_id").select2("open");
                $("#apparel_id").val(null).trigger("change");
                $("#apparel_id").select2("close");
                $("#karigar_rate").val(0);
                $("#karigar_count").html(karigar_data.length);
                calculate_master();
            }
        }
    },
    (errmsg) => {}
    );
};
const add_wrapper_karigar = (data, append = false) => {
    let tr = `<tr id="rowkarigar_${data['skt_id']}">
                <td id="karigar_name_${data['skt_id']}">${data['karigar_name']}</td>
                <td id="apparel_name_${data['skt_id']}">${data['apparel_name']}</td>
                <td id="karigar_rate_${data['skt_id']}">${data['skt_rate']}</td>
                <td>
                    <a 
                        type="button" 
                        class="btn btn-md" 
                        onclick="edit_karigar_transaction(${data['skt_id']})"
                    ><i class="text-success fa fa-edit"></i></a>
                </td>
                <td>
                    <a 
                        type="button" 
                        class="btn btn-md" 
                        onclick="remove_karigar_transaction(${data['skt_id']})"
                    ><i class="text-danger fa fa-trash"></i></a>
                </td>
            </tr>`;
    if (append) {
      $("#wrapper_karigar").append(tr);
    } else {
      $("#wrapper_karigar").prepend(tr);
    }
};
const edit_karigar_transaction = (skt_id) => {
    const find = karigar_data.find((value) => value["skt_id"] == skt_id);
    $("#skt_id").val(find['skt_id']);
    find['skt_karigar_id'] > 0 && $("#karigar_id").html(`<option value="${find['skt_karigar_id']}">${find['karigar_name']}</option>`);
    find['skt_apparel_id'] > 0 && $("#apparel_id").html(`<option value="${find['skt_apparel_id']}">${find['apparel_name']}</option>`);
    $("#karigar_rate").val(find['skt_rate']);
};
const remove_karigar_transaction = (skt_id) => {
    karigar_data = karigar_data.filter((value) => value.skt_id != skt_id);
    let karigar_name = $(`#karigar_name_${skt_id}`).html();
    toastr.success(``, `${karigar_name} REMOVED FROM LIST.`, {
      closeButton: true,
      progressBar: true,
    });
    $(`#rowkarigar_${skt_id}`).detach();
    $("#karigar_count").html(karigar_data.length);
    calculate_master();
};