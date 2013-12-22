<?php

// Admin Navigation: add new item
Navigation::add(__('Products', 'product'), 'content', 'product', 10);

// Add actions
Action::add('admin_themes_extra_index_template_actions','ProductAdmin::formComponent');
Action::add('admin_themes_extra_actions','ProductAdmin::formComponentSave');

/**
 * Product admin class
 */
class ProductAdmin extends Backend {
    /**
     * Product tables
     *
     * @var object
     */
    public static $product = null;

    /**
     * Product tree
     *
     * @var array
     */
    public static $product_array = array();

    public static $theme = 'default';
    
    public static $opt = null;

    /**
     * Main Product admin function
     */
    public static function main() {
        ProductAdmin::$opt['site_url'] = Option::get('siteurl');
        $errors = array();

        $product = new Table('product');
        $orders = new Table('product_orders');

        ProductAdmin::$product = $product;
        ProductAdmin::$product_array = array();
        // Status array
        $status_array = array('published' => __('Published', 'product'), 'draft' => __('Draft', 'product'));
        // Status array
        $orders_array = array(''=> __('New order status', 'product'), 'work' => __('In work order status', 'product'), 'complete' => __('Complete order status', 'product'));

        ProductAdmin::$opt['url'] = ProductAdmin::$opt['site_url'] . 'public/uploads/product/';
        ProductAdmin::$opt['dir'] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS;

        // Check for get actions
        // -------------------------------------
        if (Request::get('action')) {

            // Switch actions
            // -------------------------------------
            switch (Request::get('action')) {
                // Settings
                // -------------------------------------
                case "settings":

                    if (Request::post('product_submit_settings_cancel')) {
                        Request::redirect('index.php?id=product');
                    }

                    if (Request::post('product_submit_settings')) {
                        if (Security::check(Request::post('csrf'))) {
                            Option::update(array(
                                'product_limit'        => (int)Request::post('limit'),
                                'product_limit_admin'  => (int)Request::post('limit_admin'),
                                'product_w'            => (int)Request::post('width_thumb'),
                                'product_h'            => (int)Request::post('height_thumb'),
                                'product_wmax'         => (int)Request::post('width_orig'),
                                'product_hmax'         => (int)Request::post('height_orig'),
                                'product_resize'       => (string)Request::post('resize')
                            ));

                            Notification::set('success', __('Your changes have been saved', 'product'));

                            Request::redirect('index.php?id=product');
                        } else { die('csrf detected! Settings'); }
                    }

                    View::factory('product/views/backend/settings')->display();
                    Action::run('admin_product_extra_settings_template');
                    break;

                case "orders":

                    View::factory('product/views/backend/orders')
                        ->assign('items', $orders->select(null, 'all', null, array('comp', 'fio', 'phone', 'email', 'status')))
                        ->assign('status', $orders_array)
                        ->display();

                    break;

                case "status":
                    if (Security::check(Request::get('token'))) {
                        $orders->updateWhere('[id='.Request::get('order_id').']', array('status' => Request::get('status')));
                    }
                    if (Request::get('status') == '1' || Request::get('status') == '2')
                    {
                        $data = $orders->select('[id='.Request::get('order_id').']', null);
                        $records = json_decode($data['items']);
                        $items = array();
                        foreach($records as $item)
                        {
                            $record = $product->select('[id='.$item[0].']', 'all', null, array('name', 'price', 'type'));
                            if ($record != null)
                            {
                                $record[0]['amount'] = (int)$item[1];
                                $items[] = $record[0];
                            }
                        }

                        $sys_email = Option::get('system_email');

                        $mail = new PHPMailer();
                        $mail->CharSet = 'utf-8';
                        $mail->ContentType = 'text/html';
                        $mail->SetFrom($sys_email);
                        $mail->AddReplyTo($sys_email);
                        $mail->AddAddress($data['email']);
                        $mail->Subject = Option::get('sitename');
                        $mail->MsgHTML(View::factory('product/views/emails/order_'.Request::get('status'))
                            ->assign('id', Request::get('order_id'))
                            ->assign('items', $items)
                            ->assign('site', Option::get('sitename'))
                            ->assign('url', Option::get('siteurl'))
                            ->render());
                        $mail->Send();
                    }

                    Notification::set('success', __('Status for order #<i>:order</i> have been updated.', 'product', array(':order' => Request::get('order_id'))));
                    Request::redirect('index.php?id=product&action=orders');
                    break;

                case "view":

                    $data = $orders->select('[id="'.Request::get('guid').'"]', null);
                    $records = json_decode($data['items']);
                    $items = array();
                    foreach($records as $item)
                    {
                        $record = $product->select('[id='.$item[0].']', 'all', null, array('name', 'price', 'type'));
                        if ($record != null)
                        {
                            $record[0]['amount'] = (int)$item[1];
                            $items[] = $record[0];
                        }
                    }

                    View::factory('product/views/backend/order_view')
                        ->assign('order', $data)
                        ->assign('items', $items)
                        ->display();
                    break;

                // Add product
                // -------------------------------------
                case "add":

                    // Add product
                    if (Request::post('add_product') || Request::post('add_product_and_exit')) {

                        if (Security::check(Request::post('product_csrf'))) {
                            // Get parent product
                            if (Request::post('product_parent') == '0') {
                                $parent = '';
                            } else {
                                $parent = Request::post('product_parent');
                            }

                            if (Request::post('product_robots_index'))  $robots_index = 'noindex';   else $robots_index = 'index';
                            if (Request::post('product_robots_follow')) $robots_follow = 'nofollow'; else $robots_follow = 'follow';
                            
                            if ((string)Request::post('product_name') == '')
                            {
                                $errors[] = __('Name is empty', 'product');
                            }

                            if ((string)Request::post('product_slug') == '')
                            {
                                $slug = Security::safeName(Request::post('product_name'), '-', true);
                            }
                            else
                            {
                                $slug = Security::safeName(Request::post('product_slug'), '-', true);
                            }

                            $slugs = $product->select("[slug='".$slug."']");

                            if ($slugs !== null) {
                                $errors[] = __('Slug is exits', 'product');
                                Notification::set('error', __('Slug is exits', 'product'));
                            }

                            // If no errors then try to save
                            if (count($errors) == 0) {
                                $last_id = 0;
                                $data = array(
                                    'name'          => trim(Request::post('product_name')),
                                    'title'         => trim(Request::post('product_title')),
                                    'slug'          => $slug,
                                    'parent'        => $parent,
                                    'status'        => Request::post('product_status'),
                                    'robots_index'  => $robots_index,
                                    'robots_follow' => $robots_follow,
                                    'description'   => Request::post('product_description'),
                                    'keywords'      => Request::post('product_keywords'),
                                    'price'         => Request::post('product_price'),
                                    'type'          => Request::post('product_type')
                                );
                                // Insert new product
                                if ($product->insert($data)) {

                                    // Get inserted product ID
                                    $last_id = $product->lastId();
                                    if ($data['type'] == '1')
                                    {
                                        $product->updateWhere('[id="'.$last_id.'"]', array('slug' => $last_id.'-'.$data['slug']));
                                    }

                                    $files = ProductAdmin::reArrayFiles($_FILES['product_file']);

                                    if (isset($files[0])) {
                                        $img = $files[0];
                                        ProductAdmin::UploadImg($img, '240', '320', $last_id);
                                    }
                                    if ($data['type'] == '1' && $files[1] != null) {
                                        $img = $files[1];
                                        ProductAdmin::UploadImg($img, '150', '113', $last_id.'.150');
                                    }
                                    if ($data['type'] == '1' && $files[2] != null) {
                                        $img = $files[2];
                                        ProductAdmin::UploadImg($img, '150', '113', $last_id.'.151');
                                    }

                                    // Save content
                                    File::setContent(STORAGE . DS . 'product' . DS . $last_id . '.product.txt', XML::safe(Request::post('editor')));

                                    // Send notification
                                    Notification::set('success', __('Your product <i>:product</i> have been added.', 'product', array(':product' => Request::post('product_name'))));
                                }

                                // Run add extra actions
                                Action::run('admin_product_action_add');

                                // Redirect
                                if (Request::post('add_product_and_exit')) {
                                    Request::redirect('index.php?id=product');
                                } else {
                                    Request::redirect('index.php?id=product&action=edit&guid='.$last_id);
                                }
                            }
                            else
                            {
                                Notification::set('error', implode("<br>", $errors));
                                Request::redirect('index.php?id=product&action=add&parent='.Request::get('parent'));
                            }

                        } else { die('csrf detected! Add'); }

                    }

                    // Get all product
                    ProductAdmin::GetTree($status_array);
                    $product_array[] = '-none-';
                    if (is_array(ProductAdmin::$product_array))
                    {
                        foreach (ProductAdmin::$product_array as $item) {
                            $product_array[$item['slug']] = $item['dash'].$item['name'];
                        }
                    }

                    // Save fields
                    if (Request::post('product_slug'))             $product_item['slug']          = Request::post('product_slug');          else $product_item['slug'] = '';
                    if (Request::post('product_name'))             $product_item['name']          = Request::post('product_name');          else $product_item['name'] = '';
                    if (Request::post('product_title'))            $product_item['title']         = Request::post('product_title');         else $product_item['title'] = '';
                    if (Request::post('product_keywords'))         $product_item['keywords']      = Request::post('product_keywords');      else $product_item['keywords'] = '';
                    if (Request::post('product_description'))      $product_item['description']   = Request::post('product_description');   else $product_item['description'] = '';
                    if (Request::post('product_editor'))           $product_item['content']       = Request::post('product_editor');        else $product_item['content'] = '';
                    if (Request::post('product_short'))            $product_item['short']         = Request::post('product_short');         else $product_item['short'] = '';
                    if (Request::post('product_status'))           $product_item['status']        = Request::post('product_status');        else $product_item['status'] = 'published';
                    if (Request::post('product_access'))           $product_item['access']        = Request::post('product_access');        else $product_item['access'] = 'public';
                    if (Request::post('product_price'))            $product_item['price']         = Request::post('product_price');         else $product_item['price'] = '';
                    if (Request::post('product_parent'))           $product_item['parent']        = Request::post('product_parent');        else if(Request::get('parent')) $product_item['parent'] = Request::get('parent'); else $product_item['parent'] = '';
                    if (Request::post('product_robots_index'))     $product_item['robots_index']  = true;                                   else $product_item['robots_index'] = false;
                    if (Request::post('product_robots_follow'))    $product_item['robots_follow'] = true;                                   else $product_item['robots_follow'] = false;
                    //--------------

                    // Generate date
                    $product_item['date'] = Date::format(time(), 'Y-m-d H:i:s');

                    // Set Tabs State - product
                    Notification::setNow('product', 'product');

                    if (Request::get('type') == 'item')
                    {
                        // Display view
                        View::factory('product/views/backend/add_item')
                            ->assign('item', $product_item)
                            ->assign('item_array', $product_array)
                            ->assign('status_array', $status_array)
                            ->assign('errors', $errors)
                            ->display();
                    }
                    else{
                        // Display view
                        View::factory('product/views/backend/add')
                            ->assign('item', $product_item)
                            ->assign('item_array', $product_array)
                            ->assign('status_array', $status_array)
                            ->assign('errors', $errors)
                            ->display();
                    }

                    break;

                // Edit product
                // -------------------------------------
                case "edit":

                    if (Request::post('edit_product') || Request::post('edit_product_and_exit')) {

                        if (Security::check(Request::post('product_csrf'))) {
                            $guid = Request::get('guid');

                            // Get product parent
                            if (Request::post('product_parent') == '0') {
                                $parent = '';
                            } else {
                                $parent = Request::post('product_parent');
                            }

                            // Save fields
                            if (Request::post('product_name'))              $product_item['name']          = Request::post('product_name');          else $product_item['name'] = '';
                            if (Request::post('product_slug'))              $product_item['slug']          = Request::post('product_slug');          else $product_item['slug'] = '';
                            if (Request::post('product_title'))             $product_item['title']         = Request::post('product_title');         else $product_item['title'] = '';
                            if (Request::post('product_keywords'))          $product_item['keywords']      = Request::post('product_keywords');      else $product_item['keywords'] = '';
                            if (Request::post('product_description'))       $product_item['description']   = Request::post('product_description');   else $product_item['description'] = '';
                            if (Request::post('product_editor'))            $product_item['content']       = Request::post('product_editor');        else $product_item['content'] = '';
                            if (Request::post('product_status'))            $product_item['status']        = Request::post('product_status');        else $product_item['status'] = '';
                            if (Request::post('product_parent'))            $product_item['parent']        = Request::post('product_parent');        else if(Request::get('parent')) $product_item['parent'] = Request::get('parent'); else $product_item['parent'] = '';
                            if (Request::post('product_robots_index'))      $product_item['robots_index']  = true;                                   else $product_item['robots_index'] = false;
                            if (Request::post('product_robots_follow'))     $product_item['robots_follow'] = true;                                   else $product_item['robots_follow'] = false;
                            //--------------

                            if (Request::post('product_robots_index'))      $robots_index = 'noindex';      else $robots_index = 'index';
                            if (Request::post('product_robots_follow'))     $robots_follow = 'nofollow';    else $robots_follow = 'follow';

                            if ((string)Request::post('product_name') == '')
                            {
                                $errors[] = __('Name is empty', 'product');
                            }

                            if ((string)Request::post('product_slug') == '')
                            {
                                $slug = Security::safeName(Request::post('product_name'), '-', true);
                            }
                            else
                            {
                                $slug = Security::safeName(Request::post('product_slug'), '-', true);
                            }

                            $slugs = $product->select("[slug='".$slug."']", null);

                            if ($slugs !== null && $slugs['id'] != $guid) {
                                $errors[] = __('Slug is exits', 'product');
                                Notification::set('error', __('Slug is exits', 'product'));
                            }

                            if (count($errors) == 0) {

                                $data = array(
                                    'name'          => $product_item['name'],
                                    'title'         => trim(Request::post('product_title')),
                                    'slug'          => $slug,
                                    'parent'        => $parent,
                                    'status'        => $product_item['status'],
                                    'robots_index'  => $robots_index,
                                    'robots_follow' => $robots_follow,
                                    'description'   => $product_item['description'],
                                    'keywords'      => $product_item['keywords'],
                                    'price'         => Request::post('product_price'),
                                    'type'          => Request::post('product_type')
                                );

                                // Update parents in all childrens
                                if ((Security::safeName(Request::post('product_name'), '-', true)) !== (Security::safeName(Request::post('product_old_slug'), '-', true)) and (Request::post('product_old_parent') == '')) {

                                    $product->updateWhere('[parent="'.Request::get('slug').'"]', array('parent' => Text::translitIt(trim(Request::post('product_name')))));

                                    if ($product->updateWhere('[id="'.$guid.'"]', $data)) {

                                        File::setContent(STORAGE . DS . 'product' . DS . Request::post('product_id') . '.product.txt', XML::safe(Request::post('editor')));
                                        Notification::set('success', __('Your changes to the product <i>:product</i> have been saved.', 'product', array(':product' => $product_item['name'])));
                                    }

                                    // Run edit extra actions
                                    Action::run('admin_product_action_edit');

                                } else {

                                    if ($product->updateWhere('[id="'.$guid.'"]', $data)) {

                                        File::setContent(STORAGE . DS . 'product' . DS . Request::post('product_id') . '.product.txt', XML::safe(Request::post('editor')));
                                        Notification::set('success', __('Your changes to the product <i>:product</i> have been saved.', 'product', array(':product' => $product_item['name'])));
                                    }

                                    // Run edit extra actions
                                    Action::run('admin_product_action_edit');
                                }

                                $files = ProductAdmin::reArrayFiles($_FILES['product_file']);

                                if (isset($files[0]) && $files[0] != null) {
                                    $img = $files[0];
                                    ProductAdmin::UploadImg($img, '240', '320', $guid);
                                }
                                if ($data['type'] == '1' && isset($files[1]) && $files[1] != null) {
                                    $img = $files[1];
                                    ProductAdmin::UploadImg($img, '150', '113', $guid.'.150');
                                }
                                if ($data['type'] == '1' && isset($files[2]) && $files[2] != null) {
                                    $img = $files[2];
                                    ProductAdmin::UploadImg($img, '150', '113', $guid.'.151');
                                }

                                // Redirect
                                if (Request::post('edit_product_and_exit')) {
                                    Request::redirect('index.php?id=product');
                                } else {
                                    Request::redirect('index.php?id=product&action=edit&guid='.$guid);
                                }
                            }

                        } else { die('csrf detected! Edit'); }
                    }

                    // Get all product
                    ProductAdmin::GetTree($status_array);
                    $product_array[] = '-none-';
                    if (is_array(ProductAdmin::$product_array))
                    {
                        foreach (ProductAdmin::$product_array as $row) {
                            $product_array[$row['slug']] = $row['dash'].$row['name'];
                        }
                    }

                    $item = $product->select('[id="'.Request::get('guid').'"]', null);
                    $item['content'] = Text::toHtml(File::getContent(STORAGE . DS . 'product' . DS . $item['id'] . '.product.txt'));

                    if ($item) {
                        if (Request::post('product_parent')) {
                            // Save field
                            $product_item['parent'] = Request::post('product_parent');
                        } else {
                            $product_item['parent'] = $item['parent'];
                        }

                        unset($product_array[$item['slug']]);

                        Notification::setNow('product', 'product');

                        if ($item['type'] == '0')
                        {
                            // Display view
                            View::factory('product/views/backend/edit')
                                ->assign('item', $item)
                                ->assign('item_array', $product_array)
                                ->assign('status_array', $status_array)
                                ->assign('opt', ProductAdmin::$opt)
                                ->assign('errors', $errors)
                                ->display();
                        }
                        else
                        {
                            $item['content'] = Text::toHtml(File::getContent(STORAGE . DS . 'product' . DS . $item['id'] . '.product.txt'));
                            // Display view
                            View::factory('product/views/backend/edit_item')
                                ->assign('item', $item)
                                ->assign('item_array', $product_array)
                                ->assign('status_array', $status_array)
                                ->assign('opt', ProductAdmin::$opt)
                                ->assign('errors', $errors)
                                ->display();
                        }
                    }

                    break;

                // Delete product
                // -------------------------------------
                case "delete":

                    if (Security::check(Request::get('token'))) {

                        if (Request::get('order_id') != '')
                        {
                            $id = Request::get('order_id');
                            if ($orders->deleteWhere('[id="'.$id.'" ]'))
                            {
                                Notification::set('success', __('Order <i>:order</i> deleted', 'product', array(':order' => $id)));
                            }
                            else
                            {
                                Notification::set('errors', 'Not Deleted!'.$id);
                            }
                            // Redirect
                            Request::redirect('index.php?id=product&action=orders');
                        }
                        // Get specific product
                        $items = $product->select('[id="'.Request::get('pid').'"]', null);

                        //  Delete product and update <parent> fields
                        if ($product->deleteWhere('[id="'.$items['id'].'" ]')) {

                            ProductAdmin::GetChildren($items['slug'], $items['slug'], $items['parent'], '', $status_array, 0);
                            if (is_array(ProductAdmin::$product_array))
                            {
                                foreach (ProductAdmin::$product_array as $item) {
                                    if ($product->deleteWhere('[id="'.$item['id'].'" ]')) {
                                        if (File::delete(STORAGE . DS . 'product' . DS . $item['id'] . '.product.txt'))
                                        {
                                            File::delete(ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS . $item['id'] . '.jpg');
                                            File::delete(ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS . 'thumbnail' . DS . $item['id'] . '.jpg');
                                            File::delete(ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS . $item['id'] . '.150.jpg');
                                            File::delete(ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS . 'thumbnail' . DS . $item['id'] . '.150.jpg');
                                            File::delete(ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS . $item['id'] . '.151.jpg');
                                            File::delete(ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS . 'thumbnail' . DS . $item['id'] . '.151.jpg');

                                            Notification::set('success', __('Product <i>:product</i> deleted', 'product', array(':product' => Html::toText($items['title']))));
                                        }
                                        else
                                        {
                                            Notification::set('errors', 'Not Deleted!'.$items['id']);
                                        }
                                    }
                                }
                            }
                        }

                        // Run delete extra actions
                        Action::run('admin_product_action_delete');

                        // Redirect
                        Request::redirect('index.php?id=product');

                    } else { die('csrf detected! Delete'); }

                    break;

            }

        } else {
            // Index action
            // -------------------------------------

            // Init vars
            ProductAdmin::GetTree($status_array);

            // Display view
            View::factory('product/views/backend/index')
                ->assign('items', ProductAdmin::$product_array)
                ->assign('site_url', ProductAdmin::$opt['site_url'])
                ->display();
        }

    }

