<tbody id="table_tbody">
    <?php 
        if(!empty($data)): 
            foreach ($data as $key => $value):
                $value['id'] = encrypt_decrypt("encrypt", $value['asm_id'], SECRET_KEY);
    ?>

                <tr>
                    <td width="3%"><?php echo $key+1; ?></td>
                    <td width="10%"><?php echo $value['asm_name']; ?></td>
                    <td width="5%"><?php echo $value['count']; ?></td>
                    <?php if(in_array('edit', $action_data)): ?>
                    <td width="3%">
                        <a 
                            type="button" 
                            class="btn btn-sm btn-primary" 
                            data-toggle="tooltip" 
                            data-placement="bottom" 
                            title="EDIT"
                            href="<?php echo base_url($menu.'/'.$sub_menu.'?action=edit&id='.$value['id']); ?>"
                        ><i class="text-success fa fa-edit"></i></a>										
                    </td> 
                <?php endif;?>
                <?php if(in_array('delete', $action_data)): ?>
                    <td width="3%">
                        <?php if($value['isExist']): ?>
                            <span 
                                type="button" 
                                class="btn btn-sm btn-primary"
                                data-toggle="tooltip" 
                                data-placement="bottom" 
                                title="NOT ALLOWED"
                            ><i class="text-danger fa fa-ban"></i></span>
                        <?php else: ?>
                            <a 
                                type="button" 
                                class="btn btn-sm btn-primary" 
                                data-toggle="tooltip" 
                                data-placement="bottom" 
                                title="DELETE"
                                onclick='record_remove(<?php echo json_encode($value); ?>);'
                            ><i class="text-danger fa fa-trash"></i></a>
                        <?php endif; ?>
                    </td> 
                <?php endif;?>
                    
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