<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
include('../../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];



if ($action == "save_update_delete") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	if ($operation == 0)	// Insert Here txt_consumption_factor
	{
		if (is_duplicate_field("resource_id", "LIB_OPERATION_RESOURCE", " resource_id=$txt_resource_id and process_id=$cbo_process_id and is_deleted=0") == 1) {
			echo "11**0";
			die;
		} else {
			$con = connect();
			$id = return_next_id("id", "LIB_OPERATION_RESOURCE", 1);
			$field_array = "id,resource_id,resource_name,process_id,consumption_factor,needle_thread,bobbin_thread,inserted_by,insert_date,status_active,is_deleted";
			$data_array = "(" . $id . "," . $txt_resource_id . "," . $txt_resource_name . "," . $cbo_process_id . ",". $txt_consumption_factor. ",". $txt_needle_thread.",". $txt_bobbin_thread . "," . $user_id . ",'" . $pc_date_time . "'," . $cbo_status . ",0)";
			//echo $data_array;die;
			$rID = sql_insert("LIB_OPERATION_RESOURCE", $field_array, $data_array, 1);

			if ($rID) {
				oci_commit($con);
				echo "0**" . $txt_resource_id . "**0";
			} else {
				oci_rollback($con);
				echo "5**" . "0" . "**0";
			}

			disconnect($con);
			die;
		}
	} else if ($operation == 1)   // Update here
	{
		if (is_duplicate_field("resource_id", "LIB_OPERATION_RESOURCE", " resource_id=$txt_resource_id and id<>$update_id and process_id=$cbo_process_id and is_deleted=0") == 1) {
			echo "11**0";
			die;
		} else {
			$con = connect();

			$field_array = "resource_id*resource_name*process_id*consumption_factor*needle_thread*bobbin_thread*updated_by*update_date*status_active";
			$data_array = $txt_resource_id . "*" . $txt_resource_name . "*" . $cbo_process_id. "*". $txt_consumption_factor. "*". $txt_needle_thread."*". $txt_bobbin_thread . "*" . $user_id . "*'" . $pc_date_time . "'*" . $cbo_status;
			$rID = sql_update("LIB_OPERATION_RESOURCE", $field_array, $data_array, "id", "" . $update_id . "", 1);

			if ($rID) {
				oci_commit($con);
				echo "1**" . $rID;
			} else {
				oci_rollback($con);
				echo "10**" . $rID;
			}
			disconnect($con);
			die;
		}
	} else if ($operation == 2)	//Delete here
	{
		$con = connect();

		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";
		$rID = sql_delete("LIB_OPERATION_RESOURCE", $field_array, $data_array, "id", "" . $update_id . "", 1);


		if ($rID) {
			oci_commit($con);
			echo "2**" . str_replace("'", "", $txt_task_name) . "**0";
		} else {
			oci_rollback($con);
			echo "7**" . str_replace("'", "", $txt_task_name) . "**1";
		}
		disconnect($con);
		die;
	}
}


