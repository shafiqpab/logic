<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
	header("location:login.php");

require_once('../../../includes/common.php');
$user_name = $_SESSION['logic_erp']['user_id'];
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

/*
|--------------------------------------------------------------------------
| load_drop_down_buyer
|--------------------------------------------------------------------------
*/
if ($action == "load_drop_down_buyer")
{
	echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='".$data."' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) ".$buyer_cond." order by buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_location
|--------------------------------------------------------------------------
*/
if ($action == "load_drop_down_location")
{
	echo create_drop_down("cbo_location_id", 120, "select id, location_name from lib_location where status_active=1 and is_deleted=0 and company_id in(".$data.") order by location_name", "id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/knitting_program_report_controller', this.value, 'load_drop_down_floor', 'floor_td' );");
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_party_type
|--------------------------------------------------------------------------
*/
if ($action == "load_drop_down_party_type")
{
	$explode_data = explode("**", $data);
	$data = $explode_data[0];
	$selected_company = $explode_data[1];
	
	//inside
	if ($data == 1)
	{
		echo create_drop_down("cbo_party_type", 120, "select comp.id, comp.company_name from lib_company comp where comp.status_active = 1 and comp.is_deleted = 0 ".$company_cond." order by comp.company_name", "id,company_name", 1, "-- Select --", $selected_company, "", 0, 0);
	}
	//outside
	else if ($data == 3)
	{
		echo create_drop_down("cbo_party_type", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a, lib_supplier_party_type b, lib_supplier c where c.id = b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company = '".$selected_company."' and b.party_type = 20 and c.status_active = 1 and c.is_deleted = 0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", $selected, "");
	}
	//all
	else
	{
		echo create_drop_down("cbo_party_type", 120, $blank_array, "", 1, "-- Select --", $selected, "", 1);
	}
	exit();
}

/*
|--------------------------------------------------------------------------
| load_drop_down_floor
|--------------------------------------------------------------------------
*/
if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 120, "select id, floor_name from lib_prod_floor where status_active = 1 and is_deleted = 0 and location_id = ".$data." and production_process = 2 order by floor_name","id,floor_name", 1, "-- Select --", $selected );
	exit();     	 
}

/*
|--------------------------------------------------------------------------
| order_no_search_popup
|--------------------------------------------------------------------------
*/
if ($action == "order_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor)
		{
			var newColor = 'yellow';
			if (x.style)
			{
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str)
		{
			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');
			if (jQuery.inArray(str[1], selected_id) == -1)
			{
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			}
			else
			{
				for (var i = 0; i < selected_id.length; i++)
				{
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = ''; var name = '';
			for (var i = 0; i < selected_id.length; i++)
			{
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_order_id').val(id);
			$('#hide_order_no').val(name);
		}
	</script>
</head>
<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Order No</th>
						<th>Shipment Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_order_no" id="hide_order_no" value="" />
						<input type="hidden" name="hide_order_id" id="hide_order_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Order No", 2 => "Style Ref", 3 => "Job No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_order_no_search_list_view', 'search_div', 'knitting_program_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

/*
|--------------------------------------------------------------------------
| create_order_no_search_list_view
|--------------------------------------------------------------------------
*/
if ($action == "create_order_no_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"] == 1)
		{
			if ($_SESSION['logic_erp']["buyer_id"] != "")
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else
		{
			$buyer_id_cond = "";
		}
	}
	else
	{
		$buyer_id_cond = " and a.buyer_name=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "b.po_number";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else
		$search_field = "a.job_no";

	$start_date = $data[4];
	$end_date = $data[5];

	if ($start_date != "" && $end_date != "")
	{
		if ($db_type == 0)
		{
			$date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		}
		else
		{
			$date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	}
	else
	{
		$date_cond = "";
	}

	$arr = array(0 => $company_library, 1 => $buyer_arr);

	if ($db_type == 0)
		$year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	else
        $year_field = ""; //defined Later

	$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$arr=array (0=>$company_library, 1=>$buyer_arr);
	
    $sql = "select b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170", "760", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "", '', '0,0,0,0,0,0,3', '', 1);
    exit();
}

/*
|--------------------------------------------------------------------------
| create_order_no_search_list_view
|--------------------------------------------------------------------------
*/
if ($action == "booking_no_search_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	$data=explode('_',$data);
	?>	
	<script>
		function js_set_value(booking_no)
		{
			document.getElementById('selected_booking').value=booking_no;
			parent.emailwindow.hide();
		}
	</script>
</head>
<body>
	<div align="center" style="width:100%;" >
		<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
			<table width="750" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center" rules="all">
				<tr>
					<td align="center" width="100%">
						<table cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
							<thead>                	 
								<th width="150">Company Name</th>
								<th width="140">Buyer Name</th>
								<th width="80">Booking No</th>
								<th width="180">Short Booking Date</th>
								<th>&nbsp;</th>
							</thead>
							<tr>
								<td>
									<input type="hidden" id="selected_booking">
									<input type="hidden" id="job_no" value="<? echo $data[2];?>">
									<?php 
									echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", '', "load_drop_down( 'knitting_program_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
									?>
								</td>
								<td id="buyer_td"><? echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --" ); ?></td>
								<td>
									<input type="text" id="txt_booking_no" name="txt_booking_no" class="text_boxes_numeric" style="width:75px" />
								</td>
								<td>
									<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">
									<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
								</td> 
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('txt_booking_no').value+'_<?php echo $data[3].'_'.$data[4];?>', 'create_booking_search_list_view', 'search_div', 'knitting_program_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td  align="center" height="40" valign="middle">
						<?php
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
<?php
exit(); 
}

/*
|--------------------------------------------------------------------------
| create_booking_search_list_view
|--------------------------------------------------------------------------
*/
if ($action=="create_booking_search_list_view")
{
	$data=explode('_',$data);
	
	//for company
	if ($data[0]!=0)
		$company="  company_id='$data[0]'";
	else
	{
		echo "<p style='color:red; font-weight:bold; font-size:20pt;'>Please select company first.</p>";
		die;
	}
	
	//for buyer
	if ($data[1]!=0)
		$buyer=" and buyer_id='$data[1]'";
	else
		$buyer="";
		
	//for job no
	if ($data[4]!=0)
		$job_no=" and job_no='$data[4]'";
	else
		$job_no='';
		
	//for booking no
	if ($data[5]!=0)
		$booking_no=" and booking_no_prefix_num='$data[5]'";
	else
		$booking_no='';
		
	//for booking type
	if ($data[6] != 0 && $data[6] != 5)
	{
		$booking_type = " and a.booking_type = '".$data[6]."'";
		$is_short =  " and a.is_short = '".$data[7]."'";
	}
	else
	{
		$booking_type = '';
		$is_short = '';
	}

	//for from date todate
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="")
			$booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'";
		else
			$booking_date ="";
	}
	else if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="")
			$booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'";
		else
			$booking_date ="";
	}
	
	/*
	$po_array=array();
	//$sql_po= sql_select("select booking_no,po_break_down_id from wo_booking_mst where $company $buyer $booking_no $booking_date and booking_type=1 and is_short=2 and status_active=1 and is_deleted=0 order by booking_no");
	
	$sql_po= sql_select("select booking_no,po_break_down_id from wo_booking_mst where $company $buyer $booking_no $booking_date $booking_type $is_short and status_active=1 and is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$order_arr[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}
	*/
	
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);

	//$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and $company $buyer $booking_no $booking_date and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 group by a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved order by a.booking_no_prefix_num Desc";
	
	$sql = "select a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and $company $buyer $booking_no $booking_date $booking_type $is_short and a.status_active=1 and a.is_deleted=0 group by a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved order by a.booking_no_prefix_num DESC";
	
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','','');
	exit(); 
}

/*
|--------------------------------------------------------------------------
| report_generate
|--------------------------------------------------------------------------
*/
$tmplte = explode("**", $data);

if ($tmplte[0] == "viewtemplate")
	$template = $tmplte[1];
else
	$template = $lib_report_template_array[$_SESSION['menu_id']]['0'];
	
if ($template == "")
	$template = 1;

