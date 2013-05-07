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

    private $_tokens = array(
        ':string' => '([a-zA-Z]+)',
        ':number' => '([0-9]+)',
        '<\w+:alpha>'  => '([a-zA-Z0-9-_]+)',
    );

    public function __construct($routes = array()) {
        $this->_routes = $routes;
        $this->_request = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
    }

    public function dispatch() {
        // 路由中不包含通配符，直接匹配
        if (array_key_exists($this->_request, $this->_routes)) {
            return $this->_routes[$this->_request];
        }

        // 路由中包含通配符，正则匹配
        foreach ($this->_routes as $pattern => $route) {
            $pattern = strtr($pattern, $this->_tokens);
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
