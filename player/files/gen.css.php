<?php
	header('Content-type: text/css');
	require_once('styles.css.php');
?>
body
{
	font-family:<?php echo $fontfamily;?>;
	font-size:<?php echo $fontsize;?>;
	color:<?php echo $fontcolor;?>;
	background:<?php echo $background;?>;
	margin:<?php echo $bar_height+10;?>px 0px 0px 0px;
	padding:0px;
}

form
{
	margin:0px;
	padding:0px;
}

p
{
	font-size:14px;
	margin:0px 20px 5px 20px;
	text-align:left;
}

img
{
	border: 0px;
}

select
{
	border:1px solid #ddd;
	background:#ffffcc;
	font-size:12px;
	margin:0px;
	padding:0px;
	text-align:left;
	vertical-align:top;
}

div.message
{
	margin:50px 10px;
	padding:2px 0px 2px 10px;
	top:0px;
	left:0px;
	font-size:14px;
	background:#ffffcc;
	border-radius: 5px;
	border:1px solid #333;
	position:fixed;
	width:98%;
}

div.message div.snippet
{
	margin: 1px 3px 1px 0px;
	padding:0px 5px 0px 0px;
	display:inline-block;
	border-right: 2px solid #aaa;
}

input.button
{
	border:1px solid #ddd;
	background:#F5B800;
	font-size:13px;
	padding:1px 5px;
	border-radius:5px;
}

input.button:hover
{
	background:#FFD65C;
	border:1px solid #F5B800;
}