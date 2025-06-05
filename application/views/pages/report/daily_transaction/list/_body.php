<tbody id="table_tbody" class="font-weight-bold" style="font-size: 0.8rem;">
    <?php 
        if(!empty($data)): ?>
               <?php foreach ($data as $key => $value): 
                    if($key!='search'): ?>
                        <tr  style="background-color: #cacfd1; font-size: 0.9rem;">
                            <td width="5%" ></td>
                            <td width="15%"><?php echo date('d-m-Y', $key); ?></td>
                            <td width="15%"></td>
                            <td width="15%"></td>
                            <td width="15%"></td>
                        </tr>
                        <?php if(!empty($value['data'])): ?>
                            <?php foreach ($value['data'] as $k => $v): ?>
                                <tr >
                                    <td width="5%" ><?php echo $v['sr_no']; ?>.</td>
                                    <td width="15%"><?php echo $v['entry_no']; ?></td>
                                    <td width="15%"><?php echo $v['action']; ?>&nbsp;-&nbsp;<?php echo $v['customer_name']; ?></td>
                                    <td width="15%"><?php echo $v['amt']; ?></td>
                                    <td width="15%"><?php echo $v['payment_mode_name']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif;?>
                        <?php if(!empty($value['total'])): ?>
                            <tr>
                                <td class="border-0" colspan="5">
                                    <table width="100%" style="border: 1px solid #000;">
                                        <?php foreach ($value['total'] as $k => $v): ?>
                                            <tr>
                                                <td width="5%"  style="border-top: 1px dashed #000;"></td>
                                                <td width="15%" style="border-top: 1px dashed #000;"></td>
                                                <td width="15%" style="border-top: 1px dashed #000;"><?php echo $k; ?></td>
                                                <td width="15%" style="border-top: 1px dashed #000;"><?php echo $v; ?></td>
                                                <td width="15%" style="border-top: 1px dashed #000;"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </table>
                                </td>
                            </tr>
                        <?php endif;?>
                    <?php endif;?>
                <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td class="text-danger font-weight-bold text-center" colspan="9">NO RECORD FOUND!!!</td>
        </tr>
    <?php endif; ?>
</tbody>