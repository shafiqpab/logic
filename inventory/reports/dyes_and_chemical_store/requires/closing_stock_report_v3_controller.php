<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if ($action=="load_drop_down_store")
{
	//extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$item_cat_id=$data[1];
	$store_wish=$data[2];
	// echo $item_cat_id."**".$store_wish;
	if($store_wish==1)
	{
		if($item_cat_id>0){
		$sql= "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id in($company_id) and b.category_type in($item_cat_id) group by a.id, a.store_name";
		// echo $sql;
		}else{
		$sql= "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  group by a.id, a.store_name";
		// echo $sql;

		}
     	echo create_drop_down( "cbo_store_name", 120, $sql,"id,store_name", 1, "--Select--", 0, "",0 );
	}
	else{
	    echo create_drop_down( "cbo_store_name", 120, $blank,"id,store_name", 1, "--Select--", 0, "",0 );
	}
	exit();
}

if ($action=="item_account_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
	?>
    <script>
	 var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

	 function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}

	function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

	function js_set_value(id)
	{
		var str=id.split("_");
		toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
		var strdt=str[2];
		str=str[1];

		if( jQuery.inArray(  str , selected_id ) == -1 ) {
			selected_id.push( str );
			selected_name.push( strdt );
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == str  ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i,1 );
		}
		var id = '';
		var ddd='';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			ddd += selected_name[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		ddd = ddd.substr( 0, ddd.length - 1 );
		$('#item_account_id').val( id );
		$('#item_account_val').val( ddd );
	}
	</script>
     <input type="hidden" id="item_account_id" />
     <input type="hidden" id="item_account_val" />
    <?
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	if ($data[2]==0) $item_name =""; else $item_name =" and item_group_id in($data[2])";

	$sql="SELECT id, item_account, item_category_id, item_group_id, item_description, supplier_id, sub_group_name from product_details_master where company_id in($data[0]) and item_category_id in($data[1]) $item_name and status_active=1 and is_deleted=0";
	//echo $sql;
	$arr=array(1=>$item_category,2=>$itemgroupArr,5=>$supplierArr);
	echo  create_list_view("list_view", "Item Account,Item Category,Item Group,Item Sub Group,Item Description,Supplier,Product ID", "70,120,120,100,170,100","780","400",0, $sql , "js_set_value", "id,item_description", "", 0, "0,item_category_id,item_group_id,0,0,supplier_id,0", $arr , "item_account,item_category_id,item_group_id,sub_group_name,item_description,supplier_id,id", "",'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0','',1) ;
	exit();
}

if ($action=="item_group_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	//print_r ($data);
    ?>
    <script>
		var selected_id = new Array, selected_name = new Array(); 
		selected_attach_id = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById( 'list_view' ).rows.length;
			tbl_row_count = tbl_row_count - 1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value( functionParam );
				
			}
		}

		function toggle( x, origColor ) {
				var newColor = 'yellow';
				if ( x.style ) {
					x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
				}
			}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#item_name_id').val( id );
			$('#item_name_val').val( ddd );
		}
	</script>
    <input type="hidden" id="item_name_id"/>
    <input type="hidden" id="item_name_val"/>
    <?
	if ($data[1]==0) $item_category =""; else $item_category =" and item_category in($data[1])";
	// $item_category;
	$sql="SELECT id,item_name from lib_item_group where status_active=1 and is_deleted=0 $item_category"; //id=$data[1] and

	echo  create_list_view("list_view", "Item Name", "350","480","330",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name", "periodical_purchase_report_controller",'setFilterGrid("list_view",-1);','0','',1);
	exit();
}

