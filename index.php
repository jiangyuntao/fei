<?php
require 'fei/Fei.php';
$app = new Fei('./app');
$app->route(array(
    '/' => 'index',
    '/product/<name>/<page:number>' => 'admin/product.show',
));
$app->start();
