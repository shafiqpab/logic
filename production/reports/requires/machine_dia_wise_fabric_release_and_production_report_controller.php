<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}

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

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value(str) {

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
			var id = ''; var name = '';
			for (var i = 0; i < selected_id.length; i++) {
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
									echo create_drop_down("cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer--", 0, "", 0);
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
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view('<? echo $companyID; ?>' + '**' + document.getElementById('cbo_buyer_name').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value + '**' +'<? echo $year_selection; ?>', 'create_order_no_search_list_view', 'search_div', 'machine_dia_wise_fabric_release_and_production_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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

if ($action == "create_order_no_search_list_view") 
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
		$search_field = "b.po_number";
	else if ($search_by == 2)
		$search_field = "a.style_ref_no";
	else
		$search_field = "a.job_no";

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

	$year_selection = $data[6];
	if ($year_selection!=0)
	{
		if($db_type==0)
		{
			$job_year=" and YEAR(a.insert_date)=$year_selection";
		}
		else if($db_type==2)
		{
			$job_year=" and to_char(a.insert_date,'YYYY')=$year_selection";
		}
		else
		{
			$job_year="";
		}
	}

	$company_library = return_library_array("select id, company_short_name from lib_company", "id", "company_short_name");
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", "id", "short_name");
	$arr = array(0 => $company_library, 1 => $buyer_arr);

	if ($db_type == 0)
		$year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2)
		$year_field = "to_char(a.insert_date,'YYYY') as year";
	else
        $year_field = ""; //defined Later

    $sql = "SELECT b.id, a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $date_cond $job_year order by b.id, b.pub_shipment_date";

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No, Po No, Shipment Date", "80,80,50,70,140,170", "760", "220", 0, $sql, "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0,0", $arr, "company_name,buyer_name,year,job_no_prefix_num,style_ref_no,po_number,pub_shipment_date", "", '', '0,0,0,0,0,0,3', '', 1);
    exit();
}

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
			//alert(booking_no);
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
									<th width="180">Booking Date</th>
									<th>&nbsp;</th>
								</thead>
								<tr>
									<td>
										<input type="hidden" id="selected_booking">
										<input type="hidden" id="job_no" value="<? echo $data[2];?>">
										<? 
										echo create_drop_down( "cbo_company_mst", 150, "select id,company_name from lib_company comp where status_active=1 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $data[0], "load_drop_down( 'knitting_status_report_controller', this.value, 'load_drop_down_buyer', 'buyer_td' );");
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
										<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_mst').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('job_no').value+'_'+document.getElementById('txt_booking_no').value, 'create_booking_search_list_view', 'search_div', 'machine_dia_wise_fabric_release_and_production_report_controller','setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	if ($data[0]!=0) $company="  company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $buyer=" and buyer_id='$data[1]'"; else $buyer="";
	if ($data[4]!=0)
	{
		if($db_type==0)
		{
			$booking_year=" and YEAR(a.insert_date)=$data[4]";
		}
		else if($db_type==2)
		{
			$booking_year=" and to_char(a.insert_date,'YYYY')=$data[4]";
		}
		else
		{
			$booking_year="";
		}
	}
	// echo $booking_year;die;
	if ($data[5]!=0) $booking_no=" and booking_no_prefix_num='$data[5]'"; else $booking_no='';
	if($db_type==0)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-")."' and '".change_date_format($data[3], "yyyy-mm-dd", "-")."'"; else $booking_date ="";
	}
	if($db_type==2)
	{
		if ($data[2]!="" &&  $data[3]!="") $booking_date  = "and booking_date  between '".change_date_format($data[2], "yyyy-mm-dd", "-",1)."' and '".change_date_format($data[3], "yyyy-mm-dd", "-",1)."'"; else $booking_date ="";
	}
	$po_array=array();
	$sql_po= sql_select("select booking_no,po_break_down_id from wo_booking_mst where $company $buyer $booking_no $booking_date and booking_type=1 and is_short=2 and status_active=1  and 	is_deleted=0 order by booking_no");
	foreach($sql_po as $row)
	{
		$po_id=explode(",",$row[csf("po_break_down_id")]);
		//print_r( $po_id);
		$po_number_string="";
		foreach($po_id as $key=> $value )
		{
			$po_number_string.=$order_arr[$value].",";
		}
		$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
	}
	$approved=array(0=>"No",1=>"Yes");
	$is_ready=array(0=>"No",1=>"Yes",2=>"No");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$suplier=return_library_array( "select id, short_name from lib_supplier",'id','short_name');
	$arr=array (2=>$comp,3=>$buyer_arr,5=>$item_category,6=>$fabric_source,7=>$suplier,8=>$approved,9=>$is_ready);

	$sql = "SELECT a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved from wo_booking_mst a,wo_booking_dtls b where a.booking_no=b.booking_no and $company $buyer $booking_no $booking_date and a.booking_type in(1,4) and a.status_active=1 and a.is_deleted=0 $booking_year 
	group by a.booking_no_prefix_num, a.booking_no, a.booking_date, a.company_id, a.buyer_id, a.job_no, a.po_break_down_id, a.item_category, a.fabric_source, a.supplier_id, a.is_approved, a.ready_to_approved order by a.booking_no_prefix_num Desc";
	echo  create_list_view("list_view", "Booking No,Booking Date,Company,Buyer,Job No.,Fabric Nature,Fabric Source,Supplier,Approved,Is-Ready", "80,80,70,100,90,200,80,80,50,50","1020","320",0, $sql , "js_set_value", "booking_no_prefix_num", "", 1, "0,0,company_id,buyer_id,0,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", $arr , "booking_no_prefix_num,booking_date,company_id,buyer_id,job_no,item_category,fabric_source,supplier_id,is_approved,ready_to_approved", '','','0,3,0,0,0,0,0,0,0,0','','');
	
	exit(); 
}

