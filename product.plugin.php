<?php

    /**
     *  Product plugin
     *
     *  @package Monstra
     *  @subpackage Plugins
     *  @copyright Copyright (C) KANekT @ http://kanekt.ru
     *  @license http://creativecommons.org/licenses/by-nc-sa/4.0/deed.ru
     *  Creative Commons Attribution-NonCommercial 4.0
     *  Donate Web Money Z104136428007 R346491122688
     *  Yandex Money 410011782214621
     *
     */

    // Register plugin
    Plugin::register( __FILE__,
                    __('Products', 'product'),
                    __('Products plugin for Monstra', 'product'),
                    '1.2.6',
                    'KANekT',
                    'http://kanekt.ru/',
                    'product');


    // Load Product Admin for Editor and Admin
    if (Session::exists('user_role') && in_array(Session::get('user_role'), array('admin', 'editor'))) {        
        
        Plugin::admin('product');

    }

    Action::add('frontend_pre_render','Product::getAjax');

    Stylesheet::add('plugins/product/css/style.css', 'frontend', 18);

    Stylesheet::add('plugins/product/css/bootstrap-fileupload.min.css', 'backend', 18);

    Javascript::add('plugins/product/js/validate.js', 'frontend', 11);
    Javascript::add('plugins/product/js/storage.js', 'frontend', 19);
    Javascript::add('plugins/product/js/'.Option::get('language').'.lang.js', 'frontend', 20);
    Javascript::add('plugins/product/js/cart.js', 'frontend', 21);

    Javascript::add('plugins/product/js/storage.js', 'backend');
    Javascript::add('plugins/product/js/tree.js', 'backend', 20);
    Javascript::add('plugins/product/js/bootstrap-fileupload.min.js', 'backend', 18);
    Javascript::add('plugins/product/js/bootstrap-fileupload-setting.js', 'backend', 19);

