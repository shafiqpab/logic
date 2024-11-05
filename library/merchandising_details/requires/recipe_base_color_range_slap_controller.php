<?php
/*******************************************************************
|	Purpose			:	This controller is for Composition Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Aziz
|	Creation date 	:	19.12.2015
|	Updated by 		: 		
|	Update date		:    
|	QC Performed BY	:		
|	QC Date			:	
|	Comments		:
*********************************************************************/
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select --", $selected, "show_list_view(document.getElementById('cbo_company_name').value+'_'+this.value,'on_change_data','variable_settings_container','requires/excess_cut_slap_controller','');" );     	 
	exit();
}

if ($action=="save_update_delete")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$txt_deleted_id=str_replace("'",'',$txt_deleted_id);
	
	 if ($operation==1)//Insert Here
	{	
		for($i=1; $i<=$row_num; $i++)
		{
			$updateid="updateid_".$i;
			$check_updateID_array[]=str_replace("'",'',$$updateid); 
		}
		 $mst_id=implode(",",$check_updateID_array);
		 
		 if($mst_id) $mst_id_cond=" id not in($mst_id)";
		 else $mst_id_cond="";
		
	}
	 $date_upto=str_replace("'","",$txt_date_upto);
	 
	// echo "10**=select app_date_upto from lib_recipe_base_color_range where app_date_upto='$date_upto'  and is_deleted=0";die;
	 $duplicate_date= is_duplicate_field("id","lib_recipe_base_color_range","app_date_upto='$date_upto' and is_deleted=0 $mst_id_cond");
	 
		if($duplicate_date==1)
		{
			echo "11**Duplicate Applicable date is Not Allow.";
			disconnect($con);
			die;
		}
	 
	if ($operation==0)//Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id = return_next_id( "id", "lib_recipe_base_color_range", 1 );
		$field_array="id,app_date_upto,color_range_id,lower_limit_qty, upper_limit_qty,inserted_by, insert_date, status_active, is_deleted";
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$txtslapid="txtslapid_".$i;
			$txtlowerid="txtlowerid_".$i;
			$txtupperid="txtupperid_".$i;
			$updateid="updateid_".$i;
			$cbocolorrangeid="cbocolorrangeid_".$i;
			//$txt_date_upto="txt_date_upto";
			
			if(str_replace("'","",$$txtupperid)!='')
			{
				if($return_id=="") $return_id=$id; else $return_id.=",".$id;
				if ($add_comma!=0) $data_array .=",";
				//$data_array.="(".$id.",".$cbo_company_name.",".$cbo_buyer_name.",".$$txtslapid.",".$$txtlowerid.",".$$txtupperid.",".$$txtpercentid.",'".$user_id."','".$pc_date_time."',1,0)";
				$field_array="id,app_date_upto,color_range_id,lower_limit_qty, upper_limit_qty,inserted_by, insert_date, status_active, is_deleted";
				$data_array.="(".$id.",".$txt_date_upto.",".$$cbocolorrangeid.",".$$txtlowerid.",".$$txtupperid.",'".$user_id."','".$pc_date_time."',1,0)"; 
				$id=$id+1;
				$add_comma++;
			}
		}
		//echo "10**=INSERT INTO lib_recipe_base_color_range(".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("lib_recipe_base_color_range",$field_array,$data_array,1);
		
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($rID)
			{ //$row_num
				mysql_query("COMMIT");  
				echo "0**".$rID."**".$id."**".$row_num;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$id."**".$row_num;
			}
		}
		
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "0**".$rID."**".$id."**".$row_num."**".$return_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID."**".$id."**".$row_num;
			}
		}
		disconnect($con);
		die;
	}
		
	else if ($operation==1)//Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		//$field_array2="id,comapny_id,buyer_id,slap_sl_id,lower_limit_qty,upper_limit_qty,percentage,inserted_by,insert_date,status_active,is_deleted";
		$field_array2="id, app_date_upto,color_range_id,lower_limit_qty, upper_limit_qty, inserted_by, insert_date, status_active, is_deleted";
		$field_array_up="app_date_upto*color_range_id*lower_limit_qty*upper_limit_qty*updated_by*update_date*status_active*is_deleted";
		//$field_array_delete="updated_by*update_date*status_active*is_deleted";
	    //$data_array_delete="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$id = return_next_id( "id", "lib_recipe_base_color_range", 1 );
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			 
			$txtlowerid="txtlowerid_".$i;
			$txtupperid="txtupperid_".$i;
			$updateid="updateid_".$i;
			//$txt_date_upto="txt_date_upto";
			$cbocolorrangeid="cbocolorrangeid_".$i;
			
			if(str_replace("'","",$$updateid)=="")
			{
				if(str_replace("'","",$$txtupperid)!='')
				{
				if($return_id=="") $return_id=$id; else $return_id.=",".$id;
				if ($add_comma!=0) $data_array2 .=",";
				$data_array2.="(".$id.",".$txt_date_upto.",".$$cbocolorrangeid.",".$$txtlowerid.",".$$txtupperid.",'".$user_id."','".$pc_date_time."',1,0)"; 
				$id=$id+1;
				$add_comma++;
				}
			}			
			else  
			{
				if($return_id=="") $return_id=str_replace("'",'',$$updateid); else $return_id.=",".str_replace("'",'',$$updateid);
				$updateID_array[]=str_replace("'",'',$$updateid); 
				$data_array_up[str_replace("'",'',$$updateid)]=explode("*",("".$txt_date_upto."*".$$cbocolorrangeid."*".$$txtlowerid."*".$$txtupperid."*'".$user_id."'*'".$pc_date_time."'*1*0"));
			}
			$mstUpdate_id_array=array();
			$sql_dtls="Select id from lib_recipe_base_color_range where status_active=1 and is_deleted=0 and  app_date_upto=".$txt_date_upto." ";
			$nameArray=sql_select( $sql_dtls );
			foreach($nameArray as $row)
			{
				$mstUpdate_id_array[]=$row[csf('id')];
			}
		}
		
		if(implode(',',$updateID_array)!="")
		{
			$distance_delete_id=array_diff($mstUpdate_id_array,$updateID_array);
		}
		else
		{
			$distance_delete_id=$mstUpdate_id_array;
		}
			if ($txt_deleted_id != "")
			{
				/*
				|--------------------------------------------------------------------------
				|  
				| Deleted data updating
				|--------------------------------------------------------------------------
				|
				*/
				$field_array_del = "updated_by*update_date*status_active*is_deleted";
				$data_array_del = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
				$rID = sql_multirow_update("lib_recipe_base_color_range", $field_array_del, $data_array_del, "id", $txt_deleted_id, 1);
				if ($flag == 1)
				{
					if ($rID)
						$flag = 1;
					else
						$flag = 0;
				}
			}
			
		/*$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		if(implode(',',$distance_delete_id)!="")
		{
			foreach($distance_delete_id as $id_val)
			{
				$rID=sql_update("lib_recipe_base_color_range",$field_array_del,$data_array_del,"id","".$id_val."",1);

			}
		}*/
		//echo "10**=". bulk_update_sql_statement("lib_recipe_base_color_range","id",$field_array_up,$data_array_up,$updateID_array); die;
		$rID=execute_query(bulk_update_sql_statement("lib_recipe_base_color_range","id",$field_array_up,$data_array_up,$updateID_array),1);
	
		if($rID) $flag=1; else $flag=0;
			//echo $rID.'=='.$flag;die;
			if($flag==1) 
			{
				if($rID) $flag=1; else $flag=0; 
			} 
		
		//echo "10**=INSERT INTO lib_recipe_base_color_range(".$field_array2.") VALUES ".$data_array2;die;
		if($data_array2!="") 
		{
			$rID=sql_insert("lib_recipe_base_color_range",$field_array2,$data_array2,1); 
			if($flag==1) 
			{
				if($rID) $flag=1; else $flag=0; 
			} 
		}
		
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "1**".$rID."**".$id."**".$row_num;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID."**".$id."**".$row_num;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "1**".$rID."**".$id."**".$row_num."**".$return_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID."**".$id."**".$row_num;
			}
		}
		disconnect($con);
		die;
	}		

	else if ($operation==2)//Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		echo '14**Delete Not Allow';disconnect($con);die;
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		//$rID=sql_delete("lib_recipe_base_color_range",$field_array,$data_array,"id","".$update_id."",1);

		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "2**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);   
				echo "2**".$rID;
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}		
}


