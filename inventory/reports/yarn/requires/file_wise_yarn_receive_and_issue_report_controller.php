<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == '' ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action==="load_drop_down_file_year")
{
    $sql="select a.lc_year from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$data group by a.lc_year
    union
    select a.sc_year as lc_year from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$data group by a.sc_year
    order by lc_year";
	echo create_drop_down( "cbo_file_year", 100, $sql,"lc_year,lc_year", 1, "-- Select Year --", 0, "" );
	exit();
}
if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=204 and is_deleted=0 and status_active=1");
    $printButton=explode(',',$print_report_format);
    foreach($printButton as $id){
        if($id==108)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:60px; margin-top: 2px; margin-right: 2px;" value="Show" onClick="generate_report(1)" />';
        if($id==195)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:60px; margin-top: 2px; margin-right: 2px;" value="Show 2" onClick="generate_report(2)" />';
        if($id==242)$buttonHtml.='<input type="button" id="show_button" class="formbutton" style="width:60px; margin-top: 2px; margin-right: 2px;" value="Show 3" onClick="generate_report(3)" />';
     
    }
    echo "document.getElementById('load_print_button').innerHTML = '".$buttonHtml."';\n";
    exit();
}

if($action==="file_search")
{
	echo load_html_head_contents("Export LC Form", "../../../../", 1, 1,'','1','');
	extract($_REQUEST);
	$sql="select a.INTERNAL_FILE_NO, a.LC_YEAR, 1 as TYPE from com_export_lc a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$company_id and a.lc_year='$file_year' group by a.internal_file_no, a.lc_year
	union
	select a.INTERNAL_FILE_NO, a.sc_year as LC_YEAR, 2 as TYPE from com_sales_contract a where a.status_active=1 and a.is_deleted=0 and a.beneficiary_name=$company_id and a.sc_year='$file_year' group by a.internal_file_no, a.sc_year
	order by internal_file_no";
	?>

	<script>
		var selected_id = new Array, selected_name = new Array();
		function check_all_data() {
			var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str ) {
			toggle( document.getElementById( 'tr_' + str ), '#FFFFCC' );
			var file=$("#td_"+str).html();
			if( jQuery.inArray(file, selected_id ) == -1 ) {
				selected_id.push(file);

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == file) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#txt_selected_id').val( id );
		}
    </script>

	</head>
	<body>
	    <div align="center" style="width:520px;">
	        <form name="searchexportlcfrm" id="searchexportlcfrm">
	            <fieldset style="width:500px; margin-left:3px">
	            	<input type="hidden" id="txt_selected_id" >
	                <table cellpadding="0" cellspacing="0" width="100%" class="rpt_table" border="1" rules="all">
	                	<thead>
	                        <th width="50">Sl</th>
	                        <th width="80">Year</th>
	                        <th>File No</th>
	                    </thead>
	                </table>
	                <div style="width:500px; max-height:290px; overflow:auto;">
	                	<table cellpadding="0" cellspacing="0" width="480" class="rpt_table" id="table_body" border="1" rules="all">
	                        <tbody>
	                        <?
	                        $i=1;
							$sql_res=sql_select($sql);
							foreach($sql_res as $row)
							{	
								if(!in_array($row['INTERNAL_FILE_NO'],$temp_file_arr))
								{
									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
									?>
		                        	<tr bgcolor="<?= $bgcolor; ?>" onClick="js_set_value('<?= $i; ?>');" style="cursor:pointer;" id="tr_<?= $i; ?>">
		                                <td width="50" align="center"><?= $i; ?></td>
		                                <td width="80"><?= $row['LC_YEAR']; ?></td>
		                                <td id="td_<?= $i; ?>"><?= $row['INTERNAL_FILE_NO']; ?></td>
		                            </tr>
		                            <?
									$i++;
									$temp_file_arr[]=$row['INTERNAL_FILE_NO'];
								}
							}
							?>
	                        </tbody>
	                    </table>
	                    <script>setFilterGrid('table_body',-1);</script>
	                </div>
	            </fieldset>
	            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
	            <input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
	        </form>
	    </div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

//for show button
if($action==="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	//$txt_internal_file_no=str_replace("'","",$txt_internal_file_no);
	$txt_internal_file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
	$txt_internal_file_no="";
	foreach($txt_internal_file_no_arr as $file_no)
	{
		$txt_internal_file_no.="'".$file_no."',";
	}
	$txt_internal_file_no=chop($txt_internal_file_no,",");
	$file_cond='';
	if ($txt_internal_file_no=="") $file_cond=""; else $file_cond=" and a.internal_file_no in ($txt_internal_file_no) ";

	$company_arr=return_library_array("select id,company_name from lib_company where id=$cbo_company_name",'id','company_name');
	$buyer_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array("select id,color_name from lib_color", "id", "color_name");

	$sql_file = "SELECT a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID FROM com_export_lc a, com_btb_export_lc_attachment b WHERE a.id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lc_year='$cbo_file_year' $file_cond
    UNION All
    SELECT a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID FROM com_sales_contract a, com_btb_export_lc_attachment b WHERE a.id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.sc_year='$cbo_file_year' $file_cond";
	//echo $sql_file;//die;
    $sql_file_res=sql_select($sql_file);
    $file_data_arr=array();
	foreach($sql_file_res as $row)
	{		
		$btb_IDs .= $row['BTB_ID'].',';
		$file_data_arr[$row['BTB_ID']]['LC_SC_NO']=$row['LC_SC_NO'];
		$file_data_arr[$row['BTB_ID']]['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
		$file_data_arr[$row['BTB_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
	}
	unset($sql_file_res);

	$btb_IDs=rtrim($btb_IDs,',');
	$ex_btb_ids_arr=explode(',', $btb_IDs);
	
	//Temporary Table Data Insert
	$con=connect();
	$rID=true;
	foreach ($ex_btb_ids_arr as $btb_id)
	{
		//echo "insert into tmp_poid (userid, poid, type) values ($user_id,$btb_id,157)";
		$rID=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$btb_id,157)");
	}
	if ($rID) oci_commit($con);
	//End Temporary Table Data Insert

	$sql_main = "SELECT c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.yarn_composition_item1 as YARN_COMPOSITION_ID, c.YARN_COMPOSITION_PERCENTAGE1, sum(d.order_qnty) as RECEIVE_QTY, d.PROD_ID, e.LOT, f.lc_number as BTB_NO, f.lc_date as BTB_DATE, f.SUPPLIER_ID, f.id as BTB_ID FROM  tmp_poid a, com_btb_lc_pi b, com_pi_item_details c, inv_transaction d, product_details_master e, com_btb_lc_master_details f WHERE a.poid=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.id=d.pi_wo_req_dtls_id and d.prod_id=e.id and a.poid=f.id and a.type=157 and a.userid=$user_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and d.item_category=1 and d.transaction_type=1 and d.receive_basis=1 and e.status_active=1 and e.is_deleted=0 and e.item_category_id=1 and f.status_active=1 and f.is_deleted=0  and f.importer_id=$cbo_company_name GROUP BY c.pi_id, c.color_id, c.count_name, c.yarn_type, c.yarn_composition_item1, c.yarn_composition_percentage1, d.prod_id, e.lot, f.lc_number, f.lc_date, f.supplier_id, f.id ORDER BY f.id DESC"; //and f.item_category_id=1
    //echo $sql_main; 
    $sql_main_res=sql_select($sql_main);
	$pi_id_arr = array();
	$prod_id_arr = array();
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$prod_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
	}
	
	//for receive return
	$sql_rcv_rtn = "SELECT b.ID, b.CONS_QUANTITY, b.PROD_ID, c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.YARN_COMPOSITION_ITEM1, c.YARN_COMPOSITION_PERCENTAGE1 FROM inv_issue_master a, inv_transaction b, com_pi_item_details c WHERE a.id = b.mst_id AND a.pi_id = c.pi_id AND a.entry_form = 8 AND a.item_category = 1 AND a.company_id = ".$cbo_company_name." AND a.status_active=1 AND a.is_deleted=0 ".where_con_using_array($pi_id_arr, '0', 'a.pi_id')." AND b.item_category = 1 AND b.transaction_type = 3 AND b.status_active=1 AND b.is_deleted=0 ".where_con_using_array($prod_id_arr, '0', 'b.prod_id')." AND c.item_category_id = 1 AND c.status_active=1 AND c.is_deleted=0";
	//echo $sql_rcv_rtn;
    $sql_rcv_rtn_rlst = sql_select($sql_rcv_rtn);
	$rcv_rtn_data_arr = array();
	foreach($sql_rcv_rtn_rlst as $row)
	{
		$rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ITEM1']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'] += $row['CONS_QUANTITY'];
	}

    $main_data_arr=array();
    $tot_rows=0;    
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$pi_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
		
		//for receive return qty
		$rcv_rtn_qty = 0;
		$rcv_rtn_qty = $rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ID']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'];
		//for receive balance qty
		$row['RECEIVE_QTY'] = $row['RECEIVE_QTY'] - $rcv_rtn_qty;
		
		$yarn_composition=$count_arr[$row['COUNT_NAME']].' '.$composition[$row['YARN_COMPOSITION_ID']].' '.$row['YARN_COMPOSITION_PERCENTAGE1'].' '.$yarn_type[$row['YARN_TYPE']].' '.$color_arr[$row['COLOR_ID']];
		$main_data_arr[$row['PROD_ID']]['PI_ID'][$row['PI_ID']] = $row['PI_ID'];
		$main_data_arr[$row['PROD_ID']]['BTB_ID'] = $row['BTB_ID'];
		$main_data_arr[$row['PROD_ID']]['BTB_NO'] = $row['BTB_NO'];
		$main_data_arr[$row['PROD_ID']]['BTB_DATE'] = $row['BTB_DATE'];
		$main_data_arr[$row['PROD_ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
		$main_data_arr[$row['PROD_ID']]['COUNT_NAME'] = $row['COUNT_NAME'];
		$main_data_arr[$row['PROD_ID']]['YARN_COMPOSITION'] = $yarn_composition;
		$main_data_arr[$row['PROD_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$main_data_arr[$row['PROD_ID']]['COLOR_ID'] = $row['COLOR_ID'];
		$main_data_arr[$row['PROD_ID']]['LOT'] = $row['LOT'];
		$main_data_arr[$row['PROD_ID']]['RECEIVE_QTY'] += $row['RECEIVE_QTY'];
		$prod_ids .= $row['PROD_ID'].',';
		$tot_rows++;
	}
	unset($sql_main_res);
	//echo '<pre>';print_r($main_data_arr);

	if ($prod_ids != '')
	{
		$prod_ids = array_flip(array_flip(explode(',', rtrim($prod_ids,','))));
		$prod_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$prod_id_cond = ' and (';
			$prodIDArr = array_chunk($prod_ids,999);
			foreach($prodIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$prod_id_cond .= " d.prod_id in($ids) or ";
			}
			$prod_id_cond = rtrim($prod_id_cond,'or ');
			$prod_id_cond .= ')';
		}
		else
		{
			$prod_ids = implode(',', $prod_ids);
			$prod_id_cond=" and d.prod_id in ($prod_ids)";
		}
	}
	
	if ($btb_ids != '')
	{
		$btb_ids = array_flip(array_flip(explode(',', rtrim($btb_ids,','))));

		$btb_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$btb_id_cond = ' and (';
			$btbIDArr = array_chunk($btb_ids,999);
			foreach($btbIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$btb_id_cond .= " b.btb_lc_id in($ids) or ";
			}
			$btb_id_cond = rtrim($btb_id_cond,'or ');
			$btb_id_cond .= ')';
		}
		else
		{
			$btb_ids = implode(',', $btb_ids);
			$btb_id_cond=" and b.btb_lc_id in ($btb_ids)";
		}
	}

	// Temporary Table Data Delete
	$rID2=execute_query("delete from tmp_poid where userid=$user_id and type=157");
	if($rID2) oci_commit($con);
	disconnect($con);
	// End Temporary Table Data Delete

	$sql_issue_order="SELECT a.ID, a.ISSUE_PURPOSE, b.BTB_LC_ID, d.PROD_ID, d.quantity as ISSUE_QTY, e.ID as PO_ID, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO, f.BUYER_NAME
	FROM inv_issue_master a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e, wo_po_details_master f
	WHERE a.id=b.mst_id and b.id=d.trans_id and d.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.company_id=$cbo_company_name and a.entry_form=3 and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and d.entry_form=3 and d.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0 $prod_id_cond $btb_id_cond";
	//echo $sql_issue_order;
	$sql_issue_order_res=sql_select($sql_issue_order);
	
	//for issue return
	$issue_id_arr = array();
	$issue_btb_lc_id = array();
	foreach($sql_issue_order_res as $row)
	{
		$issue_id_arr['issue_id'][$row['ID']] = $row['ID'];
		$issue_id_arr['po_id'][$row['PO_ID']] = $row['PO_ID'];
		$issue_id_arr['prod_id'][$row['PROD_ID']] = $row['PROD_ID'];
		$issue_btb_lc_id[$row['ID']][$row['PROD_ID']]['btb_lc_id'] = $row['BTB_LC_ID'];
	}
	
	$sql_issue_rtn = "SELECT a.ISSUE_ID, d.ID, d.PROD_ID, d.QUANTITY, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO FROM INV_RECEIVE_MASTER a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e WHERE a.id = b.mst_id AND b.id = d.trans_id AND d.po_breakdown_id = e.id AND a.entry_form = 9 AND a.item_category = 1 and a.receive_basis = 3 AND a.company_id = ".$cbo_company_name." AND a.status_active = 1 AND a.is_deleted = 0 AND b.item_category = 1 AND b.transaction_type = 4 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.is_deleted = 0".where_con_using_array($issue_id_arr['issue_id'], '0', 'a.issue_id').where_con_using_array($issue_id_arr['prod_id'], '0', 'd.prod_id').where_con_using_array($issue_id_arr['po_id'], '0', 'e.id');
	//echo $sql_issue_rtn;
	$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);
	$issue_rtn_data_arr = array();
	$duplicate_check = array();
	foreach($sql_issue_rtn_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$btb_lc_id = $issue_btb_lc_id[$row['ISSUE_ID']][$row['PROD_ID']]['btb_lc_id'];
			$issue_rtn_data_arr[$btb_lc_id][$row['PROD_ID']][$row['PO_NUMBER']][$row['ISSUE_ID']]['qty'] += $row['QUANTITY'];
		}
	}	
	
    $issue_order_data_arr=array();
    $row_span_arr=array();
	foreach($sql_issue_order_res as $row)
	{
		//for receive return qty
		$issue_rtn_qty = 0;
		$issue_rtn_qty = $issue_rtn_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['PO_NUMBER']][$row['ID']]['qty'];
		//for issue balance qty
		$row['ISSUE_QTY'] = $row['ISSUE_QTY'] - $issue_rtn_qty;
		
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][]=array(
			'ISSUE_PURPOSE'=>$row['ISSUE_PURPOSE'],
			'ISSUE_QTY'=>$row['ISSUE_QTY'],
			'PO_NUMBER'=>$row['PO_NUMBER'],
			'BUYER_NAME'=>$row['BUYER_NAME'],
			'INTERNAL_REF'=>$row['INTERNAL_REF'],
			'FILE_NO'=>$row['FILE_NO'],
			'ID'=>$row['ID']
		);
		$row_span_arr[$row['BTB_LC_ID']][$row['PROD_ID']][]++;
	}
	unset($sql_issue_order_res);

	$table_width="1940";
	$div_width="1960";
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all;word-wrap: break-word;}
	</style>
	<div style="width:100%; margin-left:10px;" align="left">
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
            	<tr>
                    <th rowspan="2" width="100">File No</th>
                    <th rowspan="2" width="150">BTB No</th>
                    <th rowspan="2" width="80">BTB Date</th>
                    <th rowspan="2" width="120">Supplier Name</th>
                    <th colspan="6" width="710">Reveive Information</th>
                    <th colspan="7" width="780">Issue Information</th>
                </tr>
            	<tr>
            		<!-- Reveive Information -->
                    <th width="80">Count</th>
                    <th width="250">Composition</th>
                    <th width="80">Type</th>
                    <th width="100">Color</th>
                    <th width="100">Lot</th>
                    <th width="100">Received Qty</th>

                    <!-- Issue Information -->
                    <th width="120">PO No</th>
                    <th width="120">Buyer Name</th>
                    <th width="100">Issue Qty</th>
                    <th width="100">Issue Purpose</th>
                    <th width="120">Internal Ref</th>
                    <th width="120">LC/SC No</th>
                    <th width="100">File No</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:auto; width:<?= $div_width; ?>px;" id="scroll_body">
	        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
	        	<tbody>
	        	<?
	        	$i=1;$m=1;//$btb_check_val_arr=array();
            	foreach($main_data_arr as $prod_id=>$row)
            	{
					$row_span_cond='';
					$row_span= count($row_span_arr[$row['BTB_ID']][$prod_id]);
					//echo $row_span;
					if ($row_span != 0) $row_span_cond='rowspan="'.$row_span.'"';
					$btb_ID=$row['BTB_ID'];

					$btbID_check_val=$row['BTB_ID'];
					if (!in_array($btbID_check_val,$btb_check_val_arr) )
					{						
						if($m != 1)
						{
							?>
				            <tr style="background-color: #DBDBDB;">
				                <td width="100">&nbsp;</td>
				                <td width="150">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="250">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="100">&nbsp;</td>
				                <td width="100" align="right"><strong>BTB Total</strong></td>
				                <td width="100" align="right"><strong><?= number_format($sub_total_rec_qty,2); ?></strong></td>
				                <td width="120">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="100" align="right"><strong><?= number_format($sub_total_issue_qty,2); ?></strong></td>
				                <td width="100">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="100">&nbsp;</td>
				            </tr>
							<?
						}
						$btb_check_val_arr[]=$btbID_check_val;
						$m++;
						unset($sub_total_rec_qty);
						unset($sub_total_issue_qty);
					}
    				?>
    				<tr bgcolor="<?= $bgcolor;?>" id="tr<?= $i; ?>">
			            <td width="100" <?=$row_span_cond; ?> class="wrd_brk"><?= $file_data_arr[$btb_ID]['INTERNAL_FILE_NO']; ?></td>
		                <td width="150" <?= $row_span_cond; ?> class="wrd_brk"><?= $row['BTB_NO']; ?></td>
		                <td width="80" <?= $row_span_cond; ?> class="wrd_brk"><?= change_date_format($row['BTB_DATE']); ?></td>
		                <td width="120" <?= $row_span_cond; ?> class="wrd_brk"><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></td>
		                <td width="80" <?= $row_span_cond; ?> class="wrd_brk"><?= $count_arr[$row['COUNT_NAME']]; ?></td>
		                <td width="250" <?= $row_span_cond; ?> class="wrd_brk"><?= $row['YARN_COMPOSITION']; ?></td>
		                <td width="80" <?= $row_span_cond; ?> class="wrd_brk"><?= $yarn_type[$row['YARN_TYPE']]; ?></td>
		                <td width="100" <?= $row_span_cond; ?> class="wrd_brk"><?= $color_arr[$row['COLOR_ID']]; ?></td>
		                <td width="100" <?= $row_span_cond; ?> class="wrd_brk"><?= $row['LOT']; ?></td>
		                <td width="100" <?= $row_span_cond; ?> class="wrd_brk" align="right"><a href='##' onClick="openmypage_receive_popup('<?= $cbo_company_name; ?>','<?= implode(',',$row['PI_ID']); ?>','<?= $prod_id; ?>', '<?= $btb_ID; ?>')"><font color="blue"><b><?= number_format($row['RECEIVE_QTY'],2); ?></b></font></a></td>
		            	<?
		            	if ($row_span != 0)
		            	{
		            		foreach ($issue_order_data_arr[$row['BTB_ID']][$prod_id] as $rows)
		            		{
		            			?>
		            			<td width="120" class="wrd_brk">&nbsp;<?= $rows['PO_NUMBER']; ?></td>
				                <td width="120" class="wrd_brk"><?= $buyer_arr[$rows['BUYER_NAME']]; ?></td>
				                <td width="100" class="wrd_brk" align="right"><a href='##' onClick="openmypage_issue_popup('<?= $cbo_company_name; ?>','<?= $rows['ID']; ?>','<?= $prod_id; ?>','<?= $file_data_arr[$btb_ID]['LC_SC_NO']; ?>')"><font color="blue"><b><?= number_format($rows['ISSUE_QTY'],2); ?></b></font></a></td>
				                <td width="100" class="wrd_brk">&nbsp;<?= $yarn_issue_purpose[$rows['ISSUE_PURPOSE']]; ?></td>
				                <td width="120" class="wrd_brk"><?= $rows['INTERNAL_REF']; ?></td>
				                <td width="120" class="wrd_brk"><?= $file_data_arr[$btb_ID]['LC_SC_NO']; ?></td>
				                <td width="100" class="wrd_brk"><?= $rows['FILE_NO']; ?></td>
				                </tr>
		            			<?
		            			$sub_total_issue_qty+=$rows['ISSUE_QTY'];
		            			$grand_total_issue_qty+=$rows['ISSUE_QTY'];
		            		}
		            	}
		            	else
		            	{
		            		?>
		            		<td width="120">&nbsp;</td>
			                <td width="120">&nbsp;</td>
			                <td width="100">&nbsp;</td>
			                <td width="100">&nbsp;</td>
			                <td width="120">&nbsp;</td>
			                <td width="120">&nbsp;</td>
			                <td width="100">&nbsp;</td>
			                </tr>
			                <?
		            	}
	                $i++;
	                $sub_total_rec_qty+=$row['RECEIVE_QTY'];	                
	                $grand_total_rec_qty+=$row['RECEIVE_QTY'];	                 
	            }
				?>
				</tbody>                   
	        </table>
        </div>
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr style="background-color: #DBDBDB;">
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100" align="right"><strong>BTB Total</strong></td>
                <td width="100" align="right"><strong><?= number_format($sub_total_rec_qty,2); ?></strong></td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100" align="right"><strong><?= number_format($sub_total_issue_qty,2); ?></strong></td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr style="background-color: #DBDBDB;">
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100" align="right"><strong>Grand Total</strong></td>
                <td width="100" align="right"><strong><?= number_format($grand_total_rec_qty,2); ?></strong></td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100" align="right"><strong><?= number_format($grand_total_issue_qty,2); ?></strong></td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>
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
	echo "$total_data####$filename";
	exit();  
}
//for show button

