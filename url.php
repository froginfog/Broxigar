<?php
return array(
    /**
     *   '(^user/(\d+)/(\w+)$)'  =>  'index/user?id:1&name:2'
     *   正则表达式                 =>   '控制器/方法 (? 参数1:匹配值1 & 参数2:匹配值2)'
     *   模板内的url形式为： {url path="what/who/care" query1="1" query2="2" quety3="100"}，这个url在浏览器内将显示为：what/who/care/1/2/100
    **/
    '(^$)' => 'index/index',
    '(^/admin/procatelist$)' => 'admin/listCate',
    '(^/admin/delcate/(\d+)$)' =>'admin/delCate?id:1',
    '(^/admin/addcate$)' => 'admin/addCate',
    '(^/admin/modifycate$)' => 'admin/modifyCate',
    '(^/admin/catechangeorder$)' => 'admin/cateChangeOrder',
    '(^/admin/prolist$)' => 'admin/proList',
    '(^/admin/delpro$)' => 'admin/delPro',
    '(^/admin/addpro$)' => 'admin/addPro',
    '(^/admin/prochangeorder$)' => 'admin/proChangeOrder',
    '(^/admin/prodetail/(\d+)$)' => 'admin/proDetail?id:1',
);