if($action=="report_generate") // Knitting Production And Plan Report
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_year_selection = str_replace("'", "", trim($cbo_year_selection));
	if($db_type==0)
	{
		$year_cond=" and YEAR(b.insert_date)=$cbo_year_selection";
		$booking_year_cond=" and YEAR(d.insert_date)=$cbo_year_selection";
	}
	else if($db_type==2)
	{
		$year_cond=" and to_char(b.insert_date,'YYYY')=$cbo_year_selection";
		$booking_year_cond=" and to_char(d.insert_date,'YYYY')=$cbo_year_selection";
	}
	else
	{
		$year_cond="";
		$booking_year_cond="";
	}

	$cbo_buyer_name=trim(str_replace("'","",$cbo_buyer_name));
	if($cbo_buyer_name!=0) $buyer_cond=" and a.buyer_name=$cbo_buyer_name"; else $buyer_cond='';
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	
	$txt_po_no=trim(str_replace("'","",$txt_po_no));
	if($txt_po_no!="") $po_cond=" and b.po_number LIKE '".$txt_po_no."%'"; else $po_cond='';

	$po_cond = "";
	if (str_replace("'", "", trim($txt_po_no)) != "") 
	{
		if (str_replace("'", "", $hide_order_id) != "") {
			$po_cond = "and b.id in(" . str_replace("'", "", $hide_order_id) . ")";
		} else {
			$po_number = "%" . trim(str_replace("'", "", $txt_po_no)) . "%";
			$po_cond = "and b.po_number like '$po_number'";
		}
	}

	$booking_search_cond = "";
	if (str_replace("'", "", trim($txt_booking_no)) != "") 
	{
		$booking_number = "%" . trim(str_replace("'", "", $txt_booking_no)) . "%";
		$booking_search_cond = "and a.booking_no like '$booking_number'";
	}

	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	if($txt_file_no!="") $file_no_cond=" and b.file_no LIKE '".$txt_file_no."%'"; else $file_no_cond='';
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	if($txt_ref_no!="") $ref_no_cond=" and b.grouping LIKE '".$txt_ref_no."%'"; else $ref_no_cond='';

	if($cbo_buyer_name!=0) $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name"; else $buyer_cond='';

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
	 	if($db_type==0)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			$program_date_cond=" and b.program_date between '$start_date' and '$end_date'";
		}
		else if($db_type==2)
		{
			$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
			$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			$program_date_cond=" and b.program_date between '$start_date' and '$end_date'";
		}		
	}
	//for knitting source
	$cbo_type=trim(str_replace("'","",$cbo_type));
	if($cbo_type>0)
		$knitting_source_cond="and b.knitting_source=$cbo_type";
	else
		$knitting_source_cond="";

	if (str_replace("'", "", $txt_machine_dia) == "")
		$machine_dia = "%%";
	else
		$machine_dia = "%" . str_replace("'", "", $txt_machine_dia) . "%";

	if (str_replace("'", "", $txt_machine_gauge) == "")
		$machine_gauge = "%%";
	else
		$machine_gauge = "%" . str_replace("'", "", $txt_machine_gauge) . "%";

	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$body_part_type_arr=return_library_array( "select id, body_part_type from lib_body_part where body_part_type in(40,50) and status_active=1 and is_deleted=0", "id", "body_part_type");
	
	// ------------------------ constuction composition query start-------------------
	$sql_deter="SELECT a.id, a.construction, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active!=0 and a.is_deleted!=1  and b.status_active!=0 and b.is_deleted!=1";
    $deter_array=sql_select($sql_deter);
    if(count($deter_array)>0)
    {
        foreach($deter_array as $row )
        {
            if(array_key_exists($row[csf('id')],$composition_arr))
            {
                $composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
            }
            else
            {
                $composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
            }
            $constuction_arr[$row[csf('id')]]=$row[csf('construction')];
        }
    }
    unset($deter_array);
    // ------------------------ constuction composition query start-------------------

	// ============================Order Entry sql start ===============================
	$po_id_cond="";
	if($po_cond != "" || $ref_no_cond!="" || $file_no_cond!="")
	{
		$poArr=array(); $poIds=''; $tot_rows=0; $reqArr = array();
		$sql="SELECT a.job_no, a.buyer_name, a.style_ref_no, b.id, b.po_number, b.file_no, b.grouping, b.pub_shipment_date
		from wo_po_details_master a, wo_po_break_down b 
		where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $file_no_cond $ref_no_cond $po_cond $year_cond";
		// echo $sql; // TMP_PO_ID
		$result=sql_select($sql);
		foreach($result as $row)
		{
			$tot_rows++;
			$poDataArr[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$poDataArr[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$poDataArr[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
			$poDataArr[$row[csf('id')]]['file_no']=$row[csf('file_no')];
			$poDataArr[$row[csf('id')]]['grouping']=$row[csf('grouping')];
			$poDataArr[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];

			$poIds.=$row[csf('id')].",";
		}
		unset($result);	

		$poIds=chop($poIds,','); //$poIds_cond=""; 
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond_pre=" and ("; $poIds_cond_suff.=")";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				// $poIds_cond.=" b.po_break_down_id in($ids) or ";
				$po_id_cond.=" c.po_id in($ids) or ";
				// $poIds_cond_order.=" c.po_breakdown_id in($ids) or ";
			}
			
			// $poIds_cond=$poIds_cond_pre.chop($poIds_cond,'or ').$poIds_cond_suff;
			$po_id_cond=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
			// $poIds_cond_order=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		}
		else
		{
			// $poIds_cond=" and b.po_break_down_id in($poIds)";
			$po_id_cond=" and c.po_id in($poIds)";
			// $poIds_cond_order=" and c.po_breakdown_id in($poIds)";
		}
	}
	// ============================Order Entry sql end ==================================	

	if ($db_type == 0) {
		$po_field = "group_concat(c.po_id) po_id";
	} else if ($db_type == 2) {
		$po_field = "listagg(cast(c.po_id as varchar2(4000)), ',') within group (order by c.po_id) as po_id";
	}
	if ($program_date_cond == "") {
		$prog_year_cond=$year_cond;
	}
	$sql = "SELECT a.company_id, a.buyer_id, a.booking_no, a.determination_id as deter_id, a.body_part_id, c.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id as program_no, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_qnty as program_qnty, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, b.location_id, $po_field
	from ppl_planning_info_entry_mst a, ppl_planning_info_entry_dtls b, ppl_planning_entry_plan_dtls c 
	where a.id=b.mst_id and b.id=c.dtls_id and a.company_id=$cbo_company_name and b.machine_dia like '$machine_dia'  and b.machine_gg like '$machine_gauge' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and c.is_sales =0 $buyer_id_cond $po_id_cond $program_date_cond $booking_search_cond $knitting_source_cond $prog_year_cond
	group by a.company_id, a.buyer_id, a.booking_no, a.determination_id, a.body_part_id, c.color_type_id, a.fabric_desc, a.gsm_weight, a.dia, a.width_dia_type, b.id, b.knitting_source, b.knitting_party, b.color_id, b.color_range, b.machine_dia, b.machine_gg, b.program_date, b.stitch_length, b.spandex_stitch_length, b.draft_ratio, b.machine_id, b.distribution_qnty, b.status, b.start_date, b.end_date, b.remarks, b.color_id, b.location_id, b.program_qnty 
	order by a.booking_no, b.machine_dia, b.machine_gg";
	// echo $sql;die;
	$sql_result=sql_select($sql);

	// ========================= tmp_poid data insert here start ======================
 	if(!empty($sql_result))	
 	{
 		$con = connect();
		$r_id=execute_query("delete from tmp_poid where userid=$user_id");
		if($r_id)
		{
		    oci_commit($con);
		}
	}

	foreach ($sql_result as $row) 
	{
		$po_id_arr=explode(",",$row[csf('po_id')]);
		foreach($po_id_arr as $p_id)
		{			
			if(!$po_arr[$p_id])
			{
				$po_arr[$p_id] = $p_id;
		    	$rID2=execute_query("insert into tmp_poid (userid, poid) values ($user_id,$p_id)");
			}
		}
	}

	if($rID2)
	{
	    oci_commit($con);
	}
	
	//die;
	
	// ========================= tmp_poid data insert here end =======================

	// ========================== PO Booking info Start ==============================
	if(!empty($po_arr))
	{
		$po_array = array();
		/*$po_sql = sql_select("SELECT a.job_no, a.style_ref_no, b.id, b.po_number,b.pub_shipment_date, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b, tmp_poid c 
		where a.job_no=b.job_no_mst and b.id=c.poid and c.userid=$user_id and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active in (1) and b.is_deleted=0 $buyer_cond $file_no_cond $ref_no_cond $po_cond $year_cond");*/

		$po_sql = sql_select("SELECT a.job_no, a.style_ref_no, b.id, b.po_number, b.pub_shipment_date, b.grouping, b.file_no, sum(e.grey_fab_qnty) as  grey_fab_qnty , d.booking_no, d.booking_date, e.pre_cost_fabric_cost_dtls_id , f.lib_yarn_count_deter_id as deter_id, f.body_part_id, f.gsm_weight, f.color_type_id, f.width_dia_type
		from wo_po_details_master a, wo_po_break_down b, tmp_poid c, wo_booking_mst d, wo_booking_dtls e, wo_pre_cost_fabric_cost_dtls f 
		where a.job_no=b.job_no_mst and b.id=c.poid and a.job_no=d.job_no and d.booking_no=e.booking_no and c.poid=e.po_break_down_id and b.id=e.po_break_down_id and b.job_no_mst=d.job_no and b.job_no_mst=e.job_no and a.job_no=e.job_no and e.pre_cost_fabric_cost_dtls_id=f.id and e.job_no=f.job_no and c.userid=1 and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active in (1) and b.is_deleted=0 $buyer_cond $file_no_cond $ref_no_cond $po_cond  $booking_year_cond and  d.item_category in(2,13) and d.booking_type in(1,4) and d.is_deleted=0 and d.status_active=1 and e.is_deleted=0 and e.status_active=1  and f.body_part_type not in(40,50)
		group by a.job_no, a.style_ref_no, b.id, b.po_number,b.pub_shipment_date, b.grouping, b.file_no , d.booking_no, d.booking_date, e.pre_cost_fabric_cost_dtls_id , f.lib_yarn_count_deter_id, f.body_part_id, f.gsm_weight, f.color_type_id, f.width_dia_type");// and f.body_part_type not in(40,50)

		foreach ($po_sql as $row) 
		{
			$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['ship_date'] = $row[csf('pub_shipment_date')];
			$po_array[$row[csf('id')]]['year'] = $row[csf('year')];
			$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$po_array[$row[csf('id')]]['ref_no'] = $row[csf('grouping')];

			$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['job_no']=$row[csf('job_no')];
			$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['style_ref_no']=$row[csf('style_ref_no')];
			$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['ship_date']=$row[csf('pub_shipment_date')];
			$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['file_no']=$row[csf('file_no')];
			$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['ref_no']=$row[csf('grouping')];
			$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['booking_date']=$row[csf('booking_date')];
			$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['grey_fab_qnty']+=$row[csf('grey_fab_qnty')];
		}
	}
	// echo "<pre>";print_r($po_booking_arr);die;
	// ========================== PO Booking info End================================

	// =========================== main data array generate start ===================
	$data_arr=array();
	foreach($sql_result as $row)
	{
		$mc_dia_gg=$row[csf('machine_dia')].'x'.$row[csf('machine_gg')];

		$body_part_type=$body_part_type_arr[$row[csf('body_part_id')]];
		if ($body_part_type !=40 && $body_part_type !=50) // without collar & cuff data
		{
			$job_no=$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['job_no'];
			$style_ref_no=$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['style_ref_no'];
			$ship_date=$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['ship_date'];
			$file_no=$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['file_no'];
			$ref_no=$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['ref_no'];
			$booking_qnty=$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['grey_fab_qnty'];
			$booking_date=$po_booking_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['booking_date'];

			$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['buyer_name']=$row[csf('buyer_id')];
			$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['job_no']=$job_no;
			$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['style_ref_no']=$style_ref_no;
			$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['ship_date']=$ship_date;
			$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['file_no']=$file_no;
			$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['ref_no']=$ref_no;
			$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['booking_qnty']=$booking_qnty;
			$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['booking_date']=$booking_date;
			
			$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['program_no'].=$row[csf('program_no')].',';
			// echo $booking_qnty.'<br>';
			if ($row[csf('knitting_source')]==1) 
			{
				$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['inside']+=$row[csf('program_qnty')];
			}
			else
			{
				$data_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]][$mc_dia_gg]['out_side']+=$row[csf('program_qnty')];
			}			
		}		
	}
	unset($sql_result);
	// echo "<pre>";print_r($data_arr);die;
	// =========================== main data array generate End ===================

	// ====================================Booking================================= 
	/*$grey_qnty_array=array();
	$bookingSql="SELECT a.booking_no, a.booking_date, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.color_size_table_id, c.lib_yarn_count_deter_id as deter_id, c.body_part_id, sum(b.grey_fab_qnty) as grey_fab_qnty, c.gsm_weight, c.color_type_id, c.width_dia_type
	from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
	where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and a.item_category in(2,13) and a.booking_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $poIds_cond $all_booking_ids_cond
	group by a.booking_no, a.booking_date, b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, b.color_size_table_id, c.lib_yarn_count_deter_id, c.body_part_id, c.gsm_weight, c.color_type_id, c.width_dia_type"; //  and c.body_part_type not in(40,50)
	// echo $bookingSql;
	$bookingDataArray=sql_select($bookingSql);
	foreach($bookingDataArray as $val)
	{
		$pre_cost_dtls_id_arr[$val[csf('pre_cost_fabric_cost_dtls_id')]]=$val[csf('pre_cost_fabric_cost_dtls_id')];
	}*/
	// ====================================Booking end================================
	
	// ============================== Precost Start===================================
	$item_size_sql = sql_select("SELECT a.booking_no, b.lib_yarn_count_deter_id as deter_id, b.gsm_weight, b.color_type_id, b.width_dia_type, c.dia_width, c.item_size
	from   tmp_poid x, wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_pre_cos_fab_co_avg_con_dtls c 
	where x.poid=a.po_break_down_id and a.color_size_table_id=c.color_size_table_id and a.pre_cost_fabric_cost_dtls_id=b.id and b.id = c.pre_cost_fabric_cost_dtls_id 
	and a.status_active = 1 and b.status_active =1 and c.status_active=1 and a.booking_type=1 and b.body_part_type not in(40,50)
	group by a.booking_no, b.lib_yarn_count_deter_id, b.gsm_weight, b.color_type_id, b.width_dia_type, c.dia_width, c.item_size");

	$item_size_ref_arr=array();
	foreach ($item_size_sql as $row) 
	{
		$item_size_ref_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['dia_width'] .= $row[csf("dia_width")].',';
		$item_size_ref_arr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['item_size'] .= $row[csf("item_size")].',';
	}
	// echo "<pre>";print_r($item_size_ref_arr);die;
	// ============================== Precost End===================================

	// ==========================Knitting production start==========================
	$query="SELECT a.booking_id, a.booking_no, c.po_breakdown_id, b.febric_description_id as deter_id, sum(c.quantity) as knitting_qnty 
	from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, tmp_poid d 
	where a.id=b.mst_id and b.id=c.dtls_id and c.po_breakdown_id=d.poid and a.receive_basis=2 and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 
	group by  a.booking_id, a.booking_no, c.po_breakdown_id, b.febric_description_id";
	// echo $query;die;
	$data_array=sql_select($query);
	$knitDataArr=array();
	foreach($data_array as $row)
	{
		// $productionDataArr[$row[csf('booking_no')]][$row[csf('deter_id')]][$row[csf('gsm_weight')]][$row[csf('color_type_id')]][$row[csf('width_dia_type')]]['quantity']+=$row[csf('quantity')];
		$knitDataArr[$row[csf('booking_id')]]['knitting_qnty'] += $row[csf('knitting_qnty')];
	}	
	unset($data_array);
	// ==========================Knitting production end==========================
	$r_id=execute_query("delete from tmp_poid where userid=$user_id");
	if($r_id)
	{
	    oci_commit($con);
	    disconnect($con);
	}
	
	ob_start();
	?>
	<fieldset style="width:2020px">
		<table cellpadding="0" cellspacing="0" width="2020">
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
			</tr>
		</table>
		<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
            	<tr>
                    <th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="80">Job No </th>
                    <th width="70">Ref No</th>
                    <th width="90">File No</th>
                    <th width="60">Style</th>
                    <th width="100">Booking No</th>
                    <th width="70">Booking Date</th>
                    <th width="70">Pub. Shipment Date</th>
                    <th width="100">Construction</th>
                    <th width="160">Fabric Description</th>
                    <th width="50">GSM</th>
                    <th width="80">Color Type</th>
                    <th width="90">Dia/Width Type</th>
                    <th width="50" title="Precost v2 > Dia">F.Dia</th>
                    <th width="90" title="Precost v2 > Item Size">M/C Dia X Gauge</th>
                    <th width="90" title="Plan MC Dia GG">M/C Dia X Gauge</th>
                    <th width="90">Booking Qty.</th>
                    <th width="90">Inside</th>
                    <th width="90">Outside</th>
                    <th width="90">Total Program Qty</th>
                    <th width="90">Yet to Program</th>
                	<th width="80">Knitting Productin</th>
                    <th>Production Balance</th>
                </tr>
			</thead>
		</table>
		<div style="width:2020px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body"> 
				<?
				// =========================Booking, deter_id and gsm wise rowspan====================
				foreach ($data_arr as $booking_no => $booking_noArr) 
		        {
		        	foreach ($booking_noArr as $deter_id => $deter_idArr) 
		        	{
        				foreach ($deter_idArr as $gsm => $gsm_Arr)
        				{
        					foreach($gsm_Arr as $color_type_id => $color_type_id_Arr)
							{
								foreach($color_type_id_Arr as $width_dia_type => $width_dia_typeArr)
								{
									foreach($width_dia_typeArr as $mc_dia_ggkey => $row)
									{
			        					$total_program_qty=$row['inside']+$row['out_side'];
			        					$booking_qnty=$row['booking_qnty'];
			        					
			        					$rowspan_arr[$booking_no][$deter_id][$gsm]++;
			        					$program_qty_arr[$booking_no][$deter_id][$gsm]+=$total_program_qty;
			        					$booking_qnty_arr[$booking_no][$deter_id][$gsm]+=$booking_qnty;
			        				}
			        			}
			        		}        		
        				}
		        	}
		        }
				// echo "<pre>";print_r($yet_to_program_arr);

				$i=1; $grand_tot_booking_qty=$grand_tot_inside_program_qty=$grand_tot_out_side_program_qty=$grand_tot_program_qty=$grand_tot_yet_to_program=$grand_tot_knitting_qty=$grand_tot_production_balance=0;
				foreach($data_arr as $booking_no => $booking_noArr)
				{
					$booking_tot_booking_qty=$booking_tot_inside_program_qty=$booking_tot_out_side_program_qty=$booking_tot_program_qty=$booking_tot_yet_to_program=$booking_tot_knitting_qty=$booking_tot_production_balance=0;
					foreach($booking_noArr as $deter_id => $deter_idArr)
					{
						$fabric_tot_booking_qty=$fabric_tot_inside_program_qty=$fabric_tot_out_side_program_qty=$fabric_tot_program_qty=$fabric_tot_yet_to_program=$fabric_tot_knitting_qty=$fabric_tot_production_balance=0;
						foreach($deter_idArr as $gsm => $gsm_Arr)
						{
							$c=1;
							foreach($gsm_Arr as $color_type_id => $color_type_id_Arr)
							{
								foreach($color_type_id_Arr as $width_dia_type => $width_dia_typeArr)
								{
									foreach($width_dia_typeArr as $mc_dia_ggkey => $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$rowspan=$rowspan_arr[$booking_no][$deter_id][$gsm];
										$program_qty=$program_qty_arr[$booking_no][$deter_id][$gsm];
										$booking_qnty=$booking_qnty_arr[$booking_no][$deter_id][$gsm];

										$fdia=$item_size_ref_arr[$booking_no][$deter_id][$gsm][$color_type_id][$width_dia_type]['dia_width'];
										$item_size=$item_size_ref_arr[$booking_no][$deter_id][$gsm][$color_type_id][$width_dia_type]['item_size'];
										$fdia=implode(",",array_unique(explode(",",chop($fdia,","))));
										$item_size=implode(",",array_unique(explode(",",chop($item_size,","))));

										$total_program_qty=$row['inside']+$row['out_side'];
										// $yet_to_program=$row['booking_qnty']-$total_program_qty;

										$knitting_qty="";
										$program_no = array_unique(explode(",", chop($row['program_no'],",")));
										foreach ($program_no as $pro_no) 
										{
											// echo $pro_no.'<br>';
											$knitting_qty += $knitDataArr[$pro_no]['knitting_qnty'];
										}
										// echo $knitting_qty.'<br>';
										$production_balance=$total_program_qty-$knitting_qty;
										?>
										<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
											<td width="40"><p><? echo $i; ?></p></td>
											<td width="60"><p><? echo $buyer_arr[$row['buyer_name']]; ?></p></td>
				                            <td width="80"><p><? echo $row['job_no']; ?></p></td>
				                            <td width="70"><p><? echo $row['ref_no']; ?></p></td>
				                            <td width="90"><p><? echo $row['file_no']; ?></p></td>
				                            <td width="60"><p><? echo $row['style_ref_no']; ?></p></td>
				                            <td width="100"><p><? echo $booking_no;//$fDia; ?></p></td>
				                            <td width="70"><p><? echo change_date_format($row['booking_date']); ?></p></td>
											<td width="70"><p><? echo change_date_format($row['ship_date']); ?></p></td>
				                            <td width="100"><p><? echo $constuction_arr[$deter_id]; ?></p></td>
											<td width="160" title="<? echo $deter_id;?>"><p><? echo $composition_arr[$deter_id]; ?></p></td>
				                            <td width="50"><p><? echo $gsm; ?></p></td>
											<td width="80" title="<? echo $color_type_id; ?>"><p><? echo $color_type[$color_type_id]; ?></p></td>
				                            <td width="90" title="<? echo $width_dia_type; ?>"><p><? echo $fabric_typee[$width_dia_type]; ?></p></td>
				                            <td width="50" title="Precost v2 > Dia"><p><? echo $fdia; ?></p></td>
				                            <td width="90" title="Precost v2 > Item Size"><p><? echo $item_size; ?></p></td>
				                            <td width="90" title="Plan MC Dia GG"><p><? echo $mc_dia_ggkey; ?></p></td>
				                            <?
											if($c==1)
											{
											?>
											<td width="90" style="vertical-align: middle;" align="right" rowspan="<?= $rowspan;?>"><p><? echo number_format($booking_qnty,2,'.',''); 
											$fabric_tot_booking_qty+=$booking_qnty;
											$booking_tot_booking_qty+=$booking_qnty;
											$grand_tot_booking_qty+=$booking_qnty;
											?></p></td>
											<?
											}
											?>
				                            <td width="90" align="right"><p><? echo number_format($row['inside'],2,'.',''); ?></p></td>
				                            <td width="90" align="right"><p><? echo number_format($row['out_side'],2,'.',''); ?></p></td>
				                            <td width="90" align="right" title="<? echo $row['program_no'];?>"><p><? echo number_format($total_program_qty,2,'.',''); ?></p></td>
				                            <?
											if($c==1)
											{
												$yet_to_program=$booking_qnty-$program_qty;
											?>
											<td width="90" title="Booking Qty: <? echo $booking_qnty.', program_qty: '.$program_qty; ?>" style="vertical-align: middle;" align="right" rowspan="<?= $rowspan;?>"><p><? echo number_format($yet_to_program,2,'.',''); 
											$fabric_tot_yet_to_program+=$yet_to_program;
											$booking_tot_yet_to_program+=$yet_to_program;
											$grand_tot_yet_to_program+=$yet_to_program;
											?></p></td>
											<?
											}
											?>
											<td width="80" align="right"><p><? echo number_format($knitting_qty,2,'.',''); ?></p></td>
											<td align="right"><p><? echo number_format($production_balance,2,'.',''); ?></p></td>
										</tr>
										<?	
										$i++; $c++;									
										// $fabric_tot_booking_qty+=$row['booking_qnty'];
										$fabric_tot_inside_program_qty+=$row['inside'];
										$fabric_tot_out_side_program_qty+=$row['out_side'];
										$fabric_tot_program_qty+=$total_program_qty;
										// $fabric_tot_yet_to_program+=$yet_to_program;
										$fabric_tot_knitting_qty+=$knitting_qty;
										$fabric_tot_production_balance+=$production_balance;

										// $booking_tot_booking_qty+=$row['booking_qnty'];
										$booking_tot_inside_program_qty+=$row['inside'];
										$booking_tot_out_side_program_qty+=$row['out_side'];
										$booking_tot_program_qty+=$total_program_qty;
										// $booking_tot_yet_to_program+=$yet_to_program;
										$booking_tot_knitting_qty+=$knitting_qty;
										$booking_tot_production_balance+=$production_balance;

										// $grand_tot_booking_qty+=$row['booking_qnty'];
										$grand_tot_inside_program_qty+=$row['inside'];
										$grand_tot_out_side_program_qty+=$row['out_side'];
										$grand_tot_program_qty+=$total_program_qty;
										// $grand_tot_yet_to_program+=$yet_to_program;
										$grand_tot_knitting_qty+=$knitting_qty;
										$grand_tot_production_balance+=$production_balance;
									}
								}
							}
						}
						?>
						<!-- Fabric Total -->
						<tr class="tbl_bottom">
							<td width="40">&nbsp;</td>
				            <td width="60">&nbsp;</td>
		                    <td width="80">&nbsp;</td>
		                    <td width="70">&nbsp;</td>
		                    <td width="90">&nbsp;</td>
		                    <td width="60">&nbsp;</td>
		                    <td width="100">&nbsp;</td>
		                    <td width="70">&nbsp;</td>
		                    <td width="70">&nbsp;</td>
		                    <td width="100">&nbsp;</td>
		                    <td width="160">&nbsp;</td>
		                    <td width="50">&nbsp;</td>
		                    <td width="80">&nbsp;</td>
		                    <td width="90">&nbsp;</td>
		                    <td width="50">&nbsp;</td>
		                    <td width="90">&nbsp;</td>
		                    <td align="right" width="90">Fabric Total</td>
		                    <td align="right" width="90"><? echo number_format($fabric_tot_booking_qty,2,'.',''); ?></td>
		                    <td align="right" width="90"><? echo number_format($fabric_tot_inside_program_qty,2,'.',''); ?></td>
		                    <td align="right" width="90"><? echo number_format($fabric_tot_out_side_program_qty,2,'.',''); ?></td>
		                    <td align="right" width="90"><? echo number_format($fabric_tot_program_qty,2,'.',''); ?></td>
		                    <td align="right" width="90"><? echo number_format($fabric_tot_yet_to_program,2,'.',''); ?></td>
		                    <td align="right" width="80"><? echo number_format($fabric_tot_knitting_qty,2,'.',''); ?></td>
		                    <td align="right"><? echo number_format($fabric_tot_production_balance,2,'.',''); ?></td>
						</tr>
						<?
					}
					?>
					<!-- Booking Total -->
					<tr class="tbl_bottom">
						<td width="40">&nbsp;</td>
			            <td width="60">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
	                    <td width="90">&nbsp;</td>
	                    <td width="60">&nbsp;</td>
	                    <td width="100">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
	                    <td width="70">&nbsp;</td>
	                    <td width="100">&nbsp;</td>
	                    <td width="160">&nbsp;</td>
	                    <td width="50">&nbsp;</td>
	                    <td width="80">&nbsp;</td>
	                    <td width="90">&nbsp;</td>
	                    <td width="50">&nbsp;</td>
	                    <td width="90">&nbsp;</td>
	                    <td align="right" width="90">Booking Total</td>
	                    <td align="right" width="90"><? echo number_format($booking_tot_booking_qty,2,'.',''); ?></td>
	                    <td align="right" width="90"><? echo number_format($booking_tot_inside_program_qty,2,'.',''); ?></td>
	                    <td align="right" width="90"><? echo number_format($booking_tot_out_side_program_qty,2,'.',''); ?></td>
	                    <td align="right" width="90"><? echo number_format($booking_tot_program_qty,2,'.',''); ?></td>
	                    <td align="right" width="90"><? echo number_format($booking_tot_yet_to_program,2,'.',''); ?></td>
	                    <td align="right" width="80"><? echo number_format($booking_tot_knitting_qty,2,'.',''); ?></td>
	                    <td align="right"><? echo number_format($booking_tot_production_balance,2,'.',''); ?></td>
					</tr>
					<?
				}				
				?>
			</table>
		</div>    
		<!-- Grand Total --> 
		<table width="2000" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<tfoot>
                <tr>
                	<th width="40">&nbsp;</th>
                	<th width="60">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="100">&nbsp;</th>
                    <th width="160">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th align="right" width="90">Grand Total</th>
                    <th align="right" width="90"><? echo number_format($grand_tot_booking_qty,2,'.',''); ?></th>
                    <th align="right" width="90"><? echo number_format($grand_tot_inside_program_qty,2,'.',''); ?></th>
                    <th align="right" width="90"><? echo number_format($grand_tot_out_side_program_qty,2,'.',''); ?></th>
                    <th align="right" width="90"><? echo number_format($grand_tot_program_qty,2,'.',''); ?></th>
                    <th align="right" width="90"><? echo number_format($grand_tot_yet_to_program,2,'.',''); ?></th>
                    <th align="right" width="80"><? echo number_format($grand_tot_knitting_qty,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($grand_tot_production_balance,2,'.',''); ?></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
	<?

    $html=ob_get_contents();
	ob_clean();
	        
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,$html);
	echo "$html####$filename";
	
	exit();
}
?>