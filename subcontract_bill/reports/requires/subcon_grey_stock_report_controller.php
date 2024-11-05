<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$order_arr=return_library_array( "select id, order_no from  subcon_ord_dtls", "id", "order_no");
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

if ($action=="load_drop_down_buyer")
{ 
	echo create_drop_down( "cbo_party_id", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "--Select Party--", $selected, "" );
	exit();   	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	ob_start();
	?>
	<div align="center">
	 <fieldset style="width:950px;">
		<table cellpadding="0" cellspacing="0" width="930">
			<tr  class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="8" style="font-size:20px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="8" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="8" style="font-size:12px">
					<? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
				</td>
			</tr>
		</table>
		<table width="927" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
			<thead>
                <tr>
                    <th width="40" rowspan="2">SL</th>
                    <th width="150" rowspan="2">Party</th>
                    <th width="110" rowspan="2">Opening Balance</th>
                    <th width="110" rowspan="2">Net Receive</th>
                    <th width="100" rowspan="2">Batch Qty</th>                            
                    <th width="100" rowspan="2">Bill Qty</th>
                    <th width="" colspan="2">Closing Balance</th>
                </tr>
                <tr>
                	<th width="100">Weight (Stock)</th>
                    <th width="">Fin. Delivery Qty</th>
                </tr>
			</thead>
		</table>
	<div style="max-height:300px; overflow-y:scroll; width:930px" id="scroll_body">
		<table width="910" border="1" class="rpt_table" rules="all" id="table_body">
		<?
			if(str_replace("'","",$cbo_party_id)==0) $party_rec_cond=""; else  $party_rec_cond=" and a.party_id=$cbo_party_id";
			if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
			if(str_replace("'","",$cbo_party_id)==0) $party_lib_cond=""; else  $party_lib_cond=" and buy.id=$cbo_party_id";

			if(str_replace("'","",$cbo_bill_type)==0) $bill_type_cond=""; else  $bill_type_cond=" and b.bill_type=$cbo_bill_type";
			if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
			
			if($db_type==0)
			{
				if( $date_from==0 && $date_to==0 ) $receive_date_cond=""; else $receive_date_cond= " and a.subcon_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
				if( $date_from==0 && $date_to==0 ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
				if( $date_from==0 && $date_to==0 ) $batch_date_cond=""; else $batch_date_cond= " and d.batch_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
				if( $date_from==0 && $date_to==0 ) $delivery_date_cond=""; else $delivery_date_cond= " and a.delivery_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
			else if($db_type==2)
			{
				if( $date_from==0 && $date_to==0 ) $receive_date_cond=""; else $receive_date_cond= " and a.subcon_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
				if( $date_from=='' && $date_to=='' ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
				if( $date_from=='' && $date_to=='' ) $batch_date_cond=""; else $batch_date_cond= " and d.batch_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
				if( $date_from=='' && $date_to=='' ) $delivery_date_cond=""; else $delivery_date_cond= " and a.delivery_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
			}
			
			if($date_from=="") $bill_date=""; else $bill_date= " and a.bill_date <".$txt_date_from."";
			if($date_from=="") $receive_cond=""; else $receive_cond= " and a.subcon_date <".$txt_date_from."";
			
			$open_rec_array=array();
			$sql_open_rec=sql_select("select a.party_id, sum(b.quantity) as rec_qty from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 $party_rec_cond $receive_cond group by a.party_id");
		// echo "select a.party_id, sum(b.quantity) as rec_qty from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 $party_rec_cond $receive_cond group by a.party_id";
			foreach ($sql_open_rec as $row)
			{
				$open_rec_array[$row[csf("party_id")]]=$row[csf("rec_qty")];
			}
			
			$opening_bill_arr=array();
			$sql_open_bill=sql_select("select a.party_id, sum(b.delivery_qty) as bill_qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date $process_cond group by a.party_id order by a.party_id");
			//echo "select a.party_id, sum(b.delivery_qty) as bill_qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date $process_cond group by a.party_id order by a.party_id";
			foreach ($sql_open_bill as $row)
			{
				$opening_bill_arr[$row[csf("party_id")]]=$row[csf("bill_qty")];
			}
			
			$batch_qty_array=array();
			$sql_batch=sql_select("select a.party_id, sum(b.batch_qnty) as batch_qnty from subcon_ord_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, pro_batch_create_mst d where a.subcon_job=c.job_no_mst and b.po_id=c.id and d.id=b.mst_id and d.entry_form=36 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  $party_rec_cond $batch_date_cond group by a.party_id");
		 // echo "select a.party_id, sum(b.batch_qnty) as batch_qnty from subcon_ord_mst a, pro_batch_create_dtls b, subcon_ord_dtls c, pro_batch_create_mst d where a.subcon_job=c.job_no_mst and b.po_id=c.id and d.id=b.mst_id and d.entry_form=36 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_rec_cond $batch_date_cond group by a.party_id";
			foreach ($sql_batch as $row)
			{
				$batch_qty_array[$row[csf("party_id")]]=$row[csf("batch_qnty")];
			}			
			// $sql_adj[0][csf('pre_adjusted')];
			
			$rec_array=array();
			$sql_rec=sql_select("select a.party_id, sum(b.quantity) as rec_qty from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 $party_rec_cond $receive_date_cond group by a.party_id");
		  // echo "select a.party_id, sum(b.quantity) as rec_qty from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=2 and b.is_deleted=0 and a.trans_type=1 $party_rec_cond $receive_date_cond group by a.party_id";
			foreach ($sql_rec as $row)
			{
				$rec_array[$row[csf("party_id")]]=$row[csf("rec_qty")];
			}
			
			if ($db_type==0)
			{
				$sql_issue="select a.party_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_rec_cond group by a.party_id";
				$sql_return="select a.party_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and b.item_category_id in (2,13) and a.trans_type=3 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_rec_cond group by a.party_id";
			}
			elseif($db_type==2)
			{
				$sql_issue="select a.party_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=2 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_rec_cond group by a.party_id"; 
				$sql_return="select a.party_id, sum(b.quantity) as quantity from sub_material_mst a, sub_material_dtls b where a.id=b.mst_id and a.trans_type=3 and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_rec_cond group by a.party_id"; 
			}
			
			$nameArray_issue=sql_select($sql_issue);
			foreach ($nameArray_issue as $row)
			{
				$material_issue_arr[$row[csf('party_id')]]=$row[csf('quantity')];
			}
			$nameArray_return=sql_select($sql_return);
			foreach($nameArray_return as $row)
			{
				$material_return_arr[$row[csf('party_id')]]=$row[csf('quantity')];
			}
			
			$bill_qty_arr=array();
			$sql_bill=sql_select("select a.party_id, sum(b.delivery_qty) as bill_qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id  and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date_cond $process_cond group by a.party_id order by a.party_id");
			foreach ($sql_bill as $row)
			{
				$bill_qty_arr[$row[csf("party_id")]]=$row[csf("bill_qty")];
			}
			
			$delivery_qty_arr=array();
			$sql_delivery=sql_select("select a.party_id, sum(b.delivery_qty) as delivery_qty from subcon_delivery_mst a, subcon_delivery_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and b.process_id in (3,4) and a.status_active=1 and a.is_deleted=0 $party_bill_cond $delivery_date_cond $process_cond group by a.party_id order by a.party_id");
			foreach ($sql_delivery as $row)
			{
				$delivery_qty_arr[$row[csf("party_id")]]=$row[csf("delivery_qty")];
			}
			//echo $trans_data;
		 // var_dump($pay_recv_array);die;
		   
		   $sql_party="select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$cbo_company_id $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) $party_lib_cond order by buy.buyer_name";
			
			$sql_party_result=sql_select($sql_party);
			$i=1;
			foreach ($sql_party_result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$issue_qty=$material_issue_arr[$row[csf("id")]];
				$return_qty=$material_return_arr[$row[csf("id")]];
				$opening_bal=$open_rec_array[$row[csf("id")]]-$opening_bill_arr[$row[csf("id")]];
				$receive_qty=$rec_array[$row[csf("id")]]-$issue_qty-$return_qty;
				
				$batch_qty=$batch_qty_array[$row[csf("id")]];
				$bill_qty=$bill_qty_arr[$row[csf("id")]];
				$closing_stock=($opening_bal+$receive_qty)-$bill_qty;
				$delivery_qty=$delivery_qty_arr[$row[csf("id")]];
				
				?>
				<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					 <td width="40" ><? echo $i; ?></td>
					 <td width="150" ><p><? echo $row[csf('buyer_name')]; ?></p></td>
					 <td width="110" align="right" ><? echo number_format($opening_bal,2,'.',','); ?></td>
					 <td width="110" align="right" ><? echo number_format($receive_qty,2,'.',','); ?></td>
					 <td width="100" align="right" ><? echo number_format($batch_qty,2,'.',','); ?></td>
					 <td width="100" align="right" ><? echo number_format($bill_qty,2,'.',','); ?></td>
					 <td width="100" align="right" title="(Opening+Receive)-Bill Qty"><? echo number_format($closing_stock,2,'.',','); ?></td>
					 <td align="right" ><? echo number_format($delivery_qty,2,'.',','); ?></td>
				</tr>
				<?	
				$i++;
				$tot_opening+=$opening_bal;
				$tot_receive+=$receive_qty;
				$tot_batch+=$batch_qty;
				$tot_bill+=$bill_qty;
				$tot_closing+=$closing_stock;
				$tot_delivery+=$delivery_qty;
			}
			?>
			<tr class="tbl_bottom">
				<td colspan="2" align="right"><b>Total:</b></td>
				<td align="right"><b><? echo number_format($tot_opening,2); ?></b></td>
				<td align="right"><b><? echo number_format($tot_receive,2); ?></b></td>
				<td align="right"><b><? echo number_format($tot_batch,2); ?></b></td>
				<td align="right"><b><? echo number_format($tot_bill,2); ?></b></td>
				<td align="right"><b><? echo number_format($tot_closing,2); ?></b></td>
				<td align="right"><b><? echo number_format($tot_delivery,2); ?></b></td>
			</tr>
		</table>
		</div>
		</fieldset>
		</div>
	<?
	exit();
}
?>