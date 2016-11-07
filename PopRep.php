<?php
//This page formats the animal table (uploaded in the GenAnimal.php -from assisted upload- or GenStuDB.php -directly right format-) in the way PopRep likes it (only integer for animal IDs, ordered by birth_dt, ...) and run the PopRep code
//At the end, it aggregates things for the GenMon application by PLZ,.. store restults in the summary table
include("connectDataBase.php");
$dbh = db_connect();	
if(isset($_GET['breed_id'])){
$breed_id=$_GET['breed_id'];
}
if(isset($_POST['breed_id'])){
$breed_id=$_POST['breed_id'];
}
if(isset($_SESSION['user'])){
	$user=$_SESSION['user'];
}
$sql_breed_name="select short_name from codes where db_code=".$breed_id."";
$breed_name0=pg_query($sql_breed_name);
$breed_name=pg_fetch_result($breed_name0,0,0);


//Run the poprep code
exec('/home/popreport/production/apiis/bin/./process_uploads.sh -i /var/lib/postgresql/incoming -l /var/log/popreport.log -u www-data -g www-data -a /home/popreport/production/apiis');

//Get the name of the project
$dbh=db_connect();
$sql_dbname="SELECT datname FROM pg_database where datname like 'PPP%'";
$dbname0=pg_query($sql_dbname);
$project_name=pg_fetch_result($dbname0, 0, 0);

//transfer most important tables (temp tables are deleted right after)

//Rename important table
$table_name_oldnew=array(array("breed".$breed_id."_inbryear", "tmp2_table3"),
	array("breed".$breed_id."_inb_year_sex", "tmp2_table2"),
	array("breed".$breed_id."_ne", "tmp2_ne"),
	array("breed".$breed_id."_pedcompl", "tmp2_pedcompl"),
	array("breed".$breed_id."_ne_deltaF", "tmp2_table5"),
	array("breed".$breed_id."_transfer", "transfer"),
	array("breed".$breed_id."_data", "animal"),
	array("gene_stuff", "gene_stuff"),
	array("tmp1_gen", "tmp1_gen")); //contains the new name and the old name

$j=0;
while ($j<count($table_name_oldnew)){
		$sql_drop_table="DROP TABLE if exists ".$table_name_oldnew[$j][1];
		$sql_drop_table2="DROP TABLE if exists ".$table_name_oldnew[$j][0];
		$sql_drop_table3="DROP TABLE if exists apiis_admin.".$table_name_oldnew[$j][1];
		$sql_drop_table4="DROP TABLE if exists apiis_admin.".$table_name_oldnew[$j][0];
		pg_query($sql_drop_table);
		pg_query($sql_drop_table2);
		pg_query($sql_drop_table3);
		pg_query($sql_drop_table4);
		db_disconnect($dbh);
		exec('pg_dump -t '.$table_name_oldnew[$j][1].' -U apiis_admin --no-tablespaces -w '.$project_name.' | psql -U geome_admin -w GenMon_CH');
		$dbh=db_connect();
		$sql_change_schema="ALTER TABLE apiis_admin.".$table_name_oldnew[$j][1]." SET SCHEMA public";
		pg_query($sql_change_schema);
		if($table_name_oldnew[$j][1]<>$table_name_oldnew[$j][0]){
			$sql_rename_table="ALTER TABLE ".$table_name_oldnew[$j][1]." RENAME TO ".$table_name_oldnew[$j][0];
			$result=pg_query($sql_rename_table);
			if($j==0 && $result==FALSE){
				exec('mv `ls /var/lib/postgresql/projects/'.$project_name.'/*.pdf | head -1` /var/www/html/genmon-ch/pdf/error-'.$breed_name.'.pdf');
				exec('rm -R /var/lib/postgresql/projects/'.$project_name);
				exec('dropdb -U apiis_admin -w '.$project_name);
				$_SESSION['breed_name']=$breed_name;
				header("Location:error.php?error=poprep2");
				exit();
			}
		}

	$j++;
}
$index_name_oldnew=array(array("idx_transfer_1_".$breed_id, "idx_transfer_1"),
	array("idx_transfer_2_".$breed_id, "idx_transfer_2"),
	array("uidx_animal_1_".$breed_id, "uidx_animal_1"),
	array("uidx_animal_rowid_".$breed_id, "uidx_animal_rowid"),
	array("uidx_pk_transfer_".$breed_id, "uidx_pk_transfer"),
	array("uidx_transfer_rowid_".$breed_id, "uidx_transfer_rowid"));
