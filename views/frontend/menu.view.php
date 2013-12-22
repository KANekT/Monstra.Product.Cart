<ul>
    <?php
    $first = 0;
    $cnt = 0;
    $close = array();
if (count($product) != 0) {
foreach ($item as $product) {
    $cnt++;
?>

    <li>
        <?php
        echo Html::nbsp(1).Html::anchor(Html::toText($item['title']), Option::get('siteurl').'product'.$item['url'], array('target' => '_blank', 'rel' => $item['slug']));
        if (isset($item['parent'])) {
            echo '<ul>';
        }
    ?>

    <?php
    if (isset($item['parent'])) {
        $close[$item['close']] = 0;
    }
        if (isset($close[$cnt])) {
            echo '</ul>';
        }
    ?>
    </li>
<?php }
}
?>
</ul>