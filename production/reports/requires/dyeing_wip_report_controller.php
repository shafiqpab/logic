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
		echo create_drop_down("cbo_party_name", 130, "SELECT comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Company--", "", "load_drop_down('requires/dyeing_wip_report_controller',this.value, 'load_drop_down_wc_location', 'wc_location' );", 0);
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
	$source_cond 		= ($source) ? " and a.dyeing_source = $source" : "";
	$party_cond 		= ($party_id) ? " and a.dyeing_company = $party_id" : "";
	$wc_location_cond 	= ($wc_location_id) ? " and a.dyeing_company = $wc_location_id" : "";
	$date_cond 			= " and a.receive_date between '$from_date' and '$to_date'";

	// ====================================== MAIN QUERY ====================================

	$sql_batch_rcv="SELECT a.dyeing_source as source, a.dyeing_company as party, b.uom, b.roll_wgt 
	from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c
	where a.entry_form=62 and a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=c.entry_form and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 $company_cond $source_cond $party_cond $date_cond";
	// echo $sql_batch_rcv;die();
	$sql_batch_rcv_res = sql_select($sql_batch_rcv);
	$main_array = array();
	foreach ($sql_batch_rcv_res as $val) 
	{
		$main_array[$val[csf('source')]][$val[csf('party')]]['uom'] = $val[csf('uom')];
		$main_array[$val[csf('source')]][$val[csf('party')]]['rcv_qty'] += $val[csf('roll_wgt')];
	}

	// =========================================== YARN RETURN ===================================
	$date_cond_rcv 	= str_replace("receive_date", "production_date", $date_cond);
	$party_cond_rcv = str_replace("dyeing_company", "service_company", $party_cond);
	$source_cond_rcv= str_replace("dyeing_source", "service_source", $source_cond);

	$sql_fin_issue="SELECT a.service_source as source, a.service_company as party, b.production_qty
	from pro_fab_subprocess a, pro_fab_subprocess_dtls b
	where a.entry_form in(35) and a.id=b.mst_id and a.load_unload_id=2 and a.entry_form=b.entry_page and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_cond $source_cond_rcv $party_cond_rcv $date_cond_rcv
	order by a.service_company";
	// echo $sql_fin_issue;die();
	$sql_fin_issue_res = sql_select($sql_fin_issue);
	$fin_issue_array = array();
	foreach ($sql_fin_issue_res as $val) 
	{
		$fin_issue_array[$val[csf('source')]][$val[csf('party')]]['fin_qty'] += $val[csf('production_qty')];
	}

	// ========================================== OPENING BALANCE (batch) ==========================================
	$sql_opn_bal_rcv = "SELECT a.dyeing_source as source, a.dyeing_company as party, sum(b.roll_wgt) as receive from inv_receive_mas_batchroll a, pro_grey_batch_dtls b, pro_roll_details c where a.entry_form=62 and a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=c.entry_form and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and a.receive_date<'$from_date' $company_cond $source_cond $party_cond group by a.dyeing_source, a.dyeing_company"; 
	$sql_opn_bal_rcv_res = sql_select($sql_opn_bal_rcv);
	$receive_array = array();
	foreach ($sql_opn_bal_rcv_res as $val) 
	{
		$receive_array[$val[csf('source')]][$val[csf('party')]] = $val[csf('receive')];
	}

	// ========================================== OPENING BALANCE (finish) ==========================================
	$sql_opn_bal_issue = "SELECT a.service_source as source, a.service_company as party, sum(b.production_qty) as fin_issue from pro_fab_subprocess a, pro_fab_subprocess_dtls b where a.entry_form in(35) and a.id=b.mst_id and a.load_unload_id=2 and a.entry_form=b.entry_page and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.production_date<'$from_date'  $company_cond $source_cond_rcv $party_cond_rcv  group by a.service_source, a.service_company order by a.service_company"; 

	$sql_opn_bal_issue_res = sql_select($sql_opn_bal_issue);
	$issue_array = array();
	foreach ($sql_opn_bal_issue_res as $val) 
	{
		$issue_array[$val[csf('source')]][$val[csf('party')]] = $val[csf('fin_issue')];
	}

	ob_start();
	
	if($type==1)
	{		
		?>
		<style type="text/css">
			table tr td, table th{word-wrap: break-word;word-break: break-all;}
		</style>
        <div>
            <table width="820" cellpadding="0" cellspacing="0" id="caption"><!-- style="visibility:hidden; border:none"-->
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
            <table width="820" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="120">Party Name</th>
                    <th width="60">UOM</th>
                    <th width="100">Opening Bal.</th>
                    <th width="100">Batch Rcv.</th>
                    <th width="100">Issue to Fin.</th>
                    <th width="100">Balance</th>
                    <th width="100">Avg. Rate</th>
                    <th width="100">Total Value</th>
                </thead>
            </table>
            <div style="width:840px; overflow-y: auto; max-height:380px;" id="scroll_body">
                <table width="820" cellpadding="1" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">

					<tr bgcolor="#ffd76f">
						<td colspan="9">In House + Outbound Subcontract</td>
					</tr>
							
					<?
					$i=1;
					$gr_opn_bal 	= 0;
					$gr_batch_rcv 	= 0;
					$gr_fin_fab_iss	= 0;
					$gr_balance 	= 0;
					$gr_total_val 	= 0;
					$receive_qty 	= 0;
					$issue_qty 		= 0;
					foreach ($main_array as $source_key => $source_val) 
					{						
						foreach ($source_val as $party_key => $row) 
						{
							$fin_issue_qty 	= $fin_issue_array[$source_key][$party_key]['fin_qty'];

							$receive_qty 	= $receive_array[$source_key][$party_key];
							$issue_qty 	 	= $issue_array[$source_key][$party_key];
							$opening_balance= $receive_qty - $issue_qty;

							$balance 		= ($opening_balance+$row['rcv_qty'])-$fin_issue_qty;
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
								<td width="60" align="center"><? echo ($row['uom']==0) ? "Kg" : $unit_of_measurement[$row['uom']];?></td>
								<td width="100" align="right"><? echo number_format($opening_balance,2);?></td>
								<td width="100" align="right"><? echo number_format($row['rcv_qty'],2);?></td>
								<td width="100" align="right"><? echo number_format($fin_issue_qty,2);?></td>
								<td width="100" align="right"><? echo number_format($balance,2);?></td>
								<td width="100" align="right"><? echo number_format($rate,2);?></td>
								<td width="100" align="right"><? echo number_format($total_value,2);?></td>
							</tr>
							<?
							$i++;
							$gr_opn_bal 	+= $opening_balance;
							$gr_batch_rcv 	+= $row['rcv_qty'];
							$gr_fin_fab_iss	+= $fin_issue_qty;
							$gr_balance 	+= $balance;
							$gr_total_val 	+= $total_value;
						}
					}
					?>
                </table>
            </div>
            <table width="820" cellpadding="0" cellspacing="0" border="1" rules="all" class="tbl_bottom">
                <tr>
                    <td width="40">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td align="right" width="60">Total</td>
                    <td align="right" width="100"><? echo number_format($gr_opn_bal,2); ?></td>
                    <td align="right" width="100"><? echo number_format($gr_batch_rcv,2); ?></td>
                    <td align="right" width="100"><? echo number_format($gr_fin_fab_iss,2); ?></td>
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
