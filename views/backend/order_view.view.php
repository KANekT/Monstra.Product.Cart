<h2>
    <?php echo Html::anchor(__('Products', 'product'), 'index.php?id=product');?> -
    <?php echo Html::anchor(__('Orders', 'product'), 'index.php?id=product&action=orders');?> -
    Заказ от компании <?php echo $order['comp'];?>
</h2>
<table class="table table-striped">
    <thead>
    <tr>
        <th><?php echo __('Name', 'product');?></th>
        <th><?php echo __('Amount', 'product');?></th>
        <th><?php echo __('Price', 'product');?></th>
        <th> </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $all = 0;
    foreach($items as $key => $item):
        print_r($items);
        exit();
        $all += (float)$item['price'] * (int)$item['amount'];
        ?>
        <tr>
            <td><?php echo $item['name'];?></td>
            <td><input type="text" data-key="<?php echo $item['id'];?>" value="<?php echo $item['amount'];?>" class="amount" /></td>
            <td class="alignright"><?php echo $item['price'];?></td>
            <td class="button"><a href="javascript:void()" onclick="$(this).parent().parent().empty();"><img src="/plugins/product/img/del.png" alt="<?php echo __('Delete', 'product');?>" /></a></td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="2">Итого:</td>
        <td colspan="2"><strong><?php echo $all;?></strong> руб.</td>
    </tr>
    </tbody>
</table>
<?php echo Form::hidden('product_csrf', Security::token()); ?>

<div class="form-horizontal">
    <div class="control-group">
        <label class="control-label" for="comp"><?php echo __('Company', 'product');?></label>
        <div class="controls">
            <?php echo $order['comp'];?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="phone"><?php echo __('Phone', 'product');?></label>
        <div class="controls">
            <?php echo $order['phone'];?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="email"><?php echo __('Email', 'product');?></label>
        <div class="controls">
            <?php echo $order['email'];?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="fio"><?php echo __('FIO', 'product');?></label>
        <div class="controls">
            <?php echo $order['fio'];?>
        </div>
    </div>
    <div class="control-group">
        <label class="control-label" for="comm"><?php echo __('Comment in order', 'product');?></label>
        <div class="controls">
            <?php echo $order['comm'];?>
        </div>
    </div>
</div>

<button class="btn" onclick="$.jCart.save('<?php echo $order['id'];?>');"><?php echo __('Save', 'product'); ?></button>
