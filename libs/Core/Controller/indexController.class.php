<?php
class indexController extends basicController{
    function index(){
        //$this->view->assign('str', 'fuck');
        //$this->view->display('index.html');
        //$arr = ['<foo>' , "'bar'" , '"baz"' , '&blong&' ,  "\xc3\xa9"];
        //$this->Json($arr);
        //var_dump($_GET);
        $m = new indexModel();
        $data = $m->index();
        $this->view->assign('data', $data);
        $this->view->display('index.html');
    }
    function user(){
        $m = new indexModel();
        $data = $m->user();
        //var_dump($data);
        $this->view->assign("user", $data);
        $this->view->display('user.html');
    }
}