<tbody id="table_tbody">
    <?php 
        if(!empty($data)): 
            foreach ($data as $key => $value):
                $id = encrypt_decrypt("encrypt", $value['apparel_id'], SECRET_KEY);
    ?>

                <tr class="<?php echo $value['apparel_status'] == 0 ? 'text-danger' : '' ?>">
                    <td width="3%"><?php echo $key+1; ?></td>
                    <td width="10%"><?php echo $value['apparel_name']; ?></td>
                    <td width="10%"><?php echo $value['category_name']; ?></td>
                    <td width="5%"><?php echo $value['apparel_charges']; ?></td>
                    <td width="5%"><?php echo $value['apparel_status'] == 1 ? 'active' : 'inactive'; ?></td>
                   <!--  <td width="3%">
                        <button 
                            type="button" 
                            class="btn btn-sm btn-primary" 
                            onclick='apparel_process_popup(<?php echo $value["apparel_id"]; ?>)'>
                            <?php echo $value['process_cnt']; ?> <i class="text-info fa fa-plus"></i>
                        </button>										
                    </td> -->
                    <?php if(in_array('read', $action_data)): ?>
                        <td width="3%">
                            <a 
                                type="button" 
                                class="btn btn-sm btn-primary" 
                                onclick='apparel_popup(<?php echo json_encode(["action" => "read", "id" => $value["apparel_id"]]); ?>)'>
                                <i class="text-info fa fa-eye"></i>
                            </a>										
                        </td>
                    <?php endif; ?>
                    <?php if(in_array('edit', $action_data)): ?>
                        <td width="3%">
                            <a 
                                type="button" 
                                class="btn btn-sm btn-primary" 
                                onclick='apparel_popup(<?php echo json_encode(["action" => "edit", "id" => $value["apparel_id"]]); ?>)'>
                                <i class="text-success fa fa-edit"></i>
                            </a>										
                        </td>
                    <?php endif; ?>
                    <?php if(in_array('delete', $action_data)): ?>
                        <td width="3%">
                            <?php if($value['isExist']): ?>
                                <button type="button" class="btn btn-sm btn-primary"><i class="text-danger fa fa-ban"></i></button>
                            <?php else: ?>
                                <a 
                                    type="button" 
                                    class="btn btn-sm btn-primary"
                                    onclick='apparel_remove(<?php echo json_encode($value); ?>);'
                                >
                                    <i class="text-danger fa fa-trash"></i>
                                </a>
                            <?php endif; ?>												                                        
                        </td>
                    <?php endif; ?>
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