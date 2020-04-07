<!DOCTYPE HTML>
<head>
	<title>Discount Searcher</title>
	<meta name="author" content="Szymon Gniadkowski"/>
	<link rel="stylesheet" href="style.css">
	<meta charset = "utf-8">


</head>
<body>
	<?php
	// security
		$searchtext = str_replace('\'','', $_GET['searchtext']);
		$searchtext = str_replace('"','', $searchtext);
		$searchtext = trim($searchtext);
		$searchtext = substr($searchtext, 0, 16);

		$objnumber = $_GET['obj_num'];
		$objnumber = substr($objnumber, 0, 7);
		if(is_numeric("$objnumber")) { }
		else
			$objnumber = 1;


	//variable that is used in menu items info
		$searchobject = str_replace('\'','', $_GET['searchobject']);
		$searchobject = str_replace('"','', $searchobject);
		$searchobject = trim($searchobject);
		$searchobject = substr($searchobject, 0, 7);
		if(is_numeric("$searchobject")) { }
		else
			$searchobject = '';
	?>

	<div class = "intro">
		<h1>Discount Searcher</h1>
	</div>
	<div class = "searching">
		<form action = "index.php" method="get">
			<select name = "concept" class = "brand">
				<?php include 'conceptsoptions.inc' //include of concepts?>
			</select>
			<input type = "text" name = "searchtext" value = <?=$searchtext?>>
			<?php
			if($_GET['showinactive'])
				echo '<input type = "checkbox" name = "showinactive" value = "showinactive" checked> Show also inactive';
			else
				echo '<input type = "checkbox" name = "showinactive" value = "showinactive"> Show also inactive';
			?>
			<button type="submit">Submit</button>
		</form>
	</div>
	<?php
		$sql_conn1 = @sybase_connect($_GET['concept'], "user", "password") or die("Could not connect!");
		$query = "SELECT DISTINCT
		t1.name name,
		t1.obj_num obj_num,
		t1.percentage percentage,
		t1.amt amt,
		t1.effective_from effective_from,
		t1.effective_to effective_to,
		t3.name name2,
		t1.cat cat,
		t1.min_percent min_percent,
		t1.max_percent max_percent,
		t1.min_amount min_amount,
		t1.max_amount max_amount,
		t1.priv_lvl priv_lvl,
		t4.name period_name,
		t5.name period2_name,
		t6.name period3_name,
		t7.name period4_name,
		t8.name print_class,
		t9.name tax_class,
		t1.ob_limit_by_seat by_seat,
		t1.ob_limit_by_check by_check,
		t1.ob_limit_by_item by_item,
		t1.ob_dsvc02_preset preset_parameter,
		t1.ob_dsvc03_amt amount_parameter,
		t1.ob_dsvc05_ref_req refreq_parameter,
		t1.itmzr_mask_1_32 itemizer_mask,
		t1.rule_type rule_type_nr,
		t10.name trigger1,
		t10.obj_num nr_trigger1,
		t11.name trigger2,
		t11.obj_num nr_trigger2,
		t12.name trigger3,
		t12.obj_num nr_trigger3,
		t13.name trigger4,
		t13.obj_num nr_trigger4,
		t14.name trigger5,
		t14.obj_num nr_trigger5,
		t15.name trigger6,
		t15.obj_num nr_trigger6,
		t16.name trigger7,
		t16.obj_num nr_trigger7,
		t17.name trigger8,
		t17.obj_num nr_trigger8,
		t1.trigger_quantity trigger_quantity,
		t1.trigger_total trigger_total,
		t18.name award_mi_set,
		t18.obj_num nr_award_mi_set,
		t1.award_count award_quantity,
		t1.award_max_count max_award_quantity,
		t1.award_type award_type_nr,
		case when t1.dsvc_slu_seq is NOT NULL OR (t2.key_num is NOT NULL AND t2.key_type = '5') then 'yes' else 'no' end as onscreen,
		case when t1.effective_to > (SELECT NOW()) or t1.effective_to is null then 'active' else 'not active' end as active
		FROM micros.dsvc_def t1 
		LEFT JOIN micros.ts_key_def t2 ON t2.key_num = t1.dsvc_seq
		LEFT JOIN micros.mlvl_class_def t3 ON t1.mlvl_class_seq = t3.mlvl_class_seq
		LEFT JOIN micros.period_def t4 ON t1.period_seq = t4.prd_seq
		LEFT JOIN micros.period_def t5 ON t1.period2_seq = t5.prd_seq
		LEFT JOIN micros.period_def t6 ON t1.period3_seq = t6.prd_seq
		LEFT JOIN micros.period_def t7 ON t1.period4_seq = t7.prd_seq
		LEFT JOIN micros.prn_class_def t8 ON t1.prn_def_class_seq = t8.prn_def_class_seq
		LEFT JOIN micros.tax_class_def t9 ON t1.tax_class_seq = t9.tax_class_seq
		LEFT JOIN micros.mi_set_def t10 ON t10.seq = t1.trigger_mi_set1_seq
		LEFT JOIN micros.mi_set_def t11 ON t11.seq = t1.trigger_mi_set2_seq
		LEFT JOIN micros.mi_set_def t12 ON t12.seq = t1.trigger_mi_set3_seq
		LEFT JOIN micros.mi_set_def t13 ON t13.seq = t1.trigger_mi_set4_seq
		LEFT JOIN micros.mi_set_def t14 ON t14.seq = t1.trigger_mi_set5_seq
		LEFT JOIN micros.mi_set_def t15 ON t15.seq = t1.trigger_mi_set6_seq
		LEFT JOIN micros.mi_set_def t16 ON t16.seq = t1.trigger_mi_set7_seq
		LEFT JOIN micros.mi_set_def t17 ON t17.seq = t1.trigger_mi_set8_seq
		LEFT JOIN micros.mi_set_def t18 ON t18.seq = t1.award_mi_set_seq
		WHERE 
		obj_num = '$objnumber'
		ORDER BY t1.obj_num";

		$query_on_screen = "SELECT DISTINCT
		case when t1.dsvc_slu_seq is NOT NULL OR (t2.key_num is NOT NULL AND t2.key_type = '5') then 'yes' else 'no' end as onscreen
		FROM micros.dsvc_def t1
		LEFT JOIN micros.ts_key_def t2 ON t2.key_num = t1.dsvc_seq AND t2.key_type = '5'
		WHERE t1.obj_num = '$objnumber'
		AND	(t2.key_type = '5' or t2.key_type = null)";

		$query_subscription = "SELECT DISTINCT
		t1.subscriber_seq subscriber_seq,
		CASE WHEN t2.published_data_path LIKE '%MICROS_RES_POS\\DSVC_GRP\\%' THEN cast(substring(t2.published_data_path,25,29) as int) END dsvc_seq,
		t4.obj_num as MPK,
		cast(t3.obj_num as varchar) obj_num
		FROM micros.em_subscription_dtl t1
		LEFT JOIN micros.em_published_data_def t2 ON t1.published_data_seq = t2.published_data_seq
		LEFT JOIN micros.dsvc_def t3 on dsvc_seq = t3.dsvc_seq
		LEFT JOIN micros.em_store_def t4 on subscriber_seq = store_seq
		WHERE obj_num = '$objnumber'
		ORDER BY MPK";

		$query_screen_key = "SELECT DISTINCT
		t3.legend as key_key,
		t4.name as key_name_screen,
		t4.obj_num as key_number_screen,
		t5.version_name as version_name,
		'by key' as key_type_screen,
		'-' as key_slu_cat,
		case when t1.dsvc_slu_seq is NOT NULL OR t3.key_num is NOT NULL then 'yes' else 'no' end as onscreen
		FROM micros.dsvc_def t1
		LEFT JOIN micros.ts_key_def t3 ON t3.key_num = t1.dsvc_seq
		LEFT JOIN micros.ts_scrn_def t4 ON t3.ts_scrn_seq = t4.ts_scrn_seq
		left join micros.cfg_version_def t5 ON t5.scrn_seq = t3.ts_scrn_seq AND t5.version_seq = t3.cfg_sect_ver_seq
		--left join micros.cfg_version_def t6 ON t6.version_seq = t3.cfg_sect_ver_seq
		WHERE t1.obj_num = '$objnumber' AND t3.key_type = '5'
		ORDER BY key_number_screen, version_name
		";

		$query_screen_slu = "SELECT DISTINCT
		t1.name as slu_key,
		t7.name as slu_name_screen,
		t7.obj_num as slu_number_screen,
		'by SLU' as slu_type_screen,
		t2.name as slu_slu_cat,
		t7.name as version_name
		--t8.version_name as version_name
		FROM micros.dsvc_def t1
		LEFT JOIN micros.dsvc_slu_def t2 ON t1.dsvc_slu_seq = t2.dsvc_slu_seq 
		LEFT JOIN micros.ts_style_def t6 ON t2.ts_style_seq = t6.ts_style_seq
		LEFT JOIN micros.ts_scrn_def t7 ON t6.template = t7.ts_scrn_seq
		--LEFT JOIN micros.cfg_version_def t8 ON t8.scrn_seq = t7.ts_scrn_seq
		WHERE t1.obj_num = '$objnumber' AND slu_slu_cat IS NOT NULL
		";

		$query_itemizer = "SELECT
		t1.disc_itmzr_seq,
		t1.name itemizer_name
		FROM micros.disc_itmzr_def t1
		WHERE itemizer_name IS NOT NULL
		ORDER BY t1.disc_itmzr_seq";

		$query_menu_item_class = "SELECT
		t2.name menu_item_class_name,
		t1.disc_itmzr_seq,
		t1.name itemizer_name,
		t2.obj_num menu_item_class_nr
		FROM micros.mi_type_class_def t2
		LEFT JOIN micros.disc_itmzr_def t1 ON t1.disc_itmzr_seq = t2.dsc_itmzr
		WHERE menu_item_class_name IS NOT NULL AND menu_item_class_name NOT LIKE '%**%'
		ORDER BY menu_item_class_nr
		";

		$query_menu_item_info = "SELECT
		t1.obj_num item_obj_num,
		t1.name_1 item_name,
		t3.obj_num mic_number,
		t3.name mic_name,
		t4.name itemizer_name,
		t4.disc_itmzr_seq itemizer_numer,
		t5.maj_grp_seq as major_group_seq,
		t5.obj_num as major_group_obj_num,
		t5.name as major_group_name,
		t6.fam_grp_seq as fam_group_seq,
		t6.obj_num as fam_group_obj_num,
		t6.name as fam_group_name
		FROM micros.mi_def t1
		left join micros.mi_ver_def t2 ON t1.mi_seq = t2.mi_seq
		left join micros.mi_type_class_def t3 on t2.mi_type_seq = t3.mi_type_seq
		left join micros.disc_itmzr_def t4 on t4.disc_itmzr_seq = t3.dsc_itmzr
		left join micros.maj_grp_def t5 on t5.maj_grp_seq = t1.maj_grp_seq
		left join micros.fam_grp_def t6 on t6.fam_grp_seq = t1.fam_grp_seq
		WHERE item_obj_num = '$searchobject'
		ORDER BY item_name, mic_name";
		
		$query_rule_setups[8];

		for($x=0;$x<8; $x++)
		{
			$query_rule_setups[$x] = "SELECT t1.name AS MemberName,
			t1.start_obj_num AS StartObjNum,
			CASE
				WHEN Type = '4' then t3.name_1
				WHEN Type = '3' THEN t4.name
				WHEN Type = '2' THEN t5.name
				WHEN Type = '5' THEN t9.name END AS StartName,
			t1.end_obj_num AS EndObjNum,
			CASE
				WHEN Type = '4' then t6.name_1
				WHEN Type = '3' THEN t7.name
				WHEN Type = '2' THEN t8.name
				WHEN Type = '5' THEN t10.name END AS EndName,
			t1.type AS Type,
			t2.seq AS MI_set_seq_num,
			t2.obj_num as MI_set_num,
			t2.name as MI_set_name from micros.mi_set_detail_def t1
			left join micros.mi_set_def t2 on t1.mi_set_seq = t2.seq
			left join micros.mi_def t3 on type = '4' and t1.start_obj_num = t3.obj_num
			left join micros.fam_grp_def t4 on type = '3' and t1.start_obj_num = t4.obj_num
			left join micros.maj_grp_def t5 on type = '2' and t1.start_obj_num = t5.obj_num
			left join micros.mi_def t6 on type = '4' and t1.end_obj_num = t6.obj_num
			left join micros.fam_grp_def t7 on type = '3' and t1.end_obj_num = t7.obj_num
			left join micros.maj_grp_def t8 on type = '2' and t1.end_obj_num = t8.obj_num
			left join micros.disc_itmzr_def t9 on type = '5' and t1.start_obj_num = t9.disc_itmzr_seq
			left join micros.disc_itmzr_def t10 on type = '5' and t1.end_obj_num = t10.disc_itmzr_seq
			where MI_set_name is not null AND MI_set_seq_num = (select trigger_mi_set".($x+1)."_seq from micros.dsvc_def where obj_num = '$objnumber')
			order by MI_set_name, MemberName ASC";
		}

		$query_award_mi_set = "SELECT t1.name AS MemberName,
			t1.start_obj_num AS StartObjNum,
			Type,
			CASE
				WHEN Type = '4' then t3.name_1
				WHEN Type = '3' THEN t4.name
				WHEN Type = '2' THEN t5.name
				WHEN Type = '5' THEN t9.name END AS StartName,
			t1.end_obj_num AS EndObjNum,
			CASE
				WHEN Type = '4' then t6.name_1
				WHEN Type = '3' THEN t7.name
				WHEN Type = '2' THEN t8.name
				WHEN Type = '5' THEN t10.name END AS EndName,
			t1.type AS Type,
			t2.seq AS MI_set_seq_num,
			t2.obj_num as MI_set_num,
			t2.name as MI_set_name from micros.mi_set_detail_def t1
			left join micros.mi_set_def t2 on t1.mi_set_seq = t2.seq
			left join micros.mi_def t3 on type = '4' and t1.start_obj_num = t3.obj_num
			left join micros.fam_grp_def t4 on type = '3' and t1.start_obj_num = t4.obj_num
			left join micros.maj_grp_def t5 on type = '2' and t1.start_obj_num = t5.obj_num
			left join micros.mi_def t6 on type = '4' and t1.end_obj_num = t6.obj_num
			left join micros.fam_grp_def t7 on type = '3' and t1.end_obj_num = t7.obj_num
			left join micros.maj_grp_def t8 on type = '2' and t1.end_obj_num = t8.obj_num
			left join micros.disc_itmzr_def t9 on type = '5' and t1.start_obj_num = t9.disc_itmzr_seq
			left join micros.disc_itmzr_def t10 on type = '5' and t1.end_obj_num = t10.disc_itmzr_seq
			where MI_set_name is not null AND MI_set_seq_num = (select award_mi_set_seq from micros.dsvc_def where obj_num = '$objnumber')
			order by MI_set_name, MemberName asc";


		$result = sybase_query($query,$sql_conn1);
		$obAmount = sybase_num_rows($result);
		$row = sybase_fetch_assoc($result);
	?>
	<h2>Discount nr <?=$objnumber.' - '.$row['name'];?></h2>

	<div class = "block">
		<div class = "area_header">Subscription info</div>
		<div class = "area">
			<?php
			$result = sybase_query($query_subscription,$sql_conn1);
			$obAmount = sybase_num_rows($result);
			if($obAmount == 0)
				echo '<div class = "info">Discount has no subscribers.</div>';
			else {
				echo 'Discount is subscribed to restaurants: ';
				for($x = 0; $x < $obAmount; $x++){
					$row = sybase_fetch_assoc($result);
					echo $row['MPK'];					
					if(($x!==$obAmount-1 && $x !== -1) && $row['MPK'] != '')
						echo ', ';
				};
			};?>
		</div>
	<br>
	</div>
	<div class = "block">
		<div class = "area_header">Screen Info</div>
		<div class = "area">
			<?php
			$result = sybase_query($query_on_screen,$sql_conn1);
			$obAmount = sybase_num_rows($result);
			$row = sybase_fetch_assoc($result);
			if($row['onscreen'] == 'no ' || $row['onscreen'] == 'no')
				echo '<div class = "info">Discount is not visible on screens.</div></div><br>';
			else {
				$result = sybase_query($query_screen_key,$sql_conn1);
				$obAmount = sybase_num_rows($result);?>
				<table>
					<tr>
						<th>#</th>
						<th>Key / SLU</th>
						<th>Name of screen</th>
						<th>Number of screen</th>
						<th>Type of screen</th>
						<th>SLU category</th>
						<th>Version name</th>
					</tr>
					<?php
					for($x = 0; $x < $obAmount; $x++) {
						$row = sybase_fetch_assoc($result);?>
						<tr>
							<td class = "first_column"><?=$x+1;?></td>
							<td><?=$row['key_key'];?></td>
							<td><?=$row['key_name_screen'];?></td>
							<td><?=$row['key_number_screen'];?></td>
							<td><?=$row['key_type_screen'];?></td>
							<td><?=$row['key_slu_cat'];?></td>
							<td><?=$row['version_name'];?></td>
						</tr>
						<?php
						$temp_amount = $x+1;
					}
					$result = sybase_query($query_screen_slu,$sql_conn1);
					$obAmount = sybase_num_rows($result);

					for($x = $temp_amount; $x < $obAmount+$temp_amount; $x++) {
						$row = sybase_fetch_assoc($result);?>
						<tr>
							<td class = "first_column"><?=$x+1;?></td>
							<td><?=$row['slu_key'];?></td>
							<td><?=$row['slu_name_screen'];?></td>
							<td><?=$row['slu_number_screen'];?></td>
							<td><?=$row['slu_type_screen'];?></td>
							<td><?=$row['slu_slu_cat'];?></td>
							<td><?=$row['version_name'];?></td>
						</tr>
					<?php
					}				
					?>
				</table>
		</div>
		<br>
		<?php ;};?>
	</div>
	<?php
	$result = sybase_query($query,$sql_conn1);
	$obAmount = sybase_num_rows($result);
	$row = sybase_fetch_assoc($result);	
	?>
	<div class = "block">
		<div class = "area_header">General</div>
		<div class = "area">
			<table>
				<tr>
					<th>Parameter</th>
					<th>Value</th>
				</tr>
				<tr>
					<td>Print class</td>
					<td><?=$row['print_class'];?></td>
				</tr>
				<tr>
					<td>Tax class</td>
					<td><?=$row['tax_class'];?></td>
				</tr>
				<?php 
				if($row['amt'] == '') {
				}
				else {?>
					<tr>
						<td>Amount</td>
						<td><b><?=$row['amt'];?></b></td>
					</tr>
				<?php
				}
				if($row['min_amount'] == '') {
				}
				else {?>
					<tr>
						<td>Min. amount</td>
						<td><b><?=$row['min_amount'];?></b></td>
					</tr>
				<?php
				}
				if($row['max_amount'] == '') {
				}
				else {?>
					<tr>
						<td>Max. amount</td>
						<td><b><?=$row['max_amount'];?></b></td>
					</tr>
				<?php
				}
				if($row['percentage'] == '') {
				}
				else {?>
					<tr>
						<td>Percent</td>
						<td><b><?=$row['percentage'].' %';?></b></td>
					</tr>							
				<?php
				}
				if($row['min_percent'] == '') {
				}
				else {?>
					<tr>
						<td>Min. percent</td>
						<td><b><?=$row['min_percent'].' %';?></b></td>
					</tr>	
				<?php
				}
				if($row['max_percent'] == '') {
				}
				else {?>
					<tr>
						<td>Max. percent</td>
						<td><b><?=$row['max_percent'].' %';?></b></td>
					</tr>
				<?php
				}?>	
				<tr>
					<td>Privilege</td>
					<td><?=$row['priv_lvl'];?></td>
				</tr>
				<tr>
					<td>Category</td>
					<td><?=$row['cat'];?></td>
				</tr>
				<?php 
				if($row['period_name'] == '') {
				}
				else {?>
					<tr>
						<td>Period 1</td>
						<td><?=$row['period_name'];?></td>
					</tr>
				<?php
				} 
				if($row['period2_name'] == '') {
				}
				else {?>
					<tr>
						<td>Period 2</td>
						<td><?=$row['period2_name'];?></td>\
					</tr>
				<?php
				}
				if($row['period3_name'] == '') {
				}
				else {?>
					<tr>
						<td>Period 3</td>
						<td><?=$row['period3_name'];?></td>
					</tr>
				<?php
				}
				if($row['period4_name'] == '') {
				}
				else {?>
					<tr>
						<td>Period 4</td>
						<td><?=$row['period4_name'];?></td>
					</tr>
				<?php
				}?>
			</table>
		</div>
	</div>
	<br>
	<?php
	$result = sybase_query($query,$sql_conn1);
	$obAmount = sybase_num_rows($result);
	$row = sybase_fetch_assoc($result);	
	?>
	<div class = "mother_block">
		<div class = "block_left">
			<div class = "area_header">Options</div>
			<div class = "area">
				<table>
					<tr>
						<th>Parameter</th>
						<th>Value</th>
					</tr>
					<tr>
						<td>Preset:</td>
						<td>
							<?php
							if($row['preset_parameter'] == F)
								echo '<b style = "color: red; font-size: 18px">False</b>';
							elseif ($row['preset_parameter'] == T)
								echo '<b style = "color: green; font-size: 18px">True</b>';
							else echo '?';?></td>
					</tr>
					<tr>
						<td>Amount:</td>
						<td>
							<?php
							if($row['amount_parameter'] == F)
								echo '<b style = "color: red; font-size: 18px">False</b>';
							elseif ($row['amount_parameter'] == T)
								echo '<b style = "color: green; font-size: 18px">True</b>';
							else echo '?';?></td>
					</tr>
					<tr>
						<td>Reference required:</td>
						<td>
							<?php
							if($row['refreq_parameter'] == F)
								echo '<b style = "color: red; font-size: 18px">False</b>';
							elseif ($row['refreq_parameter'] == T)
								echo '<b style = "color: green; font-size: 18px">True</b>';
							else echo '?';?></td>
					</tr>
				</table>
			</div>	
		</div>	
		<div class = "block_right">
			<div class = "area_header">Restrictions</div>
			<div class = "area">
				<table>
					<tr>
						<th>Parameter</th>
						<th>Value</th>
					</tr>
					<tr>
						<td>One discount per item:</td>
						<td>
							<?php
							if($row['by_item'] == F)
								echo '<b style = "color: red; font-size: 18px">False</b>';
							else
								echo '<b style = "color: green; font-size: 18px">True</b>';?></td>
					</tr>
					<tr>
						<td>One discount per seat:</td>
						<td>
							<?php
							if($row['by_seat'] == F)
								echo '<b style = "color: red; font-size: 18px">False</b>';
							else
								echo '<b style = "color: green; font-size: 18px">True</b>';?></td>
					</tr>
					<tr>
						<td>One discount per check:</td>
						<td>
							<?php
							if($row['by_check'] == F)
								echo '<b style = "color: red; font-size: 18px">False</b>';
							else
								echo '<b style = "color: green; font-size: 18px">True</b>';?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<br>	
	<div class = "block">	
		<div class = "area_header">Active</div>
		<div class = "area">
			Is discount active?: 
			<?php 
			if ($row['active'] == 'not active')
				echo '<b style = "color: red; font-size: 20px">No</b>';
			else
				echo '<b style = "color: green; font-size: 20px">Yes</b>'; ?>
			<table>
				<tr>
					<th>Parameter</th>
					<th>Value</th>
				</tr>
				<tr>
					<td>Effective from date</td>
					<td><?php 
					if($row['effective_from'] == '')
						echo '-';
					else
						echo substr($row['effective_from'],0,-6)." ".substr($row['effective_from'],-2)?></td>
				</tr>
				<tr>
					<td>Effective to date</td>
					<td><?php
					if($row['effective_from'] == '')
						echo '-';
					else
						echo substr($row['effective_to'],0,-6)." ".substr($row['effective_to'],-2)?></td>
				</tr>
			</table>
		</div>
	</div>
	<br>
	<div class = "block">
		<div class = "area_header">Discount Itemizers</div>
		<div class = "area">
			<table>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Is discount itemizer checked?</th>
			</tr>
			<?php 
			$itemizers_name; //needed to menu items info module
			$itemizers_results; //needed to menu items info module
			$itemizers_amount; //needed to menu items info module
			$result = sybase_query($query,$sql_conn1);
			$obAmount = sybase_num_rows($result);
			$row = sybase_fetch_assoc($result);
			$itemizer_mask = $row['itemizer_mask'];
			$result = sybase_query($query_itemizer,$sql_conn1);
			$obAmount = sybase_num_rows($result);
			$itemizers_amount = $obAmount;
			include('itemizers.php');
			for($x = 0; $x < $obAmount; $x++) {
				$row = sybase_fetch_assoc($result);?>
				<tr>
					<td><?=$row['disc_itmzr_seq'];?></td>
					<td><?=$row['itemizer_name'];?></td>
					<td>
					<?php
						if($itemizer_mask & $itemizer[$x]) {
							echo '<b style = "color: green; font-size: 18px">Checked</b>';
							$itemizers_name[$x] = $row['itemizer_name'];
							$itemizers_results[$x] = '<b style = "color: green; font-size: 18px">Checked</b>';
						}
						else { 
							echo '<b style = "color: red; font-size: 18px">Unchecked</b>';
							$itemizers_name[$x] = $row['itemizer_name'];
							$itemizers_results[$x] = '<b style = "color: red; font-size: 18px">Unchecked</b>';
						}?>
					</td>
				</tr>	
			<?php }?>
		</table>
		</div>
	</div>
	<br>
	<?php 
	$result = sybase_query($query,$sql_conn1);
	$obAmount = sybase_num_rows($result);
	$row = sybase_fetch_assoc($result);
	if($row['rule_type_nr'] == '') {
		}
	else {?>
		<div class = "block">
			<div class = "area_header">Rule Setup</div>
			<?php
			$RuleType[1] = 'Item Price Substitution';
			$RuleType[2] = 'Quantity Threshold';
			$RuleType[3] = 'Total Price Threshold';
			$RuleType[4] = 'Combination Pricing';
			$RuleType[5] = 'Sales Price';
			$RuleType[6] = 'Multi Item Price Threshold';

			$AwardType[1] = 'Percent Off';
			$AwardType[2] = 'Amount Off';
			$AwardType[3] = 'Amount Substitution';
			?>
			<div class = "area">
				<table>
					<tr>
						<th>Parameter</th>
						<th>Value</th>
					</tr>
					<tr>
						<td>Rule type</td>
						<td><?=$RuleType[$row['rule_type_nr']];?></td>
					</tr>
					<?php
					if($row['trigger1'] == '') { 
					}
					else {?>
						<tr>
							<td>Trigger 1</td>
							<td><?=$row['nr_trigger1'].' - '.$row['trigger1'];?></td>
						</tr>
					<?php };
					if($row['trigger2'] == '') {
					}
					else {?>
						<tr>
							<td>Trigger 2</td>
							<td><?=$row['nr_trigger2'].' - '.$row['trigger2'];?></td>
					</tr>
					<?php };
					if($row['trigger3'] == '') {
					} 
					else {?>
						<tr>
							<td>Trigger 3</td>
							<td><?=$row['nr_trigger3'].' - '.$row['trigger3'];?></td>
						</tr>
					<?php };
					if($row['trigger4'] == '') { 
					}
					else {?>
						<tr>
							<td>Trigger 4</td>
							<td><?=$row['nr_trigger4'].' - '.$row['trigger4'];?></td>
						</tr>
					<?php };
					if($row['trigger5'] == '') {
					}
					else {?>
						<tr>
							<td>Trigger 5</td>
							<td><?=$row['nr_trigger5'].' - '.$row['trigger5'];?></td>
						</tr>
					<?php };
					if($row['trigger6'] == '') {
					}
					else {?>
						<tr>
							<td>Trigger 6</td>
							<td><?=$row['nr_trigger6'].' - '.$row['trigger6'];?></td>
						</tr>
					<?php };
					if($row['trigger7'] == '') {
					}
					else {?>
						<tr>
							<td>Trigger 7</td>
							<td><?=$row['nr_trigger7'].' - '.$row['trigger7'];?></td>
						</tr>
					<?php };
					if($row['trigger8'] == '') {
					}
					else {?>
						<tr>
							<td>Trigger 8</td>
							<td><?=$row['nr_trigger8'].' - '.$row['trigger8'];?></td>
						</tr>
					<?php };
					if($row['trigger_quantity'] == '') {
					}
					else {?>
						<tr>
							<td>Trigger Quantity</td>
							<td><?=$row['trigger_quantity'];?></td>
						</tr>
					<?php };
					if($row['trigger_total'] == '') {
					}
					else {?>
						<tr>
							<td>Trigger Total</td>
							<td><?=$row['trigger_total'];?></td>
						</tr>
					<?php };
					if($row['award_mi_set'] == '') {
					}
					else {?>
						<tr>
							<td>Award MI Set</td>
							<td><?=$row['nr_award_mi_set'].' - '.$row['award_mi_set'];?></td>
						</tr>
					<?php };
					if($row['award_quantity'] == '') {
					} 
					else {?>
						<tr>
							<td>Award Quantity</td>
							<td><?=$row['award_quantity'];?></td>
						</tr>
					<?php };
					if($row['max_award_quantity'] == '') {
					}
					else {?>
						<tr>
							<td>Max Award Quantity</td>
							<td><?=$row['max_award_quantity'];?></td>
						</tr>
					<?php };
					if($row['award_type_nr'] == '') {
					}
					else {?>
						<tr>
							<td>Award Type</td>
							<td><?=$AwardType[$row['award_type_nr']];?></td>
						</tr>
					<?php
					};?>				
				</table>
			</div>
		</div>
		<br>
	<?php }
	if($row['trigger1'] == '' && $row['trigger2'] == '' && $row['trigger3'] == '' && $row['trigger4'] == '' && $row['trigger5'] == '' && $row['trigger6'] == '' && $row['trigger7'] == '' && $row['trigger8'] == '') { $does_discount_have_any_triggers = 0; } else { $does_discount_have_any_triggers = 1;?>
			<div class = "block">
				<div class = "area_header">Menu Item Sets</div>
					<div class = "area">
						<?php 
						$MIsetType[1] = 'All';
						$MIsetType[2] = 'Major Group';
						$MIsetType[3] = 'Family Group';
						$MIsetType[4] = 'Object number';
						$MIsetType[5] = 'Discount itemizer';
						$triggers_count = 0;
						
						for($x = 0; $x < 8; $x++) {

							$result = sybase_query($query,$sql_conn1);
							$obAmount = sybase_num_rows($result);
							$row = sybase_fetch_assoc($result);
							
							if($row['trigger'.($x+1)] == '') { 
							}
							else {?>
								<table>
									<tr>
										<th colspan = "4">Trigger <?=$x+1?> | <?=$row['nr_trigger'.($x+1)].' - '.$row['trigger'.($x+1)];?></th>
									</tr>
									<tr>
										<th>Type of MI Set</th>
										<th>Name of Set Member</th>
										<th>Start number</th>
										<th>End number</th>
									</tr>
									<?php

									//here we are declaring variables that will be used in menu items info module
									$triggers[$x+1][1] = $row['nr_trigger'.($x+1)];
									$triggers[$x+1][2] = $row['trigger'.($x+1)];

									$result = sybase_query($query_rule_setups[$x],$sql_conn1);
									$obAmount = sybase_num_rows($result);
									for($y = 0; $y < $obAmount; $y++) {
									$row = sybase_fetch_assoc($result);?>
									<tr>
										<td><?=$MIsetType[$row['Type']];?></td>
										<td><?=$row['MemberName'];?></td>
										<td><?=$row['StartObjNum'].' - '.$row['StartName'];?></td>
										<td><?=$row['EndObjNum'].' - '.$row['EndName'];?></td>
									</tr>
									<?php }?>

									<?php

									//here we are declaring variables that will be used in menu items info module

									$triggers[$x+1][3] = $MIsetType[$row['Type']];
									$triggers[$x+1][4] = $row['StartObjNum'];
									$triggers[$x+1][5] = $row['EndObjNum'];
									$triggers_count += 1;
									?>									
								</table>

								<?php
								$result = sybase_query($query,$sql_conn1);
								$obAmount = sybase_num_rows($result);
								$row = sybase_fetch_assoc($result);
								
								if($row['rule_type_nr'] == 2) {?>
									<table>
										<tr>
											<th colspan = "4">Award MI Set | <?=$row['nr_award_mi_set'].' - '.$row['award_mi_set'];?></th>
										</tr>
										<tr>
											<th>Type of MI Set</th>
											<th>Name of Set Member</th>
											<th>Start number</th>
											<th>End number</th>
										</tr>
										<?php
										$result = sybase_query($query_award_mi_set,$sql_conn1);
										$obAmount = sybase_num_rows($result);
										for($y = 0; $y < $obAmount; $y++) {
										$row = sybase_fetch_assoc($result);?>
										<tr>
											<td><?=$MIsetType[$row['Type']];?></td>
											<td><?=$row['MemberName'];?></td>
											<td><?=$row['StartObjNum'].' - '.$row['StartName'];?></td>
											<td><?=$row['EndObjNum'].' - '.$row['EndName'];?></td>
										</tr>
										<?php }?>											
									</table>
								<?php }?>	
							<?php }
						}?>
					</div>
			</div>
		<br>
		<?php }?>
		<div class = "block">
			<div class = "area_header">Menu Item Classes</div>
				<div class = "area">
				<?php
				$result = sybase_query($query_menu_item_class,$sql_conn1);
				$obAmount = sybase_num_rows($result);
				?>
				<table>
					<tr>
						<th>Menu Item Class</th>
						<th>Itemizer</th>
					</tr>
					<?php for($x = 0; $x < $obAmount; $x++) { 
						$row = sybase_fetch_assoc($result);?>
						<tr>
							<td><?=$row['menu_item_class_nr'].' - '.$row['menu_item_class_name'];?></td>
							<td><?=$row['disc_itmzr_seq'].' - '.$row['itemizer_name'];?></td>
						</tr>
					<?php }; ?>
				</table>
				</div>
		</div>
		<br>
		<div class = "block">
			<div class = "area_header">Menu Items Info</div>
			<div class = "area">
			In this module you can check, if the item is related to this discount.<br><br>
			Module checks, if the selected menu item is related to the checked itemizer or the trigger.<br><br>
			<form action = "discount.php" method = "get">
				Please, type the exact object number of your item:
				<input type = "text" name = "searchobject" value = "<?=$searchobject;?>"> 
				<input type = "hidden" name = "concept" value = "<?=$_GET['concept'];?>">
				<input type = "hidden" name = "obj_num" value = "<?=$objnumber;?>">
				<input type = "hidden" name = "searchtext" value = "<?=$_GET['searchtext'];?>">
				<button type="submit">Submit</button>
				<?php
				if($searchobject != '') {	
					$result = sybase_query($query_menu_item_info,$sql_conn1);
					$obAmount = sybase_num_rows($result);
					$row = sybase_fetch_assoc($result);
					if($row['item_obj_num'] == '')
						echo '<br><b>There is no such item with this object number in this database!<b>';
					else {
						echo '<h2>'.$row['item_obj_num'].' - '.$row['item_name'].'</h2>';?>
						<?php $menu_item_number = $row['item_obj_num'];
						$menu_item_name = $row['item_name']; // it will be used to checking if the item is related to the discount by trigger 

						$is_item_related_to_discount_by_itemizer = 0; ?>
						<table>
							<tr>
								<th>Parameter</th>
								<th>Value</th>
							</tr>
							<tr>
								<td>Menu Item Class</td>
								<td><?=$row['mic_number'].' - '.$row['mic_name'];?></td>
							</tr>
							<tr>
								<td>Menu Item Class discount itemizer</td>
								<td><?=$row['itemizer_numer'].' - '.$row['itemizer_name'];?></td>
							</tr>
							<tr>
								<td>Is itemizer "<b><?=$row['itemizer_numer'].' - '.$row['itemizer_name'];?></b>" checked in this discount?</td>
								<td>
									<?php
									for($x = 0; $x < $itemizers_amount; $x++) {
										if($row['itemizer_name'] == $itemizers_name[$x]) {
											echo $itemizers_results[$x];
											$last_result = $itemizers_results[$x];
										}
									};?>
								</td>
							</tr>							
						</table>
						<?php
						if($last_result == '<b style = "color: green; font-size: 18px">Checked</b>') {
							if($does_discount_have_any_triggers == 0)
								echo 'Discount is related to this item <b>by itemizer</b>, so it should works with this item.';
							elseif($does_discount_have_any_triggers == 1)
								echo 'Discount is related to this item <b>by itemizer</b>, but this discount has also triggers so it depends on them if this discount will be working with this item.';
							$is_item_related_to_discount_by_itemizer = 1;
						}
						else 
							echo 'Discount is <b>not</b> related to this item <b>by itemizer.</b>';

						$result = sybase_query($query,$sql_conn1);
						$obAmount = sybase_num_rows($result);
						$row = sybase_fetch_assoc($result);

						//checking if the discount has any triggers
						if($row['trigger1'] == '' && $row['trigger2'] == '' && $row['trigger3'] == '' && $row['trigger4'] == '' && $row['trigger5'] == '' && $row['trigger6'] == '' && $row['trigger7'] == '' && $row['trigger8'] == '') {
							echo '<br><br>Discount does not have any triggers, so this item is not related to this discount <b>by trigger</b>.';
						 } else {?>
						 	<table>
						 		<tr>
							 		<th><b>#</b></th>
							 		<th>Name of trigger</th>
							 		<th>Is trigger related to the item?</th>
							 	</tr>
							 	<?php 

								$result = sybase_query($query_menu_item_info,$sql_conn1);
								$obAmount = sybase_num_rows($result);
								$is_item_related_to_discount_counter = 0;

							 	for($x = 0; $x < $triggers_count; $x++) {
									$row = sybase_fetch_assoc($result); ?>
							 		<tr>
							 			<td><?=$x+1?></td>
							 			<td><?=$triggers[$x+1][1].' - '.$triggers[$x+1][2];?>
							 			<td>
							 				<?php
							 					if($triggers[$x+1][3] == 'All')
							 						echo '<b style = "color: green; font-size: 18px">Related</b>';
							 					elseif($triggers[$x+1][3] == 'Major Group') {
							 						if($row['major_group_obj_num'] >= $triggers[$x+1][4] && $row['major_group_obj_num'] <= $triggers[$x+1][5]) {
							 							echo '<b style = "color: green; font-size: 18px">Related</b>';
														$is_item_related_to_discount_counter += 1;
													}
							 						else {					
														echo '<b style = "color: red; font-size: 18px">Unrelated</b>';
														echo ' | '.$triggers[$x+1][3].' \''.$row['major_group_obj_num'].' - '.$row['major_group_name'].'\' is out of range of this trigger.';
													}
							 					}
							 					elseif($triggers[$x+1][3] == 'Family Group') {
							 						if($row['fam_group_obj_num'] >= $triggers[$x+1][4] && $row['fam_group_obj_num'] <= $triggers[$x+1][5]) {
							 							echo '<b style = "color: green; font-size: 18px">Related</b>';
							 							$is_item_related_to_discount_counter += 1;
							 						}
							 						else {		
														echo '<b style = "color: red; font-size: 18px">Unrelated</b>';
														echo ' | '.$triggers[$x+1][3].' \''.$row['fam_group_obj_num'].' - '.$row['fam_group_name'].'\' is out of range of this trigger.';	
													}			 						
							 					}
							 					elseif($triggers[$x+1][3] == 'Object number') {
							 						if($row['item_obj_num'] >= $triggers[$x+1][4] && $row['item_obj_num'] <= $triggers[$x+1][5]) { 
							 							echo '<b style = "color: green; font-size: 18px">Related</b>';
							 							$is_item_related_to_discount_counter += 1;
							 						}
							 						else {
														echo '<b style = "color: red; font-size: 18px">Unrelated</b>';
														echo ' | '.$triggers[$x+1][3].' \''.$menu_item_number.' - '.$menu_item_name.'\' is out of range of this trigger.';
							 						}
							 					}
							 					elseif($triggers[$x+1][3] == 'Discount itemizer') {
							 						if($row['itemizer_numer'] >= $triggers[$x+1][4] && $row['itemizer_numer'] <= $triggers[$x+1][5]) {
							 							echo '<b style = "color: green; font-size: 18px">Related</b>';
							 							$is_item_related_to_discount_counter += 1;
							 						}
							 						else {
														echo '<b style = "color: red; font-size: 18px">Unrelated</b>';
														echo ' | '.$triggers[$x+1][3].' \''.$row['itemizer_numer'].' - '.$row['itemizer_name'].'\' is out of range of this trigger.';
							 						}
							 					}
							 				?>
							 			</td>
							 		</tr>
							 	<?php }?>
						 	</table>
						<?php
							if($is_item_related_to_discount_counter > 0) {
								if($is_item_related_to_discount_by_itemizer = 1)
									echo 'Discount is related to this item <b>by trigger</b> and it is related to this item <b>by itemizer</b> so it should work with this item.';
								elseif($is_item_related_to_discount_by_itemizer = 0)
									echo 'Discount is realted to this item <b>by trigger</b>, but it is not related to this item <b>by itemizer</b> so it will not work with this item.';
							}
							elseif($is_item_related_to_discount_counter == 0)
								echo 'Discount is not related to this item <b>by trigger</b> so it will not work with this item.';
						
						?>			
					<?php	}	
					}
				}
			?>
			</form>
		</div>
	</div>
<br>
</body>