if ($action == "report_generate") 
{
	//echo 'su..re'; die;
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));
	$txt_machine_no = str_replace("'", "", $txt_machine_no);
	
	if ($template == 1)
	{
		$cbo_knitting_status = str_replace("'", "", $cbo_knitting_status);
		$based_on = str_replace("'", "", $cbo_based_on);
		$presentationType = str_replace("'", "", $presentationType);
		$type = str_replace("'", "", $cbo_type);
		
		//lc company
		$company_name = $cbo_company_name;
		
		//type
		if($type>0)
			$knitting_source_cond=" AND b.knitting_source = ".$type."";
		else
			$knitting_source_cond="";
			
		//w company/party
		if (str_replace("'", "", $cbo_party_type) == 0)
			$party_type = "%%";
		else
			$party_type = str_replace("'", "", $cbo_party_type);
			
		//buyer			
		if (str_replace("'", "", $cbo_buyer_name) == 0) 
		{
			if ($_SESSION['logic_erp']["data_level_secured"] == 1) 
			{
				if ($_SESSION['logic_erp']["buyer_id"] != "")
					$buyer_id_cond = " AND a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
				else
					$buyer_id_cond = "";
			}
			else
			{
				$buyer_id_cond = "";
			}


		}
		else
		{
			$buyer_id_cond = " AND a.buyer_id = ".$cbo_buyer_name."";
		}
		
		//cbo_location_id
		//cbo_location_id condition gose here
		$location_cond = '';
		if (str_replace("'", "", $cbo_location_id) != 0)
			$location_cond = " AND b.location_id = ".$cbo_location_id."";;
		
		//cbo_floor_id
		//cbo_floor_id condition gose here
		$floor_id = str_replace("'", "", $cbo_floor_id);
		
		//txt_machine_dia
		if (str_replace("'", "", $txt_machine_dia) == "")
			$machine_dia = "%%";
		else
			$machine_dia = "%".str_replace("'", "", $txt_machine_dia)."%";


		//cbo_year
		$year_cond = "";
		$cbo_year = str_replace("'", "", $cbo_year);
		if (trim($cbo_year) != 0) 
		{
			if ($db_type == 0)
				$year_cond = " AND YEAR(a.insert_date) = ".$cbo_year."";
			else if ($db_type == 2)
				$year_cond = " AND to_char(a.insert_date,'YYYY') = ".$cbo_year."";
			else
				$year_cond = "";
		}

		//txt_job_no
		$job_cond = "";
		if (str_replace("'", "", $txt_job_no) != "") 
		{
			$job_number = "%" . trim(str_replace("'", "", $txt_job_no));
			$job_cond = " AND b.job_no_mst LIKE '".$job_number."'";
		}
		
		//txt_file_no
		$txt_file_no = trim(str_replace("'", "", $txt_file_no));
		if ($txt_file_no == "")
			$file_no_cond = "";
		else
			$file_no_cond = " AND b.file_no = '".$txt_file_no."' ";

		//txt_internal		
		$txt_internal = trim(str_replace("'", "", $txt_internal));
		if ($txt_internal == "")
			$internal_cond = "";
		else
			$internal_cond = " AND b.grouping = '".$txt_internal."' ";

		//cbo_company_name*cbo_type*cbo_party_type*cbo_buyer_name*cbo_location_id*cbo_floor_id*txt_machine_dia*cbo_year*txt_job_no*txt_file_no*txt_internal*txt_order_no*hide_order_id*cbo_booking_type*txt_booking_no*txt_program_no*cbo_knitting_status*cbo_based_on*txt_date_from*txt_date_to
		//txt_order_no
		$order_cond = "";
		if (str_replace("'", "", trim($txt_order_no)) != "") 
		{
			if (str_replace("'", "", $hide_order_id) != "")
			{
				$order_cond = " AND b.id IN(" . str_replace("'", "", $hide_order_id) . ")";
			}
			else
			{
				$po_number = "%" . trim(str_replace("'", "", $txt_order_no)) . "%";
				$order_cond = " AND b.po_number LIKE '".$po_number."'";
			}
		}

		//cbo_booking_type
		//cbo_booking_type condition gose here
		$booking_type = str_replace("'", "", $cbo_booking_type);
		$exp_booking_type = explode("_",$booking_type);
		if($exp_booking_type[0] != 0 && $exp_booking_type[0] != 5)
		{
			$booking_type_cond = " AND d.booking_type = '".$exp_booking_type[0]."' AND d.is_short = '".$exp_booking_type[1]."'";
		}
		else
		{
			$booking_type_cond = "";
		}

		//txt_booking_no
		$booking_search_cond = "";
		if (str_replace("'", "", trim($txt_booking_no)) != "") 
		{
			$booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
			$booking_search_cond = " AND a.booking_no LIKE '".$booking_number."'";
		}
		
		//txt_program_no
		if (str_replace("'", "", $txt_program_no) == "")
			$program_no = "%%";
		else
			$program_no = str_replace("'", "", $txt_program_no);
		
		//cbo_knitting_status
		//cbo_knitting_status condition gose here
		
		//cbo_based_on
		//cbo_based_on condition gose here
		
		//txt_date_from
		//txt_date_to
		if (str_replace("'", "", trim($txt_date_from)) != "" && str_replace("'", "", trim($txt_date_to)) != "") 
		{
			if ($based_on == 2)
			{
				$date_cond = " AND b.program_date between " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
			}
			else
			{
				$date_cond = " AND b.start_date between " . trim($txt_date_from) . " AND " . trim($txt_date_to) . "";
			}
		} 
		else
		{
			$date_cond = "";
		}

		//year_field
		if ($db_type == 0)
			$year_field = "YEAR(a.insert_date) AS year";
		else if ($db_type == 2)
			$year_field = "TO_CHAR(a.insert_date,'YYYY') AS year";
			
		/*
		|--------------------------------------------------------------------------
		| if search by order no or job no or internal ref or file no
		| then order info is preparing
		|--------------------------------------------------------------------------
		*/
		$po_array = array();
		$po_id_check = array();
		$po_id = "";
		$x = 0;

		if($order_cond != "" || $job_cond!="" || $internal_cond!="" || $file_no_cond!="")
		{
			$costing_sql = sql_select("SELECT a.job_no, ".$year_field.", a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no FROM wo_po_details_master a, wo_po_break_down b WHERE a.job_no = b.job_no_mst AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND a.company_name = ".$company_name." $order_cond $job_cond $year_cond $internal_cond $file_no_cond");
			foreach ($costing_sql as $row)
			{
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
				$po_array[$row[csf('id')]]['year'] = $row[csf('year')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$po_array[$row[csf('id')]]['ref_no'] = $row[csf('grouping')];
				$po_id .= $row[csf('id')] . ",";
			}
			if($rID)
			{
			    oci_commit($con);
			}
		}
		//echo "<pre>";
		//print_r($po_array); die;

		$po_id_cond = "";
		if ($po_id != "") 
		{
			$po_id = substr($po_id, 0, -1);
			if ($db_type == 0)
				$po_id_cond = "AND c.po_id IN(" . $po_id . ")";
			else
			{
				$po_ids = explode(",", $po_id);
				if (count($po_ids) > 999)
				{
					$po_id_cond = "AND (";
					$po_ids = array_chunk($po_ids, 999);
					$z = 0;
					foreach ($po_ids as $id)
					{
						$id = implode(",", $id);
						if ($z == 0)
							$po_id_cond .= " c.po_id IN(" . $id . ")";
						else
							$po_id_cond .= " OR c.po_id IN(" . $id . ")";
						$z++;
					}
					$po_id_cond .= ")";
				}
				else
					$po_id_cond = "AND c.po_id IN(" . $po_id . ")";
			}
		}
		
		if ($db_type == 0)
		{
			$po_field = "group_concat(c.po_id) po_id";
		}
		else if ($db_type == 2)
		{
			$po_field = "listagg(cast(c.po_id as varchar2(4000)), ',') within group (order by c.po_id) as po_id";
		}
		
		if ($presentationType == 1) 
		{
			$sqlField = "a.company_id, a.buyer_id, a.body_part_id, a.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, b.location_id, c.booking_no, b.stitch_length, c.po_id, d.booking_type, d.is_short";
			
			$sqlTable = "ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c, wo_booking_mst d";
			
			$sqlWhere = "a.id = b.mst_id AND b.id = c.dtls_id  AND a.booking_no = d.booking_no AND a.company_id = ".$company_name." AND b.knitting_party LIKE '".$party_type."' AND b.machine_dia LIKE '".$machine_dia."' AND b.id LIKE '".$program_no."' and b.ref_closing_status!=1 AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 AND c.status_active = 1 AND c.is_deleted = 0 AND c.is_sales = 0 $location_cond $buyer_id_cond $po_id_cond $date_cond $booking_type_cond $booking_search_cond $status_cond $knitting_source_cond";
		
			
			if($booking_search_cond == '' && $exp_booking_type[0] != 0)
			{
				if( $exp_booking_type[0] != 5)
				{
					//$sqlTable .= ", wo_booking_mst d";
					//$sqlWhere .= $booking_type_cond;
				}
			}

			if ($txt_machine_no != '' || $floor_id != 0)
			{
				//$sqlTable .= ", ppl_entry_machine_datewise e";
				$sqlTable .= ", ppl_planning_info_machine_dtls e";
				//$sqlWhere .= " AND b.id = e.dtls_id AND e.machine_id IN(SELECT id FROM lib_machine_name WHERE machine_no = '".$txt_machine_no."')";
				$sqlWhere .= " AND b.id = e.dtls_id AND e.machine_id IN(SELECT id FROM lib_machine_name WHERE category_id = 1 AND status_active = 1 AND is_deleted = 0 AND company_id IN(".$company_name.") AND floor_id IN(".$floor_id."))";
			}
		}
		
		$sql = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere;
		//$sql = "SELECT ".$sqlField." FROM ".$sqlTable." WHERE ".$sqlWhere." GROUP BY ".$sqlField;
		//echo $sql;  
		$nameArray = sql_select($sql);

		if(!empty($nameArray))  
		{
			$con = connect();
	        $rID2=execute_query("delete from tmp_poid where userid=$user_name and type=12");     
	        $rID3=execute_query("delete from tmp_prog_no where userid=$user_name");      
	        $rID4=execute_query("delete from tmp_reqs_no where userid=$user_name");       
	        $rID5=execute_query("delete from tmp_recv_mst_id where userid=$user_name");       
	        $rID6=execute_query("delete from tmp_barcode_no where userid=$user_name");      
	        $rID7=execute_query("delete from tmp_prod_id where userid=$user_name");
	        if( $rID2 || $rID3 || $rID4 || $rID5 || $rID6 || $rID7)
	        {
	            oci_commit($con);
	        }
	        
		}

		$party_type = array();
		$program_arr = array();
		$po_arr = array();
		$po_id_check2 = array();
		$prog_no_check = array();
		foreach ($nameArray as $row) 
		{
			if(!$po_id_check2[$row[csf('po_id')]])
		    {
		        $po_id_check2[$row[csf('po_id')]]=$row[csf('po_id')];
		        $POIDs_2 = $row[csf('po_id')];
		        $rID2=execute_query("insert into tmp_poid (userid, poid,type) values ($user_name,$POIDs_2,12)");
		    }

		    if(!$prog_no_check[$row[csf('id')]])
		    {
		        $prog_no_check[$row[csf('id')]]=$row[csf('id')];
		        $ProgNOs = $row[csf('id')];
		        $rID3=execute_query("insert into tmp_prog_no (userid, prog_no) values ($user_name,$ProgNOs)");
		    }


			$party_type[$row[csf('knitting_source')]][$row[csf('knitting_party')]]=$row[csf('knitting_party')]; 
			$program_arr[$row[csf('id')]] = $row[csf('id')];
			$po_arr[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		if($rID2 || $rID3)
		{
		    oci_commit($con);
		}

		//echo "<pre>";
		//print_r($po_arr); die;
		
		$barcode_sql=sql_select("SELECT a.barcode_no FROM pro_roll_details a,tmp_poid x  WHERE a.po_breakdown_id=x.poid and x.type=12 and x.userid=$user_name and a.entry_form=58  AND a.status_active=1");
		//AND a.po_breakdown_id IN(".implode(",",$po_arr).")
		//NEED tmp po id type 12
		$po_barcode_arr=array();
		foreach($barcode_sql as $row)
		{
			$po_barcode_arr[$row[csf("barcode_no")]]=$row[csf("barcode_no")];
		}
		//echo "<pre>";
		//print_r($po_barcode_arr); die;

		if(!empty($po_arr))
		{
			$po_array = array();
			$all_po_cond = '';
			$po_id = "";
			$x = 0;

			if($db_type==2 &&  count($po_arr)>999)
			{
				$po_chunk=array_chunk($po_arr, 999);
				foreach($po_chunk as $row)
				{
					$po_ids=implode(",", $row);
					if($all_po_cond == '')
					{
						$all_po_cond.=" AND (b.id IN (".$po_ids.")";
					}
					else
					{
						$all_po_cond.=" OR b.id IN (".$po_ids.")";
					}
	
				}
				$all_po_cond.=")";
			}
			else
			{
				
				$all_po_cond=" AND b.id IN(".implode(",",$po_arr).")";
			}
			
			$costing_sql = sql_select("SELECT a.job_no, ".$year_field.", a.style_ref_no, b.id, b.po_number,b.pub_shipment_date, b.grouping, b.file_no FROM wo_po_details_master a, wo_po_break_down b,tmp_poid x WHERE a.job_no = b.job_no_mst and b.id=x.poid and x.type=12 and x.userid=$user_name AND a.company_name = ".$company_name." AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active = 1 AND b.is_deleted = 0 $job_cond $internal_cond $file_no_cond");
			//$all_po_cond 
			//NEED tmp po id type 12
			foreach ($costing_sql as $row)
			{
				$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
				$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
				$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
				$po_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
				$po_array[$row[csf('id')]]['year'] = $row[csf('year')];
				$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
				$po_array[$row[csf('id')]]['ref_no'] = $row[csf('grouping')];
			}
		}
		
		if(!empty($program_arr))
		{
			$program_cond = '';
			$program_cond2 = '';
			$program_cond3 = '';
			$program_cond5 = '';
			$reqsDataArr = array();
			$knitDataArr = array();
			if($db_type==2 &&  count($program_arr)>999)
			{
				$po_chunk=array_chunk($program_arr, 999);
				foreach($po_chunk as $row)
				{
					$program_ids=implode(",", $row);
					if($program_cond=="")
					{
						$program_cond.=" AND (dtls_id IN (".$program_ids.")";
						$program_cond2.=" AND (knit_id IN (".$program_ids.")";
						$program_cond3.=" AND (a.booking_id IN (".$program_ids.")";
						$program_cond5.=" AND (b.program_no IN (".$program_ids.")";
					}
					else
					{
						$program_cond.=" OR dtls_id IN (".$program_ids.")";
						$program_cond2.=" OR knit_id IN (".$program_ids.")";
						$program_cond3.=" OR a.booking_id IN (".$program_ids.")";
						$program_cond5.=" OR b.program_no IN (".$program_ids.")";
					}
				}
				$program_cond.=")";
				$program_cond2.=")";
				$program_cond3.=")";
				$program_cond5.=")";
			}
			else
			{
				$program_cond=" AND dtls_id IN(".implode(",",$program_arr).")";
				$program_cond2=" AND knit_id IN(".implode(",",$program_arr).")";
				$program_cond3=" AND a.booking_id IN(".implode(",",$program_arr).")";
				$program_cond5=" AND b.program_no IN(".implode(",",$program_arr).")";
			}
			
			if ($db_type == 0)
			{
				$plan_details_array = return_library_array("SELECT a.dtls_id, GROUP_CONCAT(DISTINCT(a.po_id)) AS po_id FROM ppl_planning_entry_plan_dtls a,tmp_prog_no x  WHERE a.dtls_id=x.prog_no and x.userid=$user_name and a.company_id=".$company_name."  GROUP BY a.dtls_id", "dtls_id", "po_id");
				//$program_cond
				//NEED tmp prog 
				
				$reqsData = sql_select("SELECT a.knit_id, a.requisition_no AS reqs_no, GROUP_CONCAT(DISTINCT(a.prod_id)) AS prod_id , SUM(a.yarn_qnty) AS yarn_req_qnty FROM ppl_yarn_requisition_entry a,tmp_prog_no x WHERE a.knit_id=x.prog_no and x.userid=$user_name and a.status_active=1 AND a.is_deleted=0  GROUP BY a.knit_id, a.requisition_no");
				//$program_cond2
				//NEED tmp prog 
			}
			else
			{

				//$plan_details_array = return_library_array("SELECT dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) AS po_id FROM ppl_planning_entry_plan_dtls WHERE company_id=".$company_name." ".$program_cond." GROUP BY dtls_id", "dtls_id", "po_id");

				$plan_details_array = return_library_array("SELECT a.dtls_id, LISTAGG(a.po_id, ',') WITHIN GROUP (ORDER BY a.po_id) AS po_id FROM ppl_planning_entry_plan_dtls a,tmp_prog_no x WHERE a.dtls_id=x.prog_no and x.userid=$user_name and a.company_id=".$company_name." GROUP BY a.dtls_id", "dtls_id", "po_id");
				//$program_cond
				//NEED tmp prog 
				
				//$reqsData = sql_select("SELECT knit_id, MAX(requisition_no) AS reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) AS prod_id, SUM(yarn_qnty) AS yarn_req_qnty FROM ppl_yarn_requisition_entry WHERE status_active=1 AND is_deleted=0 ".$program_cond2." GROUP BY knit_id, requisition_no");

				$reqsData = sql_select("SELECT a.knit_id, MAX(a.requisition_no) AS reqs_no, LISTAGG(a.prod_id, ',') WITHIN GROUP (ORDER BY a.prod_id) AS prod_id, SUM(a.yarn_qnty) AS yarn_req_qnty FROM ppl_yarn_requisition_entry a,tmp_prog_no x  WHERE a.knit_id=x.prog_no and x.userid=$user_name and a.status_active=1 AND a.is_deleted=0 GROUP BY a.knit_id, a.requisition_no ");
				//$program_cond2
				//NEED tmp prog
			}
			$requisition_no_check = array();
			$prod_id_check = array();
			foreach ($reqsData as $row)
			{
				if(!$requisition_no_check[$row[csf('reqs_no')]])
			    {
			        $requisition_no_check[$row[csf('reqs_no')]]=$row[csf('reqs_no')];
			        $ReqsNo = $row[csf('reqs_no')];
			        $rID4=execute_query("insert into tmp_reqs_no (userid, reqs_no) values ($user_name,$ReqsNo)");
			    }

			    $exp_prod_id = array();
				$exp_prod_id = explode(',',$row[csf('prod_id')]);
				foreach($exp_prod_id as $key=>$val)
				{
					if($prod_id_check[$val] != $val)
					{
						$prod_id_check[$val]=$val;
						$prod_arr[] = $val;
						$rID7=execute_query("insert into tmp_prod_id (userid, prod_id) values ($user_name,$val)");
					}
					
					$reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $val;
				}

				$reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
				$reqsDataArr[$row[csf('knit_id')]]['yarn_req_qnty'] = $row[csf('yarn_req_qnty')];
				$requisition_no_arr[] = $row[csf('reqs_no')];
			}

			if($rID4 || $rID7)
			{
			    oci_commit($con);
			}
			
			//knitDataArr
			$knitting_dataArray = sql_select("SELECT a.booking_id, a.id AS knit_id, b.gsm as gsm, b.grey_receive_qnty AS knitting_qnty, reject_fabric_receive AS fabric_reject_qty, b.trans_id, b.no_of_roll FROM tmp_prog_no x,inv_receive_master a, pro_grey_prod_entry_dtls b WHERE x.prog_no=a.booking_id and x.userid=$user_name and a.id=b.mst_id AND a.item_category=13 AND a.entry_form=2 AND a.receive_basis=2 AND b.status_active=1 AND b.is_deleted=0");
			//$program_cond3
			//NEED tmp prog 

			$recv_knitting_id_check = array();
			foreach ($knitting_dataArray as $row)
			{
				if ($row[csf('trans_id')]>0)
				{
					$autoProductionRecv[$row[csf('booking_id')]]['qnty'] += $row[csf('knitting_qnty')];
					$autoProductionRecv[$row[csf('booking_id')]]['no_of_roll'] += $row[csf('no_of_roll')];
				}
				$knitDataArr[$row[csf('booking_id')]]['qnty'] += $row[csf('knitting_qnty')];
				$knitDataArr[$row[csf('booking_id')]]['no_of_roll'] += $row[csf('no_of_roll')];
				$knitDataArr[$row[csf('booking_id')]]['reject_qnty'] += $row[csf('fabric_reject_qty')];

				if($knitDataArr[$row[csf('booking_id')]]['knit_id']!='')
				{
					$knitDataArr[$row[csf('booking_id')]]['knit_id'] .=",".$row[csf('knit_id')];
				}
				else
				{
					$knitDataArr[$row[csf('booking_id')]]['knit_id'] = $row[csf('knit_id')];
				}
				
				if($knitDataArr[$row[csf('booking_id')]]['gsm']!='')
				{
					$knitDataArr[$row[csf('booking_id')]]['gsm'] =",".$row[csf('gsm')];
				}
				else
				{
					$knitDataArr[$row[csf('booking_id')]]['gsm'] = $row[csf('gsm')];
				}

				if(!$recv_knitting_id_check[$row[csf('knit_id')]])
			    {
			        $recv_knitting_id_check[$row[csf('knit_id')]]=$row[csf('knit_id')];
			        $Knit_IDs = $row[csf('knit_id')];
			        $rID5=execute_query("insert into tmp_recv_mst_id (userid, mst_id) values ($user_name,$Knit_IDs)");
			    }
				$receive_ids_arr[$row[csf('knit_id')]] = $row[csf('knit_id')];
			}
			if($rID5)
			{
			    oci_commit($con);
			}
			//knit_recvProg_qty_arr

			//$knit_recvProg_qty_arr = return_library_array("SELECT b.program_no, SUM(b.grey_receive_qnty) AS knitting_qnty FROM inv_receive_master a, pro_grey_prod_entry_dtls b WHERE a.id=b.mst_id AND a.item_category=13 AND a.entry_form=22 AND a.receive_basis=11 AND b.status_active=1 AND b.is_deleted=0 GROUP BY b.program_no ".$program_cond5."", "program_no", "knitting_qnty");

			$knit_recvProg_qty_arr = return_library_array("SELECT b.program_no, SUM(b.grey_receive_qnty) AS knitting_qnty FROM inv_receive_master a, pro_grey_prod_entry_dtls b,tmp_prog_no x WHERE a.id=b.mst_id and b.program_no=x.prog_no and x.userid=$user_name AND a.item_category=13 AND a.entry_form=22 AND a.receive_basis=11 AND b.status_active=1 AND b.is_deleted=0 GROUP BY b.program_no", "program_no", "knitting_qnty");
			//$program_cond5
			//NEED tmp prog 
		}

		if(!empty($requisition_no_arr))
		{
			$requisition_cond = '';
			$requisition_cond2 = '';
			$yarn_iss_arr = array();
			$yarn_IssRtn_arr = array();
			if($db_type==2 && count($requisition_no_arr)>999)
			{
				$program_chunk=array_chunk($requisition_no_arr, 999);
				foreach($program_chunk as $row)
				{
					$program_ids=implode(",", $row);
					if($requisition_cond=="")
					{
						$requisition_cond.=" AND (requisition_no IN (".$program_ids.")";
						$requisition_cond2.=" AND (a.booking_id IN (".$program_ids.")";
						$program_cond3.=" AND (a.booking_id IN (".$program_ids.")";
					}
					else
					{
						$requisition_cond.=" OR requisition_no IN (".$program_ids.")";
						$requisition_cond2.=" OR a.booking_id IN (".$program_ids.")";
						$program_cond3.=" OR a.booking_id IN (".$program_ids.")";
					}
				}
				$requisition_cond.=")";
				$requisition_cond2.=")";
				$program_cond3.=")";
			}
			else
			{
				$requisition_cond=" AND requisition_no IN(".implode(",",$requisition_no_arr).")";
				$requisition_cond2=" AND a.booking_id IN(".implode(",",$requisition_no_arr).")";
				$program_cond3=" AND a.booking_id IN(".implode(",",$requisition_no_arr).")";
			}
			
			//for yarn_iss_arr
			$yarnIssueData = sql_select("SELECT a.requisition_no, a.prod_id, SUM(a.cons_quantity) AS qnty from inv_transaction a, tmp_reqs_no x  WHERE a.requisition_no=x.reqs_no and x.userid=$user_name and  item_category=1 AND transaction_type=2 AND receive_basis=3 AND status_active=1 AND is_deleted=0 group by a.requisition_no, a.prod_id ");
   			//$requisition_cond
			//Need TMP_REQS_NO 
			foreach ($yarnIssueData as $row)
			{
				$yarn_iss_arr[$row[csf('requisition_no')]][$row[csf('prod_id')]] = $row[csf('qnty')];
			}
			
			// for yarn_IssRtn_arr

			$yarnIssueRtnData = sql_select("SELECT a.requisition_no, a.prod_id, SUM(a.cons_quantity) AS qnty 
			from inv_transaction  a ,tmp_reqs_no x
			WHERE a.requisition_no=x.reqs_no and x.userid=$user_name and a.item_category=1 AND a.transaction_type=2 AND a.receive_basis=3 AND a.status_active=1 AND a.is_deleted=0
			group by a.requisition_no, a.prod_id");
			//$requisition_cond2
			//Need TMP_REQS_NO
			foreach ($yarnIssueRtnData as $row) 
			{
				$yarn_IssRtn_arr[$row[csf('reqsn_no')]][$row[csf('prod_id')]] = $row[csf('qnty')];
				$yarn_IssRej_arr[$row[csf('reqsn_no')]][$row[csf('prod_id')]] = $row[csf('reject_qnty')];
			}
			$cons_qty_arr = return_library_array("SELECT a.booking_id, SUM( b.cons_quantity) AS cons_quantity  FROM tmp_reqs_no x, inv_receive_master a, inv_transaction b, product_details_master c WHERE a.booking_id=x.reqsn_no and x.userid=$user_name and b.prod_id=c.id AND a.id=b.mst_id AND b.item_category=1 AND b.transaction_type=4  GROUP BY a.booking_id", 'booking_id', 'cons_quantity');
			//$program_cond3
			//Need TMP_REQS_NO
		}

		if(!empty($receive_ids_arr))
		{
			$program_cond4 = '';
			$all_receive_id_cond = '';
			$barcode_arr = array();

			if($db_type==2 &&  count($receive_ids_arr)>999)
			{
				$program_chunk=array_chunk($receive_ids_arr, 999);
				foreach($program_chunk as $row)
				{
					$production_ids=implode(",", $row);
					if($program_cond4=="")
					{
						$program_cond4.=" AND (a.booking_id IN (".$production_ids.")";
						$all_receive_id_cond.=" AND (a.id IN (".$production_ids.")";
					}
					else
					{
						$program_cond4.=" OR a.booking_id IN (".$production_ids.")";
						$all_receive_id_cond.=" OR a.id IN (".$production_ids.")";
					}
				}
				$program_cond4.=")";
				$all_receive_id_cond.=")";
			}
			else
			{
				$program_cond4=" AND a.booking_id IN(".implode(",",$receive_ids_arr).")";
				$all_receive_id_cond=" AND a.id IN(".implode(",",$receive_ids_arr).")";
			}

			//knitting_recv_qnty_array
	
			$knitting_recv_qnty_array = return_library_array("SELECT a.booking_id, SUM(b.grey_receive_qnty) AS knitting_qnty FROM tmp_recv_mst_id x, inv_receive_master a, pro_grey_prod_entry_dtls b WHERE a.booking_id=x.mst_id and x.userid=$user_name and a.id=b.mst_id AND a.item_category=13 AND a.entry_form=22 AND a.receive_basis=9 AND b.status_active=1 AND b.is_deleted=0 GROUP BY a.booking_id", "booking_id", "knitting_qnty");
			//$program_cond4
			//Need tmp_recv_mst_id
			
			//barcodeData
			$barcodeData = sql_select("SELECT b.mst_id, b.barcode_no FROM tmp_recv_mst_id x, inv_receive_master a, pro_roll_details b WHERE a.id=x.mst_id and x.userid=$user_name and a.id=b.mst_id AND a.entry_form=2 AND b.entry_form=2 AND a.item_category=13 AND a.receive_basis=2");
			//$all_receive_id_cond
			//Need tmp_recv_mst_id

			$barcode_no_check = array();
			foreach ($barcodeData as $row)
			{
				if(!$barcode_no_check[$row[csf('barcode_no')]])
			    {
			        $barcode_no_check[$row[csf('barcode_no')]]=$row[csf('barcode_no')];
			        $BarcodeNOs = $row[csf('barcode_no')];
			        $rID6=execute_query("insert into tmp_barcode_no (userid, barcode_no) values ($user_name,$BarcodeNOs)");
			    }
				$barcode_arr[$row[csf('mst_id')]] .= $row[csf('barcode_no')].",";
				$barcode_no_arr[$row[csf('barcode_no')]] = $row[csf('barcode_no')];
			}
			if($rID6)
			{
			    oci_commit($con);
			}

		}


		//$barcode_no_arr=array_filter($barcode_no_arr);
		if(!empty($barcode_no_arr))
		{
			$all_barcode_nos = implode(",",$barcode_no_arr);
			$all_barcode_no_cond="";
			$barCond=""; 
			$deliveryStorQtyArr = array();
			
			if($db_type==2 && count($barcode_no_arr)>999)
			{
				$barcode_no_arr_chunk=array_chunk($barcode_no_arr,999) ;
				foreach($barcode_no_arr_chunk as $chunk_arr)
				{
					$chunk_arr_value=implode(",",$chunk_arr);	
					$barCond.="  a.barcode_no IN(".$chunk_arr_value.") or ";	
				}
				
				$all_barcode_no_cond.=" AND (".chop($barCond,'or ').")";	
			}
			else
			{
				$all_barcode_no_cond=" AND a.barcode_no IN(".$all_barcode_nos.")";	 
			}
			
			$delivery_qty_arr = return_library_array("SELECT a.barcode_no, a.qnty FROM pro_roll_details a,tmp_barcode_no x WHERE a.barcode_no=x.barcode_no and x.userid=$user_name and a.entry_form=58 AND a.status_active=1 AND a.is_deleted=0 ", "barcode_no", "qnty");
			//$all_barcode_no_cond
			//Need tmp barcode no
			
			$recv_roll_no_arr = return_library_array("SELECT a.barcode_no, 1 FROM pro_roll_details a,tmp_barcode_no x WHERE a.barcode_no=x.barcode_no and x.userid=$user_name and a.entry_form=58 AND a.status_active=1 AND a.is_deleted=0 ", "barcode_no", "1");
			//$all_barcode_no_cond
			//Need tmp barcode no

			$deliveryquantityArr = sql_select("SELECT a.booking_no, b.current_delivery ,COUNT(b.barcode_num) AS roll_no_delv FROM ,tmp_barcode_no x, pro_roll_details a, pro_grey_prod_delivery_dtls b WHERE a.barcode_no=x.barcode_no and x.userid=$user_name and a.mst_id=b.grey_sys_id AND a.barcode_no=b.barcode_num AND a.entry_form=2 AND a.receive_basis=2 AND a.booking_without_order=0  AND b.status_active=1 AND b.is_deleted=0 AND a.status_active=1 AND a.is_deleted=0 GROUP BY a.booking_no, a.barcode_no, b.current_delivery, b.barcode_num");
			//$all_barcode_no_cond
			//Need tmp barcode no

			foreach ($deliveryquantityArr as $row)
			{
				$deliveryStorQtyArr[$row[csf('booking_no')]] += $row[csf('current_delivery')];
				$deliveryStorNoOfRollArr[$row[csf('booking_no')]] += $row[csf('roll_no_delv')];
			}
		}

		if(!empty($prod_arr))
		{
			$product_details_arr = array();
			$procuct_cond = (!empty($prod_arr))?" AND id IN(".implode(",",$prod_arr).")":"";
			$pro_sql = sql_select("SELECT a.id, a.product_name_details, a.lot, a.supplier_id FROM product_details_master a, tmp_prod_id x WHERE a.id=x.prod_id and x.userid=$user_name and a.company_id = ".$company_name." AND a.item_category_id=1 ");
			//$procuct_cond
			//Need tmp_prod_id
			foreach ($pro_sql as $row)
			{
				$product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
				$product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
				$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
			}
		}
		
		//for floor
		$floorCond = '';
		if($floor_id != 0)
		{
			$floorCond = " AND b.floor_id = ".$floor_id."";
		}
		
		//$sqlFloor = "SELECT a.dtls_id, b.floor_id FROM ppl_entry_machine_datewise a, lib_machine_name b WHERE a.machine_id=b.id ".$program_cond."  GROUP BY a.dtls_id, b.floor_id";
		$sqlFloor = "SELECT a.dtls_id, b.floor_id FROM ppl_planning_info_machine_dtls a, lib_machine_name b WHERE a.machine_id=b.id ".$program_cond." ".$floorCond." GROUP BY a.dtls_id, b.floor_id";
		//NEED tmp prog  type 13
		//echo $sqlFloor;
		$resultSetFloor = sql_select($sqlFloor);
		$floorDataArr = array();
		foreach($resultSetFloor as $row)
		{
			$floorDataArr[$row[csf('dtls_id')]] = $row[csf('floor_id')];
		}
		//echo "<pre>";
		//print_r($floorDataArr); die;
		
		$company_dtls = return_library_array("SELECT id, company_name FROM lib_company WHERE status_active = 1", "id", "company_name");
		$location_dtls = return_library_array("SELECT id, location_name FROM lib_location WHERE status_active = 1", "id", "location_name");
		$floor_dtls = return_library_array("SELECT id, floor_name FROM lib_prod_floor WHERE status_active = 1 AND production_process = 2", "id", "floor_name");
		$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
		//$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
		$supplier_details = return_library_array("select id, short_name from lib_supplier", "id", "short_name");
		$color_type_arr = return_library_array("select id, color_type_id from wo_pre_cost_fabric_cost_dtls", 'id', 'color_type_id');
		$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
		$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
		$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");

		//data preparing
		$rptPrint = array();
		$totalQty = array();
		$totalCompanyQty = array();
		$totalLocationQty = array();
		$totalFloorQty = array();
		$totalDiaQty = array();
		foreach ($nameArray as $row)
		{

			//for floor
			if($floorDataArr[$row[csf('id')]] == '')
			{
				$floorZS = 0;
			}
			else
			{
				$floorZS = $floorDataArr[$row[csf('id')]];
			}
			
			//for knitting_source
			$knitting_source=$row[csf('knitting_source')];
			if($knitting_source==1)
			{ 
				$knitting_cond="Inside";
				$knitting_party=$company_library[$row[csf('knitting_party')]];
			}
			else
			{  
				$knitting_cond="Outside";
				$knitting_party=$supplier_details[$row[csf('knitting_party')]];
			}

			$machine_dia_gg = $row[csf('machine_dia')] . 'X' . $row[csf('machine_gg')];

			$machine_no = '';
			$machine_id = explode(",", $row[csf("machine_id")]);
			foreach ($machine_id as $val) 
			{
				if ($machine_no == '')
					$machine_no = $machine_arr[$val];
				else
					$machine_no .= "," . $machine_arr[$val];
			}

			$gmts_color = '';
			$color_id = explode(",", $row[csf("color_id")]);
			foreach ($color_id as $val) 
			{
				if ($gmts_color == '')
					$gmts_color = $color_library[$val];
				else
					$gmts_color .= "," . $color_library[$val];
			}

			$prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
			$yarn_desc = '';
			$lot = '';
			$supplier = '';
			$yarn_issue_qnty = 0;
			$yarn_issue_return_qnty = 0;
			$yarn_issue_reject_qnty = 0;
			foreach ($prod_id as $val) 
			{
				$yarn_desc .= $product_details_arr[$val]['desc'] . ",";
				$lot .= $product_details_arr[$val]['lot'] . ",";
				$supplier .= $supplier_details[$product_details_arr[$val]['supplier']] . ",";

				$yarn_issue_return_qnty += $yarn_IssRtn_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];

				$yarn_issue_reject_qnty += $yarn_IssRej_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];

				$yarn_issue_qnty += $yarn_iss_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val]; //- $yarn_IssRtn_arr[$reqsDataArr[$row[csf('id')]]['reqs_no']][$val];
			}
			$yarn_desc = explode(",", substr($yarn_desc, 0, -1));
			$lot = explode(",", substr($lot, 0, -1));
			$supplier = explode(",", substr($supplier, 0, -1));

			$po_id = array_unique(explode(",", $plan_details_array[$row[csf('id')]]));
			$po_no = '';
			$style_ref = '';
			$job_no = '';
			$file_nos = '';
			$ref_nos = '';
			$ref_nos_arr = array();
			$ship_date = array();
			foreach ($po_id as $val) 
			{
				if ($po_no == '')
					$po_no = $po_array[$val]['no'];
				else
					$po_no .= ", " . $po_array[$val]['no'];
				if ($style_ref == '')
					$style_ref = $po_array[$val]['style_ref'];
				if ($job_no == '')
					$job_no = $po_array[$val]['job_no'];

				if ($po_array[$val]['file_no'] != "") {
					if ($file_nos == '')
						$file_nos = $po_array[$val]['file_no'];
					else
						$file_nos .= "," . $po_array[$val]['file_no'];
				}

				if ($po_array[$val]['ref_no'] != "") {
					if ($ref_nos == ''){
						$ref_nos = $po_array[$val]['ref_no'];
						$ref_nos_arr[$po_array[$val]['ref_no']]= $po_array[$val]['ref_no'];
					}else{
						$ref_nos .= "," . $po_array[$val]['ref_no'];
						$ref_nos_arr[$po_array[$val]['ref_no']]= $po_array[$val]['ref_no'];
					}
				}
				
				//for shipment date
				if($po_array[$val]['ship_date'] != '')
				{
					$shp_dt = date('d-m-Y', strtotime($po_array[$val]['ship_date']));
					$ship_date[$shp_dt] = $shp_dt;
				}
			}

			$knitting_qnty = $knitDataArr[$row[csf('id')]]['qnty'] + $knit_recvProg_qty_arr[$row[csf('id')]];
			$knit_id = $knitDataArr[$row[csf('id')]]['knit_id'];
			$fabric_reject_qnty = $knitDataArr[$row[csf('id')]]['reject_qnty'];

			$knit_id = array_unique(explode(",", $knit_id));
			$knit_recv_qty = 0;
			$knitting_recv_qnty = 0;
			foreach ($knit_id as $val) 
			{
				$delivery_qty = 0;$no_of_rollRec= 0;
				$barcode_nos = explode(",", chop($barcode_arr[$val], ','));
				foreach ($barcode_nos as $barcode_no) 
				{
					if($po_barcode_arr[$barcode_no]) // add new
					{
						$delivery_qty += $delivery_qty_arr[$barcode_no];
						$no_of_rollRec+= $recv_roll_no_arr[$barcode_no];
					}
				}
				$knit_recv_qty +=  $knitting_recv_qnty_array[$val] + $delivery_qty;
			}
			$knitting_recv_qnty = $knit_recv_qty + $knit_recvProg_qty_arr[$row[csf('id')]] + $autoProductionRecv[$row[csf('id')]]['qnty'] ;
			//+ $autoProductionRecv[$row[csf('id')]]['qnty']

			$no_of_roll_knit = $knitDataArr[$row[csf('id')]]['no_of_roll'];
			$no_of_roll_delv = $deliveryStorNoOfRollArr[$row[csf('id')]];
			$no_of_roll_recv=$autoProductionRecv[$row[csf('id')]]['no_of_roll']+$no_of_rollRec;
			$no_of_roll_balance=$no_of_roll_knit-$no_of_roll_recv;

			$balance_qnty = $row[csf('program_qnty')] - $knitting_qnty;
			$balance_recv_qnty = $knitting_qnty - $knitting_recv_qnty;
			$yarn_issue_bl_qnty = $reqsDataArr[$row[csf('id')]]['yarn_req_qnty'] - $yarn_issue_qnty;

			$complete = '&nbsp;';
			if ($knitting_qnty >= $row[csf('program_qnty')])
				$complete = 'Complete';
				
			//for booking type
			if($row[csf('booking_type')] == 1)
			{
				if( $row[csf('is_short')] == 1)
				{
					$bookingType = 'Short';
				}
				else
				{
					$bookingType = 'Main';
				}
			}
			else if($row[csf('booking_type')] == 4)
			{
				if( $row[csf('is_short')] == 2)
				{
					$bookingType = 'Sample With';
				}
			}

			$typeZS = $knitting_cond;
			//$comZS = $row[csf('company_id')];
			$comZS = $row[csf('knitting_party')];
			$locZS = $row[csf('location_id')];
			$pogramZS = $row[csf('id')];
			$diaZS = $machine_dia_gg;
			
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['programNo'] = $pogramZS;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['programDate'] = change_date_format($row[csf('program_date')]);
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['bookingType'] = $bookingType;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['bookingNo'] = $row[csf('booking_no')];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['jobNo'] = $job_no;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['buyer'] = $buyer_arr[$row[csf('buyer_id')]];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['orderNo'] = $po_no;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['intRef'] = implode(",",array_unique($ref_nos_arr));
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['style'] = $style_ref;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['shipDate'] = implode(', ', $ship_date);
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['fabricDescription'] = $row[csf('fabric_desc')];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['fabricColor'] = $gmts_color;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['fabricType'] = $color_type[$row[csf('color_type_id')]];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['fabricGsm'] = $row[csf('gsm_weight')];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['fabricDia'] = $row[csf('dia')];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['stitchLength'] = $row[csf('stitch_length')];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['widthDiaType'] = $fabric_typee[$row[csf('width_dia_type')]];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['requistionNo'] = $reqsDataArr[$pogramZS]['reqs_no'];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['yarnDescription'] = join(",", array_unique($yarn_desc));
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['supplier'] = implode(", ", array_unique($supplier));
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['lot'] = join(", ", array_unique($lot));
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['programQty'] = $row[csf('program_qnty')];
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['knitProduction'] = $knitting_qnty;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['balance'] = $balance_qnty;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['requisitionQty'] = $reqsDataArr[$row[csf('id')]]['yarn_req_qnty'];
			$requi_balance = ($row[csf('program_qnty')] - $reqsDataArr[$pogramZS]['yarn_req_qnty']);
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['requisitionBalance'] = $requi_balance;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['demandQty'] = 0;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['demandBalance'] = 0;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['yarnIssueQty'] = $yarn_issue_qnty;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['yarnReturnQty'] = $yarn_issue_return_qnty;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['fabricRejectQty'] = $yarn_issue_reject_qnty;
			$rptPrint[$typeZS][$comZS][$locZS][$floorZS][$diaZS][$pogramZS]['issueBalanceQty'] = $yarn_issue_bl_qnty;
		}
		//echo "<pre>";
		//print_r($rptPrint); die;
		
        $rID2=execute_query("delete from tmp_poid where userid=$user_name and type=12");     
        $rID3=execute_query("delete from tmp_prog_no where userid=$user_name");      
        $rID4=execute_query("delete from tmp_reqs_no where userid=$user_name");       
        $rID5=execute_query("delete from tmp_recv_mst_id where userid=$user_name");       
        $rID6=execute_query("delete from tmp_barcode_no where userid=$user_name");      
        $rID7=execute_query("delete from tmp_prod_id where userid=$user_name");
        if( $rID || $rID2 || $rID3 || $rID4 || $rID5 || $rID6 || $rID7)
        {
            oci_commit($con);
        }

		ob_start();
		$colspan = 33;
		$tbl_width = 3150;
		?>
        <fieldset style="width:<? echo $tbl_width; ?>px;">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" >
                <thead>
                    <th width="40">SL</th>
                    <th width="60">Program No</th>
                    <th width="80">Program Date</th>
                    <th width="80">Booking Type</th>
                    <th width="100">Booking No</th>
                    <th width="100">Job No</th>
                    <th width="80">Buyer</th>
                    <th width="130">Order No</th>
                    <th width="80">Int. Ref.</th>
                    <th width="110">Style</th>
                    <th width="80">Ship Date</th>
                    <th width="140">Fabric Desc.</th>
                    <th width="140">Fabric Color</th>
                    <th width="100">Color Type</th>
                    <th width="70">Fabric Gsm</th>
                    <th width="70">Fabric Dia</th>
                    <th width="80">Width/Dia Type</th>
                    <th width="70">Stitch Length</th>
                    <th width="70">Req. No</th>
                    <th width="170">Desc.Of Yarn</th>
                    <th width="130">Supplier</th>
                    <th width="70">Lot</th>
                    <th width="100">Program Qnty</th>
                    <th width="100">Knitting Qnty</th>
                    <th width="100">Balance Qnty</th>
                    <th width="100">Requsition Qnty</th>
                    <th width="100">Requsition Balance<br><p style="font-size: 9px">(prog. - Req.)</p></th>
                    <th width="100">Demand Qnty</th>
                    <th width="100">Demand Balance</th>
                    <th width="100">Yarn Issue Qnty</th>
                    <th width="100">Issue Return Qnty</th>
                    <th width="100">Reject Qnty</th>
                    <th>Issue. Bal. Qnty<br><p style="font-size: 9px">(Req. - Issue)</p></th>
                </thead>
            </table>
            <div style="width:<? echo $tbl_width+18; ?>px; overflow-y:scroll; max-height:450px;" id="scroll_body">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $tbl_width; ?>" class="rpt_table" id="tbl_list_search">
                    <tbody>
                    <?php
                    $i = 0;
                    foreach($rptPrint as $type=>$typeArr)
                    {
                        ?>
                        <tr bgcolor="#C6D9C9" style="font-weight:bold;">
                            <td colspan="2">Type</td>
                            <td colspan="<?php echo $colspan-2; ?>" style="border-left:hidden;">: <?php echo $type; ?></td>
                        </tr>
                        <?php
                        foreach($typeArr as $companyId=>$companyArr)
                        {
							$companyName = $company_dtls[$companyId];
							if($type != 'Inside')
							{
								$companyName = $supplier_details[$companyId];
							}
							?>
                            <tr bgcolor="#C6D9C9" style="font-weight:bold;">
                                <td colspan="2">Company</td>
                                <td colspan="<?php echo $colspan-2; ?>" style="border-left:hidden;">: <?php echo $companyName; ?></td>
                            </tr>
                            <?php
                            $totalCompanyQty = array();
                            foreach($companyArr as $locationId=>$locationArr)
                            {
                                if($type == 'Inside')
								{
								?>
                                <tr bgcolor="#C6D9C9" style="font-weight:bold;">
                                    <td colspan="2">Location</td>
                                    <td colspan="<?php echo $colspan-2; ?>" style="border-left:hidden;">: <?php echo $location_dtls[$locationId]; ?></td>
                                </tr>
                                <?php
								}
                                $totalLocationQty = array();
                                foreach($locationArr as $floorId=>$floorArr)
                                {
									if($type == 'Inside')
									{
								    ?>
                                    <tr bgcolor="#C6D9C9" style="font-weight:bold;">
                                        <td colspan="2">Floor</td>
                                        <td colspan="<?php echo $colspan-2; ?>" style="border-left:hidden;">: <?php echo $floor_dtls[$floorId]; ?></td>
                                    </tr>
                                    <?php
									}
                                    $totalFloorQty = array();
                                    foreach($floorArr as $dia=>$diaArr)
                                    {
                                        ?>
                                        <tr bgcolor="#C6D9C9" style="font-weight:bold;">
                                            <td colspan="2">Machine Dia</td>
                                            <td colspan="<?php echo $colspan-2; ?>" style="border-left:hidden;">: <?php echo $dia; ?></td>
                                        </tr>
                                        <?php
                                        $totalDiaQty = array();
                                        foreach($diaArr as $prog=>$row)
                                        {
                                            $i++;
											if ($i % 2 == 0)
												$bgcolor = "#E9F3FF";
											else
												$bgcolor = "#FFFFFF";
                                            ?>
                                            <tr bgcolor="<?php echo $bgcolor; ?>" valign="middle">
                                                <td width="40" align="center"><?php echo $i; ?></td>
                                                <td width="60" align="center" style="word-break:break-all;"><?php echo $row['programNo']; ?></td>
                                                <td width="80" align="center" style="word-break:break-all;"><?php echo $row['programDate']; ?></td>
                                                <td width="80" align="center" style="word-break:break-all;"><?php echo $row['bookingType']; ?></td>
                                                <td width="100" align="center" style="word-break:break-all;"><?php echo $row['bookingNo']; ?></td>
                                                <td width="100" align="center" style="word-break:break-all;"><?php echo $row['jobNo']; ?></td>
                                                <td width="80" style="word-break:break-all;"><?php echo $row['buyer']; ?></td>
                                                <td width="130" style="word-break:break-all;"><?php echo $row['orderNo']; ?></td>
                                                <td width="80" style="word-break:break-all;"><?php echo $row['intRef']; ?></td>
                                                <td width="110" style="word-break:break-all;"><?php echo $row['style']; ?></td>
                                                <td width="80" align="center" style="word-break:break-all;"><?php echo $row['shipDate']; ?></td>
                                                <td width="140" style="word-break:break-all;"><?php echo $row['fabricDescription']; ?></td>
                                                <td width="140" style="word-break:break-all;"><?php echo $row['fabricColor']; ?></td>
                                                <td width="100" style="word-break:break-all;"><?php echo $row['fabricType']; ?></td>
                                                <td width="70" align="center" style="word-break:break-all;"><?php echo $row['fabricGsm']; ?></td>
                                                <td width="70" align="center" style="word-break:break-all;"><?php echo $row['fabricDia']; ?></td>
                                                <td width="80" align="center" style="word-break:break-all;"><?php echo $row['widthDiaType']; ?></td>
                                                <td width="70" align="center" style="word-break:break-all;"><?php echo $row['stitchLength']; ?></td>
                                                <td width="70" align="center" style="word-break:break-all;"><?php echo $row['requistionNo']; ?></td>
                                                <td width="170" style="word-break:break-all;"><?php echo $row['yarnDescription']; ?></td>
                                                <td width="130" style="word-break:break-all;"><?php echo $row['supplier']; ?></td>
                                                <td width="70" style="word-break:break-all;"><?php echo $row['lot']; ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['programQty'], 2); ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['knitProduction'], 2); ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['balance'], 2); ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['requisitionQty'], 2); ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['requisitionBalance'], 2); ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['demandQty'], 2); ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['demandBalance'], 2); ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['yarnIssueQty'], 2); ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['yarnReturnQty'], 2); ?></td>
                                                <td width="100" align="right"><?php echo number_format($row['fabricRejectQty'], 2); ?></td>
                                                <td align="right"><?php echo number_format($row['issueBalanceQty'], 2); ?></td>
                                            </tr>
                                            <?php
                                            //for dia total
                                            $totalDiaQty['programQty'] += number_format($row['programQty'], 2, '.', '');
                                            $totalDiaQty['knitProduction'] += number_format($row['knitProduction'], 2, '.', '');
                                            $totalDiaQty['balance'] += number_format($row['balance'], 2, '.', '');
                                            $totalDiaQty['requisitionQty'] += number_format($row['requisitionQty'], 2, '.', '');
                                            $totalDiaQty['requisitionBalance'] += number_format($row['requisitionBalance'], 2, '.', '');
                                            $totalDiaQty['demandQty'] += 0;
                                            $totalDiaQty['demandBalance'] += 0;
                                            $totalDiaQty['yarnIssueQty'] += number_format($row['yarnIssueQty'], 2, '.', '');
                                            $totalDiaQty['yarnReturnQty'] += number_format($row['yarnReturnQty'], 2, '.', '');
                                            $totalDiaQty['fabricRejectQty'] += number_format($row['fabricRejectQty'], 2, '.', '');
                                            $totalDiaQty['issueBalanceQty'] += number_format($row['issueBalanceQty'], 2, '.', '');
                                            
                                            //for floor total
                                            $totalFloorQty['programQty'] += number_format($row['programQty'], 2, '.', '');
                                            $totalFloorQty['knitProduction'] += number_format($row['knitProduction'], 2, '.', '');
                                            $totalFloorQty['balance'] += number_format($row['balance'], 2, '.', '');
                                            $totalFloorQty['requisitionQty'] += number_format($row['requisitionQty'], 2, '.', '');
                                            $totalFloorQty['requisitionBalance'] += number_format($row['requisitionBalance'], 2, '.', '');
                                            $totalFloorQty['demandQty'] += 0;
                                            $totalFloorQty['demandBalance'] += 0;
                                            $totalFloorQty['yarnIssueQty'] += number_format($row['yarnIssueQty'], 2, '.', '');
                                            $totalFloorQty['yarnReturnQty'] += number_format($row['yarnReturnQty'], 2, '.', '');
                                            $totalFloorQty['fabricRejectQty'] += number_format($row['fabricRejectQty'], 2, '.', '');
                                            $totalFloorQty['issueBalanceQty'] += number_format($row['issueBalanceQty'], 2, '.', '');
                                            
                                            //for location total
                                            $totalLocationQty['programQty'] += number_format($row['programQty'], 2, '.', '');
                                            $totalLocationQty['knitProduction'] += number_format($row['knitProduction'], 2, '.', '');
                                            $totalLocationQty['balance'] += number_format($row['balance'], 2, '.', '');
                                            $totalLocationQty['requisitionQty'] += number_format($row['requisitionQty'], 2, '.', '');
                                            $totalLocationQty['requisitionBalance'] += number_format($row['requisitionBalance'], 2, '.', '');
                                            $totalLocationQty['demandQty'] += 0;
                                            $totalLocationQty['demandBalance'] += 0;
                                            $totalLocationQty['yarnIssueQty'] += number_format($row['yarnIssueQty'], 2, '.', '');
                                            $totalLocationQty['yarnReturnQty'] += number_format($row['yarnReturnQty'], 2, '.', '');
                                            $totalLocationQty['fabricRejectQty'] += number_format($row['fabricRejectQty'], 2, '.', '');
                                            $totalLocationQty['issueBalanceQty'] += number_format($row['issueBalanceQty'], 2, '.', '');
                                
                                            //for company total
                                            $totalCompanyQty['programQty'] += number_format($row['programQty'], 2, '.', '');
                                            $totalCompanyQty['knitProduction'] += number_format($row['knitProduction'], 2, '.', '');
                                            $totalCompanyQty['balance'] += number_format($row['balance'], 2, '.', '');
                                            $totalCompanyQty['requisitionQty'] += number_format($row['requisitionQty'], 2, '.', '');
                                            $totalCompanyQty['requisitionBalance'] += number_format($row['requisitionBalance'], 2, '.', '');
                                            $totalCompanyQty['demandQty'] += 0;
                                            $totalCompanyQty['demandBalance'] += 0;
                                            $totalCompanyQty['yarnIssueQty'] += number_format($row['yarnIssueQty'], 2, '.', '');
                                            $totalCompanyQty['yarnReturnQty'] += number_format($row['yarnReturnQty'], 2, '.', '');
                                            $totalCompanyQty['fabricRejectQty'] += number_format($row['fabricRejectQty'], 2, '.', '');
                                            $totalCompanyQty['issueBalanceQty'] += number_format($row['issueBalanceQty'], 2, '.', '');
                                
                                            //for total
                                            $totalQty['programQty'] += number_format($row['programQty'], 2, '.', '');
                                            $totalQty['knitProduction'] += number_format($row['knitProduction'], 2, '.', '');
                                            $totalQty['balance'] += number_format($row['balance'], 2, '.', '');
                                            $totalQty['requisitionQty'] += number_format($row['requisitionQty'], 2, '.', '');
                                            $totalQty['requisitionBalance'] += number_format($row['requisitionBalance'], 2, '.', '');
                                            $totalQty['demandQty'] += 0;
                                            $totalQty['demandBalance'] += 0;
                                            $totalQty['yarnIssueQty'] += number_format($row['yarnIssueQty'], 2, '.', '');
                                            $totalQty['yarnReturnQty'] += number_format($row['yarnReturnQty'], 2, '.', '');
                                            $totalQty['fabricRejectQty'] += number_format($row['fabricRejectQty'], 2, '.', '');
                                            $totalQty['issueBalanceQty'] += number_format($row['issueBalanceQty'], 2, '.', '');
                                        }
                                        ?>
                                        <tr bgcolor="#C6D9C9" style="font-weight:bold;">
                                            <td align="right" colspan="<?php echo $colspan-11; ?>">Sub Total: </td>
                                            <?php
                                            foreach($totalDiaQty as $diaTot)
                                            {
                                                ?>
                                                <td align="right"><?php echo number_format($diaTot, 2);?></td>
                                                <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
									if($type == 'Inside')
									{
										?>
										<tr bgcolor="#C6D9C9" style="font-weight:bold;">
											<td align="right" colspan="<?php echo $colspan-11; ?>">Floor Total: </td>
											<?php
											foreach($totalFloorQty as $floorTot)
											{
												?>
												<td align="right"><?php echo number_format($floorTot, 2);?></td>
												<?php
											}
											?>
										</tr>
										<?php
									}
                                }
                                if($type == 'Inside')
								{
									?>
									<tr bgcolor="#C6D9C9" style="font-weight:bold;">
										<td align="right" colspan="<?php echo $colspan-11; ?>">Location Total: </td>
										<?php
										foreach($totalLocationQty as $locationTot)
										{
											?>
											<td align="right"><?php echo number_format($locationTot, 2);?></td>
											<?php
										}
										?>
									</tr>
									<?php
								}
                            }
                            ?>
                            <tr bgcolor="#C6D9C9" style="font-weight:bold;">
                                <td align="right" colspan="<?php echo $colspan-11; ?>">Company Total: </td>
                                <?php
                                foreach($totalCompanyQty as $companyTot)
                                {
                                    ?>
                                    <td align="right"><?php echo number_format($companyTot, 2);?></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                        }
                    }
                    //die;
                    ?>
                </tbody>
                <tfoot>
                    <tr bgcolor="#C6D9C9" style="font-weight:bold;">
                        <th align="right" colspan="<?php echo $colspan-11; ?>">Grand Total: </th>
                        <?php
                        foreach($totalQty as $grandTot)
                        {
                            ?>
                            <th align="right"><?php echo number_format($grandTot, 2);?></th>
                            <?php
                        }
                        ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    	</fieldset>
		<?php
    }

    foreach (glob("$user_name*.xls") as $filename)
	{
    	if (@filemtime($filename) < (time() - $seconds_old))
    		@unlink($filename);
    }
    //---------end------------//
    $name = time();
    $filename = $user_name . "_" . $name . ".xls";
    $create_new_doc = fopen($filename, 'w');
    $is_created = fwrite($create_new_doc, ob_get_contents());
    $filename = "requires/" . $user_name . "_" . $name . ".xls";
    echo "$total_data####$filename";
    exit();
}

