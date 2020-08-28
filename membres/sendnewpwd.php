<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>GenMon-CH</title>
		<style type="text/css">
		</style>
		<link rel="stylesheet" href="../style.css" media="screen"/>
		<!--<script type="text/javascript" src="js/cssrefresh.js"></script>-->
	</head>    

<?php
// Upload new study to the database
//ini_set("display_errors",0);error_reporting(0);


session_start();
include("../connectDataBase.php");
require("PHPMailer_5.2.0/class.phpmailer.php");
include("ForMail.php");
$mail->Subject = "New genomap password";
//--------------------------------------------------------------------------------------------------------------------------------
// Getting info from the upload button action of the HTML page
function random($car) 
{
	$string = "";
	$chaine = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/*";
	srand((double)microtime()*1000000);
	for($i=0; $i<$car; $i++)
	{
		$string .= $chaine[rand()%strlen($chaine)];
	}
	return $string;
}

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
$pseudo=$_POST['pseudo'];
$mailto=$_POST['mail'];
$mail->AddAddress($mailto);
$newpwd=random(10);
$sql = "select mail from membres where pseudo='".$pseudo."'";
$result = pg_query($sql);
$row= pg_fetch_array($result);
if($row[0]==null)
{
header("Location:reinitpwd.php?error=mail");
return;
}
if($mailto != $row[0])
{
	header("Location:reinitpwd.php?error=ref");
return;
	
}

$sql = "UPDATE membres SET password='".md5($newpwd)."' WHERE pseudo='".$pseudo."'";
pg_query($sql);
$mail->Body ="Your new genomap password is:\r\n".$newpwd."\r\n";
$mail->Send();
db_disconnect($dbh);
//header("Location:../index.php");
include("../header.php");
?>
<div id="page">
<h2>Password reinitilized</h2>
<p>Your new password has been sent by email. You should receive it in a few seconds/minutes</p>
</div>
<?php
include("../footer.php");
?>
</html>
