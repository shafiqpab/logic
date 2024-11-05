<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,20,21,22,23,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 150, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
	exit();
}

if($action=="generate_report")
{
	extract($_REQUEST);
	$company_name=str_replace("'","",$cbo_company_name);
	$working_company_name=str_replace("'","",$working_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_buyer=str_replace("'","",$cbo_buyer);
	$rpt_type=str_replace("'","",$rpt_type);

	
	if($cbo_buyer==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else {
				$buyer_id_cond=""; $buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer";
	}

	if($company_name>0){
		$company_cond = " and a.company_name = '$company_name'";
	}
	
	if($working_company_name>0)
	{
		$style_owner_cond = " and a.style_owner = '$working_company_name'";
	}
	if($company_name=="" && $working_company_name=="" ){
		$company_cond = "";
		$style_owner_cond="";
	}

	if($dealing_marchant!=""){
		$dealing_marchant_cond = " and a.dealing_marchant like '$dealing_marchant'";
	}else{
		$dealing_marchant_cond = "";
	}

	if($db_type==0)
	{
		if($cbo_year!=0) $year_cond=" and YEAR(b.po_received_date)=$cbo_year"; else $year_cond="";
		if($cbo_year!=0) $ex_factory_cond=" and YEAR(c.ex_factory_date)=$cbo_year"; else $ex_factory_cond="";
	}
	else if ($db_type==2)
	{
		if($cbo_year!=0) $year_cond=" and to_char(b.po_received_date,'YYYY')=$cbo_year"; else $year_cond="";
		if($cbo_year!=0) $ex_factory_cond=" and to_char (c.ex_factory_date , 'yyyy')=$cbo_year"; else $ex_factory_cond="";
	}
	$user_name_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	if($rpt_type==1)
	{
		$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$dealing_marchant_array=return_library_array( "select id,team_member_name from lib_mkt_team_member_info",'id','team_member_name');
		$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
		//$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		//$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
		//$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
		//$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
		//$cm_for_shipment_schedule_arr=return_library_array( "select job_no,cm_for_sipment_sche from  wo_pre_cost_dtls",'job_no','cm_for_sipment_sche');

		if($company_name>0 ) 
		{
			$budge_com_cond=" and d.company_name=$company_name";
			$job_btb_cond=" and d.importer_id=$company_name";
		}elseif ($working_company_name>0) {
			$budge_com_cond=" and d.style_owner=$working_company_name";
			$job_btb_cond=" and d.importer_id=$working_company_name";
		}else{
			$budge_com_cond="";
		}


		ob_start();
		?>
		<div align="center">
		<h1>Sonia  And Sweaters  Ltd</h1>
		<h2>Po Receive Report</h2>
		<h2>Year:2019</h2>
			<fieldset>
				<table width="1030" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" id="po_received_status_table">
					<thead>
						<tr>
							<th width="40" rowspan="2">SL</th>
							<th width="110" rowspan="2">Buyer Name</th>
							<th width="110" rowspan="2">Dealing Marchent</th>
							
							<th colspan="4">Order Received</th>
							<th colspan="2">Shipment</th>
							<th colspan="2">Order In Hand</th>
							<th width="110" rowspan="2">BTB to be Open($)</th>
							<th  rowspan="2">Signature</th>
						</tr>
						<tr>
							<th width="70">Quantity</th>
							<th width="70" id="value">Value</th>
							<th width="70">FOB</th>
							<th width="50">%</th>

							<th width="70">Quantity</th>
							<th width="70" id="value">Value</th>
							
							<th width="70">Quantity</th>
							<th width="70" id="value">Value</th>
						</tr>
						
					</thead>
					<tbody>
						<?
							$i=1; $total_po=0; $total_price=0;

							$sql_data_array = "select a.company_name,a.buyer_name,a.dealing_marchant,a.avg_unit_price, b.id as po_breakdow_id,b.po_quantity,b.po_total_price,b.pub_shipment_date 
							from wo_po_details_master a, wo_po_break_down b   
							where a.job_no=b.job_no_mst $company_cond $style_owner_cond $buyer_id_cond $dealing_marchant_cond $year_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
							//echo $sql_data_array;die;
							$data_array=sql_select($sql_data_array);$po_wise_buyer=array();
 
							foreach ($data_array as $value) {
								$po_receive_data_array[$value[csf('buyer_name')]]['company_name'] = $value[csf('company_name')];
								$po_receive_data_array[$value[csf('buyer_name')]]['buyer_name'] = $value[csf('buyer_name')];
								$po_receive_data_array[$value[csf('buyer_name')]]['dealing_marchant'] = $value[csf('dealing_marchant')];
								$po_receive_data_array[$value[csf('buyer_name')]]['avg_unit_price'] = $value[csf('avg_unit_price')];
								$po_receive_data_array[$value[csf('buyer_name')]]['po_quantity'] += $value[csf('po_quantity')];
								$po_receive_data_array[$value[csf('buyer_name')]]['po_total_price'] += $value[csf('po_total_price')];
								//$po_breakdow_ids[$value[csf('po_breakdow_id')]] = $value[csf('po_breakdow_id')];
								$po_breakdow_ids .= $value[csf('po_breakdow_id')].",";
								$po_wise_buyer[$value[csf('po_breakdow_id')]]=$value[csf('buyer_name')];
							}
							 //$po_breakdow_ids = implode(",",$po_breakdow_ids);
							 $po_breakdow_ids = chop($po_breakdow_ids,",");
							 //$po_breakdow_ids = array_chunk($po_breakdow_ids,999);
							
							//$po_breakdow_ids=rtrim($po_breakdow_ids,",");
							$all_po_breakdow_ids=array_chunk(explode(",",$po_breakdow_ids),999,true);
							$ex_factory_cond="";
							$ji=0;
							foreach($all_po_breakdow_ids as $key=> $value)
							{
								if($ji==0)
								{
								$ex_factory_cond=" and c.po_break_down_id in (".implode(",",$value).")"; 
								
								}
								else
								{
								$ex_factory_cond.=" or c.po_break_down_id in (".implode(",",$value).")";
								
								}
								$ji++;
							}
							//echo $ex_factory_cond;die;

							$ex_factory_sql = "select c.po_break_down_id, c.ex_factory_date, c.ex_factory_qnty
							from pro_ex_factory_mst c 
							where c.status_active = 1 and c.is_deleted = 0 $ex_factory_cond";
							//echo $ex_factory_sql;die;

							$ex_factory_details_resule=sql_select($ex_factory_sql);

							foreach ($ex_factory_details_resule as  $value) {
								$ex_factory_data_array[$po_wise_buyer[$value[csf('po_break_down_id')]]] += $value[csf("ex_factory_qnty")];
							}
							//echo "<pre>";
							//print_r($ex_factory_data_array);//die;

							foreach ($po_receive_data_array as $row)
							{
								$total_receive +=$row['po_quantity'];	
							}
							
							foreach ($po_receive_data_array as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$fob = ($row['po_total_price']*1) / ($row['po_quantity']*1);
								$percentage = (($row['po_quantity']*1)*100) / ($total_receive*1);
								
								?>
								<tr bgcolor="<? echo $bgcolor;?>">
									<td width="40"><? echo $i;?></td>
									<td width="110" title="<? echo $row['buyer_name'];?>"><? echo $buyer_name_arr[$row['buyer_name']]; ?></td>
									<td width="110"><? echo $dealing_marchant_array[$row['dealing_marchant']];?></td>
									<td align="right">
									<?
										echo number_format($row['po_quantity'],0); $total_po +=($row['po_quantity']*1);	
									?>
									</td>
									<td align="right" id="value">
									<?
										echo number_format(($row['po_total_price']*1),2); $total_price+= $row['po_total_price'];
										
									?>
									</td>
									<td id="fob_<? echo $i; ?>" align="right"><? echo number_format($fob,2);?></td>
									<td id="percent_<? echo $i; ?>" align="right"><? echo number_format($percentage,2)."%";?></td>
									<td align="right">
									<?
									echo number_format($ex_factory_data_array[$row['buyer_name']],0); 
									$shipped_total_qnty+=$ex_factory_data_array[$row['buyer_name']];
									
									?>
									</td>
									<td align="right" id="value">
									<?
										$ex_factory_value = ($ex_factory_data_array[$row['buyer_name']]*1)*($row['avg_unit_price']*1);
										echo number_format($ex_factory_value,2); $total_ex_factory_value+=$ex_factory_value;
									
									?>
									</td>
									<td align="right">
									<?
									    $order_in_hand_quantity=($row['po_quantity']*1)-($ex_factory_data_array[$row['buyer_name']]*1); 
									    echo number_format($order_in_hand_quantity,0);
									   $total_order_in_hand_quantity+=$order_in_hand_quantity;
									
									?>
									</td>
									<td align="right" id="value">
									<? 
										$in_hand_order_value=(($row['po_total_price']*1) - $ex_factory_value); $total_in_hand_order_value+=$in_hand_order_value;  
										echo number_format($in_hand_order_value,2); 
									?>											
									</td>
									<td align="right"><? //echo number_format($buyer_btb_array[$row[csf('buyer_name')]]['job_quantity'],2); //$btb_open?></td>
									<td></td>
								</tr>
								<?
								$i++;
								$ex_factory_value=0;
								
							}
						?>
					</tbody>
					<tfoot>
						<th></th>
						<th></th>
						<th></th>
						<th><? echo number_format($total_po,0); ?></th>
						<th><?  echo number_format($total_price,2); ?> <input type="hidden" id="total_value" value="<? echo $total_price;?>"/></th>
						<th></th>
						<th></th>
						<th><? echo number_format($shipped_total_qnty,0); ?></th>
						<th><? echo number_format($total_ex_factory_value,2); ?></th>
						<th><? echo number_format($total_order_in_hand_quantity,0); ?></th>
						<th><? echo number_format($total_in_hand_order_value,2); ?></th>
						<th></th>
						<th></th>
					</tfoot>
				</table>
			</fieldset>
		</div> 
    <?
	}

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
	echo "**$filename**$rpt_type";
	disconnect($con);
	exit();
}

if ($action == "inter_company_export_proceed") {
	extract($_REQUEST);
	$company_name=str_replace("'","",$cbo_company_name);
	$working_company_name=str_replace("'","",$working_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_buyer=str_replace("'","",$cbo_buyer);
	$rpt_type=str_replace("'","",$rpt_type);

	$user_name_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	if($rpt_type==2)
	{
		$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
		$dealing_marchant_array=return_library_array( "select id,team_member_name from lib_mkt_team_member_info",'id','team_member_name');
		$company_name_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
		//$buyer_wise_season_arr=return_library_array( "select id, season_name from lib_buyer_season where status_active =1 and is_deleted=0",'id','season_name');
		//$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
		//$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
		//$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where file_type=1",'master_tble_id','image_location');
		//$cm_for_shipment_schedule_arr=return_library_array( "select job_no,cm_for_sipment_sche from  wo_pre_cost_dtls",'job_no','cm_for_sipment_sche');

		if($cbo_buyer==0)
		{
			if ($_SESSION['logic_erp']["data_level_secured"]==1)
			{
				if($_SESSION['logic_erp']["buyer_id"]!="")
				{
					$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
					$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				}
				else {
					$buyer_id_cond=""; $buyer_id_cond2="";
				}
			}
			else
			{
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond=" and a.buyer_name=$cbo_buyer";//.str_replace("'","",$cbo_buyer_name)
			$buyer_id_cond2=" and a.buyer_id=$cbo_buyer";
		}
		
		if($db_type==0)
		{
			if($cbo_year!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
			if($cbo_year!=0) $ex_factory_cond=" and YEAR(c.ex_factory_date)=$cbo_year"; else $ex_factory_cond="";
		}
		else if ($db_type==2)
		{
			if($cbo_year!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
			if($cbo_year!=0) $ex_factory_cond=" and to_char (c.ex_factory_date , 'yyyy')=$cbo_year"; else $ex_factory_cond="";
		}

		if($company_name>0 ) 
		{
			$budge_com_cond=" and d.company_name=$company_name";
			$job_btb_cond=" and d.importer_id=$company_name";
			$company_cond = " and a.company_name in($company_name)";
		}elseif ($working_company_name>0) {
			$budge_com_cond=" and d.style_owner=$working_company_name";
			$job_btb_cond=" and d.importer_id=$working_company_name";
			$company_cond = " and a.style_owner in($working_company_name)";
		}else{
			$budge_com_cond="";
			$company_cond ="";
		}

		$job_btb_sql="select a.job_no, b.id as pi_dtls_id, b.net_pi_amount, d.importer_id, 1 as type  
		from wo_non_order_info_dtls a, com_pi_item_details  b, com_btb_lc_pi c, com_btb_lc_master_details d
		where a.id=b.work_order_dtls_id and b.pi_id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.item_category_id=1 and b.item_category_id=1 and a.job_no is not null $job_btb_cond
		union all 
		select a.job_no, b.id as pi_dtls_id, b.net_pi_amount, d.importer_id, 2 as type  
		from wo_booking_dtls a, com_pi_item_details  b, com_btb_lc_pi c, com_btb_lc_master_details d
		where a.id=b.work_order_dtls_id and b.pi_id=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_type=2 and b.item_category_id=4 and a.job_no is not null $job_btb_cond";

		//echo $job_btb_sql;//die;

		$job_btb_result=sql_select($job_btb_sql);
		$job_wise_btb_value=array();
		foreach($job_btb_result as $row)
		{
			if($pi_dtls_id_check[$row[csf("pi_dtls_id")]]=="")
			{
				$pi_dtls_id_check[$row[csf("pi_dtls_id")]]=$row[csf("pi_dtls_id")];
				$job_wise_btb_value[$row[csf("importer_id")]][$row[csf("job_no")]][$row[csf("type")]]+=$row[csf("net_pi_amount")];
			}
		}
		
		
		$budge_btb_open_sql="select  a.job_no, d.buyer_name, (d.job_quantity*d.total_set_qnty) as job_quantity, a.costing_per, b.amount, b.id as dtls_id, 1 as type
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_fab_yarn_cost_dtls b 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0  $budge_com_cond $year_cond	
		union all 
		select  a.job_no, d.buyer_name, (d.job_quantity*d.total_set_qnty) as job_quantity, a.costing_per, b.amount, b.id as dtls_id, 2 as type
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 $budge_com_cond $year_cond
		union all 
		select  a.job_no, d.buyer_name, (d.job_quantity*d.total_set_qnty) as job_quantity, a.costing_per, b.amount, b.id as dtls_id, 3 as type
		from wo_po_break_down c, wo_po_details_master d, wo_pre_cost_mst a, wo_pre_cost_embe_cost_dtls b 
		where c.job_no_mst=d.job_no and d.job_no=a.job_no and a.job_no=b.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.amount > 0 and b.emb_name <>3 $budge_com_cond $year_cond";
		//echo $budge_btb_open_sql;//die;
		$budge_btb_open_result=sql_select($budge_btb_open_sql);
		$job_wise_budge_amt=array();
		foreach($budge_btb_open_result as $row)
		{
			$dzn_qnty=0;
			$costing_per_id=$row[csf('costing_per')];//$fabriccostArray[$row[csf('job_no')]]['costing_per_id'];
			if($costing_per_id==1) $dzn_qnty=12;
			else if($costing_per_id==3) $dzn_qnty=12*2;
			else if($costing_per_id==4) $dzn_qnty=12*3;
			else if($costing_per_id==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
			$amount=0;
			$amount=($row[csf("amount")]/$dzn_qnty)*$row[csf("job_quantity")];
			if($job_check[$row[csf("dtls_id")]]=="")
			{
				$job_check[$row[csf("dtls_id")]]=$row[csf("dtls_id")];
				$all_job_no[$row[csf('job_no')]]=$row[csf('job_no')];
				$job_wise_budge_amt[$row[csf('buyer_name')]][$row[csf('job_no')]][$row[csf('type')]]+=$amount;
				$buyer_btb_array[$row[csf('buyer_name')]]['job_quantity']+=$row[csf("job_quantity")];
			}
		}
		// echo "<pre>";
		// print_r($job_wise_budge_amt);
		$btb_open=0;
		foreach($all_job_no as $job=>$job_num)
		{
			$btb_open +=($job_wise_budge_amt[$job][$job_num][1]-$job_wise_btb_value[$job][$job_num][1])+($job_wise_budge_amt[$job][$job_num][2]-$job_wise_btb_value[$job][$job_num][2])+($job_wise_budge_amt[$job][$job_num][3]-$job_wise_btb_value[$job_num][3]);
		}

		ob_start();
		?>
		<div align="center">			
			<h1>Reconciliation Of Inter Company Export Proceed</h1>
			<h2>Order Receive Company:Sonia Sweaters Ltd.</h2>
			<h2>Working Company:Sonia Fine Knit Ltd.</h2>
			<h3>Year:2019</h3>
			
			<fieldset>
				<table width="1030" cellspacing="0" cellpadding="0" class="rpt_table" border="1" rules="all" id="po_received_status_table">
					<thead>
						<tr>
							<th width="40" rowspan="2">SL</th>
							<th width="110" rowspan="2">Buyer Name</th>
							
							<th colspan="2">Order Received</th>
							<th colspan="3">BTB</th>
							<th colspan="2">Order In Hand</th>
							<th width="70" rowspan="2">CM</th>
							<th colspan="2">Shipment Status</th>
							<th rowspan="2">Proceed to be realize</th>
						</tr>
						<tr>
							<th width="70">Quantity</th>
							<th width="70" id="value">Value</th>

							<th width="70">Opened</th>
							<th width="70">To be Open</th>
							<th width="70" id="value">Total Value</th>
							
							<th width="70">Quantity</th>
							<th width="70" id="value">Value</th>

							<th width="70">Quantity</th>
							<th width="70" id="value">Value</th>
						</tr>
						
					</thead>
					<tbody>
						<?
							$i=1; $total_po=0; $total_price=0;
							
							$sql_data_array = "select a.company_name,a.buyer_name,a.dealing_marchant,a.avg_unit_price, b.po_quantity,b.po_total_price,c.ex_factory_date,c.ex_factory_qnty   
							from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c   
							where a.job_no=b.job_no_mst and b.id=c.po_break_down_id $company_cond $buyer_id_cond $dealing_marchant_cond $year_cond $ex_factory_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
							//echo $sql_data_array;//die;
							$data_array=sql_select($sql_data_array);

							$po_receive_details_array=array();
							foreach ($data_array as $value) {
								$po_receive_details_array[$value[csf('buyer_name')]]['company_name']=$value[csf('company_name')];
								$po_receive_details_array[$value[csf('buyer_name')]]['buyer_name']=$value[csf('buyer_name')];
								$po_receive_details_array[$value[csf('buyer_name')]]['dealing_marchant']=$value[csf('dealing_marchant')];
								$po_receive_details_array[$value[csf('buyer_name')]]['avg_unit_price']=$value[csf('avg_unit_price')];
								$po_receive_details_array[$value[csf('buyer_name')]]['po_quantity']+=$value[csf('po_quantity')];
								$po_receive_details_array[$value[csf('buyer_name')]]['po_total_price']+=$value[csf('po_total_price')];
								$po_receive_details_array[$value[csf('buyer_name')]]['ex_factory_date']=$value[csf('ex_factory_date')];

								$order_in_hand_quantity=($value[csf('po_quantity')]*1)-($value[csf('ex_factory_qnty')]*1);
								$po_receive_details_array[$value[csf('buyer_name')]]['order_in_hand_quantity']=$order_in_hand_quantity;

								$in_hand_order_value=($order_in_hand_quantity*($value[csf('avg_unit_price')]*1)); 
								$po_receive_details_array[$value[csf('buyer_name')]]['ex_factory_qnty']=$value[csf('ex_factory_qnty')];
								//$po_receive_details_array[$value[csf('buyer_name')]]['buyer_name']=$value[csf('buyer_name')];
							}

							foreach ($po_receive_details_array as $row)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$fob = ($row['po_total_price']*1) / ($row['po_quantity']*1);
								$percentage = (($row['ex_factory_qnty']*1)*100) / ($row['po_quantity']*1);
								
								?>
								<tr bgcolor="<? echo $bgcolor;?>">
									<td width="40"><? echo $i;?></td>
									<td width="130"><? echo $buyer_name_arr[$row['buyer_name']]; ?></td>
									<!-- <td width="130"><? //echo $dealing_marchant_array[$row[csf('dealing_marchant')]];?></td> -->
									<td align="right">
									<?
									echo number_format($row['po_quantity'],0); $total_po +=$row['po_quantity'];
									
									?>
									</td>
									<td align="right"  id="value">
									<?
										echo number_format(($row['po_total_price']*1),2); $total_price+= $row['po_total_price'];
										
									?>
									</td>
									<td id="fob_<? echo $i; ?>" align="right">
									<? echo $fob;?></td>
									<td id="percent_<? echo $i; ?>" align="right">
									<? echo number_format($buyer_btb_array[$row['buyer_name']]['job_quantity'],2); //$btb_open?>
									</td>
									<td align="right"  id="value">
										<?
											echo number_format($row['ex_factory_qnty'],0); $shipped_total_qnty+=$row['ex_factory_qnty'];										
										?>
									</td>
									<td align="right">
										<?
											 echo number_format($order_in_hand_quantity,0);$total_order_in_hand_quantity+=$order_in_hand_quantity;									
										?>
									</td>
									<td align="right"  id="value">
										<? 
											$total_in_hand_order_value+=$in_hand_order_value;  
											echo number_format($in_hand_order_value,2); 
										?>	
									</td>
									<td align="right">
																			
									</td>
									<td align="right">
										<?
											echo number_format($row['ex_factory_qnty'],0); $shipped_total_qnty+=$row['ex_factory_qnty'];
										
										?>
									</td>
									<td align="right"  id="value">
										<?
											$ex_factory_value = ($row['ex_factory_qnty']*1)*($row['avg_unit_price']*1);
											echo number_format($ex_factory_value,2); $total_ex_factory_value+=$ex_factory_value;
										
										?>
									</td>
									<td></td>
								</tr>
								<?
								$i++;
								$shipped_total_qnty=$total_ex_factory_value=$ex_factory_value=0;
							}
						?>
					</tbody>
					<tfoot>
						<th></th>
						<th></th>
						<th><? echo number_format($total_po,0); ?></th>
						<th width="100"><?  echo number_format($total_price,2); ?> <input type="hidden" id="total_value" value="<? echo $total_price;?>"/></th>
						<th></th>
						<th></th>
						<th></th>
						<th><? echo number_format($shipped_total_qnty,0); ?></th>
						<th><? echo number_format($total_ex_factory_value,0); ?></th>
						<th></th>
						<th><? echo number_format($total_order_in_hand_quantity,0); ?></th>
						<th><? echo number_format($total_in_hand_order_value,0); ?></th>
						<th></th>
					</tfoot>
				</table>
			</fieldset>
		</div>
    <?
	}

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
	echo "**$filename**$rpt_type";
	disconnect($con);
	exit();
}

