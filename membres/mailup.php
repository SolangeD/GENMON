<?php
session_start();
include("../connectDataBase.php");

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

$dbh=db_connect();
$pseudo=$_SESSION['user'];
$pwd = str_replace("'","''",md5($_POST['mail_pwd']));
$sql = "select password from membres where pseudo='".$pseudo."'";
$result = pg_query($sql);
$row= pg_fetch_array($result);
if($pwd == $row[0])
{
$newmail=$_POST['mail'];
$sql="UPDATE membres SET mail='".$newmail."' WHERE pseudo='".$_SESSION['user']."'";
pg_query($sql);
header("Location:AccManage.php?code=mailchange");
}
else{
header("Location:AccManage.php?code=mail_wrongpwd");
}
db_disconnect($dhh);
?>