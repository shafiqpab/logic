<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_name=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
$machine_arr=return_library_array( "select id, machine_no from lib_machine_name",'id','machine_no');
//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}


if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
	<script>
		// function js_set_value(booking_no)
		// {
		// 	document.getElementById('selected_booking').value=booking_no;
		// 	parent.emailwindow.hide();
		// }
		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;
		function change_caption(val)
		{
			if(val==1)
			{
				document.getElementById('type_caption').innerHTML = "FSO No";
				document.getElementById('date_caption').innerHTML = "FSO Date";
			}
			else if(val==2)
			{
				document.getElementById('type_caption').innerHTML = "Booking No";
				document.getElementById('date_caption').innerHTML = "Booking Date";
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon)
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];

			toggle(document.getElementById('tr_' + str), '#FFFFCC');

			if (jQuery.inArray(selectID, selected_id) == -1) {
				selected_id.push(selectID);
				selected_name.push(selectDESC);
				selected_no.push(str);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == selectID)
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_no.splice(i, 1);
			}
			var id = '';
			var name = '';
			var job = '';
			var num = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			num = num.substr(0, num.length - 1);
			$('#selected_booking_id').val(id);
			$('#selected_booking_no').val(name);
		}
	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="890" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
					<tr>
						<td align="center" width="100%">
							<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
								<thead>
									<th width="140">Company Name</th>
									<th width="140">Buyer Name</th>
									<th>Searching Type</th>
									<th id="type_caption" width="80">FSO No</th>
									<th width="180" id="date_caption">FSO Date</th>
									<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
									<input type="hidden" name="selected_booking_no" id="selected_booking_no" value="" />
									<input type="hidden" name="selected_booking_id" id="selected_booking_id" value="" />
								</thead>
								<tr>
									<td align="center">
										<?
											if($companyID != "" && $companyID != 0)
											{
												echo create_drop_down( "cbo_company_name", 140,"select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) order by comp.company_name","id,company_name", 0, "-- Select Company --", $companyID, "", 1 );
											}										
										?>
									</td>
									<td align="center">
										<?
										if($buyerID != "" && $buyerID != 0)
										{
											$selected = $buyerID;
										}
											echo create_drop_down( "cbo_buyer_name", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$companyID' and buy.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );
										?>
									</td>
									<td align="center">	
										<?
										$search_by_arr = array(1 => "FSO No", 2 => "Booking No");										
										echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", "change_caption(this.value)", 0);
										?>
									</td>     
									<td align="center" id="search_by_td">				
										<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
									</td>
									<td>
										<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
										<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
									</td>
									<td align="center">
										<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '_' + document.getElementById('cbo_buyer_name').value + '_' + document.getElementById('cbo_search_by').value + '_' +  document.getElementById('txt_search_common').value + '_' + document.getElementById('txt_date_from').value + '_' + document.getElementById('txt_date_to').value, 'create_booking_search_list_view', 'search_div', 'reference_wise_allocation_history_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td  align="center" height="40" valign="middle">
							<? 
							echo create_drop_down( "cbo_year_selection", 70, $year,"", 1, "-- Select --", date('Y'), "",0 );		
							echo load_month_buttons();  ?>
						</td>
					</tr>
					<tr>
						<td align="center"valign="top" id="search_div"></td>
					</tr>
				</table>    
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit(); 
}