/*
|--------------------------------------------------------------------------
| company_wise_report_button_setting
|--------------------------------------------------------------------------
*/
if($action=="company_wise_report_button_setting")
{
	extract($_REQUEST);
	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=4 and report_id=41 and is_deleted=0 and status_active=1");
	$print_report_format_arr=explode(",",$print_report_format);
	echo "$('#Print').hide();\n";
	echo "$('#Print2').hide();\n";
	echo "$('#Print3').hide();\n";
	echo "$('#Print4').hide();\n";
	echo "$('#Print5').hide();\n";
	echo "$('#Print6').hide();\n";
	echo "$('#Print7').hide();\n";
	//echo "$('#Print8').hide();\n";
	echo "$('#Print9').hide();\n";
	//echo "$('#Print10').hide();\n";

	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==130){echo "$('#Print').show();\n";} 
			if($id==131){echo "$('#Print2').show();\n";}    
			if($id==132){echo "$('#Print3').show();\n";}    
			if($id==133){echo "$('#Print4').show();\n";}    
			if($id==231){echo "$('#Print5').show();\n";}    
			if($id==232){echo "$('#Print6').show();\n";}
			if($id==89){echo "$('#Print7').show();\n";}  
			//if($id==580){echo "$('#Print8').show();\n";}  
			if($id==287){echo "$('#Print9').show();\n";}    
			//if($id==581){echo "$('#Print10').show();\n";}  			
		}
	}
	else
	{
		echo "$('#Print').show();\n";
		echo "$('#Print2').show();\n";
		echo "$('#Print3').show();\n";
		echo "$('#Print4').show();\n";
		echo "$('#Print5').show();\n";
		echo "$('#Print6').show();\n";
		echo "$('#Print7').show();\n";
		echo "$('#Print8').show();\n";
		echo "$('#Print9').show();\n";
		echo "$('#Print10').show();\n";
	}  
	exit(); 
}

if ($action == "job_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {

			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_no').val(id);
			$('#hide_job_id').val(name);
		}

	</script>

</head>

<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Buyer</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th>Shipment Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
								?>
							</td>                 
							<td align="center">	
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref", 3 => "Order No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>     
							<td align="center" id="search_by_td">				
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
							</td> 
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>	
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value, 'create_job_no_search_list_view', 'search_div', 'knitting_program_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
</body>           
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if ($action == "create_job_no_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];

	if ($data[1] == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] != "")
				$buyer_id_cond = " and a.buyer_name in (" . $_SESSION['logic_erp']["buyer_id"] . ")";
			else
				$buyer_id_cond = "";
		}
		else {
			$buyer_id_cond = "";
		}
	} else {
		$buyer_id_cond = " and a.buyer_name=$data[1]";
	}

	$search_by = $data[2];
	$search_string = "%" . trim($data[3]) . "%";

	if ($search_by == 1)
		$search_field = "a.job_no";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else
		$search_field = "b.po_number";

	$start_date = $data[4];
	$end_date = $data[5];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = "and b.pub_shipment_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_library, 1 => $buyer_arr);
	if ($db_type == 0)
		$year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	else
        $year_field = ""; //defined Later

    $sql = "select a.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond order by b.id, b.pub_shipment_date";
    //echo $sql;	
    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170", "760", "220", 0, $sql, "js_set_value_job", "id,job_no", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "", '', '0,0,0,0,0,0,3', '');
    exit();
}

if ($action == "machine_no_search_popup")
{
	echo load_html_head_contents("Machine Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(str)
		{
			$('#hide_machine').val(str);
			parent.emailwindow.hide();
		}
	</script>
	<input type="hidden" id="hide_machine" name="hide_machine" >
	<?
	$sql = "select id,machine_no from lib_machine_name where category_id=1 and status_active=1 and is_deleted=0";
	echo create_list_view("tbl_machine", "Machine No", "200", "240", "250", 0, $sql, "js_set_value", "id,machine_no", "", 1, "0", $arr, "machine_no", "", "setFilterGrid('tbl_machine',-1);", '0', "", "");
	exit();
}

$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");


$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
$color_type_arr = return_library_array("select id, color_type_id from wo_pre_cost_fabric_cost_dtls", 'id', 'color_type_id');
$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
$machine_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
$feeder = array(1 => "Full Feeder", 2 => "Half Feeder");



//--------------------------------------------------------------------------------------------------------------------


if ($action == "print") 
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode('*', $data);
	$company_id = $data[0];
	$program_id = $data[1];

	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$buyer_details = return_library_array("select id, buyer_name from lib_buyer", "id", "buyer_name");


	if ($db_type == 0) {
		$plan_details_array = return_library_array("select dtls_id, group_concat(po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_id group by dtls_id", "dtls_id", "po_id");
	} else {
		$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where company_id=$company_id group by dtls_id", "dtls_id", "po_id");
	}

	$po_array = array();
	$po_dataArray = sql_select("select id, grouping, file_no, po_number, job_no_mst from wo_po_break_down");
	foreach ($po_dataArray as $row) {
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
		$po_array[$row[csf('id')]]['file'] = $row[csf('file_no')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
	}

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and company_id=$company_id and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['count'] = $count_arr[$row[csf('yarn_count_id')]];
		$product_details_array[$row[csf('id')]]['comp'] = $compos;
		$product_details_array[$row[csf('id')]]['type'] = $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $row[csf('color')];
	}
	?>
	<div style="width:860px">
		<div style="margin-left:20px; width:850px">
			<div style="width:100px;float:left;position:relative;margin-top:10px">
				<? $image_location = return_field_value("image_location", "common_photo_library", "master_tble_id='$company_id' and form_name='company_details' and is_deleted=0"); ?>
				<img src="../../<? echo $image_location; ?>" height='100%' width='100%' />
			</div>
			<div style="width:50px;float:left;position:relative;margin-top:10px"></div>
			<div style="width:710px;float:left;position:relative;">   
				<table width="100%" style="margin-top:10px">

					<tr>
						<td align="center" style="font-size:16px;">
							<?
							echo $company_details[$company_id];
							?>
						</td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px">  
							<?
							$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$company_id");
							foreach ($nameArray as $result) {
								?>
								Plot No: <? echo $result['plot_no']; ?> 
								Level No: <? echo $result['level_no'] ?>
								Road No: <? echo $result['road_no']; ?> 
								Block No: <? echo $result['block_no']; ?> 
								City No: <? echo $result['city']; ?> 
								Zip Code: <? echo $result['zip_code']; ?> 
								Country: <? echo $country_arr[$result['country_id']]; ?><br> 
								Email Address: <? echo $result['email']; ?> 
								Website No: <?
								echo $result['website'];
							}
							?>   
						</td> 
					</tr>
					<tr>
						<td height="10"></td>
					</tr>
					<tr>
						<td width="100%" align="center" style="font-size:14px;"><b><u>Knitting Program</u></b></td>
					</tr>
				</table>
			</div>
		</div>
		<div style="margin-left:10px;float:left; width:850px">
			<?
			$dataArray = sql_select("select id, mst_id, knitting_source, knitting_party, program_date, color_range, stitch_length,spandex_stitch_length, feeder, machine_dia, machine_gg, program_qnty, remarks from ppl_planning_info_entry_dtls where id=$program_id");

			$mst_dataArray = sql_select("select booking_no, buyer_id, fabric_desc, gsm_weight, dia from ppl_planning_info_entry_mst where id=" . $dataArray[0][csf('mst_id')]);
			$booking_no = $mst_dataArray[0][csf('booking_no')];
			$buyer_id = $mst_dataArray[0][csf('buyer_id')];
			$fabric_desc = $mst_dataArray[0][csf('fabric_desc')];
			$gsm_weight = $mst_dataArray[0][csf('gsm_weight')];
			$dia = $mst_dataArray[0][csf('dia')];
			?>
			<table width="100%" style="margin-top:20px" cellspacing="7">
				<tr>
					<td width="140"><b>Program No:</b></td><td width="170"><? echo $dataArray[0][csf('id')]; ?></td>
					<td width="170"><b>Program Date:</b></td><td><? echo change_date_format($dataArray[0][csf('program_date')]); ?></td>
				</tr>
				<tr>
					<td><b>Factory:</b></td>
					<td>
						<?
						if ($dataArray[0][csf('knitting_source')] == 1)
							echo $company_details[$dataArray[0][csf('knitting_party')]];
						else if ($dataArray[0][csf('knitting_source')] == 3)
							echo $supplier_details[$dataArray[0][csf('knitting_party')]];
						?>
					</td>
					<td><b>Fabrication & FGSM:</b></td><td><? echo $fabric_desc . " & " . $gsm_weight; ?></td>
				</tr>
				<tr>
					<td><b>Address:</b></td>
					<td colspan="3">
						<?
						$address = '';
						if ($dataArray[0][csf('knitting_source')] == 1) {
							$addressArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,city from lib_company where id=$company_id");
							foreach ($nameArray as $result) {
								?>
								Plot No: <? echo $result['plot_no']; ?> 
								Level No: <? echo $result['level_no'] ?>
								Road No: <? echo $result['road_no']; ?> 
								Block No: <? echo $result['block_no']; ?> 
								City No: <? echo $result['city']; ?> 
								Country: <?
								echo $country_arr[$result['country_id']];
							}

						} else if ($dataArray[0][csf('knitting_source')] == 3) {
							$address = return_field_value("address_1", "lib_supplier", "id=" . $dataArray[0][csf('knitting_party')]);
							echo $address;
						}
						?>
					</td>
				</tr>
				<tr>
					<td><b>Buyer Name:</b></td>
					<td>
						<?
						echo $buyer_details[$buyer_id];

						$po_id = array_unique(explode(",", $plan_details_array[$dataArray[0][csf('id')]]));
						$po_no = '';
						$job_no = '';
						$ref_cond = '';
						$file_cond = '';

						foreach ($po_id as $val) {
							if ($po_no == '')
								$po_no = $po_array[$val]['no'];
							else
								$po_no .= "," . $po_array[$val]['no'];
							if ($job_no == '')
								$job_no = $po_array[$val]['job_no'];
							if ($ref_cond == "")
								$ref_cond = $po_array[$val]['ref'];
							else
								$ref_cond .= "," . $po_array[$val]['ref'];
							if ($file_cond == "")
								$file_cond = $po_array[$val]['file'];
							else
								$file_cond .= "," . $po_array[$val]['file'];
						}
						?>
					</td>
					<td><b>Order No:</b></td><td><? echo $po_no; ?></td>
				</tr>
				<tr>
					<td><b>Booking No:</b></td><td><b><? echo $booking_no; ?></b></td>
					<td><b>Job No:</b></td><td><b><? echo $job_no; ?></b></td>
				</tr>
				<tr>
					<td><b>Internal Ref:</b></td><td><b><? echo implode(",", array_unique(explode(",", $ref_cond))); ?></b></td>
					<td><b>File No:</b></td><td><b><? echo implode(",", array_unique(explode(",", $file_cond))); ?></b></td>
				</tr>
				<tr>   
					<td><b>Style Ref :</b></td>
					<td><?
					if ($job_no != '') {

						$style_val = return_field_value("style_ref_no", "wo_po_details_master", "job_no='$job_no'", "style_ref_no");
					}

					echo $style_val;
					?></td>
				</tr>
			</table>

			<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<thead>
					<th width="40">SL</th>
					<th width="80">Requisition No</th>
					<th width="80">Lot No</th>
					<th width="220">Yarn Description</th>
					<th width="100">Color</th>
					<th width="110">Brand</th>
					<th width="100">Requisition Qty.</th>
					<th>No of Cone</th>
				</thead>
				<?
				$i = 1;
				$tot_reqsn_qnty = 0;
				$sql = "select requisition_no, prod_id,no_of_cone, yarn_qnty from ppl_yarn_requisition_entry where knit_id='" . $dataArray[0][csf('id')] . "' and status_active=1 and is_deleted=0";
				$nameArray = sql_select($sql);
				foreach ($nameArray as $selectResult) {
					?>
					<tr>
						<td width="40" align="center"><? echo $i; ?></td>
						<td width="80">&nbsp;&nbsp;<? echo $selectResult[csf('requisition_no')]; ?></td>
						<td width="80">&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['lot']; ?></td>
						<td width="220">&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['count'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['comp'] . " " . $product_details_array[$selectResult[csf('prod_id')]]['type']; ?></td>
						<td width="100">&nbsp;&nbsp;<? echo $color_library[$product_details_array[$selectResult[csf('prod_id')]]['color']]; ?></td>
						<td width="110">&nbsp;&nbsp;<? echo $product_details_array[$selectResult[csf('prod_id')]]['brand']; ?></td>
						<td width="100" align="right"><? echo number_format($selectResult[csf('yarn_qnty')], 2); ?>&nbsp;&nbsp;</td>
						<td align="right"><? echo number_format($selectResult[csf('no_of_cone')]); ?></td>	
					</tr>
					<?
					$tot_reqsn_qnty += $selectResult[csf('yarn_qnty')];
					$i++;
				}
				?>
				<tfoot>
					<th colspan="6" align="right"><b>Total</b></th>
					<th align="right"><? echo number_format($tot_reqsn_qnty, 2); ?>&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
			<table width="850" cellpadding="0" cellspacing="0" border="1" rules="all" style="margin-top:20px;" class="rpt_table">
				<tr>
					<td width="100">&nbsp;&nbsp;<b>Colour:</b></td>
					<td width="120">&nbsp;&nbsp;<? echo $color_range[$dataArray[0][csf('color_range')]]; ?></td>
					<td width="100">&nbsp;&nbsp;<b>GGSM OR S/L:</b></td>
					<td width="120">&nbsp;&nbsp;<? echo $dataArray[0][csf('stitch_length')]; ?></td>
					<td width="100">&nbsp;&nbsp;<b>Spandex S/L:</b></td>
					<td width="110">&nbsp;&nbsp;<? echo $dataArray[0][csf('spandex_stitch_length')]; ?></td>

					<td width="100">&nbsp;&nbsp;<b>FGSM:</b></td>
					<td>&nbsp;&nbsp;<? echo $gsm_weight; ?></td>
				</tr>
			</table>
			<table style="margin-top:20px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<thead>
					<th width="100">Finish Dia</th>
					<th width="230">Machine Dia & Gauge</th>
					<th width="80">Feeder</th>
					<th width="110">Program Qnty</th>


					<th>Remarks</th>
				</thead>
				<tr>
					<td width="100">&nbsp;&nbsp;<? echo $dia; ?></td>
					<td width="230">&nbsp;&nbsp;<? echo $dataArray[0][csf('machine_dia')] . "X" . $dataArray[0][csf('machine_gg')]; ?></td>
					<td width="80">&nbsp;&nbsp;<? echo $feeder[$dataArray[0][csf('feeder')]]; ?></td>
					<td width="110" align="right">&nbsp;&nbsp;<? echo number_format($dataArray[0][csf('program_qnty')], 2); ?>&nbsp;&nbsp;</td>
					<td><? echo $dataArray[0][csf('remarks')]; ?></td>	
				</tr>
				<tr height="70" valign="middle">
					<td colspan="5"><b>Advice:</b></td>
				</tr>
			</table>
			<table width="850"> 
				<tr>
					<td width="100%" height="90" colspan="5"></td>
				</tr> 
				<tr>
					<td width="25%" align="center"><strong style="text-decoration:overline">Checked By</strong></td>
					<td width="25%" align="center"><strong style="text-decoration:overline">Store Incharge</strong></td>
					<td width="25%" align="center"><strong style="text-decoration:overline">Knitting Manager</strong></td>
					<td width="25%" align="center"><strong style="text-decoration:overline">Authorised By</strong></td>
				</tr> 
			</table>
		</div>
	</div>
	<?
	exit();
}

if ($action == "requisition_print") 
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$program_ids = $data;

	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	if ($db_type == 0) {
		$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	} else {
		$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	}


	$po_dataArray = sql_select("select id, po_number, job_no_mst from wo_po_break_down");
	foreach ($po_dataArray as $row) {
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
	}

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}


	$knit_id_array = array();
	$prod_id_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no");
	foreach ($reqsn_dataArray as $row) {
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
	}

	$order_no = '';
	$buyer_name = '';
	$knitting_factory = '';
	$job_no = '';
	$booking_no = '';
	$company = '';

	if ($db_type == 0) {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id");
	} else {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id");
	}
	foreach ($dataArray as $row) {
		if ($duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] == "") {
			$duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] = $row[csf('knitting_party')];

			if ($row[csf('knitting_source')] == 1)
				$knitting_factory .= $company_details[$row[csf('knitting_party')]] . ",";
			else if ($row[csf('knitting_source')] == 3)
				$knitting_factory .= $supplier_details[$row[csf('knitting_party')]] . ",";
		}

		if ($buyer_name == "")
			$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
		if ($booking_no == "")
			$booking_no = $row[csf('booking_no')];
		if ($company == "")
			$company = $company_details[$row[csf('company_id')]];

		$po_id = explode(",", $row[csf('po_id')]);

		foreach ($po_id as $val) {
			$order_no .= $po_array[$val]['no'] . ",";
			if ($job_no == "")
				$job_no = $po_array[$val]['job_no'];
		}
	}

	$order_no = array_unique(explode(",", substr($order_no, 0, -1)));
	?>
	<div style="width:1200px; margin-left:5px">
		<table width="100%" style="margin-top:10px">
			<tr>
				<td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
			</tr>
			<tr>
				<td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
			</tr>
		</table>
		<div style="border:1px solid;margin-top:10px; width:950px">
			<table width="100%" cellpadding="2" cellspacing="5">
				<tr>
					<td width="140"><b>Knitting Factory </b></td>
					<td>:</td>
					<td><? echo substr($knitting_factory, 0, -1); ?></td>
				</tr>
				<tr>   
					<td><b>Buyer Name </b></td>
					<td>:</td>
					<td><? echo $buyer_name; ?></td>
				</tr>
				<tr>   

					<td><b>Style </b></td>
					<td>:</td>    
					<td><?
					if ($job_no != '') {
						$style_val = return_field_value("style_ref_no", "wo_po_details_master", "job_no='$job_no'", "style_ref_no");
					}

					echo $style_val;
					?></td>
				</tr>
				<tr>   
					<td><b>Order No </b></td>
					<td>:</td>    
					<td><? echo implode(",", $order_no); ?></td>
				</tr>
				<tr>    
					<td><b>Job No </b></td>
					<td>:</td>
					<td><? echo $job_no; ?></td>
				</tr> 
				<tr>     
					<td><b>Booking No </b></td>
					<td>:</td>
					<td><? echo $booking_no; ?></td>
				</tr>
			</table>
		</div>
		<table width="950" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Requisition No</th>
				<th width="100">Brand</th>
				<th width="100">Lot No</th>
				<th width="200">Yarn Description</th>
				<th width="100">Color</th>
				<th width="100">Requisition Qty.</th>
				<th>No Of Cone</th>
			</thead>
			<?
			$j = 1;
			$tot_reqsn_qty = 0;
			foreach ($rqsn_array as $prod_id => $data) {
				if ($j % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"><? echo $j; ?></td>
					<td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
					<td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
					<td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p></th>
						<td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
						<td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
						<td align="right"><? echo number_format($data['no_of_cone']); ?></td>
					</tr>
					<?
					$tot_reqsn_qty += $data['qnty'];
					$tot_no_of_cone += $data['no_of_cone'];
					$j++;
				}
				?>
				<tfoot>
					<th colspan="6" align="right">Total</th>
					<th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
					<th><? echo number_format($tot_no_of_cone); ?></th>
				</tfoot>
			</table>

			<table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<thead>
					<th width="25">SL</th>
					<th width="60">Program No & Date</th>
					<th width="120">Fabrication</th>
					<th width="50">GSM</th>
					<th width="50">F. Dia</th>
					<th width="60">Dia Type</th>
					<th width="50">S/L</th>
					<th width="50">Spandex S/L</th>
					<th width="50">Feeder</th>
					<th width="60">Color</th>
					<th width="60">Color Range</th>
					<th width="60">Machine No</th> 
					<th width="70">Machine Dia & GG</th>
					<th width="70">Knit Plan Date</th>
					<th width="70">Program Qty.</th>
					<th width="110">Yarn Description</th>
					<th width="50">Lot</th>
					<th width="70">Yarn Qty.(KG)</th>
					<th>Remarks</th>
				</thead>
				<?
            //stitch_length,spandex_stitch_length, feeder, machine_dia, machine_gg, program_qnty, remarks from ppl_planning_info_entry_dtls
				$i = 1;
				$s = 1;
				$tot_program_qnty = 0;
				$tot_yarn_reqsn_qnty = 0;
				$company_id = '';
				$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				$nameArray = sql_select($sql);
				foreach ($nameArray as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$color = '';
					$color_id = explode(",", $row[csf('color_id')]);

					foreach ($color_id as $val) {
						if ($color == '')
							$color = $color_library[$val];
						else
							$color .= "," . $color_library[$val];
					}

					if ($company_id == '')
						$company_id = $row[csf('company_id')];

					$machine_no = '';
					$machine_id = explode(",", $row[csf('machine_id')]);

					foreach ($machine_id as $val) {
						if ($machine_no == '')
							$machine_no = $machine_arr[$val];
						else
							$machine_no .= "," . $machine_arr[$val];
					}

					if ($knit_id_array[$row[csf('program_id')]] != "") {
						$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
						$row_span = count($all_prod_id);
						$z = 0;
						foreach ($all_prod_id as $prod_id) {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<?
								if ($z == 0) {
									?>
									<td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
									<td width="60" rowspan="<? echo $row_span; ?>" align="center"><? echo $row[csf('program_id')] . '<br>' . change_date_format($row[csf('program_date')]); ?></td>
									<td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
									<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td> 
										<td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
										<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]) . " to " . change_date_format($row[csf('end_date')]); ?></td>
										<td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
										<?
										$tot_program_qnty += $row[csf('program_qnty')];
										$i++;
									}
									?>
									<td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
									<td width="50"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
									<td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
									<?
									if ($z == 0) {
										?>
										<td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										<?
									}
									?>
								</tr>
								<?
								$tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
								$z++;
							}
						} else {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="25"><? echo $i; ?></td>
								<td width="60" align="center"><? echo $row[csf('program_id')] . '<br>' . change_date_format($row[csf('program_date')]); ?></td>
								<td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
								<td width="50"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
									<td width="50"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
									<td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>
									<td width="50"><p><? echo $row[csf('stitch_length')]; ?></p></td>
									<td width="50"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
									<td width="50"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
									<td width="60"><p><? echo $color; ?></p></td>
									<td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
									<td width="60"><p><? echo $machine_no; ?></p></td> 
									<td width="70"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
									<td width="70"><? echo change_date_format($row[csf('start_date')]) . " to " . change_date_format($row[csf('end_date')]); ?></td>
									<td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
									<td width="110"><p>&nbsp;</p></td>
									<td width="50"><p>&nbsp;</p></td>
									<td width="70" align="right">&nbsp;</td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_program_qnty += $row[csf('program_qnty')];
								$i++;
							}
						}
						?>
						<tfoot>
							<th colspan="14" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
							<th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
					<br>
					<?
					$sql_strip = "select a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0";

					$result_stripe = sql_select($sql_strip);
					if (count($result_stripe) > 0) {
						?>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="7">Stripe Measurement</th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="60">Prog. no</th>
									<th width="140">Color</th> 
									<th width="130">Stripe Color</th> 
									<th width="70">Measurement</th>               
									<th width="50">UOM</th>
									<th>No Of Feeder</th> 
								</tr>
							</thead>
							<?
							$i = 1;
							$tot_feeder = 0;
							foreach ($result_stripe as $row) {
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								$tot_feeder += $row[csf('no_of_feeder')];
								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="50" align="center"><? echo $row[csf('dtls_id')]; ?></td> 	
									<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td> 
									<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>     
									<td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>               
									<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
									<td align="right" style="padding-right:10px"><? echo $row[csf('no_of_feeder')]; ?>&nbsp;</td>
								</tr>
								<?
								$tot_masurement += $row[csf('measurement')];
								$i++;
							}
							?>
						</tbody>
						<tfoot>
							<th colspan="4">Total</th>
							<th>&nbsp;</th> 
							<th>&nbsp;</th>
							<th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th> 
						</tfoot>
					</table>          
					<?
				}
				echo signature_table(41, $company_id, "1180px");
				?>
			</div>
			<?
			exit();
		}

