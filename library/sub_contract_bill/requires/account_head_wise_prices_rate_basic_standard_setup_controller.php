<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
extract($_REQUEST);

if ($action=="show_over_head_list_view")
{
	?>
	<div>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" id="list_data">
            <thead>
                <tr>
                    <th width="100">Process</th>
                    <th width="70">Rate/Pcs (BDT)</th>
                    <th width="70">COA</th>
                </tr>
            </thead>
			<tbody>
            <?php 
			list($company_id,$year,$month) = explode("__",$data);
			$sql_prv_data=sql_select("SELECT a.ID,a.PROCES_TYPE, b.AMMOUNT, b.AC_CAO_ID, b.COST_PER_PC, b.COST_PER_MIN,a.RATE_PER_PCS from lib_process_ac_head_standard_mst a, lib_process_ac_head_standard_dtls b where a.ID=b.MST_ID and a.PROCESS_YEAR=$year and a.COMPANY_ID=$company_id and  a.PROCESS_MONTH=$month and PROCES_TYPE in(1,2,3) and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.entry_form=705");

			$data_arr = array();
			foreach ($sql_prv_data as $r) 
			{
				$data_arr[$r['PROCES_TYPE']]['data'] .= $r['AC_CAO_ID']."*". $r['COST_PER_PC']."*". $r['COST_PER_MIN']."__";
				$data_arr[$r['PROCES_TYPE']]['rate'] = $r['RATE_PER_PCS'];
				$data_arr[$r['PROCES_TYPE']]['id'] = $r['ID'];
			}

            $i=0;
			$cbo_type_arr=array(1=>"Wash",2=>"Finishing",3=>"Packing");
            foreach($cbo_type_arr as $key=>$v)
			{

                $i++;		
                if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
            
				?>
				
				<tr bgcolor="<?php echo $bgcolor?>" >
					<td align="canter">
						<?=$v?> 
						<input type="hidden" id="process_<?=$i;?>" value="<?=$key;?>">
						<input type="hidden" id="update_id_<?=$i;?>" value="<?=$data_arr[$key]['id'];?>">
					</td>
					<td align="canter">
						<input type="text" class="text_boxes_numeric" style="width: 80px;" name="rate_<?=$i;?>" id="rate_<?=$i;?>" value="<?=$data_arr[$key]['rate']?>">
					</td>
					<td align="canter">
						<input type="text" class="text_boxes_numeric" style="width: 80px;" name="coa_<?=$i;?>" id="coa_<?=$i;?>" readonly placeholder="Browse" ondblclick="fnShowAcHeadPopup('<?=$key?>','<?=$i;?>');">
						<input type="hidden" id="prev_data_<?=$i;?>" value="<?=$data_arr[$key]['data'];?>">
					</td>
				</tr>
				
				<?
            }			
            ?>
			</tbody>
		</table>
	</div>
	<?
	exit();
}

