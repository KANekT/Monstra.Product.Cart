<div class="row-fluid">
    <div class="span12">

        <h2>
            <?php echo Html::anchor(__('Products', 'product'), 'index.php?id=product');?> -
            <?php echo __('Orders', 'product'); ?>
        </h2>
        <br />
        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));
        ?>

        <table class="table table-bordered">
            <thead>
            <tr>
                <td><?php echo __('Company', 'product'); ?></td>
                <td><?php echo __('FIO', 'product'); ?></td>
                <td><?php echo __('Phone', 'product'); ?></td>
                <td><?php echo __('Email', 'product'); ?></td>
                <td><?php echo __('Status', 'product'); ?></td>
                <td width="10%"><?php echo __('Actions', 'product'); ?></td>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count($items) != 0) {
                foreach ($items as $item) {
                    ?>
                    <tr>
                        <td>
                            <?php echo $item['comp']; ?>
                        </td>
                        <td>
                            <?php echo $item['fio']; ?>
                        </td>
                        <td>
                            <?php echo $item['phone']; ?>
                        </td>
                        <td>
                            <?php echo $item['email']; ?>
                        </td>
                        <td>
                            <?php echo $status[$item['status']]; ?>
                        </td>
                        <td>
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <?php echo Html::anchor(__('Edit', 'product'), 'index.php?id=product&action=view&guid='.$item['id'], array('class' => 'btn btn-actions')); ?>
                                    <a class="btn dropdown-toggle btn-actions" data-toggle="dropdown" href="#" style="font-family:arial;"><span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li <? echo ($item['status'] == '') ? 'class="active"' : '' ?>>
                                            <?php echo Html::anchor($status[''], 'index.php?id=product&action=status&status=&order_id='.$item['id'].'&token='.Security::token()); ?>
                                        </li>
                                        <li <? echo ($item['status'] == 'work') ? 'class="active"' : '' ?>>
                                            <?php echo Html::anchor($status['work'], 'index.php?id=product&action=status&status=work&order_id='.$item['id'].'&token='.Security::token()); ?>
                                        </li>
                                        <li <? echo ($item['status'] == 'complete') ? 'class="active"' : '' ?>>
                                            <?php echo Html::anchor($status['complete'], 'index.php?id=product&action=status&status=complete&order_id='.$item['id'].'&token='.Security::token()); ?>
                                        </li>
                                        <li class="divider"></li>
                                        <li>
                                            <?php echo Html::anchor(__('Delete', 'product'),
                                                'index.php?id=product&action=delete&order_id='.$item['id'].'&token='.Security::token(),
                                                array('onclick' => "return confirmDelete('".__("Delete order: :order", 'product', array(':order' => Html::toText($item['id'])))."')"));
                                            ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>