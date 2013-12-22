<h2><?php echo __('Settings product', 'product');?></h2>

<?php
$resize = array(
    'width'   => __('Respect to the width', 'product'),
    'height'  => __('Respect to the height', 'product'),
    'crop'    => __('Similarly, cutting unnecessary', 'product'),
    'stretch' => __('Similarly with the expansion', 'product'),
);
echo (
    Form::open().Form::hidden('csrf', Security::token()).
        '<div class="row-fluid show-grid">'.
        '<div class="span3">'.
        Form::label('width_thumb', __('Width thumbnails (px)', 'product')).
        Form::input('width_thumb', Option::get('product_w')).
        Form::label('height_thumb', __('Height thumbnails (px)', 'product')).
        Form::input('height_thumb', Option::get('product_h')).
        Form::label('limit', __('Product per page', 'product')).
        Form::input('limit', Option::get('product_limit')).Html::br().
        Form::submit('product_submit_settings', __('Save', 'product'), array('class' => 'btn')).Html::Nbsp(2).
        Form::submit('product_submit_settings_cancel', __('Cancel', 'product'), array('class' => 'btn')).
        '</div>'.
        '<div class="span3">'.
        Form::label('width_orig', __('Original width (px, max)', 'product')).
        Form::input('width_orig', Option::get('product_wmax')).
        Form::label('height_orig', __('Original height (px, max)', 'product')).
        Form::input('height_orig', Option::get('product_hmax')).
        Form::label('resize', __('Resize way', 'product')).
        Form::select('resize', $resize, Option::get('product_resize')).Html::Br().
        '</div>'.
        '</div>'.
        Form::close()
);
?>