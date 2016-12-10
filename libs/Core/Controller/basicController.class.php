<?php
class basicController {
    protected $view = null;

    function __construct()
    {
        global $config;
        $this->view = new Smarty();
        $this->view->left_delimiter = $config['SM_LEFT_DELIMITER'];
        $this->view->right_delimiter = $config['SM_RIGHT_DELIMITER'];
        $this->view->setTemplateDir($config['TEMPLATE_DIR']);
        $this->view->setCompileDir($config['COMPILE_DIR']);
        $this->view->setCacheDir($config['CAHCE_DIR']);
        $this->view->caching = $config['CACHEING'];
        $this->view->cache_lifetime = $config['CACHE_LIFETIME'];
    }
    protected function Json($arr){
        header("Content-type: application/json");
        echo json_encode($arr, JSON_HEX_TAG);
    }

    final function defaultPage(){
        $this -> view -> assign('str', 'Hi,There!');
    }

    final function notFound(){
        header('HTTP/1.1 404 Not Found');
        header("status: 404 Not Found");
    }

    /**
     * @param string $url '/admin/index'
     */
    protected function goBack($url=null){
        if(is_null($url)) {
            $url = $_SERVER['HTTP_REFERER'];
            header("location:$url");
        }else{
            global $config;
            $host = $_SERVER['HTTP_HOST'];
            $url = $config['ROOT'].$url;
            header("location:$host$url");
            exit;
        }
    }

    /**
     * 防止csrf 为表单添加token验证
     * 设置token
     * @param string $name token名
     */
    protected function setToken($name=null){
        $tk_name = ($name == null) ? 'token' : $name;
        if(!$_SESSION[$tk_name]){
            $str = substr(md5(uniqid(microtime(), true)), 2, 6);
            $_SESSION[$tk_name] = $str;
        }
    }

    /**
     * 获取token
     * @param string $name token名
     * @return string
     */
    protected function getToken($name=null){
        if($name == null){
            return $_SESSION['token'];
        }else{
            return $_SESSION[$name];
        }
    }
}