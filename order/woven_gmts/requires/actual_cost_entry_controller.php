<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$based_on=array(1=>"Ex-factory Qty",2=>"Production Qty",3=>"SMV Produced");

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
		
		$field_array="id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd, inserted_by, insert_date";
		$id = return_next_id( "id", "wo_actual_cost_entry", 1 );
		
		if(str_replace("'", '', $cbo_cost_head)==5 || str_replace("'", '', $cbo_cost_head)==6)
		{
			if(is_duplicate_field( "id", "wo_actual_cost_entry", "company_id=$cbo_company_id and cost_head=$cbo_cost_head and incurred_date=$txt_incurred_date and incurred_date_to=$txt_incurred_to_date" )==1)
			{
				echo "11**0"; 
				disconnect($con);die;			
			}
			
			$qnty_array=array(); $job_array=array(); $item_smv_array=array(); $item_qty_array=array(); $tot_qnty=0; $tot_produced=0;
			if(str_replace("'", '', $cbo_based_on)==1)
			{
				$sql="select a.id, a.job_no_mst, b.item_number_id, sum(b.ex_factory_qnty) as qnty from wo_po_details_master w, wo_po_break_down a, pro_ex_factory_mst b where w.job_no=a.job_no_mst and a.id=b.po_break_down_id and w.company_name=$cbo_company_id and b.ex_factory_date between $txt_incurred_date and $txt_incurred_to_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.job_no_mst, b.item_number_id";
			}
			else if(str_replace("'", '', $cbo_based_on)==2)
			{
				$sql="SELECT a.id, a.job_no_mst, b.item_number_id, sum(c.production_qnty) as qnty from wo_po_details_master w, wo_po_break_down a, pro_garments_production_mst b ,pro_garments_production_dtls c where w.job_no=a.job_no_mst and a.id=b.po_break_down_id and b.id=c.mst_id  and w.company_name=$cbo_company_id and b.production_date between $txt_incurred_date and $txt_incurred_to_date and b.production_type=5 and a.status_active=1 and c.production_type=5 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.job_no_mst, b.item_number_id";
			}
			else
			{
				$sql="SELECT w.set_break_down, a.id, a.job_no_mst, b.item_number_id, sum(c.production_qnty) as qnty from wo_po_details_master w, wo_po_break_down a, pro_garments_production_mst b ,pro_garments_production_dtls c  where w.job_no=a.job_no_mst and a.id=b.po_break_down_id and b.id=c.mst_id  and w.company_name=$cbo_company_id and b.production_date between $txt_incurred_date and $txt_incurred_to_date and b.production_type=5 and a.status_active=1 and c.production_type=5 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.item_number_id, a.job_no_mst, w.set_break_down";
			}
			//echo "10**".$sql;die;
			$result=sql_select($sql);
			if(str_replace("'", '', $cbo_based_on)==3)
			{
				foreach($result as $row)
				{
					$item_smv='';
					$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
					foreach($exp_grmts_item as $value)
					{
						$grmts_item_qty = explode("_",$value);
						if($row[csf('item_number_id')]==$grmts_item_qty[0])
						{
							$item_smv=$grmts_item_qty[2];
							break;
						}
					}
					
					$smv_produced=$row[csf('qnty')]*$item_smv;
					$qnty_array[$row[csf('id')]][$row[csf('item_number_id')]]=$smv_produced;
					$tot_produced+=$smv_produced;
					$item_smv_array[$row[csf('id')]][$row[csf('item_number_id')]]=$item_smv;
					$item_qty_array[$row[csf('id')]][$row[csf('item_number_id')]]=$row[csf('qnty')];
					$job_array[$row[csf('id')]]=$row[csf('job_no_mst')];
				}
				
				$per_pcs_amnt=str_replace("'", '', $txt_amount)/$tot_produced;
				foreach($qnty_array as $po_id=>$po_data)
				{
					foreach($po_data as $item_id=>$smv_produced)
					{
						$item_smv=$item_smv_array[$po_id][$item_id];
						$qnty=$item_qty_array[$po_id][$item_id];
						$amount=$smv_produced*$per_pcs_amnt;
						$amntUsd=$amount/str_replace("'", '', $txt_exchange_rate_order);
						
						if($data_array!="") $data_array.=",";
						$data_array.="(".$id.",".$cbo_company_id.",".$cbo_cost_head.",".$txt_exchange_rate_order.",".$txt_incurred_date.",".$txt_incurred_to_date.",".$txt_applying_period_date.",".$txt_applying_period_to_date.",".$cbo_based_on.",'".$po_id."','".$job_array[$po_id]."','".$item_id."','".$item_smv."','".$qnty."','".$smv_produced."','".$amount."','".$amntUsd."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$id = $id+1;
					}
				}
			}
			else
			{
				foreach($result as $row)
				{
					$qnty_array[$row[csf('id')]][$row[csf('item_number_id')]]=$row[csf('qnty')];
					$tot_qnty+=$row[csf('qnty')];
					$job_array[$row[csf('id')]]=$row[csf('job_no_mst')];
				}
				
				$per_pcs_amnt=str_replace("'", '', $txt_amount)/$tot_qnty;
				foreach($qnty_array as $po_id=>$po_data)
				{
					foreach($po_data as $item_id=>$qnty)
					{
						$item_smv=$item_smv_array[$po_id][$item_id];
						$amount=$qnty*$per_pcs_amnt;
						$amntUsd=$amount/str_replace("'", '', $txt_exchange_rate_order);
						
						if($data_array!="") $data_array.=",";
						$data_array.="(".$id.",".$cbo_company_id.",".$cbo_cost_head.",".$txt_exchange_rate_order.",".$txt_incurred_date.",".$txt_incurred_to_date.",".$txt_applying_period_date.",".$txt_applying_period_to_date.",".$cbo_based_on.",'".$po_id."','".$job_array[$po_id]."','".$item_id."','".$item_smv."','".$qnty."',0,'".$amount."','".$amntUsd."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$id = $id+1;
					}
				}
			}
			
		}
		else
		{
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$txt_amount="txt_amount".$j;
				$po_id="po_id".$j;
				$jobNo="jobNo".$j;
				$amntUsd=$$txt_amount/str_replace("'", '', $txt_exchange_rate_order);
				
				if($data_array!="") $data_array.=",";
				$data_array.="(".$id.",".$cbo_company_id.",".$cbo_cost_head.",".$txt_exchange_rate_order.",".$txt_incurred_date.",".$txt_incurred_to_date.",".$txt_applying_period_date.",".$txt_applying_period_to_date.",".$cbo_based_on.",'".$$po_id."','".$$jobNo."',0,0,0,0,'".$$txt_amount."','".$amntUsd."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				$id = $id+1;
			}
		}
		//echo "10**insert into wo_actual_cost_entry (".$field_array.") values ".$data_array;oci_rollback($con);disconnect($con);die;
		$rID=sql_insert("wo_actual_cost_entry",$field_array,$data_array,0);

		if($db_type==0)
		{
			if($rID)
			{
				mysql_query("COMMIT");  
				echo "0**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID)
			{
				oci_commit($con);  
				echo "0**";
			}
			else
			{
				oci_rollback($con);
				echo "5**";
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
		
		$field_array="id, company_id, cost_head, exchange_rate, incurred_date, incurred_date_to, applying_period_date, applying_period_to_date, based_on, po_id, job_no, gmts_item_id, item_smv, production_qty, smv_produced, amount, amount_usd, inserted_by,insert_date";
		$id = return_next_id( "id", "wo_actual_cost_entry", 1 );
		$field_array_update="amount*amount_usd*updated_by*update_date";
		if(str_replace("'", '', $cbo_cost_head)==5 || str_replace("'", '', $cbo_cost_head)==6)
		{
			$qnty_array=array(); $job_array=array(); $item_smv_array=array(); $item_qty_array=array(); $tot_qnty=0; $tot_produced=0;
			if(str_replace("'", '', $cbo_based_on)==1)
			{
				$sql="select a.id, a.job_no_mst, b.item_number_id, sum(b.ex_factory_qnty) as qnty from wo_po_details_master w, wo_po_break_down a, pro_ex_factory_mst b where w.job_no=a.job_no_mst and a.id=b.po_break_down_id and w.company_name=$cbo_company_id and b.ex_factory_date between $txt_incurred_date and $txt_incurred_to_date and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.job_no_mst, b.item_number_id";
			}
			else if(str_replace("'", '', $cbo_based_on)==2)
			{
				$sql="SELECT a.id, a.job_no_mst, b.item_number_id, sum(c.production_qnty) as qnty from wo_po_details_master w, wo_po_break_down a, pro_garments_production_mst b ,pro_garments_production_dtls c where w.job_no=a.job_no_mst and a.id=b.po_break_down_id and b.id=c.mst_id and w.company_name=$cbo_company_id and b.production_date between $txt_incurred_date and $txt_incurred_to_date and b.production_type=5 and a.status_active=1 and c.production_type=5 and c.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.job_no_mst, b.item_number_id";
			}
			else
			{
				$sql="SELECT w.set_break_down, a.id, a.job_no_mst, b.item_number_id, sum(c.production_qnty) as qnty from wo_po_details_master w, wo_po_break_down a, pro_garments_production_mst b ,pro_garments_production_dtls c  where w.job_no=a.job_no_mst and a.id=b.po_break_down_id and b.id=c.mst_id  and w.company_name=$cbo_company_id and b.production_date between $txt_incurred_date and $txt_incurred_to_date and b.production_type=5 and a.status_active=1 and c.production_type=5 and c.status_active=1  and b.is_deleted=0 and b.status_active=1 and a.is_deleted=0 group by a.id, b.item_number_id, a.job_no_mst, w.set_break_down";
			}
			//echo "10**".$sql;die;
			$result=sql_select($sql);
			if(str_replace("'", '', $cbo_based_on)==3)
			{
				foreach($result as $row)
				{
					$item_smv='';
					$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
					foreach($exp_grmts_item as $value)
					{
						$grmts_item_qty = explode("_",$value);
						if($row[csf('item_number_id')]==$grmts_item_qty[0])
						{
							$item_smv=$grmts_item_qty[2];
							break;
						}
					}
					
					$smv_produced=$row[csf('qnty')]*$item_smv;
					$qnty_array[$row[csf('id')]][$row[csf('item_number_id')]]=$smv_produced;
					$tot_produced+=$smv_produced;
					$item_smv_array[$row[csf('id')]][$row[csf('item_number_id')]]=$item_smv;
					$item_qty_array[$row[csf('id')]][$row[csf('item_number_id')]]=$row[csf('qnty')];
					$job_array[$row[csf('id')]]=$row[csf('job_no_mst')];
				}
				//echo "10**".$tot_qnty;
				
				$per_pcs_amnt=str_replace("'", '', $txt_amount)/$tot_produced;
				foreach($qnty_array as $po_id=>$po_data)
				{
					foreach($po_data as $item_id=>$smv_produced)
					{
						$item_smv=$item_smv_array[$po_id][$item_id];
						$qnty=$item_qty_array[$po_id][$item_id];
						$amount=$smv_produced*$per_pcs_amnt;
						$amntUsd=$amount/str_replace("'", '', $txt_exchange_rate_order);
						
						if($data_array!="") $data_array.=",";
						$data_array.="(".$id.",".$cbo_company_id.",".$cbo_cost_head.",".$txt_exchange_rate_order.",".$txt_incurred_date.",".$txt_incurred_to_date.",".$txt_applying_period_date.",".$txt_applying_period_to_date.",".$cbo_based_on.",'".$po_id."','".$job_array[$po_id]."','".$item_id."','".$item_smv."','".$qnty."','".$smv_produced."','".$amount."','".$amntUsd."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$id = $id+1;
					}
				}
			}
			else
			{
				foreach($result as $row)
				{
					$qnty_array[$row[csf('id')]][$row[csf('item_number_id')]]=$row[csf('qnty')];
					$tot_qnty+=$row[csf('qnty')];
					$job_array[$row[csf('id')]]=$row[csf('job_no_mst')];
				}
				//echo "10**".$tot_qnty;print_r($item_smv_array);die;
				
				$per_pcs_amnt=str_replace("'", '', $txt_amount)/$tot_qnty;// per qty
				foreach($qnty_array as $po_id=>$po_data)
				{
					foreach($po_data as $item_id=>$qnty)
					{
						$item_smv=$item_smv_array[$po_id][$item_id];
						$amount=$qnty*$per_pcs_amnt;
						$amntUsd=$amount/str_replace("'", '', $txt_exchange_rate_order);
						
						if($data_array!="") $data_array.=",";
						$data_array.="(".$id.",".$cbo_company_id.",".$cbo_cost_head.",".$txt_exchange_rate_order.",".$txt_incurred_date.",".$txt_incurred_to_date.",".$txt_applying_period_date.",".$txt_applying_period_to_date.",".$cbo_based_on.",'".$po_id."','".$job_array[$po_id]."','".$item_id."','".$item_smv."','".$qnty."',0,'".$amount."','".$amntUsd."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$id = $id+1;
					}
				}	
			}
		}
		else
		{
			for($j=1;$j<=$tot_row;$j++)
			{ 	
				$txt_amount="txt_amount".$j;
				$po_id="po_id".$j;
				$jobNo="jobNo".$j;
				$txt_dtls_id="txt_dtls_id".$j;
				$amntUsd=$$txt_amount/str_replace("'", '', $txt_exchange_rate_order);
				if(str_replace("'","",$$txt_dtls_id)>0)
				{
					$id_arr[]=str_replace("'",'',$$txt_dtls_id);
					$data_array_update[str_replace("'",'',$$txt_dtls_id)] = explode("*",("'".str_replace("'","",$$txt_amount)."'*'".str_replace("'","",$amntUsd)."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				else
				{
					if($data_array!="") $data_array.=",";
					$data_array.="(".$id.",".$cbo_company_id.",".$cbo_cost_head.",".$txt_exchange_rate_order.",".$txt_incurred_date.",".$txt_incurred_to_date.",".$txt_applying_period_date.",".$txt_applying_period_to_date.",".$cbo_based_on.",'".$$po_id."','".$$jobNo."',0,0,0,0,'".$$txt_amount."','".$amntUsd."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id = $id+1;
				}
			}
		}
		
		//echo "10**"; print_r($data_array_update); echo "insert into wo_actual_cost_entry (".$field_array.") values ".$data_array;die;
		//$delete=execute_query("delete from wo_actual_cost_entry where company_id=$cbo_company_id and cost_head=$cbo_cost_head and incurred_date=$txt_incurred_date and incurred_date_to=$txt_incurred_to_date",0);
		$delete_rid=$rID=$up_rid=true;
		if(str_replace("'", '', $cbo_cost_head)==5 || str_replace("'", '', $cbo_cost_head)==6)
		{
			if($data_array!="")
			{
				$delete_rid=execute_query("delete from wo_actual_cost_entry where company_id=$cbo_company_id and cost_head=$cbo_cost_head and incurred_date=$txt_incurred_date and incurred_date_to=$txt_incurred_to_date",0);
				$rID=sql_insert("wo_actual_cost_entry",$field_array,$data_array,0);
			}
		}
		else
		{
			if($data_array!="")
			{
				$rID=sql_insert("wo_actual_cost_entry",$field_array,$data_array,0);
			}
			if(count($data_array_update)>0)
			{
				$up_rid=execute_query(bulk_update_sql_statement( "wo_actual_cost_entry", "id", $field_array_update, $data_array_update, $id_arr ));
			}
		}
		//echo "10** $UpRId = $rID";die;
		if($db_type==0)
		{
			if($rID && $delete_rid && $up_rid)
			{
				mysql_query("COMMIT");  
				echo "1**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $delete_rid && $up_rid)
			{
				oci_commit($con);  
				echo "1**";
			}
			else
			{
				oci_rollback($con);
				echo "6**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$delete=execute_query("delete from wo_actual_cost_entry where company_id=$cbo_company_id and cost_head=$cbo_cost_head and incurred_date=$txt_incurred_date and incurred_date_to=$txt_incurred_to_date",0);

		if($db_type==0)
		{
			if($delete)
			{
				mysql_query("COMMIT");  
				echo "2**";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "7**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($delete)
			{
				oci_commit($con);  
				echo "2**";
			}
			else
			{
				oci_rollback($con);
				echo "7**";
			}
		}
		disconnect($con);
		die;
	}
}

 
if($action=="on_change_load_page")
{
	if($data==5 || $data==6)
	{
		$disbled="disabled='disabled'";
		$display="";
	}
	else 
	{
		$disbled='';
		$display='style="display:none"';
	}
?>
	<div style="width:650px; float:left; margin:auto" align="center">
		<table width="550" cellspacing="2" cellpadding="0" border="0">
        	<tr>
				<td align="center" class="must_entry_caption">Incurred Date</td>
				<td>
					<input type="text" name="txt_incurred_date" id="txt_incurred_date" class="datepicker" onChange="calculate_date()" style="width:140px" readonly/>	
				</td>
				<td align="center" class="must_entry_caption">Incurred Date To</td>
				<td>
					<input type="text" name="txt_incurred_to_date" id="txt_incurred_to_date" style="width:140px" class="datepicker" disabled/>	
				</td>
			</tr>
			<tr>
				<td align="center" class="must_entry_caption">Applying Period</td>
				<td>
					<input type="text" name="txt_applying_period_date" id="txt_applying_period_date" class="datepicker" style="width:140px" onChange="show_po_list()" readonly="readonly" <? echo $disbled; ?>/>	
				</td>
				<td align="center" class="must_entry_caption">Applying Period To</td>
				<td>
					<input type="text" name="txt_applying_period_to_date" id="txt_applying_period_to_date" style="width:140px" class="datepicker" onChange="show_po_list()" readonly="readonly" <? echo $disbled; ?>/>	
				</td>
			</tr>
            <tr>
				<td align="center" class="must_entry_caption">Exchange Rate</td>
				<td><input type="text" name="txt_exchange_rate_order" id="txt_exchange_rate_order" class="text_boxes_numeric" style="width:140px" disabled="disabled" /></td>
				<td align="center" class="must_entry_caption">Amount (TK.)</td>
				<td><input type="text" name="txt_amount" id="txt_amount" class="text_boxes_numeric"style="width:80px" onkeyup="calculate_balance(1)"/>
                <input type="button" name="btn_propotion" id="btn_propotion" value="Proportionate" onClick="fn_amount_propotionate()" style="width:70px" class="formbuttonplasminus"/></td>
			</tr>
            <tr <? echo $display; ?>>
                <td align="center" class="must_entry_caption">Based On</td>
                <td>
                    <? 
						if($data==5)
						{
                        	echo create_drop_down( "cbo_based_on", 152, $based_on,'', '0', '---- Select ----', 1,"",''); 
						}
						else
						{
							echo create_drop_down( "cbo_based_on", 152, $based_on,'', '0', '---- Select ----', 1,"",'','1,2'); 	
						}
                    ?>	
                </td>
                <td align="center" style="padding-left:5px"><input type="button" class="formbuttonplasminus" id="details" name="details" value="Show Details" onclick="show_list_view_details();"/></td>
            </tr>
		</table>
	</div>
<?
	exit();
}

if($action=="check_conversion_rate")
{ 
	$data=explode("**",$data);
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}
	$exchange_rate=set_conversion_rate( $data[0], $conversion_date );
	echo $exchange_rate;
	exit();	
}

if($action=='populate_data_from_actual_cost')
{
	$data=explode("**",$data);
	
	if($db_type==0)
	{
		$incurred_date=change_date_format(trim($data[2]), "yyyy-mm-dd", "-");
		$incurred_date_to=change_date_format(trim($data[3]), "yyyy-mm-dd", "-");
	}
	else
	{
		$incurred_date=change_date_format(trim($data[2]),'','',1);
		$incurred_date_to=change_date_format(trim($data[3]),'','',1);
	}

	$data_array=sql_select("select sum(amount) as amount, exchange_rate, applying_period_date, applying_period_to_date from wo_actual_cost_entry where company_id='$data[0]' and cost_head='$data[1]' and incurred_date='$incurred_date' and incurred_date_to='$incurred_date_to' and status_active=1 and is_deleted=0 group by exchange_rate, applying_period_date, applying_period_to_date");
	
	if(count($data_array)>0) 
	{
		$button_status=1;
		$exchange_rate=$data_array[0][csf("exchange_rate")];
	}
	else 
	{
		$exchange_rate=set_conversion_rate( 2, $incurred_date_to );
		$button_status=0;
	}
	
	echo "document.getElementById('txt_exchange_rate_order').value 			= '".$exchange_rate."';\n";
	echo "document.getElementById('txt_amount').value 						= '".$data_array[0][csf("amount")]."';\n";
	echo "document.getElementById('txt_applying_period_date').value 		= '".change_date_format($data_array[0][csf("applying_period_date")])."';\n";
	echo "document.getElementById('txt_applying_period_to_date').value 		= '".change_date_format($data_array[0][csf("applying_period_to_date")])."';\n";
	echo "set_button_status($button_status, '".$_SESSION['page_permission']."', 'fnc_actual_cost_entry',1,1);\n";  
	
	exit();
}

if($action=="show_po_listview")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$cost_head=$data[1];
	$buyer_id=trim($data[6]);
	$style_no=trim($data[7]);
	$job_no=trim($data[8]);
	$order_no=trim($data[9]);
	
	if($db_type==0)
	{
		$applying_period_date=change_date_format(trim($data[2]), "yyyy-mm-dd", "-");
		$applying_period_to_date=change_date_format(trim($data[3]), "yyyy-mm-dd", "-");
		$incurred_date=change_date_format(trim($data[4]), "yyyy-mm-dd", "-");
		$incurred_date_to=change_date_format(trim($data[5]), "yyyy-mm-dd", "-");
	}
	else
	{
		$applying_period_date=change_date_format(trim($data[2]),'','',1);
		$applying_period_to_date=change_date_format(trim($data[3]),'','',1);
		$incurred_date=change_date_format(trim($data[4]),'','',1);
		$incurred_date_to=change_date_format(trim($data[5]),'','',1);
	}
	
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$sql_prev_data="select id, po_id, amount from wo_actual_cost_entry where company_id='$company_id' and cost_head='$cost_head' and incurred_date='$incurred_date' and incurred_date_to='$incurred_date_to' and status_active=1 and is_deleted=0";
	$sql_prev_data_result=sql_select($sql_prev_data);
	foreach($sql_prev_data_result as $row)
	{
		$amnt_arr[$row[csf("po_id")]]=$row[csf("amount")];
		$prev_id_arr[$row[csf("po_id")]]=$row[csf("id")];
	}
	unset($sql_prev_data_result);
	//echo "<pre>";print_r($amnt_arr);echo "<pre>";print_r($prev_id_arr);die;
	//$amnt_arr=return_library_array( "select po_id, amount from wo_actual_cost_entry where company_id='$company_id' and cost_head='$cost_head' and incurred_date='$incurred_date' and incurred_date_to='$incurred_date_to' and status_active=1 and is_deleted=0",'po_id','amount');
	?>
    <table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
        <thead>
            <th width="80">Buyer</th>
            <th width="120">
            <?
			echo create_drop_down('cbo_buyer_id', 130,"select a.id, a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and a.status_active=1 and a.is_deleted=0 and b.tag_company=$company_id order by buyer_name","id,buyer_name", 1, '-- Select Buyer --',$buyer_id, '');
			?>
            </th>
            <th width="80">Style</th>
            <th width="120"> <input type="text" style="width:100px" class="text_boxes" name="txt_style" id="txt_style" placeholder="Write" value="<? echo $style_no; ?>"/></th>
            <th width="80">Job</th> 
            <th width="120"> <input type="text" style="width:100px" class="text_boxes" name="txt_job" id="txt_job" placeholder="Write"  value="<? echo $job_no; ?>"/></th>
            <th width="80">Order</th>
            <th width="120"> <input type="text" style="width:100px" class="text_boxes" name="txt_order" id="txt_order" placeholder="Write"  value="<? echo $order_no; ?>"/></th>
            <th><input type="button" name="search" id="search" value="Show" onClick="generate_report(1)" style="width:70px" class="formbutton"/></th>
        </thead>
    </table>
    <table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
        <thead>
            <th colspan="8" width="">&nbsp;</th>
            <th align="right" width="160" style="color:#F00">Remaining Amount :</th>
            <th id="tot_remain" width="110" style="color:#F00">0</th>
        </thead>
    </table>            
    <table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
        <thead>
            <th width="30">SL</th>
            <th width="100">Buyer Name</th>
            <th width="80">Order Status</th>
            <th width="100">PO Number</th>
            <th width="90">Job Number</th>
            <th width="100">Style Name</th>
            <th width="120">Item Name</th>
            <th width="80">Shipment Date</th>
            <th width="80">Order Quantity</th>
            <th>Amount(TK.)</th>
        </thead>
    </table>
    <div style="width:900px; overflow-y:scroll; max-height:250px;" id="search_div">
    	<table cellspacing="0" width="880" class="rpt_table" border="1" rules="all" id="table_body">
        <tbody> 
		<?
		$select_field='';
		if($cost_head==1) $select_field='lab_test';
		else if($cost_head==2) $select_field='freight';
		else if($cost_head==3) $select_field='inspection';
		else $select_field='currier_pre_cost';
		$fabriccostDataArray=sql_select("select job_no, $select_field from wo_pre_cost_dtls where status_active=1 and is_deleted=0");
		foreach($fabriccostDataArray as $fabRow)
		{
			 $fabriccostArray[$fabRow[csf('job_no')]]=$fabRow[csf($select_field)];
		}
		
        $sql_cond="";
		if($buyer_id>0)  $sql_cond.=" and a.buyer_name=$buyer_id";
		if($style_no!="")  $sql_cond.=" and a.style_ref_no='$style_no'";
		if($job_no!="")  $sql_cond.=" and a.job_no='$job_no'";
		if($order_no!="")  $sql_cond.=" and b.po_number='$order_no'";
		$sql="select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.total_set_qnty, b.id as po_id, b.po_number, b.po_quantity, b.is_confirmed, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_id' and b.pub_shipment_date between '$applying_period_date' and '$applying_period_to_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond order by b.pub_shipment_date, a.id";
		//echo $sql;
		$result=sql_select($sql);
        $i=1; $tot_po_qty=0; $tot_amount=0;
        foreach($result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$po_qty=$row[csf("total_set_qnty")]*$row[csf("po_quantity")];
			$tot_po_qty+=$po_qty; 
			$amount=$amnt_arr[$row[csf("po_id")]];
			$prev_dtlsid=$prev_id_arr[$row[csf("po_id")]];
			$tot_amount+=$amount;
			
			if($fabriccostArray[$row[csf('job_no')]]>0) {$bgcolor="yellow";}
			?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="100"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                <td width="80"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td>
              	<td width="100"><p><? echo $row[csf("po_number")]; ?></p>
                    <input type="hidden" name="po_id_<? echo $i; ?>" id="po_id_<? echo $i; ?>" value="<? echo $row[csf("po_id")]; ?>">
                </td>
                <td width="90" id="job_no_<? echo $i; ?>"><p><? echo $row[csf("job_no")] ?></p></td>
                <td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                <td width="120">
                    <p>
						<? 
							$gmts_item='';
							$gmts_item_id=explode(",",$row[csf("gmts_item_id")]);
							foreach($gmts_item_id as $item_id)
							{
								$gmts_item.=$garments_item[$item_id].",";
							}
							$gmts_item=substr($gmts_item,0,-1); 
                        	echo $gmts_item; 
                        ?>
                    </p>
                </td>
                <td width="80" align="center"><p><? echo change_date_format($row[csf("shipment_date")]); ?></p></td>
                <td width="80" align="right"><? echo $po_qty; ?></td>
                <td align="center">
                    <input type="text" name="txt_amount[]" id="txt_amount_<? echo $i; ?>" style="width:70px;" class="text_boxes_numeric" value="<? echo $amount; ?>" onkeyup="calculate_balance(<? echo $i; ?>);" title="<? echo $po_qty; ?>">
                    <input type="hidden" name="txt_dtls_id[]" id="txt_dtls_id_<? echo $i; ?>" value="<? echo $prev_dtlsid; ?>" />
                </td>
            </tr>
        <?	
            $i++;
		}
		?>
        </tbody>
		</table>
    </div>
    <table cellspacing="0" width="900" class="rpt_table" border="1" rules="all">
        <tfoot>	 
           <th colspan="8">Total</th>
           <th align="right" width="80" id="td_tot_po_qnty"><? echo $tot_po_qty; ?></th>
           <th align="center" width="90" style="padding-right:20px;"><input type="text" name="tot_amount" id="tot_amount" style="width:70px;" class="text_boxes_numeric" value="<? echo $tot_amount; ?>" readonly="readonly"></th>	
         </tfoot>	
    </table>
<?
	exit();
}

if($action=="show_details_listview")
{
	if($data=='0') $data='';
?>
    <table cellspacing="0" width="750" class="rpt_table" border="1" rules="all">
        <thead>
            <th width="50">SL</th>
            <th width="70">Company</th>
            <th width="90">Cost Head</th>
            <th width="90">Based On</th>
            <th width="90">Incurred Date</th>
            <th width="90">Incurred Date To</th>
            <th width="90">Period From</th>
            <th width="90">Period To</th>
            <th>Amount(TK.)</th>
        </thead>
    </table>
    <div style="width:770px; overflow-y:scroll; max-height:250px;" id="search_div">
    	<table cellspacing="0" width="750" class="rpt_table" border="1" rules="all" id="table_body"> 
		<?
        $sql="select a.id, a.company_short_name, b.cost_head, b.incurred_date, b.incurred_date_to, b.applying_period_date, b.applying_period_to_date, b.based_on, sum(b.amount) as amount from lib_company a, wo_actual_cost_entry b where a.id=b.company_id and a.company_name like '%".$data."%' and b.cost_head in(5,6) and a.status_active=1 and a.is_deleted=0 group by a.id, a.company_short_name, b.cost_head, b.incurred_date, b.incurred_date_to, b.applying_period_date, b.applying_period_to_date, b.based_on
		order by a.id asc, b.incurred_date desc";
		//echo $sql;
		$result=sql_select($sql);
        $i=1;
        foreach($result as $row)
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data('<? echo $row[csf("id")]."_".$row[csf("cost_head")]."_".$row[csf("incurred_date")]."_".$row[csf("incurred_date_to")]; ?>', 'populate_data_from_cm_cost','requires/actual_cost_entry_controller');" id="tr_<? echo $i; ?>" style="cursor:pointer" >
            	<td width="50"><? echo $i; ?></td>
                <td width="70"><p><? echo $row[csf("company_short_name")]; ?></p></td>
                <td width="90"><? echo $actual_cost_heads[$row[csf("cost_head")]]; ?></td>
                <td width="90" align="center"><? echo $based_on[$row[csf("based_on")]]; ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf("incurred_date")]); ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf("incurred_date_to")]); ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf("applying_period_date")]); ?></td>
                <td width="90" align="center"><? echo change_date_format($row[csf("applying_period_to_date")]); ?></td>
                <td align="right"><? echo number_format($row[csf("amount")],2,'.',''); ?></td>
            </tr>
        <?	
            $i++;
		}
		?>
		</table>
    </div>
