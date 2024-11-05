<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

if($action=="load_drop_down_buyer")
{
	//if($data[1]==1) $party="1,3,21,90"; else $party="80";
	$party="1,3,21,90";
	echo create_drop_down( "cbo_buyer_id", 110, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_from_store")
{
	echo create_drop_down( "cbo_from_store_id", 110, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(2)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if ($action=="load_drop_down_to_store")
{
	echo create_drop_down( "cbo_to_store_id", 110, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(2)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$cbo_transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_from_store_id=str_replace("'","",$cbo_from_store_id);
	$cbo_to_store_id=str_replace("'","",$cbo_to_store_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$search_cond="";
	if($cbo_transfer_criteria) $search_cond .=" and a.transfer_criteria = $cbo_transfer_criteria";
	if($cbo_buyer_id > 0) $search_cond .=" and f.buyer_name=$cbo_buyer_id";
	if($cbo_from_store_id) $search_cond .=" and b.from_store=$cbo_from_store_id";
	if($cbo_to_store_id) $search_cond .=" and b.to_store=$cbo_to_store_id";

	if($date_from !="" && $date_to != "")
	{
		$search_cond .= " and a.transfer_date between '$date_from' and '$date_to' ";
	}

	$cbo_year_val=str_replace("'","",$cbo_year);
	$order_no=str_replace("'","",$txt_order_id);
	if(str_replace("'","",$txt_order_id)!="" && str_replace("'","",$txt_order_id)!=0) $order_id_cond_trans=" and c.id in ($order_no)";
	
	$sql_query="SELECT a.id, a.transfer_system_id, a.transfer_date, b.from_prod_id, b.from_order_id, b.no_of_roll, b.from_store, b.to_store, b.transfer_qnty, b.remarks, c.color, c.product_name_details, d.batch_no, e.po_number, f.job_no,d.booking_no, f.buyer_name, f.style_ref_no from inv_item_transfer_mst a, inv_item_transfer_dtls b, product_details_master c, pro_batch_create_mst d,  wo_po_break_down e, wo_po_details_master f where a.id = b.mst_id and b.from_order_id = e.id and e.job_no_mst = f.job_no and b.from_prod_id = c.id and b.batch_id = d.id and a.company_id = $cbo_company_id $search_cond and a.entry_form in (14,15) and a.status_active = 1 and b.status_active =1 and b.active_dtls_id_in_transfer = 1";
	$nameArray=sql_select($sql_query);
	foreach ($nameArray as $row)
	{
		$buyer_wise_summary[$row[csf("buyer_name")]]["roll"] += $row[csf("no_of_roll")];
		$buyer_wise_summary[$row[csf("buyer_name")]]["qnty"] += $row[csf("transfer_qnty")];
	}
	
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_arr=return_library_array( "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$cbo_company_id and  b.category_type in(2)  group by a.id,a.store_name", "id", "store_name"  );
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

	ob_start();
	$summary_html = "";
	?>
	<style type="text/css">
		.word_wrap_break {
			word-break: break-all;
			word-wrap: break-word;
		}
	</style>
	<fieldset style="width:1270px;">
		<table cellpadding="0" cellspacing="0" width="1270">
			<tr  class="form_caption" style="border:none;">
				<td align="center"  colspan="13" style="font-size:18px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center"  colspan="13" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center"  colspan="13" style="font-size:14px;color: black;"><strong> <? if($date_from!="") echo "From Store: ".$store_arr[$cbo_from_store_id].", To Store: ".$store_arr[$cbo_to_store_id];?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center"  colspan="13" style="font-size:14px"><strong> <? if($date_from!="") echo "From Date : ".change_date_format(str_replace("'","",$txt_date_from)).",  To Date : ".change_date_format(str_replace("'","",$txt_date_to));?></strong></td>
			</tr>
		</table>

		<?
			$summary_html .= '<table cellpadding="0" cellspacing="0" width="600">
			<tr  class="form_caption" style="border:none;"><td align="center" colspan="13" style="font-size:16px"><strong>'. $company_arr[str_replace("'","",$cbo_company_id)].'</strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" colspan="13" style="font-size:18px"><strong>'. $report_title .' </strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" colspan="13" style="font-size:14px;color: black;"><strong>';

				 if($date_from!="") $summary_html .= "From Store: ".$store_arr[$cbo_from_store_id].", To Store: ".$store_arr[$cbo_to_store_id];
				  $summary_html .= '</strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" colspan="13" style="font-size:14px"><strong>';
				 if($date_from!="") $summary_html .= "From Date : ".change_date_format(str_replace("'","",$txt_date_from)).",  To Date : ".change_date_format(str_replace("'","",$txt_date_to));

				 $summary_html .='</strong></td>
			</tr>
		</table><br>
		<table width="500" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
			<thead>
				<tr>
					<th colspan="3">Buyer Wise Summary</th>
				</tr>
				<tr>
					<th>Buyer</th>
					<th>Transfer Qty (Kg)</th>
					<th>No Of Roll</th>
				</tr>
			</thead>
			<tbody>'; 
		?>

		<table width="320" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
			<thead>
				<tr>
					<th colspan="3">Buyer Wise Summary</th>
				</tr>
				<tr>
					<th>Buyer</th>
					<th>Transfer Qty (Kg)</th>
					<th>No Of Roll</th>
				</tr>
			</thead>
			<tbody>
				<? 
				$j=1;
				foreach ($buyer_wise_summary as $buyer_id => $row) 
				{
					if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>">
						<td width="120"><? echo $buyer_arr[$buyer_id];?></td>
						<td width="100" align="right"><? echo number_format($row["qnty"],2);?></td>
						<td width="100" align="right"><? echo number_format($row["roll"],2);?></td>
					</tr>
					<?
					$j++;
					$grand_total_roll += $row["roll"];
					$grand_total_qnty += $row["qnty"];

					$summary_html .= '<tr bgcolor="'.$bgcolor .'">
						<td width="200">'. $buyer_arr[$buyer_id].'</td>
						<td width="150" align="right">'. number_format($row["qnty"],2).'</td>
						<td width="150" align="right">'. number_format($row["roll"],2).'</td>
					</tr>';
				}
				$summary_html .= '</tbody><tfoot><tr>
					<th align="right">Grand Total :</th>
					<th align="right">'.number_format($grand_total_qnty,2).'</th>
					<th align="right">'.number_format($grand_total_roll,2).'</th></tr></tfoot></table>';

				?>
			</tbody>
			<tfoot>
				<tr>
					<th align="right">Grand Total :</th>
					<th align="right"><? echo number_format($grand_total_qnty,2);?></th>
					<th align="right"><? echo number_format($grand_total_roll,2);?></th>
				</tr>
			</tfoot>
		</table>

		<br>

		<span  style="width:1270px;text-align: left; font-weight: bold;">Details :</span>
		<table width="1270" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
			<thead>
				<tr>
					<th></th>
					<th colspan="5">From Store Order Information</th>
					<th colspan="7">Fabric Transfer Information</th>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="70">Buyer</th>
					<th width="90">Job</th>
					<th width="100">Booking No.</th>
					<th width="110">Style</th> 
					<th width="100">Order</th>
					<th width="90">Batch</th>
					<th width="90">Fin. Color</th>
					<th width="140">Fabric Description</th>
					<th width="50">No of Roll</th>
					<th width="80">Transfer Qty (Kg)</th>
					<th width="120">Trans. Ref.</th>
					<th width="100">Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:1290px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="1270" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
				<?
				$i=1;
				foreach ($nameArray as $row)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
						<td width="30"><? echo $i;?></td>
						<td width="70" class="word_wrap_break"><p><? echo $buyer_arr[$row[csf("buyer_name")]];?></p></td>
						<td width="90"><? echo $row[csf("job_no")];?></td>
						<td width="100"><? echo $row[csf("booking_no")];?></td>
						<td width="110" class="word_wrap_break"><p><? echo $row[csf("style_ref_no")];?></p></td> 
						<td width="100" class="word_wrap_break" align="center"><p><? echo $row[csf("po_number")];?></p></td>
						<td width="90" class="word_wrap_break" align="center"><p><? echo $row[csf("batch_no")];?></p></td>
						<td width="90"><? echo $color_arr[$row[csf("color")]];?></th>
						<td width="140" class="word_wrap_break" align="center"><p><? echo $row[csf("product_name_details")];?></p></td>
						<td width="50" align="right"><? echo $row[csf("no_of_roll")];?></td>
						<td width="80" align="right"><? echo $row[csf("transfer_qnty")];?></td>
						<td width="120" align="center"><a href="##" onclick="print_report_transfer_2('<? echo $row[csf("id")]?>');"><? echo $row[csf("transfer_system_id")];?></a></td>
						<td width="100" class="word_wrap_break"><p><? echo $row[csf("remarks")];?></p></td>
					</tr>
					<?
					$total_qnty += $row[csf("transfer_qnty")];
					$total_roll += $row[csf("no_of_roll")];
					$i++;
				}
				?>								
			</table>
			</div> 
			<table width="1270" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="110">&nbsp;</th> 
						<th width="100">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="90">&nbsp;</th>
						<th width="140">Grand Total:</th>
						<th width="50" align="right" id="value_trans_roll"><? echo $total_roll;?></th>
						<th width="80" align="right" id="value_trans_qnty"><? echo $total_qnty;?></th>
						<th width="120">&nbsp;</th>
						<th width="100">&nbsp;</th>
					</tr>
				</tfoot>
			</table> 
		</fieldset>         
		<?
	
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
    	@unlink($filename);
    }
    
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    //$filename_summary = $user_id."_".$name . "short.xls";
    $create_new_doc = fopen($filename, 'w');
    //$create_new_doc_summary = fopen($filename_summary, 'w');
    $is_created = fwrite($create_new_doc, $html);
    //$is_created_short = fwrite($create_new_doc_summary, $summary_html);
    echo "$html**$filename**$summary_html"; 
    exit();
}
?>