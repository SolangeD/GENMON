<?php

$_SESSION['wwwDirectory'] = "/var/www/html/genmon-ch/";		// directory where to save the javaScript file for the 
$_SESSION['wwwDataDirectory'] = "/var/www/html/genmon-ch/Data_files/";	// directory where to save the data files
$wwwDataDirectory="/var/www/html/genmon-ch/Data_files/";
//$fileName='agriculture_OFS';
$JSwwwDataDirectory = str_replace("\\", "/", $_SESSION['wwwDataDirectory']);		// directory (javaScript format) where to save the data files
$_SESSION['hostDirectory'] = "http://localhost/genmon-ch/Data_files/";	// host directory needed by the javaScript script to locate the .json file
include("connectDataBase.php");
include("FunctionsCalcIndex.php");
$dbh=db_connect();
//$type=	$_POST['type'];
$fra = $_FILES["upfil"]["tmp_name"];	// gets the name specified for this given file
$filedel=str_replace(' ','_',$_FILES["upfil"]["name"]);
$fileName=$filedel;
$til = $JSwwwDataDirectory  . str_replace(' ','_',$_FILES["upfil"]["name"]);	// set the directory where the uploaded file should be copied

if ($fra != null) {		// if a file has been specified , copy it in the directory $til (Data_files folder)
	copy($fra, $til);
	
} else {
	header("Location:AddDataOFS.php?error=nofile"); 
	return;
}

$text = readTxtFile($wwwDataDirectory . $fileName );	// read file
if ($text == null) {
	header("Location:AddDataOFS.php?error=fileformat");
	return;
}

$lines = explode("\n", $text);	// split the file with respect to the lines

$nrLin = count($lines)-1;		// number of lines containing data (wo headers)

//$colNamesTxt = trim($lines[0]); // Deletes tabs and space at the end of the line
$colNamesTxt = $lines[0];
$colNamesArray = explode(";", $colNamesTxt);	// put the column names in an array; before \t
$nrCol = count($colNamesArray);	// Count number of columns

$linesOK = array();		// initialize the new lines array
$j = 0;
for ($i = 0; $i < $nrLin-1; $i++) {		// for each line of my table called $lines...
	if (isset($lines[$i+1])) {
		
		$curLine = trim($lines[$i+1]);	// ...remove last character to avoid problems with writing data in the table
		
		if (empty($curLine) == FALSE){	// Delete empty lines at the end of the file
			$linesOK[$j] = $curLine;
			$j++;
		} 
	}
}

$sql_drop_dump="drop table ofs_dump";
pg_query($sql_drop_dump);
if(isset($_POST['year'])==1){
	$year=$_POST['year'];
echo $year;
}
else {
	header("Location:AddDataOFS.php?error=year");
}

//create the table ofs_dump with the right column names...
$string_colName="";
for ($j=0;$j<$nrCol-1; $j++){ //might need to check that the user entered the right number of columns in the dropdown list
	$string_colName = $string_colName.$_POST['column'.$j].' real, '; 
}
$j=$nrCol-1;
$string_colName = $string_colName.$_POST['column'.$j].' real'; //the last name should not have a "," after the type
$sql_create_dump="create table ofs_dump (".$string_colName.")";
pg_query($sql_create_dump);

//copy the file in the ofs_dump table
pg_copy_from($dbh, "ofs_dump", $linesOK, ";");

$sql_create_ofs = "create table ofs_".$year." as (select * from ofs)"; 
pg_query($sql_create_ofs);
echo "salut";
//join with the ofs_year
for ($j=0;$j<$nrCol; $j++){ 
	$sql_join_ofs = "update ofs_".$year." a set ".$_POST['column'.$j]." = 
	(select b.".$_POST['column'.$j]." from ofs_dump b
	where b.num_ofs = a.num_ofs)";
echo $sql_join_ofs;
	pg_query($sql_join_ofs);
}

$select_ofs0="select * from ofs_".$year." limit 1"; 
$select_ofs=pg_query($select_ofs0);
$num_field=pg_num_fields($select_ofs);
$median_income="update ofs_".$year." set median_income=1";
pg_query($median_income);
$not_empty=0;
for($k=0;$k<$num_field;$k++){
	$name_col=pg_field_name($select_ofs,$k);
	$sql_get_not_null0="select ".$name_col." from ofs_".$year." where ".$name_col." is not null";
	$sql_get_not_null=pg_query($sql_get_not_null0);
	if (is_numeric(pg_fetch_result($sql_get_not_null,0,0))){
		$not_empty++;
	}
}

if ($not_empty==$num_field){ 
	//calculation of special fields
	$sql_add_column = "ALTER TABLE ofs_".$year." ADD COLUMN percent_farmer real, ADD COLUMN percent_grazing_surface real, ADD COLUMN evol_job_primary_sector real";
	pg_query($sql_add_column);
	$sql_percent_farmer0="update ofs_".$year." set percent_farmer = round(cast(job_primary_sector/job_total*100 as numeric), 2) where job_total is not null and job_total <> 0";
	pg_query($sql_percent_farmer0);
	$sql_percent_surface_grazing0="update ofs_".$year." set percent_grazing_surface = round(cast(grazing_surface_ha/(total_surface_km2*100)*100 as numeric), 2) where total_surface_km2 is not null and total_surface_km2 <> 0";
	pg_query($sql_percent_surface_grazing0);
	$sql_evol_job = "update ofs_".$year." set evol_job_primary_sector = round(cast((job_primary_sector - job_primary_sector_past)/job_primary_sector*100 as numeric),2) where job_primary_sector is not null and job_primary_sector <>0";
	pg_query($sql_evol_job);
	//join with plz_socioec_year
	$drop_plz_socio_ec = "drop table plz_socioec_".$year."";
	pg_query($drop_plz_socio_ec);
	$create_plz_socio_ec = "create table plz_socioec_".$year." as (select * from plz_socioec)";
	pg_query($create_plz_socio_ec);
	$num_field_plz0 = "select * from plz_socioec_".$year."";
	$num_field_plz1 = pg_query($num_field_plz0);
	$num_field_plz = pg_num_fields($num_field_plz1);
	for ($m=0;$m<$num_field_plz;$m++){
		$colName=pg_field_name($num_field_plz1,$m);
		$join_ofs_plz = "update plz_socioec_".$year." a set ".$colName." =
		(select b.".$colName." from ofs_".$year." b
		where b.num_ofs=a.num_ofs)";
		pg_query($join_ofs_plz);
	}
	$sql_drop_table="drop table ofs_".$year;
	pg_query($sql_drop_table);
	IndexSocioEcPLZ($year); //function in FunctionsCalcIndex
}

function readTxtFile($filename) {
	//open file
	$fileToOpen = fopen($filename,"r");
	if ($fileToOpen == null){
		return null;
	}
	else {
		//set the content of the file into a variable
		$content = fread($fileToOpen, filesize($filename));
		//close the file
		fclose($fileToOpen);
		//return the content
		return $content;
	}
}
db_disonnect($dbh);
?>
