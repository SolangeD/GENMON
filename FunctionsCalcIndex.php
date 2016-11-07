<?php
function IndexCalc($breed_id,$crit_type,$owner,$species){
$sql="select *
		from thres_weight where owner='".$owner."' and crit_type='".$crit_type."' and species='".$species."'
		union
		select * from thres_weight
		where owner='".$owner."' and  crit_type='".$crit_type."' and species='default'
		and not exists (select * from thres_weight where owner='".$owner."' and crit_type='".$crit_type."' and species='".$species."')";
$sql1=pg_query($sql);
$num_rows=pg_num_rows($sql1);
$index1=0;
$sum_weight=0;
for($i=0;$i<$num_rows;$i++){
	$name=pg_fetch_result($sql1, $i, 0);
	$sql2="select t.weight, t.t1, t.t2 from thres_weight t
	where crit_type='".$crit_type."' and t.criteria='".$name."' and owner='".$owner."' and species='".$species."'
	union
	select t.weight, t.t1, t.t2 from thres_weight t
	where crit_type='".$crit_type."' and t.criteria='".$name."' and owner='".$owner."' and species='default'
	and not exists (select t.weight, t.t1, t.t2 from thres_weight t
		where crit_type='".$crit_type."' and t.criteria='".$name."' and owner='".$owner."' and species='".$species."')
	";
	$w=pg_query($sql2);
	$weight=pg_fetch_result($w,0,0);
	$t1=pg_fetch_result($w,0,1);
	$t2=pg_fetch_result($w,0,2);

	$sql_summary0 = "select ".$name." from summary where breed_id = ".$breed_id." and ".$name." is not null";//
	$sql_summary = pg_query($sql_summary0);
	if (pg_num_rows($sql_summary)<>0){
		$value=min(max((pg_fetch_result($sql_summary,0,0)-$t1)*1/($t2-$t1),0),1);
		$index1+=$value*$weight;
		$sum_weight+=$weight;
	}
}
if ($sum_weight==0){ //prevent division by 0
	$sum_weight=1;
}
$index1=round($index1/$sum_weight,3);

$sql_set_index="update summary set index_".$crit_type." = ".$index1." where breed_id = ".$breed_id;
pg_query($sql_set_index);
return $index1;
}

function Min_radius($breed_id){

$sql_distance= "select st_distance(st_setsrid(a.wmc,3857), st_setsrid(pc.centroid,3857)) as distance, p.num_ind_lastgi
	from (select st_geomfromtext('POINT(' || sum(st_x(st_setsrid(pc.centroid,3857))*p.num_ind_lastgi)/sum(p.num_ind_lastgi) ||' ' || sum(st_y(st_setsrid(pc.centroid,3857))*p.num_ind_lastgi)/sum(p.num_ind_lastgi) || ')') as wmc
	from (select p1.num_ind_lastgi as num_ind_lastgi, p1.plz as plz from breed".$breed_id."_inb_plz p1 where p1.num_ind_lastgi is not null) p, plz_centroid pc where p.plz=pc.plz
	) a, plz_centroid pc, (select * from breed".$breed_id."_inb_plz where num_ind_lastgi is not null) p
	where pc.plz=p.plz
	order by distance"; //Calculate the WMC of the breed and orders the plz according to the distance to the WMC
$distance1=pg_query($sql_distance);
$sql_num_ind="select sum(p.num_ind_lastgi)
from breed".$breed_id."_inb_plz p";
$num_ind0=pg_query($sql_num_ind);
$num_ind_total=pg_fetch_result($num_ind0,0,0);
$num_ind=0;
$num_ind_percent=0;
$i=0;
while($num_ind_percent<75){
	$num_ind+=pg_fetch_result($distance1, $i, 1);
	$num_ind_percent=$num_ind/$num_ind_total*100;
	$min_radius2=pg_fetch_result($distance1, $i, 0)/1000; //radius around WMC containing 75% of animals in km
	
	$i++;
	}
$min_radius2=round($min_radius2, 2);
return $min_radius2;
}

