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
   // echo $type;
    $company_id = $explode_data[1];

     
        $nameArray= sql_select("select id, company_name, variable_list, work_order_number_control from variable_setting_trim_marketing where company_name='$company_id' and variable_list=$explode_data[0] order by id");
        if(count($nameArray)>0) $is_update=1; else $is_update=0;
        ?>
        <fieldset>
            <legend>Wo No. From System Fill Up</legend>
            <div style="width:700px; min-height:20px; max-height:250px;" id="variable_list_cont1" align="center">
                <table cellspacing="0" width="100%" >
                    <tr>
                    <? if($type==1)
					{ 
					?>
                        <td width="130" align="center" id="cutting_update">WO. No. From System</td>
                        <td width="190">
                            <?
                            echo create_drop_down( "cbo_wo_number_from_system_status", 170, $yes_no,'', 1, '--Select--', $nameArray[0][csf('work_order_number_control')], "" );
                            ?>
                        </td>
                        <? 
						} 
					   else if($type==2)
					   { 
						?>
                        <td width="130" align="center" id="cutting_update">Trims Group Auto Fill up</td>
                        <td width="190">
                            <?
                            echo create_drop_down( "cbo_wo_number_from_system_status", 170, $yes_no,'', 1, '--Select--', $nameArray[0][csf('work_order_number_control')], "" );
                            ?>
                        </td>
                        <? 
						}  
                       else if($type==3)
					   { 
						?>
                        <td width="130" align="center" id="cutting_update">Order Receive Qty Update Upto- Bill/Production/Delivery.</td> 
                        <td width="190">
                            <?
							$upto_trims_variable_arr = array(1 => "Bill",2 => "Production",3 => "Delivery");
                            echo create_drop_down( "cbo_wo_number_from_system_status", 170, $upto_trims_variable_arr,'', 1, '--Select--', $nameArray[0][csf('work_order_number_control')], "" );
                            ?>
                        </td>
                        <? 
						}  
						
						?>
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
                            echo load_submit_buttons( $permission, "fnc_marketing_variable_settings", $is_update,0 ,"reset_form('marketingVariableSettings','','')",1);
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </fieldset>
        <?
    
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

        if (is_duplicate_field( "company_name", "variable_setting_trim_marketing", "company_name=$cbo_company_name_marketing and variable_list=$cbo_variable_list_marketing" ) == 1)
        {
            echo 11; disconnect($con); die;
        }
        else
        {
            $id=return_next_id( "id", "variable_setting_trim_marketing", 1 ) ;
            if(str_replace("'","",$cbo_variable_list_marketing)==1 || str_replace("'","",$cbo_variable_list_marketing)==2 || str_replace("'","",$cbo_variable_list_marketing)==3)
            {
                $field_array="id, company_name, variable_list, work_order_number_control, inserted_by, insert_date, status_active";
                $data_array="(".$id.",".$cbo_company_name_marketing.",".$cbo_variable_list_marketing.",".$cbo_wo_number_from_system_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
            }
            $rID=sql_insert("variable_setting_trim_marketing",$field_array,$data_array,1);
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
        if(str_replace("'","",$cbo_variable_list_marketing)==1 || str_replace("'","",$cbo_variable_list_marketing)==2 || str_replace("'","",$cbo_variable_list_marketing)==3)
        {
            $field_array="company_name*variable_list*work_order_number_control*updated_by*update_date*status_active";
            $data_array="".$cbo_company_name_marketing."*".$cbo_variable_list_marketing."*".$cbo_wo_number_from_system_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
            $rID=sql_update("variable_setting_trim_marketing",$field_array,$data_array,"id","".$update_id."",1);
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