if ($action=="account_head_popup")
{
	echo load_html_head_contents("Process Wise Finishing Charge Set up", "../../../", 1, 1,$unicode,'','',1,'');
	$company_id=$company_id;
	$year=$cbo_from_year;
	$month=$cbo_from_month;
	$prev_data_arr = array_filter(explode("__",$prev_data));
	$all_prv_data=array();
	foreach ($prev_data_arr as $v) 
	{
		$v_ex = explode("*",$v);
		$all_prv_data[$v_ex[0]]["prsnt"] = $v_ex[1];
		$all_prv_data[$v_ex[0]]["pcs_rate"] = $v_ex[2];
	}
	// echo "<pre>";print_r($all_prv_data);

			 
	$sql=" SELECT b.id as ID, b.ac_code as AC_CODE, b.ac_description as AC_DESCRIPTION
	FROM lib_account_group a, ac_coa_mst b
	WHERE a.id = b.ac_subgroup_id AND a.main_group = 7 AND a.status_active = 1 AND a.is_deleted = 0 AND b.is_deleted = 0 AND b.company_id = $company_id
	ORDER BY b.ac_code";	

	/* $sql_prv_data=sql_select("SELECT a.ID, b.AC_CAO_ID, b.COST_PER_PC, b.COST_PER_MIN from lib_process_ac_head_standard_mst a, lib_process_ac_head_standard_dtls b where a.ID=b.MST_ID and a.PROCESS_YEAR=$year and a.COMPANY_ID=$company_id and  a.PROCESS_MONTH=$month and PROCES_TYPE=$process and a.STATUS_ACTIVE=1 and a.IS_DELETED=0 and b.STATUS_ACTIVE=1 and b.IS_DELETED=0 and a.entry_form=705");

	$all_prv_data=array();
	foreach($sql_prv_data as $row)
	{
		$all_prv_data[$row["AC_CAO_ID"]]["COST_PER_PC"]=$row["COST_PER_PC"];
		$all_prv_data[$row["AC_CAO_ID"]]["COST_PER_MIN"]=$row["COST_PER_MIN"];
	} */
	// echo "<pre>";print_r($all_prv_data);
	$new_conn = integration_params(3);
	$account_head=sql_select($sql,"",$new_conn) ; 
	
	// print_r($account_head);
	?>
	<script>
		var rate = '<?=$rate;?>';
		function fnHandleRate(checkbox,row)
		{
			if(checkbox.checked)
			{
				// alert('ok'+row);
				$("#txt_dis_prsnt_"+row).attr("disabled",false);
			}
			else
			{
				$("#txt_dis_prsnt_"+row).attr("disabled",true);
			}
		}

		function fnSumPrsnt()
		{
			let tot_prsnt = 0;
			let tot_pcs_rate = 0;
			tot_row = $("#table_list_dtls tbody tr").length;
			// alert(tot_row);
			let i=1;
			for (let index = 0; index < tot_row; index++) 
			{
				let rate_pcs = (($("#txt_dis_prsnt_"+i).val()*1)*rate)/100;
				tot_pcs_rate += rate_pcs;
				tot_prsnt += $("#txt_dis_prsnt_"+i).val()*1;
				$("#txt_rate_"+i).val(rate_pcs);
				if(tot_prsnt>100)
				{
					tot_prsnt = tot_prsnt - $("#txt_dis_prsnt_"+i).val()*1;
					tot_pcs_rate = tot_pcs_rate - rate_pcs;
					$("#txt_dis_prsnt_"+i).val('');
					$("#txt_rate_"+i).val('');
					alert("You can not over 100%");
				}
				i++;
			}
			$("#txt_tot_prsnt").val(tot_prsnt);
			$("#txt_tot_rate").val(tot_pcs_rate);
		}

		function fn_close()
		{
			let data = "";
			tot_row = $("#table_list_dtls tbody tr").length;
			// alert(tot_row);
			let i=1;
			for (let index = 0; index < tot_row; index++) 
			{
				if($("#txt_dis_prsnt_"+i).val()*1>0)
				{
					data += $("#txt_acount_code"+i).val()+"*"+$("#txt_dis_prsnt_"+i).val()*1+"*"+$("#txt_rate_"+i).val()*1+"*"+$("#txt_ac_description_"+i).val()+"__";
				}
			
				i++;
			}
			// alert(data);
			$("#acc_head_data").val(data);
			parent.emailwindow.hide();
		}
	</script>
	<div>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="490">
			<thead>
				<tr>
					<th colspan='3' align="right" style="font-weight:bold;">Total &nbsp;&nbsp;&nbsp;</th>
					<th  align="center" style="font-weight:bold;">
						<input type="text" id="txt_tot_prsnt" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly>
					</th>
					<th>
						<input type="text" id="txt_tot_rate" name="txtminute_tot" style="width:70px; font-weight:bold;" class="text_boxes_numeric" readonly>
						<input type="hidden" id="acc_head_data">
					</th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="80">A/C Code</th>
					<th width="180">A/C Description</th>
					<th width="90">Distribution %</th>
					<th width="90">Rate/Pcs (BDT)</th>
				</tr>
			</thead>
		</table>
			
			<div style="width:510px; max-height:300px;overflow-y:auto;" >	 
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="490" class="rpt_table" id="table_list_dtls">
				<tbody>
				<?
				$i = 0;
				foreach($account_head as $value)
				{
					$i++;						
					if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";												
					?>
					<tr  bgcolor="<? echo $bgcolor; ?>">
						<td width="30" align="center">
							<? echo $i;?>  <input type="hidden" id="txt_id_<?echo $i;?>" name="txt_id[]" value="<? echo $value["ID"]?>"> 
						</td>
						<!-- <td width="20" align="center"><input type="checkbox" id="check_<?=$i;?>" onclick="fnHandleRate(this,'<?=$i;?>')"></td> -->
						<td width="80" align="center">
							<? echo $value["AC_CODE"]?> 
							<input type="hidden" name="txt_acount_code[]" id="txt_acount_code<? echo $i;?>" value="<? echo $value["AC_CODE"]?>"> 
						</td>
						<td width="180">
							<? echo $value["AC_DESCRIPTION"]?>  
							<input type="hidden" id="txt_ac_description_<? echo $i;?>" name="txt_ac_description[]" value="<? echo $value["AC_DESCRIPTION"]?>">  
						</td>
						<td  width="90" align="center">
							
							<input type="text" id="txt_dis_prsnt_<? echo $i;?>" value="<? echo $all_prv_data[$value["AC_CODE"]]["prsnt"]; ?>" onkeyup="fnSumPrsnt()" style="width:70px;" name="txt_dis_prsnt[]" class="text_boxes_numeric" maxlength="2">
						</td>
						<td width="90" align="center">
							<input type="text" id="txt_rate_<? echo $i;?>" value="<? echo $all_prv_data[$value["AC_CODE"]]["pcs_rate"]; ?>"   style="width:70px;" name="txt_rate[]" class="text_boxes_numeric">
						</td>
					</tr>
					<?
				}
				?>
				</tbody>
				<tfoot>
				<tr>
					<th colspan="5" align="center">	
						<div style="width:90%;text-align: center;">
							<input type="button" name="close" onclick="fn_close();" class="formbutton" value="Close" style="width:100px">
						</div>
							
					</th>
				</tr>
				</tfoot>
			</table>
		</div>
	</div>
	<?			
	
	exit();
}

