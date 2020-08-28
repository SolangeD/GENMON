<?php
// Upload new study to the database
//ini_set("display_errors",0);error_reporting(0);
session_start();



//--------------------------------------------------------------------------------------------------------------------------------
// Getting info from the upload button action of the HTML page
$_SESSION=array();
session_destroy();
header("Location:../index.php");
?>