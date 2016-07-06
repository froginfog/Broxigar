<?php
/* Smarty version 3.1.29, created on 2016-06-29 11:47:41
  from "F:\wwwrootnet\Broxigar\templates\tpl\index.html" */

if ($_smarty_tpl->smarty->ext->_validateCompiled->decodeProperties($_smarty_tpl, array (
  'has_nocache_code' => false,
  'version' => '3.1.29',
  'unifunc' => 'content_577344ddac70d3_84464041',
  'file_dependency' => 
  array (
    '65e0775d5ae89568703babf0036c753e67264b08' => 
    array (
      0 => 'F:\\wwwrootnet\\Broxigar\\templates\\tpl\\index.html',
      1 => 1467171944,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_577344ddac70d3_84464041 ($_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<!--<ul>
<?php
$_from = $_smarty_tpl->tpl_vars['data']->value;
if (!is_array($_from) && !is_object($_from)) {
settype($_from, 'array');
}
$__foreach_user_0_saved_item = isset($_smarty_tpl->tpl_vars['user']) ? $_smarty_tpl->tpl_vars['user'] : false;
$_smarty_tpl->tpl_vars['user'] = new Smarty_Variable();
$_smarty_tpl->tpl_vars['user']->_loop = false;
foreach ($_from as $_smarty_tpl->tpl_vars['user']->value) {
$_smarty_tpl->tpl_vars['user']->_loop = true;
$__foreach_user_0_saved_local_item = $_smarty_tpl->tpl_vars['user'];
?>
    <li><?php echo $_smarty_tpl->tpl_vars['user']->value['id'];?>
&#45;&#45;<?php echo $_smarty_tpl->tpl_vars['user']->value['username'];?>
</li>
<?php
$_smarty_tpl->tpl_vars['user'] = $__foreach_user_0_saved_local_item;
}
if ($__foreach_user_0_saved_item) {
$_smarty_tpl->tpl_vars['user'] = $__foreach_user_0_saved_item;
}
?>
</ul>-->
<?php echo $_smarty_tpl->tpl_vars['data']->value;?>

</body>
</html><?php }
}