<?
	exit();
}

if($action=='populate_data_from_cm_cost')
{
	$data=explode("_",$data);
	$data_array=sql_select("select max(based_on) as based_on, max(exchange_rate) as exchange_rate, max(applying_period_date) as applying_period_date, max(applying_period_to_date) as applying_period_to_date, sum(amount) as amount from wo_actual_cost_entry where company_id='$data[0]' and cost_head='$data[1]' and incurred_date='$data[2]' and incurred_date_to='$data[3]' and status_active=1 and is_deleted=0");
	
	echo "document.getElementById('cbo_company_id').value 					= '".$data[0]."';\n";
	echo "$('#cbo_company_id').attr('disabled','true')".";\n";
	echo "document.getElementById('cbo_cost_head').value 					= '".$data[1]."';\n";
	echo "document.getElementById('cbo_based_on').value 					= '".$data_array[0][csf("based_on")]."';\n";
	echo "document.getElementById('txt_exchange_rate_order').value 			= '".$data_array[0][csf("exchange_rate")]."';\n";
	echo "document.getElementById('txt_amount').value 						= '".number_format($data_array[0][csf("amount")],2,'.','')."';\n";
	echo "document.getElementById('txt_incurred_date').value 				= '".change_date_format($data[2])."';\n";
	echo "document.getElementById('txt_incurred_to_date').value 			= '".change_date_format($data[3])."';\n";
	echo "document.getElementById('txt_applying_period_date').value 		= '".change_date_format($data_array[0][csf("applying_period_date")])."';\n";
	echo "document.getElementById('txt_applying_period_to_date').value 		= '".change_date_format($data_array[0][csf("applying_period_to_date")])."';\n";
	
	echo "$('#cm_commercial_list_view_details').html('')".";\n";
	echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_actual_cost_entry',1,1);\n";  
	
	exit();
}

