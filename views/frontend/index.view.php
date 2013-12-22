<div id="catalog">
    <?php if(!isset($opt['display'])) {
    $cnt = count(Product::$opt["uri"]);
    if ($cnt == 1)
    {
?>
            <h1><?php echo __('Products', 'product');?></h1>
            <p class="breadcrumbs"><?php echo __('Products', 'product');?></p>
<?php
    }
    else
    {
    $BreadCrumb = Product::GetBreadCrumb($cnt);
    ?>
        <h1><?php echo Product::$h1;?></h1>
        <p class="breadcrumbs"><a href="<?php echo $opt["site_url"];?>product"><?php echo __('Product', 'product');?></a>
            <?
            echo $BreadCrumb;
            ?>
        </p>
<?php
    }
}
?>
<table>
    <tbody>
<?php
$parent = $opt["site_url"].'product/'.$parent;
if(count($items) > 0) {
    foreach ($items as $item) {
        $url_item = $parent.$item["slug"];
        if ($item['type'] == '0') {
        ?>
            <tr>
                <td><a href="<?php echo $url_item; ?>"><?php echo $item["name"]; ?></a></td>
            </tr>
        <?php
        }
        else
        {
        ?>
            <tr data-key="<?php echo $item["id"]; ?>">
                <td><a href="<?php echo $url_item; ?>"><?php echo $item["name"]; ?></a></td>
                <td class="alignright"><?php echo $item["price"]; ?> руб</td>
                <td class="button add"><img src="/plugins/product/img/add.png"/></td>
            </tr>
        <?php
        }
    }
}
else{
    ?>
    <tr>
        <td><?php echo __('Empty', 'product');?></td>
    </tr>
<?php
}
?>
    </tbody>
</table>
    <?php
    if (File::exists(STORAGE . DS . 'product' . DS . Product::$meta["id"] . '.product.txt'))
    {
        echo Text::toHtml(File::getContent(STORAGE . DS . 'product' . DS . Product::$meta["id"] . '.product.txt'));
    }
    ?>
</div>