Shortcode::add('product', 'Product::_shortcode');
    /**
     * Product class
     */
    class Product extends Frontend {
        /**
         * Current product data
         *
         * @var object
         */
        public static $product = null;

        /**
         * Product tables
         *
         * @var object
         */
        public static $products = null;
        
        /**
         * Current product data
         *
         * @var object
         */
        public static $meta = null;

        public static $tree = null;

        public static $theme = 'default';

        public static $opt = null;

        public static $h1 = '';

        /**
         * Product main function
         */ 
        public static function main(){
            Product::$products = new Table('product');
            Product::$opt["page"] = 1;
            Product::$opt["site_url"] =  Option::get('siteurl');
            Product::$opt["dir"] = ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS;
            Product::$opt["url"] = Product::$opt["site_url"] . 'public/uploads/product/';
            Product::$opt["uri"] = Uri::segments();
            Product::$product  = Product::Loader();
            Product::$h1 = __('Product', 'product');
            Product::$opt['langT'] = __('Product title', 'product');
            Product::$opt['langD'] = __('Product description', 'product');
            Product::$opt['langK'] = __('Product keywords', 'product');
            Product::$opt['langB'] = __('Product buy', 'product');
        }

        /**
         * Shortcode product
         */
        public static function _shortcode($attributes) {
            extract($attributes);
            $slug = (isset($slug)) ? (string)$slug : '';
            if (isset($list)) {
                Product::$products = new Table('product');
                Product::GetTree($slug,'','','',0);
                switch ($list) {
                    case 'menu':
                        Product::GetListMenu();
                        break;
                    case 'cart':
                        Product::GetCart();
                        break;
                }
            }
        }

        /**
         * Page loader
         *
         * @param boolean $return_data data
         * @return array
         */
        public static function Loader() {
            $uri = Uri::segments();
            if (count($uri) > 0 && $uri[0] == 'product')
            {
                if (isset($uri[1]) && $uri[1] == 'order')
                {
                    Product::$opt["pid"] = 'order';
                }
                else if (isset($uri[1]) && $uri[1] == 'confirm')
                {
                    Product::$opt["pid"] = 'confirm';
                }
                else
                {
                    return Product::lowLoader($uri);
                }
            }
            return true;
        }

        /**
         * Load current page
         *
         * @global string $default default page
         * @param array $data uri
         * @return string
         */
        public static function lowLoader($data) {

            $item = null;
            Product::$opt["pid"] = '0';
            Product::$opt["id"] = '0';

            // If data count 2 then it has Parent/Child
            $cnt = count($data);
            $cnt--;
            if ((int)$data[$cnt] > 0)
            {
                Product::$opt["page"] = (int)$data[$cnt];
                //$cnt--;
            }

            $slug = $data[$cnt];
            if ($cnt == 0)
            {
                $item = Product::$products->select('[parent=""][status="published"]', 'all');
                Product::$opt["parent"] = '';
            }
            else
            {
                // If exists parent file
                if (count(Product::$products->select('[slug="'.$slug.'"][status="published"]')) !== 0) {

                    Product::$meta = Product::$products->select('[slug="'.$slug.'"][status="published"]', null);
                    // Get child file and get parent page name
                    $item = Product::$products->select('[parent="'.$slug.'"][status="published"]');

                    $parent = '';
                    // If child page parent is not empty then get his parent
                    if (count($item) > 0) {
                        for($x=1; $x<=$cnt; $x++)
                        {
                            $parent .= $data[$x].'/';
                        }
                        Product::$opt["parent"] = $parent;
                    } else {
                        for($x=1; $x<$cnt; $x++)
                        {
                            $parent .= $data[$x];
                            if ($x != $cnt-1)
                                 $parent .= '/';
                        }
                        Product::$opt["parent"] = $parent;

                        $item = Product::$products->select('[slug="'.$slug.'"][status="published"]', null);
                        Product::$meta = Product::$products->select('[slug="'.$item['parent'].'"][status="published"]', null);
                        if ($item['status'] == 'published') {
                            if ($item['type'] == '1') {
                                Product::$opt["pid"] = $item["id"];
                            }
                            else
                            {
                                $item = null;
                            }
                        } else {
                            Product::$opt["pid"] = 'error404';
                            Response::status(404);
                        }
                    }

                } else {
                    Product::$opt["pid"] = 'error404';
                    Response::status(404);
                }
            }

            // Return page name/id to load
            return $item;
        }
        
        /**
         * Set Product title
         */
        public static function title(){
            if (isset(Product::$meta['title']) && Product::$meta['title'] != '')
            {
                return Product::$meta['title'];
            }
            else if (Product::$meta['name'] != '')
            {
                return  Product::$opt['langB'].Product::$meta['name'];
            }
            else
            {
                return Product::$opt['langT'];
            }
        }

        /**
         * Set Product description
         */
        public static function description(){
            if (isset(Product::$meta['description']) && Product::$meta['description'] != '')
            {
                return Product::$meta['description'];
            }
            else if (Product::$meta['name'] != '')
            {
                return  Product::$opt['langB'].Product::$meta['name'];
            }
            else
            {
                return  Product::$opt['langD'];
            }
        }

        /**
         * Set Product keywords
         */
        public static function keywords(){
            if (isset(Product::$meta['keywords']) && Product::$meta['keywords'] != '')
            {
                return Product::$meta['keywords'];
            }
            else if (Product::$meta['name'] != '')
            {
                return  Product::$opt['langB'].Product::$meta['name'];
            }
            else
            {
                return  Product::$opt['langK'];
            }
        }

        /**
         * Set Product content
         */
        public static function content() {

            switch (Product::$opt["pid"]) {

                case "order":
                    return View::factory('product/views/frontend/order')
                        ->assign('opt', Product::$opt)
                        ->render();
                    break;

                case "confirm":
                    return View::factory('product/views/frontend/confirm')
                        ->render();
                    break;

                case "error404":
                    return Text::toHtml(File::getContent(STORAGE . DS . 'pages' . DS . '1.page.txt'));
                break;

                case "0":
                    // Display view
                    return View::factory('product/views/frontend/index')
                        ->assign('items', Arr::subvalSort(Product::$product, 'name'))
                        ->assign('parent', Product::$opt["parent"])
                        ->assign('opt', Product::$opt)
                        ->render();

                    break;

                default:

                    return View::factory('product/views/frontend/item')
                        ->assign('item', Product::$product)
                        ->assign('opt', Product::$opt)
                        ->render();
                break;
            }
        }

        public static function GetBreadCrumb($cnt, $return = '')
        {
            $uri = '';

            for($i=0;$i<$cnt;$i++)
            {
                $uri .= '/'.Product::$opt["uri"][$i];
            }

            $cnt--;
            $slug = Product::$opt["uri"][$cnt];

            $item = Product::$products->select('[slug="'.$slug.'"]', null);
            if ($return == ''){
                Product::$h1 = $item['name'];
                $return = ' - '.$item['name'];
            }
            else{
                $return = ' - <a href="'.$uri.'">'.$item['name'].'</a>';
            }

            $item = Product::$products->select('[slug="'.$item['parent'].'"]', null);
            if (count($item) > 0){
                $return = Product::GetBreadCrumb($cnt, $return).$return;
            }

            return $return;

            //Product::GetTree('','','','',0);
        }

        /**
         * Building tree
         */
        private static function GetTree($slug, $url, $parent, $dash, $count)
        {
            // Get product
            $product_list = Product::$products->select('[parent="'.$slug.'"]', 'all', null, array('slug', 'title', 'parent'), 'name');

            // Loop
            foreach ($product_list as $item) {
                if ($parent != '')
                    $parent = $parent.' ';

                Product::$tree[$count]['id']      = $item['id'];
                Product::$tree[$count]['title']   = $item['title'];
                Product::$tree[$count]['class']   = $parent.$item['parent'];
                Product::$tree[$count]['slug']    = $item['slug'];
                Product::$tree[$count]['url']     = $url.'/'.$item['slug'];
                Product::$tree[$count]['dash']    = $dash.Html::nbsp(1);

                $count++;
                $count_new = Product::GetTree($item['slug'],$url.'/'.$item['slug'], $item['parent'], $dash.Html::nbsp(1), $count);
                if ($count != $count_new)
                {
                    $count--;
                    Product::$tree[$count]['parent'] = '1';
                    Product::$tree[$count]['close'] = $count_new;
                    $count = $count_new;
                }
            }
            return $count;
        }

        /**
         * Set Product template
         */
        public static function template() {
            return Option::get('product_template');
        }

        private static function GetListMenu()
        {
            $return = View::factory('product/views/frontend/menu')
                ->assign('product', Product::$tree)
                //->assign('opt', Product::$opt)
                ->render();

            echo $return;
        }

        public static function Cart()
        {
            Product::GetCart();
        }

        private static function GetCart()
        {
            $return = View::factory('product/views/frontend/cart')
                ->render();

            echo $return;
        }

        public static function getAjax()
        {
            if (Security::check(Request::post('token'))) {
                if (Request::post('order') == 'add') {
                    $product = new Table('product');
                    $orders = new Table('product_orders');
                    $data = array(
                        'data'          => time(),
                        'items'         => (string)Request::post('items'),
                        'comp'          => (string)Request::post('comp'),
                        'phone'         => (string)Request::post('phone'),
                        'email'         => (string)Request::post('email'),
                        'comm'          => (string)Request::post('comm'),
                        'fio'           => (string)Request::post('fio')
                    );

                    if ($orders->insert($data)) {

                        $records = json_decode($data['items']);
                        $items = array();
                        foreach($records as $item)
                        {
                            $record = $product->select('[id='.$item[0].']', 'all', null, array('name', 'price', 'type'), 'name');
                            $record[0]['amount'] = (int)$item[1];
                            $items[] = $record[0];
                        }

                        $sys_email = Option::get('system_email');

                        $mail = new PHPMailer();
                        $mail->CharSet = 'utf-8';
                        $mail->ContentType = 'text/html';
                        $mail->SetFrom($sys_email);
                        $mail->AddReplyTo($sys_email);
                        $mail->AddAddress($data['email']);
                        $mail->Subject = Option::get('sitename');
                        $mail->MsgHTML(View::factory('product/views/emails/client_mail')
                            ->assign('items', $items)
                            ->assign('site', Option::get('sitename'))
                            ->assign('url', Option::get('siteurl'))
                            ->render());
                        $mail->Send();

                        $mail = new PHPMailer();
                        $mail->CharSet = 'utf-8';
                        $mail->ContentType = 'text/html';
                        $mail->SetFrom($sys_email);
                        $mail->AddReplyTo($sys_email);
                        $mail->AddAddress($sys_email);
                        $mail->Subject = Option::get('sitename');
                        $mail->MsgHTML(View::factory('product/views/emails/admin_mail')
                            ->assign('order', $data)
                            ->assign('items', $items)
                            ->assign('site', Option::get('sitename'))
                            ->assign('url', Option::get('siteurl'))
                            ->render());
                        $mail->Send();
                    }
                    exit();
                }
                if (Request::post('order') == 'save') {
                    $orders = new Table('product_orders');
                    $orders->updateWhere('[id="'.(int)Request::post('id').'"]', array('items' => (string)Request::post('items')));
                    exit();
                }
                if (Request::post('item') == 'del') {
                    File::delete(ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS . Request::post('id') . Request::post('dir') . '.jpg');
                    File::delete(ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS . 'thumbnail' . DS  . Request::post('id') . Request::post('dir') . '.jpg');
                    exit();
                }
                exit('no action');
            }
            else
            {
                //exit('token is not valid');
            }
        }
    }
