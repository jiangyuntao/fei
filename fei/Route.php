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
        // 字符串
        ':string' => '([a-zA-Z]+)',
        // 数字
        ':number' => '([0-9]+)',
        // 字符串, 数字, 横线, 下划线
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

        // 遍历所有规则
        foreach ($this->_routes as $pattern => $route) {
            // 该条规则中是否包含正则通配符
            preg_match_all('~<(\w+)(:(\w+))?>~', $pattern, $matches);

            $replace = array();
            // 遍历每一个匹配的通配符
            foreach ($matches[0] as $key => $match) {
                // 未限定通配类型则默认为 :alpha
                if (isset($matches[2][$key])) {
                    $matches[2][$key] = ':alpha';
                }
                $replace[$match] = $this->_tokens[$matches[2][$key]];
            }
            // 把通配符替换为正则
            $pattern = strtr($pattern, $replace);

            // 根据请求匹配路由
            if (preg_match('#^/?' . $pattern . '/?$#', $this->_request, $params)) {
                // 生成相关参数
                array_shift($params);
                foreach ($matches[1] as $key => $val) {
                    $_GET[$val] = $params[$key];
                }

                return $route;
            }
        }
    }
}
