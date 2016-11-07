<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>GenMon-CH</title>
		<style type="text/css">
		</style>
		<link rel="stylesheet" href="style.css" media="screen"/>
		<!--<script type="text/javascript" src="js/cssrefresh.js"></script>-->
	</head>    
<?php 
include("header.php");
include("connectDataBase.php");
include("FunctionsCalcIndex.php");
$dbh=db_connect();

if (isset($_POST[ 'breed_id' ])){
	$breed_id = $_POST[ 'breed_id' ];
}
/*if (isset($_POST[ 'breed_name' ])){
	$breed_name = $_POST[ 'breed_name' ];
}*/



$sql_long_name="SELECT long_name FROM codes where db_code=".$breed_id.""; 
$sql_long_name2=pg_query($sql_long_name);
if(pg_num_rows($sql_long_name2)!=0){
	$long_name=pg_fetch_result($sql_long_name2, 0, 0);
	$sql_short_name="SELECT short_name FROM codes where db_code=".$breed_id.""; 
	$sql_short_name2=pg_query($sql_short_name);
	$breed_name=pg_fetch_result($sql_short_name2, 0, 0);
}
$sql_owner="SELECT owner FROM summary where breed_id=".$breed_id.""; 
$result_owner=pg_query($sql_owner);
$owner=pg_fetch_result($result_owner, 0, 0);
$sql_species="SELECT species FROM summary where breed_id=".$breed_id.""; 
$result_species=pg_query($sql_species);
$species=pg_fetch_result($result_species, 0, 0);

if(isset($_POST["Ne"])==1){
	$sql_set_Ne="update summary set ne=".$_POST["Ne"]." where breed_id=".$_POST["breed_id"].""; 
	pg_query($sql_set_Ne);
	if(isset($_SESSION['user']) && $_SESSION['user']==$owner){
	$index_demo=IndexCalc($breed_id,'demo', $_SESSION['user'], $species); //FunctionsCalcIndex.php
	$index_final=IndexCalc($breed_id,'final', $_SESSION['user'], $species); //FunctionsCalcIndex.php
	}
}
?>
	<body>
	<div id="page">
	<h2 style="margin-left:50px"><?php echo $long_name.' ('.$breed_name.')';?> </h2><br/>

	<div class="box" style="width:60%;">
	<?php
	if(isset($_SESSION['user'])|| $breed_name=='Test'){

		if($owner==$_SESSION['user'] || $breed_name=='Test'){
	?>
		<form action="GenStuDb.php" method="post" enctype="multipart/form-data"> <!--send file and info to the php file -->
			<b>Add data for this breed (<?php echo $breed_name;?>):</b>
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
			<input type="hidden" name="breed_id" value="<?php echo $breed_id; ?>" />
			<input type="hidden" name="breed_name" value="<?php echo $breed_name; ?>" />
			<input type="submit" value="Add" /> 
		</form>
		See the format in the <a href="tutorial.php">tutorial</a>.
		<form action="AddStudy.php" method="post"> Or go to the assisted upload section 
			<input type="hidden" name="breed_id" value="<?php echo $breed_id;?>">
			<input type="submit" value="Assisted upload" /> 
		</form><br /><br/>
		<?php
		}
		else{
			echo "<b>You need to be the owner of the study to add data to this breed</b><br/><br/><br/>";
		}	
	}

$sql_table_name="SELECT * FROM information_schema.tables WHERE table_name='breed".$breed_id."_inb_plz'";
$table_name0=pg_query($sql_table_name);
$table_inb_plz=pg_num_rows($table_name0);
if($table_inb_plz==1){
?>
		<form method="post" action="GoToMap.php">
			<b>See spatial distribution (<?php echo $breed_name;?>):</b>	
			<input class="button" type="submit" value="Go to map" />
			<input type="hidden" name="breed_id" value="<?php echo $breed_id; ?>" /></form>
		<br /><br />
<?php
}
else{
echo "The <strong>mapping service</strong> is not available for this breed since the spatial information has not been entered<br/><br/>";
}
?>
<a style="color:black;" href="pdf/Population-<?php echo $breed_name; ?>.pdf" target="_blank"><strong>PDF: PopRep Population Report <?php echo $breed_name; ?></strong></a><br /><br />
<a style="color:black;" href="pdf/Inbreeding-<?php echo $breed_name; ?>.pdf" target="_blank"><strong>PDF: PopRep Inbreeding Report</strong></a>
	</div> <!-- end box-->
	
