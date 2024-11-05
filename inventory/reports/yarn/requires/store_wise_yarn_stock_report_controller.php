<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');
$user_name=$_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
//--------------------------------------------------------------------------------------------

//load drop down supplier
if ($action == "load_drop_down_supplier") {
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "eval_multi_select") {
	echo "set_multiselect('cbo_supplier','0','0','','0');\n";
	exit();
}

if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$type=str_replace("'","",$type);
	$value_with=str_replace("'","",$value_with);
	//echo $value_with.test;die;

	$companyArr 	= return_library_array("select id,company_name from lib_company where status_active=1", "id", "company_name");
	$supplierArr 	= return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	$company_cond = ($cbo_company_name==0)?"":" and a.company_id=$cbo_company_name";
	$importer_cond = ($cbo_company_name==0)?"":" and a.importer_id=$cbo_company_name";
	$search_cond .= ($txt_lot_no!="")?" and a.lot='$txt_lot_no'":"";
	$search_cond .= ($cbo_supplier!="")?" and a.supplier_id in($cbo_supplier)":"";
	$search_cond .= ($cbo_dyed_type==0)?"":(($cbo_dyed_type==1)?" and a.dyed_type=$cbo_dyed_type":" and a.dyed_type<>1");
	$search_cond .= ($txt_composition_id!="")?" and (a.yarn_comp_type1st in($txt_composition_id) or a.yarn_comp_type2nd in($txt_composition_id))":"";
	$search_cond .= ($txt_count!=0)?" and a.yarn_count_id in($txt_count)":"";
	$search_cond .= ($cbo_yarn_type!=0)?" and a.yarn_type in($cbo_yarn_type)":"";
	//$search_cond .= ($value_with!=0)?" and a.current_stock>0":"";

	if ($db_type == 0) {
		$from_date = change_date_format($from_date, 'yyyy-mm-dd');
	} else if ($db_type == 2) {
		$from_date = change_date_format($from_date, '', '', 1);
	} else {
		$from_date = "";
	}

	if ($from_date != "")
		$date_cond = " and a.transaction_date<='$from_date'";

	
	//echo $num_of_store;
	if($type==1)
	{
		$stores = sql_select("select a.company_id, c.company_name, c.company_short_name, a.id, a.store_name 
		from lib_store_location a, lib_store_location_category b, lib_company c 
		where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(1) $company_cond 
		order by a.company_id, a.store_name asc");
		$num_of_store=0;
		foreach ($stores as $store)
		{
			$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
			$company_id_arr[$store[csf("company_id")]]["company_colspan"]++;
			$num_of_store++;
		}
		
		$sql_receive = "Select a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, b.store_id,b.pi_wo_batch_no,
		sum(case when b.transaction_type in (1,4,5) and b.transaction_date<='$from_date' then b.cons_quantity else 0 end) as rcv_total,
		sum(case when b.transaction_type in (1,4,5) and b.transaction_date<='$from_date' then b.cons_amount else 0 end) as rcv_total_amt,
		sum(case when b.transaction_type in (2,3,6) and b.transaction_date<='$from_date' then b.cons_quantity else 0 end) as issue_total,
		sum(case when b.transaction_type in (2,3,6) and b.transaction_date<='$from_date' then b.cons_amount else 0 end) as issue_total_amt
		from product_details_master a, inv_transaction b
		where a.id=b.prod_id and b.transaction_type in (1,2,3,4,5,6) and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $search_cond and b.cons_quantity>0
		group by a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot, b.store_id,b.pi_wo_batch_no";

		//echo $sql_receive;die;
		$result_sql_receive = sql_select($sql_receive);
		$piWoBatchNoChk = array();
		$piWoBatchNoArr = array();
		foreach ($result_sql_receive as $row) {
			$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")];
			$receive_array[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$row[csf("store_id")]]['rcv_total'] += $row[csf("rcv_total")];
			$receive_array[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$row[csf("store_id")]]['rcv_total_amt'] += $row[csf("rcv_total_amt")];

			$issue_array[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$row[csf("store_id")]]['issue_total'] += $row[csf("issue_total")];
			$issue_array[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$row[csf("store_id")]]['issue_total_amt'] += $row[csf("issue_total_amt")];
			
			$stock_arr[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]['stock_qnty']+=$row[csf("rcv_total")]-$row[csf("issue_total")];
			$stock_arr[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]['stock_amt']+=$row[csf("rcv_total_amt")]-$row[csf("issue_total_amt")];
			
			$prod_data[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]["yarn_type"]=$row[csf("yarn_type")];

			$receive_pi_array[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]['pi_wo_batch_no'] = $row[csf("pi_wo_batch_no")];

			if($row[csf('pi_wo_batch_no')] > 0)
			{
				if($piWoBatchNoChk[$row[csf('pi_wo_batch_no')]] == "")
				{
					$piWoBatchNoChk[$row[csf('pi_wo_batch_no')]] = $row[csf('pi_wo_batch_no')];
					array_push($piWoBatchNoArr,$row[csf('pi_wo_batch_no')]);
				}
			}
				
		}
	
		unset($result_sql_receive);
		//echo "<pre>";print_r($piWoBatchNoArr); echo "<pre>";die;


		$sql_lc = "SELECT a.id, b.pi_id, a.lc_number, a.supplier_id, a.application_date, a.last_shipment_date, a.lc_date, a.lc_value, c.item_category_id, a.importer_id, a.ref_closing_status, c.pi_number
		FROM com_btb_lc_master_details a, com_btb_lc_pi b, com_pi_master_details c
		WHERE a.id=b.com_btb_lc_master_details_id and b.pi_id=c.id $importer_cond and a.is_deleted = 0 and a.item_basis_id=1 ".where_con_using_array($piWoBatchNoArr,0,'b.pi_id')." ";
		//echo $sql_lc;die;
		$lc_result = sql_select($sql_lc);
		$lcInfoArr = array();
		foreach ($lc_result as $row)
		{
			$lcInfoArr[$row[csf("pi_id")]]['lc_number']= $row[csf('lc_number')];
			$lcInfoArr[$row[csf("pi_id")]]['pi_number']= $row[csf('pi_number')];
		}
		unset($lc_result);
		//echo "<pre>";print_r($lcInfoArr); echo "<pre>";die;

		$width = (1200+($num_of_store*100));
		ob_start();
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;" id="fieldsetId">
			<table width="<? echo $width+20; ?>" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" >Store Wise Yarn Stock </td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none; font-size:14px;">
							<? echo ($cbo_company_name==0)?"All Company":"Company Name : ".$companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($txt_date_from != "" && $txt_date_from != "") echo "From " . change_date_format($txt_date_from, 'dd-mm-yyyy') . " To " . change_date_format($txt_date_from, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th colspan="8">Description</th>
						<?
						$company_span=0;
						foreach ($company_id_arr as $company_id=>$company_row) {
							$company_span = $company_id_arr[$company_id]["company_colspan"];
							?>
							<th colspan="<? echo $company_span;?>"><? echo $company_row['company'];?></th>
							<?
						}
						?>
						<th rowspan="2" width="100">Group Total Qnty.</th>
						<th rowspan="2">Group Total Value</th>
					</tr>
					<tr>
						<th width="70">Count</th>
						<th width="180">Composition</th>
						<th width="80">Yarn Type</th>
						<th width="100">Color</th>
						<th width="100">PI No</th>
						<th width="100">BTB No</th>
						<th width="80">Lot</th>
						<th width="150">Supplier</th>
						<?
						foreach ($stores as $store) {
							?>
							<th width="100" style="word-break:break-all;"><? echo $store[csf("store_name")];?></th>
							<?
						}
						?>
					</tr>
				</thead>
			</table>
			<div style="max-height:425px; overflow-y:auto; width:<? echo $width+20; ?>px;" id="scroll_body">
				<table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_body">
					<tbody>
						<?
						$sql = "select a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type, a.color, a.lot 
						from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond 
						group by a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_type, a.color, a.lot";
						//echo $sql;die;
						$result = sql_select($sql);
						$prod_data=array();
						foreach($result as $row)
						{
							$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")];
							if( $value_with==0 ) $stock_arr[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]['stock_qnty']=1;
							if(number_format($stock_arr[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]['stock_qnty'],2,'.','') >0 )
							{
								$prod_data[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]["supplier_id"]=$row[csf("supplier_id")];
								$prod_data[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]["yarn_count_id"]=$row[csf("yarn_count_id")];
								$prod_data[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]["compositionDetails"]=$compositionDetails;
								
								$prod_data[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]["yarn_type"]=$row[csf("yarn_type")];
								$prod_data[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]["color"]=$row[csf("color")];
								$prod_data[$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$compositionDetails][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]]["lot"]=$row[csf("lot")];
							}
							
							
						}
						
						//echo "<pre>";print_r($prod_data);die;
						$i = 1;
						$store_wise_recv_total=array();
						$store_wise_balance_total=$store_wise_amt_balance_total=0;
						foreach($prod_data as $sup_id=>$sup_data)
						{
							foreach($sup_data as $count_id=>$count_data)
							{
								foreach($count_data as $comp_dtls=>$comp_data)
								{
									foreach($comp_data as $y_type=>$type_data)
									{
										foreach($type_data as $color_id=>$color_data)
										{
											foreach($color_data as $lot_no=>$row)
											{
												$group_sub_total_cond=0;
												foreach ($stores as $store) 
												{
													$total_receive = $receive_array[$store[csf("company_id")]][$row[("supplier_id")]][$row[("yarn_count_id")]][$comp_dtls][$row[("yarn_type")]][$row[("color")]][$row[("lot")]][$store[csf("id")]]['rcv_total'];
													$total_issue = $issue_array[$store[csf("company_id")]][$row[("supplier_id")]][$row[("yarn_count_id")]][$comp_dtls][$row[("yarn_type")]][$row[("color")]][$row[("lot")]][$store[csf("id")]]['issue_total'];

													$store_wise_balance = number_format(($total_receive - $total_issue),2,".","");

													$group_sub_total_cond += $store_wise_balance;

												}

												$pi_wo_batch_no = $receive_pi_array[$sup_id][$count_id][$comp_dtls][$y_type][$color_id][$lot_no]['pi_wo_batch_no'];
												$lc_number = $lcInfoArr[$pi_wo_batch_no]['lc_number'];
												$pi_number = $lcInfoArr[$pi_wo_batch_no]['pi_number'];

												// $get_upto_qnty=5;
												// $txt_qnty=20;
												if((($get_upto_qnty == 1 && $group_sub_total_cond > $txt_qnty) || ($get_upto_qnty == 2 && $group_sub_total_cond < $txt_qnty) || ($get_upto_qnty == 3 && $group_sub_total_cond >= $txt_qnty) || ($get_upto_qnty == 4 && $group_sub_total_cond <= $txt_qnty) || ($get_upto_qnty == 5 && $group_sub_total_cond == $txt_qnty) || $get_upto_qnty == 0))
												{
													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
														<td align="center" width="40"><? echo $i;?></td>
														<td width="70" style="word-break:break-all; mso-number-format:'\@';"><p><? echo $yarn_count_arr[$row[("yarn_count_id")]]; ?>&nbsp;</p></td>
														<td width="180" style="word-break:break-all;"><p><? echo $comp_dtls; ?></p></td>
														<td width="80" style="word-break:break-all;"><p><? echo $yarn_type[$row[("yarn_type")]]; ?></p></td>
														<td width="100" style="word-break:break-all;"><p><? echo $color_name_arr[$row[("color")]]; ?></p></td>
														<td width="100" style="word-break:break-all;"><p><? echo $pi_number; ?></p></td>
														<td width="100" style="word-break:break-all;"><p><? echo $lc_number; ?></p></td>
														<td width="80" style="word-break:break-all; mso-number-format:'\@';" title="Product ID=<? echo $row[csf('prod_id')]; ?>"><p><? echo $row[("lot")]; ?>&nbsp;</p></td>
														<td width="150" style="word-break:break-all;"><? echo $supplierArr[$row[("supplier_id")]]; ?></td>
														<?
														$group_sub_total=$group_sub_total_amt=$total_receive=$total_receive_amt=$total_issue=$total_issue_amt=0;
														foreach ($stores as $store) {
															$total_receive = $receive_array[$store[csf("company_id")]][$row[("supplier_id")]][$row[("yarn_count_id")]][$comp_dtls][$row[("yarn_type")]][$row[("color")]][$row[("lot")]][$store[csf("id")]]['rcv_total'];
															$total_receive_amt = $receive_array[$store[csf("company_id")]][$row[("supplier_id")]][$row[("yarn_count_id")]][$comp_dtls][$row[("yarn_type")]][$row[("color")]][$row[("lot")]][$store[csf("id")]]['rcv_total_amt'];
															$total_issue = $issue_array[$store[csf("company_id")]][$row[("supplier_id")]][$row[("yarn_count_id")]][$comp_dtls][$row[("yarn_type")]][$row[("color")]][$row[("lot")]][$store[csf("id")]]['issue_total'];
															$total_issue_amt = $issue_array[$store[csf("company_id")]][$row[("supplier_id")]][$row[("yarn_count_id")]][$comp_dtls][$row[("yarn_type")]][$row[("color")]][$row[("lot")]][$store[csf("id")]]['issue_total_amt'];


															$store_wise_balance = number_format(($total_receive - $total_issue),2,".","");

															$store_wise_amt_balance = number_format(($total_receive_amt - $total_issue_amt),2,".","");

															?>
															<td width="100" style="word-break:break-all;" align="right" title="<? echo "Store=".$store[csf("store_name")]."\nTotal Receive=".number_format($total_receive,2,".","")."\nTotal Issue=".number_format($total_issue,2,".","");?>"><? echo $store_wise_balance;?></td>
															<?
															$group_sub_total += $store_wise_balance;
															$group_sub_total_amt += $store_wise_amt_balance;
						
															$store_wise_recv_total[$store[csf("id")]] += $store_wise_balance;
															$store_wise_balance_total += $store_wise_balance;
															$store_wise_amt_balance_total += $store_wise_amt_balance;
														}
														$group_total_qnty+=$group_sub_total;
														$group_total_amount+=$group_sub_total_amt;
														?>
														<td width="100" align="right"><? echo number_format($group_sub_total,2,".",""); ?></td>
														<td align="right"><? echo number_format($group_sub_total_amt,2,".",""); ?></td>
													</tr>
													<?
													$i++;
												}
											}
										}
									}
								}
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<input type="hidden" id="num_of_store" value="<? echo $num_of_store;?>">
			<table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_body">
				<tr>
					<td width="905" align="right"><strong>Grand Total= </strong></td>
					<?
					$t=1;
					foreach ($stores as $store)
					{
						?>
						<td width="100" align="right" id="store_total_<? echo $t;?>"><? echo number_format($store_wise_recv_total[$store[csf("id")]],2,".","");?></td>
						<?
						$t++;
					}
					?>
					<td align="right" width="100" title="<? echo $store_wise_balance_total;?>"><? echo number_format($group_total_qnty,2,".","");?></td>
					<td align="right" title="<? echo $store_wise_amt_balance_total;?>"><? echo number_format($group_total_amount,2,".","");?></td>
				</tr>
			</table>
		</fieldset>
		<?
	}
	else
	{
		$sql_receive = "Select a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot,b.store_id,
		sum(case when b.transaction_type in (1,4,5) and b.transaction_date<='$from_date' then b.cons_quantity else 0 end) as rcv_total,
		sum(case when b.transaction_type in (1,4,5) and b.transaction_date<='$from_date' then b.cons_amount else 0 end) as rcv_total_amt,
		sum(case when b.transaction_type in (2,3,6) and b.transaction_date<='$from_date' then b.cons_quantity else 0 end) as issue_total,
		sum(case when b.transaction_type in (2,3,6) and b.transaction_date<='$from_date' then b.cons_amount else 0 end) as issue_total_amt
		from product_details_master a,inv_transaction b
		where a.id=b.prod_id and b.transaction_type in (1,2,3,4,5,6) and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $search_cond and b.cons_quantity>0
		group by a.company_id, a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot,b.store_id";
		$result_sql_receive = sql_select($sql_receive);
		foreach ($result_sql_receive as $row) 
		{
			$receive_array[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$row[csf("store_id")]]['rcv_total'] = $row[csf("rcv_total")];
			$receive_array[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$row[csf("store_id")]]['rcv_total_amt'] = $row[csf("rcv_total_amt")];
			$issue_array[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$row[csf("store_id")]]['issue_total'] = $row[csf("issue_total")];
			$issue_array[$row[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$row[csf("store_id")]]['issue_total_amt'] = $row[csf("issue_total_amt")];
			if(($row[csf("rcv_total")]-$row[csf("issue_total")])>0)
			{
				$trans_com[$row[csf("company_id")]]= $row[csf("company_id")];
			}
		}
		unset($result_sql_receive);
		//print_r($trans_com);die;
		$stores = sql_select("select a.company_id, c.company_name, c.company_short_name, a.id, a.store_name 
		from lib_store_location a, lib_store_location_category b, lib_company c 
		where a.id=b.store_location_id and a.company_id=c.id and a.status_active=1 and a.is_deleted=0 and b.category_type in(1) $company_cond 
		order by a.company_id, a.store_name asc");
		$num_of_store=0;
		foreach ($stores as $store)
		{
			if($trans_com[$store[csf("company_id")]])
			{
				$company_id_arr[$store[csf("company_id")]]["company"] = $store[csf("company_name")];
				$company_id_arr[$store[csf("company_id")]]["company_colspan"]++;
				$num_of_store++;
			}
		}
		//echo "<pre>";print_r($company_id_arr);die;
		
		$width = (950+($num_of_store*100));
		ob_start();
		?>
		<fieldset style="width:<? echo $width+20; ?>px;margin:5px auto;">
			<table width="<? echo $width+20; ?>" cellspacing="0" class="rpt_table" rules="all" id="table_header_1">
				<thead>
					<tr class="form_caption" style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:16px; font-weight:bold" >Store Wise Yarn Stock </td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none; font-size:14px;">
							<? echo ($cbo_company_name==0)?"All Company":"Company Name : ".$companyArr[str_replace("'", "", $cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="16" align="center" style="border:none;font-size:12px; font-weight:bold">
							<? if ($txt_date_from != "" && $txt_date_from != "") echo "From " . change_date_format($txt_date_from, 'dd-mm-yyyy') . " To " . change_date_format($txt_date_from, 'dd-mm-yyyy') . ""; ?>
						</td>
					</tr>
					<tr>
						<th rowspan="2" width="40">SL</th>
						<th colspan="6">Description</th>
						<?
						$company_span=0;
						foreach ($company_id_arr as $company_id=>$company_row) {
							$company_span = $company_id_arr[$company_id]["company_colspan"];
							?>
							<th colspan="<? echo $company_span;?>" title="<? echo $company_span."=".$company_id;?>"><? echo $company_row['company'];?></th>
							<?
						}
						?>
						<th rowspan="2" width="100">Group Total Qnty.</th>
						<th rowspan="2">Group Total Value</th>
					</tr>
					<tr>
						<th width="80">Count</th>
						<th width="180">Composition</th>
						<th width="80">Yarn Type</th>
						<th width="100">Color</th>
						<th width="80">Lot</th>
						<th width="80">Supplier</th>
						<?
						foreach ($stores as $store) {
							if($trans_com[$store[csf("company_id")]])
							{
								?>
								<th width="100" style="word-break:break-all"><? echo $store[csf("store_name")];?></th>
								<?
							}
						}
						?>
					</tr>
				</thead>
			</table>
			<div style="max-height:425px; overflow-y:auto; width:<? echo $width+20; ?>px;" id="scroll_body">
				<table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_body">
					<tbody>
						<?
						//a.company_id, 
						$sql = "select a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot from product_details_master a where a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 $company_cond $search_cond group by a.supplier_id, a.yarn_count_id, a.yarn_comp_type1st, a.yarn_comp_percent1st, a.yarn_comp_type2nd, a.yarn_comp_percent2nd, a.yarn_type, a.color, a.lot";
						//echo $sql;//die;
						$result = sql_select($sql);
	
						if(!empty($result))
						{
							$i = 1;
							$store_wise_recv_total=array();
							$store_wise_balance_total=$store_wise_amt_balance_total=0;
							foreach ($result as $row) 
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
								if ($row[csf("yarn_comp_type2nd")] != 0)
									$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td align="center" width="40"><? echo $i;?></td>
									<td width="80" style="word-break:break-all; mso-number-format:'\@';"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?>&nbsp;</p></td>
									<td width="180" style="word-break:break-all"><p><? echo $compositionDetails; ?></p></td>
									<td width="80" style="word-break:break-all"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?></p></td>
									<td width="100" style="word-break:break-all"><p><? echo $color_name_arr[$row[csf("color")]]; ?></p></td>
									<td width="80" style="word-break:break-all; mso-number-format:'\@';" title="Product ID=<? echo $row[csf('prod_id')]; ?>"><p><? echo $row[csf("lot")]; ?>&nbsp;</p></td>
									<td width="80" style="word-break:break-all"><? echo $supplierArr[$row[csf("supplier_id")]]; ?></td>
									<?
									$group_sub_total=$group_sub_total_amt=$total_receive=$total_receive_amt=$total_issue=$total_issue_amt=0;
									foreach ($stores as $store) {
										if($trans_com[$store[csf("company_id")]])
										{
											$total_receive = $receive_array[$store[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$store[csf("id")]]['rcv_total'];
											$total_receive_amt = $receive_array[$store[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$store[csf("id")]]['rcv_total_amt'];
											$total_issue = $issue_array[$store[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$store[csf("id")]]['issue_total'];
											$total_issue_amt = $issue_array[$store[csf("company_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count_id")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color")]][$row[csf("lot")]][$store[csf("id")]]['issue_total_amt'];
		
											$store_wise_balance = number_format(($total_receive - $total_issue),2,".","");
											$store_wise_amt_balance = number_format(($total_receive_amt - $total_issue_amt),2,".","");
											?>
											<td width="100" align="right" title="<? echo "Store=".$store[csf("store_name")]."\nTotal Receive=".number_format($total_receive,2,".","")."\nTotal Issue=".number_format($total_issue,2,".","");?>"><? echo $store_wise_balance;?></td>
											<?
											$group_sub_total += $store_wise_balance;
											$group_sub_total_amt += $store_wise_amt_balance;
		
											$store_wise_recv_total[$store[csf("id")]] += $store_wise_balance;
											$store_wise_balance_total += $store_wise_balance;
											$store_wise_amt_balance_total += $store_wise_amt_balance;
										}
									}
									?>
									<td width="100" align="right"><? echo number_format($group_sub_total,2,".",""); ?></td>
									<td align="right"><? echo number_format($group_sub_total_amt,2,".",""); ?></td>
								</tr>
								<?
								$i++;
							}
						}
						?>
					</tbody>
				</table>
			</div>
			<input type="hidden" id="num_of_store" value="<? echo $num_of_store;?>">
			<table class="rpt_table" border="1" width="<? echo $width; ?>" rules="all" id="table_body">
				<tr>
					<td align="right" width="645"><strong>Grand Total </strong></td>
					<?
					$t=1;
					foreach ($stores as $store)
					{
						if($trans_com[$store[csf("company_id")]])
						{
							?>
							<td width="100" align="right" id="store_total_<? echo $t;?>"><? echo number_format($store_wise_recv_total[$store[csf("id")]],2,".","");?></td>
							<?
							$t++;
						}
					}
					?>
					<td align="right" width="100" id="grp_total_qnty"><? echo number_format($store_wise_balance_total,2,".","");?></td>
					<td align="right" id="grp_total_value"><? echo number_format($store_wise_amt_balance_total,2,".","");?></td>
				</tr>
			</table>
		</fieldset>
		<?
	}
	exit();
}

if($action == "composition_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array(); var selected_name = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_pre_composition_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var k=0; k<old.length; k++)
				{
					js_set_value( old[k] )
				}
			}
		}

		function js_set_value( str )
		{

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				selected_name.push( $('#txt_individual' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}

			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}

			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );

			$('#hidden_composition_id').val(id);
			$('#hidden_composition').val(name);
		}
	</script>
</head>
<fieldset style="width:390px">
	<legend>Yarn Receive Details</legend>
	<input type="hidden" name="hidden_composition" id="hidden_composition" value="">
	<input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
	<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
		<thead>
			<tr>
				<th colspan="2">
					<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
				</th>
			</tr>
			<tr>
				<th width="50">SL</th>
				<th width="">Composition Name</th>
			</tr>
		</thead>
	</table>
	<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
			<?
			$i = 1;

			$result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
			$pre_composition_id_arr=explode(",",$pre_composition_id);
			foreach ($result as $row)
			{
				if ($i % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";

				if(in_array($row[csf("id")],$pre_composition_id_arr))
				{
					if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
					<td width="50">
						<? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
					</td>
					<td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
		</table>
	</div>
	<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
					</div>
				</div>
			</td>
		</tr>
	</table>
</fieldset>
<script type="text/javascript">
	setFilterGrid('table_body',-1);
	set_all();
</script>
<?

}
?>
