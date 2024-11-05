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
				if(ceil($row_p["ORDER_AMOUNT"])==ceil($row_p["CONS_AMOUNT"]))
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["ORDER_AMOUNT"]/$currency_rate;
				}
				else
				{
					$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["ORDER_AMOUNT"];
				}
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]+=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['purchase']+=$row_p["CONS_QUANTITY"];
				if(ceil($row_p["ORDER_AMOUNT"])==ceil($row_p["CONS_AMOUNT"]))
				{
					$data_array[$row_p["PROD_ID"]]['purchase_amt']+=$row_p["ORDER_AMOUNT"]/$currency_rate;
					$data_array[$row_p["PROD_ID"]]['ORDER_AMOUNT']+=$row_p["ORDER_AMOUNT"]/$currency_rate;
				}
				else
				{
					$data_array[$row_p["PROD_ID"]]['purchase_amt']+=$row_p["ORDER_AMOUNT"];
					$data_array[$row_p["PROD_ID"]]['ORDER_AMOUNT']+=$row_p["ORDER_AMOUNT"];
				}
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]+=$row_p["CONS_AMOUNT"];
			}
		}
		else if($row_p["TRANSACTION_TYPE"]==2)
		{
			if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$mrr_rate_arr[$row_p["TRANS_ID"]]["order_amount"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['issue']+=$row_p["CONS_QUANTITY"];
				$test.=$row_p["TRANS_ID"]."===";
				$data_array[$row_p["PROD_ID"]]['issue_amt']+=$mrr_rate_arr[$row_p["TRANS_ID"]]["order_amount"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
		}
		else if($row_p["TRANSACTION_TYPE"]==3)
		{
			if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array[$row_p["PROD_ID"]]['iss_total_opening']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$mrr_rate_arr[$row_p["TRANS_ID"]]["order_amount"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['receive_return']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['receive_return_amt']+=$mrr_rate_arr[$row_p["TRANS_ID"]]["order_amount"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
		}
		else if($row_p["TRANSACTION_TYPE"]==4)
		{
			$test.=$issue_rtn_data[$row_p["TRANS_ID"]]."---";
			if(strtotime($row_p["TRANSACTION_DATE"])<strtotime($from_date))
			{
				$data_array[$row_p["PROD_ID"]]['rcv_total_opening']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['rcv_total_opening_amt']+=$row_p["CONS_QUANTITY"]*$mrr_rate_arr[$issue_rtn_data[$row_p["TRANS_ID"]]]["order_amount"]/$mrr_rate_arr[$issue_rtn_data[$row_p["TRANS_ID"]]]["issue_qnty"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]+=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['issue_return']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['issue_return_amt']+=$row_p["CONS_QUANTITY"]*$mrr_rate_arr[$issue_rtn_data[$row_p["TRANS_ID"]]]["order_amount"]/$mrr_rate_arr[$issue_rtn_data[$row_p["TRANS_ID"]]]["issue_qnty"];
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
				$data_array[$row_p["PROD_ID"]]['iss_total_opening_amt']+=$mrr_rate_arr[$row_p["TRANS_ID"]]["order_amount"];
				$cons_closing_stock_value[$row_p["PROD_ID"]]["closing_amount"]-=$row_p["CONS_AMOUNT"];
			}
			else if( (strtotime($row_p["TRANSACTION_DATE"])>=strtotime($from_date)) && (strtotime($row_p["TRANSACTION_DATE"])<=strtotime($to_date)) )
			{
				$data_array[$row_p["PROD_ID"]]['item_transfer_issue']+=$row_p["CONS_QUANTITY"];
				$data_array[$row_p["PROD_ID"]]['item_transfer_issue_amt']+=$mrr_rate_arr[$row_p["TRANS_ID"]]["order_amount"];
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
				$openingBalanceValue = $data_array[$row[csf("id")]]['rcv_total_opening_amt']-$data_array[$row[csf("id")]]['iss_total_opening_amt'];
				
				if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
				
				$totalReceive = $data_array[$row[csf("id")]]['purchase']+$data_array[$row[csf("id")]]['issue_return']+$transfer_in_qty;//+$openingBalance
				$totalIssue = $data_array[$row[csf("id")]]['issue']+$data_array[$row[csf("id")]]['receive_return']+$transfer_out_qty;

				$totalReceive_amt = $data_array[$row[csf("id")]]['purchase_amt']+$data_array[$row[csf("id")]]['issue_return_amt']+$transfer_in_qty_amt;
				$totalIssue_amt = $data_array[$row[csf("id")]]['issue_amt']+$data_array[$row[csf("id")]]['receive_return_amt']+$transfer_out_qty_amt;

				$closingStock=$openingBalance+$totalReceive-$totalIssue;
				$stockValue=$openingBalanceValue+$totalReceive_amt-$totalIssue_amt;
				if($stockValue>0 && $closingStock>0) $avg_rate=$stockValue/$closingStock; else $avg_rate=0;
				
				//$closingStock=$opening+$totalReceive-$totalIssue;
				
				//$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['rcv_total_wo'];
				$pipeLine_qty=$wo_qty_arr[$row[csf("id")]]+$pi_qty_arr[$row[csf("id")]]-$data_array[$row[csf("id")]]['purchase'];
				
				if($pipeLine_qty<0) $pipeLine_qty=0;
				//echo $closingStock."=".$openingBalance."<br>";
				$closing_stock_ord=$closingStock/$row[csf("conversion_factor")];
				$closing_amount=$closing_stock_ord*$avg_rate*$currency_rate;
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
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amt'],3); $tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amt']; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return_amt'],3); $tot_issue_return_amount+=$data_array[$row[csf("id")]]['issue_return_amt'];//$row[csf("issue_return")]; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalReceive_amt,3); $tot_total_receive_amount+=$totalReceive_amt; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>

							<td width="80" align="right"><p><? echo number_format($issue_value,3); $tot_total_issue_amount+=$issue_value; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return_amt'],3); $tot_rec_return_amount+=$data_array[$row[csf("id")]]['receive_return_amt'];//$issue_result[0][csf('receive_return')]; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalIssue_amt,3); $tot_total_issue+=$totalIssue_amt; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
                            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closing_stock_ord,3); $tot_closing_stock_ord+=$closing_stock_ord; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($avg_rate,3); //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
                            <td width="120" align="right"><p><?  //echo number_format($stockValue,3); $tot_stock_value+=$stockValue; 
							if(number_format($closingStock,3)>0.000){
							 echo number_format($stockValue,3); $tot_stock_value+=$stockValue;}else { echo "0.000"; $tot_stock_value+="0.000";} 
							?></p></td>
							
                            <td width="120" align="right"><p><?
                            //$closing_amount=$closing_stock_ord*$avg_rate*$currency_rate;
                            //$cons_closing_stock_value[$row[csf("id")]]["closing_amount"]

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
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['purchase_amt'],3); $tot_purchase_amount+=$data_array[$row[csf("id")]]['purchase_amt']; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return'],3); $tot_issue_return+=$data_array[$row[csf("id")]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['issue_return_amt'],3); $tot_issue_return_amount+=$data_array[$row[csf("id")]]['issue_return_amt'];//$row[csf("issue_return")]; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalReceive_amt,3); $tot_total_receive_amount+=$totalReceive_amt; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>

							<td width="80" align="right"><p><? echo number_format($issue_value,3); $tot_issue_value+=$issue_value; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return'],3); $tot_rec_return+=$data_array[$row[csf("id")]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($data_array[$row[csf("id")]]['receive_return_amt'],3); $tot_rec_return_amount+=$data_array[$row[csf("id")]]['receive_return_amt'];//$issue_result[0][csf('receive_return')]; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($totalIssue_amt,3); $tot_total_issue_amount+=$totalIssue_amt; ?></p></td>
							<td width="100" align="right"><p><? echo number_format($closingStock,3); $tot_closing_stock+=$closingStock; ?></p></td>
                            <td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
                            <td width="100" align="right"><p><? echo number_format($closing_stock_ord,3); $tot_closing_stock_ord+=$closing_stock_ord; ?></p></td>
							<td width="80" align="right"><p><? echo number_format($avg_rate,3); //number_format($row[csf("avg_rate")],2); $tot_avg_rate+=$row[csf("avg_rate")]; ?></p></td>
                            <td width="120" align="right"><p><?
							if(number_format($closingStock,3)>0.000){
							 echo number_format($stockValue,3); $tot_stock_value+=$stockValue;}else { echo "0.000"; $tot_stock_value+="0.000";} ?></p></td>

                            <td width="120" align="right"><p><? 
                           	//$closing_amount=$closing_stock_ord*$avg_rate*$currency_rate;
                            //$cons_closing_stock_value[$row[csf("id")]]["closing_amount"]
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
