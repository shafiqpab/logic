<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../../includes/common.php');
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="on_change_data")
{
	$explode_data = explode("_",$data);
	$type = $explode_data[0];
	//echo $type;
	$company_id = $explode_data[1];

	if ($type==1)   //trims Production Update area
	{
		$nameArray= sql_select("select id, company_name, variable_list, production_update_area from variable_setting_trim_prod where company_name='$company_id' and variable_list=1 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Production Update Areas</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update"> Production Update Area for Trims</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_trims_production_update", 170, $trims_production_update_areas,'', 1, '--Select--', $nameArray[0][csf('production_update_area')], "" );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?
								echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
	<?
	}
	else if ($type==2)   //trims Production Update area
	{
		$nameArray= sql_select("select id, company_name, variable_list, process_production_qty_control from variable_setting_trim_prod where company_name='$company_id' and variable_list=2 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Last Process Production Qty Control</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Last Process Production Qty Control</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_process_production_qty_control_status", 170,$yes_no,'', 1, '--Select--', $nameArray[0][csf('process_production_qty_control')], "" );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?
								echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
	<?
	}
	else if ($type==3)   //Last Process Trims.Del.Qty Control
	{
		$nameArray= sql_select("select id, company_name, variable_list, process_production_qty_control from variable_setting_trim_prod where company_name='$company_id' and variable_list=3 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Last Process Production Qty Control</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Last Process Trims Del. Qty. Control</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_process_production_qty_control_status", 170,$yes_no,'', 1, '--Select--', $nameArray[0][csf('process_production_qty_control')], "" );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?
								echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
	<?
	}
	else if ($type==4)   //Delivery Qty. Auto Fill Up
	{
		$nameArray= sql_select("select id, company_name, variable_list, process_production_qty_control from variable_setting_trim_prod where company_name='$company_id' and variable_list=4 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Delivery Qty. Auto Fill Up</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Delivery Qty. Auto Fill Up</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_process_production_qty_control_status", 170,$yes_no,'', 1, '--Select--', $nameArray[0][csf('process_production_qty_control')], "" );
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                            <input  type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <?
								echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1);
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
	<?
	}
	exit();
}//end change on data condition

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
			//echo "10**".$cbo_variable_list_production;die;
			
		if (is_duplicate_field( "company_name", "variable_setting_trim_prod", "company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production" ) == 1)
			{
				echo 11; disconnect($con); die;
			}
			else
			{
				$id=return_next_id( "id", "variable_setting_trim_prod", 1 ) ;
				if(str_replace("'","",$cbo_variable_list_production)==1)
				{
					$field_array="id, company_name, variable_list, production_update_area, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_trims_production_update.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==2 || str_replace("'","",$cbo_variable_list_production)==3 || str_replace("'","",$cbo_variable_list_production)==4)
				{
					$field_array="id, company_name, variable_list, process_production_qty_control, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_process_production_qty_control_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				$rID=sql_insert("variable_setting_trim_prod",$field_array,$data_array,1);
			}
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo 0;
			}
			else{
				mysql_query("ROLLBACK");
				echo 10;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);
				echo "0**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
 		if(str_replace("'","",$cbo_variable_list_production)==1)
		{
			$field_array="company_name*variable_list*production_update_area*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_trims_production_update."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_setting_trim_prod",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==2 || str_replace("'","",$cbo_variable_list_production)==3 || str_replace("'","",$cbo_variable_list_production)==4)
		{
			$field_array="company_name*variable_list*process_production_qty_control*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_process_production_qty_control_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_setting_trim_prod",$field_array,$data_array,"id","".$update_id."",1);
		}

		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo 1;
			}
			else{
				mysql_query("ROLLBACK");
				echo 10;
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			 if($rID )
			    {
					oci_commit($con);
					echo "1**".$rID;
				}
			else{
					oci_rollback($con);
					echo "10**".$rID;
				}
		}
		disconnect($con);
		die;
	}
}
?>