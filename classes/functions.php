<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

function getvar($var, $default='')
{
	if (isset($_POST[$var])) { return trim(urldecode($_POST[$var])); }
	elseif (isset($_GET[$var])) { return trim($_GET[$var]); }
	else { return trim($default); }
}

function thisURL()
{
	return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
}

/*
function thisURL()
{
	$temp = debug_backtrace();
	return APP_URL.substr($temp[0]['file'],strlen(APP_PATH));
	return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
}

function readFilesInDir($dir,$ext='*')
{
	$list = array();
	if (!is_dir($dir))
	{
		throw new GenException(GEN_ERR_FILE,1,$dir);
		return false;
	}
	if (!($dh = opendir($dir)))
	{
		throw new GenException(GEN_ERR_FILE,5,$dir);
		return false;
	}

	while (($file = readdir($dh)) !== false)
		if ($ext=='*' || substr($file,-1*(strlen($ext)+1))==".$ext")
			$list[] = $file;
	closedir($dh);
	return $list;
}

function dumpvars()
{
	$cont = '';
	foreach (array_keys($_POST) as $k) $cont .= "$k=(".$_POST[$k].")<br/>";
	foreach (array_keys($_GET) as $k) $cont .= "$k=(".$_GET[$k].")<br/>";
	return $cont;
}
*/
?>