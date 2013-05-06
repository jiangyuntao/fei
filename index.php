<?php
require 'fei/Fei.php';
$app = new Fei();
$app->route(array(
    '/' => 'home',
    '/product/:alpha' => 'admin/product/show',
));
$app->start();
