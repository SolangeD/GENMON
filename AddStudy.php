<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link rel="stylesheet" href="style.css" media="screen"/>
<!-- This page corresponds to the assisted upload section, where a user can add several files at a time for data for one breed -->
<?php
include("header.php");
include("connectDataBase.php");
$dbh=db_connect();
if(isset($_POST['breed_id'])){
$breed_id=$_POST['breed_id'];
}
?>
<body>
<div id="page">
Welcome to the assisted upload section.You are uploading data for the breed:</br><h1>
<?php
if(isset($_POST['breed_id'])){
$sql_name="select long_name, short_name from codes where db_code=".$breed_id."";
$name0=pg_query($sql_name);
$long_name=pg_fetch_result($name0,0,0);
$short_name=pg_fetch_result($name0,0,1);
echo $long_name." (".$short_name.")";
}
?>
</h1><br/><br/>
<form action="GenAnimal.php" method="post" enctype="multipart/form-data">
<h2>General information about the breed:</h2></br>
Does the breed have a cultural value </br>
<input type="radio" name="cultural_value" value="1">yes</option> </br>
<input type="radio" name="cultural_value" value="0">no</option> </br></br>
Does the cultural value of the breed decreased in the recent past </br>
<input type="radio" name="cultural_value_trend" value="0">yes</option> </br>
<input type="radio" name="cultural_value_trend" value="1">no</option> </br></br>
Please give the approximate number of farms <input type="number" name="number_farm"/></br>
Please give the approximate number of farms 5 years ago<input type="number" name="number_farm_past"/></br></br>
Does the breed have cryo-conserved semen </br>
<input type="radio" name="frozen_semen" value="1">yes</option> </br>
<input type="radio" name="frozen_semen" value="0">no</option> </br></br>
Does the breed have a cryo-conservation management plan? </br>
<input type="radio" name="cryo_plan" value="1">yes</option> </br>
<input type="radio" name="cryo_plan" value="0">no</option> </br></br>
<h2>Information on the files you are uploading</h2></br>
If you have entered the following parameter, please fill in the corresponding section
<br/><br/>
The code used for sex. Male: <input type="text" name="male_code" value="M"/>
Female: <input type="text" name="female_code" value="F"/><br /><br/>
The birth date is given according to which format:
 <select name="birth_dt">
<option value="YYYY">YYYY</option>
<option value="YYYY-MM-DD">YYYY-MM-DD</option>
<option value="YYYY.MM.DD">YYYY.MM.DD</option>
<option value="DD-MM-YYYY">DD-MM-YYYY</option>
<option value="YYYY/MM/DD">YYYY/MM/DD</option>
<option value="DD/MM/YYYY">DD/MM/YYYY</option>
<option value="DD.MM.YYYY">DD.MM.YYYY</option>
<option value="DDMMYYYY">DDMMYYYY</option>
<option value="YYYYMMDD">YYYYMMDD</option>
</select><br/><br />
The introgression is given in:</br>  
<input type="radio" name="introgression" value="100">percent (ex: 95%)</option> </br>   
<input type="radio" name="introgression" value="1" checked>fraction (ex: 0.95)</option> </br>    </br> 
The introgression is given in:</br>  
<input type="radio" name="introgression2" value="notcalc" checked>%/fraction of foreign blood</option> </br>   
<input type="radio" name="introgression2" value="calc">%/fraction of own blood</option> </br>    </br>  
The inbreeding (calculated from the genetic analysis) is given in:</br>  
<input type="radio" name="inbr" value="100">percent (ex: 95%)</option> </br>   
<input type="radio" name="inbr" value="1" checked>fraction (ex: 0.95)</option> </br>    </br>   

<p>Please put the order of the columns for the file you want to upload.<br />
You can have empty columns at the end<br />
If you have multiple ID, you need to mention the joins between these IDs
<b>The ID1 are the one used in the parent IDs</b></p>
<?php
for($m=1;$m<5;$m++){
?>
<b>Enter the pedigree file <?php echo $m?>:</b>
	                <input type="file" name="upfil<?php echo $m?>" /> <br/>
				<?php
				if(isset($_GET['error']) && $_GET['error']=='filedirec')
					{
					echo "<er>Check file format and directory!</er>";
					}
				if(isset($_GET['error']) && $_GET['error']=='nofile')
					{
					echo "<er>No file selected!</er>";
					}
				if(isset($_GET['error']) && $_GET['error']=='extension')
					{
					echo "<er>Wrong extension!</er>";
					}
				?>

<?php

$sql="select * from animal_column";
$sql1=pg_query($sql);
$num_field=pg_num_fields($sql1);
?>
<!--<form action="">-->
<?php
for($j=0;$j<$num_field;$j++){
	$k=$j+1;
	?>
	<?php echo $m.' column '.$k.'  ';?><select name="<?php echo $m.'column'.$j;?>">
	<option value=""></option>
	<?php
	for($i=0;$i<$num_field;$i++){
	?>
	<option value="<?php echo pg_field_name($sql1,$i);?>"><?php echo pg_field_name($sql1,$i);?></option>
	<?php
}
?>
</select><br />
<?php
}

?>
<br />
Delimiter:<br/>
<input type="radio" name="<?php echo $m?>delimiter" value=";" checked>semicolon (;)</option> </br>   
<input type="radio" name="<?php echo $m?>delimiter" value=",">comma (,)</option> </br>    
<input type="radio" name="<?php echo $m?>delimiter" value="|">vertical line (|)</option>     </br><br />
The file contains a header line: <br/>
<input type="radio" name="<?php echo $m?>header" value="1" checked>yes</option> </br>   
<input type="radio" name="<?php echo $m?>header" value="0">no</option> </br></br>      

<?php
}
?>
<input type="hidden" name="breed_id" value="<?php echo $breed_id?>">
<input type="submit" value="Upload these files" /> </form></br> </br> </br> </br> </br> 
<?php
db_disconnect($dbh);
include("footer.php");
?>
</div>
</body>
</html>