<br />
<?php
$sql_table_name="SELECT * FROM information_schema.tables WHERE table_name='breed".$breed_id."_data'";
$table_name0=pg_query($sql_table_name);
$table_inb_plz=pg_num_rows($table_name0);
if($table_inb_plz==1){
	$sql_gi="SELECT gi FROM summary where breed_id=".$breed_id."";
	$sql_gi2=pg_query($sql_gi);
	$gi=pg_fetch_result($sql_gi2,0,0);
	$sql_min_radius="SELECT min_radius FROM summary where breed_id=".$breed_id."";
	$sql_min_radius2=pg_query($sql_min_radius);
	$min_radius=pg_fetch_result($sql_min_radius2,0,0);
	$sql_cultural_score="SELECT breed_cultural_value FROM summary where breed_id=".$breed_id;
	$res_cultural_score=pg_query($sql_cultural_score);
	$cultural_score=pg_fetch_result($res_cultural_score,0,0);
	$sql_num_farms_trend="SELECT breed_num_farms_trend FROM summary where breed_id=".$breed_id;
	$res_num_farms_trend=pg_query($sql_num_farms_trend);
	$num_farms_trend=pg_fetch_result($res_num_farms_trend,0,0);
	$sql_trend_males="SELECT trend_males FROM summary where breed_id=".$breed_id;
	$res_trend_males=pg_query($sql_trend_males);
	$trend_males=pg_fetch_result($res_trend_males,0,0);

	$sql_trend_females="SELECT trend_females FROM summary where breed_id=".$breed_id;
	$res_trend_females=pg_query($sql_trend_females);
	$trend_females=pg_fetch_result($res_trend_females,0,0);
	
	$sql_cryo_cons="SELECT cryo_cons FROM summary where breed_id=".$breed_id;
	$res_cryo_cons=pg_query($sql_cryo_cons);
	$cryo_cons=pg_fetch_result($res_cryo_cons,0,0);
	?>
	<h3>General information</h3>
	<hr/>
	<div id="margin">
		<i>Table: general information</i>
	<table>
			<thead>
			<tr>
			<th>Information</th> 
			<th>Value</th> 
			<th>Unit</th> 
			</tr>
			</thead>
			<tbody>

			<tr>
			<td>Generation interval</td>
			<td> <?php echo $gi;?> </td>
			<td>years</td>
			</tr>
			<tr>
			<td>Radius containing min. 75% of the population:</td>
			<td><?php echo $min_radius;?></td>
			<td>km</td>
			</tr>
			<tr>
			<td>Cultural score </br>(presence or not of a cultural value of the breed and the evolution over the recent past)</td>
			<td><?php echo $cultural_score;?></td>
			<td>(between 0 and 1)</td>
			</tr>
			<tr>
			<td>Trend of the number of farms over the last five years</td>
			<td><?php echo $num_farms_trend;?></td>
			<td> </td>
			</tr>
			<tr>
			<td>Trend of the number of males (% change of males over the last five years)</td>
			<td> <?php echo $trend_males;?></td>
			<td>(% change per year)</td>
			</tr>
			<tr>
			<td>Trend of the number of females (% change of females over the last five years)</td>
			<td> <?php echo $trend_females;?></td>
			<td>(% change per year)</td>
			</tr>
			<tr>
			<td>Cryo-conservation score </br>(presence or not of frozen semen and of a real cryoconservation management plan)</td>
			<td><?php echo $cryo_cons;?></td>
			<td>(between 0 and 1)</td>
			</tr>
			</tbody>
		</table>
	<!--<strong>Generation interval: <?php //echo $gi;?> years</strong>
	<br />
	<strong>Radius containing min. 75% of the population: <?php //echo $min_radius;?> km</strong>
	<br />
	<strong>Cultural score (taking into account the presence or not of a cultural value of the breed and the evolution over the recent past): <?php //echo $cultural_score;?> (between 0 and 1)</strong>
	<br />	
	<strong>Trend of the number of farms over the last five years: <?php //echo $num_farms_trend;?> </strong>
	<br />
	<strong>Trend of the number of males (% change of males over the last five years): <?php //echo $trend_males;?> (% change per year)</strong>
	<br />
	<strong>Trend of the number of females (% change of females over the last five years): <?php //echo $trend_females;?> (% change per year)</strong>
	<br />
	<strong>Cryo-conservation score (taking into account the presence or not of frozen semen and of a real cryoconservation management plan): <?php //echo $cryo_cons;?> (between 0 and 1)</strong>!-->
	<br /><br/>
	</div> <!--margin-->
	<h3>Inbreeding</h3>
	<hr/>	
	<div id="margin">
	<i style="size:bigger">Table: inbreeding per year</i>
		<table>
			<thead>
			<tr>
			<th>Year</th> 
			<th>Number <br />animal</th> 
			<th>Minimum <br />inbreeding</th>
			<th>Maximum <br />inbreeding</th>
			<th>Average <br />inbreeding</th>
			<th>STD <br /> inbreeding</th>
			</tr>
			</thead>
			<tbody>
	<?php	
	$sql_inbryear="SELECT * FROM breed".$breed_id."_inbryear order by year desc limit 20";
	$data=pg_query($sql_inbryear);
	for($i=0;$i<pg_num_rows($data);$i++){
	?>
			<tr>
			<td><?php echo pg_fetch_result($data, $i, 1); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 2); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 3); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 4); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 5); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 6); ?></td>
			</tr>
	<?php
	}
	?>
			</tbody>
		</table>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<!--<link href="../examples.css" rel="stylesheet" type="text/css">-->
		<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="../../excanvas.min.js"></script><![endif]-->
		<script language="javascript" type="text/javascript" src="flot/jquery.js"></script>
		<script language="javascript" type="text/javascript" src="flot/jquery.flot.js"></script>
	<?php
	$sql_inbryear="SELECT year, a_avg, a_max FROM breed".$breed_id."_inbryear";
	$data=pg_query($sql_inbryear);
	for($j=0;$j<pg_num_rows($data);$j++)
		{
		$array1[] = array(pg_fetch_result($data, $j, 0),pg_fetch_result($data, $j, 1));
		$array2[]= array(pg_fetch_result($data, $j, 0),pg_fetch_result($data, $j, 2));
		}
	?>
	<br/>
	<i>Figure: Maximum and mean inbreeding per year</i>
	<script type="text/javascript">
	$(function () {
		var dataset1 = <?php echo json_encode($array1); ?>;
		var dataset2 = <?php echo json_encode($array2); ?>;
		$.plot($("#placeholder"), [{label: "average inbreeding", data: dataset1 },
					{label: "maximum inbreeding", data: dataset2 }]);
	});
	</script>
		<div id="placeholder" style="width:600px;height:300px;margin-left:15px"></div>
	<br/>
	</div> <!--margin-->
	<h3>Inbreeding by sex</h3>
	<hr />
	<div id="margin">
	<i>Table: inbreeding per year and per sex</i>
	<table>
			<thead>
			<tr>
			<th>Year</th> 
			<th>Total <br />number</th> 
			<th>Inbred <br />number</th> 
			<th>Av <br />inbreeding</th> 
			<th>Number <br />Sires</th> 
			<th>Number <br />inbred sires</th> 
			<th>Av inbreeding<br /> sires</th> 
			<th>Number<br /> dams</th> 
			<th>Number <br />inbred dams</th> 
			<th>Av inbreeding<br /> dam</th>
			</tr>
			</thead>
			<tbody>
	<?php	
	$sql_num="select breed, year as year, off_num, off_bred_num, round(off_bred_inb,4), s_num, s_bred_num, round(s_bred_inb,4), d_num, d_bred_num, round(d_bred_inb,4) 
			  from breed".$breed_id."_inb_year_sex where year notnull  and year>0 order by breed,year desc limit 20"; 
	$data_num=pg_query($sql_num);
	for($i=0;$i<pg_num_rows($data_num);$i++){
	?>

			<tr>
			<td><?php echo pg_fetch_result($data_num, $i, 1); ?></td>
			<td><?php echo pg_fetch_result($data_num, $i, 2); ?></td>
			<td><?php echo pg_fetch_result($data_num, $i, 3); ?></td>
			<td><?php echo pg_fetch_result($data_num, $i, 4); ?></td>
			<td><?php echo pg_fetch_result($data_num, $i, 5); ?></td>
			<td><?php echo pg_fetch_result($data_num, $i, 6); ?></td>
			<td><?php echo pg_fetch_result($data_num, $i, 7); ?></td>
			<td><?php echo pg_fetch_result($data_num, $i, 8); ?></td>
			<td><?php echo pg_fetch_result($data_num, $i, 9); ?></td>
			<td><?php echo pg_fetch_result($data_num, $i, 10); ?></td>
			</tr>
	<?php
	}
	?>
			</tbody>
		</table>
		<br />
	</div> <!--margin-->
	<h3>Effective population size</h3>
	<hr>
	<div id="margin">
	<i>Table: Effective population size according to different computations</i>
		<table>
			<thead>
			<tr>
			<th>Method</th> 
			<th>Ne</th> 
			</tr>
			</thead>
			<tbody>
	<?php 
	$sql_ne="SELECT * FROM breed".$breed_id."_ne"; 
	$data_ne=pg_query($sql_ne);

	for($i=0;$i<pg_num_rows($data_ne);$i++){
	?>
			<tr>
			<td><?php echo pg_fetch_result($data_ne, $i, 0); ?></td>
			<td><?php echo pg_fetch_result($data_ne, $i, 1); ?></td>
			</tr>
	<?php
	}
	?>
			</tbody>
		</table>


	<?php
	$sql_comp="select a.generation, a.breed, a.year, a.number, round((a.completeness*100),1), b.ne 
	from breed".$breed_id."_pedcompl a, breed".$breed_id."_ne_deltaf b 
	where cast(a.year as int)> (select max(cast(c.year as int))-20 from breed".$breed_id."_pedcompl c) 
	and cast(a.year as  numeric)=b.year
	order by generation,year,breed"; //select the completeness over the last 20 years
	$data_comp=pg_query($sql_comp);
	$num_row=pg_num_rows($data_comp);
	$num_year=$num_row/6; //6 generations in PopRep output
	$j=0;
	for($i=0;$i<6;$i++){
			$array_ne[]=array();
		for ($j=0;$j<$num_year;$j++)
			{
			${'array_comp'.$i}[] = array(pg_fetch_result($data_comp, $i*$num_year+$j, 2),pg_fetch_result($data_comp, $i*$num_year+$j, 4));

			$array_ne[]=array(pg_fetch_result($data_comp, $j, 2),pg_fetch_result($data_comp, $j, 5));
			}
	}
	if(isset($_POST['yaxis'])){
		$yaxis=$_POST['yaxis'];
	}
	else{
		$yaxis=300;
	}
	if(isset($_POST['minyaxis'])){
		$minyaxis=$_POST['minyaxis'];
	}
	else{
		$minyaxis=0;
	}
	?>


	<script type="text/javascript">
	$(function () {
		var dataset1 = <?php echo json_encode($array_comp0); ?>;
		var dataset2 = <?php echo json_encode($array_comp1); ?>;
		var dataset3 = <?php echo json_encode($array_comp2); ?>;
		var dataset4 = <?php echo json_encode($array_comp3); ?>;
		var dataset5 = <?php echo json_encode($array_comp4); ?>;
		var dataset6 = <?php echo json_encode($array_comp5); ?>;
		var dataset7 = <?php echo json_encode($array_ne); ?>;
		$.plot($("#placeholder2"), [{label: "comp gen 1 ", data: dataset1 },
					{label: "comp gen 2", data: dataset2 },
					{label: "comp gen 3", data: dataset3 },
					{label: "comp gen 4", data: dataset4 },
					{label: "comp gen 5", data: dataset5 },
					{label: "comp gen 6", data: dataset6 },
					{label: "Ne", data: dataset7, yaxis:2 }], 
						{
	xaxes:[{tickDecimals: 0}],
	yaxes: [ { min: 0 , max: 100}, { min: 0, max: <?php echo $yaxis; ?>,
						position: "right"

					}],
	legend: { container: $("#chartLegend") }
					});
	});

	</script>

	<table style="text-align:center; margin-bottom:20px;border:none;">
	  <tr>
		<td style="text-align:left;" colspan=3"><i>Figure: Effective population size (Ne<sub>&Delta;Fp</sub>) and pedigree completeness</i></td>    
	  </tr>
	  <tr style="background-color:white">
		<td><span style="	
		writing-mode:tb-rl;
		-webkit-transform:rotate(-90deg);
		-moz-transform:rotate(-90deg);
		-o-transform: rotate(-90deg);
		white-space:nowrap;
		display:block;
		width:20px;
	">Ped compl (%)</span></td>

		<td>
		  <div id="placeholder2" style="width:600px;height:300px"></div>
		</td>

		<td><span style="	
		writing-mode:tb-rl;
		-webkit-transform:rotate(-90deg);
		-moz-transform:rotate(-90deg);
		-o-transform: rotate(-90deg);
		white-space:nowrap;
		display:block;
		width:20px;">Ne</span></td>
	   <td><div id="chartLegend"/></td>
	  </tr>

	  <tr>
		<td></td>

		<td>Years</td>

		<td></td>
	  </tr>

	</table>
	Note that if the Ne does not appear in the graph, it might be because the deltaf is negative thus giving no result for the Ne with this method.<br/><br/>
	Want to limit right y-axis?
	<form name="input_yaxis" action="" method="post">
	Min value:<input type="number" name="minyaxis">
	Max value:<input type="number" name="yaxis">
	<input type="hidden" name="breed_id" value="<?php echo $breed_id;?>">
	<input type="submit" value="Change">
	</form> 