//=================SAVE UPDATE DELETE==============
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)
	{	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
	
		$field_array="id, company_id, process_year, process_month, proces_type,entry_form,rate_per_pcs, inserted_by, insert_date, is_deleted,status_active";	
		
		$field_array_up="company_id*process_year*process_month*proces_type*rate_per_pcs*updated_by*update_date";

		$field_array_dtls="id, mst_id, ac_code, ac_description, cost_per_pc, cost_per_min, inserted_by, insert_date, is_deleted, status_active";

		$id_dtls=return_next_id("id", "lib_process_ac_head_standard_dtls", 1);

		for($i=1; $i<=$total_row; $i++)
		{
			$process        = "process".$i;
			$txt_rate       = "txt_rate".$i;
			$txt_prev_data  = "txt_prev_data".$i;
			$mstId     		= "update_id".$i;
			if($$mstId>0)
			{
				if(str_replace("'","",$$txt_prev_data) !="")
				{
					$mst_id_array[]=$$mstId;
					$data_array_up[$$mstId]=explode("*",("".$cbo_company_id."*".$cbo_from_year."*".$cbo_from_month."*'".$$process."'*".$$txt_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					// =========== for dtls table ==============
					
					$txt_prev_data_arr= array_filter(explode("__", str_replace("'","",$$txt_prev_data)));
					foreach ($txt_prev_data_arr as $rowStr) 
					{						
						$dataArr = explode("*",$rowStr);
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$id_dtls.",".$$mstId.",'".$dataArr[0]."','".$dataArr[3]."','".$dataArr[1]."','".$dataArr[2]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";
						$id_dtls=$id_dtls+1;

					}
				}
			}
			else
			{
				if(str_replace("'","",$$txt_prev_data) !="")
				{
					$mst_id=return_next_id( "id", "lib_process_ac_head_standard_mst", 1 ) ;
					if($data_array!="") $data_array.=",";
					$data_array.="(".$mst_id.",".$cbo_company_id.",".$cbo_from_year.",".$cbo_from_month.",".$$process.",705,".$$txt_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";

					
					$txt_prev_data_arr= array_filter(explode("__", str_replace("'","",$$txt_prev_data)));
					foreach ($txt_prev_data_arr as $rowStr) 
					{						
						$dataArr = explode("*",$rowStr);
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$id_dtls.",".$mst_id.",'".$dataArr[0]."','".$dataArr[3]."','".$dataArr[1]."','".$dataArr[2]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";
						$id_dtls=$id_dtls+1;

					}
				}
			}

		}
		// echo "10**insert into lib_process_ac_head_standard_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		// echo "10**";print_r($data_array_up);die;
		$rID1=$rID2=$rID3=$rID4=true;
		if(count($data_array_up)>0)
		{
			$rID1=execute_query(bulk_update_sql_statement( "lib_process_ac_head_standard_mst", "id", $field_array_up, $data_array_up, $mst_id_array ));
		}
	    if($data_array!="")
		{
			$rID2=sql_insert("lib_process_ac_head_standard_mst",$field_array,$data_array);
		}
		// echo "10**insert into operator_wise_cutting_entry_mst (".$field_array.") values ".$data_array;die;
		$deleted_id = implode(",",$mst_id_array);
		if($deleted_id!="")
		{
			$rID3=execute_query( "delete from lib_process_ac_head_standard_dtls where mst_id in ($deleted_id)",0);
		}

		
	    if($data_array_dtls!="")
		{
			$rID4=sql_insert("lib_process_ac_head_standard_dtls",$field_array_dtls,$data_array_dtls);
		}	

		// $rID=sql_insert("lib_process_ac_head_standard_mst",$field_array,$data_array,0);
		// $rID1=sql_insert("lib_process_ac_head_standard_dtls ",$field_array_dtls,$data_array_dtls,0);

		// echo "10**$rID1 = $rID2 = $rID3  = $rID4";die;

		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3  && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3  && $rID4)
			{
				oci_commit($con);  
				echo "0**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
		
	}
	//=================UPDATE==============
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}		
	
		$field_array="id, company_id, process_year, process_month, proces_type,entry_form,rate_per_pcs, inserted_by, insert_date, is_deleted,status_active";	
		
		$field_array_up="company_id*process_year*process_month*proces_type*rate_per_pcs*updated_by*update_date";

		$field_array_dtls="id, mst_id, ac_code, ac_description, cost_per_pc, cost_per_min, inserted_by, insert_date, is_deleted, status_active";

		$id_dtls=return_next_id("id", "lib_process_ac_head_standard_dtls", 1);

		for($i=1; $i<=$total_row; $i++)
		{
			$process        = "process".$i;
			$txt_rate       = "txt_rate".$i;
			$txt_prev_data  = "txt_prev_data".$i;
			$mstId     		= "update_id".$i;
			if($$mstId>0)
			{
				if(str_replace("'","",$$txt_prev_data) !="")
				{
					$mst_id_array[]=$$mstId;
					$data_array_up[$$mstId]=explode("*",("".$cbo_company_id."*".$cbo_from_year."*".$cbo_from_month."*'".$$process."'*".$$txt_rate."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					// =========== for dtls table ==============
					
					$txt_prev_data_arr= array_filter(explode("__", str_replace("'","",$$txt_prev_data)));
					foreach ($txt_prev_data_arr as $rowStr) 
					{						
						$dataArr = explode("*",$rowStr);
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$id_dtls.",".$$mstId.",'".$dataArr[0]."','".$dataArr[3]."','".$dataArr[1]."','".$dataArr[2]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";
						$id_dtls=$id_dtls+1;

					}
				}
			}
			else
			{
				if(str_replace("'","",$$txt_prev_data) !="")
				{
					$mst_id=return_next_id( "id", "lib_process_ac_head_standard_mst", 1 ) ;
					if($data_array!="") $data_array.=",";
					$data_array.="(".$mst_id.",".$cbo_company_id.",".$cbo_from_year.",".$cbo_from_month.",".$$process.",705,".$$txt_rate.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";

					
					$txt_prev_data_arr= array_filter(explode("__", str_replace("'","",$$txt_prev_data)));
					foreach ($txt_prev_data_arr as $rowStr) 
					{						
						$dataArr = explode("*",$rowStr);
						if($data_array_dtls!="") $data_array_dtls.=",";
						$data_array_dtls.="(".$id_dtls.",".$mst_id.",'".$dataArr[0]."','".$dataArr[3]."','".$dataArr[1]."','".$dataArr[2]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','0',1)";
						$id_dtls=$id_dtls+1;

					}
				}
			}

		}
		// echo "10**insert into lib_process_ac_head_standard_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		// echo "10**";print_r($data_array_up);die;
		$rID1=$rID2=$rID3=$rID4=true;
		if(count($data_array_up)>0)
		{
			$rID1=execute_query(bulk_update_sql_statement( "lib_process_ac_head_standard_mst", "id", $field_array_up, $data_array_up, $mst_id_array ));
		}
	    if($data_array!="")
		{
			$rID2=sql_insert("lib_process_ac_head_standard_mst",$field_array,$data_array);
		}
		// echo "10**insert into operator_wise_cutting_entry_mst (".$field_array.") values ".$data_array;die;
		$deleted_id = implode(",",$mst_id_array);
		if($deleted_id!="")
		{
			$rID3=execute_query( "delete from lib_process_ac_head_standard_dtls where mst_id in ($deleted_id)",0);
		}

		
	    if($data_array_dtls!="")
		{
			$rID4=sql_insert("lib_process_ac_head_standard_dtls",$field_array_dtls,$data_array_dtls);
		}	

		// $rID=sql_insert("lib_process_ac_head_standard_mst",$field_array,$data_array,0);
		// $rID1=sql_insert("lib_process_ac_head_standard_dtls ",$field_array_dtls,$data_array_dtls,0);

		// echo "10**$rID1 = $rID2 = $rID3  = $rID4";die;

		if($db_type==0)
		{
			if($rID1 && $rID2 && $rID3  && $rID4)
			{
				mysql_query("COMMIT");  
				echo "0**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID1 && $rID2 && $rID3  && $rID4)
			{
				oci_commit($con);  
				echo "0**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
		
	}
	//=================DELETE==============
	else if ($operation==2)
	{

	}
}  

?>

