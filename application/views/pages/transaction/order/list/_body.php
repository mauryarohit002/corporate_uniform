<tbody id="table_tbody">
    <?php 
        if(!empty($data)): 
            foreach ($data as $key => $value):
                $value['id'] = encrypt_decrypt("encrypt", $value['om_id'], SECRET_KEY);
    ?>

                <tr>
                    <td width="3%"><?php echo $key+1; ?></td>
                    <td width="5%"><?php echo $value['om_entry_no']; ?></td>
                    <td width="6%"><?php echo date('d-m-Y', strtotime($value['om_entry_date'])); ?></td>
                    <td width="15%"><?php echo $value['customer_name']; ?></td>
                    <td width="6%"><?php echo $value['om_total_qty']; ?></td>
                    <td width="6%"><?php echo $value['om_total_amt']; ?></td>
                   
                    <td width="3%"><?php $this->load->view('pages/'.$menu.'/'.$sub_menu.'/list/_actions', ['value' => $value]); ?></td>
                </tr>
    <?php 
            endforeach;
        else: 
    ?>
        <tr>
            <td class="text-danger font-weight-bold text-center" colspan="10">NO RECORD FOUND!!!</td>
        </tr>
    <?php endif; ?>
</tbody>