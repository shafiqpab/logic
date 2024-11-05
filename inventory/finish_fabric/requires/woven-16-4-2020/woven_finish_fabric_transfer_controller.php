<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");


if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/woven_finish_fabric_transfer_controller",$data);
}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'store','from_store_td', $('#cbo_company_id').val(),this.value); " );

	//if( $('#cbo_transfer_criteria').val()*1==2 || $('#cbo_transfer_criteria').val()*1==4)  load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id').val(),this.value);

	exit();
}

if ($action=="load_drop_down_location_to")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),this.value);" );
	exit();
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
<div align="center" style="width:780px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:760px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
                <thead>
                    <th width="200">Search By</th>
                    <th width="200" id="search_by_td_up">Please Enter Transfer ID</th>
                    <th width="220">Date Range</th>
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
                    <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />&nbsp;To&nbsp;
                    <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_transfer_search_list_view', 'search_div', 'woven_finish_fabric_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
                    </td>
                </tr>
                <tr>                  
                    <td align="center" height="40" valign="middle" colspan="4"><? echo load_month_buttons(1);  ?></td>
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
	$date_form=trim($data[3]);
	$date_to =trim($data[4]);
	
	if($date_form !="" && $date_to !="")
	{
		if($db_type==0) $date_cond=" and transfer_date between '".change_date_format($date_form,"YYYY-MM-DD")."' and '".change_date_format($date_to,"YYYY-MM-DD")."'"; 
		else $date_cond=" and transfer_date between '".change_date_format($date_form,"","",-1)."' and '".change_date_format($date_to,"","",-1)."'";
	}
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";
	
	if($db_type==0) $year_field="YEAR(insert_date)"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY')";
	else $year_field="";//defined Later
	
 	$sql="select id, $year_field as year, transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category,location_id,to_location_id from inv_item_transfer_mst where item_category=3 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria in(1,2,4) and status_active=1 and is_deleted=0 $date_cond order by id desc";
	//echo $sql;die;
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "70,60,100,120,90,140","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, location_id, transfer_date, transfer_criteria, item_category, to_company, to_location_id from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "active_inactive(".$row[csf("transfer_criteria")].");\n";
		
		echo "load_drop_down('requires/woven_finish_fabric_transfer_controller','".$row[csf("company_id")]."', 'load_drop_down_location', 'from_location_td' );\n";
		echo "load_drop_down('requires/woven_finish_fabric_transfer_controller','".$row[csf("to_company")]."', 'load_drop_down_location_to', 'to_location_td' );\n";
		
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'store','from_store_td', $('#cbo_company_id').val(),'"."',$('#cbo_location').val());\n";

		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_location_to').value 				= '".$row[csf("to_location_id")]."';\n";

		echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_store_name_to', 'store','to_store_td', $('#cbo_company_id_to').val(),'"."',$('#cbo_location_to').val());\n";

		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_location').attr('disabled','disabled');\n";
		
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
		
	}
}



if ($action=="itemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			$('#product_id').val(data);
			parent.emailwindow.hide();
		}
    </script>

</head>

