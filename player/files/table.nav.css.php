<?php
	header('Content-type: text/css');
	require_once('styles.css.php');
?>
div.nav
{
	font-weight:bold;
	cursor:pointer;
	padding:3px;
	border:1px solid transparent;
}

div.nav:hover,div.order_asc:hover,div.order_desc:hover
{
	background-color:<?php echo $tab_highlightsoft;?>;
	border:1px solid <?php echo $tab_headerborder;?>;
}

div.order_asc,div.order_desc
{
	display:inline-block;
	width:10px;
	height:100%;
	margin:0px auto 0px 0px;
	border:1px solid transparent;
	cursor:pointer;
	background-position:bottom;
	background-repeat:no-repeat;
}

div.order_asc
{ background-image:url('../images/actions/order-asc.png'); }

div.order_desc
{ background-image:url('../images/actions/order-desc.png'); }
