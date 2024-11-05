<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action=="upto_variable_settings")
{
	extract($_REQUEST);
	echo $variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=2 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	exit();
}

if ($action == "load_drop_down_company") {
	$data = explode("_",$data);
	$company_cond = ($data[0] != "" && $data[0] == 1)?" and id=$data[0]":"";
	if($data[1] == 1){
		echo create_drop_down("cbo_party", 152, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_cond order by company_name", "id,company_name", 1, "--Select--", $data[0], "");
	}else{
		echo create_drop_down("cbo_party", 162, "select a.id,a.buyer_name from lib_buyer a,lib_buyer_party_type b where a.id=b.buyer_id and b.party_type=3 and a.status_active=1", "id,buyer_name", 1, "-- Select Party --", $selected, "", 0);
	}
	
	exit();
}

if ($action == "load_drop_down_buyer") {
	$data = explode("_",$data);
	echo create_drop_down("cbo_party", 152, "select a.id,a.buyer_name from lib_buyer a,lib_buyer_party_type b where a.id=$data[0] and a.id=b.buyer_id and b.party_type=3 and a.status_active=1", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", 0);
	exit();
}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 152, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_drop_down('requires/finish_feb_roll_delivery_to_garments_controller', this.value+'_'+$data[0], 'load_drop_down_store','store_td');fnc_reset_dtls();" );
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=216 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);
	//print_r($print_report_format_arr);
	echo "$('#Print1').hide();\n";
	echo "$('#print2').hide();\n";
	echo "$('#print3').hide();\n";



	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==134){echo "$('#Print1').show();\n";}
			if($id==135){echo "$('#print2').show();\n";}
			if($id==136){echo "$('#print3').show();\n";}
		}
	}
	else
	{
		echo "$('#Print1').show();\n";
		echo "$('#print2').show();\n";
		echo "$('#print3').show();\n";
	}
	exit();
}

if ($action=="load_drop_down_store")
{
	$data=explode("_",$data);

	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$data[1]' and a.location_id = $data[0] and b.category_type=2 and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1,"--Select store--",0,"fnc_reset_dtls();");
	exit();
}

if($action == "load_drop_floor")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "cbo_floor", "182", "select b.floor_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.floor_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0  group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name","floor_id,floor_room_rack_name", 1, "--Select Floor--", 0, "" );
}

if($action == "load_drop_room")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];

	echo create_drop_down( "cbo_room", "182", "select b.room_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.room_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.room_id,a.floor_room_rack_name order by a.floor_room_rack_name","room_id,floor_room_rack_name", 1, "--Select Room--", 0, "" );
}

if($action == "load_drop_rack")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_rack", '182', "select b.rack_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.rack_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.rack_id,a.floor_room_rack_name order by a.floor_room_rack_name","rack_id,floor_room_rack_name", 1, "--Select Rack--", 0, "" );
}

if($action == "load_drop_shelf")
{
	$data = explode("_", $data);
	$store_id=$data[0];
	$company_id = $data[1];
	echo create_drop_down( "txt_shelf", '182', "select b.shelf_id,a.floor_room_rack_name from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where  a.floor_room_rack_id=b.shelf_id and b.store_id='$store_id' and a.company_id='$company_id' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by b.shelf_id,a.floor_room_rack_name order by a.floor_room_rack_name","shelf_id,floor_room_rack_name", 1, "--Select Shelf--", 0, "" );
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	// print_r($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
	// if ($fabric_sales_order_id=="") {$fsoId='0';}
	// $fabric_sales_order_id=2012;
	?> 
	<script>

		var selected_id = new Array();
		var fso_arr_chk = new Array();
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str) 
		{
			var any_selected = $('#hidden_barcode_nos').val();
			if(any_selected=="")
			{
				fso_arr_chk = [];
			}

			var fso_no = $('#hidden_fso_no_' + str).val();
			if(fso_arr_chk.length==0)
			{
				fso_arr_chk.push( fso_no );
			}
			else if( jQuery.inArray( fso_no, fso_arr_chk )==-1 &&  fso_arr_chk.length>0)
			{
				alert("Sales Order Mixed is Not Allowed");
				return;
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );
			
			var total_selected_val=$('#hidden_selected_row_total').val()*1;// txt_individual_qty

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
				total_selected_val=total_selected_val+$('#txt_individual_qty' + str).val()*1;
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
				total_selected_val=total_selected_val-$('#txt_individual_qty' + str).val()*1;
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			
			$('#hidden_barcode_nos').val( id );
			$('#hidden_selected_row_total').val( total_selected_val.toFixed(2));

			var fso_id = $('#hidden_fso_id_' + str).val();
			var booking_id = $('#hidden_booking_id_' + str).val();
			var booking_no = $('#hidden_booking_no_' + str).val();
			var within_group = $('#hidden_within_group_' + str).val();
			var po_job_no = $('#hidden_po_job_no_' + str).val();
			var booking_without_ord = $('#hidden_booking_without_ord_' + str).val();

			$('#hdn_booking_id').val(booking_id);
			$('#hdn_booking_no').val(booking_no);
			$('#hdn_booking_without_ord').val(booking_without_ord);
			$('#hdn_fso_no').val(fso_no);
			$('#hdn_fso_id').val(fso_id);
			$('#hdn_buyer_id').val();
			$('#hdn_within_group').val();
			$('#hdn_po_job').val();

		}
		
		function fnc_close()
		{
			parent.emailwindow.hide();
		}
		
		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val( '' );
			selected_id = new Array();
		}
		
		
		function change_booking_placeholder()
		{
			if(document.getElementById('chkIsSales').checked)
			{
				$("#txt_booking_no").attr("placeholder", "Full Booking No");
			}
			else
			{
				$("#txt_booking_no").attr("placeholder", "Booking No Prefix");
			}
		}
		
		var tableFilters = 
		{
			col_operation: { 
				id: ["value_total_selected_value_td"],
				col: [16],
				operation: ["sum"],
				write_method: ["innerHTML"]
			}
		}
		function check_all_data() 
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length; 

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) 
			{
				if($("#search"+i).css("display") != "none")
				{
					js_set_value( i );
				}				
			}
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:95%">
			<form name="searchwofrm"  id="searchwofrm">
				<fieldset style="width:95%; margin-left:2px;">
					<legend>Enter search words</legend>           
					<table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
						<thead>
							<th  colspan="14">
								<?
								echo create_drop_down( "cbo_search_category", 140, $string_search_type,'', 1, "-- Search Catagory --",4 );
								?>
							</th>
						</thead>
						<thead>
							<th>Year</th>
							<th>Within Group</th>
							<th>Barcode No</th>
							<th>Sales Order No</th>
							<th>Transfer Id</th>
							<th>Booking No</th>
							<th>Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:50px" class="formbutton" />
								<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
								<input type="hidden" name="hdn_booking_id" id="hdn_booking_id">  
								<input type="hidden" name="hdn_booking_no" id="hdn_booking_no">  
								<input type="hidden" name="hdn_booking_without_ord" id="hdn_booking_without_ord">
								<input type="hidden" name="hdn_fso_no" id="hdn_fso_no">
								<input type="hidden" name="hdn_fso_id" id="hdn_fso_id">
								<input type="hidden" name="hdn_buyer_id" id="hdn_buyer_id">
								<input type="hidden" name="hdn_within_group" id="hdn_within_group">
								<input type="hidden" name="hdn_po_job" id="hdn_po_job">
							</th> 
						</thead>
						<tr class="general">
							<td>
								<?php
								echo create_drop_down( "cbo_year_selection", 65, create_year_array(),"", 0,"-- --", date("Y",time()), "",0,"" );
								?>
							</td>
							<td align="center">				
								<?php
								echo create_drop_down( "cbo_within_group", 100, $yes_no,"", 0,"-- --", 1, "",0,"" );
								?>	
							</td>			
							<td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:100px" class="text_boxes" /></td> 
							<td align="center">				
								<input type="text" style="width:100px" class="text_boxes"  name="txt_sales_order_no" id="txt_sales_order_no" />	
							</td> 
							<td align="center">				
								<input type="text" style="width:100px" class="text_boxes"  name="txt_trans_id" id="txt_trans_id" disabled />	
							</td>
							<td align="center">				
								<input type="text" style="width:80px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />	
							</td>
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
							</td>

							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('cbo_within_group').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('barcode_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('txt_sales_order_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_trans_id').value+'_'+'<? echo $store_id;?>'+'_'+ '<? echo $cbo_location;?>'+'_'+ document.getElementById('cbo_search_category').value+'_'+'<? echo $fabric_sales_order_id;?>'+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_barcode_search_list_view', 'search_div', 'finish_feb_roll_delivery_to_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1,tableFilters);')" style="width:50px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body> 
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}


