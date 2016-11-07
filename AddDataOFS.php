<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link rel="stylesheet" href="style.css" media="screen"/>
<?php
include("header.php");
?>
<body>
<div id="page">
<form action="GenOFS.php" method="post" enctype="multipart/form-data">
<b>Add socio-economic data:</b>
	                <input type="file" name="upfil" />
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
<p>Please put the order of the columns for the file you want to upload.<br />
You can have empty columns at the end<br />
The num_ofs field is mandatory but might be placed anywhere</p>
<?php
include("connectDataBase.php");
$dbh=db_connect();
$sql="select * from ofs";
$sql1=pg_query($sql);
$num_field=pg_num_fields($sql1);
?>
<!--<form action="">-->
<?php
for($j=0;$j<$num_field;$j++){
	$k=$j+1;
	?>
	<?php echo 'column '.$k.'  ';?><select name="<?php echo 'column'.$j;?>">
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
db_disconnect($dbh);
?>
<br />
Year (of the data to enter. Ex: 2013): <input type="text" name="year"><br /><br />
<input type="submit" value="Upload this file" /> </form>
<?php
include("footer.php");
?>
</div>
</body>
</html>
