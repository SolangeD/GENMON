<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link rel="stylesheet" href="style.css" media="screen"/>
<?php
include("header.php");
?>
<div id="page">
<h2>Error</h2><br/>

<b>
<?php
if(isset($_GET['upload'])){
switch ($_GET['upload']) {
            case '1':
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case '2':
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case '3':
                $message = "The uploaded file was only partially uploaded";
                break;
            case '4':
                $message = "No file was uploaded";
                break;
            case '5':
                $message = "Missing a temporary folder";
                break;
            case '6':
                $message = "Failed to write file to disk";
                break;
            case '7':
                $message = "File upload stopped by extension";
                break;

            default:
                $message = "Unknown upload error";
                break;
        } 
echo 'Upload error! '.$message;
}
if(isset($_GET['error']) && $_GET['error']=='nofile'){
echo 'No file was specified!';
}
if(isset($_GET['error']) && $_GET['error']=='breed_info'){
echo 'When you uploaded information, you did not specify information on the cultural values of the breed and/or the number of farms';
}
if(isset($_GET['error']) && $_GET['error']=='database'){
$MessageMail= 'The file could not be copied to the database. <br/>Please make sure that you <ol><li>do not have special characters (like &ouml; or &ccedil; )</li> <li> have the right number of columns (as specified in the asssisted upload or in the standard file format)</li></ol>';
echo $MessageMail;
}
if(isset($_GET['pgsql'])){
switch($_GET['pgsql']){
	case '7':
		$column='plz';
		$type='integer';
		break;
	case '8':
		$column='introgression';
		$type='real';
		break;
	case '9':
		$column='inb_gen';
		$type='real';
		break;
	case '10':
		$column='cryo_cons';
		$type='boolean';
		break;	
}
$MessageMail='Data could not be copied in the right database table. <br/>You most probably have an incoherence in the datatype. <br/>Check the '.$column.' (should be of type '.$type.').';
echo $MessageMail;
}
if(isset($_GET['error']) && $_GET['error']=='poprep'){
$MessageMail='The PopRep code could not be run correctly with these data... sorry :( ';
echo $MessageMail;
}
if(isset($_GET['error']) && $_GET['error']=='poprep2'){
	if(isset($_SESSION['breed_name'])){
		$breed_name=$_SESSION['breed_name'];
		$MessageMail='The PopRep code could not be run correctly with these data... sorry :( You might find more information on <a style="color:black;" href="pdf/error-'.$breed_name.'.pdf" target="_blank">this page</a>';
	}
	else {
		$MessageMail='The PopRep code could not be run correctly with these data... sorry :( We saddly have no more information';
	}
echo $MessageMail;
}
if(isset($_GET['error']) && $_GET['error']=='fileformat'){
echo 'You do not have the right number of columns. Check the tutorial to know how to upload standard data, or go to assisted upload section';
}
if(isset($_GET['error']) && $_GET['error']=='extension'){
echo 'Wrong extension! The allowed extension is .csv file';
}
if(isset($_GET['delete']) && $_GET['delete']=='log'){
echo 'You must be the owner of the study in order to delete it!';
}
if(isset($_GET['add']) && $_GET['add']=='log'){
echo 'You must be logged in in order to add a species!';
}
if(isset($MessageMail) && isset($_SESSION['user'])){
include("connectDataBase.php");
$dbh=db_connect();
require("membres/PHPMailer_5.2.0/class.phpmailer.php");
include("membres/ForMail.php");
$mail->Subject = "Error while using GenMon";
$sql = "select mail from membres where pseudo='".$_SESSION['user']."'";
$result=pg_query($sql);
$mailto=pg_fetch_result($result,0,0);
$mail->AddAddress($mailto);
$mail->Body =$MessageMail;
$mail->Send();
db_disconnect($dbh);
}
?>
</b></br></br></br></br></br></br></br></br></br>
You landed on that page because of an error: you should see the error message abvoe. If not you encoutered an unspecified message. Please contact the system administrator in that case. </br></br>
<br/>
<br/>
<a href="index.php">Go back to the main page</a>
</div>
<?php
include("footer.php");
?>
</html>
