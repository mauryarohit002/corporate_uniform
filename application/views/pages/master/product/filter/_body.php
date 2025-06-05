<div class="row">
    <div class="d-flex flex-wrap justify-content-center floating-form">
        <div class="col-12 col-sm-12 col-md-6 col-lg-6 floating-label mt-3">
            <?php if(isset($filters['_product_name'])): ?><p class="text-uppercase"><?php echo str_replace('_', ' ', $sub_menu); ?></p><?php endif; ?>
            <select class="form-control floating-select" id="_product_name" name="_product_name">
                <?php if(isset($filters['_product_name']) && !empty($filters['_product_name'])): ?>
                    <option value="<?php echo $filters['_product_name']['value']; ?>" selected>
                        <?php echo $filters['_product_name']['text']; ?> 
                    </option>
                <?php endif; ?>
            </select>
        </div>
       
    </div>
</div>