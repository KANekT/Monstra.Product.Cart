<div id="product">
    <?php if(!isset($opt['display'])) {
        $cnt = count(Product::$opt["uri"]);
        $BreadCrumb = Product::GetBreadCrumb($cnt);
        ?>
        <h1><?php echo Product::$h1;?></h1>
        <p class="breadcrumbs"><a href="<?php echo $opt["site_url"];?>product"><?php echo __('Product', 'product');?></a>
            <?
            echo $BreadCrumb;
            ?>
        </p>
    <?php } ?>
    <div class="photo">
        <?php if (file::exists($opt["dir"].'thumbnail/'.$item['id'].'.jpg')) { ?>
            <a class="img01" href="<?php echo $opt["url"].$item['id'].'.jpg' ?>"><img class="img01" alt="" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.jpg' ?>"></a>
        <?php } else { ?>
            <img class="img01" src="<?php echo $opt["url"].'small-photo-01.jpg';?>">
        <?php } ?>
        <?php if (file::exists($opt["dir"].'thumbnail/'.$item['id'].'.150.jpg')) { ?>
            <a class="img02" href="<?php echo $opt["url"].$item['id'].'.150.jpg' ?>"><img class="img02" alt="" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.150.jpg' ?>"></a>
        <?php } else { ?>
            <img class="img02" src="<?php echo $opt["url"].'small-photo-02.jpg';?>">
        <?php } ?>
        <?php if (file::exists($opt["dir"].'thumbnail/'.$item['id'].'.151.jpg')) { ?>
            <a class="img03" href="<?php echo $opt["url"].$item['id'].'.151.jpg' ?>"><img class="img03" alt="" src="<?php echo $opt["url"].'thumbnail/'.$item['id'].'.151.jpg' ?>"></a>
        <?php } else { ?>
            <img class="img03" src="<?php echo $opt["url"].'small-photo-03.jpg';?>">
        <?php } ?>
    </div>
    <div class="description">
        <?php
        echo Text::toHtml(File::getContent(STORAGE . DS . 'product' . DS . $item['id'] . '.product.txt'));
        ?>
    </div>
    <div class="price"><?php echo $item['price'];?></div>
    <div class="buy">
        <input type="submit" value="Добавить в заказ" class="btn cart-add" data-key="<?php echo $item['id']; ?>" />
    </div>
</div>