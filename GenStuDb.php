<?php
// Upload new study to the database
//session_start();

include("FunctionsGenStud.php");
include("connectDataBase.php");		//include function
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
	$_FILES = $HTTP_POST_FILES;
	$_POST = $HTTP_POST_VARS;
	$_SERVER = $HTTP_SERVER_VARS;
}

	if(isset($_SESSION['user']))
	{
		$user=$_SESSION['user'];
	}
	else
	{
		$user="Public";
	}
	$studref = str_replace("'", "''",$_POST['breed_id']); //manages the apostrophe problems
	$breed_id=$studref;
	//$desc = str_replace("'", "''",$_POST['desc']);
	$desc="desc";
	//$type=	$_POST['type'];
	$type="type";
	$fra = $_FILES["upfil"]["tmp_name"];	// gets the name specified for this given file


	$filedel=str_replace(' ','_',$_FILES["upfil"]["name"]);
	$til = $JSwwwDataDirectory  . str_replace(' ','_',$_FILES["upfil"]["name"]);	// set the directory where the uploaded file should be copied
	if (isset($_POST[ 'breed_id' ])){
	$breed_id = $_POST[ 'breed_id' ];
	}
	if (isset($_POST[ 'breed_name' ])){
	$breed_name = $_POST[ 'breed_name' ];
	}

if ($fra != null) {		// if a file has been specified , copy it in the directory $til (Data_files folder)
	copy($fra, $til);
	
} else {
	echo $_FILES["upfil"]["error"]." ";
	header("Location:AddStudy.php?error=nofile");
	exit();
	
}

//check the file size
$file_size = filesize($fra)/1024;
if($file_size > $conf["max_size"]) {
	die("The file is too big:  " .
	$conf["max_size"] .
	ceil($file_size) . " b");
}

$infosfichier = pathinfo($_FILES["upfil"]["name"]);
$extension_upload = $infosfichier['extension'];
//allowed extension. Be carefull, if put an extension with 4 letters, should check when the name is created a bit lower in this page
$extensions_autorisees = array('csv');

if (in_array($extension_upload, $extensions_autorisees)) // verify extension
{
}
else
{
	header("Location:error.php?error=extension");
	exit();
}


$dbh = db_connect(); // connect to database and retrieve the database handle


$_SESSION['fileName'] = str_replace(' ','_',substr_replace($_FILES["upfil"]["name"] ,"",-4));	// remove the suffix .csv and store the file name in a session variable



$text = readTxtFile($_SESSION['wwwDataDirectory'] . $_SESSION['fileName'] . ".csv");	// read file
if ($text == null) {
	header("Location:AddStudy.php?error=fileformat");
	exit();
}

$lines = explode("\n", $text);	// split the file with respect to the lines

$nrLin = count($lines)-1;		// number of lines containing data (wo headers)

//$colNamesTxt = trim($lines[0]); // Deletes tabs and space at the end of the line
$colNamesTxt = $lines[0];
$colNamesArray = explode("|", $colNamesTxt);	// put the column names in an array; before \t

if($colNamesArray[8]==null){  //The file must contain 9 columns
	header("Location:error.php?error=fileformat");
	exit();
}

$date=date('Y-m-d-H-i-s');
exec("mkdir /var/lib/postgresql/incoming/".$date);
exec("mv ".$_SESSION['wwwDataDirectory'].$_SESSION['fileName'].".csv /var/lib/postgresql/incoming/".$date."/datafile");
$param=array();
$param[]="email=solange.duruz@epfl.ch";
$param[]="breed=".$breed_name;
$param[]="male=M";
$param[]="female=F";
$param[]="pedfile=datafile";
$param[]="dateformat=YYYY-MM-DD";
$param[]="datesep=-";
$param[]="get_tar=0";
$j=0;
while ($j<count($param)){
	exec("echo ".$param[$j]." >> /var/lib/postgresql/incoming/".$date."/param");
	$j++;
}

db_disconnect($dbh);
//unlink($_SESSION['wwwDataDirectory'].$filedel); //delete '.csv' file upload

header("Location:PopRep.php?breed_id=".$breed_id); //next step: run poprep code
?>
