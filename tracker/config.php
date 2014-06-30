<?php
/**
 * Contains basic functions to perform in a page.
 *
 * Contains very basic functions to perform in a page.
 * @author Cristian Bravo-Lillo
 */


/**
 * This definition is for security purposes.
 */
define('APP_NAME','tracker');
require_once('../common/db.php');


/**
 * Gets variables from environment.
 *
 * @param string $var Variable to be retrieved from GET or POST.
 * @param string $default Default value in case no value is retrieved for the variable. Defaults itself to an empty string ('').
 * @return string
 */
function getvar($var, $default='')
{
	if (isset($_POST[$var])) { return trim($_POST[$var]); }
	elseif (isset($_GET[$var])) { return trim($_GET[$var]); }
	else { return trim($default); }
}

?>