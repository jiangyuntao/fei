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
        if (is_array($this->_router)) {
        } else {
            if (strpos($this->_router, '.') !== false) {
                list($class, $action) = explode('.', $this->_router);
            } else {
                $class = $this->_router;
                $action = 'index';
            }

            $classParts = explode('/', $class);
            end($classParts);
            $key = key($classParts);
            $classParts[$key] = ucfirst($classParts[$key]);
            $classFile = APP_DIR . '/controller/' . implode('/', $classParts) . 'Controller.php';
            $className = implode('', array_map('ucfirst', explode('/', $class))) . 'Controller';
            $actionName = $action . 'Action';
        }

        if (!file_exists($classFile)) {
            throw new Exception('no controller file "' . $classFile . '" was found in app directory.');
        }
        require $classFile;

        if (!class_exists($className)) {
            throw new Exception('no controller class "' . $className . '" was found.');
        }

        $obj = new $className;

        if (!method_exists($obj, $actionName)) {
            throw new Exception('no method "' . $actionName . '" of class "' . $className . '" was found.');
        }

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
