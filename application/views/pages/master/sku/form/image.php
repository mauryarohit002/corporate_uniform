<?php $design_action= get_action_data('master', 'design');?>
<div class="d-flex flex-wrap">
    <div class="col-12 col-sm-12 col-md-4 col-lg-2 d-flex flex-wrap border p-3">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 floating-label mt-4">
            <input 
                type="file" 
                name="sku_images[]" 
                id="sku_images" 
                class="form-control floating-input" 
                onchange="upload_sku_image(this, ['image/gif', 'image/jpeg', 'image/png'])" 
                multiple="multiple" 
                tabindex="<?php echo $tabindex++; ?>"
                accept="image/*"
            >
            <label class="text-uppercase"><small class="text-danger font-weight-bold">(.png, .jpg, .jpeg, .gif) only</small></label>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-8 col-lg-10 border d-flex" id="wrapper_image" style="overflow-y: auto;"></div>
</div>