if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$report_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$item_account_id=str_replace("'","",$item_account_id);
	$item_group_id=str_replace("'","",$item_group_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_store_wise=str_replace("'","",$cbo_store_wise);
	$cbo_compliance=str_replace("'","",$cbo_compliance);
	
	//echo $cbo_store_name."=".test;die;
	

	if($report_type==3)
	{
		$company_cond="";
		if ($cbo_company_name !="") $company_cond =" and company_id in($cbo_company_name)";
		if ($cbo_item_category_id!="") $category_cond=" and a.item_category_id in($cbo_item_category_id)";  else $category_cond=" and a.item_category_id in(5,6,7,23)";
		
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
		

		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
		//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
		$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");

		$sql_loan_cond="";
		if ($cbo_company_name !="") $sql_loan_cond =" and a.company_id in($cbo_company_name)";
		if ($cbo_item_category_id=="") $sql_loan_cond.=" and a.item_category in(5,6,7,23)"; else $sql_loan_cond.=" and a.item_category in($cbo_item_category_id)";
		if ($item_account_id>0) $sql_loan_cond.=" and a.prod_id in ($item_account_id)";
		//if ($cbo_store_name>0)  $sql_loan_cond.=" and a.store_id=$cbo_store_name ";
		if ($cbo_store_name != "")  $sql_loan_cond.=" and a.store_id in($cbo_store_name) ";

		if($from_date!="" && $to_date!="")
		{
			$sql_loan="Select a.prod_id as prod_id, sum(a.cons_quantity) as cons_quantity, 1 as type
			from inv_transaction a, inv_receive_master b
			where a.mst_id=b.id and a.transaction_type=1 and b.receive_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond  group by a.prod_id
			union all
			Select a.prod_id as prod_id, sum(a.cons_quantity) as cons_quantity, 2 as type
			from inv_transaction a, inv_issue_master b
			where a.mst_id=b.id and a.transaction_type=2 and b.issue_purpose=5 and a.transaction_date between '".$from_date."' and '".$to_date."' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_loan_cond  group by a.prod_id
			order by prod_id ASC";
		}
		//echo $sql_loan;die;
		$sql_loan_result=sql_select($sql_loan);
		$loan_data=array();
		foreach($sql_loan_result as $row)
		{
			if($row[csf("type")]==1)
			{
				$loan_data[$row[csf("prod_id")]]["loan_rcv_qnty"]=$row[csf("cons_quantity")];
			}
			else
			{
				$loan_data[$row[csf("prod_id")]]["loan_issue_qnty"]=$row[csf("cons_quantity")];
			}
		}
		//var_dump($loan_data);die;
		unset($sql_loan_result);
		$search_cond="";
		if ($cbo_company_name !="") $search_cond =" and a.company_id in($cbo_company_name)";
		if ($cbo_item_category_id == "") $search_cond.=" and b.item_category_id in(5,6,7,23)"; else $search_cond.=" and b.item_category_id in($cbo_item_category_id)";
		if ($cbo_item_category_id == "") $search_cond.=" and a.item_category in(5,6,7,23)"; else $search_cond.=" and a.item_category in($cbo_item_category_id)";
		if ($item_account_id > 0) $search_cond.=" and b.id in ($item_account_id)";
		if ($item_group_id > 0) $search_cond.=" and b.item_group_id in($item_group_id)";
		//if ($cbo_store_name > 0) $search_cond.=" and a.store_id=$cbo_store_name ";
		
		if ($cbo_store_name != "")  $search_cond.=" and a.store_id in($cbo_store_name) ";
		
		
		$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS  from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond order by b.id ASC";
		
		//echo $trans_sql;die;
		$trnasactionData = sql_select($trans_sql);
		//echo count($trnasactionData).jahid;die;
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			if($row_p["TRANSACTION_TYPE"]==1)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['purchase']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['purchase_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==2)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['issue_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==3)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['receive_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['receive_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==4)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['issue_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['issue_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==5)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['item_transfer_receive_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==6)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}

				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['item_transfer_issue_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			if($batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=="" && $row_p["LOT_NO"] !="")
			{
				$batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=$row_p["LOT_NO"];
				$data_array[$row_p["PROD_ID"]]['lot_no'].=$row_p["LOT_NO"].",";
			}
			
			if($row_p["TRANSACTION_TYPE"]==1 &&($row_p["RECEIVE_BASIS"]==1 || $row_p["RECEIVE_BASIS"]==2))
			{
				$data_array[$row_p["PROD_ID"]]['rcv_total_wo']+=$row_p["CONS_QUANTITY"];
			}
			
			if(strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date))
			{
				if($row_p["TRANSACTION_TYPE"]==1 || $row_p["TRANSACTION_TYPE"]==4 || $row_p["TRANSACTION_TYPE"]==5)
				{
					$data_array[$row_p["PROD_ID"]]['tot_stock_qnty']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['tot_stock_amount']+=$row_p["CONS_AMOUNT"];
				}
				else
				{
					$data_array[$row_p["PROD_ID"]]['tot_stock_qnty']-=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['tot_stock_amount']-=$row_p["CONS_AMOUNT"];
				}
			}
		}
		unset($trnasactionData);
		 
		//echo "<pre>";print_r($data_array);die;
		$returnRes_date="select prod_id as PROD_ID, min(transaction_date) as min_date, max(transaction_date) as max_date from inv_transaction where is_deleted=0 and status_active=1 and item_category in(5,6,7,23) group by prod_id";
		//echo $returnRes_date;die;
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row["PROD_ID"]]['min_date']=$row["MIN_DATE"];
			$date_array[$row["PROD_ID"]]['max_date']=$row["MAX_DATE"];
		}


		$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and b.item_category_id in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

		$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
		$i=1;
		//echo "<pre>";print_r($data_array);die;
		ob_start();
		?>

        <p style="color:#FF0000; font-size:16px; font-weight:bold;">Value Will Not Match Store Level.</p>
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:2750px">
			<table width="2850px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="12" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? $com_id_arr=explode(",",$cbo_company_name); if(count($com_id_arr)>1) echo "JK Group"; else echo $companyArr[$cbo_company_name]; ?></b>
                           <br />
                           South Dariapur, Savar, Dhaka-1340
                           <br />
                           Consumption Summary Of Dyes & Chemicals Report
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="2850" rules="all" id="rpt_table_header">
				<thead>
					<tr>
						<th rowspan="2" width="50">SL</th>
                        <th rowspan="2" width="110">Company</th>
						<th colspan="8">Description</th>
						<th rowspan="2" width="100"> Opneing Rate</th>
                        <th rowspan="2" width="100">Opening Stock</th>
                        <th rowspan="2" width="110">Opening Value</th>
						<th colspan="6">Receive</th>
						<th colspan="6">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2" width="80">Avg. Rate</th>
						<th rowspan="2" width="120">Stock Value</th>
						<th rowspan="2" width="60">DOH</th>
						<th rowspan="2" width="100">Pipe Line</th>
						<th rowspan="2">Lot</th>
					</tr>
					<tr>
						<th width="60">Prod. ID</th>
						<th width="70">Item Code</th>
						<th width="100">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="60">UOM</th>
						<th width="80">Purchase</th>
                        <th width="80">Loan receive</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue Return</th>
						<th width="100">Total Received</th>
                        <th width="100">Received Value</th>
						<th width="80">Issue</th>
                        <th width="80">Loan Issue</th>
						<th width="80">Transfer Out</th>
						<th width="80">Receive Return</th>
						<th width="80">Total Issue</th>
                        <th width="80">Issue Value</th>
					</tr>
				</thead>
			</table>
			<div style="width:2850px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="2830" rules="all" align="left">
			<?
				$pord_cond="";
				if ($cbo_item_category_id == "") $pord_cond.=" and b.item_category_id in(5,6,7,23)"; else $pord_cond.=" and b.item_category_id in($cbo_item_category_id)";
				if ($item_account_id > 0) $pord_cond.=" and b.id in ($item_account_id)";
				if ($item_group_id > 0) $pord_cond.=" and b.item_group_id in($item_group_id)";
			 	$sql="select b.id, b.item_code, b.item_category_id, b.item_group_id, b.unit_of_measure, b.item_description, b.sub_group_name, b.re_order_label, b.current_stock, b.avg_rate_per_unit, b.company_id 
				from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id in($cbo_company_name) $pord_cond
				order by b.id";
				$result = sql_select($sql);
				foreach($result as $row)
				{

					// if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
						$stylecolor='color:#A61000;';
					else
						$stylecolor='color:#000000;';
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));


					$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
					$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];

					$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
                    $openingBalanceValue=$openingRate=0;
                    //if($data_array[$row[csf("id")]]['rcv_total_opening'] >0)
                    //{
                         //$openingRate = $data_array[$row[csf("id")]]['rcv_total_opening_amt']/$data_array[$row[csf("id")]]['rcv_total_opening'];
                    //}
                    //$openingBalanceValue = $openingBalance*$openingRate;
					$openingBalanceValue =$data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
					if($openingBalance==0)
					{
						$openingBalanceValue=0;
						$openingRate =0;
					}
					else
					{
						if($openingBalanceValue!=0 && $openingBalance !=0) $openingRate = $openingBalanceValue/$openingBalance; else $openingRate =0;
					}
					

					//$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
					//$openingRate=$openingBalanceValue/$openingBalance;

					$purchase_qnty=$data_array[$row[csf("id")]]['purchase']-$loan_data[$row[csf("id")]]["loan_rcv_qnty"];
					$loan_receive=$loan_data[$row[csf("id")]]["loan_rcv_qnty"];
					$issue_qnty=$data_array[$row[csf("id")]]['issue']-$loan_data[$row[csf("id")]]["loan_issue_qnty"];
					$loan_issue=$loan_data[$row[csf("id")]]["loan_issue_qnty"];

					$totalReceive = $purchase_qnty+$loan_receive+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
					$totalReceiveAmt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$data_array[$row[csf("id")]]['item_transfer_receive_amt'];
					
					$totalIssue = $issue_qnty+$loan_issue+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;
					$totalIssueAmt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$data_array[$row[csf("id")]]['item_transfer_issue_amt'];
					$closingStock=$openingBalance+$totalReceive-$totalIssue;
					//$closingStock=$opening+$totalReceive-$totalIssue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{
						$avg_rate=$mrr_rate_arr[$row[csf("id")]];
						//$stockValue=$closingStock*$avg_rate;
						$stockValue=(($openingBalanceValue+$data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$data_array[$row[csf("id")]]['item_transfer_receive_amt'])-($data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$data_array[$row[csf("id")]]['item_transfer_issue_amt']));
						if($closingStock==0)
						{
							$closing_rate=0;
							$stockValue=0;
						}
						else
						{
							if($stockValue!=0 && $closingStock!=0) $closing_rate=$stockValue/$closingStock; else $closing_rate=0;
						}
						
						

						$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo'];
						if($pipeLine_qty<0) $pipeLine_qty=0;
						$re_order_label = $row[csf('re_order_label')];
                		if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
						if($value_with==1)
						{
							if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000)
							{

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
                                    <td width="110"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
									<td width="60" align="center"><? echo $row[csf("id")]; ?></td>
									<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
									<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
									<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
									<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
									<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" align="right"><p><? echo number_format($openingBalanceValue,2); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($purchase_qnty,4); $tot_purchase+=$purchase_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($loan_receive,4); $tot_loan_rcv+=$loan_receive; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_in_qty,4); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],4); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceiveAmt,2); $tot_purchase_amount+=$totalReceiveAmt;?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qnty,4); $tot_issue+=$issue_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($loan_issue,4); $tot_loan_issue+=$loan_issue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_out_qty,4); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssue,4); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssueAmt,2); $tot_issue_amount+=$totalIssueAmt;?></p></td>
									<td width="100" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($closing_rate,4); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,2); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
									<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,4); ?></p></a></td>
									<td><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?></p></td>
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
						else
						{
							if((number_format($openingBalance,3)>0.000) || (number_format($totalReceive,3)>0.000) || (number_format($totalIssue,3)>0.000) || (number_format($closingStock,3)>0.000) )
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
                                    <td width="110"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
									<td width="60" align="center"><? echo $row[csf("id")]; ?></td>                                    
									<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
									<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
									<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
									<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
									<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" align="right"><p><? echo number_format($openingBalanceValue,2); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($purchase_qnty,4); $tot_purchase+=$purchase_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($loan_receive,4); $tot_loan_rcv+=$loan_receive; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_in_qty,4); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],4); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceive,4); $tot_total_receive+=$totalReceive; ?></p></td>
                                    <td width="100" align="right" title="<?= $row[csf("id")];?>"><p><? echo number_format($totalReceiveAmt,2);$tot_purchase_amount+=$totalReceiveAmt;?></p></td>
                                    <td width="80" align="right"><p><? echo number_format($issue_qnty,4); $tot_issue+=$issue_qnty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($loan_issue,4); $tot_loan_issue+=$loan_issue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_out_qty,4); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],4); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssue,4); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssueAmt,2); $tot_issue_amount+=$totalIssueAmt;?></p></td>
									<td width="100" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($closing_rate,4); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" align="right"><p><? if(number_format($closingStock,3)>0) echo number_format($stockValue,2); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
									<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,4); ?></p></a></td>
									<td><p><? echo $data_array[$row[csf("id")]]['lot_no']; ?></p></td>
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
					}
				}
			?>
			</table>
			</div>
			<table width="2850" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
				<tfoot>
					<tr>
						<th width="50">&nbsp;</th>
						<th width="110">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">Total:</th>
                        <th width="100" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,4); ?></th>
                        <th width="110" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,2); ?></th>
						<th width="80" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,4); ?></th>
                        <th width="80" align="right" id="value_tot_loan_rcv"><? echo number_format($tot_loan_rcv,4); ?></th>
						<th width="80" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,4); ?></th>
						<th width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,4); ?></th>
						<th width="100" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,4); ?></th>
                        <th width="100"><? echo number_format($tot_purchase_amount,2);?></th>
                        <th width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,4); ?></th>
                        <th width="80" align="right" id="value_tot_loan_issue"><? echo number_format($tot_loan_issue,4); ?></th>
						<th width="80" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,4); ?></th>
						<th width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,4); ?></th>
						<th width="100" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,4); ?></th>
						<th width="100"><? echo number_format($tot_issue_amount,2);?></th>
                        <th width="100" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,4); ?></th>
						<th width="80">&nbsp;</th>
						<th width="120" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
						<th width="60">&nbsp;</th>
						<th width="100" align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,4); ?></th>
						<th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
        </div>
        <?

	}
	else if($report_type==5)
	{
		//add_date
		$PreviousMonthFromDate = date('d-m-Y', strtotime($from_date. ' -1 months'));
		$PreviousMonthToDate = date("t-m-Y", strtotime($PreviousMonthFromDate));
		//echo $PreviousMonthFromDate."=".$PreviousMonthToDate;die;
		$from_month=$from_date_arr[1]."-".$from_date_arr[2];
		$sql_lib_dye_qnty_prev="select COMPANY, P_QTY, FROM_DATE, TO_DATE from MONTHLY_DYEING_PRODUCTION_ENTRY where STATUS_ACTIVE=1 and COMPANY in($cbo_company_name) and to_char(FROM_DATE,'MM-YYYY')='".date('m-Y', strtotime($PreviousMonthFromDate))."'";
		//echo $sql_lib_dye_qnty_prev;die;
		$sql_lib_dye_qnty_prev_result=sql_select($sql_lib_dye_qnty_prev);
		$tot_dying_qnty_prev=0;
		foreach($sql_lib_dye_qnty_prev_result as $val)
		{
			$tot_dying_qnty_prev+=$val["P_QTY"]*1;
		}
		
		$from_date_arr=explode("-",$from_date);
		$from_month=$from_date_arr[1]."-".$from_date_arr[2];
		$sql_lib_dye_qnty="select COMPANY, P_QTY, FROM_DATE, TO_DATE from MONTHLY_DYEING_PRODUCTION_ENTRY where STATUS_ACTIVE=1 and COMPANY in($cbo_company_name) and to_char(FROM_DATE,'MM-YYYY')='$from_month'";
		//echo $sql_lib_dye_qnty;//die;
		$sql_lib_dye_qnty_result=sql_select($sql_lib_dye_qnty);
		$tot_dying_qnty=0;
		foreach($sql_lib_dye_qnty_result as $val)
		{
			$tot_dying_qnty+=$val["P_QTY"]*1;
		}
		//echo $tot_dying_qnty;die;
		$company_cond="";
		if ($cbo_company_name !="") $company_cond =" and company_id in($cbo_company_name)";
		if ($cbo_item_category_id !="") $category_cond=" and a.item_category_id in($cbo_item_category_id)";  else $category_cond=" and a.item_category_id in(5,6,7,23)";
		
		if ($cbo_company_name=="") $company_id =""; else $company_id =" and a.company_id in($cbo_company_name)";
		if ($cbo_company_name=="") $company_cond =""; else $company_cond =" and b.company_id in($cbo_company_name)";
		if ($cbo_item_category_id=="") $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id in($cbo_item_category_id)";
		if ($item_account_id==0) $item_account=""; else $item_account=" and b.id in ($item_account_id)";
		if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
		//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
		$store_id="";
		if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";

		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
		
		$PreviousMonthFromDate=change_date_format($PreviousMonthFromDate,'','',1);
		$PreviousMonthToDate=change_date_format($PreviousMonthToDate,'','',1);
		
		$search_cond="";
		
		$sql_prev="Select sum(a.cons_amount) as issue_amt
		from product_details_master b, inv_transaction a, INV_ISSUE_MASTER c 
		where a.prod_id=b.id and a.mst_id=c.id and a.transaction_type=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and c.ISSUE_PURPOSE not in(5,18,37,63,74) and a.order_id=0 and a.transaction_date between '".$PreviousMonthFromDate."' and '".$PreviousMonthToDate."' $company_id $group_id $prod_cond $store_id $item_account $search_cond";
		//echo $sql;die;
		
		$sql_prev_result = sql_select($sql_prev);
		$prev_month_issue_amt=0;
		foreach($sql_prev_result as $row)
		{
			$prev_month_issue_amt+=$row[csf("issue_amt")];
		}
		$prev_month_cost_per=0;
		if($prev_month_issue_amt!=0 && $tot_dying_qnty_prev!=0) $prev_month_cost_per=$prev_month_issue_amt/$tot_dying_qnty_prev;

		$sql="select b.id, b.item_code,b.re_order_label, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit,b.company_id from product_details_master b where b.status_active=1 and b.is_deleted=0 $company_cond $item_category_id $group_id $prod_cond $item_account $search_cond order by b.id";
		$result = sql_select($sql);
		foreach($result as $row)
		{

			$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
			$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));

			$issue_qty=$data_array[$row[csf("id")]]['issue'];
			$issue_qty_amt=$data_array[$row[csf("id")]]['issue_amt'];
			$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
			$transfer_out_qty_amt=$data_array[$row[csf("id")]]['item_transfer_issue_amt'];

			$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];
			$transfer_in_qty_amt=$data_array[$row[csf("id")]]['item_transfer_receive_amt'];
			$expire_date=$data_array[$row[csf("id")]]['expire_date'];

			$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
			$openingBalanceValue=$openingRate=0;
			$openingBalanceValue =  $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];

			$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;
			$totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;

			$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;
			$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['loan_issue_amt']+$data_array[$row[csf("id")]]['aop_issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

			$closingStock=$openingBalance+$totalReceive-$totalIssue;
			$closingStock_amt=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;

			//$closingStock=$opening+$totalReceive-$totalIssue;
			if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
			{

				if(($value_with ==1 && (number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000)) || ($value_with ==0 && (number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)))
				{

					$summary_data[$row[csf("item_category_id")]]["openingBalanceValue"] += $openingBalanceValue;
					$summary_data[$row[csf("item_category_id")]]["purchase"] += $data_array[$row[csf("id")]]['purchase_amt'];
					$summary_data[$row[csf("item_category_id")]]["transfer_in_qty"] += $transfer_in_qty_amt;
					$summary_data[$row[csf("item_category_id")]]["issue_return"] += $data_array[$row[csf("id")]]['issue_return_amt'];
					$summary_data[$row[csf("item_category_id")]]["totalReceive"] += $totalReceive_amt;


					$summary_data[$row[csf("item_category_id")]]["issue_qty"] += $issue_qty_amt;
					$summary_data[$row[csf("item_category_id")]]["loan_issue_amt"] += $data_array[$row[csf("id")]]['loan_issue_amt'];
					$summary_data[$row[csf("item_category_id")]]["aop_issue_amt"] += $data_array[$row[csf("id")]]['aop_issue_amt'];
					$summary_data[$row[csf("item_category_id")]]["transfer_out_qty"] += $transfer_out_qty_amt;
					$summary_data[$row[csf("item_category_id")]]["receive_return"] += $data_array[$row[csf("id")]]['receive_return_amt'];
					$summary_data[$row[csf("item_category_id")]]["totalIssue"] += $totalIssue_amt;

					$summary_data[$row[csf("item_category_id")]]["stockValue"] += $closingStock_amt;
					$summary_data[$row[csf("item_category_id")]]["re_order_label"] = $row[csf('re_order_label')];
				}
			}
		}

		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	
		$sql="Select b.id as prod_id, min(a.transaction_date) as min_date, max(a.transaction_date) as max_date,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as rcv_total_opening,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_quantity else 0 end) as iss_total_opening,
		sum(case when a.transaction_type in(1,4,5) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as rcv_total_opening_amt,
		sum(case when a.transaction_type in(2,3,6) and a.transaction_date<'".$from_date."' then a.cons_amount else 0 end) as iss_total_opening_amt,
		sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as purchase,
		sum(case when a.transaction_type=1 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as purchase_amt,
		sum(case when a.transaction_type=2 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_quantity else 0 end) as issue,
		
		sum(case when a.transaction_type=2 and c.ISSUE_PURPOSE not in(5,18,37,63,74) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as issue_amt,
		sum(case when a.transaction_type=2 and c.ISSUE_PURPOSE=5 and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as loan_issue_amt,
		sum(case when a.transaction_type=2 and c.ISSUE_PURPOSE in(18) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as aop_issue_amt,
		sum(case when a.transaction_type=2 and c.ISSUE_PURPOSE in(37) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as wasing_issue_amt,
		sum(case when a.transaction_type=2 and c.ISSUE_PURPOSE in(63) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as etp_issue_amt,
		sum(case when a.transaction_type=2 and c.ISSUE_PURPOSE in(74) and a.transaction_date between '".$from_date."' and '".$to_date."' then a.cons_amount else 0 end) as utility_issue_amt,
		sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as issue_return,
		sum(case when a.transaction_type in(4) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_amount else 0 end) as issue_return_amt,
		sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as receive_return,
		sum(case when a.transaction_type in(3) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_amount else 0 end) as receive_return_amt,

		sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_issue,
		sum(case when a.transaction_type in(6) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_amount else 0 end) as item_transfer_issue_amt,
		sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_quantity else 0 end) as item_transfer_receive,
		sum(case when a.transaction_type in(5) and a.transaction_date between '".$from_date."'  and '".$to_date."' then a.cons_amount else 0 end) as item_transfer_receive_amt,
		sum(case when a.transaction_type in(1,4) and a.receive_basis in(1,2) then a.cons_quantity else 0 end) as rcv_total_wo
		from product_details_master b, inv_transaction a left join INV_ISSUE_MASTER c on a.mst_id=c.id and a.transaction_type=2
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.item_category_id in (5,6,7,23) and  a.item_category in (5,6,7,23) and a.order_id=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond  
		group by b.id";
		//echo $sql;die;
		
		$trnasactionData = sql_select($sql);
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening']=$row_p[csf("rcv_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['iss_total_opening']=$row_p[csf("iss_total_opening")];
			$data_array[$row_p[csf("prod_id")]]['rcv_total_opening_amt']=$row_p[csf("rcv_total_opening_amt")];
			$data_array[$row_p[csf("prod_id")]]['iss_total_opening_amt']=$row_p[csf("iss_total_opening_amt")];
			$data_array[$row_p[csf("prod_id")]]['purchase']=$row_p[csf("purchase")];
			$data_array[$row_p[csf("prod_id")]]['purchase_amt']=$row_p[csf("purchase_amt")];
			$data_array[$row_p[csf("prod_id")]]['issue']=$row_p[csf("issue")];
			$data_array[$row_p[csf("prod_id")]]['issue_amt']=$row_p[csf("issue_amt")];
			$data_array[$row_p[csf("prod_id")]]['loan_issue_amt']=$row_p[csf("loan_issue_amt")];
			$data_array[$row_p[csf("prod_id")]]['aop_issue_amt']=$row_p[csf("aop_issue_amt")];
			$data_array[$row_p[csf("prod_id")]]['issue_return']=$row_p[csf("issue_return")];
			
			$data_array[$row_p[csf("prod_id")]]['wasing_issue_amt']=$row_p[csf("wasing_issue_amt")];
			$data_array[$row_p[csf("prod_id")]]['etp_issue_amt']=$row_p[csf("etp_issue_amt")];
			$data_array[$row_p[csf("prod_id")]]['utility_issue_amt']=$row_p[csf("utility_issue_amt")];
			
			$data_array[$row_p[csf("prod_id")]]['issue_return_amt']=$row_p[csf("issue_return_amt")];
			$data_array[$row_p[csf("prod_id")]]['receive_return']=$row_p[csf("receive_return")];
			$data_array[$row_p[csf("prod_id")]]['receive_return_amt']=$row_p[csf("receive_return_amt")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_issue']=$row_p[csf("item_transfer_issue")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_issue_amt']=$row_p[csf("item_transfer_issue_amt")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_receive']=$row_p[csf("item_transfer_receive")];
			$data_array[$row_p[csf("prod_id")]]['item_transfer_receive_amt']=$row_p[csf("item_transfer_receive_amt")];
			$data_array[$row_p[csf("prod_id")]]['rcv_total_wo']=$row_p[csf("rcv_total_wo")];
			$data_array[$row_p[csf("prod_id")]]['expire_date']=$row_p[csf("expire_date")];
			
			$date_array[$row[csf("prod_id")]]['min_date']=$row[csf("min_date")];
			$date_array[$row[csf("prod_id")]]['max_date']=$row[csf("max_date")];
			
		}


		$sql="select b.id, b.item_code,b.re_order_label, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit,b.company_id from product_details_master b where b.status_active=1 and b.is_deleted=0 $company_cond $item_category_id $group_id $prod_cond $item_account $search_cond order by b.id";
		$result = sql_select($sql);
		foreach($result as $row)
		{

			$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
			$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));

			$issue_qty=$data_array[$row[csf("id")]]['issue'];
			$issue_qty_amt=$data_array[$row[csf("id")]]['issue_amt'];
			$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
			$transfer_out_qty_amt=$data_array[$row[csf("id")]]['item_transfer_issue_amt'];

			$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];
			$transfer_in_qty_amt=$data_array[$row[csf("id")]]['item_transfer_receive_amt'];
			$expire_date=$data_array[$row[csf("id")]]['expire_date'];

			$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
			$openingBalanceValue=$openingRate=0;
			$openingBalanceValue =  $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];

			$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;
			$totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;

			$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;
			//$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['loan_issue_amt']+$data_array[$row[csf("id")]]['aop_issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

			$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['loan_issue_amt']+$data_array[$row[csf("id")]]['aop_issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt+$data_array[$row[csf("id")]]['wasing_issue_amt']+$data_array[$row[csf("id")]]['etp_issue_amt']+$data_array[$row[csf("id")]]['utility_issue_amt'];

			$closingStock=$openingBalance+$totalReceive-$totalIssue;
			$closingStock_amt=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;

			//$closingStock=$opening+$totalReceive-$totalIssue;
			if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
			{

				if(($value_with ==1 && (number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000)) || ($value_with ==0 && (number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)))
				{

					$summary_data[$row[csf("item_category_id")]]["openingBalanceValue"] += $openingBalanceValue;
					$summary_data[$row[csf("item_category_id")]]["purchase"] += $data_array[$row[csf("id")]]['purchase_amt'];
					$summary_data[$row[csf("item_category_id")]]["transfer_in_qty"] += $transfer_in_qty_amt;
					$summary_data[$row[csf("item_category_id")]]["issue_return"] += $data_array[$row[csf("id")]]['issue_return_amt'];
					$summary_data[$row[csf("item_category_id")]]["totalReceive"] += $totalReceive_amt;


					$summary_data[$row[csf("item_category_id")]]["issue_qty"] += $issue_qty_amt;
					$summary_data[$row[csf("item_category_id")]]["loan_issue_amt"] += $data_array[$row[csf("id")]]['loan_issue_amt'];
					$summary_data[$row[csf("item_category_id")]]["aop_issue_amt"] += $data_array[$row[csf("id")]]['aop_issue_amt'];
					
					$summary_data[$row[csf("item_category_id")]]["wasing_issue_amt"] += $data_array[$row[csf("id")]]['wasing_issue_amt'];
					$summary_data[$row[csf("item_category_id")]]["etp_issue_amt"] += $data_array[$row[csf("id")]]['etp_issue_amt'];
					$summary_data[$row[csf("item_category_id")]]["utility_issue_amt"] += $data_array[$row[csf("id")]]['utility_issue_amt'];
					
					$summary_data[$row[csf("item_category_id")]]["transfer_out_qty"] += $transfer_out_qty_amt;
					$summary_data[$row[csf("item_category_id")]]["receive_return"] += $data_array[$row[csf("id")]]['receive_return_amt'];
					$summary_data[$row[csf("item_category_id")]]["totalIssue"] += $totalIssue_amt;

					$summary_data[$row[csf("item_category_id")]]["stockValue"] += $closingStock_amt;
					$summary_data[$row[csf("item_category_id")]]["re_order_label"] = $row[csf('re_order_label')];
				}
			}
		}

		ob_start();
		?>
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:1770px">
			<table width="1770px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="12" class="form_caption" align="center" style="border:none; font-size:16px;">
						   <b>Company Name : <? $com_id_arr=explode(",",$cbo_company_name); if(count($com_id_arr)>1) echo "JK Group"; else echo $companyArr[$cbo_company_name]; ?>
                           <br />
                           South Dariapur, Savar, Dhaka-1340
                           <br />
                           Consumption Summary Of Dyes & Chemicals Report</b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="12" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="1750" rules="all" id="rpt_table_header" align="left">
				<thead>
					<tr>
						<th rowspan="2" width="20">SL</th>
						<th rowspan="2" width="90">Item Category</th>
                        <th rowspan="2" width="110">Opening Value Tk</th>
						<th colspan="4">Receive Value TK</th>
						<th colspan="9">Issue Value TK</th>
						<th rowspan="2" width="120">Closing Value Tk</th>
					</tr>
					<tr>
						<th width="80">Purchase</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue Return</th>
						<th width="80">Total Rcv</th>

						<th width="80">Dyeing</th>
                        <th width="80">Loan </th>
                        <th width="80">AOP</th>
                        
                        <th width="80">Washing</th>
                        <th width="80">ETP</th>
                        <th width="80">Utility</th>
                        
						<th width="80">Transfer Out</th>
						<th width="80">Receive Return</th>
						<th width="80">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:1770px; max-height:250px; overflow-y:scroll; overflow-x:hidden; float: left;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="1750" rules="all" align="left">
			<?
			$i=1;
			$tot_chemical_used=$tot_dyes_used=0;
			foreach($summary_data as $category_id =>$row)
			{
				$re_order_label = $row[csf('re_order_label')];
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($category_id==5 || $category_id==7) $tot_chemical_used+=number_format($row["issue_qty"],3,'.','');
				else if($category_id==6) $tot_dyes_used+=number_format($row["issue_qty"],3,'.','');
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td width="20" align="center" title="<? echo $comp_id;?>"><? echo $i; ?></td>
					<td width="90"><p><? echo $item_category[$category_id]; ?></p></td>
					<td width="110" align="right"><p style="word-break: break-all;"><? echo number_format($row["openingBalanceValue"],3); ?></p></td>
					<td width="80" align="right"><p><? echo number_format($row['purchase'],3);?></p></td>
					<td width="80" align="right"><p><? echo number_format($row["transfer_in_qty"],3);?></p></td>
					<td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row['issue_return'],3);?></p></td>
					<td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row["totalReceive"],3);?></p></td>
					<td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row["issue_qty"],3); ?></p></td>
                    
                    <td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row["loan_issue_amt"],3); ?></p></td>
                    <td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row["aop_issue_amt"],3); ?></p></td>
                    
                    <td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row["wasing_issue_amt"],3); ?></p></td>
                    <td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row["etp_issue_amt"],3); ?></p></td>
                    <td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row["utility_issue_amt"],3); ?></p></td>
                    
					<td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row["transfer_out_qty"],3);?></p></td>
					<td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row['receive_return'],3);?></p></td>
					<td width="80" align="right"><p style="word-break: break-all;"><? echo number_format($row["totalIssue"],3);?></p></td>
					<td width="120" align="right"><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($row["stockValue"],3);?></p></td>
				</tr>
				<?
				$i++;$y++;
				$grand_tot_openingBalanceValue += $row["openingBalanceValue"];
				$grand_tot_purchase +=$row["purchase"];
				$grand_tot_transfer_in_qty +=$row["transfer_in_qty"];
				$grand_tot_issue_return +=$row["issue_return"];
				$grand_tot_totalReceive +=$row["totalReceive"];
				$grand_tot_issue_qty +=$row["issue_qty"];
				
				$grand_tot_loan_issue_amt +=$row["loan_issue_amt"];
				$grand_tot_aop_issue_amt +=$row["aop_issue_amt"];
				
				$grand_tot_wasing_issue_amt +=$row["wasing_issue_amt"];
				$grand_tot_etp_issue_amt +=$row["etp_issue_amt"];
				$grand_tot_utility_issue_amt +=$row["utility_issue_amt"];
				
				$grand_tot_transfer_out_qty +=$row["transfer_out_qty"];
				$grand_tot_receive_return +=$row["receive_return"];
				$grand_tot_totalIssue +=$row["totalIssue"];
				$grand_tot_stock_value+=$row["stockValue"];

			}
			?>
			</table>
			</div>
			 <table width="1750" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left">
				<tfoot>
					<tr>
						<th width="20"></th>
						<th width="90" align="right">Grand Total =</th>
                        <th width="110" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_openingBalanceValue,3); ?></p></th>
						<th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_purchase,3); ?></p></th>
						<th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_transfer_in_qty,3); ?></p></th>
						<th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_issue_return,3); ?></p></th>
						<th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_totalReceive,3); ?></p></th>

						<th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_issue_qty,3); ?></p></th>
                        
                        <th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_loan_issue_amt,3); ?></p></th>
                        <th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_aop_issue_amt,3); ?></p></th>
                        
                        <th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_wasing_issue_amt,3); ?></p></th>
                        <th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_etp_issue_amt,3); ?></p></th>
                        <th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_utility_issue_amt,3); ?></p></th>
                        
						<th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_transfer_out_qty,3); ?></p></th>
						<th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_receive_return,3); ?></p></th>
						<th width="80" align="right" id=""><p style="word-break: break-all;"><? echo number_format($grand_tot_totalIssue,3); ?></p></th>

						<th width="120" align="right" id=""><p style="word-break: break-all;word-wrap: break-word;"><? echo number_format($grand_tot_stock_value,2); ?></p></th>
					</tr>
				</tfoot>
			</table>
        </div>
        
        <table width="100%" border="0" align="left">
        	<tr style="border:none">
                <td colspan="3">&nbsp; </td>
            </tr>
            <tr style="border:none">
                <td colspan="3">&nbsp; </td>
            </tr>
        </table>
        
		<style>
			table.myFormat tr td { font-size: 20px; }
		</style>

        <div>
        <table width="500" style="font-weight:bold; margin-left:150px;" border="1" cellpadding="2" cellspacing="0" class="rpt_table myFormat"  rules="all" align="left">
            <tr style="border:none">
                <td colspan="3"> Note:</td>
            </tr>
            <tr>
                <td width="300">Total Dyeing Production Qty: </td>
                <td width="50" align="center">KG</td>
                <td align="right"><? echo number_format($tot_dying_qnty,3); ?></td>
            </tr>
            <tr>
                <td>Total Chemical Used: </td>
                <td align="center">Tk</td>
                <td align="right"><? echo number_format($tot_chemical_used,3); ?></td>
            </tr>
            <tr>
                <td>Total Dyes Used: </td>
                <td align="center">Tk</td>
                <td align="right"><? echo number_format($tot_dyes_used,3); ?></td>
            </tr>
            <tr>
                <td>Total Dyes & Chemical Used Consumption: </td>
                <td align="center">Tk</td>
                <td align="right"><? echo number_format($grand_tot_issue_qty,3); ?></td>
            </tr>
            <tr>
                <td>Chemicals Cost Per Kg: </td>
                <td align="center">Tk</td>
                <td align="right"><? echo number_format(($tot_chemical_used/$tot_dying_qnty),3); ?></td>
            </tr>
            <tr>
                <td>Dyes Cost Per Kg: </td>
                <td align="center">Tk</td>
                <td align="right"><? echo number_format(($tot_dyes_used/$tot_dying_qnty),3); ?></td>
            </tr>
            <tr>
                <td>Average Cost Per Kg: </td>
                <td align="center">Tk</td>
                <td align="right"><? echo number_format(($grand_tot_issue_qty/$tot_dying_qnty),3); ?></td>
            </tr>
            <tr style="border:none">
                <td colspan="3"> Note:</td>
            </tr>
            <tr>
                <td>Last Month Cost Per Kg: </td>
                <td align="center">Tk</td>
                <td align="right"><? echo number_format($prev_month_cost_per,3); ?></td>
            </tr>
        </table>
        </div>


		<div>
			<table border="1" style="margin-top:30px;" cellpadding="2" cellspacing="0" class="rpt_table" id="" width="1750" rules="all" align="left">
			<tr>
				<?
				$com_arr = explode(",",$cbo_company_name);
				$single_company = $com_arr[0];
				echo signature_table(324, $single_company, 1850,$cbo_template_id, 20,$inserted_by); ?>
			</tr>
		</table>
		</div>
		
        <?
		
	}
	else if($report_type==6)
	{
		if ($cbo_company_name=="") $company_id =""; else $company_id =" and a.company_id in($cbo_company_name)";
		if ($cbo_item_category_id=="") $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id in($cbo_item_category_id)";
		if ($cbo_item_category_id=="") $item_category_cond=" and a.item_category in(5,6,7,23)"; else $item_category_cond=" and a.item_category in($cbo_item_category_id)";
		if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
		if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
		if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
		//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
		$store_id="";
		if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";

		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
		
		$search_cond="";
		//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";


		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
		$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
		$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
		
		$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond order by b.id, a.transaction_date ASC";
		
		//echo $trans_sql;die;
		$trnasactionData = sql_select($trans_sql);
		//echo count($trnasactionData).jahid;die;
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			if($row_p["TRANSACTION_TYPE"]==1)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['purchase']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['purchase_amt']+=$row_p["CONS_AMOUNT"];
				}
				$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['last_receive_date']=$row_p["TRANSACTION_DATE"];
			}
			else if($row_p["TRANSACTION_TYPE"]==2)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['issue_amt']+=$row_p["CONS_AMOUNT"];
				}
				$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['last_issue_date']=$row_p["TRANSACTION_DATE"];
			}
			else if($row_p["TRANSACTION_TYPE"]==3)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['receive_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['receive_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==4)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['issue_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['issue_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==5)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['item_transfer_receive_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==6)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]][$row_p["LOT_NO"]]['item_transfer_issue_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
		}
		
		//echo "test2";die;
		//var_dump($data_array);die;
		//print_r($data_array);die;
		//echo $data_array[4103]['rcv_total_wo'].Fuad;
		if ($cbo_item_category_id=="") $item_category_cond=" and item_category in(5,6,7,23)"; else $item_category_cond=" and item_category in($cbo_item_category_id)";
		$returnRes_date="select prod_id as PROD_ID, batch_lot as LOT, min(transaction_date) as MIN_DATE, max(transaction_date) as MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 $item_category_cond group by prod_id, batch_lot";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row["PROD_ID"]][$row["LOT"]]['min_date']=$row["MIN_DATE"];
			$date_array[$row["PROD_ID"]][$row["LOT"]]['max_date']=$row["MAX_DATE"];
		}
		

		$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and b.item_category_id in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

		$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
		$i=1;
		$table_width=2748;
		ob_start();
		?>
        <p style="color:#FF0000; font-size:16px; font-weight:bold;">Value Will Not Match Store Level.</p>
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:<? echo $table_width; ?>">
			<table width="<? echo $table_width; ?>px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="12" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? $com_id_arr=explode(",",$cbo_company_name); if(count($com_id_arr)>1) echo "JK Group"; else echo $companyArr[$cbo_company_name]; ?></b>
                           <br />
                           South Dariapur, Savar, Dhaka-1340
                           <br />
                           Consumption Summary Of Dyes & Chemicals Report
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $table_width; ?>" rules="all" id="rpt_table_header">
				<thead>
					<tr>
						<th rowspan="2" width="50">SL</th>
                        <th rowspan="2" width="110">Company</th>
						<th colspan="9">Description</th>
						<th rowspan="2" width="100"> Opneing Rate</th>
                        <th rowspan="2" width="100">Opening Stock</th>
                        <th rowspan="2" width="110">Opening Value</th>
						<th colspan="5">Receive</th>
						<th colspan="5">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2" width="80">Avg. Rate</th>
						<th rowspan="2" width="120">Stock Value</th>
						<th rowspan="2" width="60">Age</th>
						<th rowspan="2" width="60">DOH</th>
						<th rowspan="2">Pipe Line</th>
					</tr>
					<tr>
						<th width="60">Prod. ID</th>
                        <th width="100">Lot</th>
						<th width="70">Item Code</th>
						<th width="100">Item Category</th>
						<th width="100">Item Group</th>
						<th width="100">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="60">UOM</th>
						<th width="80">Purchase</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue Return</th>
						<th width="100">Total Received</th>
						<th width="100">Last Receive Date</th>
						<th width="80">Issue</th>
						<th width="80">Transfer Out</th>
						<th width="80">Receive Return</th>
						<th width="100">Total Issue</th>
						<th width="100">Last issue Date</th>
					</tr>
				</thead>
			</table>
			<div style="width:<? echo $table_width; ?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $table_width-20; ?>" rules="all" align="left">
			<?
				$rcv_qnty_array=return_library_array("SELECT b.prod_id, sum(b.cons_quantity) as cons_quantity from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.status_active=1 group by b.prod_id","prod_id","cons_quantity");
				
			 	$sql="SELECT b.id, b.item_code, b.item_category_id, b.item_group_id, b.unit_of_measure, b.item_description, b.sub_group_name, b.current_stock, b.avg_rate_per_unit, c.lot, b.company_id
				from product_details_master b, inv_store_wise_qty_dtls c
				where b.id=c.prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.company_id in($cbo_company_name) $item_category_id $group_id $prod_cond $item_account_cond $search_cond
				group by b.id, b.item_code, b.item_category_id, b.item_group_id, b.unit_of_measure, b.item_description, b.sub_group_name, b.current_stock, b.avg_rate_per_unit, c.lot, b.company_id
				order by b.id";
				//echo $sql;die;
				$result = sql_select($sql);
				foreach($result as $row)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
						$stylecolor='color:#A61000;';
					else
						$stylecolor='color:#000000;';
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]][$row[csf("lot")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]][$row[csf("lot")]]['max_date'],date("Y-m-d"));


					$issue_qty=$data_array[$row[csf("id")]][$row[csf("lot")]]['issue'];
					$transfer_out_qty=$data_array[$row[csf("id")]][$row[csf("lot")]]['item_transfer_issue'];
					$transfer_in_qty=$data_array[$row[csf("id")]][$row[csf("lot")]]['item_transfer_receive'];

					$last_receive_date=change_date_format($data_array[$row[csf("id")]][$row[csf("lot")]]['last_receive_date']);
					$last_issue_date=change_date_format($data_array[$row[csf("id")]][$row[csf("lot")]]['last_issue_date']);

					$transfer_out_qty_amt=$data_array[$row[csf("id")]][$row[csf("lot")]]['item_transfer_issue_amt'];
					$transfer_in_qty_amt=$data_array[$row[csf("id")]][$row[csf("lot")]]['item_transfer_receive_amt'];

					$openingBalance = $data_array[$row[csf("id")]][$row[csf("lot")]]['rcv_total_opening']-$data_array[$row[csf("id")]][$row[csf("lot")]]['iss_total_opening'];
					$openingBalanceValue = $data_array[$row[csf("id")]][$row[csf("lot")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]][$row[csf("lot")]]['iss_total_opening_amt'];
					if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
					$totalReceive = $data_array[$row[csf("id")]][$row[csf("lot")]]['purchase']+$data_array[$row[csf("id")]][$row[csf("lot")]]['issue_return']+$transfer_in_qty;//+$openingBalance
					$totalIssue = $data_array[$row[csf("id")]][$row[csf("lot")]]['issue']+$data_array[$row[csf("id")]][$row[csf("lot")]]['receive_return']+$transfer_out_qty;

					$totalReceive_amt = $data_array[$row[csf("id")]][$row[csf("lot")]]['purchase_amt']+$data_array[$row[csf("id")]][$row[csf("lot")]]['issue_return_amt']+$transfer_in_qty_amt;
					$totalIssue_amt = $data_array[$row[csf("id")]][$row[csf("lot")]]['issue_amt']+$data_array[$row[csf("id")]][$row[csf("lot")]]['receive_return_amt']+$transfer_out_qty_amt;

					$closingStock=$openingBalance+$totalReceive-$totalIssue;
					$stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
					if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
					//$closingStock=$opening+$totalReceive-$totalIssue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{
						$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$rcv_qnty_array[$row[csf("id")]];
						if($pipeLine_qty<0) $pipeLine_qty=0;
						if($value_with==1)
						{
							if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
                                    <td width="110"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
									<td width="60" align="center"><? echo $row[csf("id")]; ?></td>
                                    <td width="100"><p><? echo $row[csf("lot")]; ?></p></td>
									<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
									<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
									<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
									<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
									<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
									<td width="100" align="right"><p><? echo $last_receive_date; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" align="right"><p><? echo $last_issue_date; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<td width="60" align="center"><p><? echo $ageOfDays; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
                                    <?
									if($prod_check[$row[csf("id")]]=="")
									{
										$prod_check[$row[csf("id")]]=$row[csf("id")];
										?>
                                        <td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
                                        <?									}
									else
									{
										?>
                                        <td align="right" ><p></p></td>	
                                        <?	
									}
									?>
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
						else
						{
							if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
							{
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="50" align="center"><? echo $i; ?></td>
                                    <td width="110"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
									<td width="60" align="center"><? echo $row[csf("id")]; ?></td>
                                    <td width="100"><p><? echo $row[csf("lot")]; ?></p></td>
									<td width="70"><p><? echo $row[csf("item_code")]; ?></p></td>
									<td width="100"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
									<td width="100"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
									<td width="100"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
									<td width="180"><p><? echo $row[csf("item_description")]; ?></p></td>
									<td width="70"><p><? echo $row[csf("item_size")]; ?></p></td>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
									<td width="100" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
									<td width="100" align="right"><p><? echo $last_receive_date; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" align="right"><p><? echo $last_issue_date; ?></p></td>
									<td width="100" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00";//number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<td width="60" align="center"><p><? echo $ageOfDays; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
                                    <?
									if($prod_check[$row[csf("id")]]=="")
									{
										$prod_check[$row[csf("id")]]=$row[csf("id")];
										?>
                                        <td align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>	
                                        <?									}
									else
									{
										?>
                                        <td align="right" ><p></p></td>	
                                        <?	
									}
									?>
																	
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
					}
				}
			?>
			</table>
			</div>
			<table width="<? echo $table_width; ?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
				<tfoot>
					<tr>
						<th width="50">&nbsp;</th>
                        <th width="110">&nbsp;</th>
						<th width="60">&nbsp;</th>
                        <th width="100">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th width="100">Total:</th>
                        <th width="100" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,4); ?></th>
                        <th width="110" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
						<th width="80" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
						<th width="100" align="right"></th>
						<th width="80" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
						<th width="80" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
						<th width="80" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
						<th width="100" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
						<th width="100" align="right"></th>
						<th width="100" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,4); ?></th>
						<th width="80">&nbsp;</th>
						<th width="120" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
						<th width="60">&nbsp;</th>
						<th width="60">&nbsp;</th>
						<th align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
					</tr>
				</tfoot>
			</table>
        </div>
        <?
	}
	else
	{

		if ($cbo_company_name=="") $company_id =""; else $company_id =" and a.company_id in($cbo_company_name)";
		if ($cbo_item_category_id=="") $item_category_id=" and b.item_category_id in(5,6,7,23)"; else $item_category_id=" and b.item_category_id in($cbo_item_category_id)";
		if ($cbo_item_category_id=="") $item_category_cond=" and a.item_category in(5,6,7,23)"; else $item_category_cond=" and a.item_category in($cbo_item_category_id)";
		if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
		if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
		if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
		if ($cbo_compliance>0) $item_account_cond.=" and b.is_compliance = $cbo_compliance";
		//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
		$store_id="";
		if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";

		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
		$search_cond="";
		//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";


		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
		//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
		$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");
		
		$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS, a.STORE_ID from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond $item_account_cond order by b.id,a.id ASC";
		
		//echo $trans_sql;die;
		$trnasactionData = sql_select($trans_sql);
		//echo count($trnasactionData).jahid;die;
		$data_array=array();
		foreach($trnasactionData as $row_p)
		{
			if($row_p["TRANSACTION_TYPE"]==1)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['purchase']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['purchase_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==2)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['issue_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==3)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['receive_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['receive_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==4)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['issue_return']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['issue_return_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==5)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['item_transfer_receive_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			else if($row_p["TRANSACTION_TYPE"]==6)
			{
				if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
				{
					$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				}
				else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
				{
					$data_array[$row_p["PROD_ID"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
					$data_array[$row_p["PROD_ID"]]['item_transfer_issue_amt']+=$row_p["CONS_AMOUNT"];
				}
			}
			if($batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=="" && $row_p["LOT_NO"] !="")
			{
				$batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=$row_p["LOT_NO"];
				$data_array[$row_p["PROD_ID"]]['lot_no'].=$row_p["LOT_NO"].",";
			}
			
			if($row_p["TRANSACTION_TYPE"]==1 &&($row_p["RECEIVE_BASIS"]==1 || $row_p["RECEIVE_BASIS"]==2))
			{
				$data_array[$row_p["PROD_ID"]]['rcv_total_wo']+=$row_p["CONS_QUANTITY"];
			}
			if($row_p["STORE_ID"]){ $data_array[$row_p["PROD_ID"]]['store_id']=$row_p["STORE_ID"];}
		}
		unset($trnasactionData);
		//echo "test2";die;
		//var_dump($data_array);die;
		//print_r($data_array);die;
		//echo $data_array[4103]['rcv_total_wo'].Fuad;
		if ($cbo_item_category_id=="") $item_category_cond=" and item_category in(5,6,7,23)"; else $item_category_cond=" and item_category in($cbo_item_category_id)";
		$returnRes_date="select prod_id as PROD_ID, min(transaction_date) as MIN_DATE, max(transaction_date) as MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 $item_category_cond group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row["PROD_ID"]]['min_date']=$row["MIN_DATE"];
			$date_array[$row["PROD_ID"]]['max_date']=$row["MAX_DATE"];
		}
		

		$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and b.item_category_id in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

		$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");

		if($cbo_store_wise==1)
		{
			$tbl_width="2830";
		}
		else
		{
			$tbl_width="2730";
		}
		$i=1;
		ob_start();
		?>
		<style>
			.wrd_brk{word-break: break-all;}
		</style>

        <p style="color:#FF0000; font-size:16px; font-weight:bold;">Value Will Be Match Only For Item Level.</p>
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:2248">
			<table width="2648px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="20" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?=$tbl_width;?>" rules="all" id="rpt_table_header">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL</th>
                        <th rowspan="2" width="110">Company</th>
						<th colspan="8">Description</th>
						<th rowspan="2" width="100"> Opneing Rate</th>
                        <th rowspan="2" width="100">Opening Stock</th>
                        <th rowspan="2" width="110">Opening Value</th>
						<th colspan="4">Receive</th>
						<th colspan="4">Issue</th>
						<th rowspan="2" width="100">Closing Stock</th>
						<th rowspan="2" width="80">Avg. Rate</th>
						<th rowspan="2" width="120">Stock Value</th>
						<th rowspan="2" width="80">Rate [USD]</th>
						<th rowspan="2" width="120">Stock Value[USD]</th>
						<?
							if($cbo_store_wise==1)
							{
								?>
									<th rowspan="2" width="100">Store Name</th>
								<?
							}
						?>
						<th rowspan="2" width="60">DOH</th>
						<th rowspan="2" width="100">Pipe Line</th>
						<th width="150" rowspan="2">Lot</th>
                        <th rowspan="2">Zero Discharge</th>
					</tr>
					<tr>
						<th width="60">Prod. ID</th>
						<th width="70">Item Code</th>
						<th width="100">Item Category</th>
						<th width="100">Item Group</th>
						<th width="80">Item Sub-group</th>
						<th width="180">Item Description</th>
						<th width="70">Item Size</th>
						<th width="50">UOM</th>
						<th width="80">Purchase</th>
						<th width="80">Transfer In</th>
						<th width="80">Issue Return</th>
						<th width="100">Total Received</th>
						<th width="80">Issue</th>
						<th width="80">Transfer Out</th>
						<th width="80">Receive Return</th>
						<th width="100">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?=$tbl_width+18;?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?=$tbl_width;?>" rules="all" align="left">
			<?
				if ($cbo_item_category_id=="") $rcv_category_cond=" and b.item_category in(5,6,7,23)"; else $rcv_category_cond=" and b.item_category in($cbo_item_category_id)";
				
			 	$sql="SELECT b.id, b.item_code, b.item_category_id,b.item_group_id,b.unit_of_measure,b.item_description,b.sub_group_name, b.current_stock,avg_rate_per_unit, b.re_order_label, b.is_compliance, b.company_id from product_details_master b where b.status_active=1 and b.is_deleted=0 and b.company_id in($cbo_company_name) $item_category_id $group_id $prod_cond $item_account_cond $search_cond order by b.id";
				//echo $sql;die;
				$result = sql_select($sql);
				foreach($result as $row)
				{
					//if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					$re_order_label=$row[csf("re_order_label")];
					if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
						$stylecolor='color:#A61000;';
					else
						$stylecolor='color:#000000;';
					$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
					$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));


					$issue_qty=$data_array[$row[csf("id")]]['issue'];

					$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
					$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];

					$transfer_out_qty_amt=$data_array[$row[csf("id")]]['item_transfer_issue_amt'];
					$transfer_in_qty_amt=$data_array[$row[csf("id")]]['item_transfer_receive_amt'];

					$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
					$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
					if($openingBalance==0)
					{
						$openingBalanceValue=0;
						$openingRate=0;
					}
					else
					{
						if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
					}
					
					$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
					$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;

					$totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;
					$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

					$closingStock=$openingBalance+$totalReceive-$totalIssue;
					$stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
					if($closingStock==0)
					{
						$stockValue=0;
						$avg_rate=0;
					}
					else
					{
						if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
					}
					
					//$closingStock=$opening+$totalReceive-$totalIssue;
					if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
					{
						//$avg_rate=$mrr_rate_arr[$row[csf("id")]];
						//$stockValue=$closingStock*$avg_rate;
						$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo'];

						if($pipeLine_qty<0) $pipeLine_qty=0;
						//echo $closingStock."=".$openingBalance."<br>";
						if($value_with==1)
						{
							if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
							{
								if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30" class="wrd_brk" align="center"><? echo $i; ?></td>
                                    <td width="110" class="wrd_brk"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
									<td width="60" align="center" class="wrd_brk"><? echo $row[csf("id")]; ?></td>
									<td width="70" class="wrd_brk"><p><? echo $row[csf("item_code")]; ?></p></td>
									<td width="100" class="wrd_brk"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
									<td width="100" class="wrd_brk"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
									<td width="80" class="wrd_brk"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
									<td width="180" class="wrd_brk"><p><? echo $row[csf("item_description")]; ?></p></td>
									<td width="70" class="wrd_brk"><p><? echo $row[csf("item_size")]; ?></p></td>
									<td width="50" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" class="wrd_brk" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00"; //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? if(number_format($avg_rate,4)>0) echo number_format($avg_rate/$txt_excenge_rate,4); else echo "0.00";?></p></td>
									<td width="120" class="wrd_brk" align="right"><p><? if(number_format($stockValue,4)>0) echo number_format($stockValue/$txt_excenge_rate,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<?
										if($cbo_store_wise==1)
										{
											?>
												<td width="100" class="wrd_brk"><p><? echo $storeArr[$data_array[$row[csf("id")]]['store_id']];?></p></td>
											<?
										}
									?>
									<td width="60" class="wrd_brk" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
									<td width="100" class="wrd_brk" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
									<td width="150" class="wrd_brk"><p><? echo chop($data_array[$row[csf("id")]]['lot_no'],","); ?></p></td>
                                    <td class="wrd_brk" align="center"><p><? echo $compliance_arr[$row[csf("is_compliance")]]; ?></p></td>
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
						else
						{
							if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
							{
								if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="30" align="center"><? echo $i; ?></td>
                                    <td width="110" class="wrd_brk"><p><? echo $companyArr[$row[csf("company_id")]]; ?></p></td>
									<td width="60" align="center" class="wrd_brk"><? echo $row[csf("id")]; ?></td>
									<td width="70" class="wrd_brk"><p><? echo $row[csf("item_code")]; ?></p></td>
									<td width="100" class="wrd_brk"><p><? echo $item_category[$row[csf("item_category_id")]]; ?></p></td>
									<td width="100" class="wrd_brk"><p><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></p></td>
									<td width="80" class="wrd_brk"><p><? echo $row[csf("sub_group_name")]; ?></p></td>
									<td width="180" class="wrd_brk"><p><? echo $row[csf("item_description")]; ?></p></td>
									<td width="70" class="wrd_brk"><p><? echo $row[csf("item_size")]; ?></p></td>
									<td width="50" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingRate,4); ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
									<td width="110" class="wrd_brk" align="right"><p><? echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
									<td width="100" class="wrd_brk" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>
									<td width="80" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($avg_rate,4); else echo "0.00";//number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
									<td width="120" class="wrd_brk" align="right"><p><? if(number_format($closingStock,4)>0) echo number_format($stockValue,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									
									<td width="80" class="wrd_brk" align="right"><p><? if(number_format($avg_rate,4)>0) echo number_format($avg_rate/$txt_excenge_rate,4); else echo "0.00";?></p></td>


									<td width="120" class="wrd_brk" align="right"><p><? if(number_format($stockValue,4)>0) echo number_format($stockValue/$txt_excenge_rate,3); else echo "0.00"; $tot_stock_value+=$stockValue; ?></p></td>
									<?
										if($cbo_store_wise==1)
										{
											?>
												<td width="100" class="wrd_brk"><p><? echo $storeArr[$data_array[$row[csf("id")]]['store_id']];?></p></td>
											<?
										}
									?>
									<td width="60" class="wrd_brk" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?></p></td>
									<td width="100" class="wrd_brk" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]."--".$rcv_qnty_array[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
									<td width="150" class="wrd_brk"><p><? echo chop($data_array[$row[csf("id")]]['lot_no'],","); ?></p></td>
                                    <td class="wrd_brk" align="center"><p><? echo $compliance_arr[$row[csf("is_compliance")]]; ?></p></td>
								</tr>
								<?
								$i++;
								$totalStockValue+=$stockValue;
								$totalpipeLine_qty+=$pipeLine_qty;
							}
						}
					}
				}
			?>
			</table>
			</div>
			<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
				<tfoot>
					<tr>
						<th width="30">&nbsp;</th>
						<th width="110">&nbsp;</th>
                        <th width="60">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="70">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="100">Total:</th>
                        <th width="100" class="wrd_brk" align="right" id="value_tot_open_bl"><? echo number_format($tot_open_bl,4); ?></th>
                        <th width="110" class="wrd_brk" align="right" id="value_tot_open_bl_amt"><? echo number_format($tot_open_bl_amt,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_purchase"><? echo number_format($tot_purchase,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_transfer_in_qty"><? echo number_format($tot_transfer_in_qty,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_issue_return"><? echo number_format($tot_issue_return,3); ?></th>
						<th width="100" class="wrd_brk" align="right" id="value_tot_total_receive"><? echo number_format($tot_total_receive,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_issue"><? echo number_format($tot_issue,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_transfer_out_qty"><? echo number_format($tot_transfer_out_qty,3); ?></th>
						<th width="80" class="wrd_brk" align="right" id="value_tot_rec_return"><? echo number_format($tot_rec_return,3); ?></th>
						<th width="100" class="wrd_brk" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue,3); ?></th>
						<th width="100" class="wrd_brk" align="right" id="value_tot_closing_stock"><? echo number_format($tot_closing_stock,4); ?></th>
						<th width="80" class="wrd_brk">&nbsp;</th>
						<th width="120" class="wrd_brk" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
						<th width="80" class="wrd_brk">&nbsp;</th>
						<th width="120" class="wrd_brk">&nbsp;</th>

						<?
							if($cbo_store_wise==1)
							{
								?>
									<th width="100">&nbsp;</th>
								<?
							}
						?>
						<th width="60" class="wrd_brk">&nbsp;</th>
						<th width="100" class="wrd_brk" align="right" id="value_totalpipeLine_qty"><? echo number_format($totalpipeLine_qty,2); ?></th>
						<th width="150">&nbsp;</th>
                        <th>&nbsp;</th>
					</tr>
				</tfoot>
			</table>
        </div>
        <?
	}

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("$user_id*.xls") as $filename) {
    //if( @filemtime($filename) < (time()-$seconds_old) )
    @unlink($filename);
    }
    //---------end------------//
    $name=time();
    $filename=$user_id."_".$name.".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, $html);
    echo "$html**$filename**$report_type";
    exit();
}


if($action=="pipe_line_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$product_sql=sql_select("select id,  item_category_id, item_group_id, sub_group_name, item_description, product_name_details, item_size from product_details_master where id=$prod_id");
	$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
    <table width="630" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="50">Product Id</th>
                <th width="120">Item Category</th>
                <th width="100">Item Group</th>
                <th width="100">Item Sub-group</th>
                <th width="150">Item Description</th>
                <th >Item Size</th>
            </tr>
        </thead>
        <tbody>
        	<tr>
            	<td align="center"><p><? echo $product_sql[0][csf("id")]; ?>&nbsp;</p></td>
                <td><p><? echo $item_category[$product_sql[0][csf("item_category_id")]]; ?>&nbsp;</p></td>
                <td><p><? echo $item_group_name; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("sub_group_name")]; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("item_description")]; ?>&nbsp;</p></td>
                <td><p><? echo $product_sql[0][csf("item_size")]; ?>&nbsp;</p></td>
            </tr>
        </tbody>
    </table>
    <br />
    <table width="680" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="70">WO/PI Date</th>
                <th width="100">WO/PI No</th>
                <th width="30">Type</th>
                <th width="70">Pay Mode</th>
                <th width="70">UOM</th>
                <th width="80">WO/PI Qty.</th>
                <th width="80">Rcv. Qnty</th>
                <th width="80">Balance</th>
                <th >Remarks</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		$rcb_qnty_sql="SELECT A.BOOKING_ID, B.RECEIVE_BASIS, B.CONS_QUANTITY from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.prod_id=$prod_id and b.status_active=1";
		//echo $rcb_qnty_sql;
		$rcb_qnty_sql_result=sql_select($rcb_qnty_sql);
		$rcv_qnty_array=array();
		foreach($rcb_qnty_sql_result as $val)
		{
			$rcv_qnty_array[$val["BOOKING_ID"]][$val["RECEIVE_BASIS"]]+=$val["CONS_QUANTITY"];
		}
		unset($rcb_qnty_sql_result);
		
		$details_sql="SELECT b.id as wo_po_id, b.wo_number as wo_po_no, b.wo_date as wo_po_date, b.pay_mode as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.supplier_order_quantity) as wo_po_qnty, 2 as type 
		from wo_non_order_info_mst b, wo_non_order_info_dtls c 
		where b.id=c.mst_id and c.item_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.item_category_id in (5,6,7,23) and b.pay_mode<>2 and c.is_deleted=0 group by b.id, b.wo_number, b.wo_date, b.pay_mode
		union all
		SELECT b.id as wo_po_id, b.pi_number as wo_po_no, b.pi_date as wo_po_date, 0 as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.quantity) as wo_po_qnty, 1 as type 
		from com_pi_master_details b, com_pi_item_details c 
		where b.id=c.pi_id and b.item_category_id in (5,6,7,23) and c.item_prod_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.pi_number, b.pi_date";
		//echo $details_sql;
		$sql_result=sql_select($details_sql);
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$rcv_qnty=$rcv_qnty_array[$row[csf("wo_po_id")]][$row[csf("type")]];
			$balance=$row[csf("wo_po_qnty")]-$rcv_qnty;
			if($row[csf("type")]==2) $type="WO"; else $type="PI";

        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                <td><p><? if($row[csf("wo_po_date")]!="" && $row[csf("wo_po_date")]!="0000-00-00") echo change_date_format($row[csf("wo_po_date")]); ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("wo_po_no")]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $type; ?>&nbsp;</p></td>
                <td><p><? echo $pay_mode[$row[csf("wo_po_mode")]]; ?>&nbsp;</p></td>
                <td><p><? echo $unit_of_measurement[$row[csf("wo_po_uom")]]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($row[csf("wo_po_qnty")],0); $total_wo_qnty+=$row[csf("wo_po_qnty")]; ?></p></td>
                <td align="right" title="<? echo $row[csf("wo_po_id")]; ?>"><p><? echo number_format($rcv_qnty,0); $total_rcv_qnty+=$rcv_qnty; ?></p></td>
                <td align="right"><p><? echo number_format($balance,0); $total_bal_qnty+=$balance;  ?></p></td>
                <td><p><? echo $row[csf("")]; ?>&nbsp;</p></td>
            </tr>
            <?
			$i++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
            	<th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >&nbsp;</th>
                <th >Total</th>
                <th ><? echo number_format($total_wo_qnty,0); ?></th>
                <th ><? echo number_format($total_rcv_qnty,0); ?></th>
                <th ><? echo number_format($total_bal_qnty,0); ?></th>
                <th >&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    <?

}

?>
