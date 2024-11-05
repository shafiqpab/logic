<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../../includes/common.php');
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if ($action=="load_drop_down_process")
{
 	
	
	$data=explode("_",$data);
	if($data[0]==1) $process_type='1';
	else if($data[0]==2) $process_type='2,3';
	else if($data[0]==3) $process_type='4,5,6,7,8,9,10,11,12,13';
	else if($data[0]==0) $process_type='0';
	 
	 
	
	echo create_drop_down( "cbo_yd_process", 170,$yd_variable_subb_process_arr,'', 1, '--Select--','', "","",$process_type );	
	exit();
}

if ($action=="on_change_data")
{
	$explode_data = explode("_",$data);
	$type = $explode_data[0];
	//echo $type;
	$company_id = $explode_data[1];

	 
	if ($type==1)   //printing Production Update area
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail from variable_setting_yarn_dyeing where company_name='$company_id' and variable_list=1 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Yarn Dyeing Material Auto Receive</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Yarn Dyeing Material Auto Receive</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_item_show_status", 170,$yes_no,'', 1, '--Select--', $nameArray[0][csf('item_show_in_detail')], "" );
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
	if ($type==2)   //Last Process Delivery Entry
	{
		$nameArray= sql_select("select id, company_name, variable_list, service_process_id,yarn_dyeing_process from variable_setting_yarn_dyeing where company_name='$company_id' and variable_list=2 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?> 
        <fieldset>
        <legend>Last Process Delivery Entry</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Last Process For  Delivery Entry</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_yd_last_process", 170,$yd_variable_process_arr,'', 1, '--Select--', $nameArray[0][csf('service_process_id')], "fnc_load_change_array(this.value);" );   
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="130" align="center" id="cutting_update">Process</td>
                        <td width="190" id="yarn_process">
							<?
								echo create_drop_down( "cbo_yd_process", 170,$yd_variable_subb_process_arr,'', 1, '--Select--', $nameArray[0][csf('yarn_dyeing_process')], "" );
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
			
			if (is_duplicate_field( "company_name", "variable_setting_yarn_dyeing", "company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production" ) == 1)
			{
				echo 11; disconnect($con); die;
			}
			else
			{
				$id=return_next_id( "id", "variable_setting_yarn_dyeing", 1 ) ;
				
 				if(str_replace("'","",$cbo_variable_list_production)==1)
				{
					$field_array="id, company_name, variable_list, item_show_in_detail, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_show_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==2)
				{
					$field_array="id, company_name, variable_list,service_process_id,yarn_dyeing_process, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_yd_last_process.",".$cbo_yd_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
			
				$rID=sql_insert("variable_setting_yarn_dyeing",$field_array,$data_array,1);
			}
		//echo $rID;die;
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
			$field_array="company_name*variable_list*item_show_in_detail*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_show_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_setting_yarn_dyeing",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==2)
		{
			$field_array="company_name*variable_list*service_process_id*yarn_dyeing_process*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_yd_last_process."*".$cbo_yd_process."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_setting_yarn_dyeing",$field_array,$data_array,"id","".$update_id."",1);
		} 
 		//echo $rID;die;
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