if ($action == "saved_operation_resource_list") {

	if($data!=-0){$whereCon=" and PROCESS_ID=$data";}
	$sql = "select ID,RESOURCE_ID,RESOURCE_NAME,PROCESS_ID,INSERTED_BY,INSERT_DATE,UPDATED_BY,UPDATE_DATE,IS_DELETED,STATUS_ACTIVE from LIB_OPERATION_RESOURCE where is_deleted=0 $whereCon order by resource_name";

	//echo $sql;die;
	$resource_image_arr=return_library_array( "select MASTER_TBLE_ID,IMAGE_LOCATION from COMMON_PHOTO_LIBRARY where FORM_NAME = 'operation_resource' and IS_DELETED=0", "MASTER_TBLE_ID","IMAGE_LOCATION"  );

	//print_r($resource_image_arr);


	

	?>
	<div style="width:auto;" align="center">
		<fieldset>
			<div style="width:670px;">
				<table align="left" width="650" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
					<thead>
						<tr>
							<th colspan="6">
								<?
								echo create_drop_down("cbo_string_search_type", 200, $string_search_type, '', 1, "-- Searching Type --");
								?>
							</th>
						</tr>
						<tr>
							<th width="30" align="center">SL</th>
							<th width="70">Process</th>
							<th width="35">Image</th>
							<th>Resource Name</th>
							<th width="180">Resource Short Name</th>
							<th width="70">Status</th>
						</tr>
					</thead>
				</table>
			</div>

			<div style="overflow-y:scroll; max-height:200px; width:670px;" align="center">
				<table align="left" id="tbl_saved_operation_resource_list" width="650" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
					<?

					$i = 1;
					$data_array = sql_select($sql);
					foreach ($data_array as $rows) {
						$bgcolor = ($i % 2 == 0 ? "#E9F3FF" : "#FFFFFF");

						if ($rows['STATUS_ACTIVE'] == 1) {
							$bgcolor = "#33CC00";
						} else if ($rows['STATUS_ACTIVE'] == 2) {
							$bgcolor = "#FFF000";
						} else if ($rows['STATUS_ACTIVE'] == 3) {
							$bgcolor = "#FF0000";
						}

					?>
						<tbody>
							<tr bgcolor="<?=$bgcolor; ?>" style="cursor:pointer" onclick="get_php_form_data(<?=$rows['ID']; ?>,'set_update_form_data','requires/operation_resource_entry_controller')">
								<td align="center" width="30"><?=$i; ?></td>
								<td width="70"><?=$machine_category[$rows['PROCESS_ID']]; ?></td>
								<td width="35" align="center"><img src="../../<?= $resource_image_arr[$rows['RESOURCE_ID']];?>" height="20" width="25" alt="." ></td>
								<td><?=$production_resource[$rows['RESOURCE_ID']]; ?></td>
								<td width="180"><?=$rows['RESOURCE_NAME']; ?></td>
								<td width="70"><?=$row_status[$rows['STATUS_ACTIVE']]; ?></td>
							</tr>
						</tbody>
					<?
					$i++;
					}
					?>

				</table>
			</div>
		</fieldset>
	</div>

	<?
}


if ($action == "operation_resource_list") {

	if($data!=-0){$whereCon=" and PROCESS_ID=$data";}
	$save_resource_sql_result = sql_select("select RESOURCE_ID,RESOURCE_NAME,PROCESS_ID,INSERTED_BY,INSERT_DATE,UPDATED_BY,UPDATE_DATE,IS_DELETED,STATUS_ACTIVE from LIB_OPERATION_RESOURCE where is_deleted=0 $whereCon");
	$saved_resource_arr = array();
	foreach ($save_resource_sql_result as $row) {
		$saved_resource_arr[$row['RESOURCE_ID']] = $row['STATUS_ACTIVE'];
		$saved_process_arr[$row['RESOURCE_ID']] = $row['PROCESS_ID'];
	}
 
	?>

	<fieldset>
		<div style="width:250px;">
			<table align="left" width="250" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
				<thead>
					<tr>
						<td colspan="2">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td bgcolor="33CC00" align="center">Active</td>
									<td bgcolor="FFF000" align="center">Inactive</td>
									<td bgcolor="FF0000" align="center">Cancelled</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<th width="35" align="center">SL</th>
						<th><?=$machine_category[$data];?> Resource Name</th>
					</tr>
				</thead>
			</table>
		</div>
		<div style="overflow-y:auto; max-height:370px; width:270px;">
			<table align="left" id="tbl_operation_resource_list" width="250" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all">
				<?
				$i = 0;
				foreach ($production_resource as $resource_id => $resource_name) {
					$i++;
					$bgcolor = ($i % 2 == 0 ? "#E9F3FF" : "#FFFFFF");
					$onclickFunction='onclick="alert(\'Duplicate Select Not Allowed.\');"';
					if ($saved_resource_arr[$resource_id] == 1) {
						$bgcolor = "#33CC00";
					} else if ($saved_resource_arr[$resource_id] == 2) {
						$bgcolor = "#FFF000";
					} else if ($saved_resource_arr[$resource_id] == 3) {
						$bgcolor = "#FF0000";
					}
					else{$onclickFunction='onclick="set_resource('.$resource_id.');"';}

				?>
					<tbody>
						<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer" <? echo $onclickFunction;?> >
							<td align="center" width="35"><? echo $i; ?></td>
							<td title="Resource Id:<?= $resource_id; ?>"><?= $resource_name; ?></td>
						</tr>
					</tbody>
				<?
				}
				?>

			</table>
		</div>
	</fieldset>

	<?
	exit();
}