if($action=="show_details_listview_po")
{
	$data=explode("**",$data);
	$company_id=$data[0];
	$cost_head=$data[1];
	$cbo_based_on=$data[2];
	
	if($db_type==0)
	{
		$incurred_date=change_date_format(trim($data[3]), "yyyy-mm-dd", "-");
		$incurred_date_to=change_date_format(trim($data[4]), "yyyy-mm-dd", "-");
	}
	else
	{
		$incurred_date=change_date_format(trim($data[3]),'','',1);
		$incurred_date_to=change_date_format(trim($data[4]),'','',1);
	}
	
	$sql="select a.job_no, a.buyer_name, a.style_ref_no, a.total_set_qnty, a.order_uom, a.set_break_down, b.po_number, b.po_quantity, b.is_confirmed, b.pub_shipment_date as shipment_date, c.amount, c.gmts_item_id, c.production_qty, c.item_smv, c.smv_produced, c.based_on 
	from wo_po_details_master a, wo_po_break_down b, wo_actual_cost_entry c 
	where a.job_no=b.job_no_mst and b.id=c.po_id and c.company_id='$company_id' and c.cost_head='$cost_head' and c.incurred_date='$incurred_date' and c.incurred_date_to='$incurred_date_to' and c.status_active=1 and c.is_deleted=0 order by b.pub_shipment_date, a.id";
	$result=sql_select($sql);
	$cbo_based_on=$result[0][csf('based_on')];
	//echo $cbo_based_on;
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	
	if($cbo_based_on==3)
	{
	?>
        <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all">
            <thead>
                <th colspan="13" width=""><? echo $actual_cost_heads[$cost_head]; ?> Cost Details</th>
            </thead>
        </table>            
        <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="60">Buyer Name</th>
                <th width="60">Order Status</th>
                <th width="80">PO Number</th>
                <th width="90">Job Number</th>
                <th width="100">Style Name</th>
                <th width="110">Item Name</th>
                <th width="80">Shipment Date</th>
                <th width="80">Order Qty.</th>
                <th width="50">SMV</th>
                <th width="80">Sewing Qty.</th>
                <th width="80">Produce Minute</th>
                <th>Amount(TK.)</th>
            </thead>
        </table>
        <div style="width:1020px; overflow-y:scroll; max-height:250px;" id="search_div">
            <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all" id="table_body"> 
            <?
            $i=1; $tot_po_qty=0; $tot_amount=0;
            foreach($result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                
				if($row[csf("order_uom")]==1)
				{
                	$po_qty=$row[csf("po_quantity")];
				}
				else
				{
					$ratio=1;
					$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
					foreach($exp_grmts_item as $value)
					{
						$grmts_item_qty = explode("_",$value);
						if($row[csf('gmts_item_id')]==$grmts_item_qty[0])
						{
							$ratio=$grmts_item_qty[1];
							break;
						}
					}
					$po_qty=$row[csf("po_quantity")]*$ratio;	
				}
				
                $tot_po_qty+=$po_qty; 
                
                $qty=$row[csf("production_qty")];
                $item_smv=$row[csf("item_smv")];
                $produce_min=$row[csf("smv_produced")];
                $amount=$row[csf("amount")];
                
                $tot_prod_qty+=$qty;
                $tot_produce_min+=$produce_min;
                $tot_amount+=$amount;
                
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="60"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                    <td width="60"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td>
                    <td width="80"><p><? echo $row[csf("po_number")]; ?></p></td>
                    <td width="90" id="job_no_<? echo $i; ?>"><p><? echo $row[csf("job_no")] ?></p></td>
                    <td width="100"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                    <td width="110"><p><?  echo $garments_item[$row[csf("gmts_item_id")]]; ?></p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf("shipment_date")]); ?></p></td>
                    <td width="80" align="right"><p><? echo $po_qty; ?></p></td>
                    <td width="50" align="right"><p><? echo $item_smv; ?></p></td>
                    <td width="80" align="right"><p><? echo $qty; ?></p></td>
                    <td width="80" align="right"><p><? echo $produce_min; ?></p></td>
                    <td align="right"><? echo number_format($amount,2,'.',''); ?></td>
                </tr>
            <?	
                $i++;
            }
            ?>
            </table>
        </div>
        <table cellspacing="0" width="1020" class="rpt_table" border="1" rules="all">
            <tfoot>	 
               <th colspan="8">Total</th>
               <th align="right" width="80"><? echo $tot_po_qty; ?></th>
               <th align="right" width="50">&nbsp;</th>
               <th align="right" width="80"><? echo $tot_prod_qty; ?></th>
               <th align="right" width="80"><? echo number_format($tot_produce_min,2,'.',''); ?></th>
               <th align="right" width="106"><? echo number_format($tot_amount,2,'.',''); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>	
             </tfoot>	
        </table>
	<?
	}
	else
	{
	?>
		<table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all">
            <thead>
                <th colspan="13" width=""><? echo $actual_cost_heads[$cost_head]; ?> Cost Details</th>
            </thead>
        </table>            
        <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all">
            <thead>
                <th width="30">SL</th>
                <th width="70">Buyer Name</th>
                <th width="70">Order Status</th>
                <th width="100">PO Number</th>
                <th width="90">Job Number</th>
                <th width="110">Style Name</th>
                <th width="120">Item Name</th>
                <th width="80">Shipment Date</th>
                <th width="100">Order Qty.</th>
                <th width="100"><? if($cbo_based_on==1) echo  "Ex-Factory"; else echo "Sewing"; ?> Qty.</th>
                <th>Amount(TK.)</th>
            </thead>
        </table>
        <div style="width:1020px; overflow-y:scroll; max-height:250px;" id="search_div">
            <table cellspacing="0" width="1000" class="rpt_table" border="1" rules="all" id="table_body"> 
            <?
            $i=1; $tot_po_qty=0; $tot_amount=0;
            foreach($result as $row)
            {
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                
				if($row[csf("order_uom")]==1)
				{
                	$po_qty=$row[csf("po_quantity")];
				}
				else
				{
					$ratio=1;
					$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
					foreach($exp_grmts_item as $value)
					{
						$grmts_item_qty = explode("_",$value);
						if($row[csf('gmts_item_id')]==$grmts_item_qty[0])
						{
							$ratio=$grmts_item_qty[1];
							break;
						}
					}
					$po_qty=$row[csf("po_quantity")]*$ratio;	
				}
                $tot_po_qty+=$po_qty; 
                
                $qty=$row[csf("production_qty")];
                $amount=$row[csf("amount")];
                
                $tot_prod_qty+=$qty;
                $tot_amount+=$amount;
                
            ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1<? echo $i; ?>">
                    <td width="30"><? echo $i; ?></td>
                    <td width="70"><p><? echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                    <td width="70"><? echo $order_status[$row[csf("is_confirmed")]]; ?></td>
                    <td width="100"><p><? echo $row[csf("po_number")]; ?></p></td>
                    <td width="90" id="job_no_<? echo $i; ?>"><p><? echo $row[csf("job_no")] ?></p></td>
                    <td width="110"><p><? echo $row[csf("style_ref_no")]; ?></p></td>
                    <td width="120"><p><?  echo $garments_item[$row[csf("gmts_item_id")]]; ?></p></td>
                    <td width="80" align="center"><p><? echo change_date_format($row[csf("shipment_date")]); ?></p></td>
                    <td width="100" align="right"><p><? echo $po_qty; ?></p></td>
                    <td width="100" align="right"><p><? echo $qty; ?></p></td>
                    <td align="right"><? echo number_format($amount,2,'.',''); ?></td>
                </tr>
            <?	
                $i++;
            }
            ?>
            </table>
        </div>
        <table cellspacing="0" width="1020" class="rpt_table" border="1" rules="all">
            <tfoot>	 
               <th colspan="8">Total</th>
               <th align="right" width="100"><? echo $tot_po_qty; ?></th>
               <th align="right" width="100"><? echo $tot_prod_qty; ?></th>
               <th align="right" width="138"><? echo number_format($tot_amount,2,'.',''); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>	
             </tfoot>	
        </table>
	<?	
	}
	exit();
}
?>