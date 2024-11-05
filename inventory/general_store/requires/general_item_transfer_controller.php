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
$userCredential = sql_select("SELECT unit_id as company_id,store_location_id FROM user_passwd where id=$user_id");
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

// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	/*echo "select store_method from variable_settings_inventory where company_name='$cbo_company_id' and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1";die;*/
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

// ==============End Floor Room Rack Shelf Bin upto variable Settings==============
if($action=="varible_inventory")
{
	$sql_variable_inventory=sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=57");
	if(count($sql_variable_inventory)>0)
	{
		echo "1**".$sql_variable_inventory[0][csf("is_editable")];
	}
	else
	{
		echo "0**".$sql_variable_inventory[0][csf("is_editable")];
	}
	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name=$data and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo "**".$variable_inventory;
	$variable_lot=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$data and variable_list=32 and status_active=1 and is_deleted=0");
	echo "**".$variable_lot;
	die;
}
/*if ($action=="load_drop_down_store")
{

	echo create_drop_down( "cbo_store_name", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond and b.category_type in (".implode(",",array_flip($general_item_category)).") group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	   exit();
}

if ($action=="load_drop_down_store_to")
{
	//echo "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data and b.category_type in (4,8,9,10,11,15,16,17,18,19,20,21,22,32,34,35,36,37,38,39) group by a.id,a.store_name order by a.store_name";die;
	echo create_drop_down( "cbo_store_name_to", 160, "select a.id,a.store_name from lib_store_location a,lib_store_location_category  b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id=$data $store_location_credential_cond and b.category_type in (".implode(",",array_flip($general_item_category)).") group by a.id,a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "",0 );  	 
	   exit();
}*/
  
if ($action=="load_drop_down_to_company")
{
	$data=explode("_",$data);
	$company_cond="";
	$company_id=$data[0];
	$transfer_criteria=$data[1];

	if ($transfer_criteria==1){
		if ($company_id != 0) $company_cond=" and id <> $company_id";
	}
	
	echo create_drop_down( "cbo_company_id_to", 160, "select id, company_name from lib_company where status_active=1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Company--", 0, "load_drop_down('requires/general_item_transfer_controller', this.value, 'load_drop_down_store','store_td_to');","" );
	exit();
}

if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/general_item_transfer_controller",$data);
}

//if ($action=="load_drop_down_store")
//{
//	$data=explode("_",$data);
//	echo create_drop_down( "cbo_store_name_to", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99) and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"reset_on_change(this.id);load_drop_down('requires/general_item_transfer_controller', this.value+'_'+$data[0], 'load_drop_floor','to_floor_td');storeUpdateUptoDisable();");
//	exit();
//}
if ($action=="load_drop_down_store")
{
    $data=explode("_",$data);
    echo create_drop_down( "cbo_store_name_to", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[0]' and b.category_type in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99) and a.status_active=1 and a.is_deleted=0  group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"reset_on_change(this.id);load_drop_down('requires/general_item_transfer_controller', this.value+'_'+$data[0], 'load_drop_floor','to_floor_td');storeUpdateUptoDisable();");
    exit();
}

if($action == "load_drop_floor")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_floor_to", 152, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "reset_on_change(this.id);load_drop_down('requires/general_item_transfer_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_room','to_room_td');storeUpdateUptoDisable();" );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$floor_id=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];

	echo create_drop_down( "cbo_room_to", 152, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.floor_id='$floor_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "reset_on_change(this.id);load_drop_down('requires/general_item_transfer_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_rack','to_rack_td');storeUpdateUptoDisable();" );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$room_id=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	echo create_drop_down( "txt_rack_to", 152, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.room_id='$room_id' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "reset_on_change(this.id);load_drop_down('requires/general_item_transfer_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_shelf','to_shelf_td');storeUpdateUptoDisable();" );
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$rack=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	echo create_drop_down( "txt_shelf_to", 152, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.rack_id='$rack' and b.store_id=$store_id and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "reset_on_change(this.id);load_drop_down('requires/general_item_transfer_controller', this.value+'_'+$company_id+'_'+$store_id, 'load_drop_bin','to_bin_td');storeUpdateUptoDisable();" );
}

if($action == "load_drop_bin")
{
	$data = explode("_", $data);
	$shelf=$data[0];
	$company_id = $data[1];
	$store_id = $data[2];
	echo create_drop_down( "cbo_bin_to", 152, "select b.bin_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.bin_id and b.shelf_id=$shelf and b.store_id='$store_id' and a.company_id='$company_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.bin_id,a.floor_room_rack_name order by a.floor_room_rack_name","bin_id,floor_room_rack_name", 1, "--Select Bin--", 0, "" );
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
						<th width="280" id="search_by_td_up">Please Enter Item Code</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="product_id" id="product_id" class="text_boxes" value="">
						</th>
					</thead>
					<tr class="general">
						<td>
						<?
						if($cbo_item_category!=0) $item_category_cond=" and item_category='$cbo_item_category'"; else  $item_category_cond="";
						echo create_drop_down( "cbo_item_group_id", 130,"select id,item_name from lib_item_group where status_active=1 $item_category_cond","id,item_name", 1, "-- Select --", "", "","","","","","");
						?>
						</td>
						<td>
							<?
								$search_by_arr=array(1=>"Item Details",2=>"Product Id.",3=>"Item Code");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", 3,$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('cbo_item_group_id').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_item_category; ?>+'_'+<? echo $cbo_store_name; ?>, 'create_product_search_list_view', 'search_div', 'general_item_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	echo load_html_head_contents("Item popup", "../../../", 1, 1,'','','');
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
	
	if($data[0]!="")
	{
		if($search_by==1) $search_field=" and a.product_name_details like '$search_string' ";	 
		else if($search_by==2)  $search_field=" and a.id like '$search_string' ";
		else if($search_by==3)  $search_field=" and a.item_code like '$search_string' ";
	}
	$entry_form_cond="";
	if($item_category_id==4) $entry_form_cond=" and a.entry_form=20";
 	//$sql="select id, company_id, supplier_id, product_name_details, lot,item_group_id, current_stock, brand, unit_of_measure from product_details_master where item_category_id in (4,8,9,10,11,15,16,17,18,19,20,21,22) and company_id=$company_id and $search_field like '$search_string' and current_stock>0 and status_active=1 and is_deleted=0 $item_group_cond  $item_category_cond";
	
	$sql="SELECT a.id, a.company_id, a.supplier_id, a.item_description,a.item_size, a.item_code, b.batch_lot as lot, a.item_group_id, a.current_stock as item_global_stock, a.brand, a.unit_of_measure,
	sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box 
	from product_details_master a, inv_transaction b 
	where a.id=b.prod_id  and a.company_id=$company_id $search_field and a.current_stock>0 and a.status_active in(1,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $entry_form_cond  $str_cond 
	group by a.id, a.company_id, a.supplier_id, a.item_description, a.item_size, a.item_code, b.batch_lot, a.item_group_id, a.current_stock, a.brand, a.unit_of_measure, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box 
	having sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end))>0";
	
	//echo $sql;
		
	$store_name_arr = return_library_array("select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$company_id and b.category_type in(4,8,9,10,11,15,16,17,18,19,20,21,22,32,33,34,35,36,37,38,39,40,41,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,89,90,91,92,93,94,99) and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id","store_name");

	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where company_id=$company_id and status_active=1 and is_deleted=0 group by floor_room_rack_id, floor_room_rack_name order by floor_room_rack_name","floor_room_rack_id","floor_room_rack_name");

	$item_group_arr=return_library_array( "select id,item_name from lib_item_group where status_active=1 ",'id','item_name');
	$arr=array(1=>$company_arr,2=>$supplier_arr,4=>$item_group_arr,8=>$unit_of_measurement,9=>$store_name_arr,10=>$floor_room_rack_arr,11=>$floor_room_rack_arr,12=>$floor_room_rack_arr,13=>$floor_room_rack_arr,14=>$floor_room_rack_arr);
	?>
    <table>
    	<thead>
        	<tr>
                <th colspan="8"><? echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" ); ?></th>
            </tr>
        </thead>
    </table>
    <?
	echo  create_list_view("tbl_list_search", "Item ID,Company,Supplier,Item Code,Item Group,Item Details,Item Size,Lot No,UOM,Store,Floor,Room,Rack,Self,Bin/Box,Stock", "60,120,120,100,100,160,60,60,80,80,80,80,80,80,60","1480","250",0, $sql, "js_set_value", "id,store_id,floor_id,room,rack,self,bin_box,lot", "", 1, "0,company_id,supplier_id,0,item_group_id,0,0,0,unit_of_measure,store_id,floor_id,room,rack,self,bin_box", $arr, "id,company_id,supplier_id,item_code,item_group_id,item_description,item_size,lot,unit_of_measure,store_id,floor_id,room,rack,self,bin_box,current_stock", '','','0,0,0,0,0,0,0,0,0,0,0,0,0,0,0');
	
	/*echo  create_list_view("tbl_list_search", "Item ID,Company,Supplier,Item Group,Item Details,Item Size,Lot No,UOM,Stock", "60,120,120,100,160,60,60,60","900","250",0, $sql, "js_set_value", "id,store_id,floor_id,room,rack,self,bin_box", "", 1, "0,company_id,supplier_id,item_group_id,0,0,0,unit_of_measure,0", $arr, "id,company_id,supplier_id,item_group_id,item_description,item_size,lot,unit_of_measure,current_stock", '','','0,0,0,0,0,0,0,0,2');*/
	
	exit();
}

if($action=='populate_data_from_product_master')
{
	//print_r($data);die;
	$data_ref=explode("_",$data);
	$prod_id=$data_ref[0];
	$from_store_id=$data_ref[1];
	$floor_id = $data_ref[2];
	$room = $data_ref[3];
	$rack = $data_ref[4];
	$self = $data_ref[5];
	$bin = $data_ref[6];
	$batch_lot = str_replace("'","",$data_ref[7]);
	$from_company_id = $data_ref[9];
	// echo $floor_id.'='.$room.'='.$rack.'='.$self.'='.$bin;die;
	
	$sqlCon="";
	if ($floor_id!="") { $sqlCon= " and b.floor_id=$floor_id"; }
	if($room!="") { $sqlCon.= " and b.room=$room"; }
	if($rack!="") { $sqlCon.= " and b.rack=$rack"; }
	if($self!="") { $sqlCon.= " and b.self=$self"; }
	if($bin!="") { $sqlCon.= " and b.bin_box=$bin"; }
	if($batch_lot!="") { $sqlCon.= " and b.batch_lot='$batch_lot'"; }
	// echo $sqlCon;die;
	$item_group_arr=return_library_array( "select id,item_name from lib_item_group where status_active=1 ",'id','item_name');
	$data_array=sql_select("SELECT a.item_group_id,a.item_description,a.product_name_details, b.batch_lot, a.current_stock as item_global_stock, a.avg_rate_per_unit, a.brand, a.item_category_id, a.unit_of_measure, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, rtrim(xmlagg(xmlelement(e,b.id,',').extract('//text()') order by b.id).GetClobVal(),',') as id  
	from product_details_master a, inv_transaction b 
	where a.id=b.prod_id and b.status_active=1 and b.is_deleted=0 and a.id='$prod_id' and b.store_id=$from_store_id $sqlCon
	group by a.item_group_id,a.item_description,a.product_name_details, b.batch_lot, a.current_stock, a.avg_rate_per_unit, a.brand, a.item_category_id, a.unit_of_measure, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box");
	foreach ($data_array as $row)
	{ 
		$row[csf("id")]=$row[csf("id")]->load();
		$product_desc=$item_group_arr[$row[csf("item_group_id")]].' '.$row[csf("item_description")];
		echo "document.getElementById('hidden_product_id').value 			= '".$prod_id."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$product_desc."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$row[csf("current_stock")]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row[csf("avg_rate_per_unit")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("unit_of_measure")]."';\n";
		echo "document.getElementById('txt_lot').value 						= '".$row[csf("batch_lot")]."';\n";
		//
		$serialString = return_field_value("LISTAGG(CAST(serial_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY serial_no) as serial_no","inv_serial_no_details","recv_trans_id=".$row[csf("id")]." group by recv_trans_id","serial_no");

		echo "$('#txt_serial_no').val('".$serialString."');\n";

		if($floor_id !=0)
		{
			echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'floor','floor_td', '".$from_company_id."','"."','".$from_store_id."',this.value);\n";
			echo "document.getElementById('cbo_floor').value 				= '".$floor_id."';\n";
		}
		if($room !=0)
		{
			echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'room','room_td', '".$from_company_id."','"."','".$from_store_id."','".$floor_id."',this.value);\n";
			echo "document.getElementById('cbo_room').value 				= '".$room."';\n";
		}
		if($rack !=0)
		{
			echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'rack','rack_td', '".$from_company_id."','"."','".$from_store_id."','".$floor_id."','".$room."',this.value);\n";
			echo "document.getElementById('txt_rack').value 				= '".$rack."';\n";
		}
		if($self !=0)
		{
			echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'shelf','shelf_td', '".$from_company_id."','"."','".$from_store_id."','".$floor_id."','".$room."','".$rack."',this.value);\n";
			echo "document.getElementById('txt_shelf').value 				= '".$self."';\n";
		}

		if($bin !=0)
		{
			echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'bin','bin_td', '".$from_company_id."','"."','".$from_store_id."','".$floor_id."','".$room."','".$rack."','".$self."',this.value);\n";
			echo "document.getElementById('cbo_bin').value 					= '".$bin."';\n";
		}
		
		echo "disable_enable_fields('cbo_floor*cbo_room*txt_rack*txt_shelf',1,'','');\n";
		
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
				var id = data.split("_");
				$('#transfer_id').val(id[0]);
				$('#hidden_posted_in_account').val(id[1]);
				parent.emailwindow.hide();
			}
		
		</script>

	</head>


	<body>
	<div align="center" style="width:980px;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:960px;margin-left:10px">
			<legend>Enter search words</legend>
				<table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
					<thead>
						<th width="100">Transfer Year</th>
						<th width="150">Search By</th>
						<th width="150" id="search_by_td_up">Please Enter Transfer ID</th>
						<th>Transfer Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
							<input type="hidden" name="transfer_id" id="transfer_id" class="text_boxes" value="">
							<input type="hidden" id="hidden_posted_in_account" value="hidden_posted_in_account" />
						</th>
					</thead>
					<tr class="general">
						<td align="center"><? echo create_drop_down( "cbo_year_selection", 100, $year,"", 1, "-- Select --", date('Y'), "",0 ); ?></td>
						<td>
							<?
								$search_by_arr=array(1=>"Transfer ID",2=>"Challan No",3=>"Requisition No");
								$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td id="search_by_td">
							<input type="text" style="width:137px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_company_to; ?>, 'create_transfer_search_list_view', 'search_div', 'general_item_transfer_controller', 'setFilterGrid(\'tbl_po_list\',-1);')" style="width:100px;" />
						</td>
						
					</tr>
					<tr>                  
						<td align="center" height="40" valign="middle" colspan="5">
							<? echo load_month_buttons(1);  ?>
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

if($action=='create_transfer_search_list_view_old')
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
	
 	$sql="select id, $year_field transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, is_posted_account 
	from inv_item_transfer_mst where entry_form=57 and company_id=$company_id and transfer_criteria=$transfer_criteria and $search_field like '$search_string' and status_active=1 and is_deleted=0";
	//echo $sql;,6=>$item_category
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria", "70,60,100,120,90","700","250",0, $sql, "js_set_value", "id,is_posted_account", "", 1, "0,0,0,company_id,0,transfer_criteria", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria", '','','0,0,0,0,3,0');
	exit();
}

