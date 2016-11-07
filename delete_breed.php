<html>
<!--salut
-a) check qu'il soit loggé et que c'est la bonne breed
-b) delete les tables breedXX
c) delete dans codes
d) delete dans summary-->
</html>
<?php 
include("membres/sessStarted.php");
if ( is_session_started() === FALSE ) session_start();

include("connectDataBase.php");
$dbh=db_connect();

if(isset($_POST['breed_id'])) {
	$breed_id=$_POST['breed_id'];
	echo $breed_id;
}



if(isset($_SESSION['user'])) { //if user is logged in, can delete its own study
	$pseudo=$_SESSION['user'];
	$sql="select owner from summary where breed_id=".$breed_id;
	$result=pg_query($sql);
	$owner=pg_fetch_result($result,0,0);
	echo $owner;
	if($owner != $pseudo){
		header("Location:error.php?delete=log"); //put an error message
		return;
	}
	//Know table names related to this breed
	$sql_table="SELECT table_name
	FROM information_schema.tables
	WHERE table_schema='public'
	AND table_type='BASE TABLE'
	AND table_name like 'breed".$breed_id."%'"; 
	$result_table=pg_query($sql_table);
	for($i=0;$i<pg_num_rows($result_table);$i++){ //delete all tables in the DB related to this breed
		$sql_delete="drop table ".pg_fetch_result($result_table,$i,0);
		pg_query($sql_delete);
    }
	$sql_delete_code="delete from codes where db_code=".$breed_id;
	pg_query($sql_delete_code);
	$sql_delete_summary="delete from summary where breed_id=".$breed_id;
	pg_query($sql_delete_summary); 

}
header("Location:index.php");	
?>