    private static function GetTree($status_array)
    {
        $count = 0;

        // Get product
        $product_list = ProductAdmin::$product->select('[parent=""]', 'all', null, array('slug', 'name', 'status', 'parent', 'type'), 'name');

        // Loop
        foreach ($product_list as $item) {

            ProductAdmin::$product_array[$count]['id']      = $item['id'];
            ProductAdmin::$product_array[$count]['name']    = $item['name'];
            ProductAdmin::$product_array[$count]['class']   = $item['parent'];
            ProductAdmin::$product_array[$count]['status']  = $status_array[$item['status']];
            ProductAdmin::$product_array[$count]['slug']    = $item['slug'];
            ProductAdmin::$product_array[$count]['url']     = $item['slug'];
            ProductAdmin::$product_array[$count]['type']    = $item['type'];
            ProductAdmin::$product_array[$count]['dash']    = '';

            $count++;
            $count_new = ProductAdmin::GetChildren($item['slug'], $item['slug'], $item['parent'], '', $status_array, $count);
            if ($count != $count_new)
            {
                $count--;
                ProductAdmin::$product_array[$count]['parent'] = '1';
                $count = $count_new;
            }
        }
    }
    /**
     * Building tree
     */
    private static function GetChildren($slug, $url, $parent, $dash, $status, $count, $parent = '')
    {
        // Get product
        $product_list = ProductAdmin::$product->select('[parent="'.$slug.'"]', 'all', null, array('slug', 'name', 'status', 'parent', 'type'), 'name');

        // Loop
        foreach ($product_list as $item) {
            if ($parent != '')
                $parent = $parent.' ';

            ProductAdmin::$product_array[$count]['id']      = $item['id'];
            ProductAdmin::$product_array[$count]['name']    = $item['name'];
            ProductAdmin::$product_array[$count]['class']   = $parent.$item['parent'];
            ProductAdmin::$product_array[$count]['status']  = $status[$item['status']];
            ProductAdmin::$product_array[$count]['slug']    = $item['slug'];
            ProductAdmin::$product_array[$count]['url']     = $url.'/'.$item['slug'];
            ProductAdmin::$product_array[$count]['type']    = $item['type'];
            ProductAdmin::$product_array[$count]['dash']    = $dash.Html::nbsp(1);

            $count++;
            $count_new = ProductAdmin::GetChildren($item['slug'],$url.'/'.$item['slug'], $item['parent'], $dash.Html::nbsp(1), $status, $count, $parent.$item['parent']);
            if ($count != $count_new)
            {
                $count--;
                ProductAdmin::$product_array[$count]['parent'] = '1';
                $count = $count_new;
            }
        }
        return $count;
    }

