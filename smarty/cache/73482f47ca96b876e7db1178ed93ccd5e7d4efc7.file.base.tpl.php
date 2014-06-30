<?php /* Smarty version Smarty-3.1.16, created on 2014-05-02 20:52:30
         compiled from "/home/cbravo/public_html/mt/smarty/templates/base.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1153389785364058eb6c672-95488591%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '73482f47ca96b876e7db1178ed93ccd5e7d4efc7' => 
    array (
      0 => '/home/cbravo/public_html/mt/smarty/templates/base.tpl',
      1 => 1399038264,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1153389785364058eb6c672-95488591',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'doctype' => 0,
    'title' => 0,
    'meta' => 0,
    'scripts' => 0,
    'css' => 0,
    'formaction' => 0,
    'content' => 0,
    'formvars' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.16',
  'unifunc' => 'content_5364058eead5c5_60496635',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5364058eead5c5_60496635')) {function content_5364058eead5c5_60496635($_smarty_tpl) {?><?php echo $_smarty_tpl->tpl_vars['doctype']->value;?>

<html>
<head>
<title><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</title>
<?php echo $_smarty_tpl->tpl_vars['meta']->value;?>
<?php echo $_smarty_tpl->tpl_vars['scripts']->value;?>
<?php echo $_smarty_tpl->tpl_vars['css']->value;?>
</head>

<body><form action='<?php echo $_smarty_tpl->tpl_vars['formaction']->value;?>
' method='post'>
<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

<?php echo $_smarty_tpl->tpl_vars['formvars']->value;?>

</form>
<div id='tip' class='gentip' style='width:200px;'></div></body></html><?php }} ?>