if($action=="create_barcode_search_list_view")
{
	// print_r($data);
	$data = explode("_",$data);

	$within_group=trim($data[0]);
	$company_id =$data[1];
	$barcode_no =trim($data[2]);
	$booking_no =trim($data[3]);
	$sales_order_no = trim($data[4]);
	$year = trim($data[5]);
	$trans_id = trim($data[6]);
	$cbo_store_name = trim($data[7]);
	$cbo_location = trim($data[8]);
	$search_category = trim($data[9]);
	$fabric_sales_order_id = trim($data[10]);
	$start_date =trim($data[11]);
	$end_date =trim($data[12]);
	// echo $fabric_sales_order_id.'=======';die;
	$color_arr=return_library_array( "select id, color_name from lib_color where status_active =1 and is_deleted=0",'id','color_name');
	$store_arr=return_library_array("select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and  a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and b.category_type in(2)","id","store_name");
	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
			$trans_date_cond="and a.transfer_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
			$trans_date_cond="and a.transfer_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
		$trans_date_cond="";
	}

	if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}

	/*if($barcode_no =="" && $booking_no == "" && $sales_order_no =="")
	{
		echo "<div style='color:red; font-weight:bold; text-align:center;'>Please enter Sales Order</div>";		
		die;
	}*/
	

	if($db_type == 0)
	{
		$sales_year =	" and year(e.insert_date) = $year";
	}else{
		$sales_year = "and to_char(e.insert_date,'yyyy') = $year";
	}

	$product_arr=return_library_array("select id, product_name_details from product_details_master where item_category_id=2",'id','product_name_details');

	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	$store_location_arr = return_library_array("select id, location_id from lib_store_location","id","location_id");

	if($within_group>0) $within_group_cond = " and e.within_group = '$within_group'";
	$sales_order_cond= "";
	if($search_category==1)
	{
		if($booking_no!="") $booking_no_cond =" and e.sales_booking_no='$booking_no'"; else $booking_no_cond="";
		if($sales_order_no!="") $sales_order_cond=" and e.job_no = '$sales_order_no'";
	}
	else if($search_category==0 || $search_category==4)
	{
		if($booking_no!="") $booking_no_cond =" and e.sales_booking_no like '%$booking_no%'"; else $booking_no_cond="";
		if($sales_order_no!="") $sales_order_cond=" and e.job_no like '%$sales_order_no%'";
	}
	else if($search_category==2)
	{
		if($booking_no!="") $booking_no_cond =" and e.sales_booking_no like '$booking_no%'"; else $booking_no_cond="";
		if($sales_order_no!="") $sales_order_cond=" and e.job_no like '$sales_order_no%'";
	}
	else if($search_category==3)
	{
		if($booking_no!="") $booking_no_cond =" and e.sales_booking_no like '%$booking_no'"; else $booking_no_cond="";
		if($sales_order_no!="") $sales_order_cond=" and e.job_no like '%$sales_order_no'";
	}
	if($fabric_sales_order_id!="") $sales_order_id_cond=" and e.id=$fabric_sales_order_id";

	$sales_order = 1;
	$sql="SELECT a.id,a.recv_number,a.company_id,a.knitting_source,a.knitting_company, a.location_id,  to_char(a.insert_date,'YYYY') as year, b.batch_id, d.batch_no, e.id as  fso_id, e.job_no, e.po_job_no, e.po_buyer, e.within_group, e.buyer_id,e.sales_booking_no,e.booking_id, e.booking_without_order, c.qnty, c.barcode_no, c.entry_form, b.floor, b.room, b.rack_no,b.shelf_no,b.fabric_description_id,a.store_id, b.gsm, b.width, b.dia_width_type, b.color_id, b.prod_id 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, pro_batch_create_mst d, fabric_sales_order_mst e 
	where a.id = b.mst_id and b.barcode_no = c.barcode_no  and c.entry_form=317 and c.is_sales= 1 and a.entry_form=317 and a.company_id=$company_id and a.store_id=$cbo_store_name and a.location_id=$cbo_location and b.batch_id = d.id and c.po_breakdown_id = e.id and a.status_active = 1 and b.status_active = 1 and c.status_active = 1 $within_group_cond $sales_order_cond $sales_order_id_cond $booking_no_cond $barcode_cond  $date_cond  and c.re_transfer=0
	union all 
	select a.id, a.transfer_system_id as recv_number,a.company_id, 0 as knitting_source, 0 as knitting_company, 0 as location_id,  to_char(a.insert_date,'YYYY') as year, b.to_batch_id as batch_id, d.batch_no, c.po_breakdown_id as fso_id, e.job_no, e.po_job_no, e.po_buyer, e.within_group, e.buyer_id, e.sales_booking_no, e.booking_id, e.booking_without_order, c.qnty, c.barcode_no, c.entry_form, b.to_floor_id as floor, b.to_room as room, b.to_rack as rack_no,  b.to_shelf as shelf_no, b.feb_description_id as fabric_description_id, b.to_store as store_id, b.gsm, b.dia_width as width, b.dia_width_type, b.color_id, b.to_prod_id as prod_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, pro_batch_create_mst d, fabric_sales_order_mst e
	where a.id=b.mst_id and b.id=c.dtls_id and b.to_batch_id=d.id and a.id=b.mst_id and c.po_breakdown_id=d.sales_order_id and c.po_breakdown_id = e.id and d.is_sales=1 and a.entry_form =628 and c.entry_form=628  and b.to_store=$cbo_store_name and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales=1 $within_group_cond $sales_order_cond $sales_order_id_cond $booking_no_cond $barcode_cond  $trans_date_cond";
	//echo $sql; //die;

	$result = sql_select($sql);
	$barcode_arr = array(); $po_nos_arr =array();
	foreach ($result as $row) {
		if ($sales_order == 1 && $row[csf('within_group')] == 1) {
			$sales_within_group = true;			
		} else {
			$sales_within_group = false;
		}
		
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];

		if($row[csf('booking_without_order')] != 1)
		{
			$po_nos_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
	}

	$barcode_arr = array_filter(array_unique($barcode_arr));

	if(count($barcode_arr)>0)
	{
		$all_barcode_nos = implode(",", $barcode_arr);
		$BarCond = $all_barcode_cond = ""; 

		if($db_type==2 && count($barcode_arr)>999)
		{
			$barcode_arr_chunk=array_chunk($barcode_arr,999) ;
			foreach($barcode_arr_chunk as $chunk_arr)
			{
				$BarCond.=" a.barcode_no in(".implode(",",$chunk_arr).") or ";	
			}

			$all_barcode_cond.=" and (".chop($BarCond,'or ').")";			
			
		}
		else
		{ 	
			$all_barcode_cond=" and a.barcode_no in($all_barcode_nos)";  
		}
	}

	if(!empty($barcode_arr))
	{
		$scanned_barcode_arr=array();
		$barcodeData=sql_select("select a.barcode_no from pro_roll_details a where a.entry_form=318 and a.status_active=1 and a.is_deleted=0 $all_barcode_cond ");
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}

		$stitch_lot_sql = sql_select("select a.barcode_no,b.stitch_length,b.yarn_lot from pro_roll_details a,pro_grey_prod_entry_dtls b where a.dtls_id = b.id and a.entry_form = 2 and a.status_active=1 and b.status_active =1 and b.is_deleted =0 and a.is_deleted =0 $all_barcode_cond");
		foreach ($stitch_lot_sql as $row) 
		{
			$stitch_lot_arr[$row[csf("barcode_no")]]['stitch_length'] = $row[csf("stitch_length")];
			$stitch_lot_arr[$row[csf("barcode_no")]]['yarn_lot'] = $row[csf("yarn_lot")];
		}
	}

	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1690" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="120">System Number</th>
			<th width="100">Batch NO</th>
			<th width="50">Source</th>
			<th width="160">Fabric Description</th>
			<th width="40">Gsm</th>
			<th width="40">Dia</th>
			<th width="60">Stitch L.</th>

			<th width="90">Job No</th>
			<th width="110">Booking No</th>
			<th width="110">FSO No</th>
			<th width="50">Within Group</th>

			<th width="70">Color Name</th>
			<th width="105">Location</th>
			<th width="75">Barcode No</th>
			<th width="40">Roll No</th>
			<th width="70">Roll Qty.</th>
			<th width="70">Store Name</th>
			<th width="70">Floor</th>
			<th width="70">Room</th>
			<th>Rack</th>
		</thead>
	</table>
	<div style="width:1700px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1682" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;$total_roll_weight=0; 
			foreach ($result as $row)
			{  
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					$within_group_con=($row[csf('within_group')] == 1)?"Yes":"No";				
					$within_group = $row[csf('within_group')];
					$is_sales = $row[csf('is_sales')];

					$sales_order_order = $row[csf('job_no')];
					$sales_booking_no = $row[csf('sales_booking_no')];
					if ($within_group == 1) {
						$job_no = $row[csf("po_job_no")];
					} else {
						$job_no = '';
					}

					$color='';
					$color_id=explode(",",$row[csf('color_id')]);
					foreach($color_id as $val)
					{
						if($val>0) $color.=$color_arr[$val].",";
					}
					$color=chop($color,',');

					$product_data=explode(",",$product_arr[$row[csf('prod_id')]]);
					if ($row[csf("entry_form")]==628) 
					{
						$location_id=$store_location_arr[$row[csf("store_id")]];
					}
					else
					{
						$location_id=$row[csf("location_id")];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="30" align="center">
							<? echo $i; ?>
							<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
							<input type="hidden" name="txt_individual_qty[]" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
							<input type="hidden" name="hidden_booking_no[]" id="hidden_booking_no_<?php echo $i; ?>" value="<?php echo $sales_booking_no; ?>"/>
							<input type="hidden" name="hidden_fso_no[]" id="hidden_fso_no_<?php echo $i; ?>" value="<?php echo $sales_order_order; ?>"/>
							<input type="hidden" name="hidden_fso_id[]" id="hidden_fso_id_<?php echo $i; ?>" value="<?php echo $row[csf('fso_id')]; ?>"/>

							<input type="hidden" name="hidden_booking_no[]" id="hidden_booking_id_<?php echo $i; ?>" value="<?php echo $row[csf('booking_id')]; ?>"/>
							<input type="hidden" name="hidden_booking_without_ord[]" id="hidden_booking_without_ord_<?php echo $i; ?>" value="<?php echo $row[csf('booking_without_order')]; ?>"/>
							<input type="hidden" name="hidden_within_group[]" id="hidden_within_group_<?php echo $i; ?>" value="<?php echo $within_group; ?>"/>
							<input type="hidden" name="hidden_po_job_no[]" id="hidden_po_job_no_<?php echo $i; ?>" value="<?php echo $job_no; ?>"/>
						</td>
						<td width="120"><p><? echo $row[csf('recv_number')]; ?></p></td>
						<td width="100"><p><? echo $row[csf('batch_no')]; ?></p></td>
						<td width="50"><p><? echo $knitting_source[$row[csf('knitting_source')]]; ?></p></td>
						<td width="160"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="40"><p><? echo $row[csf('gsm')]; ?></p></td>
						<td width="40"><p><? echo $row[csf('width')]; ?></p></td>
						<td width="60"><p><? echo $stitch_lot_arr[$row[csf("barcode_no")]]['stitch_length']; ?></p></td>
						<td width="90"><p><? echo $job_no; ?></p></td>
						<td width="110"><p><? echo $sales_booking_no; ?></p></td>
						<td width="110"><p><? echo $sales_order_order; ?></p></td>
						<td width="50" align="center"><p><? echo $within_group_con; ?></p></td>
						<td width="70"><p><? echo $color; ?></p></td>
						<td width="105"><? echo $location_arr[$location_id]; ?>&nbsp;</td>
						<td width="75"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
						<td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td width="70" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
						<td width="70" align="center"><? echo $store_arr[$row[csf('store_id')]]; ?></td>
						<td width="70" align="center"><? echo $row[csf('floor')]; ?></td>
						<td width="70" align="center"><? echo $row[csf('room')]; ?></td>
						<td align="center"><? echo $row[csf('shelf_no')]; ?></td>
					</tr>
					<?
					
					$total_roll_weight +=$row[csf('qnty')];
					
					$i++;
				}
			}
			
			?>
		</table>
	</div>
	<table width="1680" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table">
		<tr class="tbl_bottom">
			<td width="30"></td>
			<td width="120"></td>
			<td width="100"></td>
			<td width="50"></td>
			<td width="160"></td>
			<td width="40"></td>
			<td width="40"></td>
			<td width="60"></td>
			<td width="90"></td>
			<td width="110"></td>
			<td width="110"></td>
			<td width="50"></td>
			<td width="70"></td>
			<td width="105"></td>
			<td width="75"></td>
			<td width="40">Total</td>
			<td width="70" id="value_total_selected_value_td" align="right"><?php echo number_format($total_roll_weight,2); ?></td>
			<td width="70"></td>
			<td width="70"></td>
			<td width="70"></td>
			<td></td>
		</tr>
		<tr class="tbl_bottom">
			<td width="30"></td>
			<td width="120"></td>
			<td width="100"></td>
			<td width="50"></td>
			<td width="160"></td>
			<td width="40"></td>
			<td width="40"></td>
			<td width="60"></td>
			<td width="90"></td>
			<td width="110"></td>
			<td width="110"></td>
			<td width="50"></td>
			<td width="70"></td>
			<td width="105"></td>
			<td width="105" colspan="2">Selected Row Total</td>
			<td width="70">
				<input type="text"  style="width:55px" class="text_boxes_numeric" name="hidden_selected_row_total" id="hidden_selected_row_total" readonly value="0">
			</td>
			<td width="70"></td>
			<td width="70"></td>
			<td width="70"></td>
			<td></td>
		</tr>
		<tr>
			<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/> Check all
			</td>
			<td align="center" colspan="19" >
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
		</tr>

	</table>
	<?	
	exit();
}

if($action=="populate_barcode_data")
{
	//$barcode_cond = " and c.barcode_no in ($data)";
	//echo $barcode_cond;die;
	$con = connect();
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=318 and type=1");
	oci_commit($con);
	$barcode_arr=array_filter(explode(",", $data));
	// echo "<pre>"; print_r($barcode_arr);die;
	foreach ($barcode_arr as $barcode) 
	{
		execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 318, 1, ".$barcode.")");
	}
	oci_commit($con);
	

	if($db_type ==0){
		$select_year = " year(a.insert_date) as year";
	}else{
		$select_year = " to_char(a.insert_date,'YYYY') as year";
	}

	$scanned_barcode_data=sql_select("SELECT a.barcode_no, b.issue_number from tmp_barcode_no t, pro_roll_details a, inv_issue_master b where t.barcode_no=a.barcode_no and t.userid=$user_id and t.entry_form = 318  and a.mst_id = b.id and a.entry_form = 318 and b.entry_form = 318  and a.status_active=1 and a.is_deleted=0");//and  a.barcode_no in($data)

	foreach($scanned_barcode_data as $row)
	{
		echo "99!!".$row[csf('issue_number')];
	}
	if (!empty($scanned_barcode_data)) 
	{
		execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=318 and type=1");
		oci_commit($con);
		die;
	}

	$data_array=sql_select("SELECT a.id,a.recv_number,a.company_id,a.knitting_source,a.knitting_company, a.location_id,  $select_year, b.batch_id, d.batch_no, b.body_part_id, e.id as  fso_id, e.job_no, e.po_job_no, e.po_buyer, e.within_group, e.buyer_id, e.sales_booking_no, e.booking_id, e.booking_without_order, c.qnty, c.reject_qnty, c.barcode_no, c.roll_no, c.roll_id, b.floor, b.room, b.rack_no, b.shelf_no, b.fabric_description_id, a.store_id, b.gsm, b.width, b.dia_width_type, b.color_id, b.prod_id, c.reprocess,c.prev_reprocess, c.entry_form, e.po_company_id 
	from tmp_barcode_no t, inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, pro_batch_create_mst d, fabric_sales_order_mst e 
	where t.barcode_no=c.barcode_no and t.userid=$user_id and t.entry_form = 318  and a.id = b.mst_id and b.barcode_no = c.barcode_no  and c.entry_form=317 and c.is_sales= 1 and a.entry_form=317 and b.batch_id = d.id and c.po_breakdown_id = e.id and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  and c.re_transfer=0
	union all 
	select a.id, a.transfer_system_id as recv_number,a.company_id, 0 as knitting_source, 0 as knitting_company, 0 as location_id, $select_year, b.to_batch_id as batch_id, e.batch_no, b.to_body_part as body_part_id, c.po_breakdown_id as fso_id, f.job_no, f.po_job_no, f.po_buyer, f.within_group, f.buyer_id, f.sales_booking_no, f.booking_id, f.booking_without_order, c.qnty, c.reject_qnty, c.barcode_no, c.roll_no, c.roll_id, b.to_floor_id as floor, b.to_room as room, b.to_rack as rack_no,  b.to_shelf as shelf_no, b.feb_description_id as fabric_description_id, b.to_store as store_id, b.gsm, b.dia_width as width, b.dia_width_type, b.color_id, b.to_prod_id as prod_id, c.reprocess,c.prev_reprocess, c.entry_form, f.po_company_id
	from tmp_barcode_no t, inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, pro_batch_create_mst e, fabric_sales_order_mst f
	where t.barcode_no=c.barcode_no and t.userid=$user_id and t.entry_form = 318  and a.id=b.mst_id and b.id=c.dtls_id and b.to_batch_id=e.id and a.id=b.mst_id and c.po_breakdown_id=e.sales_order_id and c.po_breakdown_id = f.id and e.is_sales=1 and a.entry_form =628 and c.entry_form=628 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales=1"); //$barcode_cond

	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$store_location_arr = return_library_array("select id, location_id from lib_store_location","id","location_id");

	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	$barcode_NOs="";
	foreach($data_array as $row)
	{
		$color_id_ref_arr[$row[csf("color_id")]] = chop($row[csf("color_id")],",");
		$barcode_NOs.=$row[csf("barcode_no")].",";
	}
	$barcode_Nos_all=rtrim($barcode_NOs,","); 
	$barcode_Nos_alls=explode(",",$barcode_Nos_all);
	$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999); 
	$barcode_no_conds=" and";
	foreach($barcode_Nos_alls as $dtls_id)
	{
		if($barcode_no_conds==" and")  $barcode_no_conds.="(a.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or a.barcode_no in(".implode(',',$dtls_id).")";
	}
	$barcode_no_conds.=")";
	$production_sql_data=sql_select("SELECT a.barcode_no, a.id as roll_id, a.roll_no, a.po_breakdown_id, a.qnty as prod_qty, a.qc_pass_qnty, a.reject_qnty 
	FROM pro_roll_details a 
	WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 $barcode_no_conds");
	$production_data_arr=array();
	foreach($production_sql_data as $value)
	{
		$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
		$production_data_arr[$value[csf("barcode_no")]]['qc_pass_qnty']=$value[csf("qc_pass_qnty")];	
		$production_data_arr[$value[csf("barcode_no")]]['reject_qnty']=$value[csf("reject_qnty")];	
	}
	// echo "<pre>";print_r($production_data_arr);

	$color_id_ref_arr = array_filter(array_unique($color_id_ref_arr));
	if(count($color_id_ref_arr)>0)
	{
		$all_color_ids = implode(",", $color_id_ref_arr);
		$all_color_id_cond=""; $colorCond=""; 
		if($db_type==2 && count($color_id_ref_arr)>999)
		{
			$color_id_ref_chunk=array_chunk($color_id_ref_arr,999) ;
			foreach($color_id_ref_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$colorCond.=" id in($chunk_arr_value) or ";	
			}
			
			$all_color_id_cond.=" and (".chop($colorCond,'or ').")";	
		}
		else
		{
			$all_color_id_cond=" and id in($all_color_ids)";	 
		}

		$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0 $all_color_id_cond","id","color_name");
	}

	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=318 and type=1");
	oci_commit($con);

	foreach($data_array as $row)
	{
		if ($row[csf("entry_form")]==628) 
		{
			$location_id=$store_location_arr[$row[csf("store_id")]];
		}
		else
		{
			$location_id=$row[csf("location_id")];
		}
		//echo $location_id;die;
		if($row[csf("within_group")] == 1)
		{
			$buyer_id = $row[csf("po_buyer")];
		}else{
			$buyer_id = $row[csf("buyer_id")];
		}
		$buyer_name = $buyer_arr[$buyer_id];

		$prod_qty=$production_data_arr[$row[csf('barcode_no')]]['prod_qty'];
		$qc_pass_qnty=$production_data_arr[$row[csf('barcode_no')]]['qc_pass_qnty'];
		$reject_qnty=$production_data_arr[$row[csf('barcode_no')]]['reject_qnty'];
		$processLoss=($prod_qty-($qc_pass_qnty+$reject_qnty));

		$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("barcode_no")]."**".$row[csf("roll_no")]."**".$row[csf("batch_id")]."**".$row[csf("batch_no")]."**".$row[csf("body_part_id")]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf('fabric_description_id')]."**".$constructtion_arr[$row[csf('fabric_description_id')]]."**".$composition_arr[$row[csf('fabric_description_id')]]."**".$row[csf("color_id")]."**".$color_arr[$row[csf("color_id")]]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".number_format($row[csf("qnty")],2,'.','')."**".number_format($row[csf("reject_qnty")],2,'.','')."**".$row[csf("floor")]."**".$row[csf("room")]."**".$row[csf("rack_no")]."**".$row[csf("shelf_no")]."**".$row[csf("dia_width_type")]."**".$row[csf("year")]."**".$row[csf("po_job_no")]."**".$buyer_id."**".$row[csf("job_no")]."**".$row[csf("fso_id")]."**".$row[csf("prod_id")]."**".$row[csf("recv_number")]."**".$row[csf("sales_booking_no")]."**".$row[csf("booking_id")]."**".$row[csf("company_id")]."**".$row[csf("roll_id")]."**".$row[csf("reprocess")]."**".$row[csf("prev_reprocess")]."**".$row[csf("booking_without_order")]."**".$row[csf("sales_booking_no")]."**".$row[csf("booking_id")]."**".$fabric_typee[$row[csf("dia_width_type")]]."**".$row[csf("within_group")]."**".$row[csf("po_company_id")]."**".$buyer_name."**".$processLoss."**".$location_id."**".$row[csf("store_id")]; 


		$all_barcode_no_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
	}
	unset($data_array);
	
	$all_barcode_no_arr = array_filter(array_unique($all_barcode_no_arr));


	$all_sales_ids = rtrim($all_sales_ids,", ");
	$transFSO_ids = implode(",", array_filter($transFSO_Arr));
	if($transFSO_ids!=""){
		$all_sales_ids = $all_sales_ids.",".$transFSO_ids;
	}
	
	
	
	if(count($barcodeDataArr)>0)
	{
		foreach($barcodeDataArr as $barcode_no=>$value)
		{
			$barcodeData.=$value."#";
		}
		echo substr($barcodeData,0,-1);
	}
	else
	{
		echo "0";
	}
	
	exit();	
}