function IndexSocioEcPLZ($year, $user){
//get the weights and thresholds in an array
$sql="select * from thres_weight where crit_type='SocioEco' and owner='".$user."' and criteria not like 'breed%'";
$sql1=pg_query($sql);
$num_rows1=pg_num_rows($sql1);
$weight=array();
$t1=array();
$t2=array();
for($i=0;$i<$num_rows1;$i++){
	$name=pg_fetch_result($sql1, $i,0);
	$sql2="select t.weight, t.t1, t.t2 from thres_weight t
	where crit_type='SocioEco'
	and t.criteria='".$name."'
	and owner='".$user."'";
	$w=pg_query($sql2);
	$weight[]=pg_fetch_result($w,0,0);
	$t1[]=pg_fetch_result($w,0,1);
	$t2[]=pg_fetch_result($w,0,2);
}
//If index_socio_ec_user does not exist, add this column
$sql_column="select column_name 
from information_schema.columns 
where table_name='plz_socioec_".$year."'
and column_name='index_socioec';"; //and column_name='index_socioec_".$user."';";
$result_column=pg_query($sql_column);
if(pg_num_rows($result_column)==0){
$sql_add="alter table plz_socioec_".$year." add column index_socioec real"; //$sql_add="alter table plz_socioec_".$year." add column index_socioec_".$user." real";
pg_query($sql_add);
}

//calculate the index
$sql_ofs="select plz from plz_socioec_".$year."";
$sql_ofs2=pg_query($sql_ofs);
$num_row2=pg_num_rows($sql_ofs2);
for($j=0;$j<$num_row2;$j++){ //loop on different plz
	$index1=0;
	$sum_weight=0;
	$plz=pg_fetch_result($sql_ofs2,$j,0);
	for($k=0;$k<$num_rows1;$k++){ //loop on criteria
		$name=pg_fetch_result($sql1, $k,0);
		$sql_crit = "select ".$name." from plz_socioec_".$year." where plz = ".$plz." and ".$name." is not null";
		$crit = pg_query($sql_crit);
		if (pg_num_rows($crit)<>0){
			$value=min(max((pg_fetch_result($crit,0,0)-$t1[$k])*1/($t2[$k]-$t1[$k]),0),1); //proportion of satisfaction for the given criteria
			$index1+=$value*$weight[$k]; //sum of the weighted satisfaction value
			$sum_weight+=$weight[$k];
		}
	}
	if ($sum_weight==0){ //prevent division by 0
	$sum_weight=1;
	}	
	$index1=round($index1/$sum_weight,3);
	$sql_set_index="update plz_socioec_".$year." set index_socioec = ".$index1." where plz=".$plz.""; //	$sql_set_index="update plz_socioec_".$year." set index_socioec_".$user." = ".$index1." where plz=".$plz."";
	pg_query($sql_set_index);
}
	$sql_set_index2="update plzo_plz set index_socioec = b.index_socioec from plz_socioec_".$year." b where b.plz=plzo_plz.plz"; //	$sql_set_index="update plz_socioec_".$year." set index_socioec_".$user." = ".$index1." where plz=".$plz."";
	pg_query($sql_set_index2);
	$res_breed=pg_query("select distinct breed_id from summary");
	for($i=0;$i<pg_num_rows($res_breed);$i++){
		$breed_id=pg_fetch_result($res_breed,$i,0);
		$sql_breed_index1= "UPDATE summary SET index_socio_eco =
			(SELECT round(cast(sum(a.num_ind_lastGI*b.index_socioec)/sum(a.num_ind_lastGI) as numeric),3)
			FROM breed".$breed_id."_inb_plz a, plzo_plz b
			WHERE a.plz=b.plz)
			WHERE breed_id = ".$breed_id."";  //calc the weighted mean over plz
		$sql_breed_index2="(SELECT round(cast((aa.plz_value+bb.cult+cc.farm) as numeric),2)
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
		pg_query($sql_breed_index1);
		pg_query($sql_breed_index2);
	}
return;
}
?>
