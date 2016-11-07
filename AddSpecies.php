<?php
include("connectDataBase.php");
$dbh=db_connect();
include("membres/sessStarted.php");
if ( is_session_started() === FALSE ) session_start();
if(isset($_SESSION['user'])) //if user is loged in show account management and logout option
	{
	$owner=$_SESSION['user'];
	}
else{
	header("Location:error.php?add=log");
	}
if(isset($_POST['species_name'])) //if user is loged in show account management and logout option
	{
	$species=$_POST['species_name'];
	}
$sql_add_species="insert into species (owner, species) values ('".$owner."', '".$species."')";
pg_query($sql_add_species);
$sql_add_weight1="insert into thres_weight (criteria, t1, t2, weight, crit_type)
	(select criteria, t1, t2, weight, crit_type from thres_weight where owner='default' and crit_type!='SocioEco')"; 
pg_query($sql_add_weight1);
$sql_add_weight2="update thres_weight set species= '".$species."' where owner is null";
pg_query($sql_add_weight2);
$sql_add_weight3="update thres_weight set owner= '".$owner."' where owner is null";
pg_query($sql_add_weight3);


db_disconnect($dbh);
header("Location:index.php");
?>