<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>


	<style type="text/css">
		/* .blink {
        animation: blinker 1s step-start infinite;
    }

    @keyframes blinker {
        50% {
            opacity: 0;
        }
    } */
	</style>


	<script>
		function CopyToClipboard(containerid) {
			var range = document.createRange();
			range.selectNode(document.getElementById(containerid));
			window.getSelection().removeAllRanges(range);
			window.getSelection().addRange(range);
			document.execCommand("copy");
		}
	</script>


</head>

<body>
	<?
	include('../includes/common.php');
	session_start();
	error_reporting(0);
	$localIP = getHostByName(getHostName());
	$file = $localIP . '_db_connectin.txt';
	$current = file_get_contents($file);
	list($first, $second, $table_name) = explode('_split_', $current);

	$firstArr = explode("*", $first);
	$secondArr = explode("*", $second);
	$parent_owner = strtoupper($firstArr[1]);
	$compare_owner = strtoupper($secondArr[1]);

	$dev_conn = "//182.160.107.70:6935/LOGICDB*PLATFORMERPV3*PLATFORMERPV3";
	$first = ($first == '') ? $dev_conn : $first;
	?>

	<script>
		function confirm_process() {
			// document.getElementById('table_sequence').style.visibility="visible";
			// document.getElementById('create_table_constraints').style.visibility="visible";
			// document.getElementById('create_table').style.visibility="visible";
			// document.getElementById('execute').style.visibility="visible";
		}
	</script>




	<div id="contents">
		<?



		extract($_REQUEST);

		if ($first_connection != '' && $second_connection != '' && (isset($_POST['connect_db']) || isset($_POST['connect_db_and_generate']))) {

			// write connection.......
			$ccf = "all_customer_connection.txt";
			$myfile = fopen($ccf, "r") or die("Missing trigger not found");
			$connection_arr = array();
			while (!feof($myfile)) {
				$triger_name = trim(fgets($myfile));
				if($triger_name!='')$connection_arr[$triger_name] = $triger_name;
			}
			fclose($myfile);
			$triger_name = $first_connection."_split_".$second_connection;
			$connection_arr[$triger_name] = $triger_name;

			$current_connection_data = implode("\n",$connection_arr);
			file_put_contents($ccf, $current_connection_data);
			//...............

			$dataStr = trim($first_connection) . '_split_' . trim($second_connection) . '_split_' . trim($table_name);
			file_put_contents($file, $dataStr);
			$_SESSION["DB_EXPIRY_DATE"] = '';
			$_SESSION["message"] = '';

			$firstConArr = sql_select('select count(id) as ID from USER_PASSWD', '', $first_connection);
			$connStatus = ($firstConArr[0]['ID'] > 0) ? " First Connection Status  <b style='color:green'>Connected</b> " : " First Connection Status <b style='color:red'>Error</b>";

			$secondConArr = sql_select('select count(id) as ID from USER_PASSWD', '', $second_connection);

			$connStatus .= ($secondConArr[0]['ID'] > 0) ? ", Senod Connection Status <b style='color:green'>Connected</b> " : ", Second Connection Status <b style='color:red'>Error</b>";

			$_SESSION["message"] = $connStatus;

			//DB Expaire check...........
			$secondArr = explode("*", $second_connection);
			$compare_owner = $secondArr[1];
			$second_conn_db_expare_sql = "SELECT USERNAME, ACCOUNT_STATUS, LOCK_DATE, EXPIRY_DATE FROM dba_users WHERE username='$compare_owner'";
			$second_conn_db_expare_sql_res = sql_select($second_conn_db_expare_sql, '', $second_connection);
			$second_conn_db_expare_arr = array();
			foreach ($second_conn_db_expare_sql_res as $row) {
				$diffDays = datediff("d", date('Y-M-d') ,$row['EXPIRY_DATE']);
				$_SESSION["DB_EXPIRY_DATE"] = "<u>".$compare_owner."</u> Database Expiry Date: " . $row['EXPIRY_DATE'] .' Left '.$diffDays.' Days';
			}
			//...........................................
			
		 
			//unset($_POST['connect_db']);
			if ($_POST['connect_db_and_generate']) {
				header("location:" . htmlspecialchars($_SERVER['PHP_SELF']) . "?generate=Generate");
			} else {
				header("location:" . htmlspecialchars($_SERVER['PHP_SELF']) . "");
			}
		}

		$file = "all_customer_connection.txt";
		$myfile = fopen($file, "r") or die("Missing procedure not found");
		$option_html = "";
		while (!feof($myfile)) {
			list($fc,$sc) = explode('_split_',fgets($myfile));
			if($sc)$option_html .= '<option value="'.$sc.'">';
		}
		fclose($myfile);

		?>

		<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return confirm_process()"  autocomplete="on">
			<table width="800">
				<tr>
					<td align="right" width="130" title="//59.152.60.147:15252/orcl*LOGIC3RDVERSION*LOGIC3RDVERSION">
						First Connection</td>
					<td width="15">:</td>
					<td><input type="text" name="first_connection" value="<?= $first; ?>" required style="width:100%;" placeholder="//59.152.60.147:15252/orcl*LOGIC3RDVERSION*LOGIC3RDVERSION" /></td>
				</tr>

				<tr>
					<td align="right">Second Connection</td>
					<td>:</td>
					<td>
						<input list="second_connection_list" type="text" name="second_connection" value="<?= $second; ?>" required style="width:100%;" />
						<datalist id="second_connection_list"><?= $option_html;?></datalist>
					</td>
				</tr>
				<tr>
					<td align="right">Table</td>
					<td>:</td>
					<td><input type="text" name="table_name" value="<?= $table_name; ?>" style="width:100%;" placeholder="Not required" /></td>
				</tr>
				<tr>
					<td align="right"><b style="color:#F00">Connection Note</b></td>
					<td>:</td>
					<td>
						<small style="color:#F00">Please set connection like:
							//host:port/service*Schama*password</small>
					</td>
				</tr>

				<tr>
					<td align="right"><b style="color:#F00">Buttons Note</b></td>
					<td>:</td>
					<td>
						<small style="color:#F00">1st Stage Connect DB, 2nd Stage Generate</small>
					</td>
				</tr>

				<tr>
					<td colspan="3" align="center">
						<input type="submit" id="connect_db" name="connect_db" value="Connect DB" style="cursor:pointer; width:49%;color:green;float:left;" />
						<input type="submit" id="generate" name="generate" value="Generate" style="cursor:pointer; width:49%;color:green;float:right;" />
					</td>
				</tr>
				<tr>
					<td colspan="3" align="right">
						<input type="submit" name="table_function" value="Function" style="cursor:pointer;" />
						<input type="submit" name="table_trigger" value="Trigger" style="cursor:pointer;" />
						<input type="submit" name="table_procedure" value=" Procedure" style="cursor:pointer;" />
						<input type="submit" name="table_sequence" value="Sequence" style="cursor:pointer;" />
						<input type="submit" name="create_table_constraints" value="Constraints" style="cursor:pointer;" />
						<input type="submit" name="create_table" value="Table & constraints" style="cursor:pointer;" />
						<input type="submit" name="execute" value="Alter Query" style="cursor:pointer;" />
					</td>
				</tr>
				<tr>
					<td colspan="3" align="center">
						<b><?= ($_SESSION["message"] != '') ? $_SESSION["message"] : ""; ?></b><br>
						<div class="blink">
							<h2 style="color:red">
								<?= ($_SESSION["DB_EXPIRY_DATE"] != '') ? $_SESSION["DB_EXPIRY_DATE"] : ""; ?></h2>
						</div>

					</td>
				</tr>

			</table>
		</form>

		<?

		if (isset($_POST['table_procedure'])) {
			$file = $localIP . "_create_table_PROCEDURE_file.txt";
			$myfile = fopen($file, "r") or die("Missing procedure not found");
			while (!feof($myfile)) {
				echo fgets($myfile) . "<br>";
			}
			fclose($myfile);
			unset($_POST['create_table']);
			exit();
		} else if (isset($_POST['table_trigger'])) {
			$file = $localIP . "_create_table_TRIGGER_file.txt";
			$myfile = fopen($file, "r") or die("Missing trigger not found");
			while (!feof($myfile)) {
				$triger_name = fgets($myfile);
				echo $triger_name . "<br>";
			}
			fclose($myfile);
			unset($_POST['create_table']);
			exit();
		} else if (isset($_POST['table_function'])) {
			$file = $localIP . "_create_table_FUNCTION_file.txt";
			$myfile = fopen($file, "r") or die("Missing function not found");
			while (!feof($myfile)) {
				echo fgets($myfile) . "<br>";
			}
			fclose($myfile);
			unset($_POST['create_table']);
			exit();
		} else if (isset($_POST['create_table'])) {
			$file = $localIP . "_create_table_query_file.txt";

			$myfile = fopen($file, "r") or die("Missing table not found");
			while (!feof($myfile)) {
				echo fgets($myfile) . "<br>";
			}
			fclose($myfile);

			unset($_POST['create_table']);
			exit();
		} else if (isset($_POST['table_sequence'])) {
			$file = $localIP . "_create_table_seq_file.txt";

			$myfile = fopen($file, "r") or die("Missing sequence not found");
			while (!feof($myfile)) {
				$myfileStr = fgets($myfile);

				foreach (explode('###', $myfileStr) as $dataRow) {
					list($table_name, $seq_name) = explode('***', $dataRow);

					$secondConArr = sql_select("select max(id) as ID from $table_name", '', $second_connection);

					$max_id = $secondConArr[0]['ID'];

					$seqSql = "CREATE SEQUENCE " . $compare_owner . "." . $seq_name . "<br>
					START WITH " . ($max_id + 1) . " <br>
					MAXVALUE 9999999999999999999999999999 <br>
					MINVALUE 1 <br>
					NOCYCLE <br>
					CACHE 20 <br>
					NOORDER;";
					echo $seqSql . "<br><br>";
				}



				//$max_id = "select max(id)+1 as id from $table_name";


			}
			fclose($myfile);

			unset($_POST['table_sequence']);
			exit();
		} else if (isset($_POST['create_table_constraints'])) {
			$file = $localIP . "_create_table_constraints_file.txt";
			echo "<div id='constraints_query' ondblclick='CopyToClipboard(\"constraints_query\")'>";
			$myfile = fopen($file, "r") or die("Unable to open file!");
			while (!feof($myfile)) {
				echo fgets($myfile) . "<br>";
			}
			fclose($myfile);
			echo "</div>";
			unset($_POST['create_table_constraints']);
			exit();
		} else if (isset($_POST['execute'])) {

			$file = $localIP . "_execute_query_file.txt";
			echo "<div id='alter_query' ondblclick='CopyToClipboard(\"alter_query\")'>";
			$myfile = fopen($file, "r") or die("Unable to open file!");
			while (!feof($myfile)) {
				echo fgets($myfile) . "<br>";
			}
			fclose($myfile);
			echo "</div>";
			unset($_POST['execute']);
			exit();
		} else if (isset($_REQUEST['generate'])) {

			$first = $first_connection;
			$second = $second_connection;

			$table_name = strtoupper($table_name);
			if ($table_name != '') {
				$whereCon = " AND t.table_name LIKE ('%$table_name%')";
			}


			//Compare data.........................................
			$sql = "SELECT t.OWNER, t.TABLE_NAME, t.num_rows AS TOTAL_ROWS, COUNT (*) AS TOTAL_COLOUMS
		FROM all_tables t
		LEFT JOIN all_tab_columns c ON t.table_name = c.table_name and t.owner=c.owner
		WHERE t.owner = '$compare_owner' $whereCon
		GROUP BY t.owner, t.table_name, t.num_rows
		ORDER BY t.table_name";
			//echo $sql;die;
			$sql_com_res = sql_select($sql, '', $second);
			foreach ($sql_com_res as $row) {
				$dataArr['compare_owner'] = $row['OWNER'];
				$dataArr['compare_table'][$row['TABLE_NAME']] = $row['TABLE_NAME'];
				$dataArr['compare'][$row['TABLE_NAME']] = array(
					'TABLE_NAME' => $row['TABLE_NAME'],
					'TOTAL_ROWS' => $row['TOTAL_ROWS'],
					'TOTAL_COLOUMS' => $row['TOTAL_COLOUMS']
				);
			}



			//Parent data.........................................
			$sql = "SELECT t.OWNER, t.TABLE_NAME, t.num_rows AS TOTAL_ROWS,  COUNT (*) AS TOTAL_COLOUMS
		FROM all_tables t
		LEFT JOIN all_tab_columns c ON t.table_name = c.table_name and t.owner=c.owner
		WHERE t.owner = '$parent_owner' $whereCon
		GROUP BY t.owner, t.table_name, t.num_rows
		ORDER BY t.table_name";
			//echo $sql;die;
			$sql_par_res = sql_select($sql, '', $first);
			foreach ($sql_par_res as $row) {
				$dataArr['parent_owner'] = $row['OWNER'];
				$dataArr['parent'][$row['TABLE_NAME']] = array(
					'TABLE_NAME' => $row['TABLE_NAME'],
					'TOTAL_ROWS' => $row['TOTAL_ROWS'],
					'TOTAL_COLOUMS' => $row['TOTAL_COLOUMS']
				);


				//Missing coloum...............................................
				if ($dataArr['compare'][$row['TABLE_NAME']]['TOTAL_COLOUMS'] != $row['TOTAL_COLOUMS']) {
					$missingDataArr['coloums'][$row['TABLE_NAME']] = array(
						'TABLE_NAME' => $dataArr['compare'][$row['TABLE_NAME']]['TABLE_NAME'],
						'TOTAL_ROWS' => $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_ROWS'],
						'TOTAL_COLOUMS' => $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_COLOUMS']
					);
					$coloum_missing_table_arr[$row['TABLE_NAME']] = $row['TABLE_NAME'];
				}



				//Missing rows...............................................
				if ($dataArr['compare'][$row['TABLE_NAME']]['TOTAL_ROWS'] != $row['TOTAL_ROWS']) {
					$missingDataArr['rows'][$row['TABLE_NAME']] = array(
						'TABLE_NAME' => $dataArr['compare'][$row['TABLE_NAME']]['TABLE_NAME'],
						'TOTAL_ROWS' => $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_ROWS'],
						'TOTAL_COLOUMS' => $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_COLOUMS']
					);
				}

				//Missing table...............................................
				if ($dataArr['compare_table'][$row['TABLE_NAME']] == '') {
					$missingDataArr['table'][$row['TABLE_NAME']] = $row['TABLE_NAME'];
				}
			}


			//=====================
			$client_sql = "SELECT  COLUMN_ID,TABLE_NAME,COLUMN_NAME,DATA_TYPE,CHAR_LENGTH,DATA_PRECISION FROM all_tab_columns WHERE OWNER='$compare_owner' and  table_name in('" . implode("','", $coloum_missing_table_arr) . "') order by table_name,COLUMN_ID";
			$client_sql_res = sql_select($client_sql, '', $second);
			foreach ($client_sql_res as $row) {

				$clientDataArr[$row['TABLE_NAME']][$row['COLUMN_NAME']] = $row['COLUMN_NAME'];
			}

			//echo $client_sql;die;
			$alterQueryReadyArr = array();
			$parent_sql = "SELECT  TABLE_NAME,COLUMN_NAME,DATA_TYPE,CHAR_LENGTH,DATA_PRECISION,DATA_DEFAULT,NULLABLE FROM all_tab_columns WHERE OWNER='$parent_owner'  and  table_name in('" . implode("','", $coloum_missing_table_arr) . "')  order by table_name,COLUMN_ID";
			 //echo $parent_sql;die;

			//echo implode("','",$coloum_missing_table_arr);die;


			// var_dump($dataArr['compare_table']);die;


			$parent_sql_res = sql_select($parent_sql, '', $first);
			foreach ($parent_sql_res as $row) {
	
				if ($clientDataArr[$row['TABLE_NAME']][$row['COLUMN_NAME']] == '') {

					$devDataArr[$row['TABLE_NAME']][$row['COLUMN_NAME']] = array(
						'TABLE_NAME' => $row['TABLE_NAME'],
						'COLUMN_NAME' => $row['COLUMN_NAME'],
						'DATA_TYPE' => $row['DATA_TYPE'],
						'CHAR_LENGTH' => $row['CHAR_LENGTH'],
						'DATA_PRECISION' => $row['DATA_PRECISION'],
						'DATA_DEFAULT' => $row['DATA_DEFAULT'],
						'NULLABLE' => $row['NULLABLE']
					);

			

					//query ready........................
					if ($dataArr['compare_table'][$row['TABLE_NAME']] != '') {
						// echo $row['COLUMN_NAME'].'==='.$row['DATA_TYPE'];

						$DEFAULT = "";
						if (is_null($row['DATA_DEFAULT']) != 1) {
							$DEFAULT = " DEFAULT $row[DATA_DEFAULT]";
						}

						
						else if ($row['NULLABLE'] == 'N') {
							$NULLABLE = " NOT NULL";
						}

						if ($row['DATA_TYPE'] == 'VARCHAR2') {
							$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]($row[CHAR_LENGTH]) $DEFAULT $NULLABLE";
						} else if ($row['DATA_TYPE'] == 'NVARCHAR2') {
							$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]($row[CHAR_LENGTH]) $DEFAULT $NULLABLE";
						} else if ($row['DATA_TYPE'] == 'NUMBER' && $row['DATA_PRECISION'] > 0) {
							$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]($row[DATA_PRECISION]) $DEFAULT $NULLABLE";
						} else if ($row['DATA_TYPE'] == 'NUMBER') {
							$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE] $DEFAULT $NULLABLE";
						} else if (substr($row['DATA_TYPE'], 0, 9) == 'TIMESTAMP') {
							$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE] $DEFAULT $NULLABLE";
						} else {
							$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]";
						}

						$alterQueryReadyArr[] = "ALTER TABLE $row[TABLE_NAME] ADD ($CHAR_LENGTH);";

						//ALTER TABLE  STUDENTS ADD (status  NUMBER DEFAULT 0 NOT NULL);
					}
					//---------------------end query ready;

				}
			}

			//var_dump($devDataArr);die;

			$creatTableScriptArr = array();
			foreach ($missingDataArr['table'] as $tn) {
				$coloumnArr = array();
				foreach ($devDataArr[$tn] as $coloum_name => $row) {

					$CHAR_LENGTH = "";
					if ($row['DATA_TYPE'] == 'VARCHAR2') {
						$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]($row[CHAR_LENGTH])";
					} else if ($row['DATA_TYPE'] == 'NVARCHAR2') {
						$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]($row[CHAR_LENGTH])";
					} else if ($row['DATA_TYPE'] == 'NUMBER' && $row['DATA_PRECISION'] > 0) {
						$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]($row[DATA_PRECISION])";
					} else if ($row['DATA_TYPE'] == 'NUMBER') {
						$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]";
					} else if (substr($row['DATA_TYPE'], 0, 9) == 'TIMESTAMP') {
						$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]";
					} else {
						$CHAR_LENGTH = "$row[COLUMN_NAME]  $row[DATA_TYPE]";
					}

					//....................................
					$DEFAULT = "";
					if ($row['DATA_DEFAULT'] != '') {
						$DEFAULT = " DEFAULT $row[DATA_DEFAULT]";
					}
					$NULLABLE = "";
					if ($row['NULLABLE'] == 'N') {
						$NULLABLE = " NOT NULL";
					}
					$coloumnArr[$coloum_name] = $CHAR_LENGTH . $DEFAULT . $NULLABLE;
				}

				//$creatTableScriptArr[$tn]=" CREATE TABLE <strong>$tn</strong> (".implode(", ",$coloumnArr).");";

				//Constraints..............................................................
				$constraint_str = (strlen($tn) > 27) ? substr($tn, 0, -3) . '_PK' : $tn . '_PK';
				$createTableConstraintsArr[$tn] = " ALTER TABLE <strong>$tn</strong> ADD CONSTRAINT $constraint_str  PRIMARY KEY (ID)  ENABLE  VALIDATE;";
				//..............................................................end;

				$creatTableScriptArr[$tn] = " CREATE TABLE <strong>$tn</strong> (" . implode(", ", $coloumnArr) . ");" . $createTableConstraintsArr[$tn];
			}


			echo "<hr>";
			echo "<b>Total Missing Table :</b> " . count($missingDataArr['table']);
			echo "<br> <b>Missing Table List:</b> " . implode(",", $missingDataArr['table']);

			echo "<hr><b>Missing Create Script:</b> " . implode("<br><br>", $creatTableScriptArr);
			//put into text file for create table query next time.....................................
			file_put_contents($localIP . "_create_table_query_file.txt", implode("<br><br>", $creatTableScriptArr));
			//....................................end;

			//put into text file for create table constraints query next time.....................................
			file_put_contents($localIP . "_create_table_constraints_file.txt", implode("<br><br>", $createTableConstraintsArr));
			//....................................end;			


			echo "<hr><h3>Missing Coloum Alter Query..........................................</h3>";
			echo implode("<br>", $alterQueryReadyArr);

			//put in text file for execute next time.....................................
			file_put_contents($localIP . "_execute_query_file.txt", implode("\n", $alterQueryReadyArr));
			//....................................end;


		?>
			<hr>
			<h3>Missing Table & Coloum..........................................</h3>
			<table rules="all" border="1">
				<tr>
					<th>Table Name</th>
					<th>Coloum</th>
					<th>Data Type</th>
					<th>CHAR LENGTH</th>
					<th>DATA PRECISION</th>
				</tr>
				<?
				$i = 1;
				foreach ($devDataArr as $rowArr) {
					$f = 0;
					foreach ($rowArr as $row) {
						if ($f == 0) {
							echo "<tr><td rowspan='" . count($rowArr) . "'>" . $row['TABLE_NAME'] . "</td>";
						} else {
							echo "<tr>";
						}
				?>
						<td align="right"><?= $row['COLUMN_NAME']; ?></td>
						<td align="right"><?= $row['DATA_TYPE']; ?></td>
						<td align="right"><?= $row['CHAR_LENGTH']; ?></td>
						<td align="right"><?= $row['DATA_PRECISION']; ?></td>
						</tr>
				<?
						$f = 1;
						$i++;
					}
				}
				?>
			</table>



			<hr>
			<h3>Compare Table,Rows & Coloum..........................................</h3>
			<table rules="all" border="1">
				<tr>
					<th width="35" rowspan="2">SL</th>
					<th rowspan="2">Table Name</th>
					<th colspan="2"><?= $dataArr['parent_owner']; ?></th>
					<th colspan="2"><?= $dataArr['compare_owner']; ?></th>
					<th colspan="2">Diff</th>
				</tr>
				<tr>
					<th>Total Rows</th>
					<th>Total Coloum</th>
					<th>Total Rows</th>
					<th>Total Coloum</th>
					<th>Rows</th>
					<th>Coloum</th>
				</tr>
				<?
				$i = 1;
				foreach ($dataArr['parent'] as $row) {

					$coloum_bg = ($row['TOTAL_COLOUMS'] - $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_COLOUMS'] != 0) ? "#FFF000" : "";
					$row_bg = ($row['TOTAL_ROWS'] - $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_ROWS'] != 0) ? "#FFF000" : "";
				?>

					<tr>
						<td><?= $i; ?></td>
						<td><?= $row['TABLE_NAME']; ?></td>
						<td align="right"><?= $row['TOTAL_ROWS']; ?></td>
						<td align="right"><?= $row['TOTAL_COLOUMS']; ?></td>
						<td align="right"><?= $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_ROWS']; ?></td>
						<td align="right"><?= $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_COLOUMS']; ?></td>

						<td bgcolor="<?= $row_bg; ?>" align="right">
							<?= $row['TOTAL_ROWS'] - $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_ROWS']; ?></td>
						<td bgcolor="<?= $coloum_bg; ?>" align="right">
							<?= $row['TOTAL_COLOUMS'] - $dataArr['compare'][$row['TABLE_NAME']]['TOTAL_COLOUMS']; ?></td>

					</tr>
				<?
					$i++;
				}
				?>
			</table>


		<?

			//SEQUENCE part...........................................
			//customer...........
			$second_conn_seq_sql = "select SEQUENCE_NAME,LAST_NUMBER from ALL_SEQUENCES where SEQUENCE_OWNER='$compare_owner'";
			$second_conn_seq_sql_res = sql_select($second_conn_seq_sql, '', $second);
			$second_seq_arr = array();
			foreach ($second_conn_seq_sql_res as $row) {
				$second_seq_arr[$row['SEQUENCE_NAME']] = $row['SEQUENCE_NAME'];
			}
			//devlopment.............
			$parent_seq_table_sql = "SELECT TABLE_OWNER, TABLE_NAME, REFERENCED_OWNER AS SEQUENCE_OWNER, REFERENCED_NAME AS SEQUENCE_NAME
	FROM ALL_DEPENDENCIES d JOIN ALL_TRIGGERS t ON TRIGGER_NAME = d.NAME AND t.OWNER = d.OWNER
	WHERE REFERENCED_TYPE = 'SEQUENCE' and TABLE_OWNER = '$parent_owner'";
			 //echo $parent_seq_table_sql;die;
			$parent_seq_table_sql_res = sql_select($parent_seq_table_sql, '', $first);
			foreach ($parent_seq_table_sql_res as $row) {
				if ($second_seq_arr[$row['SEQUENCE_NAME']] == '') {
					$missing_seq_arr[$row['SEQUENCE_NAME']] = $row['TABLE_NAME'] . '***' . $row['SEQUENCE_NAME'];
				}
			}

			//put into text file table seq list  next time.....................................
			file_put_contents($localIP . "_create_table_seq_file.txt", implode("###", $missing_seq_arr));
			//....................................end;

			//........................................................................................end;

			//TRIGGER,FUNCTION,PROCEDURE,PACKAGE part...........................................

			//customer...........
			$second_conn_trig_fun_proc_pagk_sql = "SELECT OBJECT_NAME,OBJECT_TYPE FROM all_procedures WHERE OBJECT_TYPE IN ('TRIGGER','FUNCTION','PROCEDURE','PACKAGE') and owner = '$compare_owner' order by object_name";
			 //echo $second_conn_trig_fun_proc_pagk_sql;die;
			$second_conn_trig_fun_proc_pagk_sql_res = sql_select($second_conn_trig_fun_proc_pagk_sql, '', $second);
			$second_conn_trig_fun_proc_pagk_arr = array();
			foreach ($second_conn_trig_fun_proc_pagk_sql_res as $row) {
				$second_conn_trig_fun_proc_pagk_arr[$row['OBJECT_TYPE']][$row['OBJECT_NAME']] = $row['OBJECT_NAME'];
			}
			unset($second_conn_trig_fun_proc_pagk_sql_res);

			//dev...........
			$first_conn_trig_fun_proc_pagk_sql = "SELECT OBJECT_NAME,OBJECT_TYPE FROM all_procedures WHERE OBJECT_TYPE IN ('TRIGGER','FUNCTION','PROCEDURE','PACKAGE') and owner = '$parent_owner' order by object_name";
			//echo $first_conn_trig_fun_proc_pagk_sql;die;
			$first_conn_trig_fun_proc_pagk_sql_res = sql_select($first_conn_trig_fun_proc_pagk_sql, '', $first);
			$missing_trig_fun_proc_pagk_arr = array();
			foreach ($first_conn_trig_fun_proc_pagk_sql_res as $row) {
				if ($second_conn_trig_fun_proc_pagk_arr[$row['OBJECT_TYPE']][$row['OBJECT_NAME']] == '') {
					$missing_trig_fun_proc_pagk_arr[$row['OBJECT_TYPE']][$row['OBJECT_NAME']] = $row['OBJECT_NAME'];
				}
			}
			unset($first_conn_trig_fun_proc_pagk_sql_res);

			

			//Empty.......................................
			file_put_contents($localIP . "_create_table_TRIGGER_file.txt", 'Not Found TRIGGER');
			file_put_contents($localIP . "_create_table_PROCEDURE_file.txt", 'Not Found PROCEDURE');
			file_put_contents($localIP . "_create_table_FUNCTION_file.txt", 'Not Found FUNCTION');
			//...........................................end;
			foreach ($missing_trig_fun_proc_pagk_arr as $ob_type => $ob_name_arr) {
				file_put_contents($localIP . "_create_table_" . $ob_type . "_file.txt", implode("<br>", $ob_name_arr));
			}
			unset($missing_trig_fun_proc_pagk_arr);

			//........................................................................................end;


			unset($_REQUEST['generate']);
			exit();
		} //end generate if con



		?>


	</div>











</body>

</html>