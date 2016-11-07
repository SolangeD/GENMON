<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>GenMon-CH</title>
		<style type="text/css">
		</style>
		<link rel="stylesheet" href="style.css" media="screen"/>
		<script src="ol/OpenLayers.js"></script>
		<!--<script src="map1.js"> </script>-->
		<!--<script src="map1.js"> </script>-->
		<script language="javascript" type="text/javascript" src="flot/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="flot/jquery.flot.js"></script>
		
	</head>    
<?php 
include("header.php");
include("connectDataBase.php");
$dbh=db_connect();
?>
<script language="javascript" type="text/javascript" src="map1.js"> </script>

	<body onload="init()">    <!-- javascript with the openlayers parameters,... in map1.js-->  
	

        	<div id="page">
			<div id="view1">
				<div id="map">
				</div><!--end id map-->
				<div id="legendImage">
				</div><!--end id legendImage-->	
			</div><!-- end #view1 -->	
				<div id="shortdesc">
					<div id="welcome" >
						<strong><font size="+1"> See inbreeding coefficient and other information</font></strong> <br> by clicking on a polygon (plz-polygon) <br />
						<br />
						Note that all numbers shown in this page are aggregates over the last generation interval of the given breed
					<div id="legend1">
					<div class="box" style="padding:10px 10px 10px 10px; margin-bottom:60px">
<b>Stat for the selected zone</b></br>
						<div id="Inb"></div>
						<div id="Intr"></div>
						<div id="Sumind"></div>
						<div id="MeanSEI"></div><br />
<b>Stat for the last selected polygon</b></br>
						<div id="showPLZ"></div>
						<div id="MeanInb"></div>
						<div id="MeanIntr"></div>
						<div id="MaxInb"></div>
						<div id="NumbInd"></div><br/>
<b>Stat for all plz-referenced animal</b></br>
<?php
$sql="select sum(num_ind_lastgi), round(cast(avg(mean_inb_lastgi) as numeric),3), round(cast(avg(mean_introgr_lastgi) as numeric),3) from plzo_plz";
$res=pg_query($sql);
$num=pg_fetch_result($res, 0, 0);
$inb=pg_fetch_result($res, 0, 1);
$int=pg_fetch_result($res, 0, 2);
echo "Total number of animals: ".$num."<br/>";
echo "Mean inbreeding: ".$inb."<br/>";
echo "Mean introgression: ".$int."<br/>";
?>

					</div><!-- end #box-->
					</div><!-- end #legend1-->
					</div><!-- end #welcome -->
							
				</div> <!-- end #shortdesc -->	
		</div>	<!-- end #page -->
</body>			
<div id="view3">
<?php
db_disconnect($dbh);
include("footer.php");
?>
</div>
</html>
