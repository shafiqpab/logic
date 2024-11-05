<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

	$data=$_REQUEST['data'];
	$action=$_REQUEST['action'];

$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = $_SESSION['logic_erp']['supplier_id'];
$user_comp_location_ids = $_SESSION['logic_erp']['company_location_id'];

	/*if ($action=="load_drop_down_store")
	{
		if($user_store_ids) $user_store_cond = " and a.id in ($user_store_ids)"; else $user_store_cond = "";
		echo create_drop_down( "cbo_store_name", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $user_store_cond and b.category_type in (1) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
		exit();
	}

	if ($action=="load_drop_down_store_to")
	{
		if($user_store_ids) $user_store_cond = " and a.id in ($user_store_ids)"; else $user_store_cond = "";
		echo create_drop_down( "cbo_store_name_to", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $user_store_cond and b.category_type in (1) group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
		exit();
	}*/
	if ($action=="load_room_rack_self_bin")
	{
		load_room_rack_self_bin("requires/yarn_transfer_controller",$data);
	}

	if ($action=="itemDescription_popup")
	{
		echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
		extract($_REQUEST);
		?> 

		<script>

		/*$(document).ready(function(e) {
            setFilterGrid('tbl_list_search',-1);
        });*/

        function js_set_value(data)
        {
        	$('#product_id').val(data);
        	parent.emailwindow.hide();
        }

    </script>

</head>

<body>
	<div align="center" style="width:920px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:910px;margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="600" class="rpt_table">
					<thead>
						<th>Search By</th>
						<th width="280" id="search_by_td_up">Please Enter Item Details</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							$search_by_arr=array(1=>"Item Details",2=>"Lot No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_store_name; ?>, 'create_product_search_list_view', 'search_div', 'yarn_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div style="margin-top:10px" id="search_div"></div> 
			</fieldset>
		</form>
	</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_product_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$store_id =$data[3];
	
	if($search_by==1)
		$search_field="a.product_name_details";	
	else
		$search_field="a.lot";	
	
	$sql="select a.id, a.company_id, a.supplier_id, a.product_name_details, a.lot, a.current_stock, a.brand, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as balance_quantity 
	from product_details_master a, inv_transaction b 
	where a.id=b.prod_id and a.item_category_id=1 and a.company_id=$company_id and b.store_id=$store_id and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	group by a.id, a.company_id, a.supplier_id, a.product_name_details, a.lot, a.current_stock, a.brand
	having sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end))>0";
	//echo $sql;
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$arr=array(1=>$company_arr,2=>$supplier_arr,5=>$brand_arr);

	echo create_list_view("tbl_list_search", "Item ID,Company,Supplier,Item Details,Lot No,Brand,Stock", "80,120,130,180,90,100","900","250",0, $sql, "js_set_value", "id", "", 1, "0,company_id,supplier_id,0,0,brand,0", $arr, "id,company_id,supplier_id,product_name_details,lot,brand,balance_quantity", '','','0,0,0,0,0,0,2');
	
	exit();
}

if($action=='populate_data_from_product_master')
{
	$data_ref=explode("__",$data);
	$prod_id=$data_ref[0];
	$store_id=$data_ref[1];
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$data_array=sql_select("select a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as balance_quantity  from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$prod_id and b.store_id=$store_id and a.status_active=1 and b.status_active=1 group by a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand");
	
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('hidden_product_id').value 			= '".$prod_id."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_yarn_lot').value 				= '".$row[csf("lot")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".number_format($row[csf("balance_quantity")],2,".","")."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".number_format($row[csf("balance_quantity")],2,".","")."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		echo "document.getElementById('hide_brand_id').value 				= '".$row[csf("brand")]."';\n";
		echo "document.getElementById('txt_yarn_brand').value 				= '".$brand_arr[$row[csf("brand")]]."';\n";

		exit();
	}
}

