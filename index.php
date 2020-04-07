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

		$orderby = $_GET['orderby'];
		if($orderby == 'ASC' || $orderby == 'DESC') {
		}
		else
			$orderby = 'ASC';

		$ordertype = $_GET['ordertype'];
		if($ordertype == 'obj_num' || $ordertype == 'name' || $ordertype == 'active' || $ordertype == 'effective_from' || $ordertype == 'effective_to' || $ordertype == 'onscreen' || $ordertype == 'menulevel') {
		}
		else
			$ordertype = 'obj_num';
	?>
		<div class = "intro">
			<h1>Discount Searcher</h1>
		</div>
		<div class = "searching">
			<form action = "index.php" method="get">
				<select name = "concept" class = "brand">
					<?php include 'conceptsoptions.inc'?>
				</select>
				<input type = "text" name = "searchtext" value = <?=$searchtext?>>
				<?php if($_GET['showinactive'])
						echo '<input type = "checkbox" name = "showinactive" value = "showinactive" checked> Show also inactive';
					else
						echo '<input type = "checkbox" name = "showinactive" value = "showinactive"> Show also inactive';
					?>
				<button type="submit">Submit</button>
			</form>
		</div>
		<?php if($_GET['concept'] != '') {} else {?>
		<div class = "info">
			Please, select concept that you want to search in.<br><br>

			In this tool you can search discounts using:
			<ul>
				<li>Name,</li>
				<li>number,</li>
				<li>preset,</li>
				<li>amount.</li>
			</ul>
		</div>
		<?php
	};
			if($_GET['concept'] == '') {} else {
			$sql_conn1 = @sybase_connect($_GET['concept'], "user", "password") or die("Could not connect to the server.");}
			$query = "SELECT DISTINCT
			t1.name name,
			t1.obj_num obj_num,
			t1.percentage percentage,
			t1.amt amt,
			t1.effective_from effective_from,
			t1.effective_to effective_to,
			t3.name menulevel,
			case when t1.dsvc_slu_seq is NOT NULL OR (t2.key_num is NOT NULL AND t2.key_type = '5') then 'yes' else 'no' end as onscreen,
			case when t1.effective_to > (SELECT NOW()) or t1.effective_to is null then 'active' else 'not active' end as active
			FROM micros.dsvc_def t1 
			LEFT JOIN micros.ts_key_def t2 ON t2.key_num = t1.dsvc_seq AND t2.key_type = '5'
			LEFT JOIN micros.mlvl_class_def t3 ON t1.mlvl_class_seq = t3.mlvl_class_seq
			WHERE 
			(name LIKE '%$searchtext%' OR 
			obj_num LIKE '%$searchtext%' OR 
			percentage LIKE '%$searchtext%' OR 
			amt LIKE '%$searchtext%') AND
			active = 'active' AND
			(t2.key_type = '5' or t2.key_type = null)
			ORDER BY $ordertype $orderby, obj_num";

			$query_inactive = "SELECT DISTINCT
			t1.name name,
			t1.obj_num obj_num,
			t1.percentage percentage,
			t1.amt amt,
			t1.effective_from effective_from,
			t1.effective_to effective_to,
			t3.name menulevel,
			case when t1.dsvc_slu_seq is NOT NULL OR (t2.key_num is NOT NULL AND t2.key_type = '5') then 'yes' else 'no' end as onscreen,
			case when t1.effective_to > (SELECT NOW()) or t1.effective_to is null then 'active' else 'not active' end as active
			FROM micros.dsvc_def t1 
			LEFT JOIN micros.ts_key_def t2 ON t2.key_num = t1.dsvc_seq AND t2.key_type = '5'
			LEFT JOIN micros.mlvl_class_def t3 ON t1.mlvl_class_seq = t3.mlvl_class_seq
			WHERE 
			(name LIKE '%{$_GET['searchtext']}%' OR 
			obj_num LIKE '%{$_GET['searchtext']}%' OR 
			percentage LIKE '%{$_GET['searchtext']}%' OR 
			amt LIKE '%{$_GET['searchtext']}%') AND
			(t2.key_type = '5' or t2.key_type = null)
			ORDER BY $ordertype $orderby, obj_num";


			if($_GET['concept'] == '') { } else {
				if($_GET['showinactive'] == FALSE) {
					$result = sybase_query($query,$sql_conn1);
					} else {
						$result = sybase_query($query_inactive,$sql_conn1);
					};
					$obAmount = sybase_num_rows($result);
					echo '<div class = "info">'.$obAmount.' results were found.</div>';};

		?>
		<div class = "discounts_table">
			<?php if($_GET['concept'] == '') {} else {?>
			<table>
				<tr>
					<th class = "first_column">#</th>
					<th>Number 
						<?php 
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=DESC&ordertype=obj_num>'.▼.'</a>';
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=ASC&ordertype=obj_num>'.▲.'</a>';?>
					</th>
					<th>Name
						<?php 
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=DESC&ordertype=name>'.▼.'</a>';
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=ASC&ordertype=name>'.▲.'</a>';?>
					</th>
					<th>Active
						<?php 
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=DESC&ordertype=active>'.▼.'</a>';
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=ASC&ordertype=active>'.▲.'</a>';?>
					</th>
					<th>Effective from
						<?php 
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=DESC&ordertype=effective_from>'.▼.'</a>';
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=ASC&ordertype=effective_from>'.▲.'</a>';?>
					</th>
					<th>Effective to
						<?php 
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=DESC&ordertype=effective_to>'.▼.'</a>';
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=ASC&ordertype=effective_to>'.▲.'</a>';?>
					</th>
					<th>OnScreen
						<?php 
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=DESC&ordertype=onscreen>'.▼.'</a>';
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=ASC&ordertype=onscreen>'.▲.'</a>';?>
					</th>
					<th>Menu Level
						<?php 
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=DESC&ordertype=menulevel>'.▼.'</a>';
						echo '<a href = http://discountsearcher/index.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&showinactive='.$_GET['showinactive'].'&obj_num='.$row['obj_num'].'&orderby=ASC&ordertype=menulevel>'.▲.'</a>';?>
					</th>
				</tr>
				<?php
				}
					for($x = 0; $x < $obAmount; $x++){
						$row = sybase_fetch_assoc($result);?>
							<tr>
								<td class = "first_column">
									<?=$x+1;?>
								</td>
								<td>
								<?='<a href=http://discountsearcher/discount.php?concept='.$_GET['concept'].'&searchtext='.$_GET['searchtext'].'&obj_num='.$row['obj_num'].'>'.$row['obj_num'].'</a>';?>
								</td>
								<td>
									<?=$row['name'];?>
								</td>
								<td>
									<?=$row['active'];?>
								</td>
								<td>
									<?=substr($row['effective_from'],0,-6)." ".substr($row['effective_from'],-2);?>
								</td>
								<td>
									<?=substr($row['effective_to'],0,-6)." ".substr($row['effective_to'],-2);?>
								</td>
								<td>
									<?=$row['onscreen'];?>
								</td>
								<td>
									<?=$row['menulevel'];?>
								</td>
								</tr>
						<?php } ?>
			</table>
		</div>
</body>