$j=0;
while ($j<count($index_name_oldnew)){
	$sql_rename_index="ALTER INDEX if exists ".$index_name_oldnew[$j][1]." RENAME TO ".$index_name_oldnew[$j][0];
	pg_query($sql_rename_index);
	$j++;
}
pg_query("UPDATE breed".$breed_id."_data SET db_sex=2 where db_sex=117"); //in the other database, sex saved as 117 for males and 118 for females
pg_query("UPDATE breed".$breed_id."_data SET db_sex=3 where db_sex=118");
//Also the breed id is always 119...

//change -9999 data to null data (if no data would not be uploaded for plz, intro, inb_gen and cryo)
//To be checked
$columns=array('plz', 'introgression', 'inb_gen', 'cryo');
for ($i = 0; $i < count($columns); $i++) {
$sql_no_data="update breed".$breed_id."_data set ".$columns[$i]."=NULL where ".$columns[$i]."=-9999";
pg_query($sql_no_data);
}


	//Update the effective population size (Ne) table ! To put back
	$sql_add_deltaF = "insert into breed".$breed_id."_ne (method) values ('Ne_DeltaFp')";
	pg_query($sql_add_deltaF);
	$sql_ne_deltaF = "update breed".$breed_id."_ne set ne=(select avg(ne) from breed".$breed_id."_ne_deltaF 
