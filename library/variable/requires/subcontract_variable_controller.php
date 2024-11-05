<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action == "eval_multi_select")
{
	$explode_data = explode("_",$data);
	$company_id = $explode_data[1];
	$nameArray= sql_select("select id, dyeing_fin_bill from  variable_settings_subcon where company_id='$company_id' and variable_list=$explode_data[0] order by id");
	$return_data=($nameArray[0][csf('dyeing_fin_bill')]) ? $nameArray[0][csf('dyeing_fin_bill')] : "EMPTY";
	echo $return_data;
    exit();
}

if ($action=="on_change_data")
{
	$explode_data = explode("_",$data);
	$type = $explode_data[0];
	$company_id = $explode_data[1];
		
	$nameArray= sql_select("select id, dyeing_fin_bill, allow_per from variable_settings_subcon where company_id='$company_id' and variable_list=$explode_data[0] order by id");
	if(count($nameArray)>0) $is_update=1; else $is_update=0;
	$mst_id=$nameArray[0][csf('id')];
		
	if($explode_data[0]==5) $setting_type="Roll Level";
	else if($explode_data[0]==6) $setting_type="Barcode";
	else if($explode_data[0]==13) $setting_type="Batch";
	else if($explode_data[0]==16) $setting_type="Validation about Yarn Issue and Knitting Production";
	else $setting_type="Bill On";	
	?>
	<fieldset>
	<legend><? echo $setting_type;?></legend>
        <div style="width:600px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
            <table cellspacing="0" width="100%" >
            	<? if($explode_data[0]==1)
					{
					?>
	                <tr> 
	                    <td width="130" align="left" id="bill_on">Bill On
                         <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
	                    <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $dyeing_finishing_bill,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
	                </tr>
                	<? } 
                	else if ($explode_data[0]==2)
                	{?>
	                <tr> 
	                    <td width="130" align="left" id="bill_on">Rate Type
                         <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
	                    <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
	                </tr>
                <? } else if ($explode_data[0]==3){?>
                <tr> 
                    <td width="130" align="left" id="bill_on">Bill Rate Type 
                     <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                    </td>
                    <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $bill_rate,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "fnc_editible(this.value)",'','1,3,4,5' ); ?></td>
					<td style="display: none;" id="td_show">Editable :::: <? echo create_drop_down( "cbo_yes_1", 70, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('allow_per')], "",'','' ); ?></td>
                </tr>
                <? } else if ($explode_data[0]==4){?>
                 <tr> 
                    <td width="130" align="left" id="bill_on">Source
                     <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                    </td>
                    <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, array(1=>"Receive",2=>"Production",3=>"Issue"),'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
                </tr>
                <? }
				 else if ($explode_data[0]==5){?>
                 <tr> 
                    <td width="130" align="left" id="bill_on">Fabric in Roll Level
                     <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                    </td>
                    <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
                </tr>
                <? }
				 else if ($explode_data[0]==6){?>
                 <tr> 
                    <td width="130" align="left" id="bill_on">Barcode Generation
                     <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                    </td>
                    <td width="190">
						<?
						$barcode_generation_arr=array(1=>"From System",2=>"External Device For Barcode"); 
						echo create_drop_down( "cbo_bill_on", 170, $barcode_generation_arr,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' );
                        ?>
                    </td>
                </tr>
                <? }
				else if ($explode_data[0]==7){?>
                 <tr> 
                    <td width="130" align="left" id="bill_on">Knit Bill From
                     <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                    </td>
                    <td width="190">
						<?
						$bill_from_arr=array(1=>"Production type auto fabric yes & Store Receive",2=>"Fabric Delivery to Store",3=>"FSO For Service"); 
						echo create_drop_down( "cbo_bill_on", 170, $bill_from_arr,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' );
                        ?>
                    </td>
                </tr>
                <? }
				else if ($explode_data[0]==8){?>
                 <tr> 
                    <td width="130" align="left" id="bill_on">Finishing Bill From
                     <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                    </td>
                    <td width="190">
						<?
						$bill_from_arr=array(1=>"Production type auto fabric yes & Store Receive",2=>"Fabric Delivery to Store");
						echo create_drop_down( "cbo_bill_on", 170, $bill_from_arr,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' );
                        ?>
                    </td>
                </tr>
                <? } 
				else if ($explode_data[0]==9 || $explode_data[0]==10 || $explode_data[0]==11 || $explode_data[0]==12 ){
					$nameArray= sql_select("select id, dyeing_fin_bill from variable_settings_subcon where company_id='$company_id' and variable_list=$explode_data[0] order by id");
						if(count($nameArray)>0) $update_id=$nameArray[0]['ID'];
					?>
                 <tr> 
                    <td width="130" align="left" id="bill_on">Bill Control
                     <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                    </td>
                    <td width="190">
						<?
						$control_with_arr=array(1=>"Pre-Cost/Budget");//,2=>"Fabric Booking"
						echo create_drop_down( "cbo_bill_on", 170, $control_with_arr,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' );
                        ?>
                    </td>
                    <input type="hidden" name="update_id" id="update_id" value="<?=$update_id;?>">
                </tr>
                <? } 
                else if ($explode_data[0]==13)
                	{?>
	                <tr> 
	                    <td width="130" align="left" id="bill_on">Allow
                         <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
	                    <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
	                </tr>
                <? }
                else if ($explode_data[0]==14)
                	{?>
	                <tr> 
	                    <td width="130" align="left" id="bill_on">Allow
                         <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
	                    <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
	                </tr>
                <? }
                else if ($explode_data[0]==15)
				{
					$nameArray[0][csf('dyeing_fin_bill')] = ($nameArray[0][csf('dyeing_fin_bill')] == '' ? 2 : $nameArray[0][csf('dyeing_fin_bill')]);
					?>
                    <tr> 
                        <td width="130" align="left" id="bill_on">Is Program Maintain
                         <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
                        <td width="190"><?php echo create_drop_down( 'cbo_bill_on', 170, $yes_no, '', 0, '', $nameArray[0][csf('dyeing_fin_bill')], '','','' ); ?></td>
                    </tr>
                    <?php
				}
				else if ($explode_data[0]==16)
				{
					$nameArray[0][csf('dyeing_fin_bill')] = ($nameArray[0][csf('dyeing_fin_bill')] == '' ? 2 : $nameArray[0][csf('dyeing_fin_bill')]);
					if($nameArray[0][csf('allow_per')]=="") $nameArray[0][csf('allow_per')]=0;
					if($nameArray[0][csf('dyeing_fin_bill')]==2) $dis="disabled"; else $dis="";
					?>
                    <tr> 
                        <td width="130" align="left" id="bill_on">Validation about Yarn Issue and Knitting Production</td>
                        <td width="80"><?php echo create_drop_down( 'cbo_bill_on', 80, $yes_no, '', 0, '', $nameArray[0][csf('dyeing_fin_bill')], 'fnc_excessPer(this.value);','','' ); ?></td>
                        <td title="Excess %"><input type="text" name="txt_excess_per" id="txt_excess_per" class="text_boxes_numeric" value="<?=$nameArray[0][csf('allow_per')]; ?>" style="width:80px;" placeholder="Excess %" <?=$dis; ?>/>
                         <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
                    </tr>
                    <?php
				}
                else if ($explode_data[0]==17)
				{
					?>
					<tr> 
                        <td width="130" align="left" id="bill_on">Color Mixing In-bound Sub-Contract Program
                        <input  type="hidden"name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
                        <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
					</tr>
					<?
				}
				else if ($explode_data[0]==18)
				{
					$page_name_arr=array(1=>'Dyeing And Finishing Bill Entry Services W/O Mandatory Field',2=>'Knitting Bill Entry Services W/O Mandatory Field');
					//$sub_nameArray= sql_select("select id, company_name,variable_list,item_category_id,fabric_roll_level,page_upto_id from variable_settings_production where company_name='$company_id' and variable_list=3 and status_active=1 and is_deleted= 0 order by id");
					
					$sub_nameArray= sql_select("select id, dyeing_fin_bill, allow_per from variable_settings_subcon where company_id='$company_id' and variable_list=$explode_data[0] order by id");
						if(count($sub_nameArray)>0) $is_update=1; else $is_update=0;

				 
					foreach($sub_nameArray as $row)
					{
						$category_wise_array[$row[csf('dyeing_fin_bill')]]['allow_per']=$row[csf('allow_per')];
						$category_wise_array[$row[csf('dyeing_fin_bill')]]['dyeing_fin_bill']=$row[csf('dyeing_fin_bill')];
						$category_wise_array[$row[csf('dyeing_fin_bill')]]['update_id']=$row[csf('id')];
					}
					?>
					<tr> 
                        <td width="230" align="left" id="bill_on">Dyeing And Finishing Bill Entry</td>
                        <td width="100"><? echo create_drop_down( "cbo_bill_on_1", 270, $page_name_arr,'', 0, '---- Select ----', $category_wise_array[1]['dyeing_fin_bill'], "",'','1' ); ?></td>
                         <td width="100" align="left" id="bill_on"> &nbsp;
                         <input  type="hidden"name="update_id_1" id="update_id_1" value="<? echo $category_wise_array[1]['update_id']; ?>">
                         </td>
                        <td width="100"><? echo create_drop_down( "cbo_yes_1", 80, $yes_no,'', 1, '---- Select ----', $category_wise_array[1]['allow_per'], "",'','' ); ?></td>
                        
					</tr>
                    <tr> 
                        <td width="230" align="left" id="bill_on">Knitting Bill Entry</td>
                        <td width="100"><? echo create_drop_down( "cbo_bill_on_2", 270, $page_name_arr,'', 0, '---- Select ----', $category_wise_array[2]['dyeing_fin_bill'], "",'','2' ); ?></td>
                         <td width="100" align="left" id="bill_on">&nbsp;
                         <input  type="hidden"name="update_id_2" id="update_id_2" value="<? echo $category_wise_array[2]['update_id']; ?>">
                         </td>
                        <td width="100"><? echo create_drop_down( "cbo_yes_2", 80, $yes_no,'', 1, '---- Select ----', $category_wise_array[2]['allow_per'], "",'','' ); ?></td>
					</tr>
					<?
				}
				else if ($explode_data[0]==19)
				{
					$batch_color_from=array(1=>'Qty Pop up Color');
					?>
					<tr> 
                        <td width="130" align="left" id="bill_on">Sub-contract Batch Color From
                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
                        <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $batch_color_from,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
					</tr>
					<?
				}
				else if ($explode_data[0]==20)
				{  
					?>
					<tr> 
                        <td width="130" align="left" id="bill_on">Service Acknowledgement
                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
                        <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
					</tr>
					<?
				}
				else if ($explode_data[0]==21)
				{  
					?>
					<tr> 
                        <td width="130" align="left" id="bill_on">Dyeing Sub-Contract Order Entry Rate Come From Process Wise Finish Fabric Rate Chart v2
                        <input  type="hidden" name="update_id" id="update_id" value="<? echo $mst_id; ?>">
                        </td>
                        <td width="190"><? echo create_drop_down( "cbo_bill_on", 170, $yes_no,'', 1, '---- Select ----', $nameArray[0][csf('dyeing_fin_bill')], "",'','' ); ?></td>
					</tr>
					<?
				}
				?>
            </table>
        </div>
        <div style="width:400px; min-height:20px; max-height:250px;" id="variable_list_cont3" align="center">
            <table cellspacing="0" width="100%" >
                <tr> 
                    <td align="center" width="320">&nbsp;</td>						
                </tr>						 
                <tr>
                    <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
                    
						<? echo load_submit_buttons( $permission, "fnc_subcontract_variable_settings", $is_update,0 ,"reset_form('subcontractVariable','','')",1); ?>
                    </td>					
                </tr>
            </table>
        </div>
	</fieldset>
	<?
	exit();
}
	
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
		
		if (is_duplicate_field( "company_id", "variable_settings_subcon", "company_id=$cbo_company_id and variable_list=$cbo_variable_list") == 1)
		{
			echo "11**0"; disconnect($con); die;
		}
		else
		{
			$id=return_next_id( "id", "variable_settings_subcon", 1);
			$variable_list=str_replace("'","",$cbo_variable_list);
			if($variable_list==3)
			{
				$field_array="id, company_id, variable_list, dyeing_fin_bill, allow_per, inserted_by, insert_date, status_active"; 
				$data_array="(".$id.",".$cbo_company_id.",".$cbo_variable_list.",".$cbo_bill_on.",".$cbo_yes_1.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			}
			else if($variable_list==16)
			{
				$field_array="id, company_id, variable_list, dyeing_fin_bill, allow_per, inserted_by, insert_date, status_active"; 
				$data_array="(".$id.",".$cbo_company_id.",".$cbo_variable_list.",".$cbo_bill_on.",".$txt_excess_per.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			}
			else if($variable_list==18)
			{
				$field_array="id, company_id, variable_list, dyeing_fin_bill, allow_per, inserted_by, insert_date, status_active"; 
				//$data_array="(".$id.",".$cbo_company_id.",".$cbo_variable_list.",".$cbo_bill_on.",".$txt_excess_per.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
				$data_array="(".$id.",".$cbo_company_id.",".$cbo_variable_list.",".$cbo_bill_on_1.",".$cbo_yes_1.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

			$id=$id+1;
			$data_array.=",(".$id.",".$cbo_company_id.",".$cbo_variable_list.",".$cbo_bill_on_2.",".$cbo_yes_2.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			
			}
			else
			{
				$field_array="id, company_id, variable_list, dyeing_fin_bill, inserted_by, insert_date, status_active"; 
				$data_array="(".$id.",".$cbo_company_id.",".$cbo_variable_list.",".$cbo_bill_on.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";
			}
			//echo "10**=insert into variable_settings_subcon (".$field_array.") values ".$data_array."";die;
			$rID=sql_insert("variable_settings_subcon",$field_array,$data_array,1);
		}
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "0**".$rID.'**'.str_replace("'","",$cbo_variable_list);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID.'**'.str_replace("'","",$cbo_variable_list);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);   
				echo "0**".$rID.'**'.str_replace("'","",$cbo_variable_list);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID.'**'.str_replace("'","",$cbo_variable_list);
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
		$variable_list=str_replace("'","",$cbo_variable_list);
		if($variable_list==3)
		{
			$field_array="company_id*variable_list*dyeing_fin_bill*allow_per*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_id."*".$cbo_variable_list."*".$cbo_bill_on."*".$cbo_yes_1."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$update_id=str_replace("'","",$update_id);
			//echo "10**=A".$data_array;die;
		}
		else if($variable_list==16)
		{
			$field_array="company_id*variable_list*dyeing_fin_bill*allow_per*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_id."*".$cbo_variable_list."*".$cbo_bill_on."*".$txt_excess_per."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
		}
		else if($variable_list==18)
		{
			//$field_array="company_id*variable_list*dyeing_fin_bill*allow_per*updated_by*update_date*status_active"; 
			//$data_array="".$cbo_company_id."*".$cbo_variable_list."*".$cbo_bill_on."*".$txt_excess_per."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
			$field_array="company_id*variable_list*dyeing_fin_bill*allow_per*updated_by*update_date";

			$updateID_array[]=str_replace("'","",$update_id_1);
			$updateID_array[]=str_replace("'","",$update_id_2);
			
			//$data_array="(".$id.",".$cbo_company_id.",".$cbo_variable_list.",".$cbo_bill_on_1.",".$cbo_yes_1.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1)";

			$data_array[str_replace("'","",$update_id_1)]=explode("*",("".$cbo_company_id."*".$cbo_variable_list."*".$cbo_bill_on_1."*".$cbo_yes_1."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$data_array[str_replace("'","",$update_id_2)]=explode("*",("".$cbo_company_id."*".$cbo_variable_list."*".$cbo_bill_on_2."*".$cbo_yes_2."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			
			
		}
		else
		{
			$field_array="company_id*variable_list*dyeing_fin_bill*updated_by*update_date*status_active"; 
			$data_array="".$cbo_company_id."*".$cbo_variable_list."*".$cbo_bill_on."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1";
		}
		if($variable_list==18)
		{
			$rID=execute_query(bulk_update_sql_statement("variable_settings_subcon","id",$field_array,$data_array,$updateID_array),1);
		}
		else
		{
			$rID=sql_update("variable_settings_subcon",$field_array,$data_array,"id","".$update_id."",1);
		}
		 
		if($db_type==0)
		{
			if($rID )
			{
				mysql_query("COMMIT");  
				echo "1**".$rID.'**'.str_replace("'","",$cbo_variable_list);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID.'**'.str_replace("'","",$cbo_variable_list);
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID )
			{
				oci_commit($con);   
				echo "1**".$rID.'**'.str_replace("'","",$cbo_variable_list);
			}
			else
			{
				oci_rollback($con);
				echo "10**".$rID.'**'.str_replace("'","",$cbo_variable_list);
			}
		}
		disconnect($con);
		die;
	}	
}

function sql_update22($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit="",$return_query='')
{
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	if($return_query==1){return $strQuery ;}
echo $strQuery;die;
		//return $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	
	if ($exestd){user_activities($exestd);}
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}

?>