if($action=="populate_barcode_data_update")
{
	$po_ids_arr=array(); $po_details_array=array(); $barcodeDataArr=array();
	$company_name_array=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer","id","buyer_name");
	$floor_room_rack_arr=return_library_array( "select floor_room_rack_id,floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	
	$issued_data_arr=$split_from=array(); $barcode_nos='';

	if($db_type ==0){
		$select_year = " year(c.insert_date) as year,";
	}else{
		$select_year = " to_char(c.insert_date,'YYYY') as year,";
	}

	$issued_barcode_data=sql_select("SELECT a.id as roll_table_id, a.barcode_no, a.dtls_id, b.trans_id, a.roll_id,a.roll_no,b.batch_id,e.batch_no, a.po_breakdown_id, a.booking_without_order, a.is_sales, a.reprocess, a.prev_reprocess, b.body_part_id, b.trans_id,b.prod_id,d.gsm, d.dia_width, d.detarmination_id, d.color as color_id, a.qnty ,a.reject_qnty ,b.floor, b.room, b.rack_no, b.shelf_no, b.width_type, $select_year c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, c.buyer_id, c.po_buyer, c.po_job_no, c.po_company_id, c.within_group, null as recv_number from pro_roll_details a, inv_finish_fabric_issue_dtls b , fabric_sales_order_mst c , product_details_master d, pro_batch_create_mst e where a.dtls_id=b.id and a.entry_form=318 and a.po_breakdown_id = c.id and b.batch_id = e.id and b.prod_id = d.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$data and a.is_returned!=1");

	if(!empty($issued_barcode_data))
	{

	}
	else
	{
		echo "<p style='font-weight:bold;align:center;width:350px'>Data Not Found</p>";
		die;
	}

	$barcode_NOs="";
	foreach($issued_barcode_data as $row)
	{
		$barcode_NOs.=$row[csf("barcode_no")].",";

	}
	$barcode_Nos_all=rtrim($barcode_NOs,","); 
	$barcode_Nos_alls=explode(",",$barcode_Nos_all);
	$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999); 
	$barcode_no_conds=" and";
	foreach($barcode_Nos_alls as $dtls_id)
	{
		if($barcode_no_conds==" and")  $barcode_no_conds.="(a.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or a.barcode_no in(".implode(',',$dtls_id).")";
	}
	$barcode_no_conds.=")";
	$production_sql_data=sql_select("SELECT a.barcode_no, a.id as roll_id, a.roll_no, a.po_breakdown_id, a.qnty as prod_qty, a.qc_pass_qnty, a.reject_qnty 
	FROM pro_roll_details a 
	WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 $barcode_no_conds");
	$production_data_arr=array();
	foreach($production_sql_data as $value)
	{
		$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
		$production_data_arr[$value[csf("barcode_no")]]['qc_pass_qnty']=$value[csf("qc_pass_qnty")];	
		$production_data_arr[$value[csf("barcode_no")]]['reject_qnty']=$value[csf("reject_qnty")];	
	}
	// echo "<pre>";print_r($production_data_arr);


	$i=count($issued_barcode_data);
	foreach($issued_barcode_data as $row)
	{
		if($row[csf("within_group")] == 1)
		{
			$buyer_id = $row[csf("po_buyer")];
		}else{
			$buyer_id = $row[csf("buyer_id")];
		}
		$buyer_name = $buyer_arr[$buyer_id]; 

		$all_barcode_no_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$barcode_nos.=$row[csf('barcode_no')].',';

		$prod_qty=$production_data_arr[$row[csf('barcode_no')]]['prod_qty'];
		$qc_pass_qnty=$production_data_arr[$row[csf('barcode_no')]]['qc_pass_qnty'];
		$reject_qnty=$production_data_arr[$row[csf('barcode_no')]]['reject_qnty'];
		$processLoss=($prod_qty-($qc_pass_qnty+$reject_qnty));

		// $grey_used = $row[csf("qnty")]+$row[csf("reject_qnty")];
		$grey_used=$prod_qty;
		?>
		<tr id="tr_<? echo $i; ?>" align="center" valign="middle">
			<td width="40" id="sl_<? echo $i;?>"><? echo $i;?> &nbsp;&nbsp;
           		<input type="checkbox" id="checkRow_<? echo $i;?>" name="checkRow[]" checked></td>
            <td width="80" id="barcode_<? echo $i;?>"><? echo $row[csf('barcode_no')];?></td>
            <td width="45" id="rollNo_<? echo $i;?>"><? echo $row[csf('roll_no')];?></td>
            <td width="60" id="batchNo_<? echo $i;?>"><? echo $row[csf('batch_no')];?></td>
            <td width="80" id="bodyPart_<? echo $i;?>" align="left"><? echo $body_part[$row[csf("body_part_id")]];?></td>
            <td width="80" id="cons_<? echo $i;?>" align="left"><? echo $constructtion_arr[$row[csf('detarmination_id')]];?></td>
            <td width="80" id="comps_<? echo $i;?>" align="left"><? echo $composition_arr[$row[csf('detarmination_id')]];?></td>
            <td width="70" id="color_<? echo $i;?>"><? echo $color_arr[$row[csf("color_id")]];?></td>
            <td width="40" id="gsm_<? echo $i;?>"><? echo $row[csf("gsm")];?></td>
            <td width="40" id="dia_<? echo $i;?>" ><? echo $row[csf("dia_width")];?></td>
            <td width="50" id="rollWgt_<? echo $i;?>">
                <input type="text" value="<? echo number_format($row[csf("qnty")],2,'.','');?>" id="currentQty_<? echo $i;?>" class="text_boxes_numeric"  style="width:35px" name="currentQty[]" onChange="fnc_rollQntyChange()" disabled /></td>
            <td width="50" id="rejectQty_<? echo $i;?>"><? echo number_format($row[csf("reject_qnty")],2,'.','');?></td>
            <td width="50" id="processLoss_<? echo $i;?>"><? echo number_format($processLoss);?></td>
            <td width="50" id="usedQty_<? echo $i;?>"><? echo number_format($grey_used,2,'.','');?></td>
            
            <!-- <td width="50" id="floor_<? //echo $i;?>"><input type="text" id="floorName_<? //echo $i;?>" class="text_boxes_numeric"  style="width:35px" name="floorName[]" value="<? //echo $row[csf("floor")];?>"/></td>
            <td width="50" id="room_<? //echo $i;?>"><input type="text" id="roomName_<? //echo $i;?>" class="text_boxes_numeric" style="width:35px" name="roomName[]" value="<? //echo $row[csf("room")];?>"/></td>
            <td width="50" id="rack_<? //echo $i;?>"><input type="text" id="rackName_<? //echo $i;?>" class="text_boxes_numeric" style="width:35px" name="rackName[]" value="<? //echo $row[csf("rack_no")];?>"/></td>
            <td width="50" id="self_<? //echo $i;?>"><input type="text" id="selfName_<? //echo $i;?>" class="text_boxes_numeric" style="width:35px" name="selfName[]" value="<? //echo $row[csf("shelf_no")];?>"/></td> -->
            <td width="50" id="floorName_<? echo $i;?>"><? echo $floor_room_rack_arr[$row[csf("floor")]];?></td>
			<td width="50" id="roomName_<? echo $i;?>"><? echo $floor_room_rack_arr[$row[csf("room")]];?></td>
			<td width="50" id="rackName_<? echo $i;?>"><? echo $floor_room_rack_arr[$row[csf("rack_no")]];?></td>
			<td width="50" id="selfName_<? echo $i;?>"><? echo $floor_room_rack_arr[$row[csf("shelf_no")]];?></td>

            <td width="60" id="wideType_<? echo $i;?>"><? echo $fabric_typee[$row[csf("width_type")]]; ?></td>
            <td width="45" id="year_<? echo $i;?>" align="center"><? echo $row[csf("year")];?></td>
            <td width="90" id="job_<? echo $i;?>"><? echo $row[csf("po_job_no")];?></td>
            <td width="65" id="buyer_<? echo $i;?>"><? echo $buyer_name; ?></td>
            <td width="80" id="order_<? echo $i;?>" align="left"><? echo $row[csf("job_no")];?></td>
            <td width="" id="prodId_<? echo $i;?>"><? echo $row[csf("prod_id")];?></td>
            <input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i;?>" value="<? echo $row[csf('barcode_no')];?>"/>
                <input type="hidden" name="productionId[]" id="productionId_<? echo $i;?>" value=""/>
                <input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $i;?>" value=""/>
                <input type="hidden" name="deterId[]" id="deterId_<? echo $i;?>" value="<? echo $row[csf('detarmination_id')];?>"/>
                <input type="hidden" name="productId[]" id="productId_<? echo $i;?>" value="<? echo $row[csf('prod_id')];?>"/>
                <input type="hidden" name="orderId[]" id="orderId_<? echo $i;?>" value="<? echo $row[csf('fso_id')];?>"/>
                <input type="hidden" name="rollId[]" id="rollId_<? echo $i;?>" value="<? echo $row[csf('roll_id')];?>"/>
                <input type="hidden" name="rollQty[]" id="rollQty_<? echo $i;?>"  value="<? echo $row[csf('qnty')];?>" />
                <input type="hidden" name="batchID[]" id="batchID_<? echo $i;?>"  value="<? echo $row[csf('batch_id')];?>" />
                <input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $i;?>" value="<? echo $row[csf('body_part_id')];?>"/> 
                <input type="hidden" name="colorId[]" id="colorId_<? echo $i;?>" value="<? echo $row[csf('color_id')];?>"/> 
                
                <input type="hidden" name="wideTypeId[]" id="wideTypeId_<? echo $i;?>" value="<? echo $row[csf('width_type')];?>"/> 
                <input type="hidden" name="JobNumber[]" id="JobNumber_<? echo $i;?>"  value="<? echo $row[csf('po_job_no')];?>"/> 
                <input type="hidden" name="buyerId[]" id="buyerId_<? echo $i;?>" value="<? echo $buyer_id;?>"/> 
                <input type="hidden" name="reProcess[]" id="reProcess_<? echo $i;?>"  value="<? echo $row[csf('reprocess')];?>"/>
                <input type="hidden" name="prereProcess[]" id="prereProcess_<? echo $i;?>"  value="<? echo $row[csf('prev_reprocess')];?>"/>
                <input type="hidden" name="IsSalesId[]" id="IsSalesId_<? echo $i;?>" value="1"/>
                <input type="hidden" name="rejectQnty[]" id="rejectQnty_<? echo $i;?>" value="<? echo $row[csf('reject_qnty')];?>"/>
                <input type="hidden" name="usedQnty[]" id="usedQnty_<? echo $i;?>" value="<? echo $grey_used;?>"/>

                <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i;?>"  value="<? echo $row[csf('dtls_id')];?>"/> 
                <input type="hidden" name="transId[]" id="transId_<? echo $i;?>"  value="<? echo $row[csf('trans_id')];?>"/> 
                <input type="hidden" name="rollTableId[]" id="rollTableId_<? echo $i;?>"  value="<? echo $row[csf('roll_table_id')];?>"/> 
                <input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $i;?>"  value="<? echo $row[csf('booking_without_order')];?>"/> 
                <input type="hidden" name="floorId[]" id="floorId_<? echo $i;?>" value="<? echo $row[csf('floor')];?>"/>
				<input type="hidden" name="roomId[]" id="roomId_<? echo $i;?>" value="<? echo $row[csf('room')];?>"/>
				<input type="hidden" name="rackId[]" id="rackId_<? echo $i;?>" value="<? echo $row[csf('rack_no')];?>"/>
				<input type="hidden" name="selfId[]" id="selfId_<? echo $i;?>" value="<? echo $row[csf('shelf_no')];?>"/>
			</td>
		</tr> 	
		<?
		--$i;
	}
	exit();
}


if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$tot_row = str_replace("'","",$txt_tot_row);
	for($k=1;$k<=$tot_row;$k++)
	{ 	
	  	$active_id="activeId_".$k;
	   	$barcodeNo="barcodeNo_".$k;
	   	$all_barcodeNos.=$$barcodeNo.",";
	   	if($$active_id==1)
	   	{
			$transId="transId_".$k;
			$productId="productId_".$k;
			$all_prod_ids.=$$productId.",";
			$all_transIds.=$$transId.",";
	   	}
	}

	$all_prod_ids=implode(",",array_filter(array_unique(explode(",",chop($all_prod_ids,",")))));
	$all_transIds=implode(",",array_unique(explode(",",substr($all_transIds,0,-1))));
	
	if($operation==1)
	{
		$is_update_cond = " and a.id not in ($all_transIds)";
	}else{
		$is_update_cond = "";
	}

	$max_transaction_date = return_field_value("max(a.transaction_date) as max_date", "inv_transaction a, inv_receive_master b", "a.prod_id in ($all_prod_ids) and b.store_id=$cbo_store_name  and a.status_active = 1 $is_update_cond ", "max_date");      
	if($max_transaction_date != "")
	{
		$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
		$delivery_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_delivery_date)));
		if ($delivery_date < $max_transaction_date) 
		{
			echo "20**Delivery Date can not be less that Last Receive Date Of This Lot";
			die;
		}
	}

	$all_barcodeNos_arr=array_unique(explode(",",chop($all_barcodeNos,",")));
	$all_barcodeNos=implode(",",$all_barcodeNos_arr);

	$all_barcode_no_cond="";$barCond="";
	if($db_type==2 && count($all_barcodeNos_arr)>999)
	{
		$all_barcodeNos_arr_chunk=array_chunk($all_barcodeNos_arr,999) ;
		foreach($all_barcodeNos_arr_chunk as $chunk_arr)
		{
			$chunk_arr_value=implode(",",$chunk_arr);
			$barCond.=" barcode_no in($chunk_arr_value) or ";
		}

		$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
	}
	else
	{
		$all_barcode_no_cond=" and barcode_no in($all_barcodeNos)";
	}


	$scanned_barcode_data = sql_select("select dtls_id,barcode_no,qnty,reprocess from pro_roll_details where entry_form in(318,68) and status_active=1 and is_deleted=0 $all_barcode_no_cond");
	foreach ($scanned_barcode_data as $row) 
	{
		if($row[csf("entry_form")] == 68){
			$scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("qnty")];
		}else{
			$self_scnned[$row[csf("barcode_no")]][$row[csf("reprocess")]] = $row[csf("barcode_no")];
		}
	}

	// Data insert block start here
	if($operation==0)
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$delivery_date = date("Y-m-d", strtotime(str_replace("'","",$txt_delivery_date)));
		
		$category_id=2; $entry_form=318; $prefix='FRDG';

		$id = return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master", $con);
		$new_system_id = explode("*", return_next_id_by_sequence("INV_ISSUE_MASTER_PK_SEQ", "inv_issue_master",$con,1,str_replace("'","",$cbo_company_id),$prefix,$entry_form,date("Y",time())));

		$field_array="id, issue_number_prefix, issue_number_prefix_num, issue_number, issue_purpose, entry_form, item_category, company_id, issue_date, location_id, buyer_id, inserted_by, insert_date,store_id, supplier_id,fso_no, fso_id, booking_no, booking_id, coure_tube, remarks, delivery_addr,challan_no";

		$data_array="(".$id.",'".$new_system_id[1]."',".$new_system_id[2].",'".$new_system_id[0]."',0,318,2,".$cbo_company_id.",". $txt_delivery_date.",".$cbo_location.",".$hdn_buyer_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_store_name.",".$cbo_party.",".$txt_fso_no.",".$hdn_fso_id.",".$txt_booking_no.",".$hdn_booking_id.",".$txt_coure_tube.",".$txt_remarks.",".$txt_delivery_addr.",".$txt_challan_no.")";
		//echo "10**insert into inv_issue_master ($field_array) values $data_array";die;
		$finish_fabric_issue_num=$new_system_id[0];
		$finish_update_id=$id;

		$field_array_trans="id, mst_id, batch_id, company_id, prod_id, item_category, transaction_type, transaction_date,store_id, body_part_id, cons_quantity,cons_rate, cons_amount,cons_reject_qnty,floor_id, room, rack, self,  inserted_by, insert_date";
		$field_array_dtls="id, mst_id, trans_id, batch_id, prod_id, uom, issue_qnty,rate,rate_in_usd,fabric_shade,store_id, no_of_roll, body_part_id, rack_no,shelf_no,floor,room, order_id,inserted_by,insert_date,width_type";
		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,roll_id, roll_no,barcode_no,reject_qnty,qc_pass_qnty,rate, amount,prev_reprocess,reprocess,inserted_by, insert_date, is_sales, booking_without_order, booking_no";
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date,is_sales";

		$i=0;
		$cur_st_qnty=0;
		$barcodeNos="";
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$activeId="activeId_".$j;
			$rollId="rollId_".$j;
			$batchId="batchId_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			//$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$rollDia="rolldia_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollQty_".$j;
			$rolldia="rolldia_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$currentWgt="currentWgt_".$j;
			$rejectQty="rejectQty_".$j;
			$wideTypeId="wideTypeId_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
			$floor="floor_".$j;
			//$grey_rate="greyRate_".$j;
			//$dyeing_charge="dyeingCharge_".$j;
			$preReprocess = "preReprocess_" . $j;
			$reProcess = "reprocess_" . $j;
			$IsSalesId = "IsSalesId_".$j;
			$bookingWithoutOrder = "bookingWithoutOrder_".$j;
			$bookingNumber = "bookingNumber_".$j;
		   if($$activeId==1)
		   {

		   		if($scnned[$$barcodeNo][$$reProcess] != "")
				{
					echo "20**Barcode already received.\nBarcode no: ".$$barcodeNo;
					die;

				}
				else if($self_scnned[$$barcodeNo][$$reProcess] != "")
				{
					echo "20**Barcode already delivered.\nBarcode no: ".$$barcodeNo;
					die;
				}


				//$cons_rate=$$grey_rate+$$dyeing_charge;
				$cons_rate=0;
				$amount=$$currentWgt*$cons_rate;
				$cbo_room = str_replace("'", "", $$room);
				$cbo_rack = str_replace("'", "", $$rack);
				$cbo_shelf = str_replace("'", "", $$self);
				$cbo_floor = str_replace("'", "", $$floor);
				
				if($cbo_floor==""){$cbo_floor=0;}
				if($cbo_room==""){$cbo_room=0;}
				if($cbo_rack==""){$cbo_rack=0;}
				if($cbo_shelf==""){$cbo_shelf=0;}

				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$id_dtls=return_next_id_by_sequence( "INV_FIN_FAB_ISSUE_DTLS_PK_SEQ", "inv_finish_fabric_issue_dtls", $con);
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		
				if($data_array_roll!="") $data_array_roll.= ",";
				if($data_array_trans!="") $data_array_trans.= ",";
				if($data_array_dtls!="") $data_array_dtls.= ",";
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_trans.="(".$id_trans.",".$finish_update_id.",'".$$batchId."',".$cbo_company_id.",".$$productId.",".$category_id.",2,".$txt_delivery_date.",".$cbo_store_name.",".$$bodyPart.",".$$currentWgt.",'".$cons_rate."','".$amount."','".$$rejectQty."','".$cbo_floor."','".$cbo_room."','".$cbo_rack."','".$cbo_shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
				$data_array_dtls .="(".$id_dtls.",".$finish_update_id.",".$id_trans.",'".$$batchId."',".$$productId.","."0".",".$$currentWgt.",0,0,0,".$cbo_store_name.",1,".$$bodyPart.",'".$cbo_rack."','".$cbo_shelf."','".$cbo_floor."','".$cbo_room."',".$hdn_fso_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$wideTypeId."')";

				$data_array_roll.="(".$id_roll.",".$finish_update_id.",".$id_dtls.",".$hdn_fso_id.",$entry_form,'".$$rollwgt."','".$$rollId."','".$$rollNo."','".$$barcodeNo."','".$$rejectQty."','".$$currentWgt."','".$cons_rate."','".$amount."','" . $$preReprocess . "','" . $$reProcess . "',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "','".$$bookingWithoutOrder. "','".$$bookingNumber. "')";
				
				$data_array_prop.="(".$id_prop.",".$id_trans.",2,$entry_form,".$id_dtls.",".$hdn_fso_id.",".$$productId.",'".$$colorId."','".$$currentWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "')";
				$prodData_array[$$productId]+=$$currentWgt;
				$prodData_amount_array[$prod_id[$i]]+=$cons_amount;
				$barcodeNos.=$j."__".$id_dtls."__".$id_trans."__".$id_roll."__".$$currentWgt.",";
				$all_prod_id.=$$productId.",";

				$i++;
		   }
		}

		$prod_id_array=array();
		$all_prod_id=implode(",",array_unique(array_filter(explode(",",chop($all_prod_id,",")))));
		$field_array_prod_update = "last_purchased_qnty*current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		$prodResult=sql_select("select id, current_stock,avg_rate_per_unit,stock_value from product_details_master where id in($all_prod_id)");
		foreach($prodResult as $row)
		{
			$issue_qty=$prodData_array[$row[csf('id')]];
			$product_amount=$prodData_amount_array[$row[csf('id')]];
			$current_stock=$row[csf('current_stock')]-$issue_qty;
			$stockValue=$row[csf('stock_value')]-$product_amount;
			if($current_stock>0)
			{
				$avg_rate_per_unit=number_format($stockValue/$current_stock,$dec_place[3],'.','');
			}else{
				$avg_rate_per_unit=0;
				$stockValue=0;
			}
			
			$prod_id_array[]=$row[csf('id')];
			$data_array_prod_update[$row[csf('id')]]=explode("*",($prodData_array[$row[csf('id')]]."*'".$current_stock."'*'".$avg_rate_per_unit."'*'".$stockValue."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		}

		$rID=sql_insert("inv_issue_master",$field_array,$data_array,0);
		if($rID) $flag=1; else $flag=0; 

		$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		if($flag==1) 
		{
			if($prodUpdate) $flag=1; else $flag=0; 
		}

		//echo "10**insert into inv_transaction ($field_array_trans) values $data_array_trans"; oci_rollback($con);die;
		$rID3=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}

		$rID4=sql_insert("inv_finish_fabric_issue_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}

		$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		}

		$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
		if($flag==1) 
		{
			if($rID6) $flag=1; else $flag=0; 
		}
		
		//echo "10**".$flag."##".$rID."##".$rID3."##".$rID4."##".$rID5."##".$rID6."##".$prodUpdate;oci_rollback($con);die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$finish_update_id."**".$finish_fabric_issue_num;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "5**0**";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "0**".$finish_update_id."**".$finish_fabric_issue_num;
			}
			else
			{
				oci_rollback($con);
				echo "5**0**";
			}
		}

		disconnect($con);
		die;
	}
	else if ($operation==1)
	{
		$con = connect();		
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$category_id=2; $entry_form=318;

		$field_array_update="issue_date*fso_no*fso_id*booking_no*booking_id*coure_tube*remarks*delivery_addr*challan_no*updated_by*update_date";		
		$data_array_update=$txt_delivery_date."*".$txt_fso_no."*".$hdn_fso_id."*".$txt_booking_no."*".$hdn_booking_id."*".$txt_coure_tube."*".$txt_remarks."*".$txt_delivery_addr."*".$txt_challan_no."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$field_array_trans="id, mst_id, batch_id, company_id, prod_id, item_category, transaction_type, transaction_date,store_id, body_part_id, cons_quantity,cons_rate, cons_amount,cons_reject_qnty,floor_id, room, rack, self,  inserted_by, insert_date";

		$field_array_dtls="id, mst_id, trans_id, batch_id, prod_id, uom, issue_qnty,rate,rate_in_usd,fabric_shade,store_id, no_of_roll, body_part_id, rack_no,shelf_no,floor,room, order_id,inserted_by,insert_date,width_type";

		$field_array_roll="id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty,roll_id, roll_no,barcode_no,reject_qnty,qc_pass_qnty,rate, amount,prev_reprocess,reprocess,inserted_by, insert_date, is_sales, booking_without_order, booking_no";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, color_id, quantity, inserted_by, insert_date,is_sales";
		$field_array_dtls_update="status_active*is_deleted*updated_by*update_date";

		//$update_mst_id

		$cur_st_qnty=0;
		$barcodeNos="";
		for($j=1;$j<=$tot_row;$j++)
		{ 	
			$activeId="activeId_".$j;
			$transId="transId_".$j;
			$rollTableId="rollTableId_".$j;
			$updateDetailsId="updateDetailsId_".$j;

			$rollId="rollId_".$j;
			$batchId="batchId_".$j;
			$bodyPart="bodyPart_".$j;
			$colorId="colorId_".$j;
			$deterId="deterId_".$j;
			$productId="productId_".$j;
			//$orderId="orderId_".$j;
			$rollGsm="rollGsm_".$j;
			$rollDia="rolldia_".$j;
			$job_no="job_no_".$j;
			$rollwgt="rollQty_".$j;
			$rolldia="rolldia_".$j;
			$barcodeNo="barcodeNo_".$j;
			$rollNo="rollNo_".$j;
			$currentWgt="currentWgt_".$j;
			$rejectQty="rejectQty_".$j;
			$wideTypeId="wideTypeId_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
			$floor="floor_".$j;
			//$grey_rate="greyRate_".$j;
			//$dyeing_charge="dyeingCharge_".$j;
			$preReprocess = "preReprocess_" . $j;
			$reProcess = "reprocess_" . $j;
			$IsSalesId = "IsSalesId_".$j;
			$bookingWithoutOrder = "bookingWithoutOrder_".$j;
			$bookingNumber = "bookingNumber_".$j;

			//echo "10**".$$activeId."=".$$barcodeNo;die;
		   if($$activeId==1)
		   {
				//$cons_rate=$$grey_rate+$$dyeing_charge;
				$cons_rate=0;
				$amount=$$currentWgt*$cons_rate;

				$cbo_room = str_replace("'", "", $$room);
				$cbo_rack = str_replace("'", "", $$rack);
				$cbo_shelf = str_replace("'", "", $$self);
				$cbo_floor = str_replace("'", "", $$floor);
				
				if($cbo_floor==""){$cbo_floor=0;}
				if($cbo_room==""){$cbo_room=0;}
				if($cbo_rack==""){$cbo_rack=0;}
				if($cbo_shelf==""){$cbo_shelf=0;}

				if(str_replace("'","",$$updateDetailsId) == 0 || str_replace("'","",$$updateDetailsId) == "")
				{
					if($scnned[$$barcodeNo][$$reProcess] != "")
					{
						echo "20**Barcode already received.\nBarcode no: ".$$barcodeNo;
						die;

					}
					else if($self_scnned[$$barcodeNo][$$reProcess] != "")
					{
						echo "20**Barcode already delivered.\nBarcode no: ".$$barcodeNo;
						die;
					}


					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					$id_dtls=return_next_id_by_sequence( "INV_FIN_FAB_ISSUE_DTLS_PK_SEQ", "inv_finish_fabric_issue_dtls", $con);
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			
					$data_array_trans[$id_trans] ="(".$id_trans.",".$update_mst_id.",'".$$batchId."',".$cbo_company_id.",".$$productId.",".$category_id.",2,".$txt_delivery_date.",".$cbo_store_name.",".$$bodyPart.",".$$currentWgt.",'".$cons_rate."','".$amount."','".$$rejectQty."','".$cbo_floor."','".$cbo_room."','".$cbo_rack."','".$cbo_shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
					$data_array_dtls[$id_dtls] ="(".$id_dtls.",".$update_mst_id.",".$id_trans.",'".$$batchId."',".$$productId.","."0".",".$$currentWgt.",0,0,0,".$cbo_store_name.",1,".$$bodyPart.",'".$cbo_rack."','".$cbo_shelf."','".$cbo_floor."','".$cbo_room."',".$hdn_fso_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$$wideTypeId."')";

					$data_array_roll[$id_roll] ="(".$id_roll.",".$update_mst_id.",".$id_dtls.",".$hdn_fso_id.",$entry_form,'".$$rollwgt."','".$$rollId."','".$$rollNo."','".$$barcodeNo."','".$$rejectQty."','".$$currentWgt."','".$cons_rate."','".$amount."','" . $$preReprocess . "','" . $$reProcess . "',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "','".$$bookingWithoutOrder. "','".$$bookingNumber. "')";
					
					$data_array_prop[$id_prop] ="(".$id_prop.",".$id_trans.",2,$entry_form,".$id_dtls.",".$hdn_fso_id.",".$$productId.",'".$$colorId."','".$$currentWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time. "','" . $$IsSalesId . "')";
					$prodData_array[$$productId]+=$$currentWgt;
					$prodData_amount[$$productId]+=$cons_amount;
					
				}
		   }
		   else
		   {
		   		//echo "10**=0=".$$updateDetailsId;die;
		   		if($$updateDetailsId!="")
				{

					if($scnned[$$barcodeNo][$$reProcess] != "")
					{
						echo "20**Barcode already received.\nBarcode no: ".$$barcodeNo;
						die;

					}


					//echo "10**=1=".$$updateDetailsId;die;
					$updateDetailsId_arr[]=$$updateDetailsId;
					//$data_array_dtls_up[$$updateDetailsId] = explode("*", ("0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					$updateTransId_arr[]=$$transId;
					//$data_array_trans_up[$$transId] = explode("*", ("0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					$updateRollTableId_arr[]=$$rollTableId;
					//$data_array_roll_up[$$rollTableId] = explode("*", ("0*1*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));

					$adj_prod_array[$$productId]+=$$currentWgt;
					$adj_prod_amount[$$productId]+=$cons_amount;
				}
				

		   }

		   $all_prod_id_arr[$$productId] = $$productId;
		}



		$updateDetailsId_arr = array_unique(array_filter($updateDetailsId_arr));
		$all_delete_details_ids=implode(",",$updateDetailsId_arr);

		$updateTransId_arr = array_unique(array_filter($updateTransId_arr));
		$all_delete_trans_ids=implode(",",$updateTransId_arr);

		$updateRollTableId_arr = array_unique(array_filter($updateRollTableId_arr));
		$all_delete_roll_table_ids=implode(",",$updateRollTableId_arr);

		$prod_id_array=array();
		$all_prod_ids=implode(",",array_unique(array_filter($all_prod_id_arr)));

		if(!empty($all_prod_id_arr))
		{
			$field_array_prod_update = "last_issued_qnty*current_stock*stock_value*avg_rate_per_unit*updated_by*update_date";
			$prodResult=sql_select("select id, current_stock,stock_value,avg_rate_per_unit from product_details_master where id in($all_prod_ids)");

			foreach($prodResult as $row)
			{
				$issue_qty=$prodData_array[$row[csf('id')]];
				$current_stock=$row[csf('current_stock')]+$adj_prod_array[$row[csf('id')]]-$issue_qty;
				
				$stock_value=$row[csf('stock_value')]+$adj_prod_amount[$row[csf('id')]]-$prodData_amount[$row[csf('id')]];
				if($current_stock>0)
				{
					$avg_rate=$stock_value/$current_stock;
				}else{
					$avg_rate=0;
					$stock_value=0;
				}
				
				$prod_id_array[$row[csf('id')]]=$row[csf('id')];
				if(is_nan($avg_rate)) $avg_rate=0;

				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$prodData_array[$row[csf('id')]]."'*'".$current_stock."'*'".$stock_value."'*'".$avg_rate."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}


		$flag=1;
		if($all_delete_details_ids !="")
		{
			$delete_prop=execute_query("update order_wise_pro_details set status_active =0 , is_deleted=1 where entry_form=318 and dtls_id in ($all_delete_details_ids)",0);

			if($delete_prop) $flag=1; else $flag=0;
		}
		
		$rID=sql_update("inv_issue_master",$field_array_update,$data_array_update,"id",$update_mst_id,1);
		if($rID) $flag=1; else $flag=0; 

		if(count($data_array_dtls)>0)
		{
			if($flag ==1)
			{
				$data_array_trans_set=array_chunk($data_array_trans,200);
				foreach( $data_array_trans_set as $setRows)
				{
					//echo "10** insert into inv_transaction ($field_array_trans) values ".implode(",",$setRows);oci_rollback($con);die;
					$rID2=sql_insert("inv_transaction",$field_array_trans,implode(",",$setRows),0);
					if($rID2==1) $flag=1;
					else if($rID2==0)
					{
						$flag=0;
						oci_rollback($con);
						echo "10**";
						disconnect($con);die;
					}
				}
			}

			if($flag ==1)
			{
				$data_array_dtls_set=array_chunk($data_array_dtls,200);
				foreach( $data_array_dtls_set as $setRows)
				{
					$rID3=sql_insert("inv_finish_fabric_issue_dtls",$field_array_dtls,implode(",",$setRows),0);
					if($rID3==1) $flag=1;
					else if($rID3==0)
					{
						$flag=0;
						oci_rollback($con);
						echo "10**";
						disconnect($con);die;
					}
				}
			}

			if($flag ==1)
			{
				$data_array_roll_set=array_chunk($data_array_roll,200);
				foreach($data_array_roll_set as $setRows)
				{
					$rID4=sql_insert("pro_roll_details",$field_array_roll,implode(",",$setRows),0);
					if($rID4==1) $flag=1;
					else if($rID4==0)
					{
						$flag=0;
						oci_rollback($con);
						echo "10**";
						disconnect($con);die;
					}
				}
			}

			if($flag ==1)
			{
				$data_array_prop_set=array_chunk($data_array_prop,200);
				foreach($data_array_prop_set as $setRows)
				{
					$rID5=sql_insert("order_wise_pro_details",$field_array_proportionate,implode(",",$setRows),0);
					if($rID5==1) $flag=1;
					else if($rID5==0)
					{
						$flag=0;
						oci_rollback($con);
						echo "10**";
						disconnect($con);die;
					}
				}
			}
		}

		if(count($data_array_prod_update) > 0)
		{
			if($flag ==1)
			{
				$prodUpdate=execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, array_values($prod_id_array )));
				if($prodUpdate==1) $flag=1; else $flag=0;
			}
		}
		
		//echo "10**".$all_delete_details_ids;die;
		if($all_delete_details_ids!="")
		{
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$all_delete_trans_ids,0);
			if($flag ==1)
			{
				if($statusChangeTrans==1) $flag=1; else $flag=0;
			}
			$statusChangeDtls=sql_multirow_update("inv_finish_fabric_issue_dtls",$field_array_status,$data_array_status,"id",$all_delete_details_ids,0);
			if($flag ==1)
			{
				if($statusChangeDtls==1) $flag=1; else $flag=0;
			}
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$all_delete_roll_table_ids,0);
			if($flag ==1)
			{
				if($statusChangeRoll==1) $flag=1; else $flag=0;
			}
		}

		//echo "10** $flag ## $rID ## $rID2 ## $rID3 ## $rID4 ## $rID5  ## $delete_prop ## $prodUpdate ## $statusChangeTrans ## $statusChangeDtls ## $statusChangeRoll";die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'","",$update_mst_id)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "6**1";
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con); 
				echo "1**".str_replace("'","",$update_mst_id)."**".str_replace("'","",$txt_system_id);
			}
			else
			{
				oci_rollback($con);
				echo "6**1";
			}
		}	
		disconnect($con);
		die;

	}
}

