<div class="row-fluid">
    <div class="span12">

        <h2><?php echo __('Products', 'product'); ?>
            <div class="btn-group">
                <?php echo Html::anchor(__('Create product catalog', 'product'), 'index.php?id=product&action=add', array('title' => __('Create new product catalog', 'product'), 'class' => 'btn default btn-small')) ?>
                <button class="btn btn-small dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><?php echo Html::anchor(__('Settings', 'product'), 'index.php?id=product&action=settings');?></li>
                    <li><?php echo Html::anchor(__('Orders', 'product'), 'index.php?id=product&action=orders');?></li>
                    <li><?php echo Html::anchor(__('Example Code', 'product'), '#exampleCode', array('role' => 'button', 'data-toggle' => 'modal'));?></a></li>
                </ul>
            </div>
        </h2>
        <br />
        <?php
        if (Notification::get('success')) Alert::success(Notification::get('success'));
        if (Notification::get('error')) Alert::success(Notification::get('error'));
        ?>

        <table class="table table-bordered">
            <thead>
            <tr>
                <td><?php echo __('Name', 'product'); ?></td>
                <td width="10%"><?php echo __('Type', 'product'); ?></td>
                <td width="10%"><?php echo __('Status', 'product'); ?></td>
                <td width="10%"><?php echo __('Actions', 'product'); ?></td>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count($items) != 0) {
                foreach ($items as $item) {
                    ?>
                    <tr <?php if(trim($item['class']) !== '') {?> class="<?php echo $item['class']; ?>" <?php } ?>>
                        <td>
                            <?php
                            if (isset($item['parent'])) {
                                echo $item['dash'].'<span parent="true" view="true" rel="'.$item['slug'].'">-</span>';
                            }
                            else
                            {
                                echo $item['dash'].'<span parent="false">'.Html::nbsp(1).'</span>';
                            }
                            ?>
                            <?php
                            echo Html::nbsp(1).Html::anchor(Html::toText($item['name']), $site_url.'product/'.$item['url'], array('target' => '_blank', 'rel' => $item['slug']));
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($item['type'] == '0') {
                                echo __('Catalog', 'product');
                            }
                            else
                            {
                                echo __('Product', 'product');
                            }
                            ?>
                        </td>
                        <td>
                            <?php echo $item['status']; ?>
                        </td>
                        <td>
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <?php echo Html::anchor(__('Edit', 'product'), 'index.php?id=product&action=edit&guid='.$item['id'], array('class' => 'btn btn-actions')); ?>
                                    <a class="btn dropdown-toggle btn-actions" data-toggle="dropdown" href="#" style="font-family:arial;"><span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php if ($item['type'] == '0'): ?>
                                            <li><a href="index.php?id=product&action=add&parent=<?php echo $item['slug']; ?>" title="<?php echo __('Create new product catalog', 'product'); ?>"><?php echo __('Create product catalog', 'product'); ?></a></li>
                                            <li><a href="index.php?id=product&action=add&type=item&parent=<?php echo $item['slug']; ?>" title="<?php echo __('Create new product', 'product'); ?>"><?php echo __('Create product', 'product'); ?></a></li>
                                        <?php endif;?>
                                        <li>
                                            <?php echo Html::anchor(__('Delete', 'product'),
                                                'index.php?id=product&action=delete&pid='.$item['id'].'&token='.Security::token(),
                                                array('onclick' => "return confirmDelete('".__("Delete product: :product", 'product', array(':product' => Html::toText($item['name'])))."')"));
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
<?php echo View::factory('product/views/backend/modal')->render();?>