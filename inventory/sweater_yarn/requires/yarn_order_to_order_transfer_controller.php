<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');


//load drop down rack self done
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/yarn_order_to_order_transfer_controller",$data);
}

if($action=="upto_variable_settings")
{
    $sql =  sql_select("select store_method from variable_settings_inventory where company_name = $data and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('store_method')];
	}
	else
	{
		$return_data=0;
	}

	echo $return_data;
	die;
}

if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}
	</script>
</head>

<body>
	<div align="center" style="width:880px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:870px;margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
					<thead>
						<th width="220">Buyer Name</th>
                        <th width="120">Year</th>
						<th width="160">Job No</th>
                        <th width="160">Style No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
							?>
						</td>
                        <td><? echo create_drop_down( "cbo_year", 100, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" ); ?></td> 
						<td>
							<input type="text" style="width:130px;" class="text_boxes" name="txt_job_no" id="txt_job_no" />
						</td>
                        <td>
							<input type="text" style="width:130px;" class="text_boxes" name="txt_style_no" id="txt_style_no" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_job_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('txt_style_no').value+'_'+'<? echo $type; ?>'+'_'+'<? echo $cbo_store_name; ?>'+'_'+'<? echo $transfer_criteria; ?>', 'create_po_search_list_view', 'search_div', 'yarn_order_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);
	$company_name_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$buyer_id=trim($data[0]);
	$job_no=trim($data[1]);
	$company_id=trim($data[2]);
	$year_id=trim($data[3]);
	$style_no=trim($data[4]);
	$type=$data[5];
	$cbo_store_name=trim(str_replace("'","",$data[6]));
	$transfer_criteria=$data[7];
	//echo $type."**".$transfer_criteria;die;
	
	
	if($job_no =="" && $style_no =="" && $buyer_id ==0) { echo "Plese Select Buyer";die;}
	$sql_cond="";
	if($db_type==0)
	{
		if($year_id > 0) $sql_cond.=" and YEAR(a.insert_date)='$year_id'";
	}
	else if($db_type==2)
	{
		if($year_id > 0) $sql_cond.=" and to_char(a.insert_date,'YYYY')='$year_id'";
	}
	
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	if( ($transfer_criteria==6 && $type =="to") || ($transfer_criteria==7 && $type =="from") )
	{
		if($buyer_id > 0) $sql_cond.=" and b.buyer_id=$buyer_id";
		if($job_no != "") $sql_cond.=" and a.wo_number like '%$job_no'";
		if($style_no != "") $sql_cond.=" and a.style_no='$style_no'";
		
		$sql= "select a.id, a.wo_number_prefix_num as job_no_prefix_num, $year_field a.wo_number as job_no, a.company_name, b.buyer_id as buyer_name, b.style_no as style_ref_no, sum(b.supplier_order_quantity) as job_quantity, sum(b.supplier_order_quantity) as po_quantity 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b 
		where a.id=b.mst_id and a.company_name=$company_id $sql_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0
		group by a.id, a.wo_number_prefix_num, a.insert_date, a.wo_number, a.company_name, b.buyer_id, b.style_no
		order by a.wo_number";
		//echo $sql;die;  
		$arr=array (2=>$company_name_arr,3=>$buyer_name_arr);
		echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.", "150,70,150,150,150","850","200",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no,year,company_name,buyer_name,style_ref_no,job_quantity", "","",'0,0,0,0,0,1');
	}
	else
	{
		
		if($buyer_id > 0) $sql_cond.=" and a.buyer_name=$buyer_id";
		if($job_no != "") $sql_cond.=" and a.job_no like '%$job_no'";
		if($style_no != "") $sql_cond.=" and a.style_ref_no='$style_no'";
		
		
		$sql= "select a.id, a.job_no_prefix_num, $year_field a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, sum(b.po_quantity) as po_quantity 
		from wo_po_details_master a, wo_po_break_down b 
		where a.job_no=b.job_no_mst and a.company_name=$company_id $sql_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0
		group by a.id, a.job_no_prefix_num,a.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity
		order by a.job_no";
		//echo $sql;die;  
		$arr=array (2=>$company_name_arr,3=>$buyer_name_arr);
		echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.", "150,70,150,150,150","850","200",0, $sql , "js_set_value", "id,job_no", "", 1, "0,0,company_name,buyer_name,0", $arr , "job_no,year,company_name,buyer_name,style_ref_no,job_quantity", "","",'0,0,0,0,0,1');
	}	
	exit();
}

