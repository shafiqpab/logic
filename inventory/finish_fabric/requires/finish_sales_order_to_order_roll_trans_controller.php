<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
$user_level=$_SESSION['logic_erp']['user_level'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
	$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if($action=="load_drop_store_from")
{
	$data= explode("_", $data);
	$category_id = 2;
	echo create_drop_down( "cbo_from_store_name", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data[1] and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "fnc_onchang_reset(this.value);" );
}

if($action=="load_drop_store_to")
{
	$data= explode("_", $data);
	$category_id = 2;
	echo create_drop_down( "cbo_store_name", 160, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id=$data[1] and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "fn_load_floor(this.value);reset_room_rack_shelf('','cbo_store_name');" );
}

if($action=="load_drop_from_store_balnk")
{
	echo create_drop_down( "cbo_from_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "" );
}

if($action=="load_drop_store_balnk")
{
	echo create_drop_down( "cbo_store_name", 160, $blank_array,"", 1, "--Select store--", 0, "" );
}

if($action=="floor_list")
{
	$data_ref=explode("__",$data);
	$floor_arr=array();
	$floor_data=sql_select("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($floor_data as $row)
	{
		$floor_arr[$row[csf('floor_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsFloor_arr= json_encode($floor_arr);
	echo $jsFloor_arr;
	die();
}

if($action=="room_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr=array();
	$room_data=sql_select("select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and b.floor_id in($data_ref[2]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($room_data as $row)
	{
		$room_arr[$row[csf('room_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRoom_arr= json_encode($room_arr);
	echo $jsRoom_arr;
	die();
}

if($action=="rack_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$rack_arr=array();
	$rack_data=sql_select("select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and b.room_id in($data_ref[2]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($rack_data as $row)
	{
		$rack_arr[$row[csf('rack_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRack_arr= json_encode($rack_arr);
	echo $jsRack_arr;
	die();
}

if($action=="shelf_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$shelf_arr=array();
	$shelf_data=sql_select("select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and b.rack_id in($data_ref[2]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($shelf_data as $row)
	{
		$shelf_arr[$row[csf('shelf_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsShelf_arr= json_encode($shelf_arr);
	echo $jsShelf_arr;
	die();
}

if($action=="bin_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$bin_arr=array();
	$bin_data=sql_select("select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and b.shelf_id in($data_ref[2]) and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($bin_data as $row)
	{
		$bin_arr[$row[csf('bin_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsBin_arr= json_encode($bin_arr);
	echo $jsBin_arr;
	die();
}

if($action=="bodypart_list")
{
	$data_ref=explode("__",$data);
	
	$bodyPart_arr=array();
	
		// echo "SELECT b.id, b.body_part_id from fabric_sales_order_mst a, fabric_sales_order_dtls b  where a.id=b.mst_id and a.company_id= '$data_ref[0]' and b.mst_id='$data_ref[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.body_part_id order by b.id";
		$body_part_sql = sql_select("SELECT b.id, b.body_part_id from fabric_sales_order_mst a, fabric_sales_order_dtls b  where a.id=b.mst_id and a.company_id= '$data_ref[0]' and b.mst_id='$data_ref[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.body_part_id order by b.id");
		

		foreach($body_part_sql as $row)
		{
			$bodyPart_arr[$row[csf('body_part_id')]]=$body_part[$row[csf('body_part_id')]];
		}
	
	$jsBodyPart_arr= json_encode($bodyPart_arr);
	echo $jsBodyPart_arr;
	die();
}

if ($action == "load_drop_down_buyer") 
{
	$data = explode("_", $data);
	$with_in_group=$data[0];
	$company_id = $data[1];

	if ($company_id == 0)
	{
		echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
	} 
	else 
	{
		if ($with_in_group== 1) 
		{
			echo create_drop_down("cbo_buyer_name", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "--Select Buyer--", "0", "", "");
		} 
		else if ($with_in_group== 2) 
		{
			echo create_drop_down("cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "--Select Buyer--", $selected, "", 0);
		}
		else
		{
			echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, "");
		}
	}
	
	exit();
}

if ($action=="requ_variable_settings")
{
	extract($_REQUEST);
	$requisition_type="";

	$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name='$cbo_company_id' and item_category_id=13 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo $requisition_type.'**'.$variable_inventory;
	exit();
}

if($action=="barcode_popup") //  Roll Scan/Browse Pupup
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value( str)
		{
			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#hidden_barcode_nos').val( id );
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
	<div align="center" style="width:800px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:800px; margin-left:2px;">
			<legend>Enter search words</legend>
	            <table cellpadding="0" cellspacing="0" width="830" border="1" rules="all" class="rpt_table">
	                <thead>
						<th>Within Group</th>
						<th>Buyer Name</th>
						<th>IR/IB</th>
						<th>Sales Order No</th>
						<th>Sales/Booking No</th>
						<th>Batch No</th>
						<th width="170">Delivery Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
						</th>
					</thead>
	                <tr class="general">
						<td>
							<?
							echo create_drop_down("cbo_within_group", 70, $yes_no, "", 1, "--Select--", 0, "load_drop_down( 'finish_sales_order_to_order_roll_trans_controller', this.value+'_'+".$company_id.", 'load_drop_down_buyer', 'buyer_td' );");
							?>
						</td>
						<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, ""); ?></td>
						<td>
							<input type="text" style="width:80px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Int Ref." />
						</td>
						<td>
							<input type="text" style="width:80px;" class="text_boxes" name="txt_order_no" id="txt_order_no" placeholder="Enter Order No" />
						</td>
						<td>
							<input type="text" style="width:80px;" class="text_boxes" name="txt_sales_booking_no" id="txt_sales_booking_no" placeholder="Enter Sales/Booking No" />
						</td>
						<td>
							<input type="text" style="width:80px;" class="text_boxes" name="txt_batch_no" id="txt_batch_no" placeholder="Batch No" />
						</td>
						<td>
							<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
							<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly>
						</td>
						<td>
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_within_group').value+'_'+document.getElementById('txt_sales_booking_no').value+'_'+<? echo $cbo_store_name; ?>+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_batch_no').value, 'create_barcode_search_list_view', 'search_div', 'finish_sales_order_to_order_roll_trans_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
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
}

if($action=="create_barcode_search_list_view") //  Roll Scan/Browse Pupup List view
{
	$data=explode('_',$data);
	// print_r($data);die;
	$company_id=$data[2];
	$with_in_group=$data[5];
	$sales_booking_no=$data[6];
	$store_id=$data[7];
	$transfer_cateria=$data[8];
	$int_ref=$data[9];
	$batch_no=$data[10];

	if ($int_ref!="") 
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b 
		where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0";
		// echo $po_sql;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond="";
		foreach ($po_sql_result as $key => $row) 
		{
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond=" and e.sales_booking_no in('".implode("','",$bookingNo_arr)."') ";
		//echo $refBooking_cond;die;
	}

	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$delivery_date_cond = "and e.delivery_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$delivery_date_cond = "and e.delivery_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$delivery_date_cond ="";
	
	$arr=array(2=>$company_arr);//2=>$company_arr,
	if($data[0]==0) $buyer_cond=""; else $buyer_cond="and e.buyer_id='$data[0]'";
	if($data[1]!="") $po_cond="and e.job_no_prefix_num='$data[1]'"; else $po_cond="";
	if($with_in_group!=0) $within_group_cond="and e.within_group='$with_in_group'"; else $within_group_cond="";
	if($sales_booking_no!="") $sales_booking_no_cond="and e.sales_booking_no='$sales_booking_no'"; else $sales_booking_no_cond="";
	
	if($batch_no!="") $batch_no_cond="and d.batch_no='$batch_no'"; else $batch_no_cond="";

	if($store_id>0) 
	{
		$store_cond=" and a.store_id=$store_id";
		$store_cond2=" and b.to_store=$store_id";
	} 
	else 
	{
		$store_cond="";
		$store_cond2="";
	}
	// =======================================
	$fso_sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.fabric_description_id, b.gsm, b.width, b.color_id as color_names, b.body_part_id, b.floor, b.room, b.rack_no, b.shelf_no, b.bin, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, c.rate,c.amount, 1 as type,c.booking_no,a.store_id, e.id, e.job_no, e.job_no_prefix_num, to_char(e.insert_date,'YYYY') as year, e.delivery_date, e.style_ref_no, e.buyer_id, e.booking_id, e.sales_booking_no,e.po_buyer, e.within_group 
	FROM inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, fabric_sales_order_mst e, pro_batch_create_mst d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id  and b.batch_id = d.id and c.po_breakdown_id=e.id and b.trans_id<>0 and a.entry_form in(317) and c.entry_form in(317) and c.is_transfer!=6 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_service=0 and a.company_id=$company_id $buyer_cond $po_cond $delivery_date_cond $within_group_cond $sales_booking_no_cond $store_cond $refBooking_cond $batch_no_cond
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, d.detarmination_id as fabric_description_id, d.gsm, d.dia_width as width, b.color_id as color_names, b.to_body_part as body_part_id, b.to_floor_id as floor, b.to_room as room, b.to_rack as rack_no, b.to_shelf as shelf_no, b.to_bin_box as bin, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, c.rate,c.amount, 2 as type, c.booking_no, b.to_store as store_id, e.id, e.job_no, e.job_no_prefix_num, to_char(e.insert_date,'YYYY') as year, e.delivery_date, e.style_ref_no, e.buyer_id, e.booking_id, e.sales_booking_no,e.po_buyer, e.within_group
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, fabric_sales_order_mst e, product_details_master d
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.from_prod_id=d.id and c.po_breakdown_id=e.id and a.entry_form in(628) and c.entry_form in(628) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_service=0 and a.company_id=$company_id $buyer_cond $po_cond $delivery_date_cond $within_group_cond $sales_booking_no_cond $store_cond2 $refBooking_cond order by barcode_no";
	// echo $fso_sql;die;
	$result=sql_select($fso_sql);	
	// echo "<pre>";print_r($result);
	foreach ($result as $row) 
	{
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
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
		$barcodeData=sql_select( "select a.barcode_no from pro_roll_details a where a.entry_form=318 and a.status_active=1 and a.is_deleted=0 $all_barcode_cond");
		foreach ($barcodeData as $row)
		{
			$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		}
	}
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table">
        <thead>
            <th width="30">SL</th>
			<th width="50">Sales Order No</th>
			<th width="50">Year</th>
			<th width="60">With in Group</th>
			<th width="110">Sales Order Buyer</th>
			<th width="110">Sales/Booking No</th>
			<th width="100">PO Buyer</th>
			<th width="100">Style Ref.</th>
			<th width="90">Barcode No</th>
			<th>Delivery Date</th>
        </thead>
	</table>
	<div style="width:800px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
        <?
        $i=1;
		foreach ($result as $row)
        {
        	if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
        	{
				$trans_flag = "";
				if($row[csf('entry_form')] == 628)//Finish Transfer
				{
					$trans_flag = " (T)";
				}

				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)">
					<td width="30" align="center"><? echo $i; ?>
						<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						<input type="hidden" name="txt_individual_qty" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
					</td>
					<td width="50"><p><? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="50"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="60"><p><? echo $yes_no[$row[csf('within_group')]]; ?></p></td>
					<td width="110"><p><? echo $buyer_name; ?></p></td>
					<td width="110"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="100"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?></p></td>
					<td width="100"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td width="90"><p><? echo $row[csf('barcode_no')].$trans_flag; ?>&nbsp;</p></td>
					<td><? echo change_date_format($row[csf('delivery_date')]); ?></td>
				</tr>
				<?
				$i++;
				$total_grey_qnty +=$row[csf('qnty')];					
        	}	
		}
    	?>
        </table>
    </div>
    <table width="600">
        <tr>
        	<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/> Check all
			</td>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?
	exit();
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
		<div align="center" style="width:750px;">
			<form name="searchdescfrm"  id="searchdescfrm">
				<fieldset style="width:750px;">
					<table cellpadding="0" cellspacing="0" width="750" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Within Group</th>
							<th>Buyer Name</th>
							<th>IR/IB</th>
							<th>Sales Order No</th>
							<th>Sales/Booking No</th>
							<th width="170">Delivery Date Range</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:80px;" class="formbutton" />
								<input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td>
								<?
								echo create_drop_down("cbo_within_group", 70, $yes_no, "", 1, "--Select--", 0, "load_drop_down( 'finish_sales_order_to_order_roll_trans_controller', this.value+'_'+".$cbo_company_id.", 'load_drop_down_buyer', 'buyer_td' );");
								?>
							</td>
							<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 150, $blank_array, "", 1, "--Select Buyer--", 0, ""); ?></td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Int Ref." />
							</td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_order_no" id="txt_order_no" placeholder="Enter Order No" />
							</td>
							<td>
								<input type="text" style="width:80px;" class="text_boxes" name="txt_sales_booking_no" id="txt_sales_booking_no" placeholder="Enter Sales/Booking No" />
							</td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly>
							</td>
							<td>
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('cbo_within_group').value+'_'+'<? echo $txt_from_order_id; ?>'+'_'+document.getElementById('txt_sales_booking_no').value+'_'+document.getElementById('txt_ref_no').value, 'create_po_search_list_view', 'search_div', 'finish_sales_order_to_order_roll_trans_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:80px;" />
							</td>
						</tr>
						<tr>
							<td colspan="7" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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
	// print_r($data);
	$company_id=$data[2];
	$fromOrderId=$data[7];
	$sales_booking_no=$data[8];
	$int_ref=$data[9];

	//$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	//$sales_buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	//$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if ($int_ref!="") 
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b 
		where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and a.grouping='$int_ref' and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0";
		// echo $po_sql;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond="";
		foreach ($po_sql_result as $key => $row) 
		{
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
			$bookingNo_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}
		//$poIds=implode(",",array_unique(explode(",",$po_id_arr)));
		$refBooking_cond=" and a.sales_booking_no in('".implode("','",$bookingNo_arr)."') ";
		//echo $refBooking_cond;die;
	}
	
	if ($data[3]!="" &&  $data[4]!="") 
	{
		if($db_type==0)
		{
			$delivery_date_cond = "and a.delivery_date between '".change_date_format($data[3], "yyyy-mm-dd", "-")."' and '".change_date_format($data[4], "yyyy-mm-dd", "-")."'";
		}
		else
		{
			$delivery_date_cond = "and a.delivery_date between '".change_date_format($data[3],'','',1)."' and '".change_date_format($data[4],'','',1)."'";
		}
	}
	else 
		$delivery_date_cond ="";
	
	$type=$data[5];
	$arr=array(2=>$company_arr);//2=>$company_arr,
	
	$with_in_group=$data[6];
	if($type=="to") { $orderIdOmitCond = "and a.id not in($fromOrderId)";}
	if($data[0]==0) $buyer_cond=""; else $buyer_cond="and a.buyer_id='$data[0]'";
	if($data[1]!="") $po_cond="and a.job_no_prefix_num='$data[1]'"; else $po_cond="";
	if($with_in_group!=0) $within_group_cond="and a.within_group='$with_in_group'"; else $within_group_cond="";
	
	if($type=="from") $status_cond=" and b.status_active in(1,3)"; else $status_cond=" and b.status_active=1";
	if($sales_booking_no!="") $sales_booking_no_cond="and a.sales_booking_no='$sales_booking_no'"; else $sales_booking_no_cond="";
	?>
	<div style="width:100%;">
		<table cellspacing="0" border="1" cellpadding="0" rules="all" width="780" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="50">Sales Order No</th>
				<th width="50">Year</th>
				<th width="60">With in Group</th>
				<th width="110">Sales Order Buyer</th>
				<th width="110">Sales/Booking No</th>
				<th width="100">PO Buyer</th>
				<th width="100">Style Ref.</th>
				<th width="70">PO Qty</th>
				<th>Delivery Date</th>
			</thead>
		</table>
	</div>
	<div style="width:780;max-height:180px; overflow-y:scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="tbl_list_search">
			<?
			$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, to_char(a.insert_date,'YYYY') as year, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group, b.grey_qty as order_qty, b.determination_id, b.gsm_weight, b.dia
			from fabric_sales_order_mst a, fabric_sales_order_dtls b, pro_batch_create_mst c   
			where a.id=b.mst_id and a.id=c.sales_order_id and b.mst_id=c.sales_order_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_cond $po_cond $delivery_date_cond $orderIdOmitCond $within_group_cond $sales_booking_no_cond 
			$refBooking_cond order by a.id DESC ";
			/*$sql= "SELECT a.id, a.job_no, a.job_no_prefix_num, to_char(a.insert_date,'YYYY') as year, a.delivery_date, a.style_ref_no, a.buyer_id, a.booking_id, a.sales_booking_no,a.po_buyer, a.within_group, b.grey_qty as order_qty, b.determination_id, b.gsm_weight, b.dia
			from fabric_sales_order_mst a, fabric_sales_order_dtls b 
			where a.id=b.mst_id and a.company_id=$company_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $buyer_cond $po_cond $delivery_date_cond $orderIdOmitCond $within_group_cond $sales_booking_no_cond 
			$refBooking_cond order by a.id DESC ";*/
			
			//echo  $sql; //die;
			$sql_result=sql_select($sql);
			foreach($sql_result as $row)
			{
				$data_array[$row[csf('job_no')]]['fso_id']=$row[csf('id')];
				$data_array[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$data_array[$row[csf('job_no')]]['year']=$row[csf('year')];
				$data_array[$row[csf('job_no')]]['delivery_date']=$row[csf('delivery_date')];
				$data_array[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
				$data_array[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
				$data_array[$row[csf('job_no')]]['booking_id']=$row[csf('booking_id')];
				$data_array[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
				$data_array[$row[csf('job_no')]]['po_buyer']=$row[csf('po_buyer')];
				$data_array[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
				$data_array[$row[csf('job_no')]]['order_qty']+=$row[csf('order_qty')];
				$data_array[$row[csf('job_no')]]['desc'].=$row[csf('determination_id')].'*'.$row[csf('gsm_weight')].'*'.$row[csf('dia')].',';
			}
			// echo "<pre>"; print_r($data_array);
        	
			$i=1; 
			foreach($data_array as $row)
			{
				$desc=implode(",", array_unique(explode(",", chop($row['desc'],","))));
				// echo $desc.'<br>';
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				if($row['within_group']==1) $buyer_name=$company_arr[$row['buyer_id']];
				else if($row['within_group']==2) $buyer_name=$buyer_arr[$row['buyer_id']];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value('<? echo $row['fso_id']; ?>_<? echo $desc; ?>');" > 
					<td width="30"><? echo $i; ?></td>
					<td width="50"><? echo $row['job_no_prefix_num']; ?></td>
					<td width="50"><? echo $row['year']; ?></td>
					<td width="60"><? echo $yes_no[$row['within_group']]; ?></td>
					<td width="110"><? echo $buyer_name; ?></td>
					<td width="110"><? echo $row['sales_booking_no']; ?></td>
					<td width="100"><? echo $buyer_arr[$row['po_buyer']]; ?></td>
					<td width="100"><? echo $row['style_ref_no']; ?></td>
					<td width="70" align="right"><? echo number_format($row['order_qty'],2); ?></td>
					<td><? echo change_date_format($row['delivery_date']); ?></td>
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

if($action=='populate_data_from_order')
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];
	//$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	//$po_comp_arr=return_library_array( "select id, company_id from wo_booking_mst",'id','company_id');
	//$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	
	/*$data_array= sql_select("SELECT a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.within_group, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.customer_buyer, a.booking_id, listagg(b.item_number_id,',') within group (order by b.item_number_id) as item_number_id
	from fabric_sales_order_mst a, fabric_sales_order_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.id='$po_id' 
	group by a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.within_group, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.customer_buyer, a.booking_id");*/
	if ($which_order=='from') 
	{
		$sql= "SELECT a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.within_group, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.customer_buyer, a.booking_id, b.item_number_id, b.determination_id, b.gsm_weight, b.dia, c.batch_no, c.id as batch_id
		from fabric_sales_order_mst a, fabric_sales_order_dtls b, pro_batch_create_mst c  
		where a.id=b.mst_id and a.id=c.sales_order_id and b.mst_id=c.sales_order_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.id='$po_id'";
	}
	else
	{
		$sql= "SELECT a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.within_group, a.sales_booking_no,a.po_company_id,a.po_buyer, a.buyer_id, a.customer_buyer, a.booking_id, b.item_number_id, b.determination_id, b.gsm_weight, b.dia
		from fabric_sales_order_mst a, fabric_sales_order_dtls b, pro_batch_create_mst c  
		where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id='$po_id'";
	}
	
			
	// echo  $sql; die;
	$sql_result=sql_select($sql);
	foreach($sql_result as $row)
	{
		$data_array[$row[csf('job_no')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
		$data_array[$row[csf('job_no')]]['job_no']=$row[csf('job_no')];
		$data_array[$row[csf('job_no')]]['company_id']=$row[csf('company_id')];
		$data_array[$row[csf('job_no')]]['style_ref_no']=$row[csf('style_ref_no')];
		$data_array[$row[csf('job_no')]]['within_group']=$row[csf('within_group')];
		$data_array[$row[csf('job_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
		$data_array[$row[csf('job_no')]]['po_company_id']=$row[csf('po_company_id')];
		$data_array[$row[csf('job_no')]]['po_buyer']=$row[csf('po_buyer')];
		$data_array[$row[csf('job_no')]]['buyer_id']=$row[csf('buyer_id')];
		$data_array[$row[csf('job_no')]]['customer_buyer']=$row[csf('customer_buyer')];
		$data_array[$row[csf('job_no')]]['booking_id']=$row[csf('booking_id')];
		$data_array[$row[csf('job_no')]]['item_number_id']=$row[csf('item_number_id')];
		$data_array[$row[csf('job_no')]]['batch_no']=$row[csf('batch_no')];
		$data_array[$row[csf('job_no')]]['batch_id']=$row[csf('batch_id')];

		if ($user_level==2) // User managment > User Level = Admin User
		{
			$data_array[$row[csf('job_no')]]['desc'].=$row[csf('determination_id')].',';
		}
		else
		{
			$data_array[$row[csf('job_no')]]['desc'].=$row[csf('determination_id')].'*'.$row[csf('gsm_weight')].'*'.$row[csf('dia')].',';
		}
	}
	// echo "<pre>"; print_r($data_array);die;
	
	
	foreach ($data_array as $row)
	{ 
		$desc=implode(",", array_unique(explode(",", chop($row['desc'],","))));
		$gmts_item_id=array_unique(explode(",",$row['item_number_id']));
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}

		if ($row["within_group"]==1) 
		{
			$buyer=$row["po_buyer"];
		}
		else
		{
			$buyer=$row["buyer_id"];
		}

		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row["job_no"]."';\n";
		/*if ($which_order=='from') 
		{
			echo "document.getElementById('txt_from_batch_no').value 			= '".$row["batch_no"]."';\n";
			echo "document.getElementById('txt_from_batch_id').value 			= '".$row["batch_id"]."';\n";
		}*/

		echo "document.getElementById('txt_".$which_order."_booking_no').value 			= '".$row["sales_booking_no"]."';\n";
		echo "document.getElementById('cbo_".$which_order."_company').value 			= '".$row["po_company_id"]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$buyer."';\n";
		echo "document.getElementById('cbo_".$which_order."_cust_buyer_name').value 	= '".$row["customer_buyer"]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row["style_ref_no"]."';\n";
		//echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		//echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("delivery_date")])."';\n";
		// echo $which_order;

		if($which_order == 'to')
		{
			echo "document.getElementById('desc_str').value 			= '".$desc."';\n";
			echo "load_bodypart_list();\n";
		}
		
		exit();
	}
}

if($action=="show_dtls_list_view")
{	
	$con = connect();
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=628 and type=1");
	oci_commit($con);
	$barcode_arr=explode(",", $data);
	// echo "<pre>"; print_r($barcode_arr);die;
	foreach ($barcode_arr as $barcode) 
	{
		execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 628, 1, ".$barcode.")");
	}
	oci_commit($con);

	$sql="SELECT a.id, a.entry_form, b.batch_id, b.prod_id, b.body_part_id, d.batch_no, c.po_breakdown_id as fso_id, c.qnty, c.qc_pass_qnty, c.reject_qnty, c.barcode_no, c.roll_no, c.roll_id as roll_id_prev, c.id as roll_id, c.rate,c.amount, c.reprocess,c.prev_reprocess, b.floor, b.room, b.rack_no, b.shelf_no, b.bin, a.store_id, b.fabric_description_id, b.gsm, b.width, b.color_id, b.dia_width_type
	from tmp_barcode_no t, inv_receive_master a, pro_finish_fabric_rcv_dtls b, pro_roll_details c, pro_batch_create_mst d, fabric_sales_order_mst e 
	where t.barcode_no=c.barcode_no and t.userid=$user_id and a.id = b.mst_id and b.barcode_no = c.barcode_no  and c.entry_form=317 and c.is_sales= 1 and a.entry_form=317 and b.batch_id = d.id and c.po_breakdown_id = e.id and a.status_active = 1 and b.status_active = 1 and c.status_active = 1 and c.re_transfer=0
	union all
	select a.id, a.entry_form, b.to_batch_id as batch_id, b.from_prod_id as prod_id, b.to_body_part as body_part_id, e.batch_no, c.po_breakdown_id as fso_id, c.qnty, c.qc_pass_qnty, c.reject_qnty, c.barcode_no, c.roll_no, c.roll_id as roll_id_prev, c.id as roll_id, c.rate,c.amount, c.reprocess,c.prev_reprocess, b.to_floor_id as floor, b.to_room as room, b.to_rack as rack_no,  b.to_shelf as shelf_no, b.to_bin_box as bin, b.to_store as store_id, b.feb_description_id as fabric_description_id, b.gsm, b.dia_width as width, b.color_id, b.dia_width_type
	from tmp_barcode_no t, inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, pro_batch_create_mst e 
	WHERE t.barcode_no=c.barcode_no and t.userid=$user_id and a.id=b.mst_id and b.id=c.dtls_id and b.to_batch_id=e.id and c.po_breakdown_id=e.sales_order_id and e.is_sales=1 and a.entry_form in(628) and c.entry_form in(628) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales=1
	order by barcode_no";
	// echo $sql;die;
	$data_array=sql_select( $sql );

    $scanned_barcode_data=sql_select("SELECT a.barcode_no, b.issue_number from tmp_barcode_no t, pro_roll_details a, inv_issue_master b where t.barcode_no=a.barcode_no and t.userid=$user_id and a.mst_id = b.id and a.entry_form = 318 and b.entry_form = 318 and a.status_active=1 and a.is_deleted=0");

	foreach($scanned_barcode_data as $row)
	{
		$issued_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=628 and type=1");
	oci_commit($con);

	$i=1;
	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{  
			if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
			{				
				$transRollId=$row[csf('roll_id')];//roll sl id
				
				$itemString=$row[csf('fabric_description_id')].'*'.$row[csf('gsm')].'*'.$row[csf('width')];
				$fabricDesc=$constructtion_arr[$row[csf('fabric_description_id')]].", ".$composition_arr[$row[csf('fabric_description_id')]].', '.$row[csf('gsm')].', '.$row[csf('width')];

				$roll_rate = $row[csf("rate")];
				if ($roll_rate=="") $roll_rate=0;

				$color_names = "";
            	foreach (explode(",", $row[csf('color_id')]) as  $val)
            	{
            		$color_names .= $color_library[$val].",";
            	}
            	$color_names=chop($color_names,",");

				$barcodeData .=$row[csf('barcode_no')]."**".$row[csf('fso_id')]."**".$row[csf('roll_no')]."**".$row[csf('prod_id')]."**".$fabricDesc."**".$color_names."**".$itemString."**".$row[csf('amount')]."**".$roll_rate."**".$row[csf('roll_id_prev')]."**".$row[csf('qnty')]."**".$row[csf('qc_pass_qnty')]."**".$row[csf('color_id')]."**".$row[csf('body_part_id')]."**".$row[csf('floor')]."**".$row[csf('room')]."**".$row[csf('rack_no')]."**".$row[csf('shelf_no')]."**".$row[csf('bin')]."**".$transRollId."**".$row[csf('store_id')]."**".$row[csf('fabric_description_id')]."**".$row[csf('batch_id')]."**".$row[csf('batch_no')]."**".$row[csf('dia_width_type')]."**".$row[csf('reprocess')]."**".$row[csf('prev_reprocess')]."**".$row[csf('reject_qnty')]."__";
			}
		}
		echo chop($barcodeData,"__");
	}
	else
	{
		echo "0";
	}
	exit();
}

if($action=="show_transfer_listview")
{
	$data=explode("**",$data);
	
	$mst_id=$data[0];
	$order_id=$data[1];
	$cbo_transfer_criteria=$data[5];
	
	$re_trans_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=628 and re_transfer=1 and status_active=1 and is_deleted=0 and mst_id=$mst_id ", "barcode_no", "barcode_no");

	$transfer_arr=array();
	$transfer_dataArray=sql_select("SELECT a.id, a.trans_id, a.to_trans_id, b.id as roll_id, b.barcode_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$mst_id and b.mst_id=$mst_id and b.entry_form=628 and b.transfer_criteria=$cbo_transfer_criteria and b.status_active=1 and b.is_deleted=0 and b.is_sales=1");
	foreach($transfer_dataArray as $row)
	{
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id']=$row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId']=$row[csf('roll_id')];
	}

	$con = connect();
	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=628 and type=2");
	oci_commit($con);

	$sql_qry="SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.to_prod_id, b.yarn_lot, b.color_id as color_names, b.y_count as yarn_count, b.stitch_length, b.brand_id,b.body_part_id,b.floor_id,b.room,b.rack,b.shelf,b.bin_box,b.to_body_part,b.to_floor_id,b.to_room,b.to_rack,b.to_shelf,b.to_bin_box, b.batch_id, b.to_batch_id, b.transfer_qnty, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.qc_pass_qnty, c.booking_no, c.roll_id as roll_id_prev, c.rate, c.amount, 3 as type , b.from_store as store_id, b.remarks, d.detarmination_id, d.gsm, d.dia_width, b.dia_width_type, c.roll_split_from, c.reprocess, c.prev_reprocess, c.reject_qnty
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c, product_details_master d 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.to_prod_id=d.id and a.entry_form in(628) and c.entry_form in(628) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id and c.is_sales=1
	order by barcode_no";
	// echo $sql_qry;die;
	
	$data_arr=sql_select( $sql_qry );
	$barcodeNos="";$batch_ids="";
	foreach($data_arr as $vals)
	{
		$batch_id_arr[$vals[csf('batch_id')]]=$vals[csf('batch_id')];

		execute_query("insert into tmp_barcode_no (userid, entry_form, type, barcode_no) values ($user_id, 628, 2, ".$vals[csf('barcode_no')].")");
	}
	oci_commit($con);

	$batch_id_arr = array_filter(array_unique($batch_id_arr));
	if(count($batch_id_arr)>0)
	{
		$all_batch_ids = implode(",", $batch_id_arr);
		$batch_ids_cond=""; $batchIdCond="";
		if($db_type==2 && count($batch_id_arr)>999)
		{
			$batch_id_ref_chunk=array_chunk($batch_id_arr,999) ;
			foreach($batch_id_ref_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchIdCond.=" id in($chunk_arr_value) or ";
			}

			$batch_ids_cond.=" and (".chop($batchIdCond,'or ').")";
		}
		else
		{
			$batch_ids_cond=" and id in($all_batch_ids)";
		}
		$sql_batch=sql_select("select id, batch_no from pro_batch_create_mst where status_active=1 and is_deleted=0 $batch_ids_cond");
		foreach($sql_batch as $row)
		{ 
			$batch_no_arr[$row[csf('id')]]['batch_no']=$row[csf('batch_no')];
		}
	}

	//$barcodeNos=chop($barcodeNos,",");

	$sql_issue_barcode=sql_select("SELECT b.barcode_no from tmp_barcode_no t, pro_roll_details b  where t.barcode_no=b.barcode_no and t.userid=$user_id and b.entry_form in(318) and b.status_active=1 and b.is_deleted=0 and b.is_returned !=1 order by b.barcode_no");
	foreach($sql_issue_barcode as $barcodeNO)
	{ 
		$barcode_arr[$barcodeNO[csf('barcode_no')]]['barcode']=$barcodeNO[csf('barcode_no')];
	}
	
	$body_part_sql = sql_select("SELECT b.id, b.body_part_id from fabric_sales_order_mst a, fabric_sales_order_dtls b  where a.id=b.mst_id and a.company_id= '$data[2]' and b.mst_id='$data[4]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id, b.body_part_id order by b.id");
	$body_part_id_arr=array();
	foreach ($body_part_sql as $row) 
	{
		$body_part_id = $row[csf("body_part_id")];

		if($body_part_id!=""){
			$body_part_id_arr[$body_part_id] = $body_part[$row[csf("body_part_id")]];
		}
	}


	$lib_room_rack_shelf_sql = "SELECT b.company_id,b.location_id,b.store_id,b.floor_id,b.room_id,b.rack_id,b.shelf_id,b.bin_id,
	a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name,
	e.floor_room_rack_name shelf_name, f.floor_room_rack_name bin_name
	from lib_floor_room_rack_dtls b 
	left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
	left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0 
	left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0 
	left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0 
	left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0 
	where b.status_active=1 and b.is_deleted=0 and b.company_id=$data[2] and b.store_id=$data[3] 
	order by a.floor_room_rack_name , c.floor_room_rack_name , d.floor_room_rack_name , e.floor_room_rack_name , f.floor_room_rack_name";
	// echo $lib_room_rack_shelf_sql;die;
	$lib_rrsb_arr=sql_select($lib_room_rack_shelf_sql);
	if(!empty($lib_rrsb_arr))
	{
		foreach ($lib_rrsb_arr as $room_rack_shelf_row) {
			$company  = $room_rack_shelf_row[csf("company_id")];
			$floor_id = $room_rack_shelf_row[csf("floor_id")];
			$room_id  = $room_rack_shelf_row[csf("room_id")];
			$rack_id  = $room_rack_shelf_row[csf("rack_id")];
			$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
			$bin_id   = $room_rack_shelf_row[csf("bin_id")];

			if($floor_id!=""){
				$lib_floor_arr[$floor_id] = $room_rack_shelf_row[csf("floor_name")];
			}

			if($floor_id!="" && $room_id!=""){
				$lib_room_arr[$room_id] = $room_rack_shelf_row[csf("room_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!=""){
				$lib_rack_arr[$rack_id] = $room_rack_shelf_row[csf("rack_name")];
			}

			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!=""){
				$lib_shelf_arr[$shelf_id] = $room_rack_shelf_row[csf("shelf_name")];
			}
			if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
				$lib_bin_arr[$bin_id] = $room_rack_shelf_row[csf("bin_name")];
			}
		}
	}
	else
	{
		$lib_floor_arr[0]="";
		$lib_room_arr[0]="";
		$lib_rack_arr[0]="";
		$lib_shelf_arr[0]="";
		$lib_bin_arr[0]="";
	}

	$composition_arr=array(); $constructtion_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$deter_array=sql_select($sql_deter);
	foreach( $deter_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}

	execute_query("delete from tmp_barcode_no where userid=$user_id and entry_form=628 and type=2");
	oci_commit($con);
	
	if (count($data_arr)>0) 
	{
		foreach($data_arr as $rows)
		{
			$transRollId=$rows[csf('roll_id')];//transfered roll table id
			$rows[csf('roll_id')]=$rows[csf('roll_id_prev')];
			
			if($transfer_arr[$rows[csf('barcode_no')]]['dtls_id']=="")
			{
				$checked=0; 	
			}
			else $checked=1; 
			
			if($re_trans_arr[$rows[csf('barcode_no')]]=="")
			{
				$disabled=0; 	
			}
			else $disabled=1;
			//check issued barcode
			if ($barcode_arr[$rows[csf('barcode_no')]]['barcode']==$rows[csf('barcode_no')]) {
				$disabled=1;// if issue found
			}

			$color_names = "";
        	foreach (explode(",", $rows[csf('color_names')]) as  $val)
        	{
        		$color_names .= $color_library[$val].",";
        	}
        	$color_names=chop($color_names,",");

			$dtls_id=$transfer_arr[$rows[csf('barcode_no')]]['dtls_id'];
			$from_trans_id=$transfer_arr[$rows[csf('barcode_no')]]['from_trans_id'];
			$to_trans_id=$transfer_arr[$rows[csf('barcode_no')]]['to_trans_id'];
			$rolltableId=$transfer_arr[$rows[csf('barcode_no')]]['rolltableId'];
			$itemString=$rows[csf('detarmination_id')].'*'.$rows[csf('gsm')].'*'.$rows[csf('dia_width')];
			$fabricDesc=$constructtion_arr[$rows[csf('detarmination_id')]].", ".$composition_arr[$rows[csf('detarmination_id')]].', '.$rows[csf('gsm')].', '.$rows[csf('width')];
			$roll_rate = $rows[csf("rate")];
			if ($roll_rate=="") $roll_rate=0;

			$barcodeData .=$rows[csf('barcode_no')]."**".$rows[csf('po_breakdown_id')]."**".$rows[csf('roll_no')]."**".$rows[csf('prod_id')]."**".$fabricDesc."**".$color_names."**".$itemString."**".$rows[csf('amount')]."**".$roll_rate."**".$rows[csf('roll_id')]."**".$rows[csf('qnty')]."**".$rows[csf('qc_pass_qnty')]."**".$rows[csf('color_names')]."**".$rows[csf('body_part_id')]."**".$rows[csf('floor_id')]."**".$rows[csf('room')]."**".$rows[csf('rack')]."**".$rows[csf('self')]."**".$rows[csf('bin_box')]."**".$transRollId."**".$rows[csf('store_id')]."**".$rows[csf('to_prod_id')]."**".$dtls_id."**".$from_trans_id."**".$to_trans_id."**".$rolltableId."**".$rows[csf('to_body_part')]."**".$rows[csf('to_floor_id')]."**".$rows[csf('to_room')]."**".$rows[csf('to_rack')]."**".$rows[csf('to_shelf')]."**".$rows[csf('to_bin_box')]."**".$rows[csf('batch_id')]."**".$batch_no_arr[$rows[csf('batch_id')]]['batch_no']."**".$rows[csf('to_batch_id')]."**".$rows[csf('transfer_qnty')]."**".$checked."**".$disabled."**".$rows[csf('dia_width_type')]."**".$rows[csf('reprocess')]."**".$rows[csf('prev_reprocess')]."**".$rows[csf('reject_qnty')]."__";
		}
		echo chop($barcodeData,"__");
	}
	else
	{
		echo "0";
	}
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
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'finish_sales_order_to_order_roll_trans_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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
	
	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=2 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria and entry_form=628 and status_active=1 and is_deleted=0 order by id";
	// echo $sql;
	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');
	
	exit();
}

if($action=='populate_data_from_transfer_master')
{

	$data_array=sql_select("SELECT a.transfer_system_id,a.challan_no, a.company_id, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.transfer_requ_no, a.transfer_requ_id,max(b.from_store) as from_store,max(b.to_store) as to_store, a.transfer_criteria,a.purpose, a.to_company, a.to_color_id from inv_item_transfer_mst a,inv_item_transfer_dtls b where a.id=b.mst_id and a.id='$data' group by a.purpose,a.transfer_system_id,a.challan_no, a.company_id, a.transfer_date, a.item_category, a.from_order_id, a.to_order_id, a.transfer_requ_no, a.transfer_requ_id, a.transfer_criteria, a.to_company, a.to_color_id");
	foreach ($data_array as $row)
	{ 
		if ($row[csf("transfer_criteria")]==4) 
		{
			$to_company=$row[csf("company_id")];
		}
		else
		{
			$to_company=$row[csf("to_company")];
		}
		echo "load_drop_down( 'requires/finish_sales_order_to_order_roll_trans_controller','".$row[csf("transfer_criteria")].'_'.$row[csf("company_id")]."', 'load_drop_store_from', 'from_store_td' );\n";
		echo "load_drop_down( 'requires/finish_sales_order_to_order_roll_trans_controller','".$row[csf("transfer_criteria")].'_'.$to_company."', 'load_drop_store_to', 'store_td' );\n";
			
		echo "document.getElementById('update_id').value 				= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 			= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 			= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 			= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('cbo_from_store_name').value 		= '".$row[csf("from_store")]."';\n";
		echo "document.getElementById('cbo_store_name').value 			= '".$row[csf("to_store")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 		= '".change_date_format($row[csf("transfer_date")])."';\n";

		echo "$('#cbo_transfer_criteria').attr('disabled','disabled');\n";
		echo "$('#cbo_to_company_id').attr('disabled','disabled');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "document.getElementById('cbo_transfer_criteria').value 	= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_to_company_id').value 		= '".$to_company."';\n";

		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/finish_sales_order_to_order_roll_trans_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/finish_sales_order_to_order_roll_trans_controller');\n";
		//echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'bodypart_list','requires/finish_sales_order_to_order_roll_trans_controller');\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_grey_transfer_entry',1,1);\n"; 
	
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
					sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_out_qnty,
					sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_in_qnty,
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

//data save update delete here------------------------------//
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	for($k=1;$k<=$total_row;$k++)
	{ 
		$productId="productId_".$k;
		$prod_ids.=$$productId.",";
	}
	$prod_ids=implode(",",array_unique(explode(",",chop($prod_ids,','))));
	$max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in ($prod_ids) and transaction_type in (1,4,5)", "max_date");      
	$max_recv_date = date("Y-m-d", strtotime($max_recv_date));
	$trans_date = date("Y-m-d", strtotime(str_replace("'","",$txt_transfer_date)));
	if ($trans_date < $max_recv_date) 
	{
		echo "20**Transfer Date Can not Be Less Than Last Receive Date Of These Lot";
		die;
	}

	if(str_replace("'","",$cbo_transfer_criteria)==4)
	{
		$cbo_to_company_id = $cbo_company_id;
	}

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		for($x=1;$x<=$total_row;$x++)
		{
			/*$barcodeNo="barcodeNo_".$x;
			$all_barcodeNo.=$$barcodeNo.",";
			$tot_rollWgt="rollWgt_".$x;
			$tot_rollWgt2+=$$tot_rollWgt;
			$batch_no="batchNo_".$x;
			$all_batch_no.="'".$$batch_no."',";*/
			$colorName="colorName_".$x;
			$all_colorId.=$$colorName.",";
		}		
		$all_colorId=chop($all_colorId,',');
		$all_colorId_array =  array_unique(explode(",", $all_colorId));
		$all_colorId_cond=""; $colorIdCond="";
		if($db_type==2 && count($all_colorId_array)>999)
		{
			$all_colorId_array_chunk=array_chunk($all_colorId_array,999) ;
			foreach($all_colorId_array_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$colorIdCond.=" a.color_id in($chunk_arr_value) or ";
			}
			$all_colorId_cond.=" and (".chop($colorIdCond,'or ').")";
		}
		else
		{
			$all_colorId_cond=" and a.color_id in($all_colorId)";
		}
		$batchData=sql_select("SELECT a.id, a.batch_no,a.color_id, a.batch_weight, a.sales_order_id from pro_batch_create_mst a where a.batch_no=$txt_from_batch_no $all_colorId_cond and a.status_active=1 and a.is_deleted=0 and a.entry_form=628 and a.company_id=$cbo_to_company_id and a.sales_order_id=$txt_to_order_id group by a.id, a.batch_no,a.color_id, a.batch_weight, a.sales_order_id");
		$batch_data_arr=array();
		foreach ($batchData as $rows)
		{
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("sales_order_id")]]['id']=$rows[csf("id")];
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("sales_order_id")]]['batch_weight']=$rows[csf("batch_weight")];
		}

		$sales_book_data = sql_select("select sales_booking_no, booking_id, booking_without_order from fabric_sales_order_mst where id=$txt_to_order_id");
		$sales_booking_no =$sales_book_data[0][csf('sales_booking_no')];
		$sales_booking_id =$sales_book_data[0][csf('booking_id')];
		$booking_without_order =$sales_book_data[0][csf('booking_without_order')];
		
		$transfer_recv_num=''; $transfer_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
                   			//print_r($id); die;
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'FSTST',628,date("Y",time()),2 ));
			
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, transfer_criteria, to_company, entry_form, from_order_id, to_order_id, item_category, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",".$cbo_transfer_criteria.",".$cbo_to_company_id.",628,".$txt_from_order_id.",".$txt_to_order_id.",2,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			// echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
			/*$rID=sql_insert("inv_item_transfer_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;*/
			
			$transfer_recv_num=$new_transfer_system_id[0];
			$transfer_update_id=$id;
		}
		else
		{
			$field_array_update="challan_no*transfer_date*from_order_id*to_order_id*updated_by*update_date";
			$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$txt_from_order_id."*".$txt_to_order_id."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0; */
			
			$transfer_recv_num=str_replace("'","",$txt_system_id);
			$transfer_update_id=str_replace("'","",$update_id);
		}
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;

		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";

		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";

		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, floor_id, room, rack, self, bin_box,store_id, pi_wo_batch_no, inserted_by, insert_date,body_part_id";

		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, color_id, floor_id, room, rack, shelf,bin_box, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, from_store,to_store, inserted_by, insert_date, body_part_id, to_body_part,batch_id,to_batch_id, feb_description_id, from_order_id, to_order_id,remarks,gsm,dia_width,dia_width_type";		
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, qc_pass_qnty, rate, amount, roll_no, roll_id, from_roll_id, reprocess, prev_reprocess, reject_qnty, transfer_criteria,is_sales, inserted_by, insert_date";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity,color_id,is_sales, inserted_by, insert_date";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, roll_no, roll_id, barcode_no, batch_qnty, dtls_id, body_part_id, is_sales, inserted_by, insert_date";

		if(str_replace("'","",$cbo_transfer_criteria)==1) // Company to Company
		{
			$rollIds='';
			for($j=1;$j<=$total_row;$j++)
			{
				$barcodeNo="barcodeNo_".$j;
				$rollNo="rollNo_".$j;
				$batchId="batchId_".$j;
				$productId="productId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$qcPassQnty="qcPassQnty_".$j;
				$colorName="colorName_".$j;
				$floor="floor_".$j;
				$room="room_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$binbox="binbox_".$j;
				$transRollId="transRollId_".$j;
				$storeId="storeId_".$j;
				$cbo_floor_to="cbo_floor_to_".$j;
				$cbo_room_to="cbo_room_to_".$j;
				$txt_rack_to="txt_rack_to_".$j;
				$txt_shelf_to="txt_shelf_to_".$j;
				$txt_bin_to="txt_bin_to_".$j;
				$frombodypartId="frombodypartId_".$j;
				$cboToBodyPart="cbo_To_BodyPart_".$j;
				$txtRemarks="txtRemarks_".$j;
				$constructCompo="ItemDtls_".$j;
				$ItemDesc="ItemDesc_".$j;
				$rollRate="rollRate_".$j;
				$rollAmount="rollAmount_".$j;
				$diaWidthType="diaWidthType_".$j;
				$reprocess="reprocess_".$j;
				$prevReprocess="prevReprocess_".$j;
				$rejectQnty="rejectQnty_".$j;
				//echo "10**txt_bin_to : ".$$txt_bin_to;die;
				$data_item=explode("*", str_replace("'", "", $$ItemDesc));
				$detarmination_id=$data_item[0];
				$gsm=$data_item[1];
				$diaWidth=$data_item[2];
				
				$rollIds.=$$transRollId.",";
				$colorId=$colorName;

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

				$barcode_ref_arr[str_replace("'", "", $$barcodeNo)] =str_replace("'", "", $$rollWgt);
				
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				
				if ($diaWidth!="") {
					$dia_cond = " and dia_width='$diaWidth'";
				}
				else
				{
					if ($db_type==0) 
					{
						$dia_cond = " and dia_width='$diaWidth'";
					}
					else
					{
						$dia_cond = " and dia_width is null ";
					}					
				}
				$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_to_company_id and item_category_id=2 and detarmination_id=$detarmination_id and gsm='$gsm' $dia_cond and status_active=1 and is_deleted=0");
				if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_to_company_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**2"] != "")
				{
					if(count($row_prod) > 0)
					{
           				$new_prod_id = $row_prod[0][csf('id')];
           				$product_id_update_parameter[$new_prod_id]['qnty']+=str_replace("'", "", $$rollWgt);
           				$product_id_update_parameter[$new_prod_id]['amount']+=$cons_amount;
           				$update_to_prod_id[$new_prod_id]=$new_prod_id;
					}
					else
					{
						$new_prod_id = $new_prod_ref_arr[$cbo_to_company_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**2"];
						$product_id_insert_parameter[$new_prod_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**".$$constructCompo."**2"]+=str_replace("'", "", $$rollWgt);
						$product_id_insert_amount[$new_prod_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**".$$constructCompo."**2"]+=$cons_amount;
					}
               	}
               	else
               	{
               		$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
               		$new_prod_ref_arr[$cbo_to_company_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**2"] = $new_prod_id;
               		$product_id_insert_parameter[$new_prod_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**".$$constructCompo."**2"]+=str_replace("'", "", $$rollWgt);
               		$product_id_insert_amount[$new_prod_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**".$$constructCompo."**2"]+=$cons_amount;
               	}

               	// ========batch check and new batch create start==========
				$batch_no          = str_replace("'", "", $txt_from_batch_no);
				$colorId           = str_replace("'", "", $$colorName);
				$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
				$txt_to_order_id = str_replace("'", "", $txt_to_order_id);

				$batchData = $batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['id'];
				// echo "<pre>";print_r($batch_data_arr);
				if(count($batchData)>0)
				{
					$batch_id_to=$batchData;
					$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['batch_weight']+$txt_transfer_qnty;
					$field_array_batch_update="batch_weight*updated_by*update_date";
					$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
				}
				else
				{
					if($new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=="")
					{
						$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
						$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,sales_order_no,sales_order_id,is_sales,inserted_by,insert_date";
						$data_array_batch="(".$batch_id_to.",".$txt_from_batch_no.",628,".$txt_transfer_date.",".$cbo_company_id.",'".$sales_booking_id."','".$sales_booking_no."',0,".$$colorName.",".$$rollWgt.",".$txt_to_order_no.",".$txt_to_order_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						$new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=$batch_id_to;
					}
				}
				// ========batch check and new batch create end==========

				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",2,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$cons_rate.",".$cons_amount.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$binbox.",".$$storeId.",".$$batchId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$frombodypartId.")";
				
				$from_trans_id=$id_trans;
				//$id_trans=$id_trans+1;
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_to_company_id.",".$new_prod_id.",2,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$cons_rate.",".$cons_amount.",".$$cbo_floor_to.",".$$cbo_room_to.",".$$txt_rack_to.",".$$txt_shelf_to.",".$$txt_bin_to.",".$cbo_store_name.",".$batch_id_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboToBodyPart.")";	
						

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",".$new_prod_id.",2,".$$rollWgt.",".$$rollNo.",".$cons_rate.",".$cons_amount.",12,".$$colorName.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$binbox.",".$$cbo_floor_to.",".$$cbo_room_to.",".$$txt_rack_to.",".$$txt_shelf_to.",".$$txt_bin_to.",".$$storeId.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$frombodypartId.",".$$cboToBodyPart.",".$$batchId.",".$batch_id_to.",".$detarmination_id.",".$txt_from_order_id.",".$txt_to_order_id.",".$$txtRemarks.",".$gsm.",".$diaWidth.",".$$diaWidthType.")";
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$txt_to_order_id.",628,".$$rollWgt.",".$$qcPassQnty.",".$cons_rate.",".$cons_amount.",".$$rollNo.",".$$rollId.",".$$transRollId.",".$$reprocess.",".$$prevReprocess.",".$$rejectQnty.",1,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$from_trans_id.",6,628,".$id_dtls.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$$colorName.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//$id_prop=$id_prop+1;
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$id_trans.",5,628,".$id_dtls.",".$txt_to_order_id.",".$new_prod_id.",".$$rollWgt.",".$$colorName.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				
				$inserted_roll_id_arr[$id_roll] =  $id_roll;
				$barcode_id[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

				// echo str_replace("'", "", $$productId).'++++++++';
				$prodData_array[str_replace("'", "", $$productId)]+=str_replace("'", "", $$rollWgt);
				$prodData_amount_array[str_replace("'", "", $$productId)]+=str_replace("'", "", $$rollAmount);
				$all_prod_id.=$$productId.",";

				// echo '<pre>';print_r($prodData_array);
				$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
				if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
				$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$new_prod_id.",0,0,0,".$$rollWgt.",".$id_dtls.",".$$cboToBodyPart.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}

			// echo '<pre>';print_r($product_id_insert_parameter);
			if(!empty($product_id_insert_parameter))
			{
				foreach ($product_id_insert_parameter as $key => $val)
				{
					$prod_description_arr = explode("**", $key);
					$prod_id = $prod_description_arr[0];
					$fabric_desc_id = $prod_description_arr[1];
					$txt_gsm = $prod_description_arr[2];
					$txt_width = $prod_description_arr[3];
					$cons_compo = $prod_description_arr[4];

					$roll_amount = $product_id_insert_amount[$key];

					$avg_rate_per_unit = $roll_amount/$val;

					$prod_name_dtls = trim($cons_compo);

					// if Qty is zero then rate & value will be zero
					if ($val<=0) 
					{
						$roll_amount=0;
						$avg_rate_per_unit=0;
					}			

					if($data_array_prod_insert!="") $data_array_prod_insert.=",";
                   	$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_to_company_id . "," . $cbo_store_name . ",2," . $fabric_desc_id . "," . $cons_compo . "," . $prod_name_dtls . "," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
				}
			}

			if(!empty($update_to_prod_id))
			{
				$prod_id_array=array();
				$up_to_prod_ids=implode(",",array_unique($update_to_prod_id));
				//echo "10**select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ";die;
				$toProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($up_to_prod_ids) ");
				foreach($toProdIssueResult as $row)
				{
					$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")];
					$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")];						
					
					if ($stock_qnty>0) 
					{
						$avg_rate_per_unit = $stock_value/$stock_qnty;
						$stock_value = $avg_rate_per_unit*$stock_qnty;
					}
					else
					{
						$avg_rate_per_unit = 0;
						$stock_value = 0;
					}
					// if Qty is zero then rate & value will be zero
					if ($stock_qnty<=0) 
					{
						$stock_value=0;
						$avg_rate_per_unit=0;
					}
					
					// echo "10**".$avg_rate_per_unit.'==='.$stock_value.'############';
					$prod_id_array[]=$row[csf('id')];
					$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$avg_rate_per_unit."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				unset($toProdIssueResult);
			}

			$all_prod_id_arr=implode(",",array_unique(explode(",",chop($all_prod_id,','))));
			$fromProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id_arr) and company_id=$cbo_company_id");
			foreach($fromProdIssueResult as $row)
			{
				// echo $row[csf('id')].'@@';
				$issue_qty=$prodData_array[$row[csf('id')]];
				$issue_amount=$prodData_amount_array[$row[csf('id')]];

				$current_stock=$row[csf('current_stock')]-$issue_qty;
				$current_amount=$row[csf('stock_value')]-$issue_amount;
				$current_avg_rate=$row[csf('stock_value')]-$issue_amount;

				// if Qty is zero then rate & value will be zero
				if ($current_stock<=0) 
				{
					$current_amount=0;
					$current_avg_rate=0;
				}

				$prod_id_array[]=$row[csf('id')];
				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$current_stock."'*'".$current_avg_rate."'*'".$current_amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
		}
		else // order to order and store to store
		{
			$rollIds='';
			for($j=1;$j<=$total_row;$j++)
			{
				$barcodeNo="barcodeNo_".$j;
				$rollNo="rollNo_".$j;
				$batchId="batchId_".$j;
				$productId="productId_".$j;
				$rollId="rollId_".$j; //roll_id
				$rollWgt="rollWgt_".$j;
				$qcPassQnty="qcPassQnty_".$j;
				$colorName="colorName_".$j;
				$floor="floor_".$j;
				$room="room_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$binbox="binbox_".$j;
				$transRollId="transRollId_".$j;//from_roll_id
				$storeId="storeId_".$j;
				$cbo_floor_to="cbo_floor_to_".$j;
				$cbo_room_to="cbo_room_to_".$j;
				$txt_rack_to="txt_rack_to_".$j;
				$txt_shelf_to="txt_shelf_to_".$j;
				$txt_bin_to="txt_bin_to_".$j;
				$frombodypartId="frombodypartId_".$j;
				$cboToBodyPart="cbo_To_BodyPart_".$j;
				$txtRemarks="txtRemarks_".$j;
				$constructCompo="ItemDtls_".$j;
				$ItemDesc="ItemDesc_".$j;
				$rollRate="rollRate_".$j;
				$rollAmount="rollAmount_".$j;
				$diaWidthType="diaWidthType_".$j;
				$reprocess="reprocess_".$j;
				$prevReprocess="prevReprocess_".$j;
				$rejectQnty="rejectQnty_".$j;
				//echo "10**txt_bin_to : ".$$txt_bin_to;die;
				$data_item=explode("*", str_replace("'", "", $$ItemDesc));
				$detarmination_id=$data_item[0];
				$gsm=$data_item[1];
				$diaWidth=$data_item[2];
				$colorId=$$colorName;
				
				$rollIds.=$$transRollId.",";

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;

				$barcode_ref_arr[str_replace("'", "", $$barcodeNo)] =str_replace("'", "", $$rollWgt);
				
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				
				// ========batch check and new batch create start==========
				// echo $batch_id_to.'=Root='.$j.'==<br>';
				if(str_replace("'", "", $cbo_transfer_criteria) == 4)
				{
					$batch_no          = str_replace("'", "", $txt_from_batch_no);
					$colorId           = str_replace("'", "", $$colorName);
					$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
					$txt_to_order_id = str_replace("'", "", $txt_to_order_id);

					$batchData = $batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['id'];
					// echo "<pre>";print_r($batch_data_arr);
					if(count($batchData)>0)
					{
						$batch_id_to=$batchData;
						$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['batch_weight']+$txt_transfer_qnty;
						$field_array_batch_update="batch_weight*updated_by*update_date";
						$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					}
					else
					{
						if($new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=="")
						{
							$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
							$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,sales_order_no,sales_order_id,is_sales,inserted_by,insert_date";
							// echo $batch_id_to.'=B='.$j.'==<br>';
							$data_array_batch="(".$batch_id_to.",".$txt_from_batch_no.",628,".$txt_transfer_date.",".$cbo_company_id.",'".$sales_booking_id."','".$sales_booking_no."',0,".$$colorName.",".$$rollWgt.",".$txt_to_order_no.",".$txt_to_order_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							$new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=$batch_id_to;
						}
					}
				}
				else
				{
					$batch_id_to = $$batchId; // for store to store transfer
				}
				// ========batch check and new batch create end==========

				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",2,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$cons_rate.",".$cons_amount.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$binbox.",".$$storeId.",".$$batchId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$frombodypartId.")";
				
				$from_trans_id=$id_trans;
				//$id_trans=$id_trans+1;
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_to_company_id.",".$$productId.",2,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$cons_rate.",".$cons_amount.",".$$cbo_floor_to.",".$$cbo_room_to.",".$$txt_rack_to.",".$$txt_shelf_to.",".$$txt_bin_to.",".$cbo_store_name.",".$batch_id_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboToBodyPart.")";	
						

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",".$$productId.",2,".$$rollWgt.",".$$rollNo.",".$cons_rate.",".$cons_amount.",12,".$$colorName.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$binbox.",".$$cbo_floor_to.",".$$cbo_room_to.",".$$txt_rack_to.",".$$txt_shelf_to.",".$$txt_bin_to.",".$$storeId.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$frombodypartId.",".$$cboToBodyPart.",".$$batchId.",".$batch_id_to.",".$detarmination_id.",".$txt_from_order_id.",".$txt_to_order_id.",".$$txtRemarks.",".$gsm.",".$diaWidth.",".$$diaWidthType.")";
				
				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$txt_to_order_id.",628,".$$rollWgt.",".$$qcPassQnty.",".$cons_rate.",".$cons_amount.",".$$rollNo.",".$$rollId.",".$$transRollId.",".$$reprocess.",".$$prevReprocess.",".$$rejectQnty.",".$cbo_transfer_criteria.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$from_trans_id.",6,628,".$id_dtls.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$$colorName.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//$id_prop=$id_prop+1;
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$id_trans.",5,628,".$id_dtls.",".$txt_to_order_id.",".$$productId.",".$$rollWgt.",".$$colorName.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
				
				$inserted_roll_id_arr[$id_roll] =  $id_roll;
				$barcode_id[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

				if(str_replace("'", "", $cbo_transfer_criteria) == 4) // batch dtls table data insert
				{
                   	$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
					if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
					$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$$productId.",0,0,0,".$$rollWgt.",".$id_dtls.",".$$cboToBodyPart.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				}
			}
		}
		// echo "10**string";die;
		if(count($barcode_id)>0) // barcaode check validation
	    {
	    	$all_barcodeNo = implode(",", $barcode_id);
			$all_barcodeNo_cond=""; $barcodeNoCond="";
			if($db_type==2 && count($barcode_id)>999)
			{
				$all_barcodeNo_array_chunk=array_chunk($barcode_id,999) ;
				foreach($all_barcodeNo_array_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barcodeNoCond.=" a.barcode_no in($chunk_arr_value) or ";
				}
				$all_barcodeNo_cond.=" and (".chop($barcodeNoCond,'or ').")";
			}
			else
			{
				$all_barcodeNo_cond=" and a.barcode_no in($all_barcodeNo)";
			}

			//echo "10**string=".$all_barcodeNo_cond;die;

			// Issue check
			$check_if_already_scanned = sql_select("SELECT a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 318 and  a.entry_form=318 and a.is_returned!=1 $all_barcodeNo_cond and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");
			foreach ($check_if_already_scanned as $val) 
			{
				if($val[csf("barcode_no")])
				{
					echo "20**Sorry! Barcode already Scanned. Challan No: ".$val[csf("issue_number")]." Barcode No : ".$val[csf("barcode_no")];
					die;
				}
			}

			// FSO and qty check
			$trans_check_sql = sql_select("SELECT a.barcode_no, a.entry_form, a.po_breakdown_id, a.qnty from pro_roll_details a where a.entry_form in (317,628) $all_barcodeNo_cond and a.re_transfer =0 and a.status_active = 1 and a.is_deleted = 0");
			foreach ($trans_check_sql as $val) 
			{
				if( $val[csf("po_breakdown_id")]  !=  str_replace("'", "", $txt_from_order_id))
				{
					echo "20**Sorry! This barcode ". str_replace("'", "", $$barcodeNo) ." doesn't belong to this sales order ".$txt_from_order_no ."";
					die;
				}

				if( $val[csf("qnty")]  !=  $barcode_ref_arr[$val[csf("barcode_no")]])
				{
					echo "20**Sorry! current quantity does not match with original qnty. Barcode no: ". $val[csf("barcode_no")] ."";
					die;
				}
			}
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

		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			if ($data_array_prod_insert != "")
			{
				// echo "10**insert into product_details_master (".$field_array_prod_insert.") values ".$data_array_prod_insert;die;
				$rID7=sql_insert("product_details_master",$field_array_prod_insert,$data_array_prod_insert,0);
				if($rID7) $flag=1; else $flag=0;
			}
			// echo "10**".bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
		}
		
		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		} 

		/*	mysql_query("ROLLBACK");
		echo "5**".$flag;die;*/
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		//echo "10**".$rID2;die;
		// echo "10**insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		
		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		
		$rollIds=chop($rollIds,',');
		$rID4=sql_multirow_update("pro_roll_details","transfer_criteria*re_transfer","$cbo_transfer_criteria*1","id",$rollIds,0);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		}
		
		
		//echo $flag;die;
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
		if($flag==1) 
		{
			if($rID5) $flag=1; else $flag=0; 
		} 
		
		//echo $flag;die;
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID6) $flag=1; else $flag=0; 
		}

		$rID8=$rID9=true;
		if(str_replace("'","",$cbo_transfer_criteria)==1 || str_replace("'","",$cbo_transfer_criteria)==4)
		{
			if(count($batchData)>0)
			{
				//echo "10**";echo $data_array_batch_update."==".$batch_id_to;die;
				$rID8=sql_update("pro_batch_create_mst",$field_array_batch_update,$data_array_batch_update,"id",$batch_id_to,0);
			}
			else
			{
				//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;
				$rID8=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
			}
			// echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;oci_rollback($con);die;
			$rID9=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,0);
		}

		//echo $flag;die;
		//oci_rollback($con);
		//echo "5**".$flag;die;
		// echo "10**$rID##$rID2##$rID3##$rID4##$rID5##$rID6##$rID7##$rID8##$rID9";die;
		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
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

        /**
         * list of fields that will not change/update on update button event
         * fields=> from_order_id*to_order_id*
         * data => $txt_from_order_id."*".$txt_to_order_id."*".
         */
        for($x=1;$x<=$total_row;$x++)
		{
			/*$barcodeNo="barcodeNo_".$x;
			$all_barcodeNo.=$$barcodeNo.",";
			$tot_rollWgt="rollWgt_".$x;
			$tot_rollWgt2+=$$tot_rollWgt;
			$batch_no="batchNo_".$x;
			$all_batch_no.="'".$$batch_no."',";*/
			$colorName="colorName_".$x;
			$all_colorId.=$$colorName.",";
		}		
		$all_colorId=chop($all_colorId,',');
		$all_colorId_array =  array_unique(explode(",", $all_colorId));
		$all_colorId_cond=""; $colorIdCond="";
		if($db_type==2 && count($all_colorId_array)>999)
		{
			$all_colorId_array_chunk=array_chunk($all_colorId_array,999) ;
			foreach($all_colorId_array_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$colorIdCond.=" a.color_id in($chunk_arr_value) or ";
			}
			$all_colorId_cond.=" and (".chop($colorIdCond,'or ').")";
		}
		else
		{
			$all_colorId_cond=" and a.color_id in($all_colorId)";
		}

		$batchData=sql_select("SELECT a.id, a.batch_no,a.color_id, a.batch_weight, a.sales_order_id from pro_batch_create_mst a where a.batch_no=$txt_from_batch_no $all_colorId_cond and a.status_active=1 and a.is_deleted=0 and a.entry_form=628 and a.company_id=$cbo_to_company_id and a.sales_order_id=$txt_to_order_id group by a.id, a.batch_no,a.color_id, a.batch_weight, a.sales_order_id");
		$batch_data_arr=array();
		foreach ($batchData as $rows)
		{
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("sales_order_id")]]['id']=$rows[csf("id")];
			$batch_data_arr[$rows[csf("batch_no")]][$rows[csf("color_id")]][$rows[csf("sales_order_id")]]['batch_weight']=$rows[csf("batch_weight")];
		}
		// echo "<pre>";print_r($batch_data_arr);die;

		$sales_book_data = sql_select("select sales_booking_no, booking_id, booking_without_order from fabric_sales_order_mst where id=$txt_to_order_id");
		$sales_booking_no =$sales_book_data[0][csf('sales_booking_no')];
		$sales_booking_id =$sales_book_data[0][csf('booking_id')];
		$booking_without_order =$sales_book_data[0][csf('booking_without_order')];

		$field_array_update="challan_no*transfer_date*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;

		$all_prod_id="";
		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";

		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, floor_id, room, rack, self, bin_box, store_id, pi_wo_batch_no, inserted_by, insert_date,body_part_id";
		
		$field_array_trans_update="prod_id*transaction_date*order_id*cons_quantity*cons_rate*cons_amount*store_id*floor_id*room*rack*self*bin_box*body_part_id*updated_by*update_date";
		
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, color_id, floor_id, room, rack, shelf, bin_box, to_floor_id, to_room, to_rack, to_shelf, to_bin_box, from_store,to_store, inserted_by, insert_date, body_part_id, to_body_part, batch_id, to_batch_id, feb_description_id, from_order_id, to_order_id,remarks,gsm,dia_width,dia_width_type";		
		
		$field_array_dtls_update="from_prod_id*to_prod_id*transfer_qnty*roll*rate*transfer_value*from_store*to_store*floor_id*room*rack*shelf*bin_box*to_floor_id*to_room*to_rack*to_shelf*to_bin_box* body_part_id*to_body_part*gsm*dia_width*dia_width_type*updated_by*update_date";
		
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, qc_pass_qnty, rate, amount, roll_no, roll_id, from_roll_id, reprocess, prev_reprocess, reject_qnty, transfer_criteria,is_sales, inserted_by, insert_date";
		$field_array_updateroll="qnty*roll_no*updated_by*update_date";
		
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity,color_id, is_sales, inserted_by, insert_date";

		$field_array_batch_dtls="id, mst_id, po_id, prod_id, roll_no, roll_id, barcode_no, batch_qnty, dtls_id, body_part_id, is_sales, inserted_by, insert_date";

		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		
		$rollIds=''; $update_dtls_id='';$update_to_prod_id = array(); $deleted_prod_id_arr = array(); $update_from_prod_id_arr = array();
		if(str_replace("'","",$cbo_transfer_criteria)==1) // update Company to company
		{
			for($j=1;$j<=$total_row;$j++)
			{
				$barcodeNo="barcodeNo_".$j;
				$all_barcodeNo.=$$barcodeNo.",";
				$rollNo="rollNo_".$j;
				$batchId="batchId_".$j;
				$previousToBbatchId="previousToBbatchId_".$j;
				$productId="productId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$qcPassQnty="qcPassQnty_".$j;
				$hiddenTransferqnty="hiddenTransferqnty_".$j;
				$colorName="colorName_".$j;
				$floor="floor_".$j;
				$room="room_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$binbox="binbox_".$j;
				$dtlsId="dtlsId_".$j;
				$transIdFrom="transIdFrom_".$j;
				$transIdTo="transIdTo_".$j;
				$rolltableId="rolltableId_".$j;
				$transRollId="transRollId_".$j;//rollMstId
				$storeId="storeId_".$j;
				$cbo_floor_to="cbo_floor_to_".$j;
				$cbo_room_to="cbo_room_to_".$j;
				$txt_rack_to="txt_rack_to_".$j;
				$txt_shelf_to="txt_shelf_to_".$j;
				$txt_bin_to="txt_bin_to_".$j;
				$frombodypartId="frombodypartId_".$j;
				$cboToBodyPart="cbo_To_BodyPart_".$j;
				$txtRemarks="txtRemarks_".$j;
				$constructCompo="ItemDtls_".$j;
				$ItemDesc="ItemDesc_".$j;
				$rollRate="rollRate_".$j;
				$rollAmount="rollAmount_".$j;
				$diaWidthType="diaWidthType_".$j;
				$reprocess="reprocess_".$j;
				$prevReprocess="prevReprocess_".$j;
				$rejectQnty="rejectQnty_".$j;
				$toProductUp="toProductUp_".$j;
				$data_item=explode("*", str_replace("'", "", $$ItemDesc));
				$detarmination_id=$data_item[0];
				$gsm=$data_item[1];
				$diaWidth=$data_item[2];
				$colorId=$$colorName;

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;
				
				if ($$rolltableId!="") 
				{
					$saved_roll_arr[str_replace("'", "", $$barcodeNo)]=str_replace("'", "", $$rolltableId);
				}
				else
				{
					$new_roll_arr[str_replace("'", "", $$barcodeNo)]=str_replace("'", "", $$transRollId);
				}
				
				if(str_replace("'","",$$rolltableId)>0) // Company to company update
				{
					// ========batch check and new batch create start==========
					$batch_no          = str_replace("'", "", $txt_from_batch_no);
					$colorId           = str_replace("'", "", $$colorName);
					$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
					$txt_to_order_id = str_replace("'", "", $txt_to_order_id);

					$field_array_batch_update="batch_weight*updated_by*update_date";
					$batchData = $batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['id'];
					if(count($batchData)>0)
					{
						$batch_id_to=$batchData;
						// $batch_id_to=$batchData[0][csf('id')];
						if($batch_id_to==str_replace("'","",$$previousToBbatchId))
						{
							$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['batch_weight']+str_replace("'", '',$$rollWgt)-str_replace("'", '',$$hiddenTransferqnty);


							$update_batch_id[]=str_replace("'","",$$previousToBbatchId);
							$data_array_batch_update[str_replace("'","",$$previousToBbatchId)]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						}
						else
						{
							//previous batch adjusted
							if($new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=="")
							{
								$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$$previousToBbatchId");
								$adjust_batch_weight=$batch_weight-str_replace("'", '',$$hiddenTransferqnty);
								$data_array_batch_update[str_replace("'","",$$previousToBbatchId)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

								//new batch adjusted
								$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$txt_transfer_qnty);
								$update_batch_id[]=$batchData[0][csf('id')];
								$data_array_batch_update[$batchData[0][csf('id')]]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
								$new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=$batch_id_to;
							}
						}
					}
					else
					{
						if($new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=="")
						{
							$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
							$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,sales_order_no,sales_order_id,is_sales,inserted_by,insert_date";
							
							$data_array_batch="(".$batch_id_to.",".$txt_from_batch_no.",628,".$txt_transfer_date.",".$cbo_company_id.",'".$sales_booking_id."','".$sales_booking_no."','".$booking_without_order."',".$$colorName.",".$$rollWgt.",".$txt_to_order_no.",".$txt_to_order_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						}

						//previous batch adjusted
						$previousToBbatchId = str_replace("'","",$$previousToBbatchId);
						$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previousToBbatchId");
						$adjust_batch_weight=$batch_weight-str_replace("'", '',$$hiddenTransferqnty);
						$update_batch_id[]=str_replace("'","",$previousToBbatchId);
						$data_array_batch_update[str_replace("'","",$previousToBbatchId)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					}
					// ========batch check and new batch create end==========

					$update_dtls_id.=str_replace("'","",$$dtlsId).",";
					
									
					$transId_arr[]=str_replace("'","",$$transIdFrom);
					$data_array_update_trans[str_replace("'","",$$transIdFrom)]=explode("*",($$productId."*".$txt_transfer_date."*".$txt_from_order_id."*".$$rollWgt."*".$cons_rate."*".$cons_amount."*".$$storeId."*".$$floor."*".$$room."*".$$rack."*".$$shelf."*".$$binbox."*".$$frombodypartId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$transId_arr[]=str_replace("'","",$$transIdTo);
					$data_array_update_trans[str_replace("'","",$$transIdTo)]=explode("*",($$toProductUp."*".$txt_transfer_date."*".$txt_to_order_id."*".$$rollWgt."*".$cons_rate."*".$cons_amount."*".$cbo_store_name."*".$$cbo_floor_to."*".$$cbo_room_to."*".$$txt_rack_to."*".$$txt_shelf_to."*".$$txt_bin_to."*".$$cboToBodyPart."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					$dtlsId_arr[]=str_replace("'","",$$dtlsId);
					$data_array_update_dtls[str_replace("'","",$$dtlsId)]=explode("*",($$productId."*".$$toProductUp."*".$$rollWgt."*".$$rollNo."*".$cons_rate."*".$cons_amount."*".$$storeId."*".$cbo_store_name."*".$$floor."*".$$room."*".$$rack."*".$$shelf."*".$$binbox."*".$$cbo_floor_to."*".$$cbo_room_to."*".$$txt_rack_to."*".$$txt_shelf_to."*".$$txt_bin_to."*".$$frombodypartId."*".$$cboToBodyPart."*".$gsm."*".$diaWidth."*".$$diaWidthType."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$rollId_arr[]=str_replace("'","",$$rolltableId);
					$data_array_update_roll[str_replace("'","",$$rolltableId)]=explode("*",($$rollWgt."*".$$rollNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$dtlsIdProp=str_replace("'","",$$dtlsId);
					$transIdfromProp=str_replace("'","",$$transIdFrom);
					$transIdtoProp=str_replace("'","",$$transIdTo);

					$new_prod_id = $$toProductUp;
				}
				else // Company to company New insert
				{
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
					
					if ($diaWidth!="") {
						$dia_cond = " and dia_width='$diaWidth'";
					}
					else
					{
						
						if ($db_type==0) 
						{
							$dia_cond = " and dia_width='$diaWidth'";
						}
						else
						{
							$dia_cond = " and dia_width is null ";
						}					
					}

					$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_to_company_id and item_category_id=2 and detarmination_id=$detarmination_id and gsm='$gsm' $dia_cond and status_active=1 and is_deleted=0");
					if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_to_company_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**2"] != "")
					{
						if(count($row_prod) > 0)
						{
	           				$new_prod_id = $row_prod[0][csf('id')];
	           				$product_id_update_parameter[$new_prod_id]['qnty']+=str_replace("'", "", $$rollWgt);
	           				$product_id_update_parameter[$new_prod_id]['amount']+=$cons_amount;
	           				$update_to_prod_id[$new_prod_id]=$new_prod_id;
						}
						else
						{
							$new_prod_id = $new_prod_ref_arr[$cbo_to_company_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**2"];
							$product_id_insert_parameter[$new_prod_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**".$$constructCompo."**2"]+=str_replace("'", "", $$rollWgt);
							$product_id_insert_amount[$new_prod_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**".$$constructCompo."**2"]+=$cons_amount;
						}
	               	}
	               	else
	               	{
	               		$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
	               		$new_prod_ref_arr[$cbo_to_company_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**2"] = $new_prod_id;
	               		$product_id_insert_parameter[$new_prod_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**".$$constructCompo."**2"]+=str_replace("'", "", $$rollWgt);
	               		$product_id_insert_amount[$new_prod_id."**".$detarmination_id."**".$gsm."**".$diaWidth."**".$$constructCompo."**2"]+=$cons_amount;
	               	}

	               	// ========batch check and new batch create start==========
	               	$batch_no          = str_replace("'", "", $txt_from_batch_no);
					$colorId           = str_replace("'", "", $$colorName);
					$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
					$txt_to_order_id = str_replace("'", "", $txt_to_order_id);

					$batchData = $batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['id'];
					if(count($batchData)>0)
					{
						// $batch_id_to=$batchData[0][csf('id')];
						$batch_id_to=$batchData;
						$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['batch_weight']+str_replace("'", '',$$rollWgt);
						$field_array_batch_update="batch_weight*updated_by*update_date";
						$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
					}
					else
					{
						if($new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=="")
						{
							$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
							$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,sales_order_no,sales_order_id,is_sales,inserted_by,insert_date";

							$data_array_batch="(".$batch_id_to.",".$txt_from_batch_no.",628,".$txt_transfer_date.",".$cbo_company_id.",'".$sales_booking_id."','".$sales_booking_no."','".$booking_without_order."',".$$colorName.",".$$rollWgt.",".$txt_to_order_no.",".$txt_to_order_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
							$new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=$batch_id_to;
						}
					}
					// ========batch check and new batch create end==========

					$rollIds.=$$transRollId.",";
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",13,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$cons_rate.",".$cons_amount.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$binbox.",".$$storeId.",".$$batchId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$frombodypartId.")";
					
					$transIdfromProp=$id_trans;
					//$id_trans=$id_trans+1;
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$transIdtoProp=$id_trans;
					$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_to_company_id.",".$new_prod_id.",2,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$cons_rate.",".$cons_amount.",".$$cbo_floor_to.",".$$cbo_room_to.",".$$txt_rack_to.",".$$txt_shelf_to.",".$$txt_bin_to.",".$cbo_store_name.",".$batch_id_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboToBodyPart.")";
				
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$update_id.",".$transIdfromProp.",".$transIdtoProp.",".$$productId.",".$new_prod_id.",2,".$$rollWgt.",".$$rollNo.",".$cons_rate.",".$cons_amount.",12,".$$colorName.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$binbox.",".$$cbo_floor_to.",".$$cbo_room_to.",".$$txt_rack_to.",".$$txt_shelf_to.",".$$txt_bin_to.",".$$storeId.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$frombodypartId.",".$$cboToBodyPart.",".$$batchId.",".$batch_id_to.",".$detarmination_id.",".$txt_from_order_id.",".$txt_to_order_id.",".$$txtRemarks.",".$gsm.",".$diaWidth.",".$$diaWidthType.")";
					
					if($data_array_roll!="") $data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$update_id.",".$id_dtls.",".$txt_to_order_id.",628,".$$rollWgt.",".$$qcPassQnty.",".$cons_rate.",".$cons_amount.",".$$rollNo.",".$$rollId.",".$$transRollId.",".$$reprocess.",".$$prevReprocess.",".$$rejectQnty.",1,1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
					$dtlsIdProp=$id_dtls;
					$all_trans_roll_id.=$$transRollId.",";

					$barcode_ref_arr[str_replace("'", "", $$barcodeNo)]= str_replace("'", "", $$rollWgt);
					$inserted_roll_id_arr[$id_roll] =  $id_roll;
					$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);
					$all_prod_id.=$$productId.","; // if new insert $$productId is from product id

					// echo str_replace("'", "", $$productId).'++++++++';
					$prodData_array[str_replace("'", "", $$productId)]+=str_replace("'", "", $$rollWgt);
					$prodData_amount_array[str_replace("'", "", $$productId)]+=str_replace("'", "", $$rollAmount);
				}
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transIdfromProp.",6,628,".$dtlsIdProp.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$$colorName.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//$id_prop=$id_prop+1;
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$transIdtoProp.",5,628,".$dtlsIdProp.",".$txt_to_order_id.",".$new_prod_id.",".$$rollWgt.",".$$colorName.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//$id_prop=$id_prop+1;

				// echo str_replace("'", "", $$productId).'++++++++';
				//$prodData_array[str_replace("'", "", $$productId)]+=str_replace("'", "", $$rollWgt);
				//$prodData_amount_array[str_replace("'", "", $$productId)]+=str_replace("'", "", $$rollAmount);
				
				
			}			
		}
		else // update Order to Order and Store to Store
		{
			for($j=1;$j<=$total_row;$j++)
			{
				$barcodeNo="barcodeNo_".$j;
				$all_barcodeNo.=$$barcodeNo.",";
				$rollNo="rollNo_".$j;
				$batchId="batchId_".$j;
				$previousToBbatchId="previousToBbatchId_".$j;
				$productId="productId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$qcPassQnty="qcPassQnty_".$j;
				$hiddenTransferqnty="hiddenTransferqnty_".$j;
				$colorName="colorName_".$j;
				$floor="floor_".$j;
				$room="room_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$binbox="binbox_".$j;
				$dtlsId="dtlsId_".$j;
				$transIdFrom="transIdFrom_".$j;
				$transIdTo="transIdTo_".$j;
				$rolltableId="rolltableId_".$j;
				$transRollId="transRollId_".$j;//rollMstId
				$storeId="storeId_".$j;
				$cbo_floor_to="cbo_floor_to_".$j;
				$cbo_room_to="cbo_room_to_".$j;
				$txt_rack_to="txt_rack_to_".$j;
				$txt_shelf_to="txt_shelf_to_".$j;
				$txt_bin_to="txt_bin_to_".$j;
				$frombodypartId="frombodypartId_".$j;
				$cboToBodyPart="cbo_To_BodyPart_".$j;
				$txtRemarks="txtRemarks_".$j;
				$constructCompo="ItemDtls_".$j;
				$ItemDesc="ItemDesc_".$j;
				$rollRate="rollRate_".$j;
				$rollAmount="rollAmount_".$j;
				$diaWidthType="diaWidthType_".$j;
				$reprocess="reprocess_".$j;
				$prevReprocess="prevReprocess_".$j;
				$rejectQnty="rejectQnty_".$j;
				$toProductUp="toProductUp_".$j;
				$data_item=explode("*", str_replace("'", "", $$ItemDesc));
				$detarmination_id=$data_item[0];
				$gsm=$data_item[1];
				$diaWidth=$data_item[2];
				$colorId=$$colorName;
				$previousToBbatchId_arr[str_replace("'", "", $$previousToBbatchId)]= str_replace("'", "", $$previousToBbatchId);

				$cons_rate = str_replace("'", "", $$rollRate);
				$cons_amount = str_replace("'", "", $$rollWgt) * $cons_rate;
				
				if (str_replace("'", "", $$rolltableId)!="") 
				{
					$saved_roll_arr[str_replace("'", "", $$barcodeNo)]=str_replace("'", "", $$rolltableId);
				}
				else
				{
					$new_roll_arr[str_replace("'", "", $$barcodeNo)]=str_replace("'", "", $$transRollId);
				}
				$barcode_wgt_arr[str_replace("'", "", $$barcodeNo)]= str_replace("'", "", $$rollWgt);
				
				if(str_replace("'","",$$rolltableId)>0) // order to order and store to store update
				{
					// ========batch check and new batch create start==========
					if(str_replace("'","",$cbo_transfer_criteria)==4)
					{
						$batch_no          = str_replace("'", "", $txt_from_batch_no);
						$colorId           = str_replace("'", "", $$colorName);
						$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
						$txt_to_order_id = str_replace("'", "", $txt_to_order_id);

						$field_array_batch_update="batch_weight*updated_by*update_date";
						// echo $batch_no.']['.$colorId.']['.$txt_to_order_id.'<br>';
						$batchData = $batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['id'];
						if(count($batchData)>0)
						{
							// $batch_id_to=$batchData[0][csf('id')];
							$batch_id_to=$batchData;
							// echo $batch_id_to.'=AU='.$j.'<br>';
							
							if($batch_id_to==str_replace("'","",$$previousToBbatchId))
							{
								//$curr_batch_weight=$batchData[0][csf('batch_weight')]+str_replace("'", '',$$rollWgt)-str_replace("'", '',$$hiddenTransferqnty);
								$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['batch_weight']+str_replace("'", '',$txt_transfer_qnty)-str_replace("'", '',$$hiddenTransferqnty);

								$update_batch_id[]=str_replace("'","",$$previousToBbatchId);
								$data_array_batch_update[str_replace("'","",$$previousToBbatchId)]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
							}
							else
							{
								//previous batch adjusted
								$previousToBbatchId = str_replace("'","",$$previousToBbatchId);
								$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previousToBbatchId");
								$adjust_batch_weight=$batch_weight-str_replace("'", '',$$hiddenTransferqnty);
								$data_array_batch_update[str_replace("'","",$$previousToBbatchId)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

								//new batch adjusted
								$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['batch_weight']+str_replace("'", '',$txt_transfer_qnty);
								$update_batch_id[]=$batchData;
								$data_array_batch_update[$batchData]=explode("*",("'".$curr_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
							}
						}
						else
						{
							if($new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=="")
							{
								$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
								$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,sales_order_no,sales_order_id,is_sales,inserted_by,insert_date";

								$data_array_batch="(".$batch_id_to.",".$txt_from_batch_no.",628,".$txt_transfer_date.",".$cbo_company_id.",'".$sales_booking_id."','".$sales_booking_no."','".$booking_without_order."',".$$colorName.",".$$rollWgt.",".$txt_to_order_no.",".$txt_to_order_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								// echo $batch_id_to.'=U='.$j.'<br>';
								$new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=$batch_id_to;
							}
								
							//previous batch adjusted
							$previousToBbatchId = str_replace("'","",$$previousToBbatchId);
							$batch_weight= return_field_value("batch_weight","pro_batch_create_mst","id=$previousToBbatchId");
							$adjust_batch_weight=$batch_weight-str_replace("'", '',$hidden_transfer_qnty);
							$update_batch_id[]=str_replace("'","",$previousToBbatchId);
							$data_array_batch_update[str_replace("'","",$previousToBbatchId)]=explode("*",("'".$adjust_batch_weight."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
						}
					}
					else
					{
						$batch_id_to = $$batchId; // for store to store transfer
					}
					// ========batch check and new batch create end==========

					$update_dtls_id.=str_replace("'","",$$dtlsId).",";
					
									
					$transId_arr[]=str_replace("'","",$$transIdFrom);
					$data_array_update_trans[str_replace("'","",$$transIdFrom)]=explode("*",($$productId."*".$txt_transfer_date."*".$txt_from_order_id."*".$$rollWgt."*".$cons_rate."*".$cons_amount."*".$$storeId."*".$$floor."*".$$room."*".$$rack."*".$$shelf."*".$$binbox."*".$$frombodypartId."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$transId_arr[]=str_replace("'","",$$transIdTo);
					$data_array_update_trans[str_replace("'","",$$transIdTo)]=explode("*",($$productId."*".$txt_transfer_date."*'".$txt_to_order_id."'*".$$rollWgt."*".$cons_rate."*".$cons_amount."*".$cbo_store_name."*".$$cbo_floor_to."*".$$cbo_room_to."*".$$txt_rack_to."*".$$txt_shelf_to."*".$$txt_bin_to."*".$$cboToBodyPart."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					$dtlsId_arr[]=str_replace("'","",$$dtlsId);
					$data_array_update_dtls[str_replace("'","",$$dtlsId)]=explode("*",($$productId."*".$$toProductUp."*".$$rollWgt."*".$$rollNo."*".$cons_rate."*".$cons_amount."*".$$storeId."*".$cbo_store_name."*".$$floor."*".$$room."*".$$rack."*".$$shelf."*".$$binbox."*".$$cbo_floor_to."*".$$cbo_room_to."*".$$txt_rack_to."*".$$txt_shelf_to."*".$$txt_bin_to."*".$$frombodypartId."*".$$cboToBodyPart."*".$gsm."*".$diaWidth."*".$$diaWidthType."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
					
					$rollId_arr[]=str_replace("'","",$$rolltableId);
					$data_array_update_roll[str_replace("'","",$$rolltableId)]=explode("*",($$rollWgt."*".$$rollNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

					if(str_replace("'", "", $cbo_transfer_criteria) == 4)
					{
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
						$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$$productId.",0,0,0,".$$rollWgt.",".$$dtlsId.",".$$cboToBodyPart.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						// echo $data_array_batch_dtls.'<br>';
					}
					
					$dtlsIdProp=str_replace("'","",$$dtlsId);
					$transIdfromProp=str_replace("'","",$$transIdFrom);
					$transIdtoProp=str_replace("'","",$$transIdTo);
				}
				else // order to order and store to store New insert
				{
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
					$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

					// ========batch check and new batch create start==========
					if(str_replace("'", "", $cbo_transfer_criteria) == 4)
					{
						$batch_no          = str_replace("'", "", $txt_from_batch_no);
						$colorId           = str_replace("'", "", $$colorName);
						$txt_transfer_qnty = str_replace("'", "", $$rollWgt);
						$txt_to_order_id   = str_replace("'", "", $txt_to_order_id);
						// echo $batch_no.']['.$colorId.']['.$txt_to_order_id.'<br>';
						$batchData = $batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['id'];
						if(count($batchData)>0)
						{
							// $batch_id_to=$batchData[0][csf('id')];
							$batch_id_to=$batchData;
							$curr_batch_weight=$curr_batch_weight=$batch_data_arr[$batch_no][$colorId][$txt_to_order_id]['batch_weight']+str_replace("'", '',$$rollWgt);
							$field_array_batch_update="batch_weight*updated_by*update_date";
							$data_array_batch_update=$curr_batch_weight."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
							// echo $batch_id_to.'=A='.$j.'<br>';
						}
						else
						{
							if($new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=="")
							{
								$batch_id_to = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
								$field_array_batch="id,batch_no,entry_form,batch_date,company_id,booking_no_id,booking_no,booking_without_order,color_id,batch_weight,sales_order_no,sales_order_id,is_sales,inserted_by,insert_date";

								$data_array_batch="(".$batch_id_to.",".$txt_from_batch_no.",628,".$txt_transfer_date.",".$cbo_company_id.",'".$sales_booking_id."','".$sales_booking_no."','".$booking_without_order."',".$$colorName.",".$$rollWgt.",".$txt_to_order_no.",".$txt_to_order_id.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
								// echo $batch_id_to.'=A='.$j.'<br>';
								$new_created_batch[$batch_no][$colorId][$txt_to_order_id][$cbo_to_company_id]=$batch_id_to;
							}
						}
					}
					else
					{
						$batch_id_to = $$batchId; // for store to store transfer
					}
					// ========batch check and new batch create end==========

					$rollIds.=$$transRollId.",";
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",2,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$cons_rate.",".$cons_amount.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$binbox.",".$$storeId.",".$$batchId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$frombodypartId.")";
					
					$transIdfromProp=$id_trans;
					//$id_trans=$id_trans+1;
					$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$transIdtoProp=$id_trans;
					$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_to_company_id.",".$$productId.",2,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$cons_rate.",".$cons_amount.",".$$cbo_floor_to.",".$$cbo_room_to.",".$$txt_rack_to.",".$$txt_shelf_to.",".$$txt_bin_to.",".$cbo_store_name.",".$batch_id_to.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$cboToBodyPart.")";
				
					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$update_id.",".$transIdfromProp.",".$transIdtoProp.",".$$productId.",".$$productId.",2,".$$rollWgt.",".$$rollNo.",".$cons_rate.",".$cons_amount.",12,".$$colorName.",".$$floor.",".$$room.",".$$rack.",".$$shelf.",".$$binbox.",".$$cbo_floor_to.",".$$cbo_room_to.",".$$txt_rack_to.",".$$txt_shelf_to.",".$$txt_bin_to.",".$$storeId.",".$cbo_store_name.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$$frombodypartId.",".$$cboToBodyPart.",".$$batchId.",".$batch_id_to.",".$detarmination_id.",".$txt_from_order_id.",".$txt_to_order_id.",".$$txtRemarks.",".$gsm.",".$diaWidth.",".$$diaWidthType.")";
					
					if($data_array_roll!="") $data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$update_id.",".$id_dtls.",".$txt_to_order_id.",628,".$$rollWgt.",".$$qcPassQnty.",".$cons_rate.",".$cons_amount.",".$$rollNo.",".$$rollId.",".$$transRollId.",".$cbo_transfer_criteria.",".$$reprocess.",".$$prevReprocess.",".$$rejectQnty.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				
					$dtlsIdProp=$id_dtls;
					$all_trans_roll_id.=$$transRollId.",";

					$barcode_ref_arr[str_replace("'", "", $$barcodeNo)]= str_replace("'", "", $$rollWgt);
					$inserted_roll_id_arr[$id_roll] =  $id_roll;
					$new_inserted[str_replace("'", "", $$barcodeNo)] = str_replace("'", "", $$barcodeNo);

					if(str_replace("'", "", $cbo_transfer_criteria) == 4)
					{
						$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
						if($data_array_batch_dtls!="") $data_array_batch_dtls.=",";
						$data_array_batch_dtls.="(".$id_dtls_batch.",".$batch_id_to.",".$txt_to_order_id.",".$$productId.",0,0,0,".$$rollWgt.",".$id_dtls.",".$$cboToBodyPart.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					}
				}
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$transIdfromProp.",6,628,".$dtlsIdProp.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$$colorName.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//$id_prop=$id_prop+1;
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$transIdtoProp.",5,628,".$dtlsIdProp.",".$txt_to_order_id.",".$$productId.",".$$rollWgt.",".$$colorName.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//$id_prop=$id_prop+1;
			}
		}
		// echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;oci_rollback($con);die;

		// echo "10**checking";die;
		$all_barcodeNo=chop($all_barcodeNo,',');
		$all_barcodeNo_arr=explode(",", $all_barcodeNo);
		if(count($all_barcodeNo_arr)>0) // barcaode check validation
	    {
	    	$all_barcodeNo = implode(",", $all_barcodeNo_arr);
			$all_barcodeNo_cond=""; $barcodeNoCond="";
			if($db_type==2 && count($all_barcodeNo_arr)>999)
			{
				$all_barcodeNo_array_chunk=array_chunk($all_barcodeNo_arr,999) ;
				foreach($all_barcodeNo_array_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barcodeNoCond.=" a.barcode_no in($chunk_arr_value) or ";
				}
				$all_barcodeNo_cond.=" and (".chop($barcodeNoCond,'or ').")";
			}
			else
			{
				$all_barcodeNo_cond=" and a.barcode_no in($all_barcodeNo)";
			}

			//echo "10**string=".$all_barcodeNo_cond;die;

			// Split, Mother barcode transfer after, child barcode new insert current transfer id
			$next_transfer_sql = sql_select("SELECT max(a.id) as max_id,  a.barcode_no from pro_roll_details a
			where entry_form in(317,628) $all_barcodeNo_cond and a.status_active =1 and a.is_deleted=0 and a.re_transfer=0 group by  a.barcode_no");
			// echo "10**".$next_transfer_sql;die;
			foreach ($next_transfer_sql as $next_trans)
			{
				$next_transfer_arr[$next_trans[csf('barcode_no')]]=$next_trans[csf('max_id')];
			}

			$current_transfer_sql = sql_select("SELECT a.barcode_no, b.transfer_system_id as system_id, a.roll_split_from, a.qnty from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (317,628)  $all_barcodeNo_cond and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");

			foreach ($current_transfer_sql as $current_trans)
			{
				$next_transfer_ref[$current_trans[csf('barcode_no')]]["transfer_no"]=$current_trans[csf('system_id')];
				//$current_barcode_split[$current_trans[csf('barcode_no')]]=$current_trans[csf('roll_split_from')];
				$current_barcode_qnty[$current_trans[csf('barcode_no')]]=$current_trans[csf('qnty')];
			}

			if (!empty($saved_roll_arr)) // Saved barcode to next transaction found
			{
				foreach ($saved_roll_arr as $barcode => $saved_roll_id) 
				{
					
					/*if ($saved_roll_id != $next_transfer_arr[$barcode]) 
					{
						if ($current_barcode_split[$barcode]) 
						{
							echo "20**Sorry Split Found Update/Delete Not allowed, \nBarcode No :  ".$barcode;
							disconnect($con);
							die;
						}
					}*/
					if ($current_barcode_qnty[$barcode] != $barcode_wgt_arr[$barcode]) 
					{
						echo "20**Sorry Split Found Update/Delete Not allowed, \nBarcode No :  ".$barcode;
						disconnect($con);
						die;
					}
				}
			}

			$issue_data_refer = sql_select("SELECT a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 318  $all_barcodeNo_cond and a.status_active = 1 and a.is_deleted = 0 and a.is_returned=0");
			if($issue_data_refer[0][csf("barcode_no")] != "")
			{
				echo "20**Sorry Barcode No : ". $issue_data_refer[0][csf("barcode_no")] ."\nFound in Issue No ".$issue_data_refer[0][csf("issue_number")];
				disconnect($con);
				die;
			}

			

			$current_transfer_sql = sql_select("SELECT a.barcode_no, b.transfer_system_id as system_id from pro_roll_details a, inv_item_transfer_mst b where a.mst_id=b.id and a.entry_form in (628)  $all_barcodeNo_cond and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0");
			foreach ($current_transfer_sql as $current_trans)
			{
				$next_transfer_ref[$current_trans[csf('barcode_no')]]["transfer_no"]=$current_trans[csf('system_id')];
			}
			// echo '<pre>';print_r($saved_roll_arr);
			if (!empty($saved_roll_arr)) // Saved barcode to next transaction found
			{
				foreach ($saved_roll_arr as $barcode => $saved_roll_id) 
				{
					if ($saved_roll_id != $next_transfer_arr[$barcode]) 
					{
						echo "20**Sorry Barcode No : ". $barcode ." \nFound in Transfer No : ".$next_transfer_ref[$barcode]["transfer_no"];
						disconnect($con);
						die;
					}
				}
			}
			// echo '<pre>';print_r($new_roll_arr);
			if (!empty($new_roll_arr)) // new barcode show in current transfer but this barcode saved to another tab 
			{
				foreach ($new_roll_arr as $barcode => $new_roll_id) 
				{
					// echo $new_roll_id .'!='. $next_transfer_arr[$barcode].'<br>';
					if ($new_roll_id != $next_transfer_arr[$barcode]) 
					{
						echo "20**Sorry Barcode No : ". $barcode ." \nFound in Transfer No : ".$next_transfer_ref[$barcode]["transfer_no"];
						disconnect($con);
						die;
					}
				}
			}
		}
		// echo "10**string";die;

		$new_inserted_zs = array_filter($new_inserted); // barcaode check validation
		if(!empty($new_inserted_zs))
	    {
	    	$all_barcodeNo = implode(",", $new_inserted_zs);
			$all_barcodeNo_cond=""; $barcodeNoCond="";
			if($db_type==2 && count($new_inserted_zs)>999)
			{
				$all_barcodeNo_array_chunk=array_chunk($new_inserted_zs,999) ;
				foreach($all_barcodeNo_array_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$barcodeNoCond.=" a.barcode_no in($chunk_arr_value) or ";
				}
				$all_barcodeNo_cond.=" and (".chop($barcodeNoCond,'or ').")";
			}
			else
			{
				$all_barcodeNo_cond=" and a.barcode_no in($all_barcodeNo)";
			}
		
			$check_if_already_scanned = sql_select("SELECT a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 318 and  a.entry_form=318 and a.is_returned!=1 $all_barcodeNo_cond and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");

			foreach ($check_if_already_scanned as $val) 
			{
				if($val[csf("barcode_no")])
				{
					echo "20**Sorry! Barcode already Scanned. Challan No: ".$val[csf("issue_number")]." Barcode No : ".$val[csf("barcode_no")];
					die;
				}
			}

			$trans_check_sql = sql_select("SELECT a.barcode_no, a.entry_form, a.po_breakdown_id, a.qnty from pro_roll_details a where a.entry_form in (317,628) $all_barcodeNo_cond and a.re_transfer =0 and a.status_active = 1 and a.is_deleted = 0");

			foreach ($trans_check_sql as $val) 
			{
				if( $val[csf("po_breakdown_id")]  !=  str_replace("'", "", $txt_from_order_id))
				{
					echo "20**Sorry! This barcode ". str_replace("'", "", $$barcodeNo) ." doesn't belong to this sales order ".$txt_from_order_no ."";
					die;
				}

				if( $val[csf("qnty")]  !=  $barcode_ref_arr[$val[csf("barcode_no")]])
				{
					echo "20**Sorry! current quantity does not match with original qnty. Barcode no: ". $val[csf("barcode_no")] ."";
					die;
				}
			}
		}
		
		if($txt_deleted_id!="")
		{
			//echo "10**5**jahid.$txt_deleted_id";die;
			$deletedIds=explode(",",$txt_deleted_id); $dtlsIDDel=''; $transIDDel=''; $rollIDDel=''; $rollIDactive=''; $delBarcodeNo='';
			foreach($deletedIds as $delIds)
			{
				$delIds=explode("_",$delIds);
				if($dtlsIDDel=="")
				{
					$dtlsIDDel=$delIds[0];
					$transIDDel=$delIds[1].",".$delIds[2];
					$rollIDDel=$delIds[3];
					$rollIDactive=$delIds[4];
					$delBarcodeNo=$delIds[5];
				}
				else
				{
					$dtlsIDDel.=",".$delIds[0];
					$transIDDel.=",".$delIds[1].",".$delIds[2];
					$rollIDDel.=",".$delIds[3];
					$rollIDactive.=",".$delIds[4];
					$delBarcodeNo.=",".$delIds[5];
				}
			}

			$txt_deleted_prod_qty=trim($txt_deleted_prod_qty,"'");
			$txt_deleted_prod_qty=explode(",", $txt_deleted_prod_qty);
			// echo '<pre>';print_r($txt_deleted_prod_qty);
			foreach($txt_deleted_prod_qty as $val)
			{
				$qty_production=explode("_", $val);

				$up_del_prod_id_data[$qty_production[0]]['qnty'] += $qty_production[1];
				$up_del_prod_id_data[$qty_production[0]]['amount'] += $qty_production[3];
				$deleted_prod_id_arr[$qty_production[0]] = $qty_production[0];

				$up_del_from_prod_id_data[$qty_production[2]]['qnty'] += $qty_production[1];
				$up_del_from_prod_id_data[$qty_production[2]]['amount'] += $qty_production[3];
				$update_from_prod_id_arr[$qty_production[2]] = $qty_production[2];
			}

			$update_from_prod_id_arr= array_filter(array_unique($update_from_prod_id_arr));
			$deleted_prod_id_arr= array_filter(array_unique($deleted_prod_id_arr));
			
			if($delBarcodeNo != "")
			{
				$check_sql=sql_select("SELECT a.barcode_no , b.issue_number as system_no, a.entry_form, 'Issue' as msg_source from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 318 and b.entry_form = 318 and a.is_returned != 1 and  a.status_active=1 and  b.status_active=1 and a.barcode_no in ($delBarcodeNo) 
				union all 
				select a.barcode_no , b.transfer_system_id as system_no, a.entry_form, 'Transfer' as msg_source from pro_roll_details a, inv_item_transfer_mst b where a.mst_id = b.id and a.entry_form = 628 and b.entry_form = 628 and a.re_transfer = 0 and  a.status_active=1 and  b.status_active=1 and a.barcode_no in ($delBarcodeNo) and a.id not in ($rollIDDel) ");

				$msg = "";
				foreach ($check_sql as $val) 
				{
					$msg .= $val[csf("msg_source")]." Found. Barcode :".$val[csf("barcode_no")]." chalan no: ".$val[csf("system_no")]."\n";
				}

				if($msg)
				{
					echo "20**".$msg;
					disconnect($con);
					die;
				}
			}

			$prev_rol_id_sql=sql_select("select from_roll_id from pro_roll_details where id in($rollIDDel) and status_active=1");
			$prev_rol_id="";
			foreach($prev_rol_id_sql as $row)
			{
				$prev_rol_id.=$row[csf("from_roll_id")].",";
			}
			$prev_rol_id=implode(",",array_unique(explode(",",chop($prev_rol_id,","))));
			//echo "10**5##select from_roll_id from pro_roll_details where id in($rollIDDel)";die;
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			/*$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$transIDDel,0);
			$statusChangeDtls=sql_multirow_update("inv_item_transfer_dtls",$field_array_status,$data_array_status,"id",$dtlsIDDel,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$rollIDDel,0);
			$activeRoll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$rollIDactive,0);
			$active_prev_roll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$prev_rol_id,0);
			
			if($flag==1) 
			{
				if($statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $activeRoll && $active_prev_roll) $flag=1; else $flag=0; 
			}*/ 
		}
		// echo '<pre>';print_r($update_from_prod_id_arr);
		// echo "10**fail";die;

		if(!empty($product_id_insert_parameter))
		{
			foreach ($product_id_insert_parameter as $key => $val)
			{
				$prod_description_arr = explode("**", $key);
				$prod_id = $prod_description_arr[0];
				$fabric_desc_id = $prod_description_arr[1];
				$txt_gsm = $prod_description_arr[2];
				$txt_width = $prod_description_arr[3];
				$cons_compo = $prod_description_arr[4];

				$roll_amount = $product_id_insert_amount[$key];

				$avg_rate_per_unit = $roll_amount/$val;

				$prod_name_dtls = trim($cons_compo);

				// if Qty is zero then rate & value will be zero
				if ($val<=0) 
				{
					$roll_amount=0;
					$avg_rate_per_unit=0;
				}			

				if($data_array_prod_insert!="") $data_array_prod_insert.=",";
               	$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_to_company_id . "," . $cbo_store_name . ",2," . $fabric_desc_id . "," . $cons_compo . "," . $prod_name_dtls . "," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
			}
		}

		// =============
		$all_prod_id_arr=array_unique(explode(",",chop($all_prod_id,',')));
		// echo "10**";print_r($update_to_prod_id);die;
		$all_up_del_prod_id = array_merge($update_to_prod_id,$deleted_prod_id_arr,$update_from_prod_id_arr,$all_prod_id_arr); // New Roll, Deleted Roll, Deleted From roll product id Mearged to update
		// echo "10**";print_r($all_prod_id_arr);die;
		if(!empty($all_up_del_prod_id))
		{
			$prod_id_array=array();
			$all_up_del_prod_id= chop(implode(",",array_unique($all_up_del_prod_id)),",");
			$toProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_up_del_prod_id) ");

			// echo "10**"."select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_up_del_prod_id) ";die;

			foreach($toProdIssueResult as $row)
			{
				//New Roll (+) and Deleted roll (-) and Deleted from roll (+)

				$new_added_from_prod_qnty = $prodData_array[$row[csf("id")]];
				$new_added_from_prod_amount = $prodData_array_amount[$row[csf("id")]];
				// +98.2-+-13
				// echo $product_id_update_parameter[$row[csf("id")]]['qnty'] .'+'. $row[csf("current_stock")] .'-'. $up_del_prod_id_data[$row[csf("id")]]['qnty'] .'+'. $up_del_from_prod_id_data[$row[csf("id")]]['qnty'] .'-'. $new_added_from_prod_qnty.'<br>'.$row[csf("id")].'<br>';


				$stock_qnty = $product_id_update_parameter[$row[csf("id")]]['qnty'] + $row[csf("current_stock")] - $up_del_prod_id_data[$row[csf("id")]]['qnty'] + $up_del_from_prod_id_data[$row[csf("id")]]['qnty'] - $new_added_from_prod_qnty;

				$stock_value = $product_id_update_parameter[$row[csf("id")]]['amount'] + $row[csf("stock_value")] - $up_del_prod_id_data[$row[csf("id")]]['amount'] + $up_del_from_prod_id_data[$row[csf("id")]]['amount'] - $new_added_from_prod_amount;

				$avg_rate_per_unit = $stock_value/$stock_qnty;
				// if Qty is zero then rate & value will be zero
				if ($stock_qnty<=0) 
				{
					$stock_value=0;
					$avg_rate_per_unit=0;
				}
				$prod_id_array[]=$row[csf('id')];
				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$avg_rate_per_unit."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			unset($toProdIssueResult);
		}
		// ===========
		

		if($txt_deleted_id!="")
		{
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$transIDDel,0);
			$statusChangeDtls=sql_multirow_update("inv_item_transfer_dtls",$field_array_status,$data_array_status,"id",$dtlsIDDel,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$rollIDDel,0);
			$activeRoll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$rollIDactive,0);
			$active_prev_roll=sql_multirow_update("pro_roll_details","transfer_criteria*re_transfer","0*0","id",$prev_rol_id,0);// need to update here is_returned=0
			
			if($flag==1) 
			{
				if($statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $activeRoll && $active_prev_roll) $flag=1; else $flag=0; 
			} 
		}
		
		if($dtlsIDDel=="")
		{
			$update_dtls_id=chop($update_dtls_id,',');
		}
		else
		{
			$update_dtls_id=$update_dtls_id.$dtlsIDDel;
		}
		
		if($update_dtls_id!="")
		{
			$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id in(".$update_dtls_id.") and entry_form=628");
			if($flag==1) 
			{
				if($query) $flag=1; else $flag=0; 
			} 
		}
		
		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;
		
		if(count($data_array_update_roll)>0)
		{
			// echo "**10".bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_update_trans,$transId_arr);die;
			$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_update_trans,$transId_arr));
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			}
			//echo "**10".bulk_update_sql_statement("inv_item_transfer_dtls","id",$field_array_dtls_update,$data_array_update_dtls,$dtlsId_arr);die;
			$rID3=execute_query(bulk_update_sql_statement("inv_item_transfer_dtls","id",$field_array_dtls_update,$data_array_update_dtls,$dtlsId_arr));
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			}
			
			$rID4=execute_query(bulk_update_sql_statement("pro_roll_details","id",$field_array_updateroll,$data_array_update_roll,$rollId_arr));
			if($flag==1) 
			{
				if($rID4) $flag=1; else $flag=0; 
			}
			
		}

		if(str_replace("'","",$cbo_transfer_criteria)==1) // insert/update product info for company to company
		{
			if ($data_array_prod_insert != "")
			{
				// echo "10**insert into product_details_master (".$field_array_prod_insert.") values ".$data_array_prod_insert;die;
				$rID9=sql_insert("product_details_master",$field_array_prod_insert,$data_array_prod_insert,0);
				if($rID9) $flag=1; else $flag=0;
			}

			if(!empty($data_array_prod_update) )
			{
				// echo bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array );die;
				$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));

				if ($flag == 1)
				{
					if ($prodUpdate)
						$flag = 1;
					else
						$flag = 0;
				}
			}
		}		
		
		if($data_array_dtls!="")
		{
			// echo "insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
			$rIDinv=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			if($flag==1) 
			{
				if($rIDinv) $flag=1; else $flag=0; 
			} 
			// echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rIDDtls=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1) 
			{
				if($rIDDtls) $flag=1; else $flag=0; 
			} 
			
			//echo $flag;die;
			//echo "0**";
			//echo "insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rIDRoll=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			if($flag==1) 
			{
				if($rIDRoll) $flag=1; else $flag=0; 
			} 
		}
		
		$rollIds=chop($rollIds,',');
		if($rollIds!="")
		{
			$rID5=sql_multirow_update("pro_roll_details","transfer_criteria*re_transfer","$cbo_transfer_criteria*1","id",$rollIds,0);
			if($flag==1) 
			{
				if($rID5) $flag=1; else $flag=0; 
			} 
		}	
		
		
		
		// echo "10**5**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if($data_array_prop!="")
		{
			$rIDProp=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			if($flag==1) 
			{
				if($rIDProp) $flag=1; else $flag=0; 
			} 
		}

		// ======= batch mst update and create ============
		// echo "<pre>";print_r($previousToBbatchId_arr);die;
		if(str_replace("'","",$cbo_transfer_criteria)==1 || str_replace("'","",$cbo_transfer_criteria)==4)
		{			
			if(count($data_array_batch_update)>0)
			{
				//echo "10**"; echo bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id);oci_rollback($con);die;
				$batchMstUpdate=execute_query(bulk_update_sql_statement("pro_batch_create_mst","id",$field_array_batch_update,$data_array_batch_update,$update_batch_id));
				if($flag==1)
				{
					if($batchMstUpdate) $flag=1; else $flag=0;
				}
			}

			//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;

			if(count($data_array_batch)>0)
			{
				//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;oci_rollback($con);die;
				$rID8=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
				if($flag==1)
				{
					if($rID8) $flag=1; else $flag=0;
				}
			}
			
			//echo "20**all_dtlsId:: ".$all_dtlsId;die;
			$prevToBbatchId_arr = array_filter($previousToBbatchId_arr); // barcaode check validation
			if(!empty($prevToBbatchId_arr))
		    {
		    	$all_prev_batch_id = implode(",", $prevToBbatchId_arr);
				$all_prev_batch_id_cond=""; $prev_batch_idCond="";
				if($db_type==2 && count($prevToBbatchId_arr)>999)
				{
					$all_prev_batch_id_array_chunk=array_chunk($prevToBbatchId_arr,999) ;
					foreach($all_prev_batch_id_array_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$prev_batch_idCond.=" mst_id in($chunk_arr_value) or ";
					}
					$all_prev_batch_id_cond.=" and (".chop($prev_batch_idCond,'or ').")";
				}
				else
				{
					$all_prev_batch_id_cond=" and mst_id in($all_prev_batch_id)";
				}

				$all_dtlsId_arr = implode(",", $dtlsId_arr);
				$all_dtlsId_cond=""; $dtlsIdCond="";
				if($db_type==2 && count($dtlsId_arr)>999)
				{
					$all_dtlsId_array_chunk=array_chunk($dtlsId_arr,999) ;
					foreach($all_dtlsId_array_chunk as $chunk_arr)
					{
						$chunk_arr_value=implode(",",$chunk_arr);
						$dtlsIdCond.=" dtls_id in($chunk_arr_value) or ";
					}
					$all_dtlsId_cond.=" and (".chop($dtlsIdCond,'or ').")";
				}
				else
				{
					$all_dtlsId_cond=" and dtls_id in($all_dtlsId_arr)";
				}

				if($update_dtls_id!="")
				{
					// echo "20**delete from pro_batch_create_dtls where status_active=1 $all_prev_batch_id_cond $all_dtlsId_cond";die;
					$delete_batch_dtls=execute_query( "delete from pro_batch_create_dtls where status_active=1 $all_prev_batch_id_cond and dtls_id in(".$update_dtls_id.")",0);
					if($flag==1)
					{
						if($delete_batch_dtls) $flag=1; else $flag=0;
					}
				}
			}
			//echo "20**".$data_array_batch_dtls;die;

			if($data_array_batch_dtls!="")
			{
				// echo "6**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
				$batchDtls=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
				if($flag==1)
				{
					if($batchDtls) $flag=1; else $flag=0;
				}
			}
		}
		// ======= batch mst update and create ============
		
		// echo "10**5**$flag";die;
		// echo "10**5**$flag**$rID2**$rID3**$rID4**$rID9**$prodUpdate**$rIDinv**$rIDDtls**$rIDRoll**$rID5**$rIDProp**$rID7**$rID8**$batchMstUpdate**$delete_batch_dtls**$batchDtls";die;
		
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

if ($action=="finish_fabric_order_to_order_transfer_print")
{
	extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	
	echo "Will develop later";die;
	
	$sql="select id, transfer_system_id, transfer_date, challan_no, from_order_id, to_order_id, item_category, to_color_id from inv_item_transfer_mst a where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	
	$po_buyer_arr=return_library_array( "select id, buyer_id from wo_booking_mst",'id','buyer_id');
	$po_comp_arr=return_library_array( "select id, company_id from wo_booking_mst",'id','company_id');
	
	$poDataArray= sql_select("SELECT a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id, sum(b.grey_qty) as qty 
	from fabric_sales_order_mst a, fabric_sales_order_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 group by a.id, a.job_no, a.job_no_prefix_num, a.company_id, a.style_ref_no, a.sales_booking_no, a.buyer_id, a.booking_id");
	$job_array=array(); //$all_job_id='';
	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['no']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_id')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['qty']=$row[csf('qty')];
		$job_array[$row[csf('id')]]['company']=$row[csf('company_id')];
		$job_array[$row[csf('id')]]['booking']=$row[csf('sales_booking_no')];
		$job_array[$row[csf('id')]]['booking_id']=$row[csf('booking_id')];
	} 
	unset($poDataArray);

	$from_booking=$job_array[$dataArray[0][csf('from_order_id')]]['booking'];
	$to_booking=$job_array[$dataArray[0][csf('to_order_id')]]['booking'];
	if($from_booking!="" || $to_booking!="")
	{
		$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no from wo_po_break_down a, wo_booking_dtls b 
		where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and b.booking_no in('$from_booking','$to_booking') and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number, a.grouping, b.booking_no order by a.grouping";
		// echo $po_sql;die;
		$po_sql_result=sql_select($po_sql);
		$refBooking_cond="";
		foreach ($po_sql_result as $key => $row) 
		{
			$int_ref_arr[$row[csf('booking_no')]] .= $row[csf('grouping')].',';
		}
		// echo "<pre>";print_r($int_ref_arr);die;

		/*$po_sql = sql_select("select po_break_down_id from wo_booking_dtls where booking_no = '".$from_booking."'");
		foreach ($po_sql as $val)
		{
			$po_arr[$val[csf("po_break_down_id")]] =$val[csf("po_break_down_id")];
		}
		$po_arr = array_filter($po_arr);
		$po_ids = implode(",", $po_arr);

		$po_sql="select id, po_number, grouping from wo_po_break_down where status_active=1 and is_deleted=0 and id in($po_ids)";
		$result_po_sql = sql_select($po_sql);
		foreach ($result_po_sql as $key => $row)
		{
			$po_int_ref_arr[$row[csf('grouping')]]=$row[csf('grouping')];
		}
		$po_int_ref_arr = array_filter($po_int_ref_arr);
		$from_int_ref = implode(",", $po_int_ref_arr);
		unset($po_sql);*/
	}
	
	$sql_dtls="SELECT a.from_prod_id, a.transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, b.barcode_no, b.roll_no, b.booking_no, a.color_names 
	from inv_item_transfer_dtls a, pro_roll_details b 
	where a.id=b.dtls_id and b.entry_form=133 and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";
					
	$sql_result= sql_select($sql_dtls);
	$from_color_arr=array();
	foreach ($sql_result as $key => $row) 
	{
		$from_color_arr[$row[csf('color_names')]].=$row[csf('color_names')].',';
	}
	$from_color='';
	$color_id_arr= array_unique(explode(",", implode(",", $from_color_arr)));
	foreach($color_id_arr as $val)
	{
		if($val>0) $from_color.=$color_library[$val].",";
	}
	$from_color=chop($from_color,',');

	?>
	<div style="width:930px;">
		<table width="900" cellspacing="0" align="right">
			<tr>
				<td colspan="6" align="center" style="font-size:20px"><strong><? echo $company_library[$data[0]]; ?></strong></td>
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
						City No: <? echo $result[csf('city')];?> 
						Zip Code: <? echo $result[csf('zip_code')]; ?> 
						Email Address: <? echo $result[csf('email')];?> 
						Website No: <? echo $result[csf('website')];
					}
					?> 
				</td>  
			</tr>
			<tr>
				<td colspan="6" align="center" style="font-size:18px"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
			</tr>
			<tr>
				<td width="125"><strong>Transfer ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('transfer_system_id')]; ?></td>
				<td width="125"><strong>Transfer Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('transfer_date')]); ?></td>
				<td width="125"><strong>Challan No.:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
			</tr>
		</table>
		<table width="900" cellspacing="0" align="right" style="margin-top:5px;">
			<tr>
				<td width="450">
					<table width="100%" cellspacing="0" align="right" style="font-size:12px">
						<tr>
							<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>From Order</u></td>
						</tr>
						<tr>
							<td width="100">Sales Order No:</td>
							<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
							<td width="100">Quantity:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['qty']; ?></td>
						</tr>
						<tr>
							<td>Po Buyer:</td>
							<td>&nbsp;<? echo $buyer_arr[$po_buyer_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]]; ?></td>
							<td>Po Company:</td>
							<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('from_order_id')]]['booking_id']]]; ?></td>
						</tr>
						<tr>
							<td>Style Ref:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
							<td>Booking No:</td>
							<td>&nbsp;<? echo $from_booking; ?></td>
						</tr> 
						<tr>
							<td>Int. Ref. No:</td>
							<td>&nbsp;<? echo chop($int_ref_arr[$from_booking],","); ?></td>
							<td>Color:</td>
							<td>&nbsp;<? echo $from_color; ?></td>
						</tr> 
					</table>
				</td>
				<td>
					<table width="100%" cellspacing="0" align="right" style="font-size:12px">
						<tr>
							<td colspan="4" align="center" style="font-weight:bold; font-size:14px;"><u>To Order</u></td>
						</tr>
						<tr>
							<td width="100">Sales Order No:</td>
							<td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
							<td width="100">Quantity:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['qty']; ?></td>
						</tr>
						<tr>
							<td>Po Buyer:</td>
							<td>&nbsp;<? echo $buyer_arr[$po_buyer_arr[$job_array[$dataArray[0][csf('to_order_id')]]['booking_id']]]; ?></td>
							<td>Po Company:</td>
							<td>&nbsp;<? echo $company_library[$po_comp_arr[$job_array[$dataArray[0][csf('to_order_id')]]['booking_id']]]; ?></td>
						</tr>
						<tr>
							<td>Style Ref:</td>
							<td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
							<td>Booking No:</td>
							<td>&nbsp;<? echo $to_booking; ?></td>
						</tr> 
						<tr>
							<td>Int. Ref. No:</td>
							<td>&nbsp;<? echo chop($int_ref_arr[$to_booking],","); ?></td>
							<td>Color:</td>
							<td>&nbsp;<? echo $color_library[$dataArray[0][csf('to_color_id')]]; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" style="font-size:13px" >
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="80">Barcode No</th>
					<th width="60">Roll No</th>
					<th width="60">Prog. No</th>
					<th width="160">Fabric Description</th>
					<th width="80">Y/Count</th>
					<th width="70">Y/Brand</th>
					<th width="80">Y/Lot</th>
					<th width="50">Rack</th>
					<th width="50">Shelf</th>
					<th width="70">Stitch Length</th>
					<th width="50">UOM</th>
					<th>Transfered Qty</th>
				</thead>
				<tbody> 
					<?
					
					$i=1;
					foreach($sql_result as $row)
					{
						if ($i%2==0)  
							$bgcolor="#E9F3FF";
						else
							$bgcolor="#FFFFFF";
						
						$transfer_qnty=$row[csf('transfer_qnty')];
						$transfer_qnty_sum += $transfer_qnty;
						
						$ycount='';
						$count_id=explode(',',$row[csf('y_count')]);
						foreach($count_id as $count)
						{
							if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
						}   
						?>
						<tr bgcolor="<? echo $bgcolor; ?>">
							<td align="center"><? echo $i; ?></td>
							<td><? echo $row[csf("barcode_no")]; ?></td>
							<td><? echo $row[csf("roll_no")]; ?></td>
							<td><? echo $row[csf("booking_no")]; ?></td>
							<td><? echo $product_arr[$row[csf("from_prod_id")]]; ?></td>
							<td><? echo $ycount; ?></td>
							<td><? echo $brand_arr[$row[csf("brand_id")]]; ?></td>
							<td><? echo $row[csf("yarn_lot")]; ?></td>
							<td><? echo $row[csf("to_rack")]; ?></td>
							<td><? echo $row[csf("to_shelf")]; ?></td>
							<td><? echo $row[csf("stitch_length")]; ?></td>
							<td align="center"><? echo $unit_of_measurement[$row[csf("uom")]]; ?></td>
							<td align="right"><? echo $row[csf("transfer_qnty")]; ?></td>
						</tr>
						<? 
						$i++; 
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="12" align="right"><strong>Total </strong></td>
						<td align="right"><?php echo $transfer_qnty_sum; ?></td>
					</tr>                           
				</tfoot>
			</table>
			<br>
			<?
			echo signature_table(111, $data[0], "900px");
			?>
		</div>
	</div>   
	<?	
	exit();
}

?>
