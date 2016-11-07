<?php
//This page uploads data for a breed that comes from the assisted upload section (AddStudy.php). It links the different files (with possibly different animal IDs and makes minor change (like the sex code, the birth date format, ...) according to the user inputs
$_SESSION['wwwDirectory'] = "/var/www/html/genmon-ch/";		// directory where to save the javaScript file for the 
$_SESSION['wwwDataDirectory'] = "/var/www/html/genmon-ch/Data_files/";	// directory where to save the data files
$wwwDataDirectory="/var/www/html/genmon-ch/Data_files/";

$JSwwwDataDirectory = str_replace("\\", "/", $_SESSION['wwwDataDirectory']);		// directory (javaScript format) where to save the data files
$_SESSION['hostDirectory'] = "http://localhost/genmon-ch/Data_files/";	// host directory needed by the javaScript script to locate the .json file
include("connectDataBase.php");
include("FunctionsCalcIndex.php");
$dbh=db_connect();
//$type=$_POST['type'];
$sql_drop="drop table if exists animal_dump";
$sql_dump="create table animal_dump as (select * from animal_column)";
pg_query($sql_drop);
pg_query($sql_dump);
//Change the sex code (depending on what the user has entered)
if(isset($_POST['cultural_value']) && isset($_POST['cultural_value_trend']) && isset($_POST['number_farm']) && isset($_POST['number_farm_past']) && isset($_POST['frozen_semen']) && isset($_POST['cryo_plan'])){
	$cultural_score=($_POST['cultural_value']+$_POST['cultural_value_trend'])/2;
	if($_POST['number_farm_past']==0){$_POST['number_farm_past']=1;}
	$farm_trend=($_POST['number_farm']-$_POST['number_farm_past'])/$_POST['number_farm_past']*100;
	$cryo_score=($_POST['frozen_semen']+$_POST['cryo_plan'])/2;
}
else{
	header("Location:error.php?error=breed_info"); 
	exit();
}

for($m=1;$m<5;$m++){
$fra = $_FILES["upfil".$m.""]["tmp_name"];	// gets the name specified for this given file
$filedel=str_replace(' ','_',$_FILES["upfil".$m.""]["name"]);
$fileName=$filedel;
$til = $JSwwwDataDirectory  . str_replace(' ','_',$_FILES["upfil".$m.""]["name"]);	// set the directory where the uploaded file should be copied
if(isset($_POST['breed_id'])){
$breed_id=$_POST['breed_id'];
$sql_breed_name="select breed_name from summary where breed_id=".$breed_id;
$sql_breed_name2=pg_query($sql_breed_name);
$breed_name=pg_fetch_result($sql_breed_name2,0,0);
}
if ($fra == null) {		// if a file has been specified , copy it in the directory $til (Data_files folder)
	if($m==1){
	header("Location:error.php?error=nofile"); 
	exit();
	}
} else {

	copy($fra, $til);
	$delimiter=$_POST[$m.'delimiter'];
	$text = readTxtFile($wwwDataDirectory . $fileName );	// read file
	if ($text == null) {
		$error=$_FILES["upfil".$m.""]['error'];
		header("Location:error.php?upload=".$error."");
		exit();
	}

	$lines = explode("\n", $text);	// split the file with respect to the lines

	$nrLin = count($lines)-1;		// number of lines containing data (wo headers)

	$Firstline = trim($lines[0]); // Deletes tabs and space at the end of the line
	$FirstlineArray = explode("".$delimiter."", $Firstline);	// put the column names in an array; before \t
	$nrCol = count($FirstlineArray);	// Count number of columns

$sql_drop_dump="drop table if exists animal_dump2";
pg_query($sql_drop_dump);

//create the table animal_dump with the right column names...
$string_colName="";
for ($j=0;$j<$nrCol-1; $j++){ //might need to check that the user entered the right number of columns in the dropdown list
	$string_colName = $string_colName.$_POST[$m.'column'.$j].' text, '; 
}
$j=$nrCol-1;
$string_colName = $string_colName.$_POST[$m.'column'.$j].' text'; //the last name should not have a "," after the type
$sql_create_dump="create table animal_dump2 (".$string_colName.")";
pg_query($sql_create_dump);

$linesOK = array();		// initialize the new lines array
$lineOK = array();	
$j = 0;
for ($i =$_POST[$m.'header']-1; $i < $nrLin-1; $i++) {		// for each line of my table called $lines... initial value 0 if header, 1 elsewise
	if (isset($lines[$i+1])) {
		
		$curLine = trim($lines[$i+1]);	// ...remove last character to avoid problems with writing data in the table
		
		if (empty($curLine) == FALSE){	// Delete empty lines at the end of the file
			$linesOK[$j] = $curLine;
			$LineOK[0]=$curLine;
			pg_copy_from($dbh, "animal_dump2", $LineOK, "".$delimiter."", "");
			$j++;
		} 
	}
}



//copy the file in the animal_dump2 table
//pg_copy_from($dbh, "animal_dump2", $linesOK, "".$delimiter."", "");
//check that the upload succeded
$sql_check2="select count(*) from animal_dump2";
$check0_2=pg_query($sql_check2);
if (pg_fetch_result($check0_2,0,0)==0){
	header("Location:error.php?error=database");
	exit();
}

$sql_join_dump = "insert into animal_dump ("; //whole query: Ex: insert into animal_dump (id1, sire_id, dam_id) (select id1, sire_id,dam_id from animal_dump2)
$num_col_list=0;
for ($j=0;$j<$nrCol; $j++){ 
	$sql_join_dump =$sql_join_dump.$_POST[$m.'column'.$j]."";
	if ($j!=$nrCol-1){ //last column must not have the comma
	$sql_join_dump =$sql_join_dump.", ";
	}
	if(!empty($_POST[$m.'column'.$j])){
		$num_col_list++;
	}
}
if($nrCol<>$num_col_list){
	header("Location:error.php?numcol=".$m);	
	exit();
}
$sql_join_dump =$sql_join_dump.") (select ";
for ($j=0;$j<$nrCol; $j++){ 
	$sql_join_dump =$sql_join_dump.$_POST[$m.'column'.$j]."";
	if ($j!=$nrCol-1){
	$sql_join_dump =$sql_join_dump.", ";
	}
}
$sql_join_dump =$sql_join_dump." from animal_dump2)";
pg_query($sql_join_dump);
//unlink($_SESSION['wwwDataDirectory'].$filedel); //delete the file
}//end else (if file exists)
} //end for (each file)