if ($action == "requisition_print_two") 
{
	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode('**', $data);
	$typeForAttention = $data[1];
	$program_ids = $data[0];
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	if ($db_type == 0) {
		$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	} else {
		$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	}


	$po_dataArray = sql_select("select id, po_number, job_no_mst from wo_po_break_down");
	foreach ($po_dataArray as $row) {
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
	}

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) {
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}


	$knit_id_array = array();
	$prod_id_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no");
	foreach ($reqsn_dataArray as $row) {
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
	}

	$order_no = '';
	$buyer_name = '';
	$knitting_factory = '';
	$job_no = '';
	$booking_no = '';
	$company = '';
	if ($db_type == 0) {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id");
	} else {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, b.buyer_id, b.booking_no, b.company_id");
	}

	$k_source = "";
	$sup = "";
	foreach ($dataArray as $row) {
		if ($duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] == "") {
			$duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] = $row[csf('knitting_party')];

			if ($row[csf('knitting_source')] == 1)
				$knitting_factory .= $company_details[$row[csf('knitting_party')]] . ",";
			else if ($row[csf('knitting_source')] == 3)
				$knitting_factory .= $supplier_details[$row[csf('knitting_party')]] . ",";
		}

		if ($buyer_name == "")
			$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
		if ($booking_no == "")
			$booking_no = $row[csf('booking_no')];
		if ($company == "")
			$company = $company_details[$row[csf('company_id')]];
		if ($company_id == "")
			$company_id = $row[csf('company_id')];

		$po_id = explode(",", $row[csf('po_id')]);
	//echo "<pre>";
	//print_r($po_id); 
		foreach ($po_id as $val) {
			$order_no .= $po_array[$val]['no'] . ",";
			if ($job_no == "")
				$job_no = $po_array[$val]['job_no'];
		}
		$k_source = $row[csf('knitting_source')];
		$sup = $row[csf('knitting_party')];
	}
	//echo $sup;
	$order_no = array_unique(explode(",", substr($order_no, 0, -1)));
	$machine_wise_sql=sql_select("SELECT id,save_data from ppl_planning_info_entry_dtls where id in($program_ids)");
	foreach($machine_wise_sql as $k=>$v)
	{
		$machine=explode(",", $v[csf("save_data")]);
		foreach($machine as $key=>$vals)
		{
			if($vals)
			{
				$vals=explode("_", $vals);
				$machine_array[$vals[0]]["no"]=$vals[1];
				$machine_array[$vals[0]]["qty"] +=$vals[3];

			}


		}
	}
	$lib_machine_sql=sql_select("SELECT id,dia_width, gauge from lib_machine_name where status_active=1 and is_deleted=0");
	foreach($lib_machine_sql as $k=>$val)
	{
		$lib_machine_arr[$val[csf("id")]]["dia_width"]=$val[csf("dia_width")];
		$lib_machine_arr[$val[csf("id")]]["gauge"]=$val[csf("gauge")];
	}

	?>
	<div style="width:1200px; margin-left:5px">
		<table width="100%" style="margin-top:10px">
			<tr>
				<td width="100%" align="center" style="font-size:20px;"><b><? echo $company; ?></b></td>
			</tr>
			<tr>
				<td align="center" style="font-size:14px">  
					<?
					echo show_company($company_id, '', '');
					?>   
				</td> 
			</tr>
			<tr>
				<td width="100%" align="center" style="font-size:20px;"><b><u>Knitting Program</u></b></td>
			</tr>
		</table>
		<div style="margin-top:10px; width:950px">
			<table width="100%" cellpadding="2" cellspacing="5">
				<tr >
					<td width="140"><b style="font-size:18px">Knitting Factory </b></td>
					<td>:</td>
					<td style="font-size:18px"> <b><? echo substr($knitting_factory, 0, -1); ?></b></td>
				</tr>
				<tr>
					<td width="140" style="font-size:18px"><b>Attention </b></td>
					<td>:</td>
					<?
					if ($typeForAttention == 1) {
						echo "<td style=\"font-size:18px; font-weight:bold;\"> Knitting Manager </td>";
					} else {
						?>
						<td style="font-size:18px; font-weight:bold;"><b><?
						if ($k_source == 3) {
							$ComArray = sql_select("select id,contact_person from lib_supplier where id=$sup");
							foreach ($ComArray as $row) {
								echo $row[csf('contact_person')];
							}
						} else {

							echo "";
						}
						?></b></td>
						<? } ?>
					</tr>
					<tr>   
						<td><b>Buyer Name </b></td>
						<td>:</td>
						<td><? echo $buyer_name; ?></td>
					</tr>
					<tr>   
						<td><b>Style </b></td>
						<td>:</td>    
						<td><?
						if ($job_no != '') {
							$style_val = return_field_value("style_ref_no", "wo_po_details_master", "job_no='$job_no'", "style_ref_no");
						}

						echo $style_val;
						?></td>
					</tr>
					<tr>   
						<td><b>Order No </b></td>
						<td>:</td>    
						<td><? echo implode(",", $order_no); ?></td>
					</tr>
					<tr>    
						<td><b>Job No </b></td>
						<td>:</td>
						<td><? echo $job_no; ?></td>
					</tr> 
					<tr>     
						<td><b>Booking No </b></td>
						<td>:</td>
						<td><? echo $booking_no; ?></td>
					</tr>
				</table>
			</div>
			<?
			$distribute_qnty_variable = return_field_value("distribute_qnty", "variable_settings_production", "company_name=$company_id and variable_list=5 and status_active=1 and is_deleted=0", "distribute_qnty");
			?>
			<table width="950" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
				<thead>
					<th width="30">SL</th>
					<th width="100">Requisition No</th>
					<th width="100">Brand</th>
					<th width="100">Lot No</th>
					<th width="200">Yarn Description</th>
					<th width="100">Color</th>
					<? if($distribute_qnty_variable != 2){?>
					<th width="80">Distribution Qnty</th>
					<? } ?>
					<th width="100">Requisition Qty.</th>
					<? if($distribute_qnty_variable != 2){?>
					<th width="80">Returnable Qnty</th>
					<? } ?>
					<th>No Of Cone</th>
				</thead>
				<?
				$j = 1;
				$tot_reqsn_qty = 0;
				foreach ($rqsn_array as $prod_id => $data) {
					if ($j % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td width="30"><? echo $j; ?></td>
						<td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
						<td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
						<td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p></td>
						<td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
						<? 
						if($distribute_qnty_variable != 2){
							$existing_dist = return_field_value("sum(distribution_qnty) as exis_distribution_qnty","ppl_yarn_req_distribution","requisition_no in (".substr($data['reqsn'], 0, -1).") and prod_id=".$prod_id."",'exis_distribution_qnty');
							?>
							<td align="right"><? echo number_format($existing_dist, 2); ?></td>
							<? 
						}
						?>
						<td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
						<? 
						if($distribute_qnty_variable != 2){
							?>
							<td align="right"><? echo $returnable = ((substr($data['reqsn'], 0, -1)-$existing_dist) > 0)?number_format($data['qnty']-$existing_dist, 2):""; ?></td>
							<? 
						}
						?>
						<td align="right"><? echo number_format($data['no_of_cone']); ?></td>
					</tr>
					<?
					$tot_dist_qnty += $existing_dist;
					$tot_reqsn_qty += $data['qnty'];
					$tot_returnable_qnty += $returnable;
					$tot_no_of_cone += $data['no_of_cone'];
					$j++;
				}
				?>
				<tfoot>
					<th colspan="6" align="right">Total</th>
					<th align="right"><? echo number_format($tot_dist_qnty, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
					<th align="right"><? echo number_format($tot_returnable_qnty, 2, '.', ''); ?></th>
					<th><? echo number_format($tot_no_of_cone); ?></th>
				</tfoot>
			</table>

			<table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table" align="center">
				<thead align="center">
					<th width="25">SL</th>
					<th width="50">Program No & Date</th>
					<th width="120">Fabrication</th>
					<th width="50">GSM</th>
					<th width="40">F. Dia</th>
					<th width="60">Dia Type</th>

					<th width="45">Floor</th> 

					<th width="45">M/c. No</th> 
					<th width="50">M/c. Dia & GG</th>
					<th width="100">Color</th>
					<th width="60">Color Range</th>
					<th width="50">S/L</th>
					<th width="50">Spandex S/L</th>
					<th width="50">Feeder</th>
					<th width="70">Knit Start</th>
					<th width="70">Knit End</th>
					<th width="70">Program Qty.</th>
					<th width="110">Yarn Description</th>
					<th width="50">Lot</th>
					<th width="70">Yarn Qty.(KG)</th>
					<th>Remarks</th>

				</thead>
				<?
	//stitch_length,spandex_stitch_length, feeder, machine_dia, machine_gg, program_qnty, remarks from ppl_planning_info_entry_dtls
				$i = 1;
				$s = 1;
				$tot_program_qnty = 0;
				$tot_yarn_reqsn_qnty = 0;
				$company_id = '';
				$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";



				$nameArray = sql_select($sql);

				$advice = "";
				foreach ($nameArray as $row) {
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$color = '';
					$color_id = explode(",", $row[csf('color_id')]);

					foreach ($color_id as $val) {
						if ($color == '')
							$color = $color_library[$val];
						else
							$color .= "," . $color_library[$val];
					}

					if ($company_id == '')
						$company_id = $row[csf('company_id')];

					$machine_no = '';
					$machine_id = explode(",", $row[csf('machine_id')]);

					foreach ($machine_id as $val) {
						if ($machine_no == '')
							$machine_no = $machine_arr[$val];
						else
							$machine_no .= "," . $machine_arr[$val];
					}
					if ($machine_id[0] != "") {
						$sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
					}

					if ($knit_id_array[$row[csf('program_id')]] != "") {
						$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
						$row_span = count($all_prod_id);
						$z = 0;
						foreach ($all_prod_id as $prod_id) {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<?
								if ($z == 0) {
									?>
									<td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
									<td width="60" rowspan="<? echo $row_span; ?>" align="center"  style="font-size:14px;"><b><? echo $row[csf('program_id')]; ?></b><br><p style="font-size:12px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
									<td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
									<td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
										<td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

										<td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

										<td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td> 
										<td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>


										<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
										<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
										<td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
										<?
										$tot_program_qnty += $row[csf('program_qnty')];
										$i++;
									}
									?>
									<td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
									<td width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
									<td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
									<?
									if ($z == 0) {
										?>
										<td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										<?
									}
									?>
								</tr>
								<?
								$tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
								$z++;
							}
						} else {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="25"><? echo $i; ?></td>
								<td width="60" align="center" style="font-size:14px;"><b><? echo $row[csf('program_id')]; ?></b><br><p style="font-size:12px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
								<td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
									<td width="50" align="center"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
									<td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

									<td width="50" align="center"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

									<td width="50" align="center"><p><? echo $machine_no; ?></p></td>
									<td width="50"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
									<td width="50"><p><? echo $color; ?></p></td>
									<td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
									<td width="60"><p><? echo $row_span; ?><p><? echo $row[csf('stitch_length')]; ?></p></td>
									<td width="60"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td> 
									<td width="70"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
									<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
									<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
									<td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
									<td width="110"><p>&nbsp;</p></td>
									<td width="50"><p>&nbsp;</p></td>
									<td width="70" align="right">&nbsp;</td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_program_qnty += $row[csf('program_qnty')];
								$i++;
							}
							$advice = $row[csf('advice')];
							$advice = str_replace(array(";","\n"), "<br/>", $advice);
						}
						?>
						<tfoot>
							<th colspan="16" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>

							<th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
							<th>&nbsp;</th>
						</tfoot>

					</table>
					<br>
					<?
					$sql_collarCuff=sql_select("select id, body_part_id, grey_size, finish_size, qty_pcs from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id");
					if(count($sql_collarCuff)>0)
					{
						?>
						<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
							<thead>
								<tr>
									<th width="50">SL</th>
									<th width="200">Body Part</th>
									<th width="200">Grey Size</th>
									<th width="200">Finish Size</th>
									<th>Quantity Pcs</th>
								</tr>
							</thead>
							<tbody>
								<?
								$i=1; $total_qty_pcs=0;
								foreach($sql_collarCuff as $row)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr>
										<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
										<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
										<td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
										<td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
										<td align="right"><p><? echo number_format($row[csf('qty_pcs')],0); $total_qty_pcs+=$row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>	
									</tr>
									<?
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th align="right">Total</th>
									<th align="right"><? echo number_format($total_qty_pcs,0); ?>&nbsp;</th>
								</tr>
							</tfoot>
						</table>
						<?
					}
					?>
					<br>

					<?
					$sql_strip_data = "select a.pre_cost_fabric_cost_dtls_id,a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0  group by a.pre_cost_fabric_cost_dtls_id,a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder ";
						$result_stripe_data = sql_select($sql_strip_data);
						$pre_cost_fabric_cost_dtls_id="";$programIDS_arr=array();
						foreach ($result_stripe_data as $row) {
							$pre_cost_fabric_cost_dtls_id.=$row[csf('pre_cost_fabric_cost_dtls_id')].",";
							$programIDS_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]=$row[csf('dtls_id')];
						}
						$pre_cost_fabric_cost_dtls_id=chop($pre_cost_fabric_cost_dtls_id,",");

						$feeder_data_sql= sql_select("select id, knitting_source, knitting_party, subcontract_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks,attention, co_efficient, save_data, no_fo_feeder_data, location_id, advice, collar_cuff_data, grey_dia from ppl_planning_info_entry_dtls where  id in($program_ids)");
						foreach ($feeder_data_sql as $row ) {
							$no_of_feeder_data =$row[csf('no_fo_feeder_data')];
						}

						$noOfFeeder_array = array();
						$no_of_feeder_data = explode(",", $no_of_feeder_data);
						$pre_cost_id = explode(",", $pre_cost_fabric_cost_dtls_id);
						$pre_cost_id = implode(",", array_unique($pre_cost_id));

						$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

						for ($i = 0; $i < count($no_of_feeder_data); $i++) {
							$color_wise_data = explode("_", $no_of_feeder_data[$i]);
							$pre_cost_fabric_cost_dtls_id = $color_wise_data[0];
							$color_id = $color_wise_data[1];
							$stripe_color = $color_wise_data[2];
							$no_of_feeder = $color_wise_data[3];

							$noOfFeeder_array[$pre_cost_fabric_cost_dtls_id][$color_id][$stripe_color][$i]=$no_of_feeder;
							//$noOfFeeder_array[$i] = $no_of_feeder;
						}
					/* select pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id in(966) and status_active=1 and is_deleted=0 order by color_number_id,id*/

					$sql_strip= "select pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id in($pre_cost_fabric_cost_dtls_id) and status_active=1 and is_deleted=0 order by color_number_id,id";



					$result_stripe = sql_select($sql_strip);
					if (count($result_stripe) > 0) {
						?>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="7">Stripe Measurement</th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="60">Prog. no</th>
									<th width="140">Color</th> 
									<th width="130">Stripe Color</th> 
									<th width="70">Measurement</th>               
									<th width="50">UOM</th>
									<th>No Of Feeder</th> 
								</tr>
							</thead>
							<?
							$tot_feeder = 0;
							$i = 1;$kl = 0;
							$tot_feeder = 0;
							foreach ($result_stripe as $row) {
								$no_of_feeder=$noOfFeeder_array[$row[csf('pre_cost_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]][$kl];
								$tot_feeder += $no_of_feeder;
								$kl++;
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								//$tot_feeder += $row[csf('no_of_feeder')];
								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="50" align="center"><? echo $programIDS_arr[$row[csf('pre_cost_id')]];//$row[csf('dtls_id')]; ?></td> 	
									<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td> 
									<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>     
									<td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>               
									<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
									<td align="right" style="padding-right:10px"><? echo $no_of_feeder;//$row[csf('no_of_feeder')]; ?>&nbsp;</td>
								</tr>
								<?
								$tot_masurement += $row[csf('measurement')];
								$i++;
							}
							?>
						</tbody>
						<tfoot>
							<th colspan="4">Total</th>
							<th>&nbsp;</th> 
							<th>&nbsp;</th>
							<th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th> 
						</tfoot>

					</table>  

					<?
				}
				?>
				<table  border="1" rules="all"  class="rpt_table">
					<tr>
						<td style="font-size:24px; font-weight:bold; width:20px;">ADVICE: </td>
						<td style="font-size:20px; width:100%;"><? echo $advice; ?></td>
					</tr> 
				</table> 
				<div>

					<div style="float:left; border:1px solid #000; margin-top:60px;">
						<table border="1" rules="all" class="rpt_table" width="400" height="200" >
							<thead>
								<th colspan="2" style="font-size:20px; font-weight:bold;">Please Strictly Avoid The Following Faults….</th>
								<thead>
									<tbody >
										<tr >
											<td style="width:190px; font-size:14px;"><b> 1.</b> Patta</td>
											<td style="font-size:14px;"><b> 8.</b> Sinker mark</td>
										</tr>
										<tr>
											<td style="font-size:14px;"><b> 2.</b> Loop	</td>
											<td style="font-size:14px;"><b> 9.</b> Needle mark</td>
										</tr>
										<tr>
											<td style="font-size:14px;"><b> 3.</b> Hole	</td>
											<td style="font-size:14px;"><b> 10.</b> Oil mark</td>
										</tr>
										<tr>
											<td><b> 4.</b> Star marks</td>
											<td><b> 11.</b> Dia mark/Crease Mark</td>
										</tr>
										<tr>
											<td style="font-size:14px;"><b> 5.</b> Barre</td>
											<td style="font-size:14px;"><b> 12.</b> Wheel Free</td>
										</tr>
										<tr>
											<td style="font-size:14px;"><b> 6.</b> Drop Stitch</td>
											<td style="font-size:14px;"><b> 13.</b> Slub</td>
										</tr>
										<tr>
											<td style="font-size:14px;"><b> 7.</b> Lot mixing</td>
											<td style="font-size:14px;"><b> 14.</b> Other contamination</td>
										</tr>
									</tbody>
								</table>
							</div>

							<div style="float:left; border:1px solid #000; margin-top:60px;margin-left: 10px;">
								<table border="1" rules="all" class="rpt_table" width="300" height="150">
									<thead>
										<th colspan="3" style="font-size:18px; font-weight:bold;">Machine Wise Plan Distribution Qty</th>
										<thead>
											<tr>
												<th width="60"> <p>MC No</p></th>
												<th width="90"><p> M/C. Dia && GG</p></th>
												<th width="90"> <p>Prog. Qty</p></th>

											</tr>
											<?
											$total_qty=0;
											foreach($machine_array as $k=>$v)
											{
												?>
												<tr>
													<td width="60" style="font-size:14px;" align="center"> <? echo $v["no"]; ?></td>
													<td width="90" style="font-size:14px;" align="center"> <? echo  $lib_machine_arr[trim($k,",")]["dia_width"]."X".$lib_machine_arr[trim($k,",")]["gauge"];?></td>
													<td width="90" style="font-size:14px;" align="right"> <? echo number_format($v["qty"],2); ?></td>

												</tr>

												<?
												$total_qty+=$v["qty"];
											}

											?>
											<tr>
												<td colspan="2" align="right"> <b>Total</b></td>
												<td align="right"> <b> <? echo number_format($total_qty,2); ?></b></td>

											</tr>
										</thead>



									</table>
								</div>


								<div style="float:left; border:1px solid #000; margin-top:60px;margin-left: 10px;">
									<table border="1" rules="all" class="rpt_table" width="400" height="150">
										<thead>
											<th colspan="2" style="font-size:18px; font-weight:bold;">Please Mark The Role The Each Role as Follows</th>
											<thead>
												<tr>
													<td width="200" style="font-size:14px;"><b> 1.</b> Manufacturing Factory Name</td>
													<td style="font-size:14px;"><b> 6.</b> Fabrics Type</td>
												</tr>
												<tr>
													<td style="font-size:14px;"><b> 2.</b> Company Name.</td>
													<td style="font-size:14px;"><b> 7.</b> Finished Dia	</td>
												</tr>
												<tr>
													<td style="font-size:14px;"><b> 3.</b> Buyer, Style,Order no.</td>
													<td style="font-size:14px;"><b> 8.</b> Finished Gsm & Color</td>
												</tr>
												<tr>
													<td style="font-size:14px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
													<td style="font-size:14px;"><b> 9.</b> Yarn Composition</td>
												</tr>
												<tr>
													<td style="font-size:14px;"><b> 5.</b> M/C No., Dia, Stitch Length</td>
													<td style="font-size:14px;"><b> 10.</b> Knit Program No	</td>
												</tr>
											</thead>

										</table>
									</div>
								</div>
								<?
								echo signature_table(41, $company_id, "1180px");
								?>
							</div>
							<?
							exit();
						
}

if ($action == "requisition_print_three") 
{

	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode('**', $data);
	$typeForAttention = $data[1];
	$program_ids = $data[0];

	$Sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$supplier_address = return_library_array("select id, ADDRESS_1 from lib_supplier", "id", "ADDRESS_1");

	if ($db_type == 0) {
		$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	} else {
		$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	}


	$po_dataArray = sql_select("select id, grouping, po_number, job_no_mst from wo_po_break_down");
	foreach ($po_dataArray as $row) {
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
	}

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) 
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}


	$knit_id_array = array();
	$prod_id_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no");
	foreach ($reqsn_dataArray as $row) {
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
	}
	$location_array=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$order_no = '';
	$buyer_name = '';
	$knitting_factory = '';
	$job_no = '';
	$booking_no = '';
	$company = '';

	if ($db_type == 0) {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, a.subcontract_party,a.location_id, b.buyer_id, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, a.subcontract_party,a.location_id, b.buyer_id, b.booking_no, b.company_id");
	} else {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, a.subcontract_party,a.location_id, b.buyer_id, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, a.subcontract_party, a.location_id,b.buyer_id, b.booking_no, b.company_id "); 

	}

	$k_source = "";
	$sup = "";
	$sub_con= "";
	foreach ($dataArray as $row) 
	{
		if ($duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] == "") {

			$duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] = $row[csf('knitting_party')];

			if ($row[csf('knitting_source')] == 1)
				$knitting_factory .= $company_details[$row[csf('knitting_party')]] . ",";
			else if ($row[csf('knitting_source')] == 3)
				$knitting_factory .= $supplier_details[$row[csf('knitting_party')]] . ",";
				$address=$supplier_address[$row[csf('knitting_party')]] ;  
		}

		if ($buyer_name == "")
			$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
		if ($booking_no == "")
			$booking_no = $row[csf('booking_no')];
		if ($company == "")
			$company = $company_details[$row[csf('company_id')]];
		if ($company_id == "")
			$company_id = $row[csf('company_id')];

		$location_id = $row[csf('location_id')];
		if($row[csf('is_short')] == 2) $booking_type_cond = "(Main)"; else $booking_type_cond ="(Short)" ;


		$po_id = explode(",", $row[csf('po_id')]);

		foreach ($po_id as $val) {
			$order_no .= $po_array[$val]['no'] . ",";
			if ($job_no == "")
				$job_no = $po_array[$val]['job_no'];
		}
		$k_source = $row[csf('knitting_source')];
		$sup = $row[csf('knitting_party')];

		if($row[csf('subcontract_party')] != "" || $row[csf('subcontract_party')] !=0)
		{
			if($sub_con=="") 
			{
				$sub_con .= $Sub_subcontract[$row[csf('subcontract_party')]]; 
			}
			else
			{
				$sub_con .= ", ".$Sub_subcontract[$row[csf('subcontract_party')]]; 
			}
		}
	}
	

	$order_no = array_unique(explode(",", substr($order_no, 0, -1)));

	$machine_wise_sql=sql_select("SELECT id,save_data from ppl_planning_info_entry_dtls where id in($program_ids)");
	foreach($machine_wise_sql as $k=>$v)
	{
		$machine=explode("__", $v[csf("save_data")]);
		foreach($machine as $key=>$vals)
		{
			if($vals)
			{
				$vals=explode("_", $vals);
				$machine_array[$vals[0]]["no"]=$vals[1];
				$machine_array[$vals[0]]["qty"] +=$vals[3];

			}


		}
	}
	$lib_machine_sql=sql_select("SELECT id,dia_width, gauge from lib_machine_name where status_active=1 and is_deleted=0");
	foreach($lib_machine_sql as $k=>$val)
	{
		$lib_machine_arr[$val[csf("id")]]["dia_width"]=$val[csf("dia_width")];
		$lib_machine_arr[$val[csf("id")]]["gauge"]=$val[csf("gauge")];
	}

	?>
	<div style="width:1200px; margin-left:5px">
		<table width="100%" style="margin-top:10px">
			<tr>
				<td width="100%" align="center" style="font-size:22px;"><b><? echo $company; ?></b></td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">  
					<?
					echo show_company($company_id, '', '');
					?>   
				</td> 
			</tr>
			<tr>
				<td width="100%" align="center" style="font-size:22px;"><b><u>Knitting Program</u></b></td>
			</tr>
		</table>
		<div style="margin-top:10px; width:950px">
			<table width="100%" cellpadding="2" cellspacing="5">
				<tr >
					<td width="130"><b style="font-size:20px">Knitting Factory </b></td>
					<td>:</td>
					<td style="font-size:20px"> <b><? echo substr($knitting_factory, 0, -1); ?>&nbsp; <span><? echo "($location_array[$location_id])";?></span></b></td>
				</tr>
                <? if($k_source==3){?>
                <tr>
                	<td><b>Address</b></td>
                    <td>:</td>
                    <td><?= $address;?></td>
                </tr>
                <? } ?>
				<tr>
					<td style="font-size:20px"><b>Attention </b></td>
					<td>:</td>
					<?
					if ($typeForAttention == 1) {
						echo "<td style=\"font-size:18px; font-weight:bold;\"> Knitting Manager </td>";
					} else {
						?>
						<td style="font-size:20px; font-weight:bold;"><b><?
						if ($k_source == 3) {
							$ComArray = sql_select("select id,contact_person from lib_supplier where id=$sup");
							foreach ($ComArray as $row) {
								echo $row[csf('contact_person')];
							}
						} else {

							echo "";
						}
						?></b></td>
						<? } ?>
					</tr>
					<tr>
						<td style="font-size:20px"><b>Sub-contract </b></td>
						<td>:</td>
						<td style="font-size:18px; font-weight:bold;"><b><? echo $sub_con; ?></b></td>
					</tr>
					<tr>   
						<td><b>Buyer Name </b></td>
						<td>:</td>
						<td><? echo $buyer_name; ?></td>
					</tr>
					<tr>   
						<td><b>Style </b></td>
						<td>:</td>    
						<td><? 
						if ($job_no != '') {
							$style_val = return_field_value("style_ref_no", "wo_po_details_master", "job_no='$job_no' and status_active=1 and is_deleted=0", "style_ref_no");
						}

						echo $style_val;
						?></td>
					</tr>
					<tr>   
						<td><b>Order No </b></td>
						<td>:</td>    
						<td><? echo implode(',', $order_no); ?></td>
					</tr>
					<tr>   
						<td><b>Internal Ref. No </b></td>
						<td>:</td>    
						<td>   
							<?
							if($db_type==0)  
							{
								$sql_ref = sql_select("select group_concat(grouping) as grouping  from wo_po_break_down where job_no_mst='$job_no' and status_active=1 and is_deleted=0");
							}
							else
							{
								$sql_ref = sql_select("select listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping  from wo_po_break_down where job_no_mst='$job_no' and status_active=1 and is_deleted=0");
							}


							echo implode(",", array_unique(explode(",", $sql_ref[0][csf("grouping")])));

							?>
						</td>
					</tr>

					<tr>    
						<td><b>Job No </b></td>
						<td>:</td>
						<td><? echo $job_no; ?></td>
					</tr> 
					<tr>     
						<td><b>Booking No </b></td>
						<td>:</td>
						<td><b><?
						$is_short_book=return_field_value("is_short","wo_booking_mst","booking_no='$booking_no'","is_short");
						$book_sql=sql_select("select booking_type,is_short from wo_booking_mst where status_active=1 and booking_no='$booking_no'");
						$is_short_book=$book_sql[0][csf("is_short")];
						$booking_type=$book_sql[0][csf("booking_type")];
						if($booking_type==4)
						{
							$is_short_type="Sample";
						}
						else
						{
							if($is_short_book==1) $is_short_type="Short"; else $is_short_type="Main"; 
						}

						echo $booking_no.' ('.$is_short_type.")"; 
						?>

					</b></td>
				</tr>
			</table>
		</div>
        <?
		$distribute_qnty_variable = return_field_value("distribute_qnty", "variable_settings_production", "company_name=$company_id and variable_list=5 and status_active=1 and is_deleted=0", "distribute_qnty");
		
		if($distribute_qnty_variable == 1){
			$tblWidth = "1150";
		}else{
			$tblWidth = "950";
		}
		?>
		<table width="<? echo $tblWidth;?>" style="margin-top:10px" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Requisition No</th>
				<th width="100">Brand</th>
				<th width="100">Lot No</th>
				<th width="200">Yarn Description</th>
				<th width="100">Color</th>
               
                <? if($distribute_qnty_variable == 1){?>
					<th width="100">Distribution Qnty</th>
				<? } ?>
				
                <th width="100">Requisition Qty.</th>
                
				<? if($distribute_qnty_variable == 1){?>
					<th width="100">Returnable Qnty</th>
				<? } ?>
				<th>No Of Cone</th>
			</thead>
			<?
			$j = 1;
			$tot_reqsn_qty = 0;
			foreach ($rqsn_array as $prod_id => $data) 
			{
				if ($j % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"><? echo $j; ?></td>
					<td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
					<td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
					<td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p></th>
                    <td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
                    <?
					if($distribute_qnty_variable == 1){
						$existing_dist = return_field_value("sum(distribution_qnty) as exis_distribution_qnty","ppl_yarn_req_distribution","requisition_no in(".chop($data['reqsn'],",").") and prod_id=".$prod_id." and status_active=1 and is_deleted=0",'exis_distribution_qnty');
						?>
						<td align="right" title="<? echo $data['reqsn']; ?>" width="100"><? echo number_format($existing_dist, 2); ?></td>
						<?
					}
					?>
                    <td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
                    <?
					if($distribute_qnty_variable == 1){
						?>
						<td align="right" width="100"><? echo $returnable = (($data['qnty']-$existing_dist) > 0)?number_format($data['qnty']-$existing_dist, 2):"0.00"; ?></td>
						<?
					}
					?>
                    <td align="right"><? echo number_format($data['no_of_cone']); ?></td>
					</tr>
					<?
					$tot_reqsn_qty += $data['qnty'];
					$tot_no_of_cone += $data['no_of_cone'];
					
					$tot_dist_qnty += $existing_dist;
					$tot_returnable_qnty += $returnable;
					
					$j++;
				}
				?>
				<tfoot>
					<th colspan="6" align="right">Total</th>
                    <? if($distribute_qnty_variable == 1){ ?>
                        <th align="right"><? echo number_format($tot_dist_qnty, 2); ?></th>
                    <? } ?>
					<th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
                    <? if($distribute_qnty_variable == 1){ ?>
                        <th align="right"><? echo number_format($tot_returnable_qnty, 2); ?></th>
                    <? } ?>
					<th><? echo number_format($tot_no_of_cone); ?></th>
				</tfoot>
		</table>

		<table style="margin-top:10px;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table" align="center">
				<thead align="center">
					<th width="25">SL </th>
					<th width="50">Prog/Req./Date</th>
					<th width="120">Fabrication</th>
					<th width="50">GSM</th>
					<th width="40">F. Dia</th>
					<th width="60">Dia Type</th>
					<th width="45">Floor</th> 
					<th width="45">M/c. No</th> 
					<th width="50">M/c. Dia & GG</th>
					<th width="100">Color</th>
					<th width="60">Color Range</th>
					<th width="50">S/L</th>
					<th width="50">Spandex S/L</th>
					<th width="50">Feeder</th>
					<th width="100">Count Feeding</th>
					<th width="70">Knit Start</th>
					<th width="70">Knit End</th>
					<th width="70">Program Qty.</th>
					<th width="110">Yarn Description</th>
					<th width="50">Lot</th>
					<th width="70">Yarn Qty.(KG)</th>
					<th>Remarks</th>

				</thead>
				<?
				$i = 1;
				$s = 1;
				$tot_program_qnty = 0;
				$tot_yarn_reqsn_qnty = 0;
				$company_id = '';

				$feedingResult =  sql_select("SELECT dtls_id, seq_no, count_id, feeding_id FROM ppl_planning_count_feed_dtls WHERE dtls_id in($program_ids) and status_active=1 and is_deleted=0");

				$sql_reqsn = sql_select("select knit_id, requisition_no from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, requisition_no");
				foreach ($sql_reqsn as $row) {
					$requisition_array[$row[csf('knit_id')]] = $row[csf('requisition_no')];
				}


				$feedingDataArr = array();
				foreach ($feedingResult as $row) {
					$feedingSequence[$row[csf('seq_no')]] =  $row[csf('seq_no')];
					$feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['count_id'] = $row[csf('count_id')];
					$feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['feeding_id'] = $row[csf('feeding_id')];  
				}

				$yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count","id","yarn_count");

				$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

				$nameArray = sql_select($sql);
				$advice = "";
				foreach ($nameArray as $row) 
				{

					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					$color = '';
					$color_id = explode(",", $row[csf('color_id')]);

					foreach ($color_id as $val) {
						if ($color == '')
							$color = $color_library[$val];
						else
							$color .= "," . $color_library[$val];
					}

					if ($company_id == '')
						$company_id = $row[csf('company_id')];

					$machine_no = '';
					$machine_id = explode(",", $row[csf('machine_id')]);

					foreach ($machine_id as $val) {
						if ($machine_no == '')
							$machine_no = $machine_arr[$val];
						else
							$machine_no .= "," . $machine_arr[$val];
					}
					if ($machine_id[0] != "") {
						$sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
					}

					$count_feeding = "";
					foreach($feedingDataArr[$row[csf('program_id')]] as $feedingSequence=>$feedingData)
					{
						if($count_feeding =="")
						{	
							$count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];												
						} 
						else 
						{
							$count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
						}
					}

					if ($knit_id_array[$row[csf('program_id')]] != "") 
					{
						$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
						$row_span = count($all_prod_id);
						$z = 0;
						foreach ($all_prod_id as $prod_id) 
						{

							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<?
								if ($z == 0) {

									?>
									<td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
									<td width="60" rowspan="<? echo $row_span; ?>" align="center"  style="font-size:18px;"><b><? echo $row[csf('program_id')]; ?></b><br><b><? echo $requisition_array[$row[csf('program_id')]]; ?></b><br><p style="font-size:14px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
									<td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
									<td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
										<td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

										<td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

										<td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td> 
										<td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>															
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding?> </p></td>
										<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
										<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
										<td width="70" align="right" rowspan="<? echo $row_span; ?>"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
										<?
										$tot_program_qnty += $row[csf('program_qnty')];
										$i++;
									}
									?>
									<td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
									<td width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
									<td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
									<?
									if ($z == 0) {
										?>
										<td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										<?
									}
									?>
								</tr>
								<?
								$tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
								$z++;
							}
						} else {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="25"><? echo $i; ?></td>
								<td width="60" align="center" style="font-size:18px;" ><b><? echo $row[csf('program_id')]; ?></b><br><b><? echo $requisition_array[$row[csf('program_id')]]; ?></b><br><p style="font-size:14px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
								<td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
									<td width="50" align="center"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
									<td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

									<td width="50" align="center"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

									<td width="50" align="center"><p><? echo $machine_no; ?></p></td>
									<td width="50"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
									<td width="50"><p><? echo $color; ?></p></td>
									<td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
									<td width="60"><p><? echo $row_span; ?><p><? echo $row[csf('stitch_length')]; ?></p></td>
									<td width="60"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td> 
									<td width="70"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
									<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding?> </p></td>
									<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
									<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
									<td width="70" align="right"><? echo number_format($row[csf('program_qnty')], 2, '.', ''); ?>&nbsp;</td>
									<td width="110"><p>&nbsp;</p></td>
									<td width="50"><p>&nbsp;</p></td>
									<td width="70" align="right">&nbsp;</td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_program_qnty += $row[csf('program_qnty')];
								$i++;
							}
							$advice = $row[csf('advice')];
							$advice = str_replace(array(";","\n"), "<br/>", $advice);
						}
						?>
						<tfoot>
							<th colspan="17" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>

							<th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
							<th>&nbsp;</th>
						</tfoot>

					</table>
					<br>
					<?
					$sql_collarCuff=sql_select("select id, body_part_id, grey_size, finish_size, qty_pcs from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id");
					if(count($sql_collarCuff)>0)
					{
						?>
						<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
							<thead>
								<tr>
									<th width="50">SL</th>
									<th width="200">Body Part</th>
									<th width="200">Grey Size</th>
									<th width="200">Finish Size</th>
									<th>Quantity Pcs</th>
								</tr>
							</thead>
							<tbody>
								<?
								$i=1; $total_qty_pcs=0;
								foreach($sql_collarCuff as $row)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr>
										<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
										<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
										<td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
										<td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
										<td align="right"><p><? echo number_format($row[csf('qty_pcs')],0); $total_qty_pcs+=$row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>   
									</tr>
									<?
									$i++;
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th></th>
									<th></th>
									<th></th>
									<th align="right">Total</th>
									<th align="right"><? echo number_format($total_qty_pcs,0); ?>&nbsp;</th>
								</tr>
							</tfoot>
						</table>
						<?
					}
					?>
					<br>

					<?
					$sql_strip_data = "select a.pre_cost_fabric_cost_dtls_id,a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0  group by a.pre_cost_fabric_cost_dtls_id,a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder ";
						$result_stripe_data = sql_select($sql_strip_data);
						$pre_cost_fabric_cost_dtls_id="";$programIDS_arr=array();
						foreach ($result_stripe_data as $row) {
							$pre_cost_fabric_cost_dtls_id.=$row[csf('pre_cost_fabric_cost_dtls_id')].",";
							$programIDS_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]=$row[csf('dtls_id')];
						}
						$pre_cost_fabric_cost_dtls_id=chop($pre_cost_fabric_cost_dtls_id,",");

						$feeder_data_sql= sql_select("select id, knitting_source, knitting_party, subcontract_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks,attention, co_efficient, save_data, no_fo_feeder_data, location_id, advice, collar_cuff_data, grey_dia from ppl_planning_info_entry_dtls where  id in($program_ids)");
						foreach ($feeder_data_sql as $row ) {
							$no_of_feeder_data =$row[csf('no_fo_feeder_data')];
						}

						$noOfFeeder_array = array();
						$no_of_feeder_data = explode(",", $no_of_feeder_data);
						$pre_cost_id = explode(",", $pre_cost_fabric_cost_dtls_id);
						$pre_cost_id = implode(",", array_unique($pre_cost_id));

						$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

						for ($i = 0; $i < count($no_of_feeder_data); $i++) {
							$color_wise_data = explode("_", $no_of_feeder_data[$i]);
							$pre_cost_fabric_cost_dtls_id = $color_wise_data[0];
							$color_id = $color_wise_data[1];
							$stripe_color = $color_wise_data[2];
							$no_of_feeder = $color_wise_data[3];

							$noOfFeeder_array[$pre_cost_fabric_cost_dtls_id][$color_id][$stripe_color][$i]=$no_of_feeder;
							//$noOfFeeder_array[$i] = $no_of_feeder;
						}

					$sql_strip= "select pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id in($pre_cost_fabric_cost_dtls_id) and status_active=1 and is_deleted=0 order by color_number_id,id";

					$result_stripe = sql_select($sql_strip);
					if (count($result_stripe) > 0) {
						?>

						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="7">Stripe Measurement</th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="60">Prog. no</th>
									<th width="140">Color</th> 
									<th width="130">Stripe Color</th> 
									<th width="70">Measurement</th>               
									<th width="50">UOM</th>
									<th>No Of Feeder</th> 
								</tr>
							</thead>
							<?
							$tot_feeder = 0;
							$i = 1;$kl = 0;
							$tot_feeder = 0;
							foreach ($result_stripe as $row) {
								$no_of_feeder=$noOfFeeder_array[$row[csf('pre_cost_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]][$kl];
								$tot_feeder += $no_of_feeder;
								$kl++;
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								//$tot_feeder += $row[csf('no_of_feeder')];
								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="50" align="center"><? echo $programIDS_arr[$row[csf('pre_cost_id')]];//$row[csf('dtls_id')]; ?></td> 	
									<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td> 
									<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>     
									<td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>               
									<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
									<td align="right" style="padding-right:10px"><? echo $no_of_feeder;//$row[csf('no_of_feeder')]; ?>&nbsp;</td>
								</tr>
								<?
								$tot_masurement += $row[csf('measurement')];
								$i++;
							}
							?>
						</tbody>
						<tfoot>
							<th colspan="4">Total</th>
							<th>&nbsp;</th> 
							<th>&nbsp;</th>
							<th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th> 
						</tfoot>

				</table>  
				<?
			}
			?>
			<table  border="1" rules="all"  class="rpt_table">
				<tr>
					<td style="font-size:26px; font-weight:bold; width:20px;">ADVICE: </td>
					<td style="font-size:22px; width:100%;">     <? echo $advice; ?></td>
				</tr> 
			</table> 
			<div>

			<div style="float:left; border:1px solid #000; margin-top:60px;">
				<table border="1" rules="all" class="rpt_table" width="400" height="200" >
					<thead>
						<th colspan="2" style="font-size:22px; font-weight:bold;">Please Strictly Avoid The Following Faults….</th>
					</thead>
						<tbody >
								<tr >
									<td style="width:190px; font-size:16px;"><b> 1.</b> Patta</td>
									<td style="font-size:16px;"><b> 8.</b> Sinker mark</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 2.</b> Loop </td>
									<td style="font-size:16px;"><b> 9.</b> Needle mark</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 3.</b> Hole </td>
									<td style="font-size:16px;"><b> 10.</b> Oil mark</td>
								</tr>
								<tr>
									<td><b> 4.</b> Star marks</td>
									<td><b> 11.</b> Dia mark/Crease Mark</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 5.</b> Barre</td>
									<td style="font-size:16px;"><b> 12.</b> Wheel Free</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 6.</b> Drop Stitch</td>
									<td style="font-size:16px;"><b> 13.</b> Slub</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 7.</b> Lot mixing</td>
									<td style="font-size:16px;"><b> 14.</b> Other contamination</td>
							</tr>
						</tbody>
				</table>
			</div>

			<div style="float:left; border:1px solid #000; margin-top:60px;margin-left: 10px;">
				<table border="1" rules="all" class="rpt_table" width="300" height="150">
					<thead>
						<th colspan="3" style="font-size:20px; font-weight:bold;">Machine Wise Plan Distribution Qty</th>
					</thead>
					<tr>
						<th width="60"> <p>MC No</p></th>
						<th width="90"><p> M/C. Dia && GG</p></th>
						<th width="90"> <p>Prog. Qty</p></th>

					</tr>
					<?
					$total_qty=0;
					foreach($machine_array as $k=>$v)
					{
						?>
						<tr>
							<td width="60" style="font-size:16px;" align="center"> <? echo $v["no"]; ?></td>
							<td width="90" style="font-size:16px;" align="center"> <? echo  $lib_machine_arr[trim($k,",")]["dia_width"]."X".$lib_machine_arr[trim($k,",")]["gauge"];?></td>
							<td width="90" style="font-size:16px;" align="right"> <? echo number_format($v["qty"],2); ?></td>

						</tr>

						<?
						$total_qty+=$v["qty"];
					}

					?>
					<tr>
						<td colspan="2" align="right"> <b>Total</b></td>
						<td align="right"> <b> <? echo number_format($total_qty,2); ?></b></td>
					</tr>						
				</table>
			</div>

			<div style="float:right; border:1px solid #000; margin-top:60px;">
				<table border="1" rules="all" class="rpt_table" width="400" height="150">
					<thead>
						<th colspan="2" style="font-size:20px; font-weight:bold;">Please Mark The Role The Each Role as Follows</th>
					</thead>
					<tr>
						<td width="200" style="font-size:16px;"><b> 1.</b> Manufacturing Factory Name</td>
						<td style="font-size:16px;"><b> 6.</b> Fabrics Type</td>
					</tr>
					<tr>
						<td style="font-size:16px;"><b> 2.</b> Company Name.</td>
						<td style="font-size:16px;"><b> 7.</b> Finished Dia </td>
					</tr>
					<tr>
						<td style="font-size:16px;"><b> 3.</b> Buyer, Style,Order no.</td>
						<td style="font-size:16px;"><b> 8.</b> Finished Gsm & Color</td>
					</tr>
					<tr>
						<td style="font-size:16px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
						<td style="font-size:16px;"><b> 9.</b> Yarn Composition</td>
					</tr>
					<tr>
						<td style="font-size:16px;"><b> 5.</b> M/C No., Dia, Stitch Length</td>
						<td style="font-size:16px;"><b> 10.</b> Knit Program No </td>
					</tr>
				</table>
			</div>
		</div>
		<?
		echo signature_table(41, $company_id, "1180px");
		?>
	</div>
	<?
	exit();
}

if ($action == "requisition_print_four") 
{

	echo load_html_head_contents("Program Qnty Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$data = explode('**', $data);
	$typeForAttention = $data[1];
	$program_ids = $data[0];

	$Sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$company_details = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$floor_arr = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');

	if ($db_type == 0) {
		$plan_details_array = return_library_array("select dtls_id, group_concat(distinct(po_id)) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	} else {
		$plan_details_array = return_library_array("select dtls_id, LISTAGG(po_id, ',') WITHIN GROUP (ORDER BY po_id) as po_id from ppl_planning_entry_plan_dtls where dtls_id in($program_ids) group by dtls_id", "dtls_id", "po_id");
	}


	$po_dataArray = sql_select("select id, grouping, po_number, job_no_mst from wo_po_break_down");
	foreach ($po_dataArray as $row) {
		$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
		$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no_mst')];
		$po_array[$row[csf('id')]]['ref'] = $row[csf('grouping')];
	}

	$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) 
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0) {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} else {
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		$product_details_array[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
		$product_details_array[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
	}


	$knit_id_array = array();
	$prod_id_array = array();
	$rqsn_array = array();
	$reqsn_dataArray = sql_select("select knit_id, requisition_no, prod_id,sum(no_of_cone) as no_of_cone , sum(yarn_qnty) as yarn_qnty from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, prod_id, requisition_no");
	foreach ($reqsn_dataArray as $row) {
		$prod_id_array[$row[csf('knit_id')]][$row[csf('prod_id')]] = $row[csf('yarn_qnty')];
		$knit_id_array[$row[csf('knit_id')]] .= $row[csf('prod_id')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['reqsn'] .= $row[csf('requisition_no')] . ",";
		$rqsn_array[$row[csf('prod_id')]]['qnty'] += $row[csf('yarn_qnty')];
		$rqsn_array[$row[csf('prod_id')]]['no_of_cone'] += $row[csf('no_of_cone')];
	}
	$location_array=return_library_array( "select id, location_name from lib_location",'id','location_name');

	$order_no = '';
	$buyer_name = '';
	$knitting_factory = '';
	$job_no = '';
	$booking_no = '';
	$company = '';
	if ($db_type == 0) {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, a.subcontract_party,a.location_id, a.attention, b.buyer_id, b.booking_no, b.company_id, group_concat(distinct(b.po_id)) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, a.subcontract_party,a.location_id, b.buyer_id, b.booking_no, b.company_id");
	} else {
		$dataArray = sql_select("select a.id, a.knitting_source, a.knitting_party, a.subcontract_party,a.location_id, a.attention, b.buyer_id, b.booking_no, b.company_id, LISTAGG(cast(b.po_id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY b.po_id) as po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.knitting_source, a.knitting_party, a.subcontract_party, a.location_id,b.buyer_id, b.booking_no, b.company_id, a.attention ");
	}

	$k_source = "";
	$sup = "";
	$sub_con= "";
	foreach ($dataArray as $row) 
	{
		if ($duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] == "") {
			$duplicate_arr[$row[csf('knitting_source')]][$row[csf('knitting_party')]] = $row[csf('knitting_party')];

			if ($row[csf('knitting_source')] == 1)
				$knitting_factory .= $company_details[$row[csf('knitting_party')]] . ",";
			else if ($row[csf('knitting_source')] == 3)
				$knitting_factory .= $supplier_details[$row[csf('knitting_party')]] . ",";
		}
		if($row[csf('attention')]) {
			$knt_attention .= $row[csf('attention')].',';
		}		
		if ($buyer_name == "")
			$buyer_name = $buyer_arr[$row[csf('buyer_id')]];
		if ($booking_no == "")
			$booking_no = $row[csf('booking_no')];
		if ($company == "")
			$company = $company_details[$row[csf('company_id')]];
		if ($company_id == "")
			$company_id = $row[csf('company_id')];

		$location_id = $row[csf('location_id')];
		if($row[csf('is_short')] == 2) $booking_type_cond = "(Main)"; else $booking_type_cond ="(Short)" ;


		$po_id = explode(",", $row[csf('po_id')]);

		foreach ($po_id as $val) {
			$order_no .= $po_array[$val]['no'] . ",";
			if ($job_no == "")
				$job_no = $po_array[$val]['job_no'];
		}
		$k_source = $row[csf('knitting_source')];
		$sup = $row[csf('knitting_party')];

		if($row[csf('subcontract_party')] != "" || $row[csf('subcontract_party')] !=0)
		{
			if($sub_con=="") 
			{
				$sub_con .= $Sub_subcontract[$row[csf('subcontract_party')]]; 
			}
			else
			{
				$sub_con .= ", ".$Sub_subcontract[$row[csf('subcontract_party')]];    
			}
		}
	}

	$order_no = array_unique(explode(",", substr($order_no, 0, -1)));

	$machine_wise_sql=sql_select("SELECT id,save_data from ppl_planning_info_entry_dtls where id in($program_ids)");
	foreach($machine_wise_sql as $k=>$v)
	{
		$machine=explode("__", $v[csf("save_data")]);
		foreach($machine as $key=>$vals)
		{
			if($vals)
			{
				$vals=explode("_", $vals);
				$machine_array[$vals[0]]["no"]=$vals[1];
				$machine_array[$vals[0]]["qty"] +=$vals[3];
			}
		}
	}
	$lib_machine_sql=sql_select("SELECT id,dia_width, gauge from lib_machine_name where status_active=1 and is_deleted=0");
	foreach($lib_machine_sql as $k=>$val)
	{
		$lib_machine_arr[$val[csf("id")]]["dia_width"]=$val[csf("dia_width")];
		$lib_machine_arr[$val[csf("id")]]["gauge"]=$val[csf("gauge")];
	}

	?>
	<div style="width:1200px; margin-left:5px; font-family: arial-narrow">
		<table width="100%" style="margin-top:10px;">
			<tr>
				<td width="100%" align="center" style="font-size:22px;"><b><? echo $company; ?></b></td>
			</tr>
			<tr>
				<td align="center" style="font-size:16px">  
					<?
					echo show_company($company_id, '', '');
					?>   
				</td> 
			</tr>
			<tr>
				<td width="100%" align="center" style="font-size:22px;"><b><u>Knitting Program</u></b></td>
			</tr>
		</table>
		<div style="margin-top:10px; width:950px">
			<table width="100%" cellpadding="2" cellspacing="5">
				<tr >
					<td width="140"><b style="font-size:18px">Knitting Factory </b></td>
					<td>:</td>
					<td style="font-size: 18px"> <b><? echo substr($knitting_factory, 0, -1); ?>&nbsp; <span><? echo "($location_array[$location_id])";?></span></b></td>
				</tr>
				<tr>
					<td width="140" style="font-size:14px"><b>Attention </b></td>
					<td>:</td>
					<?
					if ($typeForAttention == 1) {
						echo "<td style=\"font-size:18px; font-weight:bold;\"> Knitting Manager </td>";
					} else {
						?>
						<td style="font-size:18px; font-weight:bold;"><b><?
						if ($k_source == 3) {
							$ComArray = sql_select("select id,contact_person from lib_supplier where id=$sup");
							foreach ($ComArray as $row) {
								echo $row[csf('contact_person')];
							}
						} else {
							$knt_attention_type=rtrim($knt_attention,',');
							 echo implode(", ",array_unique(explode(",",$knt_attention_type)));
						}
						?></b></td>
					<? } ?>
				</tr>
				<tr>
					<td><b>Buyer Name </b></td>
					<td>:</td>
					<td><b><? echo $buyer_name; ?></b></td>
				</tr>
				<tr>   
					<td><b>Style </b></td>
					<td>:</td>    
					<td><? 
					if ($job_no != '') {
						$style_val = return_field_value("style_ref_no", "wo_po_details_master", "job_no='$job_no' and status_active=1 and is_deleted=0", "style_ref_no");
					}

					echo $style_val;
					?></td>
				</tr>
				<tr>   
					<td><b>Order No </b></td>
					<td>:</td>    
					<td><? echo implode(',', $order_no); ?></td>
				</tr>
				<tr>   
					<td><b>Internal Ref. No </b></td>
					<td>:</td>    
					<td>
						<b>
						<?
						if($db_type==0)  
						{
							$sql_ref = sql_select("select group_concat(grouping) as grouping  from wo_po_break_down where job_no_mst='$job_no' and status_active=1 and is_deleted=0");
						}
						else
						{
							$sql_ref = sql_select("select listagg(cast(grouping as varchar2(4000)),',') within group (order by grouping) as grouping  from wo_po_break_down where job_no_mst='$job_no' and status_active=1 and is_deleted=0");
						}

						echo implode(",", array_unique(explode(",", $sql_ref[0][csf("grouping")])));

						?>
						</b>
					</td>
				</tr>

				<tr>    
					<td><b>Job No </b></td>
					<td>:</td>
					<td><? echo $job_no; ?></td>
				</tr> 
				<tr>     
					<td><b>Booking No </b></td>
					<td>:</td>
					<td><b><?
					$is_short_book=return_field_value("is_short","wo_booking_mst","booking_no='$booking_no'","is_short");
					$book_sql=sql_select("select booking_type,is_short from wo_booking_mst where status_active=1 and booking_no='$booking_no'");
					$is_short_book=$book_sql[0][csf("is_short")];
					$booking_type=$book_sql[0][csf("booking_type")];
					if($booking_type==4)
					{
						$is_short_type="Sample";
					}
					else
					{
						if($is_short_book==1) $is_short_type="Short"; else $is_short_type="Main"; 
					}

					echo $booking_no.' ('.$is_short_type.")"; 
					?></b></td>
				</tr>
			</table>
		</div>
        <?
		$distribute_qnty_variable = return_field_value("distribute_qnty", "variable_settings_production", "company_name=$company_id and variable_list=5 and status_active=1 and is_deleted=0", "distribute_qnty");
		
		if($distribute_qnty_variable == 1){
			$tblWidth = "1150";
		}else{
			$tblWidth = "950";
		}
		?>
		<table width="<? echo $tblWidth;?>" style="margin-top:10px; font-family: arial-narrow;" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="100">Requisition No</th>
				<th width="100">Brand</th>
				<th width="100">Lot No</th>
				<th width="200">Yarn Description</th>
				<th width="100">Color</th>
               
                <? if($distribute_qnty_variable == 1){?>
					<th width="100">Distribution Qnty</th>
				<? } ?>
				
                <th width="100">Requisition Qty.</th>
                
				<? if($distribute_qnty_variable == 1){?>
					<th width="100">Returnable Qnty</th>
				<? } ?>
				<th>No Of Cone</th>
			</thead>
			<?
			$j = 1;
			$tot_reqsn_qty = 0;
			foreach ($rqsn_array as $prod_id => $data) 
			{
				if ($j % 2 == 0)
					$bgcolor = "#E9F3FF";
				else
					$bgcolor = "#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30"><? echo $j; ?></td>
					<td width="100"><? echo substr($data['reqsn'], 0, -1); ?></td>
					<td width="100"><p><? echo $product_details_array[$prod_id]['brand']; ?>&nbsp;</p></td>
					<td width="100"><p><? echo $product_details_array[$prod_id]['lot']; ?></p></td>
					<td width="200"><p><? echo $product_details_array[$prod_id]['desc']; ?></p></th>
                    <td width="100"><p><? echo $product_details_array[$prod_id]['color']; ?>&nbsp;</p></td>
                    <?
					if($distribute_qnty_variable == 1){
						$existing_dist = return_field_value("sum(distribution_qnty) as exis_distribution_qnty","ppl_yarn_req_distribution","requisition_no in(".chop($data['reqsn'],",").") and prod_id=".$prod_id." and status_active=1 and is_deleted=0",'exis_distribution_qnty');
						?>
						<td align="right" title="<? echo $data['reqsn']; ?>" width="100"><? echo number_format($existing_dist, 2); ?></td>
						<?
					}
					?>
                    <td width="100" align="right"><p><? echo number_format($data['qnty'], 2, '.', ''); ?></p></td>
                    <?
					if($distribute_qnty_variable == 1){
						?>
						<td align="right" width="100"><? echo $returnable = (($data['qnty']-$existing_dist) > 0)?number_format($data['qnty']-$existing_dist, 2):"0.00"; ?></td>
						<?
					}
					?>
                    <td align="right"><? echo number_format($data['no_of_cone']); ?></td>
					</tr>
					<?
					$tot_reqsn_qty += $data['qnty'];
					$tot_no_of_cone += $data['no_of_cone'];
					
					$tot_dist_qnty += $existing_dist;
					$tot_returnable_qnty += $returnable;
					
					$j++;
				}
				?>
				<tfoot>
					<th colspan="6" align="right">Total</th>
                    <? if($distribute_qnty_variable == 1){ ?>
                        <th align="right"><? echo number_format($tot_dist_qnty, 2); ?></th>
                    <? } ?>
					<th align="right"><? echo number_format($tot_reqsn_qty, 2, '.', ''); ?></th>
                    <? if($distribute_qnty_variable == 1){ ?>
                        <th align="right"><? echo number_format($tot_returnable_qnty, 2); ?></th>
                    <? } ?>
					<th><? echo number_format($tot_no_of_cone); ?></th>
				</tfoot>
		</table>

		<table style="margin-top:10px; font-family: arial-narrow;" width="100%" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table" align="center">
				<thead align="center">
					<th width="25">SL </th>
					<th width="50">Prog/Req./Date</th>
					<th width="120">Fabrication</th>
					<th width="50">GSM</th>
					<th width="40">F. Dia</th>
					<th width="60">Dia Type</th>
					<th width="45">Floor</th> 
					<th width="45">M/c. No</th> 
					<th width="50">M/c. Dia & GG</th>
					<th width="60">Color Range (Knitting)</th>							
					<th width="50">S/L</th>
					<th width="50">Spandex S/L</th>
					<th width="50">Feeder</th>
					<th width="100">Count Feeding</th>
					<th width="70">Knit Start</th>
					<th width="70">Knit End</th>
					<th width="100">Color</th>	
					<th width="70">Program Qty.</th>
					<th width="110">Yarn Description</th>
					<th width="50">Lot</th>
					<th width="70">Yarn Qty.(KG)</th>
					<th>Remarks</th>
				</thead>
				<?
				$i = 1;
				$s = 1;
				$tot_program_qnty = 0;
				$tot_yarn_reqsn_qnty = 0;
				$company_id = '';

				$feedingResult =  sql_select("SELECT dtls_id, seq_no, count_id, feeding_id FROM ppl_planning_count_feed_dtls WHERE dtls_id in($program_ids) and status_active=1 and is_deleted=0");

				$sql_reqsn = sql_select("select knit_id, requisition_no from ppl_yarn_requisition_entry where knit_id in($program_ids) and status_active=1 and is_deleted=0 group by knit_id, requisition_no");
				foreach ($sql_reqsn as $row) {
					$requisition_array[$row[csf('knit_id')]] = $row[csf('requisition_no')];
				}


				$feedingDataArr = array();
				foreach ($feedingResult as $row) {
					$feedingSequence[$row[csf('seq_no')]] =  $row[csf('seq_no')];
					$feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['count_id'] = $row[csf('count_id')];
					$feedingDataArr[$row[csf('dtls_id')]][$row[csf('seq_no')]]['feeding_id'] = $row[csf('feeding_id')];  
				}

				$yarn_count_arr=return_library_array("select id,yarn_count from  lib_yarn_count where status_active=1 and is_deleted=0 order by yarn_count","id","yarn_count");

				$color_prog_sql = "select plan_id, program_no, color_id, color_prog_qty from ppl_color_wise_break_down where program_no in($program_ids) and status_active =1 and is_deleted = 0";
				$color_prog_data = sql_select($color_prog_sql);
				
				$color_prog_arr = array();
				foreach ($color_prog_data as $row) 
				{
					$color_prog_arr[$row[csf('program_no')]][$row[csf('color_id')]] += $row[csf('color_prog_qty')];
				}

				$sql = "select a.company_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_id, b.color_id, b.color_range, b.machine_dia, b.width_dia_type as diatype, b.machine_gg, b.fabric_dia, b.program_qnty, b.program_date, b.stitch_length,b.spandex_stitch_length,b.feeder, b.machine_id, b.start_date, b.end_date, b.remarks,b.advice from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b where a.id=b.mst_id and b.id in($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";			

				$nameArray = sql_select($sql);
				$advice = "";
				foreach ($nameArray as $row) 
				{

					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";
					$color = '';
					$color_id_arr = explode(",", $row[csf('color_id')]);
					$countColor = count($color_id_arr);

					if ($company_id == '')
						$company_id = $row[csf('company_id')];

					$machine_no = '';
					$machine_id = explode(",", $row[csf('machine_id')]);

					foreach ($machine_id as $val) {
						if ($machine_no == '')
							$machine_no = $machine_arr[$val];
						else
							$machine_no .= "," . $machine_arr[$val];
					}
					if ($machine_id[0] != "") {
						$sql_floor = sql_select("select id, machine_no, floor_id from lib_machine_name where id=$machine_id[0] and status_active=1 and is_deleted=0  order by seq_no");
					}

					$count_feeding = "";
					foreach($feedingDataArr[$row[csf('program_id')]] as $feedingSequence=>$feedingData)
					{
						if($count_feeding =="")
						{	
							$count_feeding = $feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];												
						} 
						else 
						{
							$count_feeding .= ",".$feeding_arr[$feedingData['feeding_id']]."-".$yarn_count_arr[$feedingData['count_id']];
						}
					}

					if ($knit_id_array[$row[csf('program_id')]] != "") 
					{
						$all_prod_id = explode(",", substr($knit_id_array[$row[csf('program_id')]], 0, -1));
						$row_span = count($all_prod_id);
						$z = 0;
						foreach ($all_prod_id as $prod_id) 
						{
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<?
								if ($z == 0) {

									?>
									<td width="25" rowspan="<? echo $row_span; ?>"><? echo $i; ?></td>
									<td width="60" rowspan="<? echo $row_span; ?>" align="center"  style="font-size:18px;"><b><? echo $row[csf('program_id')]; ?></b><br><b><? echo $requisition_array[$row[csf('program_id')]]; ?></b><br><p style="font-size:14px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
									<td width="120" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
									<td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
										<td width="50" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

										<td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

										<td width="60" align="center" rowspan="<? echo $row_span; ?>"><p><? echo $machine_no; ?></p></td> 
										<td width="70" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
										<td width="60" rowspan="<? echo $row_span; ?>"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td>
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>															
										<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding?> </p></td>
										<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
										<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>
										<td width="60" rowspan="<? echo $row_span; ?>" valign="middle">
										<? 
											$k=1;	
											foreach($color_id_arr as $color_id)
											{
												echo $color_library[$color_id]; 
												if(count($color_id_arr)!=$k){
													echo "<hr style='border-top: 1px solid #8dafda;'>";
												}
												$k++;
											}
										?>
                                    	</td>

										<td width="70" align="right" rowspan="<? echo $row_span; ?>" valign="middle">

											<?
											$k=1;	 										
											foreach($color_id_arr as $color_id)
											{
												if( !empty($color_prog_arr[$row[csf('program_id')]][$color_id]))
												{
													echo $color_prog_arr[$row[csf('program_id')]][$color_id];

												}else{
													echo $row[csf('program_qnty')];	
												}
												
												if(count($color_id_arr)!=$k){
													echo "<hr style='border-top: 1px solid #8dafda;'>";
												}
												$k++;
											}
											?>
										</td>

										<?
										$tot_program_qnty += $row[csf('program_qnty')];
										$i++;
									}
									?>
									
                                    
                                    <td width="110"><p><? echo $product_details_array[$prod_id]['desc']; ?>&nbsp;</p></td>
									<td width="50" align="center"><p><? echo $product_details_array[$prod_id]['lot']; ?>&nbsp;</p></td>
									<td width="70" align="right"><? echo number_format($prod_id_array[$row[csf('program_id')]][$prod_id], 2, '.', ''); ?></td>
									<?
									if ($z == 0) {
										?>
										<td rowspan="<? echo $row_span; ?>"><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
										<?
									}
									?>
								</tr>
								<?
								$tot_yarn_reqsn_qnty += $prod_id_array[$row[csf('program_id')]][$prod_id];
								$z++;
								$row_span='';
							}
						} else {
							?>
							<tr bgcolor="<? echo $bgcolor; ?>">
								<td width="25"><? echo $i; ?></td>
								<td width="60" align="center" style="font-size:18px;" ><b><? echo $row[csf('program_id')]; ?></b><br><b><? echo $requisition_array[$row[csf('program_id')]]; ?></b><br><p style="font-size:14px;"><? echo change_date_format($row[csf("program_date")]);?></p></td>
								<td width="120"><p><? echo $row[csf('fabric_desc')]; ?></p></td>
								<td width="50" align="center"><p><? echo $row[csf('gsm_weight')]; ?></p></th>
									<td width="50" align="center"><p><? echo $row[csf('fabric_dia')]; ?></p></td>
									<td width="60"><p><? echo $fabric_typee[$row[csf('diatype')]]; ?></p></td>

									<td width="50" align="center"><p><? echo $floor_arr[$sql_floor[0][csf('floor_id')]]; ?></p></td>

									<td width="50" align="center"><p><? echo $machine_no; ?></p></td>
									<td width="50"><p><? echo $row[csf('machine_dia')] . "X" . $row[csf('machine_gg')]; ?></p></td>
									<td width="60"><p><? echo $color_range[$row[csf('color_range')]]; ?></p></td>
									<td width="60"><p><? echo $row_span; ?><p><? echo $row[csf('stitch_length')]; ?></p></td>
									<td width="60"><p><? echo $row[csf('spandex_stitch_length')]; ?></p></td> 
									<td width="70"><p><? echo $feeder[$row[csf('feeder')]]; ?></p></td>
									<td width="50" rowspan="<? echo $row_span; ?>"><p><? echo $count_feeding?> </p></td>
									<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('start_date')]); ?></td>
									<td width="70" rowspan="<? echo $row_span; ?>"><? echo change_date_format($row[csf('end_date')]); ?></td>				
									<td width="50" valign="middle">
									<p>
									<? 
										$k=1;
										foreach($color_id_arr as $color_id)
										{
											echo $color_library[$color_id]; 
											if(count($color_id_arr)!=$k){
												echo "<hr style='border-top: 1px solid #8dafda;'>";
											}	
											$k++;
										}
									?>
									</p>
                                	</td>

									<td width="70" align="center" valign="middle">
										<? 	
										$k=1;									
										foreach($color_id_arr as $color_id)
										{
											echo number_format($color_prog_arr[$row[csf('program_id')]][$color_id],2, '.', '');
											if(count($color_id_arr)!=$k){
												echo "<hr style='border-top: 1px solid #8dafda;'>";
											}
											$k++;
										}
										?> 
									</td>

									<td width="110"><p>&nbsp;</p></td>
									<td width="50"><p>&nbsp;</p></td>
									<td width="70" align="right">&nbsp;</td>
									<td><p><? echo $row[csf('remarks')]; ?>&nbsp;</p></td>
								</tr>
								<?
								$tot_program_qnty += $row[csf('program_qnty')];
								$i++;
							}
							$advice = $row[csf('advice')];
							$advice = str_replace(array(";","\n"), "<br/>", $advice);
						}
						?>
						<tfoot>
							<th colspan="17" align="right"><b>Total</b></th>
							<th align="right"><? echo number_format($tot_program_qnty, 2, '.', ''); ?>&nbsp;</th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>

							<th align="right"><? echo number_format($tot_yarn_reqsn_qnty, 2, '.', ''); ?></th>
							<th>&nbsp;</th>
						</tfoot>

						</table>
						<br>
						<?
						$sql_collarCuff=sql_select("select id, body_part_id, grey_size, finish_size, qty_pcs from ppl_planning_collar_cuff_dtls where status_active=1 and is_deleted=0 and dtls_id in($program_ids) order by id");
						if(count($sql_collarCuff)>0)
						{
							?>
							<table style="margin-top:10px;" width="850" border="1" rules="all" cellpadding="0" cellspacing="0" class="rpt_table">

								<thead>
									<tr>
										<th width="50">SL</th>
										<th width="200">Body Part</th>
										<th width="200">Grey Size</th>
										<th width="200">Finish Size</th>
										<th>Quantity Pcs</th>
									</tr>
								</thead>
								<tbody>
									<?
									$i=1; $total_qty_pcs=0;
									foreach($sql_collarCuff as $row)
									{
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr>
											<td align="center"><p><? echo $i; ?>&nbsp;</p></td>
											<td><p><? echo $body_part[$row[csf('body_part_id')]]; ?>&nbsp;</p></td>
											<td style="padding-left:5px"><p><? echo $row[csf('grey_size')]; ?>&nbsp;</p></td>
											<td style="padding-left:5px"><p><? echo $row[csf('finish_size')]; ?>&nbsp;</p></td>
											<td align="right"><p><? echo number_format($row[csf('qty_pcs')],0); $total_qty_pcs+=$row[csf('qty_pcs')]; ?>&nbsp;&nbsp;</p></td>   
										</tr>
										<?
										$i++;
									}
									?>
								</tbody>
								<tfoot>
									<tr>
										<th></th>
										<th></th>
										<th></th>
										<th align="right">Total</th>
										<th align="right"><? echo number_format($total_qty_pcs,0); ?>&nbsp;</th>
									</tr>
								</tfoot>
							</table>
							<?
						}
						?>
						<br>

					<?
					$sql_strip_data = "select a.pre_cost_fabric_cost_dtls_id,a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder as no_of_feeder from wo_pre_stripe_color a, ppl_planning_feeder_dtls b where a.pre_cost_fabric_cost_dtls_id=b.pre_cost_id and a.color_number_id=b.color_id and a.stripe_color=b.stripe_color_id and b.dtls_id in($program_ids) and b.no_of_feeder>0 and a.status_active=1 and a.is_deleted=0  group by a.pre_cost_fabric_cost_dtls_id,a.color_number_id, a.stripe_color, a.measurement, a.uom, b.dtls_id, b.no_of_feeder ";
					$result_stripe_data = sql_select($sql_strip_data);
					$pre_cost_fabric_cost_dtls_id="";$programIDS_arr=array();
					foreach ($result_stripe_data as $row) {
						$pre_cost_fabric_cost_dtls_id.=$row[csf('pre_cost_fabric_cost_dtls_id')].",";
						$programIDS_arr[$row[csf('pre_cost_fabric_cost_dtls_id')]]=$row[csf('dtls_id')];
					}
					$pre_cost_fabric_cost_dtls_id=chop($pre_cost_fabric_cost_dtls_id,",");

					$feeder_data_sql= sql_select("select id, knitting_source, knitting_party, subcontract_party, color_id, color_range, machine_dia, width_dia_type, machine_gg, fabric_dia, program_qnty, stitch_length, spandex_stitch_length, draft_ratio, machine_id, machine_capacity, distribution_qnty, status, start_date, end_date, program_date, feeder, remarks,attention, co_efficient, save_data, no_fo_feeder_data, location_id, advice, collar_cuff_data, grey_dia from ppl_planning_info_entry_dtls where  id in($program_ids)");
					foreach ($feeder_data_sql as $row ) {
						$no_of_feeder_data =$row[csf('no_fo_feeder_data')];
					}

					$noOfFeeder_array = array();
					$no_of_feeder_data = explode(",", $no_of_feeder_data);
					$pre_cost_id = explode(",", $pre_cost_fabric_cost_dtls_id);
					$pre_cost_id = implode(",", array_unique($pre_cost_id));

					$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");

					for ($i = 0; $i < count($no_of_feeder_data); $i++) {
						$color_wise_data = explode("_", $no_of_feeder_data[$i]);
						$pre_cost_fabric_cost_dtls_id = $color_wise_data[0];
						$color_id = $color_wise_data[1];
						$stripe_color = $color_wise_data[2];
						$no_of_feeder = $color_wise_data[3];

						$noOfFeeder_array[$pre_cost_fabric_cost_dtls_id][$color_id][$stripe_color][$i]=$no_of_feeder;
						//$noOfFeeder_array[$i] = $no_of_feeder;
					}

					$sql_strip= "select pre_cost_fabric_cost_dtls_id as pre_cost_id, color_number_id, stripe_color, measurement, uom from wo_pre_stripe_color where pre_cost_fabric_cost_dtls_id in($pre_cost_fabric_cost_dtls_id) and status_active=1 and is_deleted=0 order by color_number_id,id";

					$result_stripe = sql_select($sql_strip);
					if (count($result_stripe) > 0) {
						?>
						<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
							<thead>
								<tr>
									<th colspan="7">Stripe Measurement</th>
								</tr>
								<tr>
									<th width="30">SL</th>
									<th width="60">Prog. no</th>
									<th width="140">Color</th> 
									<th width="130">Stripe Color</th> 
									<th width="70">Measurement</th>               
									<th width="50">UOM</th>
									<th>No Of Feeder</th> 
								</tr>
							</thead>
							<?
							$tot_feeder = 0;
							$i = 1;$kl = 0;
							$tot_feeder = 0;
							foreach ($result_stripe as $row) {
								$no_of_feeder=$noOfFeeder_array[$row[csf('pre_cost_id')]][$row[csf('color_number_id')]][$row[csf('stripe_color')]][$kl];
								$tot_feeder += $no_of_feeder;
								$kl++;
								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								//$tot_feeder += $row[csf('no_of_feeder')];
								?>
								<tr valign="middle" bgcolor="<? echo $bgcolor; ?>" id="search<? echo $i; ?>"> 
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="50" align="center"><? echo $programIDS_arr[$row[csf('pre_cost_id')]];//$row[csf('dtls_id')]; ?></td> 	
									<td width="140"><p><? echo $color_library[$row[csf('color_number_id')]]; ?></p></td> 
									<td width="130"><p><? echo $color_library[$row[csf('stripe_color')]]; ?></p></td>     
									<td width="70" align="center"><? echo $row[csf('measurement')]; ?></td>               
									<td width="50" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
									<td align="right" style="padding-right:10px"><? echo $no_of_feeder;//$row[csf('no_of_feeder')]; ?>&nbsp;</td>
								</tr>
								<?
								$tot_masurement += $row[csf('measurement')];
								$i++;
							}
							?>
						</tbody>
						<tfoot>
							<th colspan="4">Total</th>
							<th>&nbsp;</th> 
							<th>&nbsp;</th>
							<th style="padding-right:10px"><? echo $tot_feeder; ?>&nbsp;</th> 
						</tfoot>

				</table>  
				<?
			}
			?>
			<table border="1" rules="all" class="rpt_table">
				<tr>
					<td style="font-size:26px; font-weight:bold; width:20px;">ADVICE: </td>
					<td style="font-size:22px; width:100%;">     <? echo $advice; ?></td>
				</tr> 
			</table> 
			<div>

			<div style="float:left; border:1px solid #000; margin-top:60px;">
				<table border="1" rules="all" class="rpt_table" width="400" height="200" >
					<thead>
						<th colspan="2" style="font-size:22px; font-weight:bold;">Please Strictly Avoid The Following Faults….</th>
					</thead>
						<tbody >
								<tr >
									<td style="width:190px; font-size:16px;"><b> 1.</b> Patta</td>
									<td style="font-size:16px;"><b> 8.</b> Sinker mark</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 2.</b> Loop </td>
									<td style="font-size:16px;"><b> 9.</b> Needle mark</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 3.</b> Hole </td>
									<td style="font-size:16px;"><b> 10.</b> Oil mark</td>
								</tr>
								<tr>
									<td><b> 4.</b> Star marks</td>
									<td><b> 11.</b> Dia mark/Crease Mark</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 5.</b> Barre</td>
									<td style="font-size:16px;"><b> 12.</b> Wheel Free</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 6.</b> Drop Stitch</td>
									<td style="font-size:16px;"><b> 13.</b> Slub</td>
								</tr>
								<tr>
									<td style="font-size:16px;"><b> 7.</b> Lot mixing</td>
									<td style="font-size:16px;"><b> 14.</b> Other contamination</td>
							</tr>
						</tbody>
				</table>
			</div>

			<div style="float:left; border:1px solid #000; margin-top:60px;margin-left: 10px;">
				<table border="1" rules="all" class="rpt_table" width="300" height="150">
					<thead>
						<th colspan="3" style="font-size:20px; font-weight:bold;">Machine Wise Plan Distribution Qty</th>
					</thead>
					<tr>
						<th width="60"> <p>MC No</p></th>
						<th width="90"><p> M/C. Dia && GG</p></th>
						<th width="90"> <p>Prog. Qty</p></th>

					</tr>
					<?
					$total_qty=0;
					foreach($machine_array as $k=>$v)
					{
						?>
						<tr>
							<td width="60" style="font-size:16px;" align="center"> <? echo $v["no"]; ?></td>
							<td width="90" style="font-size:16px;" align="center"> <? echo  $lib_machine_arr[trim($k,",")]["dia_width"]."X".$lib_machine_arr[trim($k,",")]["gauge"];?></td>
							<td width="90" style="font-size:16px;" align="right"> <? echo number_format($v["qty"],2); ?></td>

						</tr>

						<?
						$total_qty+=$v["qty"];
					}


					?>
					<tr>
						<td colspan="2" align="right"> <b>Total</b></td>
						<td align="right"> <b> <? echo number_format($total_qty,2); ?></b></td>
					</tr>						
				</table>
			</div>

			<div style="float:right; border:1px solid #000; margin-top:60px;">
				<table border="1" rules="all" class="rpt_table" width="400" height="150">
					<thead>
						<th colspan="2" style="font-size:20px; font-weight:bold;">Please Mark The Role The Each Role as Follows</th>
					</thead>
					<tr>
						<td width="200" style="font-size:16px;"><b> 1.</b> Manufacturing Factory Name</td>
						<td style="font-size:16px;"><b> 6.</b> Fabrics Type</td>
					</tr>
					<tr>
						<td style="font-size:16px;"><b> 2.</b> Company Name.</td>
						<td style="font-size:16px;"><b> 7.</b> Finished Dia </td>
					</tr>
					<tr>
						<td style="font-size:16px;"><b> 3.</b> Buyer, Style,Order no.</td>
						<td style="font-size:16px;"><b> 8.</b> Finished Gsm & Color</td>
					</tr>
					<tr>
						<td style="font-size:16px;"><b> 4.</b> Yarn Count, Lot & Brand</td>
						<td style="font-size:16px;"><b> 9.</b> Yarn Composition</td>
					</tr>
					<tr>
						<td style="font-size:16px;"><b> 5.</b> M/C No., Dia, Stitch Length</td>
						<td style="font-size:16px;"><b> 10.</b> Knit Program No </td>
					</tr>
				</table>
			</div>
		</div>
		<?
		echo signature_table(41, $company_id, "1180px");
		?>
	</div>
	<?
	exit();
}

if ($action == "knitting_card_print") 
{
	echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$program_ids =  $data;
	$sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	if ($db_type == 0) $item_id_cond="group_concat(distinct(b.item_id))";
	else if ($db_type==2) $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

	if($program_ids)
	{
		$reqsDataArr = array();
		$program_cond2 = ($program_ids)?" and knit_id in(".$program_ids.")":"";
		if ($db_type == 0) {
			$reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
		} else {
			$reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
		}
		foreach ($reqsData as $row) {
	//$reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			$reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
	//$reqsDataArr[$row[csf('knit_id')]]['yarn_req_qnty'] = $row[csf('yarn_req_qnty')];
	//$requisition_no_arr[] = $row[csf('reqs_no')];
			$prod_arr[] = $row[csf('prod_id')];
		}
	}



	if(!empty($prod_arr))
	{
		$product_details_arr = array();
		$procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
		$pro_sql = sql_select("select id, product_name_details, lot,brand, supplier_id from product_details_master where item_category_id=1 $procuct_cond");

		foreach ($pro_sql as $row) {
			$product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
	//$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
		}
	}

	$booking_qnty_arr = array();
	$sql_data = sql_select("select a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
	foreach ($sql_data as $row) {
		$booking_qnty_arr[$row[csf('booking_no')]]= $row[csf('grey_fab_qnty')];
	}
	unset($sql_data);


	$order_no = ''; $buyer_name = ''; $knitting_factory = ''; $job_no = '';  $booking_no = ''; $company = '';


	$jobNo=""; $poQuantity="";
	$job_data_sql=sql_select("select a.job_no_mst, sum(a.po_quantity) as poQuantity from wo_po_break_down a, ppl_planning_entry_plan_dtls b where a.id=b.po_id and b.dtls_id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_no_mst");	
	foreach($job_data_sql as $row)
	{
		$jobNo= $row[csf('job_no_mst')];
		$poQuantity=$row[csf('poQuantity')] ;
	}
	
	
	$data_sql="select a.id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.remarks, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.yarn_desc, b.gsm_weight from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";

	$dataArray = sql_select($data_sql); $program_data_arr=array();
	$company_id = ''; $buyer_name = ''; $booking_no = '';
	foreach ($dataArray as $row)
	{
		$knitting_factory='';
		if ($row[csf('knitting_source')] == 1)
			$knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
		else if ($row[csf('knitting_source')] == 3)
			$knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

		$yarn_desc=''; $lot_no=""; $brand_name="";
	

		$prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
		foreach ($prod_id as $val) {
			$yarn_desc .= $product_details_arr[$val]['desc'] . ",";
			$lot_no .= $product_details_arr[$val]['lot'] . ",";
			$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ",";
		}

		$yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
		$lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
		$brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));

		$machine_name="";
		$ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));
		foreach($ex_mc_id as $mc_id)
		{
			if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
		}

		$color_name="";
		$ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
		foreach($ex_color_id as $color_id)
		{
			if($color_name=='') $color_name=$color_library[$color_id]; else $color_name.=','.$color_library[$color_id];
		}

		$program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
		$program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
		$program_data_arr[$row[csf('id')]]['color_id']=$color_name;
		$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
		$program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
		$program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
		$program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
		$program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
		$program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
		$program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]];
		$program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

		$program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
		$program_data_arr[$row[csf('id')]]['lot']= $lot_no;

		$program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
		$program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
		$program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
		$program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
		$program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
		$program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
	}	
		unset($dataArray);

	foreach($program_data_arr as $prog_no=>$prog_data)
	{
		?>
		<style type="text/css">
		.page_break	{ page-break-after: always;
		}
		</style>
		<div style="width:930px;">
			<table width="100%" cellpadding="0" cellspacing="0" >
				<tr>
					<td width="70" align="right"> 
						<img  src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' />
					</td>
					<td>
						<table width="100%" style="margin-top:10px">
							<tr>
								<td width="100%" align="center" style="font-size:20px;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
							</tr>
							<tr>
								<td align="center" style="font-size:14px"><? echo show_company($prog_data['company_id'], '', ''); ?></td> 
							</tr>
							<tr>
								<td width="100%" align="center" style="font-size:16px;"><b><u>Production Batch Card, Section-Knitting</u></b></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<div style="margin-top:5px; width:930px">
				<table cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table" style="font-size:12px; font-family:'Arial Narrow'">
					<tr>
						<td width="130">Prog. No:</td><td width="160"><? echo $prog_no; ?></td>
						<td width="130">RPM:</td><td width="160">&nbsp;</td>
						<td width="130">Knit.Card Date:</td><td><? echo date("d-m-Y",time()); ?></td>
					</tr>
					<tr>
						<td width="130">M/C Dia:</td><td width="160"><? echo $prog_data['machine_dia']; ?></td>
						<td width="130">Lot/ Batch:</td><td width="160"><? echo $prog_data['lot']; ?></td>
						<td width="130">S/L:</td><td><? echo $prog_data['s_length']; ?></td>
					</tr>
					<tr>
						<td width="130">M/C Gauge:</td><td width="160"><? echo $prog_data['machine_gg']; ?></td>
						<td width="130">Fab. Type:</td><td width="160"><? echo $prog_data['fabric_desc']; ?></td>
						<td width="130">Yarn Desc.:</td><td><? echo $prog_data['yarn_desc']; ?></td>
					</tr>
					<tr>
						<td width="130">MC No:</td><td width="160"><? echo $prog_data['mc_nmae']; ?></td>
						<td width="130">Booking Qty:</td><td width="160"><? echo number_format($prog_data['booking_qty'], 2, '.', ''); ?></td>
						<td width="130">Colour:</td><td><? echo $prog_data['color_id']; ?></td>
					</tr>
					<tr>
						<td width="130">Buyer:</td><td width="160"><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
						<td width="130">FGSM:</td><td width="160"><? echo $prog_data['gsm_weight']; ?></td>
						<td width="130">Knitting Party:</td><td><? echo $prog_data['knit_factory']; ?></td>
					</tr>
					<tr>
						<td width="130">Booking No:</td><td width="160"><? echo $prog_data['booking_no']; ?></td>
						<td width="130">Fin. Dia:</td><td width="160"><? echo $prog_data['fabric_dia']. " (".$fabric_typee[$prog_data['width_dia_type']].") "; ?></td>
						<td width="130">Sub Con Party:</td><td><? echo $prog_data['sub_party']; ?></td>
					</tr>
					<tr>
						<td width="130">Prog. Date:</td><td width="160"><? echo change_date_format($prog_data['program_date']); ?></td>
						<td width="130">Shift/Target:</td><td width="160"><? //echo $prog_data['machine_dia']; ?></td>
						<td width="130">Job No:</td><td><? echo $jobNo; ?></td>
					</tr>
                    <tr>
						<td width="130">PO Quantity:</td><td width="160"><? echo  $poQuantity; ?></td>
						<td width="130">Prog. Quantity:</td><td ><? echo $prog_data['prog_qty']; ?></td>
                        <td width="130">Brand:</td><td ><? echo $prog_data['brand_name']; ?></td>
					</tr>
                    <tr>
                    	<td width="130">Remarks:</td><td  colspan="5" ><? echo $prog_data['remarks']; ?></td>
                    </tr>
                    
				</table>
			</div>
			<div style="margin-top:5px; width:930px">
				<table cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table">
					<thead>
						<th width="20">SL.</th>
						<th width="100">Order Qty</th>
						<th width="90">Date</th>
						<th width="70">Shift</th>
						<th width="130">Roll</th>
						<th width="100">Prod Qty</th>
						<th width="100">Balance</th>
						<th width="140">Operator</th>
						<th>Remarks</th>
					</thead>
					<? $row_count=20;
					for($i=1; $i<=$row_count; $i++)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" height="25px">
							<td width="20"><? echo $i; ?></td>
							<td width="100">&nbsp;</td>
							<td width="90">&nbsp;</td>
							<td width="70">&nbsp;</td>
							<td width="130">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="100">&nbsp;</td>
							<td width="140">&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<?
					}
					?>
				</table>
			</div>
			<div style="margin-top:5px; width:920px">
				<table cellspacing="2" cellpadding="2" rules="all" width="100%" style="font-size:14px; font-family:'Arial Narrow'">
					<tbody>
						<tr><td><u><b>বিঃ দ্রঃ</b></u></td></tr>
						<tr><td>* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।</td></tr>
						<tr><td>* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।</td></tr>
						<tr><td>* রোল মার্কিং সঠিকভাবে করতে হবে। </td></tr>
						<tr><td>* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।</td></tr>
						<tr><td>* রোলের গায়ে তারিখ/ অপারেটরের নাম এবং শিফট অবশ্যই লিখতে হবে।</td></tr>
						<tr><td>&nbsp;</td></tr>
					</tbody>
				</table>
			</div>
			<? echo signature_table(119, $prog_data['company_id'], "920px"); ?>
			<div class="page_break">&nbsp;</div>
		</div>
		<?
	}
	exit();
}

