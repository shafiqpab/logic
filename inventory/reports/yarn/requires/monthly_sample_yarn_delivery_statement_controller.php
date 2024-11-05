<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
 
if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}

if ($action == "eval_multi_select") {
	echo "set_multiselect('cbo_buyer_name','0','0','','0');\n";
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$supplier_arr=return_library_array( "select id, short_name from lib_supplier",'id','short_name');

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name = str_replace("'","",$cbo_buyer_name);
	$cbo_order_type = str_replace("'","",$cbo_order_type);

	$str_cond="";

	$issue_order_cond=" and a.issue_purpose in (4,8)";

	if($cbo_order_type==2)
	{
		$str_cond =" and a.booking_without_order =1";
		$issue_order_cond=" and a.issue_purpose in (8)";
	}
	else if($cbo_order_type==1)
	{
		$str_cond =" and a.booking_without_order !=1 and a.within_group=1";
		$issue_order_cond=" and a.issue_purpose in (4)";
	} 

	if($cbo_company_name) $company_cond = " and a.company_id= $cbo_company_name"; else $company_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") 
			{
				$buyer_cond_with=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			} 
			else 
			{
				$buyer_cond_with="";
			}
		}
		else
		{
			$buyer_cond_with="";
		}
	}
	else
	{
		$buyer_cond_with =  " and a.buyer_id in ($cbo_buyer_name)";
	}

	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	
	if($db_type==0)
	{
		$date_from=change_date_format($from_date,'yyyy-mm-dd');
		$date_to=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$date_from=change_date_format($from_date,'','',1);
		$date_to=change_date_format($to_date,'','',1);
	}
	else 
	{
		$date_from="";
		$date_to="";
	}
	$date_cond="";	
	if($date_from!="" && $date_to!="") $date_cond=" and a.booking_date between '".$date_from."' and '".$date_to."'"; else $date_cond="";
	if($date_from!="" && $date_to!="") $date_cond_trans=" and b.transaction_date between '".$date_from."' and '".$date_to."'"; else $date_cond_trans="";

	$issue_id_arr = array();
	$sqlIssue = "SELECT  (b.cons_quantity) as cons_quantity, a.booking_no, a.id as issue_id,a.buyer_id,b.cons_uom,a.issue_basis, b.requisition_no
    from inv_issue_master a, inv_transaction b     
    where a.id = b.mst_id and a.issue_basis in(1,3) and b.transaction_type = 2 and b.item_category = 1
    and b.status_active = 1 and b.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and a.entry_form = 3 $company_cond $buyer_cond_with $date_cond_trans $issue_order_cond";
    //echo $sqlIssue;
    $issue_res = sql_select($sqlIssue);
	$req_no_chk = array();
	$reqNoArr = array();
	$booking_arr = array();
	$booking_no_chk = array();
	foreach($issue_res as $row)
	{
		
		if($row[csf("issue_basis")]==1)
		{
			if($booking_no_chk[$row[csf("booking_no")]] == "")
			{
				$booking_no_chk[$row[csf("booking_no")]] = $row[csf("booking_no")];
				array_push($booking_arr,$row[csf("booking_no")]);
			}
		}
		else
		{
			if($req_no_chk[$row[csf("requisition_no")]] == "")
			{
				$req_no_chk[$row[csf("requisition_no")]] = $row[csf("requisition_no")];
				array_push($reqNoArr,$row[csf("requisition_no")]);
			}
		}
		
		$data_array[$row[csf("buyer_id")]][$row[csf("cons_uom")]]["issue_qnty"] +=  $row[csf("cons_quantity")];
		//$issue_id_arr[$row[csf("issue_id")]] =  $row[csf("issue_id")];
		$issue_buyer[$row[csf("issue_id")]] =  $row[csf("buyer_id")];
	}
	//var_dump($reqNoArr);
	
	
	$sql_issue_return = "SELECT a.booking_no,b.cons_quantity, a.issue_id,b.cons_uom,a.receive_basis, a.requisition_no from inv_receive_master a, inv_transaction b where a.id = b.mst_id and a.entry_form = 9 and b.transaction_type = 4  and a.receive_basis in(1,3) and b.item_category = 1 and b.status_active = 1 and b.is_deleted=0 and a.status_active = 1 and a.is_deleted = 0 $company_cond $date_cond_trans $str_cond ";//$issue_id_cond
	//echo $sql_issue_return;
	$issue_return_res = sql_select($sql_issue_return);
	$tot_rows=0;
	foreach ($issue_return_res as $val) 
	{
		$issue_id_arr[$val[csf("issue_id")]] =  $val[csf("issue_id")];
		$tot_rows++;
	}
	$issue_id_arr = array_filter($issue_id_arr);
	$issueIds = implode(",", $issue_id_arr);

	$issue_id_cond = '';
	if ($issueIds != '')
	{
		$issueIds = rtrim($issueIds,',');
		if($db_type==2 && $tot_rows>1000)
		{
			$issue_id_cond = ' and (';	
			$issueIdArr = array_chunk(explode(',',$issueIds),999);
			foreach($issueIdArr as $ids)
			{
				$ids = implode(',',$ids);
				$issue_id_cond .= " id in($ids) or ";
			}
			$issue_id_cond = rtrim($issue_id_cond,'or ');
			$issue_id_cond .= ')';
		}
		else
		{
			$issue_id_cond = " and id in ($issueIds)";
		}
	}
	//echo $issue_id_cond;
	$buyer_conds=str_replace("a.buyer_id", "buyer_id", $buyer_cond_with);
	$issueBuyerArray = return_library_array("select id, buyer_id from inv_issue_master where buyer_id>0 $issue_id_cond $buyer_conds","id","buyer_id");
	
	//=====================================
	foreach ($issue_return_res as $val) 
	{
		//$issue_return_arr[$issue_buyer[$val[csf("issue_id")]]][$val[csf("cons_uom")]] += $val[csf("cons_quantity")];
		// $issue_return_arr[$issue_buyer[$val[csf("issue_id")]]] += $val[csf("cons_quantity")];
		if($val[csf("receive_basis")]==1)
		{
			if($booking_no_chk[$val[csf("booking_no")]] == "")
			{
				$booking_no_chk[$val[csf("booking_no")]] = $val[csf("booking_no")];
				array_push($booking_arr,$val[csf("booking_no")]);
			}
		}
		else
		{
			if($req_no_chk[$val[csf("requisition_no")]] == "")
			{
				$req_no_chk[$val[csf("requisition_no")]] = $val[csf("requisition_no")];
				array_push($reqNoArr,$val[csf("requisition_no")]);
			}
		}
		
		if(isset($issueBuyerArray[$val[csf("issue_id")]]))
		{
			$data_array[$issueBuyerArray[$val[csf("issue_id")]]][$val[csf("cons_uom")]]["return_qnty"] +=  $val[csf("cons_quantity")];
		}
		// $issue_id_arr[$row[csf("issue_id")]] =  $row[csf("issue_id")];
		// $issue_buyer[$row[csf("issue_id")]] =  $row[csf("buyer_id")];
		// echo $issueBuyerArray[$val[csf("issue_id")]]."<br>";
	}
	//var_dump($reqNoArr);

	$data_array = array_filter($data_array);
	// print_r($data_array);
	if(count($data_array)==0)
	{
		echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Report data not found!.</div>';die;
	}

	$booking_arr = array_filter($booking_arr);

	/* $booking_nos = "'".implode("','",$booking_arr)."'";
	if($booking_nos=="") $booking_nos=0;
	$bookCond=$booking_nos_cond="";
	if(count($booking_nos)>999 && $db_type==2)
	{
		$booking_arr_chunk = array_chunk($booking_arr);
		foreach ($booking_arr_chunk as $chunk_val) 
		{
			$bookCond .= " a.booking_no in (".implode(",",$chunk_val)." or ";

		}
		$booking_nos_cond .= " and (".chop($bookCond,'or ').")";
	}
	else
	{
		$booking_nos_cond .= " and a.booking_no in ($booking_nos)";
	} */
	
	$reqNoArr = array_filter($reqNoArr);
	if(!empty($reqNoArr))
	{
		$sql = "SELECT a.booking_no FROM ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, ppl_yarn_requisition_entry d WHERE a.id = b.mst_id and b.id=c.dtls_id and b.id=d.knit_id and a.status_active = 1 AND a.is_deleted = 0 AND a.is_sales = 2 AND a.company_id = ".$cbo_company_name." AND b.status_active = 1 AND b.is_deleted = 0 AND b.is_sales = 2 AND c.is_deleted = 0 AND c.status_active = 1 AND c.is_sales = 2 ".where_con_using_array($reqNoArr,0,'d.requisition_no')."";
		//echo $sql;
		$req_data = sql_select($sql);
		
		foreach($req_data as $row)
		{
			if($booking_no_chk[$row[csf("booking_no")]] == "")
			{
				$booking_no_chk[$row[csf("booking_no")]] = $row[csf("booking_no")];
				array_push($booking_arr,$row[csf("booking_no")]);
			}
		}
	}


	if($cbo_order_type==2)
	{
		$booking_sql = sql_select("SELECT  a.buyer_id,a.booking_no, b.grey_fabric as wo_qnty, b.uom from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no  = b.booking_no and a.booking_type =4 and b.status_active =1 and b.is_deleted = 0 and a.status_active =1 and a.is_deleted = 0 $company_cond $buyer_cond_with ".where_con_using_array($booking_arr,1,'a.booking_no')."");
	}
	else if($cbo_order_type==1)
	{
		$booking_sql = sql_select("SELECT a.buyer_id,a.booking_no, b.wo_qnty, b.uom from wo_booking_mst a, wo_booking_dtls b where a.booking_no = b.booking_no and a.booking_type = 4 and b.status_active =1 and b.is_deleted = 0 and a.status_active =1 and a.is_deleted = 0 $company_cond $buyer_cond_with ".where_con_using_array($booking_arr,1,'a.booking_no')."");
	}
	else
	{
		$booking_sql = sql_select("SELECT a.buyer_id,a.booking_no, b.wo_qnty, b.uom from wo_booking_mst a, wo_booking_dtls b where a.booking_no = b.booking_no and a.booking_type = 4 and b.status_active =1 and b.is_deleted = 0 and a.status_active =1 and a.is_deleted = 0 $company_cond $buyer_cond_with ".where_con_using_array($booking_arr,1,'a.booking_no')."
		union all 
		select  a.buyer_id,a.booking_no, b.grey_fabric as wo_qnty, b.uom from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no  = b.booking_no and a.booking_type =4 and b.status_active =1 and b.is_deleted = 0 and a.status_active =1 and a.is_deleted = 0 $company_cond $buyer_cond_with ".where_con_using_array($booking_arr,1,'a.booking_no')."");
	}
	
	


	foreach ($booking_sql as $value) 
	{
		//$booking_qnty_arr[$value[csf("buyer_id")]][$value[csf("uom")]] += $value[csf("wo_qnty")];
		$booking_qnty_arr[$value[csf("buyer_id")]] += $value[csf("wo_qnty")];
	}

	ob_start();
	?>

	<fieldset style="width:900px;">
		<table cellpadding="0" cellspacing="0" width="900">
			<tr>
			   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
			</tr>
			<tr>
			   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr>
			   <td align="center" width="100%" colspan="9" style="font-size:16px"><strong>Month Of <? echo date("F",strtotime($date_from))." - ".date("Y",strtotime($date_from)); ?></strong></td>
			</tr>
		</table>
		<table width="900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<thead>
				<tr>
					<th width="40">SL</th>
					<th width="120">Buyer Name</th>
					<th width="80">Unit</th>
					<th width="100">Booking Qnty</th>
					<th width="100">Delivery Qnty</th>
					<th width="100">Return</th>
					<th width="100">Actual Delivery</th>
					<th width="100">Short/Excess</th>
                    <th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:918px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="900" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">  
				<tbody>  
				<?

				$i=1;
				foreach($data_array as $buyer_id=>$buyer_data)
				{
					foreach ($buyer_data as $uom => $row) 
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						// $actual_delivery = $row["issue_qnty"] - $issue_return_arr[$buyer_id];//[$uom];
						$actual_delivery = $row["issue_qnty"] - $row["return_qnty"];//[$uom];
						$short_excess = $booking_qnty_arr[$buyer_id]- $actual_delivery;
						?>

	                    <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
	                        <td width="40" align="center"><? echo $i;?></td>
	                        <td width="120" align="center"><p><? echo $buyer_arr[$buyer_id];?></p></td>
	                        <td width="80" align="center"><p><? echo $unit_of_measurement[12];//$unit_of_measurement[$uom];?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($booking_qnty_arr[$buyer_id],2,".","");?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($row["issue_qnty"],2,".","");?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($row["return_qnty"],2,".","");?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($actual_delivery,2,".","");?></p></td>
	                        <td width="100" align="right"><p><? echo number_format($short_excess,2,".","");?></p></td>
	                        <td><p>&nbsp;</p></td>
	                    </tr>
	                    <?
	                    $i++;
	                    $total_booking_qnty += $booking_qnty_arr[$buyer_id];
	                    $total_delivery_qnty += $row["issue_qnty"];
	                    //$total_return_qnty += $issue_return_arr[$buyer_id];
	                    $total_return_qnty += $row["return_qnty"];
	                    $total_actual_qnty += $actual_delivery;
	                    $total_short_excess_qnty += $short_excess;
                	}
				}
				
				?>
				</tbody>
				<tfoot>
					<th colspan="3" align="right">Total</th>
					<th align="right"><? echo number_format($total_booking_qnty,2,".","");?></th>
					<th align="right"><? echo number_format($total_delivery_qnty,2,".","");?></th>
					<th align="right"><? echo number_format($total_return_qnty,2,".","");?></th>
					<th align="right"><? echo number_format($total_actual_qnty,2,".","");?></th>
					<th align="right"><? echo number_format($total_short_excess_qnty,2,".","");?></th>
					<th align="right">&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>      
	<?	
	
    foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}

?>
