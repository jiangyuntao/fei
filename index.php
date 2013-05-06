<?php
require 'fei/Fei.php';
$app = new Fei();
$app->route(array(
    '/', function() {
        echo 'hello world!';
    },
));
$app->start();
