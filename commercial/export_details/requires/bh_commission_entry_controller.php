<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------- Start-------------------------------------//
if($action=="proceed_realization_popup_search")
{
	echo load_html_head_contents("Export Proceeds Realization Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();
		function toggle( x, origColor ) 
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		function check_all_data()
		{
			var row_num=$('#list_view tr').length-1;
			for(var i=1;  i<=row_num;  i++)
			{
				$("#tr_"+i).click();
			}
			
		}
		function js_set_value(id)
		{
			// alert(id);
			var data=id.split("_");
			toggle( document.getElementById( 'tr_' + data[0] ), '#FFFFFF' );
			var str=data[1];
			var strdt=data[2];
			var strdtid=data[3];

			//alert(str);
			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
				selected_attach_id.push( strdtid );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
				selected_attach_id.splice( i,1 );
			}
			var id = '';
			var ddd='';
			var dddID='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
				dddID += selected_attach_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			dddID = dddID.substr( 0, dddID.length - 1 );
			// alert(ddd)
			$('#hidden_realization_id').val( id );
			$('#hidden_invoice_bill_no').val( ddd );
			$('#hidden_invoice_bill_id').val( dddID );
		} 

    </script>

    </head>

    <body>
    <div align="center" style="width:760px;">
        <form name="searchexportinformationfrm"  id="searchexportinformationfrm">
            <fieldset style="width:750px;">
            <legend>Enter search words</legend>
                <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
                    <thead>
                        <th>Buyer</th>
                        <th>Search By</th>
                        <th width="160" id="search_by_td_up">Enter Bill No</th>
                        <th>Realization Date Range</th>
                        <th>
                            <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                            <input type="hidden" name="hidden_realization_id" id="hidden_realization_id" value="" />
                            <input type="hidden" name="hidden_invoice_bill_no" id="hidden_invoice_bill_no" value="" />
                            <input type="hidden" name="hidden_invoice_bill_id" id="hidden_invoice_bill_id" value="" />
                        </th>
                    </thead>
                    <tr class="general">
                        <td>
                            <?
                            if($buyerID!=0)
                            {
                                echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company c where buy.id=c.buyer_id and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$cbo_company_name  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyerID, "",0 );
                            }
                            else
                            {
                                echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company c where buy.id=c.buyer_id  and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$cbo_company_name $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
                            }
                            ?>
                        </td>
                        <td>
                            <?
                                $arr=array(1=>'Bill No',2=>'Invoice No');
                                $dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
                                echo create_drop_down( "cbo_search_by", 150, $arr,"",0, "--Select--", "",$dd,0 );
                            ?>
                        </td>
                        <td id="search_by_td">
                            <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td>
                            <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:60px" placeholder="From Date" />
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"placeholder="To Date" />
                        </td>
                        <td>
                            <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'**'+<? echo $cbo_company_name; ?>+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+<? echo $cbo_bank_name; ?>, 'proceed_realization_search_list_view', 'search_div', 'bh_commission_entry_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
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

