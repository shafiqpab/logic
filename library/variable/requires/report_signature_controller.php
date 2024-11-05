<?

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$user_id = $_SESSION['logic_erp']["user_id"];

if ($action=="save_update_delete")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$data_array="";
		$field_array="id, company_id, report_id, template_id, user_id, prepared_by, designation, name, activities, sequence_no, inserted_by, insert_date, group_by";
		
		for($i=1;$i<=$tot_row; $i++)
		{
			$txtDesignation='txtDesignation_'.$i;
			$txtName='txtName_'.$i;
			$txtSequenceNo='txtSequenceNo_'.$i;
			$txtActivities='txtActivities_'.$i;
			$txtUser='txtUser_'.$i;
			$txtgroupBy='txtGroupBy_'.$i;

			
			
			if($id=="") $id = return_next_id("id", "variable_settings_signature", 1); else $id=$id+1;
			if($i==1) $add_comma=""; else $add_comma=",";

			$data_array.="$add_comma(".$id.",".$cbo_company_name.",".$cbo_report_name.",".$cbo_template_id.",".$$txtUser.",".$cbo_prepared_by.",".$$txtDesignation.",".$$txtName.",".$$txtActivities.",".$$txtSequenceNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$txtgroupBy.")";       
		}
		
		// echo $data_array;die;
		//echo "10**insert into variable_settings_signature($field_array) values".$data_array;die;
		$rID=sql_insert("variable_settings_signature",$field_array,$data_array,1);

		if($rID)
		{   
			oci_commit($con); 
			echo "0**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$cbo_report_name)."**1"."**".str_replace("'","",$cbo_template_id);
		}
		else
		{ 
			oci_rollback($con);
			echo "5**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$cbo_report_name)."**0"."**".str_replace("'","",$cbo_template_id);
		}
		disconnect($con);
		die;
	}
	else if($operation==1)  // Updated Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$cbo_template_id
		$delete=execute_query("delete from variable_settings_signature where company_id=$cbo_company_name and report_id=$cbo_report_name and template_id=$cbo_template_id ",1);
		
		$data_array="";
		$field_array="id, company_id, report_id,template_id,user_id,prepared_by,designation, name,activities, sequence_no, inserted_by, insert_date, group_by";
		
		for($i=1;$i<=$tot_row; $i++)
		{
			$txtDesignation='txtDesignation_'.$i;
			$txtName='txtName_'.$i;
			$txtSequenceNo='txtSequenceNo_'.$i;
			$txtActivities='txtActivities_'.$i;
			$txtUser='txtUser_'.$i;
			$txtgroupBy='txtGroupBy_'.$i;

			if($id=="") $id=return_next_id( "id", "variable_settings_signature", 1 ); else $id=$id+1;
			if($i==1) $add_comma=""; else $add_comma=",";

			$data_array.="$add_comma(".$id.",".$cbo_company_name.",".$cbo_report_name.",".$cbo_template_id.",".$$txtUser.",".$cbo_prepared_by.",".$$txtDesignation.",".$$txtName.",".$$txtActivities.",".$$txtSequenceNo.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$txtgroupBy.")";     
		}
		
		//echo "10**insert into variable_settings_signature($field_array) values".$data_array;die;

		
		$rID=sql_insert("variable_settings_signature",$field_array,$data_array,1);
       
		if($rID && $delete)
		{   
			oci_commit($con); 
			echo "1**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$cbo_report_name)."**1"."**".str_replace("'","",$cbo_template_id);
		}
		else
		{  
			oci_rollback($con);
			echo "6**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$cbo_report_name)."**1"."**".str_replace("'","",$cbo_template_id);
		}
		disconnect($con);
		die;
	}
	else if($operation==2)  // Insert Here
	{ 
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$delete=execute_query("delete from variable_settings_signature where company_id=$cbo_company_name and report_id=$cbo_report_name",1);

		if($db_type==0)
		{
			if($delete)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$cbo_report_name)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$cbo_report_name)."**1";
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($delete)
			{
				oci_commit($con); 
				echo "2**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$cbo_report_name)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "7**".str_replace("'","",$cbo_company_name)."**".str_replace("'","",$cbo_report_name)."**1";
			}
		}
		disconnect($con);
		die;
	}
}

