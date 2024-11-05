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
$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );

if ($action=="order_popup")
{
	echo load_html_head_contents("Order Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
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
			show_list_view ( document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_order_no').value+'_'+"<? echo $cbo_company_id; ?>"+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+'<? echo $type; ?>'+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+'<? echo $txt_from_order_id; ?>'+'_'+document.getElementById('cbo_status').value+'_'+'<? echo $colorType; ?>', 'create_po_search_list_view', 'search_div', 'grey_fabric_order_to_order_roll_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
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
							echo create_drop_down( "cbo_buyer_name", 150, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",$data[0] );
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
	$colorType = chop(implode(',', array_unique(explode(",",$data[13]))),',') ;


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

	if($type=="to") { 
		$orderIdOmitCond = "and b.id not in($fromOrderId)";
		$colorTypeCond = " and d.color_type in($colorType)"; // if From order color type and To order color type same
	}

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

	$sql_res = sql_select("SELECT a.job_no, $year_field, a.job_no_prefix_num,a.company_name, a.buyer_name, a.style_ref_no, a.job_quantity, b.id, b.po_number,b.po_quantity, b.pub_shipment_date as shipment_date, b.file_no, b.grouping as ref_no ,b.status_active, c.color_number_id, e.booking_no, d.color_type from wo_po_details_master a, wo_po_break_down b left join wo_booking_dtls d on b.id = d.po_break_down_id left join wo_booking_mst e on d.booking_no = e.booking_no and e.booking_type=1 and e.is_short=2 $bookin_cond, wo_po_color_size_breakdown c
	where a.job_no=b.job_no_mst and b.id = c.po_break_down_id and a.job_no = c.job_no_mst and a.buyer_name like '$buyer' and b.po_number like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 $status_cond $company_cond $shipment_date $str_cond $bookingCondId $year_cond $orderIdOmitCond $all_booking_po_arr_cond $colorTypeCond order by b.id, b.pub_shipment_date");

		foreach ($sql_res as $val)
		{
			$po_number_arr[$val[csf("job_no")]."*".$val[csf("year")]."*".$val[csf("job_no_prefix_num")]."*".$val[csf("company_name")]."*".$buyer_arr[$val[csf("buyer_name")]]."*".$val[csf("style_ref_no")]."*".$val[csf("job_quantity")]."*".$val[csf("file_no")]."*".$val[csf("ref_no")]."*".$val[csf("id")]."*".$val[csf("po_number")]."*".$val[csf("po_quantity")]."*".$val[csf("shipment_date")]."*".$val[csf("status_active")]."*".$val[csf("color_type")]] .= $color_library[$val[csf("color_number_id")]].",";

			$po_arr[$val[csf("id")]] = $val[csf("id")];

			$booking_no_arr[$val[csf("id")]] = $val[csf("booking_no")];
		}

		/*echo "<pre>";
		print_r($po_number_arr);die;*/

		$po_arr = array_filter($po_arr);

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


			$sql_rcv = sql_select("select a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, cast(b.rack as varchar(50)) as rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type,c.booking_no, b.color_id as color_names, b.body_part_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.is_transfer!=6 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 $all_po_id_cond_1 and c.is_sales <> 1
			union all
			select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, cast(b.to_rack as varchar(50)) as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type ,c.booking_no, b.color_names, 0 as body_part_id
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
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="js_set_value('<? echo $po_data[9]."**".$po_data[14];?>','<? echo $available_po_arr[$po_data[9]]?>','<? echo $type?>')" >
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
	$data=explode("**",$data);
	$po_id=$data[0];
	$which_order=$data[1];

	$data_array=sql_select("select a.job_no, a.buyer_name, a.style_ref_no, a.gmts_item_id, b.po_number, b.po_quantity, b.pub_shipment_date as shipment_date,b.grouping from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$po_id");
	if($db_type==0) $group_concat="group_concat( distinct b.booking_no) AS booking_no";
	else if($db_type==2)  $group_concat="listagg(cast(b.booking_no as varchar2(4000)),',') within group (order by b.booking_no) AS booking_no";

	$booking_nos=return_field_value("$group_concat","wo_booking_mst a, wo_booking_dtls b","a.booking_no=b.booking_no and b.po_break_down_id=$po_id and a.is_deleted=0 and b.booking_type in(1,4) and a.status_active=1 and b.is_deleted=0 and b.status_active=1","booking_no");
				//echo "select $group_concat from wo_booking_mst a, wo_booking_dtls b where  a.booking_no=b.booking_no and b.po_break_down_id=$po_id and a.is_deleted=0 and b.booking_type in(1,4) and a.status_active=1 and b.is_deleted=0 and b.status_active=1";

	foreach ($data_array as $row)
	{
		$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
		foreach($gmts_item_id as $item_id)
		{
			if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
		}
		$booking_noss=implode(",",array_unique(explode(",",$booking_nos)));
		echo "document.getElementById('txt_".$which_order."_order_id').value 			= '".$po_id."';\n";
		echo "document.getElementById('txt_".$which_order."_order_no').value 			= '".$row[csf("po_number")]."';\n";
		echo "document.getElementById('txt_".$which_order."_po_qnty').value 			= '".$row[csf("po_quantity")]."';\n";
		echo "document.getElementById('cbo_".$which_order."_buyer_name').value 			= '".$row[csf("buyer_name")]."';\n";
		echo "document.getElementById('txt_".$which_order."_style_ref').value 			= '".$row[csf("style_ref_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_job_no').value 				= '".$row[csf("job_no")]."';\n";
		echo "document.getElementById('txt_".$which_order."_booking_no').value 			= '".$booking_noss."';\n";
		echo "document.getElementById('txt_".$which_order."_internal_ref_no').value 	= '".$row[csf("grouping")]."';\n";
		echo "document.getElementById('txt_".$which_order."_gmts_item').value 			= '".$gmts_item."';\n";
		echo "document.getElementById('txt_".$which_order."_shipment_date').value 		= '".change_date_format($row[csf("shipment_date")])."';\n";
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

	$sql="select a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, cast(b.rack as varchar(100)) as rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type,c.booking_no, b.color_id as color_names, b.body_part_id ,a.store_id, c.amount
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data and c.is_sales <> 1 and c.booking_without_order = 0
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, cast(b.to_rack as varchar(100)) as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type ,c.booking_no, b.color_names, 0 as body_part_id ,b.to_store as store_id, c.amount
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83,82) and c.entry_form in(83,82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$data and c.is_sales <> 1 and c.booking_without_order = 0
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
	    $all_ref_barcode_no_2=""; $barCond_2=""; 
	    if($db_type==2 && count($all_barcode_arr)>999)
	    {
	    	$ref_barcode_arr_chunk=array_chunk($all_barcode_arr,999) ;
	    	foreach($ref_barcode_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);	
	    		$barCond.="  barcode_no in($chunk_arr_value) or ";
	    		$barCond_2.="  barcode_num in($chunk_arr_value) or ";		
	    	}

	    	$all_ref_barcode_no.=" and (".chop($barCond,'or ').")";	
	    	$all_ref_barcode_no_2.=" and (".chop($barCond_2,'or ').")";	
	    }
	    else
	    {
	    	$all_ref_barcode_no=" and barcode_no in($all_ref_barcode_nos)";	
	    	$all_ref_barcode_no_2=" and barcode_num in($all_ref_barcode_nos)"; 
	    }

	    $issued_barcode_arr=return_library_array("select barcode_no from pro_roll_details where entry_form=61 and status_active=1 and is_deleted=0 $all_ref_barcode_no and is_returned !=1 ","barcode_no", "barcode_no");

	    $delv_arr=return_library_array("select barcode_num, grey_sys_id from pro_grey_prod_delivery_dtls where entry_form=56 $all_ref_barcode_no_2", "barcode_num", "grey_sys_id");
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

		?>
			<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
				<td width="40" align="center" valign="middle"><input type="checkbox" id="tbl_<? echo $i;?>" name="check[]" onClick="show_selected_total('<? echo $i;?>')"/></td>
				<td width="40"><? echo $i; ?></td>
				<td width="80"><? echo $row[csf('barcode_no')]; ?></td>
				<td width="50"><? echo $row[csf('roll_no')]; ?></td>
				<td width="70"><p><? echo $program_no; ?>&nbsp;</p></td>
				<td width="60"><p><? echo $row[csf('prod_id')]; ?></p></td>
				<td width="180"><p><? echo $product_arr[$row[csf('prod_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $ycount; ?>&nbsp;</p></td>
				<td width="70"><p><? echo $brand_arr[$row[csf('brand_id')]]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('yarn_lot')]; ?>&nbsp;</p></td>
				<td align="center" width="80"><p>
                	<?
                	$color_string= "";
                	foreach (explode(",",$row[csf('color_names')]) as $val) {
                		$color_string .= $color_library[$val].",";
                	}
                		echo chop($color_string,",");
                	?>&nbsp;</p>
                </td>
                <td width="80"><? echo $color_type[$body_color_type_arr[$row[csf("barcode_no")]]["color_type_id"]];?></td>
                <td width="100"><? echo $body_part[$body_color_type_arr[$row[csf("barcode_no")]]["body_part_id"]];?></td>

				<td width="55"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td width="80" align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
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
                    <input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdFrom[]" id="transIdFrom_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transIdTo[]" id="transIdTo_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="rolltableId[]" id="rolltableId_<? echo $i; ?>" value=""/>
                    <input type="hidden" name="transRollId[]" id="transRollId_<? echo $i; ?>" value="<? echo $transRollId ?>"/>
                    <input type="hidden" name="colorName[]" id="colorName_<? echo $i; ?>" value="<? echo $row[csf('color_names')]; ?>"/>
                    <input type="hidden" name="colorType[]" id="colorType_<? echo $i; ?>" value="<? echo $body_color_type_arr[$row[csf("barcode_no")]]["color_type_id"]; ?>"/>
                    <input type="hidden" name="bodeyPart[]" id="bodyPart_<? echo $i; ?>" value="<? echo $body_color_type_arr[$row[csf("barcode_no")]]["body_part_id"]; ?>"/>
                    <input type="hidden" name="storeId[]" id="storeId_<? echo $i; ?>" value="<? echo $row[csf('store_id')]; ?>"/>
                    <input type="hidden" name="rollAmount[]" id="rollAmount_<? echo $i; ?>" value="<? echo $row[csf('amount')]; ?>"/>
                </td>

			</tr>
		<?
			$i++;
		}
	}
	exit();
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

	/*$sql="select a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$order_id
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 2 as type
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$order_id
	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id
	order by barcode_no";*/

	/*$sql="select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id
	order by barcode_no";*/

	$sql="select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type , b.color_names, b.to_store as store_id, c.amount
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83) and c.entry_form in(83) and c.status_active=1 and c.is_deleted=0 and c.mst_id=$mst_id

	union all
	select a.id, a.entry_form, a.receive_basis, a.booking_id, b.prod_id, b.yarn_lot, b.yarn_count, b.stitch_length, b.brand_id, b.rack, b.self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 1 as type ,  b.color_id as color_names, a.store_id,c.amount
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and b.trans_id<>0 and a.entry_form in(2,22,58) and c.entry_form in(2,22,58) and c.is_transfer!=6 and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$order_id

	union all
	select a.id, a.entry_form, 0 as receive_basis, 0 as booking_id, b.from_prod_id as prod_id, b.yarn_lot, b.y_count as yarn_count, b.stitch_length, b.brand_id, b.to_rack as rack, b.to_shelf as self, c.id as roll_id, c.barcode_no, c.po_breakdown_id, c.roll_no, c.qnty, c.roll_id as roll_id_prev, 3 as type, b.color_names, b.to_store as store_id, c.amount
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form in(83,82) and c.entry_form in(83,82) and c.re_transfer=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id=$order_id and c.is_sales <> 1

	order by barcode_no ";

	//echo $sql;
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
				<td width="55"><p><? echo $row[csf('rack')]; ?>&nbsp;</p></td>
				<td width="55"><p><? echo $row[csf('self')]; ?>&nbsp;</p></td>
				<td width="80"><p><? echo $row[csf('stitch_length')]; ?>&nbsp;</p></td>
				<td width="80" align="right" style="padding-right:2px"><? echo $row[csf('qnty')]; ?>
                	<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
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
                </td>
			</tr>
		<?
			$i++;
		}
	}
	exit();
}

if($action=="populate_data_about_order")
{
	$data=explode("**",$data);
	$order_id=$data[0];
	$prod_id=$data[1];

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
                        <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>, 'create_transfer_search_list_view', 'search_div', 'grey_fabric_order_to_order_roll_transfer_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
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

	if($search_by==1)
		$search_field="transfer_system_id";
	else
		$search_field="challan_no";

	if($db_type==0) $year_field="YEAR(insert_date) as year,";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year,";
	else $year_field="";//defined Later

 	$sql="select id, transfer_prefix_number, $year_field transfer_system_id, challan_no, company_id, transfer_date, transfer_criteria, item_category from inv_item_transfer_mst where item_category=13 and company_id=$company_id and $search_field like '$search_string' and transfer_criteria=4 and entry_form=83 and status_active=1 and is_deleted=0 order by id";

	$arr=array(3=>$company_arr,5=>$item_transfer_criteria,6=>$item_category);

	echo  create_list_view("tbl_list_search", "Transfer ID,Year,Challan No,Company,Transfer Date,Transfer Criteria,Item Category", "80,70,100,80,90,130","750","250",0, $sql, "js_set_value", "id", "", 1, "0,0,0,company_id,0,transfer_criteria,item_category", $arr, "transfer_prefix_number,year,challan_no,company_id,transfer_date,transfer_criteria,item_category", '','','0,0,0,0,3,0,0');

	exit();
}

if($action=='populate_data_from_transfer_master')
{
	$data_array=sql_select("select transfer_system_id,challan_no, company_id, transfer_date, item_category, from_order_id, to_order_id from inv_item_transfer_mst where id='$data'");
	foreach ($data_array as $row)
	{
		echo "document.getElementById('update_id').value 					= '".$data."';\n";
		echo "document.getElementById('txt_system_id').value 				= '".$row[csf("transfer_system_id")]."';\n";
		echo "document.getElementById('cbo_company_id').value 				= '".$row[csf("company_id")]."';\n";
		echo "document.getElementById('txt_challan_no').value 				= '".$row[csf("challan_no")]."';\n";
		echo "document.getElementById('txt_transfer_date').value 			= '".change_date_format($row[csf("transfer_date")])."';\n";
		echo "get_php_form_data('".$row[csf("from_order_id")]."**from'".",'populate_data_from_order','requires/grey_fabric_order_to_order_roll_transfer_controller');\n";
		echo "get_php_form_data('".$row[csf("to_order_id")]."**to'".",'populate_data_from_order','requires/grey_fabric_order_to_order_roll_transfer_controller');\n";
		echo "$('#cbo_company_id').attr('disabled','disabled');\n";
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

	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		$transfer_recv_num=''; $transfer_update_id='';

		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			//$new_transfer_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', 'GFOTOTE', date("Y",time()), 5, "select transfer_prefix, transfer_prefix_number from inv_item_transfer_mst where company_id=$cbo_company_id and entry_form=83 and transfer_criteria=4 and item_category=13 and $year_cond=".date('Y',time())." order by id desc ", "transfer_prefix", "transfer_prefix_number" ));

			//$id=return_next_id( "id", "inv_item_transfer_mst", 1 ) ;
			$id = return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst", $con);
			$new_transfer_system_id = explode("*", return_next_id_by_sequence("INV_ITEM_TRANSFER_MST_PK_SEQ", "inv_item_transfer_mst",$con,1,$cbo_company_id,'GFOTOTE',83,date("Y",time()),13 ));

			$field_array="id, transfer_prefix, transfer_prefix_number, transfer_system_id, company_id, challan_no, transfer_date, entry_form, transfer_criteria, to_company, from_order_id, to_order_id, item_category, inserted_by, insert_date";

			$data_array="(".$id.",'".$new_transfer_system_id[1]."',".$new_transfer_system_id[2].",'".$new_transfer_system_id[0]."',".$cbo_company_id.",".$txt_challan_no.",".$txt_transfer_date.",83,4,0,".$txt_from_order_id.",".$txt_to_order_id.",13,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//echo "insert into inv_item_transfer_mst (".$field_array.") values ".$data_array;die;
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
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, program_no, stitch_length,store_id, inserted_by, insert_date";

		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length,color_names, from_store, to_store, inserted_by, insert_date";

		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, amount, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, inserted_by, insert_date";

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";

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
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$rollNo="rollNo_".$j;
			$transRollId="transRollId_".$j;
			$colorName="colorName_".$j;
			$storeId="storeId_".$j;
			$rollAmount="rollAmount_".$j;

			$rollIds.=$$transRollId.",";

			$check_if_already_scanned = sql_select("select a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and b.entry_form = 61 and  a.entry_form=61 and a.is_returned!=1 and a.barcode_no = " .$$barcodeNo." and a.status_active = 1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted=0");

			if($check_if_already_scanned[0][csf("barcode_no")]!="")
			{
				echo "20**Sorry! Barcode already Scanned. Challan No: ".$check_if_already_scanned[0][csf("issue_number")]." Barcode No ".$$barcodeNo;disconnect($con);
				die;
			}

			$trans_check_sql = sql_select("select barcode_no,entry_form,po_breakdown_id, qnty from pro_roll_details where barcode_no =" .$$barcodeNo." and entry_form in ( 58,83) and re_transfer =0 and status_active = 1 and is_deleted = 0");

			if($trans_check_sql[0][csf("barcode_no")] !="")
			{
				foreach ($trans_check_sql as $val)
				{
					if( $val[csf("po_breakdown_id")]  !=  str_replace("'", "", $txt_from_order_id))
					{
						echo "20**Sorry! This barcode ". str_replace("'", "", $$barcodeNo) ." doesn't belong to this order ".$txt_from_order_no ."";disconnect($con);
						die;
					}

					if( $val[csf("qnty")]  !=  str_replace("'", "", $$rollWgt))
					{
						echo "20**Sorry! current quantity does not match with original qnty. Barcode no: ". str_replace("'", "", $$barcodeNo) ."";disconnect($con);
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
			$data_array_dtls.="(".$id_dtls.",".$transfer_update_id.",".$from_trans_id.",".$id_trans.",".$$productId.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",'".$amount."',12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$colorName.",".$$storeId.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			if($data_array_roll!="") $data_array_roll.= ",";
			$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$transfer_update_id.",".$id_dtls.",".$txt_to_order_id.",83,".$$rollWgt.",'".$amount."',".$$rollNo.",".$$rollId.",".$$transRollId.",5,4,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$from_trans_id.",6,83,".$id_dtls.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			//$id_prop=$id_prop+1;
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.=",(".$id_prop.",".$id_trans.",5,83,".$id_dtls.",".$txt_to_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			//$id_roll=$id_roll+1;
			//$id_prop=$id_prop+1;
			//$id_trans=$id_trans+1;
			//$id_dtls=$id_dtls+1;
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

		/*	mysql_query("ROLLBACK");
		echo "5**".$flag;die;*/
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;
		//echo "10**".$rID2;die;
		//echo "insert into inv_item_transfer_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;

		$rID3=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
		if($flag==1)
		{
			if($rID3) $flag=1; else $flag=0;
		}

		$rollIds=chop($rollIds,',');
		$rID4=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*4*1","id",$rollIds,0);
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

		/*$all_trans_roll_id=chop($all_trans_roll_id,',');
		if($all_trans_roll_id!="")
		{
			$rID7=sql_multirow_update("pro_roll_details","re_transfer","1","id",$all_trans_roll_id,0);
			if($flag==1)
			{
				if($rID7) $flag=1; else $flag=0;
			}
		} */
		//echo $flag;die;
		//oci_rollback($con);
		//echo "5**".$flag;die;
		//echo "10**$rID##$rID2##$rID3##$rID4##$rID5##$rID6##$rID7"; oci_rollback($con); die;
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
         * List of fields that will not change/update on update event
         * fields => from_order_id*to_order_id*
         * data => $txt_from_order_id."*".$txt_to_order_id."*".
         */
		$field_array_update="challan_no*transfer_date*updated_by*update_date";
		$data_array_update=$txt_challan_no."*".$txt_transfer_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		$rate=0; $amount=0;
		//$id_trans=return_next_id( "id", "inv_transaction", 1 ) ;
		$field_array_trans="id, mst_id, company_id, prod_id, item_category, transaction_type, transaction_date, order_id, cons_uom, cons_quantity, cons_rate, cons_amount, rack, self, program_no, stitch_length, store_id, inserted_by, insert_date";
		$field_array_trans_update="prod_id*transaction_date*order_id*cons_quantity*cons_rate*cons_amount*rack*self*program_no*stitch_length*updated_by*update_date";

		//$id_dtls=return_next_id( "id", "inv_item_transfer_dtls", 1 ) ;
		$field_array_dtls="id, mst_id, trans_id, to_trans_id, from_prod_id, to_prod_id, item_category, transfer_qnty, roll, rate, transfer_value, uom, y_count, brand_id, yarn_lot, rack, shelf, to_rack, to_shelf, from_program, to_program, stitch_length, from_store, to_store, inserted_by, insert_date";
		$field_array_dtls_update="from_prod_id*to_prod_id*transfer_qnty*roll*rate*transfer_value*y_count*brand_id*yarn_lot*rack*shelf*to_rack*to_shelf*from_program*to_program*stitch_length*updated_by*update_date";

		//$id_roll = return_next_id( "id", "pro_roll_details", 1 );
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, amount, roll_no, roll_id, from_roll_id, is_transfer, transfer_criteria, inserted_by, insert_date";
		$field_array_updateroll="qnty*roll_no*updated_by*update_date";

		//$id_prop = return_next_id( "id", "order_wise_pro_details", 1 );
		$field_array_proportionate="id, trans_id, trans_type, entry_form, dtls_id, po_breakdown_id, prod_id, quantity, inserted_by, insert_date";
		$txt_deleted_barcode_no=str_replace("'", "", $txt_deleted_barcode_no);
		if($txt_deleted_barcode_no != "")
		{
			$issue_data_ref = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and a.barcode_no  in ($txt_deleted_barcode_no) and a.status_active = 1 and a.is_deleted = 0 and is_returned <> 1");
			if($issue_data_ref[0][csf("barcode_no")] != ""){
				echo "20**Sorry Barcode No ". $issue_data_ref[0][csf("barcode_no")] ." Found in Issue No ".$issue_data_ref[0][csf("issue_number")];disconnect($con);
				die;
			}

			$splited_roll_sql=sql_select("select barcode_no,split_from_id from pro_roll_split where status_active =1 and barcode_no in ($txt_deleted_barcode_no)");
			foreach($splited_roll_sql as $bar)
			{ 
				$splited_roll_ref[$bar[csf('barcode_no')]][$bar[csf('split_from_id')]]=$bar[csf('barcode_no')];
			}

			$child_split_sql=sql_select("select barcode_no, id from pro_roll_details where roll_split_from >0 and barcode_no in ($txt_deleted_barcode_no) and entry_form = 83 order by barcode_no");
			foreach($child_split_sql as $bar)
			{ 
				$child_split_arr[$bar[csf('barcode_no')]][$bar[csf('id')]]=$bar[csf('barcode_no')];
			}
		}

		for($i=1;$i<=$total_row;$i++)
		{
			$barcodeNum="barcodeNo_".$i;
			$BarcodeRolltableId="rolltableId_".$i;
			$barcodeWgt="rollWgt_".$i;

			$barcodeNumbers .=$$barcodeNum .",";
			$barcodeWgtArr[str_replace("'", "", $$barcodeNum)] = str_replace("'", "", $$barcodeWgt);
			$BarcodeRolltableIdArr[str_replace("'", "", $$barcodeNum)] = str_replace("'", "", $$BarcodeRolltableId);
		}

		//echo "10**="; print_r($BarcodeRolltableIdArr['20020001976'])."=<br>".print_r($BarcodeRolltableIdArr);die;

		$barcodeNumbers = chop($barcodeNumbers,",");

		if($barcodeNumbers !="")
		{
			$issue_data_ref = sql_select("select a.id, a.barcode_no, b.issue_number from pro_roll_details a, inv_issue_master b where a.mst_id = b.id and a.entry_form = 61 and a.barcode_no  in ($barcodeNumbers) and a.status_active = 1 and a.is_deleted = 0 and is_returned <> 1");
			if($issue_data_ref[0][csf("barcode_no")] != ""){
				echo "20**Sorry Barcode No ". $issue_data_ref[0][csf("barcode_no")] ." Found in Issue No ".$issue_data_ref[0][csf("issue_number")];disconnect($con);
				die;
			}


			$trans_check_sql = sql_select("select id, barcode_no, entry_form,po_breakdown_id, qnty from pro_roll_details where barcode_no in ($barcodeNumbers) and entry_form in ( 58,83) and re_transfer =0 and status_active = 1 and is_deleted = 0");

			if($trans_check_sql[0][csf("barcode_no")] !="")
			{
				foreach ($trans_check_sql as $val)
				{
					

					if($BarcodeRolltableIdArr[$val[csf("barcode_no")]])
					{
						//echo "10**M";
						//echo $BarcodeRolltableIdArr[$val[csf("barcode_no")]]."<br>";print_r($BarcodeRolltableIdArr);die;
						if( $val[csf("po_breakdown_id")]  !=  str_replace("'", "", $txt_to_order_id))
						{
							echo "20**Sorry! This barcode ". $val[csf("barcode_no")] ." doesn't belong this To Order ".$txt_to_order_no ."";disconnect($con);
							die;
						}
					}
					else
					{
						if( $val[csf("po_breakdown_id")]  !=  str_replace("'", "", $txt_from_order_id))
						{
							echo "20**Sorry! This barcode ". $val[csf("barcode_no")] ." doesn't belong this From Order ".$txt_from_order_no ."";disconnect($con);
							die;
						}
					}

					if( $val[csf("qnty")]  !=  $barcodeWgtArr[$val[csf("barcode_no")]])
					{
						echo "20**Sorry! current quantity does not match with original qnty. Barcode no: ". $val[csf("barcode_no")] ."";disconnect($con);
						die;
					}
				}
			}
		}

		//echo "10**";disconnect($con);die;


		$rollIds=''; $update_dtls_id='';
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
			$rack="rack_".$j;
			$shelf="shelf_".$j;
			$rollNo="rollNo_".$j;
			$dtlsId="dtlsId_".$j;
			$transIdFrom="transIdFrom_".$j;
			$transIdTo="transIdTo_".$j;
			$rolltableId="rolltableId_".$j;
			$transRollId="transRollId_".$j;
			$storeId="storeId_".$j;
			$rollAmount="rollAmount_".$j;


			$amount = str_replace("'", "", $$rollAmount);
			if($amount > 0)
			{
				$roll_qnty = str_replace("'", "", $$rollWgt);
				$rate = $amount / $roll_qnty;
				$rate = number_format($rate,2,".","");
			}


			if(str_replace("'","",$$rolltableId)>0)
			{
				$update_dtls_id.=str_replace("'","",$$dtlsId).",";

				$transId_arr[]=str_replace("'","",$$transIdFrom);
				$data_array_update_trans[str_replace("'","",$$transIdFrom)]=explode("*",($$productId."*".$txt_transfer_date."*".$txt_from_order_id."*".$$rollWgt."*".$rate."*".$amount."*".$$rack."*".$$shelf."*".$$progId."*".$$stichLn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$transId_arr[]=str_replace("'","",$$transIdTo);
				$data_array_update_trans[str_replace("'","",$$transIdTo)]=explode("*",($$productId."*".$txt_transfer_date."*".$txt_to_order_id."*".$$rollWgt."*".$rate."*".$amount."*".$$rack."*".$$shelf."*".$$progId."*".$$stichLn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$dtlsId_arr[]=str_replace("'","",$$dtlsId);
				$data_array_update_dtls[str_replace("'","",$$dtlsId)]=explode("*",($$productId."*".$$productId."*".$$rollWgt."*".$$rollNo."*".$rate."*".$amount."*".$$yarnCount."*".$$brandId."*".$$yarnLot."*".$$rack."*".$$shelf."*".$$rack."*".$$shelf."*".$$progId."*".$$progId."*".$$stichLn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$rollId_arr[]=str_replace("'","",$$rolltableId);
				$data_array_update_roll[str_replace("'","",$$rolltableId)]=explode("*",($$rollWgt."*".$$rollNo."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

				$dtlsIdProp=str_replace("'","",$$dtlsId);
				$transIdfromProp=str_replace("'","",$$transIdFrom);
				$transIdtoProp=str_replace("'","",$$transIdTo);
			}
			else
			{
				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$id_dtls = return_next_id_by_sequence("INV_ITEM_TRANSFER_DTLS_PK_SEQ", "inv_item_transfer_dtls", $con);
				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);


				$rollIds.=$$transRollId.",";
				if($data_array_trans!="") $data_array_trans.=",";
				$data_array_trans.="(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",13,6,".$txt_transfer_date.",".$txt_from_order_id.",12,".$$rollWgt.",".$rate.",'".$amount."',".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$transIdfromProp=$id_trans;

				$id_trans = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
				$transIdtoProp=$id_trans;
				$data_array_trans.=",(".$id_trans.",".$update_id.",".$cbo_company_id.",".$$productId.",13,5,".$txt_transfer_date.",".$txt_to_order_id.",12,".$$rollWgt.",".$rate.",'".$amount."',".$$rack.",".$$shelf.",".$$progId.",".$$stichLn.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_dtls!="") $data_array_dtls.=",";
				$data_array_dtls.="(".$id_dtls.",".$update_id.",".$transIdfromProp.",".$transIdtoProp.",".$$productId.",".$$productId.",13,".$$rollWgt.",".$$rollNo.",".$rate.",'".$amount."',12,".$$yarnCount.",".$$brandId.",".$$yarnLot.",".$$rack.",".$$shelf.",".$$rack.",".$$shelf.",".$$progId.",".$$progId.",".$$stichLn.",".$$storeId.",".$$storeId.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				if($data_array_roll!="") $data_array_roll.= ",";
				$data_array_roll.="(".$id_roll.",".$$barcodeNo.",".$update_id.",".$id_dtls.",".$txt_to_order_id.",83,".$$rollWgt.",'".$amount."',".$$rollNo.",".$$rollId.",".$$transRollId.",5,4,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$dtlsIdProp=$id_dtls;
				//$id_dtls=$id_dtls+1;
				$all_trans_roll_id.=$$transRollId.",";
			}
			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);

			if($data_array_prop!="") $data_array_prop.= ",";
			$data_array_prop.="(".$id_prop.",".$transIdfromProp.",6,83,".$dtlsIdProp.",".$txt_from_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
			$data_array_prop.=",(".$id_prop.",".$transIdtoProp.",5,83,".$dtlsIdProp.",".$txt_to_order_id.",".$$productId.",".$$rollWgt.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		}


		if($txt_deleted_id!="")
		{
			//echo "10**5**jahid";die;
			$deletedIds=explode(",",$txt_deleted_id); $dtlsIDDel=''; $transIDDel=''; $rollIDDel=''; $rollIDactive='';$delBarcodeNo='';
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

				if($splited_roll_ref[$delIds[5]][$delIds[3]] !="" ||$child_split_arr[$delIds[5]][$delIds[3]] !="")
				{
					echo "20**"."Split Found. barcode no: ".$delIds[5];
					disconnect($con);
					die;
				}
			}

			$prev_rol_id_sql=sql_select("select from_roll_id, re_transfer from pro_roll_details where id in($rollIDDel) and status_active=1");
			$prev_rol_id="";
			foreach($prev_rol_id_sql as $row)
			{
				$prev_rol_id.=$row[csf("from_roll_id")].",";
				if($row[csf("re_transfer")]==1)
				{
					echo "20**"."Next Transaction Found";disconnect($con);die;
				}
			}
			$prev_rol_id=implode(",",array_unique(explode(",",chop($prev_rol_id,","))));
			//echo "10**5##select from_roll_id from pro_roll_details where id in($rollIDDel)";die;
			$field_array_status="updated_by*update_date*status_active*is_deleted";
			$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$statusChangeTrans=sql_multirow_update("inv_transaction",$field_array_status,$data_array_status,"id",$transIDDel,0);
			$statusChangeDtls=sql_multirow_update("inv_item_transfer_dtls",$field_array_status,$data_array_status,"id",$dtlsIDDel,0);
			$statusChangeRoll=sql_multirow_update("pro_roll_details",$field_array_status,$data_array_status,"id",$rollIDDel,0);
			$activeRoll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$rollIDactive,0);
			$active_prev_roll=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","0*0*0","id",$prev_rol_id,0);

			if($flag==1)
			{
				if($statusChangeTrans && $statusChangeDtls && $statusChangeRoll && $activeRoll && $active_prev_roll) $flag=1; else $flag=0;
			}
		}

		$rID=sql_update("inv_item_transfer_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		if(count($data_array_update_roll)>0)
		{
			$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_update_trans,$transId_arr));
			if($flag==1)
			{
				if($rID2) $flag=1; else $flag=0;
			}
			//echo "10**".bulk_update_sql_statement("inv_item_transfer_dtls","id",$field_array_dtls_update,$data_array_update_dtls,$dtlsId_arr);die;
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

		if($data_array_dtls!="")
		{
			$rIDinv=sql_insert("inv_transaction",$field_array_trans,$data_array_trans,0);
			if($flag==1)
			{
				if($rIDinv) $flag=1; else $flag=0;
			}

			$rIDDtls=sql_insert("inv_item_transfer_dtls",$field_array_dtls,$data_array_dtls,0);
			if($flag==1)
			{
				if($rIDDtls) $flag=1; else $flag=0;
			}

			//echo $flag;die;
			//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rIDRoll=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,1);
			if($flag==1)
			{
				if($rIDRoll) $flag=1; else $flag=0;
			}
		}

		$rollIds=chop($rollIds,',');
		if($rollIds!="")
		{
			$rID5=sql_multirow_update("pro_roll_details","is_transfer*transfer_criteria*re_transfer","6*4*1","id",$rollIds,0);
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}
		}

		//echo "10**";die;

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
			$query = execute_query("DELETE FROM order_wise_pro_details WHERE dtls_id in(".$update_dtls_id.") and entry_form=83");
			if($flag==1)
			{
				if($query) $flag=1; else $flag=0;
			}
		}

		//echo "10** $rID2 && $rID3 && $rID4 && $rIDinv && $rIDDtls && $rIDRoll && $flag";oci_rollback($con);die;

		//echo "10**5**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;
		if($data_array_prop!="")
		{
			$rIDProp=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,1);
			if($flag==1)
			{
				if($rIDProp) $flag=1; else $flag=0;
			}
		}

		//echo "10**5**$flag";die;

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
	$product_arr = return_library_array("select id, product_name_details from product_details_master where item_category_id=13","id","product_name_details");
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$brand_arr=return_library_array( "select id, brand_name from lib_brand",'id','brand_name');

	$poDataArray=sql_select("select b.id,a.buyer_name,a.style_ref_no,a.job_no,b.po_number,b.pub_shipment_date, b.file_no, b.grouping as ref_no, (a.total_set_qnty*b.po_quantity) as qty from wo_po_break_down b,wo_po_details_master a where  a.job_no=b.job_no_mst and a.company_name=$data[0] and b.status_active=1 and b.is_deleted=0 ");
	$job_array=array(); //$all_job_id='';
	foreach($poDataArray as $row)
	{
		$job_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
		$job_array[$row[csf('id')]]['job']=$row[csf('job_no')];
		$job_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
		$job_array[$row[csf('id')]]['no']=$row[csf('po_number')];
		$job_array[$row[csf('id')]]['date']=$row[csf('pub_shipment_date')];
		$job_array[$row[csf('id')]]['qty']=$row[csf('qty')];
		$job_array[$row[csf('id')]]['file']=$row[csf('file_no')];
		$job_array[$row[csf('id')]]['ref']=$row[csf('ref_no')];
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
            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Report</u></strong></td>
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
                <table width="100%" cellspacing="0" align="right">
                 	<tr>
                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>From Order</u></td>
                    </tr>
                    <tr>
                    	<td width="100">Order No:</td>
                        <td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['no']; ?></td>
                        <td width="100">Quantity:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['qty']; ?></td>
                    </tr>
                    <tr>
                    	<td>Buyer:</td>
                        <td>&nbsp;<? echo $buyer_library[$job_array[$dataArray[0][csf('from_order_id')]]['buyer']]; ?></td>
                        <td>Job No:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['job']; ?></td>
                    </tr>
                    <tr>
                    	<td>File No:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['file']; ?></td>
                        <td>Ref. No:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['ref']; ?></td>
                    </tr>
                    <tr>
                    	<td>Style Ref:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('from_order_id')]]['style']; ?></td>
                        <td>Ship. Date:</td>
                        <td>&nbsp;<? echo change_date_format($job_array[$dataArray[0][csf('from_order_id')]]['date']); ?></td>
                    </tr>
                </table>
            </td>
            <td>
                <table width="100%" cellspacing="0" align="right">
                 	<tr>
                    	<td colspan="4" align="center" style="font-weight:bold; font-size:18px;"><u>To Order</u></td>
                    </tr>
                    <tr>
                    	<td width="100">Order No:</td>
                        <td width="120">&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['no']; ?></td>
                        <td width="100">Quantity:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['qty']; ?></td>
                    </tr>
                    <tr>
                    	<td>Buyer:</td>
                        <td>&nbsp;<? echo $buyer_library[$job_array[$dataArray[0][csf('to_order_id')]]['buyer']]; ?></td>
                        <td>Job No:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['job']; ?></td>
                    </tr>
                    <tr>
                    	<td>File No:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['file']; ?></td>
                        <td>Ref. No:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['ref']; ?></td>
                    </tr>
                    <tr>
                    	<td>Style Ref:</td>
                        <td>&nbsp;<? echo $job_array[$dataArray[0][csf('to_order_id')]]['style']; ?></td>
                        <td>Ship. Date:</td>
                        <td>&nbsp;<? echo change_date_format($job_array[$dataArray[0][csf('to_order_id')]]['date']); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
	<br>
    <div style="width:100%;">
        <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
            <thead bgcolor="#dddddd" align="center">
                <th width="30">SL</th>
                <th width="80">Barcode No</th>
                <th width="60">Roll No</th>
                <th width="180">Fabric Description</th>
                <th width="80">Y/Count</th>
                <th width="70">Y/Brand</th>
                <th width="80">Y/Lot</th>
                <th width="55">Rack</th>
                <th width="55">Shelf</th>
                <th width="70">Stitch Length</th>
                <th width="60">UOM</th>
                <th width="100">Transfered Qnty</th>
            </thead>
            <tbody>
			<?
            $sql_dtls="select a.from_prod_id, a.transfer_qnty, a.uom, a.y_count, a.brand_id, a.yarn_lot, a.to_rack, a.to_shelf, a.stitch_length, b.barcode_no, b.roll_no from inv_item_transfer_dtls a, pro_roll_details b where a.id=b.dtls_id and b.entry_form=83 and a.mst_id='$data[1]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by b.barcode_no";

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
                    <td colspan="11" align="right"><strong>Total </strong></td>
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