    /**
     * Form Component Save
     */
    public static function formComponentSave() {
        if (Request::post('product_component_save')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update('product_template', Request::post('product_form_template'));
                Request::redirect('index.php?id=themes');
            }
        }
    }


    /**
     * Form Component
     */
    public static function formComponent() {

        $_templates = Themes::getTemplates();
        foreach($_templates as $template) {
            $templates[basename($template, '.template.php')] = basename($template, '.template.php');
        }

        echo (
            Form::open().
                Form::hidden('csrf', Security::token()).
                Form::label('product_form_template', __('Product template', 'product')).
                Form::select('product_form_template', $templates, Option::get('product_template')).
                Html::br().
                Form::submit('product_component_save', __('Save', 'product'), array('class' => 'btn')).
                Form::close()
        );
    }

    private static function UploadImg($img, $h, $w, $uid)
    {
        if($img['type'] == 'image/jpeg' ||
            $img['type'] == 'image/png' ||
            $img['type'] == 'image/gif') {

            $img  = Image::factory($img['tmp_name']);
            $file['wmax']   = (int)Option::get('product_wmax');
            $file['hmax']   = (int)Option::get('product_hmax');
            $file['w']      = (int)$w;
            $file['h']      = (int)$h;
            ProductAdmin::ReSize($img, ProductAdmin::$opt['dir'], $uid.'.jpg', $file);
        }
        else if ($img['size'] == 0)
        {

        }
        else
        {
            exit('exit');
        }
    }

    public static function ReSize($img, $folder, $name, $opt)
    {
        $wmax   = (int)$opt['wmax'];
        $hmax   = (int)$opt['hmax'];
        $width  = (int)$opt['w'];
        $height = (int)$opt['h'];

        if ($img->width > $wmax or $img->height > $hmax) {
            if ($img->height > $img->width) {
                $img->resize($wmax, $hmax, Image::HEIGHT);
            } else {
                $img->resize($wmax, $hmax, Image::WIDTH);
            }
        }
        $img->save($folder.$name);

        if ($img->width > $width or $img->height > $height) {
            if ($img->height > $img->width) {
                $img->resize($width, $height, Image::HEIGHT);
            } else {
                $img->resize($width, $height, Image::WIDTH);
            }
        }
        $img->save($folder.'thumbnail'.DS.$name);

        $im = imageCreatetrueColor($width, $height) or die ("Ошибка при создании изображения");
        $red = imagecolorallocate($im, 255, 255, 255);
        imagefill($im, 0, 0, $red);
        $image = imagecreatefromjpeg($folder.'thumbnail'.DS.$name);
        imageCopyResampled($im, $image, 0, 0, 0, 0, $img->width, $img->height, $img->width, $img->height);
        imagejpeg($im, $folder.'thumbnail'.DS.$name, 100);
    }

    private static function reArrayFiles(&$file_post) {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        return $file_ary;
    }

}