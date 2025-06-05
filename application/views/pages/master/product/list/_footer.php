<script src="<?php echo assets('dist/js/'.$menu.'/product.js?v=3')?>"></script>
<script src="<?php echo assets('dist/js/'.$menu.'/common.js?v=1')?>"></script>

<script type="text/javascript">
    setTimeout(() => {
        $("#_product_name").select2(
            select2_default({
                url: `<?php echo $menu ?>/<?php echo $sub_menu; ?>/get_select2/_product_name`,
                placeholder: "prodcut",
            })
        ).on("change", () => trigger_search());
        $("#_category_name").select2(
            select2_default({
                url: `<?php echo $menu ?>/<?php echo $sub_menu; ?>/get_select2/_category_name`,
                placeholder: "readymade category",
            })
        ).on("change", () => trigger_search());
    }, RELOAD_TIME)
</script>