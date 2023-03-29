<?php
$db = require_once 'start.php';
//    $create = $db->table('products')->create([
//      'name' => 'Sản phâm2',
//      'content' => 'Nội dung sản phẩm 2',
//      'price'   => 20000
//    ]);
//    $products = $db->table('products')->delete();
$ids = [9,10,11,12];
$product = $db->table('products')->where(function ($query){
          $query->where('id' , 1)
          ->where('price' , 13000)->where(function ($query2){
              $query2->where('id' , 3);
      });
})->where('id' , 4)->where(function ($query){
    $query->where('id' , 5);
})->get();
echo "<pre>";
print_r($product);
echo "</pre>";