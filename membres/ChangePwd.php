<?php 
	session_start();
	
	

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
$lastpwd= str_replace("'", "''",md5($_POST['lastpwd']));
$newpwd = str_replace("'", "''",md5($_POST['newpwd']));
$newpwd2 = str_replace("'", "''",md5($_POST['newpwd2']));

include("../connectDataBase.php");
$dbh=db_connect();
$sql="SELECT password FROM membres WHERE pseudo='".$_SESSION['user']."'"; #attention insertion sql
$result = pg_query($sql);
$row = pg_fetch_array($result);
if(isset($lastpwd)  && $lastpwd==$row[0])
{
	if(isset($newpwd)  && $newpwd==$newpwd2)
	{
	$sql="UPDATE membres SET password='".$newpwd."' WHERE pseudo='".$_SESSION['user']."'";
	pg_query($sql);
	header("Location:AccManage.php?code=changeok");
	}
	else
	{
	header("Location:AccManage.php?code=pwdr");
	}
}
else
{
	header("Location:AccManage.php?code=pwd");
}
$dbh=db_disconnect;
?>