if($action=='create_transfer_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$trans_criteria =$data[3];
	$selected_year =$data[4];
	$from_date =$data[5];
	$to_date =$data[6];
	$to_company =$data[7];
	
	if($search_by==1)
		$search_field="transfer_system_id";	
	else if($search_by==2)
		$search_field="challan_no";
	else 
	    $search_field="requisition_no";

	if($db_type==0)
	{ 
		if ($from_date!="" &&  $to_date!="") $transfer_date_cond = "and b.transfer_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'"; else $transfer_date_cond ="";
		$year_cond=" and YEAR(insert_date)=$selected_year";  
	}
	else
	{
		if ($from_date!="" &&  $to_date!="") $transfer_date_cond = "and b.transfer_date between '".change_date_format($from_date, "", "",1)."' and '".change_date_format($to_date, "", "",1)."'"; else $transfer_date_cond ="";
		$year_cond=" and to_char(b.insert_date,'YYYY')=$selected_year";
	}
	
	if($db_type==0) $year_field="YEAR(b.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(b.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	if($to_company!=0){
		$company_to=" and to_company= $to_company";
	}
	
 	//$sql="select id, $year_field transfer_prefix_number, transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria,item_category from inv_item_transfer_mst where entry_form=55 and company_id=$company_id  $transfer_date_cond $year_cond and $search_field like '$search_string' and transfer_criteria in($trans_criteria) and status_active=1 and is_deleted=0 order by id";

 	$sql="SELECT b.transfer_criteria,b.company_id,b.to_company,b.is_posted_account,b.transfer_prefix_number, b.transfer_system_id, b.challan_no,  b.transfer_date,a.id,a. mst_id, a.from_store, a.to_store,a.trans_id ,a.to_trans_id,a.item_category,b.requisition_no, $year_field from inv_item_transfer_dtls a, inv_item_transfer_mst b where b.entry_form=57 and b.status_active=1 and b.is_deleted=0 and b.company_id=$company_id  $transfer_date_cond $year_cond $company_to and $search_field like '$search_string' and b.transfer_criteria in($trans_criteria) and b.id=a.mst_id";
 	$qry_result=sql_select($sql);
 	foreach ($qry_result as  $row) 
	{
		//
		$trans_arr[$row[csf("mst_id")]]["transfer_criteria"] =$row[csf("transfer_criteria")];
		$trans_arr[$row[csf("mst_id")]]["is_posted_account"] =$row[csf("is_posted_account")];
		$trans_arr[$row[csf("mst_id")]]["year"] =$row[csf("year")];
		$trans_arr[$row[csf("mst_id")]]["company_id"] =$row[csf("company_id")];
		$trans_arr[$row[csf("mst_id")]]["to_company"] =$row[csf("to_company")];
		$trans_arr[$row[csf("mst_id")]]["transfer_prefix_number"] =$row[csf("transfer_prefix_number")];
		$trans_arr[$row[csf("mst_id")]]["transfer_system_id"] =$row[csf("transfer_system_id")];
		$trans_arr[$row[csf("mst_id")]]["challan_no"] =$row[csf("challan_no")];
		$trans_arr[$row[csf("mst_id")]]["transfer_date"] =$row[csf("transfer_date")];
		$trans_arr[$row[csf("mst_id")]]["from_store"] .=$row[csf("from_store")].',';
		$trans_arr[$row[csf("mst_id")]]["to_store"] .=$row[csf("to_store")].',';
		$trans_arr[$row[csf("mst_id")]]["item_category"] .=$row[csf("item_category")].',';
		$trans_arr[$row[csf("mst_id")]]["requisition_no"] =$row[csf("requisition_no")];
	}
	//echo $sql;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="950" >
        <thead>
            <th width="30">SL</th>
            <th width="40">Transfer ID</th>
            <th width="40">Year</th>
            <th width="80">Challan No</th>
            <th width="80">Requisition No</th>
            <th width="100">Company</th>
            <th width="100">To Company</th>
            <th width="60">Transfer Date</th>
            <th width="120">Transfer Criteria</th>
            <th width="120">From Store</th>
            <th width="80">To Store</th>
            <th>Item Category</th>
        </thead>
        </table>
        <div style="width:950px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table" id="tbl_po_list">
        <tbody>
            <? 
            $i=1; 
            foreach($trans_arr as $mst_id=> $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
               	// $from_store = $row['from_store'];
				$from_store=array_unique(explode(",",$row['from_store']));
				$to_store=array_unique(explode(",",$row['to_store']));
				$item_categorys=array_unique(explode(",",$row['item_category']));
				$from_store_name=""; $to_store_name="";	 $category_name="";	
				foreach ($from_store as $store_id){
					if($from_store_name=="") $from_store_name=$store_arr[$store_id]; else $from_store_name.=','.$store_arr[$store_id];
				}

				foreach ($to_store as $store_id){
					if($to_store_name=="") $to_store_name=$store_arr[$store_id]; else $to_store_name.=','.$store_arr[$store_id];
				}

				foreach ($item_categorys as $cat){
					if($category_name=="") $category_name=$item_category[$cat]; else $category_name.=','.$item_category[$cat];
				}

				
				//$buyer_style=implode(",",array_unique(explode(",",$buyer_styles)));
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $mst_id.'_'.$row['is_posted_account']; ?>")' style="cursor:pointer" >
                    <td width="30"><? echo $i; ?></td>
                    <td width="40" style="text-align:center;"><? echo $row['transfer_prefix_number']; ?></td>
                    <td width="40" style="text-align:center;"><? echo $row['year']; ?></td>
                    <td width="80"><? echo $row['challan_no']; ?></td>
                    <td width="80"><? echo $row['requisition_no']; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $company_arr[$row['company_id']]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $company_arr[$row['to_company']]; ?></td>
                    <td width="60" style="text-align:center;"><? echo change_date_format($row['transfer_date']); ?></td>
                    <td width="120" style="text-align:center;"><? echo $item_transfer_criteria[$row['transfer_criteria']]; ?></td>	
                    <td width="120" style="text-align:center;"><? echo chop($from_store_name,','); ?></td>	
                    <td width="80" style="text-align:center;"><? echo chop($to_store_name,','); ?></td>	
                    <td style="word-break:break-all"><? echo chop($category_name,','); ?></td>
                </tr>
				<? 
                $i++; 
            } 
            ?>
        </tbody>
    </table>
	<script>
		setFilterGrid("tbl_po_list",-1);
	</script>
	<?
	/*$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "70,60,100,120,90,140","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');*/
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	
	$data_array=sql_select("select transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, to_company, requisition_no, requisition_id from inv_item_transfer_mst where id='$data'");

	$company_id=$data_array[0][csf("company_id")];
	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method=$variable_inventory_sql[0][csf("store_method")];


	$to_company=$data_array[0][csf("to_company")];
	if($transfer_criteria=$data_array[0][csf("transfer_criteria")] != 1)
	{
		$to_company=$data_array[0][csf("company_id")];
	}
	
	

	$variable_inventory_sql_to=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$to_company and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$store_method_to=$variable_inventory_sql_to[0][csf("store_method")];

	foreach ($data_array as $row)
	{ 
		$to_company=$row[csf("to_company")];
		if (str_replace("'", "", $row[csf("transfer_criteria")]) !=1) 
		{
			$to_company=$row[csf("company_id")];
		}
		
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		
		echo "active_inactive(".$row[csf("transfer_criteria")].");\n";
		
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_requisition_no').value 			= '".$row[csf("requisition_no")]."';\n";
		echo "document.getElementById('hidden_req_id').value 				= '".$row[csf("requisition_id")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$to_company."';\n";
		//echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('store_update_upto').value 			= '".$store_method."';\n";
		echo "document.getElementById('store_update_upto_to').value 		= '".$store_method_to."';\n";
		echo "load_drop_down('requires/general_item_transfer_controller', $to_company, 'load_drop_down_store','store_td_to');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id_to').attr('disabled','disabled');\n";
		echo "$('#txt_requisition_no').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		
		exit();
	}
}

