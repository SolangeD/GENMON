<?php
	include("connectDataBase.php");
	$dbh=db_connect();
//Stats for LASI variables
$sql1="select percent_change_wsl, demog_balance,	unemployment_rate,percent_farmer,percent_grazing_surface,percent_less_19,percent_more_65,evol_job_primary_sector from plz_socioec_2014";
	$res1=pg_query($sql1);
	$n=pg_num_fields($res1);

	for ($a=0; $a<$n; $a++){
echo pg_field_name($res1, $a)."<br/>";
$sql2="select stddev_samp(".pg_field_name($res1, $a).") as std".$a.", avg(".pg_field_name($res1, $a).") as avg".$a.", min(".pg_field_name($res1, $a).") as min".$a.", max(".pg_field_name($res1, $a).") as max".$a." from plz_socioec_2014";
$result=pg_query($sql2);

if(pg_num_rows($result)!=0){
		echo "<table>";
		for($i=0;$i<pg_num_rows($result);$i++){
			if($i==0){
				echo "<thead><tr>";
			}
			if($i==0){
				for($j=0;$j<pg_num_fields($result);$j++){
					
					echo "<th> ".pg_field_name($result, $j)." </th>";
					if($j==(pg_num_fields($result)-1)){
						echo "</tr> </thead> <tbody>";
					}
				}
			}
			for($j=0;$j<pg_num_fields($result);$j++){
				if($j==0){
					echo "<tr>";
				}
				echo "<td>".pg_fetch_result($result, $i, $j)."</td>";
				
			}
			if($j==(pg_num_fields($result)-1)){
				echo "</tr>";
			}
		}
		echo "</tbody></table>";
	}





}

/*
//$sql="select stddev_samp(percent_change_wsl) from plz_socioec_2014";
$sql="select corr(percent_change_wsl, demog_balance) from plz_socioec_2014";
	//$sql="SELECT table_name FROM information_schema.tables WHERE table_schema='public' order by table_name";
	$result=pg_query($sql1);

	if(pg_num_rows($result)!=0){
		echo "<table>";
		for($i=0;$i<pg_num_rows($result);$i++){
			if($i==0){
				echo "<thead><tr>";
			}
			if($i==0){
				for($j=0;$j<pg_num_fields($result);$j++){
					
					echo "<th> ".pg_field_name($result, $j)." </th>";
					if($j==(pg_num_fields($result)-1)){
						echo "</tr> </thead> <tbody>";
					}
				}
			}
			for($j=0;$j<pg_num_fields($result);$j++){
				if($j==0){
					echo "<tr>";
				}
				echo "<td>".pg_fetch_result($result, $i, $j)."</td>";
				
			}
			if($j==(pg_num_fields($result)-1)){
				echo "</tr>";
			}
		}
		echo "</tbody></table>";
	}
	//alter table thres_weight add column owner text
	//alter table thres_weight add column species text
	//alter table summary add column owner text
	//alter table summary add column species text
	//alter table summary add column public integer
/*
//To calculate correlations
	/*$sql1="select percent_change_wsl, demog_balance,	unemployment_rate,percent_farmer,percent_grazing_surface,percent_less_19,percent_more_65,evol_job_primary_sector from plz_socioec_2014";
	$res1=pg_query($sql1);
	$n=pg_num_fields($res1);
echo $n."   ";

$res=array();

	for ($a=0; $a<$n; $a++){
for ($b=0; $b<$n; $b++){
$sql2="select corr(".pg_field_name($res1, $a).", ".pg_field_name($res1, $b).") from plz_socioec_2014";
$res2=pg_query($sql2);

if($a<$b){
$res[$a][$b]=pg_fetch_result($res2,0,0);
echo $res[$a][$b]."<br/>";


}
}
}
*/
?>