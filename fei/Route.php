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
            return $this->_getRouterInfo($this->_routes[$this->_request]);
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

                return $this->_getRouterInfo($route);
            }
        }

        // 如果在请求在路由列表中不存在，则正常解析
        $segments = explode('/', trim($this->_request, '/'));
        $file = APP_DIR . '/controller/';
        $class = '';
        $found = false;
        foreach ($segments as $segment) {
            if (!$found) {
                if (file_exists($file . ucfirst($segment) . 'Controller.php')) {
                    $file .= ucfirst($segment) . 'Controller.php';
                    $found = true;
                } else {
                    $file .= $segment . '/';
                }
                $class .= ucfirst($segment);
                // 使用过的元素移出 $segments 数组
                array_shift($segments);
            }
        }

        // 类名称
        $class .= 'Controller';
        // 类方法
        $method = array_shift($segments) . 'Action';

        // 把余下的 URL 其他部分按对转为参数
        $segmentsCount = count($segments);
        for ($i = 0; $i < $segmentsCount; $i += 2) {
            $_GET[$segments[$i]] = isset($segments[$i + 1]) ? $segments[$i + 1] : null;
        }

        return array(
            'file' => $file,
            'class' => $class,
            'method' => $method,
        );
    }

    protected function _getRouterInfo($router = '') {
        if (strpos($router, '.') !== false) {
            list($classAlias, $method) = explode('.', $router);
        } else {
            $classAlias = $router;
            $method = 'index';
        }

        if (strpos($router, '/') !== false) {
            $classParts = explode('/', $classAlias);
            end($classParts);
            $key = key($classParts);
            $classParts[$key] = ucfirst($classParts[$key]);
            $file = APP_DIR . '/controller/' . implode('/', $classParts) . 'Controller.php';
        } else {
            $file = APP_DIR . '/controller/' . $classAlias . 'Controller.php';
        }

        $class = implode('', array_map('ucfirst', explode('/', $classAlias))) . 'Controller';
        $method = $method . 'Action';

        return array(
            'file' => $file,
            'class' => $class,
            'method' => $method,
        );
    }
}
