$(document).ready(function () {
  $("#_apparel_name")
    .select2(
      select2_default({
        url: `${link}/${sub_link}/get_select2/_apparel_name`,
        placeholder: "name",
      })
    )
    .on("change", () => trigger_search());
});
const record_remove = (data) => {
  const path = `${link}/${sub_link}/handler`;
  const form_data = { func: "remove", id: data.spm_id };
  let html = `<table class="table table-sm table-hover text-uppercase">
                  <tbody>
                    <tr>
                      <td class="font-weight-bold" width="30%" align="right">apparel : </td>
                      <td width="70%">${data.apparel_name}</td>
                    </tr>
                </tbody>
              </table>`;
  remove_datav3({ path, form_data, html });
};
