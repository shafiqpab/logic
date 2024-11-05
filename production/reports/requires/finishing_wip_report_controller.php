<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
    extract($_REQUEST);
    $choosenCompany = $data;  
	echo create_drop_down( "cbo_lc_location_name", 140, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "" );
	exit();
}

if ($action == "load_drop_down_party")
{
	$data = explode("_", $data);
	$company_id = $data[1];

	//$company_id
	if ($data[0] == 1) 
	{
		echo create_drop_down("cbo_party_name", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", "", "load_drop_down('requires/finishing_wip_report_controller',this.value, 'load_drop_down_wc_location', 'wc_location' );", 0);
	} 
	else if ($data[0] == 3) 
	{
		echo create_drop_down("cbo_party_name", 130, "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "--Select Supplier--", 0, "");
	} 
	else 
	{
		echo create_drop_down("cbo_party_name", 130, $blank_array, "", 1, "--Select Company--", 0, "");
	}
	exit();
}

if ($action=="load_drop_down_wc_location")
{
    extract($_REQUEST);
    $choosenCompany = $data;  
	echo create_drop_down( "cbo_wc_location_name", 140, "SELECT id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id in( $choosenCompany) group by id,location_name  order by location_name","id,location_name", 1, "-- Select location --", $selected, "" ,0);
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");	
	$party_library=return_library_array( "SELECT a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id", "supplier_name"  );
	
	// ============================== GETTING FORM DATA ================================

	$lc_company_id 	= str_replace("'", "", $cbo_company_name);
	$lc_location_id = str_replace("'", "", $cbo_lc_location_name);
	$source 		= str_replace("'", "", $cbo_source);
	$party_id 		= str_replace("'", "", $cbo_party_name);
	$wc_location_id = str_replace("'", "", $cbo_wc_location_name);
	$rate 			= str_replace("'", "", $txt_rate);
	$type 			= str_replace("'","",$type);

	if($db_type==0)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'yyyy-mm-dd');
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'yyyy-mm-dd');
	}
	elseif($db_type==2)
	{
		$from_date=change_date_format(str_replace("'","",$txt_date_from),'','',1);
		$to_date=change_date_format(str_replace("'","",$txt_date_to),'','',1);
	}

	// ====================================== MAKING QUERY CON =============================
	$company_cond 		= ($lc_company_id) ? " and a.company_id = $lc_company_id" : "";
	$location_cond 		= ($lc_location_id) ? " and a.location_id = $lc_location_id" : "";
	$source_cond 		= ($source) ? " and a.service_source = $source" : "";
	$party_cond 		= ($party_id) ? " and a.service_company = $party_id" : "";
	$wc_location_cond 	= ($wc_location_id) ? " and a.dyeing_company = $wc_location_id" : "";
	$date_cond 			= " and a.production_date between '$from_date' and '$to_date'";

	// ====================================== MAIN QUERY ====================================

	$sql_fin_issue="SELECT a.service_source as source, a.service_company as party, b.production_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.entry_form in(35) and a.id=b.mst_id and a.load_unload_id=2 and a.result=1 and a.entry_form=b.entry_page and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $source_cond $party_cond $date_cond order by a.service_company"; 
	// echo $sql_fin_issue;die();
	$sql_fin_issue_res = sql_select($sql_fin_issue);
	$main_array = array();
	foreach ($sql_fin_issue_res as $val) 
	{
		$main_array[$val[csf('source')]][$val[csf('party')]]['fin_qty'] += $val[csf('production_qty')];
	}

	// =========================================== FOR QC PASS ===================================
	$date_cond_rcv 	= str_replace("production_date", "receive_date", $date_cond);
	$party_cond_rcv = str_replace("service_company", "knitting_company", $party_cond);
	$source_cond_rcv= str_replace("service_source", "knitting_source", $source_cond);

	$sql_qc_pass="SELECT a.knitting_source as source, a.knitting_company as party, b.receive_qnty, b.reject_qty,b.uom 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b
	where a.item_category in(2) and a.entry_form in(7) and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $source_cond_rcv $party_cond_rcv $date_cond_rcv
	order by a.knitting_company";
	// echo $sql_qc_pass;
	$sql_qc_pass_res = sql_select($sql_qc_pass);
	$qc_pass_array = array();
	foreach ($sql_qc_pass_res as $val) 
	{
		$qc_pass_array[$val[csf('source')]][$val[csf('party')]]['receive_qnty'] += $val[csf('receive_qnty')];
		$qc_pass_array[$val[csf('source')]][$val[csf('party')]]['reject_qty'] += $val[csf('reject_qty')];
		$main_array[$val[csf('source')]][$val[csf('party')]]['uom'] = $val[csf('uom')];
	}
	// echo "<pre>";
	// print_r($main_array);
	// =========================================== FOR STORE RECEIVE ===================================	
	$sql_store_rec="SELECT a.knitting_source as source, a.knitting_company as party, b.cons_quantity, b.return_qnty, b.cons_reject_qnty 
	from inv_receive_master a, inv_transaction b, order_wise_pro_details c 
	where a.item_category in(2) and a.entry_form in(37) and a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=c.trans_type and c.status_active=1 and b.item_category in(2) and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $source_cond_rcv $party_cond_rcv $date_cond_rcv
	order by a.knitting_company";
	// echo $sql_store_rec;
	$sql_store_rec_res = sql_select($sql_store_rec);
	$store_rec_array = array();
	foreach ($sql_store_rec_res as $val) 
	{
		$store_rec_array[$val[csf('source')]][$val[csf('party')]]['store_rcv_qty'] += $val[csf('cons_quantity')];
	}

	// ========================================== OPENING BALANCE (receive) ==========================================
	$sql_opn_bal_rcv = "SELECT a.service_source as source, a.service_company as party, sum(b.production_qty) as production_qty from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.production_date<'$from_date' and a.entry_form in(35) and a.id=b.mst_id and a.load_unload_id=2 and a.result=1 and a.entry_form=b.entry_page and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $source_cond $party_cond group by a.service_source, a.service_company order by a.service_company";
	$sql_opn_bal_rcv_res = sql_select($sql_opn_bal_rcv);
	$receive_array = array();
	foreach ($sql_opn_bal_rcv_res as $val) 
	{
		$receive_array[$val[csf('source')]][$val[csf('party')]] = $val[csf('production_qty')];
	}

	// ========================================== OPENING BALANCE (store receive) ==========================================
	$sql_opn_bal_store_rcv = "SELECT a.knitting_source as source, a.knitting_company as party, sum(b.cons_quantity) as cons_quantity from inv_receive_master a, inv_transaction b, order_wise_pro_details c where a.receive_date<'$from_date' and a.item_category in(2) and a.entry_form in(37) and a.id=b.mst_id and b.id=c.trans_id and b.transaction_type=c.trans_type and c.status_active=1 and b.item_category in(2) and b.transaction_type in(1) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $source_cond_rcv $party_cond_rcv group by a.knitting_source, a.knitting_company order by a.knitting_company"; 
	$sql_opn_bal_store_rcv_rs = sql_select($sql_opn_bal_store_rcv);
	$issue_array = array();
	foreach ($sql_opn_bal_store_rcv_rs as $val) 
	{
		$issue_array[$val[csf('source')]][$val[csf('party')]] = $val[csf('cons_quantity')];
	}

	ob_start();
	
	if($type==1)
	{		
		?>
		<style type="text/css">
			table tr td, table th{word-wrap: break-word;word-break: break-all;}
		</style>
        <div>
            <table width="920" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
                <tr>
                   <td align="center" width="100%" colspan="12" style="font-size:24px !important;"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="12" style="font-size:16px;font-weight: bold;"><? echo $report_title; ?> (Party Wise)</td>
                </tr>  
                <tr> 
                   <td align="center" width="100%" colspan="12" style="font-size:16px;font-weight: bold;"><? echo "From ".change_date_format(str_replace("'","",$txt_date_from))." To ".change_date_format(str_replace("'","",$txt_date_to)); ?></td>
                </tr>
            </table>
            <table width="920" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="120">Party Name</th>
                    <th width="60">UOM</th>
                    <th width="100">Opening Bal.</th>
                    <th width="100">Fin.Rcv.</th>
                    <th width="100">QC Pass</th>
                    <th width="100">Store Rcv.</th>
                    <th width="100">Balance</th>
                    <th width="100">Avg. Rate</th>
                    <th width="100">Total Value</th>
                </thead>
            </table>
            <div style="width:940px; overflow-y: auto; max-height:380px;" id="scroll_body">
                <table width="920" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
					<tr bgcolor="#ffd76f">
						<td colspan="12">In House + Outbound Subcontract</td>
					</tr>							
					<?
					$i=1;
					$gr_opn_bal 	= 0;
					$gr_yarn_issue 	= 0;
					$gr_fab_rcv 	= 0;
					$gr_rej_fab_rcv = 0;
					$gr_yarn_rtn 	= 0;
					$gr_rej_yarn_rtn= 0;
					$gr_balance 	= 0;
					$gr_total_val 	= 0;
					$receive_qty 	= 0;
					$issue_qty 		= 0;
					foreach ($main_array as $source_key => $source_val) 
					{
						foreach ($source_val as $party_key => $row) 
						{
							$qc_pass_qty 	= $qc_pass_array[$source_key][$party_key]['receive_qnty'];

							$store_rec_qty 	= $store_rec_array[$source_key][$party_key]['store_rcv_qty'];

							$receive_qty 	= $receive_array[$source_key][$party_key];
							$issue_qty 	 	= $issue_array[$source_key][$party_key];
							$opening_balance= $receive_qty - $issue_qty;

							$balance 		= ($opening_balance+$row['fin_qty'])-$store_rec_qty;
							$total_value 	= $balance*$rate;

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
								<td width="40" align=""><? echo $i;?></td>
								<td width="120" align="">
								<? 
			                    	if($source_key==1)
			                    	{
			                    		echo $company_arr[$party_key];
			                    	}
			                    	else
			                    	{
			                    		echo $party_library[$party_key];
			                    	}
			                    	
		                    	?>
								</td>
								<td width="60" align="center"><? echo $unit_of_measurement[$row['uom']];?></td>
								<td width="100" align="right"><? echo number_format($opening_balance,2);?></td>
								<td width="100" align="right"><? echo number_format($row['fin_qty'],2);?></td>
								<td width="100" align="right"><? echo number_format($qc_pass_qty,2);?></td>
								<td width="100" align="right"><? echo number_format($store_rec_qty,2);?></td>
								<td width="100" align="right"><? echo number_format($balance,2);?></td>
								<td width="100" align="right"><? echo number_format($rate,2);?></td>
								<td width="100" align="right"><? echo number_format($total_value,2);?></td>
							</tr>
							<?
							$i++;
							$gr_opn_bal 	+= 0;
							$gr_yarn_issue 	+= $row['fin_qty'];
							$gr_fab_rcv 	+= $fab_rcv_qty;
							$gr_rej_fab_rcv += $fab_rej_qty;
							$gr_yarn_rtn 	+= $yarn_rtn_qty;
							$gr_rej_yarn_rtn+= $yarn_rej_qty;
							$gr_balance 	+= $balance;
							$gr_total_val 	+= $total_value;
						}
					}
					?>
                </table>
            </div>
            <table width="920" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
                <tr>
                    <td width="40">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td align="right" width="60">Total</td>
                    <td align="right" width="100"><? echo number_format($gr_opn_bal,2); ?></td>
                    <td align="right" width="100"><? echo number_format($gr_yarn_issue,2); ?></td>
                    <td align="right" width="100"><? echo number_format($gr_fab_rcv,2); ?></td>
                    <td align="right" width="100"><? echo number_format($gr_rej_fab_rcv,2); ?></td>
                    <td align="right" width="100"><? echo number_format($gr_balance,2); ?></td>
                    <td align="right" width="100">&nbsp;</td>
                    <td align="right" width="100"><? echo number_format($gr_total_val,2); ?></td>
                </tr>
            </table>
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
	echo "$total_data####$filename";
	exit();
}
?>