if( $action == 'signature_details' ) 
 {	
	
	$userArr=return_library_array( "select id,USER_NAME from USER_PASSWD where status_active=1 and is_deleted=0", "id","USER_NAME" );
	
	$data = explode( '_', $data );
	$company_id=$data[0];
	$report_id=$data[1];
	$template_id=$data[2];
	if($report_id!=0)
	{
	?>
	<table class="rpt_table" width="800" cellspacing="0" cellpadding="0" border="1" rules="all" id="tbl_report">
		<thead>
			<th width="170">Designation</th>
			<th width="170">Name</th>
			<th width="170">Activities</th>
			<th width="100">User</th>
			<th width="50">Seq. No</th>
			<th width="50">Group By</th>
			<th></th>
		</thead>
		<tbody>
			<?
			$i=0; $button_status=0;
			$nameArray=sql_select( "SELECT designation, name, template_id, sequence_no, activities, user_id,prepared_by,group_by FROM variable_settings_signature WHERE company_id='$company_id' AND report_id='$report_id' and template_id in($template_id)and status_active=1 and is_deleted=0 order by sequence_no" ); // group_by
			if(count($nameArray)<1)
			{
			?>
				<tr class="general" id="row_1">
					<td>
						<input type="text" name="txtDesignation[]" id="txtDesignation_1" onkeyup="add_auto_complete(1);" class="text_boxes" value="" style="width:150px;"/>
					</td>
					<td>
						<input type="text" name="txtName[]" id="txtName_1" class="text_boxes" value="" style="width:150px;"/>
					</td>
					<td>
						<input type="text" name="txtActivities[]" id="txtActivities_1" class="text_boxes" value="" style="width:150px;"/>
					</td>
					<td>
						<?
						echo create_drop_down( "txtUser_1", 100, $userArr,'', 1, '--All--', 0);
						?>
					</td>
					<td>
						<input type="text" name="txtSequenceNo[]" id="txtSequenceNo_1" class="text_boxes_numeric" value="1" style="width:45px;"/>
					</td>
					<td>
						<input type="text" name="txtgroupBy[]" id="txtgroupBy_1" class="text_boxes_numeric" value="1" style="width:45px;"/>
					</td>
					<td>
						<input type="button" id="increase_1" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( 1 )" />
						<input type="button" id="decrease_1" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(1);" />
					</td> 
				</tr>
			<?
			}
			else
			{
				$button_status=1;
				foreach ($nameArray as $row)
				{
					$i++;
				?>
					<tr class="general" id="row_<? echo $i; ?>">
						<td>
							<input type="text" name="txtDesignation[]" id="txtDesignation_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('designation')]; ?>" onkeyup="add_auto_complete(<? echo $i; ?>);" style="width:150px;"/>
						</td>
						<td>
							<input type="text" name="txtName[]" id="txtName_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('name')]; ?>" style="width:150px;"/>
						</td>
						<td>
							<input type="text" name="txtActivities[]" id="txtActivities_<? echo $i; ?>" class="text_boxes" value="<? echo $row[csf('activities')]; ?>" style="width:150px;"/>
						</td>
						<td>
							<?
							echo create_drop_down( "txtUser_".$i, 100, $userArr,'', 1, '--All--',$row[csf('user_id')]);
							?>
						</td>
						<td>
							<input type="text" name="txtSequenceNo[]" id="txtSequenceNo_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('sequence_no')]; ?>" style="width:45px;"/>
						</td>
						<td>
							<input type="text" name="txtgroupBy[]" id="txtgroupBy_<? echo $i; ?>" class="text_boxes_numeric" value="<? echo $row[csf('group_by')]; ?>" style="width:45px;"/>
						</td>
						<td>
							<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbutton" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
							<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbutton" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
						</td> 
					</tr>
				<?	
				}
			}
			?>
		</tbody>
	</table> 
		<table width="600">
		<tr>
			<td class="button_container" colspan="2"><input type="hidden" value="<? echo $button_status; ?>" id="button_status"></td>
		</tr>
		<tr>
			<td width="100%" align="center"> 
				Show Prepared By: <? echo create_drop_down( "cbo_prepared_by", 60, $yes_no,'', 0, '', ($row[csf('prepared_by')])?$row[csf('prepared_by')]:2, "1","","","","");?> &nbsp; 
				<?
				echo load_submit_buttons( $_SESSION['page_permission'], "fnc_report_signature", 0,0 ,"reset_form('reportsignature_1','','','txt_tot_row,1','$(\'#tbl_report tbody tr:not(:first)\').remove();')",1) ; ?>
			</td>
		</tr>
	</table>
	 <?
	}
 }