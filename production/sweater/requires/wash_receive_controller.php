<?
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];
$user_level = $_SESSION['logic_erp']['user_level'];

$userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, company_location_id as location_id FROM user_passwd where id=$user_id");

$location_id = $userCredential[0][csf('location_id')];
$location_credential_cond = "";

if ($location_id != '') {
	$location_credential_cond = " and id in($location_id)";
}

//************************************ Start **************************************************

if ($action == "load_drop_down_multiple") 
{
	echo create_drop_down("cbo_location_name", 130, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 $location_credential_cond order by location_name", "id,location_name", 1, "-- Select Location --", $selected, "");

	
	exit();
}

if ($action=="load_drop_down_location")
{
	$data=explode("_",$data);
	if($data[1]==1) { $dropdown_name="cbo_location"; $load_function=""; }
	else if($data[1]==2) { $dropdown_name="cbo_wash_location"; $load_function="load_drop_down( 'requires/wash_receive_controller', this.value, 'load_drop_down_floor', 'floor_td' );"; }
	echo create_drop_down( $dropdown_name, 130, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "$load_function" );	
	exit();
}

if ($action=="load_drop_down_location2")
{
	$data=explode("_",$data);
	echo create_drop_down( 'cbo_wash_location', 130, "select id,location_name from lib_location where company_id='$data[0]' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "$load_function" );	
	exit();
}

if ($action=="load_drop_down_floor")
{
 	echo create_drop_down( "cbo_floor", 130, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$data' and production_process in (7,8,9) order by floor_name","id,floor_name", 1, "--Select Floor--", $selected, "",0 );
	exit();
}

if ($action=="load_drop_down_working_com")
{
	$data=explode("_",$data);
	// print_r($data);die;

	if($data[1]==1) $load_function="fnc_load_party(2,document.getElementById('cbo_source').value)";
	else $load_function="";
	
	if($data[1]==1)
	{
		echo create_drop_down( "cbo_wash_company", 130, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-Wash. Company-", $data[2], "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_wash_company", 130, "select a.id, a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=23 and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-Wash. Company-", $data[2], "" );
		// echo "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name";die;
	}	
	exit();	 
}

if ($action == "load_variable_settings") 
{

	extract($_REQUEST);
	//echo " select format_id from lib_report_template where template_name ='".$data."'  and module_id=7 and report_id=86 and is_deleted=0 and status_active=1";
	$print_report_format = return_field_value("format_id", " lib_report_template", "template_name ='" . $data . "'  and module_id=7 and report_id=86 and is_deleted=0 and status_active=1");
	
	
	// =====================================================
	echo "$('#sewing_production_variable').val(0);\n";
	$sql_result = sql_select("select sewing_pcq,production_entry from variable_settings_production where company_name=$data and variable_list=1 and status_active=1");
	// echo $sql_result;die;
	foreach ($sql_result as $result) {
		echo "$('#sewing_production_variable').val(" . $result[csf("sewing_pcq")] . ");\n";
		echo "$('#styleOrOrderWisw').val(" . $result[csf("production_entry")] . ");\n";
		if ($result[csf("sewing_pcq")] == 1) {
			echo "$('#txt_ex_quantity').attr('readonly',false);\n";
		}
	}
	
	$control_and_preceding = sql_select("select is_control, preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=32 and company_name='$data'");
	
	echo "$('#hidden_variable_cntl').val('0');\n";
	
	
	if (!$control_and_preceding[0][csf("is_control")]) $variable_is_control = 0;
	echo "document.getElementById('variable_is_controll').value='" . $variable_is_control . "';\n";
	echo "$('#hidden_variable_cntl').val('" . $variable_is_control . "');\n";
	echo "document.getElementById('txt_qty_source').value='" . $control_and_preceding[0][csf("preceding_page_id")] . "';\n";
	echo "$('#hidden_preceding_process').val('" . $control_and_preceding[0][csf("preceding_page_id")] . "');\n";
	$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
	$qty_source = 0;
	if ($preceding_process == 29) $qty_source = 5; //Sewing Output
	else if ($preceding_process == 30) $qty_source = 7; //Iron Output
	else if ($preceding_process == 31) $qty_source = 8; //Packing And Finishing
	else if ($preceding_process == 260) $qty_source = 82; //Finish gmts issue
	else if ($preceding_process == 277) $qty_source = 81; //Finish gmts rcv
	else if ($preceding_process == 276) $qty_source = 14; //Garments Finishing Delivery
	else if ($preceding_process == 91) $qty_source = 91; //Buyer Inspection
	else if ($preceding_process == 103) $qty_source = 11; //Poly Entry
	
	if ($qty_source != 0) {
		echo "$('#source_msg').text('');\n";
		if ($qty_source == 4) {
			echo "$('#source_msg').text('Sewing Input Qty');\n";
		} else if ($qty_source == 5) {
			echo "$('#source_msg').text('Sewing Output Qty');\n";
		} else if ($qty_source == 7) {
			echo "$('#source_msg').text('Iron Qty');\n";
		}else if ($qty_source == 8) {
			echo "$('#source_msg').text('Packing And Finishing');\n";
		} else if ($qty_source == 11) {
			echo "$('#source_msg').text('Poly Entry Qty');\n";
		} else if ($qty_source == 91) {
			echo "$('#source_msg').text('Buyer Inspection Qty');\n";
		} else if ($qty_source == 82) {
			echo "$('#source_msg').text('Finish Gmts Issue Qty');\n";
		} else if ($qty_source == 14) {
			echo "$('#source_msg').text('Gmts Finish Del. Qty.');\n";
		} else if ($qty_source == 81) {
			echo "$('#source_msg').text('Finish Gmts Rec. Qty.');\n";
		} else {
			echo "$('#source_msg').text('Sewing Finish Qty');\n";
		}
	}

	
	exit();
}


if ($action == "sys_surch_popup") {
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info", "../../../", 1, 1, $unicode);

	?>
	<script>
		var company_id = '<? echo $company_id; ?>';

		function js_set_value(str) {
			$("#hidden_delivery_id").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>

	<body>
		<div align="center" style="width:100%;">
			<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
				<table width="950" cellspacing="0" cellpadding="0" border="1" class="rpt_table" align="center" rules="all">
					<thead>
						<th width="130">Company Name</th>
						<th width="120">Buyer Name</th>
						<th width="100">Receive No</th>
						<th width="100">Job No</th>
						<th width="100">Style Ref.</th>
						<th width="100">Order No</th>
						<th width="180">Date Range</th>
						<th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
					</thead>
					
							<td width="140">
									<?
									echo create_drop_down("cbo_company_name", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name", "id,company_name", 1, "-- Select Company --", '', "load_drop_down_multiple( 'requires/garments_delivery_entry_controller', this.value, 'load_drop_down_multiple', 'location_td*transfer_com*forwarder_td*forwarder_td2' ); get_php_form_data(this.value,'load_variable_settings','requires/garments_delivery_entry_controller');", 0); ?>
									<input type="hidden" name="sewing_production_variable" id="sewing_production_variable" value="" />
									<input type="hidden" name="check_posted_in_accounce" id="check_posted_in_accounce" value="" />

									<input type="hidden" id="styleOrOrderWisw" />
									<input type="hidden" id="variable_is_controll" />
									<input type="hidden" id="txt_user_lebel" value="<? echo $level; ?>" />
									<input type="hidden" id="txt_qty_source" />
									<input type="hidden" id="is_update_mood" />
                            		<input type="hidden" id="wip_valuation_for_accounts" />
                                    <input type="hidden" name="hidden_variable_cntl" id="hidden_variable_cntl" value="0">
									<input type="hidden" name="hidden_preceding_process" id="hidden_preceding_process" value="0">
								</td>
						

							<td id="buyer_td"><? echo create_drop_down("cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, ""); ?></td>

							<td><input name="txt_issue_no" id="txt_issue_no" class="text_boxes" style="width:90px"  placeholder="Write"/></td>
							<td><input name="txt_job_no" id="txt_job_no" class="text_boxes" style="width:90px"  placeholder="Write"/></td>
							<td><input name="txt_style_ref" id="txt_style_ref" class="text_boxes" style="width:90px"  placeholder="Write"/></td>
							<td><input name="txt_order_no" id="txt_order_no" class="text_boxes" style="width:90px" placeholder="Write" /></td>
							<td>
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
							</td>
								<td>
							<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_issue_no').value+'_'+document.getElementById('txt_job_no').value+'_'+document.getElementById('txt_style_ref').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_company_name').value+'_'+document.getElementById('txt_order_no').value+'_'+document.getElementById('cbo_buyer_name').value, 'create_delivery_search_list', 'search_div_delivery', 'wash_receive_controller','setFilterGrid(\'tbl_invoice_list\',-1)')" style="width:100px;" />
						</td>
					</tr>
					<tr>
						<td align="center" colspan="8" valign="middle">
							<? echo load_month_buttons(1);  ?>
							<input type="hidden" id="hidden_delivery_id">
						</td>
					</tr>
				</table>
				<div id="search_div_delivery" style="margin-top:20px;"></div>
			</form>
		</div>
		<script type="text/javascript">
			$("#cbo_company_name").val(company_id);
		</script>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
	exit();
}
if ($action == "create_delivery_search_list") {
	
    $ex_data = explode("_",$data);
	$issue_no = $ex_data[0];
	$job_no = $ex_data[1];
	$style_ref= $ex_data[2];
	$from_date = $ex_data[3];
	$to_date = $ex_data[4];
	$company = $ex_data[5];	
	$order_no= $ex_data[6];
	$buyer = $ex_data[8];
	
	// echo "<pre>";print_r($ex_data);die;
	
	// if(str_replace("'","",$company)==0) { echo "Please select company First"; die;}
    if($db_type==2)
	{ 
		$year_cond=" and extract(year from a.insert_date)=$cut_year"; 
		$year=" extract(year from a.insert_date) as year";
	}
    else  if($db_type==0)
	{ 
		$year_cond=" and SUBSTRING_INDEX(a.insert_date, '-', 1)=$cut_year"; 
		$year=" SUBSTRING_INDEX(a.insert_date, '-', 1) as year";
	}
	if(str_replace("'","",$company)==0) $conpany_cond=""; else $conpany_cond="and a.company_id=".str_replace("'","",$company)."";
	if(str_replace("'","",$job_no)=="") $job_cond=""; else $job_cond="and c.job_no_prefix_num='".str_replace("'","",$job_no)."'";
	if(str_replace("'","",$buyer)=="") $buyer_cond=""; else $buyer_cond="and c.buyer_name='".str_replace("'","",$buyer)."'";
	if(str_replace("'","",$issue_no)=="") $system_cond=""; else $system_cond="and a.sys_number_prefix_num='".str_replace("'","",$issue_no)."'";
	if(str_replace("'","",$order_no)=="") $order_cond=""; else $order_cond=" and d.po_number='".str_replace("'","",$order_no)."'";
	if(str_replace("'","",$style_ref)=="") $style_ref_cond=""; else $style_ref_cond="and c.style_ref_no='".str_replace("'","",$style_ref)."'";
	
	$company_arr = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$location_arr = return_library_array("select id,location_name from lib_location", 'id', 'location_name');
	$floor_arr = return_library_array("select id,floor_name from lib_prod_floor", 'id', 'floor_name');
	
	if( $from_date!="" && $to_date!="" )
	{
		if($db_type==0)
		{
			$sql_cond= " and a.delivery_date between '".change_date_format($from_date,'yyyy-mm-dd')."' and '".change_date_format($to_date,'yyyy-mm-dd')."'";
		}
		if($db_type==2)
		{
			$sql_cond= " and a.delivery_date between '".change_date_format($from_date,'yyyy-mm-dd','-',1)."' and '".change_date_format($to_date,'yyyy-mm-dd','-',1)."'";
		}
	}
	
	$sql_pop="SELECT a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.production_source, a.working_company_id, a.working_location_id, a.floor_id, a.job_id, a.job_no, a.delivery_date, a.challan_no, a.remarks, c.style_ref_no, c.buyer_name,b.item_number_id,b.country_id,d.po_number, $year
    FROM pro_gmts_delivery_mst a, pro_garments_production_mst b, wo_po_details_master c, wo_po_break_down d
    where c.garments_nature=100 and a.entry_form=625 and a.id=b.delivery_mst_id and b.po_break_down_id=d.id and c.job_no=d.job_no_mst $conpany_cond $job_cond $sql_cond $order_cond $system_cond $buyer_cond $style_ref_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.sys_number_prefix_num, a.sys_number, a.company_id, a.location_id, a.production_source, a.working_company_id, a.working_location_id, a.floor_id, a.job_id, a.job_no, a.delivery_date, a.embel_name, a.embel_type, a.sending_company, a.sending_location, a.challan_no, a.remarks, c.style_ref_no, c.buyer_name, a.insert_date,b.item_number_id,b.country_id,d.po_number order by a.id DESC";
	// echo $sql_pop;die;
	
	$sql_pop_res=sql_select($sql_pop);
	?>
     <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="985" >
        <thead>
            <th width="30">SL</th>
            <th width="100">Location</th>
            <th width="60">Receive Year</th>
            <th width="60">Receive No</th>
            <th width="120">Wash. Company</th>
            <th width="120">Wash. Location</th>
            <th width="80">Floor</th>
            <th width="70">Receive Date</th>
            <th width="110">Job No</th>
            <th>Style Ref.</th>
        </thead>
        </table>
        <div style="width:985px; max-height:270px;overflow-y:scroll;" >	 
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="965" class="rpt_table" id="list_view">
        <tbody>
            <? 
            $i=1;
            foreach($sql_pop_res as $row)
            {  
                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
				$sending_location=0;
				if($row[csf('sending_company')]!=0) $sending_location=$row[csf('sending_location')].'*'.$row[csf('sending_company')]
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('id')].'_'.$row[csf('sys_number')].'_'.$row[csf('company_id')].'_'.$row[csf('location_id')].'_'.$row[csf('production_source')].'_'.$row[csf('working_company_id')].'_'.$row[csf('working_location_id')].'_'.$row[csf('floor_id')].'_'.$row[csf('job_id')].'_'.$row[csf('job_no')].'_'.change_date_format($row[csf('delivery_date')]).'_'.$row[csf('embel_name')].'_'.$row[csf('embel_type')].'_'.$sending_location.'_'.$row[csf('challan_no')].'_'.$row[csf('remarks')].'_'.$row[csf('style_ref_no')].'_'.$row[csf('buyer_name')]; ?>')" style="cursor:pointer" >
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $location_arr[$row[csf('location_id')]]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('year')]; ?></td>
                    <td width="60" align="center"><? echo $row[csf('sys_number_prefix_num')]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $company_arr[$row[csf('working_company_id')]]; ?></td>
                    <td width="120" style="word-break:break-all"><? echo $location_arr[$row[csf('working_location_id')]]; ?></td>
                    <td width="80" style="word-break:break-all"><? echo $floor_arr[$row[csf('floor_id')]]; ?></td>
                    <td width="70" style="text-align:center;"><? echo change_date_format($row[csf('delivery_date')]); ?></td>
                    <td width="110" style="word-break:break-all"><? echo $row[csf('job_no')]; ?></td>	
                    <td style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>
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

//$order_num_arr=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");
$order_sql = sql_select("SELECT id, po_number,sum(po_quantity) as po_quantity  from wo_po_break_down group by id, po_number");
foreach ($order_sql as $val) {
	$order_num_arr[$val[csf("id")]] = $val[csf("po_number")];
	$order_qnty_arr[$val[csf("id")]] += $val[csf("po_quantity")];
}

if ($action == "load_drop_down_buyer") {
	if ($data != 0) {
		echo create_drop_down("cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
		exit();
	} else {
		echo create_drop_down("cbo_buyer_name", 120, "SELECT buy.id as id ,buy.buyer_name as buyer_name  from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
		exit();
	}
}




if ($action == "show_dtls_listview_mst") {
	echo load_html_head_contents("Popup Info", "../", 1, 1, $unicode);
	$country_short_array = return_library_array("select id,short_name from lib_country", "id", "short_name");
 ?>
	<div style="width:1200px">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
			<thead>
			<th width="20">SL</th>
	                <th width="150" align="center">Item Name</th>
	                <th width="100" align="center">Country</th>
	                <th width="70" align="center">Rcv. Date</th>
	                <th width="60" align="center">Rcv. Qnty</th>	               
	                <th width="120" align="center">Serving Company</th>
	                <th width="100" align="center">Location</th>
	                <th width="100" align="center">Floor</th>
			</thead>
		</table>
	</div>
	<div style="width:1200px;max-height:180px; overflow:y-scroll" id="sewing_production_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="details_table">
			<?
			$i = 1;
			$total_production_qnty = 0;
			$sqlEx = sql_select("select id,invoice_no,is_lc,lc_sc_id from com_export_invoice_ship_mst where status_active=1");
			foreach ($sqlEx as $row) {
				$invoice_data_arr[$row[csf("id")]]["id"] = $row[csf("id")];
				$invoice_data_arr[$row[csf("id")]]["invoice_no"] = $row[csf("invoice_no")];
				$invoice_data_arr[$row[csf("id")]]["is_lc"] = $row[csf("is_lc")];
				$invoice_data_arr[$row[csf("id")]]["lc_sc_id"] = $row[csf("lc_sc_id")];
			}
			//echo "select id,po_break_down_id,item_number_id,country_id,ex_factory_date,ex_factory_qnty,location,lc_sc_no,invoice_no,challan_no from  pro_ex_factory_mst where delivery_mst_id=$data and status_active=1 and is_deleted=0 order by id";
			$sqlResult = sql_select("SELECT a.id,a.po_break_down_id,a.item_number_id,a.country_id,a.ex_factory_date,a.ex_factory_qnty,a.location,a.lc_sc_no,a.invoice_no,b.challan_no,a.shiping_status from  pro_ex_factory_mst a,  pro_ex_factory_delivery_mst b where a.delivery_mst_id=b.id and  a.delivery_mst_id=$data and b.entry_form!=85 and a.status_active=1 and a.is_deleted=0 order by id");
			$po_id_arr = array(); $mst_id_arr = array();
			foreach ($sqlResult as $row) {
				$po_id_arr[$row[csf('po_break_down_id')]] = $row[csf('po_break_down_id')];
				$mst_id_arr[$row[csf('id')]] = $row[csf('id')];
			}
			$mst_ids = implode(",", $mst_id_arr);
			$allPoId = implode(",", $po_id_arr);
			$style_sql = "SELECT a.style_ref_no,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($allPoId)";
			$style_sql_res = sql_select($style_sql);
			$style_ref_arr = array();
			foreach ($style_sql_res as $val) {
				$style_ref_arr[$val[csf('id')]] = $val[csf('style_ref_no')];
			}
			
			$actual_po_library = return_library_array("SELECT id, acc_po_no from wo_po_acc_po_info where po_break_down_id in($allPoId)", 'id', 'acc_po_no');
			
			$actual_po = sql_select("SELECT mst_id, actual_po_id from pro_ex_factory_actual_po_details where mst_id in($mst_ids) and status_Active=1 and is_deleted=0 group by mst_id, actual_po_id");
			$acc_po_arr = array();
			foreach ($actual_po as $val) 
			{
				$acc_po_arr[$val[csf('mst_id')]]=$actual_po_library[$val[csf('actual_po_id')]];
			}
			unset($actual_po);

			foreach ($sqlResult as $selectResult) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$total_production_qnty += $selectResult[csf('ex_factory_qnty')];
				if ($invoice_data_arr[$selectResult[csf("invoice_no")]]["is_lc"] == 1) //  lc
					$lc_sc = $lc_num_arr[$invoice_data_arr[$selectResult[csf("invoice_no")]]["lc_sc_id"]];
				else if ($invoice_data_arr[$selectResult[csf("invoice_no")]]["is_lc"] == 2)
					$lc_sc = $sc_num_arr[$invoice_data_arr[$selectResult[csf("invoice_no")]]["lc_sc_id"]];

				$invoiceNo = $invoice_data_arr[$selectResult[csf("invoice_no")]]["invoice_no"];
				//$order_num_arr
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data(<? echo $selectResult[csf('id')]; ?>,'populate_exfactory_form_data','requires/wash_receive_controller');get_php_form_data('<? echo $selectResult[csf('po_break_down_id')]; ?>+**+<? echo $selectResult[csf('item_number_id')]; ?>+**+<? echo $selectResult[csf('country_id')]; ?>'+'**'+$('#hidden_preceding_process').val()+'**'+$('#txt_mst_id').val()+'**1'+'**'+$('#sewing_production_variable').val()+'**'+$('#variable_is_controll').val()+'**'+$('#txt_country_ship_date').val()+'**'+$('#txt_pack_type').val(),'populate_data_from_search_popup','requires/wash_receive_controller');">
                
					<td style="word-break: break-all;word-wrap: break-word;" width="20" align="center"><? echo $i; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;" width="120" align="center">
						<p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p>
					</td>
					
					<td style="word-break: break-all;word-wrap: break-word;" width="110" align="center">
						<p><? echo $country_library[$selectResult[csf('country_id')]]; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="60" align="center">
						<p><? echo $country_short_array[$selectResult[csf('country_id')]]; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="120" align="left">
						<p><? echo $style_ref_arr[$selectResult[csf('po_break_down_id')]]; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="100" align="center">
						<p><? echo $order_num_arr[$selectResult[csf('po_break_down_id')]]; ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="80" align="center">
						<p><? echo $order_qnty_arr[$selectResult[csf('po_break_down_id')]]; ?></p>
					</td>
                    <td style="word-break: break-all;word-wrap: break-word;" width="90" align="center">
						<p><? echo $acc_po_arr[$selectResult[csf('id')]]; ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="60" align="center">
						<p><? echo change_date_format($selectResult[csf('ex_factory_date')]); ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="60" align="center">
						<p><? echo $selectResult[csf('ex_factory_qnty')]; ?></p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="90" align="center">
						<p><? echo $invoiceNo; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" width="90" align="center">
						<p><? echo $lc_sc; ?>&nbsp;</p>
					</td>
					<td width="70" style="word-break: break-all;word-wrap: break-word;" align="center">
						<p><? echo $selectResult[csf('challan_no')]; ?>&nbsp;</p>
					</td>
					<td style="word-break: break-all;word-wrap: break-word;" align="center">
						<p><? echo $shipment_status[$selectResult[csf('shiping_status')]]; ?>&nbsp;</p>
					</td>
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




if($action=="order_popup")
{
	//print_r($_REQUEST);die;
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	?>
	<script>
		$(document).ready(function(e) 
		{
            $("#txt_search_common").focus();
			$("#company_search_by").val('<?php echo $_REQUEST['company'] ?>');
        });

		function search_populate(str)
		{
			//alert(str);
	// onkeydown="if (event.keyCode == 13) document.getElementById('btn_show').click()"
			if(str==0)
			{
				document.getElementById('search_by_th_up').innerHTML="Order No";
				document.getElementById('search_by_td').innerHTML='<input 	type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value="" onKeyDown="getActionOnEnter(event)" />';
			}
			else if(str==1)
			{
				document.getElementById('search_by_th_up').innerHTML="Style Ref. Number";
				document.getElementById('search_by_td').innerHTML='<input  type="text"	name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value="" onKeyDown="getActionOnEnter(event)"  />';
			}
			else if(str==3)
			{
				document.getElementById('search_by_th_up').innerHTML="Job No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==4)
			{
				document.getElementById('search_by_th_up').innerHTML="Actual PO No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==5)
			{
				document.getElementById('search_by_th_up').innerHTML="File No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else if(str==6)
			{
				document.getElementById('search_by_th_up').innerHTML="Internal Ref. No";
				document.getElementById('search_by_td').innerHTML='<input type="text" name="txt_search_common" style="width:230px " class="text_boxes" id="txt_search_common"	value=""  />';
			}
			else //if(str==2)
			{
				load_drop_down( 'wash_receive_controller',document.getElementById('company_search_by').value,'load_drop_down_buyer', 'search_by_td' );
				document.getElementById('search_by_th_up').innerHTML="Select Buyer Name";				
			}
		}

	function js_set_value(id,item_id,country_id,job_num,company)
	{
		$("#hidden_mst_id").val(id);
		$("#hidden_grmtItem_id").val(item_id);
		// $("#hidden_po_qnty").val(po_qnty);
		// $("#hidden_plancut_qnty").val(plan_qnty);

		$("#hidden_country_id").val(country_id);
		$("#hid_job_num").val(job_num);
		$("#hid_company_id").val(company);
   		parent.emailwindow.hide();
 	}

    </script>
	</head>
	<body>
	<div align="center" style="width:100%;" >
	    <form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	        <table width="990" cellspacing="0" cellpadding="0" class="rpt_table" align="center" border="1" rules="all">
	    		<tr>
	        		<td align="center" width="100%">
	            		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
	                   		 <thead>
	                        	<th width="130" class="must_entry_caption">Company Name</th>
	                        	<th width="130">Search By</th>
	                        	<th  width="180" align="center" id="search_by_th_up">Enter Order Number</th>
	                        	<th width="250">Date Range</th>
	                        	<th width="80"><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:100px;" /></th>
	                    	</thead>
	        				<tr>
	        				<td width="130">
	        				 <? echo create_drop_down( "company_search_by", 210, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $selected, " ",0 );
	                                        ?>
							</td>
	                    		<td width="130">
									<?
	                                    $searchby_arr=array(0=>"Order No",1=>"Style Ref. Number",2=>"Buyer Name",3=>"Job No",4=>"Actual PO No",5=>"File No",6=>"Internal Ref. No");
	                                    echo create_drop_down( "txt_search_by", 130, $searchby_arr,"", 1, "-- Select --", $selected, "search_populate(this.value)",0 );
	                                ?>
	                    		</td>
	                   			<td width="180" align="center" id="search_by_td">
									<input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" onKeyDown="if (event.keyCode == 13) document.getElementById('btn_show').click()" />
	            				</td>
	                    		<td align="center">
	                            	<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:80px"> To
						  			<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:80px">
						 		</td>
	            		 		<td align="center">
	                     			<input type="button" name="btn_show" id="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('company_search_by').value+'_'+<? echo $garments_nature; ?>+'_'+document.getElementById('cbo_year_selection').value, 'create_po_search_list_view', 'search_div', 'wash_receive_controller', 'setFilterGrid(\'tbl_po_list\',-1)')" style="width:100px;" />
	                            </td>
	        				</tr>
	             		</table>
	          		</td>
	        	</tr>
	        	<tr>
	            	<td  align="center" height="40" valign="middle">
						<? echo load_month_buttons(1);  ?>
	                    <input type="hidden" id="hidden_mst_id">
	                    <input type="hidden" id="hidden_grmtItem_id">
	                
	                    <input type="hidden" id="hidden_country_id">
	                    <input type="hidden" id="hid_job_num">
	                    <input type="hidden" id="hid_company_id">
	          		</td>
	            </tr>
	    	</table>
	        <div style="margin-top:10px" id="search_div"></div>
	    </form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{

 	$ex_data = explode("_",$data);
 	//echo '<pre>';print_r($data);
 	if($ex_data[4]== 0)
	{
		//print_r ($data);die;
		echo "Please Select Company First."; die;
	}
	$txt_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
 	$garments_nature = $ex_data[5];
 	$year = $ex_data[6];
	//print_r ($ex_data);
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==0)
			$sql_cond = " and b.po_number like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==1)
			$sql_cond = " and a.style_ref_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==2)
			$sql_cond = " and a.buyer_name='$txt_search_common'";
		else if(trim($txt_search_by)==3)
			$sql_cond = " and a.job_no like '%".trim($txt_search_common)."'";
		else if(trim($txt_search_by)==4)
			$sql_cond = " and c.acc_po_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==5)
			$sql_cond = " and b.file_no like '%".trim($txt_search_common)."%'";
		else if(trim($txt_search_by)==6)
			$sql_cond = " and b.grouping like '%".trim($txt_search_common)."%'";
 	}

	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0){$sql_cond .= " and b.shipment_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";}
		if($db_type==2 || $db_type==1){ $sql_cond .= " and b.shipment_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_name='$company'";

	if(trim($txt_search_by)==4 && trim($txt_search_common)!="")
	{
		$sql = "SELECT b.id,a.order_uom,a.buyer_name,b.grouping,b.file_no,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b, wo_po_acc_po_info c
			where
			a.id = b.job_id and
			b.id=c.po_break_down_id and
			a.status_active=1 and
			a.is_deleted=0 and
			b.status_active=1 and
			b.is_deleted=0 and
			c.status_active=1 and
			c.is_deleted=0 and
			
			a.garments_nature=$garments_nature
			$sql_cond group by b.id,a.order_uom,a.buyer_name,b.grouping,b.file_no,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity,b.plan_cut, b.t_year order by b.shipment_date";
	}
	else
	{
 		$sql = "SELECT b.id,a.order_uom,a.buyer_name,b.grouping,b.file_no,a.company_name,a.total_set_qnty,a.set_break_down, a.job_no,a.style_ref_no,a.gmts_item_id,a.location_name,b.shipment_date,b.po_number,b.po_quantity ,b.plan_cut
			from wo_po_details_master a, wo_po_break_down b
			where
			a.id = b.job_id and
			a.status_active=1 and
			a.is_deleted=0 and
			b.status_active=1 and
			b.is_deleted=0 and
			
			a.garments_nature=$garments_nature
			$sql_cond order by b.shipment_date";
	}
	//echo $sql;//die;
	$result = sql_select($sql);

	$po_id_array = array();
	foreach ($result as $val) 
	{
		$po_id_array[$val['ID']] = $val['ID'];
	}

	$po_id_cond = where_con_using_array($po_id_array,0,"po_break_down_id");

 	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	/*if($db_type==0)
	{
		$po_country_arr=return_library_array( "select po_break_down_id, group_concat(country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}
	else
	{
		$po_country_arr=return_library_array( "select po_break_down_id, listagg(CAST(country_id as VARCHAR(4000)),',') within group (order by country_id) as country from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','country');
	}*/

	$po_country_data_arr=array(); $pocountry_arr=array();
	$poCountryData=sql_select( "SELECT po_break_down_id, item_number_id, country_id, sum(order_quantity) as qnty, sum(plan_cut_qnty) as plan_cut_qnty from wo_po_color_size_breakdown where status_active=1 and is_deleted=0 $po_id_cond group by po_break_down_id, item_number_id, country_id");

	foreach($poCountryData as $row)
	{
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['po_qnty']=$row[csf('qnty')];
		$po_country_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]['plan_cut_qnty']=$row[csf('plan_cut_qnty')];
		$pocountry_arr[$row[csf('po_break_down_id')]].=$row[csf('country_id')].',';
	}

	$total_cut_data_arr=array();
	$total_cut_qty_arr=sql_select("SELECT po_break_down_id, item_number_id, country_id, sum(production_quantity) as production_quantity from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=111 $po_id_cond group by po_break_down_id, item_number_id, country_id");

	foreach($total_cut_qty_arr as $row)
	{
		$total_cut_data_arr[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]][$row[csf('country_id')]]=$row[csf('production_quantity')];
	}

	?>
    <div style="width:1290px;">
     	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
                <th width="30">SL</th>
                <th width="70">Shipment Date</th>
                <th width="150">Order No</th>
                <th width="110">Job No</th>
                <th width="120">Buyer</th>
                <th width="120">Style</th>
                <th width="80">File No</th>
                <th width="80">Ref. No</th>
                <th width="140">Item</th>
                <th width="100">Country</th>
                <th width="80">Order Qty</th>
                <th width="80">Wash Qty</th>
                
                <th >Balance</th>
               
            </thead>
     	</table>
     </div>
     <div style="width:1290px; max-height:240px;overflow-y:scroll;" >
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1272" class="rpt_table" id="tbl_po_list" >
        	
        		
        	
			<?
			$i=1;
            foreach( $result as $row )
            {
				$exp_grmts_item = explode("__",$row[csf("set_break_down")]);
				$numOfItem = count($exp_grmts_item);
				$set_qty=""; $grmts_item="";$job_num="";
				$job_num=$row[csf("job_no")];
				$company_id=$row[csf("company_name")];
				$pocountry_id=chop($pocountry_arr[$row[csf("id")]],',');
				$country=array_unique(explode(",",$pocountry_id));
				//print_r($country);
				$numOfCountry = count($country);

				for($k=0;$k<$numOfItem;$k++)
				{
					if($row["total_set_qnty"]>1)
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}else
					{
						$grmts_item_qty = explode("_",$exp_grmts_item[$k]);
						$grmts_item = $grmts_item_qty[0];
						$set_qty = $grmts_item_qty[1];
					}

					foreach($country as $country_id)
					{
						if ($i%2==0)  $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

						//$po_qnty=$row[csf("po_quantity")]; $plan_cut_qnty=$row[csf("plan_cut")];
						$po_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['po_qnty'];
						$plan_cut_qnty=$po_country_data_arr[$row[csf('id')]][$grmts_item][$country_id]['plan_cut_qnty'];
						$trim_qnty=$total_cut_data_arr[$row[csf('id')]][$grmts_item][$country_id];

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none;cursor:pointer" onClick="js_set_value(<? echo $row[csf("id")];?>,'<? echo $grmts_item;?>','<? echo $country_id;?>','<? echo$job_num;?>','<? echo $company_id;?>');" >
                            <td width="30" align="center"><?php echo $i; ?></td>
                            <td width="70" align="center"><?php echo change_date_format($row[csf("shipment_date")]);?></td>
                            <td width="150"><p><?php echo $row[csf("po_number")]; ?></p></td>
                            <td width="110"><p><?php echo $job_num; ?></p></td>
                            <td width="120"><p><?php echo $buyer_arr[$row[csf("buyer_name")]]; ?></p></td>
                            <td width="120"><p><?php echo $row[csf("style_ref_no")]; ?></p></td>
                            <td width="80"><p><?php echo $row[csf("file_no")]; ?></p></td>
                            <td width="80"><p><?php echo $row[csf("grouping")]; ?></p></td>
                            <td width="140"><p><?php  echo $garments_item[$grmts_item];?></p></td>
                            <td width="100"><p><?php echo $country_library[$country_id]; ?>&nbsp;</p></td>
                            <td width="80" align="right"><?php echo $po_qnty; ?>&nbsp;</td>
                            <td width="80" align="right"><?php echo $trim_qnty; ?>&nbsp;</td>
                          
                            <td  align="right">
                                <?php
                                    $balance=$po_qnty-$trim_qnty;
                                    echo $balance;
                                ?>&nbsp;
                            </td>
                            
                        </tr>
						<?
						$i++;
					}
				}
            }
   		?>

        </table>
    </div>
	<?
	exit();
}




if($action=="populate_data_from_search_popup")
{
	$dataArr = explode("**",$data);
	$po_id = $dataArr[0];
	$item_id = $dataArr[1];
	$country_id = $dataArr[2];
	$preceding_process = $dataArr[3];

	$qty_source=111;
	// if($preceding_process==4) $qty_source=4; //Linking Complete
	// else if($preceding_process==111) $qty_source=111; //knitting Complete

	$res = sql_select("SELECT a.id,a.po_quantity,a.plan_cut, a.po_number,a.po_quantity,b.company_name, b.buyer_name, b.style_ref_no,b.gmts_item_id, b.order_uom, b.job_no,b.location_name
			from wo_po_break_down a, wo_po_details_master b
			where a.job_id=b.id and a.id=$po_id");

 	foreach($res as $result)
	{
		echo "$('#txt_order_no').val('".$result[csf('po_number')]."');\n";
		echo "$('#hidden_po_break_down_id').val('".$result[csf('id')]."');\n";
		echo "$('#cbo_buyer_name').val('".$result[csf('buyer_name')]."');\n";
		echo "$('#txt_style_ref').val('".$result[csf('style_ref_no')]."');\n";
		echo "$('#txt_job_no').val('".$result[csf('job_no')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('gmts_item_id')]."');\n";
 		echo "$('#txt_trimming_qty').attr('placeholder','');\n";//initialize quatity input field

 		
			$dataArray=sql_select("SELECT SUM(CASE WHEN a.production_type=$qty_source and b.production_type=$qty_source THEN b.production_qnty END) as totalinput,SUM(CASE WHEN a.production_type=118 and b.production_type=118  THEN b.production_qnty ELSE 0 END) as totalreceive from pro_garments_production_mst a,pro_garments_production_dtls b WHERE a.id=b.mst_id and  a.po_break_down_id=".$result[csf('id')]." and a.item_number_id='$item_id' and a.country_id='$country_id' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
			// echo $dataArray;die;
			foreach($dataArray as $row)
			{
				echo "$('#txt_trimming_qty').val('".$row[csf('totalinput')]."');\n";
				echo "$('#txt_receive_qty').attr('placeholder','".$row[csf('totalreceive')]."');\n";
				echo "$('#txt_cumul_quantity').val('".$row[csf('totalreceive')]."');\n";
				$yet_to_produced = $row[csf('totalreceive')]-$row[csf('totalinput')];
				echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
				echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
			}
		
   	}
 	exit();
}
if($action=="color_and_size_level")
{
		$dataArr = explode("**",$data);
		$po_id = $dataArr[0];
		$item_id = $dataArr[1];
		$variableSettings = $dataArr[2];
		$styleOrOrderWisw = $dataArr[3];
		$country_id = $dataArr[4];
		$job_num = $dataArr[5];
		$variableSettingsRej = $dataArr[6];
		$preceding_process = $dataArr[7];
		$garments_nature = $dataArr[8];

		$qty_source=111;
	
		$color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		$size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');

		$sqls_col_size="SELECT color_number_id,size_number_id, sum(plan_cut_qnty) as qnty from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active=1 and is_deleted=0 group by color_number_id,size_number_id";
		foreach(sql_select($sqls_col_size) as $key=>$value)
		{
			$po_color_size_qnty_arr[$value[csf("color_number_id")]][$value[csf("size_number_id")]] +=$value[csf("qnty")];
		}


		//#############################################################################################//
		// order wise - color level, color and size level


		//echo "logic123_".$variableSettings;

		
		
			if( $variableSettings==2 ) // color level
			{
				
					$sql = "SELECT a.item_number_id, a.color_number_id, sum(a.order_quantity) as order_quantity, sum(a.plan_cut_qnty) as plan_cut_qnty,sum(b.production_qnty) as production_qnty
					from wo_po_color_size_breakdown a left join pro_garments_production_dtls b on a.id=b.color_size_break_down_id and b.production_type=118
					where a.po_break_down_id='$po_id' and a.item_number_id='$item_id' and a.country_id='$country_id' and a.is_deleted=0 and a.status_active=1 group by a.item_number_id, a.color_number_id";

			}
			else if( $variableSettings==3 ) //color and size level
			{
				$dtlsData =sql_select ("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=$qty_source then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=118 then a.production_qnty ELSE 0 END) as cur_production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,118) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cur_cut']= $row[csf('cur_production_qnty')];
				}

				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order"; //color_number_id, id


			}
			else // by default color and size level
			{
				$dtlsData =sql_select ("SELECT a.color_size_break_down_id,
											sum(CASE WHEN a.production_type=$qty_source then a.production_qnty ELSE 0 END) as production_qnty,
											sum(CASE WHEN a.production_type=118 then a.production_qnty ELSE 0 END) as cur_production_qnty
											from pro_garments_production_dtls a,pro_garments_production_mst b where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id='$po_id' and b.item_number_id='$item_id' and b.country_id='$country_id' and a.color_size_break_down_id!=0 and a.production_type in($qty_source,118) group by a.color_size_break_down_id");

				foreach($dtlsData as $row)
				{
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
					$color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cur_cut']= $row[csf('cur_production_qnty')];
				}

				$sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				from wo_po_color_size_breakdown
				where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";//color_number_id, id
			}
			// echo $dtlsData;die;
			if($variableSettingsRej!=1)
			{
				$disable="";
			}
			else
			{
				$disable="disabled";
			}
			//echo $sql;die;
			$colorResult = sql_select($sql);
	  		$colorHTML="";
			$colorID='';
			$chkColor = array();
			$i=0;$totalQnty=0;
	 		foreach($colorResult as $color)
			{
				if( $variableSettings==2 ) // color level
				{
					$colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';
					$totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
					$colorID .= $color[csf("color_number_id")].",";
				}
				else //color and size level
				{
					if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					{
						if( $i!=0 ) $colorHTML .= "</table></div>";
						$i=0;
						$colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)">  <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
						$colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						$chkColor[] = $color[csf("color_number_id")];
					}
							 $bundle_mst_data="";
							 $bundle_dtls_data="";
						 $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
	 				//$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					$colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					$cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
					$cur_cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cur_cut'];

	 				$colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($cut_qnty-$cur_cut_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'></td><td><input type="text" name="button" id="button_'.$color[csf("color_number_id")].($i+1).'" value="'.$po_color_size_qnty_arr[$color[csf("color_number_id")]][$color[csf("size_number_id")]].'" class="text_boxes_numeric" disabled="" readonly=""  style="size:30px;"  /></td></tr>';
				}

				$i++;
			}
			//echo $colorHTML;die;
			if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			$colorList = substr($colorID,0,-1);
			echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			//#############################################################################################//
			exit();

			
		
}



if($action=="populate_mst_form_data")
{
	$sql ="SELECT a.id, a.sys_number, a.company_id, a.delivery_date, a.floor_id,a.working_company_id,a.location_id,a.remarks,a.production_source,a.working_location_id,a.challan_no,b.country_id,b.item_number_id,c.po_number from pro_gmts_delivery_mst a,pro_garments_production_mst b,wo_po_break_down c where a.id=b.delivery_mst_id and c.id=b.po_break_down_id  and a.id='$data' and a.production_type=118" ;

	// echo $sql.";\n";
	$result =sql_select($sql);
	// /$source_val=$result[0][csf('company_id')]."_".$result[0][csf('production_source')];
	$location_parameter=$result[0][csf('company_id')]."_".$result[0][csf('production_source')];
	//echo $location_parameter; die;
	echo"load_drop_down( 'requires/wash_receive_controller', '".$location_parameter."', 'load_drop_down_working_com', 'wash_company_td' );\n";
	echo"load_drop_down( 'requires/wash_receive_controller', ".$result[0][csf('working_company_id')].", 'load_drop_down_location2', 'working_location_td' );\n";

	echo"load_drop_down( 'requires/wash_receive_controller', ".$result[0][csf('working_location_id')].", 'load_drop_down_floor', 'cbo_floor' );\n";
	

	echo "$('#txt_system_no').val('".$result[0][csf('sys_number')]."');\n";
	echo "$('#txt_system_id').val('".$result[0][csf('id')]."');\n";
	echo "$('#txt_mst_id').val('".$result[0][csf('id')]."');\n";
	echo "$('#cbo_company_name').val('".$result[0][csf('company_id')]."');\n";
	echo "$('#cbo_wash_company').val('".$result[0][csf('working_company_id')]."');\n";
	echo "$('#cbo_location').val('".$result[0][csf('location_id')]."');\n";
	echo "$('#cbo_wash_location').val('".$result[0][csf('working_location_id')]."');\n";
	echo "$('#cbo_floor').val('".$result[0][csf('floor_id')]."');\n";
	echo "$('#txt_issue_date').val('".change_date_format($result[0][csf('delivery_date')])."');\n";
	echo "$('#cbo_source').val('".$result[0][csf('production_source')]."');\n";
	echo "$('#txt_remark').val('".$result[0][csf('remarks')]."');\n";
	echo "$('#txt_challan').val('".$result[0][csf('challan_no')]."');\n";
	echo "$('#cbo_country_name').val('".$result[0][csf('country_id')]."');\n";
	echo "$('#cbo_item_name').val('".$result[0][csf('item_number_id')]."');\n";
	echo "$('#txt_order_no').val('".$result[0][csf('po_number')]."');\n";
	echo "set_button_status(0, permission, 'fnc_washReceive_entry',1,0);\n";
 	exit();
}
if($action=="show_listview")
{
	$dataArr = explode("**",$data);
	//print_r($data);die;
	$lib_supplier = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$lib_company = return_library_array("select id, company_name from lib_company", 'id', 'company_name');
	$lib_location = return_library_array("select id, location_name from lib_location", 'id', 'location_name');
	$lib_floor = return_library_array("select id, floor_name from lib_prod_floor", 'id', 'floor_name');
	$country_library = return_library_array("select id, COUNTRY_NAME from LIB_COUNTRY", 'id', 'COUNTRY_NAME');
	

		
	?>	 
	<div style="width:100%;">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table">
            <thead>
					<th width="20">SL</th>
					<th width="150" align="center">Item Name</th>
					<th width="100" align="center">Country</th>
					<th width="70" align="center">Rcv. Date</th>
					<th width="60" align="center">Rcv. Qnty</th>	               
					<th width="120" align="center">Serving Company</th>
					<th width="100" align="center">Location</th>
					<th width="100" align="center">Floor</th>
            </thead>
		</table>
	</div>
	<div style="width:100%;max-height:180px; overflow:y-scroll" id="wash_list_view" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<?php  
			$i=1;
			$total_production_qnty=0;
			
				$sqlResult =sql_select("SELECT b.id,a.id as mst_id,a.po_break_down_id,a.item_number_id, a.country_id, a.production_date, a.production_quantity,a.production_source, a.serving_company, a.location,a.floor_id from pro_garments_production_mst a,pro_gmts_delivery_mst b where a.delivery_mst_id=$data and b.id=a.delivery_mst_id and a.production_type='118' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.production_date");
			
		//  echo $sqlResult;die;
			foreach($sqlResult as $selectResult){
				
				if ($i%2==0)  $bgcolor="#E9F3FF";
                else $bgcolor="#FFFFFF";
				$total_production_qnty+=$selectResult[csf('production_quantity')];
 		?>
			<tbody>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="get_php_form_data('<? echo $selectResult[csf('id')].'_'.$selectResult[csf('mst_id')]; ?>','populate_input_form_data','requires/wash_receive_controller');" > 
					<td width="20" align="center"><? echo $i; ?></td>
					<td width="150" align="center"><p><? echo $garments_item[$selectResult[csf('item_number_id')]]; ?></p></td>
					<td width="100" align="center"><p>
						<? 
							echo $country_library[$selectResult[csf('country_id')]]."</br>"; 
						
						?>        		
						</p></td>
					<td width="70" align="center"><?php echo change_date_format($selectResult[csf('production_date')]); ?></td>
					<td width="60" align="center"><?php  echo $selectResult[csf('production_quantity')]; ?></td>
					<?php
					$source= $selectResult[csf('production_source')];
					if($source==3)
					$serving_company= return_field_value("supplier_name","lib_supplier","id='".$selectResult[csf('serving_company')]."'");
					else
					$serving_company= return_field_value("company_name","lib_company","id='".$selectResult[csf('serving_company')]."'");
					?>	
					<td width="120" align="center"><p><?php echo $serving_company; ?></p></td>
					<?php 
					$location_name= return_field_value("location_name","lib_location","id='".$selectResult[csf('location')]."'");
					?>
					<td width="100" align="center"><? echo $location_name; ?></td>

					<td width="100"  align="left"><? echo $lib_floor[$selectResult[csf('floor_id')]]; ?></td>
				</tr>

			
			
			<?php
			$i++;
			}
			?>
			</tbody>
             <!--<tfoot>
            	<tr>
                	<th colspan="3"></th>
                    <th><!? echo $total_production_qnty; ?></th>
                    <th colspan="3"></th>
                </tr>
            </tfoot>-->
		</table>
	</div>
	<?
	exit();
}
if($action=="populate_input_form_data")
{
		$data = explode("_",$data);
	
	
		$sqlResult =sql_select("SELECT b.id as sys_id,b.sys_number,a.id, a.company_id, a.garments_nature, a.po_break_down_id, a.item_number_id, a.challan_no, a.country_id,  a.pack_type, a.production_source, a.serving_company, a.sewing_line, a.location, a.produced_by, a.embel_name,  a.production_date, a.production_quantity, a.production_source, a.production_type, a.entry_break_down_type,  a.remarks, a.floor_id,  a.total_produced, a.yet_to_produced,b.location_id ,
		SUM(CASE WHEN a.production_type=111 THEN a.production_quantity END) as totalinput,
		SUM(CASE WHEN a.production_type=118 THEN a.production_quantity ELSE 0 END) as totalreceive
		from pro_garments_production_mst a,pro_gmts_delivery_mst b where b.id='$data[0]'and a.id='$data[1]' and a.delivery_mst_id=b.id and a.production_type in(111,118) and a.status_active=1 and a.is_deleted=0 group by  b.id, a.company_id, a.garments_nature, a.po_break_down_id, a.item_number_id, a.challan_no, a.country_id,  a.pack_type, a.production_source, a.serving_company, a.sewing_line, a.location, a.produced_by, a.embel_name,  a.production_date, a.production_quantity, a.production_source, a.production_type, a.entry_break_down_type,  a.remarks, a.floor_id,  a.total_produced, a.yet_to_produced,b.location_id,b.sys_number,a.id  order by b.id");
		// echo $sqlResult	;die;
	
	$company_id=$sqlResult[0][csf('company_id')];
	$po_id=$sqlResult[0][csf('po_break_down_id')];
	$item_id=$sqlResult[0][csf('item_number_id')];
	$country_id=$sqlResult[0][csf('country_id')];
	$country_qnty=return_field_value("sum(order_quantity)","wo_po_color_size_breakdown","po_break_down_id=$po_id and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1");	
	$dissable='';	
	if($sqlResult[0][csf('production_source')]==1)
	{
		$company=$sqlResult[0][csf('serving_company')];
	}
	else
	{
		$company=$sqlResult[0][csf('company_id')];
	}		 
	

	$qty_source=111;

	$po_id = $sqlResult[0][csf('po_break_down_id')];
	$sql = sql_select("SELECT a.buyer_name,a.style_ref_no,a.job_no,b.po_quantity,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and b.id=$po_id and b.status_active=1 and b.is_deleted=0");
	// echo $sql;die;
	
	foreach(($sqlResult) as $result)
	{ 
		echo "$('#txt_system_id').val('".$result['SYS_ID']."');\n";
		echo "$('#txt_system_no').val('".$result['SYS_NUMBER']."');\n";
		echo "$('#txt_issue_date').val('".change_date_format($result[csf('production_date')])."');\n";
		echo "$('#cbo_source').val('".$result[csf('production_source')]."');\n";
		echo "load_drop_down( 'requires/wash_receive_controller', ".$result[csf('production_source')].", 'load_drop_down_source', '' );\n";
		echo "load_drop_down( 'requires/wash_receive_controller',".$result[csf('serving_company')].", 'load_drop_down_location2', 'working_location_td' );";
		echo "$('#cbo_wash_company').val('".$result[csf('serving_company')]."');\n";
		echo "$('#cbo_wash_location').val('".$result[csf('location')]."');\n";
		echo "$('#cbo_location').val('".$result[csf('location_id')]."');\n";
		echo "load_drop_down( 'requires/wash_receive_controller', ".$result[csf('location')].", 'load_drop_down_floor', 'floor_td' );\n";
		echo "$('#cbo_floor').val('".$result[csf('floor_id')]."');\n";
		echo "$('#cbo_country_name').val('".$result[csf('country_id')]."');\n";
		echo "$('#cbo_item_name').val('".$result[csf('item_number_id')]."');\n";
		echo "$('#txt_mst_id').val('".$result['ID']."');\n";

		echo "$('#txt_job_no').val('".$sql[0]['JOB_NO']."');\n";
		echo "$('#txt_style_no').val('".$sql[0]['STYLE_REF_NO']."');\n";
		echo "$('#cbo_buyer_name').val('".$sql[0]['BUYER_NAME']."');\n";
		echo "$('#txt_order_qty').val('".$sql[0]['PO_QUANTITY']."');\n";
		echo "$('#txt_order_no').val('".$sql[0]['PO_NUMBER']."');\n";
		echo "$('#hidden_po_break_down_id').val('$po_id');\n";
		echo "$('#txt_challan').val('".$sql[0]['challan_no']."');\n";
		$variableSettings = $result[csf('entry_break_down_type')];
		echo "$('#sewing_production_variable').val('".$variableSettings."');\n";

		if($result[csf('production_source')]==3)
		{
			$company=$sqlResult[0][csf('company_id')];
			$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=269 and company_name='$company'");  
			$preceding_process= $control_and_preceding[0][csf("preceding_page_id")];
			echo "$('#hidden_variable_cntl').val('".$control_and_preceding[0][csf('is_control')]."');\n";
			 
		}
 		echo "$('#txt_receive_qty').val('".$result[csf('totalreceive')]."');\n";
		echo "$('#txt_trimming_qty').val('".$result[csf('totalinput')]."');\n";
  		echo "$('#txt_remark').val('".$result[csf('remarks')]."');\n";
				
		
		$dataSql="SELECT 
		SUM(CASE WHEN production_type='$qty_source' THEN production_quantity END) as totalinput,
		SUM(CASE WHEN production_type=118 THEN production_quantity ELSE 0 END) as totalreceive		 
		
		from pro_garments_production_mst WHERE po_break_down_id=".$result[csf('po_break_down_id')]."  and item_number_id=".$result[csf('item_number_id')]." and country_id=".$result[csf('country_id')]."  and status_active=1 and is_deleted=0";
	
		// echo $dataSql;die;

		$dataArray=sql_select($dataSql);
 		foreach($dataArray as $row)
		{  
			
			echo "$('#txt_trimming_qty').val('".$row[csf('totalinput')]."');\n";
			echo "$('#txt_receive_qty').attr('placeholder','".$row[csf('totalreceive')]."');\n";
			echo "$('#txt_cumul_quantity').val('".$row[csf('totalreceive')]."');\n";
			$yet_to_produced = $row[csf('totalreceive')]-$row[csf('totalinput')];
			echo "$('#txt_yet_quantity').attr('placeholder','".$yet_to_produced."');\n";
			echo "$('#txt_yet_quantity').val('".$yet_to_produced."');\n";
		}		
		
 		echo "set_button_status(1, permission, 'fnc_washReceive_entry',1,1);\n";
		
		 
	
		 $qty_source=111;
	
		 $color_library=return_library_array( "select id, color_name from lib_color",'id','color_name');
		 $size_library=return_library_array( "select id, size_name from lib_size",'id','size_name');
 
		 $sqls_col_size="SELECT color_number_id,size_number_id, sum(plan_cut_qnty) as qnty from wo_po_color_size_breakdown where po_break_down_id=$po_id and status_active=1 and is_deleted=0 group by color_number_id,size_number_id";
		 foreach(sql_select($sqls_col_size) as $key=>$value)
		 {
			 $po_color_size_qnty_arr[$value[csf("color_number_id")]][$value[csf("size_number_id")]] +=$value[csf("qnty")];
		 }
 
		 
		 
			 if( $variableSettings==2 ) // color level
			 {
				 
					 
					$sql = ("SELECT b.color_size_break_down_id,c.color_number_id,
					sum(b.production_qnty) as production_qnty,
					sum(b.reject_qty) as reject_qty
					from pro_garments_production_mst a,pro_garments_production_dtls b ,wo_po_color_size_breakdown c where a.id=b.mst_id and c.id=b.color_size_break_down_id and  a.id='$data[1]' and a.status_active=1 and  b.status_active=1 and  c.status_active=1 and b.color_size_break_down_id!=0 and a.production_type=118  and a.production_type=118 and  a.embel_name=3  group by  b.color_size_break_down_id,c.color_number_id");
					//echo $sql;die;

 
			 }
			 else if( $variableSettings==3 ) //color and size level
			 {
				 $dtlsData = sql_select("SELECT a.color_size_break_down_id,
											 sum(CASE WHEN a.production_type=118 then a.production_qnty ELSE 0 END) as cur_production_qnty
											 from pro_garments_production_dtls a,pro_garments_production_mst b where a.mst_id=b.id and b.id='$data[1]' and a.status_active=1 and  b.status_active=1 and a.color_size_break_down_id!=0 and a.production_type in($qty_source,118) group by a.color_size_break_down_id");
											 echo $dtlsData;die;
 
				 foreach($dtlsData as $row)
				 {
					 $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
					 $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cur_cut']= $row[csf('cur_production_qnty')];
				 }
 
				 $sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				 from wo_po_color_size_breakdown
				 where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order"; //color_number_id, id
 
 
			 }
			 else // by default color and size level
			 {
				 $dtlsData = sql_select("SELECT a.color_size_break_down_id,
				 sum(CASE WHEN a.production_type=118 then a.production_qnty ELSE 0 END) as cur_production_qnty
				 from pro_garments_production_dtls a,pro_garments_production_mst b where a.mst_id=b.id and b.id='$data[1]' and a.status_active=1 and  b.status_active=1 and a.color_size_break_down_id!=0 and a.production_type in(118) group by a.color_size_break_down_id");
 
				 foreach($dtlsData as $row)
				 {
					 $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cut']= $row[csf('production_qnty')];
					 $color_size_qnty_array[$row[csf('color_size_break_down_id')]]['cur_cut']= $row[csf('cur_production_qnty')];
				 }
 
				 $sql = "SELECT id, item_number_id, size_number_id, color_number_id, order_quantity, plan_cut_qnty
				 from wo_po_color_size_breakdown
				 where po_break_down_id='$po_id' and item_number_id='$item_id' and country_id='$country_id' and is_deleted=0 and status_active=1 order by color_number_id,size_order";//color_number_id, id
				// echo $sql;die;
			 }
			//  echo $dtlsData;die;
			 if($variableSettingsRej!=1)
			 {
				 $disable="";
			 }
			 else
			 {
				 $disable="disabled";
			 }
			 //echo $sql;die;
			 $colorResult = sql_select($sql);
			   $colorHTML="";
			 $colorID='';
			 $chkColor = array();
			 $i=0;$totalQnty=0;
			  foreach($colorResult as $color)
			 {
				 if( $variableSettings==2 ) // color level
				 {
					 $colorHTML .='<tr><td>'.$color_library[$color[csf("color_number_id")]].'</td><td><input type="text" name="txt_color" id="colSize_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="'.($color[csf("production_qnty")]-$color[csf("cur_production_qnty")]).'" onblur="fn_colorlevel_total('.($i+1).')"></td><td><input type="text" name="txtColSizeRej" id="colSizeRej_'.($i+1).'" style="width:60px"  class="text_boxes_numeric" placeholder="Rej." onblur="fn_colorRej_total('.($i+1).') '.$disable.'"></td></tr>';
					 $totalQnty += $color[csf("production_qnty")]-$color[csf("cur_production_qnty")];
					 $colorID .= $color[csf("color_number_id")].",";
				 }
				 else //color and size level
				 {
					 if( !in_array( $color[csf("color_number_id")], $chkColor ) )
					 {
						 if( $i!=0 ) $colorHTML .= "</table></div>";
						 $i=0;
						 $colorHTML .= '<h3 align="left" id="accordion_h'.$color[csf("color_number_id")].'" style="width:300px" class="accordion_h" onClick="accordion_menu( this.id,\'content_search_panel_'.$color[csf("color_number_id")].'\', \'\',1)">  <span id="accordion_h'.$color[csf("color_number_id")].'span">+</span>'.$color_library[$color[csf("color_number_id")]].' : <span id="total_'.$color[csf("color_number_id")].'"></span></h3>';
						 $colorHTML .= '<div id="content_search_panel_'.$color[csf("color_number_id")].'" style="display:none" class="accord_close"><table id="table_'.$color[csf("color_number_id")].'">';
						 $chkColor[] = $color[csf("color_number_id")];
					 }
							  $bundle_mst_data="";
							  $bundle_dtls_data="";
						  $tmp_col_size="'".$color_library[$color[csf("color_number_id")]]."__".$size_library[$color[csf("size_number_id")]]."'";
					  //$index = $color[csf("size_number_id")].$color[csf("color_number_id")];
					 $colorID .= $color[csf("size_number_id")]."*".$color[csf("color_number_id")].",";
					 $cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cut'];
					 $cur_cut_qnty=$color_size_qnty_array[$color[csf('id')]]['cur_cut'];
 
					  $colorHTML .='<tr><td>'.$size_library[$color[csf("size_number_id")]].'</td><td><input type="hidden" name="bundlemst" id="bundle_mst_'.$color[csf("color_number_id")].($i+1).'" value="'.$bundle_mst_data.'"  class="text_boxes_numeric" style="width:100px"  ><input type="hidden" name="bundledtls" id="bundle_dtls_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:100px" value="'.$bundle_dtls_data.'" ><input type="text" name="colorSize" id="colSize_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="'.($cur_cut_qnty).'" onblur="fn_total('.$color[csf("color_number_id")].','.($i+1).')"value="'.$cur_cut_qnty.'"><input type="text" name="colorSizeRej" id="colSizeRej_'.$color[csf("color_number_id")].($i+1).'"  class="text_boxes_numeric" style="width:50px" placeholder="Rej. Qty" onblur="fn_total_rej('.$color[csf("color_number_id")].','.($i+1).')" '.$disable.'></td><td><input type="text" name="button" id="button_'.$color[csf("color_number_id")].($i+1).'" value="'.$po_color_size_qnty_arr[$color[csf("color_number_id")]][$color[csf("size_number_id")]].'" class="text_boxes_numeric" disabled="" readonly=""  style="size:30px;"  /></td></tr>';
				 }
 
				 $i++;
			 }
			 //echo $colorHTML;die;
			 if( $variableSettings==2 ){ $colorHTML = '<table id="table_color" class="rpt_table"><thead><th width="70">Color</th><th width="60">Quantity</th><th width="60">Reject</th></thead><tbody>'.$colorHTML.'<tbody><tfoot><tr><th>Total</th><th><input type="text" id="total_color" placeholder="'.$totalQnty.'" class="text_boxes_numeric" style="width:60px" ></th><th><input type="text" id="total_color_rej" class="text_boxes_numeric" style="width:60px" ></th></tr></tfoot></table>'; }
			 echo "$('#breakdown_td_id').html('".addslashes($colorHTML)."');\n";
			 $colorList = substr($colorID,0,-1);
			 echo "$('#hidden_colorSizeID').val('".$colorList."');\n";
			}
			 //#############################################################################################//
			 exit();
 	
}


if ($action == "save_update_delete") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));


	$is_control = return_field_value("is_control", "variable_settings_production", "company_name=$cbo_company_name and variable_list=33 and page_category_id=32");
	if (!str_replace("'", "", $sewing_production_variable)) $sewing_production_variable = 3;
			
	/* ======================================================================== /
	/							check variable setting							/
	========================================================================= */
	if(!str_replace("'","",$sewing_production_variable)) $sewing_production_variable=3;
	$control_and_preceding=sql_select("select is_control,preceding_page_id from variable_settings_production where status_active=1 and is_deleted=0 and variable_list=33 and page_category_id=269 and company_name=$cbo_company_name");  
   $is_control=$control_and_preceding[0][csf("is_control")];
   $preceding_process=$control_and_preceding[0][csf("preceding_page_id")];
 
   $qty_source = 111;
   //echo "10**".$qty_source;
	
		$color_data_array = array();
		if(str_replace("'","",$sewing_production_variable)==2)
		{
			$rowEx = array_filter(explode("**",$colorIDvalue));
			foreach ($rowEx as $v) 
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[0]]['ok']+=$colorSizeNumberIDArr[1];
			}
			// ===========================
			$rowEx = array_filter(explode("**",$colorIDvalueRej));
			foreach ($rowEx as $v) 
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[0]]['rej']+=$colorSizeNumberIDArr[1];
			}
		}

		if(str_replace("'","",$sewing_production_variable)==3)
		{
			$rowEx = array_filter(explode("***",$colorIDvalue));
			foreach ($rowEx as $v) 
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[1]]['ok']+=$colorSizeNumberIDArr[2];
			}
			// ===========================
			$rowEx = array_filter(explode("***",$colorIDvalueRej));
			foreach ($rowEx as $v) 
			{
				$colorSizeNumberIDArr = explode("*",$v);
				$color_data_array[$colorSizeNumberIDArr[1]]['rej']+=$colorSizeNumberIDArr[2];
			}
		}
	
	

	if ($operation == 0) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		
		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$txt_country_ship_date = date('d-M-Y',strtotime(str_replace("'","",$txt_country_ship_date)));
		if (str_replace("'", "", $txt_system_id) == "") 
		{
			if ($db_type == 2) $mrr_cond = "and  TO_CHAR(insert_date,'YYYY')=" . date('Y', time());
			else if ($db_type == 0) $mrr_cond = "and year(insert_date)=" . date('Y', time());
			$mst_id = return_next_id_by_sequence(  "pro_gmts_delivery_mst_seq",  "pro_gmts_delivery_mst", $con );

			$new_sys_number = explode("*", return_next_id_by_sequence("", "pro_gmts_delivery_mst",$con,1,$cbo_company_name,'WRCV',625,date("Y",time()),0,0,118,0,0 ));


			$field_array_delivery = "id, sys_number_prefix, sys_number_prefix_num, sys_number, company_id, production_type,embel_name, location_id, production_source, working_company_id, working_location_id, floor_id, job_no, delivery_date,challan_no, remarks, entry_form, inserted_by, insert_date, status_active, is_deleted";
			$data_array_delivery="(".$mst_id.",'".$new_sys_number[1]."','".(int)$new_sys_number[2]."','".$new_sys_number[0]."',".$cbo_company_name.",118,3,".$cbo_location.",".$cbo_source.",".$cbo_wash_company.",".$cbo_wash_location.",".$cbo_floor.",".$txt_job_no.",".$txt_issue_date.",".$txt_challan.",".$txt_remark.",625,".$user_id.",'".$pc_date_time."',1,0)";
		} else {
			$mst_id = str_replace("'", "", $txt_system_id);
			// $mst_id = str_replace("'", "", $txt_system_no);
			// $mrr_no_challan = str_replace("'", "", $txt_challan_no);

			$field_array_delivery ="company_id*location_id* production_source*working_company_id*working_location_id*floor_id*delivery_date*challan_no*remarks*updated_by*update_date";
			$data_array_delivery = "" .$cbo_company_name . "*" . $cbo_location . "*" . $cbo_source . "*" . $cbo_wash_company . "*".$cbo_wash_location."*".$cbo_floor."*" .$txt_issue_date."*" .$txt_challan."*" .$txt_remark ."*" .$user_id . "*'" . $pc_date_time . "'";
		}

		if(str_replace("'","",$txt_pack_type)=="") $pack_type_cond=""; else $pack_type_cond=" and pack_type=$txt_pack_type";
	
		$id = return_next_id("id", "pro_garments_production_mst", 1);

		$field_array1="id, garments_nature, company_id, challan_no, po_break_down_id, item_number_id, country_id, production_source, serving_company, location, production_date, production_quantity, production_type,embel_name,entry_break_down_type, remarks, floor_id,total_produced,yet_to_produced,delivery_mst_id, inserted_by, insert_date"; 

		$data_array1="(".$id.",".$garments_nature.",".$cbo_company_name.",".$txt_challan.",".$hidden_po_break_down_id.",".$cbo_item_name.",".$cbo_country_name.",".$cbo_source.",".$cbo_wash_company.",".$cbo_wash_location.",".$txt_issue_date.",".$txt_receive_qty.",118,3,".$sewing_production_variable.",".$txt_remark.",".$cbo_floor.",".$txt_cumul_quantity.",".$txt_yet_quantity.",".$mst_id.",".$user_id.",'".$pc_date_time."')";

		$po_id=str_replace("'","",$hidden_po_break_down_id);
		$item_id=str_replace("'","",$cbo_item_name);
		$country_id=str_replace("'","",$cbo_country_name);



		// echo "INSERT INTO pro_gmts_delivery_mst (".$field_array_delivery.") VALUES ".$data_array_delivery;die;
		// echo "INSERT INTO pro_gmts_delivery_mst (".$field_array1.") VALUES ".$data_array_;die;

		//$rID=sql_insert("pro_ex_factory_mst",$field_array1,$data_array1,1);

		//echo "10**update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name";die;

		$dtlsData = sql_select("select a.color_size_break_down_id,
			sum(CASE WHEN a.production_type=111 then a.production_qnty ELSE 0 END) as production_qnty,
			sum(CASE WHEN a.production_type=118  then a.production_qnty ELSE 0 END) as cur_production_qnty
			from pro_garments_production_dtls a,pro_garments_production_mst b
			where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id!=0 and a.production_type in(111,118)
			group by a.color_size_break_down_id");

		$color_pord_data=array();
		foreach($dtlsData as $row)
		{
			$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
		}
		unset($dtlsData);

		$field_array="id, mst_id, production_type, color_size_break_down_id, production_qnty,reject_qty,entry_form, status_active, is_deleted";
  		$dtlsrID=true;
		if(str_replace("'","",$sewing_production_variable)==2)//color level wise
		{
			$color_sizeID_arr=sql_select( "select id, color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0 order by id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf("id")];
			}
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
			$rowExRej = explode("**",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorSizeRejIDArr = explode("*",$valR);
				//echo $colorSizeRejIDArr[0]; die;
				$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
			}
			// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
 			$rowEx = explode("**",$colorIDvalue);
 			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$val)
			{
				$colorSizeNumberIDArr = explode("*",$val);
				
				if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
				{
					//3 for Receive From Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0) $data_array = "(".$dtls_id.",".$id.",118,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',625,1,0)";
					else $data_array .= ",(".$dtls_id.",".$id.",118,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."',625,1,0)";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
 		}

		if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
		{
			$color_sizeID_arr=sql_select( "select id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active=1 and is_deleted=0  order by size_number_id,color_number_id" );
			$colSizeID_arr=array();
			foreach($color_sizeID_arr as $val){
				$index = $val[csf("size_number_id")].$val[csf("color_number_id")];
				$colSizeID_arr[$index]=$val[csf('id')];
			}
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
			$rowExRej = explode("***",$colorIDvalueRej);
			foreach($rowExRej as $rowR=>$valR)
			{
				$colorAndSizeRej_arr = explode("*",$valR);
				$sizeID = $colorAndSizeRej_arr[0];
				$colorID = $colorAndSizeRej_arr[1];
				$colorSizeRej = $colorAndSizeRej_arr[2];
				$index = $sizeID.$colorID;
				$rejQtyArr[$index]=$colorSizeRej;
			}
			//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------//
 			$rowEx = explode("***",$colorIDvalue);
			//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
			$data_array="";$j=0;
			foreach($rowEx as $rowE=>$valE)
			{
				$colorAndSizeAndValue_arr = explode("*",$valE);
				$sizeID = $colorAndSizeAndValue_arr[0];
				$colorID = $colorAndSizeAndValue_arr[1];
				$colorSizeValue = $colorAndSizeAndValue_arr[2];
				$index = $sizeID.$colorID;

				if($colSizeID_arr[$index]!="")
				{
					
					//3 for Receive From Print / Emb Entry
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq", "pro_garments_production_dtls", $con );
					if($j==0)$data_array = "(".$dtls_id.",".$id.",118,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',625,1,0)";
					else $data_array .= ",(".$dtls_id.",".$id.",118,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."',625,1,0)";
					//$dtls_id=$dtls_id+1;
					$j++;
				}
			}
		}

		
		
		// echo "10**insert into pro_garments_production_dtls (".$field_array.") values ".$data_array;die;
		
		if (str_replace("'", "", $txt_system_id) == "") 
		{
            $challanrID = sql_insert("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, 1);
        } 
        else 
        {
            $challanrID = sql_update("pro_gmts_delivery_mst", $field_array_delivery, $data_array_delivery, "id", $txt_system_id, 1);
        }

		
		$rID=sql_insert("pro_garments_production_mst",$field_array1,$data_array1,1);;	

		if(str_replace("'","",$sewing_production_variable)==2 || str_replace("'","",$sewing_production_variable)==3)
		{ 
 			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array,$data_array,1);
		}
		// echo "10**".$challanrID;die();
		
		// echo "10**".$rID."**".$challanrID."**".$dtlsrID;die;
		
		//release lock table
		//check_table_status( 160,0);
		
		
		if($db_type==2 || $db_type==1 )
		{
			if(str_replace("'","",$sewing_production_variable)!=1)
			{
				if($rID && $dtlsrID && $challanrID)
				{
					oci_commit($con); 
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".$new_sys_number[0]."**".$mst_id;
				}
				else
				{
					oci_rollback($con);
				//	echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
			else
			{
				if($rID && $challanrID)
				{
					oci_commit($con);  
					echo "0**".str_replace("'","",$hidden_po_break_down_id)."**".str_replace("'","",$new_sys_number[0])."**".$mst_id;
				}
				else
				{
					oci_rollback($con);
				//	echo "10**".str_replace("'","",$hidden_po_break_down_id);
				}
			}
		}
		//check_table_status( 160,0);
		disconnect($con);
		die;

	
	} 
	else if ($operation==1) // Update Here 
	{
		
		$con = connect();
			
		$txt_finishing_qty=str_replace("'","",$txt_finishing_qty);
		if($txt_finishing_qty=='') $txt_finishing_qty=0;
		$txt_mst_id=str_replace("'","",$txt_mst_id);
		$txt_system_id=str_replace("'","",$txt_system_id);
		
		
		//--------------------------------------------------------------Compare end;
		
		$field_array_delivery_up ="company_id*location_id* production_source*working_company_id*working_location_id*floor_id*delivery_date*challan_no*remarks*updated_by*update_date";

        $data_array_delivery_up = "" .$cbo_company_name . "*" . $cbo_location . "*" . $cbo_source . "*" . $cbo_wash_company . "*".$cbo_wash_location."*".$cbo_floor."*" .$txt_issue_date."*" .$txt_challan."*" .$txt_remark ."*" .$user_id . "*'" . $pc_date_time . "'";


	

		
		
		// pro_garments_production_mst table data entry here 
		$field_array_up1="challan_no*po_break_down_id*item_number_id*country_id*production_source*serving_company*location* production_date*production_quantity*entry_break_down_type*remarks*floor_id*total_produced*yet_to_produced* updated_by*update_date"; 

		$data_array_up1="".$txt_challan."*".$hidden_po_break_down_id."*".$cbo_item_name."*".$cbo_country_name."*".$cbo_source."*".$cbo_wash_company."*".$cbo_wash_location."*".$txt_issue_date."*".$txt_receive_qty."*".$sewing_production_variable."*".$txt_remark."*".$cbo_floor."*".$txt_cumul_quantity."*".$txt_yet_quantity."*".$user_id."*'".$pc_date_time."'";

		// echo "INSERT INTO pro_garments_production_mst (".$field_array1.") VALUES ".$data_array1;die;
				
		
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='' ) //  not gross level
		{
			
			$dtlsData = sql_select("SELECT a.color_size_break_down_id,
										sum(CASE WHEN a.production_type='$qty_source' then a.production_qnty ELSE 0 END) as production_qnty,
										sum(CASE WHEN a.production_type=118 then a.production_qnty ELSE 0 END) as cur_production_qnty 
										from pro_garments_production_dtls a,pro_garments_production_mst b 
										where a.status_active=1 and a.mst_id=b.id and b.po_break_down_id=$hidden_po_break_down_id and b.item_number_id=$cbo_item_name and b.country_id=$cbo_country_name and a.color_size_break_down_id<>0 and a.production_type in($qty_source,118) and b.id !=$txt_mst_id 
										group by a.color_size_break_down_id");
			$color_pord_data=array();							
			foreach($dtlsData as $row)
			{				  
				$color_pord_data[$row[csf("color_size_break_down_id")]]=$row[csf('production_qnty')]-$row[csf("cur_production_qnty")];
			}
			
			
 			$field_array_up="id, mst_id, production_type, color_size_break_down_id, production_qnty, reject_qty,delivery_mst_id";
			
			if(str_replace("'","",$sewing_production_variable)==2)//color level wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name  and status_active =1 and is_deleted=0  order by id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				
				// $colorIDvalue concate as colorID*Value**colorID*Value -------------------------//
				$rowExRej = explode("**",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorSizeRejIDArr = explode("*",$valR);
					//echo $colorSizeRejIDArr[0]; die;
					$rejQtyArr[$colorSizeRejIDArr[0]]=$colorSizeRejIDArr[1];
				}
				
				$rowEx = explode("**",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array_up="";$j=0;
				foreach($rowEx as $rowE=>$val)
				{
					$colorSizeNumberIDArr = explode("*",$val);
					
					if($colSizeID_arr[$colorSizeNumberIDArr[0]]!="")
					{
							
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",  "pro_garments_production_dtls", $con );
					if($j==0)$data_array_up = "(".$dtls_id.",".$txt_mst_id.",118,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$txt_system_id."')";
					else $data_array_up .= ",(".$dtls_id.",".$txt_mst_id.",118,'".$colSizeID_arr[$colorSizeNumberIDArr[0]]."','".$colorSizeNumberIDArr[1]."','".$rejQtyArr[$colorSizeNumberIDArr[0]]."','".$txt_system_id."')";
					$j++;	
				}							
				}
			}
			
			if(str_replace("'","",$sewing_production_variable)==3)//color and size wise
			{		
				$color_sizeID_arr=sql_select( "SELECT id,size_number_id,color_number_id from wo_po_color_size_breakdown where po_break_down_id=$hidden_po_break_down_id and item_number_id=$cbo_item_name and country_id=$cbo_country_name and status_active =1 and is_deleted=0 order by size_number_id,color_number_id" );
				$colSizeID_arr=array(); 
				foreach($color_sizeID_arr as $val){
					$index = $val[csf("size_number_id")].$color_arr[$val[csf("color_number_id")]].$val[csf("color_number_id")];
					$colSizeID_arr[$index]=$val[csf("id")];
				}	
				unset($color_sizeID_arr);
				
				//	colorIDvalue concate as sizeID*colorID*value***sizeID*colorID*value	--------------------------// 
				$rowExRej = explode("***",$colorIDvalueRej);
				foreach($rowExRej as $rowR=>$valR)
				{
					$colorAndSizeRej_arr = explode("*",$valR);
					$sizeID = $colorAndSizeRej_arr[0];
					$colorID = $colorAndSizeRej_arr[1];				
					$colorSizeRej = $colorAndSizeRej_arr[2];
					$index = $sizeID.$color_arr[$colorID].$colorID;
					$rejQtyArr[$index]=$colorSizeRej;
				}
				
				$rowEx = explode("***",$colorIDvalue); 
				//$dtls_id=return_next_id("id", "pro_garments_production_dtls", 1);
				$data_array="";$j=0;
				foreach($rowEx as $rowE=>$valE)
				{
					$colorAndSizeAndValue_arr = explode("*",$valE);
					$sizeID = $colorAndSizeAndValue_arr[0];
					$colorID = $colorAndSizeAndValue_arr[1];				
					$colorSizeValue = $colorAndSizeAndValue_arr[2];
					$index = $sizeID.$colorID;
					
					
					if($colSizeID_arr[$index]!="")
					{
					
				
					$dtls_id = return_next_id_by_sequence(  "pro_gar_production_dtls_seq",   "pro_garments_production_dtls", $con );
					if($j==0)$data_array_up = "(".$dtls_id.",".$txt_mst_id.",118,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."','".$txt_system_id."')";
					else $data_array_up .= ",(".$dtls_id.",".$txt_mst_id.",118,'".$colSizeID_arr[$index]."','".$colorSizeValue."','".$rejQtyArr[$index]."','".$txt_system_id."')";
					//$dtls_id=$dtls_id+1;
					$j++;
					}
				}
			}
		}
		
		$rID = $dtlsrDelete = $dtlsrID = $challanrID=true;
		$dtlsrDelete = execute_query("delete from pro_garments_production_dtls where mst_id=$txt_mst_id",1);
		
		
		 $challanrID=sql_update("pro_gmts_delivery_mst",$field_array_delivery_up,$data_array_delivery_up,"id","".$txt_system_id."",1);

		$rID=sql_update("pro_garments_production_mst",$field_array_up1,$data_array_up1,"id","".$txt_mst_id."",1);
		// echo $challanrID;die;
		
		if(str_replace("'","",$sewing_production_variable)!=1 && str_replace("'","",$txt_mst_id)!='')// check is not gross level
		{
			$dtlsrID=sql_insert("pro_garments_production_dtls",$field_array_up,$data_array_up,1);
			//  echo "INSERT INTO pro_garments_production_dtls (".$field_array_up.") VALUES ".$data_array_up;die;
		}
		//echo "10**insert into pro_garments_production_dtls (".$field_array_up.") values "(".$field_array_up.") ;die;

		
		
		// echo "10**".$rID ."=". $dtlsrID ."=". $challanrID;die;
		
		//release lock table
		//check_table_status( 160,0);
		
		
		
		if(str_replace("'","",$sewing_production_variable)!=1)
		{
			if($rID && $dtlsrID && $challanrID)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".$txt_system_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id)."**mst*".$rID ."**dtls*". $dtlsrID ."**cln*". $challanrID;;
			}
		}
		else
		{
			if($rID)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$hidden_po_break_down_id)."**".$txt_system_id;
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'","",$hidden_po_break_down_id)."**mst*".$rID ."**dtls*". $dtlsrID ."**cln*". $challanrID;;
			}
		}
		disconnect($con);
	}
	else if ($operation == 2)  // Delete Here----------------------------------------------------------
	{
		$con = connect();
		
		$delivery_mst_id = str_replace("'", "", $txt_system_id);
		$mrr_no = str_replace("'", "", $txt_system_no);
		$mrr_no_challan = str_replace("'", "", $txt_challan_no);

		$is_gate_passed = return_field_value("sys_number", "inv_gate_pass_mst", "challan_no='$mrr_no' and basis=12 and status_active=1 and is_deleted=0");
		if ($is_gate_passed != "") {
			echo "37**Gate Pass Found($is_gate_passed).Delete operation not allow!";
			disconnect($con);
			die();
		}

		$country_order_qty = return_field_value("sum(order_quantity)", "wo_po_color_size_breakdown", "po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active in(1,2,3) and is_deleted=0");

		$country_exfactory_qty = return_field_value("sum(ex_factory_qnty)", "pro_ex_factory_mst", "po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name and status_active=1 and is_deleted=0 and id<>$txt_mst_id");

		if ($country_exfactory_qty >= $country_order_qty) $country_order_status = 3;
		else if ($country_exfactory_qty > 0 && $country_exfactory_qty < $country_order_qty) $country_order_status = 2;
		else $country_order_status = 1;

		$sts_country = execute_query("update wo_po_color_size_breakdown set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name", 1);

		$country_wise_status = return_field_value("count(id)", "wo_po_color_size_breakdown", "po_break_down_id=$hidden_po_break_down_id and shiping_status<>3 and status_active=1 and is_deleted=0");
		if ($country_wise_status > 0 && $country_exfactory_qty > 0) $order_status = 2;
		else if ($country_wise_status > 0 && $country_exfactory_qty <= 0) $order_status = 1;
		else $order_status = 3;

		$sts_ex = execute_query("update wo_po_break_down set shiping_status=$order_status where id=$hidden_po_break_down_id", 1);
		$sts_ex_mst = execute_query("update pro_ex_factory_mst set shiping_status=$country_order_status where po_break_down_id=$hidden_po_break_down_id and country_id=$cbo_country_name", 1);

		$rID = sql_delete("pro_ex_factory_mst", "updated_by*update_date*status_active*is_deleted", "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1", 'id', $txt_mst_id, 1);
		$dtlsrID = sql_delete("pro_ex_factory_dtls", "status_active*is_deleted", "0*1", 'mst_id', $txt_mst_id, 1);

		$actualPoDelete = execute_query("delete from pro_ex_factory_actual_po_details where mst_id=$txt_mst_id", 1);

		
		if ($db_type == 2 || $db_type == 1) {
			if ($rID && $dtlsrID && $sts_country && $sts_ex && $sts_ex_mst && $actualPoDelete) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $hidden_po_break_down_id) . "**" . str_replace("'", "", $delivery_mst_id) . "**" . $mrr_no;
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $hidden_po_break_down_id);
			}
		}
		disconnect($con);
		die;
	}
}
?>