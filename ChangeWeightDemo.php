<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link rel="stylesheet" href="style.css" media="screen"/>
<?php
include("header.php");
include("FunctionsCalcIndex.php");
$string_error="";
if(isset($_GET["error"])==1){
	if($_GET["error"]=="sum"){
		$string_error="Be careful: the sum of the weight you entered does not equal 1! Please change your weights!";
	}
	elseif($_GET["error"]=="threshold"){
		$string_error="Be careful: the satisfaction threshold (t1) and the non-satisfaction threshold (t2) must be different from each other!";
	}
}
?>
<body>
<div id="page">
<?php
include("connectDataBase.php");
$dbh=db_connect();
include("FunctionsChangeWeight.php");
$table_name="thres_weight";

if(isset($_GET["error"])==1){
	if($_GET["error"]=="sum"){
		$string_error="Be careful: the sum of the weight you entered does not equal 1! Please change your weights!";
		?>
		<strong><?php echo $string_error ;?></strong><br /><br />
	<?php
	}
	elseif($_GET["error"]=="threshold"){
		$string_error="Be careful: the satisfaction threshold (t1) and the non-satisfaction threshold (t2) must be different from each other!";
		?>
		<strong><?php echo $string_error ;?></strong><br /><br />
	<?php
	}
}
db_disconnect($dbh);
if(isset($_POST["00"])==1){
	$dbh=db_connect();
	if(isset($_SESSION['user']) && isset($_POST["species"])){
		$user=$_SESSION['user'];
		$total_weight=change_weight_db($table_name,'demo',$user, $_POST["species"]); //change in the DB table. Function from the FunctionsChangeWeight.php page
		if ($total_weight <> 1){
			db_disconnect($dbh);
			header("Location:ChangeWeightDemo.php?error=sum");
			exit();
		}
		else{	
			$sql_thres="select count(*) from thres_weight where (t1-t2)=0";
			$thres0=pg_query($sql_thres);
			$thres=pg_fetch_result($thres0,0,0);
			if($thres<>0){
			db_disconnect($dbh);
			header("Location:ChangeWeightDemo.php?error=threshold");
			exit();
			}
			else{
				//IF RECALCULATE THE GLOBAL INDEX FOR ALL BREED WHEN CHANGING THE WEIGHTS OF CRITERIA. NOT NEC. GOOD...
				$sql_db_code="select breed_id from summary where species='".$_POST["species"]."' and owner='".$user."'";
				$db_code0=pg_query($sql_db_code);
				$num_breed=pg_num_fields($db_code0);
				for($k=0;$k<$num_breed;$k++){
					$index_demo=IndexCalc(pg_fetch_result($db_code0,$k,0),'demo',$user,$_POST["species"]); //IndexCalc: function from the FunctionCalcIndex.php
					$index_final=IndexCalc(pg_fetch_result($db_code0,$k,0),'final',$user,$_POST["species"]);
				}
				db_disconnect($dbh);
				header("Location:ChangeWeightDemo.php");
			}
		}
	}
}
$dbh=db_connect($dbh);
if(isset($_SESSION['user'])) //if user is logged show all weights for the species
	{
	$user=$_SESSION['user'];
	$sql_select_species="select distinct species from thres_weight where owner='".$user."' and species<>'default' order by species";
	$result_select_species=pg_query($sql_select_species);
	for($i=0;$i<pg_num_rows($result_select_species);$i++){
		$species=pg_fetch_result($result_select_species,$i,0);
		echo "<b> Species: ".$species." </b><br /><br />";
		change_weight_form($table_name,'demo', $user, $species); 
	}
	
	} 
else{
	echo "You need to be logged in in order to change the weights";
}
?>
<br /><br />
If you do not see any array to change the weights, <strong>make sure you have first created a specie!</strong><br/><br/>
Make sure that the sum of the weight equals 1!<br />

				<ol>
				<li>t1: threshold at which the criteria is completely not satisfactory</li>
				<li>t2: threshold at which the criteria is completely satisfactory</li><br/>
				Note that if you are trying to minimize a criterion (for example the unemployment rate), t1 will be bigger than t2.
				<li>weight: The weight of the criteria. Note that the sum of the weights must equal one</li></ol>

				<br></br><br></br><br></br><br></br><br></br>
</div>
</body>
<?php


db_disconnect($dbh);
include("footer.php");
?>
</html>
