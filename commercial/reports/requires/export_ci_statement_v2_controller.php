<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');
include('../../../includes/class4/class.yarns.php');
include('../../../includes/class4/class.conversions.php');
include('../../../includes/class4/class.trims.php');
include('../../../includes/class4/class.emblishments.php');
include('../../../includes/class4/class.washes.php');
include('../../../includes/class4/class.commercials.php');
include('../../../includes/class4/class.commisions.php');
include('../../../includes/class4/class.others.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");

	$cbo_based_on=str_replace("'","",$cbo_based_on);
	$cbo_ascending_by=str_replace("'","",$cbo_ascending_by);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
	$cbo_location=str_replace("'","",$cbo_location);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_lc_sc_no=str_replace("'","",$txt_lc_sc_no);
	$txt_invoice_no=str_replace("'","",$txt_invoice_no);
	$shipping_mode=str_replace("'","",$shipping_mode);
	$txt_int_ref_no=str_replace("'","",$txt_int_ref_no);
	$cbo_search_by=str_replace("'","",$cbo_search_by);

	$sql_cond = ""; $com_cond='';

	$sql_cond .= ($cbo_lien_bank!=0) ? " and b.lien_bank=$cbo_lien_bank" : "";
	$sql_cond .= ($cbo_location!=0) ? " and a.location_id=$cbo_location" : "";
	$sql_cond .= ($cbo_buyer_name!=0) ? " and a.buyer_id=$cbo_buyer_name" : "";
	$sql_cond .= ($shipping_mode!=0) ? " and a.shipping_mode=$shipping_mode" : "";


	if($txt_invoice_no!="") $sql_cond.=" and a.invoice_no like '%$txt_invoice_no%'"; else $invoice_cond="";
	if($txt_int_ref_no!="") $sql_cond.=" and d.grouping like '%$txt_int_ref_no%'"; else $invoice_cond="";

	if($txt_lc_sc_no!="") $export_lc_no ="  b.export_lc_no LIKE '%$txt_lc_sc_no%'"; else $export_lc_no="";
	if($txt_lc_sc_no!="") $contract_no.=" and b.contract_no LIKE '%$txt_lc_sc_no%'"; else $contract_no="";
	
	

	// $com_cond .= ($cbo_company_name!=0) ? " and a.benificiary_id=$cbo_company_name" : "";

	$ascendig_cond="";
	if($cbo_ascending_by!="" && $cbo_ascending_by==1 ) 
	{
		$ascending_by = "invoice_no";
		$ascendig_cond=" order by $ascending_by asc ";
	}else if($cbo_ascending_by!="" && $cbo_ascending_by==2){
		$ascending_by = "invoice_no";
		$ascendig_cond=" order by $ascending_by asc ";
	}

	if($cbo_based_on==1){
		if($txt_date_from!="" && $txt_date_to!=""){
			$sql_cond.=" and a.INVOICE_DATE between '".$txt_date_from."' and '".$txt_date_to."'";
		}
	}
	else{
		if($txt_date_from!="" && $txt_date_to!=""){
			$sql_cond.=" and g.INSERT_DATE between '".$txt_date_from."' and '".$txt_date_to."'";
		}
	}
	
	
	if($cbo_based_on==1){

		if($cbo_search_by==1){
			$used_unused="and a.invoice_no in(SELECT invoice_no FROM lib_invoice_creation where COMPANY_ID=$cbo_company_name)";

		}
		elseif($cbo_search_by==2){
			$used_unused="and a.invoice_no not in(SELECT invoice_no FROM lib_invoice_creation where COMPANY_ID=$cbo_company_name)";
		}

		$sql="SELECT  a.invoice_no, g.insert_date, a.invoice_date, a.ex_factory_date, a.remarks, a.is_lc, a.buyer_id, a.exp_form_no,  b.export_lc_no as sc_lc_no, a.shipping_mode, d.po_number,  d.grouping, d.po_quantity, c.current_invoice_qnty, c.current_invoice_rate, c.current_invoice_value
		FROM com_export_invoice_ship_mst a left join lib_invoice_creation g on a.invoice_no=g.invoice_no and g.status_active=1, com_export_lc b, com_export_invoice_ship_dtls c, wo_po_break_down d, wo_po_details_master e
		WHERE d.id=c.po_breakdown_id and e.id=d.job_id and a.is_lc=1 and a.lc_sc_id=b.id and a.id=c.mst_id and a.status_active=1  and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and  a.benificiary_id=$cbo_company_name $used_unused $sql_cond $export_lc_no 
		UNION ALL
		SELECT  a.invoice_no, g.insert_date, a.invoice_date, a.ex_factory_date, a.remarks, a.is_lc, a.buyer_id, a.exp_form_no, b.contract_no as sc_lc_no, a.shipping_mode, d.po_number, d.grouping, d.po_quantity, c.current_invoice_qnty, c.current_invoice_rate, c.current_invoice_value
		FROM com_export_invoice_ship_mst a left join lib_invoice_creation g on a.invoice_no=g.invoice_no and g.status_active=1, com_sales_contract b, com_export_invoice_ship_dtls c, wo_po_break_down d, wo_po_details_master e
		WHERE d.id=c.po_breakdown_id  and e.id=d.job_id and a.is_lc=2 and a.lc_sc_id=b.id and a.id=c.mst_id and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.benificiary_id=$cbo_company_name $used_unused $sql_cond $contract_no $ascendig_cond";
	}
	else
	{
		if($cbo_search_by==1){
			$used_unused="and g.invoice_no in(SELECT invoice_no FROM com_export_invoice_ship_mst where benificiary_id=$cbo_company_name)";

		}elseif($cbo_search_by==2){
			$used_unused="and g.invoice_no not in(SELECT invoice_no FROM com_export_invoice_ship_mst where benificiary_id=$cbo_company_name)";
		}

		$sql="SELECT  g.invoice_no, g.insert_date, a.invoice_date, a.ex_factory_date, a.remarks, a.is_lc, a.buyer_id, a.exp_form_no,  b.export_lc_no as sc_lc_no, a.shipping_mode, d.po_number,  d.grouping, d.po_quantity, c.current_invoice_qnty, c.current_invoice_rate, c.current_invoice_value
		FROM lib_invoice_creation g 
		LEFT JOIN com_export_invoice_ship_mst a ON a.invoice_no = g.invoice_no AND a.status_active = 1 AND a.is_deleted = 0   and   a.is_lc = 1 
		LEFT JOIN com_export_lc b ON a.lc_sc_id = b.id  AND b.status_active = 1 AND b.is_deleted = 0 
		LEFT JOIN com_export_invoice_ship_dtls c ON a.id = c.mst_id AND c.status_active = 1  AND c.is_deleted = 0 
		LEFT JOIN wo_po_break_down d ON c.po_breakdown_id = d.id 
		LEFT JOIN wo_po_details_master e ON d.job_id = e.id 
		WHERE g.COMPANY_ID=$cbo_company_name $used_unused $sql_cond  $export_lc_no 
		UNION ALL
		SELECT  g.invoice_no, g.insert_date, a.invoice_date, a.ex_factory_date, a.remarks, a.is_lc, a.buyer_id, a.exp_form_no, b.contract_no as sc_lc_no, a.shipping_mode, d.po_number, d.grouping, d.po_quantity, c.current_invoice_qnty, c.current_invoice_rate, c.current_invoice_value
		FROM lib_invoice_creation g 
		left join  com_export_invoice_ship_mst a on a.invoice_no=g.invoice_no and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 
		left join com_sales_contract b on a.lc_sc_id=b.id and b.status_active=1 and b.is_deleted=0 
		left join com_export_invoice_ship_dtls c on a.id=c.mst_id  and c.status_active=1 and c.is_deleted=0 left join wo_po_break_down d on d.id=c.po_breakdown_id 
		left join wo_po_details_master e on e.id=d.job_id
		WHERE g.COMPANY_ID=$cbo_company_name $used_unused $sql_cond $contract_no $ascendig_cond";
	}
	
	// echo $sql;//die;
	$sql_re=sql_select($sql);

	$invoice_arr=array();$source_data_issue=array();
	foreach($sql_re as $row){
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["invoice_no"]=$row[csf("invoice_no")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["invoice_date"]=$row[csf("invoice_date")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["remarks"]=$row[csf("remarks")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["is_lc"]=$row[csf("is_lc")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["buyer_id"]=$row[csf("buyer_id")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["insert_date"]=$row[csf("insert_date")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["exp_form_no"]=$row[csf("exp_form_no")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["sc_lc_no"]=$row[csf("sc_lc_no")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["shipping_mode"]=$row[csf("shipping_mode")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["po_number"]=$row[csf("po_number")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["grouping"]=$row[csf("grouping")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["po_quantity"]=$row[csf("po_quantity")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["current_invoice_qnty"]=$row[csf("current_invoice_qnty")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["current_invoice_rate"]=$row[csf("current_invoice_rate")];
		$invoice_arr[$row[csf("invoice_no")]][$row[csf("po_number")]]["current_invoice_value"]=$row[csf("current_invoice_value")];

		$source_data_issue[$row[csf('invoice_no')]]["invoice_no"]+=$row[csf('invoice_no')];
	}

	$invoice_rowspan_arr=array();
	foreach($invoice_arr as $invoice=> $invoice_data)
	{
		$invoice_rowspan=0;
		foreach($invoice_data as $subDtlsID=> $row)
		{
			$invoice_rowspan++;
		}
		$invoice_rowspan_arr[$invoice]=$invoice_rowspan;
	}

	ob_start();
	?>
	<div style="width:1880px">
		<table width="1850" cellpadding="0" cellspacing="0" id="caption" align="left">
			<tr>
				<td align="center" width="100%" colspan="20" class="form_caption" >
					<strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" width="100%" colspan="20" class="form_caption" >
					<strong style="font-size:18px"><? echo $report_title; ?></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1850" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_1" align="left">
			<thead>
				<tr>
					<th width="50">Sl</th>
					<th width="100">Invoice No.</th>
					<th width="100">Invoice Create Date </th>
					<th width="100"> Invoice Date</th>
					<th width="100">EXP No</th>
					<th width="100">SC/LC</th>
					<th width="100">SC/LC No.</th>
					<th width="100"> Ship Mode</th>
					<th width="100">Buyer Name</th>
					<th width="100">Order No</th>
					<th width="100">Int. Ref. No</th>                                     
					<th width="100">Order Qnty</th>
					<th width="100">Ship Qnty Pcs (Invoice)</th>
					<th width="100">Ship Balance Qnty in Pcs</th>
					<th width="100">Unite Price</th>
					<th width="100">Invoice Value</th>
					<th width="100">Ship Balance Qnty Value</th>
					<th width="100">Ex-Factory Date</th>
					<th width="100">Remarks</th>
				</tr>
			</thead>
		</table>	
		<table width="1850" align="left" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" >
			<tbody align="left">
				<?$k=1;      					
				$total_ord_qty=$current_invoice_qnty=$total_ship_bal_qty=$total_current_invoice_value=$total_ship_bal=0;
				foreach($invoice_arr as $invoice_key=>$invoice_data)
				{  
					$invoice_rowspan=0;
					$sub_total_ord_qty=$sub_current_invoice_qnty=$sub_total_ship_bal_qty=$sub_total_current_invoice_value=$sub_total_ship_bal=0;
					foreach($invoice_data as $order_key=>$row_result)
					{
					if($row['is_lc']==1){
						$is_lc_sc='LC';
					}
					elseif($row['is_lc']==2) {
						$is_lc_sc='SC';
					}					

					// echo $row[csf('is_lc')]."__";
					if ($k%2==0)
					$bgcolor="#E9F3FF";
					else
					$bgcolor="#FFFFFF";
					// echo $row_result['insert_date'];
					// $date = DateTime::createFromFormat('d-M-y h.i.s.u A', $row_result['insert_date']);
					// $formattedDate = $date->format('d-m-Y');	
					// echo $formattedDate;
				
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
						<?
						if($invoice_rowspan==0){ 
						?> 	
							<td width="50" rowspan="<? echo $invoice_rowspan_arr[$invoice_key]; ?>"  style="word-break: break-all;text-align: center;"><? echo $k;?></td>							
							<td width="100" rowspan="<? echo $invoice_rowspan_arr[$invoice_key]; ?>"  style="word-break: break-all;"><? echo $row_result['invoice_no'];?></td>
							<td width="100" rowspan="<? echo $invoice_rowspan_arr[$invoice_key]; ?>"  align="center" style="word-break: break-all;"> <? echo change_date_format($row_result['insert_date']);?>
							</td>											   
							<td width="100" rowspan="<? echo $invoice_rowspan_arr[$invoice_key]; ?>" align="center" style="word-break: break-all;"><? echo change_date_format($row_result['invoice_date']);?></td>
							<td width="100" rowspan="<? echo $invoice_rowspan_arr[$invoice_key]; ?>"  style="word-break: break-all;"> <? echo  $row_result["exp_form_no"]; ?>
							</td>					
							</p></td>
							<td width="100" rowspan="<? echo $invoice_rowspan_arr[$invoice_key]; ?>" style="word-break: break-all;"><p><? echo $is_lc_sc ;?>&nbsp;</p>
						    </td>							
							<td width="100" rowspan="<? echo $invoice_rowspan_arr[$invoice_key]; ?>"  align="center" style="word-break: break-all;">
								<? echo $row_result["sc_lc_no"]?>
							</td>
							<td width="100" rowspan="<? echo $invoice_rowspan_arr[$invoice_key]; ?>"  style="word-break: break-all;">
								<? echo $shipment_mode[$row_result['shipping_mode']];?>
							</td>
							<td width="100" rowspan="<? echo $invoice_rowspan_arr[$invoice_key]; ?>" align="center" style="word-break: break-all;">
								<? echo $buyer_arr[$row_result['buyer_id']]; ?>
							</td>
						<? $k++;} ?>
							<td width="100"  style="word-break: break-all;"><? echo $row_result['po_number'];?></td>
							<td width="100" style="word-break: break-all;"><? echo $row_result['grouping'];?></td>
							<td width="100" align="right" style="word-break: break-all;"><? echo $row_result['po_quantity'];?></td>
							<td width="100" align="right" style="word-break: break-all;"><? echo $row_result['current_invoice_qnty']?></td>
							<td width="100" align="right" style="word-break: break-all;"><? echo number_format($row_result['po_quantity']-$row_result['current_invoice_qnty'],2)?></td>
							<td width="100" align="right" style="word-break: break-all;"><? echo $row_result['current_invoice_rate']?></td>
							<td width="100" align="right" style="word-break: break-all;"><? echo $row_result['current_invoice_value']?></td>
							<td width="100" align="right" style="word-break: break-all;"><? echo number_format(($row_result['po_quantity']-$row_result['current_invoice_qnty'])*$row_result['current_invoice_rate'],2)?></td>
							<td width="100" align="center"  style="word-break: break-all;"><? echo change_date_format($row_result["ex_factory_date"]);?></td>								
							<td width="100" align="center" style="word-break: break-all;"><? echo $row_result["remarks"] ?></td>								
					</tr>
					<?
					$invoice_rowspan++;									
					$sub_total_ord_qty+=$row_result['po_quantity'];
					$sub_current_invoice_qnty+=$row_result['current_invoice_qnty'];
					$sub_total_ship_bal_qty+=$row_result['po_quantity']-$row_result['current_invoice_qnty'];
					$sub_total_current_invoice_value+=$row_result['current_invoice_value'];
					$sub_total_ship_bal+=($row_result['po_quantity']-$row_result['current_invoice_qnty'])*$row_result['current_invoice_rate'];

					$total_ord_qty+=$row_result['po_quantity'];
					$current_invoice_qnty+=$row_result['current_invoice_qnty'];
					$total_ship_bal_qty+=$row_result['po_quantity']-$row_result['current_invoice_qnty'];
					$total_current_invoice_value+=$row_result['current_invoice_value'];
					$total_ship_bal+=($row_result['po_quantity']-$row_result['current_invoice_qnty'])*$row_result['current_invoice_rate'];
				}
				?>
				<tr style="background-color: #cccccc;">
					<th colspan="11" align="right">Sub Total :</th>						
					<th align="right"><?=$sub_total_ord_qty; ?></th>
					<th align="right"><?=$sub_current_invoice_qnty; ?></th>
					<th align="right" ><?=$sub_total_ship_bal_qty; ?></th>
					<th ></th>
					<th align="right"> <?=$sub_total_current_invoice_value; ?></th>
					<th align="right"><? echo number_format($sub_total_ship_bal,2); ?></th>
					<th></th>
					<th></th>
				</tr>				
				<?
			}?>					
			</tbody>
			<tfoot>
				<tr>						
					<th colspan="11" >Grand Total:</th>
					<th align="right" width="100"><?=$total_ord_qty?></th>
					<th align="right" width="100"><?=$current_invoice_qnty?> </th>
					<th align="right" width="100"><?=$total_ship_bal_qty?> </th>
					<th align="right" width="100"> </th>
					<th align="right" width="100"><?=$total_current_invoice_value?></th>
					<th align="right" width="100"><?=$total_ship_bal?></th>
					<th width="100"></th>
					<th width="100"></th>					
				</tr>
			</tfoot>
		</table>		
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

	echo "$total_data****".$RptType."****".$filename;	
	exit();
}


if($action=="invoice_popup_search")
{
	echo load_html_head_contents("Invoice No PopUp Search", "../../../", 1, 1,'','1','');
	extract($_REQUEST);

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	?>

	<script>

		function js_set_value(data)
		{
			var data_string=data.split('_');
			$('#txt_invoice_no').val(data_string[0]);
			$('#cbo_buyer_name').val(data_string[1]);
			parent.emailwindow.hide();
		}

    </script>

	</head>

	<body>
	<div align="center" style="width:900px;">
		<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
			<fieldset style="width:880px;">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="860" class="rpt_table" border="1" rules="all">
				<input type="hidden" name="txt_invoice_no" id="txt_invoice_no" value="" />
				<input type="hidden" name="cbo_buyer_name" id="cbo_buyer_name" value="" />
					<thead>
						<tr>
                            <th colspan="4"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --",1 ); ?></th>
                            <th colspan="2"><input type="checkbox" name="with_value" id="with_value" /> Load PO with only value</th>
                        </tr>
						<tr>
							<th>Company</th>
							<th>Buyer</th>
							<th>Search By</th>
							<th>Invoice Date Range</th>
							<th>Enter Invoice No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							</th>
						</tr>
					</thead>
					<tr class="general">
						<td>
							<? 
								echo create_drop_down( "cbo_company_name", 170, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.id=$cbo_company_name order by comp.company_name","id,company_name", $cbo_company_name, "--- Select Company ---", 0, "load_drop_down( 'export_information_entry_controller', this.value, 'load_drop_down_buyer', 'buyer_td_id' );",1 );
							?>
						</td>
						<td id="buyer_td_id">
							<?
							echo create_drop_down("cbo_buyer_name", 162, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "--- Select Buyer ---", $selected, "");
							?>
						</td>
						<td>
							<?
								$arr=array(1=>'Invoice NO');
								echo create_drop_down( "cbo_search_by", 100, $arr,"", 0, "", 0, "" );
							?>
						</td>
						<td>
							<input type="text" name="invoice_start_date" id="invoice_start_date" class="datepicker" style="width:70px;" />To
                            <input type="text" name="invoice_end_date" id="invoice_end_date" class="datepicker" style="width:70px;" />
						</td>
						<td>
							<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_name').value+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('invoice_start_date').value+'**'+document.getElementById('invoice_end_date').value+'**'+document.getElementById('cbo_string_search_type').value,'invoice_search_list_view', 'search_div', 'export_ci_statement_v2_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
						</td>
					</tr> 
				</table>
				<div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
			</fieldset>
		</form> 
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action==='invoice_search_list_view')
{
	list($company_id, $buyer_id, $search_by, $invoice_num, $invoice_start_date, $invoice_end_date, $search_string) = explode('**', $data);

	// print_r($data);
	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']['data_level_secured']==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!='') $buyer_id_cond=" and buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond='';
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_id=$buyer_id";
	}

	$search_text=''; $company_cond ='';
	if($company_id !=0) $company_cond = "and benificiary_id=$company_id";

	if ($invoice_num != '')
	{
		if($search_string==1){
			$search_text="and invoice_no like '%".trim($invoice_num)."%'";
     	}
	}

	if ($invoice_start_date != '' && $invoice_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($invoice_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and invoice_date between '" . change_date_format($invoice_start_date, '', '', 1) . "' and '" . change_date_format($invoice_end_date, '', '', 1) . "'";
        }
    } 
    else 
    {
        $date_cond = '';
    }

    $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	  $sql = "select id, benificiary_id, buyer_id, invoice_no, invoice_date, is_lc, lc_sc_id, invoice_value, net_invo_value, import_btb, is_posted_account from com_export_invoice_ship_mst where status_active=1 and is_deleted=0 $company_cond $search_text $buyer_id_cond $date_cond order by invoice_date desc";
	$data_array=sql_select($sql);		

	$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
	$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');

	?>
	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="40">SL</th>
            <th width="100">Company</th>
            <th width="100">Buyer</th>
            <th width="150">Invoice No</th>
            <th width="100">Invoice Date</th>
            <th width="150">LC/SC No</th>
            <th width="100">LC/SC</th>
            <th>Net Invoice Value</th>
        </thead>
     </table>
     <div style="width:900px; overflow-y:scroll; max-height:280px">
     	<table width="880" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
		<?			
            $i = 1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";

				if($row[csf('is_lc')]==1)
				{
					$lc_sc_no=$lc_arr[$row[csf('lc_sc_id')]];
					$is_lc_sc='LC';
				}
				else
				{
					$lc_sc_no=$sc_arr[$row[csf('lc_sc_id')]];
					$is_lc_sc='SC';
				}

				if($row[csf('import_btb')]==1) $buyer=$comp_arr[$row[csf('buyer_id')]]; else $buyer=$buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value( '<? echo $row[csf('invoice_no')]; ?>_<? echo $row[csf('buyer_id')]; ?>');" >                	
					<td width="40"><? echo $i; ?></td>
					<td width="100"><p><? echo $comp_arr[$row[csf('benificiary_id')]]; ?></p></td>
					<td width="100"><p><? echo $buyer; ?></p></td>
                    <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo change_date_format($row[csf('invoice_date')]); ?></td>
                    <td width="150"><p><? echo $lc_sc_no; ?></p></td>
                    <td width="100" align="center"><p><? echo $is_lc_sc; ?></p></td>
					<td align="right"><p><?
					echo number_format($row[csf('net_invo_value')],2);
					//echo number_format($row[csf('invoice_value')],2); ?></p></td>
				</tr>
            <?
			$i++;
            }
			?>
		</table>
    </div>
<?
	exit();
}

disconnect($con);
?>
