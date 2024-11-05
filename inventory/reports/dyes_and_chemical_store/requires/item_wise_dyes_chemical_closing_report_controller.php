<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="print_button_variable_setting")
{
    $print_report_format=0;
    $print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=6 and report_id=139 and is_deleted=0 and status_active=1");
   	$printButton=explode(',',$print_report_format);

	foreach($printButton as $id){

		if($id==256)$buttonHtml.='<input type="button" name="search" id="search" value="Report2" onClick="generate_report(3)" style="width:80px" class="formbutton" /> ';
        if($id==267)$buttonHtml.='<input type="button" name="search" id="search" value="Report3" onClick="generate_report(7)" style="width:80px" class="formbutton" /> ';
        if($id==715)$buttonHtml.='<input type="button" name="search" id="search" value="All Data" onClick="generate_report(4)" style="width:80px" class="formbutton" /> ';
		if($id==716)$buttonHtml.='<input type="button" name="search" id="search" value="Stock Value" onClick="generate_report(5)" style="width:80px" class="formbutton" /> ';
		if($id==717)$buttonHtml.='<input type="button" name="search" id="search" value="Lot Wise" onClick="generate_report(6)" style="width:70px" class="formbutton" />';
		if($id==438)$buttonHtml.='<input type="button" name="search" id="search" value="QTY And VALUE" onClick="generate_report(8)" style="width:80px" class="formbutton" /> ';

		
	}

   echo "document.getElementById('button_data_panel').innerHTML = '".$buttonHtml."';\n";
    exit();
}

