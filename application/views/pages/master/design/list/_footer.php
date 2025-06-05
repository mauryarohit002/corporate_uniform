<script src="<?php echo assets('dist/js/'.$menu.'/'.$sub_menu.'.js?v=1')?>"></script>
<script type="text/javascript">
    setTimeout(() => {
        $("#_design_name").select2(
            select2_default({
                url: `<?php echo $menu; ?>/<?php echo $sub_menu; ?>/get_select2/_design_name`,
                placeholder: "design",
            })
        ).on("change", () => trigger_search());
        $("#_supplier_name").select2(
            select2_default({
                url: `<?php echo $menu; ?>/<?php echo $sub_menu; ?>/get_select2/_supplier_name`,
                placeholder: "supplier",
            })
        ).on("change", () => trigger_search());
    }, RELOAD_TIME)
</script>