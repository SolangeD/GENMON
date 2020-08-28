<?php
// Generate new user (comes from signUp.php)



$conf["max_size"] = "90000000000000000";
// Check PHP-version
list($major, $minor, $rev) = explode(".", phpversion());
if($major < 4) { //need php version > 4 in order to have session 
	die("Wrong php version");
}
//session_destroy();
session_start(); 
// if early version rename HTML form variables
if($minor < 1) {
	$_POST = $HTTP_POST_VARS;
	$_SERVER = $HTTP_SERVER_VARS;
}

	$pseudo = str_replace("'", "''",$_POST['pseudo']);
	$mdp = str_replace("'", "''",md5($_POST['mdp']));
	$mdp2 = str_replace("'", "''",md5($_POST['mdp2']));
	$mail = str_replace("'", "''",$_POST['mail']); //manages the apostrophe problems -- for security reasons
	// Sanitize e-mail address
	$mail=filter_var($mail, FILTER_SANITIZE_EMAIL);
	// Validate e-mail address
if(filter_var($mail, FILTER_VALIDATE_EMAIL)==FALSE) {
	header("Location:signUp.php?code=mail");
	return;
}
if($pseudo==null)
{
	header("Location:signUp.php?code=nopseudo");
	return;
}
if($pseudo == 'Public')
{
	header("Location:signUp.php?code=public");
	return;
}
include("../connectDataBase.php");
$dbh = db_connect(); // connect to database and retrieve the database handle

//check ref_study is free
$sql = "SELECT pseudo FROM membres";
$result = pg_query($sql);

while ($row = pg_fetch_array($result))
{

	if($pseudo==$row[0])
	{
		header("Location:signUp.php?code=pseudo");
		return;
	}
}
if($mdp!=$mdp2)
{
	header("Location:signUp.php?code=pwd_diff");
	return;
}

$sql = "select COUNT(*) from membres ";
$result = pg_query($sql);
$row = pg_fetch_array($result);
$id=$row[0]+1;
	$sql = "
	INSERT INTO membres VALUES ('".$id."','".$pseudo."','". $mdp ."','".$mail."','".  date('YmdHis') ."','".  date('YmdHis') ."','user')
	";
	pg_query($sql);
//Need to add a default breed in the thres_weight table, otherwise will have some problem...
$sql_add_species="insert into species (owner, species) values ('".$pseudo."', 'default')";
pg_query($sql_add_species);

$sql_add_species2="insert into thres_weight (criteria, t1, t2, weight, crit_type)
	(select criteria, t1, t2, weight, crit_type from thres_weight where owner='default')"; 
pg_query($sql_add_species2);
	
$sql_add_weight2="update thres_weight set species= 'default' where owner is null";
pg_query($sql_add_weight2);
$sql_add_weight3="update thres_weight set owner= '".$pseudo."' where owner is null";
pg_query($sql_add_weight3);

//define session variable	
$_SESSION['user']=$pseudo;
header("Location:../index.php");
?>