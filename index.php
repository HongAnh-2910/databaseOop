<?php
$db = require_once 'connect.php';
//    $create = $db->table('products')->create([
//      'name' => 'Sản phâm2',
//      'content' => 'Nội dung sản phẩm 2',
//      'price'   => 20000
//    ]);
//    $products = $db->table('products')->delete();
$ids = [9,10,11,12];
$product = $db->where('id','<>',10)->table('products')->get();
echo "<pre>";
print_r($product);
echo "</pre>";