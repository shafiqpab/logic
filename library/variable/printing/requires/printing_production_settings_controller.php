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

	if ($type==1)   //printing Production Update area
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail from variable_setting_printing_prod where company_name='$company_id' and variable_list=1 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Printing Recipe Show Items Without Stock</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Item Show In Detail</td>
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
	if ($type==2)   //printing bill issue area
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail,quantity_control,validation_qty_control from variable_setting_printing_prod where company_name='$company_id' and variable_list=2 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Printing Recipe Show Items Without Stock</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Printing Bill. Qty. Control </td>
                        <td width="190">
							<?
							      $yesno = array(1 => "Delivery qty", 2 => "Order Qty"); //2= Deleted,3= Locked
								echo create_drop_down( "cbo_item_show_status", 170,$yesno,'', 1, '--Select--', $nameArray[0][csf('quantity_control')], "" );
                            ?>
                        </td>
                        <td width="130" align="center" id="cutting_update">Order And Color Size Qty. Control </td>
                        <td width="190">
							<?
							      $qty_yes_no = array(1 => "Color Size Qty", 2 => "Order Qty"); //2= Deleted,3= Locked
								echo create_drop_down( "cbo_qty_item_show_status", 170,$qty_yes_no,'', 1, '--Select--', $nameArray[0][csf('validation_qty_control')], "" );
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
	if ($type==3)   //printing bill issue area
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail,quantity_control from variable_setting_printing_prod where company_name='$company_id' and variable_list=3 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Printing Recipe Show Items Without Stock</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Printing Delivery Entry Control </td>
                        <td width="190">
							<?
							
							
							      $DeliveryControl = array(1 => "Order Entry", 2 => "Printing Production Entry" ,3 => "Printing QC Entry"); //2= Deleted,3= Locked
								echo create_drop_down( "cbo_item_show_status", 170,$DeliveryControl,'', 1, '--Select--', $nameArray[0][csf('quantity_control')], "" );
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
	if ($type==4)   //Embroidery  Delivery area
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail,quantity_control from variable_setting_printing_prod where company_name='$company_id' and variable_list=4 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Embroidery  Delivery Entry Control</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Embroidery  Delivery Entry Control </td>
                        <td width="190">
							<?
							
							
							      $embroideryDeliveryControl = array(1 => "Embroidery Order Entry", 2 => "Embroidery Production Entry" ,3 => "Embroidery QC Entry"); //2= Deleted,3= Locked
								echo create_drop_down( "cbo_item_show_status", 170,$embroideryDeliveryControl,'', 1, '--Select--', $nameArray[0][csf('quantity_control')], "" );
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
	if ($type==5) // Printing Barcode Maintain
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail,quantity_control,validation_qty_control from variable_setting_printing_prod where company_name='$company_id' and variable_list=5 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		$barcode_maintain=$nameArray[0][csf('quantity_control')];
		
		if($barcode_maintain==2 || $barcode_maintain==0) $disable_con="1";else $disable_con="";
		$integrated_status = array(1 => "Independent", 2 => "Integrated");
		?>
		<fieldset>
            <legend>Printing Barcode Maintain</legend>
            <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="100" align="left"  id="season_mandatory_td"><strong>Barcode Level</strong></td>
                        <td width="100"><? echo create_drop_down( "cbo_item_show_status", 100, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('quantity_control')], "fnc_check_field(this.value)",'','' ); ?> </td>
                        <td width="190"><? echo create_drop_down( "cbo_qty_item_show_status", 170, $integrated_status,'', 1, '---- Select ----', $nameArray[0][csf('validation_qty_control')], "",$disable_con,'' ); ?> </td>
                    </tr>
                </table>
            </div>
            <div style="width:500px; min-height:20px; max-height:250px;" id="variable_list_cont2" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    	<td align="center" width="320">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" valign="bottom" align="center" class="button_container">
                            <input type="hidden"name="update_id" id="update_id" value="<? echo $nameArray[0][csf('id')]; ?>">
                            <? echo load_submit_buttons( $permission, "fnc_production_variable_settings", $is_update,0 ,"reset_form('productionVariableSettings','','')",1); ?>
                        </td>
                    </tr>
                </table>
            </div>
		</fieldset>
		<?
	}
	if ($type==6)   //Embroidery Bill Quantity Control
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail,quantity_control,validation_qty_control from variable_setting_printing_prod where company_name='$company_id' and variable_list=6 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Embroidery Bill Quantity Control</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Embroidery Bill. Qty. Control </td>
                        <td width="190">
							<?
							      $yesno = array(1 => "Delivery qty", 2 => "Order Qty"); //2= Deleted,3= Locked
								echo create_drop_down( "cbo_item_show_status", 170,$yesno,'', 1, '--Select--', $nameArray[0][csf('quantity_control')], "" );
                            ?>
                        </td>
                        <td width="130" align="center" id="cutting_update">Order And Color Size Qty. Control </td>
                        <td width="190">
							<?
							      $qty_yes_no = array(1 => "Color Size Qty", 2 => "Order Qty"); //2= Deleted,3= Locked
								echo create_drop_down( "cbo_qty_item_show_status", 170,$qty_yes_no,'', 1, '--Select--', $nameArray[0][csf('validation_qty_control')], "" );
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
	if ($type==7)   //printing Production Update area
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail from variable_setting_printing_prod where company_name='$company_id' and variable_list=7 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend>Printing Material Auto Receive</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Printing Material Auto Receive</td>
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
    if ($type==8)   //Embroidery Bill Quantity Control
    {
        $nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail,quantity_control,validation_qty_control from variable_setting_printing_prod where company_name='$company_id' and variable_list=8 order by id");
        if(count($nameArray)>0)$is_update=1;else $is_update=0;
        ?>
        <fieldset>
        <legend>Printing Bill Quantity Control</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update">Printing Bill Quantity Control </td>
                        <td width="190">
                            <?
                                  $yesno = array(1 => "Delivery qty", 2 => "Order Qty"); //2= Deleted,3= Locked
                                echo create_drop_down( "cbo_item_show_status", 170,$yesno,'', 1, '--Select--', $nameArray[0][csf('quantity_control')], "" );
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
    if ($type==9)   //printing Production Update area
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail from variable_setting_printing_prod where company_name='$company_id' and variable_list=9 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend> Embroidery Material Auto Receive</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update"> Embroidery Material Auto Receive</td>
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
	if ($type==10)   //Bundle Process Maintain fro Production
	{
		$nameArray= sql_select("select id, company_name, variable_list, item_show_in_detail from variable_setting_printing_prod where company_name='$company_id' and variable_list=10 order by id");
		if(count($nameArray)>0)$is_update=1;else $is_update=0;
		?>
        <fieldset>
        <legend> Printing Bundle Process Maintain For Production</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                        <td width="130" align="center" id="cutting_update"> Printing Bundle Process Maintain for Production</td>
                        <td width="190">
							<?
								echo create_drop_down( "cbo_item_show_status", 170,$yes_no,'', 1, '--Select--', $nameArray[0][csf('item_show_in_detail')], "", "", "", "", "" , "1" );
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
			
		if (is_duplicate_field( "company_name", "variable_setting_printing_prod", "company_name=$cbo_company_name_production and variable_list=$cbo_variable_list_production" ) == 1)
			{
				echo 11; disconnect($con); die;
			}
			else
			{
				$id=return_next_id( "id", "variable_setting_printing_prod", 1 ) ;
				
				
				if(str_replace("'","",$cbo_variable_list_production)==1 || str_replace("'","",$cbo_variable_list_production)==7 || str_replace("'","",$cbo_variable_list_production)==9 || str_replace("'","",$cbo_variable_list_production)==10)
				{
					$field_array="id, company_name, variable_list, item_show_in_detail, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_show_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==2 || str_replace("'","",$cbo_variable_list_production)==5 || str_replace("'","",$cbo_variable_list_production)==6)
				{
					$field_array="id, company_name, variable_list, quantity_control,validation_qty_control, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_show_status.",".$cbo_qty_item_show_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==3)
				{
					$field_array="id, company_name, variable_list, quantity_control, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_show_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
				else if(str_replace("'","",$cbo_variable_list_production)==4)
				{
					$field_array="id, company_name, variable_list, quantity_control, inserted_by, insert_date, status_active";
					$data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_show_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				}
                else if(str_replace("'","",$cbo_variable_list_production)==8)
                {
                    $field_array="id, company_name, variable_list, quantity_control, inserted_by, insert_date, status_active";
                    $data_array="(".$id.",".$cbo_company_name_production.",".$cbo_variable_list_production.",".$cbo_item_show_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
                }
				$rID=sql_insert("variable_setting_printing_prod",$field_array,$data_array,1);
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
 		if(str_replace("'","",$cbo_variable_list_production)==1 || str_replace("'","",$cbo_variable_list_production)==7 || str_replace("'","",$cbo_variable_list_production)==9 || str_replace("'","",$cbo_variable_list_production)==10 )
		{
			$field_array="company_name*variable_list*item_show_in_detail*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_show_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_setting_printing_prod",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==2 || str_replace("'","",$cbo_variable_list_production)==5 || str_replace("'","",$cbo_variable_list_production)==6)
		{
			$field_array="company_name*variable_list*quantity_control*validation_qty_control*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_show_status."*".$cbo_qty_item_show_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_setting_printing_prod",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==3)
		{
			$field_array="company_name*variable_list*quantity_control*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_show_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_setting_printing_prod",$field_array,$data_array,"id","".$update_id."",1);
		}
		else if(str_replace("'","",$cbo_variable_list_production)==4)
		{
			$field_array="company_name*variable_list*quantity_control*updated_by*update_date*status_active";
			$data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_show_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$rID=sql_update("variable_setting_printing_prod",$field_array,$data_array,"id","".$update_id."",1);
		}
        else if(str_replace("'","",$cbo_variable_list_production)==8)
        {
            $field_array="company_name*variable_list*quantity_control*updated_by*update_date*status_active";
            $data_array="".$cbo_company_name_production."*".$cbo_variable_list_production."*".$cbo_item_show_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
            $rID=sql_update("variable_setting_printing_prod",$field_array,$data_array,"id","".$update_id."",1);
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