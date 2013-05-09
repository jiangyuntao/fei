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
    }

    public function start() {
        if (strpos($this->_router, '.') !== false) {
            list($class, $action) = explode('.', $this->_router);
        } else {
            $class = $this->_router;
            $action = 'index';
        }
        $className = implode('', array_map('ucfirst', explode('/', $class))) . 'Controller';
        $classFile = APP_DIR . '/controller/' . $className . '.php';
        $actionName = $action . 'Action';

        dump($classFile);
        if (!file_exists($classFile)) {
            throw new Exception('no controller file was found in app directory.');
        }
        require $classFile;

        $obj = new $className;
        call_user_func(array($obj, $actionName));
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

function dump($variable = null) {
    ob_start();
    var_dump($variable);
    $output = ob_get_clean();
    echo '<pre>';
    echo htmlspecialchars($output);
    echo '</pre>';
}
