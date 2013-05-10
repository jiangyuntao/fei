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
        // 项目目录
        defined('APP_DIR') || define('APP_DIR', realpath($appDir));

        // 框架目录
        defined('FEI_DIR') || define('FEI_DIR', realpath(dirname(__FILE__)));

        // 自动载入框架内类文件
        spl_autoload_register(array($this, 'autoload'));
    }

    public function route($routes = array()) {
        $route = new Route($routes);
        $this->_router = $route->dispatch();
    }

    public function start() {
        if (!file_exists($this->_router['file'])) {
            if ($this->get('debug')) {
                throw new Exception('no controller file "' . $this->_router['file'] . '" was found in app directory.');
            } else {
                $this->_pageNotFound();
            }
        }
        require $this->_router['file'];

        if (!class_exists($this->_router['class'])) {
            if ($this->get('debug')) {
                throw new Exception('no controller class "' . $this->_router['class'] . '" was found.');
            } else {
                $this->_pageNotFound();
            }
        }

        $obj = new $this->_router['class'];

        if (!method_exists($obj, $this->_router['method'])) {
            if ($this->get('debug')) {
                throw new Exception('no method "' . $this->_router['method'] . '" of class "' . $this->_router['class'] . '" was found.');
            } else {
                $this->_pageNotFound();
            }
        }

        call_user_func(array($obj, $this->_router['method']));
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

    public function set($variable = '', $value = null) {
    }

    public function get($variable = '') {
        return false;
    }

    private function _pageNotFound() {
        header('HTTP/1.0 404 Not Found');
        echo '<h1>404 Not Found</h1>';
        exit;
    }
}

function dump($variable = null) {
    ob_start();
    var_dump($variable);
    $output = ob_get_clean();
    echo '<pre>';
    echo htmlspecialchars($output);
    echo '</pre>';
}
