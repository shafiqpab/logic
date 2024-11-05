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

// ==============Start Floor Room Rack Shelf Bin upto variable Settings============
if ($action=="varible_inventory")
{
	$store_maintain=return_field_value("store_method","variable_settings_inventory","company_name=$data and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	if($store_maintain=="" || $store_maintain==2) $store_maintain=0; else $store_maintain=$store_maintain;
	echo "document.getElementById('store_update_upto').value 		= '".$store_maintain."';\n";
}
// ==============End Floor Room Rack Shelf Bin upto variable Settings==============

if ($action=="load_drop_down_store_to")
{
	$data=explode("_",$data);
	$company=$data[0];

	$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id=$user_id");
	$store_location_id = $userCredential[0][csf('store_location_id')];
	if ($store_location_id != '') {$store_location_credential_cond = " and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}

	echo create_drop_down( "cbo_store_name_to", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id=b.store_location_id and a.company_id=$company and a.status_active=1 and a.is_deleted=0 and b.category_type in(13) group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", 0, "load_drop_down( 'requires/grey_fabric_sample_transfer_controller',this.value+'_'+$data[0], 'load_drop_down_floor_to', 'floor_td_to' );reset_func('store_fn');" );
	exit();
}

if ($action=="load_drop_down_floor_to")
{
	//print_r($data);die;
	$data=explode("_",$data);
	$store=$data[0];
	$company=$data[1];
	echo create_drop_down( "cbo_floor_to", 150, "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store' and a.company_id='$company' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name", "floor_id,floor_room_rack_name", 1, "-- Select Floor --", 0, "load_drop_down( 'requires/grey_fabric_sample_transfer_controller',this.value+'_'+$company+'_'+$store, 'load_drop_down_room_to', 'room_td_to' );reset_func('floor_fn');");
	exit();
}

if ($action=="load_drop_down_room_to")
{
	$data=explode("_",$data);
	$floorId=$data[0];
	$company=$data[1];
	$store=$data[2];
	echo create_drop_down( "cbo_room_to", 150, "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "-- Select Room --", 0, "load_drop_down( 'requires/grey_fabric_sample_transfer_controller',this.value+'_'+$company+'_'+$store+'_'+$floorId, 'load_drop_down_rack_to', 'rack_td_to' );reset_func('room_fn');");
	exit();
}

if ($action=="load_drop_down_rack_to")
{
	$data=explode("_",$data);
	$roomId=$data[0];
	$company=$data[1];
	$store=$data[2];
	$floorId=$data[3];
	echo create_drop_down( "txt_rack_to", 150, "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and room_id=$roomId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "-- Select Rack --", 0, "load_drop_down( 'requires/grey_fabric_sample_transfer_controller',this.value+'_'+$company+'_'+$store+'_'+$floorId+'_'+$roomId, 'load_drop_down_shelf_to', 'shelf_td_to' );");
	exit();
}
if ($action=="load_drop_down_shelf_to")
{
	$data=explode("_",$data);
	$rackId=$data[0];
	$company=$data[1];
	$store=$data[2];
	$floorId=$data[3];
	$roomId=$data[4];
	echo create_drop_down( "txt_shelf_to", 150, "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id=$store and a.company_id='$company' and b.floor_id=$floorId and room_id=$roomId and rack_id=$rackId and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "-- Select Shelf --", 0, "");
	exit();
}

if ($action=="load_room_rack_self_bin")
{
	/*$explodeData = explode('*', $data);
	$customFnc = array( 'store_update_upto_disable()' ); // not necessarily an array, see manual quote
	array_splice( $explodeData, 11, 0, $customFnc ); // splice in at position 3
	$data=implode('*', $explodeData);*/
	//echo $data;
	load_room_rack_self_bin("requires/grey_fabric_sample_transfer_controller",$data);
}

if($action=='populate_store_floor_room_rack_self_data')
{
	$data = explode("_",$data);
	$company_id=$data[0];
	$store_id = $data[1];
	$floor_id = $data[2];
	$room = $data[3];
	$rack = $data[4];
	$self = $data[5];

	if($store_id !=0)
	{
		echo "load_room_rack_self_bin('requires/grey_fabric_sample_transfer_controller*13', 'store','from_store_td', '".$data[0]."','"."','"."',this.value);\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$store_id."';\n";
	}

	if($floor_id !=0)
	{
		echo "load_room_rack_self_bin('requires/grey_fabric_sample_transfer_controller*13', 'floor','floor_td', '".$data[0]."','"."','".$store_id."',this.value);\n";
		echo "document.getElementById('cbo_floor').value 				= '".$floor_id."';\n";
	}
	if($room !=0)
	{
		echo "load_room_rack_self_bin('requires/grey_fabric_sample_transfer_controller*13', 'room','room_td', '".$data[0]."','"."','".$store_id."','".$floor_id."',this.value);\n";
		echo "document.getElementById('cbo_room').value 				= '".$room."';\n";
	}
	if($rack !=0)
	{
		echo "load_room_rack_self_bin('requires/grey_fabric_sample_transfer_controller*13', 'rack','rack_td', '".$data[0]."','"."','".$store_id."','".$floor_id."','".$room."',this.value);\n";
		echo "document.getElementById('txt_rack').value 				= '".$rack."';\n";
	}
	if($self !=0)
	{
		echo "load_room_rack_self_bin('requires/grey_fabric_sample_transfer_controller*13', 'shelf','shelf_td', '".$data[0]."','"."','".$store_id."','".$floor_id."','".$room."','".$rack."',this.value);\n";
		echo "document.getElementById('txt_shelf').value 				= '".$self."';\n";
	}
	echo "$('#cbo_store_name').prop('disabled', true);\n";
	echo "$('#cbo_floor').prop('disabled', true);\n";
	echo "$('#cbo_room').prop('disabled', true);\n";
	echo "$('#txt_rack').prop('disabled', true);\n";
	echo "$('#txt_shelf').prop('disabled', true);\n";	
	exit();
}

if ($action=="order_popup") // To order, to sample popup
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?> 
	<script>
		function js_set_value(data)
		{
			$('#order_booking_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<?
		if ($cbo_transfer_criteria==7) // To Order
		{
			?>
			<div align="center" style="width:880px;">
				<form name="searchdescfrm"  id="searchdescfrm">
					<fieldset style="width:870px;margin-left:10px">
			        <legend>Enter search words</legend>
			            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
			                <thead>
			                    <th>Buyer Name</th>
			                    <th>Order No</th>
			                    <th>Internal Ref.</th>
			                    <th width="230">Shipment Date Range</th>
			                    <th>
			                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
			                        <input type="hidden" name="order_booking_id" id="order_booking_id" class="text_boxes" value="">
			                    </th>
			                </thead>
			                <tr class="general">
			                    <td>
									<?
										echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
									?>
			                    </td>
			                    <td>
			                        <input type="text" style="width:130px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
			                    </td>
			                    <td>
			                        <input type="text" style="width:130px;" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" />
			                    </td>
			                    <td>
			                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
			                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
			                    </td>
			                    <td>
			                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_internal_ref').value, 'create_po_search_list_view', 'search_div', 'grey_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
			                    </td>
			                </tr>
			                <tr>
			                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
			                </tr>
			            </table>
			        	<div style="margin-top:10px" id="search_div"></div> 
					</fieldset>
				</form>
			</div> 
			<?
		}
		else // To Sample
		{
			?>
			<div align="center" style="width:880px;">
				<form name="searchdescfrm"  id="searchdescfrm">
					<fieldset style="width:870px;margin-left:10px">
			        <legend>Enter search words</legend>
			            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
			                <thead>
			                    <th>Buyer Name</th>
			                    <th>Booking No</th>
			                    <th width="230">Booking Date Range</th>
			                    <th>
			                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
			                        <input type="hidden" name="order_booking_id" id="order_booking_id" class="text_boxes" value="">
			                    </th>
			                </thead>
			                <tr class="general">
			                    <td>
									<?
										echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
									?>
			                    </td>
			                    <td>
			                        <input type="text" style="width:130px;" class="text_boxes" name="txt_sample_no" id="txt_sample_no" />
			                    </td>
			                    <td>
			                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
			                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
			                    </td>
			                    <td>
			                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_sample_search_list_view', 'search_div', 'grey_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
			                    </td>
			                </tr>
			                <tr>
			                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
			                </tr>
			            </table>
			        	<div style="margin-top:10px" id="search_div"></div> 
					</fieldset>
				</form>
			</div>
			<?
		}
		?>
		   
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_po_search_list_view')
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer=""; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$shipment_date ="";
	
	$type=$data[5];
	$internal_ref=trim($data[6]);
	$arr=array (2=>$company_arr,3=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later

	if($internal_ref) {
		$ref_cond= " and b.grouping like '%$internal_ref%'";
	}
	if($buyer){
		$buyer_cond="and a.buyer_name = $buyer";
	}
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	$sql= "select a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.grouping, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id $buyer_cond and b.po_number like '$search_string' $ref_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date order by b.id, b.pub_shipment_date";  
	 
	echo create_list_view("tbl_list_search", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,Internal Ref.,PO Quantity,Shipment Date", "30,40,70,80,120,90,110,90,90,80","900","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,grouping,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,1,3');
	
	exit();
}

if($action=='create_sample_search_list_view')
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$booking_date ="";
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	$arr=array (2=>$company_arr,3=>$buyer_arr,4=>$style_name_array,5=>$body_part);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "select a.id as booking_id, a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description, b.body_part
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no  and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' and b.booking_no like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date order by a.id, b.id";
	
	//echo  $sql;die;
	 
	echo create_list_view("tbl_list_search", "Booking No,Year,Company,Buyer Name,Style Ref. No,Garments Item,Style Description,Booking Date", "60,60,80,80,120,130,160","850","200",0, $sql , "js_set_value", "booking_id", "", 1, "0,0,company_id,buyer_id,style_id,body_part,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,body_part,fabric_description,booking_date", "",'','0,0,0,0,0,0,0,3');
	
	exit();
}

if($action=='populate_data_to_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	$transfer_criteria=$data[2];

	if ($transfer_criteria==7) // To Order data populate
	{
		$data_array=sql_select("SELECT a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date 
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
		foreach ($data_array as $row)
		{ 
			$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
			foreach($gmts_item_id as $item_id)
			{
				if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
			}
			
			echo "document.getElementById('txt_to_order_book_id').value 	= '".$po_id."';\n";
			echo "document.getElementById('txt_to_order_book_no').value 	= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('txt_to_qnty').value 				= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 		= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 		= '".$row[csf("style_ref_no")]."';\n";
			echo "document.getElementById('txt_to_job_no').value 			= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_to_gmts_item').value 		= '".$gmts_item."';\n";
			echo "document.getElementById('txt_to_shipment_date').value 	= '".change_date_format($row[csf("shipment_date")])."';\n";
			exit();
		}
	}
	else // To sample data populate
	{
		$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
		$data_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
		where a.booking_no=b.booking_no and a.id=$po_id");
		foreach ($data_array as $row)
		{ 
			
			echo "document.getElementById('txt_to_order_book_no').value 	= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_to_order_book_id').value 	= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_to_qnty').value 				= '".$row[csf("grey_fabric")]."';\n";
			echo "document.getElementById('cbo_to_buyer_name').value 		= '".$row[csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_to_style_ref').value 		= '".$style_name_array[$row[csf("style_id")]]."';\n";
			echo "document.getElementById('txt_to_gmts_item').value 		= '".$row[csf("body_part")]."';\n";
			exit();
		}
	}
}

if ($action=="from_sample_order_popup")
{
	echo load_html_head_contents("sample Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			$('#return_id').val(data);
			parent.emailwindow.hide();
		}
    </script>
	</head>
	<body>
		<?
		if ($cbo_transfer_criteria==6) // Order
		{
			?>
			<div align="center" style="width:880px;">
				<form name="searchdescfrm"  id="searchdescfrm">
					<fieldset style="width:870px;margin-left:10px">
			        <legend>Enter search words</legend>
			            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
			                <thead>
			                    <th>Buyer Name</th>
			                    <th>Order No</th>
			                    <th>Internal Ref.</th>
			                    <th width="230">Shipment Date Range</th>
			                    <th>
			                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
			                        <input type="hidden" name="return_id" id="return_id" class="text_boxes" value="">
			                    </th>
			                </thead>
			                <tr class="general">
			                    <td>
									<?
										echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
									?>
			                    </td>
			                    <td>
			                        <input type="text" style="width:130px;" class="text_boxes" name="txt_order_no" id="txt_order_no" />
			                    </td>
			                    <td>
			                        <input type="text" style="width:130px;" class="text_boxes" name="txt_internal_ref" id="txt_internal_ref" />
			                    </td>
			                    <td>
			                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
			                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
			                    </td>
			                    <td>
			                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_internal_ref').value, 'create_po_search_list_view_from', 'search_div', 'grey_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
			                    </td>
			                </tr>
			                <tr>
			                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
			                </tr>
			            </table>
			        	<div style="margin-top:10px" id="search_div"></div> 
					</fieldset>
				</form>
			</div>
			<?
		}
		else // Sample
		{
			?>
			<div align="center" style="width:880px;">
				<form name="searchdescfrm"  id="searchdescfrm">
					<fieldset style="width:870px;margin-left:10px">
			        <legend>Enter search words</legend>
			            <table cellpadding="0" cellspacing="0" width="800" class="rpt_table">
			                <thead>
			                    <th>Buyer Name</th>
			                    <th>Booking No</th>
			                    <th width="230">Booking Date Range</th>
			                    <th>
			                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
			                        <input type="hidden" name="return_id" id="return_id" class="text_boxes" value="">
			                    </th>
			                </thead>
			                <tr class="general">
			                    <td>
									<?
										echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
									?>
			                    </td>
			                    <td>
			                        <input type="text" style="width:130px;" class="text_boxes" name="txt_sample_no" id="txt_sample_no" />
			                    </td>
			                    <td>
			                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px" placeholder="From Date" readonly>
			                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px" placeholder="To Date" readonly>
			                    </td>
			                    <td>
			                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_sample_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_sample_search_list_view_from', 'search_div', 'grey_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_sample\',-1);')" style="width:100px;" />
			                    </td>
			                </tr>
			                <tr>
			                    <td colspan="4" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
			                </tr>
			            </table>
			        	<div style="margin-top:10px" id="search_div"></div> 
					</fieldset>
				</form>
			</div>
			<?
		}
		?>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=='create_sample_search_list_view_from')
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$booking_date = "and a.booking_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$booking_date ="";
	
	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	$arr=array (2=>$company_arr,3=>$buyer_arr,4=>$style_name_array,5=>$body_part);
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	
	$sql= "SELECT a.id as booking_id, a.booking_no, $year_field, a.booking_no_prefix_num, a.company_id, a.buyer_id, a.booking_date, a.delivery_date, b.style_id, b.fabric_description, b.body_part
	from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
	where a.booking_no=b.booking_no and a.booking_type=4 and a.company_id=$company_id and a.buyer_id like '$buyer' and b.booking_no like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $booking_date order by a.id, b.id";
	
	 
	echo create_list_view("tbl_list_sample", "Booking No,Year,Company,Buyer Name,Style Ref. No,Garments Item,Style Description,Booking Date", "60,60,80,80,120,130,160","850","200",0, $sql , "js_set_value", "booking_id", "", 1, "0,0,company_id,buyer_id,style_id,body_part,0,0", $arr , "booking_no_prefix_num,year,company_id,buyer_id,style_id,body_part,fabric_description,booking_date", "",'','0,0,0,0,0,0,0,3');
	
	exit();
}

if($action=='create_po_search_list_view_from')
{
	$data=explode('_',$data);
	
	if ($data[0]==0) $buyer=""; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$shipment_date = "and b.pub_shipment_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$shipment_date ="";
	
	$type=$data[5];
	$internal_ref=trim($data[6]);
	$arr=array (2=>$company_arr,3=>$buyer_arr);
	
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
		
	if($internal_ref) {
		$ref_cond= " and b.grouping like '%$internal_ref%'";
	}
	if($buyer){
		$buyer_cond="and a.buyer_name = $buyer";
	}

	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	$sql= "SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number, b.grouping, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id $buyer_cond and b.po_number like '$search_string' $ref_cond and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $shipment_date order by b.id, b.pub_shipment_date";  
	 
	echo create_list_view("tbl_list_sample", "Job No,Year,Company,Buyer Name,Style Ref. No,Job Qty.,PO number,Internal Ref,PO Quantity,Shipment Date", "50,40,70,80,120,90,110,90,90,80","900","200",0, $sql , "js_set_value", "id", "", 1, "0,0,company_name,buyer_name,0,0,0,0", $arr , "job_no_prefix_num,year,company_name,buyer_name,style_ref_no,job_quantity,po_number,grouping,po_quantity,shipment_date", "",'','0,0,0,0,0,1,0,0,1,3');
	exit();
}

if($action=="load_drop_down_item_desc")
{
	$data=explode("**", $data);
	$booking_order_id = $data[0];
	$transfer_criteria = $data[1];

	$item_description=array();
	if ($transfer_criteria==6) // Order
	{
		$sql="SELECT a.id, a.product_name_details, 1 as type from product_details_master a, order_wise_pro_details b
		where a.id=b.prod_id and b.po_breakdown_id=$booking_order_id and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0
		union all
		select a.id, a.product_name_details, 2 as type 
		from product_details_master a, inv_transaction b, inv_item_transfer_mst c 
		where a.id=b.prod_id and b.mst_id=c.id 
		and b.transaction_type=5 and b.status_active=1 and b.is_deleted=0 and c.to_order_id=$booking_order_id group by a.id, a.product_name_details";
	}
	else // Sample
	{
		$sql="SELECT a.id, a.product_name_details, 1 as type from product_details_master a,  inv_transaction b,  inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id  and c.booking_id=$booking_order_id and b.transaction_type=1 and c.receive_basis in(1,2,11) and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=1 group by a.id, a.product_name_details
		union all
		select a.id, a.product_name_details, 2 as type from product_details_master a,  inv_transaction b,  inv_receive_master c where a.id=b.prod_id and b.mst_id=c.id  and c.booking_id in (select m.id as id from inv_receive_master m where m.booking_id=$booking_order_id and m.entry_form=2 and M.BOOKING_WITHOUT_ORDER=1) and b.transaction_type=1 and c.receive_basis=9 and b.status_active=1 and b.is_deleted=0 and c.booking_without_order=1 group by a.id, a.product_name_details
		union all
		select a.id, a.product_name_details, 2 as type 
		from product_details_master a, inv_transaction b, inv_item_transfer_mst c 
		where a.id=b.prod_id and b.mst_id=c.id 
		and b.transaction_type=5 and b.status_active=1 and b.is_deleted=0 and c.to_order_id=$booking_order_id group by a.id, a.product_name_details";
	}
	
	//echo $sql;die;
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('product_name_details')];
	}
	echo create_drop_down( "cbo_item_desc", 368, $item_description,'', 1, "--Select Item Description--",'0','','1');  
	exit();
}

if($action=="populate_data_from_sample")
{
	$data=explode("**",$data);
	$return_id=$data[0]; // return_id is booking or order no
	$from=$data[1];
	$transfer_criteria=$data[2];

	$style_name_array=return_library_array( "select id, style_ref_no from sample_development_mst",'id','style_ref_no');
	if ($transfer_criteria==6) // Order
	{
		$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$return_id");
		foreach ($data_array as $row)
		{ 
			$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
			foreach($gmts_item_id as $item_id)
			{
				if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
			}
			
			echo "document.getElementById('txt_from_order_book_no').value 		= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('txt_from_order_book_id').value 		= '".$return_id."';\n";
			echo "document.getElementById('txt_from_qnty').value 				= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_from_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_from_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
			echo "document.getElementById('txt_from_job_no').value 				= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('cbo_from_garments_item').value 		= '".$gmts_item."';\n";
			echo "document.getElementById('txt_from_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
			exit();
		}
	}
	else // sample
	{
		$data_array=sql_select("SELECT a.id as booking_id, a.booking_no, a.booking_no_prefix_num, a.buyer_id, b.style_id, b.body_part, b.grey_fabric
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
		where a.booking_no=b.booking_no and a.id=$return_id");
		foreach ($data_array as $row)
		{ 
			
			echo "document.getElementById('txt_from_order_book_no').value 	= '".$row[csf("booking_no")]."';\n";
			echo "document.getElementById('txt_from_order_book_id').value 	= '".$row[csf("booking_id")]."';\n";
			echo "document.getElementById('txt_from_qnty').value 			= '".$row[csf("grey_fabric")]."';\n";
			echo "document.getElementById('cbo_from_buyer_name').value 		= '".$row[csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_from_style_ref').value 		= '".$style_name_array[$row[csf("style_id")]]."';\n";
			echo "document.getElementById('cbo_from_garments_item').value 	= '".$row[csf("body_part")]."';\n";
			exit();
		}
	}	
}

if($action=="show_dtls_list_view") // Site List view
{
	$data=explode("**", $data);
	$booking_order_id = $data[0];
	$transfer_criteria = $data[1];
	$cbo_company_id = $data[2];

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$floor_arr=return_library_array( "select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst",'floor_room_rack_id','floor_room_rack_name');

	$query="select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active=1 and is_deleted=0";
	$nameArray = sql_select($query);
	$new_array=array();
	foreach ($nameArray as $result) 
	{
		$new_array[$result[csf('floor_room_rack_id')]] = $result[csf('floor_room_rack_name')];
	}

	if ($transfer_criteria==6) // Order
	{
		$auto_recv_variable=return_field_value("auto_update","variable_settings_production","company_name='$cbo_company_id' and item_category_id=13 and variable_list=15 and is_deleted=0 and status_active=1");
		if($auto_recv_variable==2)
		{
			$sql = "SELECT a.id, a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form, e.store_id, e.floor_id, e.room, e.rack, e.self
			from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d, inv_transaction e
			where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and d.id=e.mst_id and c.trans_id=e.id and  a.item_category_id=13 and b.entry_form in(22) and d.entry_form in(22)  and d.entry_form <>2  and b.po_breakdown_id=$booking_order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=9 
			group by a.id, a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, d.receive_basis, d.booking_id, d.booking_no, d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, e.store_id, e.floor_id, e.room, e.rack, e.self
			union all
		    select a.id, a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, c.y_count as yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, 0 as receive_basis, c.from_program as booking_id, null as booking_no, d.entry_form, e.store_id, e.floor_id, e.room, e.rack, e.self
		    from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_item_transfer_mst d, inv_transaction e
		    where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(13,81) and d.entry_form in(13,81) and b.po_breakdown_id=$booking_order_id and b.trans_type=5 and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.item_category=13
		    group by a.id, a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, d.entry_form , c.y_count, c.brand_id, c.yarn_lot, c.stitch_length,c.from_program, e.store_id, e.floor_id, e.room, e.rack, e.self"; // Transfer in entry_form=81 Grey Fabric Sample To Order Transfer Entry
		}
		else
		{
			$sql = "SELECT a.id, a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form, e.store_id, e.floor_id, e.room, e.rack, e.self
			from product_details_master a, order_wise_pro_details b, pro_grey_prod_entry_dtls c, inv_receive_master d, inv_transaction e
			where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(2,22) and d.entry_form in(2,22) and b.po_breakdown_id=$booking_order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis<>9 and d.id=e.mst_id
			group by a.id, a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, d.receive_basis, d.booking_id, d.booking_no, d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, e.store_id, e.floor_id, e.room, e.rack, e.self
			union all
		    select a.id, a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, c.y_count as yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, 0 as receive_basis, c.from_program as booking_id, null as booking_no, d.entry_form, e.store_id, e.floor_id, e.room, e.rack, e.self
		    from product_details_master a, order_wise_pro_details b, inv_item_transfer_dtls c, inv_item_transfer_mst d, inv_transaction e
		    where a.id=b.prod_id and b.dtls_id=c.id and c.mst_id=d.id and a.item_category_id=13 and b.entry_form in(13,81) and d.entry_form in(13,81) and b.po_breakdown_id=$booking_order_id and b.trans_type=5 and d.id=e.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and e.item_category=13
		    group by a.id, a.product_name_details, a.lot, a.current_stock, a.avg_rate_per_unit, a.brand, d.entry_form , c.y_count, c.brand_id, c.yarn_lot, c.stitch_length,c.from_program, e.store_id, e.floor_id, e.room, e.rack, e.self"; // Transfer in entry_form=81 Grey Fabric Sample To Order Transfer Entry
		}
	}
	else // Sample
	{
		$sql = "SELECT a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form, 1 as type, b.store_id, b.floor_id, b.room, b.rack, b.self	 
		from product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
		where a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form in(2,22) and d.booking_id=$booking_order_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and  d.receive_basis=(case when d.entry_form = 22 then 2 when d.entry_form = 2 then 1 else 0 end) and d.booking_without_order=1
		group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, b.store_id, b.floor_id, b.room, b.rack, b.self
		union all 
		SELECT a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form, 1 as type, b.store_id, b.floor_id, b.room, b.rack, b.self     
		from product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
		where a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.booking_id=$booking_order_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 
		and b.is_deleted=0 and d.entry_form = 22 and d.receive_basis= 11 and d.booking_without_order=1
		group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, b.store_id, b.floor_id, b.room, b.rack, b.self
		union all
		select a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form, 2 as type, b.store_id, b.floor_id, b.room, b.rack, b.self 	 
		from product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
		where a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form =22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=1 and d.booking_id in(select p.id from com_pi_master_details p, com_pi_item_details q where p.id=q.pi_id and q.work_order_id=$booking_order_id) and d.booking_without_order=1
		group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, b.store_id, b.floor_id, b.room, b.rack, b.self
		union all
		select a.id, a.product_name_details, c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, d.receive_basis, d.booking_id, d.booking_no,d.entry_form, 3 as type, b.store_id, b.floor_id, b.room, b.rack, b.self	 
		from product_details_master a, inv_transaction b, pro_grey_prod_entry_dtls c, inv_receive_master d
		where a.id=b.prod_id and b.id=c.trans_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form =22 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.receive_basis=9 and d.booking_id in(select p.id from inv_receive_master p where p.booking_id=$booking_order_id and p.entry_form=2 and p.receive_basis=1 and p.booking_without_order=1) and d.booking_without_order=1
		group by a.id, a.product_name_details, d.receive_basis, d.booking_id, d.booking_no,d.entry_form , c.yarn_count, c.brand_id, c.yarn_lot, c.stitch_length, b.store_id, b.floor_id, b.room, b.rack, b.self
		union all 
		SELECT a.id, a.product_name_details, c.y_count, c.brand_id, c.yarn_lot, c.stitch_length, 0 as receive_basis, d.to_order_id as booking_id, null as booking_no, d.entry_form, 3 as type, b.store_id, b.floor_id, b.room, b.rack, b.self     
		from product_details_master a, inv_transaction b, inv_item_transfer_dtls c, inv_item_transfer_mst d
		where a.id=b.prod_id and d.id=b.mst_id and c.mst_id=d.id and a.item_category_id=13 and d.entry_form in(80,432) and b.transaction_type=5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.to_order_id=$booking_order_id
		group by a.id, a.product_name_details,  d.to_order_id, d.entry_form , c.y_count, c.brand_id, c.yarn_lot, c.stitch_length, b.store_id, b.floor_id, b.room, b.rack, b.self";
	}
	
	// echo $sql;
	//$booking_id=$data;
	$data_array=sql_select($sql);	
	
	?>
    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="550">
        <thead>
            <th>Fabric Description</th>
            <th width="70">Book./ Prog. No</th>
            <th width="40">Y/count</th>
            <th width="40">Y/Brand</th>
            <th width="40">Y/Lot</th>
            <th width="45">Stitch Length</th>
            <th width="40">Store</th>
            <th width="40">Floor</th>
            <th width="40">Room</th>
            <th width="40">Rack</th>
            <th width="40">Shelf</th>
        </thead>
        <tbody>
            <? 
            $i=1;$booking_no="";
            foreach($data_array as $row)
            {  
                if ($i%2==0)  
                    $bgcolor="#E9F3FF";
                else
                    $bgcolor="#FFFFFF";
				$ycount='';
				$count_id=explode(',',$row[csf('yarn_count')]);
				foreach($count_id as $count)
				{
					if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
				}
				
				if($row[csf('entry_form')]==2)
				{
					if($row[csf('receive_basis')]==2)
					{
						$knit_palan_no=$row[csf('booking_no')]; 
					}
					else 
					{
						$knit_palan_no="";
					}
				}
				else
				{
					$booking_order_id="";
					if($row[csf('receive_basis')]==9)
					{

						$book_sql=sql_select("select booking_id, booking_no from inv_receive_master where id='".$row[csf('booking_id')]."' and status_active=1 and is_deleted=0 and entry_form in(2)");
						$booking_no=$book_sql[0][csf("booking_no")];
						$booking_order_id=$book_sql[0][csf("booking_id")];
						$knit_palan_no=$book_sql[0][csf("booking_id")];

					}
					else
					{
						$booking_no=$row[csf('booking_no')];
						$booking_order_id=$row[csf('booking_id')];
					}
				}
				$booking_plan="";
				if($transfer_criteria==6)
				{
					$booking_plan=$knit_palan_no;
				}
				else{
					if ($row[csf('entry_form')]==2 || $row[csf('entry_form')]==22)
                    {
                    	$booking_plan= $row[csf('booking_no')];
                    }
                    else 
                    { 
                    	$booking_plan= $row[csf('booking_id')];
                    }
				}
					
	            ?>
	                <tr bgcolor="<? echo $bgcolor; ?>" onClick='set_form_data("<? echo $row[csf('id')]."**".$ycount."**".$row[csf('yarn_count')]."**".$brand_arr[$row[csf('brand_id')]]."**".$row[csf('brand_id')]."**".$row[csf('yarn_lot')]."**".$row[csf('rack')]."**".$row[csf('self')]."**".$knit_palan_no."**".$row[csf('stitch_length')]."**".$booking_order_id."**".$row[csf('product_name_details')]."**".$row[csf('store_id')]."**".$row[csf('floor_id')]."**".$row[csf('room')];?>")' style="cursor:pointer">
	                    <td><p><? echo $row[csf('product_name_details')]; ?></p></td>
	                    <td><p><? echo $booking_plan; ?>&nbsp;</p></td>
	                    <td><p><? echo $ycount; ?>&nbsp;</p></td>
	                    <td><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
	                    <td><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
	                    <td><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
	                    <td><p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p></td>
	                    <td><p><? echo $new_array[$row[csf('floor_id')]]; ?>&nbsp;</p></td>
	                    <td><p><? echo $new_array[$row[csf('room')]]; ?>&nbsp;</p></td>
	                    <td><p><? echo $new_array[$row[csf('rack')]]; ?>&nbsp;</p></td>
	                    <td><p><? echo $new_array[$row[csf('self')]]; ?>&nbsp;</p></td>
	                </tr>
	            <? 
	            $i++; 
            } 
            ?>
        </tbody>
    </table>
	<?
	exit();
}

if($action=="populate_data_about_order") // Current Stock
{
	$data=explode("**",$data);
	$order_id=$data[0];
	$prod_id=$data[1];
	$from_program_no=$data[2];
	$company_id=$data[3];
	$transfer_criteria=$data[4];
	$store=$data[5];
	$floor=$data[6];
	$room=$data[7];
	$rack=$data[8];
	$shelf=$data[9];
	//echo $store.'='.$floor.'='.$room.'='.$rack.'='.$shelf;die;
	$sql_cond="";
	if ($store>0) { $sql_cond .= " and c.store_id=$store "; }
	if ($floor>0) { $sql_cond .= " and c.floor_id=$floor "; }
	if ($room>0) { $sql_cond .= " and c.room=$room "; }
	if ($rack>0) { $sql_cond .= " and c.rack=$rack "; }
	if ($shelf>0) { $sql_cond .= " and c.self=$shelf "; }

	$trans_in_sql_cond="";
	if ($store>0) { $trans_in_sql_cond .= " and c.to_store=$store "; }
	if ($floor>0) { $trans_in_sql_cond .= " and c.to_floor_id=$floor "; }
	if ($room>0) { $trans_in_sql_cond .= " and c.to_room=$room "; }
	if ($rack>0) { $trans_in_sql_cond .= " and c.to_rack=$rack "; }
	if ($shelf>0) { $trans_in_sql_cond .= " and c.to_shelf=$shelf "; }

	$trans_out_sql_cond="";
	if ($store>0) { $trans_out_sql_cond .= " and c.from_store=$store "; }
	if ($floor>0) { $trans_out_sql_cond .= " and c.floor_id=$floor "; }
	if ($room>0) { $trans_out_sql_cond .= " and c.room=$room "; }
	if ($rack>0) { $trans_out_sql_cond .= " and c.rack=$rack "; }
	if ($shelf>0) { $trans_out_sql_cond .= " and c.shelf=$shelf "; }

	// echo $sql_cond;die;
	$current_stock=0;
	if ($transfer_criteria==6) // order
	{
		if($from_program_no!="")
		{
			$fabric_store_auto_update=return_field_value("auto_update","variable_settings_production","company_name =$company_id and variable_list=15 and item_category_id=13 and is_deleted=0 and status_active=1");
			
			if($fabric_store_auto_update==1)
			{
				$receive_sql="SELECT b.id, d.po_breakdown_id, d.quantity 
				from inv_receive_master b, inv_transaction c, order_wise_pro_details d
				where b.id=c.mst_id and c.id=d.trans_id and d.trans_id>0 and b.entry_form=2 and d.entry_form=2 and b.receive_basis=2 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.booking_id=$from_program_no and d.po_breakdown_id in($order_id) $sql_cond";
			}
			else
			{
				$receive_sql="SELECT b.id, d.po_breakdown_id, d.quantity 
				from inv_receive_master a, inv_receive_master b, inv_transaction c, order_wise_pro_details d
				where a.id=b.booking_id and b.id=c.mst_id and c.id=d.trans_id and d.trans_id>0 and a.entry_form=2 and b.entry_form=22 and d.entry_form=22 and b.receive_basis=9 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.booking_id=$from_program_no and d.po_breakdown_id in($order_id) $sql_cond";
			}
			//echo $receive_sql;die;
			$receive_result=sql_select($receive_sql);
			$all_rcv_id="";
			foreach($receive_result as $row)
			{
				if($rcv_chaeck[$row[csf('id')]]=="")
				{
					$rcv_chaeck[$row[csf('id')]]=$row[csf('id')];
					$all_rcv_id.=$row[csf('id')].",";
				}
				$current_stock+=$row[csf('quantity')];
			}
			$all_rcv_id=chop($all_rcv_id,",");
			if($all_rcv_id!="")
			{
				$rcv_rtn_sql=" SELECT d.po_breakdown_id, d.quantity 
				from inv_issue_master b, inv_transaction c, order_wise_pro_details d
				where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=3 and b.entry_form=45 and d.entry_form=45 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.received_id in($all_rcv_id) and d.po_breakdown_id in($order_id) $sql_cond";
				//echo $rcv_rtn_sql;die;
				$rcv_rtn_result=sql_select($rcv_rtn_sql);
				foreach($rcv_rtn_result as $row)
				{
					$current_stock-=$row[csf('quantity')];
				}
			}
			
			$issue_sql="SELECT d.po_breakdown_id, d.quantity from inv_grey_fabric_issue_dtls b, inv_transaction c, order_wise_pro_details d 
			where c.id=d.trans_id and b.trans_id=c.id and  b.id=d.dtls_id and b.trans_id>0  and d.trans_id>0 and d.entry_form=16 and b.program_no=$from_program_no and d.po_breakdown_id in($order_id) and c.status_active=1 and d.status_active=1  and b.status_active=1 $sql_cond";
			// echo $issue_sql;
			$issue_result=sql_select($issue_sql);
			foreach($issue_result as $row)
			{
				$current_stock-=$row[csf('quantity')];
			}
			
			$issue_rtn_sql=" SELECT d.po_breakdown_id, d.quantity  
			from inv_receive_master b, inv_transaction c, order_wise_pro_details d 
			where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=4 and b.entry_form=51 and d.entry_form=51 and b.booking_id=$from_program_no and d.po_breakdown_id in($order_id) and b.status_active=1 and c.status_active=1 and d.status_active=1 $sql_cond";
			$issue_rtn_result=sql_select($issue_rtn_sql);
			foreach($issue_rtn_result as $row)
			{
				$current_stock+=$row[csf('quantity')];
			}

			$transfer_in_sql="SELECT d.trans_type, d.po_breakdown_id, d.quantity 
			from inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
			where b.id=c.mst_id and c.id=d.dtls_id  and b.entry_form in(13,81) and d.entry_form in(13,81) and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.from_program=$from_program_no and d.po_breakdown_id in($order_id) and d.trans_type=5 $trans_in_sql_cond";			
			// echo $transfer_in_sql;die;
			// echo $receive_sql.'<br>'.$rcv_rtn_sql.'<br>'.$issue_sql.'<br>'.$issue_rtn_sql.'<br>'.$transfer_in_sql;die;
			
			$transfer_in_result=sql_select($transfer_in_sql);
			foreach($transfer_in_result as $row)
			{
				if($row[csf('trans_type')]==5)
				{
					$current_stock+=$row[csf('quantity')];
				}
			}

			$transfer_out_sql="SELECT d.trans_type, d.po_breakdown_id, d.quantity 
			from inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
			where b.id=c.mst_id and c.id=d.dtls_id  and b.entry_form in(13,80) and d.entry_form in(13,80) and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.from_program=$from_program_no and d.po_breakdown_id in($order_id) and d.trans_type=6 $trans_out_sql_cond";
			// echo $transfer_in_sql.'<br>'.$transfer_out_sql;
			$transfer_out_result=sql_select($transfer_out_sql);
			foreach($transfer_out_result as $row)
			{
				if($row[csf('trans_type')]==6)
				{
					$current_stock-=$row[csf('quantity')];
				}
			}
		}
		else
		{
			$sql=sql_select("SELECT 
			sum(case when b.entry_form in(2,22) then b.quantity end) as grey_fabric_recv, 
            sum(case when b.entry_form in(16) then b.quantity end) as grey_fabric_issued,
            sum(case when b.entry_form=45 then b.quantity end) as grey_fabric_recv_return, 
            sum(case when b.entry_form=51 then b.quantity end) as grey_fabric_issue_return,
            sum(case when b.entry_form in(13,81) and b.trans_type=5 then b.quantity end) as grey_fabric_trans_recv, 
            sum(case when b.entry_form in(13,80) and b.trans_type=6 then b.quantity end) as grey_fabric_trans_issued from order_wise_pro_details b, inv_transaction c 
            where b.trans_id<>0 and b.trans_id=c.id and b.prod_id=$prod_id and b.po_breakdown_id=$order_id
            and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $sql_cond");
					
			$grey_fabric_recv=$sql[0][csf('grey_fabric_recv')]+$sql[0][csf('grey_fabric_trans_recv')]+$sql[0][csf('grey_fabric_issue_return')];
			$grey_fabric_issued=$sql[0][csf('grey_fabric_issued')]+$sql[0][csf('grey_fabric_trans_issued')]+$sql[0][csf('grey_fabric_recv_return')];
			$current_stock=$grey_fabric_recv-$grey_fabric_issued;
		}
	}
	else // sample
	{
		$recv_id='';
		if($db_type==0)
		{
			$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$order_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		else
		{
			$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$order_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		
		if($recv_id=="") $recv_id=0;
		$all_booking_id=$order_id.",".$recv_id;
		$recv_sql = "SELECT sum(qnty) as qnty, sum(qnty2) as qnty2 from 
		(
			select sum(case when a.receive_basis!=9 and a.booking_id=$order_id then b.grey_receive_qnty else 0 end ) as qnty,
			sum(case when a.receive_basis=9 and a.booking_id in($recv_id) then b.grey_receive_qnty else 0 end) as qnty2
			from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_transaction c 
			where a.id=b.mst_id and a.id=b.mst_id and b.trans_id=c.id and a.entry_form in(2,22) and a.booking_without_order=1 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.booking_id in ($all_booking_id) and b.prod_id=$prod_id $sql_cond
			union all
			select sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c 
			where b.id=c.mst_id and b.entry_form=51 and b.receive_purpose=8 and c.item_category=13 and c.transaction_type=4 and b.booking_id=$order_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id $sql_cond
			union all
			select sum(b.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c 
			where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and b.item_category=13 and a.transfer_criteria in(6,8) and a.to_order_id=$order_id and a.status_active=1 and a.is_deleted=0 and b.from_prod_id=$prod_id and a.entry_form in(80,432) and c.transaction_type=5 and b.to_trans_id= c.id and c.prod_id=$prod_id $sql_cond
		) product_details_master"; // Transfer In entry form b.entry_form=80, 432 (Grey Fabric Order To Sample Transfer Entry) // b.transfer_criteria in(6)
				
		$result_recv=sql_select($recv_sql);
		
		$iss_sql="SELECT sum(qnty) as qnty from
		(
			select sum(b.issue_qnty) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, inv_transaction c 
			where a.id=b.mst_id and a.id=b.mst_id and b.trans_id=c.id and a.item_category=13 and a.entry_form=16 and a.issue_purpose in(3,8,26,29,30,31) and a.issue_basis=1 and a.booking_id=$order_id and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 $sql_cond
			union all
			select sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$order_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=13 and b.entry_form=45 and c.transaction_type=3 and c.item_category=13 and c.prod_id=$prod_id $sql_cond
			union all
			select sum(b.transfer_qnty) as qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c 
			where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and a.transfer_criteria in(7,8) and b.item_category=13 and a.from_order_id=$order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(81,432) and b.from_prod_id=$prod_id  and c.transaction_type=6 and b.trans_id= c.id and c.prod_id=$prod_id $sql_cond
		) inv_issue_master"; // Transfer Out entry form b.entry_form=81 (Grey Fabric Sample To Order Transfer Entry) // b.transfer_criteria in(7)
		//echo $recv_sql.'<br>'.$iss_sql;

		$result_iss=sql_select($iss_sql);
		$grey_fabric_recv=$result_recv[0][csf('qnty')]+$result_recv[0][csf('qnty2')];
		$grey_fabric_issued=$result_iss[0][csf('qnty')];
		$current_stock=$grey_fabric_recv-$grey_fabric_issued;
	}
	echo "$('#txt_current_stock').val('".$current_stock."');\n"; 
 	
	exit();	
}

if($action=="populate_stock_data___________")
{
	$data=explode("**",$data);
	$prod_id=$data[0];
	$booking_order_id=$data[1];
	$transfer_criteria=$data[2];
	if ($transfer_criteria==6) // Order
	{
		$sql=sql_select("SELECT sum(case when trans_type in(1,4,5) then quantity else 0 end) as tot_receive,sum(case when trans_type in(2,3,6) then quantity else 0 end) as tot_issue from order_wise_pro_details where status_active=1 and is_deleted=0 and po_breakdown_id=$booking_order_id and prod_id=$prod_id");
		$current_stock=$sql[0][csf("tot_receive")]-$sql[0][csf("tot_issue")];
	}
	else // Sample
	{
		$recv_id='';
		$recv_id='';
		if($db_type==0)
		{
			$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_order_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		else
		{
			$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_order_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
		}
		
		if($recv_id=="") $recv_id=0;
		$all_booking_id=$booking_order_id.",".$recv_id;
		$sql = "SELECT sum(qnty) as qnty, sum(qnty2) as qnty2 from 
		(
		select sum(case when b.receive_basis!=9 and b.booking_id=$booking_order_id then c.grey_receive_qnty else 0 end ) as qnty, sum(case when b.receive_basis=9 and b.booking_id in($recv_id) then c.grey_receive_qnty else 0 end) as qnty2 from inv_receive_master b, pro_grey_prod_entry_dtls c where b.id=c.mst_id and b.entry_form in(2,22) and b.booking_without_order=1 and c.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and b.booking_id in ($all_booking_id) and c.prod_id=$prod_id
		union all
		select sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c where b.id=c.mst_id and b.entry_form=51 and b.receive_purpose=8 and c.item_category=13 and c.transaction_type=4 and b.booking_id=$booking_order_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id
		union all
		select sum(c.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and c.item_category=13 and b.transfer_criteria=6 and b.to_order_id=$booking_order_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
		) product_details_master";
				
		$result=sql_select($sql);
		
		$iss_sql="SELECT sum(qnty) as qnty from
		(
		select sum(c.issue_qnty) as qnty from inv_issue_master b, inv_grey_fabric_issue_dtls c where b.id=c.mst_id and b.item_category=13 and b.entry_form=16 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$booking_order_id and c.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0
		union all
		select sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$booking_order_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=13 and b.entry_form=45 and c.transaction_type=3 and c.item_category=13 and c.prod_id=$prod_id 
		union all
		select sum(c.transfer_qnty) as qnty from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and b.transfer_criteria=7 and c.item_category=13 and b.from_order_id=$booking_order_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
		) inv_issue_master";
		$result_iss=sql_select($iss_sql);
				
		$grey_fabric_recv=$result[0][csf('qnty')]+$result[0][csf('qnty2')];
		$grey_fabric_issued=$result_iss[0][csf('qnty')];
		$current_stock=$grey_fabric_recv-$grey_fabric_issued;
	}
	echo "document.getElementById('txt_current_stock').value 	= '".$current_stock."';\n";
	exit;
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
	                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'grey_fabric_sample_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
 	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=13 and company_id=$company_id and transfer_criteria=$transfer_criteria and $search_field like '$search_string' and transfer_criteria in(6,7,8) and status_active=1 and is_deleted=0 and entry_form in (80,81,432) order by id desc";
	
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,110,90,130","760","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("SELECT transfer_system_id,challan_no, company_id, transfer_date, item_category, from_order_id, to_order_id,transfer_criteria from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 				= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";

		echo "get_php_form_data('".$row[csf("from_order_id")]."**from**".$row[csf("transfer_criteria")]."'".",'populate_data_from_sample','requires/grey_fabric_sample_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to**".$row[csf("transfer_criteria")]."'".",'populate_data_to_order','requires/grey_fabric_sample_transfer_controller');\n";

		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
	}
}

if($action=="show_transfer_listview")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	
	//$sql="select id, from_prod_id, transfer_qnty, item_category, uom, to_rack as rack, to_shelf as shelf from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$sql="SELECT a.company_id, a.from_order_id, b.id, b.from_prod_id, b.transfer_qnty, b.item_category, b.uom, b.to_rack as rack, b.to_shelf as shelf from inv_item_transfer_mst a,  inv_item_transfer_dtls b where a.id=b. mst_id and a.id='$data' and b.status_active = '1' and b.is_deleted = '0'";
	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement);
	 
	echo create_list_view("list_view", "Item Category,Item Description,Transfered Qnty,UOM, Rack, Shelf", "120,250,100,70,80","730","200",0, $sql, "get_php_form_data", "id,from_order_id,from_prod_id,company_id", "'populate_transfer_details_form_data'", 0, "item_category,from_prod_id,0,uom,0,0", $arr, "item_category,from_prod_id,transfer_qnty,uom,rack,shelf", "requires/grey_fabric_sample_transfer_controller",'','0,0,2,0,0,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	
	$data=explode("_",$data);
	$booking_id=$data[1];
	$prod_id=$data[2];
	$dtls_id=$data[0];
	$company_id=$data[3];

	/*$recv_id='';
	if($db_type==0)
	{
		$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
	}
	else
	{
		$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
	}
	
	if($recv_id=="") $recv_id=0;
	$all_booking_id=$booking_id.",".$recv_id;
	$sql = "select sum(qnty) as qnty, sum(qnty2) as qnty2 from 
	(
	select sum(case when b.receive_basis!=9 and b.booking_id=$booking_id then c.grey_receive_qnty else 0 end ) as qnty, sum(case when b.receive_basis=9 and b.booking_id in($recv_id) then c.grey_receive_qnty else 0 end) as qnty2 from inv_receive_master b, pro_grey_prod_entry_dtls c where b.id=c.mst_id and b.entry_form in(2,22) and b.booking_without_order=1 and c.trans_id!=0 and b.status_active=1 and b.is_deleted=0 and b.booking_id in ($all_booking_id) and c.prod_id=$prod_id
	union all
	select sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c where b.id=c.mst_id and b.entry_form=51 and b.receive_purpose=8 and c.item_category=13 and c.transaction_type=4 and b.booking_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id
	union all
	select sum(c.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and c.item_category=13 and b.transfer_criteria=6 and b.to_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
	) product_details_master";
			
	$result=sql_select($sql);
	
	$iss_sql="select sum(qnty) as qnty from
	(
	select sum(c.issue_qnty) as qnty from inv_issue_master b, inv_grey_fabric_issue_dtls c where b.id=c.mst_id and b.item_category=13 and b.entry_form=16 and b.issue_purpose in(3,8,26,29,30,31) and b.issue_basis=1 and b.booking_id=$booking_id and c.prod_id=$prod_id and b.status_active=1 and b.is_deleted=0
	union all
	select sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=13 and b.entry_form=45 and c.transaction_type=3 and c.item_category=13 and c.prod_id=$prod_id 
	union all
	select sum(c.transfer_qnty) as qnty from inv_item_transfer_mst b, inv_item_transfer_dtls c where b.id=c.mst_id and b.transfer_criteria=7 and c.item_category=13 and b.from_order_id=$booking_id and b.status_active=1 and b.is_deleted=0 and c.from_prod_id=$prod_id
	) inv_issue_master";
	$result_iss=sql_select($iss_sql);
			
	$grey_fabric_recv=$result[0][csf('qnty')]+$result[0][csf('qnty2')];
	$grey_fabric_issued=$result_iss[0][csf('qnty')];
	$current_stock=$grey_fabric_recv-$grey_fabric_issued;*/
	
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$data_array=sql_select("SELECT id, mst_id, from_prod_id, transfer_qnty, roll, item_category, uom, y_count, yarn_lot, brand_id, from_store,floor_id,room,rack,shelf, to_store,to_floor_id,to_room,to_rack,to_shelf, from_program,to_program,stitch_length, trans_id, to_trans_id from inv_item_transfer_dtls where id='$dtls_id'");
	foreach ($data_array as $row)
	{ 
		$ycount='';
		$count_id=explode(',',$row[csf('y_count')]);
		foreach($count_id as $count)
		{
			if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
		}

		echo "get_php_form_data('".$company_id."_".$row[csf("from_store")]."_".$row[csf("floor_id")]."_".$row[csf("room")]."_".$row[csf("rack")]."_".$row[csf("shelf")]."'".",'populate_store_floor_room_rack_self_data','requires/grey_fabric_sample_transfer_controller');\n";

		if($row[csf("to_store")]>0){			
			echo "load_drop_down('requires/grey_fabric_sample_transfer_controller','".$company_id."', 'load_drop_down_store_to', 'to_store_td' );\n";
			echo "document.getElementById('cbo_store_name_to').value 		= '".$row[csf("to_store")]."';\n";
		}
		if($row[csf("to_floor_id")]>0){		
			$store_com=$row[csf("to_store")]."_".$company_id;	
			echo "load_drop_down('requires/grey_fabric_sample_transfer_controller','".$store_com."', 'load_drop_down_floor_to', 'floor_td_to' );\n";
			echo "document.getElementById('cbo_floor_to').value 		= '".$row[csf("to_floor_id")]."';\n";
		}
		if($row[csf("to_room")]>0){		
			$floor_com_store=$row[csf("to_floor_id")]."_".$company_id."_".$row[csf("to_store")];	
			echo "load_drop_down('requires/grey_fabric_sample_transfer_controller','".$floor_com_store."', 'load_drop_down_room_to', 'room_td_to' );\n";
			echo "document.getElementById('cbo_room_to').value 		= '".$row[csf("to_room")]."';\n";
		}
		if($row[csf("to_rack")]>0){		
			$room_floor_com_store=$row[csf("to_room")]."_".$company_id."_".$row[csf("to_store")]."_".$row[csf("to_floor_id")];
			echo "load_drop_down('requires/grey_fabric_sample_transfer_controller','".$room_floor_com_store."', 'load_drop_down_rack_to', 'rack_td_to' );\n";
			echo "document.getElementById('txt_rack_to').value 		= '".$row[csf("to_rack")]."';\n";
		}
		if($row[csf("to_shelf")]>0){		
			$rack_room_floor_com_store=$row[csf("to_rack")]."_".$company_id."_".$row[csf("to_store")]."_".$row[csf("to_floor_id")]."_".$row[csf("to_room")];
			echo "load_drop_down('requires/grey_fabric_sample_transfer_controller','".$rack_room_floor_com_store."', 'load_drop_down_shelf_to', 'shelf_td_to' );\n";
			echo "document.getElementById('txt_shelf_to').value 		= '".$row[csf("to_shelf")]."';\n";
		}

		echo "document.getElementById('update_dtls_id').value 			= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 			= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_prod_id').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('previous_trans_qnty').value 		= '".$row[csf("transfer_qnty")]."';\n";
		//$current_stock=$current_stock+$row[csf("transfer_qnty")];
		//echo "document.getElementById('txt_current_stock').value 		= '".$current_stock."';\n";
		echo "document.getElementById('cbo_item_category').value 		= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_uom').value 					= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_roll').value 				= '".$row[csf("roll")]."';\n";
		echo "document.getElementById('txt_ycount').value 				= '".$ycount."';\n";
		echo "document.getElementById('hid_ycount').value 				= '".$row[csf("y_count")]."';\n";
		echo "document.getElementById('txt_ybrand').value 				= '".$brand_arr[$row[csf('brand_id')]]."';\n";
		echo "document.getElementById('hid_ybrand').value 				= '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_ylot').value 				= '".$row[csf("yarn_lot")]."';\n";

		echo "document.getElementById('txt_rack_to').value 				= '".$row[csf("to_rack")]."';\n";
		echo "document.getElementById('txt_shelf_to').value 			= '".$row[csf("to_shelf")]."';\n";
		echo "document.getElementById('txt_rack').value 				= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_shelf').value 				= '".$row[csf("shelf")]."';\n";

		echo "document.getElementById('txt_form_prog').value 			= '".$row[csf("from_program")]."';\n";
		echo "document.getElementById('txt_to_prog').value 				= '".$row[csf("to_program")]."';\n";
		echo "document.getElementById('stitch_length').value 			= '".$row[csf("stitch_length")]."';\n";
		echo "populate_stock();\n";
		/*$sql_trans=sql_select("SELECT b.id as trans_id, b.transaction_type  from  inv_item_transfer_dtls a, inv_transaction b where a.mst_id=b.mst_id and a.id=".$row[csf('id')]." and b.transaction_type in(5,6) order by b.transaction_type DESC");*/
		echo "document.getElementById('update_trans_issue_id').value 	= '".$row[csf('trans_id')]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 	= '".$row[csf('to_trans_id')]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
		exit();
	}
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$order_booking_id=str_replace("'","",$txt_from_order_book_id); // $txt_sam_book_id
	$prod_id=str_replace("'","",$cbo_item_desc); 
	$trans_date=str_replace("'","",$txt_transfer_date); 
	$transfer_criteria=str_replace("'","",$cbo_transfer_criteria);
	$from_program_no=str_replace("'","",$txt_form_prog);

	$cbo_store_name=str_replace("'","",$cbo_store_name);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$cbo_room=str_replace("'","",$cbo_room);
	$txt_rack=str_replace("'","",$txt_rack);
	$txt_shelf=str_replace("'","",$txt_shelf);
	if ($cbo_store_name=="") { $cbo_store_name = 0;}
	if ($cbo_floor=="") { $cbo_floor = 0;}
	if ($cbo_room=="") { $cbo_room = 0;}
	if ($txt_rack=="") { $txt_rack = 0;}
	if ($txt_shelf=="") { $txt_shelf = 0;}

	$cbo_store_name_to=str_replace("'","",$cbo_store_name_to);
	$cbo_floor_to=str_replace("'","",$cbo_floor_to);
	$cbo_room_to=str_replace("'","",$cbo_room_to);
	$txt_rack_to=str_replace("'","",$txt_rack_to);
	$txt_shelf_to=str_replace("'","",$txt_shelf_to);
	if ($cbo_store_name_to=="") { $cbo_store_name_to = 0;}
	if ($cbo_floor_to=="") { $cbo_floor_to = 0;}
	if ($cbo_room_to=="") { $cbo_room_to = 0;}
	if ($txt_rack_to=="") { $txt_rack_to = 0;}
	if ($txt_shelf_to=="") { $txt_shelf_to = 0;}

	$sql_cond="";
	if ($store>0) { $sql_cond .= " and c.store_id=$store "; }
	if ($floor>0) { $sql_cond .= " and c.floor_id=$floor "; }
	if ($room>0) { $sql_cond .= " and c.room=$room "; }
	if ($rack>0) { $sql_cond .= " and c.rack=$rack "; }
	if ($shelf>0) { $sql_cond .= " and c.self=$shelf "; }

	$trans_in_sql_cond="";
	if ($store>0) { $trans_in_sql_cond .= " and c.to_store=$store "; }
	if ($floor>0) { $trans_in_sql_cond .= " and c.to_floor_id=$floor "; }
	if ($room>0) { $trans_in_sql_cond .= " and c.to_room=$room "; }
	if ($rack>0) { $trans_in_sql_cond .= " and c.to_rack=$rack "; }
	if ($shelf>0) { $trans_in_sql_cond .= " and c.to_shelf=$shelf "; }

	$trans_out_sql_cond="";
	if ($store>0) { $trans_out_sql_cond .= " and c.from_store=$store "; }
	if ($floor>0) { $trans_out_sql_cond .= " and c.floor_id=$floor "; }
	if ($room>0) { $trans_out_sql_cond .= " and c.room=$room "; }
	if ($rack>0) { $trans_out_sql_cond .= " and c.rack=$rack "; }
	if ($shelf>0) { $trans_out_sql_cond .= " and c.shelf=$shelf "; }
	
    $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5)", "max_date");
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$trans_date = date("Y-m-d", strtotime($trans_date));
	if ($trans_date < $max_recv_date) 
    {
        echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
        die;
	}

	if($transfer_criteria==8) // Sample to Sample
	{
		$entry_form_no = 432;
		$short_prefix_name="GFSTSTE";
	}
	elseif($transfer_criteria==7) // Sample to Order
	{
		$entry_form_no = 81;
		$short_prefix_name="GFSTOTE";
	}
	else{
		$entry_form_no = 80; // // Order to Sample
		$short_prefix_name="GFOTSTE";
	}
    //echo "10**".$entry_form_no;die;
        
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';
		$first_rcv_date=return_field_value("min(b.transaction_date) as transaction_date","inv_receive_master a, inv_transaction b","a.id=b.mst_id and b.transaction_type=1 and a.booking_id=$order_booking_id and b.prod_id=$prod_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","transaction_date");
		$first_rcv_date=strtotime($first_rcv_date);
		$trans_date=strtotime($trans_date);
		if($trans_date<$first_rcv_date)
		{
			echo "40**Transfer Date Lower Then First Received Date";die;
		}
		
		// ================= current stock check start ============================		
		if ($transfer_criteria == 6) // order
		{
			if($from_program_no!="")
			{
				$fabric_store_auto_update=return_field_value("auto_update","variable_settings_production","company_name =$cbo_company_id and variable_list=15 and item_category_id=13 and is_deleted=0 and status_active=1");
				
				if($fabric_store_auto_update==1)
				{
					$receive_sql="SELECT b.id, d.po_breakdown_id, d.quantity 
					from inv_receive_master b, inv_transaction c, order_wise_pro_details d
					where b.id=c.mst_id and c.id=d.trans_id and d.trans_id>0 and b.entry_form=2 and d.entry_form=2 and b.receive_basis=2 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.booking_id=$from_program_no and d.po_breakdown_id in($order_booking_id) $sql_cond";
				}
				else
				{
					$receive_sql="SELECT b.id, d.po_breakdown_id, d.quantity 
					from inv_receive_master a, inv_receive_master b, inv_transaction c, order_wise_pro_details d
					where a.id=b.booking_id and b.id=c.mst_id and c.id=d.trans_id and d.trans_id>0 and a.entry_form=2 and b.entry_form=22 and d.entry_form=22 and b.receive_basis=9 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.booking_id=$from_program_no and d.po_breakdown_id in($order_booking_id) $sql_cond";
				}
				//echo $receive_sql;die;
				$receive_result=sql_select($receive_sql);
				$all_rcv_id="";
				foreach($receive_result as $row)
				{
					if($rcv_chaeck[$row[csf('id')]]=="")
					{
						$rcv_chaeck[$row[csf('id')]]=$row[csf('id')];
						$all_rcv_id.=$row[csf('id')].",";
					}
					$current_stock+=$row[csf('quantity')];
				}
				$all_rcv_id=chop($all_rcv_id,",");
				if($all_rcv_id!="")
				{
					$rcv_rtn_sql=" SELECT d.po_breakdown_id, d.quantity 
					from inv_issue_master b, inv_transaction c, order_wise_pro_details d
					where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=3 and b.entry_form=45 and d.entry_form=45 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.received_id in($all_rcv_id) and d.po_breakdown_id in($order_booking_id) $sql_cond";
					//echo $rcv_rtn_sql;die;
					$rcv_rtn_result=sql_select($rcv_rtn_sql);
					foreach($rcv_rtn_result as $row)
					{
						$current_stock-=$row[csf('quantity')];
					}
				}
				
				$issue_sql="SELECT d.po_breakdown_id, d.quantity from inv_grey_fabric_issue_dtls b, inv_transaction c, order_wise_pro_details d 
				where c.id=d.trans_id and b.trans_id=c.id and  b.id=d.dtls_id and b.trans_id>0  and d.trans_id>0 and d.entry_form=16 and b.program_no=$from_program_no and d.po_breakdown_id in($order_booking_id) and c.status_active=1 and d.status_active=1  and b.status_active=1 $sql_cond";
				// echo $issue_sql;
				$issue_result=sql_select($issue_sql);
				foreach($issue_result as $row)
				{
					$current_stock-=$row[csf('quantity')];
				}
				
				$issue_rtn_sql=" SELECT d.po_breakdown_id, d.quantity  
				from inv_receive_master b, inv_transaction c, order_wise_pro_details d 
				where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=4 and b.entry_form=51 and d.entry_form=51 and b.booking_id=$from_program_no and d.po_breakdown_id in($order_booking_id) and b.status_active=1 and c.status_active=1 and d.status_active=1 $sql_cond";
				$issue_rtn_result=sql_select($issue_rtn_sql);
				foreach($issue_rtn_result as $row)
				{
					$current_stock+=$row[csf('quantity')];
				}

				$transfer_in_sql="SELECT d.trans_type, d.po_breakdown_id, d.quantity 
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
				where b.id=c.mst_id and c.id=d.dtls_id  and b.entry_form in(13,81) and d.entry_form in(13,81) and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.from_program=$from_program_no and d.po_breakdown_id in($order_booking_id) and d.trans_type=5 $trans_in_sql_cond";			
				// echo $transfer_in_sql;die;
				// echo $receive_sql.'<br>'.$rcv_rtn_sql.'<br>'.$issue_sql.'<br>'.$issue_rtn_sql.'<br>'.$transfer_in_sql;die;
				
				$transfer_in_result=sql_select($transfer_in_sql);
				foreach($transfer_in_result as $row)
				{
					if($row[csf('trans_type')]==5)
					{
						$current_stock+=$row[csf('quantity')];
					}
				}

				$transfer_out_sql="SELECT d.trans_type, d.po_breakdown_id, d.quantity 
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
				where b.id=c.mst_id and c.id=d.dtls_id  and b.entry_form in(13,80) and d.entry_form in(13,80) and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.from_program=$from_program_no and d.po_breakdown_id in($order_booking_id) and d.trans_type=6 $trans_out_sql_cond";

				$transfer_out_result=sql_select($transfer_out_sql);
				foreach($transfer_out_result as $row)
				{
					if($row[csf('trans_type')]==6)
					{
						$current_stock-=$row[csf('quantity')];
					}
				}
			}
			else
			{
				$sql=sql_select("SELECT 
				sum(case when b.entry_form in(2,22) then b.quantity end) as grey_fabric_recv, 
	            sum(case when b.entry_form in(16) then b.quantity end) as grey_fabric_issued,
	            sum(case when b.entry_form=45 then b.quantity end) as grey_fabric_recv_return, 
	            sum(case when b.entry_form=51 then b.quantity end) as grey_fabric_issue_return,
	            sum(case when b.entry_form in(13,81) and b.trans_type=5 then b.quantity end) as grey_fabric_trans_recv, 
	            sum(case when b.entry_form in(13,80) and b.trans_type=6 then b.quantity end) as grey_fabric_trans_issued from order_wise_pro_details b, inv_transaction c 
	            where b.trans_id<>0 and b.trans_id=c.id and b.prod_id=$prod_id and b.po_breakdown_id=$order_booking_id
	            and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $sql_cond");
						
				$grey_fabric_recv=$sql[0][csf('grey_fabric_recv')]+$sql[0][csf('grey_fabric_trans_recv')]+$sql[0][csf('grey_fabric_issue_return')];
				$grey_fabric_issued=$sql[0][csf('grey_fabric_issued')]+$sql[0][csf('grey_fabric_trans_issued')]+$sql[0][csf('grey_fabric_recv_return')];
				$current_stock=$grey_fabric_recv-$grey_fabric_issued;
			}
			// echo "5**Stock quantity $current_stock";die;
			$trans_qnty=str_replace("'","",$txt_transfer_qnty);
			if($trans_qnty>$current_stock)
			{
				echo "5**Transfer Quantity Not Allow More Then Order Stock quantity";die;
			}
		}
		else // sample
		{
			$recv_id='';
			if($db_type==0)
			{
				$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$order_booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
			}
			else
			{
				$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$order_booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
			}
			
			if($recv_id=="") $recv_id=0;
			$all_booking_id=$order_booking_id.",".$recv_id;
			$recv_sql = "SELECT sum(qnty) as qnty, sum(qnty2) as qnty2 from 
			(
				select sum(case when a.receive_basis!=9 and a.booking_id=$order_booking_id then b.grey_receive_qnty else 0 end ) as qnty,
				sum(case when a.receive_basis=9 and a.booking_id in($recv_id) then b.grey_receive_qnty else 0 end) as qnty2
				from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_transaction c 
				where a.id=b.mst_id and a.id=b.mst_id and b.trans_id=c.id and a.entry_form in(2,22) and a.booking_without_order=1 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.booking_id in ($all_booking_id) and b.prod_id=$prod_id $sql_cond
				union all
				select sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c 
				where b.id=c.mst_id and b.entry_form=51 and b.receive_purpose=8 and c.item_category=13 and c.transaction_type=4 and b.booking_id=$order_booking_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id $sql_cond
				union all
				select sum(b.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c 
				where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and b.item_category=13 and a.transfer_criteria in(6,8) and a.to_order_id=$order_booking_id and a.status_active=1 and a.is_deleted=0 and b.from_prod_id=$prod_id and a.entry_form in(80,432) and c.transaction_type=5 and c.prod_id=$prod_id $sql_cond
			) product_details_master"; // Transfer In entry form b.entry_form=80, 432 (Grey Fabric Order To Sample Transfer Entry) // b.transfer_criteria in(6)
					
			$result_recv=sql_select($recv_sql);
			
			$iss_sql="SELECT sum(qnty) as qnty from
			(
				select sum(b.issue_qnty) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, inv_transaction c 
				where a.id=b.mst_id and a.id=b.mst_id and b.trans_id=c.id and a.item_category=13 and a.entry_form=16 and a.issue_purpose in(3,8,26,29,30,31) and a.issue_basis=1 and a.booking_id=$order_booking_id and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 $sql_cond
				union all
				select sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$order_booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=13 and b.entry_form=45 and c.transaction_type=3 and c.item_category=13 and c.prod_id=$prod_id $sql_cond
				union all
				select sum(b.transfer_qnty) as qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c 
				where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and a.transfer_criteria in(7,8) and b.item_category=13 and a.from_order_id=$order_booking_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(81,432) and b.from_prod_id=$prod_id and c.transaction_type=6 and c.prod_id=$prod_id $sql_cond
			) inv_issue_master"; // Transfer Out entry form b.entry_form=81 (Grey Fabric Sample To Order Transfer Entry) // b.transfer_criteria in(7)
			//echo $recv_sql.'<br>'.$iss_sql;

			$result_iss=sql_select($iss_sql);
			$grey_fabric_recv=$result_recv[0][csf('qnty')]+$result_recv[0][csf('qnty2')];
			$grey_fabric_issued=$result_iss[0][csf('qnty')];
			$current_stock=$grey_fabric_recv-$grey_fabric_issued;
			$trans_qnty=str_replace("'","",$txt_transfer_qnty);
			// echo "5**Stock quantity $current_stock";die;
			if($trans_qnty>$current_stock)
			{
				echo "5**Transfer Quantity Not Allow More Then Sample Stock quantity";die;
			}
		}
		// echo "5**Stock quantity $current_stock";die;
		// ================= current stock check end ============================
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
		 	
			//$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;			
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
            //print_r($id); die;
            $new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,$short_prefix_name,$entry_form_no,date("Y",time()),13 ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, item_category, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$entry_form_no.",".$cbo_transfer_criteria.",0,".$txt_from_order_book_id.",".$txt_to_order_book_id.",".$cbo_item_category.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_book_id."*".$txt_to_order_book_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, store_id, floor_id, room, rack, self, program_no, stitch_length, inserted_by, insert_date";
		
		$data_array_trans="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",6,".$txt_transfer_date.",".$txt_from_order_book_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$txt_form_prog.",".$stitch_length.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_trans_recv=$id_trans+1; 
		$id_trans_recv=return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$data_array_trans.=",(".$id_trans_recv.",".$transfer_update_id.",".$cbo_company_id.",".$cbo_item_desc.",".$cbo_item_category.",5,".$txt_transfer_date.",".$txt_to_order_book_id.",".$cbo_uom.",".$txt_transfer_qnty.",'".$rate."','".$amount."',".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$txt_to_prog.",".$stitch_length.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		// echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		/*$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} */
		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ; 
		$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
		$field_array_dtls="id, mst_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot,from_store,floor_id,room,rack,shelf, to_store,to_floor_id,to_room,to_rack,to_shelf,trans_id,to_trans_id,from_program, to_program, stitch_length, inserted_by, insert_date";
		
		$data_array_dtls="(".$id_dtls.",".$transfer_update_id.",".$cbo_item_desc.",".$cbo_item_category.",".$txt_transfer_qnty.",".$txt_roll.",'".$rate."','".$amount."',".$cbo_uom.",".$hid_ycount.",".$hid_ybrand.",".$txt_ylot.",".$cbo_store_name.",".$cbo_floor.",".$cbo_room.",".$txt_rack.",".$txt_shelf.",".$cbo_store_name_to.",".$cbo_floor_to.",".$cbo_room_to.",".$txt_rack_to.",".$txt_shelf_to.",".$id_trans.",".$id_trans_recv.",".$txt_form_prog.",".$txt_to_prog.",".$stitch_length.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		/*$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} */

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		/*$data_array_prop="(".$id_prop.",".$id_trans.",6,81,".$id_dtls.",".$txt_from_order_book_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		$id_prop=$id_prop+1;*/
		if ($transfer_criteria == 6) // order to sample
		{
			$data_array_prop="(".$id_prop.",".$id_trans.",6,80,".$id_dtls.",".$txt_from_order_book_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
			//$id_prop=$id_prop+1;
			/*$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.=",(".$id_prop.",".$id_trans_recv.",5,13,".$id_dtls.",".$txt_to_order_book_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/
		}

		if ($transfer_criteria == 7) // sample to order 
		{
			$data_array_prop="(".$id_prop.",".$id_trans_recv.",5,81,".$id_dtls.",".$txt_to_order_book_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}
		
		if(str_replace("'","",$update_id)=="")
		{
		    //echo "10** insert into inv_item_transfer_mst ($field_array ) values $data_array";die;
			$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; 
		}
        //echo "10** insert into inv_transaction ($field_array_trans ) values $data_array_trans";die;
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		//echo "10**".$rID2;die;
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		} 
		//echo $flag;die;
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if ($transfer_criteria == 6 ||$transfer_criteria == 7)
		{
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
		}
		//echo $flag;die;
        // echo "10** $rID=$rID2=$rID3=$rID4";oci_rollback($con);die;
		
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
				echo "5**0**"."&nbsp; sumon"."**0";
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
				echo "5**0**"."&nbsp; fiq"."**0";
			}
		}
		disconnect($con);
		die;
	}	
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$first_rcv_date=return_field_value("min(b.transaction_date) as transaction_date","inv_receive_master a, inv_transaction b","a.id=b.mst_id and b.transaction_type=1 and a.booking_id=$order_booking_id and b.prod_id=$prod_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","transaction_date");
		$first_rcv_date=strtotime($first_rcv_date);
		$trans_date=strtotime($trans_date);
		if($trans_date<$first_rcv_date)
		{
			echo "40**Transfer Date Lower Then First Received Date";die;
		}
		// ================= current stock check start ============================		
		if ($transfer_criteria == 6) // order
		{
			if($from_program_no!="")
			{
				$fabric_store_auto_update=return_field_value("auto_update","variable_settings_production","company_name =$cbo_company_id and variable_list=15 and item_category_id=13 and is_deleted=0 and status_active=1");
				
				if($fabric_store_auto_update==1)
				{
					$receive_sql="SELECT b.id, d.po_breakdown_id, d.quantity 
					from inv_receive_master b, inv_transaction c, order_wise_pro_details d
					where b.id=c.mst_id and c.id=d.trans_id and d.trans_id>0 and b.entry_form=2 and d.entry_form=2 and b.receive_basis=2 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.booking_id=$from_program_no and d.po_breakdown_id in($order_booking_id) $sql_cond";
				}
				else
				{
					$receive_sql="SELECT b.id, d.po_breakdown_id, d.quantity 
					from inv_receive_master a, inv_receive_master b, inv_transaction c, order_wise_pro_details d
					where a.id=b.booking_id and b.id=c.mst_id and c.id=d.trans_id and d.trans_id>0 and a.entry_form=2 and b.entry_form=22 and d.entry_form=22 and b.receive_basis=9 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.booking_id=$from_program_no and d.po_breakdown_id in($order_booking_id) $sql_cond";
				}
				//echo $receive_sql;die;
				$receive_result=sql_select($receive_sql);
				$all_rcv_id="";
				foreach($receive_result as $row)
				{
					if($rcv_chaeck[$row[csf('id')]]=="")
					{
						$rcv_chaeck[$row[csf('id')]]=$row[csf('id')];
						$all_rcv_id.=$row[csf('id')].",";
					}
					$current_stock+=$row[csf('quantity')];
				}
				$all_rcv_id=chop($all_rcv_id,",");
				if($all_rcv_id!="")
				{
					$rcv_rtn_sql=" SELECT d.po_breakdown_id, d.quantity 
					from inv_issue_master b, inv_transaction c, order_wise_pro_details d
					where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=3 and b.entry_form=45 and d.entry_form=45 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.received_id in($all_rcv_id) and d.po_breakdown_id in($order_booking_id) $sql_cond";
					//echo $rcv_rtn_sql;die;
					$rcv_rtn_result=sql_select($rcv_rtn_sql);
					foreach($rcv_rtn_result as $row)
					{
						$current_stock-=$row[csf('quantity')];
					}
				}
				
				$issue_sql="SELECT d.po_breakdown_id, d.quantity from inv_grey_fabric_issue_dtls b, inv_transaction c, order_wise_pro_details d 
				where c.id=d.trans_id and b.trans_id=c.id and  b.id=d.dtls_id and b.trans_id>0  and d.trans_id>0 and d.entry_form=16 and b.program_no=$from_program_no and d.po_breakdown_id in($order_booking_id) and c.status_active=1 and d.status_active=1  and b.status_active=1 $sql_cond";
				// echo $issue_sql;
				$issue_result=sql_select($issue_sql);
				foreach($issue_result as $row)
				{
					$current_stock-=$row[csf('quantity')];
				}
				
				$issue_rtn_sql=" SELECT d.po_breakdown_id, d.quantity  
				from inv_receive_master b, inv_transaction c, order_wise_pro_details d 
				where b.id=c.mst_id and c.id=d.trans_id and c.transaction_type=4 and b.entry_form=51 and d.entry_form=51 and b.booking_id=$from_program_no and d.po_breakdown_id in($order_booking_id) and b.status_active=1 and c.status_active=1 and d.status_active=1 $sql_cond";
				$issue_rtn_result=sql_select($issue_rtn_sql);
				foreach($issue_rtn_result as $row)
				{
					$current_stock+=$row[csf('quantity')];
				}

				$transfer_in_sql="SELECT d.trans_type, d.po_breakdown_id, d.quantity 
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
				where b.id=c.mst_id and c.id=d.dtls_id  and b.entry_form in(13,81) and d.entry_form in(13,81) and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.from_program=$from_program_no and d.po_breakdown_id in($order_booking_id) and d.trans_type=5 $trans_in_sql_cond";			
				// echo $transfer_in_sql;die;
				// echo $receive_sql.'<br>'.$rcv_rtn_sql.'<br>'.$issue_sql.'<br>'.$issue_rtn_sql.'<br>'.$transfer_in_sql;die;
				
				$transfer_in_result=sql_select($transfer_in_sql);
				foreach($transfer_in_result as $row)
				{
					if($row[csf('trans_type')]==5)
					{
						$current_stock+=$row[csf('quantity')];
					}
				}

				$transfer_out_sql="SELECT d.trans_type, d.po_breakdown_id, d.quantity 
				from inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
				where b.id=c.mst_id and c.id=d.dtls_id  and b.entry_form in(13,80) and d.entry_form in(13,80) and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.from_program=$from_program_no and d.po_breakdown_id in($order_booking_id) and d.trans_type=6 $trans_out_sql_cond";

				$transfer_out_result=sql_select($transfer_out_sql);
				foreach($transfer_out_result as $row)
				{
					if($row[csf('trans_type')]==6)
					{
						$current_stock-=$row[csf('quantity')];
					}
				}
			}
			else
			{
				$sql=sql_select("SELECT 
				sum(case when b.entry_form in(2,22) then b.quantity end) as grey_fabric_recv, 
	            sum(case when b.entry_form in(16) then b.quantity end) as grey_fabric_issued,
	            sum(case when b.entry_form=45 then b.quantity end) as grey_fabric_recv_return, 
	            sum(case when b.entry_form=51 then b.quantity end) as grey_fabric_issue_return,
	            sum(case when b.entry_form in(13,81) and b.trans_type=5 then b.quantity end) as grey_fabric_trans_recv, 
	            sum(case when b.entry_form in(13,80) and b.trans_type=6 then b.quantity end) as grey_fabric_trans_issued from order_wise_pro_details b, inv_transaction c 
	            where b.trans_id<>0 and b.trans_id=c.id and b.prod_id=$prod_id and b.po_breakdown_id=$order_booking_id
	            and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 $sql_cond");
						
				$grey_fabric_recv=$sql[0][csf('grey_fabric_recv')]+$sql[0][csf('grey_fabric_trans_recv')]+$sql[0][csf('grey_fabric_issue_return')];
				$grey_fabric_issued=$sql[0][csf('grey_fabric_issued')]+$sql[0][csf('grey_fabric_trans_issued')]+$sql[0][csf('grey_fabric_recv_return')];
				$current_stock=$grey_fabric_recv-$grey_fabric_issued;
			}
			// echo "5**Stock quantity $current_stock";die;
			$trans_qnty=str_replace("'","",$txt_transfer_qnty);
			if($trans_qnty>$current_stock)
			{
				echo "5**Transfer Quantity Not Allow More Then Order Stock quantity";die;
			}
		}
		else // sample
		{
			$recv_id='';
			if($db_type==0)
			{
				$recv_id=return_field_value("group_concat(distinct(id)) as id","inv_receive_master","booking_without_order=1 and booking_id=$order_booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
			}
			else
			{
				$recv_id=return_field_value("LISTAGG(id, ',') WITHIN GROUP (ORDER BY id) as id","inv_receive_master","booking_without_order=1 and booking_id=$order_booking_id and status_active=1 and is_deleted=0 and entry_form in(2)","id");
			}
			
			if($recv_id=="") $recv_id=0;
			$all_booking_id=$order_booking_id.",".$recv_id;
			$recv_sql = "SELECT sum(qnty) as qnty, sum(qnty2) as qnty2 from 
			(
				select sum(case when a.receive_basis!=9 and a.booking_id=$order_booking_id then b.grey_receive_qnty else 0 end ) as qnty,
				sum(case when a.receive_basis=9 and a.booking_id in($recv_id) then b.grey_receive_qnty else 0 end) as qnty2
				from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_transaction c 
				where a.id=b.mst_id and a.id=b.mst_id and b.trans_id=c.id and a.entry_form in(2,22) and a.booking_without_order=1 and b.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and a.booking_id in ($all_booking_id) and b.prod_id=$prod_id $sql_cond
				union all
				select sum(c.cons_quantity) as qnty, 0 as qnty2 from inv_receive_master b, inv_transaction c 
				where b.id=c.mst_id and b.entry_form=51 and b.receive_purpose=8 and c.item_category=13 and c.transaction_type=4 and b.booking_id=$order_booking_id and b.status_active=1 and b.is_deleted=0 and c.prod_id=$prod_id $sql_cond
				union all
				select sum(b.transfer_qnty) as qnty, 0 as qnty2 from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c 
				where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and b.item_category=13 and a.transfer_criteria in(6,8) and a.to_order_id=$order_booking_id and a.status_active=1 and a.is_deleted=0 and b.from_prod_id=$prod_id and c.transaction_type=5 and a.entry_form in(80,432) and c.transaction_type=5 and c.prod_id=$prod_id $sql_cond
			) product_details_master"; // Transfer In entry form b.entry_form=80, 432 (Grey Fabric Order To Sample Transfer Entry) // b.transfer_criteria in(6)
					
			$result_recv=sql_select($recv_sql);
			
			$iss_sql="SELECT sum(qnty) as qnty from
			(
				select sum(b.issue_qnty) as qnty from inv_issue_master a, inv_grey_fabric_issue_dtls b, inv_transaction c 
				where a.id=b.mst_id and a.id=b.mst_id and b.trans_id=c.id and a.item_category=13 and a.entry_form=16 and a.issue_purpose in(3,8,26,29,30,31) and a.issue_basis=1 and a.booking_id=$order_booking_id and b.prod_id=$prod_id and a.status_active=1 and a.is_deleted=0 $sql_cond
				union all
				select sum(c.cons_quantity) as qnty from inv_issue_master b, inv_transaction c, inv_receive_master d where b.id=c.mst_id and b.received_id=d.id and ((d.booking_id=$order_booking_id and d.receive_basis!=9) or (d.booking_id in ($recv_id) and d.receive_basis=9)) and d.booking_without_order=1 and b.item_category=13 and b.entry_form=45 and c.transaction_type=3 and c.item_category=13 and c.prod_id=$prod_id $sql_cond
				union all
				select sum(b.transfer_qnty) as qnty from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c 
				where a.id=b.mst_id and a.id=c.mst_id and b.mst_id=c.mst_id and a.transfer_criteria in(7,8) and b.item_category=13 and a.from_order_id=$order_booking_id and c.transaction_type=6 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(81,432) and b.from_prod_id=$prod_id and c.transaction_type=6 and c.prod_id=$prod_id $sql_cond
			) inv_issue_master"; // Transfer Out entry form b.entry_form=81 (Grey Fabric Sample To Order Transfer Entry) // b.transfer_criteria in(7)
			//echo $recv_sql.'<br>'.$iss_sql;

			$result_iss=sql_select($iss_sql);
			$grey_fabric_recv=$result_recv[0][csf('qnty')]+$result_recv[0][csf('qnty2')];
			$grey_fabric_issued=$result_iss[0][csf('qnty')];
			$current_stock=$grey_fabric_recv-$grey_fabric_issued;
			$trans_qnty=str_replace("'","",$txt_transfer_qnty);
			// echo "5**Stock quantity $current_stock";die;
			if($trans_qnty>$current_stock)
			{
				echo "5**Transfer Quantity Not Allow More Then Sample Stock quantity";die;
			}
		}
		// echo "5**Stock quantity $current_stock";die;
		// ================= current stock check end ============================
        /**
         * List of fields that will not change/update on update button event
         * fields=> from_order_id*to_order_id*
         * data=> $txt_from_order_book_id."*".$txt_to_order_book_id."*".
         */
		$field_array_update="challan_no*transfer_date*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		$field_array_trans="prod_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*store_id*floor_id*room*rack*self*program_no*stitch_length*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		 
		$rate=0; $amount=0;
		$updateTransID_array[]=$update_trans_issue_id; 
		$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_from_order_book_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$txt_form_prog."*".$stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		$updateTransID_array[]=$update_trans_recv_id; 
		$updateTransID_data[$update_trans_recv_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_to_order_book_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_to_prog."*".$stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		$field_array_dtls="from_prod_id*transfer_qnty*roll*rate*transfer_value*uom*y_count*brand_id*yarn_lot*from_store*floor_id*room*rack*shelf*to_store*to_floor_id*to_room*to_rack*to_shelf*from_program*to_program*stitch_length*updated_by*update_date";
		$data_array_dtls=$cbo_item_desc."*".$txt_transfer_qnty."*".$txt_roll."*'".$rate."'*'".$amount."'*".$cbo_uom."*".$hid_ycount."*".$hid_ybrand."*".$txt_ylot."*".$cbo_store_name."*".$cbo_floor."*".$cbo_room."*".$txt_rack."*".$txt_shelf."*".$cbo_store_name_to."*".$cbo_floor_to."*".$cbo_room_to."*".$txt_rack_to."*".$txt_shelf_to."*".$txt_form_prog."*".$txt_to_prog."*".$stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";		
		
		if ($transfer_criteria == 6) // order to sample
		{
			$data_array_prop="(".$id_prop.",".$update_trans_issue_id.",6,80,".$update_dtls_id.",".$txt_from_order_book_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}

		if ($transfer_criteria == 7) // sample to order 
		{
			$data_array_prop="(".$id_prop.",".$update_trans_recv_id.",5,81,".$update_dtls_id.",".$txt_to_order_book_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		}	
		
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}
		// echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array);die;
		$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		if ($transfer_criteria == 6) // order to sample
		{
			$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=80");
			{
				if($query) $flag=1; else $flag=0; 
			}			
			// echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
		}
		if ($transfer_criteria == 7) // sample to order
		{
			$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=81");
			{
				if($query) $flag=1; else $flag=0; 
			} 

			$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}	
		}	

		//echo "10**$rID**$rID2**$rID3**$query**$rID4";die;
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
		disconnect($con);
		die;
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
	                    <?
						if($type=="from")
						{ 
						?>
	                        <th width="100">Knitted</th>
	                        <th width="100">Issue to dye</th>
	                    	<th width="100">Issue Return</th>
	                        <th width="100">Transfer Out</th>
	                        <th width="100">Transfer In</th>
	                        <th>Remaining</th>
	                    <?
						}
						else
						{
						?>
	                        <th width="80">Yrn. Issued</th>
	                        <th width="80">Yrn. Issue Rtn</th>
	                        <th width="80">Knitted</th>
	                        <th width="90">Issue Rtn.</th>
	                        <th width="100">Transf. Out</th>
	                        <th width="100">Transf. In</th>
	                        <th>Shortage</th>
	                    <?	
						}
						?>
	                    
	                </thead>
	                <?
						$req_qty=return_field_value("sum(b.grey_fab_qnty) as grey_req_qnty","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and a.item_category in(2,13) and b.po_break_down_id=$txt_order_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1","grey_req_qnty");
						
						$sql="select 
									sum(CASE WHEN entry_form ='3' THEN quantity ELSE 0 END) AS issue_qnty,
									sum(CASE WHEN entry_form ='5' THEN quantity ELSE 0 END) AS dye_issue_qnty,
									sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
									sum(CASE WHEN entry_form ='81' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_out_qnty,
									sum(CASE WHEN entry_form ='81' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_in_qnty,
									sum(CASE WHEN trans_id<>0 and entry_form in(2,22) THEN quantity ELSE 0 END) AS knit_qnty
								from order_wise_pro_details where po_breakdown_id=$txt_order_id and status_active=1 and is_deleted=0";
						$dataArray=sql_select($sql);
						$remaining=0; $shoratge=0;
					?>
	                <tr bgcolor="#EFEFEF">
	                    <td>1</td>
	                    <td align="right"><? echo number_format($req_qty,2); ?>&nbsp;</td>
	                    <?
						if($type=="from")
						{
							$remaining=$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]-$dataArray[0][csf('transfer_out_qnty')]+$dataArray[0][csf('transfer_in_qnty')]-$dataArray[0][csf('knit_qnty')];
						?>
	                        <td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
	                        <td align="right"><? echo number_format($dataArray[0][csf('dye_issue_qnty')],2); ?></td>
	                        <td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?>&nbsp;</td>
	                        <td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?></td>
	                    	<td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
	                        <td align="right"><? echo number_format($remaining,2); ?>&nbsp;</td>
	                    <?
						}
						else
						{
							$shoratge=$req_qty-$dataArray[0][csf('issue_qnty')]-$dataArray[0][csf('return_qnty')]+$dataArray[0][csf('transfer_out_qnty')]-$dataArray[0][csf('transfer_in_qnty')];
						?>
	                        <td align="right"><? echo number_format($dataArray[0][csf('issue_qnty')],2); ?>&nbsp;</td>
	                        <td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
	                        <td align="right"><? echo number_format($dataArray[0][csf('knit_qnty')],2); ?>&nbsp;</td>
	                        <td align="right"><? echo number_format($dataArray[0][csf('return_qnty')],2); ?></td>
	                    	<td align="right"><? echo number_format($dataArray[0][csf('transfer_in_qnty')],2); ?>&nbsp;</td>
	                        <td align="right"><? echo number_format($dataArray[0][csf('transfer_out_qnty')],2); ?>&nbsp;</td>
	                    	<td align="right"><? echo number_format($shoratge,2); ?>&nbsp;</td>
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

if ($action=="grey_fabric_order_to_order_transfer_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$job_arr = return_library_array("select id, job_no from wo_po_details_master","id","job_no");
	$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$qnty_arr = return_library_array("select id, po_quantity from wo_po_break_down","id","po_quantity");
	$buyer_arr = return_library_array("select id, buyer_name from wo_po_details_master","id","buyer_name");
	$style_arr = return_library_array("select id, style_ref_no from wo_po_details_master","id","style_ref_no");
	$ship_date_arr = return_library_array("select id, pub_shipment_date from wo_po_break_down","id","pub_shipment_date");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
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
        	<td width="125"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
            <td width="125"><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
            <td width="125"><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
        </tr>
        <tr>
            <td><strong>From order No:</strong></td> <td width="175px"><? echo $po_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
            <td><strong>From ord Qnty:</strong></td> <td width="175px"><? echo $qnty_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
            <td><strong>From ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$buyer_arr[$dataArray[0][csf('from_order_id')]]]; ?></td>
        </tr>
        <tr>
            <td><strong>From Style Ref.:</strong></td> <td width="175px"><? echo $style_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
            <td><strong>From Job No:</strong></td> <td width="175px"><? echo $job_arr[$dataArray[0][csf('from_order_id')]]; ?></td>
            <td><strong>From Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($ship_date_arr[$dataArray[0][csf('from_order_id')]]); ?></td>
        </tr>
        <tr>
            <td><strong>To order No:</strong></td> <td width="175px"><? echo $po_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To ord Qnty:</strong></td> <td width="175px"><? echo $qnty_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$buyer_arr[$dataArray[0][csf('to_order_id')]]]; ?></td>
        </tr>
        <tr>
            <td><strong>To Style Ref.:</strong></td> <td width="175px"><? echo $style_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To Job No:</strong></td> <td width="175px"><? echo $job_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($ship_date_arr[$dataArray[0][csf('to_order_id')]]); ?></td>
        </tr>
    </table>
        <br>
    <div style="width:100%;">
    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
        <thead bgcolor="#dddddd" align="center">
            <th width="30">SL</th>
            <th width="120" >Item Category</th>
            <th width="250" >Item Description</th>
            <th width="70" >UOM</th>
            <th width="100" >Transfered Qnty</th>
        </thead>
        <tbody> 
   
	<?
	$sql_dtls="select id, item_category, item_group, from_prod_id, transfer_qnty, uom from inv_item_transfer_dtls where mst_id='$data[1]' and status_active=1 and is_deleted=0";
	
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
                <td><? echo $item_category[$row[csf("item_category")]]; ?></td>
                <td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
                <td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
			</tr>
			<? $i++; } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $transfer_qnty_sum; ?></td>
            </tr>                           
        </tfoot>
      </table>
        <br>
		 <?
            echo signature_table(19, $data[0], "900px");
         ?>
      </div>
   </div>   
 	<?	
 	exit();
}
?>
