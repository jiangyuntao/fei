<?php
/**
 * Fei Micro Framework
 *
 * @author Thor Jiang <jiangyuntao@gmail.com>
 * @license The MIT License
 */

class Route {
    private $_request = '';
    private $_routes = array();

    public function __construct($routes = array()) {
        $this->_routes = $routes;
        $this->_request = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
    }

    public function dispatch() {
        if (array_key_exists($this->_request, $this->_routes)) {
            return $this->_routes[$this->_request];
        }

        $tokens = array(
            ':string' => '([a-zA-Z]+)',
            ':number' => '([0-9]+)',
            ':alpha'  => '([a-zA-Z0-9-_]+)',
        );

        foreach ($this->_routes as $pattern => $route) {
            $pattern = strtr($pattern, $tokens);
            if (preg_match('#^/?' . $pattern . '/?$#', $this->_request, $matches)) {
                unset($matches[0]);
                return array(
                    'route' => $route,
                    'params' => $matches,
                );
            }
        }
    }
}
