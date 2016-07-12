<?php
class basicController {
    protected $view = null;
    protected $url;

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
        echo json_encode($arr, JSON_HEX_TAG);
    }

    final function defaultPage(){
        $this -> view -> assign('str', 'Hi,There!');
    }

    final function notFound(){
        $this -> view -> assign('str', '404哟~');
        $this -> view -> display('404.html');
    }

    protected function goBack(){
        $this->url = $_SERVER['HTTP_REFERER'];
        header("location:$this->url");
    }
}