$sql_drop_animal="drop table if exists animal2";
$sql_create_animal="CREATE TABLE animal2
	(
	  id1 text default null,
	  id2 text default null,
	  id3 text default null,
	  sire_id text default null,
	  dam_id text default null,
	  birth_dt text default null,
	  sex text default null,
	  plz int default null,
	  introgression real default null,
	  inb_gen real default null,
	  cryo_cons boolean default null
	)";
pg_query($sql_drop_animal);
pg_query($sql_create_animal);

//join animal_dump with animal
$sql_animal=array(); //array with a whole bunch of queries
//insert groups of id1 and id2
$sql_animal[]="insert into animal2
(select id1, id2
from animal_dump a1
where id1 is not null
and id2 is not null
group by id1, id2)";
//insert the id1 that have no correspondant id2
$sql_animal[]="insert into animal2 (id1) 
(select distinct(a1.id1) from animal_dump a1 left outer join animal2 a2 on a1.id1=a2.id1 where a2.id1 is null)";

$sql_column_join="select * from animal2 limit 1";
$column_join=pg_query($sql_column_join);
$num_field=pg_num_fields($column_join);
//join the different fields of animal2 and animal_dump
for($i=2;$i<$num_field;$i++){ //loop on fields; begin after id1 and id2 (already put in the table
	$col_name=pg_field_name($column_join,$i);
	$col_type=pg_field_type($column_join,$i);
	if($col_type!='text'){
		$equality="cast(b.".$col_name." as ".$col_type.")"; //must cast for example to integer if not text field
	}
	else{
		$equality="b.".$col_name."";
	}

	$sql_animal[]="update animal2 a2 set ".$col_name." = ".$equality."
	from animal_dump b
	where b.id1=a2.id1
	and b.id1 is not null
	and b.".$col_name." is not null";
	$sql_animal[]="update animal2 a2 set ".$col_name." = ".$equality."
	from animal_dump b
	where b.id2=a2.id2
	and b.id2 is not null
	and b.".$col_name." is not null"; //Note that putting 2 different queries instead of the same query with id1=id1 or id2=id2 is more efficient
	if($i>2){ //For all attributes but id3, can also be linked through id3
			$sql_animal[]="update animal2 a2 set ".$col_name." = ".$equality."
			from animal_dump b
			where b.id3=a2.id3
			and b.id3 is not null
			and b.".$col_name." is not null";
	}
}
//Change the format of the birth_dt according to the format entered by the user
if(isset($_POST['birth_dt'])){
$birth_dt=$_POST['birth_dt'];
	if($birth_dt=='YYYY'){
	$sql_animal[]="update animal2 a1 set birth_dt=birth_dt||'-01-01'";
	}
	elseif($birth_dt=='YYYY/MM/DD' || $birth_dt=='YYYY.MM.DD' || $birth_dt=='YYYY-MM-DD'){
	$sql_animal[]="update animal2 a1 set birth_dt=substring(birth_dt from 1 for 4)||'-'||substring(birth_dt from 6 for 2)||'-'||substring(birth_dt from 9 for 2)";
	}
	elseif($birth_dt=='DD/MM/YYYY' || $birth_dt=='DD.MM.YYYY' || $birth_dt=='DD-MM-YYYY'){
	$sql_animal[]="update animal2 a1 set birth_dt=substring(birth_dt from 7 for 4)||'-'||substring(birth_dt from 4 for 2)||'-'||substring(birth_dt from 1 for 2)";
	}
	elseif($birth_dt=='YYYYMMDD'){
	$sql_animal[]="update animal2 a1 set birth_dt=substring(birth_dt from 1 for 4)||'-'||substring(birth_dt from 5 for 2)||'-'||substring(birth_dt from 7 for 2)";
	}
	elseif($birth_dt=='DDMMYYYY'){
	$sql_animal[]="update animal2 a1 set birth_dt=substring(birth_dt from 5 for 4)||'-'||substring(birth_dt from 3 for 2)||'-'||substring(birth_dt from 1 for 2)";
	}
}
//Change the sex code (depending on what the user has entered)
if(isset($_POST['male_code']) && $_POST['male_code']!='M'){
$sql_animal[]="update animal2 set sex='M'
where sex='".$_POST['male_code']."'";
}
if(isset($_POST['female_code']) && $_POST['female_code']!='F'){
$sql_animal[]="update animal2 set sex='F'
where sex='".$_POST['female_code']."'";
}
//devide by 100 if user has put in % (introgression, inbreeding)
if(isset($_POST['introgression']) && $_POST['introgression']!=1){
$sql_animal[]="update animal2 set introgression=introgression/".$_POST['introgression']."";
}
if(isset($_POST['introgression2']) && $_POST['introgression2']=='calc'){
$sql_animal[]="update animal2 set introgression=1-introgression";
}
if(isset($_POST['inb_gen']) && $_POST['inb_gen']!=1){
$sql_animal[]="update animal2 set inb_gen=inb_gen/".$_POST['inb_gen']."";
}
$sql_animal[]="update animal2 set sire_id='unknown_sire' where sire_id is null or sire_id='0'";
$sql_animal[]="update animal2 set dam_id='unknown_dam' where dam_id is null or dam_id='0'";
$sql_animal[]="update animal2 set sex='F' where sex is null and id1 in (select dam_id from animal2)";
$sql_animal[]="update animal2 set sex='M' where sex is null and id1 in (select sire_id from animal2)"; 
$sql_animal[]="delete from animal2 where sex is null";
$sql_animal[]="alter table animal2 rename column id1 to db_animal";
$sql_animal[]="alter table animal2 drop column id2";
$sql_animal[]="alter table animal2 drop column id3";
$sql_animal[]="alter table animal2 rename column sire_id to db_sire";
$sql_animal[]="alter table animal2 rename column dam_id to db_dam";
//execute all queries in the $sql_animal array
for($j=0;$j<count($sql_animal);$j++){
$result=pg_query($sql_animal[$j]);
	if($result){
	    //success
	}
	else{
	    //echo pg_last_error($dbh);
		header("Location:error.php?pgsql=".$j."");
		exit();
	}
}
//copy to file
$date=date('Y-m-d');
mkdir("/var/lib/postgresql/incoming/".$date, 0777);
$param=array();
$param[]="email=solange.duruz@epfl.ch";
$param[]="breed=".$breed_name;
$param[]="male=M";
$param[]="female=F";
$param[]="pedfile=datafile";
$param[]="dateformat=YYYY-MM-DD";
$param[]="datesep=-";
$param[]="get_tar=0";
$j=0;
while ($j<count($param)){
	exec("echo ".$param[$j]." >> /var/lib/postgresql/incoming/".$date."/param");
	$j++;
}
//exec("chmod -R 777 '/var/lib/postgresql/incoming/".$date."'");
//$sql_copy="\COPY animal2 TO '/var/www/html/genmon-ch/Data_files/datafile' WITH (NULL '', DELIMITER '|')";
//pg_query($sql_copy);
$data=pg_copy_to($dbh, 'animal2', '|', '');
file_put_contents('/var/www/html/genmon-ch/Data_files/datafile', $data);
$sql_update_cultvalue="UPDATE summary SET breed_cultural_value=".$cultural_score." WHERE breed_id=".$breed_id;
$sql_update_farmtrend="UPDATE summary SET breed_num_farms_trend=".round($farm_trend,2)." WHERE breed_id=".$breed_id;
$sql_update_cryovalue="UPDATE summary SET cryo_cons=".$cryo_score." WHERE breed_id=".$breed_id;
pg_query($sql_update_cultvalue);
pg_query($sql_update_farmtrend); 
pg_query($sql_update_cryovalue);
exec("cp '/var/www/html/genmon-ch/Data_files/datafile' '/var/lib/postgresql/incoming/".$date."/datafile'");
header("Location:PopRep.php?breed_id=".$breed_id);
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
?>
