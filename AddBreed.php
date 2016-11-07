<?php
//This adds a new breed in the database by creating a new row in the codes table (with name and short-name entered by the user)
//and creates a new row in the summary table (that will be filled in when data are entered)
include("connectDataBase.php");
$dbh=db_connect();
if (isset($_POST[ 'breed_name' ])){
	$breed_name = $_POST[ 'breed_name' ];
}
if (isset($_POST[ 'breed_long_name' ])){
	$breed_long_name = $_POST[ 'breed_long_name' ];
}
if (isset($_POST[ 'species' ])){
	$species = $_POST[ 'species' ];
}
if (isset($_POST[ 'public' ])){
	$public = $_POST[ 'public' ];
}
if(isset($_POST['user_name'])){ //rajout
	$user=$_POST['user_name'];
}

//check that the name is not already used
$get_name="SELECT short_name FROM codes";
$get_name2=pg_query($get_name);
for($i=0;$i<pg_num_rows($get_name2);$i++){
	if(pg_fetch_result($get_name2,$i,0)==$breed_name){
		header("Location:index.php?error=wrong_name");
		return;
	}
}
$get_long_name="SELECT long_name FROM codes";
$get_long_name2=pg_query($get_long_name);
for($i=0;$i<pg_num_rows($get_long_name);$i++){
	if(pg_fetch_result($get_long_name2,$i,0)==$breed_long_name){
		header("Location:index.php?error=wrong_name");
		return;
	}
}

//add values to the codes and summary tables
$sql_max_id="select max(db_code) from codes";
$max_id0=pg_query($sql_max_id);
$next_id=pg_fetch_result($max_id0,0,0)+1;
$add_code="INSERT INTO codes (short_name, class, long_name, db_code) values ('".$breed_name."', 'BREED', '".$breed_long_name."', ".$next_id.")";
pg_query($add_code);
$get_id="SELECT db_code FROM codes WHERE short_name='".$breed_name."'";
$get_id2=pg_query($get_id);
$breed_id=pg_fetch_result($get_id2, 0,0);
$add_summary="INSERT INTO summary (breed_id, breed_name, owner, species, public) VALUES (".$breed_id.", '".$breed_name."', '".$user."', '".$species."', ".$public." )"; //rajout
pg_query($add_summary);
header("Location:index.php");
db_disconnect($dbh);
?>
