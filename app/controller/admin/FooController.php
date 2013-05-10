<?php
class AdminFooController extends Controller {
    public function barAction() {
        echo 'app/controller/admin/FooController.php';
        dump($_GET);
    }
}
