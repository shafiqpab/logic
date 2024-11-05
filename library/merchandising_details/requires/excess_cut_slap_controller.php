<?php
/*******************************************************************
|	Purpose			:	This controller is for Composition Entry
|	Functionality	:	
|	JS Functions	:
|	Created by		:	Aziz
|	Creation date 	:	19.12.2015
|	Updated by 		: 	zakaria joy	
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
if ($action=="on_change_data")
{
	extract($_REQUEST);
	$explode_data = explode("_",$data);
	$buyer_id = $explode_data[1];
	$company_id = $explode_data[0];
	//echo $company_id;die;
	$buyer_cond="";
	if($buyer_id>0) $buyer_cond=" and buyer_id=$buyer_id";
	$nameArray= sql_select("SELECT id, comapny_id, buyer_id, slap_sl_id, lower_limit_qty, upper_limit_qty, print, emb, wash, splwork, cutting, sewing, finishing, print_difficulty, emb_difficulty,  wash_difficulty, splwork_difficulty, cutting_difficulty, sewing_difficulty, finishing_difficulty,ship_plan, ship_plan_difficulty, total from lib_excess_cut_slab where comapny_id=$company_id  $buyer_cond  and status_active=1 and is_deleted=0 order by id");
	if(count($nameArray)>0)$is_update=1;else $is_update=0;
	
	?>
	<!-- min-height:20px; max-height:250px;-->
	<fieldset>
		<legend>Excess Cut Entry</legend>
		<div onLoad="set_hotkey();" style="width:1400px;height:auto; " id="cuting_list_cont" align="center">
			<table  width="1400px" cellpadding="0" cellspacing="0" border="1" class="rpt_table" align="center" id="tbl_cut_details" rules="all" >
				<thead>
					<th width="30">Slab No</th>
					<th width="60">Lower Limit(Qty)</th>
					<th width="60">Upper Limit(Qty)</th>
					<th width="50">Print(%)</th>
					<th width="60">Difficulty</th>
					<th width="50">Embro(%)</th>
					<th width="60">Difficulty</th>
					<th width="50">Gmt Wash(%)</th>
					<th width="60">Difficulty</th>
					<th width="50">SP. Works(%)</th>
					<th width="60">Difficulty</th>
					<th width="50">Cutting(%)</th>
					<th width="60">Difficulty</th>					
					<th width="50">Sewing(%)</th>
					<th width="60">Difficulty</th>
					<th width="50">Finishing(%)</th>
					<th width="60">Difficulty</th>
					<th width="50">Ship Plan(%)</th>
					<th width="60">Difficulty</th>
					<th width="60">Total(%)</th>
					<th width="60">&nbsp;</th>
					
				</thead>
				<tbody id="cut_slap_dtls">
				
				   <?
				   	$slab_arr=array(1=>'print',2=>'embro',3=>'gmtwash',4=>'spworks',5=>'cutting', 6=>'sewing', 7=>'finishing', 8=>'shipplan');
					$slab_update_arr=array('print'=>'print','emb'=>'embro','wash'=>'gmtwash','splwork'=>'spworks','cutting'=>'cutting','sewing'=>'sewing', 'finishing'=>'finishing', 'shipplan'=>'shipplan');
				   	//$slab_update_arr=array('print'=>'print','emb'=>'embro','wash'=>'gmtwash','splwork'=>'spworks','cutting'=>'cutting','sewing'=>'sewing', 'finishing'=>'finishing', 'ship_plan'=>'ship_plan');

				   	$disabled='';
					if(count($nameArray)>0)
					{ $i=1;
					foreach($nameArray as $row)
					{
						if(count($nameArray)!=$i)
						{
							$disabled="disabled";
						}
						else{
							$disabled='';
						} 
				   ?>
				   <tr id="trCut_<?=$i; ?>">
					<td> 
						<input type="text" name="txtslapid[]" id="txtslapid_<?=$i;?>" style="width:50px;" class="text_boxes_numeric" align="left" value="<?=$row[csf('slap_sl_id')];?>" readonly="readonly"/>
					</td>
					 <td> 
						<input type="text" name="txtlowerid[]" id="txtlowerid_<?=$i;?>" class="text_boxes_numeric" style="width:60px;" align="right" value=" <?=$row[csf('lower_limit_qty')];?> " /><!--readonly ISD-22-14595 by kausar-->
					</td>
					<td> 
						<input type="text" name="txtupperid[]" id="txtupperid_<?=$i;?>" class="text_boxes_numeric" style="width:60px;" align="right" value="<?=$row[csf('upper_limit_qty')];?> " onChange="check_qty_limit(<?=$i; ?>);" <?=$disabled ?>/>
					</td>
					<? 
						foreach ($slab_update_arr as $key => $data) { ?>
						<td> 
							<input  type="text" name="txt<?=$data; ?>[]" id="txt<?=$data; ?>_<?=$i; ?>" class="text_boxes_numeric" style="width:50px;" align="right" onChange="calculate_total_per(<?=$i; ?>);" value="<?=$row[csf($key)]; ?>" />
						</td>
						<td> 
							<? 
							$key_generate=$key.'_difficulty';
							echo create_drop_down( "cbo".$data."_".$i, 60, $difficulty_arr,"", 1, "Difficulty", $row[csf($key_generate)], "",0,"" ); ?>
						</td>
						<? }
					?>

					<td> 
						<input type="text" name="txttotalper[]" id="txttotalper_<?=$i; ?>" class="text_boxes_numeric" style="width:70px;" align="right" value="<?=$row[csf('total')];?> " disabled=""/>
						 <input type="hidden" name="updateid[]" id="updateid_<?=$i; ?>" style="width:20px;" class="text_boxes"  value="<?=$row[csf('id')]; ?> " />
					</td>
					<td> 
						<input type="button" id="increase_<?=$i; ?>" style="width:25px" class="formbutton" value="+" onClick="add_break_down_tr(<?=$i; ?>);" />
                        <input type="button" id="decrease_<?=$i; ?>" style="width:25px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(<?=$i; ?>);" />
					</td>
				</tr>
				<? 
				$i++;
						}
					}
					else
					{
					?>
					<tr>
					<td> 
						<input  type="text" name="txtslapid[]" id="txtslapid_1" style="width:50px;" class="text_boxes_numeric" align="left" value="1" readonly="readonly"/>
					</td>
					 <td> 
						<input  type="text" name="txtlowerid[]" id="txtlowerid_1" class="text_boxes_numeric" style="width:60px;" align="right" value="0" />
					</td>
					 <td> 
						<input  type="text" name="txtupperid[]" id="txtupperid_1" class="text_boxes_numeric" style="width:60px;"  align="right" onChange="check_qty_limit(1)" /> <!-- onBlur="append_cut_slap_row(this.value,1);" -->
					</td>
					<? 
						foreach ($slab_arr as $key => $data) { ?>
						<td> 
							<input  type="text" name="txt<?=$data; ?>[]" id="txt<?=$data; ?>_1" class="text_boxes_numeric" style="width:50px;" align="right" onChange="calculate_total_per(1);"/>
						</td>
						<td> 
							<? echo create_drop_down( "cbo".$data."_1", 60, $difficulty_arr,"", 1, "Difficulty", $selected, "",0,"" ); ?>
						</td>
						<? }
					?>
					<td> 
						<input  type="text" name="txttotalper[]" id="txttotalper_1" class="text_boxes_numeric" style="width:60px;"  align="right" disabled="" />
						<input  type="hidden" name="updateid[]" id="updateid_1"/>
					</td>
					<td> 
						<input type="button" id="increase_1" style="width:25px" class="formbutton" value="+" onClick="add_break_down_tr(1);" />
                        <input type="button" id="decrease_1" style="width:25px" class="formbutton" value="-" onClick="fn_deletebreak_down_tr(1);" />
					</td>
					 
				</tr>
					<?	
					}
				?>
				</tbody>
			</table>
		</div>
		 <div style="width:1400px; height:auto;" id="slap_list" align="center">
			<table cellspacing="0" width="100%" >
						<tr> 
							<td align="center" width="320">&nbsp;</td>						
						</tr>						 
						<tr>
						   <td colspan="3" height="40" valign="bottom" align="center" class="button_container">
								<?=load_submit_buttons( $permission, "fnc_excess_cut_slap", $is_update,0 ,"reset_form('variable_settings_container','cuting_list_cont','')",1); ?>
							</td>					
						</tr>
			 </table>
		</div>
		<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</fieldset>
	<?
}

if ($action=="save_update_delete")
{  
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)//Insert Here
	{
		$con = connect();
		$id = return_next_id( "id", "lib_excess_cut_slab", 1 );
		$field_array="id, comapny_id, buyer_id, slap_sl_id, lower_limit_qty, upper_limit_qty, print, emb, wash, splwork, cutting, sewing, finishing, print_difficulty, emb_difficulty, wash_difficulty, splwork_difficulty, cutting_difficulty, sewing_difficulty, finishing_difficulty, ship_plan, ship_plan_difficulty, total, inserted_by, insert_date, status_active, is_deleted";
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$txtslapid="txtslapid_".$i;
			$txtlowerid="txtlowerid_".$i;
			$txtupperid="txtupperid_".$i;

			$txtprint="txtprint_".$i;
			$txtembro="txtembro_".$i;
			$txtgmtwash="txtgmtwash_".$i;
			$txtspworks="txtspworks_".$i;
			$txtcutting="txtcutting_".$i;
			$txtsewing="txtsewing_".$i;
			$txtfinishing="txtfinishing_".$i;

			$cboprint="cboprint_".$i;
			$cboembro="cboembro_".$i;
			$cbogmtwash="cbogmtwash_".$i;
			$cbospworks="cbospworks_".$i;
			$cbocutting="cbocutting_".$i;
			$cbosewing="cbosewing_".$i;
			$cbofinishing="cbofinishing_".$i;

			$txtship_plan="txtshipplan_".$i;
			$cboship_plan="txtshipplan_".$i;

			$txttotalper="txttotalper_".$i;
			$updateid="updateid_".$i;
			
			if(str_replace("'","",$$txttotalper)!='')
			{
				if($return_id=="") $return_id=$id; else $return_id.=",".$id;
				if ($add_comma!=0) $data_array .=",";
				//$field_array="id, comapny_id, buyer_id, slap_sl_id, lower_limit_qty, upper_limit_qty, print, emb, wash, splwork, cutting, sewing, finishing, print_difficulty, emb_difficulty,  wash_difficulty, splwork_difficulty, cutting_difficulty, sewing_difficulty, finishing_difficulty, ship_plan, ship_plan_difficulty, total, inserted_by, insert_date, status_active, is_deleted";
				$data_array.="(".$id.",".$cbo_company_name.",".$cbo_buyer_name.",".$$txtslapid.",".$$txtlowerid.",".$$txtupperid.",".$$txtprint.", ".$$txtembro.", ".$$txtgmtwash.", ".$$txtspworks.", ".$$txtcutting.", ".$$txtsewing.", ".$$txtfinishing.", ".$$cboprint.", ".$$cboembro.", ".$$cbogmtwash.", ".$$cbospworks.", ".$$cbocutting.", ".$$cbosewing.", ".$$cbofinishing.",".$$txtship_plan.",".$$cboship_plan.",".$$txttotalper.",'".$user_id."','".$pc_date_time."',1,0)"; 
				$id=$id+1;
				$add_comma++;
			}
		}
		// echo "10**INSERT INTO lib_excess_cut_slab(".$field_array.") VALUES ".$data_array;die;
		$rID=sql_insert("lib_excess_cut_slab",$field_array,$data_array,1);
		
		//----------------------------------------------------------------------------------
		
		
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
		//$field_array2="id,comapny_id,buyer_id,slap_sl_id,lower_limit_qty,upper_limit_qty,percentage,inserted_by,insert_date,status_active,is_deleted";
		$field_array2="id, comapny_id, buyer_id, slap_sl_id, lower_limit_qty, upper_limit_qty, print, emb, wash, splwork, cutting, sewing, finishing, print_difficulty, emb_difficulty,  wash_difficulty, splwork_difficulty, cutting_difficulty, sewing_difficulty, finishing_difficulty, ship_plan, ship_plan_difficulty,  total, inserted_by, insert_date, status_active, is_deleted";
		$field_array_up="comapny_id*buyer_id*slap_sl_id*lower_limit_qty*upper_limit_qty*print*emb*wash*splwork*cutting*sewing*finishing*print_difficulty*emb_difficulty*wash_difficulty*splwork_difficulty*cutting_difficulty*sewing_difficulty*finishing_difficulty*ship_plan*ship_plan_difficulty*total*updated_by*update_date*status_active*is_deleted";
		//$field_array_delete="updated_by*update_date*status_active*is_deleted";
	    //$data_array_delete="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$id = return_next_id( "id", "lib_excess_cut_slab", 1 );
		$add_comma=0;
		$return_id="";
		for($i=1; $i<=$row_num; $i++)
		{
			$txtslapid="txtslapid_".$i;
			$txtlowerid="txtlowerid_".$i;
			$txtupperid="txtupperid_".$i;

			$txtprint="txtprint_".$i;
			$txtembro="txtembro_".$i;
			$txtgmtwash="txtgmtwash_".$i;
			$txtspworks="txtspworks_".$i;
			$txtcutting="txtcutting_".$i;
			$txtsewing="txtsewing_".$i;
			$txtfinishing="txtfinishing_".$i;

			$cboprint="cboprint_".$i;
			$cboembro="cboembro_".$i;
			$cbogmtwash="cbogmtwash_".$i;
			$cbospworks="cbospworks_".$i;
			$cbocutting="cbocutting_".$i;
			$cbosewing="cbosewing_".$i;
			$cbofinishing="cbofinishing_".$i;

			 

			$txtship_plan="txtshipplan_".$i;
			$cboship_plan="txtshipplan_".$i;

			$txttotalper="txttotalper_".$i;
			$updateid="updateid_".$i;
			
			if(str_replace("'","",$$updateid)=="")
			{
				if(str_replace("'","",$$txttotalper)!='')
				{
					if($return_id=="") $return_id=$id; else $return_id.=",".$id;
					if ($add_comma!=0) $data_array2 .=",";
					$data_array2.="(".$id.",".$cbo_company_name.",".$cbo_buyer_name.",".$$txtslapid.",".$$txtlowerid.",".$$txtupperid.",".$$txtprint.", ".$$txtembro.", ".$$txtgmtwash.", ".$$txtspworks.", ".$$txtcutting.", ".$$txtsewing.", ".$$txtfinishing.", ".$$cboprint.", ".$$cboembro.", ".$$cbogmtwash.", ".$$cbospworks.", ".$$cbocutting.", ".$$cbosewing.", ".$$cbofinishing.",".$$txtship_plan.",".$$cboship_plan.",".$$txttotalper.",'".$user_id."','".$pc_date_time."',1,0)"; 
					$id=$id+1;
					$add_comma++;
				}
			}			
			else  
			{
				if($return_id=="") $return_id=str_replace("'",'',$$updateid); else $return_id.=",".str_replace("'",'',$$updateid);
				$updateID_array[]=str_replace("'",'',$$updateid); 
				$data_array_up[str_replace("'",'',$$updateid)]=explode("*",("".$cbo_company_name."*".$cbo_buyer_name."*".$$txtslapid."*".$$txtlowerid."*".$$txtupperid."*".$$txtprint."*".$$txtembro."*".$$txtgmtwash."*".$$txtspworks."*".$$txtcutting."*".$$txtsewing."*".$$txtfinishing."*".$$cboprint."*".$$cboembro."*".$$cbogmtwash."*".$$cbospworks."*".$$cbocutting."*".$$cbosewing."*".$$cbofinishing."*".$$txtship_plan."*".$$cboship_plan."*".$$txttotalper."*'".$user_id."'*'".$pc_date_time."'*1*0"));
			}
			$mstUpdate_id_array=array();
			$sql_dtls="Select id from lib_excess_cut_slab where comapny_id=".str_replace("'",'',$cbo_company_name)." and buyer_id=".str_replace("'",'',$cbo_buyer_name)." and status_active=1 and is_deleted=0";
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
		$flag=$rIDdel=$rIDUp=$rID=1;
		$field_array_del="status_active*is_deleted*updated_by*update_date";
		$data_array_del="'0'*'1'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		if(implode(',',$distance_delete_id)!="")
		{
			foreach($distance_delete_id as $id_val)
			{
				$rIDdel=sql_update("lib_excess_cut_slab",$field_array_del,$data_array_del,"id","".$id_val."",1);
				if($rIDdel==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		$rIDUp=execute_query(bulk_update_sql_statement("lib_excess_cut_slab","id",$field_array_up,$data_array_up,$updateID_array),1);
		if($rIDUp==1 && $flag==1) $flag=1; else $flag=0;
		
		if($data_array2!="") 
		{
			$rID=sql_insert("lib_excess_cut_slab",$field_array2,$data_array2,1); 
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
			//echo "10**INSERT INTO lib_excess_cut_slab(".$field_array2.") VALUES ".$data_array2;die;
		}
		//echo "10**".$flag.'='.$rIDdel.'='.$rIDUp.'='.$rID; die;
		
		//----------------------------------------------------------------------------------
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


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id, composition_name, status_active from lib_composition_array where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_composition_name').value = '".($inf[csf("composition_name")])."';\n";    
		echo "document.getElementById('cbo_status').value			= '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_composition_entry',1);\n";  
	}
}

?>