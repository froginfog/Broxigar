<?php
return array(
    /**
     *   '(^user/(\d+)/(\w+)$)'  =>  'index/user?id:1&name:2'
     *   正则表达式                 =>   '控制器/方法 (? 参数1:匹配值1 & 参数2:匹配值2)'
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