<body>
<div align="center">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:910px;margin-left:10px">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="900" class="rpt_table" border="1" rules="all" align="center">
                <thead>
                	<th width="150">Order</th>
                    <th width="150">Booking</th>
                    <th width="150">Batch No.</th>
                    <th width="150">Buyer</th>
                    <th width="180" id="search_by_td_up">Item Details</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
                        <input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                	<td>
                        <input type="text" style="width:120px;" class="text_boxes"  name="txt_order_no" id="txt_order_no" value="<? if($transfer_criteria==4) echo $txt_from_order_no; ?>" <? if($transfer_criteria==4) echo "disabled"; else echo ""; ?> />
                        <input type="hidden"  name="txt_order_id" id="txt_order_id" value="<? if($transfer_criteria==4) echo $txt_from_order_id; ?>" />
                    </td>
                     <td>
                        <input type="text" style="width:120px;" class="text_boxes"  name="txt_batch_no" id="txt_batch_no" />
                    </td>
                    <td>
                        <input type="text" style="width:120px;" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />
                    </td>
                   
                    <td>
                        <?
                        echo create_drop_down( "cbo_buyer_name", 140, "select a.id, a.buyer_name from lib_buyer a, lib_buyer_party_type b, lib_buyer_tag_company c where a.id=b.buyer_id and a.id=c.buyer_id and b.party_type=1 and c.tag_company='$cbo_company_id' and a.status_active=1 and a.is_deleted =0 order by a.buyer_name","id,buyer_name", 1, "-- Select --", $selected, "" ); 
						?>
                    </td>
                    
                    <td>
                        <input type="text" style="width:150px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_batch_no').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_floor; ?>+'_'+<? echo $cbo_room; ?>+'_'+<? echo $txt_rack; ?>+'_'+<? echo $txt_shelf; ?>+'_'+document.getElementById('txt_order_id').value, 'create_product_search_list_view', 'search_div', 'woven_finish_fabric_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	
	$txt_order_no 	= str_replace("'","",$data[0]);
	$txt_batch_no 	= str_replace("'","",$data[1]);
	$txt_booking_no = str_replace("'","",$data[2]);
	$cbo_buyer_name = str_replace("'","",$data[3]);
	$search_string	= trim(str_replace("'","",$data[4]));
	$company_id 	= str_replace("'","",$data[5]);
	$cbo_store_name	= str_replace("'","",$data[6]);
	$cbo_floor		= str_replace("'","",$data[7]);
	$cbo_room		= str_replace("'","",$data[8]);
	$cbo_rack		= str_replace("'","",$data[9]);
	$txt_shelf		= str_replace("'","",$data[10]);
	$order_id		= trim(str_replace("'","",$data[11]));
	
	//$ord_cond="";
	//if($order_id!="") $ord_cond=" and id=$order_id";
	//$orderNumber=return_library_array( "select id from wo_po_break_down where po_number='$txt_order_no' and status_active=1 and is_deleted = 0 $ord_cond", "id", "id"  );
	$product_ids="";;
	if($db_type==0) $select_prod_field=" group_concat(id) as id"; else $select_prod_field="listagg(cast(id as varchar(4000)),',') within group(order by id) as id";
	if($search_string!="") $product_ids=return_field_value("$select_prod_field","product_details_master","product_name_details ='$search_string'","id");
	
	$sql_cond="";
	if($order_id!="") 			$sql_cond =" and c.po_breakdown_id =$order_id";
	if($product_ids!="") 		$sql_cond .=" and b.prod_id in($product_ids)";
	if($txt_order_no != "") 	$sql_cond .=" and d.po_number = '$txt_order_no'";
	if($txt_booking_no != "") 	$sql_cond .=" and a.booking_no = '$txt_booking_no'";
	if($txt_batch_no != "") 	$sql_cond .=" and a.batch_no = '$txt_batch_no'";
	if($cbo_buyer_name > 0) 	$sql_cond .=" and e.buyer_name ='$cbo_buyer_name'";	
	if($cbo_floor  > 0) 		$sql_cond .=" and b.floor_id ='$cbo_floor'";
	if($cbo_room  > 0) 			$sql_cond .=" and b.room ='$cbo_room'";
	if($cbo_rack  > 0) 			$sql_cond .=" and b.rack ='$cbo_rack'";
	if($txt_shelf  > 0) 		$sql_cond .=" and b.self ='$txt_shelf'";

	if($db_type == 0){
		$select_batch = " b.pi_wo_batch_no";
	}else{
		$select_batch = " cast(b.pi_wo_batch_no as varchar(4000))";
	}
	$sql = "SELECT b.company_id, b.batch_id as batch_id, e.buyer_name as buyer_id, c.quantity, c.po_breakdown_id as order_id, c.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id as booking_id, a.booking_no, d.po_number FROM pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e WHERE a.id=b.batch_id and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$company_id and b.store_id = '$cbo_store_name' $sql_cond
		union all 
		SELECT b.company_id, $select_batch as batch_id, e.buyer_name as buyer_id, c.quantity, c.po_breakdown_id as order_id, c.prod_id, b.store_id, b.floor_id, b.room, b.rack, b.self, a.batch_no, a.booking_no_id as booking_id, a.booking_no, d.po_number 
		from pro_batch_create_mst a, inv_transaction b, order_wise_pro_details c, wo_po_break_down d, wo_po_details_master e 
		where a.id=b.pi_wo_batch_no and b.id = c.trans_id and c.po_breakdown_id=d.id and d.job_no_mst=e.job_no and b.item_category=3 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$company_id and b.store_id = '$cbo_store_name' $sql_cond ";

	//echo $sql;die;

	$result = sql_select($sql);

	if(empty($result))
	{
		echo "Data Not Found";
		die;
	}
	$order_data_array=array();
	foreach($result as $row )
	{
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['item_details']=$row[csf('product_name_details')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['color']=$row[csf('color')];
		
		//$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]]['stock_qty']		+= $row[csf('quantity')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['company_id']=$row[csf('company_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['buyer_id']=$row[csf('buyer_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['booking_id']=$row[csf('booking_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['booking_no']=$row[csf('booking_no')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['booking_no_batch']=$row[csf('booking_no')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['batch_id']=$row[csf('batch_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['batch_no']=$row[csf('batch_no')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['store_id']=$row[csf('store_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['floor_id']=$row[csf('floor_id')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['room']=$row[csf('room')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['rack']=$row[csf('rack')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['self']=$row[csf('self')];
		$order_data_array[$row[csf('order_id')]][$row[csf('prod_id')]][$row[csf('batch_id')]]['po_number']=$row[csf('po_number')];
		
		$orderidArr[$row[csf('order_id')]]=$row[csf('order_id')];
		$prodidArr[$row[csf('prod_id')]]=$row[csf('prod_id')];
		$batchidArr[$row[csf('batch_id')]]=$row[csf('batch_id')];
	}

	/*echo "<pre>";
	print_r($order_data_array);
	die;*/

	//$all_scanned_barcode_no = chop($new_barcode_nos,",");

	$batchidArr = array_filter($batchidArr);
	$all_batch_nos = "'".implode("','", $batchidArr)."'";
	$all_batch_id_cond=""; $batchCond=""; 
	$all_batch_id_cond2=""; $batchCond2=""; 
	if($db_type==2 && count($batchidArr)>999)
	{
		$all_batchidArr_chunk=array_chunk($batchidArr,999) ;
		foreach($all_batchidArr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);	
			$batchCond.="  a.batch_id in($chunk_arr_value) or ";
			$batchCond2.="  a.pi_wo_batch_no in($chunk_arr_value) or ";
		}
		$all_batch_id_cond.=" and (".chop($batchCond,'or ').")";
		$all_batch_id_cond2.=" and (".chop($batchCond2,'or ').")";
	}
	else
	{
		$all_batch_id_cond=" and a.batch_id in($all_batch_nos)";	 
		$all_batch_id_cond2=" and a.pi_wo_batch_no in($all_batch_nos)";	 
	}

	$sql_stock1=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.batch_id,
	sum(case when a.transaction_type in(1) and b.trans_type in(1) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(2) and b.trans_type in(2) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in(17,19) and a.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3  and a.company_id=$company_id and b.po_breakdown_id in(".implode(",",$orderidArr).") and a.prod_id in(".implode(",",$prodidArr).") $all_batch_id_cond
	group by b.po_breakdown_id, a.prod_id, a.batch_id");


	foreach($sql_stock1 as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['issue_qty'] +=$value[csf('issue_qty')];
	}

	$sql_stock2=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,
	sum(case when a.transaction_type in(4,5) and b.trans_type in(4,5) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(3,6) and b.trans_type in(3,6) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in(202,209,258) and a.store_id = '$cbo_store_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3  and a.company_id=$company_id and b.po_breakdown_id in(".implode(",",$orderidArr).") and a.prod_id in(".implode(",",$prodidArr).") $all_batch_id_cond2
	group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no");


	foreach($sql_stock2 as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['issue_qty'] +=$value[csf('issue_qty')];
	}
	
	$prod_sql=sql_select("select id, product_name_details, color from product_details_master where id in(".implode(",",$prodidArr).")");
	$prod_data=array();
	foreach($prod_sql as $row)
	{
		$prod_data[$row[csf("id")]]["product_name_details"]=$row[csf("product_name_details")];
		$prod_data[$row[csf("id")]]["color"]=$row[csf("color")];
	}
	
	?>
    <div style="width:1000px;" align="center">  
    <table cellspacing="0" width="100%" class="rpt_table" id="" rules="all" align="center">
        <thead>
            <tr>
                <th width="35">SL</th>
                <th width="80">Order</th>
                <th width="80">Item ID</th>
                <th width="120">Company</th>
                <th width="120">Buyer</th>
                <th width="200">Item Details</th>
                <th width="90">Color</th>
                <th width="90">Booking</th>
                <th width="90">Batch</th>
                <th>Stock</th>
            </tr>
        </thead>
    </table>
    </div>
    <div style="width:1000px; overflow-y:scroll; max-height:250px;" align="center">  
    <table cellspacing="0" width="980" class="rpt_table" id="tbl_list_search" rules="all" align="center"  style="margin-bottom: 5px; word-break:break-all">
        <tbody>
            <? 
            $i=1;
            foreach($order_data_array as $order_id => $order_data_array)
            {
                foreach($order_data_array as $item_ids => $item_val)
                {
					foreach($item_val as $batch_ids => $row)
					{
						$stock = $stock_data_arrray[$order_id][$item_ids][$batch_ids]['rcv_qty'] - $stock_data_arrray[$order_id][$item_ids][$batch_ids]['issue_qty'];
						if($stock>0)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";		
							?>
							<tr  bgcolor="<? echo $bgcolor; ?>" valign="middle"  style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $order_id."_".$item_ids."_".$row['company_id']."_".$row['store_id']."_".$row['floor_id']."_".$row['room']."_".$row['rack']."_".$row['self']."_".$batch_ids; ?>')" >
								<td width="35" align="center"><? echo $i; ?></td>
								<td width="80" title="<? echo $order_id; ?>"><? echo $row['po_number']; ?></td>
								<td width="80"><? echo $item_ids; ?></td>
								<td width="120"><? echo $company_arr[$row['company_id']]; ?></td>
								<td width="120"><? echo $buyer_library[$row['buyer_id']]; ?></td>
								<td width="200"><? echo $prod_data[$item_ids]["product_name_details"];?></td>
								<td width="90"><? echo $color_arr[$prod_data[$item_ids]["color"]]; ?></td>
								<td width="90"><? echo $row['booking_no_batch']; ?></td>
								<td width="90"><? echo $row['batch_no']; ?></td>
								<td><? echo $stock; ?></td>
							</tr>
							<? 
							$i++;
						}
					}
                }
            }
            ?>
        </tbody>
    </table>
    </div>
	<?
	exit();
}


