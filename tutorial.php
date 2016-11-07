<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
	<meta name="google" value="notranslate">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>GenMon-CH Tutorial</title>
	<link rel="stylesheet" type="text/css" href="style.css" media="screen" />
	<script type="text/javascript" src="jscolor/jscolor.js"></script>
	<link rel="shortcut icon" href="images/greenADN.png">
</head>
   <body>
<?php
include("header.php");
?>
	<div id="page">
<h2>Table of contents</h2><br/>
<a href="#desc">Detailed description</a><br/>
<a href="#add_data">Add data to a breed</a><br/>
<a href="#add_SE_data">Add socio-economic data</a><br/>
<a href="#add_breed">Add a breed</a><br/>
<a href="#change_weight">Change weights and threshold</a><br/>
<a href="#vis">Spatial distribution visualisation</a><br/><br/><br/>
<h2 id="desc">Detailed description</h2><br/>
A detailed description of the methodology and the platform is available here: <br/>
<a href="pdf/SDuruz_PdM_final.pdf">A Web-GIS application for the monitoring of Farm Animal Genetic Resources (FAnGR) in Switzerland</a>
<br/><br/>
	
				<h2 id="add_data">Add data to a breed</h2>
				<ol>
                    			<li> Create a <b>csv</b> file, with "|" (vertical pipe) as column separators.</li>
					<li> Ensure it has the correct order of columns: 
					<ol>
						<li>Animal ID (might contain letter), </li>
						<li>Sire ID (might contain letter), </li>
						<li>Dam ID (might contain letter), </li>
						<li>Birthdate (<b>YYYY-MM-DD</b>), </li>
						<li>Sex (<b>M if male, F if female</b>), </li>
						<li>PLZ (<b>integer</b>), </li>
						<li>Introgression (<b>float, between 0 and 1</b>), </li>
						<li>Inbreeding coefficient calculated from genetic tests (<b>float, between 0 and 1</b>), </li>
						<li>Cryoconserve (1 if gametes from this animal have been cryoconsered, 0 otherwise)</li></ol>
<b>Important note:</b><br/> The <b>first line</b> should be commented with a # and is planned for <b>headers</b>; You must have a header for all columns<br/>
The first 5 columns are needed to run PopRep. If you do not have data for the last 4 columns, you can leave them empty</li> <br/>	
					<li> Alternatively, you can use the assisted upload section, were it is possible to upload several files that will be joined together, and were you can specify the column order and format.				
					<li> If your data are in a spreadsheet-format (like '.xls(x)' or '.ods'), simply save them as csv (separated through comma) in the "Save as" options.</li>  
					<li> Avoid unconventional characters in the csv file name.</li>
					<li> From the home page, click on more on the breed you want to add data</li>
					<li> Browse your csv file directly in the page, or in the assisted upload page (do not forget to specify the column order and format of the column if you do so)</li>
					<li> Click on Add</li>	
					<li> Depending on the size of the data you uploaded, <b>computations might take a long time (up to a few hours)</b>; do not be surprised and let your internet browser open</li>
					<li> To perform the "deomgraphical index" and therefore the "final index", you need to specify the effective population size. For this, click on "more" and at the end of the page, based on the information that are given, choose an effective population size range</li>		