if ($action=="load_drop_down_store")
{
	//extract($_REQUEST);
	$data=explode('_',$data);
	$company_id=$data[0];
	$item_cat_id=$data[1];
	$store_wish=$data[2];
	// echo $item_cat_id."**".$store_wish;
	if($store_wish==1){
		if($item_cat_id>0){
		$sql= "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in($item_cat_id) group by a.id, a.store_name";
		// echo $sql;
		}else{
		$sql= "select a.id,a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  group by a.id, a.store_name";
		}
        // echo $sql;
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

        $sql="SELECT id, item_account, item_category_id, item_group_id, item_description, supplier_id, sub_group_name from product_details_master where company_id=$data[0] and item_category_id in ($data[1]) $item_name and  status_active=1 and is_deleted=0";
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
	$sql="SELECT id,item_name from  lib_item_group where status_active=1 and is_deleted=0 $item_category"; //id=$data[1] and

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
	

	if($report_type==1) //show button
	{

		if ($cbo_company_name==0) $company_id =""; else $company_id =" and a.company_id='$cbo_company_name'";
		if ($cbo_item_category_id==0) $item_category_id=" and b.item_category_id in(5,6,7,22,23)"; else $item_category_id=" and b.item_category_id in($cbo_item_category_id)";
		if ($cbo_item_category_id==0) $item_category_cond=" and a.item_category in(5,6,7,22,23)"; else $item_category_cond=" and a.item_category in ($cbo_item_category_id)";
		if ($item_group_id==0) $group_id=""; else $group_id=" and b.item_group_id in($item_group_id)";
		if ($item_account_id==0) $item_account=""; else $item_account=" and a.prod_id in ($item_account_id)";
		if ($item_account_id==0) $item_account_cond=""; else $item_account_cond=" and b.id in ($item_account_id)";
	
		//if ($cbo_store_name==0)  $store_id="";  else  $store_id=" and a.store_id=$cbo_store_name ";
		$store_id="";
		if ($cbo_store_name != "")  $store_id.=" and a.store_id in($cbo_store_name) ";


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
		else
		{
			$from_date=""; $to_date="";
		}
		$search_cond="";
		if($value_with==0) $search_cond =""; else $search_cond= "  and b.current_stock>0";


		$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
		// $supplierArr = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
		$storeArr = return_library_array("select id,store_name from lib_store_location where status_active=1 and is_deleted=0","id","store_name");
		//$itemnameArr = return_library_array("select id,item_name from lib_item_creation where status_active=1 and is_deleted=0","id","item_name");
		$itemgroupArr = return_library_array("select id,item_name from lib_item_group where status_active=1 and is_deleted=0 and item_category in(5,6,7,23)","id","item_name");
		

		$con = connect();
		$r_id6=execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (108)");
		oci_commit($con);


		$trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS, a.STORE_ID,  A.EXPIRE_DATE, A.MANUFACTURE_DATE, a.mst_id as MST_ID
		from inv_transaction a, product_details_master b
		where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond $item_account_cond order by b.id ASC";
		
		// $trans_sql="SELECT b.id as PROD_ID, a.transaction_date as TRANSACTION_DATE, a.transaction_type as TRANSACTION_TYPE, a.cons_quantity as CONS_QUANTITY, a.cons_amount as CONS_AMOUNT, a.batch_lot as LOT_NO, a.receive_basis as RECEIVE_BASIS, a.STORE_ID,  A.EXPIRE_DATE, A.MANUFACTURE_DATE,c.RECEIVE_DATE
		// from inv_transaction a, product_details_master b,inv_receive_master c
		// where a.prod_id=b.id and  c.id=a.mst_id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond $item_account_cond order by b.id,a.id ASC";
		
		//echo $trans_sql;//die;
		$trnasactionData = sql_select($trans_sql);
		//echo count($trnasactionData).jahid;die;
		$data_array=array();
		$mst_ids_arr=array();
		foreach($trnasactionData as $row_p)
		{
			$mst_ids_arr[$row_p['MST_ID']]=$row_p['MST_ID'];

			if($row_p["TRANSACTION_TYPE"]==1)
			{
				$data_array[$row_p["PROD_ID"]]['expire_date']=$row_p["EXPIRE_DATE"];
				$data_array[$row_p["PROD_ID"]]['manufacture_date']=$row_p["MANUFACTURE_DATE"];

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
		
		if (count($mst_ids_arr)>0)
		{

			fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 108, 1, $mst_ids_arr, $empty_arr);
			$rcv_sql="SELECT b.id as PROD_ID,max(c.RECEIVE_DATE) as RCV_DATE,a.ORDER_QNTY, c.RECEIVE_PURPOSE
			from inv_transaction a, product_details_master b,inv_receive_master c,GBL_TEMP_ENGINE g
			where a.prod_id=b.id and  c.id=a.mst_id and c.ID = g.REF_VAL and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond $item_account_cond AND  g.USER_ID= $user_id AND g.ENTRY_FORM=108 AND g.REF_FROM=1 group by b.id,a.ORDER_QNTY, c.RECEIVE_PURPOSE";
			//echo $rcv_sql;
			$rcv_sql_result = sql_select($rcv_sql);
			foreach($rcv_sql_result as $row)
			{
				if($row['RECEIVE_PURPOSE']==5){  // rcv pur=loan
					$rcv_loan[$row["PROD_ID"]]['loan_rcv']+=$row["ORDER_QNTY"];
				}

				$date_array[$row["PROD_ID"]]['rcv_date']=$row["RCV_DATE"];
			}

			$issue_sql="SELECT b.id as PROD_ID,a.CONS_QUANTITY, c.ISSUE_PURPOSE,a.TRANSACTION_TYPE
			from inv_transaction a, product_details_master b,INV_ISSUE_MASTER c,GBL_TEMP_ENGINE g
			where a.prod_id=b.id and  c.id=a.mst_id and c.ID = g.REF_VAL and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.is_deleted=0 $company_id $group_id $prod_cond $store_id $item_account $search_cond $item_category_id $item_category_cond $item_account_cond AND  g.USER_ID= $user_id AND g.ENTRY_FORM=108 AND g.REF_FROM=1 group by b.id,a.CONS_QUANTITY, c.ISSUE_PURPOSE,a.TRANSACTION_TYPE";
	
			//echo $issue_sql;
			$issue_sql_result = sql_select($issue_sql);
			foreach($issue_sql_result as $row)
			{
				if($row['TRANSACTION_TYPE']==2){
					if($row['ISSUE_PURPOSE']==5){  // rcv pur=loan
						$issue_arr[$row["PROD_ID"]]['loan_issue']+=$row["CONS_QUANTITY"];
					}
					elseif($row['ISSUE_PURPOSE']==15){  // rcv pur=sales
						$issue_arr[$row["PROD_ID"]]['sales_issue']+=$row["CONS_QUANTITY"];
					}
					else{
						$issue_arr[$row["PROD_ID"]]['consumption']+=$row["CONS_QUANTITY"];
					}
				}
			}

		}

	


		if ($cbo_item_category_id==0) $item_category_cond=" and item_category in(5,6,7,22,23)"; else $item_category_cond=" and item_category='$cbo_item_category_id'";
		$returnRes_date="select prod_id as PROD_ID, min(transaction_date) as MIN_DATE, max(transaction_date) as MAX_DATE from inv_transaction where is_deleted=0 and status_active=1 $item_category_cond group by prod_id";
		$result_returnRes_date = sql_select($returnRes_date);
		foreach($result_returnRes_date as $row)
		{
			$date_array[$row["PROD_ID"]]['min_date']=$row["MIN_DATE"];
			$date_array[$row["PROD_ID"]]['max_date']=$row["MAX_DATE"];
		}
		
		execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and entry_form in (108)");
		oci_commit($con);
		disconnect($con);


		$wo_qty_arr=return_library_array("select b.item_id, sum(b.supplier_order_quantity) as qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and b.item_category_id in (5,6,7,23) and a.pay_mode<>2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_id","item_id","qty");

		$pi_qty_arr=return_library_array("select b.item_prod_id, sum(b.quantity) as qty from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id in (5,6,7,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by item_prod_id","item_prod_id","qty");

		
		$tbl_width="1950";
		
		$i=1;
		ob_start();
		?>
		<style>
			.wrd_brk{word-break: break-all;}
		</style>

        <!-- <p style="color:#FF0000; font-size:16px; font-weight:bold;">Value Will Be Match Only For Item Level.</p> -->
		<div align="center" style="height:auto; width:auto; margin:0 auto; padding:0; width:1890">
			<table width="1890px" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead>
					<tr style="border:none;">
						<td colspan="11" align="center" class="form_caption" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr style="border:none;">
						<td colspan="11" class="form_caption" align="center" style="border:none; font-size:14px;">
						   <b>Company Name : <? echo $companyArr[$cbo_company_name]; ?></b>
						</td>
					</tr>
					<tr style="border:none;">
						<td colspan="11" align="center" class="form_caption" style="border:none;font-size:12px; font-weight:bold">
							<? if($from_date!="" || $to_date!="")echo "From : ".change_date_format($from_date,'dd-mm-yyyy')." To : ".change_date_format($to_date,'dd-mm-yyyy')."" ;?>
						</td>
					</tr>
				</thead>
			</table>
			<table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<?=$tbl_width;?>" rules="all" id="rpt_table_header">
				<thead>
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="100">Item Group</th>
						<th rowspan="2" width="180">Item Description</th>
						<th rowspan="2" width="80">In Housed Date</th>
						<th rowspan="2" width="80">Manufature Date</th>
						<th rowspan="2" width="80">Expired Date</th>
						<th rowspan="2" width="50">UOM</th>
                        <th rowspan="2" width="100">Opening Stock</th>

						<th colspan="5">Receive</th>
						<th colspan="6">Issued Status</th>

						<th rowspan="2" width="100">Closing Stock</th>


						<th rowspan="2" width="80">Requisition Qty</th>
						<th rowspan="2" >Remarks</th>
						
					</tr>
					<tr>
						
						<th width="80">Purchase</th>
						<th width="80">Loan receive</th>
						<th width="80">Inter Factory T. In</th>
						<th width="80">Issue Return</th>
						<th width="100">Total Received</th>

						<th width="80">Sales</th>
						<th width="80">Consumption</th>
						<th width="80">Loan Issue</th>
						<th width="80">Inter Factory T. Out</th>
						<th width="80">Receive Return</th>
						<th width="100">Total Issue</th>
					</tr>
				</thead>
			</table>
			<div style="width:<?=$tbl_width+18;?>px; max-height:250px; overflow-y:scroll; overflow-x:hidden;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<?=$tbl_width;?>" rules="all" align="left">
			<?
				if ($cbo_item_category_id==0) $rcv_category_cond=" and b.item_category in(5,6,7,22,23)"; else $rcv_category_cond=" and b.item_category='$cbo_item_category_id'";
				
				
				$sql="SELECT B.ID, B.ITEM_CODE, B.ITEM_CATEGORY_ID,B.ITEM_GROUP_ID,B.UNIT_OF_MEASURE,B.ITEM_DESCRIPTION,B.SUB_GROUP_NAME, B.CURRENT_STOCK,AVG_RATE_PER_UNIT, B.RE_ORDER_LABEL, B.IS_COMPLIANCE FROM PRODUCT_DETAILS_MASTER B WHERE B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND B.COMPANY_ID='$cbo_company_name' $item_category_id $group_id $prod_cond $item_account_cond $search_cond ORDER BY B.ID";
				//echo $sql;//die;
				$result = sql_select($sql);

				foreach($result as $row)
				{
					//$seq_grouping = $row['ITEM_GROUP_ID']."*".$row['ID'];

					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['ID']  = $row['ID'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['ITEM_CODE']  = $row['ITEM_CODE'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['ITEM_CATEGORY_ID']  = $row['ITEM_CATEGORY_ID'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['ITEM_GROUP_ID']  = $row['ITEM_GROUP_ID'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['UNIT_OF_MEASURE']  = $row['UNIT_OF_MEASURE'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['ITEM_DESCRIPTION']  = $row['ITEM_DESCRIPTION'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['SUB_GROUP_NAME']  = $row['SUB_GROUP_NAME'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['CURRENT_STOCK']  = $row['CURRENT_STOCK'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['AVG_RATE_PER_UNIT']  = $row['AVG_RATE_PER_UNIT'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['RE_ORDER_LABEL']  = $row['RE_ORDER_LABEL'];
					$all_data[$row['ITEM_CATEGORY_ID']][$row['ITEM_GROUP_ID']][$row['ID']]['IS_COMPLIANCE']  = $row['IS_COMPLIANCE'];
				}

				foreach ($all_data as $cat_id => $category_val) 
				{
					foreach($category_val as $item_grp_key => $item_grp_no)
					{
						$row_no=0;
						foreach($item_grp_no as $dtls_id => $row)
						{
							$issue_qty=$data_array[$row["ID"]]['issue'];
							$transfer_out_qty=$data_array[$row["ID"]]['item_transfer_issue'];
							$transfer_in_qty=$data_array[$row["ID"]]['item_transfer_receive'];

							$transfer_out_qty_amt=$data_array[$row["ID"]]['item_transfer_issue_amt'];
							$transfer_in_qty_amt=$data_array[$row["ID"]]['item_transfer_receive_amt'];

							$openingBalance = $data_array[$row["ID"]]['rcv_total_opening']-$data_array[$row["ID"]]['iss_total_opening'];							
							
							$totalReceive = $data_array[$row["ID"]]['purchase']+$data_array[$row["ID"]]['issue_return']+$transfer_in_qty;//+$openingBalance
							$totalIssue = $data_array[$row["ID"]]['issue']+$data_array[$row["ID"]]['receive_return']+$transfer_out_qty;

							$closingStock=$openingBalance+$totalReceive-$totalIssue;

							if((($get_upto==1 && $ageOfDays>$txt_days) || ($get_upto==2 && $ageOfDays<$txt_days) || ($get_upto==3 && $ageOfDays>=$txt_days) || ($get_upto==4 && $ageOfDays<=$txt_days) || ($get_upto==5 && $ageOfDays==$txt_days) || $get_upto==0) && (($get_upto_qnty==1 && $closingStock>$txt_qnty) || ($get_upto_qnty==2 && $closingStock<$txt_qnty) || ($get_upto_qnty==3 && $closingStock>=$txt_qnty) || ($get_upto_qnty==4 && $closingStock<=$txt_qnty) || ($get_upto_qnty==5 && $closingStock==$txt_qnty) || $get_upto_qnty==0))
							{
								if($value_with==1)
								{
									if(number_format($closingStock,2)>0.00 || number_format($openingBalance,2)>0.00 )
									{
										$itm_grp_td_span[$cat_id][$item_grp_key]++;
									}
								}
								else
								{
									if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
									{
										$itm_grp_td_span[$cat_id][$item_grp_key]++;
									}
								}
							}
						}
						//$itm_grp_td_span[$item_grp_key] = $row_no;
					}
				}
				//echo "<pre>";print_r($itm_grp_td_span);die;

				$i=1;
				$previos_item_category='';
				foreach ($all_data as $cat_id => $category_val) 
				{
					$item_cat=$row['ITEM_CATEGORY_ID'];
					if($previos_item_category!=$item_cat)
					{
						?>
						<tr bgcolor="#acacac">   
							<td colspan="25" align="left" ><b>Category : <? echo $item_category[$cat_id]; ?></b></td>
						</tr>
						<?
					}
					$tot_open_bl=0;
					$tot_purchase=0;
					$tot_loan_rcv=0;
					$tot_transfer_in_qty=0;
					$tot_issue_return=0;
					$tot_total_receive=0;
					$tot_total_sales=0;
					$tot_issue=0;
					$tot_total_loan=0;
					$tot_transfer_out_qty=0;
					$tot_rec_return=0;
					$tot_total_issue=0;
					$tot_closing_stock=0;
					foreach($category_val as $item_grp_key => $item_grp_no)
					{
						$z=1;
						$rowspan= $itm_grp_td_span[$cat_id][$item_grp_key];
						foreach($item_grp_no as $des_dtls => $row)
						{
							$re_order_label=$row["RE_ORDER_LABEL"];
							if( $row[csf("transaction_type")]==1 || $row[csf("transaction_type")]==2 || $row[csf("transaction_type")]==3 || $row[csf("transaction_type")]==4 )
								$stylecolor='color:#A61000;';
							else
								$stylecolor='color:#000000;';
							$ageOfDays = datediff("d",$date_array[$row["ID"]]['min_date'],date("Y-m-d"));
							$daysOnHand = datediff("d",$date_array[$row["ID"]]['max_date'],date("Y-m-d"));


							$issue_qty=$data_array[$row["ID"]]['issue'];
							//$issue_qty=$issue_arr[$row["ID"]]['consumption'];
							$issue_loan_qty = $issue_arr[$row["ID"]]['loan_issue'];
							$issue_sales_qty = $issue_arr[$row["ID"]]['sales_issue'];


							$transfer_out_qty=$data_array[$row["ID"]]['item_transfer_issue'];
							$transfer_in_qty=$data_array[$row["ID"]]['item_transfer_receive'];

							$transfer_out_qty_amt=$data_array[$row["ID"]]['item_transfer_issue_amt'];
							$transfer_in_qty_amt=$data_array[$row["ID"]]['item_transfer_receive_amt'];

							$openingBalance = $data_array[$row["ID"]]['rcv_total_opening']-$data_array[$row["ID"]]['iss_total_opening'];
							$openingBalanceValue = $data_array[$row["ID"]]['rcv_total_opening_amt']-$data_array[$row["ID"]]['iss_total_opening_amt'];
							if($openingBalance==0)
							{
								$openingBalanceValue=0;
								$openingRate=0;
							}
							else
							{
								if($openingBalanceValue>0 && $openingBalance>0) $openingRate=$openingBalanceValue/$openingBalance; else $openingRate=0;
							}
							
							$totalReceive = $data_array[$row["ID"]]['purchase']+$data_array[$row["ID"]]['issue_return']+$transfer_in_qty;//+$openingBalance
							$totalIssue = $data_array[$row["ID"]]['issue']+$data_array[$row["ID"]]['receive_return']+$transfer_out_qty;
							//$totalIssue = $data_array[$row["ID"]]['receive_return']+$transfer_out_qty+$issue_qty+$issue_loan_qty+$issue_sales_qty;

							$totalReceive_amt = $data_array[$row["ID"]]['purchase_amt']+$data_array[$row["ID"]]['issue_return_amt']+$transfer_in_qty_amt;
							$totalIssue_amt = $data_array[$row["ID"]]['issue_amt']+$data_array[$row["ID"]]['receive_return_amt']+$transfer_out_qty_amt;

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
								$pipeLine_qty=$wo_qty_arr[$row["ID"]]+$pi_qty_arr[$row["ID"]]-$data_array[$row["ID"]]['rcv_total_wo'];

								if($pipeLine_qty<0) $pipeLine_qty=0;
								if($value_with==1)
								{
									if(number_format($closingStock,2)>0.00 && number_format($openingBalance,2)>0.00 )
									{
										if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30" class="wrd_brk" align="center"><? echo $i; ?></td>
											<?if($z == 1){?>
											<td rowspan="<? echo $rowspan;?>" valign="middle" align="center" width="100" class="wrd_brk"><p><? echo $itemgroupArr[$row["ITEM_GROUP_ID"]]; ?></p></td>
											<?}?>

											<td width="180" class="wrd_brk"><p><? echo $row["ITEM_DESCRIPTION"]; ?></p></td>
											<td width="80" align="center" class="wrd_brk"><p><? echo change_date_format($date_array[$row["ID"]]['rcv_date']); ?></p></td>
											<td width="80" align="center" class="wrd_brk"><p><? echo change_date_format($data_array[$row["ID"]]['expire_date']); ?></p></td>
											<td width="80" align="center" class="wrd_brk"><p><? echo change_date_format($data_array[$row["ID"]]['manufacture_date']); ?></p></td>
											<td width="50" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row["ID"]]['purchase'],3); $tot_purchase+=$data_array[$row["ID"]]['purchase']; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($rcv_loan[$row["ID"]]['loan_rcv'],2); $tot_loan_rcv += $rcv_loan[$row["ID"]]['loan_rcv'];?></p></td>
											
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row["ID"]]['issue_return'],3); $tot_issue_return+=$data_array[$row["ID"]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_sales_qty,2);  $tot_total_sales+=$issue_sales_qty;?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_loan_qty,2);  $tot_total_loan+=$issue_loan_qty;?></p></td>


											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row["ID"]]['receive_return'],3); $tot_rec_return+=$data_array[$row["ID"]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p>NA</p></td>
											<td class="wrd_brk" align="right">NA</p></td>

										</tr>
										<?
										$i++;
										$z++;
										$totalStockValue+=$stockValue;
										$totalpipeLine_qty+=$pipeLine_qty;
									}
								}
								else
								{
									if(number_format($closingStock,3)>0.000 || number_format($openingBalance,3)>0.000 || number_format($totalReceive,3)>0.000 || number_format($totalIssue,3)>0.000)
									{
										
										//echo "<pre>";print_r($all_data);
										if($closingStock <= $re_order_label){$bgcolor="red";}elseif($i%2==0){$bgcolor="#E9F3FF";}else{$bgcolor="#FFFFFF";}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" style=" word-wrap:break-word; <? echo $stylecolor; ?> "  onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="30" align="center"><? echo $i; ?></td>

											<?if($z == 1){?>
											<td rowspan="<? echo $rowspan;?>" valign="middle" align="center" width="100" class="wrd_brk"><p><? echo $itemgroupArr[$row["ITEM_GROUP_ID"]]; ?></p></td>
											<?}?>

											<td width="180" class="wrd_brk"><p><? echo $row["ITEM_DESCRIPTION"]; ?></p></td>
											<td width="80" align="center" class="wrd_brk"><p><? echo change_date_format($date_array[$row["ID"]]['rcv_date']); ?></p></td>
											<td width="80" align="center" class="wrd_brk"><p><? echo change_date_format($data_array[$row["ID"]]['expire_date']); ?></p></td>
											<td width="80" align="center" class="wrd_brk"><p><? echo change_date_format($data_array[$row["ID"]]['manufacture_date']); ?></p></td>
											<td width="50" class="wrd_brk" align="center"><p><? echo $unit_of_measurement[$row["UNIT_OF_MEASURE"]]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($openingBalance,4); $tot_open_bl+=$openingBalance; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row["ID"]]['purchase'],3); $tot_purchase+=$data_array[$row["ID"]]['purchase']; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($rcv_loan[$row["ID"]]['loan_rcv'],2); $tot_loan_rcv += $rcv_loan[$row["ID"]]['loan_rcv'];?></p></td>


											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_in_qty,3); $tot_transfer_in_qty+=$transfer_in_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row["ID"]]['issue_return'],3); $tot_issue_return+=$data_array[$row["ID"]]['issue_return'];//$row[csf("issue_return")]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalReceive,3); $tot_total_receive+=$totalReceive; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_sales_qty,2) ; $tot_total_sales+=$issue_sales_qty;?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_qty,3); $tot_issue+=$issue_qty; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($issue_loan_qty,2); $tot_total_loan+=$issue_loan_qty; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($transfer_out_qty,3); $tot_transfer_out_qty+=$transfer_out_qty; ?></p></td>
											<td width="80" class="wrd_brk" align="right"><p><? echo number_format($data_array[$row["ID"]]['receive_return'],3); $tot_rec_return+=$data_array[$row["ID"]]['receive_return'];//$issue_result[0][csf('receive_return')]; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($totalIssue,3); $tot_total_issue+=$totalIssue; ?></p></td>
											<td width="100" class="wrd_brk" align="right"><p><? echo number_format($closingStock,4); $tot_closing_stock+=$closingStock; ?></p></td>

											<td width="80" class="wrd_brk" align="right"><p>NA</p></td>
											<td class="wrd_brk" align="right">NA</p></td>
											
										</tr>
										<?
										$i++;
										$z++;
										$totalStockValue+=$stockValue;
										$totalpipeLine_qty+=$pipeLine_qty;
									}
								}
							}
							
							
						}
						?>
								<tr bgcolor="#dddddd">
									<td colspan="7" align="right" ><b>Total:</b></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_open_bl,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_purchase,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_loan_rcv,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_transfer_in_qty,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_issue_return,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_total_receive,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_total_sales,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_issue,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_total_loan,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_transfer_out_qty,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_rec_return,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_total_issue,2)?></b></p></td>
									<td align="right" class="wrd_brk"><p><b><? echo number_format($tot_closing_stock,2)?></b></p></td>
									<td></td>
									<td></td>
									
								</tr>
							<?
					}
				}	
			?>
			</table>
			</div>
			<table width="<?=$tbl_width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer">
				<tfoot>
					<tr>
						<!-- <th width="30">&nbsp;</th>
						<th width="100">&nbsp;</th>
						<th width="180">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="80">&nbsp;</th>
						<th width="50">&nbsp;</th>
						<th width="100">Total:</th> -->
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
