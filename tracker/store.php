<?xml version="1.0" encoding="UTF-8"?>
<?php
	require_once('config.php');
	require_once('files/CompactDB.php');

	// We open a connection to the database where everything will be kept.
	$db = new CompactDB($mt_dbhost, $mt_dbname, $mt_dbuser, $mt_dbpass);

	// We obtain the data coming from the request.
	$pid = getvar('pid', '');			// Participant ID.
	$name = getvar('name', '');			// Name of this 'project' (optional)
	$content = getvar('content', '');	// Whole record of mouse-tracking data.
	$agent = getvar('agent', '');

	// And some additional variables.
	$error = 0;
	$errormsg = '';
	$ret = 0;
	$additional = '';

	// If we receive no pid, we complain.
	if ($pid=='')
	{
		$error = 1;
		$errormsg = 'No participant id in URL';
	}

	// If there is no content to store, we don't bother trying.
	else if ($content=='')
	{
		$error = 2;
		$errormsg = 'No tracking data to store';
	}

	// We finally try to insert. If we fail, we complain.
	else if (!$db->qInsert("insert into track (`pid`,`name`,`content`,`agent`) values ('$pid','$name','".$db->escape($content)."','".$db->escape($agent)."')"))
	{
		$error = 3;
		$errormsg = 'Insertion in database failed';
		$additional = $db->error();
	}

	// If we get to this point, everything went well.
	else
		$errormsg = 'OK';
?>
<response>
	<error><?php echo $error;?></error>
	<errormsg><?php echo $errormsg;?></errormsg>
	<additional><?php echo $additional;?></additional>
</response>