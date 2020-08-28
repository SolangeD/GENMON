<?php
include("sessStarted.php");
if ( is_session_started() === FALSE ) session_start();

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

$pseudo = str_replace(" ","",$_POST['pseudo']);
$pwd = str_replace("'","''",md5($_POST['pwd']));

include("../connectDataBase.php");
db_connect(); // connect to database and retrieve the database handle

$sql = "select password from membres where pseudo='".$pseudo."'";
$result = pg_query($sql);
$row= pg_fetch_array($result);
if($pwd == $row[0])
{
	$_SESSION['user']=$pseudo;
	$sql=" UPDATE membres SET lastvisit='".  date('YmdHis') ."' WHERE pseudo='".$pseudo."'";
	pg_query($sql);
	$sql = "SELECT adm FROM membres where pseudo='".$pseudo."'";
	$result = pg_query($sql);
	$row= pg_fetch_array($result);
	if($row[0]=="Admin")
	{$_SESSION['admin']=true;}
	else
	{$_SESSION['admin']=false;}
	echo $_SESSION['user'];
}
else
{
	header("Location:../index.php?error=log");
	session_destroy();
	return;
}

header("Location:../index.php");
?>