if ($action=="load_php_data_to_form")
{
	extract($_REQUEST);
	$data=explode("_",$data);
	$app_date=$data[0];
	$mst_id=$data[1];
		// and id in($mst_id)
	$nameArray_sql="select id,color_range_id,app_date_upto,lower_limit_qty, upper_limit_qty from lib_recipe_base_color_range where app_date_upto='$app_date' and status_active=1 order by id asc";
	$nameArray_res=sql_select($nameArray_sql);
	//print_r($nameArray_res);die;
	//echo  "select id, app_date_upto,color_range_id, app_date_upto,lower_limit_qty, upper_limit_qty from lib_recipe_base_color_range where app_date_upto='$app_date' and id in($mst_id) and status_active=1 "; 
	 
	$k=1;
	foreach ($nameArray_res as $row)
	{
		//echo $row[csf('color_range_id')];die;
		?>
         <tr class="general" id="trCut_<? echo $k; ?>">
            <td width="120"> 
			<?
                echo create_drop_down( "cbocolorrangeid_".$k, 120, $color_range,"", 1, "-- Select --",$row[csf('color_range_id')], "","","1,2,9,10" );
             ?>
          </td>
          
           <td> 
                <input  type="text" name="txtlowerid[]" id="txtlowerid_<? echo $k; ?>" class="text_boxes_numeric" style="width:100px;" align="right" value="<? echo $row[csf('lower_limit_qty')]; ?>" />
           </td>
           <td> 
                 <input  type="text" name="txtupperid[]" id="txtupperid_<? echo $k; ?>" class="text_boxes_numeric" style="width:100px;"  align="right" onChange="check_qty_limit(1)" value="<? echo $row[csf('upper_limit_qty')]; ?>" /> 
           </td>
            <td> 
                   <input  type="hidden" name="updateid[]" id="updateid_<? echo $k; ?>" value="<? echo $row[csf('id')]; ?>"/>
                    <input type="button" id="increase_<? echo $k; ?>" style="width:40px" class="formbutton" value="+" onClick="add_break_down_tr(<? echo $k; ?>);" />
                    <input type="button" id="decrease_<? echo $k; ?>" style="width:40px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<? echo $k; ?>);" />
            </td>

	<?
	$k++;
    }
	
}

?>