if ($action == "knitting_card_print_2") 
{
	echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
			// $data = explode('**', $data);
		//$typeForAttention = $data[1];
	$program_ids =  $data;


	$sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	if ($db_type == 0) $item_id_cond="group_concat(distinct(b.item_id))";
	else if ($db_type==2) $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

	if($program_ids)
	{
		$result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");					
		$machin_prog = array();
		foreach ($result_machin_prog as $row) {
			$machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
		}
	}



	if($program_ids)
	{
		$reqsDataArr = array();
		$program_cond2 = ($program_ids)?" and knit_id in(".$program_ids.")":"";
		if ($db_type == 0) {
			$reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
		} else {
			$reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
		}
		foreach ($reqsData as $row) {
			//$reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			$reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
			//$reqsDataArr[$row[csf('knit_id')]]['yarn_req_qnty'] = $row[csf('yarn_req_qnty')];
			//$requisition_no_arr[] = $row[csf('reqs_no')];
			$prod_arr[] = $row[csf('prod_id')];
		}
	}

	if(!empty($prod_arr))
	{
		$product_details_arr = array();
		$procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
		$pro_sql = sql_select("select id, product_name_details, lot,brand, supplier_id from product_details_master where item_category_id=1 $procuct_cond");
		foreach ($pro_sql as $row) {
			$product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
		//$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
		}
	}


	$booking_qnty_arr = array();
	$sql_data = sql_select("select a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
	foreach ($sql_data as $row) {
		$booking_qnty_arr[$row[csf('booking_no')]]= $row[csf('grey_fab_qnty')];
	}
	unset($sql_data);

		/*$product_details_array = array();
		$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
		$result = sql_select($sql);

		foreach ($result as $row) 
		{
			$compos = '';
			if ($row[csf('yarn_comp_percent2nd')] != 0)
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			} 
			else 
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}

			$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
			$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		}
		unset($result);*/
		$order_no = ''; $buyer_name = ''; $knitting_factory = ''; $job_no = '';  $booking_no = ''; $company = '';

			//$sql_req_lot = sql_select("select b.lot from ppl_yarn_requisition_entry a,product_details_master b where a.knit_id in($program_ids) and a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
		
		$jobNo=""; $poQuantity="";
		$job_data_sql=sql_select("select a.id,a.job_no_mst,a.po_number, sum(a.po_quantity) as poQuantity,c.style_ref_no from wo_po_break_down a, ppl_planning_entry_plan_dtls b,wo_po_details_master c where a.id=b.po_id and b.dtls_id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst=job_no group by a.id,a.job_no_mst,a.po_number,c.style_ref_no");	
		$po_details= array();
		foreach($job_data_sql as $row)
		{
			$jobNo= $row[csf('job_no_mst')];
			$poQuantity=$row[csf('poQuantity')];
			$style = $row[csf('style_ref_no')];
			$po_details[$row[csf('id')]]['po_number']= $row[csf('po_number')];

		}
		
		$data_sql="select a.id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date,a.draft_ratio,a.start_date,a.end_date, a.remarks,a.co_efficient, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.yarn_desc, b.gsm_weight,b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		
		$dataArray = sql_select($data_sql); $program_data_arr=array();
		$company_id = ''; $buyer_name = ''; $booking_no = '';
		$orderNo = "";
		foreach ($dataArray as $row)
		{
			$knitting_factory='';
			if ($row[csf('knitting_source')] == 1)
				$knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
			else if ($row[csf('knitting_source')] == 3)
				$knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

			$yarn_desc=''; $lot_no=""; $brand_name="";
			/*$ex_yarn_desc=array_unique(explode(",",$booking_item_array[$row[csf('booking_no')]]));
			foreach($ex_yarn_desc as $prodid)
			{
				if($yarn_desc=='') $yarn_desc=$product_details_array[$prodid]['desc']; else $yarn_desc.=','.$product_details_array[$prodid]['desc'];
				if($lot_no=='') $lot_no=$product_details_array[$prodid]['lot']; else $lot_no.=','.$product_details_array[$prodid]['lot'];
			}*/
			if($orderNo=="")
			{
				$orderNo .= $row[csf('po_id')];
				$po_number .= $po_details[$row[csf('po_id')]]['po_number'];
			}else {
				$orderNo .= ",".$row[csf('po_id')];
				$po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
			}
			

			$prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
			foreach ($prod_id as $val) {
				$yarn_desc .= $product_details_arr[$val]['desc'] . ",";
				$lot_no .= $product_details_arr[$val]['lot'] . ",";
				$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ",";
			}

			$yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
			$lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
			$brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
			$ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

			/*$machine_name="";
			foreach($ex_mc_id as $mc_id)
			{
				if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
			}*/

			$color_name="";
			$ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
			foreach($ex_color_id as $color_id)
			{
				if($color_name=='') $color_name=$color_library[$color_id]; else $color_name.=','.$color_library[$color_id];
			}

			$program_data_arr[$row[csf('id')]]['po_number']=$po_number;
			$program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
			$program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
			$program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
			$program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
			$program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
			$program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
			$program_data_arr[$row[csf('id')]]['color_id']=$color_name;
			$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
			$program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
			$program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
			$program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
			$program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
			$program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]];
			$program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

			$program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
			$program_data_arr[$row[csf('id')]]['lot']= $lot_no;
			$program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
			$program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
			$program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
			$program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
			$program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
			$program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
		}
		unset($dataArray);

		if($orderNo!="")
		{
			$sql_tna = sql_select("select job_no,po_number_id,task_start_date,task_finish_date FROM tna_process_mst WHERE status_active=1 AND is_deleted=0 and po_number_id in ($orderNo) and task_number=60");
			$tnaData = array();

			if(!empty($sql_tna))
			{
				foreach($sql_tna as $tna_row)
				{
					$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_start_date'] =  $tna_row[csf('task_start_date')];

					$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_finish_date'] =  $tna_row[csf('task_finish_date')];
				}
			}
			
		}
		
		foreach($ex_mc_id as $mc_id)
		{
			// program array loop
			foreach($program_data_arr as $prog_no=>$prog_data)
			{
				?>
				<style type="text/css">
				.page_break	{ page-break-after: always;
				}
			</style>
			<div style="width:930px;">
				<table width="100%" cellpadding="0" cellspacing="0" >
					<tr>
						<td width="70" align="right"> 
							<img  src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' />
						</td>
						<td>
							<table width="100%" style="margin-top:10px">
								<tr>
									<td width="100%" align="center" style="font-size:20px;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
								</tr>
								<tr>
									<td align="center" style="font-size:14px"><? echo show_company($prog_data['company_id'], '', ''); ?></td> 
								</tr>
								<tr>
									<td width="100%" align="center" style="font-size:16px;"><b><u>Job Card / Knit Card</u></b></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<div style="margin-top:5px; width:930px">
					<table cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table" style="font-size:13px; font-family:'Arial Narrow'">
						<thead height="25">
							<th colspan="2">Program Details</th>
							<th colspan="2" width="200">Job Details</th>
							<th colspan="2" width="200">Yarn/Fabric Details</th>
							<th colspan="2" width="200">M/C Details</th>
							<th colspan="2" width="80">Technical Details</th>
						</thead>
						<tr height="22">
							<td>Program No</td>
							<td><? echo $prog_no; ?></td>
							<td>Buyer</td>
							<td><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
							<td rowspan="4" valign="middle">Yarn Desc</td>
							<td rowspan="4" valign="middle" width="150"><? echo $prog_data['yarn_desc']; ?></td>
							<td>M/C No</td>
							<td><? echo $machine_arr[$mc_id];?></td>
							<td>Stitch Length</td>
							<td width="50"><? echo $prog_data['s_length']; ?></td>
						</tr>
						<tr height="22">
							<td>Program Date</td>
							<td><? echo change_date_format($prog_data['program_date']); ?></td>
							<td>Order</td>
							<td><? echo $prog_data['po_number']; ?></td>
							<td>Dia x Gauge</td>
							<td><? echo $prog_data['machine_dia']."x".$prog_data['machine_gg']; ?></td>
							<td>Draft Ratio</td>
							<td><? echo number_format($prog_data['draft_ratio'],2);?></td>
						</tr>
						<tr height="22">
							<td>Program Qty</td>
							<td><? echo number_format($prog_data['prog_qty'],2);?></td>
							<td>Job No</td>
							<td><? echo $jobNo; ?></td>
							<td>Finished Dia</td>
							<td><? echo $prog_data['fabric_dia']; ?></td>
							<td>M/C RPM</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Target/Shift</td>
							<td>&nbsp;</td>
							<td>Style</td>
							<td><? echo $style; ?></td>
							<td>Fabric Type</td>
							<td width="150"><? echo $prog_data['fabric_desc']; ?></td>
							<td>Grey GSM</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Program TnA Start</td>
							<td><? echo change_date_format($prog_data['start_date']); ?></td>
							<td>Knit TnA Star</td>
							<td><? echo change_date_format($tnaData[$jobNo][$prog_data['po_id']]['task_start_date']); ?></td>
							<td rowspan="2" valign="middle">Yarn Brand:</td>
							<td rowspan="2" valign="middle" width="150"><? echo $prog_data['brand_name']; ?></td>
							<td>FGSM</td>
							<td><? echo $prog_data['gsm_weight']; ?></td>
							<td>Yarn Tension</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Program TnA End</td>
							<td><? echo change_date_format($prog_data['end_date']); ?></td>
							<td>Knit TnA End</td>
							<td><? echo change_date_format($tnaData[$jobNo][$prog_data['po_id']]['task_finish_date']); ?></td>
							<td>Color</td>
							<td><? echo $prog_data['color_id']; ?></td>
							<td>Spreder Dia</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Knitting Coefficient</td>
							<td><? echo $prog_data['co_efficient']; ?></td>
							<td>Knit Party</td>
							<td><? echo $prog_data['knit_factory']; ?></td>
							<td>Yarn Lot:</td>
							<td><? echo $prog_data['lot']; ?></td>
							<td>Counter</td>
							<td>&nbsp;</td>
							<td>Fabric Take-up</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Target Qty</td>
							<td>
								<?
								$distribution_qnty = $machin_prog[$mc_id][$prog_no]['distribution_qnty'];
								$targateQty = ($distribution_qnty*$prog_data['co_efficient']);
								echo $targateQty;
								?>

							</td>
							<td>Remarks</td>
							<td colspan="3"><? echo $prog_data['remarks']; ?></td>
							<td>M/C Target QTY</td>
							<td><?php echo $distribution_qnty; ?></td>

							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
				<div style="margin-top:10px; width:930px;">
					<table  cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table">

						<thead height="25">
							<th width="64" height="20">Date</th>
							<th width="64">Shift</th>
							<th width="68">Order Qty</th>
							<th width="74">No. Or Roll</th>
							<th width="99">Production qty</th>
							<th width="69">Reject qty</th>
							<th width="80">Balance Qty</th>
							<th width="78">Operator Id</th>
							<th width="100">Name</th>
							<th width="66">Signature</th>
							<th width="150">Remarks</th>
						</thead>

						<? $row_count=10;
						for($i=1; $i<=$row_count; $i++)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr height="24" bgcolor="<? echo $bgcolor; ?>">
								<td rowspan="2">&nbsp;</td>
								<td align="center" height="24">Shift-A</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr height="24">
								<td align="center" height="24">Shift-B</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?
						}
						?>

					</table>
				</div>
				<div style="margin-top:10px; width:920px">
					<table cellspacing="2" cellpadding="2" rules="all" width="100%" style="font-size:14px; font-family:'Arial Narrow'">
						<tbody>
							<tr><td><u><b>বিঃ দ্রঃ</b></u></td></tr>
							<tr><td>* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।</td></tr>
							<tr><td>* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।</td></tr>
							<tr><td>* রোল মার্কিং সঠিকভাবে করতে হবে। </td></tr>
							<tr><td>* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।</td></tr>
							<tr><td>* রোলের গায়ে তারিখ/ অপারেটরের নাম এবং শিফট অবশ্যই লিখতে হবে।</td></tr>
							<tr><td>&nbsp;</td></tr>
						</tbody>
					</table>
				</div>
				<? echo signature_table(119, $prog_data['company_id'], "920px","","20"); ?>
				<div class="page_break">&nbsp;</div>
			</div>
			<?
		}
	}
	exit();
}

