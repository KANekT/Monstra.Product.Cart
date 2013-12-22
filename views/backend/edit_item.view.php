<div class="row-fluid">
    <div class="span12">

        <h2><?php echo __('Edit product', 'product'); ?></h2>
        <br />

        <?php if (Notification::get('success')) Alert::success(Notification::get('success')); ?>

        <?php
        echo (
            Form::open(null, array('class' => 'form_validate','enctype' => 'multipart/form-data')).
            Form::hidden('product_csrf', Security::token()).
            Form::hidden('product_old_slug', $item['slug']).
            Form::hidden('product_old_parent', $item['parent']).
            Form::hidden('product_id', $item['id']).
            Form::hidden('product_type', $item['type'])
        );
        ?>

        <ul class="nav nav-tabs">
            <li <?php if (Notification::get('product')) { ?>class="active"<?php } ?>><a href="#product" data-toggle="tab"><?php echo __('Product', 'product'); ?></a></li>
            <li <?php if (Notification::get('metadata')) { ?>class="active"<?php } ?>><a href="#metadata" data-toggle="tab"><?php echo __('Metadata', 'product'); ?></a></li>
            <li <?php if (Notification::get('settings')) { ?>class="active"<?php } ?>><a href="#settings" data-toggle="tab"><?php echo __('Settings', 'product'); ?></a></li>
            <li <?php if (Notification::get('img')) { ?>class="active"<?php } ?>><a href="#img" data-toggle="tab"><?php echo __('Image', 'product'); ?></a></li>
        </ul>

        <div class="tab-content tab-page">
            <div class="tab-pane <?php if (Notification::get('product')) { ?>active<?php } ?>" id="product">
                <?php
                echo (
                    Form::label('product_name', __('Name', 'product')).
                    Form::input('product_name', $item['name'], array('class' => 'required span6')).
                    Form::label('product_slug', __('Name (slug)', 'product')).
                    Form::input('product_slug', $item['slug'], array('class' => 'required span6')).

                    Form::label('product_price', __('Price', 'product')).
                    Form::input('product_price', $item['price'], array('class' => 'span6'))
                );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('metadata')) { ?>active<?php } ?>" id="metadata">
                <?php
                echo (
                    Form::label('product_title', __('Title', 'product')).
                    Form::input('product_title', $item['title'], array('class' => 'span8')).
                    Html::br(2).
                    Form::label('product_keywords', __('Keywords', 'product')).
                    Form::input('product_keywords', $item['keywords'], array('class' => 'span8')).
                    Html::br(2).
                    Form::label('product_description', __('Description', 'product')).
                    Form::textarea('product_description', $item['description'], array('class' => 'span8'))
                );
                echo (
                    Html::br(2).
                    Form::label('robots', __('Search Engines Robots', 'product')).
                    'no Index'.Html::nbsp().Form::checkbox('product_robots_index', 'index', $item['robots_index']).Html::nbsp(2).
                    'no Follow'.Html::nbsp().Form::checkbox('product_robots_follow', 'follow', $item['robots_follow'])
                );
                ?>
            </div>
            <div class="tab-pane <?php if (Notification::get('settings')) { ?>active<?php } ?>" id="settings">
                <div class="row-fluid">
                    <div class="span3">
                        <?php
                        echo (
                            Form::label('product_parent', __('Parent', 'product')).
                            Form::select('product_parent', $item_array, $item['parent'])
                        );
                        ?>
                    </div>
                    <div class="span3">
                        <?php
                        echo (
                            Form::label('product_status', __('Status', 'product')).
                            Form::select('product_status', $status_array, $item['status'])
                        );
                        ?>
                    </div>
                </div>
            </div>
            <div class="tab-pane <?php if (Notification::get('img')) { ?>active<?php } ?>" id="img">
                <div class="row-fluid"><ul class="thumbnails img-upload">
                        <li>
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-preview thumbnail image" style="width: 200px; height: 150px; vertical-align: middle; line-height: 150px;">
                                    <?php
                                    if(file::exists($opt['dir'] . DS . 'thumbnail' . DS . $item['id'].'.jpg')):
                                        ?>
                                        <a href="#" rel="<?php echo $opt['url'].$item['id'].'.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $opt['url'].'thumbnail/'.$item['id'].'.jpg' ?>"></a>
                                    <?php endif; ?>
                                </div>
                                <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new"><?php echo __('Select image', 'product'); ?></span>
                                <span class="fileupload-exists"><?php echo __('Change', 'product'); ?></span>
                                <?php echo Form::file('product_file[]', array('id' => 'product_file'))?>
                            </span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload" data-key="<?php echo $item['id'] ?>" data-dir="" data-action="delProdImg"><?php echo __('Remove', 'product'); ?></a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-preview thumbnail image" style="width: 200px; height: 150px; vertical-align: middle; line-height: 150px;">
                                    <?php
                                    if(file::exists($opt['dir'] . DS . 'thumbnail' . DS . $item['id'].'.150.jpg')):
                                        ?>
                                        <a href="#" rel="<?php echo $opt['url'].$item['id'].'.150.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $opt['url'].'thumbnail/'.$item['id'].'.150.jpg' ?>"></a>
                                    <?php endif; ?>
                                </div>
                                <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new"><?php echo __('Select image', 'product'); ?></span>
                                <span class="fileupload-exists"><?php echo __('Change', 'product'); ?></span>
                                <?php echo Form::file('product_file[]', array('id' => 'product_file_150'))?>
                            </span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload" data-key="<?php echo $item['id'] ?>" data-dir=".150" data-action="delProdImg"><?php echo __('Remove', 'product'); ?></a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-preview thumbnail image" style="width: 200px; height: 150px; vertical-align: middle; line-height: 150px;">
                                    <?php
                                    if(file::exists($opt['dir'] . DS . 'thumbnail' . DS . $item['id'].'.151.jpg')):
                                        ?>
                                        <a href="#" rel="<?php echo $opt['url'].$item['id'].'.151.jpg' ?>"><img alt="" style="max-width:100px; max-height:50px;" src="<?php echo $opt['url'].'thumbnail/'.$item['id'].'.151.jpg' ?>"></a>
                                    <?php endif; ?>
                                </div>
                                <div>
                            <span class="btn btn-file">
                                <span class="fileupload-new"><?php echo __('Select image', 'product'); ?></span>
                                <span class="fileupload-exists"><?php echo __('Change', 'product'); ?></span>
                                <?php echo Form::file('product_file[]', array('id' => 'product_file_151'))?>
                            </span>
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload" data-key="<?php echo $item['id'] ?>" data-dir=".151" data-action="delProdImg"><?php echo __('Remove', 'product'); ?></a>
                                </div>
                            </div>
                        </li>
                    </ul></div>
            </div>

        </div>
        <br />
        <?php Action::run('admin_editor', array(Html::toText($item['content']))); ?>

        <br />

        <div class="row-fluid">
            <div class="span6">
                <?php
                echo (
                    Form::submit('edit_product_and_exit', __('Save and exit', 'product'), array('class' => 'btn')).Html::nbsp(2).
                    Form::submit('edit_product', __('Save', 'product'), array('class' => 'btn'))
                );
                ?>
            </div>
            <div class="span6">
                <?php echo Form::close(); ?>
            </div>
        </div>
    </div>
</div>