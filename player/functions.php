<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Common functions.
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

function parseTriplet($str)
{
	if (!is_string($str) || strlen($str)==0)
		return false;

	list($t, $action, $content) = explode(':', $str);
	$result = Array();
	$pairs = explode(';',$content);

	if ($pairs[count($pairs)-1]=='')
		array_pop($pairs);

	foreach ($pairs as $pair)
	{
		list($k,$v) = explode('=', $pair);
		$result[$k] = $v;
	}
	$result['t'] = $t;
	$result['action'] = $action;

	if (array_key_exists('x', $result))
	{
		$result['xc'] = $result['x'];
		unset($result['x']);
	}

	if (array_key_exists('y', $result))
	{
		$result['yc'] = $result['y'];
		unset($result['y']);
	}
	return($result);
}

function replaceKey(&$arr, $key1, $key2)
{
	if (!is_array($arr) || !array_key_exists($key1, $arr))
		return false;

	$arr[$key2] = $arr[$key1];
	unset($arr[$key1]);
}

function scale($num) { global $scl; return intval($num*$scl); }

?>