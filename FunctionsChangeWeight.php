<?php
function change_weight_form($table_name, $criteria_type, $owner, $species){
	$sql_tw="select criteria, t1, t2, weight
		from thres_weight where owner='".$owner."' and crit_type='".$criteria_type."' and species='".$species."' order by criteria";
		/*union
		select * from thres_weight
		where owner='".$owner."' and  crit_type='".$criteria_type."' and species='default'
		and not exists (select * from thres_weight where owner='".$owner."' and crit_type='".$criteria_type."' and species='".$species."')";*/
	$sql_tw2=pg_query($sql_tw);
	$num_row=pg_num_rows($sql_tw2); //num criteria
	$num_col=pg_num_fields($sql_tw2); // typically 4: name, t1, t2, weight
	?>
	<table>
	<thead>
	<?php for($i=0;$i<$num_col;$i++){ ?>
		<th><?php echo pg_field_name($sql_tw2,$i)?></th> <!-- Name of column: name, t1, t2, weight -->
	<?php
	}
	?>
	<form name="input" action="" method="post">


	</thead>
	<tbody>
	<?php for($j=0;$j<$num_row;$j++){ ?>
	<tr>
		<?php for($i=0;$i<$num_col;$i++){ 
			if($i==0){
			?>
				<td><?php echo pg_fetch_result($sql_tw2,$j,$i);?></td> 
				<input type="hidden" name="<?php echo $i.$j?>" value="<?php echo pg_fetch_result($sql_tw2,$j,$i)?>"> <!-- name of the criterion -->
				<input type="hidden" name="species" value="<?php echo $species?>">
			<?php
			}
			else {?>
			<td><input type="text" name="<?php echo $i.$j?>" value="<?php echo pg_fetch_result($sql_tw2,$j,$i)?>"></td>
		<?php
			}
	} ?>
	</tr>
	<?php
	}
	?>
	</tbody>
	</table>
	<br />
	<input type="submit" value="Change">
	</form><br/><br/>
	<?php
}

function change_weight_db($table_name,$criteria_type,$owner, $species){
	$total_weight=0;
	$sql_tw="select criteria, t1, t2, weight
		from thres_weight where owner='".$owner."' and crit_type='".$criteria_type."' and species='".$species."'
		union
		select criteria, t1, t2, weight from thres_weight
		where owner='".$owner."' and  crit_type='".$criteria_type."' and species='default'
		and not exists (select criteria, t1, t2, weight from thres_weight where owner='".$owner."' and crit_type='".$criteria_type."' and species='".$species."')";
	$sql_tw2=pg_query($sql_tw);
	$num_row=pg_num_rows($sql_tw2);
	$num_col=pg_num_fields($sql_tw2);

	$sql_change_w="";
	for($j=0;$j<$num_row;$j++){ 
		$crit=$_POST["0".$j];
		for($i=1;$i<$num_col;$i++){ 
			$ij1=$i.$j;
			if(isset($_POST[$ij1])==1){
				$ij=$_POST[$ij1];
				switch($i){
				case 1:
					$sql_change_w="update ".$table_name." set t1 = ".$ij." where criteria='".$crit."' and owner='".$owner."' and species='".$species."'";
					pg_query($sql_change_w);
					break;
				case 2:
					$sql_change_w="update ".$table_name." set t2 = ".$ij." where criteria='".$crit."' and owner='".$owner."' and species='".$species."'";
					pg_query($sql_change_w);
					break;
				case 3:
					$sql_change_w="update ".$table_name." set weight = ".$ij." where criteria='".$crit."' and owner='".$owner."' and species='".$species."'";
					pg_query($sql_change_w);
					$total_weight+=$ij;
					break;
				}
			}
		} 
	}
	return $total_weight;

}
?>
