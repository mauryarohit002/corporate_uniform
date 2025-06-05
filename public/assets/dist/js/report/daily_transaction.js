$(document).ready(function () {
	
	$("#_payment_mode_name")
		.select2(
			select2_default({
				url: `report/daily_transaction/get_select2/_payment_mode_name`,
				placeholder: "payment mode",
			})
		)
		.on("change", () => trigger_search());
});


