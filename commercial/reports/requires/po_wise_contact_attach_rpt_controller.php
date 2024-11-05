<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');

//report generated here--------------------//
if($action=="generate_report")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	$txt_job_no= str_replace("'","",$txt_job_no);       
    $txt_style_no= str_replace("'","",$txt_style_no);   
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	
	if($cbo_buyer_name!=0) $cbo_buyer_name="and b.buyer_name=$cbo_buyer_name"; else $cbo_buyer_name="";
	if($txt_style_ref!="")
	{
		if($txt_style_ref_id!="")
		{
			$txt_style_ref_id="and b.id in($txt_style_ref_id)";
		}
		else
		{
			$txt_style_ref_id="and b.style_ref_no='$txt_style_ref'"; 

		}
	}
	else
	{
		 $txt_style_ref_id="";
	}
	$sql_cond="";
	if($txt_order_no!="") 
	{
		$sql_cond=" and a.po_number='$txt_order_no'";
	}
	
	if ($txt_job_no!='') {$sql_cond.=" and a.job_no_mst='$txt_job_no'";}
 	if ($txt_int_ref_no!='') {$sql_cond.=" and a.grouping='$txt_int_ref_no'";}
 	if(	$txt_date_from !="" && $txt_date_to !="" ) $sql_cond.=" and a.pub_shipment_date between '$txt_date_from' and '$txt_date_to'";
 	//$sql_style_cond="";
	if ($txt_style_no!='') {$sql_cond.=" and d.style_ref_no='$txt_style_no'";} 
	
	/*$sql_style="select a.id as order_id
	from wo_po_break_down a,wo_po_details_master b 
	where  b.company_name=$cbo_company_name  and a.job_no_mst=b.job_no and b.id=a.job_id   and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $sql_cond $sql_style_cond
	group by a.id";  
	$style_sql_result=sql_select($sql_style);
	$style_data_arr=array();
	foreach($style_sql_result as $row)
	{
		$style_data_arr[]=$row[csf("order_id")];
	}
 	//echo count($style_data_arr); die;
	if(count($style_data_arr)>0) $sql_cond.=" and a.id in(".implode(",",$style_data_arr).")";*/
	
	
	
	  $sql_order="select a.id as order_id, a.po_number, a.po_quantity, a.po_total_price, a.unit_price, c.id as lc_sc_id, c.contract_no as lc_sc_no, sum(b.attached_qnty) as attached_qnty, sum(b.attached_value) as attached_value, 2 as type
	from wo_po_break_down a,wo_po_details_master d, com_sales_contract_order_info b, com_sales_contract c
	where  a.job_no_mst=d.job_no and d.id=a.job_id  and a.id=b.wo_po_break_down_id and b.com_sales_contract_id=c.id and c.beneficiary_name=$cbo_company_name and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 $sql_cond
	group by a.id, a.po_number, a.po_quantity, a.po_total_price, a.unit_price, c.id, c.contract_no
	union all
	select a.id as order_id, a.po_number, a.po_quantity, a.po_total_price, a.unit_price, c.id as lc_sc_id, c.export_lc_no as lc_sc_no, sum(b.attached_qnty) as attached_qnty, sum(b.attached_value) as attached_value, 1 as type
	from wo_po_break_down a,wo_po_details_master d , com_export_lc_order_info b, com_export_lc c 
	where a.job_no_mst=d.job_no and d.id=a.job_id  and  a.id=b.wo_po_break_down_id and b.com_export_lc_id=c.id  and  c.beneficiary_name=$cbo_company_name and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 $sql_cond
	group by a.id, a.po_number, a.po_quantity, a.po_total_price, a.unit_price, c.id, c.export_lc_no
	order by order_id";
	//echo $sql_order;die;
	$sql_order_result=sql_select($sql_order);
	$invoice_sql="select c.is_lc, c.lc_sc_id, b.po_breakdown_id, sum(b.current_invoice_qnty) as inv_qnty
	from wo_po_break_down a, com_export_invoice_ship_dtls b, com_export_invoice_ship_mst c
	where a.id=b.po_breakdown_id and b.mst_id=c.id and c.benificiary_id=$cbo_company_name and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 $sql_cond
	group by c.is_lc, c.lc_sc_id, b.po_breakdown_id";
	//echo $invoice_sql;die;
	$invoice_sql_result=sql_select($invoice_sql);
	$invoice_data_arr=array();
	foreach($invoice_sql_result as $row)
	{
		$invoice_data_arr[$row[csf("po_breakdown_id")]][$row[csf("lc_sc_id")]][$row[csf("is_lc")]]+=$row[csf("inv_qnty")];
	}
	//var_dump($invoice_data_arr);die;
	//echo $sql;die;
	
	$i=1;
	ob_start();	
	?>
	<div style="width:1200"> 
        <table width="1200" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
            <tr style="border:none;">
                <td colspan="12" align="center" style="border:none; font-size:14px;"> Company Name : <? echo $company_arr[$cbo_company_name]; ?></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td> 
            </tr>
        </table>
        <br />
        <table width="1200" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
            <thead>
                <tr>
                    <th width="50">SL</th>
                    <th width="120">PO No</th>
                    <th width="100">PO Qty</th>
                    <th width="100">PO Value</th>
                    <th width="120">Attache Con. No.</th>
                    <th width="100">Attache Qty</th>
                    <th width="100">Attached value</th>
                    <th width="100">Cum. Balance</th>
                    <th width="100">Invoice Qty</th>
                    <th width="100">Invoice Value</th>
                    <th width="100">Balance Qty</th>
                    <th>Balance Value</th>
                </tr>
            </thead>
        </table> 
		<div style="width:1220px; overflow-y: scroll; max-height:250px;" id="scroll_body">
		<table width="1200" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
			<?
			$order_arr=array();$i=1;$p=1;
			$sub_total_inv_qty=0;
			$sub_total_inv_value=0;
			$grand_total_qty=0;
			$grand_total_val=0;
			foreach($sql_order_result as $row)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				if($order_check[$row[csf("order_id")]]=="")
				{
					$order_check[$row[csf("order_id")]]=$row[csf("order_id")];
					$cu_bal=$row[csf("po_quantity")]-$row[csf("attached_qnty")];
					$po_number=$row[csf("po_number")];
					$po_quantity=$row[csf("po_quantity")];
					$po_total_price=$row[csf("po_total_price")];
					if($p!=1)
					{
						?>
                        <tr bgcolor="#FFFFCC">
                            <td colspan="2" align="right">Order Total:</td>
                            <td align="right"><? echo number_format($order_qnty,2); ?></td>
                            <td align="right"><? echo number_format($order_value,2); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($order_attached_qnty,2); ?></td>
                            <td align="right"><? echo number_format($order_attached_value,2); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($order_invoice_qnty,2); ?></td>
                            <td align="right"><? echo number_format($order_invoice_value,2); ?></td>
                            <td align="right"><? echo number_format($order_bal_inv_qnty,2); ?></td>
                            <td align="right"><? echo number_format($order_bal_inv_value,2); ?></td>
                        </tr>
                        <?
						$order_attached_qnty=$order_attached_value=$order_invoice_qnty=$order_invoice_value=$order_bal_inv_qnty=$order_bal_inv_value=0;
					}
					$p++;
				}
				else
				{
					$po_number="";
					$po_quantity="";
					$po_total_price="";
					$cu_bal=$cu_bal-$row[csf("attached_qnty")];
				}
				?>
				<tr bgcolor="<? echo $bgcolor;?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center" width="50"><? echo $i; ?></td>
					<td width="120"><p><? echo $po_number; ?>&nbsp;</p></td>
					<td width="100" align="right"><? echo number_format($po_quantity,2); ?></td>
					<td width="100" align="right"><? echo number_format($po_total_price,2); ?></td>
                    <td width="120"><p><? echo $row[csf("lc_sc_no")]; ?>&nbsp;</p></td>
                    <td width="100" align="right"><? echo number_format($row[csf("attached_qnty")],2); ?></td>                    
                    <td width="100" align="right"><? echo number_format($row[csf("attached_value")],2); ?></td>
					<td width="100" align="right"><? echo number_format($cu_bal,2); ?></td>
					<td width="100" align="right"><? echo number_format($invoice_data_arr[$row[csf("order_id")]][$row[csf("lc_sc_id")]][$row[csf("type")]],2); ?></td>
					<td width="100" align="right"><? $inv_value=$invoice_data_arr[$row[csf("order_id")]][$row[csf("lc_sc_id")]][$row[csf("type")]]*$row[csf("unit_price")]; echo number_format($inv_value,2); ?></td>
                    <td width="100" align="right"><? $bal_inv_qnty=$row[csf("attached_qnty")]-$invoice_data_arr[$row[csf("order_id")]][$row[csf("lc_sc_id")]][$row[csf("type")]]; echo number_format($bal_inv_qnty,2); ?></td>
                    <td align="right"><? $bal_inv_value=$bal_inv_qnty*$row[csf("unit_price")];  echo number_format($bal_inv_value,2); ?></td>
				</tr>
				<?
				$tot_po_qnty+=$po_quantity;$order_qnty =$po_quantity;
				$tot_po_value+=$po_total_price;$order_value =$po_total_price;
				$tot_attached_qnty+=$row[csf("attached_qnty")];$order_attached_qnty+=$row[csf("attached_qnty")];
				$tot_attached_value+=$row[csf("attached_value")];$order_attached_value+=$row[csf("attached_value")];
				$tot_invoice_qnty+=$invoice_data_arr[$row[csf("order_id")]][$row[csf("lc_sc_id")]][$row[csf("type")]];$order_invoice_qnty+=$invoice_data_arr[$row[csf("order_id")]][$row[csf("lc_sc_id")]][$row[csf("type")]];
				$tot_invoice_value+=$inv_value;$order_invoice_value+=$inv_value;
				$tot_bal_inv_qnty+=$bal_inv_qnty;$order_bal_inv_qnty+=$bal_inv_qnty;
				$tot_bal_inv_value+=$bal_inv_value;$order_bal_inv_value+=$bal_inv_value;
				$i++;
			}
			?>    
			<tr bgcolor="#ccc">
				<td colspan="2" align="right"><b>Grand Total</b></td>
				<td align="right"><b><? echo number_format($tot_po_qnty,2); ?></b> </td>
				<td align="right"><b><? echo number_format($tot_po_value,2); ?></b></td>
				<td>&nbsp;</td>
                <td align="right"><b><? echo number_format($tot_attached_qnty,2); ?></b></td>
                <td align="right"><b><? echo number_format($tot_attached_value,2); ?></b></td>
				<td >&nbsp;</td>
                <td align="right"><b><? echo number_format($tot_invoice_qnty,2); ?></b></td>
                <td align="right"><b><? echo number_format($tot_invoice_value,2); ?></b></td>
				<td align="right"><b><? echo number_format($tot_bal_inv_qnty,2); ?></b></td>
                <td align="right"><b><? echo number_format($tot_bal_inv_value,2); ?></b></td>
			</tr>
		</table>
	 </div>
   </div>    
	<?	 
	
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("$user_id*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();
	disconnect($con);
}
?>

