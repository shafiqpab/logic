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

if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/grey_fabric_transfer_roll_milti_criteria_controller",$data);
}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/grey_fabric_transfer_roll_milti_criteria_controller*13', 'store','from_store_td', $('#cbo_company_id').val(),this.value); load_room_rack_self_bin('requires/grey_fabric_transfer_roll_milti_criteria_controller*13', 'floor','floor_td_1', $('#cbo_company_id').val(),this.value) " );
	exit();
}

if ($action=="load_drop_down_location_to")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_location_to", 160, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "load_room_rack_self_bin('requires/grey_fabric_transfer_roll_milti_criteria_controller*13*cbo_to_store', 'store','to_store_td', $('#cbo_company_id_to').val(),this.value);" );
	exit();
}



if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($cbo_company_id_to) $cbo_company_id=$cbo_company_id_to;
?>

	<script>
		function js_set_value(data,available_po,type)
		{
			if(available_po=="" && type == 'from'){
				alert("Fabric not available to transfer");
				return;
			}
			$('#order_id').val(data);
			parent.emailwindow.hide();
		}
		function fnc_close()
		{
			var txt_order_no = $('#txt_order_no').val();
			var txt_date_from = $('#txt_date_from').val();
			var txt_date_to = $('#txt_date_to').val();
			var txt_file_no = $('#txt_file_no').val();
			var txt_ref_no = $('#txt_ref_no').val();
			var txt_job_no = $('#txt_job_no').val();
			var txt_booking_no = $('#txt_booking_no').val();
			if(txt_order_no =="" && txt_ref_no =="" && txt_file_no =="" && txt_job_no =="" && txt_booking_no =="" )
			{
				if (form_validation('txt_date_from*txt_date_to','From Date*To Date')==false)
				{
					return;
				}
			}
			show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+"<? echo $cbo_company_id; ?>"+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo $txt_from_order_id; ?>'+'_'+document.getElementById('cbo_status').value, 'create_po_search_list_view', 'search_div', 'grey_fabric_transfer_roll_milti_criteria_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
		}
    </script>
</head>
<body>
<div align="center" style="width:1190px;">
	<form name="searchdescfrm"  id="searchdescfrm">
		<fieldset style="width:1160px;">
        <legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="1060" class="rpt_table" border="1" rules="all">
                <thead>
                    <th>Buyer Name</th>
                     <th>Job No</th>
                    <th>Order No</th>
                    <th>File No</th>
                    <th>Ref. No</th>
                    <th>Booking No</th>
                    <th>Status</th>
                    <th width="150">Shipment Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:60px;" class="formbutton" />
                        <input type="hidden" name="order_id" id="order_id" class="text_boxes" value="">
                    </th>
                </thead>
                <tr class="general">
                    <td>
						<?
							echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
						?>
                    </td>
                     <td>
                        <input type="text" style="width:80px;" class="text_boxes" name="txt_job_no" id="txt_job_no" placeholder="Enter Job No" />
                    </td>
                    <td>
                        <input type="text" style="width:110px;" class="text_boxes" name="txt_order_no" id="txt_order_no" placeholder="Enter Order No" />
                    </td>
                    <td>
                        <input type="text" style="width:110px;" class="text_boxes" name="txt_file_no" id="txt_file_no" placeholder="Enter File No" />
                    </td>
                    <td>
                        <input type="text" style="width:110px;" class="text_boxes" name="txt_ref_no" id="txt_ref_no" placeholder="Enter Ref. No" />
                    </td>

                    <td>
                        <input type="text" style="width:110px;" class="text_boxes" name="txt_booking_no" id="txt_booking_no" placeholder="Enter Booking. No" />
                    </td>

                    <td>
                        <?
                        	if($type == "from"){
                        		$stat_cond = "1,3";
                        	}else{
                        		$stat_cond = "1";
                        	}
                        	echo create_drop_down( "cbo_status", 90, $row_status,"", 1, "- Select -", $selected, "","",$stat_cond );
                        ?>
                    </td>

                    <td>
                    	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px" placeholder="From Date" readonly>
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px" placeholder="To Date" readonly>
                    </td>

                    <td>
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_close()" style="width:60px;" />
                    </td>
                </tr>
                <tr>
                    <td colspan="8" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
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

	if ($data[0]==0) $buyer="%%"; else $buyer=$data[0];
	$search_string="%".trim($data[1])."%";
	$company_id=$data[2];
	$file_no=$data[6];
	$ref_no=$data[7];
	$job_no=str_replace("'","",$data[8]);
	$booking_no = trim($data[9]);
	$year = trim($data[10]);
	$fromOrderId = $data[11];
	$statusId = $data[12];


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
	else {
		$shipment_date ="";

	}

	$type=$data[5];

	$str_cond="";
	if($file_no!="")  $str_cond=" and b.file_no=$file_no";
	if($ref_no!="")  $str_cond.=" and b.grouping like '%$ref_no%'";
	if($job_no!='') $str_cond.="and a.job_no like '%$job_no%'";


	if($type=="from")
	{
		$status_cond=" and b.status_active in(1,3)";
		if($statusId != 0)
		{
			$status_cond=" and b.status_active =$statusId ";
		}
	}
	else
	{
		$status_cond=" and b.status_active=1";
	}

	if($type=="to") { $orderIdOmitCond = "and b.id not in($fromOrderId)";}

	$company_cond=" and a.company_name=$company_id";

	if($booking_no!='') 
	{
		$bookin_cond ="and e.booking_no_prefix_num = '$booking_no'";

		$booking_po_sql = sql_select("select d.po_break_down_id from wo_booking_dtls d, wo_booking_mst e where d.booking_no = e.booking_no and e.booking_type=1 and e.is_short=2 and d.status_active=1 $bookin_cond");

		foreach ($booking_po_sql as $val) 
		{
			$booking_po_arr[$val[csf("po_break_down_id")]] =$val[csf("po_break_down_id")];
		}

		if(empty($booking_po_arr))
		{
			echo "Data Not Found";
			die;
		}
		else
		{
			$all_booking_po_arr = implode(",",$booking_po_arr);

			$all_booking_po_arr_cond=""; $bookpoCond="";
			if($db_type==2 && count($booking_po_arr)>999)
			{
				$booking_po_arr_chunk=array_chunk($booking_po_arr,999) ;
				foreach($booking_po_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$bookpoCond.="  b.id in($chunk_arr_value) or ";
				}

				$all_booking_po_arr_cond.=" and (".chop($bookpoCond,'or ').")";
			}
			else
			{
				$all_booking_po_arr_cond=" and b.id in($all_booking_po_arr)";
			}
		}
	}


	if($db_type==0){
		$year_field="YEAR(a.insert_date) as year";
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)= $year";
	}
	else if($db_type==2) {
		$year_field="to_char(a.insert_date,'YYYY') as year";
		$year_cond=" and to_char(a.insert_date,'YYYY')=$year";
	}

	$sql_res = sql_select("SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number,b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no ,b.status_active, c.color_number_id, e.booking_no from wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls d on b.id = d.po_break_down_id left join wo_booking_mst e on d.booking_no = e.booking_no and e.booking_type=1 and e.is_short=2 $bookin_cond, wo_po_color_size_breakdown c
		 where a.job_no=b.job_no_mst and b.id = c.po_break_down_id and a.job_no = c.job_no_mst and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $company_cond $shipment_date $str_cond $bookingCondId $year_cond $orderIdOmitCond $all_booking_po_arr_cond order by b.id, b.pub_shipment_date");

		foreach ($sql_res as $val)
		{
			$po_number_arr[$val[csf("job_no")]."*".$val[csf("year")]."*".$val[csf("job_no_prefix_num")]."*".$val[csf("company_name")]."*".$buyer_arr[$val[csf("buyer_name")]]."*".$val[csf("style_ref_no")]."*".$val[csf("job_quantity")]."*".$val[csf("file_no")]."*".$val[csf("ref_no")]."*".$val[csf("id")]."*".$val[csf("po_number")]."*".$val[csf("po_quantity")]."*".$val[csf("shipment_date")]."*".$val[csf("status_active")]] .= $color_library[$val[csf("color_number_id")]].",";

			$po_arr[$val[csf("id")]] = $val[csf("id")];

			$booking_no_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}



		$po_arr = array_filter($po_arr);
		//if($type=="to");
		if(!empty($po_arr))
		{
			$all_po_id = implode(",",$po_arr);

			$all_po_id_cond_1=""; $poCond_1="";
			$all_po_id_cond_2=""; $poCond_2="";
			if($db_type==2 && count($po_arr)>999)
			{
				$po_arr_chunk=array_chunk($po_arr,999) ;
				foreach($po_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);
					$poCond_1.="  c.po_breakdown_id in($chunk_arr_value) or ";
				}

				$all_po_id_cond_1.=" and (".chop($poCond_1,'or ').")";
			}
			else
			{
				$all_po_id_cond_1=" and c.po_breakdown_id in($all_po_id)";
			}


			$issued_barcode_arr=return_library_array("select c.barcode_no from pro_roll_details c where entry_form=61 and status_active=1 and is_deleted=0 $all_po_id_cond_1 and is_returned = 0","barcode_no", "barcode_no");


			$sql_rcv = sql_select("select a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type,c.booking_no, b.color_id as color_names, b.body_part_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.is_transfer!=6 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 $all_po_id_cond_1 and c.is_sales <> 1
			union all
			select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type ,c.booking_no, b.color_names, 0 as body_part_id
			from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83,82) and c.entry_form in(83,82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 $all_po_id_cond_1 and c.is_sales <> 1
			order by barcode_no");

			foreach ($sql_rcv as $val)
			{
				if($issued_barcode_arr[$val[csf("barcode_no")]] == "")
				{
					$available_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
				}
			}

		}

		?>

		<div id="report_container" style="width:100%">
			<table border="1" class="rpt_table" rules="all" width="1200" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th width="40">SL No</th>
						<th width="50">Job No</th>
						<th width="40">Year</th>
						<th width="60">Company</th>
						<th width="80">Buyer Name</th>
						<th width="110">Style Ref. No</th>
						<th width="80">Job Qty.</th>
						<th width="70">File No</th>
						<th width="90">Ref. No</th>
						<th width="110">PO number</th>
						<th width="110">Booking No</th>
						<th width="100">PO Quantity</th>
						<th width="100">Color</th>
						<th width="80">Shipment Date</th>
						<th width="">Status</th>
					</tr>
				</thead>
			</table>
			<div style="width:1220px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden; float: left; cursor: pointer;" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="1200" cellpadding="0" cellspacing="0" id="tbl_list_search">
					<tbody>
						<?
						$i=1;$po_data = array();
						foreach($po_number_arr as $po_datas=>$color)
						{

							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$po_data = explode("*", $po_datas);
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="js_set_value('<? echo $po_data[9];?>','<? echo $available_po_arr[$po_data[9]]?>','<? echo $type?>')" >
								<td width="40"><? echo $i;?></td>
								<td width="50"><? echo $po_data[2];?></td>
								<td width="40"><? echo $po_data[1];?></td>
								<td width="60" title="company"><? echo $company_arr[$po_data[3]];?></td>
								<td width="80"><? echo $po_data[4];?></td>
								<td width="110"><? echo $po_data[5];?></td>
								<td width="80"><? echo $po_data[6];?></td>
								<td width="70"><? echo $po_data[7];?></td>
								<td width="90"><? echo $po_data[8];?></td>
								<td width="110"><? echo $po_data[10];?></td>
								<td width="110"><? echo $booking_no_arr[$po_data[9]];?></td>
								<td width="100"><? echo $po_data[11];?></td>
								<td width="100">
									<p>
										<?
											echo implode(",",array_filter(array_unique(explode(",", chop($color,",")))));
										?>
									</p>
								</td>
								<td width="80"><? echo $po_data[12];?></td>
								<td width=""><? echo $row_status[$po_data[13]];?></td>
							</tr>
							<?
							$i++;

						}

						?>
					</tbody>
				</table>
			</div>

		<?
		exit();
}

if($action=='populate_data_from_order')
{
	// echo "string";die;
	$data=explode("**",$data);
	$po_id 			=$data[0];
	$which_order	=$data[1];
	$trans_criteria=$data[2];
	
	$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	foreach ($data_array as $row)
	{ 
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		
		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
		if($trans_criteria==2)
		{
			$which_order="to";
			echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
			echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
			echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
			echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
			echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
			echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
			echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
			echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";

		}
		
		exit();
	}
}


