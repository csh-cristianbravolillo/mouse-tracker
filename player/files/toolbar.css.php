<?php
	header('Content-type: text/css');
	require_once('styles.css.php');
?>
div.toolbar
{
	position:fixed;
	top:0px;
	left:0px;
	background:<?php echo $bar_background;?>;
	border-bottom:2px solid <?php echo $bar_bordercolor;?>;
	margin-bottom:10px;
	height:<?php echo $bar_height;?>px;
	width:100%;
}

div.toolbar div.action
{
	font-family:<?php echo $bar_action_fontfamily;?>;
	font-size:<?php echo $bar_action_fontsize;?>;
	color:<?php echo $bar_action_fontcolor;?>;
	background:<?php echo $bar_action_background;?>;
	text-align:center;
	float:left;
	border:1px solid <?php echo $bar_action_bordercolor;?>;
	padding:1px 3px;
	margin:0px 5px;
	width:<?php echo $bar_action_width;?>;
	cursor:pointer;
	border-bottom-left-radius:10px;
	border-bottom-right-radius:10px;
}

div.toolbar div.action:hover
{
	border:1px solid <?php echo $bar_action_bordercolorhover;?>;
}

div.toolbar div.info
{
	font-family:<?php echo $bar_info_fontfamily;?>;
	font-size:<?php echo $bar_info_fontsize;?>;
	color:<?php echo $bar_info_fontcolor;?>;
	width:<?php echo $bar_info_width;?>;
	text-align:left;
	float:left;
	line-height:15px;
	border:1px solid black;
	padding:3px 10px;
	background-color:white;
	border-radius:8px;
	margin:3px 5px 0px 5px;
}

div.toolbar div.appid
{
	font-family:<?php echo $bar_info_fontfamily;?>;
	font-size:<?php echo $bar_info_fontsize;?>;
	color:<?php echo $bar_info_fontcolor;?>;
	text-align:right;
	vertical-align:top;
	float:right;
	margin-right:10px;
}