if ($action=="itemTransfer_popup")
{
	echo load_html_head_contents("Item Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

	<script>
		
		function js_set_value(data)
		{
			$('#transfer_id').val(data);
			parent.emailwindow.hide();
		}

	</script>

</head>

<body>
	<div align="center" style="width:770px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:760px;">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
					<thead>
						<th width="200">Search By</th>
						<th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
						<th width="220">Transfer Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<?
							$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td" align="center">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px;" placeholder="From Date" readonly/>&nbsp; TO &nbsp;
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" placeholder="To Date" style="width:80px;" readonly/>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'yarn_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td colspan="4" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
					</tr>
				</table>
				<div style="margin-top:10px" id="search_div"></div> 
			</fieldset>
		</form>
	</div>    
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$date_form=$data[3];
	$date_to =$data[4];
	$year_selection =$data[5];
	$transfer_criteria =$data[6];
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
	//echo $date_form."==".$date_to;die;
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";

	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	$date_cond="";
	if($date_form!="" && $date_to)
	{
		$date_cond=" and transfer_date between '$date_form' and '$date_to' ";	
	}
	if($year!="")
	{
		 $year_cond="and to_char(transfer_date,'YYYY')=$year_selection"; //data show only year wise, just apply query condition	
	}
	
	$sql="select id, $year_field transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=1 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria in($transfer_criteria) and status_active=1 and is_deleted=0 $date_cond order by id";
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "70,60,100,120,90,140","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category, to_company,remarks from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		
		echo "active_inactive(".$row[csf("transfer_criteria")].");\n";
		
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=1","id","product_name_details");
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$sql="select id, from_store, to_store, from_prod_id, transfer_qnty, yarn_lot, brand_id from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_arr,5=>$brand_arr);

	echo create_list_view("list_view", "From Store,To Store,Item Description,Yarn Lot,Transfered Qnty,Yarn Brand", "130,130,220,100,110","880","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,floor_id,room,rack,shelf,to_store,to_floor_id,to_room,to_rack,to_shelf,from_prod_id,0,0,brand_id", $arr, "from_store,to_store,from_prod_id,yarn_lot,transfer_qnty,brand_id", "requires/yarn_transfer_controller",'','0,0,0,0,2,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$data_array=sql_select("select a.transfer_criteria, a.company_id,a.to_company,b.id, b.mst_id, b.from_store, b.to_store, b.from_prod_id,b.floor_id,b.room,b.rack,b.shelf,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.yarn_lot, b.brand_id,b.no_of_bag,b.no_of_cone,b.weight_per_bag from inv_item_transfer_mst a,inv_item_transfer_dtls b where b.id='$data' and a.id=b.mst_id");
	foreach ($data_array as $row)
	{
		if ($row[csf("transfer_criteria")]==1) {
			$company_id=$row[csf("to_company")];
		}
		else
		{
			$company_id=$row[csf("company_id")];
		}
		//echo $row[csf("from_store")].'===';
		echo "reset_form('','','cbo_store_name*cbo_store_name_to*txt_yarn_lot*txt_transfer_qnty*txt_rate*txt_transfer_value*hide_brand_id*txt_yarn_brand*hidden_transfer_qnty*hidden_product_id*previous_from_prod_id*previous_to_prod_id*txt_item_desc*txt_current_stock*txt_btb_selection*txt_btb_lc_id*hidden_current_stock*origin_product_id*update_trans_issue_id*update_trans_recv_id','');\n";
		
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";

		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'store','from_store_td', $('#cbo_company_id').val(),'"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'floor','floor_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'room','room_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'rack','rack_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1', 'shelf','shelf_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";


		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*cbo_store_name_to', 'store','to_store_td', '".$company_id."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name_to').value 			= '".$row[csf("to_store")]."';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*cbo_floor_to', 'floor','floor_td_to', '".$company_id."','"."','".$row[csf('to_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*cbo_room_to', 'room','room_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*txt_rack_to', 'rack','rack_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		echo "load_room_rack_self_bin('requires/yarn_transfer_controller*1*txt_shelf_to', 'shelf','shelf_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";


		
		echo "document.getElementById('txt_yarn_lot').value 				= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";
		echo "document.getElementById('hide_brand_id').value 				= '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_yarn_brand').value 				= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_product_id').value 			= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '".$row[csf("to_prod_id")]."';\n";
		echo "document.getElementById('txt_no_of_bag').value 				= '".$row[csf("no_of_bag")]."';\n";
		echo "document.getElementById('txt_no_of_cone').value 				= '".$row[csf("no_of_cone")]."';\n";
		echo "document.getElementById('txt_weight_per_bag').value 			= '".$row[csf("weight_per_bag")]."';\n";
		
		$from_prod_id=$row[csf('from_prod_id')];
		$from_store_id=$row[csf('from_store')];
		
		//$sql=sql_select("select a.product_name_details, a.current_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as balance_qnty from product_details_master a, inv_transaction b where a.id=b.prod_id and a.id=$from_prod_id and b.store_id=$from_store_id group by a.product_name_details, a.current_stock, a.avg_rate_per_unit");
		
		$sql=sql_select("select a.product_name_details, a.current_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as balance_qnty,b.btb_lc_id 
			from product_details_master a, inv_transaction b 
			where a.id=b.prod_id and a.id=$from_prod_id and b.store_id=$from_store_id 
			group by a.product_name_details, a.current_stock, a.avg_rate_per_unit, b.btb_lc_id");

		$stock=$sql[0][csf("balance_qnty")]+$row[csf("transfer_qnty")];
		
		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$sql[0][csf("balance_qnty")]."';\n";
		if($sql[0][csf("btb_lc_id")]>0)
		{
			$btb_lc_num=return_field_value("lc_number","com_btb_lc_master_details","id=".$sql[0][csf("btb_lc_id")],"lc_number");
			echo "document.getElementById('txt_btb_selection').value = '".$btb_lc_num."';\n";
			echo "document.getElementById('txt_btb_lc_id').value = '".$sql[0][csf("btb_lc_id")]."';\n";
		}
		
		echo "document.getElementById('hidden_current_stock').value 		= '".$stock."';\n";
		
		$prod_id_all=$row[csf("from_prod_id")].",". $row[csf("to_prod_id")];
		
		$sql_trans=sql_select("select id, prod_id, transaction_type, origin_prod_id from inv_transaction where mst_id=".$row[csf('mst_id')]." and item_category=1 and transaction_type in(5,6) and prod_id in($prod_id_all) order by id asc");
        //echo "select id, transaction_type from inv_transaction where mst_id=$row[mst_id] and item_category=1 and transaction_type in(5,6) order by id asc";die;
		$trans_issue_id=''; $trans_recv_id='';
		foreach($sql_trans as $row_trans)
		{
			if($row_trans[csf('transaction_type')]==6 && $row_trans[csf('prod_id')]==$row[csf("from_prod_id")])
			{
				$trans_issue_id=$row_trans[csf('id')];
				$orgin_prod_id=$row_trans[csf('origin_prod_id')];
			}
			
			if($row_trans[csf('transaction_type')]==5 && $row_trans[csf('prod_id')]==$row[csf("to_prod_id")])
			{
				$trans_recv_id=$row_trans[csf('id')];
			}
		}
		
		
		
		echo "document.getElementById('origin_product_id').value 			= '".$orgin_prod_id."';\n";
		echo "document.getElementById('update_trans_issue_id').value 		= '".$trans_issue_id."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$trans_recv_id."';\n";
		echo "$('#cbo_store_name').attr('disabled',true);\n";
		echo "$('#cbo_store_name_to').attr('disabled',true);\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
//        $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5)", "max_date");      
//	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
//	$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
//	if ($transfer_date < $max_recv_date) 
//        {
//            echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
//            die;
//	}

	$txt_transfer_value = str_replace("'","",$txt_transfer_value);
	$txt_transfer_qnty	= str_replace("'","",$txt_transfer_qnty);

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$transfer_recv_num=''; $transfer_update_id=''; $order_rate=0; $order_amount=0;
		
		$trans_stock=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as cons_quantity from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and status_active=1 and is_deleted=0");
		$current_trans_stock=$trans_stock[0][csf("cons_quantity")];
		if(str_replace("'","",$txt_transfer_qnty)>$current_trans_stock)
		{
			echo "30**Transfer Quantity Not Allow More Then Stock Quantity.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$product_info = sql_select("select current_stock,available_qnty,avg_rate_per_unit from product_details_master where id=$hidden_product_id");
			if(str_replace("'","",$txt_transfer_qnty) > $product_info[0][csf("available_qnty")]*1){
				echo "21**Transfer quantity can not be greater than available quantity. Available quantity= ".$product_info[0][csf('available_qnty')];
				die;
			}
		}

		if(str_replace("'","",$update_id)!="")
		{
			$duplicate = is_duplicate_field("b.id"," inv_item_transfer_mst a, inv_transaction b","a.id=b.mst_id and a.id=$update_id and b.prod_id=$hidden_product_id and b.transaction_type in(5,6)"); 
			if( $duplicate==1) 
			{
				
				echo "20**Duplicate Product is Not Allow in Same Transfer Number.";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
		}
		
		
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later			

			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'YTE',10,date("Y",time()),1 ));

			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, from_order_id, to_order_id, item_category, remarks, inserted_by, insert_date,entry_form";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",0,0,".$cbo_item_category.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',10)";
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			//$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			//if($rID) $flag=1; else $flag=0;
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*to_company*remarks*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_company_id_to."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			//if($rID) $flag=1; else $flag=0; 
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		
		$field_array_trans="id, mst_id, company_id, supplier_id, prod_id, origin_prod_id, item_category, transaction_type, transaction_date, store_id,floor_id,room,rack,self, brand_id, order_uom, order_qnty, order_rate, order_amount, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date, btb_lc_id";		

		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, yarn_lot, brand_id, item_group, from_store,floor_id,room,rack,shelf, to_store,to_floor_id,to_room,to_rack,to_shelf, item_category, transfer_qnty, rate, transfer_value, rate_in_usd, transfer_value_in_usd, uom,no_of_bag,no_of_cone,weight_per_bag, inserted_by, insert_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, available_qnty from product_details_master where id=$hidden_product_id");
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			$presentAvaillableQty=$data_prod[0][csf('available_qnty')]-str_replace("'","",$txt_transfer_qnty);
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$presentAvaillableQty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//echo "10**".$data_array_prodUpdate;die;
			/*$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} */
			
			$supplier_id=$data_prod[0][csf('supplier_id')];
			
			$row_prod=sql_select("select id, current_stock, stock_value, avg_rate_per_unit, available_qnty, dyed_type from product_details_master where company_id=$cbo_company_id_to and item_category_id=1 and supplier_id='$supplier_id' and product_name_details=$txt_item_desc and lot=$txt_yarn_lot and status_active=1 and is_deleted=0");
			
			//echo "10**".count($row_prod);die;

			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$current_stock_qnty=($row_prod[0][csf('current_stock')] + str_replace("'", "", $txt_transfer_qnty));
				$current_stock_value=($row_prod[0][csf('stock_value')]+ str_replace("'", "", $txt_transfer_value));
				$current_avg_rate=number_format(($current_stock_value/$current_stock_qnty),6,'.','');
				$curr_availlable_qty=$row_prod[0][csf('available_qnty')]+str_replace("'", '',$txt_transfer_qnty);
				
				$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
				$data_array_prod_update=$current_avg_rate."*".$txt_transfer_qnty."*".$current_stock_qnty."*".$current_stock_value."*".$curr_availlable_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				
				//echo "10**".$data_array_prod_update;die;
				
				
				/*$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} */
			}
			else
			{
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				$curr_availlable_qty=str_replace("'","",$txt_transfer_qnty);
				
				$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, available_qnty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date, dyed_type) 
				select	
				'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, $curr_availlable_qty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand,'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."', dyed_type from product_details_master where id=$hidden_product_id";
				/*$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} */
			}
			
             //----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}

             //----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}


            //<<<<<<<<<<<<<<<----------------------------------


			$recv_qty=0; $recv_amnt=0;
			$sql_receive="select a.receive_date, a.currency_id, sum(b.order_qnty) as qty, sum(b.order_qnty*b.order_rate) as amnt from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=1 and a.item_category=1 and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$hidden_product_id group by a.receive_date, a.currency_id";
			$resultReceive = sql_select($sql_receive);
			foreach($resultReceive as $row)
			{
				$recv_qty+=$row[csf('qty')];
				if($row[csf('currency_id')]==1)
				{
					$exchange_rate=set_conversion_rate( 2, $row[csf('receive_date')]);
					if($exchange_rate<=0) {$exchange_rate=76;}
					$recv_amnt+=$row[csf('amnt')]/$exchange_rate;
				}
				else
				{
					$recv_amnt+=$row[csf('amnt')];
				}
			}
			
			$sql_trans="select sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=5 and item_category=1 and status_active=1 and is_deleted=0 and prod_id=$hidden_product_id";
			$resultTrans = sql_select($sql_trans);
			$trnas_recv_qty=$resultTrans[0][csf('qty')];
			$trnas_recv_amnt=$resultTrans[0][csf('amnt')];
			
			$tot_recv_qty=$recv_qty+$trnas_recv_qty;
			$tot_recv_amnt=$recv_amnt+$trnas_recv_amnt;
			
			$order_rate=$tot_recv_amnt/$tot_recv_qty;
			$order_amount=$order_rate*str_replace("'","",$txt_transfer_qnty);
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",'".$supplier_id."',".$hidden_product_id.",".$origin_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$hide_brand_id.",0,0,0,0,".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_btb_lc_id.")";
			

			$recv_trans_id=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id_to.",'".$supplier_id."',".$product_id.",".$origin_product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$hide_brand_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$order_rate."','".$order_amount."',".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_btb_lc_id.")";
			
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$product_id.",".$txt_yarn_lot.",".$hide_brand_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",'".$order_rate."','".$order_amount."',".$cbo_uom.",".$txt_no_of_bag.",".$txt_no_of_cone.",".$txt_weight_per_bag.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=1 and status_active=1 and is_deleted=0");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			//$field_array_mrr2= "id,recv_trans_id,entry_form,prod_id,item_return_qty,amount,transfer_criteria,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");
			foreach($sql as $result)
			{				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				if($cons_rate=="") { $cons_rate=str_replace("'","",$txt_rate); }

				
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",10,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",10,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
			}//end foreach
 			// LIFO/FIFO END-----------------------------------------------//
			
			/*$data_array_mrr2='';
			//#### consult with cto sir this operation will be hold
			
			$prev_trans_prod_id=return_field_value("distinct(from_prod_id) as prod_id","inv_item_transfer_dtls","to_prod_id=$hidden_product_id and status_active=1 and is_deleted=0","prod_id");
			if($prev_trans_prod_id!="")
			{	
				$transfer_qnty2 = str_replace("'","",$txt_transfer_qnty);
				$dataTrans=sql_select("select a.recv_trans_id, a.issue_qnty, a.amount, b.balance_qnty, b.cons_rate, b.balance_amount from inv_mrr_wise_issue_details a, inv_transaction b where a.recv_trans_id=b.id and a.prod_id=$prev_trans_prod_id and a.entry_form=10 and a.transfer_criteria=0 and a.status_active=1 and a.is_deleted=0 order by a.recv_trans_id");
				foreach($dataTrans as $rowT)
				{
					$mrrWiseIsID++; 
					$cons_rate = $rowT[csf("cons_rate")];
					$balance_qnty = $rowT[csf("issue_qnty")];
					$balance_amount = $rowT[csf("amount")];
					
					$recv_trans_id = $rowT[csf("recv_trans_id")];
					
					$tran_qty_bl=$balance_qnty-$transfer_qnty2;
					$tran_amnt_bl = $balance_amount-($transfer_qnty2*$cons_rate);
					
					if($tran_qty_bl>=0)
					{					
						$amount = $transfer_qnty2*$cons_rate;
						//for insert
						if($data_array_mrr2!="") $data_array_mrr2 .= ",";  
						$data_array_mrr2 .= "(".$mrrWiseIsID.",".$recv_trans_id.",10,".$prev_trans_prod_id.",".$transfer_qnty2.",".$amount.",".$cbo_transfer_criteria.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$latest_balance_qty=$rowT[csf("balance_qnty")]+$transfer_qnty2;
						$latest_balance_amt=$rowT[csf("balance_amount")]+$amount;
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",($latest_balance_qty."*".$latest_balance_amt."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						break;
					}
					else if($tran_qty_bl<0)
					{
						$tran_qty_bl = $transfer_qnty2-$balance_qnty;				
						$amount = $balance_qnty*$cons_rate;
						
						//for insert
						if($data_array_mrr2!="") $data_array_mrr2 .= ",";  
						$data_array_mrr2 .= "(".$mrrWiseIsID.",".$recv_trans_id.",10,".$prev_trans_prod_id.",".$balance_qnty.",".$amount.",".$cbo_transfer_criteria.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$latest_balance_qty=$rowT[csf("balance_qnty")]+$balance_qnty;
						$latest_balance_amt=$rowT[csf("balance_amount")]+$amount;
						
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",($latest_balance_qty."*".$latest_balance_amt."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						$transfer_qnty2 = $tran_qty_bl;
					}
				}
			}*/
			
			
			//echo "insert into inv_mrr_wise_issue_details (".$field_array_mrr2.") values ".$data_array_mrr2;
			//print_r($update_data);
			//die;
		}
		else
		{
			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			
			 //----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",0,".$hidden_product_id.",".$origin_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$hide_brand_id.",0,0,0,0,".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_btb_lc_id.")";
			
			//$id_trans=$id_trans+1;
			$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id.",0,".$hidden_product_id.",".$origin_product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$hide_brand_id.",0,0,0,0,".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_btb_lc_id.")";
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$hidden_product_id.",".$txt_yarn_lot.",".$hide_brand_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$cbo_uom.",".$txt_no_of_bag.",".$txt_no_of_cone.",".$txt_weight_per_bag.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=1 and status_active=1 and is_deleted=0");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			//$field_array_mrr2= "id,recv_trans_id,entry_form,prod_id,item_return_qty,amount,transfer_criteria,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and balance_qnty>0 and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");
			foreach($sql as $result)
			{				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				if($cons_rate=="") { $cons_rate=str_replace("'","",$txt_rate); }

				
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",10,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",10,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
			}//end foreach
 			// LIFO/FIFO END-----------------------------------------------//
		}
		

		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		//echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			if($flag==1) 
			{
				if($mrrWiseIssueID) $flag=1; else $flag=0; 
			} 
		}
		
		/*if($data_array_mrr2!="")
		{		
			$mrrWiseIssueID2=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr2,$data_array_mrr2,1);
			if($flag==1) 
			{
				if($mrrWiseIssueID2) $flag=1; else $flag=0; 
			} 
		}*/
		//echo $flag;die;
		//echo "10**"."insert into inv_mrr_wise_issue_details (".$field_array_mrr.") values ".$data_array_mrr;die;
		//transaction table stock update here------------------------//

		//echo "10**".$rID."#".$rID2."#".$rID3; die();
		
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			if($flag==1) 
			{
				if($upTrID) $flag=1; else $flag=0; 
			} 
		}
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 
			
			if(count($row_prod)>0)
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
				//echo "10**".$flag;die;
			}
			
			
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);   
				echo "0**".$transfer_update_id."**".$transfer_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$trans_stock=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as cons_quantity from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and status_active=1 and is_deleted=0");
		$current_trans_stock=$trans_stock[0][csf("cons_quantity")]+str_replace("'","",$hidden_transfer_qnty);
		if(str_replace("'","",$txt_transfer_qnty)>$current_trans_stock)
		{
			echo "30**Transfer Quantity Not Allow More Then Stock Quantity.";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$product_info = sql_select("select available_qnty,avg_rate_per_unit from product_details_master where id=$hidden_product_id");
		//$rate=return_field_value("avg_rate_per_unit","product_details_master","id=$cbo_item_desc");
			if(str_replace("'","",$txt_transfer_qnty) > ($product_info[0][csf("available_qnty")]+str_replace("'","",$hidden_transfer_qnty))){
				echo "21**Transfer quantity can not be greater than available quantity. available quantity= ".($product_info[0][csf("available_qnty")]+str_replace("'","",$hidden_transfer_qnty));
				die;
			}
		}
		if(str_replace("'","",$update_id)!="")
		{
			$rcv_trans_id= str_replace("'", "", $update_trans_recv_id);
			$issue_trans_id= str_replace("'", "", $update_trans_issue_id);
			
			$duplicate = is_duplicate_field("b.id"," inv_item_transfer_mst a, inv_transaction b","a.id=b.mst_id and a.id=$update_id and b.prod_id=$hidden_product_id and b.id not in($rcv_trans_id,$issue_trans_id) and b.transaction_type in(5,6)"); 
			if( $duplicate==1) 
			{
				echo "20**Duplicate Product is Not Allow in Same Transfer Number.";
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
		}

		/*#### Stop not eligible field from update operation start ####*/
		// to_company*
		// $cbo_company_id_to."*".
		/*#### Stop not eligible field from update operation end ####*/

		$field_array_update="challan_no*transfer_date*remarks*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		//*floor_id*room*rack*self
		
		$field_array_trans="supplier_id*prod_id*origin_prod_id*transaction_date*store_id*floor_id*room*rack*self*brand_id*order_qnty*order_rate*order_amount*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date*btb_lc_id";
		$field_array_dtls="from_prod_id*to_prod_id*yarn_lot*brand_id*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*transfer_qnty*rate*transfer_value*rate_in_usd*transfer_value_in_usd*uom*no_of_bag*no_of_cone*weight_per_bag*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		$hidden_product_id=str_replace("'","",$hidden_product_id);
		$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
		$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
		
		$all_prod_id=$hidden_product_id.",".$previous_from_prod_id.",".$previous_to_prod_id; $prod_arr=array(); $order_rate=0; $order_amount=0;
		$prodData=sql_select("select id, current_stock, avg_rate_per_unit, supplier_id, available_qnty from product_details_master where id in ($all_prod_id)");
		foreach($prodData as $row)
		{
			$prod_arr[$row[csf('id')]]['st']=$row[csf('current_stock')];
			$prod_arr[$row[csf('id')]]['rate']=$row[csf('avg_rate_per_unit')];
			$prod_arr[$row[csf('id')]]['sid']=$row[csf('supplier_id')];
			$prod_arr[$row[csf('id')]]['aq']=$row[csf('available_qnty')];
		}

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$updateProdID_array=array();
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value*available_qnty";
			
			if($hidden_product_id==$previous_from_prod_id)
			{
				$adjust_curr_stock_from=$prod_arr[$previous_from_prod_id]['st']+str_replace("'", '',$hidden_transfer_qnty)-str_replace("'","",$txt_transfer_qnty);
				$adjust_curr_availlable_from=$prod_arr[$previous_from_prod_id]['aq']+str_replace("'", '',$hidden_transfer_qnty)-str_replace("'","",$txt_transfer_qnty);
				$cur_st_rate_from=$prod_arr[$previous_from_prod_id]['rate'];
				$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
				$updateProdID_array[]=$previous_from_prod_id; 
				$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$adjust_curr_availlable_from));
			}
			else
			{
				$adjust_curr_stock_from=$prod_arr[$previous_from_prod_id]['st']+str_replace("'", '',$hidden_transfer_qnty);
				$adjust_curr_availlable_from=$prod_arr[$previous_from_prod_id]['aq']+str_replace("'", '',$hidden_transfer_qnty);
				$cur_st_rate_from=$prod_arr[$previous_from_prod_id]['rate'];
				$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
				$updateProdID_array[]=$previous_from_prod_id; 
				$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$adjust_curr_availlable_from));
				
				$presentStock=$prod_arr[$hidden_product_id]['st']-str_replace("'","",$txt_transfer_qnty);
				$presentAvaillable=$prod_arr[$hidden_product_id]['aq']-str_replace("'","",$txt_transfer_qnty);
				$presentAvgRate=$prod_arr[$hidden_product_id]['rate'];
				$presentStockValue=$presentStock*$presentAvgRate;
				$updateProdID_array[]=$hidden_product_id; 
				$data_array_adjust[$hidden_product_id]=explode("*",("".$presentStock."*".$presentAvgRate."*".$presentStockValue."*".$presentAvaillable));
			}
			
			$supplier_id=$prod_arr[$hidden_product_id]['sid'];
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, available_qnty, dyed_type from product_details_master where company_id=$cbo_company_id_to and item_category_id=1 and supplier_id='$supplier_id' and product_name_details=$txt_item_desc and lot=$txt_yarn_lot and status_active=1 and is_deleted=0");
			
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				if($product_id==$previous_to_prod_id)
				{
					$stock_qnty=$row_prod[0][csf('current_stock')];
					//$stock_qnty=$row_prod[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty);
					$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$hidden_transfer_qnty);
					//$curr_stock_qnty=$stock_qnty-str_replace("'", '',$txt_transfer_qnty);
					$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					$curr_available_qnty=$row_prod[0][csf('available_qnty')]+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$hidden_transfer_qnty);
					
					if($curr_stock_qnty<0)
					{
						echo "30**Stock cannot be less than zero.";
						//check_table_status( $_SESSION['menu_id'],0);
						disconnect($con);
						die;
					}
					
					$updateProdID_array[]=$product_id; 
					$data_array_adjust[$product_id]=explode("*",("".$curr_stock_qnty."*".$avg_rate_per_unit."*".$stock_value."*".$curr_available_qnty));
				}
				else
				{
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
					$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					$curr_availlable_qnty=$row_prod[0][csf('available_qnty')]+str_replace("'", '',$txt_transfer_qnty);
					
					$adjust_curr_stock_to=$stock_qnty-str_replace("'", '',$hidden_transfer_qnty);
					$adjust_curr_availlable_to=$row_prod[0][csf('available_qnty')]-str_replace("'", '',$hidden_transfer_qnty);
					$cur_st_value_to=$adjust_curr_stock_to*$avg_rate_per_unit;
					$updateProdID_array[]=$previous_to_prod_id; 
					$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$avg_rate_per_unit."*".$cur_st_value_to."*".$adjust_curr_availlable_to));
					
					if($adjust_curr_stock_to<0)
					{
						echo "30**Stock cannot be less than zero.";
						//check_table_status( $_SESSION['menu_id'],0);
						disconnect($con);
						die;
					}
					
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$curr_availlable_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
			}
			else
			{
				$adjust_curr_stock_to=$prod_arr[$previous_to_prod_id]['st']-str_replace("'", '',$hidden_transfer_qnty);
				$adjust_curr_availlable_to=$prod_arr[$previous_to_prod_id]['aq']-str_replace("'", '',$hidden_transfer_qnty);
				$avg_rate_per_unit=$prod_arr[$previous_to_prod_id]['rate'];
				$cur_st_value_to=$adjust_curr_stock_to*$avg_rate_per_unit;
				$updateProdID_array[]=$previous_to_prod_id; 
				$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$avg_rate_per_unit."*".$cur_st_value_to."*".$adjust_curr_availlable_to));
				
				if($adjust_curr_stock_to<0)
				{
					echo "30**Stock cannot be less than zero.";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
				
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				//$avg_rate_per_unit=$prod_arr[$hidden_product_id]['rate'];
				$avg_rate_per_unit=str_replace("'","",$txt_rate);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				$available_qnty=str_replace("'","",$txt_transfer_qnty);
				
				$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, available_qnty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, inserted_by, insert_date, dyed_type) 
				select	
				'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, $available_qnty, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand,'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."', dyed_type from product_details_master where id=$hidden_product_id";
			}
			//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array).count($row_prod);die;
			

             //----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id = $cbo_store_name and id <> $update_trans_recv_id  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			
			 //----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to and id not in ($update_trans_recv_id , $update_trans_issue_id )  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}



			$recv_qty=0; $recv_amnt=0;
			$sql_receive="select a.receive_date, a.currency_id, sum(b.order_qnty) as qty, sum(b.order_qnty*b.order_rate) as amnt from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.entry_form=1 and a.item_category=1 and b.transaction_type=1 and b.item_category=1 and b.status_active=1 and b.is_deleted=0 and b.prod_id=$hidden_product_id group by a.receive_date, a.currency_id";
			$resultReceive = sql_select($sql_receive);
			foreach($resultReceive as $row)
			{
				$recv_qty+=$row[csf('qty')];
				if($row[csf('currency_id')]==1)
				{
					$exchange_rate=set_conversion_rate(2, $row[csf('receive_date')]);
					//echo "10**".$exchange_rate;die;
					if($exchange_rate<=0) {$exchange_rate=76;}
					$recv_amnt+=$row[csf('amnt')]/$exchange_rate;
				}
				else
				{
					$recv_amnt+=$row[csf('amnt')];
				}
			}
			
			$sql_trans="select sum(order_qnty) as qty, sum(order_amount) as amnt from inv_transaction where transaction_type=5 and item_category=1 and status_active=1 and is_deleted=0 and prod_id=$hidden_product_id";
			$resultTrans = sql_select($sql_trans);
			$trnas_recv_qty=$resultTrans[0][csf('qty')];
			$trnas_recv_amnt=$resultTrans[0][csf('amnt')];
			
			$tot_recv_qty=$recv_qty+$trnas_recv_qty;
			$tot_recv_amnt=$recv_amnt+$trnas_recv_amnt;
			
			$order_rate=$tot_recv_amnt/$tot_recv_qty;
			$order_amount=$order_rate*str_replace("'","",$txt_transfer_qnty);
			
			/*$sql_receive="select sum(order_qnty) as qty, sum(order_qnty*order_rate) as amnt from inv_transaction where transaction_type in(1,5) and item_category=1 and status_active=1 and is_deleted=0 and prod_id=$hidden_product_id";
			$resultReceive = sql_select($sql_receive);
			
			$order_rate=$resultReceive[0][csf('amnt')]/$resultReceive[0][csf('qty')];
			$order_amount=$order_rate*str_replace("'","",$txt_transfer_qnty);*/
			//*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."
			//*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."
			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("'".$supplier_id."'*'".$hidden_product_id."'*".$origin_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$hide_brand_id."*'0'*'0'*'0'*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_btb_lc_id));
			
			$updateTransID_array[]=$update_trans_recv_id; 
			$updateTransID_data[$update_trans_recv_id]=explode("*",("'".$supplier_id."'*'".$product_id."'*".$origin_product_id."*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$hide_brand_id."*'".$txt_transfer_qnty."'*'".$order_rate."'*'".$order_amount."'*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*'".$txt_transfer_qnty."'*'".$txt_transfer_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_btb_lc_id));
			
			$data_array_dtls=$hidden_product_id."*'".$product_id."'*".$txt_yarn_lot."*".$hide_brand_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*'".$txt_transfer_qnty."'*".$txt_rate."*".$txt_transfer_value."*'".$order_rate."'*'".$order_amount."'*".$cbo_uom."*".$txt_no_of_bag."*".$txt_no_of_cone."*".$txt_weight_per_bag."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//transaction table START--------------------------//
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=10 and a.item_category=1"); 
			$updateID_array = array();
			$update_data = array();
			foreach($sql as $result)
			{
				$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
				$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
				$updateID_array[]=$result[csf("id")]; 
				$update_data[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
				
				$trans_data_array[$result[csf("id")]]['qnty']=$adjBalance;
				$trans_data_array[$result[csf("id")]]['amnt']=$adjAmount;
			}

			
			//print_r($update_data);
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=1 and status_active=1 and is_deleted=0");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			//$field_array_mrr2= "id,recv_trans_id,entry_form,prod_id,item_return_qty,amount,transfer_criteria,inserted_by,insert_date";
			
			
			
			//echo "10**select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo";print_r($trans_data_array);die;
			
			$transId=implode(",",$updateID_array);
			if($hidden_product_id==$previous_from_prod_id) $balance_cond="(balance_qnty>0 or id in($transId))";
			else $balance_cond="balance_qnty>0";
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");
			foreach($sql as $result)
			{				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				if($trans_data_array[$recv_trans_id]['qnty']=="")
				{
					$balance_qnty = $result[csf("balance_qnty")];
					$balance_amount = $result[csf("balance_amount")];
				}
				else
				{
					$balance_qnty = $trans_data_array[$recv_trans_id]['qnty'];
					$balance_amount = $trans_data_array[$recv_trans_id]['amnt'];
				}
				//echo $balance_qnty."**";
				$cons_rate = $result[csf("cons_rate")];
				if($cons_rate=="") { $cons_rate=$txt_rate; }
				
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",10,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					if(!in_array($recv_trans_id,$updateID_array))
					{
						$updateID_array[]=$recv_trans_id; 
					}
					
					$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",10,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					if(!in_array($recv_trans_id,$updateID_array))
					{
						$updateID_array[]=$recv_trans_id; 
					}
					
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
			}//end foreach
			
			
			
			/*
			$deleted_id='';$data_array_mrr2='';
			//#### consult with cto sir this operation will be hold
			
			$prevTransArr=array(); 
			$dataTransAdj=sql_select("select a.id, a.recv_trans_id, a.item_return_qty, a.amount, b.balance_qnty, b.cons_rate, b.balance_amount from inv_mrr_wise_issue_details a, inv_transaction b where a.recv_trans_id=b.id and a.prod_id=$previous_to_prod_id and a.entry_form=10 and a.transfer_criteria=1 and a.status_active=1 and a.is_deleted=0");
			foreach($dataTransAdj as $rowAdj)
			{
				$rowAdjQty=$rowAdj[csf('balance_qnty')]-$rowAdj[csf('item_return_qty')];
				$rowAdjAmt=$rowAdj[csf('balance_amount')]-$rowAdj[csf('amount')];
				$prevTransArr[$rowAdj[csf('recv_trans_id')]][1]=$rowAdjQty;
				$prevTransArr[$rowAdj[csf('recv_trans_id')]][2]=$rowAdjAmt;
				$updateID_array[]=$rowAdj[csf('recv_trans_id')]; 
				$update_data[$rowAdj[csf('recv_trans_id')]]=explode("*",($rowAdjQty."*".$rowAdjAmt."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				
				if($deleted_id=='') $deleted_id=$rowAdj[csf('id')]; else $deleted_id.=",".$rowAdj[csf('id')];
			}
			
			
			$prev_trans_prod_id=return_field_value("distinct(from_prod_id) as prod_id","inv_item_transfer_dtls","to_prod_id=$hidden_product_id and status_active=1 and is_deleted=0","prod_id");
			if($prev_trans_prod_id!="")
			{	
				$transfer_qnty2 = str_replace("'","",$txt_transfer_qnty);
				$dataTrans=sql_select("select a.recv_trans_id, a.issue_qnty, a.amount, b.balance_qnty, b.cons_rate, b.balance_amount from inv_mrr_wise_issue_details a, inv_transaction b where a.recv_trans_id=b.id and a.prod_id=$prev_trans_prod_id and a.entry_form=10 and a.transfer_criteria=0 and a.status_active=1 and a.is_deleted=0 order by a.recv_trans_id");
				foreach($dataTrans as $rowT)
				{
					$mrrWiseIsID++; 
					$cons_rate = $rowT[csf("cons_rate")];
					$recv_trans_id = $rowT[csf("recv_trans_id")];
					$balance_qnty = $rowT[csf("issue_qnty")];
					$balance_amount = $rowT[csf("amount")];
					
					if($prevTransArr[$recv_trans_id][1]==="")
					{
						$bal_qnty = $rowT[csf("balance_qnty")];
						$bal_amount = $rowT[csf("balance_amount")];
					}
					else
					{
						$bal_qnty = $prevTransArr[$recv_trans_id][1];
						$bal_amount = $prevTransArr[$recv_trans_id][2];
					}
					
					$tran_qty_bl=$balance_qnty-$transfer_qnty2;
					$tran_amnt_bl = $balance_amount-($transfer_qnty2*$cons_rate);
					
					if($tran_qty_bl>=0)
					{			
						$amount = $transfer_qnty2*$cons_rate;
						//for insert
						if($data_array_mrr2!="") $data_array_mrr2 .= ",";  
						$data_array_mrr2 .= "(".$mrrWiseIsID.",".$recv_trans_id.",10,".$prev_trans_prod_id.",".$transfer_qnty2.",".$amount.",".$cbo_transfer_criteria.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$latest_balance_qty=$bal_qnty+$transfer_qnty2;
						$latest_balance_amt=$bal_amount+$amount;
						
						if(!in_array($recv_trans_id,$updateID_array))
						{
							$updateID_array[]=$recv_trans_id;
						} 
						$update_data[$recv_trans_id]=explode("*",($latest_balance_qty."*".$latest_balance_amt."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						break;
					}
					else if($tran_qty_bl<0)
					{
						$tran_qty_bl = $transfer_qnty2-$balance_qnty;				
						$amount = $balance_qnty*$cons_rate;
						//for insert
						if($data_array_mrr2!="") $data_array_mrr2 .= ",";  
						$data_array_mrr2 .= "(".$mrrWiseIsID.",".$recv_trans_id.",10,".$prev_trans_prod_id.",".$balance_qnty.",".$amount.",".$cbo_transfer_criteria.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$latest_balance_qty=$bal_qnty+$balance_qnty;
						$latest_balance_amt=$bal_amount+$amount;
						
						if(!in_array($recv_trans_id,$updateID_array))
						{
							$updateID_array[]=$recv_trans_id;
						}
						$update_data[$recv_trans_id]=explode("*",($latest_balance_qty."*".$latest_balance_amt."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						$transfer_qnty2 = $tran_qty_bl;
					}
				}
			}*/
			
			
			//print_r($update_data);
			//echo "insert into inv_mrr_wise_issue_details (".$field_array_mrr2.") values ".$data_array_mrr2;
			//die;
			//echo "10**test";
		}
		else
		{


            //----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name and id <> $update_trans_recv_id  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}

             //----------------Check Last Transaction Date for Transfer In---------------->>>>>>>
			$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id = $cbo_store_name_to and id not in ($update_trans_recv_id , $update_trans_issue_id)  and status_active = 1", "max_date");      
			if($max_transaction_date !="")
			{
				$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_transaction_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}

			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("0*'".$hidden_product_id."'*".$origin_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$hide_brand_id."*0*0*0*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_btb_lc_id));
			
			$updateTransID_array[]=$update_trans_recv_id; 
			$updateTransID_data[$update_trans_recv_id]=explode("*",("0*'".$hidden_product_id."'*".$origin_product_id."*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$hide_brand_id."*0*0*0*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$txt_transfer_qnty."*".$txt_transfer_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_btb_lc_id));
			
			$data_array_dtls=$hidden_product_id."*".$hidden_product_id."*".$txt_yarn_lot."*".$hide_brand_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$cbo_uom."*".$txt_no_of_bag."*".$txt_no_of_cone."*".$txt_weight_per_bag."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=1 and status_active=1 and is_deleted=0");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			//$field_array_mrr2= "id,recv_trans_id,entry_form,prod_id,item_return_qty,amount,transfer_criteria,inserted_by,insert_date";
			
			
			
			//echo "10**select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo";print_r($trans_data_array);die;
			
			if($hidden_product_id==$previous_from_prod_id) $balance_cond="(balance_qnty>0 or id in($rcv_trans_id))";
			else $balance_cond="balance_qnty>0";
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and store_id=$cbo_store_name and $balance_cond and transaction_type in (1,4,5) and item_category=1 order by transaction_date $cond_lifofifo");
			foreach($sql as $result)
			{				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				if($trans_data_array[$recv_trans_id]['qnty']=="")
				{
					$balance_qnty = $result[csf("balance_qnty")];
					$balance_amount = $result[csf("balance_amount")];
				}
				else
				{
					$balance_qnty = $trans_data_array[$recv_trans_id]['qnty'];
					$balance_amount = $trans_data_array[$recv_trans_id]['amnt'];
				}
				//echo $balance_qnty."**";
				$cons_rate = $result[csf("cons_rate")];
				if($cons_rate=="") { $cons_rate=$txt_rate; }
				
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",10,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					if(!in_array($recv_trans_id,$updateID_array))
					{
						$updateID_array[]=$recv_trans_id; 
					}
					
					$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",10,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					if(!in_array($recv_trans_id,$updateID_array))
					{
						$updateID_array[]=$recv_trans_id; 
					}
					
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
			}//end foreach
		}
		
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array);die;
		
		$rID=$rID2=$rID3=$query=$query2=$mrrWiseIssueID=true;
		
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}

		 $rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}

		
		//transaction table stock update here------------------------//
		if(count($updateID_array)>0)
		{
			//echo "10**".bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
			$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			if($flag==1) 
			{
				if($query) $flag=1; else $flag=0; 
			} 
		}
		
		
		
		$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=10");
		if($flag==1) 
		{
			if($query2) $flag=1; else $flag=0; 
		}
		

		
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			if($flag==1) 
			{
				if($mrrWiseIssueID) $flag=1; else $flag=0; 
			} 
		}
		
		//echo "10**$rID=$rID2=$rID3"; die();
		//echo "10**$rID=$rID2=$rID3=$query=$query2=$mrrWiseIssueID";check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}
			
			if(count($row_prod)>0)
			{
				if($product_id!=$previous_to_prod_id)
				{
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					}
				}
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}
			}
			
			
			//echo "10**";print_r($update_data);die;

			
			//echo $deleted_id;die;
			/*if($deleted_id!=''){
				$query3 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE id in($deleted_id) and entry_form=10 and transfer_criteria=1");
				if($flag==1) 
				{
					if($query3) $flag=1; else $flag=0; 
				} 
			}
			
			if($data_array_mrr2!="")
			{		
				$mrrWiseIssueID2=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr2,$data_array_mrr2,1);
				if($flag==1) 
				{
					if($mrrWiseIssueID2) $flag=1; else $flag=0; 
				} 
			}*/
			//echo "10**".$flag."insert into inv_mrr_wise_issue_details (".$field_array_mrr2.") values ".$data_array_mrr2;die;
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			
			if($flag==1)
			{
				oci_commit($con);   
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_system_id)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**1";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	}
}

