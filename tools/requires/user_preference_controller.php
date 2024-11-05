<?php

header('Content-type:text/html; charset=utf-8');
session_start();

if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
include('../../includes/field_list_array.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
extract($_REQUEST);
 
if($action=="load_drop_down_item")
{
	$data=explode("**",$data);
	$form_id=$data[0];
	$company_id=$data[1];
	
	/*print_r($data);
	echo $company_id;
	echo $form_id;*/
	$preference_array=array();
	//print_r($preference_array);
	$is_update=0;
	$dataArray=sql_select("select id, field_id, default_list, is_editable from admin_user_preference where company_id=$company_id and form_id=$form_id and status_active=1 and is_deleted=0");
	if(count($dataArray)>0)
	{
		//print_r($preference_array);
		$is_update=1;
		foreach($dataArray as $row)
		{
			//print_r($preference_array);
			$preference_array[$row[csf('field_id')]]['list']=$row[csf('default_list')];
			$preference_array[$row[csf('field_id')]]['id']=$row[csf('id')];
			$preference_array[$row[csf('field_id')]]['is_editable']=$row[csf('is_editable')];

			
			
			//print_r($row);
			//print_r($preference_array);
		}
	}
	
	$field_arr=get_fieldlevel_arr($form_id);
	//print_r($field_arr);
	//echo create_drop_down("cbo_form_id",150,$field_arr,"","","","","","","","","","","","","cbo_form_id");
	$i=0;
	foreach($field_arr as $id=>$list)
	{
		$i++;
	?>
		<tr align="center">
			<td width="150"><input type="text" id="fieldName_<? echo $i; ?>" name="fieldName[]" class="text_boxes" value="<? echo $list; ?>" disabled="disabled"></td>
			<td width="150"><input type="text" id="defaultList_<? echo $i; ?>" name="defaultList[]" value="<? echo $preference_array[$list]['list']; ?>" class="text_boxes"> </td>
			<td width="150">
            	<input type="hidden" name="detailsId" id="detailsId_<? echo $i; ?>" value="<? echo $preference_array[$list]['id']; ?>" />
            	<? 
				if($i==1)
				{
				?>
            		<input type="hidden" name="is_update" id="is_update" value="<? echo $is_update; ?>" />
                <?
				}
				//echo create_drop_down("cbo_permission_id_".$i,150,$yes_no,"",1,"-- Select --",0,"","","","","","","","","cbo_permission_id[]"); ?>
                <?php 
					//function create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name )
					echo create_drop_down("cbo_permission_id_".$i,150,$yes_no,"",1,"-- Select --",$preference_array[$list]['is_editable'],"","","","","","","","","cbo_permission_id[]"); 
				?>
			</td>
		</tr>
	<?
	}
	exit();
}

if($action=='save_update_delete')
{
	$process=array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	//insert
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//return_next_id( $field_name, $table_name, $max_row=1, $new_conn );
		$id = return_next_id( "id", "admin_user_preference", 1 ) ;
		$field_array = "id, company_id, form_id, field_id, default_list, is_editable, inserted_by, inserted_date";

		for($i=1; $i<=$total_row; $i++)
		{
			$fieldName="fieldName_".$i;
			$defaultList="defaultList_".$i;
			$cbo_permission_id="cbo_permission_id_".$i;
			
			if($data_array!="") $data_array.=","; 	
			$data_array.="(".$id.",".$cbo_company_id.",".$cbo_form_id.",".$$fieldName.",".$$defaultList.",".$$cbo_permission_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			$id=$id+1;
		}
		$rID2 = sql_insert("admin_user_preference",$field_array,$data_array,1);
		//echo $rID; die;
		
		if($db_type==0)
		{
			if($rID2)
			{
				mysql_query("COMMIT");  
				echo "0**".$cbo_company_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$cbo_company_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID2)
			{
				oci_commit($con);   
				echo "0**".$cbo_company_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$cbo_company_id;
			}
		}
		disconnect($con);
		die;		
	}
	//update
	else if($operation==1)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//function return_next_id( $field_name, $table_name, $max_row=1, $new_conn );
		$id = return_next_id( "id", "admin_user_preference", 1 ) ;
		$field_array = "id, company_id, form_id, field_id, default_list, is_editable, inserted_by, inserted_date";
		$field_array_update = "field_id*default_list*is_editable*updated_by*update_date";

		for($i=1; $i<=$total_row; $i++)
		{
			$fieldName="fieldName_".$i;
			$defaultList="defaultList_".$i;
			$cbo_permission_id="cbo_permission_id_".$i;
			$detailsId="detailsId_".$i;
			
			if(str_replace("'",'',$$detailsId)!="")
			{
				$id_arr[]=str_replace("'",'',$$detailsId);
				$data_array_update[str_replace("'",'',$$detailsId)] = explode("*",("".$$fieldName."*".$$defaultList."*".$$cbo_permission_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			 }
			 else
			 {
				if($data_array!="") $data_array.=","; 	
				$data_array.="(".$id.",".$cbo_company_id.",".$cbo_form_id.",".$$fieldName.",".$$defaultList.",".$$cbo_permission_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
				$id=$id+1;
			 }
		}
		
		//$rID=execute_query("delete from admin_user_preference where company_id=$cbo_company_id and form_id=$cbo_form_id",0);
		$rID=true; $rID2=true;
		if(count($data_array_update)>0)
		{
		//function bulk_update_sql_statement( $table, $id_column, $update_column, $data_values, $id_count )
			$rID=execute_query(bulk_update_sql_statement( "admin_user_preference", "id", $field_array_update, $data_array_update, $id_arr ),1);
		}
		//function sql_update($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues, $commit)
		if($data_array!="")
		{
			$rID2=sql_insert("admin_user_preference",$field_array,$data_array,1);
		}
		
		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");  
				echo "1**".$cbo_company_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$cbo_company_id;
			}
		}
		else if($db_type==2 || $db_type==1 ) //$db_type=(0=Mysql,1=mssql,2=oracle);
		{
			if($rID && $rID2)
			{
				oci_commit($con);   
				echo "1**".$cbo_company_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$cbo_company_id;
			}
		}
	   disconnect($con);
	   die;
	}
	//delete
	else if($operation==2)
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		//$rID=execute_query("delete from admin_user_preference where company_id=$cbo_company_id and form_id=$cbo_form_id",0);	
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
 
		$rID=sql_delete("admin_user_preference",$field_array,$data_array,"company_id*form_id",$cbo_company_id."*".$cbo_form_id,1);	
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "2**".$cbo_company_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$cbo_company_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "2**".$cbo_company_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$cbo_company_id;
			}
		}
		disconnect($con);
		die;	
	}
	exit();
}

?>