if($action=='populate_data_from_product_master')
{
	$data = explode("_",$data);
	$transfer_criteria=$data[9];
	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in($data[0])",'id','po_number');

	$sql_stock=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.batch_id,
	sum(case when a.transaction_type in(1) and b.trans_type in(1) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(2) and b.trans_type in(2) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (17,19) and a.store_id = $data[3] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3  and a.company_id=$data[2] and b.po_breakdown_id=$data[0] and a.prod_id=$data[1] and a.batch_id=$data[8]
	group by b.po_breakdown_id, a.prod_id, a.batch_id");

	foreach($sql_stock as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['issue_qty'] +=$value[csf('issue_qty')];
	}

	$sql_stock2=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,
	sum(case when a.transaction_type in(4,5) and b.trans_type in(4,5) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(3,6) and b.trans_type in(3,6) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (202,209,258) and a.store_id = $data[3] and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.company_id=$data[2] and b.po_breakdown_id=$data[0] and a.prod_id=$data[1] and a.pi_wo_batch_no=$data[8]
	group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no");

	foreach($sql_stock2 as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['issue_qty'] +=$value[csf('issue_qty')];
	}

	if($db_type == 0){
		$select_batch = " b.pi_wo_batch_no ";
	}else{
		$select_batch = " cast(b.pi_wo_batch_no as varchar(4000)) ";
	}

	$sql ="select  a.company_id, b.batch_id, c.quantity, c.po_breakdown_id as order_id, a.id as prod_id, a.product_name_details, a.unit_of_measure, a.color, a.current_stock , a.avg_rate_per_unit, b.store_id, b.floor_id, b.room, b.rack, b.self
	from product_details_master a, inv_transaction b, order_wise_pro_details c
	where a.id=b.prod_id and b.id = c.trans_id and b.item_category=3 and c.entry_form in(17) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.batch_id>0 and b.company_id=$data[2] and b.store_id = $data[3] and c.po_breakdown_id=$data[0] and b.prod_id=$data[1] and b.batch_id=$data[8]
	union all
	select  a.company_id, $select_batch as batch_id, c.quantity, c.po_breakdown_id as order_id, a.id as prod_id, a.product_name_details, a.unit_of_measure, a.color, a.current_stock , a.avg_rate_per_unit, b.store_id, b.floor_id, b.room, b.rack, b.self
	from product_details_master a, inv_transaction b, order_wise_pro_details c
	where a.id=b.prod_id and b.id = c.trans_id and b.item_category=3 and c.entry_form in(258) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and b.pi_wo_batch_no>0 and b.company_id=$data[2] and b.store_id = $data[3] and c.po_breakdown_id=$data[0] and b.prod_id=$data[1] and b.pi_wo_batch_no=$data[8]";

	$data_array=sql_select($sql);
	foreach ($data_array as $row)
	{ 
		if($row[csf("floor_id")]) $floor_id=$row[csf("floor_id")]; else $floor_id=0;
		if($row[csf("room")]) $room=$row[csf("room")]; else  $room=0;
		if($row[csf("rack")]) $rack=$row[csf("rack")]; else  $rack=0;
		if($row[csf("self")]) $self=$row[csf("self")]; else  $self=0;
		 
		$stockQty = $stock_data_arrray[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("batch_id")]]['rcv_qty'] - $stock_data_arrray[$row[csf("order_id")]][$row[csf("prod_id")]][$row[csf("batch_id")]]['issue_qty'];
		echo "document.getElementById('txt_from_order_id').value 			= '".$row[csf("order_id")]."';\n";
		echo "document.getElementById('txt_from_order_no').value 			= '".$order_name_arr[$row[csf("order_id")]]."';\n";
		if($transfer_criteria==2)
		{
			echo "document.getElementById('txt_to_order_id').value 			= '".$row[csf("order_id")]."';\n";
			echo "document.getElementById('txt_to_order_no').value 			= '".$order_name_arr[$row[csf("order_id")]]."';\n";
		}
		echo "document.getElementById('from_product_id').value 				= '".$row[csf("prod_id")]."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$stockQty."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stockQty."';\n";
		echo "document.getElementById('batch_id').value 					= '".$row[csf("batch_id")]."';\n";
		//echo "document.getElementById('txt_current_stock').value 			= '".$row[csf("current_stock")]."';\n";
		//echo "document.getElementById('hidden_current_stock').value 		= '".$row[csf("current_stock")]."';\n";
		//echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		echo "document.getElementById('hide_color_id').value 				= '".$row[csf("color")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color")]]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("unit_of_measure")]."';\n";
		
		if($floor_id !=0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'floor','floor_td', '".$data[2]."','"."','".$data[3]."',this.value);\n";
			echo "document.getElementById('cbo_floor').value 				= '".$floor_id."';\n";
		}
		if($room !=0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'room','room_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."',this.value);\n";
			echo "document.getElementById('cbo_room').value 				= '".$room."';\n";
		}
		if($rack !=0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'rack','rack_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."',this.value);\n";
			echo "document.getElementById('txt_rack').value 				= '".$rack."';\n";
		}
		if($self !=0)
		{
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'shelf','shelf_td', '".$data[2]."','"."','".$data[3]."','".$floor_id."','".$room."','".$rack."',this.value);\n";
			echo "document.getElementById('txt_shelf').value 				= '".$self."';\n";
		}
	}
	exit();
}