if($action=="yarn_transfer_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id, item_category,remarks from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=1","id","product_name_details");
	$brand_arr=return_library_array( "select id, brand_name from   lib_brand", "id", "brand_name"  );
	?>
	<div style="width:1130px;">
		<table width="1100" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">  
					<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
						?>
						Plot No: <? echo $result['plot_no']; ?> 
						Level No: <? echo $result['level_no']?>
						Road No: <? echo $result['road_no']; ?> 
						Block No: <? echo $result['block_no'];?> 
						City No: <? echo $result['city'];?> 
						Zip Code: <? echo $result['zip_code']; ?> 
						Province No: <?php echo $result['province'];?> 
						Country: <? echo $country_arr[$result['country_id']]; ?><br> 
						Email Address: <? echo $result['email'];?> 
						Website No: <? echo $result['website'];
					}
					?> 
				</td>  
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
			</tr>
			<tr>
				<td width="120"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="130"><strong>Transfer Criteria:</strong></td> <td width="175px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td width="125"><strong>To Company</strong></td><td width="175px"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Item Category:</strong></td> <td width="175px"><? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
			</tr>
			<tr>           
				<td><strong>Remarks:</strong></td>             
				<td width="465px" colspan="5"><? echo $dataArray[0][csf('remarks')]; ?></td>         
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="1100"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="40">SL</th>
					<th width="100" >From Store</th>
					<th width="100" >To Store</th>
					<th width="250" >Item Description</th>
					<th width="50" >Yarn Lot</th>
					<th width="100" >Transfered Qnty</th>
					<th width="100" >No of Bag</th>
					<th width="100" >No of Cone</th>
					<th width="110" >Yarn Brand</th> 
				</thead>
				<tbody> 

					<?
					$sql_dtls="select id, from_store, to_store, from_prod_id, yarn_lot, transfer_qnty, brand_id, no_of_bag, no_of_cone from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
					$sql_result= sql_select($sql_dtls);
					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";

						$transfer_qnty=$row[csf('transfer_qnty')];
						$no_of_bag_sum+=$row[csf('no_of_bag')];
						$no_of_cone_sum+=$row[csf('no_of_cone')];
						$transfer_qnty_sum += $transfer_qnty;

						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td align="center"><? echo $store_library[$row[csf("from_store")]]; ?></td>
							<td align="center"><? echo $store_library[$row[csf("to_store")]]; ?></td>
							<td align="center"><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td align="center"><? echo $row[csf("yarn_lot")]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
							<td align="right"><? echo $row[csf("no_of_bag")]; ?></td>
							<td align="right"><? echo $row[csf("no_of_cone")]; ?></td>
							<td align="center"><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
						</tr>
						<? $i++; } ?>
					</tbody>
					<tfoot>
						<tr style="font-weight: bold;">
							<td colspan="5" align="right"><strong>Total :</strong></td>
							<td align="right"><?php echo $transfer_qnty_sum; ?></td>
							<td align="right"><?php echo $no_of_bag_sum; ?></td>
							<td align="right"><?php echo $no_of_cone_sum; ?></td>
							<td align="right"><?php //echo $req_qny_edit_sum; ?></td>
						</tr>                           
					</tfoot>
				</table>
				<br>
				<?
				echo signature_table(38, $data[0], "900px");
				?>
			</div>
		</div>   
		<?
		exit();	
	}

	if ($action=="btb_selection_popup"){
		echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
		extract($_REQUEST);
		$supplier_arr = return_library_array( "select id,supplier_name from lib_supplier",'id','supplier_name');
		$company_arr=return_library_array( "select id,company_name from lib_company",'id','company_name');
		$suplier_cond="";
	//if($supplier>0) $suplier_cond=" and a.supplier_id=$supplier and b.supplier_id=$supplier";
		$sql="select d.id, d.lc_number, d.importer_id, d.lc_date, d.last_shipment_date, a.supplier_id from product_details_master a, inv_transaction b, com_btb_lc_pi c, com_btb_lc_master_details d where a.id=b.prod_id and b.pi_wo_batch_no=c.pi_id and c.com_btb_lc_master_details_id=d.id and a.item_category_id=1 and b.item_category = 1 and b.receive_basis = 1 and b.transaction_type=1 and a.lot='$lot_no' and a.company_id=$comany_name and b.company_id=$comany_name $suplier_cond group by d.id, d.lc_number, d.importer_id, d.lc_date, d.last_shipment_date, a.supplier_id";
        //echo $sql;
		?>
		<script>
			function js_set_value(data)
			{ 
				var splitSTR = data.split("**");
				var id = splitSTR[0];
				var lc_number = splitSTR[1];

				$("#hidden_btb_id").val(id);  
				$("#hidden_btb_lc_no").val(lc_number);  
				parent.emailwindow.hide();
			}
		</script>
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="700" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
				<thead>
					<tr>
						<th width="50">SL</th>
						<th width="150">LC NO.</th>
						<th width="150">Importer</th>
						<th width="150">Supplier Name</th>	
						<th width="80">LC Date</th>
						<th>Last Shipment Date</th>
					</tr>
				</thead>
				<tbody>
					<? $nameArray=sql_select( $sql );
					$i=1;
					foreach ($nameArray as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('id')].'**'.$row[csf('lc_number')]; ?>')" id="tr_<? echo $i; ?>" style=" cursor: pointer;"> 
							<td><? echo $i;?></td>
							<td><? echo $row[csf('lc_number')]?></td>
							<td><? echo $company_arr[$row[csf('importer_id')]];?></td>
							<td><? echo $supplier_arr[$row[csf('supplier_id')]];?></td>
							<td><? echo ($row[csf('lc_date')] != "")? date("d-m-Y",strtotime($row[csf('lc_date')])): "";?></td>
							<td><? echo date("d-m-Y",strtotime($row[csf('last_shipment_date')]));?></td>
						</tr>
						<?
						$i++;
					}?>
				</tbody>
				<tfoot>
					<input type="hidden" id="hidden_btb_id" value="">
					<input type="hidden" id="hidden_btb_lc_no" value="">
				</tfoot>

			</table>    
		</form>
		<?
	}
	?>
