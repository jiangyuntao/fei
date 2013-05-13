<?php
require 'fei/Fei.php';
$app = Fei::getInstance();
$app->route(array(
    '/' => 'index',
    '/product/<name>/<page:number>' => 'admin/product.show',
));
$app->run();