if ($action=="po_search_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST); 
	?> 
	<script> 
		
		function fn_show_check()
		{
			if( $('#txt_po_no').val()=='' && $('#txt_job_no').val()=='' && $('#cbo_buyer_name').val()*1==0 && $('#txt_internal_ref').val()==''){
				alert("Please Enter at Least One Search");return;
			}
			show_list_view ( $('#txt_po_no').val()+'_'+$('#txt_job_no').val()+'_'+<? echo $cbo_company_id_to; ?>+'_'+$('#cbo_buyer_name').val()+'_'+$('#txt_internal_ref').val(), 'create_po_search_list_view', 'search_div', 'woven_finish_fabric_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
		}

		function js_set_value( id,name) 
		{
			$('#hidden_order_id').val(id);
			$('#hidden_order_no').val(name);
			parent.emailwindow.hide();
		}
		
		function hidden_field_reset()
		{
			$('#hidden_order_id').val('');
			$('#hidden_order_no').val( '' );
		}
		
	</script>

	</head>
	<body>
		<div align="center">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:620px;margin-left:5px">
				<input type="hidden" name="hidden_order_id" id="hidden_order_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_order_no" id="hidden_order_no" class="text_boxes" value="">
				<table cellpadding="0" cellspacing="0" width="620" class="rpt_table" border="1" rules="all">
					<thead>
						<th>Buyer</th>
						<th>PO No</th>
                        <th>Job No</th>
                        <th>Internal Ref. No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="po_id" id="po_id" value="">
						</th> 
					</thead>
					<tr class="general">
						<td align="center">
							<?
							echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id_to' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,30,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyer_name, "","" ); 
							?>       
						</td>
                        <td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_po_no" id="txt_po_no" placeholder="Write" />	
						</td>
                        <td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_job_no" id="txt_job_no"  placeholder="Write"/>	
						</td>
                        <td align="center">				
							<input type="text" style="width:130px" class="text_boxes"  name="txt_internal_ref" id="txt_internal_ref"  placeholder="Write"/>	
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check()" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"  align="center"></div>	
			</fieldset>
		</form>
        </div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}






if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);
	$txt_po_no = trim($data[0]);
	$txt_job_no = trim($data[1]);
	$company_id =$data[2];
	$buyer_id =$data[3];
	$txt_internal_ref =$data[4];
	
	$search_con="";
	if($buyer_id!=0)
		$search_con = " and a.buyer_name=$buyer_id";

	if($txt_po_no!="")
		$search_con .= " and b.po_number like '%$txt_po_no%'";
	if($txt_job_no!="")
		$search_con .=" and a.job_no like '%$txt_job_no%'";
	if($txt_internal_ref!="")
		$search_con .=" and b.grouping like '%$txt_internal_ref%'"; 

	$sql = "select a.job_no, a.style_ref_no, a.buyer_name, a.order_uom, b.id,b.grouping as ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date 
	from wo_po_details_master a, wo_po_break_down b 
	where a.job_no=b.job_no_mst and a.company_name=$company_id $search_con and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.id desc";
	?>
	
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" >
			<thead>
				<th width="40">SL</th>
				<th width="150">Job No</th>
				<th width="160">Style No</th>
				<th width="160">PO No</th>
				<th width="100">Ref. No</th>
				<th width="">UOM</th>
			</thead>
		</table>
		<div style="width:720px; overflow-y:scroll; max-height:220px;" id="buyer_list_view" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="700" class="rpt_table" id="tbl_list_search" >
				<?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)  
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					if(in_array($selectResult[csf('id')],$hidden_po_id)) 
					{ 
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value('<? echo $selectResult[csf('id')];?>','<? echo $selectResult[csf('po_number')];?>')"> 
						<td width="40" align="center"><?php echo "$i"; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>	
						<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $selectResult[csf('po_number')]; ?>"/>
						<input type="hidden" name="txt_buyer" id="txt_buyer<?php echo $i ?>" value="<? echo $selectResult[csf('buyer_name')]; ?>"/>
						<input type="hidden" name="txt_styleRef" id="txt_styleRef<?php echo $i ?>" value="<? echo $selectResult[csf('style_ref_no')]; ?>"/>	
					</td>	
					<td width="150"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
					<td width="160"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
					<td width="160"><p><? echo $selectResult[csf('po_number')]; ?></p></td>
					<td width="100"><p><? echo $selectResult[csf('ref_no')]; ?></p></td>
					<td width="" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
			<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<?php echo $po_row_id; ?>"/>
		</table>
	</div>
	<?
	exit();
}





