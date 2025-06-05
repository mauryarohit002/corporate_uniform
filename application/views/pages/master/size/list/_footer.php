<script src="<?php echo assets('dist/js/'.$menu.'/'.$sub_menu.'.js?v=1')?>"></script>
<script type="text/javascript">
    setTimeout(() => {
        $("#_size_name").select2(
            select2_default({
                url: `<?php echo $menu; ?>/<?php echo $sub_menu; ?>/get_select2/_size_name`,
                placeholder: "size",
            })
        ).on("change", () => trigger_search());
        $("#_gender_name").select2(
            select2_default({
                url: `<?php echo $menu; ?>/<?php echo $sub_menu; ?>/get_select2/_gender_name`,
                placeholder: "gender",
            })
        ).on("change", () => trigger_search());
    }, RELOAD_TIME)
</script>