<?php
require 'fei/Fei.php';
$app = new Fei();
$app->route(array(
    '/' => 'index.index',
    '/product/<name>/<page:number>' => 'admin/product.show',
));
$app->start();
