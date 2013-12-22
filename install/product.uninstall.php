<?php defined('MONSTRA_ACCESS') or die('No direct script access.');

// Delete Options
Option::delete('product_template');
Option::delete('product_limit');
Option::delete('product_limit_admin');
Option::delete('product_w');
Option::delete('product_h');
Option::delete('product_wmax');
Option::delete('product_hmax');
Option::delete('product_resize');

Table::drop('product');
Table::drop('product_orders');

function RemoveDir($dir) {
	if ($objs = glob($dir."/*")) {
		foreach($objs as $obj) {
			is_dir($obj) ? RemoveDir($obj) : unlink($obj);
		}
	}
	rmdir($dir);
}

RemoveDir(ROOT . DS . 'public' . DS . 'uploads' . DS . 'product' . DS);
RemoveDir(ROOT . DS . 'storage' . DS . 'product');