if($action=="show_dtls_list_view")
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	//$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$data", "barcode_num", "grey_sys_id");
	$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");

	/*$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and po_breakdown_id=$data and barcode_no not in(select barcode_no from pro_roll_details where entry_form in(51,84) and po_breakdown_id=$data and status_active=1 and is_deleted=0)","barcode_no", "barcode_no");*/
	//$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and po_breakdown_id=$data and is_returned = 0","barcode_no", "barcode_no");

	$programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$data and b.status_active=1 and b.is_deleted=0", "id", "booking_id");

	$sql="SELECT a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.floor_id, b.room, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type,c.booking_no, b.color_id as color_names, b.body_part_id ,a.store_id, c.amount, b.febric_description_id as febric_description_id, b.gsm, b.width as width 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.is_transfer!=6 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data and c.is_sales <> 1
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_floor_id as floor_id, b.to_room as room, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type ,c.booking_no, b.color_names, 0 as body_part_id ,b.to_store as store_id, c.amount, b.feb_description_id as febric_description_id, b.gsm, b.dia_width as width 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83,82) and c.entry_form in(83,82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data and c.is_sales <> 1
	order by barcode_no";
	//echo $sql;//die;

	$data_array=sql_select($sql);


	foreach($data_array as $val)
	{
		//if($issued_barcode_arr[$val[csf('barcode_no')]]=="")
		//{
			$all_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

			$body_color_type_arr[$val[csf("barcode_no")]]["body_part_id"] = $val[csf("body_part_id")];

		//}
	}

	$all_barcode_arr = array_filter($all_barcode_arr);
	if(count($all_barcode_arr)<1) {
		echo "Data Not Found";die;
	}

    if(count($all_barcode_arr)>0)
    {
	    $all_ref_barcode_nos = implode(",", $all_barcode_arr);
	    $all_ref_barcode_no=""; $barCond=""; 
	    if($db_type==2 && count($all_barcode_arr)>999)
	    {
	    	$ref_barcode_arr_chunk=array_chunk($all_barcode_arr,999) ;
	    	foreach($ref_barcode_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);	
	    		$barCond.="  barcode_no in($chunk_arr_value) or ";	
	    	}

	    	$all_ref_barcode_no.=" and (".chop($barCond,'or ').")";	
	    }
	    else
	    {
	    	$all_ref_barcode_no=" and barcode_no in($all_ref_barcode_nos)";	 
	    }

	    $issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 $all_ref_barcode_no and is_returned !=1 ","barcode_no", "barcode_no");

	    $delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 $all_ref_barcode_no", "barcode_num", "grey_sys_id");
	}


	$sql_product = sql_select("select c.entry_form, a.receive_basis, a.booking_id, c.barcode_no, a.booking_no, b.body_part_id,b.febric_description_id,b.gsm, b.width, c.po_breakdown_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(2,22)
		and c.entry_form in(2,22)  and c.status_active=1 and c.is_deleted=0  and c.is_sales <>1 and c.booking_without_order=0  $all_ref_barcode_no");


	foreach ($sql_product as $val)
	{
		if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 2)
		{
			$program_no_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
		}
		else if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 1)
		{
			$booking_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
		}
		else
		{
			$indepent_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
		}
		$barcode_ref_arr[$val[csf("barcode_no")]]["febric_description_id"] = $val[csf("febric_description_id")];
		$barcode_ref_arr[$val[csf("barcode_no")]]["gsm"] = $val[csf("gsm")];
		$barcode_ref_arr[$val[csf("barcode_no")]]["width"] = $val[csf("width")];


	}
	/*echo "<pre>";
	print_r($barcode_ref_arr);*/


	$program_no_arr = array_filter($program_no_arr);
	$booking_id_arr = array_filter($booking_id_arr);
	$indepent_po_id_arr = array_filter($indepent_po_id_arr);

	if(count($program_no_arr)>0)
	{
		$planning_sql = sql_select("select  a.color_type_id,a.body_part_id,a.booking_no, b.id as program_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id = b.mst_id and b.id in (".implode(",", $program_no_arr).") and b.status_active = 1 and b.is_deleted= 0");

		foreach ($planning_sql as  $val)
		{
			$program_data[$val[csf("program_no")]]["body_part_id"] = $val[csf("body_part_id")];
			$program_data[$val[csf("program_no")]]["color_type_id"] = $val[csf("color_type_id")];
		}
	}

	if(count($booking_id_arr)>0 )
	{

		$color_type_sql = sql_select("select a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id ,c.lib_yarn_count_deter_id, c.gsm_weight from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=3 and a.id in (". implode(",", $booking_id_arr) .") and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id, c.gsm_weight");

		foreach ($color_type_sql as $row)
		{
			$color_type_array_precost[$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['color_type_id'] = $row[csf('color_type_id')];
		}
	}

	if(count($indepent_po_id_arr)>0)
	{

		$color_type_sql = sql_select("select a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id ,c.lib_yarn_count_deter_id, c.gsm_weight, b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=3 and b.po_break_down_id in (". implode(",", $indepent_po_id_arr) .") and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id, c.gsm_weight , b.po_break_down_id");
		foreach ($color_type_sql as $row)
		{
			$color_type_array_precost[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['color_type_id'] = $row[csf('color_type_id')];
		}
	}


	foreach ($sql_product as $val)
	{

		if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 2)
		{
			$body_color_type_arr[$val[csf("barcode_no")]]["color_type_id"] = $program_data[$val[csf("booking_id")]]["color_type_id"];
		}
		else if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 1)
		{
			$body_color_type_arr[$val[csf("barcode_no")]]["color_type_id"] = $color_type_array_precost[$val[csf('booking_id')]][$val[csf('body_part_id')]][$val[csf('febric_description_id')]][$val[csf('gsm')]][$val[csf('width')]]['color_type_id'];
		}
		else
		{
			$body_color_type_arr[$val[csf("barcode_no")]]["color_type_id"] = $color_type_array_precost[$val[csf('po_breakdown_id')]][$val[csf('body_part_id')]][$val[csf('febric_description_id')]][$val[csf('gsm')]][$val[csf('width')]]['color_type_id'];
		}
	}


	$i=1;
	foreach($data_array as $row)
	{
		if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			$ycount='';
			$count_id=explode(',',$row[csf('yarn_count')]);
			foreach($count_id as $count)
			{
				if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
			}

			$transRollId=$row[csf('roll_id')];
			$program_no='';
			if($row[csf('entry_form')]==2)
			{
				if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
			}
			else if($row[csf('entry_form')]==58)
			{
				$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}
			else if($row[csf('entry_form')]==83)
			{
				$program_no=$programArr[$trans_arr[$row[csf('barcode_no')]]];
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}
			else if($row[csf('entry_form')]==22)
			{
				$program_no=$row[csf('booking_no')];
			}

			if($row[csf('febric_description_id')] == "" || $row[csf('febric_description_id')] == 0)
			{
				$febric_description_id = $barcode_ref_arr[$row[csf("barcode_no")]]["febric_description_id"];
				$febric_gsm = $barcode_ref_arr[$row[csf("barcode_no")]]["gsm"];
				$febric_width = $barcode_ref_arr[$row[csf("barcode_no")]]["width"];
			}
			else
			{
				$febric_description_id = $row[csf('febric_description_id')] ;
				$febric_gsm = $row[csf('gsm')] ;
				$febric_width = $row[csf('width')] ;
			}




		?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" name="check[]" onClick="show_selected_total('<? echo $i;?>')"/>
					<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
                    <input type="hidden" name="rollNo[]" id="hiddenRollno_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                    <input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                    <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $row[csf('yarn_lot')]; ?>"/>
                    <input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $row[csf('yarn_count')]; ?>"/>
                    <input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>
                    <input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
                    <input type="hidden" name="floorsId[]" id="floorsId_<? echo $i; ?>" value="<? echo $row[csf('floor_id')]; ?>"/>
					<input type="hidden" name="roomHidd[]" id="roomHidd_<? echo $i; ?>" value="<? echo $row[csf('room')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                    <input type="hidden" name="colorName[]" id="colorNameId_<? echo $i; ?>" value="<? echo $row[csf('color_names')]; ?>"/>
                    <input type="hidden" name="colorType[]" id="colorTypeId_<? echo $i; ?>" value="<? echo $body_color_type_arr[$row[csf("barcode_no")]]["color_type_id"]; ?>"/>
                    <input type="hidden" name="bodeyPart[]" id="bodyPartId_<? echo $i; ?>" value="<? echo $body_color_type_arr[$row[csf("barcode_no")]]["body_part_id"]; ?>"/>
                    <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/>
                    <input type="hidden" name="rollAmount[]" id="rollAmount_<? echo $i; ?>" value="<? echo $row[csf('amount')]; ?>"/>
                    <input type="hidden" name="knitDetailsId[]" id="knitDetailsId_<? echo $i; ?>" value="<? echo $row[csf('dtls_id')]; ?>"/>
                    <input type="hidden" name="febDescripId[]" id="febDescripId_<? echo $i; ?>" value="<? echo $febric_description_id; ?>"/>
                    <input type="hidden" name="gsm[]" id="gsm_<? echo $i; ?>" value="<? echo $febric_gsm; ?>"/>
                    <input type="hidden" name="diaWidth[]" id="diaWidth_<? echo $i; ?>" value="<? echo $febric_width; ?>"/></td>
				<td width="40" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
				<td width="80" id="barCodeNo_<? echo $i; ?>"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50" id="rollNo_<? echo $i; ?>"><? echo $row[csf('roll_no')]; ?></td>
				<td width="70" id="programNo_<? echo $i; ?>"><p><? echo $program_no; ?>&nbsp;</p></td>
				<td width="60" id="prodId_<? echo $i; ?>"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="180" style="word-break: break-all; word-wrap: break-word;" id="fabricDesc_<? echo $i; ?>"><p><? echo $product_arr[$row[csf('prod_id')]]; ?>&nbsp;</p></td>
				<td width="80" id="ycount_<? echo $i; ?>" style="word-break: break-all; word-wrap: break-word;"><p><? echo $ycount; ?>&nbsp;</p></td>
				<td width="70" id="brandsId_<? echo $i; ?>"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
				<td width="80" id="yarnLots_<? echo $i; ?>"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
				<td align="center" width="80" id="colorNames_<? echo $i; ?>"><p>
                	<?
                	$color_string= "";
                	foreach (explode(",",$row[csf('color_names')]) as $val) {
                		$color_string .= $color_library[$val].",";
                	}
                		echo chop($color_string,",");
                	?>&nbsp;</p>
                </td>
                <td width="80" id="colorTypeName_<? echo $i; ?>"><? echo $color_type[$body_color_type_arr[$row[csf("barcode_no")]]["color_type_id"]];?></td>
                <td width="100" id="bodyPartName_<? echo $i; ?>"><? echo $body_part[$body_color_type_arr[$row[csf("barcode_no")]]["body_part_id"]];?></td>

				<td width="120" id="floor_td_<? echo $i;?>">
				<? 
				echo create_drop_down( "floors_$i", 120, "","",1, "--Select Floor--", "", "" );
				?>
				</td>
				<td width="100" id="room_td_<? echo $i;?>">
					<? 
						echo create_drop_down( "rooms_$i", 100, "","",1, "--Select Room--", "", "" );
					?>
				</td>
				<td width="100" id="rack_td_<? echo $i;?>">
					<? 
						echo create_drop_down( "racks_$i", 100, "","", 1, "--Select Rack--", "", "" ); 
					?>
				</td>
				<td width="80" id="shelf_td_<? echo $i;?>">
					<? 
					echo create_drop_down( "self_$i", 80, "","",1, "--Select Shelf--", "", "" );
					?>					
				</td>
				
				<td width="80" id="stitchLength_<? echo $i; ?>"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td width="" id="qnty_<? echo $i; ?>" align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?></td>
			</tr>
		<?
			$i++;
		}
	}
	exit();
}


if($action=="load_drop_down_item_desc")
{
	$item_description=array();
	$sql="select a.id, a.product_name_details from product_details_master a, order_wise_pro_details b where a.id=b.prod_id and b.po_breakdown_id=$data and b.entry_form in(2,22) and b.trans_type=1 and b.status_active=1 and b.is_deleted=0";
	$dataArray=sql_select($sql);	
	foreach($dataArray as $row)
	{
		$item_description[$row[csf('id')]]=$row[csf('product_name_details')];
	}
	echo create_drop_down( "cbo_item_desc", 368, $item_description,'', 1, "--Select Item Description--",'0','','1');  
	exit();
}