if ($action == "knitting_card_print_3") 
{
	echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
			// $data = explode('**', $data);
		//$typeForAttention = $data[1];
	$program_ids =  $data;


	$sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	if ($db_type == 0) $item_id_cond="group_concat(distinct(b.item_id))";
	else if ($db_type==2) $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";

	if($program_ids)
	{
		$result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");					
		$machin_prog = array();
		foreach ($result_machin_prog as $row) {
			$machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
		}
	}



	if($program_ids)
	{
		$reqsDataArr = array();
		$program_cond2 = ($program_ids)?" and knit_id in(".$program_ids.")":"";
		if ($db_type == 0) {
			$reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
		} else {
			$reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
		}
		foreach ($reqsData as $row) {
			//$reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			$reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
			//$reqsDataArr[$row[csf('knit_id')]]['yarn_req_qnty'] = $row[csf('yarn_req_qnty')];
			//$requisition_no_arr[] = $row[csf('reqs_no')];
			$prod_arr[] = $row[csf('prod_id')];
		}
	}

	if(!empty($prod_arr))
	{
		$product_details_arr = array();
		$procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
		$pro_sql = sql_select("select id, product_name_details, lot,brand, supplier_id from product_details_master where item_category_id=1 $procuct_cond");
		foreach ($pro_sql as $row) {
			$product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
		//$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
		}
	}


	$booking_qnty_arr = array();
	$sql_data = sql_select("select a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
	foreach ($sql_data as $row) {
		$booking_qnty_arr[$row[csf('booking_no')]]= $row[csf('grey_fab_qnty')];
	}
	unset($sql_data);

		/*$product_details_array = array();
		$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
		$result = sql_select($sql);

		foreach ($result as $row) 
		{
			$compos = '';
			if ($row[csf('yarn_comp_percent2nd')] != 0)
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			} 
			else 
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}

			$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
			$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		}
		unset($result);*/
		$order_no = ''; $buyer_name = ''; $knitting_factory = ''; $job_no = '';  $booking_no = ''; $company = '';

			//$sql_req_lot = sql_select("select b.lot from ppl_yarn_requisition_entry a,product_details_master b where a.knit_id in($program_ids) and a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
		
		$jobNo=""; $poQuantity="";
		$job_data_sql=sql_select("select a.id,a.job_no_mst,a.po_number, sum(a.po_quantity) as poQuantity,c.style_ref_no from wo_po_break_down a, ppl_planning_entry_plan_dtls b,wo_po_details_master c where a.id=b.po_id and b.dtls_id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst=job_no group by a.id,a.job_no_mst,a.po_number,c.style_ref_no");	
		$po_details= array();
		foreach($job_data_sql as $row)
		{
			$jobNo= $row[csf('job_no_mst')];
			$poQuantity=$row[csf('poQuantity')];
			$style = $row[csf('style_ref_no')];
			$po_details[$row[csf('id')]]['po_number']= $row[csf('po_number')];

		}
		
		$data_sql="select a.id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date,a.draft_ratio,a.start_date,a.end_date, a.remarks,a.co_efficient, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.yarn_desc, b.gsm_weight,b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		
		$dataArray = sql_select($data_sql); $program_data_arr=array();
		$company_id = ''; $buyer_name = ''; $booking_no = '';
		$orderNo = "";
		foreach ($dataArray as $row)
		{
			$knitting_factory='';
			if ($row[csf('knitting_source')] == 1)
				$knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
			else if ($row[csf('knitting_source')] == 3)
				$knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

			$yarn_desc=''; $lot_no=""; $brand_name="";
			/*$ex_yarn_desc=array_unique(explode(",",$booking_item_array[$row[csf('booking_no')]]));
			foreach($ex_yarn_desc as $prodid)
			{
				if($yarn_desc=='') $yarn_desc=$product_details_array[$prodid]['desc']; else $yarn_desc.=','.$product_details_array[$prodid]['desc'];
				if($lot_no=='') $lot_no=$product_details_array[$prodid]['lot']; else $lot_no.=','.$product_details_array[$prodid]['lot'];
			}*/
			if($orderNo=="")
			{
				$orderNo .= $row[csf('po_id')];
				$po_number .= $po_details[$row[csf('po_id')]]['po_number'];
			}else {
				$orderNo .= ",".$row[csf('po_id')];
				$po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
			}
			

			$prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
			foreach ($prod_id as $val) {
				$yarn_desc .= $product_details_arr[$val]['desc'] . ",";
				$lot_no .= $product_details_arr[$val]['lot'] . ",";
				$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ",";
			}


			$yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
			$lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
			$brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
			$ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

			/*$machine_name="";
			foreach($ex_mc_id as $mc_id)
			{
				if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
			}*/

			$color_name="";
			$ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
			foreach($ex_color_id as $color_id)
			{
				if($color_name=='') $color_name=$color_library[$color_id]; else $color_name.=','.$color_library[$color_id];
			}

			$program_data_arr[$row[csf('id')]]['po_number']=$po_number;
			$program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
			$program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
			$program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
			$program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
			$program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
			$program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
			$program_data_arr[$row[csf('id')]]['color_id']=$color_name;
			$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
			$program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
			$program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
			$program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
			$program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
			$program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]];
			$program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

			$program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
			$program_data_arr[$row[csf('id')]]['lot']= $lot_no;
			$program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
			$program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
			$program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
			$program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
			$program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
			$program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
		}
		unset($dataArray);

		if($orderNo!="")
		{
			$sql_tna = sql_select("select job_no,po_number_id,task_start_date,task_finish_date FROM tna_process_mst WHERE status_active=1 AND is_deleted=0 and po_number_id in ($orderNo) and task_number=60");
			$tnaData = array();

			if(!empty($sql_tna))
			{
				foreach($sql_tna as $tna_row)
				{
					$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_start_date'] =  $tna_row[csf('task_start_date')];

					$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_finish_date'] =  $tna_row[csf('task_finish_date')];
				}
			}
			
		}
		
		foreach($ex_mc_id as $mc_id)
		{
			// program array loop
			foreach($program_data_arr as $prog_no=>$prog_data)
			{
				?>
				<style type="text/css">
				.page_break	{ page-break-after: always;
				}
			</style>
			<div style="width:930px;">
				<table width="100%" cellpadding="0" cellspacing="0" >
					<tr>
						<td width="70" align="right"> 
							<img  src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' />
						</td>
						<td>
							<table width="100%" style="margin-top:10px">
								<tr>
									<td width="100%" align="center" style="font-size:20px;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
								</tr>
								<tr>
									<td align="center" style="font-size:14px"><? echo show_company($prog_data['company_id'], '', ''); ?></td> 
								</tr>
								<tr>
									<td width="100%" align="center" style="font-size:16px;"><b><u>Job Card / Knit Card</u></b></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<div style="margin-top:5px; width:930px">
					<table cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table" style="font-size:13px; font-family:'Arial Narrow'">
						<thead height="25">
							<th colspan="2">Program Details</th>
							<th colspan="2" width="200">Job Details</th>
							<th colspan="2" width="200">Yarn/Fabric Details</th>
							<th colspan="2" width="200">M/C Details</th>
							<th colspan="2" width="80">Technical Details</th>
						</thead>
						<tr height="22">
							<td>Program No</td>
							<td><? echo $prog_no; ?></td>
							<td>Buyer</td>
							<td><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
							<td rowspan="4" valign="middle">Yarn Desc</td>
							<td rowspan="4" valign="middle" width="150"><? echo $prog_data['yarn_desc']; ?></td>
							<td>M/C No</td>
							<td><? echo $machine_arr[$mc_id];?></td>
							<td>Stitch Length</td>
							<td width="50"><? echo $prog_data['s_length']; ?></td>
						</tr>
						<tr height="22">
							<td>Program Date</td>
							<td><? echo change_date_format($prog_data['program_date']); ?></td>
							<td>Order</td>
							<td><? echo $prog_data['po_number']; ?></td>
							<td>Dia x Gauge</td>
							<td><? echo $prog_data['machine_dia']."x".$prog_data['machine_gg']; ?></td>
							<td>Draft Ratio</td>
							<td><? echo number_format($prog_data['draft_ratio'],2);?></td>
						</tr>
						<tr height="22">
							<td>Program Qty</td>
							<td><? echo number_format($prog_data['prog_qty'],2);?></td>
							<td>Job No</td>
							<td><? echo $jobNo; ?></td>
							<td>Finished Dia</td>
							<td><? echo $prog_data['fabric_dia']; ?></td>
							<td>M/C RPM</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Target/Shift</td>
							<td>&nbsp;</td>
							<td>Style</td>
							<td><? echo $style; ?></td>
							<td>Fabric Type</td>
							<td width="150"><? echo $prog_data['fabric_desc']; ?></td>
							<td>Grey GSM</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Program TnA Start</td>
							<td><? echo change_date_format($prog_data['start_date']); ?></td>
							<td>Knit TnA Star</td>
							<td><? echo change_date_format($tnaData[$jobNo][$prog_data['po_id']]['task_start_date']); ?></td>
							<td rowspan="2" valign="middle">Yarn Brand:</td>
							<td rowspan="2" valign="middle" width="150"><? echo $prog_data['brand_name']; ?></td>
							<td>FGSM</td>
							<td><? echo $prog_data['gsm_weight']; ?></td>
							<td>Yarn Tension</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Program TnA End</td>
							<td><? echo change_date_format($prog_data['end_date']); ?></td>
							<td>Knit TnA End</td>
							<td><? echo change_date_format($tnaData[$jobNo][$prog_data['po_id']]['task_finish_date']); ?></td>
							<td>Color</td>
							<td><? echo $prog_data['color_id']; ?></td>
							<td>Spreder Dia</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Knitting Coefficient</td>
							<td><? echo $prog_data['co_efficient']; ?></td>
							<td>Knit Party</td>
							<td><? echo $prog_data['knit_factory']; ?></td>
							<td>Yarn Lot:</td>
							<td><? echo $prog_data['lot']; ?></td>
							<td>Counter</td>
							<td>&nbsp;</td>
							<td>Fabric Take-up</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="22">
							<td>Target Qty</td>
							<td>
								<?
								$distribution_qnty = $machin_prog[$mc_id][$prog_no]['distribution_qnty'];
								$targateQty = ($distribution_qnty*$prog_data['co_efficient']);
								echo $targateQty;
								?>

							</td>
							<td>Remarks</td>
							<td colspan="3"><? echo $prog_data['remarks']; ?></td>
							<td>M/C Target QTY</td>
							<td><?php echo $distribution_qnty; ?></td>

							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
					</table>
				</div>
				<div style="margin-top:10px; width:930px;">
					<table  cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table">

						<thead height="25">
							<th width="64" height="20">Date</th>
							<th width="64">Shift</th>
							<th width="68">Order Qty</th>
							<th width="74">No. Or Roll</th>
							<th width="99">Production qty</th>
							<th width="69">Reject qty</th>
							<th width="80">Balance Qty</th>
							<th width="78">Operator Id</th>
							<th width="100">Name</th>
							<th width="66">Signature</th>
							<th width="150">Remarks</th>
						</thead>

						<? $row_count=10;
						for($i=1; $i<=$row_count; $i++)
						{

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if ($i%2==0) $bgcolor_2="#FFFFFF";else $bgcolor_2="#E9F3FF";
							?>
							<tr height="24" bgcolor="<? echo $bgcolor; ?>">
								<td rowspan="3">&nbsp;</td>
								<td align="center" height="24">Shift-A</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr height="24" bgcolor="<? echo $bgcolor_2; ?>">
								<td align="center" height="24">Shift-B</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr height="24" bgcolor="<? echo $bgcolor; ?>">
								<td align="center" height="24">Shift-C</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?
						}
						?>

					</table>
				</div>
				<div style="margin-top:10px; width:920px">
					<table cellspacing="2" cellpadding="2" rules="all" width="100%" style="font-size:14px; font-family:'Arial Narrow'">
						<tbody>
							<tr><td><u><b>বিঃ দ্রঃ</b></u></td></tr>
							<tr><td>* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।</td></tr>
							<tr><td>* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।</td></tr>
							<tr><td>* রোল মার্কিং সঠিকভাবে করতে হবে। </td></tr>
							<tr><td>* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।</td></tr>
							<tr><td>* রোলের গায়ে তারিখ/ অপারেটরের নাম এবং শিফট অবশ্যই লিখতে হবে।</td></tr>
							<tr><td>&nbsp;</td></tr>
						</tbody>
					</table>
				</div>
				<? echo signature_table(119, $prog_data['company_id'], "920px","","20"); ?>
				<div class="page_break">&nbsp;</div>
			</div>
			<?
		}
	}
	exit();
}

