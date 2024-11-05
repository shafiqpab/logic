<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}
if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "SELECT id,location_name from lib_location where company_id='$data' and status_active=1 and is_deleted=0 order by location_name","id,location_name", 1, "-- All Location --", $selected, "" ,0);
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$txt_order_no=str_replace("'","",$txt_order_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	$txt_invoice_no=str_replace("'","",$txt_invoice_no);
	$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);

	if($cbo_company_name!=0) $str_cond = " and e.beneficiary_name = $cbo_company_name ";	
	if($cbo_buyer_name != 0) $str_cond .=" and a.buyer_id = $cbo_buyer_name ";
	if($cbo_location != 0) $str_cond .=" and a.location_id = $cbo_location ";	
	if($txt_order_no!="") $str_cond .=" and c.po_number like '%$txt_order_no%'";
	if($txt_style_ref!="") $str_cond .=" and d.style_ref_no like '%$txt_style_ref%'";
	if($txt_invoice_no!="") $str_cond .=" and a.invoice_no like '%$txt_invoice_no%'";

	if ($txt_date_from!="" && $txt_date_to!="")
	{
		$str_cond.=" and a.invoice_date between '$txt_date_from' and  '$txt_date_to'";
	}
	
	/*$sql_order_set=sql_select("SELECT a.mst_id, a.po_breakdown_id, a.current_invoice_qnty, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs, c.total_set_qnty from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and a.status_active=1 and a.is_deleted=0");
	$inv_qnty_pcs_arr=array();
	foreach($sql_order_set as $row)
	{
		$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
	}*/
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$inv_qnty_pcs_arr=return_library_array("SELECT po_breakdown_id,sum(current_invoice_qnty) as current_invoice_qnty from com_export_invoice_ship_dtls where status_active=1 and is_deleted=0 group by po_breakdown_id","po_breakdown_id","current_invoice_qnty");

	/*$sql="SELECT * from (SELECT a.benificiary_id as BENIFICIARY_ID, a.location_id as LOCATION_ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE, a.buyer_id as BUYER_ID, a.is_lc as IS_LC, a.exp_form_no as EXP_FORM_NO, b.current_invoice_qnty as CURRENT_INVOICE_QNTY, b.current_invoice_value as CURRENT_INVOICE_VALUE, b.current_invoice_rate as CURRENT_INVOICE_RATE, c.id as PO_ID, c.po_number as PO_NUMBER, c.po_quantity as PO_QUANTITY, d.style_ref_no as STYLE_REF_NO, d.gmts_item_id as GMTS_ITEM_ID, e.export_lc_no as LC_SC_NO
	FROM com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c, wo_po_details_master d, com_export_lc e
	WHERE a.id=b.mst_id and b.po_breakdown_id=c.id and c.job_id=d.id and a.is_lc=1 and a.lc_sc_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond
	UNION ALL
	SELECT a.benificiary_id as BENIFICIARY_ID, a.location_id as LOCATION_ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE, a.buyer_id as BUYER_ID, a.is_lc as IS_LC, a.exp_form_no as EXP_FORM_NO, b.current_invoice_qnty as CURRENT_INVOICE_QNTY, b.current_invoice_value as CURRENT_INVOICE_VALUE, b.current_invoice_rate as CURRENT_INVOICE_RATE, c.id as PO_ID, c.po_number as PO_NUMBER, c.po_quantity as PO_QUANTITY, d.style_ref_no as STYLE_REF_NO, d.gmts_item_id as GMTS_ITEM_ID, e.contract_no as LC_SC_NO
	FROM com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b, wo_po_break_down c, wo_po_details_master d, com_sales_contract e
	WHERE a.id=b.mst_id and b.po_breakdown_id=c.id and c.job_id=d.id and a.is_lc=2 and a.lc_sc_id=e.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.contract_no LIKE '%$txt_lc_sc_no%' $str_cond)
	ORDER BY PO_ID";*/
	$sql="SELECT * from (SELECT e.beneficiary_name as BENIFICIARY_ID, a.location_id as LOCATION_ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE, a.buyer_id as BUYER_ID, a.is_lc as IS_LC, a.exp_form_no as EXP_FORM_NO, b.current_invoice_qnty as CURRENT_INVOICE_QNTY, b.current_invoice_value as CURRENT_INVOICE_VALUE, b.current_invoice_rate as CURRENT_INVOICE_RATE, c.id as PO_ID, c.po_number as PO_NUMBER, c.po_quantity as PO_QUANTITY, d.style_ref_no as STYLE_REF_NO, d.gmts_item_id as GMTS_ITEM_ID, e.export_lc_no as LC_SC_NO
	FROM  wo_po_break_down c, wo_po_details_master d, com_export_lc e,com_export_lc_order_info f
	left join com_export_invoice_ship_mst a on a.lc_sc_id=f.com_export_lc_id and a.is_lc=1 and a.status_active=1 and a.is_deleted=0
	left join com_export_invoice_ship_dtls b on f.wo_po_break_down_id=b.po_breakdown_id and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0
	WHERE f.com_export_lc_id=e.id and f.wo_po_break_down_id=c.id and c.job_id=d.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.export_lc_no LIKE '%$txt_lc_sc_no%' $str_cond
	group by e.beneficiary_name, a.location_id, a.invoice_no, a.invoice_date, a.buyer_id, a.is_lc, a.exp_form_no, b.current_invoice_qnty, b.current_invoice_value, b.current_invoice_rate, c.id, c.po_number, c.po_quantity, d.style_ref_no, d.gmts_item_id, e.export_lc_no
	UNION ALL
	SELECT e.beneficiary_name as BENIFICIARY_ID, a.location_id as LOCATION_ID, a.invoice_no as INVOICE_NO, a.invoice_date as INVOICE_DATE, a.buyer_id as BUYER_ID, a.is_lc as IS_LC, a.exp_form_no as EXP_FORM_NO, b.current_invoice_qnty as CURRENT_INVOICE_QNTY, b.current_invoice_value as CURRENT_INVOICE_VALUE, b.current_invoice_rate as CURRENT_INVOICE_RATE, c.id as PO_ID, c.po_number as PO_NUMBER, c.po_quantity as PO_QUANTITY, d.style_ref_no as STYLE_REF_NO, d.gmts_item_id as GMTS_ITEM_ID, e.contract_no as LC_SC_NO
	FROM wo_po_break_down c, wo_po_details_master d, com_sales_contract e, com_sales_contract_order_info f
	left join com_export_invoice_ship_mst a on a.lc_sc_id=f.com_sales_contract_id and a.is_lc=2 and a.status_active=1 and a.is_deleted=0
	left join com_export_invoice_ship_dtls b on f.wo_po_break_down_id=b.po_breakdown_id and a.id=b.mst_id and b.status_active=1 and b.is_deleted=0
	WHERE f.com_sales_contract_id=e.id and f.wo_po_break_down_id=c.id and c.job_id=d.id and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and e.contract_no LIKE '%$txt_lc_sc_no%' $str_cond
	group by e.beneficiary_name, a.location_id, a.invoice_no, a.invoice_date, a.buyer_id, a.is_lc, a.exp_form_no, b.current_invoice_qnty, b.current_invoice_value, b.current_invoice_rate, c.id, c.po_number, c.po_quantity, d.style_ref_no, d.gmts_item_id, e.contract_no)
	ORDER BY PO_ID";
	// echo $sql;die;
	$sql_data=sql_select($sql);
	foreach($sql_data as $val)
	{
		$po_count[$val["PO_ID"]]++;
	}
	ob_start();

	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
	</style>

	<div style="width:1350px"> 
		<table width="1350" cellpadding="0" cellspacing="0" id="caption">
	        <tr>
	        <td align="center" width="100%" colspan="15" class="form_caption" ><strong style="font-size:18px"><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	        </tr>
	    </table>
		<br />           
        <table width="1350" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
	        <thead>
	            <tr>
	                <th width="30">Sl</th>
	                <th width="100">Company</th>
	                <th width="100">Order No</th>
	                <th width="100">Style Ref No</th>
	                <th width="80">Order Qty</th>
	                <th width="80">EX FACTORY QNT</th>
	                <th width="80">Balance</th>
	                <th width="80">Invoice Wise Rate</th>
	                <th width="80">Invoice Value</th>
	                <th width="100">Invoice </th>
	                <th width="100">EXP NO</th>
	                <th width="80">EX FACTORY DATE</th>
	                <th width="80">Gmts. Item</th>
	                <th width="100">LC/SC</th>
	                <th >Buyer</th>
	            </tr>
	        </thead>
        </table>
        <div style="width:1350px; overflow-y:scroll; max-height:290px;font-size:12px; overflow-x:hidden;" id="scroll_body_short" align="left">
            <table width="1330" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
            	<tbody>
	                <?						
						$k=1;$po_chk=array();$po_chk2=array();
						foreach($sql_data as $row_result)
						{
							if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$row_count=$po_count[$row_result['PO_ID']];
							$gmts_item = '';
							$gmts_item_id = explode(",", $row_result['GMTS_ITEM_ID']);
							foreach ($gmts_item_id as $item_id) {
								if ($gmts_item == ""){$gmts_item = $garments_item[$item_id];}
								else{$gmts_item .= "," . $garments_item[$item_id];}
							}
							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
								<td width="30" ><? echo $k; ?></td>
									<?
										if(!in_array($row_result['PO_ID'],$po_chk))
										{
											$po_chk[]=$row_result['PO_ID'];
											?>
												<td width="100" rowspan="<?=$row_count;?>" class="wrd_brk" valign="middle"><? echo $company_arr[$row_result['BENIFICIARY_ID']]; ?></td>
												<td width="100" rowspan="<?=$row_count;?>" class="wrd_brk center" valign="middle"><? echo $row_result['PO_NUMBER'];?></td>
												<td width="100" rowspan="<?=$row_count;?>" class="wrd_brk" valign="middle"><? echo $row_result['STYLE_REF_NO'];?></td>
												<td width="80" rowspan="<?=$row_count;?>" class="wrd_brk right" valign="middle"><? echo $row_result['PO_QUANTITY'];?></td>
											<?
										}
									?>
								<td width="80" class="wrd_brk right"><? echo $row_result['CURRENT_INVOICE_QNTY']; ?></td>
									<?
										if(!in_array($row_result['PO_ID'],$po_chk2))
										{
											$po_chk2[]=$row_result['PO_ID'];
											?>
												<td width="80" rowspan="<?=$row_count;?>" class="wrd_brk right" valign="middle"><? echo $row_result['PO_QUANTITY']-$inv_qnty_pcs_arr[$row_result['PO_ID']]; ?></td>
											<?
										}
									?>
								<td width="80" class="wrd_brk right" ><? echo $row_result['CURRENT_INVOICE_RATE']; ?></td>
								<td width="80" class="wrd_brk right" ><? echo number_format($row_result['CURRENT_INVOICE_VALUE'],2); ?></td>
								<td width="100" class="wrd_brk" ><? echo $row_result['INVOICE_NO']; ?></td>
								<td width="100" class="wrd_brk" ><? echo $row_result['EXP_FORM_NO']; ?></td>
								<td width="80" class="wrd_brk center" ><? echo change_date_format($row_result['INVOICE_DATE'])."&nbsp;";?></td>
								<td width="80" class="wrd_brk center" ><? echo $gmts_item;?></td>
								<td width="100" class="wrd_brk" ><? echo $row_result['LC_SC_NO']; ?></td>
								<td class="wrd_brk"><? echo $buyer_arr[$row_result['BUYER_ID']]; ?></td>
							</tr>
							<?
							$k++;
						}
	                ?>
                </tbody>
            </table>
     		<!-- <table width="1350" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer" align="left">
            	<tfoot>
                    <tr>
	                    <th width="30">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="80">&nbsp;</th>
		                <th width="100">&nbsp;</th>
		                <th >&nbsp;</th>
                    </tr>
                </tfoot>
            </table> -->
 		</div>
	</div>
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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data****$filename";
	//echo "****".$RptType;

	exit();
}

disconnect($con);
?>
