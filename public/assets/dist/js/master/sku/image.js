let image_data = [];
const upload_sku_image = (element, validTypes) => {
    if (!validate_multiple_document(element, validTypes)) return false;
    let path        = `${link}/${sub_link}/upload_sku_image/`;
    let form_id     = document.getElementById("_form");
    let form_data   = new FormData(form_id);
    fileUpAjaxCall(
        "POST",
        path,
        form_data,
        "JSON",
        (resp) => {
            if (handle_response(resp)) {
                const { data, msg } = resp;
                if(data && data.length != 0){
                    data.forEach((value) => {
                        image_data.unshift(value);
                        add_wrapper_image(value)
                    });
                }
                lazy_loading("form_loading");
                $("#image_count").html(image_data.length);
                $("#sku_images").val("");
                toastr.success("Image attached to form.", "", {
                    closeButton: true,
                    progressBar: true,
                    preventDuplicates: true,
                });
            }
        },
        (errmsg) => {
            $("#sku_images").val("");
        }
    );
};
const add_wrapper_image = (data) => {
    let div = `<div class="d-flex flex-column p-2" id="rowimage_${data['sit_id']}">
                    <span class="d-flex justify-content-center" style="width: 9rem; height:15rem;">
                        ${
                            data['sit_path'].includes(".pdf")
                                ? `<object 
                                        class="img-thumbnail pan form_loading" 
                                        type="application/pdf" 
                                        data="${data['sit_path']}"
                                        style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                    ></object>`
                                : `<img 
                                        class="img-thumbnail pan form_loading" 
                                        onclick="zoom(this)" 
                                        title="click to zoom in and zoom out" 
                                        src="${LAZYLOADING}" 
                                        data-big="${data['sit_path']}" 
                                        data-src="${data['sit_path']}" 
                                        style="max-width: 100%; max-height: 100%; object-fit: contain;"
                                    />`
                        }
                    </span>
                    <button 
                        type="button" 
                        class="btn btn-sm btn-primary mt-2" 
                        onclick="remove_image_transaction(${data['sit_id']})"
                    >REMOVE <i class="text-danger fa fa-trash"></i></button>
                    <a 
                        type="button" 
                        class="btn btn-sm btn-primary mt-2"
                        href="${data['sit_path']}"
                        download
                    >DOWNLOAD <i class="text-info fa fa-download"></i></a>
                </div>`;
    $("#wrapper_image").append(div);
};
const remove_image_transaction = (sit_id) => {
    image_data = image_data.filter((value) => value.sit_id != sit_id);
    toastr.success(``, `IMAGE REMOVED FROM LIST.`, {
      closeButton: true,
      progressBar: true,
    });
    $(`#rowimage_${sit_id}`).detach();
    $("#image_count").html(image_data.length);
};