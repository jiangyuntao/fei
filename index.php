<?php
require 'fei/Fei.php';
$app = new Fei();
$app->route(array(
    '/' => 'index.index',
    '/product/{string:name}' => 'admin/product.show',
));
$app->start();
