<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

require_once('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']['user_id'];
if ($_SESSION['logic_erp']['user_id'] == "") {
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
$buyer_list = return_library_array("select id,short_name from lib_buyer", "id", "short_name");
//--------------------------------------------------------------------------------------------------------------------

function dd($da)
{
	echo '<br><pre>';
	var_dump($da);
	echo '</pre><br>';
	die;
}

if ($action == "load_drop_down_location") {
	echo create_drop_down("cbo_buyer_id", 140, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.id=b.buyer_id and   b.tag_company='$data' and a.status_active=1 and a.is_deleted=0 order by a.short_name", "id,buyer_name", 1, "-- Select Buyer --", 0, "");
	
	exit();
}

if ($action == "load_drop_down_party_name") {
	$data = explode('_', $data);
	echo create_drop_down("cbo_party_name", 130, "select a.id,a.SUPPLIER_NAME from lib_supplier a, lib_supplier_tag_company b where a.id=b.SUPPLIER_ID and b.tag_company='$data[0]' and a.status_active=1 and a.is_deleted=0 order by a.short_name", "id,SUPPLIER_NAME", 1, "-- Select Party --", $selected, "", "", "", "", "", "", 5);

	exit();
}

if ($action == "batch_no_popup") {
	echo load_html_head_contents("PO Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script type="text/javascript">
		function js_set_value(id) {
			document.getElementById('selected_id').value = id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" />
<?
	if ($db_type == 0) $group_by_cond = "GROUP BY a.batch_no, a.extention_no";
	else if ($db_type == 2) $group_by_cond = " GROUP BY a.id, a.batch_no, a.extention_no, a.batch_no, a.booking_no, a.color_id, a.batch_weight order by a.batch_no, a.extention_no desc";

	$sql = "select a.id, a.batch_no, a.extention_no, a.booking_no, a.color_id, a.batch_weight from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and a.company_id=$company_name and a.status_active=1 and a.is_deleted=0 $group_by_cond ";

	$arr = array(2 => $color_library);

	echo  create_list_view("list_view", "Batch no,Ext,Color,Booking no,Batch weight ", "100,70,100,100,100", "520", "350", 0, $sql, "js_set_value", "id,batch_no,extention_no", "", 1, "0,0,color_id,0,0", $arr, "batch_no,extention_no,color_id,booking_no,batch_weight", "subcon_batch_report_controller", 'setFilterGrid("list_view",-1);', '0,0,0,0,2');
	exit();
}


if ($action == "job_no_popup") {
	echo load_html_head_contents("Job Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
?>
	<script>
		function js_set_value(id) {
			document.getElementById('selected_id').value = id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" />
	<?
	if ($db_type == 0) $year_field = "year(insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YYYY') as year";
	else $year_field = "";

	if ($db_type == 0) $year_field_by = " and year(a.insert_date)";
	else if ($db_type == 2) $year_field_by = "  and to_char(insert_date,'YYYY')";

	$year_job = str_replace("'", "", $year);
	if (trim($year) != 0) $year_cond = " $year_field_by=$year_job";
	else $year_cond = "";
	if ($cbo_buyer_name == 0) $buyer_cond = "";
	else $buyer_cond = " and a.party_id='$cbo_buyer_name'";

	$buyer = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");

	// $sql = "select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id, a.subcon_job from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_cond order by a.id desc";

	// $sql = "select a.party_id, a.subcon_job, a.job_no_prefix_num, $year_field, b.order_no, b.cust_style_ref, b.main_process_id, a.subcon_job from  subcon_ord_mst a, subcon_ord_dtls b where a.subcon_job=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.company_id='$company_id' $buyer_cond $year_cond order by a.id desc";

	$sql = "select id, JOB_NO_PREFIX_NUM, $year_field, job_no, BUYER_NAME , STYLE_REF_NO from wo_po_details_master where is_deleted=0 and status_active=1 and company_name='$company_id' $year_cond order by id desc ";



	// echo $sql;

	?>
	<table width="420" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="80">Job no</th>
			<th width="70">Year</th>
			<th width="130">Buyer</th>
			<th width="110">Style</th>
			
		</thead>
	</table>
	<div style="max-height:340px; overflow:auto;">
		<table id="table_body2" width="420" border="1" rules="all" class="rpt_table">
			<? $data_array = sql_select($sql);
			$i = 1;
			foreach ($data_array as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('job_no_prefix_num')]; ?>')" style="cursor:pointer;">
					<td width="30"><? echo $i; ?></td>
					<td align="center" width="80"><? echo $row[csf('job_no_prefix_num')]; ?></td>
					<td align="center" width="70"><? echo $row[csf('year')]; ?></td>
					<td width="130"><? echo $buyer[$row[csf('BUYER_NAME')]]; ?></td>
					<td width="110">
						<p><? echo $row[csf('STYLE_REF_NO')]; ?></p>
					</td>
					
				</tr>
			<? $i++;
			}
			?>
		</table>
	</div>
	<script>
		setFilterGrid("table_body2", -1);
	</script>
<?
	disconnect($con);
	exit();
}

if ($action == "batchextensionpopup") {
	echo load_html_head_contents("PO Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

?>
	<script type="text/javascript">
		function js_set_value(id) {
			document.getElementById('selected_id').value = id;
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="selected_id" name="selected_id" />
<?
	$buyer = str_replace("'", "", $buyer_name);
	$year = str_replace("'", "", $year);
	$buyer = str_replace("'", "", $buyer_name);
	$batch_number = str_replace("'", "", $batch_number_show);
	if ($db_type == 0) $year_field_by = "and YEAR(a.insert_date)";
	else if ($db_type == 2) $year_field_by = " and to_char(a.insert_date,'YYYY')";
	else $year_field_by = "";

	if ($company_name == 0) $company = "";
	else $company = " and a.company_id=$company_name";
	if ($batch_number == 0) $batch_no = "";
	else $batch_no = " and a.batch_no=$batch_number";

	if (trim($year) != 0) $year_cond = " $year_field_by=$year";
	else $year_cond = "";

	//echo $buyer;die;
	if ($buyer == 0) $buyername = "";
	else $buyername = " and b.buyer_name=$buyer";

	$sql = "select a.id,a.batch_no,a.extention_no,a.batch_for,a.booking_no,a.color_id,a.batch_weight from pro_batch_create_mst a,pro_batch_create_dtls b where a.id=b.mst_id and a.is_deleted=0 $company $batch_no ";

	$arr = array(2 => $color_library);

	echo  create_list_view("list_view", "Batch no,Extention No,Color,Booking no, Batch for,Batch weight ", "100,70,100,100,100,170", "620", "350", 0, $sql, "js_set_value", "extention_no,extention_no", "", 1, "0,0,color_id,0,0,0", $arr, "batch_no,extention_no,color_id,booking_no,batch_for,batch_weight", "employee_info_controller", 'setFilterGrid("list_view",-1);', '0');
	exit();
} //batchnumbershow;




if ($action == "report_generate") {
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$supplier_arr = return_library_array("select id, SUPPLIER_NAME from lib_supplier", 'id', 'SUPPLIER_NAME');
	$company_library = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$grey_used_arr=return_library_array( "select dtls_id, used_qty from  pro_material_used_dtls",'dtls_id','used_qty');


	$exdata=explode('***',$data);
	$ex_bill_for=$exdata[2];
	$cbo_company_id = str_replace("'", "", $cbo_company_id);
	$cbo_buyer_id = str_replace("'", "", $cbo_buyer_id);
	$cbo_year = str_replace("'", "", $cbo_year);
	$txt_job_no = str_replace("'", "", $txt_job_no);
	$hid_job_id = str_replace("'", "", $hid_job_id);
	// $txt_style_nos = str_replace("'", "", $txt_style_no);
	$cbo_party_name = str_replace("'", "", $cbo_party_name);
	$txt_date_from = str_replace("'", "", $txt_date_from);
	$txt_date_to = str_replace("'", "", $txt_date_to);



	

	$search_cond='';
	$search_cond_non_order='';

	if ($cbo_buyer_id!="" && $cbo_buyer_id !='0'){
		$search_cond .=" and c.buyer_name in ($cbo_buyer_id) ";
		$search_cond_non_order='';
	}
	
	

	if ($cbo_party_name!="" && $cbo_party_name!='0'){
	$search_cond .=" and a.SUPPLIER_ID in ($cbo_party_name) ";
	$search_cond_non_order .=" and a.SUPPLIER_ID in ($cbo_party_name) ";

   }

  

	if ($txt_style_no=="''"){
		$search_cond.="";
	} else {
	   $search_cond .=" and c.STYLE_REF_NO in ($txt_style_no) ";
   }

   if ($txt_job_no==""){
	$search_cond.="";
	
	} else {
		$search_cond.=" and c.JOB_NO_PREFIX_NUM in ($txt_job_no) ";
	}

	if ($cbo_company_id==""){
		$search_cond.="";
		$search_cond_non_order.="";
	} else {
		$search_cond .=" and a.company_id in ($cbo_company_id) ";
		$search_cond_non_order.=" and a.company_id in ($cbo_company_id) ";
	}



	if($db_type==0)
	{
		
		if($cbo_year!=0){ 
		$year_cond=" and year(a.insert_date)='$cbo_year' "; 
		}
		else{ $year_cond="";
		}
	}
	else
	{
		
		if($cbo_year!=0) {
		$year_cond=" and to_char(a.insert_date,'YYYY')='$cbo_year' ";
		}  
		else{ $year_cond="";
		}
	}



   $search_cond .=$year_cond;
   $search_cond_non_order.=$year_cond;


   if ($txt_date_from && $txt_date_to) {
		if ($db_type == 0) {
			$date_from = change_date_format($txt_date_from, 'yyyy-mm-dd');
			$date_to = change_date_format($txt_date_to, 'yyyy-mm-dd');
			$dates_com = "and a.bill_date BETWEEN '$date_from' AND '$date_to'";
			$search_cond .= $dates_com;
			$search_cond_non_order .= $dates_com;
		}
		if ($db_type == 2) {
			$date_from = change_date_format($txt_date_from, '', '', 1);
			$date_to = change_date_format($txt_date_to, '', '', 1);
			$dates_com = "and a.bill_date BETWEEN '$date_from' AND '$date_to'";
			$search_cond .= $dates_com;
			$search_cond_non_order .= $dates_com;
		}
	}
	$i=1;


	// $sql= " select a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID ,
	// c.BUYER_NAME,c.STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID,b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, c.STYLE_REF_NO, b.RATE, d.YARN_DESCRIPTION from subcon_outbound_bill_mst a , subcon_outbound_bill_dtls b,WO_PO_DETAILS_MASTER c , wo_yarn_dyeing_dtls d 
	//   where a.id=b.mst_id and b.mst_id=a.id and b.job_no=c.job_no and d.MST_ID=b.WO_NUM_ID and d.job_no= b.job_no  AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  $search_cond GROUP BY a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , c.BUYER_NAME,c.STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID, b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, b.RATE, d.MST_ID,  d.YARN_DESCRIPTION order by a.id ";

	if(str_replace("'", "", $txt_style_no)!="" || $cbo_buyer_id >0 || $txt_job_no!="" ){

		// $sql = "select a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , c.BUYER_NAME,c.STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID,b.COLOR_ID , b.receive_qty, b.amount,b.currency_id , b.RATE, d.YARN_DESCRIPTION  from subcon_outbound_bill_mst a , subcon_outbound_bill_dtls b,WO_PO_DETAILS_MASTER c , wo_yarn_dyeing_dtls d where a.id=b.mst_id and b.mst_id=a.id and b.job_no=c.job_no and d.MST_ID=b.WO_NUM_ID and d.job_no= b.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0   $search_cond and a.BILL_FOR =2 GROUP BY a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , c.BUYER_NAME,c.STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID, b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, b.RATE, d.MST_ID, d.YARN_DESCRIPTION";

		$sql= "select a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , c.BUYER_NAME,c.STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID,b.COLOR_ID , b.receive_qty, b.amount,b.currency_id , b.RATE, d.YARN_DESCRIPTION 
		from subcon_outbound_bill_mst a , subcon_outbound_bill_dtls b,WO_PO_DETAILS_MASTER c , wo_yarn_dyeing_dtls d 
		where a.id=b.mst_id and b.mst_id=a.id and b.job_no=c.job_no and d.MST_ID=b.WO_NUM_ID and d.job_no= b.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 $search_cond and a.BILL_FOR =2 GROUP BY a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE,
		b.CHALLAN_NO, a.PARTY_ID , c.BUYER_NAME,c.STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID, b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, b.RATE, d.MST_ID, d.YARN_DESCRIPTION ";
	}
	else{

	//   $sql = "select a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , c.BUYER_NAME,c.STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID,b.COLOR_ID , b.receive_qty, b.amount,b.currency_id , b.RATE, d.YARN_DESCRIPTION  from subcon_outbound_bill_mst a , subcon_outbound_bill_dtls b,WO_PO_DETAILS_MASTER c , wo_yarn_dyeing_dtls d where a.id=b.mst_id and b.mst_id=a.id and b.job_no=c.job_no and d.MST_ID=b.WO_NUM_ID and d.job_no= b.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0  $search_cond and a.BILL_FOR =2 GROUP BY a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , c.BUYER_NAME,c.STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID, b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, b.RATE, d.MST_ID, d.YARN_DESCRIPTION  union all select a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, null as JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , null as BUYER_NAME,null as STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID,b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, b.RATE, d.YARN_DESCRIPTION from subcon_outbound_bill_mst a , subcon_outbound_bill_dtls b, wo_yarn_dyeing_dtls d where a.id=b.mst_id and b.mst_id=a.id and d.ID=b.WO_NUM_ID AND a.status_active = 1  AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 $search_cond_non_order and a.BILL_FOR =3 GROUP BY a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , a.SUPPLIER_ID, b.WO_NUM_ID, b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, b.RATE, d.MST_ID, d.YARN_DESCRIPTION ";

	$sql= " select a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , c.BUYER_NAME,c.STYLE_REF_NO,
	a.SUPPLIER_ID, b.WO_NUM_ID,b.COLOR_ID , b.receive_qty, b.amount,b.currency_id , b.RATE, d.YARN_DESCRIPTION 
	from subcon_outbound_bill_mst a , subcon_outbound_bill_dtls b,WO_PO_DETAILS_MASTER c , wo_yarn_dyeing_dtls d 
	where a.id=b.mst_id and b.mst_id=a.id and b.job_no=c.job_no and d.MST_ID=b.WO_NUM_ID and d.job_no= b.job_no AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0  $search_cond and a.BILL_FOR =2 GROUP BY a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , c.BUYER_NAME,c.STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID, b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, b.RATE, d.MST_ID, d.YARN_DESCRIPTION 
	union all 
	select a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, null as JOB_NO,b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , null as BUYER_NAME,null as STYLE_REF_NO, a.SUPPLIER_ID, b.WO_NUM_ID,b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, b.RATE, d.YARN_DESCRIPTION from subcon_outbound_bill_mst a , subcon_outbound_bill_dtls b, wo_yarn_dyeing_dtls d where a.id=b.mst_id and b.mst_id=a.id and d.ID=b.WO_NUM_ID AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND d.status_active = 1 AND d.is_deleted = 0 $search_cond_non_order
	and a.BILL_FOR =3 GROUP BY a.BILL_FOR, a.id, a.company_id, a.bill_date, a.BILL_NO, b.mst_id, b.MRR_NO, b.RECEIVE_DATE, b.CHALLAN_NO, a.PARTY_ID , a.SUPPLIER_ID, b.WO_NUM_ID, b.COLOR_ID , b.receive_qty, b.amount,b.currency_id, b.RATE, d.MST_ID, d.YARN_DESCRIPTION ";

	}


	


	//  echo $sql;


	$dataArray = sql_select($sql);

?>
	<div>
		<fieldset style="width:1400px;">
			<div align="center"> <strong> <? echo $company_library[$company]; ?> </strong><br><strong> Job/Order Wise Yarn Dyeing Bill Report </strong> </div>
			<table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<tr>
						<th width="30">SL</th>
						<th width="100">Bill No</th>
						<th width="100">MRR No.</th>
						<th width="80">Date</th>
						<th width="80">Challan No.</th>
						<th width="100">Party</th>
						<th width="120">Buyer Name</th>
						<th width="70">Style Name</th>
						<th width="100">Job No</th>
						<th width="130">Work Order No</th>
						<th width="60">Process Name</th>
						<th width="100">Yarn Description</th>
						<th width="60">Yarn Color</th>
						<th width="70">Bill Qty</th>
						<th width="50">Rate/Kg</th>
						<td width="80" align="center">Bill Value</td>
						<th>Currency</th>
					</tr>
				</thead>
			</table>
			<div style=" max-height:350px; width:1400px; overflow-y:scroll;" id="scroll_body">
				<table class="rpt_table" id="table_body" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tbody>
						<?

						
						$batch_chk_arr = array();
						$total_receive_qty=0;
						$total_amount=0;
						foreach ($dataArray as $key => $row) {
							if ($key % 2 == 0) $bgcolor = "#E9F3FF";
							else $bgcolor = "#FFFFFF";

						?>
							<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)" style="cursor:pointer;">

								<td width="30">
									<?
									echo $key+1;
									?>
								</td>
								<td align="center" width="100">
									<p>
										<?
										echo $row[csf('bill_no')];
										?>
									</p>
								</td>
								<td align="center" width="100">
									<p>
										<?
										echo $row[csf('mrr_no')];
										?>
									</p>
								</td>
								<td align="center" width="80">
									<p>
										<?
										echo $row[csf('bill_date')];
										?>
									</p>
								</td>
								<td width="80">
									<p>
										<?
										echo $row[csf('challan_no')];
										?>
									</p>
								</td>
								<td width="100">
									<p>
										<?
										echo $supplier_arr[$row[csf('SUPPLIER_ID')]];
										?>
									</p>
								</td>

								<td width="120" >
									<p>
										<?
										
										echo $buyer_arr[$row[csf('BUYER_NAME')]];
										
										?>
									</p>
								</td>
								<td width="70" title="">
									<p>
										<?
										echo $row[csf('STYLE_REF_NO')];
										?>
									</p>
								</td>

								<td width="100" >
									<p>
										<?
										echo $row[csf('JOB_NO')];
										?>
									</p>
								</td>
								<td width="130" title="">
									<p>
										<?
										$ydw_no = return_field_value("ydw_no", "wo_yarn_dyeing_mst", " status_active=1 and id=" . $row[csf('wo_num_id')] . "", "ydw_no");
										echo $ydw_no;
										?>
									</p>
								</td>
								<td align="center" width="60" title="">
									<p>
										<?
											echo "Yarn Dyeing";
										?>
									</p>
								</td>
								<td align="center" width="100" title=">">
									<p>
										<?
										echo $row[csf('YARN_DESCRIPTION')];
										?>
									</p>
								</td>
								<td align="left" width="60" title="">
									<p>
										<?
										echo  $color_arr[$row[csf('COLOR_ID')]];
										?>
									</p>
								</td>
								<td align="right" width="70" title="">
									<?
									echo number_format($row[csf('receive_qty')],2);
									$total_receive_qty += $row[csf('receive_qty')];
									?>
								</td>
								<td align="right" width="50" title="">
									<?
									echo $row[csf('rate')];

									?>
								</td>
								<td align="right" width="80" title="">
									<?
										echo number_format($row[csf('amount')],2);
										$total_amount +=$row[csf('amount')];
									?>
								</td>
								<td>
									<?
									echo $currency[$row[csf('currency_id')]];
									?>

								</td>
							</tr>
						<?


						}

						?>

					</tbody>
				</table>
				<table class="rpt_table" width="1400" cellpadding="0" cellspacing="0" border="1" rules="all">
					<tfoot>
						<tr>
							<th width="30">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="120">&nbsp;</th>
							<th width="70">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="130">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="100">&nbsp;</th>
							<th width="60">&nbsp;</th>
							<th width="70"><? echo $btq; ?></th>
							<th width="50">&nbsp;</th>
							<th width="80">&nbsp;</th>
							<th>&nbsp;</th>
						</tr>
						<tr>
							<td colspan="13" align="center" style="border:none;"><b>Total</b></td>

							<td align="left">&nbsp;
								<? echo number_format($total_receive_qty,2); ?>
							</td>
							<td align="left">&nbsp;
								<? echo ""; ?>
							</td>
							<td align="left">&nbsp;
								<? echo  number_format($total_amount,2); ?>
							</td>
							<td align="left">&nbsp;
								<? echo ""; ?>
							</td>



						</tr>

					</tfoot>
				</table>
			</div>
		</fieldset>
	</div>
<?
	exit();
} //BatchReport
?>