if($action=="show_transfer_listview")
{
	$product_name_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=3","id","product_name_details");
	
	$sql="select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_name_arr,4=>$color_arr);
	 
	echo  create_list_view("list_view", "From Store,To Store,Item Description,Transfered Qnty,Color", "130,130,280,130,110","880","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0,color_id", $arr, "from_store,to_store,from_prod_id,transfer_qnty,color_id", "requires/woven_finish_fabric_transfer_controller",'','0,0,0,2,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	$data_array=sql_select("SELECT a.transfer_criteria, a.company_id, a.to_company, a.location_id, a.to_location_id, b.from_order_id, b.to_order_id, b.id, b.mst_id, b.from_store, b.to_store, b.floor_id, b.room, b.rack, b.shelf, b.to_floor_id, b.to_room, b.to_rack, b.to_shelf, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate, b.transfer_value, b.color_id, b.batch_id,b.remarks, b.to_trans_id, b.trans_id,b.uom from inv_item_transfer_mst a, inv_item_transfer_dtls b where b.id='$data' and a.id=b.mst_id");
	
	$order_no=explode(",",trim($data_array[0][csf('from_order_id')].",".$data_array[0][csf('to_order_id')],"  , "));
	
	$order_name_arr=return_library_array( "select id ,po_number from wo_po_break_down where status_active=1 and is_deleted =0 and id in(".implode(",",$order_no).")",'id','po_number');
	/*$sql_stock=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,
	sum(case when a.transaction_type in(1,4,5) and b.trans_type in(1,4,5) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(2,3,6) and b.trans_type in(2,3,6) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id  and b.entry_form in(17,19,202,209,258) and a.store_id = ".$data_array[0][csf('from_store')]."
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
	and a.item_category=3  and a.company_id=".$data_array[0][csf('company_id')]."
	group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no");

	foreach($sql_stock as $value){
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['rcv_qty']=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['issue_qty']=$value[csf('issue_qty')];
	}*/


	//==============================================

	$sql_stock1=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.batch_id,
	sum(case when a.transaction_type in(1) and b.trans_type in(1) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(2) and b.trans_type in(2) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (17,19) and a.store_id = ".$data_array[0][csf('from_store')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3  and a.company_id=".$data_array[0][csf('company_id')]."
	group by b.po_breakdown_id, a.prod_id, a.batch_id");

	foreach($sql_stock1 as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('batch_id')]]['issue_qty'] +=$value[csf('issue_qty')];
	}

	$sql_stock2=sql_select("select b.po_breakdown_id as order_id, a.prod_id, a.pi_wo_batch_no,
	sum(case when a.transaction_type in(4,5) and b.trans_type in(4,5) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(3,6) and b.trans_type in(3,6) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (202,209,258) and a.store_id = ".$data_array[0][csf('from_store')]." and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.company_id=".$data_array[0][csf('company_id')]."
	group by b.po_breakdown_id, a.prod_id, a.pi_wo_batch_no");

	foreach($sql_stock2 as $value)
	{
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['rcv_qty'] +=$value[csf('rcv_qty')];
		$stock_data_arrray[$value[csf('order_id')]][$value[csf('prod_id')]][$value[csf('pi_wo_batch_no')]]['issue_qty'] +=$value[csf('issue_qty')];
	}




	//==============================================

	
	/*echo "<pre>";
	print_r($stock_data_arrray); die;*/
	
	foreach ($data_array as $row)
	{ 
		if ($row[csf("transfer_criteria")]==1) {
			$company_id=$row[csf("to_company")];
		}
		else
		{
			$company_id=$row[csf("company_id")];
		}
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		
		
		
		//if($row[csf("from_store")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'store','from_store_td', $('#cbo_company_id').val(),'"."',this.value);\n";
			echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		//}



		//if($row[csf("floor_id")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'floor','floor_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."',this.value);\n";
			echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";			
		//}


		//if($row[csf("room")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'room','room_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		//}


		//if($row[csf("rack")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'rack','rack_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
			echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		//}


		//if($row[csf("shelf")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3', 'shelf','shelf_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";
		//}

		
		//if($row[csf("to_store")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_store_name_to', 'store','to_store_td', '".$company_id."','"."',this.value);\n";
			echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
		//}
		//if($row[csf("to_floor_id")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_floor_to', 'floor','floor_td_to', '".$company_id."','"."','".$row[csf('to_store')]."',this.value);\n";
			echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		//}
		//if($row[csf("to_room")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*cbo_room_to', 'room','room_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
			echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		//}
		//if($row[csf("to_rack")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*txt_rack_to', 'rack','rack_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
			echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		//}
		//if($row[csf("to_shelf")]>0){
			echo "load_room_rack_self_bin('requires/woven_finish_fabric_transfer_controller*3*txt_shelf_to', 'shelf','shelf_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";
			echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		//}

		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hide_color_id').value 				= '".$row[csf("color_id")]."';\n";
		echo "document.getElementById('txt_color').value 					= '".$color_arr[$row[csf("color_id")]]."';\n";
		
		echo "document.getElementById('batch_id').value 					= '".$row[csf("batch_id")]."';\n";
		echo "document.getElementById('txt_from_order_no').value 			= '".$order_name_arr[$row[csf("from_order_id")]]."';\n";
		echo "document.getElementById('txt_from_order_id').value 			= '".$row[csf("from_order_id")]."';\n";
		echo "document.getElementById('from_product_id').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '".$row[csf("from_prod_id")]."';\n";
		
		echo "document.getElementById('txt_to_order_no').value 				= '".$order_name_arr[$row[csf("to_order_id")]]."';\n";
		echo "document.getElementById('txt_to_order_id').value 				= '".$row[csf("to_order_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '".$row[csf("to_prod_id")]."';\n";
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		
		$sql=sql_select("select product_name_details,current_stock,avg_rate_per_unit from product_details_master where id=".$row[csf('from_prod_id')]);

		$stockQty = ($stock_data_arrray[$row[csf("from_order_id")]][$row[csf("from_prod_id")]][$row[csf("batch_id")]]['rcv_qty'] - $stock_data_arrray[$row[csf("from_order_id")]][$row[csf("from_prod_id")]][$row[csf("batch_id")]]['issue_qty'])+$row[csf("transfer_qnty")];
		
		
		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$stockQty."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stockQty."';\n";

		echo "document.getElementById('update_trans_issue_id').value 		= '".$row[csf("trans_id")]."';\n"; 
		echo "document.getElementById('update_trans_recv_id').value 		= '".$row[csf("to_trans_id")]."';\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if($db_type==0)	{ mysql_query("BEGIN"); }
	if(str_replace("'","",$cbo_transfer_criteria)==2 || str_replace("'","",$cbo_transfer_criteria)==4)
	{
		$company_id_to=str_replace("'","",$cbo_company_id);
		$cbo_location_to=str_replace("'","",$cbo_location);
	}
	else
	{
		$company_id_to=str_replace("'","",$cbo_company_id_to);
		$cbo_location_to=str_replace("'","",$cbo_location_to);
	}
	
	if(str_replace("'","",$update_id)!="")
	{
		$up_cond="";
		if(str_replace("'","",$update_trans_issue_id)!="")
		{
			$all_trans_id=str_replace("'","",$update_trans_issue_id);
			$up_cond=" and id not in($all_trans_id)";
		}
		
		$duplicate_product_check=return_field_value("id", "inv_transaction", " status_active=1 and transaction_type in(6) and prod_id=$from_product_id and mst_id=$update_id $up_cond", "id");
		if($duplicate_product_check)
		{
			echo "20**Duplicate Item Not Allow Within Same MRR and prod_id=$from_product_id and mst_id=$update_id $up_cond";disconnect($con);die;
		}
	}
	
	$up_trans_id=str_replace("'","",$update_trans_issue_id);
	if(str_replace("'","",$update_trans_recv_id)!="") $up_trans_id.=",".str_replace("'","",$update_trans_recv_id);
	$up_cond="";
	if(str_replace("'","",$update_trans_issue_id)!="") $up_cond=" and a.id not in($up_trans_id)";

	$sql_stock1=sql_select("select sum(case when a.transaction_type in(1) and b.trans_type in(1) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(2) and b.trans_type in(2) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (17,19) and a.store_id = $cbo_store_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3  and a.company_id=$cbo_company_id and b.po_breakdown_id=$txt_from_order_id and a.prod_id=$from_product_id and a.batch_id=$batch_id $up_cond");

	$rcv_qty=$issue_qty = 0;
	foreach($sql_stock1 as $value)
	{
		$rcv_qty +=$value[csf('rcv_qty')];
		$issue_qty +=$value[csf('issue_qty')];
	}

	$sql_stock2=sql_select("select sum(case when a.transaction_type in(4,5) and b.trans_type in(4,5) then b.quantity end) as rcv_qty,
	sum(case when a.transaction_type in(3,6) and b.trans_type in(3,6) then b.quantity end) as issue_qty  
	from inv_transaction a, order_wise_pro_details b
	where a.id=b.trans_id and b.entry_form in (202,209,258) and a.store_id = $cbo_store_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.item_category=3 and a.company_id=$cbo_company_id and b.po_breakdown_id=$txt_from_order_id and a.prod_id=$from_product_id and a.pi_wo_batch_no=$batch_id $up_cond");

	foreach($sql_stock2 as $value)
	{
		$rcv_qty +=$value[csf('rcv_qty')];
		$issue_qty +=$value[csf('issue_qty')];
	}
	$trans_qnty=str_replace("'","",$txt_transfer_qnty)*1;
	$stock_qnty=$rcv_qty - $issue_qty;

	if($trans_qnty>$stock_qnty)
	{
		echo "20**Transfer Quantity Not Allow More Than Balance Quantity\nBalance Quantity : $stock_qnty";disconnect($con);die;
	}

	//echo "10**fail=$stock_qnty";die;

	
	//echo "10**".str_replace("'","",$item_desc_w_space)."|==";die;
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		$transfer_recv_num=''; $transfer_update_id='';
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1;
		}
		//echo "10**".$variable_auto_rcv; die;

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,"WFTE",258,date("Y",time()),$cbo_item_category ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, location_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company,to_location_id, from_order_id, to_order_id, item_category, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$cbo_location.",".$txt_challan_no.",".$txt_transfer_date.",258,".$cbo_transfer_criteria.",".$company_id_to.",".$cbo_location_to.",".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			//echo "10**fail=1";die;
			$field_array_update="challan_no*transfer_date*to_company*to_location_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$company_id_to."*".$cbo_location_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);

			if($variable_auto_rcv != 1)
			{
				//echo "10**fail=2";die;
				$pre_saved_store=sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form = 258 and a.id = $transfer_update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");

				if( ($pre_saved_store[0][csf("from_store")]  !=str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  !=str_replace("'", "", $cbo_store_name_to)) )
				{
					echo "20**Duplicate From Store and To Store is not allowed in same MRR";
					disconnect($con);
					die;
				}
			}

		}

		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, company_id, prod_id, pi_wo_batch_no, item_category, transaction_type, transaction_date, store_id, floor_id, room, rack, self, order_id, brand_id, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id, trans_id, to_trans_id,remarks";

		$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, batch_id, yarn_lot, brand_id, color_id, item_group, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date, from_order_id, to_order_id";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1) //Company to Company Transfer
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, detarmination_id, weight, dia_width from product_details_master where id=$from_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate="'".$presentAvgRate."'*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$supplier_id=$data_prod[0][csf('supplier_id')];// and supplier_id='$supplier_id'
			$product_id=0;	
			
			if ($data_prod[0][csf('dia_width')]=="") 
			{
				if($db_type == 0){
					$dia_cond = " and dia_width = '' ";
				}else{
					$dia_cond = " and dia_width is null ";
				}
			}
			else
			{
				$dia_cond = " and dia_width = '".$data_prod[0][csf('dia_width')]."'";
			}

			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and item_category_id=$cbo_item_category and detarmination_id=".$data_prod[0][csf('detarmination_id')]." $dia_cond and weight = ".$data_prod[0][csf('weight')]." and color=$hide_color_id and unit_of_measure = ".$cbo_uom." and status_active=1 and is_deleted=0");

			//echo "10**".count($row_prod)."nnn";//$item_desc_w_space
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				if($variable_auto_rcv==1)
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update=$avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
			}
			else
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);

				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				if($variable_auto_rcv==1)
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date from product_details_master where id=$from_product_id";
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date from product_details_master where id=$from_product_id";
				}
			}
			
			//----------------Check Last Receive Date for Transfer Out----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
					disconnect($con);
					die;
				}
			}

			//----------------Check Last Issue Date for Transfer In----------------
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					disconnect($con);
					die;
				}
			}

			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_from_order_id.",0,".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//$recv_trans_id=$id_trans+1;
			$recv_trans_id=0;
			if($variable_auto_rcv==1)
			{
				$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$company_id_to.",".$product_id.",".$batch_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$txt_to_order_id.",0,".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			//.",".$txt_from_order_id.",".$txt_to_order_id
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.")";
			
			if($variable_auto_rcv==2)
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$from_product_id.",".$product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.")";
			}
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop="(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$txt_from_order_id.",".$from_product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			if($variable_auto_rcv==1)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$txt_to_order_id.",".$product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
		}
		else //Store To Store and order to order Transfer
		{
			//if(str_replace("'","",$cbo_transfer_criteria)==2) $txt_to_order_id = $txt_from_order_id;
			//----------------Check Last Receive Date for Transfer Out----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
					disconnect($con);
					die;
				}
			}
			
			 //----------------Check Last Issue Date for Transfer In----------------
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Item";
					disconnect($con);
					die;
				}
			}

			$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_from_order_id.",0,".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";			
			
			//$id_trans=$id_trans+1;
			//$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			$recv_trans_id=0;
			if($variable_auto_rcv==1)
			{
				$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_company_id.",".$from_product_id.",".$batch_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$txt_from_order_id.",0,".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
			
			$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.",".$id_trans.",".$recv_trans_id.",".$txt_remarks.")";
			
			if($variable_auto_rcv==2)
			{//echo "10**nazim"; die;
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$from_product_id.",".$from_product_id.",".$batch_id.",0,0,".$hide_color_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_from_order_id.",".$txt_to_order_id.")";
			}

			//$recv_trans_id=$id_trans;
			
			
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop="(".$id_prop.",".$id_trans.",6,258,".$id_dtls.",".$txt_from_order_id.",".$from_product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			if($variable_auto_rcv==1)
			{
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$recv_trans_id.",5,258,".$id_dtls.",".$txt_to_order_id.",".$from_product_id.",".$hide_color_id.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}
		}
		
		if(str_replace("'","",$update_id)=="")
		{
			//echo "10**insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
		
		//echo "10**";die;
		$rID=$prod=$rID2=$rID3=$rID4=$rID5=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			//echo "10**".$data_array_prodUpdate;die;
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$from_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} 
			
			if(count($row_prod)>0)
			{
				if($variable_auto_rcv==1)
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
				//echo "10**".$sql_prod_insert;die;
				$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				} 
			}
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
		
		//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1); 
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}
		//echo "10**".$variable_auto_rcv; die;
		if($variable_auto_rcv==2)
		{
			//echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac;die;
			$rID5=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			}
		}
		
		//echo "10**".$rID."**".$prod."**".$rID2."**".$rID3."**".$rID4."**".$flag."**".$variable_auto_rcv."**".$rID5; die;
		// 10**1**1**1**1**1**1**2**
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
		
		disconnect($con);
		die;
				
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id and variable_list= 27", "auto_transfer_rcv");

		if($variable_auto_rcv == "")
		{
			$variable_auto_rcv = 1;
		}

		$field_array_update="challan_no*transfer_date*to_company*to_location_id*to_order_id*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$company_id_to."*".$cbo_location_to."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		
		$field_array_trans="prod_id*pi_wo_batch_no*transaction_date*store_id*floor_id*room*rack*self*order_id*brand_id*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		
		$field_array_dtls="from_prod_id*to_prod_id*batch_id*color_id*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*transfer_qnty*rate*transfer_value*uom*updated_by*update_date*from_order_id*to_order_id*remarks";
		
		$field_array_proportionate_update="po_breakdown_id*prod_id*color_id*quantity*updated_by*update_date";
		$prod=true;


		if($variable_auto_rcv != 1)
		{
			$pre_saved_store=sql_select("select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form = 258 and a.id = $update_id and b.id != $update_dtls_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");
			if(!empty($pre_saved_store))
			{
				if( ($pre_saved_store[0][csf("from_store")]  !=str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  !=str_replace("'", "", $cbo_store_name_to)) )
				{
					echo "20**Duplicate From Store and To Store is not allowed in same MRR"."select a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form = 258 and a.id = $update_id and b.id != $update_dtls_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store";
					disconnect($con);
					die;
				}
			}
		}

		//echo "10**fail";die;

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$updateProdID_array=array();
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
			
			$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id");
			
			$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty)-str_replace("'","",$txt_transfer_qnty);
			$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];
			
			$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
			
			$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
			$updateProdID_array[]=$previous_from_prod_id; 
			
			$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from."*".$user_id."*'".$pc_date_time."'"));
			
			$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id");
			$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty)+str_replace("'","",$txt_transfer_qnty);
			$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
			$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;
			
			$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
			$updateProdID_array[]=$previous_to_prod_id; 
			
			$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to."*".$user_id."*'".$pc_date_time."'"));
			
			//echo "10**".bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array);die;
			
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, detarmination_id,dia_width,weight from product_details_master where id=$from_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate="'".$presentAvgRate."'*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			

			$supplier_id=$data_prod[0][csf('supplier_id')];// and supplier_id='$supplier_id'

			if ($data_prod[0][csf('dia_width')]=="") 
			{
				if($db_type == 0){
					$dia_cond = " and dia_width = '' ";
				}else{
					$dia_cond = " and dia_width is null ";
				}
			}
			else
			{
				$dia_cond = " and dia_width = '".$data_prod[0][csf('dia_width')]."'";
			}

			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit from product_details_master where company_id=$company_id_to and item_category_id=$cbo_item_category and detarmination_id=".$data_prod[0][csf('detarmination_id')]." $dia_cond and weight = ".$data_prod[0][csf('weight')]." and unit_of_measure = ".$cbo_uom." and color=$hide_color_id and status_active=1 and is_deleted=0");			

			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				if($variable_auto_rcv == 1 )
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update="'".$avg_rate_per_unit."'*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}		
			}
			else
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				
				if($variable_auto_rcv == 1 )
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date) 
				select	
				'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date from product_details_master where id=$from_product_id";
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date) 
				select	
				'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, weight, inserted_by, insert_date from product_details_master where id=$from_product_id";
				}
			}
			
			//echo "10**".$product_id;die;
			
			//----------------Check Last Receive Date for Transfer Out----------------
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$from_product_id and store_id = $cbo_store_name and id <> $update_trans_recv_id  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Lot";
					disconnect($con);
					die;
				}
			}
			
			//----------------Check Last Issue Date for Transfer In----------------
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to and id not in ($update_trans_recv_id , $update_trans_issue_id )  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = date("Y-m-d", strtotime($max_issue_date));
				$transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					disconnect($con);
					die;
				}
			}
			//,".$batch_id."
			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$from_product_id."*".$batch_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_from_order_id."*0*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			if($variable_auto_rcv == 1 )
			{
				$updateTransID_array[]=$update_trans_recv_id; 				
				$updateTransID_data[$update_trans_recv_id]=explode("*",("'".$product_id."'*".$batch_id."*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_to_order_id."*0*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$txt_transfer_qnty."*".$txt_transfer_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
			$data_array_dtls=$from_product_id."*".$product_id."*".$batch_id."*".$hide_color_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_from_order_id."*".$txt_to_order_id."*".$txt_remarks."";
			//echo "10**string";
			
			
			$data_array_prop_update[$update_trans_issue_id]=explode("*",("".$txt_from_order_id."*".$from_product_id."*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			if($variable_auto_rcv == 1 )
			{
				$data_array_prop_update[$update_trans_recv_id]=explode("*",("".$txt_to_order_id."*'".$product_id."'*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		else
		{
			//$txt_to_order_id=$txt_from_order_id;

			$updateTransID_array[]=$update_trans_issue_id; 
			
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$from_product_id."*".$batch_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_from_order_id."*0*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			if($variable_auto_rcv == 1 )
			{
				$updateTransID_array[]=$update_trans_recv_id; 
				
				$updateTransID_data[$update_trans_recv_id]=explode("*",("".$from_product_id."*".$batch_id."*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_from_order_id."*0*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*0*0*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
			$data_array_dtls=$from_product_id."*".$from_product_id."*".$batch_id."*".$hide_color_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_from_order_id."*".$txt_to_order_id."*".$txt_remarks."";
			
			$data_array_prop_update[$update_trans_issue_id]=explode("*",("".$txt_from_order_id."*".$from_product_id."*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			if($variable_auto_rcv == 1 )
			{
				$data_array_prop_update[$update_trans_recv_id]=explode("*",("".$txt_to_order_id."*".$from_product_id."*".$hide_color_id."*".$txt_transfer_qnty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
		}
		
		$prod=$prodUpdate_adjust=$prodUpdate=$rID=$rID2=$rID3=$rID4=$rID5=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			if(count($row_prod)>0)
			{
				if($data_array_prod_update!="")
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
			
			
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$from_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			}
		}
		
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
		//echo "10**".bulk_update_sql_statement("order_wise_pro_details","trans_id",$field_array_proportionate_update,$data_array_prop_update,$updateTransID_array);die;
		$rID4=execute_query(bulk_update_sql_statement("order_wise_pro_details","trans_id",$field_array_proportionate_update,$data_array_prop_update,$updateTransID_array));
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}

		if($variable_auto_rcv==2)
		{
			$rID5=sql_update("inv_item_transfer_dtls_ac",$field_array_dtls,$data_array_dtls,"dtls_id",$update_dtls_id,1);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			}
		}
		
		//echo "10**".$rID."**".$prodUpdate_adjust."**".$prodUpdate."**".$prod."**".$rID2."**".$rID3."**".$rID4."**".$rID5;die;
		
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
		disconnect($con);
		die;
 	}
}


if($action=="finish_fabric_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	// print_r ($data);die;
	
	$sql="SELECT id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id, item_category,location_id,to_location_id from inv_item_transfer_mst where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name");
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name");	
	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=3");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}	
?>
<div style="width:930px;">
    <table width="900" cellspacing="0" align="right">
        <tr>
            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "SELECT plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
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
    </table>
        <br>
    <div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="40">SL</th>
            <th width="250">From Referance</th>
            <th width="250">To Referance</th>
            <th width="250">Item Description</th>
            <th width="50">UOM</th>
            <th width="100">Transfered Qnty</th>
            <th width="110">Color</th> 
        </thead>
        <tbody> 
		<?
        //$sql_dtls="SELECT id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
        $sql_dtls="SELECT b.id, b.from_store, b.to_store, b.from_prod_id, b.transfer_qnty, b.color_id,a.from_order_id,a.to_order_id from inv_item_transfer_mst a , inv_item_transfer_dtls b where a.id=b.mst_id and a.id='$data[1]' and b.status_active=1 and a.is_deleted=0";
        // echo $sql_dtls;die();
        $sql_result= sql_select($sql_dtls);
        $po_id_array=array();
        foreach ($sql_result as $row) 
        {
        
        	$po_id_array[]=$row[csf('from_order_id')].",".$row[csf('to_order_id')];
        }
        $poIds = implode(",",array_unique($po_id_array));
        $buyer_arr=return_library_array( "SELECT a.id ,b.buyer_name from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in($poIds)", "id", "buyer_name" );
        $style_arr=return_library_array( "SELECT a.id ,b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in($poIds)", "id", "style_ref_no" );
        $po_no_arr=return_library_array( "SELECT id, po_number from  wo_po_break_down where id in($poIds)", "id", "po_number");
         //print_r($style_arr);die();
        // echo $poIds;die();

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
                <td>Store:<? echo $store_library[$row[csf("from_store")]]; ?><br>Style:<? echo $style_arr[$row[csf("from_order_id")]]; ?><br>Buyer:<? echo $buyer_library[$buyer_arr[$row[csf("from_order_id")]]]; ?><br>Order No:<? echo $po_no_arr[$row[csf("from_order_id")]]; ?></td>
                <td>Store:<? echo $store_library[$row[csf("to_store")]]; ?><br>Style:<? echo $style_arr[$row[csf("to_order_id")]]; ?><br>Buyer:<? echo $buyer_library[$buyer_arr[$row[csf("to_order_id")]]]; ?><br>Order No:<? echo $po_no_arr[$row[csf("to_order_id")]]; ?></td>                
                <td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$uom_arr[$row[csf("from_prod_id")]]]; ?></td>
                <td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
                <td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
            </tr>
            <? 
            $i++; 
        } 
        ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $transfer_qnty_sum; ?></td>
                <td align="right"><?php //echo $req_qny_edit_sum; ?></td>
            </tr>                           
        </tfoot>
        </table>
        <br>
         <?
            echo signature_table(23, $data[0], "900px");
         ?>
        </div>
    </div>   
	<?	
    exit();
}

if($action=="finish_fabric_transfer_print_2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$batch_arr = return_library_array("select id, batch_no from pro_batch_create_mst where is_deleted=0 and status_active=1","id","batch_no");
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$location_library=return_library_array( "select id, location_name from lib_location", "id", "location_name");
	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");
	
	$prod_sql=sql_select("select id, product_name_details, unit_of_measure from product_details_master where item_category_id=3");
	foreach($prod_sql as $row)
	{
		$product_arr[$row[csf("id")]]=$row[csf("product_name_details")];
		$uom_arr[$row[csf("id")]]=$row[csf("unit_of_measure")];
	}

	$sql="select a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria, b.batch_id, a.item_category, a.location_id, a.to_company, a.from_store_id, a.to_store_id, b.from_order_id, b.to_order_id, a.item_category, a.location_id, b.from_store, b.to_store, b.from_prod_id, sum(b.transfer_qnty) as transfer_qnty, b.uom,b.remarks  
	from inv_item_transfer_mst a,inv_item_transfer_dtls b 
	where a.id=b.mst_id and a.id='$data[1]' and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 
	group by a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria,b.batch_id, a.item_category, a.location_id, a.to_company, a.from_store_id, a.to_store_id, b.from_order_id, b.to_order_id, a.item_category, a.location_id, b.from_store, b.to_store, b.from_prod_id, b.uom,b.remarks  
	order by b.from_prod_id";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$orderID="";$prodID="";
	foreach ($dataArray as $row) {
		$orderidArr[$row[csf('from_order_id')]]=$row[csf('from_order_id')];
		$orderID.=$row[csf('from_order_id')].',';
  		$prodID.=$row[csf('from_prod_id')].',';
		
	}
	$orderID=chop($orderID,",");
	$prodID=chop($prodID,",");
	//echo "select a.id ,a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in(".implode(",",$orderidArr).")";die;
	$sql_order= sql_select("select a.id ,a.po_number, b.style_ref_no from wo_po_break_down a,wo_po_details_master b  where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted =0 and a.id in(".implode(",",$orderidArr).")");
	foreach ($sql_order as  $row) {
		$orderArr[$row[csf('id')]]['po_number']=$row[csf('po_number')];
		$orderArr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
	}

	$sql_dtls ="select   a.company_id, a.supplier_id,  b.buyer_id, c.po_breakdown_id as order_id, d.id as prod_id, d.product_name_details, d.color , f.booking_no as booking_no_batch
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c, product_details_master d , inv_transaction e, pro_batch_create_mst f
	where a.id=b.mst_id and b.id = c.dtls_id and b.prod_id=d.id  and c.trans_id=e.id and b.batch_id=f.id and a.item_category=2  and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id is not null and c.po_breakdown_id in($orderID) and d.id in($prodID) group by a.company_id, a.supplier_id, b.buyer_id, c.po_breakdown_id, d.id, d.product_name_details, d.color , f.booking_no order by d.id";
	//echo $sql_dtls;
	$sql_result= sql_select($sql_dtls);

	$tarnsferArry=array();
	foreach($sql_result as $row)
	{
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['buyer_id']=$row[csf('buyer_id')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['booking_no_batch']=$row[csf('booking_no_batch')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['color']=$row[csf('color')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['product_name_details']=$row[csf('product_name_details')];
		$tarnsferArry[$row[csf('order_id')]][$row[csf('prod_id')]]['batch_id']=$batch_arr[$row[csf('batch_id')]];
	}
	$tableWidth = 1300;
?>
<div style="width:<? echo $tableWidth; ?>px;">
    <table width="<? echo $tableWidth; ?>" cellspacing="0" align="right">
        <tr>
        	<td align="left"><img src="../../<? echo $image_location; ?>" height="70" width="180"></td>
            <td  align="left" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
            <td></td>
        </tr>
        <tr class="form_caption">
        	<td colspan="6" align="center" style="font-size:14px">  
				<?
					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]"); 
					foreach ($nameArray as $result)
					{ 
					?>
						Plot No: <? echo $result[csf('plot_no')]; ?> 
						Level No: <? echo $result[csf('level_no')]?>
						Road No: <? echo $result[csf('road_no')]; ?> 
						Block No: <? echo $result[csf('block_no')];?> 
						City No: <? echo $result[csf('city')];?> <br>
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Province No: <?php echo $result[csf('province')];?> 
						Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
                ?> 

            </td>  
        </tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr><td></td></tr>
        <tr>
            <td colspan="6" align="center" style="font-size:x-large;"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
        </tr>
    </table>
    <table width="<? echo $tableWidth; ?>" cellspacing="0" align="right" style="margin-top: 30px;">
        <tr>
        	<td width="130"><strong>Transfer Criteria</strong></td> <td width="175px"><strong>:<? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></strong></td>
        	<td><strong>Print Date and Time</strong></td><td width="175px"><strong>:<? echo date("d/m/Y") ." : ". date("h:i:sa"); ?></strong></td>
        </tr>
        <tr style="height: 20px;">
        </tr>

        <tr>
        	<td width="125"><strong>From</strong></td><td width="175px">: <? echo $store_library[$dataArray[0][csf('from_store')]]; ?></td>
        	<td width="125"><strong>To Company</strong></td><td width="175px">: <? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
        </tr>
        <tr>
        	<td width="120"><strong>Transfer ID</strong></td><td width="175px"><strong>: <? echo $dataArray[0][csf('transfer_system_id')]; ?></strong></td>
        	<td width="125"><strong>To Store</strong></td><td width="175px">: <? echo $store_library[$dataArray[0][csf('to_store')]]; ?></td>
        </tr>
        <tr>
        	<td><strong>Transfer Date</strong></td><td width="175px">: <? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
        </tr>
        <tr>
        <td><strong>Challan No</strong></td><td width="175px">: <? echo $dataArray[0][csf('challan_no')]; ?></td>
        	</tr>	
        <tr>
			<td><strong>Item Category</strong></td> <td width="175px">: <? echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
        </tr>
    </table>
        <br>
    <div  style="width:<? echo $tableWidth; ?>px;">
    <table align="right" cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="100">Buyer</th>
            <th width="180">Order</th>
            <th width="100">Style</th>
            <th width="160">Fab. Booking</th>
            <th width="100">Batch</th>
            <th width="100">Fab. Color</th> 
            <th width="350">Item Description</th> 
            <th width="100">Fab. WT</th> 
            <th width="50">UOM</th> 
            <th>Remarks</th> 
        </thead>
        <tbody> 
   
<?
  	
	//$sql_dtls="select id, from_store, to_store, from_prod_id, transfer_qnty, color_id from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";

	
	$i=1;
	foreach($dataArray as $row)
	{
		if ($i%2==0)  
			$bgcolor="#E9F3FF";
		else
			$bgcolor="#FFFFFF";
			
		?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td width="30" align="center"><? echo $i; ?></td>
				<td align="center" width="100"><? echo $buyer_library[$tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['buyer_id']]; ?></td>
				<td align="center" width="100" title="<? echo $order_id; ?>"><? echo $orderArr[$row[csf('from_order_id')]]['po_number'] ; ?></td>
				<td align="center" width="100"><? echo $orderArr[$row[csf('from_order_id')]]['style_ref_no']; ?></td>
				<td align="center" width="180"><? echo $tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['booking_no_batch']; ?></td>
				<td align="center" width="100"><? echo $batch_arr[$row[csf('batch_id')]]; ?></td>
				<td align="center" width="100"><? echo $color_arr[$tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['color']]; ?></td>
				<td align="center" width="250"><? echo $tarnsferArry[$row[csf('from_order_id')]][$row[csf('from_prod_id')]]['product_name_details']; ?></td>
				<td align="right" width="100"><? echo $total_trnf=$row[csf('transfer_qnty')];  ?></td>
				<td align="center" width="50"><? echo $unit_of_measurement[$row[csf('uom')]]; ?></td>
				<td align="center"><? echo $row[csf('remarks')]; ?></td>
			</tr>
			<?
			$total_transfer_qty+=$total_trnf;
			 $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $total_transfer_qty; ?></td>
                <td align="right"><?php //echo $req_qny_edit_sum; ?></td>
                <td align="right"></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(23, $data[0], "1100px");
         ?>
      </div>
   </div>   
 <?	
 exit();
}



function sql_update_a($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{
	
	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);	
	
	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}
	
	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";
	
	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);	
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}
	echo $strQuery;die;
	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd) 
		return "1";
	else 
		return "0";
	
	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con); 
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}
?>