if ($action == "set_update_form_data") {
	
	$sql = "select ID,RESOURCE_ID,RESOURCE_NAME,PROCESS_ID,CONSUMPTION_FACTOR,NEEDLE_THREAD,BOBBIN_THREAD,INSERTED_BY,INSERT_DATE,UPDATED_BY,UPDATE_DATE,IS_DELETED,STATUS_ACTIVE from LIB_OPERATION_RESOURCE where is_deleted=0  AND id='$data'";
	$data_arr = sql_select($sql);
	foreach ($data_arr as $row) { // txt_consumption_factor
		echo "document.getElementById('update_id').value='" . $row['ID'] . "';\n";
		echo "document.getElementById('txt_resource_id').value='" . $row['RESOURCE_ID'] . "';\n";
		echo "document.getElementById('txt_resource_name').value='" . $row['RESOURCE_NAME'] . "';\n";
		echo "document.getElementById('cbo_process_id').value='" . $row['PROCESS_ID'] . "';\n";
		echo "document.getElementById('txt_consumption_factor').value='" . number_format($row['CONSUMPTION_FACTOR'],2) . "';\n";
		echo "document.getElementById('txt_needle_thread').value='" . $row['NEEDLE_THREAD'] . "';\n";
		echo "document.getElementById('txt_bobbin_thread').value='" . $row['BOBBIN_THREAD'] . "';\n";
		echo "document.getElementById('cbo_status').value='" . $row['STATUS_ACTIVE'] . "';\n";
		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "','fnc_resource_entry',1);\n";
	}
}


if ($action == "insert_default_data") {
	//http://localhost/platform-v3.5/prod_planning/work_study/requires/operation_resource_entry_controller.php?action=insert_default_data
	//execute_query( "delete from LIB_OPERATION_RESOURCE",0);

	//update PPL_GSD_ENTRY_MST set PROCESS_ID=8 where (PROCESS_ID is null or PROCESS_ID=0)

	$id = return_next_id("id", "LIB_OPERATION_RESOURCE", 1);
	$con = connect();
	
	$cbo_process_id = 8;
	$cbo_status = 1;
	$field_array = "id,resource_id,resource_name,process_id,inserted_by,insert_date,status_active,is_deleted";
	foreach($production_resource as $txt_resource_id => $txt_resource_name){
		
		if($data_array!=''){$data_array.=",";}
		$data_array = "(" . $id . "," . $txt_resource_id . ",'" . trim($txt_resource_name) . "'," . $cbo_process_id . "," . $user_id . ",'" . $pc_date_time . "'," . $cbo_status . ",0)";
		$rID = sql_insert("LIB_OPERATION_RESOURCE", $field_array, $data_array, 1);
		$id++;
	}

	// echo "insert into LIB_OPERATION_RESOURCE ($field_array) values $data_array";die;

	execute_query( "update PPL_GSD_ENTRY_MST set PROCESS_ID=8 where (PROCESS_ID is null or PROCESS_ID=0)",0);
	
	oci_commit($con);
	disconnect($con);
	echo 1;
	 

	exit();
	
}

?>