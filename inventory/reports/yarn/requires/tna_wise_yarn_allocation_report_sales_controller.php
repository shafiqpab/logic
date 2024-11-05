<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "")
{
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

//--------------------------------------------------------------------------------------------

//load drop down supplier
if ($action == "load_drop_down_supplier")
{
	if($data){$companyCon=" and a.tag_company='$data'";}
	else{$companyCon="";}
	echo create_drop_down("cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id $companyCon and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 0, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "eval_multi_select")
{
	echo "set_multiselect('cbo_supplier','0','0','','0');\n";
	exit();
}

if($action == "composition_popup")
{
	echo load_html_head_contents("Composition Info","../../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>
	var selected_id = new Array(); var selected_name = new Array();

	function check_all_data()
	{
		var tbl_row_count = document.getElementById( 'table_body' ).rows.length;

		tbl_row_count = tbl_row_count-1;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			js_set_value( i );
		}
	}

	function toggle( x, origColor ) 
	{
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}

	function set_all()
	{
		var old=document.getElementById('txt_pre_composition_row_id').value;
		if(old!="")
		{
			old=old.split(",");
			for(var k=0; k<old.length; k++)
			{
				js_set_value( old[k] )
			}
		}
	}

	function js_set_value( str )
	{

		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_individual_id' + str).val() );
			selected_name.push( $('#txt_individual' + str).val() );

		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
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

		$('#hidden_composition_id').val(id);
		$('#hidden_composition').val(name);
	}
	</script>
	</head>
	<fieldset style="width:390px">
		<legend>Yarn Receive Details</legend>
		<input type="hidden" name="hidden_composition" id="hidden_composition" value="">
		<input type="hidden" name="hidden_composition_id" id="hidden_composition_id" value="">
		<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<tr>
					<th colspan="2">
						<? echo create_drop_down( "cbo_string_search_type", 150, $string_search_type,'', 1, "-- Searching Type --" ); ?>
					</th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="">Composition Name</th>
				</tr>
			</thead>
		</table>
		<div style="width:390px; overflow-y:scroll; max-height:300px" id="scroll_body">
			<table width="370" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
		<?
		$i = 1;

		$result=sql_select("select id,composition_name from  lib_composition_array where status_active=1 and is_deleted=0 order by composition_name");
		$pre_composition_id_arr=explode(",",$pre_composition_id);
		foreach ($result as $row)
		{
			if ($i % 2 == 0)
				$bgcolor = "#E9F3FF";
			else
				$bgcolor = "#FFFFFF";


			if(in_array($row[csf("id")],$pre_composition_id_arr))
			{
				if($pre_composition_ids=="") $pre_composition_ids=$i; else $pre_composition_ids.=",".$i;
			}
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="js_set_value(<? echo $i; ?>)" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
				<td width="50">
					<? echo $i; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $row[csf("id")]; ?>"/>
					<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $row[csf("composition_name")]; ?>"/>
				</td>
				<td width=""><p><? echo $row[csf("composition_name")]; ?></p></td>
			</tr>
			<?
			$i++;
		}
		?>
		<input type="hidden" name="txt_pre_composition_row_id" id="txt_pre_composition_row_id" value="<?php echo $pre_composition_ids; ?>"/>
		</table>
		</div>
		<table width="390" cellspacing="0" cellpadding="0" style="border:none" align="center">
			<tr>
				<td align="center" height="30" valign="bottom">
					<div style="width:100%">
						<div style="width:50%; float:left" align="left">
							<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
						</div>
						<div style="width:50%; float:left" align="left">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
						</div>
					</div>
				</td>
			</tr>
		</table>
	</fieldset>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
		set_all();
	</script>
	<?
}

//for show btn
if ($action == "generate_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$from_date = change_date_format($from_date, '', '', 1);
	$to_date = change_date_format($to_date, '', '', 1);
	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);
	ob_start();
	
	//for company
	$companyArr[0] = "All Company";
	$company_cond_mrr = '';
	if ($cbo_company_name != 0)
	{
		$company_cond_mrr = " and b.company_id=".$cbo_company_name;
	}

	$search_cond = "";
	$search_cond2 = "";
	//for yarn type
	if ($cbo_yarn_type == 0)
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_type in (".$cbo_yarn_type.")";
	
	//for yarn count
	if ($txt_count == "")
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_count_id in(".$txt_count.")";
	
	//for lot
	if ($txt_lot_no == "")
		$search_cond .= "";
	else
		$search_cond .= " and trim(b.lot)='" . trim($txt_lot_no) . "'";

	//for supplier
	if ($cbo_supplier == 0)
		$search_cond .= "";
	else
		$search_cond .= "  and b.supplier_id in(".$cbo_supplier.")";
		
	//for composition
	if ($txt_composition == "")
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_comp_type1st in (".$txt_composition_id.")";
		
	//for fso no
	if($txt_job_no != '')
	{
		$search_cond2 .= " AND C.JOB_NO LIKE '%".$txt_job_no."'";
	}
	
	//for booking no
	if($txt_booking_no != '')
	{
		$search_cond2 .= " AND C.SALES_BOOKING_NO LIKE '%".$txt_booking_no."'";
	}

	//for date condition
	if($from_date != '' && $to_date != '')
	{
		$search_cond2 .= " AND C.TASK_START_DATE BETWEEN '".$from_date."' AND '".$to_date."'";
	}

	if ($cbo_company_name == 0)
	{
		$company_cond = "";
	}
	else
	{
		$company_cond = " and a.company_id=".$cbo_company_name;
		$search_cond2 .= " AND D.COMPANY_ID=".$cbo_company_name;
	}

	if ($to_date != "")
		$mrr_date_cond = " and b.transaction_date<='".$to_date."'";

	$fso_arr = array();
	$prd_arr = array();
	$row_span = array();

	//for TNA information
	$sql_tna = "SELECT C.JOB_NO, C.TASK_START_DATE, C.TASK_FINISH_DATE FROM TNA_PROCESS_MST C, FABRIC_SALES_ORDER_MST D WHERE C.JOB_NO = D.JOB_NO AND C.TASK_NUMBER = 48 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0
	".$search_cond2." GROUP BY C.JOB_NO, C.TASK_START_DATE, C.TASK_FINISH_DATE ORDER BY C.JOB_NO";
	//echo $sql_tna;
	$sql_tna_rslt = sql_select($sql_tna);
	$tna_data = array();
	foreach($sql_tna_rslt as $row)
	{
		$fso_arr[$row['JOB_NO']] = $row['JOB_NO'];
		$tna_data[$row['JOB_NO']]['TASK_START_DATE'] = $row['TASK_START_DATE'];
		$tna_data[$row['JOB_NO']]['TASK_FINISH_DATE'] = $row['TASK_FINISH_DATE'];
	}

	//for fso no
	$con = connect();
	execute_query("DELETE FROM TMP_JOB_NO WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_ISSUE_ID WHERE USER_ID = ".$user_id);
	oci_commit($con);
	
	foreach($fso_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_JOB_NO(JOB_NO, USERID) VALUES('".$val."', '".$user_id."')");
	}
	
	//for allocation information
	$sql_allocation = "SELECT A.ITEM_ID, A.JOB_NO, A.PO_BREAK_DOWN_ID, A.BOOKING_NO, SUM(A.QNTY) AS ALLOCATE_QTY, A.INSERT_DATE, A.ALLOCATION_DATE, A.IS_SALES, B.COMPANY_ID, B.ID, B.SUPPLIER_ID, B.YARN_COUNT_ID, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_COMP_TYPE2ND, B.YARN_COMP_PERCENT2ND, B.YARN_TYPE, B.COLOR, B.LOT, B.ALLOCATED_QNTY, B.AVAILABLE_QNTY, B.AVG_RATE_PER_UNIT, B.DYED_TYPE AS IS_DYIED_YARN, B.IS_WITHIN_GROUP FROM INV_MATERIAL_ALLOCATION_DTLS A, PRODUCT_DETAILS_MASTER B, TMP_JOB_NO C WHERE A.ITEM_ID=B.ID AND A.JOB_NO = C.JOB_NO AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.QNTY>0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.USERID = ".$user_id.$search_cond.$company_cond_mrr." GROUP BY A.JOB_NO, A.PO_BREAK_DOWN_ID, A.BOOKING_NO, A.INSERT_DATE, A.ALLOCATION_DATE, A.IS_SALES, B.COMPANY_ID, B.ID, A.ITEM_ID, B.LOT, B.SUPPLIER_ID, B.YARN_COUNT_ID, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_COMP_TYPE2ND, B.YARN_COMP_PERCENT2ND, B.YARN_TYPE, B.COLOR, B.ALLOCATED_QNTY, B.AVAILABLE_QNTY, B.AVG_RATE_PER_UNIT, B.DYED_TYPE, B.IS_WITHIN_GROUP ORDER BY A.JOB_NO";
	$sql_allocation_rslt = sql_select($sql_allocation);
	$allocation_data = array();
	foreach($sql_allocation_rslt as $row)
	{
		$prd_arr[$row['ITEM_ID']] = $row['ITEM_ID'];
		$row_span[$row['JOB_NO']]++;
		
		$ageOfDays = datediff("d", $row['INSERT_DATE'], date("Y-m-d"));
		$row[csf("allocate_qty")] = $qty_arr[$row['JOB_NO']][$row['ITEM_ID']]['qty'];

		//for composition
		$compositionDetails = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%\n";
		if ($row['YARN_COMP_TYPE2ND'] != 0)
		{
			$compositionDetails .= $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		//end for composition
		
		//for supplier
		if($row['IS_WITHIN_GROUP'] == 1)
		{
			$supplier = $companyArr[$row['SUPPLIER_ID']];
		}
		else
		{
			$supplier = $supplierArr[$row['SUPPLIER_ID']];							
		}
		//end for supplier
		
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['COMPANY_ID'] = $row['COMPANY_ID'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['AGE_OF_DAYS'] = $ageOfDays;
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['COMPOSITION'] = $compositionDetails;
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['SUPPLIER'] = $supplier;
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['COLOR'] = $row['COLOR'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['LOT'] = $row['LOT'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['ALLOCATION_DATE'] = $row['ALLOCATION_DATE'];
	}
	/*echo "<pre>";
	print_r($allocation_data);
	echo "<pre>";*/
	
	//for product
	foreach($prd_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_PROD_ID(PROD_ID, USERID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);
	
	//for fso info
	$sql_fso = "SELECT A.ID, A.COMPANY_ID,  A.JOB_NO, A.SALES_BOOKING_NO, A.BUYER_ID, A.CUSTOMER_BUYER, A.BOOKING_DATE, A.DELIVERY_DATE, A.DELIVERY_START_DATE, B.FINISH_QTY, B.PP_QNTY, B.MTL_QNTY, B.FPT_QNTY, B.GPT_QNTY,(SELECT SUM(D.CONS_QTY) FROM FABRIC_SALES_ORDER_YARN_DTLS D WHERE A.ID = D.MST_ID AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 GROUP BY D.MST_ID) AS REQ_QTY FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B, TMP_JOB_NO C WHERE A.ID = B.MST_ID AND A.JOB_NO = B.JOB_NO_MST AND A.JOB_NO = C.jOB_NO AND B.JOB_NO_MST = C.jOB_NO AND C.USERID = ".$user_id." AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0";
	//echo $sql_fso;
	$sql_fso_rslt = sql_select($sql_fso);
	$fso_data = array();
	foreach($sql_fso_rslt as $row)
	{
		$fso_data[$row['JOB_NO']]['ID'] = $row['ID'];
		$fso_data[$row['JOB_NO']]['COMPANY_ID'] = $row['COMPANY_ID'];
		$fso_data[$row['JOB_NO']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
		$fso_data[$row['JOB_NO']]['BUYER_ID'] = $buy_name_arr[$row['BUYER_ID']];
		$fso_data[$row['JOB_NO']]['CUSTOMER_BUYER'] = $buy_name_arr[$row['CUSTOMER_BUYER']];
		$fso_data[$row['JOB_NO']]['BOOKING_DATE'] = $row['BOOKING_DATE'];
		$fso_data[$row['JOB_NO']]['DELIVERY_START_DATE'] = $row['DELIVERY_START_DATE'];
		$fso_data[$row['JOB_NO']]['DELIVERY_END_DATE'] = $row['DELIVERY_DATE'];
		$fso_data[$row['JOB_NO']]['BOOKING_QTY'] += ($row['FINISH_QTY']+$row['PP_QNTY']+$row['MTL_QNTY']+$row['FPT_QNTY']+$row['GPT_QNTY']);
		$fso_data[$row['JOB_NO']]['REQ_QTY'] = $row['REQ_QTY'];
	}
	unset($sql_fso_rslt);
	
	//for issue qty
	$sql_issue = "SELECT A.ID AS ISSUE_ID, B.PROD_ID, B.JOB_NO, C.ID, C.PO_BREAKDOWN_ID, C.QUANTITY FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C, TMP_PROD_ID D WHERE A.ID = B.MST_ID AND B.ID = C.TRANS_ID AND B.PROD_ID = C.PROD_ID AND B.PROD_ID = D.PROD_ID AND C.PROD_ID = D.PROD_ID AND A.ENTRY_FORM = 3 AND A.ISSUE_BASIS IN(1, 3, 8) AND A.ISSUE_PURPOSE IN(1, 2, 7, 12, 15, 38, 46, 50, 51, 63) AND B.TRANSACTION_TYPE=2 AND B.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.USERID = ".$user_id."";
	//echo $sql_issue;
	$sql_issue_rslt = sql_select($sql_issue);
	$check_trans_id = array();
	$issue_id_arr = array();
	$issue_job_arr = array();
	foreach($sql_issue_rslt as $row)
	{
		if($check_trans_id[$row['ID']] != $row['ID'])
		{
			$check_trans_id[$row['ID']] = $row['ID'];
			$issue_id_arr[$row['ISSUE_ID']] = $row['ISSUE_ID'];
			$issue_data[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]['issue_qty'] += $row['QUANTITY'];
			$issue_job_arr[$row['ISSUE_ID']][$row['PROD_ID']]['job_no'] = $row['JOB_NO'];
		}
	}
	unset($sql_issue_rslt);
	/*echo "<pre>";
	print_r($issue_data);
	echo "</pre>";*/
	
	//for issue
	foreach($issue_id_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_ISSUE_ID(ISSUE_ID, USER_ID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);
	
	//for issue return qty
	$sql_issue_rtn="SELECT A.ISSUE_ID, B.PROD_ID, C.ID, C.PO_BREAKDOWN_ID, C.QUANTITY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C, TMP_ISSUE_ID D WHERE  A.ID=B.MST_ID AND A.ISSUE_ID = D.ISSUE_ID AND B.ISSUE_ID = D.ISSUE_ID AND B.ID = C.TRANS_ID AND B.PROD_ID = C.PROD_ID AND D.USER_ID = ".$user_id." AND A.ITEM_CATEGORY = 1 AND A.ENTRY_FORM=9 AND B.TRANSACTION_TYPE=4 AND B.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0";
	// AND C.ISSUE_PURPOSE IN(1, 2, 7, 12, 15, 38, 46, 50, 51, 63)
	//echo $sql_issue_rtn;
	$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);
	$issue_rtn_data = array();
	$duplicate_check = array();
	foreach($sql_issue_rtn_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$issue_rtn_data[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]['issue_rtn_qty'] += $row['QUANTITY'];
		}
	}
	unset($sql_issue_rtn_rslt);
	//echo "<pre>";
	//print_r($reqNoArr); die;
	
	//for allocation qty
	$sql_allo = "SELECT A.ID, A.ITEM_ID, A.JOB_NO, A.QNTY, A.INSERT_DATE, A.ALLOCATION_DATE, A.IS_SALES FROM INV_MATERIAL_ALLOCATION_DTLS A, TMP_JOB_NO B, TMP_PROD_ID C WHERE A.JOB_NO = B.JOB_NO AND A.ITEM_ID = C.PROD_ID AND B.USERID = ".$user_id." AND C.USERID = ".$user_id." AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.IS_SALES = 1 AND A.QNTY>0 ORDER BY A.JOB_NO";
	//echo $sql_allo;
	$sql_allo_rslt = sql_select($sql_allo);
	$qty_arr = array();
	foreach($sql_allo_rslt as $row)
	{
		$qty_arr[$row['JOB_NO']][$row['ITEM_ID']]['qty'] += $row['QNTY'];
	}
	$tbl_width = 2200;
	?>
	<style type="text/css">
	</style>
	<div>
    	<?
        if(empty($tna_data))
		{
			echo get_empty_data_msg();
			die;
        }
        ?>
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all" id="table_header_1">
			<thead> 
				<tr class="form_caption" style="border:none;">
					<td colspan="26" align="center" style="border:none;font-size:16px; font-weight:bold">TNA Wise Yarn Allocation Report Sales</td>
				</tr>
				<tr style="border:none;">
					<td colspan="26" align="center" style="border:none; font-size:14px;">
						Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="26" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
					</td>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="120">Company</th>
					<th width="120">Sales Order No</th>
					<th width="80">Booking No.</th>
					<th width="70">Booking Date</th>
					<th width="120">Customer</th>
					<th width="120">Cust. Buyer</th>
					<th width="70">Delivery Start Date</th>
					<th width="70">Delivery End Date</th>
					<th width="70">Yarn Delivery Start Date</th>
					<th width="70">Yarn Delivery End Date</th>
					<th width="80">Fabric Booking Qty</th>
					<th width="80">Req. Yarn Qty</th>
					<th width="70">Product ID</th>
					<th width="60">Count</th>
					<th width="120">Composition</th>
					<th width="100">Type</th>
					<th width="100">Color</th>
					<th width="80">Lot</th>
					<th width="120">Supplier</th>
					<th width="70">Allocaiton Date</th>
					<th width="80">Allocated Qty</th>
					<th width="80">Issue Qty</th>
					<th width="80">Issue Rtn Qty</th>
					<th width="80">Balance</th>
					<th width="60">Age Up To All/Date</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width+18; ?>px; overflow-y:scroll; max-height:250px" id="scroll_body">  
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" style="font:'Arial Narrow';" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
				$i = 0;
				$balance = 0;
				$grand_total_allocate_qty = 0;
				$grand_total_issue_qty = 0;
				$grand_total_issue_rtn_qty = 0;
				$grand_total_balance = 0;
				$prodStock = array();
				foreach ($tna_data as $job_no=>$row) 
				{
					/*if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";*/
					$bgcolor = "#FFFFFF";
			
					//for fso information	
					$company = $companyArr[$fso_data[$job_no]['COMPANY_ID']];	
					$booking_no = $fso_data[$job_no]['SALES_BOOKING_NO'];	
					$customer = $fso_data[$job_no]['BUYER_ID'];	
					$cust_buyer = $fso_data[$job_no]['CUSTOMER_BUYER'];
					$booking_date = $fso_data[$job_no]['BOOKING_DATE'];
					$dlv_start_date = $fso_data[$job_no]['DELIVERY_START_DATE'];
					$dlv_end_date = $fso_data[$job_no]['DELIVERY_END_DATE'];
					$booking_qty = $fso_data[$job_no]['BOOKING_QTY'];
					$req_qty = $fso_data[$job_no]['REQ_QTY'];
					$fso_id = $fso_data[$job_no]['ID'];
					
					$sub_total_allocated_qty = 0;
					$sub_total_issue_qty = 0;
					$sub_total_issue_rtn_qty = 0;
					$sub_total_balance_qty = 0;
					
					if(empty($allocation_data[$job_no]))
					{
						$rspn = 1;
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="middle">
                            <td width="30" align="center"><? echo $i; ?></td>
                            <td width="120" align="center"><p><? echo $company; ?>&nbsp;</p></td>
                            <td width="120">
                                <div style="word-wrap:break-word;text-align: center;">
                                    <? echo $job_no; ?>
                                </div> 
                            </td>
                            <td width="80"><p><? echo $booking_no; ?></p></td>
                            <td width="70" align="center"><? echo change_date_format($booking_date);?></td>
                            <td width="120" align="center"><p><? echo $customer; ?></p></td>
                            <td width="120" align="center"><p><? echo $cust_buyer; ?></p></td>
                            <td width="70" align="center"><? echo change_date_format($dlv_start_date);?></td>
                            <td width="70" align="center"><? echo change_date_format($dlv_end_date);?></td>
                            <td width="70" align="center"><? echo change_date_format($row['TASK_START_DATE']);?></td>
                            <td width="70" align="center"><? echo change_date_format($row['TASK_FINISH_DATE']);?></td>
                            <td width="80" align="right"><p><? echo decimal_format($booking_qty, '1', ','); ?></p></td>
                            <td width="80" align="right"><p><? echo decimal_format($req_qty, '1', ','); ?></p></td>
                            <td width="70"></td>
                            <td width="60"></td>
                            <td width="120"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="80"></td>
                            <td width="120"></td>
                            <td width="70"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="80"></td>
                            <td width="60"></td>
						</tr>
						<?							
					}
					else
					{
						foreach($allocation_data[$job_no] as $prod_id=>$vprod)
						{
							//for allocation qty
							$allocated_qty = $qty_arr[$job_no][$prod_id]['qty'];
							
							//for issue information
							$issue_qty = $issue_data[$fso_id][$prod_id]['issue_qty'];
							$issue_rtn_qty = $issue_rtn_data[$fso_id][$prod_id]['issue_rtn_qty'];
							$balance = (decimal_format($allocated_qty, '1', '') + decimal_format($issue_rtn_qty, '1', '')) - (decimal_format($issue_qty, '1', ''));
							//for row span
							$rspn = count($allocation_data[$job_no]);
							if($check_row_span[$job_no] != $job_no)
							{
								$i++;
								$check_row_span[$job_no] = $job_no;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="middle">
									<td width="30" align="center" rowspan="<? echo $rspn; ?>"><? echo $i; ?></td>
									<td width="120" align="center" rowspan="<? echo $rspn; ?>"><p><? echo $company; ?>&nbsp;</p></td>
									<td width="120" rowspan="<? echo $rspn; ?>">
										<div style="word-wrap:break-word;text-align: center;">
											<? echo $job_no; ?>
										</div> 
									</td>
									<td width="80" rowspan="<? echo $rspn; ?>"><p><? echo $booking_no; ?></p></td>
									<td width="70" align="center" rowspan="<? echo $rspn; ?>"><? echo change_date_format($booking_date);?></td>
									<td width="120" align="center" rowspan="<? echo $rspn; ?>"><p><? echo $customer; ?></p></td>
									<td width="120" align="center" rowspan="<? echo $rspn; ?>"><p><? echo $cust_buyer; ?></p></td>
									<td width="70" align="center" rowspan="<? echo $rspn; ?>"><? echo change_date_format($dlv_start_date);?></td>
									<td width="70" align="center" rowspan="<? echo $rspn; ?>"><? echo change_date_format($dlv_end_date);?></td>
									<td width="70" align="center" rowspan="<? echo $rspn; ?>"><? echo change_date_format($row['TASK_START_DATE']);?></td>
									<td width="70" align="center" rowspan="<? echo $rspn; ?>"><? echo change_date_format($row['TASK_FINISH_DATE']);?></td>
									<td width="80" align="right" rowspan="<? echo $rspn; ?>"><p><? echo decimal_format($booking_qty, '1', ','); ?></p></td>
									<td width="80" align="right" rowspan="<? echo $rspn; ?>"><p><? echo decimal_format($req_qty, '1', ','); ?></p></td>
                                    <?
								}
								?>
								<td width="70" align="center"><? echo $prod_id; ?></td>
								<td width="60" align="center"><p><? echo $yarn_count_arr[$vprod['YARN_COUNT_ID']]; ?></p></td>
								<td width="120"><p><? echo $vprod['COMPOSITION']; ?>&nbsp;</p></td>
								<td width="100" align="center"><p><? echo $yarn_type[$vprod['YARN_TYPE']]; ?>&nbsp;</p></td>
								<td width="100"><? echo $color_name_arr[$vprod['COLOR']]; ?></td>
								<td width="80"><? echo $vprod['LOT']; ?></td>
								<td width="120"><p><? echo $vprod['SUPPLIER']; ?></p></td>
								<td width="70" align="center"><? echo change_date_format($vprod['ALLOCATION_DATE']); ?></td>
								<td width="80" align="right"><? echo decimal_format($allocated_qty, '1', ','); ?></td>
								<td width="80" align="right"><? echo decimal_format($issue_qty, '1', ','); ?></td>
								<td width="80" align="right"><? echo decimal_format($issue_rtn_qty, '1', ','); ?></td>
								<td width="80" align="right"><? echo decimal_format($balance, '1', ','); ?></td>
								<td width="60" align="center"><? echo $vprod['AGE_OF_DAYS']; ?></td>
							</tr>
							<?
							//for sub total
							$sub_total_allocated_qty += decimal_format($allocated_qty, '1', '');
							$sub_total_issue_qty += decimal_format($issue_qty, '1', '');
							$sub_total_issue_rtn_qty += decimal_format($issue_rtn_qty, '1', '');
							$sub_total_balance_qty += decimal_format($balance, '1', '');
							
							//for grand total
							$grand_total_allocate_qty += decimal_format($allocated_qty, '1', '');
							$grand_total_issue_qty += decimal_format($issue_qty, '1', '');
							$grand_total_issue_rtn_qty += decimal_format($issue_rtn_qty, '1', '');
							$grand_total_balance += decimal_format($balance, '1', '');
						}
						
						if($rspn > 1)
						{
							?>
							<tr style="font-weight:bold; background-color:#F0F0F0;">
								<td colspan="21" align="right" style="padding-right:10px;">Job Total</td>
								<td align="right"><? echo decimal_format($sub_total_allocated_qty, '1', ','); ?></td>
								<td align="right"><? echo decimal_format($sub_total_issue_qty, '1', ','); ?></td>
								<td align="right"><? echo decimal_format($sub_total_issue_rtn_qty, '1', ','); ?></td>
								<td align="right"><? echo decimal_format($sub_total_balance_qty, '1', ','); ?></td>
								<td></td>
							</tr>
							<?
						}
					}
					$grand_total_booking_qty += decimal_format($booking_qty, '1', '');
					$grand_total_req_qty += decimal_format($req_qty, '1', '');
				}
				?>
			</table>
		</div>
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">			
			<tr class="tbl_bottom">
				<td width="30">&nbsp;</td> 
				<td width="120">&nbsp;</td>  
				<td width="120">&nbsp;</td>  
				<td width="80">&nbsp;</td>  
				<td width="70">&nbsp;</td>  
				<td width="120">&nbsp;</td>  
				<td width="120">&nbsp;</td>  
				<td width="70">&nbsp;</td>
				<td width="70">&nbsp;</td>  
				<td width="70">&nbsp;</td>  
				<td width="70"align="right">Grand Total</td>  
				<td width="80" style="word-break: break-all; text-align:right;" id="value_total_booking_qty"><? echo decimal_format($grand_total_booking_qty, '1', ''); ?></td>  
				<td width="80" style="word-break: break-all; text-align:right;" id="value_total_req_qty"><? echo decimal_format($grand_total_req_qty, '1', ''); ?></td>
				<td width="70">&nbsp;</td>  
				<td width="60">&nbsp;</td>  
				<td width="120">&nbsp;</td>
				<td width="100">&nbsp;</td> 
				<td width="100">&nbsp;</td> 
				<td width="80">&nbsp;</td> 
				<td width="120">&nbsp;</td>  
				<td width="70">&nbsp;</td>
				<td width="80" style="word-break: break-all; text-align:right;" id="value_total_allocation_qty"><? echo decimal_format($grand_total_allocate_qty, '1', ''); ?></td>
				<td width="80" style="word-break: break-all;text-align:right;" id="value_total_issue_qty"><? echo decimal_format($grand_total_issue_qty, '1', ''); ?></td>
				<td width="80" style="word-break: break-all;text-align:right;" id="value_total_issue_return_qty"><? echo decimal_format($grand_total_issue_rtn_qty, '1', ''); ?></td>
				<td width="80" align="right" style="word-break: break-all;" id="value_total_balance"><? echo decimal_format($grand_total_balance, '1', ''); ?></td>
				<td width="60">&nbsp;</td>  
			</tr>
		</table>			
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}

	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}

//for summary btn
if ($action == "generate_summary_report")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$from_date = change_date_format($from_date, '', '', 1);
	$to_date = change_date_format($to_date, '', '', 1);
	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);
	ob_start();
	
	//for company
	$companyArr[0] = "All Company";
	$company_cond_mrr = '';
	if ($cbo_company_name != 0)
	{
		$company_cond_mrr = " and b.company_id=".$cbo_company_name;
	}

	$search_cond = "";
	$search_cond2 = "";
	//for yarn type
	if ($cbo_yarn_type == 0)
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_type in (".$cbo_yarn_type.")";
	
	//for yarn count
	if ($txt_count == "")
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_count_id in(".$txt_count.")";
	
	//for lot
	if ($txt_lot_no == "")
		$search_cond .= "";
	else
		$search_cond .= " and trim(b.lot)='" . trim($txt_lot_no) . "'";

	//for supplier
	if ($cbo_supplier == 0)
		$search_cond .= "";
	else
		$search_cond .= "  and b.supplier_id in(".$cbo_supplier.")";
		
	//for composition
	if ($txt_composition == "")
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_comp_type1st in (".$txt_composition_id.")";
		
	//for fso no
	if($txt_job_no != '')
	{
		$search_cond2 .= " AND C.JOB_NO LIKE '%".$txt_job_no."'";
	}
	
	//for booking no
	if($txt_booking_no != '')
	{
		$search_cond2 .= " AND C.SALES_BOOKING_NO LIKE '%".$txt_booking_no."'";
	}

	//for date condition
	if($from_date != '' && $to_date != '')
	{
		$search_cond2 .= " AND C.TASK_START_DATE BETWEEN '".$from_date."' AND '".$to_date."'";
	}

	if ($cbo_company_name == 0)
	{
		$company_cond = "";
	}
	else
	{
		$company_cond = " and a.company_id=".$cbo_company_name;
		$search_cond2 .= " AND D.COMPANY_ID=".$cbo_company_name;
	}

	if ($to_date != "")
		$mrr_date_cond = " and b.transaction_date<='".$to_date."'";

	$fso_arr = array();
	$prd_arr = array();
	$row_span = array();

	//for TNA information
	$sql_tna = "SELECT C.JOB_NO, C.TASK_START_DATE, C.TASK_FINISH_DATE FROM TNA_PROCESS_MST C, FABRIC_SALES_ORDER_MST D WHERE C.JOB_NO = D.JOB_NO AND C.TASK_NUMBER = 48 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED=0
	".$search_cond2." GROUP BY C.JOB_NO, C.TASK_START_DATE, C.TASK_FINISH_DATE ORDER BY C.JOB_NO";
	//echo $sql_tna;
	$sql_tna_rslt = sql_select($sql_tna);
	$tna_data = array();
	foreach($sql_tna_rslt as $row)
	{
		$fso_arr[$row['JOB_NO']] = $row['JOB_NO'];
		$tna_data[$row['JOB_NO']]['TASK_START_DATE'] = $row['TASK_START_DATE'];
		$tna_data[$row['JOB_NO']]['TASK_FINISH_DATE'] = $row['TASK_FINISH_DATE'];
	}

	//for fso no
	$con = connect();
	execute_query("DELETE FROM TMP_JOB_NO WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_ISSUE_ID WHERE USER_ID = ".$user_id);
	oci_commit($con);
	
	foreach($fso_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_JOB_NO(JOB_NO, USERID) VALUES('".$val."', '".$user_id."')");
	}
	
	//for allocation information
	$sql_allocation = "SELECT A.ITEM_ID, A.JOB_NO, A.PO_BREAK_DOWN_ID, A.BOOKING_NO, SUM(A.QNTY) AS ALLOCATE_QTY, A.INSERT_DATE, A.ALLOCATION_DATE, A.IS_SALES, B.COMPANY_ID, B.ID, B.SUPPLIER_ID, B.YARN_COUNT_ID, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_COMP_TYPE2ND, B.YARN_COMP_PERCENT2ND, B.YARN_TYPE, B.COLOR, B.LOT, B.ALLOCATED_QNTY, B.AVAILABLE_QNTY, B.AVG_RATE_PER_UNIT, B.DYED_TYPE AS IS_DYIED_YARN, B.IS_WITHIN_GROUP FROM INV_MATERIAL_ALLOCATION_DTLS A, PRODUCT_DETAILS_MASTER B, TMP_JOB_NO C WHERE A.ITEM_ID=B.ID AND A.JOB_NO = C.JOB_NO AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.QNTY>0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.USERID = ".$user_id.$search_cond.$company_cond_mrr." GROUP BY A.JOB_NO, A.PO_BREAK_DOWN_ID, A.BOOKING_NO, A.INSERT_DATE, A.ALLOCATION_DATE, A.IS_SALES, B.COMPANY_ID, B.ID, A.ITEM_ID, B.LOT, B.SUPPLIER_ID, B.YARN_COUNT_ID, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_COMP_TYPE2ND, B.YARN_COMP_PERCENT2ND, B.YARN_TYPE, B.COLOR, B.ALLOCATED_QNTY, B.AVAILABLE_QNTY, B.AVG_RATE_PER_UNIT, B.DYED_TYPE, B.IS_WITHIN_GROUP ORDER BY A.JOB_NO";
	$sql_allocation_rslt = sql_select($sql_allocation);
	$allocation_data = array();
	foreach($sql_allocation_rslt as $row)
	{
		$prd_arr[$row['ITEM_ID']] = $row['ITEM_ID'];
		$row_span[$row['JOB_NO']]++;
		
		$ageOfDays = datediff("d", $row['INSERT_DATE'], date("Y-m-d"));
		$row[csf("allocate_qty")] = $qty_arr[$row['JOB_NO']][$row['ITEM_ID']]['qty'];

		//for composition
		$compositionDetails = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%\n";
		if ($row['YARN_COMP_TYPE2ND'] != 0)
		{
			$compositionDetails .= $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		//end for composition
		
		//for supplier
		if($row['IS_WITHIN_GROUP'] == 1)
		{
			$supplier = $companyArr[$row['SUPPLIER_ID']];
		}
		else
		{
			$supplier = $supplierArr[$row['SUPPLIER_ID']];							
		}
		//end for supplier
		
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['COMPANY_ID'] = $row['COMPANY_ID'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['AGE_OF_DAYS'] = $ageOfDays;
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['COMPOSITION'] = $compositionDetails;
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['SUPPLIER'] = $supplier;
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['COLOR'] = $row['COLOR'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['LOT'] = $row['LOT'];
		$allocation_data[$row['JOB_NO']][$row['ITEM_ID']]['ALLOCATION_DATE'] = $row['ALLOCATION_DATE'];
	}
	/*echo "<pre>";
	print_r($allocation_data);
	echo "<pre>";*/
	
	//for product
	foreach($prd_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_PROD_ID(PROD_ID, USERID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);
	
	//for fso info
	$sql_fso = "SELECT A.ID, A.COMPANY_ID,  A.JOB_NO, A.SALES_BOOKING_NO, A.BUYER_ID, A.CUSTOMER_BUYER, A.BOOKING_DATE, A.DELIVERY_DATE, A.DELIVERY_START_DATE, B.FINISH_QTY, B.PP_QNTY, B.MTL_QNTY, B.FPT_QNTY, B.GPT_QNTY,(SELECT SUM(D.CONS_QTY) FROM FABRIC_SALES_ORDER_YARN_DTLS D WHERE A.ID = D.MST_ID AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 GROUP BY D.MST_ID) AS REQ_QTY FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B, TMP_JOB_NO C WHERE A.ID = B.MST_ID AND A.JOB_NO = B.JOB_NO_MST AND A.JOB_NO = C.jOB_NO AND B.JOB_NO_MST = C.jOB_NO AND C.USERID = ".$user_id." AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0";
	//echo $sql_fso;
	$sql_fso_rslt = sql_select($sql_fso);
	$fso_data = array();
	foreach($sql_fso_rslt as $row)
	{
		$fso_data[$row['JOB_NO']]['ID'] = $row['ID'];
		$fso_data[$row['JOB_NO']]['COMPANY_ID'] = $row['COMPANY_ID'];
		$fso_data[$row['JOB_NO']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
		$fso_data[$row['JOB_NO']]['BUYER_ID'] = $buy_name_arr[$row['BUYER_ID']];
		$fso_data[$row['JOB_NO']]['CUSTOMER_BUYER'] = $buy_name_arr[$row['CUSTOMER_BUYER']];
		$fso_data[$row['JOB_NO']]['BOOKING_DATE'] = $row['BOOKING_DATE'];
		$fso_data[$row['JOB_NO']]['DELIVERY_START_DATE'] = $row['DELIVERY_START_DATE'];
		$fso_data[$row['JOB_NO']]['DELIVERY_END_DATE'] = $row['DELIVERY_DATE'];
		$fso_data[$row['JOB_NO']]['BOOKING_QTY'] += ($row['FINISH_QTY']+$row['PP_QNTY']+$row['MTL_QNTY']+$row['FPT_QNTY']+$row['GPT_QNTY']);
		$fso_data[$row['JOB_NO']]['REQ_QTY'] = $row['REQ_QTY'];
	}
	unset($sql_fso_rslt);
	
	//for issue qty
	$sql_issue = "SELECT A.ID AS ISSUE_ID, B.PROD_ID, B.JOB_NO, C.ID, C.PO_BREAKDOWN_ID, C.QUANTITY FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C, TMP_PROD_ID D WHERE A.ID = B.MST_ID AND B.ID = C.TRANS_ID AND B.PROD_ID = C.PROD_ID AND B.PROD_ID = D.PROD_ID AND C.PROD_ID = D.PROD_ID AND A.ENTRY_FORM = 3 AND A.ISSUE_BASIS IN(1, 3, 8) AND A.ISSUE_PURPOSE IN(1, 2, 7, 12, 15, 38, 46, 50, 51, 63) AND B.TRANSACTION_TYPE=2 AND B.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.USERID = ".$user_id."";
	//echo $sql_issue;
	$sql_issue_rslt = sql_select($sql_issue);
	$check_trans_id = array();
	$issue_id_arr = array();
	$issue_job_arr = array();
	foreach($sql_issue_rslt as $row)
	{
		if($check_trans_id[$row['ID']] != $row['ID'])
		{
			$check_trans_id[$row['ID']] = $row['ID'];
			$issue_id_arr[$row['ISSUE_ID']] = $row['ISSUE_ID'];
			$issue_data[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]['issue_qty'] += $row['QUANTITY'];
			$issue_job_arr[$row['ISSUE_ID']][$row['PROD_ID']]['job_no'] = $row['JOB_NO'];
		}
	}
	unset($sql_issue_rslt);
	/*echo "<pre>";
	print_r($issue_data);
	echo "</pre>";*/
	
	//for issue
	foreach($issue_id_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_ISSUE_ID(ISSUE_ID, USER_ID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);
	
	//for issue return qty
	$sql_issue_rtn="SELECT A.ISSUE_ID, B.PROD_ID, C.ID, C.PO_BREAKDOWN_ID, C.QUANTITY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C, TMP_ISSUE_ID D WHERE  A.ID=B.MST_ID AND A.ISSUE_ID = D.ISSUE_ID AND B.ISSUE_ID = D.ISSUE_ID AND B.ID = C.TRANS_ID AND B.PROD_ID = C.PROD_ID AND D.USER_ID = ".$user_id." AND A.ITEM_CATEGORY = 1 AND A.ENTRY_FORM=9 AND B.TRANSACTION_TYPE=4 AND B.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0";
	// AND C.ISSUE_PURPOSE IN(1, 2, 7, 12, 15, 38, 46, 50, 51, 63)
	//echo $sql_issue_rtn;
	$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);
	$issue_rtn_data = array();
	$duplicate_check = array();
	foreach($sql_issue_rtn_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$issue_rtn_data[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]['issue_rtn_qty'] += $row['QUANTITY'];
		}
	}
	unset($sql_issue_rtn_rslt);
	//echo "<pre>";
	//print_r($reqNoArr); die;
	
	//for allocation qty
	$sql_allo = "SELECT A.ID, A.ITEM_ID, A.JOB_NO, A.QNTY, A.INSERT_DATE, A.ALLOCATION_DATE, A.IS_SALES FROM INV_MATERIAL_ALLOCATION_DTLS A, TMP_JOB_NO B, TMP_PROD_ID C WHERE A.JOB_NO = B.JOB_NO AND A.ITEM_ID = C.PROD_ID AND B.USERID = ".$user_id." AND C.USERID = ".$user_id." AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.IS_SALES = 1 AND A.QNTY>0 ORDER BY A.JOB_NO";
	//echo $sql_allo;
	$sql_allo_rslt = sql_select($sql_allo);
	$qty_arr = array();
	foreach($sql_allo_rslt as $row)
	{
		$qty_arr[$row['JOB_NO']][$row['ITEM_ID']]['qty'] += $row['QNTY'];
	}
	
	$tbl_width = 1310;
	?>
	<style type="text/css">
	</style>
	<div style="width:1328px;">
    	<?
        if(empty($tna_data))
		{
			echo get_empty_data_msg();
			die;
        }
        ?>
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px; float:left;" rules="all" id="table_header_1">
			<thead> 
				<tr class="form_caption" style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:16px; font-weight:bold">TNA Wise Yarn Allocation Report Sales</td>
				</tr>
				<tr style="border:none;">
					<td colspan="15" align="center" style="border:none; font-size:14px;">
						Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="15" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
					</td>
				</tr>
				<tr>
					<th width="120">Company</th>
					<th width="120">Sales Order No</th>
					<th width="80">Booking No.</th>
					<th width="70">Booking Date</th>
					<th width="120">Customer</th>
					<th width="120">Cust. Buyer</th>
					<th width="70">Delivery Start Date</th>
					<th width="70">Delivery End Date</th>
					<th width="70">Yarn Delivery Start Date</th>
					<th width="70">Yarn Delivery End Date</th>
					<th width="80">Fabric Booking Qty</th>
					<th width="80">Req. Yarn Qty</th>
					<th width="80">Allocated Qty</th>
					<th width="80">Yet to Allocate</th>
					<th width="80">Net Issue Qty</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width+18; ?>px; overflow-y:scroll; max-height:250px; float:left;" id="scroll_body">  
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" style="font:'Arial Narrow';" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
				$i = 0;
				$balance = 0;
				$grand_total_allocate_qty = 0;
				$grand_total_issue_qty = 0;
				$grand_total_issue_rtn_qty = 0;
				$grand_total_balance = 0;
				$prodStock = array();
				foreach ($tna_data as $job_no=>$row) 
				{
					$i++;
					/*if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";*/
					$bgcolor = "#FFFFFF";
			
					//for fso information	
					$company_id = $fso_data[$job_no]['COMPANY_ID'];	
					$company = $companyArr[$fso_data[$job_no]['COMPANY_ID']];	
					$booking_no = $fso_data[$job_no]['SALES_BOOKING_NO'];	
					$customer = $fso_data[$job_no]['BUYER_ID'];	
					$cust_buyer = $fso_data[$job_no]['CUSTOMER_BUYER'];
					$booking_date = $fso_data[$job_no]['BOOKING_DATE'];
					$dlv_start_date = $fso_data[$job_no]['DELIVERY_START_DATE'];
					$dlv_end_date = $fso_data[$job_no]['DELIVERY_END_DATE'];
					$booking_qty = $fso_data[$job_no]['BOOKING_QTY'];
					$req_qty = $fso_data[$job_no]['REQ_QTY'];
					$fso_id = $fso_data[$job_no]['ID'];
					
					$sub_total_allocated_qty = 0;
					$sub_total_issue_qty = 0;
					$sub_total_issue_rtn_qty = 0;
					$sub_total_balance_qty = 0;
					
					//for allocation qty
					$allocated_qty = 0;
					$issue_qty = 0;
					$issue_rtn_qty = 0;
					$prod_id_arr = array();
					if(!empty($allocation_data[$job_no]))
					{
						foreach($allocation_data[$job_no] as $prod_id=>$vprod)
						{
							$prod_id_arr[$prod_id] = $prod_id;
							//for allocation qty
							$allocated_qty += $qty_arr[$job_no][$prod_id]['qty'];
							
							//for issue qty
							$issue_qty += $issue_data[$fso_id][$prod_id]['issue_qty'];
							$issue_rtn_qty += $issue_rtn_data[$fso_id][$prod_id]['issue_rtn_qty'];
						}
					}
					
					//for balance qty
					$balance = decimal_format($req_qty, '1', '') - decimal_format($allocated_qty, '1', '');
					
					//for net issue qty
					$net_issue = decimal_format($issue_qty, '1', '') - decimal_format($issue_rtn_qty, '1', '');
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="middle">
						<td width="120" align="center"><p><? echo $company; ?>&nbsp;</p></td>
						<td width="120"><p><a href='##' onclick="fabric_sales_order_print6('<? echo $company_id; ?>', '<? echo $booking_no; ?>', '<? echo $job_no; ?>')"><? echo $job_no; ?></a></p></td>
						<td width="80"><p><? echo $booking_no; ?></p></td>
						<td width="70" align="center"><? echo change_date_format($booking_date);?></td>
						<td width="120" align="center"><p><? echo $customer; ?></p></td>
						<td width="120" align="center"><p><? echo $cust_buyer; ?></p></td>
						<td width="70" align="center"><? echo change_date_format($dlv_start_date);?></td>
						<td width="70" align="center"><? echo change_date_format($dlv_end_date);?></td>
                        <td width="70" align="center"><? echo change_date_format($row['TASK_START_DATE']);?></td>
                        <td width="70" align="center"><? echo change_date_format($row['TASK_FINISH_DATE']);?></td>
						<td width="80" align="right"><p><? echo decimal_format($booking_qty, '1', ','); ?></p></td>
						<td width="80" align="right"><p><a href='##' onclick="func_qty_popup('<? echo $fso_id; ?>','req_qty_popup')"><? echo decimal_format($req_qty, '1', ','); ?></a></p></td>
						<td width="80" align="right"><p><a href='##' onclick="func_allocation_qty_popup('<? echo $job_no; ?>','<? echo implode(',',$prod_id_arr); ?>','allocation_qty_popup')"><? echo decimal_format($allocated_qty, '1', ','); ?></a></p></td>
						<td width="80" align="right"><p><? echo decimal_format($balance, '1', ','); ?></p></td>
						<td width="80" align="right"><p><? echo decimal_format($net_issue, '1', ','); ?></p></td>
					</tr>
					<?							
				}
				?>
			</table>
		</div>
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename)
	{
		@unlink($filename);
	}

	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}

//for req_qty_popup
if ($action == "req_qty_popup")
{
	echo load_html_head_contents("Required Qty.", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);
	
	$sql = "SELECT YARN_COUNT_ID, COMPOSITION_ID, YARN_TYPE, COLOR_ID, CONS_QTY FROM FABRIC_SALES_ORDER_YARN_DTLS WHERE MST_ID = ".$id." AND STATUS_ACTIVE = 1 AND IS_DELETED = 0";
	$sql_rslt = sql_select($sql);
	
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	?>
	<div align="center">
		<table width="580" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<th width="30">SL</th>
				<th width="60">Count</th>
				<th width="150">Composition</th>
				<th width="120">Type</th>
				<th width="120">Color</th>
				<th width="100">Qty</th>
			</thead>
		</table>
		<div style="width:597px; max-height:300px" id="scroll_body">
			<table width="580" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            	<tbody>
                <?
				$sl = 0;
                foreach($sql_rslt as $row)
				{
					$sl++;
					if ($sl % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" height="25" valign="middle">
                    	<td width="30" align="center"><? echo $sl; ?></td>
                    	<td width="60"><? echo $yarn_count_arr[$row['YARN_COUNT_ID']]; ?></td>
                    	<td width="150"><? echo $composition[$row['COMPOSITION_ID']]; ?></td>
                    	<td width="120"><? echo $yarn_type[$row['YARN_TYPE']]; ?></td>
                    	<td width="120"><? echo $color_name_arr[$row['COLOR_ID']]; ?></td>
                    	<td width="100" align="right"><? echo decimal_format($row['CONS_QTY'], '1', ','); ?></td>
                    </tr>
                    <?
					$total_qty += decimal_format($row['CONS_QTY'], '1', '');
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="5">Total</th>
                        <th align="right"><? echo decimal_format($total_qty, '1', ','); ?></th>
                    </tr>
                </tfoot>
			</table>
		</div>
	</div>
	<?
	exit();
}

//for allocation_qty_popup
if ($action == "allocation_qty_popup")
{
	echo load_html_head_contents("Allocation Qty.", "../../../../", 1, 1, $unicode, '', '');
	extract($_REQUEST);

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	
	//for allocation information
	$sql_allocation = "SELECT A.ITEM_ID, A.JOB_NO, A.PO_BREAK_DOWN_ID, A.BOOKING_NO, A.INSERT_DATE, A.ALLOCATION_DATE, A.IS_SALES, B.COMPANY_ID, B.ID, B.SUPPLIER_ID, B.YARN_COUNT_ID, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_COMP_TYPE2ND, B.YARN_COMP_PERCENT2ND, B.YARN_TYPE, B.COLOR, B.LOT, B.ALLOCATED_QNTY, B.AVAILABLE_QNTY, B.AVG_RATE_PER_UNIT, B.DYED_TYPE AS IS_DYIED_YARN, B.IS_WITHIN_GROUP FROM INV_MATERIAL_ALLOCATION_DTLS A, PRODUCT_DETAILS_MASTER B WHERE A.ITEM_ID=B.ID AND A.JOB_NO = '".$job_no."' AND B.ID IN(".$prod_id.") AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.QNTY>0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 GROUP BY A.JOB_NO, A.PO_BREAK_DOWN_ID, A.BOOKING_NO, A.INSERT_DATE, A.ALLOCATION_DATE, A.IS_SALES, B.COMPANY_ID, B.ID, A.ITEM_ID, B.LOT, B.SUPPLIER_ID, B.YARN_COUNT_ID, B.YARN_COMP_TYPE1ST, B.YARN_COMP_PERCENT1ST, B.YARN_COMP_TYPE2ND, B.YARN_COMP_PERCENT2ND, B.YARN_TYPE, B.COLOR, B.ALLOCATED_QNTY, B.AVAILABLE_QNTY, B.AVG_RATE_PER_UNIT, B.DYED_TYPE, B.IS_WITHIN_GROUP";
	//echo $sql_allocation;
	$sql_allocation_rslt = sql_select($sql_allocation);
	$allocation_data = array();
	foreach($sql_allocation_rslt as $row)
	{
		//for composition
		$compositionDetails = $composition[$row['YARN_COMP_TYPE1ST']] . " " . $row['YARN_COMP_PERCENT1ST'] . "%\n";
		if ($row['YARN_COMP_TYPE2ND'] != 0)
		{
			$compositionDetails .= $composition[$row['YARN_COMP_TYPE2ND']] . " " . $row['YARN_COMP_PERCENT2ND'] . "%";
		}
		//end for composition
		
		//for supplier
		if($row['IS_WITHIN_GROUP'] == 1)
		{
			$supplier = $companyArr[$row['SUPPLIER_ID']];
		}
		else
		{
			$supplier = $supplierArr[$row['SUPPLIER_ID']];							
		}
		//end for supplier
		
		$allocation_data[$row['ITEM_ID']]['COMPOSITION'] = $compositionDetails;
		$allocation_data[$row['ITEM_ID']]['SUPPLIER'] = $supplier;
		$allocation_data[$row['ITEM_ID']]['YARN_COUNT_ID'] = $row['YARN_COUNT_ID'];
		$allocation_data[$row['ITEM_ID']]['YARN_TYPE'] = $row['YARN_TYPE'];
		$allocation_data[$row['ITEM_ID']]['COLOR'] = $row['COLOR'];
		$allocation_data[$row['ITEM_ID']]['LOT'] = $row['LOT'];
		$allocation_data[$row['ITEM_ID']]['ALLOCATION_DATE'] = $row['ALLOCATION_DATE'];
	}
	/*echo "<pre>";
	print_r($allocation_data);
	echo "<pre>";*/
	
	//for allocation qty
	$sql_allo = "SELECT A.ID, A.ITEM_ID, A.JOB_NO, A.QNTY, A.INSERT_DATE, A.ALLOCATION_DATE, A.IS_SALES FROM INV_MATERIAL_ALLOCATION_DTLS A WHERE A.JOB_NO = '".$job_no."' AND A.ITEM_ID IN(".$prod_id.") AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND A.IS_SALES = 1 AND A.QNTY>0";
	//echo $sql_allo;
	$sql_allo_rslt = sql_select($sql_allo);
	$qty_arr = array();
	foreach($sql_allo_rslt as $row)
	{
		$qty_arr[$row['ITEM_ID']]['qty'] += $row['QNTY'];
	}
	?>
	<div align="center">
		<table width="960" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all">
			<thead>
				<th width="30">SL</th>
                <th width="100">Product ID</th>
				<th width="60">Count</th>
				<th width="150">Composition</th>
				<th width="120">Type</th>
				<th width="120">Color</th>
				<th width="80">Lot</th>
				<th width="120">Supplier</th>
				<th width="80">Allocated Date</th>
				<th width="100">Qty</th>
			</thead>
		</table>
		<div style="width:977px; max-height:300px" id="scroll_body">
			<table width="960" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            	<tbody>
                <?
				$sl = 0;
                foreach($allocation_data as $prd_id=>$row)
				{
					$sl++;
					if ($sl % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
						
					$allocated_qty = $qty_arr[$prd_id]['qty'];
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" height="25" valign="middle">
                    	<td width="30" align="center"><p><? echo $sl; ?></p></td>
                    	<td width="100"><p><? echo $prd_id; ?></p></td>
                    	<td width="60"><p><? echo $yarn_count_arr[$row['YARN_COUNT_ID']]; ?></p></td>
                    	<td width="150"><p><? echo $row['COMPOSITION']; ?></p></td>
                    	<td width="120"><p><? echo $yarn_type[$row['YARN_TYPE']]; ?></p></td>
                    	<td width="120"><p><? echo $color_name_arr[$row['COLOR']]; ?></p></td>
                    	<td width="80"><p><? echo $row['LOT']; ?></p></td>
                    	<td width="120"><p><? echo $row['SUPPLIER']; ?></p></td>
                    	<td width="80" align="center"><p><? echo change_date_format($row['ALLOCATION_DATE']); ?></p></td>
                    	<td width="100" align="right"><p><? echo decimal_format($allocated_qty, '1', ','); ?></p></td>
                    </tr>
                    <?
					$total_qty += decimal_format($allocated_qty, '1', '');
				}
				?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="9">Total</th>
                        <th align="right"><? echo decimal_format($total_qty, '1', ','); ?></th>
                    </tr>
                </tfoot>
			</table>
		</div>
	</div>
	<?
	exit();
}

if ($action == "generate_report_22012022")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$companyArr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplierArr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$color_name_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$buy_short_name_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');
	$buy_name_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$from_date = change_date_format($from_date, '', '', 1);
	$to_date = change_date_format($to_date, '', '', 1);

	$txt_lot_no = trim($txt_lot_no);
	$txt_composition = trim($txt_composition);
	ob_start();
	//===========
	
	//for company
	$companyArr[0] = "All Company";
	$company_cond_mrr = '';
	if ($cbo_company_name != 0)
	{
		$company_cond_mrr = " and b.company_id=".$cbo_company_name;
	}

	$search_cond = "";
	//for yarn type
	if ($cbo_yarn_type == 0)
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_type in (".$cbo_yarn_type.")";
	
	//for yarn count
	if ($txt_count == "")
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_count_id in(".$txt_count.")";
	
	//for lot
	if ($txt_lot_no == "")
		$search_cond .= "";
	else
		$search_cond .= " and trim(b.lot)='" . trim($txt_lot_no) . "'";

	//for supplier
	if ($cbo_supplier == 0)
		$search_cond .= "";
	else
		$search_cond .= "  and b.supplier_id in(".$cbo_supplier.")";
		
	//for composition
	if ($txt_composition == "")
		$search_cond .= "";
	else
		$search_cond .= " and b.yarn_comp_type1st in (".$txt_composition_id.")";
		
	//for fso no
	if($txt_job_no != '')
	{
		$search_cond .= " and a.job_no like '%".$txt_job_no."'";
	}
	
	//for booking no
	if($txt_booking_no != '')
	{
		$search_cond .= " and a.booking_no like '%".$txt_booking_no."'";
	}

	//for date condition
	if($from_date != '' && $to_date != '')
	{
		$search_cond .= " and c.task_start_date between '".$from_date."' and '".$to_date."'";
	}

	if ($cbo_company_name == 0)
	{
		$company_cond = "";
	}
	else
	{
		$company_cond = " and a.company_id=".$cbo_company_name;
	}

	if ($to_date != "")
		$mrr_date_cond = " and b.transaction_date<='".$to_date."'";

	$sql_allocation = "select b.company_id, b.id, a.item_id, a.job_no, a.po_break_down_id, a.booking_no, sum(a.qnty) as allocate_qty, a.insert_date, a.allocation_date, a.is_sales, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.lot, b.allocated_qnty, b.available_qnty, b.avg_rate_per_unit, b.dyed_type as is_dyied_yarn, b.is_within_group, c.task_start_date, c.task_finish_date
	from inv_material_allocation_dtls a, product_details_master b, tna_process_mst c
	where a.item_id=b.id and a.job_no = c.job_no 
	and a.status_active=1 and a.is_deleted=0 and a.qnty>0 
	and b.status_active=1 and b.is_deleted=0 
	and c.task_number = 48 and c.status_active=1 and c.is_deleted=0
	$search_cond $company_cond_mrr
	group by b.company_id, b.id, a.item_id, b.lot, b.supplier_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color, b.allocated_qnty, b.available_qnty, b.avg_rate_per_unit, b.dyed_type, a.job_no, a.po_break_down_id, a.booking_no, a.insert_date, a.allocation_date, a.is_sales, b.is_within_group, c.task_start_date, c.task_finish_date
	order by a.job_no";
	//echo $sql_allocation; die;
	$result_allocation = sql_select($sql_allocation);
	$fso_arr = array();
	$prd_arr = array();
	$row_span = array();
	foreach($result_allocation as $row)
	{
		$fso_arr[$row[csf('job_no')]] = $row[csf('job_no')];
		$prd_arr[$row[csf('item_id')]] = $row[csf('item_id')];
		$row_span[$row[csf('job_no')]]++;
	}
	
	//for fso no
	$con = connect();
	execute_query("DELETE FROM TMP_JOB_NO WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_PROD_ID WHERE USERID = ".$user_id);
	execute_query("DELETE FROM TMP_ISSUE_ID WHERE USER_ID = ".$user_id);
	oci_commit($con);
	
	foreach($fso_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_JOB_NO(JOB_NO, USERID) VALUES('".$val."', '".$user_id."')");
	}
	
	//for product
	foreach($prd_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_PROD_ID(PROD_ID, USERID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);
	
	//for fso info
	$sql_fso = "SELECT A.ID, A.JOB_NO, A.BUYER_ID, A.CUSTOMER_BUYER, A.BOOKING_DATE, A.DELIVERY_DATE, A.DELIVERY_START_DATE, B.FINISH_QTY, B.PP_QNTY, B.MTL_QNTY, B.FPT_QNTY, B.GPT_QNTY,(SELECT SUM(D.CONS_QTY) FROM FABRIC_SALES_ORDER_YARN_DTLS D WHERE A.ID = D.MST_ID AND D.STATUS_ACTIVE = 1 AND D.IS_DELETED = 0 GROUP BY D.MST_ID) AS REQ_QTY FROM FABRIC_SALES_ORDER_MST A, FABRIC_SALES_ORDER_DTLS B, TMP_JOB_NO C WHERE A.ID = B.MST_ID AND A.JOB_NO = B.JOB_NO_MST AND A.JOB_NO = C.jOB_NO AND B.JOB_NO_MST = C.jOB_NO AND C.USERID = ".$user_id." AND A.STATUS_ACTIVE = 1 AND A.IS_DELETED = 0 AND B.STATUS_ACTIVE = 1 AND B.IS_DELETED = 0";
	//echo $sql_fso;
	$sql_fso_rslt = sql_select($sql_fso);
	$fso_data = array();
	foreach($sql_fso_rslt as $row)
	{
		$fso_data[$row['JOB_NO']]['ID'] = $row['ID'];
		$fso_data[$row['JOB_NO']]['BUYER_ID'] = $buy_name_arr[$row['BUYER_ID']];
		$fso_data[$row['JOB_NO']]['CUSTOMER_BUYER'] = $buy_name_arr[$row['CUSTOMER_BUYER']];
		$fso_data[$row['JOB_NO']]['BOOKING_DATE'] = $row['BOOKING_DATE'];
		$fso_data[$row['JOB_NO']]['DELIVERY_START_DATE'] = $row['DELIVERY_START_DATE'];
		$fso_data[$row['JOB_NO']]['DELIVERY_END_DATE'] = $row['DELIVERY_DATE'];
		$fso_data[$row['JOB_NO']]['BOOKING_QTY'] += ($row['FINISH_QTY']+$row['PP_QNTY']+$row['MTL_QNTY']+$row['FPT_QNTY']+$row['GPT_QNTY']);
		$fso_data[$row['JOB_NO']]['REQ_QTY'] = $row['REQ_QTY'];
	}
	unset($sql_fso_rslt);
	
	//for issue qty
	$sql_issue = "SELECT A.ID AS ISSUE_ID, B.PROD_ID, B.JOB_NO, C.ID, C.PO_BREAKDOWN_ID, C.QUANTITY FROM INV_ISSUE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C, TMP_PROD_ID D WHERE A.ID = B.MST_ID AND B.ID = C.TRANS_ID AND B.PROD_ID = C.PROD_ID AND B.PROD_ID = D.PROD_ID AND C.PROD_ID = D.PROD_ID AND A.ENTRY_FORM = 3 AND A.ISSUE_BASIS IN(1, 3, 8) AND A.ISSUE_PURPOSE IN(1, 2, 7, 12, 15, 38, 46, 50, 51, 63) AND B.TRANSACTION_TYPE=2 AND B.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND D.USERID = ".$user_id."";
	//echo $sql_issue;
	$sql_issue_rslt = sql_select($sql_issue);
	$check_trans_id = array();
	$issue_id_arr = array();
	$issue_job_arr = array();
	foreach($sql_issue_rslt as $row)
	{
		if($check_trans_id[$row['ID']] != $row['ID'])
		{
			$check_trans_id[$row['ID']] = $row['ID'];
			$issue_id_arr[$row['ISSUE_ID']] = $row['ISSUE_ID'];
			$issue_data[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]['issue_qty'] += $row['QUANTITY'];
			$issue_job_arr[$row['ISSUE_ID']][$row['PROD_ID']]['job_no'] = $row['JOB_NO'];
		}
	}
	unset($sql_issue_rslt);
	/*echo "<pre>";
	print_r($issue_data);
	echo "</pre>";*/
	
	//for issue
	foreach($issue_id_arr as $key=>$val)
	{
		execute_query("INSERT INTO TMP_ISSUE_ID(ISSUE_ID, USER_ID) VALUES('".$val."', '".$user_id."')");
	}
	oci_commit($con);
	
	//for issue return qty
	$sql_issue_rtn="SELECT A.ISSUE_ID, B.PROD_ID, C.ID, C.PO_BREAKDOWN_ID, C.QUANTITY FROM INV_RECEIVE_MASTER A, INV_TRANSACTION B, ORDER_WISE_PRO_DETAILS C, TMP_ISSUE_ID D WHERE  A.ID=B.MST_ID AND A.ISSUE_ID = D.ISSUE_ID AND B.ISSUE_ID = D.ISSUE_ID AND B.ID = C.TRANS_ID AND B.PROD_ID = C.PROD_ID AND D.USER_ID = ".$user_id." AND A.ITEM_CATEGORY = 1 AND A.ENTRY_FORM=9 AND B.TRANSACTION_TYPE=4 AND B.ITEM_CATEGORY=1 AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0";
	// AND C.ISSUE_PURPOSE IN(1, 2, 7, 12, 15, 38, 46, 50, 51, 63)
	//echo $sql_issue_rtn;
	$sql_issue_rtn_rslt = sql_select($sql_issue_rtn);
	$issue_rtn_data = array();
	$duplicate_check = array();
	foreach($sql_issue_rtn_rslt as $row)
	{
		if($duplicate_check[$row['ID']] != $row['ID'])
		{
			$duplicate_check[$row['ID']] = $row['ID'];
			$issue_rtn_data[$row['PO_BREAKDOWN_ID']][$row['PROD_ID']]['issue_rtn_qty'] += $row['QUANTITY'];
		}
	}
	unset($sql_issue_rtn_rslt);
	//echo "<pre>";
	//print_r($reqNoArr); die;
	
	//for allocation qty
	$sql_allo = "select a.id, a.item_id, a.job_no, a.qnty, a.insert_date, a.allocation_date, a.is_sales
	from inv_material_allocation_dtls a, tmp_job_no b, tmp_prod_id c
	where a.job_no = b.job_no and a.item_id = c.prod_id and b.userid = ".$user_id." and c.userid = ".$user_id." and a.status_active=1 and a.is_deleted=0 and a.is_sales = 1 and a.qnty>0
	order by a.job_no";
	//echo $sql_allo;
	$sql_allo_rslt = sql_select($sql_allo);
	$qty_arr = array();
	foreach($sql_allo_rslt as $row)
	{
		$qty_arr[$row[csf('job_no')]][$row[csf('item_id')]]['qty'] += $row[csf('qnty')];
	}
	$tbl_width = 2200;
	?>
	<style type="text/css">
		<!--table tr th, table tr td{word-wrap: break-word;word-break: break-all;}-->
	</style>

	<div>
    	<?
        if(empty($result_allocation))
		{
			echo get_empty_data_msg();
			die;
        }
        ?>
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" style="font:'Arial Narrow'; font-size:14px" rules="all" id="table_header_1">
			<thead> 
				<tr class="form_caption" style="border:none;">
					<td colspan="26" align="center" style="border:none;font-size:16px; font-weight:bold">TNA Wise Yarn Allocation Report Sales</td>
				</tr>
				<tr style="border:none;">
					<td colspan="26" align="center" style="border:none; font-size:14px;">
						Company Name : <? echo $companyArr[str_replace("'", "", $cbo_company_name)]; ?>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="26" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? if ($from_date != "" && $to_date != "") echo "From " . change_date_format($from_date, 'dd-mm-yyyy') . " To " . change_date_format($to_date, 'dd-mm-yyyy') . ""; ?>
					</td>
				</tr>
				<tr>
					<th width="30">SL</th>
					<th width="120">Company</th>
					<th width="120">Sales Order No</th>
					<th width="80">Booking No.</th>
					<th width="70">Booking Date</th>
					<th width="120">Customer</th>
					<th width="120">Cust. Buyer</th>
					<th width="70">Delivery Start Date</th>
					<th width="70">Delivery End Date</th>
					<th width="70">Yarn Delivery Start Date</th>
					<th width="70">Yarn Delivery End Date</th>
					<th width="80">Fabric Booking Qty</th>
					<th width="80">Req. Yarn Qty</th>
					<th width="70">Product ID</th>
					<th width="60">Count</th>
					<th width="120">Composition</th>
					<th width="100">Type</th>
					<th width="100">Color</th>
					<th width="80">Lot</th>
					<th width="120">Supplier</th>
					<th width="70">Allocaiton Date</th>
					<th width="80">Allocated Qty</th>
					<th width="80">Issue Qty</th>
					<th width="80">Issue Rtn Qty</th>
					<th width="80">Balance</th>
					<th width="60">Age Up To All/Date</th>
				</tr>
			</thead>
		</table>
		<div style="width:<? echo $tbl_width+18; ?>px; overflow-y:scroll; max-height:250px" id="scroll_body">  
			<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" style="font:'Arial Narrow';" cellspacing="0" class="rpt_table" rules="all" id="table_body">
				<?
				$i = 1;
				$balance = 0;
				$grand_total_allocate_qty = 0;
				$grand_total_issue_qty = 0;
				$grand_total_issue_rtn_qty = 0;
				$grand_total_balance = 0;
				$prodStock = array();

				foreach ($result_allocation as $row) 
				{
					/*if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";*/
					$bgcolor = "#FFFFFF";
						
					$prod_id = $row[csf("item_id")];
					$ageOfDays = datediff("d", $row[csf("insert_date")], date("Y-m-d"));
					$row[csf("allocate_qty")] = $qty_arr[$row[csf('job_no')]][$row[csf('item_id')]]['qty'];

					$compositionDetails = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")] . "%\n";
					if ($row[csf("yarn_comp_type2nd")] != 0)
					{
						$compositionDetails .= $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")] . "%";
					}
					
					//for supplier	
					if($row[csf('is_within_group')] == 1)
					{
						$supplier = $companyArr[$row[csf('supplier_id')]];
					}
					else
					{
						$supplier = $supplierArr[$row[csf('supplier_id')]];							
					}
					
					//for fso information	
					$customer = $fso_data[$row[csf('job_no')]]['BUYER_ID'];	
					$cust_buyer = $fso_data[$row[csf('job_no')]]['CUSTOMER_BUYER'];
					$booking_date = $fso_data[$row[csf('job_no')]]['BOOKING_DATE'];
					$dlv_start_date = $fso_data[$row[csf('job_no')]]['DELIVERY_START_DATE'];
					$dlv_end_date = $fso_data[$row[csf('job_no')]]['DELIVERY_END_DATE'];
					$booking_qty = $fso_data[$row[csf('job_no')]]['BOOKING_QTY'];
					$req_qty = $fso_data[$row[csf('job_no')]]['REQ_QTY'];
					$fso_id = $fso_data[$row[csf('job_no')]]['ID'];
					
					//for issue information
					$issue_qty = $issue_data[$fso_id][$row[csf('item_id')]]['issue_qty'];
					$issue_rtn_qty = $issue_rtn_data[$fso_id][$row[csf('item_id')]]['issue_rtn_qty'];
					$balance = (decimal_format($row[csf("allocate_qty")], '1', '') + decimal_format($issue_rtn_qty, '1', '')) - (decimal_format($issue_qty, '1', ''));

					//echo (30+120+80+80+70+120+120+280+160+70+60+520+70+380);
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" valign="middle">
					<?
					if($check_row_span[$row[csf('job_no')]] != $row[csf('job_no')])
					{
						$i++;
						$check_row_span[$row[csf('job_no')]] = $row[csf('job_no')];
						?>
						<td width="30" align="center" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><? echo $i; ?></td>
						<td width="120" align="center" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><p><? echo $companyArr[$row[csf("company_id")]]; ?>&nbsp;</p></td>
						<td width="120" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>">
							<div style="word-wrap:break-word;text-align: center;">
								<? echo $row[csf("job_no")];?>
							</div> 
						</td>
						<td width="80" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="70" align="center" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><? echo change_date_format($booking_date);?></td>
						<td width="120" align="center" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><p><? echo $customer; ?></p></td>
						<td width="120" align="center" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><p><? echo $cust_buyer; ?></p></td>
						<td width="70" align="center" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><? echo change_date_format($dlv_start_date);?></td>
						<td width="70" align="center" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><? echo change_date_format($dlv_end_date);?></td>
						<td width="70" align="center" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><? echo change_date_format($row[csf('task_start_date')]);?></td>
						<td width="70" align="center" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><? echo change_date_format($row[csf('task_finish_date')]);?></td>
						<td width="80" align="right" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><p><? echo decimal_format($booking_qty, '1', ','); ?></p></td>
						<td width="80" align="right" rowspan="<? echo $row_span[$row[csf('job_no')]]; ?>"><p><? echo decimal_format($req_qty, '1', ','); ?></p></td>
					<?
					$grand_total_booking_qty += decimal_format($booking_qty, '1', '');
					$grand_total_req_qty += decimal_format($req_qty, '1', '');
					}
					?>
						<td width="70" align="center"><? echo $row[csf("item_id")]; ?></td>
						<td width="60" align="center"><p><? echo $yarn_count_arr[$row[csf("yarn_count_id")]]; ?></p></td>
						<td width="120"><p><? echo $compositionDetails; ?>&nbsp;</p></td>
						<td width="100" align="center"><p><? echo $yarn_type[$row[csf("yarn_type")]]; ?>&nbsp;</p></td>
						<td width="100"><? echo $color_name_arr[$row[csf("color")]]; ?></td>
						<td width="80"><? echo $row[csf("lot")]; ?></td>
						<td width="120"><p><? echo $supplier; ?></p></td>
						<td width="70" align="center"><? echo change_date_format($row[csf("allocation_date")]); ?></td>
						<td width="80" align="right"><? echo decimal_format($row[csf("allocate_qty")], '1', ','); ?></td>
						<td width="80" align="right"><? echo decimal_format($issue_qty, '1', ','); ?></td>
						<td width="80" align="right"><? echo decimal_format($issue_rtn_qty, '1', ','); ?></td>
						<td width="80" align="right"><? echo decimal_format($balance, '1', ','); ?></td>
						<td width="60" align="center"><? echo $ageOfDays; ?></td>
					</tr>
					<?
					/*$sub_total_allocated_qty += decimal_format($row[csf("allocate_qty")], '1', '');
					$sub_total_issue_qty += decimal_format($issue_qty, '1', '');
					$sub_total_issue_rtn_qty += decimal_format($issue_rtn_qty, '1', '');
					$sub_total_balance_qty += decimal_format($balance, '1', '');*/

					$grand_total_allocate_qty += decimal_format($row[csf("allocate_qty")], '1', '');
					$grand_total_issue_qty += decimal_format($issue_qty, '1', '');
					$grand_total_issue_rtn_qty += decimal_format($issue_rtn_qty, '1', '');
					$grand_total_balance += decimal_format($balance, '1', '');
				}
				?>
			</table>
		</div>
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_footer">			
			<tr class="tbl_bottom">
				<td width="30">&nbsp;</td> 
				<td width="120">&nbsp;</td>  
				<td width="120">&nbsp;</td>  
				<td width="80">&nbsp;</td>  
				<td width="70">&nbsp;</td>  
				<td width="120">&nbsp;</td>  
				<td width="120">&nbsp;</td>  
				<td width="70">&nbsp;</td>
				<td width="70">&nbsp;</td>  
				<td width="70">&nbsp;</td>  
				<td width="70"align="right">Grand Total</td>  
				<td width="80" style="word-break: break-all; text-align:right;" id="value_total_booking_qty"><? echo decimal_format($grand_total_booking_qty, '1', ''); ?></td>  
				<td width="80" style="word-break: break-all; text-align:right;" id="value_total_req_qty"><? echo decimal_format($grand_total_req_qty, '1', ''); ?></td>
				<td width="70">&nbsp;</td>  
				<td width="60">&nbsp;</td>  
				<td width="120">&nbsp;</td>
				<td width="100">&nbsp;</td> 
				<td width="100">&nbsp;</td> 
				<td width="80">&nbsp;</td> 
				<td width="120">&nbsp;</td>  
				<td width="70">&nbsp;</td>
				<td width="80" style="word-break: break-all; text-align:right;" id="value_total_allocation_qty"><? echo decimal_format($grand_total_allocate_qty, '1', ''); ?></td>
				<td width="80" style="word-break: break-all;text-align:right;" id="value_total_issue_qty"><? echo decimal_format($grand_total_issue_qty, '1', ''); ?></td>
				<td width="80" style="word-break: break-all;text-align:right;" id="value_total_issue_return_qty"><? echo decimal_format($grand_total_issue_rtn_qty, '1', ''); ?></td>
				<td width="80" align="right" style="word-break: break-all;" id="value_total_balance"><? echo decimal_format($grand_total_balance, '1', ''); ?></td>
				<td width="60">&nbsp;</td>  
			</tr>
		</table>			
	</div>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}

	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}
?>
<style>
	a {
		color: #0254EB
	}
	a:visited {
		color: #0254EB
	}
	a.morelink {
		text-decoration:none;
		outline: none;
	}
	.morecontent span {
		display: none;
	}
	.comment {
		width: 400px;
		background-color: #f0f0f0;
		margin: 10px;
	}
</style>