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
        ':alpha'  => '([a-zA-Z0-9-_]+)',
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
            preg_match_all('~<(\w+)(:(\w+))?>~', $pattern, $matches);

            $replace = array();
            foreach ($matches[0] as $key => $match) {
                if ($matches[2][$key]) {
                    $matches[2][$key] = ':alpha';
                }
                echo $match;
                $replace[$match] = $this->_tokens[$matches[2][$key]];
            }
            $pattern = strtr($pattern, $replace);

            if (preg_match('#^/?' . $pattern . '/?$#', $this->_request, $matches)) {
                unset($matches[0]);
                var_dump($matches);
            }
        }
    }
}