if($action=="proceed_realization_search_list_view")
{
	$data=explode('**',$data);

	$buyer_id=$data[0];
	$company_id=$data[1];
	$search_by=$data[2];
	$search_text=trim($data[3]);
	$date_form=$data[4];
	$date_to=$data[5];
	$bank_id=$data[6];

	$search_cond="";
	if($search_by == 1)
	{
		$search_cond = " and  b.bank_ref_no like '%".$search_text."%'";
	}else{
		$search_cond = " and e.invoice_no like '%".$search_text."%'";
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $byer_cond="and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $byer_cond="";
		}
		else
		{
			$byer_cond="";
		}
	}
	else
	{
		$byer_cond="and a.buyer_id= $buyer_id";
	}

	$date_cond="";
	if($date_form!="" && $date_to !="")
	{
		if($db_type==0)
		{
			$date_form=change_date_format($date_form,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
		}
		else
		{
			$date_form=change_date_format($date_form,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}
		$date_cond=" and a.received_date between '$date_form' and '$date_to'";
	}
	$duplicat_sql="select realization_id as REALIZATION_ID from bh_commission where status_active=1 and is_deleted=0 ";
	$duplicat_data=sql_select($duplicat_sql);
	$realization_id='';
	foreach($duplicat_data as $value)
	{
		if($realization_id !=''){$realization_id .= ",".$value['REALIZATION_ID'];}else{$realization_id = $value['REALIZATION_ID'];}
	}
	unset($duplicat_data);
	$realization_info="";
	if($realization_id!="") $realization_info=" and a.id not in ($realization_id)";
		$sql = "select a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, b.bank_ref_no, b.bank_ref_date,b.lien_bank, sum(c.net_invo_value) as bill_amnt, c.is_lc,c.lc_sc_id, a.received_date,b.import_btb 
		from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c , com_export_invoice_ship_mst e 
		where a.invoice_bill_id=b.id and a.is_invoice_bill=1 and b.id=c.doc_submission_mst_id and e.id = c.invoice_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.is_partial=0 and a.benificiary_id=$company_id and b.lien_bank=$bank_id $byer_cond $date_cond $search_cond $realization_info
		group by a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.received_date, b.bank_ref_no, b.bank_ref_date,c.is_lc,c.lc_sc_id,b.import_btb,b.lien_bank";

	// echo $sql;
	$is_invoiceBill_arr=array(1=>"Bill",2=>"Invoice");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$result = sql_select($sql);
	foreach ($result as $value) {
		if($value[csf("import_btb")] == 1)
		{
			$buyer_company[$value[csf("id")]] = $comp[$value[csf("buyer_id")]];
		}else{
			$buyer_company[$value[csf("id")]] = $buyer_arr[$value[csf("buyer_id")]];
		}
	}

	$arr=array (2=>$is_invoiceBill_arr,3=>$comp,4=>$buyer_company);

	echo create_list_view("list_view", "System Id,Bill/ Invoice No,Bill/ Invoice,Benificiary,Buyer,Bill/ Invoice Amnt,Received Date", "70,120,70,80,100,110","720","230",0, $sql, "js_set_value", "id,bank_ref_no,invoice_bill_id", "", 1, "0,0,is_invoice_bill,benificiary_id,id,0,0", $arr , "id,bank_ref_no,is_invoice_bill,benificiary_id,id,bill_amnt,received_date", "",'','0,0,0,0,0,2,3','',1);
	exit();
}

if($action=="populate_data_from_invoice_bill")
{
	
	$sql="SELECT a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.export_lc_no as SC_LC_NO,f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE,g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, g.current_invoice_value as CURRENT_INVOICE_VALUE, h.po_number as PO_NUMBER, i.style_ref_no as STYLE_REF_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
	from com_export_proceed_realization a,com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_export_lc e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g,wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
	left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
	where b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_no_mst=i.job_no and i.id=j.job_id and d.is_lc=1 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and a.id in ($data) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0
	group by a.buyer_id ,a.received_date, c.bank_ref_no, d.net_invo_value, d.is_lc, e.export_lc_no,f.invoice_no ,f.invoice_date ,g.po_breakdown_id ,g.current_invoice_qnty, g.current_invoice_value, h.po_number, i.style_ref_no, i.order_uom, i.total_set_qnty, j.costing_per, k.commission_amount
	union all
	select a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.contract_no as SC_LC_NO,f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE, g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, g.current_invoice_value as CURRENT_INVOICE_VALUE, h.po_number as PO_NUMBER, i.style_ref_no as STYLE_REF_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_sales_contract e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g, wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
	left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
	where b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_no_mst=i.job_no and i.id=j.job_id  and d.is_lc=2 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and  a.id in ($data) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0 
	group by a.buyer_id ,a.received_date, c.bank_ref_no, d.net_invo_value, d.is_lc, e.contract_no,f.invoice_no ,f.invoice_date ,g.po_breakdown_id ,g.current_invoice_qnty, g.current_invoice_value, h.po_number, i.style_ref_no, i.order_uom, i.total_set_qnty, j.costing_per, k.commission_amount";
	// echo $sql;
	$sql_result=sql_select($sql);
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
	?>
	<table class="rpt_table" width="1080px" cellspacing="1" rules="all" id="tbl_panel">
		<thead>
			<tr width="1080px">
			<th width="30px">SL</th>
			<th width="100px">Bank Ref/ Bill No</th>
			<th width="100px">LC/SC No</th>
			<th width="100px">Invoice No</th>
			<th width="60px">Invoice Date</th>
			<th width="60px">Inoviec Qty PCs</th>
			<th width="80px">Net Inv. Value</th>
			<th width="80px">Realize Value</th>
			<th width="60px">Realize Date</th>
			<th width="60px">Commission Rate</th>
			<th width="80px">Commission Amount</th>
			<th width="100px">Buyer</th>
			<th width="80px">Style No</th>
			<th >Order No</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i = 0;
			$total_invoice_qnty=0;
			// $total_invoice_value=0;
			// $total_document_value=0;
			$total_commission_amount=0;
			foreach($sql_result as $row)
			{
				$i++;
				$commission_rate=0;
				if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
				$commission_am=$row['COMMISSION_AMOUNT']*1;
				// $set_qnty=$row['TOTAL_SET_QNTY']*1;
				$invoice_qnty=$row['CURRENT_INVOICE_QNTY']*1;
				if($row['COSTING_PER']==1){
					$commission_rate=($commission_am/12);
					$commission_amount=$commission_rate*$invoice_qnty;
				}
				if($row['COSTING_PER']==2){
					$commission_rate=($commission_am/1);
					$commission_amount=$commission_rate*$invoice_qnty;
				}
				if($row['COSTING_PER']==3){
					$commission_rate=($commission_am/24);
					$commission_amount=$commission_rate*$invoice_qnty;
				}
				if($row['COSTING_PER']==4){
					$commission_rate=($commission_am/36);
					$commission_amount=$commission_rate*$invoice_qnty;
				}
				if($row['COSTING_PER']==5){
					$commission_rate=($commission_am/48);
					$commission_amount=$commission_rate*$invoice_qnty;
				}
				?>
				<tr align="center" bgcolor="<? echo $bgcolor; ?>">
					<td align="center"><? echo $i; ?></td>
					<td ><? echo $row['BANK_REF_NO']; ?></td>
					<td ><? echo $row['SC_LC_NO']; ?></td>
					<td ><? echo $row['INVOICE_NO']; ?></td>
					<td align="center"><? echo change_date_format($row['INVOICE_DATE']); ?></td>
					<td align="right"><? echo $row['CURRENT_INVOICE_QNTY']; ?></td>
					<td align="right"><? echo $row['CURRENT_INVOICE_VALUE']; ?></td>
					<td align="right"><? echo $row['DOCUMENT_CURRENCY']; ?></td>
					<td align="center"><? echo change_date_format($row['REALIZATION_DATE']); ?></td>
					<td align="right"><? echo number_format($commission_rate,4); ?></td>
					<td align="right"><? echo number_format($commission_amount,4); ?></td>
					<td ><? echo $buyer_arr[$row['BUYER_ID']]; ?></td>
					<td ><? echo $row['STYLE_REF_NO']; ?></td>
					<td ><? echo $row['PO_NUMBER']; ?></td>
				</tr>
			<?
				$total_invoice_qnty+=$row['CURRENT_INVOICE_QNTY'];
				// $total_invoice_value+=$row['NET_INVO_VALUE'];
				// $total_document_value+=$row['DOCUMENT_CURRENCY'];
				$total_commission_amount+=$commission_amount;
			}
			?>
		</tbody>
		<tfoot>
			<tr style="font-weight: bold;">
				<td colspan="5" align="right">Total &nbsp;</td>
				<td align="right"><? echo $total_invoice_qnty; ?></td>
				<td align="right">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="center">&nbsp;</td>
				<td align="right">&nbsp;</td>
				<td align="right"><input type="text" style="width:70px" class="text_boxes_numeric" name="txt_total_amount" id="txt_total_amount" readonly value="<? echo $total_commission_amount; ?>"></td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr style="font-weight: bold;">
				<td colspan="10" align="right">Upcharge &nbsp;</td>
				<td align="right"><input type="text" style="width:70px" class="text_boxes_numeric" name="txt_upcharge" id="txt_upcharge" onKeyUp="calculate_total_amount()"></td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr style="font-weight: bold;">
				<td colspan="10" align="right">Discount &nbsp;</td>
				<td align="right"><input type="text" style="width:70px" class="text_boxes_numeric" name="txt_discount" id="txt_discount" onKeyUp="calculate_total_amount()"></td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr style="font-weight: bold;">
				<td colspan="10" align="right">Net Total &nbsp;</td>
				<td align="right"><input type="text" style="width:70px" class="text_boxes_numeric" name="txt_total_amount_net" id="txt_total_amount_net" readonly></td>
				<td colspan="3">&nbsp;</td>
			</tr>
		</tfoot>
	</table>
	<?
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 

	if ($operation==0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	
		$mst_id=return_next_id("id", "BH_COMMISSION", 1);
		
		if($db_type==0) $insert_date_con="and YEAR(insert_date)=".date('Y',time())."";
		else if($db_type==2) $insert_date_con="and TO_CHAR(insert_date,'YYYY')=".date('Y',time())."";
		
		$new_sys_no=explode("*",return_mrr_number( str_replace("'","",$cbo_company_name), '', 'BHC', date("Y",time()), 5, "select sys_number_prefix,sys_number_prefix_num from bh_commission where company_id=$cbo_company_name $insert_date_con order by id desc ", "sys_number_prefix", "sys_number_prefix_num" ));
		
		$field_array_mst="id, sys_number, sys_number_prefix, sys_number_prefix_num, company_id, bank_id, commision_date, submission_invoice_id, realization_id, bill_no, remarks, buying_house_info, total_amount, upcharge, discount, net_total_amount, inserted_by, insert_date, status_active, is_deleted";
		$data_array_mst="(".$mst_id.",'".$new_sys_no[0]."','".$new_sys_no[1]."','".$new_sys_no[2]."',".$cbo_company_name.",".$cbo_bank_name.",".$txt_commission_date.",".$submission_invoice_id.",".$realization_id.",".$txt_bill_no.",".$txt_remarks.",".$txt_buying_house_info_dtls.",".$txt_total_amount.",".$txt_upcharge.",".$txt_discount.",".$txt_total_amount_net.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		// echo "10**INSERT INTO BH_COMMISSION (".$field_array_mst.") VALUES ".$data_array_mst."</br>"; 
		// die;
		$rID=sql_insert("BH_COMMISSION",$field_array_mst,$data_array_mst,0);
		// echo '100**'.$rID;oci_rollback($con);die;
		
		if($db_type==0)
		{
			if($rID==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$new_sys_no[0]."**".$mst_id;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1)
			{
				oci_commit($con);  
				echo "0**".$new_sys_no[0]."**".$mst_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array_mst="company_id*bank_id*commision_date*submission_invoice_id*realization_id*bill_no*remarks*buying_house_info*total_amount*upcharge*discount*net_total_amount*updated_by*update_date";
		$data_array_mst="".$cbo_company_name."*".$cbo_bank_name."*".$txt_commission_date."*".$submission_invoice_id."*".$realization_id."*".$txt_bill_no."*".$txt_remarks."*".$txt_buying_house_info_dtls."*".$txt_total_amount."*".$txt_upcharge."*".$txt_discount."*".$txt_total_amount_net."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rID=sql_update("BH_COMMISSION",$field_array_mst,$data_array_mst,"id","".$update_id."",0);
		// echo "10**".$rID."</br>"; die;
		if($db_type==0)
		{
			if($rID==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1)
			{
				oci_commit($con);  
				echo "1**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
	
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here----------------------------------------------------------  
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rID=sql_delete("BH_COMMISSION",$field_array,$data_array,"id","".$update_id."",0);

		// echo "10**".$rID."</br>"; die;
		if($db_type==0)
		{
			if($rID==1)
			{
				mysql_query("COMMIT");  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID==1)
			{
				oci_commit($con);  
				echo "2**".str_replace("'",'',$txt_system_id)."**".str_replace("'",'',$update_id);;
			}
			else
			{
				oci_rollback($con);
				echo "10**";
			}
		}
		
		disconnect($con);
	}// Delete Here End ----------------------------------------------------------
	
}

if ($action=="system_popup")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1,$unicode,1,'');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value( id )
		{
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:100%;" >
    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
        <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
                </tr>
                <tr>
                    <th class="must_entry_caption">Company Name</th>
					<th >System ID</th>
                    <th >Bank Name</th>
					<th >Bill No</th>
                    <th colspan="2">Commision Date Range</th>
					<th><input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
					</th>
                </tr>        
            </thead>
            <tbody>
                <tr class="general">
                    <td class="must_entry_caption"> 
                        <input type="hidden" id="selected_id">
                        <? echo create_drop_down( "cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $cbo_company_name, '',1);?>
                    </td>
                    <td > 
						<input name="txt_sys" id="txt_sys" class="text_boxes" style="width:100px">
                    </td>
                    <td><?
						echo create_drop_down("cbo_bank", 150, "select bank_name,id from lib_bank where is_deleted=0 and status_active=1 order by bank_name", "id,bank_name", 1, "-- Select Bank --", 0, "");
						?>
					</td>
					<td >
						<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_bill" id="txt_search_bill" />
					</td>
                    <td><input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date"></td>
                    <td><input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date"></td> 
                    <td align="center">
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('txt_sys').value+'_'+document.getElementById('cbo_bank').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('cbo_string_search_type').value+'_'+document.getElementById('txt_search_bill').value, 'create_system_search_list_view', 'search_div', 'bh_commission_entry_controller', 'setFilterGrid(\'search_div\',-1)');" style="width:100px;" /></td>
                </tr>
                <tr>
                    <td align="center" colspan="8"><? echo load_month_buttons(1); ?></td>
                </tr>
            </tbody>
        </table>
		<br>
        <div id="search_div"></div>
    </form>
    </div>
    </body>
      <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}

if($action=="create_system_search_list_view")
{
	// echo $data;die;
	$com_cond="";$date_cond ="";$year_cond="";$search_text="";$bank_num=""; $search_bill_num="";
	list($company_id,$search_sys,$bank_id,$submission_start_date, $submission_end_date,$year,$search_string,$search_bill ) = explode('_', $data);
	if ($company_id!=0) {$com_cond=" and a.company_id=$company_id";}
	if ($bank_id!=0) {$bank_num=" and a.bank_id=$bank_id";}

	if($search_bill !=''){ $search_bill_num="and b.bill_no like '%".trim($search_bill)."%'";}

	if ($submission_start_date != '' && $submission_end_date != '') 
	{
        if ($db_type == 0) {
            $date_cond = "and a.commision_date '" . change_date_format($submission_start_date, 'yyyy-mm-dd') . "' and '" . change_date_format($submission_end_date, 'yyyy-mm-dd') . "'";
        } else if ($db_type == 2) {
            $date_cond = "and a.commision_date between '" . change_date_format($submission_start_date, '', '', 1) . "' and '" . change_date_format($submission_end_date, '', '', 1) . "'";
		}
    } 
    else 
    {
        $date_cond = '';
		if($year!=""){
			if($db_type==0)
			{
				$year_cond=" and YEAR(a.commision_date) =$year ";
			}
			else
			{	
				$year_cond=" and to_char(a.commision_date,'YYYY') =$year ";
			}
		}
	}
	if ($search_sys != '')
	{
		if($search_string==1)
			{$search_text="and a.sys_number like '".trim($search_sys)."'";}
		else if ($search_string==2) 
			{$search_text="and a.sys_number like '".trim($search_sys)."%'";}
		else if ($search_string==3)
			{$search_text="and a.sys_number like '%".trim($search_sys)."'";}
		else if ($search_string==4 || $search_string==0)
			{$search_text="and a.sys_number like '%".trim($search_sys)."%'";}
	}
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank where status_active=1 and is_deleted=0",'id','bank_name');

    $arr=array(0=>$company_arr,2=>$bank_arr);

		$sql = "select a.id, a.company_id,a.bank_id,a.sys_number_prefix_num, a.commision_date, a.bill_no
		from bh_commission a
		where a.status_active=1 and a.is_deleted=0 $com_cond $bank_num $search_text $search_bill_num $date_cond $year_cond";

	// echo $sql;die;
	echo  create_list_view("search_div", "Company Name,System ID,Bank Name,Bill No,Commision Date", "150,70,150,120,80","670","300",0, $sql , "js_set_value", "id", "", 1, "company_id,0,bank_id,0,0", $arr , "company_id,sys_number_prefix_num,bank_id,bill_no,commision_date", "",'','0,0,0,0,0,3');
	exit();
}

if ($action=="populate_data_from_search_popup")
{
	$mst_sql="SELECT id as ID, sys_number as SYS_NUMBER, company_id as COMPANY_ID, bank_id as BANK_ID, commision_date as COMMISION_DATE, submission_invoice_id as SUBMISSION_INVOICE_ID, realization_id as REALIZATION_ID, bill_no as BILL_NO, remarks as REMARKS, buying_house_info as BUYING_HOUSE_INFO, TOTAL_AMOUNT as TOTAL_AMOUNT, UPCHARGE as UPCHARGE, DISCOUNT as DISCOUNT, NET_TOTAL_AMOUNT as NET_TOTAL_AMOUNT
	from bh_commission 	
	where id=$data and status_active=1 and is_deleted=0";
	// echo $mst_sql;
	$mst_sql_result=sql_select($mst_sql);
	$realization_id=$mst_sql_result[0]['REALIZATION_ID'];
	$bh_total_amount=$mst_sql_result[0]['TOTAL_AMOUNT'];
	$bh_upcharge=$mst_sql_result[0]['UPCHARGE'];
	$bh_discount=$mst_sql_result[0]['DISCOUNT'];
	$bh_net_total_amount=$mst_sql_result[0]['NET_TOTAL_AMOUNT'];
	$sql="SELECT a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.export_lc_no as SC_LC_NO,f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE,g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, g.current_invoice_value as CURRENT_INVOICE_VALUE, h.po_number as PO_NUMBER, i.style_ref_no as STYLE_REF_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
	from com_export_proceed_realization a,com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_export_lc e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g,wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
	left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
	where a.id in ($realization_id) and b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_no_mst=i.job_no and i.id=j.job_id and d.is_lc=1 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0 
	group by a.buyer_id ,a.received_date , c.bank_ref_no, d.net_invo_value, d.is_lc, e.export_lc_no,f.invoice_no ,f.invoice_date ,g.po_breakdown_id ,g.current_invoice_qnty , g.current_invoice_value, h.po_number , i.style_ref_no, i.order_uom , i.total_set_qnty , j.costing_per, k.commission_amount
	union all
	SELECT a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.contract_no as SC_LC_NO,f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE, g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, g.current_invoice_value as CURRENT_INVOICE_VALUE, h.po_number as PO_NUMBER, i.style_ref_no as STYLE_REF_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_sales_contract e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g, wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
	left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
	where a.id in ($realization_id) and b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_no_mst=i.job_no and i.id=j.job_id and d.is_lc=2 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0 
	group by a.buyer_id ,a.received_date , c.bank_ref_no, d.net_invo_value, d.is_lc, e.contract_no,f.invoice_no ,f.invoice_date ,g.po_breakdown_id ,g.current_invoice_qnty , g.current_invoice_value, h.po_number , i.style_ref_no, i.order_uom , i.total_set_qnty , j.costing_per, k.commission_amount";
	// echo $sql;die;
	$sql_result=sql_select($sql);
	echo "document.getElementById('txt_system_id').value = '".$mst_sql_result[0]["SYS_NUMBER"]."';\n"; 
	echo "document.getElementById('cbo_company_name').value 	= '".$mst_sql_result[0]["COMPANY_ID"]."';\n";
	echo "document.getElementById('txt_commission_date').value 	= '".change_date_format($mst_sql_result[0]["COMMISION_DATE"])."';\n";
	echo "document.getElementById('cbo_bank_name').value 		= '".$mst_sql_result[0]["BANK_ID"]."';\n";
	echo "document.getElementById('txt_bill_no').value 		= '".$mst_sql_result[0]["BILL_NO"]."';\n";
	echo "document.getElementById('submission_invoice_id').value = '".$mst_sql_result[0]["SUBMISSION_INVOICE_ID"]."';\n";
	echo "document.getElementById('realization_id').value 		 = '".$mst_sql_result[0]["REALIZATION_ID"]."';\n";
	echo "document.getElementById('txt_remarks').value 		    = '".$mst_sql_result[0]["REMARKS"]."';\n";
    echo "document.getElementById('update_id').value = '".$mst_sql_result[0]["ID"]."';\n"; 
	$buying_house_info=explode("__",$mst_sql_result[0]["BUYING_HOUSE_INFO"]);
	echo "$('#txt_buying_house_info').val('".$buying_house_info[2]."');\n";
	echo "$('#txt_buying_house_info_dtls').val('".$mst_sql_result[0]["BUYING_HOUSE_INFO"]."');\n";
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
	$html='';
	$html.='<table class="rpt_table" width="1080px" cellspacing="1" rules="all" id="tbl_panel">';
	$html.='<thead>';
	$html.='<tr width="1080px">';
	$html.='<th width="30px">SL</th>';
	$html.='<th width="100px">Bank Ref/ Bill No</th>';
	$html.='<th width="100px">LC/SC No</th>';
	$html.='<th width="100px">Invoice No</th>';
	$html.='<th width="60px">Invoice Date</th>';
	$html.='<th width="60px">Inoviec Qty PCs</th>';
	$html.='<th width="80px">Net Inv. Value</th>';
	$html.='<th width="80px">Realize Value</th>';
	$html.='<th width="60px">Realize Date</th>';
	$html.='<th width="60px">Commission Rate</th>';
	$html.='<th width="80px">Commission Amount</th>';
	$html.='<th width="100px">Buyer</th>';
	$html.='<th width="80px">Style No</th>';
	$html.='<th >Order No</th>';
	$html.='</tr>';
	$html.='</thead>';
	$html.='<tbody>';
	$i = 0;
	$total_invoice_qnty=0;
	$total_invoice_value=0;
	$total_document_value=0;
	$total_commission_amount=0;
	foreach($sql_result as $row)
	{
		$i++;
		$commission_rate=0;
		if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
		$commission_am=$row['COMMISSION_AMOUNT']*1;
		// $set_qnty=$row['TOTAL_SET_QNTY']*1;
		$invoice_qnty=$row['CURRENT_INVOICE_QNTY']*1;
		if($row['COSTING_PER']==1){
			$commission_rate=($commission_am/12);
			$commission_amount=$commission_rate*$invoice_qnty;
		}
		if($row['COSTING_PER']==2){
			$commission_rate=($commission_am/1);
			$commission_amount=$commission_rate*$invoice_qnty;
		}
		if($row['COSTING_PER']==3){
			$commission_rate=($commission_am/24);
			$commission_amount=$commission_rate*$invoice_qnty;
		}
		if($row['COSTING_PER']==4){
			$commission_rate=($commission_am/36);
			$commission_amount=$commission_rate*$invoice_qnty;
		}
		if($row['COSTING_PER']==5){
			$commission_rate=($commission_am/48);
			$commission_amount=$commission_rate*$invoice_qnty;
		}

		$html.='<tr align="center" bgcolor="'.$bgcolor.'">';
		$html.='<td align="center">'.$i.'</td>';
		$html.='<td>'.$row['BANK_REF_NO'].'</td>';
		$html.='<td >'.$row['SC_LC_NO'].'</td>';
		$html.='<td >'.$row['INVOICE_NO'].'</td>';
		$html.='<td align="center">'.change_date_format($row['INVOICE_DATE']).'</td>';
		$html.='<td align="right">'.$row['CURRENT_INVOICE_QNTY'].'</td>';
		$html.='<td align="right">'.$row['CURRENT_INVOICE_VALUE'].'</td>';
		$html.='<td align="right">'.$row['DOCUMENT_CURRENCY'].'</td>';
		$html.='<td align="center">'.change_date_format($row['REALIZATION_DATE']).'</td>';
		$html.='<td align="right">'.number_format($commission_rate,4).'</td>';
		$html.='<td align="right">'.number_format($commission_amount,4).'</td>';
		$html.='<td>'.$buyer_arr[$row['BUYER_ID']].'</td>';
		$html.='<td>'.$row['STYLE_REF_NO'].'</td>';
		$html.='<td>'.$row['PO_NUMBER'].'</td>';
		$html.='</tr>';
		$total_invoice_qnty+=$row['CURRENT_INVOICE_QNTY'];
		// $total_invoice_value+=$row['NET_INVO_VALUE'];
		// $total_document_value+=$row['DOCUMENT_CURRENCY'];
		$total_commission_amount+=$commission_amount;
	}
	$html.='</tbody>';
	$html.='<tfoot><tr style="font-weight: bold;">';
	$html.='<td colspan="5" align="right">Total &nbsp;</td>';
	$html.='<td align="right">'.$total_invoice_qnty.'</td>';
	$html.='<td align="right"></td>';
	$html.='<td align="right"></td>';
	$html.='<td align="center">&nbsp;</td><td align="center">&nbsp;</td>';
	$html.='<td align="right"><input type="text" style="width:70px" class="text_boxes_numeric" name="txt_total_amount" id="txt_total_amount" readonly value="'.$total_commission_amount.'"></td>';
	$html.='<td colspan="3">&nbsp;</td>';
	$html.='</tr>';
	$html.='<tr style="font-weight: bold;">';
	$html.='<td colspan="10" align="right">Upcharge &nbsp;</td>';
	$html.='<td align="right"><input type="text" style="width:70px" class="text_boxes_numeric" name="txt_upcharge" id="txt_upcharge" onKeyUp="calculate_total_amount()" value="'.$bh_upcharge.'"></td>';
	$html.='<td colspan="3">&nbsp;</td>';
	$html.='</tr>';
	$html.='<tr style="font-weight: bold;">';
	$html.='<td colspan="10" align="right">Discount &nbsp;</td>';
	$html.='<td align="right"><input type="text" style="width:70px" class="text_boxes_numeric" name="txt_discount" id="txt_discount" onKeyUp="calculate_total_amount()" value="'.$bh_discount.'"></td>';
	$html.='<td colspan="3">&nbsp;</td>';
	$html.='</tr>';
	$html.='<tr style="font-weight: bold;">';
	$html.='<td colspan="10" align="right">Net Total &nbsp;</td>';
	$html.='<td align="right"><input type="text" style="width:70px" class="text_boxes_numeric" name="txt_total_amount_net" id="txt_total_amount_net" readonly value="'.$bh_net_total_amount.'"></td>';
	$html.='<td colspan="3">&nbsp;</td>';
	$html.='</tr></tfoot>';
	$html.='</table>';
	echo "document.getElementById('commission_tbl').innerHTML = '".$html."';\n"; 
	exit();

}

if($action=="buying_house_popup")
{
  	echo load_html_head_contents("Buying House Popup","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$txt_buying_house_info_dtls=explode("__",str_replace("'","",$txt_buying_house_info_dtls));  
	?>
	     
	<script>
		function js_set_value()
		{
	 		var forwarding_type=$('#txt_forwarding_type').val();
			var account_name=$('#txt_account_name').val();
			var buying_house=$('#txt_buying_house').val();
			var account_no=$('#txt_account_no').val();
			var bank_name_and_address=$('#txt_bank_name_and_address').val();
			$('#hdn_buying_house_info_dtls').val(forwarding_type+"__"+account_name+"__"+buying_house+"__"+account_no+"__"+bank_name_and_address);
			parent.emailwindow.hide();
		}
		var str_forwarding_type = [ <? echo substr(return_library_autocomplete("SELECT REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,1) dtls1 FROM BH_COMMISSION where BUYING_HOUSE_INFO IS NOT NULL group by REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,1)", "dtls1" ), 0, -1); ?> ];
		var str_account_name = [ <? echo substr(return_library_autocomplete("SELECT REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,2) dtls2 FROM BH_COMMISSION where BUYING_HOUSE_INFO IS NOT NULL group by REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,2)", "dtls2" ), 0, -1); ?> ];
		var str_buying_house = [ <? echo substr(return_library_autocomplete("SELECT REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,3) dtls3 FROM BH_COMMISSION where BUYING_HOUSE_INFO IS NOT NULL group by REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,3)", "dtls3" ), 0, -1); ?> ];
		var str_account_no = [ <? echo substr(return_library_autocomplete("SELECT REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,4) dtls4 FROM BH_COMMISSION where BUYING_HOUSE_INFO IS NOT NULL group by REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,4)", "dtls4" ), 0, -1); ?> ];
		var str_bank_name_and_address = [ <? echo substr(return_library_autocomplete("SELECT REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,5) dtls5 FROM BH_COMMISSION where BUYING_HOUSE_INFO IS NOT NULL group by REGEXP_SUBSTR(BUYING_HOUSE_INFO, '[^__]+',1,5)", "dtls5" ), 0, -1); ?> ];

		$( document ).ready(function() {
			$("#txt_forwarding_type").autocomplete({
			source: str_forwarding_type
			});
			$("#txt_account_name").autocomplete({
					source: str_account_name
			});
			$("#txt_buying_house").autocomplete({
					source: str_buying_house
			});
			$("#txt_account_no").autocomplete({
					source: str_account_no
			});
			$("#txt_bank_name_and_address").autocomplete({
					source: str_bank_name_and_address
			});
		});
	</script>

	</head>

	<body>
	<div align="center" style="width:700px;">
	<form name="searchdocfrm_1"  id="searchdocfrm_1" autocomplete="off" >
    <legend>Buying House Info</legend>
	<table width="680" cellspacing="2" cellpadding="0" border="0" id="tbl_master">
            <tbody>
                <tr>
                	<td width="130" align="right">Forwarding Type</td>
                    <td width="170">&nbsp;<input type="text" name="txt_forwarding_type" id="txt_forwarding_type" style="width:150px" class="text_boxes" value="<?= $txt_buying_house_info_dtls[0];?>" /></td> 
                    <td width="130" align="right">Account Name</td>
                    <td>&nbsp;<input type="text" name="txt_account_name" id="txt_account_name" style="width:150px" class="text_boxes"  value="<?= $txt_buying_house_info_dtls[1]; ?>" /></td> 
            	</tr>
                <tr>
                	<td align="right">Buying House</td>
                    <td>&nbsp;<input type="text" name="txt_buying_house" id="txt_buying_house" style="width:150px" class="text_boxes"  value="<?= $txt_buying_house_info_dtls[2];?>" /></td> 
                    <td align="right">Account No</td>
                    <td>&nbsp;<input type="text" name="txt_account_no" id="txt_account_no" style="width:150px" class="text_boxes"  value="<?= $txt_buying_house_info_dtls[3];?>" /></td> 
            	</tr>
                <tr>
                	<td align="right">Bank Name and Address</td>
                    <td>&nbsp;<input type="text" name="txt_bank_name_and_address" id="txt_bank_name_and_address" style="width:150px" class="text_boxes"  value="<?= $txt_buying_house_info_dtls[4];?>" /></td> 
   
            	</tr>
                <tr><td>&nbsp;</td></tr>
                <tr>
                	<td colspan="4" align="center">
                    <input type="button" id="btn_close" style="width:100px" class="formbutton" value="Close" onClick="js_set_value();" />
                    <input type="hidden" id="hdn_buying_house_info_dtls" name="hdn_buying_house_info_dtls" />
                    </td>
                </tr>
            </tbody>         
    </table>    
    </form>
    </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if($action=="bh_commission_print")
{
	extract($_REQUEST);
    $data=explode('*',$data);
    $mst_id=$data[0];
    echo load_html_head_contents($data[1],"../../../", 1, 1, $unicode,'','');
	$mst_sql="SELECT id as ID, sys_number as SYS_NUMBER, company_id as COMPANY_ID, bank_id as BANK_ID, commision_date as COMMISION_DATE, submission_invoice_id as SUBMISSION_INVOICE_ID, realization_id as REALIZATION_ID, bill_no as BILL_NO, remarks as REMARKS, buying_house_info as BUYING_HOUSE_INFO, inserted_by as INSERTED_BY
	from bh_commission m	
	where id=$mst_id and status_active=1 and is_deleted=0";
	$mst_sql_result=sql_select($mst_sql);
	$company_id=$mst_sql_result[0]['COMPANY_ID'];
	$inserted_by=$mst_sql_result[0]['INSERTED_BY'];

	$sql="SELECT a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.export_lc_no as SC_LC_NO, e.lc_date as SC_LC_DATE, f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE,g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, h.po_number as PO_NUMBER, i.style_ref_no as STYLE_REF_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
		from com_export_proceed_realization a,com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_export_lc e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g,wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
		left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
		where a.id in (".$mst_sql_result[0]['REALIZATION_ID'].") and b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_no_mst=i.job_no and i.id=j.job_id and d.is_lc=1 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0 
		group by a.buyer_id ,a.received_date , c.bank_ref_no, d.net_invo_value, d.is_lc, e.export_lc_no, e.lc_date, f.invoice_no ,f.invoice_date ,g.po_breakdown_id ,g.current_invoice_qnty , h.po_number , i.style_ref_no, i.order_uom , i.total_set_qnty , j.costing_per, k.commission_amount
		union all
		SELECT a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.contract_no as SC_LC_NO, e.contract_date as SC_LC_DATE, f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE, g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, h.po_number as PO_NUMBER, i.style_ref_no as STYLE_REF_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
		from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_sales_contract e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g, wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
		left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
		where a.id in (".$mst_sql_result[0]['REALIZATION_ID'].") and b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_no_mst=i.job_no and i.id=j.job_id and d.is_lc=2 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0 
		group by a.buyer_id ,a.received_date , c.bank_ref_no, d.net_invo_value, d.is_lc, e.contract_no, e.contract_date, f.invoice_no ,f.invoice_date ,g.po_breakdown_id ,g.current_invoice_qnty , h.po_number , i.style_ref_no, i.order_uom , i.total_set_qnty , j.costing_per, k.commission_amount";
	// echo $sql;
	$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
	$bank_arr = return_library_array("select id,bank_name from lib_bank where is_deleted=0","id","bank_name");
	$sql_result=sql_select($sql);

	?>
		<table cellspacing="0" width="1000" >
			<tr>
				<td ><strong>Company</strong></td>
				<td ><? echo $lib_company_arr[$mst_sql_result[0]['COMPANY_ID']]; ?></td>
				<td ><strong>Commision Date</strong></td>
				<td ><? echo change_date_format($mst_sql_result[0]['COMMISION_DATE']); ?></td>
				<td ><strong>Bank Name</strong></td>
				<td ><? echo $bank_arr[$mst_sql_result[0]['BANK_ID']]; ?></td>
			</tr>
			<tr>
				<td><strong>Bank Ref/ Bill No</strong></td>
				<td ><? echo $mst_sql_result[0]['BILL_NO']; ?></td>
				<td ><strong>Remarks</strong></td>
				<td colspan="3"><? echo $mst_sql_result[0]['REMARKS']; ?></td>

			</tr>
		</table>
		<br>
		<table width="1080px" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<thead>
				<tr width="1080px">
				<th width="30px">SL</th>
				<th width="100px">Bank Ref/ Bill No</th>
				<th width="100px">LC/SC No</th>
				<th width="100px">Invoice No</th>
				<th width="60px">Invoice Date</th>
				<th width="60px">Inoviec Qty PCs</th>
				<th width="80px">Net Inv. Value</th>
				<th width="80px">Realize Value</th>
				<th width="60px">Realize Date</th>
				<th width="60px">Commission Rate</th>
				<th width="80px">Commission Amount</th>
				<th width="100px">Buyer</th>
				<th width="80px">Style No</th>
				<th width="80px">Order No</th>
				</tr>
			</thead>
			<tbody>
				<?
				$i = 0;
				$total_invoice_qnty=0;
				// $total_invoice_value=0;
				// $total_document_value=0;
				$total_commission_amount=0;
				foreach($sql_result as $row)
				{
					$i++;
					$commission_rate=0;
					// if( $i % 2 == 0 ) $bgcolor="#E9F3FF"; else $bgcolor = "#FFFFFF";
					$commission_am=$row['COMMISSION_AMOUNT']*1;
					$set_qnty=$row['TOTAL_SET_QNTY']*1;
					$invoice_qnty=$row['CURRENT_INVOICE_QNTY']*1;
					if($row['COSTING_PER']==1){
						$commission_rate=($commission_am/12)/$set_qnty;
						$commission_amount=$commission_rate*$invoice_qnty;
					}
					if($row['COSTING_PER']==2){
						$commission_rate=($commission_am/1)/$set_qnty;
						$commission_amount=$commission_rate*$invoice_qnty;
					}
					if($row['COSTING_PER']==3){
						$commission_rate=($commission_am/24)/$set_qnty;
						$commission_amount=$commission_rate*$invoice_qnty;
					}
					if($row['COSTING_PER']==4){
						$commission_rate=($commission_am/36)/$set_qnty;
						$commission_amount=$commission_rate*$invoice_qnty;
					}
					if($row['COSTING_PER']==5){
						$commission_rate=($commission_am/48)/$set_qnty;
						$commission_amount=$commission_rate*$invoice_qnty;
					}
					?>
					<tr align="center" >
						<td align="center"><? echo $i; ?></td>
						<td ><? echo $row['BANK_REF_NO']; ?></td>
						<td><? echo $row['SC_LC_NO']; ?></td>
						<td ><? echo $row['INVOICE_NO']; ?></td>
						<td align="center"><? echo change_date_format($row['INVOICE_DATE']); ?></td>
						<td align="right"><? echo $row['CURRENT_INVOICE_QNTY']; ?></td>
						<td align="right"><? echo $row['NET_INVO_VALUE']; ?></td>
						<td align="right"><? echo $row['DOCUMENT_CURRENCY']; ?></td>
						<td align="center"><? echo change_date_format($row['REALIZATION_DATE']); ?></td>
						<td align="right"><? echo number_format($commission_rate); ?></td>
						<td align="right"><? echo number_format($commission_amount); ?></td>
						<td><? echo $buyer_arr[$row['BUYER_ID']]; ?></td>
						<td ><? echo $row['STYLE_REF_NO']; ?></td>
						<td><? echo $row['PO_NUMBER']; ?></td>
					</tr>
				<?
					$total_invoice_qnty+=$row['CURRENT_INVOICE_QNTY'];
					// $total_invoice_value+=$row['NET_INVO_VALUE'];
					// $total_document_value+=$row['DOCUMENT_CURRENCY'];
					$total_commission_amount+=$commission_amount;
				}
				?>
			</tbody>
			<tfoot>
				<tr style="font-weight: bold;">
					<td colspan="5" align="right">Total &nbsp;</td>
					<td align="right"><? echo $total_invoice_qnty; ?></td>
					<td align="right">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="center">&nbsp;</td>
					<td align="right">&nbsp;</td>
					<td align="right"><? echo $total_commission_amount; ?></td>
					<td colspan="3">&nbsp;</td>
				</tr>
			</tfoot>
		</table>
	<?
	echo signature_table(272, $company_id,"960px",'',70,$inserted_by);
}

if($action=="print_letter")
{
	extract($_REQUEST);
    $data=explode('**',$data);
    $letter_type=$data[0];
    $mst_id=$data[2];
    echo load_html_head_contents($data[1],"../../../", 1, 1, $unicode,'','');
	if($letter_type==1)
	{
		$mst_sql="SELECT id as ID, sys_number as SYS_NUMBER, company_id as COMPANY_ID, bank_id as BANK_ID, commision_date as COMMISION_DATE, submission_invoice_id as SUBMISSION_INVOICE_ID, realization_id as REALIZATION_ID, bill_no as BILL_NO, remarks as REMARKS, buying_house_info as BUYING_HOUSE_INFO, total_amount as TOTAL_AMOUNT, upcharge as UPCHARGE, discount as DISCOUNT, net_total_amount as NET_TOTAL_AMOUNT, inserted_by as INSERTED_BY
 		from bh_commission 	
		where id=$mst_id and status_active=1 and is_deleted=0";
		// echo $mst_sql;
		$mst_sql_result=sql_select($mst_sql);
		$realization_id=$mst_sql_result[0]['REALIZATION_ID'];
		$bh_total_amount=$mst_sql_result[0]['TOTAL_AMOUNT'];
		$bh_upcharge=$mst_sql_result[0]['UPCHARGE'];
		$bh_discount=$mst_sql_result[0]['DISCOUNT'];
		$company_id=$mst_sql_result[0]['COMPANY_ID'];
		$inserted_by=$mst_sql_result[0]['INSERTED_BY'];
		$bh_net_total_amount=$mst_sql_result[0]['NET_TOTAL_AMOUNT'];
		$sql="SELECT a.id as REALIZATION_ID, a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.export_lc_no as SC_LC_NO, e.lc_date as SC_LC_DATE, f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE,g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, g.current_invoice_value as CURRENT_INVOICE_VALUE, h.po_number as PO_NUMBER, i.style_ref_no as STYLE_REF_NO, i.job_no as JOB_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
		from com_export_proceed_realization a,com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_export_lc e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g,wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
		left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
		where a.id in ($realization_id) and b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_id=i.id and i.id=j.job_id and d.is_lc=1 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0 
		group by a.id, a.buyer_id ,a.received_date , c.bank_ref_no, d.net_invo_value, d.is_lc, e.export_lc_no, e.lc_date, f.invoice_no ,f.invoice_date ,g.po_breakdown_id ,g.current_invoice_qnty, g.current_invoice_value , h.po_number , i.style_ref_no, i.job_no, i.order_uom , i.total_set_qnty , j.costing_per, k.commission_amount
		union all
		SELECT a.id as REALIZATION_ID, a.buyer_id as BUYER_ID,a.received_date as REALIZATION_DATE, sum(b.document_currency) as DOCUMENT_CURRENCY, c.bank_ref_no as BANK_REF_NO, d.net_invo_value as NET_INVO_VALUE, d.is_lc as IS_LC, e.contract_no as SC_LC_NO, e.contract_date as SC_LC_DATE, f.invoice_no as INVOICE_NO,f.invoice_date as INVOICE_DATE, g.po_breakdown_id as PO_BREAKDOWN_ID,g.current_invoice_qnty as CURRENT_INVOICE_QNTY, g.current_invoice_value as CURRENT_INVOICE_VALUE, h.po_number as PO_NUMBER, i.style_ref_no as STYLE_REF_NO, i.job_no as JOB_NO, i.order_uom as ORDER_UOM, i.total_set_qnty as TOTAL_SET_QNTY, j.costing_per as COSTING_PER, k.commission_amount as COMMISSION_AMOUNT
		from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b, com_export_doc_submission_mst c, com_export_doc_submission_invo d, com_sales_contract e ,com_export_invoice_ship_mst f,com_export_invoice_ship_dtls g, wo_po_break_down h,wo_po_details_master i,wo_pre_cost_mst j
		left join wo_pre_cost_commiss_cost_dtls k on j.job_no=k.job_no and k.particulars_id=2 and k.status_active=1 and k.is_deleted=0
		where a.id in ($realization_id) and b.mst_id=a.id and a.invoice_bill_id=c.id and d.lc_sc_id=e.id and d.invoice_id=f.id and f.id=g.mst_id and g.po_breakdown_id=h.id and h.job_id=i.id and i.id=j.job_id and d.is_lc=2 and b.type=1 and c.id=d.doc_submission_mst_id and a.is_invoice_bill=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and g.status_active=1 and g.is_deleted=0 and h.status_active=1 and h.is_deleted=0 and i.status_active=1 and i.is_deleted=0 and j.status_active=1 and j.is_deleted=0 
		group by a.id, a.buyer_id ,a.received_date , c.bank_ref_no, d.net_invo_value, d.is_lc, e.contract_no, e.contract_date, f.invoice_no ,f.invoice_date ,g.po_breakdown_id ,g.current_invoice_qnty , g.current_invoice_value, h.po_number , i.style_ref_no, i.job_no, i.order_uom , i.total_set_qnty , j.costing_per, k.commission_amount";
		// echo $sql;
		$lib_company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$buyer_arr = return_library_array("select id,buyer_name from lib_buyer where is_deleted=0","id","buyer_name");
		// $bank_arr = return_library_array("select id,bank_name from lib_bank where is_deleted=0","id","bank_name");
		$sql_bank_info = sql_select("SELECT ID, BANK_NAME, BRANCH_NAME, ADDRESS from lib_bank ");
		$bank_dtls_arr=array();
		foreach($sql_bank_info as $row)
		{
			$bank_dtls_arr[$row['ID']]['BANK_NAME']=$row['BANK_NAME'];
			$bank_dtls_arr[$row['ID']]['BRANCH_NAME']=$row['BRANCH_NAME'];
			$bank_dtls_arr[$row['ID']]['ADDRESS']=$row['ADDRESS'];
		}
		$sql_result=sql_select($sql);
		$service_charge=0;
		foreach($sql_result as $row)
		{
			$commission_rate=0;
			$commission_am=$row['COMMISSION_AMOUNT']*1;
			// $set_qnty=$row['TOTAL_SET_QNTY']*1;
			$invoice_qnty=$row['CURRENT_INVOICE_QNTY']*1;
			if($row['COSTING_PER']==1){
				$commission_rate=($commission_am/12);
				$service_charge+=$commission_rate*$invoice_qnty;
			}
			if($row['COSTING_PER']==2){
				$commission_rate=($commission_am/1);
				$service_charge+=$commission_rate*$invoice_qnty;
			}
			if($row['COSTING_PER']==3){
				$commission_rate=($commission_am/24);
				$service_charge+=$commission_rate*$invoice_qnty;
			}
			if($row['COSTING_PER']==4){
				$commission_rate=($commission_am/36);
				$service_charge+=$commission_rate*$invoice_qnty;
			}
			if($row['COSTING_PER']==5){
				$commission_rate=($commission_am/48);
				$service_charge+=$commission_rate*$invoice_qnty;
			}
		}
		$buying_house_info=explode("__",$mst_sql_result[0]["BUYING_HOUSE_INFO"]);

		?>
		<style type="text/css">
			.a4size {
				width: 21cm;
				height: 26.7cm;
				margin-top:100PX;
				font-family: Cambria, Georgia, serif;
			}
			@media print {
				.a4size{ 
					font-family: Cambria;margin: 150px 50PX 100px 25px;size: A4 portrait;
				}
			}
			.wrd_brk{word-break: break-all;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>
		<div class="a4size">
			<table cellspacing="0" width="1050" >
				<tr>
					<td width='180' style="font-size: 20px;"><strong>BUYING HOUSE  </strong></td>
					<td><strong>: </strong></td>
					<td style="font-size: 20px;"><strong><?echo $buying_house_info[2];?></strong></td>
				</tr>
				<tr>
					<td style="font-size: 16px;"><strong>ACCOUNT NAME </strong></td>
					<td><strong>: </strong></td>
					<td style="font-size: 16px;"><strong><?echo $buying_house_info[1];?></strong></td>
				</tr>
				<tr>
					<td style="font-size: 16px;"><strong>ACCOUNT NUMBER </strong></td>
					<td><strong>: </strong></td>
					<td style="font-size: 16px;"><strong><?echo $buying_house_info[3];?></strong></td>
				</tr>
				<tr>
					<td style="font-size: 16px;"><strong>BANK NAME </strong></td>
					<td><strong>: </strong></td>
					<td valign="top" style="font-size: 16px;"><strong><?echo $buying_house_info[4];?></strong></td>
				</tr>
			</table>
			<br>
			<table cellspacing="0" width="1050" >
				<tr width='100%'>
					<td class='center'style="font-size: 20px;"><strong><?echo $buying_house_info[0];?> </strong></td>
				</tr>
				<tr>
					<td height='25'></td>
				</tr>
				<tr>
					<td ><strong>DATE: <? echo change_date_format($mst_sql_result[0]['COMMISION_DATE']); ?> </strong></td>
				</tr>
				<tr>
					<td ><strong>The Manager <br>
								 <?echo $bank_dtls_arr[$mst_sql_result[0]['BANK_ID']]['BANK_NAME']?><br>
								 <?echo $bank_dtls_arr[$mst_sql_result[0]['BANK_ID']]['ADDRESS']?>
						</strong></td>
				</tr>
				<tr>
					<td height='25'></td>
				</tr>
				<tr>
					<td ><strong>Subject:  Request you to release the service charge for USD <?=number_format($bh_net_total_amount,2);?></strong></td>
				</tr>
				<tr>
					<td height='25'></td>
				</tr>
				<tr>
					<td ><strong>Dear Sir,</strong></td>
				</tr>
				<tr>
					<td height='15'></td>
				</tr>
				<tr>
					<td ><strong>We would like to inform you that the above subject, we are submitting the service charge statement for <?echo $buying_house_info[2];?>, as bellow:</strong></td>
				</tr>
			</table>
			<br>
			<table width="1050" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
				<thead>
					<tr>
						<th width="80">S/C No and Date</th>
						<th width="80">Style no</th>
						<th width="80">Job no</th>
						<th width="80">Order no</th>
						<th width="100">Invoice no.</th>
						<th width="80">FDBC No</th>
						<th width="60">Realize Date</th>
						<th width="80">Bank Name</th>
						<th width="60">Invoice Qnty</th>
						<th width="60">Invoice Value</th>
						<th width="60">Realize Value</th>
						<th width="80" class="wrd_brk" >Commission/ Pc</th>
						<th >Total Comm. In USD(invoice)</th>
					</tr>
				</thead>
				<tbody>
					<?
					$total_document_currency=array();
					$total_commission_amount=$total_invoice_qnty=$total_invo_value=0;
					foreach($sql_result as $row)
					{
						$commission_rate=0;
						$commission_am=$row['COMMISSION_AMOUNT']*1;
						// $set_qnty=$row['TOTAL_SET_QNTY']*1;
						$invoice_qnty=$row['CURRENT_INVOICE_QNTY']*1;
						if($row['COSTING_PER']==1){
							$commission_rate=($commission_am/12);
							$commission_amount=$commission_rate*$invoice_qnty;
						}
						if($row['COSTING_PER']==2){
							$commission_rate=($commission_am/1);
							$commission_amount=$commission_rate*$invoice_qnty;
						}
						if($row['COSTING_PER']==3){
							$commission_rate=($commission_am/24);
							$commission_amount=$commission_rate*$invoice_qnty;
						}
						if($row['COSTING_PER']==4){
							$commission_rate=($commission_am/36);
							$commission_amount=$commission_rate*$invoice_qnty;
						}
						if($row['COSTING_PER']==5){
							$commission_rate=($commission_am/48);
							$commission_amount=$commission_rate*$invoice_qnty;
						}
						if($commission_amount!=0 || $commission_amount!='')
						{
							?>
							<tr>
								<td class="wrd_brk"><? echo $row['SC_LC_NO'].' '.change_date_format($row['SC_LC_DATE']); ?></td>
								<td class="wrd_brk"><? echo $row['STYLE_REF_NO']; ?></td>
								<td class="wrd_brk"><? echo $row['JOB_NO']; ?></td>
								<td class="wrd_brk"><? echo $row['PO_NUMBER']; ?></td>
								<td class="wrd_brk"><? echo $row['INVOICE_NO']; ?></td>
								<td class="wrd_brk"><? echo $row['BANK_REF_NO']; ?></td>
								<td class="center"><? echo change_date_format($row['REALIZATION_DATE']); ?></td>
								<td class="wrd_brk"><? echo $bank_dtls_arr[$mst_sql_result[0]['BANK_ID']]['BANK_NAME']; ?></td>
								<td class="right"><? echo $row['CURRENT_INVOICE_QNTY']; ?></td>
								<!-- <td align="right"><? echo $row['NET_INVO_VALUE']; ?></td> -->
								<td class="right"><? echo $row['CURRENT_INVOICE_VALUE']; ?></td>
								<td class="right"><? echo $row['DOCUMENT_CURRENCY']; ?></td>
								<td class="right"><? echo number_format($commission_rate,2); ?></td>
								<td class="right"><? echo number_format($commission_amount,2); ?></td>
							</tr>
							<?
							$total_commission_amount+=$commission_amount;
							$total_invoice_qnty+=$row['CURRENT_INVOICE_QNTY'];
							$total_invo_value+=$row['CURRENT_INVOICE_VALUE'];
							$total_document_currency[$row['REALIZATION_ID']]=$row['DOCUMENT_CURRENCY'];
						}
					}
					?>
				</tbody>
				<!-- <tfoot> -->
					<tr style="font-weight: bold;">
						<td colspan="8" class="right" >Total &nbsp;</td>
						<td class="right" ><? echo number_format($total_invoice_qnty,2); ?></td>
						<td class="right"><? echo number_format($total_invo_value,2); ?></td>
						<td class="right" ><? echo number_format(array_sum($total_document_currency),2); ?></td>
						<td ></td>
						<td class="right" ><? echo number_format($total_commission_amount,2); ?></td>
					</tr>
					<tr style="font-weight: bold;">
						<td colspan="12" class="right">Upcharge &nbsp;</td>
						<td class="right" ><? echo number_format($bh_upcharge,2); ?></td>
					</tr>
					<tr style="font-weight: bold;">
						<td colspan="12" class="right" >Discount &nbsp;</td>
						<td class="right" ><? echo number_format($bh_discount,2); ?></td>
					</tr>
					<tr style="font-weight: bold;">
						<td colspan="12" class="right" >Net Total &nbsp;</td>
						<td class="right"><? echo number_format($bh_net_total_amount,2); ?></td>
					</tr>
				<!-- </tfoot> -->
			</table>
			<br>
			<table cellspacing="0" width="900" >
				<tr>
					<td height='25'></td>
				</tr>
				<tr>
					<td ><strong>Thanking you, </strong></td>
				</tr>
				<tr>
					<td height='25'></td>
				</tr>
				<tr>
					<td ><strong><?=$lib_company_arr[$mst_sql_result[0]['COMPANY_ID']];?> </strong></td>
				</tr>
			</table>		
		</div>
		<?
		echo signature_table(272, $company_id,"960px",'',70,$inserted_by);
	}
	exit();
}