if($action=="populate_data_about_order")
{
	$data=explode("**",$data);
	$order_id=$data[0];
	$prod_id=$data[1];
	$program_no=$data[2];
	$company_id=$data[3];
	$yet_issue=0;
	//echo $program_no."==".jahid;die;
	if($program_no!="")
	{
		$fabric_store_auto_update=return_field_value("auto_update","variable_settings_production","company_name =$company_id and variable_list=15 and item_category_id=13 and is_deleted=0 and status_active=1");
		
		//echo $program_no."===jahid";
		if($fabric_store_auto_update==1)
		{
			$receive_sql="select b.id, d.po_breakdown_id, d.quantity 
			from inv_receive_master b, pro_grey_prod_entry_dtls c, order_wise_pro_details d
			where b.id=c.mst_id and c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and b.entry_form=2 and d.entry_form=2 and b.receive_basis=2 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.booking_id=$program_no and d.po_breakdown_id in($order_id)";
		}
		else
		{
			$receive_sql="select b.id, d.po_breakdown_id, d.quantity 
			from inv_receive_master a, inv_receive_master b, pro_grey_prod_entry_dtls c, order_wise_pro_details d
			where a.id=b.booking_id and b.id=c.mst_id and c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and a.entry_form=2 and b.entry_form=22 and d.entry_form=22 and b.receive_basis=9 and b.status_active=1 and c.status_active=1 and d.status_active=1 and a.booking_id=$program_no and d.po_breakdown_id in($order_id)";
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
			$yet_issue+=$row[csf('quantity')];
		}
		$all_rcv_id=chop($all_rcv_id,",");
		if($all_rcv_id!="")
		{
			$rcv_rtn_sql=" select d.po_breakdown_id, d.quantity 
			from inv_issue_master b, inv_transaction c, order_wise_pro_details d
			where b.id=c.mst_id and c.id=d.trans_id and b.transaction_type=3 and b.entry_form=45 and d.entry_form=45 and b.status_active=1 and c.status_active=1 and d.status_active=1 and b.received_id in($all_rcv_id) and d.po_breakdown_id in($order_id) ";
			//echo $rcv_rtn_sql;die;
			$rcv_rtn_result=sql_select($rcv_rtn_sql);
			foreach($rcv_rtn_result as $row)
			{
				$yet_issue-=$row[csf('quantity')];
			}
		}
		
		$issue_sql=" select d.po_breakdown_id, d.quantity  from inv_grey_fabric_issue_dtls c, order_wise_pro_details d 
		where c.id=d.dtls_id and c.trans_id>0 and d.trans_id>0 and d.entry_form=16 and c.program_no=$program_no and d.po_breakdown_id in($order_id) and c.status_active=1 and d.status_active=1 ";
		$issue_result=sql_select($issue_sql);
		foreach($issue_result as $row)
		{
			$yet_issue-=$row[csf('quantity')];
		}
		
		$issue_rtn_sql=" select d.po_breakdown_id, d.quantity  
		from inv_receive_master b, inv_transaction c, order_wise_pro_details d 
		where b.id=c.mst_id and c.id=d.trans_id and b.transaction_type=4 and b.entry_form=51 and d.entry_form=51 and b.booking_id=$program_no and d.po_breakdown_id in($order_id) and b.status_active=1 and c.status_active=1 and d.status_active=1 ";
		$issue_rtn_result=sql_select($issue_rtn_sql);
		foreach($issue_rtn_result as $row)
		{
			$yet_issue+=$row[csf('quantity')];
		}
		
		$transfer_sql="select d.trans_type, d.po_breakdown_id, d.quantity 
		from inv_item_transfer_mst b, inv_item_transfer_dtls c, order_wise_pro_details d
		where b.id=c.mst_id and c.id=d.dtls_id  and b.entry_form in(13,81) and d.entry_form in(13,81) and b.status_active=1 and c.status_active=1 and d.status_active=1 and c.from_program=$program_no and d.po_breakdown_id in($order_id)";
		
		//echo $transfer_sql;die;
		
		$transfer_result=sql_select($transfer_sql);
		foreach($transfer_result as $row)
		{
			if($row[csf('trans_type')]==5)
			{
				$yet_issue+=$row[csf('quantity')];
			}
			else
			{
				$yet_issue-=$row[csf('quantity')];
			}
			
		}
	}
	else
	{
		$sql=sql_select("select 
					sum(case when entry_form in(2,22) then quantity end) as grey_fabric_recv, 
					sum(case when entry_form in(16) then quantity end) as grey_fabric_issued,
					sum(case when entry_form=45 then quantity end) as grey_fabric_recv_return, 
					sum(case when entry_form=51 then quantity end) as grey_fabric_issue_return,
					sum(case when entry_form in(13,81) and trans_type=5 then quantity end) as grey_fabric_trans_recv, 
					sum(case when entry_form in(13,80) and trans_type=6 then quantity end) as grey_fabric_trans_issued
				from order_wise_pro_details where trans_id<>0 and prod_id=$prod_id and po_breakdown_id=$order_id and is_deleted=0 and status_active=1");
				
		$grey_fabric_recv=$sql[0][csf('grey_fabric_recv')]+$sql[0][csf('grey_fabric_trans_recv')]+$sql[0][csf('grey_fabric_issue_return')];
		$grey_fabric_issued=$sql[0][csf('grey_fabric_issued')]+$sql[0][csf('grey_fabric_trans_issued')]+$sql[0][csf('grey_fabric_recv_return')];
		$yet_issue=$grey_fabric_recv-$grey_fabric_issued;
	}
	

	echo "$('#txt_stock').val('".$yet_issue."');\n"; 
 	
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
			//alert(data);return;
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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+<? echo $cbo_transfer_criteria; ?>, 'create_transfer_search_list_view', 'search_div', 'grey_fabric_transfer_roll_milti_criteria_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

 	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=$transfer_criteria and entry_form=82 and status_active=1 and is_deleted=0 order by id";

	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');

	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id,transfer_criteria,challan_no, company_id, transfer_date, item_category, from_order_id, to_order_id, to_company, location_id, to_location_id, from_store_id, to_store_id, remarks from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{ 
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_transfer_criteria').value 		= '".$row[csf("transfer_criteria")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('cbo_company_id_to').value 			= '".$row[csf("to_company")]."';\n";
		echo "document.getElementById('cbo_location').value 				= '".$row[csf("location_id")]."';\n";	
		echo "load_drop_down('requires/grey_fabric_transfer_roll_milti_criteria_controller','".$row[csf("to_company")]."', 'load_drop_down_location_to', 'to_location_td');\n";		
		echo "document.getElementById('cbo_location_to').value 			= '".$row[csf("to_location_id")]."';\n";
		echo "load_room_rack_self_bin('requires/grey_fabric_transfer_roll_milti_criteria_controller*13', 'store','from_store_td', '".$row[csf("company_id")]."','".$row[csf("location_id")]."');\n";
		echo "load_room_rack_self_bin('requires/grey_fabric_transfer_roll_milti_criteria_controller*13*cbo_to_store', 'store','to_store_td', '".$row[csf("to_company")]."','".$row[csf("to_location_id")]."');\n";
		echo "document.getElementById('cbo_store_name').value 				= '".$row[csf("from_store_id")]."';\n";
		echo "document.getElementById('cbo_store_name_to').value 			= '".$row[csf("to_store_id")]."';\n";	
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";		
		echo "document.getElementById('txt_remarks').value 					= '".$row[csf("remarks")]."';\n";
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/grey_fabric_transfer_roll_milti_criteria_controller');\n";
		//echo "get_php_form_data('".$row[csf("from_order_id")]."**from**".$row[csf("from_order_id")]"'".",'populate_data_from_order','requires/grey_fabric_transfer_roll_milti_criteria_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/grey_fabric_transfer_roll_milti_criteria_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_grey_transfer_entry',1,1);\n"; 
		exit();
	}
}


if($action=="show_transfer_listview")
{
	$data=explode("**",$data);
	$mst_id=$data[0];
	$order_id=$data[1];

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 and order_id=$order_id", "barcode_num", "grey_sys_id");
	$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");
	$re_trans_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=83 and re_transfer=1 and status_active=1 and is_deleted=0 and mst_id=$mst_id ", "barcode_no", "barcode_no");
	$issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 and po_breakdown_id=$order_id and is_returned = 0","barcode_no", "barcode_no");

	$transfer_arr=array();
	$transfer_dataArray=sql_select("select a.id, a.trans_id, a.to_trans_id, b.id as roll_id, b.barcode_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and a.mst_id=$mst_id and b.mst_id=$mst_id and b.entry_form=83 and b.transfer_criteria=4 and b.status_active=1 and b.is_deleted=0");
	foreach($transfer_dataArray as $row)
	{
		$transfer_arr[$row[csf('barcode_no')]]['dtls_id']=$row[csf('id')];
		$transfer_arr[$row[csf('barcode_no')]]['from_trans_id']=$row[csf('trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$transfer_arr[$row[csf('barcode_no')]]['rolltableId']=$row[csf('roll_id')];
	}

	$programArr=return_library_array("select a.id, a.booking_id from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and a.receive_basis=2 and b.entry_form=2 and b.po_breakdown_id=$order_id and b.status_active=1 and b.is_deleted=0", "id", "booking_id");

	$sql="SELECT a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.color_names, b.to_store as store_id, c.amount, b.feb_description_id as febric_description_id, b.gsm, b.dia_width as width 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id

	union all
	select a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type ,  b.color_id as color_names, a.store_id,c.amount, b.febric_description_id as febric_description_id, b.gsm, b.width as width
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.is_transfer!=6 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$order_id

	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type, b.color_names, b.to_store as store_id, c.amount, b.feb_description_id as febric_description_id, b.gsm, b.dia_width as width 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83,82) and c.entry_form in(83,82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$order_id and c.is_sales <> 1
	order by barcode_no ";

	// echo $sql;
	$data_array=sql_select($sql);

	//----------------------------------------------------------------------
	foreach($data_array as $val)
	{
		if($issued_barcode_arr[$val[csf('barcode_no')]]=="")
		{
			$all_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];
		}
	}

	$all_barcode_arr = array_filter($all_barcode_arr);
	if(count($all_barcode_arr)<1) {
		echo "Data Not Found";die;
	}


	$sql_product = sql_select("select c.entry_form, a.receive_basis, a.booking_id, c.barcode_no, a.booking_no, b.body_part_id,b.febric_description_id,b.gsm, b.width, c.po_breakdown_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(2,22)
		and c.entry_form in(2,22)  and c.status_active=1 and c.is_deleted=0  and c.is_sales <>1 and c.booking_without_order=0 and c.barcode_no in (".implode(",", $all_barcode_arr).")");


	foreach ($sql_product as $val)
	{
		$body_color_type_arr[$val[csf("barcode_no")]]["body_part_id"] = $val[csf("body_part_id")];

		if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 2)
		{
			$program_no_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
		}
		else if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 1)
		{
			$booking_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
		}
		else
		{
			$indepent_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
		}

	}


	$program_no_arr = array_filter($program_no_arr);
	$booking_id_arr = array_filter($booking_id_arr);
	$indepent_po_id_arr = array_filter($indepent_po_id_arr);

	if(count($program_no_arr)>0)
	{
		$planning_sql = sql_select("select  a.color_type_id,a.body_part_id,a.booking_no, b.id as program_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id = b.mst_id and b.id in (".implode(",", $program_no_arr).") and b.status_active = 1 and b.is_deleted= 0");

		foreach ($planning_sql as  $val)
		{
			$program_data[$val[csf("program_no")]]["body_part_id"] = $val[csf("body_part_id")];
			$program_data[$val[csf("program_no")]]["color_type_id"] = $val[csf("color_type_id")];
		}
		unset($planning_sql);
	}

	if(count($booking_id_arr)>0 )
	{

		$color_type_sql = sql_select("select a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id ,c.lib_yarn_count_deter_id, c.gsm_weight from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=3 and a.id in (". implode(",", $booking_id_arr) .") and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id, c.gsm_weight");

		foreach ($color_type_sql as $row)
		{
			$color_type_array_precost[$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['color_type_id'] = $row[csf('color_type_id')];
		}
		unset($color_type_sql);
	}

	if(count($indepent_po_id_arr)>0)
	{

		$color_type_sql = sql_select("select a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id ,c.lib_yarn_count_deter_id, c.gsm_weight, b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=3 and b.po_break_down_id in (". implode(",", $indepent_po_id_arr) .") and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id, c.gsm_weight , b.po_break_down_id");
		foreach ($color_type_sql as $row)
		{
			$color_type_array_precost[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['color_type_id'] = $row[csf('color_type_id')];
		}
		unset($color_type_sql);
	}


	foreach ($sql_product as $val)
	{

		if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 2)
		{
			$body_color_type_arr[$val[csf("barcode_no")]]["color_type_id"] = $program_data[$val[csf("booking_id")]]["color_type_id"];
		}
		else if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 1)
		{
			$body_color_type_arr[$val[csf("barcode_no")]]["color_type_id"] = $color_type_array_precost[$val[csf('booking_id')]][$val[csf('body_part_id')]][$val[csf('febric_description_id')]][$val[csf('gsm')]][$val[csf('width')]]['color_type_id'];
		}
		else
		{
			$body_color_type_arr[$val[csf("barcode_no")]]["color_type_id"] = $color_type_array_precost[$val[csf('po_breakdown_id')]][$val[csf('body_part_id')]][$val[csf('febric_description_id')]][$val[csf('gsm')]][$val[csf('width')]]['color_type_id'];
		}
	}



	//-------------------------------------------------------------------


	$i=1;
	foreach($data_array as $row)
	{
		if($issued_barcode_arr[$row[csf('barcode_no')]]=="")
		{
			if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

			$ycount='';
			$count_id=explode(',',$row[csf('yarn_count')]);
			foreach($count_id as $count)
			{
				if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
			}

			$transRollId=$row[csf('roll_id')];
			$program_no='';
			if($row[csf('entry_form')]==2)
			{
				if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
			}
			else if($row[csf('entry_form')]==58)
			{
				$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}
			else if($row[csf('entry_form')]==83)
			{
				$program_no=$programArr[$trans_arr[$row[csf('barcode_no')]]];
				$row[csf('roll_id')]=$row[csf('roll_id_prev')];
			}

			if($transfer_arr[$row[csf('barcode_no')]]['dtls_id']=="")
			{
				$checked="";
			}
			else $checked="checked";

			if($re_trans_arr[$row[csf('barcode_no')]]=="")
			{
				$disabled="";
			}
			else $disabled="disabled";

			$dtls_id=$transfer_arr[$row[csf('barcode_no')]]['dtls_id'];
			$from_trans_id=$transfer_arr[$row[csf('barcode_no')]]['from_trans_id'];
			$to_trans_id=$transfer_arr[$row[csf('barcode_no')]]['to_trans_id'];
			$rolltableId=$transfer_arr[$row[csf('barcode_no')]]['rolltableId'];
		?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" <? echo $checked." ".$disabled; ?> name="check[]" onClick="show_selected_total('<? echo $i;?>')" /></td>
				<td width="40"><? echo $i; ?></td>
				<td width="80"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50"><? echo $row[csf('roll_no')]; ?></td>
				<td width="70"><p><? echo $program_no; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="180"><p><? echo $product_arr[$row[csf('prod_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $ycount; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
				<td width="80" align="center">
                	<p>
                	<?
                	$color_names = "";
                	foreach (explode(",", $row[csf('color_names')]) as  $val)
                	{
                		$color_names .= $color_library[$val].",";
                	}
                		echo chop($color_names,",");
                	?>&nbsp;
                	</p>
                </td>
				<td width="80"><p><? echo $color_type[$body_color_type_arr[$row[csf("barcode_no")]]["color_type_id"]];?></p></td>
                <td width="100"><p><? echo $body_part[$body_color_type_arr[$row[csf("barcode_no")]]["body_part_id"]];?></p></td>
				<td width="120"><p><? echo $row[csf('floor')]; ?>&nbsp;</p></td>
				<td width="100"><p><? echo $row[csf('room')]; ?>&nbsp;</p></td>
				<td width="100"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
                	<input type="hidden" name="recvBasis[]" id="recvBasis_<? echo $i; ?>"/>
					<input type="hidden" name="progBookPiId[]" id="progBookPiId_<? echo $i; ?>"/>
					<input type="hidden" name="bookWithoutOrder[]" id="bookWithoutOrder_<? echo $i; ?>"/>
                    <input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $row[csf('roll_no')]; ?>"/>
                    <input type="hidden" name="progId[]" id="progId_<? echo $i; ?>" value="<? echo $program_no; ?>"/>
                    <input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
                    <input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
                    <input type="hidden" name="rollWgt[]" id="rollWgt_<? echo $i; ?>" value="<? echo $row[csf('qnty')]; ?>"/>
                    <input type="hidden" name="yarnLot[]" id="yarnLot_<? echo $i; ?>" value="<? echo $row[csf('yarn_lot')]; ?>"/>
                    <input type="hidden" name="yarnCount[]" id="yarnCount_<? echo $i; ?>" value="<? echo $row[csf('yarn_count')]; ?>"/>
                    <input type="hidden" name="stichLn[]" id="stichLn_<? echo $i; ?>" value="<? echo $row[csf('stitch_length')]; ?>"/>
                    <input type="hidden" name="brandId[]" id="brandId_<? echo $i; ?>" value="<? echo $row[csf('brand_id')]; ?>"/>
                    <input type="hidden" name="rack[]" id="rack_<? echo $i; ?>" value="<? echo $row[csf('rack')]; ?>"/>
                    <input type="hidden" name="shelf[]" id="shelf_<? echo $i; ?>" value="<? echo $row[csf('self')]; ?>"/>
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>"/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value="<? echo $from_trans_id; ?>"/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value="<? echo $to_trans_id; ?>"/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value="<? echo $rolltableId; ?>"/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                    <input type="hidden" name="colorName[]" id="colorName_<? echo $i; ?>" value="<? echo $row[csf('color_names')]; ?>"/>
                    <input type="hidden" name="colorType[]" id="colorType_<? echo $i; ?>" value="<? echo $body_color_type_arr[$row[csf("barcode_no")]]["color_type_id"]; ?>"/>
                    <input type="hidden" name="bodeyPart[]" id="bodyPart_<? echo $i; ?>" value="<? echo $body_color_type_arr[$row[csf("barcode_no")]]["body_part_id"]; ?>"/>
                    <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/>
                    <input type="hidden" name="rollAmount[]" id="rollAmount_<? echo $i; ?>" value="<? echo $row[csf('amount')]; ?>"/>
                    <input type="hidden" name="febDescripId[]" id="febDescripId_<? echo $i; ?>" value="<? echo $row[csf('febric_description_id')]; ?>"/>
                    <input type="hidden" name="gsm[]" id="gsm_<? echo $i; ?>" value="<? echo $row[csf('gsm')]; ?>"/>
                    <input type="hidden" name="diaWidth[]" id="diaWidth_<? echo $i; ?>" value="<? echo $row[csf('width')]; ?>"/>
                </td>
			</tr>
		<?
			$i++;
		}
	}
	exit();
}



if($action=="show_transfer_listview_______")
{
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	
	$sql="select id, from_prod_id, transfer_qnty, item_category, uom, to_rack as rack, to_shelf as shelf from inv_item_transfer_dtls where mst_id='$data' and status_active = '1' and is_deleted = '0'";
	
	$arr=array(0=>$item_category,1=>$product_arr,3=>$unit_of_measurement);
	 
	echo create_list_view("list_view", "Item Category,Item Description,Transfered Qnty,UOM, Rack, Shelf", "120,250,100,70,80","730","200",0, $sql, "get_php_form_data", "id", "'populate_transfer_details_form_data'", 0, "item_category,from_prod_id,0,uom,0,0", $arr, "item_category,from_prod_id,transfer_qnty,uom,rack,shelf", "requires/grey_fabric_transfer_roll_milti_criteria_controller",'','0,0,2,0,0,0');
	
	exit();
}

if($action=='populate_transfer_details_form_data')
{
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');	
	$data_array=sql_select("select id, mst_id, from_prod_id, transfer_qnty, roll, item_category, uom, y_count, yarn_lot, brand_id, to_rack, to_shelf, rack, shelf,from_program,to_program,stitch_length from inv_item_transfer_dtls where id='$data'");
	foreach ($data_array as $row)
	{ 
		$ycount='';
		$count_id=explode(',',$row[csf('y_count')]);
		foreach($count_id as $count)
		{
			if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
		}
	
		echo "document.getElementById('update_dtls_id').value 				= '".$row[csf("id")]."';\n";
		echo "document.getElementById('cbo_item_desc').value 				= '".$row[csf("from_prod_id")]."';\n";
		echo "document.getElementById('txt_transfer_qnty').value 			= '".$row[csf("transfer_qnty")]."';\n";
		echo "document.getElementById('cbo_item_category').value 			= '".$row[csf("item_category")]."';\n";
		echo "document.getElementById('cbo_uom').value 						= '".$row[csf("uom")]."';\n";
		echo "document.getElementById('txt_roll').value 					= '".$row[csf("roll")]."';\n";
		echo "document.getElementById('txt_ycount').value 					= '".$ycount."';\n";
		echo "document.getElementById('hid_ycount').value 					= '".$row[csf("y_count")]."';\n";
		echo "document.getElementById('txt_ybrand').value 					= '".$brand_arr[$row[csf('brand_id')]]."';\n";
		echo "document.getElementById('hid_ybrand').value 					= '".$row[csf("brand_id")]."';\n";
		echo "document.getElementById('txt_ylot').value 					= '".$row[csf("yarn_lot")]."';\n";
		echo "document.getElementById('txt_torack').value 					= '".$row[csf("to_rack")]."';\n";
		echo "document.getElementById('txt_toshelf').value 					= '".$row[csf("to_shelf")]."';\n";
		echo "document.getElementById('txt_rack').value 					= '".$row[csf("rack")]."';\n";
		echo "document.getElementById('txt_shelf').value 					= '".$row[csf("shelf")]."';\n";
		echo "document.getElementById('txt_form_prog').value 				= '".$row[csf("from_program")]."';\n";
		echo "document.getElementById('txt_to_prog').value 					= '".$row[csf("to_program")]."';\n";
		echo "document.getElementById('stitch_length').value 				= '".$row[csf("stitch_length")]."';\n";
		echo "document.getElementById('hide_trans_qty').value 				= '".$row[csf("transfer_qnty")]."';\n";
		echo "populate_stock();\n";
		//$sql_trans=sql_select("select id, transaction_type from inv_transaction where mst_id=".$row[csf('mst_id')]." and prod_id=".$row[csf('from_prod_id')]." and item_category=13 and transaction_type in(5,6) order by id asc");
		$sql_trans=sql_select("select trans_id from order_wise_pro_details where dtls_id=".$row[csf('id')]." and entry_form=13 and trans_type in(5,6) order by trans_type DESC");
		echo "document.getElementById('update_trans_issue_id').value 		= '".$sql_trans[0][csf("trans_id")]."';\n";
		echo "document.getElementById('update_trans_recv_id').value 		= '".$sql_trans[1][csf("trans_id")]."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_transfer_entry',1,1);\n"; 
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

if($action=="barcode_nos")
{
	if($db_type==0) 
	{
		$barcode_nos=return_field_value("group_concat(barcode_no order by id desc) as barcode_nos","pro_roll_details","entry_form=82 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	else if($db_type==2) 
	{
		//echo "SELECT LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos FROM pro_roll_details WHERE entry_form=82 and status_active=1 and is_deleted=0 and mst_id=$data";

		$barcode_nos=return_field_value("LISTAGG(barcode_no, ',') WITHIN GROUP (ORDER BY id desc) as barcode_nos","pro_roll_details","entry_form=82 and status_active=1 and is_deleted=0 and mst_id=$data","barcode_nos");
	}
	echo $barcode_nos;
	exit();	
}

if($action=="barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($company_id>0) $disable=1; else $disable=0;  
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
    </script>

	</head>

	<body>
	<div align="center" style="width:960px;">
		<form name="searchwofrm"  id="searchwofrm">
			<fieldset style="width:960px; margin-left:2px;">
			<legend>Enter search words</legend>           
	            <table cellpadding="0" cellspacing="0" width="850" border="1" rules="all" class="rpt_table">
	                <thead>
	                    <th>Location</th>
	                    <th>Order No</th>
	                    <th>File No</th>
	                    <th>Internal Ref No</th>
	                    <th>Barcode No</th>
	                    <th>Booking No</th>
	                    <th>
	                    	<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
	                        <input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">  
	                    </th> 
	                </thead>
	                <tr class="general">
	                    <td>
	                    <?
							echo create_drop_down( "cbo_location_name", 130, "select id,location_name from lib_location where company_id=$company_id and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
						?>
	                    </td>
	                    <td align="center">				
	                        <input type="text" style="width:120px" class="text_boxes"  name="txt_order_no" id="txt_order_no" />	
	                    </td> 
	                    <td align="center">				
	                        <input type="text" style="width:120px" class="text_boxes"  name="txt_file_no" id="txt_file_no" />	
	                    </td>
	                    <td align="center">				
	                        <input type="text" style="width:120px" class="text_boxes"  name="txt_ref_no" id="txt_ref_no" />	
	                    </td>			
	                    <td align="center"><input type="text" name="barcode_no" id="barcode_no" style="width:120px" class="text_boxes" /></td> 
	                    <td align="center">				
	                        <input type="text" style="width:120px" class="text_boxes"  name="txt_booking_no" id="txt_booking_no" />	
	                    </td>
	                       			
	            		<td align="center">
	                     	<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_location_name').value+'_'+document.getElementById('txt_order_no').value+'_'+<? echo $company_id; ?>+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('barcode_no').value+'_'+<? echo $cbo_transfer_criteria; ?>+'_'+<? echo $cbo_to_store; ?>+'_'+document.getElementById('txt_booking_no').value, 'create_barcode_search_list_view', 'search_div', 'grey_fabric_transfer_roll_milti_criteria_controller', 'setFilterGrid(\'tbl_list_search\',-1);reset_hide_field();')" style="width:100px;" />
	                     </td>
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

if($action=="create_barcode_search_list_view")
{
	$data = explode("_",$data);
	
	$location_id=trim($data[0]);
	$order_no=$data[1];
	$company_id =$data[2];
	$file_no =trim($data[3]);
	$ref_no =trim($data[4]);
	$barcode_no =trim($data[5]);
	$transfer_cateria =trim($data[6]);
	$store_id=trim($data[7]);
	$bookingNo=trim($data[8]);
	
	// echo $store_id."jahid";die;
	

	$search_field_cond="";
	if($order_no!="") $search_field_cond=" and d.po_number like '%$order_no%'";
	if($file_no!="") $search_field_cond.=" and d.file_no like '%$file_no%'";
	if($ref_no!="") $search_field_cond.=" and d.grouping like '%$ref_no%'";
	
	$location_cond="";
	if($location_id>0) $location_cond=" and a.location_id=$location_id";
	
	if($barcode_no!="")
	{
		$barcode_cond=" and c.barcode_no='$barcode_no'";
	}
	
	$scanned_barcode_arr=array();
	$barcodeData=sql_select( "select barcode_no from pro_roll_details where entry_form in(61) and is_returned=0 and status_active=1 and is_deleted=0");
	foreach ($barcodeData as $row)
	{
		$scanned_barcode_arr[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}

	if($bookingNo !=""){
		//"query from plan to get program_no";
		$program_arr=array();$programIds="";
		$qry_plan=sql_select( "select a.booking_no,b.id as progran_no from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no like '%$bookingNo%' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");

		foreach ($qry_plan as $row) {
			$program_arr[$row[csf('progran_no')]]["progran_no"]=$row[csf('progran_no')];
			$programIds.="'".$row[csf('progran_no')]."'".",";

		}
		$programIds=chop($programIds,",");
	}



	$po_sql = sql_select("select d.id as po_id, b.booking_no from wo_po_break_down d, wo_booking_dtls b where d.id = b.po_break_down_id and d.status_active = 1 and b.status_active = 1 and b.booking_no like '%$bookingNo%' $search_field_cond");


	foreach ($po_sql as $val) 
	{
		$trans_po_arr[$val[csf("po_id")]] = $val[csf("po_id")];
	}

	$trans_po_arr = array_filter(array_unique($trans_po_arr));
	if(count($trans_po_arr)>0)
	{
		$all_po_nos = implode(",", $trans_po_arr);
		$all_po_cond=""; $poCond=""; 
		if($db_type==2 && count($trans_po_arr)>999)
		{
			$trans_po_arr_chunk=array_chunk($trans_po_arr,999) ;
			foreach($trans_po_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);	
				$poCond.=" c.po_breakdown_id in($chunk_arr_value) or ";	
			}
			
			$all_po_cond.=" and (".chop($poCond,'or ').")";	
		}
		else
		{
			$all_po_cond=" and c.po_breakdown_id in($all_po_nos)";	 
		}
	}



	if(!empty($program_arr)){
		//"query from knitting production with entry form 2 to get the barcodes";
		$barcode_arr=array();$barcodeAllNo="";
		$qry_roll_dtls=sql_select( "select b.barcode_no,b.booking_no from inv_receive_master a,pro_roll_details b where a.id=b.mst_id and a.receive_basis=2 and  a.booking_id in($programIds) and b.entry_form in(2) and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0");
		foreach ($qry_roll_dtls as $row) {

			$barcode_arr[$row[csf('booking_no')]]["barcode_no"]=$row[csf('barcode_no')];
			$barcodeAllNo.="'".$row[csf('barcode_no')]."'".",";

		}
		$barcodeAllNo=chop($barcodeAllNo,",");
	}
	
	if(!empty($barcode_arr)) $barcode_cond_for_booking=" and c.barcode_no in($barcodeAllNo)";

	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	if($store_id>0) $store_cond=" and a.store_id!=$store_id"; else $store_cond="";


	/*$sql= "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 $search_field_cond $barcode_cond $location_cond $store_cond $barcode_cond_for_booking

		union all
		  select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form
		 from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d 
		 where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(83,82) and c.entry_form in(83,82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0  $barcode_cond $all_po_cond";*/

		 $sql= "SELECT a.recv_number, a.location_id, b.prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down d 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.company_id=$company_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.is_sales <> 1 and c.re_transfer=0 $search_field_cond $barcode_cond $location_cond $store_cond $barcode_cond_for_booking

		union all
		  select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form
		 from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d 
		 where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(83) and c.entry_form in(83) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0  $barcode_cond $all_po_cond

		 union all
		  select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form
		 from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d 
		 where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(82) and c.entry_form in(82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 and a.transfer_criteria =1 and a.to_company = $company_id $barcode_cond $all_po_cond
		 union all
		  select a.transfer_system_id as recv_number, null as location_id, to_prod_id as prod_id, c.barcode_no, c.roll_no, c.qnty, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping, a.entry_form
		 from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c , wo_po_break_down d 
		 where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form in(82) and c.entry_form in(82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.re_transfer=0 and a.transfer_criteria =2 and a.company_id = $company_id  $barcode_cond $all_po_cond
		 ";


	// echo $sql;die;
	$result = sql_select($sql);
	?>
    <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table">
        <thead>
            <th width="30">SL</th>
            <th width="150">Fabric Description</th>
            <th width="100">Job No</th>
            <th width="110">Order No</th>
            <th width="110">Location</th>
            <th width="70">File NO</th>
            <th width="70">Ref No</th>
            <th width="70">Shipment Date</th>
            <th width="90">Barcode No</th>
            <th width="50">Roll No</th>
            <th>Roll Qty.</th>
        </thead>
	</table>
	<div style="width:960px; max-height:210px; overflow-y:scroll" id="list_container_batch" align="left">	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940" class="rpt_table" id="tbl_list_search">  
        <?
            $i=1;
            foreach ($result as $row)
            {  
				if($scanned_barcode_arr[$row[csf('barcode_no')]]=="")
				{
					$trans_flag = "";
					if($row[csf('entry_form')] == 82 || $row[csf('entry_form')] == 83)
					{
						$trans_flag = " (T)";
					}
					
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>)"> 
						<td width="30" align="center">
							<? echo $i; ?>
							 <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
						</td>
						<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
						<td width="100"><p><? echo $row[csf('job_no_mst')]; ?></p></td>
						<td width="110"><p><? echo $row[csf('po_number')]; ?></p></td>
                        <td width="110" align="center"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('file_no')]; ?></td>
                        <td width="70" align="center"><? echo $row[csf('grouping')]; ?></td>
						<td width="70" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
						<td width="90"><p><? echo $row[csf('barcode_no')].$trans_flag; ?>&nbsp;</p></td>
						<td width="50" align="center"><? echo $row[csf('roll_no')]; ?></td>
						<td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
					</tr>
				<?
					$i++;
				}
			}
        	?>
        </table>
    </div>
    <table width="940">
        <tr>
            <td align="center" >
                <input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
            </td>
        </tr>
    </table>
	<?	
	exit();
}

if($action=="populate_barcode_data")
{
	$data=explode("**",$data);
	$bar_code=$data[0];
	$sys_id=$data[1];

	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');	
	$color_from_library=return_library_array( "select id, color_name from lib_color",'id','color_name');	
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');
	$trans_arr=return_library_array("select barcode_no, mst_id from pro_roll_details where entry_form=2", "barcode_no", "mst_id");
	$product_arr=return_library_array( "select id, product_name_details from product_details_master where item_category_id=13",'id','product_name_details');
	$issue_roll_mst_arr=return_library_array( "select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b  where a.mst_id=b.id and a.entry_form=61 and a.barcode_no in($bar_code)",'barcode_no','issue_number');

	$scanned_barcode_issue_data=sql_select("select a.id, a.barcode_no,a.entry_form, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and a.entry_form =61 and a.is_returned=0 and a.status_active=1 and a.is_deleted=0 and a.barcode_no in ($bar_code)");

	foreach($scanned_barcode_issue_data as $row)
	{
		$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
		$issue_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('issue_number')];
	}

	$scanned_barcode_update_data=sql_select("select a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.to_store, b.to_prod_id, b.from_prod_id from pro_roll_details a, inv_item_transfer_dtls b  where a.dtls_id=b.id and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id");

	if($sys_id != "")
	{
		$scanned_barcode_update_data=sql_select("select  a.barcode_no, a.roll_id, c.transfer_system_id, a.entry_form from pro_roll_details a, inv_item_transfer_dtls b, inv_item_transfer_mst c where a.dtls_id=b.id and b.mst_id = c.id and a.mst_id = c.id and c.entry_form = 82 and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id");
		foreach($scanned_barcode_update_data as $row)
		{
			$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			$scanned_barcode_entry_form_array[$row[csf('barcode_no')]]=$row[csf('entry_form')];
			$transfer_roll_mst_arr[$row[csf('barcode_no')]] = $row[csf('transfer_system_id')];
		}
	}
	

	$order_to_order_trans_sql=sql_select("select a.id, a.barcode_no, a.po_breakdown_id,a.entry_form,a.booking_without_order from pro_roll_details a where a.entry_form in(83,82,58) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0 and a.barcode_no in($bar_code)");
	$order_to_order_trans_data=array();
	foreach($order_to_order_trans_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"]=$row[csf("id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"]=$row[csf("entry_form")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"]=$row[csf("booking_without_order")];
	}
	unset($order_to_order_trans_sql);

	$trans_store_sql=sql_select("select a.company_id,a.to_company, a.transfer_criteria, to_prod_id as prod_id, c.barcode_no, c.entry_form, b.to_store
	from  inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c 
	where a.id = b.mst_id and a.entry_form=82 and b.id=c.dtls_id and c.entry_form in(82) 
	and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.is_sales <> 1 and c.barcode_no in($bar_code)
	order by c.barcode_no desc");

	foreach($trans_store_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"]=$row[csf("to_store")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["to_prod_id"]=$row[csf("prod_id")];

		if($row[csf("transfer_criteria")] == 1)
		{
			$order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("to_company")];
		}
		else
		{
			$order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"] = $row[csf("company_id")];
		}
	}

	unset($trans_store_sql);
	
	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.floor_id) as floor_id, max(b.room) as room, max(b.rack) as rack, max(b.self) as self, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id as roll_mst_id
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code) 
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty,c.amount, c.booking_without_order,c.id");



	if(count($data_array)>0)
	{
		foreach($data_array as $row)
		{
			if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
			{
				$po_id=$row[csf("po_breakdown_id")];
			}
			else
			{
				$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
			}


			if($row[csf("booking_without_order")]==1)
			{
				$non_order_booking_buyer_po_arr[$po_id] = $po_id;

			}
			else
			{
				$po_arr_book_booking_arr[$po_id] = $po_id;
			}


			if($order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"] == 1)
			{
				$non_order_booking_buyer_po_arr[$po_id] = $po_id;
			}
			else
			{
				$po_arr_book_booking_arr[$po_id] = $po_id;
			}


			if($row[csf("booking_without_order")]==1)
			{
				$non_order_booking_buyer_po_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			}
			else
			{
				$po_arr_book_booking_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
			}


			$color_id_ref_arr[$row[csf("color_id")]] = $row[csf("color_id")];

			$company_ids .= $row[csf("company_id")].",";
			$store_ids .= $row[csf("store_id")].",";
			$febric_description_ids .= $row[csf("febric_description_id")].",";
		}

		$company_ids = chop($company_ids,",");
		$store_ids = chop($store_ids,",");
		$febric_description_ids = chop($febric_description_ids,",");

		$production_basis_sql = sql_select("select a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id  from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 and a.barcode_no in ($bar_code)");
		foreach ($production_basis_sql as $val) 
		{
			$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

			if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
			{
				$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}
			
		}


		$company_name_array=return_library_array( "select id, company_name from  lib_company where status_active=1 and is_deleted=0 and id in($company_ids)",'id','company_name');
		$buyer_arr=return_library_array( "select id, short_name from lib_buyer where status_active=1 and is_deleted=0",'id','short_name');
		$store_arr=return_library_array( "select id, store_name from lib_store_location where status_active=1 and is_deleted=0 and id in($store_ids)",'id','store_name');

		$composition_arr=array(); 
		$constructtion_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id=b.mst_id and a.id in ($febric_description_ids)";

		$deter_data_array=sql_select($sql_deter);
		foreach( $deter_data_array as $row )
		{
			$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
			$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
		}
		unset($deter_data_array);

	}
	$po_id="";

	$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")",'id','buyer_id');

	if(count($non_order_booking_buyer_po_arr)>0)
	{
		$non_order_sql = sql_select("select id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")");
		foreach ($non_order_sql as  $val) 
		{
			$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
			$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}
	}


	$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);

	if(count($po_arr_book_booking_arr) >0 )
	{
		if(!empty($program_with_order_arr))
		{
			$book_booking_sql=sql_select("select a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) and a.po_break_down_id in (".implode(',', $po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

			foreach ($book_booking_sql as $val) 
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
			}
		}
		else
		{
			$book_booking_arr=return_library_array("select po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 and po_break_down_id in (".implode(",", $po_arr_book_booking_arr).") ",'po_break_down_id','booking_no');
		}

		$po_ref_data_array=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and a.status_active =1 and b.status_active=1 and b.id in (".implode(",", $po_arr_book_booking_arr).") ");

		$po_details_array=array();
		foreach($po_ref_data_array as $row)
		{
			$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
			$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
			$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
			$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
		}
		unset($po_ref_data_array);
	}



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

// ==================================================================== new add
	foreach($data_array as $val)
	{
		//if($issued_barcode_arr[$val[csf('barcode_no')]]=="")
		//{
			$all_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

			$body_color_type_arr[$val[csf("barcode_no")]]["body_part_id"] = $val[csf("body_part_id")];

		//}
	}

	$all_barcode_arr = array_filter($all_barcode_arr);
	if(count($all_barcode_arr)<1) {
		echo "Data Not Found";die;
	}

    if(count($all_barcode_arr)>0)
    {
	    $all_ref_barcode_nos = implode(",", $all_barcode_arr);
	    $all_ref_barcode_no=""; $barCond=""; 
	    if($db_type==2 && count($all_barcode_arr)>999)
	    {
	    	$ref_barcode_arr_chunk=array_chunk($all_barcode_arr,999) ;
	    	foreach($ref_barcode_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);	
	    		$barCond.="  barcode_no in($chunk_arr_value) or ";	
	    	}

	    	$all_ref_barcode_no.=" and (".chop($barCond,'or ').")";	
	    }
	    else
	    {
	    	$all_ref_barcode_no=" and barcode_no in($all_ref_barcode_nos)";	 
	    }

	    $issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 $all_ref_barcode_no and is_returned !=1 ","barcode_no", "barcode_no");

	    $delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 $all_ref_barcode_no", "barcode_num", "grey_sys_id");
	}

	$sql_product = sql_select("select c.entry_form, a.receive_basis, a.booking_id, c.barcode_no, a.booking_no, b.body_part_id,b.febric_description_id,b.gsm, b.width, c.po_breakdown_id
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form in(2,22)
		and c.entry_form in(2,22)  and c.status_active=1 and c.is_deleted=0  and c.is_sales <>1 and c.booking_without_order=0  $all_ref_barcode_no");
	foreach ($sql_product as $val)
	{
		if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 2)
		{
			$program_no_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
		}
		else if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 1)
		{
			$booking_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
		}
		else
		{
			$indepent_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
		}

	}


	$program_no_arr = array_filter($program_no_arr);
	$booking_id_arr = array_filter($booking_id_arr);
	$indepent_po_id_arr = array_filter($indepent_po_id_arr);

	if(count($program_no_arr)>0)
	{
		$planning_sql = sql_select("select  a.color_type_id,a.body_part_id,a.booking_no, b.id as program_no from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id = b.mst_id and b.id in (".implode(",", $program_no_arr).") and b.status_active = 1 and b.is_deleted= 0");

		foreach ($planning_sql as  $val)
		{
			$program_data[$val[csf("program_no")]]["body_part_id"] = $val[csf("body_part_id")];
			$program_data[$val[csf("program_no")]]["color_type_id"] = $val[csf("color_type_id")];
		}
	}

	if(count($booking_id_arr)>0 )
	{

		$color_type_sql = sql_select("select a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id ,c.lib_yarn_count_deter_id, c.gsm_weight from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=3 and a.id in (". implode(",", $booking_id_arr) .") and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id, c.gsm_weight");

		foreach ($color_type_sql as $row)
		{
			$color_type_array_precost[$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['color_type_id'] = $row[csf('color_type_id')];
		}
	}

	if(count($indepent_po_id_arr)>0)
	{

		$color_type_sql = sql_select("select a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id ,c.lib_yarn_count_deter_id, c.gsm_weight, b.po_break_down_id from wo_booking_mst a, wo_booking_dtls b,wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and a.company_id=3 and b.po_break_down_id in (". implode(",", $indepent_po_id_arr) .") and a.booking_type=1 and b.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, b.booking_no,b.dia_width,c.color_type_id,c.body_part_id, c.lib_yarn_count_deter_id, c.gsm_weight , b.po_break_down_id");
		foreach ($color_type_sql as $row)
		{
			$color_type_array_precost[$row[csf('po_break_down_id')]][$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]]['color_type_id'] = $row[csf('color_type_id')];
		}
	}


	foreach ($sql_product as $val)
	{

		if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 2)
		{
			$body_color_type_arr[$val[csf("barcode_no")]]["color_type_id"] = $program_data[$val[csf("booking_id")]]["color_type_id"];
		}
		else if($val[csf("entry_form")] == 2 && $val[csf("receive_basis")] == 1)
		{
			$body_color_type_arr[$val[csf("barcode_no")]]["color_type_id"] = $color_type_array_precost[$val[csf('booking_id')]][$val[csf('body_part_id')]][$val[csf('febric_description_id')]][$val[csf('gsm')]][$val[csf('width')]]['color_type_id'];
		}
		else
		{
			$body_color_type_arr[$val[csf("barcode_no")]]["color_type_id"] = $color_type_array_precost[$val[csf('po_breakdown_id')]][$val[csf('body_part_id')]][$val[csf('febric_description_id')]][$val[csf('gsm')]][$val[csf('width')]]['color_type_id'];
		}
	}
	// ====================================================================  new add end

	
	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		
		foreach($data_array as $row)
		{
			if($scanned_barcode_issue_array[$row[csf('barcode_no')]]=="")
			{
				if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==22 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
				{
					$receive_basis="Independent";
					$receive_basis_id=0;

				}
				else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==2)) 
				{
					$receive_basis="Booking";
					$receive_basis_id=2;
				}
				else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
				{
					$receive_basis="Knitting Plan";
					$receive_basis_id=3;
				}
				else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==1) 
				{
					$receive_basis="PI";
					$receive_basis_id=1;
				}
				else if($row[csf("entry_form")]==58) 
				{
					$receive_basis="Delivery";
					$receive_basis_id=9;
				}
				
				if($row[csf("roll_id")]==0) 
				{
					$roll_id=$row[csf("roll_tbl_id")];
				}
				else
				{
					$roll_id=$row[csf("roll_id")];
				}
				
				$color='';
				$color_id=explode(",",$row[csf('color_id')]);
				foreach($color_id as $val)
				{
					if($val>0) $color.=$color_arr[$val].",";
				}
				$color=chop($color,',');
				if($row[csf("knitting_source")]==1)
				{
					$knitting_company_name=$company_name_array[$row[csf("knitting_company")]];
				}
				else if($row[csf("knitting_source")]==3)
				{
					$knitting_company_name=$supplier_arr[$row[csf("knitting_company")]];;
				}
				
				if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
				{
					$po_id=$row[csf("po_breakdown_id")];
					$roll_mst_id=$row[csf("roll_mst_id")];
					$entry_form=$row[csf("entry_form")];
					$booking_without_order="";
				}
				else
				{
					$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
					$roll_mst_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"];
					$entry_form=$order_to_order_trans_data[$row[csf("barcode_no")]]["entry_form"];
					$booking_without_order=$order_to_order_trans_data[$row[csf("barcode_no")]]["booking_without_order"];

				}

				if($entry_form == 82)
				{
					$to_store = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_store"];
				}
				else
				{
					$to_store = $row[csf("store_id")];
				}

				
				if($row[csf("booking_without_order")]==1)
				{
					//$buyer_name=$buyer_arr[$book_buyer_arr[$po_id]];
					//$booking_no_fab="";
					
					$booking_no_fab=$non_booking_arr[$row[csf("po_breakdown_id")]];
				}
				else
				{
					//$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
					//$booking_no_fab=$book_booking_arr[$po_id];

					if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
					{
						$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
						$booking_no_fab = $book_booking_arr_plan_wise[$row[csf("po_breakdown_id")]][$plan_id];
					}
					else
					{
						$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
					}
				}
				
				if($entry_form == 82 || $entry_form == 83)
				{
					$booking_no_fab = $booking_no_fab ." (T)";
					if($booking_without_order == 1)
					{
						$buyer_name=$buyer_arr[$book_buyer_arr[$po_id]];
					}
					else
					{
						$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
					}
				}
				else
				{
					if($row[csf("booking_without_order")]==1)
					{
						$buyer_name=$buyer_arr[$book_buyer_arr[$row[csf("po_breakdown_id")]]];
					}
					else
					{
						$buyer_name=$buyer_arr[$po_details_array[$row[csf("po_breakdown_id")]]['buyer_name']];
					}

				}
				
				
				$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];//entry_form
				
				$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];

				if($order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"])
				{
					$barcode_company_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_company"];
					$to_prod_id = $order_to_order_trans_data[$row[csf("barcode_no")]]["to_prod_id"];
				}
				else
				{
					$barcode_company_id = $row[csf("company_id")];
					$to_prod_id = $row[csf("prod_id")];
				}

				$ycount='';
				$count_id=explode(',',$row[csf('yarn_count')]);
				foreach($count_id as $count)
				{
					if ($ycount=='') $ycount=$count_arr[$count]; else $ycount.=",".$count_arr[$count];
				}
            	$color_string= "";
            	foreach (explode(",",$row[csf('color_id')]) as $val) {
            		$color_string .= $color_from_library[$val].",";
            	}
            	$color_name=chop($color_string,",");

            	$transRollId=$row[csf('roll_id')];
				$program_no='';
				if($row[csf('entry_form')]==2)
				{
					if($row[csf('receive_basis')]==2) $program_no=$row[csf('booking_id')];
				}
				else if($row[csf('entry_form')]==58)
				{
					$program_no=$programArr[$delv_arr[$row[csf('barcode_no')]]];
					$row[csf('roll_id')]=$row[csf('roll_id_prev')];
				}
				else if($row[csf('entry_form')]==83)
				{
					$program_no=$programArr[$trans_arr[$row[csf('barcode_no')]]];
					$row[csf('roll_id')]=$row[csf('roll_id_prev')];
				}
				else if($row[csf('entry_form')]==22)
				{
					$program_no=$row[csf('booking_no')];
				}

				$barcodeData=$row[csf('id')]."**".$barcode_company_id."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$to_store."**".$row[csf("knitting_company")]."**".$row[csf("yarn_count")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$to_prod_id."**".$roll_id."**".$po_id."**".$po_details_array[$po_id]['job_no']."**".$po_details_array[$po_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$po_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$to_store]."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$booking_without_order."**".$row[csf('color_id')]."**".$body_color_type_arr[$row[csf("barcode_no")]]["color_type_id"]."**".$row[csf("body_part_id")]."**".$row[csf('store_id')]."**".$transRollId."**".$row[csf("barcode_no")]."**".$row[csf("roll_no")]."**".$program_no."**".$row[csf('prod_id')]."**".$compsition_description."**".$ycount."**".$brand_arr[$row[csf('brand_id')]]."**".$row[csf("yarn_lot")]."**".$color_name."**".$color_type[$body_color_type_arr[$row[csf("barcode_no")]]["color_type_id"]]."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$row[csf("stitch_length")]."**".$row[csf("qnty")]."**".$row[csf("febric_description_id")]."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$row[csf("floor_id")]."**".$row[csf("room")]."**".$product_arr[$row[csf('prod_id')]];
			}
			else
			{
				if($scanned_barcode_entry_form_array[$row[csf('barcode_no')]]==82)
				{
					$barcodeData="-1**".$transfer_roll_mst_arr[$row[csf('barcode_no')]];
				}
				else
				{
					$barcodeData="-1**".$issue_roll_mst_arr[$row[csf('barcode_no')]];
				}
				
			}
			
			
		}
		echo trim($barcodeData);
	}
	else
	{
		echo "0";
	}
	exit();
}