if ($action == "knitting_card_print_4") 
{
	echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$program_ids =  $data;
	
	if(!$program_ids)
	{
		echo "Program is not found . ";die;
	}
	


		$sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
		$brand_arr 		= return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
		$company_arr 	= return_library_array("select id,company_name from lib_company", "id", "company_name");
		$imge_arr		=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
		$count_arr 		= return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
		$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
		
		
		
		
		if ($db_type == 0) 		$item_id_cond="group_concat(distinct(b.item_id))";
		else if ($db_type==2) 	$item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";
		
		
		$result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");					
		$machin_prog = array();
		foreach ($result_machin_prog as $row) {
			$machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
		}
		
		$reqsDataArr = array();
		$program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";
		if ($db_type == 0) {
			$reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
		} else {
			$reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
		}
		foreach ($reqsData as $row) {
			$reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			$reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
			$prod_arr[] = $row[csf('prod_id')];
		}

	if(!empty($prod_arr))
	{
		$product_details_arr = array();
		$procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
		$pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 $procuct_cond");
		foreach ($pro_sql as $row) {
			$compos = '';
			if ($row[csf('yarn_comp_percent2nd')] != 0)
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			} 
			else 
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}
			
			$product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
		//$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
			$yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
			$yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
			$yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
			$yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$yarn_details_arr[$row[csf('id')]]['composition'] = $compos;
			$yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
			
		}
	}

	//echo "<pre>";
	//print_r($yarn_details_arr);

	$booking_qnty_arr = array();
	$sql_data = sql_select("select a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty,a.quality_level from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.booking_no,a.quality_level");
	foreach ($sql_data as $row) {
		$booking_qnty_arr[$row[csf('booking_no')]] += $row[csf('grey_fab_qnty')];
		$order_nature_booking_arr[$row[csf('booking_no')]]= $row[csf('quality_level')];
		
	}
	unset($sql_data);

		/*$product_details_array = array();
		$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
		$result = sql_select($sql);

		foreach ($result as $row) 
		{
			$compos = '';
			if ($row[csf('yarn_comp_percent2nd')] != 0)
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			} 
			else 
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}

			$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
			$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
		}
		unset($result);*/
		$order_no = ''; $buyer_name = ''; $knitting_factory = ''; $job_no = '';  $booking_no = ''; $company = '';

		
		$jobNo=""; $poQuantity="";
		$job_data_sql=sql_select("select a.id,a.grouping, a.job_no_mst,a.po_number, sum(a.po_quantity) as poQuantity,b.booking_no,c.style_ref_no from wo_po_break_down a, ppl_planning_entry_plan_dtls b,wo_po_details_master c where a.id=b.po_id and b.dtls_id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst=job_no group by a.id,a.grouping,a.job_no_mst,a.po_number,b.booking_no,c.style_ref_no");	
		//echo "select a.id,a.grouping, a.job_no_mst,a.po_number, sum(a.po_quantity) as poQuantity,b.booking_no,c.style_ref_no from wo_po_break_down a, ppl_planning_entry_plan_dtls b,wo_po_details_master c where a.id=b.po_id and b.dtls_id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst=job_no group by a.id,a.grouping,a.job_no_mst,a.po_number,b.booking_no,c.style_ref_no";
		$po_details= array();
		foreach($job_data_sql as $row)
		{
			$jobNo		= $row[csf('job_no_mst')];
			$poQuantity	= $row[csf('poQuantity')];
			$style 		= $row[csf('style_ref_no')];
			$po_details[$row[csf('id')]]['po_number']= $row[csf('po_number')];
			$ref_no 	= $row[csf('grouping')];
			$order_nature=$order_nature_booking_arr[$row[csf('booking_no')]];
		}
		//echo $order_nature.'XXXXXXX';
		
		$data_sql="select a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id";
		//, b.yarn_desc
		$dataArray = sql_select($data_sql);
		 
		$program_data_arr=array();
		
		$sql = "select count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=".$dataArray[0][csf('id')]." order by seq_no";
		$data_array = sql_select($sql);
		$count_feeding_data="";
		foreach ($data_array as $row) {
			//$count_feeding_data_arr[]=$row[csf('count_id')].'_'.$row[csf('feeding_id')];
			if($count_feeding_data !="") $count_feeding_data .=",";
			$count_feeding_data .= $count_arr[$row[csf('count_id')]].'-'.$feeding_arr[$row[csf('feeding_id')]];
		}
		
		
		$company_id = ''; $buyer_name = ''; $booking_no = '';
		$orderNo = "";
		foreach ($dataArray as $row)
		{
			$knitting_factory='';
			if ($row[csf('knitting_source')] == 1)
				$knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
			else if ($row[csf('knitting_source')] == 3)
				$knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

			$yarn_desc=''; $lot_no=""; $brand_name="";$yarn_dtls="";
			if($orderNo=="")
			{
				$orderNo .= $row[csf('po_id')];
				$po_number .= $po_details[$row[csf('po_id')]]['po_number'];
			}else {
				$orderNo .= ",".$row[csf('po_id')];
				$po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
			}
			

			$prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
			foreach ($prod_id as $val) {
				$yarn_desc .= $product_details_arr[$val]['desc'] . ", ";
				$lot_no .= $product_details_arr[$val]['lot'] . ", ";
				$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ", ";
				//$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
				$yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
			}

			$yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
			$lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
			$brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
			$ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

			/*$machine_name="";
			foreach($ex_mc_id as $mc_id)
			{
				if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
			}*/

			$color_name="";
			$ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
			foreach($ex_color_id as $color_id)
			{
				if($color_name=='') $color_name=$color_library[$color_id]; else $color_name.=','.$color_library[$color_id];
			}

			$program_data_arr[$row[csf('id')]]['po_number']=$po_number;
			$program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
			$program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
			$program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
			$program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
			$program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
			$program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
			$program_data_arr[$row[csf('id')]]['color_id']=$color_name;
			//$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
			$program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
			$program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
			$program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
			$program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
			$program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
			$program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]];
			$program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];


			$program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
			$program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
			$program_data_arr[$row[csf('id')]]['lot']= $lot_no;
			$program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
			$program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
			$program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
			$program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
			$program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
			$program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
			$program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
			$program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
			$program_data_arr[$row[csf('id')]]['advice']=$row[csf('advice')];
			$program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
			$program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
		}
		unset($dataArray);

		if($orderNo!="")
		{
			$sql_tna = sql_select("select job_no,po_number_id,task_start_date,task_finish_date FROM tna_process_mst WHERE status_active=1 AND is_deleted=0 and po_number_id in ($orderNo) and task_number=60");
			$tnaData = array();

			if(!empty($sql_tna))
			{
				foreach($sql_tna as $tna_row)
				{
					$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_start_date'] =  $tna_row[csf('task_start_date')];

					$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_finish_date'] =  $tna_row[csf('task_finish_date')];
				}
			}
			
		}
		//$knit_id_arr = return_library_array("select a.requisition_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.id in($program_ids) group by a.requisition_no", "requisition_no", "requisition_no");
		//echo "<pre>";
		//print_r($ex_mc_id);
		
		foreach($ex_mc_id as $mc_id)
		{
			// program array loop
			foreach($program_data_arr as $prog_no=>$prog_data)
			{
			?>
			<style type="text/css">
				.page_break	{ page-break-after: always;
				}
				#font_size_define{
					font-size:14px; 
					font-family:'Arial Narrow';
				}
				.font_size_define{
					font-size:14px; 
					font-family:'Arial Narrow';
				}
				#dataTable tbody tr span{
					 opacity:0.2;
					 color:gray;
				}
				#dataTable tbody tr{
					vertical-align:middle;
				}
			</style>
			<div style="width:930px;">
				<table width="100%" cellpadding="0" cellspacing="0" >
					<tr>
						<td width="70" align="right"> 
							<img  src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' />
						</td>
						<td>
							<table width="100%" style="margin-top:10px">
								<tr>
									<td width="100%" align="center" style="font-size:20px;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
								</tr>
								<tr>
									<td align="center" style="font-size:14px"><? echo show_company($prog_data['company_id'], '', ''); ?></td> 
								</tr>
								<tr>
									<td width="100%" align="center" style="font-size:16px;"><b><u>Knit Card</u></b> <b style=" float:right;color:#000"><? if($fbooking_order_nature[$order_nature]) echo "(".$fbooking_order_nature[$order_nature].")".'&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp;';else echo " ";?></b></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<div style="margin-top:5px; width:930px">
					<table cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table" style="" id="dataTable">
						<thead height="25">
							<th colspan="2" width="230" id="font_size_define">Program Details</th>
							<th colspan="2" width="233" id="font_size_define">Job Details</th>
							<th colspan="2" width="233" id="font_size_define">M/C Details</th>
							<th colspan="2" width="233" id="font_size_define">Technical Details</th>
						</thead>
                        <tbody>
                            <tr height="22">
                                <td width="100" class="font_size_define">Program No</td>
                                <td width="132" class="font_size_define" align="center"><? echo $prog_no; ?></td>
                                <td width="100" class="font_size_define">Buyer</td>
                                <td width="132" class="font_size_define" align="center"><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
                                <td width="100" class="font_size_define">M/C No</td>
                                <td width="132" class="font_size_define" align="center"><? echo $machine_arr[$mc_id];?></td>
                                <td width="100" class="font_size_define">Stitch Length</td>
                                <td width="132" class="font_size_define" align="center"><? echo $prog_data['s_length']; ?></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Program Date</td>
                                <td class="font_size_define" align="center"><? echo change_date_format($prog_data['program_date']); ?></td>
                                <td class="font_size_define">Internal Ref. No</td>
                                <td class="font_size_define" align="center"><? echo ($ref_no)? $ref_no : "" ; ?></td>
                                <td class="font_size_define">Dia x Gauge</td>
                                <td class="font_size_define" align="center"><? echo $prog_data['machine_dia']." x ".$prog_data['machine_gg']; ?></td>
                                <td class="font_size_define">Spandex Stich Lenth</td>
                                <td class="font_size_define" align="center"><? echo $prog_data['spandex_stitch_length'];?></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Program Qty</td>
                                <td class="font_size_define" align="center"><? echo number_format($prog_data['prog_qty'],2);?></td>
                                <td class="font_size_define">Knit Party</td>
                                <td class="font_size_define"><? echo $prog_data['knit_factory']; ?></td>
                                <td class="font_size_define">Finished Dia</td>
                                <td class="font_size_define" align="center"><? echo $prog_data['fabric_dia']." "."[".$fabric_typee[$prog_data['width_dia_type']]."]"; ?></td>
                                <td class="font_size_define">M/C RPM</td>
                                <td class="font_size_define"><span>Write&nbsp;</span></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Target/Shift</td>
                                <td class="font_size_define"><span>Write&nbsp;</span></td>
                                <td class="font_size_define">Y.Requsition</td>
                                <td class="font_size_define" align="center"><? echo $reqsDataArr[$prog_no]['reqs_no'];//implode(",",$knit_id_arr) ; ?></td>
                                <td class="font_size_define">Fabric Type</td>
                                <td class="font_size_define"><? echo $prog_data['fabric_desc']; ?></td>
                                <td class="font_size_define">Counter</td>
                                <td class="font_size_define"><span>Write&nbsp;</span></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Target Qty</td>
                                <td class="font_size_define" align="center">
                                    <?
                                    $distribution_qnty = $machin_prog[$mc_id][$prog_no]['distribution_qnty'];
                                    $targateQty = ($distribution_qnty*$prog_data['co_efficient']);
                                    echo $targateQty;
                                    ?>
                                </td>
                                <td class="font_size_define">Count Feeding</td>
                                <td class="font_size_define"><? echo $count_feeding_data; ?></td>
                                <td class="font_size_define">FGSM</td>
                                <td class="font_size_define" align="center"><? echo $prog_data['gsm_weight']; ?></td>
                                <td class="font_size_define">Feeder</td>
                                <td class="font_size_define"><? echo $feeder[$prog_data['feeder']]; ?></td>
                            </tr>
                            <tr height="22">
                                <td class="font_size_define">Fab. Color</td>
                                <td colspan="3" class="font_size_define"><? echo $prog_data['color_id']; ?></td>
                                <td class="font_size_define">M/C Prog. Qty.</td>
                                <td colspan="3" class="font_size_define"><? echo $machin_prog[$mc_id][$prog_no]['distribution_qnty']; ?>&nbsp;</td>
                            </tr>
                            <tr height="50">
                                <td class="font_size_define">Yarn Details</td>
                                <td colspan="3" class="font_size_define"><? echo $yarn_dtls; ?></td>
                                <td class="font_size_define">Advice</td>
                                <td colspan="3" class="font_size_define"><? echo $prog_data['advice']; ?></td>
                            </tr>
                            <tr height="50">
                                <td class="font_size_define">Technical Instruction [Hand Writing]</td>
                                <td colspan="7" class="font_size_define"><span>Write&nbsp;</span></td>
                            </tr>
                        </tbody>
					</table>
				</div>
				<div style="margin-top:10px; width:920px">
					<table cellspacing="2" cellpadding="2" rules="" width="100%">
						<tbody>
							<tr><td class="font_size_define"><u><b>বিঃ দ্রঃ</b></u></td></tr>
							<tr><td class="font_size_define">* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।</td></tr>
							<tr><td class="font_size_define">* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।</td></tr>
							<tr><td class="font_size_define">* রোল মার্কিং সঠিকভাবে করতে হবে। </td></tr>
							<tr><td class="font_size_define">* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।</td></tr>
							<tr><td class="font_size_define">* রোলের গায়ে তারিখ/ অপারেটরের নাম এবং শিফট অবশ্যই লিখতে হবে।</td></tr>
							<tr><td class="font_size_define">&nbsp;</td></tr>
						</tbody>
					</table>
				</div>
				<? echo signature_table(119, $prog_data['company_id'], "920px","","20"); ?>
				<div class="page_break">&nbsp;</div>
			</div>
			<?
		}
	}
	exit();
}

if ($action == "knitting_card_print_5") 
{
	echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	// $data = explode('**', $data);
	//$typeForAttention = $data[1];
	$program_ids =  $data;


	$sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$imge_arr=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

	if ($db_type == 0) $item_id_cond="group_concat(distinct(b.item_id))";
	else if ($db_type==2) $item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";
	
	if($program_ids)
	{
		$result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");					
		$machin_prog = array();
		foreach ($result_machin_prog as $row) {
			$machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
		}
	}
	
	//=============================================================================================================

	if($program_ids)
	{
		$reqsDataArr = array();
		$program_cond2 = ($program_ids)?" and knit_id in(".$program_ids.")":"";
		if ($db_type == 0) {
			$reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
		} else {
			$reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
		}
		foreach ($reqsData as $row) {
		//$reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
			$reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
		//$reqsDataArr[$row[csf('knit_id')]]['yarn_req_qnty'] = $row[csf('yarn_req_qnty')];
		//$requisition_no_arr[] = $row[csf('reqs_no')];
			$prod_arr[] = $row[csf('prod_id')];
		}
	}

	if(!empty($prod_arr))
	{
		$product_details_arr = array();
		$procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
		$pro_sql = sql_select("select id, product_name_details, lot,brand, supplier_id from product_details_master where item_category_id=1 $procuct_cond");
		foreach ($pro_sql as $row) {
			$product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
	//$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
		}
	}



	//===========================================================================================================
	$booking_qnty_arr = array();
	$sql_data = sql_select("select a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
	foreach ($sql_data as $row) {
		$booking_qnty_arr[$row[csf('booking_no')]]= $row[csf('grey_fab_qnty')];
	}
	unset($sql_data);

	/*$product_details_array = array();
	$sql = "select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 and status_active=1 and is_deleted=0";
	$result = sql_select($sql);

	foreach ($result as $row) 
	{
		$compos = '';
		if ($row[csf('yarn_comp_percent2nd')] != 0)
		{
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
		} 
		else 
		{
			$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
		}

		$product_details_array[$row[csf('id')]]['desc'] = $count_arr[$row[csf('yarn_count_id')]] . " " . $compos . " " . $yarn_type[$row[csf('yarn_type')]];
		$product_details_array[$row[csf('id')]]['lot'] = $row[csf('lot')];
	}
	unset($result);*/
	$order_no = ''; $buyer_name = ''; $knitting_factory = ''; $job_no = '';  $booking_no = ''; $company = '';

	//$sql_req_lot = sql_select("select b.lot from ppl_yarn_requisition_entry a,product_details_master b where a.knit_id in($program_ids) and a.prod_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ");
	
	$jobNo=""; $poQuantity="";
	$job_data_sql=sql_select("select a.id,a.job_no_mst,a.po_number, sum(a.po_quantity) as poQuantity,c.style_ref_no from wo_po_break_down a, ppl_planning_entry_plan_dtls b,wo_po_details_master c where a.id=b.po_id and b.dtls_id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst=job_no group by a.id,a.job_no_mst,a.po_number,c.style_ref_no");	
	$po_details= array();
	foreach($job_data_sql as $row)
	{
		$jobNo= $row[csf('job_no_mst')];
		$poQuantity=$row[csf('poQuantity')];
		$style = $row[csf('style_ref_no')];
		$po_details[$row[csf('id')]]['po_number']= $row[csf('po_number')];

	}
	
	$data_sql="select a.id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date,a.draft_ratio,a.start_date,a.end_date, a.remarks,a.co_efficient, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.yarn_desc, b.gsm_weight,b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	// echo $data_sql; die;
	$dataArray = sql_select($data_sql); $program_data_arr=array();
	$company_id = ''; $buyer_name = ''; $booking_no = '';
	$orderNo = "";
	foreach ($dataArray as $row)
	{
		$knitting_factory='';
		if ($row[csf('knitting_source')] == 1)
			$knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
		else if ($row[csf('knitting_source')] == 3)
			$knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

		$yarn_desc=''; $lot_no=""; $brand_name="";
		/*$ex_yarn_desc=array_unique(explode(",",$booking_item_array[$row[csf('booking_no')]]));
		foreach($ex_yarn_desc as $prodid)
		{
			if($yarn_desc=='') $yarn_desc=$product_details_array[$prodid]['desc']; else $yarn_desc.=','.$product_details_array[$prodid]['desc'];
			if($lot_no=='') $lot_no=$product_details_array[$prodid]['lot']; else $lot_no.=','.$product_details_array[$prodid]['lot'];
		}*/
		if($orderNo=="")
		{
			$orderNo .= $row[csf('po_id')];
			$po_number .= $po_details[$row[csf('po_id')]]['po_number'];
		}else {
			$orderNo .= ",".$row[csf('po_id')];
			$po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
		}
		

		$prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
		foreach ($prod_id as $val) {
			$yarn_desc .= $product_details_arr[$val]['desc'] . ",";
			$lot_no .= $product_details_arr[$val]['lot'] . ",";
			$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ",";
		}

		$yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
		$lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
		$brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
		$ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

		/*$machine_name="";
		foreach($ex_mc_id as $mc_id)
		{
			if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
		}*/

		$color_name="";
		$ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
		foreach($ex_color_id as $color_id)
		{
			if($color_name=='') $color_name=$color_library[$color_id]; else $color_name.=','.$color_library[$color_id];
		}

		$program_data_arr[$row[csf('id')]]['po_number']=$po_number;
		$program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
		$program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
		$program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
		$program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
		$program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
		$program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
		$program_data_arr[$row[csf('id')]]['color_id']=$color_name;
		$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
		$program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
		$program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
		$program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
		$program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
		$program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]];
		$program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];

		$program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
		$program_data_arr[$row[csf('id')]]['lot']= $lot_no;
		$program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
		$program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
		$program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
		$program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
		$program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
		$program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
		$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
	}
	unset($dataArray);

	if($orderNo!="")
	{
		$sql_tna = sql_select("select job_no,po_number_id,task_start_date,task_finish_date FROM tna_process_mst WHERE status_active=1 AND is_deleted=0 and po_number_id in ($orderNo) and task_number=60");
		$tnaData = array();

		if(!empty($sql_tna))
		{
			foreach($sql_tna as $tna_row)
			{
				$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_start_date'] =  $tna_row[csf('task_start_date')];

				$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_finish_date'] =  $tna_row[csf('task_finish_date')];
			}
		}
		
	}
	
	foreach($ex_mc_id as $mc_id)
	{
		// program array loop
		foreach($program_data_arr as $prog_no=>$prog_data)
		{
			?>
			<style type="text/css">
			.page_break	{ page-break-after: always;
			}
		</style>
		<div style="width:930px;">
			<table width="100%" cellpadding="0" cellspacing="0" >
				<tr>
					<td width="70" align="right"> 
						<img  src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' />
					</td>
					<td>
						<table width="100%" style="margin-top:10px">
							<tr>
								<td width="100%" align="center" style="font-size:20px;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
							</tr>
							<tr>
								<td align="center" style="font-size:14px"><? echo show_company($prog_data['company_id'], '', ''); ?></td> 
							</tr>
							<tr>
								<td width="100%" align="center" style="font-size:16px;"><b><u>Job Card / Knit Card</u></b></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
			<div style="margin-top:5px; width:930px">
				<table cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table" style="font-size:13px; font-family:'Arial Narrow'">
				   <thead height="25">
						<th colspan="2">Program Details</th>
						<th colspan="2" width="200">Job Details</th>
						<th colspan="2" width="200">Yarn/Fabric Details</th>
						<th colspan="2" width="200">M/C Details</th>
						<th colspan="2" width="80">Technical Details</th>
					</thead>
					<tr height="22">
						<td>Program No</td>
						<td><? echo $prog_no; ?></td>
						<td>Buyer</td>
						<td><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
						<td rowspan="4" valign="middle">Yarn Desc</td>
						<td rowspan="4" valign="middle" width="150"><? echo $prog_data['yarn_desc']; ?></td>
						<td>M/C No</td>
						<td><? echo $machine_arr[$mc_id];?></td>
						<td>Stitch Length</td>
						<td width="50"><? echo $prog_data['s_length']; ?></td>
					</tr>
					<tr height="22">
						<td>Program Date</td>
						<td><? echo change_date_format($prog_data['program_date']); ?></td>
						<td>Order</td>
						<td><? echo $prog_data['po_number']; ?></td>
						<td>Dia x Gauge</td>
						<td><? echo $prog_data['machine_dia']."x".$prog_data['machine_gg']; ?></td>
						<td>Draft Ratio</td>
						<td><? echo number_format($prog_data['draft_ratio'],2);?></td>
					</tr>
					<tr height="22">
						<td>Program Qty</td>
						<td><? echo number_format($prog_data['prog_qty'],2);?></td>
						<td>Job No</td>
						<td><? echo $jobNo; ?></td>
						<td>Finished Dia</td>
						<td><? echo $prog_data['fabric_dia']; ?></td>
						<td>M/C RPM</td>
						<td>&nbsp;</td>
					</tr>
					<tr height="22">
						<td>Target/Shift</td>
						<td>&nbsp;</td>
						<td>Style</td>
						<td><? echo $style; ?></td>
						<td>Fabric Type</td>
						<td width="150"><? echo $prog_data['fabric_desc']; ?></td>
						<td>Grey GSM</td>
						<td>&nbsp;</td>
					</tr>
					<tr height="22">
						<td>Program TnA Start</td>
						<td><? echo change_date_format($prog_data['start_date']); ?></td>
						<td>Knit TnA Star</td>
						<td><? echo change_date_format($tnaData[$jobNo][$prog_data['po_id']]['task_start_date']); ?></td>
						<td rowspan="2" valign="middle">Yarn Brand:</td>
						<td rowspan="2" valign="middle" width="150"><? echo $prog_data['brand_name']; ?></td>
						<td>FGSM</td>
						<td><? echo $prog_data['gsm_weight']; ?></td>
						<td>Yarn Tension</td>
						<td>&nbsp;</td>
					</tr>
					<tr height="22">
						<td>Program TnA End</td>
						<td><? echo change_date_format($prog_data['end_date']); ?></td>
						<td>Knit TnA End</td>
						<td><? echo change_date_format($tnaData[$jobNo][$prog_data['po_id']]['task_finish_date']); ?></td>
						<td>Color</td>
						<td><? echo $prog_data['color_id']; ?></td>
						<td>Spreder Dia</td>
						<td>&nbsp;</td>
					</tr>
					<tr height="22">
						<td>Knitting Coefficient</td>
						<td><? echo $prog_data['co_efficient']; ?></td>
						<td>Knit Party</td>
						<td><? echo $prog_data['knit_factory']; ?></td>
						<td>Yarn Lot:</td>
						<td><? echo $prog_data['lot']; ?></td>
						<td>Counter</td>
						<td>&nbsp;</td>
						<td>Fabric Take-up</td>
						<td>&nbsp;</td>
					</tr>
					<tr height="22">
						<td>Target Qty</td>
						<td>
						<?
						$distribution_qnty = $machin_prog[$mc_id][$prog_no]['distribution_qnty'];
						$targateQty = ($distribution_qnty*$prog_data['co_efficient']);
						echo $targateQty;
						?>
						
						</td>
						<td>Remarks</td>
						<td colspan="3"><? echo $prog_data['remarks']; ?></td>

						<td>M/C Target QTY</td>
						<td><?php echo $distribution_qnty; ?></td>
						
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
			<div style="margin-top:10px; width:930px;">
				<table  cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table">
				   
						<thead height="25">
							<th width="64" height="20">Date</th>
							<th width="64">Shift</th>
							<th width="68">Order Qty</th>
							<th width="74">No. Or Roll</th>
							<th width="99">Production qty</th>
							<th width="69">Reject qty</th>
							<th width="80">Balance Qty</th>
							<th width="78">Operator Id</th>
							<th width="100">Name</th>
							<th width="66">Signature</th>
							<th width="150">Remarks</th>
						</thead>

						<? $row_count=10;
						for($i=1; $i<=$row_count; $i++)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
							<tr height="24" bgcolor="<? echo $bgcolor; ?>">
								<td rowspan="2">&nbsp;</td>
								<td align="center" height="24">Shift-A</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>

							<tr height="24">
								<td align="center" height="24">Shift-B</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<?
						}
						?>
				   
				</table>
			</div>
			<div style="margin-top:10px; width:920px">
				<table cellspacing="2" cellpadding="2" rules="all" width="100%" style="font-size:14px; font-family:'Arial Narrow'">
					<tbody>
						<tr><td><u><b>বিঃ দ্রঃ</b></u></td></tr>
						<tr><td>* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।</td></tr>
						<tr><td>* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।</td></tr>
						<tr><td>* রোল মার্কিং সঠিকভাবে করতে হবে। </td></tr>
						<tr><td>* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।</td></tr>
						<tr><td>* রোলের গায়ে তারিখ/ অপারেটরের নাম এবং শিফট অবশ্যই লিখতে হবে।</td></tr>
						<tr><td>&nbsp;</td></tr>
					</tbody>
				</table>
			</div>
			<? echo signature_table(119, $prog_data['company_id'], "920px","","20"); ?>
			<div class="page_break">&nbsp;</div>
		</div>
		<?
	}
	}
	exit();
}

