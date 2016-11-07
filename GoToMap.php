<?php
//Link between the breed_detail.php and FinalMap.php page. Checks which breed is stored in the plzo_plz and joins the breed if necessary
include("connectDataBase.php");
include("FunctionsGenDiv.php");
if (isset($_POST[ 'breed_id' ])){
	$breed_id = $_POST[ 'breed_id' ];
}
$dbh=db_connect();
//in the last_log: store the breed ID for which data are in the plzo_plz table 
$sql_last_breed="SELECT db_breed from last_log";
$last_breed=pg_query($sql_last_breed);
if (pg_fetch_result($last_breed, 0,0)==$breed_id){
	db_disconnect($dbh);
	header("Location:FinalMap.php");
	return;
}
else {
	joinSQL($breed_id); //join using plz number between the table breedXX_inb_plz (list of plz with corresponding inbreeding,...) and the spatial layer plzo_plz (the one desplayed in the map)
	db_disconnect($dbh);
	header("Location:FinalMap.php");
}
?>
