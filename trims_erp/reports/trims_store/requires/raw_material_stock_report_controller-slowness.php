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
	//echo $data;
	echo create_drop_down( "cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type=101 order by store_name","id,store_name", 1, "--Select Store--", 0, "",0 );
}

if($action=="print_button_variable_setting")
{
	$print_report_format=fnc_report_button($data,17,233,0);
    echo "print_report_button_setting('".$print_report_format."');\n";
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

	$sql="SELECT id, item_account, item_category_id, item_group_id, item_description, supplier_id, sub_group_name from product_details_master where company_id=$data[0] and item_category_id='$data[1]' $item_name and  status_active=1 and is_deleted=0";
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
		function js_set_value(id)
		{
			document.getElementById('item_name_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
    <input type="hidden" id="item_name_id" />
    <?
	if ($data[1]==0) $item_category =""; else $item_category =" and item_category in($data[1])";
	// $item_category;
	$sql="SELECT id, item_name from lib_item_group where status_active=1 and is_deleted=0 $item_category"; //id=$data[1] and

	echo  create_list_view("list_view", "Item Name", "350","500","330",0, $sql , "js_set_value", "id,item_name", "", 1, "0", $arr , "item_name", "periodical_purchase_report_controller",'setFilterGrid("list_view",-1);','0') ;
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
	$value_with=str_replace("'","",$value_with);
	$from_date=str_replace("'","",$from_date);
	$to_date=str_replace("'","",$to_date);
	$cbo_bond_status=str_replace("'","",$cbo_bond_status);
	$cbo_section=str_replace("'","",$cbo_section);
	
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$country_nameArr = return_library_array("select id,country_name from  lib_country where status_active=1 and is_deleted=0","id","country_name");
	
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and company_id = $cbo_company_name and  con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2 and status_active = 1 and company_id = $cbo_company_name)" , "conversion_rate" );

	$company_cond="";
	if ($cbo_company_name>0) $company_cond =" and company_id=$cbo_company_name";

	unset($mrr_rate_sql);
	$issue_rtn_sql=sql_select("select b.id as issue_trans_id, a.id as isssue_rtn_trans_id from inv_transaction a, inv_transaction b 
	where a.issue_id=b.mst_id and a.prod_id=b.prod_id and a.transaction_type=4 and a.item_category=101 and b.item_category=101 and b.transaction_type=2");
	$issue_rtn_data=array();
	foreach($issue_rtn_sql as $row)
	{
		$issue_rtn_data[$row[csf("isssue_rtn_trans_id")]]=$row[csf("issue_trans_id")];
	}
	
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(101)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
	if ($cbo_item_category_id==0) $item_category_cond=" and a.item_category in(101)"; else $item_category_cond=" and a.item_category='$cbo_item_category_id'";
	if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
	if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
	if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
	if ($item_group_id==0)  $group_id="";  else  $group_id=" and b.item_group_id=$item_group_id ";
	$search_cond="";
	//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";
	if($cbo_bond_status>0) $search_cond.=" and b.bond_status=$cbo_bond_status";
	if($cbo_section>0) $search_cond.=" and b.section_id=$cbo_section";
	
	$trans_sql="Select b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.order_qnty as ORDER_QNTY, a.order_amount as ORDER_AMOUNT, a.id as TRANS_ID, b.section_id
	from inv_transaction a, product_details_master b
	where a.prod_id=b.id and a.status_active=1 and b.item_category_id=101 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond order by b.id, a.id ASC";
	
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
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]+=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['purchase']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['purchase_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]+=$row_p["CONS_AMOUNT"];
			}
		}
		else if($row_p["TRANSACTION_TYPE"]==2)
		{
			if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"]; 
				$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['issue']+=$row_p["CONS_QUANTITY"];
				$test.=$row_p["TRANS_ID"]."===";
				$data_array[$row_p["PROD_ID"]]['issue_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
		}
		else if($row_p["TRANSACTION_TYPE"]==3)
		{
			if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['receive_return']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['receive_return_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
		}
		else if($row_p["TRANSACTION_TYPE"]==4)
		{
			$test.=$issue_rtn_data[$row_p["TRANS_ID"]]."---";
			if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]+=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['issue_return']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['issue_return_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]+=$row_p["CONS_AMOUNT"];
			}
		}
		else if($row_p["TRANSACTION_TYPE"]==5)
		{
			if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]+=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['item_transfer_receive']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['item_transfer_receive_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]+=$row_p["CONS_AMOUNT"];
			}
		}
		else if($row_p["TRANSACTION_TYPE"]==6)
		{
			if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['item_transfer_issue_amt']+=$row_p["CONS_AMOUNT"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
		}
		if($batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=="" && $row_p["LOT_NO"] !="")
		{
			$batch_lot_check[$row_p["PROD_ID"]][$row_p["LOT_NO"]]=$row_p["LOT_NO"];
			$data_array[$row_p["PROD_ID"]]['lot_no'].=$row_p["LOT_NO"].",";
		}
	}
	//echo $test;die;
	//echo "<pre>";print_r($data_array);die;
	//echo "test2";die;
	//var_dump($data_array);die;
	//print_r($data_array);die;
	//echo $data_array[4103]['rcv_total_wo'].Fuad;
	if ($cbo_item_category_id==0) $item_category_cond=" and item_category in(101)"; else $item_category_cond=" and item_category='$cbo_item_category_id'";
	$returnRes_date="select prod_id as PROD_ID, min(transaction_date) as MIN_DATE, max(transaction_date) as MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 $item_category_cond group by prod_id";
	$result_returnRes_date = sql_select($returnRes_date);
	foreach($result_returnRes_date as $row)
	{
		$date_array[$row["PROD_ID"]]['min_date']=$row["MIN_DATE"];
		$date_array[$row["PROD_ID"]]['max_date']=$row["MAX_DATE"];
	}
	

	$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (101) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

	$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
	$i=1;
	ob_start();
	?>
	<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:3168px;">
		<table width="3168" cellpadding="0" cellspacing="0" id="caption" align="center">
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
		<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="3168" rules="all" id="rpt_table_header">
			<thead>
				<tr>
					<th  width="50">SL</th>
					<th width="60">Prod. ID</th>
					<th width="100">Item group</th>
					<th width="100">Sub group name</th>
					<th width="180">Item Description</th>
                    <th width="100">Section</th>
                    <th width="100">Brand Name</th>
					<th width="70">Item Size</th>
					<th width="80">Origin</th>
                    <th width="60">Cons. UOM</th>

					<th width="100">Opening Stock Qty</th>
					<th width="100"> Opneing Rate ($)</th>
					<th width="110">Opening Stock Value ($)</th>
					<th width="80">Rcv Qty [Purchase]</th>
					<th width="80">Rcv Value [Purchase] ($)</th>
					<th width="80">Issue Rtn Qty</th>
					<th width="80">Issue Rtn Value ($)</th>
					<th width="100">TTL Rcv Qty</th>
					<th width="100">TTL Rcv Value ($)</th>
					<th width="80">Issue Qty</th>

					<th width="80">Issue Value ($)</th>
					<th width="80">Rcv Rtn Qty</th>
					<th width="80">Rcv Rtn Value ($)</th>
					<th width="100">TTL Issue Qty</th>
					<th width="100">TTL Issue Value ($)</th>
					<th  width="100">Closing Stock Qty (Cons.UOM)</th>
                    <th  width="60">Order UOM</th>
                    <th  width="100">Closing Stock Qty (Order UOM)</th>
					<th  width="80">Avg. Rate ($)</th>
					<th  width="120">Closing Stock Value ($)</th>
                    <th  width="120">Closing Stock Value (Tk)</th>
					<th  width="60">DOH</th>
					<th  width="100">Pipe Line</th>
					<th>Remarks</th>
				</tr>
			</thead>
		</table>
		<div style="width:3168px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="3150" rules="all" align="left">
		<?
			$sql="select b.id, b.item_code, b.item_category_id, b.item_group_id, b.unit_of_measure, b.item_description, b.sub_group_name, b.current_stock, b.avg_rate_per_unit, b.item_size,b.origin, b.brand_name, b.order_uom, b.conversion_factor, b.section_id
			from product_details_master b 
			where b.status_active=1 and b.is_deleted=0 and b.item_category_id=101 and b.company_id='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $group_id $search_cond order by b.id";
			//echo $sql;die;
			$result = sql_select($sql);
			foreach($result as $row)
			{
				if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
				if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
					$stylecolor='color:#A61000;';
				else
					$stylecolor='color:#000000;';
				$ageOfDays = datediff("d",$date_array[$row[csf("id")]]['min_date'],date("Y-m-d"));
				$daysOnHand = datediff("d",$date_array[$row[csf("id")]]['max_date'],date("Y-m-d"));


				$issue_qty=$data_array[$row[csf("id")]]['issue'];
				$issue_value=$data_array[$row[csf("id")]]['issue_amt'];

				$transfer_out_qty=$data_array[$row[csf("id")]]['item_transfer_issue'];
				$transfer_in_qty=$data_array[$row[csf("id")]]['item_transfer_receive'];

				$transfer_out_qty_amt=$data_array[$row[csf("id")]]['item_transfer_issue_amt'];
				$transfer_in_qty_amt=$data_array[$row[csf("id")]]['item_transfer_receive_amt'];

				$openingBalance = $data_array[$row[csf("id")]]['rcv_total_opening']-$data_array[$row[csf("id")]]['iss_total_opening'];
				$rcv_total_opening_amt=$data_array[$row[csf("id")]]['rcv_total_opening_amt']/$currency_rate;
				$iss_total_opening_amt=$data_array[$row[csf("id")]]['iss_total_opening_amt']/$currency_rate;
				$openingBalanceValue = $rcv_total_opening_amt-$iss_total_opening_amt;
                $openingBalanceValueTk = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
				//$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
				
				if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
				
				$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
				$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;

				$totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']/$currency_rate+$data_array[$row[csf("id")]]['issue_return_amt']/$currency_rate+$transfer_in_qty_amt/$currency_rate;

                $totalReceive_amt_tk = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;
				//$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;
				$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']/$currency_rate+$data_array[$row[csf("id")]]['receive_return_amt']/$currency_rate+$transfer_out_qty_amt/$currency_rate;
                $totalIssue_amt_tk = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

				$closingStock=$openingBalance+$totalReceive-$totalIssue;
				$stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
                $stockValue_tk=$openingBalanceValueTk+$totalReceive_amt_tk-$totalIssue_amt_tk;

				if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;

				$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['purchase'];
				
				if($pipeLine_qty<0) $pipeLine_qty=0;
				//echo $closingStock."=".$openingBalance."<br>";
				$closing_stock_ord=$closingStock/$row[csf("conversion_factor")];
				//$closing_amount=$closing_stock_ord*$avg_rate*$currency_rate;
                $closing_amount=$stockValue_tk;

                if($value_with==1)
				{
					if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
					{
						//$closing_amount=0;
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="60"><? echo $row[csf("id")]; ?></td>
							<td width="100"><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></td>
							<td width="100"><? echo $row[csf("sub_group_name")]; ?></td>
							<td width="180"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $trims_section[$row[csf("section_id")]]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $row[csf("brand_name")]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $row[csf("item_size")]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $country_nameArr[$row[csf("origin")]]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>

                            <td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
                            <td width="110" align="right"><p><? //echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; 
							if(number_format($openingBalance,3)>0.000){
							 echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue;}else { echo "0.000"; $tot_open_bl_amt+="0.000";} 
							?></p></td>
							<td width="80" align="right"><a href="##" onclick="fnc_purchase_details('<? echo $row[csf("id")]."_".$from_date."_".$to_date;?>','purchase_popup')"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></a></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amt']/$currency_rate,3); $tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amt']/$currency_rate; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return_amt']/$currency_rate,3); $tot_issue_return_amount+=$data_array[$row[csf("id")]]['issue_return_amt']/$currency_rate;//$row[csf("issue_return")]; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalReceive_amt,3); $tot_total_receive_amount+=$totalReceive_amt; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>

							<td width="80" align="right"><p><? echo number_format($issue_value/$currency_rate,3); $tot_total_issue_amount+=$issue_value/$currency_rate; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return_amt']/$currency_rate,3); $tot_rec_return_amount+=$data_array[$row[csf("id")]]['receive_return_amt']/$currency_rate;//$issue_result[0][csf('receive_return')]; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalIssue_amt,3); $tot_total_issue+=$totalIssue_amt; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
                            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closing_stock_ord,3); $tot_closing_stock_ord+=$closing_stock_ord; ?></p></td>
							<td width="80" align="right" title="<?= $avg_rate; ?>"><p><? echo number_format($avg_rate,3); //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
                            <td width="120" align="right"><p><?  //echo number_format($stockValue,3); $tot_stock_value+=$stockValue; 
							if(number_format($closingStock,3)>0.000){
							 echo number_format($stockValue,3); $tot_stock_value+=$stockValue;}else { echo "0.000"; $tot_stock_value+="0.000";} 
							?></p></td>
							
                            <td width="120" align="right"><p><?
                             echo number_format($closing_amount,3); $value_tot_stock_value_tka+=$closing_amount; ?></p></td>
							<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?>&nbsp;</p></td>
							<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
							<td><p><? echo "";//chop($data_array[$row[csf("id")]]['lot_no'],","); ?>&nbsp;</p></td>
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
							<td width="60"><? echo $row[csf("id")]; ?></td>
							<td width="100"><? echo $itemgroupArr[$row[csf("item_group_id")]]; ?></td>
							<td width="100"><? echo $row[csf("sub_group_name")]; ?></td>
							<td width="180"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $trims_section[$row[csf("section_id")]]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo $row[csf("brand_name")]; ?>&nbsp;</p></td>
							<td width="70"><p><? echo $row[csf("item_size")]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $country_nameArr[$row[csf("origin")]];  ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>

                            <td width="100" align="right"><p><? echo number_format($openingBalance,3); $tot_open_bl+=$openingBalance; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($openingRate,3); ?></p></td>
                            <td width="110" align="right"><p><? //echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue; 
							if(number_format($openingBalance,3)>0.000){
							 echo number_format($openingBalanceValue,3); $tot_open_bl_amt+=$openingBalanceValue;}else { echo "0.000"; $tot_open_bl_amt+="0.000";} 
							?></p></td>
							<td width="80" align="right"><a href="##" onclick="fnc_purchase_details('<? echo $row[csf("id")]."_".$from_date."_".$to_date; ?>','purchase_popup')"><p><? echo number_format($data_array[$row[csf("id")]]['purchase'],3); $tot_purchase+=$data_array[$row[csf("id")]]['purchase']; ?></p></a></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amt']/$currency_rate,3); $tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amt']/$currency_rate; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return_amt']/$currency_rate,3); $tot_issue_return_amount+=$data_array[$row[csf("id")]]['issue_return_amt']/$currency_rate;//$row[csf("issue_return")]; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalReceive_amt,3); $tot_total_receive_amount+=$totalReceive_amt; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>

							<td width="80" align="right"><p><? echo number_format($issue_value/$currency_rate,3); $tot_issue_value+=$issue_value/$currency_rate; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return_amt']/$currency_rate,3); $tot_rec_return_amount+=$data_array[$row[csf("id")]]['receive_return_amt']/$currency_rate;//$issue_result[0][csf('receive_return')]; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalIssue_amt,3); $tot_total_issue_amount+=$totalIssue_amt; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
                            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closing_stock_ord,3); $tot_closing_stock_ord+=$closing_stock_ord; ?></p></td>
							<td width="80" align="right" title="<?= $avg_rate; ?>"><p><? echo number_format($avg_rate,3); //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
                            <td width="120" align="right"><p><?
							if(number_format($closingStock,3)>0.000){
							 echo number_format($stockValue,3); $tot_stock_value+=$stockValue;}else { echo "0.000"; $tot_stock_value+="0.000";} ?></p></td>

                            <td width="120" align="right"><p><? 
                            echo number_format($closing_amount,3); $value_tot_stock_value_tka+=$closing_amount; ?></p></td>
							<td width="60" align="center"><p><? echo $daysOnHand;//$daysOnHand; ?>&nbsp;</p></td>
							<td width="100" align="right" title="<? echo $wo_qty_arr[$row[csf("id")]]."==".$pi_qty_arr[$row[csf("id")]]; ?>"><a href="##" onclick="fnc_pipeLine_details('<? echo $row[csf("id")];?>','pipe_line_popup')"><p><? echo number_format($pipeLine_qty,2); ?></p></a></td>
							<td><p><? echo "";//chop($data_array[$row[csf("id")]]['lot_no'],","); ?>&nbsp;</p></td>
						</tr>
						<?
						$i++;
						$totalStockValue+=$stockValue;
						$totalpipeLine_qty+=$pipeLine_qty;
					}
				}
			}
		?>
		</table>
		</div>
		<table width="3168" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
			<tfoot>
				<tr>
					<th width="50">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="180">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="100">&nbsp;</th>
					<th width="70">&nbsp;</th>
					<th width="80">&nbsp;</th>
                    <th width="60">&nbsp;</th>

					<th width="100">Total:</th>
					<th width="100" align="right" id=""><? //echo number_format($tot_open_bl,3); ?></th>
					<th width="110" align="right" id="value_tot_purchase_amount"><? echo number_format($tot_open_bl_amt,3); ?></th>
					<th width="80" align="right" id="value_tot_purchase"><? //echo number_format($tot_purchase,3); ?></th>
					<th width="80" align="right" id="value_tot_issue_return_amount"><? echo number_format($tot_purchase_amount,3); ?></th>
					<th width="80" align="right" id="value_tot_transfer_in_qty"><? //echo number_format($tot_issue_return,3); ?></th>
					<th width="80" align="right" id="value_tot_total_receive_amount"><? echo number_format($tot_issue_return_amount,3); ?></th>
					<th width="100" align="right" id="value_tot_total_receive"><? //echo number_format($tot_total_receive,3); ?></th>
					<th width="100" align="right" id="value_tot_issue_value"><? echo number_format($tot_total_receive_amount,3); ?></th>
					<th width="80" align="right" id="value_tot_issue"><? //echo number_format($tot_issue,3); ?></th>

					<th width="80" align="right" id="value_tot_rec_return_amount"><? echo number_format($tot_issue_value,3); ?></th>
					<th width="80" align="right" id="value_tot_rec_return"><? //echo number_format($tot_rec_return,3); ?></th>
					<th width="80" align="right" id="value_tot_total_issue_amount"><? echo number_format($tot_rec_return_amount,3); ?></th>
					
					<th width="100" align="right" id="value_tot_stock_valu"><p><?// echo number_format($tot_stock_value,2); ?></p></th>
					<th width="100" align="right" id="value_tot_total_issue"><? echo number_format($tot_total_issue_amount,3); ?></th>
                    <th width="100" align="right" id="value_tot_closing_stock"><? //echo number_format($tot_closing_stock,3); ?></th>
                    <th width="60" align="right"></th>
                    <th width="100" align="right" id="value_tot_closing_stock_ord"><? //echo number_format($tot_closing_stock_ord,3); ?></th>
					<th width="80">&nbsp;</th>
					<th width="120" align="right" id="value_tot_stock_value"><p><? echo number_format($tot_stock_value,2); ?></p></th>
					
                    <th width="120" align="right" id="value_tot_stock_value_tk"><p><? echo number_format($value_tot_stock_value_tka,2); ?></p></th>
					<th width="60">&nbsp;</th>
					<th width="100" align="right" id="value_totalpipeLine_qty"><? //echo number_format($totalpipeLine_qty,2); ?></th>
					<th>&nbsp;</th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
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

