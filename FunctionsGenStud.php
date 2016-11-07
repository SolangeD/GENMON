<?php

//***************************************************************************************************************************
//*************				FUNCTIONS	ADD   STUDY			*************************************
//*************************************************************************************************************************** 

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

/*function txt2dataBase($USER,$STUDREF,$fileName,$DESC,$TYPE,$wwwDataDirectory,$dbh) {
	if (isset($_GET[ 'breed_id' ])){
	$breed_id = $_GET[ 'breed_id' ];
	}
	if (isset($_POST[ 'breed_id' ])){
	$breed_id = $_POST[ 'breed_id' ];
	}
	if (isset($_POST[ 'breed_name' ])){
	$breed_name = $_POST[ 'breed_name' ];
	}
	$text = readTxtFile($wwwDataDirectory . $fileName . ".csv");	// read file
	if ($text == null) {
		header("Location:AddStudy.php?error=fileformat");
		return;
	}

	$lines = explode("\n", $text);	// split the file with respect to the lines
	
	$nrLin = count($lines)-1;		// number of lines containing data (wo headers)
	
	//$colNamesTxt = trim($lines[0]); // Deletes tabs and space at the end of the line
	$colNamesTxt = $lines[0];
	$colNamesArray = explode(",", $colNamesTxt);	// put the column names in an array; before \t
	if($colNamesArray[5]==null){  //The file must contain 6 columns
	header("Location:AddStudy.php?error=fileformat");
	return;}
	
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

	$sql="CREATE TABLE animal2
	(
	  db_animal text,
	  db_sire text,
	  db_dam text,
	  birth_dt int,
	  sex char(1),
	  plz int,
	  introgression real,
	  inb_gen real,
	  cryo_cons boolean
	);";
	pg_query($sql);

	//Copy the file in the (new-created) table animal2
	pg_copy_from($dbh, "animal2", $linesOK, ";");
	



	$sql="CREATE TABLE animal
	(
	  db_animal bigint,
	  db_sire bigint,
	  db_dam bigint,
	  birth_dt date,
	  sex integer,
	  plz integer,
	  introgression real,
	  inb_gen real,
	  cryo_cons boolean
	);";
	
	$sql2="ALTER TABLE animal OWNER TO geome_admin; ";

	pg_query($sql);		// submit SQL query	


	//deletes the null values (would make PopRep crash)
	$sql_delete_null="delete from animal2 
		where db_sire is null
		or db_sire='0'
		or db_dam is null
		or db_dam='0'
		or birth_dt is null
		or sex is null";
	pg_query($sql_delete_null);

	//change the animal ID to integer through the table transfer
	$sql_transfer = array();
	$sql_transfer[]="drop table transfer";
	$sql_transfer[]="create table transfer (ext_animal text, db_animal serial)";
	$sql_transfer[]="insert into transfer (ext_animal) (select db_animal from animal2 order by birth_dt)";
	$sql_transfer[]="update animal2 a set db_animal=(select b.db_animal from transfer b where a.db_animal=b.ext_animal)";
	$sql_transfer[]="insert into transfer (ext_animal) (
		SELECT distinct db_sire 
		FROM animal2 b
		LEFT JOIN (select d.ext_animal from transfer d) c ON b.db_sire = c.ext_animal
		WHERE c.ext_animal IS NULL)";
	$sql_transfer[]="update animal2 a set db_sire=(select b.db_animal from transfer b where a.db_sire=b.ext_animal)";
	$sql_transfer[]="insert into transfer (ext_animal) (
		SELECT distinct db_dam 
		FROM animal2 b
		LEFT JOIN (select d.ext_animal from transfer d) c ON b.db_dam = c.ext_animal
		WHERE c.ext_animal IS NULL)";
	$sql_transfer[]="update animal2 a set db_dam=(select b.db_animal from transfer b where a.db_dam=b.ext_animal)";
	$i=0;
	while ($sql_transfer[$i]){
		pg_query($sql_transfer[$i]);
		$i++;
	}

	//put in the real animal table. The birth date is set to the 1. of January for all animals. the sex is transformed into numeric values as coded in the code table M->2 F->3
	$sql_sex="update animal2 a set sex=
		(select c.db_code from codes c
		where c.short_name=a.sex
		or c.db_code=cast(a.sex as numeric))";
	pg_query($sql_sex);
	$sql_animal="insert into animal (db_animal, db_sire, db_dam, birth_dt, sex, plz, introgression, inb_gen, cryo_cons) 
		(select cast(a2.db_animal as numeric), cast(a2.db_sire as numeric), cast(a2.db_dam as numeric), cast(a2.birth_dt||'-01-01' as date) , cast(a2.sex as numeric), a2.plz, a2.introgression, a2.inb_gen, a2.cryo_cons 
		from animal2 a2)";
	pg_query($sql_animal);
	$sql_drop_animal2="drop table animal2";
	pg_query($sql_dropt_animal2);
	//put the breed ID for popRep
	$sql_col_breed="ALTER TABLE animal ADD COLUMN db_breed bigint";
	pg_query($sql_col_breed);
	$sql_add_breed="UPDATE animal SET db_breed=".$breed_id.""; 
	pg_query($sql_add_breed);

	//add data already in the database (if exists...). Needs to be changed
	/*$sql_table_name="select * from information_schema.tables"; //return the name of all tables
	$tables=pg_query($sql_table_name);
	while (pg_fetch_result($table, $j, 2)){
		if (pg_fetch_result($table, $j, 2)=="breed".$breed_id."_data"){
			$sql_add_ex_data="INSERT INTO animal VALUES (SELECT * FROM breed".$breed_id."_data)";
		}
	}*/

/*
	return ;
}*/

?>
