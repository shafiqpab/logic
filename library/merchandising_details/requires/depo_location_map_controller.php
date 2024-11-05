<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];
$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="load_drop_down_ultimate_country")
{
	echo create_drop_down( "cbo_ultimate_id", 150, "select id, ultimate_country_code from lib_country_loc_mapping where country_id='$data' and status_active=1 and is_deleted=0 order by ultimate_country_code","id,ultimate_country_code", 1, "-- Select--", $selected, "show_list_view(this.value+'_'+document.getElementById('cbo_country_id').value,'on_change_data','depo_location_mapping','requires/depo_location_map_controller','');" );     	 
	exit();
}

if ($action=="on_change_data")
{
	extract($_REQUEST);
	$explode_data = explode("_",$data);
	$country_id = $explode_data[1];
	$ultimate_id = $explode_data[0];
	
	$nameArray= sql_select("select id, depo_code from lib_country_depo_mapping where country_id='$country_id' and ultimate_country_id='$ultimate_id' and status_active=1 and is_deleted=0 order by id");
	?>
	<fieldset>
        <legend>Country Depo Entry</legend>
        <div onLoad="set_hotkey();" style="width:400px;height:auto;" align="center">
        <table width="400px" cellpadding="0" cellspacing="0" border="1" class="rpt_table" align="center" id="depo_tbl" rules="all" >
            <thead>
                <th width="30">SL No</th>
                <th>Country Depo Name</th>
            </thead>
            <tbody>
				<?
				//echo count($nameArray);
                    if(count($nameArray)>0)
                    {
						$is_update=1;
                        $i=1;
                        foreach($nameArray as $row)
                        {
                            ?>
                            <tr id="trUltimate_<? echo $i; ?>">
                                <td width="40"><? echo $i; ?></td>
                                <td><input type="text" name="txtDepoDtls_<? echo $i;?>" id="txtDepoDtls_<? echo $i;?>" class="text_boxes" style="width:300px;" value=" <? echo $row[csf('depo_code')];?>" onBlur="append_depo_mapping_row(this.value,<? echo $i;?>);" />
                                <input type="hidden" name="updateid_<? echo $i;?>" id="updateid_<? echo $i;?>" style="width:20px;" class="text_boxes"  value="<? echo $row[csf('id')];?> " />
                                </td>
                            </tr>
                            <? 
                            $i++;
                        }
						?>
						<tr id="trUltimate_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td><input  type="text"name="txtDepoDtls_<? echo $i;?>" id="txtDepoDtls_<? echo $i;?>" class="text_boxes" style="width:300px;" value="" onBlur="append_depo_mapping_row(this.value,<? echo $i;?>);" />
                            <input type="hidden" name="updateid_<? echo $i;?>" id="updateid_<? echo $i;?>" style="width:20px;" class="text_boxes"  value="" />
                            </td>
                        </tr>
                        <?
                    }
                    else
                    {
						$is_update=0;
                        ?>
                        <tr id="trUltimate_1">
                            <td width="40">1</td>
                            <td> 
                                <input type="text"name="txtDepoDtls_1" id="txtDepoDtls_1" style="width:300px;" class="text_boxes" onBlur="append_depo_mapping_row(this.value,1);" />
                                <input type="hidden" name="updateid_1" id="updateid_1" style="width:20px;" class="text_boxes" />
                            </td>
                        </tr>
                        <?	
                    }
                ?>
            </tbody>
            <tfoot>
            	<tr>
                	<td colspan="2" height="40" valign="bottom" align="center" class="button_container">
					<? 
                    	echo load_submit_buttons( $permission, "fnc_depo_mapping", $is_update,0 ,"reset_form('ultimatecountry_1','depo_location_mapping','')",1);
                    ?>
                    </td>	
                </tr>
            </tfoot>
        </table>
        </div>
        <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
    <?
	exit();
}

if ($action=="save_update_delete")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)//Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id = return_next_id( "id", "lib_country_depo_mapping", 1 );
		$field_array="id, country_id, ultimate_country_id, depo_code, inserted_by, insert_date";
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$updateid="updateid_".$i;
			$txtDepoDtls="txtDepoDtls_".$i;
			
			if(str_replace("'","",$$txtDepoDtls)!='')
			{
				if ($add_comma!=0) $data_array .=",";
				$data_array.="(".$id.",".$cbo_country_id.",".$cbo_ultimate_id.",".$$txtDepoDtls.",'".$user_id."','".$pc_date_time."')"; 
				$id=$id+1;
				$add_comma++;
			}
		}
		//echo $return_id;
		//print_r($data_array);die;
		//echo "INSERT INTO lib_excess_cut_slab(".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("lib_country_depo_mapping",$field_array,$data_array,1);
		
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($rID)
			{ //$row_num
				mysql_query("COMMIT");  
				echo "0**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		elseif($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);   
				echo "0**".$rID;
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
	else if ($operation==1)//Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id = return_next_id( "id", "lib_country_depo_mapping", 1 );
		$field_array="id, country_id, ultimate_country_id, depo_code, inserted_by, insert_date";
		$field_array_up="depo_code*updated_by*update_date";
		
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$updateid="updateid_".$i;
			$txtDepoDtls="txtDepoDtls_".$i;
			if(str_replace("'","",$$updateid)=="")
			{
				if(str_replace("'","",$$txtDepoDtls)!='')
				{
					if ($add_comma!=0) $data_array .=",";
					$data_array.="(".$id.",".$cbo_country_id.",".$cbo_ultimate_id.",".$$txtDepoDtls.",'".$user_id."','".$pc_date_time."')"; 
					$id=$id+1;
					$add_comma++;
				}
			}
			else
			{
				if(str_replace("'","",$$txtDepoDtls)!='')
				{
					$updateID_array[]=str_replace("'",'',$$updateid); 
					$data_array_up[str_replace("'",'',$$updateid)]=explode("*",("".$$txtDepoDtls."*'".$user_id."'*'".$pc_date_time."'"));
				}
			}
			
			$mstUpdate_id_array=array();
			$sql_dtls="Select id from lib_country_depo_mapping where country_id=$cbo_country_id and ultimate_country_id=$cbo_ultimate_id and status_active=1 and is_deleted=0";
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
		//print_r($distance_delete_id);
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		if(implode(',',$distance_delete_id)!="")
		{
			foreach($distance_delete_id as $id_val)
			{
				$rID=sql_update("lib_country_depo_mapping",$field_array_del,$data_array_del,"id","".$id_val."",1);
				if($rID) $flag=1; else $flag=0;
			}
		}
		
		//print_r($field_array_up);
		
		//if($rID) $flag=1; else $flag=0;
		//$rID=sql_delete("lib_excess_cut_slab",$field_array_up,$data_array_up,"id","".$update_id."",1);
		$rID=execute_query(bulk_update_sql_statement("lib_country_depo_mapping","id",$field_array_up,$data_array_up,$updateID_array),1);
		if($rID) $flag=1; else $flag=0;
		if($flag==1) 
		{
			if($rID) $flag=1; else $flag=0; 
		} 
		
		if($data_array!="") 
		{
			$rID=sql_insert("lib_country_depo_mapping",$field_array,$data_array,1); 
			if($flag==1) 
			{
				if($rID) $flag=1; else $flag=0; 
			} 
		}
		
		//----------------------------------------------------------------------------------
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".$rID;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "1**".$rID;
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
		$rID=sql_delete("lib_excess_cut_slab",$field_array,$data_array,"id","".$update_id."",1);

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


?>