//for show-2 button
if($action==="report_generate_2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	//$txt_internal_file_no=str_replace("'","",$txt_internal_file_no);
	$txt_internal_file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
	$txt_internal_file_no="";
	foreach($txt_internal_file_no_arr as $file_no)
	{
		$txt_internal_file_no.="'".$file_no."',";
	}
	$txt_internal_file_no=chop($txt_internal_file_no,",");
	$file_cond='';
	if ($txt_internal_file_no=="") $file_cond=""; else $file_cond=" and a.internal_file_no in ($txt_internal_file_no) ";

	$company_arr=return_library_array("select id,company_name from lib_company where id=$cbo_company_name",'id','company_name');
	$buyer_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array("select id,color_name from lib_color", "id", "color_name");

	$sql_file = "SELECT a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID FROM com_export_lc a, com_btb_export_lc_attachment b WHERE a.id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lc_year='$cbo_file_year' $file_cond
    UNION All
    SELECT a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID FROM com_sales_contract a, com_btb_export_lc_attachment b WHERE a.id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.sc_year='$cbo_file_year' $file_cond";
	//echo $sql_file;//die;
    $sql_file_res=sql_select($sql_file);
    $file_data_arr=array();
	foreach($sql_file_res as $row)
	{		
		$btb_IDs .= $row['BTB_ID'].',';
		$file_data_arr[$row['BTB_ID']]['LC_SC_NO']=$row['LC_SC_NO'];
		$file_data_arr[$row['BTB_ID']]['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
		$file_data_arr[$row['BTB_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
	}
	unset($sql_file_res);

	$btb_IDs=rtrim($btb_IDs,',');
	$ex_btb_ids_arr=explode(',', $btb_IDs);
	
	//Temporary Table Data Insert
	$con=connect();
	$rID=true;
	foreach ($ex_btb_ids_arr as $btb_id)
	{
		//echo "insert into tmp_poid (userid, poid, type) values ($user_id,$btb_id,157)";
		$rID=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$btb_id,157)");
	}
	if ($rID) oci_commit($con);
	//End Temporary Table Data Insert

	$sql_main = "SELECT c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.yarn_composition_item1 as YARN_COMPOSITION_ID, c.YARN_COMPOSITION_PERCENTAGE1, sum(d.order_qnty) as RECEIVE_QTY, d.PROD_ID, e.LOT, f.lc_number as BTB_NO, f.lc_date as BTB_DATE, f.SUPPLIER_ID, f.id as BTB_ID FROM  tmp_poid a, com_btb_lc_pi b, com_pi_item_details c, inv_transaction d, product_details_master e, com_btb_lc_master_details f WHERE a.poid=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.id=d.pi_wo_req_dtls_id and d.prod_id=e.id and a.poid=f.id and a.type=157 and a.userid=$user_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and d.item_category=1 and d.transaction_type=1 and d.receive_basis=1 and e.status_active=1 and e.is_deleted=0 and e.item_category_id=1 and f.status_active=1 and f.is_deleted=0  and f.importer_id=$cbo_company_name GROUP BY c.pi_id, c.color_id, c.count_name, c.yarn_type, c.yarn_composition_item1, c.yarn_composition_percentage1, d.prod_id, e.lot, f.lc_number, f.lc_date, f.supplier_id, f.id ORDER BY f.id DESC"; //and f.item_category_id=1
    //echo $sql_main;
    $sql_main_res=sql_select($sql_main);
	$pi_id_arr = array();
	$prod_id_arr = array();
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$prod_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
	}
	
	//for receive return
	$sql_rcv_rtn = "SELECT b.ID, b.CONS_QUANTITY, b.PROD_ID, c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.YARN_COMPOSITION_ITEM1, c.YARN_COMPOSITION_PERCENTAGE1 FROM inv_issue_master a, inv_transaction b, com_pi_item_details c WHERE a.id = b.mst_id AND a.pi_id = c.pi_id AND a.entry_form = 8 AND a.item_category = 1 AND a.company_id = ".$cbo_company_name." AND a.status_active=1 AND a.is_deleted=0 ".where_con_using_array($pi_id_arr, '0', 'a.pi_id')." AND b.item_category = 1 AND b.transaction_type = 3 AND b.status_active=1 AND b.is_deleted=0 ".where_con_using_array($prod_id_arr, '0', 'b.prod_id')." AND c.item_category_id = 1 AND c.status_active=1 AND c.is_deleted=0";
	//echo $sql_rcv_rtn;
    $sql_rcv_rtn_rlst = sql_select($sql_rcv_rtn);
	$rcv_rtn_data_arr = array();
	foreach($sql_rcv_rtn_rlst as $row)
	{
		$rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ITEM1']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'] += $row['CONS_QUANTITY'];
	}

    $main_data_arr=array();
    $tot_rows=0;    
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$pi_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
		
		//for receive return qty
		$rcv_rtn_qty = 0;
		$rcv_rtn_qty = $rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ID']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'];
		//for receive balance qty
		$row['RECEIVE_QTY'] = $row['RECEIVE_QTY'] - $rcv_rtn_qty;
		
		$yarn_composition=$count_arr[$row['COUNT_NAME']].' '.$composition[$row['YARN_COMPOSITION_ID']].' '.$row['YARN_COMPOSITION_PERCENTAGE1'].' '.$yarn_type[$row['YARN_TYPE']].' '.$color_arr[$row['COLOR_ID']];
		$main_data_arr[$row['PROD_ID']]['PI_ID'][$row['PI_ID']] = $row['PI_ID'];
		$main_data_arr[$row['PROD_ID']]['BTB_ID'] = $row['BTB_ID'];
		$main_data_arr[$row['PROD_ID']]['BTB_NO'] = $row['BTB_NO'];
		$main_data_arr[$row['PROD_ID']]['BTB_DATE'] = $row['BTB_DATE'];
		$main_data_arr[$row['PROD_ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
		$main_data_arr[$row['PROD_ID']]['COUNT_NAME'] = $row['COUNT_NAME'];
		$main_data_arr[$row['PROD_ID']]['YARN_COMPOSITION'] = $yarn_composition;
		$main_data_arr[$row['PROD_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$main_data_arr[$row['PROD_ID']]['COLOR_ID'] = $row['COLOR_ID'];
		$main_data_arr[$row['PROD_ID']]['LOT'] = $row['LOT'];
		$main_data_arr[$row['PROD_ID']]['RECEIVE_QTY'] += $row['RECEIVE_QTY'];
		$prod_ids .= $row['PROD_ID'].',';
		$tot_rows++;
	}
	unset($sql_main_res);
	/*echo '<pre>';
	print_r($main_data_arr);
	echo '</pre>'; die;*/

	if ($prod_ids != '')
	{
		$prod_ids = array_flip(array_flip(explode(',', rtrim($prod_ids,','))));
		$prod_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$prod_id_cond = ' and (';
			$prodIDArr = array_chunk($prod_ids,999);
			foreach($prodIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$prod_id_cond .= " d.prod_id in($ids) or ";
			}
			$prod_id_cond = rtrim($prod_id_cond,'or ');
			$prod_id_cond .= ')';
		}
		else
		{
			$prod_ids = implode(',', $prod_ids);
			$prod_id_cond=" and d.prod_id in ($prod_ids)";
		}
	}
	
	if ($btb_ids != '')
	{
		$btb_ids = array_flip(array_flip(explode(',', rtrim($btb_ids,','))));

		$btb_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$btb_id_cond = ' and (';
			$btbIDArr = array_chunk($btb_ids,999);
			foreach($btbIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$btb_id_cond .= " b.btb_lc_id in($ids) or ";
			}
			$btb_id_cond = rtrim($btb_id_cond,'or ');
			$btb_id_cond .= ')';
		}
		else
		{
			$btb_ids = implode(',', $btb_ids);
			$btb_id_cond=" and b.btb_lc_id in ($btb_ids)";
		}
	}

	// Temporary Table Data Delete
	$rID2=execute_query("delete from tmp_poid where userid=$user_id and type=157");
	if($rID2) oci_commit($con);
	disconnect($con);
	// End Temporary Table Data Delete

	$sql_issue_order="SELECT a.ID, a.ISSUE_PURPOSE, b.BTB_LC_ID, d.PROD_ID, d.quantity as ISSUE_QTY, e.ID as PO_ID, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO, f.BUYER_NAME
	FROM inv_issue_master a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e, wo_po_details_master f
	WHERE a.id=b.mst_id and b.id=d.trans_id and d.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.company_id=$cbo_company_name and a.entry_form=3 and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and d.entry_form=3 and d.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0 $prod_id_cond $btb_id_cond";
	//echo $sql_issue_order;
	$sql_issue_order_res=sql_select($sql_issue_order);
	
	//for issue return
	$issue_id_arr = array();
	$issue_btb_lc_id = array();
	foreach($sql_issue_order_res as $row)
	{
		$issue_id_arr['issue_id'][$row['ID']] = $row['ID'];
		$issue_id_arr['po_id'][$row['PO_ID']] = $row['PO_ID'];
		$issue_id_arr['prod_id'][$row['PROD_ID']] = $row['PROD_ID'];
		$issue_btb_lc_id[$row['ID']][$row['PROD_ID']]['btb_lc_id'] = $row['BTB_LC_ID'];
	}
	
	$sql_issue_rtn = "SELECT a.ISSUE_ID, d.ID, d.PROD_ID, d.QUANTITY, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO FROM INV_RECEIVE_MASTER a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e WHERE a.id = b.mst_id AND b.id = d.trans_id AND d.po_breakdown_id = e.id AND a.entry_form = 9 AND a.item_category = 1 and a.receive_basis = 3 AND a.company_id = ".$cbo_company_name." AND a.status_active = 1 AND a.is_deleted = 0 AND b.item_category = 1 AND b.transaction_type = 4 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.is_deleted = 0".where_con_using_array($issue_id_arr['issue_id'], '0', 'a.issue_id').where_con_using_array($issue_id_arr['prod_id'], '0', 'd.prod_id').where_con_using_array($issue_id_arr['po_id'], '0', 'e.id');
	//echo $sql_issue_rtn;
	$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);
	$issue_rtn_data_arr = array();
	$duplicate_check = array();
	foreach($sql_issue_rtn_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$btb_lc_id = $issue_btb_lc_id[$row['ISSUE_ID']][$row['PROD_ID']]['btb_lc_id'];
			$issue_rtn_data_arr[$btb_lc_id][$row['PROD_ID']][$row['INTERNAL_REF']][$row['ISSUE_ID']]['qty'] += $row['QUANTITY'];
			//$issue_rtn_data_arr[$btb_lc_id][$row['PROD_ID']][$row['PO_NUMBER']][$row['ISSUE_ID']]['qty'] += $row['QUANTITY'];
		}
	}	
	
    $issue_order_data_arr=array();
    $row_span_arr=array();
	foreach($sql_issue_order_res as $row)
	{
		//for receive return qty
		$issue_rtn_qty = 0;
		$issue_rtn_qty = $issue_rtn_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['ID']]['qty'];
		//for issue balance qty
		$row['ISSUE_QTY'] = $row['ISSUE_QTY'] - $issue_rtn_qty;
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['ISSUE_PURPOSE'] = $row['ISSUE_PURPOSE'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['PO_NUMBER'] = $row['PO_NUMBER'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['BUYER_NAME'] = $row['BUYER_NAME'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['INTERNAL_REF'] = $row['INTERNAL_REF'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['FILE_NO'] = $row['FILE_NO'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['ID'][] = $row['ID'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['ISSUE_QTY'] += $row['ISSUE_QTY'];
		
		//$row_span_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']][]++;
		$row_span_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]++;
	}
	unset($sql_issue_order_res);
	//echo '<pre>';print_r($row_span_arr);
	//echo count($issue_order_data_arr);	

	$table_width="1820";
	$div_width="1840";
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all;word-wrap: break-word;}
	</style>
	<div style="width:100%; margin-left:10px;" align="left">
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
            	<tr>
                    <th rowspan="2" width="100">File No</th>
                    <th rowspan="2" width="150">BTB No</th>
                    <th rowspan="2" width="80">BTB Date</th>
                    <th rowspan="2" width="120">Supplier Name</th>
                    <th colspan="6" width="710">Reveive Information</th>
                    <th colspan="6" width="660">Issue Information</th>
                </tr>
            	<tr>
            		<!-- Reveive Information -->
                    <th width="80">Count</th>
                    <th width="250">Composition</th>
                    <th width="80">Type</th>
                    <th width="100">Color</th>
                    <th width="100">Lot</th>
                    <th width="100">Received Qty</th>

                    <!-- Issue Information -->
                    <th width="120">Buyer Name</th>
                    <th width="100">Issue Qty</th>
                    <th width="100">Issue Purpose</th>
                    <th width="120">Internal Ref</th>
                    <th width="120">LC/SC No</th>
                    <th width="100">File No</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:auto; width:<?= $div_width; ?>px;" id="scroll_body">
	        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
	        	<tbody>
	        	<?
	        	$i=1;$m=1;//$btb_check_val_arr=array();
            	foreach($main_data_arr as $prod_id=>$row)
            	{
					//for row span
					$row_span_cond = '';
					$row_span = 0;
					//$row_span= count($row_span_arr[$row['BTB_ID']][$prod_id][$prod_id]);
					foreach($row_span_arr[$row['BTB_ID']][$prod_id] as $key=>$val)
					{
						$row_span++;
					}
					
					//echo $row_span;
					if ($row_span != 0) $row_span_cond='rowspan="'.$row_span.'"';
					$btb_ID=$row['BTB_ID'];

					$btbID_check_val=$row['BTB_ID'];
					if (!in_array($btbID_check_val,$btb_check_val_arr) )
					{						
						if($m != 1)
						{
							?>
				            <tr style="background-color: #DBDBDB;">
				                <td width="100">&nbsp;</td>
				                <td width="150">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="250">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="100">&nbsp;</td>
				                <td width="100" align="right"><strong>BTB Total</strong></td>
				                <td width="100" align="right"><strong><?= number_format($sub_total_rec_qty,2); ?></strong></td>
				                <td width="120">&nbsp;</td>
				                <td width="100" align="right"><strong><?= number_format($sub_total_issue_qty,2); ?></strong></td>
				                <td width="100">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="100">&nbsp;</td>
				            </tr>
							<?
						}
						$btb_check_val_arr[]=$btbID_check_val;
						$m++;
						unset($sub_total_rec_qty);
						unset($sub_total_issue_qty);
					}
    				?>
    				<tr bgcolor="<?= $bgcolor;?>" id="tr<?= $i; ?>">
			            <td width="100" <?=$row_span_cond; ?> class="wrd_brk"><?= $file_data_arr[$btb_ID]['INTERNAL_FILE_NO']; ?></td>
		                <td width="150" <?= $row_span_cond; ?> class="wrd_brk"><?= $row['BTB_NO']; ?></td>
		                <td width="80" <?= $row_span_cond; ?> class="wrd_brk"><?= change_date_format($row['BTB_DATE']); ?></td>
		                <td width="120" <?= $row_span_cond; ?> class="wrd_brk"><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></td>
		                <td width="80" <?= $row_span_cond; ?> class="wrd_brk"><?= $count_arr[$row['COUNT_NAME']]; ?></td>
		                <td width="250" <?= $row_span_cond; ?> class="wrd_brk"><?= $row['YARN_COMPOSITION']; ?></td>
		                <td width="80" <?= $row_span_cond; ?> class="wrd_brk"><?= $yarn_type[$row['YARN_TYPE']]; ?></td>
		                <td width="100" <?= $row_span_cond; ?> class="wrd_brk"><?= $color_arr[$row['COLOR_ID']]; ?></td>
		                <td width="100" <?= $row_span_cond; ?> class="wrd_brk"><?= $row['LOT']; ?></td>
		                <td width="100" <?= $row_span_cond; ?> class="wrd_brk" align="right"><a href='##' onClick="openmypage_receive_popup('<?= $cbo_company_name; ?>','<?= implode(',',$row['PI_ID']); ?>','<?= $prod_id; ?>', '<?= $btb_ID; ?>')"><font color="blue"><b><?= number_format($row['RECEIVE_QTY'],2); ?></b></font></a></td>
		            	<?
		            	if ($row_span != 0)
		            	{
		            		foreach ($issue_order_data_arr[$row['BTB_ID']][$prod_id] as $rows)
		            		{
		            			?>
				                <td width="120" class="wrd_brk"><?= $buyer_arr[$rows['BUYER_NAME']]; ?></td>
				                <td width="100" class="wrd_brk" align="right"><a href='##' onClick="openmypage_issue_popup('<?= $cbo_company_name; ?>','<?= implode(',', $rows['ID']); ?>','<?= $prod_id; ?>','<?= $file_data_arr[$btb_ID]['LC_SC_NO']; ?>')"><font color="blue"><b><?= number_format($rows['ISSUE_QTY'],2); ?></b></font></a></td>
				                <td width="100" class="wrd_brk">&nbsp;<?= $yarn_issue_purpose[$rows['ISSUE_PURPOSE']]; ?></td>
				                <td width="120" class="wrd_brk"><?= $rows['INTERNAL_REF']; ?></td>
				                <td width="120" class="wrd_brk"><?= $file_data_arr[$btb_ID]['LC_SC_NO']; ?></td>
				                <td width="100" class="wrd_brk"><?= $rows['FILE_NO']; ?></td>
				                </tr>
		            			<?
		            			$sub_total_issue_qty+=$rows['ISSUE_QTY'];
		            			$grand_total_issue_qty+=$rows['ISSUE_QTY'];
		            		}
		            	}
		            	else
		            	{
		            		?>
			                <td width="120">&nbsp;</td>
			                <td width="100">&nbsp;</td>
			                <td width="100">&nbsp;</td>
			                <td width="120">&nbsp;</td>
			                <td width="120">&nbsp;</td>
			                <td width="100">&nbsp;</td>
			                </tr>
			                <?
		            	}
	                $i++;
	                $sub_total_rec_qty+=$row['RECEIVE_QTY'];	                
	                $grand_total_rec_qty+=$row['RECEIVE_QTY'];	                 
	            }
				?>
				</tbody>                   
	        </table>
        </div>
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr style="background-color: #DBDBDB;">
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100" align="right"><strong>BTB Total</strong></td>
                <td width="100" align="right"><strong><?= number_format($sub_total_rec_qty,2); ?></strong></td>
                <td width="120">&nbsp;</td>
                <td width="100" align="right"><strong><?= number_format($sub_total_issue_qty,2); ?></strong></td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr style="background-color: #DBDBDB;">
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100" align="right"><strong>Grand Total</strong></td>
                <td width="100" align="right"><strong><?= number_format($grand_total_rec_qty,2); ?></strong></td>
                <td width="120">&nbsp;</td>
                <td width="100" align="right"><strong><?= number_format($grand_total_issue_qty,2); ?></strong></td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>
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
	echo "$total_data####$filename";
	exit();  
}
//end for show-2 button

//for show-3 button
if($action==="report_generate_3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	$txt_internal_file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
	$txt_internal_file_no="";
	foreach($txt_internal_file_no_arr as $file_no)
	{
		$txt_internal_file_no.="'".$file_no."',";
	}
	$txt_internal_file_no=chop($txt_internal_file_no,",");
	
	$file_cond='';
	if ($txt_internal_file_no=="") $file_cond="";
	else $file_cond=" and a.internal_file_no in (".$txt_internal_file_no.")";

	$company_arr=return_library_array("select id,company_name from lib_company where id=$cbo_company_name",'id','company_name');
	$buyer_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array("select id,color_name from lib_color", "id", "color_name");

	$sql_file = "SELECT a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID 
	FROM com_export_lc a, com_btb_export_lc_attachment b 
	WHERE a.id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lc_year='$cbo_file_year' $file_cond
    UNION All
    SELECT a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID 
	FROM com_sales_contract a, com_btb_export_lc_attachment b 
	WHERE a.id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.sc_year='$cbo_file_year' $file_cond";
	//echo $sql_file;//die;
    $sql_file_res=sql_select($sql_file);
    $file_data_arr=array();
	foreach($sql_file_res as $row)
	{		
		$btb_IDs .= $row['BTB_ID'].',';
		$file_data_arr[$row['BTB_ID']]['LC_SC_NO']=$row['LC_SC_NO'];
		$file_data_arr[$row['BTB_ID']]['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
		$file_data_arr[$row['BTB_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
	}
	unset($sql_file_res);

	$btb_IDs=rtrim($btb_IDs,',');
	$ex_btb_ids_arr=explode(',', $btb_IDs);
	
	//Temporary Table Data Insert
	$con=connect();
	$rID=true;
	foreach ($ex_btb_ids_arr as $btb_id)
	{
		$rID=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$btb_id,157)");
	}
	if ($rID) oci_commit($con);
	//End Temporary Table Data Insert

	$sql_main = "SELECT c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.yarn_composition_item1 as YARN_COMPOSITION_ID, c.YARN_COMPOSITION_PERCENTAGE1, sum(d.order_qnty) as RECEIVE_QTY, d.PROD_ID, e.LOT, f.lc_number as BTB_NO, f.lc_date as BTB_DATE, f.SUPPLIER_ID, f.id as BTB_ID 
	FROM  tmp_poid a, com_btb_lc_pi b, com_pi_item_details c, inv_transaction d, product_details_master e, com_btb_lc_master_details f 
	WHERE a.poid=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.id=d.pi_wo_req_dtls_id and d.prod_id=e.id and a.poid=f.id and a.type=157 and a.userid=$user_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and d.item_category=1 and d.transaction_type=1 and d.receive_basis=1 and e.status_active=1 and e.is_deleted=0 and e.item_category_id=1 and f.status_active=1 and f.is_deleted=0  and f.pi_entry_form=165 and f.importer_id=$cbo_company_name
	GROUP BY c.pi_id, c.color_id, c.count_name, c.yarn_type, c.yarn_composition_item1, c.yarn_composition_percentage1, d.prod_id, e.lot, f.lc_number, f.lc_date, f.supplier_id, f.id 
	ORDER BY f.id DESC"; //and f.item_category_id=1
    //echo $sql_main;die;
    $sql_main_res=sql_select($sql_main);
	$pi_id_arr = array();
	$prod_id_arr = array();
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$prod_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
	}
	
	if(empty($sql_main_res))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$sql_lc = "SELECT a.export_lc_no as LC_SC_NO, b.WO_PO_BREAK_DOWN_ID
	FROM com_export_lc a, COM_EXPORT_LC_ORDER_INFO b  
	WHERE a.id=b.COM_EXPORT_LC_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lc_year='$cbo_file_year'
    UNION All
    SELECT a.contract_no as LC_SC_NO, b.WO_PO_BREAK_DOWN_ID 
	FROM com_sales_contract a, COM_SALES_CONTRACT_ORDER_INFO b 
	WHERE a.id=b.COM_SALES_CONTRACT_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name
	and a.sc_year='$cbo_file_year'
	";
	//echo $sql_lc;
    $sql_lc_rslt = sql_select($sql_lc);
	$lc_data_arr = array();
	foreach($sql_lc_rslt as $row)
	{
		$lc_data_arr[$row['WO_PO_BREAK_DOWN_ID']] = $row['LC_SC_NO'];
	}
	/*echo "<pre>";
	print_r($lc_data_arr);
	echo "</pre>";*/

	//for receive return
	$sql_rcv_rtn = "SELECT b.ID, b.CONS_QUANTITY, b.PROD_ID, c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.YARN_COMPOSITION_ITEM1, c.YARN_COMPOSITION_PERCENTAGE1 FROM inv_issue_master a, inv_transaction b, com_pi_item_details c WHERE a.id = b.mst_id AND a.pi_id = c.pi_id AND a.entry_form = 8 AND a.item_category = 1 AND a.company_id = ".$cbo_company_name." AND a.status_active=1 AND a.is_deleted=0 ".where_con_using_array($pi_id_arr, '0', 'a.pi_id')." AND b.item_category = 1 AND b.transaction_type = 3 AND b.status_active=1 AND b.is_deleted=0 ".where_con_using_array($prod_id_arr, '0', 'b.prod_id')." AND c.item_category_id = 1 AND c.status_active=1 AND c.is_deleted=0";
	//echo $sql_rcv_rtn;
    $sql_rcv_rtn_rlst = sql_select($sql_rcv_rtn);
	$rcv_rtn_data_arr = array();
	foreach($sql_rcv_rtn_rlst as $row)
	{
		$rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ITEM1']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'] += $row['CONS_QUANTITY'];
	}

    $main_data_arr=array();
    $tot_rows=0;    
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$pi_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
		
		//for receive return qty
		$rcv_rtn_qty = 0;
		$rcv_rtn_qty = $rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ID']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'];
		//for receive balance qty
		$row['RECEIVE_QTY'] = $row['RECEIVE_QTY'] - $rcv_rtn_qty;
		
		$yarn_composition=$count_arr[$row['COUNT_NAME']].' '.$composition[$row['YARN_COMPOSITION_ID']].' '.$row['YARN_COMPOSITION_PERCENTAGE1'].' '.$yarn_type[$row['YARN_TYPE']].' '.$color_arr[$row['COLOR_ID']];
		
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['PI_ID'][$row['PI_ID']] = $row['PI_ID'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['BTB_ID'] = $row['BTB_ID'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['BTB_NO'] = $row['BTB_NO'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['BTB_DATE'] = $row['BTB_DATE'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['COUNT_NAME'] = $row['COUNT_NAME'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['YARN_COMPOSITION'] = $yarn_composition;
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['COLOR_ID'] = $row['COLOR_ID'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['LOT'] = $row['LOT'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['RECEIVE_QTY'] += $row['RECEIVE_QTY'];
		$prod_ids .= $row['PROD_ID'].',';
		$tot_rows++;
	}
	unset($sql_main_res);
	//echo '<pre>';
	//print_r($main_data_arr);
	//echo '</pre>'; die;

	if ($prod_ids != '')
	{
		$prod_ids = array_flip(array_flip(explode(',', rtrim($prod_ids,','))));
		$prod_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$prod_id_cond = ' and (';
			$prodIDArr = array_chunk($prod_ids,999);
			foreach($prodIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$prod_id_cond .= " d.prod_id in($ids) or ";
			}


			$prod_id_cond = rtrim($prod_id_cond,'or ');
			$prod_id_cond .= ')';
		}
		else
		{
			$prod_ids = implode(',', $prod_ids);
			$prod_id_cond=" and d.prod_id in ($prod_ids)";
		}
	}
	
	if ($btb_ids != '')
	{
		$btb_ids = array_flip(array_flip(explode(',', rtrim($btb_ids,','))));

		$btb_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$btb_id_cond = ' and (';
			$btbIDArr = array_chunk($btb_ids,999);
			foreach($btbIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$btb_id_cond .= " b.btb_lc_id in($ids) or ";
			}
			$btb_id_cond = rtrim($btb_id_cond,'or ');
			$btb_id_cond .= ')';
		}
		else
		{
			$btb_ids = implode(',', $btb_ids);
			$btb_id_cond=" and b.btb_lc_id in ($btb_ids)";
		}
	}

	// Temporary Table Data Delete
	$rID2=execute_query("delete from tmp_poid where userid=$user_id and type=157");
	if($rID2) oci_commit($con);
	disconnect($con);
	// End Temporary Table Data Delete

	$sql_issue_order="SELECT a.ID, a.ISSUE_PURPOSE, b.BTB_LC_ID, d.PROD_ID, d.quantity as ISSUE_QTY, e.ID as PO_ID, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO, e.SC_LC, f.BUYER_NAME
	FROM inv_issue_master a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e, wo_po_details_master f
	WHERE a.id=b.mst_id and b.id=d.trans_id and d.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.company_id=$cbo_company_name and a.entry_form=3 and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and d.entry_form=3 and d.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0 $prod_id_cond $btb_id_cond";
	//echo $sql_issue_order;
	$sql_issue_order_res=sql_select($sql_issue_order);
	
	//for issue return
	$issue_id_arr = array();
	$issue_btb_lc_id = array();
	foreach($sql_issue_order_res as $row)
	{
		$issue_id_arr['issue_id'][$row['ID']] = $row['ID'];
		$issue_id_arr['po_id'][$row['PO_ID']] = $row['PO_ID'];
		$issue_id_arr['prod_id'][$row['PROD_ID']] = $row['PROD_ID'];
		$issue_btb_lc_id[$row['ID']][$row['PROD_ID']]['btb_lc_id'] = $row['BTB_LC_ID'];
	}
	/*echo '<pre>';
	print_r($issue_id_arr['po_id']);
	echo '</pre>';
	die;*/
	
	$sql_issue_rtn = "SELECT a.ISSUE_ID, d.ID, d.PROD_ID, d.QUANTITY, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO FROM INV_RECEIVE_MASTER a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e WHERE a.id = b.mst_id AND b.id = d.trans_id AND d.po_breakdown_id = e.id AND a.entry_form = 9 AND a.item_category = 1 and a.receive_basis = 3 AND a.company_id = ".$cbo_company_name." AND a.status_active = 1 AND a.is_deleted = 0 AND b.item_category = 1 AND b.transaction_type = 4 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.is_deleted = 0".where_con_using_array($issue_id_arr['issue_id'], '0', 'a.issue_id').where_con_using_array($issue_id_arr['prod_id'], '0', 'd.prod_id').where_con_using_array($issue_id_arr['po_id'], '0', 'e.id');
	//echo $sql_issue_rtn;
	$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);
	$issue_rtn_data_arr = array();
	$duplicate_check = array();
	foreach($sql_issue_rtn_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])

		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$btb_lc_id = $issue_btb_lc_id[$row['ISSUE_ID']][$row['PROD_ID']]['btb_lc_id'];
			$issue_rtn_data_arr[$btb_lc_id][$row['PROD_ID']][$row['INTERNAL_REF']][$row['ISSUE_ID']]['qty'] += $row['QUANTITY'];
		}
	}	
	
    $issue_order_data_arr=array();
    $row_span_arr=array();
	foreach($sql_issue_order_res as $row)
	{
		//for receive return qty
		$issue_rtn_qty = 0;
		$issue_rtn_qty = $issue_rtn_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']][$row['ID']]['qty'];
		//for issue balance qty
		$row['ISSUE_QTY'] = $row['ISSUE_QTY'] - $issue_rtn_qty;
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['ISSUE_PURPOSE'] = $row['ISSUE_PURPOSE'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['PO_NUMBER'] = $row['PO_NUMBER'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['BUYER_NAME'] = $row['BUYER_NAME'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['INTERNAL_REF'] = $row['INTERNAL_REF'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['FILE_NO'] = $row['FILE_NO'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['ID'][] = $row['ID'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['ISSUE_QTY'] += $row['ISSUE_QTY'];
		
		//for export lc no
		$row['LC_SC'] = $lc_data_arr[$row['PO_ID']];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['LC_SC'][$row['LC_SC']] = $row['LC_SC'];
		
		$row_span_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]++;
	}
	unset($sql_issue_order_res);
	
	/*echo '<pre>';
	print_r($issue_order_data_arrs);
	echo '</pre>';
	die;*/
	
	$sql_pi_rslt = sql_select("SELECT A.PI_NUMBER, B.PI_ID, B.COUNT_NAME, B.YARN_COMPOSITION_ITEM1, B.YARN_COMPOSITION_PERCENTAGE1, B.YARN_TYPE, B.COLOR_ID, B.QUANTITY FROM COM_PI_MASTER_DETAILS A, COM_PI_ITEM_DETAILS B WHERE A.ID = B.PI_ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0".where_con_using_array($pi_id_arr, '0', 'B.PI_ID'));
	//$pi_id_arr
	$pi_dtls = array();
	$pi_data = array();
	foreach($sql_pi_rslt as $row)
	{
		$yrn_compo=$count_arr[$row['COUNT_NAME']].' '.$composition[$row['YARN_COMPOSITION_ITEM1']].' '.$row['YARN_COMPOSITION_PERCENTAGE1'].' '.$yarn_type[$row['YARN_TYPE']].' '.$color_arr[$row['COLOR_ID']];
		
		$pi_dtls[$row['PI_ID']] = $row['PI_NUMBER'];
		$pi_data[$row['PI_ID']][$row['COUNT_NAME']][$yrn_compo]['PI_QTY'] = $row['QUANTITY'];
	}
	//echo '<pre>';print_r($main_data_arr);
	/*
	
	echo '</pre>';
	die;*/

	$table_width="2050";
	$div_width="2170";
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all; }
		.tr_total{
			background-color:#CCC;
		}
		.td_total{
			font-size:13px;
			font-weight:bold;
			text-align:right;
			padding-right:3px;
		}
	</style>
	<div style="width:100%; margin-left:10px;" align="left">
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
            	<tr>
                    <th rowspan="2" width="30">Sl</th>
                    <th rowspan="2" width="100">File No</th>
                    <th rowspan="2" width="100">Buyer</th>
                    <th rowspan="2" width="150">BTB No</th>
                    <th rowspan="2" width="80">BTB Date</th>
                    <th rowspan="2" width="120">Supplier Name</th>
                    <th rowspan="2" width="100">PI No</th>
                    <th colspan="7" width="710">Reveive Information</th>
                    <th colspan="5" width="560">Issue Information</th>
                </tr>
            	<tr>
            		<!-- Reveive Information -->
                    <th width="80">Count</th>
                    <th width="250">Composition</th>
                    <th width="80">Type</th>
                    <th width="100">Color</th>
                    <th width="100">PI Qty.(Kg)</th>
                    <th width="100">Lot</th>
                    <th width="100">Received Qty</th>

                    <!-- Issue Information -->
                    <th width="120">Buyer Name</th>
                    <th width="100">Issue Qty</th>
                    <th width="120">Internal Ref</th>
                    <th width="120">LC/SC No</th>
                    <th width="100">File No</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:auto; width:<?= $div_width; ?>px;" id="scroll_body">
	        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
	        	<tbody>
	        	<?
				$r_spn = array();
				$r_spn_pi = array();

				$r_spn_cnt = array();
				$r_spn_compo = array();
				$r_spn_prd = array();
				//$r_spn_typ = array();
				//$r_spn_clr = array();
				$r_spn_iss = array();
            	foreach($main_data_arr as $k_btb_no=>$v_btb_no)
				{
					foreach($v_btb_no as $k_pi_id=>$v_pi_id)
					{
						$r_spn_pi2[$k_btb_no]++;
						foreach($v_pi_id as $k_count=>$v_count)
						{
							if($r_spn_cnt2_check[$k_btb_no][$k_pi_id][$k_count]=="")
							{
								$r_spn_cnt2_check[$k_btb_no][$k_pi_id][$k_count]=$k_pi_id;
								$r_spn_cnt2[$k_btb_no]++;
							}
							
							foreach($v_count as $k_compo=>$v_compo)
							{
								foreach($v_compo as $prod_id=>$row)
								{
									//for btb row span
									$r_spn[$row['BTB_ID']]++;
									
									if(!empty($issue_order_data_arr[$row['BTB_ID']][$prod_id]))
									{
										foreach($issue_order_data_arr[$row['BTB_ID']][$prod_id] as $key=>$val)
										{
											//for pi row span
											$r_spn_pi[$k_btb_no][$k_pi_id]++;
											
											//for count row span
											$r_spn_cnt[$k_btb_no][$k_pi_id][$k_count]++;
											
											//for composition row span
											$r_spn_compo[$k_btb_no][$k_pi_id][$k_count][$k_compo]++;
											
											//for product row span
											$r_spn_prd[$k_btb_no][$k_pi_id][$k_count][$k_compo][$prod_id]++;
											
											//for issue row span
											$r_spn_iss[$row['BTB_ID']]++;
										}
									}
									else
									{
										//for pi row span
										$r_spn_pi[$k_btb_no][$k_pi_id]++;
											
										//for count row span
										$r_spn_cnt[$k_btb_no][$k_pi_id][$k_count]++;
										
										//for composition row span
										$r_spn_compo[$k_btb_no][$k_pi_id][$k_count][$k_compo]++;
											
										//for product row span
										$r_spn_prd[$k_btb_no][$k_pi_id][$k_count][$k_compo][$prod_id]++;
										
										//for issue row span
										$r_spn_iss[$row['BTB_ID']]++;
									}
								}
							}
						}
					}
				}
				//echo "<pre>";print_r($r_spn_pi);echo "jk<pre>";print_r($r_spn_cnt);echo "jk<pre>";print_r($r_spn_compo);echo "jk<pre>";print_r($r_spn_iss);die;
				/*echo "<pre>";
				print_r($r_spn_compo);
				echo "</pre>";
				die;*/
	        	$i=0;
            	foreach($main_data_arr as $k_btb_no=>$v_btb_no)
				{
					$btb_rspn = 1;
					foreach($v_btb_no as $k_pi_id=>$v_pi_id)
					{
						$i++;
						$pi_rspn = 1;
						$pi_total_pi_qty = 0;
						$pi_total_rcv_qty = 0;
						$pi_total_issue_qty = 0;
						foreach($v_pi_id as $k_count=>$v_count)
						{
							$cnt_rspn = 1;
							$cnt_total_pi_qty = 0;
							$cnt_total_rcv_qty = 0;
							$cnt_total_issue_qty = 0;
							foreach($v_count as $k_compo=>$v_compo)
							{
								$compo_rspn = 1;
								foreach($v_compo as $prod_id=>$row)
								{
									//issue row span
									$iss_rspn = 1;
									$r_span_issue = $r_spn_iss[$row['BTB_ID']];
									
									//for compsition row span
									$compo_row_span_cond = '';
									$compo_row_span = $r_spn_compo[$k_btb_no][$k_pi_id][$k_count][$k_compo];
									if ($compo_row_span != 0) $compo_row_span_cond='rowspan="'.$compo_row_span.'"';
									
									//for cout row span
									$cnt_row_span_cond = '';
									$cnt_row_span = count($r_spn_compo[$k_btb_no][$k_pi_id][$k_count][$k_compo])+$r_spn_cnt[$k_btb_no][$k_pi_id][$k_count];
									if ($cnt_row_span != 0) $cnt_row_span_cond='rowspan="'.$cnt_row_span.'"';
									
									//for pi row span
									$pi_row_span_cond = '';
									$pi_row_span = count($r_spn_cnt[$k_btb_no][$k_pi_id])+$r_spn_pi[$k_btb_no][$k_pi_id];
									if ($pi_row_span != 0) $pi_row_span_cond='rowspan="'.$pi_row_span.'"';
	
									//for btb row span
									//echo 
									$row_span_cond = '';
									if($btb_rspn==1)
									{
										//print_r($r_spn_cnt2);echo "<br>";
										//echo $r_spn_pi2[$k_btb_no]."=".$r_spn_cnt2[$k_btb_no]."=".$row['BTB_NO']."=".count($r_spn_cnt[$k_btb_no][$k_pi_id])."=".count($r_spn_pi[$k_btb_no])."=".$r_span_issue."<br>";
										$row_span = $r_spn_cnt2[$k_btb_no]+$r_spn_pi2[$k_btb_no]+$r_span_issue;
										if ($row_span != 0) $row_span_cond='rowspan="'.$row_span.'"';
									}
									
									//for product row span
									$prd_row_span_cond='';
									$prd_row_span = $r_spn_prd[$k_btb_no][$k_pi_id][$k_count][$k_compo][$prod_id];
									if ($prd_row_span != 0) $prd_row_span_cond='rowspan="'.$prd_row_span.'"';

									//for pi qty
									$row['PI_QTY'] = $pi_data[$k_pi_id][$k_count][$k_compo]['PI_QTY'];
									?>
									<tr bgcolor="<?= $bgcolor;?>" id="tr<?= $i; ?>" valign="middle">
										<?
										if($btb_rspn == 1)
										{
											$btb_rspn++;
											?>
                                            <td width="30" <?=$row_span_cond; ?> class="wrd_brk"><?= $i; ?></td>
                                            <td width="100" <?=$row_span_cond; ?> class="wrd_brk"><?= $file_data_arr[$row['BTB_ID']]['INTERNAL_FILE_NO']; ?></td>
                                            <td width="100" <?=$row_span_cond; ?> class="wrd_brk"><?= $buyer_arr[$file_data_arr[$row['BTB_ID']]['BUYER_NAME']]; ?></td>
                                            <td width="150" <?=$row_span_cond; ?> class="wrd_brk"><?= $row['BTB_NO']; ?></td>
                                            <td width="80" <?=$row_span_cond; ?> class="wrd_brk"><?= change_date_format($row['BTB_DATE']); ?></td>
                                            <td width="120" <?=$row_span_cond; ?> class="wrd_brk"><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></td>
											<?
										}
										if($pi_rspn == 1)
										{
											$pi_rspn++;
											?>
											<td width="100" <?= $pi_row_span_cond; ?> class="wrd_brk"><?= $pi_dtls[$k_pi_id]; ?></td>        
											<?
										}
										if($cnt_rspn == 1)
										{
											$cnt_rspn++;
											?>
											<td width="80" <?= $cnt_row_span_cond; ?> class="wrd_brk"><?= $count_arr[$row['COUNT_NAME']]; ?></td>
											<?
										}
										if($compo_rspn == 1)
										{
											$compo_rspn++;
											?>

											<td width="250" <?= $compo_row_span_cond; ?> class="wrd_brk"><?= $row['YARN_COMPOSITION']; ?></td>
											<td width="80" <?= $compo_row_span_cond; ?> class="wrd_brk"><?= $yarn_type[$row['YARN_TYPE']]; ?></td>
											<td width="100" <?= $compo_row_span_cond; ?> class="wrd_brk"><?= $color_arr[$row['COLOR_ID']]; ?></td>
											<td width="100" <?= $compo_row_span_cond; ?> class="wrd_brk" align="right"><?= number_format($row['PI_QTY'],2); ?></td>
											<?
											$cnt_total_pi_qty += $row['PI_QTY'];
											$pi_total_pi_qty += $row['PI_QTY'];											
										}
										if($iss_rspn == 1)
										{
											$iss_rspn++;
											?>                                            
											<td width="100" <?= $prd_row_span_cond; ?> class="wrd_brk"><?= $row['LOT']; ?></td>
                                            <td width="100" <?= $prd_row_span_cond; ?> class="wrd_brk" align="right"><a href='##' onClick="openmypage_receive_popup('<?= $cbo_company_name; ?>','<?= implode(',',$row['PI_ID']); ?>','<?= $prod_id; ?>', '<?= $btb_ID; ?>')"><font color="blue"><b><?= number_format($row['RECEIVE_QTY'],2); ?></b></font></a></td>
											<?
											$cnt_total_rcv_qty += $row['RECEIVE_QTY'];
											$pi_total_rcv_qty += $row['RECEIVE_QTY'];
										}
										
										foreach ($issue_order_data_arr[$row['BTB_ID']][$prod_id] as $rows)
										{
											?>
											<td width="120" class="wrd_brk"><?= $buyer_arr[$rows['BUYER_NAME']]; ?></td>
											<td width="100" class="wrd_brk" align="right"><a href='##' onClick="openmypage_issue_popup('<?= $cbo_company_name; ?>','<?= implode(',', $rows['ID']); ?>','<?= $prod_id; ?>','<?= implode(', ',$rows['LC_SC']); ?>')"><font color="blue"><b><?= number_format($rows['ISSUE_QTY'],2); ?></b></font></a></td>
											<td width="120" class="wrd_brk"><?= $rows['INTERNAL_REF']; ?></td>
											<td width="120" class="wrd_brk"><?= implode(', ',$rows['LC_SC']); ?></td>
											<td width="100" class="wrd_brk"><?= $rows['FILE_NO']; ?></td>
                                        </tr>
											<?
											$cnt_total_issue_qty += $rows['ISSUE_QTY'];
											$pi_total_issue_qty += $rows['ISSUE_QTY'];
										}
										if(empty($issue_order_data_arr[$row['BTB_ID']][$prod_id]))
										{
											?>
											<td width="120" class="wrd_brk"></td>
											<td width="100" class="wrd_brk" align="right"></td>
											<td width="120" class="wrd_brk"></td>
											<td width="120" class="wrd_brk"></td>
											<td width="100" class="wrd_brk"></td>
                                        </tr>
										<?
                                    }
								}
                            }
							?>
							<tr class="tr_total">
								<td colspan="3" class="td_total">Count Sub Total</td>
								<td class="td_total"><?= number_format($cnt_total_pi_qty,2); ?></td>
								<td></td>
								<td class="td_total"><?= number_format($cnt_total_rcv_qty,2); ?></td>
								<td></td>
								<td class="td_total"><?= number_format($cnt_total_issue_qty,2); ?></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<?
                        }
						?>
						<tr class="tr_total">
							<td colspan="5" class="td_total">PI Sub Total</td>
								<td class="td_total"><?= number_format($pi_total_pi_qty,2); ?></td>
								<td></td>
								<td class="td_total"><?= number_format($pi_total_rcv_qty,2); ?></td>
								<td></td>
								<td class="td_total"><?= number_format($pi_total_issue_qty,2); ?></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?
						$grnd_total_pi_qty += $pi_total_pi_qty;
						$grnd_total_rcv_qty += $pi_total_rcv_qty;
						$grnd_total_issue_qty += $pi_total_issue_qty;
					}
				}
				?>
				</tbody>                   
	        </table>
        </div>
        <!--<table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr style="background-color: #DBDBDB;">
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100" align="right"><strong>BTB Total</strong></td>
                <td width="100" align="right"><strong><?= number_format($sub_total_rec_qty,2); ?></strong></td>
                <td width="120">&nbsp;</td>
                <td width="100" align="right"><strong><?= number_format($sub_total_issue_qty,2); ?></strong></td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>-->
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr class="tr_total">
                <td width="30">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100" class="td_total">Grand Total</td>
                <td width="100" class="td_total"><?= number_format($grnd_total_pi_qty,2); ?></td>
                <td width="100">&nbsp;</td>
                <td width="100" class="td_total"><?= number_format($grnd_total_rcv_qty,2); ?></td>
                <td width="120">&nbsp;</td>
                <td width="100" class="td_total"><?= number_format($grnd_total_issue_qty,2); ?></strong></td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>
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
	echo "$total_data####$filename";
	exit();  
}

if($action==="report_generate_3__________")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	$txt_internal_file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
	$txt_internal_file_no="";
	foreach($txt_internal_file_no_arr as $file_no)
	{
		$txt_internal_file_no.="'".$file_no."',";
	}
	$txt_internal_file_no=chop($txt_internal_file_no,",");
	
	$file_cond='';
	if ($txt_internal_file_no=="") $file_cond="";
	else $file_cond=" and a.internal_file_no in (".$txt_internal_file_no.")";

	$company_arr=return_library_array("select id,company_name from lib_company where id=$cbo_company_name",'id','company_name');
	$buyer_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array("select id,color_name from lib_color", "id", "color_name");

	$sql_file = "SELECT a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID 
	FROM com_export_lc a, com_btb_export_lc_attachment b 
	WHERE a.id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lc_year='$cbo_file_year' $file_cond
    UNION All
    SELECT a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID 
	FROM com_sales_contract a, com_btb_export_lc_attachment b 
	WHERE a.id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.sc_year='$cbo_file_year' $file_cond";
	//echo $sql_file;//die;
    $sql_file_res=sql_select($sql_file);
    $file_data_arr=array();
	foreach($sql_file_res as $row)
	{		
		$btb_IDs .= $row['BTB_ID'].',';
		$file_data_arr[$row['BTB_ID']]['LC_SC_NO']=$row['LC_SC_NO'];
		$file_data_arr[$row['BTB_ID']]['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
		$file_data_arr[$row['BTB_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
	}
	unset($sql_file_res);

	$btb_IDs=rtrim($btb_IDs,',');
	$ex_btb_ids_arr=explode(',', $btb_IDs);
	
	//Temporary Table Data Insert
	$con=connect();
	$rID=true;
	foreach ($ex_btb_ids_arr as $btb_id)
	{
		$rID=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$btb_id,157)");
	}
	if ($rID) oci_commit($con);
	//End Temporary Table Data Insert

	$sql_main = "SELECT c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.yarn_composition_item1 as YARN_COMPOSITION_ID, c.YARN_COMPOSITION_PERCENTAGE1, sum(d.order_qnty) as RECEIVE_QTY, d.PROD_ID, e.LOT, f.lc_number as BTB_NO, f.lc_date as BTB_DATE, f.SUPPLIER_ID, f.id as BTB_ID 
	FROM  tmp_poid a, com_btb_lc_pi b, com_pi_item_details c, inv_transaction d, product_details_master e, com_btb_lc_master_details f 
	WHERE a.poid=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.id=d.pi_wo_req_dtls_id and d.prod_id=e.id and a.poid=f.id and a.type=157 and a.userid=$user_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and d.item_category=1 and d.transaction_type=1 and d.receive_basis=1 and e.status_active=1 and e.is_deleted=0 and e.item_category_id=1 and f.status_active=1 and f.is_deleted=0  and f.importer_id=$cbo_company_name
	--and c.pi_id = 15829 
	--and c.pi_id = 16015
	--and c.pi_id = 17572
	--and c.pi_id = 17163
	GROUP BY c.pi_id, c.color_id, c.count_name, c.yarn_type, c.yarn_composition_item1, c.yarn_composition_percentage1, d.prod_id, e.lot, f.lc_number, f.lc_date, f.supplier_id, f.id 
	ORDER BY f.id DESC"; //and f.item_category_id=1
    //echo $sql_main;
    $sql_main_res=sql_select($sql_main);
	$pi_id_arr = array();
	$prod_id_arr = array();
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$prod_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
	}
	
	if(empty($sql_main_res))
	{
		echo get_empty_data_msg();
		die;
	}
	
	$sql_lc = "SELECT a.export_lc_no as LC_SC_NO, b.WO_PO_BREAK_DOWN_ID
	FROM com_export_lc a, COM_EXPORT_LC_ORDER_INFO b  
	WHERE a.id=b.COM_EXPORT_LC_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lc_year='$cbo_file_year'
    UNION All
    SELECT a.contract_no as LC_SC_NO, b.WO_PO_BREAK_DOWN_ID 
	FROM com_sales_contract a, COM_SALES_CONTRACT_ORDER_INFO b 
	WHERE a.id=b.COM_SALES_CONTRACT_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name
	and a.sc_year='$cbo_file_year'
	";
	//echo $sql_lc;
    $sql_lc_rslt = sql_select($sql_lc);
	$lc_data_arr = array();
	foreach($sql_lc_rslt as $row)
	{
		$lc_data_arr[$row['WO_PO_BREAK_DOWN_ID']] = $row['LC_SC_NO'];
	}
	/*echo "<pre>";
	print_r($lc_data_arr);
	echo "</pre>";*/

	//for receive return
	$sql_rcv_rtn = "SELECT b.ID, b.CONS_QUANTITY, b.PROD_ID, c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.YARN_COMPOSITION_ITEM1, c.YARN_COMPOSITION_PERCENTAGE1 FROM inv_issue_master a, inv_transaction b, com_pi_item_details c WHERE a.id = b.mst_id AND a.pi_id = c.pi_id AND a.entry_form = 8 AND a.item_category = 1 AND a.company_id = ".$cbo_company_name." AND a.status_active=1 AND a.is_deleted=0 ".where_con_using_array($pi_id_arr, '0', 'a.pi_id')." AND b.item_category = 1 AND b.transaction_type = 3 AND b.status_active=1 AND b.is_deleted=0 ".where_con_using_array($prod_id_arr, '0', 'b.prod_id')." AND c.item_category_id = 1 AND c.status_active=1 AND c.is_deleted=0";
	//echo $sql_rcv_rtn;
    $sql_rcv_rtn_rlst = sql_select($sql_rcv_rtn);
	$rcv_rtn_data_arr = array();
	foreach($sql_rcv_rtn_rlst as $row)
	{
		$rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ITEM1']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'] += $row['CONS_QUANTITY'];
	}

    $main_data_arr=array();
    $tot_rows=0;    
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$pi_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
		
		//for receive return qty
		$rcv_rtn_qty = 0;
		$rcv_rtn_qty = $rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ID']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'];
		//for receive balance qty
		$row['RECEIVE_QTY'] = $row['RECEIVE_QTY'] - $rcv_rtn_qty;
		
		$yarn_composition=$count_arr[$row['COUNT_NAME']].' '.$composition[$row['YARN_COMPOSITION_ID']].' '.$row['YARN_COMPOSITION_PERCENTAGE1'].' '.$yarn_type[$row['YARN_TYPE']].' '.$color_arr[$row['COLOR_ID']];
		
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['PI_ID'][$row['PI_ID']] = $row['PI_ID'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['BTB_ID'] = $row['BTB_ID'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['BTB_NO'] = $row['BTB_NO'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['BTB_DATE'] = $row['BTB_DATE'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['COUNT_NAME'] = $row['COUNT_NAME'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['YARN_COMPOSITION'] = $yarn_composition;
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['COLOR_ID'] = $row['COLOR_ID'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['LOT'] = $row['LOT'];
		$main_data_arr[$row['BTB_NO']][$row['PI_ID']][$row['COUNT_NAME']][$yarn_composition][$row['PROD_ID']]['RECEIVE_QTY'] += $row['RECEIVE_QTY'];
		$prod_ids .= $row['PROD_ID'].',';
		$tot_rows++;
	}
	unset($sql_main_res);
	/*echo '<pre>';
	print_r($main_data_arr);
	echo '</pre>'; die;*/

	if ($prod_ids != '')
	{
		$prod_ids = array_flip(array_flip(explode(',', rtrim($prod_ids,','))));
		$prod_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$prod_id_cond = ' and (';
			$prodIDArr = array_chunk($prod_ids,999);
			foreach($prodIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$prod_id_cond .= " d.prod_id in($ids) or ";
			}


			$prod_id_cond = rtrim($prod_id_cond,'or ');
			$prod_id_cond .= ')';
		}
		else
		{
			$prod_ids = implode(',', $prod_ids);
			$prod_id_cond=" and d.prod_id in ($prod_ids)";
		}
	}
	
	if ($btb_ids != '')
	{
		$btb_ids = array_flip(array_flip(explode(',', rtrim($btb_ids,','))));

		$btb_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$btb_id_cond = ' and (';
			$btbIDArr = array_chunk($btb_ids,999);
			foreach($btbIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$btb_id_cond .= " b.btb_lc_id in($ids) or ";
			}
			$btb_id_cond = rtrim($btb_id_cond,'or ');
			$btb_id_cond .= ')';
		}
		else
		{
			$btb_ids = implode(',', $btb_ids);
			$btb_id_cond=" and b.btb_lc_id in ($btb_ids)";
		}
	}

	// Temporary Table Data Delete
	$rID2=execute_query("delete from tmp_poid where userid=$user_id and type=157");
	if($rID2) oci_commit($con);
	disconnect($con);
	// End Temporary Table Data Delete

	$sql_issue_order="SELECT a.ID, a.ISSUE_PURPOSE, b.BTB_LC_ID, d.PROD_ID, d.quantity as ISSUE_QTY, e.ID as PO_ID, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO, e.SC_LC, f.BUYER_NAME
	FROM inv_issue_master a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e, wo_po_details_master f
	WHERE a.id=b.mst_id and b.id=d.trans_id and d.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.company_id=$cbo_company_name and a.entry_form=3 and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and d.entry_form=3 and d.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0 $prod_id_cond $btb_id_cond";
	//echo $sql_issue_order;
	$sql_issue_order_res=sql_select($sql_issue_order);
	
	//for issue return
	$issue_id_arr = array();
	$issue_btb_lc_id = array();
	foreach($sql_issue_order_res as $row)
	{
		$issue_id_arr['issue_id'][$row['ID']] = $row['ID'];
		$issue_id_arr['po_id'][$row['PO_ID']] = $row['PO_ID'];
		$issue_id_arr['prod_id'][$row['PROD_ID']] = $row['PROD_ID'];
		$issue_btb_lc_id[$row['ID']][$row['PROD_ID']]['btb_lc_id'] = $row['BTB_LC_ID'];
	}
	/*echo '<pre>';
	print_r($issue_id_arr['po_id']);
	echo '</pre>';
	die;*/
	
	$sql_issue_rtn = "SELECT a.ISSUE_ID, d.ID, d.PROD_ID, d.QUANTITY, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO FROM INV_RECEIVE_MASTER a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e WHERE a.id = b.mst_id AND b.id = d.trans_id AND d.po_breakdown_id = e.id AND a.entry_form = 9 AND a.item_category = 1 and a.receive_basis = 3 AND a.company_id = ".$cbo_company_name." AND a.status_active = 1 AND a.is_deleted = 0 AND b.item_category = 1 AND b.transaction_type = 4 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.is_deleted = 0".where_con_using_array($issue_id_arr['issue_id'], '0', 'a.issue_id').where_con_using_array($issue_id_arr['prod_id'], '0', 'd.prod_id').where_con_using_array($issue_id_arr['po_id'], '0', 'e.id');
	//echo $sql_issue_rtn;
	$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);
	$issue_rtn_data_arr = array();
	$duplicate_check = array();
	foreach($sql_issue_rtn_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])

		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$btb_lc_id = $issue_btb_lc_id[$row['ISSUE_ID']][$row['PROD_ID']]['btb_lc_id'];
			$issue_rtn_data_arr[$btb_lc_id][$row['PROD_ID']][$row['INTERNAL_REF']][$row['ISSUE_ID']]['qty'] += $row['QUANTITY'];
		}
	}	
	
    $issue_order_data_arr=array();
    $row_span_arr=array();
	foreach($sql_issue_order_res as $row)
	{
		//for receive return qty
		$issue_rtn_qty = 0;
		$issue_rtn_qty = $issue_rtn_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']][$row['ID']]['qty'];
		//for issue balance qty
		$row['ISSUE_QTY'] = $row['ISSUE_QTY'] - $issue_rtn_qty;
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['ISSUE_PURPOSE'] = $row['ISSUE_PURPOSE'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['PO_NUMBER'] = $row['PO_NUMBER'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['BUYER_NAME'] = $row['BUYER_NAME'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['INTERNAL_REF'] = $row['INTERNAL_REF'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['FILE_NO'] = $row['FILE_NO'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['ID'][] = $row['ID'];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['ISSUE_QTY'] += $row['ISSUE_QTY'];
		
		//for export lc no
		$row['LC_SC'] = $lc_data_arr[$row['PO_ID']];
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]['LC_SC'][$row['LC_SC']] = $row['LC_SC'];
		
		$row_span_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['INTERNAL_REF']]++;
	}
	unset($sql_issue_order_res);
	
	/*echo '<pre>';
	print_r($issue_order_data_arrs);
	echo '</pre>';
	die;*/
	
	$sql_pi_rslt = sql_select("SELECT A.PI_NUMBER, B.PI_ID, B.COUNT_NAME, B.YARN_COMPOSITION_ITEM1, B.YARN_COMPOSITION_PERCENTAGE1, B.YARN_TYPE, B.COLOR_ID, B.QUANTITY FROM COM_PI_MASTER_DETAILS A, COM_PI_ITEM_DETAILS B WHERE A.ID = B.PI_ID AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0".where_con_using_array($pi_id_arr, '0', 'B.PI_ID'));
	//$pi_id_arr
	$pi_dtls = array();
	$pi_data = array();
	foreach($sql_pi_rslt as $row)
	{
		$yrn_compo=$count_arr[$row['COUNT_NAME']].' '.$composition[$row['YARN_COMPOSITION_ITEM1']].' '.$row['YARN_COMPOSITION_PERCENTAGE1'].' '.$yarn_type[$row['YARN_TYPE']].' '.$color_arr[$row['COLOR_ID']];
		
		$pi_dtls[$row['PI_ID']] = $row['PI_NUMBER'];
		$pi_data[$row['PI_ID']][$row['COUNT_NAME']][$yrn_compo]['PI_QTY'] = $row['QUANTITY'];
	}
	/*echo '<pre>';
	print_r($pi_data);
	echo '</pre>';
	die;*/

	$table_width="2150";
	$div_width="2170";
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all;word-wrap: break-word;}
		.tr_total{
			background-color:#CCC;
		}
		.td_total{
			font-size:13px;
			font-weight:bold;
			text-align:right;
			padding-right:3px;
		}
	</style>
	<div style="width:100%; margin-left:10px;" align="left">
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
            	<tr>
                    <th rowspan="2" width="30">Sl</th>
                    <th rowspan="2" width="100">File No</th>
                    <th rowspan="2" width="100">Buyer</th>
                    <th rowspan="2" width="150">BTB No</th>
                    <th rowspan="2" width="80">BTB Date</th>
                    <th rowspan="2" width="120">Supplier Name</th>
                    <th rowspan="2" width="100">PI No</th>
                    <th colspan="7" width="710">Reveive Information</th>
                    <th colspan="6" width="660">Issue Information</th>
                </tr>
            	<tr>
            		<!-- Reveive Information -->
                    <th width="80">Count</th>
                    <th width="250">Composition</th>
                    <th width="80">Type</th>
                    <th width="100">Color</th>
                    <th width="100">PI Qty.(Kg)</th>
                    <th width="100">Lot</th>
                    <th width="100">Received Qty</th>

                    <!-- Issue Information -->
                    <th width="120">Buyer Name</th>
                    <th width="100">Issue Qty</th>
                    <th width="100">Issue Purpose</th>
                    <th width="120">Internal Ref</th>
                    <th width="120">LC/SC No</th>
                    <th width="100">File No</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:auto; width:<?= $div_width; ?>px;" id="scroll_body">
	        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
	        	<tbody>
	        	<?
				$r_spn = array();
				$r_spn_pi = array();

				$r_spn_cnt = array();
				$r_spn_compo = array();
				$r_spn_prd = array();
				//$r_spn_typ = array();
				//$r_spn_clr = array();
				$r_spn_iss = array();
            	foreach($main_data_arr as $k_btb_no=>$v_btb_no)
				{
					foreach($v_btb_no as $k_pi_id=>$v_pi_id)
					{
						foreach($v_pi_id as $k_count=>$v_count)
						{
							foreach($v_count as $k_compo=>$v_compo)
							{
								foreach($v_compo as $prod_id=>$row)
								{
									//for btb row span
									$r_spn[$k_btb_no]++;
									
									if(!empty($issue_order_data_arr[$row['BTB_ID']][$prod_id]))
									{
										foreach($issue_order_data_arr[$row['BTB_ID']][$prod_id] as $key=>$val)
										{
											//for pi row span
											$r_spn_pi[$k_btb_no][$k_pi_id]++;
											
											//for count row span
											$r_spn_cnt[$k_btb_no][$k_pi_id][$k_count]++;
											
											//for composition row span
											$r_spn_compo[$k_btb_no][$k_pi_id][$k_count][$k_compo]++;
											
											//for product row span
											$r_spn_prd[$k_btb_no][$k_pi_id][$k_count][$k_compo][$prod_id]++;
											
											//for issue row span
											$r_spn_iss[$row['BTB_ID']]++;
										}
									}
									else
									{
										//for pi row span
										$r_spn_pi[$k_btb_no][$k_pi_id]++;
											
										//for count row span
										$r_spn_cnt[$k_btb_no][$k_pi_id][$k_count]++;
										
										//for composition row span
										$r_spn_compo[$k_btb_no][$k_pi_id][$k_count][$k_compo]++;
											
										//for product row span
										$r_spn_prd[$k_btb_no][$k_pi_id][$k_count][$k_compo][$prod_id]++;
										
										//for issue row span
										$r_spn_iss[$row['BTB_ID']]++;
									}
								}
							}
						}
					}
				}
				
				/*echo "<pre>";
				print_r($r_spn_compo);
				echo "</pre>";
				die;*/
	        	$i=0;
            	foreach($main_data_arr as $k_btb_no=>$v_btb_no)
				{
					$btb_rspn = 1;
					foreach($v_btb_no as $k_pi_id=>$v_pi_id)
					{
						$i++;
						$pi_rspn = 1;
						$pi_total_pi_qty = 0;
						$pi_total_rcv_qty = 0;
						$pi_total_issue_qty = 0;
						foreach($v_pi_id as $k_count=>$v_count)
						{
							$cnt_rspn = 1;
							$cnt_total_pi_qty = 0;
							$cnt_total_rcv_qty = 0;
							$cnt_total_issue_qty = 0;
							foreach($v_count as $k_compo=>$v_compo)
							{
								$compo_rspn = 1;
								foreach($v_compo as $prod_id=>$row)
								{
									//issue row span
									$iss_rspn = 1;
									$r_span_issue = $r_spn_iss[$row['BTB_ID']];
									
									//for compsition row span
									$compo_row_span_cond = '';
									$compo_row_span = $r_spn_compo[$k_btb_no][$k_pi_id][$k_count][$k_compo];
									if ($compo_row_span != 0) $compo_row_span_cond='rowspan="'.$compo_row_span.'"';
									
									//for cout row span
									$cnt_row_span_cond = '';
									$cnt_row_span = count($r_spn_compo[$k_btb_no][$k_pi_id][$k_count][$k_compo])+$r_spn_cnt[$k_btb_no][$k_pi_id][$k_count];
									if ($cnt_row_span != 0) $cnt_row_span_cond='rowspan="'.$cnt_row_span.'"';
									
									//for pi row span
									$pi_row_span_cond = '';
									$pi_row_span = count($r_spn_cnt[$k_btb_no][$k_pi_id])+$r_spn_pi[$k_btb_no][$k_pi_id];
									if ($pi_row_span != 0) $pi_row_span_cond='rowspan="'.$pi_row_span.'"';
	
									//for btb row span
									$row_span_cond = '';
									$row_span = count($r_spn_cnt[$k_btb_no][$k_pi_id])+count($r_spn_pi[$k_btb_no])+$r_span_issue;
									if ($row_span != 0) $row_span_cond='rowspan="'.$row_span.'"';
									
									//for product row span
									$prd_row_span_cond='';
									$prd_row_span = $r_spn_prd[$k_btb_no][$k_pi_id][$k_count][$k_compo][$prod_id];
									if ($prd_row_span != 0) $prd_row_span_cond='rowspan="'.$prd_row_span.'"';

									//for pi qty
									$row['PI_QTY'] = $pi_data[$k_pi_id][$k_count][$k_compo]['PI_QTY'];
									?>
									<tr bgcolor="<?= $bgcolor;?>" id="tr<?= $i; ?>" valign="middle">
										<?
										if($btb_rspn == 1)
										{
											$btb_rspn++;
											?>
                                            <td width="30" <?=$row_span_cond; ?> class="wrd_brk"><?= $i; ?></td>
                                            <td width="100" <?=$row_span_cond; ?> class="wrd_brk"><?= $file_data_arr[$row['BTB_ID']]['INTERNAL_FILE_NO']; ?></td>
                                            <td width="100" <?=$row_span_cond; ?> class="wrd_brk"><?= $buyer_arr[$file_data_arr[$row['BTB_ID']]['BUYER_NAME']]; ?></td>
                                            <td width="150" <?=$row_span_cond; ?> class="wrd_brk"><?= $row['BTB_NO']; ?></td>
                                            <td width="80" <?=$row_span_cond; ?> class="wrd_brk"><?= change_date_format($row['BTB_DATE']); ?></td>
                                            <td width="120" <?=$row_span_cond; ?> class="wrd_brk"><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></td>
											<?
										}
										if($pi_rspn == 1)
										{
											$pi_rspn++;
											?>
											<td width="100" <?= $pi_row_span_cond; ?> class="wrd_brk"><?= $pi_dtls[$k_pi_id]; ?></td>        
											<?
										}
										if($cnt_rspn == 1)
										{
											$cnt_rspn++;
											?>
											<td width="80" <?= $cnt_row_span_cond; ?> class="wrd_brk"><?= $count_arr[$row['COUNT_NAME']]; ?></td>
											<?
										}
										if($compo_rspn == 1)
										{
											$compo_rspn++;
											?>

											<td width="250" <?= $compo_row_span_cond; ?> class="wrd_brk"><?= $row['YARN_COMPOSITION']; ?></td>
											<td width="80" <?= $compo_row_span_cond; ?> class="wrd_brk"><?= $yarn_type[$row['YARN_TYPE']]; ?></td>
											<td width="100" <?= $compo_row_span_cond; ?> class="wrd_brk"><?= $color_arr[$row['COLOR_ID']]; ?></td>
											<td width="100" <?= $compo_row_span_cond; ?> class="wrd_brk" align="right"><?= number_format($row['PI_QTY'],2); ?></td>
											<?
											$cnt_total_pi_qty += $row['PI_QTY'];
											$pi_total_pi_qty += $row['PI_QTY'];											
										}
										if($iss_rspn == 1)
										{
											$iss_rspn++;
											?>                                            
											<td width="100" <?= $prd_row_span_cond; ?> class="wrd_brk"><?= $row['LOT']; ?></td>
                                            <td width="100" <?= $prd_row_span_cond; ?> class="wrd_brk" align="right"><a href='##' onClick="openmypage_receive_popup('<?= $cbo_company_name; ?>','<?= implode(',',$row['PI_ID']); ?>','<?= $prod_id; ?>', '<?= $btb_ID; ?>')"><font color="blue"><b><?= number_format($row['RECEIVE_QTY'],2); ?></b></font></a></td>
											<?
											$cnt_total_rcv_qty += $row['RECEIVE_QTY'];
											$pi_total_rcv_qty += $row['RECEIVE_QTY'];
										}
										
										foreach ($issue_order_data_arr[$row['BTB_ID']][$prod_id] as $rows)
										{
											?>
											<td width="120" class="wrd_brk"><?= $buyer_arr[$rows['BUYER_NAME']]; ?></td>
											<td width="100" class="wrd_brk" align="right"><a href='##' onClick="openmypage_issue_popup('<?= $cbo_company_name; ?>','<?= implode(',', $rows['ID']); ?>','<?= $prod_id; ?>','<?= implode(', ',$rows['LC_SC']); ?>')"><font color="blue"><b><?= number_format($rows['ISSUE_QTY'],2); ?></b></font></a></td>
											<td width="100" class="wrd_brk">&nbsp;<?= $yarn_issue_purpose[$rows['ISSUE_PURPOSE']]; ?></td>
											<td width="120" class="wrd_brk"><?= $rows['INTERNAL_REF']; ?></td>
											<td width="120" class="wrd_brk"><?= implode(', ',$rows['LC_SC']); ?></td>
											<td width="100" class="wrd_brk"><?= $rows['FILE_NO']; ?></td>
                                        </tr>
											<?
											$cnt_total_issue_qty += $rows['ISSUE_QTY'];
											$pi_total_issue_qty += $rows['ISSUE_QTY'];
										}
										if(empty($issue_order_data_arr[$row['BTB_ID']][$prod_id]))
										{
											?>
											<td width="120" class="wrd_brk"></td>
											<td width="100" class="wrd_brk" align="right"></td>
											<td width="100" class="wrd_brk"></td>
											<td width="120" class="wrd_brk"></td>
											<td width="120" class="wrd_brk"></td>
											<td width="100" class="wrd_brk"></td>
                                        </tr>
										<?
                                    }
								}
                            }
							?>
							<tr class="tr_total">
								<td colspan="3" class="td_total">Count Sub Total</td>
								<td class="td_total"><?= number_format($cnt_total_pi_qty,2); ?></td>
								<td></td>
								<td class="td_total"><?= number_format($cnt_total_rcv_qty,2); ?></td>
								<td></td>
								<td class="td_total"><?= number_format($cnt_total_issue_qty,2); ?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<?
                        }
						?>
						<tr class="tr_total">
							<td colspan="5" class="td_total">PI Sub Total</td>
								<td class="td_total"><?= number_format($pi_total_pi_qty,2); ?></td>
								<td></td>
								<td class="td_total"><?= number_format($pi_total_rcv_qty,2); ?></td>
								<td></td>
								<td class="td_total"><?= number_format($pi_total_issue_qty,2); ?></td>
							<td></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<?
						$grnd_total_pi_qty += $pi_total_pi_qty;
						$grnd_total_rcv_qty += $pi_total_rcv_qty;
						$grnd_total_issue_qty += $pi_total_issue_qty;
					}
				}
				?>
				</tbody>                   
	        </table>
        </div>
        <!--<table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr style="background-color: #DBDBDB;">
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100" align="right"><strong>BTB Total</strong></td>
                <td width="100" align="right"><strong><?= number_format($sub_total_rec_qty,2); ?></strong></td>
                <td width="120">&nbsp;</td>
                <td width="100" align="right"><strong><?= number_format($sub_total_issue_qty,2); ?></strong></td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>-->
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr class="tr_total">
                <td width="30">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100" class="td_total">Grand Total</td>
                <td width="100" class="td_total"><?= number_format($grnd_total_pi_qty,2); ?></td>
                <td width="100">&nbsp;</td>
                <td width="100" class="td_total"><?= number_format($grnd_total_rcv_qty,2); ?></td>
                <td width="120">&nbsp;</td>
                <td width="100" class="td_total"><?= number_format($grnd_total_issue_qty,2); ?></strong></td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>
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
	echo "$total_data####$filename";
	exit();  
}
//end for show-3 button

if($action==="report_generate_02112021_with_order")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_file_year=str_replace("'","",$cbo_file_year);
	//$txt_internal_file_no=str_replace("'","",$txt_internal_file_no);
	$txt_internal_file_no_arr=explode(",",str_replace("'","",$txt_internal_file_no));
	$txt_internal_file_no="";
	foreach($txt_internal_file_no_arr as $file_no)
	{
		$txt_internal_file_no.="'".$file_no."',";
	}
	$txt_internal_file_no=chop($txt_internal_file_no,",");
	$file_cond='';
	if ($txt_internal_file_no=="") $file_cond=""; else $file_cond=" and a.internal_file_no in ($txt_internal_file_no) ";

	$company_arr=return_library_array("select id,company_name from lib_company where id=$cbo_company_name",'id','company_name');
	$buyer_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
	$supplier_arr=return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$count_arr=return_library_array("select id,yarn_count from lib_yarn_count",'id','yarn_count');
	$color_arr=return_library_array("select id,color_name from lib_color", "id", "color_name");

	$sql_file = "SELECT a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID FROM com_export_lc a, com_btb_export_lc_attachment b WHERE a.id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lc_year='$cbo_file_year' $file_cond
    UNION All
    SELECT a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID FROM com_sales_contract a, com_btb_export_lc_attachment b WHERE a.id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.sc_year='$cbo_file_year' $file_cond";
	//echo $sql_file;//die;
    $sql_file_res=sql_select($sql_file);
    $file_data_arr=array();
	foreach($sql_file_res as $row)
	{		
		$btb_IDs .= $row['BTB_ID'].',';
		$file_data_arr[$row['BTB_ID']]['LC_SC_NO']=$row['LC_SC_NO'];
		$file_data_arr[$row['BTB_ID']]['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
		$file_data_arr[$row['BTB_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
	}
	unset($sql_file_res);

	$btb_IDs=rtrim($btb_IDs,',');
	$ex_btb_ids_arr=explode(',', $btb_IDs);
	
	//Temporary Table Data Insert
	$con=connect();
	$rID=true;
	foreach ($ex_btb_ids_arr as $btb_id)
	{
		//echo "insert into tmp_poid (userid, poid, type) values ($user_id,$btb_id,157)";
		$rID=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$btb_id,157)");
	}
	if ($rID) oci_commit($con);
	//End Temporary Table Data Insert

	$sql_main = "SELECT c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.yarn_composition_item1 as YARN_COMPOSITION_ID, c.YARN_COMPOSITION_PERCENTAGE1, sum(d.order_qnty) as RECEIVE_QTY, d.PROD_ID, e.LOT, f.lc_number as BTB_NO, f.lc_date as BTB_DATE, f.SUPPLIER_ID, f.id as BTB_ID FROM  tmp_poid a, com_btb_lc_pi b, com_pi_item_details c, inv_transaction d, product_details_master e, com_btb_lc_master_details f WHERE a.poid=b.com_btb_lc_master_details_id and b.pi_id=c.pi_id and c.id=d.pi_wo_req_dtls_id and d.prod_id=e.id and a.poid=f.id and a.type=157 and a.userid=$user_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.item_category_id=1 and d.status_active=1 and d.is_deleted=0 and d.item_category=1 and d.transaction_type=1 and d.receive_basis=1 and e.status_active=1 and e.is_deleted=0 and e.item_category_id=1 and f.status_active=1 and f.is_deleted=0 and f.item_category_id=1 and f.importer_id=$cbo_company_name GROUP BY c.pi_id, c.color_id, c.count_name, c.yarn_type, c.yarn_composition_item1, c.yarn_composition_percentage1, d.prod_id, e.lot, f.lc_number, f.lc_date, f.supplier_id, f.id ORDER BY f.id DESC";
    //echo $sql_main;
    $sql_main_res=sql_select($sql_main);
	$pi_id_arr = array();
	$prod_id_arr = array();
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$prod_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
	}
	
	//for receive return
	$sql_rcv_rtn = "SELECT b.ID, b.CONS_QUANTITY, b.PROD_ID, c.PI_ID, c.COLOR_ID, c.COUNT_NAME, c.YARN_TYPE, c.YARN_COMPOSITION_ITEM1, c.YARN_COMPOSITION_PERCENTAGE1 FROM inv_issue_master a, inv_transaction b, com_pi_item_details c WHERE a.id = b.mst_id AND a.pi_id = c.pi_id AND a.entry_form = 8 AND a.item_category = 1 AND a.company_id = ".$cbo_company_name." AND a.status_active=1 AND a.is_deleted=0 ".where_con_using_array($pi_id_arr, '0', 'a.pi_id')." AND b.item_category = 1 AND b.transaction_type = 3 AND b.status_active=1 AND b.is_deleted=0 ".where_con_using_array($prod_id_arr, '0', 'b.prod_id')." AND c.item_category_id = 1 AND c.status_active=1 AND c.is_deleted=0";
	//echo $sql_rcv_rtn;
    $sql_rcv_rtn_rlst = sql_select($sql_rcv_rtn);
	$rcv_rtn_data_arr = array();
	foreach($sql_rcv_rtn_rlst as $row)
	{
		$rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ITEM1']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'] += $row['CONS_QUANTITY'];
	}

    $main_data_arr=array();
    $tot_rows=0;    
	foreach($sql_main_res as $row)
	{
		$pi_id_arr[$row['PI_ID']] = $row['PI_ID']; 
		$pi_id_arr[$row['PROD_ID']] = $row['PROD_ID'];
		
		//for receive return qty
		$rcv_rtn_qty = 0;
		$rcv_rtn_qty = $rcv_rtn_data_arr[$row['PROD_ID']][$row['PI_ID']][$row['COLOR_ID']][$row['COUNT_NAME']][$row['YARN_TYPE']][$row['YARN_COMPOSITION_ID']][$row['YARN_COMPOSITION_PERCENTAGE1']]['qty'];
		//for receive balance qty
		$row['RECEIVE_QTY'] = $row['RECEIVE_QTY'] - $rcv_rtn_qty;
		
		$yarn_composition=$count_arr[$row['COUNT_NAME']].' '.$composition[$row['YARN_COMPOSITION_ID']].' '.$row['YARN_COMPOSITION_PERCENTAGE1'].' '.$yarn_type[$row['YARN_TYPE']].' '.$color_arr[$row['COLOR_ID']];
		/*$main_data_arr[$row['PROD_ID']]=array(
			'PI_ID'=>$row['PI_ID'],
			'BTB_ID'=>$row['BTB_ID'],
			'BTB_NO'=>$row['BTB_NO'],
			'BTB_DATE'=>$row['BTB_DATE'],
			'SUPPLIER_ID'=>$row['SUPPLIER_ID'],						
			'COUNT_NAME'=>$row['COUNT_NAME'],
			'YARN_COMPOSITION'=>$yarn_composition,
			'YARN_TYPE'=>$row['YARN_TYPE'],
			'COLOR_ID'=>$row['COLOR_ID'],			
			'LOT'=>$row['LOT'],
			'RECEIVE_QTY'=>$row['RECEIVE_QTY']
		);*/
		
		$main_data_arr[$row['PROD_ID']]['PI_ID'] = $row['PI_ID'];
		$main_data_arr[$row['PROD_ID']]['BTB_ID'] = $row['BTB_ID'];
		$main_data_arr[$row['PROD_ID']]['BTB_NO'] = $row['BTB_NO'];
		$main_data_arr[$row['PROD_ID']]['BTB_DATE'] = $row['BTB_DATE'];
		$main_data_arr[$row['PROD_ID']]['SUPPLIER_ID'] = $row['SUPPLIER_ID'];
		$main_data_arr[$row['PROD_ID']]['COUNT_NAME'] = $row['COUNT_NAME'];
		$main_data_arr[$row['PROD_ID']]['YARN_COMPOSITION'] = $yarn_composition;
		$main_data_arr[$row['PROD_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$main_data_arr[$row['PROD_ID']]['COLOR_ID'] = $row['COLOR_ID'];
		$main_data_arr[$row['PROD_ID']]['LOT'] = $row['LOT'];
		$main_data_arr[$row['PROD_ID']]['RECEIVE_QTY'] += $row['RECEIVE_QTY'];
		$prod_ids .= $row['PROD_ID'].',';
		$tot_rows++;
	}
	unset($sql_main_res);
	//echo '<pre>';print_r($main_data_arr);

	if ($prod_ids != '')
	{
		$prod_ids = array_flip(array_flip(explode(',', rtrim($prod_ids,','))));
		$prod_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$prod_id_cond = ' and (';
			$prodIDArr = array_chunk($prod_ids,999);
			foreach($prodIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$prod_id_cond .= " d.prod_id in($ids) or ";
			}
			$prod_id_cond = rtrim($prod_id_cond,'or ');
			$prod_id_cond .= ')';
		}
		else
		{
			$prod_ids = implode(',', $prod_ids);
			$prod_id_cond=" and d.prod_id in ($prod_ids)";
		}
	}
	
	if ($btb_ids != '')
	{
		$btb_ids = array_flip(array_flip(explode(',', rtrim($btb_ids,','))));

		$btb_id_cond = '';

		if($db_type==2 && $tot_rows>1000)
		{
			$btb_id_cond = ' and (';
			$btbIDArr = array_chunk($btb_ids,999);
			foreach($btbIDArr as $ids)
			{
				$ids = implode(',',$ids);
				$btb_id_cond .= " b.btb_lc_id in($ids) or ";
			}
			$btb_id_cond = rtrim($btb_id_cond,'or ');
			$btb_id_cond .= ')';
		}
		else
		{
			$btb_ids = implode(',', $btb_ids);
			$btb_id_cond=" and b.btb_lc_id in ($btb_ids)";
		}
	}

	// Temporary Table Data Delete
	$rID2=execute_query("delete from tmp_poid where userid=$user_id and type=157");
	if($rID2) oci_commit($con);
	disconnect($con);
	// End Temporary Table Data Delete

	$sql_issue_order="SELECT a.ID, a.ISSUE_PURPOSE, b.BTB_LC_ID, d.PROD_ID, d.quantity as ISSUE_QTY, e.ID as PO_ID, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO, f.BUYER_NAME
	FROM inv_issue_master a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e, wo_po_details_master f
	WHERE a.id=b.mst_id and b.id=d.trans_id and d.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.company_id=$cbo_company_name and a.entry_form=3 and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and d.entry_form=3 and d.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0 $prod_id_cond $btb_id_cond";
	//echo $sql_issue_order;
	$sql_issue_order_res=sql_select($sql_issue_order);
	
	//for issue return
	$issue_id_arr = array();
	$issue_btb_lc_id = array();
	foreach($sql_issue_order_res as $row)
	{
		$issue_id_arr['issue_id'][$row['ID']] = $row['ID'];
		$issue_id_arr['po_id'][$row['PO_ID']] = $row['PO_ID'];
		$issue_id_arr['prod_id'][$row['PROD_ID']] = $row['PROD_ID'];
		$issue_btb_lc_id[$row['ID']][$row['PROD_ID']]['btb_lc_id'] = $row['BTB_LC_ID'];
	}
	
	$sql_issue_rtn = "SELECT a.ISSUE_ID, d.ID, d.PROD_ID, d.QUANTITY, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO FROM INV_RECEIVE_MASTER a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e WHERE a.id = b.mst_id AND b.id = d.trans_id AND d.po_breakdown_id = e.id AND a.entry_form = 9 AND a.item_category = 1 and a.receive_basis = 3 AND a.company_id = ".$cbo_company_name." AND a.status_active = 1 AND a.is_deleted = 0 AND b.item_category = 1 AND b.transaction_type = 4 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.is_deleted = 0".where_con_using_array($issue_id_arr['issue_id'], '0', 'a.issue_id').where_con_using_array($issue_id_arr['prod_id'], '0', 'd.prod_id').where_con_using_array($issue_id_arr['po_id'], '0', 'e.id');
	//echo $sql_issue_rtn;
	$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);
	$issue_rtn_data_arr = array();
	$duplicate_check = array();
	foreach($sql_issue_rtn_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$btb_lc_id = $issue_btb_lc_id[$row['ISSUE_ID']][$row['PROD_ID']]['btb_lc_id'];
			$issue_rtn_data_arr[$btb_lc_id][$row['PROD_ID']][$row['PO_NUMBER']][$row['ISSUE_ID']]['qty'] += $row['QUANTITY'];
		}
	}	
	
    $issue_order_data_arr=array();
    $row_span_arr=array();
	foreach($sql_issue_order_res as $row)
	{
		//for receive return qty
		$issue_rtn_qty = 0;
		$issue_rtn_qty = $issue_rtn_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][$row['PO_NUMBER']][$row['ID']]['qty'];
		//for issue balance qty
		$row['ISSUE_QTY'] = $row['ISSUE_QTY'] - $issue_rtn_qty;
		
		$issue_order_data_arr[$row['BTB_LC_ID']][$row['PROD_ID']][]=array(
			'ISSUE_PURPOSE'=>$row['ISSUE_PURPOSE'],
			'ISSUE_QTY'=>$row['ISSUE_QTY'],
			'PO_NUMBER'=>$row['PO_NUMBER'],
			'BUYER_NAME'=>$row['BUYER_NAME'],
			'INTERNAL_REF'=>$row['INTERNAL_REF'],
			'FILE_NO'=>$row['FILE_NO'],
			'ID'=>$row['ID']
		);
		$row_span_arr[$row['BTB_LC_ID']][$row['PROD_ID']][]++;
	}
	unset($sql_issue_order_res);
	//echo '<pre>';print_r($row_span_arr);
	//echo count($issue_order_data_arr);	

	$table_width="1940";
	$div_width="1960";
	ob_start();
	?>
	<style>
		.wrd_brk{word-break: break-all;word-wrap: break-word;}
	</style>
	<div style="width:100%; margin-left:10px;" align="left">
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <thead>
            	<tr>
                    <th rowspan="2" width="100">File No</th>
                    <th rowspan="2" width="150">BTB No</th>
                    <th rowspan="2" width="80">BTB Date</th>
                    <th rowspan="2" width="120">Supplier Name</th>
                    <th colspan="6" width="710">Reveive Information</th>
                    <th colspan="7" width="780">Issue Information</th>
                </tr>
            	<tr>
            		<!-- Reveive Information -->
                    <th width="80">Count</th>
                    <th width="250">Composition</th>
                    <th width="80">Type</th>
                    <th width="100">Color</th>
                    <th width="100">Lot</th>
                    <th width="100">Received Qty</th>

                    <!-- Issue Information -->
                    <th width="120">PO No</th>
                    <th width="120">Buyer Name</th>
                    <th width="100">Issue Qty</th>
                    <th width="100">Issue Purpose</th>
                    <th width="120">Internal Ref</th>
                    <th width="120">LC/SC No</th>
                    <th width="100">File No</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:auto; width:<?= $div_width; ?>px;" id="scroll_body">
	        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body">
	        	<tbody>
	        	<?
	        	$i=1;$m=1;//$btb_check_val_arr=array();
            	foreach($main_data_arr as $prod_id=>$row)
            	{
					$row_span_cond='';
					$row_span= count($row_span_arr[$row['BTB_ID']][$prod_id]);
					//echo $row_span;
					if ($row_span != 0) $row_span_cond='rowspan="'.$row_span.'"';
					$btb_ID=$row['BTB_ID'];

					$btbID_check_val=$row['BTB_ID'];
					if (!in_array($btbID_check_val,$btb_check_val_arr) )
					{						
						if($m != 1)
						{
							?>
				            <tr style="background-color: #DBDBDB;">
				                <td width="100">&nbsp;</td>
				                <td width="150">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="250">&nbsp;</td>
				                <td width="80">&nbsp;</td>
				                <td width="100">&nbsp;</td>
				                <td width="100" align="right"><strong>BTB Total</strong></td>
				                <td width="100" align="right"><strong><?= number_format($sub_total_rec_qty,2); ?></strong></td>
				                <td width="120">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="100" align="right"><strong><?= number_format($sub_total_issue_qty,2); ?></strong></td>
				                <td width="100">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="120">&nbsp;</td>
				                <td width="100">&nbsp;</td>
				            </tr>
							<?
						}
						$btb_check_val_arr[]=$btbID_check_val;
						$m++;
						unset($sub_total_rec_qty);
						unset($sub_total_issue_qty);
					}
    				?>
    				<tr bgcolor="<?= $bgcolor;?>" id="tr<?= $i; ?>">
			            <td width="100" <?=$row_span_cond; ?> class="wrd_brk"><?= $file_data_arr[$btb_ID]['INTERNAL_FILE_NO']; ?></td>
		                <td width="150" <?= $row_span_cond; ?> class="wrd_brk"><?= $row['BTB_NO']; ?></td>
		                <td width="80" <?= $row_span_cond; ?> class="wrd_brk"><?= change_date_format($row['BTB_DATE']); ?></td>
		                <td width="120" <?= $row_span_cond; ?> class="wrd_brk"><?= $supplier_arr[$row['SUPPLIER_ID']]; ?></td>
		                <td width="80" <?= $row_span_cond; ?> class="wrd_brk"><?= $count_arr[$row['COUNT_NAME']]; ?></td>
		                <td width="250" <?= $row_span_cond; ?> class="wrd_brk"><?= $row['YARN_COMPOSITION']; ?></td>
		                <td width="80" <?= $row_span_cond; ?> class="wrd_brk"><?= $yarn_type[$row['YARN_TYPE']]; ?></td>
		                <td width="100" <?= $row_span_cond; ?> class="wrd_brk"><?= $color_arr[$row['COLOR_ID']]; ?></td>
		                <td width="100" <?= $row_span_cond; ?> class="wrd_brk"><?= $row['LOT']; ?></td>
		                <td width="100" <?= $row_span_cond; ?> class="wrd_brk" align="right"><a href='##' onClick="openmypage_receive_popup('<?= $cbo_company_name; ?>','<?= $row['PI_ID']; ?>','<?= $prod_id; ?>', '<?= $btb_ID; ?>')"><font color="blue"><b><?= number_format($row['RECEIVE_QTY'],2); ?></b></font></a></td>
		            	<?
		            	if ($row_span != 0)
		            	{
		            		foreach ($issue_order_data_arr[$row['BTB_ID']][$prod_id] as $rows)
		            		{
		            			?>
		            			<td width="120" class="wrd_brk">&nbsp;<?= $rows['PO_NUMBER']; ?></td>
				                <td width="120" class="wrd_brk"><?= $buyer_arr[$rows['BUYER_NAME']]; ?></td>
				                <td width="100" class="wrd_brk" align="right"><a href='##' onClick="openmypage_issue_popup('<?= $cbo_company_name; ?>','<?= $rows['ID']; ?>','<?= $prod_id; ?>','<?= $file_data_arr[$btb_ID]['LC_SC_NO']; ?>')"><font color="blue"><b><?= number_format($rows['ISSUE_QTY'],2); ?></b></font></a></td>
				                <td width="100" class="wrd_brk">&nbsp;<?= $yarn_issue_purpose[$rows['ISSUE_PURPOSE']]; ?></td>
				                <td width="120" class="wrd_brk"><?= $rows['INTERNAL_REF']; ?></td>
				                <td width="120" class="wrd_brk"><?= $file_data_arr[$btb_ID]['LC_SC_NO']; ?></td>
				                <td width="100" class="wrd_brk"><?= $rows['FILE_NO']; ?></td>
				                </tr>
		            			<?
		            			$sub_total_issue_qty+=$rows['ISSUE_QTY'];
		            			$grand_total_issue_qty+=$rows['ISSUE_QTY'];
		            		}
		            	}
		            	else
		            	{
		            		?>
		            		<td width="120">&nbsp;</td>
			                <td width="120">&nbsp;</td>
			                <td width="100">&nbsp;</td>
			                <td width="100">&nbsp;</td>
			                <td width="120">&nbsp;</td>
			                <td width="120">&nbsp;</td>
			                <td width="100">&nbsp;</td>
			                </tr>
			                <?
		            	}
	                $i++;
	                $sub_total_rec_qty+=$row['RECEIVE_QTY'];	                
	                $grand_total_rec_qty+=$row['RECEIVE_QTY'];	                 
	            }
				?>
				</tbody>                   
	        </table>
        </div>
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr style="background-color: #DBDBDB;">
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100" align="right"><strong>BTB Total</strong></td>
                <td width="100" align="right"><strong><?= number_format($sub_total_rec_qty,2); ?></strong></td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100" align="right"><strong><?= number_format($sub_total_issue_qty,2); ?></strong></td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>
        <table width="<?= $table_width; ?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
            <tr style="background-color: #DBDBDB;">
                <td width="100">&nbsp;</td>
                <td width="150">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="250">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100" align="right"><strong>Grand Total</strong></td>
                <td width="100" align="right"><strong><?= number_format($grand_total_rec_qty,2); ?></strong></td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100" align="right"><strong><?= number_format($grand_total_issue_qty,2); ?></strong></td>
                <td width="100">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="100">&nbsp;</td>
            </tr>
        </table>
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
	echo "$total_data####$filename";
	exit();  
}

//for receive popup
if ($action==='receive_qty_popup')
{
	echo load_html_head_contents("Receive Info Detais", "../../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
	?>
	<script>
		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('rpt_container').innerHTML+'</body</html>');
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="380px";
		}	
	</script>	
	<?php
	$buyer_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');
	$sql_file = "SELECT a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID FROM com_export_lc a, com_btb_export_lc_attachment b WHERE a.id=b.lc_sc_id and b.is_lc_sc=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=".$company_id." and b.import_mst_id=".$btb_id."
    UNION All
    SELECT a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.BUYER_NAME, b.import_mst_id as BTB_ID FROM com_sales_contract a, com_btb_export_lc_attachment b WHERE a.id=b.lc_sc_id and b.is_lc_sc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.beneficiary_name=".$company_id." and b.import_mst_id=".$btb_id;
    $sql_file_res=sql_select($sql_file);
    $file_data_arr=array();
	foreach($sql_file_res as $row)
	{		
		$file_data_arr['LC_SC_NO']=$row['LC_SC_NO'];
		$file_data_arr['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
		$file_data_arr['BUYER_NAME']=$buyer_arr[$row['BUYER_NAME']];
	}
	unset($sql_file_res);	
	
    $sql_receive="SELECT a.RECV_NUMBER, a.RECEIVE_DATE, b.order_qnty as RECEIVE_QTY from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.item_category=1 and a.receive_basis=1 and a.entry_form=1 and a.company_id=".$company_id." and a.booking_id in(".$pi_id.") and a.status_active=1 and a.is_deleted=0 and b.item_category=1 and b.transaction_type=1 and b.receive_basis=1 and b.pi_wo_batch_no in(".$pi_id.") and b.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0";
    $sql_receive_res=sql_select($sql_receive);
	
	//for receive return
	$sql_rcv_rtn = "SELECT a.ISSUE_NUMBER, a.ISSUE_DATE, b.ID, b.CONS_QUANTITY, b.PROD_ID FROM inv_issue_master a, inv_transaction b WHERE a.id = b.mst_id AND a.entry_form = 8 AND a.item_category = 1 AND a.company_id = ".$company_id." AND a.status_active=1 AND a.is_deleted=0 AND a.pi_id  in(".$pi_id.") AND b.item_category = 1 AND b.transaction_type = 3 AND b.status_active=1 AND b.is_deleted=0 AND b.prod_id = ".$prod_id;
	//echo $sql_rcv_rtn;
    $sql_rcv_rtn_rlst = sql_select($sql_rcv_rtn);
    ?>
    </head>
    <body>
        <p id="btn_container" style="margin-left:10px; margin-top:10px; margin-bottom:10px;"></p>
        <?php ob_start(); ?>
        <div align="center" style="width:420px;" id="rpt_container">
            <div style="width:420px; margin-left: 5px;" id="id_fieldset">
                <table cellpadding="0" cellspacing="0" width="400" class="rpt_table" border="1" rules="all" align="left">
                	<thead>
                        <tr>
                        	<th colspan="4" style="text-align:left;">File: <?php echo $file_data_arr['INTERNAL_FILE_NO'].'; Buyer: '.$file_data_arr['BUYER_NAME'].'; LC/SC No: '.$file_data_arr['LC_SC_NO']; ?></th>
                        </tr>
                    	<tr>
                        <th width="50">Sl</th>
                        <th width="150">MRR No</th>
                        <th width="100">MRR Date</th>
                        <th width="100">MRR Qty</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:420px; max-height:280px; overflow-y:scroll;" id="scroll_body" >
                	<table cellpadding="0" cellspacing="0" width="400" class="rpt_table" id="table_body" border="1" rules="all">
                        <tbody>
                        	<tr bgcolor="#E9F3FF">
                            	<td colspan="4" style="font-size:14px;"><strong>Receive</strong></td>
                            </tr>
                        	<?
							$i=1;
							$total_receive_qty = 0;
							$total_receive_return_qty = 0;
							$balance_qty = 0;
                            foreach($sql_receive_res as $row)
                            {
                            	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                            	?>
	                            <tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
	                                <td width="50" align="center"><?= $i; ?></td>
	                                <td width="150"><?= $row['RECV_NUMBER']; ?></td>
	                                <td width="100"><?= change_date_format($row['RECEIVE_DATE']); ?></td>
	                                <td width="100" align="right"><?= number_format($row['RECEIVE_QTY'],2); ?></td>
	                            </tr>
                            	<?
                                $i++;
								$total_receive_qty += $row['RECEIVE_QTY'];
                            }
							
							if(!empty($sql_rcv_rtn_rlst))
							{
								?>
								<tr bgcolor="#E9F3FF">
									<td colspan="4" style="font-size:14px;"><strong>Receive Return</strong></td>
								</tr>
								<?php
								$i=1;
								foreach($sql_rcv_rtn_rlst as $row)
								{
									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
									?>
									<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
										<td width="50" align="center"><?= $i; ?></td>
										<td width="150"><?= $row['ISSUE_NUMBER']; ?></td>
										<td width="100"><?= change_date_format($row['ISSUE_DATE']); ?></td>
										<td width="100" align="right"><?= number_format($row['CONS_QUANTITY'],2); ?></td>
									</tr>
									<?
									$i++;
									$total_receive_return_qty += $row['CONS_QUANTITY'];
								}
							}
							$balance_qty = number_format($total_receive_qty,2,'.','')- number_format($total_receive_return_qty,2,'.','');
							?>
                        </tbody>
                        <tfoot>
                        	<tr>
                            	<th colspan="3">Balance</th>
                            	<th><?= number_format($balance_qty,2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </body>
    <?  	
	$html=ob_get_contents();
	ob_flush();
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	ob_end_clean();
	?>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
    $(document).ready(function(e){
        document.getElementById('btn_container').innerHTML='<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>&nbsp;&nbsp;<a href="<? echo $filename; ?>" style="text-decoration:none"><input type="button" value="Excel Preview" name="excel" id="excel" class="formbutton" style="width:100px"/></a>';
    });	
    </script>
    </html>
	<?
	exit();
}
//end for receive popup

//for issue popup
if ($action==='issue_qty_popup')
{
	echo load_html_head_contents("Receive Info Detais", "../../../../", 1, 1, $unicode,'','');
    extract($_REQUEST);
	$buyer_arr=return_library_array("select id,short_name from lib_buyer",'id','short_name');

	$sql_issue_order="SELECT a.ID, a.ISSUE_NUMBER, a.ISSUE_PURPOSE, b.btb_lc_id, d.PROD_ID, d.quantity as ISSUE_QTY, e.ID as PO_ID, e.PO_NUMBER, e.grouping as INTERNAL_REF, e.FILE_NO, f.BUYER_NAME FROM inv_issue_master a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e, wo_po_details_master f WHERE a.id=b.mst_id and b.id=d.trans_id and d.po_breakdown_id=e.id and e.job_no_mst=f.job_no and a.company_id=".$company_id." and a.entry_form=3 and a.item_category=1 and b.item_category=1 and b.transaction_type=2 and d.entry_form=3 and d.trans_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.is_deleted=0 and f.is_deleted=0 and d.prod_id = ".$prod_id." and a.id in(".$issue_id.")";
	//echo $sql_issue_order;
	$sql_issue_order_res=sql_select($sql_issue_order);
	
	$sql_issue_rtn = "SELECT a.ISSUE_ID, a.RECV_NUMBER, a.RECEIVE_PURPOSE, d.ID, d.PROD_ID, d.QUANTITY AS ISSUE_QTY, e.PO_NUMBER, e.grouping AS INTERNAL_REF, e.FILE_NO, f.BUYER_NAME FROM INV_RECEIVE_MASTER a, inv_transaction b, order_wise_pro_details d, wo_po_break_down e, wo_po_details_master f WHERE a.id = b.mst_id AND b.id = d.trans_id AND d.po_breakdown_id = e.id AND e.job_no_mst=f.job_no AND a.entry_form = 9 AND a.item_category = 1 and a.receive_basis = 3 AND a.company_id = ".$company_id." AND a.status_active = 1 AND a.is_deleted = 0 AND b.item_category = 1 AND b.transaction_type = 4 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 AND e.is_deleted = 0 AND f.is_deleted=0 AND a.issue_id in(".$issue_id.") AND d.prod_id = ".$prod_id;
	//echo $sql_issue_rtn;
	$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);	
    ?>
    </head>
    <body>
        <div align="center" style="width:820px;">
            <fieldset style="width:920px; margin-left: 5px;">
                <table cellpadding="0" cellspacing="0" width="920" class="rpt_table" border="1" rules="all" align="left">
                	<thead>
                        <th width="120">PO No</th>
                        <th width="120">Buyer Name</th>
                        <th width="120">Issue No</th>
                        <th width="100">Issue Qty</th>
                        <th width="100">Issue Purpose</th>
                        <th width="120">Internal Ref</th>
                        <th width="120">LC/SC No</th>
                        <th>File No</th>
                    </thead>
                </table>
                <div style="width:920px; max-height:280px; overflow-y:scroll;" >
                	<table cellpadding="0" cellspacing="0" width="900" class="rpt_table" id="table_body" border="1" rules="all">
                        <tbody>
                        	<tr bgcolor="#E9F3FF">
                            	<td colspan="7" style="font-size:14px;"><strong>Issue</strong></td>
                            </tr>
                        	<?
							$i=1;
							$total_issue_qty = 0;
							$total_issue_return_qty = 0;
							$balance_qty = 0;
                            foreach($sql_issue_order_res as $row)
                            {
                            	$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                            	?>
	                            <tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
	                                <td width="120"><?= $row['PO_NUMBER']; ?></td>
	                                <td width="120"><?= $buyer_arr[$row['BUYER_NAME']]; ?></td>
	                                <td width="120"><?= $row['ISSUE_NUMBER']; ?></td>
	                                <td width="100" align="right"><?= number_format($row['ISSUE_QTY'],2); ?></td>
	                                <td width="100">&nbsp;<?= $yarn_issue_purpose[$row['ISSUE_PURPOSE']]; ?></td>
                                    <td width="120"><?= $row['INTERNAL_REF']; ?></td>
                                    <td width="120"><?= $lc_no; ?></td>
                                    <td><?= $row['FILE_NO']; ?></td>
	                            </tr>
                            	<?
                                $i++;
								$total_issue_qty += $row['ISSUE_QTY'];
                            }
							
							if(!empty($sql_issue_rtn_rslt))
							{
								?>
								<tr bgcolor="#E9F3FF">
									<td colspan="8" style="font-size:14px;"><strong>Issue Return</strong></td>
								</tr>
								<?php
								$i=1;
								foreach($sql_issue_rtn_rslt as $row)
								{
									$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
									?>
									<tr bgcolor="<?= $bgcolor; ?>" style="cursor:pointer;" id="tr_<?= $i;?>" onClick="change_color('tr_<?= $i; ?>','<?= $bgcolor; ?>')">
										<td width="120"><?= $row['PO_NUMBER']; ?></td>
										<td width="120"><?= $buyer_arr[$row['BUYER_NAME']]; ?></td>
										<td width="120"><?= $row['RECV_NUMBER']; ?></td>
										<td width="100" align="right"><?= number_format($row['ISSUE_QTY'],2); ?></td>
										<td width="100">&nbsp;<?= $yarn_issue_purpose[$row['ISSUE_PURPOSE']]; ?></td>
										<td width="120"><?= $row['INTERNAL_REF']; ?></td>
										<td width="120"><?= $lc_no; ?></td>
										<td><?= $row['FILE_NO']; ?></td>
									</tr>
									<?
									$i++;
									$total_issue_return_qty += $row['ISSUE_QTY'];
								}
							}
							$balance_qty = number_format($total_issue_qty,2,'.','')- number_format($total_issue_return_qty,2,'.','');
							?>
                        </tbody>
                        <tfoot>
                        	<tr>
                            	<th colspan="3">Balance</th>
                            	<th><?= number_format($balance_qty,2); ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </fieldset>
        </div>
    </body>
    <script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
	<?
	exit();
}
//end for issue popup
?>