where year > (select max(year)- (SELECT round(pop,0) FROM tmp1_gen ORDER BY year DESC OFFSET 3 LIMIT 1) from breed".$breed_id."_ne_deltaF))
where method='Ne_DeltaFp'"; 
	pg_query($sql_ne_deltaF);

	//Add the inbreeding to all animals from the animal table
	$sql_breed_data=array();
	//$sql_breed_data[] = "drop table if exists breed".$breed_id."_data";
	//$sql_breed_data[] = "create table breed".$breed_id."_data as (select * from animal)";
	$sql_breed_data[]="alter table breed".$breed_id."_data add column inbreeding real";
	$sql_breed_data[]="update breed".$breed_id."_data set inbreeding = 
	(select i.inbreeding
	from gene_stuff i
	where breed".$breed_id."_data.db_animal=i.db_animal)";
	$i=0;
	while ($i<count($sql_breed_data)){
		pg_query($sql_breed_data[$i]);
		$i++;
	}

	// Put the inbreeding coefficient by plz. Calc mean/max inb  and number individuals over the last generation interval (GI)
	//know the last year of data
	$sql_max_year="SELECT distinct max(EXTRACT(YEAR FROM birth_dt)) as max_year FROM breed".$breed_id."_data";
	$max_year0 = pg_query($sql_max_year);
	$max_year=pg_fetch_result($max_year0,0,0);

	//know the generation interval
	$sql_GI="SELECT round(pop,0) FROM tmp1_gen ORDER BY year DESC OFFSET 3 LIMIT 1"; 
	$GI0=pg_query($sql_GI);
	$GI=pg_fetch_result($GI0,0,0);

	//Create the bree_inb_plz table (with mean inbreeding/introgression and # animal over last GI per plz)
	$sql0="DROP TABLE if exists breed".$breed_id."_inb_plz";
	$sql1="CREATE TABLE breed".$breed_id."_inb_plz (plz int references plzo_plz(plz), mean_inb_lastgi real, max_inb_lastgi real, num_ind_lastgi int, mean_inb_gen_lastgi real, mean_introgr_lastgi real)"; //with a foreign key on plzo_plz
	$sql2="INSERT INTO breed".$breed_id."_inb_plz (select plz from plzo_plz)";
	pg_query($sql0);
	pg_query($sql1);
	pg_query($sql2);

		//mean inbreeding
	$sql_mean= "UPDATE breed".$breed_id."_inb_plz
	SET mean_inb_lastgi = 
	(select q.in from 
			(select avg(bd.inbreeding) as in, bd.plz as p 
			from breed".$breed_id."_data bd
			where extract(year from bd.birth_dt)>=(".$max_year."-".$GI.")
			group by bd.plz) q 
	where q.p=breed".$breed_id."_inb_plz.plz)";
	pg_query($sql_mean);
		//max inbreeding
	$sql_max= "UPDATE breed".$breed_id."_inb_plz
	SET max_inb_lastgi = 
	(select q.in from 
			(select max(bd.inbreeding) as in, bd.plz as p 
			from breed".$breed_id."_data bd
			where extract(year from bd.birth_dt)>=(".$max_year."-".$GI.")
			group by bd.plz) q 
	where q.p=breed".$breed_id."_inb_plz.plz)";
	pg_query($sql_max);
		//Number of individuals
	$sql_numInd= "UPDATE breed".$breed_id."_inb_plz
	SET num_ind_lastgi = 
	(select q.in from 
			(select count(*) as in, bd.plz as p 
			from breed".$breed_id."_data bd
			where extract(year from bd.birth_dt)>=(".$max_year."-".$GI.")
			group by bd.plz) q 
	where q.p=breed".$breed_id."_inb_plz.plz)";
	pg_query($sql_numInd);
		//mean inbreeding (from genetic data)
	$sql_mean_gen= "UPDATE breed".$breed_id."_inb_plz
	SET mean_inb_gen_lastgi = 
	(select q.in from 
			(select avg(bd.inb_gen) as in, bd.plz as p 
			from breed".$breed_id."_data bd
			where extract(year from bd.birth_dt)>=(".$max_year."-".$GI.")
			group by bd.plz) q 
	where q.p=breed".$breed_id."_inb_plz.plz)";
	pg_query($sql_mean_gen);
		//mean introgression
	$sql_mean_introgr= "UPDATE breed".$breed_id."_inb_plz
	SET mean_introgr_lastgi = 
	(select q.in from 
			(select avg(bd.introgression) as in, bd.plz as p 
			from breed".$breed_id."_data bd
			where extract(year from bd.birth_dt)>=(".$max_year."-".$GI.")
			group by bd.plz) q 
	where q.p=breed".$breed_id."_inb_plz.plz)";
	pg_query($sql_mean_introgr);

		//introgression (mean, max, min, std) by year
	$sql_drop_intryear="DROP TABLE if exists breed".$breed_id."_intryear";	
	$sql_intryear="create table breed".$breed_id."_intryear as
