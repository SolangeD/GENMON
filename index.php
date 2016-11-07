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
		$dbh=db_connect();
		if (isset($_GET[ 'error' ])==1 && $_GET[ 'error' ]=='wrong_name'){
			echo "The name of the breed you entered already exists";
		}
		if(isset($_SESSION['user'])){ 
			$user=$_SESSION['user'];
		}
	?>
	<body>
		<div id="page">
			<h3>Welcome</h3>
			<hr/>
			<div id="margin">
				Welcome on GenMon-CH, an open Web-GIS application for the monitoring of Farm Animal Genetic Resources (FAnGR). <br/><br/>This application is designed to rank breeds according to four criteria: Genetic diversity (estimated from pedigree data), Introgression, Geographical concentration and Agriculture sustainability (from Socio-ecnomic and environmental data).<br/><br/> The ranking of the breed is shown in the following table, while more information for each breeds (table, graphs, maps) is available if you click on more info.<br/><br/> Please refer to the <a href="tutorial.php">tutorial</a> for more information and for test data to try the application.
			</div> <!--end margin-->
			<br />
			<h3>Summary table</h3>
			<hr/>
			
			<div id="margin">
				
				<form name="input" action="" method="post">
					Show: 
					<input type="radio" name="show_public" value="0" <?php if(isset($user)){echo "checked";}?>>My breeds only
					<input type="radio" name="show_public" value="1" <?php if(empty($user) || (isset($_POST['show_public']) && $_POST['show_public']==1)){echo "checked" ;}?>>My breeds and the public breeds
				<input style="margin-left:20px;" type="submit" value="Change" /></form><br /><br />
				<?php
					if(isset($user)){
						if(isset($_POST['show_public']) && $_POST['show_public']==1) {
							$sql_summary="SELECT * FROM summary where owner='".$user."' or public=1 order by index_final";
						}
						else{
							$sql_summary="SELECT * FROM summary where owner='".$user."' order by index_final"; 
						}
					}
					else {
						$sql_summary="SELECT * FROM summary where public=1 order by index_final";
					}
					
					$data=pg_query($sql_summary);
				?>
				<table>
					<thead>
						<tr>
							<th>Breed name</th> 
							<th>Last year <br />of data</th> 
							<th>Number individals<br />last GI</th> 
							<th>Pedigree compl.<br />6th gen, last GI</th> 
							<th>Average inbreeding<br />last GI</th> 
							<th>Effective pop <br />size range</th>
							<th>Trend males <br />last 5 years</th>
							<th>Trend females <br />last 5 years</th>
							<th>Pedig Index<br />(0-1) </th>
							<th>Introg Index</th> 
							<th>Geog index<br />(km)</th>
							<th>BAS Index<br />(0-1)</th>
							<th>Cryo-cons score<br />(0-1)</th>
							<th>Global Index<br />(0-1)</th>
							<th>Delete Breed</th>
							<th>More Info</th>
						</tr>
					</thead>
					<tbody>
						<?php	
							
							for($i=0;$i<pg_num_rows($data);$i++){ //loop on the breeds to be shown
								$ne2='';
								$ne=pg_fetch_result($data, $i, "\"ne\"");
								switch ($ne){
									case 20:
									$ne2='0-30';
									break;
									case 40:
									$ne2='30-50';
									break;
									case 60:
									$ne2='50-70';
									break;
									case 85:
									$ne2='70-100';
									break;
									case 150:
									$ne2='100-200';
									break;
									case 250:
									$ne2='&gt;200';
									break;
									
								}
							?>
							<tr>
								<td><?php echo pg_fetch_result($data, $i, 1)." (".pg_fetch_result($data, $i, "\"species\"").")"; ?></td>
								<td><?php echo pg_fetch_result($data, $i, 3); ?></td>
								<td><?php echo pg_fetch_result($data, $i, 4); ?></td>
								<td><?php echo pg_fetch_result($data, $i, "\"ped_compl\""); 
								$color=ColorCode(pg_fetch_result($data, $i, "\"ped_compl\""),'ped_compl', pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font> </td>
								<td><?php echo pg_fetch_result($data, $i, "\"avg_inb\""); 
								$color=ColorCode(pg_fetch_result($data, $i, "\"avg_inb\""),'avg_inb', pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font></td>
								<td><?php echo $ne2; 
								$color=ColorCode($ne,'ne', pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font></td>
								<td><?php echo pg_fetch_result($data, $i, "\"trend_males\""); 
								$color=ColorCode(pg_fetch_result($data, $i, "\"trend_males\""),'trend_males',pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font></td>
								<td><?php echo pg_fetch_result($data, $i, "\"trend_females\""); 
								$color=ColorCode(pg_fetch_result($data, $i, "\"trend_females\""),'trend_females',pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font></td>
								<td><?php echo pg_fetch_result($data, $i, "\"index_demo\""); 
								$color=ColorCode(pg_fetch_result($data, $i, "\"index_demo\""),'index_demo',pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font></td>
								<td><?php echo pg_fetch_result($data, $i, "\"introgression\""); 
								$color=ColorCode(pg_fetch_result($data, $i, "\"introgression\""),'introgression', pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font></td>
								<td><?php echo pg_fetch_result($data, $i, "\"min_radius\""); 
								$color=ColorCode(pg_fetch_result($data, $i, "\"min_radius\""),'min_radius', pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font></td></td>
								<td><?php echo pg_fetch_result($data, $i, "\"index_socio_eco\""); 
								$color=ColorCode(pg_fetch_result($data, $i, 7),'index_socio_eco', pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font></td>
								<td><?php echo pg_fetch_result($data, $i, "\"cryo_cons\""); 
								$color=ColorCode(pg_fetch_result($data, $i, "\"cryo_cons\""),'cryo_cons', pg_fetch_result($data, $i, "\"owner\""), pg_fetch_result($data, $i, "\"species\""));?> 
								<font color="<?php echo $color ?>">&#9632;</font></td>
								<td><?php echo pg_fetch_result($data, $i, 8); ?></td>
								<td>
									<?php
										$sql_owner="select owner from summary where breed_id=".pg_fetch_result($data, $i, 0);
										$result_owner=pg_query($sql_owner);
										$owner=pg_fetch_result($result_owner,0,0);
										if(isset($user) && $user==$owner){
										?>
										<form method="post" action="delete_breed.php">
											<input class="button" type="submit" value="delete" onclick="return confirm('Are you sure you want to delete this breed?');"/>
											<input type="hidden" name="breed_id" value="<?php echo pg_fetch_result($data, $i, 0); ?>" />
											<input type="hidden" name="breed_name" value="<?php echo pg_fetch_result($data, $i, 1); ?>" />
										</form>
										<?php
										}
									?>
								</td>
								<td><form method="post" action="breed_detail.php">
									<input class="button" type="submit" value="more" />
									<input type="hidden" name="breed_id" value="<?php echo pg_fetch_result($data, $i, 0); ?>" />
								<input type="hidden" name="breed_name" value="<?php echo pg_fetch_result($data, $i, 1); ?>" /></form></td>
				</tr>
				<?php
				}
			?>
		</tbody>
	</table>
	
	<br />
	In this table, breeds are ordered according to their endangerment, assessed by the global index (the breeds with lower global indices are more endangered).
	<br />
	Here is a description of the indices
	<ul>
		<li><strong>GI: </strong>refers to the generation interval (in this case, the average age of dams that gave birth to an animal in the last year)</li>
		<br />
		<li><strong>Pedig index: </strong>mean inbreeding coefficient over the last GI and effective population size</li>
		<li><strong>Introg index: </strong>mean introgression over the last GI</li>
		<li><strong>Geog index: </strong>radius of circle containing 75% of animals centered in the centre of mass (km) for animals born within the last GI</li>
		<li><strong>BAS index: </strong>Agriculture Sustainability index at the breed level(containing socio-economical and environmental data)</li>
		<br/>
		<li><strong>Global index: </strong>Global sustainability index; includes the 4 above mentioned indices</li>
	</ul>
	To see how criteria are aggregated, move to the <a href="tutorial.php">tutorial</a> section
</div> <!--end margin-->
<br /><br />
<h3>Add a breed</h3>
<hr/>
<div id="margin">
	<?php
		if(isset($_SESSION['user'])){
		?>
		<form method="post" action="AddBreed.php">
			Name of the breed:<input type="text" name="breed_long_name"/><br />
			Short name of the breed <input type="text" name="breed_name"/><br />
			<select name="species">
				<?php
					$sql_species="select distinct species from thres_weight where owner='".$_SESSION['user']."'";
					$result_species=pg_query($sql_species);
					$num_species=pg_num_rows($result_species);
					for($i=0;$i<$num_species;$i++){
					?>
					<option value="<?php echo pg_fetch_result($result_species,$i,0);?>"><?php echo pg_fetch_result($result_species,$i,0);?></option>
					<?php
					}
				?>
			</select> Note: if you do not see any species in the dropdown list, add a species in the "Add a species" section <br/>
			This breed is: 
			<input type="radio" name="public" value="1" checked>Public  
			<input type="radio" name="public" value="0">Private</br> <br/>
			<input type="hidden" name="user_name" value="<?php echo $user; ?>" />
			<input style="margin-left:20px;" type="submit" value="Add" /><br /><br />
		</form>
		<?php
		}
		else{
			echo "You need to be logged in in order to add a breed <br /><br />";
		}
	?>
	
</div> <!--end margin-->
<h3>Add a species</h3>
<hr/>
<div id="margin">
	<?php
		if(isset($_SESSION['user'])){
		?>
		<form method="post" action="AddSpecies.php">
			Name of the species:<input type="text" name="species_name"/>
			<input style="margin-left:20px;" type="submit" value="Add" /><br /><br />
		</form>
		<?php
		}
		else{
			echo "You need to be logged in in order to add a breed <br /><br />";
		}
	?>
</div> <!--end margin-->
<h3>Change weights and thresholds</h3>
<hr/>
<div id="margin">
	<?php
		if(isset($_SESSION['user'])){
		?>
		<form method="post" action="ChangeWeightDemo.php">
			<strong>Change weights Pedig index: </strong><br />
			<input style="margin-left:20px;" type="submit" value="Change Weight" /><br /><br />
		</form>
		
		
		<form method="post" action="ChangeWeightSocioEco.php">
			<strong>Change weights Agriculture sustainability index: </strong><br />
			<input style="margin-left:20px;" type="submit" value="Change Weight" /><br /><br />
		</form>
		
		<form method="post" action="ChangeWeightFinal.php">
			<strong>Change weights Final index: </strong><br />
			<input style="margin-left:20px;" type="submit" value="Change Weight" /><br /><br />
		</form>
		<?php
		}
		else{
			echo "You need to be logged in in order to change weights <br /><br />";
		}
	?>
</div> <!--end margin-->
<h3>Upload Socio-Economic data</h3>
<hr/>
<div id="margin">
	<form method="post" action="AddDataOFS.php">
		<strong>Upload Socio-Economic data: </strong><br />
		<input style="margin-left:20px;" type="submit" value="Upload Socio-Eco Data" /><br />
	</form>
	<br/><br/><br/><br/><br/><br/><br/>
</div>
</body>
<?php
	db_disconnect($dbh);
	include("footer.php");
	function ColorCode($value0, $criteria,$owner_breed,$species_breed){
		$sql_threshold="select t1, t2 from thres_weight where criteria='".$criteria."' and owner='".$owner_breed."' and species='".$species_breed."' 
			union select t1, t2 from thres_weight where criteria='".$criteria."' and owner='".$owner_breed."' and species='default'";
		$threshold0=pg_query($sql_threshold);
		if(pg_num_rows($threshold0) != 0){
			$t1=pg_fetch_result($threshold0,0,0);
			$t2=pg_fetch_result($threshold0,0,1);
			$value=min(max(($value0-$t1)*1/($t2-$t1),0),1);
			if ($value>0.95){
				$color='green';
			}
			elseif ($value>0.5){
				$color='yellowgreen';
			}
			elseif ($value>=0.1){
				$color='gold';
			}
			elseif ($value<0.1){
				$color='red';
			}
		}
		else {
			$color='White';
		}
		return $color;
	}
	function _isset($val) { return isset($val); }
?>
</html>