if ($action == "load_drop_down_floor") {

	echo create_drop_down("cbo_floor", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_room") {

	echo create_drop_down("cbo_room", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_rack") {

	echo create_drop_down("txt_rack", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_shelf") {

	echo create_drop_down("txt_shelf", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}
if ($action == "load_drop_down_bin") {

	echo create_drop_down("cbo_bin", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 1);
	exit();
}

if ($action == "load_drop_down_floor_to") {

	echo create_drop_down("cbo_floor_to", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 0);
	exit();
}
if ($action == "load_drop_down_room_to") {

	echo create_drop_down("cbo_room_to", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 0);
	exit();
}
if ($action == "load_drop_down_rack_to") {

	echo create_drop_down("txt_rack_to", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 0);
	exit();
}
if ($action == "load_drop_down_shelf_to") {

	echo create_drop_down("txt_shelf_to", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 0);
	exit();
}
if ($action == "load_drop_down_bin_to") {

	echo create_drop_down("cbo_bin_to", 152, "select FLOOR_ROOM_RACK_ID, FLOOR_ROOM_RACK_NAME from LIB_FLOOR_ROOM_RACK_MST where COMPANY_ID = '$data'", "FLOOR_ROOM_RACK_ID,FLOOR_ROOM_RACK_NAME", 1, "-- Select --", 0, "", 0);
	exit();
}

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$job_id=$data[0];
	$which_order=$data[1];
	$transfer_criteria=$data[2];
	if( ($transfer_criteria==6 && $which_order =="to") || ($transfer_criteria==7 && $which_order =="from") )
	{
		$data_array=sql_select("select a.wo_number as job_no, b.buyer_id as buyer_name, b.style_no as style_ref_no, 0 as gmts_item_id, sum(b.supplier_order_quantity) as job_quantity, sum(b.supplier_order_quantity) as po_quantity 
		from wo_non_order_info_mst a, wo_non_order_info_dtls b 
		where a.id=b.mst_id and a.id=$job_id
		group by a.wo_number, b.buyer_id, b.style_no");
	}
	else
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, a.job_quantity from wo_po_details_master a where a.id=$job_id");
	}
	
	foreach ($data_array as $row)
	{ 
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		
		echo "document.getElementById('txt_".$which_order."_job_id').value 				= '".$job_id."';\n";
		echo "document.getElementById('txt_".$which_order."_job').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("job_quantity")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		//echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
		exit();
	}
}

if($action=='populate_data_from_item_stock')
{
	$data=explode("**",$data);
	$job_no=$data[0];
	$prod_id=$data[1];
	$cbo_store_name=$data[2];
	$company = $data[3];
	$sql="select b.unit_of_measure, a.floor_id, a.room, a.rack, a.self, a.bin_box,
		sum(CASE WHEN a.transaction_type=1 THEN a.cons_quantity ELSE 0 END) AS rcv_qnty_yarn,
		sum(CASE WHEN a.transaction_type=2 THEN a.cons_quantity ELSE 0 END) AS iss_qnty_yarn,
		sum(CASE WHEN a.transaction_type=4 THEN a.cons_quantity ELSE 0 END) AS issue_return_qnty,
		sum(CASE WHEN a.transaction_type=3 THEN a.cons_quantity ELSE 0 END) AS rcv_return_qnty,
		sum(CASE WHEN a.transaction_type=5 THEN a.cons_quantity ELSE 0 END) AS transfer_in_qnty,
		sum(CASE WHEN a.transaction_type=6 THEN a.cons_quantity ELSE 0 END) AS transfer_out_qnty
		from inv_transaction a, product_details_master b where a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and a.job_no='$job_no' and a.prod_id='$prod_id' and a.store_id=$cbo_store_name and a.entry_form in(248,249,277,382)
		group by b.unit_of_measure, a.floor_id, a.room, a.rack, a.self, a.bin_box";
	//echo $sql;
	$dataArray=sql_select($sql);
	
	$tot_issued_qty=$dataArray[0][csf('iss_qnty_yarn')]+$dataArray[0][csf('rcv_return_qnty')]+$dataArray[0][csf('transfer_out_qnty')];
	$tot_rcv_qty=$dataArray[0][csf('rcv_qnty_yarn')]+$dataArray[0][csf('issue_return_qnty')]+$dataArray[0][csf('transfer_in_qnty')];
	$tot_transfer_qty=$dataArray[0][csf('transfer_out_qnty')];
	$transferable_qnty=$tot_rcv_qty-$tot_issued_qty;
	
	echo "document.getElementById('txt_cum_issue_qnty').value 				= '".$transferable_qnty."';\n";
	echo "document.getElementById('txt_tot_transfer_qnty').value 			= '".$tot_transfer_qty."';\n";
	echo "document.getElementById('txt_transferable_qnty').value 			= '".$transferable_qnty."';\n";
	echo "document.getElementById('cbo_uom').value 							= '".$dataArray[0][csf('unit_of_measure')]."';\n";
	
	echo "document.getElementById('cbo_floor').value 		= '".$dataArray[0][csf('floor_id')]."';\n";
	echo "document.getElementById('cbo_room').value 		= '".$dataArray[0][csf('room')]."';\n";
	echo "document.getElementById('txt_rack').value 		= '".$dataArray[0][csf('rack')]."';\n";
	echo "document.getElementById('txt_shelf').value 		= '".$dataArray[0][csf('self')]."';\n";
	// echo "document.getElementById('cbo_bin').value 			= '".$dataArray[0][csf('bin_box')]."';\n";

	exit();
	
}

if($action=="load_drop_down_item_desc")
{
	$data_ref=explode("**",$data);
	$job_no=$data_ref[0];
	$store_name=$data_ref[1];
	$item_description=array();
	$sql="select a.id, a.product_name_details, a.lot from product_details_master a, inv_transaction b where a.id=b.prod_id and b.job_no='$job_no' and b.store_id=$store_name and b.transaction_type in(1,5) and b.status_active=1 and b.is_deleted=0";
	//echo $sql;die;
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('lot')]."**".$row[csf('product_name_details')];
	}
	echo create_drop_down( "cbo_item_desc", 250, $item_description,'', 1, "--Select Item Description--",'0','load_all_dropdowns();load_item_stock_data(this.value);','');  
	exit();
}

if ($action=="orderToorderTransfer_popup")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
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
	<div align="center" style="width:780px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:760px;margin-left:10px">
				<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
					<thead>
						<th>Search By</th>
						<th width="240" id="search_by_td_up">Please Enter Transfer ID</th>
                        <th>Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
							<?
							$search_by_arr=array(1=>"Transfer ID",2=>"Challan No.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
                        <td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date"/>&nbsp; To
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date"/>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'yarn_order_to_order_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
                    <tr>
                        <td align="center" height="40" valign="middle" colspan="4">
                            <? echo load_month_buttons(1); ?>
                            
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

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$company_id =$data[2];
	$date_form =$data[3];
	$date_to =$data[4];
	if(trim($data[0]) == "" && $date_form == "" && $date_to == "")
	{
		echo "Please Select Date Range";die;
	}
	
	if($date_form != "" && $date_to != "")
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
		$sql_cond .= " and transfer_date between '$date_form' and '$date_to'";
	}
	if($search_string!="")
	{
		if($search_by==1)
			$sql_cond .= " and transfer_prefix_number=$search_string";	
		else
			$sql_cond .= " and challan_no='$search_string'";
	}
	
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=1 and company_id=$company_id and status_active=1 and is_deleted=0 and entry_form=249 $sql_cond order by id desc";
	
	//echo $sql;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);
	
	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,110,90,140","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, item_category, from_order_id, to_order_id, from_store_id, to_store_id, transfer_criteria, to_company, remarks from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store_id")]."';\n";
		if($row[csf("transfer_criteria")]==1)
		{
			echo "load_room_rack_self_bin('requires/yarn_order_to_order_transfer_controller*1*cbo_store_name_to', 'store','to_store_td', '".$row[csf("to_company")]."');\n";
		}
		echo "document.getElementById('cbo_store_name_to').value 			= '".$row[csf("to_store_id")]."';\n";
		
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from**".$row[csf("transfer_criteria")]."'".",'populate_data_from_order','requires/yarn_order_to_order_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to**".$row[csf("transfer_criteria")]."'".",'populate_data_from_order','requires/yarn_order_to_order_transfer_controller');\n";
		
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";	
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";	
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";	
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";	
		echo "$('#cbo_store_name_to').attr('disabled','disabled');\n";	
		echo "$('#txt_from_job').attr('disabled','disabled');\n";
		echo "$('#txt_to_job').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=1","id","product_name_details");
	
	$sql="select id, from_prod_id, transfer_qnty, item_category, uom from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement);
	
	echo create_list_view("list_view", "Item Category,Item Description,Transfered Qnty,UOM", "130,350,140","750","200",0, $sql, "load_all_dropdowns();load_all_dropdowns_to(); get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "item_category,from_prod_id,0,uom", $arr, "item_category,from_prod_id,transfer_qnty,uom", "requires/yarn_order_to_order_transfer_controller",'','0,0,2,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	$data_array=sql_select("select id, mst_id, from_prod_id, from_store, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, to_prod_id, transfer_qnty, item_category, uom from inv_item_transfer_dtls where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hide_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		
		//echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		//echo "document.getElementById('cbo_store_name_to').value 			= '".$row[csf("to_store")]."';\n";
		
		$prod_id=$row[csf("from_prod_id")].",".$row[csf("to_prod_id")];
		$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and item_category=1 and transaction_type in(5,6) and entry_form=249 and prod_id in($prod_id) order by id asc");
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("id")]."';\n";
		echo "load_item_stock_data('".$row[csf("from_prod_id")]."');\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		echo "document.getElementById('cbo_floor_to').value 				= '".$row[csf("to_floor_id")]."';\n";
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		echo "document.getElementById('txt_shelf_to').value 				= '".$row[csf("to_shelf")]."';\n";
		echo "document.getElementById('cbo_bin_to').value 				= '".$row[csf("to_bin_box")]."';\n";


		exit();
	}
}