if($action=="populate_barcode_data_update")
{
	$data=explode("**",$data);
	$bar_code=$data[0];
	$sys_id=$data[1];
	$company_name_array=return_library_array( "select id, company_name from  lib_company",'id','company_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	
	
	$composition_arr=array(); $constructtion_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	foreach( $data_array as $row )
	{
		$constructtion_arr[$row[csf('id')]]=$row[csf('construction')];
		$composition_arr[$row[csf('id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
	}
	unset($data_array);
	
	/*$inserted_roll=sql_select("select b.barcode_no from inv_item_transfer_dtls a,pro_roll_details b where a.id=b.dtls_id and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.entry_form=82");
	$inserted_roll_arr=array();
	foreach($inserted_roll as $inf)
	{
		$inserted_roll_arr[$inf[csf('barcode_no')]]=$inf[csf('barcode_no')];
	}
	
	$scanned_barcode_data=sql_select("select a.id, a.barcode_no, a.dtls_id, b.trans_id from pro_roll_details a, inv_item_transfer_dtls b where a.dtls_id=b.id and a.entry_form=82 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	foreach($scanned_barcode_data as $row)
	{
		$scanned_barcode_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
		$barcode_dtlsId_array[$row[csf('barcode_no')]]=$row[csf('dtls_id')];
		$barcode_trnasId_array[$row[csf('barcode_no')]]=$row[csf('trans_id')];
		$barcode_rollTableId_array[$row[csf('barcode_no')]]=$row[csf('id')];
	}*/
	
	/*$scanned_barcode_issue_data=sql_select("select a.id, a.barcode_no from pro_roll_details a where a.entry_form in(61,62,82) and a.status_active=1 and a.is_deleted=0 and  a.mst_id!=$sys_id");
	foreach($scanned_barcode_issue_data as $row)
	{
		$scanned_barcode_issue_array[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
	}*/

	/*$order_to_order_trans_sql=sql_select("select a.id, a.barcode_no, a.po_breakdown_id from pro_roll_details a where a.entry_form in(83) and a.status_active=1 and a.is_deleted=0 and a.re_transfer=0 and a.barcode_no in($bar_code)");
	$order_to_order_trans_data=array();
	foreach($order_to_order_trans_sql as $row)
	{
		$order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=$row[csf("barcode_no")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"]=$row[csf("po_breakdown_id")];
		$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"]=$row[csf("id")];
	}*/
	
	$scanned_barcode_update_data=sql_select("select c.company_id, a.id as roll_upid, a.po_breakdown_id, a.barcode_no, a.roll_id, a.dtls_id, b.trans_id, b.to_trans_id, b.from_trans_entry_form, b.from_store, b.to_store, b.to_prod_id, b.from_prod_id, b.from_order_id, b.from_booking_without_order from pro_roll_details a, inv_item_transfer_dtls b , inv_item_transfer_mst c  where a.dtls_id=b.id and b.mst_id = c.id and c.entry_form = 82 and a.entry_form in(82) and a.status_active=1 and a.is_deleted=0 and a.mst_id=$sys_id");
	foreach($scanned_barcode_update_data as $row)
	{
		$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']=$row[csf('roll_upid')];
		$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']=$row[csf('dtls_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['trans_id']=$row[csf('trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']=$row[csf('to_trans_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']=$row[csf('po_breakdown_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form']=$row[csf('from_trans_entry_form')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_store']=$row[csf('to_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_store']=$row[csf('from_store')];
		$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']=$row[csf('to_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']=$row[csf('from_prod_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_order_id']=$row[csf('from_order_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['company_id']=$row[csf('company_id')];
		$barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order']=$row[csf('from_booking_without_order')];

		if($row[csf('from_booking_without_order')] == 1)
		{
			$non_order_booking_buyer_po_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
		else
		{
			$po_arr_book_booking_arr[$row[csf('from_order_id')]] = $row[csf('from_order_id')];
		}
	}

	/*$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, b.id as dtls_id, b.prod_id, b.body_part_id, b.febric_description_id, b.machine_no_id, b.gsm, b.width, b.color_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.rack, b.self, c.barcode_no, c.id as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty 
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)");*/
	
	$data_array=sql_select("SELECT a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, max(b.id) as dtls_id, max(b.prod_id) as prod_id, max(b.body_part_id) as body_part_id, max(b.febric_description_id) as febric_description_id, max(b.machine_no_id) as machine_no_id, max(b.gsm) as gsm, max(b.width) as width, max(b.color_id) as color_id, max(b.yarn_lot) as yarn_lot, max(b.yarn_count) as yarn_count, max(b.stitch_length) as stitch_length, max(b.brand_id) as brand_id, max(b.rack) as rack, max(b.self) as self, c.barcode_no, max(c.id) as roll_tbl_id, c.roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id as roll_mst_id
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($bar_code)
	group by  a.id, a.entry_form, a.company_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.store_id, c.roll_id, c.roll_no, c.barcode_no, c.po_breakdown_id, c.qnty, c.amount, c.booking_without_order,c.id");
	
	if(count($data_array)>0)
	{
		
		foreach($data_array as $val)
		{
			$splitted_barcode_arr[$val[csf("barcode_no")]] = $val[csf("barcode_no")];

			if($val[csf("booking_without_order")] == 1 )
			{
				$non_order_booking_buyer_po_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}else{
				$po_arr_book_booking_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
			}


			/*if($val[csf("receive_basis")] == 2){
				$program_id_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}*/
		}

		$splited_barcode = implode(',',array_filter($splitted_barcode_arr));
		$nxProcessedBarcode = array();
		if($splited_barcode)
		{
			$nxtProcessSql = sql_select("select a.id,a.barcode_no,a.roll_no from  pro_roll_details a where a.barcode_no in (".$splited_barcode.") and a.entry_form in (61) and a.status_active=1 and a.is_deleted=0 and a.is_returned!=1");
			foreach ($nxtProcessSql as $val2) 
			{
				$nxProcessedBarcode[$val2[csf("barcode_no")]] = $val2[csf("barcode_no")];
			}
			//print_r($nxProcessedBarcode);
			

			$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($splited_barcode)");

			foreach($splited_roll_sql as $bar)
			{ 
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($splited_barcode) and entry_form = 82 order by barcode_no");
			foreach($child_split_sql as $bar)
			{ 
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}

			//print_r($splited_roll_ref);die;

		}

		$production_basis_sql = sql_select("select a.barcode_no, b.receive_basis, a.booking_without_order, b.booking_id  from pro_roll_details a, inv_receive_master b where a.mst_id = b.id and b.entry_form = 2 and a.entry_form = 2 and a.status_active =1 and a.barcode_no in ($bar_code)");
		foreach ($production_basis_sql as $val) 
		{
			$production_basis_arr[$val[csf("barcode_no")]]["receive_basis"] = $val[csf("receive_basis")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_without_order"] = $val[csf("booking_without_order")];
			$production_basis_arr[$val[csf("barcode_no")]]["booking_id"] = $val[csf("booking_id")];

			if($val[csf("receive_basis")] ==2 && $val[csf("booking_without_order")] ==0)
			{
				$program_with_order_arr[$val[csf("booking_id")]] = $val[csf("booking_id")];
			}
			
		}
	}
	
	$po_data_sql=sql_select("SELECT a.job_no, a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, a.style_ref_no, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst");
	$po_details_array=array();
	foreach($po_data_sql as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no']=$row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['buyer_name']=$row[csf("buyer_name")];
		$po_details_array[$row[csf("po_id")]]['style_ref_no']=$row[csf("style_ref_no")];
		$po_details_array[$row[csf("po_id")]]['year']=date("Y",strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number']=$row[csf("po_number")];
	}

	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1",'id','buyer_id');

	$non_order_booking_buyer_po_arr = array_filter($non_order_booking_buyer_po_arr);
	//$book_buyer_arr=return_library_array("select id, buyer_id from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")",'id','buyer_id');

	if(count($non_order_booking_buyer_po_arr)>0)
	{
		$non_order_sql = sql_select("select id, buyer_id, booking_no from wo_non_ord_samp_booking_mst where is_deleted=0 and status_active=1 and id in (".implode(',', $non_order_booking_buyer_po_arr).")");
		foreach ($non_order_sql as  $val) 
		{
			$book_buyer_arr[$val[csf("id")]] = $val[csf("buyer_id")];
			$non_booking_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}
	}


	$po_arr_book_booking_arr = array_filter($po_arr_book_booking_arr);
	if(count($po_arr_book_booking_arr)>0)
	{
		if(!empty($program_with_order_arr))
		{
			$book_booking_sql=sql_select("select a.po_break_down_id, a.booking_no , c.id as plan_id from wo_booking_dtls a left join ppl_planning_info_entry_mst b on a.booking_no = b.booking_no left join  ppl_planning_info_entry_dtls c on b.id = c.mst_id and c.id in (".implode(",", $program_with_order_arr).") where  a.is_deleted=0 and a.status_active=1 and a.booking_type in (1,4) and a.po_break_down_id in (".implode(',', $po_arr_book_booking_arr).") group by a.po_break_down_id, a.booking_no ,c.id");

			foreach ($book_booking_sql as $val) 
			{
				$book_booking_arr_plan_wise[$val[csf("po_break_down_id")]][$val[csf("plan_id")]] = $val[csf("booking_no")];
			}
		}
		else
		{
			$book_booking_arr=return_library_array("select po_break_down_id, booking_no from wo_booking_dtls where is_deleted=0 and status_active=1 and booking_type=1 and po_break_down_id in (". implode(',', $po_arr_book_booking_arr) .")",'po_break_down_id','booking_no');
		}
	}

	$roll_details_array=array(); $barcode_array=array(); 
	if(count($data_array)>0)
	{
		
		foreach($data_array as $row)
		{
			if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==0) || ($row[csf("entry_form")]==22 && ($row[csf("receive_basis")]==4 || $row[csf("receive_basis")]==6)))
			{
				$receive_basis="Independent";
				$receive_basis_id=0;
			}
			else if(($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==1) || ($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==2)) 
			{
				$receive_basis="Booking";
				$receive_basis_id=2;
			}
			else if($row[csf("entry_form")]==2 && $row[csf("receive_basis")]==2) 
			{
				$receive_basis="Knitting Plan";
				$receive_basis_id=3;
			}
			else if($row[csf("entry_form")]==22 && $row[csf("receive_basis")]==1) 
			{
				$receive_basis="PI";
				$receive_basis_id=1;
			}
			else if($row[csf("entry_form")]==58) 
			{
				$receive_basis="Delivery";
				$receive_basis_id=9;
			}
			
			if($row[csf("roll_id")]==0) 
			{
				$roll_id=$row[csf("roll_tbl_id")];
			}
			else
			{
				$roll_id=$row[csf("roll_id")];
			}
			
			$color='';
			$color_id=explode(",",$row[csf('color_id')]);
			foreach($color_id as $val)
			{
				if($val>0) $color.=$color_arr[$val].",";
			}
			$color=chop($color,',');
			if($row[csf("knitting_source")]==1)
			{
				$knitting_company_name=$company_name_array[$row[csf("knitting_company")]];
			}
			else if($row[csf("knitting_source")]==3)
			{
				$knitting_company_name=$supplier_arr[$row[csf("knitting_company")]];;
			}
			
			
			
			
			if($order_to_order_trans_data[$row[csf("barcode_no")]]["barcode_no"]=="")
			{
				$po_id=$row[csf("po_breakdown_id")];
				$roll_mst_id= $row[csf("roll_mst_id")];
			}
			else
			{
				$po_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["po_breakdown_id"];
				$roll_mst_id=$order_to_order_trans_data[$row[csf("barcode_no")]]["roll_table_id"];
			}
			
			//echo $po_id;die;
			
			if($row[csf("booking_without_order")]==1)
			{
				//$buyer_name=$buyer_arr[$book_buyer_arr[$po_id]];
				//$booking_no_fab="";
				$booking_no_fab=$non_booking_arr[$row[csf("po_breakdown_id")]];
			}
			else
			{
				//$buyer_name=$buyer_arr[$po_details_array[$po_id]['buyer_name']];
				//$booking_no_fab=$book_booking_arr[$po_id];
				if($production_basis_arr[$row[csf('barcode_no')]]["receive_basis"] == 2 && $production_basis_arr[$row[csf('barcode_no')]]["booking_without_order"] == 0 )
				{
					$plan_id = $production_basis_arr[$row[csf('barcode_no')]]["booking_id"];
					$booking_no_fab = $book_booking_arr_plan_wise[$row[csf("po_breakdown_id")]][$plan_id];
				}
				else
				{
					$booking_no_fab=$book_booking_arr[$row[csf("po_breakdown_id")]];
				}


				
			}

			$from_order_id =  $barcode_update_data[$row[csf('barcode_no')]]['from_order_id'];
			$from_booking_without_order = $barcode_update_data[$row[csf('barcode_no')]]['from_booking_without_order'];

			if($from_booking_without_order == 1)
			{
				$buyer_name=$buyer_arr[$book_buyer_arr[$from_order_id]];
			}
			else
			{
				$buyer_name=$buyer_arr[$po_details_array[$from_order_id]['buyer_name']];
			}

			if($barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 82 || $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'] == 83)
			{
				$booking_no_fab = $booking_no_fab . " (T)";
			}
			
			$store_id = $barcode_update_data[$row[csf('barcode_no')]]['from_store'];
			$entry_form = $barcode_update_data[$row[csf('barcode_no')]]['from_trans_entry_form'];
			
			$roll_details_array[$row[csf("barcode_no")]]['brand_id']=$row[csf("brand_id")];
			
			$compsition_description=$constructtion_arr[$row[csf("febric_description_id")]]." ".$composition_arr[$row[csf("febric_description_id")]];
			
			$barcodeData.=$row[csf('id')]."**".$barcode_update_data[$row[csf('barcode_no')]]['company_id']."**".$body_part[$row[csf("body_part_id")]]."**".$receive_basis."**".$receive_basis_id."**".change_date_format($row[csf("receive_date")])."**".$row[csf("booking_no")]."**".$row[csf("booking_id")]."**".$color."**".$row[csf("knitting_source")]."**".$knitting_source[$row[csf("knitting_source")]]."**".$store_id."**".$row[csf("knitting_company")]."**".$row[csf("yarn_lot")]."**".$row[csf("yarn_count")]."**".$row[csf("stitch_length")]."**".$row[csf("rack")]."**".$row[csf("self")]."**".$knitting_company_name."**".$row[csf("dtls_id")]."**".$barcode_update_data[$row[csf('barcode_no')]]['to_prod_id']."**".$row[csf("febric_description_id")]."**".$compsition_description."**".$row[csf("gsm")]."**".$row[csf("width")]."**".$roll_id."**".$row[csf("roll_no")]."**".$from_order_id."**".$row[csf("qnty")]."**".$row[csf("barcode_no")]."**".$po_details_array[$from_order_id]['job_no']."**".$po_details_array[$from_order_id]['buyer_name']."**".$buyer_name."**".$po_details_array[$from_order_id]['po_number']."**".$row[csf("color_id")]."**".$store_arr[$store_id]."**".$row[csf("body_part_id")]."**".$row[csf("brand_id")]."**".$row[csf("machine_no_id")]."**".$entry_form."**".$row[csf("booking_without_order")]."**".$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']."**".$barcode_update_data[$row[csf('barcode_no')]]['dtls_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['trans_id']."**".$barcode_update_data[$row[csf('barcode_no')]]['to_trans_id']."**".$po_details_array[$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']]['po_number']."**".$barcode_update_data[$row[csf('barcode_no')]]['po_breakdown_id']."**".$nxProcessedBarcode[$row[csf("barcode_no")]]."**".$roll_mst_id."**".$booking_no_fab."**".$row[csf("amount")]."**".$barcode_update_data[$row[csf('barcode_no')]]['from_prod_id']."**".$from_booking_without_order."**".$splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."__";//$row[csf("roll_mst_id")]."__";
				
			 //$test_str .= $splited_roll_ref[$row[csf('barcode_no')]][$barcode_update_data[$row[csf('barcode_no')]]['roll_upid']]."__";
			
		}
		//echo $test_str;die;
		echo chop($barcodeData,"__");
	}
	else
	{
		echo "0";
	}
	exit();
}

//data save update delete here------------------------------//
if($action=="save_update_delete")
{	 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
        $max_recv_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (1,4,5)", "max_date");      
	if($max_recv_date != "")
        {    
            $max_recv_date = date("Y-m-d", strtotime($max_recv_date));
            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
            if ($transfer_date < $max_recv_date) 
            {
                echo "20**Transfer Date Can not Be Less Than Last Receive Date Of This Lot";
                die;
            }
        }
        
        $max_issue_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$cbo_item_desc and transaction_type in (2,3,6)", "max_date");      
	if($max_issue_date != "")
        {    
            $max_issue_date = date("Y-m-d", strtotime($max_issue_date));
            $transfer_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_transfer_date)));
            if ($transfer_date < $max_issue_date) 
            {
                echo "20**Transfer Date Can not Be Less Than Last Issue Date Of This Lot";
                die;
            }
        }
        
	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		// echo $total_row;die;
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }
		
		$transfer_recv_num=''; $transfer_update_id='';
		
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,"GFOTOTE",82,date("Y",time()),13 ));
					 
			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, from_store_id, to_store_id, item_category, location_id, to_location_id , remarks, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',"
                        .$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",82,".$cbo_transfer_criteria.",".$cbo_company_id_to.",".$txt_from_order_id.",".$txt_to_order_id.",".$cbo_store_name.",".$cbo_store_name_to.",".$cbo_item_category.",".$cbo_location.",".$cbo_location_to.",".$txt_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";            
			
			// echo "10**"."insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;

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
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, program_no, stitch_length,store_id, inserted_by, insert_date";

		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length,color_names, from_store, to_store, inserted_by, insert_date";

		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, amount, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, inserted_by, insert_date";

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";


		$field_array_prod_insert = "id, company_id, store_id, item_category_id, detarmination_id, item_description, product_name_details, unit_of_measure, avg_rate_per_unit, last_purchased_qnty, current_stock, stock_value, gsm, dia_width, inserted_by, insert_date";
		$field_array_prod_update = "current_stock*avg_rate_per_unit*stock_value*updated_by*update_date";
		$field_array_roll_update="is_transfer*updated_by*update_date";



		if(str_replace("'","",$cbo_transfer_criteria)==1) // Company to Company
		{
			$rollIds='';
			for($j=1;$j<=$total_row;$j++)
			{
				$barcodeNo="barcodeNo_".$j;
				$recvBasis="recvBasis_".$j;
				$progBookPiId="progBookPiId_".$j;
				$bookWithoutOrder="bookWithoutOrder_".$j;
				$rollNo="rollNo_".$j;
				$progId="progId_".$j;
				$productId="productId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$yarnLot="yarnLot_".$j;
				$yarnCount="yarnCount_".$j;
				$stichLn="stichLn_".$j;
				$brandId="brandId_".$j;
				$floorsId="floorsId_".$j;
				$roomHidd="roomHidd_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$febDescripId="febDescripId_".$j;
				$constructCompo="fabricDesc_".$j;
				$gsm="gsm_".$j;
				$diaWidth="diaWidth_".$j;
				$transRollId="transRollId_".$j;
				$colorName="colorNameId_".$j;
				$storeId="storeId_".$j;
				$rollAmount="rollAmount_".$j;

				$rollIds.=$$transRollId.",";

				$check_if_already_scanned = sql_select("select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and  a.entry_form=61 and a.is_returned!=1 and a.barcode_no = " .$$barcodeNo." and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");

				if($check_if_already_scanned[0][csf("barcode_no")]!="")
				{
					echo "20**Sorry! Barcode already Scanned. Challan No: ".$check_if_already_scanned[0][csf("issue_number")]." Barcode No ".$$barcodeNo;
					die;
				}

				$trans_check_sql = sql_select("select barcode_no,entry_form,po_breakdown_id, qnty from pro_roll_details where barcode_no =" .$$barcodeNo." and entry_form in ( 58,83,82) and re_transfer =0 and status_active = 1 and is_deleted = 0");

				if($trans_check_sql[0][csf("barcode_no")] !="")
				{
					foreach ($trans_check_sql as $val)
					{
						if( $val[csf("po_breakdown_id")]  !=  str_replace("'", "", $txt_from_order_id))
						{
							echo "20**Sorry! This barcode ". str_replace("'", "", $$barcodeNo) ." doesn't belong to this order ".$txt_from_order_no ."";
							die;
						}

						if( $val[csf("qnty")]  !=  str_replace("'", "", $$rollWgt))
						{
							echo "20**Sorry! current quantity does not match with original qnty. Barcode no: ". str_replace("'", "", $$barcodeNo) ."";
							die;
						}
					}
				}

				//echo "10**Failed.".$$rollWgt;die;
				//$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

				/*echo "select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id_to and item_category_id=13 and detarmination_id=".$$febDescripId." and gsm=".$$gsm." and dia_width=".$$diaWidth." and status_active=1 and is_deleted=0";die;*/

				$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where company_id=$cbo_company_id_to and item_category_id=13 and detarmination_id=".$$febDescripId." and gsm=".$$gsm." and dia_width=".$$diaWidth." and status_active=1 and is_deleted=0");

				if (count($row_prod) > 0 || $new_prod_ref_arr[$cbo_company_id_to."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**13"] != "") 
				{
					if(count($row_prod) > 0)
					{
           				$new_prod_id = $row_prod[0][csf('id')];
           				$product_id_update_parameter[$new_prod_id]['qnty']+=str_replace("'", "", $$rollWgt);
           				$product_id_update_parameter[$new_prod_id]['amount']+=str_replace("'", "", $$rollAmount);
           				$update_to_prod_id[$new_prod_id]=$new_prod_id;
					}
					else
					{
						$new_prod_id = $new_prod_ref_arr[$cbo_company_id_to."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**13"];
						$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**13"]+=$$rollWgt;
						$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**13"]+=$$rollAmount;
					}       				
               	}
               	else
               	{
               		$new_prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
               		$new_prod_ref_arr[$cbo_company_id_to."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**13"] = $new_prod_id;
               		$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**13"]+=$$rollWgt;
               		$product_id_insert_amount[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**13"]+=$$rollAmount;
               	}



				$amount = str_replace("'", "", $$rollAmount);
				if($amount > 0)
				{
					$roll_qnty = str_replace("'", "", $$rollWgt);
					$rate = $amount / $roll_qnty;
					$rate = number_format($rate,2,".","");
				}

				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",13,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$rate.",'".$amount."',".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$from_trans_id=$id_trans;
				//$id_trans=$id_trans+1;
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				// $to_trans_id=$id_trans;
				$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$new_prod_id.",13,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$rate.",'".$amount."',".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",'".$amount."',12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$colorName.",".$$storeId.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$txt_to_order_id.",82,".$$rollWgt.",'".$amount."',".$$rollNo.",".$$rollId.",".$$transRollId.",5,4,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$from_trans_id.",6,82,".$id_dtls.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//$id_prop=$id_prop+1;
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$id_trans.",5,82,".$id_dtls.",".$txt_to_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

               	/*
	               	if($txt_to_order_id=="") $toOrderIdRef=$txt_from_order_id; else $toOrderIdRef=$txt_to_order_id;

	               	if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id.",'".$$productId."',13,6,".$txt_transfer_date.",'".$$rollWgt."','".$cbo_store_name."','".$$brandId."','".$$rack."','".$$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					
					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",6,82,'".$dtls_id."','".$txt_from_order_id."','".$$productId."','".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					
					$from_trans_id=$transactionID;
					//$transactionID = $transactionID+1;
					$transactionID = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
					$to_trans_id=$transactionID;
					//$id_prop = $id_prop+1;
					$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
					if($data_array_trans!="") $data_array_trans.=",";
					$data_array_trans.="(".$transactionID.",".$id.",'".$$recvBasis."','".$$progBookPiId."',".$cbo_company_id_to.",".$new_prod_id.",13,5,".$txt_transfer_date.",'".$$rollWgt."',".$cbo_store_name_to.",'".$$brandId."','".$$rack."','".$$shelf."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					if($data_array_prop!="") $data_array_prop.= ",";
					$data_array_prop.="(".$id_prop.",".$transactionID.",5,82,'".$dtls_id."','".$toOrderIdRef."',".$new_prod_id.",'".$$rollWgt."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

					if($data_array_dtls!="") $data_array_dtls.=",";
					$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",'".$amount."',12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$colorName.",".$$storeId.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
					if($data_array_roll!="") $data_array_roll.= ",";
					$data_array_roll.="(".$id_roll.",'".$$barcodeNo."',".$id.",".$dtls_id.",".$cbo_company_id_to.",6,'".$toOrderIdRef."',82,'".$$rollWgt."','".$$rollNo."','".$$rollId."','".$$bookWithoutOrder."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					
		
					$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$from_trans_id."__".$to_trans_id."__".$id_roll.",";
					$prodData_array[$$productId]+=$$rollWgt;
					$prodData_amount_array[$$productId]+=$$rollAmount;
					$all_prod_id.=$$productId.",";
					$all_roll_id.=$$rollId.",";
				*/

				$barcodeNos.=$$barcodeNo."__".$dtls_id."__".$from_trans_id."__".$to_trans_id."__".$id_roll.",";
				$prodData_array[$$productId]+=$$rollWgt;
				$prodData_amount_array[$$productId]+=$$rollAmount;
				$all_prod_id.=$$productId.",";
				$all_roll_id.=$$rollId.",";

			}

			// print_r($update_to_prod_id);

			if(!empty($product_id_insert_parameter))
			{
				foreach ($product_id_insert_parameter as $key => $val) 
				{
					//$product_id_insert_parameter[$new_prod_id."**".$$febDescripId."**".$$gsm."**".$$diaWidth."**".$$constructCompo."**13"]+=$$rollWgt;
					$prod_description_arr = explode("**", $key);
					$prod_id = $prod_description_arr[0];
					$fabric_desc_id = $prod_description_arr[1];
					$txt_gsm = $prod_description_arr[2];
					$txt_width = $prod_description_arr[3];
					$cons_compo = $prod_description_arr[4];

					$roll_amount = $product_id_insert_amount[$key];

					$avg_rate_per_unit = $roll_amount/$val;


					$prod_name_dtls = trim($cons_compo) . ", " . trim($txt_gsm) . ", " . trim($txt_width);
                   	

					if($data_array_prod_insert!="") $data_array_prod_insert.=",";
                   	$data_array_prod_insert .= "(" . $prod_id . "," . $cbo_company_id_to . "," . $cbo_store_name_to . ",13," . $fabric_desc_id . ",'" . $cons_compo . "','" . $prod_name_dtls . "'," . "12" . "," . $avg_rate_per_unit . "," . $val . "," . $val . "," . $roll_amount . "," . $txt_gsm . ",'" . $txt_width . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";
					
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
					$avg_rate_per_unit = $stock_value/$stock_qnty;
			
					$prod_id_array[]=$row[csf('id')];
					$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$stock_qnty."'*'".$avg_rate_per_unit."'*'".$stock_value."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
				}
				unset($toProdIssueResult);
			}

			$all_prod_id_arr=implode(",",array_unique(explode(",",chop($all_prod_id,','))));
			$fromProdIssueResult=sql_select("select id, current_stock, avg_rate_per_unit, stock_value from product_details_master where id in($all_prod_id_arr) and company_id=$cbo_company_id");
			foreach($fromProdIssueResult as $row)
			{
				$issue_qty=$prodData_array[$row[csf('id')]];
				$issue_amount=$prodData_amount_array[$row[csf('id')]];

				$current_stock=$row[csf('current_stock')]-$issue_qty;
				$current_amount=$row[csf('stock_value')]-$issue_amount;
				$current_avg_rate=$row[csf('stock_value')]-$issue_amount;

				$prod_id_array[]=$row[csf('id')];
				$data_array_prod_update[$row[csf('id')]]=explode("*",("'".$current_stock."'*'".$current_avg_rate."'*'".$current_amount."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}
			
			$all_roll_id_arr=array_unique(explode(",",chop($all_roll_id,',')));
			foreach($all_roll_id_arr as $roll_id)
			{
				$roll_id_array[]=$roll_id;
				$data_array_roll_update[$roll_id]=explode("*",("5*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			}

		}
		else // Store to Store and Order to Order
		{
			$rollIds='';
			for($j=1;$j<=$total_row;$j++)
			{
				$barcodeNo="barcodeNo_".$j;
				$rollNo="rollNo_".$j;
				$progId="progId_".$j;
				$productId="productId_".$j;
				$rollId="rollId_".$j;
				$rollWgt="rollWgt_".$j;
				$yarnLot="yarnLot_".$j;
				$yarnCount="yarnCount_".$j;
				$stichLn="stichLn_".$j;
				$brandId="brandId_".$j;
				$floorsId="floorsId_".$j;
				$roomHidd="roomHidd_".$j;
				$rack="rack_".$j;
				$shelf="shelf_".$j;
				$febDescripId="febDescripId_".$j;
				$gsm="gsm_".$j;
				$diaWidth="diaWidth_".$j;
				$transRollId="transRollId_".$j;
				$colorName="colorNameId_".$j;
				$storeId="storeId_".$j;
				$rollAmount="rollAmount_".$j;

				$rollIds.=$$transRollId.",";

				$check_if_already_scanned = sql_select("select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and  a.entry_form=61 and a.is_returned!=1 and a.barcode_no = " .$$barcodeNo." and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");

				if($check_if_already_scanned[0][csf("barcode_no")]!="")
				{
					echo "20**Sorry! Barcode already Scanned. Challan No: ".$check_if_already_scanned[0][csf("issue_number")]." Barcode No ".$$barcodeNo;
					die;
				}

				$trans_check_sql = sql_select("select barcode_no,entry_form,po_breakdown_id, qnty from pro_roll_details where barcode_no =" .$$barcodeNo." and entry_form in ( 58,83, 82) and re_transfer =0 and status_active = 1 and is_deleted = 0");

				if($trans_check_sql[0][csf("barcode_no")] !="")
				{
					foreach ($trans_check_sql as $val)
					{
						if( $val[csf("po_breakdown_id")]  !=  str_replace("'", "", $txt_from_order_id))
						{
							echo "20**Sorry! This barcode ". str_replace("'", "", $$barcodeNo) ." doesn't belong to this order ".$txt_from_order_no ."";
							die;
						}

						if( $val[csf("qnty")]  !=  str_replace("'", "", $$rollWgt))
						{
							echo "20**Sorry! current quantity does not match with original qnty. Barcode no: ". str_replace("'", "", $$barcodeNo) ."";
							die;
						}
					}
				}

				//echo "10**Failed";die;

				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);


				$amount = str_replace("'", "", $$rollAmount);
				if($amount > 0)
				{
					$roll_qnty = str_replace("'", "", $$rollWgt);
					$rate = $amount / $roll_qnty;
					$rate = number_format($rate,2,".","");
				}

				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",13,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$rate.",'".$amount."',".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$from_trans_id=$id_trans;
				//$id_trans=$id_trans+1;
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$data_array_trans.=",(".$id_trans.",".$transfer_update_id.",".$cbo_company_id.",".$$productId.",13,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$rate.",'".$amount."',".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",'".$amount."',12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$colorName.",".$$storeId.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$txt_to_order_id.",82,".$$rollWgt.",'".$amount."',".$$rollNo.",".$$rollId.",".$$transRollId.",5,4,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$from_trans_id.",6,82,".$id_dtls.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
				//$id_prop=$id_prop+1;
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				$data_array_prop.=",(".$id_prop.",".$id_trans.",5,82,".$id_dtls.",".$txt_to_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				//$id_roll=$id_roll+1;
				//$id_prop=$id_prop+1;
				//$id_trans=$id_trans+1;
				//$id_dtls=$id_dtls+1;
			}
		}



		if(str_replace("'","",$cbo_transfer_criteria)==1)
		{
			$rollUpdate=execute_query(bulk_update_sql_statement( "pro_roll_details", "id", $field_array_roll_update, $data_array_roll_update, $roll_id_array ));
			if($rollUpdate) $flag=1; else $flag=0;

			if ($data_array_prod_insert != "")
			{
				$rID7=sql_insert("product_details_master",$field_array_prod_insert,$data_array_prod_insert,0);
				if($rID7) $flag=1; else $flag=0;
			}
			
			$prodUpdate=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_id_array ));
			if($prodUpdate) $flag=1; else $flag=0;
			
		}

		$rollIds=chop($rollIds,',');
		$rID4=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*4*1","id",$rollIds,0);
		if($flag==1)
		{
			if($rID4) $flag=1; else $flag=0;
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

		$rID2=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		//echo $flag;die;
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID5=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
		if($flag==1)
		{
			if($rID5) $flag=1; else $flag=0;
		}

		$rID6=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1)
		{
			if($rID6) $flag=1; else $flag=0;
		}

		//echo "10**".$rID.'=='.$rID2.'=='.$rID3.'=='.$rID4.'=='.$rID5.'=='.$rID6.'=='.$rID7.'=='.$prodUpdate;die;

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
         * List of fields that will not change/update on update button event
         * fields=> from_order_id*to_order_id*
         * data=> $txt_from_order_id."*".$txt_to_order_id."*".
         */
		$field_array_update="challan_no*transfer_date*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;*/
		
		$field_array_trans="prod_id*transaction_date*order_id*cons_uom*cons_quantity*cons_rate*cons_amount*rack*self*program_no*stitch_length*updated_by*update_date";
		$updateTransID_array=array();
		$update_trans_issue_id=str_replace("'","",$update_trans_issue_id);
		$update_trans_recv_id=str_replace("'","",$update_trans_recv_id); 
		 
		$rate=0; $amount=0;
		$updateTransID_array[]=$update_trans_issue_id; 
		$updateTransID_data[$update_trans_issue_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_from_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$txt_rack."*".$txt_shelf."*".$txt_form_prog."*".$stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		$updateTransID_array[]=$update_trans_recv_id; 
		$updateTransID_data[$update_trans_recv_id]=explode("*",("".$cbo_item_desc."*".$txt_transfer_date."*".$txt_to_order_id."*".$cbo_uom."*".$txt_transfer_qnty."*'".$rate."'*'".$amount."'*".$txt_torack."*".$txt_toshelf."*".$txt_to_prog."*".$stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
		
		/*$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans,$updateTransID_data,$updateTransID_array));
		if($flag==1) 
		{
			if($rID2) $flag=1; else $flag=0; 
		}*/
		
		$field_array_dtls="from_prod_id*transfer_qnty*roll*rate*transfer_value*uom*y_count*brand_id*yarn_lot*rack*shelf*to_rack*to_shelf*from_program*to_program*stitch_length*updated_by*update_date";
		$data_array_dtls=$cbo_item_desc."*".$txt_transfer_qnty."*".$txt_roll."*'".$rate."'*'".$amount."'*".$cbo_uom."*".$hid_ycount."*".$hid_ybrand."*".$txt_ylot."*".$txt_rack."*".$txt_shelf."*".$txt_torack."*".$txt_toshelf."*".$txt_form_prog."*".$txt_to_prog."*".$stitch_length."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		
		/*$rID3=sql_update("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,"id",$update_dtls_id,1);
		if($flag==1) 
		{
			if($rID3) $flag=1; else $flag=0; 
		}
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=13");
		{
			if($query) $flag=1; else $flag=0; 
		} */
		
		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		
		$data_array_prop="(".$id_prop.",".$update_trans_issue_id.",6,13,".$update_dtls_id.",".$txt_from_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
		//$id_prop=$id_prop+1;
		$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
		$data_array_prop.=",(".$id_prop.",".$update_trans_recv_id.",5,13,".$update_dtls_id.",".$txt_to_order_id.",".$cbo_item_desc.",".$txt_transfer_qnty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
		
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
		$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id=$update_dtls_id and entry_form=13");
		{
			if($query) $flag=1; else $flag=0; 
		} 
		//echo "insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		$rID4=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
		if($flag==1) 
		{
			if($rID4) $flag=1; else $flag=0; 
		} 
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
	//$job_arr = return_library_array("select b.id, a.job_no from wo_po_details_master a,","id","job_no");
	$po_arr = return_library_array("select id, po_number from wo_po_break_down","id","po_number");
	$qnty_arr = return_library_array("select id, po_quantity from wo_po_break_down","id","po_quantity");
	$buyer_arr = return_library_array("select id, buyer_name from wo_po_details_master","id","buyer_name");
	//$style_arr = return_library_array("select id, style_ref_no from wo_po_details_master","id","style_ref_no");
	$ship_date_arr = return_library_array("select id, pub_shipment_date from wo_po_break_down","id","pub_shipment_date");
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	
	$poDataArray=sql_select("select b.id,a.buyer_name,a.style_ref_no,a.job_no,b.po_number from  wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$data[0] and b.status_active=1 and b.is_deleted=0 ");// and a.season like '$txt_season'
		$job_array=array(); //$all_job_id='';
		foreach($poDataArray as $row)
		{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
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
            <td><strong>From ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$job_array[$dataArray[0][csf('from_order_id')]]['buyer']]; //$buyer_library[$buyer_arr[$dataArray[0][csf('from_order_id')]]]; ?></td>
        </tr>
        <tr>
            <td><strong>From Style Ref.:</strong></td> <td width="175px"><? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; //$style_arr ?></td>
            <td><strong>From Job No:</strong></td> <td width="175px"><? echo $job_array[$dataArray[0][csf('from_order_id')]]['job'];
			//$job_array[$row[csf('id')]]['job'];
			 ?></td>
            <td><strong>From Ship. Date:</strong></td> <td width="175px"><? echo change_date_format($ship_date_arr[$dataArray[0][csf('from_order_id')]]); ?></td>
        </tr>
        <tr>
            <td><strong>To order No:</strong></td> <td width="175px"><? echo $po_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To ord Qnty:</strong></td> <td width="175px"><? echo $qnty_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To ord Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$job_array[$dataArray[0][csf('to_order_id')]]['buyer']];//$buyer_library[$buyer_arr[$dataArray[0][csf('to_order_id')]]]; ?></td>
        </tr>
        <tr>
            <td><strong>To Style Ref.:</strong></td> <td width="175px"><? echo $job_array[$dataArray[0][csf('to_order_id')]]['style'];//$style_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
            <td><strong>To Job No:</strong></td> <td width="175px"><? echo $job_array[$dataArray[0][csf('to_order_id')]]['job']//$job_arr[$dataArray[0][csf('to_order_id')]]; ?></td>
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
