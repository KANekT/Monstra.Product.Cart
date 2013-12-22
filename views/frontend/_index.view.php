<?php if(!isset($opt['display'])) {
    $cnt = count(Product::$opt["uri"]);
    if ($cnt == 1)
    {
?>
<ul class="breadcrumb">
        <li><?php echo __('Product', 'product');?></li>
</ul>
<?php
    }
    else
    {
    ?>
    <ul class="breadcrumb">
        <li><a href="<?php echo $opt["site_url"];?>product"><?php echo __('Product', 'product');?></a> <span class="divider">/</span></li>
        <?
        echo $cnt;
        echo Product::GetBreadCrumb($cnt);
        ?>
    </ul>
<?php
    }
}
$parent = $opt["site_url"].'product/'.$parent;
foreach ($items as $row) {
    $url_item = $opt["site_url"].'product/'.$row["slug"];
    ?>
<div class="media">
    <?php if (file::exists($opt["dir"].$row['id'].'.jpg')) { ?>
    <a class="pull-left" href="<?php echo $parent.$row['slug']; ?>"><img class="media-object" alt="" src="<?php echo $url.'thumbnail/'.$row['id'].'.jpg' ?>"></a>
    <?php } else { ?>
    <a class="pull-left" href="<?php echo $url_item; ?>"><img class="img-polaroid" src="<?php echo $opt["url"].'no_item.jpg';?>"></a>
    <?php } ?>
    <div class="media-body">
        <h4 class="media-heading"><?php echo $row['name']; ?></h4>
        <p><?php echo Text::toHtml($row['short']); ?></p>
        <div class="pull-right"><a href="<?php echo $parent.$row['slug']; ?>">подробнее</a></div>
    </div>
</div>
<?php
}