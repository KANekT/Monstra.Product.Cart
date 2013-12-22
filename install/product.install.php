<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

    // Add New Options
    Option::add('product_template', 'index');
    Option::add('product_limit', 7);
    Option::add('product_limit_admin', 10);
    Option::add('product_w', 165);
    Option::add('product_h', 100);
    Option::add('product_wmax', 900);
    Option::add('product_hmax', 800);
    Option::add('product_resize', 'crop');
    // Add tables

Table::create('product', array('name', 'title', 'slug', 'parent', 'robots_index', 'robots_follow', 'status', 'description', 'keywords', 'type', 'price'));

//Add name, type, price
Table::create('product_orders', array('data', 'status', 'items', 'comp', 'phone', 'email', 'fio', 'comm'));

// Add directory for content
$dir = ROOT . DS . 'storage' . DS . 'product' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);


// Add directory for content
$dir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);

File::copy(ROOT . DS . 'plugins' . DS . 'product'. DS . 'img' . DS .'noimage.jpg' , $dir.'no_item.jpg');
File::copy(ROOT . DS . 'plugins' . DS . 'product'. DS . 'img' . DS .'small-photo-01.jpg' , $dir.'small-photo-01.jpg');
File::copy(ROOT . DS . 'plugins' . DS . 'product'. DS . 'img' . DS .'small-photo-02.jpg' , $dir.'small-photo-02.jpg');
File::copy(ROOT . DS . 'plugins' . DS . 'product'. DS . 'img' . DS .'small-photo-03.jpg' , $dir.'small-photo-03.jpg');

$dir = $dir . 'thumbnail' . DS;
if(!is_dir($dir)) mkdir($dir, 0755);