if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	$company_id = $data[0];
	$buyer_id = $data[1];
	$cbo_search_by = $data[2];
	$txt_search_common = $data[3];
	$txt_date_from = $data[4];
	$txt_date_to = $data[5];
	$fso_cond = "";
	$booking_cond = "";
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", "id", "buyer_name");
	if($cbo_search_by == 1) //fso
	{
		if($buyer_id!= 0 && $buyer_id != "")
		{
			$fso_cond .= " and a.buyer_id = $buyer_id";
		}
		if($txt_search_common!= 0 && $txt_search_common != "")
		{
			$fso_cond .= " and a.job_no_prefix_num = $txt_search_common";
		}
		if($txt_date_from != "" &&  $txt_date_to != "")
		{
			$fso_cond .= "and a.booking_date between '" . change_date_format(trim($txt_date_from), '', '', 1) . "' and '" . change_date_format(trim($txt_date_to), '', '', 1) . "'";
		}			
	}
	else if($cbo_search_by == 2) //booking
	{
		if($buyer_id!= 0 && $buyer_id != "")
		{
			$booking_cond .= " and b.buyer_id = $buyer_id";
		}
		if($txt_search_common!= 0 && $txt_search_common != "")
		{
			$booking_cond .= " and b.BOOKING_NO_PREFIX_NUM = $txt_search_common";
		}
		if($txt_date_from != "" &&  $txt_date_to != "")
		{
			$booking_cond .= "and b.booking_date between '" . change_date_format(trim($txt_date_from), '', '', 1) . "' and '" . change_date_format(trim($txt_date_to), '', '', 1) . "'";
		}	
	}

	// if ($data[1] == 0) {
	// 	if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
	// 		if ($_SESSION['logic_erp']["buyer_id"] != "")
	// 			$buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
	// 		else
	// 			$buyer_id_cond = "";
	// 	}
	// 	else {
	// 		$buyer_id_cond = "";
	// 	}
	// } else {
	// 	$buyer_id_cond = " and a.buyer_id=$data[1]";
	// }

	// $search_by = $data[2];
	// $search_string = "%" . trim($data[3]) . "%";

	// if ($search_by == 1)
	// 	$search_field = "a.booking_no_prefix_num";
	// else
	// 	$search_field = "a.job_no";

	// $start_date = $data[4];
	// $end_date = $data[5];
	// $cbo_year = str_replace("'", "", $data[6]);
	// $year_cond = "";
	// if (trim($cbo_year) != 0) 
	// {
	// 	if ($db_type == 0)
	// 		$year_cond = " and YEAR(c.insert_date)=$cbo_year";
	// 	else if ($db_type == 2)
	// 		$year_cond = " and to_char(c.insert_date,'YYYY')=$cbo_year";
	// 	else
	// 		$year_cond = "";
	// }

	// if ($start_date != "" && $end_date != "") {
	// 	if ($db_type == 0) {
	// 		$date_cond = "and a.booking_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
	// 	} else {
	// 		$date_cond = "and b.booking_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
	// 	}
	// } else {
	// 	$date_cond = "";
	// }

	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	// $arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	$booking_type_arr = array(108 => "Partial Fabric Booking", 118 => "Main Fabric Booking", 88 => "Short Fabric Booking", 271 => "Partial fabric Booking", 86 => "Main Fabric Booking", 140 => "Sample Fabric Booking-Without order", 90 => "Sample Fabric Booking-Without order", 610 => "Sample Fabric Booking -Without order", 694=>"Sample Fabric Booking Without Order", 139 => "Sample Fabric Booking-With order");	
	if($cbo_search_by ==1) //fso
	{
		$sql = "select a.id, a.JOB_NO, a.SALES_BOOKING_NO as booking_no, a.STYLE_REF_NO, a.COMPANY_ID, a.BUYER_ID, a.BOOKING_TYPE, a.BOOKING_ENTRY_FORM, a.BOOKING_DATE as fso_date, b.BOOKING_DATE, a.booking_id from FABRIC_SALES_ORDER_MST a left join WO_BOOKING_MST b on a.SALES_BOOKING_NO = b.BOOKING_NO where a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and a.company_id = $company_id $fso_cond";
		// echo $sql;
	}	
	else if($cbo_search_by == 2) // booking
	{
		$sql = "SELECT a.id, a.JOB_NO, b.BOOKING_NO, a.STYLE_REF_NO, a.COMPANY_ID, b.BUYER_ID, b.BOOKING_TYPE, b.ENTRY_FORM as BOOKING_ENTRY_FORM, a.BOOKING_DATE  AS fso_date, b.BOOKING_DATE FROM WO_BOOKING_MST b LEFT JOIN FABRIC_SALES_ORDER_MST a ON a.SALES_BOOKING_NO = b.BOOKING_NO WHERE a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and b.company_id = $company_id $booking_cond";
		// echo $sql;
	}
	$sql_res = sql_select($sql);
	// echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','','');
	?>
    <table width="830" cellpadding="0" cellspacing="0" border="0" class="rpt_table" id="rpt_tablelist_view" rules="all">
		<thead>
			<tr>
				<th width="50">SL No</th>
				<th width="80">Buyer Style</th>
				<th width="120">Booking No</th>
				<th width="80">Booking Type</th>
				<th width="100">Booking Date</th>
				<th width="120">FSO No</th>
				<th width="100">FSO Date</th>
				<th width="80">Company</th>
				<th>Buyer</th>
			</tr>
		</thead>
	</table> 
    <div style="width:830px; overflow-y:scroll; min-height:70px; max-height:250px;" id="">
		<table align="left" width="830" height="" cellpadding="0" cellspacing="0" border="0" class="rpt_table" id="tbl_list_search" rules="all">
			<tbody>
                <?
                $i = 1;
                foreach ($sql_res as $item) { 
					$id_arr[] = $item['ID'];

					if ($i % 2 == 0) {
						$bgcolor = "#E9F3FF";
					} else {
						$bgcolor = "#FFFFFF";
					}
					$selectedString = "'".$i.'_'.$item['ID'].'_'.$item['JOB_NO']."'";	
				?>
				<tr onClick="js_set_value(<? echo $selectedString;?>)" bgcolor="<?=$bgcolor;?>" style="cursor:pointer" id="tr_<?=$i;?>">
					<td width="50"><?= $i; ?></td>
					<td align="left" width="80"><p><?=$item["STYLE_REF_NO"]; ?></p></td>
					<td align="left" width="120"><p><?=$item["BOOKING_NO"]; ?></p></td>
					<td align="left" width="80"><p><?=$booking_type_arr[$item["BOOKING_ENTRY_FORM"]]; ?></p></td>
					<td align="left" width="100"><p><?=$item["BOOKING_DATE"]; ?></p></td>
					<td align="left" width="120"><p><?=$item["JOB_NO"]; ?></p></td>
					<td align="left" width="100"><p><?=$item["FSO_DATE"]; ?></p></td>
					<td align="left" width="80"><p><?=$company_arr[$item["COMPANY_ID"]]; ?></p></td>
                    <td><p><?=$buyer_arr[$item["BUYER_ID"]]; ?></p></td>
				</tr>
                <? $i++; } ?>
			</tbody>
		</table>
	</div>
	<div style="width:580px;" align="left">
		<table width="100%">
			<tr>
				<td align="center" colspan="6" height="30" valign="bottom">
					<div style="width:100%">
							<div style="width:50%; float:left" align="left">
								<!-- <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<?// echo implode(',',$id_arr);?>')" /> Check / Uncheck All -->
							</div>
							<div style="width:50%;" align="center">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
					</div>
				</td>
			</tr>
		</table>
    </div>
    <?
	
	exit(); 
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_within_group=str_replace("'","",$cbo_within_group);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_booking_type=str_replace("'","",$cbo_booking_type);
	$txt_booking_id=str_replace("'","",$txt_booking_id);
	$cbo_date_type=str_replace("'","",$cbo_date_type);
	$txt_lot=str_replace("'","",$txt_lot);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$txt_product_id=str_replace("'","",$txt_product_id);

	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	

 	if($type==1)
	{
		$fso_cond = '';
		if($cbo_within_group != ''){
			$fso_cond .= " and a.within_group = $cbo_within_group";
		}
		if($cbo_buyer_name != '' && $cbo_buyer_name != 0)
		{
			$fso_cond .= " and a.buyer_id = $cbo_buyer_name";
		}
		if (trim($cbo_year) != 0) 
		{
			$fso_cond .= " and to_char(a.insert_date,'YYYY') = ".$cbo_year."";
			$booking_cond .= " and a.BOOKING_YEAR = $cbo_year";
		}
		if($cbo_date_type != '')
		{
			if($txt_date_from != '' && $txt_date_to != '')
			{
				if($cbo_date_type == 2) //fso date
				{
					$fso_cond .= " and a.insert_date between '" . trim($txt_date_from) . "' AND '" . trim($txt_date_to) . "'";
				}
				else if($cbo_date_type == 3) //Booking date
				{
					// $booking_cond .= " and a.insert_date between '" . trim($txt_date_from) . "' AND '" . trim($txt_date_to) . "'";
					$fso_cond .= " and a.booking_date between '" . trim($txt_date_from) . "' AND '" . trim($txt_date_to) . "'";
				}
				else if($cbo_date_type == 1) //allocation date
				{
					$allocation_cond .= " and a.allocation_date between '" . trim($txt_date_from) . "' AND '" . trim($txt_date_to) . "'";
				}
			}	
		}
		if($cbo_within_group != '')
		{
			if($cbo_within_group == 1)
			{
				$fso_cond .= " and a.within_group = 1";
			}
			else if($cbo_within_group == 2)
			{
				$fso_cond .= " and a.within_group = 2";
			}
		}
		if($txt_booking_id != '')
		{
			$fso_cond .= " and a.id in ($txt_booking_id) ";
		}
		if($txt_product_id != '')
		{
			$allocation_cond .= " and b.id in ($txt_product_id)";
		}


		
		ob_start();
		$dataArray = array();
		// and a.job_no = 'FAL-FSOE-24-00046'
		$fso_sql = sql_select("select a.id, a.job_no as fso_no, a.po_job_no, a.company_id, a.sales_booking_no, a.booking_id, a.style_ref_no, a.buyer_id, a.booking_date, a.delivery_date, sum(b.grey_qty) as fso_qty, a.insert_date from fabric_sales_order_mst a , fabric_sales_order_dtls b where a.company_id = $cbo_company_name and a.STATUS_ACTIVE = 1 and b.status_active = 1 and a.id = b.mst_id $fso_cond  group by a.id, a.job_no, a.po_job_no, a.company_id, a.sales_booking_no, a.style_ref_no, a.buyer_id, a.booking_date, a.delivery_date, a.booking_id, a.insert_date");
		
		foreach($fso_sql as $row)
		{
			$dataArray[$row['FSO_NO']][$row['SALES_BOOKING_NO']]['FSO_NO'] = $row['FSO_NO'];
			$dataArray[$row['FSO_NO']][$row['SALES_BOOKING_NO']]['COMPANY_ID'] = $row['COMPANY_ID'];
			$dataArray[$row['FSO_NO']][$row['SALES_BOOKING_NO']]['SALES_BOOKING_NO'] = $row['SALES_BOOKING_NO'];
			$dataArray[$row['FSO_NO']][$row['SALES_BOOKING_NO']]['STYLE_REF_NO'] = $row['STYLE_REF_NO'];
			$dataArray[$row['FSO_NO']][$row['SALES_BOOKING_NO']]['BUYER_ID'] = $row['BUYER_ID'];
			$dataArray[$row['FSO_NO']][$row['SALES_BOOKING_NO']]['BOOKING_DATE'] = $row['BOOKING_DATE'];
			$dataArray[$row['FSO_NO']][$row['SALES_BOOKING_NO']]['FSO_DATE'] = $row['INSERT_DATE'];
			$dataArray[$row['FSO_NO']][$row['SALES_BOOKING_NO']]['FSO_QTY'] = $row['FSO_QTY'];
			$dataArray[$row['FSO_NO']][$row['SALES_BOOKING_NO']]['PO_JOB_NO'] = $row['PO_JOB_NO'];
			$all_sale_ids .= $row['ID'].","; 
		}
		// echo "<pre>";
		// print_r($dataArray); die;

		$all_sale_ids = trim($all_sale_ids, ",");
		$booking_sql = sql_select("select a.BOOKING_NO, sum(b.GREY_FAB_QNTY) as booking_qty from WO_BOOKING_MST a, WO_BOOKING_DTLS b where a.id = b.BOOKING_MST_ID and a.status_active = 1 and b.status_active = 1 and a.booking_type in (1,4) $booking_cond and a.COMPANY_ID = $cbo_company_name group by a.BOOKING_NO");
		foreach($booking_sql as $row)
		{
			foreach($dataArray as $fso)
			{
				foreach($fso as $key=>$val)
				{
					if($val['SALES_BOOKING_NO'] == $row['BOOKING_NO'])
					{
						$dataArray[$val['FSO_NO']][$val['SALES_BOOKING_NO']]['BOOKING_QTY'] = $row['BOOKING_QTY'];
					}
				}
			}
		}

		// echo $all_sale_ids;
		$allocation_sql = sql_select("select a.job_no, a.item_id, a.qnty as allocated_qty, a.allocation_date, c.update_date, a.po_break_down_id, a.booking_no,  b.lot, b.color, b.brand, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.PRODUCT_NAME_DETAILS, c.remarks, b.current_stock from inv_material_allocation_dtls a left join INV_MATERIAL_ALLOCATION_MST c on c.id = a.mst_id, product_details_master b where a.status_active = 1 and b.status_active = 1 and c.status_active = 1 and a.item_id = b.id and a.item_category = 1 and a.po_break_down_id in ($all_sale_ids) $allocation_cond");
		
		$new_data_arr = array();

		$yarn_color_arr=return_library_array( "select id, COLOR_NAME from LIB_COLOR", "id", "COLOR_NAME"  );
		$yarn_type_arr=return_library_array( "select YARN_TYPE_ID, YARN_TYPE_SHORT_NAME from LIB_YARN_TYPE", "YARN_TYPE_ID", "YARN_TYPE_SHORT_NAME"  );
		$yarn_brand_arr=return_library_array( "select ID, BRAND_NAME from LIB_BRAND", "ID", "BRAND_NAME"  );
		$yarn_count_arr=return_library_array( "select ID, YARN_COUNT from LIB_YARN_COUNT", "ID", "YARN_COUNT"  );
		$shipment_date_arr=return_library_array( "select JOB_NO_MST, PUB_SHIPMENT_DATE from WO_PO_BREAK_DOWN",'JOB_NO_MST','PUB_SHIPMENT_DATE');

		foreach($allocation_sql as $row)
		{
			$yarn_composition = $yarn_type_arr[$row['YARN_COMP_TYPE1ST']]." ".$row['YARN_COMP_PERCENT1ST']."% ".$yarn_type_arr[$row['YARN_COMP_TYPE2ND']]." ".$row['YARN_COMP_PERCENT2ND'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['FSO_NO'] = $row['JOB_NO'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['COMPANY_ID'] = $dataArray[$row['JOB_NO']][$row['BOOKING_NO']]['COMPANY_ID'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['SALES_BOOKING_NO'] = $dataArray[$row['JOB_NO']][$row['BOOKING_NO']]['SALES_BOOKING_NO'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['STYLE_REF_NO'] = $dataArray[$row['JOB_NO']][$row['BOOKING_NO']]['STYLE_REF_NO'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['BUYER_ID'] = $dataArray[$row['JOB_NO']][$row['BOOKING_NO']]['BUYER_ID'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['BOOKING_DATE'] = $dataArray[$row['JOB_NO']][$row['BOOKING_NO']]['BOOKING_DATE'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['FSO_DATE'] = $dataArray[$row['JOB_NO']][$row['BOOKING_NO']]['FSO_DATE'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['FSO_QTY'] = $dataArray[$row['JOB_NO']][$row['BOOKING_NO']]['FSO_QTY'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['PO_JOB_NO'] = $dataArray[$row['JOB_NO']][$row['BOOKING_NO']]['PO_JOB_NO'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['BOOKING_QTY'] = $dataArray[$row['JOB_NO']][$row['BOOKING_NO']]['BOOKING_QTY'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['ALLOCATED_QTY'] = $row['ALLOCATED_QTY'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['FIRST_ALLOCATION_DATE'] = $row['ALLOCATION_DATE'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['LAST_ALLOCATION_DATE'] = $row['UPDATE_DATE'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['YARN_COMPOSITION'] = $row['PRODUCT_NAME_DETAILS'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['LOT'] = $row['LOT'];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['COLOR'] = $yarn_color_arr[$row['COLOR']];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['BRAND'] = $yarn_brand_arr[$row['BRAND']];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['YARN_TYPE'] = $yarn_type_arr[$row['YARN_TYPE']];
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['YARN_COUNT'] = $yarn_count_arr[$row['YARN_COUNT_ID']];		
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['PRODUCT_ID'] = $row['ITEM_ID'];		
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['ALLOCATION_REMARKS'] = $row['REMARKS'];		
			$new_data_arr[$row['JOB_NO']][$row['BOOKING_NO']][$row['ITEM_ID']]['CURRENT_STOCK'] = $row['CURRENT_STOCK'];		
		}
		$requisition_sql = sql_select("SELECT a.JOB_NO, c.PROD_ID, c.REQUISITION_NO, c.yarn_qnty AS requisition_qnty, d.DEMAND_QNTY, a.SALES_BOOKING_NO, CASE  WHEN e.transaction_type = 2 THEN e.CONS_QUANTITY ELSE NULL  END as issue_qnty, CASE  WHEN e.transaction_type = 4 THEN e.CONS_QUANTITY ELSE NULL  END as issue_return_qnty FROM FABRIC_SALES_ORDER_MST a, PPL_PLANNING_ENTRY_PLAN_DTLS  b, PPL_YARN_REQUISITION_ENTRY c LEFT JOIN PPL_YARN_DEMAND_ENTRY_DTLS d ON c.REQUISITION_NO = d.REQUISITION_NO LEFT JOIN inv_transaction e ON e.requisition_no = d.requisition_no AND (e.transaction_type = 2 OR e.transaction_type = 4) AND e.prod_id = c.prod_id WHERE a.id = b.po_id AND b.dtls_id = c.knit_id AND a.ID IN ($all_sale_ids) and b.company_id = $cbo_company_name and a.status_active = 1 and b.status_active = 1 and c.status_active = 1 and d.status_active = 1");

		foreach($requisition_sql as $row)
		{
			if($row['REQUISITION_QNTY'])
				$new_data_arr[$row['JOB_NO']][$row['SALES_BOOKING_NO']][$row['PROD_ID']]['REQUISITION_QNTY'] = $row['REQUISITION_QNTY'];
			else
				$new_data_arr[$row['JOB_NO']][$row['SALES_BOOKING_NO']][$row['PROD_ID']]['REQUISITION_QNTY'] = 0;
			
			if($row['DEMAND_QNTY'])
				$new_data_arr[$row['JOB_NO']][$row['SALES_BOOKING_NO']][$row['PROD_ID']]['DEMAND_QNTY'] = $row['DEMAND_QNTY'];
			else
				$new_data_arr[$row['JOB_NO']][$row['SALES_BOOKING_NO']][$row['PROD_ID']]['DEMAND_QNTY'] = 0;
			
			$new_data_arr[$row['JOB_NO']][$row['SALES_BOOKING_NO']][$row['PROD_ID']]['ISSUE_QNTY'] += $row['ISSUE_QNTY'];
			$new_data_arr[$row['JOB_NO']][$row['SALES_BOOKING_NO']][$row['PROD_ID']]['ISSUE_RETURN_QNTY'] += $row['ISSUE_RETURN_QNTY'];
			
		}
		// echo "<pre>";
		// print_r($new_data_arr); die;
	
		$date_type_arr = array(1 => 'Allcoation Date', 2 => 'FSO Date', 3 => 'Booking Date', 4 => 'Shipment Date');
		?>
		<fieldset style="width:3140px; margin-left:10px;">
			<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="center" width="100%" colspan="20" ><strong style="font-size:23px"><? echo $company_library[$cbo_company_name]; ?></strong></td>
				</tr>
				<tr>
					<td align="center" width="100%" colspan="20" ><strong style="font-size:19px">Reference wise Allocation History Report</strong></td>
				</tr>
				<? if($txt_date_from && $txt_date_to){ ?>
				<tr>
					<td align="center" width="100%" colspan="20" ><strong style="font-size:16px"><? echo $date_type_arr[$cbo_date_type]." ".$txt_date_from." to ".$txt_date_to ; ?></strong></td>
				</tr>
				<? } ?>
            </table>
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width='160'>Company</th>
					<th width="80">Buyer</th>
					<th width="120">Style No.</th>
					<th width="80">Shipment Date</th>
					<th width="80">Booking Date</th>
					<th width="100">Booking No</th>
					<th width="100">Booking Qnty</th>
					<th width="80">FSO Date</th>
                    <th width="120">FSO No</th>
                    <th width="100">FSO Qnty</th>
                    <th width="80">Allocation Date [First]</th>
					<th width="80">Allocation Date [Last]</th>
					<th width="80">Product ID</th>
					<th width="100">Yarn Lot</th>
					<th width="80">Yarn Color</th>
					<th width="100">Yarn Brand</th>
					<th width="80">Yarn Count</th>
					<th width="80">Yarn Type</th>
					<th width="200">Yarn Composition</th>
					<th width="100">Wgt.<br>Bag/Cone</th>
					<th width="100">Allocated Yarn Qnty</th>
					<th width="100">FSO wise Allocation Balance</th>
					<th width="100">Requisition Qnty</th>
					<th width="100">Requisition Balance</th>
					<th width="100">Demand Qnty</th>
					<th width="100">Demand Balance</th>
					<th width="100">Issue Qnty</th>
					<th width="100">Issue Return</th>
					<th width="100">Net Issue</th>
					<th width="120">Unallocated Yarn Qnty (Stock)</th>
					<th width="80">Allocation Remarks</th>
				</thead>
			</table>
			<div style="width:100%; overflow-y:scroll; max-height:330px;" id="scroll_body">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
					<tbody>
						<?
						$i = 1;
						$grand_booking_qnty = 0;
						$grand_fso_qnty = 0;
						$grand_allocated_qnty = 0;
						$grand_allocated_balance = 0;
						$grand_req_qnty = 0;
						$grand_req_balance = 0;
						$grand_demand_qnty = 0;
						$grand_demand_balance = 0;
						$grand_issue_qnty = 0;
						$grand_issue_return_qnty = 0;
						$grand_net_issue_qnty = 0;
						$grand_unallocated_qnty = 0;

						foreach($new_data_arr as $job)
						{
							foreach($job as $booking)
							{
								$sub_booking_qnty = 0;
								$sub_fso_qnty = 0;
								$sub_allocated_qnty = 0;
								$sub_allocated_balance = 0;
								$sub_req_qnty = 0;
								$sub_req_balance = 0;
								$sub_demand_qnty = 0;
								$sub_demand_balance = 0;
								$sub_issue_qnty = 0;
								$sub_issue_return_qnty = 0;
								$sub_net_issue_qnty = 0;
								$sub_unallocated_qnty = 0;
								foreach($booking as $item)
								{
									if($item['FSO_NO'] != ""){

										if($item['REQUISITION_QNTY'])
											$requisition_qnty =  $item['REQUISITION_QNTY'];
										else
											$requisition_qnty = 0;
										if($item['DEMAND_QNTY'])
											$demand_qnty =  $item['DEMAND_QNTY'];
										else
											$demand_qnty = 0;
										$fso_wise_allocation_balance = $item['FSO_QTY']-$item['ALLOCATED_QTY'];
										$requisition_balance = $item['ALLOCATED_QTY'] - $requisition_qnty;
										$demand_balance = $requisition_qnty - $demand_qnty;
										$bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_1nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_1nd<? echo $i; ?>">
											<td align="center" width="40"><?= $i; ?></td>
											<td align="center" width="160"><?= $company_library[$item['COMPANY_ID']]; ?></td>
											<td align="center" width="80"><?= $buyer_arr[$item['BUYER_ID']]; ?></td>
											<td align="center" width="120"><p><?= $item['STYLE_REF_NO']; ?></p></td>
											<td align="center" width="80"><?= $shipment_date_arr[explode(",",$item['PO_JOB_NO'])[0]]; ?></td>
											<td align="center" width="80"><?= $item['BOOKING_DATE']; ?></td>
											<td align="center" width="100"><p><?= $item['SALES_BOOKING_NO']; ?></p></td>
											<td align="right" width="100"><?= $item['BOOKING_QTY']; $sub_booking_qnty = $item['BOOKING_QTY']; ?></td>
											<td align="center" width="80"><?= explode(" ",$item['FSO_DATE'])[0]; ?></td>
											<td align="center" width="120"><p><?= $item['FSO_NO']; ?></p></td>
											<td align="right" width="100"><p><?= $item['FSO_QTY']; $sub_fso_qnty = $item['FSO_QTY']; ?></p></td>
											<td align="center" width="80"><?= $item['FIRST_ALLOCATION_DATE']; ?></td>
											<td align="center" width="80"><? 
											if($item['LAST_ALLOCATION_DATE']){
												echo explode(" ",$item['LAST_ALLOCATION_DATE'])[0];
											}
											else{
												echo $item['FIRST_ALLOCATION_DATE'];
											}
											 ?></td>
											<td align="center" width="80"><?= $item['PRODUCT_ID']; ?></td>
											<td align="center" width="100"><p><?= $item['LOT']; ?></p></td>
											<td align="center" width="80"><p><?= $item['COLOR']; ?></p></td>
											<td align="center" width="100"><p><?= $item['BRAND']; ?></p></td>
											<td align="center" width="80"><p><?= $item['YARN_COUNT']; ?></p></td>
											<td align="center" width="80"><p><?= $item['YARN_TYPE']; ?></p></td>
											<td align="center" width="200"><p><?= $item['YARN_COMPOSITION']; ?></p></td>
											<td align="center" width="100"><? ?></td>
											<td align="right" width="100"><?= $item['ALLOCATED_QTY']; $sub_allocated_qnty += $item['ALLOCATED_QTY']; ?></td>
											<td align="right" width="100"><p><?= number_format($fso_wise_allocation_balance,2); $sub_allocated_balance += $fso_wise_allocation_balance; ?></p></td>
											<td align="right" width="100"><p><?= $requisition_qnty; $sub_req_qnty += $requisition_qnty; ?></p></td>
											<td align="right" width="100"><?= $requisition_balance; $sub_req_balance += $requisition_balance; ?></td>
											<td align="right" width="100"><p><?= $demand_qnty; $sub_demand_qnty += $demand_qnty; ?></p></td>
											<td align="right" width="100"><p><?= $demand_balance; $sub_demand_balance += $demand_balance; ?></p></td>
											<td align="right" width="100"><p><? if($item['ISSUE_QNTY']){ $issue_qnty = $item['ISSUE_QNTY']; } else { $issue_qnty = 0 ;} echo $issue_qnty; $sub_issue_qnty += $issue_qnty; ?></p></td>
											<td align="right" width="100"><p><? if($item['ISSUE_RETURN_QNTY']){ $issue_return_qnty = $item['ISSUE_RETURN_QNTY']; } else { $issue_return_qnty = 0 ;} echo $issue_return_qnty; $sub_issue_return_qnty += $issue_return_qnty ?></p></td>
											<td align="right" width="100"><p><? $net_issue = $issue_qnty - $issue_return_qnty; echo $net_issue; $sub_net_issue_qnty += $net_issue; ?></p></td>
											<td align="right" width="120"><p><? $unallocated_balance = ($item['CURRENT_STOCK'] - $item['ALLOCATED_QTY']); echo number_format($unallocated_balance,2); $sub_unallocated_qnty += $unallocated_balance  ?></p></td>
											<td align="center" width="80"><p><?= $item['ALLOCATION_REMARKS']; ?></p></td>
										</tr> 
										<?
										$i++;
									}
								}
							}
							if($sub_booking_qnty != 0 || $sub_fso_qnty != 0 || $sub_allocated_qnty != 0 || $sub_allocated_balance != 0 || $sub_req_qnty != 0 || $sub_req_balance != 0 || $sub_demand_qnty != 0 || $sub_demand_balance != 0 || $sub_issue_qnty != 0 || $sub_issue_return_qnty != 0 || $sub_net_issue_qnty != 0 || $sub_unallocated_qnty != 0)
							{
								?>
								<tr style="background-color: #d9d9d9;">
									<th width="40"></th>
									<th width='160'></th>
									<th width="80"></th>
									<th width="120"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="100">Sub Total</th>
									<th align="right" width="100"><?= $sub_booking_qnty; ?></th>
									<th width="80"></th>
									<th width="120"></th>
									<th align="right" width="100"><?= $sub_fso_qnty; ?></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="100"></th>
									<th width="80"></th>
									<th width="100"></th>
									<th width="80"></th>
									<th width="80"></th>
									<th width="200"></th>
									<th width="100"></th>
									<th align="right" width="100"><?= $sub_allocated_qnty; ?></th>
									<th align="right" width="100"><?= number_format($sub_allocated_balance,2); ?></th>
									<th align="right" width="100"><?= number_format($sub_req_qnty,2); ?></th> 
									<th align="right" width="100"><?= number_format($sub_req_balance,2); ?></th>
									<th align="right" width="100"><?= number_format($sub_demand_qnty,2); ?></th>
									<th align="right" width="100"><?= number_format($sub_demand_balance,2); ?></th>
									<th align="right" width="100"><?= number_format($sub_issue_qnty,2); ?></th>
									<th align="right" width="100"><?= number_format($sub_issue_return_qnty,2); ?></th>
									<th align="right" width="100"><?= number_format($sub_net_issue_qnty,2); ?></th>
									<th align="right" width="120"><p><?= number_format($sub_unallocated_qnty,2); ?></p></th>
									<th width="80"></th>					
								</tr>
								<?
							}
							$grand_booking_qnty += $sub_booking_qnty;
							$grand_fso_qnty += $sub_fso_qnty;
							$grand_allocated_qnty += $sub_allocated_qnty;
							$grand_allocated_balance += $sub_allocated_balance;
							$grand_req_qnty += $sub_req_qnty;
							$grand_req_balance += $sub_req_balance;
							$grand_demand_qnty += $sub_demand_qnty;
							$grand_demand_balance += $sub_demand_balance;
							$grand_issue_qnty += $sub_issue_qnty;
							$grand_issue_return_qnty += $sub_issue_return_qnty;
							$grand_net_issue_qnty += $sub_net_issue_qnty;
							$grand_unallocated_qnty += $sub_unallocated_qnty;

						}	
						?>
					</tbody>
				</table>
				<table width="100%" cellpadding="2" cellspacing="0" border="1" class="rpt_table">
					<tfoot>
						<tr style="background-color: #d9d9d9;">
							<th width="40"></th>
							<th width='160'></th>
							<th width="80"></th>
							<th width="120"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="100">Grand Total</th>
							<th align="right" width="100"><?= $grand_booking_qnty; ?></th>
							<th width="80"></th>
							<th width="120"></th>
							<th align="right" width="100"><?= $grand_fso_qnty; ?></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="100"></th>
							<th width="80"></th>
							<th width="80"></th>
							<th width="200"></th>
							<th width="100"></th>
							<th align="right" width="100"><?= $grand_allocated_qnty; ?></th>
							<th align="right" width="100"><?= number_format($grand_allocated_balance,2); ?></th>
							<th align="right" width="100"><?= number_format($grand_req_qnty,2); ?></th> 
							<th align="right" width="100"><?= number_format($grand_req_balance,2); ?></th>
							<th align="right" width="100"><?= number_format($grand_demand_qnty,2); ?></th>
							<th align="right" width="100"><?= number_format($grand_demand_balance,2); ?></th>
							<th align="right" width="100"><?= number_format($grand_issue_qnty,2); ?></th>
							<th align="right" width="100"><?= number_format($grand_issue_return_qnty,2); ?></th>
							<th align="right" width="100"><?= number_format($grand_net_issue_qnty,2); ?></th>
							<th align="right" width="120"><p><?= number_format($grand_unallocated_qnty,2); ?></p></th>
							<th width="80"></th>					
						</tr>
					</tfoot>
				</table>
			</div>
		</fieldset>
		<?
	}
	
	foreach (glob("$user_name*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_name."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename="requires/".$user_name."_".$name.".xls";
	echo "$total_data####$filename";
	exit();	
}


if ($action == "item_description_search")
{
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>
	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_no = new Array;

		function check_all_data() {
			var tbl_row_count = document.getElementById('list_view').rows.length;
			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				var onclickString = $('#tr_' + i).attr('onclick');
				var paramArr = onclickString.split("'");
				var functionParam = paramArr[1];
				js_set_value(functionParam);

			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(strCon)
		{
			var splitSTR = strCon.split("_");
			var str = splitSTR[0];
			var selectID = splitSTR[1];
			var selectDESC = splitSTR[2];

			toggle(document.getElementById('tr_' + str), '#FFFFCC');

			if (jQuery.inArray(selectID, selected_id) == -1) {
				selected_id.push(selectID);
				selected_name.push(selectDESC);
				selected_no.push(str);
			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == selectID)
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_no.splice(i, 1);
			}
			var id = '';
			var name = '';
			var job = '';
			var num = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + ',';
				num += selected_no[i] + ',';
			}
			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			num = num.substr(0, num.length - 1);

			$('#txt_selected_id').val(id);
			$('#txt_selected').val(name);
			$('#txt_selected_no').val(num);
		}

		function fn_check_lot()
		{ 
            var supplier =  document.getElementById('cbo_supplier').value;
            var dyed_type =  document.getElementById('cbo_dyed_type').value;
            var lot = document.getElementById('txt_search_common').value;
            var yarn_count = document.getElementById('cbo_yarn_count').value;
            var yarn_type = document.getElementById('cbo_yarn_type').value;
			show_list_view ( supplier+'_'+dyed_type+'_'+lot+'_'+yarn_count+'_'+yarn_type+'_'+<? echo $company; ?>, 'create_lot_search_list_view', 'search_div', 'reference_wise_allocation_history_report_controller', 'setFilterGrid("list_view",-1)');
		}
	</script>
	<body>
		<div align="center" style="width:100%;" >
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table width="700" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
					<tr>                	 
						<th>Supplier Name</th>
						<th>Dyed Type</th>
						<th>Lot</th>
						<th>Yarn Count</th>
						<th>Yarn Type</th>
 						<th>
                       		<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />
                            <input type='hidden' id='txt_selected_id' />
							<input type='hidden' id='txt_selected' />
							<input type='hidden' id='txt_selected_no' />
                        </th>
					</tr>
				</thead>
				<tbody>
					<tr align="center">
						<td align="center">
							<? echo create_drop_down("cbo_supplier", 80, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' $user_supplier_cond and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0); ?>
						</td>
                        <td>
                            <? $dyed_type_arr = array(1 => 'Dyed Yarn', 2 => 'Non Dyed Yarn');
                            echo create_drop_down("cbo_dyed_type", 80, $dyed_type_arr, "", 1, "All", 0, "", 0); ?>
                        </td>
						<td width="100" align="center" id="search_by_td">				
							<input type="text" style="width:100px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />	
						</td> 
                        <td>
                            <? echo create_drop_down("cbo_yarn_count", 80, "select id,yarn_count from lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count", 1, "-- Select --", 0, "", 0); ?>
                        </td>
                        <td>
                            <? echo create_drop_down("cbo_yarn_type", 120, "select yarn_type_id,yarn_type_short_name from lib_yarn_type where is_deleted=0 and status_active=1 order by yarn_type_short_name", "yarn_type_id,yarn_type_short_name", 1, "-- Select --", 0, "", 0); ?>
                        </td>
						<td align="center">
							<input type="button" name="btn_show" class="formbutton" value="Show" onClick="fn_check_lot()" style="width:100px;" />				
						</td>
					</tr>
 				</tbody>
				</tr>
			</table>
			<div align="center" valign="top" style="margin-top:5px" id="search_div"> </div>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
    /* $sql = "select id,supplier_id,lot,product_name_details from product_details_master where company_id=$company and item_category_id=1";
      //echo $sql;
      $supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
      $arr=array(1=>$supplier_arr);
      echo create_list_view("list_view", "Product Id, Supplier, Lot, Item Description","70,160,70","600","300",0, $sql , "js_set_value", "id,product_name_details", "", 1, "0,supplier_id,0,0", $arr, "id,supplier_id,lot,product_name_details", "","setFilterGrid('list_view',-1)","0","",1) ;
      echo "<input type='hidden' id='txt_selected_id' />";
      echo "<input type='hidden' id='txt_selected' />";
      echo "<input type='hidden' id='txt_selected_no' />"; */
      ?>
      <script language="javascript" type="text/javascript">
        /*var style_no='<? echo $txt_produc_no; ?>';
         var style_id='<? echo $txt_produc_id; ?>';
         var style_des='<? echo $txt_product; ?>';
         //alert(style_id);
         if(style_no!="")
         {
         style_no_arr=style_no.split(",");
         style_id_arr=style_id.split(",");
         style_des_arr=style_des.split(",");
         var str_ref="";
         for(var k=0;k<style_no_arr.length; k++)
         {
         str_ref=style_no_arr[k]+'_'+style_id_arr[k]+'_'+style_des_arr[k];
         js_set_value(str_ref);
         }
     }*/
 	</script>
 	<?
 	exit();
}

if($action=="create_lot_search_list_view")
{
 	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$dyed_type = $ex_data[1];
	$yarn_count = $ex_data[3];
	$yarn_type = $ex_data[4];
	$lot = trim($ex_data[2]);
	$company = $ex_data[5];
	
	$sql_cond="";
	if(trim($lot)!="")
	{
        $sql_cond .= " and lot LIKE '%$lot%'";
 	}
    if($supplier != '' && $supplier != 0)
    {
        $sql_cond .= " and SUPPLIER_ID = $supplier";
    } 
    if($dyed_type != '' && $dyed_type != 0)
    {
        $sql_cond .= " and DYED_TYPE = $dyed_type";
    } 
    if($yarn_count != '' && $yarn_count != 0)
    {
        $sql_cond .= " and YARN_COUNT_ID = $yarn_count";
    } 
    if($yarn_type != '' && $yarn_type != 0)
    {
        $sql_cond .= " and YARN_TYPE = $yarn_type";
    } 
	$sql = "select id, supplier_id, lot, product_name_details, yarn_count_id, dyed_type, yarn_type, color from product_details_master where status_active = 1 and is_deleted = 0 and company_id=$company and item_category_id=1 $sql_cond";
 	$sql_res = sql_select($sql); 
    // echo $sql; die;
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$yarn_count_id_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$yarn_type_arr=return_library_array( "select YARN_TYPE_ID, YARN_TYPE_SHORT_NAME from lib_yarn_type",'YARN_TYPE_ID','YARN_TYPE_SHORT_NAME');
	$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
    $dyed_type_arr = array(1 => 'Dyed Yarn', 2 => 'Non Dyed Yarn');

	// $arr=array(1=>$yarn_count_id_arr,2=>$dyed_type_arr,5=>$supplier_arr);
	// echo create_list_view("list_view", "Product Id,Count,Dyed Type,Yarn Composition,Lot,Supplier","70,70,80,160,100,100","670","260",0, $sql , "js_set_value", "id,yarn_count_id,dyed_type,product_name_details,lot,supplier_id", "", 1, "0,yarn_count_id,dyed_type,0,0,supplier_id", $arr, "id,yarn_count_id,dyed_type,product_name_details,lot,supplier_id", "","","0","",1) ;

    ?>
    <table width="790" cellpadding="0" cellspacing="0" border="0" class="rpt_table" id="rpt_tablelist_view" rules="all">
		<thead>
			<tr>
				<th width="50">SL No</th>
				<th width="70">Product Id</th>
				<th width="70">Count</th>
				<th width="80">Dyed Type</th>
				<th width="160">Yarn Composition</th>
				<th width="80">Yarn Type</th>
				<th width="80">Color</th>
				<th width="100">Lot</th>
				<th>Supplier</th>
			</tr>
		</thead>
	</table> 
    <div style="width:798px; overflow-y:scroll; min-height:50px; max-height:230px;" id="">
		<table align="left" width="798" height="" cellpadding="0" cellspacing="0" border="0" class="rpt_table" id="list_view" rules="all">
			<tbody>
                <?
                $i = 1;
                foreach ($sql_res as $item) { 
					$id_arr[] = $item['ID'];

					if ($i % 2 == 0) {
						$bgcolor = "#E9F3FF";
					} else {
						$bgcolor = "#FFFFFF";
					}
					$selectedString = "'".$i.'_'.$item['ID'].'_'.$item['PRODUCT_NAME_DETAILS']."'";	
				?>
				<tr onClick="js_set_value(<? echo $selectedString;?>)" bgcolor="<?=$bgcolor;?>" style="cursor:pointer" id="tr_<?=$i;?>">
					<td width="50"><?= $i; ?></td>
					<td align="left" width="70"><p><?=$item["ID"]; ?></p></td>
					<td align="left" width="70"><p><?=$yarn_count_id_arr[$item["YARN_COUNT_ID"]]; ?></p></td>
					<td align="left" width="80"><p><?=$dyed_type_arr[$item["DYED_TYPE"]]; ?></p></td>
					<td align="left" width="160"><p><?=$item["PRODUCT_NAME_DETAILS"]; ?></p></td>
					<td align="left" width="80"><p><?=$yarn_type_arr[$item["YARN_TYPE"]]; ?></p></td>
					<td align="left" width="80"><p><?=$color_arr[$item["COLOR"]]; ?></p></td>
					<td align="left" width="100"><p><?=$item["LOT"]; ?></p></td>
                    <td><p><?=$supplier_arr[$item["SUPPLIER_ID"]]; ?></p></td>
				</tr>
                <? $i++; } ?>
			</tbody>
		</table>
	</div>
	<div style="width:580px;" align="left">
		<table width="100%">
			<tr>
				<td align="center" colspan="6" height="30" valign="bottom">
					<div style="width:100%">
							<div style="width:50%; float:left" align="left">
								<!--<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data('<?// echo implode(',',$id_arr);?>')" /> Check / Uncheck All-->
							</div>
							<div style="width:50%;" align="center">
							<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton" value="Close" style="width:100px" />
							</div>
					</div>
				</td>
			</tr>
		</table>
    </div>
    <?

    
    
	exit();	
}


?>