if ($action=="orderInfo_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 

</head>

<body>
	<div align="center" style="width:770px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:760px;margin-left:15px">
				<legend><? echo ucfirst($type); ?> Order Info</legend>
				<br>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr bgcolor="#FFFFFF">
						<td align="center"><? echo ucfirst($type); ?> Order No: <b><? echo $txt_order_no; ?></b></td>
					</tr>
				</table>
				<br>
				<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="750" align="center">
					<thead>
						<th width="40">SL</th>
						<th width="100">Required</th>
						<th width="100">Issued</th>
						<th width="100">Issue Return</th>
						<th width="100">Transfer Out</th>
						<th width="100">Transfer In</th>
						<?
						if($type=="from")
						{ 
							?>
							<th width="100">Knitted</th>
							<th>Remaining</th>
							<?
						}
						else
						{
							?>
							<th width="100">Shortage</th>
							<th>Knitted</th>
							<?	
						}
						?>
						
					</thead>
					<?
					$req_qty=return_field_value("sum(b.grey_fab_qnty) as grey_req_qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and a.item_category in(2,13) and b.po_break_down_id=$txt_order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","grey_req_qnty");
					
					$sql="select 
					sum(CASE WHEN entry_form ='3' THEN quantity ELSE 0 END) AS issue_qnty,
					sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
					sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty,
					sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
					sum(CASE WHEN trans_id<>0 and entry_form in(2,22) THEN quantity ELSE 0 END) AS knit_qnty
					from order_wise_pro_details where po_breakdown_id=$txt_order_id and status_active=1 and is_deleted=0";
					$dataArray=sql_select($sql);
					$remaining=0; $shoratge=0;
					?>
					<tr bgcolor="#EFEFEF">
						<td>1</td>
						<td align="right"><? echo number_format($req_qty,2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($dataArray[0][csf('issue_qnty')],2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
						<td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
						<td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?></td>
						<?
						if($type=="from")
						{
							$remaining=$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]-$dataArray[0][csf('transfer_out_qnty')]+$dataArray[0][csf('transfer_in_qnty')]-$dataArray[0][csf('knit_qnty')];
							?>
							<td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($remaining,2); ?>&nbsp;</td>
							<?
						}
						else
						{
							$shoratge=$req_qty-$dataArray[0][csf('issue_qnty')]+$dataArray[0][csf('return_qnty')]+$dataArray[0][csf('transfer_out_qnty')]-$dataArray[0][csf('transfer_in_qnty')];
							?>
							<td align="right"><? echo number_format($shoratge,2); ?>&nbsp;</td>
							<td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
							<?	
						}
						?>
					</tr>
				</table>
				<table>
					<tr>
						<td align="center" >
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="parent.emailwindow.hide();" style="width:100px" />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
	</div>    
</body>           
</html>
<?
exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$job_no=str_replace("'","",$txt_from_job);
	$store_id=str_replace("'","",$cbo_store_name);
	$cbo_floor_to=str_replace("'","",$cbo_floor_to);
	$cbo_room_to=str_replace("'","",$cbo_room_to);
	$txt_rack_to=str_replace("'","",$txt_rack_to);
	$txt_shelf_to=str_replace("'","",$txt_shelf_to);
	$cbo_bin_to=str_replace("'","",$cbo_bin_to);
	$prod_id=str_replace("'","",$cbo_item_desc);
	$hidden_product_id=str_replace("'","",$cbo_item_desc);
	if(str_replace("'","",$cbo_transfer_criteria)!=1)
	{
		$cbo_company_id_to=str_replace("'","",$cbo_company_id);
	}
	
        //----------------Check Last Receive Date for Transfer Out----------------
	$is_update_cond_for_iss = ($operation==1)? " and id <> $update_trans_recv_id ": "";
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5) $is_update_cond_for_iss and status_active = 1", "max_date");      
	if($max_recv_date !="")
	{
		$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
		$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

		if ($transfer_date < $max_recv_date) 
		{
			echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
			die;
		}
	}
        //-----------------Check Last issue date for Transfer In-----------------
	$is_update_cond_for_rcv = ($operation==1)? " and id not in (".str_replace("'","",$update_trans_recv_id).",".str_replace("'","",$update_trans_issue_id).")": "";
	$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc $is_update_cond_for_rcv and status_active = 1", "max_date");      
	if($max_issue_date != "")
	{
		$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
		$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
		if ($transfer_date < $max_issue_date) 
		{
			echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
                //check_table_status($_SESSION['menu_id'], 0);
			disconnect($con);
			die;
		}
	} 
	
	if($job_no!="")
	{
		$allo_sql="select sum(b.alocated_qty) as alocated_qty from ppl_cut_lay_mst a, ppl_cut_lay_prod_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=253 and a.job_no = '$job_no' and a.store_id=$store_id and b.prod_id='$prod_id'";
		$allo_sql_result=sql_select($allo_sql);
		$allo_qnty=$allo_sql_result[0][csf("alocated_qty")]*1;
		/*echo "10**select sum(CASE WHEN transaction_type=1 THEN cons_quantity ELSE 0 END) AS rcv_qnty_yarn, 
		sum(CASE WHEN transaction_type=2 THEN cons_quantity ELSE 0 END) AS iss_qnty_yarn,
		sum(CASE WHEN transaction_type=2 and receive_basis=6 THEN cons_quantity ELSE 0 END) AS iss_qnty_ag_allocation,  
		sum(CASE WHEN transaction_type=3 THEN cons_quantity ELSE 0 END) AS rcv_return_qnty, 
		sum(CASE WHEN transaction_type=4 THEN cons_quantity ELSE 0 END) AS issue_return_qnty, 
		sum(CASE WHEN transaction_type=5 THEN cons_quantity ELSE 0 END) AS transfer_in_qnty, 
		sum(CASE WHEN transaction_type=6 THEN cons_quantity ELSE 0 END) AS transfer_out_qnty
		from inv_transaction where status_active=1 and is_deleted=0 and job_no='$job_no' and prod_id='$prod_id' and store_id=$store_id and entry_form in(248,249,277,381,382) $is_update_cond_for_rcv";die;*/
		$dataArray=sql_select("select sum(CASE WHEN transaction_type=1 THEN cons_quantity ELSE 0 END) AS rcv_qnty_yarn, 
		sum(CASE WHEN transaction_type=2 THEN cons_quantity ELSE 0 END) AS iss_qnty_yarn,
		sum(CASE WHEN transaction_type=2 and receive_basis=6 THEN cons_quantity ELSE 0 END) AS iss_qnty_ag_allocation,  
		sum(CASE WHEN transaction_type=3 THEN cons_quantity ELSE 0 END) AS rcv_return_qnty, 
		sum(CASE WHEN transaction_type=4 THEN cons_quantity ELSE 0 END) AS issue_return_qnty, 
		sum(CASE WHEN transaction_type=5 THEN cons_quantity ELSE 0 END) AS transfer_in_qnty, 
		sum(CASE WHEN transaction_type=6 THEN cons_quantity ELSE 0 END) AS transfer_out_qnty
		from inv_transaction where status_active=1 and is_deleted=0 and job_no='$job_no' and prod_id='$prod_id' and store_id=$store_id and entry_form in(248,249,277,381,382) $is_update_cond_for_rcv");
		//echo "20**";print_r($dataArray);die;
		//$tot_issued_qty=$dataArray[0][csf('iss_qnty_yarn')]+$dataArray[0][csf('rcv_return_qnty')]+$dataArray[0][csf('transfer_out_qnty')];
		//$tot_rcv_qty=$dataArray[0][csf('rcv_qnty_yarn')]+$dataArray[0][csf('issue_return_qnty')]+$dataArray[0][csf('transfer_in_qnty')];
		$tot_issued_qty=$dataArray[0][csf('iss_qnty_yarn')]+$dataArray[0][csf('rcv_return_qnty')]+$dataArray[0][csf('transfer_out_qnty')];
		$tot_rcv_qty=$dataArray[0][csf('rcv_qnty_yarn')]+$dataArray[0][csf('issue_return_qnty')]+$dataArray[0][csf('transfer_in_qnty')];
		$job_issue_qnty=$dataArray[0][csf('iss_qnty_ag_allocation')];
		$cu_allo_qnty=$allo_qnty-$job_issue_qnty;
		$stock_qnty=$tot_rcv_qty-$tot_issued_qty;
		$transferable_qnty=$stock_qnty-$cu_allo_qnty;
		$trans_qnty=str_replace("'","",$txt_transfer_qnty);
		//echo "20**Transfer Quantity Not Allow Over This Job Stock Or Available Qnty $trans_qnty=$transferable_qnty=$cu_allo_qnty";die;
		if($trans_qnty>$transferable_qnty)
		{
			echo "20**Transfer Quantity Not Allow Over This Job Stock Or Available Qnty $trans_qnty = $transferable_qnty";
			disconnect($con);
			die;
		}
	}
	
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$transfer_recv_num=''; $transfer_update_id='';
		
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			//$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'YOTOTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and transfer_criteria=4 and item_category='1' and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));

			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ","inv_item_transfer_mst",$con,1,$cbo_company_id,'YSTSTE',249,date("Y",time()),1 ));

			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, from_store_id, to_store_id, from_order_id, to_order_id, item_category, remarks, inserted_by, insert_date,entry_form";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$cbo_store_name.",".$cbo_store_name_to.",".$txt_from_job_id.",".$txt_to_job_id.",".$cbo_item_category.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',249)";
			
			//echo "5**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_store_id*from_order_id*to_order_id*remarks*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_store_name."*".$txt_from_job_id."*".$txt_to_job_id."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;*/ 
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		
		$field_array_trans="id, mst_id, company_id, prod_id, store_id, supplier_id, floor_id, room, rack, self, bin_box, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, inserted_by, insert_date, job_no, buyer_id, style_ref_no, entry_form";
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, from_store, to_store, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date";
		$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, available_qnty from product_details_master where id=$cbo_item_desc");
		$supplier_id=$data_prod[0][csf('supplier_id')];
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			$presentAvaillableQty=$data_prod[0][csf('available_qnty')]-str_replace("'","",$txt_transfer_qnty);
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*available_qnty*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$presentAvaillableQty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";			
						
			
			$item_lot=str_replace("'","",$item_lot);
			$product_name_dtls=str_replace("'","",$product_name_dtls);
			$row_prod=sql_select("select id, current_stock, stock_value, avg_rate_per_unit, available_qnty, dyed_type from product_details_master where company_id=$cbo_company_id_to and item_category_id=1 and supplier_id='$supplier_id' and product_name_details='$product_name_dtls' and lot='$item_lot' and status_active=1 and is_deleted=0");
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
				
			}
			
			
			$rate=return_field_value("avg_rate_per_unit","product_details_master","id=$cbo_item_desc");
			$amount=str_replace("'","",$txt_transfer_qnty)*$rate;
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			
			
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_store_name.",'".$supplier_id."',".$cbo_item_category.",6,".$txt_transfer_date.",".$txt_from_job_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_job.",".$cbo_from_buyer_name.",".$txt_from_style_ref.",249)";
			
			//$id_trans_iss=$id_trans+1;
			$id_trans_iss = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.=",(".$id_trans_iss.",".$transfer_update_id.",".$cbo_company_id_to.",".$product_id.",".$cbo_store_name_to.",'".$supplier_id."',".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$txt_to_job_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_to_job.",".$cbo_to_buyer_name.",".$txt_to_style_ref.",249)";
			
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$cbo_item_desc.",".$product_id.",".$cbo_store_name.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		else
		{
			$rate=return_field_value("avg_rate_per_unit","product_details_master","id=$cbo_item_desc");
			$amount=str_replace("'","",$txt_transfer_qnty)*$rate;
			
			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			
			
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_store_name.",'".$supplier_id."',".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_item_category.",6,".$txt_transfer_date.",".$txt_from_job_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_job.",".$cbo_from_buyer_name.",".$txt_from_style_ref.",249)";
			
			//$id_trans_iss=$id_trans+1;
			$id_trans_iss = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans.=",(".$id_trans_iss.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_store_name_to.",'".$supplier_id."',".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",5,".$txt_transfer_date.",".$txt_to_job_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_to_job.",".$cbo_to_buyer_name.",".$txt_to_style_ref.",249)";
			
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);			
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$cbo_item_desc.",".$cbo_item_desc.",".$cbo_store_name.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		

		
		$rID=$rID2=$rID3=$prodUpdate=$prod=true;
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
		// echo "5** $rID ";die;
		// echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		// echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$cbo_item_desc,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 
			//echo "5**".$sql_prod_insert;die;
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
		//echo "5**$rID=$rID2=$rID3=$prodUpdate=$prod";die;
		//echo "5** $rID = $rID2 = $rID3";die;
		
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
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		/*#### Stop not eligible field from update operation end ####*/
		//$field_array_update="challan_no*transfer_date*remarks*updated_by*update_date";
		//$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$field_array_update="challan_no*transfer_date*from_store_id*from_order_id*to_order_id*remarks*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_store_name."*".$txt_from_job_id."*".$txt_to_job_id."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$trans_sql=sql_select("select prod_id, cons_quantity from inv_transaction where id=$update_trans_issue_id");
		$previous_from_prod_id=$trans_sql[0][csf("prod_id")];
		$prev_trans_qnty=$trans_sql[0][csf("cons_quantity")];
		
		$to_trans_sql=sql_select("select prod_id, cons_quantity from inv_transaction where id=$update_trans_recv_id");
		$previous_to_prod_id=$to_trans_sql[0][csf("prod_id")];
		$prev_to_trans_qnty=$to_trans_sql[0][csf("cons_quantity")];
		$cbo_item_desc=str_replace("'","",$cbo_item_desc);
		$all_prod_id=$cbo_item_desc.",".$previous_from_prod_id.",".$previous_to_prod_id;
		//echo "10**select id, current_stock, avg_rate_per_unit, supplier_id, available_qnty from product_details_master where id in ($all_prod_id)";die;
		$prodData=sql_select("select id, current_stock, avg_rate_per_unit, supplier_id, available_qnty, supplier_id from product_details_master where id in ($all_prod_id)");
		foreach($prodData as $row)
		{
			$prod_arr[$row[csf('id')]]['st']=$row[csf('current_stock')];
			$prod_arr[$row[csf('id')]]['rate']=$row[csf('avg_rate_per_unit')];
			$prod_arr[$row[csf('id')]]['sid']=$row[csf('supplier_id')];
			$prod_arr[$row[csf('id')]]['aq']=$row[csf('available_qnty')];
			$prod_arr[$row[csf('id')]]['supplier_id']=$row[csf('supplier_id')];
		}
		
		$field_array_trans="prod_id*supplier_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*updated_by*update_date*job_no*buyer_id*style_ref_no";
		$field_array_dtls="from_prod_id*to_prod_id*from_store*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*transfer_qnty*rate*transfer_value*uom*updated_by*update_date";

		$updateTransID_array=array();
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$updateProdID_array=array();
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value*available_qnty";
			
			if($cbo_item_desc==$previous_from_prod_id)
			{
				$adjust_curr_stock_from=$prod_arr[$previous_from_prod_id]['st']+$prev_trans_qnty-str_replace("'","",$txt_transfer_qnty);
				$adjust_curr_availlable_from=$prod_arr[$previous_from_prod_id]['aq']+$prev_trans_qnty-str_replace("'","",$txt_transfer_qnty);
				$cur_st_rate_from=$prod_arr[$previous_from_prod_id]['rate'];
				$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
				$updateProdID_array[]=$previous_from_prod_id; 
				$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$adjust_curr_availlable_from));
			}
			else
			{
				$adjust_curr_stock_from=$prod_arr[$previous_from_prod_id]['st']+$prev_trans_qnty;
				$adjust_curr_availlable_from=$prod_arr[$previous_from_prod_id]['aq']+$prev_trans_qnty;
				$cur_st_rate_from=$prod_arr[$previous_from_prod_id]['rate'];
				$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
				$updateProdID_array[]=$previous_from_prod_id; 
				$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$adjust_curr_availlable_from));
				
				$presentStock=$prod_arr[$cbo_item_desc]['st']-str_replace("'","",$txt_transfer_qnty);
				$presentAvaillable=$prod_arr[$cbo_item_desc]['aq']-str_replace("'","",$txt_transfer_qnty);
				$presentAvgRate=$prod_arr[$cbo_item_desc]['rate'];
				$presentStockValue=$presentStock*$presentAvgRate;
				$updateProdID_array[]=$cbo_item_desc; 
				$data_array_adjust[$cbo_item_desc]=explode("*",("".$presentStock."*".$presentAvgRate."*".$presentStockValue."*".$presentAvaillable));
			}
			
			$supplier_id=$prod_arr[str_replace("'","",$cbo_item_desc)]['sid'];
			$item_lot=str_replace("'","",$item_lot);
			$product_name_dtls=str_replace("'","",$product_name_dtls);
			//echo "10**select id, current_stock, avg_rate_per_unit, available_qnty, dyed_type from product_details_master where company_id=$cbo_company_id_to and item_category_id=1 and supplier_id='$supplier_id' and product_name_details='$product_name_dtls' and lot='$item_lot' and status_active=1 and is_deleted=0";die;
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, available_qnty, dyed_type from product_details_master where company_id=$cbo_company_id_to and item_category_id=1 and supplier_id='$supplier_id' and product_name_details='$product_name_dtls' and lot='$item_lot' and status_active=1 and is_deleted=0");
			
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				if($product_id==$previous_to_prod_id)
				{
					$stock_qnty=$row_prod[0][csf('current_stock')];
					$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
					$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty)-$prev_to_trans_qnty;
					$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					$curr_available_qnty=$row_prod[0][csf('available_qnty')]+str_replace("'", '',$txt_transfer_qnty)-$prev_to_trans_qnty;
					
					if($curr_stock_qnty<0)
					{
						echo "30**Stock cannot be less than zero.";
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
					
					$updateProdID_array[]=$previous_to_prod_id; 
					$data_array_adjust[$previous_to_prod_id]=explode("*",("".$curr_stock_qnty."*".$avg_rate_per_unit."*".$stock_value."*".$curr_availlable_qnty));
					
					$adjust_rate_to=$prod_arr[$previous_to_prod_id]['rate'];
					$adjust_curr_stock_to=$prod_arr[$previous_to_prod_id]['st']-$prev_to_trans_qnty;
					$adjust_curr_availlable_to=$prod_arr[$previous_to_prod_id]['aq']-$prev_to_trans_qnty;
					$adjust_st_value_to=$adjust_curr_stock_to*$adjust_rate_to;
					$updateProdID_array[]=$previous_to_prod_id; 
					$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$adjust_rate_to."*".$adjust_st_value_to."*".$adjust_curr_availlable_to));
					
					if($adjust_curr_stock_to<0)
					{
						echo "30**Stock cannot be less than zero.";
						//check_table_status( $_SESSION['menu_id'],0);
						disconnect($con);
						die;
					}
				}
			}
			else
			{
				$adjust_curr_stock_to=$prod_arr[$previous_to_prod_id]['st']-$prev_to_trans_qnty;
				$adjust_curr_availlable_to=$prod_arr[$previous_to_prod_id]['aq']-$prev_to_trans_qnty;
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
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prod_id and store_id = $cbo_store_name and id not in ($update_trans_recv_id , $update_trans_issue_id ) and status_active = 1", "max_date");      
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
			
			
			
			
			$rate=return_field_value("avg_rate_per_unit","product_details_master","id=$cbo_item_desc");
			$amount=str_replace("'","",$txt_transfer_qnty)*$rate;
			$supplier_id=$prod_arr[str_replace("'","",$cbo_item_desc)]['supplier_id'];
			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*'".$supplier_id."'*".$txt_transfer_date."*".$txt_from_job_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_from_job."*".$cbo_from_buyer_name."*".$txt_from_style_ref.""));
			$supplier_id=$prod_arr[str_replace("'","",$product_id)]['supplier_id'];
			$updateTransID_array[]=$update_trans_recv_id; 
			$updateTransID_data[$update_trans_recv_id]=explode("*",("".$product_id."*'".$supplier_id."'*".$txt_transfer_date."*".$txt_to_job_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_to_job."*".$cbo_to_buyer_name."*".$txt_to_style_ref.""));
			
			$data_array_dtls=$cbo_item_desc."*".$product_id."*".$cbo_store_name."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		}
		else
		{
			
			$rate=return_field_value("avg_rate_per_unit","product_details_master","id=$cbo_item_desc");
			$amount=str_replace("'","",$txt_transfer_qnty)*$rate;
			$supplier_id=$prod_arr[str_replace("'","",$cbo_item_desc)]['supplier_id'];
			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*'".$supplier_id."'*".$txt_transfer_date."*".$txt_from_job_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_from_job."*".$cbo_from_buyer_name."*".$txt_from_style_ref.""));
			$updateTransID_array[]=$update_trans_recv_id; 
			$updateTransID_data[$update_trans_recv_id]=explode("*",("".$cbo_item_desc."*'".$supplier_id."'*".$txt_transfer_date."*".$txt_to_job_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_to_job."*".$cbo_to_buyer_name."*".$txt_to_style_ref.""));
			
			$data_array_dtls=$cbo_item_desc."*".$cbo_item_desc."*".$cbo_store_name."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		/*#### Stop not eligible field from update operation start ####*/
		// from_order_id*to_order_id*
		// $txt_from_job_id."*".$txt_to_job_id."*".
		
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		
		
		$rID=$rID2=$rID3=$prod=$prodUpdate_adjust=true;
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array);die;
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
		
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}
			
			if(count($row_prod)>0)
			{
				/*if($product_id!=$previous_to_prod_id)
				{
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
					if($flag==1) 
					{
						if($prod) $flag=1; else $flag=0; 
					}
				}*/
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}
			}
		}
		//echo "10**$rID=$rID2=$rID3=$prod=$prodUpdate_adjust";oci_rollback($con);die;
		//echo $flag;die;
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

