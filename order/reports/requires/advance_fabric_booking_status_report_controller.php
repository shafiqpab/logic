<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$date=date('Y-m-d');

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$season_arr=return_library_array("select id,season_name from  lib_buyer_season","id","season_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
$supplier_library=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name"  );
$po_name_arr=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	$company_name=str_replace("'","",$cbo_company_name);
	$working_factory=str_replace("'","",$cbo_working_factory);
	$buyer_name=str_replace("'","",$cbo_buyer_name);
	$date_for=str_replace("'","",$cbo_date_for);
	$from_date=str_replace("'","",$txt_date_from);
	$to_date=str_replace("'","",$txt_date_to);
	$search_con="";
	if($company_name!=0){
		$search_con=" and a.company_name=$company_name";
	}
	if($working_factory !=0){
		$search_con.=" and a.working_company_id=$working_factory";
	}
	if($buyer_name !=0){
		$search_con.=" and e.buyer_id=$buyer_name";
	}
	if(str_replace("'","",$txt_style_ref)!=""){
		$search_con.=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";	
	}
	if(str_replace("'","",$txt_job_no)!=""){
		$search_con.=" and a.job_no_prefix_num like '%".str_replace("'","",$txt_job_no)."%'";	
	}
	if(str_replace("'","",$txt_booking_no)!=""){
		$search_con.=" and e.booking_no_prefix_num like '%".str_replace("'","",$txt_booking_no)."%'";	
	}

	if($from_date!="" && $to_date!="")
	{
		if($date_for==1){
			$search_con.=" and b.pub_shipment_date between '".change_date_format(trim($from_date),'','',1)."' and '".change_date_format(trim($to_date),'','',1)."'";
		}
		else if($date_for==2){
			$search_con.=" and b.po_received_date between '".change_date_format(trim($from_date),'','',1)."' and '".change_date_format(trim($to_date),'','',1)."'";
		}
		else{
			$search_con.=" and e.booking_date between '".change_date_format(trim($from_date),'','',1)."' and '".change_date_format(trim($to_date),'','',1)."'";
		}
		
	}

	$main_data=sql_select("SELECT a.id as job_id, a.style_ref_no, b.id as po_id,  b.po_number, b.plan_cut, c.id as fabric_id, c.uom, c.lib_yarn_count_deter_id, c.fabric_description, c.avg_finish_cons,  d.gmts_color_id, e.id as booking_id, e.booking_no, e.supplier_id, e.pay_mode, e.buyer_id, d.fin_fab_qnty from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_pre_cost_fabric_cost_dtls c on a.id=c.job_id join wo_booking_dtls d on b.id=d.po_break_down_id and c.id=d.pre_cost_fabric_cost_dtls_id join wo_booking_mst e on d.booking_mst_id=e.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.proceed_advanced=1 $search_con");
	$main_data_arr=array();
	$main_attributes=array('job_id','style_ref_no','fabric_id','uom','gmts_color_id','booking_id','booking_no','supplier_id', 'pay_mode', 'fin_fab_qnty', 'lib_yarn_count_deter_id', 'fabric_description', 'buyer_id', 'avg_finish_cons');
	foreach ($main_data as $row) {
		$key=$row[csf('fabric_id')].'*'.$row[csf('gmts_color_id')];
		foreach ($main_attributes as $attr) {
			$main_data_arr[$key][$attr]=$row[csf($attr)];
		}
			$main_data_arr[$key]['po_data'][$row[csf('po_id')]]=$row[csf('po_number')];
			$main_data_arr[$key]['plan_cut']+=$row[csf('plan_cut')];
			$fab_req_arr[$key]['fin_fab_qnty']+=$row[csf('fin_fab_qnty')];
			$booking_id_arr[$row[csf('booking_id')]]=$row[csf('booking_id')];
			$po_id_arr[$row[csf('po_id')]]=$row[csf('po_id')];
			$job_id_arr[$row[csf('po_id')]]=$row[csf('job_id')];
			$yarn_id_arr[$row[csf('lib_yarn_count_deter_id')]]=$row[csf('lib_yarn_count_deter_id')];
	}
	$pi_number_data=sql_select("SELECT f.style_ref_no,d.pi_number,g.lc_number,f.job_no,i.job_id, i.lib_yarn_count_deter_id, c.amount, c.color_id as color_id from wo_booking_dtls b left join  wo_pre_cost_fabric_cost_dtls i on   b.job_no = i.job_no,com_pi_item_details c , com_pi_master_details d ,wo_po_details_master f,com_btb_lc_master_details g,com_btb_lc_pi h 	where b.booking_no=c.work_order_no and g.id=h.com_btb_lc_master_details_id and h.pi_id=d.id and b.job_no = f.job_no and d.id=c.pi_id and b.status_active=1 and b.is_deleted=0 	and c.status_active=1 and c.is_deleted=0 and g.item_basis_id in (1,2) and d.status_active=1 and d.is_deleted=0 and g.importer_id in (1) and d.entry_form=166  ".where_con_using_array($job_id_arr,0,'i.job_id')." 	group by f.style_ref_no, d.pi_number, g.lc_number, f.job_no , i.job_id, i.lib_yarn_count_deter_id, c.amount, c.color_id");
	
	$pi_data_arr=array();
	foreach ($pi_number_data as $row) {
		$pi_key=$row[csf('job_id')].'*'.$row[csf('lib_yarn_count_deter_id')].'*'.$row[csf('color_id')];
		$pi_data_arr[$pi_key]['pi_number']=$row[csf('pi_number')];
		$pi_data_arr[$pi_key]['blc_no']=$row[csf('lc_number')];
	}

	$receive_qty_data=sql_select("SELECT d.detarmination_id, d.color, b.order_qnty, b.order_amount, e.job_id,  b.order_rate	from inv_receive_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e 	on e.id=c.po_breakdown_id where a.entry_form=17 and a.status_active=1 and a.is_deleted=0 and  b.transaction_type=1 and b.status_active=1 and b.is_deleted=0 and c.trans_type=1 and c.entry_form=17 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($po_id_arr,0,'c.po_breakdown_id')."	group by d.detarmination_id, d.color, b.order_qnty, b.order_amount, e.job_id, b.order_rate");
	//b.receive_basis=1 and

	$receive_qty_arr=array();
	foreach ($receive_qty_data as $row) {
		$rcv_key=$row[csf('job_id')].'*'.$row[csf('detarmination_id')].'*'.$row[csf('color')];
		$receive_qty_arr[$rcv_key]+=$row[csf('order_qnty')];
	}

	$issue_qty_data=sql_select("SELECT d.detarmination_id, d.color, b.cons_quantity, b.cons_amount, e.job_id, e.id as po_id,d.supplier_id from inv_issue_master a join inv_transaction b on a.id = b.mst_id join order_wise_pro_details c on b.id = c.trans_id join product_details_master d on d.id=c.prod_id join wo_po_break_down e on e.id=c.po_breakdown_id where a.entry_form=19 and a.status_active=1 and a.is_deleted=0 and b.transaction_type=2 and b.status_active=1 and b.is_deleted=0 and c.trans_type=2 and c.entry_form=19 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 ".where_con_using_array($po_id_arr,0,'c.po_breakdown_id')."");
	$issue_qty_arr=array();

	foreach ($issue_qty_data as $row) {
		$issue_key=$row[csf('job_id')].'*'.$row[csf('detarmination_id')].'*'.$row[csf('color')];
		$issue_qty_arr[$issue_key]+=$row[csf('cons_quantity')];
	}

	$woven_finish_fabric_transfer=sql_select("SELECT b.transfer_qnty, b.to_order_id, c.detarmination_id, c.color, d.job_id  from inv_item_transfer_mst a join inv_item_transfer_dtls b on a.id=b.mst_id join product_details_master c on b.from_prod_id=c.id join wo_po_break_down d on b.from_order_id=d.id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  d.status_active=1 and d.is_deleted=0 ".where_con_using_array($po_id_arr,0,'b.from_order_id')." ".where_con_using_array($yarn_id_arr,0,'c.detarmination_id')." ");
	$wff_transfer_arr=array();
	foreach ($woven_finish_fabric_transfer as $row) {
		$wff_transfer_key=$row[csf('job_id')].'*'.$row[csf('detarmination_id')].'*'.$row[csf('color')];
		$wff_transfer_arr[$wff_transfer_key][$row[csf('to_order_id')]] = $row[csf('transfer_qnty')];
	}
	
	foreach ($wff_transfer_arr as $key=>$order_data) {
		$wff_transfer_row[$key]=count($order_data);
	}

	$max_transfer_row = max($wff_transfer_row);

	/*echo '<pre>';
	print_r($wff_transfer_row); die;*/

	$tbl_width=1360+($max_transfer_row*80);
	?>
	<div style="width:100%">
	<table width="<? echo $tbl_width;?>">
		<tr>
		    <td align="center" width="100%" colspan="44" class="form_caption">Advance Fabric Booking Status report<br><? echo $company_library[str_replace("'","",$cbo_company_name)].'<br/>';
			if($txt_date_from!="") echo  $txt_date_from.' To '.$txt_date_to;
			 ?></td>
		</tr>
	</table>
	<table width="<? echo $tbl_width;?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1">
		<thead>
			<tr>
				<th width="80" rowspan="2">Booking No</th>
				<th width="80" rowspan="2">PI No.</th>
				<th width="80" rowspan="2">BTB LC No.</th>
				<th width="80" rowspan="2">Supplier</th>
				<th width="280" rowspan="2">Item Name(Fabrication)</th>
				<th width="80" rowspan="2">Fabric Color</th>
				<th width="80" rowspan="2">Buyer</th>
				<th width="80" rowspan="2">Style Name</th>
				<th width="80" rowspan="2">Ordr No.</th>
				<th width="40" rowspan="2">PO QTY.</th>
				<th width="40" rowspan="2">Avg. Cons.</th>
				<th width="40" rowspan="2">Booking Qty</th>
				<th width="40" rowspan="2">Unit</th>
				<th width="40" rowspan="2">Recv Qty</th>
				<th width="80" rowspan="2">Own Order Issue Qty</th>
				<th width="<?= $max_transfer_row*80 ?>" colspan="<?= $max_transfer_row*2 ?>">Transfer to Other Order</th>
				<th width="80" rowspan="2">Total Issue</th>
				<th width="80" rowspan="2">Balance Qty</th>
			</tr>
			<tr>
				<? for($i=1; $i<=$max_transfer_row; $i++){ ?>					
					<th width="40">Order No</th>
					<th width="40">Trans Qty</th>
				<? } ?>
			</tr>
		</thead>
		<tbody>
			<?
			/*echo '<pre>';
			print_r($main_data_arr); die;*/
			foreach($main_data_arr as $value) {	
				$pikey=$value['job_id'].'*'.$value['lib_yarn_count_deter_id'].'*'.$value['gmts_color_id'];
				$pi_no=	$pi_data_arr[$pikey]['pi_number'];
				$rcvkey=$value['job_id'].'*'.$value['lib_yarn_count_deter_id'].'*'.$value['gmts_color_id'];
				$key=$value['fabric_id'].'*'.$value['gmts_color_id'];
				 $value['fin_fab_qnty']=0;
				 $value['fin_fab_qnty']=$fab_req_arr[$key]['fin_fab_qnty'];
			?>
			<tr>
				<td width="80"><?= $value['booking_no'] ?></td>
				<td width="80" title="<?= $pikey ?>"><?= $pi_no ?></td>
				<td width="80"><?= $pi_data_arr[$pikey]['blc_no'] ?></td>
				<td width="80"><?= $supplier_library[$value['supplier_id']]  ?></td>
				<td width="280"><?= $value['fabric_description'] ?></td>
				<td width="80"><?= $color_library[$value['gmts_color_id']] ?></td>
				<td width="80"><?= $buyer_arr[$value['buyer_id']] ?></td>
				<td width="80"><?= $value['style_ref_no'] ?></td>
				<td width="80"><?= implode(", ", $value['po_data']);  ?></td>
				<td width="40" align="right"><?= $value['plan_cut'] ?></td>
				<td width="40" align="right"><?= $value['avg_finish_cons']?></td>
				<td width="40" align="right"><?= number_format($value['fin_fab_qnty'],2)?></td>
				<td width="40"><?= $unit_of_measurement[$value['uom']]?></td>
				<td width="40" align="right"><?= number_format($receive_qty_arr[$rcvkey],2);  ?></td>
				<td width="80" align="right"><?= number_format($issue_qty_arr[$rcvkey],2);  ?></td>
				<?
					$total_transfer_issue=0;
					$k=1;
					if(count($wff_transfer_arr[$rcvkey])>0){
						foreach ($wff_transfer_arr[$rcvkey] as $poid=>$transferQty) { ?>
						<td width="40"><?= $po_name_arr[$poid]  ?></td>
						<td width="40" align="right"><?= $transferQty ?></td>
						<? 
						$colume_wise_total[$k]+=$transferQty;
						$total_transfer_issue+=$transferQty;
						$k++;
						} 
						if(count($wff_transfer_arr[$rcvkey])!=$max_transfer_row){
							for($i=1; $i<=$max_transfer_row-count($wff_transfer_arr[$rcvkey]); $i++){ ?>		
							<td width="40"></td>
							<td width="40" align="right">0</td>
							<?
							}
						}
					}
					else{
						for($i=1; $i<=$max_transfer_row; $i++){ ?>					
							<td width="40"></td>
							<td width="40" align="right">0</td>
							<?
						}
					}
					?>
					
				<td width="80" align="right"><?= number_format($issue_qty_arr[$rcvkey]+$total_transfer_issue,2);  ?></td>
				<td width="80" align="right"><?= number_format($receive_qty_arr[$rcvkey]-($issue_qty_arr[$rcvkey]+$total_transfer_issue),2);  ?></td>
			</tr>
			<? 
			$total_plan_cut_qty+=$value['plan_cut'];
			$total_booking_qty+=$value['fin_fab_qnty'];
			$total_receive_qty+=$receive_qty_arr[$rcvkey];
			$total_issue_qty+=$issue_qty_arr[$rcvkey];
			$grand_total_issue_qty+=$issue_qty_arr[$rcvkey]+$total_transfer_issue;
			$total_balance_qty+=$receive_qty_arr[$rcvkey]-($issue_qty_arr[$rcvkey]+$total_transfer_issue);
			} ?>
		</tbody>
		<tfoot>
			<th colspan="9" align="right">Total</th>
			<th align="right"><?= $total_plan_cut_qty ?></th>
			<th></th>
			<th align="right"><?= number_format($total_booking_qty,4) ?></th>
			<th></th>
			<th align="right"><?= number_format($total_receive_qty,4) ?></th>
			<th align="right"><?= number_format($total_issue_qty,4) ?></th>
			<?
			for($i=1; $i<=$max_transfer_row; $i++){ ?>					
				<th width="40"></th>
				<th width="40" align="right"><?= $colume_wise_total[$i] ?></th>
				<?
			}
			?>
			<th align="right"><?= number_format($grand_total_issue_qty,4) ?></th>
			<th align="right"><?= number_format($total_balance_qty,4) ?></th>
		</tfoot>		
	</table>
	<?
	//File Create
	foreach (glob("*.xls") as $filename) {
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
}
?>