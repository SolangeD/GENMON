<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link rel="stylesheet" href="style.css" media="screen"/>
<?php
include("header.php");
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
if(isset($_SESSION['user'])){
change_weight_form($table_name,'SocioEco',$_SESSION['user'],'default');
}
?>
Make sure that the sum of the weight equals 1!<br />

				<ol>
				<li>t1: threshold at which the criteria is completely not satisfactory</li>
				<li>t2: threshold at which the criteria is completely satisfactory</li><br/>
				Note that if you are trying to minimize a criterion (for example the number of inhabitant aged 65 or more), t1 will be bigger than t2.
				<li>weight: The weight of the criteria. Note that the sum of the weights must equal one</li></ol>

				<br></br><br></br><br></br><br></br><br></br>
</body>
<?php


if(isset($_POST["00"])==1){
	if(isset($_SESSION['user'])){
		$total_weight=change_weight_db($table_name,'SocioEco', $_SESSION['user'], 'default');
		if ($total_weight <> 1){
			db_disconnect($dbh);
			header("Location:ChangeWeightSocioEco.php?error=sum");
		}
		else{
			$sql_thres="select count(*) from thres_weight where (t1-t2)=0";
			$thres0=pg_query($sql_thres);
			$thres=pg_fetch_result($thres0,0,0);
			if($thres<>0){
			header("Location:ChangeWeightFinal.php?error=threshold");
			exit();
			}
			else{			
			$sql_year="SELECT cast(substring(table_name, 13,4) as integer) as table_name2 FROM information_schema.tables WHERE table_schema='public' and table_name like 'plz_socioec_%' order by table_name2 desc limit 1" ; //select the most recent year for which we have a plz_socioec table
			$res_year=pg_query($sql_year);
			$year=pg_fetch_result($res_year,0,0);
			include("FunctionsCalcIndex.php");
			IndexSocioEcPLZ($year, $_SESSION['user']); //to calculate the score for each PLZ from FunctionsCalcIndex.php
			db_disconnect($dbh);
			header("Location:index.php");		
			}
		}
	}
}
include("footer.php");
?>
</html>