if ($action=="yarn_order_to_order_transfer_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category, transfer_criteria, company_id, to_company, remarks from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]' and item_category=1";
	//echo $sql;die;   
	$dataArray=sql_select($sql);
	$from_com=$dataArray[0][csf('company_id')];
	$to_com=$dataArray[0][csf('to_company')];
	$transfer_criteria=$dataArray[0][csf('transfer_criteria')];
	if($transfer_criteria==6)
	{
		$sam_id=$dataArray[0][csf('to_order_id')];
	}
	elseif($transfer_criteria==7)
	{
		$sam_id=$dataArray[0][csf('from_order_id')];
	}
	else
	{
		$sam_id=0;
	}
	
	if($sam_id)
	{
		$samp_sql="select MST_ID, JOB_NO, STYLE_NO from WO_NON_ORDER_INFO_DTLS where mst_id=$sam_id and status_active=1";
		$samp_sql_result=sql_select($samp_sql);
		foreach($samp_sql_result as $row)
		{
			if($row["JOB_NO"]!="" && $job_check[$row["JOB_NO"]]=="")
			{
				$job_check[$row["JOB_NO"]]=$row["JOB_NO"];
				$samp_job.=$row["JOB_NO"].",";
			}
			if($row["STYLE_NO"]!="" && $style_check[$row["STYLE_NO"]]=="")
			{
				$job_check[$row["STYLE_NO"]]=$row["STYLE_NO"];
				$samp_style.=$row["STYLE_NO"].",";
			}
		}
		$samp_job=chop($samp_job,",");
		$samp_style=chop($samp_style,",");
	}
	
	$all_order_id=$dataArray[0][csf('from_order_id')];
	if($dataArray[0][csf('to_order_id')]) $all_order_id.=",".$dataArray[0][csf('to_order_id')];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	/*$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$job_arr = return_library_array("select id, job_no from wo_po_details_master","id","job_no");
	$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$qnty_arr = return_library_array("select id, po_quantity from wo_po_break_down","id","po_quantity");
	$buyer_arr = return_library_array("select id, buyer_name from wo_po_details_master","id","buyer_name");
	$style_arr = return_library_array("select id, style_ref_no from wo_po_details_master","id","style_ref_no");
	$ship_date_arr = return_library_array("select id, pub_shipment_date from wo_po_break_down","id","pub_shipment_date");*/
	
	$job_sql="select ID, JOB_NO, STYLE_REF_NO from wo_po_details_master where id in($all_order_id)";
	$job_sql_result=sql_select($job_sql);
	$job_data=array();
	foreach($job_sql_result as $val)
	{
		$job_data[$val["ID"]]["JOB_NO"]=$val["JOB_NO"];
		$job_data[$val["ID"]]["STYLE_REF_NO"]=$val["STYLE_REF_NO"];
	}
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=1 and company_id in($all_com)","id","product_name_details");
	?>
	<div style="width:930px;">
		<table width="900" cellspacing="0" align="right">
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
				<td width="125"><strong>Transfer ID :</strong></td><td width="175"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="125"><strong>Criteria:</strong></td><td width="175"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td width="125"><strong>To Company:</strong></td><td width="175"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
            	<td><strong>Transfer Date:</strong></td><td><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td><strong>From Style Ref.:</strong></td> <td><? if($transfer_criteria==7) echo $samp_style ;else echo $job_data[$dataArray[0][csf('from_order_id')]]["STYLE_REF_NO"]; ?></td>
				<td><strong>From Job No:</strong></td> <td><? if($transfer_criteria==7) echo $samp_job ; else echo  $job_data[$dataArray[0][csf('from_order_id')]]["JOB_NO"]; ?></td>
			</tr>
			<tr>
				<td><strong>To Style Ref.:</strong></td> <td><? if($transfer_criteria==6) echo $samp_style ; else echo $job_data[$dataArray[0][csf('to_order_id')]]["STYLE_REF_NO"]; ?></td>
				<td><strong>To Job No:</strong></td> <td><? if($transfer_criteria==6) echo $samp_job ; else echo $job_data[$dataArray[0][csf('to_order_id')]]["JOB_NO"]; ?></td>
				<td><strong>Challan No.:</strong></td><td><? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>Remarks:</strong></td><td><? echo $dataArray[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="170">From Store</th>
                    <th width="160">To Store</th>
                    <th width="80">Category</th>
					<th width="250">Item Description</th>
					<th width="70">UOM</th>
					<th>Transfered Qnty</th>
				</thead>
				<tbody> 
					
					<?
					$sql_dtls="select id, item_category, item_group, from_prod_id, transfer_qnty, uom, from_store, to_store from inv_item_transfer_dtls where mst_id='$data[1]' and item_category=1 and status_active=1 and is_deleted=0";
					
					$sql_result= sql_select($sql_dtls);
					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						$transfer_qnty=$row[csf('transfer_qnty')];
						$transfer_qnty_sum += $transfer_qnty;
						
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $store_library[$row[csf("from_store")]]; ?></td>
                            <td><? echo $store_library[$row[csf("to_store")]]; ?></td>
                            <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
							<td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
						</tr>
						<? $i++; } ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td align="right"><?php echo $transfer_qnty_sum; ?></td>
						</tr>                           
					</tfoot>
				</table>
				<br>
				<?
				echo signature_table(39, $data[0], "900px");
				?>
			</div>
		</div>   
		<?	
		exit();
	}
	?>