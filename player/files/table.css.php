<?php
	header('Content-type: text/css');
	require_once('styles.css.php');
?>
table.simple
{
	margin:10px auto;
}

table.simple tr th
{
	font-weight:bold;
	color:<?php echo $tab_fontcolor;?>;
	text-align:center;
	border-bottom:2px solid black;
	font-size:12px;
	border-right:1px solid #ccc;
}

table.simple tr th.header,div.microheader
{
	color:<?php echo $tab_fontcolorheader;?>;
	cursor:pointer;
	border-top:1px solid transparent;
	border-left:1px solid transparent;
	border-right:1px solid transparent;
}

table.simple tr th.header:hover,div.microheader:hover
{
	color:<?php echo $tab_fontcolorheaderhover;?>;
	background:<?php echo $tab_highlightsoft;?>;
	border-top:1px solid <?php echo $tab_headerborder;?>;
	border-left:1px solid <?php echo $tab_headerborder;?>;
	border-right:1px solid <?php echo $tab_headerborder;?>;
}

div.microheader
{ display:inline-block; }

table.simple tr.hl:hover
{
	background:<?php echo $tab_highlightsoft;?>;
}

table.simple tr td
{
	color:<?php echo $tab_fontcolor;?>;
	text-align:center;
	border-bottom:1px solid #ddd;
	font-size:12px;
	padding-left:10px;
	padding-right:10px;
	vertical-align:top;
}

table.simple tr td.caption
{
	font-style:italic;
	font-size:18px;
	text-align:center;
	padding-bottom:10px;
}

table.simple tr.selected
{
	font-style:italic;
	border-bottom:1px solid #ddd;
	background:<?php echo $tab_highlightstrong;?>;
}

table.simple tr td.Events
{
	text-align:left;
	font-size:8px;
}

img.tablink
{
	cursor:pointer;
}


table.simple tr td.wid, table.simple tr td.plat
{ text-align: left; }

table.simple tr td.runs
{
	width: auto;
	text-align:left;
	vertical-align:middle;
	padding:0px;
}
