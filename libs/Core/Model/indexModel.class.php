<?php
class indexModel {
    function index(){
        $data = new DB();
        $sql = "select count(*) from user limit 10";
        $res = $data->count($sql);
        return $res;
    }
    
    function user(){
        $data = new DB();
        $id = $data->quote($_GET['id']);
        $name = $data->quote($_GET['name']);
        $sql = "select * from user where id=$id and username=$name";
        $res = $data->getOne($sql);
        return $res;
    }
}
