<script type="text/javascript">
    setTimeout(() => {
        $("#_entry_no").select2(
            select2_default({
                url: `<?php echo $menu; ?>/<?php echo $sub_menu; ?>/get_select2/_entry_no`,
                placeholder: "entry no",
            })
        ).on("change", () => trigger_search());
        $("#_customer_name").select2(
            select2_default({
                url: `<?php echo $menu; ?>/<?php echo $sub_menu; ?>/get_select2/_customer_name`,
                placeholder: "company",
            })
        ).on("change", () => trigger_search());
    }, RELOAD_TIME);
</script>
<script src="<?php echo assets('dist/js/transaction/order/order.js?v=27')?>"></script>