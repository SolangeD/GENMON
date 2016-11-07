<?php

//*****************************************************************************************************************************
//*****************************************************************************************************************************
//******************				START OF THE MAIN SCRIPT	        	*******************************
//*****************************************************************************************************************************
//*****************************************************************************************************************************

session_start();	// starts the session in order to use "session variables" to transfert varaibles from a php script to another

include("FunctionsGenDiv.php");		// include the file with the functions to be used throughout the main script
include("connectDataBase.php");		// include the file for the connection to the database

//accessible server directory addresses used throughout the script. The variables that are needed by "FinalMap.php" as well are declared as session variables
$_SESSION['wwwDirectory'] = "/var/www/html/genmon-ch/";		// directory where to save the javaScript file for the OpenLayers mapping
$_SESSION['wwwDataDirectory'] = "/var/www/html/genmon-ch/Data_files/";	// directory where to save the data files
$JSwwwDataDirectory = str_replace("\\", "/", $_SESSION['wwwDataDirectory']);		// directory (javaScript format) where to save the data files
$_SESSION['hostDirectory'] = "http://localhost/genmon-ch/Data_files/";	// host directory needed by the javaScript script to locate the .json file


//--------------------------------------------------------------------------------------------------------------------------------
// Getting info from the upload button action of the HTML page

$conf["max_size"] = "90000000000000000";
// Check PHP-version
list($major, $minor, $rev) = explode(".", phpversion());
if($major < 4) {
	die("Wrong php version");
}

// if early version rename HTML form variables
if($minor < 1) {
	$_POST = $HTTP_POST_VARS;
	$_SERVER = $HTTP_SERVER_VARS;
}
$dbh = db_connect();// connect to the database for next request
if(isset($_GET['ref']))
{$sql="SELECT stdref FROM url WHERE codeurl='".$_GET['ref']."'";
$result = pg_query($sql);
$row = pg_fetch_array($result);
$stref=$row[0];
$_SESSION['study']=$stref;
}
else if(isset($_POST['study']))
{
$stref= $_POST['study'];
$_SESSION['study']=$stref;
}
else
{
$stref=$_SESSION['study'];
}
if($stref==null)// if no study selected stop
{
db_disconnect($dbh);
header("Location:index.php?error=noselect");
	return;
}


$sql = "SELECT type FROM ref_study WHERE ref_study='".$stref."'";//extract table reference for this study
$result = pg_query($sql);
$row = pg_fetch_array($result);
$typestd=$row[0];
$sql = "SELECT users FROM ref_study WHERE ref_study='".$stref."'";//extract table reference for this study
$result = pg_query($sql);
$row = pg_fetch_array($result);
$users=$row[0];
if(isset($_GET['ref']))
{}
else{
if($typestd!='Public')
{
	if($_SESSION['user']!=$users)
	{
		if($_SESSION['admin']!=true)
		{
		db_disconnect($dbh);
		header("Location:index.php?code=accDenied");
		}
	}	
}
}


joinSQL($_SESSION['study']);
db_disconnect($dbh);

header("Location:index.php");

?>




