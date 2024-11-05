<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//========== user credential start ========
$user_id = $_SESSION['logic_erp']['user_id'];
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id='$user_id'");
$company_id = $userCredential[0][csf('company_id')];
$store_location_id = $userCredential[0][csf('store_location_id')];

if ($company_id !='') {
    $company_credential_cond = "and comp.id in($company_id)";
}
if ($store_location_id !='') {
    $store_location_credential_cond = "and a.id in($store_location_id)"; 
}
//========== user credential end ==========

// ===========================================================

$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");

/*if ($action=="load_drop_down_store")
{

	echo create_drop_down( "cbo_store_name", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond and b.category_type in (".implode(",",array_flip($item_category)).") group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	   exit();
}

if ($action=="load_drop_down_store_to")
{
	//echo "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39) group by a.id,a.store_name order by a.store_name";die;
	echo create_drop_down( "cbo_store_name_to", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond and b.category_type in (".implode(",",array_flip($item_category)).") group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	   exit();
}*/
if ($action=="load_room_rack_self_bin")
	{
		load_room_rack_self_bin("requires/raw_material_item_transfer_controller",$data);
	}
if ($action=="itemDescription_popup")
{
	echo load_html_head_contents("Item Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_item_category=str_replace("'","",$cbo_item_category);
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
                   <th>Item Group</th>
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
					  if($cbo_item_category!=0) $item_category_cond=" and item_category='$cbo_item_category'"; else  $item_category_cond="";
                      echo create_drop_down( "cbo_item_group_id", 130,"select id,item_name  from lib_item_group where   status_active=1 $item_category_cond","id,item_name", 1, "-- Select --", "", "","","","","","");
					?>
                    </td>
                    <td>
						<?
							$search_by_arr=array(1=>"Item Details",2=>"Product Id.");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_item_group_id').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_item_category; ?>+'_'+<? echo $cbo_store_name; ?>, 'create_product_search_list_view', 'search_div', 'raw_material_item_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$search_by=trim($data[1]);
	$item_group_id=trim($data[2]);
	$company_id =trim($data[3]);
	$item_category_id =trim($data[4]);
	$from_store_id =trim($data[5]);
	$str_cond="";
	if($item_group_id>0) $str_cond.=" and a.item_group_id=$item_group_id ";
	if($item_category_id>0) $str_cond.=" and a.item_category_id=$item_category_id and b.item_category=$item_category_id";
	if($from_store_id>0) $str_cond.=" and b.store_id=$from_store_id ";
	
	/*$item_group_cond="";
	if($data[2]!=0) $item_group_cond=" and item_group_id=$data[2] ";
	$item_category_cond="";
	if($data[4]!=0) $item_category_cond=" and  	item_category_id=$data[4] ";*/
	
	if($search_by==1) $search_field=" a.product_name_details";	 
	else if($search_by==2)  $search_field=" a.id";
	$entry_form_cond="";
	if($item_category_id==4) $entry_form_cond=" and a.entry_form=20";
 	//$sql="select id, company_id, supplier_id, product_name_details, lot,item_group_id, current_stock, brand, unit_of_measure from product_details_master where item_category_id in (4,8,9,10,11,15,16,17,18,19,20,21,22) and company_id=$company_id and $search_field like '$search_string' and current_stock>0 and status_active=1 and is_deleted=0 $item_group_cond  $item_category_cond";
	
	$sql="select a.id, a.company_id, a.supplier_id, a.item_description,a.item_size, a.lot, a.item_group_id, a.current_stock as item_global_stock, a.brand, a.unit_of_measure,
	sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock
	from product_details_master a, inv_transaction b 
	where a.id=b.prod_id  and a.company_id=$company_id and $search_field like '$search_string' and a.current_stock>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $entry_form_cond  $str_cond 
	group by a.id, a.company_id, a.supplier_id, a.item_description, a.item_size, a.lot, a.item_group_id, a.current_stock, a.brand, a.unit_of_measure";
	
	//echo $sql;

	$item_group_arr=return_library_array( "select id,item_name  from lib_item_group where   status_active=1 ",'id','item_name');
	$arr=array(1=>$company_arr,2=>$supplier_arr,3=>$item_group_arr,7=>$unit_of_measurement);

	echo  create_list_view("tbl_list_search", "Item ID,Company,Supplier,Item Group,Item Details,Item Size,Lot No,UOM,Stock", "60,120,120,100,160,60,60,60","900","250",0, $sql, "js_set_value", "id", "", 1, "0,company_id,supplier_id,item_group_id,0,0,0,unit_of_measure,0", $arr, "id,company_id,supplier_id,item_group_id,item_description,item_size,lot,unit_of_measure,current_stock", '','','0,0,0,0,0,0,0,0,2');
	
	exit();
}

if($action=='populate_data_from_product_master')
{
	
	
	$data_ref=explode("**",$data);
	$prod_id=$data_ref[0];
	$from_store_id=$data_ref[1];
	$data_array=sql_select("select a.product_name_details, a.lot, a.current_stock as item_global_stock, a.avg_rate_per_unit, a.brand, a.item_category_id, a.unit_of_measure, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock 
	from product_details_master a, inv_transaction b where a.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and a.id='$prod_id' and b.store_id=$from_store_id 
	group by a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, a.item_category_id, a.unit_of_measure");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('hidden_product_id').value 			= '".$prod_id."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row[csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("unit_of_measure")]."';\n";
		//

		
	}
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
            <table cellpadding="0" cellspacing="0" width="550" class="rpt_table">
                <thead>
                    <th>Search By</th>
                    <th width="240" id="search_by_td_up">Please Enter Transfer ID</th>
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+'<? echo $cbo_company_id; ?>'+'_'+'<? echo $cbo_transfer_criteria; ?>', 'create_transfer_search_list_view', 'search_div', 'raw_material_item_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$transfer_criteria =$data[3];
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else
		$search_field="challan_no";
		
	if($db_type==0) $year_field="YEAR(insert_date) as year,"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later
	
 	$sql="select id, $year_field transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria 
	from inv_item_transfer_mst where entry_form=57 and company_id=$company_id and transfer_criteria=$transfer_criteria and $search_field like '$search_string' and status_active=1 and is_deleted=0";
	//echo $sql;,6=>$item_category
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria", "70,60,100,120,90","700","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria", '','','0,0,0,0,3,0');
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, to_company from inv_item_transfer_mst where id='$data'");
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
		//echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id in (".implode(",",array_flip($item_category)).")","id","product_name_details");
	
	$sql="select id, from_store, to_store, from_prod_id, transfer_qnty from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_arr);
	 
	echo  create_list_view("list_view", "From Store,To Store,Item Description,Transfered Qnty", "150,150,250","680","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0", $arr, "from_store,to_store,from_prod_id,transfer_qnty", "requires/raw_material_item_transfer_controller",'','0,0,0,2');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	$data_array=sql_select("select  a.transfer_criteria, a.company_id,a.to_company,b.id, b.mst_id, b.from_store, b.to_store,b.floor_id,b.room,b.rack,b.shelf,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf, b.from_prod_id, b.to_prod_id, b.transfer_qnty, b.rate,b.item_category, b.transfer_value, b.yarn_lot, b.brand_id, b.uom from inv_item_transfer_mst a,inv_item_transfer_dtls b where b.id='$data' and a.id=b.mst_id");
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
		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94', 'store','from_store_td', $('#cbo_company_id').val(),'"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94', 'floor','floor_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94', 'room','room_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94', 'rack','rack_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94', 'shelf','shelf_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";

		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94*cbo_store_name_to', 'store','to_store_td', '".$company_id."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name_to').value 				= '".$row[csf("to_store")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94*cbo_floor_to', 'floor','floor_td_to', '".$company_id."','"."','".$row[csf('to_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94*cbo_room_to', 'room','room_td_to', '".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."',this.value);\n";
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94*txt_rack_to', 'rack','rack_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."',this.value);\n";
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		echo "load_room_rack_self_bin('requires/raw_material_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94*txt_shelf_to', 'shelf','shelf_td_to','".$company_id."','"."','".$row[csf('to_store')]."','".$row[csf('to_floor_id')]."','".$row[csf('to_room')]."','".$row[csf('to_rack')]."',this.value);\n";

		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";

		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".$row[csf("transfer_value")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		//echo "document.getElementById('txt_yarn_brand').value 				= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_product_id').value 			= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 		= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 			= '".$row[csf("to_prod_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		
		echo "disable_enable_fields('cbo_store_name*cbo_item_category*txt_item_desc',1);\n";
		
		$sql=sql_select("select a.product_name_details, a.current_stock as item_global_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock  from product_details_master a, inv_transaction b  where a.id=b.prod_id and a.id='".$row[csf('from_prod_id')]."' and b.store_id='".$row[csf("from_store")]."' and b.status_active=1 and b.is_deleted=0 group by a.product_name_details, a.current_stock, a.avg_rate_per_unit" );
		
		$stock=$sql[0][csf("current_stock")]+$row[csf("transfer_qnty")];
		
		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$stock."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stock."';\n";
		$prod_id=$row[csf("from_prod_id")].",".$row[csf("to_prod_id")];
		$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and item_category=".$row[csf("item_category")]." and transaction_type in(5,6) and prod_id in($prod_id) order by id asc");
        //echo "select id, transaction_type from inv_transaction where mst_id=$row[mst_id] and item_category=1 and transaction_type in(5,6) order by id asc";die;
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 

		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	        
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		//table lock here 
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$transfer_recv_num=''; $transfer_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			/*$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and transfer_criteria in(1,2) and entry_form=57 and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));
		 
			$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;*/
			
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'GTE',57,date("Y",time())));
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);

			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company,entry_form, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",57,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			//$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			//if($rID) $flag=1; else $flag=80;
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*to_company*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_company_id_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			//if($rID) $flag=1; else $flag=0; 
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id,transaction_criteria, company_id, prod_id, item_category, transaction_type, transaction_date, store_id,floor_id,room,rack,self, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, item_group, from_store,floor_id,room,rack,shelf, to_store,to_floor_id,to_room,to_rack,to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, inserted_by, insert_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$hidden_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			
			/*if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} */
			//echo "10**".$txt_item_desc;die;
			$supplier_id=$data_prod[0][csf('supplier_id')];
			
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category  and trim(product_name_details)=trim($txt_item_desc) and status_active=1 and is_deleted=0");
		
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$row_prod[0][csf('stock_value')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$curr_stock_value=$stock_value+str_replace("'", '',$txt_transfer_value);
				$curr_avg_rate_per_unit=$curr_stock_value/$curr_stock_qnty;
				
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
	
				$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				
				$data_array_prod_update=$curr_avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				
				/*$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}*/ 
			}
			else
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				
				$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, item_size, inserted_by, insert_date,entry_form) 
			select	
			'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, item_size, inserted_by, insert_date,entry_form from product_details_master where id=$hidden_product_id";
				//echo $sql_prod_insert;die;
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
            $max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");      
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



			
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//$recv_trans_id=$id_trans+1;
			$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
			
			$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$product_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17 and item_category_id=$cbo_item_category and status_active=1 and is_deleted=0");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category order by transaction_date $cond_lifofifo");
			foreach($sql as $result)
			{
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",57,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",57,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
			
			/*if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
				if($flag==1) 
				{
					if($mrrWiseIssueID) $flag=1; else $flag=0; 
				} 
			}
			
			//transaction table stock update here------------------------//
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				if($flag==1) 
				{
					if($upTrID) $flag=1; else $flag=0; 
				} 
			}	*/
 		// LIFO/FIFO END-----------------------------------------------//
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
            $max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id = $cbo_store_name_to  and status_active = 1", "max_date");      
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



			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$id_trans=$id_trans+1;
			$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_uom.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$hidden_product_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_rate.",".$txt_transfer_value.",".$cbo_uom.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		//echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		/*$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */
		
		
		//insert update all queries
		$rID=$rID2=$rID3=$prodUpdate=$prod=$mrrWiseIssueID=$upTrID=true;
		//echo "10**insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		//echo "10**insert into inv_transaction ($field_array_trans) values $data_array_trans";die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if(count($row_prod)>0)
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
			}
			if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			}
		}
		
		//end
		//echo $flag;die;
		
		//echo $upTrID;die;
		
		
		//echo "10** $rID && $rID2 && $rID3 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID";oci_rollback($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID)
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
			if($rID && $rID2 && $rID3 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID)
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
		
		//table lock here 
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}

		$field_array_update="challan_no*transfer_date*to_company*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_company_id_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; */
	
		$field_array_trans="prod_id*transaction_date*store_id*floor_id*room*rack*self*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 

		$field_array_dtls="from_prod_id*to_prod_id*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*item_category*transfer_qnty*rate*transfer_value*uom*updated_by*update_date";
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$updateProdID_array=array();
			$field_array_adjust="current_stock*avg_rate_per_unit*stock_value";
			
			$stock_from=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_from_prod_id");
			$adjust_curr_stock_from=$stock_from[0][csf('current_stock')]+str_replace("'", '',$hidden_transfer_qnty);
			$cur_st_rate_from=$stock_from[0][csf('avg_rate_per_unit')];
			$cur_st_value_from=$adjust_curr_stock_from*$cur_st_rate_from;
			
			$previous_from_prod_id=str_replace("'","",$previous_from_prod_id);
			$updateProdID_array[]=$previous_from_prod_id; 
			
			$data_array_adjust[$previous_from_prod_id]=explode("*",("".$adjust_curr_stock_from."*".$cur_st_rate_from."*".$cur_st_value_from));
			
			$stock_to=sql_select("select current_stock, avg_rate_per_unit from product_details_master where id=$previous_to_prod_id");
			$adjust_curr_stock_to=$stock_to[0][csf('current_stock')]-str_replace("'", '',$hidden_transfer_qnty);
			$cur_st_rate_to=$stock_to[0][csf('avg_rate_per_unit')];
			$cur_st_value_to=$adjust_curr_stock_to*$cur_st_rate_to;
			
			$previous_to_prod_id=str_replace("'","",$previous_to_prod_id);
			$updateProdID_array[]=$previous_to_prod_id;
			
			$data_array_adjust[$previous_to_prod_id]=explode("*",("".$adjust_curr_stock_to."*".$cur_st_rate_to."*".$cur_st_value_to));
			
			/*$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			if($flag==1) 
			{
				if($prodUpdate_adjust) $flag=1; else $flag=0; 
			}*/
			
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$hidden_product_id");
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty)+str_replace("'","",$hidden_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=$presentStock*$presentAvgRate;
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=$presentAvgRate."*".$txt_transfer_qnty."*".$presentStock."*".$presentStockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if($flag==1) 
			{
				if($prodUpdate) $flag=1; else $flag=0; 
			} */
			
			$supplier_id=$data_prod[0][csf('supplier_id')];
			
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category  and product_name_details=trim($txt_item_desc ) and status_active=1 and is_deleted=0");
		
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$row_prod[0][csf('stock_value')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				//$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				$curr_stock_value=$stock_value+str_replace("'", '',$txt_transfer_value);
				$curr_avg_rate_per_unit=$curr_stock_value/$curr_stock_qnty;
	
				$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				
				$data_array_prod_update=$curr_avg_rate_per_unit."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".$curr_stock_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				/*$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}*/
			}
			else
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);
				$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				$sql_prod_insert="insert into product_details_master(id, company_id, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details, lot, item_code, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, item_size, inserted_by, insert_date) 
			select	
			'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty, $stock_value, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, item_size, inserted_by, insert_date from product_details_master where id=$hidden_product_id";
				
				/*$prod=execute_query($sql_prod_insert,0);
				if($flag==1) 
				{
					if($prod) $flag=1; else $flag=0; 
				}*/ 
			}


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
			
			 //----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and store_id = $cbo_store_name_to and id not in ($update_trans_recv_id, $update_trans_recv_id )  and status_active = 1", "max_date");      
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




			
			$updateTransID_array[]=$update_trans_issue_id;
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			$updateTransID_array[]=$update_trans_recv_id; 
			$updateTransID_data[$update_trans_recv_id]=explode("*",("'".$product_id."'*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$txt_transfer_qnty."*".$txt_transfer_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			$data_array_dtls=$hidden_product_id."*".$product_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_item_category."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			//transaction table START--------------------------//
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$sql = sql_select("select a.id,a.balance_qnty,a.balance_amount,b.issue_qnty,b.rate,b.amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=57 and a.item_category=$cbo_item_category "); 
			$updateID_array = array();
			$update_data = array();
			foreach($sql as $result)
			{
				$adjBalance = $result[csf("balance_qnty")]+$result[csf("issue_qnty")];
				$adjAmount = $result[csf("balance_amount")]+$result[csf("amount")];
				$updateID_array[]=$result[csf("id")]; 
				$update_data[$result[csf("id")]]=explode("*",("".$adjBalance."*".$adjAmount."*'".$user_id."'*'".$pc_date_time."'"));
			}

			/*if(count($updateID_array)>0)
			{
				$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				if($flag==1) 
				{
					if($query) $flag=1; else $flag=0; 
				} 
				
				$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=57 ");
				{
					if($query2) $flag=1; else $flag=0; 
				} 
			}*/
			//transaction table END----------------------------//
			
			//LIFO/FIFO Start-----------------------------------------------//
			$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17");
			if($isLIFOfifo==2) $cond_lifofifo=" DESC"; else $cond_lifofifo=" ASC";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=cbo_item_category order by transaction_date $cond_lifofifo");
			foreach($sql as $result)
			{
				$mrrWiseIsID = return_next_id_by_sequence("INV_MRR_WISE_ISSUE_PK_SEQ", "inv_mrr_wise_issue_details", $con);				
				$recv_trans_id = $result[csf("id")]; // this row will be updated
				$balance_qnty = $result[csf("balance_qnty")];
				$balance_amount = $result[csf("balance_amount")];
				$cons_rate = $result[csf("cons_rate")];
				$transferQntyBalance = $balance_qnty-$transfer_qnty; // minus issue qnty
				$transferStockBalance = $balance_amount-($transfer_qnty*$cons_rate);
				if($transferQntyBalance>=0)
				{					
					$amount = $transfer_qnty*$cons_rate;
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",57,".$hidden_product_id.",".$transfer_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",57,".$hidden_product_id.",".$balance_qnty.",".$cons_rate.",".$amount.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
			
			/*if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
				if($flag==1) 
				{
					if($mrrWiseIssueID) $flag=1; else $flag=0; 
				} 
				
			}
			//transaction table stock update here------------------------//
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				if($flag==1) 
				{
					if($upTrID) $flag=1; else $flag=0; 
				} 
			}	*/
 		// LIFO/FIFO END-----------------------------------------------//
		}
		else
		{


			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name and id <> $update_trans_recv_id and status_active = 1", "max_date");      
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
			$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and store_id = $cbo_store_name_to and id not in ($update_trans_recv_id , $update_trans_issue_id) and status_active = 1", "max_date");      
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
			
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			
			$updateTransID_array[]=$update_trans_recv_id; 
			
			$updateTransID_data[$update_trans_recv_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_uom."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$txt_transfer_qnty."*".$txt_transfer_value."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			$data_array_dtls=$hidden_product_id."*".$hidden_product_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_item_category."*".$txt_transfer_qnty."*".$txt_rate."*".$txt_transfer_value."*".$cbo_uom."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
			
		
		
		
		$rID=$rID2=$rID3=$prodUpdate_adjust=$prodUpdate=$prod=$query=$query2=$mrrWiseIssueID=$upTrID=true;
		//all update and insert operation
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate_adjust=execute_query(bulk_update_sql_statement("product_details_master","id",$field_array_adjust,$data_array_adjust,$updateProdID_array));
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if(count($row_prod)>0)
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
			}
			if(count($updateID_array)>0)
			{
				$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=57 ");
			}
			
			if($data_array_mrr!="")
			{		
				$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
			}
			//transaction table stock update here------------------------//
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
			}	
			
		}

		//echo $upTrID;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $prodUpdate_adjust && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID)
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
			
			if($rID && $rID2 && $rID3 && $prodUpdate_adjust && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID)
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
 	else if ($operation==2) //Delete Here----------------------------------------------------------
	{
		$con = connect(); 
		if($db_type==0)	{ mysql_query("BEGIN"); }
		$mst_id = str_replace("'","",$update_id);
		if($mst_id=="" || $mst_id==0)
		{ 
			echo "16**Delete not allowed. Problem occurred"; 
			disconnect($con);
			die;
		}
		else 
		{
			$update_id = str_replace("'","",$update_dtls_id);
			$product_id = str_replace("'","",$hidden_product_id);
			if( str_replace("'","",$update_dtls_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred"; 
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die; 
			}
			//echo "10**nazim"; die;
			//echo "10**select id,prod_id from inv_transaction where status_active=1 and is_deleted=0 and mst_id=$mst_id and transaction_type in(5,6)"; die;
			$rID1=$rID2=$rID3=true;
			if(str_replace("'","",$cbo_transfer_criteria)==2)
			{//echo "10**nazim"; die;
				$sql = sql_select("select id from inv_transaction where status_active=1 and is_deleted=0 and mst_id=$mst_id and prod_id=$product_id and transaction_type in(5,6)");
				foreach( $sql as $row)
				{
					$trans_ids .= $row[csf("id")].","; 	
				}				
			}
			else if(str_replace("'","",$cbo_transfer_criteria)==1)
			{
				//echo "10**select id, prod_id, transaction_type, cons_quantity, cons_amount  from inv_transaction where status_active=1 and is_deleted=0 and mst_id=$mst_id and transaction_type in(5,6)"; die;
				$sql = sql_select("select id, prod_id, transaction_type, cons_quantity, cons_amount  from inv_transaction where status_active=1 and is_deleted=0 and mst_id=$mst_id and transaction_type in(5,6)");
				$max_id=0;
				foreach( $sql as $row)
				{
					$prod_ids .= $row[csf("prod_id")].",";
					$trans_ids .= $row[csf("id")].","; 	
					if ($row[csf("id")] > $max_id)
					{
				        $max_id = $row[csf("id")];
				    }
					
					if($row[csf("transaction_type")]==6)
					{
						$issue_trans_id=$row[csf("id")];
						$issue_qnty=$row[csf("cons_quantity")];
						$issue_amt=$row[csf("cons_amount")];
						$issue_prod_id=$row[csf("prod_id")];
					}
					else
					{
						$rcv_trans_id=$row[csf("id")];
						$rcv_qnty=$row[csf("cons_quantity")];
						$rcv_amt=$row[csf("cons_amount")];
						$rcv_prod_id=$row[csf("prod_id")];
					} 	
				}
				$prod_ids=chop($prod_ids,",");
				//echo "10**select id from inv_transaction where and prod_id in ($prod_ids) and status_active=1 and is_deleted=0 and id >$max_id  "; die;
				$chk_next_transaction=return_field_value("id","inv_transaction"," and prod_id in ($prod_ids) and status_active=1 and is_deleted=0 and id >$max_id ","id");
				if($chk_next_transaction !="")
				{ 
					echo "17**Delete not allowed.This item is used in another transaction"; 
					disconnect($con);
					die;
				}
				else
				{
					//echo "10**select id from inv_mrr_wise_issue_details where prod_id=$issue_prod_id and status_active=1 and is_deleted=0 and issue_trans_id=$issue_trans_id "; die;
					$mrr_table_id=return_field_value("id","inv_mrr_wise_issue_details","prod_id=$issue_prod_id and status_active=1 and is_deleted=0 and issue_trans_id=$issue_trans_id ","id");

					$issue_prod_sql = sql_select("select avg_rate_per_unit, current_stock, stock_value from product_details_master where status_active=1 and is_deleted=0 and id=$issue_prod_id");					
					$beforeStock=$beforeStockValue=0; 
					foreach( $prod_sql as $row)
					{
						$beforeStock			=$row[csf("current_stock")];
						$beforeStockValue		=$row[csf("stock_value")];
					}
					//stock value minus here---------------------------//
					$adj_beforeStock			=$beforeStock+$issue_qnty;
					$adj_beforeStockValue		=$beforeStockValue+$issue_amt;
					//$adj_beforeAvgRate		=number_format(($adj_beforeStockValue/$adj_beforeStock),$dec_place[3],'.','');		
					$field_array_product_issue="current_stock*stock_value*updated_by*update_date";
					$data_array_product_issue = "".$adj_beforeStock."*".number_format($adj_beforeStockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";

					$rcv_prod_sql = sql_select("select avg_rate_per_unit, current_stock, stock_value from product_details_master where status_active=1 and is_deleted=0 and id=$rcv_prod_id");
					
					$beforeRcvStock=$beforeRcvStockValue=$beforeRcvAvgRate=0; 
					foreach( $prod_sql as $row)
					{
						$beforeRcvStock			=$row[csf("current_stock")];
						$beforeRcvStockValue	=$row[csf("stock_value")];
						$beforeRcvAvgRate		=$row[csf("avg_rate_per_unit")];	
					}
					//stock value minus here---------------------------//
					$adj_RcvbeforeStock			=$beforeRcvStock+$rcv_qnty;
					$adj_RcvbeforeStockValue	=$beforeRcvStockValue+$rcv_amt;
					$adj_RecbeforeAvgRate			=number_format(($adj_RcvbeforeStockValue/$adj_RcvbeforeStock),$dec_place[3],'.','');		
				
					$field_array_product_rcv="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
					$data_array_product_rcv = "".$adj_RecbeforeAvgRate."*".$adj_RcvbeforeStock."*".number_format($adj_RcvbeforeStockValue,$dec_place[4],'.','')."*'".$user_id."'*'".$pc_date_time."'";
				}

				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
				$field_array_mrr="updated_by*update_date*status_active*is_deleted";
				$data_array_mrr="".$user_id."*'".$pc_date_time."'*0*1";

				$rID1=sql_update("product_details_master",$field_array_product_issue,$data_array_product_issue,"id",$issue_prod_id,1);
				$rID2=sql_update("product_details_master",$field_array_product_rcv,$data_array_product_rcv,"id",$rcv_prod_id,1);
				$rID3=sql_update("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,"id",$mrr_table_id,1);
			}
			$trans_ids=chop($trans_ids,",");
			$field_array_trans="updated_by*update_date*status_active*is_deleted";
			$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
			
			$field_array_dtls="updated_by*update_date*status_active*is_deleted";
			$data_array_dtls="".$user_id."*'".$pc_date_time."'*0*1";
			//echo "10**".sql_multirow_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$trans_ids,0); die;
			$rID=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_id,1);
			$statusChange=sql_multirow_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$trans_ids,1);
			//echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$statusChange; die;
			if($db_type==0)
			{
				if($rID && $rID1 && $rID2 && $rID3 && $statusChange)
				{
					mysql_query("COMMIT");  
					echo "2**".$mst_id."**".str_replace("'","",$txt_system_id)."**0";
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$mst_id."**".str_replace("'","",$txt_system_id)."**0";
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID1 && $rID2 && $rID3 && $statusChange)
				{
					oci_commit($con);
					echo "2**".$mst_id."**".str_replace("'","",$txt_system_id)."**0";
				}
				else
				{
					oci_rollback($con); 
					echo "10**".$mst_id."**".str_replace("'","",$txt_system_id)."**0";
				}
			}
			disconnect($con);
			die;
 		}
	}
}
function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
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

	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	echo "10**".$strQuery; die;
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
if($action=="yarn_transfer_print")
{
	 
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id in (".implode(",",array_flip($item_category)).")","id","product_name_details");
	$brand_arr=return_library_array( "select id, brand_name from   lib_brand", "id", "brand_name");
?>
<div style="width:930px; font-family:'Arial Narrow'">
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
        	<td width="120"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
            <td width="130"><strong>Transfer Criteria:</strong></td> <td width="175px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
            <td width="125"><strong>To Company</strong></td><td width="175px"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
        </tr>
        <tr>
            <td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
            <td><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
    </table>
        <br>
    <div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="50">SL</th>
            <th width="150" >From Store</th>
            <th width="150" >To Store</th>
            <th width="130" >Item Category</th> 
            <th width="250" >Item Description</th>
            <th>Transfered Qnty</th>
            
        </thead>
        <tbody> 
   
<?
	$sql_dtls="select id, from_store, to_store, from_prod_id, yarn_lot, transfer_qnty,item_category from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
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
                <td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" align="right"><strong>Total :</strong></td>
                <td align="right" style="font-weight:bold;"><?php echo $transfer_qnty_sum; ?></td>
               
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(153, $data[0], "900px");
         ?>
      </div>
   </div>   
 <?
 exit();	
 
}
?>