if($action=='populate_data_from_to_garments')
{
	$data_array=sql_select("select id, store_id,company_id,location_id,issue_date, coure_tube, remarks, delivery_addr, challan_no from inv_issue_master where id='$data'");
	
	foreach ($data_array as $row)
	{ 
		echo "load_drop_down('requires/finish_feb_roll_delivery_to_garments_controller', '".$row[csf('location_id')]."_".$row[csf('company_id')]."', 'load_drop_down_store','store_td');\n";
		echo "document.getElementById('cbo_store_name').value 		= '".$row[csf("store_id")]."';\n";
		echo "document.getElementById('txt_delivery_date').value 	= '".change_date_format($row[csf("issue_date")])."';\n";
		echo "document.getElementById('txt_coure_tube').value 		= '".$row[csf("coure_tube")]."';\n";
		echo "document.getElementById('txt_remarks').value 			= '".$row[csf("remarks")]."';\n";
		echo "document.getElementById('txt_delivery_addr').value 	= '".$row[csf("delivery_addr")]."';\n";
		echo "document.getElementById('txt_challan_no').value 		= '".$row[csf("challan_no")]."';\n";

		exit();
	}
}

if ($action=="systemId_popup")
{
	echo load_html_head_contents("System ID Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(data)
		{
			var dataValue = data.split("**");
			$('#hidden_sys_id').val(dataValue[0]);
			$('#hidden_batch_id').val(dataValue[1]);
			$('#hidden_sales_id').val(dataValue[2]);
			$('#hidden_booking_no').val(dataValue[3]);
			$('#hidden_buery_id').val(dataValue[4]);
			$('#hidden_po_company_id').val(dataValue[5]);
			$('#hidden_fso_no').val(dataValue[6]);
			$('#hidden_batch_no').val(dataValue[7]);
			$('#hidden_po_job_no').val(dataValue[8]);
			$('#hidden_po_company_name').val(dataValue[9]);
			$('#hidden_location').val(dataValue[10]);
			$('#hidden_sys_number').val(dataValue[11]);
			$('#hidden_within_group').val(dataValue[12]);
			$('#hidden_store_id').val(dataValue[13]);
			$('#hidden_booking_id').val(dataValue[14]);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:980px;">
			<form name="searchsystemidfrm"  id="searchsystemidfrm">
				<fieldset style="width:970px;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="800" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Delivery Date Range</th>
							<th>Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up">Please Enter System Id</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px;" class="formbutton" />
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_sys_id" id="hidden_sys_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_sys_number" id="hidden_sys_number" class="text_boxes" value="">
								<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_sales_id" id="hidden_sales_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_fso_no" id="hidden_fso_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_buery_id" id="hidden_buery_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_po_company_id" id="hidden_po_company_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_po_company_name" id="hidden_po_company_name" class="text_boxes" value="">
								<input type="hidden" name="hidden_po_job_no" id="hidden_po_job_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_location" id="hidden_location" class="text_boxes" value="">
								<input type="hidden" name="hidden_grey_sys_id" id="hidden_grey_sys_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_grey_sys_number" id="hidden_grey_sys_number" class="text_boxes" value="">
								<input type="hidden" name="hidden_sys_dtls_id" id="hidden_sys_dtls_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_within_group" id="hidden_within_group" class="text_boxes" value="">
								<input type="hidden" name="hidden_store_id" id="hidden_store_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_party_id" id="hidden_party_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px;">To<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px;">
							</td>
							<td id="">
								<?
								echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   
								?>
							</td>
							<td>
								<?
								$search_by_arr=array(1=>"System ID",2=>"Sales Order No",3=>"Booking No",4=>"Batch No");
								$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
								echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
								?>
							</td>
							<td id="search_by_td">
								<input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_finish_search_list_view', 'search_div', 'finish_feb_roll_delivery_to_garments_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="margin-top:10px; margin-left:3px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit;
}

if($action=="create_finish_search_list_view")
{
	$data = explode("_",$data);
	$search_string=trim($data[0]);
	$search_by=$data[1];
	$start_date =$data[2];
	$end_date =$data[3];
	$company_id =$data[4];
	$buyer_id =$data[5];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	if($buyer_id!=0) $buyer_cond="and a.buyer_id=$buyer_id";
	else $buyer_cond="";

	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date), "yyyy-mm-dd", "-")."' and '".change_date_format(trim($end_date), "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$date_cond="and a.issue_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}
	}
	else
	{
		$date_cond="";
	}
	
	if(trim($data[0])!="")
	{
		if($search_by==1)
			$search_field_cond="and a.issue_number_prefix_num='$search_string'";
		else if($search_by==2)
			$search_field_cond="and d.job_no_prefix_num='$search_string'";
		else if($search_by==3)
			$search_field_cond="and d.sales_booking_no like '%$search_string'";
		else 
			$search_field_cond="and c.batch_no like '%$search_string'";
	}
	else
	{
		$search_field_cond="";
	}

	$delivery_sql="SELECT a.id, issue_number_prefix_num, to_char(a.insert_date,'YYYY') as year, a.issue_number, a.challan_no, a.company_id, a.issue_date,a.issue_purpose,a.supplier_id party_name, a.buyer_id,a.location_id,a.store_id, b.sample_type, sum(b.issue_qnty) as issue_qnty, listagg(cast(c.batch_no as varchar2(4000)), ',') within group (order by c.id) as batch_no,d.id order_id,d.job_no,d.sales_booking_no, d.booking_id, d.buyer_id,d.within_group,d.po_buyer,d.po_company_id, d.po_job_no from inv_issue_master a, inv_finish_fabric_issue_dtls b, pro_batch_create_mst c,fabric_sales_order_mst d where a.entry_form=318 and a.id=b.mst_id and b.batch_id=c.id and b.order_id=to_char(d.id) and a.item_category=2 and a.company_id=$company_id $search_field_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $date_cond group by a.id, issue_number_prefix_num, a.issue_number, a.challan_no, a.company_id, a.issue_date, a.issue_purpose,a.supplier_id, a.buyer_id,a.location_id,a.store_id, b.sample_type, a.insert_date,d.id,d.job_no,d.sales_booking_no,d.booking_id, d.buyer_id,d.within_group,d.po_buyer,d.po_company_id, d.po_job_no order by a.id";

	$deliveryData = sql_select($delivery_sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="100">Company</th>
			<th width="50">System ID</th>
			<th width="50">Year</th>
			<th width="80">Within Group</th>
			<th width="100">Buyer Name</th>
			<th width="120">FSO No</th>
			<th width="100">Booking No</th>
			<th width="100">Batch No</th>
			<th width="80">Delivery date</th>
			<th width="80">Delivery Qnty</th>
		</thead>
	</table>
	<div style="width:890px; max-height:240px; overflow-y:scroll" id="list_container_batch" align="left">	 
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="870" class="rpt_table" id="tbl_list_search">  
			<?
			$i=1;
			foreach ($deliveryData as $row)
			{  
				if ($i%2==0)  
					$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";	 
				$buyer_id = ($row[csf('within_group')]==1)?$row[csf('po_buyer')]:$row[csf('buyer_id')];
				$data = $row[csf('id')] . "**" . $row[csf('batch_id')] . "**" . $row[csf('order_id')] . "**" . $row[csf('sales_booking_no')] . "**" . $buyer_id . "**" . $row[csf('party_name')] . "**" . $row[csf('job_no')] . "**" . $row[csf('batch_no')] . "**" . $row[csf('po_job_no')] . "**" . $company_arr[$row[csf('party_name')]] . "**" . $row[csf('location_id')] . "**" . $row[csf('issue_number')] . "**" . $row[csf('within_group')] . "**" . $row[csf('store_id')]."**".$row[csf('booking_id')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $data; ?>');"> 
					<td width="40" align="center"><? echo $i; ?></td>
					<td width="100" align="center"><p><? echo $company_arr[$row[csf('company_id')]]; ?></p></td>
					<td width="50" align="center"><p><? echo $row[csf('issue_number_prefix_num')]; ?></p></td>
					<td width="50" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
					<td width="100" align="center"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
					<td width="120" align="center"><p><? echo $row[csf('job_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf('batch_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('issue_date')]); ?></td>
					<td width="80" align="right"><? echo number_format($row[csf('issue_qnty')],2); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?	
	exit();
}

if($action=="finish_delivery_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];
	$storeId=$data[4];
	$location=$data[5];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	?>
    <div style="width:1010px;">
    	<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td  align="left" colspan="2" rowspan="4">
				<?
				foreach($data_array as $img_row)
				{
					?>
					<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='90' width='100' align="middle" />
					<?
				}
				?>
			</td>
			<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
			
				<td align="center">
					<?
 					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
					foreach ($nameArray as $result)
					{ 
											 
						 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
						 <? echo $result[csf('email')];?> 
						 <? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			
			<tr>
				
				<td align="center" style="font-size:16px"><strong><u>Finish Fabric Roll Delivery To Garments</u></strong></td>
			</tr>
            <tr>
            	
				<td align="center" style="font-size:18px"><strong><u>Challan No <? echo $txt_challan_no; ?></u></strong></td>
			</tr>
        </table> 
        <br>
        <?
            $sql_data= sql_select("select a.challan_no,a.issue_number,a.company_id, a.knit_dye_source, a.knit_dye_company, a.issue_date,a.store_id,a.location_id, b.within_group, b.po_company_id, b.buyer_id
            from  inv_issue_master a, fabric_sales_order_mst b
            where a.entry_form=318 and a.fso_id=b.id and a.company_id=$company and a.issue_number='$txt_challan_no'");
		?>
        
		<table width="1110" cellspacing="0" align="center" border="0">
			<tr>
			
			<td style="font-size:16px; font-weight:bold;" width="100">Party :</td>
                <td width="200">
                <? 
                if($sql_data[0][csf('within_group')]==1) 
                {
                	echo  $company_array[$sql_data[0][csf('po_company_id')]]['name'];
                }
				else  
				{
					echo $buyer_arr[$sql_data[0][csf('buyer_id')]];
				}

                ?>
                	
                </td>
                
                <td style="font-size:16px; font-weight:bold;" width="150">Delivery Date :</td>
                <td width="200"  align=""><? echo change_date_format($sql_data[0][csf('issue_date')]); ?></td>
                <td style="font-size:16px; font-weight:bold;" width="190"></td>
                <!-- Dye/Finishing Company : -->
                <td width="200">&nbsp;
                 <?
				 //if($sql_data[0][csf('knitting_source')]==1) echo  $company_array[$sql_data[0][csf('knitting_company')]]['name'];
				 //else  echo $supplier_arr[$sql_data[0][csf('knitting_company')]];
				 ?>
				</td>
			</tr>
			<tr>
				<td width="">&nbsp;</td>	
             	
		    </tr>
            <tr>
				<td width="" id="barcode_img_id"  colspan="2"></td>	
             	
		    </tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Barcode No</th>
                    <th width="40">Roll No</th>
                    <th width="60">Batch No</th>
                    <th width="70">Body Part</th>
                    <th width="70">Construction</th>
                    <th width="70">Composition</th>
                    <th width="70"> Color</th>
                    <th width="50">GSM</th>
                    <th width="40">Dia</th>
                    <th width="70">Roll Qty.</th> 
                    <th width="70">Reject Qty.</th> 
                    <th width="70">Process Loss</th> 
                    <th width="70">Grey Wgt.</th> 
                    <th width="60">Dia/ Width Type</th> 
                    <th width="40">Year</th> 
                    <th width="70">Job No</th>
                    <th width="100">Buyer</th>  

                    <th width="120">Order No</th>
                    <th width="120">Fab. Booking No</th>                    
                </tr>
            </thead>
            <?
			
			$data_array=sql_select("SELECT a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, b.id as dtls_id, b.prod_id,b.color_id ,b.batch_id, b.fabric_description_id,b.body_part_id,b.dia_width_type, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.reject_qnty FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=66 and c.entry_form=66 and c.status_active=1 and c.is_deleted=0");
			$roll_details_array=array(); $barcode_array=array(); 
			foreach($data_array as $row)
			{
				
				$roll_details_array[$row[csf("barcode_no")]]['knitting_source_id']=$row[csf("knitting_source")];
				$roll_details_array[$row[csf("barcode_no")]]['knitting_source']=$knitting_source[$row[csf("knitting_source")]];
				$roll_details_array[$row[csf("barcode_no")]]['knitting_company_id']=$row[csf("knitting_company")];
				
				if($row[csf("knitting_source")]==1)
				{
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$company_array[$row[csf("knitting_company")]]['name'];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$roll_details_array[$row[csf("barcode_no")]]['knitting_company']=$supplier_arr[$row[csf("knitting_company")]];
				}
			
			}
		
			$i=1; 
			if($db_type ==0){
				$select_year = " year(c.insert_date) as year,";
			}else{
				$select_year = " to_char(c.insert_date,'YYYY') as year,";
			}
			$sql_update=sql_select("SELECT a.id as roll_table_id, a.barcode_no, a.dtls_id, $select_year b.trans_id, a.roll_id,a.roll_no,b.batch_id,e.batch_no, a.po_breakdown_id, a.booking_without_order, a.is_sales, a.reprocess, a.prev_reprocess, b.body_part_id, b.trans_id,b.prod_id, d.gsm, d.dia_width, d.detarmination_id, d.color as color_id, a.qnty ,a.reject_qnty ,b.floor, b.room, b.rack_no, b.shelf_no, b.width_type,  c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, c.buyer_id, c.po_buyer, c.po_job_no, c.po_company_id, c.within_group, null as recv_number 
			from pro_roll_details a, inv_finish_fabric_issue_dtls b, fabric_sales_order_mst c , product_details_master d, pro_batch_create_mst e 
			where a.dtls_id=b.id and a.entry_form=318 and a.po_breakdown_id=c.id and b.batch_id=e.id and b.prod_id = d.id and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$update_id and a.is_returned!=1");

			$barcode_NOs="";
			foreach($sql_update as $row)
			{
				$barcode_NOs.=$row[csf("barcode_no")].",";
			}
			$barcode_Nos_all=rtrim($barcode_NOs,","); 
			$barcode_Nos_alls=explode(",",$barcode_Nos_all);
			$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999); 
			$barcode_no_conds=" and";
			foreach($barcode_Nos_alls as $dtls_id)
			{
				if($barcode_no_conds==" and")  $barcode_no_conds.="(a.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or a.barcode_no in(".implode(',',$dtls_id).")";
			}
			$barcode_no_conds.=")";
			$production_sql_data=sql_select("SELECT a.barcode_no, a.id as roll_id, a.roll_no, a.po_breakdown_id, a.qnty as prod_qty, a.qc_pass_qnty, a.reject_qnty 
			FROM pro_roll_details a 
			WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 $barcode_no_conds");
			$production_data_arr=array();
			foreach($production_sql_data as $value)
			{
				$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
				$production_data_arr[$value[csf("barcode_no")]]['qc_pass_qnty']=$value[csf("qc_pass_qnty")];	
				$production_data_arr[$value[csf("barcode_no")]]['reject_qnty']=$value[csf("reject_qnty")];	
			}
			// echo "<pre>";print_r($production_data_arr);

			foreach($sql_update as $row)
			{
				$prod_qty=$production_data_arr[$row[csf('barcode_no')]]['prod_qty'];
				$qc_pass_qnty=$production_data_arr[$row[csf('barcode_no')]]['qc_pass_qnty'];
				$reject_qnty=$production_data_arr[$row[csf('barcode_no')]]['reject_qnty'];
				$processLoss=($prod_qty-($qc_pass_qnty+$reject_qnty));
				// $grey_used = $row[csf("qnty")]+$row[csf("reject_qnty")];
				$grey_used = $prod_qty;

				if($row[csf("within_group")] == 1)
				{
					$buyer_id = $row[csf("po_buyer")];
				}else{
					$buyer_id = $row[csf("buyer_id")];
				}
				$buyer_name = $buyer_arr[$buyer_id]; 
				?>
            	<tr>
                    <td width="30"><? echo $i; ?></td>
                    <td width="100" style="word-break:break-all;"><? echo $row[csf('barcode_no')]; ?></td>
                    <td width="40" align="center"><? echo $row[csf("roll_no")]; ?></td>
                    <td width="60" style="word-break:break-all;" align="center"><? echo $row[csf('batch_no')]; ?></td>
                    <td width="70" style="word-break:break-all;" align="center"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
                    <td width="70" style="word-break:break-all;" align="center"><? echo $constructtion_arr[$row[csf('detarmination_id')]]; ?></td>
                    <td width="70" style="word-break:break-all;" align="center"><? echo $composition_arr[$row[csf('detarmination_id')]]; ?></td>
                    <td width="70" style="word-break:break-all;" align="center"><? echo $color_arr[$row[csf("color_id")]]; ?></td>
                    <td width="50" align="center" ><? echo  $row[csf('gsm')];  ?></td>
                    <td width="40" align="center"><? echo $row[csf('dia_width')]; ?></td>
                    <td width="70" align="right"><? echo number_format($row[csf('qnty')],2); ?></td>

                    <td width="70" align="right"><? echo number_format($row[csf("reject_qnty")],2); ?></td>
                    <td width="70" align="right"><? echo number_format($processLoss); ?></td>
                    <td width="70" align="right"><? echo number_format($grey_used,2); ?></td>
                    
                    <td width="60" style="word-break:break-all;" align="center"><? echo $fabric_typee[$row[csf('width_type')]]; ?></td>
                    <td width="40" align="center" ><? echo  $row[csf("year")];  ?></td>
                    <td width="70"><? echo $row[csf("po_job_no")];?></td>
                    <td width="100"><? echo $buyer_name; ?></td>
            		<td width="120"><? echo $row[csf("job_no")]; ?></td>
            		<td width="120"><? echo $row[csf("sales_booking_no")]; ?></td>
                    
                </tr>
            	<?
				$tot_qty+=$row[csf('qnty')];
				$tot_reject_qty+=$row[csf('reject_qnty')];
				$tot_processLoss+=$processLoss;
				$tot_grey_used+=$grey_used;
				$i++;
			}
			?>
            <tr> 
                <td align="right" colspan="10"><strong>Total</strong></td>
                <td align="right"><strong><? echo number_format($tot_qty,2,'.',''); ?></strong></td>
                <td align="right"><strong><? echo number_format($tot_reject_qty,2,'.',''); ?></strong></td>
                <td align="right"><strong><? echo number_format($tot_processLoss,2,'.',''); ?></strong></td>
                <td align="right"><strong><? echo number_format($tot_grey_used,2,'.',''); ?></strong></td>
                <td align="right" colspan="6">&nbsp;</td>
			</tr>
		</table>
	</div>
    <? echo signature_table(246, $company, "1210px"); ?>
   	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 40,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if($action=="finish_delivery_print2")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];
	$storeId=$data[4];
	$location=$data[5];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country where status_active=1 and is_deleted=0", "id", "country_name"  );
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	?>
    <div style="width:1010px;">
    	<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td  align="left" colspan="2" rowspan="4">
				<?
				foreach($data_array as $img_row)
				{
					?>
					<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='90' width='100' align="middle" />
					<?
				}
				?>
			</td>
			<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
			
				<td align="center">
					<?
 					$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company"); 
					foreach ($nameArray as $result)
					{ 
											 
						 echo $result[csf('plot_no')].', '.$result[csf('level_no')].', '.$result[csf('road_no')].', '.$result[csf('block_no')].', '.$result[csf('city')].', '.$result[csf('zip_code')].', '.$result[csf('province')].', '.$country_arr[$result[csf('country_id')]]; ?><br> 
						 <? echo $result[csf('email')];?> 
						 <? echo $result[csf('website')];
					}
					?>
				</td>
			</tr>
			
			<tr>
				
				<td align="center" style="font-size:16px"><strong>Finish Fabric Roll Delivery To Garments</strong></td>
			</tr>
        </table> 
        <br>
        <?
            $sql_data= sql_select("SELECT a.challan_no, a.issue_number,a.company_id, a.knit_dye_source, a.knit_dye_company, a.issue_date,a.store_id,a.location_id, b.within_group, b.po_company_id, b.buyer_id, a.coure_tube, a.remarks
            from  inv_issue_master a, fabric_sales_order_mst b
            where a.entry_form=318 and a.fso_id=b.id and a.company_id=$company and a.issue_number='$txt_challan_no'");
		?>
        
		<table width="1250" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">DV TO</td>
                <td width="200">:&nbsp;<strong>
                <? 
                if($sql_data[0][csf('within_group')]==1) 
                {
                	$po_company_id=$sql_data[0][csf('po_company_id')];
                	echo $company_array[$po_company_id]['name'];               	
                }
				else  
				{
					$fso_buyer_id=$sql_data[0][csf('buyer_id')];
					echo $buyer_arr[$fso_buyer_id];
				}
                ?></strong>
                </td>
                
                
                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td style="font-size:16px; font-weight:bold;" width="300">Delivery Challan No.</td>
                <td width="250" align="left">:&nbsp;<strong><? echo $sql_data[0][csf('issue_number')]; ?></strong></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Address</td>
                <td width="800">:&nbsp;
                <? 
                if($sql_data[0][csf('within_group')]==1) 
                {
                	$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$po_company_id"); 
					foreach ($nameArray as $result)
					{
						echo $result[csf('city')].', '.$country_arr[$result[csf('country_id')]].'.'; ?>
						<?
					}
                }
				else  
				{
					$buyerNameArray=sql_select( "select address_1, address_2, country_id from lib_buyer where id=$fso_buyer_id"); 
					foreach ($buyerNameArray as $result)
					{					 
						echo $result[csf('address_1')].', '.$country_arr[$result[csf('country_id')]].'.'; ?>
						<?
					}
				}
                ?>
                </td>
                
                
                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td style="font-size:16px; font-weight:bold;" width="150">Trolly/Coure Tube(PCS)</td>
                <td width="200" align="left">:&nbsp;<? echo $sql_data[0][csf('coure_tube')]; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100"></td>
                <td width="800">
                </td>                
                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>

                <td style="font-size:16px; font-weight:bold;" width="150">Delivery Date</td>
                <td width="200" align="left">:&nbsp;<? echo change_date_format($sql_data[0][csf('issue_date')]); ?></td>
			</tr>
		    <tr>
				<td width="" id="barcode_img_id"  colspan="2"></td>	
             	
		    </tr>
		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
            <thead>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Buyer</th>
                    <th width="120">Job No</th>
                    <th width="120">FSO No</th>
                    <th width="120">Booking No</th>
                    <th width="100">Style</th>
                    <th width="60">Batch No</th>
                    <th width="70">Color</th>
                    <th width="70">Shade Type</th>
                    <th width="150">Fabric Type</th>
                    <th width="150">Process Rute</th>
                    <th width="40">Dia</th>
                    <th width="40">Gsm</th>
                    <th width="40">UOM</th>
                    <th width="50">Roll</th>
                    <th width="70">Grey Used</th>
                    <th width="70">F.QTY (KG)</th>         
                </tr>
            </thead>
            <?
			$i=1;
			$sql_update=sql_select("SELECT a.id as roll_table_id, a.barcode_no, a.dtls_id, b.trans_id, a.roll_id,a.roll_no, b.batch_id, e.id, e.batch_no, a.po_breakdown_id, a.booking_without_order, a.is_sales, a.reprocess, a.prev_reprocess, b.body_part_id, b.trans_id, b.prod_id, d.gsm, d.dia_width as dia, d.detarmination_id as deter_id, d.color as color_id, a.qnty ,a.reject_qnty, b.width_type,  c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, c.buyer_id, c.po_buyer, c.po_job_no, c.po_company_id, c.within_group, c.style_ref_no, d.unit_of_measure as uom, null as recv_number 
			from pro_roll_details a, inv_finish_fabric_issue_dtls b, fabric_sales_order_mst c , product_details_master d, pro_batch_create_mst e
			where a.dtls_id=b.id and b.batch_id=e.id and a.po_breakdown_id=c.id and b.prod_id=d.id and a.entry_form=318 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$update_id and a.is_returned!=1");

			$barcode_NOs="";
			foreach($sql_update as $row)
			{
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['within_group']=$row[csf("within_group")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['po_buyer']=$row[csf("po_buyer")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['buyer_id']=$row[csf("buyer_id")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['po_job_no']=$row[csf("po_job_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['job_no']=$row[csf("job_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['sales_booking_no']=$row[csf("sales_booking_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['style_ref_no']=$row[csf("style_ref_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['batch_no']=$row[csf("batch_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['color_id']=$row[csf("color_id")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['uom']=$row[csf("uom")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['fin_qnty']+=$row[csf("qnty")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['roll_count']++;

				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['barcode_no'].=$row[csf("barcode_no")].',';
				$barcode_NOs.=$row[csf("barcode_no")].",";
				$fso_ids.=$row[csf("fso_id")].",";
			}
			// echo "<pre>";print_r($data_grouping);

			$barcode_Nos_all=rtrim($barcode_NOs,","); 
			$barcode_Nos_alls=explode(",",$barcode_Nos_all);
			$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999);
			$barcode_no_conds=" and";
			foreach($barcode_Nos_alls as $dtls_id)
			{
				if($barcode_no_conds==" and")  $barcode_no_conds.="(a.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or a.barcode_no in(".implode(',',$dtls_id).")";
			}
			$barcode_no_conds.=")";

			$production_sql_data=sql_select("SELECT a.barcode_no, a.id as roll_id, a.roll_no, a.po_breakdown_id, a.qnty as prod_qty, a.qc_pass_qnty, a.reject_qnty 
			FROM pro_roll_details a 
			WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 $barcode_no_conds");
			$production_data_arr=array();
			foreach($production_sql_data as $value)
			{
				$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
				$production_data_arr[$value[csf("barcode_no")]]['qc_pass_qnty']=$value[csf("qc_pass_qnty")];	
				$production_data_arr[$value[csf("barcode_no")]]['reject_qnty']=$value[csf("reject_qnty")];
			}
			// echo "<pre>";print_r($production_data_arr);

			$fso_ids_all=implode(",", array_unique(explode(",",rtrim($fso_ids,","))));
			$fso_ids_alls=explode(",",$fso_ids_all);
			$fso_ids_alls=array_chunk($fso_ids_alls,999); 
			$fso_id_conds=" and";
			foreach($fso_ids_alls as $fso_id)
			{
				if($fso_id_conds==" and")  $fso_id_conds.="(b.mst_id in(".implode(',',$fso_id).")"; else $fso_id_conds.=" or b.mst_id in(".implode(',',$fso_id).")";
			}
			$fso_id_conds.=")";
			// echo $fso_id_conds;
			$fso_sql_data=sql_select("SELECT b.mst_id, b.color_id, b.determination_id as deter_id, b.color_range_id, b.process_id_main
			FROM fabric_sales_order_dtls b WHERE b.status_active=1 and b.is_deleted=0 $fso_id_conds");
			$fso_data_arr=array();
			foreach($fso_sql_data as $val)
			{
				$fso_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]][$val[csf("deter_id")]]['shade_type'].=$val[csf("color_range_id")].',';
				$fso_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]][$val[csf("deter_id")]]['process_rute'].=$val[csf("process_id_main")].',';
			}
			// echo "<pre>";print_r($fso_data_arr);
			$tot_roll=$tot_grey_used=$tot_fin_qty=0;
			foreach ($data_grouping as $fso_idkey => $fso_idArr) 
			{
				foreach ($fso_idArr as $batch_idKey => $batch_idArr) 
				{
					foreach ($batch_idArr as $deter_id => $deter_idArr) 
					{
						foreach ($deter_idArr as $dia => $diaArr) 
						{
							foreach ($diaArr as $gsm => $row) 
							{
								$barcode_no_arr=array_unique(explode(',', chop($row['barcode_no'],',')));
								$grey_used=0;
								foreach ($barcode_no_arr as $key => $barcode) 
								{
									$grey_used+=$production_data_arr[$barcode]['prod_qty'];
								}

								if($row["within_group"] == 1)
								{
									$buyer_id = $row["po_buyer"];
								}else{
									$buyer_id = $row["buyer_id"];
								}
								$buyer_name = $buyer_arr[$buyer_id];

								$shade_type=$fso_data_arr[$fso_idkey][$row["color_id"]][$deter_id]['shade_type'];
								$shade_type_arr=array_unique(explode(',', chop($shade_type,',')));
								$shade_type_data ="";
							    foreach($shade_type_arr as $key => $shade_type_val)
							    {
							        if ($shade_type_data=="") 
							        {
							            $shade_type_data.= $color_range[$shade_type_val];
							        }
							        else 
							        {
							            $shade_type_data.= ','.$color_range[$shade_type_val];
							        }
							    }

								$process_rute=$fso_data_arr[$fso_idkey][$row["color_id"]][$deter_id]['process_rute'];
								$process_rute_arr=array_unique(explode(',', chop($process_rute,',')));
								$process_rute_data ="";
							    foreach($process_rute_arr as $key => $process_val)
							    {
							        if ($process_rute_data=="") 
							        {
							            $process_rute_data.= $conversion_cost_head_array[$process_val];
							        }
							        else 
							        {
							            $process_rute_data.= ','.$conversion_cost_head_array[$process_val];
							        }
							    }
								?>
				            	<tr>
				                    <td width="30"><? echo $i; ?></td>
				                    <td width="100" style="word-break:break-all;" align="center"><? echo $buyer_name; ?></td>
				                    <td width="120" align="center"><? echo $row["po_job_no"]; ?></td>
				                    <td width="60" style="word-break:break-all;" align="center" title="<?=$fso_idkey;?>"><? echo $row["job_no"]; ?></td>
				                    <td width="70" style="word-break:break-all;" align="center"><? echo $row["sales_booking_no"]; ?></td>
				                    <td width="70" style="word-break:break-all;" align="center"><? echo $row["style_ref_no"]; ?></td>
				                    <td width="70" style="word-break:break-all;" align="center"><? echo $row['batch_no']; ?></td>
				                    <td width="70" style="word-break:break-all;" align="center" title="<?=$row["color_id"];?>"><? echo $color_arr[$row["color_id"]]; ?></td>
				                    <td width="50" align="center" ><? echo $shade_type_data;  ?></td>
				                    <td width="40" align="center" title="<?=$deter_id;?>"><? echo $constructtion_arr[$deter_id].','.$composition_arr[$deter_id]; ?></td>
				                    <td width="70" align="center"><? echo $process_rute_data; ?></td>
				                    <td width="70" align="center"><? echo $dia; ?></td>
				                    <td width="60" style="word-break:break-all;" align="center"><? echo $gsm; ?></td>
				                    <td width="70" align="center"><? echo $unit_of_measurement[$row['uom']];?></td>
				                    <td width="100" align="center"><? echo $row['roll_count']; ?></td>
				            		<td width="120" align="right"><? echo number_format($grey_used,2); ?></td>
				            		<td width="120" align="right"><? echo number_format($row['fin_qnty'],2); ?></td>
				                </tr>
				            	<?
								$tot_roll+=$row['roll_count'];
								$tot_grey_used+=$grey_used;
								$tot_fin_qty+=$row['fin_qnty'];
								$i++;
							}
						}
					}
				}
			}
			?>
            <tr> 
                <td align="right" colspan="14"><strong>Total</strong></td>
                <td align="right"><strong><? echo $tot_roll; ?></strong></td>
                <td align="right"><strong><? echo number_format($tot_grey_used,2,'.',''); ?></strong></td>
                <td align="right"><strong><? echo number_format($tot_fin_qty,2,'.',''); ?></strong></td>
			</tr>
		</table>

		<table width="1250" cellspacing="0" align="center" border="0">
			<tr>
				<td width="">&nbsp;</td>
		    </tr>
			<tr style="line-height: 40px;">
				<td style="font-size:16px; font-weight:bold; border: 1px solid; border-right:none;" width="100">Remarks:</td>
                <td width="200" colspan="6" style="border: 1px solid;"><? echo $sql_data[0][csf('remarks')]; ?></td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>
			</tr>
		</table>
	</div>
    <? echo signature_table(205, $company, "1210px"); ?>
   	<script type="text/javascript" src="../../../js/jquery.js"></script>
   	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 40,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}

if($action=="finish_delivery_print3")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	$company=$data[0];
	$txt_challan_no=$data[1];
	$update_id=$data[2];
	$storeId=$data[4];
	$location=$data[5];

	$company_array=array();
	$company_data=sql_select("select id, company_name, company_short_name from lib_company");
	$country_arr=return_library_array( "SELECT id, country_name from  lib_country where status_active=1 and is_deleted=0", "id", "country_name"  );
	foreach($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname']=$row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name']=$row[csf('company_name')];
	}
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");

	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id order by b.id asc";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	?>
    <div style="width:1010px;">
    	<table width="1010" cellspacing="0" align="center" border="0">
			<tr>
			<?
				$data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$data[0]' and form_name='company_details' and is_deleted=0 and file_type=1");
			?>
			<td  align="left" colspan="2" rowspan="4">
				<?
				foreach($data_array as $img_row)
				{
					?>
					<img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='90' width='100' align="middle" />
					<?
				}
				?>
			</td>
			<td align="center" style="font-size:x-large"><strong><? echo $company_array[$company]['name']; ?></strong></td>
			</tr>
			<tr>
				<td align="center">
					<?
					$nameArray_com=sql_select( "SELECT PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, COUNTRY_ID, PROVINCE, CITY, ZIP_CODE, CONTACT_NO, EMAIL, WEBSITE, VAT_NUMBER FROM LIB_COMPANY WHERE ID='".$company."' AND STATUS_ACTIVE=1 AND IS_DELETED=0");
					$loc = '';
					foreach ($nameArray_com as $result)
					{
						if($result['PLOT_NO'] != '')
						{
							$loc .= $result['PLOT_NO'];
						}
						
						if($result['LEVEL_NO'] != '')
						{
							if($loc != '')
							{
								$loc .= ', '.$result['PLOT_NO'];
							}
							else
							{
								$loc .= $result['PLOT_NO'];
							}
						}
						
						if($result['ROAD_NO'] != '')
						{
							if($loc != '')
							{
								$loc .= ', '.$result['ROAD_NO'];
							}
							else
							{
								$loc .= $result['ROAD_NO'];
							}
						}
						
						if($result['BLOCK_NO'] != '')
						{
							if($loc != '')
							{
								$loc .= ', '.$result['BLOCK_NO'];
							}
							else
							{
								$loc .= $result['BLOCK_NO'];
							}
						}
						
						if($result['CITY'] != '')
						{
							if($loc != '')
							{
								$loc .= ', '.$result['CITY'];
							}
							else
							{
								$loc .= $result['CITY'];
							}
						}
					}
					echo $loc;
					?>
				</td>
			</tr>
			
			<tr>
				
				<td align="center" style="font-size:16px"><strong>Finish Fabric Roll Delivery To Garments</strong></td>
			</tr>
        </table> 
        <br>
        <?
            $sql_data= sql_select("SELECT a.challan_no, a.issue_number,a.company_id, a.knit_dye_source, a.knit_dye_company, a.issue_date,a.store_id,a.location_id, b.within_group, b.po_company_id, b.buyer_id, a.coure_tube, a.remarks, a.delivery_addr
            from  inv_issue_master a, fabric_sales_order_mst b
            where a.entry_form=318 and a.fso_id=b.id and a.company_id=$company and a.issue_number='$txt_challan_no'");
		?>
        
		<table width="1250" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="120">Delivery To</td>
                <td width="180">:&nbsp;<strong>
                <? 
                if($sql_data[0][csf('within_group')]==1) 
                {
                	$po_company_id=$sql_data[0][csf('po_company_id')];
                	echo $company_array[$po_company_id]['name'];               	
                }
				else  
				{
					$fso_buyer_id=$sql_data[0][csf('buyer_id')];
					echo $buyer_arr[$fso_buyer_id];
				}
                ?></strong>
                </td>
                <td style="font-size:16px; font-weight:bold;" width="300">Delivery Challan No.</td>
                <td width="250" align="left">:&nbsp;<strong><? echo $sql_data[0][csf('issue_number')]; ?></strong></td>
                
                <td width="190"></td>
                <td width="200">&nbsp;</td>                
                <td width="200">&nbsp;</td>

                <td width="390" id="barcode_img_id"  colspan=""></td>	
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="100">Address</td>
                <td width="800">:&nbsp;
                <? 
                echo $sql_data[0][csf('delivery_addr')];
                ?>
                </td>
                
                
                <td style="font-size:16px; font-weight:bold;" width="190">Delivery Date</td>
                <td width="200" align="left">:&nbsp;<? echo change_date_format($sql_data[0][csf('issue_date')]); ?></td>

                <td width="200">&nbsp;</td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>
			</tr>

		</table>
        <br>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1250" class="rpt_table" >
            <thead>
                <tr>
					<th width="30">SL</th>
					<th width="100">Customer</th>
					<th width="120">Cust. Buyer</th>
					<th width="100">FSO No</th>
					<th width="100">Booking No</th>
					<th width="100">Style</th>
					<th width="70">Batch No</th>
					<th width="70">Color</th>
					<th width="50">Shade Type</th>
					<th width="150">Fabric Type</th>
					<th width="100">Process Rute</th>
					<th width="70">Dia</th>
					<th width="60">Gsm</th>
					<th width="70">UOM</th>
					<th width="80">Roll</th>
					<th width="90">Grey Used</th>
					<th width="90">F.QTY (KG)</th>        
                </tr>
            </thead>
            <?
			$i=1;
			$sql_update=sql_select("SELECT a.id as roll_table_id, a.barcode_no, a.dtls_id, b.trans_id, a.roll_id,a.roll_no, b.batch_id, e.id, e.batch_no, a.po_breakdown_id, a.booking_without_order, a.is_sales, a.reprocess, a.prev_reprocess, b.body_part_id, b.trans_id, b.prod_id, d.gsm, d.dia_width as dia, d.detarmination_id as deter_id, d.color as color_id, a.qnty ,a.reject_qnty, b.width_type,  c.job_no, c.id as fso_id, c.sales_booking_no, c.booking_id, c.buyer_id, c.po_buyer, c.po_job_no, c.po_company_id, c.within_group, c.style_ref_no, d.unit_of_measure as uom, null as recv_number, c.customer_buyer 
			from pro_roll_details a, inv_finish_fabric_issue_dtls b, fabric_sales_order_mst c , product_details_master d, pro_batch_create_mst e
			where a.dtls_id=b.id and b.batch_id=e.id and a.po_breakdown_id=c.id and b.prod_id=d.id and a.entry_form=318 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.mst_id=$update_id and a.is_returned!=1");

			$barcode_NOs="";
			foreach($sql_update as $row)
			{
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['within_group']=$row[csf("within_group")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['po_buyer']=$row[csf("po_buyer")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['buyer_id']=$row[csf("buyer_id")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['customer_buyer']=$row[csf("customer_buyer")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['po_job_no']=$row[csf("po_job_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['job_no']=$row[csf("job_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['sales_booking_no']=$row[csf("sales_booking_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['style_ref_no']=$row[csf("style_ref_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['batch_no']=$row[csf("batch_no")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['color_id']=$row[csf("color_id")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['uom']=$row[csf("uom")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['fin_qnty']+=$row[csf("qnty")];
				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['roll_count']++;

				$data_grouping[$row[csf("fso_id")]][$row[csf("id")]][$row[csf("deter_id")]][$row[csf("dia")]][$row[csf("gsm")]]['barcode_no'].=$row[csf("barcode_no")].',';
				$barcode_NOs.=$row[csf("barcode_no")].", ";
				$fso_ids.=$row[csf("fso_id")].",";
			}
			// echo "<pre>";print_r($data_grouping);

			$barcode_Nos_all=rtrim($barcode_NOs,", "); 
			$barcode_Nos_alls=explode(", ",$barcode_Nos_all);
			$barcode_Nos_alls=array_chunk($barcode_Nos_alls,999);
			$barcode_no_conds=" and";
			foreach($barcode_Nos_alls as $dtls_id)
			{
				if($barcode_no_conds==" and")  $barcode_no_conds.="(a.barcode_no in(".implode(',',$dtls_id).")"; else $barcode_no_conds.=" or a.barcode_no in(".implode(',',$dtls_id).")";
			}
			$barcode_no_conds.=")";

			$production_sql_data=sql_select("SELECT a.barcode_no, a.id as roll_id, a.roll_no, a.po_breakdown_id, a.qnty as prod_qty, a.qc_pass_qnty, a.reject_qnty 
			FROM pro_roll_details a 
			WHERE a.entry_form=66 and a.status_active=1 and a.is_deleted=0 $barcode_no_conds");
			$production_data_arr=array();
			foreach($production_sql_data as $value)
			{
				$production_data_arr[$value[csf("barcode_no")]]['prod_qty']=$value[csf("prod_qty")];	
				$production_data_arr[$value[csf("barcode_no")]]['qc_pass_qnty']=$value[csf("qc_pass_qnty")];	
				$production_data_arr[$value[csf("barcode_no")]]['reject_qnty']=$value[csf("reject_qnty")];
			}
			// echo "<pre>";print_r($production_data_arr);

			$fso_ids_all=implode(",", array_unique(explode(",",rtrim($fso_ids,","))));
			$fso_ids_alls=explode(",",$fso_ids_all);
			$fso_ids_alls=array_chunk($fso_ids_alls,999); 
			$fso_id_conds=" and";
			foreach($fso_ids_alls as $fso_id)
			{
				if($fso_id_conds==" and")  $fso_id_conds.="(b.mst_id in(".implode(',',$fso_id).")"; else $fso_id_conds.=" or b.mst_id in(".implode(',',$fso_id).")";
			}
			$fso_id_conds.=")";
			// echo $fso_id_conds;
			$fso_sql_data=sql_select("SELECT b.mst_id, b.color_id, b.determination_id as deter_id, b.color_range_id, b.process_id_main
			FROM fabric_sales_order_dtls b WHERE b.status_active=1 and b.is_deleted=0 $fso_id_conds");
			$fso_data_arr=array();
			foreach($fso_sql_data as $val)
			{
				$fso_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]][$val[csf("deter_id")]]['shade_type'].=$val[csf("color_range_id")].',';
				$fso_data_arr[$val[csf("mst_id")]][$val[csf("color_id")]][$val[csf("deter_id")]]['process_rute'].=$val[csf("process_id_main")].',';
			}
			// echo "<pre>";print_r($fso_data_arr);
			$tot_roll=$tot_grey_used=$tot_fin_qty=0;
			foreach ($data_grouping as $fso_idkey => $fso_idArr) 
			{
				foreach ($fso_idArr as $batch_idKey => $batch_idArr) 
				{
					foreach ($batch_idArr as $deter_id => $deter_idArr) 
					{
						foreach ($deter_idArr as $dia => $diaArr) 
						{
							foreach ($diaArr as $gsm => $row) 
							{
								$barcode_no_arr=array_unique(explode(',', chop($row['barcode_no'],',')));
								$grey_used=0;
								foreach ($barcode_no_arr as $key => $barcode) 
								{
									$grey_used+=$production_data_arr[$barcode]['prod_qty'];
								}

								if($row["within_group"] == 1)
								{
									$buyer_id = $row["po_buyer"];
								}else{
									$buyer_id = $row["buyer_id"];
								}
								$buyer_name = $buyer_arr[$buyer_id];

								$shade_type=$fso_data_arr[$fso_idkey][$row["color_id"]][$deter_id]['shade_type'];
								$shade_type_arr=array_unique(explode(',', chop($shade_type,',')));
								$shade_type_data ="";
							    foreach($shade_type_arr as $key => $shade_type_val)
							    {
							        if ($shade_type_data=="") 
							        {
							            $shade_type_data.= $color_range[$shade_type_val];
							        }
							        else 
							        {
							            $shade_type_data.= ','.$color_range[$shade_type_val];
							        }
							    }

								$process_rute=$fso_data_arr[$fso_idkey][$row["color_id"]][$deter_id]['process_rute'];
								$process_rute_arr=array_unique(explode(',', chop($process_rute,',')));
								$process_rute_data ="";
							    foreach($process_rute_arr as $key => $process_val)
							    {
							        if ($process_rute_data=="") 
							        {
							            $process_rute_data.= $conversion_cost_head_array[$process_val];
							        }
							        else 
							        {
							            $process_rute_data.= ','.$conversion_cost_head_array[$process_val];
							        }
							    }
								?>
				            	<tr>
				                    <td width="30"><? echo $i; ?></td>
				                    <td width="100" style="word-break:break-all;" align="center"><? echo $buyer_name; ?></td>
				                    <td width="120" align="center"><? echo $buyer_arr[$row["customer_buyer"]]; ?></td>
				                    <td width="100" style="word-break:break-all;" align="center" title="<?=$fso_idkey;?>"><? echo $row["job_no"]; ?></td>
				                    <td width="100" style="word-break:break-all;" align="center"><? echo $row["sales_booking_no"]; ?></td>
				                    <td width="100" style="word-break:break-all;" align="center"><? echo $row["style_ref_no"]; ?></td>
				                    <td width="70" style="word-break:break-all;" align="center"><? echo $row['batch_no']; ?></td>
				                    <td width="70" style="word-break:break-all;" align="center" title="<?=$row["color_id"];?>"><? echo $color_arr[$row["color_id"]]; ?></td>
				                    <td width="50" align="center" ><? echo $shade_type_data;  ?></td>
				                    <td width="40" align="center" title="<?=$deter_id;?>"><? echo $constructtion_arr[$deter_id].','.$composition_arr[$deter_id]; ?></td>
				                    <td width="100" align="center"><? echo $process_rute_data; ?></td>
				                    <td width="70" align="center"><? echo $dia; ?></td>
				                    <td width="60" style="word-break:break-all;" align="center"><? echo $gsm; ?></td>
				                    <td width="70" align="center"><? echo $unit_of_measurement[$row['uom']];?></td>
				                    <td width="80" align="center"><? echo $row['roll_count']; ?></td>
				            		<td width="90" align="right"><? echo number_format($grey_used,2); ?></td>
				            		<td width="90" align="right"><? echo number_format($row['fin_qnty'],2); ?></td>
				                </tr>

				            	<?
								$tot_roll+=$row['roll_count'];
								$tot_grey_used+=$grey_used;
								$tot_fin_qty+=$row['fin_qnty'];
								$i++;
							}
						}
					}
				}
			}
			?>
            <tr> 
                <td align="left" colspan="12" style="word-wrap: break-word;"><strong>Barcode No - </strong><? echo chop($barcode_NOs,", "); ?></td>
                <td align="right" colspan="2"><strong>Total</strong></td>
                <td align="right"><strong><? echo $tot_roll; ?></strong></td>
                <td align="right"><strong><? echo number_format($tot_grey_used,2,'.',''); ?></strong></td>
                <td align="right"><strong><? echo number_format($tot_fin_qty,2,'.',''); ?></strong></td>
			</tr>
		</table>

		<table width="1250" cellspacing="0" align="center" border="0">
			<tr>
				<td width="">&nbsp;</td>
		    </tr>
			<tr style="line-height: 40px;">
				<td style="font-size:16px; font-weight:bold; border: 1px solid; border-right:none;" width="100">Remarks:</td>
                <td width="200" colspan="6" style="border: 1px solid;"><? echo $sql_data[0][csf('remarks')]; ?></td>

                <td width="190"></td>
                <td width="200">&nbsp;</td>
			</tr>
		</table>
	</div>
    <? echo signature_table(205, $company, "1210px"); ?>
   	<script type="text/javascript" src="../../../js/jquery.js"></script>
   	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode( valuess )
		{
			var value = valuess;//$("#barcodeValue").val();
		  //alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer ='bmp';// $("input[name=renderer]:checked").val();
			 
			var settings = {
			  output:renderer,
			  bgColor: '#FFFFFF',
			  color: '#000000',
			  barWidth: 1,
			  barHeight: 40,
			  moduleSize:5,
			  posX: 10,
			  posY: 20,
			  addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			 value = {code:value, rect: false};
			
			$("#barcode_img_id").show().barcode(value, btype, settings);
		} 
		generateBarcode('<? echo $txt_challan_no; ?>');
	</script>
	<?
	exit();
}
?>
