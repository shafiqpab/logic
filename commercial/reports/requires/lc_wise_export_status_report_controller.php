<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == '' ) header('location:login.php');
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action==='load_drop_down_buyer')
{
	echo create_drop_down( 'cbo_buyer_name', 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name",'id,buyer_name', 1, '-- All Buyer --', $selected, '' ,0);
	exit();
}

if($action==='lc_popup')
{
	echo load_html_head_contents("LC Info", '../../../', 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; 
		var selected_name = new Array;
		
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {

			if (str!="") str=str.split("_");
			//alert(str[0]);return;
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );

			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			
			$('#hidden_lc_sc_id').val( id );
			$('#hidden_lc_sc_no').val( name );
		}
	
    </script>
    </head>
    <body>
    <div align="center">
        <form name="lcsc_form" id="lcsc_form">
        <input type="hidden" name="lc_sc_id" name="lc_sc_id">	
        <input type="hidden" name="lc_sc_no" name="lc_sc_no">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
            	<thead>
                    <th>Buyer</th>
                    <th>File No</th>
                    <th>LC/SC No</th>
                    <th colspan="2">LC/SC Date</th>
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('lcsc_form','search_div','','','','');"></th>
                    <input type="hidden" name="hidden_lc_sc_no" id="hidden_lc_sc_no" value="" />
                    <input type="hidden" name="hidden_lc_sc_id" id="hidden_lc_sc_id" value="" />
                </thead>
                <tbody>
                	<tr>
                        <td align="center">
                        <?	
                    		echo create_drop_down( 'cbo_buyer_name', 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_name' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name",'id,buyer_name', 1, '-- All Buyer --', $selected, '' ,0);
                    	?>	
                        </td>
                        <td align="center">
                        	<input type="text" style="width:100px" class="text_boxes" name="txt_file_no" id="txt_file_no" value=""/>
                        </td>
                        <td align="center">
                        	<input type="text" style="width:100px" class="text_boxes" name="txt_lc_sc_no" id="txt_lc_sc_no" value=""/>
                        </td>
                        <td align="center">
                        	<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:75px" placeholder="From Date">
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:75px" placeholder="To Date">
							</td>
                        </td>               
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $cbo_company_name; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_file_no').value+'**'+document.getElementById('txt_lc_sc_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'lc_search_list_view', 'search_div', 'lc_wise_export_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;"/>
                    </td>
                    </tr>
            	</tbody>
           	</table>
           	<div style="margin-top:15px" id="search_div"></div>
		</fieldset>            
	</form>
    </div>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit(); 
}

if($action==='lc_search_list_view')
{		  
	echo load_html_head_contents('Popup Info','../../../', 1, 1, $unicode);
	list($company,$buyer_name,$file_no,$lc_sc_no,$date_from,$date_to)=explode('**',$data);
	//echo $company.'***'.$buyer_name.'***'.$file_no.'***'.$lc_sc_no.'***'.$date_from.'***'.$date_to;die;
	$buyer_cond=$file_cond='';
	$lc_search_con=$sc_search_con=$lc_date_cond=$sc_date_cond='';
	if($buyer_name != 0) $buyer_cond=" and buyer_name=$buyer_name";
	if($file_no != '') $file_cond=" and internal_file_no like('%$file_no')";

	$buyer_arr=return_library_array( "select ID, BUYER_NAME from lib_buyer",'ID','BUYER_NAME');

	if($lc_sc_no != '') 
	{ 
		$lc_search_con = " and export_lc_no like('%$lc_sc_no')";
		$sc_search_con = " and contract_no like('%$lc_sc_no')";
	}

	if($date_from != '' && $date_to != '')
	{
		if($db_type==0)
		{
			$date_from = date("Y-m-d", strtotime($date_from));
			$date_to   = date("Y-m-d", strtotime($date_to));
			$lc_date_cond = " and lc_date between '$date_from' and '$date_to'";
			$sc_date_cond = " and contract_date between '$date_from' and '$date_to'";
		}
		else
		{
			$date_from = date("d-M-Y", strtotime($date_from));
			$date_to   = date("d-M-Y", strtotime($date_to));
			$lc_date_cond = " and lc_date between '$date_from' and '$date_to'";
			$sc_date_cond = " and contract_date between '$date_from' and '$date_to'";
		}
	}

	$sql = "SELECT ID, BENEFICIARY_NAME, BUYER_NAME, INTERNAL_FILE_NO, EXPORT_LC_NO AS LC_SC_NO, LC_DATE AS LC_SC_DATE, 1 AS TYPE from com_export_lc where beneficiary_name=$company and status_active=1 and is_deleted=0 $buyer_cond $file_cond $lc_search_con $lc_date_cond
		UNION ALL
		SELECT ID, BENEFICIARY_NAME, BUYER_NAME, INTERNAL_FILE_NO, CONTRACT_NO AS LC_SC_NO, CONTRACT_DATE AS LC_SC_DATE, 2 AS TYPE from com_sales_contract where beneficiary_name=$company and status_active=1 and is_deleted=0 $buyer_cond $file_cond $sc_search_con $sc_date_cond 
		ORDER BY lc_sc_date DESC";
	$sql_res = sql_select($sql);	
	
	?>
	<div style=" width:697px;">
        <table  width="700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
            <thead>
                <th width="50">SL</th>
                <th width="150">Buyer</th>
                <th width="150">File No</th>
                <th width="150">LC/SC No</th>
                <th width="50">LC/SC</th>
                <th>LC/SC Date</th>
            </thead> 
       </table>          
       <div style="width:700px; overflow-y:scroll; max-height:300px" id="scroll_body">                
       		<table class="rpt_table" width="680" cellpadding="0" cellspacing="0" id="tbl_list_search" border="1" rules="all">
       			<?
       			$i=1;
       			foreach ($sql_res as $row) 
       			{
       				if (fmod($i,2)==0)  $bgcolor="#E9F3FF";
                    else $bgcolor="#FFFFFF";
                    if ($row['TYPE'] == 1) $is_lc_sc = 'LC';
                    else $is_lc_sc = 'SC';
       				?>
	       			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $i.'_'.$row['ID'].'_'.$row['LC_SC_NO']; ?>')">
	       				<td width="50"><? echo $i; ?></td>
	                    <td width="150"><p><? echo $buyer_arr[$row['BUYER_NAME']]; ?></p></td>
	                    <td width="150"><p><? echo $row['INTERNAL_FILE_NO']; ?></p></td>
	                    <td width="150"><p><? echo $row['LC_SC_NO']; ?></p></td>
	                    <td width="50" align="center"><p><? echo $is_lc_sc; ?></p></td>
	                    <td align="center"><p><? echo change_date_format($row['LC_SC_DATE']); ?></p></td>
	                </tr>
	                <?
	                $i++;
	            }
	            ?>            
       		</table>
       	</div>
    </div>
    <br>
    <div style="width:45%; float:left" align="left">
    	<input type="checkbox" name="check_all_lcsc" id="check_all_lcsc" onClick="check_all_data()" value="0" />&nbsp;&nbsp;Check All
    </div>
    <div style="width:30%; float:left" align="left">
        <input type="submit" class="formbutton" id="close" style="width:80px" onClick="parent.emailwindow.hide();" value="Close"/>
    </div>  
    <?
    exit();
}

if($action==='report_generate')
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();

	$cbo_company_name = str_replace("'","",$cbo_company_name);
	$cbo_buyer_name   = str_replace("'","",$cbo_buyer_name);
	$txt_file_no      = str_replace("'","",$txt_file_no);
	$txt_lc_sc_id     = trim(str_replace("'","",$txt_lc_sc_id));
	$txt_date_from    = str_replace("'","",$txt_date_from);
	$txt_date_to      = str_replace("'","",$txt_date_to);
	
	
	$rID4=execute_query("delete from tmp_poid where userid=$user_id");
	if($rID4) oci_commit($con);
	
	$company_arr= return_library_array("select ID, COMPANY_NAME from lib_company", 'ID','COMPANY_NAME');
	$buyer_arr  = return_library_array("select ID, SHORT_NAME from lib_buyer", 'ID','SHORT_NAME');
	$garm_item_arr  = return_library_array("select ID, ITEM_NAME from lib_garment_item", 'ID','ITEM_NAME');

	$sc_no_cond=$lc_no_cond=$file_no_cond='';
	if ($txt_file_no != '') $file_no_cond=" and a.internal_file_no like('%$txt_file_no')";

	if ($txt_lc_sc_id != '')
	{
		$lc_no_cond=" and a.id in($txt_lc_sc_id)";
		$sc_no_cond=" and a.id in($txt_lc_sc_id)";
	} 

	$company_cond=$buyer_cond='';
	if ($cbo_company_name != 0) $company_cond=" and a.beneficiary_name=$cbo_company_name";
	if ($cbo_buyer_name != 0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name";

	$lc_date_cond=$sc_date_cond='';
	if ($txt_date_from != '' && $txt_date_to != '')
	{
		if ($db_type == 0)
		{
			$txt_date_from = date('Y-m-d', strtotime($txt_date_from));
			$txt_date_to   = date('Y-m-d', strtotime($txt_date_to));
			$lc_date_cond=" and a.lc_date between '$txt_date_from' and '$txt_date_to'";
			$sc_date_cond=" and a.contract_date between '$txt_date_from' and '$txt_date_to'";
		} 
		else
		{
			$txt_date_from = date('d-M-Y', strtotime($txt_date_from));
			$txt_date_to = date('d-M-Y', strtotime($txt_date_to));
			$lc_date_cond=" and a.lc_date between '$txt_date_from' and '$txt_date_to'";
			$sc_date_cond=" and a.contract_date between '$txt_date_from' and '$txt_date_to'";
		}
	}

	if ($db_type == 0)
	{
		$listagg_group_invo_no = "group_concat(b.invoice_no) as INVOICE_NO";
		$listagg_group_invo_date = "group_concat(b.invoice_date) as INVOICE_DATE";
		$listagg_group_all_order_no = "group_concat(c.all_order_no) as ALL_ORDER_NO";
	}
	else
	{
		$listagg_group_invo_no = "listagg(b.invoice_no,',') within group (order by b.invoice_no) as INVOICE_NO";
		$listagg_group_invo_date = "listagg(b.invoice_date,',') within group (order by b.invoice_date) as INVOICE_DATE";
		$listagg_group_all_order_no = "listagg(c.all_order_no,',') within group (order by c.all_order_no) as ALL_ORDER_NO";
	}	

	$sql = "SELECT a.id AS LS_SC_ID, a.export_lc_no AS LC_SC_NO, a.lc_date AS LC_SC_DATE, a.lc_value AS LC_SC_VALUE, a.BUYER_NAME, sum(b.invoice_quantity) as INVOICE_QUANTITY, sum(b.invoice_value) as INVOICE_VALUE, sum(b.net_invo_value) as NET_INVO_VALUE, $listagg_group_invo_no, $listagg_group_invo_date, $listagg_group_all_order_no, max(b.ex_factory_date) as EX_FACTORY_DATE, max(b.doc_handover) as DOC_HANDOVER_DATE, max(b.shipping_mode) as SHIPPING_MODE, d.id as SUB_ID, d.BANK_REF_NO, d.SUBMIT_DATE, d.REMARKS, c.IS_LC
	FROM com_export_lc a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d 
	WHERE a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and b.is_lc=1 and c.is_lc=1 and d.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond $buyer_cond $file_no_cond $lc_no_cond $lc_date_cond
	GROUP BY a.id, a.export_lc_no, a.lc_date, a.lc_value, a.buyer_name, d.id, d.bank_ref_no, d.submit_date, d.remarks, c.is_lc
	UNION ALL
	SELECT a.id as LS_SC_ID, a.contract_no as LC_SC_NO, a.contract_date as LC_SC_DATE, a.contract_value as LC_SC_VALUE, a.BUYER_NAME, sum(b.invoice_quantity) as INVOICE_QUANTITY, sum(b.invoice_value) as INVOICE_VALUE, sum(b.net_invo_value) as NET_INVO_VALUE, $listagg_group_invo_no, $listagg_group_invo_date, $listagg_group_all_order_no, max(b.ex_factory_date) as EX_FACTORY_DATE, max(b.doc_handover) as DOC_HANDOVER_DATE, max(b.shipping_mode) as SHIPPING_MODE, d.id as SUB_ID, d.BANK_REF_NO, d.SUBMIT_DATE, d.REMARKS, c.IS_LC
	FROM com_sales_contract a, com_export_invoice_ship_mst b, com_export_doc_submission_invo c, com_export_doc_submission_mst d 
	WHERE a.id=b.lc_sc_id and b.id=c.invoice_id and c.doc_submission_mst_id=d.id and b.is_lc=2 and c.is_lc=2 and d.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $company_cond $buyer_cond $file_no_cond $sc_no_cond $sc_date_cond
	GROUP BY a.id, a.contract_no, a.contract_date, a.contract_value, a.buyer_name, d.id, d.bank_ref_no, d.submit_date, d.remarks, c.is_lc 
	order by submit_date desc";
	//echo $sql;die;

	$sql_res = sql_select($sql);
	$rID=$rID2=$rID3=true;
	foreach ($sql_res as $row)
	{
		$lc_sc_id=$row['LS_SC_ID'];
		if($row['IS_LC'] == 1)
		{
			if($lc_id_check[$lc_sc_id] == '')
			{
				$lc_id_check[$lc_sc_id]=$lc_sc_id;
				$rID2=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$lc_sc_id,158)");
				if($rID2==false)
				{
					oci_rollback($con);
					$rID4=execute_query("delete from tmp_poid where userid=$user_id");
					if($rID4) oci_commit($con);
					disconnect($con);die;
				}
			}			
		}
		else
		{
			if($sc_id_check[$lc_sc_id] == '')
			{
				$sc_id_check[$lc_sc_id]=$lc_sc_id;
				$rID3=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$lc_sc_id,159)");
				if($rID3==false)
				{
					oci_rollback($con);
					$rID4=execute_query("delete from tmp_poid where userid=$user_id");
					if($rID4) oci_commit($con);
					disconnect($con);die;
				}
			}			
		}

		if($row['SUB_ID'])
		{
			$sub_id=$row['SUB_ID'];
			$rID=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$sub_id,160)");
			if($rID==false)
			{
				oci_rollback($con);
				$rID4=execute_query("delete from tmp_poid where userid=$user_id");
				if($rID4) oci_commit($con);
				disconnect($con);die;
			}
		}
	}

	
	if($rID && $rID2 && $rID3)
	{
		oci_commit($con);
	}

	$sql_order = "SELECT c.id as PO_ID, c.PO_NUMBER, d.GMTS_ITEM_ID, b.id as ATTACH_ID, b.ATTACHED_QNTY, b.ATTACHED_VALUE,  b.com_export_lc_id as LC_SC_ID, 1 as TYPE
	FROM tmp_poid a, com_export_lc_order_info b, wo_po_break_down c, wo_po_details_master d 
	WHERE a.poid=b.com_export_lc_id and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and a.type=158 and a.userid=$user_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0
	UNION 
	SELECT c.id as PO_ID, c.PO_NUMBER, d.GMTS_ITEM_ID, b.id as ATTACH_ID, b.ATTACHED_QNTY, b.ATTACHED_VALUE, b.com_sales_contract_id as LC_SC_ID, 2 as TYPE 
	FROM tmp_poid a, com_sales_contract_order_info b, wo_po_break_down c, wo_po_details_master d 
	WHERE a.poid=b.com_sales_contract_id and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and a.type=159 and a.userid=$user_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	//echo $sql_order;die;
	$sql_order_res = sql_select($sql_order);
	$order_arr=array();
	$order_attach_arr=array();
	foreach ($sql_order_res as $row)
	{
		$order_arr[$row['PO_ID']]['PO_NUMBER']    = $row['PO_NUMBER'];
		$order_arr[$row['PO_ID']]['GMTS_ITEM_ID'] = $row['GMTS_ITEM_ID'];
		if($attach_id_check[$row['ATTACH_ID']] == '')
		{
			$attach_id_check[$row['ATTACH_ID']] = $row['ATTACH_ID'];
			$order_arr[$row['PO_ID']]['ATTACHED_QNTY'] += $row['ATTACHED_QNTY'];
			$attach_key = $row['LC_SC_ID'].'*'.$row['TYPE'];
			$order_attach_arr[$attach_key]['ATTACHED_QNTY']  += $row['ATTACHED_QNTY'];
			$order_attach_arr[$attach_key]['ATTACHED_VALUE'] += $row['ATTACHED_VALUE'];
		}						
	}
	//echo '<pre>';print_r($order_attach_arr);

	$sql_exp_realize = "SELECT a.INVOICE_BILL_ID, a.received_date as REALIZED_DATE, sum(c.document_currency) as REALIZED_QTY, c.TYPE
	FROM com_export_proceed_realization a, tmp_poid b, com_export_proceed_rlzn_dtls c
	WHERE a.invoice_bill_id=b.poid and b.type=159 and b.userid=$user_id and a.id=c.mst_id and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0
	GROUP BY a.invoice_bill_id, a.received_date, c.type";

	$sql_exp_realize_res = sql_select($sql_exp_realize);
	$realization_arr=array();
	foreach ($sql_exp_realize_res as $row)
	{
		$realization_arr[$row['INVOICE_BILL_ID']]['REALIZED_DATE']=$row['REALIZED_DATE'];
		if ($row['TYPE'] == 0)
		{				
			$realization_arr[$row['INVOICE_BILL_ID']][0]=$row['REALIZED_QTY'];
		}
		else if ($row['TYPE'] == 1)
		{
			$realization_arr[$row['INVOICE_BILL_ID']][1]=$row['REALIZED_QTY'];
		}			
	}
	//echo '<pre>';print_r($realization_arr);die;
	$rID4=execute_query("delete from tmp_poid where userid=$user_id");
	if($rID4) oci_commit($con);
	disconnect($con);	

	ob_start();
	?>

	<div width="2050">
		<table class="rpt_table" border="1" rules="all" width="2050" cellpadding="0" cellspacing="0" style="margin-left: 2px;">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">FDBP NO</th>
	                <th width="80">DATE</th>
					<th width="150">Invoice No</th>
	                <th width="150">Invoice Date</th>
	                <th width="150">Order No</th>
	                <th width="150">GMT Item</th>
	                <th width="100">Order Qty</th>
	                <th width="100">Invoice Qty</th>
	                <th width="100">AVG Unit Price</th>
	                <th width="100">INV Value</th>
	                <th width="100">Net Invoice Value</th>
	                <th width="80">Ex Factory Date</th>
	                <th width="80">Doc Handover Date</th>
	                <th width="100">Realized Value</th>
	                <th width="80">Short Realized</th>
	                <th width="80">Realized Date</th>
	                <th width="80">Ship Mode</th>
	                <th width="80">Excess Ship</th>
	                <th>remarks</th>
				</tr>
			</thead>
		</table>

		<div style="width:2070px; overflow-y:scroll; max-height:350px" id="scroll_body">			
		    <table class="rpt_table" border="1" rules="all" width="2050" cellpadding="0" cellspacing="0" id="table_body">
		        <tbody>
		        	<?
					$i=1;$m=1;
					$lc_sc_no_check_arr=array();
					foreach ($sql_res as $key => $row) 
					{
						if (fmod($i,2)==0) $bgcolor='#E9F3FF';
		                else $bgcolor='#FFFFFF';
		                $avg_unit_price = $row['INVOICE_VALUE']/$row['INVOICE_QUANTITY'];
		                $sub_id = $row['SUB_ID'];

		                $all_ord_id_arr=array_unique(explode(',',$row['ALL_ORDER_NO']));
		                $po_no=$po_garments='';
		                $po_attach_qnty=0;
		                foreach ($all_ord_id_arr as $po_id)
		                {                	
							$po_no .= $order_arr[$po_id]['PO_NUMBER'].',';
							$po_garments .= $garm_item_arr[$order_arr[$po_id]['GMTS_ITEM_ID']].',';
							$po_attach_qnty += $order_arr[$po_id]['ATTACHED_QNTY'];
		                }
		                $lc_key = $row['LS_SC_ID'].'*'.$row['IS_LC'];
		                $lc_po_attach_qnty = $order_attach_arr[$lc_key]['ATTACHED_QNTY'];
		                $lc_po_attach_value = $order_attach_arr[$lc_key]['ATTACHED_VALUE'];

		                $po_no = rtrim($po_no,',');
		                $po_garments  = implode(',',array_unique(explode(',',rtrim($po_garments,','))));
		                $inv_date_arr = array_unique(explode(',',$row['INVOICE_DATE']));
		                $all_inv_date = '';
		                foreach ($inv_date_arr as $inv_date)
		                {                	
							$all_inv_date .= change_date_format($inv_date).',';
		                }
		                $all_inv_date = rtrim($all_inv_date,',');

		                $realized_value  = $realization_arr[$sub_id][1];
		                $short_realized  = $realization_arr[$sub_id][0];
		                $excess_ship_qty = $po_attach_qnty-$row['INVOICE_QUANTITY'];
		                $lc_sc_date      = change_date_format($row['LC_SC_DATE']);
		                $lc_sc_value     = $row['LC_SC_VALUE'];
		                $buyer_name      = $buyer_arr[$row['BUYER_NAME']];

						$lc_sc_no_check_val = $row['LC_SC_NO'];
						if (!in_array($lc_sc_no_check_val,$lc_sc_no_check_arr) )
						{
							if($m != 1)
							{
								?>
								<tr class="tbl_bottom">
			                        <td colspan="7" align="right">LC Total:&nbsp;</td>
			                        <td width="100" align="right"><? echo number_format($sub_tot_po_attach_qnty,2); ?></td>
			                        <td width="100" align="right"><? echo number_format($sub_tot_invoice_quantity,2); ?></td>
			                        <td width="100">&nbsp;</td>
			                        <td width="100" align="right"><? echo number_format($sub_total_invoice_value,2); ?></td>
			                        <td width="100" align="right"><? echo number_format($sub_total_net_invo_value,2); ?></td>
			                        <td width="80">&nbsp;</td>
			                        <td width="80">&nbsp;</td>
			                        <td width="100" align="right"><? echo number_format($sub_total_realized_value,2); ?></td>
			                        <td width="80" align="right"><? echo number_format($sub_total_short_realized,2); ?></td>
			                        <td width="80">&nbsp;</td>
			                        <td width="80">&nbsp;</td>
			                        <td width="80">&nbsp;</td>
			                        <td>&nbsp;</td>
			                    </tr>
								<?
								unset($sub_tot_po_attach_qnty);
								unset($sub_tot_invoice_quantity);
								unset($sub_total_invoice_value);
								unset($sub_total_net_invo_value);
								unset($sub_total_realized_value);
								unset($sub_total_short_realized);
							}							
							?>
							<tr bgcolor="#FFFFFF"><td colspan="20" align="left"><strong>Buyer Name:&nbsp; <? echo $buyer_name; ?>,&nbsp;L/C NO:&nbsp;<? echo $lc_sc_no_check_val; ?>,&nbsp;LC DATE:&nbsp;<? echo $lc_sc_date; ?>,&nbsp;LC Value:&nbsp;<? echo number_format($lc_sc_value,2); ?>,&nbsp;PO Attach Qty:&nbsp;<? echo number_format($lc_po_attach_qnty,2); ?>&nbsp;(Pcs),&nbsp;PO Attach Value:&nbsp;<? echo number_format($lc_po_attach_value,2); ?></strong></td>
							</tr>
							<?
							$lc_sc_no_check_arr[] = $lc_sc_no_check_val;
							$m++;
						}
						?>
			        	<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer">
				        	<td width="30"><? echo $i; ?></td>
							<td width="100"><p><? echo $row['BANK_REF_NO']; ?></p></td>
			                <td width="80"><p><? echo change_date_format($row['SUBMIT_DATE']); ?></p></td>
							<td width="150"><p><? echo $row['INVOICE_NO']; ?></p></td>
			                <td width="150"><p><? echo $all_inv_date; ?></p></td>
			                <td width="150"><p><? echo $po_no; ?></p></td>
			                <td width="150"><p><? echo $po_garments; ?></p></td>
			                <td width="100" align="right"><p><? echo number_format($po_attach_qnty,2); ?></p></td>
			                <td width="100" align="right"><p><? echo number_format($row['INVOICE_QUANTITY'],2); ?></p></td>
			                <td width="100" align="right"><p><? echo number_format($avg_unit_price,4); ?></p></td>
			                <td width="100" align="right"><p><? echo number_format($row['INVOICE_VALUE'],2); ?></p></td>
			                <td width="100" align="right"><p><? echo number_format($row['NET_INVO_VALUE'],2); ?></p></td>
			                <td width="80" align="center"><p><? echo change_date_format($row['EX_FACTORY_DATE']); ?></p></td>
			                <td width="80" align="center"><p><? echo change_date_format($row['DOC_HANDOVER_DATE']); ?></p></td>
			                <td width="100" align="right"><p><? echo number_format($realized_value,2); ?></p></td>
			                <td width="80" align="right"><p><? echo number_format($short_realized,2); ?></p></td>
			                <td width="80" align="center"><p><? echo change_date_format($realization_arr[$sub_id]['REALIZED_DATE']); ?></p></td>
			                <td width="80"><p><? echo $shipment_mode[$row['SHIPPING_MODE']]; ?></p></td>
			                <td width="80" align="right"><p><? echo number_format($excess_ship_qty,2); ?></p></td>
			                <td><p><? echo $row['REMARKS']; ?></p></td>	        		
			        	</tr>			        
					    <?
					    $i++;
						$sub_tot_po_attach_qnty   += $po_attach_qnty;
						$sub_tot_invoice_quantity += $row['INVOICE_QUANTITY'];
						$sub_total_invoice_value  += $row['INVOICE_VALUE'];
						$sub_total_net_invo_value += $row['NET_INVO_VALUE'];
						$sub_total_realized_value += $realized_value;
						$sub_total_short_realized += $short_realized;
					}
					?>
					<tr class="tbl_bottom">
		                <td colspan="7" align="right">LC Total:&nbsp;</td>
		                <td width="100" align="right"><? echo number_format($sub_tot_po_attach_qnty,2); ?></td>
		                <td width="100" align="right"><? echo number_format($sub_tot_invoice_quantity,2); ?></td>
		                <td width="100">&nbsp;</td>
		                <td width="100" align="right"><? echo number_format($sub_total_invoice_value,2); ?></td>
		                <td width="100" align="right"><? echo number_format($sub_total_net_invo_value,2); ?></td>
		                <td width="80">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="100" align="right"><? echo number_format($sub_total_realized_value,2); ?></td>
		                <td width="80" align="right"><? echo number_format($sub_total_short_realized,2); ?></td>
		                <td width="80">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td width="80">&nbsp;</td>
		                <td>&nbsp;</td>
		            </tr> 
	        	</tbody>
			</table>   
	    </div>
	</div>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
}