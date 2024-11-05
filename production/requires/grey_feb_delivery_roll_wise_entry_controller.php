<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");


if ($action == "load_drop_down_location")
{
	echo create_drop_down("cbo_location_id", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'grey_feb_delivery_roll_wise_entry_controller', $data+'__'+this.value, 'load_drop_down_floor', 'floor_td' );");
	exit();
}

if ($action == "load_drop_down_floor")
{
	$ex_data=explode("__",$data);
	if ($ex_data[1] == 0 || $ex_data[1] == "") $location_cond = ""; else $location_cond = " and b.location_id=$ex_data[1]";
	echo create_drop_down("cbo_floor_id", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id='$ex_data[0]' and b.status_active=1 and b.is_deleted=0 and a.production_process=2 $location_cond group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select --", $selected, "", "");
	exit();
}

if ($action == "booking_no_popup")
{
	echo load_html_head_contents("Booking Info", "../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_booking_id").val(splitData[0]);
			$("#hide_booking_no").val(splitData[1]);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:840px;">
					<table width="840" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th width="130">Please Enter Job No</th>
							<th width="130">Please Enter Booking No</th>
							<th>Booking Date</th>

							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
							<input type="hidden" name="hide_booking_no" id="hide_booking_no" value="" />
							<input type="hidden" name="hide_booking_id" id="hide_booking_id" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($companyID) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--","","",0 );
									?>
								</td>
								<td align="center">
									<input type="text" style="width:100px" class="text_boxes" name="txt_job_no" id="txt_job_no" />
								</td>
								<td align="center">
									<input type="text" style="width:100px" class="text_boxes" name="txt_booking_no" id="txt_booking_no" />
								</td>
								<td align="center">
									<input type="text" style="width:70px" class="datepicker" name="txt_date_from" id="txt_date_from" readonly/> To
									<input type="text" style="width:70px" class="datepicker" name="txt_date_to" id="txt_date_to" readonly/>
								</td>

								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_booking_no').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value+'**'+document.getElementById('txt_job_no').value+'**'+document.getElementById('cbo_year_selection').value, 'create_booking_no_search_list_view', 'search_div', 'grey_feb_delivery_roll_wise_entry_controller', 'setFilterGrid(\'list_view\',-1)');" style="width:100px;" />
								</td>
							</tr>
							<tr>
								<td colspan="4" align="center" width="95%"><? echo load_month_buttons(1); ?></td>
							</tr>
						</tbody>
					</table>
					<div style="margin-top:15px" id="search_div"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
<?
exit();
}//bookingnumbershow;

if($action == "create_booking_no_search_list_view")
{
	//echo 111; die();
	$data=explode('**',$data);
	//echo '<pre>';print_r($data);
	$txt_date_from = $data[3];
	$txt_date_to = $data[4];
 	$year = $data[6];
	if ($data[0]!=0) $company="  a.company_id in($data[0])";
//	if ($data[1]!=0) $buyer=" and a.buyer_id='$data[1]'"; else $buyer="";
	if ($data[2]!=0) $booking_no=" and a.booking_no_prefix_num='$data[2]'"; else $booking_no='';
	if ($data[5]!="") $job_no_cond =" and a.job_no like '%$data[5]'"; else $job_no_cond='';
	if ($data[5]!="") $job_no_prefix_num =" and c.job_no_prefix_num = '$data[5]'"; else $job_no_prefix_num='';

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and a.booking_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and a.booking_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if($year !=0)
	{
		if($db_type==0) { $booking_date=" and YEAR(a.booking_date)=$year";   }
		if($db_type==2) {$booking_date=" and to_char(a.booking_date,'YYYY')=$year";}
	}
	// JOB_NO_PREFIX_NUM
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);
	//pro_batch_create_mst//wo_non_ord_samp_booking_mst
 	$sql= "(select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,pro_batch_create_mst b, wo_po_details_master c where  $company $buyer $job_no_prefix_num $booking_no $sql_cond $booking_date and a.job_no=c.job_no and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0
	union all
	 select a.id,a.booking_no_prefix_num as no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no,a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_non_ord_samp_booking_mst a ,pro_batch_create_mst b where $company $buyer $booking_no $sql_cond $booking_date $job_no_cond and a.booking_no=b.booking_no and a.booking_type=4 and b.status_active=1 and b.is_deleted=0) order by id Desc
	";
	//echo $sql;
	//echo "select a.id,a.booking_no_prefix_num as no_prefix_num,a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,wo_non_ord_samp_booking_mst b where $company $buyer $booking_no $booking_date and a.booking_no=b.booking_no and a.booking_type=1 and a.is_short=2 and a.status_active=1 and a.is_deleted=0 ";
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "110,80,80,80,90,120,80,80,60,50","910","320",0, $sql , "js_set_value", "id,no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','setFilterGrid(\'list_view\',-1);','0,3,0,0,0,0,0,0,0,0','','');
	exit();
}

if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=7 and report_id=42 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#Printt1').hide();\n";
	echo "$('#btn_mc_wise1').hide();\n";
	echo "$('#btn_mc_wise2').hide();\n";
	echo "$('#btn_mc_wise3').hide();\n";
	echo "$('#btn_mc_wise4').hide();\n";
	echo "$('#btn_fabric_label5').hide();\n";
	echo "$('#btn_print11').hide();\n";
	echo "$('#Printt1_booking').hide();\n";
	echo "$('#btn_print12').hide();\n";
	echo "$('#btn_print13').hide();\n";
	echo "$('#print9').hide();\n";
	echo "$('#print10').hide();\n";
	echo "$('#print11').hide();\n";
	echo "$('#print12').hide();\n";
	echo "$('#printmg').hide();\n";
	echo "$('#btn_print20').hide();\n";
	echo "$('#Printt1_booking_2').hide();\n";
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==134){echo "$('#Printt1').show();\n";}
			if($id==135){echo "$('#btn_mc_wise1').show();\n";}
			if($id==136){echo "$('#btn_mc_wise2').show();\n";}
			if($id==137){echo "$('#btn_mc_wise3').show();\n";}
			if($id==138){echo "$('#btn_mc_wise4').show();\n";}
			if($id==139){echo "$('#btn_fabric_label5').show();\n";}
			if($id==161){echo "$('#btn_print11').show();\n";}
			if($id==162){echo "$('#Printt1_booking').show();\n";}
			if($id==191){echo "$('#btn_print12').show();\n";}
			if($id==227){echo "$('#btn_print13').show();\n";}
			if($id==235){echo "$('#print9').show();\n";}
			if($id==274){echo "$('#print10').show();\n";}
			if($id==241){echo "$('#print11').show();\n";}
			if($id==427){echo "$('#print12').show();\n";}
			if($id==848){echo "$('#printmg').show();\n";}
			if($id==768){echo "$('#btn_print20').show();\n";}
			if($id==161){echo "$('#btn_print11').show();\n";}
			if($id==902){echo "$('#Printt1_booking_2').show();\n";}
			
			
		}
	}
	else
	{
		echo "$('#Printt1').show();\n";
		echo "$('#btn_mc_wise1').show();\n";
		echo "$('#btn_mc_wise2').show();\n";
		echo "$('#btn_mc_wise3').show();\n";
		echo "$('#btn_mc_wise4').show();\n";
		echo "$('#btn_fabric_label5').show();\n";
		echo "$('#btn_print11').show();\n";
		echo "$('#Printt1_booking').show();\n";
		echo "$('#btn_print12').show();\n";
		echo "$('#btn_print13').show();\n";
		echo "$('#print9').show();\n";
		echo "$('#print10').show();\n";
		echo "$('#print11').show();\n";
		echo "$('#print12').show();\n";
		echo "$('#printmg').show();\n";
		echo "$('#btn_print20').show();\n";
		echo "$('#Printt1_booking_2').show();\n";
	}
	exit();
}

if ($action == "load_drop_down_knitting_com")
{
	$data = explode("_", $data);
	$company_id = $data[1];
	//$company_id
	if ($data[0] == 1) {
		echo create_drop_down("cbo_workingcompany_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", "id,company_name", 1, "-Knit Company-", "", "load_drop_down( 'grey_feb_delivery_roll_wise_entry_controller', this.value, 'load_drop_down_location', 'location_td' ); load_drop_down( 'grey_feb_delivery_roll_wise_entry_controller', this.value+'__0', 'load_drop_down_floor', 'floor_td' );", "");
	} else if ($data[0] == 3) {
		echo create_drop_down("cbo_workingcompany_id", 130, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=20 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", "id,supplier_name", 1, "-Knit Company-", 0, "load_drop_down( 'grey_feb_delivery_roll_wise_entry_controller', ".$company_id."+'__0', 'load_drop_down_location', 'location_td' ); load_drop_down( 'grey_feb_delivery_roll_wise_entry_controller', ".$company_id."+'__0', 'load_drop_down_floor', 'floor_td' );");
	} else {
		echo create_drop_down("cbo_workingcompany_id", 130, $blank_array, "", 1, "-Knit Company-", 0, "load_location();");
	}
	exit();
}

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$fabricLibraryData = sql_select("select auto_update from variable_settings_production where company_name =$cbo_company_id and variable_list in(15) and item_category_id=13 and is_deleted=0 and status_active=1");
	$fabric_store_auto_update=$fabricLibraryData[0][csf("auto_update")];

	if (str_replace("'", "", $fabric_store_auto_update) == 1)
	{
		echo "11**variable settings AUTO FABRIC STORE UPDATE is set to YES.\nDelivery not available";
		die;

	}

	$all_production_barcode="";
	for ($x = 1; $x <= $tot_row; $x++)
	{
		$proBarcodeNo = "barcodeNo_" . $x;
		$all_production_barcode .= $$proBarcodeNo . ",";
	}

	$all_production_barcode = chop($all_production_barcode, ",");


	if($all_production_barcode!="")
	{
		$all_production_barcode_arr=array_unique(explode(",",$all_production_barcode));
		if($db_type==2 && count($all_production_barcode_arr)>999)
		{
			$barcode_cond_production=" and (";
			$all_production_barcode_arr_chunk=array_chunk($all_production_barcode_arr,999);
			foreach($all_production_barcode_arr_chunk as $barcode)
			{
				$barcodes=implode(",",$barcode);
				$barcode_cond_production.=" barcode_no in($barcodes) or ";
			}

			$barcode_cond_production=chop($barcode_cond_production,'or ');
			$barcode_cond_production.=")";
		}
		else
		{
			$barcode_cond_production=" and barcode_no in (".implode(",",$all_production_barcode_arr).")";
		}


		$productions_sql = sql_select("select barcode_no, status_active, qnty from pro_roll_details where entry_form = 2 and status_active =1 and is_deleted=0 $barcode_cond_production");
		foreach ($productions_sql as $val)
		{
			$sql_production_status[$val[csf("barcode_no")]]=$val[csf("status_active")];
			$production_qnty_arr[$val[csf("barcode_no")]]=$val[csf("qnty")];
		}

		$recv_sql = sql_select("SELECT a.recv_number, b.barcode_no from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=58 and b.entry_form=58 and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 $barcode_cond_production");
		foreach ($recv_sql as $row)
		{
			$recv_no_arr[$row[csf("recv_number")]]=$row[csf("recv_number")];
		}
		$recv_no='';
		if (!empty($recv_no_arr)) 
		{
			$recv_no=implode(",", $recv_no_arr);
			echo "11**Receive Found, Update Not Allow. Receive No = ".$recv_no;
			disconnect($con);
			die;
		}
	}
	// echo "10**string";die;

	//$sql_production_status = return_library_array("select barcode_no,status_active from pro_roll_details where entry_form = 2 and status_active =1 and is_deleted=0 and barcode_no in ($all_production_barcode) ","barcode_no","status_active");

	/*echo "10**select barcode_no,status_active from pro_roll_details where entry_form = 2 and status_active =1 and is_deleted=0 and barcode_no in ($all_production_barcode) ";
	die;*/

	for ($s = 1; $s <= $tot_row; $s++)
	{
		$proBarcodeNo = "barcodeNo_" . $s;
		$currentDelivery = "currentDelivery_" . $s;

		if ($sql_production_status[str_replace("'", "", $$proBarcodeNo)] == "")
		{
			echo "11**Barcode not found in production.\nBarcode No = ".str_replace("'", "", $$proBarcodeNo);
			die;
		}

		if(number_format($production_qnty_arr[str_replace("'", "", $$proBarcodeNo)],2,'.','') != number_format(str_replace("'", "", $$currentDelivery),2,'.','') )
		{
			echo "11**Barcode qnty not match with production.\nBarcode No = ".str_replace("'", "", $$proBarcodeNo)."\nProduction qnty= ".number_format($production_qnty_arr[str_replace("'", "", $$proBarcodeNo)],2)."\nDelivery qnty =".number_format(str_replace("'", "", $$currentDelivery),2);
			die;
		}
	}

	//echo "10**";die;

	if ($operation == 0)  // Insert Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		/*if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**0";
			die;
		}*/

		if ($db_type == 0) $year_cond = "YEAR(insert_date)";
		else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
		else $year_cond = "";//defined Later

		$id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst", $con);
		//$new_mrr_number = explode("*", return_mrr_number(str_replace("'", "", $cbo_company_id), '', 'GDSR', date("Y", time()), 5, "select sys_number_prefix, sys_number_prefix_num from pro_grey_prod_delivery_mst where company_id=$cbo_company_id and entry_form=56 and $year_cond=" . date('Y', time()) . " order by id desc ", "sys_number_prefix", "sys_number_prefix_num"));
		$new_mrr_number = explode("*", return_next_id_by_sequence("PRO_GREY_PROD_DELI_MST_PK_SEQ", "pro_grey_prod_delivery_mst",$con,1,$cbo_company_id,'GDSR',56,date("Y",time()) ));

		//$id = return_next_id("id", "pro_grey_prod_delivery_mst", 1);
		$field_array = "id,sys_number_prefix,sys_number_prefix_num,sys_number,delevery_date,company_id,location_id,knitting_source,knitting_company,entry_form,remarks,attention,floor_ids,inserted_by,insert_date,barcode_type";
		//$field_array="id,sys_number_prefix,sys_number_prefix_num,sys_number,company_id,location_id,knitting_source,knitting_company,entry_form,remarks,inserted_by,insert_date";
		$data_array = "(" . $id . ",'" . $new_mrr_number[1] . "'," . $new_mrr_number[2] . ",'" . $new_mrr_number[0] . "'," . $txt_delivery_date . "," . $cbo_company_id . "," . $cbo_location_id . "," . $cbo_knitting_source . "," . $knit_company_id . ",56," . $txt_remarks . "," . $txt_attention . "," . $txt_floor_no . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $cbo_barcode_type . ")";

		$field_array_dtls = "id, mst_id, entry_form, grey_sys_id, sys_dtls_id, product_id, order_id, determination_id, roll_id, barcode_num, current_delivery, inserted_by, insert_date";
		//$dtls_id = return_next_id("id", "pro_grey_prod_delivery_dtls", 1);

		//$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);

		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, booking_no, booking_without_order, inserted_by, insert_date,is_sales";
		//$id_roll = return_next_id("id", "pro_roll_details", 1);
		//$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);


		$all_barcode="";
		for ($k = 1; $k <= $tot_row; $k++)
		{
			$barcodeNo = "barcodeNo_" . $k;
			$all_barcode .= $$barcodeNo . ",";
		}
		$all_barcode = chop($all_barcode, ",");
		if($all_barcode!="")
		{
			$barcodeNumbersArr=array_unique(explode(",",$all_barcode));
			if($db_type==2 && count($barcodeNumbersArr)>999)
			{
				$barcode_cond=" and (";
				$barcodeNumbersArr=array_chunk($barcodeNumbersArr,999);
				foreach($barcodeNumbersArr as $barcode)
				{
					$barcodes=implode(",",$barcode);
					$barcode_cond.=" b.barcode_no in($barcodes) or ";
				}

				$barcode_cond=chop($barcode_cond,'or ');
				$barcode_cond.=")";
			}
			else
			{
				$barcode_cond=" and b.barcode_no in (".implode(",",$barcodeNumbersArr).")";
			}
		}
		$sql_result = sql_select("SELECT a.sys_number, b.id,b.dtls_id,b.barcode_no from pro_grey_prod_delivery_mst a,pro_roll_details b where a.id=b.mst_id $barcode_cond and b.entry_form=56 and a.status_active=1 and b.status_active=1 and b.is_deleted=0");

		$dublicateBarcod = "";
		foreach ($sql_result as $b_row) {
			if($dublicateBarcod==""){
				$dublicateBarcod = $b_row[csf('barcode_no')];
				$challan_no = $b_row[csf('sys_number')];
			} else {
				$dublicateBarcod .=",".$b_row[csf('barcode_no')];
				$challan_no .=",". $b_row[csf('sys_number')];
			}
		}
		$challan_no = implode(",",array_unique(explode(",",$challan_no)));
		if ($dublicateBarcod != "") {
			echo "11**Barcode Already Scanned.\nBarcode No = ".$dublicateBarcod."\nChallan No = ".$challan_no;
			disconnect($con);
			die;
		}

		$barcodeNos = '';
		$used_roll_ids = '';
		for ($j = 1; $j <= $tot_row; $j++) {

			$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

			$productionId = "productionId_" . $j;
			$productionDtlsId = "productionDtlsId_" . $j;
			$productId = "productId_" . $j;
			$orderId = "orderId_" . $j;
			$deterId = "deterId_" . $j;
			$rollId = "rollId_" . $j;
			$barcodeNo = "barcodeNo_" . $j;
			$currentDelivery = "currentDelivery_" . $j;
			$rollNo = "rollNo_" . $j;
			$bookingWithoutOrder = "bookingWithoutOrder_" . $j;
			$smnBookingNo = "smnBookingNo_" . $j;
			$isSales = "isSales_" . $j;
			//$all_barcode .= $$barcodeNo . ",";

			if ($$bookingWithoutOrder == 1) $booking_no = $$smnBookingNo; else $booking_no = '';

			if ($data_array_dtls != "") $data_array_dtls .= ",";
			$data_array_dtls .= "(" . $dtls_id . "," . $id . ",56," . $$productionId . ",'" . $$productionDtlsId . "','" . $$productId . "','" . $$orderId . "','" . $$deterId . "','" . $$rollId . "','" . $$barcodeNo . "','" . $$currentDelivery . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

			if ($data_array_roll != "") $data_array_roll .= ",";
			$data_array_roll .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $id . "," . $dtls_id . ",'" . $$orderId . "',56,'" . $$currentDelivery . "','" . $$rollNo . "','" . $$rollId . "','" . $booking_no . "','" . $$bookingWithoutOrder . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',".$$isSales.")";
			//$id_roll = $id_roll + 1;

			$used_roll_ids .= $$rollId . ",";
			$barcodeNos .= $$barcodeNo . "__" . $dtls_id . "__" . number_format($$currentDelivery, 2) . ",";
			//$dtls_id = $dtls_id + 1;
		}

		/*$all_barcode_arr=array_unique(explode(",",chop($all_barcode,",")));*/


		//echo "10**$data_array_roll";die;

		//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
		//echo "10**insert into pro_grey_prod_delivery_mst (".$field_array.") values ".$data_array;die;

		$rID = $rID2 = $rID3 = $statusUsed = true;
		$rID = sql_insert("pro_grey_prod_delivery_mst", $field_array, $data_array, 0);
		$rID2 = sql_insert("pro_grey_prod_delivery_dtls", $field_array_dtls, $data_array_dtls, 0);
		$rID3 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
		$used_roll_ids = chop($used_roll_ids, ",");
		$statusUsed = sql_multirow_update("pro_roll_details", "roll_used", 1, "id", $used_roll_ids, 0);

		//echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$statusUsed;die;

		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $statusUsed && ($dublicateBarcod == "")) {
				mysql_query("COMMIT");
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $statusUsed && ($dublicateBarcod == "")) {
				oci_commit($con);
				echo "0**" . $id . "**" . $new_mrr_number[0] . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}
		/*if (check_table_status($_SESSION['menu_id'], 1) == 0) {
			echo "15**1";
			die;
		}*/
		if(str_replace("'", '', $update_id)=="")
		{
			echo "20**System ID not found";
			die;
		}

		$field_array = "delevery_date*location_id*knitting_source*knitting_company*remarks*attention*updated_by*update_date";
		$data_array = $txt_delivery_date . "*" . $cbo_location_id . "*" . $cbo_knitting_source . "*" . $knit_company_id . "*" . $txt_remarks . "*" . $txt_attention . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$field_array_dtls = "id, mst_id, entry_form, grey_sys_id, sys_dtls_id, product_id, order_id, determination_id, roll_id, barcode_num, current_delivery, inserted_by, insert_date";
		//$dtls_id = return_next_id("id", "pro_grey_prod_delivery_dtls", 1);
		$field_array_update = "updated_by*update_date*status_active*is_deleted";
		$field_array_roll = "id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, booking_no, booking_without_order, inserted_by, insert_date,is_sales";
		//$id_roll = return_next_id("id", "pro_roll_details", 1);
		//echo "11**".$tot_row ;die;
		$barcodeNos = '';
		$used_roll_ids = '';
		$data_array_roll='';

		for ($j = 1; $j <= $tot_row; $j++) {
			$dtls_id = return_next_id_by_sequence("PRO_GREY_PROD_DELI_DTLS_PK_SEQ", "pro_grey_prod_delivery_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

			$productionId = "productionId_" . $j;
			$productionDtlsId = "productionDtlsId_" . $j;
			$productId = "productId_" . $j;
			$orderId = "orderId_" . $j;
			$deterId = "deterId_" . $j;
			$rollId = "rollId_" . $j;
			$barcodeNo = "barcodeNo_" . $j;
			$currentDelivery = "currentDelivery_" . $j;
			$dtlsId = "dtlsId_" . $j;
			$rollNo = "rollNo_" . $j;
			$bookingWithoutOrder = "bookingWithoutOrder_" . $j;
			$smnBookingNo = "smnBookingNo_" . $j;
			$isSales = "isSales_" . $j;
			$all_barcode .= $$barcodeNo . ",";
			$all_dtlsId .= $$dtlsId . ",";
			$all_dtlsId_arr[$$dtlsId]=$$dtlsId;


			if ($$dtlsId > 0)
			{
				$dtlsId_arr[$$dtlsId] = $$dtlsId;
				$data_array_update[str_replace("'","",$$dtlsId)] = explode("*", ( $_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0"));

				$barcode_dtls_arr[$$barcodeNo]['dtls_id'] = $$dtlsId;
				$barcode_dtls_arr[$$barcodeNo]['qty'] = $$currentDelivery;

				$barcodeNos .= $$barcodeNo . "__" . $$dtlsId . "__" . number_format($$currentDelivery, 2) . ",";
				$dtls_id_for_roll = $$dtlsId;
				$rID4=1;
				/*$receive_barcode = return_field_value("id", "pro_roll_details", "barcode_no in(".$$barcodeNo.") and entry_form=58 and status_active=1", "id");
				if ($receive_barcode != "") {
					echo "11**Update Not Allow, Roll Already Receive.";
					die;
				}*/
			}
			else
			{
				if ($data_array_dtls != "") $data_array_dtls .= ",";
				$data_array_dtls .= "(" . $dtls_id . "," . $update_id . ",56," . $$productionId . ",'" . $$productionDtlsId . "','" . $$productId . "','" . $$orderId . "','" . $$deterId . "','" . $$rollId . "','" . $$barcodeNo . "','" . $$currentDelivery . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$barcodeNos .= $$barcodeNo . "__" . $dtls_id . "__" . number_format($$currentDelivery, 2) . ",";
				$dtls_id_for_roll = $dtls_id;

				if ($$bookingWithoutOrder == 1) $booking_no = $$smnBookingNo; else $booking_no = '';
				if ($data_array_roll != "") $data_array_roll .= ",";
				$data_array_roll .= "(" . $id_roll . ",'" . $$barcodeNo . "'," . $update_id . "," . $dtls_id_for_roll . ",'" . $$orderId . "',56,'" . $$currentDelivery . "','" . $$rollNo . "','" . $$rollId . "','" . $booking_no . "','" . $$bookingWithoutOrder . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',".$$isSales.")";
			}



			$used_roll_ids .= $$rollId . ",";


			//$id_roll = $id_roll + 1;
			//$all_barcode .= $$barcodeNo . ",";
		}

		/*echo "10**"; die;
		echo $data_array_roll; die;*/
		$all_barcode = chop($all_barcode, ",");
		if($all_barcode!="")
		{
			$barcodeNumbersArr=array_unique(explode(",",$all_barcode));
			if($db_type==2 && count($barcodeNumbersArr)>999)
			{
				$barcode_cond=" and (";
				$barcodeNumbersArr=array_chunk($barcodeNumbersArr,999);
				foreach($barcodeNumbersArr as $barcode)
				{
					$barcodes=implode(",",$barcode);
					$barcode_cond.=" b.barcode_no in($barcodes) or ";
				}

				$barcode_cond=chop($barcode_cond,'or ');
				$barcode_cond.=")";
			}
			else
			{
				$barcode_cond=" and b.barcode_no in (".implode(",",$barcodeNumbersArr).")";
			}
		}

		$all_dtlsId = ltrim($all_dtlsId, ",");
		$all_dtlsId = chop($all_dtlsId, ",");
		if($all_dtlsId!="")
		{
			$dtls_idsArr=array_unique(explode(",",$all_dtlsId));
			if($db_type==2 && count($dtls_idsArr)>999)
			{
				$dtls_id_cond=" and (";
				$dtls_idsArr=array_chunk($dtls_idsArr,999);
				foreach($dtls_idsArr as $dtls_id)
				{
					$dtls_idss=implode(",",$dtls_id);
					$dtls_id_cond.=" b.dtls_id not in($dtls_idss) or ";
				}

				$dtls_id_cond=chop($dtls_id_cond,'or ');
				$dtls_id_cond.=")";
			}
			else
			{
				$dtls_id_cond=" and b.dtls_id not in (".implode(",",$dtls_idsArr).")";
			}
		}
		//echo "10**<pre>";print_r($all_dtlsId_arr);die;
		// echo "11**SELECT a.sys_number, b.id,b.dtls_id,b.barcode_no from pro_grey_prod_delivery_mst a,pro_roll_details b where a.id=b.mst_id $barcode_cond $dtls_id_cond and b.entry_form=56 and a.status_active=1 and b.status_active=1 and b.is_deleted=0";die;
		$sql_result = sql_select("SELECT a.sys_number, b.id,b.dtls_id,b.barcode_no from pro_grey_prod_delivery_mst a,pro_roll_details b where a.id=b.mst_id $barcode_cond  and b.entry_form=56 and a.status_active=1 and b.status_active=1 and b.is_deleted=0");

		$dublicateBarcod = "";
		foreach ($sql_result as $b_row) 
		{
			// echo $all_dtlsId_arr[$b_row[csf('dtls_id')]].'==<br>';
			if ($all_dtlsId_arr[$b_row[csf('dtls_id')]] == "") 
			{
				if($dublicateBarcod=="")
				{
					$dublicateBarcod = $b_row[csf('barcode_no')];
					$challan_no = $b_row[csf('sys_number')];
				} 
				else 
				{
					$dublicateBarcod .=",".$b_row[csf('barcode_no')];
					$challan_no .=",". $b_row[csf('sys_number')];
				}
			}			
		}		

		if ($dublicateBarcod != "") {
			echo "11**Barcode Already Scanned.\nBarcode No = ".$dublicateBarcod."\nChallan No = ".$challan_no;
			//check_table_status($_SESSION['menu_id'], 0);
			disconnect($con);
			die;
		}
		// echo "10**string";die;

		if (str_replace("'", "", $txt_deleted_barcode)!="")
		{
			$receive_barcode_no = return_field_value("id", "pro_roll_details", "barcode_no in(".str_replace("'", "", $txt_deleted_barcode).") and entry_form=58 and status_active=1", "id");
		}

		/*echo "10**".$receive_barcode_no."=".$txt_deleted_barcode;
		oci_rollback($con);
		die;*/

		if ($receive_barcode_no != "") {
			echo "11**Update Not Allow, Roll Already Receive.";
			disconnect($con);
			die;
		}

		//echo "insert into com_export_proceed_rlzn_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
		$rID = sql_update("pro_grey_prod_delivery_mst", $field_array, $data_array, "id", $update_id, 0);

		$rID2 = true;
		$rID3 = true;
		$statusChange = true;
		$statusNotUsed = true;
		/*if (count($data_array_update) > 0) {
			$rID2 = execute_query(bulk_update_sql_statement("pro_grey_prod_delivery_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr));
			//echo "10**".bulk_update_sql_statement( "pro_grey_prod_delivery_dtls", "id", $field_array_update, $data_array_update, $dtlsId_arr );die;
		}*/

		$data_array_update_chunk=array_chunk($data_array_update,50,true);
		$dtlsId_arr_up_arr=array_chunk($dtlsId_arr,50,true);
		$count_up_dtls_id=count($dtlsId_arr_up_arr);
		for ($i=0;$i<$count_up_dtls_id;$i++)
		{
			//echo "10**".bulk_update_sql_statement( "pro_grey_prod_delivery_dtls", "id", $field_array_update, $data_array_update_chunk[$i], array_values($dtlsId_arr_up_arr[$i]) );
			$rID2=execute_query(bulk_update_sql_statement( "pro_grey_prod_delivery_dtls", "id", $field_array_update, $data_array_update_chunk[$i], array_values($dtlsId_arr_up_arr[$i] )),1);

			if($rID2 != "1" )
			{
				oci_rollback($con);
				echo "6**0**1";
				disconnect($con);
				die;
			}
		}
		// echo "10**string";die;

		//$delete_roll=true;
		$txt_deleted_id = str_replace("'", "", $txt_deleted_id);
		if ($txt_deleted_id != "") {
			$field_array_status = "updated_by*update_date*status_active*is_deleted";
			$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
			$statusChange = sql_multirow_update("pro_grey_prod_delivery_dtls", $field_array_status, $data_array_status, "id", $txt_deleted_id, 0);
			$delete_roll =execute_query("update pro_roll_details set status_active=0, is_deleted=1, updated_by=".$_SESSION['logic_erp']['user_id'].", update_date='".$pc_date_time."' where dtls_id in($txt_deleted_id) and entry_form=56", 0);
		}

		if ($data_array_dtls != "") {
			$rID3 = sql_insert("pro_grey_prod_delivery_dtls", $field_array_dtls, $data_array_dtls, 1);
		}

		/*$field_array_status="updated_by*update_date*status_active*is_deleted";
		$data_array_status=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$statusChange=sql_multirow_update("pro_grey_prod_delivery_dtls",$field_array_status,$data_array_status,"mst_id",$update_id,0);*/
		//$delete_roll = execute_query("delete from pro_roll_details where dtls_id in($txt_deleted_id) and entry_form=56", 0);


		$txt_deleted_roll_id = str_replace("'", "", $txt_deleted_roll_id);
		if ($txt_deleted_roll_id != "") {
			$statusNotUsed = sql_multirow_update("pro_roll_details", "roll_used", 0, "id", $txt_deleted_roll_id, 0);
		}
		if($data_array_roll != ""){
			$rID4 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
		}else{
			$rID4 = 1;
		}

		$used_roll_ids = chop($used_roll_ids, ",");
		$statusUsed = sql_multirow_update("pro_roll_details", "roll_used", 1, "id", $used_roll_ids, 0);


		/*echo "10**".$rID."&&".$rID2."&&".$rID3."&&".$rID4."&&".$statusChange."&&".$statusUsed."&&".$statusNotUsed."==".$delete_roll;
		oci_rollback($con);
		die;*/


		if ($db_type == 0) {
			if ($rID && $rID2 && $rID3 && $rID4 && $statusChange && $statusNotUsed) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_challan_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				mysql_query("ROLLBACK");
				echo "6**" . str_replace("'", '', $update_id) . "**";
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $rID2 && $rID3 && $rID4 && $statusChange && $statusNotUsed) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_challan_no) . "**" . substr($barcodeNos, 0, -1);
			} else {
				oci_rollback($con);
				echo "6**" . str_replace("'", '', $update_id) . "**1";
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	}
}

if ($action == "challan_popup")
{
	echo load_html_head_contents("Challan Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(data) {
			$('#hidden_data').val(data);
			parent.emailwindow.hide();
		}
		function fnc_floor_load(data)
		{
			//alert(data);
			if(data==3)
			{
				$("#search_by_td input").remove();
				var text='<? echo create_drop_down("txt_search_common", 150, "SELECT id, floor_name from lib_prod_floor where  status_active=1 and is_deleted=0 and production_process=2   ", 'id,floor_name', 1, '-- Select Floor --', 0, "", 0); ?>';
				$("#search_by_td").html(text);

			}
		}

		function fnc_show()
		{

			if($("#txt_search_common").val().trim()=="")
			{
				if (form_validation('cbo_company_id*txt_date_from*txt_date_to', 'Company*From Date*To Date') == false)
				{
					return;
				}
			}
			else
			{
				if (form_validation('cbo_company_id', 'Company') == false)
				{
					return;
				}
			}



			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_year_selection').value, 'create_challan_search_list_view', 'search_div', 'grey_feb_delivery_roll_wise_entry_controller', 'setFilterGrid(\'tbl_list_search\',-1);');
		}

	</script>

</head>

<body>
	<div align="center" style="width:860px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:860px; margin-left:2px">
				<table cellpadding="0" cellspacing="0" width="750" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Company</th>
						<th>Delivery Date Range</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="180">Please Enter Challan No</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
							class="formbutton"/>
							<input type="hidden" name="hidden_data" id="hidden_data">
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<? echo create_drop_down("cbo_company_id", 150, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 and comp.core_business not in(3) $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', 0, "", 0); ?>
						</td>
						<td align="center">
							<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker"
							style="width:70px" readonly>To
							<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
							readonly>
						</td>
						<td align="center">
							<?
							$search_by_arr = array(1 => "Challan No", 2 => "Barcode No",3=>"Floor Name");
							$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../');fnc_floor_load(this.value); ";
							echo create_drop_down("cbo_search_by", 100, $search_by_arr, "", 0, "--Select--", 1, $dd, 0);
							?>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
							id="txt_search_common"/>
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show"
							onClick="fnc_show();"
							style="width:100px;"/>
						</td>
					</tr>
					<tr>
						<td colspan="5" align="center" height="40"
						valign="middle"><? echo load_month_buttons(1); ?></td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action == "create_challan_search_list_view")
{
	$data = explode("_", $data);
	$search_string = $data[0];
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$year_selection = $data[5];
	if($search_by==3)
	{
		if($search_string>0)
		{
			$floors=sql_select("select id,floor_name from lib_prod_floor where company_id='$company_id' and id =$data[0]");
			foreach ($floors as $key => $value)
			{
				$fl_arr[$value[csf("id")]]=$value[csf("id")];
			}
			$all_floor="'".implode("','", $fl_arr)."'";
			$floor_conds=" and a.floor_ids in($all_floor)   ";
		}
		else
		{
			$floor_conds="";
		}
	}

	if ($company_id == 0) {
		echo "Please Select Company First.";
		die;
	}

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and delevery_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and delevery_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	if ($db_type == 2) {
		$group_con = "LISTAGG(mst_id, ',') WITHIN GROUP (ORDER BY mst_id desc) as mst_id";
	} else {
		$group_con = "group_concat(mst_id order by mst_id desc) as mst_id";
	}

	if ($search_by == 2) {
		$barcode_no = trim($data[0]);
		$mst_id = '';
		if ($barcode_no != '') {
			$mst_id = return_field_value("$group_con", "pro_grey_prod_delivery_dtls", "barcode_num=$barcode_no and entry_form=56 and status_active=1 and is_deleted=0 ", "mst_id");
		}
	}

	$search_field_cond = "";
	if (trim($data[0]) != "")
	{
		if ($search_by == 1) $search_field_cond = "and sys_number_prefix_num like '$search_string'";
		else if ($search_by == 2 && $mst_id != "") $search_field_cond = "and a.id in($mst_id)";
	}

	$year_cond = "";
	if (trim($year_selection) != 0) {
		if ($db_type == 0)
			$year_cond = " and YEAR(a.insert_date)=$year_selection";
		else if ($db_type == 2)
			$year_cond = " and to_char(a.insert_date,'YYYY')=$year_selection";
		else
			$year_cond = "";
	}

	/*$sql = "select a.id, sys_number_prefix_num, a.sys_number,sum(b.current_delivery) current_delivery, a.company_id, a.knitting_source, a.knitting_company, a.location_id,a.remarks, a.delevery_date,a.floor_ids,a.insert_date from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.entry_form=56 and a.company_id=$company_id $search_field_cond $date_cond $floor_conds group by a.id, sys_number_prefix_num, a.sys_number,a.company_id, a.knitting_source, a.knitting_company, a.location_id,a.remarks, a.delevery_date,a.floor_ids,a.insert_date order by sys_number_prefix_num asc";*/
	$sql = "select a.id, sys_number_prefix_num, a.sys_number, sum(c.qnty) as  current_delivery, a.company_id, a.knitting_source, a.knitting_company, a.location_id,a.remarks,attention, a.delevery_date,a.floor_ids,a.insert_date, a.barcode_type
  from pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b,pro_roll_details c
  where a.id=b.mst_id and
 b.id=c.dtls_id and b.mst_id=c.mst_id and b.order_id=c.po_breakdown_id and c.status_active=1 and c.status_active=1 and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.entry_form=56 and c.entry_form=56 and a.company_id=$company_id $search_field_cond $date_cond $floor_conds $year_cond
 group by a.id, sys_number_prefix_num, a.sys_number,a.company_id, a.knitting_source, a.knitting_company, a.location_id,a.remarks, a.delevery_date,a.floor_ids,a.insert_date, a.barcode_type,a.attention
 order by sys_number_prefix_num asc";
	$result = sql_select($sql);

	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$supllier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$location_arr = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	//$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="130">Company</th>
			<th width="100">Location</th>
			<th width="100">Knitting Floor</th>
			<th width="70">Challan No</th>
			<th width="60">Year</th>
			<th width="90">Knitting Source</th>
			<th width="130">Knitting Company</th>
			<th width="80">Delivery Date</th>
			<th width="80">Delivery Qnty</th>
		</thead>
	</table>
	<div style="width:950px; max-height:218px; overflow-y:scroll" id="list_conatiner_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="930" class="rpt_table"
		id="tbl_list_search">
		<?
		$i = 1;
		foreach ($result as $row) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			$knit_comp = "&nbsp;";
			if ($row[csf('knitting_source')] == 1)
				$knit_comp = $company_arr[$row[csf('knitting_company')]];
			else
				$knit_comp = $supllier_arr[$row[csf('knitting_company')]];

			$data = $row[csf('id')] . "**" . $row[csf('sys_number')] . "**" . change_date_format($row[csf('delevery_date')]) . "**" . $row[csf('company_id')] . "**" . $row[csf('location_id')] . "**" . $row[csf('knitting_source')] . "**" . $row[csf('knitting_company')] . "**" . $knit_comp . "**" . $row[csf('remarks')] . "**" . $row[csf('floor_ids')] . "**" . $row[csf('barcode_type')] . "**" . $row[csf('attention')];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
				onClick="js_set_value('<? echo $data; ?>');">
				<td width="40" align="center"><? echo $i; ?></td>
				<td width="130" align="center"><? echo $company_arr[$row[csf('company_id')]]; ?></td>
				<td width="100"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
				<td width="100">
					<p>
						<?
						$floorIdArr =array();$field_ids="";
						$floorIdArr = explode(",", $row[csf('floor_ids')]);
						foreach ($floorIdArr as $floor_id) {
							$field_ids .= $floor_name_arr[$floor_id].",";
						}
						$field_ids = chop($field_ids,",");
						echo $field_ids;
						?>
					</p>
				</td>
				<td width="70" align="center"><? echo $row[csf('sys_number_prefix_num')]; ?></td>
				<td width="60" align="center"><? echo date("Y",strtotime($row[csf('insert_date')])); ?></td>
				<td width="90" align="center"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
				<td width="130" align="center"><? echo $knit_comp; ?></td>
				<td width="80" align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
				<td width="80" align="right"><? echo number_format($row[csf('current_delivery')],2,".",""); ?></td>
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

if ($action == "barcode_popup")
{
	echo load_html_head_contents("Barcode Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST); //echo $floor_id;
	if ($company_id > 0){
		$disable = 1;
	}
	else{
		$disable = 0;
	}

	?>
	<script>
		var selected_id = new Array();
		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i );
			}
		}

		function js_set_value(str)
		{
			if( $("#search"+str).css("display") != "none" )
			{
				if($("#qc_failed"+str).val() ==0)
				{
					toggle(document.getElementById('search' + str), '#FFFFCC');
					var total_selected_val=$('#hidden_selected_row_total').val()*1;// txt_individual_qty

					if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1)
					{
						selected_id.push($('#txt_individual_id' + str).val());
						total_selected_val=total_selected_val+$('#txt_individual_qty' + str).val()*1;

					}
					else
					{
						for (var i = 0; i < selected_id.length; i++)
						{
							if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
						}
						selected_id.splice(i, 1);
						total_selected_val=total_selected_val-$('#txt_individual_qty' + str).val()*1;
					}
					var id = '';
					for (var i = 0; i < selected_id.length; i++)
					{
						id += selected_id[i] + ',';
					}
					id = id.substr(0, id.length - 1);

					$('#hidden_barcode_nos').val(id);
					$('#hidden_selected_row_total').val( total_selected_val.toFixed(2));

					if(id!=""){
					var no_of_roll = id.split(',').length;
					}else{
						var no_of_roll = "0";
					}
					$('#hidden_selected_row_count').val(no_of_roll);
				}
			}
		}

		function qc_alert(str)
		{
			if($("#qc_failed"+str).val() ==1){
				alert("Barcode Not QC passed yet.");
				return;
			}

		}

		function fnc_close()
		{
			parent.emailwindow.hide();
		}

		function reset_hide_field()
		{
			$('#hidden_barcode_nos').val('');
			selected_id = new Array();
		}

		function fnc_list_generate()
		{

			var fromDate=$('#txt_date_from_knit').val();
			var toDate=$('#txt_date_to_knit').val();
			var diffDays=date_diff( "d", fromDate, toDate  );
			if(diffDays>15)
			{
				alert("Not allowed more than 15 days from start date");
				return;
			}

			if( form_validation('cbo_company_id','Company Name')==false)
			{
				return;
			}
			else
			{
				show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_location_id').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('txt_file_no').value+'_'+document.getElementById('txt_ref_no').value+'_'+document.getElementById('txt_sales_order_no').value+'_'+document.getElementById('txt_booking_no').value+'_'+document.getElementById('chkIsSales').checked+'_'+document.getElementById('cbo_floor_id').value+'_'+document.getElementById('cbo_knitting_source').value+'_'+document.getElementById('cbo_workingcompany_id').value+'_'+document.getElementById('txt_date_from_knit').value+'_'+document.getElementById('txt_date_to_knit').value+'_'+'<?php echo $barcode_type; ?>', 'create_barcode_search_list_view', 'search_div', 'grey_feb_delivery_roll_wise_entry_controller', 'setFilterGrid("tbl_list_search",-1); reset_hide_field();');
			}
		}

		function disable_enable_check_box(company_id)
		{
			var url = "grey_feb_delivery_roll_wise_entry_controller.php?action=check_box&company_id="+company_id;
			
			fetch(url)
			.then((response) => {
				return response.text();
			})
			.then((data) => {
			if (data==1) {
                document.getElementById("chkIsSales").checked = true;
            } else {
                document.getElementById("chkIsSales").checked = false;
            }

			})
			
		}

	</script>
</head>
<body>
	<div align="center" style="width:1370px;">
		<form name="searchwofrm" id="searchwofrm">
			<fieldset style="width:1360px;">
				<!--<legend>Enter search words</legend>-->
				<table cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
					<thead>
						<th class="must_entry_caption">Company</th>
						<th>Source</th>
						<th class="must_entry_caption">Working Company</th>
						<th>Location</th>
						<th>Floor</th>
						<th>Order No</th>
						<th>File No</th>
						<th>Ref. No</th>
						<th>Sales Order No</th>
						<th>Booking No</th>
						<th title="Knitting Production Date Range">Date Range</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton"/>
							<input type="hidden" name="hidden_barcode_nos" id="hidden_barcode_nos">
						</th>
					</thead>
					<tr class="general">
						<td align="center">
							<? echo create_drop_down("cbo_company_id", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name", 'id,company_name', 1, '-- Select Company --', $company_id, "disable_enable_check_box(this.value)", $disable); //load_drop_down( 'grey_feb_delivery_roll_wise_entry_controller', this.value, 'load_drop_down_location', 'location_td' ); load_drop_down( 'grey_feb_delivery_roll_wise_entry_controller', this.value+'__0', 'load_drop_down_floor', 'floor_td' );?>
						</td>
						<td align="center">
							<?
							echo create_drop_down("cbo_knitting_source",120,$knitting_source,"", 1, "-- Select --", 0,"load_drop_down( 'grey_feb_delivery_roll_wise_entry_controller', this.value+'_'+document.getElementById('cbo_company_id').value, 'load_drop_down_knitting_com','knitting_com');",0,'1,3');
							?>
						</td>
						<td align="center" id="knitting_com">
							<? echo create_drop_down("cbo_workingcompany_id", 130, $blank_array, "", 1, "-Knit Company-", 0, ""); ?>
						</td>
						<td align="center" id="location_td">
							<? if ($company_id > 0) {
								echo create_drop_down("cbo_location_id", 130, "select id,location_name from lib_location where company_id='$company_id' and status_active =1 and is_deleted=0 order by location_name", "id,location_name", 1, "-- Select --", $location_id, "", 1);
							} else {
								echo create_drop_down("cbo_location_id", 130, $blank_array, "", 1, "-- Select --", $selected, "", 1, "");
							}
							?>
						</td>
						<td align="center" id="floor_td">
							<? if ($location_id > 0) {
								echo create_drop_down("cbo_floor_id", 130, "select a.id, a.floor_name from lib_prod_floor a, lib_machine_name b where a.id=b.floor_id and b.category_id=1 and b.company_id='$company_id' and b.status_active=1 and b.is_deleted=0 and a.production_process=2 and a.location_id='$location_id' group by a.id, a.floor_name order by a.floor_name", "id,floor_name", 1, "-- Select --", $floor_id, "", 1);
							} else {
								echo create_drop_down("cbo_floor_id", 130, $blank_array, "", 1, "-- Select --", $selected, "", 1, "");
							}
							?>
						</td>
						<td align="center">
							<input type="text" style="width:80px" class="text_boxes" name="txt_order_no" id="txt_order_no"/>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:80px" class="text_boxes" name="txt_file_no" id="txt_file_no"/>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:80px" class="text_boxes" name="txt_ref_no" id="txt_ref_no"/>
						</td>
						<td align="center" id="search_by_td">
							<input type="text" style="width:80px" class="text_boxes" name="txt_sales_order_no" id="txt_sales_order_no"/>
						</td>
						<td align="center" id="search_by_td">
							
							<input type="text" style="width:80px" class="text_boxes" name="txt_booking_no" id="txt_booking_no"/><br />
							<input type="checkbox" name="chkIsSales" id="chkIsSales"/> <label for="chkIsSales">Is sales order </label>
						</td>
						<td>
							<input type="text" name="txt_date_from_knit" id="txt_date_from_knit" class="datepicker" style="width:55px;">To<input type="text" name="txt_date_to_knit" id="txt_date_to_knit" class="datepicker" style="width:55px;">
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="fnc_list_generate();" style="width:70px;"/>
						</td>
					</tr>
				</table>
				<div style="width:100%; margin-top:5px;" id="search_div" align="left"></div>
			</fieldset>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if ($action == "create_barcode_search_list_view")
{
	$data = explode("_", $data);
	$company_id = $data[0];
	$location_id = $data[1];
	$order_no = trim($data[2]);
	$file_no = trim($data[3]);
	$ref_no = trim($data[4]);
	$sales_order_no = trim($data[5]);
	$sales_booking = trim($data[6]);
	$is_sales = trim($data[7]);
	$floor_id = $data[8];
	$source_id = trim($data[9]);
	$working_company_id = trim($data[10]);
	$start_date =$data[11];
	$end_date =$data[12];
	$barcode_type =$data[13];
	//echo $ref_no;die;
	if($order_no=="" && $file_no =="" && $ref_no =="" && $sales_order_no =="" && $sales_booking ==""  && $start_date ==""  && $end_date =="")
	{
		echo "Please Fill Up At Least One Roll Information.";
		die;
	}

	$search_string = "%" . trim($sales_order_no) . "%";
	$booksearch_string = "%" . trim($sales_booking) . "%";
	$company_arr = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");


	if($start_date!="" && $end_date!="")
	{
		if($db_type==0)
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),"yyyy-mm-dd","-")."' and '".change_date_format(trim($end_date),"yyyy-mm-dd","-")."'";
		}
		else
		{
			$date_cond="and a.receive_date between '".change_date_format(trim($start_date),'','',1)."' and '".change_date_format(trim($end_date),'','',1)."'";
		}


	}
	else
	{
		$date_cond="";
	}

	if ($company_id == 0)
	{
		echo "Please Select Company First.";
		die;
	}
	$location_id == 0 ? $location_id = "" : $location_id = "and a.location_id='$location_id'";
	$floor_id == 0 ? $floor_id = "" : $floor_id_cond = "and b.floor_id='$floor_id'";
	$order_no == "" ? $order_no = "" : $order_no = "and d.po_number like '%" . $order_no . "%'";
	$file_no == "" ? $file_no = "" : $file_no = "and d.file_no='$file_no'";
	$ref_no == "" ? $ref_no = "" : $ref_no = "and d.grouping like '%" . $ref_no . "%'";

	$source_id == 0 ? $source_id_cond = "" : $source_id_cond = "and a.knitting_source='$source_id'";
	$working_company_id == 0 ? $working_company_id_cond = "" : $working_company_id_cond = "and a.knitting_company='$working_company_id'";

	if ($sales_order_no != "") $sales_order_cond = "and d.job_no_prefix_num like '$search_string'"; else $sales_order_cond = "";
	if ($is_sales == "true")
	{
		if ($sales_booking != "") $sales_booking_cond = "and d.sales_booking_no like '$booksearch_string'"; else $sales_booking_cond = "";
	}
	else
	{
		if ($sales_booking != "") $sales_booking_cond = "and a.booking_no like '$booksearch_string'"; else $sales_booking_cond = "";
		if ($sales_booking != "") $non_order_booking = "and d.booking_no like '$booksearch_string'"; else $non_order_booking = "";
	}

	//for barcode type
	$barcodeTypeCon='';
	if($barcode_type == 1)
	{
		$barcodeTypeCon=" and c.booking_without_order != 1";
	}
	else
	{
		$barcodeTypeCon=" and c.booking_without_order = 1";
	}

	$dtls_ids = implode(",", array_unique(explode(",", $dtls_id)));
	if ($dtls_ids != '') $dtls_ids_con = "and b.id in($dtls_ids)"; else $dtls_ids_con = "";
	if (($sales_order_no != "" && $is_sales == "false") || $sales_order_no != "" && $is_sales == "true")
	{
		$sales_order = 1;
		$sql = "SELECT a.recv_number_prefix_num, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, a.booking_no, b.shift_name, b.machine_no_id, b.floor_id, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, c.qc_pass_qnty_pcs, d.job_no as sales_order_no, d.sales_booking_no, d.within_group,a.receive_basis, c.roll_used, e.roll_status
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c left join pro_qc_result_mst e on c.barcode_no=e.barcode_no and e.status_active=1 and e.is_deleted=0, fabric_sales_order_mst d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.roll_used=0 and a.company_id=$company_id $location_id $source_id_cond $working_company_id_cond $floor_id_cond $order_no $file_no $ref_no $dtls_ids_con $sales_order_cond $sales_booking_cond $date_cond and c.barcode_no not in(select d.barcode_num from pro_grey_prod_delivery_dtls d where c.barcode_no=d.barcode_num and d.entry_form=56 and d.status_active=1 and d.is_deleted=0) $barcodeTypeCon order by a.knitting_source,  a.location_id, a.knitting_company"; //  c.roll_no>0 and round(c.qnty,2)>0
	}
	else if ($sales_order_no == "" && $is_sales == "true")
	{
		$sales_order = 1;
		$sql = "SELECT a.recv_number_prefix_num, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, a.booking_no, b.shift_name, b.machine_no_id, b.floor_id, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, c.qc_pass_qnty_pcs, d.job_no as sales_order_no, d.sales_booking_no, d.within_group ,a.receive_basis, c.roll_used, e.roll_status
		from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c left join pro_qc_result_mst e on c.barcode_no=e.barcode_no and e.status_active=1 and e.is_deleted=0, fabric_sales_order_mst d
		where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.roll_used=0 and a.company_id=$company_id $location_id $source_id_cond $working_company_id_cond $floor_id_cond $order_no $file_no $ref_no $dtls_ids_con $sales_order_cond $sales_booking_cond $date_cond and c.barcode_no not in(select d.barcode_num from pro_grey_prod_delivery_dtls d where c.barcode_no=d.barcode_num and d.entry_form=56 and d.status_active=1 and d.is_deleted=0) $barcodeTypeCon order by a.knitting_source,  a.location_id, a.knitting_company"; //  c.roll_no>0 and round(c.qnty,2)>0
	}
	else
	{
		//echo "found";
		$sales_order = 0;
		$pono_arr=array();
		if($start_date!="" && $end_date!="")
		{
			$sql_roll_po = sql_select("select c.po_breakdown_id, c.booking_without_order
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0
			and a.company_id=$company_id $location_id $source_id_cond $working_company_id_cond $floor_id_cond $date_cond and c.is_sales !=1
			$po_barcode_cond"); //  c.roll_no>0 and round(c.qnty,2)>0
			$roll_po_id="";
			foreach($sql_roll_po as $row)
			{
				$roll_po_id.=$row[csf("po_breakdown_id")].",";
			}
			$roll_po_id=chop($roll_po_id,",");
			$roll_po_id_cond="";$roll_without_ord_cond="";
			if($roll_po_id!="")
			{
				$roll_po_id_arr=array_unique(explode(",",$roll_po_id));
				if($db_type==2 && count($roll_po_id_arr)>999)
				{
					$roll_po_id_cond=" and (";
					$roll_without_ord_cond=" and (";
					$roll_po_id_arr=array_chunk($roll_po_id_arr,999);
					foreach($roll_po_id_arr as $ids)
					{
						$ids=implode(",",$ids);
						$roll_po_id_cond.=" d.id in($ids) or ";
						$roll_without_ord_cond.=" c.po_breakdown_id in($ids) or ";
					}

					$roll_po_id_cond=chop($roll_po_id_cond,'or ');
					$roll_po_id_cond.=")";

					$roll_without_ord_cond=chop($roll_without_ord_cond,'or ');
					$roll_without_ord_cond.=")";
				}
				else
				{
					$roll_po_id_cond=" and d.id in (".implode(",",$roll_po_id_arr).")";
					$roll_without_ord_cond=" and c.po_breakdown_id in (".implode(",",$roll_po_id_arr).")";
				}
			}
		}

		if ($order_no!="" || $file_no!="" || $ref_no!="" || $roll_po_id_cond!="")
		{
			$sql_po="select d.id, d.po_number, d.pub_shipment_date, d.job_no_mst, d.file_no, d.grouping from wo_po_break_down d where 1=1 $order_no $file_no $ref_no $roll_po_id_cond";
			//echo $sql_po;die;
			$sql_po_res=sql_select($sql_po); $poIds=''; $tot_rows=0;
			foreach($sql_po_res as $row)
			{
				$tot_rows++;
				$poIds.=$row[csf("id")].",";
				$pono_arr[$row[csf("id")]]['po']=$row[csf("po_number")];
				$pono_arr[$row[csf("id")]]['ship_date']=$row[csf("shipment_date")];
				$pono_arr[$row[csf("id")]]['job']=$row[csf("job_no_mst")];
				$pono_arr[$row[csf("id")]]['file']=$row[csf("file_no")];
				$pono_arr[$row[csf("id")]]['group']=$row[csf("grouping")];
			}
			unset($sql_po_res);

			//echo $poIds;die;

			$poIds=chop($poIds,',');  $knit_prod_po_cond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$knit_prod_po_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$poIds),999);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$knit_prod_po_cond.=" c.po_breakdown_id in($ids) or ";
				}

				$knit_prod_po_cond=chop($knit_prod_po_cond,'or ');
				$knit_prod_po_cond.=")";
			}
			else
			{
				$knit_prod_po_cond=" and c.po_breakdown_id in ($poIds)";
			}
		}


		if($start_date!="" && $end_date!="")
		{
			$sql = "SELECT a.recv_number_prefix_num, a.within_group, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, a.booking_no, a.buyer_id, b.shift_name, b.machine_no_id, b.floor_id, c.barcode_no, c.roll_no, c.qnty, c.booking_without_order, c.po_breakdown_id, c.qc_pass_qnty_pcs, b.body_part_id,a.receive_basis, c.roll_used, e.roll_status
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c left join pro_qc_result_mst e on c.barcode_no=e.barcode_no and e.status_active=1 and e.is_deleted=0
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.booking_without_order!=1 and c.roll_used=0
			and a.company_id=$company_id $location_id $source_id_cond $working_company_id_cond $floor_id_cond $sales_booking_cond $date_cond $knit_prod_po_cond and c.is_sales=0 $barcodeTypeCon
			union all
			select a.recv_number_prefix_num,a.within_group, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, a.booking_no, a.buyer_id, b.shift_name, b.machine_no_id,  b.floor_id, c.barcode_no, c.roll_no, c.qnty, c.booking_without_order, c.po_breakdown_id, c.qc_pass_qnty_pcs, b.body_part_id,a.receive_basis, c.roll_used, e.roll_status
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c left join pro_qc_result_mst e on c.barcode_no=e.barcode_no and e.status_active=1 and e.is_deleted=0, wo_non_ord_samp_booking_mst d
			where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.booking_without_order=1 and c.roll_used=0 and a.company_id=$company_id $location_id $source_id_cond $working_company_id_cond $floor_id_cond $non_order_booking $date_cond $roll_without_ord_cond and c.is_sales!=1 $barcodeTypeCon
			order by knitting_source, location_id, knitting_company"; //  c.roll_no>0  and round(c.qnty,2)>0
		}
		else
		{
			$sql = "SELECT a.recv_number_prefix_num, a.within_group, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, a.booking_no, a.buyer_id, b.shift_name, b.machine_no_id, b.floor_id, c.barcode_no, c.roll_no, c.qnty, c.booking_without_order, c.po_breakdown_id, c.qc_pass_qnty_pcs, b.body_part_id,a.receive_basis, c.roll_used, e.roll_status
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c left join pro_qc_result_mst e on c.barcode_no=e.barcode_no and e.status_active=1 and e.is_deleted=0
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.booking_without_order!=1 and c.roll_used=0
			and a.company_id=$company_id $location_id $source_id_cond $working_company_id_cond $floor_id_cond $sales_booking_cond $date_cond $knit_prod_po_cond and c.is_sales!=1 $barcodeTypeCon
			union all
			SELECT a.recv_number_prefix_num, a.within_group, a.location_id, a.knitting_source, a.knitting_company, a.receive_date, a.booking_no, a.buyer_id, b.shift_name, b.machine_no_id, b.floor_id, c.barcode_no, c.roll_no, c.qnty, c.booking_without_order, c.po_breakdown_id, c.qc_pass_qnty_pcs, b.body_part_id,a.receive_basis, c.roll_used, e.roll_status
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c left join pro_qc_result_mst e on c.barcode_no=e.barcode_no and e.status_active=1 and e.is_deleted=0, wo_non_ord_samp_booking_mst d
			where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id = d.id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.roll_no>0 and c.booking_without_order=1 and c.roll_used=0
			and a.company_id=$company_id $location_id $source_id_cond $working_company_id_cond $floor_id_cond $non_order_booking $date_cond $knit_prod_po_cond and c.is_sales!=1 $barcodeTypeCon order by knitting_source, location_id, knitting_company"; // c.roll_no>0  and round(c.qnty,2)>0
		}
	}
	//echo $sql;//die;

	$result = sql_select($sql);
	foreach ($result as $row)
	{
		if ($sales_order == 1 && $row[csf('within_group')] == 1)
		{
			$sales_within_group = true;
			$booking_no_arr[] = "'".$row[csf("booking_no")]."'";
		}
		else
		{
			$sales_within_group = false;
		}

		if($row[csf('receive_basis')] == 2){
			$plan_arr[] = $row[csf('booking_no')];
		}

		if($row[csf("is_sales")] == 1){
			$sales_ids[] = $row[csf('po_breakdown_id')];
		}else{
			$po_ids[] = $row[csf('po_breakdown_id')];
		}

		$barcodeNumbers .= $row[csf("barcode_no")].",";
	}
	$plan_id=implode(',',$plan_arr);
	if(!empty($sales_ids))
	{
		$sales_order_result = sql_select("select within_group,sales_booking_no,id po_id,job_no from fabric_sales_order_mst where id in(".implode(",",$sales_ids).") and status_active=1 and is_deleted=0");
		$sales_arr = array();
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("po_id")]]["job_no"] 			= $sales_row[csf("job_no")];
			$sales_arr[$sales_row[csf("po_id")]]["sales_booking_no"] 	= $sales_row[csf("sales_booking_no")];
			$sales_arr[$sales_row[csf("po_id")]]["within_group"] 		= $sales_row[csf("within_group")];
			$booking_nos[] = "'".$sales_row[csf("sales_booking_no")]."'";
		}
	}
	$plan_cond = ($plan_id != "")?" and a.id in ($plan_id)":"";
	$ppl_data = sql_select("select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $plan_cond");
	$plan_array=array();
	foreach ($ppl_data as $row)
	{
		$plan_array[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
	}

	/* $barcodeNumbers=chop($barcodeNumbers,",");
	if($barcodeNumbers!="")
	{
		$barcodeNumbersArr=array_unique(explode(",",$barcodeNumbers));
		if($db_type==2 && count($barcodeNumbersArr)>999)
		{
			$barcode_cond=" and (";
			$barcodeNumbersArr=array_chunk($barcodeNumbersArr,999);
			foreach($barcodeNumbersArr as $barcode)
			{
				$barcodes=implode(",",$barcode);
				$barcode_cond.=" c.barcode_no in($barcodes) or ";
			}

			$barcode_cond=chop($barcode_cond,'or ');
			$barcode_cond.=")";
		}
		else
		{
			$barcode_cond=" and c.barcode_no in (".implode(",",$barcodeNumbersArr).")";
		}
	} */

	/* $sql_roll="select c.barcode_no, c.booking_without_order from pro_roll_details c where c.entry_form=56 and c.status_active=1 $barcode_cond";
	//echo $sql_roll;die;
	$sql_roll_res=sql_select($sql_roll);
	$previous_delivery_barcode=array();
	foreach($sql_roll_res as $row)
	{
		$previous_delivery_barcode[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
	}
	unset($sql_roll_res); */


	$po_arr = array();
	if ($sales_order == 1)
	{
		if ($sales_within_group == true)
		{
			$po_info = sql_select("select b.job_no job_no_mst, b.booking_no from wo_booking_mst b where b.entry_form in (86,88,89,90,118,119) and b.status_active=1 and b.is_deleted=0 and b.booking_no in(".implode(",",$booking_no_arr).")");
			if (!empty($po_info)) {
				foreach ($po_info as $po_row) {
					$po_arr[$po_row[csf('booking_no')]] = $po_row[csf('job_no_mst')];
				}
			}
		}
	}

	$variable_settingAutoQC = return_field_value("qc_mandatory", "variable_settings_production", "company_name =$company_id and variable_list in(48) and item_category_id=13 and is_deleted=0 and status_active=1", "qc_mandatory");
	$qc_passed=array();
	if($variable_settingAutoQC == 1)
	{
		/* $roll_status = sql_select("SELECT c.barcode_no, c.roll_status from pro_qc_result_mst c  where c.is_deleted=0 and c.status_active=1 $barcode_cond ");
		foreach ($roll_status as $val)
		{
			$qc_passed[$val[csf("barcode_no")]] = $val[csf("roll_status")];
		} */
	}

	?>
	<table cellspacing="0" cellpadding="0" width="1580" border="1" rules="all" class="rpt_table">
		<thead>
			<th width="30">SL</th>
			<th width="50">System Id</th>
			<th width="60">Production Date</th>
			<th width="80">Job No</th>
			<th width="80">Buyer</th>
			<th width="110">Order/FSO No</th>
			<th width="50">file No</th>
			<th width="50">Ref. No</th>
			<th width="60">Shipment Date</th>
			<th width="65">Knitting Source</th>
			<th width="60">Knitting Company</th>
			<th width="80">Location</th>
			<th width="80">Floor</th>
			<th width="100">Prog. / Booking No</th>
			<th width="100">Fab. Booking No</th>
			<th width="40">Shift</th>
			<th width="60">Machine No</th>
			<th width="150">Barcode No</th>
			<th width="100">Body Part</th>
			<th width="50">Roll No</th>
			<th width="50">Qty. In Pcs</th>
			<th>Roll Qty.</th>
		</thead>
	</table>
	<div style="width:1600px; max-height:230px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1580" class="rpt_table" id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$within_group = $row[csf('within_group')];
				$is_sales = $row[csf('is_sales')];
				if ($sales_order == 1)
				{
					$sales_order_order = $row[csf('sales_order_no')];
					$sales_booking_no = $row[csf('sales_booking_no')];
					if ($within_group == 1)
					{
						$po_data = explode("**", $po_arr[$sales_booking_no]);
						$job_no = $po_data[0];
					//$po_shipdate_no = change_date_format($po_data[0]);
					}
					else
					{
						$job_no = '';
						$po_shipdate_no = '';
					}
				}
				else
				{
					$sales_order_order=''; $job_no =''; $sales_booking_no = ''; $po_shipdate_no =''; $file_no =''; $group_no ='';
					if($row[csf("booking_without_order")]!=1)
					{
						$sales_order_order = $pono_arr[$row[csf("po_breakdown_id")]]['po'];
						$job_no = $pono_arr[$row[csf("po_breakdown_id")]]['job'];
						$sales_booking_no = '';
						$po_shipdate_no = $pono_arr[$row[csf("po_breakdown_id")]]['ship_date'];
						$file_no = $pono_arr[$row[csf("po_breakdown_id")]]['file'];
						$group_no = $pono_arr[$row[csf("po_breakdown_id")]]['group'];
					}
				}

				if($row[csf('receive_basis')]==1){
					$booking_no = $row[csf('booking_no')];
				}elseif ($row[csf('receive_basis')]==2) {
					$booking_no = $plan_array[$row[csf('booking_no')]]['booking_no'];
				}elseif ($row[csf('receive_basis')]==4) {
					$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
				}

				$qc_failed = 0;$qc_msg="";
				//if($variable_settingAutoQC==1 && ($qc_passed[$row[csf("barcode_no")]] == "" || $qc_passed[$row[csf("barcode_no")]]==2 || $qc_passed[$row[csf("barcode_no")]]==3))
				if($variable_settingAutoQC==1 && ($row[csf("roll_status")] == "" || $row[csf("roll_status")]==2 || $row[csf("roll_status")]==3))
				{
					$bgcolor="#ffcccb";
					$qc_failed = 1;
					$qc_msg = "Barcode not QC Passed yet.";
				}else{
					$bgcolor=$bgcolor;
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>); qc_alert(<? echo $i; ?>);" title="<? echo $qc_msg;?>">
					<td width="30" align="center"><? echo $i; ?>
					<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i; ?>" value="<?php echo $row[csf('barcode_no')]; ?>"/>
					<input type="hidden" name="txt_individual_qty[]" id="txt_individual_qty<?php echo $i; ?>" value="<?php echo $row[csf('qnty')]; ?>"/>
					<input type="hidden" name="qc_failed[]" id="qc_failed<?php echo $i; ?>" value="<?php echo $qc_failed; ?>"/>
					</td>
					<td width="50" align="center"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
					<td width="60" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
					<td width="80" align="center"><? echo $job_no; ?></td>
					<td width="80" align="center"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
					<td width="110" align="center"><? echo $sales_order_order; ?></td>
					<td width="50"><? echo $file_no; ?></td>
					<td width="50"><? echo $group_no; ?></td>
					<td width="60" align="center"><? if ($row[csf('booking_without_order')] == 1) echo '&nbsp;'; else echo change_date_format($po_shipdate_no); ?></td>
					<td width="65" align="center"><? echo $knitting_source[$row[csf('knitting_source')]]; ?></td>
					<td width="60" align="center">
						<?
						if ($row[csf('knitting_source')] == 1) {
							echo $company_arr[$row[csf('knitting_company')]];
						} else {
							echo $supplier_arr[$row[csf('knitting_company')]];
						}
						?>
					</td>
					<td width="80" align="center"><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
					<td width="80" align="center"><p><? echo $floor_arr[$row[csf('floor_id')]]; ?></p></td>
					<td width="100" align="center"><p><? echo $row[csf('booking_no')]; ?></p></td>
					<td width="100" align="center"><p><? echo $booking_no; ?></p></td>
					<td width="40" align="center"><p><? echo $shift_name[$row[csf('shift_name')]]; ?></p></td>
					<td width="60" align="center"><p><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></p></td>
					<td width="150" align="center"><p><? echo $row[csf('barcode_no')]; ?>&nbsp;</p></td>
					<td width="100" align="center"><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
					<td width="50" align="center"><? echo $row[csf('roll_no')]; ?>&nbsp;</td>
					<td width="50" align="right"><? echo number_format($row[csf('qc_pass_qnty_pcs')], 2); ?></td>
					<td align="right"><? echo number_format($row[csf('qnty')], 2); ?></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<!-- <table width="1550"> -->
	<table width="1580" cellspacing="0" cellpadding="0" border="1" rules="all"  class="rpt_table" >
		<tr class="tbl_bottom">
			<td width="30"></td>
			<td width="50"></td>
			<td width="60"></td>
			<td width="80"></td>
			<td width="80"></td>
			<td width="110"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td width="60"></td>
			<td width="65"></td>
			<td width="60"></td>
			<td width="80"></td>
			<td width="80"></td>
			<td width="100"></td>
			<td width="100"></td>
			<td width="40"></td>
			<td width="60"></td>
			<td width="150"></td>
			<td width="100"></td>
			<td width="50"></td>
			<td width="50"></td>
			<td></td>
		</tr>
		<tr>
			<td align="left" colspan="2">
				<input type="checkbox" name="close" class="formbutton" onClick="check_all_data()"/> Check all
			</td>
			<td align="center" colspan="10">
				<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
			</td>
			<td align="center" colspan="3"><strong>Count of Selected Row:</strong>
				<input type="text"  style="width:50px" class="text_boxes_numeric" name="hidden_selected_row_count" id="hidden_selected_row_count" readonly value="0">
			</td>
			<td align="center" colspan="3"><strong>Selected Row Total:</strong>
				<input type="text" style="width:50px" class="text_boxes_numeric" name="hidden_selected_row_total" id="hidden_selected_row_total" readonly value="0">
			</td>
	    </tr>
		<!--<tr>
			<td align="center" height="30">
				<div style="width:50%; float:left" align="left">
					<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
				</div>
				<div style="width:50%; float:left" align="left">
					<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px"/>
				</div>
			</td>
		</tr> -->
	</table>
	<?
	exit();
}
/*
if ($action == "populate_barcode_data")
{
	$barcodeData = '';
	$po_ids_arr = array();
	$po_details_array = array();
	$barcodeDataArr = array();
	$barcodeBuyerArr = array();
	$company_name_array = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");

	$data_array = sql_select("select a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)");
	$roll_details_array = array();
	$barcode_array = array();

	foreach ($data_array as $row) {
		$booking_no_id = $row[csf('booking_no')];
		$sales_order_no = "";
		if ($row[csf("receive_basis")] == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$booking_no_id");
			if ($is_salesOrder == "" || $is_salesOrder == 0) {
				$is_salesOrder = 0;
			} else {
				$is_salesOrder = 1;

			}
		}
		if ($row[csf("receive_basis")] == 4) {
			$is_salesOrder = 1;
		}
		if ($row[csf("knitting_source")] == 1) {
			$knit_company = $company_name_array[$row[csf("knitting_company")]];
		} else if ($row[csf("knitting_source")] == 3) {
			$knit_company = $supplier_arr[$row[csf("knitting_company")]];
		}
		$prodQnty = number_format(($row[csf("qnty")] + $row[csf("reject_qnty")]), 2, '.', '');

		if ($row[csf("booking_without_order")] != 1) {
			if ($is_salesOrder == 1) {
				$data_array = sql_select("select a.mst_id,b.within_group,b.booking_no,b.po_id,c.job_no from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b,fabric_sales_order_mst c where a.id = b.dtls_id and b.booking_no=c.sales_booking_no and a.id=$booking_no_id");

				// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
				$booking_no = $data_array[0]['BOOKING_NO'];
				$po_id = $data_array[0]['PO_ID'];
				$sales_order_no = $data_array[0]['JOB_NO'];
				$within_group = $data_array[0]['WITHIN_GROUP'];
			} else {
				$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				$po_id = $row[csf("po_breakdown_id")];
			}
		} else {
			if ($is_salesOrder == 1) {
				// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
				$salesOrder_id = return_field_value("ID", "FABRIC_SALES_ORDER_MST", "JOB_NO='$booking_no_id'");
				$within_group = return_field_value("WITHIN_GROUP", "FABRIC_SALES_ORDER_MST", "JOB_NO='$booking_no_id'");
				$po_id = $salesOrder_id;
				$sales_order_no = $booking_no_id;
			} else {
				$po_id = $row[csf("po_breakdown_id")];
			}
		}
		//echo $po_id.'zzzz';

		$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("barcode_no")] . "**" . $row[csf("id")] . "**" . $row[csf("company_id")] . "**" . $row[csf("recv_number")] . "**" . $receive_basis[$row[csf("receive_basis")]] . "**" . change_date_format($row[csf("receive_date")]) . "**" . $row[csf("booking_no")] . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("location_id")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . $row[csf("roll_id")] . "**" . $row[csf("roll_no")] . "**" . $po_id . "**" . $row[csf("qnty")] . "**" . $prodQnty . "**" . $row[csf("bwo")] . "**" . $row[csf("booking_without_order")];

		$barcodeBuyerArr[$row[csf('barcode_no')]] = $row[csf("booking_without_order")] . "__" . $po_id . "__" . $row[csf("buyer_id")];
	}

	if (count($po_ids_arr) > 0) {
		$data_array = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in(" . implode(",", $po_ids_arr) . ")");
		$po_details_array = array();
		foreach ($data_array as $row) {
			$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
			$po_details_array[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_name")];
			$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));

			if ($is_salesOrder == 1) {
				$po_details_array[$row[csf("po_id")]]['po_number'] = $sales_order_no;
			} else {
				$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
			}
		}
	}

	if (count($barcodeDataArr) > 0) {
		foreach ($barcodeDataArr as $barcode_no => $value) {
			$barcodeDatas = explode("__", $barcodeBuyerArr[$barcode_no]);
			$booking_without_order = $barcodeDatas[0];
			if ($booking_without_order == 1) {
				$buyer_id = $barcodeDatas[2];
				$po_no = '';
				$job_no = '';
				$year = '';
			} else {
				$buyer_id = $po_details_array[$po_id]['buyer_name'];
				$po_no = ($within_group == 2) ? $sales_order_no : $po_details_array[$po_id]['po_number'];
				$job_no = $po_details_array[$po_id]['job_no'];
				$year = $po_details_array[$po_id]['year'];
			}

			if ($po_id == '') {
				$po_id = 0;
			}

			$barcodeData .= $value . "**" . $po_id . "**" . $buyer_id . "**" . $buyer_name_array[$buyer_id] . "**" . $po_no . "**" . $job_no . "**" . $year . "_";
		}
		echo substr($barcodeData, 0, -1);
	} else {
		echo "0";
	}

	exit();
}

if ($action == "populate_barcode_data_update")
{
	$po_ids_arr = array();
	$po_details_array = array();
	$barcodeDataArr = array();
	$sales_order_no = "";
	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	$delivery_data_arr = array();
	$barcode_nos = '';
	//echo "select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$data <br>";die;
	$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$data");
	foreach ($delivery_barcode_data as $row) {
		$delivery_data_arr[$row[csf('barcode_no')]]['dtls_id'] = $row[csf('dtls_id')];
		$delivery_data_arr[$row[csf('barcode_no')]]['qnty'] = number_format($row[csf("qnty")], 2, '.', '');

		$barcode_nos .= $row[csf('barcode_no')] . ',';
		if ($row[csf("booking_without_order")] != 1) {
			$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}
	}
	$is_salesOrder = 0;
	$barcode_nos = chop($barcode_nos, ',');
	$data_array = sql_select("select a.id, a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($barcode_nos)");
	$roll_details_array = array();
	$barcode_array = array();
	foreach ($data_array as $row) {
		$booking_no_id = $row[csf('booking_no')];
		if ($row[csf("receive_basis")] == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$booking_no_id");
			if ($is_salesOrder == "" || $is_salesOrder == 0) {
				$is_salesOrder = 0;
			} else {
				$is_salesOrder = 1;

			}
		}
		if ($row[csf("receive_basis")] == 4) {
			$is_salesOrder = 1;
		}
		$prodQnty = number_format(($row[csf("qnty")] + $row[csf("reject_qnty")]), 2, '.', '');

		if ($row[csf("booking_without_order")] != 1) {
			if ($row[csf("booking_without_order")] != 1) {
				if ($is_salesOrder == 1) {
					$data_array = sql_select("select a.mst_id,b.within_group,b.booking_no,b.po_id,c.job_no from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b,fabric_sales_order_mst c where a.id = b.dtls_id and b.booking_no=c.sales_booking_no and a.id=$booking_no_id");

					// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
					$booking_no = $data_array[0]['BOOKING_NO'];
					$po_id = $data_array[0]['PO_ID'];
					$sales_order_no = $data_array[0]['JOB_NO'];
					$within_group = $data_array[0]['WITHIN_GROUP'];
				} else {
					$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
					$po_id = $row[csf("po_breakdown_id")];
				}
			} else {
				if ($is_salesOrder == 1) {
					// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
					$salesOrder_id = return_field_value("ID", "FABRIC_SALES_ORDER_MST", "JOB_NO='$booking_no_id'");
					$within_group = return_field_value("WITHIN_GROUP", "FABRIC_SALES_ORDER_MST", "JOB_NO='$booking_no_id'");
					$po_id = $salesOrder_id;
					$sales_order_no = $booking_no_id;
				} else {
					$po_id = $row[csf("po_breakdown_id")];
				}
			}
		}

		$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("id")] . "**" . $row[csf("recv_number")] . "**" . $receive_basis[$row[csf("receive_basis")]] . "**" . change_date_format($row[csf("receive_date")]) . "**" . $row[csf("booking_no")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . $row[csf("roll_id")] . "**" . $row[csf("roll_no")] . "**" . $po_id . "**" . $prodQnty . "**" . $row[csf("bwo")] . "**" . $row[csf("booking_without_order")] . "**" . $row[csf("buyer_id")] . "**" . $is_salesOrder;
	}

	$receive_barcode_array = array();
	$receive_barcode_data = sql_select("select barcode_no from pro_roll_details where entry_form=58 and status_active=1 and is_deleted=0 and barcode_no in($barcode_nos)");
	foreach ($receive_barcode_data as $row) {
		$receive_barcode_array[] = $row[csf('barcode_no')];
	}

	if (count($po_ids_arr) > 0) {
		$data_array = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, b.id as po_id FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in(" . implode(",", $po_ids_arr) . ")");
		$po_details_array = array();
		foreach ($data_array as $row) {
			if ($is_salesOrder == 1) {
				$po_details_array[$row[csf("po_id")]]['job_no'] = "";
				$po_details_array[$row[csf("po_id")]]['buyer_name'] = "";
				$po_details_array[$row[csf("po_id")]]['year'] = "";
				$po_details_array[$row[csf("po_id")]]['po_number'] = $sales_order_no;
			} else {
				$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
				$po_details_array[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_name")];
				$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
				$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
			}
		}
	}

	$i = count($delivery_barcode_data);
	foreach ($barcodeDataArr as $barcode_no => $value) {
		$barcodeDatas = explode("**", $value);
		$id = $barcodeDatas[0];
		$recv_number = $barcodeDatas[1];
		$receive_basis = $barcodeDatas[2];
		$receive_date = $barcodeDatas[3];
		$booking_no = $barcodeDatas[4];
		$knitting_source = $barcodeDatas[5];
		$dtls_id = $barcodeDatas[6];
		$prod_id = $barcodeDatas[7];
		$febric_description_id = $barcodeDatas[8];
		$gsm = $barcodeDatas[9];
		$width = $barcodeDatas[10];
		$roll_id = $barcodeDatas[11];
		$roll_no = $barcodeDatas[12];
		$po_id = $barcodeDatas[13];
		$prodQnty = $barcodeDatas[14];
		$bwo = $barcodeDatas[15];
		$booking_without_order = $barcodeDatas[16];
		$buyer_id = $barcodeDatas[17];

		$cons = $constructtion_arr[$febric_description_id];
		$comp = $composition_arr[$febric_description_id];
		$dtls_roll_id = $delivery_data_arr[$barcode_no]['dtls_id'];
		$qnty = $delivery_data_arr[$barcode_no]['qnty'];

		if ($booking_without_order == 1) {
			$buyer_id = $without_order_buyer[$barcode_no];
			$buyer_name = $buyer_name_array[$buyer_id];
			$po_no = $sales_order_no;
			$job_no = '';
			$year = '';
		} else {
			$buyer_id = $po_details_array[$po_id]['buyer_name'];
			$buyer_name = $buyer_name_array[$po_details_array[$po_id]['buyer_name']];
			if ($is_salesOrder == 1) {
				$po_no = $sales_order_no;
			} else {
				$po_no = $po_details_array[$po_id]['po_number'];
			}
			$job_no = $po_details_array[$po_id]['job_no'];
			$year = $po_details_array[$po_id]['year'];
		}

		$disable = '';
		if (in_array($barcode_no, $receive_barcode_array)) {
			$disable = 'disabled="disabled"';
		}

		?>
		<tr id="tr_<? echo $i; ?>" align="center" valign="middle">
			<td width="30" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
			<td width="80" id="barcode_<? echo $i; ?>"><? echo $barcode_no; ?></td>
			<td width="100" id="systemId_<? echo $i; ?>"><? echo $recv_number; ?></td>
			<td width="85" id="progBookId_<? echo $i; ?>"><? echo $booking_no; ?></td>
			<td width="75" id="basis_<? echo $i; ?>"><? echo $receive_basis; ?></td>
			<td width="75" id="knitSource_<? echo $i; ?>"><? echo $knitting_source; ?></td>
			<td width="70" id="prodDate_<? echo $i; ?>"><? echo $receive_date; ?></td>
			<td width="50" id="prodId_<? echo $i; ?>"><? echo $prod_id; ?></td>
			<td width="40" id="year_<? echo $i; ?>" align="center"><? echo $year; ?></td>
			<td width="50" id="job_<? echo $i; ?>"><? echo $job_no; ?></td>
			<td width="55" id="buyer_<? echo $i; ?>"><? echo $buyer_name; ?></td>
			<td width="80" id="order_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $po_no; ?></td>
			<td width="80" id="cons_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $cons; ?></td>
			<td width="100" id="comps_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $comp; ?></td>
			<td width="40" id="gsm_<? echo $i; ?>"><? echo $gsm; ?></td>
			<td width="40" id="dia_<? echo $i; ?>"><? echo $width; ?></td>
			<td width="40" id="roll_<? echo $i; ?>"><? echo $roll_no; ?></td>
			<td width="70" id="prodQty_<? echo $i; ?>" align="right"><? echo $prodQnty; ?></td>
			<td id="delevQt_<? echo $i; ?>" width="80" align="center"><input type="text" name="currentDelivery[]"
																			 id="currentDelivery_<? echo $i; ?>"
																			 style="width:65px"
																			 class="text_boxes_numeric"
																			 onKeyUp="check_qty(<? echo $i; ?>)"
																			 value="<? echo $qnty; ?>" disabled
																			 readonly/></td>
			<td id="button_<? echo $i; ?>" align="center">
				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px"
					   class="formbuttonplasminus" value="-"
					   onClick="fn_deleteRow(<? echo $i; ?>);" <? echo $disable; ?> />
				<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $barcode_no; ?>"/>
				<input type="hidden" name="productionId[]" id="productionId_<? echo $i; ?>" value="<? echo $id; ?>"/>
				<input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $i; ?>"
					   value="<? echo $dtls_id; ?>"/>
				<input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>"
					   value="<? echo $febric_description_id; ?>"/>
				<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $prod_id; ?>"/>
				<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $po_id; ?>"/>
				<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $roll_id; ?>"/>
				<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_roll_id; ?>"/>
				<input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $i; ?>"
					   value="<? echo $booking_without_order; ?>"/>
			</td>
		</tr>
		<?
		$i--;
	}
	exit();
}*/

if ($action == "populate_barcode_data")
{
	$barcodeData = '';
	$po_ids_arr = array();
	$po_details_array = array();
	$barcodeDataArr = array();
	$barcodeBuyerArr = array();

	//for barcode type
	$exp_data=explode('_', $data);
	$data=$exp_data[0];
	$barcode_type=$exp_data[1];

	$barcodeTypeCon='';
	if($barcode_type == 1)
	{
		$barcodeTypeCon=" and c.booking_without_order != 1";
	}
	else
	{
		$barcodeTypeCon=" and c.booking_without_order = 1";
	}

	$company_name_array = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	//$grey_system_challan = return_field_value("a.sys_number as sys_number", "pro_grey_prod_delivery_mst a,pro_grey_prod_delivery_dtls b", " a.id=b.mst_id and b.barcode_num in($data) and a.entry_form=56 and b.status_active=1", "sys_number");

	$grey_system_challan_arr = return_library_array("select b.barcode_num as barcode_num, a.sys_number as sys_number from pro_grey_prod_delivery_mst a, pro_grey_prod_delivery_dtls b where a.id=b.mst_id and b.barcode_num in($data) and a.entry_form=56 and b.status_active=1", "barcode_num", "sys_number");

	$data_array = sql_select("SELECT a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.knitting_location_id location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order, c.is_sales,b.color_id, c.qc_pass_qnty_pcs, b.body_part_id,c.coller_cuff_size
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data) $barcodeTypeCon order by a.company_id, a.knitting_source, a.knitting_location_id, a.knitting_company");

	$plan_arr=array();
	foreach ($data_array as $row)
	{
		if($row[csf('receive_basis')] == 2){
			$plan_arr[] = $row[csf('booking_no')];
		}
		$plan_id=implode(',',$plan_arr);
		if($row[csf("is_sales")] == 1){
			$sales_ids[] = $row[csf('po_breakdown_id')];
		}else{
			$po_ids[] = $row[csf('po_breakdown_id')];
		}

		if($row[csf("booking_without_order")]==1)
		{
			$non_ord_samp_booking_id .= $row[csf("po_breakdown_id")].",";
		}

		$company_id = $row[csf('company_id')];
	}

	$variable_settingAutoQC = return_field_value("qc_mandatory", "variable_settings_production", "company_name =$company_id and variable_list in(48) and item_category_id=13 and is_deleted=0 and status_active=1", "qc_mandatory");
	if($variable_settingAutoQC == 1)
	{
		$roll_status_sql = sql_select("SELECT roll_status, barcode_no from pro_qc_result_mst  where barcode_no in ($data) and is_deleted=0 and status_active=1");
		if(!empty($roll_status_sql))
		{
			foreach ($roll_status_sql as $key => $row)
			{
				if($row[csf("roll_status")] =="" || $row[csf("roll_status")] ==2 || $row[csf("roll_status")] ==3)
				{
					$settingAutoQCArray[$row[csf("barcode_no")]] = 1;
				}
				else{
					$settingAutoQCArray[$row[csf("barcode_no")]] = 0;
				}

			}
		}
		else{
			$settingAutoQC_barcode =1;
		}
	}
	else
	{
		$settingAutoQC_barcode =0;
	}






    $non_ord_samp_booking_id = implode(",",array_unique(explode(",",chop($non_ord_samp_booking_id,","))));
	if($non_ord_samp_booking_id!="")
	{
		$sqlsamplnoOrder = sql_select("select id,insert_date,buyer_id from wo_non_ord_samp_booking_mst where id in($non_ord_samp_booking_id) and status_active=1 and is_deleted=0");

		foreach ($sqlsamplnoOrder as $row) {
			$no_order_details_array[$row[csf("id")]]['buyer_id'] = $row[csf("buyer_id")];
			$no_order_details_array[$row[csf("id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		}
	}

	if(!empty($sales_ids))
	{
		$sales_order_result = sql_select("select within_group,sales_booking_no,id po_id,job_no from fabric_sales_order_mst where id in(".implode(",",$sales_ids).") and status_active=1 and is_deleted=0");
		$sales_arr = array();
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("po_id")]]["job_no"] 			= $sales_row[csf("job_no")];
			$sales_arr[$sales_row[csf("po_id")]]["sales_booking_no"] 	= $sales_row[csf("sales_booking_no")];
			$sales_arr[$sales_row[csf("po_id")]]["within_group"] 		= $sales_row[csf("within_group")];
			$booking_nos[] = "'".$sales_row[csf("sales_booking_no")]."'";
		}
	}

	$po_cond = (!empty($po_ids))?"and c.id in(".implode(",",$po_ids).")":" and a.booking_no in(".implode(",",$booking_nos).")";
	$po_array = sql_select("select b.job_no,c.po_number,a.buyer_id,c.id po_id,b.booking_no,b.insert_date,c.grouping from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.booking_type in(1,4) $po_cond");
	$po_details_array = array();
	foreach ($po_array as $row)
	{
		$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no")];
		$po_details_array[$row[csf("po_id")]]['grouping'] = $row[csf("grouping")];
		$po_details_array[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_id")];
		$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];

		$po_details_array[$row[csf("booking_no")]]['job_no'] 		= $row[csf("job_no")];
		$po_details_array[$row[csf("booking_no")]]['buyer_name'] 	= $row[csf("buyer_id")];
		$po_details_array[$row[csf("booking_no")]]['po_id'] 		= $row[csf("po_id")];
		$po_details_array[$row[csf("booking_no")]]['po_number'] 	= $row[csf("po_number")];
		$po_details_array[$row[csf("booking_no")]]['grouping'] 	= $row[csf("grouping")];
		$po_details_array[$row[csf("booking_no")]]['year'] 			= date("Y", strtotime($row[csf("insert_date")]));
	}

	$plan_cond = ($plan_id != "")?" and a.id in ($plan_id)":"";
	$ppl_data = sql_select("select a.id,b.booking_no from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where b.id=a.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $plan_cond");
	$plan_array=array();
	foreach ($ppl_data as $row)
	{
		$plan_array[$row[csf('id')]]['booking_no'] = $row[csf('booking_no')];
	}

	$roll_details_array = array();
	$barcode_array = array();
	foreach ($data_array as $row)
	{
		$booking_no_id = $row[csf('booking_no')];
		$sales_order_no = "";
		$is_salesOrder = $row[csf("is_sales")];

		if ($row[csf("knitting_source")] == 1) {
			$knit_company = $company_name_array[$row[csf("knitting_company")]];
		} else if ($row[csf("knitting_source")] == 3) {
			$knit_company = $supplier_arr[$row[csf("knitting_company")]];
		}
		$prodQnty = number_format(($row[csf("qnty")] + $row[csf("reject_qnty")]), 2, '.', '');

		if ($row[csf("booking_without_order")] != 1) {
			if ($is_salesOrder == 1) {
				// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
				$sales_id = $row[csf("po_breakdown_id")];
				$within_group = $sales_arr[$sales_id]["within_group"];
				if($within_group == 1){
					$booking_no = $sales_arr[$sales_id]["sales_booking_no"];

					$sales_order_no = $sales_arr[$sales_id]["job_no"];
					$job_no = $po_details_array[$booking_no]['job_no'];
					$internal_ref_no = $po_details_array[$booking_no]['grouping'];
					$po_id = $row[csf("po_breakdown_id")];
					$po_no = $sales_arr[$sales_id]["job_no"];
					$buyer_id = $po_details_array[$booking_no]['buyer_name'];
					$year = $po_details_array[$booking_no]['year'];
				}else{
					$sales_order_no = $sales_arr[$sales_id]["job_no"];
					$job_no = "";
					$internal_ref_no = "";
					$po_id = $sales_id;
					$po_no = $sales_order_no;
					$buyer_id = "";
					$year = "";
				}
			} else {
				$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				$po_id = $row[csf("po_breakdown_id")];
				$year = $po_details_array[$po_id]['year'];
				$job_no = $po_details_array[$po_id]['job_no'];
				$internal_ref_no = $po_details_array[$po_id]['grouping'];
				$buyer_id = $po_details_array[$po_id]['buyer_name'];
				$po_no=$po_details_array[$po_id]['po_number'];
			}
		} else {
			// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
			if ($is_salesOrder == 1) {
				$sales_id = $row[csf("po_breakdown_id")];
				$sales_order_no = $sales_arr[$sales_id]["job_no"];
				$within_group = $sales_arr[$sales_id]["within_group"];
				$po_id = $sales_id;
			} else {
				$po_id = $row[csf("po_breakdown_id")];
			}

			$year = $no_order_details_array[$row[csf("po_breakdown_id")]]['year'];
			$buyer_id =  $no_order_details_array[$row[csf("po_breakdown_id")]]['buyer_id'];


		}

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		if($row[csf('receive_basis')]==1){
			$booking_no = $row[csf('booking_no')];
		}elseif ($row[csf('receive_basis')]==2) {
			$booking_no = $plan_array[$row[csf('booking_no')]]['booking_no']." / ".$row[csf('booking_no')];
		}elseif ($row[csf('receive_basis')]==4) {
			$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]["sales_booking_no"];
		}


		if($variable_settingAutoQC == 1 && !empty($settingAutoQCArray))
		{
			$settingAutoQC_barcode = $settingAutoQCArray[$row[csf("barcode_no")]];
		}

		$barcodeData .= $row[csf("barcode_no")] . "**" . $row[csf("id")] . "**" . $row[csf("company_id")] . "**" . $row[csf("recv_number")] . "**" . $receive_basis[$row[csf("receive_basis")]] . "**" . change_date_format($row[csf("receive_date")]) . "**" . $booking_no . "**" . $row[csf("knitting_source")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("knitting_company")] . "**" . $knit_company . "**" . $row[csf("location_id")] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . $row[csf("roll_id")] . "**" . $row[csf("roll_no")] . "**" . $po_id . "**" . $row[csf("qnty")] . "**" . $prodQnty . "**" . $row[csf("bwo")] . "**" . $row[csf("booking_without_order")]. "**" . $row[csf("challan_no")] . "**" . $row[csf("service_booking_no")] . "**" . $po_id . "**" . $buyer_id . "**" . $buyer_name_array[$buyer_id] . "**" . $po_no . "**" . $job_no . "**" . $year . "**" . $grey_system_challan_arr[$row[csf("barcode_no")]] . "**" . $is_salesOrder. "**" . $color . "**" . $body_part[$row[csf("body_part_id")]]. "**" . $row[csf("qc_pass_qnty_pcs")] . "**" . $row[csf("reject_qnty")]  . "**" . $internal_ref_no . "**" . $row[csf("coller_cuff_size")]. "**" . $settingAutoQC_barcode  . "___";
	}
	echo $barcodeData=chop($barcodeData,"___");
	//echo substr($barcodeData, 0, -1);
	exit();
}

if ($action == "populate_barcode_data_update")
{
	$po_ids_arr = array();
	$po_details_array = array();
	$barcodeDataArr = array();
	$sales_order_no = "";
	$buyer_name_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$composition_arr = array();
	$constructtion_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active =1";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
		$composition_arr[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}

	$delivery_data_arr = array();
	$barcode_nos = '';
	$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$data");
	foreach ($delivery_barcode_data as $row) {
		$delivery_data_arr[$row[csf('barcode_no')]]['dtls_id'] = $row[csf('dtls_id')];
		$delivery_data_arr[$row[csf('barcode_no')]]['qnty'] = number_format($row[csf("qnty")], 2, '.', '');

		$barcode_nos .= "'".$row[csf('barcode_no')] . "',";
		if ($row[csf("booking_without_order")] != 1) {
			$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}
	}
	$is_salesOrder = 0;
	$barcode_nos = chop($barcode_nos, ',');
	$all_barcode_no_arr = array_filter(explode(",",$barcode_nos));
    $all_barcode_no_cond="";$barCond="";
    if($db_type==2 && count($all_barcode_no_arr)>999)
    {
    	$all_barcode_nos_chunk=array_chunk($all_barcode_no_arr,999) ;
    	foreach($all_barcode_nos_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$barCond.="  c.barcode_no in($chunk_arr_value) or ";
    	}

    	$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
    }
    else
    {
    	$all_barcode_no_cond=" and c.barcode_no in($barcode_nos)";
    }
	$data_array = sql_select("SELECT a.id,a.challan_no,a.service_booking_no , a.company_id, a.recv_number, a.receive_basis, a.receive_date, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.knitting_location_id location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id,b.body_part_id, c.qc_pass_qnty_pcs,c.coller_cuff_size from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $all_barcode_no_cond ");
	//and c.barcode_no in($barcode_nos)
	$roll_details_array = array();
	$barcode_array = array();
	foreach ($data_array as $row) {
		$booking_no_id = $row[csf('booking_no')];
		$is_salesOrder = $row[csf('is_sales')];
		/*if ($row[csf("receive_basis")] == 2) {
			$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$booking_no_id");
			if ($is_salesOrder == "" || $is_salesOrder == 0) {
				$is_salesOrder = 0;
			} else {
				$is_salesOrder = 1;

			}
		}
		if ($row[csf("receive_basis")] == 4) {
			$is_salesOrder = 1;
		}*/
		$prodQnty = number_format(($row[csf("qnty")] + $row[csf("reject_qnty")]), 2, '.', '');

		if ($row[csf("booking_without_order")] != 1) {
			if ($is_salesOrder == 1) {
				$data_array = sql_select("select a.mst_id,b.within_group,b.booking_no,b.po_id,c.job_no, a.id as program from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b,fabric_sales_order_mst c where a.id = b.dtls_id and b.booking_no=c.sales_booking_no and a.id=$booking_no_id");

				// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
				$booking_no = $data_array[0]['BOOKING_NO'];
				$po_id = $data_array[0]['PO_ID'];
				$sales_order_no = $data_array[0]['JOB_NO'];
				$within_group = $data_array[0]['WITHIN_GROUP'];
				$po_ids_arr[$row[csf("po_breakdown_id")]] = $data_array[0]['PO_ID'];
				$prog_booking_no = $data_array[0]['PROGRAM'] =  $booking_no;
				
			} else {
				$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				$po_id = $row[csf("po_breakdown_id")];
			}
		} else {
			if ($is_salesOrder == 1) {
				// IF "SALES ORDER" THEN SALES ORDER ID WILL BE USED INSTEAD ORDER NO
				$salesOrder_id = return_field_value("ID", "FABRIC_SALES_ORDER_MST", "JOB_NO='$booking_no_id'");
				$within_group = return_field_value("WITHIN_GROUP", "FABRIC_SALES_ORDER_MST", "JOB_NO='$booking_no_id'");
				$po_id = $salesOrder_id;
				$sales_order_no = $booking_no_id;
			} else {
				$po_id = $row[csf("po_breakdown_id")];

			}

			$non_ord_samp_booking_id .= $row[csf("po_breakdown_id")] . ",";
		}


		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$barcodeDataArr[$row[csf('barcode_no')]] = $row[csf("id")] . "**" . $row[csf("recv_number")] . "**" . $receive_basis[$row[csf("receive_basis")]] . "**" . change_date_format($row[csf("receive_date")]) . "**" . $row[csf("booking_no")] . "**" . $knitting_source[$row[csf("knitting_source")]] . "**" . $row[csf("dtls_id")] . "**" . $row[csf("prod_id")] . "**" . $row[csf("febric_description_id")] . "**" . $row[csf("gsm")] . "**" . $row[csf("width")] . "**" . $row[csf("roll_id")] . "**" . $row[csf("roll_no")] . "**" . $po_id . "**" . $prodQnty . "**" . $row[csf("bwo")] . "**" . $row[csf("booking_without_order")] . "**" . $row[csf("buyer_id")] . "**" . $is_salesOrder. "**" . $row[csf("challan_no")] . "**" . $row[csf("service_booking_no")]."**".$is_salesOrder."**".$color."**".$body_part[$row[csf("body_part_id")]]."**".$row[csf("qc_pass_qnty_pcs")]."**".$row[csf("reject_qnty")]."**".$row[csf("coller_cuff_size")]."**".$row[csf("receive_basis")];
	}

	$receive_barcode_array = array();
	if($barcode_nos!="")
	{
		$receive_barcode_data = sql_select("select barcode_no from pro_roll_details where entry_form=58 and status_active=1 and is_deleted=0 and barcode_no in($barcode_nos)");
		foreach ($receive_barcode_data as $row) {
			$receive_barcode_array[] = $row[csf('barcode_no')];
		}
	}


	$non_ord_samp_booking_id = implode(",",array_unique(explode(",",chop($non_ord_samp_booking_id,","))));
	if($non_ord_samp_booking_id!="")
	{
		$sqlsamplnoOrder = sql_select("select id,insert_date,buyer_id from wo_non_ord_samp_booking_mst where id in($non_ord_samp_booking_id) and status_active=1 and is_deleted=0");

		foreach ($sqlsamplnoOrder as $row) {
			$no_order_details_array[$row[csf("id")]]['buyer_id'] = $row[csf("buyer_id")];
			$no_order_details_array[$row[csf("id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
		}
	}

	if (count($po_ids_arr) > 0) {
		$data_array = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date, b.po_number, b.id as po_id,b.grouping FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no=b.job_no_mst and b.id in('" . implode("','", $po_ids_arr) . "')");

		$po_details_array = array();
		foreach ($data_array as $row) {
			if ($is_salesOrder == 1) {
				$po_details_array[$row[csf("po_id")]]['po_number'] = $sales_order_no;
			} else {
				$po_details_array[$row[csf("po_id")]]['job_no'] = $row[csf("job_no_prefix_num")];
				$po_details_array[$row[csf("po_id")]]['grouping'] = $row[csf("grouping")];
				$po_details_array[$row[csf("po_id")]]['buyer_name'] = $row[csf("buyer_name")];
				$po_details_array[$row[csf("po_id")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
				$po_details_array[$row[csf("po_id")]]['po_number'] = $row[csf("po_number")];
			}
		}


		$sales_order_result = sql_select("select id,job_no,within_group,sales_booking_no,buyer_id from fabric_sales_order_mst where status_active=1 and is_deleted=0 and id in('" . implode("','", $po_ids_arr) . "')");
		$sales_arr = array();$sales_booking_nos="";
		foreach ($sales_order_result as $sales_row) {
			$sales_arr[$sales_row[csf("id")]]["po_number"] 			= $sales_row[csf("job_no")];
			$sales_arr[$sales_row[csf("id")]]["sales_booking_no"] 	= $sales_row[csf("sales_booking_no")];
			$sales_arr[$sales_row[csf("id")]]["within_group"] 		= $sales_row[csf("within_group")];
			$sales_arr[$sales_row[csf("id")]]["buyer_id"] 			= $sales_row[csf("buyer_id")];
			$sales_booking_arr[] = "'".$sales_row[csf("sales_booking_no")]."'";
			$sales_booking_nos.= "'".$sales_row[csf("sales_booking_no")]."',";

		}
		$sales_booking_nos=chop($sales_booking_nos,",");

		if(!empty($sales_booking_arr)){
			/*$data_array = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date,b.id as po_id,c.booking_no,b.grouping FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no in(" . implode("','", $sales_booking_arr) . ") ");*/

			$data_array = sql_select("SELECT a.job_no_prefix_num, a.buyer_name, a.insert_date,b.id as po_id,c.booking_no,b.grouping as grouping FROM wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c WHERE a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no in($sales_booking_nos)
				 union all
				 SELECT 0 as job_no_prefix_num, a.buyer_id, a.insert_date,0 as po_id,c.booking_no,a.grouping as grouping
				 FROM wo_non_ord_samp_booking_mst a,  wo_non_ord_samp_booking_dtls c
				 WHERE a.booking_no=c.booking_no and c.booking_no in($sales_booking_nos) ");
			$po_job_array = array();
			foreach ($data_array as $row) {
				if($row[csf("job_no_prefix_num")]!=0)
				{
					$po_job_array[$row[csf("booking_no")]]['job_no'] 	= $row[csf("job_no_prefix_num")];
				}
				$po_job_array[$row[csf("booking_no")]]['grouping'] 		= $row[csf("grouping")];
				$po_job_array[$row[csf("booking_no")]]['buyer_name'] 	= $row[csf("buyer_name")];
				$po_job_array[$row[csf("booking_no")]]['year'] = date("Y", strtotime($row[csf("insert_date")]));
			}
		}
	}
	$i = 1;
	foreach ($barcodeDataArr as $barcode_no => $value) {
		$barcodeDatas = explode("**", $value);
		$id = $barcodeDatas[0];
		$recv_number = $barcodeDatas[1];
		$receive_basis = $barcodeDatas[2];
		$receive_date = $barcodeDatas[3];
		$booking_no = $barcodeDatas[4];
		$knitting_source = $barcodeDatas[5];
		$dtls_id = $barcodeDatas[6];
		$prod_id = $barcodeDatas[7];
		$febric_description_id = $barcodeDatas[8];
		$gsm = $barcodeDatas[9];
		$width = $barcodeDatas[10];
		$roll_id = $barcodeDatas[11];
		$roll_no = $barcodeDatas[12];
		$po_id = $barcodeDatas[13];
		$prodQnty = $barcodeDatas[14];
		$bwo = $barcodeDatas[15];
		$booking_without_order = $barcodeDatas[16];
		$buyer_id = $barcodeDatas[17];
		$rcvChallanNo = $barcodeDatas[19];
		$serviceBookingNo = $barcodeDatas[20];
		$is_salesOrder = $barcodeDatas[21];
		$color_id = $barcodeDatas[22];
		$body_part = $barcodeDatas[23];
		$qnty_in_pcs = $barcodeDatas[24];
		$reject_qnty = $barcodeDatas[25];
		$coller_cuff_size = $barcodeDatas[26];
		$receive_basis_id = $barcodeDatas[27];

		$cons = $constructtion_arr[$febric_description_id];
		$comp = $composition_arr[$febric_description_id];
		$dtls_roll_id = $delivery_data_arr[$barcode_no]['dtls_id'];
		$qnty = $delivery_data_arr[$barcode_no]['qnty'];
		/*echo "<pre>";
		print_r($composition_arr);die;*/
		if ($booking_without_order == 1) {
			$buyer_id = $without_order_buyer[$barcode_no];
			$po_no = $sales_order_no;
			$job_no = '';
			$internal_ref_no = '';
			$buyer_name = $buyer_name_array[$no_order_details_array[$po_id]['buyer_id']];
			$year = $no_order_details_array[$po_id]['year'];

		} else {
			$within_group = $sales_arr[$po_id]["within_group"];
			$sales_booking = $sales_arr[$po_id]["sales_booking_no"];
			if ($is_salesOrder == 1) {
				if($within_group == 1){
					$po_no = $sales_arr[$po_id]["po_number"];
					$job_no = $po_job_array[$sales_booking]['job_no'];
					$internal_ref_no = $po_job_array[$sales_booking]['grouping'];
					$year = $po_job_array[$sales_booking]['year'];
					$buyer_name = $buyer_name_array[$po_job_array[$sales_booking]['buyer_name']];
				}else{
					$po_no = $sales_arr[$po_id]["po_number"];
					$job_no = "";
					$internal_ref_no = "";
					$year = $po_job_array[$sales_booking]['year'];
					$buyer_name = $buyer_name_array[$sales_arr[$po_id]["buyer_id"]];
				}

			} else {
				$po_no = $po_details_array[$po_id]['po_number'];
				$buyer_id = $po_details_array[$po_id]['buyer_name'];
				$buyer_name = $buyer_name_array[$po_details_array[$po_id]['buyer_name']];
				$job_no = $po_details_array[$po_id]['job_no'];
				$internal_ref_no = $po_details_array[$po_id]['grouping'];
				$year = $po_details_array[$po_id]['year'];
			}
		}
		
		if($receive_basis_id==2){
			echo $booking_no = $prog_booking_no."/ ".$booking_no;
		}else{
			$booking_no=$booking_no;
		}

		$disable = '';
		if (in_array($barcode_no, $receive_barcode_array)) {
			$disable = 'disabled="disabled"';
		}

		?>
		<tr id="tr_<? echo $i; ?>" align="center" valign="middle">
			<td width="30" id="sl_<? echo $i; ?>"><? echo $i; ?></td>
			<td width="80" id="barcode_<? echo $i; ?>"><? echo $barcode_no; ?></td>
			<td width="100" id="systemId_<? echo $i; ?>"><? echo $recv_number; ?></td>
			<td width="85" id="progBookId_<? echo $i; ?>"><? echo $booking_no ; ?></td>
			<td width="75" id="basis_<? echo $i; ?>"><? echo $receive_basis; ?></td>
			<td width="75" id="knitSource_<? echo $i; ?>"><? echo $knitting_source; ?></td>
			<td width="100" id="prodDate_<? echo $i; ?>"><? echo $receive_date; ?></td>
			<td width="80" id="rcvChallanNo_<? echo $i; ?>"><? echo $rcvChallanNo; ?></td>
			<td width="120" id="serviceBookingNo_<? echo $i; ?>"><? echo $serviceBookingNo; ?></td>
			<td width="50" id="prodId_<? echo $i; ?>"><? echo $prod_id; ?></td>
			<td width="40" id="year_<? echo $i; ?>" align="center"><? echo $year; ?></td>
			<td width="110" id="job_<? echo $i; ?>"><? echo $job_no; ?></td>
			<td width="100" id="internalRefNo_<? echo $i; ?>"><? echo $internal_ref_no; ?></td>
			<td width="55" id="buyer_<? echo $i; ?>"><? echo $buyer_name; ?></td>
			<td width="80" id="order_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $po_no; ?></td>
			<td width="100" id="bodyPart_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $body_part; ?></td>
			<td width="80" id="color_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $color_id; ?></td>
			<td width="80" id="cons_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $cons; ?></td>
			<td width="100" id="comps_<? echo $i; ?>" style="word-break:break-all;" align="left"><? echo $comp; ?></td>
			<td width="40" id="gsm_<? echo $i; ?>"><? echo $gsm; ?></td>
			<td width="40" id="dia_<? echo $i; ?>"><? echo $width; ?></td>
			<td width="40" id="roll_<? echo $i; ?>"><? echo $roll_no; ?></td>
			<td width="70" id="prodQty_<? echo $i; ?>" align="right"><? echo $prodQnty; ?></td>
			<td width="50" id="rejectQty_<? echo $i; ?>" align="right"><? echo $reject_qnty; ?></td>
			<td width="50" id="qntyInPcs_<? echo $i; ?>" align="right"><? echo $qnty_in_pcs; ?></td>
			<td width="80" id="delevQt_<? echo $i; ?>" align="center">
				<input type="text" name="currentDelivery[]" id="currentDelivery_<? echo $i; ?>"
				style="width:65px" class="text_boxes_numeric" onKeyUp="check_qty(<? echo $i; ?>)" value="<? echo $qnty; ?>" disabled readonly/>
			</td>
			<td width="50" id="size_<? echo $i; ?>" align="center"><? echo $coller_cuff_size; ?></td>
			<td width="30" id="button_<? echo $i; ?>" align="center">
				<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" <? echo $disable; ?> />
				<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $i; ?>" value="<? echo $barcode_no; ?>"/>
				<input type="hidden" name="productionId[]" id="productionId_<? echo $i; ?>" value="<? echo $id; ?>"/>
				<input type="hidden" name="productionDtlsId[]" id="productionDtlsId_<? echo $i; ?>" value="<? echo $dtls_id; ?>"/>
				<input type="hidden" name="deterId[]" id="deterId_<? echo $i; ?>" value="<? echo $febric_description_id; ?>"/>
				<input type="hidden" name="productId[]" id="productId_<? echo $i; ?>" value="<? echo $prod_id; ?>"/>
				<input type="hidden" name="orderId[]" id="orderId_<? echo $i; ?>" value="<? echo $po_id; ?>"/>
				<input type="hidden" name="rollId[]" id="rollId_<? echo $i; ?>" value="<? echo $roll_id; ?>"/>
				<input type="hidden" name="rollNo[]" id="rollNo_<? echo $i; ?>" value="<? echo $roll_no; ?>"/>
				<input type="hidden" name="dtlsId[]" id="dtlsId_<? echo $i; ?>" value="<? echo $dtls_roll_id; ?>"/>
				<input type="hidden" name="isSales[]" id="isSales_<? echo $i; ?>" value="<? echo $is_salesOrder; ?>"/>
				<input type="hidden" name="bookingWithoutOrder[]" id="bookingWithoutOrder_<? echo $i; ?>" value="<? echo $booking_without_order; ?>"/>
				<input type="hidden" name="jobNo[]" id="jobNo_<? echo $i; ?>" value="<? echo $job_no; ?>"/>
			</td>
		</tr>
		<?
		$i++;
	}
	exit();
}

if ($action == "load_scanned_barcode_nos")
{
	$scanned_arr = array();
	$dataArr = sql_select("select barcode_no as BARCODE_NO from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0");
	foreach ($dataArr as $row) {
		$scanned_arr[] = $row['BARCODE_NO'];
	}
	$jsbarcode_array = json_encode($scanned_arr);
	echo $jsbarcode_array;
	exit();
}

if ($action == "grey_delivery_print__old_bk")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$is_salesOrder = 0;
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$location_data = return_library_array("select id, location_name from lib_location", "id", "location_name");

	//$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");

	//$mstData=sql_select("select company_id,location_id, delevery_date, knitting_source, knitting_company, remarks from pro_grey_prod_delivery_mst where id=$update_id");

	$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis,c.booking_without_order from pro_grey_prod_delivery_mst a
		inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
		inner join inv_receive_master c on b.grey_sys_id = c.id
		where a.id=$update_id group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");


	$search_param = $mstData[0][csf('booking_no')];
	$booking_without_order = $mstData[0][csf('booking_without_order')];

	if ($mstData[0][csf('receive_basis')] == 2) {
		$booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
			inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
			inner join wo_booking_mst c on b.booking_no = c.booking_no
			where a.id = $search_param");

		$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
		if ($is_salesOrder == "" || $is_salesOrder == 0) {
			$is_salesOrder = 0;
		} else {
			$is_salesOrder = 1;
		}
	} else if ($mstData[0][csf('receive_basis')] == 4) {
		$is_salesOrder = 1;
		$booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
			inner join wo_booking_mst b on a.booking_id = b.id
			where a.job_no = '$search_param'");
	} else {
		$booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
	}
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	?>
	<div style="width:1700px;">
		<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td rowspan="3" colspan="2">
					<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px;"><? echo $company_array[$company]['name']; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px;"><u>Delivery Challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="60">Location</td>
				<td width="170"><? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
				<td width="810" id="barcode_img_id" align="right"></td>

			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:14px; font-weight:bold;">Remarks</td>
				<td colspan="4" style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
			</tr>

			<tr>
				<td style="font-size:14px; font-weight:bold;">Floor No</td>
				<td>:&nbsp;<? echo $floor_name ?></td>
				<?php if ($is_po && ($is_salesOrder == 1)) { ?>
					<td style="font-size:14px; font-weight:bold;">PO Company</td>
					<td>:&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
				<?php } ?>
			</tr>


		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1850" class="rpt_table"
		style="font-family: tahoma; font-size: 12px;">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="70">Production Date</th><!--new-->
				<th width="90">Order/FSO No</th>
				<th width="100">Style No</th>
				<th width="100"><?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?>Buyer
					/<br> <?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?> Job
				</th>
				<th width="60">File No <br> Ref No</th>
				<th width="50">System ID</th>
				<th width="50">Yarn Issue Challan No</th>
				<th width="85">Prog./ Book. No</th>
				<th width="80">Production Basis</th>
				<th width="120">Rcv. Challan No./ Service Booking No.</th>
				<th width="40">Shift</th><!--new-->
				<th width="70">Knitting Company</th>
				<th width="50">Yarn Count</th>
				<th width="50">Yarn Composition</th>
				<th width="70">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="70">Fab Color</th>
				<th width="70">Color Range</th>
				<th width="150">Fabric Type</th>
				<th width="50">Stich</th>
				<th width="50">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. No</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="50">Floor Name</th>
				<th width="80">Barcode No</th>
				<th width="40">Roll No</th>
				<th>QC Pass Qty</th>
			</tr>
		</thead>
		<?
		/*$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
		from  pro_grey_prod_entry_dtls d,  inv_receive_master e
		where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
		$result_arr = sql_select($sql_dtls_knit);
		$machine_dia_guage_arr = array();
		foreach ($result_arr as $row) {
			$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
			$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
		}*/

		$job_arr=array();
		$sql_job=sql_select("select e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping as ref_no from wo_po_break_down c, wo_po_details_master e where  c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0  and  c.status_active in(1,2) and c.is_deleted=0 group by e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping");

		foreach ($sql_job as $job_row) {
			$job_arr[$job_row[csf('id')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
			$job_arr[$job_row[csf('id')]]['po_number'] 			= $job_row[csf('po_number')];
			$job_arr[$job_row[csf('id')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
			$job_arr[$job_row[csf('id')]]["style_ref_no"] 		= $job_row[csf('style_ref_no')];
			$job_arr[$job_row[csf('id')]]["ref_no"] 		= $job_row[csf('ref_no')];
			$job_arr[$job_row[csf('id')]]["file_no"] 		= $job_row[csf('file_no')];
		}

		$sales_arr=array();
		$sql_sales=sql_select("select id,job_no_prefix_num, job_no, style_ref_no,job_no po_number,sales_booking_no from fabric_sales_order_mst where status_active=1 and is_deleted=0");
		foreach ($sql_sales as $sales_row) {
			$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["style_ref_no"] 	= $sales_row[csf('style_ref_no')];
		}
						// echo "<pre>";
						// print_r($sales_arr);

		$yarn_lot_arr = array();
		$yarn_lot_sql=sql_select("select id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0");
		foreach ($yarn_lot_sql as $value) {
			$yarn_lot_arr[$value[csf('id')]]['yarn_comp_type1st'] = $value[csf('yarn_comp_type1st')];
			$yarn_lot_arr[$value[csf('id')]]['percent1st'] = $value[csf('yarn_comp_percent1st')];
			$yarn_lot_arr[$value[csf('id')]]['type2nd'] = $value[csf('yarn_comp_type2nd')];
			$yarn_lot_arr[$value[csf('id')]]['percent2nd'] = $value[csf('yarn_comp_percent2nd')];
		}
		$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$update_id");
		foreach ($delivery_barcode_data as $row) {
			$barcode_nos .= $row[csf('barcode_no')] . ',';
			$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
		}
		$barcode_nos = rtrim($barcode_nos,", ");

		$i = 0;
		$tot_qty = 0;
		$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
		if ($is_salesOrder == 1) {

			$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source,a.knitting_company, a.receive_date, a.location_id,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm, b.floor_id,b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.barcode_no, c.roll_no, c.po_breakdown_id, c.qnty as current_delivery, c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id  and c.barcode_no in($barcode_nos) and a.entry_form=2 and c.entry_form=2  and a.booking_without_order<>1  order by a.booking_no";
		} else {
			$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm,b.floor_id, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.barcode_no, c.roll_no, c.po_breakdown_id, c.qnty as current_delivery, c.booking_no as bwo, c.booking_without_order,c.is_sales
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			WHERE a.id=b.mst_id and b.id=c.dtls_id and c.barcode_no in($barcode_nos) and a.entry_form=2 and c.entry_form=2 and a.booking_without_order<>1   order by a.booking_no";
		}

		//echo $sql;
		$order_data = array();
		$job_no_data = array();
		$booking_data = sql_select("select a.booking_no,a.buyer_id,a.job_no from wo_booking_mst a where a.status_active=1 and a.is_deleted=0");
		foreach ($booking_data as $row) {
			$order_data[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
			$order_data[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		}

		$job_data = sql_select("select a.job_no_prefix_num, a.job_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0");
		foreach ($job_data as $row) {
			$job_no_data[$row[csf('job_no')]] = $row[csf('job_no_prefix_num')];
		}

		$result = sql_select($sql);
		$loc_arr = array();
		$loc_nm = ": ";
		$k = 1;
		$j = 1;
		$style_check = array();
		$program_check = array();
		foreach ($result as $row) {
			$po_number = $row[csf('job_no')];
			$booking_no = $row[csf('sales_booking_no')];
			$booking_dtls_data = sql_select("select a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no partial_job_no from wo_booking_mst a
				inner join wo_booking_dtls b on a.booking_no = b.booking_no
				where a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no' group by a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no");

				//echo $booking_dtls_data;
			if ($loc_arr[$row[csf('location_id')]] == "") {
				$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
				$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
			}

			$knit_company = "&nbsp;";
			if ($row[csf("knitting_source")] == 1) {
				$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
			} else if ($row[csf("knitting_source")] == 3) {
				$knit_company = $supplier_arr[$row[csf("knitting_company")]];
			}

			$count = '';
			$yarn_count = explode(",", $row[csf('yarn_count')]);
			foreach ($yarn_count as $count_id) {
				if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
			}

			if ($row[csf('receive_basis')] == 1) {
					//$booking_no=explode("-",$row[csf('booking_no')]);
					//$prog_book_no=(int)$booking_no[3];
				$prog_book_no = "";
			} else $prog_book_no = $row[csf('booking_no')];

			/*if ($row[csf("receive_basis")] == 2) {
				$is_salesOrder = sql_select("select b.booking_no, a.is_sales,a.machine_gg,a.machine_dia  from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
				$plan_booking_no = $is_salesOrder[0][csf('booking_no')];
				$mc_dia = $is_salesOrder[0][csf('machine_dia')];
				$machine_gg = $is_salesOrder[0][csf('machine_gg')];
				if ($is_salesOrder[0][csf('is_sales')] == "" || $is_salesOrder[0][csf('is_sales')] == 0) {
					$is_salesOrder = 0;
				} else {
					$is_salesOrder = 1;
				}
			} else {
				$plan_booking_no = $row[csf('booking_no')];
				$mc_dia = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'];
				$machine_gg = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'];
					//echo $machine_gg.'ddddddddd';
			}*/

			$composition_string = "";
			$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
			if(count(array_filter($yarn_prod_id)) > 0)
			{
				foreach($yarn_prod_id as $val)
				{
					$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
					$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
					$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
					$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

					$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
					if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
					$composition_string .= ", ";
				}
			}
			$composition_string = chop($composition_string,", ");

				if ($row[csf("receive_basis")] == 4) // SALES ORDER
				{
					$is_salesOrder = 1;
				}
				if ($row[csf('is_sales')] == 1) {
					$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
					if($within_group == 1){
						$job_buyer = "B: " . $buyer_array[$job_arr[$row[csf('po_breakdown_id')]]['buyer_name']] . "<br>J: " . $job_arr[$row[csf('po_breakdown_id')]]['job_no_mst'];
					}else{
						$job_buyer = "B: " . $buyer_array[$job_arr[$row[csf('po_breakdown_id')]]['buyer_name']] . "<br>J: " ."";
					}
					$po_number = $sales_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
					$style_ref = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$file_ref_no = "F:" . $sales_arr[$row[csf('po_breakdown_id')]]['file_no'] . "<br>R:" . $sales_arr[$row[csf('po_breakdown_id')]]['ref_no'] ;
				} else {
					$po_number = $job_arr[$row[csf('po_breakdown_id')]]['po_number'];
					$job_buyer = "B: " . $buyer_array[$job_arr[$row[csf('po_breakdown_id')]]['buyer_name']] . "<br>J: " . $job_arr[$row[csf('po_breakdown_id')]]['job_no_mst'];
					$style_ref = $job_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$file_ref_no = "F:" . $job_arr[$row[csf('po_breakdown_id')]]['file_no'] . "<br>R:" . $job_arr[$row[csf('po_breakdown_id')]]['ref_no'] ;
				}

				if (!in_array($row[csf('booking_no')], $program_check)) {
					if ($j != 1) {
						?>
						<tr bgcolor="#CCCCCC">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right" colspan="2" style="font-weight:bold;">Program SubTotal:</td>
							<td align="right"><? echo number_format($program_tot_delivery, 2); ?></td>
						</tr>
						<?
						$program_tot_delivery = 0;
					}
					if (!in_array($style_ref, $style_check)) {
						if ($k != 1) {
							?>
							<tr bgcolor="#CCCCCC">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="2" style="font-weight:bold;">Style SubTotal:</td>
								<td align="right"><? echo number_format($style_tot_delivery, 2); ?></td>
							</tr>
							<?
							$style_tot_delivery = 0;

						}
						$style_check[] = $style_ref;
						$k++;
					}
					$program_check[] = $row[csf('booking_no')];
					$j++;
				}
				?>
				<tr>
					<td width="30"><? echo $i+1; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
					<td width="100" style="word-break:break-all;"><? echo $po_number; ?></td>
					<td width="100" style="word-break:break-all;"><? echo $style_ref; ?></td>
					<td width="100" style="word-break:break-all;"><? echo $job_buyer; ?></td>
					<td width="60"
					style="word-break:break-all;"><? echo "F:" . $row[csf('file_no')] . "<br>R:" . $row[csf('ref_no')]; ?></td>
					<td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
					<td width="50"><? echo $row[csf('yarn_issue_challan_no')]; ?></td>
					<td width="85" style="word-break:break-all;">
						P: <? echo $prog_book_no . (($prog_book_no != "") ? " /<br />" : "") . "B: " . $plan_booking_no; ?></td>
						<td width="80"  style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>

						<td width="120"  style="word-break:break-all;"><? echo "C:".$row[csf('challan_no')]."/<br />SB:".$row[csf('service_booking_no')]; ?></td>
						<td width="40"
						style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
						<td width="70"
						style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
						<td width="70" style="word-break:break-all;">
							<?
						//echo $color_arr[$row[csf("color_id")]];
							$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
							$all_color_name = "";
							foreach ($color_id_arr as $c_id) {
								$all_color_name .= $color_arr[$c_id] . ",";
							}
							$all_color_name = chop($all_color_name, ",");
							echo $all_color_name;
							?>
						</td>
						<td width="70"
						style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
						<td width="150"
						style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
						<td width="50" style="word-break:break-all;"
						align="center"><? echo $row[csf('stitch_length')]; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
						<td width="40" style="word-break:break-all;"
						align="center"><? echo $machine_details[$row[csf('machine_no_id')]]["machine_no"]; ?></td>
						<td width="40" style="word-break:break-all;"
                        align="center"><? echo $row[csf("machine_dia")];//$mc_dia;//$machine_details[$row[csf('machine_no_id')]]["dia_width"];
                        ?></td>
                        <td width="40" style="word-break:break-all;"
                        align="center"><? echo $row[csf("machine_gg")]//$machine_gg;//$machine_details[$row[csf('machine_no_id')]]["gauge"];
                        ?></td>
                        <td width="50" align="center"><? echo $floor_name_arr[$row[csf('floor_id')]]; ?></td>
                        <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
                        <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
                    </tr>
                    <?
                    $style_tot_delivery += $row[csf('current_delivery')];
                    $program_tot_delivery += $row[csf('current_delivery')];
                    $grand_program_tot_delivery += $row[csf('current_delivery')];
                    $tot_qty += $row[csf('current_delivery')];

                    $i++;
                }

                /*$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
                from  pro_grey_prod_entry_dtls d,  inv_receive_master e
                where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
                $result_arr = sql_select($sql_dtls_knit);
                $machine_dia_guage_arr = array();
                foreach ($result_arr as $row) {
                	$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
                	$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
                }*/

                /*$yarn_lot_arr = array();
                $yarn_lot_sql=sql_select("select id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0");
                foreach ($yarn_lot_sql as $value) {
                	$yarn_lot_arr[$value[csf('id')]]['yarn_comp_type1st'] = $value[csf('yarn_comp_type1st')];
                	$yarn_lot_arr[$value[csf('id')]]['percent1st'] = $value[csf('yarn_comp_percent1st')];
                	$yarn_lot_arr[$value[csf('id')]]['type2nd'] = $value[csf('yarn_comp_type2nd')];
                	$yarn_lot_arr[$value[csf('id')]]['percent2nd'] = $value[csf('yarn_comp_percent2nd')];
                }*/
				if($booking_without_order==1)
				{
                $sql_no_order = "SELECT a.recv_number_prefix_num,a.challan_no,a.service_booking_no , a.buyer_id, a.receive_basis, a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order, b.machine_dia, b.machine_gg FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and d.status_active=1 and d.is_deleted=0 and a.booking_without_order=1 order by e.seq_no";
				$result_nonorder = sql_select($sql_no_order);
				}

			//echo $sql_no_order;


                $loc_arr = array();
                $loc_nm = ": ";
                $k = 1;
                $style_check = array();
                foreach ($result_nonorder as $row) {
                	if ($loc_arr[$row[csf('location_id')]] == "") {
                		$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
                		$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
                	}

                	$knit_company = "&nbsp;";
                	if ($row[csf("knitting_source")] == 1) {
                		$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
                	} else if ($row[csf("knitting_source")] == 3) {
                		$knit_company = $supplier_arr[$row[csf("knitting_company")]];
                	}

                	$count = '';
                	$yarn_count = explode(",", $row[csf('yarn_count')]);
                	foreach ($yarn_count as $count_id) {
                		if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
                	}

                	if ($row[csf('receive_basis')] == 1) {
                		$booking_no = explode("-", $row[csf('booking_no')]);
                		$prog_book_no = (int)$booking_no[3];
                	} else $prog_book_no = $row[csf('booking_no')];

                	/*if ($row[csf('receive_basis')] == 2) {
                		$planOrder = sql_select("select b.booking_no, a.is_sales,a.machine_gg,a.machine_dia from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
                		$mc_dia = $planOrder[0][csf('machine_dia')];
                		$machine_gg = $planOrder[0][csf('machine_gg')];
                	} else {
                		$mc_dia = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'];
                		$machine_gg = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'];
                	}*/

                	$composition_string = "";
					$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
					if(count(array_filter($yarn_prod_id)) > 0)
					{
						foreach($yarn_prod_id as $val)
						{
							$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
							$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
							$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
							$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

							$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
							if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
							$composition_string .= ", ";
						}
					}
					$composition_string = chop($composition_string,", ");

                	$i++;
                	?>
                	<tr>
                		<td width="30"><? echo $i; ?></td>
                		<td width="70"
                		style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
                		<td width="100" style="word-break:break-all;"><? echo $row[csf('bwo')]; ?></td>
                		<td width="100" style="word-break:break-all;">&nbsp;</td>
                		<td width="60"
                		style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]; ?></td>
                		<td width="60" style="word-break:break-all;"><? echo "F:<br>R:"; ?></td>
                		<td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
                		<td width="50"><? echo $row[csf('yarn_issue_challan_no')]; ?></td>
                		<td width="85" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
                		<td width="80" style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
                		<td width="120"  style="word-break:break-all;"><? echo "C:".$row[csf('challan_no')]."/<br />SB:".$row[csf('service_booking_no')]; ?></td>
                		<td width="40"
                		style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
                		<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
                		<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
                		<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
                		<td width="70"
                		style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
                		<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
                		<td width="70" style="word-break:break-all;">
                			<?
						//echo $color_arr[$row[csf("color_id")]];
                			$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
                			$all_color_name = "";
                			foreach ($color_id_arr as $c_id) {
                				$all_color_name .= $color_arr[$c_id] . ",";
                			}
                			$all_color_name = chop($all_color_name, ",");
                			echo $all_color_name;
                			?>
                		</td>
                		<td width="70"
                		style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
                		<td width="150"
                		style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
                		<td width="50" style="word-break:break-all;"
                		align="center"><? echo $row[csf('stitch_length')]; ?></td>
                		<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
                		<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
                		<td width="40" style="word-break:break-all;"
                		align="center"><? echo $machine_details[$row[csf('machine_no_id')]]["machine_no"]; ?></td>
                		<td width="40" style="word-break:break-all;"
                        align="center"><? echo $row[csf("machine_dia")];// $mc_dia;//$machine_details[$row[csf('machine_no_id')]]["dia_width"];
                        ?></td>
                        <td width="40" style="word-break:break-all;"
                        align="center"><? echo $row[csf("machine_gg")] //$machine_gg;//$machine_details[$row[csf('machine_no_id')]]["gauge"];
                        ?></td>
                        <td width="50" align="center"><? echo $floor_name_arr[$row[csf('floor_id')]]; ?></td>
                        <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
                        <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
                    </tr>
                    <?
                    $tot_qty += $row[csf('current_delivery')];
                }

                $loc_nm = rtrim($loc_nm, ', ');
                ?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right" colspan="2" style="font-weight:bold;">Program SubTotal:</td>
                	<td align="right"><? echo number_format($program_tot_delivery, 2); ?></td>
                </tr>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right" colspan="2" style="font-weight:bold;">Style Total:</td>
                	<td align="right"><? echo number_format($style_tot_delivery, 2); ?></td>
                </tr>
                <tr>
                	<td align="right" colspan="28"><strong>Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td align="right" colspan="28"><strong>Program Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_delivery, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td align="right" colspan="28"><strong>Style Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_delivery, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td colspan="2" align="left"><b>Remarks:</b></td>
                	<td colspan="28">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(125, $company, "1600px"); ?></div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        	function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}

if ($action == "grey_delivery_print")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//echo '<pre>';print_r($data);die;
	$is_salesOrder = 0;
	$company = $data[0];
	$location = $data[6];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");

	$party_array = array();
	$party_data = sql_select("select id, company_name, company_short_name,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company");
	foreach ($party_data as $row) {
		$party_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$party_array[$row[csf('id')]]['address'] = $row[csf('city')].",".$country_array[$row[csf('country_id')]];
	}
	/*$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['address'] .= $row[csf('plot_no')].",".$row[csf('level_no')].",".$row[csf('road_no')].",".$row[csf('block_no')]."</br>".$row[csf('city')].",".$country_array[$row[csf('country_id')]];
	}*/
	// if($location=='')
	// {
	// 	$company_location=$company_array[$company]['address'];
	// }
	// else
	// {
	// 	$company_location= return_field_value("address","lib_location","company_id=$company and id=$location ","address");
	// }

	//echo $company_location; die;
	//if($company_location=='') $company_location=$company_array[$company]['address']; else $company_location=$company_location;

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$location_data = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$company_arr_data = return_library_array("select id, company_name from lib_company", "id", "company_name");

	//$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");


	$mstData = sql_select( "select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id,  d.barcode_no, d.qnty, d.po_breakdown_id from pro_grey_prod_delivery_mst a,pro_roll_details d where a.id=$update_id and a.id = d.mst_id  and d.entry_form = 56 and d.status_active =1 and d.is_deleted =0 group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id,d.barcode_no, d.qnty,d.po_breakdown_id");


	$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$update_id");
		foreach ($delivery_barcode_data as $row) {
			$barcode_nos .= $row[csf('barcode_no')] . ',';
			$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
		}
		$barcode_nos = rtrim($barcode_nos,", ");

	foreach ($mstData as  $row)
	{
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
	}

    $barcode_arr = array_filter($barcode_arr);
    $all_barcode_no_cond=""; $barCond="";
    $all_barcode_nos = implode(",", $barcode_arr);
    if($db_type==2 && count($barcode_arr)>999)
    {
    	$all_barcode_arr_chunk=array_chunk($barcode_arr,999) ;
    	foreach($all_barcode_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$barCond.="  c.barcode_no in($chunk_arr_value) or ";
    	}

    	$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
    }
    else
    {
    	$all_barcode_no_cond=" and c.barcode_no in($all_barcode_nos)";
    }

    $dtls_sql = sql_select("SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source,a.knitting_company, a.receive_date, a.location_id,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm, b.floor_id,b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.machine_gg, b.machine_dia, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.barcode_no, c.roll_no, c.po_breakdown_id, c.qnty as current_delivery, c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id  $all_barcode_no_cond and a.entry_form=2 and c.entry_form=2  order by a.receive_basis, a.booking_no,a.booking_without_order,c.roll_no");

    foreach ($dtls_sql as  $row)
    {
    	if($row[csf('receive_basis')] == 2)
		{
			$program_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}

		if($row[csf('booking_without_order')] == 1)
		{
			$non_ord_booking_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
		else
		{
			if($row[csf('is_sales')] == 1)
			{
				$sales_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}else{
				$po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}
		}
		$yarn_prod_arr[$row[csf('yarn_prod_id')]] =  $row[csf('yarn_prod_id')];
    }

    $program_arr = array_filter($program_arr);
	if(!empty($program_arr))
	{
		$program_sql = sql_select("select b.booking_no, a.id as program_no, a.machine_dia, a.machine_gg from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.id in (".implode(',', $program_arr).")" );

		foreach ($program_sql as  $val)
		{
			$program_ref[$val[csf("program_no")]]["booking_no"] = $val[csf("booking_no")];
			$program_ref[$val[csf("program_no")]]["machine_dia"] = $val[csf("machine_dia")];
			$program_ref[$val[csf("program_no")]]["machine_gg"] = $val[csf("machine_gg")];
		}

	}

	if(!empty($sales_id_arr))
	{
		$sql_sales = sql_select("select id, sales_booking_no, po_buyer,po_job_no,job_no,style_ref_no, within_group, buyer_id from fabric_sales_order_mst where status_active = 1 and id in (".implode(',', $sales_id_arr).")");
		foreach ($sql_sales as $sales_row)
		{
			$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["style_ref_no"] 	= $sales_row[csf('style_ref_no')];
			$sales_arr[$sales_row[csf('id')]]["po_buyer"] 	= $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]["po_job_no"] 	= $sales_row[csf('po_job_no')];
			$sales_arr[$sales_row[csf('id')]]["buyer_id"] 	= $sales_row[csf('buyer_id')];
			$sales_job_arr[$sales_row[csf('po_job_no')]] 	= $sales_row[csf('po_job_no')];
		}
	}

	$sales_job_cond="";
	$sales_job_arr = array_filter($sales_job_arr);
	if(!empty($sales_job_arr))
	{
		foreach ($sales_job_arr as $val)
		{
			$sales_job_nos .= "'".implode("','",explode(",", $val))."',";
		}
		$sales_job_nos = chop($sales_job_nos,",");
		$sales_job_cond = " and e.job_no in ($sales_job_nos)";
	}

	$job_arr=array();
	if(!empty($po_arr) || $sales_job_cond !="")
	{
		if(!empty($po_arr)){
			$po_id_cond = " and c.id in (".implode(',', $po_arr).") ";
		}
		$sql_job=sql_select("select e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping as ref_no from wo_po_break_down c, wo_po_details_master e where  c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0  and  c.status_active in(1,2) and c.is_deleted=0 $po_no_cond $sales_job_cond group by e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping");

		foreach ($sql_job as $job_row) {
			$job_arr[$job_row[csf('id')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
			$job_arr[$job_row[csf('id')]]['po_number'] 			= $job_row[csf('po_number')];
			$job_arr[$job_row[csf('id')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
			$job_arr[$job_row[csf('id')]]["style_ref_no"] 		= $job_row[csf('style_ref_no')];
			$job_arr[$job_row[csf('id')]]["ref_no"] 			= $job_row[csf('ref_no')];
			$job_arr[$job_row[csf('id')]]["file_no"] 			= $job_row[csf('file_no')];

			$job_arr[$job_row[csf('job_no')]]["ref_no"] 		.= $job_row[csf('ref_no')].",";
			$job_arr[$job_row[csf('job_no')]]["file_no"] 		.= $job_row[csf('file_no')].",";
		}
	}

	if(!empty($non_ord_booking_id_arr))
	{
		$non_booking_sql=sql_select("select buyer_id, id, booking_no from wo_non_ord_samp_booking_mst where status_active=1 and id in (". implode(',', $non_ord_booking_id_arr).")");
		foreach ($non_booking_sql as $val)
		{
			$non_ord_booking_ref[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
			$non_ord_booking_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
		}
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");

	$yarn_lot_arr = array();
	$yarn_prod_arr = array_filter($yarn_prod_arr);
	if(!empty($yarn_prod_arr))
	{
		$yarn_prod_arr_cond=""; $yProdCond="";
	    $yarn_prod_ids = implode(",", $yarn_prod_arr);
	    if($db_type==2 && count($yarn_prod_arr)>999)
	    {
	    	$yarn_prod_arr_chunk=array_chunk($yarn_prod_arr,999) ;
	    	foreach($yarn_prod_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$yProdCond.="  id in($chunk_arr_value) or ";
	    	}

	    	$yarn_prod_arr_cond.=" and (".chop($yProdCond,'or ').")";
	    }
	    else
	    {
	    	$yarn_prod_arr_cond=" and id in($yarn_prod_ids)";
	    }
		$yarn_lot_sql=sql_select("select id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 $yarn_prod_arr_cond");
		foreach ($yarn_lot_sql as $value) {
			$yarn_lot_arr[$value[csf('id')]]['yarn_comp_type1st'] = $value[csf('yarn_comp_type1st')];
			$yarn_lot_arr[$value[csf('id')]]['percent1st'] = $value[csf('yarn_comp_percent1st')];
			$yarn_lot_arr[$value[csf('id')]]['type2nd'] = $value[csf('yarn_comp_type2nd')];
			$yarn_lot_arr[$value[csf('id')]]['percent2nd'] = $value[csf('yarn_comp_percent2nd')];
		}
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1700px;">
		<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td rowspan="4" colspan="2">
					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px;"><? echo $com_dtls[0]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><? echo $com_dtls[1]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px;"><u>Delivery Challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="80">Party</td>
				<td style="font-size:14px; " width="170">:&nbsp;<? echo $party_array[$sales_arr[$mstData[0][csf('po_breakdown_id')]]["buyer_id"]]['name'] ; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="60">Party Location</td>
				<td width="170">:&nbsp;<? echo $party_array[$sales_arr[$mstData[0][csf('po_breakdown_id')]]["buyer_id"]]['address']; ?></td>

			<tr>
				<td style="font-size:14px; font-weight:bold;" width="80">Challan No</td>
				<td style="font-size:14px; font-weight:bold;" width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="60">Location</td>
				<td width="170">:&nbsp;<? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
				<td width="810" id="barcode_img_id" align="right"></td>

			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:14px; font-weight:bold;">Remarks</td>
				<td colspan="4" style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
			</tr>

			<tr>
				<td style="font-size:14px; font-weight:bold;">Floor No</td>
				<td>:&nbsp;<? echo $floor_name ?></td>
				<?php if ($is_po && ($is_salesOrder == 1)) { ?>
					<td style="font-size:14px; font-weight:bold;">PO Company</td>
					<td>:&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
				<?php } ?>
			</tr>


		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1850" class="rpt_table"
		style="font-family: tahoma; font-size: 12px;">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="70">Production Date</th><!--new-->
				<th width="90">Order/FSO No</th>
				<th width="100">Style No</th>
				<th width="100"><?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?>Buyer
					/<br> <?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?> Job
				</th>
				<th width="60">File No <br> Ref No</th>
				<th width="50">System ID</th>
				<th width="50">Yarn Issue Challan No</th>
				<th width="85">Prog./ Book. No</th>
				<th width="80">Production Basis</th>
				<th width="120">Rcv. Challan No./ Service Booking No.</th>
				<th width="40">Shift</th>
				<th width="120">Knitting Company</th>
				<th width="50">Yarn Count</th>
				<th width="50">Yarn Composition</th>
				<th width="70">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="70">Fab Color</th>
				<th width="70">Color Range</th>
				<th width="150">Fabric Type</th>
				<th width="50">Stich</th>
				<th width="50">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. No</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="50">Floor Name</th>
				<th width="80">Barcode No</th>
				<th width="40">Roll No</th>
				<th>QC Pass Qty</th>
			</tr>
		</thead>
		<?

		$i = 0;
		$tot_qty = 0;
		$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");


		$loc_arr = array();
		$loc_nm = ": ";
		$k = 1;
		$j = 1;
		$style_check = array();
		$program_check = array();
		foreach ($dtls_sql as $row)
		{
			if ($loc_arr[$row[csf('location_id')]] == "") {
				$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
				$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
			}

			$knit_company = "&nbsp;";
			if ($row[csf("knitting_source")] == 1) {
				$knit_company = $company_arr_data[$row[csf("knitting_company")]];
			} else if ($row[csf("knitting_source")] == 3) {
				$knit_company = $supplier_arr[$row[csf("knitting_company")]];
			}

			$count = '';
			$yarn_count = explode(",", $row[csf('yarn_count')]);
			foreach ($yarn_count as $count_id) {
				if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
			}

			if ($row[csf('receive_basis')] == 1) {
				$prog_book_no = "";
				$plan_booking_no = $row[csf('booking_no')];
				$machine_gg = $row[csf('machine_gg')];
				$machine_dia = $row[csf('machine_dia')];
			}
			else
			{
				$prog_book_no = $row[csf('booking_no')];
				$plan_booking_no = $program_ref[$row[csf('booking_no')]]["booking_no"];
				$machine_gg = $program_ref[$row[csf('booking_no')]]["machine_gg"];
				$machine_dia = $program_ref[$row[csf('booking_no')]]["machine_dia"];
			}


			$composition_string = "";
			$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
			if(count(array_filter($yarn_prod_id)) > 0)
			{
				foreach($yarn_prod_id as $val)
				{
					$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
					$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
					$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
					$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

					$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
					if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
					$composition_string .= ", ";
				}
			}
			$composition_string = chop($composition_string,", ");

				if ($row[csf("receive_basis")] == 4)
				{
					$is_salesOrder = 1;
				}
				if ($row[csf('is_sales')] == 1)
				{
					$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
					if($within_group == 1){
						$job_buyer = "B: " . $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["po_buyer"]] . "<br>J: " . $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
					}else{
						$job_buyer = "B: " . $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["buyer_id"]] . "<br>J: " ."";
					}
					$po_number = $sales_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
					$style_ref = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$ref_no=$file_no="";
					$sales_po_job_no = $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
					$sales_po_job_no_arr = explode(",", $sales_po_job_no);
					foreach ($sales_po_job_no_arr as $sales_job)
					{
						$file_no = $job_arr[$sales_job]["file_no"].",";
					 	$ref_no = $job_arr[$sales_job]["ref_no"].",";
					}

					$file_no = implode(",",array_filter(array_unique(explode(",", $file_no))));
					$ref_no = implode(",",array_filter(array_unique(explode(",", $ref_no))));
					$file_ref_no = "F:" . $file_no . "<br>R:" . $ref_no ;
				} else {
					$po_number = $job_arr[$row[csf('po_breakdown_id')]]['po_number'];
					$job_buyer = "B: " . $buyer_array[$job_arr[$row[csf('po_breakdown_id')]]['buyer_name']] . "<br>J: " . $job_arr[$row[csf('po_breakdown_id')]]['job_no_mst'];
					$style_ref = $job_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$file_ref_no = "F:" . $job_arr[$row[csf('po_breakdown_id')]]['file_no'] . "<br>R:" . $job_arr[$row[csf('po_breakdown_id')]]['ref_no'] ;
				}

				if($row[csf('booking_without_order')] == 1)
				{
					$job_buyer = "B: " . $buyer_array[$non_ord_booking_ref[$row[csf("po_breakdown_id")]]["buyer_name"]] . "<br>J: " ."";
				}

				if (!in_array($row[csf('booking_no')], $program_check)) {
					if ($j != 1) {
						?>
						<tr bgcolor="#CCCCCC">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right" colspan="2" style="font-weight:bold;">Program SubTotal:</td>
							<td align="right"><? echo number_format($program_tot_delivery, 2); ?></td>
						</tr>
						<?
						$program_tot_delivery = 0;
					}
					if (!in_array($style_ref, $style_check)) {
						if ($k != 1) {
							?>
							<tr bgcolor="#CCCCCC">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="2" style="font-weight:bold;">Style SubTotal:</td>
								<td align="right"><? echo number_format($style_tot_delivery, 2); ?></td>
							</tr>
							<?
							$style_tot_delivery = 0;

						}
						$style_check[] = $style_ref;
						$k++;
					}
					$program_check[] = $row[csf('booking_no')];
					$j++;
				}
				?>
					<tr>
						<td width="30"><? echo $i+1; ?></td>
						<td width="70" style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
						<td width="100" style="word-break:break-all;"><? echo $po_number; ?></td>
						<td width="100" style="word-break:break-all;"><? echo $style_ref; ?></td>
						<td width="100" style="word-break:break-all;"><? echo $job_buyer; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $file_ref_no; ?></td>
						<td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
						<td width="50"><? echo $row[csf('yarn_issue_challan_no')]; ?></td>
						<td width="85" style="word-break:break-all;">
							P: <? echo $prog_book_no . (($prog_book_no != "") ? " /<br />" : "") . "B: " . $plan_booking_no; ?>
						</td>
						<td width="80"  style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
						<td width="120"  style="word-break:break-all;">
							<? echo "C:".$row[csf('challan_no')]."/<br />SB:".$row[csf('service_booking_no')]; ?>
						</td>
						<td width="40" style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
						<td width="120" style="word-break:break-all;"><? echo $knit_company; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
						<td width="70" style="word-break:break-all;">
							<?
							$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
							$all_color_name = "";
							foreach ($color_id_arr as $c_id) {
								$all_color_name .= $color_arr[$c_id] . ",";
							}
							$all_color_name = chop($all_color_name, ",");
							echo $all_color_name;
							?>
						</td>
						<td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
						<td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
						<td width="40" style="word-break:break-all;" align="center">
							<? echo $machine_details[$row[csf('machine_no_id')]]["machine_no"]; ?>
						</td>
						<td width="40" style="word-break:break-all;" align="center">
							<? echo $machine_dia; ?>
	                    </td>
	                    <td width="40" style="word-break:break-all;" align="center">
	                    	<? echo $machine_gg; ?>
	                    </td>
	                    <td width="50" align="center"><? echo $floor_name_arr[$row[csf('floor_id')]]; ?></td>
	                    <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
	                    <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
	                    <td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
	                </tr>
                    <?
                    $style_tot_delivery += $row[csf('current_delivery')];
                    $program_tot_delivery += $row[csf('current_delivery')];
                    $grand_program_tot_delivery += $row[csf('current_delivery')];
                    $tot_qty += $row[csf('current_delivery')];

                    $i++;
                }

                $loc_arr = array();
                $loc_nm = ": ";
                $k = 1;
                $style_check = array();

                $loc_nm = rtrim($loc_nm, ', ');
                ?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right" colspan="2" style="font-weight:bold;">Program SubTotal:</td>
                	<td align="right"><? echo number_format($program_tot_delivery, 2); ?></td>
                </tr>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right" colspan="2" style="font-weight:bold;">Style Total:</td>
                	<td align="right"><? echo number_format($style_tot_delivery, 2); ?></td>
                </tr>
                <tr>
                	<td align="right" colspan="28"><strong>Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td align="right" colspan="28"><strong>Program Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_delivery, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td align="right" colspan="28"><strong>Style Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_delivery, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td colspan="2" align="left"><b>Remarks:</b></td>
                	<td colspan="28">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(125, $company, "1600px"); ?></div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        	function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}

if ($action == "grey_delivery_print11")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//echo '<pre>';print_r($data);die;
	$is_salesOrder = 0;
	$company = $data[0];
	$location = $data[6];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	/*$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['address'] .= $row[csf('plot_no')].",".$row[csf('level_no')].",".$row[csf('road_no')].",".$row[csf('block_no')]."</br>".$row[csf('city')].",".$country_array[$row[csf('country_id')]];
	}*/
	// if($location=='')
	// {
	// 	$company_location=$company_array[$company]['address'];
	// }
	// else
	// {
	// 	$company_location= return_field_value("address","lib_location","company_id=$company and id=$location ","address");
	// }

	//echo $company_location; die;
	//if($company_location=='') $company_location=$company_array[$company]['address']; else $company_location=$company_location;

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$location_data = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$company_arr_data = return_library_array("select id, company_name from lib_company", "id", "company_name");

	//$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");


	$mstData = sql_select( "select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id,  d.barcode_no, d.qnty from pro_grey_prod_delivery_mst a,pro_roll_details d where a.id=$update_id and a.id = d.mst_id  and d.entry_form = 56 and d.status_active =1 and d.is_deleted =0 group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id,d.barcode_no, d.qnty");


	$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$update_id");
		foreach ($delivery_barcode_data as $row) {
			$barcode_nos .= $row[csf('barcode_no')] . ',';
			$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
		}
		$barcode_nos = rtrim($barcode_nos,", ");

	foreach ($mstData as  $row)
	{
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
	} 

    $barcode_arr = array_filter($barcode_arr);
    $all_barcode_no_cond=""; $barCond="";
    $all_barcode_nos = implode(",", $barcode_arr);
    if($db_type==2 && count($barcode_arr)>999)
    {
    	$all_barcode_arr_chunk=array_chunk($barcode_arr,999) ;
    	foreach($all_barcode_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$barCond.="  c.barcode_no in($chunk_arr_value) or ";
    	}

    	$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
    }
    else
    {
    	$all_barcode_no_cond=" and c.barcode_no in($all_barcode_nos)";
    }

    $dtls_sql = sql_select("SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source,a.knitting_company, a.receive_date, a.location_id,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm, b.floor_id,b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.machine_gg, b.machine_dia, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.barcode_no, c.roll_no, c.po_breakdown_id, c.qnty as current_delivery, c.booking_no as bwo, c.booking_without_order,c.is_sales, b.body_part_id, c.reject_qnty, c.coller_cuff_size, c.qc_pass_qnty_pcs as grey_receive_qnty_pcs FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id  $all_barcode_no_cond and a.entry_form=2 and c.entry_form=2  order by a.receive_basis, a.booking_no,a.booking_without_order,c.roll_no");
  
    foreach ($dtls_sql as  $row)
    {
    	if($row[csf('receive_basis')] == 2)
		{
			$program_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}

		if($row[csf('booking_without_order')] == 1)
		{
			$non_ord_booking_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
		else
		{
			if($row[csf('is_sales')] == 1)
			{
				$sales_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}else{
				$po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}
		}
		$yarn_prod_arr[$row[csf('yarn_prod_id')]] =  $row[csf('yarn_prod_id')];
    }

    $program_arr = array_filter($program_arr);
	if(!empty($program_arr))
	{
		$program_sql = sql_select("select b.booking_no, a.id as program_no, a.machine_dia, a.machine_gg from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.id in (".implode(',', $program_arr).")" );
		

		foreach ($program_sql as  $val)
		{
			$program_ref[$val[csf("program_no")]]["booking_no"] = $val[csf("booking_no")];
			$program_ref[$val[csf("program_no")]]["machine_dia"] = $val[csf("machine_dia")];
			$program_ref[$val[csf("program_no")]]["machine_gg"] = $val[csf("machine_gg")];
		}

	} 
	if(!empty($sales_id_arr))
	{
		// $sql_sales = sql_select("select id, sales_booking_no, po_buyer,po_job_no,job_no,style_ref_no, within_group, buyer_id from fabric_sales_order_mst where status_active = 1 and id in (".implode(',', $sales_id_arr).")");

		$sql_sales = sql_select("select id, sales_booking_no, po_buyer,po_job_no,job_no,style_ref_no, within_group,buyer_id,case when booking_type=1 then 'Main' when booking_type=4 then 'Sample' when booking_type=9 then 'Excess' end as booking_type from fabric_sales_order_mst where status_active = 1 and id in (".implode(',', $sales_id_arr).")");   //al-hassan update

		foreach ($sql_sales as $sales_row)
		{
			$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["booking_type"] 		= $sales_row[csf('booking_type')];
			$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["style_ref_no"] 	= $sales_row[csf('style_ref_no')];
			$sales_arr[$sales_row[csf('id')]]["po_buyer"] 	= $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]["po_job_no"] 	= $sales_row[csf('po_job_no')];
			$sales_arr[$sales_row[csf('id')]]["buyer_id"] 	= $sales_row[csf('buyer_id')];
			$sales_job_arr[$sales_row[csf('po_job_no')]] 	= $sales_row[csf('po_job_no')];
		}
	}

	$sales_job_cond="";
	$sales_job_arr = array_filter($sales_job_arr);
	if(!empty($sales_job_arr))
	{
		foreach ($sales_job_arr as $val)
		{
			$sales_job_nos .= "'".implode("','",explode(",", $val))."',";
		}
		$sales_job_nos = chop($sales_job_nos,",");
		$sales_job_cond = " and e.job_no in ($sales_job_nos)";
	}

	$job_arr=array();
	if(!empty($po_arr) || $sales_job_cond !="")
	{
		if(!empty($po_arr)){
			$po_id_cond = " and c.id in (".implode(',', $po_arr).") ";
		}
		$sql_job=sql_select("select e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping as ref_no from wo_po_break_down c, wo_po_details_master e where  c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0  and  c.status_active in(1,2) and c.is_deleted=0 $po_no_cond $sales_job_cond group by e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping");

		foreach ($sql_job as $job_row) {
			$job_arr[$job_row[csf('id')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
			$job_arr[$job_row[csf('id')]]['po_number'] 			= $job_row[csf('po_number')];
			$job_arr[$job_row[csf('id')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
			$job_arr[$job_row[csf('id')]]["style_ref_no"] 		= $job_row[csf('style_ref_no')];
			$job_arr[$job_row[csf('id')]]["ref_no"] 			= $job_row[csf('ref_no')];
			$job_arr[$job_row[csf('id')]]["file_no"] 			= $job_row[csf('file_no')];

			$job_arr[$job_row[csf('job_no')]]["ref_no"] 		.= $job_row[csf('ref_no')].",";
			$job_arr[$job_row[csf('job_no')]]["file_no"] 		.= $job_row[csf('file_no')].",";
		}
	}

	if(!empty($non_ord_booking_id_arr))
	{
        // $non_booking_sql=sql_select("select buyer_id, id, booking_no from wo_non_ord_samp_booking_mst where status_active=1 and id in (". implode(',', $non_ord_booking_id_arr).")");
		// foreach ($non_booking_sql as $val)
		// {
		// 	$non_ord_booking_ref[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
		// 	$non_ord_booking_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
		// }
		$non_booking_sql=sql_select("select buyer_id, id, booking_no,case when booking_type=1 then 'Main' when booking_type=4 then 'Sample' when booking_type=9 then 'Excess' end as booking_type from wo_non_ord_samp_booking_mst where status_active=1 and id in (". implode(',', $non_ord_booking_id_arr).")");
		foreach ($non_booking_sql as $val)
		{
			$non_ord_booking_ref[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
			$non_ord_booking_ref[$val[csf("id")]]["booking_type"] = $val[csf("booking_type")];
			$non_ord_booking_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
		} // al-hassan update
 
		
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");

	$yarn_lot_arr = array();
	$yarn_prod_arr = array_filter($yarn_prod_arr);
	if(!empty($yarn_prod_arr))
	{
		$yarn_prod_arr_cond=""; $yProdCond="";
	    $yarn_prod_ids = implode(",", $yarn_prod_arr);
	    if($db_type==2 && count($yarn_prod_arr)>999)
	    {
	    	$yarn_prod_arr_chunk=array_chunk($yarn_prod_arr,999) ;
	    	foreach($yarn_prod_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$yProdCond.="  id in($chunk_arr_value) or ";
	    	}

	    	$yarn_prod_arr_cond.=" and (".chop($yProdCond,'or ').")";
	    }
	    else
	    {
	    	$yarn_prod_arr_cond=" and id in($yarn_prod_ids)";
	    }
		$yarn_lot_sql=sql_select("select id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 $yarn_prod_arr_cond");
		foreach ($yarn_lot_sql as $value) {
			$yarn_lot_arr[$value[csf('id')]]['yarn_comp_type1st'] = $value[csf('yarn_comp_type1st')];
			$yarn_lot_arr[$value[csf('id')]]['percent1st'] = $value[csf('yarn_comp_percent1st')];
			$yarn_lot_arr[$value[csf('id')]]['type2nd'] = $value[csf('yarn_comp_type2nd')];
			$yarn_lot_arr[$value[csf('id')]]['percent2nd'] = $value[csf('yarn_comp_percent2nd')];
		}
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1700px;">
		<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td rowspan="4" colspan="2">
					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px;"><? echo $com_dtls[0]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><? echo $com_dtls[1]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px;"><u>Delivery Challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="80">Challan No</td>
				<td style="font-size:14px; font-weight:bold;" width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="60">Location</td>
				<td width="170">:&nbsp;<? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
				<td width="810" id="barcode_img_id" align="right"></td>

			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:14px; font-weight:bold;">Remarks</td>
				<td colspan="4" style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
			</tr>

			<tr>
				<td style="font-size:14px; font-weight:bold;">Floor No</td>
				<td>:&nbsp;<? echo $floor_name ?></td>
				<?php if ($is_po && ($is_salesOrder == 1)) { ?>
					<td style="font-size:14px; font-weight:bold;">PO Company</td>
					<td>:&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
				<?php } ?>
			</tr>


		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1890" class="rpt_table"
		style="font-family: tahoma; font-size: 12px;">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="70">Production Date</th><!--new-->
				<th width="90">Order/FSO No</th>
				<th width="100">Style No</th>
				<th width="100"><?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?>Buyer
					/<br> <?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?> Job
				</th>
				<th width="60">File No <br> Ref No</th>
				<th width="50">System ID</th>
				<th width="50">Body Part</th>
				<th width="85">Prog./ Book. No</th>
				
				<!-- <th width="80">Production Basis</th> -->
				<th width="80">Booking Type</th>
				<th width="120">Rcv. Challan No./ Service Booking No.</th>
				<th width="40">Shift</th>
				<th width="120">Knitting Company</th>
				<th width="50">Yarn Count</th>
				<th width="50">Yarn Composition</th>
				<th width="70">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="70">Fab Color</th>
				<th width="70">Color Range</th>
				<th width="150">Fabric Type</th>
				<th width="50">Stich</th>
				<th width="50">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. No</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="50">F/Size</th>
				<th width="80">Barcode No</th>
				<th width="40">Roll No</th>
				<th width="40">Qnty in Pcs</th>
				<th width="80">QC Pass Qty</th>
				<th>Reject Qty</th>
			</tr>
		</thead>
		<?

		$i = 0;
		$tot_qty = 0;
		$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");


		$loc_arr = array();
		$loc_nm = ": ";
		$k = 1;
		$j = 1;
		$style_check = array();
		$program_check = array();
		foreach ($dtls_sql as $row)
		{
			if ($loc_arr[$row[csf('location_id')]] == "") {
				$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
				$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
			}

			$knit_company = "&nbsp;";
			if ($row[csf("knitting_source")] == 1) {
				$knit_company = $company_arr_data[$row[csf("knitting_company")]];
			} else if ($row[csf("knitting_source")] == 3) {
				$knit_company = $supplier_arr[$row[csf("knitting_company")]];
			}

			$count = '';
			$yarn_count = explode(",", $row[csf('yarn_count')]);
			foreach ($yarn_count as $count_id) {
				if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
			}

			if ($row[csf('receive_basis')] == 1) {
				$prog_book_no = "";
				$plan_booking_no = $row[csf('booking_no')];
				$machine_gg = $row[csf('machine_gg')];
				$machine_dia = $row[csf('machine_dia')];
			}
			else
			{
				$prog_book_no = $row[csf('booking_no')];
				$plan_booking_no = $program_ref[$row[csf('booking_no')]]["booking_no"];
				$machine_gg = $program_ref[$row[csf('booking_no')]]["machine_gg"];
				$machine_dia = $program_ref[$row[csf('booking_no')]]["machine_dia"];
			}


			$composition_string = "";
			$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
			if(count(array_filter($yarn_prod_id)) > 0)
			{
				foreach($yarn_prod_id as $val)
				{
					$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
					$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
					$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
					$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

					$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
					if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
					$composition_string .= ", ";
				}
			}
			$composition_string = chop($composition_string,", ");

				if ($row[csf("receive_basis")] == 4)
				{
					$is_salesOrder = 1;
				}
				if ($row[csf('is_sales')] == 1)
				{
					$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
					if($within_group == 1){
						$job_buyer = "B: " . $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["po_buyer"]] . "<br>J: " . $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
					}else{
						$job_buyer = "B: " . $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["buyer_id"]] . "<br>J: " ."";
					}
					$po_number = $sales_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
					$style_ref = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$ref_no=$file_no="";
					$sales_po_job_no = $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
					$sales_po_job_no_arr = explode(",", $sales_po_job_no);
					foreach ($sales_po_job_no_arr as $sales_job)
					{
						$file_no = $job_arr[$sales_job]["file_no"].",";
					 	$ref_no = $job_arr[$sales_job]["ref_no"].",";
					}

					$file_no = implode(",",array_filter(array_unique(explode(",", $file_no))));
					$ref_no = implode(",",array_filter(array_unique(explode(",", $ref_no))));
					$file_ref_no = "F:" . $file_no . "<br>R:" . $ref_no ;
				} else {
					$po_number = $job_arr[$row[csf('po_breakdown_id')]]['po_number'];
					$job_buyer = "B: " . $buyer_array[$job_arr[$row[csf('po_breakdown_id')]]['buyer_name']] . "<br>J: " . $job_arr[$row[csf('po_breakdown_id')]]['job_no_mst'];
					$style_ref = $job_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$file_ref_no = "F:" . $job_arr[$row[csf('po_breakdown_id')]]['file_no'] . "<br>R:" . $job_arr[$row[csf('po_breakdown_id')]]['ref_no'] ;
				}

				if($row[csf('booking_without_order')] == 1)
				{
					$job_buyer = "B: " . $buyer_array[$non_ord_booking_ref[$row[csf("po_breakdown_id")]]["buyer_name"]] . "<br>J: " ."";
				}

				if (!in_array($row[csf('booking_no')], $program_check)) {
					if ($j != 1) {
						?>
						<tr bgcolor="#CCCCCC">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right" colspan="2" style="font-weight:bold;">Program SubTotal:</td>
							<td align="right"><? echo number_format($program_tot_qnty_in_pcs, 2); ?></td>
							<td align="right"><? echo number_format($program_tot_delivery, 2); ?></td>
							<td align="right"><? echo number_format($program_tot_reject, 2); ?></td>
						</tr>
						<?
						$program_tot_qnty_in_pcs = 0;
						$program_tot_delivery = 0;
						$program_tot_reject = 0;
					}
					if (!in_array($style_ref, $style_check)) {
						if ($k != 1) {
							?>
							<tr bgcolor="#CCCCCC">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="2" style="font-weight:bold;">Style SubTotal:</td>
								<td align="right"><? echo number_format($style_tot_qnty_in_pcs, 2); ?></td>
								<td align="right"><? echo number_format($style_tot_delivery, 2); ?></td>
								<td align="right"><? echo number_format($style_tot_reject, 2); ?></td>
							</tr>
							<?
							$style_tot_qnty_in_pcs = 0;
							$style_tot_delivery = 0;
							$style_tot_reject = 0;

						}
						$style_check[] = $style_ref;
						$k++;
					}
					$program_check[] = $row[csf('booking_no')];
					$j++;
				}
				?>
					<tr>
						<td width="30"><? echo $i+1; ?></td>
						<td width="70" style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
						<td width="100" style="word-break:break-all;"><? echo $po_number; ?></td>
						<td width="100" style="word-break:break-all;"><? echo $style_ref; ?></td>
						<td width="100" style="word-break:break-all;"><? echo $job_buyer; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $file_ref_no; ?></td>
						<td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
						<td width="50"><? echo $body_part[$row[csf("body_part_id")]]; ?></td>
						<td width="85" style="word-break:break-all;">
							P: <? echo $prog_book_no . (($prog_book_no != "") ? " /<br />" : "") . "B: " . $plan_booking_no; ?>
						</td>
						<!-- <td width="80"  style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td> -->
						<td width="80" style="word-break:break-all; text-align:center;"><? echo $sales_arr[$sales_row[csf('id')]]["booking_type"]; ?></td>


						<td width="120"  style="word-break:break-all;">
							<? echo "C:".$row[csf('challan_no')]."/<br />SB:".$row[csf('service_booking_no')]; ?> 
						</td> 
						<td width="40" style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
						<td width="120" style="word-break:break-all;"><? echo $knit_company; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
						<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
						<td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
						<td width="70" style="word-break:break-all;">
							<?
							$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
							$all_color_name = "";
							foreach ($color_id_arr as $c_id) {
								$all_color_name .= $color_arr[$c_id] . ",";
							}
							$all_color_name = chop($all_color_name, ",");
							echo $all_color_name;
							?>
						</td>
						<td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
						<td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
						<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
						<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
						<td width="40" style="word-break:break-all;" align="center">
							<? echo $machine_details[$row[csf('machine_no_id')]]["machine_no"]; ?>
						</td>
						<td width="40" style="word-break:break-all;" align="center">
							<? echo $machine_dia; ?>
	                    </td>
	                    <td width="40" style="word-break:break-all;" align="center">
	                    	<? echo $machine_gg; ?>
	                    </td>
	                    <td width="50" align="center"><? echo $row[csf('coller_cuff_size')]; ?></td>
	                    <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
	                    <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
	                    <td width="40" align="right"><? echo number_format($row[csf('grey_receive_qnty_pcs')], 2); ?></td>
	                    <td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
	                    <td align="right"><? echo number_format($row[csf('reject_qnty')], 2); ?></td>
	                </tr>
                    <?
                    $style_tot_qnty_in_pcs += $row[csf('grey_receive_qnty_pcs')];
                    $style_tot_delivery += $row[csf('current_delivery')];
                    $style_tot_reject += $row[csf('reject_qnty')];
                    $program_tot_qnty_in_pcs += $row[csf('grey_receive_qnty_pcs')];
                    $program_tot_delivery += $row[csf('current_delivery')];
                    $program_tot_reject += $row[csf('reject_qnty')];
                    $grand_program_tot_qnty_in_pcs += $row[csf('grey_receive_qnty_pcs')];
                    $grand_program_tot_delivery += $row[csf('current_delivery')];
                    $grand_program_tot_reject += $row[csf('reject_qnty')];
                    $tot_qnty_in_pcs += $row[csf('grey_receive_qnty_pcs')];
                    $tot_qty += $row[csf('current_delivery')];
                    $tot_reject_qty += $row[csf('reject_qnty')];

                    $i++;
                }

                $loc_arr = array();
                $loc_nm = ": ";
                $k = 1;
                $style_check = array();

                $loc_nm = rtrim($loc_nm, ', ');
                ?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right" colspan="2" style="font-weight:bold;">Program SubTotal:</td>
                	<td align="right"><? echo number_format($program_tot_qnty_in_pcs, 2); ?></td>
                	<td align="right"><? echo number_format($program_tot_delivery, 2); ?></td>
                	<td align="right"><? echo number_format($program_tot_reject, 2); ?></td>
                </tr>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right" colspan="2" style="font-weight:bold;">Style Total:</td>
                	<td align="right"><? echo number_format($style_tot_qnty_in_pcs, 2); ?></td>
                	<td align="right"><? echo number_format($style_tot_delivery, 2); ?></td>
                	<td align="right"><? echo number_format($style_tot_reject, 2); ?></td>
                </tr>
                <tr>
                	<td align="right" colspan="28"><strong>Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($tot_qnty_in_pcs, 2, '.', ''); ?></td>
                	<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
                	<td align="right"><? echo number_format($tot_reject_qty, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td align="right" colspan="28"><strong>Program Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_qnty_in_pcs, 2, '.', ''); ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_delivery, 2, '.', ''); ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_reject, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td align="right" colspan="28"><strong>Style Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_qnty_in_pcs, 2, '.', ''); ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_delivery, 2, '.', ''); ?></td>
                	<td align="right"><? echo number_format($grand_program_tot_reject, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td colspan="2" align="left"><b>Remarks:</b></td>
                	<td colspan="30">&nbsp;</td>
                </tr>
            </table>
        </div>
        <div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(125, $company, "1600px"); ?></div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        	function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}

if ($action == "grey_delivery_print12")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//echo '<pre>';print_r($data);die;
	$is_salesOrder = 0;
	$company = $data[0];
	$location = $data[6];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");

	$party_array = array();
	$party_data = sql_select("select id, company_name, company_short_name,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company");
	foreach ($party_data as $row) {
		$party_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$party_array[$row[csf('id')]]['address'] = $row[csf('city')].",".$country_array[$row[csf('country_id')]];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$location_data = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$company_arr_data = return_library_array("select id, company_name from lib_company", "id", "company_name");

	//$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");


	$mstData = sql_select( "select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id,  d.barcode_no, d.qnty, d.po_breakdown_id from pro_grey_prod_delivery_mst a,pro_roll_details d where a.id=$update_id and a.id = d.mst_id  and d.entry_form = 56 and d.status_active =1 and d.is_deleted =0 group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id,d.barcode_no, d.qnty,d.po_breakdown_id");


	$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$update_id");
		foreach ($delivery_barcode_data as $row) {
			$barcode_nos .= $row[csf('barcode_no')] . ',';
			$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
		}
		$barcode_nos = rtrim($barcode_nos,", ");

	foreach ($mstData as  $row)
	{
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
	}

    $barcode_arr = array_filter($barcode_arr);
    $all_barcode_no_cond=""; $barCond="";
    $all_barcode_nos = implode(",", $barcode_arr);
    if($db_type==2 && count($barcode_arr)>999)
    {
    	$all_barcode_arr_chunk=array_chunk($barcode_arr,999) ;
    	foreach($all_barcode_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$barCond.="  c.barcode_no in($chunk_arr_value) or ";
    	}

    	$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
    }
    else
    {
    	$all_barcode_no_cond=" and c.barcode_no in($all_barcode_nos)";
    }

	$dtls_sql = sql_select("SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source,a.knitting_company, a.receive_date, a.location_id,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm, b.floor_id,b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.machine_gg, b.machine_dia, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.barcode_no, c.roll_no, c.po_breakdown_id, c.qnty as current_delivery, c.booking_no as bwo, c.booking_without_order,c.is_sales, c.reject_qnty
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id $all_barcode_no_cond and a.entry_form=2 and c.entry_form=2
	order by b.shift_name");
	//order by a.receive_basis, a.booking_no,a.booking_without_order,c.roll_no, b.shift_name

    foreach ($dtls_sql as  $row)
    {
    	if($row[csf('receive_basis')] == 2)
		{
			$program_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}

		if($row[csf('booking_without_order')] == 1)
		{
			$non_ord_booking_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
		else
		{
			if($row[csf('is_sales')] == 1)
			{
				$sales_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}else{
				$po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}
		}
		$yarn_prod_arr[$row[csf('yarn_prod_id')]] =  $row[csf('yarn_prod_id')];
		$shift_count_arr[$row[csf('shift_name')]]++;
		$shift_tot_qty_arr[$row[csf('shift_name')]] +=  $row[csf('current_delivery')];
		$shift_tot_rej_arr[$row[csf('shift_name')]] +=  $row[csf('reject_qnty')];
    }
    // echo "<pre>";print_r($shift_count_arr);

    $program_arr = array_filter($program_arr);
	if(!empty($program_arr))
	{
		$program_sql = sql_select("select b.booking_no, a.id as program_no, a.machine_dia, a.machine_gg from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.id in (".implode(',', $program_arr).")" );

		foreach ($program_sql as  $val)
		{
			$program_ref[$val[csf("program_no")]]["booking_no"] = $val[csf("booking_no")];
			$program_ref[$val[csf("program_no")]]["machine_dia"] = $val[csf("machine_dia")];
			$program_ref[$val[csf("program_no")]]["machine_gg"] = $val[csf("machine_gg")];
		}

		$program_wise_mc=sql_select("SELECT a.id as program_no, b.machine_id, b.distribution_qnty
		from ppl_planning_info_entry_dtls a, ppl_planning_info_machine_dtls b
		where a.id=b.dtls_id and a.mst_id=b.mst_id  and a.id in (".implode(',', $program_arr).")");
		$program_and_mc_wise_distribut_qty_arr=array();
		foreach ($program_wise_mc as  $val)
		{
			$program_and_mc_wise_distribut_qty_arr[$val[csf("program_no")]][$val[csf("machine_id")]] += $val[csf("distribution_qnty")];
		}
	}

	if(!empty($sales_id_arr))
	{
		$sql_sales = sql_select("select id, sales_booking_no, po_buyer,po_job_no,job_no,style_ref_no, within_group, buyer_id from fabric_sales_order_mst where status_active = 1 and id in (".implode(',', $sales_id_arr).")");
		foreach ($sql_sales as $sales_row)
		{
			$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["style_ref_no"] 	= $sales_row[csf('style_ref_no')];
			$sales_arr[$sales_row[csf('id')]]["po_buyer"] 	= $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]["po_job_no"] 	= $sales_row[csf('po_job_no')];
			$sales_arr[$sales_row[csf('id')]]["buyer_id"] 	= $sales_row[csf('buyer_id')];
			$sales_job_arr[$sales_row[csf('po_job_no')]] 	= $sales_row[csf('po_job_no')];
		}
	}

	$sales_job_cond="";
	$sales_job_arr = array_filter($sales_job_arr);
	if(!empty($sales_job_arr))
	{
		foreach ($sales_job_arr as $val)
		{
			$sales_job_nos .= "'".implode("','",explode(",", $val))."',";
		}
		$sales_job_nos = chop($sales_job_nos,",");
		$sales_job_cond = " and e.job_no in ($sales_job_nos)";
	}

	$job_arr=array();
	if(!empty($po_arr) || $sales_job_cond !="")
	{
		if(!empty($po_arr)){
			$po_id_cond = " and c.id in (".implode(',', $po_arr).") ";
		}
		$sql_job=sql_select("select e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping as ref_no from wo_po_break_down c, wo_po_details_master e where  c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0  and  c.status_active in(1,2) and c.is_deleted=0 $po_no_cond $sales_job_cond group by e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping");

		foreach ($sql_job as $job_row) {
			$job_arr[$job_row[csf('id')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
			$job_arr[$job_row[csf('id')]]['po_number'] 			= $job_row[csf('po_number')];
			$job_arr[$job_row[csf('id')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
			$job_arr[$job_row[csf('id')]]["style_ref_no"] 		= $job_row[csf('style_ref_no')];
			$job_arr[$job_row[csf('id')]]["ref_no"] 			= $job_row[csf('ref_no')];
			$job_arr[$job_row[csf('id')]]["file_no"] 			= $job_row[csf('file_no')];

			$job_arr[$job_row[csf('job_no')]]["ref_no"] 		.= $job_row[csf('ref_no')].",";
			$job_arr[$job_row[csf('job_no')]]["file_no"] 		.= $job_row[csf('file_no')].",";
		}
	}

	if(!empty($non_ord_booking_id_arr))
	{
		$non_booking_sql=sql_select("select buyer_id, id, booking_no from wo_non_ord_samp_booking_mst where status_active=1 and id in (". implode(',', $non_ord_booking_id_arr).")");
		foreach ($non_booking_sql as $val)
		{
			$non_ord_booking_ref[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
			$non_ord_booking_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
		}
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");

	$yarn_lot_arr = array();
	$yarn_prod_arr = array_filter($yarn_prod_arr);
	if(!empty($yarn_prod_arr))
	{
		$yarn_prod_arr_cond=""; $yProdCond="";
	    $yarn_prod_ids = implode(",", $yarn_prod_arr);
	    if($db_type==2 && count($yarn_prod_arr)>999)
	    {
	    	$yarn_prod_arr_chunk=array_chunk($yarn_prod_arr,999) ;
	    	foreach($yarn_prod_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$yProdCond.="  id in($chunk_arr_value) or ";
	    	}

	    	$yarn_prod_arr_cond.=" and (".chop($yProdCond,'or ').")";
	    }
	    else
	    {
	    	$yarn_prod_arr_cond=" and id in($yarn_prod_ids)";
	    }
		$yarn_lot_sql=sql_select("select id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 $yarn_prod_arr_cond");
		foreach ($yarn_lot_sql as $value) {
			$yarn_lot_arr[$value[csf('id')]]['yarn_comp_type1st'] = $value[csf('yarn_comp_type1st')];
			$yarn_lot_arr[$value[csf('id')]]['percent1st'] = $value[csf('yarn_comp_percent1st')];
			$yarn_lot_arr[$value[csf('id')]]['type2nd'] = $value[csf('yarn_comp_type2nd')];
			$yarn_lot_arr[$value[csf('id')]]['percent2nd'] = $value[csf('yarn_comp_percent2nd')];
			$yarn_lot_arr[$value[csf('id')]]['brand'] = $brand_details[$value[csf('brand')]];
		}
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1700px;">
		<table width="1090" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px; ">
			<tr>
				<td rowspan="4" colspan="2" align="left" >
					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left; ">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px;"><? echo $com_dtls[0]; ?></strong>
				</td>
				<td rowspan="6" colspan="2" >
					<span id="qrcode" width="180" style="float:left; margin-top: 20px; " ></span>
				</td>
				
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><? echo $com_dtls[1]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px;"><u>Delivery Challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1290" cellspacing="0" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" colspan="6" width="1290">&nbsp;</td>
			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="100">Challan No</td>
				<td style="font-size:14px; font-weight:bold;" width="370">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="100">Location</td>
				<td style="font-size:14px;" width="370">:&nbsp;<? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="100"></td>
				<td width="" id="" align="right"></td>
			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:14px; font-weight:bold;">Remarks</td>
				<td style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
				<td style="font-size:14px; font-weight:bold;"></td>
				<td style="font-size:14px; font-weight:bold;"></td>
			</tr>

			<tr>
				<td style="font-size:14px; font-weight:bold;">Floor No</td>
				<td>:&nbsp;<? echo $floor_name ?></td>
				<td style="font-size:14px; font-weight:bold;"></td>
				<td style="font-size:14px; font-weight:bold;"></td>
				<td style="font-size:14px; font-weight:bold;"></td>
				<td style="font-size:14px; font-weight:bold;"></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1650" class="rpt_table"
		style="font-family: tahoma; font-size: 12px;">
			<thead>
				<tr bgcolor="#CCCCCC">
					<th width="30">SL</th>
					<th width="70">Production Date</th>
					<th width="40">MC. No</th>
					<th width="40">MC. Dia</th>
					<th width="40">MC. Gauge</th>
					<th width="40">Fab. Dia</th>
					<th width="120">Order/FSO No</th>
					<th width="100"><?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?>Buyer
						/<br> <?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?> Job
					</th>
					<th width="60" title="Int. Ref/Prog. No">Int. Booking/<br>Prg. No</th>
					<th width="300">Fabric Type</th>
					<th width="50">Yarn Count</th>
					<th width="50">Yarn Composition</th>
					<th width="70">Yarn Brand</th>
					<th width="60">Lot No</th>
					<th width="50">Fin GSM</th>
					<th width="50">Stich</th>
					<th width="70">Color Range</th>
					<th width="80">Program Wise Knit Card Distribution Qty, Kg</th>
					<th width="80">Knit Card Wise Balance Qty, Kg</th>
					<th width="40">Shift</th>
					<th width="80">Barcode No</th>
					<th width="40">Roll No</th>
					<th width="80">Del. Qty</th>
					<th width="80">Reject Qty</th>
					<th width="80">QC Pass QTY</th>
					<th width="80">Shift Total</th>
					<th>Shift Total Reject</th>
				</tr>
			</thead>
			<?

			$i = 0;
			$tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");


			$loc_arr = array();
			$loc_nm = ": ";
			$style_check = array();
			$program_check = array();$shift_span="";
			foreach ($dtls_sql as $row)
			{
				if ($loc_arr[$row[csf('location_id')]] == "") {
					$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
					$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
				}

				$knit_company = "&nbsp;";
				if ($row[csf("knitting_source")] == 1) {
					$knit_company = $company_arr_data[$row[csf("knitting_company")]];
				} else if ($row[csf("knitting_source")] == 3) {
					$knit_company = $supplier_arr[$row[csf("knitting_company")]];
				}

				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
				}

				if ($row[csf('receive_basis')] == 1) {
					$prog_book_no = "";
					$plan_booking_no = $row[csf('booking_no')];
					$machine_gg = $row[csf('machine_gg')];
					$machine_dia = $row[csf('machine_dia')];
				}
				else
				{
					$prog_book_no = $row[csf('booking_no')];
					$plan_booking_no = $program_ref[$row[csf('booking_no')]]["booking_no"];
					$machine_gg = $program_ref[$row[csf('booking_no')]]["machine_gg"];
					$machine_dia = $program_ref[$row[csf('booking_no')]]["machine_dia"];
				}


				$composition_string = "";$yarn_brand ="";
				$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
				if(count(array_filter($yarn_prod_id)) > 0)
				{
					foreach($yarn_prod_id as $val)
					{
						$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
						$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
						$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
						$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

						$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
						if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
						$composition_string .= ", ";
						$yarn_brand .= ($yarn_brand =="") ? $yarn_lot_arr[$val]["brand"] :  ",". $yarn_lot_arr[$val]["brand"];
					}
				}
				$composition_string = chop($composition_string,", ");
				$yarn_brand =implode(",",array_filter(array_unique(explode(",", $yarn_brand))));

				if ($row[csf("receive_basis")] == 4)
				{
					$is_salesOrder = 1;
				}
				if ($row[csf('is_sales')] == 1)
				{
					$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
					if($within_group == 1){
						$job_buyer = "B: " . $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["po_buyer"]] . "<br>J: " . $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
					}else{
						$job_buyer = "B: " . $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["buyer_id"]] . "<br>J: " ."";
					}
					$po_number = $sales_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
					$style_ref = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$ref_no=$file_no="";
					$sales_po_job_no = $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
					$sales_po_job_no_arr = explode(",", $sales_po_job_no);
					foreach ($sales_po_job_no_arr as $sales_job)
					{
						$file_no = $job_arr[$sales_job]["file_no"].",";
					 	$ref_no = $job_arr[$sales_job]["ref_no"].",";
					}

					$file_no = implode(",",array_filter(array_unique(explode(",", $file_no))));
					$ref_no = implode(",",array_filter(array_unique(explode(",", $ref_no))));
					$file_ref_no = "F:" . $file_no . "<br>R:" . $ref_no ;
					$ref_no_program = "R:" . $ref_no . "<br>F:" . $prog_book_no;
				}
				else
				{
					$po_number = $job_arr[$row[csf('po_breakdown_id')]]['po_number'];
					$job_buyer = "B: " . $buyer_array[$job_arr[$row[csf('po_breakdown_id')]]['buyer_name']] . "<br>J: " . $job_arr[$row[csf('po_breakdown_id')]]['job_no_mst'];
					$style_ref = $job_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$file_ref_no = "F:" . $job_arr[$row[csf('po_breakdown_id')]]['file_no'] . "<br>R:" . $job_arr[$row[csf('po_breakdown_id')]]['ref_no'] ;
					$ref_no_program = "R:" . $job_arr[$row[csf('po_breakdown_id')]]['ref_no'] . "<br>F:" . $prog_book_no;
				}

				if($row[csf('booking_without_order')] == 1)
				{
					$job_buyer = "B: " . $buyer_array[$non_ord_booking_ref[$row[csf("po_breakdown_id")]]["buyer_name"]] . "<br>J: " ."";
				}
				$distribut_qty=$program_and_mc_wise_distribut_qty_arr[$prog_book_no][$row[csf('machine_no_id')]];

				$shift_span = $shift_count_arr[$row[csf("shift_name")]]++;
				$shift_tot_qty = $shift_tot_qty_arr[$row[csf("shift_name")]];
				$shift_tot_rej = $shift_tot_rej_arr[$row[csf("shift_name")]];
				// echo $shift_span.'<br>';
				?>
                <tr>
					<td width="30"><? echo $i+1; ?></td>
					<td width="70" style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
					<td width="40" style="word-break:break-all;" align="center">
						<? echo $machine_details[$row[csf('machine_no_id')]]["machine_no"]; ?>
					</td>
					<td width="40" style="word-break:break-all;" align="center">
						<? echo $machine_dia; ?>
                    </td>
                    <td width="40" style="word-break:break-all;" align="center">
                    	<? echo $machine_gg; ?>
                    </td>
                    <td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
                    <td width="120" style="word-break:break-all;"><? echo $po_number; ?></td>
                    <td width="100" style="word-break:break-all;"><? echo $job_buyer; ?></td>
                    <td width="60" style="word-break:break-all;"><? echo $ref_no_program;?></td>
					<td width="300" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
					<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
					<td width="80" align="right" style="word-break:break-all;"><? echo number_format($distribut_qty, 2); ?></td>
					<td width="80" align="right" style="word-break:break-all;"><? $balance=$row[csf('current_delivery')]-$distribut_qty; echo number_format($balance, 2); ?></td>
					<?
					if(!in_array($row[csf("shift_name")],$shift_chk))
					{
						$shift_chk[]=$row[csf("shift_name")];
						?>
						<td width="40" style="word-break:break-all;" title="<? echo $shift_span; ?>" rowspan="<? echo $shift_span ;?>"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
						<?
					}
					?>

					<td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
					<td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
                    <td width="80" align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
                    <td width="80" align="right"><? echo number_format($row[csf('reject_qnty')], 2); ?></td>
                    <td width="80" align="right"><? $qc_pass_qty=$row[csf('current_delivery')]-$row[csf('reject_qnty')]; echo number_format($qc_pass_qty, 2);?></td>

                    <?
					if(!in_array($row[csf("shift_name")],$shift_chk2))
					{
						$shift_chk2[]=$row[csf("shift_name")];

						?>
						<td width="80" align="right" title="<? echo $shift_span; ?>" rowspan="<? echo $shift_span ;?>"><? echo number_format($shift_tot_qty, 2); ?></td>

                    	<td width="" align="right" title="<? echo $shift_span; ?>" rowspan="<? echo $shift_span ;?>"><? echo number_format($shift_tot_rej, 2); ?></td>
                    <?
					}
					?>
                </tr>
                <?
                $tot_qty += $row[csf('current_delivery')];
                $tot_reject_qnty += $row[csf('reject_qnty')];
                $tot_qc_pass_qty += $qc_pass_qty;

                $i++;
        	}

            $loc_arr = array();
            $loc_nm = ": ";
            $k = 1;
            $style_check = array();

            $loc_nm = rtrim($loc_nm, ', ');
            ?>
            <tr bgcolor="#CCCCCC">
            	<td align="right" colspan="21"><strong>Total</strong></td>
            	<td align="right"><? echo $i; ?></td>
            	<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
            	<td align="right"><? echo number_format($tot_reject_qnty, 2, '.', ''); ?></td>
            	<td align="right"><? echo number_format($tot_qc_pass_qty, 2, '.', ''); ?></td>
            	<td align="right"></td>
            	<td align="right"></td>
            </tr>
        </table>
        </div>
        <!-- <div style="font-family: tahoma; font-size: 11px;"><? //echo signature_table(125, $company, "1600px"); ?></div> -->
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <!-- <script type="text/javascript" src="../../js/jquerybarcode.js"></script> -->
		<script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
        <script>
        	// function generateBarcode(valuess) {
            // var value = valuess;//$("#barcodeValue").val();
            // //alert(value)
            // var btype = 'code39';//$("input[name=btype]:checked").val();
            // var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            // var settings = {
            // 	output: renderer,
            // 	bgColor: '#FFFFFF',
            // 	color: '#000000',
            // 	barWidth: 1,
            // 	barHeight: 40,
            // 	moduleSize: 5,
            // 	posX: 10,
            // 	posY: 20,
            // 	addQuietZone: 1
            // };
            // //$("#barcode_img_id").html('11');
            // value = {code: value, rect: false};

            // $("#barcode_img_id").show().barcode(value, btype, settings);
	        // }
	        // generateBarcode('<?// echo $txt_challan_no; ?>');
	        // document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
	    </script>
		<script>
			var main_value='<? echo $txt_challan_no; ?>';
			// alert(main_value);
			$('#qrcode').qrcode(main_value)	
		</script>
    <?
    exit();
}

if ($action == "grey_delivery_print9") // Print 9 for Palmal Group
{
	extract($_REQUEST);
	$data = explode('*', $data);
	//echo '<pre>';print_r($data);die;
	$is_salesOrder = 0;
	$company = $data[0];
	$location = $data[6];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$location_data = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$company_arr_data = return_library_array("select id, company_name from lib_company", "id", "company_name");

	//$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");


	$mstData = sql_select( "select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id,  d.barcode_no, d.qnty from pro_grey_prod_delivery_mst a,pro_roll_details d where a.id=$update_id and a.id = d.mst_id  and d.entry_form = 56 and d.status_active =1 and d.is_deleted =0 group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id,d.barcode_no, d.qnty");


	$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$update_id");
	foreach ($delivery_barcode_data as $row)
	{
		$barcode_nos .= $row[csf('barcode_no')] . ',';
		$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
	}
	$barcode_nos = rtrim($barcode_nos,", ");

	foreach ($mstData as  $row)
	{
		$barcode_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
		$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
	}

    $barcode_arr = array_filter($barcode_arr);
    $all_barcode_no_cond=""; $barCond="";
    $all_barcode_nos = implode(",", $barcode_arr);
    if($db_type==2 && count($barcode_arr)>999)
    {
    	$all_barcode_arr_chunk=array_chunk($barcode_arr,999) ;
    	foreach($all_barcode_arr_chunk as $chunk_arr)
    	{
    		$chunk_arr_value=implode(",",$chunk_arr);
    		$barCond.="  c.barcode_no in($chunk_arr_value) or ";
    	}

    	$all_barcode_no_cond.=" and (".chop($barCond,'or ').")";
    }
    else
    {
    	$all_barcode_no_cond=" and c.barcode_no in($all_barcode_nos)";
    }

	$dtls_sql = sql_select("SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source,a.knitting_company, a.receive_date, a.location_id,a.yarn_issue_challan_no, b.prod_id, b.febric_description_id, b.gsm, b.floor_id,b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.machine_gg, b.machine_dia, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.barcode_no, c.roll_no, c.po_breakdown_id, c.qnty as current_delivery, c.booking_no as bwo, c.booking_without_order,c.is_sales, c.qc_pass_qnty_pcs,c.coller_cuff_size
	FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	WHERE a.id=b.mst_id and b.id=c.dtls_id  $all_barcode_no_cond and a.entry_form=2 and c.entry_form=2  order by a.receive_basis, a.booking_no,a.booking_without_order,c.roll_no");

    foreach ($dtls_sql as  $row)
    {
    	if($row[csf('receive_basis')] == 2)
		{
			$program_arr[$row[csf('booking_no')]] = $row[csf('booking_no')];
		}

		if($row[csf('booking_without_order')] == 1)
		{
			$non_ord_booking_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
		}
		else
		{
			if($row[csf('is_sales')] == 1)
			{
				$sales_id_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}else{
				$po_arr[$row[csf('po_breakdown_id')]] = $row[csf('po_breakdown_id')];
			}
		}
		$yarn_prod_arr[$row[csf('yarn_prod_id')]] =  $row[csf('yarn_prod_id')];
    }

    $program_arr = array_filter($program_arr);
	if(!empty($program_arr))
	{
		$program_sql = sql_select("select b.booking_no, a.id as program_no, a.machine_dia, a.machine_gg from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b where a.mst_id = b.id and a.id in (".implode(',', $program_arr).")" );

		foreach ($program_sql as  $val)
		{
			$program_ref[$val[csf("program_no")]]["booking_no"] = $val[csf("booking_no")];
			$program_ref[$val[csf("program_no")]]["machine_dia"] = $val[csf("machine_dia")];
			$program_ref[$val[csf("program_no")]]["machine_gg"] = $val[csf("machine_gg")];
		}
	}

	if(!empty($sales_id_arr))
	{
		$sql_sales = sql_select("select id, sales_booking_no, po_buyer,po_job_no,job_no,style_ref_no, within_group, buyer_id, customer_buyer from fabric_sales_order_mst where status_active = 1 and id in( ".implode(',', $sales_id_arr) .")");
		foreach ($sql_sales as $sales_row)
		{
			$sales_arr[$sales_row[csf('id')]]["within_group"] 		= $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]["sales_booking_no"] 	= $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]["sales_order_no"] 	= $sales_row[csf('job_no')];
			$sales_arr[$sales_row[csf('id')]]["style_ref_no"] 	= $sales_row[csf('style_ref_no')];
			$sales_arr[$sales_row[csf('id')]]["po_buyer"] 	= $sales_row[csf('po_buyer')];
			$sales_arr[$sales_row[csf('id')]]["po_job_no"] 	= $sales_row[csf('po_job_no')];
			$sales_arr[$sales_row[csf('id')]]["customer_buyer"] 	= $sales_row[csf('customer_buyer')];
			$sales_arr[$sales_row[csf('id')]]["buyer_id"] 	= $sales_row[csf('buyer_id')];
			$sales_job_arr[$sales_row[csf('po_job_no')]] 	= $sales_row[csf('po_job_no')];
		}
	}

	$sales_job_cond="";
	$sales_job_arr = array_filter($sales_job_arr);
	if(!empty($sales_job_arr))
	{
		foreach ($sales_job_arr as $val)
		{
			$sales_job_nos .= "'".implode("','",explode(",", $val))."',";
		}
		$sales_job_nos = chop($sales_job_nos,",");
		$sales_job_cond = " and e.job_no in ($sales_job_nos)";
	}

	$job_arr=array();
	if(!empty($po_arr) || $sales_job_cond !="")
	{
		if(!empty($po_arr)){
			$po_id_cond = " and c.id in (".implode(',', $po_arr).") ";
		}
		$sql_job=sql_select("select e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping as ref_no from wo_po_break_down c, wo_po_details_master e where  c.job_no_mst=e.job_no and e.status_active=1 and e.is_deleted=0  and  c.status_active in(1,2) and c.is_deleted=0 $po_no_cond $sales_job_cond group by e.buyer_name, e.job_no_prefix_num,e.job_no,c.id,c.po_number,e.style_ref_no, c.file_no, c.grouping");

		foreach ($sql_job as $job_row) {
			$job_arr[$job_row[csf('id')]]["job_no_mst"] 		= $job_row[csf('job_no_prefix_num')];
			$job_arr[$job_row[csf('id')]]['po_number'] 			= $job_row[csf('po_number')];
			$job_arr[$job_row[csf('id')]]["buyer_name"] 		= $job_row[csf('buyer_name')];
			$job_arr[$job_row[csf('id')]]["style_ref_no"] 		= $job_row[csf('style_ref_no')];
			$job_arr[$job_row[csf('id')]]["ref_no"] 			= $job_row[csf('ref_no')];
			$job_arr[$job_row[csf('id')]]["file_no"] 			= $job_row[csf('file_no')];

			$job_arr[$job_row[csf('job_no')]]["ref_no"] 		.= $job_row[csf('ref_no')].",";
			$job_arr[$job_row[csf('job_no')]]["file_no"] 		.= $job_row[csf('file_no')].",";
		}
	}

	if(!empty($non_ord_booking_id_arr))
	{
		$non_booking_sql=sql_select("select buyer_id, id, booking_no from wo_non_ord_samp_booking_mst where status_active=1 and id in (". implode(',', $non_ord_booking_id_arr).")");
		foreach ($non_booking_sql as $val)
		{
			$non_ord_booking_ref[$val[csf("id")]]["booking_no"] = $val[csf("booking_no")];
			$non_ord_booking_ref[$val[csf("id")]]["buyer_name"] = $val[csf("buyer_id")];
		}
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");

	$yarn_lot_arr = array();
	$yarn_prod_arr = array_filter($yarn_prod_arr);
	if(!empty($yarn_prod_arr))
	{
		$yarn_prod_arr_cond=""; $yProdCond="";
	    $yarn_prod_ids = implode(",", $yarn_prod_arr);
	    if($db_type==2 && count($yarn_prod_arr)>999)
	    {
	    	$yarn_prod_arr_chunk=array_chunk($yarn_prod_arr,999) ;
	    	foreach($yarn_prod_arr_chunk as $chunk_arr)
	    	{
	    		$chunk_arr_value=implode(",",$chunk_arr);
	    		$yProdCond.="  id in($chunk_arr_value) or ";
	    	}

	    	$yarn_prod_arr_cond.=" and (".chop($yProdCond,'or ').")";
	    }
	    else
	    {
	    	$yarn_prod_arr_cond=" and id in($yarn_prod_ids)";
	    }
		$yarn_lot_sql=sql_select("select id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0 $yarn_prod_arr_cond");
		foreach ($yarn_lot_sql as $value) {
			$yarn_lot_arr[$value[csf('id')]]['yarn_comp_type1st'] = $value[csf('yarn_comp_type1st')];
			$yarn_lot_arr[$value[csf('id')]]['percent1st'] = $value[csf('yarn_comp_percent1st')];
			$yarn_lot_arr[$value[csf('id')]]['type2nd'] = $value[csf('yarn_comp_type2nd')];
			$yarn_lot_arr[$value[csf('id')]]['percent2nd'] = $value[csf('yarn_comp_percent2nd')];
		}
	}
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<style type="text/css">
		canvas{
			width: 170;
		}
	</style>
	<div style="width:1700px;">
		<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td rowspan="4" colspan="2">
					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px;"><? echo $com_dtls[0]; ?></strong>
					<div style="float:right;width:24px; margin-right:80px; text-align:right">
						<div style="height:50px; width:40px;" id="qrcode"></div> 
		            </div>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><? echo $com_dtls[1]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px;"><u>Delivery Challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="80">Challan No</td>
				<td style="font-size:14px; font-weight:bold;" width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="60">Location</td>
				<td width="170">:&nbsp;<? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
				<td width="810" id="barcode_img_id" align="left"></td>

			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:14px; font-weight:bold;">Remarks</td>
				<td colspan="4" style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
			</tr>

			<tr>
				<td style="font-size:14px; font-weight:bold;">Floor No</td>
				<td>:&nbsp;<? echo $floor_name ?></td>
				<?php if ($is_po && ($is_salesOrder == 1)) { ?>
					<td style="font-size:14px; font-weight:bold;">PO Company</td>
					<td>:&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
				<?php } ?>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1990" class="rpt_table"
		style="font-family: tahoma; font-size: 12px;">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="70">Production Date</th>
					<th width="90">Order/FSO No</th>
					<th width="100">Style No</th>
					<th width="100"><?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?>Customer</th>
					<th width="60">Customer Buyer</th>
					<th width="50">Production ID</th>
					<th width="85">Prog./ Book. No</th>
					<th width="80">Production Basis</th>
					<th width="120">Rcv. Challan No./ Service Booking No.</th>
					<th width="40">Shift</th>
					<th width="120">Knitting Company</th>
					<th width="50">Yarn Count</th>
					<th width="50">Yarn Composition</th>
					<th width="70">Yarn Brand</th>
					<th width="60">Lot No</th>
					<th width="70">Fab Color</th>
					<th width="70">Color Range</th>
					<th width="150">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. No</th>
					<th width="40">MC. Dia</th>
					<th width="40">MC. Gauge</th>
					<th width="50">Floor Name</th>
					<th width="80">Barcode No</th>
					<th width="40">Roll No</th>
					<th width="80">Collar Cuff Size</th>
					<th width="50">Qty in Pcs</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?

			$i = 0;
			$tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");


			$loc_arr = array();
			$loc_nm = ": ";
			$k = 1;
			$j = 1;
			$style_check = array();
			$program_check = array();
			foreach ($dtls_sql as $row)
			{
				if ($loc_arr[$row[csf('location_id')]] == "") {
					$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
					$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
				}

				$knit_company = "&nbsp;";
				if ($row[csf("knitting_source")] == 1) {
					$knit_company = $company_arr_data[$row[csf("knitting_company")]];
				} else if ($row[csf("knitting_source")] == 3) {
					$knit_company = $supplier_arr[$row[csf("knitting_company")]];
				}

				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
				}

				if ($row[csf('receive_basis')] == 1) {
					$prog_book_no = "";
					$plan_booking_no = $row[csf('booking_no')];
					$machine_gg = $row[csf('machine_gg')];
					$machine_dia = $row[csf('machine_dia')];
				}
				else
				{
					$prog_book_no = $row[csf('booking_no')];
					$plan_booking_no = $program_ref[$row[csf('booking_no')]]["booking_no"];
					$machine_gg = $program_ref[$row[csf('booking_no')]]["machine_gg"];
					$machine_dia = $program_ref[$row[csf('booking_no')]]["machine_dia"];
				}


				$composition_string = "";
				$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
				if(count(array_filter($yarn_prod_id)) > 0)
				{
					foreach($yarn_prod_id as $val)
					{
						$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
						$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
						$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
						$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

						$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
						if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
						$composition_string .= ", ";
					}
				}
				$composition_string = chop($composition_string,", ");

				if ($row[csf("receive_basis")] == 4)
				{
					$is_salesOrder = 1;
				}
				if ($row[csf('is_sales')] == 1)
				{
					$within_group 	= $sales_arr[$row[csf('po_breakdown_id')]]["within_group"];
					if($within_group == 1)
					{
						$job_buyer = $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["po_buyer"]];
						$customer_buyer_name = '';
					}
					else
					{
						$job_buyer = $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["buyer_id"]];
						$customer_buyer_name = $buyer_array[$sales_arr[$row[csf('po_breakdown_id')]]["customer_buyer"]];
					}
					$po_number = $sales_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
					$style_ref = $sales_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$ref_no=$file_no="";
					$sales_po_job_no = $sales_arr[$row[csf('po_breakdown_id')]]["po_job_no"];
					$sales_po_job_no_arr = explode(",", $sales_po_job_no);
					foreach ($sales_po_job_no_arr as $sales_job)
					{
						$file_no = $job_arr[$sales_job]["file_no"].",";
					 	$ref_no = $job_arr[$sales_job]["ref_no"].",";
					}

					$file_no = implode(",",array_filter(array_unique(explode(",", $file_no))));
					$ref_no = implode(",",array_filter(array_unique(explode(",", $ref_no))));
					$file_ref_no = "F:" . $file_no . "<br>R:" . $ref_no ;
				}
				else
				{
					$customer_buyer_name = '';
					$po_number = $job_arr[$row[csf('po_breakdown_id')]]['po_number'];
					$job_buyer = $buyer_array[$job_arr[$row[csf('po_breakdown_id')]]['buyer_name']];
					$style_ref = $job_arr[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$file_ref_no = "F:" . $job_arr[$row[csf('po_breakdown_id')]]['file_no'] . "<br>R:" . $job_arr[$row[csf('po_breakdown_id')]]['ref_no'] ;
				}

				if($row[csf('booking_without_order')] == 1)
				{
					$job_buyer = $buyer_array[$non_ord_booking_ref[$row[csf("po_breakdown_id")]]["buyer_name"]];
				}

				if (!in_array($row[csf('booking_no')], $program_check))
				{
					if ($j != 1)
					{
						?>
						<tr bgcolor="#CCCCCC">
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td align="right" colspan="2" style="font-weight:bold;">Program SubTotal:</td>
							<td align="right"><? echo number_format($program_tot_qc_pass_qnty_pcs, 2); ?></td>
							<td align="right"><? echo number_format($program_tot_delivery, 2); ?></td>
						</tr>
						<?
						$program_tot_qc_pass_qnty_pcs = 0;
						$program_tot_delivery = 0;
					}
					if (!in_array($style_ref, $style_check))
					{
						if ($k != 1) {
							?>
							<tr bgcolor="#CCCCCC">
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td align="right" colspan="2" style="font-weight:bold;">Style SubTotal:</td>
								<td align="right"><? echo number_format($style_tot_qc_pass_qnty_pcs, 2); ?></td>
								<td align="right"><? echo number_format($style_tot_delivery, 2); ?></td>
							</tr>
							<?
							$style_tot_qc_pass_qnty_pcs = 0;
							$style_tot_delivery = 0;

						}
						$style_check[] = $style_ref;
						$k++;
					}
					$program_check[] = $row[csf('booking_no')];
					$j++;
				}
				?>
				<tr>
					<td width="30"><? echo $i+1; ?></td>
					<td width="70" style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
					<td width="100" style="word-break:break-all;" title="PO ID:<? echo $row[csf('po_breakdown_id')];?>"><? echo $po_number; ?></td>
					<td width="100" style="word-break:break-all;"><? echo $style_ref; ?></td>
					<td width="100" style="word-break:break-all;"><? echo $job_buyer; ?></td>
					<td width="60" style="word-break:break-all;"><? echo $customer_buyer_name; ?></td>
					<td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
					<td width="85" style="word-break:break-all;">
						P: <? echo $prog_book_no . (($prog_book_no != "") ? " /<br />" : "") . "B: " . $plan_booking_no; ?>
					</td>
					<td width="80"  style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
					<td width="120"  style="word-break:break-all;">
						<? echo "C:".$row[csf('challan_no')]."/<br />SB:".$row[csf('service_booking_no')]; ?>
					</td>
					<td width="40" style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
					<td width="120" style="word-break:break-all;"><? echo $knit_company; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
					<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
					<td width="70" style="word-break:break-all;">
						<?
						$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
						$all_color_name = "";
						foreach ($color_id_arr as $c_id) {
							$all_color_name .= $color_arr[$c_id] . ",";
						}
						$all_color_name = chop($all_color_name, ",");
						echo $all_color_name;
						?>
					</td>
					<td width="70" style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
					<td width="150" style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('stitch_length')]; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center">
						<? echo $machine_details[$row[csf('machine_no_id')]]["machine_no"]; ?>
					</td>
					<td width="40" style="word-break:break-all;" align="center">
						<? echo $machine_dia; ?>
	                </td>
	                <td width="40" style="word-break:break-all;" align="center">
	                	<? echo $machine_gg; ?>
	                </td>
	                <td width="50" align="center"><? echo $floor_name_arr[$row[csf('floor_id')]]; ?></td>
	                <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
	                <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
	                <td width="80" align="center"><? echo $row[csf('coller_cuff_size')]; ?></td>
	                <td width="50" align="right"><? echo number_format($row[csf('qc_pass_qnty_pcs')], 2); ?></td>
	                <td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
	            </tr>
	            <?
	            $style_tot_delivery += $row[csf('current_delivery')];
	            $style_tot_qc_pass_qnty_pcs += $row[csf('qc_pass_qnty_pcs')];
	            $program_tot_delivery += $row[csf('current_delivery')];
	            $program_tot_qc_pass_qnty_pcs += $row[csf('qc_pass_qnty_pcs')];
	            $grand_program_tot_delivery += $row[csf('current_delivery')];
	            $tot_qty += $row[csf('current_delivery')];
	            $tot_qnty_in_pcs += $row[csf('qc_pass_qnty_pcs')];

	            $i++;
	        }

	        $loc_arr = array();
	        $loc_nm = ": ";
	        $k = 1;
	        $style_check = array();

	        $loc_nm = rtrim($loc_nm, ', ');
	        ?>
	        <tr bgcolor="#CCCCCC">
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td>&nbsp;</td>
	        	<td align="right" colspan="3" style="font-weight:bold;">Program SubTotal:</td>
	        	<td align="right"><? echo number_format($program_tot_qc_pass_qnty_pcs, 2); ?></td>
	        	<td align="right"><? echo number_format($program_tot_delivery, 2); ?></td>
	        </tr>
	        <tr>
	        	<td align="right" colspan="27"><strong>Total</strong></td>
	        	<td align="right"><? echo $i; ?></td>
	        	<td align="right"></td>
	        	<td align="right"><? echo number_format($tot_qnty_in_pcs, 2, '.', ''); ?></td>
	        	<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
	        </tr>

	        <tr>
	        	<td colspan="2" align="left"><b>Remarks:</b></td>
	        	<td colspan="29">&nbsp;</td>
	        </tr>
	    </table>
    </div>
    <div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(125, $company, "1600px"); ?></div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
    <script>
    	var main_value='<? echo $txt_challan_no; ?>';
		//alert(main_value);
		$('#qrcode').qrcode(main_value);

    	function generateBarcode(valuess)
    	{
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}

if ($action == "grey_delivery_print10")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$is_salesOrder = 0;
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$location_data = return_library_array("select id, location_name from lib_location", "id", "location_name");

	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");

	$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
		inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
		inner join inv_receive_master c on b.grey_sys_id = c.id
		where a.id=$update_id group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");

	$search_param = $mstData[0][csf('booking_no')];
	if ($mstData[0][csf('receive_basis')] == 2) {
		$booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
			inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
			inner join wo_booking_mst c on b.booking_no = c.booking_no
			where a.id = $search_param");

		$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
		if ($is_salesOrder == "" || $is_salesOrder == 0) {
			$is_salesOrder = 0;
		} else {
			$is_salesOrder = 1;
		}
	} else if ($mstData[0][csf('receive_basis')] == 4) {
		$is_salesOrder = 1;
		$booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
			inner join wo_booking_mst b on a.booking_id = b.id
			where a.job_no = '$search_param'");
	} else {
		$booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
	}
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
		$composition_arr2[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	}



	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1800px;">
		<table width="1390" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td rowspan="4" colspan="2">
					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px;"><? echo $com_dtls[0]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><? echo $com_dtls[1]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px;"><u>Delivery Challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1390" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="60">Location</td>
				<td width="170">:&nbsp;<? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
				<td width="810" id="barcode_img_id" align="right"></td>

			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:14px; font-weight:bold;">Remarks</td>
				<td colspan="4" style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
			</tr>

			<tr>
				<td style="font-size:14px; font-weight:bold;">Floor No</td>
				<td>:&nbsp;<? echo $floor_name ?></td>
				<?php if ($is_po && ($is_salesOrder == 1)) { ?>
					<td style="font-size:14px; font-weight:bold;">PO Company</td>
					<td>:&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
				<?php } ?>
			</tr>


		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="2100" class="rpt_table"
		style="font-family: tahoma; font-size: 12px;">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="70">Production Date</th><!--new-->
				<th width="90"><?php echo ($is_po && ($is_salesOrder == 1)) ? "Sales Order No " : "Order No" ?></th>
				<th width="100">Style No</th>
				<th width="100"><?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?>Buyer
					/<br> <?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?> Job
				</th>
				<th width="60">File No <br> Ref No</th>
				<th width="50">System ID</th>
				<th width="115">Yarn Issue<br>Challan No</th>
				<th width="85">Prog./ Book. No</th>
				<th width="80">Production Basis</th>
				<th width="120">Rcv. Challan No./ Service Booking No.</th>
				<th width="40">Shift</th><!--new-->
				<th width="70">Knitting Company</th>
				<th width="50">Yarn Count</th>
				<th width="50">Yarn Composition</th>
				<th width="70">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="70">Fab Color</th>
				<th width="70">Color Range</th>
				<th width="150">Fabric Type</th>
				<th width="50">Stich</th>
				<th width="50">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. No</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="50">Floor Name</th>
				<th width="80">Barcode No</th>
				<th width="80">Collar Cuff Details/Size</th>
				<th width="40">Qnty in Pcs</th>
				<th width="40">Roll No</th>
				<th>QC Pass Qty</th>
			</tr>
		</thead>
		<?
		$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
		from  pro_grey_prod_entry_dtls d,  inv_receive_master e
		where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
		$result_arr = sql_select($sql_dtls_knit);
		$machine_dia_guage_arr = array();
		foreach ($result_arr as $row) {
			$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
			$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
		}

		$yarn_lot_arr = array();
		$yarn_lot_sql=sql_select("select id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0");
		foreach ($yarn_lot_sql as $value) {
			$yarn_lot_arr[$value[csf('id')]]['yarn_comp_type1st'] = $value[csf('yarn_comp_type1st')];
			$yarn_lot_arr[$value[csf('id')]]['percent1st'] = $value[csf('yarn_comp_percent1st')];
			$yarn_lot_arr[$value[csf('id')]]['type2nd'] = $value[csf('yarn_comp_type2nd')];
			$yarn_lot_arr[$value[csf('id')]]['percent2nd'] = $value[csf('yarn_comp_percent2nd')];
		}

		$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$update_id");
		foreach ($delivery_barcode_data as $row) {
			$barcode_nos .= $row[csf('barcode_no')] . ',';
			$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
		}
		$barcode_nos = rtrim($barcode_nos,", ");

		$i = 0;
		$tot_qty = 0;
		$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
		if ($is_salesOrder == 1) {

			$sql =" SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source,a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.floor_id,b.width, b.yarn_count, b.yarn_lot,b.yarn_prod_id, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order, p.job_no_prefix_num, p.job_no, p.style_ref_no, p.id, p.job_no po_number,p.sales_booking_no,  '' as file_no, '' as ref_no,c.qc_pass_qnty_pcs as grey_receive_qnty_pcs,c.coller_cuff_size,a.yarn_issue_challan_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst p WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=p.id and c.barcode_no in($barcode_nos) and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.booking_without_order<>1 order by a.recv_number_prefix_num,b.febric_description_id,b.yarn_lot,b.color_id,a.booking_no";


		} else {

			$sql = " SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date,a.location_id, b.prod_id, b.febric_description_id, b.gsm,b.floor_id, b.width, b.yarn_count, b.yarn_lot,b.yarn_prod_id, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,c.barcode_no, c.roll_no, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order, q.job_no_prefix_num, q.job_no, q.style_ref_no, p.id, p.po_number, p.file_no, p.grouping as ref_no,c.qc_pass_qnty_pcs as grey_receive_qnty_pcs,c.coller_cuff_size,a.yarn_issue_challan_no
			  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down p, wo_po_details_master q
			 WHERE a.id=b.mst_id and b.id=c.dtls_id  and c.po_breakdown_id=p.id and p.job_no_mst=q.job_no and c.barcode_no in($barcode_nos) and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.booking_without_order<>1
			 order by a.recv_number_prefix_num,b.febric_description_id,b.yarn_lot,b.color_id,a.booking_no";
		}
		//echo $sql."<br>"; wo_po_details_master
		$order_data = array();
		$job_no_data = array();
		$booking_data = sql_select("select a.booking_no,a.buyer_id,a.job_no from wo_booking_mst a where a.status_active=1 and a.is_deleted=0");
		foreach ($booking_data as $row) {
			$order_data[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
			$order_data[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		}

		$job_data = sql_select("select a.job_no_prefix_num, a.job_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0");
		foreach ($job_data as $row) {
			$job_no_data[$row[csf('job_no')]] = $row[csf('job_no_prefix_num')];
		}


		$result = sql_select($sql);
		$loc_arr = array();
		$loc_nm = ": ";
		$all_data_array = array();
		foreach ($result as $row) {

			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['recv_number_prefix_num'] = $row[csf('recv_number_prefix_num')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['buyer_id'] = $row[csf('buyer_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['receive_basis'] = $row[csf('receive_basis')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['challan_no'] = $row[csf('challan_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['service_booking_no'] = $row[csf('service_booking_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['booking_without_order'] = $row[csf('booking_without_order')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['booking_id'] = $row[csf('booking_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['knitting_source'] = $row[csf('knitting_source')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['knitting_company'] = $row[csf('knitting_company')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['receive_date'] = $row[csf('receive_date')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['location_id'] = $row[csf('location_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['prod_id'] = $row[csf('prod_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['gsm'] = $row[csf('gsm')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['floor_id'] = $row[csf('floor_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['width'] = $row[csf('width')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_count'] = $row[csf('yarn_count')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_prod_id'] = $row[csf('yarn_prod_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['color_range_id'] = $row[csf('color_range_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['machine_no_id'] = $row[csf('machine_no_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['stitch_length'] = $row[csf('stitch_length')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['brand_id'] = $row[csf('brand_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['shift_name'] = $row[csf('shift_name')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['barcode_no'] = $row[csf('barcode_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['roll_no'] = $row[csf('roll_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['po_breakdown_id'] = $row[csf('po_breakdown_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['current_delivery'] = $deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"];//$row[csf('current_delivery')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['bwo'] = $row[csf('bwo')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['job_no'] = $row[csf('job_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['po_number'] = $row[csf('po_number')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['file_no'] = $row[csf('file_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['ref_no'] = $row[csf('ref_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['grey_receive_qnty_pcs'] = $row[csf('grey_receive_qnty_pcs')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_issue_challan_no'] = $row[csf('yarn_issue_challan_no')];

		}


		/*echo "<pre>";
		print_r($all_data_array);	*/
		$size_wise_qnt=array();

		foreach ($all_data_array as $booking_nos => $booking_data)
			{	$booking_tot_delivery = 0;
				foreach ($booking_data as $color_id => $color_data)
					{	$color_tot_delivery = 0;
						foreach ($color_data as $yarn_lot => $lot_data)
							{	$lot_tot_delivery = 0;
								foreach ($lot_data as $fabric_type => $fabric_data)
									{	$fabric_tot_delivery = 0;
										foreach ($fabric_data as $dtlsID => $dtlsData)
										{
											$po_number = $dtlsData['job_no'];
											$booking_no = $dtlsData['sales_booking_no'];
											$booking_dtls_data = sql_select("select a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no partial_job_no from wo_booking_mst a
												inner join wo_booking_dtls b on a.booking_no = b.booking_no
												where a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no' group by a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no");

							//echo $booking_dtls_data;
											if ($loc_arr[$dtlsData['location_id']] == "") {
												$loc_arr[$dtlsData['location_id']] = $dtlsData['location_id'];
												$loc_nm .= $location_arr[$dtlsData['location_id']] . ', ';
											}

											$knit_company = "&nbsp;";
											if ($dtlsData["knitting_source"] == 1) {
												$knit_company = $company_array[$dtlsData["knitting_company"]]['shortname'];
											} else if ($dtlsData["knitting_source"] == 3) {
												$knit_company = $supplier_arr[$dtlsData["knitting_company"]];
											}

											$count = '';
											$yarn_count = explode(",", $dtlsData['yarn_count']);
											foreach ($yarn_count as $count_id) {
												if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
											}


											$composition_string = "";
											$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
											if(count(array_filter($yarn_prod_id)) > 0)
											{
												foreach($yarn_prod_id as $val)
												{
													$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
													$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
													$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
													$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

													//$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
													//if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
													//$composition_string .= ", ";
												}
											}
											//$composition_string = chop($composition_string,", ");
											$composition_string = $composition_arr2[$fabric_type];


											if ($dtlsData['receive_basis'] == 1) {
								//$booking_no=explode("-",$row[csf('booking_no')]);
								//$prog_book_no=(int)$booking_no[3];
												$prog_book_no = "";
											} else $prog_book_no = $booking_nos;

											if ($dtlsData["receive_basis"] == 2) {
												$is_salesOrder = sql_select("select b.booking_no, a.is_sales,a.machine_gg,a.machine_dia  from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
												$plan_booking_no = $is_salesOrder[0][csf('booking_no')];
												$mc_dia = $is_salesOrder[0][csf('machine_dia')];
												$machine_gg = $is_salesOrder[0][csf('machine_gg')];
												if ($is_salesOrder[0][csf('is_sales')] == "" || $is_salesOrder[0][csf('is_sales')] == 0) {
													$is_salesOrder = 0;
												} else {
													$is_salesOrder = 1;
												}
											} else {
												$plan_booking_no = $booking_nos;
												$mc_dia = $machine_dia_guage_arr[$dtlsData['booking_id']][$dtlsData['prod_id']]['dia'];
												$machine_gg = $machine_dia_guage_arr[$dtlsData['booking_id']][$dtlsData['prod_id']]['gg'];
								//echo $machine_gg.'ddddddddd';
											}

										if ($dtlsData["receive_basis"] == 4) // SALES ORDER
										{
											$is_salesOrder = 1;
										}
										if ($is_salesOrder == 1) {
											if ($booking_dtls_data[0][csf('booking_type')] == 1 && $booking_dtls_data[0][csf('is_short')] == 2) {
												$po_job = $job_no_data[$booking_dtls_data[0][csf('partial_job_no')]];
											} else {
												$po_job = $job_no_data[$booking_dtls_data[0][csf('job_no')]];
											}

											$po_jobs = explode(",", $order_data[$dtlsData['sales_booking_no']]['job_no']);
											foreach ($po_jobs as $job) {
												$po_job .= $job_no_data[$job] . ",";
											}
											$job_buyer = "B: " . $buyer_array[$order_data[$dtlsData['sales_booking_no']]['buyer_id']] . "<BR />J: " . rtrim($po_job, ',');
											$style_ref = $dtlsData['style_ref_no'];
										} else {
											$po_number = $dtlsData['po_number'];
											$job_buyer = "B: " . $buyer_array[$dtlsData['buyer_id']] . "<br>J: " . $dtlsData['job_no_prefix_num'];
											$style_ref = $dtlsData['style_ref_no'];
										}
										?>
										<tr>
											<td width="30"><? echo $i+1; ?></td>
											<td width="70"
											style="word-break:break-all;"><? echo change_date_format($dtlsData["receive_date"]); ?></td>
											<td width="100" style="word-break:break-all;"><? echo $dtlsData['po_number']; ?></td>
											<td width="100" style="word-break:break-all;"><? echo $dtlsData['style_ref_no']; ?></td>
											<td width="100" style="word-break:break-all;"><? echo $job_buyer; ?></td>
											<td width="60"
											style="word-break:break-all;"><? echo "F:" . $dtlsData['file_no'] . "<br>R:" . $dtlsData['ref_no']; ?></td>
											<td width="50"><? echo $dtlsData['recv_number_prefix_num']; ?></td>
											<td width="115"><p><?php echo $dtlsData['yarn_issue_challan_no']; ?></p></td>
											<td width="85" style="word-break:break-all;">
												P: <? echo $prog_book_no . (($prog_book_no != "") ? " /<br />" : "") . "B: " . $plan_booking_no; ?>
											</td>
											<td width="80"  style="word-break:break-all;"><? echo $receive_basis[$dtlsData['receive_basis']]; ?></td>

											<td width="120"  style="word-break:break-all;"><? echo "C:".$dtlsData['challan_no']."/<br />SB:".$dtlsData['service_booking_no']; ?></td>
											<td width="40"
											style="word-break:break-all;"><? echo $shift_name[$dtlsData["shift_name"]]; ?></td>
											<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
											<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
											<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
											<td width="70"
											style="word-break:break-all;"><? echo $brand_details[$dtlsData["brand_id"]]; ?></td>
											<td width="60" style="word-break:break-all;"><? echo $yarn_lot; ?></td>
											<td width="70" style="word-break:break-all;">
												<?
												//echo $color_arr[$row[csf("color_id")]];
												$color_id_arr = array_unique(explode(",", $color_id));
												$all_color_name = "";
												foreach ($color_id_arr as $c_id) {
													$all_color_name .= $color_arr[$c_id] . ",";
												}
												$all_color_name = chop($all_color_name, ",");
												echo $all_color_name;
												?>
											</td>
											<td width="70"
											style="word-break:break-all;"><? echo $color_range[$dtlsData["color_range_id"]]; ?></td>
											<td width="150"
											style="word-break:break-all;"><? echo $composition_arr[$fabric_type]; ?></td>
											<td width="50" style="word-break:break-all;"
											align="center"><? echo $dtlsData['stitch_length']; ?></td>
											<td width="50" style="word-break:break-all;" align="center"><? echo $dtlsData['gsm']; ?></td>
											<td width="40" style="word-break:break-all;" align="center"><? echo $dtlsData['width']; ?></td>
											<td width="40" style="word-break:break-all;"
											align="center"><? echo $machine_details[$dtlsData['machine_no_id']]["machine_no"]; ?></td>
											<td width="40" style="word-break:break-all;"
						                        align="center"><? echo $mc_dia;//$machine_details[$row[csf('machine_no_id')]]["dia_width"];
						                        ?></td>
						                        <td width="40" style="word-break:break-all;"
						                        align="center"><? echo $machine_gg;//$machine_details[$row[csf('machine_no_id')]]["gauge"];
						                        ?></td>
						                        <td width="50" align="center"><? echo $floor_name_arr[$dtlsData['floor_id']]; ?></td>
						                        <td width="80" align="center"><? echo $dtlsData['barcode_no']; ?></td>
						                        <td width="80" align="center"><p><? echo $dtlsData['coller_cuff_size']; ?></p></td>
						                        <td width="40" align="right"><? echo $dtlsData['grey_receive_qnty_pcs']; ?></td>
						                        <td width="40" align="center"><? echo $dtlsData['roll_no']; ?></td>
						                        <td align="right"><? echo number_format($dtlsData['current_delivery'], 2); ?></td>
						                    </tr>
						                    <?

						                    if(!empty($dtlsData['coller_cuff_size']))
						                    {

						                    	$size_wise_qnt[trim($dtlsData['coller_cuff_size'])]+=$dtlsData['grey_receive_qnty_pcs'];

						                    }
						                    $booking_tot_delivery += $dtlsData['current_delivery'];
						                    $color_tot_delivery += $dtlsData['current_delivery'];
						                    $lot_tot_delivery += $dtlsData['current_delivery'];
						                    $fabric_tot_delivery += $dtlsData['current_delivery'];
						                    $grand_tot_qty += $dtlsData['current_delivery'];
						                    $i++;
						                }
						                ?>
						                <tr bgcolor="#CCCCCC">
						                	<td align="right" colspan="31" style="font-weight:bold;">Fabric SubTotal:</td>
						                	<td align="right"><? echo number_format($fabric_tot_delivery, 2); ?></td>
						                </tr>
						                <?
						            }
						            ?>
						            <tr bgcolor="#CCCCCC">
						            	<td align="right" colspan="31" style="font-weight:bold;">Lot SubTotal:</td>
						            	<td align="right"><? echo number_format($lot_tot_delivery, 2); ?></td>
						            </tr>
						            <?
						        }
						        ?>
						        <tr bgcolor="#CCCCCC">
						        	<td align="right" colspan="31" style="font-weight:bold;">Color SubTotal:</td>
						        	<td align="right"><? echo number_format($color_tot_delivery, 2); ?></td>
						        </tr>
						        <?
						    }
						    ?>
						    <tr bgcolor="#CCCCCC">
						    	<td align="right" colspan="31" style="font-weight:bold;">Booking/Order SubTotal:</td>
						    	<td align="right"><? echo number_format($booking_tot_delivery, 2); ?></td>
						    </tr>
						    <?
						    $booking_grand_total += $booking_tot_delivery;
						}
						//$febric_tot_delivery += $dtlsData['current_delivery'];
						/*$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
						from  pro_grey_prod_entry_dtls d,  inv_receive_master e
						where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
						$result_arr = sql_select($sql_dtls_knit);
						$machine_dia_guage_arr = array();
						foreach ($result_arr as $row) {
							$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
							$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
						}*/

                //$i = 1;
				//$tot_qty = 0;

						$sql_no_order = " SELECT a.recv_number_prefix_num,a.challan_no,a.service_booking_no , a.buyer_id, a.receive_basis, a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot,b.yarn_prod_id, b.color_id,b.floor_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.qc_pass_qnty_pcs as grey_receive_qnty_pcs,c.coller_cuff_size,a.yarn_issue_challan_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=e.id  and a.entry_form=2 and c.entry_form=2 and a.booking_without_order=1 and c.barcode_no in($barcode_nos) order by e.seq_no";


					//echo $sql_no_order;//die;

						$result_nonorder = sql_select($sql_no_order);
						$loc_arr = array();
						$loc_nm = ": ";
						foreach ($result_nonorder as $row) {
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['recv_number_prefix_num'] = $row[csf('recv_number_prefix_num')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['buyer_id'] = $row[csf('buyer_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['receive_basis'] = $row[csf('receive_basis')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['challan_no'] = $row[csf('challan_no')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['service_booking_no'] = $row[csf('service_booking_no')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['booking_without_order'] = $row[csf('booking_without_order')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['booking_id'] = $row[csf('booking_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['knitting_source'] = $row[csf('knitting_source')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['knitting_company'] = $row[csf('knitting_company')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['receive_date'] = $row[csf('receive_date')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['location_id'] = $row[csf('location_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['prod_id'] = $row[csf('prod_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['gsm'] = $row[csf('gsm')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['width'] = $row[csf('width')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_count'] = $row[csf('yarn_count')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['color_range_id'] = $row[csf('color_range_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['machine_no_id'] = $row[csf('machine_no_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['stitch_length'] = $row[csf('stitch_length')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['brand_id'] = $row[csf('brand_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['shift_name'] = $row[csf('shift_name')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['barcode_no'] = $row[csf('barcode_no')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['roll_no'] = $row[csf('roll_no')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['po_breakdown_id'] = $row[csf('po_breakdown_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['current_delivery'] = $deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"];//$row[csf('current_delivery')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['bwo'] = $row[csf('bwo')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['grey_receive_qnty_pcs'] = $row[csf('grey_receive_qnty_pcs')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_issue_challan_no'] = $row[csf('yarn_issue_challan_no')];

						}

				 /*echo "<pre>";
				 print_r($nonOrder_data_array);die;*/

						foreach ($nonOrder_data_array as $booking_nos => $booking_data)
							{	$booking_tot_delivery = 0;
								foreach ($booking_data as $color_id => $color_data)
									{	$color_tot_delivery = 0;
										foreach ($color_data as $yarn_lot => $lot_data)
											{	$lot_tot_delivery = 0;
												foreach ($lot_data as $fabric_type => $fabric_data)
													{	$fabric_tot_delivery = 0;
														foreach ($fabric_data as $dtlsData)
														{
															if ($loc_arr[$dtlsData['location_id']] == "") {
																$loc_arr[$dtlsData['location_id']] = $dtlsData['location_id'];
																$loc_nm .= $location_arr[$dtlsData['location_id']] . ', ';
															}

															$knit_company = "&nbsp;";
															if ($dtlsData["knitting_source"] == 1) {
																$knit_company = $company_array[$dtlsData["knitting_company"]]['shortname'];
															} else if ($dtlsData["knitting_source"] == 3) {
																$knit_company = $supplier_arr[$dtlsData["knitting_company"]];
															}

															$count = '';
															$yarn_count = explode(",", $dtlsData['yarn_count']);
															foreach ($yarn_count as $count_id) {
																if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
															}


															$composition_string = "";
															$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
															if(count(array_filter($yarn_prod_id))>0)
															{
																foreach($yarn_prod_id as $val)
																{
																	$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
																	$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
																	$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
																	$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

																	$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
																	if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
																	$composition_string .= ", ";
																}
															}
															$composition_string = chop($composition_string,", ");


															if ($dtlsData['receive_basis'] == 1) {
																$booking_no = explode("-", $booking_nos);
																$prog_book_no = (int)$booking_no[3];
															} else $prog_book_no = $booking_nos;

															if ($dtlsData['receive_basis'] == 2) {
																$planOrder = sql_select("select b.booking_no, a.is_sales,a.machine_gg,a.machine_dia from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
																$mc_dia = $planOrder[0]['machine_dia'];
																$machine_gg = $planOrder[0]['machine_gg'];
															} else {
																$mc_dia = $machine_dia_guage_arr[$dtlsData['booking_id']][$dtlsData['prod_id']]['dia'];
																$machine_gg = $machine_dia_guage_arr[$dtlsData['booking_id']][$dtlsData['prod_id']]['gg'];
															}
															?>
															<tr>
																<td width="30"><? echo $i+1; ?></td>
																<td width="70"
																style="word-break:break-all;"><? echo change_date_format($dtlsData["receive_date"]); ?></td>
																<td width="100" style="word-break:break-all;"><? echo $dtlsData['bwo']; ?></td>
																<td width="100" style="word-break:break-all;">&nbsp;</td>
																<td width="60"
																style="word-break:break-all;"><? echo $buyer_array[$dtlsData['buyer_id']]; ?></td>
																<td width="60" style="word-break:break-all;"><? echo "F:<br>R:"; ?></td>
																<td width="50"><? echo $dtlsData['recv_number_prefix_num']; ?></td>
																<td width="115"><? echo $dtlsData['yarn_issue_challan_no']; ?></td>
																<td width="85" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
																<td width="80" style="word-break:break-all;"><? echo $receive_basis[$dtlsData['receive_basis']]; ?></td>
																<td width="120"  style="word-break:break-all;"><? echo "C:".$dtlsData['challan_no']."/<br />SB:".$dtlsData['service_booking_no']; ?></td>
																<td width="40"
																style="word-break:break-all;"><? echo $shift_name[$dtlsData["shift_name"]]; ?></td>
																<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
																<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
																<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
																<td width="70"
																style="word-break:break-all;"><? echo $brand_details[$dtlsData["brand_id"]]; ?></td>
																<td width="60" style="word-break:break-all;"><? echo $yarn_lot; ?></td>
																<td width="70" style="word-break:break-all;">
																	<?
											//echo $color_arr[$row[csf("color_id")]];
																	$color_id_arr = array_unique(explode(",", $color_id));
																	$all_color_name = "";
																	foreach ($color_id_arr as $c_id) {
																		$all_color_name .= $color_arr[$c_id] . ",";
																	}
																	$all_color_name = chop($all_color_name, ",");
																	echo $all_color_name;
																	?>
																</td>
																<td width="70"
																style="word-break:break-all;"><? echo $color_range[$dtlsData["color_range_id"]]; ?></td>
																<td width="150"
																style="word-break:break-all;"><? echo $composition_arr[$fabric_type]; ?></td>
																<td width="50" style="word-break:break-all;"
																align="center"><? echo $dtlsData['stitch_length']; ?></td>
																<td width="50" style="word-break:break-all;" align="center"><? echo $dtlsData['gsm']; ?></td>
																<td width="40" style="word-break:break-all;" align="center"><? echo $dtlsData['width']; ?></td>
																<td width="40" style="word-break:break-all;"
																align="center"><? echo $machine_details[$dtlsData['machine_no_id']]["machine_no"]; ?></td>
																<td width="40" style="word-break:break-all;"
					                        align="center"><? echo $mc_dia;//$machine_details[$row[csf('machine_no_id')]]["dia_width"];
					                        ?></td>
					                        <td width="40" style="word-break:break-all;"
					                        align="center"><? echo $machine_gg;//$machine_details[$row[csf('machine_no_id')]]["gauge"];
					                        ?></td>
					                        <td width="50" align="center"><? echo $floor_name_arr[$dtlsData['floor_id']]; ?></td>
					                        <td width="80" align="center"><? echo $dtlsData['barcode_no']; ?></td>
					                        <td width="80" align="center"><p><? echo $dtlsData['coller_cuff_size']; ?></p></td>
					                        <td width="40" align="right"><? echo $dtlsData['grey_receive_qnty_pcs']; ?></td>
					                        <td width="40" align="center"><? echo $dtlsData['roll_no']; ?></td>
					                        <td align="right"><? echo number_format($dtlsData['current_delivery'], 2); ?></td>
					                    </tr>
					                    <?
					                    if(!empty($dtlsData['coller_cuff_size']))
					                    {

					                    	$size_wise_qnt[trim($dtlsData['coller_cuff_size'])]+=$dtlsData['grey_receive_qnty_pcs'];
					                    }
					                    $booking_tot_delivery += $dtlsData['current_delivery'];
					                    $color_tot_delivery += $dtlsData['current_delivery'];
					                    $lot_tot_delivery += $dtlsData['current_delivery'];
					                    $fabric_tot_delivery += $dtlsData['current_delivery'];
					                    $grand_tot_qty += $dtlsData['current_delivery'];
					                    $i++;
					                }
					                ?>
					                <tr bgcolor="#CCCCCC">
					                	<td align="right" colspan="31" style="font-weight:bold;">Fabric SubTotal:</td>
					                	<td align="right"><? echo number_format($fabric_tot_delivery, 2); ?></td>
					                </tr>
					                <?
					            }
					            ?>
					            <tr bgcolor="#CCCCCC">
					            	<td align="right" colspan="31" style="font-weight:bold;">Lot SubTotal:</td>
					            	<td align="right"><? echo number_format($lot_tot_delivery, 2); ?></td>
					            </tr>
					            <?
					        }
					        ?>
					        <tr bgcolor="#CCCCCC">
					        	<td align="right" colspan="31" style="font-weight:bold;">Color SubTotal:</td>
					        	<td align="right"><? echo number_format($color_tot_delivery, 2); ?></td>
					        </tr>
					        <?
					    }
					    ?>
					    <tr bgcolor="#CCCCCC">
					    	<td align="right" colspan="31" style="font-weight:bold;">Booking/Order SubTotal:</td>
					    	<td align="right"><? echo number_format($booking_tot_delivery, 2); ?></td>
					    </tr>
					    <?
					    $booking_grand_total += $booking_tot_delivery;
					}

					?>
					<tr>
						<td align="right" colspan="30"><strong>Grand Total</strong></td>
						<td align="right"><? echo $i; ?></td>
						<td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?></td>
					</tr> <tr>
						<td align="right" colspan="30"><strong>Booking Total</strong></td>
						<td align="right"><? echo $i; ?></td>
						<td align="right"><? echo number_format($booking_grand_total, 2, '.', ''); ?></td>
					</tr>

					<tr>
						<td colspan="2" align="left"><b>Remarks:</b></td>
						<td colspan="30">&nbsp;</td>
					</tr>
				</table>
				<br>
				<?
				if(count($size_wise_qnt))
				{
						?>
					<table cellspacing="0" cellpadding="3" border="1" rules="all" width="200" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
						<caption>Collar Cuff Size Summary </caption>
						<thead>
							<tr>
								<th>Sl</th>
								<th>Size</th>
								<th>Pcs</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$i=1;
								$total=0;

								foreach ($size_wise_qnt as $key => $value)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
										<td><?=$i ?></td>
										<td><?=$key?></td>
										<td align="right"><?=number_format($value)?></td>
									</tr>
									<?
									$i++;
									$total+=$value;
								}

							 ?>

						</tbody>
						<tfoot>
							<tr>
								<th colspan="2">Total</th>
								<th align="right"><?=number_format($total)?></th>
							</tr>
						</tfoot>
					</table>
						<?
				}
				?>
			</div>
			<div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(125, $company, "1600px"); ?></div>
			<script type="text/javascript" src="../../js/jquery.js"></script>
			<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
			<script>
				function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}

if ($action == "grey_delivery_print4")
{

	extract($_REQUEST);
	$data = explode('*', $data);
	$is_salesOrder = 0;
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];

	$delivery_data_arr = array();
	$barcode_nos = '';
	$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$update_id");
	foreach ($delivery_barcode_data as $row) {
		$delivery_data_arr[$row[csf('barcode_no')]]['dtls_id'] = $row[csf('dtls_id')];
		$delivery_data_arr[$row[csf('barcode_no')]]['qnty'] = number_format($row[csf("qnty")], 2, '.', '');

		$barcode_nos .= "'".$row[csf('barcode_no')] . "',";
		if ($row[csf("booking_without_order")] != 1) {
			$po_ids_arr[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
		}
	}

	$barcode_nos = chop($barcode_nos,",");

	$knitting_company_arr=return_library_array("select id, knitting_company from pro_grey_prod_delivery_mst",'id','knitting_company');
	$booking_without_order="";

	$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_id,c.booking_no, c.receive_basis,c.booking_without_order from pro_grey_prod_delivery_mst a
		inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
		inner join inv_receive_master c on b.grey_sys_id = c.id
		where a.id=$update_id group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no,c.booking_id, c.receive_basis,c.booking_without_order");

	foreach ($mstData as $data_row) {
		$bookingIdArr[$data_row[csf('booking_id')]] = $data_row[csf('booking_id')];
	}

	$search_param = $mstData[0][csf('booking_no')];
    $booking_without_order = $mstData[0][csf('booking_without_order')];//die;

    if ($mstData[0][csf('receive_basis')] == 2) {
    	/*$booking_data = sql_select("select a.id as booking_id , b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
    		inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
    		inner join wo_booking_mst c on b.booking_no = c.booking_no
    		where a.id = $search_param");*/

    	$booking_data = sql_select("SELECT a.id as booking_id , b.booking_no, c.company_id
		from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b, wo_booking_mst c
		where a.mst_id = b.id and b.booking_no = c.booking_no and a.id =$search_param
		union all
		select a.id as booking_id , b.booking_no, c.company_id
		from ppl_planning_info_entry_dtls a, ppl_planning_info_entry_mst b, wo_non_ord_samp_booking_mst c
		where a.mst_id = b.id and b.booking_no = c.booking_no and a.id =$search_param");

    	$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
    	if ($is_salesOrder == "" || $is_salesOrder == 0 || $is_salesOrder == 2) {
    		$is_salesOrder = 0;
    	} else {
    		$is_salesOrder = 1;
    	}
    } else if ($mstData[0][csf('receive_basis')] == 4) {
    	$is_salesOrder = 1;
    	$booking_data = sql_select("select a.booking_id,a.sales_booking_no, b.company_id from fabric_sales_order_mst a
    		inner join wo_booking_mst b on a.booking_id = b.id
    		where a.job_no = '$search_param'");
    } else {
    	$booking_data = sql_select("select a.id as booking_id,a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
    }

    $composition_arr = array();
    $sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
    $data_array = sql_select($sql_deter);
    foreach ($data_array as $row) {
    	if (array_key_exists($row[csf('id')], $composition_arr)) {
    		$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
    	} else {
    		$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
    	}
    }

    $image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");

	if ($is_salesOrder == 1) {
		$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source,a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.floor_id,b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order, d.mst_id,p.job_no_prefix_num, p.job_no, p.style_ref_no, p.id, p.job_no po_number,p.sales_booking_no, '' as file_no, '' as ref_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, fabric_sales_order_mst p WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.order_id=p.id and d.entry_form=56 and d.mst_id=$update_id and d.status_active=1 and d.is_deleted=0 and a.booking_without_order<>1 and a.booking_id in('".implode("','",$bookingIdArr)."') order by a.booking_no, style_ref_no";

		$job_data = sql_select("select a.job_no_prefix_num, a.job_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0");
		foreach ($job_data as $row) {
			$job_no_data[$row[csf('job_no')]] = $row[csf('job_no_prefix_num')];
		}

	} else {
		$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm,b.floor_id, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order,c.is_sales, q.job_no_prefix_num, q.job_no, q.style_ref_no, p.id, p.po_number, p.file_no, p.grouping as ref_no
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, wo_po_break_down p, wo_po_details_master q
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.order_id=p.id and p.job_no_mst=q.job_no and d.entry_form=56 and d.mst_id=$update_id and d.status_active=1 and a.booking_without_order<>1 and d.is_deleted=0  and a.booking_id in('".implode("','",$bookingIdArr)."')

		union all

		SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm,b.floor_id, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order,c.is_sales, null as job_no_prefix_num , null as job_no, null as style_ref_no, null as id, null as po_number, null as file_no, null as ref_no
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d
		WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and d.status_active=1 and d.is_deleted=0 and a.booking_without_order=1";
	}


    $result = sql_select($sql);

    foreach ($result as $ommittData) {
    	$colorArr[$ommittData[csf('color_id')]] = $ommittData[csf('color_id')];
    	$locationArr[$ommittData[csf('location_id')]] = $ommittData[csf('location_id')];
    	$machineArr[$ommittData[csf('machine_no_id')]] = $ommittData[csf('machine_no_id')];
    	$yearnArr[$ommittData[csf('yarn_count')]] = $ommittData[csf('yarn_count')];
    	$brandArr[$ommittData[csf('brand_id')]] = $ommittData[csf('brand_id')];
    	$companyArr[$ommittData[csf('knitting_company')]] = $ommittData[csf('knitting_company')];
    	$buyerArr[$ommittData[csf('buyer_id')]] = $ommittData[csf('buyer_id')];
    }

    $company_array = array();
    $company_data = sql_select("select id, company_name, company_short_name from lib_company");
    foreach ($company_data as $row) {
    	$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
    	$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
    }

	if($colorArr!="")
	{
   		 $color_arr = return_library_array("select id, color_name from lib_color where id in(".implode(',', $colorArr).")", 'id', 'color_name');
	}

	if($companyArr!="")
	{
    	$supplier_arr = return_library_array("select id, short_name from lib_supplier where id in(".implode(',', $companyArr).")", "id", "short_name");
		$supplier_arr_outbound = return_library_array("select id, supplier_name from lib_supplier where id in(".implode(',', $companyArr).")", "id", "supplier_name");
	}

	if($buyerArr!="")
	{
    	$buyer_array = return_library_array("select id, short_name from lib_buyer where id in(".implode(',', $buyerArr).")", "id", "short_name");
	}

	if($yearnArr!="")
	{
    	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count where id in(".implode(',', $yearnArr).")", "id", "yarn_count");
	}

	if($locationArr!="")
	{
    	$location_data = return_library_array("select id, location_name from lib_location where id in(".implode(',', $locationArr).")", "id", "location_name");
	}

    $machine_details = array();
    $machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
    foreach ($machine_sql as $row) {
    	$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
    	$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
    	$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
    }

    $brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
    $location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");

    ?>
    <div style="width:1700px;">
    	<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
    		<tr>
    			<td rowspan="3" colspan="2">
    				<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
    			</td>
    			<td align="center" style="font-size:x-large">
    				<strong style="margin-right:300px;"><? echo $company_array[$company]['name']; ?></strong>
    			</td>
    		</tr>
    		<tr>
    			<td align="center" style="font-size:16px">
    				<strong style="margin-right:300px;"><u>Delivery Challan</u></strong>
    			</td>
    		</tr>
    		<tr>
    			<td align="center" style="font-size:14px">
    				<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
    			</td>
    		</tr>
    	</table>
    	<br>
    	<table width="1290" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
    		<tr>
    			<td style="font-size:18px; font-weight:bold;" width="80">Challan No</td>
    			<td style="font-size:18px">:&nbsp; <strong><? echo $txt_challan_no; ?></strong></td>
    			<td style="font-size:14px; font-weight:bold;" width="60">Location</td>
    			<td width="170"><? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
    			<td width="810" id="barcode_img_id" align="right"></td>

    		</tr>
    		<tr>
    			<td style="font-size:14px; font-weight:bold;">Delivery Date</td>
    			<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>

    			<td style="font-size:14px; font-weight:bold;" width="80">Knitting Com</td>
    			<? if($kniting_source==1)
    			{
    				$knitting_company_name=$company_array[$mstData[0][csf('knitting_company')]]['name'];
    			}
    			else
    			{
    				$knitting_company_name=$supplier_arr_outbound[$mstData[0][csf('knitting_company')]]  ;
    			}
    			?>
    			<td width="170">:&nbsp; <strong><? echo $knitting_company_name; ?></strong></td>
    		</tr>

    		<tr>
    			<td style="font-size:14px; font-weight:bold;">Floor No</td>
    			<td>:&nbsp;<? echo $floor_name ?></td>
    			<td style="font-size:14px; font-weight:bold;">Remarks</td>
    			<td style="font-size: 14px">:&nbsp;<? echo $mstData[0][csf('remarks')];?></td>
    			<?php if ($is_po && ($is_salesOrder == 1)) { ?>
    				<td style="font-size:14px; font-weight:bold;">PO Company</td>
    				<td>:&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
    			<?php } ?>
    		</tr>


    	</table>
    	<br>
    	<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1780" class="rpt_table"
    	style="font-family: tahoma; font-size: 14px;">
    	<thead>
    		<tr>
    			<th width="30">SL</th>
    			<th width="50"><?php echo ($is_po && ($is_salesOrder == 1)) ? "Sales Order No " : "Order No" ?></th>
    			<th width="20">Style No</th>
    			<th width="70"><?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?>Buyer
    				/<br> <?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?> Job
    			</th>
    			<th width="30">System ID</th>
    			<th width="85">Prog./ Book. No</th>
    			<th width="80">Rcv. Challan No./ Service Booking No.</th>
    			<th width="30">Shift</th><!--new-->
    			<th width="50">Yarn Count</th>
    			<th width="70">Yarn Brand</th>
    			<th width="50">Lot No</th>
    			<th width="50">Fab Color</th>
    			<th width="70">Color Range</th>
    			<th width="120">Fabric Type</th>
    			<th width="30">Stich</th>
    			<th width="40">Fin GSM</th>
    			<th width="40">Fab. Dia</th>
    			<th width="40">MC. No</th>
    			<th width="40">MC. Dia</th>
    			<th width="40">MC. Gauge</th>
    			<th width="80">Barcode No</th>
    			<th width="30">Roll No</th>
    			<th width="50">QC Pass Qty</th>
    		</tr>
    	</thead>
    	<?
    	$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_dia,d.machine_gg
    	from  pro_grey_prod_entry_dtls d,  inv_receive_master e
    	where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22) and e.booking_id in('".implode("','",$bookingIdArr)."')";
    	$result_arr = sql_select($sql_dtls_knit);
    	$machine_dia_guage_arr = array();
    	foreach ($result_arr as $row) {
    		$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
    		$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
    	}

    	$i = 1;
    	$tot_qty = 0;
    	$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");


    	$order_data = array();
    	$job_no_data = array();
    	$booking_data = sql_select("select a.booking_no,a.buyer_id,a.job_no from wo_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.id in('".implode("','",$bookingIdArr)."')");
    	foreach ($booking_data as $row) {
    		$order_data[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
    		$order_data[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
    	}
	$booking_dtls_data = sql_select("select a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no partial_job_no from wo_booking_mst a inner join wo_booking_dtls b on a.booking_no = b.booking_no where a.status_active=1 and a.is_deleted=0 and a.id in('".implode("','",$bookingIdArr)."') group by a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no");

		$loc_arr = array();
    	$loc_nm = ": ";
    	$k = 1;
    	$j = 1;
    	$style_check = array();
    	$program_check = array();
    	foreach ($result as $row) {

    		$po_number = $row[csf('job_no')];
    		$booking_no = $row[csf('sales_booking_no')];
    		if ($loc_arr[$row[csf('location_id')]] == "") {
    			$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
    			$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
    		}

    		$knit_company = "&nbsp;";
    		if ($row[csf("knitting_source")] == 1) {
    			$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
    		} else if ($row[csf("knitting_source")] == 3) {
    			$knit_company = $supplier_arr[$row[csf("knitting_company")]];
    		}

    		$count = '';
    		$yarn_count = explode(",", $row[csf('yarn_count')]);
    		foreach ($yarn_count as $count_id) {
    			if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
    		}

    		if ($row[csf('receive_basis')] == 1) {
				$prog_book_no = "";
    		}
			else
			{
				$prog_book_no = $row[csf('booking_no')];
			}

    		if ($row[csf("receive_basis")] == 2) {
    			$is_salesOrder = sql_select("select b.booking_no, a.is_sales,a.machine_gg,a.machine_dia  from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
    			$plan_booking_no = $is_salesOrder[0][csf('booking_no')];
    			$mc_dia = $is_salesOrder[0][csf('machine_dia')];
    			$machine_gg = $is_salesOrder[0][csf('machine_gg')];
    			if ($is_salesOrder[0][csf('is_sales')] == "" || $is_salesOrder[0][csf('is_sales')] == 0 || $is_salesOrder[0][csf('is_sales')] == 2) {
    				$is_salesOrder = 0;
    			} else {
    				$is_salesOrder = 1;
    			}
    		} else {
    			$plan_booking_no = $row[csf('booking_no')];
    			$mc_dia = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'];
    			$machine_gg = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'];
    		}
			if ($row[csf("receive_basis")] == 4) // SALES ORDER
			{
				$is_salesOrder = 1;
			}
			if ($is_salesOrder == 1) {
				if ($booking_dtls_data[0][csf('booking_type')] == 1 && $booking_dtls_data[0][csf('is_short')] == 2) {
					$po_job = $job_no_data[$booking_dtls_data[0][csf('partial_job_no')]];
				} else {
					$po_job = $job_no_data[$booking_dtls_data[0][csf('job_no')]];
				}

				$po_jobs = explode(",", $order_data[$row[csf('sales_booking_no')]]['job_no']);
				foreach ($po_jobs as $job) {
					$po_job .= $job_no_data[$job] . ",";
				}
				$job_buyer = "B: " . $buyer_array[$order_data[$row[csf('sales_booking_no')]]['buyer_id']] . "<BR />J: " . rtrim($po_job, ',');
				$style_ref = $row[csf('style_ref_no')];
			} else {
				$po_number = $row[csf('po_number')];
				$job_buyer = "B: " . $buyer_array[$row[csf('buyer_id')]] . "<br>J: " . $row[csf('job_no_prefix_num')];
				$style_ref = $row[csf('style_ref_no')];
			}
			?>
			<tr>
				<td width="30"><? echo $i; ?></td>
					<td width="50" style="word-break:break-all; font-size: 14"><? echo $po_number; ?></td>
					<td width="20" style="word-break:break-all;"><? echo $style_ref; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $job_buyer; ?></td>
					<td width="30"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
					<td width="85" style="word-break:break-all;">
					<?
					/*if($prog_book_no !="")
					{
						$plan_booking_no="";
					}
					else
					{
						$prog_book_no="";
					}*/
					?>
					P: <? echo $prog_book_no . (($prog_book_no != "") ? " /<br />" : "")  ."B:" . $plan_booking_no; ?></td>

					<td width="80"  style="word-break:break-all;"><? echo "C:".$row[csf('challan_no')]."/<br />SB:".$row[csf('service_booking_no')]; ?></td>
					<td width="30"
					style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
					<td width="50" style="word-break:break-all;">
						<?
						$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
						$all_color_name = "";
						foreach ($color_id_arr as $c_id) {
							$all_color_name .= $color_arr[$c_id] . ",";
						}
						$all_color_name = chop($all_color_name, ",");
						echo $all_color_name;
						?>
					</td>
					<td width="70"
					style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
					<td width="120"
					style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td width="30" style="word-break:break-all;"
					align="center"><? echo $row[csf('stitch_length')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
					<td width="40" style="word-break:break-all;"
					align="center"><? echo $machine_details[$row[csf('machine_no_id')]]["machine_no"]; ?></td>
					<td width="40" style="word-break:break-all;"
					align="center"><? echo $mc_dia;
					?></td>
					<td width="40" style="word-break:break-all;"
					align="center"><? echo $machine_gg;
					?></td>

					<td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
					<td width="30" align="center"><? echo $row[csf('roll_no')]; ?></td>
					<td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
				</tr>
				<?
				$style_tot_delivery += $row[csf('current_delivery')];
				$program_tot_delivery += $row[csf('current_delivery')];
				$grand_program_tot_delivery += $row[csf('current_delivery')];
				$tot_qty += $row[csf('current_delivery')];
				$i++;
           	 }

                $loc_nm = rtrim($loc_nm, ', ');
                ?>
                <tr>
                	<td align="right" colspan="22"><strong>Total</strong></td>
                	<td align="right"><strong><? echo number_format($tot_qty, 2, '.', ''); ?></strong></td>
                </tr>
            </table>
        </div>
        <div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(125, $company, "1600px"); ?></div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        	function generateBarcode(valuess) {
            var value = valuess;
            //alert(value)
            var btype = 'code39';
            var renderer = 'bmp';

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };

            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}

if ($action == "grey_delivery_print3") //New Button
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$is_salesOrder = 0;
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$location_data = return_library_array("select id, location_name from lib_location", "id", "location_name");

	//$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");
	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");

	//$mstData=sql_select("select company_id,location_id, delevery_date, knitting_source, knitting_company, remarks from pro_grey_prod_delivery_mst where id=$update_id");

	$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
		inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
		inner join inv_receive_master c on b.grey_sys_id = c.id
		where a.id=$update_id group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis");


	$search_param = $mstData[0][csf('booking_no')];
	if ($mstData[0][csf('receive_basis')] == 2) {
		$booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
			inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
			inner join wo_booking_mst c on b.booking_no = c.booking_no
			where a.id = $search_param");

		$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
		if ($is_salesOrder == "" || $is_salesOrder == 0) {
			$is_salesOrder = 0;
		} else {
			$is_salesOrder = 1;
		}
	} else if ($mstData[0][csf('receive_basis')] == 4) {
		$is_salesOrder = 1;
		$booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
			inner join wo_booking_mst b on a.booking_id = b.id
			where a.job_no = '$search_param'");
	} else {
		$booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
	}
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	?>
	<div style="width:1600px;">
		<table width="1600" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td rowspan="3" colspan="2">
					<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px;"><? echo $company_array[$company]['name']; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px;"><u>Roll Wise Grey Fabric Delivery challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1600" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="60">Location</td>
				<td width="170">:&nbsp;<? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
				<td width="810" id="barcode_img_id" align="right"></td>

			</tr>
			<tr>
				<td width="170" style="font-size:14px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:14px; font-weight:bold;">Remarks</td>
				<td colspan="4" style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
			</tr>


		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1600" class="rpt_table"
		style="font-family: tahoma; font-size: 12px;">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="70">Production Date</th><!--new-->
				<th width="50">System ID</th>
				<th width="100"><?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?>Buyer
					/<br> <?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?> Style
				</th>
				<th width="110"><?php echo ($is_po && ($is_salesOrder == 1)) ? "Sales Order No " : "Order No" ?></th>

				<th width="110">Prog./ Book. No</th>
				<th width="80">Production Basis</th>
				<th width="40">Shift</th>
				<th width="70">Knitting Company</th>
				<th width="50">Yarn Count</th>
				<th width="70">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="70">Fab Color</th>
				<th width="70">Color Range</th>
				<th width="150">Fabrication</th>
				<th width="50">Stich Length</th>
				<th width="50">Spandex S.L</th>
				<th width="50">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. No</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="80">Barcode No</th>
				<th width="40">Roll No</th>
				<th>QC Pass Qty</th>
			</tr>
		</thead>
		<?
		$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
		from  pro_grey_prod_entry_dtls d,  inv_receive_master e
		where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
		$result_arr = sql_select($sql_dtls_knit);
		$machine_dia_guage_arr = array();
		foreach ($result_arr as $row) {
			$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
			$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
		}
			//and a.booking_without_order<>1
		$i = 0;
		$tot_qty = 0;
		$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan", 4 => "Sales Order");

	/*	$sql = "SELECT a.recv_number_prefix_num as recv_no, a.buyer_id, a.receive_basis as recv_basis, a.booking_without_order as without_ordr, a.booking_no,a.booking_id, a.knitting_source,
		a.knitting_company as knit_com, a.receive_date, a.location_id, b.prod_id, b.febric_description_id as fab_desc_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id as machine_no, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id as po_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order, d.mst_id,p.job_no_prefix_num as job, p.job_no, p.style_ref_no, p.id, p.sales_booking_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, fabric_sales_order_mst p WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.order_id=p.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0  order by b.machine_no_id,c.barcode_no";*/

		$sql = "SELECT a.knitting_company as knit_com,a.booking_no,b.machine_no_id as machine_no,d.barcode_num as barcode_no,d.current_delivery , a.recv_number_prefix_num as recv_no, a.buyer_id, a.receive_basis as recv_basis, a.booking_without_order as without_ordr, a.booking_id,
		a.knitting_source, a.receive_date, a.location_id, b.prod_id, b.febric_description_id as fab_desc_id, b.gsm, b.width, b.yarn_count,b.yarn_lot, b.color_id, b.color_range_id,  b.stitch_length, b.brand_id, b.shift_name, c.roll_no, c.po_breakdown_id as po_id, c.booking_no as bwo, c.booking_without_order, d.mst_id,p.job_no_prefix_num as job, p.job_no, p.style_ref_no, p.id, p.sales_booking_no
		FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d,fabric_sales_order_mst p WHERE a.id=b.mst_id and b.id=c.dtls_id and d.entry_form=56 and c.id=d.roll_id and d.order_id=p.id and d.mst_id=$update_id and b.id=d.sys_dtls_id and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number_prefix_num , a.buyer_id, a.receive_basis, a.booking_without_order , a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id,b.shift_name, d.barcode_num, c.roll_no, c.po_breakdown_id, c.booking_no , c.booking_without_order, d.mst_id ,d.current_delivery,p.job_no_prefix_num, p.job_no, p.style_ref_no, p.id, p.sales_booking_no
		order by a.knitting_company,a.booking_no,b.machine_no_id,d.barcode_num";

			//echo $sql;
		$order_data = array();
		$job_no_data = array();
		$booking_data = sql_select("select a.booking_no,a.buyer_id,a.job_no from wo_booking_mst a where a.status_active=1 and a.is_deleted=0
			union all
				 select a.booking_no,a.buyer_id,a.job_no
				 FROM wo_non_ord_samp_booking_mst a
				 WHERE  a.status_active=1 and a.is_deleted=0");
		foreach ($booking_data as $row) {
			$order_data[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
			$order_data[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		}

			/*$job_data = sql_select("select a.job_no_prefix_num, a.job_no,a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0");
			foreach ($job_data as $row) {
				$job_no_data[$row[csf('job_no')]] = $row[csf('job_no_prefix_num')];
				$style_no_data[$row[csf('job_no')]] = $row[csf('style_ref_no')];
			}
	*/

			$result = sql_select($sql);
			foreach ($result as $row) {
				$tot_roll=count($row[csf('roll_no')]);
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['rec_no'] = $row[csf('recv_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['receive_date'] = $row[csf('receive_date')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['buyer_id'] = $row[csf('buyer_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['recv_basis'] = $row[csf('recv_basis')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['without_ordr'] = $row[csf('without_ordr')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['booking_id'] = $row[csf('booking_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['knitting_source'] = $row[csf('knitting_source')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['stitch_length'] = $row[csf('stitch_length')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['brand_id'] = $row[csf('brand_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['color_id'] = $row[csf('color_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['yarn_lot'] = $row[csf('yarn_lot')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['color_range_id'] = $row[csf('color_range_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['yarn_count'] = $row[csf('yarn_count')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['fab_desc_id'] = $row[csf('fab_desc_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['shift_name'] = $row[csf('shift_name')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['location_id'] = $row[csf('location_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['roll_no'] = $row[csf('roll_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['tot_roll_no']=$tot_roll;
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['po_id'] = $row[csf('po_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['current_delivery'] = $row[csf('current_delivery')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['bwo'] = $row[csf('bwo')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['job'] = $row[csf('job')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['prod_id'] = $row[csf('prod_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['current_delivery'] = $row[csf('current_delivery')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['gsm'] = $row[csf('gsm')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['width'] = $row[csf('width')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['job_no'] = $row[csf('job_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['po_number'] = $row[csf('po_number')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['booking_id'] = $row[csf('booking_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['knitting_source'] = $row[csf('knitting_source')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['sales_booking_no'] = $row[csf('sales_booking_no')];

			}
			/*$sql_no_order="SELECT a.recv_number_prefix_num as recv_no, a.buyer_id, a.receive_basis as recv_basis, a.booking_without_order as without_ordr, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company as knit_com, a.receive_date, a.location_id, b.prod_id, b.febric_description_id as fab_desc_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id as machine_no, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id as po_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 and a.booking_without_order=1 order by e.seq_no";
			$result_sql_no_order=sql_select($sql_no_order);
			foreach($result_sql_no_order as $row)
			{
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['rec_no']=$row[csf('recv_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['receive_date']=$row[csf('receive_date')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['buyer_id']=$row[csf('buyer_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['recv_basis']=$row[csf('recv_basis')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['without_ordr']=$row[csf('without_ordr')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['booking_id']=$row[csf('booking_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['knitting_source']=$row[csf('knitting_source')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['stitch_length']=$row[csf('stitch_length')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['brand_id']=$row[csf('brand_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['color_id']=$row[csf('color_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['yarn_lot']=$row[csf('yarn_lot')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['color_range_id']=$row[csf('color_range_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['yarn_count']=$row[csf('yarn_count')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['fab_desc_id']=$row[csf('fab_desc_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['shift_name']=$row[csf('shift_name')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['location_id']=$row[csf('location_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['roll_no']=$row[csf('roll_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['po_id']=$row[csf('po_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['current_delivery']=$row[csf('current_delivery')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['bwo']=$row[csf('bwo')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['job']=$row[csf('job')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['prod_id']=$row[csf('prod_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['current_delivery']=$row[csf('current_delivery')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['gsm']=$row[csf('gsm')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['width']=$row[csf('width')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['job_no']=$row[csf('job_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['style_ref_no']=$row[csf('style_ref_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['po_number']=$row[csf('po_number')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['booking_id']=$row[csf('booking_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['knitting_source']=$row[csf('knitting_source')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['sales_booking_no']=$row[csf('sales_booking_no')];
			}*/


			$loc_arr = array();
			$loc_nm = ": ";
			$i = 1;
			$j = 1;
			$grand_total_delivery = 0;$sub_tot_roll_coun=$grand_tot_roll_count=0;
			$tot_qty = 0;
			foreach ($roll_wise_arr as $knit_com => $knit_data) {
				foreach ($knit_data as $bookin_prog => $bookin_prog_data) {
					foreach ($bookin_prog_data as $machine_no => $machine_data) {
						foreach ($machine_data as $barcode_no => $row) {
							$po_number = $row[('job_no')];
							$booking_no = $row[('sales_booking_no')];
							/*$booking_dtls_data = sql_select("select a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no partial_job_no from wo_booking_mst a

					inner join wo_booking_dtls b on a.booking_no = b.booking_no
					where a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no' group by a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no");*/

							//echo $booking_dtls_data;  fabric_sales_order_mst
					$within_group = return_field_value("within_group", "fabric_sales_order_mst", "sales_booking_no='$booking_no'", "within_group");
					$buyer_id = return_field_value("buyer_id", "fabric_sales_order_mst", "sales_booking_no='$booking_no'", "buyer_id");
							//echo $within_group;
					if ($loc_arr[$row[('location_id')]] == "") {
						$loc_arr[$row[('location_id')]] = $row[('location_id')];
						$loc_nm .= $location_arr[$row[('location_id')]] . ', ';
					}

					$knit_company = "&nbsp;";
					if ($row[("knitting_source")] == 1) {
						$knit_company = $company_array[$knit_com]['shortname'];
					} else if ($row[("knitting_source")] == 3) {
						$knit_company = $supplier_arr[$knit_com];
					}

					$count = '';
					$yarn_count = explode(",", $row[('yarn_count')]);
					foreach ($yarn_count as $count_id) {
						if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
					}

					if ($row[('recv_basis')] == 1) {
								//$booking_no=explode("-",$row[csf('booking_no')]);
								//$prog_book_no=(int)$booking_no[3];
						$prog_book_no = "";
					} else $prog_book_no = $bookin_prog;

					if ($row[("recv_basis")] == 2) {
						$is_salesOrder = sql_select("select a.id as prog_no,b.booking_no,a.spandex_stitch_length, a.is_sales,a.machine_gg,a.machine_dia  from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
						$plan_booking_no = $is_salesOrder[0][csf('booking_no')];
						$prog_no = $is_salesOrder[0][csf('prog_no')];
						$mc_dia = $is_salesOrder[0][csf('machine_dia')];
						$machine_gg = $is_salesOrder[0][csf('machine_gg')];
						$spandex_stitch_length = $is_salesOrder[0][csf('spandex_stitch_length')];
								//echo $prog_no.'ddd';
					} else {
						$is_salesOrder = sql_select("select a.id as prog_no,b.booking_no,a.spandex_stitch_length, a.is_sales,a.machine_gg,a.machine_dia  from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where b.booking_no = $booking_no");
						$prog_no = $is_salesOrder[0][csf('prog_no')];

						$mc_dia = $machine_dia_guage_arr[$row[('booking_id')]][$row[('prod_id')]]['dia'];
						$machine_gg = $machine_dia_guage_arr[$row[('booking_id')]][$row[('prod_id')]]['gg'];
						$spandex_stitch_length = $is_salesOrder[0][csf('spandex_stitch_length')];
								//echo $prog_no.'ddddddddd';
					}
							if ($row[("recv_basis")] == 4) // SALES ORDER
							{
								$is_salesOrder = 1;
							}

							/*if($booking_dtls_data[0][csf('booking_type')] == 1 && $booking_dtls_data[0][csf('is_short')] == 2){
						$po_job = $job_no_data[$booking_dtls_data[0][csf('partial_job_no')]];
					}else{
						$po_job = $job_no_data[$booking_dtls_data[0][csf('job_no')]];
					}

					$po_jobs = explode(",",$order_data[$row[('sales_booking_no')]]['job_no']);
					foreach ($po_jobs as $job) {
						$po_job .= $job_no_data[$job] . ",";
					}*/
					if ($within_group == 1) {
						$job_buyer = "B: " . $buyer_array[$order_data[$row[('sales_booking_no')]]['buyer_id']] . "<BR />S: " . $row[('style_ref_no')];
					} else {
						$job_buyer = "B: " . $buyer_array[$buyer_id] . "<BR />S: " . $row[('style_ref_no')];


					}
					$style_ref = $row[('style_ref_no')];
					$tot_roll_no = $row[('tot_roll_no')];
					?>
					<tr>
						<td width="30"><? echo $i; ?></td>
						<td width="70"
						style="word-break:break-all;"><? echo change_date_format($row[("receive_date")]); ?></td>
						<td width="50"><? echo $row[('rec_no')]; ?></td>
						<td width="100" style="word-break:break-all;"><? echo $job_buyer; ?></td>
						<td width="110" style="word-break:break-all;"><? echo $po_number; ?></td>
						<td width="110" style="word-break:break-all;">
							P: <? echo $prog_no . (($prog_no != "") ? " /<br />" : "") . "B: " . $booking_no; ?></td>
							<td width="80"
							style="word-break:break-all;"><? echo $receive_basis[$row[('recv_basis')]]; ?></td>
							<td width="40"
							style="word-break:break-all;"><? echo $shift_name[$row[("shift_name")]]; ?></td>
							<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
							<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
							<td width="70"
							style="word-break:break-all;"><? echo $brand_details[$row[("brand_id")]]; ?></td>
							<td width="60" style="word-break:break-all;"><? echo $row[('yarn_lot')]; ?></td>
							<td width="70" style="word-break:break-all;">
								<?
									//echo $color_arr[$row[csf("color_id")]];
								$color_id_arr = array_unique(explode(",", $row[("color_id")]));
								$all_color_name = "";
								foreach ($color_id_arr as $c_id) {
									$all_color_name .= $color_arr[$c_id] . ",";
								}
								$all_color_name = chop($all_color_name, ",");
								echo $all_color_name;
								?>
							</td>
							<td width="70"
							style="word-break:break-all;"><? echo $color_range[$row[("color_range_id")]]; ?></td>
							<td width="150"
							style="word-break:break-all;"><? echo $composition_arr[$row[('fab_desc_id')]]; ?></td>
							<td width="50" style="word-break:break-all;"
							align="center"><? echo $row[('stitch_length')]; ?></td>
							<td width="50" style="word-break:break-all;"
							align="center"><? echo $spandex_stitch_length; ?></td>
							<td width="50" style="word-break:break-all;"
							align="center"><? echo $row[('gsm')]; ?></td>
							<td width="40" style="word-break:break-all;"
							align="center"><? echo $row[('width')]; ?></td>
							<td width="40" style="word-break:break-all;"
							align="center"><? echo $machine_details[$machine_no]["machine_no"]; ?></td>
							<td width="40" style="word-break:break-all;"
                                    align="center"><? echo $mc_dia;//$machine_details[$row[csf('machine_no_id')]]["dia_width"];
                                    ?></td>
                                    <td width="40" style="word-break:break-all;"
                                    align="center"><? echo $machine_gg;//$machine_details[$row[csf('machine_no_id')]]["gauge"];
                                    ?></td>
                                    <td width="80" align="center"><? echo $barcode_no; ?></td>
                                    <td width="40" align="center"><? echo $row[('roll_no')]; ?></td>
                                    <td align="right"><? echo number_format($row[('current_delivery')], 2); ?></td>
                                </tr>
                                <?

                                $grand_total_delivery += $row[('current_delivery')];
                                $sub_tot_qty += $row[('current_delivery')];
                                $sub_tot_roll_count+=$tot_roll_no;
                                $grand_tot_roll_count+=$tot_roll_no;

                                $i++;
                            }
                            ?>

                            <tr bgcolor="#CCCCCC">
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td>&nbsp;</td>
                            	<td align="right" style="font-weight:bold;">Sub Total:</td>
                            	<td align="center"><? echo $sub_tot_roll_count;
                            	$sub_tot_roll_count = 0; ?></td>
                            	<td align="right"><? echo number_format($sub_tot_qty, 2);
                            	$sub_tot_qty = 0; ?></td>
                            </tr>
                            <?
                        }
                        ?>

                        <?
                    }
                    ?>
                    <?
                }
                ?>
                <tr bgcolor="#CCCCCC">
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>


                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td>&nbsp;</td>
                	<td align="right"  style="font-weight:bold;">Grand Total:</td>
                	<td align="center"><? echo number_format($grand_tot_roll_count, 0); ?></td>
                	<td align="right"><? echo number_format($grand_total_delivery, 2); ?></td>
                </tr>
            </table>

        </div>
        <div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(125, $company, "1600px"); ?></div>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        	function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}

if ($action == "grey_delivery_printmg") //New Button Print MG
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$is_salesOrder = 0;
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];


	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$location_data = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan", 4 => "Sales Order");

	$mstData = sql_select("SELECT a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis, d.sales_booking_no,c.buyer_id from pro_grey_prod_delivery_mst a
		inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
		inner join inv_receive_master c on b.grey_sys_id = c.id
		inner join fabric_sales_order_mst d on b.order_id=d.id
		where a.id=$update_id group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis, d.sales_booking_no,c.buyer_id");


	$sales_booking_no = $mstData[0][csf('sales_booking_no')];
	if($sales_booking_no !="")
	{
		$order_data_arr = array();
		$booking_data = sql_select("SELECT a.booking_no,a.buyer_id,a.job_no from wo_booking_mst a where a.status_active=1 and a.is_deleted=0 and a.booking_no='$sales_booking_no'
			union all
				SELECT a.booking_no,a.buyer_id,a.job_no
				FROM wo_non_ord_samp_booking_mst a
				WHERE  a.status_active=1 and a.is_deleted=0 and a.booking_no='$sales_booking_no'");
		foreach ($booking_data as $row)
		{
			$order_data_arr[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
			$order_data_arr[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		}
		$within_group = return_field_value("within_group", "fabric_sales_order_mst", "sales_booking_no='$sales_booking_no'", "within_group");
		$buyer_id = return_field_value("buyer_id", "fabric_sales_order_mst", "sales_booking_no='$sales_booking_no'", "buyer_id");

	}

	if ($within_group == 1)
	{
		$job_buyer = $buyer_array[$order_data_arr[$sales_booking_no]['buyer_id']];
	}
	else
	{
		$job_buyer = $buyer_array[$mstData[0][csf('buyer_id')]];
	}

	$search_param = $mstData[0][csf('booking_no')];
	if ($mstData[0][csf('receive_basis')] == 2)
	{
		$booking_data = sql_select("select b.booking_no, c.company_id from ppl_planning_info_entry_dtls a
			inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
			inner join wo_booking_mst c on b.booking_no = c.booking_no
			where a.id = $search_param");

		$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
		if ($is_salesOrder == "" || $is_salesOrder == 0) {
			$is_salesOrder = 0;
		} else {
			$is_salesOrder = 1;
		}
	}
	else if ($mstData[0][csf('receive_basis')] == 4)
	{
		$is_salesOrder = 1;
		$booking_data = sql_select("select a.sales_booking_no, b.company_id from fabric_sales_order_mst a
			inner join wo_booking_mst b on a.booking_id = b.id
			where a.job_no = '$search_param'");
	}
	else
	{
		$booking_data = sql_select("select a.booking_no, a.company_id from wo_booking_mst a where a.booking_no = '$search_param'");
	}

	?>
	<div style="width:1130px;">
		<table width="1130" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td rowspan="3" colspan="2">
					<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px;"><? echo $company_array[$company]['name']; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px;"><u>Roll Wise Grey Fabric Delivery challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1130" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="120">Knitting Company</td>
				<td width="170">:&nbsp;
					<?
						$knit_company = "&nbsp;";
						if ($mstData[0][csf('knitting_source')] == 1) {
							$knit_company = $company_array[$mstData[0][csf('knitting_company')]]['name'];
						} else if ($mstData[0][csf('knitting_source')] == 3) {
							$knit_company = $supplier_arr[$mstData[0][csf('knitting_company')]];
						}
						echo $knit_company;
					?>
				</td>
				<td style="font-size:14px; font-weight:bold;" width="120">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="120">Production Basis</td>
				<td width="170">:&nbsp;<? echo $receive_basis[$mstData[0][csf('receive_basis')]]; ?></td>
			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="120">Buyer</td>
				<td width="170" style="font-size:14px;">:&nbsp;<? echo $job_buyer; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="120">Location</td>
				<td width="170">:&nbsp;<? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
				<td width="120" style="font-size:14px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
			</tr>
			<tr>
			<tr>
				<td width="120" style="font-size:14px; font-weight:bold;">Remarks</td>
				<td colspan="4" style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
				<td width="810" id="barcode_img_id" align="right"></td>
			</tr>

		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="1130" class="rpt_table"
			style="font-family: tahoma; font-size: 12px;">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="100">Style</th>
					<th width="110"><?php echo ($is_po && ($is_salesOrder == 1)) ? "Sales Order No " : "Order No" ?></th>
					<th width="110">Prog./ Book. No</th>
					<th width="50">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="60">Lot No</th>
					<th width="70">Fab Color</th>
					<th width="150">Fabrication</th>
					<th width="50">Stich Length</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. Dia</th>
					<th width="40">MC. Gauge</th>
					<th width="80">Barcode No</th>
					<th width="40">Roll No</th>
					<th width="40">QC Pass Qty</th>
					<th width="40">Qty Pcs</th>
					<th>Size</th>
				</tr>
			</thead>
			<?

			$i = 0;
			$tot_qty = 0;

			$sql = "SELECT a.knitting_company as knit_com,a.booking_no,b.machine_no_id as machine_no,d.barcode_num as barcode_no,d.current_delivery , a.recv_number_prefix_num as recv_no, a.buyer_id, a.receive_basis as recv_basis, a.booking_without_order as without_ordr, a.booking_id,
			a.knitting_source, a.receive_date, a.location_id, b.prod_id, b.febric_description_id as fab_desc_id, b.gsm, b.width, b.yarn_count,b.yarn_lot, b.color_id, b.color_range_id,  b.stitch_length, b.brand_id, b.shift_name, c.roll_no, c.po_breakdown_id as po_id, c.booking_no as bwo, c.booking_without_order, c.qc_pass_qnty_pcs, c.coller_cuff_size, d.mst_id,p.job_no_prefix_num as job, p.job_no, p.style_ref_no, p.id, p.sales_booking_no
			FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d,fabric_sales_order_mst p WHERE a.id=b.mst_id and b.id=c.dtls_id and d.entry_form=56 and c.id=d.roll_id and d.order_id=p.id and d.mst_id=$update_id and b.id=d.sys_dtls_id and d.status_active=1 and d.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by a.recv_number_prefix_num , a.buyer_id, a.receive_basis, a.booking_without_order , a.booking_no,a.booking_id, a.knitting_source, a.knitting_company,a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id,b.shift_name, d.barcode_num, c.roll_no, c.po_breakdown_id, c.booking_no , c.booking_without_order, c.qc_pass_qnty_pcs, c.coller_cuff_size, d.mst_id ,d.current_delivery,p.job_no_prefix_num, p.job_no, p.style_ref_no, p.id, p.sales_booking_no
			order by a.knitting_company,a.booking_no,b.machine_no_id,d.barcode_num";

			//echo $sql;

			$result = sql_select($sql);
			$roll_wise_arr = array();
			$bookingIdArr = array();
			$febDesIdArr = array();
			$bookingIdChk = array();
			$febDesIdChk = array();
			foreach ($result as $row)
			{
				$tot_roll=count($row[csf('roll_no')]);
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['rec_no'] = $row[csf('recv_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['receive_date'] = $row[csf('receive_date')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['buyer_id'] = $row[csf('buyer_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['recv_basis'] = $row[csf('recv_basis')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['without_ordr'] = $row[csf('without_ordr')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['booking_id'] = $row[csf('booking_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['knitting_source'] = $row[csf('knitting_source')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['stitch_length'] = $row[csf('stitch_length')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['brand_id'] = $row[csf('brand_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['color_id'] = $row[csf('color_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['yarn_lot'] = $row[csf('yarn_lot')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['color_range_id'] = $row[csf('color_range_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['yarn_count'] = $row[csf('yarn_count')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['fab_desc_id'] = $row[csf('fab_desc_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['shift_name'] = $row[csf('shift_name')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['location_id'] = $row[csf('location_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['roll_no'] = $row[csf('roll_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['tot_roll_no']=$tot_roll;
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['po_id'] = $row[csf('po_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['current_delivery'] = $row[csf('current_delivery')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['bwo'] = $row[csf('bwo')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['job'] = $row[csf('job')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['prod_id'] = $row[csf('prod_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['current_delivery'] = $row[csf('current_delivery')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['gsm'] = $row[csf('gsm')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['width'] = $row[csf('width')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['job_no'] = $row[csf('job_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['po_number'] = $row[csf('po_number')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['booking_id'] = $row[csf('booking_id')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['qc_pass_qnty_pcs'] = $row[csf('qc_pass_qnty_pcs')];
				$roll_wise_arr[$row[csf('knit_com')]][$row[csf('booking_no')]][$row[csf('machine_no')]][$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];

				if($bookingIdChk[$row[csf('booking_id')]] == "")
				{
					$bookingIdChk[$row[csf('booking_id')]] = $row[csf('booking_id')];
					array_push($bookingIdArr,$row[csf('booking_id')]);
				}

				if($febDesIdChk[$row[csf('fab_desc_id')]] == "")
				{
					$febDesIdChk[$row[csf('fab_desc_id')]] = $row[csf('fab_desc_id')];
					array_push($febDesIdArr,$row[csf('fab_desc_id')]);
				}
			}
			//echo "<pre>";print_r($febDesIdArr);

			$sql_dtls_knit = "SELECT e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
			from  pro_grey_prod_entry_dtls d,  inv_receive_master e
			where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22) ".where_con_using_array($bookingIdArr,0,'e.booking_id')." ";
			//echo $sql_dtls_knit;
			$result_arr = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result_arr as $row)
			{
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}
			$composition_arr = array();
			$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and b.status_active =1 ".where_con_using_array($febDesIdArr,0,'a.id')."";
			//echo $sql_deter;
			$data_array = sql_select($sql_deter);
			foreach ($data_array as $row) {
				$constructtion_arr[$row[csf('id')]] = $row[csf('construction')];
				$composition_arr[$row[csf('id')]] .= $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
			}


			/* $sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id ".where_con_using_array($febDesIdArr,0,'a.id')."";
			//echo $sql_deter;
			$data_array = sql_select($sql_deter);
			foreach ($data_array as $row)
			{
				if (array_key_exists($row[csf('id')], $composition_arr)) {
					$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
				} else {
					$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
				}
			} */


			$loc_arr = array();
			$loc_nm = ": ";
			$i = 1;
			$j = 1;
			$grand_total_delivery = 0;$sub_tot_roll_coun=$grand_tot_roll_count=0;
			$tot_qty = 0;
			foreach ($roll_wise_arr as $knit_com => $knit_data)
			{
				foreach ($knit_data as $bookin_prog => $bookin_prog_data)
				{
					foreach ($bookin_prog_data as $machine_no => $machine_data)
					{
						foreach ($machine_data as $barcode_no => $row)
						{
							$po_number = $row[('job_no')];
							$booking_no = $row[('sales_booking_no')];

							$within_group = return_field_value("within_group", "fabric_sales_order_mst", "sales_booking_no='$booking_no'", "within_group");
									//echo $within_group;
							if ($loc_arr[$row[('location_id')]] == "") {
								$loc_arr[$row[('location_id')]] = $row[('location_id')];
								$loc_nm .= $location_arr[$row[('location_id')]] . ', ';
							}

							$knit_company = "&nbsp;";
							if ($row[("knitting_source")] == 1) {
								$knit_company = $company_array[$knit_com]['shortname'];
							} else if ($row[("knitting_source")] == 3) {
								$knit_company = $supplier_arr[$knit_com];
							}

							$count = '';
							$yarn_count = explode(",", $row[('yarn_count')]);
							foreach ($yarn_count as $count_id) {
								if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
							}

							if ($row[('recv_basis')] == 1) {
										//$booking_no=explode("-",$row[csf('booking_no')]);
										//$prog_book_no=(int)$booking_no[3];
								$prog_book_no = "";
							} else $prog_book_no = $bookin_prog;

							if ($row[("recv_basis")] == 2) {

								$is_salesOrder = sql_select("select a.id as prog_no,b.booking_no,a.spandex_stitch_length, a.is_sales,a.machine_gg,a.machine_dia  from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
								$plan_booking_no = $is_salesOrder[0][csf('booking_no')];
								$prog_no = $is_salesOrder[0][csf('prog_no')];
								$mc_dia = $is_salesOrder[0][csf('machine_dia')];
								$machine_gg = $is_salesOrder[0][csf('machine_gg')];
								$spandex_stitch_length = $is_salesOrder[0][csf('spandex_stitch_length')];
										//echo $prog_no.'ddd';
							} else {
								$is_salesOrder = sql_select("select a.id as prog_no,b.booking_no,a.spandex_stitch_length, a.is_sales,a.machine_gg,a.machine_dia  from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where b.booking_no = $booking_no");
								$prog_no = $is_salesOrder[0][csf('prog_no')];

								$mc_dia = $machine_dia_guage_arr[$row[('booking_id')]][$row[('prod_id')]]['dia'];
								$machine_gg = $machine_dia_guage_arr[$row[('booking_id')]][$row[('prod_id')]]['gg'];
								$spandex_stitch_length = $is_salesOrder[0][csf('spandex_stitch_length')];
										//echo $prog_no.'ddddddddd';
							}
							/* if ($row[("recv_basis")] == 4) // SALES ORDER
							{
								$is_salesOrder = 1;
							} */

							if ($within_group == 1) {
								$job_style = $row[('style_ref_no')];
							} else {
								$job_style = $row[('style_ref_no')];


							}
							$style_ref = $row[('style_ref_no')];
							$tot_roll_no = $row[('tot_roll_no')];
							?>
							<tr>
								<td width="30"><? echo $i; ?></td>
								<td width="100" style="word-break:break-all;"><? echo $job_style; ?></td>
								<td width="110" style="word-break:break-all;"><? echo $po_number; ?></td>
								<td width="110" style="word-break:break-all;">
								P: <? echo $prog_no . (($prog_no != "") ? " /<br />" : "") . "B: " . $booking_no; ?></td>
								<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
								<td width="70"
								style="word-break:break-all;"><? echo $brand_details[$row[("brand_id")]]; ?></td>
								<td width="60" style="word-break:break-all;"><? echo $row[('yarn_lot')]; ?></td>
								<td width="70" style="word-break:break-all;">
									<?
									$color_id_arr = array_unique(explode(",", $row[("color_id")]));
									$all_color_name = "";
									foreach ($color_id_arr as $c_id) {
										$all_color_name .= $color_arr[$c_id] . ",";
									}
									$all_color_name = chop($all_color_name, ",");
									echo $all_color_name;
									?>
								</td>
								<td width="150"
								style="word-break:break-all;"><? echo $composition_arr[$row[('fab_desc_id')]]; ?></td>
								<td width="50" style="word-break:break-all;"
								align="center"><? echo $row[('stitch_length')]; ?></td>
								<td width="50" style="word-break:break-all;"
								align="center"><? echo $row[('gsm')]; ?></td>
								<td width="40" style="word-break:break-all;"
								align="center"><? echo $row[('width')]; ?></td>
								<td width="40" style="word-break:break-all;" align="center"><? echo $mc_dia; ?></td>
								<td width="40" style="word-break:break-all;" align="center"><? echo $machine_gg; ?></td>
								<td width="80" align="center"><? echo $barcode_no; ?></td>
								<td width="40" align="center"><? echo $row[('roll_no')]; ?></td>
								<td width="40" align="right"><? echo number_format($row[('current_delivery')], 2); ?></td>
								<td width="40" align="center"><? echo $row[('qc_pass_qnty_pcs')]; ?></td>
								<td align="center"><? echo $row[('coller_cuff_size')]; ?></td>
							</tr>
							<?							
							$sub_tot_qty += $row[('current_delivery')];
							$sub_tot_roll_count+=$tot_roll_no;
							$sub_tot_qty_pcs += $row[('qc_pass_qnty_pcs')];

							$grand_total_delivery += $row[('current_delivery')];
							$grand_tot_roll_count+=$tot_roll_no;
							$grand_tot_qty_pcs += $row[('qc_pass_qnty_pcs')];

							$i++;
	                    }
                        ?>
                        <tr bgcolor="#CCCCCC">
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td>&nbsp;</td>
                        	<td align="right" style="font-weight:bold;">Sub Total:</td>
                        	<td align="center"><? echo $sub_tot_roll_count;
                        	$sub_tot_roll_count = 0; ?></td>
                        	<td align="right"><? echo number_format($sub_tot_qty, 2);
                        	$sub_tot_qty = 0; ?></td>
                        	<td align="right"><? echo number_format($sub_tot_qty_pcs, 2);
                        	$sub_tot_qty_pcs = 0; ?></td>
                        	<td>&nbsp;</td>
                        </tr>
	                    <?
	                }
				}
				?>
				<?
	        }
            ?>
            <tr bgcolor="#CCCCCC">
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td>&nbsp;</td>
            	<td align="right"  style="font-weight:bold;">Grand Total:</td>
            	<td align="center"><? echo number_format($grand_tot_roll_count, 0); ?></td>
            	<td align="right"><? echo number_format($grand_total_delivery, 2); ?></td>
            	<td align="right"><? echo number_format($grand_tot_qty_pcs, 2); ?></td>
            	<td>&nbsp;</td>
            </tr>
        </table>
    </div>
    <div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(125, $company, "1130px"); ?></div>
    <script type="text/javascript" src="../../js/jquery.js"></script>
    <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
    <script>
    	function generateBarcode(valuess)
		{
			var value = valuess;//$("#barcodeValue").val();
			//alert(value)
			var btype = 'code39';//$("input[name=btype]:checked").val();
			var renderer = 'bmp';// $("input[name=renderer]:checked").val();

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			//$("#barcode_img_id").html('11');
			value = {code: value, rect: false};

			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
		document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
	</script>
    <?
    exit();
}


if ($action == "grey_delivery_print_machine")
{
	extract($_REQUEST);
	$data = explode('*', $data);

	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");

	$machine_sql = sql_select("select id, machine_no, dia_width from lib_machine_name");
	$machine_details = array();
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
	}
	//$machine_details=return_library_array( "select id, machine_no from lib_machine_name", "id", "machine_no");

	$mstData = sql_select("select company_id, delevery_date, knitting_source, knitting_company,remarks,attention from pro_grey_prod_delivery_mst where id=$update_id");

	$job_array = array();
	$job_sql = "select a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1550px;">
		<table width="1290" cellspacing="0" align="center" border="0">
			<tr>
				<td colspan="2" rowspan="4">
					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px"><? echo $com_dtls[0]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px"><? echo $com_dtls[1]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px">
					<strong style="margin-right:300px"><u>Delivery Challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>

		<table width="1290" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="60">Location</td>
				<td width="170" id="location_td"></td>
				<td width="810" id="barcode_img_id" align="right"></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>

				<td style="font-size:16px; font-weight:bold;">Floor No.</td>
				<td style="font-size:14px;">:&nbsp;<? echo $floor_name; ?></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Remarks</td>
				<td style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1550" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="90">Order No</th>
					<th width="60">Buyer <br> Job</th>
					<th width="60">File No <br> Ref No</th>
					<th width="50">System ID</th>
					<th width="65">Prog./ Book. No</th>
					<th width="80">Production Basis</th>
					<th width="70">Production Date</th><!--new-->
					<th width="40">Shift</th><!--new-->
					<th width="70">Knitting Company</th>
					<th width="50">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="60">Lot No</th>
					<th width="70">Fab Color</th>
					<th width="70">Color Range</th>
					<th width="150">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. No</th>
					<th width="50">MC. dia</th>
					<th width="80">Barcode No</th>
					<th width="40">Roll No</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?
			$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
			from  pro_grey_prod_entry_dtls d,  inv_receive_master e
			where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
			$result_arr = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result_arr as $row) {
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}
			$i = 0;
			$tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			if ($kniting_source == 1)//in-house
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e
				WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by e.seq_no";
			} else {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, d.current_delivery, c.booking_no as bwo, c.booking_without_order
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d
				WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by c.roll_no";
			}
			//echo $sql;
			$result = sql_select($sql);
			$loc_arr = array();
			$loc_nm = ": ";
			foreach ($result as $row) {
				if ($loc_arr[$row[csf('location_id')]] == "") {
					$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
					$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
				}

				$knit_company = "&nbsp;";
				if ($row[csf("knitting_source")] == 1) {
					$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
				} else if ($row[csf("knitting_source")] == 3) {
					$knit_company = $supplier_arr[$row[csf("knitting_company")]];
				}

				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
				}

				if ($row[csf('receive_basis')] == 1) {
					$booking_no = explode("-", $row[csf('booking_no')]);
					$prog_book_no = (int)$booking_no[3];
				} else $prog_book_no = $row[csf('booking_no')];
				if ($row[csf('receive_basis')] == 2) {
					$planOrder = sql_select("select b.booking_no, a.is_sales,a.machine_gg,a.machine_dia from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
					$mc_dia = $planOrder[0][csf('machine_dia')];
					$machine_gg = $planOrder[0][csf('machine_gg')];
				} else {
					$mc_dia = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'];
					$machine_gg = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'];
				}

				$i++;
				?>
				<tr>
					<td width="30"><? echo $i; ?></td>
					<?
					if ($row[csf('receive_basis')] == 1) {
						?>
						<td width="90" style="word-break:break-all;"><? echo $row[csf('bwo')]; ?></td>
						<td width="60"
						style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo "F:<br>R:"; ?></td>
						<?
					} else {
						?>
						<td width="90"
						style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
						<td width="60"
						style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]] . "<br>" . $job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
						<td width="60"
						style="word-break:break-all;"><? echo "F:" . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . "<br>R:" . $job_array[$row[csf('po_breakdown_id')]]['ref_no']; ?></td>
						<?
					}
					?>
					<td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
					<td width="65" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
					<td width="80"
					style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
					<td width="40"
					style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
					<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
					<td width="70" style="word-break:break-all;">
						<?
						//echo $color_arr[$row[csf("color_id")]];
						$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
						$all_color_name = "";
						foreach ($color_id_arr as $c_id) {
							$all_color_name .= $color_arr[$c_id] . ",";
						}
						$all_color_name = chop($all_color_name, ",");
						echo $all_color_name;
						?>
					</td>
					<td width="70"
					style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
					<td width="150"
					style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td width="50" style="word-break:break-all;"
					align="center"><? echo $row[csf('stitch_length')]; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
					<td width="40" style="word-break:break-all;"
					align="center"><? echo $machine_details[$row[csf('machine_no_id')]]["machine_no"];
					?></td>
					<td width="50" style="word-break:break-all;"
                        align="center"><? echo $mc_dia;//$machine_gg;//$machine_details[$row[csf('machine_no_id')]]["dia_width"];
                        ?></td>
                        <td width="80" align="center"><? echo $row[csf('barcode_no')]; ?></td>
                        <td width="40" align="center"><? echo $row[csf('roll_no')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
                    </tr>
                    <?
                    $tot_qty += $row[csf('current_delivery')];
                }

                $loc_nm = rtrim($loc_nm, ', ');
                ?>
                <tr>
                	<td align="right" colspan="22"><strong>Total</strong></td>
                	<td align="right"><? echo $i; ?></td>
                	<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
                </tr>

                <tr>
                	<td colspan="2" align="left"><b>Remarks:</b></td>
                	<td colspan="22">&nbsp;</td>
                </tr>
            </table>
        </div>
        <? echo signature_table(125, $company, "1550px"); ?>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        	function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}

if ($action == "grey_delivery_print_fabric_label")
{
	extract($_REQUEST);
	$data = explode('*', $data);

	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$remarks 	= $data[6];
	$attention 	= $data[7];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}


	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");

	$mstData = sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");

	$job_array = array();
	$job_sql = "select a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$job_sql_result = sql_select($job_sql);
	foreach ($job_sql_result as $row) {
		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
		$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
	}

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1600px;">
		<table width="1390" cellspacing="0" align="center" border="0">
			<tr>
				<td rowspan="3" colspan="2">
					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px"><? echo $com_dtls[0]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px"><? echo $com_dtls[1]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px">
					<strong style="margin-right:300px"><u>Delivery Challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1390" cellspacing="0" align="center" border="0">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="60">Location</td>
				<td width="170" id="location_td"></td>
				<td width="810" id="barcode_img_id" align="right"></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:16px; font-weight:bold;">Floor No.</td>
				<td colspan="2">:&nbsp;<? echo $floor_name; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1600" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="90">Order No</th>
					<th width="60">Buyer <br> Job</th>
					<th width="60">File No <br> Ref No</th>
					<th width="50">System ID</th>
					<th width="65">Prog./ Book. No</th>
					<th width="100">Plan Batch No</th>
					<th width="80">Production Basis</th>
					<th width="90">Production Date</th><!--new-->
					<th width="40">Shift</th><!--new-->
					<th width="70">Knitting Company</th>
					<th width="80">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="80">Lot No</th>
					<th width="90">Fab Color</th>
					<th width="70">Color Range</th>
					<th width="150">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. No</th>
					<th width="40">MC. dia</th>
					<th width="40">No Of Roll</th>
					<th width="40">Roll Qty</th>
				</tr>
			</thead>
			<?
			$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
			from  pro_grey_prod_entry_dtls d,  inv_receive_master e
			where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
			$result_arr = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result_arr as $row) {
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}

			$i = 0;
			$tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			if ($kniting_source == 1)//in-house
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,e.seq_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.po_breakdown_id , c.booking_no, c.booking_without_order,e.seq_no order by e.seq_no";
			} else {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,e.seq_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.po_breakdown_id , c.booking_no, c.booking_without_order,e.seq_no order by e.seq_no";
			}
			//echo $sql;
			$result = sql_select($sql);
			$loc_arr = array();
			$loc_nm = ": ";
			foreach ($result as $row) {
				if ($loc_arr[$row[csf('location_id')]] == "") {
					$loc_arr[$row[csf('location_id')]] = $row[csf('location_id')];
					$loc_nm .= $location_arr[$row[csf('location_id')]] . ', ';
				}

				$knit_company = "&nbsp;";
				if ($row[csf("knitting_source")] == 1) {
					$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
				} else if ($row[csf("knitting_source")] == 3) {
					$knit_company = $supplier_arr[$row[csf("knitting_company")]];
				}

				$count = '';
				$yarn_count = explode(",", $row[csf('yarn_count')]);
				foreach ($yarn_count as $count_id) {
					if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
				}

				if ($row[csf('receive_basis')] == 1) {
					$booking_no = explode("-", $row[csf('booking_no')]);
					$prog_book_no = (int)$booking_no[3];
				} else $prog_book_no = $row[csf('booking_no')];
				if ($row[csf('receive_basis')] == 2) {
					$planOrder = sql_select("select b.booking_no, a.is_sales,a.machine_gg,a.machine_dia,a.batch_no from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
					$mc_dia = $planOrder[0][csf('machine_dia')];
					$planBatch = $planOrder[0][csf('batch_no')];
					//$machine_gg=$planOrder[0][csf('machine_gg')];
				} else {
					$mc_dia = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'];
					//$machine_gg=$machine_dia_guage_arr[$row[csf('booking_no')]][$row[csf('prod_id')]]['gg'];
				}

				$i++;
				?>
				<tr>
					<td width="30"><? echo $i; ?></td>
					<?
					if ($row[csf('receive_basis')] == 1) {
						?>
						<td width="90" style="word-break:break-all;"><? echo $row[csf('bwo')]; ?></td>
						<td width="60"
						style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]; ?></td>
						<td width="60" style="word-break:break-all;"><? echo "F:<br>R:"; ?></td>
						<?
					} else {
						?>
						<td width="90"
						style="word-break:break-all;"><? echo $job_array[$row[csf('po_breakdown_id')]]['po']; ?></td>
						<td width="60"
						style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]] . "<br>" . $job_array[$row[csf('po_breakdown_id')]]['job']; ?></td>
						<td width="60"
						style="word-break:break-all;"><? echo "F:" . $job_array[$row[csf('po_breakdown_id')]]['file_no'] . "<br>R:" . $job_array[$row[csf('po_breakdown_id')]]['ref_no']; ?></td>
						<?
					}
					?>
					<td width="50"><? echo $row[csf('recv_number_prefix_num')]; ?></td>
					<td width="65" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
					<td width="100" style="word-break:break-all;"><? echo $planBatch; ?></td>
					<td width="80"
					style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo change_date_format($row[csf("receive_date")]); ?></td>
					<td width="40"
					style="word-break:break-all;"><? echo $shift_name[$row[csf("shift_name")]]; ?></td>
					<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
					<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
					<td width="70"
					style="word-break:break-all;"><? echo $brand_details[$row[csf("brand_id")]]; ?></td>
					<td width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
					<td width="70" style="word-break:break-all;">
						<?
						//echo $color_arr[$row[csf("color_id")]];
						$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
						$all_color_name = "";
						foreach ($color_id_arr as $c_id) {
							$all_color_name .= $color_arr[$c_id] . ",";
						}
						$all_color_name = chop($all_color_name, ",");
						echo $all_color_name;
						?>
					</td>
					<td width="70"
					style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
					<td width="150"
					style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
					<td width="50" style="word-break:break-all;"
					align="center"><? echo $row[csf('stitch_length')]; ?></td>
					<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
					<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
					<td width="40" style="word-break:break-all;"
					align="center"><? echo $machine_details_arr[$row[csf('machine_no_id')]]; ?></td>
					<td width="40"
                        align="center"><? echo $mc_dia;//$machine_details[$row[csf('machine_no_id')]]["dia_width"];
                        ?></td>
                        <td width="40" align="center"><? echo $row[csf('num_of_roll')]; ?></td>
                        <td align="right"><? echo number_format($row[csf('current_delivery')], 2); ?></td>
                    </tr>
                    <?
                    $tot_no_of_roll += $row[csf('num_of_roll')];
					$tot_qty += $row[csf('current_delivery')];
                }

                $loc_nm = rtrim($loc_nm, ', ');
                ?>
                <tr>
                	<td align="right" colspan="22"><strong>Total</strong></td>
                	<td align="center"><? echo $tot_no_of_roll; ?></td>
                	<td align="right"><? echo number_format($tot_qty, 2, '.', ''); ?></td>
                </tr>
                <tr>
                	<td colspan="2" align="left"><b>Remarks:</b></td>
                	<td colspan="22"><? echo $remarks; ?></td>
                </tr>
                <tr>
                	<td colspan="2" align="left"><b>Roll No:</b></td>
                	<td colspan="22"><p style="word-wrap:break-word; width:1350px;">
                		<?
                		if ($db_type == 0) {
                			$sql = sql_select("SELECT group_concat(c.roll_no) as roll_no  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by e.seq_no");
                		} else {
                			$sql = sql_select("SELECT listagg((cast(c.roll_no as varchar2(100))),',')within group (order by roll_no) as roll_no  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 order by e.seq_no");
                		}
                		echo $sql[0][csf('roll_no')];


                		?>
                	</p></td>
                </tr>
            </table>
        </div>
        <? echo signature_table(125, $company, "1500px"); ?>
        <script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
        <script>
        	function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}

if ($action == "populate_floor_data")
{
	$barcodeDataArr = Array();
	$data_array = sql_select("SELECT b.floor_id, d.floor_name from  pro_grey_prod_entry_dtls b, pro_roll_details c, lib_prod_floor d where b.id=c.dtls_id and b.floor_id=d.id and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in ($data) group by b.floor_id, d.floor_name");
	foreach ($data_array as $row)
	{
		if ($row[csf("floor_id")] != "" || $row[csf("floor_id")] != 0)
		{
			$floor_id_array[$row[csf("floor_id")]] = $row[csf("floor_id")];
			$floor_name_array[$row[csf("floor_name")]] = $row[csf("floor_name")];
		}
	}

	$floor_id_array = array_filter($floor_id_array);
	$floor_name_array = array_filter($floor_name_array);

	echo implode(",", array_unique($floor_id_array)) . "**" . implode(",", array_unique($floor_name_array));
}
if ($action == "populate_floor_ids_to_name")
{
	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$floor_id_arr = explode(",", $data);
	$comma = "";
	$name = "";
	foreach ($floor_id_arr as $id) {
		$name .= $comma . $floor_name_arr[$id];
		$comma = ",";
	}
	echo $name;
}

if($action == "check_if_barcode_scanned")
{
	$scanned_barcode_data=sql_select("select barcode_no from pro_roll_details where barcode_no='$data' and entry_form=56 and status_active=1 and is_deleted=0");
	if(!empty($scanned_barcode_data)){
		echo "1";
	}else{
		echo "0";
	}
	exit();
}

if($action == "CheckVariableSettingAutoQC")
{
	extract($_REQUEST);
	$data = explode('**', $data);

	$variable_settingAutoQC = return_field_value("qc_mandatory", "variable_settings_production", "company_name =$data[1] and variable_list in(48) and item_category_id=13 and is_deleted=0 and status_active=1", "qc_mandatory");
	if($variable_settingAutoQC == 1)
	{
		$roll_status = sql_select("SELECT roll_status from pro_qc_result_mst  where barcode_no=$data[0] and is_deleted=0 and status_active=1");
		$roll_status_set = $roll_status[0][csf("roll_status")];

		if( $roll_status_set == "" || $roll_status_set ==2 || $roll_status_set ==3)
		{
			echo "1";
		}else{
			echo "0";
		}
	}
	else
	{
		echo "0";
	}
}

if ($action == "grey_delivery_print_11_old_with_poBreakDownID")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$knit_company = $data[6];
	$location_id= $data[7];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name,group_id,vat_number from lib_company");

	$group_com_arr_lib = return_library_array("select id,group_name from lib_group where is_deleted=0 and status_active=1 order by group_name", 'id', 'group_name');

	//select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name"
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['group_id'] = $row[csf('group_id')];
		$company_array[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
	}


	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$address_arr = return_library_array("select id, address from lib_location", "id", "address");
	$feeder_arr = array(1 => "Full Feeder", 2 => "Half Feeder");

	$mstData = sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");


	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);

	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$sql_mst_tbl=sql_select("select knitting_company,location_id,remarks,attention from pro_grey_prod_delivery_mst where id=$update_id");

	?>
	<div style="width:1880px;">
		<table width="1190" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td rowspan="5" colspan="2" valign="top">
					<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px"><? echo $group_com_arr_lib[$company_array[$company]['group_id']]; ?></strong><br>
					<?php /*?> <strong style="margin-right:300px"><? echo $company_array[$company]['name']; ?></strong><?php */?>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px">
					<strong style="margin-right:300px">Working Company: <? echo $knit_company. " (Location: ".$location_arr[$location_id]. ")"; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Working Company Add: <? echo $address_arr[$sql_mst_tbl[0][csf('location_id')]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px" >
					<strong style="margin-right:300px">Owner Company: <span id="woner_comp_td"></span></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Fabric Delivery Challan (Knitting)</strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1490" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="80">Delivery Date:</td>
				<td width="100"><? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:16px; font-weight:bold;" width="50">Vat No:</td>
				<td width="100"><? echo $company_array[$company]['vat_number']; ?></td>
				<td width="610" id="barcode_img_id" align="left"></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Remarks</td>
				<td colspan="2">:&nbsp;<? echo $sql_mst_tbl[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1680" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="60">Buyer</th>
					<th width="60">Job</th>
					<!--<th width="60">Style No</th>-->
					<th width="60">Ref No</th>
					<!--<th width="90">PO No</th>-->
					<th width="65">Prog No</th>
					<!--<th width="50">Requisition No</th>-->
					<th width="65">Book. No</th>
					<th width="100">Production Basis</th>
					<!--<th width="90">Production Date</th>--><!--new-->
					<th width="70">Knitting Company</th>
					<!--<th width="40">Shift</th>--><!--new-->
					<th width="100">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="80">Lot No</th>
					<th width="90">Fab Color</th>
					<th width="100">Color Range</th>
					<th width="100">Feeder</th>
					<th width="220">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. Dia</th>
					<th width="40">MC. Gauge</th>
					<th width="40">No Of Roll</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?
			$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
			from  pro_grey_prod_entry_dtls d,  inv_receive_master e
			where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
			$result_arr = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result_arr as $row) {
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}

			$i = 1;
			$grnd_total_no_of_roll=0;$grnd_tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			/*if ($kniting_source == 1)//in-house
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
			} else {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
			}*/

			$delivery_res = sql_select("select roll_id, sum(a.current_delivery) as current_delivery from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$update_id and a.status_active = 1 and a.is_deleted = 0 group by a.roll_id");
			foreach ($delivery_res as $val)
			{
				$roll_ids .= $val[csf("roll_id")].",";
				$qntyFromRoll[$val[csf("roll_id")]] = $val[csf("current_delivery")];
			}
			$roll_ids = chop($roll_ids,",");

			/*$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery
			 FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.id in ($roll_ids)
			 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales
			 order by c.po_breakdown_id,a.booking_no";*/

			 $sql = "select  a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company,
			 a.location_id, a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id,
			 b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg,
			 c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery,count(c.barcode_no) as num_of_roll
			 from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			 where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1
			 and b.is_deleted = 0 and c.id in ($roll_ids)
			 group by  a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.location_id,
			 a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales
			 order by c.po_breakdown_id,a.booking_no";

			//echo $sql;
			 $sql_result = sql_select($sql);
			 $po_id_array = $sales_id_array = $booking_program_arr = array();
			 foreach ($sql_result as $row) {
			 	if($row[csf("is_sales")] == 1){
			 		$sales_id_array[] = $row[csf("po_breakdown_id")];
			 	}else{
			 		$po_id_array[] = $row[csf("po_breakdown_id")];
			 	}
			 	if ($row[csf('receive_basis')] == 2) {
			 		$booking_program_arr[] = $row[csf("booking_no")];
			 	}else{
			 		$booking_no = explode("-", $row[csf('booking_no')]);
			 		$booking_program_arr[] = (int)$booking_no[3];
			 	}
			 }
			//print_r($booking_program_arr);
			 $planOrder = sql_select("select a.id,b.booking_no,b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
			 $plan_arr = array();
			 foreach ($planOrder as $plan_row) {
			 	$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
			 	$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
			 	$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
			 }

			 $job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
			 if(!empty($po_id_array)){
			 	$job_sql = "select a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
			 	$job_sql_result = sql_select($job_sql);
			 	foreach ($job_sql_result as $row) {
			 		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
			 		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
			 		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			 		$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
			 		$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			 		$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
			 	}
			 }

			 if(!empty($sales_id_array)){
			 	$sales_details = sql_select("select id,job_no,sales_booking_no,within_group,style_ref_no,buyer_id from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
			 	foreach ($sales_details as $sales_row) {
			 		$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
			 		$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
			 		$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
			 		$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
			 		$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
			 	}
			 }
			 $booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
			 $booking_details = sql_select("select a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 $booking_cond group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");
			 foreach ($booking_details as $booking_row) {
			 	$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
			 	$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
			 	$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
			 	$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
			 	$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
			 }
			 $reqs_array = array();
			 $reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
			 foreach ($reqs_sql as $row) {
			 	$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			 }

			 $refno_data_array=array();
			/*echo "<pre>";
			print_r($sql_result);*/
			foreach ($sql_result as $row) {
				$is_sales = $row[csf('is_sales')];
				if($is_sales == 1){
					$within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
					if($within_group == 1){
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
						$job_no = $booking_arr[$booking_no]["job_no"];
						$po_id = $booking_arr[$booking_no]["po_break_down_id"];
						$style_ref_no = $job_array[$po_id]['style_ref_no'];
						$ref_no = $booking_arr[$po_id]["ref_no"];
						$buyer_id=$booking_arr[$booking_no]["buyer_id"];
					}else{
						$job_no = "";
						$style_ref_no = "";
						$ref_no = "";
						$po="";
						$buyer_id=$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id'];
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
					}
				}else{
					$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
					$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
					$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
					$buyer_id=$row[csf('buyer_id')];
					$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
				}
				$refno_data_array[$job_array[$row[csf('po_breakdown_id')]]['ref_no']][$row[csf('febric_description_id')]][]=array(
					recv_number_prefix_num=>$row[csf('recv_number_prefix_num')],
					buyer_id=>$buyer_id,
					ref_no=>$ref_no,
					receive_basis=>$row[csf('receive_basis')],
					booking_id=>$row[csf('booking_id')],
					booking_no=>$booking_no,
					knitting_source=>$row[csf('knitting_source')],
					knitting_company=>$row[csf('knitting_company')],
					location_id=>$row[csf('location_id')],
					febric_description_id=>$row[csf('febric_description_id')],
					gsm=>$row[csf('gsm')],
					width=>$row[csf('width')],
					yarn_count=>$row[csf('yarn_count')],
					yarn_lot=>$row[csf('yarn_lot')],
					color_id=>$row[csf('color_id')],
					color_range_id=>$row[csf('color_range_id')],
					machine_no_id=>$row[csf('machine_no_id')],
					stitch_length=>$row[csf('stitch_length')],
					brand_id=>$row[csf('brand_id')],
					shift_name=>$row[csf('shift_name')],
					machine_gg=>$row[csf('machine_gg')],
					machine_dia=>$row[csf('machine_dia')],
					num_of_roll=>$row[csf('num_of_roll')],
					no_of_roll=>$row[csf('no_of_roll')],
					po_breakdown_id=>$row[csf('po_breakdown_id')],
					current_delivery=>$row[csf('current_delivery')],
					bwo=>$row[csf('bwo')],
					booking_without_order=>$row[csf('booking_without_order')],
					within_group=>$row[csf('within_group')],
					is_sales=>$row[csf('is_sales')],
					receive_date=>$row[csf('receive_date')],
					job_no=>$job_no,
					style_ref_no=>$style_ref_no,
					po=>$po_id
				);//seq_no=>$row[csf('seq_no')],
			}

			$loc_arr = array();
			$loc_nm = ": ";
			$ownner_comp=" ";
			$k=1; $sub_group_arr=array();

			foreach($refno_data_array as $refArr){
				$sub_tot_qty = 0;
				$sub_total_no_of_roll=0;
				foreach ($refArr as $refDataArr) {
					$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
					foreach ($refDataArr as $row) {
						if ($loc_arr[$row['location_id']] == "") {
							$loc_arr[$row['location_id']] = $row['location_id'];
							$loc_nm .= $location_arr[$row['location_id']] . ', ';
						}
						$ownner_comp=$job_company_arr[$row['job_no']]['company_name'];
						$knit_company = "&nbsp;";
						if ($row["knitting_source"] == 1) {
							$knit_company = $company_array[$row["knitting_company"]]['shortname'];
						} else if ($row["knitting_source"] == 3) {
							$knit_company = $supplier_arr[$row["knitting_company"]];
						}

						$count = '';
						$yarn_count = explode(",", $row['yarn_count']);
						foreach ($yarn_count as $count_id) {
							if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
						}

						if ($row["booking_without_order"] != 1) {

						} else {

						}
						if ($row['receive_basis'] == 1) {
							$booking_no = explode("-", $row['booking_no']);
							$prog_book_no = (int)$booking_no[3];
						} else {
							$prog_book_no = $row['bwo'];
						}
						?>
						<tr>
							<td width="30"><? echo $i; ?></td>
							<?
							if ($row['receive_basis'] == 1) {
								?>
								<td width="90" style="word-break:break-all;"><? echo $buyer_array[$row['buyer_id']]; ?></td>
								<td width="60" style="word-break:break-all;"><? echo $row['bwo']; ?></td>
								<td width="95" style="word-break:break-all;"><? echo $row['job_no']; ?></td>
								<?
							} else {
								?>
								<td align="center" width="60" style="word-break:break-all;"><? echo $buyer_array[$row['buyer_id']]; ?></td>
								<td align="center" width="95" style="word-break:break-all;"><? echo $row['job_no']; ?></td>
								<td align="center" width="60" style="word-break:break-all;"><? echo $row['ref_no']; ?></td>
								<?
							}
							?>
							<td align="center" width="65" style="word-break:break-all;"><? echo $prog_book_no;  ?></td>
							<?
							if ($row['receive_basis'] == 2) {
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $plan_arr[$prog_book_no]["booking_no"]; ?></td>
								<?
							}
							else
							{
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $row['bwo']; ?></td>
								<?
							}
							?>
							<td align="center" width="100" style="word-break:break-all;"><? echo $receive_basis[$row['receive_basis']]; ?></td>
							<td align="center" width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
							<td align="center" width="100" style="word-break:break-all;"><? echo $count; ?></td>
							<td align="center" width="70"
							style="word-break:break-all;"><? echo $brand_details[$row['brand_id']]; ?></td>
							<td align="center" width="60" style="word-break:break-all;"><? echo $row['yarn_lot']; ?></td>
							<td align="center" width="70" style="word-break:break-all;">
								<?
								$color_id_arr = array_unique(explode(",", $row["color_id"]));
								$all_color_name = "";
								foreach ($color_id_arr as $c_id) {
									$all_color_name .= $color_arr[$c_id] . ",";
								}
								$all_color_name = chop($all_color_name, ",");
								echo $all_color_name;
								?>
							</td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $color_range[$row["color_range_id"]]; ?></td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $feeder_arr[$plan_arr[$prog_book_no]["feeder"]]; ?></td>
							<td width="220"
							style="word-break:break-all;"><? echo $composition_arr[$row['febric_description_id']]; ?></td>
							<td width="50" style="word-break:break-all;"
							align="center"><? echo $row['stitch_length']; ?></td>
							<td width="50" style="word-break:break-all;" align="center"><? echo $row['gsm']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['width']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_dia']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_gg']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><strong><? echo $row['num_of_roll']; ?></strong></td>
							<td style="word-break:break-all;" align="right"><strong><? echo $row['current_delivery']; ?></strong></td>
						</tr>
						<?
						$sub_tot_qty_fabric += $row['current_delivery'];
						$sub_total_no_of_roll_fabric += $row['num_of_roll'];

						$sub_tot_qty += $row['current_delivery'];
						$sub_total_no_of_roll += $row['num_of_roll'];

						$i++;
						$grnd_total_no_of_roll+=$row['num_of_roll'];
						$grnd_tot_qty+=$row['current_delivery'];
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="20" style=" text-align:right;"><strong>Fabric Type Total</strong></td>
						<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll_fabric; ?></td>
						<td align="right"><strong><? echo number_format($sub_tot_qty_fabric,2); ?></strong></td>
					</tr>
					<?



				}
				?>
				<tr class="tbl_bottom">
					<td colspan="20" style=" text-align:right;"><strong>Reference Total</strong></td>
					<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll; ?></td>
					<td align="right"><strong><? echo number_format($sub_tot_qty,2); ?></strong></td>
				</tr>
				<?
			}
			$loc_nm = rtrim($loc_nm, ', ');
			?>
			<tr>
				<td align="right" colspan="20"><strong>Grand Total</strong></td>
				<td align="center" style="font-weight: bold;"><? echo $grnd_total_no_of_roll; ?></td>
				<td align="right"><strong><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></strong></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(125, $company, "1880px");?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess;
			var btype = 'code39';
			var renderer = 'bmp';

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code: value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
		document.getElementById('woner_comp_td').innerHTML = '<? echo $company_array[$ownner_comp]['name']; ?>';
	</script>
	<?
	exit();
}

if ($action == "grey_delivery_print_12")
{
	die;
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$knit_company = $data[6];
	$location_id= $data[7];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name,group_id,vat_number from lib_company");

	$group_com_arr_lib = return_library_array("select id,group_name from lib_group where is_deleted=0 and status_active=1 order by group_name", 'id', 'group_name');

	//select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name"
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['group_id'] = $row[csf('group_id')];
		$company_array[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
	}


	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$address_arr = return_library_array("select id, address from lib_location", "id", "address");
	$feeder_arr = array(1 => "Full Feeder", 2 => "Half Feeder");

	$mstData = sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");


	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$sql_mst_tbl=sql_select("select knitting_company,location_id,remarks,attention from pro_grey_prod_delivery_mst where id=$update_id");

	?>
	<div style="width:1880px;">
		<table width="1190" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td rowspan="5" colspan="2" valign="top">
					<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px"><? echo $group_com_arr_lib[$company_array[$company]['group_id']]; ?></strong><br>
					<?php /*?> <strong style="margin-right:300px"><? echo $company_array[$company]['name']; ?></strong><?php */?>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px">
					<strong style="margin-right:300px">Working Company: <? echo $knit_company. " (Location: ".$location_arr[$location_id]. ")"; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Working Company Add: <? echo $address_arr[$sql_mst_tbl[0][csf('location_id')]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px" >
					<strong style="margin-right:300px">Owner Company: <span id="woner_comp_td"></span></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Fabric Delivery Challan (Knitting)</strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1490" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="80">Delivery Date:</td>
				<td width="100"><? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:16px; font-weight:bold;" width="50">Vat No:</td>
				<td width="100"><? echo $company_array[$company]['vat_number']; ?></td>
				<td width="610" id="barcode_img_id" align="left"></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Remarks</td>
				<td colspan="2">:&nbsp;<? echo $sql_mst_tbl[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1685" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="60">Buyer</th>
					<th width="60">Job</th>
					<!--<th width="60">Style No</th>-->
					<th width="60">Ref No</th>
					<!--<th width="90">PO No</th>-->
					<th width="65">Prog No</th>
					<!--<th width="50">Requisition No</th>-->
					<th width="70">Book. No</th>
					<th width="100">Production Basis</th>
					<!--<th width="90">Production Date</th>--><!--new-->
					<th width="70">Knitting Company</th>
					<!--<th width="40">Shift</th>--><!--new-->
					<th width="100">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="80">Lot No</th>
					<th width="90">Fab Color</th>
					<th width="100">Color Range</th>
					<th width="100">Feeder</th>
					<th width="220">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. Dia</th>
					<th width="40">MC. Gauge</th>
					<th width="40">No Of Roll</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?
			$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
			from  pro_grey_prod_entry_dtls d,  inv_receive_master e
			where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
			$result_arr = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result_arr as $row) {
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}

			$i = 1;
			$grnd_total_no_of_roll=0;$grnd_tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			/*if ($kniting_source == 1)//in-house
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
			} else {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
			}*/

			$delivery_res = sql_select("select roll_id, sum(a.current_delivery) as current_delivery from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$update_id and a.status_active = 1 and a.is_deleted = 0 group by a.roll_id");
			foreach ($delivery_res as $val)
			{
				$roll_ids .= $val[csf("roll_id")].",";
				$qntyFromRoll[$val[csf("roll_id")]] = $val[csf("current_delivery")];
			}
			$roll_ids = chop($roll_ids,",");

			/*$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery
			 FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.id in ($roll_ids)
			 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales
			 order by c.po_breakdown_id,a.booking_no";*/


			 $sql = "select  a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company,
			 a.location_id, a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id,
			 b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg,
			 c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery,count(c.barcode_no) as num_of_roll
			 from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			 where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1
			 and b.is_deleted = 0 and c.id in ($roll_ids)
			 group by  a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.location_id,
			 a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales
			 order by c.po_breakdown_id,a.booking_no";


			//echo $sql;
			 $sql_result = sql_select($sql);
			 $po_id_array = $sales_id_array = $booking_program_arr = array();
			 foreach ($sql_result as $row) {
			 	if($row[csf("is_sales")] == 1){
			 		$sales_id_array[] = $row[csf("po_breakdown_id")];
			 	}else{
			 		$po_id_array[] = $row[csf("po_breakdown_id")];
			 	}
			 	if ($row[csf('receive_basis')] == 2) {
			 		$booking_program_arr[] = $row[csf("booking_no")];
			 	}else{
			 		$booking_no = explode("-", $row[csf('booking_no')]);
			 		$booking_program_arr[] = (int)$booking_no[3];
			 	}
			 }
			//print_r($booking_program_arr);
			 $planOrder = sql_select("select a.id,b.booking_no,b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
			 $plan_arr = array();
			 foreach ($planOrder as $plan_row) {
			 	$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
			 	$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
			 	$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
			 }

			 $job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
			 if(!empty($po_id_array)){
			 	$job_sql = "select a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
			 	$job_sql_result = sql_select($job_sql);
			 	foreach ($job_sql_result as $row) {
			 		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
			 		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
			 		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			 		$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
			 		$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			 		$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
			 	}
			 }

			 if(!empty($sales_id_array)){
			 	$sales_details = sql_select("select id,job_no,sales_booking_no,within_group,style_ref_no,buyer_id from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
			 	foreach ($sales_details as $sales_row) {
			 		$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
			 		$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
			 		$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
			 		$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
			 		$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
			 	}
			 }
			 $booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
			 $booking_details = sql_select("select a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 $booking_cond group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");
			 foreach ($booking_details as $booking_row) {
			 	$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
			 	$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
			 	$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
			 	$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
			 	$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
			 }
			 $reqs_array = array();
			 $reqs_sql = sql_select("select knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
			 foreach ($reqs_sql as $row) {
			 	$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			 }

			 $refno_data_array=array();
			/*echo "<pre>";
			print_r($sql_result);*/
			foreach ($sql_result as $row) {
				$is_sales = $row[csf('is_sales')];
				if($is_sales == 1){
					$within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
					if($within_group == 1){
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
						$job_no = $booking_arr[$booking_no]["job_no"];
						$po_id = $booking_arr[$booking_no]["po_break_down_id"];
						$style_ref_no = $job_array[$po_id]['style_ref_no'];
						$ref_no = $booking_arr[$po_id]["ref_no"];
						$buyer_id=$booking_arr[$booking_no]["buyer_id"];
					}else{
						$job_no = "";
						$style_ref_no = "";
						$ref_no = "";
						$po="";
						$buyer_id=$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id'];
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
					}
				}else{
					$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
					$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
					$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
					$buyer_id=$row[csf('buyer_id')];
					$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
				}
				$refno_data_array[$job_array[$row[csf('po_breakdown_id')]]['ref_no']][$row[csf('febric_description_id')]][]=array(
					recv_number_prefix_num=>$row[csf('recv_number_prefix_num')],
					buyer_id=>$buyer_id,
					ref_no=>$ref_no,
					receive_basis=>$row[csf('receive_basis')],
					booking_id=>$row[csf('booking_id')],
					booking_no=>$booking_no,
					knitting_source=>$row[csf('knitting_source')],
					knitting_company=>$row[csf('knitting_company')],
					location_id=>$row[csf('location_id')],
					febric_description_id=>$row[csf('febric_description_id')],
					gsm=>$row[csf('gsm')],
					width=>$row[csf('width')],
					yarn_count=>$row[csf('yarn_count')],
					yarn_lot=>$row[csf('yarn_lot')],
					color_id=>$row[csf('color_id')],
					color_range_id=>$row[csf('color_range_id')],
					machine_no_id=>$row[csf('machine_no_id')],
					stitch_length=>$row[csf('stitch_length')],
					brand_id=>$row[csf('brand_id')],
					shift_name=>$row[csf('shift_name')],
					machine_gg=>$row[csf('machine_gg')],
					machine_dia=>$row[csf('machine_dia')],
					num_of_roll=>$row[csf('num_of_roll')],
					no_of_roll=>$row[csf('no_of_roll')],
					po_breakdown_id=>$row[csf('po_breakdown_id')],
					current_delivery=>$row[csf('current_delivery')],
					bwo=>$row[csf('bwo')],
					booking_without_order=>$row[csf('booking_without_order')],
					within_group=>$row[csf('within_group')],
					is_sales=>$row[csf('is_sales')],
					receive_date=>$row[csf('receive_date')],
					job_no=>$job_no,
					style_ref_no=>$style_ref_no,
					po=>$po_id
				);//seq_no=>$row[csf('seq_no')],
			}

			$loc_arr = array();
			$loc_nm = ": ";
			$ownner_comp=" ";
			$k=1; $sub_group_arr=array();

			foreach($refno_data_array as $refArr){
				$sub_tot_qty = 0;
				$sub_total_no_of_roll=0;
				foreach ($refArr as $refDataArr) {
					$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
					foreach ($refDataArr as $row) {
						if ($loc_arr[$row['location_id']] == "") {
							$loc_arr[$row['location_id']] = $row['location_id'];
							$loc_nm .= $location_arr[$row['location_id']] . ', ';
						}
						$ownner_comp=$job_company_arr[$row['job_no']]['company_name'];
						$knit_company = "&nbsp;";
						if ($row["knitting_source"] == 1) {
							$knit_company = $company_array[$row["knitting_company"]]['shortname'];
						} else if ($row["knitting_source"] == 3) {
							$knit_company = $supplier_arr[$row["knitting_company"]];
						}

						$count = '';
						$yarn_count = explode(",", $row['yarn_count']);
						foreach ($yarn_count as $count_id) {
							if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
						}

						if ($row["booking_without_order"] != 1) {

						} else {

						}
						if ($row['receive_basis'] == 1) {
							$booking_no = explode("-", $row['booking_no']);
							$prog_book_no = (int)$booking_no[3];
						} else {
							$prog_book_no = $row['bwo'];
						}
						?>
						<tr>
							<td width="30"><? echo $i; ?></td>
							<?
							if ($row['receive_basis'] == 1) {
								?>
								<td width="90" style="word-break:break-all;"><? echo $buyer_array[$row['buyer_id']]; ?></td>
								<td width="60" style="word-break:break-all;"><? echo $row['bwo']; ?></td>
								<td width="95" style="word-break:break-all;"><? echo $row['job_no']; ?></td>
								<?
							} else {
								?>
								<td align="center" width="60" style="word-break:break-all;"><? echo $buyer_array[$row['buyer_id']]; ?></td>
								<td align="center" width="95" style="word-break:break-all;"><? echo $row['job_no']; ?></td>
								<td align="center" width="60" style="word-break:break-all;"><? echo $row['ref_no']; ?></td>
								<?
							}
							?>
							<td align="center" width="65" style="word-break:break-all;"><? echo $prog_book_no;  ?></td>

							<?
							if ($row['receive_basis'] == 2) {
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $plan_arr[$prog_book_no]["booking_no"]; ?></td>
								<?
							}
							else
							{
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $row['bwo']; ?></td>
								<?
							}
							?>
							<td align="center" width="100" style="word-break:break-all;"><? echo $receive_basis[$row['receive_basis']]; ?></td>
							<td align="center" width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
							<td align="center" width="100" style="word-break:break-all;"><? echo $count; ?></td>
							<td align="center" width="70"
							style="word-break:break-all;"><? echo $brand_details[$row['brand_id']]; ?></td>
							<td align="center" width="60" style="word-break:break-all;"><? echo $row['yarn_lot']; ?></td>
							<td align="center" width="70" style="word-break:break-all;">
								<?
								$color_id_arr = array_unique(explode(",", $row["color_id"]));
								$all_color_name = "";
								foreach ($color_id_arr as $c_id) {
									$all_color_name .= $color_arr[$c_id] . ",";
								}
								$all_color_name = chop($all_color_name, ",");
								echo $all_color_name;
								?>
							</td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $color_range[$row["color_range_id"]]; ?></td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $feeder_arr[$plan_arr[$prog_book_no]["feeder"]]; ?></td>
							<td width="220"
							style="word-break:break-all;"><? echo $composition_arr[$row['febric_description_id']]; ?></td>
							<td width="50" style="word-break:break-all;"
							align="center"><? echo $row['stitch_length']; ?></td>
							<td width="50" style="word-break:break-all;" align="center"><? echo $row['gsm']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['width']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_dia']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_gg']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><strong><? echo $row['num_of_roll']; ?></strong></td>
							<td style="word-break:break-all;" align="right"><strong><? echo $row['current_delivery']; ?></strong></td>
						</tr>
						<?
						$sub_tot_qty_fabric += $row['current_delivery'];
						$sub_total_no_of_roll_fabric += $row['num_of_roll'];

						$sub_tot_qty += $row['current_delivery'];
						$sub_total_no_of_roll += $row['num_of_roll'];

						$i++;
						$grnd_total_no_of_roll+=$row['num_of_roll'];
						$grnd_tot_qty+=$row['current_delivery'];
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="20" style=" text-align:right;"><strong>Fabric Type Total</strong></td>
						<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll_fabric; ?></td>
						<td align="right"><strong><? echo number_format($sub_tot_qty_fabric,2); ?></strong></td>
					</tr>
					<?



				}
				?>
				<tr class="tbl_bottom">
					<td colspan="20" style=" text-align:right;"><strong>Reference Total</strong></td>
					<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll; ?></td>
					<td align="right"><strong><? echo number_format($sub_tot_qty,2); ?></strong></td>
				</tr>
				<?
			}
			$loc_nm = rtrim($loc_nm, ', ');
			?>
			<tr>
				<td align="right" colspan="20"><strong>Grand Total</strong></td>
				<td align="center" style="font-weight: bold;"><? echo $grnd_total_no_of_roll; ?></td>
				<td align="right"><strong><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></strong></td>
			</tr>
		</table>
	</div>
	<? echo signature_table(125, $company, "1880px");?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess;
			var btype = 'code39';
			var renderer = 'bmp';

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code: value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
		document.getElementById('woner_comp_td').innerHTML = '<? echo $company_array[$ownner_comp]['name']; ?>';
	</script>
	<?
	exit();
}

if ($action == "grey_delivery_print_11-without_group_testingMode")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$knit_company = $data[6];
	$location_id= $data[7];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name,group_id,vat_number from lib_company");

	$group_com_arr_lib = return_library_array("select id,group_name from lib_group where is_deleted=0 and status_active=1 order by group_name", 'id', 'group_name');

	//select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name"
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['group_id'] = $row[csf('group_id')];
		$company_array[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
	}


	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$address_arr = return_library_array("select id, address from lib_location", "id", "address");
	$feeder_arr = array(1 => "Full Feeder", 2 => "Half Feeder");

	$mstData = sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");


	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}
	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$sql_mst_tbl=sql_select("select knitting_company,location_id,remarks,attention from pro_grey_prod_delivery_mst where id=$update_id");

	?>
	<div style="width:1880px;">
		<table width="1190" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td rowspan="5" colspan="2" valign="top">
					<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px"><? echo $group_com_arr_lib[$company_array[$company]['group_id']]; ?></strong><br>
					<?php /*?> <strong style="margin-right:300px"><? echo $company_array[$company]['name']; ?></strong><?php */?>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px">
					<strong style="margin-right:300px">Working Company: <? echo $knit_company. " (Location: ".$location_arr[$location_id]. ")"; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Working Company Add: <? echo $address_arr[$sql_mst_tbl[0][csf('location_id')]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px" >
					<strong style="margin-right:300px">Owner Company: <span id="woner_comp_td"></span></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Fabric Delivery Challan (Knitting)</strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1490" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="80">Delivery Date:</td>
				<td width="100"><? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:16px; font-weight:bold;" width="50">Vat No:</td>
				<td width="100"><? echo $company_array[$company]['vat_number']; ?></td>
				<td width="610" id="barcode_img_id" align="left"></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Remarks</td>
				<td colspan="2">:&nbsp;<? echo $sql_mst_tbl[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1685" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="60">Buyer</th>
					<th width="60">Job</th>
					<!--<th width="60">Style No</th>-->
					<th width="60">Ref No</th>
					<!--<th width="90">PO No</th>-->
					<th width="65">Prog No</th>
					<!--<th width="50">Requisition No</th>-->
					<th width="70">Book. No</th>
					<th width="100">Production Basis</th>
					<!--<th width="90">Production Date</th>--><!--new-->
					<th width="70">Knitting Company</th>
					<!--<th width="40">Shift</th>--><!--new-->
					<th width="100">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="80">Lot No</th>
					<th width="90">Fab Color</th>
					<th width="100">Color Range</th>
					<th width="100">Feeder</th>
					<th width="220">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. Dia</th>
					<th width="40">MC. Gauge</th>
					<th width="40">No Of Roll</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?
			$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
			from  pro_grey_prod_entry_dtls d,  inv_receive_master e
			where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
			$result_arr = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result_arr as $row) {
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}

			$i = 1;
			$grnd_total_no_of_roll=0;$grnd_tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			/*if ($kniting_source == 1)//in-house
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
			} else {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
			}*/

			$delivery_res = sql_select("select roll_id, sum(a.current_delivery) as current_delivery from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$update_id and a.status_active = 1 and a.is_deleted = 0 group by a.roll_id");
			foreach ($delivery_res as $val)
			{
				$roll_ids .= $val[csf("roll_id")].",";
				$qntyFromRoll[$val[csf("roll_id")]] = $val[csf("current_delivery")];
			}
			$roll_ids = chop($roll_ids,",");

			/*$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery
			 FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.id in ($roll_ids)
			 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales
			 order by c.po_breakdown_id,a.booking_no";*/


			 $sql = "select  a.buyer_id, a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company,
			 a.location_id, a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id,
			 b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery,count(c.barcode_no) as num_of_roll
			 from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			 where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1
			 and b.is_deleted = 0 and c.id in ($roll_ids)
			 group by  a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.location_id,
			 a.within_group, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length,b.brand_id,b.machine_dia,b.machine_gg , c.booking_no, c.booking_without_order,c.is_sales
			 order by a.booking_no";
			 $sql_result = sql_select($sql);

			 $booking_program_arr = array();
			 foreach ($sql_result as $row)
			 {
			 	if ($row[csf('receive_basis')] == 2) {
			 		$booking_program_arr[] = $row[csf("booking_no")];
			 	}else{
			 		$booking_no = explode("-", $row[csf('booking_no')]);
			 		$booking_program_arr[] = (int)$booking_no[3];
			 	}

			 }

	//print_r($booking_program_arr);
			 $planOrder = sql_select("select a.id,b.booking_no,b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
			 $plan_arr = array();
			 foreach ($planOrder as $plan_row) {
			 	$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
			 	$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
			 	$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
			 }
			 $booking_details = sql_select("select a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping ref_no from wo_booking_mst a,wo_booking_dtls b,wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping");

			 foreach ($booking_details as $booking_row) {
			 	$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
			 	$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
			 	$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
			 	$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
			 	$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];

			 	$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
			 }

			 foreach ($sql_result as $row)
			 {

			 	if ($row[csf('receive_basis')] == 1) {
			 		$booking_no = explode("-", $row[csf('booking_no')]);
			 		$prog_book_no = (int)$booking_no[3];
			 	} else {
			 		$prog_book_no = $row[csf('bwo')];
			 	}

		// knitting company
			 	$knit_company = "&nbsp;";
			 	if ($row[csf("knitting_source")] == 1) {
			 		$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
			 	} else if ($row[csf("knitting_source")] == 3) {
			 		$knit_company = $supplier_arr[$row[csf("knitting_company")]];
			 	}

		//yarn count
			 	$count = '';
			 	$yarn_count = explode(",", $row[csf('yarn_count')]);
			 	foreach ($yarn_count as $count_id) {
			 		if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
			 	}

			 	?>
			 	<tr>
			 		<td width="30"><? echo $i; ?></td>
			 		<?
			 		if ($row[csf('receive_basis')] == 1) {
			 			?>
			 			<td width="90" style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]; ?></td>
			 			<td width="60" style="word-break:break-all;"><? echo $row[csf('bwo')]; ?></td>
			 			<td width="95" style="word-break:break-all;"><? echo  $booking_arr[$row[csf('bwo')]]["job_no"];  ?></td>
			 			<?
			 		} else {
			 			?>
			 			<td align="center" width="60" style="word-break:break-all;"><? echo $buyer_array[$row[csf('buyer_id')]]; ?></td>
			 			<td align="center" width="95" style="word-break:break-all;"><?

			 			if($row[csf('receive_basis')] == 2)
			 			{
			 				echo  $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["job_no"];
			 			}
			 			else
			 			{
			 				echo  $booking_arr[$row[csf('bwo')]]["job_no"];
			 			}


			 			?></td>
			 			<td align="center" width="60" style="word-break:break-all;"><?

			 			if($row[csf('receive_basis')] == 2)
			 			{
			 				echo  $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["booking_ref_no"];
			 			}
			 			else
			 			{
			 				echo  $booking_arr[$row[csf('bwo')]]["booking_ref_no"];
			 			}

			 			?></td>
			 			<?
			 		}
			 		?>
			 		<td align="center" width="65" style="word-break:break-all;"><? echo $prog_book_no;  ?></td>

			 		<?
			 		if ($row[csf('receive_basis')] == 2) {
			 			?>
			 			<td align="center" width="130" style="word-break:break-all;"><? echo $plan_arr[$prog_book_no]["booking_no"]; ?></td>
			 			<?
			 		}
			 		else
			 		{
			 			?>
			 			<td align="center" width="130" style="word-break:break-all;"><? echo $row[csf('bwo')]; ?></td>
			 			<?
			 		}
			 		?>
			 		<td align="center" width="100" style="word-break:break-all;"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
			 		<td align="center" width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
			 		<td align="center" width="100" style="word-break:break-all;"><? echo $count; ?></td>
			 		<td align="center" width="70"
			 		style="word-break:break-all;"><? echo $brand_details[$row[csf('brand_id')]]; ?></td>
			 		<td align="center" width="60" style="word-break:break-all;"><? echo $row[csf('yarn_lot')]; ?></td>
			 		<td align="center" width="70" style="word-break:break-all;">
			 			<?
			 			$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
			 			$all_color_name = "";
			 			foreach ($color_id_arr as $c_id) {
			 				$all_color_name .= $color_arr[$c_id] . ",";
			 			}
			 			$all_color_name = chop($all_color_name, ",");
			 			echo $all_color_name;
			 			?>
			 		</td>
			 		<td align="center" width="100"
			 		style="word-break:break-all;"><? echo $color_range[$row[csf("color_range_id")]]; ?></td>
			 		<td align="center" width="100"
			 		style="word-break:break-all;"><? echo $feeder_arr[$plan_arr[$prog_book_no]["feeder"]]; ?></td>
			 		<td width="220"
			 		style="word-break:break-all;"><? echo $composition_arr[$row[csf('febric_description_id')]]; ?></td>
			 		<td width="50" style="word-break:break-all;"
			 		align="center"><? echo $row[csf('stitch_length')]; ?></td>
			 		<td width="50" style="word-break:break-all;" align="center"><? echo $row[csf('gsm')]; ?></td>
			 		<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('width')]; ?></td>
			 		<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('machine_dia')]; ?></td>
			 		<td width="40" style="word-break:break-all;" align="center"><? echo $row[csf('machine_gg')]; ?></td>
			 		<td width="40" style="word-break:break-all;" align="center"><strong><? echo $row[csf('num_of_roll')]; ?></strong></td>
			 		<td style="word-break:break-all;" align="right"><strong><? echo $row[csf('current_delivery')]; ?></strong></td>
			 	</tr>

			 	<?
			 	$i++;
			 	$grnd_total_no_of_roll+=$row[csf('num_of_roll')];
			 	$grnd_tot_qty+=$row[csf('current_delivery')];
			 }
			 ?>
			 <tr>
			 	<td align="right" colspan="20"><strong>Grand Total</strong></td>
			 	<td align="center" style="font-weight: bold;"><? echo $grnd_total_no_of_roll; ?></td>
			 	<td align="right"><strong><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></strong></td>
			 </tr>
			</table>
		</div>
		<? echo signature_table(125, $company, "1880px");?>
		<script type="text/javascript" src="../../js/jquery.js"></script>
		<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess) {
				var value = valuess;
				var btype = 'code39';
				var renderer = 'bmp';

				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 40,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				value = {code: value, rect: false};
				$("#barcode_img_id").show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $txt_challan_no; ?>');
			document.getElementById('woner_comp_td').innerHTML = '<? echo $company_array[$ownner_comp]['name']; ?>';
		</script>
		<?
		exit();
	}


if ($action == "grey_delivery_print_11")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$knit_company = $data[6];
	$location_id= $data[7];
	$show_val_column= $data[8];


	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name, group_id, vat_number from lib_company where status_active=1 and  is_deleted=0");

	$group_com_arr_lib = return_library_array("select id, group_name from lib_group where is_deleted=0 and status_active=1 order by group_name", 'id', 'group_name');

	//select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name"
	foreach ($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['group_id'] = $row[csf('group_id')];
		$company_array[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
	}

	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row)
	{
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$name_iso_Array=sql_select("select iso_no from lib_iso where company_id=$company and status_active=1 and module_id=7 and menu_id=551");

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$address_arr = return_library_array("select id, address from lib_location", "id", "address");
	$feeder_arr = array(1 => "Full Feeder", 2 => "Half Feeder");

	$mstData = sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row)
	{
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$sql_mst_tbl=sql_select("select knitting_company,location_id,remarks,attention from pro_grey_prod_delivery_mst where id=$update_id");

	?>
	<div style="width:1785px;">
		<table width="1785" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td rowspan="5" colspan="2" valign="top">
					<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px"><? echo $group_com_arr_lib[$company_array[$company]['group_id']]; ?></strong><br>
					<?php /*?> <strong style="margin-right:300px"><? echo $company_array[$company]['name']; ?></strong><?php */?>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px">
					<strong style="margin-right:300px">Working Company: <? echo $knit_company. " (Location: ".$location_arr[$location_id]. ")"; ?></strong>
				</td>
				<td><b><?= "ISO Number :".$name_iso_Array[0]["ISO_NO"];?></b></td>
				<td width="150"> </td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Working Company Add: <? echo $address_arr[$sql_mst_tbl[0][csf('location_id')]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px" >
					<strong style="margin-right:300px">Owner Company: <span id="woner_comp_td"></span></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Fabric Delivery Challan (Knitting)</strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1785" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="80">Delivery Date:</td>
				<td width="100"><? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:16px; font-weight:bold;" width="50">Vat No:</td>
				<td width="100"><? echo $company_array[$company]['vat_number']; ?></td>
				<td width="610" id="barcode_img_id" align="left"></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Remarks</td>
				<td colspan="2">:&nbsp;<? echo $sql_mst_tbl[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1785" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="60">Buyer</th>
					<th width="60">Job and Style Ref.</th>
					<th width="100">Order/FSO No</th>
					<!--<th width="60">Style No</th>-->
					<th width="60">Ref No</th>
					<!--<th width="90">PO No</th>-->
					<th width="65">Prog No</th>
					<!--<th width="50">Requisition No</th>-->
					<th width="70">Book. No</th>
					<th width="100">Production Basis</th>
					<!--<th width="90">Production Date</th>--><!--new-->
					<th width="70">Knitting Company</th>
					<!--<th width="40">Shift</th>--><!--new-->
					<th width="100">Yarn Count</th>
					<? if($show_val_column==1){?>
					<th width="70">Yarn Brand</th>
					<? } ?>
					<th width="80">Lot No</th>
					<th width="90">Fab Color</th>
					<th width="100">Color Range</th>
					<th width="100">Feeder</th>
					<th width="220">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. Dia</th>
					<th width="40">MC. Gauge</th>
					<th width="40">No Of Roll</th>
					<th width="40">Reject Qnty</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?
			$sql_dtls_knit = "SELECT e.booking_id, d.prod_id, d.machine_no_id, d.machine_dia, d.machine_gg
			    from  pro_grey_prod_entry_dtls d,  inv_receive_master e
			    where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
			$result_arr = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result_arr as $row)
			{
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}

			$i = 1;
			$grnd_total_no_of_roll=0;$grnd_tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

		    /*if ($kniting_source == 1)//in-house
		    {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
			    } else {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
		    }*/

			$delivery_res = sql_select("SELECT a.barcode_num, sum(a.current_delivery) as current_delivery from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$update_id and a.status_active = 1 and a.is_deleted = 0 group by a.barcode_num");
			foreach ($delivery_res as $val)
			{
				$barcode_nums .= $val[csf("barcode_num")].",";
				$qntyFromRoll[$val[csf("barcode_num")]] = $val[csf("current_delivery")];
			}
			$barcode_nums = chop($barcode_nums,",");

			$poIds=chop($poIds,',');  $barcode_cond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$barcode_cond=" and (";
				$barcodeArr=array_chunk(explode(",",$barcode_nums),999);
				foreach($barcodeArr as $barcode)
				{
					$barcode=implode(",",$barcode);
					$barcode_cond.=" c.barcode_no in($barcode) or ";
				}

				$barcode_cond=chop($barcode_cond,'or ');
				$barcode_cond.=")";
			}
			else
			{
				$barcode_cond=" and c.barcode_no in ($barcode_nums)";
			}

			/*$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery
			 FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.id in ($roll_ids)
			 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales
			 order by c.po_breakdown_id,a.booking_no";*/

			 //"select a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)"

			$sql = "SELECT  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery,count(c.barcode_no) as num_of_roll, sum(c.reject_qnty) as reject_qnty, c.po_breakdown_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 $barcode_cond
			group by  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no, c.booking_without_order, c.is_sales, c.po_breakdown_id
			order by a.booking_no";


		    //echo $sql;
		    $sql_result = sql_select($sql);
		    $feedar_prog_id="";
		    $po_id_array = $sales_id_array = $booking_program_arr = array();
		    foreach ($sql_result as $row)
		    {
				if($row[csf("is_sales")] == 1){
					$sales_id_array[] = $row[csf("po_breakdown_id")];
				}else{
					$po_id_array[] = $row[csf("po_breakdown_id")];
				}

				if ($row[csf('receive_basis')] == 2) {
					$booking_program_arr[] = $row[csf("booking_no")];
					$productionYarnCount[$row[csf("booking_no")]] = $row[csf("yarn_count")];
				}else{
					$booking_no = explode("-", $row[csf('booking_no')]);
					$booking_program_arr[] = (int)$booking_no[3];
				}

			    $feedar_prog_id .= $row[csf("bwo")].",";
		    }
			$feedar_prog_ids = chop($feedar_prog_id,",");
			//print_r($booking_program_arr);
			$planOrder = sql_select("SELECT a.id, b.booking_no, b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
			$plan_arr = array();
			foreach ($planOrder as $plan_row)
			{
				$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
				$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
				$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
			}

		    $job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
		    if(!empty($po_id_array))
		    {
			    $job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
			    // echo $job_sql;die;
			    $job_sql_result = sql_select($job_sql);
			    foreach ($job_sql_result as $row)
			    {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
					$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
					$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
			    }
		    }

		    if(!empty($sales_id_array))
		    {
			    $sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no, buyer_id from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
				foreach ($sales_details as $sales_row)
				{
					$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
					$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
					$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
					$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
					$sales_arr[$sales_row[csf('id')]]['sales_order_no'] = $sales_row[csf('job_no')];
					$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
				}
		    }
		    //$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
		    $booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping as ref_no,d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,d.style_ref_no");

		    foreach ($booking_details as $booking_row)
		    {
				$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
				$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
				$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
				$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["style_ref_no"] = $booking_row[csf("style_ref_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["internal_ref_no"] = $booking_row[csf("ref_no")];
		    }


			$non_booking_sql=sql_select("select a.buyer_id, a.booking_no, c.style_ref_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b left join sample_development_mst c on b.style_id=c.id where a.booking_no = b.booking_no and a.status_active=1 ");
			foreach ($non_booking_sql as $val)
			{
				$booking_arr[$val[csf("booking_no")]]["buyer_id"] = $val[csf("buyer_id")];
				//$booking_arr[$val[csf("booking_no")]]["style_ref_no"] = $val[csf("style_ref_no")];
			}
			unset($non_booking_sql);

			$reqs_array = array();
			$reqs_sql = sql_select("SELECT knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
			foreach ($reqs_sql as $row)
			{
				$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			}

			/* $ppl_count_feeder_sql = sql_select("select a.booking_no,c.count_id,c.feeding_id ,c.seq_no
				from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,ppl_planning_count_feed_dtls c
				where a.mst_id=b.mst_id and a.dtls_id=b.id

				and a.dtls_id=c.dtls_id and a.mst_id=c.mst_id and b.mst_id=c.mst_id and b.id=c.dtls_id

				and a.company_id=$company and b.id in($feedar_prog_ids)

				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.booking_no,c.count_id,c.feeding_id ,c.seq_no order by c.seq_no");*/


			$ppl_count_feeder_sql = sql_select("SELECT b.id as prog_no, c.count_id, c.feeding_id , c.seq_no
				from ppl_planning_info_entry_dtls b,ppl_planning_count_feed_dtls c
				where  b.mst_id=c.mst_id and b.id=c.dtls_id and b.id in($feedar_prog_ids)
				and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by c.count_id,c.feeding_id ,c.seq_no,b.id order by c.seq_no");
			$ppl_count_feeder_array=array();
			foreach ($ppl_count_feeder_sql as $row)
			{
				$feeder_count=strlen($feeding_arr[$row[csf('feeding_id')]]);
				if($row[csf('feeding_id')]==0){ $dividerSign= "";} else{ $dividerSign= "_";}
				$ppl_count_feeder_array[$row[csf('prog_no')]]['count_id'] .= substr($feeding_arr[$row[csf('feeding_id')]],-$feeder_count,1).$dividerSign.$yarn_count_details[$row[csf('count_id')]].',';
			}
			$refno_data_array=array();
		    /*echo "<pre>";
		    print_r($sql_result);*/
		    $ppl_feeding_id="";$ppl_count_id="";
		    foreach ($sql_result as $row)
		    {
			    $is_sales = $row[csf('is_sales')];
			    $booking_without_order = $row[csf('booking_without_order')];
			    if($is_sales == 1)
			    {
				    $within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
				    if($within_group == 1)
				    {
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
						$job_no = $booking_arr[$booking_no]["job_no"];
						$po_id = $booking_arr[$booking_no]["po_break_down_id"];
						//$style_ref_no = $job_array[$po_id]['style_ref_no'];
						$ref_no = $booking_arr[$po_id]["ref_no"];
						$buyer_id=$booking_arr[$booking_no]["buyer_id"];
						$style_ref_no = $sales_arr[$sales_row[csf('id')]]['style_ref_no'];
				    }
				    else
				    {
						$job_no = "";
						$style_ref_no = "";
						$ref_no = "";
						$po="";
						$buyer_id=$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id'];
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
						$style_ref_no = $sales_arr[$sales_row[csf('id')]]['style_ref_no'];
				    }
				    $order_no = $sales_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
			    }
			    else
			    {
			    	if ($booking_without_order==0)
			    	{
			    		$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
						$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
						$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
						$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
						$order_no=$job_array[$row[csf('po_breakdown_id')]]['po'];
						$buyer_id=$row[csf('buyer_id')];
						$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
			    	}
			    	else
			    	{
			    		$ref_no='';
						$job_no='';
						$style_ref_no='';
						$po='';
						$order_no='';
						$buyer_id=$row[csf('buyer_id')];
						$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
			    	}

			    }

			    $refno_data_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row[csf('febric_description_id')]][]=array(
			    //$refno_data_array[$job_array[$row[csf('po_breakdown_id')]]['ref_no']][$row[csf('febric_description_id')]][]=array(
				recv_number_prefix_num=>$row[csf('recv_number_prefix_num')],
				buyer_id=>$buyer_id,
				ref_no=>$ref_no,
				receive_basis=>$row[csf('receive_basis')],
				booking_id=>$row[csf('booking_id')],
				booking_no=>$booking_no,
				knitting_source=>$row[csf('knitting_source')],
				knitting_company=>$row[csf('knitting_company')],
				location_id=>$row[csf('location_id')],
				febric_description_id=>$row[csf('febric_description_id')],
				gsm=>$row[csf('gsm')],
				width=>$row[csf('width')],
				yarn_count=>$row[csf('yarn_count')],
				yarn_lot=>$row[csf('yarn_lot')],
				color_id=>$row[csf('color_id')],
				color_range_id=>$row[csf('color_range_id')],
				machine_no_id=>$row[csf('machine_no_id')],
				stitch_length=>$row[csf('stitch_length')],
				brand_id=>$row[csf('brand_id')],
				shift_name=>$row[csf('shift_name')],
				machine_gg=>$row[csf('machine_gg')],
				machine_dia=>$row[csf('machine_dia')],
				num_of_roll=>$row[csf('num_of_roll')],
				no_of_roll=>$row[csf('no_of_roll')],
				po_breakdown_id=>$row[csf('po_breakdown_id')],
				current_delivery=>$row[csf('current_delivery')],
				bwo=>$row[csf('bwo')],
				booking_without_order=>$row[csf('booking_without_order')],
				within_group=>$row[csf('within_group')],
				is_sales=>$row[csf('is_sales')],
				reject_qnty=>$row[csf('reject_qnty')],

				receive_date=>$row[csf('receive_date')],
				job_no=>$job_no,
				style_ref_no=>$style_ref_no,
				po=>$po_id,
				order_no=>$order_no
			    );//seq_no=>$row[csf('seq_no')],
		    }
			// echo "<pre>"; print_r($refno_data_array);
			//$address_arr = return_library_array("select id, address from lib_location", "id", "address");
			//select id, body_part_type from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)

			//$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
			$colarCupArr= sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(30,40,50)");
			foreach($colarCupArr as $row)
			{
				$body_part_data_arr[$row[csf('id')]]['body_part_full_name']=$row[csf('body_part_full_name')];
				$body_part_data_arr[$row[csf('id')]]['body_part_type']=$row[csf('body_part_type')];

			}

			// For Coller and Cuff data
			$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs
			    from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
			$sql_coller_cuff_result = sql_select($sql_coller_cuff);
			foreach ($sql_coller_cuff_result as $row2)
			{
				if($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type']>0 && $row2[csf('qc_pass_qnty_pcs')]>0)
				{
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
				}
			}
			//echo "<pre>";
			//print_r($coller_data_arr);//die;
			//print_r($cuff_data_arr);die;

		    //Without order booking
			$bookings_without_order="";
			foreach($refno_data_array as $refArr)
			{
				foreach ($refArr as $refDataArr)
				{
					foreach ($refDataArr as $row)
					{
						if ($row['booking_without_order']==1 && $row['receive_basis'] == 2)
						{
							$bookings_without_order.="'".$plan_arr[$row['bwo']]["booking_no"]."',";
						}
						if ($row['booking_without_order']==1 && $row['receive_basis'] != 2)
						{
							$bookings_without_order.="'".$row['bwo']."',";
						}
					}
				}
			}
			$bookings_without_order=chop($bookings_without_order,',');
	 		$non_order_booking_sql= sql_select("SELECT a.booking_no,a.buyer_id,a.grouping, b.style_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	 			where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_no in($bookings_without_order) group by  a.booking_no,a.buyer_id,a.grouping, b.style_id");
			foreach ($non_order_booking_sql as $row)
			{
			 	$style_id=$row[csf("style_id")];
			 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_id')];
			 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['grouping']=$row[csf('grouping')];
			 	//$nonOrderBookingData_arr[$row[csf('booking_no')]]['style_id']=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
			}
		    $nonOrderBookingStyle=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
		    // print_r($nonOrderBookingData_arr);
			$loc_arr = array();
			$loc_nm = ": ";
			$ownner_comp=" ";
			$k=1; $sub_group_arr=array();

			foreach($refno_data_array as $refArr)
			{
				$sub_tot_qty = 0;
				$sub_total_no_of_roll=0;
				foreach ($refArr as $refDataArr)
				{
					$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
					foreach ($refDataArr as $row)
					{
						if ($loc_arr[$row['location_id']] == "")
						{
							$loc_arr[$row['location_id']] = $row['location_id'];
							$loc_nm .= $location_arr[$row['location_id']] . ', ';
						}
						$ownner_comp=$job_company_arr[$row['job_no']]['company_name'];
						$knit_company = "&nbsp;";
						if ($row["knitting_source"] == 1) {
							$knit_company = $company_array[$row["knitting_company"]]['shortname'];
						} else if ($row["knitting_source"] == 3) {
							$knit_company = $supplier_arr[$row["knitting_company"]];
						}

						$count = '';
						$yarn_count = explode(",", $row['yarn_count']);
						foreach ($yarn_count as $count_id) {
							if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
						}
						if ($row['receive_basis'] == 1) {
							$booking_no = explode("-", $row['booking_no']);
							$prog_book_no = (int)$booking_no[3];
						} else {
							$prog_book_no = $row['bwo'];
						}
						if ($row['receive_basis'] == 2)
						{
							$ppl_count_ids="";
							$countID=explode(",", $productionYarnCount[$row['bwo']]);
							foreach ($countID as $count_ids) {
								$ppl_count_ids.=$yarn_count_details[$count_ids].",";
							}
							$ppl_count_id =chop($ppl_count_ids,',');

							/*$ppl_count_ids=$ppl_count_feeder_array[$row['bwo']]['count_id'];
							$ppl_count_id =chop($ppl_count_ids,',');*/
						}
						else if ($row['receive_basis'] == 1)
						{
							if ($row['booking_without_order'] == 1) {
								//$ppl_count_id =$yarn_count_details[$row['yarn_count']];
								$yarn_count = explode(",", $row['yarn_count']);
								$ppl_count_id="";
								foreach ($yarn_count as $count_id) {
									if ($ppl_count_id == '') $ppl_count_id = $yarn_count_details[$count_id]; else $ppl_count_id .= "," . $yarn_count_details[$count_id];
								}
							}
						}
						?>
						<tr>
							<td width="30"><? echo $i; ?></td>
							<?
							if ($row['receive_basis'] == 1)
							{
								?>
								<td width="90" style="word-break:break-all;"><?
								if($row['booking_without_order']==1)
								{
									echo $buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
								}
								else
								{
									echo $buyer_array[$row['buyer_id']];
								}
								?></td>

								<td width="60" style="word-break:break-all;"><?
								if($row['booking_without_order']==1)
								{
									echo "";
								}
								else
								{
									echo $booking_arr[$row['bwo']]["job_no"]."<br/>";//$row['bwo']."<br/>";
									echo $booking_arr[$row['bwo']]["style_ref_no"];
								}
								?></td>
								<td width="100" style="word-break:break-all;"><? echo $row['order_no']; ?></td>
								<td width="95" style="word-break:break-all;"><?
									if($row['booking_without_order']==1)
									{
										echo $nonOrderBookingData_arr[$row['bwo']]['grouping'];
									}
									else
									{
										echo $booking_arr[$row['bwo']]["internal_ref_no"];
										//$booking_arr[$row['bwo']]["job_no"];
									}
								 //echo $booking_arr[$row['bwo']]["booking_ref_no"];
								?></td>
								<?
							}
							else
							{
								?>
								<td align="center" width="60" title="Program:<? echo $row['bwo']; ?>" style="word-break:break-all;">
								<?
								if ($row['is_sales'] == 1)
								{
									echo $buyer_array[$row['buyer_id']];
								}
								else
								{
									if($row['booking_without_order']==1)
									{
										if($row['receive_basis'] == 2 && $row['is_sales'] == 2)
										{
											echo $buyer_array[$nonOrderBookingData_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
										}
										else
										{
											echo $buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
										}
									}
									else
									{
										echo $buyer_array[$booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
									}
								}
								?>
								</td>
								<td align="center" width="95" title="Booking:<?echo $plan_arr[$prog_book_no]["booking_no"].',==Praogram'.$row['bwo'];?>" style="word-break:break-all;"><?

								if($row['receive_basis'] == 2)
								{
									if ($row['is_sales'] == 2)
									{
										echo $nonOrderBookingStyle;
									}
									else
									{
										echo $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["job_no"]."<br/>";
										echo $row['style_ref_no'];
										//echo $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["style_ref_no"];
										//echo $plan_arr[$prog_book_no]["booking_no"];
									}
								}
								else
								{
									echo $booking_arr[$row['bwo']]["job_no"]."<br/>";
									echo $booking_arr[$row['bwo']]["style_ref_no"];
								}

								?></td>
								<td width="100" style="word-break:break-all;"><? echo $row['order_no']; ?></td>

								<td align="center" width="60" style="word-break:break-all;"><?

								if($row['receive_basis'] == 2)
								{
									echo  $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["booking_ref_no"];
								}
								else
								{
									echo  $booking_arr[$row['bwo']]["booking_ref_no"];
								}

								?></td>
								<?
							}
							?>
							<td align="center" width="65" style="word-break:break-all;"><? echo $prog_book_no;  ?></td>

							<?
							if ($row['receive_basis'] == 2) {
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $plan_arr[$prog_book_no]["booking_no"]; ?></td>
								<?
							}
							else
							{
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $row['bwo'];  ?></td>
								<?
							}
							?>
							<td align="center" width="100" style="word-break:break-all;"><? echo $receive_basis[$row['receive_basis']]; ?></td>
							<td align="center" width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
							<td align="center" width="100" style="word-break:break-all;"><? echo $ppl_count_id;//$count; ?></td>
							<? if($show_val_column==1){?>
							<td align="center" width="70"
							style="word-break:break-all;"><? echo $brand_details[$row['brand_id']]; ?></td>
							<? } ?>
							<td align="center" width="60" style="word-break:break-all;"><? echo $row['yarn_lot']; ?></td>
							<td align="center" width="70" style="word-break:break-all;">
								<?
								$color_id_arr = array_unique(explode(",", $row["color_id"]));
								$all_color_name = "";
								foreach ($color_id_arr as $c_id) {
									$all_color_name .= $color_arr[$c_id] . ",";
								}
								$all_color_name = chop($all_color_name, ",");
								echo $all_color_name;
								?>
							</td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $color_range[$row["color_range_id"]]; ?></td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $feeder_arr[$plan_arr[$prog_book_no]["feeder"]]; ?></td>
							<td width="220"
							style="word-break:break-all;"><? echo $composition_arr[$row['febric_description_id']]; ?></td>
							<td width="50" style="word-break:break-all;"
							align="center"><? echo $row['stitch_length']; ?></td>
							<td width="50" style="word-break:break-all;" align="center"><? echo $row['gsm']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['width']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_dia']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_gg']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><strong><? echo $row['num_of_roll']; ?></strong></td>
							<td width="40" style="word-break:break-all;" align="center"><strong><? echo $row['reject_qnty']; ?></strong></td>
							<td style="word-break:break-all;" align="right"><strong><? echo $row['current_delivery']; ?></strong></td>
						</tr>
						<?
						$sub_tot_qty_fabric += $row['current_delivery'];
						$sub_total_no_of_roll_fabric += $row['num_of_roll'];
						$sub_total_reject_qnty_fabric += $row['reject_qnty'];

						$sub_tot_qty += $row['current_delivery'];
						$sub_total_no_of_roll += $row['num_of_roll'];
						$sub_total_reject_qnty += $row['reject_qnty'];

						$i++;
						$grnd_total_no_of_roll+=$row['num_of_roll'];
						$grnd_total_reject_qnty+=$row['reject_qnty'];
						$grnd_tot_qty+=$row['current_delivery'];
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="<? echo ($show_val_column==1) ? 21 : 20;?>" style=" text-align:right;"><strong>Fabric Type Total</strong></td>
						<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll_fabric; ?></td>
						<td align="center" style="font-weight: bold;"><? echo number_format($sub_total_reject_qnty_fabric,2); ?></td>
						<td align="right"><strong><? echo number_format($sub_tot_qty_fabric,2); ?></strong></td>
					</tr>
					<?
				}
				?>
				<tr class="tbl_bottom">
					<td colspan="<? echo ($show_val_column==1) ? 21 : 20;?>" style=" text-align:right;"><strong>Reference Total</strong></td>
					<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll; ?></td>
					<td align="center" style="font-weight: bold;"><? echo number_format($sub_total_reject_qnty,2); ?></td>
					<td align="right"><strong><? echo number_format($sub_tot_qty,2); ?></strong></td>
				</tr>
				<?
			}
			$loc_nm = rtrim($loc_nm, ', ');
			?>
			<tr>
				<td align="right" colspan="<? echo ($show_val_column==1) ? 21 : 20;?>"><strong>Grand Total</strong></td>
				<td align="center" style="font-weight: bold;"><? echo $grnd_total_no_of_roll; ?></td>
				<td align="center" style="font-weight: bold;"><? echo number_format($grnd_total_reject_qnty, 2, '.', ''); ?></td>
				<td align="right"><strong><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></strong></td>
			</tr>
		</table>
	</div>
	<br>
	<div style="width:1685px;">
    	<?
    	//echo '<pre>';print_r($coller_cuff_data_arr);
		$CoCu=1;
		foreach($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr)
		{
			if( count($booking_data_arr)>0)
			{
			    //$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
			    ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left;">
                    <tr>
                        <th colspan="4"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name'];?> Details</th>
                    </tr>
                    <tr>
                        <th>Internal Ref. No</th>
                        <th>Fabric Booking No</th>
                        <th>Size</th>
                        <th>Qty Pcs</th>
                    </tr>
                    <?
                    $coller_cuff_qty_total=0;
                    foreach($booking_data_arr as $bookingId => $bookingData )
                    {
                        foreach($bookingData as $jobId => $jobData )
                        {
                            foreach($jobData as $size => $row )
                            {
                                ?>
                                <tr>
                                    <?
                                     if($row['receive_basis'] == 2)
                                     {
                                        ?>
                                        <td><?
                                        if($row['receive_basis'] == 2){
                                            echo  $booking_arr[$plan_arr[$bookingId]["booking_no"]]["booking_ref_no"];
                                        }else{
                                            echo  $booking_arr[$bookingId]["booking_ref_no"];
                                        }
                                        ?></td>
                                        <td><?  echo $plan_arr[$bookingId]["booking_no"]; ?></td>
                                        <?
                                     }
                                     else
                                     {
                                         ?>
                                        <td><? echo $booking_arr[$bookingId]["job_no"];?></td>
                                        <td><?  echo  $bookingId;  ?></td>
                                        <?
                                     }
                                    ?>
                                    <td align="center"><? echo $size;?></td>
                                    <td align="center"><? echo $row['qc_pass_qnty_pcs'];?></td>
                                </tr>
                                <?
                                $coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
                    </tr>
                </table>
			    <?
				if($CoCu==1){
					echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"4\">&nbsp;</td></tr></table>";
				}
				$CoCu++;
			}
		}
		?>
	</div>
	<? echo signature_table(125, $company, "1685px");?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess;
			var btype = 'code39';
			var renderer = 'bmp';

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code: value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
		document.getElementById('woner_comp_td').innerHTML = '<? echo $company_array[$company]['name']; ?>';
	</script>
	<?
	exit();
}

if ($action == "grey_delivery_print_21")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$knit_company = $data[6];
	$location_id= $data[7];
	$show_val_column= $data[8];


	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name, group_id, vat_number from lib_company where status_active=1 and  is_deleted=0");

	$group_com_arr_lib = return_library_array("select id, group_name from lib_group where is_deleted=0 and status_active=1 order by group_name", 'id', 'group_name');

	//select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name"
	foreach ($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['group_id'] = $row[csf('group_id')];
		$company_array[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
	}

	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row)
	{
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$name_iso_Array=sql_select("select iso_no from lib_iso where company_id=$company and status_active=1 and module_id=7 and menu_id=551");

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$address_arr = return_library_array("select id, address from lib_location", "id", "address");
	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$feeder_arr = array(1 => "Full Feeder", 2 => "Half Feeder");

	$mstData = sql_select("select company_id, delevery_date, knitting_source, knitting_company from pro_grey_prod_delivery_mst where id=$update_id");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row)
	{
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$sql_mst_tbl=sql_select("select knitting_company,location_id,remarks,attention from pro_grey_prod_delivery_mst where id=$update_id");

	?>
	<div style="width:1785px;">
		<table width="1785" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td rowspan="5" colspan="2" valign="top">
					<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px"><? echo $group_com_arr_lib[$company_array[$company]['group_id']]; ?></strong><br>
					<?php /*?> <strong style="margin-right:300px"><? echo $company_array[$company]['name']; ?></strong><?php */?>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px">
					<strong style="margin-right:300px">Working Company: <? echo $company_arr[$company]; ?></strong>
				</td>
				<td><b><?= "ISO Number :".$name_iso_Array[0]["ISO_NO"];?></b></td>
				<td width="150"> </td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Working Company Add: <? echo $address_arr[$sql_mst_tbl[0][csf('location_id')]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px" >
					<strong style="margin-right:300px">Owner Company: <span id="woner_comp_td"></span></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Fabric Delivery Challan (Knitting)</strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1785" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="80">Delivery Date:</td>
				<td width="100"><? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:16px; font-weight:bold;" width="50">Vat No:</td>
				<td width="100"><? echo $company_array[$company]['vat_number']; ?></td>
				<td width="610" id="barcode_img_id" align="left"></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Remarks</td>
				<td colspan="2">:&nbsp;<? echo $sql_mst_tbl[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1785" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="60">Buyer</th>
					<th width="60">Job and Style Ref.</th>
					<th width="100">Order/FSO No</th>
					<!--<th width="60">Style No</th>-->
					<th width="60">Ref No</th>
					<!--<th width="90">PO No</th>-->
					<th width="65">Prog No</th>
					<!--<th width="50">Requisition No</th>-->
					<th width="70">Book. No</th>
					<th width="100">Production Basis</th>
					<!--<th width="90">Production Date</th>--><!--new-->
					<!-- <th width="70">Knitting Company</th> -->
					<!--<th width="40">Shift</th>--><!--new-->
					<th width="100">Yarn Count</th>
					<? if($show_val_column==1){?>
					<th width="70">Yarn Brand</th>
					<? } ?>
					<th width="80">Lot No</th>
					<th width="90">Fab Color</th>
					<th width="100">Color Range</th>
					<th width="100">Feeder</th>
					<th width="220">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. Dia</th>
					<th width="40">MC. Gauge</th>
					<th width="40">No Of Roll</th>
					<th width="40">Reject Qnty</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?
			$sql_dtls_knit = "SELECT e.booking_id, d.prod_id, d.machine_no_id, d.machine_dia, d.machine_gg
			    from  pro_grey_prod_entry_dtls d,  inv_receive_master e
			    where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
			$result_arr = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result_arr as $row)
			{
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}

			$i = 1;
			$grnd_total_no_of_roll=0;$grnd_tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

		    /*if ($kniting_source == 1)//in-house
		    {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
			    } else {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
		    }*/

			$delivery_res = sql_select("SELECT a.barcode_num, sum(a.current_delivery) as current_delivery from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$update_id and a.status_active = 1 and a.is_deleted = 0 group by a.barcode_num");
			foreach ($delivery_res as $val)
			{
				$barcode_nums .= $val[csf("barcode_num")].",";
				$qntyFromRoll[$val[csf("barcode_num")]] = $val[csf("current_delivery")];
			}
			$barcode_nums = chop($barcode_nums,",");

			$poIds=chop($poIds,',');  $barcode_cond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$barcode_cond=" and (";
				$barcodeArr=array_chunk(explode(",",$barcode_nums),999);
				foreach($barcodeArr as $barcode)
				{
					$barcode=implode(",",$barcode);
					$barcode_cond.=" c.barcode_no in($barcode) or ";
				}

				$barcode_cond=chop($barcode_cond,'or ');
				$barcode_cond.=")";
			}
			else
			{
				$barcode_cond=" and c.barcode_no in ($barcode_nums)";
			}

			/*$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery
			 FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.id in ($roll_ids)
			 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales
			 order by c.po_breakdown_id,a.booking_no";*/

			 //"select a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)"

			$sql = "SELECT  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery,count(c.barcode_no) as num_of_roll, sum(c.reject_qnty) as reject_qnty, c.po_breakdown_id
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 $barcode_cond
			group by  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no, c.booking_without_order, c.is_sales, c.po_breakdown_id
			order by a.booking_no";


		    //echo $sql;
		    $sql_result = sql_select($sql);
		    $feedar_prog_id="";
		    $po_id_array = $sales_id_array = $booking_program_arr = array();
		    foreach ($sql_result as $row)
		    {
				if($row[csf("is_sales")] == 1){
					$sales_id_array[] = $row[csf("po_breakdown_id")];
				}else{
					$po_id_array[] = $row[csf("po_breakdown_id")];
				}

				if ($row[csf('receive_basis')] == 2) {
					$booking_program_arr[] = $row[csf("booking_no")];
					$productionYarnCount[$row[csf("booking_no")]] = $row[csf("yarn_count")];
				}else{
					$booking_no = explode("-", $row[csf('booking_no')]);
					$booking_program_arr[] = (int)$booking_no[3];
				}

			    $feedar_prog_id .= $row[csf("bwo")].",";
		    }
			$feedar_prog_ids = chop($feedar_prog_id,",");
			//print_r($booking_program_arr);
			$planOrder = sql_select("SELECT a.id, b.booking_no, b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
			$plan_arr = array();
			foreach ($planOrder as $plan_row)
			{
				$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
				$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
				$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
			}

		    $job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
		    if(!empty($po_id_array))
		    {
			    $job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
			    // echo $job_sql;die;
			    $job_sql_result = sql_select($job_sql);
			    foreach ($job_sql_result as $row)
			    {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
					$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
					$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
			    }
		    }

		    if(!empty($sales_id_array))
		    {
			    $sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no, buyer_id from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
				foreach ($sales_details as $sales_row)
				{
					$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
					$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
					$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
					$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
					$sales_arr[$sales_row[csf('id')]]['sales_order_no'] = $sales_row[csf('job_no')];
					$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
				}
		    }
		    //$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
		    $booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping as ref_no,d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,d.style_ref_no");

		    foreach ($booking_details as $booking_row)
		    {
				$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
				$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
				$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
				$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["style_ref_no"] = $booking_row[csf("style_ref_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["internal_ref_no"] = $booking_row[csf("ref_no")];
		    }


			$non_booking_sql=sql_select("select a.buyer_id, a.booking_no, c.style_ref_no from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b left join sample_development_mst c on b.style_id=c.id where a.booking_no = b.booking_no and a.status_active=1 ");
			foreach ($non_booking_sql as $val)
			{
				$booking_arr[$val[csf("booking_no")]]["buyer_id"] = $val[csf("buyer_id")];
				//$booking_arr[$val[csf("booking_no")]]["style_ref_no"] = $val[csf("style_ref_no")];
			}
			unset($non_booking_sql);

			$reqs_array = array();
			$reqs_sql = sql_select("SELECT knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
			foreach ($reqs_sql as $row)
			{
				$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			}

			/* $ppl_count_feeder_sql = sql_select("select a.booking_no,c.count_id,c.feeding_id ,c.seq_no
				from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,ppl_planning_count_feed_dtls c
				where a.mst_id=b.mst_id and a.dtls_id=b.id

				and a.dtls_id=c.dtls_id and a.mst_id=c.mst_id and b.mst_id=c.mst_id and b.id=c.dtls_id

				and a.company_id=$company and b.id in($feedar_prog_ids)

				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.booking_no,c.count_id,c.feeding_id ,c.seq_no order by c.seq_no");*/


			$ppl_count_feeder_sql = sql_select("SELECT b.id as prog_no, c.count_id, c.feeding_id , c.seq_no
				from ppl_planning_info_entry_dtls b,ppl_planning_count_feed_dtls c
				where  b.mst_id=c.mst_id and b.id=c.dtls_id and b.id in($feedar_prog_ids)
				and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by c.count_id,c.feeding_id ,c.seq_no,b.id order by c.seq_no");
			$ppl_count_feeder_array=array();
			foreach ($ppl_count_feeder_sql as $row)
			{
				$feeder_count=strlen($feeding_arr[$row[csf('feeding_id')]]);
				if($row[csf('feeding_id')]==0){ $dividerSign= "";} else{ $dividerSign= "_";}
				$ppl_count_feeder_array[$row[csf('prog_no')]]['count_id'] .= substr($feeding_arr[$row[csf('feeding_id')]],-$feeder_count,1).$dividerSign.$yarn_count_details[$row[csf('count_id')]].',';
			}
			$refno_data_array=array();
		    /*echo "<pre>";
		    print_r($sql_result);*/
		    $ppl_feeding_id="";$ppl_count_id="";
		    foreach ($sql_result as $row)
		    {
			    $is_sales = $row[csf('is_sales')];
			    $booking_without_order = $row[csf('booking_without_order')];
			    if($is_sales == 1)
			    {
				    $within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
				    if($within_group == 1)
				    {
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
						$job_no = $booking_arr[$booking_no]["job_no"];
						$po_id = $booking_arr[$booking_no]["po_break_down_id"];
						//$style_ref_no = $job_array[$po_id]['style_ref_no'];
						$ref_no = $booking_arr[$po_id]["ref_no"];
						$buyer_id=$booking_arr[$booking_no]["buyer_id"];
						$style_ref_no = $sales_arr[$sales_row[csf('id')]]['style_ref_no'];
				    }
				    else
				    {
						$job_no = "";
						$style_ref_no = "";
						$ref_no = "";
						$po="";
						$buyer_id=$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id'];
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
						$style_ref_no = $sales_arr[$sales_row[csf('id')]]['style_ref_no'];
				    }
				    $order_no = $sales_arr[$row[csf('po_breakdown_id')]]["sales_order_no"];
			    }
			    else
			    {
			    	if ($booking_without_order==0)
			    	{
			    		$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
						$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
						$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
						$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
						$order_no=$job_array[$row[csf('po_breakdown_id')]]['po'];
						$buyer_id=$row[csf('buyer_id')];
						$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
			    	}
			    	else
			    	{
			    		$ref_no='';
						$job_no='';
						$style_ref_no='';
						$po='';
						$order_no='';
						$buyer_id=$row[csf('buyer_id')];
						$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
			    	}

			    }

			    $refno_data_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row[csf('febric_description_id')]][]=array(
			    //$refno_data_array[$job_array[$row[csf('po_breakdown_id')]]['ref_no']][$row[csf('febric_description_id')]][]=array(
				recv_number_prefix_num=>$row[csf('recv_number_prefix_num')],
				buyer_id=>$buyer_id,
				ref_no=>$ref_no,
				receive_basis=>$row[csf('receive_basis')],
				booking_id=>$row[csf('booking_id')],
				booking_no=>$booking_no,
				knitting_source=>$row[csf('knitting_source')],
				knitting_company=>$row[csf('knitting_company')],
				location_id=>$row[csf('location_id')],
				febric_description_id=>$row[csf('febric_description_id')],
				gsm=>$row[csf('gsm')],
				width=>$row[csf('width')],
				yarn_count=>$row[csf('yarn_count')],
				yarn_lot=>$row[csf('yarn_lot')],
				color_id=>$row[csf('color_id')],
				color_range_id=>$row[csf('color_range_id')],
				machine_no_id=>$row[csf('machine_no_id')],
				stitch_length=>$row[csf('stitch_length')],
				brand_id=>$row[csf('brand_id')],
				shift_name=>$row[csf('shift_name')],
				machine_gg=>$row[csf('machine_gg')],
				machine_dia=>$row[csf('machine_dia')],
				num_of_roll=>$row[csf('num_of_roll')],
				no_of_roll=>$row[csf('no_of_roll')],
				po_breakdown_id=>$row[csf('po_breakdown_id')],
				current_delivery=>$row[csf('current_delivery')],
				bwo=>$row[csf('bwo')],
				booking_without_order=>$row[csf('booking_without_order')],
				within_group=>$row[csf('within_group')],
				is_sales=>$row[csf('is_sales')],
				reject_qnty=>$row[csf('reject_qnty')],

				receive_date=>$row[csf('receive_date')],
				job_no=>$job_no,
				style_ref_no=>$style_ref_no,
				po=>$po_id,
				order_no=>$order_no
			    );//seq_no=>$row[csf('seq_no')],
		    }
			// echo "<pre>"; print_r($refno_data_array);
			//$address_arr = return_library_array("select id, address from lib_location", "id", "address");
			//select id, body_part_type from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)

			//$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
			$colarCupArr= sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(30,40,50)");
			foreach($colarCupArr as $row)
			{
				$body_part_data_arr[$row[csf('id')]]['body_part_full_name']=$row[csf('body_part_full_name')];
				$body_part_data_arr[$row[csf('id')]]['body_part_type']=$row[csf('body_part_type')];

			}

			// For Coller and Cuff data
			$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs
			    from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
			$sql_coller_cuff_result = sql_select($sql_coller_cuff);
			foreach ($sql_coller_cuff_result as $row2)
			{
				if($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type']>0 && $row2[csf('qc_pass_qnty_pcs')]>0)
				{
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
				}
			}
			//echo "<pre>";
			//print_r($coller_data_arr);//die;
			//print_r($cuff_data_arr);die;

		    //Without order booking
			$bookings_without_order="";
			foreach($refno_data_array as $refArr)
			{
				foreach ($refArr as $refDataArr)
				{
					foreach ($refDataArr as $row)
					{
						if ($row['booking_without_order']==1 && $row['receive_basis'] == 2)
						{
							$bookings_without_order.="'".$plan_arr[$row['bwo']]["booking_no"]."',";
						}
						if ($row['booking_without_order']==1 && $row['receive_basis'] != 2)
						{
							$bookings_without_order.="'".$row['bwo']."',";
						}
					}
				}
			}
			$bookings_without_order=chop($bookings_without_order,',');
	 		$non_order_booking_sql= sql_select("SELECT a.booking_no,a.buyer_id,a.grouping, b.style_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	 			where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_no in($bookings_without_order) group by  a.booking_no,a.buyer_id,a.grouping, b.style_id");
			foreach ($non_order_booking_sql as $row)
			{
			 	$style_id=$row[csf("style_id")];
			 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_id')];
			 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['grouping']=$row[csf('grouping')];
			 	//$nonOrderBookingData_arr[$row[csf('booking_no')]]['style_id']=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
			}
		    $nonOrderBookingStyle=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
		    // print_r($nonOrderBookingData_arr);
			$loc_arr = array();
			$loc_nm = ": ";
			$ownner_comp=" ";
			$k=1; $sub_group_arr=array();

			foreach($refno_data_array as $refArr)
			{
				$sub_tot_qty = 0;
				$sub_total_no_of_roll=0;
				foreach ($refArr as $refDataArr)
				{
					$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
					foreach ($refDataArr as $row)
					{
						if ($loc_arr[$row['location_id']] == "")
						{
							$loc_arr[$row['location_id']] = $row['location_id'];
							$loc_nm .= $location_arr[$row['location_id']] . ', ';
						}
						$ownner_comp=$job_company_arr[$row['job_no']]['company_name'];
						$knit_company = "&nbsp;";
						if ($row["knitting_source"] == 1) {
							$knit_company = $company_array[$row["knitting_company"]]['shortname'];
						} else if ($row["knitting_source"] == 3) {
							$knit_company = $supplier_arr[$row["knitting_company"]];
						}

						$count = '';
						$yarn_count = explode(",", $row['yarn_count']);
						foreach ($yarn_count as $count_id) {
							if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
						}
						if ($row['receive_basis'] == 1) {
							$booking_no = explode("-", $row['booking_no']);
							$prog_book_no = (int)$booking_no[3];
						} else {
							$prog_book_no = $row['bwo'];
						}
						if ($row['receive_basis'] == 2)
						{
							$ppl_count_ids="";
							$countID=explode(",", $productionYarnCount[$row['bwo']]);
							foreach ($countID as $count_ids) {
								$ppl_count_ids.=$yarn_count_details[$count_ids].",";
							}
							$ppl_count_id =chop($ppl_count_ids,',');

							/*$ppl_count_ids=$ppl_count_feeder_array[$row['bwo']]['count_id'];
							$ppl_count_id =chop($ppl_count_ids,',');*/
						}
						else if ($row['receive_basis'] == 1)
						{
							if ($row['booking_without_order'] == 1) {
								//$ppl_count_id =$yarn_count_details[$row['yarn_count']];
								$yarn_count = explode(",", $row['yarn_count']);
								$ppl_count_id="";
								foreach ($yarn_count as $count_id) {
									if ($ppl_count_id == '') $ppl_count_id = $yarn_count_details[$count_id]; else $ppl_count_id .= "," . $yarn_count_details[$count_id];
								}
							}
						}
						?>
						<tr>
							<td width="30"><? echo $i; ?></td>
							<?
							if ($row['receive_basis'] == 1)
							{
								?>
								<td width="90" style="word-break:break-all;"><?
								if($row['booking_without_order']==1)
								{
									echo $buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
								}
								else
								{
									echo $buyer_array[$row['buyer_id']];
								}
								?></td>

								<td width="60" style="word-break:break-all;"><?
								if($row['booking_without_order']==1)
								{
									echo "";
								}
								else
								{
									echo $booking_arr[$row['bwo']]["job_no"]."<br/>";//$row['bwo']."<br/>";
									echo $booking_arr[$row['bwo']]["style_ref_no"];
								}
								?></td>
								<td width="100" style="word-break:break-all;"><? echo $row['order_no']; ?></td>
								<td width="95" style="word-break:break-all;"><?
									if($row['booking_without_order']==1)
									{
										echo $nonOrderBookingData_arr[$row['bwo']]['grouping'];
									}
									else
									{
										echo $booking_arr[$row['bwo']]["internal_ref_no"];
										//$booking_arr[$row['bwo']]["job_no"];
									}
								 //echo $booking_arr[$row['bwo']]["booking_ref_no"];
								?></td>
								<?
							}
							else
							{
								?>
								<td align="center" width="60" title="Program:<? echo $row['bwo']; ?>" style="word-break:break-all;">
								<?
								if ($row['is_sales'] == 1)
								{
									echo $buyer_array[$row['buyer_id']];
								}
								else
								{
									if($row['booking_without_order']==1)
									{
										if($row['receive_basis'] == 2 && $row['is_sales'] == 2)
										{
											echo $buyer_array[$nonOrderBookingData_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
										}
										else
										{
											echo $buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
										}
									}
									else
									{
										echo $buyer_array[$booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
									}
								}
								?>
								</td>
								<td align="center" width="95" title="Booking:<?echo $plan_arr[$prog_book_no]["booking_no"].',==Praogram'.$row['bwo'];?>" style="word-break:break-all;"><?

								if($row['receive_basis'] == 2)
								{
									if ($row['is_sales'] == 2)
									{
										echo $nonOrderBookingStyle;
									}
									else
									{
										echo $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["job_no"]."<br/>";
										echo $row['style_ref_no'];
										//echo $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["style_ref_no"];
										//echo $plan_arr[$prog_book_no]["booking_no"];
									}
								}
								else
								{
									echo $booking_arr[$row['bwo']]["job_no"]."<br/>";
									echo $booking_arr[$row['bwo']]["style_ref_no"];
								}

								?></td>
								<td width="100" style="word-break:break-all;"><? echo $row['order_no']; ?></td>

								<td align="center" width="60" style="word-break:break-all;"><?

								if($row['receive_basis'] == 2)
								{
									echo  $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["booking_ref_no"];
								}
								else
								{
									echo  $booking_arr[$row['bwo']]["booking_ref_no"];
								}

								?></td>
								<?
							}
							?>
							<td align="center" width="65" style="word-break:break-all;"><? echo $prog_book_no;  ?></td>

							<?
							if ($row['receive_basis'] == 2) {
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $plan_arr[$prog_book_no]["booking_no"]; ?></td>
								<?
							}
							else
							{
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $row['bwo'];  ?></td>
								<?
							}
							?>
							<td align="center" width="100" style="word-break:break-all;"><? echo $receive_basis[$row['receive_basis']]; ?></td>
							<!-- <td align="center" width="70" style="word-break:break-all;"><?// echo $knit_company; ?></td> -->
							<td align="center" width="100" style="word-break:break-all;"><? echo $ppl_count_id;//$count; ?></td>
							<? if($show_val_column==1){?>
							<td align="center" width="70"
							style="word-break:break-all;"><? echo $brand_details[$row['brand_id']]; ?></td>
							<? } ?>
							<td align="center" width="60" style="word-break:break-all;"><? echo $row['yarn_lot']; ?></td>
							<td align="center" width="70" style="word-break:break-all;">
								<?
								$color_id_arr = array_unique(explode(",", $row["color_id"]));
								$all_color_name = "";
								foreach ($color_id_arr as $c_id) {
									$all_color_name .= $color_arr[$c_id] . ",";
								}
								$all_color_name = chop($all_color_name, ",");
								echo $all_color_name;
								?>
							</td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $color_range[$row["color_range_id"]]; ?></td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $feeder_arr[$plan_arr[$prog_book_no]["feeder"]]; ?></td>
							<td width="220"
							style="word-break:break-all;"><? echo $composition_arr[$row['febric_description_id']]; ?></td>
							<td width="50" style="word-break:break-all;"
							align="center"><? echo $row['stitch_length']; ?></td>
							<td width="50" style="word-break:break-all;" align="center"><? echo $row['gsm']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['width']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_dia']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_gg']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><strong><? echo $row['num_of_roll']; ?></strong></td>
							<td width="40" style="word-break:break-all;" align="center"><strong><? echo $row['reject_qnty']; ?></strong></td>
							<td style="word-break:break-all;" align="right"><strong><? echo $row['current_delivery']; ?></strong></td>
						</tr>
						<?
						$sub_tot_qty_fabric += $row['current_delivery'];
						$sub_total_no_of_roll_fabric += $row['num_of_roll'];
						$sub_total_reject_qnty_fabric += $row['reject_qnty'];

						$sub_tot_qty += $row['current_delivery'];
						$sub_total_no_of_roll += $row['num_of_roll'];
						$sub_total_reject_qnty += $row['reject_qnty'];

						$i++;
						$grnd_total_no_of_roll+=$row['num_of_roll'];
						$grnd_total_reject_qnty+=$row['reject_qnty'];
						$grnd_tot_qty+=$row['current_delivery'];
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="<? echo ($show_val_column==1) ? 20 : 19;?>" style=" text-align:right;"><strong>Fabric Type Total</strong></td>
						<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll_fabric; ?></td>
						<td align="center" style="font-weight: bold;"><? echo number_format($sub_total_reject_qnty_fabric,2); ?></td>
						<td align="right"><strong><? echo number_format($sub_tot_qty_fabric,2); ?></strong></td>
					</tr>
					<?
				}
				?>
				<tr class="tbl_bottom">
					<td colspan="<? echo ($show_val_column==1) ? 20 : 19;?>" style=" text-align:right;"><strong>Reference Total</strong></td>
					<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll; ?></td>
					<td align="center" style="font-weight: bold;"><? echo number_format($sub_total_reject_qnty,2); ?></td>
					<td align="right"><strong><? echo number_format($sub_tot_qty,2); ?></strong></td>
				</tr>
				<?
			}
			$loc_nm = rtrim($loc_nm, ', ');
			?>
			<tr>
				<td align="right" colspan="<? echo ($show_val_column==1) ? 20 : 19;?>"><strong>Grand Total</strong></td>
				<td align="center" style="font-weight: bold;"><? echo $grnd_total_no_of_roll; ?></td>
				<td align="center" style="font-weight: bold;"><? echo number_format($grnd_total_reject_qnty, 2, '.', ''); ?></td>
				<td align="right"><strong><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></strong></td>
			</tr>
		</table>
	</div>
	<br>
	<div style="width:1685px;">
    	<?
    	//echo '<pre>';print_r($coller_cuff_data_arr);
		$CoCu=1;
		foreach($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr)
		{
			if( count($booking_data_arr)>0)
			{
			    //$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
			    ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left;">
                    <tr>
                        <th colspan="4"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name'];?> Details</th>
                    </tr>
                    <tr>
                        <th>Internal Ref. No</th>
                        <th>Fabric Booking No</th>
                        <th>Size</th>
                        <th>Qty Pcs</th>
                    </tr>
                    <?
                    $coller_cuff_qty_total=0;
                    foreach($booking_data_arr as $bookingId => $bookingData )
                    {
                        foreach($bookingData as $jobId => $jobData )
                        {
                            foreach($jobData as $size => $row )
                            {
                                ?>
                                <tr>
                                    <?
                                     if($row['receive_basis'] == 2)
                                     {
                                        ?>
                                        <td><?
                                        if($row['receive_basis'] == 2){
                                            echo  $booking_arr[$plan_arr[$bookingId]["booking_no"]]["booking_ref_no"];
                                        }else{
                                            echo  $booking_arr[$bookingId]["booking_ref_no"];
                                        }
                                        ?></td>
                                        <td><?  echo $plan_arr[$bookingId]["booking_no"]; ?></td>
                                        <?
                                     }
                                     else
                                     {
                                         ?>
                                        <td><? echo $booking_arr[$bookingId]["job_no"];?></td>
                                        <td><?  echo  $bookingId;  ?></td>
                                        <?
                                     }
                                    ?>
                                    <td align="center"><? echo $size;?></td>
                                    <td align="center"><? echo $row['qc_pass_qnty_pcs'];?></td>
                                </tr>
                                <?
                                $coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
                    </tr>
                </table>
			    <?
				if($CoCu==1){
					echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"4\">&nbsp;</td></tr></table>";
				}
				$CoCu++;
			}
		}
		?>
	</div>
	<? echo signature_table(125, $company, "1685px");?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess;
			var btype = 'code39';
			var renderer = 'bmp';

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code: value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
		document.getElementById('woner_comp_td').innerHTML = '<? echo $company_array[$company]['name']; ?>';
	</script>
	<?
	exit();
}

if ($action == "grey_delivery_print_15") // Print 10
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$knit_company = $data[6];
	$location_id= $data[7];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name, group_id, vat_number from lib_company where status_active=1 and  is_deleted=0");

	$group_com_arr_lib = return_library_array("select id, group_name from lib_group where is_deleted=0 and status_active=1 order by group_name", 'id', 'group_name');

	//select group_name,id from lib_group where is_deleted=0 and status_active=1 order by group_name", "id,group_name"
	foreach ($company_data as $row)
	{
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
		$company_array[$row[csf('id')]]['group_id'] = $row[csf('group_id')];
		$company_array[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
	}

	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row)
	{
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$machine_details_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");
	$address_arr = return_library_array("select id, address from lib_location", "id", "address");
	$feeder_arr = array(1 => "Full Feeder", 2 => "Half Feeder");

	$mstData = sql_select("select company_id, delevery_date, knitting_source, knitting_company,inserted_by,insert_date from pro_grey_prod_delivery_mst where id=$update_id");

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row)
	{
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
	}

	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$sql_mst_tbl=sql_select("select knitting_company,location_id,remarks,attention from pro_grey_prod_delivery_mst where id=$update_id");

	?>
	<div style="width:1685px;">
		<table width="1685" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td rowspan="5" colspan="2" valign="top">
					<img src="../../<? echo $image_location; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px"><? echo $group_com_arr_lib[$company_array[$company]['group_id']]; ?></strong><br>
					<?php /*?> <strong style="margin-right:300px"><? echo $company_array[$company]['name']; ?></strong><?php */?>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:18px">
					<strong style="margin-right:300px">Working Company: <? echo $knit_company. " (Location: ".$location_arr[$location_id]. ")"; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Working Company Add: <? echo $address_arr[$sql_mst_tbl[0][csf('location_id')]]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px" >
					<strong style="margin-right:300px">Owner Company: <span id="woner_comp_td"></span></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px">Fabric Delivery Challan (Knitting)</strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1685" cellspacing="0" align="center" border="0" style="float:left;">
			<tr>
				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:16px; font-weight:bold;" width="80">Delivery Date:</td>
				<td width="100"><? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:16px; font-weight:bold;" width="50">Vat No:</td>
				<td width="100"><? echo $company_array[$company]['vat_number']; ?></td>
				<td width="610" id="barcode_img_id" align="left"></td>
			</tr>
			<tr>
				<td style="font-size:16px; font-weight:bold;">Remarks</td>
				<td colspan="2">:&nbsp;<? echo $sql_mst_tbl[0][csf('remarks')]; ?></td>
			</tr>
		</table>
		<br>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1685" class="rpt_table">
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="60">Buyer</th>
					<th width="110">Job and Style Ref.</th>
					<!--<th width="60">Style No</th>-->
					<th width="160">Textile Ref. No</th>
					<!--<th width="90">PO No</th>-->
					<th width="65">Prog No</th>
					<!--<th width="50">Requisition No</th>-->
					<th width="70">Book. No</th>
					<th width="100">Production Basis</th>
					<!--<th width="90">Production Date</th>--><!--new-->
					<th width="70">Knitting Company</th>
					<!--<th width="40">Shift</th>--><!--new-->
					<th width="100">Yarn Count</th>
					<th width="70">Yarn Brand</th>
					<th width="80">Lot No</th>
					<th width="90">Fab Color</th>
					<th width="100">Color Range</th>
					<th width="100">Feeder</th>
					<th width="220">Fabric Type</th>
					<th width="50">Stich</th>
					<th width="50">Fin GSM</th>
					<th width="40">Fab. Dia</th>
					<th width="40">MC. Dia</th>
					<th width="40">MC. Gauge</th>
					<th width="40">No Of Roll</th>
					<th width="40">Reject Qnty</th>
					<th>QC Pass Qty</th>
				</tr>
			</thead>
			<?
			$sql_dtls_knit = "SELECT e.booking_id, d.prod_id, d.machine_no_id, d.machine_dia, d.machine_gg
			    from  pro_grey_prod_entry_dtls d,  inv_receive_master e
			    where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
			$result_arr = sql_select($sql_dtls_knit);
			$machine_dia_guage_arr = array();
			foreach ($result_arr as $row)
			{
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
			}

			$i = 1;
			$grnd_total_no_of_roll=0;$grnd_tot_qty = 0;
			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

		    /*if ($kniting_source == 1)//in-house
		    {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
			    } else {
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as bwo, c.booking_without_order,c.is_sales FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales order by c.po_breakdown_id,a.booking_no";
		    }*/

			$delivery_res = sql_select("SELECT a.barcode_num, sum(a.current_delivery) as current_delivery from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$update_id and a.status_active = 1 and a.is_deleted = 0 group by a.barcode_num");
			foreach ($delivery_res as $val)
			{
				$barcode_nums .= $val[csf("barcode_num")].",";
				$qntyFromRoll[$val[csf("barcode_num")]] = $val[csf("current_delivery")];
			}
			$barcode_nums = chop($barcode_nums,",");

			$poIds=chop($poIds,',');  $barcode_cond="";
			if($db_type==2 && $tot_rows>1000)
			{
				$barcode_cond=" and (";
				$barcodeArr=array_chunk(explode(",",$barcode_nums),999);
				foreach($barcodeArr as $barcode)
				{
					$barcode=implode(",",$barcode);
					$barcode_cond.=" c.barcode_no in($barcode) or ";
				}

				$barcode_cond=chop($barcode_cond,'or ');
				$barcode_cond.=")";
			}
			else
			{
				$barcode_cond=" and c.barcode_no in ($barcode_nums)";
			}

			/*$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,count(c.barcode_no) as num_of_roll,b.no_of_roll, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery
			 FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			 WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.id in ($roll_ids)
			 group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, a.within_group, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.machine_dia,b.machine_gg,b.no_of_roll, c.po_breakdown_id , c.booking_no, c.booking_without_order,c.is_sales
			 order by c.po_breakdown_id,a.booking_no";*/

			 //"select a.id,a.challan_no,a.service_booking_no,a.company_id,a.recv_number,a.receive_basis,a.receive_date, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, a.buyer_id, b.id as dtls_id, b.prod_id, b.febric_description_id, b.gsm, b.width, c.barcode_no, c.id as roll_id, c.roll_no, c.po_breakdown_id, c.qnty, c.reject_qnty, c.booking_no as bwo, c.booking_without_order,c.is_sales,b.color_id from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 and c.barcode_no in($data)"

			$sql = "SELECT  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales, c.po_breakdown_id, sum(c.qnty) as current_delivery,count(c.barcode_no) as num_of_roll, sum(c.reject_qnty) as reject_qnty
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 $barcode_cond
			group by  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no, c.booking_without_order, c.is_sales, c.po_breakdown_id
			order by a.booking_no";


		    //echo $sql;
		    $sql_result = sql_select($sql);
		    $feedar_prog_id="";
		    $po_id_array = $sales_id_array = $booking_program_arr = array();
		    foreach ($sql_result as $row)
		    {
				if($row[csf("is_sales")] == 1){
					$sales_id_array[] = $row[csf("po_breakdown_id")];
				}else{
					$po_id_array[] = $row[csf("po_breakdown_id")];
				}

				if ($row[csf('receive_basis')] == 2) {
					$booking_program_arr[] = $row[csf("booking_no")];
					$productionYarnCount[$row[csf("booking_no")]] = $row[csf("yarn_count")];
				}else{
					$booking_no = explode("-", $row[csf('booking_no')]);
					$booking_program_arr[] = (int)$booking_no[3];
				}

			    $feedar_prog_id .= $row[csf("bwo")].",";
		    }
			$feedar_prog_ids = chop($feedar_prog_id,",");
			//print_r($booking_program_arr);
			// echo "<pre>";print_r($sales_id_array);die;

			$planOrder = sql_select("SELECT a.id, b.booking_no, b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
			$plan_arr = array();
			foreach ($planOrder as $plan_row)
			{
				$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
				$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
				$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
			}

		    $job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
		    if(!empty($po_id_array))
		    {
			    $job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
			    $job_sql_result = sql_select($job_sql);
			    foreach ($job_sql_result as $row)
			    {
					$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
					$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
					$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
					$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
					$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
					$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
			    }
		    }

		    if(!empty($sales_id_array))
		    {
			    $sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no, buyer_id, customer_buyer from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
			    // echo "SELECT id, job_no, sales_booking_no, within_group, style_ref_no, buyer_id from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")";
				foreach ($sales_details as $sales_row)
				{
					$sales_arr[$sales_row[csf('id')]]['fso_no'] = $sales_row[csf('job_no')];
					$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
					$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
					$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
					$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
					$sales_arr[$sales_row[csf('id')]]['customer_buyer'] = $sales_row[csf('customer_buyer')];
					$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
				}
		    }
		    //$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
		    $booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping as ref_no,d.style_ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,d.style_ref_no");

		    foreach ($booking_details as $booking_row)
		    {
				$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
				$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
				$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
				$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["style_ref_no"] = $booking_row[csf("style_ref_no")];
				$booking_arr[$booking_row[csf("booking_no")]]["internal_ref_no"] = $booking_row[csf("ref_no")];
		    }
			$reqs_array = array();
			$reqs_sql = sql_select("SELECT knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
			foreach ($reqs_sql as $row)
			{
				$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			}

			/* $ppl_count_feeder_sql = sql_select("select a.booking_no,c.count_id,c.feeding_id ,c.seq_no
				from ppl_planning_entry_plan_dtls a,ppl_planning_info_entry_dtls b,ppl_planning_count_feed_dtls c
				where a.mst_id=b.mst_id and a.dtls_id=b.id

				and a.dtls_id=c.dtls_id and a.mst_id=c.mst_id and b.mst_id=c.mst_id and b.id=c.dtls_id

				and a.company_id=$company and b.id in($feedar_prog_ids)

				and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by a.booking_no,c.count_id,c.feeding_id ,c.seq_no order by c.seq_no");*/


			$ppl_count_feeder_sql = sql_select("SELECT b.id as prog_no, c.count_id, c.feeding_id , c.seq_no
				from ppl_planning_info_entry_dtls b,ppl_planning_count_feed_dtls c
				where  b.mst_id=c.mst_id and b.id=c.dtls_id and b.id in($feedar_prog_ids)
				and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by c.count_id,c.feeding_id ,c.seq_no,b.id order by c.seq_no");
			$ppl_count_feeder_array=array();
			foreach ($ppl_count_feeder_sql as $row)
			{
				$feeder_count=strlen($feeding_arr[$row[csf('feeding_id')]]);
				if($row[csf('feeding_id')]==0){ $dividerSign= "";} else{ $dividerSign= "_";}
				$ppl_count_feeder_array[$row[csf('prog_no')]]['count_id'] .= substr($feeding_arr[$row[csf('feeding_id')]],-$feeder_count,1).$dividerSign.$yarn_count_details[$row[csf('count_id')]].',';
			}
			$refno_data_array=array();
		    /*echo "<pre>";
		    print_r($sql_result);*/
		    $ppl_feeding_id="";$ppl_count_id="";
		    foreach ($sql_result as $row)
		    {
			    $is_sales = $row[csf('is_sales')];
			    if($is_sales == 1)
			    {
				    $within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
				    if($within_group == 1)
				    {
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
						$job_no = $booking_arr[$booking_no]["job_no"];
						$po_id = $booking_arr[$booking_no]["po_break_down_id"];
						$style_ref_no = $job_array[$po_id]['style_ref_no'];
						$ref_no = $booking_arr[$po_id]["ref_no"];
						// $buyer_id=$booking_arr[$booking_no]["buyer_id"];
				    }
				    else
				    {
						$job_no = "";
						$style_ref_no = "";
						$ref_no = "";
						$po="";
						// $buyer_id=$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id'];
						$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
				    }
				    $buyer_id=$sales_arr[$row[csf('po_breakdown_id')]]['customer_buyer'];
			    }
			    else
			    {
					$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
					$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
					$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
					$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
					$buyer_id=$row[csf('buyer_id')];
					$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
			    }

			    $refno_data_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row[csf('febric_description_id')]][]=array(
			    //$refno_data_array[$job_array[$row[csf('po_breakdown_id')]]['ref_no']][$row[csf('febric_description_id')]][]=array(
				recv_number_prefix_num=>$row[csf('recv_number_prefix_num')],
				buyer_id=>$buyer_id,
				ref_no=>$ref_no,
				receive_basis=>$row[csf('receive_basis')],
				booking_id=>$row[csf('booking_id')],
				booking_no=>$booking_no,
				knitting_source=>$row[csf('knitting_source')],
				knitting_company=>$row[csf('knitting_company')],
				location_id=>$row[csf('location_id')],
				febric_description_id=>$row[csf('febric_description_id')],
				gsm=>$row[csf('gsm')],
				width=>$row[csf('width')],
				yarn_count=>$row[csf('yarn_count')],
				yarn_lot=>$row[csf('yarn_lot')],
				color_id=>$row[csf('color_id')],
				color_range_id=>$row[csf('color_range_id')],
				machine_no_id=>$row[csf('machine_no_id')],
				stitch_length=>$row[csf('stitch_length')],
				brand_id=>$row[csf('brand_id')],
				shift_name=>$row[csf('shift_name')],
				machine_gg=>$row[csf('machine_gg')],
				machine_dia=>$row[csf('machine_dia')],
				num_of_roll=>$row[csf('num_of_roll')],
				no_of_roll=>$row[csf('no_of_roll')],
				po_breakdown_id=>$row[csf('po_breakdown_id')],
				current_delivery=>$row[csf('current_delivery')],
				bwo=>$row[csf('bwo')],
				booking_without_order=>$row[csf('booking_without_order')],
				within_group=>$row[csf('within_group')],
				is_sales=>$row[csf('is_sales')],
				reject_qnty=>$row[csf('reject_qnty')],
				receive_date=>$row[csf('receive_date')],
				job_no=>$job_no,
				style_ref_no=>$style_ref_no,
				po=>$po_id
			    );//seq_no=>$row[csf('seq_no')],
		    }
			//print_r($refno_data_array);
			//$address_arr = return_library_array("select id, address from lib_location", "id", "address");
			//select id, body_part_type from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)

			//$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
			$colarCupArr= sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
			foreach($colarCupArr as $row)
			{
				$body_part_data_arr[$row[csf('id')]]['body_part_full_name']=$row[csf('body_part_full_name')];
				$body_part_data_arr[$row[csf('id')]]['body_part_type']=$row[csf('body_part_type')];

			}

			// For Coller and Cuff data
			$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs, c.qc_pass_qnty
			    from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
			$sql_coller_cuff_result = sql_select($sql_coller_cuff);
			foreach ($sql_coller_cuff_result as $row2)
			{
				if($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type']>0 && $row2[csf('qc_pass_qnty_pcs')]>0)
				{
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty'] += $row2[csf('qc_pass_qnty')];
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['no_of_roll']++;
					$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
				}
			}
			//echo "<pre>";
			//print_r($coller_data_arr);//die; inserted_by,insert_date
			//print_r($cuff_data_arr);die;

		    //Without order booking
			$bookings_without_order="";
			foreach($refno_data_array as $refArr)
			{
				foreach ($refArr as $refDataArr)
				{
					foreach ($refDataArr as $row)
					{
						if ($row['booking_without_order']==1 && $row['receive_basis'] == 2)
						{
							$bookings_without_order.="'".$plan_arr[$row['bwo']]["booking_no"]."',";
						}
						if ($row['booking_without_order']==1 && $row['receive_basis'] != 2)
						{
							$bookings_without_order.="'".$row['bwo']."',";
						}
					}
				}
			}
			$bookings_without_order=chop($bookings_without_order,',');
	 		$non_order_booking_sql= sql_select("SELECT a.booking_no,a.buyer_id,a.grouping, b.style_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
	 			where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_no in($bookings_without_order) group by  a.booking_no,a.buyer_id,a.grouping, b.style_id");
			foreach ($non_order_booking_sql as $row)
			{
			 	$style_id=$row[csf("style_id")];
			 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_id')];
			 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['grouping']=$row[csf('grouping')];
			 	//$nonOrderBookingData_arr[$row[csf('booking_no')]]['style_id']=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
			}
		    $nonOrderBookingStyle=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
		    // print_r($nonOrderBookingData_arr);
			$loc_arr = array();
			$loc_nm = ": ";
			$ownner_comp=" ";
			$k=1; $sub_group_arr=array();

			foreach($refno_data_array as $refArr)
			{
				$sub_tot_qty = 0;
				$sub_total_no_of_roll=0;
				foreach ($refArr as $refDataArr)
				{
					$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
					foreach ($refDataArr as $row)
					{
						if ($loc_arr[$row['location_id']] == "")
						{
							$loc_arr[$row['location_id']] = $row['location_id'];
							$loc_nm .= $location_arr[$row['location_id']] . ', ';
						}
						$ownner_comp=$job_company_arr[$row['job_no']]['company_name'];
						$knit_company = "&nbsp;";
						if ($row["knitting_source"] == 1) {
							$knit_company = $company_array[$row["knitting_company"]]['shortname'];
						} else if ($row["knitting_source"] == 3) {
							$knit_company = $supplier_arr[$row["knitting_company"]];
						}

						$count = '';
						$yarn_count = explode(",", $row['yarn_count']);
						foreach ($yarn_count as $count_id) {
							if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
						}
						if ($row['receive_basis'] == 1) {
							$booking_no = explode("-", $row['booking_no']);
							$prog_book_no = (int)$booking_no[3];
						} else {
							$prog_book_no = $row['bwo'];
						}
						if ($row['receive_basis'] == 2)
						{
							$ppl_count_ids="";
							$countID=explode(",", $productionYarnCount[$row['bwo']]);
							foreach ($countID as $count_ids) {
								$ppl_count_ids.=$yarn_count_details[$count_ids].",";
							}
							$ppl_count_id =chop($ppl_count_ids,',');

							/*$ppl_count_ids=$ppl_count_feeder_array[$row['bwo']]['count_id'];
							$ppl_count_id =chop($ppl_count_ids,',');*/
						}
						else if ($row['receive_basis'] == 1)
						{
							if ($row['booking_without_order'] == 1) {
								//$ppl_count_id =$yarn_count_details[$row['yarn_count']];
								$yarn_count = explode(",", $row['yarn_count']);
								$ppl_count_id="";
								foreach ($yarn_count as $count_id) {
									if ($ppl_count_id == '') $ppl_count_id = $yarn_count_details[$count_id]; else $ppl_count_id .= "," . $yarn_count_details[$count_id];
								}
							}
						}
						?>
						<tr>
							<td width="30"><? echo $i; ?></td>
							<?
							if ($row['receive_basis'] == 1)
							{
								?>
								<td width="90" style="word-break:break-all;"><?
								if($row['booking_without_order']==1)
								{
									echo $buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
								}
								else
								{
									echo $buyer_array[$row['buyer_id']];
								}
								?></td>

								<td width="60" style="word-break:break-all;"><?
								if($row['booking_without_order']==1)
								{
									echo "";
								}
								else
								{
									echo $booking_arr[$row['bwo']]["job_no"]."<br/>";//$row['bwo']."<br/>";
									echo $booking_arr[$row['bwo']]["style_ref_no"];
								}
								?></td>

								<td width="160" style="word-break:break-all;"><?
									/*if($row['booking_without_order']==1)
									{
										echo $nonOrderBookingData_arr[$row['bwo']]['grouping'];
									}
									else
									{
										echo $booking_arr[$row['bwo']]["internal_ref_no"];
									}*/
									echo $sales_arr[$row['po_breakdown_id']]['fso_no'];
								?></td>
								<?
							}
							else
							{
								?>
								<td align="center" width="60" title="Program:<? echo $row['bwo']; ?>" style="word-break:break-all;">
								<?
								if ($row['is_sales'] == 1)
								{
									echo $buyer_array[$row['buyer_id']];
								}
								else
								{
									if($row['booking_without_order']==1)
									{
										if($row['receive_basis'] == 2 && $row['is_sales'] == 2)
										{
											echo $buyer_array[$nonOrderBookingData_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
										}
										else
										{
											echo $buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
										}
									}
									else
									{
										echo $buyer_array[$booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
									}
								}
								?>
								</td>
								<td align="center" width="95" title="Booking:<?echo $plan_arr[$prog_book_no]["booking_no"].',==Praogram'.$row['bwo'];?>" style="word-break:break-all;"><?

								if($row['receive_basis'] == 2)
								{
									if ($row['is_sales'] == 2)
									{
										echo $nonOrderBookingStyle;
									}
									else
									{
										echo  $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["job_no"]."<br/>";
										echo  $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["style_ref_no"];
									}
								}
								else
								{
									echo  $booking_arr[$row['bwo']]["job_no"]."<br/>";
									echo  $booking_arr[$row['bwo']]["style_ref_no"];
								}

								?></td>
								<td align="center" width="160" style="word-break:break-all;"><?

								/*if($row['receive_basis'] == 2)
								{
									echo  $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["booking_ref_no"];
								}
								else
								{
									echo  $booking_arr[$row['bwo']]["booking_ref_no"];
								}*/
								echo $sales_arr[$row['po_breakdown_id']]['fso_no'];
								?></td>
								<?
							}
							?>
							<td align="center" width="65" style="word-break:break-all;"><? echo $prog_book_no;  ?></td>

							<?
							if ($row['receive_basis'] == 2) {
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $plan_arr[$prog_book_no]["booking_no"]; ?></td>
								<?
							}
							else
							{
								?>
								<td align="center" width="130" style="word-break:break-all;"><? echo $row['bwo'];  ?></td>
								<?
							}
							?>
							<td align="center" width="100" style="word-break:break-all;"><? echo $receive_basis[$row['receive_basis']]; ?></td>
							<td align="center" width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
							<td align="center" width="100" style="word-break:break-all;"><? echo $ppl_count_id;//$count; ?></td>
							<td align="center" width="70"
							style="word-break:break-all;"><? echo $brand_details[$row['brand_id']]; ?></td>
							<td align="center" width="60" style="word-break:break-all;"><? echo $row['yarn_lot']; ?></td>
							<td align="center" width="70" style="word-break:break-all;">
								<?
								$color_id_arr = array_unique(explode(",", $row["color_id"]));
								$all_color_name = "";
								foreach ($color_id_arr as $c_id) {
									$all_color_name .= $color_arr[$c_id] . ",";
								}
								$all_color_name = chop($all_color_name, ",");
								echo $all_color_name;
								?>
							</td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $color_range[$row["color_range_id"]]; ?></td>
							<td align="center" width="100"
							style="word-break:break-all;"><? echo $feeder_arr[$plan_arr[$prog_book_no]["feeder"]]; ?></td>
							<td width="220"
							style="word-break:break-all;"><? echo $composition_arr[$row['febric_description_id']]; ?></td>
							<td width="50" style="word-break:break-all;"
							align="center"><? echo $row['stitch_length']; ?></td>
							<td width="50" style="word-break:break-all;" align="center"><? echo $row['gsm']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['width']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_dia']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><? echo $row['machine_gg']; ?></td>
							<td width="40" style="word-break:break-all;" align="center"><strong><? echo $row['num_of_roll']; ?></strong></td>
							<td width="40" style="word-break:break-all;" align="center"><strong><? echo number_format($row['reject_qnty'],2); ?></strong></td>
							<td style="word-break:break-all;" align="right"><strong><? echo number_format($row['current_delivery'],2); ?></strong></td>
						</tr>
						<?
						$sub_tot_qty_fabric += $row['current_delivery'];
						$sub_total_no_of_roll_fabric += $row['num_of_roll'];
						$sub_total_reject_qnty_fabric += $row['reject_qnty'];

						$sub_tot_qty += $row['current_delivery'];
						$sub_total_no_of_roll += $row['num_of_roll'];
						$sub_total_reject_qnty += $row['reject_qnty'];

						$i++;
						$grnd_total_no_of_roll+=$row['num_of_roll'];
						$grnd_total_reject_qnty+=$row['reject_qnty'];
						$grnd_tot_qty+=$row['current_delivery'];
					}
					?>
					<tr class="tbl_bottom">
						<td colspan="20" style=" text-align:right;"><strong>Fabric Type Total</strong></td>
						<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll_fabric; ?></td>
						<td align="center" style="font-weight: bold;"><? echo number_format($sub_total_reject_qnty_fabric,2); ?></td>
						<td align="right"><strong><? echo number_format($sub_tot_qty_fabric,2); ?></strong></td>
					</tr>
					<?
				}
				?>
				<tr class="tbl_bottom">
					<td colspan="20" style=" text-align:right;"><strong>Reference Total</strong></td>
					<td align="center" style="font-weight: bold;"><? echo $sub_total_no_of_roll; ?></td>
					<td align="center" style="font-weight: bold;"><? echo number_format($sub_total_reject_qnty,2); ?></td>
					<td align="right"><strong><? echo number_format($sub_tot_qty,2); ?></strong></td>
				</tr>
				<?
			}
			$loc_nm = rtrim($loc_nm, ', ');
			?>
			<tr>
				<td align="right" colspan="20"><strong>Grand Total</strong></td>
				<td align="center" style="font-weight: bold;"><? echo $grnd_total_no_of_roll; ?></td>
				<td align="center" style="font-weight: bold;"><? echo number_format($grnd_total_reject_qnty, 2, '.', ''); ?></td>
				<td align="right"><strong><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></strong></td>
			</tr>
		</table>
	</div>
	<br>
	<div style="width:1685px;">
    	<?
    	// echo '<pre>';print_r($coller_cuff_data_arr);
		$CoCu=1;
		foreach($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr)
		{
			if( count($booking_data_arr)>0)
			{
			    //$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
			    ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left;">
                    <tr>
                        <th colspan="4"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name'];?> Details</th>
                    </tr>
                    <tr>
                        <th>Internal Ref. No</th>
                        <th>Fabric Booking No</th>
                        <th>Size</th>
                        <th>Qty Pcs</th>
                        <th>Qty KG</th>
                        <th>Roll Qty</th>
                    </tr>
                    <?
                    $coller_cuff_qty_total=0;$qc_pass_qnty_total=0;$no_of_roll_total=0;
                    foreach($booking_data_arr as $bookingId => $bookingData )
                    {
                        foreach($bookingData as $jobId => $jobData )
                        {
                            foreach($jobData as $size => $row )
                            {
                                ?>
                                <tr>
                                    <?
                                     if($row['receive_basis'] == 2)
                                     {
                                        ?>
                                        <td><?
                                        if($row['receive_basis'] == 2){
                                            echo  $booking_arr[$plan_arr[$bookingId]["booking_no"]]["booking_ref_no"];
                                        }else{
                                            echo  $booking_arr[$bookingId]["booking_ref_no"];
                                        }
                                        ?></td>
                                        <td><?  echo $plan_arr[$bookingId]["booking_no"]; ?></td>
                                        <?
                                     }
                                     else
                                     {
                                         ?>
                                        <td><? echo $booking_arr[$bookingId]["job_no"];?></td>
                                        <td><?  echo  $bookingId;  ?></td>
                                        <?
                                     }
                                    ?>
                                    <td align="center"><? echo $size;?></td>
                                    <td align="center"><? echo $row['qc_pass_qnty_pcs'];?></td>
                                    <td align="center"><? echo $row['qc_pass_qnty'];?></td>
                                    <td align="center"><? echo $row['no_of_roll'];?></td>
                                </tr>
                                <?
                                $coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
                                $qc_pass_qnty_total += $row['qc_pass_qnty'];
                                $no_of_roll_total += $row['no_of_roll'];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
                        <td align="center"><b><? echo $qc_pass_qnty_total; ?></b></td>
                        <td align="center"><b><? echo $no_of_roll_total; ?></b></td>
                    </tr>
                </table>
			    <?
				if($CoCu==1){
					echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"4\">&nbsp;</td></tr></table>";
				}
				$CoCu++;
			}
		}
		?>
	</div>
<!-- //inserted_by,insert_date -->
	<?php
	$inserted_by = $mstData[0]['INSERTED_BY'];
	$mstData[0]['INSERT_DATE'];
	$sql = "SELECT a.id, a.user_full_name, a.designation, b.id as desig_id, b.custom_designation FROM user_passwd a, lib_designation b WHERE a.designation= b.id";
	$user_res = sql_select($sql);
	$user_arr = array();
	foreach($user_res as $row)
	{
		$user_arr[$row['ID']]['name'] = $row['USER_FULL_NAME']; 
		$user_arr[$row['ID']]['custom_designation'] = $row['CUSTOM_DESIGNATION']; 
	} 
	$userDtlsArr=array(); 
	$userDtlsArr[$mstData[0]['INSERTED_BY']] = "<div><b>".$user_arr[$mstData[0]['INSERTED_BY']]['name']."</b></div><div><b>".$user_arr[$mstData[0]['INSERTED_BY']]['custom_designation']."</b></div><div><small>".$mstData[0]['INSERT_DATE']."</small></div>";
	echo get_app_signature(125, $company, "1685px",'', '', $inserted_by, $userDtlsArr); 
	?>

	<? // echo signature_table(125, $company, "1685px");?>

	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess) {
			var value = valuess;
			var btype = 'code39';
			var renderer = 'bmp';

			var settings = {
				output: renderer,
				bgColor: '#FFFFFF',
				color: '#000000',
				barWidth: 1,
				barHeight: 40,
				moduleSize: 5,
				posX: 10,
				posY: 20,
				addQuietZone: 1
			};
			value = {code: value, rect: false};
			$("#barcode_img_id").show().barcode(value, btype, settings);
		}
		generateBarcode('<? echo $txt_challan_no; ?>');
		document.getElementById('woner_comp_td').innerHTML = '<? echo $company_array[$company]['name']; ?>';
	</script>
	<?
	exit();
}

if ($action=="grey_delivery_print_7") // Rehan for Barnali
{
 	extract($_REQUEST);
 	$data = explode('*', $data);

 	$company = $data[0];
 	$txt_challan_no = $data[1];
 	$update_id = $data[2];
 	$kniting_source = $data[4];
 	$floor_name = $data[5];
 	$organicyesno = $data[6];
	
 	$company_array = array();
 	$company_data = sql_select("SELECT id, company_name, company_short_name from lib_company");
 	foreach ($company_data as $row)
 	{
 		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
 		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
 	}

 	$machine_details = array();
 	$machine_sql = sql_select("SELECT id, machine_no, dia_width, gauge from lib_machine_name");
 	foreach ($machine_sql as $row)
 	{
 		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
 		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
 		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
 	}

 	$color_arr = return_library_array("SELECT id, color_name from lib_color", 'id', 'color_name');
 	$supplier_arr = return_library_array("SELECT id, short_name from lib_supplier", "id", "short_name");
 	$buyer_array = return_library_array("SELECT id, short_name from lib_buyer", "id", "short_name");
 	$yarn_count_details = return_library_array("SELECT id,yarn_count from lib_yarn_count", "id", "yarn_count");
 	$machine_details_arr = return_library_array("SELECT id, machine_no from lib_machine_name", "id", "machine_no");
 	$brand_details = return_library_array("SELECT id, brand_name from lib_brand", "id", "brand_name");
 	$location_arr = return_library_array("SELECT id, location_name from lib_location", "id", "location_name");
 	
	
 	$smn_booking_style_arr=return_library_array( "select booking_no, style_ref_no from wo_non_ord_samp_booking_dtls a, sample_development_mst b where a.style_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by booking_no, style_ref_no", "booking_no", "style_ref_no");

 	$mstData = sql_select("SELECT company_id, delevery_date, knitting_source, knitting_company,location_id, remarks from pro_grey_prod_delivery_mst where id=$update_id");

 	$job_array = array();
 	//$job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
 	$job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no, a.style_ref_no
 	from wo_po_details_master a, wo_po_break_down b
 	where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
 	$job_sql_result = sql_select($job_sql);
 	foreach ($job_sql_result as $row)
 	{
 		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
 		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
 		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
 		$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
 		$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];

 		$job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
 		$job_array[$row[csf('id')]]['booking_no'] = $row[csf('id')];
		
 	}
 	//print_r($job_array);
	
 	$composition_arr = array();
 	$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
 	$data_array = sql_select($sql_deter);
 	foreach ($data_array as $row)
 	{
 		if (array_key_exists($row[csf('id')], $composition_arr))
 		{
 			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
 		}
 		else
 		{
 			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
 		}
 	}

 	$yarn_lot_arr = array();
 	$yarn_lot_sql=sql_select("SELECT id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id,yarn_type,supplier_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0");
 	foreach ($yarn_lot_sql as $value)
 	{
 		if($value[csf('yarn_comp_percent2nd')])
 		{
 			$production_wise_yarn_dtls[$value[csf('id')]]=$yarn_count_details[$value[csf('yarn_count_id')]].','.$value[csf('lot')].", ".$composition[$value[csf('yarn_comp_type1st')]].' '.$value[csf('yarn_comp_percent1st')].'%'." ".$composition[$value[csf('yarn_comp_type2nd')]]. ", " . $value[csf('yarn_comp_percent2nd')] . "%" .', '.$yarn_type[$value[csf('yarn_type')]].", ".$supplier_arr[$value[csf("supplier_id")]];
 		}
 		else
 		{
 			$production_wise_yarn_dtls[$value[csf('id')]]=$yarn_count_details[$value[csf('yarn_count_id')]].','.$value[csf('lot')].", ".$composition[$value[csf('yarn_comp_type1st')]].' '.$value[csf('yarn_comp_percent1st')].'%'.', '.$yarn_type[$value[csf('yarn_type')]].", ".$supplier_arr[$value[csf("supplier_id")]];
 		}
 	}


 	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
 	$com_dtls = fnc_company_location_address($company, $location, 2);


 	?>
 	<div style="width:1820px;">
 		<table width="1290" cellspacing="0" border="0">
 			<tr>
 				<td rowspan="3" width="10">
 					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
 				</td>
 				<td align="center" style="font-size:x-large">
 					<strong ><? echo $com_dtls[0]; ?></strong>
					 <!-- style="margin-right:300px" -->
 				</td>

 				<td>
					<?php
					if($organicyesno==1)
					{
						?>
						<div style="border: 2px solid #000; padding: 0px; color: #000;float: right; margin-right: 10px;  width: 235px; text-align: center;">ORGANIC</div>
						<?
					}else{
						echo "&nbsp;";
					}
					?>
				</td>
 			</tr>
 			<tr>
				<td align="center" style="font-size:14px">
					<strong ><? echo $com_dtls[1]; ?></strong>
					<!-- style="margin-right:400px" -->
				</td>
			</tr>
 			<tr>
 				<td align="center" style="font-size:18px">
 					<strong ><u>Delivery Challan</u></strong>
					 <!-- style="margin-right:300px" -->
 				</td>
 			</tr>
 			<tr>
 				<td align="center" style="font-size:16px">
 					<strong style="margin-right:300px"><u>Knitting Section</u></strong>
 				</td>
 			</tr>
 		</table>
 		<br>
 		<table width="1290" cellspacing="0" align="center" border="0">
 			<tr>
 				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
 				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
 				<td style="font-size:16px; font-weight:bold;" width="60">Location</td>
 				<td width="270" id="location_td" >:&nbsp;<? echo $location_arr[$mstData[0][csf('location_id')]]; ?></td>
				<td></td>
 				<td width="710" id="barcode_img_id" align="right"></td>
 			</tr>
 			<tr>
 				<td style="font-size:16px; font-weight:bold;">Delivery Date</td>
 				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
 				<td style="font-size:16px; font-weight:bold;">Floor No.</td>
 				<td  style="width:150px">:&nbsp;<? echo $floor_name; ?></td>
				<td style="font-size:16px; font-weight:bold;" colspan="2">Remarks:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>

 			</tr>
 		</table>
 		<br>
 		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1810" class="rpt_table">
 			<thead>
 				<tr>
 					<th width="30" style="word-break: break-all;word-wrap: break-word;">SL</th>
 					<th width="80" style="word-break: break-all;word-wrap: break-word;">Production Date</th>
 					<th width="40" style="word-break: break-all;word-wrap: break-word;">Shift</th>
 					<th width="55" style="word-break: break-all;word-wrap: break-word;">Prod.ID</th>
 					<th width="80" style="word-break: break-all;word-wrap: break-word;">Buyer</th>
 					<th width="60" style="word-break: break-all;word-wrap: break-word;">Program No</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Order No/Booking No</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Body Part</th>
 					<th width="100" style="word-break: break-all;word-wrap: break-word;">Style / Internal ref.</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Knit. Comp.</th>
 					<th width="170" style="word-break: break-all;word-wrap: break-word;">Yarn Details</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Fab Color</th>
 					<th width="150" style="word-break: break-all;word-wrap: break-word;">Fabric Type</th>
 					<th width="35" style="word-break: break-all;word-wrap: break-word;">Stich</th>
 					<th width="35" style="word-break: break-all;word-wrap: break-word;">GSM</th>
 					<th width="65" style="word-break: break-all;word-wrap: break-word;">Dia X GG</th>
 					<th width="60" style="word-break: break-all;word-wrap: break-word;">Fab. Dia</th>
 					<th width="100" style="word-break: break-all;word-wrap: break-word;">MC. No</th>
 					<th width="45" style="word-break: break-all;word-wrap: break-word;">No of Roll</th>
 					<th width="50" style="word-break: break-all;word-wrap: break-word;">Qnty. in Pcs</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Deliv. QTY</th>
 				</tr>
 			</thead>
 			<?
 			$sql_dtls_knit = "SELECT e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
 			from  pro_grey_prod_entry_dtls d,  inv_receive_master e
 			where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
 			$result_arr = sql_select($sql_dtls_knit);
 			$machine_dia_guage_arr = array();
 			foreach ($result_arr as $row) {
 				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
 				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
 			}

 			$i = 0;
 			$tot_qty = 0;
 			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			if ($kniting_source == 1)//in-house
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as booking, c.booking_without_order,b.body_part_id,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs,e.seq_no,c.barcode_no , b.machine_dia, b.machine_gg
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e
				WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0
				group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.po_breakdown_id , c.booking_no, c.booking_without_order,b.body_part_id,e.seq_no,c.barcode_no, b.machine_dia, b.machine_gg order by e.seq_no";
			}
			else
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as booking, c.booking_without_order,b.body_part_id,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs,c.barcode_no , b.machine_dia,b.machine_gg
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d
				WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0
				group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.po_breakdown_id , c.booking_no, c.booking_without_order,b.body_part_id,c.barcode_no, b.machine_dia,b.machine_gg";
			}
			//echo $sql;
			$result = sql_select($sql);
			$all_barcode_no="";$orderIds=array();
			foreach($result as $row)
			{
				$all_barcode_no.=$row[csf("barcode_no")].",";
				$orderIds[$row[csf('po_breakdown_id')]]= $row[csf('po_breakdown_id')];

			}
			$orderIds_cond=" and po_break_down_id in (".implode(",",$orderIds).")";
			$order_booking_arr = return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where booking_type !=2 $orderIds_cond", "po_break_down_id", "booking_no");

			$all_barcode_no=implode(",",array_unique(explode(",",chop($all_barcode_no,","))));
			if($all_barcode_no!="")
			{
				$production_sql=sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis,a.knitting_source, a.knitting_company  from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

				$production_data_arr=array();
				foreach($production_sql as $row)
				{
					$production_data_arr[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
					$production_data_arr[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
					if($row[csf("receive_basis")]==2)
					{
						$all_program_no.=$row[csf("booking_id")].",";
					}
				}
			}
			$all_program_no=implode(",",array_unique(explode(",",chop($all_program_no,","))));
			if($all_program_no!="")
			{
				$program_sql=sql_select("select a.booking_id, c.booking_no from inv_receive_master a,  ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c
					where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");
				$prog_book_arr=array(); $prog_full_book_arr=array();
				foreach($program_sql as $row)
				{
					$prog_full_book_arr[$row[csf("booking_id")]]=$row[csf("booking_no")];
				}
			}


			$loc_arr = array();
			$loc_nm = ": ";
			$style_po_fab_clr_arr=array(); $booking_arr=array();$sampleBookingNos ="";
			foreach ($result as $row)
			{
				$booking_arr_coller_cut[$row[csf('booking_id')]]['booking_no']=$row[csf('booking_no')];
				if($row[csf("booking_without_order")] == 1)
				{
					//unset($row[csf('po_breakdown_id')]);
					$sampleBookingNo = $row[csf('booking_no')];
					$sampleBookingNos .= "'".$row[csf('booking_no')]."'".",";

					$booking_arr[$row[csf('po_breakdown_id')]]['booking_no']=$row[csf('booking_no')];
					$booking_arr[$row[csf('po_breakdown_id')]]['booking_without_order']=$row[csf('booking_without_order')];
				}

				$yarn_prod_ids=explode(",", $row[csf('yarn_prod_id')]);
				$count = '';
				foreach($yarn_prod_ids as $vals)
				{
					if($count=="")
					{
						$count.="(".$production_wise_yarn_dtls[$vals].")";
					}
					else
					{
						$count.=" , (".$production_wise_yarn_dtls[$vals].")";
					}

				}

				if ($row[csf("knitting_source")] == 1)
				{
					$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
				}
				else if ($row[csf("knitting_source")] == 3)
				{
					$knit_company = $supplier_arr[$row[csf("knitting_company")]];
				}

				if ($row[csf('receive_basis')] == 1)
				{
					$booking_no = explode("-", $row[csf('booking_no')]);
					$prog_book_no = (int)$booking_no[3];
				}
				else
				{
					$prog_book_no = $row[csf('booking_no')];
				}

				$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
				$all_color_name = "";
				foreach ($color_id_arr as $c_id)
				{
					$all_color_name .= $color_arr[$c_id] . ",";
				}
				$all_color_name = chop($all_color_name, ",");



				$lots = '';
				$yarn_lot = explode(",", $row[csf('yarn_lot')]);
				foreach ($yarn_lot as $lot_id)
				{
					if ($lots == '') $lots = $lot_id; else $lots .= "," . $lot_id;
				}



				if ($row[csf('receive_basis')] == 2)
				{
					$planOrder = sql_select("SELECT b.booking_no, a.is_sales, a.machine_gg, a.machine_dia from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
					$mc_dia = $planOrder[0][csf('machine_dia')].'X'.$planOrder[0][csf('machine_gg')];
				}
				else
				{
					//$mc_dia = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'].'X'.$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'];
					$mc_dia = $row[csf('machine_dia')].'X'.$row[csf('machine_gg')];
				}

			 	//$yarn_dtls=$count.','.$lots.','.$composition_string;
				$yarn_dtls=$count;
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["receive_date"]=change_date_format($row[csf("receive_date")]);

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["shift_name"]=$shift_name[$row[csf("shift_name")]];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["knit_company"]=$knit_company;

				if($style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["recv_number_prefix_num"]=="")
				{
					$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["recv_number_prefix_num"]=$row[csf('recv_number_prefix_num')];
				}
				else
				{
					$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["recv_number_prefix_num"] .=',,,'.$row[csf('recv_number_prefix_num')];
				}

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["prog_book_no"]=$prog_book_no;

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["count"]=$yarn_dtls;

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["febric_description_id"]=$composition_arr[$row[csf('febric_description_id')]];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["stitch_length"]=$row[csf('stitch_length')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["gsm"]=$row[csf('gsm')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["machine_no_id"]=$row[csf('machine_no_id')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["width"]=$row[csf('width')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["width"]=$row[csf('width')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["num_of_roll"] +=$row[csf('num_of_roll')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["current_delivery"] +=$row[csf('current_delivery')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["mc_dia"] =$mc_dia;

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["buyer"]  =$buyer_array[$row[csf('buyer_id')]];
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["booking"]=$row[csf('booking')];
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["receive_basis"]=$row[csf('receive_basis')];
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["barcode_no"]=$row[csf('barcode_no')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["fabric_color"]  =$all_color_name;

				//$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["body_part_id"]  =$body_part[$row[csf('body_part_id')]];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["grey_receive_qnty_pcs"]  +=$row[csf('grey_receive_qnty_pcs')];
			}
			// echo '<pre>';print_r($style_po_fab_clr_arr);
			$sampleBookingNos=chop($sampleBookingNos,",");
			$sql_sample_style = "SELECT a.booking_no, a.style_id, b.style_ref_no FROM wo_non_ord_samp_booking_dtls a, sample_development_mst b WHERE booking_no in($sampleBookingNos) AND a.status_active=1 AND a.is_deleted=0 AND a.style_id=b.id  group by a.booking_no,a.style_id,b.style_ref_no";

			$sample_result = sql_select($sql_sample_style);

			$sample_style_arr = array();
			foreach($sample_result as $row)
			{
				$sample_style_arr[$row[csf('booking_no')]]['syle_ref'] = $row[csf('style_ref_no')];
			}

			$i=1;
			$gr_color_roll=0;
			$gr_color_qty =0;
			$gr_qntyInpcs =0;
			foreach($style_po_fab_clr_arr as $style=>$po_data)
			{
				foreach($po_data as $po_id=>$fabr_data)
				{
					$po_color_roll=0;
					$po_color_qty=0;
					$po_grey_qntyinPcs=0;

					foreach($fabr_data as $fabr_id=>$buyer_data)
					{
						$color_roll=0;
						$color_qty=0;
						$grey_qtyInPcs=0;
						foreach($buyer_data as $buyer_id=>$date_data)
						{
							foreach($date_data as $date_id=>$shift_data)
							{
								foreach($shift_data as $shift_id=>$knit_data)
								{
									foreach($knit_data as $knit_id=>$yarn_data)
									{
										foreach($yarn_data as $yarn_id=>$fabr_data)
										{
											foreach($fabr_data as $fabric_description_id=>$stich_data)
											{
												foreach($stich_data as $stich_id=>$gsm_data)
												{
													foreach($gsm_data as $gsm_id=>$width_data)
													{
														foreach($width_data as $width_id=>$mc_dia_data)
														{
															foreach($mc_dia_data as $mc_dia_id=>$mc_dia_idv)
															{
																foreach($mc_dia_idv as $body_part_id=>$row)
																{
																$color_roll+=$row['num_of_roll'];
																$color_qty+=$row['current_delivery'];
																$grey_qtyInPcs+=$row['grey_receive_qnty_pcs'];
																$po_color_roll+=$row['num_of_roll'];
																$po_color_qty+=$row['current_delivery'];
																$po_grey_qntyinPcs+=$row['grey_receive_qnty_pcs'];
																$gr_color_roll+=$row['num_of_roll'];
																$gr_color_qty+=$row['current_delivery'];
																$gr_qntyInpcs+=$row['grey_receive_qnty_pcs'];
																$smn_booking=""; $smn_style="";
																if($booking_arr[$po_id]['booking_without_order']==1) // without order
																{
																	$smn_booking=""; $smn_style="";
																	if($production_data_arr[$row[csf("booking")]]["receive_basis"]==1)//Fabric Booking
																	{
																		$smn_booking=$row[csf('booking')];
																		$smn_style=$smn_booking_style_arr[$row[csf('booking_no')]];
																		//WO_NON_ORD_SAMP_BOOKING_DTLS_table[$row[csf('booking')]]['style_des']
																	}
																	else
																	{
																		$smn_booking=$prog_full_book_arr[$production_data_arr[$row['barcode_no']]['booking_id']];
																		$smn_style=$smn_booking_style_arr[$prog_full_book_arr[$production_data_arr[$row['barcode_no']]['booking_id']]];
																	}
																}
																?>
																<tr style="font-family: Arial Narrow, Arial, sans-serif;">
																	<td width="30" align="center"><? echo $i; ?></td>
																	<td width="80" style="word-break:break-all;word-wrap: break-word;"><? echo  $row['receive_date']; ?></td>
																	<td width="40"
																	style="word-break:break-all;word-wrap: break-word;"><? echo  $row['shift_name']; ?></td>
																	<td width="55"><? echo implode(" , ",array_unique(explode(",,,", $row['recv_number_prefix_num']))); ?></td>
																	<td width="80" style=""><? echo $row['buyer']; ?></td>
																	<td width="60" align="center" style=""><? echo $row['prog_book_no']; ?></td>
																	<td width="90" title="<? echo $po_id; ?>" style="word-break:break-all;word-wrap: break-word;">
																		<?  //echo $orderOrBookin = ($job_array[$po_id]['po']!="")?$job_array[$po_id]['po']:$sampleBookingNo; ?>
																		<?
																		if ($booking_arr[$po_id]['booking_without_order']==1)
																		{

																			echo $smn_booking;//$sampleBookingNo = $booking_arr[$po_id]['booking_no'];
																		}
																		else
																		{
																			echo $orderOrBookin = $job_array[$po_id]['po'].'<br>';
																			echo $orderOrBookin = $order_booking_arr[$job_array[$po_id]['booking_no']];
																		}
																		 ?>
																	</td>
																	<td width="90" style="word-break:break-all;word-wrap: break-word;"><? echo $body_part[$body_part_id];//$row['body_part_id']; ?>
																	</td>
																	<td title="<? echo $po_id; ?>" width="100" style="word-break:break-all;word-wrap: break-word;">
																	<?
																		if ($booking_arr[$po_id]['booking_without_order']==1)
																		{
																			echo $smn_style;//$styleRef =$sample_style_arr[$booking_arr[$po_id]['booking_no']]['syle_ref'];
																		}
																		else
																		{
																			echo $styleRef = $style.'<br>'.$job_array[$po_id]['ref_no'];
																			//echo $job_array[$po_id]['ref_no'];
																		}
																		//echo $styleRef = ($style!="")?$style:$sample_style_arr[$sampleBookingNo]['syle_ref'];
																	?>
                                                                    </td>
																	<td width="90" style="word-break:break-all;word-wrap: break-word;"><? echo  $row['knit_company']; ?></td>
																	
																	<td width="170" style=""><? echo $row['count'];?></td>

																	<td width="70" style="word-break:break-all;word-wrap: break-word;"><? echo $row['fabric_color']; ?></td>
																	<td width="150" style=""><? echo  $row['febric_description_id']; ?> </td>
																	
																	<td width="35" style="word-break:break-all;word-wrap: break-word;" align="center"><? echo  $row['stitch_length']; ?></td>
																	<td width="35" style="word-break:break-all;word-wrap: break-word;" align="center"><? echo  $row['gsm']; ?></td>
																	<td width="65" style="word-break:break-all;word-wrap: break-word;"  align="center"><? echo  $row['mc_dia'] ; ?></td>
																	<td width="60" style="word-break:break-all;word-wrap: break-word;" align="center"><? echo  $row['width']; ?></td>
																	<td width="100" align="center" style="word-break:break-all;word-wrap: break-word;"  align="right"><? echo $machine_details[$row['machine_no_id']]["machine_no"]; ?></td>
																	<td width="45" align="center" style="word-break:break-all;word-wrap: break-word;"  align="right"><? echo $row['num_of_roll']; ?></td>
																	<td width="45" align="center" style="word-break:break-all;word-wrap: break-word;"  align="right"><? echo $row['grey_receive_qnty_pcs']; ?></td>
																	<td width="90" style="word-break:break-all;word-wrap: break-word;" align="right"><? echo number_format($row['current_delivery'], 2); ?></td>
																</tr>

																<?
															    $i++;
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
						?>
						<tr>
							<td colspan="13" align="right">&nbsp;</td>
							<td colspan="7" align="left"><strong> Total <? echo "(".$row['fabric_color'].")";?></strong></td>
							<td align="center"><? echo $color_roll;?></td>
							<td align="right"><? echo number_format($grey_qtyInPcs,2);?></td>
							<td align="right"><? echo number_format($color_qty,2);?></td>
						</tr>
						<?
					}
					?>
					<tr>
						<td colspan="20" align="right"><strong>PO Total <? echo "(".$job_array[$po_id]['po'],")" ;?></strong></td>
						<td align="center"><strong><? echo $po_color_roll;?></strong></td>
						<td align="right"><strong><? echo number_format($po_grey_qntyinPcs,2);?></strong></td>
						<td align="right"><strong><? echo number_format($po_color_qty,2);?></strong></td>
					</tr>
					<?
					//$i++;
				}
			}
			?>
			<tr>
				<td colspan="20" align="right"><strong>Grand Total</strong></td>
				<td align="center"><strong><? echo $gr_color_roll;?></strong></td>
				<td align="right"><strong><? echo number_format($gr_qntyInpcs,2);?></strong></td>
				<td align="right"><strong><? echo number_format($gr_color_qty,2);?></strong></td>
			</tr>
			<tr>
				<td colspan="2" align="left"><b>Remarks:</b></td>
				<td colspan="25">&nbsp;</td>
			</tr>
		</table>
	</div>
	<br>
	<div style="width:1685px;">
    	<?
    	$barcode_res = sql_select("SELECT a.barcode_num from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$update_id and a.status_active = 1 and a.is_deleted = 0 group by a.barcode_num");
		foreach ($barcode_res as $val)
		{
			$barcode_nums .= $val[csf("barcode_num")].",";
		}
		$barcode_nums = chop($barcode_nums,",");

		$sql = "SELECT  a.receive_basis, a.booking_no, c.booking_no as bwo, c.is_sales
		    from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums)
		    group by a.receive_basis, a.booking_no, c.booking_no, c.is_sales
		    order by a.booking_no";
		$sql_result = sql_select($sql);
		$po_id_array = $sales_id_array = $booking_program_arr = array();
	    foreach ($sql_result as $row)
	    {
			if($row[csf("is_sales")] == 1){
				$sales_id_array[] = $row[csf("po_breakdown_id")];
			}else{
				$po_id_array[] = $row[csf("po_breakdown_id")];
			}

			if ($row[csf('receive_basis')] == 2) {
				$booking_program_arr[] = $row[csf("booking_no")];
			}else{
				$booking_no = explode("-", $row[csf('booking_no')]);
				$booking_program_arr[] = (int)$booking_no[3];
			}
	    }

		$planOrder = sql_select("SELECT a.id, b.booking_no from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
		$plan_arr = array();
		foreach ($planOrder as $plan_row)
		{
			$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
		}

		$job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
	    if(!empty($po_id_array))
	    {
		    $job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
		    $job_sql_result = sql_select($job_sql);
		    foreach ($job_sql_result as $row)
		    {
				$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
				$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
				$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
				$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		    }
	    }

	    if(!empty($sales_id_array))
	    {
		    $sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
			foreach ($sales_details as $sales_row)
			{
				$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
				$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
				$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
				$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
			}
	    }
		    //$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
		$booking_details_sql = sql_select("SELECT a.booking_no, b.job_no, b.po_break_down_id, c.grouping as ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 group by a.booking_no,b.job_no,b.po_break_down_id,c.grouping");

	    foreach ($booking_details_sql as $booking_row)
	    {
			$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
			$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
			$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
			$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
			$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
	    }

	    foreach ($sql_result as $row)
	    {
		    $is_sales = $row[csf('is_sales')];
		    if($is_sales == 1)
		    {
			    $within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
			    if($within_group == 1)
			    {
					$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
					$job_no = $booking_arr[$booking_no]["job_no"];
					$po_id = $booking_arr[$booking_no]["po_break_down_id"];
					$style_ref_no = $job_array[$po_id]['style_ref_no'];
					$ref_no = $booking_arr[$po_id]["ref_no"];
			    }
			    else
			    {
					$job_no = "";
					$style_ref_no = "";
					$ref_no = "";
					$po="";
					$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
			    }
		    }
		    else
		    {
				$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
				$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
				$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
				$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
				$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
		    }

		    $refno_data_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['booking_ref_no']][]=array(
		    //$refno_data_array[$job_array[$row[csf('po_breakdown_id')]]['ref_no']][$row[csf('febric_description_id')]][]=array(
			ref_no=>$ref_no,
			receive_basis=>$row[csf('receive_basis')],
			booking_id=>$row[csf('booking_id')],
			booking_no=>$booking_no,
			po_breakdown_id=>$row[csf('po_breakdown_id')],
			bwo=>$row[csf('bwo')],
			booking_without_order=>$row[csf('booking_without_order')],
			within_group=>$row[csf('within_group')],
			is_sales=>$row[csf('is_sales')],
			job_no=>$job_no,
			style_ref_no=>$style_ref_no,
			po=>$po_id
		    );
	    }


		$colarCupArr= sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
		foreach($colarCupArr as $row)
		{
			$body_part_data_arr[$row[csf('id')]]['body_part_full_name']=$row[csf('body_part_full_name')];
			$body_part_data_arr[$row[csf('id')]]['body_part_type']=$row[csf('body_part_type')];
		}
		//echo '<pre>';print_r($body_part_data_arr);

		// For Coller and Cuff data
		$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id";
		$sql_coller_cuff_result = sql_select($sql_coller_cuff);
		foreach ($sql_coller_cuff_result as $row2)
		{
			if($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type']>0 && $row2[csf('qc_pass_qnty_pcs')]>0)
			{
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			}
		}
		$CoCu=1;
		foreach($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr)
		{
			if( count($booking_data_arr)>0)
			{
			    //$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
			    ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left;">
                    <tr>
                        <th colspan="4"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name'];?> Details</th>
                    </tr>
                    <tr>
                        <th>Internal Ref. No</th>
                        <th>Fabric Booking No</th>
                        <th>Size</th>
                        <th>Qty Pcs</th>
                    </tr>
                    <?
                    $coller_cuff_qty_total=0;
                    foreach($booking_data_arr as $bookingId => $bookingData )
                    {
                        foreach($bookingData as $jobId => $jobData )
                        {
                            foreach($jobData as $size => $row )
                            {
                                ?>
                                <tr>
                                    <?
                                     if($row['receive_basis'] == 2)
                                     {
                                        ?>
                                        <td><?
                                        if($row['receive_basis'] == 2){
                                            echo  $booking_arr[$plan_arr[$bookingId]["booking_no"]]["booking_ref_no"];
                                        }else{
                                            echo  $booking_arr[$bookingId]["booking_ref_no"];
                                        }
                                        ?></td>
                                        <td><?  echo $plan_arr[$bookingId]["booking_no"]; ?></td>
                                        <?
                                     }
                                     else
                                     {
                                         ?>
                                        <td><? echo $booking_arr[$bookingId]["job_no"];?></td>
                                        <td><?  echo  $bookingId;  ?></td>
                                        <?
                                     }
                                    ?>
                                    <td align="center"><? echo $size;?></td>
                                    <td align="center"><? echo $row['qc_pass_qnty_pcs'];?></td>
                                </tr>
                                <?
                                $coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
                    </tr>
                </table>
			    <?
				if($CoCu==1){
					echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"4\">&nbsp;</td></tr></table>";
				}
				$CoCu++;
			}
		}
		?>
	</div>


	<? echo signature_table(125, $company, "1500px"); ?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess)
		{
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
	    }
	    generateBarcode('<? echo $txt_challan_no; ?>');
	    // document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
   </script>
   <?
   exit();
}

if ($action=="grey_delivery_print_13") // 
{
 	extract($_REQUEST);
 	$data = explode('*', $data);

 	$company = $data[0];
 	$txt_challan_no = $data[1];
 	$update_id = $data[2];
 	$kniting_source = $data[4];
 	$floor_name = $data[5];
 	$organicyesno = $data[6];

 	$company_array = array();
 	$company_data = sql_select("SELECT id, company_name, company_short_name from lib_company");
 	foreach ($company_data as $row)
 	{
 		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
 		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
 	}

 	$machine_details = array();
 	$machine_sql = sql_select("SELECT id, machine_no, dia_width, gauge from lib_machine_name");
 	foreach ($machine_sql as $row)
 	{
 		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
 		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
 		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
 	}

 	$color_arr = return_library_array("SELECT id, color_name from lib_color", 'id', 'color_name');
 	$supplier_arr = return_library_array("SELECT id, short_name from lib_supplier", "id", "short_name");
 	$buyer_array = return_library_array("SELECT id, short_name from lib_buyer", "id", "short_name");
 	$yarn_count_details = return_library_array("SELECT id,yarn_count from lib_yarn_count", "id", "yarn_count");
 	$machine_details_arr = return_library_array("SELECT id, machine_no from lib_machine_name", "id", "machine_no");
 	$brand_details = return_library_array("SELECT id, brand_name from lib_brand", "id", "brand_name");
 	$location_arr = return_library_array("SELECT id, location_name from lib_location", "id", "location_name");
 	$order_booking_arr = return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where booking_type !=2", "po_break_down_id", "booking_no");
 	$smn_booking_style_arr=return_library_array( "select booking_no, style_ref_no from wo_non_ord_samp_booking_dtls a, sample_development_mst b where a.style_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by booking_no, style_ref_no", "booking_no", "style_ref_no");

 	$mstData = sql_select("SELECT company_id, delevery_date, knitting_source, knitting_company,location_id, remarks from pro_grey_prod_delivery_mst where id=$update_id");

 	$job_array = array();
 	//$job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
 	$job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no, a.style_ref_no
 	from wo_po_details_master a, wo_po_break_down b
 	where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
 	$job_sql_result = sql_select($job_sql);
 	foreach ($job_sql_result as $row)
 	{
 		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
 		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
 		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
 		$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
 		$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];

 		$job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
 		$job_array[$row[csf('id')]]['booking_no'] = $row[csf('id')];
 	}
 	//print_r($job_array);

 	$composition_arr = array();
 	$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
 	$data_array = sql_select($sql_deter);
 	foreach ($data_array as $row)
 	{
 		if (array_key_exists($row[csf('id')], $composition_arr))
 		{
 			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
 		}
 		else
 		{
 			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
 		}
 	}

 	$yarn_lot_arr = array();
 	$yarn_lot_sql=sql_select("SELECT id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id,yarn_type,supplier_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0");
 	foreach ($yarn_lot_sql as $value)
 	{
 		if($value[csf('yarn_comp_percent2nd')])
 		{
 			$production_wise_yarn_dtls[$value[csf('id')]]=$yarn_count_details[$value[csf('yarn_count_id')]].','.$value[csf('lot')].", ".$composition[$value[csf('yarn_comp_type1st')]].' '.$value[csf('yarn_comp_percent1st')].'%'." ".$composition[$value[csf('yarn_comp_type2nd')]]. ", " . $value[csf('yarn_comp_percent2nd')] . "%" .', '.$yarn_type[$value[csf('yarn_type')]].", ".$supplier_arr[$value[csf("supplier_id")]];
 		}
 		else
 		{
 			$production_wise_yarn_dtls[$value[csf('id')]]=$yarn_count_details[$value[csf('yarn_count_id')]].','.$value[csf('lot')].", ".$composition[$value[csf('yarn_comp_type1st')]].' '.$value[csf('yarn_comp_percent1st')].'%'.', '.$yarn_type[$value[csf('yarn_type')]].", ".$supplier_arr[$value[csf("supplier_id")]];
 		}
 	}


 	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
 	$com_dtls = fnc_company_location_address($company, $location, 2);


 	?>
 	<div style="width:1820px;">
 		<table width="1290" cellspacing="0" border="0">
 			<tr>
 				<td rowspan="3" width="10">
 					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
 				</td>
 				<td align="center" style="font-size:x-large">
 					<strong ><? echo $com_dtls[0]; ?></strong>
					 <!-- style="margin-right:300px" -->
 				</td>

 				<td>
					<?php
					if($organicyesno==1)
					{
						?>
						<div style="border: 2px solid #000; padding: 0px; color: #000;float: right; margin-right: 10px;  width: 235px; text-align: center;">ORGANIC</div>
						<?
					}else{
						echo "&nbsp;";
					}
					?>
				</td>
 			</tr>
 			<tr>
				<td align="center" style="font-size:14px">
					<strong ><? echo $com_dtls[1]; ?></strong>
					<!-- style="margin-right:400px" -->
				</td>
			</tr>
 			<tr>
 				<td align="center" style="font-size:18px">
				 <strong ><u>Knitting Grey Delivery Challan</u></strong>
					 <!-- style="margin-right:300px" -->
 				</td>
 			</tr>
 			<tr>
 				<td align="center" style="font-size:16px">
 					<strong style="margin-right:300px"><u>Knitting Section</u></strong>
 				</td>
 			</tr>
 		</table>
 		<br>
 		<table width="1290" cellspacing="0" align="center" border="0">
 			<tr>
 				<td style="font-size:16px; font-weight:bold;" width="80">Challan No</td>
 				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
 				<td style="font-size:16px; font-weight:bold;" width="60">Location</td>
 				<td width="270" id="location_td" >:&nbsp;<? echo $location_arr[$mstData[0][csf('location_id')]]; ?></td>
				<td></td>
 				<td width="710" id="barcode_img_id" align="right"></td>
 			</tr>
 			<tr>
 				<td style="font-size:16px; font-weight:bold;">Delivery Date</td>
 				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
 				<td style="font-size:16px; font-weight:bold;">Floor No.</td>
 				<td  style="width:150px">:&nbsp;<? echo $floor_name; ?></td>
				<td style="font-size:16px; font-weight:bold;" colspan="2">Remarks:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>

 			</tr>
 		</table>
 		<br>
 		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1890" class="rpt_table">
 			<thead>
 				<tr>
 					<th width="30" style="word-break: break-all;word-wrap: break-word;">SL</th>
 					<th width="80" style="word-break: break-all;word-wrap: break-word;">Production Date</th>
 					<th width="40" style="word-break: break-all;word-wrap: break-word;">Shift</th>
 					<th width="60" style="word-break: break-all;word-wrap: break-word;">Prod.ID</th>
 					<th width="80" style="word-break: break-all;word-wrap: break-word;">Buyer</th>
 					<th width="60" style="word-break: break-all;word-wrap: break-word;">Program No</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Order No/Booking No</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Body Part</th>
 					<th width="100" style="word-break: break-all;word-wrap: break-word;">Style / Internal ref.</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Knit. Comp.</th>
 					<th width="170" style="word-break: break-all;word-wrap: break-word;">Yarn Details</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Fab Color</th>
 					<th width="150" style="word-break: break-all;word-wrap: break-word;">Fabric Type</th>
					<th width="80" style="word-break: break-all;word-wrap: break-word;">Dia Type</th>
 					<th width="35" style="word-break: break-all;word-wrap: break-word;">Stich</th>
 					<th width="35" style="word-break: break-all;word-wrap: break-word;">GSM</th>
 					<th width="65" style="word-break: break-all;word-wrap: break-word;">Dia X GG</th>
 					<th width="60" style="word-break: break-all;word-wrap: break-word;">Fab. Dia</th>
 					<th width="100" style="word-break: break-all;word-wrap: break-word;">MC. No</th>
 					<th width="45" style="word-break: break-all;word-wrap: break-word;">No of Roll</th>
 					<th width="50" style="word-break: break-all;word-wrap: break-word;">Qnty. in Pcs</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Deliv. QTY</th>
 				</tr>
 			</thead>
 			<?
 			$sql_dtls_knit = "SELECT e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
 			from  pro_grey_prod_entry_dtls d,  inv_receive_master e
 			where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
 			$result_arr = sql_select($sql_dtls_knit);
 			$machine_dia_guage_arr = array();
 			foreach ($result_arr as $row) {
 				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
 				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
 			}

 			$i = 0;
 			$tot_qty = 0;
 			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			if ($kniting_source == 1)//in-house
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as booking, c.booking_without_order,b.body_part_id,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs,e.seq_no,c.barcode_no , b.machine_dia, b.machine_gg, c.is_sales 
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e
				WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0
				group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.po_breakdown_id , c.booking_no, c.booking_without_order,b.body_part_id,e.seq_no,c.barcode_no, b.machine_dia, b.machine_gg, c.is_sales  order by e.seq_no";
			}
			else
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as booking, c.booking_without_order,b.body_part_id,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs,c.barcode_no , b.machine_dia,b.machine_gg, c.is_sales 
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d
				WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0
				group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.po_breakdown_id , c.booking_no, c.booking_without_order,b.body_part_id,c.barcode_no, b.machine_dia,b.machine_gg, c.is_sales ";
			}
			// echo $sql;
			$result = sql_select($sql);
			$all_barcode_no="";
			foreach($result as $row)
			{
				$all_barcode_no.=$row[csf("barcode_no")].",";
				if($row[csf("is_sales")] == 1)
				{
					$all_sales_arr[$row[csf("po_breakdown_id")]]=$row[csf("po_breakdown_id")];
				}
			}

			if(!empty($all_sales_arr))
			{
				$fso_sql=sql_select("select a.id, a.sales_booking_no, c.grouping from fabric_sales_order_mst a, wo_booking_dtls b, wo_po_break_down c where a.sales_booking_no=b.booking_no and a.within_group=1 and b.po_break_down_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id in (". implode(',',$all_sales_arr) .")
				group by a.id, a.sales_booking_no, c.grouping");

				$fso_data_arr=array();
				foreach($fso_sql as $row)
				{
					$fso_data_arr[$row[csf("id")]]["sales_booking_no"]=$row[csf("sales_booking_no")];
					$fso_data_arr[$row[csf("id")]]["grouping"]=$row[csf("grouping")];

				}
				unset($fso_sql);
			}



			$all_barcode_no=implode(",",array_unique(explode(",",chop($all_barcode_no,","))));
			if($all_barcode_no!="")
			{
				$production_sql=sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis,a.knitting_source, a.knitting_company  from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

				$production_data_arr=array();
				foreach($production_sql as $row)
				{
					$production_data_arr[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
					$production_data_arr[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
					if($row[csf("receive_basis")]==2)
					{
						$all_program_no.=$row[csf("booking_id")].",";
					}
				}
			}
			$all_program_no=implode(",",array_unique(explode(",",chop($all_program_no,","))));
			if($all_program_no!="")
			{
				$program_sql=sql_select("select a.booking_id, c.booking_no from inv_receive_master a,  ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c
					where a.booking_id=b.id and b.mst_id=c.id and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");
				$prog_book_arr=array(); $prog_full_book_arr=array();
				foreach($program_sql as $row)
				{
					$prog_full_book_arr[$row[csf("booking_id")]]=$row[csf("booking_no")];
				}
			}


			$loc_arr = array();
			$loc_nm = ": ";
			$style_po_fab_clr_arr=array(); $booking_arr=array();$sampleBookingNos ="";
			foreach ($result as $row)
			{
				$booking_arr_coller_cut[$row[csf('booking_id')]]['booking_no']=$row[csf('booking_no')];
				if($row[csf("booking_without_order")] == 1)
				{
					//unset($row[csf('po_breakdown_id')]);
					$sampleBookingNo = $row[csf('booking_no')];
					$sampleBookingNos .= "'".$row[csf('booking_no')]."'".",";

					$booking_arr[$row[csf('po_breakdown_id')]]['booking_no']=$row[csf('booking_no')];
					$booking_arr[$row[csf('po_breakdown_id')]]['booking_without_order']=$row[csf('booking_without_order')];
				}

				$yarn_prod_ids=explode(",", $row[csf('yarn_prod_id')]);
				$count = '';
				foreach($yarn_prod_ids as $vals)
				{
					if($count=="")
					{
						$count.="(".$production_wise_yarn_dtls[$vals].")";
					}
					else
					{
						$count.=" , (".$production_wise_yarn_dtls[$vals].")";
					}

				}

				if ($row[csf("knitting_source")] == 1)
				{
					$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
				}
				else if ($row[csf("knitting_source")] == 3)
				{
					$knit_company = $supplier_arr[$row[csf("knitting_company")]];
				}

				if ($row[csf('receive_basis')] == 1)
				{
					$booking_no = explode("-", $row[csf('booking_no')]);
					$prog_book_no = (int)$booking_no[3];
				}
				else
				{
					$prog_book_no = $row[csf('booking_no')];
				}

				$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
				$all_color_name = "";
				foreach ($color_id_arr as $c_id)
				{
					$all_color_name .= $color_arr[$c_id] . ",";
				}
				$all_color_name = chop($all_color_name, ",");



				$lots = '';
				$yarn_lot = explode(",", $row[csf('yarn_lot')]);
				foreach ($yarn_lot as $lot_id)
				{
					if ($lots == '') $lots = $lot_id; else $lots .= "," . $lot_id;
				}



				if ($row[csf('receive_basis')] == 2)
				{
					$planOrder = sql_select("SELECT b.booking_no, a.is_sales, a.machine_gg, a.machine_dia from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
					$mc_dia = $planOrder[0][csf('machine_dia')].'X'.$planOrder[0][csf('machine_gg')];
				}
				else
				{
					//$mc_dia = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'].'X'.$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'];
					$mc_dia = $row[csf('machine_dia')].'X'.$row[csf('machine_gg')];
				}

			 	//$yarn_dtls=$count.','.$lots.','.$composition_string;
				$yarn_dtls=$count;
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["receive_date"].=(change_date_format($row[csf("receive_date")]).',');

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["shift_name"]=$shift_name[$row[csf("shift_name")]];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["knit_company"]=$knit_company;

				if($style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["recv_number_prefix_num"]=="")
				{
					$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["recv_number_prefix_num"]=$row[csf('recv_number_prefix_num')];
				}
				else
				{
					$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["recv_number_prefix_num"] .=',,,'.$row[csf('recv_number_prefix_num')];
				}

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["prog_book_no"]=$prog_book_no;

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["count"]=$yarn_dtls;

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["febric_description_id"]=$composition_arr[$row[csf('febric_description_id')]];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["stitch_length"]=$row[csf('stitch_length')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["gsm"]=$row[csf('gsm')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["machine_no_id"]=$row[csf('machine_no_id')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["width"]=$row[csf('width')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["width"]=$row[csf('width')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["num_of_roll"] +=$row[csf('num_of_roll')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["current_delivery"] +=$row[csf('current_delivery')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["mc_dia"] =$mc_dia;

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["buyer"]  =$buyer_array[$row[csf('buyer_id')]];
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["booking"]=$row[csf('booking')];
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["receive_basis"]=$row[csf('receive_basis')];
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["barcode_no"]=$row[csf('barcode_no')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["fabric_color"]  =$all_color_name;

				//$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["body_part_id"]  =$body_part[$row[csf('body_part_id')]];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["grey_receive_qnty_pcs"]  +=$row[csf('grey_receive_qnty_pcs')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["is_sales"]  =$row[csf('is_sales')];
			}
			// echo '<pre>';print_r($style_po_fab_clr_arr);
			$sampleBookingNos=chop($sampleBookingNos,",");
			$sql_sample_style = "SELECT a.booking_no, a.style_id, b.style_ref_no FROM wo_non_ord_samp_booking_dtls a, sample_development_mst b WHERE booking_no in($sampleBookingNos) AND a.status_active=1 AND a.is_deleted=0 AND a.style_id=b.id  group by a.booking_no,a.style_id,b.style_ref_no";

			$sample_result = sql_select($sql_sample_style);

			$sample_style_arr = array();
			foreach($sample_result as $row)
			{
				$sample_style_arr[$row[csf('booking_no')]]['syle_ref'] = $row[csf('style_ref_no')];
			}

			$i=1;
			$gr_color_roll=0;
			$gr_color_qty =0;
			$gr_qntyInpcs =0;
			foreach($style_po_fab_clr_arr as $style=>$po_data)
			{
				foreach($po_data as $po_id=>$fabr_data)
				{
					$po_color_roll=0;
					$po_color_qty=0;
					$po_grey_qntyinPcs=0;

					foreach($fabr_data as $fabr_id=>$buyer_data)
					{
						$color_roll=0;
						$color_qty=0;
						$grey_qtyInPcs=0;
						foreach($buyer_data as $buyer_id=>$shift_data)
						{
							
								foreach($shift_data as $shift_id=>$knit_data)
								{
									foreach($knit_data as $knit_id=>$yarn_data)
									{
										foreach($yarn_data as $yarn_id=>$fabr_data)
										{
											foreach($fabr_data as $fabric_description_id=>$stich_data)
											{
												foreach($stich_data as $stich_id=>$gsm_data)
												{
													foreach($gsm_data as $gsm_id=>$width_data)
													{
														foreach($width_data as $width_id=>$mc_dia_data)
														{
															foreach($mc_dia_data as $mc_dia_id=>$mc_dia_idv)
															{
																foreach($mc_dia_idv as $body_part_id=>$row)
																{
																$color_roll+=$row['num_of_roll'];
																$color_qty+=$row['current_delivery'];
																$grey_qtyInPcs+=$row['grey_receive_qnty_pcs'];
																$po_color_roll+=$row['num_of_roll'];
																$po_color_qty+=$row['current_delivery'];
																$po_grey_qntyinPcs+=$row['grey_receive_qnty_pcs'];
																$gr_color_roll+=$row['num_of_roll'];
																$gr_color_qty+=$row['current_delivery'];
																$gr_qntyInpcs+=$row['grey_receive_qnty_pcs'];
																$smn_booking=""; $smn_style="";
																if($booking_arr[$po_id]['booking_without_order']==1) // without order
																{
																	$smn_booking=""; $smn_style="";
																	if($production_data_arr[$row[csf("booking")]]["receive_basis"]==1)//Fabric Booking
																	{
																		$smn_booking=$row[csf('booking')];
																		$smn_style=$smn_booking_style_arr[$row[csf('booking_no')]];
																		//WO_NON_ORD_SAMP_BOOKING_DTLS_table[$row[csf('booking')]]['style_des']
																	}
																	else
																	{
																		$smn_booking=$prog_full_book_arr[$production_data_arr[$row['barcode_no']]['booking_id']];
																		$smn_style=$smn_booking_style_arr[$prog_full_book_arr[$production_data_arr[$row['barcode_no']]['booking_id']]];
																	}
																}
																?>
																<tr style="font-family: Arial Narrow, Arial, sans-serif;">
																	<td width="30" align="center"><? echo $i; ?></td>
																	<td width="80"><? echo implode(" , ",array_unique(explode(",",rtrim($row['receive_date'],",")))) ; ?></td>
																	<td width="40"
																	style="word-break:break-all;word-wrap: break-word;"><? echo  $row['shift_name']; ?></td>
																	<td width="60"><? echo implode(" , ",array_unique(explode(",,,", $row['recv_number_prefix_num']))); ?></td>
																	<td width="80" style=""><? echo $row['buyer']; ?></td>
																	<td width="60" align="center" style=""><? echo $row['prog_book_no']; ?></td>
																	<td width="90" title="<? echo $po_id; ?>" style="word-break:break-all;word-wrap: break-word;">
																		<?  //echo $orderOrBookin = ($job_array[$po_id]['po']!="")?$job_array[$po_id]['po']:$sampleBookingNo; ?>
																		<?
																		if ($row['is_sales']==1)
																		{
																			echo $booking= $fso_data_arr[$po_id]["sales_booking_no"];
																		}
																		else if ($booking_arr[$po_id]['booking_without_order']==1)
																		{

																			echo $booking= $smn_booking;//$sampleBookingNo = $booking_arr[$po_id]['booking_no'];
																		}
																		else
																		{
																			echo $orderOrBookin = $job_array[$po_id]['po'].'<br>';
																			echo $booking= $orderOrBookin = $order_booking_arr[$job_array[$po_id]['booking_no']];
																		}
																		 ?>
																	</td>
																	<td width="90" style="word-break:break-all;word-wrap: break-word;"><? echo $body_part[$body_part_id];//$row['body_part_id']; ?>
																	</td>
																	<td title="<? echo $po_id; ?>" width="100" style="word-break:break-all;word-wrap: break-word;">
																	<?
																		if ($row['is_sales']==1)
																		{
																			echo $fso_data_arr[$po_id]["grouping"];
																		}
																		else if ($booking_arr[$po_id]['booking_without_order']==1)
																		{
																			echo $smn_style;//$styleRef =$sample_style_arr[$booking_arr[$po_id]['booking_no']]['syle_ref'];
																		}
																		else
																		{
																			echo $styleRef = $style.'<br>'.$job_array[$po_id]['ref_no'];
																			//echo $job_array[$po_id]['ref_no'];
																		}
																		//echo $styleRef = ($style!="")?$style:$sample_style_arr[$sampleBookingNo]['syle_ref'];
																	?>
                                                                    </td>
																	<td width="90" style="word-break:break-all;word-wrap: break-word;"><? echo  $row['knit_company']; ?></td>
																	<td width="170" style=""><? echo  $row['count']; ?></td>

																	<td width="70" style="word-break:break-all;word-wrap: break-word;"><? echo $row['fabric_color']; ?></td>
																	<td width="150" style=""><? echo  $row['febric_description_id']; ?> </td>
																	<td width="80" style="word-break:break-all;word-wrap: break-word;" align="center">
																		<? $with_type_dia = sql_select("select width_dia_type from ppl_planning_info_entry_mst where booking_no='$booking'");
																		 echo $fabric_typee[$with_type_dia[0]['WIDTH_DIA_TYPE']];
																	?></td>
																	<td width="35" style="word-break:break-all;word-wrap: break-word;" align="center"><? echo  $row['stitch_length']; ?></td>
																	<td width="35" style="word-break:break-all;word-wrap: break-word;" align="center"><? echo  $row['gsm']; ?></td>
																	<td width="65" style="word-break:break-all;word-wrap: break-word;"  align="center"><? echo  $row['mc_dia'] ; ?></td>
																	<td width="60" style="word-break:break-all;word-wrap: break-word;" align="center"><? echo  $row['width']; ?></td>
																	<td width="100" align="center" style="word-break:break-all;word-wrap: break-word;"  align="right"><? echo $machine_details[$row['machine_no_id']]["machine_no"]; ?></td>
																	<td width="45" align="center" style="word-break:break-all;word-wrap: break-word;"  align="right"><? echo $row['num_of_roll']; ?></td>
																	<td width="45" align="center" style="word-break:break-all;word-wrap: break-word;"  align="right"><? echo $row['grey_receive_qnty_pcs']; ?></td>
																	<td width="90" style="word-break:break-all;word-wrap: break-word;" align="right"><? echo number_format($row['current_delivery'], 2); ?></td>
																</tr>

																<?
															    $i++;
																}
															}
														}
													}
												}
											}
										}
									}
								
							}
						}
						?>
						<tr>
							<td colspan="11" align="right">&nbsp;</td>
							<td colspan="7" align="left"><strong> Total <? echo "(".$row['fabric_color'].")";?></strong></td>
							<td align="center"><? echo $color_roll;?></td>
							<td align="right"><? echo number_format($grey_qtyInPcs,2);?></td>
							<td align="right"><? echo number_format($color_qty,2);?></td>
						</tr>
						<?
					}
					?>
					<tr>
						<td colspan="18" align="right"><strong>PO Total <? echo "(".$job_array[$po_id]['po'],")" ;?></strong></td>
						<td align="center"><strong><? echo $po_color_roll;?></strong></td>
						<td align="right"><strong><? echo number_format($po_grey_qntyinPcs,2);?></strong></td>
						<td align="right"><strong><? echo number_format($po_color_qty,2);?></strong></td>
					</tr>
					<?
					//$i++;
				}
			}
			?>
			<tr>
				<td colspan="18" align="right"><strong>Grand Total</strong></td>
				<td align="center"><strong><? echo $gr_color_roll;?></strong></td>
				<td align="right"><strong><? echo number_format($gr_qntyInpcs,2);?></strong></td>
				<td align="right"><strong><? echo number_format($gr_color_qty,2);?></strong></td>
			</tr>
			<tr>
				<td colspan="2" align="left"><b>Remarks:</b></td>
				<td colspan="23">&nbsp;</td>
			</tr>
		</table>
	</div>
	<br>
	<div style="width:1685px;">
    	<?
    	$barcode_res = sql_select("SELECT a.barcode_num from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$update_id and a.status_active = 1 and a.is_deleted = 0 group by a.barcode_num");
		foreach ($barcode_res as $val)
		{
			$barcode_nums .= $val[csf("barcode_num")].",";
		}
		$barcode_nums = chop($barcode_nums,",");

		$sql = "SELECT  a.receive_basis, a.booking_no, c.booking_no as bwo, c.is_sales
		    from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums)
		    group by a.receive_basis, a.booking_no, c.booking_no, c.is_sales
		    order by a.booking_no";
		$sql_result = sql_select($sql);
		$po_id_array = $sales_id_array = $booking_program_arr = array();
	    foreach ($sql_result as $row)
	    {
			if($row[csf("is_sales")] == 1){
				$sales_id_array[] = $row[csf("po_breakdown_id")];
			}else{
				$po_id_array[] = $row[csf("po_breakdown_id")];
			}

			if ($row[csf('receive_basis')] == 2) {
				$booking_program_arr[] = $row[csf("booking_no")];
			}else{
				$booking_no = explode("-", $row[csf('booking_no')]);
				$booking_program_arr[] = (int)$booking_no[3];
			}
	    }

		$planOrder = sql_select("SELECT a.id, b.booking_no from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
		$plan_arr = array();
		foreach ($planOrder as $plan_row)
		{
			$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
		}

		$job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
	    if(!empty($po_id_array))
	    {
		    $job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
		    $job_sql_result = sql_select($job_sql);
		    foreach ($job_sql_result as $row)
		    {
				$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
				$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
				$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
				$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		    }
	    }

	    if(!empty($sales_id_array))
	    {
		    $sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
			foreach ($sales_details as $sales_row)
			{
				$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
				$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
				$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
				$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
			}
	    }
		    //$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
		$booking_details_sql = sql_select("SELECT a.booking_no, b.job_no, b.po_break_down_id, c.grouping as ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 group by a.booking_no,b.job_no,b.po_break_down_id,c.grouping");

	    foreach ($booking_details_sql as $booking_row)
	    {
			$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
			$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
			$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
			$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
			$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
	    }

	    foreach ($sql_result as $row)
	    {
		    $is_sales = $row[csf('is_sales')];
		    if($is_sales == 1)
		    {
			    $within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
			    if($within_group == 1)
			    {
					$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
					$job_no = $booking_arr[$booking_no]["job_no"];
					$po_id = $booking_arr[$booking_no]["po_break_down_id"];
					$style_ref_no = $job_array[$po_id]['style_ref_no'];
					$ref_no = $booking_arr[$po_id]["ref_no"];
			    }
			    else
			    {
					$job_no = "";
					$style_ref_no = "";
					$ref_no = "";
					$po="";
					$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
			    }
		    }
		    else
		    {
				$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
				$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
				$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
				$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
				$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
		    }

		    $refno_data_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['booking_ref_no']][]=array(
		    //$refno_data_array[$job_array[$row[csf('po_breakdown_id')]]['ref_no']][$row[csf('febric_description_id')]][]=array(
			ref_no=>$ref_no,
			receive_basis=>$row[csf('receive_basis')],
			booking_id=>$row[csf('booking_id')],
			booking_no=>$booking_no,
			po_breakdown_id=>$row[csf('po_breakdown_id')],
			bwo=>$row[csf('bwo')],
			booking_without_order=>$row[csf('booking_without_order')],
			within_group=>$row[csf('within_group')],
			is_sales=>$row[csf('is_sales')],
			job_no=>$job_no,
			style_ref_no=>$style_ref_no,
			po=>$po_id
		    );
	    }


		$colarCupArr= sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
		foreach($colarCupArr as $row)
		{
			$body_part_data_arr[$row[csf('id')]]['body_part_full_name']=$row[csf('body_part_full_name')];
			$body_part_data_arr[$row[csf('id')]]['body_part_type']=$row[csf('body_part_type')];
		}
		//echo '<pre>';print_r($body_part_data_arr);

		// For Coller and Cuff data
		$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id";
		$sql_coller_cuff_result = sql_select($sql_coller_cuff);
		foreach ($sql_coller_cuff_result as $row2)
		{
			if($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type']>0 && $row2[csf('qc_pass_qnty_pcs')]>0)
			{
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			}
		}
		$CoCu=1;
		foreach($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr)
		{
			if( count($booking_data_arr)>0)
			{
			    //$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
			    ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left;">
                    <tr>
                        <th colspan="4"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name'];?> Details</th>
                    </tr>
                    <tr>
                        <th>Internal Ref. No</th>
                        <th>Fabric Booking No</th>
                        <th>Size</th>
                        <th>Qty Pcs</th>
                    </tr>
                    <?
                    $coller_cuff_qty_total=0;
                    foreach($booking_data_arr as $bookingId => $bookingData )
                    {
                        foreach($bookingData as $jobId => $jobData )
                        {
                            foreach($jobData as $size => $row )
                            {
                                ?>
                                <tr>
                                    <?
                                     if($row['receive_basis'] == 2)
                                     {
                                        ?>
                                        <td><?
                                        if($row['receive_basis'] == 2){
                                            echo  $booking_arr[$plan_arr[$bookingId]["booking_no"]]["booking_ref_no"];
                                        }else{
                                            echo  $booking_arr[$bookingId]["booking_ref_no"];
                                        }
                                        ?></td>
                                        <td><?  echo $plan_arr[$bookingId]["booking_no"]; ?></td>
                                        <?
                                     }
                                     else
                                     {
                                         ?>
                                        <td><? echo $booking_arr[$bookingId]["job_no"];?></td>
                                        <td><?  echo  $bookingId;  ?></td>
                                        <?
                                     }
                                    ?>
                                    <td align="center"><? echo $size;?></td>
                                    <td align="center"><? echo $row['qc_pass_qnty_pcs'];?></td>
                                </tr>
                                <?
                                $coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
                    </tr>
                </table>
			    <?
				if($CoCu==1){
					echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"4\">&nbsp;</td></tr></table>";
				}
				$CoCu++;
			}
		}
		?>
	</div>


	<? echo signature_table(125, $company, "1500px"); ?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess)
		{
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
	    }
	    generateBarcode('<? echo $txt_challan_no; ?>');
	    // document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
   </script>
   <?
   exit();
}
//for norban
if ($action == "grey_delivery_print_13") // Print 8, created by Tipu
{
	extract($_REQUEST);
	echo load_html_head_contents("Roll Wise Grey Fabric Delivery to Store Print-8", "../../", 1, 1, '', '', '');
	$data = explode('*', $data);

	$company   		= $data[0];
	$system_no 		= $data[1];
	$report_title 	= $data[2];
	$mst_id     	= $data[3];
	$knit_source    = $data[4];
	$floor_id 		= $data[5];
	$no_copy 		= $data[6];
	$floor_id 		= $data[7];

	$floor_name_arr = return_library_array("select id, floor_name from lib_prod_floor", "id", "floor_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location_arr = return_library_array("select id,location_name from lib_location", "id", "location_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$body_part_type=return_library_array("select id, body_part_type from lib_body_part where status_active=1",'id','body_part_type');
	$composition_arr = array();$yarn_composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row)
	{
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . " :: " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}

		if (array_key_exists($row[csf('id')], $yarn_composition_arr)) {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		} else {
			$yarn_composition_arr[$row[csf('id')]] = $composition[$row[csf('copmposition_id')]];
		}
	}



	$store_location_id=return_field_value("location_id","lib_store_location","id=$store_id and is_deleted=0","location_id");
	$store_arr = return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
	$department_arr = return_library_array("select id, department_name from lib_department", "id", "department_name");



	//for buyer
	$sqlBuyer = sql_select("select id as ID, buyer_name as BUYER_NAME, short_name as SHORT_NAME from lib_buyer");
	foreach($sqlBuyer as $row)
	{
		$buyer_arr[$row['ID']] = $row['SHORT_NAME'];
		$buyer_dtls_arr[$row['ID']] = $row['BUYER_NAME'];
	}
	unset($sqlBuyer);

	//for supplier
	$sqlSupplier = sql_select("select id as ID, supplier_name as SUPPLIER_NAME, short_name as SHORT_NAME from lib_supplier");
	foreach($sqlSupplier as $row)
	{
		$supplier_arr[$row['ID']] = $row['SHORT_NAME'];
		$supplier_dtls_arr[$row['ID']] = $row['SUPPLIER_NAME'];
	}
	unset($sqlSupplier);

	//for gate pass
	$sql_get_pass = "SELECT a.ID, a.SYS_NUMBER, a.BASIS, a.COMPANY_ID, a.GET_PASS_NO, a.DEPARTMENT_ID, a.ATTENTION, a.SENT_BY, a.WITHIN_GROUP, a.SENT_TO, a.CHALLAN_NO, a.OUT_DATE, a.TIME_HOUR, a.TIME_MINUTE, a.RETURNABLE, a.DELIVERY_AS, a.EST_RETURN_DATE, a.INSERTED_BY, a.CARRIED_BY, a.LOCATION_ID, a.COM_LOCATION_ID, a.VHICLE_NUMBER, a.LOCATION_NAME, a.REMARKS, a.DO_NO, a.MOBILE_NO, a.ISSUE_ID, a.RETURNABLE_GATE_PASS_REFF, a.DELIVERY_COMPANY, a.ISSUE_PURPOSE,a.DRIVER_NAME,a.DRIVER_LICENSE_NO,a.SECURITY_LOCK_NO, b.QUANTITY, b.NO_OF_BAGS FROM inv_gate_pass_mst a, INV_GATE_PASS_DTLS b WHERE a.id = b.mst_id AND a.company_id = ".$company." AND a.basis = 10 AND a.STATUS_ACTIVE = 1 AND a.IS_DELETED = 0 AND a.challan_no LIKE '".$system_no."%'";
	// echo $sql_get_pass;
	$sql_get_pass_rslt = sql_select($sql_get_pass);
	$is_gate_pass = 0;
	$is_gate_out = 0;
	$gate_pass_id = '';
	$gatePassDataArr = array();
	foreach($sql_get_pass_rslt as $row)
	{
		$exp = explode(',', $row['CHALLAN_NO']);
		foreach($exp as $key=>$val)
		{
			if($val == $system_no)
			{
				$is_gate_pass = 1;
				$gate_pass_id = $row['ID'];

				$row['OUT_DATE'] = ($row['OUT_DATE']!=''?date('d-m-Y', strtotime($row['OUT_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');
				$row['EST_RETURN_DATE'] = ($row['EST_RETURN_DATE']!=''?date('d-m-Y', strtotime($row['EST_RETURN_DATE'])):'');

				if($row['WITHIN_GROUP'] == 1)
				{
					//$row['SENT_TO'] = ($row['BASIS']==50?$buyer_dtls_arr[$row['SENT_TO']]:$supplier_dtls_arr[$row['SENT_TO']]);
					$row['SENT_TO'] = $company_library[$row['SENT_TO']];
					$row['LOCATION_NAME'] = $location_arr[$row['LOCATION_ID']];
				}

				//for gate pass info
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_id'] = $row['SYS_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_company'] = $company_library[$row['COMPANY_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['from_location'] =$location_arr[ $row['COM_LOCATION_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['gate_pass_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$row['CHALLAN_NO']]['returnable'] = $yes_no[$row['RETURNABLE']];
				$gatePassDataArr[$row['CHALLAN_NO']]['est_return_date'] = $row['EST_RETURN_DATE'];

				$gatePassDataArr[$row['CHALLAN_NO']]['to_company'] = $row['SENT_TO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['to_location'] = $row['LOCATION_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_kg'] += $row['QUANTITY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['delivery_bag'] += $row['NO_OF_BAGS'];

				$gatePassDataArr[$row['CHALLAN_NO']]['department'] = $department_arr[$row['DEPARTMENT_ID']];
				$gatePassDataArr[$row['CHALLAN_NO']]['attention'] = $row['ATTENTION'];
				$gatePassDataArr[$row['CHALLAN_NO']]['issue_purpose'] = $row['ISSUE_PURPOSE'];
				$gatePassDataArr[$row['CHALLAN_NO']]['remarks'] = $row['REMARKS'];
				$gatePassDataArr[$row['CHALLAN_NO']]['carried_by'] = $row['CARRIED_BY'];
				$gatePassDataArr[$row['CHALLAN_NO']]['vhicle_number'] = $row['VHICLE_NUMBER'];
				$gatePassDataArr[$row['CHALLAN_NO']]['mobile_no'] = $row['MOBILE_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_name'] = $row['DRIVER_NAME'];
				$gatePassDataArr[$row['CHALLAN_NO']]['driver_license_no'] = $row['DRIVER_LICENSE_NO'];
				$gatePassDataArr[$row['CHALLAN_NO']]['security_lock_no'] = $row['SECURITY_LOCK_NO'];
			}
		}
	}

	//for gate out
	if($gate_pass_id != '')
	{
		$sql_gate_out="SELECT OUT_DATE, OUT_TIME FROM INV_GATE_OUT_SCAN WHERE STATUS_ACTIVE = 1 AND IS_DELETED = 0 AND INV_GATE_PASS_MST_ID='".$gate_pass_id."'";
		$sql_gate_out_rslt = sql_select($sql_gate_out);
		if(!empty($sql_gate_out_rslt))
		{
			foreach($sql_gate_out_rslt as $row)
			{
				$is_gate_out = 1;
				$gatePassDataArr[$system_no]['out_date'] = date('d-m-Y', strtotime($row['OUT_DATE']));
				$gatePassDataArr[$system_no]['out_time'] = $row['OUT_TIME'];
			}
		}
	}

	$sql = "SELECT sys_number, delevery_date, company_id, knitting_company, knitting_source, knitting_company, location_id, remarks,attention from pro_grey_prod_delivery_mst where id='".$data[3]."'";
	//echo $sql;die;
	$dataArray = sql_select($sql);
	$reqBookingNoArr = array();
	foreach($dataArray as $row)
	{
		$sys_number = $row[csf('sys_number')];
		$delevery_date = $row[csf('delevery_date')];
		$lc_company_id = $row[csf('company_id')];
		$knit_source = $row[csf('knitting_source')];
		$attention = $row[csf('attention')];
		$inhouse_location = $row[csf('location_id')];
		$remarks = $row[csf('remarks')];

		//for issue to
		$knitting_company = '';
		if ($row[csf('knitting_source')] == 1)
			$knitting_company = $company_library[$row[csf('knitting_company')]];
		else
			$knitting_company = $supplier_dtls_arr[$row[csf('knitting_company')]];
	}

	// ====
	$delivery_res = sql_select("SELECT a.barcode_num, sum(a.current_delivery) as current_delivery from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$mst_id and a.status_active=1 and a.is_deleted=0 group by a.barcode_num");
	foreach ($delivery_res as $val)
	{
		$barcode_nums .= $val[csf("barcode_num")].",";
		$qntyFromRoll[$val[csf("barcode_num")]] = $val[csf("current_delivery")];
	}
	$barcode_nums = chop($barcode_nums,",");

	$sql = "SELECT a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no as bwo, c.booking_without_order,c.is_sales ,sum(c.qnty) as current_delivery,count(c.barcode_no) as num_of_roll, sum(c.reject_qnty) as reject_qnty, sum(c.qc_pass_qnty_pcs) as delivery_qty_pcs, b.body_part_id, b.prod_id, d.detarmination_id
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, product_details_master d
	where a.id=b.mst_id and b.id=c.dtls_id and b.prod_id=d.id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums)
	group by  a.buyer_id,a.receive_basis, a.booking_no, a.knitting_source, a.knitting_company, a.location_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.stitch_length, b.brand_id, b.machine_dia, b.machine_gg, c.booking_no, c.booking_without_order, c.is_sales, b.body_part_id, b.prod_id, d.detarmination_id
	order by a.booking_no";
	// echo $sql;die;
	$sql_result = sql_select($sql);
	$feedar_prog_id="";
    $po_id_array = $sales_id_array = $booking_program_arr = array();
    foreach ($sql_result as $row)
    {
		if($row[csf("is_sales")] == 1){
			$sales_id_array[] = $row[csf("po_breakdown_id")];
		}else{
			$po_id_array[] = $row[csf("po_breakdown_id")];
		}

		if ($row[csf('receive_basis')] == 2) {
			$booking_program_arr[] = $row[csf("booking_no")];
			$productionYarnCount[$row[csf("booking_no")]] = $row[csf("yarn_count")];
		}else{
			$booking_no = explode("-", $row[csf('booking_no')]);
			$booking_program_arr[] = (int)$booking_no[3];
		}

	    $feedar_prog_id .= $row[csf("bwo")].",";
    }
	$feedar_prog_ids = chop($feedar_prog_id,",");
	//print_r($booking_program_arr);
	$planOrder = sql_select("SELECT a.id, b.booking_no, b.buyer_id, a.is_sales,a.machine_gg,a.machine_dia,a.feeder from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
	$plan_arr = array();
	foreach ($planOrder as $plan_row)
	{
		$plan_arr[$plan_row[csf("id")]]["machine_dia"] = $plan_row[csf("machine_dia")];
		$plan_arr[$plan_row[csf("id")]]["feeder"] = $plan_row[csf("feeder")];
		$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
	}

	$job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
    if(!empty($po_id_array))
    {
	    $job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
	    $job_sql_result = sql_select($job_sql);
	    foreach ($job_sql_result as $row)
	    {
			$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
			$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
			$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
			$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$job_company_arr[$row[csf('job_no_prefix_num')]]['company_name'] = $row[csf('company_name')];
	    }
    }

    if(!empty($sales_id_array))
    {
	    $sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no, buyer_id from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
		foreach ($sales_details as $sales_row)
		{
			$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
			$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
			$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
			$sales_arr[$sales_row[csf('id')]]['buyer_id'] = $sales_row[csf('buyer_id')];
			$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
		}
    }
    //$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
    $booking_details = sql_select("SELECT a.booking_no, a.buyer_id, b.job_no, b.po_break_down_id, c.grouping as ref_no,d.style_ref_no,d.sustainability_standard,d.fab_material from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_no_mst=d.job_no and a.status_active=1 and b.status_active=1 group by a.booking_no,a.buyer_id,b.job_no,b.po_break_down_id,c.grouping,d.style_ref_no,d.sustainability_standard,d.fab_material");

    foreach ($booking_details as $booking_row)
    {
		$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["buyer_id"] = $booking_row[csf("buyer_id")];
		$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
		$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
		$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["style_ref_no"] = $booking_row[csf("style_ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["internal_ref_no"] = $booking_row[csf("ref_no")];
		$booking_arr[$booking_row[csf("booking_no")]]["sustainability_standard"] = $booking_row[csf("sustainability_standard")];
		$booking_arr[$booking_row[csf("booking_no")]]["fab_material"] = $booking_row[csf("fab_material")];
    }
	$reqs_array = array();
	$reqs_sql = sql_select("SELECT knit_id, requisition_no as reqs_no, sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 group by knit_id, requisition_no");
	foreach ($reqs_sql as $row)
	{
		$reqs_array[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
	}
	$ppl_count_feeder_sql = sql_select("SELECT b.id as prog_no, c.count_id, c.feeding_id , c.seq_no
		from ppl_planning_info_entry_dtls b,ppl_planning_count_feed_dtls c
		where  b.mst_id=c.mst_id and b.id=c.dtls_id and b.id in($feedar_prog_ids)
		and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  group by c.count_id,c.feeding_id ,c.seq_no,b.id order by c.seq_no");
	$ppl_count_feeder_array=array();
	foreach ($ppl_count_feeder_sql as $row)
	{
		$feeder_count=strlen($feeding_arr[$row[csf('feeding_id')]]);
		if($row[csf('feeding_id')]==0){ $dividerSign= "";} else{ $dividerSign= "_";}
		$ppl_count_feeder_array[$row[csf('prog_no')]]['count_id'] .= substr($feeding_arr[$row[csf('feeding_id')]],-$feeder_count,1).$dividerSign.$yarn_count_details[$row[csf('count_id')]].',';
	}
	$refno_data_array=array();$jobCountArr=array();
	/*echo "<pre>";
	print_r($sql_result);die;*/
	$ppl_feeding_id="";$ppl_count_id="";
    foreach ($sql_result as $row)
    {
	    $is_sales = $row[csf('is_sales')];
	    if($is_sales == 1)
	    {
		    $within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
		    if($within_group == 1)
		    {
				$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
				$job_no = $booking_arr[$booking_no]["job_no"];
				$po_id = $booking_arr[$booking_no]["po_break_down_id"];
				$style_ref_no = $job_array[$po_id]['style_ref_no'];
				$ref_no = $booking_arr[$po_id]["ref_no"];
				$buyer_id=$booking_arr[$booking_no]["buyer_id"];
		    }
		    else
		    {
				$job_no = "";
				$style_ref_no = "";
				$ref_no = "";
				$po="";
				$buyer_id=$sales_arr[$row[csf('po_breakdown_id')]]['buyer_id'];
				$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
		    }
	    }
	    else
	    {
			$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
			$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
			$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
			$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
			$buyer_id=$row[csf('buyer_id')];
			$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
	    }
	    $jobCountArr[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['job_no']]=array(job_no=>$job_no);


	    $refno_data_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['job_no']][$row[csf('febric_description_id')]][]=array(
		recv_number_prefix_num=>$row[csf('recv_number_prefix_num')],
		buyer_id=>$buyer_id,
		ref_no=>$ref_no,
		receive_basis=>$row[csf('receive_basis')],
		booking_id=>$row[csf('booking_id')],
		booking_no=>$booking_no,
		knitting_source=>$row[csf('knitting_source')],
		knitting_company=>$row[csf('knitting_company')],
		location_id=>$row[csf('location_id')],
		febric_description_id=>$row[csf('febric_description_id')],
		gsm=>$row[csf('gsm')],
		width=>$row[csf('width')],
		yarn_count=>$row[csf('yarn_count')],
		yarn_lot=>$row[csf('yarn_lot')],
		color_id=>$row[csf('color_id')],
		color_range_id=>$row[csf('color_range_id')],
		machine_no_id=>$row[csf('machine_no_id')],
		stitch_length=>$row[csf('stitch_length')],
		body_part_id=>$row[csf('body_part_id')],
		brand_id=>$row[csf('brand_id')],
		shift_name=>$row[csf('shift_name')],
		machine_gg=>$row[csf('machine_gg')],
		machine_dia=>$row[csf('machine_dia')],
		num_of_roll=>$row[csf('num_of_roll')],
		no_of_roll=>$row[csf('no_of_roll')],
		po_breakdown_id=>$row[csf('po_breakdown_id')],
		current_delivery=>$row[csf('current_delivery')],
		bwo=>$row[csf('bwo')],
		booking_without_order=>$row[csf('booking_without_order')],
		within_group=>$row[csf('within_group')],
		is_sales=>$row[csf('is_sales')],
		delivery_qty_pcs=>$row[csf('delivery_qty_pcs')],
		reject_qnty=>$row[csf('reject_qnty')],
		detarmination_id=>$row[csf('detarmination_id')],

		receive_date=>$row[csf('receive_date')],
		job_no=>$job_no,
		style_ref_no=>$style_ref_no,
		po=>$po_id
	    );//seq_no=>$row[csf('seq_no')],
    }
    // echo "<pre>"; print_r($refno_data_array);

    $colarCupArr= sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
	foreach($colarCupArr as $row)
	{
		$body_part_data_arr[$row[csf('id')]]['body_part_full_name']=$row[csf('body_part_full_name')];
		$body_part_data_arr[$row[csf('id')]]['body_part_type']=$row[csf('body_part_type')];

	}

	// For Coller and Cuff data
	$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs, c.barcode_no
	from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
	where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id, c.coller_cuff_size";
	// echo $sql_coller_cuff;
	$sql_coller_cuff_result = sql_select($sql_coller_cuff);
	foreach ($sql_coller_cuff_result as $row2)
	{
		if($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type']>0 && $row2[csf('qc_pass_qnty_pcs')]>0)
		{
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['no_of_roll'] += count($row2[csf('barcode_no')]);
		}
	}
	//echo "<pre>";
	//print_r($coller_data_arr);//die;
	//print_r($cuff_data_arr);die;

    //Without order booking
	$bookings_without_order="";
	foreach($refno_data_array as $refArr)
	{
		foreach ($refArr as $refDataArr)
		{
			foreach ($refDataArr as $row)
			{
				if ($row['booking_without_order']==1 && $row['receive_basis'] == 2)
				{
					$bookings_without_order.="'".$plan_arr[$row['bwo']]["booking_no"]."',";
				}
				if ($row['booking_without_order']==1 && $row['receive_basis'] != 2)
				{
					$bookings_without_order.="'".$row['bwo']."',";
				}
			}
		}
	}
	$bookings_without_order=chop($bookings_without_order,',');
		$non_order_booking_sql= sql_select("SELECT a.booking_no,a.buyer_id,a.grouping, b.style_id from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
			where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and a.booking_no in($bookings_without_order) group by  a.booking_no,a.buyer_id,a.grouping, b.style_id");
	foreach ($non_order_booking_sql as $row)
	{
	 	$style_id=$row[csf("style_id")];
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['buyer_id']=$row[csf('buyer_id')];
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['grouping']=$row[csf('grouping')];
	 	//$nonOrderBookingData_arr[$row[csf('booking_no')]]['style_id']=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['sustainability_std_id']=return_field_value("sustainability_std_id", "sample_development_mst", "id=$style_id");
	 	$nonOrderBookingData_arr[$row[csf('booking_no')]]['fabric_material_id']=return_field_value("fabric_material_id", "sample_development_mst", "id=$style_id");
	}
	//var_dump($nonOrderBookingData_arr);die;
    $nonOrderBookingStyle=return_field_value("style_ref_no", "sample_development_mst", "id=$style_id");


	$com_dtls = fnc_company_location_address($company, $store_location_id, 2);
	?>
	<style type="text/css">
		table tr td {
			font-size: 16px;
		}
		.rpt_table thead th{
			font-size: 16px;
		}
		.rpt_table tfoot th{
			font-size: 16px;
		}
	</style>
    <?php
	$data_array = sql_select("select image_location  from common_photo_library where master_tble_id='".$data[0]."' and form_name='company_details' and is_deleted=0 and file_type=1");

	$noOfCopy = "";
	for ($x = 1; $x <= $no_copy; $x++)
	{
		if($x==1)
		{
			$sup = 'st';
		}
		else if($x==2)
		{
			$sup = 'nd';
		}
		else if($x==3)
		{
			$sup = 'rd';
		}
		else
		{
			$sup = 'th';
		}

		$noOfCopy ="<span style='font-size:x-large;font-weight:bold'>".$x."<sup>".$sup."</sup> Copy</span>";
		?>

		<div style="width:1100px;">
			<table width="1100" cellspacing="0" align="right" border="0" style="margin-right:-10px;">
				<tr>
					<td align="left" width="50">
						<?
						foreach ($data_array as $img_row)
						{
							if ($data[5] != 1)
							{
								?>
								<img src='../../<? echo $com_dtls[2]; ?>' height='50' width='50' align="middle"/>
								<?
							}
							else
							{
								?>
								<img src='../<? echo $com_dtls[2]; ?>' height='50' width='50' align="middle"/>
								<?
							}
						}
						?>
					</td>
                    <td align="center" style="font-size:30px" colspan="3"><strong><? echo $com_dtls[0]."<br><span style=\"font-size:14px;\">".$com_dtls[1]."</span>"; ?></strong></td>
					<td width="110" align="right"><?php echo $noOfCopy.($is_gate_pass==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Pass Done</span>":'').($is_gate_out==1?"<br><span style=\"color:#F00;font-weight:bold;\">Gate Out Done</span>":''); ?></td>
				</tr>
				<tr>
					<td colspan="3" align="center" height="50" valign="middle" style="font-size:25px">
						<strong>Knit Greige Fabric Delivery Challan</strong>
						<?php
						if ($data[4] == 1)
						{
							?>
							<!-- <span style="color:#0F0; font-weight:bold;">[Approved]</span> -->
							<?php
						}
						?>
					</td>
				</tr>
			</table>
			<div style="width:100%;">
				<div style="clear:both;">
		            <table style="margin-right:-40px;" cellspacing="0" width="1120" border="1" rules="all" class="rpt_table">
						<tr>
							<td width="130"><strong>Delivery Challan No:</strong></td>
							<td width="200px"><? echo $sys_number; ?></td>
							<td width="130"><strong>Delivery Date:</strong></td>
							<td width="200px"><? echo change_date_format($delevery_date); ?></td>
							<td width="120"><strong>Floor Name:</strong></td>
							<td><?
								$floorIdArr =array();$field_ids="";
								$floorIdArr = explode(",", $floor_id);
								foreach ($floorIdArr as $floor_id)
								{
									$field_ids .= $floor_name_arr[$floor_id].",";
								}
								$field_ids = chop($field_ids,",");
								echo $field_ids; ?>
							</td>
						</tr>
						<tr>
							<td><strong>LC Company:</strong></td>
							<td><? echo $company_library[$lc_company_id]; ?></td>
							<td><strong>Knitting Source:</strong></td>
							<td><? echo $knitting_source[$knit_source]; ?></td>
							<td><strong>Attention:</strong></td>
							<td><? echo $attention; ?></td>
						</tr>
						<tr>
							<td><strong>Knitting Company:</strong></td>
							<td><? echo $knitting_company; ?></td>
							<td><strong>Location:</strong></td>
							<td colspan="3"><? echo $location_arr[$inhouse_location]; ?></td>
						</tr>
						<tr>
							<td><strong>Remarks:</strong></td>
							<td colspan="5"><? echo $remarks; ?></td>
						</tr>
						<tr>
							<td align="center" colspan="6" id="barcode_img_id_<?php echo $x; ?>" height="50" valign="middle" style="border-left:hidden;border-right:hidden;border-bottom:hidden;"></td>
						</tr>
					</table>

					<table style="margin-right:-40px;" cellspacing="0" width="1120" border="1" rules="all" class="rpt_table">
						<thead bgcolor="#dddddd">

							<tr>
								<th rowspan="2" width="20">SL</th>
								<th rowspan="2" width="120">Buyer, Job, <br>Style and<br>Booking</th>
								<th rowspan="2" width="45">Body Part</th>
								<th rowspan="2" width="210">Fabric Details</th>
								<th rowspan="2" width="65">Color Range</th>
								<th rowspan="2" width="180">Yarn Details</th>
								<th rowspan="2" width="60">Fab. Dia<br>& GSM</th>
								<th rowspan="2" width="50">MC DIA <br/> X <br/> M.GAUGE</th>
								<th rowspan="2" width="60">S.L</th>
								<th colspan="2" width="120">Delivery Qty</th>
								<th rowspan="2" width="80">Roll Qty</th>
								<th rowspan="2">Reject Qty</th>
							</tr>
							<tr>
								<th width="60">KG</th>
								<th width="60">PCS</th>
							</tr>
						</thead>
                        <tbody>
							<?
							$i=1;$sub_group_arr=array();

							foreach($refno_data_array as $job => $jobArr)
							{
								$sub_tot_qty = 0;
								$sub_total_no_of_roll=0;
								foreach ($jobArr as $febricDescDataArr)
								{
									$sub_total_no_of_roll_fabric = $sub_tot_qty_fabric = 0;
									foreach ($febricDescDataArr as $row)
									{
										if ($i % 2 == 0)
											$bgcolor = "#E9F3FF";
										else
											$bgcolor = "#FFFFFF";

										$count = '';
										$yarn_count = explode(",", $row['yarn_count']);
										foreach ($yarn_count as $count_id) {
											if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
										}

										if ($row['receive_basis'] == 1) {
											$booking_no = explode("-", $row['booking_no']);
											$prog_book_no = (int)$booking_no[3];
										} else {
											$prog_book_no = $row['bwo'];
										}

										if ($row['receive_basis'] == 2)
										{
											$ppl_count_ids="";
											$countID=explode(",", $productionYarnCount[$row['bwo']]);
											foreach ($countID as $count_ids) {
												$ppl_count_ids.=$yarn_count_details[$count_ids].",";
											}
											$ppl_count_id =chop($ppl_count_ids,',');

											/*$ppl_count_ids=$ppl_count_feeder_array[$row['bwo']]['count_id'];
											$ppl_count_id =chop($ppl_count_ids,',');*/
										}
										else if ($row['receive_basis'] == 1)
										{
											if ($row['booking_without_order'] == 1) {
												//$ppl_count_id =$yarn_count_details[$row['yarn_count']];
												$yarn_count = explode(",", $row['yarn_count']);
												$ppl_count_id="";
												foreach ($yarn_count as $count_id) {
													if ($ppl_count_id == '') $ppl_count_id = $yarn_count_details[$count_id]; else $ppl_count_id .= "," . $yarn_count_details[$count_id];
												}
											}
										}
										$fab_material=array(1=>"Organic",2=>"BCI");
										$buyer=$jobNo=$style=$booking=$sustainability=$material="";
										if ($row['receive_basis'] == 1)
										{
											if($row['booking_without_order']==1)
											{
												$buyer=$buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
												//$sustainability=$nonOrderBookingData_arr[$row['bwo']]['sustainability_std_id'];

											}
											else
											{
												$buyer=$buyer_array[$row['buyer_id']];
												//$sustainability=$nonOrderBookingData_arr[$row['bwo']]['sustainability_std_id'];
											}

											if($row['booking_without_order']==1)
											{
												$jobNo = "";
												$style = "";
												$sustainability = "";
												$material = "";
											}
											else
											{
												$jobNo = $booking_arr[$row['bwo']]["job_no"];
												$style = $booking_arr[$row['bwo']]["style_ref_no"];
												$sustainability = $sustainability_standard[$booking_arr[$row['bwo']]["sustainability_standard"]];
												$material = $fab_material[$booking_arr[$row['bwo']]["fab_material"]];
											}
										}
										else
										{
											if ($row['is_sales'] == 1)
											{
												$buyer = $buyer_array[$row['buyer_id']];
											}
											else
											{
												if($row['booking_without_order']==1)
												{
													if($row['receive_basis'] == 2 && $row['is_sales'] == 2)
													{
														$buyer = $buyer_array[$nonOrderBookingData_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
													}
													else
													{
														$buyer = $buyer_array[$nonOrderBookingData_arr[$row['bwo']]['buyer_id']];
														//$sustainability=$nonOrderBookingData_arr[$row['bwo']]['sustainability_std_id'];
													}
												}
												else
												{
													$buyer = $buyer_array[$booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["buyer_id"]];
												}
											}
											if($row['receive_basis'] == 2)
											{
												if ($row['is_sales'] == 2)
												{
													$style = $nonOrderBookingStyle;

												}
												else
												{
													$jobNo = $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["job_no"];
													$style = $booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["style_ref_no"];
													$sustainability = $sustainability_standard[$booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["sustainability_standard"]];
													$material = $fab_material[$booking_arr[$plan_arr[$prog_book_no]["booking_no"]]["fab_material"]];
												}
											}
											else
											{
												$jobNo = $booking_arr[$row['bwo']]["job_no"];
												$style = $booking_arr[$row['bwo']]["style_ref_no"];
												$sustainability = $sustainability_standard[$booking_arr[$row['bwo']]["sustainability_standard"]];
												$material = $fab_material[$booking_arr[$row['bwo']]["fab_material"]];
											}
										}

										if ($row['receive_basis'] == 2)
										{
											$booking = $plan_arr[$prog_book_no]["booking_no"];
										}
										else
										{
											$booking = $row['bwo'];
											$sustainability=$sustainability_standard[$nonOrderBookingData_arr[$row['bwo']]['sustainability_std_id']];
											$material=$fab_material[$nonOrderBookingData_arr[$row['bwo']]['fabric_material_id']];
										}

										?>
			                            <tr bgcolor="<? echo $bgcolor; ?>">
			                                <td style="font-size: 15px" title="order type: <? echo $row['booking_without_order']; ?>"><? echo $i; ?></td>
			                                <td style="font-size: 15px">
			                                    <div style="word-wrap:break-word; width:130px"><?
			                                    echo $buyer.' ::<br>'.$jobNo.' ::<br>'.$style.' ::<br>'.$booking.' ::<br>'.$sustainability.' ::'.$material; ?>
			                                    </div>
			                                </td>
			                                <td style="font-size: 15px">
			                                    <div style="word-wrap:break-word; width:45px"><? echo $body_part[$row['body_part_id']]; ?></div>
			                                </td>
			                                <td style="font-size: 15px" title="<? echo $row['febric_description_id']; ?>">
			                                    <div style="word-wrap:break-word; width:210px">
			                                        <?
													$color_id_arr = array_unique(explode(",", $row["color_id"]));
													$all_color_name = "";
													foreach ($color_id_arr as $c_id) {
														$all_color_name .= $color_arr[$c_id] . ",";
													}
													$all_color_name = chop($all_color_name, ",");
													echo $all_color_name.' :: '.$composition_arr[$row['febric_description_id']]; ?>
			                                    </div>
			                                </td>
			                                <td style="font-size: 15px">
			                                    <div style="word-wrap:break-word; width:65px"><? echo $color_range[$row["color_range_id"]]; ?></div>
			                                </td>
			                                <td style="font-size: 15px" title="Yarn Dtls:<? echo $row['detarmination_id']; ?>">
			                                    <div style="word-wrap:break-word; width:180">
			                                        <? echo $ppl_count_id.', '.$yarn_composition_arr[$row['febric_description_id']].', '.$row['yarn_lot'].', '.$brand_details[$row['brand_id']]; ?>
			                                    </div>
			                                </td>
			                                <td style="font-size: 15px">
			                                    <div style="word-wrap:break-word; width:60px">
			                                        <? echo $row['width'].' & '.$row['gsm']; ?>
			                                    </div>
			                                </td>
			                                <td style="font-size: 15px">
			                                    <div style="word-wrap:break-word; width:65px;text-align: center;"><? echo $row['machine_dia'].'X'.$row['machine_gg']; ?></div>
			                                </td>
			                                <td style="font-size: 15px" align="center">
			                                    <div style="word-wrap:break-word; width:60px"><? echo $row['stitch_length']; ?></div>
			                                </td>
			                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? echo number_format($row['current_delivery'], 2, '.', ''); ?></td>
			                                <td style="font-size: 15px" align="right" style="font-size: 15px;"><? echo number_format($row['delivery_qty_pcs'], 2, '.', '') ?></td>
			                                <td style="font-size: 15px" align="right"><? echo $row['num_of_roll']; ?></td>
			                                <td style="font-size: 15px" align="right">
			                                    <div style="word-wrap:break-word; width:60px"><? echo number_format($row['reject_qnty'], 2, '.', ''); ?></div>
			                                </td>
			                            </tr>
										<?
										$sub_tot_qty_fabric += $row['current_delivery'];
										$sub_total_delivery_qty_pcs_fabric += $row['delivery_qty_pcs'];
										$sub_total_no_of_roll_fabric += $row['num_of_roll'];
										$sub_total_reject_qnty_fabric += $row['reject_qnty'];

										$sub_tot_qty += $row['current_delivery'];
										$sub_total_delivery_qty_pcs_qnty += $row['delivery_qty_pcs'];
										$sub_total_no_of_roll += $row['num_of_roll'];
										$sub_total_reject_qnty += $row['reject_qnty'];

										$i++;
										$grnd_tot_qty+=$row['current_delivery'];
										$grnd_total_delivery_qty_pcs_qnty+=$row['delivery_qty_pcs'];
										$grnd_total_no_of_roll+=$row['num_of_roll'];
										$grnd_total_reject_qnty+=$row['reject_qnty'];
									}
									?>
									<tr class="tbl_bottom">
										<td colspan="9" style=" text-align:right;font-size: 15px;"><strong>Fabric Type Total</strong></td>
										<td align="right" style="font-size: 15px;">
											<b><? echo number_format($sub_tot_qty_fabric, 2, '.', ''); ?></b>
										</td>
										<td align="right" style="font-size: 15px;"><? echo number_format($sub_total_delivery_qty_pcs_fabric, 2, '.', ''); ?></td>
										<td align="right" style="font-size: 15px;"><? echo number_format($sub_total_no_of_roll_fabric, 2, '.', ''); ?></td>
										<td align="right" style="font-size: 15px;"><? echo number_format($sub_total_reject_qnty_fabric, 2, '.', ''); ?></td>
									</tr>
									<?
								}
								// echo "<pre>";print_r($refno_data_array);
								if ($row['booking_without_order']==0)
	                            {
								?>
								<tr class="tbl_bottom">
									<td colspan="9" style=" text-align:right;font-size: 15px;"><strong>Job Total</strong></td>
									<td align="right" style="font-weight: bold;font-size: 15px;"><? echo number_format($sub_tot_qty,2); ?></td>
									<td align="right" style="font-weight: bold;font-size: 15px;"><? echo number_format($sub_total_delivery_qty_pcs_qnty,2); ?></td>
									<td align="right" style="font-weight: bold;font-size: 15px;"><? echo number_format($sub_total_no_of_roll,2); ?></td>
									<td align="right" style="font-weight: bold;font-size: 15px;"><? echo number_format($sub_total_reject_qnty,2); ?></td>
								</tr>
								<?
								}
							}

							?>
							<tr class="tbl_bottom">
								<?
								if ($row['booking_without_order']==0)
	                            {
	                            	?>
									<td style="font-size: 16px;" colspan="2" align="center"><b>Total Job:
		                            <?php
		                            	if(!empty($jobCountArr))
										{
			                           		echo " ".count($jobCountArr);
										}
									?></b></td>
									<?
								}
								else
								{
									?>
									<td colspan="2"></td>
									<?

								}?>
	                            <td align="right"></td>
								<td align="right" style="font-size: 16px;" colspan="6"><strong>Grand Total</strong></td>
								<td align="center" style="font-weight: bold; font-size: 16px;"><? echo number_format($grnd_tot_qty, 2, '.', ''); ?></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grnd_total_delivery_qty_pcs_qnty, 2, '.', ''); ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grnd_total_no_of_roll, 2, '.', ''); ?></strong></td>
								<td align="right" style="font-size: 16px;"><strong><? echo number_format($grnd_total_reject_qnty, 2, '.', ''); ?></strong></td>
							</tr>
	                    </tbody>
                    </table>
                    <br>
                    <!-- =========== Collar and Cuff Details Start ============= -->
                    <?
			    	//echo '<pre>';print_r($coller_cuff_data_arr);
					$CoCu=1;
					foreach($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr)
					{
						if( count($booking_data_arr)>0)
						{
						    ?>
			                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left; margin-bottom:10px;">
			                	<thead bgcolor="#dddddd">
				                    <tr>
				                        <th colspan="3"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name'];?> Details</th>
				                    </tr>
				                    <tr>
				                        <th>Size</th>
				                        <th>Qty Pcs</th>
				                        <th>No. of Roll</th>
				                    </tr>
			                	</thead>
			                    <?
			                    $coller_cuff_qty_total=$coller_cuff_roll_total=0;
			                    foreach($booking_data_arr as $bookingId => $bookingData )
			                    {
			                        foreach($bookingData as $jobId => $jobData )
			                        {
			                            foreach($jobData as $size => $row )
			                            {
			                                ?>
			                                <tr>
			                                    <td align="center"><? echo $size;?></td>
			                                    <td align="center"><? echo $row['qc_pass_qnty_pcs'];?></td>
			                                    <td align="center"><? echo $row['no_of_roll'];?></td>
			                                </tr>
			                                <?
			                                $coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
			                                $coller_cuff_roll_total += $row['no_of_roll'];
			                            }
			                        }
			                    }
			                    ?>
			                    <tr>
			                        <td align="right"><b>Total</b></td>
			                        <td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
			                        <td align="center"><b><? echo $coller_cuff_roll_total; ?></b></td>
			                    </tr>
			                </table>
						    <?
							if($CoCu==1){
								echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"3\">&nbsp;</td></tr></table>";
							}
							$CoCu++;
						}
					}
					?>
					<!-- =========== Collar and Cuff Details End ============= -->

                    <!-- ============= Gate Pass Info Start ========= -->
					<table style="margin-right:-40px;" cellspacing="0" width="1220" border="1" rules="all" class="rpt_table">
                        <tr>
                        	<td colspan="15" height="30" style="border-left:hidden;border-right:hidden; text-align: center;">For mishandling or other reason no claim is acceptable in any stage, once the Goods is received in good condition and quality and out from factory premises.</td>
                        </tr>
                        <tr>
                        	<td colspan="4" align="center" valign="middle" style="font-size:25px;"><strong>&lt;&lt;Gate Pass&gt;&gt;</strong></td>
                            <td colspan="9" align="center" valign="middle" id="gate_pass_barcode_img_id_<?php echo $x; ?>" height="50"></td>
                        </tr>
                        <tr>
                        	<td colspan="2" title="<? echo $system_no; ?>"><strong>From Company:</strong></td>
                        	<td colspan="2" width="120"><?php echo $gatePassDataArr[$system_no]['from_company']; ?></td>

                        	<td colspan="2"><strong>To Company:</strong></td>
                        	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['to_company']; ?></td>

                        	<td colspan="3"><strong>Carried By:</strong></td>
                        	<td colspan="3" width="120"><?php echo $gatePassDataArr[$system_no]['carried_by']; ?></td>
                        </tr>
                        <tr>
                        	<td colspan="2"><strong>From Location:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['from_location']; ?></td>
                        	<td colspan="2"><strong>To Location:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['to_location']; ?></td>
                        	<td colspan="3"><strong>Driver Name:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_name']; ?></td>
                        </tr>
                        <tr>
                        	<td colspan="2"><strong>Gate Pass ID:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_id']; ?></td>
                        	<td colspan="2" rowspan="2"><strong>Delivery Qnty</strong></td>
                        	<td align="center"><strong>Kg</strong></td>
                        	<td align="center"><strong>Roll</td>
                        	<td align="center"><strong>PCS</td>
                        	<td colspan="3"><strong>Vehicle Number:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['vhicle_number']; ?></td>
                        </tr>
                        <tr>
                        	<td colspan="2"><strong>Gate Pass Date:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['gate_pass_date']; ?></td>
                        	<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_kg']; ?></td>
                        	<td align="center"><?php echo $gatePassDataArr[$system_no]['delivery_bag']; ?></td>
                        	<td align="center"><?php
                        	if ($gatePassDataArr[$system_no]['gate_pass_id'] !="")
                        	{
                        		echo number_format($grnd_total_delivery_qty_pcs_qnty, 2, '.', '');
                        	}
                        	?></td>
                        	<td colspan="3"><strong>Driver License No.:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['driver_license_no']; ?></td>
                        </tr>
                        <tr>
                        	<td colspan="2"><strong>Out Date:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_date']; ?></td>
                        	<td colspan="2"><strong>Dept. Name:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['department']; ?></td>
                        	<td colspan="3"><strong>Mobile No.:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['mobile_no']; ?></td>
                        </tr>
                        <tr>
                        	<td colspan="2"><strong>Out Time:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['out_time']; ?></td>
                        	<td colspan="2"><strong>Attention:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['attention']; ?></td>
                        	<td colspan="3"><strong>Sequrity Lock No.:</strong></td>
                        	<td colspan="3"><?php echo $gatePassDataArr[$system_no]['security_lock_no']; ?></td>
                        </tr>
                        <tr>
                        	<td colspan="2"><strong>Returnable:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['returnable']; ?></td>
                        	<td colspan="2"><strong>Purpose:</strong></td>
                        	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['issue_purpose']; ?></td>
                        </tr>
                        <tr>
                        	<td colspan="2"><strong>Est. Return Date:</strong></td>
                        	<td colspan="2"><?php echo $gatePassDataArr[$system_no]['est_return_date']; ?></td>
                        	<td colspan="2"><strong>Remarks:</strong></td>
                        	<td colspan="9"><?php echo $gatePassDataArr[$system_no]['remarks']; ?></td>
                        </tr>
                    </table>
                    <!-- ============= Gate Pass Info End =========== -->
				</div>
				<br>
				<? echo signature_table(125, $company, "1200px"); ?>
			</div>
		</div>
		<script type="text/javascript" src="../../js/jquery.js"></script>
        <script type="text/javascript" src="../../js/jquerybarcode.js"></script>
		<script>
			function generateBarcode(valuess)
			{
				var zs = '<?php echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#barcode_img_id_"+zs).html('11');
				value = {code: value, rect: false};
				$("#barcode_img_id_"+zs).show().barcode(value, btype, settings);
			}
			generateBarcode('<? echo $data[1]; ?>');

			//for gate pass barcode
			function generateBarcodeGatePass(valuess)
			{
				var zs = '<?php echo $x; ?>';
				var value = valuess;//$("#barcodeValue").val();
				var btype = 'code39';//$("input[name=btype]:checked").val();
				var renderer = 'bmp';// $("input[name=renderer]:checked").val();
				var settings = {
					output: renderer,
					bgColor: '#FFFFFF',
					color: '#000000',
					barWidth: 1,
					barHeight: 30,
					moduleSize: 5,
					posX: 10,
					posY: 20,
					addQuietZone: 1
				};
				$("#gate_pass_barcode_img_id_"+zs).html('11');
				value = {code: value, rect: false};
				$("#gate_pass_barcode_img_id_"+zs).show().barcode(value, btype, settings);
			}

			if('<? echo $gatePassDataArr[$system_no]['gate_pass_id']; ?>' != '')
			{
				generateBarcodeGatePass('<? echo strtoupper($gatePassDataArr[$system_no]['gate_pass_id']); ?>');
			}
		</script>
        <div style="page-break-after:always;"></div>
    	<?php
	}
    exit();
}

if($action == "check_box")
{
	extract($_REQUEST);
	
	$variable_textile_sales_maintain = sql_select("select production_entry, process_loss_editable from variable_settings_production where company_name=$company_id and variable_list=66 and status_active=1");
	// var_dump($variable_textile_sales_maintain[0][csf('production_entry')]);
	
	if($variable_textile_sales_maintain[0][csf('production_entry')] ==2) 
	{
		$textile_sales_maintain = 1;
	 } 
	 else 
	{
	 $textile_sales_maintain = 0;
	}

	echo $textile_sales_maintain;
}

if ($action=="grey_delivery_print_22") //GMS
{
 	extract($_REQUEST);
 	$data = explode('*', $data);
 	$company = $data[0];
 	$txt_challan_no = $data[1];
 	$update_id = $data[2];
 	$kniting_source = $data[4];
 	$floor_name = $data[5];
 	$organicyesno = $data[6];
	
 	$company_array = array();
 	$company_data = sql_select("SELECT id, company_name, company_short_name from lib_company");
 	foreach ($company_data as $row)
 	{
 		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
 		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
 	}

 	$machine_details = array();
 	$machine_sql = sql_select("SELECT id, machine_no, dia_width, gauge from lib_machine_name");
 	foreach ($machine_sql as $row)
 	{
 		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
 		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
 		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
 	}

 	$color_arr = return_library_array("SELECT id, color_name from lib_color", 'id', 'color_name');
 	$supplier_arr = return_library_array("SELECT id, short_name from lib_supplier", "id", "short_name");
 	$buyer_array = return_library_array("SELECT id, short_name from lib_buyer", "id", "short_name");
 	$yarn_count_details = return_library_array("SELECT id,yarn_count from lib_yarn_count", "id", "yarn_count");
 	$machine_details_arr = return_library_array("SELECT id, machine_no from lib_machine_name", "id", "machine_no");
 	$brand_details = return_library_array("SELECT id, brand_name from lib_brand", "id", "brand_name");
 	$location_arr = return_library_array("SELECT id, location_name from lib_location", "id", "location_name");
 	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
 	
	
 	$smn_booking_style_arr=return_library_array( "select booking_no, style_ref_no from wo_non_ord_samp_booking_dtls a, sample_development_mst b where a.style_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by booking_no, style_ref_no", "booking_no", "style_ref_no");

 	$mstData = sql_select("SELECT company_id, delevery_date, knitting_source, knitting_company,location_id, remarks from pro_grey_prod_delivery_mst where id=$update_id");
	
 	$job_array = array();
 	//$job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no, a.style_ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
 	$job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no, b.grouping as ref_no, a.style_ref_no
 	from wo_po_details_master a, wo_po_break_down b
 	where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
 	$job_sql_result = sql_select($job_sql);
 	foreach ($job_sql_result as $row)
 	{
 		$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
 		$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
 		$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
 		$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
 		$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
 		$job_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
 		$job_array[$row[csf('id')]]['booking_no'] = $row[csf('id')];
		
 	}
 	//print_r($job_array);
	
	
 	$composition_arr = array();
 	$sql_deter = "SELECT a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
 	$data_array = sql_select($sql_deter);
 	foreach ($data_array as $row)
 	{
 		if (array_key_exists($row[csf('id')], $composition_arr))
 		{
 			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
 		}
 		else
 		{
 			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
 		}
 	}
	

 	$yarn_lot_arr = array();
 	$yarn_lot_sql=sql_select("SELECT id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id,yarn_type,supplier_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0");
 	foreach ($yarn_lot_sql as $value)
 	{
 		if($value[csf('yarn_comp_percent2nd')])
 		{
 			$production_wise_yarn_dtls[$value[csf('id')]]=$yarn_count_details[$value[csf('yarn_count_id')]].','.$value[csf('lot')].", ".$composition[$value[csf('yarn_comp_type1st')]].' '.$value[csf('yarn_comp_percent1st')].'%'." ".$composition[$value[csf('yarn_comp_type2nd')]]. ", " . $value[csf('yarn_comp_percent2nd')] . "%" .', '.$yarn_type[$value[csf('yarn_type')]].", ".$supplier_arr[$value[csf("supplier_id")]];
 		}
 		else
 		{
 			$production_wise_yarn_dtls[$value[csf('id')]]=$yarn_count_details[$value[csf('yarn_count_id')]].','.$value[csf('lot')].", ".$composition[$value[csf('yarn_comp_type1st')]].' '.$value[csf('yarn_comp_percent1st')].'%'.', '.$yarn_type[$value[csf('yarn_type')]].", ".$supplier_arr[$value[csf("supplier_id")]];
 		}
 	}


 	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
 	$com_dtls = fnc_company_location_address($company, $location, 2);


 	?>
 	<div style="width:1820px;">
 		<table width="1290" cellspacing="0" border="0">
 			<tr>
 				<td rowspan="3" width="10">
 					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
 				</td>
 				<td align="center" style="font-size:x-large">
 					<strong ><? echo $com_dtls[0]; ?></strong>
					 <!-- style="margin-right:300px" -->
 				</td>

 				<td>
					<?php
					if($organicyesno==1)
					{
						?>
						<div style="border: 2px solid #000; padding: 0px; color: #000;float: right; margin-right: 10px;  width: 235px; text-align: center;">ORGANIC</div>
						<?
					}else{
						echo "&nbsp;";
					}
					?>
				</td>
 			</tr>
 			<tr>
				<td align="center" style="font-size:14px">
					<strong ><? echo $com_dtls[1]; ?></strong>
					<!-- style="margin-right:400px" -->
				</td>
			</tr>
 			<tr>
 				<td align="center" style="font-size:18px">
 					<strong ><u>Knitting Grey Fabric Delivery Challan</u></strong>
					 <!-- style="margin-right:300px" -->
 				</td>
 			</tr>
 			<tr>
 				<td align="center" style="font-size:16px">
 					<strong style="margin-right:300px"><u>Knitting Section</u></strong>
 				</td>
 			</tr>
 		</table>
 		<br>
 		<table width="1590" cellspacing="0" align="center" border="0">
 			<tr>
 				<td style="font-size:16px; font-weight:bold;" width="100">Challan No</td>
 				<td width="200">:&nbsp;<? echo $txt_challan_no; ?></td>
 				<td style="font-size:16px; font-weight:bold;" width="70" >Location</td>
 				<td valign="button"  id="location_td" style="float:left;" colspan="3"  width="400">:&nbsp;<? echo $location_arr[$mstData[0][csf('location_id')]]; ?></td>
				<td></td>
 				<td width="710" id="barcode_img_id" align="left" rowspan="2" colspan="2"></td>
 			</tr>
 			<tr>
 				<td width="100" style="font-size:16px; font-weight:bold;">Delivery Date</td>
 				<td width="200">:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
 				<td style="font-size:16px; font-weight:bold;" width="70">Floor No</td>
 				<td  style="width:150px">:&nbsp;<? echo $floor_name; ?></td>
				<td style="font-size:16px; font-weight:bold;" colspan="2" width="300">Remarks:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>

 			</tr>
 		</table>
 		<br>
 		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="2010" class="rpt_table">
 			<thead>
 				<tr>
 					<th width="30" style="word-break: break-all;word-wrap: break-word;">SL</th>
 					<th width="80" style="word-break: break-all;word-wrap: break-word;">Production Date</th>
 					<th width="40" style="word-break: break-all;word-wrap: break-word;">Shift</th>
 					<th width="55" style="word-break: break-all;word-wrap: break-word;">Prod.ID</th>
 					<th width="80" style="word-break: break-all;word-wrap: break-word;">Buyer</th>
 					<th width="60" style="word-break: break-all;word-wrap: break-word;">Program No</th>
 					<th width="100" style="word-break: break-all;word-wrap: break-word;">Booking No</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Body Part</th>
 					<th width="100" style="word-break: break-all;word-wrap: break-word;">Style</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Knit. Comp.</th>
 					<th width="170" style="word-break: break-all;word-wrap: break-word;">Yarn Details</th>
					 <th width="100" style="word-break: break-all;word-wrap: break-word;">Yarn Brand</th>
					 <th width="100" style="word-break: break-all;word-wrap: break-word;">Lot Type</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Fab Color</th>
 					<th width="150" style="word-break: break-all;word-wrap: break-word;">Fabric Type</th>
 					<th width="35" style="word-break: break-all;word-wrap: break-word;">Stich</th>
 					<th width="35" style="word-break: break-all;word-wrap: break-word;">GSM</th>
 					<th width="65" style="word-break: break-all;word-wrap: break-word;">Dia X GG</th>
 					<th width="60" style="word-break: break-all;word-wrap: break-word;">Fab. Dia</th>
 					<th width="100" style="word-break: break-all;word-wrap: break-word;">MC. No</th>
 					<th width="45" style="word-break: break-all;word-wrap: break-word;">No of Roll</th>
 					<th width="50" style="word-break: break-all;word-wrap: break-word;">Qnty. in Pcs</th>
 					<th width="90" style="word-break: break-all;word-wrap: break-word;">Deliv. QTY</th>
 				</tr>
 			</thead>
 			<?
 			$sql_dtls_knit = "SELECT e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
 			from  pro_grey_prod_entry_dtls d,  inv_receive_master e
 			where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
 			$result_arr = sql_select($sql_dtls_knit);
 			$machine_dia_guage_arr = array();
 			foreach ($result_arr as $row) {
 				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
 				$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
 			}

 			$i = 0;
 			$tot_qty = 0;
 			$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");

			if ($kniting_source == 1)//in-house
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as booking, c.booking_without_order,b.body_part_id,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs,e.seq_no,c.barcode_no , b.machine_dia, b.machine_gg , g.style_ref_no
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d, lib_machine_name e, wo_po_break_down f, wo_po_details_master g
				WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and b.machine_no_id=e.id and f.id=c.po_breakdown_id and f.job_no_mst=g.job_no and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0
				group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.po_breakdown_id , c.booking_no, c.booking_without_order,b.body_part_id,e.seq_no,c.barcode_no, b.machine_dia, b.machine_gg , g.style_ref_no order by e.seq_no";
			} 
			else 
			{
				$sql = "SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no, a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id,count(c.barcode_no) as num_of_roll,c.po_breakdown_id, sum(d.current_delivery) as current_delivery ,c.booking_no as booking, c.booking_without_order,b.body_part_id,sum(c.qc_pass_qnty_pcs) as grey_receive_qnty_pcs,c.barcode_no , b.machine_dia,b.machine_gg , f.style_ref_no 
				FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, pro_grey_prod_delivery_dtls d , wo_po_break_down e,
				wo_po_details_master f
				WHERE a.id=b.mst_id and b.id=c.dtls_id and c.id=d.roll_id and e.id=c.po_breakdown_id and e.job_no_mst=f.job_no  and d.entry_form=56 and d.mst_id=$update_id and a.entry_form=2 and c.entry_form=2 and d.status_active=1 and d.is_deleted=0
				group by a.recv_number_prefix_num, a.buyer_id, a.receive_basis, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,b.yarn_prod_id, c.po_breakdown_id , c.booking_no, c.booking_without_order,b.body_part_id,c.barcode_no, b.machine_dia,b.machine_gg , f.style_ref_no ";
			}
			// echo $sql; // ppl_planning_info_entry_mst wo_po_details_master wo_po_details_master pro_grey_prod_entry_dtls e.job_no_mst=f.job_no 
			$result = sql_select($sql);
			$all_barcode_no="";$orderIds=array();
			foreach($result as $row)
			{
				$all_barcode_no.=$row[csf("barcode_no")].",";
				$orderIds[$row[csf('po_breakdown_id')]]= $row[csf('po_breakdown_id')];

			}
			$orderIds_cond=" and po_break_down_id in (".implode(",",$orderIds).")";
			$order_booking_arr = return_library_array("SELECT po_break_down_id, booking_no from wo_booking_dtls where booking_type !=2 $orderIds_cond", "po_break_down_id", "booking_no");

			$all_barcode_no=implode(",",array_unique(explode(",",chop($all_barcode_no,","))));
			if($all_barcode_no!="")
			{
				$production_sql=sql_select("select b.barcode_no, a.booking_id, a.booking_no, a.receive_basis,a.knitting_source, a.knitting_company  from  inv_receive_master a, pro_roll_details b where a.id=b.mst_id and a.entry_form=2 and b.entry_form=2 and b.status_active=1 and b.barcode_no in($all_barcode_no)");

				$production_data_arr=array();
				foreach($production_sql as $row)
				{
					$production_data_arr[$row[csf("barcode_no")]]["receive_basis"]=$row[csf("receive_basis")];
					$production_data_arr[$row[csf("barcode_no")]]["booking_id"]=$row[csf("booking_id")];
					if($row[csf("receive_basis")]==2)
					{
						$all_program_no.=$row[csf("booking_id")].",";
					}
				}
			}
			$all_program_no=implode(",",array_unique(explode(",",chop($all_program_no,","))));
			if($all_program_no!="")
			{
				$program_sql=sql_select("select DISTINCT  a.booking_id, c.booking_no , f.job_no, f.style_ref_no from inv_receive_master a, ppl_planning_info_entry_dtls b, ppl_planning_info_entry_mst c , wo_booking_dtls d , wo_po_details_master f
				where a.booking_id=b.id and  d.booking_no = c.booking_no and b.mst_id=c.id and d.job_no = f.job_no and a.entry_form=2 and a.receive_basis=2 and a.booking_id in($all_program_no)");
				$prog_book_arr=array(); $prog_full_book_arr=array();
				foreach($program_sql as $row)
				{
					$prog_full_book_arr[$row[csf("booking_id")]]['booking_no']=$row[csf("booking_no")];
					$prog_full_book_arr[$row[csf("booking_id")]]['style_ref_no']=$row[csf("style_ref_no")];


				}
			}


			$loc_arr = array();
			$loc_nm = ": ";
			$style_po_fab_clr_arr=array(); $booking_arr=array();$sampleBookingNos ="";
			foreach ($result as $row)
			{
				$booking_arr_coller_cut[$row[csf('booking_id')]]['booking_no']=$row[csf('booking_no')];
				if($row[csf("booking_without_order")] == 1)
				{
					//unset($row[csf('po_breakdown_id')]);
					$sampleBookingNo = $row[csf('booking_no')];
					$sampleBookingNos .= "'".$row[csf('booking_no')]."'".",";

					$booking_arr[$row[csf('po_breakdown_id')]]['booking_no']=$row[csf('booking_no')];
					$booking_arr[$row[csf('po_breakdown_id')]]['booking_without_order']=$row[csf('booking_without_order')];
				}

				$yarn_prod_ids=explode(",", $row[csf('yarn_prod_id')]);
				$count = '';
				foreach($yarn_prod_ids as $vals)
				{
					if($count=="")
					{
						$count.="(".$production_wise_yarn_dtls[$vals].")";
					}
					else
					{
						$count.=" , (".$production_wise_yarn_dtls[$vals].")";
					}

				}

				if ($row[csf("knitting_source")] == 1)
				{
					$knit_company = $company_array[$row[csf("knitting_company")]]['shortname'];
				}
				else if ($row[csf("knitting_source")] == 3)
				{
					$knit_company = $supplier_arr[$row[csf("knitting_company")]];
				}

				if ($row[csf('receive_basis')] == 1)
				{
					$booking_no = explode("-", $row[csf('booking_no')]);
					$prog_book_no = (int)$booking_no[3];
				}
				else
				{
					$prog_book_no = $row[csf('booking_no')];
				}

				$color_id_arr = array_unique(explode(",", $row[csf("color_id")]));
				$all_color_name = "";
				foreach ($color_id_arr as $c_id)
				{
					$all_color_name .= $color_arr[$c_id] . ",";
				}
				$all_color_name = chop($all_color_name, ",");



				$lots = '';
				$yarn_lot = explode(",", $row[csf('yarn_lot')]);
				foreach ($yarn_lot as $lot_id)
				{
					if ($lots == '') $lots = $lot_id; else $lots .= "," . $lot_id;
				}



				if ($row[csf('receive_basis')] == 2)
				{
					$planOrder = sql_select("SELECT b.booking_no, a.is_sales, a.machine_gg, a.machine_dia from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
					$mc_dia = $planOrder[0][csf('machine_dia')].'X'.$planOrder[0][csf('machine_gg')];
				}
				else
				{
					//$mc_dia = $machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'].'X'.$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'];
					$mc_dia = $row[csf('machine_dia')].'X'.$row[csf('machine_gg')];
				}

			 	//$yarn_dtls=$count.','.$lots.','.$composition_string; 
				$yarn_dtls=$count;
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["receive_date"]=change_date_format($row[csf("receive_date")]);

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["shift_name"]=$shift_name[$row[csf("shift_name")]];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["knit_company"]=$knit_company;

				if($style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["recv_number_prefix_num"]=="")
				{
					$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["recv_number_prefix_num"]=$row[csf('recv_number_prefix_num')];
				}
				else
				{
					$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["recv_number_prefix_num"] .=',,,'.$row[csf('recv_number_prefix_num')];
				}

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["prog_book_no"]=$prog_book_no;

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["count"]=$yarn_dtls;

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["febric_description_id"]=$composition_arr[$row[csf('febric_description_id')]];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["stitch_length"]=$row[csf('stitch_length')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["gsm"]=$row[csf('gsm')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["machine_no_id"]=$row[csf('machine_no_id')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["width"]=$row[csf('width')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["width"]=$row[csf('width')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["num_of_roll"] +=$row[csf('num_of_roll')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["current_delivery"] +=$row[csf('current_delivery')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["mc_dia"] =$mc_dia;

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["buyer"]  =$buyer_array[$row[csf('buyer_id')]];
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["booking"]=$row[csf('booking')];
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["receive_basis"]=$row[csf('receive_basis')];
				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["barcode_no"]=$row[csf('barcode_no')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["fabric_color"]  =$all_color_name;

				//$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["body_part_id"]  =$body_part[$row[csf('body_part_id')]];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["grey_receive_qnty_pcs"]  +=$row[csf('grey_receive_qnty_pcs')];

				$style_po_fab_clr_arr[$job_array[$row[csf('po_breakdown_id')]]['style_ref_no']][$row[csf('po_breakdown_id')]][$all_color_name][$row[csf('buyer_id')]][$row[csf("receive_date")]][$row[csf("shift_name")]][$knit_company][$yarn_dtls][$row[csf('febric_description_id')]][$row[csf('stitch_length')]][$row[csf('gsm')]][$row[csf('width')]][$mc_dia][$row[csf('body_part_id')]]["brand_id"]  =$row[csf('brand_id')];
			}
			// echo '<pre>';print_r($style_po_fab_clr_arr);
			$sampleBookingNos=chop($sampleBookingNos,",");
			$sql_sample_style = "SELECT a.booking_no, a.style_id, b.style_ref_no FROM wo_non_ord_samp_booking_dtls a, sample_development_mst b WHERE booking_no in($sampleBookingNos) AND a.status_active=1 AND a.is_deleted=0 AND a.style_id=b.id  group by a.booking_no,a.style_id,b.style_ref_no";

			$sample_result = sql_select($sql_sample_style);

			$sample_style_arr = array();
			foreach($sample_result as $row)
			{
				$sample_style_arr[$row[csf('booking_no')]]['syle_ref'] = $row[csf('style_ref_no')];
			}

			$i=1;
			$gr_color_roll=0;
			$gr_color_qty =0;
			$gr_qntyInpcs =0;
			foreach($style_po_fab_clr_arr as $style=>$po_data)
			{
				foreach($po_data as $po_id=>$fabr_data)
				{
					$po_color_roll=0;
					$po_color_qty=0;
					$po_grey_qntyinPcs=0;

					foreach($fabr_data as $fabr_id=>$buyer_data)
					{
						$color_roll=0;
						$color_qty=0;
						$grey_qtyInPcs=0;
						foreach($buyer_data as $buyer_id=>$date_data)
						{
							foreach($date_data as $date_id=>$shift_data)
							{
								foreach($shift_data as $shift_id=>$knit_data)
								{
									foreach($knit_data as $knit_id=>$yarn_data)
									{
										foreach($yarn_data as $yarn_id=>$fabr_data)
										{
											foreach($fabr_data as $fabric_description_id=>$stich_data)
											{
												foreach($stich_data as $stich_id=>$gsm_data)
												{
													foreach($gsm_data as $gsm_id=>$width_data)
													{
														foreach($width_data as $width_id=>$mc_dia_data)
														{
															foreach($mc_dia_data as $mc_dia_id=>$mc_dia_idv)
															{
																foreach($mc_dia_idv as $body_part_id=>$row)
																{
																$color_roll+=$row['num_of_roll'];
																$color_qty+=$row['current_delivery'];
																$grey_qtyInPcs+=$row['grey_receive_qnty_pcs'];
																$po_color_roll+=$row['num_of_roll'];
																$po_color_qty+=$row['current_delivery'];
																$po_grey_qntyinPcs+=$row['grey_receive_qnty_pcs'];
																$gr_color_roll+=$row['num_of_roll'];
																$gr_color_qty+=$row['current_delivery'];
																$gr_qntyInpcs+=$row['grey_receive_qnty_pcs'];
																$smn_booking=""; $smn_style="";
																if($booking_arr[$po_id]['booking_without_order']==1) // without order
																{
																	$smn_booking=""; $smn_style="";
																	if($production_data_arr[$row[csf("booking")]]["receive_basis"]==1)//Fabric Booking
																	{
																		$smn_booking=$row[csf('booking')];
																		$smn_style=$smn_booking_style_arr[$row[csf('booking_no')]];
																		//WO_NON_ORD_SAMP_BOOKING_DTLS_table[$row[csf('booking')]]['style_des']
																	}
																	else
																	{
																		$smn_booking=$prog_full_book_arr[$production_data_arr[$row['barcode_no']]['booking_no']];
																		$smn_style=$smn_booking_style_arr[$prog_full_book_arr[$production_data_arr[$row['barcode_no']]['booking_no']]];
																	}
																}
																// var_dump($smn_booking);
																?>
																<tr style="font-family: Arial Narrow, Arial, sans-serif;">
																	<td width="30" align="center"><? echo $i; ?></td>
																	<td width="80" style="word-break:break-all;word-wrap: break-word;"><? echo  $row['receive_date']; ?></td>
																	<td width="40"
																	style="word-break:break-all;word-wrap: break-word;"><? echo  $row['shift_name']; ?></td>
																	<td width="55"><? echo implode(" , ",array_unique(explode(",,,", $row['recv_number_prefix_num']))); ?></td>
																	<td width="80" style=""><? echo $row['buyer']; ?></td>
																	<td width="60" align="center" style=""><? echo $row['prog_book_no']; ?></td>
																	<td width="90" title="<? echo $po_id; ?>" style="word-break:break-all;word-wrap: break-word;">
																		<?  //echo $orderOrBookin = ($job_array[$po_id]['po']!="")?$job_array[$po_id]['po']:$sampleBookingNo; ?>
																		<?
																		if ($booking_arr[$po_id]['booking_without_order']==1)
																		{

																			echo $smn_booking;//$sampleBookingNo = $booking_arr[$po_id]['booking_no'];
																		}
																		else
																		{
																			// echo $orderOrBookin = $job_array[$po_id]['po'].'<br>';
																			if($order_booking_arr[$job_array[$po_id]['booking_no']]!=null)
																			echo $prog_full_book_arr[$row['prog_book_no']]['booking_no']; 
																			else{
																				
																				$orderOrBookin = $order_booking_arr[$job_array[$po_id]['booking_no']];
																				if($orderOrBookin!=null){
																					echo "$orderOrBookin";
																				}
																				else{
																					echo $prog_full_book_arr[$row['prog_book_no']]['booking_no'];
																				}

																			}
																			
																		}
																		 ?>
																	</td>
																	<td width="90" style="word-break:break-all;word-wrap: break-word;"><? echo $body_part[$body_part_id];//$row['body_part_id']; ?>
																	</td>
																	<td title="<? echo $po_id; ?>" width="100" style="word-break:break-all;word-wrap: break-word;">
																	<?
																		if ($booking_arr[$po_id]['booking_without_order']==1)
																		{
																			echo $smn_style;//$styleRef =$sample_style_arr[$booking_arr[$po_id]['booking_no']]['syle_ref'];
																		}
																		else
																		{
																			// echo $styleRef = $style.'<br>'.$job_array[$po_id]['ref_no'];
																			//echo $job_array[$po_id]['ref_no'];
																			echo $prog_full_book_arr[$row['prog_book_no']]['style_ref_no'];
																		}
																		// echo $styleRef = ($style!="")?$style:$sample_style_arr[$sampleBookingNo]['syle_ref'];
																	?>
                                                                    </td>
																	<td width="90" style="word-break:break-all;word-wrap: break-word;"><? echo  $row['knit_company']; ?></td>
																	<?
																		$yarn_band_data ="";
																		$yarn_lot_data ="";
																		$yarn_dtls_data ="";

																		$yarn_dtls_arr = explode(" , ",$row['count']);
																		$yarn_raw_data=array();
																		foreach($yarn_dtls_arr as $value){
																			array_push($yarn_raw_data,ltrim($value,','));
																		}
																		foreach($yarn_raw_data as $value){
																			$yarn_count_data = explode(",",$value);
																			$yarn_dtls_data .= $yarn_count_data[0].",".$yarn_count_data[2].",".$yarn_count_data[3]."),";
																			$yarn_band_data .= rtrim($yarn_count_data[4],")").",";
																			$yarn_lot_data .= $yarn_count_data[1].",";
																			
																		}
																	 ?>
																	<td width="170" style=""><? 
																		echo rtrim($yarn_dtls_data,",");
																	?></td>
																	<td width="100" style=""><? 
																		echo $brand_details[$row['brand_id']];
																	?> </td>
																	<td width="100" style=""><? 
																		echo rtrim($yarn_lot_data,","); 
																	?> </td>

																	<td width="70" style="word-break:break-all;word-wrap: break-word;"><? echo $row['fabric_color']; ?></td>
																	<td width="150" style=""><? echo  $row['febric_description_id']; ?> </td>
																	
																	<td width="35" style="word-break:break-all;word-wrap: break-word;" align="center"><? echo  $row['stitch_length']; ?></td>
																	<td width="35" style="word-break:break-all;word-wrap: break-word;" align="center"><? echo  $row['gsm']; ?></td>
																	<td width="65" style="word-break:break-all;word-wrap: break-word;"  align="center"><? echo  $row['mc_dia'] ; ?></td>
																	<td width="60" style="word-break:break-all;word-wrap: break-word;" align="center"><? echo  $row['width']; ?></td>
																	<td width="100" align="center" style="word-break:break-all;word-wrap: break-word;"  align="right"><? echo $machine_details[$row['machine_no_id']]["machine_no"]; ?></td>
																	<td width="45" align="center" style="word-break:break-all;word-wrap: break-word;"  align="right"><? echo $row['num_of_roll']; ?></td>
																	<td width="45" align="center" style="word-break:break-all;word-wrap: break-word;"  align="right"><? echo $row['grey_receive_qnty_pcs']; ?></td>
																	<td width="90" style="word-break:break-all;word-wrap: break-word;" align="right"><? echo number_format($row['current_delivery'], 2); ?></td>
																</tr>

																<?
															    $i++;
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
						?>
						<tr>
							<td colspan="13" align="right">&nbsp;</td>
							<td colspan="7" align="left"><strong> Total <? echo "(".$row['fabric_color'].")";?></strong></td>
							<td align="center"><? echo $color_roll;?></td>
							<td align="right"><? echo number_format($grey_qtyInPcs,2);?></td>
							<td align="right"><? echo number_format($color_qty,2);?></td>
						</tr>
						<?
					}
					?>
					<tr>
						<td colspan="20" align="right"><strong>PO Total <? echo "(".$job_array[$po_id]['po'],")" ;?></strong></td>
						<td align="center"><strong><? echo $po_color_roll;?></strong></td>
						<td align="right"><strong><? echo number_format($po_grey_qntyinPcs,2);?></strong></td>
						<td align="right"><strong><? echo number_format($po_color_qty,2);?></strong></td>
					</tr>
					<?
					//$i++;
				}
			}
			?>
			<tr>
				<td colspan="20" align="right"><strong>Grand Total</strong></td>
				<td align="center"><strong><? echo $gr_color_roll;?></strong></td>
				<td align="right"><strong><? echo number_format($gr_qntyInpcs,2);?></strong></td>
				<td align="right"><strong><? echo number_format($gr_color_qty,2);?></strong></td>
			</tr>
			<tr>
				<td colspan="2" align="left"><b>Remarks:</b></td>
				<td colspan="25">&nbsp;</td>
			</tr>
		</table>
	</div>
	<br>
	<div style="width:1685px;">
    	<?
    	$barcode_res = sql_select("SELECT a.barcode_num from pro_grey_prod_delivery_dtls a where a.entry_form=56 and a.mst_id=$update_id and a.status_active = 1 and a.is_deleted = 0 group by a.barcode_num");
		foreach ($barcode_res as $val)
		{
			$barcode_nums .= $val[csf("barcode_num")].",";
		}
		$barcode_nums = chop($barcode_nums,",");

		$sql = "SELECT  a.receive_basis, a.booking_no, c.booking_no as bwo, c.is_sales
		    from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
		    where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums)
		    group by a.receive_basis, a.booking_no, c.booking_no, c.is_sales
		    order by a.booking_no";
		$sql_result = sql_select($sql);
		$po_id_array = $sales_id_array = $booking_program_arr = array();
	    foreach ($sql_result as $row)
	    {
			if($row[csf("is_sales")] == 1){
				$sales_id_array[] = $row[csf("po_breakdown_id")];
			}else{
				$po_id_array[] = $row[csf("po_breakdown_id")];
			}

			if ($row[csf('receive_basis')] == 2) {
				$booking_program_arr[] = $row[csf("booking_no")];
			}else{
				$booking_no = explode("-", $row[csf('booking_no')]);
				$booking_program_arr[] = (int)$booking_no[3];
			}
	    }

		$planOrder = sql_select("SELECT a.id, b.booking_no from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id in(".implode(",",$booking_program_arr).")");
		$plan_arr = array();
		foreach ($planOrder as $plan_row)
		{
			$plan_arr[$plan_row[csf("id")]]["booking_no"] = $plan_row[csf("booking_no")];
		}

		$job_array = $job_company_arr = $sales_arr = $sales_booking_arr = $booking_arr = array();
	    if(!empty($po_id_array))
	    {
		    $job_sql = "SELECT a.job_no_prefix_num, a.job_no, b.id, b.po_number, b.file_no,a.style_ref_no,company_name, b.grouping as ref_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in(".implode(",",$po_id_array).")";
		    $job_sql_result = sql_select($job_sql);
		    foreach ($job_sql_result as $row)
		    {
				$job_array[$row[csf('id')]]['job'] = $row[csf('job_no_prefix_num')];
				$job_array[$row[csf('id')]]['po'] = $row[csf('po_number')];
				$job_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$job_array[$row[csf('id')]]['ref_no'] = $row[csf('ref_no')];
				$job_array[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
		    }
	    }

	    if(!empty($sales_id_array))
	    {
		    $sales_details = sql_select("SELECT id, job_no, sales_booking_no, within_group, style_ref_no from fabric_sales_order_mst where id in(".implode(",",$sales_id_array).")");
			foreach ($sales_details as $sales_row)
			{
				$sales_arr[$sales_row[csf('id')]]['sales_booking_no'] = $sales_row[csf('sales_booking_no')];
				$sales_arr[$sales_row[csf('id')]]['within_group'] = $sales_row[csf('within_group')];
				$sales_arr[$sales_row[csf('id')]]['style_ref_no'] = $sales_row[csf('style_ref_no')];
				$sales_booking_arr[] = "'".$sales_row[csf('sales_booking_no')]."'";
			}
	    }
		    //$booking_cond = !empty($sales_booking_arr)? " and a.booking_no in (".implode(",",$sales_booking_arr).")" : " and b.po_break_down_id in (".implode(",",$po_id_array).")";
		$booking_details_sql = sql_select("SELECT a.booking_no, b.job_no, b.po_break_down_id, c.grouping as ref_no from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c where a.booking_no=b.booking_no and b.po_break_down_id=c.id and a.status_active=1 and b.status_active=1 group by a.booking_no,b.job_no,b.po_break_down_id,c.grouping");

	    foreach ($booking_details_sql as $booking_row)
	    {
			$booking_arr[$booking_row[csf("booking_no")]]["job_no"] = $booking_row[csf("job_no")];
			$booking_arr[$booking_row[csf("booking_no")]]["po_break_down_id"] = $booking_row[csf("po_break_down_id")];
			$booking_arr[$booking_row[csf("po_break_down_id")]]["booking_no"] = $booking_row[csf("booking_no")];
			$booking_arr[$booking_row[csf("po_break_down_id")]]["ref_no"] = $booking_row[csf("ref_no")];
			$booking_arr[$booking_row[csf("booking_no")]]["booking_ref_no"] = $booking_row[csf("ref_no")];
	    }

	    foreach ($sql_result as $row)
	    {
		    $is_sales = $row[csf('is_sales')];
		    if($is_sales == 1)
		    {
			    $within_group = $sales_arr[$row[csf('po_breakdown_id')]]['within_group'];
			    if($within_group == 1)
			    {
					$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
					$job_no = $booking_arr[$booking_no]["job_no"];
					$po_id = $booking_arr[$booking_no]["po_break_down_id"];
					$style_ref_no = $job_array[$po_id]['style_ref_no'];
					$ref_no = $booking_arr[$po_id]["ref_no"];
			    }
			    else
			    {
					$job_no = "";
					$style_ref_no = "";
					$ref_no = "";
					$po="";
					$booking_no = $sales_arr[$row[csf('po_breakdown_id')]]['sales_booking_no'];
			    }
		    }
		    else
		    {
				$ref_no=$job_array[$row[csf('po_breakdown_id')]]['ref_no'];
				$job_no=$job_array[$row[csf('po_breakdown_id')]]['job'];
				$style_ref_no=$job_array[$row[csf('po_breakdown_id')]]['style_ref_no'];
				$po=$job_array[$row[csf('po_breakdown_id')]]['po'];
				$booking_no = $booking_arr[$row[csf('po_breakdown_id')]]["booking_no"];
		    }

		    $refno_data_array[$booking_arr[$plan_arr[$row[csf('bwo')]]["booking_no"]]['booking_ref_no']][]=array(
		    //$refno_data_array[$job_array[$row[csf('po_breakdown_id')]]['ref_no']][$row[csf('febric_description_id')]][]=array(
			ref_no=>$ref_no,
			receive_basis=>$row[csf('receive_basis')],
			booking_id=>$row[csf('booking_id')],
			booking_no=>$booking_no,
			po_breakdown_id=>$row[csf('po_breakdown_id')],
			bwo=>$row[csf('bwo')],
			booking_without_order=>$row[csf('booking_without_order')],
			within_group=>$row[csf('within_group')],
			is_sales=>$row[csf('is_sales')],
			job_no=>$job_no,
			style_ref_no=>$style_ref_no,
			po=>$po_id
		    );
	    }


		$colarCupArr= sql_select("SELECT id, body_part_type, body_part_full_name from  lib_body_part where status_active=1 and  is_deleted=0 and body_part_type in(40,50)");
		foreach($colarCupArr as $row)
		{
			$body_part_data_arr[$row[csf('id')]]['body_part_full_name']=$row[csf('body_part_full_name')];
			$body_part_data_arr[$row[csf('id')]]['body_part_type']=$row[csf('body_part_type')];
		}
		//echo '<pre>';print_r($body_part_data_arr);

		// For Coller and Cuff data
		$sql_coller_cuff = "SELECT a.receive_basis, a.booking_no, b.body_part_id, c.booking_no as bwo, c.coller_cuff_size, c.qnty, c.qc_pass_qnty_pcs
			from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c
			where a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active = 1 and c.is_deleted=0 and b.status_active = 1 and b.is_deleted = 0 and c.barcode_no in ($barcode_nums) order by b.body_part_id";
		$sql_coller_cuff_result = sql_select($sql_coller_cuff);
		foreach ($sql_coller_cuff_result as $row2)
		{
			if($body_part_data_arr[$row2[csf('body_part_id')]]['body_part_type']>0 && $row2[csf('qc_pass_qnty_pcs')]>0)
			{
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qnty'] += $row2[csf('qnty')];
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['qc_pass_qnty_pcs'] += $row2[csf('qc_pass_qnty_pcs')];
				$coller_cuff_data_arr[$row2[csf('body_part_id')]][$row2[csf('bwo')]][$booking_arr[$plan_arr[$row2[csf('bwo')]]["booking_no"]]['booking_ref_no']][$row2[csf('coller_cuff_size')]]['receive_basis'] = $row2[csf('receive_basis')];
			}
		}
		$CoCu=1;
		foreach($coller_cuff_data_arr as $coll_cuff_id => $booking_data_arr)
		{
			if( count($booking_data_arr)>0)
			{
			    //$body_part_full_name = return_field_value("body_part_full_name", "lib_body_part", "status_active=1 and is_deleted =0 and id=$coll_cuff_id", "body_part_full_name");
			    ?>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="35%" class="rpt_table" style="float:left;">
                    <tr>
                        <th colspan="4"><? echo $body_part_data_arr[$coll_cuff_id]['body_part_full_name'];?> Details</th>
                    </tr>
                    <tr>
                        <th>Internal Ref. No</th>
                        <th>Fabric Booking No</th>
                        <th>Size</th>
                        <th>Qty Pcs</th>
                    </tr>
                    <?
                    $coller_cuff_qty_total=0;
                    foreach($booking_data_arr as $bookingId => $bookingData )
                    {
                        foreach($bookingData as $jobId => $jobData )
                        {
                            foreach($jobData as $size => $row )
                            {
                                ?>
                                <tr>
                                    <?
                                     if($row['receive_basis'] == 2)
                                     {
                                        ?>
                                        <td><?
                                        if($row['receive_basis'] == 2){
                                            echo  $booking_arr[$plan_arr[$bookingId]["booking_no"]]["booking_ref_no"];
                                        }else{
                                            echo  $booking_arr[$bookingId]["booking_ref_no"];
                                        }
                                        ?></td>
                                        <td><?  echo $plan_arr[$bookingId]["booking_no"]; ?></td>
                                        <?
                                     }
                                     else
                                     {
                                         ?>
                                        <td><? echo $booking_arr[$bookingId]["job_no"];?></td>
                                        <td><?  echo  $bookingId;  ?></td>
                                        <?
                                     }
                                    ?>
                                    <td align="center"><? echo $size;?></td>
                                    <td align="center"><? echo $row['qc_pass_qnty_pcs'];?></td>
                                </tr>
                                <?
                                $coller_cuff_qty_total += $row['qc_pass_qnty_pcs'];
                            }
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="3" align="right"><b>Total</b></td>
                        <td align="center"><b><? echo $coller_cuff_qty_total; ?></b></td>
                    </tr>
                </table>
			    <?
				if($CoCu==1){
					echo "<table width=\"30%\" style=\"float:left\"><tr><td colspan=\"4\">&nbsp;</td></tr></table>";
				}
				$CoCu++;
			}
		}
		?>
	</div>


	<? echo signature_table(125, $company, "1500px"); ?>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
	<script>
		function generateBarcode(valuess)
		{
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
	    }
	    generateBarcode('<? echo $txt_challan_no; ?>');
	    // document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
   </script>
   <?
   exit();
}

if ($action == "grey_delivery_print23")
{
	extract($_REQUEST);
	$data = explode('*', $data);
	$is_salesOrder = 0;
	$company = $data[0];
	$txt_challan_no = $data[1];
	$update_id = $data[2];
	$kniting_source = $data[4];
	$floor_name = $data[5];
	$is_po = $data[6];

	$company_array = array();
	$company_data = sql_select("select id, company_name, company_short_name from lib_company");
	foreach ($company_data as $row) {
		$company_array[$row[csf('id')]]['shortname'] = $row[csf('company_short_name')];
		$company_array[$row[csf('id')]]['name'] = $row[csf('company_name')];
	}

	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');
	$supplier_arr = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
	$buyer_array = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$yarn_count_details = return_library_array("select id,yarn_count from lib_yarn_count", "id", "yarn_count");
	$location_data = return_library_array("select id, location_name from lib_location", "id", "location_name");

	$machine_details = array();
	$machine_sql = sql_select("select id, machine_no, dia_width, gauge from lib_machine_name");
	foreach ($machine_sql as $row) {
		$machine_details[$row[csf("id")]]["machine_no"] = $row[csf("machine_no")];
		$machine_details[$row[csf("id")]]["dia_width"] = $row[csf("dia_width")];
		$machine_details[$row[csf("id")]]["gauge"] = $row[csf("gauge")];
	}

	$brand_details = return_library_array("select id, brand_name from lib_brand", "id", "brand_name");
	$location_arr = return_library_array("select id, location_name from lib_location", "id", "location_name");

	$mstData = sql_select("select a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis from pro_grey_prod_delivery_mst a
		inner join pro_grey_prod_delivery_dtls b on a.id = b.mst_id
		inner join inv_receive_master c on b.grey_sys_id = c.id
		where a.id=$update_id group by a.company_id,a.location_id, a.delevery_date, a.knitting_source, a.knitting_company, a.remarks,a.attention, a.id, b.grey_sys_id, c.booking_no, c.receive_basis"); //wo_po_details_master

	$search_param = $mstData[0][csf('booking_no')];
	if ($mstData[0][csf('receive_basis')] == 2) {
		$booking_data = sql_select("select b.booking_no, c.company_id, c.job_no from ppl_planning_info_entry_dtls a
			inner join ppl_planning_info_entry_mst b on a.mst_id = b.id
			inner join wo_booking_mst c on b.booking_no = c.booking_no
			where a.id = $search_param");

		$is_salesOrder = return_field_value("is_sales", "ppl_planning_info_entry_dtls", "id=$search_param");
		if ($is_salesOrder == "" || $is_salesOrder == 0) {
			$is_salesOrder = 0;
		} else {
			$is_salesOrder = 1;
		}
	} else if ($mstData[0][csf('receive_basis')] == 4) {
		$is_salesOrder = 1;
		$booking_data = sql_select("select a.sales_booking_no, b.company_id, b.job_no from fabric_sales_order_mst a
			inner join wo_booking_mst b on a.booking_id = b.id
			where a.job_no = '$search_param'");
	} else {
		$booking_data = sql_select("select a.booking_no, a.company_id, a.job_no from wo_booking_mst a where a.booking_no = '$search_param'");
	}


	if ($is_salesOrder == 1){
		$style_owner_sql = "select DISTINCT c.style_owner from pro_roll_details a , fabric_sales_order_mst b , wo_po_details_master c 
		where a.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_breakdown_id=b.id 
		and b.po_job_no = c.job_no and a.mst_id=$update_id ";
	}
	else{
		$style_owner_sql = "select DISTINCT c.style_owner  from pro_roll_details a , wo_po_break_down b , wo_po_details_master c 
		where a.entry_form=56 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_breakdown_id=b.id 
		and b.job_id = c.id and a.mst_id=$update_id";
	}
	$sql_owner_arr = sql_select($style_owner_sql);

	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array = sql_select($sql_deter);
	foreach ($data_array as $row) {
		if (array_key_exists($row[csf('id')], $composition_arr)) {
			$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		} else {
			$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
		}
		$composition_arr2[$row[csf('id')]] .= $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "% ";
	} 


	$image_location = return_field_value("image_location", "common_photo_library", "file_type=1 and form_name='company_details' and master_tble_id='$company'", "image_location");
	$com_dtls = fnc_company_location_address($company, $location, 2);
	?>
	<div style="width:1800px;">
		<table width="1390" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td rowspan="5" colspan="2">
					<img src="../../<? echo $com_dtls[2]; ?>" height="60" width="180" style="float:left;">
				</td>
				<td align="center" style="font-size:x-large">
					<strong style="margin-right:300px;"><? echo $com_dtls[0]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><? echo $com_dtls[1]; ?></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;">Style Owner: <?
						echo $company_array[$sql_owner_arr[0]['STYLE_OWNER']]['name'];
					 ?> </strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">
					<strong style="margin-right:300px;"><u>Delivery Challan</u></strong>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">
					<strong style="margin-right:300px;"><u>Knitting Section</u></strong>
				</td>
			</tr>
		</table>
		<br>
		<table width="1390" cellspacing="0" align="center" border="0" style="font-family: tahoma; font-size: 12px;">
			<tr>
				<td style="font-size:14px; font-weight:bold;" width="80">Challan No</td>
				<td width="170">:&nbsp;<? echo $txt_challan_no; ?></td>
				<td style="font-size:14px; font-weight:bold;" width="60">Location</td>
				<td width="170">:&nbsp;<? echo $location_data[$mstData[0][csf('location_id')]]; ?></td>
				<td width="810" id="barcode_img_id" align="right"></td>

			</tr>
			<tr>
				<td style="font-size:14px; font-weight:bold;">Delivery Date</td>
				<td>:&nbsp;<? echo change_date_format($mstData[0][csf('delevery_date')]); ?></td>
				<td style="font-size:14px; font-weight:bold;">Remarks</td>
				<td colspan="4" style="font-size:14px;">:&nbsp;<? echo $mstData[0][csf('remarks')]; ?></td>
			</tr>

			<tr>
				<td style="font-size:14px; font-weight:bold;">Floor No</td>
				<td>:&nbsp;<? echo $floor_name ?></td>
				<?php if ($is_po && ($is_salesOrder == 1)) { ?>
					<td style="font-size:14px; font-weight:bold;">PO Company</td>
					<td>:&nbsp;<? echo $company_array[$booking_data[0][csf('company_id')]]['name']; ?></td>
				<?php } ?>
			</tr>

		</table>
		<br>
		<table cellspacing="0" cellpadding="3" border="1" rules="all" width="2100" class="rpt_table"
		style="font-family: tahoma; font-size: 12px;">
		<thead>
			<tr>
				<th width="30">SL</th>
				<th width="70">Production Date</th><!--new-->
				<th width="90"><?php echo ($is_po && ($is_salesOrder == 1)) ? "Sales Order No " : "Order No" ?></th>
				<th width="100">Style No</th>
				<th width="100"><?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?>Buyer
					/<br> <?php echo ($is_po && ($is_salesOrder == 1)) ? "PO " : "" ?> Job
				</th>
				<th width="60">File No <br> Ref No</th>
				<th width="50">System ID</th>
				<th width="115">Yarn Issue<br>Challan No</th>
				<th width="85">Prog./ Book. No</th>
				<th width="80">Production Basis</th>
				<th width="120">Rcv. Challan No./ Service Booking No.</th>
				<th width="40">Shift</th><!--new-->
				<th width="70">Knitting Company</th>
				<th width="50">Yarn Count</th>
				<th width="50">Yarn Composition</th>
				<th width="70">Yarn Brand</th>
				<th width="60">Lot No</th>
				<th width="70">Fab Color</th>
				<th width="70">Color Range</th>
				<th width="150">Fabric Type</th>
				<th width="50">Stich</th>
				<th width="50">Fin GSM</th>
				<th width="40">Fab. Dia</th>
				<th width="40">MC. No</th>
				<th width="40">MC. Dia</th>
				<th width="40">MC. Gauge</th>
				<th width="50">Floor Name</th>
				<th width="80">Barcode No</th>
				<th width="80">Collar Cuff Details/Size</th>
				<th width="40">Qnty in Pcs</th>
				<th width="40">Roll No</th>
				<th>QC Pass Qty</th>
			</tr>
		</thead>
		<?
		$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
		from  pro_grey_prod_entry_dtls d,  inv_receive_master e
		where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
		$result_arr = sql_select($sql_dtls_knit);
		$machine_dia_guage_arr = array();
		foreach ($result_arr as $row) {
			$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
			$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
		}

		$yarn_lot_arr = array();
		$yarn_lot_sql=sql_select("select id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd,yarn_comp_percent2nd,lot,item_category_id,brand,yarn_count_id from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0");
		foreach ($yarn_lot_sql as $value) {
			$yarn_lot_arr[$value[csf('id')]]['yarn_comp_type1st'] = $value[csf('yarn_comp_type1st')];
			$yarn_lot_arr[$value[csf('id')]]['percent1st'] = $value[csf('yarn_comp_percent1st')];
			$yarn_lot_arr[$value[csf('id')]]['type2nd'] = $value[csf('yarn_comp_type2nd')];
			$yarn_lot_arr[$value[csf('id')]]['percent2nd'] = $value[csf('yarn_comp_percent2nd')];
		}

		$delivery_barcode_data = sql_select("select id, barcode_no, dtls_id, roll_id, qnty, po_breakdown_id, booking_without_order from pro_roll_details where entry_form=56 and status_active=1 and is_deleted=0 and mst_id=$update_id");

		foreach ($delivery_barcode_data as $row) {
			$barcode_nos .= $row[csf('barcode_no')] . ',';
			$deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"] = $row[csf('qnty')];
		}
		$barcode_nos = rtrim($barcode_nos,", ");

		$i = 0;
		$tot_qty = 0;
		$receive_basis = array(0 => "Independent", 1 => "Fabric Booking", 2 => "Knitting Plan");
		if ($is_salesOrder == 1) {

			$sql =" SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source,a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.floor_id,b.width, b.yarn_count, b.yarn_lot,b.yarn_prod_id, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order, p.job_no_prefix_num, p.job_no, p.style_ref_no, p.id, p.job_no po_number,p.sales_booking_no,  '' as file_no, '' as ref_no,c.qc_pass_qnty_pcs as grey_receive_qnty_pcs,c.coller_cuff_size,a.yarn_issue_challan_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, fabric_sales_order_mst p WHERE a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=p.id and c.barcode_no in($barcode_nos) and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.booking_without_order<>1 order by a.recv_number_prefix_num,b.febric_description_id,b.yarn_lot,b.color_id,a.booking_no";


		} else {

			$sql = " SELECT a.recv_number_prefix_num, a.buyer_id, a.receive_basis,a.challan_no,a.service_booking_no , a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date,a.location_id, b.prod_id, b.febric_description_id, b.gsm,b.floor_id, b.width, b.yarn_count, b.yarn_lot,b.yarn_prod_id, b.color_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name,c.barcode_no, c.roll_no, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order, q.job_no_prefix_num, q.job_no, q.style_ref_no, p.id, p.po_number, p.file_no, p.grouping as ref_no,c.qc_pass_qnty_pcs as grey_receive_qnty_pcs,c.coller_cuff_size,a.yarn_issue_challan_no
			  FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, wo_po_break_down p, wo_po_details_master q
			 WHERE a.id=b.mst_id and b.id=c.dtls_id  and c.po_breakdown_id=p.id and p.job_no_mst=q.job_no and c.barcode_no in($barcode_nos) and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and a.booking_without_order<>1
			 order by a.recv_number_prefix_num,b.febric_description_id,b.yarn_lot,b.color_id,a.booking_no";
		}
		//echo $sql."<br>"; 
		$order_data = array();
		$job_no_data = array();
		$booking_data = sql_select("select a.booking_no,a.buyer_id,a.job_no from wo_booking_mst a where a.status_active=1 and a.is_deleted=0");
		foreach ($booking_data as $row) {
			$order_data[$row[csf('booking_no')]]['buyer_id'] = $row[csf('buyer_id')];
			$order_data[$row[csf('booking_no')]]['job_no'] = $row[csf('job_no')];
		}

		$job_data = sql_select("select a.job_no_prefix_num, a.job_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0");
		foreach ($job_data as $row) {
			$job_no_data[$row[csf('job_no')]] = $row[csf('job_no_prefix_num')];
		}


		$result = sql_select($sql);
		$loc_arr = array();
		$loc_nm = ": ";
		$all_data_array = array();
		foreach ($result as $row) {

			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['recv_number_prefix_num'] = $row[csf('recv_number_prefix_num')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['buyer_id'] = $row[csf('buyer_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['receive_basis'] = $row[csf('receive_basis')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['challan_no'] = $row[csf('challan_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['service_booking_no'] = $row[csf('service_booking_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['booking_without_order'] = $row[csf('booking_without_order')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['booking_id'] = $row[csf('booking_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['knitting_source'] = $row[csf('knitting_source')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['knitting_company'] = $row[csf('knitting_company')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['receive_date'] = $row[csf('receive_date')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['location_id'] = $row[csf('location_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['prod_id'] = $row[csf('prod_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['gsm'] = $row[csf('gsm')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['floor_id'] = $row[csf('floor_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['width'] = $row[csf('width')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_count'] = $row[csf('yarn_count')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_prod_id'] = $row[csf('yarn_prod_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['color_range_id'] = $row[csf('color_range_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['machine_no_id'] = $row[csf('machine_no_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['stitch_length'] = $row[csf('stitch_length')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['brand_id'] = $row[csf('brand_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['shift_name'] = $row[csf('shift_name')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['barcode_no'] = $row[csf('barcode_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['roll_no'] = $row[csf('roll_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['po_breakdown_id'] = $row[csf('po_breakdown_id')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['current_delivery'] = $deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"];//$row[csf('current_delivery')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['bwo'] = $row[csf('bwo')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['job_no_prefix_num'] = $row[csf('job_no_prefix_num')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['job_no'] = $row[csf('job_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['sales_booking_no'] = $row[csf('sales_booking_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['po_number'] = $row[csf('po_number')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['file_no'] = $row[csf('file_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['ref_no'] = $row[csf('ref_no')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['grey_receive_qnty_pcs'] = $row[csf('grey_receive_qnty_pcs')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
			$all_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_issue_challan_no'] = $row[csf('yarn_issue_challan_no')];

		}


		/*echo "<pre>";
		print_r($all_data_array);	*/
		$size_wise_qnt=array();

		foreach ($all_data_array as $booking_nos => $booking_data)
			{	$booking_tot_delivery = 0;
				foreach ($booking_data as $color_id => $color_data)
					{	$color_tot_delivery = 0;
						foreach ($color_data as $yarn_lot => $lot_data)
							{	$lot_tot_delivery = 0;
								foreach ($lot_data as $fabric_type => $fabric_data)
									{	$fabric_tot_delivery = 0;
										foreach ($fabric_data as $dtlsID => $dtlsData)
										{
											$po_number = $dtlsData['job_no'];
											$booking_no = $dtlsData['sales_booking_no'];
											$booking_dtls_data = sql_select("select a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no partial_job_no from wo_booking_mst a
												inner join wo_booking_dtls b on a.booking_no = b.booking_no
												where a.status_active=1 and a.is_deleted=0 and a.booking_no = '$booking_no' group by a.booking_no,a.buyer_id,a.job_no, a.booking_type,a.is_short, b.job_no");

							//echo $booking_dtls_data;
											if ($loc_arr[$dtlsData['location_id']] == "") {
												$loc_arr[$dtlsData['location_id']] = $dtlsData['location_id'];
												$loc_nm .= $location_arr[$dtlsData['location_id']] . ', ';
											}

											$knit_company = "&nbsp;";
											if ($dtlsData["knitting_source"] == 1) {
												$knit_company = $company_array[$dtlsData["knitting_company"]]['shortname'];
											} else if ($dtlsData["knitting_source"] == 3) {
												$knit_company = $supplier_arr[$dtlsData["knitting_company"]];
											}

											$count = '';
											$yarn_count = explode(",", $dtlsData['yarn_count']);
											foreach ($yarn_count as $count_id) {
												if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
											}


											$composition_string = "";
											$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
											if(count(array_filter($yarn_prod_id)) > 0)
											{
												foreach($yarn_prod_id as $val)
												{
													$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
													$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
													$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
													$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

													//$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
													//if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
													//$composition_string .= ", ";
												}
											}
											//$composition_string = chop($composition_string,", ");
											$composition_string = $composition_arr2[$fabric_type];


											if ($dtlsData['receive_basis'] == 1) {
								//$booking_no=explode("-",$row[csf('booking_no')]);
								//$prog_book_no=(int)$booking_no[3];
												$prog_book_no = "";
											} else $prog_book_no = $booking_nos;

											if ($dtlsData["receive_basis"] == 2) {
												$is_salesOrder = sql_select("select b.booking_no, a.is_sales,a.machine_gg,a.machine_dia  from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
												$plan_booking_no = $is_salesOrder[0][csf('booking_no')];
												$mc_dia = $is_salesOrder[0][csf('machine_dia')];
												$machine_gg = $is_salesOrder[0][csf('machine_gg')];
												if ($is_salesOrder[0][csf('is_sales')] == "" || $is_salesOrder[0][csf('is_sales')] == 0) {
													$is_salesOrder = 0;
												} else {
													$is_salesOrder = 1;
												}
											} else {
												$plan_booking_no = $booking_nos;
												$mc_dia = $machine_dia_guage_arr[$dtlsData['booking_id']][$dtlsData['prod_id']]['dia'];
												$machine_gg = $machine_dia_guage_arr[$dtlsData['booking_id']][$dtlsData['prod_id']]['gg'];
								//echo $machine_gg.'ddddddddd';
											}

										if ($dtlsData["receive_basis"] == 4) // SALES ORDER
										{
											$is_salesOrder = 1;
										}
										if ($is_salesOrder == 1) {
											if ($booking_dtls_data[0][csf('booking_type')] == 1 && $booking_dtls_data[0][csf('is_short')] == 2) {
												$po_job = $job_no_data[$booking_dtls_data[0][csf('partial_job_no')]];
											} else {
												$po_job = $job_no_data[$booking_dtls_data[0][csf('job_no')]];
											}

											$po_jobs = explode(",", $order_data[$dtlsData['sales_booking_no']]['job_no']);
											foreach ($po_jobs as $job) {
												$po_job .= $job_no_data[$job] . ",";
											}
											$job_buyer = "B: " . $buyer_array[$order_data[$dtlsData['sales_booking_no']]['buyer_id']] . "<BR />J: " . rtrim($po_job, ',');
											$style_ref = $dtlsData['style_ref_no'];
										} else {
											$po_number = $dtlsData['po_number'];
											$job_buyer = "B: " . $buyer_array[$dtlsData['buyer_id']] . "<br>J: " . $dtlsData['job_no_prefix_num'];
											$style_ref = $dtlsData['style_ref_no'];
										}
										?>
										<tr>
											<td width="30"><? echo $i+1; ?></td>
											<td width="70"
											style="word-break:break-all;"><? echo change_date_format($dtlsData["receive_date"]); ?></td>
											<td width="100" style="word-break:break-all;"><? echo $dtlsData['po_number']; ?></td>
											<td width="100" style="word-break:break-all;"><? echo $dtlsData['style_ref_no']; ?></td>
											<td width="100" style="word-break:break-all;"><? echo $job_buyer; ?></td>
											<td width="60"
											style="word-break:break-all;"><? echo "F:" . $dtlsData['file_no'] . "<br>R:" . $dtlsData['ref_no']; ?></td>
											<td width="50"><? echo $dtlsData['recv_number_prefix_num']; ?></td>
											<td width="115"><p><?php echo $dtlsData['yarn_issue_challan_no']; ?></p></td>
											<td width="85" style="word-break:break-all;">
												P: <? echo $prog_book_no . (($prog_book_no != "") ? " /<br />" : "") . "B: " . $plan_booking_no; ?>
											</td>
											<td width="80"  style="word-break:break-all;"><? echo $receive_basis[$dtlsData['receive_basis']]; ?></td>

											<td width="120"  style="word-break:break-all;"><? echo "C:".$dtlsData['challan_no']."/<br />SB:".$dtlsData['service_booking_no']; ?></td>
											<td width="40"
											style="word-break:break-all;"><? echo $shift_name[$dtlsData["shift_name"]]; ?></td>
											<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
											<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
											<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
											<td width="70"
											style="word-break:break-all;"><? echo $brand_details[$dtlsData["brand_id"]]; ?></td>
											<td width="60" style="word-break:break-all;"><? echo $yarn_lot; ?></td>
											<td width="70" style="word-break:break-all;">
												<?
												//echo $color_arr[$row[csf("color_id")]];
												$color_id_arr = array_unique(explode(",", $color_id));
												$all_color_name = "";
												foreach ($color_id_arr as $c_id) {
													$all_color_name .= $color_arr[$c_id] . ",";
												}
												$all_color_name = chop($all_color_name, ",");
												echo $all_color_name;
												?>
											</td>
											<td width="70"
											style="word-break:break-all;"><? echo $color_range[$dtlsData["color_range_id"]]; ?></td>
											<td width="150"
											style="word-break:break-all;"><? echo $composition_arr[$fabric_type]; ?></td>
											<td width="50" style="word-break:break-all;"
											align="center"><? echo $dtlsData['stitch_length']; ?></td>
											<td width="50" style="word-break:break-all;" align="center"><? echo $dtlsData['gsm']; ?></td>
											<td width="40" style="word-break:break-all;" align="center"><? echo $dtlsData['width']; ?></td>
											<td width="40" style="word-break:break-all;"
											align="center"><? echo $machine_details[$dtlsData['machine_no_id']]["machine_no"]; ?></td>
											<td width="40" style="word-break:break-all;"
						                        align="center"><? echo $mc_dia;//$machine_details[$row[csf('machine_no_id')]]["dia_width"];
						                        ?></td>
						                        <td width="40" style="word-break:break-all;"
						                        align="center"><? echo $machine_gg;//$machine_details[$row[csf('machine_no_id')]]["gauge"];
						                        ?></td>
						                        <td width="50" align="center"><? echo $floor_name_arr[$dtlsData['floor_id']]; ?></td>
						                        <td width="80" align="center"><? echo $dtlsData['barcode_no']; ?></td>
						                        <td width="80" align="center"><p><? echo $dtlsData['coller_cuff_size']; ?></p></td>
						                        <td width="40" align="right"><? echo $dtlsData['grey_receive_qnty_pcs']; ?></td>
						                        <td width="40" align="center"><? echo $dtlsData['roll_no']; ?></td>
						                        <td align="right"><? echo number_format($dtlsData['current_delivery'], 2); ?></td>
						                    </tr>
						                    <?

						                    if(!empty($dtlsData['coller_cuff_size']))
						                    {

						                    	$size_wise_qnt[trim($dtlsData['coller_cuff_size'])]+=$dtlsData['grey_receive_qnty_pcs'];

						                    }
						                    $booking_tot_delivery += $dtlsData['current_delivery'];
						                    $color_tot_delivery += $dtlsData['current_delivery'];
						                    $lot_tot_delivery += $dtlsData['current_delivery'];
						                    $fabric_tot_delivery += $dtlsData['current_delivery'];
						                    $grand_tot_qty += $dtlsData['current_delivery'];
						                    $i++;
						                }
						                ?>
						                <tr bgcolor="#CCCCCC">
						                	<td align="right" colspan="31" style="font-weight:bold;">Fabric SubTotal:</td>
						                	<td align="right"><? echo number_format($fabric_tot_delivery, 2); ?></td>
						                </tr>
						                <?
						            }
						            ?>
						            <tr bgcolor="#CCCCCC">
						            	<td align="right" colspan="31" style="font-weight:bold;">Lot SubTotal:</td>
						            	<td align="right"><? echo number_format($lot_tot_delivery, 2); ?></td>
						            </tr>
						            <?
						        }
						        ?>
						        <tr bgcolor="#CCCCCC">
						        	<td align="right" colspan="31" style="font-weight:bold;">Color SubTotal:</td>
						        	<td align="right"><? echo number_format($color_tot_delivery, 2); ?></td>
						        </tr>
						        <?
						    }
						    ?>
						    <tr bgcolor="#CCCCCC">
						    	<td align="right" colspan="31" style="font-weight:bold;">Booking/Order SubTotal:</td>
						    	<td align="right"><? echo number_format($booking_tot_delivery, 2); ?></td>
						    </tr>
						    <?
						    $booking_grand_total += $booking_tot_delivery;
						}
						//$febric_tot_delivery += $dtlsData['current_delivery'];
						/*$sql_dtls_knit = "select e.booking_id,d.prod_id,d.machine_no_id,d.machine_dia,d.machine_gg
						from  pro_grey_prod_entry_dtls d,  inv_receive_master e
						where d.mst_id=e.id and e.company_id=" . $company . " and d.status_active=1 and d.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.entry_form in(2,22)";
						$result_arr = sql_select($sql_dtls_knit);
						$machine_dia_guage_arr = array();
						foreach ($result_arr as $row) {
							$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['dia'] = $row[csf('machine_dia')];
							$machine_dia_guage_arr[$row[csf('booking_id')]][$row[csf('prod_id')]]['gg'] = $row[csf('machine_gg')];
						}*/

                //$i = 1;
				//$tot_qty = 0;

						$sql_no_order = " SELECT a.recv_number_prefix_num,a.challan_no,a.service_booking_no , a.buyer_id, a.receive_basis, a.booking_without_order, a.booking_no,a.booking_id, a.knitting_source, a.knitting_company, a.receive_date, a.location_id, b.prod_id, b.febric_description_id, b.gsm, b.width, b.yarn_count, b.yarn_lot,b.yarn_prod_id, b.color_id,b.floor_id, b.color_range_id, b.machine_no_id, b.stitch_length, b.brand_id, b.shift_name, c.barcode_no, c.roll_no, c.po_breakdown_id, c.booking_no as bwo, c.booking_without_order,c.qc_pass_qnty_pcs as grey_receive_qnty_pcs,c.coller_cuff_size,a.yarn_issue_challan_no FROM inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c, lib_machine_name e WHERE a.id=b.mst_id and b.id=c.dtls_id and b.machine_no_id=e.id  and a.entry_form=2 and c.entry_form=2 and a.booking_without_order=1 and c.barcode_no in($barcode_nos) order by e.seq_no";


					//echo $sql_no_order;//die;

						$result_nonorder = sql_select($sql_no_order);
						$loc_arr = array();
						$loc_nm = ": ";
						foreach ($result_nonorder as $row) {
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['recv_number_prefix_num'] = $row[csf('recv_number_prefix_num')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['buyer_id'] = $row[csf('buyer_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['receive_basis'] = $row[csf('receive_basis')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['challan_no'] = $row[csf('challan_no')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['service_booking_no'] = $row[csf('service_booking_no')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['booking_without_order'] = $row[csf('booking_without_order')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['booking_id'] = $row[csf('booking_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['knitting_source'] = $row[csf('knitting_source')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['knitting_company'] = $row[csf('knitting_company')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['receive_date'] = $row[csf('receive_date')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['location_id'] = $row[csf('location_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['prod_id'] = $row[csf('prod_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['gsm'] = $row[csf('gsm')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['width'] = $row[csf('width')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_count'] = $row[csf('yarn_count')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['color_range_id'] = $row[csf('color_range_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['machine_no_id'] = $row[csf('machine_no_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['stitch_length'] = $row[csf('stitch_length')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['brand_id'] = $row[csf('brand_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['shift_name'] = $row[csf('shift_name')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['barcode_no'] = $row[csf('barcode_no')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['roll_no'] = $row[csf('roll_no')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['po_breakdown_id'] = $row[csf('po_breakdown_id')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['current_delivery'] = $deliveryBarcodeData[$row[csf('barcode_no')]]["qnty"];//$row[csf('current_delivery')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['bwo'] = $row[csf('bwo')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['grey_receive_qnty_pcs'] = $row[csf('grey_receive_qnty_pcs')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['coller_cuff_size'] = $row[csf('coller_cuff_size')];
							$nonOrder_data_array[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('yarn_lot')]][$row[csf('febric_description_id')]][$row[csf('barcode_no')]]['yarn_issue_challan_no'] = $row[csf('yarn_issue_challan_no')];

						}

				 /*echo "<pre>";
				 print_r($nonOrder_data_array);die;*/

						foreach ($nonOrder_data_array as $booking_nos => $booking_data)
							{	$booking_tot_delivery = 0;
								foreach ($booking_data as $color_id => $color_data)
									{	$color_tot_delivery = 0;
										foreach ($color_data as $yarn_lot => $lot_data)
											{	$lot_tot_delivery = 0;
												foreach ($lot_data as $fabric_type => $fabric_data)
													{	$fabric_tot_delivery = 0;
														foreach ($fabric_data as $dtlsData)
														{
															if ($loc_arr[$dtlsData['location_id']] == "") {
																$loc_arr[$dtlsData['location_id']] = $dtlsData['location_id'];
																$loc_nm .= $location_arr[$dtlsData['location_id']] . ', ';
															}

															$knit_company = "&nbsp;";
															if ($dtlsData["knitting_source"] == 1) {
																$knit_company = $company_array[$dtlsData["knitting_company"]]['shortname'];
															} else if ($dtlsData["knitting_source"] == 3) {
																$knit_company = $supplier_arr[$dtlsData["knitting_company"]];
															}

															$count = '';
															$yarn_count = explode(",", $dtlsData['yarn_count']);
															foreach ($yarn_count as $count_id) {
																if ($count == '') $count = $yarn_count_details[$count_id]; else $count .= "," . $yarn_count_details[$count_id];
															}


															$composition_string = "";
															$yarn_prod_id = explode(",", $row[csf('yarn_prod_id')]);
															if(count(array_filter($yarn_prod_id))>0)
															{
																foreach($yarn_prod_id as $val)
																{
																	$composition_id =$yarn_lot_arr[$val]['yarn_comp_type1st'];
																	$percent1st 	=$yarn_lot_arr[$val]['percent1st'];
																	$type2nd 		=$yarn_lot_arr[$val]['type2nd'];
																	$percent2nd 	=$yarn_lot_arr[$val]['percent2nd'];

																	$composition_string .= $composition[$composition_id] . " " . $percent1st . "%";
																	if ($type2nd != 0) $composition_string .= " " . $composition[$type2nd] . " " . $percent2nd . "%";
																	$composition_string .= ", ";
																}
															}
															$composition_string = chop($composition_string,", ");


															if ($dtlsData['receive_basis'] == 1) {
																$booking_no = explode("-", $booking_nos);
																$prog_book_no = (int)$booking_no[3];
															} else $prog_book_no = $booking_nos;

															if ($dtlsData['receive_basis'] == 2) {
																$planOrder = sql_select("select b.booking_no, a.is_sales,a.machine_gg,a.machine_dia from ppl_planning_info_entry_dtls a inner join ppl_planning_info_entry_mst b on a.mst_id = b.id where a.id = $prog_book_no");
																$mc_dia = $planOrder[0]['machine_dia'];
																$machine_gg = $planOrder[0]['machine_gg'];
															} else {
																$mc_dia = $machine_dia_guage_arr[$dtlsData['booking_id']][$dtlsData['prod_id']]['dia'];
																$machine_gg = $machine_dia_guage_arr[$dtlsData['booking_id']][$dtlsData['prod_id']]['gg'];
															}
															?>
															<tr>
																<td width="30"><? echo $i+1; ?></td>
																<td width="70"
																style="word-break:break-all;"><? echo change_date_format($dtlsData["receive_date"]); ?></td>
																<td width="100" style="word-break:break-all;"><? echo $dtlsData['bwo']; ?></td>
																<td width="100" style="word-break:break-all;">&nbsp;</td>
																<td width="60"
																style="word-break:break-all;"><? echo $buyer_array[$dtlsData['buyer_id']]; ?></td>
																<td width="60" style="word-break:break-all;"><? echo "F:<br>R:"; ?></td>
																<td width="50"><? echo $dtlsData['recv_number_prefix_num']; ?></td>
																<td width="115"><? echo $dtlsData['yarn_issue_challan_no']; ?></td>
																<td width="85" style="word-break:break-all;"><? echo $prog_book_no; ?></td>
																<td width="80" style="word-break:break-all;"><? echo $receive_basis[$dtlsData['receive_basis']]; ?></td>
																<td width="120"  style="word-break:break-all;"><? echo "C:".$dtlsData['challan_no']."/<br />SB:".$dtlsData['service_booking_no']; ?></td>
																<td width="40"
																style="word-break:break-all;"><? echo $shift_name[$dtlsData["shift_name"]]; ?></td>
																<td width="70" style="word-break:break-all;"><? echo $knit_company; ?></td>
																<td width="50" style="word-break:break-all;"><? echo $count; ?></td>
																<td width="50" style="word-break:break-all;"><? echo $composition_string; ?></td>
																<td width="70"
																style="word-break:break-all;"><? echo $brand_details[$dtlsData["brand_id"]]; ?></td>
																<td width="60" style="word-break:break-all;"><? echo $yarn_lot; ?></td>
																<td width="70" style="word-break:break-all;">
																	<?
											//echo $color_arr[$row[csf("color_id")]];
																	$color_id_arr = array_unique(explode(",", $color_id));
																	$all_color_name = "";
																	foreach ($color_id_arr as $c_id) {
																		$all_color_name .= $color_arr[$c_id] . ",";
																	}
																	$all_color_name = chop($all_color_name, ",");
																	echo $all_color_name;
																	?>
																</td>
																<td width="70"
																style="word-break:break-all;"><? echo $color_range[$dtlsData["color_range_id"]]; ?></td>
																<td width="150"
																style="word-break:break-all;"><? echo $composition_arr[$fabric_type]; ?></td>
																<td width="50" style="word-break:break-all;"
																align="center"><? echo $dtlsData['stitch_length']; ?></td>
																<td width="50" style="word-break:break-all;" align="center"><? echo $dtlsData['gsm']; ?></td>
																<td width="40" style="word-break:break-all;" align="center"><? echo $dtlsData['width']; ?></td>
																<td width="40" style="word-break:break-all;"
																align="center"><? echo $machine_details[$dtlsData['machine_no_id']]["machine_no"]; ?></td>
																<td width="40" style="word-break:break-all;"
					                        align="center"><? echo $mc_dia;//$machine_details[$row[csf('machine_no_id')]]["dia_width"];
					                        ?></td>
					                        <td width="40" style="word-break:break-all;"
					                        align="center"><? echo $machine_gg;//$machine_details[$row[csf('machine_no_id')]]["gauge"];
					                        ?></td>
					                        <td width="50" align="center"><? echo $floor_name_arr[$dtlsData['floor_id']]; ?></td>
					                        <td width="80" align="center"><? echo $dtlsData['barcode_no']; ?></td>
					                        <td width="80" align="center"><p><? echo $dtlsData['coller_cuff_size']; ?></p></td>
					                        <td width="40" align="right"><? echo $dtlsData['grey_receive_qnty_pcs']; ?></td>
					                        <td width="40" align="center"><? echo $dtlsData['roll_no']; ?></td>
					                        <td align="right"><? echo number_format($dtlsData['current_delivery'], 2); ?></td>
					                    </tr>
					                    <?
					                    if(!empty($dtlsData['coller_cuff_size']))
					                    {

					                    	$size_wise_qnt[trim($dtlsData['coller_cuff_size'])]+=$dtlsData['grey_receive_qnty_pcs'];
					                    }
					                    $booking_tot_delivery += $dtlsData['current_delivery'];
					                    $color_tot_delivery += $dtlsData['current_delivery'];
					                    $lot_tot_delivery += $dtlsData['current_delivery'];
					                    $fabric_tot_delivery += $dtlsData['current_delivery'];
					                    $grand_tot_qty += $dtlsData['current_delivery'];
					                    $i++;
					                }
					                ?>
					                <tr bgcolor="#CCCCCC">
					                	<td align="right" colspan="31" style="font-weight:bold;">Fabric SubTotal:</td>
					                	<td align="right"><? echo number_format($fabric_tot_delivery, 2); ?></td>
					                </tr>
					                <?
					            }
					            ?>
					            <tr bgcolor="#CCCCCC">
					            	<td align="right" colspan="31" style="font-weight:bold;">Lot SubTotal:</td>
					            	<td align="right"><? echo number_format($lot_tot_delivery, 2); ?></td>
					            </tr>
					            <?
					        }
					        ?>
					        <tr bgcolor="#CCCCCC">
					        	<td align="right" colspan="31" style="font-weight:bold;">Color SubTotal:</td>
					        	<td align="right"><? echo number_format($color_tot_delivery, 2); ?></td>
					        </tr>
					        <?
					    }
					    ?>
					    <tr bgcolor="#CCCCCC">
					    	<td align="right" colspan="31" style="font-weight:bold;">Booking/Order SubTotal:</td>
					    	<td align="right"><? echo number_format($booking_tot_delivery, 2); ?></td>
					    </tr>
					    <?
					    $booking_grand_total += $booking_tot_delivery;
					}

					?>
					<tr>
						<td align="right" colspan="30"><strong>Grand Total</strong></td>
						<td align="right"><? echo $i; ?></td>
						<td align="right"><? echo number_format($grand_tot_qty, 2, '.', ''); ?></td>
					</tr> <tr>
						<td align="right" colspan="30"><strong>Booking Total</strong></td>
						<td align="right"><? echo $i; ?></td>
						<td align="right"><? echo number_format($booking_grand_total, 2, '.', ''); ?></td>
					</tr>

					<tr>
						<td colspan="2" align="left"><b>Remarks:</b></td>
						<td colspan="30">&nbsp;</td>
					</tr>
				</table>
				<br>
				<?
				if(count($size_wise_qnt))
				{
						?>
					<table cellspacing="0" cellpadding="3" border="1" rules="all" width="200" class="rpt_table" style="font-family: tahoma; font-size: 12px;">
						<caption>Collar Cuff Size Summary </caption>
						<thead>
							<tr>
								<th>Sl</th>
								<th>Size</th>
								<th>Pcs</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$i=1;
								$total=0;

								foreach ($size_wise_qnt as $key => $value)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_2nd<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i; ?>">
										<td><?=$i ?></td>
										<td><?=$key?></td>
										<td align="right"><?=number_format($value)?></td>
									</tr>
									<?
									$i++;
									$total+=$value;
								}

							 ?>

						</tbody>
						<tfoot>
							<tr>
								<th colspan="2">Total</th>
								<th align="right"><?=number_format($total)?></th>
							</tr>
						</tfoot>
					</table>
						<?
				}
				?>
			</div>
			<div style="font-family: tahoma; font-size: 11px;"><? echo signature_table(125, $company, "1600px"); ?></div>
			<script type="text/javascript" src="../../js/jquery.js"></script>
			<script type="text/javascript" src="../../js/jquerybarcode.js"></script>
			<script>
				function generateBarcode(valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 40,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#barcode_img_id").show().barcode(value, btype, settings);
        }
        generateBarcode('<? echo $txt_challan_no; ?>');
        document.getElementById('location_td').innerHTML = '<? echo $loc_nm; ?>';
    </script>
    <?
    exit();
}


?>