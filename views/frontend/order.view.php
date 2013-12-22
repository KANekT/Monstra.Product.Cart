<div id="order">
    <h1>Оформление заказа</h1>
    <p class="breadcrumbs"><a href="<?php echo $opt["site_url"];?>product"><?php echo __('Product', 'product');?></a> - Оформление заказа</p>
    <table>
        <h3>Ваш заказ</h3>
        <thead>
        <tr>
            <th><?php echo __('Name', 'product');?></th>
            <th><?php echo __('Amount', 'product');?></th>
            <th><?php echo __('Price', 'product');?></th>
            <th> </th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <form class="form validate">
        <?php echo Form::hidden('product_csrf', Security::token()); ?>
        <label for="order_comp"><?php echo __('Company', 'product');?></label>
        <input type="text" class="input" id="order_comp" name="ocomp" required />

        <label for="order_phone"><?php echo __('Phone', 'product');?></label>
        <input type="text" class="input" id="order_phone" name="ophone" required />

        <label for="order_email"><?php echo __('Email', 'product');?></label>
        <input type="email" class="input" id="order_email" name="oemail" required />

        <label for="order_fio"><?php echo __('FIO', 'product');?></label>
        <input type="text" class="input" id="order_fio" name="ofio" required />

        <label for="order_comm"><?php echo __('Comment in order', 'product');?></label>
        <textarea style="height: 200px;" class="input" id="order_comm" name="ocomm" required /></textarea>

        <input type="submit" value="Отправить заказ" class="btn" />
    </form>

</div>
