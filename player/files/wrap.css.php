<?php
	header('Content-type: text/css');
	$path = "../images/wrap/";
?>

#wrapviewport
{
	position:absolute;
	border:1px ridge #900;
	display:inline-block;
	top:40px;
	left:10px;
	margin:10px;
	padding:0px;
	float:left;
	background-image:url('<?php echo $path;?>screen.png');
	z-index:2;
}

#wrapmouse
{
	position:absolute;
	display:inline-block;
	z-index:10;
	background-color:transparent;
}

#inmsg
{
	margin:-19px 0px 0px 27px;
	padding:3px;
	border:1px solid black;
	border-radius:3px;
	background-color:white;
	text-align:center;
	vertical-align:top;
	width:100px;
	height:25px;
	box-shadow:3px 3px 3px #888888;
	font-size:20px;
	font-weight:bold;
}

#clock
{
	margin:2px;
	padding:1px 10px;
	font-size:20px;
	text-align:center;
	height:20px;
	background-color:white;
	border:1px solid black;
	border-radius:5px;
	display:inline-block;
	float:left;
}