if($action=="show_transfer_listview")
{
	//$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id in (".implode(",",array_flip($general_item_category)).")","id","product_name_details");
	$sql_prod_res=sql_select("select a.id, b.item_name, a.item_description, a.product_name_details from product_details_master a, lib_item_group b where a.item_group_id=b.id and a.item_category_id in (".implode(",",array_flip($general_item_category)).")");
	foreach($sql_prod_res as $row){
		$product_arr[$row[csf('id')]]=$row[csf('item_name')].' '.$row[csf('item_description')];
	}
	
	$sql="select id, from_store, to_store, from_prod_id, transfer_qnty from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$store_arr,1=>$store_arr,2=>$product_arr);
	 
	echo  create_list_view("list_view", "From Store,To Store,Item Description,Transfered Qnty", "150,150,250","680","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "from_store,to_store,from_prod_id,0", $arr, "from_store,to_store,from_prod_id,transfer_qnty", "requires/general_item_transfer_controller",'','0,0,0,2');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	$data_array=sql_select("select  a.transfer_criteria, a.company_id,a.to_company,b.id, b.mst_id, b.from_store, b.to_store,b.floor_id,b.room,b.rack,b.shelf,b.bin_box,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf, b.to_bin_box, b.from_prod_id, b.to_prod_id, b.transfer_qnty,b.serial_no, b.rate,b.item_category, b.transfer_value, b.yarn_lot, b.brand_id, b.uom, b.requisition_dtls_id 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b 
	where b.id='$data' and a.id=b.mst_id");
	foreach ($data_array as $row)
	{ 
		if ($row[csf("transfer_criteria")]==1) {
			$company_id=$row[csf("to_company")];
		}
		else
		{
			$company_id=$row[csf("company_id")];
		}
		
		$from_bin_box=(str_replace("'", "", $row[csf("bin_box")]) =="" )?0:$row[csf("bin_box")];
		$to_bin_box=(str_replace("'", "", $row[csf("to_bin_box")]) =="" )?0:$row[csf("to_bin_box")];

		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'store','from_store_td', $('#cbo_company_id').val(),'"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store")]."';\n";
		echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'floor','floor_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$row[csf("floor_id")]."';\n";
		if($row[csf("floor_id")])
		{
			echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'room','room_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_room').value 				= '".$row[csf("room")]."';\n";
		if($row[csf("room")])
		{
			echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'rack','rack_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		}
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		if($row[csf("rack")])
		{
			echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'shelf','shelf_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		}
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";
		if($row[csf("shelf")])
		{
			echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'bin','bin_td', $('#cbo_company_id').val(),'"."','".$row[csf('from_store')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('shelf')]."',this.value);\n";
		}
		echo "document.getElementById('cbo_bin').value 					= '".$from_bin_box."';\n";
		// =============================================================================================================
		echo "load_drop_down('requires/general_item_transfer_controller', $company_id, 'load_drop_down_store','store_td_to');\n";
		echo "document.getElementById('cbo_store_name_to').value 		= '".$row[csf("to_store")]."';\n";

		echo "load_drop_down('requires/general_item_transfer_controller','".$row[csf("to_store")].'_'.$company_id."', 'load_drop_floor','to_floor_td');\n";
		echo "document.getElementById('cbo_floor_to').value 			= '".$row[csf("to_floor_id")]."';\n";
		if($row[csf("to_floor_id")])
		{
			echo "load_drop_down('requires/general_item_transfer_controller','".$row[csf("to_floor_id")].'_'.$company_id.'_'.$row[csf("to_store")]."', 'load_drop_room','to_room_td');\n";
		}
		echo "document.getElementById('cbo_room_to').value 				= '".$row[csf("to_room")]."';\n";
		if($row[csf("to_room")])
		{
			echo "load_drop_down('requires/general_item_transfer_controller','".$row[csf("to_room")].'_'.$company_id.'_'.$row[csf("to_store")]."', 'load_drop_rack','to_rack_td');\n";
		}
		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		if($row[csf("to_rack")])
		{
			echo "load_drop_down('requires/general_item_transfer_controller','".$row[csf("to_rack")].'_'.$company_id.'_'.$row[csf("to_store")]."', 'load_drop_shelf','to_shelf_td');\n";
		}
		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		if($row[csf("to_shelf")])
		{
			echo "load_drop_down('requires/general_item_transfer_controller','".$row[csf("to_shelf")].'_'.$company_id.'_'.$row[csf("to_store")]."', 'load_drop_bin','to_bin_td');\n";
		}
		echo "document.getElementById('cbo_bin_to').value 				= '".$to_bin_box."';\n";

		echo "document.getElementById('cbo_store_name_to').disabled=true; ;\n";
		echo "document.getElementById('cbo_floor_to').disabled=true; ;\n";
		echo "document.getElementById('cbo_room_to').disabled=true; ;\n";
		echo "document.getElementById('txt_rack_to').disabled=true; ;\n";
		echo "document.getElementById('txt_shelf_to').disabled=true; ;\n";

		echo "document.getElementById('txt_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('txt_serial_no').value 		= '".$row[csf("serial_no")]."';\n";
		echo "document.getElementById('txt_rate').value 				= '".$row[csf("rate")]."';\n";
		echo "document.getElementById('txt_transfer_value').value 		= '".$row[csf("transfer_value")]."';\n";
		echo "document.getElementById('cbo_item_category').value 		= '".$row[csf("item_category")]."';\n";
		//echo "document.getElementById('txt_yarn_brand').value 				= '".$brand_arr[$row[csf("brand_id")]]."';\n";
		echo "document.getElementById('hidden_transfer_qnty').value 	= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('hidden_product_id').value 		= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_from_prod_id').value 	= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('previous_to_prod_id').value 		= '".$row[csf("to_prod_id")]."';\n";
		echo "document.getElementById('cbo_uom').value 					= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('hidden_req_dtls_id').value 		= '".$row[csf("requisition_dtls_id")]."';\n";
		echo "storeUpdateUptoDisable();\n";
		echo "disable_enable_fields('cbo_store_name*cbo_floor*cbo_room*txt_rack*txt_shelf*cbo_bin*cbo_item_category*txt_item_desc',1);\n";
		
		$sql=sql_select("select a.product_name_details, a.current_stock as item_global_stock, a.avg_rate_per_unit, sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end)) as current_stock  
		from product_details_master a, inv_transaction b  
		where a.id=b.prod_id and a.id='".$row[csf('from_prod_id')]."' and b.store_id='".$row[csf("from_store")]."' and b.status_active=1 and b.is_deleted=0 
		group by a.product_name_details, a.current_stock, a.avg_rate_per_unit" );
		
		$stock=$sql[0][csf("current_stock")]+$row[csf("transfer_qnty")];
		
		echo "document.getElementById('txt_item_desc').value 				= '".$sql[0][csf("product_name_details")]."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$stock."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$stock."';\n";
		$prod_id=$row[csf("from_prod_id")].",".$row[csf("to_prod_id")];
		$sql_trans=sql_select("select id, transaction_type, batch_lot from inv_transaction where mst_id=".$row[csf('mst_id')]." and item_category=".$row[csf("item_category")]." and transaction_type in(5,6) and prod_id in($prod_id) order by id asc");
        //echo "select id, transaction_type from inv_transaction where mst_id=$row[mst_id] and item_category=1 and transaction_type in(5,6) order by id asc";die;
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("id")]."';\n";
		echo "document.getElementById('txt_lot').value 						= '".$sql_trans[0][csf("batch_lot")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 

		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$uptrIssId=str_replace("'","",$update_trans_issue_id);
	$uptrRcvId=str_replace("'","",$update_trans_recv_id);
	$variable_lot=str_replace("'","",$variable_lot);
	$trnsQnty=str_replace("'","",$txt_transfer_qnty);
	$trnsValue=str_replace("'","",$txt_transfer_value);
	$txt_transfer_value=str_replace("'","",$txt_transfer_value);
	$txt_rate=str_replace("'","",$txt_rate);
	$up_tr_cond="";
	if($uptrIssId >0 && $uptrRcvId >0 )
	{
		$sqlLotCon= "" ;
		if($variable_lot==1 && str_replace("'","",$cbo_item_category)==22)
		{
			$sqlLotCon= " and batch_lot=$txt_lot" ;
		}
		$up_tr_cond=" and id not in($uptrIssId,$uptrRcvId)";
		$trans_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
		from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$hidden_product_id and store_id=$cbo_store_name_to $sqlLotCon $up_tr_cond");
		$stockQnty=$trans_sql[0][csf("bal")]*1;
		
		if($stockQnty < 0 )
		{
			 echo "20**Transfer Quantity Not Allow Over Stock Quantity.";disconnect($con);die;
		}
	}
	
	if(number_format($txt_rate,10,".","")==0)
	{
		echo "20**Rate Not Found.";disconnect($con);die;
	}
	
	$store_update_upto = str_replace("'", "", $store_update_upto);
	$cbo_floor = str_replace("'", "", $cbo_floor);
	$cbo_room = str_replace("'", "", $cbo_room);
	$txt_rack = str_replace("'", "", $txt_rack);
	$txt_shelf = str_replace("'", "", $txt_shelf);
	$cbo_bin = str_replace("'", "", $cbo_bin);
	
	if($store_update_upto > 1)
	{
		if($store_update_upto==6)
		{
			if($cbo_floor==0 || $cbo_room==0 || $txt_rack==0 || $txt_shelf==0 || $cbo_bin==0)
			{
				echo "30**Up To Bin Value Full Fill Required For Inventory";die;
			}
		}
		else if($store_update_upto==5)
		{
			if($cbo_floor==0 || $cbo_room==0 || $txt_rack==0 || $txt_shelf==0)
			{
				echo "30**Up To Shelf Value Full Fill Required For Inventory";die;
			}
			else
			{
				$cbo_bin=0;
				$cbo_bin_to=0;
			}
		}
		else if($store_update_upto==4 )
		{
			if($cbo_floor==0 || $cbo_room==0 || $txt_rack==0)
			{
				echo "30**Up To Rack Value Full Fill Required For Inventory";die;
			}
			else
			{
				$cbo_bin=0;$txt_shelf=0;
				$cbo_bin_to=0;$txt_shelf_to=0;
			}
		}
		else if($store_update_upto==3)
		{
			if($cbo_floor==0 || $cbo_room==0)
			{
				echo "30**Up To Room Value Full Fill Required For Inventory";die;
			}
			else
			{
				$cbo_bin=0;$txt_shelf=0;$txt_rack=0;
				$cbo_bin_to=0;$txt_shelf_to=0;$txt_rack_to=0;
			}
		}
		else if($store_update_upto==2)
		{
			if($cbo_floor==0)
			{
				echo "30**Up To Floor Value Full Fill Required For Inventory";die;
			}
			else
			{
				$cbo_bin=0;$txt_shelf=0;$txt_rack=0;$cbo_room=0;
				$cbo_bin_to=0;$txt_shelf_to=0;$txt_rack_to=0;$cbo_room_to=0;
			}
		}
	}
	else
	{
		$cbo_bin=0;$txt_shelf=0;$txt_rack=0;$cbo_room=0;$cbo_floor=0;
		$cbo_bin_to=0;$txt_shelf_to=0;$txt_rack_to=0;$cbo_room_to=0;$cbo_floor_to=0;
	}
	
	//######### this stock item store level and calculate rate ########//
	$store_up_conds="";
	if($uptrIssId >0) 
	{
		if($uptrRcvId >0) $store_up_conds=" and id not in($uptrIssId,$uptrRcvId)";
		else $store_up_conds=" and id not in($uptrIssId)";
	}
	
	$store_stock_sql="select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as BALANCE_STOCK, sum((case when transaction_type in(1,4,5) then store_amount else 0 end)-(case when transaction_type in(2,3,6) then store_amount else 0 end)) as BALANCE_AMT 
	from inv_transaction 
	where status_active=1 and prod_id=$hidden_product_id and store_id=$cbo_store_name $store_up_conds";
	//echo "20**$store_stock_sql";disconnect($con);die;
	$store_stock_sql_result=sql_select($store_stock_sql);
	$store_item_rate=0;
	if($store_stock_sql_result[0]["BALANCE_AMT"]!=0 && $store_stock_sql_result[0]["BALANCE_STOCK"]!=0)
	{
		$store_item_rate=$store_stock_sql_result[0]["BALANCE_AMT"]/$store_stock_sql_result[0]["BALANCE_STOCK"];
	}
	$issue_store_value=$trnsQnty*$store_item_rate;
	
	$floor_room_rack_cond="";
	if(str_replace("'","",$cbo_floor)>0) $floor_room_rack_cond .=" and floor_id=$cbo_floor";
	if(str_replace("'","",$cbo_room)>0) $floor_room_rack_cond .=" and room=$cbo_room";
	if(str_replace("'","",$txt_rack)>0) $floor_room_rack_cond .=" and rack=$txt_rack";
	if(str_replace("'","",$txt_shelf)>0) $floor_room_rack_cond .=" and self=$txt_shelf";
	if(str_replace("'","",$cbo_bin)>0) $floor_room_rack_cond .=" and bin_box=$cbo_bin";
	$sqlLotCon= "" ;
	if($variable_lot==1 && str_replace("'","",$cbo_item_category)==22)
	{
		$sqlLotCon= " and batch_lot=$txt_lot" ;
	}
	
	$trans_sql=sql_select("select sum((case when transaction_type in(1,4,5) then cons_quantity else 0 end)-(case when transaction_type in(2,3,6) then cons_quantity else 0 end)) as bal
	from inv_transaction where status_active=1 and is_deleted=0 and prod_id=$hidden_product_id and store_id=$cbo_store_name $sqlLotCon $store_up_conds $floor_room_rack_cond");
	$stockQnty=$trans_sql[0][csf("bal")]*1;
	if($trnsQnty > $stockQnty)
	{
		 echo "20**Transfer Quantity Not Allow Over Stock Quantity. $trnsQnty = $stockQnty";disconnect($con);die;
	}
	// echo $transfer_system_id=str_replace("'","",$update_id);die;
	$variable_auto_rcv = return_field_value("auto_transfer_rcv", "variable_settings_inventory", " company_name=$cbo_company_id_to and item_category_id=8 and status_active=1 and variable_list= 27", "auto_transfer_rcv");
	if($variable_auto_rcv == "")
	{
		$variable_auto_rcv = 1;
	}
	
	//echo "10**";die;
	
	$variable_store_wise_rate=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_id and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate != 1) $variable_store_wise_rate=2;
	
	$variable_store_wise_rate_to=return_field_value("auto_transfer_rcv","variable_settings_inventory","company_name=$cbo_company_id_to and variable_list=47 and item_category_id=8 and status_active=1 and is_deleted=0","auto_transfer_rcv");
	if($variable_store_wise_rate_to != 1) $variable_store_wise_rate_to=2;

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		//table lock here 
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		
		$transfer_system_id=str_replace("'","",$update_id);//die;
		
		$transfer_recv_num=''; $transfer_update_id='';

		if (str_replace("'", "", $hidden_req_id) != ""){
			$transferQuantity = return_field_value(" transfer_qnty as transfer_qnty", "inv_item_transfer_requ_dtls", "mst_id=$hidden_req_id and from_prod_id=$hidden_product_id and entry_form=494 and status_active=1", "transfer_qnty");
			$itemTransferQuantity = return_field_value(" sum(transfer_qnty) as item_transfer_qnty", "inv_item_transfer_dtls", "requisition_mst_id=$hidden_req_id and from_prod_id=$hidden_product_id and status_active=1", "item_transfer_qnty");
			//echo "10**$transferQuantity**$itemTransferQuantity";die;
			$itemTransferQuantity=$itemTransferQuantity+str_replace("'", "", $txt_transfer_qnty);
			if ($itemTransferQuantity>$transferQuantity){
				echo "20**Item Transfer Quantity Cannot Greater Than Transfer Requisition Quantity";
				disconnect($con);
	            die;
			}		
		}
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			/*$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and transfer_criteria in(1,2) and entry_form=57 and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));
		 
			$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;*/
			
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'GTE',57,date("Y",time())));
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);

			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, requisition_no, requisition_id, entry_form, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$txt_requisition_no.",".$hidden_req_id.",57,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
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
			$transfer_update_id=str_replace("'","",$update_id);
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			if($variable_auto_rcv == 2) // if auto receive yes(1), then no need to acknowledgement
			{
				//echo "10**fail=2";die;
				//$transfer_recv_num=str_replace("'","",$txt_system_id);
				//$transfer_update_id=str_replace("'","",$update_id);

				$pre_saved_store=sql_select("SELECT a.id, b.from_store, b.to_store from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id = b.mst_id and a.entry_form in(57) and a.id = $transfer_update_id and a.status_active =1 and a.is_deleted =0 and b.status_active =1 and b.is_deleted =0 group by a.id, b.from_store, b.to_store");

				if( ($pre_saved_store[0][csf("from_store")]  !=str_replace("'", "", $cbo_store_name)) || ($pre_saved_store[0][csf("to_store")]  !=str_replace("'", "", $cbo_store_name_to)) )
				{
					echo "20**Duplicate From Store and To Store is not allowed in same MRR";
					disconnect($con);
					die;
				}
			}
		}
		
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id,transaction_criteria, company_id, prod_id, item_category, transaction_type, transaction_date, store_id,floor_id,room,rack,self,bin_box, cons_uom, cons_quantity, cons_rate, cons_amount, balance_qnty, balance_amount, inserted_by, insert_date, batch_lot,store_rate,store_amount";
		
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$field_array_dtls="id, mst_id, from_prod_id, to_prod_id, item_group, from_store,floor_id,room,rack,shelf,bin_box, to_store,to_floor_id,to_room,to_rack,to_shelf,to_bin_box, item_category, transfer_qnty, rate, transfer_value, uom,serial_no, requisition_mst_id, requisition_dtls_id, trans_id, to_trans_id, inserted_by, insert_date,store_rate,store_amount";
		$field_array_dtls_ac="id, mst_id, dtls_id, is_acknowledge, from_prod_id, to_prod_id, yarn_lot, from_store, floor_id, room, rack, shelf, to_store, to_floor_id, to_room, to_rack, to_shelf, item_category, transfer_qnty, rate, transfer_value, uom, requisition_mst_id, requisition_dtls_id, remarks, inserted_by, insert_date,store_rate,store_amount";
		
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
		$data_array_store_rcv="";
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$data_prod=sql_select("select supplier_id, current_stock, avg_rate_per_unit, stock_value, item_group_id, sub_group_name, item_description, item_size, model, item_number, item_code from product_details_master where id=$hidden_product_id");
			
			$item_group_id=$data_prod[0][csf('item_group_id')];
			$sub_group_name=trim($data_prod[0][csf('sub_group_name')]);
			$item_description=trim($data_prod[0][csf('item_description')]);
			$item_size=trim($data_prod[0][csf('item_size')]);
			$model=trim($data_prod[0][csf('model')]);
			$item_number=trim($data_prod[0][csf('item_number')]);
			$item_code=trim($data_prod[0][csf('item_code')]);
			
			$presentStock=$data_prod[0][csf('current_stock')]-str_replace("'","",$txt_transfer_qnty);
			$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
			$presentStockValue=0;
			if ( $presentStock != 0 ) 
			{
				$presentAvgRate=$data_prod[0][csf('avg_rate_per_unit')];
				$presentStockValue=$presentStock*$presentAvgRate;
			}			
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=number_format($presentAvgRate,10,'.','')."*".$txt_transfer_qnty."*".$presentStock."*".number_format($presentStockValue,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$supplier_id=$data_prod[0][csf('supplier_id')];
			
			$row_prod=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id_to and item_category_id=$cbo_item_category and item_group_id=$item_group_id and trim(sub_group_name)=trim($sub_group_name) and trim(item_description)=trim($item_description) and trim(item_size)=trim($item_size) and trim(model)=trim($model) and trim(item_number)=trim($item_number) and trim(item_code)=trim($item_code) and status_active in(1,3) and is_deleted=0");
		
			if(count($row_prod)>0)
			{
				$product_id=$row_prod[0][csf('id')];
				$stock_qnty=$row_prod[0][csf('current_stock')];
				$avg_rate_per_unit=$row_prod[0][csf('avg_rate_per_unit')];
				$stock_value=$row_prod[0][csf('stock_value')];
	
				$curr_stock_qnty=$stock_qnty+str_replace("'", '',$txt_transfer_qnty);
				$curr_stock_value=0;
				$curr_avg_rate_per_unit=$avg_rate_per_unit;
				if ( $curr_stock_qnty != 0 ){
					$curr_stock_value=$stock_value+str_replace("'", '',$txt_transfer_value);
					$curr_avg_rate_per_unit=$curr_stock_value/$curr_stock_qnty;
				} 
								
				$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
				if($variable_auto_rcv==1)
				{
					$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
					$data_array_prod_update=number_format($curr_avg_rate_per_unit,10,'.','')."*".$txt_transfer_qnty."*".$curr_stock_qnty."*".number_format($curr_stock_value,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
			}
			else
			{
				//$product_id=return_next_id( "id", "product_details_master", 1 ) ;
				$product_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$curr_stock_qnty=str_replace("'","",$txt_transfer_qnty);				
				
				if($variable_auto_rcv==1)
				{
					$stock_value=0;
					$avg_rate_per_unit=$data_prod[0][csf('avg_rate_per_unit')];
					if ( $curr_stock_qnty != 0 ) {						
						$stock_value=$curr_stock_qnty*$avg_rate_per_unit;
					}
					$sql_prod_insert="insert into product_details_master(id,company_id,supplier_id,item_category_id,detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details,lot,item_code,unit_of_measure,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,yarn_count_id,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color,gsm,dia_width,brand,item_size,model,item_number,inserted_by,insert_date,entry_form,order_uom,conversion_factor,item_sub_group_id) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, $txt_transfer_qnty, $curr_stock_qnty,".number_format($stock_value,8,'.','').", yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, item_size, model, item_number, '$user_id', '$pc_date_time', entry_form, order_uom, conversion_factor, item_sub_group_id from product_details_master where id=$hidden_product_id";
				}
				else
				{
					$sql_prod_insert="insert into product_details_master(id,company_id,supplier_id,item_category_id,detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, product_name_details,lot,item_code,unit_of_measure,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,yarn_count_id,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color,gsm,dia_width,brand,item_size,model,item_number,inserted_by,insert_date,entry_form,order_uom,conversion_factor,item_sub_group_id) 
					select	
					'$product_id', $cbo_company_id_to, supplier_id, item_category_id, detarmination_id,sub_group_code,sub_group_name,item_group_id,item_description, trim(product_name_details), lot, item_code, unit_of_measure, avg_rate_per_unit, 0, 0, 0, yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, color, gsm, dia_width, brand, item_size, model, item_number, '$user_id', '$pc_date_time', entry_form, order_uom, conversion_factor, item_sub_group_id from product_details_master where id=$hidden_product_id";
				}
			}


			 //----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
            $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and store_id = $cbo_store_name  and status_active = 1", "max_date");      
            if($max_recv_date !="")
            {
                $max_recv_date = strtotime($max_recv_date);
                $transfer_date = strtotime(str_replace("'", "", $txt_transfer_date));

                if ($transfer_date < $max_recv_date) 
                {
                    echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";disconnect($con);die;
                }
            }


            //----------------Check Last Issue Date for Transfer In---------------->>>>>>>
            $max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$product_id and transaction_type in (2,3,6) and status_active = 1", "max_date");      
            if($max_transaction_date !="")
            {
                $max_transaction_date = strtotime($max_transaction_date);
                $transfer_date = strtotime(str_replace("'", "", $txt_transfer_date));

                if ($transfer_date < $max_transaction_date) 
                {
                    echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Item";disconnect($con);die;
                }
            }

            //---------------Check Duplicate product in Same return number ------------------------//
            /*echo "SELECT b.id FROM inv_item_transfer_mst a, inv_transaction b WHERE a.id=b.mst_id and a.id=$transfer_system_id and b.prod_id=$hidden_product_id and b.transaction_type=6  and a.status_active=1 and b.status_active=1";*/
            if ($transfer_system_id!='') 
            {
            	$duplicate = is_duplicate_field("b.id","inv_item_transfer_mst a, inv_transaction b","a.id=b.mst_id and a.id=$transfer_system_id and b.prod_id=$hidden_product_id and b.transaction_type=6  and a.status_active=1 and b.status_active=1"); 
				if($duplicate==1) 
				{
					echo "20**Duplicate Product is Not Allow in Same System ID.";disconnect($con);die;
				}
            }	        
			//------------------------------Check product END---------------------------------------//
			
			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_uom.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$txt_transfer_qnty.",".number_format($txt_transfer_value,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			
			//$recv_trans_id=$id_trans+1;
			//echo "10**".$variable_auto_rcv;die;
			$recv_trans_id=0;
			if($variable_auto_rcv==1)
			{
				$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_uom.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$txt_transfer_qnty.",".number_format($txt_transfer_value,8,'.','').",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			}
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$product_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$cbo_uom.",".$txt_serial_no.",".$hidden_req_id.",".$hidden_req_dtls_id.",".$id_trans.",".$recv_trans_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")"; 
			
			//echo "10**".$data_array_dtls;die;
			
			if($variable_auto_rcv==2) // acknowledgement_dtls_table
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$hidden_product_id.",".$product_id.",".$txt_lot.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$cbo_uom.",".$hidden_req_id.",".$hidden_req_dtls_id.",'".$txt_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			}
			
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
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name order by id $cond_lifofifo");
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",57,".$hidden_product_id.",".$transfer_qnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",57,".$hidden_product_id.",".$balance_qnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
			
			if($variable_store_wise_rate==1)
			{
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
				$store_up_id=0;
				if(count($sql_store)<1)
				{
					echo "20**No Data Found.";disconnect($con);die;
				}
				elseif(count($sql_store)>1)
				{
					echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
				}
				else
				{
					$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
					foreach($sql_store as $result)
					{
						$store_up_id=$result[csf("id")];
						$store_presentStock	=$result[csf("current_stock")];
						$store_presentStockValue =$result[csf("stock_value")];
						$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
					}
					
					$currentStock_store		=$store_presentStock-$trnsQnty;
					$currentValue_store		=$store_presentStockValue-$issue_store_value;
					if($store_up_id)
					{
						$store_id_arr[]=$store_up_id;
						$data_array_store[$store_up_id]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					//"".$txt_transfer_qnty."*".$currentStock_store."*".$currentValue_store."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
				}
			}
			
			if($variable_auto_rcv==1 && $variable_store_wise_rate==1 && $variable_store_wise_rate_to=1)
			{
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$product_id and category_id=$cbo_item_category and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
				$store_up_id_to=0;
				if(count($sql_store_rcv)<1)
				{
					$field_array_store_rcv="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";
				
					$sdtlsid = return_next_id( "id", "inv_store_wise_gen_qty_dtls", 1 );
					$data_array_store_rcv= "(".$sdtlsid.",".$cbo_company_id_to.",".$cbo_store_name_to.",".$cbo_item_category.",".$product_id.",".$txt_transfer_qnty.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').",".$txt_transfer_qnty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',".$txt_lot.",".$txt_transfer_date.",".$txt_transfer_date.")";
				}
				elseif(count($sql_store_rcv)>1)
				{
					echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
				}
				else
				{
					$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
					foreach($sql_store_rcv as $result)
					{
						$store_up_id_to=$result[csf("id")];
						$store_presentStock	=$result[csf("current_stock")];
						$store_presentStockValue =$result[csf("stock_value")];
						$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
					}
					$currentStock_store		=$store_presentStock+$trnsQnty;
					$currentValue_store		=$store_presentStockValue+$issue_store_value;
					if($store_up_id_to)
					{
						$store_id_arr[]=$store_up_id_to;
						$data_array_store[$store_up_id_to]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
				}
			}
		}
		else
		{
			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
            $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and status_active = 1", "max_date");      
            if($max_recv_date !="")
            {
                $max_recv_date = strtotime($max_recv_date);
                $transfer_date = strtotime(str_replace("'", "", $txt_transfer_date));

                if ($transfer_date < $max_recv_date) 
                {
                    echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
					disconnect($con);
                    die;
                }
            }
			
			$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (2,3,6) and status_active = 1", "max_date");      
			if($max_transaction_date !="")
			{
				$max_transaction_date = strtotime($max_transaction_date);
				$transfer_date = strtotime(str_replace("'", "", $txt_transfer_date));

				if ($transfer_date < $max_transaction_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}

            //---------------Check Duplicate product in Same return number ------------------------//
            /*echo "SELECT b.id FROM inv_item_transfer_mst a, inv_transaction b WHERE a.id=b.mst_id and a.id=$transfer_system_id and b.prod_id=$hidden_product_id and b.transaction_type=5  and a.status_active=1 and b.status_active=1";*/
            if ($transfer_system_id!='') 
            {
            	$duplicate = is_duplicate_field("b.id","inv_item_transfer_mst a, inv_transaction b","a.id=b.mst_id and a.id=$transfer_system_id and b.prod_id=$hidden_product_id and b.transaction_type=5  and a.status_active=1 and b.status_active=1"); 
				if($duplicate==1) 
				{
					echo "20**Duplicate Product is Not Allow in Same System ID.";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
            }	        
			//------------------------------Check product END---------------------------------------//


			$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",6,".$txt_transfer_date.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_uom.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",0,0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			
			$recv_trans_id=0;
			if($variable_auto_rcv==1)
			{
				$recv_trans_id = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$recv_trans_id.",".$transfer_update_id.",".$cbo_transfer_criteria.",".$cbo_company_id.",".$hidden_product_id.",".$cbo_item_category.",5,".$txt_transfer_date.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_uom.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$txt_transfer_qnty.",".$txt_transfer_value.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$txt_lot.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			}
			
			
			$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$hidden_product_id.",".$hidden_product_id.",0,".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_bin.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_bin_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$cbo_uom.",".$txt_serial_no.",".$hidden_req_id.",".$hidden_req_dtls_id.",".$id_trans.",".$recv_trans_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			
			if($variable_auto_rcv==2) // acknowledgement_dtls_table
			{
				$id_dtls_ac = return_next_id_by_sequence("INV_ITEM_TRANS_DTLS_AC_PK_SEQ", "inv_item_transfer_dtls_ac", $con);
				$data_array_dtls_ac="(".$id_dtls_ac.",".$transfer_update_id.",".$id_dtls.",0,".$hidden_product_id.",".$hidden_product_id.",".$txt_lot.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$cbo_item_category.",".$txt_transfer_qnty.",".number_format($txt_rate,10,'.','').",".number_format($txt_transfer_value,8,'.','').",".$cbo_uom.",".$hidden_req_id.",".$hidden_req_dtls_id.",'".$txt_remarks."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').")";
			}
			
			$store_up_id=0;
			if($variable_store_wise_rate==1)
			{
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
				
				if(count($sql_store)<1)
				{
					echo "20**No Data Found.";disconnect($con);die;
				}
				elseif(count($sql_store)>1)
				{
					echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
				}
				else
				{
					$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
					foreach($sql_store as $result)
					{
						$store_up_id=$result[csf("id")];
						$store_presentStock	=$result[csf("current_stock")];
						$store_presentStockValue =$result[csf("stock_value")];
						$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
					}
					
					$currentStock_store		=$store_presentStock-$trnsQnty;
					$currentValue_store		=$store_presentStockValue-$issue_store_value;
					if($store_up_id)
					{
						$store_id_arr[]=$store_up_id;
						$data_array_store[$store_up_id]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					//"".$txt_transfer_qnty."*".$currentStock_store."*".$currentValue_store."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'";
				}
			}
			
			$store_up_id_to=0;
			if($variable_auto_rcv==1 && $variable_store_wise_rate==1 && $variable_store_wise_rate_to==1)
			{
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name_to and company_id=$cbo_company_id");
				
				if(count($sql_store_rcv)<1)
				{
					$field_array_store_rcv="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";
				
					$sdtlsid = return_next_id( "id", "inv_store_wise_gen_qty_dtls", 1 );
					$data_array_store_rcv= "(".$sdtlsid.",".$cbo_company_id.",".$cbo_store_name_to.",".$cbo_item_category.",".$hidden_product_id.",".$txt_transfer_qnty.",".number_format($store_item_rate,10,'.','').",".number_format($issue_store_value,8,'.','').",".$txt_transfer_qnty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."',".$txt_lot.",".$txt_transfer_date.",".$txt_transfer_date.")";
				}
				elseif(count($sql_store_rcv)>1)
				{
					echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
				}
				else
				{
					$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
					foreach($sql_store_rcv as $result)
					{
						$store_up_id_to=$result[csf("id")];
						$store_presentStock	=$result[csf("current_stock")];
						$store_presentStockValue =$result[csf("stock_value")];
						$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
					}
					$currentStock_store		=$store_presentStock+$trnsQnty;
					$currentValue_store		=$store_presentStockValue+$issue_store_value;
					if($store_up_id_to)
					{
						$store_id_arr[]=$store_up_id_to;
						$data_array_store[$store_up_id_to]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
				}
			}
			
			
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
			
			$sql = sql_select("select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name order by id $cond_lifofifo");
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",57,".$hidden_product_id.",".$transfer_qnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$id_trans.",57,".$hidden_product_id.",".$balance_qnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
		}
		
		
		//insert update all queries
		$rID=$rID2=$rID3=$rID4=$prodUpdate=$prod=$mrrWiseIssueID=$upTrID=true;
		//echo "10**insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";die;
		if(str_replace("'","",$update_id)=="")
		{
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		}
		//echo "10**insert into inv_transaction ($field_array_trans) values $data_array_trans";oci_rollback($con);disconnect($con);die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		//echo "10**insert into inv_item_transfer_dtls ($field_array_dtls) values $data_array_dtls";oci_rollback($con);disconnect($con);die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
		}
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
		}
		
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if(count($row_prod)>0)
			{
				if($variable_auto_rcv==1) 
				{
					$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
				}
			}
			else
			{
				$prod=execute_query($sql_prod_insert,0);
			}
		}
		
		if($variable_auto_rcv==2) // inv_item_transfer_dtls_ac
		{
			//echo "10**insert into inv_item_transfer_dtls_ac (".$field_array_dtls_ac.") values ".$data_array_dtls_ac;oci_rollback($con);disconnect($con);die;
			$rID4=sql_insert("inv_item_transfer_dtls_ac",$field_array_dtls_ac,$data_array_dtls_ac,0);
		}
		
		$storeUpID=$storeInsID=true;
		if(count($store_id_arr)>0 && $variable_store_wise_rate==1)
		{
			$storeUpID=execute_query(bulk_update_sql_statement("inv_store_wise_gen_qty_dtls","id",$field_array_store,$data_array_store,$store_id_arr));
		}
		if($data_array_store_rcv!="" && $variable_store_wise_rate==1 && $variable_store_wise_rate_to==1)
		{
			$storeInsID=sql_insert("inv_store_wise_gen_qty_dtls",$field_array_store_rcv,$data_array_store_rcv,1); 
		}
		
		//echo "10**$rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID && $storeUpID && $storeInsID";oci_rollback($con);disconnect($con);die;
		
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID && $storeUpID && $storeInsID)
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
			if($rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prod && $mrrWiseIssueID && $upTrID && $storeUpID && $storeInsID)
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
	else if($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		//table lock here 
		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		if($variable_auto_rcv==2)
		{
			$is_acknoledges = return_field_value("is_acknowledge", "inv_item_transfer_dtls_ac", "dtls_id=$update_dtls_id and status_active = 1", "is_acknowledge");
			if($is_acknoledges ==1)
			{
				echo "20**This Transaction Already Acknowledged";disconnect($con);die;
			}
		}
		
		if(str_replace("'","",$update_trans_recv_id)>0)
		{
			$max_transaction_id = return_field_value("max(id) as max_trans_id", "inv_transaction", "prod_id=$hidden_product_id and mst_id<>$update_id and transaction_type in(2,3,6) and status_active = 1", "max_trans_id");
			if($max_transaction_id > str_replace("'","",$update_trans_recv_id))
			{
				echo "20**Next Transaction Found, Update Not Allow $max_transaction_id = $update_trans_recv_id";disconnect($con);die;
			}
		}

		$field_array_update="challan_no*transfer_date*to_company*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$cbo_company_id_to."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		if (str_replace("'", "", $hidden_req_id) != ""){
			$transferQuantity = return_field_value(" transfer_qnty as transfer_qnty", "inv_item_transfer_requ_dtls", "mst_id=$hidden_req_id and from_prod_id=$hidden_product_id and entry_form=494 and status_active=1", "transfer_qnty");
			$itemTransferQuantity = return_field_value(" sum(transfer_qnty) as item_transfer_qnty", "inv_item_transfer_dtls", "mst_id=$hidden_req_id and from_prod_id=$hidden_product_id and status_active=1", "item_transfer_qnty");
			$itemTransferQuantity=$itemTransferQuantity+str_replace("'", "", $txt_transfer_qnty);
			if ($itemTransferQuantity>$transferQuantity){
				echo "20**Item Transfer Quantity Cannot Greater Than Transfer Requisition Quantity";
				disconnect($con);
	            die;
			}		
		}

		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0; */
	
		$field_array_trans="prod_id*transaction_date*store_id*floor_id*room*rack*self*bin_box*cons_uom*cons_quantity*cons_rate*cons_amount*balance_qnty*balance_amount*updated_by*update_date*batch_lot*store_rate*store_amount";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id); 
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 

		$field_array_dtls="from_prod_id*to_prod_id*from_store*floor_id*room*rack*shelf*bin_box*to_store*to_floor_id*to_room*to_rack*to_shelf*to_bin_box*item_category*transfer_qnty*rate*transfer_value*uom*serial_no*updated_by*update_date*store_rate*store_amount";
		$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
		$product_id=str_replace("'","",$previous_to_prod_id);
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and id <> $update_trans_recv_id  and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = strtotime($max_recv_date);
				$transfer_date = strtotime(str_replace("'", "", $txt_transfer_date));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			
			 //----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$previous_to_prod_id and transaction_type in (2,3,6) and id not in ($update_trans_recv_id, $update_trans_recv_id )  and status_active = 1", "max_date");      
			if($max_issue_date !="")
			{
				$max_issue_date = strtotime($max_issue_date);
				$transfer_date = strtotime(str_replace("'", "", $txt_transfer_date));

				if ($transfer_date < $max_issue_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Lot";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			
			$stock_from=sql_select("select a.current_stock, a.avg_rate_per_unit, a.stock_value, b.transfer_qnty, b.transfer_value, b.store_rate, b.store_amount 
			from product_details_master a, inv_item_transfer_dtls b 
			where a.id=b.FROM_PROD_ID and a.id=$previous_from_prod_id and b.id=$update_dtls_id and a.status_active in(1,3) and b.status_active=1");
			
			$prev_transfer_qnty=$stock_from[0][csf('transfer_qnty')];
			$prev_transfer_value=$stock_from[0][csf('transfer_value')];
			$prev_store_rate=$stock_from[0][csf('store_rate')];
			$prev_store_amount=$stock_from[0][csf('store_amount')];		
			
			$presentStock_form=$stock_from[0][csf('current_stock')]+$prev_transfer_qnty-$trnsQnty;
			$presentStockValue_from=$stock_from[0][csf('stock_value')]+$prev_transfer_value-$trnsValue;
			$presentAvgRate_form=0;
			if($presentStock_form!=0 && $presentStockValue_from!=0) $presentAvgRate_form=$presentStockValue_from/$presentStock_form;			
			
			$field_array_prodUpdate="avg_rate_per_unit*last_issued_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prodUpdate=number_format($presentAvgRate_form,10,'.','')."*".$txt_transfer_qnty."*".$presentStock_form."*".number_format($presentStockValue_from,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			
			if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$stock_to=sql_select("select current_stock, avg_rate_per_unit, stock_value from product_details_master where id=$previous_to_prod_id");
				$presentStock_to=($stock_to[0][csf('current_stock')]-$prev_transfer_qnty)+$trnsQnty;
				$presentStockValue_to=($stock_to[0][csf('stock_value')]-$prev_transfer_value)+$trnsValue;
				$presentAvgRate_to=0;			
				if($presentStock_to!=0 && $presentStockValue_to!=0) $presentAvgRate_to=$presentStockValue_to/$presentStock_to;
				
				$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
				$data_array_prod_update=number_format($presentAvgRate_to,10,'.','')."*".$txt_transfer_qnty."*".$presentStock_to."*".number_format($presentStockValue_to,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			}
			
			if($variable_store_wise_rate==1)
			{
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$previous_from_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
				$store_up_id=0;
				if(count($sql_store)<1)
				{
					echo "20**No Data Found.";disconnect($con);die;
				}
				elseif(count($sql_store)>1)
				{
					echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
				}
				else
				{
					$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
					foreach($sql_store as $result)
					{
						$store_up_id=$result[csf("id")];
						$store_presentStock	=$result[csf("current_stock")];
						$store_presentStockValue =$result[csf("stock_value")];
						$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
					}
					
					$currentStock_store		=$store_presentStock+$prev_transfer_qnty-$trnsQnty;
					$currentValue_store		=$store_presentStockValue+$prev_store_amount-$issue_store_value;
					if($store_up_id)
					{
						$store_id_arr[]=$store_up_id;
						$data_array_store[$store_up_id]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
				}
			}
			
			if($variable_auto_rcv==1 && $variable_store_wise_rate==1 && $variable_store_wise_rate_to==1)
			{
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$previous_to_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
				$store_up_id_to=0;
				if(count($sql_store_rcv)<1)
				{
					echo "20**No Data Found.";disconnect($con);die;
				}
				elseif(count($sql_store_rcv)>1)
				{
					echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
				}
				else
				{
					$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
					foreach($sql_store_rcv as $result)
					{
						$store_up_id_to=$result[csf("id")];
						$store_presentStock	=$result[csf("current_stock")];
						$store_presentStockValue =$result[csf("stock_value")];
						$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
					}
					$currentStock_store		=($store_presentStock-$prev_transfer_qnty)+$trnsQnty;
					$currentValue_store		=($store_presentStockValue-$prev_store_amount)+$issue_store_value;
					if($store_up_id_to)
					{
						$store_id_arr[]=$store_up_id_to;
						$data_array_store[$store_up_id_to]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
				}
			}

			
			$updateTransID_array[]=$update_trans_issue_id;
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_uom."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			if($variable_auto_rcv == 1 )
			{
				$updateTransID_array[]=$update_trans_recv_id; 
				$updateTransID_data[$update_trans_recv_id]=explode("*",("'".$product_id."'*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$cbo_uom."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*".$txt_transfer_qnty."*".number_format($txt_transfer_value,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			}
			
			$data_array_dtls=$hidden_product_id."*".$product_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$cbo_item_category."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*".$cbo_uom."*".$txt_serial_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','')."";
			
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

			//transaction table END----------------------------//
			
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();			
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);			
			$sql_trans = "select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name 
			union all
			select a.id, b.rate cons_rate, b.issue_qnty as balance_qnty, b.amount as balance_amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=57 and a.item_category=$cbo_item_category
			order by id $cond_lifofifo";
			//echo "10**$sql_trans";die;
			$sql = sql_select($sql_trans);
			$mrr_bal_trans_data=array();
			foreach($sql as $val)
			{
				$mrr_bal_trans_data[$val[csf("id")]]["ID"]=$val[csf("id")];
				$mrr_bal_trans_data[$val[csf("id")]]["CONS_RATE"]=$val[csf("cons_rate")];
				$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_QNTY"]+=$val[csf("balance_qnty")];
				$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_AMOUNT"]+=$val[csf("balance_amount")];
			}
			//echo "10**<pre>";print_r($mrr_bal_trans_data);die;
			$p=1;
			foreach($mrr_bal_trans_data as $result)
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
					
					if($p>1)
					{
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("".$result[csf("balance_qnty")]."*".$result[csf("balance_amount")]."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					else
					{
						$amount = $transfer_qnty*$cons_rate;
						//for insert
						if($data_array_mrr!="") $data_array_mrr .= ",";  
						$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",55,".$hidden_product_id.",".$transfer_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					$p++;
					//break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",55,".$hidden_product_id.",".$balance_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
			
			//LIFO/FIFO Start-----------------------------------------------//
			/*$isLIFOfifo=return_field_value("store_method","variable_settings_inventory","company_name=$cbo_company_id and variable_list=17");
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",57,".$hidden_product_id.",".$transfer_qnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
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
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",57,".$hidden_product_id.",".$balance_qnty.",".number_format($cons_rate,10,'.','').",".number_format($amount,8,'.','').",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach*/
			
 			// LIFO/FIFO END-----------------------------------------------//
		}
		else
		{
			//----------------Check Last Receive Date for Transfer Out---------------->>>>>>>
			$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (1,4,5) and id <> $update_trans_recv_id and status_active = 1", "max_date");      
			if($max_recv_date !="")
			{
				$max_recv_date = strtotime($max_recv_date);
				$transfer_date = strtotime(str_replace("'", "", $txt_transfer_date));

				if ($transfer_date < $max_recv_date) 
				{
					echo "20**Transfer Out Date Can not Be Less Than Last Receive Date Of This Item";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			
			 //----------------Check Last Issue Date for Transfer In---------------->>>>>>>
			$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$hidden_product_id and transaction_type in (2,3,6) and id not in ($update_trans_recv_id , $update_trans_issue_id) and status_active = 1", "max_date");      
			if($max_transaction_date !="")
			{
				$max_transaction_date = strtotime($max_transaction_date);
				$transfer_date = strtotime(str_replace("'", "", $txt_transfer_date));

				if ($transfer_date < $max_transaction_date) 
				{
					echo "20**Transfer In Date Can not Be Less Than Last Transaction Date Of This Item";
					//check_table_status( $_SESSION['menu_id'],0);
					disconnect($con);
					die;
				}
			}
			
			$stock_from=sql_select("select a.current_stock, a.avg_rate_per_unit, a.stock_value, b.transfer_qnty, b.transfer_value, b.store_rate, b.store_amount 
			from product_details_master a, inv_item_transfer_dtls b 
			where a.id=b.FROM_PROD_ID and a.id=$hidden_product_id and b.id=$update_dtls_id and a.status_active in(1,3) and b.status_active=1");
			
			$prev_transfer_qnty=$stock_from[0][csf('transfer_qnty')];
			$prev_transfer_value=$stock_from[0][csf('transfer_value')];
			$prev_store_rate=$stock_from[0][csf('store_rate')];
			$prev_store_amount=$stock_from[0][csf('store_amount')];
			if($variable_store_wise_rate==1)
			{
				$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
				$store_up_id=0;
				if(count($sql_store)<1)
				{
					echo "20**No Data Found.";disconnect($con);die;
				}
				elseif(count($sql_store)>1)
				{
					echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
				}
				else
				{
					$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
					foreach($sql_store as $result)
					{
						$store_up_id=$result[csf("id")];
						$store_presentStock	=$result[csf("current_stock")];
						$store_presentStockValue =$result[csf("stock_value")];
						$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
					}
					
					$currentStock_store		=$store_presentStock+$prev_transfer_qnty-$trnsQnty;
					$currentValue_store		=$store_presentStockValue+$prev_store_amount-$issue_store_value;
					if($store_up_id)
					{
						$store_id_arr[]=$store_up_id;
						$data_array_store[$store_up_id]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
				}
			}
			
			if($variable_auto_rcv==1 && $variable_store_wise_rate==1 && $variable_store_wise_rate_to==1)
			{
				$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$hidden_product_id and category_id=$cbo_item_category and store_id=$cbo_store_name_to and company_id=$cbo_company_id");
				$store_up_id_to=0;
				if(count($sql_store_rcv)<1)
				{
					echo "20**No Data Found.";disconnect($con);die;
				}
				elseif(count($sql_store_rcv)>1)
				{
					echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
				}
				else
				{
					$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
					foreach($sql_store_rcv as $result)
					{
						$store_up_id_to=$result[csf("id")];
						$store_presentStock	=$result[csf("current_stock")];
						$store_presentStockValue =$result[csf("stock_value")];
						$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
					}
					$currentStock_store		=($store_presentStock-$prev_transfer_qnty)+$trnsQnty;
					$currentValue_store		=($store_presentStockValue-$prev_store_amount)+$issue_store_value;
					if($store_up_id_to)
					{
						$store_id_arr[]=$store_up_id_to;
						$data_array_store[$store_up_id_to]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
				}
			}

			$updateTransID_array[]=$update_trans_issue_id; 
			$updateTransID_data[$update_trans_issue_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_uom."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*'0'*'0'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			if($variable_auto_rcv == 1 ) // if auto receive Yes(1), then no need to acknowledgement
			{
				$updateTransID_array[]=$update_trans_recv_id; 
				$updateTransID_data[$update_trans_recv_id]=explode("*",("".$hidden_product_id."*".$txt_transfer_date."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$cbo_uom."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*".$txt_transfer_qnty."*".number_format($txt_transfer_value,8,'.','')."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$txt_lot."*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','').""));
			}
			
			$data_array_dtls=$hidden_product_id."*".$hidden_product_id."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_bin."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$cbo_bin_to."*".$cbo_item_category."*".$txt_transfer_qnty."*".number_format($txt_rate,10,'.','')."*".number_format($txt_transfer_value,8,'.','')."*".$cbo_uom."*".$txt_serial_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".number_format($store_item_rate,10,'.','')."*".number_format($issue_store_value,8,'.','')."";
			
			$transfer_qnty = str_replace("'","",$txt_transfer_qnty);
			$transfer_value = str_replace("'","",$txt_transfer_value);
			$field_array_mrr= "id,recv_trans_id,issue_trans_id,entry_form,prod_id,issue_qnty,rate,amount,inserted_by,insert_date";
			$update_array = "balance_qnty*balance_amount*updated_by*update_date";
			$updateID_array=array();
			$update_data=array();			
			//$mrrWiseIsID = return_next_id("id", "inv_mrr_wise_issue_details", 1);			
			$sql_trans = "select id, cons_rate, balance_qnty, balance_amount from inv_transaction where prod_id=$hidden_product_id and balance_qnty>0 and transaction_type in (1,4,5) and item_category=$cbo_item_category and store_id=$cbo_store_name 
			union all
			select a.id, b.rate cons_rate, b.issue_qnty as balance_qnty, b.amount as balance_amount from inv_transaction a, inv_mrr_wise_issue_details b where a.id=b.recv_trans_id and b.issue_trans_id=$update_trans_issue_id and b.entry_form=57 and a.item_category=$cbo_item_category
			order by id $cond_lifofifo";
			//echo "10**$sql_trans";die;
			$sql = sql_select($sql_trans);
			$mrr_bal_trans_data=array();
			foreach($sql as $val)
			{
				$mrr_bal_trans_data[$val[csf("id")]]["ID"]=$val[csf("id")];
				$mrr_bal_trans_data[$val[csf("id")]]["CONS_RATE"]=$val[csf("cons_rate")];
				$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_QNTY"]+=$val[csf("balance_qnty")];
				$mrr_bal_trans_data[$val[csf("id")]]["BALANCE_AMOUNT"]+=$val[csf("balance_amount")];
			}
			//echo "10**<pre>";print_r($mrr_bal_trans_data);die;
			$p=1;
			foreach($mrr_bal_trans_data as $result)
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
					
					if($p>1)
					{
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("".$result[csf("balance_qnty")]."*".$result[csf("balance_amount")]."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					else
					{
						$amount = $transfer_qnty*$cons_rate;
						//for insert
						if($data_array_mrr!="") $data_array_mrr .= ",";  
						$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",55,".$hidden_product_id.",".$transfer_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
						//for update
						$updateID_array[]=$recv_trans_id; 
						$update_data[$recv_trans_id]=explode("*",("".$transferQntyBalance."*".$transferStockBalance."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					}
					$p++;
					//break;
				}
				else if($transferQntyBalance<0)
				{
					$transferQntyBalance = $transfer_qnty-$balance_qnty;				
					$transfer_qnty = $balance_qnty;				
					$amount = $transfer_qnty*$cons_rate;
					
					//for insert
					if($data_array_mrr!="") $data_array_mrr .= ",";  
					$data_array_mrr .= "(".$mrrWiseIsID.",".$recv_trans_id.",".$update_trans_issue_id.",55,".$hidden_product_id.",".$balance_qnty.",".number_format($cons_rate,10,".","").",".number_format($amount,8,".","").",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
					//for update
					$updateID_array[]=$recv_trans_id; 
					$update_data[$recv_trans_id]=explode("*",("0*0*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
					$transfer_qnty = $transferQntyBalance;
				}
				//$mrrWiseIsID++;
			}//end foreach
			
		}
		
		
		$rID=$rID2=$rID3=$rID4=$prodUpdate=$prod=$query=$query2=$mrrWiseIssueID=$upTrID=$storeUpID=true;
		//all update and insert operation
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($data_array_mrr!="")
		{		
			$mrrWiseIssueID=sql_insert("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,1);
		}
		//transaction table stock update here------------------------//
		if(count($updateID_array)>0)
		{
			$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
		}
			
		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$prodUpdate=sql_update("product_details_master",$field_array_prodUpdate,$data_array_prodUpdate,"id",$hidden_product_id,1);
			if($variable_auto_rcv==1) 
			{
				$prod=sql_update("product_details_master",$field_array_prod_update,$data_array_prod_update,"id",$product_id,0);
			}
			if(count($updateID_array)>0)
			{
				$query=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array));
				$query2 = execute_query("DELETE FROM inv_mrr_wise_issue_details WHERE issue_trans_id=$update_trans_issue_id and entry_form=57 ");
			}
		}
		
		if($variable_auto_rcv==2) //acknowledgement details table update, 
		{
			$rID4=sql_update("inv_item_transfer_dtls_ac",$field_array_dtls,$data_array_dtls,"dtls_id",$update_dtls_id,1);	
		}
		
		if(count($store_id_arr)>0 && $variable_store_wise_rate==1)
		{
			$storeUpID=execute_query(bulk_update_sql_statement("inv_store_wise_gen_qty_dtls","id",$field_array_store,$data_array_store,$store_id_arr));
		}
		
		//echo "10**$rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID && $storeUpID";oci_rollback($con);disconnect($con);die;

		//echo $upTrID;die;
		if($db_type==0)
		{
			if($rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID && $storeUpID)
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
			
			if($rID && $rID2 && $rID3 && $rID4 && $prodUpdate && $prod && $query && $query2 && $mrrWiseIssueID && $upTrID && $storeUpID)
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
		$update_trans_issue_id = str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id = str_replace("'","",$update_trans_recv_id);
		$all_trans_id=$update_trans_issue_id.",".$update_trans_recv_id;
		$previous_from_prod_id = str_replace("'","",$previous_from_prod_id);
		$previous_to_prod_id=$update_trans_issue_id.",".$previous_to_prod_id;
		
		$all_prod_id=$previous_from_prod_id.",".$previous_to_prod_id;
		
		if($variable_auto_rcv==2)
		{
			$is_acknoledges = return_field_value("is_acknowledge", "inv_item_transfer_dtls_ac", "dtls_id=$update_dtls_id and status_active = 1", "is_acknowledge");
			if($is_acknoledges ==1)
			{
				echo "20**This Transaction Already Acknowledged";disconnect($con);die;
			}
		}
		
		$chk_next_transaction=return_field_value("id","inv_transaction"," status_active=1 and is_deleted=0 and prod_id in ($all_prod_id) and transaction_type in(2,3,6) and id >$update_trans_recv_id ","id");
		if($chk_next_transaction !="")
		{ 
			echo "17**Delete not allowed.This item is used in another transaction"; disconnect($con);die;
		}
		
		if($mst_id=="" || $mst_id==0 || $update_trans_issue_id=="" || $update_trans_issue_id==0 || $update_trans_recv_id=="" || $update_trans_recv_id==0)
		{ 
			echo "16**Delete not allowed. Problem occurred"; disconnect($con);die;
		}
		else 
		{
			$update_id = str_replace("'","",$update_dtls_id);
			$product_id = str_replace("'","",$hidden_product_id);
			if( str_replace("'","",$update_dtls_id) == "" )
			{
				echo "16**Delete not allowed. Problem occurred"; disconnect($con);die(); 
			}
			$field_array_store="last_issued_qnty*cons_qty*amount*updated_by*update_date";
			$rID1=$rID2=$rID3=$storeUpID=true;
			if(str_replace("'","",$cbo_transfer_criteria)==2)
			{//echo "10**nazim"; die;
				$sql = sql_select("select id from inv_transaction where status_active=1 and is_deleted=0 and mst_id=$mst_id and prod_id=$product_id and transaction_type in(5,6) and id in($all_trans_id)");
				foreach( $sql as $row)
				{
					$trans_ids .= $row[csf("id")].","; 	
				}
				
				$issue_prod_sql = sql_select("select a.current_stock, a.avg_rate_per_unit, a.stock_value, b.transfer_qnty, b.transfer_value, b.store_rate, b.store_amount 
				from product_details_master a, inv_item_transfer_dtls b  
				where a.id=b.FROM_PROD_ID and a.id=$product_id and b.id=$update_dtls_id and a.status_active in(1,3) and b.status_active=1");					
				$beforeStock=$beforeStockValue=0; 
				foreach( $issue_prod_sql as $row)
				{
					$beforeStock			=$row[csf("current_stock")];
					$beforeStockValue		=$row[csf("stock_value")];
					$before_transfer_qnty	=$row[csf("transfer_qnty")];
					$before_transfer_value	=$row[csf("transfer_value")];
					$before_store_amount	=$row[csf("store_amount")];
				}
				
				if($variable_store_wise_rate==1)
				{
					$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$product_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
					$store_up_id=0;
					if(count($sql_store)<1)
					{
						echo "20**No Data Found.";disconnect($con);die;
					}
					elseif(count($sql_store)>1)
					{
						echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
					}
					else
					{
						$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
						foreach($sql_store as $result)
						{
							$store_up_id=$result[csf("id")];
							$store_presentStock	=$result[csf("current_stock")];
							$store_presentStockValue =$result[csf("stock_value")];
							$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
						}
						
						$currentStock_store		=$store_presentStock+$before_transfer_qnty;
						$currentValue_store		=$store_presentStockValue+$before_store_amount;
						if($store_up_id)
						{
							$store_id_arr[]=$store_up_id;
							$data_array_store[$store_up_id]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						}
					}
				}
				
				if($variable_store_wise_rate==1 && $variable_store_wise_rate_to==1)
				{
					$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$product_id and category_id=$cbo_item_category and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
					$store_up_id_to=0;
					if(count($sql_store_rcv)<1)
					{
						echo "20**No Data Found.";disconnect($con);die;
					}
					elseif(count($sql_store_rcv)>1)
					{
						echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
					}
					else
					{
						$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
						foreach($sql_store_rcv as $result)
						{
							$store_up_id_to=$result[csf("id")];
							$store_presentStock	=$result[csf("current_stock")];
							$store_presentStockValue =$result[csf("stock_value")];
							$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
						}
						$currentStock_store		=($store_presentStock-$before_transfer_qnty);
						$currentValue_store		=($store_presentStockValue-$before_store_amount);
						if($store_up_id_to)
						{
							$store_id_arr[]=$store_up_id_to;
							$data_array_store[$store_up_id_to]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						}
					}	
				}
			}
			else if(str_replace("'","",$cbo_transfer_criteria)==1)
			{
				$sql = sql_select("select id, prod_id, transaction_type, cons_quantity, cons_amount  from inv_transaction where status_active=1 and is_deleted=0 and mst_id=$mst_id and transaction_type in(5,6) and id in($all_trans_id)");
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
				

				$issue_prod_sql = sql_select("select a.current_stock, a.avg_rate_per_unit, a.stock_value, b.transfer_qnty, b.transfer_value, b.store_rate, b.store_amount 
				from product_details_master a, inv_item_transfer_dtls b  
				where a.id=b.FROM_PROD_ID and a.id=$issue_prod_id and b.id=$update_dtls_id and a.status_active in(1,3) and b.status_active=1");					
				$beforeStock=$beforeStockValue=0; 
				foreach( $issue_prod_sql as $row)
				{
					$beforeStock			=$row[csf("current_stock")];
					$beforeStockValue		=$row[csf("stock_value")];
					$before_transfer_qnty	=$row[csf("transfer_qnty")];
					$before_transfer_value	=$row[csf("transfer_value")];
					$before_store_amount	=$row[csf("store_amount")];
				}
				//stock value minus here---------------------------//
				$adj_beforeStock=$beforeStock+$before_transfer_qnty;		
				$adj_beforeStockValue=$beforeStockValue+$before_transfer_value;	
				$field_array_product_issue="current_stock*stock_value*updated_by*update_date";
				$data_array_product_issue = "".$adj_beforeStock."*".number_format($adj_beforeStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";				

				$rcv_prod_sql = sql_select("select avg_rate_per_unit, current_stock, stock_value from product_details_master where status_active in(1,3) and is_deleted=0 and id=$rcv_prod_id");
				
				$beforeRcvStock=$beforeRcvStockValue=$beforeRcvAvgRate=0; 
				foreach( $prod_sql as $row)
				{
					$beforeRcvStock			=$row[csf("current_stock")];
					$beforeRcvStockValue	=$row[csf("stock_value")];
					$beforeRcvAvgRate		=$row[csf("avg_rate_per_unit")];	
				}
				//stock value minus here---------------------------//
				$adj_RcvbeforeStock			=$beforeRcvStock+$before_transfer_qnty;
				$adj_RcvbeforeStockValue	=$beforeRcvStockValue+$before_transfer_value;
				$adj_RecbeforeAvgRate=0;
				if ($adj_RcvbeforeStock != 0 && $adj_RcvbeforeStockValue != 0) $adj_RecbeforeAvgRate =number_format(($adj_RcvbeforeStockValue/$adj_RcvbeforeStock),10,'.','');						
			
				$field_array_product_rcv="avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
				$data_array_product_rcv = "".number_format($adj_RecbeforeAvgRate,10,'.','')."*".$adj_RcvbeforeStock."*".number_format($adj_RcvbeforeStockValue,8,'.','')."*'".$user_id."'*'".$pc_date_time."'";
				
				if($variable_store_wise_rate==1)
				{
					$sql_store = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$issue_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name and company_id=$cbo_company_id");
					$store_up_id=0;
					if(count($sql_store)<1)
					{
						echo "20**No Data Found.";disconnect($con);die;
					}
					elseif(count($sql_store)>1)
					{
						echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
					}
					else
					{
						$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
						foreach($sql_store as $result)
						{
							$store_up_id=$result[csf("id")];
							$store_presentStock	=$result[csf("current_stock")];
							$store_presentStockValue =$result[csf("stock_value")];
							$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
						}
						
						$currentStock_store		=$store_presentStock+$before_transfer_qnty;
						$currentValue_store		=$store_presentStockValue+$before_store_amount;
						if($store_up_id)
						{
							$store_id_arr[]=$store_up_id;
							$data_array_store[$store_up_id]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						}
					}
				}
				
				if($variable_store_wise_rate==1 && $variable_store_wise_rate_to==1)
				{
					$sql_store_rcv = sql_select("select id, rate as avg_rate_per_unit, cons_qty as current_stock, amount as stock_value from inv_store_wise_gen_qty_dtls where status_active=1 and prod_id=$rcv_prod_id and category_id=$cbo_item_category and store_id=$cbo_store_name_to and company_id=$cbo_company_id_to");
					$store_up_id_to=0;
					if(count($sql_store_rcv)<1)
					{
						echo "20**No Data Found.";disconnect($con);die;
					}
					elseif(count($sql_store_rcv)>1)
					{
						echo "20**Duplicate Product is Not Allow in Same REF Number.";disconnect($con);die;
					}
					else
					{
						$store_presentStock=$store_presentStockValue=$store_presentAvgRate=0;
						foreach($sql_store_rcv as $result)
						{
							$store_up_id_to=$result[csf("id")];
							$store_presentStock	=$result[csf("current_stock")];
							$store_presentStockValue =$result[csf("stock_value")];
							$store_presentAvgRate	=$result[csf("avg_rate_per_unit")];
						}
						$currentStock_store		=($store_presentStock-$before_transfer_qnty);
						$currentValue_store		=($store_presentStockValue-$before_store_amount);
						if($store_up_id_to)
						{
							$store_id_arr[]=$store_up_id_to;
							$data_array_store[$store_up_id_to]= explode("*",("".$txt_transfer_qnty."*".$currentStock_store."*".number_format($currentValue_store,8,'.','')."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
						}
					}
				}
				

				$field_array_trans="updated_by*update_date*status_active*is_deleted";
				$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
				
				//echo "10**$issue_prod_id"; disconnect($con);die;
				$rID1=sql_update("product_details_master",$field_array_product_issue,$data_array_product_issue,"id",$issue_prod_id,1);
				$rID2=sql_update("product_details_master",$field_array_product_rcv,$data_array_product_rcv,"id",$rcv_prod_id,1);
				
			}
			
			$upTrID=$mrr_table_id=$rID3=true;
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
			
			if(count($updateID_array)>0)
			{
				$upTrID=execute_query(bulk_update_sql_statement("inv_transaction","id",$update_array,$update_data,$updateID_array)); 
			}
			
			$field_array_mrr="updated_by*update_date*status_active*is_deleted";
			$data_array_mrr="".$user_id."*'".$pc_date_time."'*0*1";
			$mrr_table_id=return_field_value("id","inv_mrr_wise_issue_details","prod_id=$issue_prod_id and status_active=1 and is_deleted=0 and issue_trans_id=$update_trans_issue_id ","id");
			$rID3=sql_update("inv_mrr_wise_issue_details",$field_array_mrr,$data_array_mrr,"id",$mrr_table_id,1);

			$sql_mst = sql_select("select id from inv_transaction where status_active=1 and is_deleted=0 and transaction_type=6 and mst_id=$mst_id");
			if(count($sql_mst)==1)
			{
				$field_array_mst="updated_by*update_date*status_active*is_deleted";
				$data_array_mst="".$user_id."*'".$pc_date_time."'*0*1";

				$rID4=sql_update("inv_item_transfer_mst",$field_array_mst,$data_array_mst,"id",$mst_id,1);
				$resetLoad=1;
			}
			else
			{
				$rID4=1;
				$resetLoad=2;
			}

			$trans_ids=chop($trans_ids,",");
			$field_array_trans="updated_by*update_date*status_active*is_deleted";
			$data_array_trans="".$user_id."*'".$pc_date_time."'*0*1";
			
			$field_array_dtls="updated_by*update_date*status_active*is_deleted";
			$data_array_dtls="".$user_id."*'".$pc_date_time."'*0*1";
			//echo "10**".sql_multirow_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$trans_ids,0); die;
			$rID=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_id,1);
			$statusChange=sql_multirow_update("inv_transaction",$field_array_trans,$data_array_trans,"id",$trans_ids,1);
			if(count($store_id_arr)>0 && $variable_store_wise_rate==1)
			{
				$storeUpID=execute_query(bulk_update_sql_statement("inv_store_wise_gen_qty_dtls","id",$field_array_store,$data_array_store,$store_id_arr));
			}
			// echo "10**".$rID."**".$rID1."**".$rID2."**".$rID3."**".$statusChange."**".$rID4."**".$storeUpID;oci_rollback($con); die;
			if($db_type==0)
			{
				if($rID && $rID1 && $rID2 && $rID3 && $statusChange && $rID4 && $storeUpID && $upTrID && $mrr_table_id)
				{
					mysql_query("COMMIT");  
					echo "2**".$mst_id."**".str_replace("'","",$txt_system_id)."**0**".$resetLoad;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$mst_id."**".str_replace("'","",$txt_system_id)."**0**".$resetLoad;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID && $rID1 && $rID2 && $rID3 && $statusChange && $rID4 && $storeUpID && $upTrID && $mrr_table_id)
				{
					oci_commit($con);
					echo "2**".$mst_id."**".str_replace("'","",$txt_system_id)."**0**".$resetLoad;
				}
				else
				{
					oci_rollback($con); 
					echo "10**".$mst_id."**".str_replace("'","",$txt_system_id)."**0**".$resetLoad;
				}
			}
			disconnect($con);
			die;
 		}
	}
}

if($action=="yarn_transfer_print")
{
	 
    extract($_REQUEST);
	$data=explode('*',$data);
	$show_val_column=$data[3];
	//print_r ($data);
	
	$sql="select id, transfer_system_id, challan_no, transfer_date, transfer_criteria, item_category, to_company, from_order_id, to_order_id, requisition_no, requisition_id, inserted_by from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$sql_dtls="select id, from_store, to_store, from_prod_id, yarn_lot, transfer_qnty, item_category, uom from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	$sql_result= sql_select($sql_dtls);

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$sql_prod_res=sql_select("select id, product_name_details, item_code, avg_rate_per_unit as rate, unit_of_measure from product_details_master where item_category_id in (".implode(",",array_flip($general_item_category)).")");
	foreach ($sql_prod_res as $val) {
		$product_arr[$val[csf('id')]]['id']=$val[csf('id')];
		$product_arr[$val[csf('id')]]['product_name_details']=$val[csf('product_name_details')];
		$product_arr[$val[csf('id')]]['item_code']=$val[csf('item_code')];
		$product_arr[$val[csf('id')]]['rate']=$val[csf('rate')];
		$product_arr[$val[csf('id')]]['unit_of_measure']=$val[csf('unit_of_measure')];
	}
	//$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id in (".implode(",",array_flip($general_item_category)).")","id","product_name_details");
	$brand_arr=return_library_array( "select id, brand_name from   lib_brand", "id", "brand_name");
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name");
	$user_full_name=$user_arr[$dataArray[0][csf('inserted_by')]];
	?>
	<div style="width:930px; font-family:'Arial Narrow'">
		<table width="900" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
			</tr>
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:14px">  
					<?
						$nameArray=sql_select("select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{ 
						?>
							Plot No: <? echo $result[csf('plot_no')]; ?> 
							Level No: <? echo $result[csf('level_no')]?>
							Road No: <? echo $result[csf('road_no')]; ?> 
							Block No: <? echo $result[csf('block_no')];?> 
							City No: <? echo $result[csf('city')];?> 
							Zip Code: <? echo $result[csf('zip_code')]; ?> 
							Province No: <?php echo $result[csf('province')];?> 
							Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
							Email Address: <? echo $result[csf('email')];?> 
							Website No: <? echo $result[csf('website')];
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
				<td width="125"><strong>To Company:</strong></td><td width="175px"><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Requisition No:</strong></td><td width="175px"><? echo $dataArray[0][csf('requisition_no')]; ?></td>
			</tr>
			<tr>
				<td><strong>
					<?
						if($dataArray[0][csf('transfer_criteria')]==2 && $dataArray[0][csf('requisition_id')]!=0)
						{
							echo "To Company Store Address:";
						}
						else
						{
							echo "To Company Address:";
						}
					?>
					
					</strong>
				</td>
				<td colspan="5" valign="top">
					<?
						if($dataArray[0][csf('transfer_criteria')]==2 && $dataArray[0][csf('requisition_id')]!=0)
						{
							$nameArray=sql_select("SELECT address as ADDRESS from lib_store_location a, lib_location b where a.id='".$sql_result[0][csf('to_store')]."' and a.location_id=b.id");
							echo $nameArray[0]['ADDRESS'];
						}
						else
						{
							$nameArray=sql_select("select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id='".$dataArray[0][csf('to_company')]."'"); 
							foreach ($nameArray as $result)
							{ 
								if ($result[csf('plot_no')] != "") echo $result[csf('plot_no')];
								if ($result[csf('level_no')] != "") echo ','.$result[csf('level_no')];
								if ($result[csf('road_no')] != "") echo ','.$result[csf('road_no')];
								if ($result[csf('block_no')] != "") echo ','.$result[csf('block_no')];
								if ($result[csf('city')] != "") echo ','.$result[csf('city')];
								if ($result[csf('zip_code')] != "") echo ','.$result[csf('zip_code')];
								if ($result[csf('province')] != "") echo ','.$result[csf('province')];
								if ($result[csf('country_id')] != "") echo ','.$country_arr[$result[csf('country_id')]];
							}
						}

					?>
				</td>
			</tr>
		</table>
		<br>
		<?
		if ($show_val_column==1)
		{	
			?>
			<div style="width:100%;">
				<table align="right" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<th width="30">SL</th>
						<th width="150">From Store</th>
						<th width="150">To Store</th>
						<th width="120">Item Category</th> 
						<th width="200">Item Description</th>
						<th width="120">Item Code</th>
                        <th width="60">UOM</th>
						<th>Transfered Qnty</th>
					</thead>
					<tbody> 
			
						<?
						// $sql_dtls="select id, from_store, to_store, from_prod_id, yarn_lot, transfer_qnty,item_category from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
						// $sql_result= sql_select($sql_dtls);
						$i=1;
						$transfer_qnty_sum=0;
						foreach($sql_result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";												
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><? echo $store_library[$row[csf("from_store")]]; ?></td>
								<td><? echo $store_library[$row[csf("to_store")]]; ?></td>
								<td><? echo $item_category[$row[csf("item_category")]]; ?></td>
								<td><? echo $product_arr[$row[csf("from_prod_id")]]['product_name_details']; ?></td>
								<td><? echo $product_arr[$row[csf("from_prod_id")]]['item_code']; ?></td>
                                <td align="center"><? echo $unit_of_measurement[$product_arr[$row[csf("from_prod_id")]]['unit_of_measure']]; ?></td>
								<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
							</tr>
							<? 
							$i++;
							$transfer_qnty_sum += $row[csf("transfer_qnty")];
						} 
						?>
					</tbody>
					<!--<tfoot>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<td align="right" style="font-weight:bold;"><?php// echo $transfer_qnty_sum; ?></td>					
						</tr>                           
					</tfoot>-->
				</table>
				<br>
				<?
					echo signature_table(153, $data[0], "900px", '', 70, $user_full_name);
				?>
			</div>
			<?
		}
		else
		{
			?>
			<div style="width:100%;">
				<table align="right" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<th width="30">SL</th>
						<th width="120">From Store</th>
						<th width="120">To Store</th>
						<th width="120">Item Category</th> 
						<th width="180">Item Description</th>
						<th width="80">Item Code</th>
                        <th width="60">UOM</th>
						<th width="70">Transfered Qnty</th>
						<th width="70">Rate</th>
						<th>Amount</th>
					</thead>
					<tbody> 
			
						<?
						// $sql_dtls="select id, from_store, to_store, from_prod_id, yarn_lot, transfer_qnty, item_category from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
						// $sql_result= sql_select($sql_dtls);
						$i=1;
						$transfer_qnty_sum=0;
						$transfer_value_sum=0;
						foreach($sql_result as $row)
						{
							if ($i%2==0)  $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
							$rate=$product_arr[$row[csf("from_prod_id")]]['rate'];
							$amount=$row[csf("transfer_qnty")]*$rate;									
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><? echo $store_library[$row[csf("from_store")]]; ?></td>
								<td><? echo $store_library[$row[csf("to_store")]]; ?></td>
								<td><? echo $item_category[$row[csf("item_category")]]; ?></td>
								<td><? echo $product_arr[$row[csf("from_prod_id")]]['product_name_details']; ?></td>
								<td><? echo $product_arr[$row[csf("from_prod_id")]]['item_code']; ?></td>
                                <td align="center"><? echo $unit_of_measurement[$product_arr[$row[csf("from_prod_id")]]['unit_of_measure']]; ?></td>
								<td align="right"><? echo number_format($row[csf("transfer_qnty")],2); ?></td>
								<td align="right"><? echo number_format($rate,4); ?></td>
								<td align="right"><? echo number_format($amount,2); ?></td>
							</tr>
							<? 
							$i++;
							$transfer_qnty_sum += $row[csf("transfer_qnty")];
							$transfer_value_sum += $amount;
						} 
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="9" align="right"><strong>Total :</strong></td>
							<!--<td align="right" style="font-weight:bold;"><?// echo $transfer_qnty_sum; ?></td>
							<td></td>-->
							<td align="right" style="font-weight:bold;"><? echo number_format($transfer_value_sum,2); ?></td>
						
						</tr>                           
					</tfoot>
				</table>
				<br>
				<?
					echo signature_table(153, $data[0], "950px", '', 70, $user_full_name);
				?>
			</div>
			<?
		} 
		?>	
	</div>   
	<?
	exit();	 
}

if($action=="yarn_transfer_print2")
{
	 
    extract($_REQUEST);
	$data=explode('*',$data);
	$show_val_column=$data[3];
	//print_r ($data);
	
	$sql="select a.id, a.transfer_system_id, a.challan_no, a.transfer_date, a.transfer_criteria, a.item_category, a.to_company, a.from_order_id, a.to_order_id, a.requisition_no, a.requisition_id, a.inserted_by, b.security_lock_no, b.vhicle_number, b.sys_number, b.location_id from inv_item_transfer_mst a left join inv_gate_pass_mst b on a.TRANSFER_SYSTEM_ID=b.challan_no where a.id='$data[1]' and a.company_id='$data[0]' ";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$sql_dtls="select id, from_store, to_store, from_prod_id, yarn_lot, transfer_qnty, item_category, uom from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	//echo $sql_dtls;die;
	$sql_result= sql_select($sql_dtls);

	$to_company=$dataArray[0][csf('to_company')];
	$to_company_addrs=sql_select("select plot_no,level_no,road_no,block_no,province,city from lib_company where id=$to_company");

	$image_location=return_field_value("image_location","common_photo_library","file_type=1 and form_name='company_details' and master_tble_id='$data[0]'","image_location");

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$location_arr=return_library_array( "select id, location_name from  lib_location", "id", "location_name"  );
	$store_location=return_library_array( "select id, store_location from  lib_store_location", "id", "store_location"  );

	$sql_prod_res=sql_select("select id, product_name_details, item_code, avg_rate_per_unit as rate, unit_of_measure from product_details_master where item_category_id in (".implode(",",array_flip($general_item_category)).")");
	foreach ($sql_prod_res as $val) {
		$product_arr[$val[csf('id')]]['id']=$val[csf('id')];
		$product_arr[$val[csf('id')]]['product_name_details']=$val[csf('product_name_details')];
		$product_arr[$val[csf('id')]]['item_code']=$val[csf('item_code')];
		$product_arr[$val[csf('id')]]['rate']=$val[csf('rate')];
		$product_arr[$val[csf('id')]]['unit_of_measure']=$val[csf('unit_of_measure')];
	}
	//$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id in (".implode(",",array_flip($general_item_category)).")","id","product_name_details");
	$brand_arr=return_library_array( "select id, brand_name from   lib_brand", "id", "brand_name");
	$user_arr=return_library_array( "select id, user_full_name from user_passwd", "id", "user_full_name");
	$user_full_name=$user_arr[$dataArray[0][csf('inserted_by')]];
	?>
	<div style="width:930px; font-family:'Arial Narrow'">
		<table width="900" cellspacing="0" align="right" >

			<tr>
				<td  align="left" width="200px" rowspan="2"><img src="../../<? echo $image_location; ?>" height="70" width="200"></td>

				<td colspan="3" align="center"  rowspan="2" style="margin-left:10px;margin-right:10px;">
				<strong style="font-size:xx-large"><? echo $company_library[$data[0]]; ?></strong><br>
					<?
						$nameArray=sql_select("select plot_no, level_no, road_no, block_no, country_id, province, city, zip_code, email, website from lib_company where id=$data[0]"); 
						foreach ($nameArray as $result)
						{ 
						?>
							Plot No: <? echo $result[csf('plot_no')]; ?> 
							Level No: <? echo $result[csf('level_no')]?>
							Road No: <? echo $result[csf('road_no')]; ?> 
							Block No: <? echo $result[csf('block_no')];?> 
							City No: <? echo $result[csf('city')];?> 
							Zip Code: <? echo $result[csf('zip_code')]; ?> 
							Province No: <?php echo $result[csf('province')];?> 
							Country: <? echo $country_arr[$result[csf('country_id')]]; ?><br> 
							Email Address: <? echo $result[csf('email')];?> 
							Website No: <? echo $result[csf('website')];
						}
					?>
				</td>
				<td  align="right" rowspan="2"><div style="float:left; height:5%; width:5%;" id="qrcode"></div></td>
			</tr>	
			<tr></tr>
			 
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong ><u>General Item Transfer Challan</u></strong></td>
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:x-large"><strong >&nbsp;</strong></td>
			</tr>	
		</table>

		<table  border="1" rules="all" >		  
			<tr>
				<td width="120"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="130"><strong>Transfer Criteria:</strong></td> <td width="175px"><? echo $item_transfer_criteria[$dataArray[0][csf('transfer_criteria')]]; ?></td>
				<td><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				
			</tr>
			<tr>
				<td width="125"><strong>To Company:</strong></td>
				<td width="175px" colspan=""><? echo $company_library[$dataArray[0][csf('to_company')]]; ?></td>
				<td width="125"><strong>Address:</strong></td>
				<td width="175px" colspan="3"><? echo $to_company_addrs[0]['PLOT_NO']." ".$to_company_addrs[0]['LEVEL_NO']." ".$to_company_addrs[0]['ROAD_NO']." ".$to_company_addrs[0]['PROVINCE']; ?></td>
				
			</tr>
			<tr>
				<td><strong>To Location:</strong></td><td width="175px"><? echo $store_location[$sql_result[0][csf('to_store')]]; ?></td>
				<td><strong>Requisition On:</strong></td><td width="175px"><? 
				$date_arr=explode("-",$dataArray[0][csf('transfer_date')]);
				echo $date_arr[1]."-".$date_arr[2]; ?></td>
				<td><strong>Requisition No:</strong></td><td width="175px"><? echo $dataArray[0][csf('requisition_no')]; ?></td>
			</tr>

			<tr>
				<td><strong>Gate Pass No:</strong></td><td width="175px"><? echo $dataArray[0][csf('sys_number')]; ?></td>
				<td><strong>Vehicle No:</strong></td><td width="175px"><? echo $dataArray[0][csf('vhicle_number')]; ?></td>
				<td><strong>Security Lock No:</strong></td><td width="175px"><? echo $dataArray[0][csf('security_lock_no')]; ?></td>
			</tr>			 
		</table>
		<br>
		
			<div style="width:100%;">
				<table align="right" cellspacing="0" width="950"  border="1" rules="all" class="rpt_table" >
					<thead bgcolor="#dddddd" align="center">
						<th width="30">SL</th>
						<th width="120">From Store</th>
						<th width="120">To Store</th>
						<th width="120">Item Category</th> 
						<th width="180">Item Description</th>
						<th width="80">Product Id</th>                        
						<th width="70">Transfered Qnty</th>
						<th width="60">UOM</th>						 
					</thead>
					<tbody> 
			
						<?
						// $sql_dtls="select id, from_store, to_store, from_prod_id, yarn_lot, transfer_qnty, item_category from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
						// $sql_result= sql_select($sql_dtls);
						$i=1;
						$transfer_qnty_sum=0;
						$transfer_value_sum=0;
						foreach($sql_result as $row)
						{
							if ($i%2==0)  $bgcolor="#E9F3FF";
							else $bgcolor="#FFFFFF";
							$rate=$product_arr[$row[csf("from_prod_id")]]['rate'];
							$amount=$row[csf("transfer_qnty")]*$rate;									
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td align="center"><? echo $i; ?></td>
								<td><? echo $store_library[$row[csf("from_store")]]; ?></td>
								<td><? echo $store_library[$row[csf("to_store")]]; ?></td>
								<td><? echo $item_category[$row[csf("item_category")]]; ?></td>
								<td><? echo $product_arr[$row[csf("from_prod_id")]]['product_name_details']; ?></td>								 
								<td><? echo $row[csf("from_prod_id")]; ?></td>	
                                <td align="right"><? echo number_format($row[csf("transfer_qnty")],2); ?></td>
								<td align="center"><? echo $unit_of_measurement[$product_arr[$row[csf("from_prod_id")]]['unit_of_measure']]; ?></td>
								 
							</tr>
							<? 
							$i++;
							$transfer_qnty_sum += $row[csf("transfer_qnty")];
							 
						} 
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="6" align="right"><strong>Total :</strong></td>
							<!--<td align="right" style="font-weight:bold;"><?// echo $transfer_qnty_sum; ?></td>
							<td></td>-->
							<td align="right" style="font-weight:bold;"><? echo number_format($transfer_qnty_sum,2); ?></td>
							<td align="right" style="font-weight:bold;"><? echo "&nbsp;"; ?></td>
						
						</tr>                           
					</tfoot>
				</table>
				<br>
				<?
					//echo signature_table(153, $data[0], "950px", '', 70, $user_full_name);
				?>
			</div>
			<script type="text/javascript" src="../../js/jquery.js"></script>
			<script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
			<script>
				var main_value='<? echo $data[2]; ?>';
				$('#qrcode').qrcode(main_value);
			</script>	
	</div>   
	<?
	exit();	 
}

if($action=="chk_issue_requisition_variable")
{
	
    $sql =  sql_select("select user_given_code_status as USER_GIVEN_CODE_STATUS,id from variable_settings_inventory where company_name = $data and variable_list =30 and item_category_id=8 and is_deleted = 0 and status_active = 1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0]['USER_GIVEN_CODE_STATUS'];
	}
	else
	{ 
		$return_data=0; 
	}
	
	echo $return_data;
	die;
}

//Start Requisition No here------------------------------//
if ($action=="item_requisition_popup_search")
{
	echo load_html_head_contents("Order To Order Transfer Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			// alert(data);
			var data_info=data.split("_");
			if(data_info[4]==1 && (data_info[3]==0 || data_info[3]==2))
			{
				alert("General Transfer Requisition is no Approved");
				return;
			}
			else
			{
				$('#requisition_info').val(data);
				parent.emailwindow.hide();
			}
		}
    </script>
	</head>
	<body>
	<div align="center" style="width:850px; margin: 0 auto;">
		<form name="searchdescfrm"  id="searchdescfrm">
			<fieldset style="width:850px">
	        <legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="850" class="rpt_table">
	                <thead>
	                    <th>Search By</th>
	                    <th width="150" id="search_by_td_up">Please Enter Requisition ID</th>
	                    <th width="190">Date Range</th>
	                    <th>
	                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
	                        <input type="hidden" name="requisition_info" id="requisition_info" class="text_boxes" value="">
							<input type="hidden" name="cbo_company_id_to" id="cbo_company_id_to" class="text_boxes" value="<?= $cbo_company_id_to ?>">
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
	                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
	                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
	                    </td>
	                    <td>
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id_to').value, 'create_requisition_search_list_view', 'search_div', 'general_item_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
	                    </td>
	                </tr>
	                <tr>
	                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
	                </tr>
	            </table>	        	
				<div style="margin-top: 10px">
					<div style="margin-top:10px" id="search_div"></div> 
				</div>
			</fieldset>
		</form>
	</div>    
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_requisition_search_list_view')
{
	$data = explode("_",$data);
	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];
	$company_id =$data[2];
	$company_to =$data[6];
	$transfer_criteria_id =$data[3];
	
	if($search_by==1)
		$search_field="a.transfer_system_id";	
	else
		$search_field="a.challan_no";
	$to_company_cond="";
	if($data[6]!=0){
		$to_company_cond="and a.to_company=$data[6]";
	}

	if ($data[4]!="" &&  $data[5]!="") 
	{
		if($db_type==0)
		{
			$transfer_date = "and a.transfer_date between '".change_date_format($data[4], "yyyy-mm-dd", "-")."' and '".change_date_format($data[5], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$transfer_date = "and a.transfer_date between '".change_date_format($data[4],'','',1)."' and '".change_date_format($data[5],'','',1)."'";
		}
	}
	else 
		$transfer_date ="";
	
	if($db_type==0) $year_field="a.YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	if ($db_type==2) $app_nes_setup_date=change_date_format(date('d-m-Y'), "", "",1);
    else if ($db_type==0) $app_nes_setup_date=change_date_format(date('d-m-Y'),'yyyy-mm-dd');
	$approval_status="SELECT approval_need, allow_partial from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '$app_nes_setup_date' and company_id='$company_id')) and page_id=41 and status_active=1 and is_deleted=0";
    $app_need_setup=sql_select($approval_status);
    $approval_need=$app_need_setup[0][csf("approval_need")];
	
	if($db_type==0)
	{
		$sql="SELECT a.id, a.transfer_prefix_number, $year_field, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category, a.to_company,a.is_approved, b.transfer_qnty,a.ready_to_approve,a.requisition_status  
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b where a.id=b.mst_id and a.company_id=$company_id and $search_field like '$search_string' and a.transfer_criteria=$transfer_criteria_id $transfer_date and a.entry_form in(494) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $to_company_cond order by a.id desc";
	}
	else
	{
		$sql="SELECT a.id, a.transfer_prefix_number, a.transfer_system_id, $year_field, a.challan_no, a.company_id, a.transfer_date, a.transfer_criteria, a.item_category, a.to_company,a.is_approved, b.transfer_qnty,a.ready_to_approve,a.requisition_status, a.from_store_id 
		from inv_item_transfer_requ_mst a, inv_item_transfer_requ_dtls b 
		where a.id=b.mst_id and a.company_id=$company_id and $search_field like '$search_string' and a.transfer_criteria=$transfer_criteria_id $transfer_date and a.entry_form in(494) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $to_company_cond order by a.id desc";
	}
	//echo $sql;//die; 
	$sql_res=sql_select($sql);
	$check_transfer_requ_id=array();
	$transfer_requ_arr=array();
	foreach ($sql_res as $row) 
	{
		if ($check_transfer_requ_id[$row[csf('id')]]=="")
		{
			$check_transfer_requ_id[$row[csf('id')]]=$row[csf('id')];
			$transfer_requ_arr[$row[csf('id')]]['id']=$row[csf('id')];
			$transfer_requ_arr[$row[csf('id')]]['transfer_prefix_number']=$row[csf('transfer_prefix_number')];
			$transfer_requ_arr[$row[csf('id')]]['transfer_system_id']=$row[csf('transfer_system_id')];
			$transfer_requ_arr[$row[csf('id')]]['year']=$row[csf('year')];
			$transfer_requ_arr[$row[csf('id')]]['challan_no']=$row[csf('challan_no')];
			$transfer_requ_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
			$transfer_requ_arr[$row[csf('id')]]['transfer_date']=$row[csf('transfer_date')];
			$transfer_requ_arr[$row[csf('id')]]['ready_to_approve']=$row[csf('ready_to_approve')];
			$transfer_requ_arr[$row[csf('id')]]['requisition_status']=$row[csf('requisition_status')];
			$transfer_requ_arr[$row[csf('id')]]['transfer_criteria']=$row[csf('transfer_criteria')];
			$transfer_requ_arr[$row[csf('id')]]['item_category']=$row[csf('item_category')];
			$transfer_requ_arr[$row[csf('id')]]['to_company']=$row[csf('to_company')];
			$transfer_requ_arr[$row[csf('id')]]['is_approved']=$row[csf('is_approved')];
			$transfer_requ_arr[$row[csf('id')]]['from_store_id']=$row[csf('from_store_id')];
		}
		$transfer_requ_arr[$row[csf('id')]]['transfer_qnty']+=$row[csf('transfer_qnty')];
		
	}

	$item_transfer_sql="SELECT b.requisition_mst_id, b.transfer_qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and a.entry_form=57 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$item_transfer_result=sql_select($item_transfer_sql);
	$item_transfer_arr=array();
	foreach($item_transfer_result as $row)
	{
		$item_transfer_arr[$row[csf('requisition_mst_id')]]['transfer_qnty']+=$row[csf('transfer_qnty')];
	}

	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="825">
        <thead>
            <th width="30">SL</th>
            <th width="80">Requisition ID</th>
            <th width="50">Year</th>
            <th width="70">Challan No</th>
            <th width="60">Company</th>
            <th width="100">Requisition Date</th>
			<th width="120">Ready To Approve</th>
			<th width="100">Approval Status</th>
            <th width="105">Transfer Criteria</th>
            <th>To Company</th>
        </thead>
    </table>
    <div style="width:825px; max-height:270px;overflow-y:scroll;">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="825" class="rpt_table" id="tbl_list_search">
	        <tbody>
	            <? 
	            $i=1;
	            foreach($transfer_requ_arr as $row)
	            {  
	                if ($i%2==0) $bgcolor="#E9F3FF"; 
	                else $bgcolor="#FFFFFF";
	                $balance_qty= $row["transfer_qnty"] - $item_transfer_arr[$row['id']]['transfer_qnty'];
	                //echo  $balance_qty.'**'.$row["transfer_system_id"].'system';
	                if ($balance_qty > 0)
	                {           	
	                	?>
		                <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row["id"].'_'.$row["transfer_system_id"].'_'.$row["to_company"].'_'.$row["is_approved"].'_'.$approval_need.'_'.$row["from_store_id"]; ?>")' style="cursor:pointer" >
		                    <td width="30"><? echo $i; ?></td>
		                    <td width="80"><? echo $row['transfer_prefix_number']; ?></td>
		                    <td width="50"><? echo $row['year']; ?></td>
		                    <td width="70"><? echo $row['challan_no']; ?></td>
		                    <td width="60"><? echo $company_arr[$row['company_id']]; ?></td>
		                    <td width="100"><? echo change_date_format($row['transfer_date']); ?></td>
		                    <td width="120"><? if($row['ready_to_approve']==1){echo "Yes";}else{echo "No";}?></td>
		                    <td width="100"><? if($row['is_approved']==1){echo "Yes";}else{echo "No";} ?></td>
						    <td width="105"><? echo $item_transfer_criteria[$row['transfer_criteria']]; ?></td>
		                    <td><? echo $company_arr[$row['to_company']]; ?></td>
		                </tr>
						<?
					}	 
	                $i++; 
	            } 
	            ?>
	        </tbody>
    	</table>
    </div>
    <script>
		setFilterGrid("tbl_list_search",-1);
	</script>	
	<?
	exit();
}

if($action=="show_item_requisition_listview")
{
	$data_ref=explode("_",$data);
	$req_mst_id=$data_ref[0];
	$company_id=$data_ref[1];
	$store_id=$data_ref[2];	
	$item_transfer_sql="SELECT requisition_dtls_id as REQUISITION_DTLS_ID, transfer_qnty as TRANSFER_QNTY from inv_item_transfer_dtls where requisition_mst_id=$req_mst_id and status_active=1";
	$item_transfer_result=sql_select($item_transfer_sql);
	$item_transfer_arr=array();
	if(count($item_transfer_result)>0)
	{
		foreach($item_transfer_result as $row)
		{
			$item_transfer_arr[$row['REQUISITION_DTLS_ID']]['REQUISITION_DTLS_ID']=$row['REQUISITION_DTLS_ID'];
			$item_transfer_arr[$row['REQUISITION_DTLS_ID']]['TRANSFER_QNTY']+=$row['TRANSFER_QNTY'];
		}
	}

	//echo '<pre>';print_r($item_transfer_arr);

	//$sql="SELECT  c.id as ID, c.from_prod_id as FROM_PROD_ID, c.transfer_qnty as TRANSFER_QNTY, c.item_category as ITEM_CATEGORY, b.item_name as ITEM_NAME, a.item_code as ITEM_CODE, a.ITEM_DESCRIPTION as ITEM_DESCRIPTION
	//from product_details_master a, lib_item_group b, inv_item_transfer_requ_dtls c
	//where c.mst_id=$req_mst_id and c.entry_form=494 and c.from_prod_id=a.id and a.item_group_id=b.id and c.status_active=1 and a.status_active in(1,3) ";
	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where company_id=$company_id and status_active=1 and is_deleted=0 group by floor_room_rack_id, floor_room_rack_name order by floor_room_rack_name","floor_room_rack_id","floor_room_rack_name");
	$store_cond="";
	if($store_id>0) 
	{
		$store_cond=" and b.store_id=$store_id";
	}
	else
	{
		$req_store_id=return_field_value("from_store_id","inv_item_transfer_requ_mst","id=$req_mst_id and status_active=1","from_store_id");
		if($req_store_id>0) $store_cond=" and b.store_id=$req_store_id"; 	
	}
	
	$sql="SELECT A.ID as PROD_ID, A.COMPANY_ID, A.SUPPLIER_ID, A.ITEM_DESCRIPTION, A.ITEM_SIZE, A.ITEM_CODE, B.BATCH_LOT AS LOT, A.ITEM_GROUP_ID, A.CURRENT_STOCK AS ITEM_GLOBAL_STOCK, A.BRAND, A.UNIT_OF_MEASURE,
	SUM((CASE WHEN B.TRANSACTION_TYPE IN(1,4,5) THEN B.CONS_QUANTITY ELSE 0 END)-(CASE WHEN B.TRANSACTION_TYPE IN(2,3,6) THEN B.CONS_QUANTITY ELSE 0 END)) AS CURRENT_STOCK, B.STORE_ID, B.FLOOR_ID, B.ROOM, B.RACK, B.SELF, B.BIN_BOX, c.item_category as ITEM_CATEGORY, a.ITEM_GROUP_ID, c.id as ID, c.transfer_qnty as TRANSFER_QNTY 
	from product_details_master a, inv_transaction b, inv_item_transfer_requ_dtls c  
	where a.id=b.prod_id and b.prod_id=c.from_prod_id and a.company_id=$company_id $search_field and a.current_stock>0 and a.status_active in(1,3) and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.entry_form=494 and c.mst_id=$req_mst_id $store_cond
	group by a.id, a.company_id, a.supplier_id, a.item_description, a.item_size, a.item_code, b.batch_lot, a.item_group_id, a.current_stock, a.brand, a.unit_of_measure, b.store_id, b.floor_id, b.room, b.rack, b.self, b.bin_box, c.item_category, a.ITEM_GROUP_ID, c.id, c.transfer_qnty  
	having sum((case when b.transaction_type in(1,4,5) then b.cons_quantity else 0 end)-(case when b.transaction_type in(2,3,6) then b.cons_quantity else 0 end))>0";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	?>
	<table width="570" cellspacing="0" class="rpt_table" border="1" rules="all">
		<thead>
			<tr>
				<th width="70">Item Category</th>
				<th width="80">Item Desc.</th>
                <th width="60">Floor</th>
                <th width="60">Room</th>
                <th width="60">Rack</th>
                <th width="60">Shelf</th>
                <th width="60">Stock</th>
				<th width="60">Req. Qty</th>
				<th width="60">Bal. Qty</th>
			</tr>
		</thead>
		<tbody>
			<?
			$i=1;
			foreach($dataArray as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF";
				else $bgcolor="#FFFFFF";
				$tbl_id=$chk_array[$row["ID"]];
				$balance_qty=$row['TRANSFER_QNTY']-$item_transfer_arr[$row["ID"]]['TRANSFER_QNTY'];
				//echo $balance_qty."=".$row['TRANSFER_QNTY']."=".$item_transfer_arr[$row["ID"]]['TRANSFER_QNTY']."=".$row['ID']."<br>";
				if ($balance_qty > 0) 
				{
					//get_php_form_data(' echo $row['ID'];','requisition_transfer_details_form_data','requires/general_item_transfer_controller');
					//id,store_id,floor_id,room,rack,self,bin_box,lot
					$item_data=$row["PROD_ID"]."_".$row["STORE_ID"]."_".$row["FLOOR_ID"]."_".$row["ROOM"]."_".$row["RACK"]."_".$row["SELF"]."_".$row["BIN_BOX"]."_".$row["LOT"]."_".$store_id."_".$company_id;
					?>
					<tr bgcolor="<?=$bgcolor;?>" style="cursor: pointer;" onClick="get_php_form_data('<? echo $item_data; ?>', 'populate_data_from_product_master', 'requires/general_item_transfer_controller' );">
                    
						<td><? echo $general_item_category[$row['ITEM_CATEGORY']];?></td>						
						<td><? echo $row['ITEM_DESCRIPTION'];?></td>
                        <td title="<?=$row['FLOOR_ID'];?>"><? echo $floor_room_rack_arr[$row['FLOOR_ID']];?></td>
						<td title="<?=$row['ROOM'];?>"><? echo $floor_room_rack_arr[$row['ROOM']];?></td>
                        <td title="<?=$row['RACK'];?>"><? echo $floor_room_rack_arr[$row['RACK']];?></td>
						<td title="<?=$row['SELF'];?>"><? echo $floor_room_rack_arr[$row['SELF']];?></td>
                        <td align="right"><? echo $row['CURRENT_STOCK'];?></td>
						<td align="right"><? echo $row['TRANSFER_QNTY'];?></td>
						<td align="right"><? echo $balance_qty; ?></td>
					</tr>
					<?
				}	
				$i++;
			}
			?>
		</tbody>
	</table>
	<?	
	exit();
}

if($action=='requisition_transfer_details_form_data')
{
	$sql="SELECT c.id as ID, c.from_prod_id as FROM_PROD_ID, c.transfer_qnty as TRANSFER_QNTY, c.item_category as ITEM_CATEGORY, c.from_store as FROM_STORE, c.to_store as TO_STORE, a.product_name_details as PRODUCT_NAME_DETAILS, a.unit_of_measure as UNIT_OF_MEASURE, a.current_stock as ITEM_GLOBAL_STOCK, a.avg_rate_per_unit as AVG_RATE_PER_UNIT, a.order_uom as ORDER_UOM, d.batch_lot as BATCH_LOT, sum((case when d.transaction_type in(1,4,5) then d.cons_quantity else 0 end)- (case when d.transaction_type in(2,3,6) then d.cons_quantity else 0 end)) as CURRENT_STOCK
	from product_details_master a, inv_item_transfer_requ_dtls c, inv_transaction d
	where c.id=$data and c.entry_form=494 and c.from_prod_id=a.id and c.from_prod_id=d.prod_id and d.store_id=c.from_store and c.status_active=1 and a.status_active in(1,3) and d.status_active=1 and d.status_active=1 and d.is_deleted=0 
	group by c.id, c.from_prod_id, c.transfer_qnty, c.item_category, c.from_store, c.to_store, a.product_name_details, a.unit_of_measure, a.current_stock, a.avg_rate_per_unit, a.order_uom, d.batch_lot order by c.id";

	$item_transfer_sql="SELECT requisition_dtls_id as REQUISITION_DTLS_ID, transfer_qnty as TRANSFER_QNTY from inv_item_transfer_dtls where requisition_dtls_id=$data and status_active=1";
	$item_transfer_result=sql_select($item_transfer_sql);
	$item_transfer_arr=array();
	if(count($item_transfer_result)>0)
	{
		foreach($item_transfer_result as $row)
		{
			$item_transfer_arr[$row['REQUISITION_DTLS_ID']]['REQUISITION_DTLS_ID']=$row['REQUISITION_DTLS_ID'];
			$item_transfer_arr[$row['REQUISITION_DTLS_ID']]['TRANSFER_QNTY']+=$row['TRANSFER_QNTY'];
		}
	}
	// echo $sql;die;
	$data_array = sql_select($sql);

	foreach ($data_array as $row) 
	{ 			
		$transfer_qnty=$row["TRANSFER_QNTY"]-$item_transfer_arr[$row['ID']]['TRANSFER_QNTY'];
		echo "document.getElementById('hidden_product_id').value 			= '".$row["FROM_PROD_ID"]."';\n";
		echo "document.getElementById('hidden_req_dtls_id').value 			= '".$data."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row["ITEM_CATEGORY"]."';\n";
		echo "document.getElementById('txt_item_desc').value 				= '".$row["PRODUCT_NAME_DETAILS"]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$transfer_qnty."';\n";
		echo "document.getElementById('txt_current_stock').value 			= '".$row["CURRENT_STOCK"]."';\n";
		echo "document.getElementById('hidden_current_stock').value 		= '".$row["CURRENT_STOCK"]."';\n";
		echo "document.getElementById('txt_rate').value 					= '".$row["AVG_RATE_PER_UNIT"]."';\n";
		echo "document.getElementById('txt_transfer_value').value 			= '".number_format($row["AVG_RATE_PER_UNIT"]*$transfer_qnty,4,'.','')."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row["UNIT_OF_MEASURE"]."';\n";
		echo "document.getElementById('txt_lot').value 						= '".$row["BATCH_LOT"]."';\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row["FROM_STORE"]."';\n";
		echo "document.getElementById('cbo_store_name_to').value 			= '".$row["TO_STORE"]."';\n";

		echo "$('#cbo_item_category').attr('disabled', true);\n";
		echo "$('#txt_item_desc').attr('disabled', true);\n";
		echo "$('#cbo_store_name').attr('disabled', true);\n";
		echo "$('#cbo_store_name_to').attr('disabled', true);\n";
		echo "reset_on_change(".$row['TO_STORE'].");load_drop_down('requires/general_item_transfer_controller', ".$row['TO_STORE']."+'_'+document.getElementById('cbo_company_id').value, 'load_drop_floor','to_floor_td');\n";

	 	echo "load_room_rack_self_bin('requires/general_item_transfer_controller*4_8_9_10_11_15_16_17_18_19_20_21_22_32_33_34_35_36_37_38_39_40_41_44_45_46_47_48_49_50_51_52_53_54_55_56_57_58_59_60_61_62_63_64_65_66_67_68_69_70_89_90_91_92_93_94_99', 'floor','floor_td', document.getElementById('cbo_company_id').value,'"."','".$row["FROM_STORE"]."',this.value);storeUpdateUptoDisable();\n";

		//  echo "calculate_value()";
		
		exit();
	}
}
//End Requisition No here------------------------------//

?>