(select q.year, count(*) as num, round(cast(avg(q.introgression) as numeric),3) as av, round(cast(max(q.introgression) as numeric),3) as max, round(cast(min(q.introgression) as numeric),3) as min, round(cast(stddev(q.introgression) as numeric),3) as std
from 
	(select extract(year from birth_dt) as year, introgression
	from breed".$breed_id."_data 
	where introgression is not null) q
group by q.year
order by q.year)";
	pg_query($sql_drop_intryear);
	pg_query($sql_intryear);

	//Update summary table
	$sql_summary1="SELECT sum(a_avg*number)/sum(number) as inb_avg, sum(number) FROM breed".$breed_id."_inbryear WHERE year != 'unknown' and cast(year as integer) >=(".$max_year."-".$GI.")"; //breed".$breed_id."_inbryear=tmp2_table3 
	$summary1=pg_query($sql_summary1);
	$inb_avg=round(pg_fetch_result($summary1, 0, 0),4);
	$sql_breed_summary=array();
	$sql_breed_summary[] = "UPDATE summary SET last_year = ".$max_year."
		where breed_id=".$breed_id."";
	$sql_breed_summary[] = "UPDATE summary SET avg_inb = ".$inb_avg."
		where breed_id=".$breed_id."";
	$sql_breed_summary[] = "UPDATE summary SET num_ind = ".pg_fetch_result($summary1, 0, 1)."
		where breed_id=".$breed_id."";
	$sql_breed_summary[] = "UPDATE summary SET gi=".$GI." where breed_id=".$breed_id."";
	$sql_breed_summary[] = "UPDATE summary SET ne=null where breed_id=".$breed_id."";
	include("FunctionsCalcIndex.php");
	$min_radius2=Min_radius($breed_id);
	$sql_breed_summary[]="UPDATE summary SET min_radius = ".$min_radius2."
		where breed_id = ".$breed_id."";
	$sql_table_socioec="SELECT table_name
		FROM information_schema.tables
		WHERE table_schema='public'
		AND table_name LIKE 'plz_socioec_%'
		ORDER BY table_name DESC
		LIMIT 1"; //select the last table (most recent one) with socioeconomical data
	$table_socioec0=pg_query($sql_table_socioec);
	$table_socioec=pg_fetch_result($table_socioec0,0,0);
	$sql_breed_summary[] = "UPDATE summary SET index_socio_eco =
		(SELECT round(cast(sum(a.num_ind_lastGI*b.index_socioec)/sum(a.num_ind_lastGI) as numeric),3)
		FROM breed".$breed_id."_inb_plz a, ".$table_socioec." b
		WHERE a.plz=b.plz)
		WHERE breed_id = ".$breed_id."";  //calc the weighted mean over plz
	$sql_breed_summary[]="(SELECT round(cast((aa.plz_value+bb.cult+cc.farm) as numeric),2)
			FROM (SELECT sum(a.index_socio_eco*b.weight) as plz_value
				FROM summary a, thres_weight b
				WHERE b.crit_type='SocioEco'
				AND b.criteria NOT LIKE 'breed%'
				AND b.owner='".$user."'
				AND a.breed_id=".$breed_id.") aa,
				(SELECT d.weight*c.breed_cultural_value as cult
				FROM summary c, thres_weight d
				WHERE d.criteria='breed_cultural_value'
				AND d.owner='".$user."'
				AND c.breed_id=".$breed_id.") bb,
				(SELECT f.weight*e.breed_num_farms_trend as farm
				FROM summary e, thres_weight f
				WHERE f.criteria='breed_num_farms_trend'
				AND f.owner='".$user."'
				AND e.breed_id=".$breed_id.") cc)"; //add to the soceco index the breed dimension
	$sql_breed_summary[] = "UPDATE summary a SET introgression =
		(SELECT round(cast(avg(b.introgression) as numeric),3)
		FROM breed".$breed_id."_data b
		WHERE extract(year from b.birth_dt)>=(".$max_year."-".$GI."))
		WHERE a.breed_id=".$breed_id."";
	//calculate the trend of number of animals
	$sql_max_year="SELECT max(date_part('year',birth_dt)) from breed".$breed_id."_data";
	$res_max_year=pg_query($sql_max_year);
	$max_year=pg_fetch_result($res_max_year,0,0);
	$males=array();
	$females=array();
	$years=array();
	include("FunctionsLinearTrend.php");
	for($i=1;$i<7;$i++){ //calculate over last 5 years (without taking the very last year which might be incomplete)
		$year=$max_year-$i;
		$sql_get_male="SELECT count(*) FROM breed".$breed_id."_data WHERE db_sex=2 AND date_part('year', birth_dt)=".$year; //maybe sex=3
		$sql_get_female="SELECT count(*) FROM breed".$breed_id."_data WHERE db_sex=3 AND date_part('year', birth_dt)=".$year;
		$res_get_male=pg_query($sql_get_male);
		$res_get_female=pg_query($sql_get_female);
		if(!empty(pg_fetch_result($res_get_male,0,0)) && !empty(pg_fetch_result($res_get_female,0,0))){
			$males[]=pg_fetch_result($res_get_male,0,0);	
			$females[]=pg_fetch_result($res_get_female,0,0);	
			$years[]=$year;
		}
	}
	$trend_male=linear_regression($years, $males);
	$trend_female=linear_regression($years, $females);
	$change_male=round(floatval($trend_male[m])/floatval(end($males))*100,2);
	$change_female=round(floatval($trend_female[m])/floatval(end($females))*100,2);
	$sql_breed_summary[]="UPDATE summary SET trend_males=".$change_male." WHERE breed_id=".$breed_id;
	$sql_breed_summary[]="UPDATE summary SET trend_females=".$change_female." WHERE breed_id=".$breed_id;
	$sql_breed_summary[]="UPDATE summary SET ped_compl=(
				select round(avg(completeness)*100,2)
				from breed".$breed_id."_pedcompl
				where generation=6
				and year::integer>(select max(year::integer) from breed".$breed_id."_pedcompl)-(select gi from summary where breed_id=".$breed_id".))
				where breed_id=".$breed_id;
	//Note that cultural_value, num_farms_trend and cultural_value are updated in the GenAnimal.php page
	$i=0;
	while ($i<count($sql_breed_summary)){
		pg_query($sql_breed_summary[$i]);
		$i++;
	}


	//save most important tables (temp tables are deleted right after)

	/*$sql_save_table=array();
	$sql_save_table[]="DROP TABLE if exists breed".$breed_id."_inbryear";
	$sql_save_table[]="ALTER TABLE tmp2_table3 RENAME TO breed".$breed_id."_inbryear"; 
	$sql_save_table[]="DROP TABLE if exists breed".$breed_id."_inb_year_sex";
	$sql_save_table[]="ALTER TABLE tmp2_table2 RENAME TO breed".$breed_id."_inb_year_sex"; 
	$sql_save_table[]="DROP TABLE if exists breed".$breed_id."_ne";
	$sql_save_table[]="ALTER TABLE tmp2_ne RENAME TO breed".$breed_id."_ne"; 
	$sql_save_table[]="DROP TABLE if exists breed".$breed_id."_pedcompl";
	$sql_save_table[]="ALTER TABLE tmp2_pedcompl RENAME TO breed".$breed_id."_pedcompl"; 
	$sql_save_table[]="DROP TABLE if exists breed".$breed_id."_ne_deltaF";
	$sql_save_table[]="ALTER TABLE tmp2_table5 RENAME TO breed".$breed_id."_ne_deltaF"; 
	$sql_save_table[]="DROP TABLE if exists breed".$breed_id."_transfer";
	$sql_save_table[]="ALTER TABLE transfer RENAME TO breed".$breed_id."_transfer"; 
	$sql_drop_table=array();
	$sql_drop_table[]="DROP TABLE if exists gene_stuff";
	$sql_drop_table[]="DROP TABLE if exists tmp1_gen";

	$i=0;
	while ($i<count($sql_drop_table)){
		pg_query($sql_drop_table[$i]);
		$i++;
	}*/

db_disconnect($dbh);
	//delete temp tables
	//exec('/home/lasigadmin/genmon-ch/apiis/bin/./Calc_inb.pl -k 1'); //does not run the code but deletes temporary tables	 
	//exec('/home/lasigadmin/genmon-ch/apiis/bin/./Calc_pop.pl -b '.$breed_name.' -m M -f F -d 1');

exec('mv /var/lib/postgresql/projects/'.$project_name.'/Inbreeding-'.$breed_name.'.pdf /var/www/html/genmon-ch/pdf/Inbreeding-'.$breed_name.'.pdf');
exec('mv /var/lib/postgresql/projects/'.$project_name.'/Population-'.$breed_name.'.pdf /var/www/html/genmon-ch/pdf/Population-'.$breed_name.'.pdf');
exec('rm -R /var/lib/postgresql/projects/'.$project_name);
exec('dropdb -U apiis_admin -w '.$project_name);
exec('rm -R /var/lib/postgresql/incoming/*');

$_SESSION['breed_id']=$breed_id;
header("Location:index.php");
?>
