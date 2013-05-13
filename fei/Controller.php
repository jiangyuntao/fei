<?php
/**
 * Fei Micro Framework
 *
 * @author Thor Jiang <jiangyuntao@gmail.com>
 * @license The MIT License
 */

class Controller {
    public $app = null;

    public function __construct() {
        $this->app = Fei::getInstance();
    }

    public function render() {
    }

    public function redirect() {
    }

    public function referer() {
    }

    public function _before() {
    }

    public function _after() {
    }
}
