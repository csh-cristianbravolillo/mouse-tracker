<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");
/**
 * Contains class CompactDB.
 *
 * @author Cristian Bravo-Lillo
 * @version 0.1
 */

/**
 * Class CompactDB: Very basic db class.
 */
class CompactDB
{
	private $link = null;

	/**
	 * Constructor.
	 *
	 * @param string $dbhost Hostname to connect to. By default, 'localhost'.
	 * @param string $dbname Name of the databse to connect to.
	 * @param string $dbuser Username to connect to the database.
	 * @param string $dbpass Password to connecto to the database.
	 * @return CompactDB | false
	 */
	function __construct($dbhost='localhost', $dbname, $dbuser, $dbpass)
	{
		if (!($this->link = mysql_connect($dbhost,$dbuser,$dbpass)))
			return false;

		if (!mysql_select_db($dbname,$this->link))
			return false;
	}

	private function makeQuery($query)
	{
		if (!($result = mysql_query($query,$this->link)))
			return false;
		return $result;
	}

	/**
	 * qSelect. Performs a select query and returns the result.
	 *
	 * @param string $query Query to send to the database.
	 * @return false, or an array where each row is a MySql result.
	 */
	function qSelect($query)
	{
		if (!($res = $this->makeQuery($query)))
			return false;
		$results = array();
		while ($row = mysql_fetch_assoc($res))
			$results[] = $row;
		return $results;
	}

	function qGetRow($query)
	{
		if (!($res = $this->makeQuery($query)))
			return false;
		return mysql_fetch_assoc($res);
	}

	function qGetValue($query)
	{
		if (!($res = $this->makeQuery($query)))
			return false;
		$res = mysql_fetch_array($res);
		return $res[0];
	}

	/**
	 * qInsert. Performs an insert query, and returns the id of the inserted row.
	 *
	 * @param string $query Query to send to the database.
	 * @return false, or an integer with the id of the just inserted row.
	 */
	function qInsert($query)
	{
		if (!$this->makeQuery($query))
			return false;
		return mysql_insert_id($this->link);
	}

	/**
	 * qUpdateOrDelete. Performs an update or delete query, and returns the number of affected rows.
	 *
	 * @param string $query Query to send to the database.
	 * @return false, or an integer that represents the number of affected rows.
	 */
	function qUpdateOrDelete($query)
	{
		if (!$this->makeQuery($query))
			return false;
		return mysql_affected_rows($this->link);
	}

	/**
	 * escape. A wrapper for the mysql_real_escape_string() function.
	 *
	 * @param string $string Query that needs to replace special characters for their safe representation.
	 * @return string Escaped version of the argument.
	 */
	function escape($string)
	{ return mysql_real_escape_string($string,$this->link); }

	/**
	 * error. A wrapper for the mysql_error() function.
	 *
	 * @return string Description of the last error that ocurred with the current connection to the database.
	 */
	function error()
	{ return mysql_error($this->link); }
}
?>