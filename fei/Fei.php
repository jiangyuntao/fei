<?php
/**
 * Fei Micro Framework
 *
 * @author Thor Jiang <jiangyuntao@gmail.com>
 * @license The MIT License
 */

class Fei {
    protected $_registry = array();
    protected $_router = null;

    public function __construct($appDir = './app') {
        defined('APP_DIR') || define('APP_DIR', realpath($appDir));
        defined('FEI_DIR') || define('FEI_DIR', realpath(dirname(__FILE__)));

        spl_autoload_register(array($this, 'autoload'));
    }

    public function route($routes = array()) {
        $route = new Route($routes);
        $this->_router = $route->dispatch();
        var_dump($this->_router);
    }

    public function start() {
    }

    public function autoload($className = '') {
        $classFile = FEI_DIR . '/' . $className . '.php';
        if (file_exists($classFile)) {
            require $classFile;
        } else {
            throw new Exception('no file was found in fei directory.');
        }
    }

    public function register($classPath = '', $params = array()) {
    }
}