<br/>
Here is a data sample that you can use for testing the application: <a href="pdf/data_sample.csv">test data</a>. It is already in the right format, but you can try uploading it via the assisted upload section. </br>
If you want to test the upload, you can use the Test breed, which has been created for test purpose.		

					<!--
					<li> If your are logged choose public or private access:
					<br>
						Private: only you can see this study
					<br>
						Public: the study is in free access
					<br>   
						(You can modify data acces to your studies, at any time, in the "Account Management" menu)
                    </ol>
                    <p>
						<br>
						Useful links for the coordinates conversion to decimal degrees: <br>
						<a href="http://www.fcc.gov/mb/audio/bickel/DDDMMSS-decimal.html" target="_blank">http://www.fcc.gov/mb/audio/bickel/DDDMMSS-decimal.html </a>
						<br>
						<a href="http://www.directionsmag.com/latlong.php" target="_blank">http://www.directionsmag.com/latlong.php </a>
						<br>
						<a href="http://www.geology.enr.state.nc.us/gis/latlon.html" target="_blank">http://www.geology.enr.state.nc.us/gis/latlon.html </a>
						<br>
						<a href="#top"> Top Page</a>
						<br></br>
					</p>-->
				</ol>
								<h2 id="add_SE_data">Add socio-economic data</h2><br/>
				Given that the information on Socio-Economic activities might come from different sources, you have the opportunity to upload your files in several steps. Everytime you upload a file, the BFS number must be specified. The order of the columns are not important, but you need to specify this order using the drop-down list.
				<ol>
					<li> Make sure the weights (home page>Change Socio-Economic weights) are the one you want to use</li>
					<li> From the home page, click on Upload Socio-Ecomonic data</li>
					<li> Browse the file you want to upload</li>
					<li> Specify the column order</li>
					<ol>
						<li>num_ofs: bfs number</li>
						<li>demog_balance: Increase/decrease in population in the last few years (in %)</li>
						<li>median_income: can be replaced by the social assistance rate</li>
						<li>unemployment_rate: if not available, might use the social assistance rate (in %)</li>
						<li>job_primary_sector: Number of jobs in the primary sector</li>
						<li>job_total: Total number of jobs (all three sectors)</li>
						<li>grazing_surface_ha: Surface used for animal breeding (in ha) (grünfläche)</li>
						<li>total_suface_km2: The total surface of the commune (in km2)</li>
						<li>job_primary_sector_past: The number of jobs in the primary sector from a previous year (the year should be decided by the user, and the corresponding threshold set accordingly)</li>
						<li>percent_less_19: Percentage of the population younger than 19 years old (in %)</li>
						<li>percent_more_65: Percentage of the population older than 65 years old (in %)</li></ol>
<b>Where to find this data:</b> 
<ul>
<li>Point 2,4,5,8,(9),10,11 can be found in the BFS page, Regional Data>Communes>Portrait commune</li>
<li>Point 7 (and 9) can be obtained in the Landwirtschaft STAT-TAB interactive database from BFS. Choose the "Landwirtshaftliche Betriebe nach Jahr und Gemeinde"</li>
</ul>
					<li> Add the year corresponding to the data (integer format</li>
					<li> Click on add</li>
					<li> Based on the weights specified, the index per commune will be caculated. This might take a few minutes</li>
				</ol>
Indices are computed using the MACBETH criteria aggregation technique <a href="http://www.m-macbeth.com/en/m-home.html">see more</a>
				<br/><br/>
				<h2 id="add_breed">Add a breed</h2>
				<ol>
				<li>In the home page, enter the "name of the breed" (for example Original Braunvieh)</li>
				<li>Then choose a shortname for the breed, i.e. a few letter (for example BVO)</li>
				<li>Click on add. This will add the breed in the summary table. You then need to add the data</li>
				</ol>
				<h2 id="change_weight">Change weights and thresholds</h2><br/>
The procedure described here is valid for all kinds of weight change. Here is a description of the different parameters that you must enter. Click on change weight to do so.
				<ol>
				<li>t1: threshold at which the criteria is completely not satisfactory</li>
				<li>t2: threshold at which the criteria is completely satisfactory</li><br/>
				Note that if you are trying to minimize a criterion (for example the unemployment rate), t1 will be bigger than t2.
				<li>weight: The weight of the criteria. Note that the sum of the weights must equal one</li></ol>

				<h2 id="vis">Spatial distribution visualisation</h2><br/>
				<ol>
				<li>From the home page click the more button for the corresponding breed</li>
				<li>Click on "Go To Map" (the preparation of the map can take a few seconds)</li>
				<li>Choose the layer to visualize by using the "+" button on the right</li>
				<li>Zoom with the "+" button on the left or with the scroll of your mouse</li>
<li>Select a PLZ (or a group of plz) with your mouse; you will see the detail for this region on the right of the map</li></ol>
				
				<h2 id="new_user">New users</h2>
				<ol>
				<li>New users who are interested in upload multiple breeds, change weights etc... need to first sign up and create an account</li>
				<li>When a new account is created, the default weights and thresholds are set. These can be changed according to the description <a href=#change_weight">above</a></li>
				<li>Note that in order to change the weight from the pedig-index, you first need to create a breed</li>
				<br/><br/><a href="#top"> Top Page</a>
				<br></br><br></br><br></br><br></br><br></br>
	

	</div><!-- end #page -->
   </body>
<?php
include("footer.php");
?>
</html>