<?php
if(isset($_SESSION['user']) && $_SESSION['user']==$owner){
?>
	With the help the information listed above you need to choose the range for the Effective Population size
	<?php 
	//$Ne_range=1;
	$sql_Ne_range="select ne from summary where breed_id=".$breed_id."";
	$Ne_range0=pg_query($sql_Ne_range);
	$Ne_range=pg_fetch_result($Ne_range0,0,0);
	?>
	<form name="input" action="" method="post">
	<input type="radio" name="Ne" value="20" <?php if($Ne_range==20){echo "checked";}?>>&lt;30<br>
	<input type="radio" name="Ne" value="40" <?php if($Ne_range==40){echo "checked";}?>>30-50<br>
	<input type="radio" name="Ne" value="60" <?php if($Ne_range==60){echo "checked";}?>>50-70<br>
	<input type="radio" name="Ne" value="85" <?php if($Ne_range==85){echo "checked";}?>>70-100<br>
	<input type="radio" name="Ne" value="150" <?php if($Ne_range==150){echo "checked";}?>>100-200<br>
	<input type="radio" name="Ne" value="250" <?php if($Ne_range==250){echo "checked";}?>>&gt;200<br>
	<input type="hidden" name="breed_id" value="<?php echo $breed_id;?>">
	<input type="submit" value="Save Ne">
	</form> 
<?php
}
?>
	</div> <!--margin-->
	<br/>
	<h3>Introgression</h3>
	<hr/>	
	<div id="margin">
	<i style="size:bigger">Table: introgression per year</i>
		<table>
			<thead>
			<tr>
			<th>Year</th> 
			<th>Number <br />animal</th> 
			<th>Minimum <br />introgression</th>
			<th>Maximum <br />introgression</th>
			<th>Average <br />introgression</th>
			<th>STD <br /> introgression</th>
			</tr>
			</thead>
			<tbody>
	<?php	
	$sql_intryear="SELECT year, num, min, max, av, std FROM breed".$breed_id."_intryear order by year desc limit 20";
	$data=pg_query($sql_intryear);
	for($i=0;$i<pg_num_rows($data);$i++){
	?>
			<tr>
			<td><?php echo pg_fetch_result($data, $i, 0); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 1); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 2); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 3); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 4); ?></td>
			<td><?php echo pg_fetch_result($data, $i, 5); ?></td>
			</tr>
	<?php
	}
	?>
			</tbody>
		</table>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<!--<link href="../examples.css" rel="stylesheet" type="text/css">-->
		<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="../../excanvas.min.js"></script><![endif]-->
		<script language="javascript" type="text/javascript" src="flot/jquery.js"></script>
		<script language="javascript" type="text/javascript" src="flot/jquery.flot.js"></script>
	<?php
	$sql_intryear="SELECT year, av, max FROM breed".$breed_id."_intryear";
	$data=pg_query($sql_intryear);
	for($j=0;$j<pg_num_rows($data);$j++)
		{
		$array_intr1[] = array(pg_fetch_result($data, $j, 0),pg_fetch_result($data, $j, 1));
		$array_intr2[]= array(pg_fetch_result($data, $j, 0),pg_fetch_result($data, $j, 2));
		}
	?>
	<br/>
	<i>Figure: Maximum and mean introgression per year</i>
	<script type="text/javascript">
	$(function () {
		var dataset_intr1 = <?php echo json_encode($array_intr1); ?>;
		var dataset_intr2 = <?php echo json_encode($array_intr2); ?>;
		$.plot($("#placeholder3"), [{label: "average introgression", data: dataset_intr1 },
					{label: "maximum introgression", data: dataset_intr2 }]);
	});
	</script>
		<div id="placeholder3" style="width:600px;height:300px;margin-left:15px"></div>
	<br/>
	</div> <!--margin-->
<?php
}
else{
	echo "You need to add data, to visualize the details of the breed";
}
?>
<br/><br/><br/><br/><br/><br/>
	<div/> <!--end page-->
</body>
<?php
include("footer.php");
db_disconnect($dbh);
?>
</html>
