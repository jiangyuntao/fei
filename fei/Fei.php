<?php
/**
 * Fei Micro Framework
 *
 * @author Thor Jiang <jiangyuntao@gmail.com>
 * @license The MIT License
 */

class Fei {
    /**
     * @var object the instance of class Fei
     */
    protected static $_instance = null;

    /**
     * @var array configuration container
     */
    protected $_config = array();
    protected $_registry = array();
    protected $_router = null;

    /**
     * Constructor
     */
    public function __construct() {
        // 项目目录
        defined('APP_DIR') || define('APP_DIR', realpath('./app'));

        // 框架目录
        defined('FEI_DIR') || define('FEI_DIR', realpath(dirname(__FILE__)));

        // 自动载入框架内类文件
        spl_autoload_register(array($this, 'autoload'));
    }

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    public function route($routes = array()) {
        $route = new Route($routes);
        $this->_router = $route->dispatch();
    }

    public function run() {
        if (file_exists($this->_router['file'])) {
            require $this->_router['file'];
            if (class_exists($this->_router['class'])) {
                $obj = new $this->_router['class'];
                if (method_exists($obj, $this->_router['method'])) {
                    return call_user_func(array($obj, $this->_router['method']));
                }
            }
        }
        $this->_pageNotFound();
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

    /**
     * Set or get a configuration
     * @param mixed $variable variable name or an array
     * @param mixed $value value of config item
     */
    public function config($variable = '', $value = null) {
        if (func_num_args() == 1) {
            // set
            if (is_array($variable)) {
                return $this->_config = array_merge($this->_config, $variable);
            // get
            } else {
                if (isset($this->_config[$variable])) {
                    return $this->_config[$variable];
                } elseif (strpos($variable, '.') !== false) {
                    $parts = explode('.', $variable);
                    $config = $this->_config;
                    foreach ($parts as $part) {
                        if (isset($config[$part])) {
                            $config = $config[$part];
                        }
                    }
                    return $config;
                } else {
                    return null;
                }
            }
        // set
        } else {
            return $this->_config[$variable] = $value;
        }
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
