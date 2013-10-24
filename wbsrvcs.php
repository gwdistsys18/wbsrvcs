<html>
<body>

<?php

/*
*	Simple PHP web services emulator.
*
*	Can be used to create a fake web application with an
* 	arbitrary topology.
*
*	Expects URLs like:
	"http://localhost/wbsrvcs/wbsrvcs.php?hop=1&h1name=frontend&h1comp=5&h1write=1&h2name=backend&h2comp=10&h2write=2&"

*	Single server example - 5 computation loops and 2 DB inserts:
	"http://localhost/wbsrvcs/wbsrvcs.php?hop=1&h1name=frontend&h1comp=5&h1write=2"

*	Multi-tier query with write at backend:
	wget "192.168.246.102/wbsrvcs/wbsrvcs.php?hop=1&h1name=frontend&h1comp=5&h2name=192.168.246.101&h2comp=10&hwrite2=1" -O tmp

*	Database Setup:
		You must create a user and give it access to add new databases:
			CREATE USER 'wbsrvcs' IDENTIFIED BY 'wbsrvcs';
			GRANT ALL PRIVILEGES ON * . * TO 'wbsrvcs';
		Then you can load this page with 'wbsrvcs.php?cmd=setup' to have it create the databse
*/
require_once("util.php");

/* Important variables */
$maxHosts=4;
$dbHost="localhost";
$dbUser="wbsrvcs";
$dbPass="wbsrvcs";
$dbName="wbsrvcs";
$numRecords = 10000;

$useUpdates = false; // SET TO TRUE if you want to do DB updates instead of writes.

if($_GET["cmd"] == "setup"){
	echo "<p>Setting up database...</p>";
	mysql_connect($dbHost, $dbUser, $dbPass) or die(mysql_error());
	mysql_query("CREATE DATABASE IF NOT EXISTS " . $dbName . ";")
		or die(mysql_error());
	echo "<p>Clearing old tables...</p>";
	mysql_select_db($dbName) or die(mysql_error());
	mysql_query("DROP TABLE IF EXISTS " . $dbName)
		or die(mysql_error());
	echo "<p>Creating empty table...</p>";
	mysql_query("CREATE TABLE " . $dbName . " (id INT NOT NULL AUTO_INCREMENT,
		PRIMARY KEY(id), data BLOB) ENGINE=InnoDB")
		or die(mysql_error());


	for ($i=1; $i <= $numRecords; $i++) {

		mysql_query("INSERT INTO " . $dbName . " (data)
                        VALUES(" . $i . " ) ")
                        or die(mysql_error());
	}
	echo "<p>Finished setting up database!</p>";
	exit();
}

// Read in computation and writes to perform at each tier

if(empty($_GET["hop"]))
{
	$hop=1;
}
else
{
	$hop=$_GET["hop"];
}

for ($i=1; $i <= $maxHosts; $i++)
{
	if(empty($_GET["h" . $i . "name"]))
	{
		break;
	}
	$names[$i-1]=$_GET["h" . $i . "name"];
	if(empty($_GET["h" . $i . "comp"]))
	{
		$comps[$i-1] = 0;
	}
	else
	{
		$comps[$i-1]=$_GET["h" . $i . "comp"];
	}
	if(empty($_GET["h" . $i . "write"]))
	{
		$writes[$i-1]=0;
	}
	else
	{
		$writes[$i-1]=$_GET["h" . $i . "write"];
	}
}


// Forward request on to next tier
$hop+=1;
$query="hop=" . $hop . "&";
for ($i=1; $i < $maxHosts; $i++)
{
	if(empty($names[$i]))
	{
		break;
	}
	$query=$query . "h" . $i . "name=" . $names[$i] . "&";
	$query=$query . "h" . $i . "comp=" . $comps[$i] . "&";
	$query=$query . "h" . $i . "write=" . $writes[$i] . "&";
}

if(!empty($names[1]))
{
	$timer = startTimer();
	echo "\n<p>Next query: " . $query . "</p>";
	$output=makeReq($names[1], $query);
	echo "\n<p>Result:<br>\n\n" . $output;
	echo "\n<br><br>End of Result</p>";
	$timer = endTimer($timer);
	echo "\n<p>Query time: " . $timer . " seconds.</p>";
}
else
{
	echo "\n<p>Last query in chain!</p>";
}


// Do local mysql write
if($writes[0] > 0)
{
	echo "<p>Performing " . $writes[0] . " DB inserts.</p>";
	$timer = startTimer();
	mysql_connect($dbHost, $dbUser, $dbPass) or die(mysql_error());
	echo "\n<p>Connected to MySQL...";
	mysql_select_db($dbName) or die(mysql_error());
	echo "\nConnected to Database</p>";

	/* TODO: Should change this code so we can insert a variable amount of data
		into the Database.  The data column is of type BLOB, so it can store ~64KB
		of data. Right now we just insert a small number.
	*/

	for($i=0; $i < $writes[0]; $i++)
	{
		if(!$useUpdates) {
			mysql_query("INSERT INTO " . $dbName . " (data)
				VALUES(" . $hop . " - " . $i . " ) ")
				or die(mysql_error());
		}
		else {
			$randID=rand(1,$numRecords);
			mysql_query("UPDATE " . $dbName . " SET
                                data= " . $hop . " - " . $i . "
				WHERE id=$randID ")
                                or die(mysql_error());
		}
	}
	$timer = endTimer($timer);

	echo "\n<p>DB Write time: " . $timer . " seconds.</p>";
}

// Do local computation
if($comps[0] > 0)
{
	echo "<p>Performing " . $comps[0] . " computational loops.</p>";
	$timer = startTimer();
	loop($comps[0]);
	$timer = endTimer($timer);

	echo "\n<p>Local Computation time: " . $timer . " seconds.</p>";
}

?>
