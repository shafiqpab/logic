<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

	include('../../../includes/common.php');
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

if ($action=="on_change_data")
{
	$explode_data = explode("_",$data);
	$type = $explode_data[0];
	$company_id = $explode_data[1];

	if ($type==1 || $type==2)
	{ 
		$appTitle="For yes- If any job with less the given std value that will go first (Ready to approve -Yes updated) in confirmation page. After pass from the page will appear in approval page.";
		?>
		<fieldset>
			<legend title="<?=$appTitle; ?>"><?= $approval_module[$type];?></legend>
			<div style="width:400px;" align="left">
				<table width="100%" cellspacing="2" cellpadding="0" class="rpt_table" rules="all">
					<thead>
						<th width="70" align="center" title="<?=$appTitle; ?>">Is Required</th>
						<th width="150" align="center" colspan="2" title="<?=$appTitle; ?>">CM Std.</th>
						<th align="center" colspan="2" title="<?=$appTitle; ?>">Margin/Pcs Std.</th>
					</thead>
					<?
                    $i=0; $is_update=0;
                    $sub_nameArray= sql_select("select id, company_name, variable_list, is_required, cm_std_per, cm_std_value, margin_std_per, margin_std_value from variable_approval_settings where company_name='$company_id' and variable_list=$type order by id");
					
                    foreach($sub_nameArray as $rows)
                    {
                        $i++; $is_update=1;
                        ?>
                        <tr style="text-decoration:none">
                            <td width="70"><?=create_drop_down( "cboisrequired_$i", 70, $yes_no,"", 1, "-- Select--", $rows[csf("is_required")], "","","" ); ?></td>
                            <td width="70"><input type="text" name="txtcmper_<?=$i; ?>" id="txtcmper_<?=$i; ?>" value="<?=$rows[csf("cm_std_per")]; ?>" class="text_boxes_numeric" style="width:60px;" placeholder="In %"/></td>
                            <td width="70"><input type="text" name="txtcmval_<?=$i; ?>" id="txtcmval_<?=$i; ?>" value="<?=$rows[csf("cm_std_value")]; ?>" class="text_boxes_numeric" style="width:60px;" placeholder="In Value"/></td>
                            <td width="70"><input type="text" name="txtmarginper_<?=$i; ?>" id="txtmarginper_<?=$i; ?>" value="<?=$rows[csf("margin_std_per")]; ?>" class="text_boxes_numeric" style="width:60px;" placeholder="In %"/></td>
                            <td>
                                <input type="text" name="txtmarginval_<?=$i; ?>" id="txtmarginval_<?=$i; ?>" value="<?=$rows[csf("margin_std_value")]; ?>" class="text_boxes_numeric" style="width:60px;" placeholder="In Value"/>
                                <input type="hidden"name="updateid_<?=$i; ?>" id="updateid_<?=$i; ?>" value="<?=$rows[csf("id")]; ?>">
                            </td>
                        </tr>
                <? } 
                    if($i==0)
                    {	
                        $i++;	
                        ?>						
                           <tr style="text-decoration:none">
                                <td width="70"><?=create_drop_down( "cboisrequired_$i", 70, $yes_no,"", 1, "-- Select--", 0, "","","" ); ?></td>
                                <td width="70"><input type="text" name="txtcmper_<?=$i; ?>" id="txtcmper_<?=$i; ?>" value="<?=$rows[csf("cm_std_per")]; ?>" class="text_boxes_numeric" style="width:60px;" placeholder="In %"/></td>
                                <td width="70"><input type="text" name="txtcmval_<?=$i; ?>" id="txtcmval_<?=$i; ?>" value="<?=$rows[csf("cm_std_value")]; ?>" class="text_boxes_numeric" style="width:60px;" placeholder="In Value"/></td>
                                <td width="70"><input type="text" name="txtmarginper_<?=$i; ?>" id="txtmarginper_<?=$i; ?>" value="<?=$rows[csf("margin_std_per")]; ?>" class="text_boxes_numeric" style="width:60px;" placeholder="In %"/></td>
                                <td>
                                    <input type="text" name="txtmarginval_<?=$i; ?>" id="txtmarginval_<?=$i; ?>" value="<?=$rows[csf("margin_std_value")]; ?>" class="text_boxes_numeric" style="width:60px;" placeholder="In Value"/>
                                    <input type="hidden"name="updateid_<?=$i; ?>" id="updateid_<?=$i; ?>" value="<?=$rows[csf("id")]; ?>">
                                </td>
                            </tr>
                    <? } ?>
                </table>
            </div>	
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td colspan="5" valign="bottom" align="center" class="button_container">
                            <? 
                            echo load_submit_buttons( $permission, "fnc_approval_variable_settings", $is_update,0 ,"reset_form('approvalvariablesettings_1','variable_settings_container','')",1);
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
	$cbo_variable_list = str_replace("'","",$cbo_variable_list);
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		$rID=1;
		if($cbo_variable_list == 1 || $cbo_variable_list == 2)
		{
			$field_array="id, company_name, variable_list, is_required, cm_std_per, cm_std_value, margin_std_per, margin_std_value, inserted_by, insert_date, status_active, is_deleted";
			
			$variable_id = return_next_id( "id", "variable_approval_settings", 1);
			
			$data_array="(".$variable_id.",".$cbo_company_name_wo.",".$cbo_variable_list.",".$cboisrequired_1.",".$txtcmper_1.",".$txtcmval_1.",".$txtmarginper_1.",".$txtmarginval_1.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$variable_id++;		
			//echo "10**insert into variable_approval_settings ($field_array) values $data_array"; die;				
			$rID=sql_insert("variable_approval_settings",$field_array,$data_array,1); 
			
		}
		
		if($rID )
		{
			oci_commit($con);   
			echo "0**".$rID."**".$id;
		}
		else{
			oci_rollback($con);
			echo "10**".$rID;
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
		if($cbo_variable_list == 1 || $cbo_variable_list == 2)
		{	
			$field_array="is_required*cm_std_per*cm_std_value*margin_std_per*margin_std_value*updated_by*update_date"; 
			$data_array="".$cboisrequired_1."*".$txtcmper_1."*".$txtcmval_1."*".$txtmarginper_1."*".$txtmarginval_1."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			$rID=sql_update("variable_approval_settings",$field_array,$data_array,"id","".$updateid_1."",1);
		}
		
		
		if($rID )
		{
			oci_commit($con);   
			echo "1**".$rID."**".str_replace("'","",$update_id);
		}
		else{
			oci_rollback($con);
			echo "10**".$rID;
		}
		
		disconnect($con);
		die;
	}	
}
?>