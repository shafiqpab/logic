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

    if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0)
		{
			$sql_cond_date.= "and c.ex_factory_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			$date_cond_invo = "and c.invoice_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		}
		if($db_type==2 || $db_type==1)
		{ 
			$sql_cond_date.="and c.ex_factory_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			$date_cond_invo = "and c.invoice_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
	}
	
	if ($txt_job_no!='') 
	{
		$sql_cond.=" and a.job_no_mst='$txt_job_no'";
	}
 	if ($txt_int_ref_no!='')
	{
		$sql_cond.=" and a.grouping='$txt_int_ref_no'";
	}
 	
	
	
	$sql_order="SELECT a.id as order_id,a.job_no_mst, a.po_number, (a.po_quantity*b.total_set_qnty) as po_qty, a.po_total_price, (a.unit_price/b.total_set_qnty) as unit_price,a.grouping,a.pub_shipment_date,b.buyer_name,sum(c.ex_factory_qnty) as  ex_factory_qnty
	from wo_po_break_down a,wo_po_details_master b,pro_ex_factory_mst c where a.job_id = b.id and a.id=c.po_break_down_id and b.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 and b.status_active =1 and c.is_deleted=0 and c.entry_form != 85 and c.status_active =1 $sql_cond $sql_cond_date
	group by a.id,a.job_no_mst, a.po_number, a.po_quantity, a.po_total_price, a.unit_price,a.grouping,a.pub_shipment_date,b.buyer_name,a.po_quantity,b.total_set_qnty,a.unit_price,b.total_set_qnty,c.ex_factory_qnty";

    $sql_order_return ="SELECT a.id as order_id, sum(c.ex_factory_qnty) as  ex_factory_qnty, (a.unit_price/b.total_set_qnty) as unit_price
	from wo_po_break_down a,wo_po_details_master b,pro_ex_factory_mst c where a.job_id = b.id and a.id=c.po_break_down_id and b.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 and b.status_active =1 and c.is_deleted=0 and c.entry_form = 85 and c.status_active =1 $sql_cond $sql_cond_date
	group by a.id,a.job_no_mst, a.po_number, a.po_quantity, a.po_total_price, a.unit_price,a.grouping,a.pub_shipment_date,b.buyer_name,a.po_quantity,b.total_set_qnty,a.unit_price,b.total_set_qnty,c.ex_factory_qnty";

    $sql_order_ex_return_result=sql_select($sql_order_return);
	$sql_order_ex_result=sql_select($sql_order);

    $ex_invoice_data_arr=array();
    $ex_invoice_return_arr = array();
    foreach($sql_order_ex_result as $row)
	{
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_number"]=$row[csf("po_number")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["job_no_mst"]=$row[csf("job_no_mst")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["grouping"]=$row[csf("grouping")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_qty"]=$row[csf("po_qty")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_total_price"]=$row[csf("po_total_price")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["ex_factory_value"]+=$row[csf("ex_factory_qnty")]*$row[csf("unit_price")];
	}

    foreach ($sql_order_ex_return_result as $return){
        $ex_invoice_return_arr[$return[csf("order_id")]]["ex_factory_qnty"]+= $return[csf("ex_factory_qnty")];
        $ex_invoice_return_arr[$return[csf("order_id")]]["ex_factory_value"]+=($return[csf("ex_factory_qnty")]*$return[csf("unit_price")]);
    }

	$sql_invoice="SELECT a.id as order_id,c.id as ship_mst_id, a.job_no_mst,a.job_no_mst, a.po_number, (a.po_quantity*b.total_set_qnty) as po_qty, a.po_total_price, a.grouping,a.pub_shipment_date,b.buyer_name,sum(d.current_invoice_qnty) as INVOICE_QNTY,sum(d.current_invoice_value) as INVOICE_VALUE
	from wo_po_break_down a,wo_po_details_master b,com_export_invoice_ship_mst c,com_export_invoice_ship_dtls d
	where a.job_id = b.id and a.id=d.po_breakdown_id and c.id=d.mst_id and b.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 and b.status_active =1 and c.is_deleted=0 and c.status_active =1 and d.is_deleted=0 and d.status_active =1 $sql_cond $date_cond_invo 
	group by a.id,c.id,a.job_no_mst,a.job_no_mst, a.po_number, a.po_quantity, a.po_total_price, a.unit_price,a.grouping,a.pub_shipment_date,b.buyer_name,a.po_quantity,b.total_set_qnty,a.unit_price,b.total_set_qnty
	";

	$sql_invoice_result=sql_select($sql_invoice);
	foreach($sql_invoice_result as $row)
	{
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_number"]=$row[csf("po_number")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["job_no_mst"]=$row[csf("job_no_mst")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["grouping"]=$row[csf("grouping")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_qty"]=$row[csf("po_qty")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_total_price"]=$row[csf("po_total_price")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["current_invoice_qnty"]+=$row['INVOICE_QNTY'];
		$ex_invoice_data_arr[$row[csf("order_id")]]["current_invoice_value"]+=$row['INVOICE_VALUE'];
		//$ex_invoice_data_arr[$row[csf("order_id")]][$row[csf("po_number")]]["current_invoice_value"]+=$inv_val_pcs_arr[$row[csf("ship_mst_id")]];
	}
	
	$invoice_data_arr=array();
	foreach($invoice_sql_result as $row)
	{
		$invoice_data_arr[$row[csf("po_breakdown_id")]][$row[csf("lc_sc_id")]][$row[csf("is_lc")]]+=$row[csf("inv_qnty")];
	}
	
	$i=1;
	ob_start();	
	?>
	<div style="width:1230"> 
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
                    <th width="50">SL No</th>
                    <th width="120">IR No</th>
                    <th width="100">Buyer</th>
                    <th width="100">PO No</th>
                    <th width="120">PO Qty</th>
                    <th width="100">PO Ship Date</th>
                    <th width="100">Ex Factory Qty</th>
                    <th width="100">Ex.Amount</th>
                    <th width="100">Invoice Qty</th>
                    <th width="100">Inv.Amount</th>
                    <th width="100">Qty Variance</th>
                    <th>Amt Variance</th>
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
			foreach($ex_invoice_data_arr as $key => $val)
			{
				//var_dump($val);
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				
				?>
				<tr bgcolor="<? echo $bgcolor;?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center" width="50"><? echo $i; ?></td>
					<td width="120" align="center" style="word-break:break-all"><p><? echo $val["grouping"]; ?>&nbsp;</p></td>
					<td width="100" align="center" style="word-break:break-all"><p><? echo $buyer_name_arr[$val["buyer_name"]]; ?>&nbsp;</p></td>
					<td width="100" align="center" style="word-break:break-all"><p><? echo $val["po_number"]; ?>&nbsp;</p></td>
					<td width="120" align="right" style="word-break:break-all"><p><? echo number_format($val["po_qty"],2); ?>&nbsp;</p></td>
					<td width="100" align="center" style="word-break:break-all"><p><? echo change_date_format($val['pub_shipment_date']); ?></p></td>                    
					<td width="100" align="right" style="word-break:break-all">
						<p>
							<? 
							if($val["ex_factory_qnty"]>0)
							{
								?>
								<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo $val["job_no_mst"]; ?>','<? echo $val["po_number"]; ?>','<? echo $val["grouping"]; ?>','ex_factory_qnty_popup');"><? echo number_format($val["ex_factory_qnty"] - (isset($ex_invoice_return_arr[$key]['ex_factory_qnty']) ? $ex_invoice_return_arr[$key]['ex_factory_qnty'] : 0),2,'.',''); ?></a>

								<?
							}
							else
							{
								echo number_format($val["ex_factory_qnty"] - (isset($ex_invoice_return_arr[$key]['ex_factory_qnty']) ? $ex_invoice_return_arr[$key]['ex_factory_qnty'] : 0),2,'.','');
							}
							//echo number_format($val["ex_factory_qnty"],2); 
							?>
						</p>
					</td>
					<td width="100" align="right" style="word-break:break-all"><p><? echo number_format($val["ex_factory_value"] - (isset($ex_invoice_return_arr[$key]['ex_factory_value']) ? $ex_invoice_return_arr[$key]['ex_factory_value'] : 0),2); ?>&nbsp;</p></td>
					<td width="100" align="right" style="word-break:break-all">
						<p>
							<? 
							if($val["current_invoice_qnty"]>0)
							{
								?>
								<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo $val["job_no_mst"]; ?>','<? echo $val["po_number"]; ?>','<? echo $val["grouping"]; ?>','invoice_qnty_popup');"><? echo number_format($val["current_invoice_qnty"],2,'.',''); ?></a>
							
								<?
							}
							else
							{
								echo number_format($val["current_invoice_qnty"],2,'.','');	
							}
							//echo number_format($val["current_invoice_qnty"],2); 
							?>
						</p>
					</td>
					<td width="100" align="right" style="word-break:break-all"><p><? echo number_format($val["current_invoice_value"],2); ?>&nbsp;</p></td>
					<td width="100" align="right" style="word-break:break-all"><p><? echo number_format(($val["ex_factory_qnty"]-(isset($ex_invoice_return_arr[$key]['ex_factory_qnty']) ? $ex_invoice_return_arr[$key]['ex_factory_qnty'] : 0))-$val["current_invoice_qnty"],2); ?>&nbsp;</p></td>
					<td align="right" style="word-break:break-all"><p><? echo number_format(($val["ex_factory_value"] - (isset($ex_invoice_return_arr[$key]['ex_factory_value']) ? $ex_invoice_return_arr[$key]['ex_factory_value'] : 0))-$val["current_invoice_value"],2); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
				$tot_po_qnty+=$val["po_qty"];
				$tot_ex_factory_qnty+=$val["ex_factory_qnty"];
				$tot_ex_factory_value+=$val["ex_factory_value"] - (isset($ex_invoice_return_arr[$key]['ex_factory_value']) ? $ex_invoice_return_arr[$key]['ex_factory_value'] : 0);
				$tot_current_invoice_qnty+=$val["current_invoice_qnty"];
				$tot_current_invoice_value+=$val["current_invoice_value"];
				$tot_qty_var+=$val["ex_factory_qnty"] - (isset($ex_invoice_return_arr[$key]['ex_factory_qnty']) ? $ex_invoice_return_arr[$key]['ex_factory_qnty'] : 0);
				$tot_amt_var+=($val["ex_factory_value"]-(isset($ex_invoice_return_arr[$key]['ex_factory_value']) ? $ex_invoice_return_arr[$key]['ex_factory_value'] : 0))-$val["current_invoice_value"];
			}
			?>    
			
		</table>
	 </div>
	 <table width="1220" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer">
			<tfoot>
				<th width="50">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100"><b>Total : </b></th>
				<th width="120" id="tot_po_qnty"><? echo number_format($tot_po_qnty,2,'.',',');  ?></th>
				<th width="100" >&nbsp;</th>
				<th width="100" id="tot_ex_factory_qnty"><? echo number_format($tot_ex_factory_qnty,2,'.',',');  ?></th>
				<th width="100" id="tot_ex_factory_value"><? echo number_format($tot_ex_factory_value,2,'.',',');  ?></th>
				<th width="100" id="tot_current_invoice_qnty"><? echo number_format($tot_current_invoice_qnty,2,'.',',');  ?></th>
				<th width="100" id="tot_current_invoice_value"><? echo number_format($tot_current_invoice_value,2,'.',',');  ?></th>
				<th width="100" id="tot_qty_var"><? echo number_format($tot_qty_var,2,'.',',');  ?></th>
				<th width="" id="tot_amt_var" style="padding-right: 24px;"><? echo number_format($tot_amt_var,2,'.',',');  ?></th>
				
			</tfoot>
		</table>
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

