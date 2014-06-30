<?php if (!defined('APP_NAME')) exit("Direct access to this file is not allowed.");

define('GEN_ERR_SYSTEM',	10);
define('GEN_ERR_FILE',		20);
define('GEN_ERR_UPLOAD',	30);
define('GEN_ERR_DB',		40);
define('GEN_ERR_TABLE',		50);
define('GEN_ERR_CANVAS',	60);

class GenException extends Exception
{
	function __construct($id,$subid=0,$arg1='',$arg2='')
	{
		switch ($id)
		{
			case 0: $msg = "No error"; break;

			case GEN_ERR_SYSTEM:
				switch ($subid)
				{
					case 1: $msg = "Constructor of this class requires an argument."; break;
					case 2: $msg = "I don't know how to process this study ($arg1)."; break;
				}
				break;

			#> Files and directories errors.
			case GEN_ERR_FILE:
				switch ($subid)
				{
					case 1: $msg = "$arg1 is not a directory"; break;
					//case 2: $msg = "$arg1 is not a file"; break;
					//case 3: $msg = "$arg1 is not readable"; break;
					//case 4: $msg = "$arg1 is not writeable"; break;
					case 5: $msg = "Could not open $arg1"; break;
					case 6: $msg = "Could not read $arg1"; break;
					//case 7: $msg = "Could not write $arg1"; break;
				}
				break;

			case GEN_ERR_UPLOAD:
				switch ($subid)
				{
					// These codes are the same as those detailed in http://us3.php.net/manual/en/features.file-upload.errors.php
					// except for 9, which belongs to my code.
					case 1: $msg = "The file exceeds the upload_max_filesize directive in php.ini"; break;
					case 2: $msg = "The file exceeds the max_file_size directive in the html form"; break;
					case 3: $msg = "The file was only partially uploaded"; break;
					case 4: $msg = "No file was actually uploaded"; break;
					case 6: $msg = "Missing a temporary folder"; break;
					case 7: $msg = "Failed to write to disk"; break;
					case 8: $msg = "File upload stopped by a php extension"; break;
					case 9: $msg = "File extension '$arg1' is not accepted"; break;
				}
				break;

			case GEN_ERR_DB:
				switch ($subid)
				{
					case 1: $msg = "Could connect to mysql engine: $arg1"; break;
					case 2: $msg = "Could not select database: $arg1"; break;
					case 3: $msg = "Could not execute query: $arg1"; break;
					case 4: $msg = "Not enough information to compose a query"; break;
					case 6: $msg = "Not an even number of arguments"; break;
				}
				break;

			case GEN_ERR_TABLE:
				switch ($subid)
				{
					case 1: $msg = "Column $arg1 does not exist"; break;
					case 2: $msg = "Column $arg1 does exist"; break;
					case 3: $msg = "Unknown property $arg1. Known properties are: $arg2."; break;
					case 4: $msg = "Attempt to populate table with a non-array element."; break;
					case 5: $msg = "A column containing a unique ID for the table is needed, and has not been provided. You may set it through the method setProperty('id',COL_NAME)."; break;
					case 6: $msg = "Attempt to set property '$arg1' of column '$arg2' as non-boolean"; break;
					case 7: $msg = "Attempt to set a filter of column '$arg1' with an non-filter object '$arg2'"; break;
					case 8: $msg = "Attempt to set column properties of a table with an incompatible object."; break;
				}
				break;

			case GEN_ERR_CANVAS:
				switch ($subid)
				{
					case 1: $msg = "You may only register objects of type 'Element'."; break;
				}
		}
		parent::__construct($msg, $id+$subid);
	}

	function display()
	{
		ob_end_clean();
		$t = new Template('error.tpl');
		$t->assign('message', "[".$this->getCode()."]: ".$this->getMessage());
		$t->assign('trace', str_replace("\n","<br/>",$this->getTraceAsString()));
		$t->display();
	}
}
?>