if($action=="generate_report2")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$report_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$item_account_id=str_replace("'","",$item_account_id);
	$item_group_id=str_replace("'","",$item_group_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$value_with=str_replace("'","",$value_with);
	$from_date=str_replace("'","",$from_date);
	$to_date=str_replace("'","",$to_date);
	$cbo_bond_status=str_replace("'","",$cbo_bond_status);
	$cbo_section=str_replace("'","",$cbo_section);
	
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
	$itemgroupArr = return_library_array("select id,item_name from  lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	$country_nameArr = return_library_array("select id,country_name from  lib_country where status_active=1 and is_deleted=0","id","country_name");
	
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2)" , "conversion_rate" );
	
	$company_cond="";
	if ($cbo_company_name>0) $company_cond =" and company_id=$cbo_company_name";
	$mrr_rate_sql=sql_select("select c.exchange_rate, a.recv_trans_id, a.issue_trans_id, a.issue_qnty, a.amount 
	from inv_mrr_wise_issue_details a, inv_transaction b, inv_receive_master c
	where a.recv_trans_id=b.id and b.mst_id=c.id and b.item_category=101 and a.entry_form in(264,265) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	$mrr_rate_arr=array();
	foreach($mrr_rate_sql as $row)
	{
		$mrr_rate_arr[$row[csf("issue_trans_id")]]["exchange_rate"]=$row[csf("exchange_rate")];
		$mrr_rate_arr[$row[csf("issue_trans_id")]]["issue_qnty"]+=$row[csf("issue_qnty")];
		if($row[csf("exchange_rate")]==1)
		{
			$mrr_rate_arr[$row[csf("issue_trans_id")]]["order_amount"]+=$row[csf("amount")]/$currency_rate;
		}
		else
		{
			$mrr_rate_arr[$row[csf("issue_trans_id")]]["order_amount"]+=$row[csf("amount")]/$row[csf("exchange_rate")];
		}
	}
	//echo "<pre>";print_r($mrr_rate_arr);die;
	unset($mrr_rate_sql);
	$issue_rtn_sql=sql_select("select b.id as issue_trans_id, a.id as isssue_rtn_trans_id from inv_transaction a, inv_transaction b 
	where a.issue_id=b.mst_id and a.prod_id=b.prod_id and a.transaction_type=4 and a.item_category=101 and b.item_category=101 and b.transaction_type=2");
	$issue_rtn_data=array();
	foreach($issue_rtn_sql as $row)
	{
		$issue_rtn_data[$row[csf("isssue_rtn_trans_id")]]=$row[csf("issue_trans_id")];
	}
	
	if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
	if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(101)"; else $item_category_id=" and b.item_category_id='$cbo_item_category_id'";
	if ($cbo_item_category_id==0) $item_category_cond=" and a.item_category in(101)"; else $item_category_cond=" and a.item_category='$cbo_item_category_id'";
	if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
	if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
	if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
	if ($item_group_id==0)  $group_id="";  else  $group_id=" and b.item_group_id=$item_group_id ";
	$search_cond="";
	//if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";
	if($cbo_bond_status>0) $search_cond.=" and b.bond_status=$cbo_bond_status";
	if($cbo_section>0) $search_cond.=" and b.section_id=$cbo_section";
	
	$rcv_sql="Select c.id as RCV_ID, b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.order_qnty as ORDER_QNTY, a.order_amount as ORDER_AMOUNT, a.id as TRANS_ID, b.section_id,  c.supplier_id , c.recv_number, c.receive_date,b.item_group_id, b.brand_name, b.origin, b.unit_of_measure,b.item_description
	from inv_transaction a, product_details_master b, inv_receive_master c
	where a.mst_id=c.id and c.entry_form=263 and a.transaction_type=1 and a.prod_id=b.id and a.status_active=1 and c.status_active=1 and c.is_deleted=0  and b.item_category_id=101 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond order by b.id, a.id ASC";
	
	//echo $rcv_sql;
	$mrrr_result = sql_select($rcv_sql);
	//echo count($trnasactionData).jahid;die;
         
	foreach($mrrr_result as $row){
		$prod_id[$row['PROD_ID']]=$row['PROD_ID'];
	}
     $prd_id_in=where_con_using_array($prod_id,0,'a.prod_id');

	 $stock_sql= "SELECT a.PROD_ID, sum(case when a.transaction_type in (1,4,5) then a.cons_quantity else 0 end) as TOTAL_RECEIVE,
	sum(case when a.transaction_type in (2,3,6) then a.cons_quantity else 0 end) as TOTAL_ISSUE, sum(case when a.transaction_type in (1,4,5) then a.cons_amount else 0 end) as TOTAL_RECEIVE_AMMOUNT,
	sum(case when a.transaction_type in (2,3,6) then a.cons_amount else 0 end) as TOTAL_ISSUE_AMMOUNT
	from  inv_transaction a where  a.status_active=1  $prd_id_in
	group by  a.prod_id";
	//echo $stock_sql;
    $stock_data=sql_select($stock_sql);


	$stock_data_array=array();
	

	foreach($stock_data as $row){
		$stock_data_array[$row["PROD_ID"]]["cons_quantity"]=$row["TOTAL_RECEIVE"]-$row["TOTAL_ISSUE"];
		$stock_data_array[$row["PROD_ID"]]["cons_amount"]=$row["TOTAL_RECEIVE_AMMOUNT"]-$row["TOTAL_ISSUE_AMMOUNT"];
	}
	
    //print_r($stock_data_array);
	$data_array=array();
	
	//echo $test;die;
	//echo "<pre>";print_r($data_array);die;
	//echo "test2";die;
	//var_dump($data_array);die;
	//print_r($data_array);die;
	//echo $data_array[4103]['rcv_total_wo'].Fuad;
	if ($cbo_item_category_id==0) $item_category_cond=" and item_category in(101)"; else $item_category_cond=" and item_category='$cbo_item_category_id'";
	$returnRes_date="select prod_id as PROD_ID, min(transaction_date) as MIN_DATE, max(transaction_date) as MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 $item_category_cond group by prod_id";
	$result_returnRes_date = sql_select($returnRes_date);
	foreach($result_returnRes_date as $row)
	{
		$date_array[$row["PROD_ID"]]['min_date']=$row["MIN_DATE"];
		$date_array[$row["PROD_ID"]]['max_date']=$row["MAX_DATE"];
	}
	

	$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.item_category in (101) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

	$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (101) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");
	$i=1;
	ob_start();
	?>
	<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:2168px;">
		<table width="2168" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead>
				<tr style="border:none;">
					<td colspan="10" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="10" class="form_caption" align="center" style="border:none; font-size:14px;">
					   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="10" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
						<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
					</td>
				</tr>
			</thead>
		</table>
		<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="2168" rules="all" id="rpt_table_header">
			<thead>
				<tr>
					<th  width="30">SL</th>
					<th width="100">Company Name</th>
					<th width="50">Prod.ID</th>
					<th width="120">Item Description</th>
                    <th width="70">Brand Name</th>
                    <th width="100">Supplier</th>
					<th width="80">Origin</th>
                    <th width="60">Cons. UOM</th>

					<th width="60">Stock In Hand</th>
					<th width="60">Avg. Rate (USD)</th>
					<th width="100">Stock Value (USD)</th>
					<th width="80">MRR No</th>
					<th width="80">Receive Date</th>
					<th width="80">Age (Days)</th>
					</tr>
			</thead>
		</table>
		<div style="width:2168px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="2168" rules="all" align="left">
		<?
		
			foreach($mrrr_result as $row)
			{
				if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
				
				$stock_qty=$stock_data_array[$row["PROD_ID"]]["cons_quantity"];;
				$stock_vlu=$stock_data_array[$row["PROD_ID"]]["cons_amount"];
				$stock_avg_rate=$stock_vlu/$stock_qty;

				if(($value_with==0)||($value_with==1 && $stock_qty>0))
				{
					 
					        $date1=date_create($row[csf("receive_date")]);
							$date2=date_create(date("d-M-Y")) ;
							$diff=date_diff($date1,$date2);
							
					
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30" align="center"><? echo $i; ?></td>
							<td width="100"><? echo $companyArr[$cbo_company_name]; ?></td>
							<td width="50"><? echo $row[csf("prod_id")]; ?></td>
							<td width="120"><p><? echo $row[csf("item_description")]; ?>&nbsp;</p></td>
                            <td width="70"><p><? echo $row[csf("brand_name")]; ?>&nbsp;</p></td>
                            <td width="100"><p><? echo  $supplierArr[$row[csf("supplier_id")]]; ?>&nbsp;</p></td>
							<td width="80" align="center"><p><? echo $country_nameArr[$row[csf("origin")]]; ?>&nbsp;</p></td>
                            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("unit_of_measure")]]; ?>&nbsp;</p></td>

                            <td width="60" align="right"><p> <a href="##" onclick="fnc_receive_details('<? echo $row[csf("prod_id")];?>','<? echo $row[csf("rcv_id")];?>','receive_details_popup')"> <? echo number_format($row[csf("cons_quantity")],2);  //echo number_format($stock_qty,2);  ?></a></p></td>

						
							<td width="60" align="right"><p><? echo number_format($stock_avg_rate,2);?></p></td>
                            <td width="100" align="right"><p><? echo number_format($stock_vlu,2) ;?></p></td>
							<td width="80" align="right"><p><? echo $row[csf("recv_number")]; ?></p></td>
							<td width="80" align="right"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
							<td width="80" align="center"><p><? echo  $diff->format("%a");?></p></td>
							
						</tr>
						<?
					
				}
			$i++;
			 $total_stock_val+=$stock_vlu;

			}
		?>
		</table>
		</div>
		<table width="2168" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
			<tfoot>
				<tr>
					<th width="30">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="50">&nbsp;</th>
					<th width="120">&nbsp;</th>
                    <th width="70">&nbsp;</th>
					<th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="60"></th>
					<th width="60" align="right" id=""><? //echo number_format($tot_open_bl,3); ?></th>
					<th width="60" align="right" id="value_tot_purchase_amount"><? //echo number_format($tot_open_bl_amt,3); ?></th>
					<th width="100" align="center" id="value_tot_issue_return_amount"><? echo number_format($total_stock_val,2); ?></th>
					<th width="80" align="right" id="value_tot_issue_return_amount"><? //echo number_format($tot_purchase_amount,3); ?></th>
					<th width="80" align="right" id="value_tot_transfer_in_qty"><? //echo number_format($tot_issue_return,3); ?></th>
					<th width="80" align="right" id="value_tot_total_receive_amount"><?// echo number_format($tot_issue_return_amount,3); ?></th>
					
				</tr>
			</tfoot>
		</table>
	</div>
	<?

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
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

if($action=="generate_report4")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$report_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$item_account_id=str_replace("'","",$item_account_id);
	$item_group_id=str_replace("'","",$item_group_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$value_with=str_replace("'","",$value_with);
	$from_date=str_replace("'","",$from_date);
	$to_date=str_replace("'","",$to_date);
	$cbo_bond_status=str_replace("'","",$cbo_bond_status);
	$cbo_section=str_replace("'","",$cbo_section);
	
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	
	//$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	//$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	//$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
	//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
	$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0","id","item_name");
	//$country_nameArr = return_library_array("select id,country_name from  lib_country where status_active=1 and is_deleted=0","id","country_name");
	
	//$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2)" , "conversion_rate" );
	
	$sql_cond="";
	if ($cbo_company_name>0) $sql_cond .=" and c.company_id=$cbo_company_name";
	if($item_group_id>0) $sql_cond.=" and c.ITEM_GROUP_ID=$item_group_id";
	if($item_account_id !="") $sql_cond.=" and c.id in($item_account_id)";
	if($cbo_section>0) $sql_cond.=" and c.SECTION_ID=$cbo_section";
	if($cbo_bond_status>0) $sql_cond.=" and b.bond_status=$cbo_bond_status";
	
	$store_cond="";
	if($cbo_store_name>0) $store_cond.=" and b.store_id=$cbo_store_name";
	$date_cond="";
	if($from_date !="" && $to_date !="") $date_cond=" and b.transaction_date between '$from_date' and '$to_date'";
	
	$mrr_wise_sql="select b.MST_ID, a.RECV_TRANS_ID, a.ISSUE_TRANS_ID, a.ISSUE_QNTY, a.AMOUNT, b.PROD_ID, b.TRANSACTION_TYPE 
	from inv_mrr_wise_issue_details a, inv_transaction b, product_details_master c
	where a.ISSUE_TRANS_ID=b.id and b.prod_id=c.id and b.item_category=101 and a.entry_form in(264,265) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $store_cond";
	//echo $mrr_wise_sql;die;
	$mrr_wise_sql_result=sql_select($mrr_wise_sql);
	$mrr_data_arr=array();$mrr_issue_wise_rcv_id=array();$issue_rtn_wise_rcv_id=array();
	foreach($mrr_wise_sql_result as $row)
	{
		$tot_mrr_data_arr[$row["RECV_TRANS_ID"]]["ISSUE_QNTY"]+=$row["ISSUE_QNTY"];
		$mrr_data_arr[$row["RECV_TRANS_ID"]][$row["TRANSACTION_TYPE"]]["ISSUE_QNTY"]+=$row["ISSUE_QNTY"];
		$mrr_data_arr[$row["RECV_TRANS_ID"]][$row["TRANSACTION_TYPE"]]["ISSUE_TRANS_ID"]=$row["ISSUE_TRANS_ID"];
		$mrr_issue_wise_rcv_id[$row["ISSUE_TRANS_ID"]].=$row["RECV_TRANS_ID"].",";
		$issue_rtn_wise_rcv_id[$row["MST_ID"]][$row["PROD_ID"]]=$row["RECV_TRANS_ID"];
	}
	//print_r($issue_rtn_wise_rcv_id);
	//echo "<pre>";print_r($mrr_data_arr);die;
	unset($mrr_wise_sql_result);
	
	$current_trans_sql="select b.ID as TRANS_ID, b.MST_ID, b.TRANSACTION_TYPE, b.TRANSACTION_DATE, b.ISSUE_ID, b.PROD_ID, b.CONS_QUANTITY 
	from product_details_master c, inv_transaction b where c.id=b.prod_id and c.item_category_id=101 and b.item_category=101 and c.status_active=1 and b.status_active=1 $sql_cond $store_cond $date_cond";
	//echo $current_trans_sql;//die;
	$current_trans_sql_result=sql_select($current_trans_sql);
	$current_data=array();
	foreach($current_trans_sql_result as $row)
	{
		if($row["TRANSACTION_TYPE"]==1)
		{
			$current_data[$row["PROD_ID"]][$row["TRANS_ID"]]["RCV_QUANTITY"]+=$row["CONS_QUANTITY"];
		}
		else if($row["TRANSACTION_TYPE"]==2)
		{
			$rcv_trans_id_all=array_unique(explode(",",chop($mrr_issue_wise_rcv_id[$row["TRANS_ID"]],",")));
			foreach($rcv_trans_id_all as $rcv_id)
			{
				$current_data[$row["PROD_ID"]][$rcv_id]["ISSUE_QUANTITY"]=$mrr_data_arr[$rcv_id][$row["TRANSACTION_TYPE"]]["ISSUE_QNTY"];
			}
			
		}
		else if($row["TRANSACTION_TYPE"]==3)
		{
			$rcv_trans_id_all=array_unique(explode(",",chop($mrr_issue_wise_rcv_id[$row["TRANS_ID"]],",")));
			foreach($rcv_trans_id_all as $rcv_id)
			{
				$current_data[$row["PROD_ID"]][$rcv_id]["RCV_RTN_QUANTITY"]=$mrr_data_arr[$rcv_id][$row["TRANSACTION_TYPE"]]["ISSUE_QNTY"];
			}
			//$current_data[$row["PROD_ID"]][$mrr_issue_wise_rcv_id[$row["TRANS_ID"]]]["RCV_RTN_QUANTITY"]+=$row["CONS_QUANTITY"];
		}
		else if($row["TRANSACTION_TYPE"]==4)
		{
			$current_data[$row["PROD_ID"]][$issue_rtn_wise_rcv_id[$row["ISSUE_ID"]][$row["PROD_ID"]]]["ISSUE_RTN_QUANTITY"]+=$row["CONS_QUANTITY"];
		}
	}
	unset($current_trans_sql_result);
	//print_r($current_data);
	
	$mrr_sql="select a.ID, a.RECV_NUMBER_PREFIX_NUM, a.RECV_NUMBER, a.CURRENCY_ID, a.EXCHANGE_RATE, b.ID as TRANS_ID, b.ORDER_QNTY, b.ORDER_RATE, b.CONS_QUANTITY, b.CONS_RATE, c.ID as PROD_ID, c.ITEM_GROUP_ID, c.ITEM_DESCRIPTION, c.SECTION_ID, c.ITEM_SIZE 
	from inv_receive_master a, inv_transaction b, product_details_master c 
	where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=101 and c.item_category_id=101 and a.status_active=1 and b.status_active=1 and c.status_active=1 and c.CURRENT_STOCK>0 $sql_cond $store_cond
	order by TRANS_ID";
	//echo $mrr_sql;die;
	$mrr_sql_result=sql_select($mrr_sql);
	$report_data=array();
	foreach($mrr_sql_result as $row)
	{
		$current_rcv=$current_data[$row["PROD_ID"]][$row["TRANS_ID"]]["RCV_QUANTITY"];
		$current_issue=$current_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ISSUE_QUANTITY"];
		$current_rcv_rtn=$current_data[$row["PROD_ID"]][$row["TRANS_ID"]]["RCV_RTN_QUANTITY"];
		$current_issue_rtn=$current_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ISSUE_RTN_QUANTITY"];
		$mrr_balance=($row["CONS_QUANTITY"]+$current_issue+$current_rcv_rtn)-($tot_mrr_data_arr[$row["TRANS_ID"]]["ISSUE_QNTY"]);
		if($mrr_balance>0)
		{
			if($current_rcv>0)
			{
				$mrr_opening_stock=0;
			}
			else
			{
				$mrr_opening_stock=$mrr_balance;
				$current_rcv=0;
			}
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["PROD_ID"]=$row["PROD_ID"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["RECV_NUMBER_PREFIX_NUM"]=$row["RECV_NUMBER_PREFIX_NUM"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["RECV_NUMBER"]=$row["RECV_NUMBER"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["CURRENCY_ID"]=$row["CURRENCY_ID"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["EXCHANGE_RATE"]=$row["EXCHANGE_RATE"];
			
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ITEM_GROUP_ID"]=$row["ITEM_GROUP_ID"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ITEM_DESCRIPTION"]=$row["ITEM_DESCRIPTION"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["SECTION_ID"]=$row["SECTION_ID"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ITEM_SIZE"]=$row["ITEM_SIZE"];
			
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ORDER_QNTY"]=$row["ORDER_QNTY"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ORDER_RATE"]=$row["ORDER_RATE"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["CONS_QUANTITY"]=$row["CONS_QUANTITY"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["CONS_RATE"]=$row["CONS_RATE"];
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["MRR_OPENING_STOCK"]=$mrr_opening_stock;
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["MRR_OPENING_VALUE"]=$mrr_opening_stock*$row["ORDER_RATE"];
			
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["RCV_QNTY"]=$current_rcv;
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["RCV_VALUE"]=$current_rcv*$row["ORDER_RATE"];
			
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ISSUE_QNTY"]=$current_issue;
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ISSUE_VALUE"]=$current_issue*$row["ORDER_RATE"];
			
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["RCV_RTN_QNTY"]=$current_rcv_rtn;
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["RCV_RTN_VALUE"]=$current_rcv_rtn*$row["ORDER_RATE"];
			
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ISSUE_RTN_QNTY"]=$current_issue_rtn;
			$report_data[$row["PROD_ID"]][$row["TRANS_ID"]]["ISSUE_RTN_VALUE"]=$current_issue_rtn*$row["ORDER_RATE"];
		}
		
	}
	unset($mrr_sql_result);
	//echo "<pre>";print_r($report_data);die;
	$i=1;
	ob_start();
	?>
	<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:2150px;">
		<table width="2130" cellpadding="0" cellspacing="0" id="caption" align="left">
			<thead>
				<tr style="border:none;">
					<td colspan="10" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td colspan="10" class="form_caption" align="center" style="border:none; font-size:14px;">
					   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
					</td>
				</tr>
				<tr style="border:none;">
					<td colspan="10" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
						<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
					</td>
				</tr>
			</thead>
		</table>
		<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="2130" rules="all" id="rpt_table_header" align="left">
			<thead>
				<tr>
					<th  width="30">SL</th>
                    <th width="50">Prod.ID</th>
                    <th width="50">MRR No</th>
                    <th width="100">Item Group</th>
					<th width="120">Item Description</th>
                    <th width="100">Section</th>
                    <th width="80">Item Size</th>
                    <th width="80">Opening Stock Qty</th>
                    <th width="80">Opneing Rate ($)</th>
					<th width="100">Opening Stock Value ($)</th>
                    <th width="80">Rcv Qty [Purchase]</th>
					<th width="80">RATE ($)</th>
					<th width="100">Rcv Value [Purchase] ($)</th>
					<th width="80">Issue Rtn Qty</th>
					<th width="100">Issue Rtn Value ($)</th>
					<th width="80">TTL Rcv Qty</th>
					<th width="100">TTL Rcv Value ($)</th>
                    <th width="80">Issue Qty</th>
					<th width="100">Issue Value ($)</th>
					<th width="80">Rcv Rtn Qty</th>
					<th width="100">Rcv Rtn Value ($)</th>
					<th width="80">TTL Issue Qty</th>
					<th width="100">TTL Issue Value ($)</th>
                    <th width="80">Closing Stock Qty</th>
					<th>Closing Stock Value ($)</th>
				</tr>
			</thead>
		</table>
		<div style="width:2150px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body" align="left">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="2130" rules="all" align="left">
		<?
		
			foreach($report_data as $prod_id=>$prod_val)
			{
				foreach($prod_val as $rcv_trans_id=>$row)
				{
					if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="30" align="center"><? echo $i; ?></td>
                        <td width="50" align="center"><? echo $row["PROD_ID"];; ?></td>
                        <td width="50" align="center"><? echo $row["RECV_NUMBER_PREFIX_NUM"]; ?></td>
                        <td width="100" title="<?= $row["ITEM_GROUP_ID"];?>"><p><? echo $itemgroupArr[$row["ITEM_GROUP_ID"]]; ?>&nbsp;</p></td>
                        <td width="120"><p><? echo $row["ITEM_DESCRIPTION"]; ?>&nbsp;</p></td>
                        <td width="100" title="<?= $row["SECTION_ID"];?>"><p><? echo $trims_section[$row["SECTION_ID"]]; ?>&nbsp;</p></td>
                        <td width="80"><p><? echo $row["ITEM_SIZE"]; ?>&nbsp;</p></td>
                        <td width="80" align="right"><? echo number_format($row["MRR_OPENING_STOCK"],2);?></td>
                        <td width="80" align="right"><? if($row["MRR_OPENING_STOCK"]!=0) echo number_format($row["ORDER_RATE"],4);?></td>
                        <td width="100" align="right"><? echo number_format($row["MRR_OPENING_VALUE"],4);?></td>
                        <td width="80" align="right"><? echo number_format($row["RCV_QNTY"],2);?></td>
                        <td width="80" align="right"><? echo number_format($row["ORDER_RATE"],4);?></td>
                        <td width="100" align="right"><? echo number_format($row["RCV_VALUE"],4);?></td>
                        <td width="80" align="right"><? echo number_format($row["ISSUE_RTN_QNTY"],2);?></td>
                        <td width="100" align="right"><? echo number_format($row["ISSUE_RTN_VALUE"],4);?></td>
                        <td width="80" align="right"><? $tot_recv_qnty=$row["RCV_QNTY"]+$row["ISSUE_RTN_QNTY"]; echo number_format($tot_recv_qnty,2);?></td>
                        <td width="100" align="right"><? $tot_recv_value=$tot_recv_qnty*$row["ORDER_RATE"];  echo number_format($tot_recv_value,4);?></td>
                        <td width="80" align="right"><? echo number_format($row["ISSUE_QNTY"],2);?></td>
                        <td width="100" align="right"><? echo number_format($row["ISSUE_VALUE"],4);?></td>
                        <td width="80" align="right"><? echo number_format($row["RCV_RTN_QNTY"],2);?></td>
                        <td width="100" align="right"><? echo number_format($row["RCV_RTN_VALUE"],4);?></td>
                        <td width="80" align="right"><? $tot_issue_qnty=$row["ISSUE_QNTY"]+$row["RCV_RTN_QNTY"]; echo number_format($tot_issue_qnty,2);?></td>
                        <td width="100" align="right"><? $tot_issue_value=$tot_issue_qnty*$row["ORDER_RATE"];  echo number_format($tot_issue_value,4);?></td>
                        <td width="80" align="right"><? $closing_stock=($row["MRR_OPENING_STOCK"]+$tot_recv_qnty)-$tot_issue_qnty; echo number_format($closing_stock,2);?></td>
                        <td align="right"><? $closing_value=$closing_stock*$row["ORDER_RATE"]; echo number_format($closing_value,4);?></td>
                    </tr>
                    <?
					$i++;
					$total_opening_stock+=$row["MRR_OPENING_STOCK"];
					$total_opening_value+=$row["MRR_OPENING_VALUE"];
					$total_rcv_qnty+=$row["RCV_QNTY"];
					$total_rcv_value+=$row["RCV_VALUE"];
					$total_issue_rtn_qnty+=$row["ISSUE_RTN_QNTY"];
					$total_issue_rtn_value+=$row["ISSUE_RTN_VALUE"];					
					$total_gt_rcv_qnty+=$tot_recv_qnty;
					$total_gt_rcv_value+=$tot_recv_value;
					
					$total_issue_qnty+=$row["ISSUE_QNTY"];
					$total_issue_value+=$row["ISSUE_VALUE"];
					$total_rcv_rtn_qnty+=$row["RCV_RTN_QNTY"];
					$total_rcv_rtn_value+=$row["RCV_RTN_VALUE"];					
					$total_gt_issue_qnty+=$tot_issue_qnty;
					$total_gt_issue_value+=$tot_issue_value;
					
					$total_closing_stock+=$closing_stock;
					$total_closing_value+=$closing_value;
				}
			}
		?>
		</table>
		</div>
		<table width="2130" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" align="left">
			<tfoot>
				<tr>
					<th  width="30">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="100">&nbsp;</th>
					<th width="120">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="80" align="right">Total:</th>
                    <th width="80" align="right"><? echo number_format($total_opening_stock,2);?></th>
                    <th width="80">&nbsp;</th>
					<th width="100" align="right"><? echo number_format($total_opening_value,4);?></th>
                    <th width="80" align="right"><? echo number_format($total_rcv_qnty,2);?></th>
					<th width="80">&nbsp;</th>
					<th width="100" align="right"><? echo number_format($total_rcv_value,4);?></th>
					<th width="80" align="right"><? echo number_format($total_issue_rtn_qnty,2);?></th>
					<th width="100" align="right"><? echo number_format($total_issue_rtn_value,4);?></th>
					<th width="80" align="right"><? echo number_format($total_gt_rcv_qnty,2);?></th>
					<th width="100" align="right"><? echo number_format($total_gt_rcv_value,4);?></th>
                    <th width="80" align="right"><? echo number_format($total_issue_qnty,2);?></th>
					<th width="100" align="right"><? echo number_format($total_issue_value,4);?></th>
					<th width="80" align="right"><? echo number_format($total_rcv_rtn_qnty,2);?></th>
					<th width="100" align="right"><? echo number_format($total_rcv_rtn_value,4);?></th>
					<th width="80" align="right"><? echo number_format($total_gt_issue_qnty,2);?></th>
					<th width="100" align="right"><? echo number_format($total_gt_issue_value,4);?></th>
                    <th width="80" align="right"><? echo number_format($total_closing_stock,2);?></th>
					<th align="right"><? echo number_format($total_closing_value,4);?></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<?

    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) {
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

if($action=="generate_report3")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$report_type);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_item_category_id=str_replace("'","",$cbo_item_category_id);
	$item_account_id=str_replace("'","",$item_account_id);
	$item_group_id=str_replace("'","",$item_group_id);
	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$value_with=str_replace("'","",$value_with);
	$from_date=str_replace("'","",$from_date);
	$to_date=str_replace("'","",$to_date);
	$cbo_bond_status=str_replace("'","",$cbo_bond_status);
	$cbo_section=str_replace("'","",$cbo_section);
	
	if($db_type==0)
	{
		$from_date=change_date_format($from_date,'yyyy-mm-dd');
		$to_date=change_date_format($to_date,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$from_date=change_date_format($from_date,'','',1);
		$to_date=change_date_format($to_date,'','',1);
	}
	
	$companyArr = return_library_array("SELECT id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2)" , "conversion_rate" );
	
	$sql_cond=$sql_cond2="";
	if($cbo_company_name>0){ $sql_cond .=" and a.company_id=$cbo_company_name";  $sql_cond2 .=" and a.company_id=$cbo_company_name"; }	
	if($cbo_item_category_id==0){ $sql_cond.=" and b.item_category_id in(101)"; }
	else{ $sql_cond.=" and b.item_category_id=$cbo_item_category_id "; }
	if($cbo_item_category_id==0){ $sql_cond.=" and a.item_category in(101)"; }
	else{ $sql_cond.=" and a.item_category=$cbo_item_category_id "; }
	if($item_account_id>0){ $sql_cond.=" and a.prod_id in ($item_account_id)"; }
	if($item_account_id>0){ $sql_cond.=" and b.id in ($item_account_id)"; }
	if($cbo_store_name>0){ $sql_cond.=" and a.store_id=$cbo_store_name "; }
	if($item_group_id>0){ $sql_cond.=" and b.item_group_id=$item_group_id "; }
	if($cbo_bond_status>0){ $sql_cond.=" and b.bond_status=$cbo_bond_status"; }
	if($cbo_section>0){ $sql_cond.=" and c.section=$cbo_section"; $sql_cond2.=" and b.section=$cbo_section"; }
	
	$trans_sql="SELECT c.section as SECTION,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (1,4)  then a.cons_amount else 0 end) as OPEN_RCV_AMOUNT,
	sum(case when a.transaction_date<'$from_date' and a.transaction_type in (2,3)  then a.cons_amount else 0 end) as OPEN_ISS_AMOUNT,
	sum(case when a.transaction_type in (1,4) and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as CURR_RCV_AMOUNT,
	sum(case when a.transaction_type in (2,3) and a.transaction_date between '$from_date' and '$to_date' then a.cons_amount else 0 end) as CURR_ISS_AMOUNT
	from inv_transaction a, product_details_master b, lib_item_group c
	where a.prod_id=b.id and b.item_group_id=c.id and c.section>0 and b.item_category_id=101 and a.status_active=1 and b.status_active=1 $sql_cond
	group by c.section";
	// echo $trans_sql;die;

	$trnasactionData = sql_select($trans_sql);

	$order_sql="SELECT a.id,b.section,b.order_no,b.sub_section,c.description, sum(c.qnty) as QNTY, sum(c.amount) as AMOUNT
	from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c
	where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.is_deleted=0 and b.status_active=1 and c.status_active=1 $sql_cond2 
	group by a.id,b.section,b.order_no,b.sub_section,c.description";

	$orderData = sql_select($order_sql);
	foreach($orderData as $row)
	{
		$booked_array[$row['SECTION']][$row['SUB_SECTION']][$row['DESCRIPTION']][$row['ORDER_NO']][$row['ID']]['order_quantity']=$row['QNTY'];
		$booked_array[$row['SECTION']][$row['SUB_SECTION']][$row['DESCRIPTION']][$row['ORDER_NO']][$row['ID']]['amount']=$row['AMOUNT'];
	}
	unset($orderData);
		
	$delevery_sql= "SELECT b.received_id, b.order_no, c.section as section_id,c.sub_section, sum(b.delevery_qty) as DELEVERY_QTY, d.description as item_description
	from trims_delivery_mst a, trims_delivery_dtls b,subcon_ord_dtls c, subcon_ord_breakdown d 
	where c.id=d.mst_id and d.id=b.break_down_details_id and  a.id=b.mst_id and c.id=b.receive_dtls_id and a.entry_form=208 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.delivery_date between '$from_date' and '$to_date' $sql_cond2
	group by b.received_id, b.order_no, c.section,c.sub_section, d.description"; 
	
	$deleveryData = sql_select($delevery_sql);
	$deleveryArr=array();
	foreach($deleveryData as $row)
	{
		$orderquantity=$booked_array[$row["SECTION_ID"]][$row["SUB_SECTION"]][$row["ITEM_DESCRIPTION"]][$row["ORDER_NO"]][$row["RECEIVED_ID"]]['order_quantity'];
		$orderamount=$booked_array[$row["SECTION_ID"]][$row["SUB_SECTION"]][$row["ITEM_DESCRIPTION"]][$row["ORDER_NO"]][$row["RECEIVED_ID"]]['amount'];
		$delevety_rate=$orderamount/$orderquantity;
		$deleveryArr[$row["SECTION_ID"]]+=$row["DELEVERY_QTY"]*$delevety_rate;
	}


	ob_start();
	?>
	<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:620px;">
		<table width="600" cellpadding="0" cellspacing="0" id="caption" align="center">
			<thead>
				<tr style="border:none;">
					<td align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
				</tr>
				<tr style="border:none;">
					<td class="form_caption" align="center" style="border:none; font-size:14px;">
					   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
					</td>
				</tr>
				<tr style="border:none;">
					<td align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
						<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
					</td>
				</tr>
			</thead>
		</table>
		<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="620" rules="all" id="rpt_table_header">
			<thead>
				<tr>
					<th width="100">Item</th>
					<th width="100">Opening Price $</th>
					<th width="100">Closing Price $</th>
					<th width="100">Rcv. Price $</th>
					<th width="100">Consumtion $</th>
                    <th >Sales $</th>
				</tr>
			</thead>
		</table>
		<div style="width:620px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
		<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="600" rules="all" align="right">
		<?
			$i=1;
			foreach($trnasactionData as $row)
			{
				if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
				$openingBalance=($row["OPEN_RCV_AMOUNT"]-$row["OPEN_ISS_AMOUNT"])/$currency_rate;
				$closingStock=($openingBalance+$row["CURR_RCV_AMOUNT"]-$row["CURR_ISS_AMOUNT"])/$currency_rate;
				$currRcvValue=$row["CURR_RCV_AMOUNT"]/$currency_rate;
				$currIssValue=$row["CURR_ISS_AMOUNT"]/$currency_rate;
				
				if($value_with==1)
				{
					if( $closingStock>0 || $openingBalance>0 )
					{
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style=" word-wrap:break-word; <?=$stylecolor; ?> "  onclick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
							<td width="100"><?=$trims_section[$row["SECTION"]]; ?></td>
                            <td width="100" align="right"><?=number_format($openingBalance,2); ?></td>
                            <td width="100" align="right"><?=number_format($closingStock,2); ?></td>
                            <td width="100" align="right"><?=number_format($currRcvValue,2); ?></td>
                            <td width="100" align="right"><?=number_format($currIssValue,2); ?></td>	
							<td align="right"><?=number_format($deleveryArr[$row["SECTION"]],2)?></td>
						</tr>
						<?
						$i++;
					}
				}
				else
				{
					if( $closingStock>0 || $openingBalance>0 || $currRcvValue>0 || $currIssValue>0 || $deleveryArr[$row["SECTION"]]>0)
					{
						?>
						<tr bgcolor="<?=$bgcolor; ?>" style=" word-wrap:break-word; <?=$stylecolor; ?> "  onclick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
                            <td width="100"><?=$trims_section[$row["SECTION"]]; ?></td>
                            <td width="100" align="right"><?=number_format($openingBalance,2); ?></td>
                            <td width="100" align="right"><?=number_format($closingStock,2); ?></td>
                            <td width="100" align="right"><?=number_format($currRcvValue,2); ?></td>
                            <td width="100" align="right"><?=number_format($currIssValue,2); ?></td>	
							<td align="right"><?=number_format($deleveryArr[$row["SECTION"]],2)?></td>										
						</tr>
						<?
						$i++;
					}
				}
			}
		?>
		</table>
		</div>
	</div>
	<?

    $html = ob_get_contents();
    ob_clean();
    foreach (glob($user_id."*.xls") as $filename) {
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

if($action=="receive_details_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
   
    <table width="680" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="100">MRR Number</th>
                <th width="100">Receive Date</th>
                <th width="70">Receive Basis</th>
                <th width="70">Rcv Qty</th>
                <th width="30">BTB LC No.</th>
               </tr>
        </thead>
        <tbody>
		<?
	
		$details_sql="SELECT a.id , a.receive_date, a.recv_number, a.receive_basis, a.lc_no, b.cons_quantity from  inv_receive_master a, inv_transaction b
		where b.mst_id=a.id and a.id=$rcv_id and b.transaction_type=1 and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 order by a.id ASC";
		
		//echo $details_sql; die;
		
        $sql_result=sql_select($details_sql); 
		$t=1;
		foreach($sql_result as $row)
		{
        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
            	<td align="center"><p><? echo $t; ?>&nbsp;</p></td>
                <td  width="100"><? echo $row[csf('recv_number')]; ?></td>
                <td  width="100"><? echo change_date_format($row[csf("receive_date")]); ?></td>
                <td  width="100"><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></td>
                <td  width="70"><? echo $row[csf('cons_quantity')];?></td>
                <td  width="30" align="center"><? echo $row[csf("lc_no")];?></td>
               
            </tr>
            <?
			$t++;
		}
		?>
        </tbody>
        <tfoot>
        	<tr>
                <th colspan="3" >Total</th>
                <th >&nbsp;</th>
               	<th >&nbsp;</th>
                <th ></th>
               
            </tr>
        </tfoot>
    </table>
    <?

}


if($action=="purchase_popup")
{
	echo load_html_head_contents("Popup Info","../../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");

	//$item_group_name = return_field_value("item_name","lib_item_group","id=".$product_sql[0][csf("item_group_id")],"item_name");	?>
   
    <table width="680" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all">
    	<thead>
        	<tr>
            	<th width="30">SL</th>
                <th width="100">MRR Number</th>
                <th width="100">Receive Date</th>
                <th width="100">Supplier Name</th>
                <th width="70">Item Description</th>
                <th width="30">UOM.</th>
                <th width="70">Rcv Qty</th>
                <th width="80">Rcv Rate</th>
                <th>Rcv Value</th>
              
            </tr>
        </thead>
        <tbody>
		<?
		$prod_id_ref=explode("_",$prod_id);
		$product_id=$prod_id_ref[0];
		$date_form=$prod_id_ref[1];
		$date_to=$prod_id_ref[2];
		$currency_rate=return_field_value( "conversion_rate", "currency_conversion_rate", "currency=2 and status_active=1 and con_date=(select max(con_date) as con_date from currency_conversion_rate where currency=2)" , "conversion_rate" );

		$details_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.order_amount, a.order_rate, a.batch_lot as LOT_NO,b.item_description,b.unit_of_measure,b.avg_rate_per_unit,a.transaction_date,a.mst_id,a.supplier_id, c.recv_number
		from inv_transaction a, product_details_master b, inv_receive_master c
		where a.prod_id=b.id and a.mst_id=c.id and b.id=$product_id and a.transaction_date>='$date_form' and a.transaction_date <='$date_to' and b.item_category_id=101 and a.item_category=101 and a.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.id ASC";
		
		//echo $details_sql;
		$sql_result=sql_select($details_sql);
		$t=1;
		foreach($sql_result as $row)
		{
			if ($t%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			if(ceil($row[csf("order_amount")])==ceil($row[csf("CONS_AMOUNT")]))
			{
				$order_amt=$row[csf("order_amount")]/$currency_rate;
				$order_rate=$row[csf("order_rate")]/$currency_rate;
			}
			else
			{
				$order_amt=$row[csf("order_amount")];
				$order_rate=$row[csf("order_rate")];
			}
        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $t; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $t; ?>">
            	<td align="center"><p><? echo $t; ?>&nbsp;</p></td>
                <td  width="100"><? echo $row[csf('recv_number')]; //$recv_number_arr[$row[csf('mst_id')]]; ?></td>
                <td  width="100"><? echo change_date_format($row[csf("transaction_date")]); ?></td>
                <td  width="100"><? echo $supplierArr[$row[csf('supplier_id')]]; ?></td>
                <td  width="70"><? echo $row[csf("item_description")];?></td>
                <td  width="40" align="center"><? echo $unit_of_measurement[$row[csf("unit_of_measure")]];?></td>
                <td  width="70" align="right"><? echo number_format($row[csf("CONS_QUANTITY")],0); $total_wo_qnty+=$row[csf("CONS_QUANTITY")];  ?></td>
                <td  width="80" align="right"><? echo number_format($order_rate,4);?></td>
                <td align="right"><? echo number_format($order_amt,2); $total_bal_qnty+=$order_amt;  ?></td>
            </tr>
            <?
			$t++;
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
               	<th >&nbsp;</th>
              <th><? echo number_format($total_bal_qnty,2); ?></th>
            </tr>
        </tfoot>
    </table>
    <?

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

		$rcv_qnty_array=return_library_array("select a.booking_id, sum(b.cons_quantity) as cons_quantity from  inv_receive_master a,  inv_transaction b where a.id=b.mst_id and a.receive_basis in(1,2) and b.transaction_type=1 and b.prod_id=$prod_id and b.status_active=1 group by a.booking_id","booking_id","cons_quantity");
		$details_sql="select b.id as wo_po_id, b.wo_number as wo_po_no, b.wo_date as wo_po_date, b.pay_mode as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.supplier_order_quantity) as wo_po_qnty, 1 as type from wo_non_order_info_mst b,  wo_non_order_info_dtls c where b.id=c.mst_id and c.item_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and b.item_category in (101) and b.pay_mode<>2 and c.is_deleted=0 group by b.id, b.wo_number, b.wo_date, b.pay_mode
		union all
		select b.id as wo_po_id, b.pi_number as wo_po_no, b.pi_date as wo_po_date, 0 as wo_po_mode, max(c.uom) as wo_po_uom, sum(c.quantity) as wo_po_qnty, 2 as type from com_pi_master_details b, com_pi_item_details c where b.id=c.pi_id and b.item_category_id in (101) and c.item_prod_id=$prod_id and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, b.pi_number, b.pi_date";
		//echo $details_sql;
		$sql_result=sql_select($details_sql);
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$rcv_qnty=$rcv_qnty_array[$row[csf("wo_po_id")]];
			$balance=$row[csf("wo_po_qnty")]-$rcv_qnty;
			if($row[csf("type")]==1) $type="WO"; else $type="PI";

        	?>
        	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
                <td><p><? if($row[csf("wo_po_date")]!="" && $row[csf("wo_po_date")]!="0000-00-00") echo change_date_format($row[csf("wo_po_date")]); ?>&nbsp;</p></td>
                <td><p><? echo $row[csf("wo_po_no")]; ?>&nbsp;</p></td>
                <td align="center"><p><? echo $type; ?>&nbsp;</p></td>
                <td><p><? echo $pay_mode[$row[csf("wo_po_mode")]]; ?>&nbsp;</p></td>
                <td><p><? echo $unit_of_measurement[$row[csf("wo_po_uom")]]; ?>&nbsp;</p></td>
                <td align="right"><p><? echo number_format($row[csf("wo_po_qnty")],0); $total_wo_qnty+=$row[csf("wo_po_qnty")]; ?></p></td>
                <td align="right"><p><? echo number_format($rcv_qnty,0); $total_rcv_qnty+=$rcv_qnty; ?></p></td>
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