//report generated 2 here--------------------//
if($action=="generate_report2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
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

    if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0)
		{
			$sql_cond_date.= "and c.ex_factory_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
			$date_cond_invo = "and c.invoice_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		}
		if($db_type==2 || $db_type==1)
		{ 
			$sql_cond_date.="and c.ex_factory_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
			$date_cond_invo = "and c.invoice_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
	}
	
	if ($txt_job_no!='') 
	{
		$sql_cond.=" and a.job_no_mst='$txt_job_no'";
	}
 	if ($txt_int_ref_no!='')
	{
		$sql_cond.=" and a.grouping='$txt_int_ref_no'";
	}
 	
	
	
	$sql_order="SELECT a.id as order_id,a.job_no_mst, a.po_number, (a.po_quantity*b.total_set_qnty) as po_qty, a.po_total_price, (a.unit_price/b.total_set_qnty) as unit_price,a.grouping,a.pub_shipment_date,b.buyer_name,sum(c.ex_factory_qnty) as  ex_factory_qnty
	from wo_po_break_down a,wo_po_details_master b,pro_ex_factory_mst c where a.job_id = b.id and a.id=c.po_break_down_id and b.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 and b.status_active =1 and c.is_deleted=0 and c.entry_form != 85 and c.status_active =1 $sql_cond $sql_cond_date
	group by a.id,a.job_no_mst, a.po_number, a.po_quantity, a.po_total_price, a.unit_price,a.grouping,a.pub_shipment_date,b.buyer_name,a.po_quantity,b.total_set_qnty,a.unit_price,b.total_set_qnty,c.ex_factory_qnty";

    $sql_order_return ="SELECT a.id as order_id, sum(c.ex_factory_qnty) as  ex_factory_qnty, (a.unit_price/b.total_set_qnty) as unit_price
	from wo_po_break_down a,wo_po_details_master b,pro_ex_factory_mst c where a.job_id = b.id and a.id=c.po_break_down_id and b.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 and b.status_active =1 and c.is_deleted=0 and c.entry_form = 85 and c.status_active =1 $sql_cond $sql_cond_date
	group by a.id,a.job_no_mst, a.po_number, a.po_quantity, a.po_total_price, a.unit_price,a.grouping,a.pub_shipment_date,b.buyer_name,a.po_quantity,b.total_set_qnty,a.unit_price,b.total_set_qnty,c.ex_factory_qnty";

    $sql_order_ex_return_result=sql_select($sql_order_return);
	$sql_order_ex_result=sql_select($sql_order);

    $ex_invoice_data_arr=array();
    $ex_invoice_return_arr = array();
    foreach($sql_order_ex_result as $row)
	{
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_number"]=$row[csf("po_number")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["job_no_mst"]=$row[csf("job_no_mst")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["grouping"]=$row[csf("grouping")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_qty"]=$row[csf("po_qty")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_total_price"]=$row[csf("po_total_price")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["ex_factory_value"]+=$row[csf("ex_factory_qnty")]*$row[csf("unit_price")];
	}

    foreach ($sql_order_ex_return_result as $return){
        $ex_invoice_return_arr[$return[csf("order_id")]]["ex_factory_qnty"]+= $return[csf("ex_factory_qnty")];
        $ex_invoice_return_arr[$return[csf("order_id")]]["ex_factory_value"]+=($return[csf("ex_factory_qnty")]*$return[csf("unit_price")]);
    }

	$sql_invoice="SELECT a.id as order_id,c.id as ship_mst_id, a.job_no_mst,a.job_no_mst, a.po_number, (a.po_quantity*b.total_set_qnty) as po_qty, a.po_total_price, a.grouping,a.pub_shipment_date,b.buyer_name,sum(d.current_invoice_qnty) as INVOICE_QNTY,sum(d.current_invoice_value) as INVOICE_VALUE,c.invoice_no
	from wo_po_break_down a,wo_po_details_master b,com_export_invoice_ship_mst c,com_export_invoice_ship_dtls d
	where a.job_id = b.id and a.id=d.po_breakdown_id and c.id=d.mst_id and b.company_name=$cbo_company_name and a.is_deleted=0 and a.status_active =1 and b.is_deleted=0 and b.status_active =1 and c.is_deleted=0 and c.status_active =1 and d.is_deleted=0 and d.status_active =1 $sql_cond $date_cond_invo 
	group by a.id,c.id,a.job_no_mst,a.job_no_mst, a.po_number, a.po_quantity, a.po_total_price, a.unit_price,a.grouping,a.pub_shipment_date,b.buyer_name,a.po_quantity,b.total_set_qnty,a.unit_price,b.total_set_qnty,c.invoice_no
	";

	$sql_invoice_result=sql_select($sql_invoice);
	foreach($sql_invoice_result as $row)
	{
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_number"]=$row[csf("po_number")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["job_no_mst"]=$row[csf("job_no_mst")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["grouping"]=$row[csf("grouping")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["buyer_name"]=$row[csf("buyer_name")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_qty"]=$row[csf("po_qty")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["po_total_price"]=$row[csf("po_total_price")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
		$ex_invoice_data_arr[$row[csf("order_id")]]["current_invoice_qnty"]+=$row['INVOICE_QNTY'];
		$ex_invoice_data_arr[$row[csf("order_id")]]["current_invoice_value"]+=$row['INVOICE_VALUE'];
		$ex_invoice_data_arr[$row[csf("order_id")]]["invoice_no"].=$row[csf('invoice_no')].", ";
		//$ex_invoice_data_arr[$row[csf("order_id")]][$row[csf("po_number")]]["current_invoice_value"]+=$inv_val_pcs_arr[$row[csf("ship_mst_id")]];
	}
	
	$invoice_data_arr=array();
	foreach($invoice_sql_result as $row)
	{
		$invoice_data_arr[$row[csf("po_breakdown_id")]][$row[csf("lc_sc_id")]][$row[csf("is_lc")]]+=$row[csf("inv_qnty")];
	}
	
	$i=1;
	ob_start();	
	?>
	<div style="width:1330"> 
        <table width="1300" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
            <tr style="border:none;">
                <td colspan="13" align="center" style="border:none; font-size:14px;"> Company Name : <? echo $company_arr[$cbo_company_name]; ?></td>
            </tr>
            <tr class="form_caption" style="border:none;">
                <td colspan="12" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?> </td> 
            </tr>
        </table>
        <br />
        <table width="1300" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" align="left"> 
            <thead>
                <tr>
                    <th width="50">SL No</th>
                    <th width="120">IR No</th>
                    <th width="100">Buyer</th>
                    <th width="100">PO No</th>
                    <th width="100">Invoice No</th>
                    <th width="120">PO Qty</th>
                    <th width="100">PO Ship Date</th>
                    <th width="100">Ex Factory Qty</th>
                    <th width="100">Ex.Amount</th>
                    <th width="100">Invoice Qty</th>
                    <th width="100">Inv.Amount</th>
                    <th width="100">Qty Variance</th>
                    <th>Amt Variance</th>
                </tr>
            </thead>
        </table> 
		<div style="width:1320px; overflow-y: scroll; max-height:250px;" id="scroll_body">
		<table width="1300" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body" align="left">
			<?
			$order_arr=array();$i=1;$p=1;
			$sub_total_inv_qty=0;
			$sub_total_inv_value=0;
			$grand_total_qty=0;
			$grand_total_val=0;
			foreach($ex_invoice_data_arr as $key => $val)
			{
				$invoice_item="";
                      
				$invoice=explode(",",$val['invoice_no']);
				foreach($invoice as $inv){

					$invoice_item.=$inv.",";
				}

				//var_dump($val);
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				
				?>
				<tr bgcolor="<? echo $bgcolor;?>"  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center" width="50"><? echo $i; ?></td>
					<td width="120" align="center" style="word-break:break-all"><p><? echo $val["grouping"]; ?>&nbsp;</p></td>
					<td width="100" align="center" style="word-break:break-all"><p><? echo $buyer_name_arr[$val["buyer_name"]]; ?>&nbsp;</p></td>
					<td width="100" align="center" style="word-break:break-all"><p><? echo $val["po_number"]; ?>&nbsp;</p></td>
					<td width="100" align="center" style="word-break:break-all"><p><? echo rtrim($invoice_item,", "); ?>&nbsp;</p></td>
					<td width="120" align="right" style="word-break:break-all"><p><? echo number_format($val["po_qty"],2); ?>&nbsp;</p></td>
					<td width="100" align="center" style="word-break:break-all"><p><? echo change_date_format($val['pub_shipment_date']); ?></p></td>                    
					<td width="100" align="right" style="word-break:break-all">
						<p>
							<? 
							if($val["ex_factory_qnty"]>0)
							{
								?>
								<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo $val["job_no_mst"]; ?>','<? echo $val["po_number"]; ?>','<? echo $val["grouping"]; ?>','ex_factory_qnty_popup');"><? echo number_format($val["ex_factory_qnty"] - (isset($ex_invoice_return_arr[$key]['ex_factory_qnty']) ? $ex_invoice_return_arr[$key]['ex_factory_qnty'] : 0),2,'.',''); ?></a>

								<?
							}
							else
							{
								echo number_format($val["ex_factory_qnty"] - (isset($ex_invoice_return_arr[$key]['ex_factory_qnty']) ? $ex_invoice_return_arr[$key]['ex_factory_qnty'] : 0),2,'.','');
							}
							//echo number_format($val["ex_factory_qnty"],2); 
							?>
						</p>
					</td>
					<td width="100" align="right" style="word-break:break-all"><p><? echo number_format($val["ex_factory_value"] - (isset($ex_invoice_return_arr[$key]['ex_factory_value']) ? $ex_invoice_return_arr[$key]['ex_factory_value'] : 0),2); ?>&nbsp;</p></td>
					<td width="100" align="right" style="word-break:break-all">
						<p>
							<? 
							if($val["current_invoice_qnty"]>0)
							{
								?>
								<a style="text-decoration: none;" href='#report_details' onClick="openmypage('<? echo $val["job_no_mst"]; ?>','<? echo $val["po_number"]; ?>','<? echo $val["grouping"]; ?>','invoice_qnty_popup');"><? echo number_format($val["current_invoice_qnty"],2,'.',''); ?></a>
							
								<?
							}
							else
							{
								echo number_format($val["current_invoice_qnty"],2,'.','');	
							}
							//echo number_format($val["current_invoice_qnty"],2); 
							?>
						</p>
					</td>
					<td width="100" align="right" style="word-break:break-all"><p><? echo number_format($val["current_invoice_value"],2); ?>&nbsp;</p></td>
					<td width="100" align="right" style="word-break:break-all"><p><? echo number_format(($val["ex_factory_qnty"]-(isset($ex_invoice_return_arr[$key]['ex_factory_qnty']) ? $ex_invoice_return_arr[$key]['ex_factory_qnty'] : 0))-$val["current_invoice_qnty"],2); ?>&nbsp;</p></td>
					<td align="right" style="word-break:break-all"><p><? echo number_format(($val["ex_factory_value"] - (isset($ex_invoice_return_arr[$key]['ex_factory_value']) ? $ex_invoice_return_arr[$key]['ex_factory_value'] : 0))-$val["current_invoice_value"],2); ?>&nbsp;</p></td>
				</tr>
				<?
				$i++;
				$tot_po_qnty+=$val["po_qty"];
				$tot_ex_factory_qnty+=$val["ex_factory_qnty"];
				$tot_ex_factory_value+=$val["ex_factory_value"] - (isset($ex_invoice_return_arr[$key]['ex_factory_value']) ? $ex_invoice_return_arr[$key]['ex_factory_value'] : 0);
				$tot_current_invoice_qnty+=$val["current_invoice_qnty"];
				$tot_current_invoice_value+=$val["current_invoice_value"];
				$tot_qty_var+=$val["ex_factory_qnty"] - (isset($ex_invoice_return_arr[$key]['ex_factory_qnty']) ? $ex_invoice_return_arr[$key]['ex_factory_qnty'] : 0);
				$tot_amt_var+=($val["ex_factory_value"]-(isset($ex_invoice_return_arr[$key]['ex_factory_value']) ? $ex_invoice_return_arr[$key]['ex_factory_value'] : 0))-$val["current_invoice_value"];
			}
			?>    
			
		</table>
	 </div>
	 <table width="1320" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer">
			<tfoot>
				<th width="50">&nbsp;</th>
				<th width="120">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100"><b>Total : </b></th>
				<th width="120" id="tot_po_qnty"><? echo number_format($tot_po_qnty,2,'.',',');  ?></th>
				<th width="100" >&nbsp;</th>
				<th width="100" id="tot_ex_factory_qnty"><? echo number_format($tot_ex_factory_qnty,2,'.',',');  ?></th>
				<th width="100" id="tot_ex_factory_value"><? echo number_format($tot_ex_factory_value,2,'.',',');  ?></th>
				<th width="100" id="tot_current_invoice_qnty"><? echo number_format($tot_current_invoice_qnty,2,'.',',');  ?></th>
				<th width="100" id="tot_current_invoice_value"><? echo number_format($tot_current_invoice_value,2,'.',',');  ?></th>
				<th width="100" id="tot_qty_var"><? echo number_format($tot_qty_var,2,'.',',');  ?></th>
				<th width="" id="tot_amt_var" style="padding-right: 24px;"><? echo number_format($tot_amt_var,2,'.',',');  ?></th>
				
			</tfoot>
		</table>
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

if($action=="ex_factory_qnty_popup")
{
	echo load_html_head_contents("Ex Factory Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst",'id','ydw_no');

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0)
		{
			$sql_cond_date.= "and b.ex_factory_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
		}
		if($db_type==2 || $db_type==1)
		{ 
			$sql_cond_date.="and b.ex_factory_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		}
	}
	

	$ex_popup_sql = "SELECT c.id, a.sys_number_prefix_num,c.job_no_mst, b.ex_factory_date, a.sys_number, a.entry_form, a.challan_no,c.po_number,sum(b.ex_factory_qnty) as ex_factory_qnty,(c.unit_price/d.total_set_qnty) as unit_price
	from  pro_ex_factory_delivery_mst a, pro_ex_factory_mst b,wo_po_break_down c,wo_po_details_master d
	where a.id=b.delivery_mst_id and c.id=b.po_break_down_id and c.job_id = d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  d.company_name=$companyID and c.job_no_mst='$job_no' and c.po_number='$po_number' $sql_cond_date
	group by  c.id, a.sys_number_prefix_num,c.job_no_mst, b.ex_factory_date, a.sys_number, a.entry_form, a.challan_no,c.po_number,b.ex_factory_qnty,c.unit_price,d.total_set_qnty order by b.ex_factory_date desc";

	$ex_popup_sqlArr=sql_select($ex_popup_sql);

	$ex_popup_data_arr=array();
    $ex_return_popup_data_arr=array();
	foreach ($ex_popup_sqlArr as $row) {

		$ex_popup_data_arr[$row[csf("id")]][$row[csf("challan_no")]]["ex_factory_date"]=$row[csf("ex_factory_date")];
		$ex_popup_data_arr[$row[csf("id")]][$row[csf("challan_no")]]["challan_no"]=$row[csf("challan_no")];
		$ex_popup_data_arr[$row[csf("id")]][$row[csf("challan_no")]]["po_number"]=$row[csf("po_number")];
        $ex_popup_data_arr[$row[csf("id")]][$row[csf("challan_no")]]["entry_form"]=$row[csf("entry_form")];
        if($row[csf("entry_form")] == 85){
            $ex_popup_data_arr[$row[csf("id")]][$row[csf("challan_no")]]["ex_factory_qnty"]+= (0 - $row[csf("ex_factory_qnty")]);
            $ex_popup_data_arr[$row[csf("id")]][$row[csf("challan_no")]]["ex_factory_val"]+= (0 - $row[csf("ex_factory_qnty")])*$row[csf("unit_price")];

        }else{
            $ex_popup_data_arr[$row[csf("id")]][$row[csf("challan_no")]]["ex_factory_qnty"]+=$row[csf("ex_factory_qnty")];
            $ex_popup_data_arr[$row[csf("id")]][$row[csf("challan_no")]]["ex_factory_val"]+=$row[csf("ex_factory_qnty")]*$row[csf("unit_price")];

        }
	}

    $colSpan=4;
		$width=540;
		$margin_left=3;

	?>
    <fieldset style="width:<? echo $width.'px'?>; margin-left:<? echo $margin_left.'px'?>" align="center">
		<!-- <div id="scroll_body" align="center"> -->
		<div style="overflow-y: scroll; max-height:150px;" id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="<? echo $width?>" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Ex-Factory Details: IR No : <? echo $ir_no; ?></th>
                    </tr>
                	<tr>
					<th width="40">Sl</th>
                        <th width="100">ExFactory Date</th>
                        <th width="60">Challan No</th>
                        <th width="100">PO Number</th>
                        <th width="60">Qty</th>
                        <th width="">Amount</th>
                        <th width="">Status</th>
                    </tr>
				</thead>
                <tbody>
                <? $i=1;
				foreach($ex_popup_data_arr as $ex_popup_id=>$ex_popup_data_arr)
				{
					foreach ($ex_popup_data_arr as $key => $value) {
						
						//var_dump($value);
						//var_dump($row);
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="40"><p><? echo $i; ?></p></td>
							<td width="100" align="center"><p><? echo change_date_format($value['ex_factory_date']); ?></p></td>
						
							<td width="60" align="center"><p><? echo $value['challan_no']; ?></p></td>
							<td width="100" align="center"><p><? echo $value['po_number']; ?></p></td>
							<td width="60" align="right"><p><? echo number_format($value['ex_factory_qnty'],2); ?></p></td>
							<td width="60" align="right"><p><? echo number_format($value['ex_factory_val'],2); ?></p></td>
                            <td width="60" align="center"><p><? echo $value['entry_form'] == 85 ? 'Returned' : '-'?> </p></td>
                        </tr>
						<?
						$i++;
						$tot_ex_factory_qnty+=$value['ex_factory_qnty'];
						$tot_ex_factory_val+=$value['ex_factory_val'];
						
					}
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="<? echo $colSpan;?>" align="right"><b>Total :</b></td>
                        <td align="right">&nbsp;<? echo number_format($tot_ex_factory_qnty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_ex_factory_val,2); ?>&nbsp;</td>
                        <td></td>

                    </tr>
                </tfoot>
            </table>
               
        </div>
      </fieldset>
	<?
    exit();
}

if($action=="invoice_qnty_popup")
{
	
	echo load_html_head_contents("Invoice Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$booking_array=return_library_array( "select id, ydw_no from wo_yarn_dyeing_mst",'id','ydw_no');

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0)
		{
			$date_cond_invo = "and a.invoice_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
		}
		if($db_type==2 || $db_type==1)
		{ 
			$date_cond_invo = "and a.invoice_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
		}
	}

	/*$sql_order_set=sql_select("SELECT a.mst_id, (a.current_invoice_qnty*c.total_set_qnty) as invoice_qnty_pcs,b.po_number, (b.unit_price*c.total_set_qnty) as unit_price from com_export_invoice_ship_dtls a, wo_po_break_down b, wo_po_details_master c where a.po_breakdown_id=b.id and b.job_no_mst=c.job_no and c.company_name=$companyID and b.po_number='$po_number' and a.status_active=1 and a.is_deleted=0");

		$inv_qnty_pcs_arr=array();
		foreach($sql_order_set as $row)
		{
			$inv_qnty_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")];
			//$inv_val_pcs_arr[$row[csf("mst_id")]]+=$row[csf("invoice_qnty_pcs")]*$row[csf("unit_price")];
		}*/
		//var_dump($inv_qnty_pcs_arr);

	$sql_invoice = "SELECT a.id, a.invoice_no, a.invoice_date,c.po_number, sum(b.current_invoice_qnty) as invoice_qnty,sum(b.current_invoice_value) as invoice_value from com_export_invoice_ship_mst a,com_export_invoice_ship_dtls b,wo_po_break_down c,wo_po_details_master d where a.id=b.mst_id and b.po_breakdown_id=c.id and c.job_id = d.id and d.company_name=$companyID  and c.job_no_mst='$job_no' and c.po_number='$po_number' $date_cond_invo and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 group by a.id, a.invoice_no, a.invoice_date,c.po_number,c.unit_price,d.total_set_qnty order by a.invoice_date desc";
	
	//echo $sql_invoice;
	$sql_invoice_arr=sql_select($sql_invoice);
	$sql_invoice_data_arr=array();
	foreach ($sql_invoice_arr as $row) {

		$sql_invoice_data_arr[$row[csf("mst_id")]][$row[csf("invoice_no")]]["invoice_no"]=$row[csf("invoice_no")];
		$sql_invoice_data_arr[$row[csf("mst_id")]][$row[csf("invoice_no")]]["invoice_date"]=$row[csf("invoice_date")];
		$sql_invoice_data_arr[$row[csf("mst_id")]][$row[csf("invoice_no")]]["po_number"]=$row[csf("po_number")];
		$sql_invoice_data_arr[$row[csf("mst_id")]][$row[csf("invoice_no")]]["current_invoice_qnty"]+=$row[csf("invoice_qnty")];
		$sql_invoice_data_arr[$row[csf("mst_id")]][$row[csf("invoice_no")]]["current_invoice_val"]+=$row[csf("invoice_value")];
		//$sql_invoice_data_arr[$row[csf("mst_id")]][$row[csf("invoice_no")]]["current_invoice_val"]+=$inv_val_pcs_arr[$row[csf("id")]];
	}
	//var_dump($sql_invoice_data_arr);


	
		$colSpan=4;
		$width=480;
		$margin_left=3;

	?>
    <fieldset style="width:<? echo $width.'px'?>; margin-left:<? echo $margin_left.'px'?>" align="center">
		<div style="overflow-y: scroll; max-height:220px;" id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="<? echo $width?>" cellpadding="0" cellspacing="0" align="center">
				<thead>
                	<tr>
                    	<th colspan="8">Invoice Details: IR No: <? echo $ir_no; ?></th>
                    </tr>
                	<tr>
					<th width="40">Sl</th>
                        <th width="100">Invoice Date</th>
                        <th width="100">CI No</th>
                        <th width="60">PO Number</th>
                        <th width="60">Invoice Qty</th>
                        <th width="">Amount</th>
                    </tr>
				</thead>
                <tbody>
                <? $i=1;
				foreach($sql_invoice_data_arr as $inv_popup_id=>$inv_popup_data_arr)
				{
					foreach ($inv_popup_data_arr as $key => $value) {
						
						//var_dump($value);
						//var_dump($row);
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td width="40"><p><? echo $i; ?></p></td>
							<td width="100" align="center"><p><? echo change_date_format($value['invoice_date']); ?></p></td>
						
							<td width="100" align="center"><p><? echo $value['invoice_no']; ?></p></td>
							<td width="60" align="center"><p><? echo $value['po_number']; ?></p></td>
							<td width="60" align="right"><p><? echo number_format($value['current_invoice_qnty'],2); ?></p></td>
							<td width="60" align="right"><p><? echo number_format($value['current_invoice_val'],2); ?></p></td>
						</tr>
						<?
						$i++;
						$tot_invoice_qnty+=$value['current_invoice_qnty'];
						$tot_invoice_val+=$value['current_invoice_val'];
						
					}
				}
				?>
                </tbody>
                <tfoot>
                	<tr class="tbl_bottom">
                    	<td colspan="<? echo $colSpan;?>" align="right"><b>Total :</b> </td>
                        <td align="right">&nbsp;<? echo number_format($tot_invoice_qnty,2); ?>&nbsp;</td>
                        <td align="right">&nbsp;<? echo number_format($tot_invoice_val,2); ?>&nbsp;</td>

                    </tr>
                </tfoot>
            </table>
               
        </div>
      </fieldset>
	<?
    exit();
}

?>