if ($action == "knitting_card_print_6") 
{
	echo load_html_head_contents("Knitting Card Info", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$program_ids =  $data;
	
	if(!$program_ids)
	{
		echo "Program is not found . ";die;
	}

	$sub_subcontract = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$brand_arr 		= return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$company_arr 	= return_library_array("select id,company_name from lib_company", "id", "company_name");
	$imge_arr		=return_library_array( "select master_tble_id, image_location from common_photo_library where form_name='company_details' and file_type=1",'master_tble_id','image_location');
	$count_arr 		= return_library_array("select id, yarn_count from lib_yarn_count where status_active=1 and is_deleted=0 ", 'id', 'yarn_count');
	$color_library = return_library_array("select id,color_name from lib_color", "id", "color_name");
	
	if ($db_type == 0) 		$item_id_cond="group_concat(distinct(b.item_id))";
	else if ($db_type==2) 	$item_id_cond="LISTAGG(b.item_id, ',') WITHIN GROUP (ORDER BY b.item_id)";
	
	
	$result_machin_prog = sql_select("SELECT machine_id,dtls_id,distribution_qnty from ppl_planning_info_machine_dtls WHERE DTLS_ID IN($program_ids)");					
	$machin_prog = array();
	foreach ($result_machin_prog as $row) {
		$machin_prog[$row[csf('machine_id')]][$row[csf('dtls_id')]]['distribution_qnty'] = $row[csf('distribution_qnty')];
	}

	$color_prog_sql = "select plan_id, program_no, color_id, sum(color_prog_qty) as color_prog_qty from ppl_color_wise_break_down where status_active=1 and is_deleted=0 and program_no in($program_ids) group by plan_id, program_no, color_id";
	$color_prog_data = sql_select($color_prog_sql);
	
	$color_prog_arr = array();
	foreach($color_prog_data as $row)
	{
		$color_prog_arr[$row[csf('program_no')]][$row[csf('color_id')]] = $row[csf('color_prog_qty')];
	}
	
	$reqsDataArr = array();
	$program_cond2 = ($program_ids) ? " and knit_id in(".$program_ids.")" : "";
	if ($db_type == 0) {
		$reqsData = sql_select("select knit_id, requisition_no as reqs_no, group_concat(distinct(prod_id)) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id");
	} else {
		$reqsData = sql_select("select knit_id, max(requisition_no) as reqs_no, LISTAGG(prod_id, ',') WITHIN GROUP (ORDER BY prod_id) as prod_id , sum(yarn_qnty) as yarn_req_qnty from ppl_yarn_requisition_entry where status_active=1 and is_deleted=0 $program_cond2 group by knit_id,requisition_no");
	}
	foreach ($reqsData as $row) {
		$reqsDataArr[$row[csf('knit_id')]]['reqs_no'] = $row[csf('reqs_no')];
		$reqsDataArr[$row[csf('knit_id')]]['prod_id'] = $row[csf('prod_id')];
		$prod_arr[] = $row[csf('prod_id')];
	}

	if(!empty($prod_arr))
	{
		$product_details_arr = array();
		$procuct_cond = (!empty($prod_arr))?" and id in(".implode(",",$prod_arr).")":"";
		$pro_sql = sql_select("select id, supplier_id, lot, current_stock, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_count_id, yarn_type, color, brand from product_details_master where item_category_id=1 $procuct_cond");
		foreach ($pro_sql as $row) 
		{
			$compos = '';
			if ($row[csf('yarn_comp_percent2nd')] != 0)
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]] . " " . $row[csf('yarn_comp_percent2nd')] . "%";
			} 
			else 
			{
				$compos = $composition[$row[csf('yarn_comp_type1st')]] . " " . $row[csf('yarn_comp_percent1st')] . "%" . " " . $composition[$row[csf('yarn_comp_type2nd')]];
			}
			
			$product_details_arr[$row[csf('id')]]['desc'] = $row[csf('product_name_details')];
			$product_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$product_details_arr[$row[csf('id')]]['brand_name'] = $row[csf('brand')];
			//$product_details_arr[$row[csf('id')]]['supplier'] = $row[csf('supplier_id')];
			$yarn_details_arr[$row[csf('id')]]['yarn_count'] = $count_arr[$row[csf('yarn_count_id')]];
			$yarn_details_arr[$row[csf('id')]]['yarn_type'] = $yarn_type[$row[csf('yarn_type')]];
			$yarn_details_arr[$row[csf('id')]]['brand'] = $brand_arr[$row[csf('brand')]];
			$yarn_details_arr[$row[csf('id')]]['lot'] = $row[csf('lot')];
			$yarn_details_arr[$row[csf('id')]]['composition'] = $compos;
			$yarn_details_arr[$row[csf('id')]]['color'] = $color_library[$row[csf('color')]];
			
		}
	}

	//echo "<pre>";
	//print_r($yarn_details_arr);

	$booking_qnty_arr = array();
	$sql_data = sql_select("select a.booking_no, sum(b.grey_fab_qnty ) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category=2 and a.fabric_source=1 and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 group by a.booking_no");
	foreach ($sql_data as $row) {
		$booking_qnty_arr[$row[csf('booking_no')]] += $row[csf('grey_fab_qnty')];
	}
	unset($sql_data);

	$order_no = ''; $buyer_name = ''; $knitting_factory = ''; $job_no = '';  $booking_no = ''; $company = '';

		
	$jobNo=""; $poQuantity="";
	$job_data_sql=sql_select("select a.id,a.grouping, a.job_no_mst,a.po_number, sum(a.po_quantity) as poQuantity,c.style_ref_no from wo_po_break_down a, ppl_planning_entry_plan_dtls b,wo_po_details_master c where a.id=b.po_id and b.dtls_id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.job_no_mst=job_no group by a.id,a.grouping,a.job_no_mst,a.po_number,c.style_ref_no");	
	$po_details= array();
	foreach($job_data_sql as $row)
	{
		$jobNo		= $row[csf('job_no_mst')];
		$poQuantity	= $row[csf('poQuantity')];
		$style 		= $row[csf('style_ref_no')];
		$po_details[$row[csf('id')]]['po_number']= $row[csf('po_number')];
		$ref_no 	= $row[csf('grouping')];
	}
		
	$data_sql="select a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id and a.id in ($program_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.mst_id, a. knitting_source, a.knitting_party, a.subcontract_party, a.machine_id, a.machine_gg, a.machine_dia, a.color_id, a.program_qnty, a.stitch_length, a.fabric_dia, a.program_date, a.draft_ratio, a.start_date, a.end_date, a.remarks, a.co_efficient, a.spandex_stitch_length, a.feeder, a.advice, a.width_dia_type, b.buyer_id, b.booking_no, b.company_id, b.fabric_desc, b.gsm_weight, b.po_id";
		//, b.yarn_desc
		$dataArray = sql_select($data_sql);
		 
		$program_data_arr=array();
		
		$sql = "select count_id,feeding_id from ppl_planning_count_feed_dtls where dtls_id=".$dataArray[0][csf('id')]." order by seq_no";
		$data_array = sql_select($sql);
		$count_feeding_data="";
		foreach ($data_array as $row) {
			//$count_feeding_data_arr[]=$row[csf('count_id')].'_'.$row[csf('feeding_id')];
			if($count_feeding_data !="") $count_feeding_data .=",";
			$count_feeding_data .= $count_arr[$row[csf('count_id')]].'-'.$feeding_arr[$row[csf('feeding_id')]];
		}
			
		$company_id = ''; $buyer_name = ''; $booking_no = '';
		$orderNo = "";
		foreach ($dataArray as $row)
		{
			$knitting_factory='';
			if ($row[csf('knitting_source')] == 1)
				$knitting_factory = $company_arr[$row[csf('knitting_party')]] . ",";
			else if ($row[csf('knitting_source')] == 3)
				$knitting_factory = $supplier_details[$row[csf('knitting_party')]] . ",";

			$yarn_desc=''; $lot_no=""; $brand_name="";$yarn_dtls="";
			if($orderNo=="")
			{
				$orderNo .= $row[csf('po_id')];
				$po_number .= $po_details[$row[csf('po_id')]]['po_number'];
			}else {
				$orderNo .= ",".$row[csf('po_id')];
				$po_number .= ",".$po_details[$row[csf('po_id')]]['po_number'];
			}
			

			$prod_id = array_unique(explode(",", $reqsDataArr[$row[csf('id')]]['prod_id']));
			foreach ($prod_id as $val) {
				$yarn_desc .= $product_details_arr[$val]['desc'] . ", ";
				$lot_no .= $product_details_arr[$val]['lot'] . ", ";
				$brand_name .= $brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . ", ";
				//$yarn_dtls .= $product_details_arr[$val]['desc'] . ",".$product_details_arr[$val]['lot'] . ",".$brand_arr[$product_details_arr[$val]['brand_name']].'('.$product_details_arr[$val]['lot'].')' . "<br>";
				$yarn_dtls .= $yarn_details_arr[$val]['yarn_count'] . ", ".$yarn_details_arr[$val]['composition'] . ", ".$yarn_details_arr[$val]['yarn_type'].", ".$yarn_details_arr[$val]['color'].', '.$yarn_details_arr[$val]['brand'].", ".$yarn_details_arr[$val]['lot'] . "<br>";
			}

			$yarn_desc = implode(",",array_filter(array_unique(explode(",", substr($yarn_desc, 0, -1)))));
			$lot_no = implode(",",array_filter(array_unique(explode(",", substr($lot_no, 0, -1)))));
			$brand_name = implode(",",array_filter(array_unique(explode(",", substr($brand_name, 0, -1)))));
			$ex_mc_id=array_unique(explode(",",$row[csf('machine_id')]));

			/*$machine_name="";
			foreach($ex_mc_id as $mc_id)
			{
				if($machine_name=='') $machine_name=$machine_arr[$mc_id]; else $machine_name.=','.$machine_arr[$mc_id];
			}*/

			$color_name="";
			$ex_color_id=array_unique(explode(",",$row[csf('color_id')]));
			foreach($ex_color_id as $color_id)
			{
				if($color_name=='') $color_name=$color_library[$color_id]; else $color_name.=','.$color_library[$color_id];
			}

			$program_data_arr[$row[csf('id')]]['po_number']=$po_number;
			$program_data_arr[$row[csf('id')]]['co_efficient']=$row[csf('co_efficient')];
			$program_data_arr[$row[csf('id')]]['draft_ratio']=$row[csf('draft_ratio')];
			$program_data_arr[$row[csf('id')]]['start_date']=$row[csf('start_date')];
			$program_data_arr[$row[csf('id')]]['end_date']=$row[csf('end_date')];
			$program_data_arr[$row[csf('id')]]['machine_dia']=$row[csf('machine_dia')];
			$program_data_arr[$row[csf('id')]]['machine_gg']=$row[csf('machine_gg')];
			$program_data_arr[$row[csf('id')]]['color_id']=$color_name;
			//$program_data_arr[$row[csf('id')]]['prog_qty']+=$row[csf('program_qnty')];
			$program_data_arr[$row[csf('id')]]['prog_qty']=$row[csf('program_qnty')];
			$program_data_arr[$row[csf('id')]]['s_length']=$row[csf('stitch_length')];
			$program_data_arr[$row[csf('id')]]['fabric_dia']=$row[csf('fabric_dia')];
			$program_data_arr[$row[csf('id')]]['program_date']=$row[csf('program_date')];
			$program_data_arr[$row[csf('id')]]['fabric_desc']=$row[csf('fabric_desc')];
			$program_data_arr[$row[csf('id')]]['booking_qty']=$booking_qnty_arr[$row[csf('booking_no')]];
			$program_data_arr[$row[csf('id')]]['remarks']=$row[csf('remarks')];


			$program_data_arr[$row[csf('id')]]['yarn_dtls']= $yarn_dtls;
			$program_data_arr[$row[csf('id')]]['yarn_desc']= $yarn_desc;
			$program_data_arr[$row[csf('id')]]['lot']= $lot_no;
			$program_data_arr[$row[csf('id')]]['brand_name']= $brand_name;
			$program_data_arr[$row[csf('id')]]['mc_nmae']= $machine_name;
			$program_data_arr[$row[csf('id')]]['knit_factory']= $knitting_factory;
			$program_data_arr[$row[csf('id')]]['sub_party']= $supplier_details[$row[csf('subcontract_party')]];
			$program_data_arr[$row[csf('id')]]['gsm_weight']=$row[csf('gsm_weight')];
			$program_data_arr[$row[csf('id')]]['booking_no']=$row[csf('booking_no')];
			$program_data_arr[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
			$program_data_arr[$row[csf('id')]]['company_id']=$row[csf('company_id')];
			$program_data_arr[$row[csf('id')]]['spandex_stitch_length']=$row[csf('spandex_stitch_length')];
			$program_data_arr[$row[csf('id')]]['feeder']=$row[csf('feeder')];
			$program_data_arr[$row[csf('id')]]['advice']=$row[csf('advice')];
			$program_data_arr[$row[csf('id')]]['knit_id']=$row[csf('mst_id')];
			$program_data_arr[$row[csf('id')]]['width_dia_type']=$row[csf('width_dia_type')];
		}
		unset($dataArray);

		if($orderNo!="")
		{
			$sql_tna = sql_select("select job_no,po_number_id,task_start_date,task_finish_date FROM tna_process_mst WHERE status_active=1 AND is_deleted=0 and po_number_id in ($orderNo) and task_number=60");
			$tnaData = array();

			if(!empty($sql_tna))
			{
				foreach($sql_tna as $tna_row)
				{
					$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_start_date'] =  $tna_row[csf('task_start_date')];

					$tnaData[$tna_row[csf('job_no')]][$tna_row[csf('po_number_id')]]['task_finish_date'] =  $tna_row[csf('task_finish_date')];
				}
			}
			
		}
		//$knit_id_arr = return_library_array("select a.requisition_no from ppl_yarn_requisition_entry a, ppl_planning_info_entry_dtls b where a.knit_id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.id in($program_ids) group by a.requisition_no", "requisition_no", "requisition_no");
		//echo "<pre>";
		//print_r($ex_mc_id);
		
		foreach($ex_mc_id as $mc_id)
		{
			// program array loop
			foreach($program_data_arr as $prog_no=>$prog_data)
			{
			?>
			<style type="text/css">
				.page_break	{ page-break-after: always;
				}
				#font_size_define{
					font-size:14px; 
					font-family:'Arial Narrow';
				}
				.font_size_define{
					font-size:14px; 
					font-family:'Arial Narrow';
				}
				#dataTable tbody tr span{
					 opacity:0.2;
					 color:gray;
				}
				#dataTable tbody tr{
					vertical-align:middle;
				}
			</style>
			<div style="width:930px;">
				<table width="100%" cellpadding="0" cellspacing="0" >
					<tr>
						<td width="70" align="right"> 
							<img  src='../../<? echo $imge_arr[str_replace("'","",$prog_data['company_id'])]; ?>' height='100%' width='100%' />
						</td>
						<td>
							<table width="100%" style="margin-top:10px">
								<tr>
									<td width="100%" align="center" style="font-size:20px;"><b><? echo $company_arr[$prog_data['company_id']]; ?></b></td>
								</tr>
								<tr>
									<td align="center" style="font-size:14px"><? echo show_company($prog_data['company_id'], '', ''); ?></td> 
								</tr>
								<tr>
									<td width="100%" align="center" style="font-size:16px;"><b><u>Knit Card</u></b></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<div style="margin-top:5px; width:930px">

					<table cellspacing="2" cellpadding="2" border="1" rules="all" width="100%" class="rpt_table" style="" id="dataTable">
					  	<thead height="25">
							<th colspan="2" width="230" id="font_size_define">Program Details</th>
							<th colspan="2" width="233" id="font_size_define">Job Details</th>
							<th colspan="2" width="233" id="font_size_define">M/C Details</th>
							<th colspan="2" width="233" id="font_size_define">Technical Details</th>
						</thead>
						<tbody> 	
						  <tr>
						    <td width="100" class="font_size_define" >Program No</td>
						    <td width="132" class="font_size_define" align="center" ><? echo $prog_no; ?></td>
						    <td width="100" class="font_size_define" >Buyer</td>
						    <td width="132" class="font_size_define" align="center"><? echo $buyer_arr[$prog_data['buyer_id']]; ?></td>
						    <td width="100" class="font_size_define" >M/C No</td>
						    <td width="132" class="font_size_define" align="center" ><? echo $machine_arr[$mc_id];?></td>
						    <td width="100" class="font_size_define" >Stitch Length</td>
						    <td width="132" class="font_size_define" align="center" ><? echo $prog_data['s_length']; ?></td>
						  </tr>

						  <tr>
						    <td class="font_size_define" >Program Date</td>
						    <td class="font_size_define" align="center"><? echo change_date_format($prog_data['program_date']); ?></td>
						    <td class="font_size_define">Internal Ref. No</td>
						    <td class="font_size_define" align="center" ><? echo ($ref_no)? $ref_no : "" ; ?></td>
						    <td class="font_size_define" >Dia x Gauge</td>
						    <td class="font_size_define" align="center" ><? echo $prog_data['machine_dia']." x ".$prog_data['machine_gg']; ?></td>
						    <td class="font_size_define" >Spandex Stich Lenth</td>
						    <td class="font_size_define" align="center"><? echo $prog_data['spandex_stitch_length'];?></td>
						  </tr>

						  <tr>
						    <td class="font_size_define" >Program Qty</td>
						    <td class="font_size_define" align="center" ><? echo number_format($prog_data['prog_qty'],2);?></td>
						    <td class="font_size_define">Knit Party</td>
						    <td class="font_size_define" ><? echo $prog_data['knit_factory']; ?></td>
						    <td class="font_size_define">M/C Prog. Qty.</td>
						    <td class="font_size_define" ><? echo $machin_prog[$mc_id][$prog_no]['distribution_qnty']; ?></td>
						    <td class="font_size_define" >M/C RPM</td>
						    <td><span>Write&nbsp;</span></td>
						  </tr>

						  <tr>
						    <td class="font_size_define">Target/Shift</td>
						    <td class="font_size_define" ><span>Write&nbsp;</span></td>
						    <td class="font_size_define">Y.Requsition</td>
						    <td class="font_size_define" align="center"><? echo $reqsDataArr[$prog_no]['reqs_no']; ?></td>
						    <td class="font_size_define" >Finished Dia</td>
						    <td class="font_size_define" align="center" ><? echo $prog_data['fabric_dia']." "."[".$fabric_typee[$prog_data['width_dia_type']]."]"; ?></td>
						    <td class="font_size_define" >Counter</td>
						   	<td class="font_size_define"><span>Write&nbsp;</span></td>
						  </tr>

						  <tr>
						    <td class="font_size_define" rowspan="2">Target Qty</td>
						    <td class="font_size_define" rowspan="2" align="center">
						    	<?
                                $distribution_qnty = $machin_prog[$mc_id][$prog_no]['distribution_qnty'];
                                $targateQty = ($distribution_qnty*$prog_data['co_efficient']);
                                echo $targateQty;
                                ?>
						    </td>
						    <td class="font_size_define" rowspan="2">Count Feeding</td>
						    <td class="font_size_define" rowspan="2"><? echo $count_feeding_data; ?></td>
						    <td class="font_size_define" >Fabric Type</td>
						    <td class="font_size_define" ><? echo $prog_data['fabric_desc']; ?></td>
						    <td class="font_size_define" >Feeder</td>
						    <td><? echo $feeder[$prog_data['feeder']]; ?></td>
						  </tr>

						  <tr>
						    <td class="font_size_define" >FGSM</td>
						    <td class="font_size_define" align="center"><? echo $prog_data['gsm_weight']; ?></td>
						    <td>&nbsp;</td>
						    <td>&nbsp;</td>
						  </tr>

						  <tr>
						    <td rowspan="2" class="font_size_define" >Yarn Details</td>
						    <td colspan="3" class="font_size_define" ><? echo $yarn_dtls; ?></td>
						    <td rowspan="2" class="font_size_define" >Advice</td>
						    <td colspan="3" rowspan="2" class="font_size_define"><? echo $prog_data['advice']; ?></td>
						  </tr>
						  
						  <tr>
						    <td colspan="3" class="font_size_define"><span>Write&nbsp;</span></td>
						  </tr>
						
						<? 
						foreach ($color_prog_arr as $progNo=>$colorArr) 
						{
							$colorRowspan =  count($colorArr)+1;
							?>
							<tr>
							    <td class="font_size_define">Fab. Color</td>
							    <td class="font_size_define" >Prg. Qnty</td>

							    <td rowspan="<? echo $colorRowspan;?>">Technical Instruction [Hand Writing]</td>
							    <td colspan="5" rowspan="<? echo $colorRowspan;?>"><span>Write&nbsp;</span></td>
							</tr>
							<?
							foreach ($colorArr as $colorId=>$qty) {

							?>
					  		<tr>
					    		<td class="font_size_define"><? echo $color_library[$colorId];?></td>
					    		<td class="font_size_define" align="center"><? echo $qty;?></td>
					    	</tr>
					    	<?
					    	}
						}  
						?>
						</tbody>
					</table>

				</div>
				<div style="margin-top:10px; width:920px">
					<table cellspacing="2" cellpadding="2" rules="" width="100%">
						<tbody>
							<tr><td class="font_size_define"><u><b>বিঃ দ্রঃ</b></u></td></tr>
							<tr><td class="font_size_define">* প্রোগ্রামের শেষ পর্যায়ে অবশ্যই সূতা মিল করে চালাতে হবে।</td></tr>
							<tr><td class="font_size_define">* সূতা মেশিনে উঠানোর সময় ১/১ করে উঠতে হবে।</td></tr>
							<tr><td class="font_size_define">* রোল মার্কিং সঠিকভাবে করতে হবে। </td></tr>
							<tr><td class="font_size_define">* রোলে কোন প্রকার কাটা-কাটি বা ভুল লেখা চলবেনা।</td></tr>
							<tr><td class="font_size_define">* রোলের গায়ে তারিখ/ অপারেটরের নাম এবং শিফট অবশ্যই লিখতে হবে।</td></tr>
							<tr><td class="font_size_define">&nbsp;</td></tr>
						</tbody>
					</table>
				</div>
				<? echo signature_table(119, $prog_data['company_id'], "920px","","20"); ?>
				<div class="page_break">&nbsp;</div>
			</div>
			<?
		}
	}
	exit();
}

if($action == "knitting_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);

	$machine_arr = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$receive_basis = array(2 => "Knitting Plan",11=>'Service Booking Based');
	?>
	<script>

		var tableFilters = {
			col_operation: {
				id: ["value_receive_qnty_in", "value_receive_qnty_out", "value_receive_qnty_tot"],
				col: [7, 8, 9],
				operation: ["sum", "sum", "sum"],
				write_method: ["innerHTML", "innerHTML", "innerHTML"]
			}
		}
		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1, tableFilters);
		});

		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";

			$('#tbl_list_search tr:first').hide();

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";

			$('#tbl_list_search tr:first').show();
		}

	</script>
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
		<fieldset style="width:1037px;">
			<div id="report_container">
				
				<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="12"><b>Grey Receive Info</b></th>
					</thead>
					<thead>
						<th width="30">SL</th>
						<th width="115">Receive Id</th>
						<th width="95">Receive Basis</th>
						<th width="110">Product Details</th>
						<th width="100">Booking / Program No</th>
						<th width="60">Machine No</th>
						<th width="75">Production Date</th>
						<th width="80">Inhouse Production</th>
						<th width="80">Outside Production</th>
						<th width="80">Production Qnty</th>
						<th width="70">Challan No</th>
						<th>Kniting Com.</th>
					</thead>
				</table>
				<div style="width:1038px; max-height:330px; overflow-y:scroll" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0"
					id="tbl_list_search">
					<?
					$i = 1;
					$total_receive_qnty = 0;
					$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
					$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

					$sql = "select * from (
						select  a.receive_date, a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_company,a.knitting_source,  a.receive_basis, a.challan_no, sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d where a.id=b.mst_id and a.booking_id = d.id and c.id = d.mst_id and a.item_category=13 and a.entry_form=2 and a.receive_basis=2 and a.booking_id = $program_id and a.company_id = $companyID and b.status_active=1 and b.is_deleted=0 group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source,  a.knitting_company, a.challan_no
						union all
						select a.receive_date, a.recv_number,c.booking_no, b.prod_id, b.machine_no_id,a.knitting_company, a.knitting_source,   a.receive_basis, a.challan_no,   sum(b.grey_receive_qnty) as knitting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b , ppl_planning_info_entry_mst c, ppl_planning_info_entry_dtls d where a.id=b.mst_id  and b.program_no = d.id and c.id = d.mst_id and a.item_category=13 and a.entry_form=22 and a.receive_basis=11 and b.status_active=1 and b.is_deleted=0 and b.program_no in($program_id) and a.company_id = $companyID group by a.receive_date,a.receive_basis,a.recv_number,c.booking_no,  b.prod_id,b.machine_no_id, a.knitting_source, a.knitting_company, a.challan_no
					) order by receive_date
					";

					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$total_receive_qnty += $row[csf('knitting_qnty')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="115" align="center"><? echo $row[csf('recv_number')]; ?></td>
							<td width="95" align="center"><? echo $receive_basis[$row[csf('receive_basis')]]; ?></td>
							<td width="110" align="center"><? echo $product_arr[$row[csf('prod_id')]]; ?></td>
							<td width="100" align="center"><? echo $row[csf('booking_no')]; ?></td>
							<td width="60" align="center"><? echo $machine_arr[$row[csf('machine_no_id')]]; ?></td>
							<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
							<td align="right" width="80">
								<?
								if ($row[csf('knitting_source')] != 3) {
									echo number_format($row[csf('knitting_qnty')], 2, '.', '');
									$total_receive_qnty_in += $row[csf('knitting_qnty')];
								} else echo "&nbsp;";
								?>
							</td>
							<td align="right" width="80">
								<?
								if ($row[csf('knitting_source')] == 3) {
									echo number_format($row[csf('knitting_qnty')], 2, '.', '');
									$total_receive_qnty_out += $row[csf('knitting_qnty')];
								} else echo "&nbsp;";
								?>
							</td>
							<td align="right" width="80"><? echo number_format($row[csf('knitting_qnty')], 2, '.', ''); ?></td>
							<td width="70" align="center"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
							<td>
								<p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?></p>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
				</table>
			</div>
			<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
				<tfoot>
					<th width="30">&nbsp;</th>
					<th width="115">&nbsp;</th>
					<th width="95">&nbsp;</th>
					<th width="110">&nbsp;</th>
					<th width="100">&nbsp;</th>
					<th width="60">&nbsp;</th>
					<th width="75" align="right">Total</th>
					<th width="80" align="right"
					id="value_receive_qnty_in"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
					<th width="80" align="right"
					id="value_receive_qnty_out"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
					<th width="80" align="right"
					id="value_receive_qnty_tot"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
					<th width="70">&nbsp;</th>
					<th>&nbsp;</th>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();

}
if ($action == "grey_purchase_delivery") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<div style="width:750px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
	<fieldset style="width:740px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>Grey Delivery Info</b></th>
				</thead>
				<thead>
					<th width="30">SL</th>
					<th width="125">Receive Id</th>
					<th width="150">Product Details</th>
					<th width="75">Production Date</th>
					<th width="80">Delivery Quantity</th>
					<th>Kniting Com.</th>
				</thead>
			</table>
			<div style="width:740px; max-height:330px; overflow-y:scroll" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="720" cellpadding="0" cellspacing="0">
					<?
					$i = 1;
					$total_receive_qnty = 0;
					$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
					$supplier_details = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
					$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');

					$sql = "select c.sys_number,c.knitting_company,c.knitting_source,c.delevery_date, a.booking_no, sum(b.current_delivery)  as quantity, b.product_id from pro_roll_details a,pro_grey_prod_delivery_dtls b, pro_grey_prod_delivery_mst c where a.mst_id=b.grey_sys_id and b.mst_id = c.id and a.barcode_no=b.barcode_num and a.entry_form=2 and a.receive_basis=2 and a.booking_without_order=0 and a.booking_no = '$program_id' and c.company_id = $companyID and b.entry_form = 56 and b.status_active=1 and b.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 group by c.sys_number,c.knitting_company,c.knitting_source,c.delevery_date, a.booking_no, b.product_id order by c.delevery_date";


					$deliveryStorQtyArr = array();
					foreach ($deliveryquantityArr as $row) {
						$deliveryStorQtyArr[$row[csf('booking_no')]] += $row[csf('current_delivery')];
					}

					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$total_receive_qnty += $row[csf('quantity')];
						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="125"><p><? echo $row[csf('sys_number')]; ?></p></td>
							<td width="150"><p><? echo $product_arr[$row[csf('product_id')]]; ?></p></td>
							<td width="75" align="center"><? echo change_date_format($row[csf('delevery_date')]); ?></td>
							<td align="right" width="80">
								<?
								echo number_format($row[csf('quantity')], 2, '.', '');
								$total_receive_qnty_in += $row[csf('quantity')];
								?>
							</td>
							<td>
								<? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?>
							</td>
						</tr>
						<?
						$i++;
					}
					?>
					<tfoot>
						<th colspan="4" align="right">Total</th>
						<th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
						<th>&nbsp;</th>
					</tfoot>
				</table>
			</div>
		</div>
	</fieldset>
	<?
	exit();
}
if ($action == "po_details_action") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<div style="width:335px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
	<fieldset style="width:335px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="330" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>PO Info</b></th>
				</thead>
				<thead>
					<th width="30">SL</th>
					<th width="125">PO Number</th>
					<th>Country Ship Date</th>
				</thead>
			</table>
			<div style="width:340px; max-height:330px; overflow-y:scroll" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="320" cellpadding="0" cellspacing="0">
					<?
					$i = 1;
						$po_ids=$program_id;
					 	$sql = "select a.id,a.po_number,c.country_ship_date from wo_po_break_down a,wo_po_details_master b,wo_po_color_size_breakdown c where b.job_no=a.job_no_mst and c.job_no_mst=a.job_no_mst and c.po_break_down_id=a.id and b.company_name=$companyID and a.id in($po_ids) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,a.po_number,c.country_ship_date";
					$result = sql_select($sql);
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="125"><p><? echo $row[csf('po_number')]; ?></p></td>
							<td align="center"><p><? echo change_date_format($row[csf('country_ship_date')]); ?></p></td>
						</tr>
						<?
						$i++;
					}
					?>
					
				</table>
			</div>
		</div>
	</fieldset>
	<?
	exit();
}
if ($action == "program_qnty_popup_action") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$program_data=explode("***", $program_id);
	$prog_booking_no=$program_data[0];
	$prog_color_id=$program_data[1];
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<div style="width:335px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
	<fieldset style="width:335px; margin-left:2px">
		<div id="report_container">
			<table border="1" class="rpt_table" rules="all" width="330" cellpadding="0" cellspacing="0">
				<thead>
					<th colspan="11"><b>PO Info</b></th>
				</thead>
				<thead>
					<th width="30">SL</th>
					<th width="125">Program No</th>
					<th width="80">Program Date</th>
					<th>Program Qnty</th>
				</thead>
			</table>
			<div style="width:340px; max-height:330px; overflow-y:scroll" id="scroll_body">
				<table border="1" class="rpt_table" rules="all" width="320" cellpadding="0" cellspacing="0">
					<?
					$i = 1;
					$sql="select b.id as plan_id,a.booking_no,b.color_id,b.program_date,sum(b.program_qnty) as program_qnty from ppl_planning_info_entry_mst a,ppl_planning_info_entry_dtls b where a.id=b.mst_id and a.booking_no in('$prog_booking_no') and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_id=$companyID and b.color_id in('$prog_color_id')  group by b.id,a.booking_no,b.color_id,b.program_date";

					$result = sql_select($sql);$total_progQnty=0;
					foreach ($result as $row) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						?>
						<tr bgcolor="<? echo $bgcolor; ?>"
							onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="30"><? echo $i; ?></td>
							<td width="125"><p><? echo $row[csf('plan_id')]; ?></p></td>
							<td width="80" align="center"><p><? echo change_date_format($row[csf('program_date')]); ?></p></td>
							<td align="right"><p><? echo number_format($row[csf('program_qnty')],2); ?></p></td>
						</tr>
						<?
						$total_progQnty+=$row[csf('program_qnty')];
						$i++;
					}
					?>
					<tr style="background-color: #f9f9f9;">
						<td align="right" colspan="3"><b>Total</b></td>
						<td align="right"><b><? echo number_format($total_progQnty,2); ?></b></td>
					</tr>
				</table>
			</div>
		</div>
	</fieldset>
	<?
	exit();
}
if ($action == "grey_receive_popup") 
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1, '', '', '');

	extract($_REQUEST);
	$order_id = explode('_', $order_id);
	?>
	<script>
		function print_window() {
			document.getElementById('scroll_body').style.overflow = "auto";
			document.getElementById('scroll_body').style.maxHeight = "none";

			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">' +
				'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>' + document.getElementById('report_container').innerHTML + '</body</html>');

			d.close();
			document.getElementById('scroll_body').style.overflowY = "scroll";
			document.getElementById('scroll_body').style.maxHeight = "230px";
		}
	</script>
	<div style="width:1037px" align="center"><input type="button" value="Print Preview" onClick="print_window()"
		style="width:100px" class="formbutton"/></div>
		<fieldset style="width:1037px; margin-left:2px">
			<div id="report_container">
				<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
					<thead>
						<th colspan="11"><b>Grey Receive / Purchase Info</b></th>
					</thead>
					<thead>
						<th width="30">SL</th>
						<th width="125">Receive Id</th>
						<th width="95">Receive Basis</th>
						<th width="150">Product Details</th>
						<th width="110">Booking/PI/ Production No</th>
						<th width="75">Production Date</th>
						<th width="80">Inhouse Production</th>
						<th width="80">Outside Production</th>
						<th width="80">Production Qnty</th>
						<th width="65">Challan No</th>
						<th>Kniting Com.</th>
					</thead>
				</table>
				<div style="width:1037px; max-height:330px; overflow-y:scroll" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="1020" cellpadding="0" cellspacing="0">
						<?
						$i = 1;
						$total_receive_qnty = 0;
						$product_arr = return_library_array("select id,product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');



						//$sql = "select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id $receive_basis_cond and a.entry_form in (22,58) and c.entry_form in (22,58) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id";

						/*

						58
						
						select a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.qnty) as quantity 
						from inv_receive_master a, pro_grey_prod_entry_dtls b, pro_roll_details c 
						where a.id=b.mst_id and b.id=c.dtls_id  and a.entry_form = 58 and c.entry_form = 58 and a.status_active=1 and a.is_deleted=0 
						and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.booking_no = '6153'
						group by b.id, a.recv_number, a.receive_date, a.receive_basis, a.booking_no, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id
						

						//22__9
						select a.id, a.recv_number, b.grey_receive_qnty
						from inv_receive_master a, pro_grey_prod_entry_dtls b, inv_receive_master c
						where a.id = b.mst_id and a.entry_form =22 and a.receive_basis =9 and a.company_id = 3  and  a.booking_id = c.id and c.entry_form=2 and c.receive_basis = 2*/


						$sql_22 ="select a.recv_number as booking_no,a.id
						from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c  
						where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2 
						and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_id' and b.trans_id = 0 and a.company_id = $companyID"; 
						$result_22 = sql_select($sql_22);
						foreach($result_22 as $row_22)
						{
							$booking_id .= $row_22[csf('id')].",";
						}

						$booking_id =  chop($booking_id,',');
						if($booking_id != ""){
						$sql_extend = " union all 
						select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity,a.booking_no 
						from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c 
						where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=22 and c.entry_form=22 and a.receive_basis in (9,11) 
						and b.status_active=1 and b.is_deleted=0 and a.company_id = $companyID
						and a.booking_id in ($booking_id) 
						group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id,a.booking_no ";
					}
					$sql =  "select * from (  
						select b.recv_number, b.receive_date, b.receive_basis, b.knitting_source, b.challan_no, b.knitting_company, c.prod_id, sum(a.qnty) as quantity,b.booking_no 
						from pro_roll_details a,inv_receive_master b, pro_grey_prod_entry_dtls c
						where a.entry_form = 58 and a.mst_id = b.id and b.id = c.mst_id and a.dtls_id = c.id
						and a.booking_no = '$program_id' 
						and a.status_active = 1 and a.is_deleted = 0 and b.company_id = $companyID
						group by b.recv_number, b.receive_date, b.receive_basis, b.knitting_source, b.challan_no, b.knitting_company, c.prod_id,b.booking_no
						union all
						select a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id, sum(c.quantity) as quantity,a.booking_no 
						from inv_receive_master a, pro_grey_prod_entry_dtls b,order_wise_pro_details c  
						where a.id=b.mst_id and c.dtls_id=b.id and a.item_category=13 and a.entry_form=2 and c.entry_form=2 and a.receive_basis=2 
						and b.status_active=1 and b.is_deleted=0 and a.booking_id = '$program_id'  and b.trans_id <> 0  and a.company_id = $companyID
						group by a.recv_number, a.receive_date, a.receive_basis, a.knitting_source, a.challan_no, a.knitting_company, b.prod_id,a.booking_no
						$sql_extend 
					) order by receive_date";  

					//echo $sql;

						$result = sql_select($sql);
						foreach ($result as $row) {
							if ($i % 2 == 0)
								$bgcolor = "#E9F3FF";
							else
								$bgcolor = "#FFFFFF";

							$total_receive_qnty += $row[csf('quantity')];
							?>
							<tr bgcolor="<? echo $bgcolor; ?>"
								onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="30"><? echo $i; ?></td>
								<td width="125"><p><? echo $row[csf('recv_number')]; ?></p></td>
								<td width="95"><p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?></p></td>
								<td width="150"><p><? echo $product_arr[$row[csf('prod_id')]]; ?></p></td>
								<td width="110"><p><? echo $row[csf('booking_no')]; ?>&nbsp;</p></td>
								<td width="75" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
								<td align="right" width="80">
									<?
									if ($row[csf('knitting_source')] != 3) {
										echo number_format($row[csf('quantity')], 2, '.', '');
										$total_receive_qnty_in += $row[csf('quantity')];
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right" width="80">
									<?
									if ($row[csf('knitting_source')] == 3) {
										echo number_format($row[csf('quantity')], 2, '.', '');
										$total_receive_qnty_out += $row[csf('quantity')];
									} else echo "&nbsp;";
									?>
								</td>
								<td align="right"
								width="80"><? echo number_format($row[csf('quantity')], 2, '.', ''); ?></td>
								<td width="65"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td>
									<p><? if ($row[csf('knitting_source')] == 1) echo $company_library[$row[csf('knitting_company')]]; else if ($row[csf('knitting_source')] == 3) echo $supplier_details[$row[csf('knitting_company')]]; ?>
								&nbsp;</p></td>
							</tr>
							<?
							$i++;
						}
						?>
						<tfoot>
							<th colspan="6" align="right">Total</th>
							<th align="right"><? echo number_format($total_receive_qnty_in, 2, '.', ''); ?></th>
							<th align="right"><? echo number_format($total_receive_qnty_out, 2, '.', ''); ?></th>
							<th align="right"><? echo number_format($total_receive_qnty, 2, '.', ''); ?></th>
							<th>&nbsp;</th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>
			</div>
		</fieldset>
		<?
		exit();
	}
?>